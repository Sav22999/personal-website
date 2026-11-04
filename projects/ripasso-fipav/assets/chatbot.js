/*
 * Fischietto — Chatbot (alpha, administrators only).
 *
 * Answers questions in natural language using ONLY the references and quiz questions of the portal
 * (window.APP_CONTENT). Nothing is sent to external services: everything runs in the browser.
 *
 * Two layers:
 *  A. Retrieval (always available, instant): finds the references that answer the question.
 *  B. Assistant (optional): a small language model (Qwen2.5 Instruct) runs ON THE DEVICE — WebLLM on
 *     WebGPU, or Transformers.js on WebAssembly where WebGPU is not enough —, reads only the references
 *     found in step A and writes the answer in its own words,
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

  // Two engines, chosen automatically:
  //  - "gpu": WebLLM on WebGPU (fast; Chrome/Edge, also Android);
  //  - "cpu": Transformers.js on WebAssembly (slower, but works everywhere, Firefox included).
  const WEBLLM_URL = 'https://cdn.jsdelivr.net/npm/@mlc-ai/web-llm@0.2.85/lib/index.js';
  // dist/transformers.min.js is the self-contained build (the .web build needs a bundler for onnxruntime)
  const TFJS_URL = 'https://cdn.jsdelivr.net/npm/@huggingface/transformers@3.7.5/dist/transformers.min.js';
  const MODELS = {
    gpu: {
      light: { label: 'Leggero', size: '≈ 0,4 GB', id: 'Qwen2.5-0.5B-Instruct', note: 'veloce, risposte più semplici' },
      standard: { label: 'Standard', size: '≈ 1 GB', id: 'Qwen2.5-1.5B-Instruct', note: 'consigliato' },
      quality: { label: 'Qualità', size: '≈ 2 GB', id: 'Qwen2.5-3B-Instruct', note: 'risposte migliori, serve un dispositivo potente' },
    },
    cpu: {
      light: { label: 'Leggero', size: '≈ 0,5 GB', id: 'onnx-community/Qwen2.5-0.5B-Instruct', note: 'consigliato con il processore' },
      standard: { label: 'Standard', size: '≈ 1,2 GB', id: 'onnx-community/Qwen2.5-1.5B-Instruct', note: 'più preciso ma lento (meglio su computer)' },
    },
  };
  const LLM = {
    kind: null, lib: null, engine: null, gen: null, stopper: null, modelId: null, state: 'off', progress: 0, text: '', f16: true, busy: false, files: {},
    /** Which engine this browser can use: 'gpu', 'cpu' or '' (none), with the reason when WebGPU is not usable. */
    async detect() {
      if (this.kind !== null) return this.kind;
      this.gpuNote = '';
      if ('gpu' in navigator && window.isSecureContext) {
        try {
          // requestAdapter can hang on some drivers: after 4 s it counts as "no adapter"
          const ad = await Promise.race([navigator.gpu.requestAdapter(), new Promise(r => setTimeout(() => r(null), 4000))]);
          // WebLLM needs at least 10 storage buffers per shader stage (Firefox gives 9)
          if (ad && (ad.limits.maxStorageBuffersPerShaderStage || 0) >= 10) { this.f16 = ad.features.has('shader-f16'); return (this.kind = 'gpu'); }
          this.gpuNote = ad ? 'La WebGPU di questo browser è troppo limitata per il motore veloce (succede per esempio con Firefox)' : 'Nessuna scheda grafica compatibile con WebGPU';
        } catch (e) { this.gpuNote = 'WebGPU non disponibile'; }
      } else this.gpuNote = 'Questo browser non ha WebGPU';
      return (this.kind = typeof WebAssembly === 'object' ? 'cpu' : '');
    },
    models() { return MODELS[this.kind] || MODELS.cpu; },
    choice() {
      let k = 'standard';
      try { k = localStorage.getItem('fischietto-chatbot-model-' + this.kind) || (this.kind === 'cpu' ? 'light' : 'standard'); } catch (e) { /* private mode */ }
      return this.models()[k] ? k : Object.keys(this.models())[0];
    },
    setChoice(k) { try { localStorage.setItem('fischietto-chatbot-model-' + this.kind, k); } catch (e) { /* private mode */ } },
    async gpuId(k) {
      if (!this.lib) this.lib = await import(WEBLLM_URL);
      return MODELS.gpu[k].id + (this.f16 ? '-q4f16_1-MLC' : '-q4f32_1-MLC');
    },
    async cached(k) {
      try {
        if (this.kind === 'gpu') return await this.lib.hasModelInCache(await this.gpuId(k));
        const c = await caches.open('transformers-cache');
        const keys = await c.keys();
        return keys.some(r => r.url.includes(MODELS.cpu[k].id + '/') && /\.onnx/.test(r.url));
      } catch (e) { return false; }
    },
    async load(k, onProgress) {
      this.state = 'loading'; this.progress = 0; this.text = 'Preparazione…'; this.files = {}; onProgress();
      try {
        if (this.kind === 'gpu') {
          const id = await this.gpuId(k);
          if (this.engine) { try { await this.engine.unload(); } catch (e) { /* ignore */ } }
          this.engine = await this.lib.CreateMLCEngine(id, { initProgressCallback: r => { this.progress = r.progress || 0; this.text = r.text || ''; onProgress(); } });
          this.modelId = id;
        } else {
          const T = this.tf || (this.tf = await import(TFJS_URL));
          T.env.allowLocalModels = false;
          if (this.gen && this.gen.dispose) { try { await this.gen.dispose(); } catch (e) { /* ignore */ } }
          let last = 0;
          this.gen = await T.pipeline('text-generation', MODELS.cpu[k].id, {
            dtype: 'q4', device: 'wasm',
            progress_callback: p => {
              if (p.status === 'progress' && p.file) this.files[p.file] = [p.loaded || 0, p.total || 0];
              const v = Object.values(this.files), tot = v.reduce((a, x) => a + x[1], 0), done = v.reduce((a, x) => a + x[0], 0);
              this.progress = tot ? done / tot : 0;
              this.text = p.file ? 'Scarico ' + p.file + (tot ? ' — ' + (done / 1048576).toFixed(0) + ' / ' + (tot / 1048576).toFixed(0) + ' MB' : '') : 'Avvio del modello…';
              if (Date.now() - last > 150) { last = Date.now(); onProgress(); }
            },
          });
          this.modelId = MODELS.cpu[k].id;
        }
        this.state = 'ready';
      } catch (e) {
        this.engine = null; this.gen = null; this.state = 'error'; this.text = (e && e.message) || String(e);
      }
      onProgress();
    },
    async generate(messages, onToken) {
      this.busy = true;
      try {
        let out = '';
        if (this.kind === 'gpu') {
          const chunks = await this.engine.chat.completions.create({ messages, stream: true, temperature: 0.2, top_p: 0.9, max_tokens: 420 });
          for await (const c of chunks) {
            const d = c.choices && c.choices[0] && c.choices[0].delta && c.choices[0].delta.content;
            if (d) { out += d; onToken(out); }
          }
        } else {
          const T = this.tf;
          this.stopper = new T.InterruptableStoppingCriteria();
          const streamer = new T.TextStreamer(this.gen.tokenizer, { skip_prompt: true, skip_special_tokens: true, callback_function: txt => { out += txt; onToken(out); } });
          await this.gen(messages, { max_new_tokens: 320, do_sample: false, repetition_penalty: 1.1, streamer, stopping_criteria: this.stopper });
        }
        return out;
      } finally { this.busy = false; }
    },
    stop() {
      if (!this.busy) return;
      if (this.kind === 'gpu' && this.engine) this.engine.interruptGenerate();
      else if (this.stopper) this.stopper.interrupt();
    },
  };

  const SYSTEM_PROMPT = 'Sei l’assistente di Fischietto, un portale per arbitri di pallavolo FIPAV. ' +
    'Rispondi in italiano, in modo chiaro e breve (al massimo 5 frasi), alla domanda dell’arbitro usando SOLO i riferimenti forniti nel messaggio. ' +
    'Interpreta la domanda anche se è scritta con parole diverse da quelle del regolamento. ' +
    'Dopo ogni affermazione cita il codice del riferimento tra parentesi quadre, per esempio [RG-15.4]. ' +
    'Non inventare regole, numeri o codici. Se i riferimenti non contengono la risposta, scrivi soltanto: «Non trovo la risposta nei riferimenti di Fischietto.»';

  /** Messages for the model: system prompt, the last exchanges (for follow-up questions), the references found. */
  function buildMessages(question, found, prev) {
    let ctx = '', budget = LLM.kind === 'cpu' ? 3600 : 6500;
    if (found.quiz) ctx += '[QUIZ] Domanda: ' + found.quiz.q['question-text'] + ' — Risposta corretta: ' + found.quiz.answer + '\n\n';
    for (const r of found.refs) {
      const body = (r.ref['ref-full-text'] || r.ref['ref-text'] || '').replace(/\s+/g, ' ').trim();
      const piece = '[' + r.ref['ref-code'] + '] ' + r.ref['ref-title'] + ' (' + r.ref['ref-from'] + ')\n' + body.slice(0, LLM.kind === 'cpu' ? 800 : 1100) + '\n\n';
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
  const $ = id => document.getElementById(id);
  const CITE_RE = /\[((?:RG|CAS|IT|NF|VAD)-[0-9][0-9A-Za-z.]*)\]/g;
  const svg = (d, size, fill) => '<svg width="' + (size || 16) + '" height="' + (size || 16) + '" viewBox="0 0 24 24" ' + (fill ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"') + ' aria-hidden="true">' + d + '</svg>';
  const ICON = {
    send: svg('<path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2z"/>', 18, true),
    stop: svg('<rect x="6" y="6" width="12" height="12" rx="2"/>', 16, true),
    copy: svg('<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>', 14),
    check: svg('<path d="M20 6 9 17l-5-5"/>', 14),
    redo: svg('<path d="M21 12a9 9 0 1 1-2.64-6.36L21 8"/><path d="M21 3v5h-5"/>', 14),
    spark: svg('<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M19 15l.7 2.3L22 18l-2.3.7L19 21l-.7-2.3L16 18l2.3-.7z"/>', 14),
    search: svg('<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>', 14),
    plus: svg('<path d="M12 5v14M5 12h14"/>', 16),
    chevron: svg('<path d="m6 9 6 6 6-6"/>', 16),
  };

  /** Highlights the words of the question in a sentence. */
  function highlight(text, qt) {
    return esc(text).replace(/[A-Za-zÀ-ÿ0-9]+/g, w => (qt && qt.has(stem(norm(w))) && w.length > 2 ? '<mark>' + w + '</mark>' : w));
  }
  /** Model text → HTML: [RG-15.4] become buttons that open the reference, **bold**, line breaks. */
  function formatAnswer(text) {
    return esc(text)
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(CITE_RE, (m, c) => BY_CODE[c.toUpperCase()]
        ? '<button type="button" class="cb-cite" data-ref="' + c + '" title="Leggi il riferimento ' + c + '">' + c + '</button>'
        : '<span class="cb-cite bad" title="Codice non trovato nei riferimenti">' + c + '</span>')
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
  const ENGINE = { gpu: 'scheda grafica (WebGPU)', cpu: 'processore' };
  const STORE = 'fischietto-chatbot';
  let history = [];   // { role: 'user', text } | { role: 'bot', q, mode: 'search' | 'ai', a, refs: [codes], done, error, stopped }
  let mounted = false;
  let statusOpen = false;
  let stopRequested = false;

  function save() { try { sessionStorage.setItem(STORE, JSON.stringify(history.slice(-30))); } catch (e) { /* private mode */ } }
  function load() {
    try {
      history = (JSON.parse(sessionStorage.getItem(STORE) || '[]') || []).map(m => (m.role === 'bot' && m.mode === 'ai' && !m.done ? Object.assign(m, { done: true, stopped: true }) : m));
    } catch (e) { history = []; }
  }
  function announce(text) { const a = $('cb-announce'); if (a) { a.textContent = ''; setTimeout(() => { a.textContent = text; }, 30); } }

  // Search answers are computed from the question (they are not saved): cached per message
  const FOUND = new WeakMap();
  function found(m) { if (!FOUND.has(m)) FOUND.set(m, answer(m.q)); return FOUND.get(m); }

  // ---------------------------------------------------------------- UI: messages

  function srcTag(ref) {
    return '<span class="cb-src cb-src-' + (SRC_CLASS[ref['ref-from']] || 'rg') + '">' + esc(ref['ref-code']) + '</span>';
  }
  function refHead(ref) {
    return srcTag(ref) + '<span class="cb-ref-title"><strong>' + esc(ref['ref-title']) + '</strong><small>' + esc(ref['ref-from']) + '</small></span>';
  }
  /** The best reference is shown open; the others are collapsed under their title. */
  function refBlock(ref, sentences, qt, main) {
    const body = (sentences || []).map(s => '<p>' + highlight(s, qt) + '</p>').join('') +
      '<button type="button" class="cb-open" data-ref="' + esc(ref['ref-code']) + '">Leggi tutto il riferimento</button>';
    if (main) return '<div class="cb-ref cb-main"><div class="cb-ref-head">' + refHead(ref) + '</div>' + body + '</div>';
    return '<details class="cb-ref"><summary class="cb-ref-head">' + refHead(ref) + '<span class="cb-chev">' + ICON.chevron + '</span></summary><div class="cb-ref-body">' + body + '</div></details>';
  }
  function refChip(ref, on) {
    return '<button type="button" class="cb-chip cb-chip-ref' + (on ? ' on' : '') + '" data-ref="' + esc(ref['ref-code']) + '" title="' + esc(ref['ref-code'] + ' · ' + ref['ref-title']) + '"><b>' + esc(ref['ref-code']) + '</b><span>' + esc(ref['ref-title']) + '</span></button>';
  }

  function renderSearch(m) {
    const a = found(m);
    if (a.kind === 'none') {
      return '<p>Non ho trovato niente di pertinente nei riferimenti.</p>' +
        '<p class="cb-hint">Prova con altre parole, magari quelle del regolamento (per esempio «servizio» invece di «battuta»), oppure scrivi direttamente un codice come <button type="button" class="cb-cite" data-ask="RG-15.4">RG-15.4</button>.</p>';
    }
    const qt = queryTerms(m.q);
    let html = '';
    if (a.quiz) html += '<div class="cb-quiz"><span class="cb-label">Dal quiz</span><p class="cb-qtext">' + esc(a.quiz.q['question-text']) + '</p><p class="cb-qans">' + ICON.check + '<strong>' + esc(a.quiz.answer) + '</strong></p></div>';
    a.refs.forEach((r, i) => {
      if (i === 1) html += '<span class="cb-label cb-label-more">Altri riferimenti</span>';
      html += refBlock(r.ref, r.sentences, qt, i === 0);
    });
    if (a.related && a.related.length) html += '<div class="cb-related"><span class="cb-label">Vedi anche</span>' + a.related.map(r => refChip(r)).join('') + '</div>';
    return html;
  }

  function renderAi(m) {
    const cited = new Set((m.a.match(CITE_RE) || []).map(x => x.slice(1, -1).toUpperCase()));
    const used = (m.refs || []).map(c => BY_CODE[c]).filter(Boolean);
    let html;
    if (m.a) html = '<div class="cb-ai"><p>' + formatAnswer(m.a) + (m.done ? '' : '<span class="cb-caret"></span>') + '</p></div>';
    else if (!m.done) html = '<p class="cb-thinking"><span class="cb-typing"><i></i><i></i><i></i></span>Leggo ' + (used.length === 1 ? 'il riferimento trovato' : 'i ' + used.length + ' riferimenti trovati') + (LLM.kind === 'cpu' ? '… con il processore la prima parola può arrivare dopo qualche secondo.' : '…') + '</p>';
    else html = '';
    if (m.stopped) html += '<p class="cb-note">Risposta interrotta.</p>';
    if (m.error) html += '<p class="cb-error">L’assistente si è fermato per un errore: ' + esc(m.error) + '</p>';
    if (m.done && used.length) {
      html += '<details class="cb-sources"' + (cited.size ? '' : ' open') + '><summary>' + ICON.chevron + 'Riferimenti consultati (' + used.length + ')</summary><div class="cb-related">' +
        used.map(r => refChip(r, cited.has(r['ref-code'].toUpperCase()))).join('') + '</div></details>';
    }
    return html;
  }

  function botMeta(m) {
    if (m.mode === 'ai') return '<span class="cb-who cb-who-ai">' + ICON.spark + 'Assistente · AI locale</span>';
    const a = found(m);
    const conf = a.kind === 'code' ? ['ok', 'Riferimento'] : { high: ['ok', 'Risposta trovata'], medium: ['mid', 'Possibile risposta'], low: ['low', 'Incerta: controlla le fonti'] }[a.confidence];
    return '<span class="cb-who">' + ICON.search + 'Ricerca nei riferimenti</span>' + (conf ? '<span class="cb-conf cb-conf-' + conf[0] + '">' + conf[1] + '</span>' : '');
  }

  function botActions(m) {
    if (m.mode === 'ai' && !m.done) return '';
    const btn = (act, icon, label) => '<button type="button" class="cb-act" data-act="' + act + '">' + icon + '<span>' + label + '</span></button>';
    let h = '';
    if (m.mode === 'search' ? found(m).kind !== 'none' : !!m.a) h += btn('copy', ICON.copy, 'Copia');
    if (m.mode === 'ai') { if (LLM.state === 'ready') h += btn('redo', ICON.redo, m.error ? 'Riprova' : 'Rigenera'); }
    else if (LLM.state === 'ready' && found(m).kind !== 'none') h += btn('explain', ICON.spark, 'Chiedi all’assistente');
    return h ? '<div class="cb-actions">' + h + '</div>' : '';
  }

  function msgInner(m) {
    if (m.role === 'user') return '<div class="cb-bubble">' + esc(m.text) + '</div>';
    return '<div class="cb-bubble"><div class="cb-meta">' + botMeta(m) + '</div><div class="cb-body">' + (m.mode === 'ai' ? renderAi(m) : renderSearch(m)) + '</div>' + botActions(m) + '</div>';
  }
  const msgHtml = (m, i) => '<div class="cb-msg cb-' + m.role + '" data-i="' + i + '">' + msgInner(m) + '</div>';
  const msgNode = i => document.querySelector('#cb-log .cb-msg[data-i="' + i + '"]');

  function welcomeHtml() {
    return '<div class="cb-welcome">' +
      '<span class="cb-welcome-icon">' + svg('<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>', 22) + '</span>' +
      '<h2>Fai una domanda sul regolamento</h2>' +
      '<p>Scrivi con parole tue: cerco la risposta nei ' + REFS.length + ' riferimenti e nelle ' + QUESTIONS.length + ' domande del quiz' +
      (LLM.state === 'ready' ? ' e l’assistente te la spiega, citando i riferimenti.' : ', e ti mostro i passaggi che rispondono.') + ' Puoi anche scrivere un codice, come RG-15.4.</p>' +
      '<span class="cb-label">Prova con</span><div class="cb-examples">' + EXAMPLES.map(e => '<button type="button" class="cb-chip" data-ask="' + esc(e) + '">' + esc(e) + '</button>').join('') + '</div></div>';
  }

  /** Full repaint (opening, new conversation, assistant switched on). */
  function paint() {
    const log = $('cb-log');
    log.innerHTML = history.length ? history.map(msgHtml).join('') : welcomeHtml();
    $('cb-clear').hidden = !history.length;
  }
  function appendMsg(i) {
    const log = $('cb-log');
    const w = log.querySelector('.cb-welcome');
    if (w) w.remove();
    log.insertAdjacentHTML('beforeend', msgHtml(history[i], i));
    $('cb-clear').hidden = false;
  }
  function updateMsg(i, bodyOnly) {
    const n = msgNode(i);
    if (!n) return;
    const m = history[i];
    if (bodyOnly) { const b = n.querySelector('.cb-body'); if (b) { b.innerHTML = renderAi(m); return; } }
    n.innerHTML = msgInner(m);
  }
  /** Brings the question to the top of the screen, so the answer is read from its beginning. */
  function reveal(i) { const n = msgNode(i); if (n) n.scrollIntoView({ block: 'start', behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }); }

  /** Plain text of an answer, for "Copia". */
  function plainText(m) {
    if (m.mode === 'ai') {
      const used = (m.refs || []).map(c => BY_CODE[c]).filter(Boolean);
      return m.a.trim() + (used.length ? '\n\nRiferimenti consultati: ' + used.map(r => r['ref-code'] + ' (' + r['ref-title'] + ')').join(', ') : '');
    }
    const a = found(m);
    let t = '';
    if (a.quiz) t += 'Dal quiz: ' + a.quiz.q['question-text'] + ' → ' + a.quiz.answer + '\n\n';
    t += a.refs.map(r => r.ref['ref-code'] + ' · ' + r.ref['ref-title'] + ' (' + r.ref['ref-from'] + ')\n' + r.sentences.join(' ')).join('\n\n');
    return t;
  }
  async function copy(m, button) {
    const text = 'Domanda: ' + m.q + '\n\n' + plainText(m);
    try { await navigator.clipboard.writeText(text); } catch (e) {
      const ta = document.createElement('textarea'); ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
      document.body.appendChild(ta); ta.select(); try { document.execCommand('copy'); } catch (e2) { /* ignore */ } ta.remove();
    }
    if (button) {
      const label = button.querySelector('span');
      button.classList.add('done'); button.firstElementChild.outerHTML = ICON.check; label.textContent = 'Copiata';
      setTimeout(() => { if (!button.isConnected) return; button.classList.remove('done'); button.firstElementChild.outerHTML = ICON.copy; label.textContent = 'Copia'; }, 1600);
    }
    announce('Risposta copiata');
  }

  // ---------------------------------------------------------------- UI: asking

  function ask(text) {
    text = String(text || '').trim().slice(0, 600);
    if (!text || LLM.busy) return false;
    history.push({ role: 'user', text });
    const ui = history.length - 1;
    appendMsg(ui);
    // Without the assistant, or when nothing pertinent is found (the model would have nothing to read): search answer
    const ctx = LLM.state === 'ready' ? retrieveFor(text, aiPrev(ui)) : null;
    if (!ctx || (!ctx.refs.length && !ctx.quiz)) {
      const m = { role: 'bot', q: text, mode: 'search' };
      history.push(m);
      appendMsg(history.length - 1);
      save(); reveal(ui);
      const a = found(m);
      announce(a.kind === 'none' ? 'Nessun riferimento pertinente' : 'Risposta trovata in ' + a.refs.map(r => r.ref['ref-code']).join(', '));
      return true;
    }
    history.push({ role: 'bot', q: text, mode: 'ai', a: '', refs: [], done: false });
    appendMsg(history.length - 1);
    reveal(ui);
    runAi(history.length - 1);
    return true;
  }

  /** The assistant's earlier answers before history[i] (for follow-up questions). */
  function aiPrev(i) {
    return history.slice(0, i).filter(x => x.role === 'bot' && x.mode === 'ai' && x.done && x.a && !x.error && !x.stopped).map(x => ({ q: x.q, a: x.a }));
  }

  /** Writes (or rewrites) the assistant's answer of history[i]. */
  async function runAi(i) {
    const m = history[i];
    const prev = aiPrev(i);
    const ctx = retrieveFor(m.q, prev);
    Object.assign(m, { mode: 'ai', a: '', refs: ctx.refs.map(r => r.ref['ref-code'].toUpperCase()), done: false, error: '', stopped: false });
    stopRequested = false;
    updateMsg(i); setBusy(true);
    announce('L’assistente sta scrivendo la risposta');
    try {
      let last = 0;
      m.a = await LLM.generate(buildMessages(m.q, ctx, prev), out => {
        m.a = out;
        if (Date.now() - last > 60) { last = Date.now(); updateMsg(i, true); }
      });
    } catch (e) {
      m.error = (e && e.message) || String(e);
    }
    m.a = (m.a || '').trim();
    if (!m.a && !m.error && !stopRequested) m.a = 'Non trovo la risposta nei riferimenti di Fischietto.';
    m.stopped = stopRequested;
    m.done = true;
    setBusy(false); save(); updateMsg(i);
    announce(m.error ? 'Errore dell’assistente' : m.stopped ? 'Risposta interrotta' : 'Risposta pronta');
  }

  function stop() { if (LLM.busy) { stopRequested = true; LLM.stop(); } }

  /** While the assistant writes, the send button becomes "Ferma". */
  function setBusy(on) {
    const b = $('cb-send'), input = $('cb-input');
    if (!b) return;
    b.classList.toggle('cb-send-stop', !!on);
    b.innerHTML = on ? ICON.stop : ICON.send;
    b.setAttribute('aria-label', on ? 'Ferma la risposta' : 'Invia');
    b.title = on ? 'Ferma la risposta (Esc)' : 'Invia (Invio)';
    b.disabled = on ? false : !input.value.trim();
  }

  // ---------------------------------------------------------------- UI: reference preview

  /** Shows a reference without leaving the chat; "Apri nel Ripasso" goes to the full card. */
  function openRef(code, q) {
    const r = BY_CODE[String(code || '').toUpperCase()];
    if (!r) return;
    const qt = q ? queryTerms(q) : null;
    const back = document.activeElement;
    const full = (r['ref-full-text'] || r['ref-text'] || '').replace(/\r/g, '').trim();
    const paras = full.split(/\n+/).map(p => p.trim()).filter(Boolean).map(p => '<p>' + highlight(p, qt) + '</p>').join('');
    const change = r['ref-change'];
    const ov = document.createElement('div');
    ov.className = 'modal-overlay cb-ref-modal';
    ov.innerHTML = '<div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="cb-ref-title">' +
      '<div class="cb-ref-modal-head">' + srcTag(r) + (typeof changeBadgeHtml === 'function' ? changeBadgeHtml(change) : '') + '<small>' + esc(r['ref-from']) + '</small>' +
      '<button type="button" class="icon-btn" data-close aria-label="Chiudi">' + svg('<path d="M18 6 6 18M6 6l12 12"/>', 18) + '</button></div>' +
      '<h2 class="modal-title" id="cb-ref-title">' + esc(r['ref-title']) + '</h2>' +
      (typeof changeNoteHtml === 'function' ? changeNoteHtml(change) : '') +
      '<div class="cb-ref-text">' + paras + '</div>' +
      (typeof docLinkHtml === 'function' ? docLinkHtml(r['ref-from']) : '') +
      '<div class="modal-actions"><button type="button" class="btn btn-secondary" data-close>Chiudi</button>' +
      (window.jumpToRef ? '<button type="button" class="btn btn-primary" data-jump>Apri nel Ripasso</button>' : '') + '</div></div>';
    const close = () => { ov.remove(); document.removeEventListener('keydown', onKey, true); if (back && back.isConnected) back.focus(); };
    const onKey = e => {
      if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); close(); }
      else if (e.key === 'Tab') { // keep the focus inside the dialog
        const f = [...ov.querySelectorAll('button, a[href]')];
        if (!f.length) return;
        const first = f[0], lastF = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); lastF.focus(); }
        else if (!e.shiftKey && document.activeElement === lastF) { e.preventDefault(); first.focus(); }
      }
    };
    ov.addEventListener('click', e => {
      if (e.target === ov || e.target.closest('[data-close]')) close();
      else if (e.target.closest('[data-jump]')) { close(); window.jumpToRef(r['ref-code']); }
    });
    document.addEventListener('keydown', onKey, true);
    document.body.appendChild(ov);
    // The focus goes to the dialog itself (Tab then reaches its buttons)
    const box = ov.querySelector('.modal-box');
    box.tabIndex = -1;
    box.focus({ preventScroll: true });
  }

  // ---------------------------------------------------------------- UI: assistant status

  function modelPicker(cur) {
    return '<span class="cb-label">Modello</span><div class="cb-models" role="radiogroup" aria-label="Modello dell’assistente">' + Object.entries(LLM.models()).map(([k, M]) =>
      '<button type="button" class="cb-model' + (k === cur ? ' sel' : '') + '" role="radio" aria-checked="' + (k === cur) + '" data-model="' + k + '"><strong>' + M.label + '</strong><small>' + M.size + ' · ' + M.note + '</small></button>').join('') + '</div>';
  }

  /** Status of the assistant: checking / unsupported / off / downloading x% / ready / error. */
  async function paintStatus() {
    const box = $('cb-status');
    if (!box) return;
    const kind = await LLM.detect();
    const row = (dot, title, sub, actions) => '<div class="cb-st-row"><span class="cb-st-dot ' + dot + '"></span><div class="cb-st-text"><strong>' + title + '</strong>' + (sub ? '<span>' + sub + '</span>' : '') + '</div>' + (actions ? '<div class="cb-st-actions">' + actions + '</div>' : '') + '</div>';
    const more = '<button type="button" class="cb-link cb-more" id="cb-more" aria-expanded="' + statusOpen + '" aria-controls="cb-st-more">' + (statusOpen ? 'Meno' : 'Dettagli') + ICON.chevron + '</button>';
    if (!kind) {
      box.className = 'cb-status';
      box.innerHTML = row('off', 'Modalità ricerca', 'Rispondo con i passaggi più pertinenti dei riferimenti. Questo browser non può far girare l’assistente AI locale.');
      return;
    }
    const k = LLM.choice(), M = LLM.models()[k];
    box.className = 'cb-status cb-st-' + LLM.state;
    if (LLM.state === 'loading') {
      const pct = Math.round(LLM.progress * 100);
      box.innerHTML = row('load', 'Preparo l’assistente · ' + M.label + ' · ' + pct + '%', 'Intanto puoi già fare domande: rispondo con la ricerca.') +
        '<div class="cb-bar" role="progressbar" aria-label="Download del modello" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + pct + '"><i style="width:' + pct + '%"></i></div>' +
        '<small class="cb-st-file">' + esc(String(LLM.text || '').slice(0, 140)) + '</small>';
      return;
    }
    let details = '';
    if (LLM.state === 'ready') {
      box.innerHTML = row('on', 'Assistente AI attivo', 'Modello ' + M.label + ' sul tuo dispositivo, con il ' + ENGINE[kind] + '.', more);
      details = '<p>Interpreta la domanda (anche le domande di seguito, come «e per il Libero?») e risponde usando solo i riferimenti trovati, citandone i codici.' +
        (kind === 'cpu' ? ' Con il processore la prima parola può arrivare dopo qualche secondo, poi la risposta si scrive man mano.' : '') + '</p>' + modelPicker(k);
    } else {
      // Whether the model is already downloaded: checked in the background (WebLLM comes from the CDN)
      const cached = CACHED[kind + k];
      if (cached === undefined) {
        CACHED[kind + k] = null;
        Promise.race([LLM.cached(k), new Promise(r => setTimeout(() => r(false), 8000))]).catch(() => false)
          .then(v => { CACHED[kind + k] = !!v; if (LLM.state === 'off' || LLM.state === 'error') paintStatus(); });
      }
      const err = LLM.state === 'error';
      box.innerHTML = row(err ? 'err' : 'off', err ? 'L’assistente non si è avviato' : 'Modalità ricerca',
        err ? esc(String(LLM.text || '').slice(0, 200)) : 'Rispondo con i passaggi dei riferimenti. L’assistente AI scrive la risposta con parole sue' + (cached === true ? ' (il modello è già scaricato).' : cached === false ? ' (al primo avvio scarica ' + M.size + ').' : '.'),
        '<button type="button" class="btn btn-primary btn-sm" id="cb-enable">' + ICON.spark + (err ? 'Riprova' : 'Attiva l’assistente') + '</button>' + more);
      details = '<p>L’assistente è un piccolo modello linguistico che gira <strong>sul tuo dispositivo</strong>, con il ' + ENGINE[kind] + ': legge solo i riferimenti trovati e scrive la risposta citandoli. Le domande non lasciano mai il dispositivo.</p>' +
        '<p>' + (cached ? 'Il modello ' + M.label + ' è già scaricato.' : 'Il modello si scarica una volta sola (' + M.label + ': ' + M.size + ') e resta nel browser: le volte successive si avvia in pochi secondi.') + '</p>' +
        (kind === 'cpu' && LLM.gpuNote ? '<p class="cb-st-note">' + esc(LLM.gpuNote) + ': uso il motore compatibile, più lento. Con Chrome o Edge le risposte arrivano prima.</p>' : '') +
        modelPicker(k);
      $('cb-enable').onclick = () => LLM.load(LLM.choice(), onLlmProgress);
    }
    box.insertAdjacentHTML('beforeend', '<div class="cb-st-more" id="cb-st-more"' + (statusOpen ? '' : ' hidden') + '>' + details + '</div>');
    $('cb-more').onclick = () => { statusOpen = !statusOpen; paintStatus(); };
    box.querySelectorAll('[data-model]').forEach(b => b.onclick = () => {
      const was = LLM.choice();
      LLM.setChoice(b.dataset.model);
      if (LLM.state === 'ready' && b.dataset.model !== was) LLM.load(b.dataset.model, onLlmProgress);
      else paintStatus();
    });
  }
  const CACHED = {}; // engine + model → downloaded already? (undefined: not checked, null: checking)
  let wasReady = false;
  function onLlmProgress() {
    paintStatus();
    const ready = LLM.state === 'ready';
    if (ready !== wasReady) {
      wasReady = ready;
      if (LLM.state !== 'loading') announce(ready ? 'Assistente attivo' : 'L’assistente non si è avviato');
      if (ready) { CACHED[LLM.kind + LLM.choice()] = true; statusOpen = false; paintStatus(); paint(); } // "Chiedi all’assistente" on the earlier answers
    }
  }

  // ---------------------------------------------------------------- mount

  function mount() {
    if (mounted) return;
    mounted = true;
    load();
    const form = $('cb-form');
    const input = $('cb-input');
    const fit = () => { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 160) + 'px'; };
    form.addEventListener('submit', e => {
      e.preventDefault();
      if (LLM.busy) { stop(); return; }
      if (ask(input.value)) { input.value = ''; fit(); setBusy(LLM.busy); }
    });
    input.addEventListener('keydown', e => {
      if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); if (!LLM.busy) form.requestSubmit(); }
      else if (e.key === 'Escape' && LLM.busy) { e.preventDefault(); stop(); }
    });
    input.addEventListener('input', () => { fit(); setBusy(LLM.busy); });
    $('cb-clear').addEventListener('click', () => { stop(); history = []; save(); paint(); input.focus(); announce('Nuova conversazione'); });
    $('cb-log').addEventListener('click', e => {
      const b = e.target.closest('[data-ask],[data-ref],[data-act]');
      if (!b) return;
      const node = b.closest('.cb-msg');
      const i = node ? +node.dataset.i : -1;
      const m = history[i];
      if (b.dataset.ask) { if (!ask(b.dataset.ask)) input.focus(); }
      else if (b.dataset.ref) openRef(b.dataset.ref, m && m.q);
      else if (m && b.dataset.act === 'copy') copy(m, b);
      else if (m && (b.dataset.act === 'redo' || b.dataset.act === 'explain') && !LLM.busy && LLM.state === 'ready') runAi(i);
    });
    paint();
    setBusy(false);
    paintStatus();
  }

  window.Chatbot = {
    open() {
      mount();
      setTimeout(() => { const i = $('cb-input'); if (i && matchMedia('(pointer: fine)').matches) i.focus({ preventScroll: true }); }, 50);
    },
    answer, retrieveFor, buildMessages,
  };
})();
