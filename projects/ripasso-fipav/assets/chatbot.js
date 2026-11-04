/*
 * Fischietto — Chatbot (alpha, administrators only).
 *
 * Answers questions in natural language using ONLY the references and quiz questions of the portal
 * (window.APP_CONTENT). Nothing is sent to external services: everything runs in the browser.
 *
 * Two layers:
 *  A. Retrieval (always available, instant): finds the references that answer the question.
 *  B. Assistant (optional, needs WebGPU): a small language model (Qwen2.5 Instruct via WebLLM) runs
 *     ON THE DEVICE, reads only the references found in step A and writes the answer in its own words,
 *     citing the codes. The model files are downloaded once (≈ 0.4–2 GB, kept in the browser cache);
 *     the questions never leave the device.
 *
 * Retrieval (classic information retrieval):
 *  1. the question is normalized (lowercase, no accents), split into words, stop words removed,
 *     every word reduced to a short stem and expanded with volleyball synonyms (battuta → servizio…);
 *  2. every reference is an indexed document: title, short text, full text and the text of the quiz
 *     questions/answers that cite it (they describe it with the words people actually use);
 *  3. references are ranked with BM25 (title words weigh more, phrases of two words get a bonus);
 *  4. the answer is extractive: the sentences of the best references that match the question best,
 *     with the reference codes as sources. If a quiz question is very similar, its correct answer is shown too.
 */
