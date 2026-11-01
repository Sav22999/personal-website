/* Fischietto · Match simulation: page logic (setup, game loop, controls, decisions, feedback, statistics). */
(function () {
  'use strict';

  const $ = id => document.getElementById(id);
  const E = window.SimEngine;
  const SIT = window.SimSituations.S;
  const CATS = window.SimSituations.CATS;
  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));

  /** Minimum permission level to use the simulation (beta: administrators only). */
  const SIM_MIN_LEVEL = 9;
  const STORE_KEY = 'fipav-sim';
  const TEAM_COLOR = { A: '#2f6fd0', B: '#e2552b' };

  let refs = {};
  let simData = null;

  // ======================================================================================
  // Saved data (statistics and reports), in the account's "sim" slot
  // ======================================================================================
  function loadSimData() {
    let d = null;
    try { d = JSON.parse(Store.getItem(STORE_KEY) || 'null'); } catch (e) {}
    if (!d || typeof d !== 'object') d = {};
    d.v = 1;
    d.byCat = d.byCat || {};
    d.bySit = d.bySit || {};
    d.games = Array.isArray(d.games) ? d.games : [];
    d.flagged = Array.isArray(d.flagged) ? d.flagged : [];
    return d;
  }
  function saveSimData() { try { Store.setItem(STORE_KEY, JSON.stringify(simData)); } catch (e) {} }

  function record(sitId, ok, helped) {
    const sit = SIT[sitId] || SIT.wrong_whistle;
    const c = simData.byCat[sit.cat] || (simData.byCat[sit.cat] = { c: 0, w: 0, h: 0 });
    const s = simData.bySit[sitId] || (simData.bySit[sitId] = { c: 0, w: 0 });
    if (ok) { c.c++; s.c++; } else { c.w++; s.w++; }
    if (helped) c.h++;
    G.decisions.push({ sit: sitId, ok, helped: !!helped });
    saveSimData();
  }

  // ======================================================================================
  // Sound: a referee's whistle, synthesised (no audio files)
  // ======================================================================================
  let audio = null;
  /** The referee's whistle: in volleyball it is always the same short, sharp blast. */
  function whistle() {
    try {
      audio = audio || new (window.AudioContext || window.webkitAudioContext)();
      const t0 = audio.currentTime;
      const dur = 0.34;
      const osc = audio.createOscillator();
      const trem = audio.createOscillator();
      const tremGain = audio.createGain();
      const gain = audio.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(2850, t0);
      trem.frequency.value = 38;
      tremGain.gain.value = 140;
      trem.connect(tremGain).connect(osc.frequency);
      gain.gain.setValueAtTime(0.0001, t0);
      gain.gain.exponentialRampToValueAtTime(0.16, t0 + 0.02);
      gain.gain.setValueAtTime(0.16, t0 + dur - 0.05);
      gain.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
      osc.connect(gain).connect(audio.destination);
      osc.start(t0); trem.start(t0);
      osc.stop(t0 + dur + 0.02); trem.stop(t0 + dur + 0.02);
    } catch (e) { /* audio not available */ }
  }

  // ======================================================================================
  // Toast and banner
  // ======================================================================================
  let toastTimer;
  function toast(msg) {
    const t = $('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), Math.max(2600, msg.length * 55));
  }
  let bannerTimer;
  function banner(title, text, ms) {
    const b = $('sim-banner');
    b.innerHTML = (title ? '<strong>' + esc(title) + '</strong>' : '') + esc(text);
    b.hidden = false;
    clearTimeout(bannerTimer);
    if (ms !== 0) bannerTimer = setTimeout(() => { b.hidden = true; }, ms || 2600);
  }
  function hideBanner() { clearTimeout(bannerTimer); $('sim-banner').hidden = true; }
  function hint(text) { $('sim-hint').textContent = text || ''; }

  // ======================================================================================
  // Game state
  // ======================================================================================
  const G = {
    M: null, R: null, renderer: null,
    phase: 'idle', t: 0, prepT: 0, paused: false, modal: null,
    view: 'first', top: false,
    prepFrom: {}, queue: [], event: null, eventT: 0,
    whistled: false, whistleT: null, helped: false,
    netShake: null, decisions: [], subAnim: null, lastFrame: 0, deadAt: null, afterDecision: null,
    aiPartnerGrace: 1.2,
    // Referee movement: own position (refX) and the automatic partner's position (partnerX)
    refX: 0, partnerX: 0, moveDir: 0, manualUntil: 0,
  };

  // The 1st referee only leans slightly on the stand (a few tens of centimetres); the 2nd walks along the side line
  const MOVE = { '1': { max: 0.3, speed: 0.45 }, '2': { max: 3.6, speed: 2.6 } };

  /**
   * Where a referee would stand by themselves: the 1st leans towards the attacking team (the side the ball is on),
   * the 2nd moves towards the receiving team (before the service the team receiving it, then the team about to defend).
   */
  function autoRefX(role) {
    const M = G.M, R = G.R;
    if (!M) return 0;
    let side;
    if (R && (G.phase === 'rally' || G.phase === 'decision')) {
      const b = M.ballAt(R, G.t);
      const bx = b ? b.x : M.side[R.serving];
      if (role === '1') side = Math.sign(bx) || 0;
      else side = G.t < R.tHit + 0.3 ? M.side[R.receiving] : -(Math.sign(bx) || 1);
    } else {
      const recv = R ? R.receiving : E.other(M.serving);
      side = role === '1' ? M.side[E.other(recv)] * 0.4 : M.side[recv];
    }
    return side * MOVE[role].max * (role === '1' ? 0.85 : 0.75);
  }

  function updateMovement(dt) {
    const M = G.M;
    if (!M || G.phase === 'idle' || G.phase === 'over') return;
    const myRole = M.opts.mode === '2' ? '2' : '1';
    const mv = MOVE[myRole];
    let target = G.refTarget === undefined ? 0 : G.refTarget;
    if (G.moveDir) {
      // Manual: arrows / on-screen controller. In the 2nd referee's view "right" on screen is towards x < 0
      const screenSign = G.view === 'second' ? -1 : 1;
      target = clamp(G.refX + G.moveDir * screenSign * mv.speed * 0.5, -mv.max, mv.max);
      G.manualUntil = performance.now() + 1800;
    } else if (M.opts.autoMove && performance.now() > G.manualUntil) {
      target = autoRefX(myRole);
    } else {
      target = G.refX;
    }
    G.refTarget = target;
    const step = mv.speed * dt;
    G.refX += clamp(target - G.refX, -step, step);
    G.renderer.setRefX(G.refX);
    // The automatic partner always moves by themselves
    if (M.opts.mode !== 'solo') {
      const pr = myRole === '1' ? '2' : '1';
      const pt = autoRefX(pr);
      const ps = MOVE[pr].speed * dt;
      G.partnerX += clamp(pt - G.partnerX, -ps, ps);
    }
  }

  function roleName(m) { return m === '1' ? '1° arbitro' : m === '2' ? '2° arbitro' : 'Arbitro unico'; }

  // ======================================================================================
  // Setup screen
  // ======================================================================================
  function initSetup() {
    const grid = $('role-grid');
    grid.querySelectorAll('.role-card').forEach(b => b.addEventListener('click', () => {
      grid.querySelectorAll('.role-card').forEach(x => { x.classList.toggle('active', x === b); x.setAttribute('aria-checked', x === b ? 'true' : 'false'); });
      $('opt-libero-row').hidden = b.dataset.mode === '1';
    }));
    $('opt-libero-row').hidden = true;
    ['opt-target', 'opt-freq'].forEach(id => $(id).querySelectorAll('button').forEach(b => b.addEventListener('click', () => {
      $(id).querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b));
    })));
    $('btn-start').addEventListener('click', () => {
      const mode = grid.querySelector('.role-card.active').dataset.mode;
      startMatch({
        mode,
        target: parseInt($('opt-target').querySelector('.active').dataset.v, 10),
        freq: parseFloat($('opt-freq').querySelector('.active').dataset.v),
        aids: $('opt-aids').checked,
        libero: $('opt-libero').checked,
        autoMove: $('opt-automove').checked,
        summary: $('opt-summary').checked,
      });
    });
    renderStats();
  }

  function renderStats() {
    const cats = Object.keys(simData.byCat);
    const card = $('sim-stats-card');
    if (!cats.length) { card.hidden = true; return; }
    card.hidden = false;
    let c = 0, w = 0, h = 0;
    cats.forEach(k => { c += simData.byCat[k].c; w += simData.byCat[k].w; h += simData.byCat[k].h || 0; });
    const pct = c + w ? Math.round(c / (c + w) * 100) : 0;
    let html = '<div class="ss-totals">' +
      '<div class="ss-tile"><b>' + simData.games.length + '</b><span>partite</span></div>' +
      '<div class="ss-tile"><b>' + (c + w) + '</b><span>decisioni</span></div>' +
      '<div class="ss-tile"><b>' + pct + '%</b><span>corrette</span></div>' +
      '<div class="ss-tile"><b>' + h + '</b><span>con l’aiuto</span></div></div>';
    Object.keys(CATS).filter(k => simData.byCat[k]).forEach(k => {
      const s = simData.byCat[k], n = s.c + s.w, p = n ? Math.round(s.c / n * 100) : 0;
      html += '<div class="ss-row"><span>' + esc(CATS[k]) + '</span><span class="ss-bar"><span style="width:' + p + '%"></span></span><span class="ss-num">' + p + '% · ' + n + '</span></div>';
    });
    $('sim-stats').innerHTML = html;
  }

  // ======================================================================================
  // Match lifecycle
  // ======================================================================================
  function startMatch(opts) {
    G.M = new E.Match(opts);
    G.decisions = [];
    G.log = [];
    G.lastCall = null;
    G.refX = 0; G.partnerX = 0; G.moveDir = 0; G.manualUntil = 0; G.refTarget = 0; G.timers = [];
    G.view = opts.mode === '2' ? 'second' : 'first';
    G.top = false;
    $('sim-setup').hidden = true;
    $('sim-end').hidden = true;
    $('sim-game').hidden = false;
    $('sim-top-actions').hidden = false;
    $('badge-role').textContent = roleName(opts.mode);
    $('sb-set').textContent = 'Set a ' + opts.target;
    if (!G.renderer) G.renderer = new SimRender.Renderer($('sim-canvas'));
    G.renderer.resize();
    applyView();
    updateScore();
    whistle();
    banner('Inizio del set', 'Al servizio la ' + E.TEAM_DEF[G.M.serving].label + '. ' + (opts.mode === '2' ? 'Il 1° arbitro autorizza i servizi.' : 'Tocca a te autorizzare il servizio.'), 3000);
    startBreak(true);
  }

  function endMatch(early) {
    G.phase = 'over';
    closeModals();
    const M = G.M;
    const ok = G.decisions.filter(d => d.ok).length, n = G.decisions.length;
    if (n) {
      simData.games.push({ date: new Date().toISOString(), mode: M.opts.mode, score: M.score.A + '-' + M.score.B, c: ok, w: n - ok, early: !!early });
      if (simData.games.length > 100) simData.games = simData.games.slice(-100);
      saveSimData();
    }
    $('sim-game').hidden = true;
    $('sim-top-actions').hidden = true;
    $('sim-end').hidden = false;
    const wrong = G.decisions.filter(d => !d.ok);
    const bySit = {};
    wrong.forEach(d => { bySit[d.sit] = (bySit[d.sit] || 0) + 1; });
    const pct = n ? Math.round(ok / n * 100) : 0;
    let html = '<h1>' + (early ? 'Partita interrotta' : 'Fine del set') + '</h1>' +
      '<p class="sim-lead">' + E.TEAM_DEF.A.label + ' ' + M.score.A + ' – ' + M.score.B + ' ' + E.TEAM_DEF.B.label + ' · ' + roleName(M.opts.mode) + '</p>' +
      '<div class="ss-totals" style="margin-top:16px">' +
        '<div class="ss-tile"><b>' + n + '</b><span>decisioni</span></div>' +
        '<div class="ss-tile"><b>' + ok + '</b><span>corrette</span></div>' +
        '<div class="ss-tile"><b>' + pct + '%</b><span>precisione</span></div>' +
        '<div class="ss-tile"><b>' + G.decisions.filter(d => d.helped).length + '</b><span>con l’aiuto</span></div></div>';
    if (wrong.length) {
      html += '<h2 class="sim-h2">Da ripassare</h2>';
      Object.keys(bySit).forEach(id => {
        const s = SIT[id] || SIT.wrong_whistle;
        html += '<div class="ss-row"><span>' + esc(s.title) + '</span><span class="form-hint">' + s.refs.map(esc).join(' · ') + '</span><span class="ss-num">' + bySit[id] + '×</span></div>';
      });
    } else if (n) {
      html += '<p style="margin-top:12px">Nessun errore: ottimo lavoro!</p>';
    }
    // Official signals of the set, to review: wrong decisions first, one per situation
    const seen = {};
    const withSig = wrong.concat(G.decisions.filter(d => d.ok)).filter(d => d.signal && !seen[d.sit] && (seen[d.sit] = true));
    if (withSig.length) {
      html += '<h2 class="sim-h2">Segnaletica della partita</h2><div class="end-signals">' + withSig.map((d, i) =>
        '<div class="end-sig' + (d.ok ? '' : ' ko') + '"><button class="end-sig-head" data-i="' + i + '" aria-expanded="false">' +
          '<span class="end-sig-mark">' + (d.ok ? '✓' : '✕') + '</span><span>' + esc((SIT[d.sit] || SIT.wrong_whistle).title) +
          '<small>' + esc(d.right || '') + ' · ' + (d.signal.role === '2' ? '2° arbitro' : '1° arbitro') + '</small></span><span class="end-sig-go">Segnaletica ›</span></button>' +
          '<div class="end-sig-body" hidden><div class="fg-fig"></div><div><p class="fg-cap"></p><ol class="fg-list">' +
          d.signal.steps.filter(s => s.pose !== 'rest' || /Attende|Si porta/.test(s.caption)).map(s => '<li>' + esc(s.caption) + '</li>').join('') +
          '</ol><button class="btn btn-ghost btn-sm end-sig-replay">↻ Rivedi il gesto</button></div></div></div>').join('') + '</div>';
    }
    if (G.log.length) html += '<details class="end-log"><summary>Cronologia della partita (' + G.log.length + ')</summary><div class="log-list">' + logHtml(G.log) + '</div></details>';
    html += '<div class="sim-start-row"><button class="btn btn-primary btn-lg" id="end-again">Nuova partita</button><a class="btn btn-ghost" href="./">Torna al portale</a></div>';
    $('sim-end-card').innerHTML = html;
    $('sim-end-card').querySelectorAll('.end-sig').forEach((box, i) => {
      const d = withSig[i];
      const body = box.querySelector('.end-sig-body'), fig = box.querySelector('.fg-fig');
      const run = () => SimGestures.play(fig, d.signal.steps, box.querySelector('.fg-cap'), box.querySelector('.fg-list'), d.sides);
      box.querySelector('.end-sig-head').addEventListener('click', e => {
        body.hidden = !body.hidden;
        e.currentTarget.setAttribute('aria-expanded', String(!body.hidden));
        if (body.hidden) SimGestures.stop(fig); else run();
      });
      box.querySelector('.end-sig-replay').addEventListener('click', run);
    });
    $('end-again').addEventListener('click', () => { $('sim-end').hidden = true; $('sim-setup').hidden = false; renderStats(); });
  }

  function updateScore() {
    const M = G.M;
    $('sb-score-a').textContent = M.score.A;
    $('sb-score-b').textContent = M.score.B;
    $('sb-serve-a').classList.toggle('on', M.serving === 'A');
    $('sb-serve-b').classList.toggle('on', M.serving === 'B');
    // Time-outs (2 per set), substitutions (out of MAX_SUBS) and sanctions of each team
    ['A', 'B'].forEach(T => {
      const t = M.teams[T];
      const to = [0, 1].map(i => '<i class="sbx-dot' + (i < t.timeouts ? ' used' : '') + '"></i>').join('');
      const cards = t.sanctions.map(x => {
        const c = /Espulsione|Squalifica/.test(x) ? 'yr' : /Penalizzazione/.test(x) ? 'r' : /giallo|Avvertimento per ritardo/.test(x) ? 'y' : 'v';
        return '<i class="sbx-card ' + c + (/ritardo/.test(x) ? ' delay' : '') + '" title="' + esc(x) + '"></i>';
      }).join('');
      const box = $('sbx-' + T.toLowerCase());
      box.innerHTML = '<span class="sbx-item" title="Tempi di riposo usati: ' + t.timeouts + ' su 2">T ' + to + '</span>' +
        '<span class="sbx-item" title="Sostituzioni usate: ' + t.subs.count + ' su ' + E.MAX_SUBS + '">S ' + t.subs.count + '/' + E.MAX_SUBS + '</span>' +
        (cards ? '<span class="sbx-item sbx-cards">' + cards + '</span>' : '');
    });
  }

  // ======================================================================================
  // Views
  // ======================================================================================
  function applyView() {
    const v = G.top ? 'top' : G.view;
    G.renderer.setView(v);
    $('btn-view').classList.toggle('active', G.top);
    $('btn-view-label').textContent = G.top ? (G.view === 'second' ? 'Sotto rete' : 'Seggiolone') : 'Dall’alto';
  }

  // ======================================================================================
  // Break between rallies
  // ======================================================================================
  /**
   * Game-flow timers run on the animation loop (not setTimeout), so they cannot get lost or throttled:
   * they advance with the game clock and wait while the game is paused or a window is open.
   */
  function later(fn, ms, opts) {
    (G.timers = G.timers || []).push({ left: (ms || 0) / 1000, fn, always: !!(opts && opts.always) });
  }
  function runTimers(dt, running) {
    if (!G.timers || !G.timers.length) return;
    const due = [];
    G.timers = G.timers.filter(t => {
      if (running || t.always) t.left -= dt;
      if (t.left <= 0) { due.push(t.fn); return false; }
      return true;
    });
    due.forEach(fn => { try { fn(); } catch (e) { console.error(e); } });
  }

  // ======================================================================================
  // Game log ("Cronologia"): every rally and every request or decision between rallies, with who took it
  // ======================================================================================
  function userRole() { return G.M.opts.mode === '2' ? '2° arbitro' : G.M.opts.mode === 'solo' ? 'Arbitro unico' : '1° arbitro'; }
  const LOG_KIND = { rally: 'Azione', timeout: 'Tempo di riposo', sub: 'Sostituzione', libero: 'Rimpiazzo del Libero', conduct: 'Condotta' };
  function logEntry(x) {
    const c = G.lastCall || {};
    G.lastCall = null;
    if (c.quiet) return;              // regular Libero replacements handled by the 2nd referee: no entry
    G.log.push({
      kind: x.kind, before: x.before, after: G.M.score.A + '–' + G.M.score.B, outcome: x.outcome,
      by: c.by || (x.kind === 'rally' ? '1° arbitro' : '2° arbitro'), user: !!c.user, ok: c.ok, yours: c.yours, happened: c.happened || '',
      replay: x.replay || null,
    });
    if (!$('log').hidden) renderLog();
  }
  function logHtml(list) {
    if (!list.length) return '<p class="form-hint">Ancora nessuna azione.</p>';
    return list.slice().reverse().map(l => {
      const cls = l.user ? (l.ok ? 'ok' : 'ko') : 'ai';
      const n = G.log.indexOf(l) + 1;
      return '<div class="lg-item ' + cls + '">' +
        '<div class="lg-top"><span class="lg-n">' + n + '. ' + esc(LOG_KIND[l.kind] || 'Evento') + '</span><span class="lg-score">' + esc(l.before === l.after ? l.after : l.before + ' → ' + l.after) + '</span></div>' +
        '<div class="lg-what">' + esc(l.outcome) + '</div>' +
        (l.happened ? '<div class="lg-hap">' + esc(l.happened) + '</div>' : '') +
        '<div class="lg-by"><span class="lg-badge">' + esc(l.by) + (l.user ? ' · tu' : '') + '</span>' +
          (l.user ? (l.ok ? '<span class="lg-res ok">✓ corretta</span>' : '<span class="lg-res ko">✕ tua decisione: ' + esc(l.yours || '') + '</span>') : '') +
          (l.replay ? '<button class="lg-replay" data-log="' + (n - 1) + '" title="Rivedi l’azione">▶ Rivedi</button>' : '') + '</div>' +
        '</div>';
    }).join('');
  }
  /** Hides the decision window for a moment to look at the court (the game stays paused). */
  function peekCourt() {
    $('decision-modal').hidden = true;
    $('peek-bar').hidden = false;
    $('peek-view').textContent = G.top ? 'Vista dal tuo posto' : 'Vista dall’alto';
    setTimeout(() => $('peek-back').focus(), 30);
  }
  function endPeek() {
    $('peek-bar').hidden = true;
    $('decision-modal').hidden = false;
    G.modal = 'decision-modal';
  }

  /** The decision windows can always open the game log (the drawer opens above the window, like the score sheet). */
  function bindDecLog() {
    $('dec-log').addEventListener('click', () => toggleLog(true));
  }
  function renderLog() { $('log-body').innerHTML = logHtml(G.log || []); }
  function toggleLog(force) {
    const s = $('log');
    const show = force === undefined ? s.hidden : force;
    if (show) toggleSheet(false);
    s.hidden = !show;
    $('btn-log').classList.toggle('active', show);
    if (show) renderLog();
  }

  /** Short history of the game flow, for diagnosing problems in tests. */
  function trace(x) { (G.trace = G.trace || []).push(x + '@' + G.phase); if (G.trace.length > 40) G.trace.shift(); }

  function startBreak(first) {
    trace('startBreak');
    const M = G.M;
    if (M.over) return endMatch(false);
    G.phase = 'break';
    G.queue = first ? M.betweenEvents().filter(e => e.kind === 'libero') : M.betweenEvents();
    G.event = null;
    nextEvent();
  }

  function nextEvent() {
    trace('nextEvent q' + G.queue.length);
    const M = G.M;
    G.subAnim = null;
    if (M.over) return endMatch(false);
    if (!G.queue.length) return startPrep();
    const e = G.queue.shift();
    if (!M.stillValid(e)) return nextEvent();
    G.event = e;
    G.eventT = 0;
    if (e.user) {
      hint(e.kind === 'conduct' ? 'Che provvedimento prendi?' : 'Decidi sulla richiesta');
      later(() => openEventDecision(e), e.kind === 'libero' ? 700 : 500);
    } else {
      // Handled by the automatic referee
      later(() => {
        const exp = e.expected || (e.regular ? 'authorize' : null);
        const txt = aiEventText(e, exp);
        G.lastCall = { by: e.comp === '1' ? '1° arbitro' : '2° arbitro', user: false, right: txt, happened: e.text, quiet: e.kind === 'libero' && e.regular };
        if (txt) banner(e.comp === '1' ? '1° arbitro' : '2° arbitro', txt, 2400);
        finishEvent(e, txt ? 1700 : 500);
      }, e.kind === 'libero' && e.regular ? 300 : 900);
    }
  }

  function aiEventText(e, exp) {
    const lbl = E.TEAM_DEF[e.team].label;
    if (e.kind === 'libero' && e.regular) return '';
    if (e.kind === 'libero') return 'Rimpiazzo irregolare del Libero della ' + lbl + ': va corretto, ' + (exp === 'delay_penalty' ? 'penalizzazione' : 'avvertimento') + ' per ritardo.';
    if (e.kind === 'timeout') return exp === 'authorize' ? 'Tempo di riposo alla ' + lbl + '.' : 'Richiesta di tempo di riposo della ' + lbl + ' respinta: ' + E.REQUEST_DECISIONS[exp].replace(/^Respingi[^:]*: ?/, '') + '.';
    if (e.kind === 'sub') return exp === 'authorize' ? 'Sostituzione nella ' + lbl + ': entra il n. ' + e.inn + ', esce il n. ' + e.out + '.' : 'Sostituzione della ' + lbl + ' respinta: ' + E.REQUEST_DECISIONS[exp].replace(/^Respingi[^:]*: ?/, '') + '.';
    if (e.kind === 'conduct') return E.CONDUCT_DECISIONS[exp] + ' al n. ' + e.who + ' della ' + lbl + '. ' + e.text;
    return '';
  }

  function finishEvent(e, delay) {
    trace('finishEvent ' + e.kind);
    const before = G.M.score.A + '–' + G.M.score.B;
    // Snapshot of the scene for "Rivedi" in the log (who was on court and where, before the change)
    const snap = { event: Object.assign({}, e), pos: JSON.parse(JSON.stringify(G.M.pos)),
      court: { A: [0, 1, 2, 3, 4, 5].map(k => G.M.onCourt('A', k)), B: [0, 1, 2, 3, 4, 5].map(k => G.M.onCourt('B', k)) } };
    const res = G.M.applyBetween(e);
    updateScore();
    logEntry({ kind: e.kind, before, outcome: (G.lastCall && G.lastCall.right) || aiEventText(e, e.expected || (e.regular ? 'authorize' : null)) || e.text,
      replay: { snap, from: 0, to: 4, focus: 1.6, label: LOG_KIND[e.kind] || 'Richiesta', event: true } });
    if (e.kind === 'timeout' && (e.expected === 'authorize')) {
      banner('Tempo di riposo', E.TEAM_DEF[e.team].label + ' · 30 secondi', 2200);
      delay = Math.max(delay || 0, 2300);
    }
    if (res.point) banner('Punto', 'Punto e servizio alla ' + E.TEAM_DEF[res.point].label + '.', 2200);
    if (res.followUp && res.followUp.length) G.queue.unshift(...res.followUp);
    G.event = null;
    later(() => { if (G.phase === 'break') nextEvent(); }, delay || 400);
  }

  function openEventDecision(e) {
    if (G.phase !== 'break') return;
    const opts = e.kind === 'conduct' ? E.CONDUCT_DECISIONS
      : e.kind === 'libero' ? { authorize: 'Regolare: lascia proseguire', delay_warning: 'Irregolare: correggi, avvertimento per ritardo', delay_penalty: 'Irregolare: correggi, penalizzazione per ritardo' }
      : E.REQUEST_DECISIONS;
    $('decision-title').textContent = e.kind === 'conduct' ? 'Condotta di un giocatore' : e.kind === 'libero' ? 'Rimpiazzo del Libero' : e.kind === 'timeout' ? 'Richiesta di tempo di riposo' : 'Richiesta di sostituzione';
    $('decision-sub').textContent = '';
    let html = '<div class="dec-event">' + esc(e.text) + '</div><div class="dec-grid" style="margin-top:12px">';
    Object.keys(opts).forEach(k => { html += '<button class="dec-btn" data-k="' + k + '">' + esc(opts[k]) + '</button>'; });
    html += '</div><div class="dec-back"><button class="btn btn-ghost btn-sm" id="dec-help">Aiuto</button> <button class="btn btn-ghost btn-sm" id="dec-sheet">Apri il referto</button> <button class="btn btn-ghost btn-sm" id="dec-log">Cronologia</button></div>';
    $('decision-body').innerHTML = html;
    bindDecLog();
    $('decision-body').querySelectorAll('[data-k]').forEach(b => b.addEventListener('click', () => {
      closeModal('decision-modal');
      evaluateEvent(e, b.dataset.k);
    }));
    $('dec-help').addEventListener('click', () => openHelp());
    $('dec-sheet').addEventListener('click', () => toggleSheet(true));
    openModal('decision-modal');
  }

  function evaluateEvent(e, choice) {
    trace('evaluateEvent ' + e.kind);
    const exp = e.expected || 'authorize';
    const ok = choice === exp;
    const sitId = e.sit;
    record(sitId, ok, e.helped);
    const opts = e.kind === 'conduct' ? E.CONDUCT_DECISIONS : E.REQUEST_DECISIONS;
    const label = k => (e.kind === 'libero' && k === 'authorize') ? 'Lasciare proseguire (regolare)' : opts[k];
    showFeedback({
      ok, sitId,
      // Routine correct calls (authorised requests, regular Libero replacements) do not stop the game
      quiet: ok && exp === 'authorize',
      happened: e.text,
      yours: label(choice),
      right: label(exp),
      note: null,
      context: e.text,
      signal: eventSignal(e, exp),
      yourSignal: eventSignal(e, choice),
    }, () => finishEvent(e, 400));
  }

  // ======================================================================================
  // Before the service
  // ======================================================================================
  function startPrep() {
    trace('startPrep');
    const M = G.M;
    G.phase = 'prep';
    G.prepT = 0;
    G.whistled = false;
    G.whistleT = null;
    G.helped = false;
    G.deadAt = null;
    G.netShake = null;
    G.authorized = false;
    G.prepFrom = {};
    Object.keys(M.pos).forEach(k => { G.prepFrom[k] = { x: M.pos[k].x, y: M.pos[k].y }; });
    const plan = M.planRally();
    G.R = M.buildRally(plan);
    G.R.early = plan.sit === 'serve_early';
    applyView();
    $('btn-whistle').classList.remove('pulse');
    if (M.opts.mode === '2') hint('Controlla la formazione della squadra in ricezione');
    else hint('Squadre in posizione: autorizza il servizio quando sono pronte');
  }

  function authorize() {
    if (G.phase === 'prep') {
      if (G.prepT < 1.3) { toast('Aspetta che le squadre siano pronte e che il giocatore al servizio abbia la palla.'); return; }
      G.authorized = true;
      whistle();
      beginRally();
      return;
    }
    // Service before the whistle: if you authorise before the hit, the service becomes regular
    if (G.phase === 'rally' && G.R.early && !G.authorized && G.t < G.R.tHit - 0.15) {
      G.authorized = true;
      G.R.early = false;
      G.R.faults = G.R.faults.filter(f => f.key !== 'serve_early');
      G.R.plan.sit = null;
      whistle();
      $('btn-authorize').hidden = true;
    }
  }

  function beginRally() {
    G.phase = 'rally';
    G.t = 0;
    $('btn-authorize').hidden = true;
    $('btn-authorize').classList.remove('pulse');
    hint(G.M.opts.mode === '2' ? 'Fischia se vedi un fallo di tua competenza' : 'Fischia quando vedi un fallo');
  }

  // ======================================================================================
  // Main loop
  // ======================================================================================
  function frame(now) {
    requestAnimationFrame(frame);
    if (!G.M || G.phase === 'idle' || G.phase === 'over') return;
    const dt = Math.min(0.05, (now - (G.lastFrame || now)) / 1000);
    G.lastFrame = now;
    const running = !G.paused && !G.modal;
    if (running) { step(dt * (G.speed || 1)); updateMovement(dt * (G.speed || 1)); }
    runTimers(dt * (G.speed || 1), running);
    draw();
  }

  function slowNow() {
    const R = G.R;
    if (!R || !G.M.opts.aids || G.phase !== 'rally') return false;
    return R.slow.some(s => G.t >= s - 0.35 && G.t <= s + 0.6);
  }

  function step(dt) {
    const M = G.M, R = G.R;
    if (G.phase === 'break') { G.eventT += dt; return; }
    if (G.phase === 'prep') {
      G.prepT += dt;
      const ready = G.prepT >= 1.3;
      if (M.opts.mode === '2') {
        if (G.prepT >= 2.6) { G.authorized = true; whistle(); banner('1° arbitro', 'Autorizza il servizio.', 1200); beginRally(); }
      } else {
        $('btn-authorize').hidden = !ready;
        $('btn-authorize').classList.toggle('pulse', ready);
        // Service before the whistle: the server does not wait for you
        if (R.early && G.prepT >= 1.6) { G.phase = 'rally'; G.t = 0; hint('Fischia quando vedi un fallo'); }
      }
      return;
    }
    if (G.phase === 'rally') {
      // "Authorise" stays available only while a service before the whistle can still be prevented
      $('btn-authorize').hidden = G.authorized || !R.early || G.t > R.tHit - 0.1;
      const slow = slowNow();
      $('badge-slow').hidden = !slow;
      const prevT = G.t;
      G.t += dt * (slow ? 0.28 : 1);
      // Net shakes
      R.events.forEach(ev => { if (ev.kind === 'net' && ev.t > prevT && ev.t <= G.t) G.netShake = { y: ev.y, t0: G.t, amp: ev.strong ? 0.09 : 0.05 }; });
      // The automatic referee whistles faults of their competence
      for (const f of R.faults) {
        if (G.whistled) break;
        const userDecides = M.userDecides(f.comp);
        const partner = M.opts.mode !== 'solo';
        if (!userDecides && G.t >= f.t + 0.35) return aiWhistle(f, false);
        if (userDecides && f.comp === 'both' && partner && G.t >= f.t + G.aiPartnerGrace + 0.35) return aiWhistle(f, true);
        if (f.t > G.t) break;
      }
      // Ball dead
      if (G.t >= R.endT + 0.35 && !G.whistled) {
        if (G.deadAt === null) G.deadAt = G.t;
        ballDead();
      }
    }
  }

  // ======================================================================================
  // Whistles and decisions
  // ======================================================================================
  function userWhistle() {
    if (G.paused || G.modal) return;
    if (G.phase === 'rally' && !G.whistled) {
      whistle();
      G.whistled = true;
      G.whistleT = G.t;
      $('badge-slow').hidden = true;
      openRallyDecision(G.t);
      return;
    }
    if (G.phase === 'prep') { toast('Il gioco non è ancora iniziato: prima va autorizzato il servizio.'); return; }
  }

  function aiWhistle(f, userMissed) {
    const M = G.M;
    G.whistled = true;
    whistle();
    const who = M.opts.mode === '2' ? '1° arbitro' : '2° arbitro';
    const tr = { fault: f };
    const exp = M.expected(G.R, tr);
    if (userMissed) {
      record(f.sit, false, G.helped);
      showFeedback({
        ok: false, sitId: f.sit,
        happened: f.text,
        yours: 'Non hai fischiato', yourSignal: { role: '1', steps: [], note: 'Non hai fischiato: nessuna segnaletica.' },
        right: decisionLabel(exp),
        note: 'Questo fallo era anche di tua competenza: lo ha fischiato il ' + who + '.',
        by: who,
        replay: replayInfo(G.R, f, true),
        context: f.text,
        signal: rallySignal(exp, f.comp),
      }, () => afterRally(exp));
      return;
    }
    G.lastCall = { by: who, user: false, happened: f.text };
    banner(who + ' · fischio', E.DECISIONS[f.key].label + (exp.point ? ': punto alla ' + E.TEAM_DEF[exp.point].label : '') + '. ' + f.text, 3200);
    later(() => afterRally(exp), 2400);
  }

  function ballDead() {
    const M = G.M, R = G.R;
    G.whistled = true;
    const tr = M.truth(R, null);
    const exp = M.expected(R, tr);
    if (M.opts.mode === '2') {
      // The 1st referee ends the rally; a missed fault of yours is shown
      if (tr.fault && M.userDecides(tr.fault.comp)) {
        whistle();
        record(tr.fault.sit, false, G.helped);
        showFeedback({ ok: false, sitId: tr.fault.sit, happened: tr.fault.text, yours: 'Non hai fischiato', yourSignal: { role: '1', steps: [], note: 'Non hai fischiato: nessuna segnaletica.' }, right: decisionLabel(exp), note: 'L’azione è proseguita: andava fischiato subito.', by: '1° arbitro', replay: replayInfo(R, tr.fault, true), context: tr.fault.text, signal: rallySignal(exp, tr.fault.comp) }, () => afterRally(exp));
        return;
      }
      whistle();
      G.lastCall = { by: '1° arbitro', user: false, happened: R.natural ? naturalText(R.natural) : '' };
      banner('1° arbitro', decisionLabel(exp) + '.', 2200);
      later(() => afterRally(exp), 1800);
      return;
    }
    // 1st referee / sole referee: you end the rally
    hint('Fischia e indica l’esito');
    $('btn-whistle').classList.add('pulse');
    later(() => { if (G.phase === 'rally' && !G.modal) { whistle(); openRallyDecision(null); } }, 900);
  }

  function decisionLabel(exp) {
    const d = E.DECISIONS[exp.key];
    return d.label + (exp.point ? ' · punto alla ' + E.TEAM_DEF[exp.point].label : '');
  }

  /** Two steps: what (fault type or outcome), then which team wins the point. */
  function openRallyDecision(whistleT) {
    const M = G.M;
    G.phase = 'decision';
    $('btn-whistle').classList.remove('pulse');
    $('decision-title').textContent = whistleT === null ? 'Fine dell’azione: che cosa decidi?' : 'Hai fischiato: che cosa hai visto?';
    $('decision-sub').textContent = '';
    const groups = {};
    Object.keys(E.DECISIONS).forEach(k => {
      const comp = k === 'pos_fault' ? 'pos' : E.COMPETENCE[k];
      const show = k === 'double_fault' || k === 'no_fault' || comp === 'pos' || (comp && M.userDecides(comp));
      if (!show) return;
      if (whistleT === null && k === 'no_fault') return;
      if (k === 'serve_early' && M.opts.mode === '2') return;
      const g = E.DECISIONS[k].group;
      (groups[g] = groups[g] || []).push(k);
    });
    let html = '';
    Object.keys(groups).forEach(g => {
      html += '<div class="dec-group"><h5>' + esc(g) + '</h5><div class="dec-grid">' +
        groups[g].map(k => '<button class="dec-btn" data-k="' + k + '">' + esc(E.DECISIONS[k].label) + '</button>').join('') + '</div></div>';
    });
    html += '<div class="dec-back"><button class="btn btn-ghost btn-sm" id="dec-help">Aiuto</button> <button class="btn btn-ghost btn-sm" id="dec-sheet">Referto</button> <button class="btn btn-ghost btn-sm" id="dec-top">Vista dall’alto</button> <button class="btn btn-ghost btn-sm" id="dec-log">Cronologia</button></div>';
    $('decision-body').innerHTML = html;
    bindDecLog();
    $('decision-body').querySelectorAll('[data-k]').forEach(b => b.addEventListener('click', () => chooseTeam(b.dataset.k, whistleT)));
    $('dec-help').addEventListener('click', () => openHelp());
    $('dec-sheet').addEventListener('click', () => toggleSheet(true));
    $('dec-top').addEventListener('click', () => { G.top = !G.top; applyView(); peekCourt(); });
    openModal('decision-modal');
  }

  function chooseTeam(key, whistleT) {
    const d = E.DECISIONS[key];
    closeModal('decision-modal');
    if (!d.team) return evaluateRally({ key, point: null }, whistleT);
    openPointPicker(d.label, team => evaluateRally({ key, point: team }, whistleT), () => openRallyDecision(whistleT));
  }

  // ---------- Point: tap the half court of the team that wins it ----------
  let pick = null;
  /** Shows the two half courts as tappable areas (labelled with the team on that side of the current view). */
  function openPointPicker(label, onPick, onBack) {
    const layer = $('pick-layer');
    const cam = G.renderer.cam;
    const w = G.renderer.cssW, h = G.renderer.cssH;
    let svg = '<svg viewBox="0 0 ' + w + ' ' + h + '" width="100%" height="100%">';
    ['A', 'B'].forEach(T => {
      const s = G.M.side[T];
      // The team's half court plus the free zone behind and beside it
      const pts = cam.projectPoly([[0, -6, 0], [s * 12, -6, 0], [s * 12, 6.4, 0], [0, 6.4, 0]]);
      if (!pts) return;
      const cx = pts.reduce((a, p) => a + clamp(p.x, 0, w), 0) / pts.length;
      const cy = pts.reduce((a, p) => a + clamp(p.y, 0, h), 0) / pts.length;
      svg += '<g class="pick-half pick-' + T.toLowerCase() + '" data-p="' + T + '" tabindex="0" role="button" aria-label="Punto alla Squadra ' + T + '">' +
        '<polygon points="' + pts.map(p => p.x.toFixed(1) + ',' + p.y.toFixed(1)).join(' ') + '"/>' +
        '<g class="pick-label" transform="translate(' + clamp(cx, 90, w - 90).toFixed(0) + ' ' + clamp(cy, 120, h - 150).toFixed(0) + ')">' +
          '<rect x="-78" y="-22" width="156" height="44" rx="22"/><text text-anchor="middle" dy="6">Squadra ' + T + '</text></g></g>';
    });
    svg += '</svg>';
    layer.innerHTML = svg + '<div class="pick-head"><strong>' + esc(label) + '</strong><span>Tocca la metà campo della squadra che vince il punto</span>' +
      '<button class="btn btn-ghost btn-sm" id="pick-back">← Cambia decisione</button></div>';
    layer.hidden = false;
    G.modal = 'pick';
    pick = { onPick, onBack };
    const done = team => { closePicker(); onPick(team); };
    layer.querySelectorAll('[data-p]').forEach(g => {
      g.addEventListener('click', () => done(g.dataset.p));
      g.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); done(g.dataset.p); } });
    });
    $('pick-back').addEventListener('click', () => { closePicker(); onBack(); });
  }
  function closePicker() {
    $('pick-layer').hidden = true;
    $('pick-layer').innerHTML = '';
    pick = null;
    if (G.modal === 'pick') G.modal = null;
  }

  function evaluateRally(dec, whistleT) {
    const M = G.M, R = G.R;
    const tr = M.truth(R, whistleT);
    const exp = M.expected(R, tr);
    let sitId, happened, note = null;
    if (tr.fault) {
      sitId = tr.fault.sit;
      happened = tr.fault.text;
      if (tr.missed) note = 'Il fallo è avvenuto durante l’azione: andava fischiato subito, senza aspettare la fine.';
      else if (tr.late) note = 'Hai fischiato con un po’ di ritardo rispetto al fallo.';
    } else if (tr.wrongWhistle) {
      if (R.trap) { sitId = R.trap.sit; happened = R.trap.text; }
      else if (R.plan.sit === 'serve_8s' && whistleT < 8) { sitId = 'serve_8s'; happened = 'Hai fischiato dopo ' + whistleT.toFixed(1).replace('.', ',') + ' secondi: il giocatore aveva ancora tempo per servire.'; }
      else { sitId = 'wrong_whistle'; happened = 'Nel momento in cui hai fischiato non c’era alcun fallo.'; }
    } else {
      const n = R.natural;
      if (R.trap) note = R.trap.text + ' Bene non aver fischiato.';
      if (n.noWhistle) note = (note ? note + ' ' : '') + 'Con la sperimentazione 2026/27 su un servizio senza tocchi non si fischia: basta la segnaletica (Normative federali 4.7).';
      sitId = n.key === 'touched_out' ? 'touched_out' : n.borderline ? 'no_fault_close' : n.key === 'serve_net' ? 'serve_net' : exp.key === 'in' ? 'rally_in' : 'rally_out';
      happened = naturalText(n);
    }
    const ok = dec.key === exp.key && (exp.point === null || dec.point === exp.point);
    record(sitId, ok, G.helped);
    showFeedback({
      ok, sitId, happened, note,
      yours: E.DECISIONS[dec.key].label + (dec.point ? ' · punto alla ' + E.TEAM_DEF[dec.point].label : ''),
      right: decisionLabel(exp),
      context: happened,
      quiet: ok && !tr.fault && !tr.wrongWhistle && !R.trap && !(R.natural && R.natural.borderline) && sitId !== 'touched_out',
      signal: rallySignal(exp, tr.fault ? tr.fault.comp : null, !tr.fault && !tr.wrongWhistle && R.natural && R.natural.noWhistle),
      // The signal for the decision the user took (shown even when it is wrong)
      yourSignal: rallySignal(dec, null, !whistleT && (dec.key === 'in' || dec.key === 'out') && R.natural && R.natural.noWhistle),
      replay: replayInfo(R, tr.fault, !!tr.missed),
    }, () => afterRally(exp));
  }

  function naturalText(n) {
    const T = k => E.TEAM_DEF[k].label;
    if (n.key === 'touched_out') return 'La palla d’attacco tocca le mani del muro della ' + T(n.team) + ' ed esce.';
    if (n.key === 'serve_net') return 'Il servizio della ' + T(n.team) + ' non supera la rete.';
    if (n.key === 'in') {
      let s = 'La palla tocca terra nel campo della ' + T(n.team);
      if (n.borderline) s += n.dist > 0 ? ': il centro è ' + Math.round(n.dist * 100) + ' cm oltre la linea, ma la palla tocca la linea' : ': tocca il campo vicino alla linea';
      return s + '.';
    }
    let s = 'La palla inviata dalla ' + T(n.team) + ' esce';
    if (n.borderline) s += ': il centro è ' + Math.round(n.dist * 100) + ' cm oltre la linea e nessuna parte della palla la tocca';
    return s + '.';
  }

  /** Applies the correct outcome, saves the players' positions and moves on. */
  function afterRally(exp) {
    trace('afterRally');
    const M = G.M, R = G.R;
    // Positions at the end of the rally become the start of the next one
    Object.keys(R.moves).forEach(k => {
      const p = M.playerAt(R, k, G.t);
      if (p) M.pos[k] = { x: p.x, y: p.y };
    });
    const before = M.score.A + '–' + M.score.B;
    const replay = exp.key === 'serve_early' || exp.key === 'no_fault' || exp.key === 'double_fault';
    if (replay) {
      banner('Si ripete', exp.key === 'serve_early' ? 'Il servizio è annullato e va ripetuto.' : 'L’azione va rigiocata.', 2000);
    } else {
      M.applyRally(exp);
    }
    const f0 = R.faults[0];
    logEntry({ kind: 'rally', before, outcome: replay ? decisionLabel(exp) + ' · si ripete' : decisionLabel(exp),
      replay: replayInfo(R, f0 && f0.key === exp.key ? f0 : null, false) });
    updateScore();
    hideBanner();
    G.R = null;
    hint('');
    later(() => startBreak(false), 600);
    G.phase = 'break';
    G.queue = [];
  }

  // ======================================================================================
  // Official signals (Rule 22.2.3, Indicazioni tecniche 4.3)
  // ======================================================================================
  const FAULT_SIGNAL = {
    in: { pose: 'in', name: 'Palla dentro: braccio teso verso il campo, palmo aperto' },
    out: { pose: 'out', name: 'Palla fuori: braccia piegate a 90°, avambracci verticali, palmi verso il campo, dita unite' },
    touched_out: { pose: 'touched', motion: 'brush', name: 'Palla toccata: mano verticale in alto, dorso verso i campi; l’altra mano, palmo in basso, sfiora le dita una volta da dietro in avanti' },
    serve_foot: { pose: 'line', name: 'Fallo del giocatore al servizio: indica la linea di fondo, senza oscillare il braccio' },
    serve_8s: { pose: 'eight', name: 'Ritardo al servizio: otto dita divaricate' },
    serve_net: { pose: 'net', name: 'Palla di servizio che non supera la rete: braccio teso dritto verso il segnapunti all’altezza della spalla, mano aperta in prosecuzione del braccio' },
    pos_fault: { pose: 'position', motion: 'circle', name: 'Fallo di posizione: movimento circolare con l’indice' },
    four_hits: { pose: 'four', name: 'Quattro tocchi: quattro dita divaricate' },
    catch: { pose: 'catch', motion: 'lift', name: 'Palla trattenuta: solleva lentamente l’avambraccio, palmo in alto' },
    double_contact: { pose: 'double', name: 'Doppio tocco: due dita divaricate' },
    net_touch: { pose: 'net', name: 'Tocco della rete: braccio teso dritto verso il segnapunti all’altezza della spalla, mano aperta in prosecuzione del braccio' },
    centre_line: { pose: 'line', name: 'Invasione: indica la linea centrale, senza oscillare il braccio' },
    back_attack: { pose: 'attack', motion: 'spike', name: 'Attacco irregolare: braccio teso in alto che, con un unico movimento, si piega e «cade» verso il lato opposto, finendo sopra la testa col palmo in basso' },
    libero_attack: { pose: 'attack', motion: 'spike', name: 'Attacco irregolare: braccio teso in alto che, con un unico movimento, si piega e «cade» verso il lato opposto, finendo sopra la testa col palmo in basso' },
    libero_set: { pose: 'attack', motion: 'spike', name: 'Attacco irregolare (su palleggio del Libero), poi indica il Libero' },
    attack_serve: { pose: 'attack', motion: 'spike', name: 'Attacco irregolare (sulla palla di servizio): braccio teso in alto che, con un unico movimento, si piega e «cade» verso il lato opposto, finendo sopra la testa col palmo in basso' },
    reach_beyond: { pose: 'reach', name: 'Invasione aerea: avambraccio orizzontale davanti al petto, palmo verso il basso' },
    under_net: { pose: 'line', name: 'Palla nello spazio inferiore: indica lo spazio sotto la rete' },
    antenna: { pose: 'out', name: 'Palla sull’antenna: segnale di palla fuori' },
    block_serve: { pose: 'blockFault', name: 'Fallo di muro (muro sul servizio): entrambe le braccia alzate in verticale, palmi in avanti' },
    back_block: { pose: 'blockFault', name: 'Fallo di muro (difensore a muro): entrambe le braccia alzate in verticale, palmi in avanti' },
  };

  /** Hand on the side of `team`, as seen by the referee of `role` (1st faces the scorer, 2nd faces the 1st). */
  function handFor(team, role) {
    const s = (role === '2' ? -1 : 1) * G.M.side[team];
    return s < 0 ? 'L' : 'R';
  }

  /** Who signals a rally decision: the 2nd referee only when the user is the 2nd and the fault is theirs. */
  function signalRole(key, faultComp) {
    const M = G.M;
    if (M.opts.mode !== '2') return '1';
    const comp = faultComp || E.COMPETENCE[key];
    return comp === '2' || comp === 'both' ? '2' : '1';
  }

  function rallySignal(exp, faultComp, noWhistle) {
    const M = G.M;
    const lbl = T => E.TEAM_DEF[T].label;
    if (exp.key === 'no_fault') return { role: '1', steps: [], note: 'Nessuna segnaletica: non c’era nulla da fischiare.' };
    if (exp.key === 'serve_early') return { role: '1', steps: [
      { pose: 'whistle', whistle: true, caption: 'Fischio: il servizio è annullato', dur: 1.1 },
      ...SimGestures.authorizeSteps(handFor(M.serving, '1'), 'Nuova autorizzazione: '),
    ] };
    if (exp.key === 'double_fault') return { role: '1', steps: [
      { pose: 'whistle', whistle: true, caption: 'Fischio di fine azione', dur: 1.1 },
      { pose: 'doubleFault', caption: 'Doppio fallo: braccia tese verso il segnapunti, pollici alzati all’altezza delle spalle', dur: 1.8 },
      { pose: 'serve', side: handFor(M.serving, '1'), caption: 'Servizio alla squadra che aveva appena servito', dur: 1.6 },
    ] };
    const sig = FAULT_SIGNAL[exp.key];
    if (!sig) return { role: '1', steps: [] };
    const faultTeam = E.other(exp.point);
    const role = signalRole(exp.key, faultComp);
    const steps = [];
    if (!noWhistle) steps.push({ pose: 'whistle', whistle: true, caption: 'Fischio di fine azione', dur: 1.0 });
    if (role === '1') {
      steps.push({ pose: 'serve', side: handFor(exp.point, '1'), caption: 'Servizio alla ' + lbl(exp.point), dur: 1.5 });
      steps.push({ pose: sig.pose, side: handFor(faultTeam, '1'), motion: sig.motion, caption: sig.name, dur: 2.2 });
    } else {
      steps.push({ pose: 'rest', caption: 'Si porta dal lato della squadra in fallo (' + lbl(faultTeam) + ')', dur: 0.9 });
      steps.push({ pose: sig.pose, side: handFor(faultTeam, '2'), motion: sig.motion, caption: sig.name, dur: 2.2 });
      steps.push({ pose: 'rest', caption: 'Attende che il 1° arbitro assegni il servizio', dur: 1.2 });
      steps.push({ pose: 'serve', side: handFor(exp.point, '2'), caption: 'Ripete il gesto del 1° arbitro: servizio alla ' + lbl(exp.point), dur: 1.6 });
    }
    steps.push({ pose: 'rest', caption: 'Posizione di riposo', dur: 0.6 });
    return { role, steps, note: noWhistle ? 'Servizio senza tocchi: nel 2026/27 non si fischia, si esegue solo la segnaletica (Normative federali 4.7).' : null };
  }

  function eventSignal(e, exp) {
    const lbl = E.TEAM_DEF[e.team].label;
    const W = c => ({ pose: 'whistle', whistle: true, caption: c || 'Fischio', dur: 1.0 });
    if (e.kind === 'libero' && exp === 'authorize') return { role: '2', steps: [], note: 'Nessun gesto: i rimpiazzi regolari del Libero non si autorizzano con il fischio.' };
    if (e.kind === 'conduct') {
      const s = handFor(e.team, '1');
      if (exp === 'verbal') return { role: '1', steps: [], note: 'Avvertimento verbale: nessun cartellino, si comunica al capitano in gioco.' };
      if (exp === 'none') return { role: '1', steps: [] };
      const steps = [W('Fischio'), { pose: exp === 'yellow' ? 'yellow' : exp === 'red' ? 'red' : exp === 'expulsion' ? 'expulsion' : 'disqualification', side: s,
        caption: { yellow: 'Cartellino giallo (avvertimento ufficiale) al n. ' + e.who, red: 'Cartellino rosso (penalizzazione) al n. ' + e.who, expulsion: 'Giallo e rosso insieme (espulsione) al n. ' + e.who, disqualification: 'Giallo e rosso separati (squalifica) al n. ' + e.who }[exp], dur: 2.2 }];
      if (exp === 'red') steps.push({ pose: 'serve', side: handFor(E.other(e.team), '1'), caption: 'Servizio alla squadra avversaria', dur: 1.5 });
      return { role: '1', steps };
    }
    if (exp === 'authorize' && e.kind === 'timeout') return { role: '2', steps: [W('Fischio'), { pose: 'timeout', caption: 'Tempo di riposo: una mano verticale col palmo verso l’interno, l’altra sopra col palmo in basso (T)', dur: 1.8 }, { pose: 'serve', side: handFor(e.team, '2'), caption: 'Indica la squadra che lo ha chiesto (' + lbl + ')', dur: 1.4 }] };
    if (exp === 'authorize' && e.kind === 'sub') return { role: '2', steps: [W('Fischio'), { pose: 'sub', motion: 'roll', once: true, caption: 'Sostituzione: pugni chiusi, gli avambracci fanno un giro completo uno intorno all’altro', dur: 2.0 }] };
    if (exp === 'reject_improper') return { role: '2', steps: [W('Fischio'), { pose: 'improper', side: handFor(e.team, '2'), caption: 'Richiesta impropria respinta: braccio teso verso la squadra ma più in basso della spalla, palmo verso l’esterno e dorso verso il campo', dur: 2.0 }] };
    if (exp === 'delay_warning' || exp === 'delay_penalty') {
      const steps = [W('Il 1° arbitro fischia'), { pose: exp === 'delay_warning' ? 'delayY' : 'delayR', side: handFor(e.team, '1'), caption: (exp === 'delay_warning' ? 'Avvertimento per ritardo: cartellino giallo' : 'Penalizzazione per ritardo: cartellino rosso') + ' al polso dal lato della ' + lbl, dur: 2.2 }];
      if (exp === 'delay_penalty') steps.push({ pose: 'serve', side: handFor(E.other(e.team), '1'), caption: 'Servizio alla squadra avversaria', dur: 1.5 });
      return { role: '1', steps };
    }
    return { role: '2', steps: [] };
  }

  /** Which team the referee of `role` sees on the left and on the right (1st: A left; 2nd: the opposite). */
  function sidesFor(role) {
    const leftTeam = G.M.side.A * (role === '2' ? -1 : 1) < 0 ? 'A' : 'B';
    return { left: leftTeam, right: leftTeam === 'A' ? 'B' : 'A' };
  }

  /** Small animated referee in a corner, for routine calls that do not open the feedback. */
  function miniSignal(sig) {
    if (!sig || !sig.steps.length) return;
    const box = $('gesture-mini');
    box.hidden = false;
    SimGestures.play($('gm-fig'), sig.steps, $('gm-cap'), null, sidesFor(sig.role));
    const total = sig.steps.reduce((s, x) => s + (x.dur || 1.6), 0);
    clearTimeout(box._t);
    box._t = setTimeout(() => { box.hidden = true; SimGestures.stop($('gm-fig')); }, total * 1000 + 400);
  }

  // ======================================================================================
  // Feedback
  // ======================================================================================
  let fbContinue = null;
  let fbInfo = null;
  function showFeedback(info, onContinue) {
    G.lastCall = { by: info.by || userRole(), user: !info.by, ok: info.ok, yours: info.yours, right: info.right, happened: info.happened };
    const last = G.decisions[G.decisions.length - 1];
    if (last && !last.signal && info.signal && info.signal.steps.length) {
      last.signal = info.signal;
      last.sides = sidesFor(info.signal.role);
      last.right = info.right;
    }
    // Routine correct calls (clear ball in/out) close quickly, without interrupting the game
    if (info.quiet) {
      banner('Corretto', info.right + '.', 1600);
      miniSignal(info.signal);
      later(onContinue, 900);
      return;
    }
    // Unless the user asked for the summary after every decision, show only their own signal first
    if (!G.M.opts.summary) return showCard(info, onContinue);
    showFullFeedback(info, onContinue);
  }

  // ======================================================================================
  // Replay of the last rally ("registrazione"), in a window of its own
  // ======================================================================================
  function replayInfo(R, fault, missed) {
    if (!R) return null;
    const focus = fault ? fault.t : (R.natural ? R.natural.t : R.endT);
    const start = Math.max(0, Math.min(focus - 3.2, R.tHit - 0.6));
    return { R, focus, from: Math.max(start, focus - 4.5), to: Math.min(R.endT + 0.4, focus + 1.6), missed, label: fault ? 'Momento del fallo' : 'Fine dell’azione' };
  }
  const RP = { raf: 0, playing: true, slow: false, top: false, t: 0, renderer: null, info: null, last: 0 };
  function openReplay(info) {
    if (!info) return;
    RP.info = info; RP.t = info.from; RP.playing = true; RP.last = performance.now(); RP.top = false;
    $('rp-range').min = info.from; $('rp-range').max = info.to; $('rp-range').step = 0.01;
    $('rp-mark').style.left = ((info.focus - info.from) / (info.to - info.from) * 100) + '%';
    $('rp-mark').title = info.label;
    $('replay-title').textContent = info.title || 'Registrazione dell’azione';
    $('rp-note').textContent = info.event ? info.snap.event.text : info.missed ? 'Il fallo non è stato fischiato: guarda il momento evidenziato sulla barra.' : 'Rivedi l’azione: il segno sulla barra indica il momento decisivo.';
    openModal('replay-modal');
    if (!RP.renderer) RP.renderer = new SimRender.Renderer($('rp-canvas'));
    RP.renderer.resize();
    RP.renderer.setView(G.view);
    RP.renderer.setRefX && RP.renderer.setRefX(G.refX || 0);
    replayButtons();
    cancelAnimationFrame(RP.raf);
    RP.raf = requestAnimationFrame(replayTick);
  }
  function replayButtons() {
    $('rp-play').textContent = RP.playing ? '❚❚ Pausa' : '▶ Riproduci';
    $('rp-slow').classList.toggle('active', RP.slow);
    $('rp-top').classList.toggle('active', RP.top);
  }
  function replayTick(now) {
    const info = RP.info;
    if (!info || $('replay-modal').hidden) return;
    const dt = Math.min(0.05, (now - RP.last) / 1000);
    RP.last = now;
    if (RP.playing) {
      // Slower around the decisive moment, and always when "Rallenty" is on
      const near = Math.abs(RP.t - info.focus) < 0.6;
      RP.t += dt * (RP.slow ? 0.3 : near ? 0.5 : 1);
      if (RP.t > info.to) RP.t = info.from;
    }
    $('rp-range').value = RP.t;
    $('rp-badge').hidden = !(Math.abs(RP.t - info.focus) < 0.35);
    $('rp-badge').textContent = info.label;
    draw({ renderer: RP.renderer, R: info.R || null, t: RP.t, snap: info.snap });
    RP.raf = requestAnimationFrame(replayTick);
  }
  function closeReplay() {
    cancelAnimationFrame(RP.raf);
    closeModal('replay-modal');
  }

  /** Plays a signal in one of the signal boxes (prefix: fg = correct, fy = yours, dc = decision card). */
  function fillSignal(pfx, sig, title) {
    const box = $(pfx + '-gesture') || $(pfx + '-sig'), fig = $(pfx + '-fig'), list = $(pfx + '-list');
    const steps = sig ? sig.steps : [];
    if (title && $(pfx + '-title')) $(pfx + '-title').textContent = title;
    if (list) list.innerHTML = steps.filter(s => s.pose !== 'rest' || /Attende|Si porta/.test(s.caption)).map(s => '<li>' + esc(s.caption) + '</li>').join('');
    if (box) box.classList.toggle('nofig', !steps.length);
    $(pfx + '-note').textContent = (sig && sig.note) || '';
    $(pfx + '-note').hidden = !(sig && sig.note);
    fig.hidden = !steps.length;
    if ($(pfx + '-replay')) {
      $(pfx + '-replay').hidden = !steps.length;
      $(pfx + '-replay').onclick = () => SimGestures.play(fig, steps, $(pfx + '-cap'), list, sidesFor(sig.role));
    }
    if (steps.length) SimGestures.play(fig, steps, $(pfx + '-cap'), list, sidesFor(sig.role));
    else { SimGestures.stop(fig); $(pfx + '-cap').textContent = ''; }
  }
  const roleLabel = sig => (sig.role === '2' ? '2° arbitro' : '1° arbitro');

  /** Compact card after a decision: the user's own signal (even if wrong), with "Mostra dettagli". */
  function showCard(info, onContinue) {
    fbInfo = info;
    fbContinue = onContinue;
    const c = $('dcard');
    c.className = 'dcard ' + (info.ok ? 'ok' : 'ko');
    $('dc-icon').textContent = info.ok ? '✓' : '✕';
    $('dc-title').textContent = info.ok ? 'Decisione corretta' : 'Decisione da rivedere';
    $('dc-yours').textContent = info.yours + (info.ok ? '' : ' · corretta: ' + info.right);
    const sig = (info.ok ? info.signal : info.yourSignal) || { role: '1', steps: [] };
    if (!sig.steps.length && !sig.note) sig.note = 'Nessuna segnaletica.';
    $('dc-sig').classList.toggle('ko', !info.ok);
    $('dc-replay').hidden = !info.replay;
    fillSignal('dc', sig);
    c.hidden = false;
    G.modal = 'dcard';
    setTimeout(() => $(info.ok ? 'dc-continue' : 'dc-details').focus(), 50);
    // A correct call goes on by itself once the signal is over
    if (info.ok) {
      const tok = c._tok = {};
      const total = sig.steps.reduce((s, x) => s + (x.dur || 1.6), 0);
      later(() => { if (c._tok === tok && !c.hidden) continueCard(); }, total * 1000 + 1500, { always: true });
    }
  }
  function closeCard() {
    const c = $('dcard');
    c._tok = null;
    c.hidden = true;
    SimGestures.stop($('dc-fig'));
    if (G.modal === 'dcard') G.modal = null;
  }
  function continueCard() {
    closeCard();
    const f = fbContinue;
    fbContinue = null;
    if (f) f();
  }
  function cardDetails() {
    const info = fbInfo, f = fbContinue;
    closeCard();
    showFullFeedback(info, f);
  }

  function showFullFeedback(info, onContinue) {
    fbInfo = info;
    fbContinue = onContinue;
    const sit = SIT[info.sitId] || SIT.wrong_whistle;
    $('fb-head').className = 'fb-head ' + (info.ok ? 'ok' : 'ko');
    $('fb-icon').textContent = info.ok ? '✓' : '✕';
    $('feedback-title').textContent = info.ok ? 'Decisione corretta' : 'Decisione da rivedere';
    let html = '<div class="fb-section"><h5>Che cosa è successo</h5><p>' + esc(info.happened) + '</p></div>' +
      '<div class="fb-compare"><div><small>La tua decisione</small>' + esc(info.yours) + '</div><div><small>Decisione corretta</small>' + esc(info.right) + '</div></div>';
    if (info.note) html += '<div class="fb-note">' + esc(info.note) + '</div>';
    html += '<div class="fb-section"><h5>' + esc(sit.title) + '</h5><p>' + esc(sit.solution) + '</p></div>';
    html += '<div class="fb-section"><h5>Riferimenti</h5><div class="fb-refs">' + sit.refs.map(code => {
      const r = refs[code];
      return '<details class="fb-ref"><summary><span class="code">' + esc(code) + '</span><span>' + esc(r ? r['ref-title'] : '') + '</span></summary>' +
        (r ? '<p>' + esc(r['ref-full-text'] || r['ref-text']) + '</p>' : '') + '</details>';
    }).join('') + '</div></div>';
    $('feedback-body').innerHTML = html;
    // Correct official signals, animated
    const sig = info.signal;
    const g = $('fb-gesture');
    if (sig && (sig.steps.length || sig.note)) {
      g.hidden = false;
      fillSignal('fg', sig, 'Segnaletica corretta · ' + roleLabel(sig) + ' · a specchio');
    } else {
      g.hidden = true;
      SimGestures.stop($('fg-fig'));
    }
    // The signal the user made, when the decision was wrong
    const ys = info.yourSignal;
    const y = $('fy-gesture');
    if (!info.ok && ys && (ys.steps.length || ys.note)) {
      y.hidden = false;
      fillSignal('fy', ys, 'La tua segnaletica · ' + roleLabel(ys));
    } else {
      y.hidden = true;
      SimGestures.stop($('fy-fig'));
    }
    $('fb-replay').hidden = !info.replay;
    openModal('feedback-modal');
    setTimeout(() => $('fb-continue').focus(), 50);
  }

  function continueFeedback() {
    SimGestures.stop($('fg-fig'));
    SimGestures.stop($('fy-fig'));
    closeModal('feedback-modal');
    const f = fbContinue;
    fbContinue = null;
    if (f) f();
  }

  // ======================================================================================
  // Report a situation (goes to the admins' Reports, code SIM-<situation>)
  // ======================================================================================
  function openReport() {
    if (!fbInfo) return;
    $('report-text').value = '';
    openModal('report-modal');
    setTimeout(() => $('report-text').focus(), 50);
  }
  function sendReport() {
    const text = $('report-text').value.trim();
    const M = G.M;
    simData.flagged.push({
      code: 'SIM-' + fbInfo.sitId,
      text,
      date: new Date().toISOString(),
      resolved: false,
      resolvedDate: null,
      context: roleName(M.opts.mode) + ' · ' + M.score.A + '-' + M.score.B + ' · ' + fbInfo.context + ' · Tua decisione: ' + fbInfo.yours + ' · Indicata come corretta: ' + fbInfo.right,
    });
    saveSimData();
    Store.flush && Store.flush();
    closeModal('report-modal');
    toast('Segnalazione inviata: grazie!');
  }

  // ======================================================================================
  // Help
  // ======================================================================================
  function openHelp() {
    const M = G.M;
    let html = '';
    const mode = M.opts.mode;
    const general = {
      '1': ['Prima del servizio: le squadre sono pronte e il giocatore al servizio ha la palla? Allora fischia e autorizza.', 'Al colpo di servizio: piedi del giocatore al servizio e posizioni della squadra al servizio.', 'Durante l’azione: tocchi di palla (trattenuta, doppio, quattro tocchi), rete dal lato dell’attacco, attacchi di difensori e Libero.', 'Alla fine: palla dentro, fuori o toccata dal muro.'],
      '2': ['Prima del servizio: controlla la formazione della squadra in ricezione con il referto.', 'Al colpo di servizio: posizioni della squadra in ricezione (avanti-difensori e ordine nelle linee).', 'Durante l’azione: linea centrale, rete soprattutto dal lato del muro, attacchi di difensori e Libero.', 'Tra le azioni: tempi di riposo, sostituzioni e rimpiazzi del Libero, controllando il referto.'],
      solo: ['Sei solo: svolgi i compiti del 1° e del 2° arbitro.', 'Prima del servizio: squadre pronte, formazioni, poi autorizza.', 'Durante l’azione: tocchi, rete, linea centrale, attacchi irregolari.', 'Tra le azioni: richieste, sostituzioni, Libero e condotta.'],
    }[mode];
    html += '<div class="fb-section"><h5>Il tuo compito come ' + esc(roleName(mode)) + '</h5><ul style="padding-left:18px;line-height:1.55">' + general.map(g => '<li>' + esc(g) + '</li>').join('') + '</ul></div>';
    // What to look at right now
    let sitId = null;
    if (G.phase === 'break' && G.event && G.event.user) { sitId = G.event.sit; G.event.helped = true; }
    else if (G.R && G.R.plan.sit) {
      const s = G.R.plan.sit;
      const f = G.R.faults[0];
      if (!f || M.userDecides(f.comp) || G.R.trap) sitId = s;
      G.helped = true;
    }
    if (sitId && SIT[sitId]) {
      html += '<div class="fb-section"><h5>In questo momento</h5><p>' + esc(SIT[sitId].help) + '</p></div>';
    } else if (G.R) {
      html += '<div class="fb-section"><h5>In questo momento</h5><p>Nulla di particolare: segui l’azione e fischia solo se sei certo di un fallo.</p></div>';
    }
    html += '<p class="form-hint" style="margin-top:12px">Le decisioni prese dopo aver aperto l’aiuto vengono contate a parte nelle statistiche.</p>';
    $('help-body').innerHTML = html;
    openModal('help-modal');
  }

  // ======================================================================================
  // Score sheet
  // ======================================================================================
  function toggleSheet(force) {
    const s = $('sheet');
    const show = force === undefined ? s.hidden : force;
    if (show && !$('log').hidden) toggleLog(false);
    s.hidden = !show;
    $('btn-sheet').classList.toggle('active', show);
    if (show) renderSheet();
  }

  function renderSheet() {
    const M = G.M;
    const roman = ['I', 'II', 'III', 'IV', 'V', 'VI'];
    let html = '';
    ['A', 'B'].forEach(T => {
      const t = M.teams[T], d = t.def;
      // Positions in the classic layout: IV III II / V VI I
      const order = [3, 2, 1, 4, 5, 0];
      html += '<div class="sh-team"><h4><span class="sh-dot" style="background:' + TEAM_COLOR[T] + '"></span>' + esc(d.label) + (M.serving === T ? ' · al servizio' : '') + '</h4>';
      html += '<div class="sh-pos">' + order.map(k => {
        const n = t.lineup[k];
        const lib = t.libero.on === n;
        return '<div class="' + (lib ? 'lib' : '') + '"><small>' + roman[k] + '</small>' + n + (lib ? ' <small>Libero ' + t.libero.num + ' in campo</small>' : '') + '</div>';
      }).join('') + '</div>';
      const line = (a, b) => '<div class="sh-line"><span>' + a + '</span><span>' + b + '</span></div>';
      html += line('A referto', d.roster.join(', ') + ' · Libero ' + d.libero + ' · Capitano ' + d.captain);
      html += line('Formazione iniziale', d.lineup.map((n, i) => roman[i] + ' ' + n).join(' · '));
      html += line('Tempi di riposo', t.timeouts + ' di 2');
      html += line('Sostituzioni', t.subs.count + ' di ' + E.MAX_SUBS + (t.subs.list.length ? ': ' + t.subs.list.join(', ') : ''));
      html += line('Rimpiazzi del Libero', t.libero.log.length ? t.libero.log.slice(-4).join(', ') + (t.libero.lastRally === M.completedRallies ? ' (in questa interruzione)' : '') : '—');
      html += line('Richieste improprie', t.improper ? String(t.improper) : '—');
      html += line('Sanzioni', t.sanctions.length ? t.sanctions.join(', ') : '—');
      html += '</div>';
    });
    $('sheet-body').innerHTML = html;
  }

  // ======================================================================================
  // Modals and pause
  // ======================================================================================
  function openModal(id) { $(id).hidden = false; G.modal = id; }
  function closeModal(id) {
    $(id).hidden = true;
    const still = ['replay-modal', 'decision-modal', 'feedback-modal', 'help-modal', 'pause-modal', 'report-modal'].find(m => !$(m).hidden);
    G.modal = still || (!$('pick-layer').hidden ? 'pick' : !$('dcard').hidden ? 'dcard' : null);
  }
  function closeModals() { cancelAnimationFrame(RP.raf); $('peek-bar').hidden = true; ['replay-modal', 'decision-modal', 'feedback-modal', 'help-modal', 'pause-modal', 'report-modal'].forEach(m => { $(m).hidden = true; }); closeCard(); G.modal = null; }

  function pause() {
    if (G.phase === 'over' || G.phase === 'idle') return;
    if (G.modal === 'pause-modal') return resume();
    if (G.modal) return;
    $('pause-text').textContent = E.TEAM_DEF.A.label + ' ' + G.M.score.A + ' – ' + G.M.score.B + ' ' + E.TEAM_DEF.B.label + '. Puoi riprendere quando vuoi.';
    openModal('pause-modal');
  }
  function resume() { closeModal('pause-modal'); G.lastFrame = performance.now(); }

  // ======================================================================================
  // Drawing: builds the scene for the renderer
  // ======================================================================================
  const BENCH = { A: { x0: -6.2, x1: -2.8, coach: -6.9 }, B: { x0: 2.8, x1: 6.2, coach: 6.9 } };

  function personFor(T, num, p) {
    const d = E.TEAM_DEF[T];
    const isL = num === d.libero;
    return {
      x: p.x, y: p.y, z: p.z || 0, pose: p.pose || 'ready', number: num,
      jersey: isL ? d.liberoJersey : d.jersey, numberColor: isL ? d.liberoNumber : '#fff',
      shorts: d.shorts, trim: isL ? null : d.trim, hair: d.hair, skin: (num * 7) % 4,
      captain: num === d.captain, h: 1.82 + ((num * 13) % 9) / 50,
    };
  }

  /** Draws the scene; with `rp` ({ renderer, R, t }) it draws a moment of a rally in the replay window instead. */
  function draw(rp) {
    const M = G.M, R = rp ? rp.R : G.R;
    const people = [];
    const onCourtKeys = new Set();
    const t = rp ? rp.t : G.t;
    const phase = rp ? (rp.snap ? 'break' : 'rally') : G.phase;
    const event = rp ? (rp.snap ? rp.snap.event : null) : G.event;
    const renderer = rp ? rp.renderer : G.renderer;
    // Who is on court: now, or (replay of a past rally) the players recorded in that rally
    const snap = rp && rp.snap;
    const courtNums = T => snap ? snap.court[T] : rp ? Object.values(R.keys).filter(k => k.team === T).map(k => k.num) : [0, 1, 2, 3, 4, 5].map(k => M.onCourt(T, k));
    const posOf = snap ? snap.pos : M.pos;

    // Players on court
    ['A', 'B'].forEach(T => {
      courtNums(T).forEach(n => {
        const key = T + n;
        onCourtKeys.add(key);
        let p = null;
        if (R && (phase === 'rally' || phase === 'decision') && R.moves[key]) p = M.playerAt(R, key, t);
        else if (R && phase === 'prep' && R.moves[key]) {
          const target = M.playerAt(R, key, 0);
          const from = G.prepFrom[key] || target;
          const u = clamp(G.prepT / 1.25, 0, 1);
          const e = u * u * (3 - 2 * u);
          p = { x: from.x + (target.x - from.x) * e, y: from.y + (target.y - from.y) * e, z: 0, pose: u < 1 ? 'walk' : (R.server.team === T && R.server.num === n ? 'stand' : 'ready') };
        } else {
          const q = posOf[key] || { x: M.side[T] * 4, y: 0 };
          p = { x: q.x, y: q.y, z: 0, pose: 'stand' };
        }
        const person = personFor(T, n, p);
        // Break events: highlight who is involved
        const e = event;
        if (e && e.team === T && (e.out === n || e.who === n || e.swap === n)) person.highlight = 'rgba(255,213,79,.55)';
        if (e && e.kind === 'conduct' && e.team === T && e.who === n) { person.bubble = '!!'; person.bubbleColor = '#ffcdd2'; person.pose = 'wave'; }
        people.push(person);
      });
    });

    // Benches, coaches, Libero off court, players waiting in the substitution zone
    ['A', 'B'].forEach(T => {
      const d = E.TEAM_DEF[T], team = M.teams[T], b = BENCH[T];
      const onCourtNums = courtNums(T);
      let bench = d.roster.filter(n => !onCourtNums.includes(n));
      if (!onCourtNums.includes(d.libero)) bench.push(d.libero);
      const e = event;
      bench = bench.filter(n => !(e && e.team === T && e.inn === n));
      bench.forEach((n, i) => {
        const x = b.x0 + (b.x1 - b.x0) * (i + 0.5) / Math.max(bench.length, 1);
        people.push(personFor(T, n, { x, y: 7.65, pose: 'sit' }));
      });
      const coach = { x: b.coach, y: 6.9, pose: 'stand', jersey: '#3a4252', shorts: '#3a4252', h: 1.8, skin: T === 'A' ? 1 : 2, label: 'All. ' + T };
      if (e && e.team === T && e.kind === 'timeout') { coach.pose = 'gesture-t'; coach.bubble = 'T'; }
      people.push(coach);
      // Incoming player (substitution or Libero) waiting at the side line
      if (e && e.team === T && (e.kind === 'sub' || e.kind === 'libero')) {
        const zx = e.kind === 'sub' ? M.side[T] * 1.6 : M.side[T] * 6.2;
        const pin = personFor(T, e.inn, { x: zx, y: 5.1, pose: 'stand' });
        pin.bubble = e.kind === 'sub' ? '⇄ ' + e.inn + ' per ' + e.out : 'Libero';
        pin.highlight = 'rgba(255,213,79,.55)';
        people.push(pin);
      }
    });

    // Referees and scorer
    const mode = M.opts.mode;
    const firstX = mode === '2' ? G.partnerX : G.refX;
    people.push({ x: firstX, y: -5.6, z: 1.55, pose: 'stand', jersey: '#1b1b1b', shorts: '#1b1b1b', h: 1.8, skin: 0, label: '1° arbitro', hiddenIn: mode === '2' ? null : 'first' });
    if (mode !== 'solo') {
      const sx = mode === '2' ? G.refX : G.partnerX;
      people.push({ x: sx, y: 5.25, pose: 'stand', jersey: '#1b1b1b', shorts: '#1b1b1b', h: 1.78, skin: 1, label: '2° arbitro', hiddenIn: mode === '2' ? 'second' : null });
    }
    people.push({ x: 0, y: 7.55, pose: 'sit', jersey: '#5b6b88', shorts: '#2a3140', h: 1.74, skin: 2, label: 'Segnapunti' });

    // Ball
    let ball = null;
    if (R) {
      const serverKey = R.server.team + R.server.num;
      if (phase === 'prep' || ((phase === 'rally' || phase === 'decision') && t < R.ball[0].t0)) {
        const sp = phase === 'prep'
          ? (() => { const tg = M.playerAt(R, serverKey, 0), fr = G.prepFrom[serverKey] || tg, u = clamp(G.prepT / 1.25, 0, 1); return { x: fr.x + (tg.x - fr.x) * u, y: fr.y + (tg.y - fr.y) * u }; })()
          : M.playerAt(R, serverKey, t);
        const bounceT = phase === 'prep' ? G.prepT : t;
        const dribble = Math.abs(Math.sin(bounceT * 3.2));
        ball = { x: sp.x - M.side[R.server.team] * 0.25, y: sp.y + 0.25, z: 0.15 + dribble * 0.9, spin: bounceT * 4 };
      } else {
        const b = M.ballAt(R, t);
        if (b) ball = b;
      }
      if (ball && (rp ? Math.abs(t - RP.info.focus) < 0.35 : slowNow())) ball.highlight = '#ffd54f';
    }

    // Net shake and impact mark (visual aids)
    let netShake = null;
    const shake = rp ? (() => { if (!R) return null; const n = R.events.filter(ev => ev.kind === 'net' && ev.t <= t).pop(); return n ? { y: n.y, t0: n.t, amp: n.strong ? 0.09 : 0.05 } : null; })() : G.netShake;
    if (shake && R) {
      const age = t - shake.t0;
      if (age < 0.9) netShake = { y: shake.y, amp: shake.amp * Math.exp(-age * 4), phase: age * 38 };
    }
    const markers = [];
    if (R && M.opts.aids && R.natural && R.natural.land && R.natural.borderline && t >= R.natural.t) {
      markers.push({ x: R.natural.land[0], y: R.natural.land[1], r: E.BALL_R, color: '#ffd54f', fill: 'rgba(255,213,79,.35)' });
    }

    // The referee turns their head to follow the play (the server before the service, then the ball)
    if (rp ? !RP.top : !G.top) {
      let lx = 0;
      if (ball) lx = ball.x * 0.4;
      else if (R) lx = M.side[R.server.team] * 3.2;
      const dt = Math.min(0.05, (performance.now() - ((rp ? RP.lastLook : G.lastLook) || performance.now())) / 1000);
      if (rp) RP.lastLook = performance.now(); else G.lastLook = performance.now();
      renderer.lookAt(clamp(lx, -3.6, 3.6), rp ? dt : (G.paused || G.modal ? 0 : dt));
    }
    renderer.render({ people, ball, netHeight: E.NET_H, netShake, markers });
  }

  // ======================================================================================
  // Controls
  // ======================================================================================
  let keysDown = {}, padDir = 0;
  function updateMoveDir() {
    G.moveDir = padDir || ((keysDown.ArrowRight ? 1 : 0) - (keysDown.ArrowLeft ? 1 : 0));
  }

  function bindControls() {
    $('btn-whistle').addEventListener('click', userWhistle);
    $('btn-authorize').addEventListener('click', authorize);
    $('btn-pause').addEventListener('click', pause);
    $('pause-resume').addEventListener('click', resume);
    $('pause-end').addEventListener('click', () => { closeModals(); endMatch(true); });
    $('btn-view').addEventListener('click', () => { G.top = !G.top; applyView(); });
    $('btn-sheet').addEventListener('click', () => toggleSheet());
    $('sheet-close').addEventListener('click', () => toggleSheet(false));
    $('btn-log').addEventListener('click', () => toggleLog());
    $('peek-back').addEventListener('click', endPeek);
    // "Rivedi" on any rally of the log (drawer or end-of-set report)
    document.addEventListener('click', e => {
      const b = e.target.closest('.lg-replay');
      if (!b) return;
      const l = G.log[+b.dataset.log];
      if (l && l.replay) openReplay(Object.assign({}, l.replay, { title: 'Azione ' + (+b.dataset.log + 1) + ' · ' + l.outcome }));
    });
    $('peek-view').addEventListener('click', () => { G.top = !G.top; applyView(); $('peek-view').textContent = G.top ? 'Vista dal tuo posto' : 'Vista dall’alto'; });
    $('log-close').addEventListener('click', () => toggleLog(false));
    $('btn-help').addEventListener('click', () => { if (!G.modal) openHelp(); });
    $('help-close').addEventListener('click', () => { closeModal('help-modal'); G.lastFrame = performance.now(); });
    $('fb-continue').addEventListener('click', continueFeedback);
    $('fb-report').addEventListener('click', openReport);
    $('dc-continue').addEventListener('click', continueCard);
    $('dc-details').addEventListener('click', cardDetails);
    $('dc-replay').addEventListener('click', () => { $('dcard')._tok = null; openReplay(fbInfo && fbInfo.replay); });
    $('fb-replay').addEventListener('click', () => openReplay(fbInfo && fbInfo.replay));
    $('rp-close').addEventListener('click', closeReplay);
    $('rp-play').addEventListener('click', () => { RP.playing = !RP.playing; replayButtons(); });
    $('rp-slow').addEventListener('click', () => { RP.slow = !RP.slow; replayButtons(); });
    $('rp-top').addEventListener('click', () => { RP.top = !RP.top; RP.renderer.setView(RP.top ? 'top' : G.view); replayButtons(); });
    $('rp-range').addEventListener('input', e => { RP.t = parseFloat(e.target.value); RP.playing = false; replayButtons(); });
    $('report-cancel').addEventListener('click', () => closeModal('report-modal'));
    $('report-send').addEventListener('click', sendReport);
    $('sim-exit').addEventListener('click', e => {
      if (G.phase !== 'idle' && G.phase !== 'over' && G.decisions.length && !confirm('Uscire dalla partita? Le decisioni prese finora restano nelle statistiche.')) e.preventDefault();
    });
    window.addEventListener('resize', () => { if (G.renderer) { G.renderer.resize(); applyView(); } });
    document.addEventListener('keydown', e => {
      if (e.target.closest('input, textarea, select')) return;
      if ($('sim-game').hidden) return;
      const k = e.key.toLowerCase();
      if (G.modal === 'pick' && pick) {
        if (k === 'a' || k === 'b') { e.preventDefault(); const p = pick; closePicker(); p.onPick(k.toUpperCase()); }
        else if (e.key === 'Escape') { const p = pick; closePicker(); p.onBack(); }
        return;
      }
      if (e.key === ' ' && !G.modal) { e.preventDefault(); userWhistle(); }
      else if (e.key === 'Enter' && !G.modal) { e.preventDefault(); authorize(); }
      else if (e.key === 'Enter' && G.modal === 'feedback-modal') { e.preventDefault(); continueFeedback(); }
      else if (e.key === 'Enter' && G.modal === 'dcard' && !e.target.closest('#dc-details')) { e.preventDefault(); continueCard(); }
      else if (k === 'd' && G.modal === 'dcard') { e.preventDefault(); cardDetails(); }
      else if (e.key === 'Escape' && G.modal === 'help-modal') closeModal('help-modal');
      else if (e.key === 'Escape' && G.modal === 'replay-modal') closeReplay();
      else if ((e.key === 'Escape' || e.key === 'Enter') && !$('peek-bar').hidden) { e.preventDefault(); endPeek(); }
      else if (e.key === ' ' && G.modal === 'replay-modal') { e.preventDefault(); RP.playing = !RP.playing; replayButtons(); }
      else if (e.key === 'Escape' && G.modal === 'pause-modal') resume();
      else if (k === 'p') pause();
      else if (k === 'v' && !G.modal) { G.top = !G.top; applyView(); }
      else if (k === 'r') toggleSheet();
      else if (k === 'c') toggleLog();
      else if (k === 'h' && !G.modal) openHelp();
      else if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') { e.preventDefault(); keysDown[e.key] = true; updateMoveDir(); }
    });
    document.addEventListener('keyup', e => {
      if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') { keysDown[e.key] = false; updateMoveDir(); }
    });
    window.addEventListener('blur', () => { keysDown = {}; padDir = 0; updateMoveDir(); });
    // On-screen controller (phones and tablets): hold to move
    document.querySelectorAll('[data-move]').forEach(btn => {
      const dir = parseInt(btn.dataset.move, 10);
      const start = e => { e.preventDefault(); padDir = dir; btn.classList.add('active'); updateMoveDir(); try { btn.setPointerCapture(e.pointerId); } catch (x) {} };
      const stop = () => { if (padDir === dir) padDir = 0; btn.classList.remove('active'); updateMoveDir(); };
      btn.addEventListener('pointerdown', start);
      btn.addEventListener('pointerup', stop);
      btn.addEventListener('pointercancel', stop);
      btn.addEventListener('lostpointercapture', stop);
      btn.addEventListener('contextmenu', e => e.preventDefault());
    });
    // Pause automatically when the page is hidden
    document.addEventListener('visibilitychange', () => { if (document.hidden && G.phase !== 'idle' && G.phase !== 'over' && !G.modal) pause(); });
  }

  // ======================================================================================
  // Startup
  // ======================================================================================
  Api.onUnauthenticated(() => { window.location.href = './'; });
  (async () => {
    try {
      const me = await Api.me();
      if (!me.user || !me.user.canUse || me.user.mustChangePassword) { window.location.replace('./'); return; }
      if ((me.user.permissions || 0) < SIM_MIN_LEVEL) {
        $('boot-message').textContent = 'La simulazione partita è in prova ed è disponibile solo per gli amministratori.';
        return;
      }
      const [content, state] = await Promise.all([Api.get('content'), Api.get('state.get')]);
      (content.refs || []).forEach(r => { refs[r['ref-code']] = r; });
      Store.load(state.state || {});
      simData = loadSimData();
      $('boot-screen').hidden = true;
      $('sim-root').hidden = false;
      initSetup();
      bindControls();
      requestAnimationFrame(frame);
      // Exposed for automated tests
      window.SimDebug = { G, startMatch, userWhistle, authorize, evaluateRally, endMatch, updateScore };
    } catch (e) {
      $('boot-message').textContent = e.message || 'Errore durante il caricamento.';
    }
  })();
})();
