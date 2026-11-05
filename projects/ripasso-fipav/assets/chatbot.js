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
    invasione: 'linea centrale rete penetrazione interferenza', piede: 'piede linea', calpestare: 'toccare linea piede campo', pestare: 'toccare linea piede campo',
    doppia: 'doppio tocco', doppio: 'doppio tocco', trattenuta: 'palla trattenuta', accompagnata: 'palla trattenuta', portata: 'palla trattenuta',
    campo: 'terreno gioco campo', misure: 'dimensioni', grande: 'dimensioni', lungo: 'lunghezza', largo: 'larghezza', alta: 'altezza', alto: 'altezza',
    pallone: 'palla', fischietto: 'fischio', fischiare: 'fischio', arbitrare: 'arbitro', primo: '1 arbitro', secondo: '2 arbitro',
    rotazione: 'rotazione posizione', ruotare: 'rotazione', posizioni: 'posizione', fallo: 'fallo', falli: 'fallo',
    vince: 'vincere', vinto: 'vincere', vincere: 'vincere set partita', punteggio: 'punto punteggio', punti: 'punto',
    libero: 'libero liberoplayer', liberi: 'libero liberoplayer', capitano: 'capitano squadra capitano gioco responsabili',
    allenatore: 'allenatore', coach: 'allenatore', panchina: 'panchina',
    ritardo: 'ritardo', perdere: 'perdere tempo ritardo', protesta: 'condotta scorretta', insulto: 'condotta offensiva', offendere: 'condotta offensiva',
    muro: 'muro', murare: 'muro', antenna: 'antenna', asticella: 'antenna', rete: 'rete', nastro: 'rete',
    infortunio: 'infortunio', infortunato: 'infortunio', sangue: 'infortunio', sudore: 'interruzione',
    divisa: 'uniforme', maglia: 'uniforme maglia', numero: 'numero', scarpe: 'uniforme',
    tocco: 'tocco contatto', tocca: 'tocco contatto', toccare: 'tocco contatto', toccato: 'tocco contatto', contatto: 'contatto tocco',
    'tocca la rete': 'contatto rete fallo', 'toccare la rete': 'contatto rete fallo', 'tocco la rete': 'contatto rete fallo', 'tocco di rete': 'contatto rete fallo',
    'fallo di rete': 'contatto rete interferenza',
    'fare il capitano': 'capitano squadra capitano gioco responsabili', 'essere capitano': 'capitano squadra capitano gioco responsabili',
    'puo essere capitano': 'capitano squadra capitano gioco responsabili', 'puo fare il capitano': 'capitano squadra capitano gioco responsabili',
    'pestare la linea': 'toccare linea fondo campo servizio', 'linea di fondo': 'linea fondo campo servizio',
    'palla lontana': 'azione giocare palla interferenza', 'fuori azione': 'azione giocare palla interferenza',
    'sotto la rete': 'penetrazione spazio avversario linea centrale', 'sopra la rete': 'spazio aereo piano verticale',
    salto: 'stacco ricaduta',
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
    if (/(^| )liber[oi]( |$)/.test(n)) { add('liberoplayer', 1.3); out.set(stem('libero'), 0.5); }
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
    const coreWords = words(q).filter(w => w !== 'liberoplayer');
    const coreStems = new Set(coreWords.map(stem));
    const seq = terms(q);
    const qBigrams = [];
    for (let i = 0; i < seq.length - 1; i++) qBigrams.push(seq[i] + ' ' + seq[i + 1]);
    const k1 = 1.4, b = 0.7;
    const scored = [];
    for (const d of DOCS) {
      let s = 0, hit = 0, coreHit = 0;
      qt.forEach((w, t) => {
        const f = d.tf.get(t);
        if (!f) return;
        hit++;
        s += w * idf(t) * (f * (k1 + 1)) / (f + k1 * (1 - b + b * d.len / AVG));
      });
      if (!s) continue;
      for (const cs of coreStems) {
        if (d.tf.has(cs) || (cs === stem('libero') && d.tf.has('liberoplayer'))) coreHit++;
      }
      for (const bg of qBigrams) if (d.bigrams.has(bg)) s += 1.5;
      s *= 0.6 + 0.4 * (hit / Math.max(1, qt.size)); // coverage of the question
      if (coreStems.size >= 2) {
        const coreRatio = coreHit / coreStems.size;
        s *= 0.6 + 0.4 * coreRatio;
        if (coreRatio === 1) s *= 1.25; // boost references containing all core terms
      }
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
    // Keep the references reasonably close to the best one (at most 4)
    const chosen = list.filter(x => x.score >= top * 0.45).slice(0, 4);
    if (quiz) for (const rc of quiz.q['question-ref'] || []) {
      const r = BY_CODE[rc.toUpperCase()];
      if (r && !chosen.some(c => c.ref === r)) chosen.unshift({ ref: r, score: top });
    }
    const refs = chosen.slice(0, 4).map((c, i) => ({ ref: c.ref, score: c.score, sentences: bestSentences(c.ref, qt, i === 0 ? 3 : 2) }));
    const confidence = quiz ? 'high' : top > 9 ? 'high' : top > 4.5 ? 'medium' : 'low';
    return { kind: 'search', refs, quiz, confidence, related: list.slice(4, 8).map(x => x.ref) };
  }

  // ---------------------------------------------------------------- B. local language model (WebLLM)

  // Two engines, chosen automatically:
  //  - "gpu": WebLLM on WebGPU (fast; Chrome/Edge, newer Firefox/Safari, Android);
  //  - "cpu": Transformers.js (supports WebGPU acceleration on Firefox where available, and falls back to WebAssembly CPU).
  const WEBLLM_URL = 'https://cdn.jsdelivr.net/npm/@mlc-ai/web-llm@0.2.85/lib/index.js';
  // dist/transformers.min.js is the self-contained build (the .web build needs a bundler for onnxruntime)
  const TFJS_URL = 'https://cdn.jsdelivr.net/npm/@huggingface/transformers@3.7.5/dist/transformers.min.js';
  const MODELS = {
    gpu: {
      light: { label: 'Leggero', size: '≈ 0,4 GB', id: 'Qwen2.5-0.5B-Instruct', note: 'molto veloce, risposte minime', power: 1 },
      standard: { label: 'Standard', size: '≈ 1 GB', id: 'Qwen2.5-1.5B-Instruct', note: 'consigliato, veloce e bilanciato', power: 2 },
      quality: { label: 'Qualità', size: '≈ 2 GB', id: 'Qwen2.5-3B-Instruct', note: 'risposte più articolate e precise', power: 3 },
      ultra: { label: 'Massima precisione', size: '≈ 4,5 GB', id: 'Qwen2.5-7B-Instruct', note: 'qualità superiore, per computer potenti', power: 4 },
    },
    cpu: {
      light: { label: 'Leggero', size: '≈ 0,5 GB', id: 'onnx-community/Qwen2.5-0.5B-Instruct', note: 'molto rapido, per risposte veloci', power: 1 },
      standard: { label: 'Standard', size: '≈ 1,2 GB', id: 'onnx-community/Qwen2.5-1.5B-Instruct', note: 'consigliato, risposte affidabili e precise', power: 2 },
      quality: { label: 'Qualità', size: '≈ 3,2 GB', id: 'onnx-community/Llama-3.2-3B-Instruct-ONNX', note: 'risposte più articolate (consigliata GPU)', power: 3 },
    },
  };
  const CACHED = {}; // engine + modelKey → downloaded already? (undefined: not checked, null: checking, true/false)
  const storageInfo = { persistent: false, usage: 0, checked: false };

  async function hasAnyModelCache() {
    if (typeof caches !== 'undefined') {
      try {
        const keys = await caches.keys();
        if (keys.some(k => k === 'transformers-cache' || k.includes('webllm') || k.includes('mlc'))) return true;
      } catch (e) { /* ignore */ }
    }
    return false;
  }

  async function updateStorageInfo(requestPersist = false) {
    if (typeof navigator !== 'undefined' && navigator.storage) {
      try {
        if (navigator.storage.persisted) {
          storageInfo.persistent = await navigator.storage.persisted();
        }
        if (requestPersist && !storageInfo.persistent && navigator.storage.persist) {
          storageInfo.persistent = await navigator.storage.persist();
        }
        if (navigator.storage.estimate) {
          const est = await navigator.storage.estimate();
          const hasCache = await hasAnyModelCache();
          // If no AI model caches exist in CacheStorage, any large reported usage (> 30 MB)
          // is stale quota cache from the browser. In that case, model usage is 0.
          if (!hasCache && (est.usage || 0) > 30 * 1024 * 1024) {
            storageInfo.usage = 0;
          } else {
            storageInfo.usage = est.usage || 0;
          }
        }
      } catch (e) { /* ignore */ }
    }
    storageInfo.checked = true;
    return storageInfo;
  }

  let webGpuChecked = false;
  let webGpuActive = false;

  /** Checks if WebGPU is actually active and able to instantiate a device in this browser. */
  async function checkWebGpuActive() {
    if (webGpuChecked) return webGpuActive;
    if (typeof navigator === 'undefined' || !('gpu' in navigator) || !window.isSecureContext) {
      webGpuChecked = true;
      webGpuActive = false;
      return false;
    }
    try {
      const adapter = await Promise.race([
        navigator.gpu.requestAdapter(),
        new Promise(r => setTimeout(() => r(null), 3000))
      ]);
      if (!adapter) {
        webGpuChecked = true;
        webGpuActive = false;
        return false;
      }
      const device = await Promise.race([
        adapter.requestDevice(),
        new Promise(r => setTimeout(() => r(null), 2500))
      ]);
      if (device) {
        webGpuActive = true;
        try { device.destroy(); } catch (e) { /* ignore */ }
      } else {
        webGpuActive = false;
      }
    } catch (e) {
      webGpuActive = false;
    }
    webGpuChecked = true;
    return webGpuActive;
  }

  function getCachedModels(kind) {
    const models = MODELS[kind] || {};
    return Object.keys(models).filter(k => CACHED[kind + k] === true);
  }

  function bestCachedModel(kind) {
    const cached = getCachedModels(kind);
    if (!cached.length) return null;
    const models = MODELS[kind] || {};
    return cached.sort((a, b) => (models[b]?.power || 0) - (models[a]?.power || 0))[0];
  }

  const LLM = {
    kind: null, device: 'wasm', hasGpuAdapter: false, lib: null, libPromise: null, engine: null, gen: null, stopper: null, modelId: null, state: 'off', progress: 0, text: '', f16: true, busy: false, files: {},
    /** Which engine this browser can use: 'gpu', 'cpu' or '' (none), with the reason when WebGPU is not usable. */
    async detect() {
      if (this.kind !== null) return this.kind;
      this.gpuNote = '';
      this.hasGpuAdapter = false;
      this.device = 'wasm';
      const isFirefox = typeof navigator !== 'undefined' && /firefox|fxios/i.test(navigator.userAgent);
      if (isFirefox) {
        // Firefox WebGPU implementation (wgpu) is still experimental and currently panics on in-browser LLM shaders (error 3587054648).
        // WebAssembly on CPU is the stable, working execution path on Firefox.
        this.gpuNote = 'Firefox: in uso il motore WebAssembly su processore (CPU)';
        this.device = 'wasm';
        return (this.kind = typeof WebAssembly === 'object' ? 'cpu' : '');
      }
      const hasGpu = await checkWebGpuActive();
      if (hasGpu) {
        this.hasGpuAdapter = true;
        try {
          const ad = await navigator.gpu.requestAdapter();
          if (ad) {
            const maxStorage = (ad.limits && ad.limits.maxStorageBuffersPerShaderStage) || 0;
            // WebLLM needs at least 10 storage buffers per shader stage (Chrome, Edge)
            if (maxStorage >= 10) {
              this.f16 = ad.features.has('shader-f16');
              this.device = 'webgpu';
              return (this.kind = 'gpu');
            }
          }
        } catch (e) { /* fallback */ }
      } else {
        this.gpuNote = 'Questo browser non ha WebGPU attivo';
      }
      return (this.kind = typeof WebAssembly === 'object' ? 'cpu' : '');
    },
    models() { return MODELS[this.kind] || MODELS.cpu; },
    choice() {
      let k = 'standard';
      try {
        k = localStorage.getItem('fischietto-chatbot-model-' + this.kind);
        // If not set, or if it defaulted to light on cpu previously without explicit user choice, upgrade to standard
        if (!k || (this.kind === 'cpu' && k === 'light' && !localStorage.getItem('fischietto-chatbot-model-explicit'))) {
          k = 'standard';
        }
      } catch (e) { /* private mode */ }
      return this.models()[k] ? k : (this.models().standard ? 'standard' : Object.keys(this.models())[0]);
    },
    setChoice(k) {
      try {
        localStorage.setItem('fischietto-chatbot-model-' + this.kind, k);
        localStorage.setItem('fischietto-chatbot-model-explicit', '1');
      } catch (e) { /* private mode */ }
    },
    async loadLib() {
      if (!this.libPromise) this.libPromise = import(WEBLLM_URL).then(m => (this.lib = m));
      return this.libPromise;
    },
    async gpuId(k) {
      await this.loadLib();
      return MODELS.gpu[k].id + (this.f16 ? '-q4f16_1-MLC' : '-q4f32_1-MLC');
    },
    async cached(k, preloadedKeys) {
      try {
        if (this.kind === 'gpu') {
          // Fast check in CacheStorage if possible before loading entire WebLLM library
          if (typeof caches !== 'undefined') {
            try {
              const c = await caches.open('webllm/model');
              const keys = await c.keys();
              const baseId = MODELS.gpu[k].id;
              if (keys.some(r => r.url.includes(baseId))) return true;
            } catch (e) { /* fallback to WebLLM check */ }
          }
          const id = await this.gpuId(k);
          if (this.lib && await this.lib.hasModelInCache(id)) return true;
          const alt = MODELS.gpu[k].id + (this.f16 ? '-q4f32_1-MLC' : '-q4f16_1-MLC');
          if (this.lib && await this.lib.hasModelInCache(alt)) return true;
          return false;
        }
        if (typeof caches === 'undefined') return false;
        let keys = preloadedKeys;
        if (!keys) {
          const c = await caches.open('transformers-cache');
          keys = await c.keys();
        }
        return keys.some(r => r.url.includes(MODELS.cpu[k].id + '/') && /\.onnx/.test(r.url));
      } catch (e) { return false; }
    },
    async load(k, onProgress) {
      updateStorageInfo(true);
      this.state = 'loading'; this.progress = 0; this.text = 'Preparazione…'; this.files = {}; onProgress();
      try {
        if (this.kind === 'gpu') {
          try {
            const id = await this.gpuId(k);
            if (this.engine) { try { await this.engine.unload(); } catch (e) { /* ignore */ } }
            this.engine = await this.lib.CreateMLCEngine(id, {
              initProgressCallback: r => { this.progress = r.progress || 0; this.text = r.text || ''; onProgress(); }
            });
            this.modelId = id;
            this.device = 'webgpu';
          } catch (gpuInitErr) {
            console.warn('Avvio WebLLM GPU non riuscito, passaggio automatico a WebAssembly CPU:', gpuInitErr);
            this.engine = null;
            this.kind = 'cpu';
            this.device = 'wasm';
            return this.load(this.choice(), onProgress);
          }
        } else {
          const T = this.tf || (this.tf = await import(TFJS_URL));
          T.env.allowLocalModels = false;
          if (this.gen && this.gen.dispose) { try { await this.gen.dispose(); } catch (e) { /* ignore */ } }
          let last = 0;
          const progressCb = p => {
            if (p.status === 'progress' && p.file) this.files[p.file] = [p.loaded || 0, p.total || 0];
            const v = Object.values(this.files), tot = v.reduce((a, x) => a + x[1], 0), done = v.reduce((a, x) => a + x[0], 0);
            this.progress = tot ? done / tot : 0;
            this.text = p.file ? 'Scarico ' + p.file + (tot ? ' — ' + (done / 1048576).toFixed(0) + ' / ' + (tot / 1048576).toFixed(0) + ' MB' : '') : 'Avvio del modello su processore (WebAssembly)…';
            if (Date.now() - last > 150) { last = Date.now(); onProgress(); }
          };

          // WebAssembly configuration:
          // Enable SIMD acceleration.
          // Multi-threading: SharedArrayBuffer requires a cross-origin isolated context (COOP/COEP headers).
          // Without crossOriginIsolated, setting numThreads > 1 triggers an internal WASM panic (error 3587054648).
          try {
            if (T.env.backends?.onnx?.wasm) {
              T.env.backends.onnx.wasm.simd = true;
              const canUseThreads = typeof SharedArrayBuffer !== 'undefined' && window.crossOriginIsolated === true;
              T.env.backends.onnx.wasm.numThreads = canUseThreads
                ? Math.min(4, Math.max(1, (navigator.hardwareConcurrency || 2) - 1))
                : 1;
            }
          } catch (e) { /* ignore */ }

          this.device = 'wasm';
          this.text = 'Avvio del modello su processore (WebAssembly)…';
          onProgress();

          this.gen = await T.pipeline('text-generation', MODELS.cpu[k].id, {
            dtype: 'q4',
            device: 'wasm',
            progress_callback: progressCb,
          });
          this.modelId = MODELS.cpu[k].id;
        }
        this.state = 'ready';
      } catch (e) {
        this.engine = null; this.gen = null; this.state = 'error';
        const raw = (e && e.message) || String(e || '');
        if (/^\d+$/.test(raw)) {
          this.text = 'Inizializzazione del modello non riuscita (errore interno WebAssembly ' + raw + '). Prova a usare il modello Leggero o la modalità ricerca.';
        } else {
          this.text = raw;
        }
      }
      onProgress();
    },
    async generate(messages, onToken) {
      this.busy = true;
      try {
        let out = '';
        if (this.kind === 'gpu') {
          const chunks = await this.engine.chat.completions.create({
            messages,
            stream: true,
            temperature: 0.1,
            top_p: 0.9,
            max_tokens: 450,
          });
          for await (const c of chunks) {
            const d = c.choices && c.choices[0] && c.choices[0].delta && c.choices[0].delta.content;
            if (d) { out += d; onToken(out); }
          }
        } else {
          const T = this.tf;
          this.stopper = new T.InterruptableStoppingCriteria();
          const streamer = new T.TextStreamer(this.gen.tokenizer, {
            skip_prompt: true,
            skip_special_tokens: true,
            callback_function: txt => { out += txt; onToken(out); }
          });
          const maxTokens = (this.kind === 'cpu' && this.device === 'wasm') ? 250 : 380;
          const res = await this.gen(messages, {
            max_new_tokens: maxTokens,
            do_sample: false,
            repetition_penalty: 1.1,
            streamer,
            stopping_criteria: this.stopper
          });
          if (!out && res && res[0]) {
            const genText = res[0].generated_text;
            if (typeof genText === 'string') out = genText;
            else if (Array.isArray(genText) && genText.length) {
              const last = genText[genText.length - 1];
              if (last && last.content) out = last.content;
            }
          }
        }
        return out;
      } finally { this.busy = false; }
    },
    stop() {
      if (!this.busy) return;
      if (this.kind === 'gpu' && this.engine) this.engine.interruptGenerate();
      else if (this.stopper) this.stopper.interrupt();
    },
    async unload() {
      if (this.busy) this.stop();
      if (this.kind === 'gpu' && this.engine) {
        try { await this.engine.unload(); } catch (e) { /* ignore */ }
        this.engine = null;
      } else if (this.gen) {
        if (this.gen.dispose) { try { await this.gen.dispose(); } catch (e) { /* ignore */ } }
        this.gen = null;
      }
      this.modelId = null;
      this.state = 'off';
      this.text = '';
      this.progress = 0;
      this.busy = false;
    },
  };

  const SYSTEM_PROMPT =
    'Sei l’assistente virtuale di Fischietto, il portale per arbitri di pallavolo FIPAV.\n\n' +
    'LINGUA E INTERPRETAZIONE (VINCOLO CATEGORICO):\n' +
    '- Tutte le domande dell’arbitro e tutte le tue risposte sono e saranno SEMPRE ed ESCLUSIVAMENTE in lingua ITALIANA.\n' +
    '- Il tuo ambito di interpretazione concettuale, linguistica e terminologica è limitato rigorosamente alla lingua italiana e al regolamento tecnico di pallavolo FIPAV.\n' +
    '- Non rispondere mai in altre lingue, non tradurre in altre lingue e non interpretare i termini secondo significati estranei alla pallavolo.\n\n' +
    'COMPRENSIONE DELLA DOMANDA E APPLICAZIONE DELLE REGOLE:\n' +
    '- L’arbitro formula spesso quesiti usando espressioni colloquiali o descrivendo casistiche pratiche di gara (es. «toccare la rete a palla lontana», «il libero può fare il capitano?», «il battitore può pestare la linea?»).\n' +
    '- Il tuo compito è COMPRENDERE il senso pratico del quesito, ricondurlo alle regole ufficiali di pallavolo presenti nei documenti forniti e dedurre la decisione corretta (se l’azione è regolare o fallosa, cosa stabilisce il regolamento ed eventuali eccezioni).\n' +
    '- Collega sempre il linguaggio pratico della domanda ai concetti regolamentari presenti nei riferimenti.\n\n' +
    'REGOLE DI RISPOSTA:\n' +
    '1. Rispondi in modo chiaro, diretto, sicuro e autorevole (circa 3-5 frasi in lingua italiana).\n' +
    '2. Basa la spiegazione sui riferimenti regolamentari e sui quiz ufficiali forniti nel messaggio, applicandoli al caso specifico chiesto dall’arbitro.\n' +
    '3. Dopo ogni affermazione, regola o decisione, cita SEMPRE il codice del riferimento tra parentesi quadre, per esempio [RG-5], [RG-11.3.1] o [CAS-19.1].\n' +
    '4. Non inventare mai regole, numeri o codici inesistenti.\n' +
    '5. Rispondi con «Non trovo la risposta nei riferimenti di Fischietto.» SOLO ED ESCLUSIVAMENTE se i testi forniti non trattano minimamente il tema del quesito e non contengono regole applicabili.';

  /** Messages for the model: system prompt, the last exchange (for follow-up questions), the references found. */
  function buildMessages(question, found, prev = []) {
    const isCpuWasm = LLM.kind === 'cpu' && LLM.device === 'wasm';
    let ctx = '', budget = isCpuWasm ? 2400 : 7000;
    if (found.quiz) {
      ctx += 'ESEMPIO DA CASISTICA / QUIZ UFFICIALE FIPAV:\n' +
        'Quesito: ' + found.quiz.q['question-text'] + '\n' +
        'Soluzione ufficiale: ' + found.quiz.answer + '\n\n';
    }
    for (const r of found.refs) {
      const full = (r.ref['ref-full-text'] || '').trim();
      const short = (r.ref['ref-text'] || '').trim();
      const text = full ? (short && !full.includes(short) ? short + '\n' + full : full) : short;
      const clean = text.replace(/\s+/g, ' ').trim();
      const maxLen = isCpuWasm ? 650 : 1500;
      const piece = 'RIFERIMENTO [' + r.ref['ref-code'] + '] ' + r.ref['ref-title'] + ' (' + r.ref['ref-from'] + '):\n' +
        (clean.length > maxLen ? clean.slice(0, maxLen) + '…' : clean) + '\n\n';
      if (ctx.length + piece.length > budget) break;
      ctx += piece;
    }
    const msgs = [{ role: 'system', content: SYSTEM_PROMPT }];
    // Include at most 1 previous exchange for follow-up context without clutter
    for (const p of (prev || []).slice(-1)) {
      msgs.push({ role: 'user', content: 'Domanda dell’arbitro: ' + p.q });
      msgs.push({ role: 'assistant', content: p.a });
    }
    const userPrompt =
      'DOMANDA DELL’ARBITRO (in lingua italiana):\n«' + question + '»\n\n' +
      'DOCUMENTI E RIFERIMENTI REGOLAMENTARI FIPAV DA CONSULTARE:\n' +
      (ctx.trim() || 'Nessun riferimento disponibile.') + '\n\n' +
      'ISTRUZIONI:\n' +
      '- Rispondi rigorosamente in lingua ITALIANA alla «DOMANDA DELL’ARBITRO» sopra riportata.\n' +
      '- Interpreta il quesito pratico e applica le regole e i principi presenti nei riferimenti sopra riportati.\n' +
      '- Cita sempre il codice tra parentesi quadre dopo ogni regola o decisione (es. [RG-5]).\n' +
      '- Mantieni la risposta chiara, corretta e concisa (3-5 frasi).\n' +
      '- Se e solo se i documenti forniti non contengono alcuna regola o principio inerente alla domanda, rispondi testualmente: «Non trovo la risposta nei riferimenti di Fischietto.»';

    msgs.push({ role: 'user', content: userPrompt });
    return msgs;
  }

  /** Retrieval used by the assistant: more references, and the previous question for short follow-ups. */
  function retrieveFor(question, prev = []) {
    let q = question;
    if (prev && prev.length && words(question).length < 6) q = prev[prev.length - 1].q + ' ' + question; // "e per il libero?"
    if (!DOCS.length) build();
    const { list, qt } = rank(q);
    const quiz = similarQuestion(question);
    const top = list[0] ? list[0].score : 0;
    const isCpuWasm = LLM.kind === 'cpu' && LLM.device === 'wasm';
    const maxRefs = isCpuWasm ? 3 : 5;
    const chosen = list.filter(x => x.score >= top * 0.32).slice(0, maxRefs);
    if (quiz) for (const rc of quiz.q['question-ref'] || []) {
      const r = BY_CODE[rc.toUpperCase()];
      if (r && !chosen.some(c => c.ref === r)) chosen.unshift({ ref: r, score: top });
    }
    return { refs: chosen.slice(0, maxRefs), quiz, qt };
  }

  // ---------------------------------------------------------------- UI

  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const $ = id => document.getElementById(id);
  const CITE_RE = /\[((?:RG|CAS|IT|NF|VAD)[-\s]?[0-9][0-9A-Za-z.]*)\]/g;
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
    trash: svg('<path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>', 14),
    user: svg('<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>', 14),
    external: svg('<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>', 12),
    shield: svg('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', 12),
  };

  /** Highlights the words of the question in a sentence. */
  function highlight(text, qt) {
    return esc(text).replace(/[A-Za-zÀ-ÿ0-9]+/g, w => (qt && qt.has(stem(norm(w))) && w.length > 2 ? '<mark>' + w + '</mark>' : w));
  }
  /** Model text → HTML: [RG-15.4] become buttons that open the reference, **bold**, line breaks. */
  function formatAnswer(text) {
    return esc(text)
      .replace(/([A-Za-z0-9À-ÿ])\[/g, '$1 [')
      .replace(/\]([A-Za-z0-9À-ÿ])/g, '] $1')
      .replace(/\]\[/g, '] [')
      .replace(/\s+([,.;:!?])/g, '$1')
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(CITE_RE, (m, raw) => {
        const clean = raw.replace(/\.+$/, '').replace(/\s+/, '-').toUpperCase();
        const r = BY_CODE[clean];
        const srcCls = r ? (SRC_CLASS[r['ref-from']] || 'rg') : 'bad';
        return r
          ? '<button type="button" class="cb-cite cb-cite-' + srcCls + '" data-ref="' + clean + '" title="Leggi il riferimento ' + clean + ' (' + esc(r['ref-from']) + ' · ' + esc(r['ref-title']) + ')">' + clean + '</button>'
          : '<span class="cb-cite bad" title="Codice non trovato nei riferimenti">' + esc(raw) + '</span>';
      })
      .replace(/\s+([,.;:!?])/g, '$1')
      .replace(/\n{2,}/g, '</p><p>').replace(/\n/g, '<br>');
  }
  const SRC_CLASS = { 'Regole di gioco': 'rg', 'Casistica': 'cas', 'Indicazioni tecniche': 'it', 'Normative federali': 'nf', 'Vademecum': 'vad' };
  const EXAMPLE_GROUPS = [
    {
      category: 'Regole & Misure',
      icon: svg('<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20"/>', 14),
      items: [
        'Quanto è alta la rete per le donne?',
        'Quante sostituzioni può fare una squadra in un set?',
      ],
    },
    {
      category: 'Libero & Ruoli',
      icon: svg('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', 14),
      items: [
        'Il libero può fare il capitano?',
        'Il libero può alzare in palleggio davanti alla linea dei 3 metri?',
      ],
    },
    {
      category: 'Falli & Rete',
      icon: svg('<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>', 14),
      items: [
        'Se un giocatore tocca la rete mentre la palla è dall’altra parte, è fallo?',
        'Il battitore può pestare la linea di fondo?',
      ],
    },
    {
      category: 'Tempi & Protocolli',
      icon: svg('<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>', 14),
      items: [
        'Quanto dura un tempo di riposo?',
        'Cosa succede se la palla tocca l’antenna?',
      ],
    },
  ];
  const EXAMPLES = EXAMPLE_GROUPS.flatMap(g => g.items);
  const ENGINE = { gpu: 'scheda grafica (WebGPU)', cpu: 'processore' };
  const STORE = 'fischietto-chatbot';
  let history = [];   // { role: 'user', text } | { role: 'bot', q, mode: 'search' | 'ai', modelKey, modelLabel, modelId, engineKind, a, refs: [codes], done, error, stopped }
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
    const srcCls = SRC_CLASS[ref['ref-from']] || 'rg';
    return '<button type="button" class="cb-chip cb-chip-ref' + (on ? ' on' : '') + '" data-ref="' + esc(ref['ref-code']) + '" title="' + esc(ref['ref-code'] + ' · ' + ref['ref-title']) + '">' +
      '<span class="cb-src cb-src-' + srcCls + '">' + esc(ref['ref-code']) + '</span>' +
      '<span class="cb-chip-title">' + esc(ref['ref-title']) + '</span>' +
      '<span class="cb-chip-icon">' + ICON.external + '</span>' +
      '</button>';
  }

  function renderSearch(m) {
    const a = found(m);
    if (a.kind === 'none') {
      return '<div class="cb-empty-search">' +
        '<p class="cb-empty-title">Nessun passaggio pertinente trovato</p>' +
        '<p class="cb-hint">Prova a formulare la domanda con parole diverse (es. usando i termini del regolamento), oppure cerca direttamente un codice come <button type="button" class="cb-cite cb-cite-rg" data-ask="RG-15.4">RG-15.4</button>.</p>' +
        '</div>';
    }
    const qt = queryTerms(m.q);
    let html = '';
    if (a.quiz) html += '<div class="cb-quiz"><div class="cb-quiz-head">' + ICON.check + '<span class="cb-label">Dal quiz ufficiale FIPAV</span></div><p class="cb-qtext">' + esc(a.quiz.q['question-text']) + '</p><div class="cb-qans"><strong>Soluzione:</strong> <span>' + esc(a.quiz.answer) + '</span></div></div>';
    a.refs.forEach((r, i) => {
      if (i === 1) html += '<div class="cb-divider"><span>Altri riferimenti pertinenti</span></div>';
      html += refBlock(r.ref, r.sentences, qt, i === 0);
    });
    if (a.related && a.related.length) html += '<div class="cb-related"><span class="cb-label">Vedi anche nel regolamento</span>' + a.related.map(r => refChip(r)).join('') + '</div>';
    return html;
  }

  function renderAi(m) {
    const cited = new Set((m.a.match(CITE_RE) || []).map(x => x.slice(1, -1).toUpperCase()));
    const used = (m.refs || []).map(c => BY_CODE[c]).filter(Boolean);
    let html;
    if (m.a) html = '<div class="cb-ai"><p>' + formatAnswer(m.a) + (m.done ? '' : '<span class="cb-caret"></span>') + '</p></div>';
    else if (!m.done) html = '<div class="cb-thinking"><span class="cb-typing"><i></i><i></i><i></i></span><span>Consulto ' + (used.length === 1 ? 'il riferimento pertinente' : 'i ' + used.length + ' riferimenti pertinenti') + (m.engineKind === 'cpu' || LLM.kind === 'cpu' ? ' (elaborazione su processore)…' : '…') + '</span></div>';
    else html = '';
    if (m.stopped) html += '<p class="cb-note">Risposta interrotta dall’utente.</p>';
    if (m.error) html += '<div class="cb-error">' + svg('<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>', 16) + '<span>L’assistente si è fermato per un errore: ' + esc(m.error) + '</span></div>';
    if (m.done && used.length) {
      html += '<details class="cb-sources"' + (cited.size ? '' : ' open') + '><summary>' + ICON.chevron + '<span>Riferimenti normativi consultati (' + used.length + ')</span></summary><div class="cb-related">' +
        used.map(r => refChip(r, cited.has(r['ref-code'].toUpperCase()))).join('') + '</div></details>';
    }
    return html;
  }

  function botMeta(m) {
    if (m.mode === 'ai') {
      const modelKey = m.modelKey || LLM.choice();
      const models = LLM.models();
      const M = models[modelKey] || {};
      const label = m.modelLabel || M.label || 'Standard';
      const techId = (m.modelId || M.id || '').replace('onnx-community/', '');
      const isGpu = m.deviceKind === 'webgpu' || m.engineKind === 'gpu' || (m.engineKind !== 'cpu' && LLM.kind === 'gpu') || (m.engineKind === 'cpu' && LLM.device === 'webgpu');
      const engineText = isGpu ? 'GPU' : 'CPU';
      return '<div class="cb-meta-pills">' +
        '<span class="cb-pill cb-pill-ai">' + ICON.spark + ' <span>Assistente AI</span></span>' +
        '<span class="cb-pill cb-pill-model" title="' + (techId ? esc(techId) + ' · ' : '') + engineText + '"><strong>' + esc(label) + '</strong>' + (techId ? ' <small class="cb-model-tech">(' + esc(techId) + ')</small>' : '') + '</span>' +
        '<span class="cb-pill cb-pill-local" title="Elaborato interamente sul tuo dispositivo con ' + engineText + '">' + ICON.shield + ' <span>In locale (' + engineText + ')</span></span>' +
        '</div>';
    }
    const a = found(m);
    const conf = a.kind === 'code' ? ['ok', 'Riferimento diretto'] : { high: ['ok', 'Risposta trovata'], medium: ['mid', 'Possibile risposta'], low: ['low', 'Incerta: verifica le fonti'] }[a.confidence];
    return '<div class="cb-meta-pills">' +
      '<span class="cb-pill cb-pill-search">' + ICON.search + ' <span>Ricerca nei riferimenti</span></span>' +
      '<span class="cb-pill cb-pill-model"><strong>BM25</strong></span>' +
      (conf ? '<span class="cb-pill cb-conf-' + conf[0] + '"><span>' + conf[1] + '</span></span>' : '') +
      '</div>';
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
    if (m.role === 'user') {
      return '<div class="cb-bubble">' + esc(m.text) + '</div>';
    }
    return '<div class="cb-bubble">' +
      botMeta(m) +
      '<div class="cb-body">' + (m.mode === 'ai' ? renderAi(m) : renderSearch(m)) + '</div>' +
      botActions(m) +
      '</div>';
  }
  const msgHtml = (m, i) => '<div class="cb-msg cb-' + m.role + '" data-i="' + i + '">' + msgInner(m) + '</div>';
  const msgNode = i => document.querySelector('#cb-log .cb-msg[data-i="' + i + '"]');

  function renderFirefoxGuide(context = 'welcome') {
    const isFirefox = typeof navigator !== 'undefined' && /firefox|fxios/i.test(navigator.userAgent);
    if (!isFirefox) return '';
    if (context === 'status') {
      return '<div class="cb-firefox-tip">' +
        '<strong>' + ICON.spark + ' Informazioni per Firefox:</strong>' +
        '<p>Su Firefox l’assistente AI viene eseguito in modo stabile e ottimizzato sul processore (WebAssembly con accelerazione SIMD). ' +
        'Per risposte rapide ti consigliamo il modello <strong>Leggero</strong> o <strong>Standard</strong>. ' +
        'Per l’accelerazione su scheda grafica (WebGPU), usa Google Chrome o Microsoft Edge.</p>' +
      '</div>';
    }
    return '';
  }

  function welcomeHtml() {
    const isAi = LLM.state === 'ready';
    const curModel = LLM.models()[LLM.choice()] || {};
    return '<div class="cb-welcome">' +
      '<div class="cb-welcome-hero">' +
      '<div class="cb-welcome-icon-wrap">' +
      '<span class="cb-welcome-icon">' + (isAi ? ICON.spark : ICON.search) + '</span>' +
      '</div>' +
      '<div class="cb-welcome-badge">' + (isAi ? 'Assistente AI attivo · ' + esc(curModel.label || 'Standard') : 'Modalità ricerca diretta') + '</div>' +
      '<h2>Come posso aiutarti sul regolamento?</h2>' +
      '<p class="cb-welcome-desc">Scrivi una domanda con parole tue o inserisci un codice regolamentare (es. <button type="button" class="cb-cite cb-cite-rg" data-ask="RG-15.4">RG-15.4</button>). ' +
      'Le risposte sono elaborate sul tuo dispositivo da <strong>' + REFS.length + ' riferimenti</strong> e <strong>' + QUESTIONS.length + ' quiz</strong> ufficiali FIPAV.</p>' +
      '</div>' +
      '<div id="cb-firefox-welcome-guide">' + renderFirefoxGuide('welcome') + '</div>' +
      '<div class="cb-welcome-pills">' +
      '<span class="cb-pill-item">' + ICON.shield + ' 100% in locale</span>' +
      '<span class="cb-pill-item">' + ICON.check + ' Nessun dato condiviso</span>' +
      '<span class="cb-pill-item">' + ICON.check + ' Fonti ufficiali FIPAV</span>' +
      '</div>' +
      '<div class="cb-welcome-cards">' +
      EXAMPLE_GROUPS.map(g =>
        '<div class="cb-example-card">' +
        '<div class="cb-example-card-head">' + g.icon + '<strong>' + esc(g.category) + '</strong></div>' +
        '<div class="cb-example-card-items">' +
        g.items.map(q => '<button type="button" class="cb-example-btn" data-ask="' + esc(q) + '"><span>' + esc(q) + '</span><span class="cb-example-arrow">→</span></button>').join('') +
        '</div>' +
        '</div>'
      ).join('') +
      '</div>' +
      '</div>';
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
    let header = '';
    if (m.mode === 'ai') {
      const label = m.modelLabel || (LLM.models()[m.modelKey || LLM.choice()] || {}).label || 'Standard';
      header = '[Fischietto AI · ' + label + ']\n';
      const used = (m.refs || []).map(c => BY_CODE[c]).filter(Boolean);
      return header + m.a.trim() + (used.length ? '\n\nRiferimenti consultati: ' + used.map(r => r['ref-code'] + ' (' + r['ref-title'] + ')').join(', ') : '');
    }
    header = '[Ricerca nei riferimenti · BM25]\n';
    const a = found(m);
    let t = header;
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
      const m = { role: 'bot', q: text, mode: 'search', engineName: 'Ricerca nei riferimenti' };
      history.push(m);
      appendMsg(history.length - 1);
      save(); reveal(ui);
      const a = found(m);
      announce(a.kind === 'none' ? 'Nessun riferimento pertinente' : 'Risposta trovata in ' + a.refs.map(r => r.ref['ref-code']).join(', '));
      return true;
    }
    const curKey = LLM.choice();
    const curM = LLM.models()[curKey] || {};
    const bi = history.push({
      role: 'bot',
      q: text,
      mode: 'ai',
      modelKey: curKey,
      modelLabel: curM.label || 'Standard',
      modelId: (LLM.modelId || curM.id || '').replace('onnx-community/', ''),
      engineKind: LLM.kind || 'gpu',
      deviceKind: LLM.device || (LLM.kind === 'gpu' ? 'webgpu' : 'wasm'),
      a: '',
      refs: ctx.refs.map(r => r.ref['ref-code']),
      done: false
    }) - 1;
    appendMsg(bi);
    save(); reveal(ui);
    runAi(bi, ctx);
    return true;
  }

  /** The assistant's earlier answers before history[i] (for follow-up questions). */
  function aiPrev(i) {
    return history.slice(0, i).filter(x => x.role === 'bot' && x.mode === 'ai' && x.done && x.a && !x.error && !x.stopped).map(x => ({ q: x.q, a: x.a }));
  }

  /** Writes (or rewrites) the assistant's answer of history[i]. */
  async function runAi(i, precomputedCtx) {
    const m = history[i];
    const prev = aiPrev(i);
    const ctx = precomputedCtx || retrieveFor(m.q, prev);
    const curKey = LLM.choice();
    const curM = LLM.models()[curKey] || {};
    Object.assign(m, {
      mode: 'ai',
      modelKey: curKey,
      modelLabel: curM.label || 'Standard',
      modelId: (LLM.modelId || curM.id || '').replace('onnx-community/', ''),
      engineKind: LLM.kind || 'gpu',
      deviceKind: LLM.device || (LLM.kind === 'gpu' ? 'webgpu' : 'wasm'),
      a: '',
      refs: ctx.refs.map(r => r.ref['ref-code'].toUpperCase()),
      done: false,
      error: '',
      stopped: false
    });
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
    m.a = (m.a || '').replace(/<think>[\s\S]*?<\/think>/gi, '').trim();
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

  let checkingCachePromise = null;
  async function checkAllCached(kind, force = false) {
    if (!kind) return;
    if (checkingCachePromise && !force) return checkingCachePromise;
    checkingCachePromise = (async () => {
      const models = MODELS[kind] || {};
      let preloadedKeys = null;
      if (kind === 'cpu' && typeof caches !== 'undefined') {
        try {
          const c = await caches.open('transformers-cache');
          preloadedKeys = await c.keys();
        } catch (e) { /* ignore */ }
      }
      await Promise.all(Object.keys(models).map(async k => {
        if (!force && CACHED[kind + k] === true) return;
        try {
          const res = await Promise.race([
            LLM.cached(k, preloadedKeys),
            new Promise(r => setTimeout(() => r(false), 7000))
          ]);
          CACHED[kind + k] = !!res;
        } catch (e) {
          CACHED[kind + k] = false;
        }
      }));
      checkingCachePromise = null;
    })();
    return checkingCachePromise;
  }

  async function clearModelCaches() {
    try {
      if (LLM.busy) LLM.stop();
      if (LLM.state === 'ready' || LLM.engine || LLM.gen) {
        await LLM.unload();
      }
      if (LLM.lib && LLM.lib.deleteModelAllInfoInCache) {
        for (const k in (MODELS.gpu || {})) {
          try {
            await LLM.lib.deleteModelAllInfoInCache(MODELS.gpu[k].id + '-q4f16_1-MLC').catch(() => {});
            await LLM.lib.deleteModelAllInfoInCache(MODELS.gpu[k].id + '-q4f32_1-MLC').catch(() => {});
          } catch (e) { /* ignore */ }
        }
      }
      if (typeof caches !== 'undefined') {
        const keys = await caches.keys();
        for (const k of keys) {
          if (!k.startsWith('fischietto-')) {
            await caches.delete(k);
          }
        }
      }
      if (typeof indexedDB !== 'undefined' && indexedDB.databases) {
        try {
          const dbs = await indexedDB.databases();
          for (const db of dbs) {
            if (db.name && !db.name.startsWith('fischietto')) {
              indexedDB.deleteDatabase(db.name);
            }
          }
        } catch (e) { /* ignore */ }
      }
      for (const k in CACHED) CACHED[k] = false;
      storageInfo.usage = 0;
      checkingCachePromise = null;

      statusOpen = true;
      paintStatus();
      announce('Modelli AI rimossi: memoria liberata.');

      setTimeout(async () => {
        await updateStorageInfo(false);
        paintStatus();
      }, 600);
      setTimeout(async () => {
        await updateStorageInfo(false);
        paintStatus();
      }, 2000);
    } catch (e) {
      console.warn('Errore pulizia cache modelli:', e);
      storageInfo.usage = 0;
      for (const k in CACHED) CACHED[k] = false;
      paintStatus();
    }
  }

  let autoActivated = false;
  async function initAutoActivation() {
    updateStorageInfo(true);
    try {
      if (sessionStorage.getItem('fischietto-chatbot-mode') === 'search') {
        paintStatus();
        return;
      }
    } catch (e) { /* private mode */ }
    const kind = await LLM.detect();
    if (!kind) return;
    await checkAllCached(kind);
    paintStatus();
    if (!autoActivated && LLM.state === 'off' && !LLM.busy) {
      const best = bestCachedModel(kind);
      if (best) {
        autoActivated = true;
        LLM.setChoice(best);
        LLM.load(best, onLlmProgress);
        return;
      }
    }
  }

  async function disableAssistant() {
    try { sessionStorage.setItem('fischietto-chatbot-mode', 'search'); } catch (e) { /* private mode */ }
    await LLM.unload();
    paintStatus();
    paint();
    announce('Modalità ricerca attiva. L’assistente è disattivato.');
  }

  function enableAssistant() {
    try { sessionStorage.removeItem('fischietto-chatbot-mode'); } catch (e) { /* private mode */ }
    LLM.load(LLM.choice(), onLlmProgress);
  }

  function renderStorageSection() {
    const isPersist = storageInfo.persistent;
    const hasAnyCached = Object.keys(CACHED).some(k => CACHED[k] === true) || (storageInfo.usage > 30 * 1024 * 1024);
    let usageStr = '';
    if (storageInfo.usage >= 1073741824) {
      usageStr = (storageInfo.usage / 1073741824).toFixed(1) + ' GB';
    } else if (storageInfo.usage >= 1048576) {
      usageStr = (storageInfo.usage / 1048576).toFixed(0) + ' MB';
    }

    return '<div class="cb-storage-info">' +
      '<div class="cb-storage-row">' +
        '<span>' +
          '<strong>Memoria dispositivo:</strong> ' +
          (hasAnyCached && usageStr ? '<span>' + usageStr + ' occupati</span> · ' : '<span>Nessun modello salvato (0 MB)</span> · ') +
          (isPersist
            ? '<span class="cb-tag cb-tag-ok" title="Il browser non cancellerà i modelli automaticamente">' + ICON.check + ' Persistente (protetta)</span>'
            : '<span class="cb-tag cb-tag-warn" title="Il browser potrebbe cancellare i file temporanei se lo spazio sul disco si esaurisce">Standard (temporanea)</span>') +
        '</span>' +
        (hasAnyCached ? '<button type="button" class="btn btn-ghost btn-sm cb-clear-models-btn" id="cb-clear-models" title="Elimina i modelli AI salvati sul dispositivo per liberare memoria">' + ICON.trash + '<span>Libera spazio</span></button>' : '') +
      '</div>' +
      (!isPersist
        ? '<small class="cb-storage-note">Lo spazio è temporaneo: se il disco scarseggia, il browser potrebbe rimuovere la cache dei modelli. Per proteggerla, aggiungi Fischietto ai preferiti o installalo come app (PWA).</small>'
        : '<small class="cb-storage-note">I modelli salvati sono protetti dallo svuotamento automatico del browser.</small>') +
    '</div>';
  }

  function modelPicker(cur) {
    const list = Object.entries(LLM.models()).sort((a, b) => (a[1].power || 0) - (b[1].power || 0));
    return '<span class="cb-label">Modello</span><div class="cb-models" role="radiogroup" aria-label="Modello dell’assistente">' + list.map(([k, M]) => {
      const isCached = CACHED[LLM.kind + k] === true;
      const tag = isCached
        ? '<span class="cb-model-tag" title="File già scaricati sul dispositivo: nessun download necessario">' + ICON.check + '<span>Scaricato</span></span>'
        : '';
      return '<button type="button" class="cb-model' + (k === cur ? ' sel' : '') + (isCached ? ' cached' : '') + '" role="radio" aria-checked="' + (k === cur) + '" data-model="' + k + '">' +
        '<div class="cb-model-head"><strong>' + M.label + '</strong>' + tag + '</div>' +
        '<small>' + M.size + ' · ' + M.note + '</small>' +
        '</button>';
    }).join('') + '</div>';
  }

  function paintComposerMode() {
    const el = $('cb-mode-indicator');
    if (!el) return;
    const k = LLM.choice(), M = (LLM.models() || {})[k] || {};
    if (LLM.state === 'ready') {
      el.className = 'cb-mode-indicator is-ai';
      el.innerHTML = ICON.spark + '<span>Assistente AI · <strong>' + esc(M.label || 'Standard') + '</strong></span>';
    } else if (LLM.state === 'loading') {
      const pct = Math.round(LLM.progress * 100);
      el.className = 'cb-mode-indicator is-load';
      el.innerHTML = ICON.spark + '<span>Download ' + pct + '% · Ricerca</span>';
    } else {
      el.className = 'cb-mode-indicator is-search';
      el.innerHTML = ICON.search + '<span>Ricerca nei riferimenti</span>';
    }
  }

  /** Status of the assistant: checking / unsupported / off / downloading x% / ready / error. */
  async function paintStatus() {
    paintComposerMode();
    const box = $('cb-status');
    if (!box) return;
    const kind = await LLM.detect();
    const row = (dot, title, sub, actions) => '<div class="cb-st-row"><span class="cb-st-dot ' + dot + '"></span><div class="cb-st-text"><strong>' + title + '</strong>' + (sub ? '<span>' + sub + '</span>' : '') + '</div>' + (actions ? '<div class="cb-st-actions">' + actions + '</div>' : '') + '</div>';
    const more = '<button type="button" class="cb-link cb-more" id="cb-more" aria-expanded="' + statusOpen + '" aria-controls="cb-st-more">' + (statusOpen ? 'Meno' : 'Dettagli') + ICON.chevron + '</button>';
    if (!kind) {
      box.className = 'cb-status';
      box.innerHTML = row('off', 'Modalità ricerca', 'Rispondo con i passaggi più pertinenti dei riferimenti. Questo browser non può far girare l’assistente AI locale.');
      paintComposerMode();
      return;
    }
    const k = LLM.choice(), M = LLM.models()[k];
    box.className = 'cb-status cb-st-' + LLM.state;
    if (LLM.state === 'loading') {
      const pct = Math.round(LLM.progress * 100);
      box.innerHTML = row('load', 'Preparo l’assistente · ' + M.label + ' · ' + pct + '%',
        'Intanto puoi già fare domande: rispondo con la ricerca.',
        '<button type="button" class="btn btn-secondary btn-sm" id="cb-cancel-load">Passa alla ricerca</button>') +
        '<div class="cb-bar" role="progressbar" aria-label="Download del modello" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + pct + '"><i style="width:' + pct + '%"></i></div>' +
        '<small class="cb-st-file">' + esc(String(LLM.text || '').slice(0, 140)) + '</small>';
      const cancelBtn = $('cb-cancel-load');
      if (cancelBtn) cancelBtn.onclick = disableAssistant;
      paintComposerMode();
      return;
    }
    const isGpu = LLM.kind === 'gpu' || LLM.device === 'webgpu';
    const engineLabel = isGpu ? 'la scheda grafica (WebGPU)' : 'il processore (WebAssembly)';
    const firefoxTip = renderFirefoxGuide('status');
    const guideEl = $('cb-firefox-welcome-guide');
    if (guideEl) {
      guideEl.innerHTML = renderFirefoxGuide('welcome');
    }
    let details = '';
    if (LLM.state === 'ready') {
      box.innerHTML = row('on', 'Assistente AI attivo', 'Modello ' + M.label + ' sul tuo dispositivo, con ' + engineLabel + '.',
        '<button type="button" class="btn btn-secondary btn-sm" id="cb-disable" title="Disattiva l’assistente AI e passa alla semplice ricerca">' + ICON.search + '<span>Passa alla ricerca</span></button>' + more);
      details = '<p>Interpreta la domanda (anche le domande di seguito, come «e per il Libero?») e risponde usando solo i riferimenti trovati, citandone i codici.' +
        (!isGpu ? ' Con il processore la prima parola può arrivare dopo qualche secondo, poi la risposta si scrive man mano.' : '') + '</p>' +
        '<p><button type="button" class="cb-link" id="cb-disable-link">Disattiva l’assistente e usa solo la semplice ricerca</button></p>' +
        firefoxTip +
        modelPicker(k) +
        renderStorageSection();
    } else {
      const cached = CACHED[kind + k];
      if (cached === undefined) {
        checkAllCached(kind).then(() => { if (LLM.state === 'off' || LLM.state === 'error') paintStatus(); });
      }
      const err = LLM.state === 'error';
      box.innerHTML = row(err ? 'err' : 'off', err ? 'L’assistente non si è avviato' : 'Modalità ricerca',
        err ? esc(String(LLM.text || '').slice(0, 200)) : 'Rispondo con i passaggi dei riferimenti. L’assistente AI scrive la risposta con parole sue' + (cached === true ? ' (il modello è già scaricato).' : cached === false ? ' (al primo avvio scarica ' + M.size + ').' : '.'),
        '<button type="button" class="btn btn-primary btn-sm" id="cb-enable">' + ICON.spark + (err ? 'Riprova' : 'Attiva l’assistente') + '</button>' + more);
      details = '<p>L’assistente è un modello linguistico che gira <strong>sul tuo dispositivo</strong>, con ' + engineLabel + ': riceve domande solo in italiano, legge i riferimenti trovati e scrive la risposta citandoli. Le domande non lasciano mai il dispositivo.</p>' +
        '<p>' + (cached ? 'Il modello ' + M.label + ' è già scaricato.' : 'I modelli con l’etichetta «Scaricato» sono già presenti sul dispositivo e non richiedono download; per gli altri si scaricano i file al primo avvio (' + M.label + ': ' + M.size + ').') + '</p>' +
        (!isGpu && LLM.gpuNote ? '<p class="cb-st-note">' + esc(LLM.gpuNote) + ': in uso il motore WebAssembly su CPU.</p>' : '') +
        firefoxTip +
        modelPicker(k) +
        renderStorageSection();
      $('cb-enable').onclick = enableAssistant;
    }
    box.insertAdjacentHTML('beforeend', '<div class="cb-st-more" id="cb-st-more"' + (statusOpen ? '' : ' hidden') + '>' + details + '</div>');
    $('cb-more').onclick = () => { statusOpen = !statusOpen; paintStatus(); };
    const disableBtn = $('cb-disable');
    if (disableBtn) disableBtn.onclick = disableAssistant;
    const disableLink = $('cb-disable-link');
    if (disableLink) disableLink.onclick = disableAssistant;
    const clearModelsBtn = $('cb-clear-models');
    if (clearModelsBtn) {
      clearModelsBtn.onclick = async () => {
        if (!confirm('Vuoi rimuovere i modelli AI salvati sul dispositivo per liberare spazio? (Al prossimo avvio dell’assistente il modello scelto verrà riscaricato)')) {
          return;
        }
        clearModelsBtn.disabled = true;
        clearModelsBtn.innerHTML = '<span>Eliminazione…</span>';
        await clearModelCaches();
      };
    }
    box.querySelectorAll('[data-model]').forEach(b => b.onclick = () => {
      const was = LLM.choice();
      LLM.setChoice(b.dataset.model);
      try { sessionStorage.removeItem('fischietto-chatbot-mode'); } catch (e) { /* private mode */ }
      if (LLM.state === 'ready' && b.dataset.model !== was) LLM.load(b.dataset.model, onLlmProgress);
      else { paintStatus(); if (!history.length) paint(); }
    });
    paintComposerMode();
  }
  let wasReady = false;
  function onLlmProgress() {
    paintStatus();
    const ready = LLM.state === 'ready';
    if (ready !== wasReady) {
      wasReady = ready;
      if (LLM.state !== 'loading') announce(ready ? 'Assistente attivo' : 'L’assistente non si è avviato');
      if (ready) {
        CACHED[LLM.kind + LLM.choice()] = true;
        statusOpen = false;
        updateStorageInfo(true).then(() => paintStatus());
        paint();
      }
    }
  }

  // ---------------------------------------------------------------- mount

  function mount() {
    if (mounted) return;
    mounted = true;
    load();
    const form = $('cb-form');
    const input = $('cb-input');
    const counter = $('cb-counter');
    const updateCounter = () => {
      if (!counter || !input) return;
      const len = input.value.length;
      if (len > 0) {
        counter.textContent = len + ' / 600';
        counter.classList.toggle('is-warn', len > 500);
      } else {
        counter.textContent = '';
        counter.classList.remove('is-warn');
      }
    };
    const fit = () => { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 160) + 'px'; };
    form.addEventListener('submit', e => {
      e.preventDefault();
      if (LLM.busy) { stop(); return; }
      if (ask(input.value)) { input.value = ''; fit(); updateCounter(); setBusy(LLM.busy); }
    });
    input.addEventListener('keydown', e => {
      if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); if (!LLM.busy) form.requestSubmit(); }
      else if (e.key === 'Escape' && LLM.busy) { e.preventDefault(); stop(); }
    });
    input.addEventListener('input', () => { fit(); updateCounter(); setBusy(LLM.busy); });
    $('cb-clear').addEventListener('click', () => { stop(); history = []; save(); paint(); input.value = ''; fit(); updateCounter(); input.focus(); announce('Nuova conversazione'); });
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
    updateCounter();
    paintStatus();
    initAutoActivation();
    checkWebGpuActive().then(() => {
      const g = $('cb-firefox-welcome-guide');
      if (g) g.innerHTML = renderFirefoxGuide('welcome');
    });
  }

  window.Chatbot = {
    open() {
      mount();
      setTimeout(() => { const i = $('cb-input'); if (i && matchMedia('(pointer: fine)').matches) i.focus({ preventScroll: true }); }, 50);
      initAutoActivation();
      checkWebGpuActive().then(() => {
        const g = $('cb-firefox-welcome-guide');
        if (g) g.innerHTML = renderFirefoxGuide('welcome');
      });
    },
    answer, retrieveFor, buildMessages,
  };
})();