(function () {
  'use strict';

  const REFS = (window.APP_CONTENT && window.APP_CONTENT.refs) || [];
  const QUESTIONS = (window.APP_CONTENT && window.APP_CONTENT.questions) || [];

  // ---------------------------------------------------------------- text processing

  const STOP = new Set(('a ad al allo ai agli all agl alla alle con col coi da dal dallo dai dagli dall dagl dalla dalle di del dello dei degli dell degl della delle in nel nello nei negli nell negl nella nelle su sul sullo sui sugli sull sugl sulla sulle per tra fra ' +
    'il lo la i gli le l un uno una un\' e ed o od ma se che chi cui non ne ci vi si mi ti lui lei noi voi loro io tu esso essa essi esse questo questa questi queste quello quella quelli quelle ' +
    'come dove quando quanto quanti quanta quante quale quali perche cosa cos qual sono sei siamo siete era erano essere e stato stata stati state ha hanno ho hai abbiamo avete avere aveva ' +
    'puo possono posso puoi deve devono devo devi fare fa fanno faccio viene vengono va vanno c anche piu meno molto poi gia ancora sempre mai solo ogni tutto tutti tutta tutte altro altri altra altre ' +
    'cosi allora quindi oppure invece mentre dopo prima durante contro senza sopra sotto dentro fuori verso nei della succede succedere caso casi bisogna serve regola regole regolamento').split(/\s+/));
  // "fuori", "sopra", "sotto", "dentro" are meaningful in volleyball (ball in/out): kept out of the stop list
  ['fuori', 'sopra', 'sotto', 'dentro', 'prima', 'dopo', 'contro'].forEach(w => STOP.delete(w));

  /** Volleyball synonyms: common words → words used by the rulebook (applied to the question). */
  const SYNONYMS = {
    battuta: 'servizio', battere: 'servizio', batte: 'servizio', battitore: 'servizio giocatore', servire: 'servizio', serve: 'servizio',
    schiacciata: 'attacco', schiacciare: 'attacco', schiaccia: 'attacco', attaccare: 'attacco', pallonetto: 'attacco',
    alzata: 'palleggio', alzare: 'palleggio', alzatore: 'palleggiatore', bagher: 'ricezione tocco',
    timeout: 'tempo riposo', 'time out': 'tempo riposo', 'time-out': 'tempo riposo', pausa: 'tempo riposo intervallo',
    cambio: 'sostituzione', cambi: 'sostituzioni', cambiare: 'sostituzione', sostituire: 'sostituzione', entra: 'sostituzione', esce: 'sostituzione',
    cartellino: 'sanzione cartellino', giallo: 'ammonizione penalizzazione', rosso: 'penalizzazione', ammonito: 'ammonizione', espulso: 'espulsione', squalificato: 'squalifica',
    invasione: 'linea centrale rete', piede: 'piede linea', calpestare: 'piede linea', pestare: 'linea',
    doppia: 'doppio tocco', doppio: 'doppio tocco', trattenuta: 'palla trattenuta', accompagnata: 'palla trattenuta', portata: 'palla trattenuta',
    campo: 'terreno gioco campo', misure: 'dimensioni', grande: 'dimensioni', lungo: 'lunghezza', largo: 'larghezza', alta: 'altezza', alto: 'altezza',
    pallone: 'palla', fischietto: 'fischio', fischiare: 'fischio', arbitrare: 'arbitro', primo: '1 arbitro', secondo: '2 arbitro',
    rotazione: 'rotazione posizione', ruotare: 'rotazione', posizioni: 'posizione', fallo: 'fallo', falli: 'fallo',
    vince: 'vincere', vinto: 'vincere', vincere: 'vincere set partita', punteggio: 'punto punteggio', punti: 'punto',
    libero: 'libero', liberi: 'libero', capitano: 'capitano', allenatore: 'allenatore', coach: 'allenatore', panchina: 'panchina',
    ritardo: 'ritardo', perdere: 'perdere tempo ritardo', protesta: 'condotta scorretta', insulto: 'condotta offensiva', offendere: 'condotta offensiva',
    muro: 'muro', murare: 'muro', antenna: 'antenna', asticella: 'antenna', rete: 'rete', nastro: 'rete',
    infortunio: 'infortunio', infortunato: 'infortunio', sangue: 'infortunio', sudore: 'interruzione',
    divisa: 'uniforme', maglia: 'uniforme maglia', numero: 'numero', scarpe: 'uniforme',
    // question words → what the rulebook says ("Quante sostituzioni…" → "numero massimo di sostituzioni")
    quanti: 'numero massimo', quante: 'numero massimo', 'quanto dura': 'durata', 'quanto tempo': 'durata tempo', 'quanto e alta': 'altezza', 'quanto alta': 'altezza', 'quanto e lungo': 'lunghezza dimensioni', 'chi decide': 'competenza arbitro', 'chi deve': 'competenza',
    '3 metri': 'linea attacco zona anteriore', 'tre metri': 'linea attacco zona anteriore', 'linea dei 3': 'linea attacco', 'seconda linea': 'difensore zona difesa', 'prima linea': 'attaccante zona anteriore',
    referto: 'referto', segnapunti: 'segnapunti', video: 'video check', videocheck: 'video check', challenge: 'video check',
  };

  function norm(s) {
    return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[’'`]/g, ' ').replace(/[^a-z0-9.\- ]+/g, ' ').replace(/\s+/g, ' ').trim();
  }

  /** Light Italian stemmer: drops the most common endings so that "sostituzioni" ≈ "sostituzione". */
  function stem(w) {
    if (/^\d/.test(w) || w.length <= 3) return w;
    const suffixes = ['azioni', 'azione', 'izioni', 'izione', 'uzioni', 'uzione', 'zioni', 'zione', 'amenti', 'amento', 'imenti', 'imento', 'mente', 'ita', 'ivi', 'ive', 'ivo', 'iva',
      'ando', 'endo', 'ante', 'enti', 'ente', 'ati', 'ate', 'ato', 'ata', 'iti', 'ite', 'ito', 'ita', 'uti', 'ute', 'uto', 'uta', 'are', 'ere', 'ire', 'ano', 'ono', 'i', 'e', 'a', 'o'];
    for (const s of suffixes) if (w.endsWith(s) && w.length - s.length >= 4) return w.slice(0, -s.length);
    return w;
  }

  /** "Libero" with a capital L (the player) becomes its own word, so it does not match "zona libera". */
  const libero = text => String(text || '').replace(/(^|[^A-Za-zÀ-ÿ])Liber[oi](?![A-Za-zÀ-ÿ])/g, '$1 liberoplayer ');
  function words(text) {
    return norm(libero(text)).split(' ').map(w => w.replace(/^[.\-]+|[.\-]+$/g, '')).filter(w => w && !STOP.has(w) && (w.length > 1 || /\d/.test(w)));
  }
  const terms = text => words(text).map(stem);

  /** Words of the question, expanded with synonyms (synonyms weigh a bit less). */
  function queryTerms(q) {
    const n = norm(q);
    const out = new Map();
    const add = (t, w) => { if (t && !STOP.has(t)) out.set(t, Math.max(out.get(t) || 0, w)); };
    for (const w of words(q)) add(stem(w), 1);
    // In a question "libero" almost always means the player
    if (/(^| )liber[oi]( |$)/.test(n)) { add('liberoplayer', 1.3); out.set(stem('libero'), 0.25); }
    for (const [k, v] of Object.entries(SYNONYMS)) {
      const re = new RegExp('(^| )' + k.replace(/[.*+?^${}()|[\]\\-]/g, '\\$&') + '( |$)');
      if (re.test(n)) v.split(' ').forEach(x => add(stem(x), 0.7));
    }
    return out;
  }

  // ---------------------------------------------------------------- index

  const DOCS = [];          // { ref, tf: Map(term → weighted count), len, bigrams: Set }
  const DF = new Map();
  let AVG = 1;
  const BY_CODE = {};
  const Q_INDEX = [];       // quiz questions: { q, terms: Set, answer }

  function build() {
    const qByRef = {};
    for (const q of QUESTIONS) {
      const correct = (q['question-answers'] || []).filter(a => a.correct).map(a => a.text).join(' · ');
      const text = q['question-text'] + ' ' + correct;
      for (const rc of q['question-ref'] || []) (qByRef[rc] = qByRef[rc] || []).push(text);
      Q_INDEX.push({ q, terms: new Set(terms(q['question-text'])), answer: correct });
    }
    for (const r of REFS) {
      BY_CODE[r['ref-code'].toUpperCase()] = r;
      const tf = new Map();
      const addAll = (text, w) => { for (const t of terms(text)) tf.set(t, (tf.get(t) || 0) + w); };
      addAll(r['ref-title'], 3);
      addAll(r['ref-text'], 1.5);
      addAll(r['ref-full-text'] || '', 1);
      addAll((qByRef[r['ref-code']] || []).join(' '), 0.6);
      let len = 0; tf.forEach(v => { len += v; });
      const seq = terms(r['ref-title'] + ' ' + (r['ref-full-text'] || r['ref-text']));
      const bigrams = new Set();
      for (let i = 0; i < seq.length - 1; i++) bigrams.add(seq[i] + ' ' + seq[i + 1]);
      DOCS.push({ ref: r, tf, len, bigrams });
      tf.forEach((_, t) => DF.set(t, (DF.get(t) || 0) + 1));
    }
    AVG = DOCS.reduce((s, d) => s + d.len, 0) / Math.max(1, DOCS.length);
  }

  function idf(t) { const n = DF.get(t) || 0; return Math.log(1 + (DOCS.length - n + 0.5) / (n + 0.5)); }

  /** BM25 ranking of the references for a question. */
  function rank(q) {
    const qt = queryTerms(q);
    const seq = terms(q);
    const qBigrams = [];
    for (let i = 0; i < seq.length - 1; i++) qBigrams.push(seq[i] + ' ' + seq[i + 1]);
    const k1 = 1.4, b = 0.7;
    const scored = [];
    for (const d of DOCS) {
      let s = 0, hit = 0;
      qt.forEach((w, t) => {
        const f = d.tf.get(t);
        if (!f) return;
        hit++;
        s += w * idf(t) * (f * (k1 + 1)) / (f + k1 * (1 - b + b * d.len / AVG));
      });
      if (!s) continue;
      for (const bg of qBigrams) if (d.bigrams.has(bg)) s += 1.2;
      s *= 0.6 + 0.4 * (hit / Math.max(1, qt.size)); // coverage of the question
      scored.push({ ref: d.ref, score: s });
    }
    return { list: scored.sort((a, b) => b.score - a.score), qt };
  }

  /** The quiz question most similar to the user's question (Jaccard on stems), if close enough. */
  function similarQuestion(q) {
    const qs = new Set(terms(q));
    if (qs.size < 2) return null;
    let best = null;
    for (const e of Q_INDEX) {
      let inter = 0; qs.forEach(t => { if (e.terms.has(t)) inter++; });
      const j = inter / (qs.size + e.terms.size - inter);
      if (!best || j > best.j) best = { ...e, j };
    }
    return best && best.j >= 0.42 ? best : null;
  }

  /** Best sentences of a reference for the question (extractive answer). */
  function bestSentences(ref, qt, max) {
    const text = (ref['ref-full-text'] || ref['ref-text'] || '').replace(/\r/g, '');
    const parts = text.split(/(?<=[.;:!?])\s+(?=[A-ZÀ-Ü0-9«"(-])|\n+/).map(s => s.replace(/^[-–•]\s*/, '').trim()).filter(s => s.length > 12);
    if (!parts.length) return [ref['ref-text']];
    const sc = parts.map((s, i) => {
      const st = new Set(terms(s));
      let v = 0; qt.forEach((w, t) => { if (st.has(t)) v += w * idf(t); });
      return { s, i, v: v - i * 0.01 };
    });
    const top = sc.slice().sort((a, b) => b.v - a.v).slice(0, max).filter(x => x.v > 0).sort((a, b) => a.i - b.i).map(x => x.s);
    return top.length ? top : [ref['ref-text']];
  }

  /** Main entry: question → answer object. */
  function answer(question) {
    if (!DOCS.length) build();
    const q = String(question || '').trim();
    // Direct reference code ("RG-15.4", "regola 15.4", "casistica 8.3")
    const code = q.toUpperCase().match(/\b(RG|CAS|IT|NF|VAD)[-\s]?(\d+(?:\.\d+)*)\b/) || q.match(/\bregol[ae]\s+(\d+(?:\.\d+)*)\b/i);
    if (code) {
      const key = code.length === 3 ? code[1] + '-' + code[2] : 'RG-' + code[1];
      const r = BY_CODE[key.toUpperCase()];
      if (r) return { kind: 'code', refs: [{ ref: r, sentences: [r['ref-full-text'] || r['ref-text']], score: 99 }], confidence: 'high' };
    }
    const { list, qt } = rank(q);
    const quiz = similarQuestion(q);
    if (!list.length && !quiz) return { kind: 'none', refs: [], confidence: 'none' };
    const top = list[0] ? list[0].score : 0;
    // Keep the references reasonably close to the best one (at most 3)
    const chosen = list.filter(x => x.score >= top * 0.55).slice(0, 3);
    if (quiz) for (const rc of quiz.q['question-ref'] || []) {
      const r = BY_CODE[rc.toUpperCase()];
      if (r && !chosen.some(c => c.ref === r)) chosen.unshift({ ref: r, score: top });
    }
    const refs = chosen.slice(0, 3).map((c, i) => ({ ref: c.ref, score: c.score, sentences: bestSentences(c.ref, qt, i === 0 ? 3 : 2) }));
    const confidence = quiz ? 'high' : top > 9 ? 'high' : top > 4.5 ? 'medium' : 'low';
    return { kind: 'search', refs, quiz, confidence, related: list.slice(3, 7).map(x => x.ref) };
  }

  // ---------------------------------------------------------------- B. local language model (WebLLM)

  const WEBLLM_URL = 'https://cdn.jsdelivr.net/npm/@mlc-ai/web-llm@0.2.85/lib/index.js';
  const MODELS = {
    light: { label: 'Leggero', size: '≈ 0,4 GB', id: 'Qwen2.5-0.5B-Instruct', note: 'veloce, risposte più semplici' },
    standard: { label: 'Standard', size: '≈ 1 GB', id: 'Qwen2.5-1.5B-Instruct', note: 'consigliato' },
    quality: { label: 'Qualità', size: '≈ 2 GB', id: 'Qwen2.5-3B-Instruct', note: 'risposte migliori, serve un dispositivo potente' },
  };
  const LLM = {
    lib: null, engine: null, modelId: null, state: 'off', progress: 0, text: '', f16: true, busy: false,
    supported() { return 'gpu' in navigator && window.isSecureContext; },
    /** WebLLM needs at least 10 storage buffers per shader stage (Firefox gives 9): checked before downloading. */
    async gpuProblem() {
      try {
        const ad = await navigator.gpu.requestAdapter();
        if (!ad) return 'Il browser non ha trovato una scheda grafica compatibile con WebGPU.';
        if ((ad.limits.maxStorageBuffersPerShaderStage || 0) < 10) return 'La WebGPU di questo browser è troppo limitata per il modello (succede per esempio con Firefox).';
        return '';
      } catch (e) { return 'WebGPU non disponibile.'; }
    },
    choice() { try { return localStorage.getItem('fischietto-chatbot-model') || 'standard'; } catch (e) { return 'standard'; } },
    setChoice(k) { try { localStorage.setItem('fischietto-chatbot-model', k); } catch (e) { /* private mode */ } },
    async fullId(k) {
      if (this.lib === null) this.lib = await import(WEBLLM_URL);
      try { const ad = await navigator.gpu.requestAdapter(); this.f16 = !!(ad && ad.features.has('shader-f16')); } catch (e) { this.f16 = false; }
      return MODELS[k].id + (this.f16 ? '-q4f16_1-MLC' : '-q4f32_1-MLC');
    },
    async cached(k) { try { const id = await this.fullId(k); return await this.lib.hasModelInCache(id); } catch (e) { return false; } },
    async load(k, onProgress) {
      this.state = 'loading'; this.progress = 0; this.text = 'Preparazione…'; onProgress();
      try {
        const id = await this.fullId(k);
        if (this.engine) { try { await this.engine.unload(); } catch (e) { /* ignore */ } }
        this.engine = await this.lib.CreateMLCEngine(id, { initProgressCallback: r => { this.progress = r.progress || 0; this.text = r.text || ''; onProgress(); } });
        this.modelId = id; this.state = 'ready';
      } catch (e) {
        this.engine = null; this.state = 'error'; this.text = (e && e.message) || String(e);
      }
      onProgress();
    },
    async generate(messages, onToken) {
      this.busy = true;
      try {
        const chunks = await this.engine.chat.completions.create({ messages, stream: true, temperature: 0.2, top_p: 0.9, max_tokens: 420 });
        let out = '';
        for await (const c of chunks) {
          const d = c.choices && c.choices[0] && c.choices[0].delta && c.choices[0].delta.content;
          if (d) { out += d; onToken(out); }
        }
        return out;
      } finally { this.busy = false; }
    },
    stop() { if (this.engine && this.busy) this.engine.interruptGenerate(); },
  };

  const SYSTEM_PROMPT = 'Sei l’assistente di Fischietto, un portale per arbitri di pallavolo FIPAV. ' +
    'Rispondi in italiano, in modo chiaro e breve (al massimo 5 frasi), alla domanda dell’arbitro usando SOLO i riferimenti forniti nel messaggio. ' +
    'Interpreta la domanda anche se è scritta con parole diverse da quelle del regolamento. ' +
    'Dopo ogni affermazione cita il codice del riferimento tra parentesi quadre, per esempio [RG-15.4]. ' +
    'Non inventare regole, numeri o codici. Se i riferimenti non contengono la risposta, scrivi soltanto: «Non trovo la risposta nei riferimenti di Fischietto.»';

  /** Messages for the model: system prompt, the last exchanges (for follow-up questions), the references found. */
  function buildMessages(question, found, prev) {
    let ctx = '', budget = 6500;
    if (found.quiz) ctx += '[QUIZ] Domanda: ' + found.quiz.q['question-text'] + ' — Risposta corretta: ' + found.quiz.answer + '\n\n';
    for (const r of found.refs) {
      const body = (r.ref['ref-full-text'] || r.ref['ref-text'] || '').replace(/\s+/g, ' ').trim();
      const piece = '[' + r.ref['ref-code'] + '] ' + r.ref['ref-title'] + ' (' + r.ref['ref-from'] + ')\n' + body.slice(0, 1100) + '\n\n';
      if (ctx.length + piece.length > budget) break;
      ctx += piece;
    }
    const msgs = [{ role: 'system', content: SYSTEM_PROMPT }];
    for (const p of prev.slice(-2)) { msgs.push({ role: 'user', content: p.q }); msgs.push({ role: 'assistant', content: p.a }); }
    msgs.push({ role: 'user', content: 'Riferimenti:\n' + ctx + 'Domanda: ' + question });
    return msgs;
  }

  /** Retrieval used by the assistant: more references, and the previous question for short follow-ups. */
  function retrieveFor(question, prev) {
    let q = question;
    if (prev.length && words(question).length < 6) q = prev[prev.length - 1].q + ' ' + question; // "e per il libero?"
    if (!DOCS.length) build();
    const { list, qt } = rank(q);
    const quiz = similarQuestion(question);
    const top = list[0] ? list[0].score : 0;
    const chosen = list.filter(x => x.score >= top * 0.35).slice(0, 6);
    if (quiz) for (const rc of quiz.q['question-ref'] || []) {
      const r = BY_CODE[rc.toUpperCase()];
      if (r && !chosen.some(c => c.ref === r)) chosen.unshift({ ref: r, score: top });
    }
    return { refs: chosen.slice(0, 6), quiz, qt };
  }

  // ---------------------------------------------------------------- UI

  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  /** Highlights the words of the question in a sentence. */
  function highlight(text, qt) {
    return esc(text).replace(/[A-Za-zÀ-ÿ0-9]+/g, w => (qt.has(stem(norm(w))) && w.length > 2 ? '<mark>' + w + '</mark>' : w));
  }
  /** Model text → HTML: [RG-15.4] become buttons that open the reference, **bold**, line breaks. */
  function formatAnswer(text) {
    return esc(text)
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(/\[((?:RG|CAS|IT|NF|VAD)-[0-9][0-9A-Za-z.]*)\]/g, (m, c) => BY_CODE[c.toUpperCase()] ? '<button type="button" class="cb-cite" data-ref="' + c + '">' + c + '</button>' : '<span class="cb-cite bad" title="Codice non trovato nei riferimenti">' + c + '</span>')
      .replace(/\n{2,}/g, '</p><p>').replace(/\n/g, '<br>');
  }
  const SRC_CLASS = { 'Regole di gioco': 'rg', 'Casistica': 'cas', 'Indicazioni tecniche': 'it', 'Normative federali': 'nf', 'Vademecum': 'vad' };
  const EXAMPLES = [
    'Quanto è alta la rete per le donne?',
    'Quante sostituzioni può fare una squadra in un set?',
    'Il libero può alzare in palleggio davanti alla linea dei 3 metri?',
    'Se un giocatore tocca la rete mentre la palla è dall’altra parte, è fallo?',
    'Quanto dura un tempo di riposo?',
    'Il battitore può pestare la linea di fondo?',
  ];
  const STORE = 'fischietto-chatbot';
  let history = [];   // { role: 'user', text } | { role: 'bot', q, mode: 'search' | 'ai', a, refs: [codes], done }
  let mounted = false;

  function save() { try { sessionStorage.setItem(STORE, JSON.stringify(history.slice(-30))); } catch (e) { /* private mode */ } }
  function load() { try { history = (JSON.parse(sessionStorage.getItem(STORE) || '[]') || []).map(m => (m.role === 'bot' && m.mode === 'ai' && !m.done ? Object.assign(m, { done: true, a: m.a || '(interrotta)' }) : m)); } catch (e) { history = []; } }

  function refCard(ref, sentences, qt, main) {
    const cls = SRC_CLASS[ref['ref-from']] || 'rg';
    return '<div class="cb-ref' + (main ? ' cb-main' : '') + '">' +
      '<div class="cb-ref-head"><span class="cb-src cb-src-' + cls + '">' + esc(ref['ref-code']) + '</span><strong>' + esc(ref['ref-title']) + '</strong><small>' + esc(ref['ref-from']) + '</small></div>' +
      (sentences || []).map(s => '<p>' + highlight(s, qt) + '</p>').join('') +
      '<button type="button" class="cb-open" data-ref="' + esc(ref['ref-code']) + '">Apri nel Ripasso →</button></div>';
  }

  function renderSearchAnswer(q) {
    const a = answer(q);
    const qt = queryTerms(q);
    if (a.kind === 'none') return '<p>Non ho trovato niente di pertinente nei riferimenti. Prova con altre parole o cerca per codice, come <code>RG-15.4</code>.</p>';
    let html = '';
    const conf = { high: ['ok', 'Risposta trovata'], medium: ['mid', 'Possibile risposta'], low: ['low', 'Risposta incerta: controlla le fonti'] }[a.confidence];
    if (conf && a.kind !== 'code') html += '<span class="cb-conf cb-conf-' + conf[0] + '">' + conf[1] + '</span>';
    if (a.quiz) html += '<div class="cb-quiz"><span class="cb-label">Dal quiz</span><p class="cb-qtext">' + esc(a.quiz.q['question-text']) + '</p><p class="cb-qans">→ <strong>' + esc(a.quiz.answer) + '</strong></p></div>';
    a.refs.forEach((r, i) => { html += refCard(r.ref, r.sentences, qt, i === 0); });
    if (a.related && a.related.length) html += '<div class="cb-related"><span class="cb-label">Vedi anche</span>' + a.related.map(r => '<button type="button" class="cb-chip" data-ref="' + esc(r['ref-code']) + '">' + esc(r['ref-code']) + ' · ' + esc(r['ref-title']) + '</button>').join('') + '</div>';
    return html;
  }

  function renderAiAnswer(m) {
    const cited = new Set((m.a.match(/\[((?:RG|CAS|IT|NF|VAD)-[0-9][0-9A-Za-z.]*)\]/g) || []).map(x => x.slice(1, -1).toUpperCase()));
    const used = (m.refs || []).map(c => BY_CODE[c]).filter(Boolean);
    let html = '<span class="cb-conf cb-conf-ai">Assistente · AI locale</span><div class="cb-ai"><p>' + (m.a ? formatAnswer(m.a) : '<span class="cb-typing"><i></i><i></i><i></i></span>') + (m.done ? '' : '<span class="cb-caret"></span>') + '</p></div>';
    if (m.done && used.length) {
      html += '<details class="cb-sources"' + (cited.size ? '' : ' open') + '><summary>Riferimenti consultati (' + used.length + ')</summary>' +
        used.map(r => '<button type="button" class="cb-chip' + (cited.has(r['ref-code'].toUpperCase()) ? ' on' : '') + '" data-ref="' + esc(r['ref-code']) + '">' + esc(r['ref-code']) + ' · ' + esc(r['ref-title']) + '</button>').join('') + '</details>';
      html += '<p class="cb-disclaimer">Risposta generata sul tuo dispositivo: verifica sempre il testo del riferimento.</p>';
    }
    if (!m.done) html += '<button type="button" class="cb-stop" data-stop="1">Ferma</button>';
    return html;
  }

  function paint() {
    const log = document.getElementById('cb-log');
    if (!history.length) {
      log.innerHTML = '<div class="cb-welcome"><p><strong>Fai una domanda sul regolamento</strong>, con parole tue. Cerco la risposta nei ' + REFS.length + ' riferimenti e nelle ' + QUESTIONS.length + ' domande del quiz' +
        (LLM.state === 'ready' ? ' e l’assistente la spiega, citando i riferimenti.' : '.') + ' Niente viene inviato a servizi esterni.</p>' +
        '<div class="cb-examples">' + EXAMPLES.map(e => '<button type="button" class="cb-chip" data-ask="' + esc(e) + '">' + esc(e) + '</button>').join('') + '</div></div>';
      return;
    }
    log.innerHTML = history.map(m => m.role === 'user'
      ? '<div class="cb-msg cb-user"><div class="cb-bubble">' + esc(m.text) + '</div></div>'
      : '<div class="cb-msg cb-bot"><div class="cb-bubble">' + (m.mode === 'ai' ? renderAiAnswer(m) : renderSearchAnswer(m.q)) + '</div></div>').join('');
  }
  function scrollToLast() { const log = document.getElementById('cb-log'); if (log.lastElementChild) log.lastElementChild.scrollIntoView({ block: 'start', behavior: 'smooth' }); }

  async function ask(text) {
    text = String(text || '').trim();
    if (!text || LLM.busy) return;
    history.push({ role: 'user', text });
    if (LLM.state !== 'ready') {
      history.push({ role: 'bot', q: text, mode: 'search' });
      save(); paint(); scrollToLast();
      return;
    }
    const prev = history.filter(m => m.role === 'bot' && m.mode === 'ai' && m.done && m.a).map(m => ({ q: m.q, a: m.a }));
    const found = retrieveFor(text, prev);
    const m = { role: 'bot', q: text, mode: 'ai', a: '', refs: found.refs.map(r => r.ref['ref-code'].toUpperCase()), done: false };
    history.push(m);
    paint(); scrollToLast(); setBusy(true);
    try {
      let last = 0;
      m.a = await LLM.generate(buildMessages(text, found, prev), out => {
        m.a = out;
        if (Date.now() - last > 60) { last = Date.now(); const b = document.querySelector('#cb-log .cb-msg:last-child .cb-bubble'); if (b) b.innerHTML = renderAiAnswer(m); }
      });
    } catch (e) {
      m.a = (m.a ? m.a + '\n\n' : '') + '(Errore dell’assistente: ' + ((e && e.message) || e) + ')';
    }
    m.a = m.a.trim() || 'Non trovo la risposta nei riferimenti di Fischietto.';
    m.done = true;
    setBusy(false); save(); paint();
  }

  function setBusy(on) {
    const b = document.getElementById('cb-send');
    if (b) b.disabled = on;
  }

  /** Status bar of the assistant: unsupported / not downloaded / downloading x% / ready. */
  async function paintStatus() {
    const box = document.getElementById('cb-status');
    if (!box) return;
    if (!LLM.supported()) {
      box.innerHTML = '<span class="cb-st-dot off"></span><span><strong>Modalità ricerca.</strong> Questo browser non supporta WebGPU: l’assistente AI locale non può funzionare qui (prova con Chrome o Edge aggiornati, anche su Android). Le risposte arrivano dai riferimenti più pertinenti.</span>';
      return;
    }
    const problem = LLM.state === 'ready' ? '' : await LLM.gpuProblem();
    if (problem) {
      box.innerHTML = '<span class="cb-st-dot off"></span><span><strong>Modalità ricerca.</strong> ' + esc(problem) + ' Per l’assistente AI locale usa <strong>Chrome</strong> o <strong>Edge</strong> aggiornati (computer o Android). Le risposte arrivano comunque dai riferimenti più pertinenti.</span>';
      return;
    }
    const k = LLM.choice(), M = MODELS[k];
    if (LLM.state === 'loading') {
      const pct = Math.round(LLM.progress * 100);
      box.innerHTML = '<span class="cb-st-dot load"></span><span><strong>Preparo l’assistente (' + M.label + ')… ' + pct + '%</strong><br><small>' + esc(LLM.text).slice(0, 140) + '</small><span class="cb-bar"><i style="width:' + pct + '%"></i></span></span>';
      return;
    }
    if (LLM.state === 'ready') {
      box.innerHTML = '<span class="cb-st-dot on"></span><span><strong>Assistente attivo</strong> · modello ' + M.label + ' sul tuo dispositivo. Interpreta la domanda e risponde usando solo i riferimenti di Fischietto.</span>' +
        '<button type="button" class="cb-link" id="cb-model">Cambia modello</button>';
      document.getElementById('cb-model').onclick = chooseModel;
      return;
    }
    const cached = await LLM.cached(k).catch(() => false);
    box.innerHTML = '<span class="cb-st-dot off"></span><span>' + (LLM.state === 'error' ? '<strong>Assistente non avviato:</strong> ' + esc(LLM.text).slice(0, 200) + '<br>' : '<strong>Modalità ricerca.</strong> ') +
      'Attiva l’<strong>assistente AI locale</strong> per risposte scritte che interpretano la domanda: il modello gira sul tuo dispositivo' + (cached ? ' ed è già scaricato.' : ' e va scaricato una volta (' + M.size + ').') + '</span>' +
      '<span class="cb-st-actions"><button type="button" class="btn btn-primary btn-sm" id="cb-enable">' + (cached ? 'Attiva' : 'Scarica e attiva') + '</button><button type="button" class="cb-link" id="cb-model">Modello: ' + M.label + '</button></span>';
    document.getElementById('cb-enable').onclick = () => LLM.load(LLM.choice(), paintStatusAndLog);
    document.getElementById('cb-model').onclick = chooseModel;
  }
  function paintStatusAndLog() { paintStatus(); if (LLM.state === 'ready' && !history.length) paint(); }

  function chooseModel() {
    const cur = LLM.choice();
    const box = document.getElementById('cb-status');
    box.innerHTML = '<span><strong>Scegli il modello</strong> (resta salvato nel browser):</span><span class="cb-models">' + Object.entries(MODELS).map(([k, M]) =>
      '<button type="button" class="cb-model' + (k === cur ? ' sel' : '') + '" data-model="' + k + '"><strong>' + M.label + '</strong><small>' + M.size + ' · ' + M.note + '</small></button>').join('') + '</span>';
    box.querySelectorAll('[data-model]').forEach(b => b.onclick = () => {
      LLM.setChoice(b.dataset.model);
      if (LLM.state === 'ready' && b.dataset.model !== cur) LLM.load(b.dataset.model, paintStatusAndLog);
      else paintStatus();
    });
  }

  function mount() {
    if (mounted) return;
    mounted = true;
    load();
    const form = document.getElementById('cb-form');
    const input = document.getElementById('cb-input');
    form.addEventListener('submit', e => { e.preventDefault(); const v = input.value; input.value = ''; input.style.height = ''; ask(v); });
    input.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.requestSubmit(); } });
    input.addEventListener('input', () => { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 140) + 'px'; });
    document.getElementById('cb-clear').addEventListener('click', () => { if (LLM.busy) LLM.stop(); history = []; save(); paint(); input.focus(); });
    document.getElementById('cb-log').addEventListener('click', e => {
      const b = e.target.closest('[data-ask],[data-ref],[data-stop]');
      if (!b) return;
      if (b.dataset.stop) LLM.stop();
      else if (b.dataset.ask) ask(b.dataset.ask);
      else if (window.jumpToRef) window.jumpToRef(b.dataset.ref);
    });
    paint();
    paintStatus();
  }

  window.Chatbot = { open() { mount(); setTimeout(() => { const i = document.getElementById('cb-input'); if (i && matchMedia('(pointer: fine)').matches) i.focus(); }, 50); }, answer, retrieveFor, buildMessages };
})();
