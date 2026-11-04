/* Fischietto · Match simulation: page logic (setup, game loop, controls, decisions, feedback, statistics). */
(function () {
  'use strict';

  const $ = id => document.getElementById(id);
  const E = window.SimEngine;
  const SIT = window.SimSituations.S;
  const CATS = window.SimSituations.CATS;
  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));

  /** Minimum permission level to use the simulation (beta, open to every user). */
  const SIM_MIN_LEVEL = 1;
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
  function saveSimData() {
    // Category names travel with the data, so the Statistiche page of the portal can show them
    simData.catLabels = CATS;
    try { Store.setItem(STORE_KEY, JSON.stringify(simData)); } catch (e) {}
  }

  function record(sitId, ok, helped, warn) {
    const sit = SIT[sitId] || SIT.wrong_whistle;
    const c = simData.byCat[sit.cat] || (simData.byCat[sit.cat] = { c: 0, w: 0, h: 0 });
    const s = simData.bySit[sitId] || (simData.bySit[sitId] = { c: 0, w: 0 });
    if (ok) { c.c++; s.c++; } else { c.w++; s.w++; }
    if (helped) c.h++;
    G.decisions.push({ sit: sitId, ok, helped: !!helped, warn: !!warn });
    if (G.M) G.M.adaptConduct(ok);
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
  const MOVE = { '1': { max: 0.3, speed: 0.45 }, '2': { max: 2.0, speed: 1.4 } };

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

  /**
   * "All in order": after time-outs and substitutions, and before the start of the set, the scorer and the
   * 2nd referee (if present) raise both arms (same signal as the block fault) to tell the 1st that play can go on.
   */
  const ALL_OK = [{ pose: 'blockFault', caption: 'Tutto in ordine: entrambe le braccia alzate, palmi in avanti', dur: 1.8 }];
  function allOk() {
    const mode = G.M.opts.mode;
    G.allOk = { t0: performance.now() };
    if (mode === '2' && G.M.opts.allOkAuto === false) {
      // You make the signal yourself: the 1st referee waits for it before authorising the service
      G.waitAllOk = true;
      $('btn-allok').hidden = false;
      $('btn-allok').classList.add('pulse');
      hint('Segnapunti pronto: fai il segnale di «tutto in ordine» al 1° arbitro');
      return;
    }
    if (mode !== 'solo') miniSignal({ role: '2', steps: ALL_OK }, mode === '2' ? 'Tu (2° arbitro) e il segnapunti · tutto in ordine' : '2° arbitro e segnapunti · tutto in ordine');
  }

  function userAllOk() {
    if (!G.waitAllOk) return;
    G.waitAllOk = false;
    $('btn-allok').hidden = true;
    $('btn-allok').classList.remove('pulse');
    G.prepT = Math.min(G.prepT, 1.4);
    miniSignal({ role: '2', steps: ALL_OK }, 'Tu (2° arbitro) · tutto in ordine');
  }

  /** During a time-out (after the signal), the 2nd referee waits at the net post, facing the scorer. */
  function timeoutStill() {
    return !!G.huddle && G.phase === 'break' && !(G.partnerNudge && performance.now() < G.partnerNudge.until) && !(G.selfNudge && performance.now() < G.selfNudge.until);
  }

  function hold2() { return G.r2HoldUntil != null && (G.clock || 0) < G.r2HoldUntil; }

  /** Substitutions: the 2nd referee stands with the back to the net post, just on the side of that team. */
  function postSide(T) { return clamp(G.M.side[T] * 0.35, -MOVE['2'].max, MOVE['2'].max); }

  /** Where the 2nd referee stands to signal towards a team: on that team's side of the net. */
  function sideOfTeam2(T) { return clamp(G.M.side[T] * 1.6, -MOVE['2'].max, MOVE['2'].max); }

  function updateMovement(dt) {
    const M = G.M;
    if (!M || G.phase === 'idle' || G.phase === 'over') return;
    // The rally is over: the 2nd referee checks that the players at the net have landed in balance before
    // moving to the side of the next receiving team (otherwise some faults could not be seen)
    if (G.whistled && !G.r2HoldSet && (G.phase === 'rally' || G.phase === 'decision')) { G.r2HoldSet = true; G.r2HoldUntil = (G.clock || 0) + 1.6; }
    const myRole = M.opts.mode === '2' ? '2' : '1';
    const mv = MOVE[myRole];
    let target = G.refTarget === undefined ? 0 : G.refTarget;
    if (G.moveDir) {
      // Manual: arrows / on-screen controller. In the 2nd referee's view "right" on screen is towards x < 0
      const screenSign = G.view === 'second' ? -1 : 1;
      target = clamp(G.refX + G.moveDir * screenSign * mv.speed * 0.5, -mv.max, mv.max);
      G.manualUntil = performance.now() + 1800;
    } else if (myRole === '2' && hold2()) {
      // End of the rally: the 2nd stays where he is until all players have landed in balance
      target = G.refX;
    } else if (myRole === '2' && timeoutStill()) {
      // Time-out: the 2nd referee stands still with the back to the net post, facing the scorer
      target = 0;
    } else if (G.selfNudge && performance.now() < G.selfNudge.until) {
      // After your own whistle as 2nd referee: you move to the side of the team at fault, then signal
      target = G.selfNudge.x;
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
      let pt = autoRefX(pr);
      // A small step towards the team at fault, while signalling to the 1st referee
      if (G.partnerNudge && performance.now() < G.partnerNudge.until) pt = G.partnerNudge.x;
      else if (pr === '2' && hold2()) pt = G.partnerX;
      else if (pr === '2' && timeoutStill()) pt = 0;
      const ps = MOVE[pr].speed * dt;
      G.partnerX += clamp(pt - G.partnerX, -ps, ps);
    }
    recordRefs();
  }

  /**
   * Where the two referees were during each rally (x along the court by rally time: x1 the 1st, x2 the 2nd), so its
   * replay shows them there and not where they are now (after a side-out the 2nd is already on the other side).
   * Kept aside, not in the rally: a reported rally (rebuilt from the report) gets the automatic positions instead.
   */
  const REFLOG = new WeakMap();
  function recordRefs() {
    const M = G.M, R = G.R;
    if (!M || !R) return;
    const m = M.opts.mode, x1 = m === '2' ? G.partnerX : G.refX, x2 = m === '2' ? G.refX : G.partnerX;
    let log = REFLOG.get(R);
    if (!log) REFLOG.set(R, log = []);
    if (G.phase === 'prep') log[0] = { t: 0, x1, x2 }; // where they are at the service
    else if ((G.phase === 'rally' || G.phase === 'decision') && (!log.length || G.t - log[log.length - 1].t >= 0.05)) log.push({ t: G.t, x1, x2 });
  }
  /** The automatic referees' movement through a rally (as autoRefX): at the service the 2nd on the receiving team's side. */
  function simRefs(R, whistleT) {
    const M = G.M, out = [], dt = 0.05, rc = R.receiving;
    const m1 = MOVE['1'], m2 = MOVE['2'], stop = whistleT != null ? whistleT : R.endT;
    let x1 = M.side[R.serving] * 0.4 * m1.max * 0.85, x2 = M.side[rc] * m2.max * 0.75;
    for (let t = 0; t <= (R.endT || 0) + 4; t += dt) {
      out.push({ t, x1, x2 });
      const b = M.ballAt(R, t), bx = b ? b.x : M.side[R.serving];
      const s2 = t < R.tHit + 0.3 ? M.side[rc] : -(Math.sign(bx) || 1);
      x1 += clamp((Math.sign(bx) || 0) * m1.max * 0.85 - x1, -m1.speed * dt, m1.speed * dt);
      // After the whistle the 2nd stays where he is until the players at the net have landed
      if (t < stop) x2 += clamp(s2 * m2.max * 0.75 - x2, -m2.speed * dt, m2.speed * dt);
    }
    return out;
  }
  /** The referees' positions at time t of a rally's replay ({ x1, x2 }), or null when they are not known. */
  function refsAt(R, t, whistleT) {
    if (!R || !R.ball || !R.ball.length) return null;
    let log = REFLOG.get(R);
    if (!log || !log.length) REFLOG.set(R, log = simRefs(R, whistleT));
    let i = 0;
    while (i < log.length - 1 && log[i + 1].t <= t) i++;
    const a = log[i], b = log[Math.min(i + 1, log.length - 1)];
    const u = b.t > a.t ? clamp((t - a.t) / (b.t - a.t), 0, 1) : 0;
    return { x1: a.x1 + (b.x1 - a.x1) * u, x2: a.x2 + (b.x2 - a.x2) * u };
  }

  function roleName(m) { return m === '1' ? '1° arbitro' : m === '2' ? '2° arbitro' : 'Arbitro unico'; }

  // ======================================================================================
  // Setup screen
  // ======================================================================================
  function initSetup() {
    // Portrait phone tip ("turn the phone"): dismissed once, remembered on this browser
    try { if (localStorage.getItem('fipav-sim-rot') === '1') document.body.classList.add('rot-dismissed'); } catch (e) {}
    const rot = $('rot-dismiss');
    if (rot) rot.addEventListener('click', () => {
      document.body.classList.add('rot-dismissed');
      try { localStorage.setItem('fipav-sim-rot', '1'); } catch (e) {}
    });
    const grid = $('role-grid');
    grid.querySelectorAll('.role-card').forEach(b => b.addEventListener('click', () => {
      grid.querySelectorAll('.role-card').forEach(x => { x.classList.toggle('active', x === b); x.setAttribute('aria-checked', x === b ? 'true' : 'false'); });
      $('opt-libero-row').hidden = b.dataset.mode === '1';
      $('opt-allok-row').hidden = b.dataset.mode !== '2';
    }));
    $('opt-libero-row').hidden = true;
    $('opt-allok-row').hidden = true;
    ['opt-target', 'opt-freq', 'opt-fouls', 'opt-forms', 'opt-conduct', 'opt-score', 'opt-anim', 'opt-graphics'].forEach(id => $(id).querySelectorAll('button').forEach(b => b.addEventListener('click', () => {
      $(id).querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b));
    })));
    $('btn-start').addEventListener('click', () => {
      const mode = grid.querySelector('.role-card.active').dataset.mode;
      startMatch(Object.assign({ mode }, readOptions()));
    });
    renderStats();
  }

  // ---------- Starting line-up slip (1st referee alone, or 2nd referee) ----------
  /** The slip handed in by the coach before the set: the players in positions I–VI (Rule 7.3, scorer's manual). */
  function openSlip(T) {
    const c = $('slip-card');
    if (!c.hidden && c.dataset.team === T) { c.hidden = true; return; }
    const d = E.TEAM_DEF[T], L = d.lineup;
    const R = ['I', 'II', 'III', 'IV', 'V', 'VI'];
    c.dataset.team = T;
    c.style.setProperty('--team', TEAM_COLOR[T]);
    $('slip-title').textContent = d.label + ' · 1° set';
    $('slip-row').innerHTML = L.map((n, i) => '<div class="slip-box"><small>' + R[i] + '</small><b>' + n + '</b></div>').join('');
    // On court: front row IV III II at the net, back row V VI I
    $('slip-court').innerHTML = [3, 2, 1, 4, 5, 0].map(i => '<div class="slip-pos"><small>' + R[i] + '</small><b>' + L[i] + '</b></div>').join('');
    c.hidden = false;
  }

  /** The options of the setup screen (all but the role). */
  function readOptions() {
    return {
        target: parseInt($('opt-target').querySelector('.active').dataset.v, 10),
        freq: parseFloat($('opt-freq').querySelector('.active').dataset.v),
        fouls: parseFloat($('opt-fouls').querySelector('.active').dataset.v),
        formations: $('opt-forms').querySelector('.active').dataset.v,
        conduct: (v => v === 'auto' ? 'auto' : parseFloat(v))($('opt-conduct').querySelector('.active').dataset.v),
        aids: $('opt-aids').checked,
        libero: $('opt-libero').checked,
        autoMove: $('opt-automove').checked,
        waitTimeout: $('opt-timeout').checked,
        scoreMine: $('opt-score').querySelector('.active').dataset.v === 'mine',
        subAnim: $('opt-subanim').checked,
        summary: $('opt-summary').checked,
        allOkAuto: $('opt-allok').checked,
        animSpeed: parseFloat($('opt-anim').querySelector('.active').dataset.v),
        graphics: $('opt-graphics').querySelector('.active').dataset.v,
    };
  }

  /** Puts the controls back to the values in use (when the settings are opened during the match). */
  function writeOptions(o) {
    const seg = (id, v) => $(id).querySelectorAll('button').forEach(b => b.classList.toggle('active', b.dataset.v === String(v)));
    seg('opt-target', o.target); seg('opt-freq', o.freq); seg('opt-fouls', o.fouls); seg('opt-forms', o.formations || 'easy');
    seg('opt-conduct', o.conduct); seg('opt-score', o.scoreMine ? 'mine' : 'right');
    seg('opt-anim', o.animSpeed || 1); seg('opt-graphics', o.graphics || 'high');
    $('opt-aids').checked = !!o.aids; $('opt-libero').checked = !!o.libero; $('opt-automove').checked = !!o.autoMove;
    $('opt-timeout').checked = !!o.waitTimeout; $('opt-subanim').checked = !!o.subAnim; $('opt-summary').checked = !!o.summary;
    $('opt-allok').checked = o.allOkAuto !== false;
  }

  // ---------- Settings during the match: the same controls, moved into a window ----------
  let optsHome = null;
  function openOptions() {
    if (!G.M || G.phase === 'over' || G.phase === 'idle') return;
    if (G.modal && G.modal !== 'pause-modal') return;
    if (G.modal === 'pause-modal') closeModal('pause-modal');
    const box = document.querySelector('.sim-options');
    optsHome = { parent: box.parentNode, next: box.nextSibling };
    writeOptions(G.M.opts);
    $('opt-libero-row').hidden = G.M.opts.mode === '1';
    $('opt-allok-row').hidden = G.M.opts.mode !== '2';
    // A set cannot be shortened below the points already played
    const top = Math.max(G.M.score.A, G.M.score.B);
    $('opt-target').querySelectorAll('button').forEach(b => { b.disabled = parseInt(b.dataset.v, 10) <= top; });
    $('opts-body').appendChild(box);
    openModal('opts-modal');
  }
  function closeOptions(apply) {
    const box = document.querySelector('.sim-options');
    if (apply && G.M) {
      const o = readOptions();
      Object.assign(G.M.opts, o);
      applyGraphics();
      banner('Impostazioni aggiornate', 'Valgono dalla prossima azione.', 1800);
      updateScore();
    }
    $('opt-target').querySelectorAll('button').forEach(b => { b.disabled = false; });
    if (optsHome) optsHome.parent.insertBefore(box, optsHome.next);
    optsHome = null;
    closeModal('opts-modal');
    G.lastFrame = performance.now();
  }

  /** The statistics are on the Statistiche page of the portal: here only a link, once there is something to see. */
  function renderStats() {
    $('sim-stats-card').hidden = !Object.keys(simData.byCat).length;
  }

  // ======================================================================================
  // Match lifecycle
  // ======================================================================================
  function startMatch(opts) {
    G.M = new E.Match(opts);
    G.decisions = [];
    G.log = [];
    G.clock = 0;
    G.toWait = null;
    G.lastCall = null;
    G.refX = 0; G.partnerX = 0; G.moveDir = 0; G.manualUntil = 0; G.refTarget = 0; G.timers = [];
    G.view = opts.mode === '2' ? 'second' : 'first';
    G.top = false;
    $('sim-setup').hidden = true;
    $('sim-end').hidden = true;
    $('sim-game').hidden = false;
    $('sim-top-actions').hidden = false;
    // The starting line-up slips can be checked when there is no 2nd referee to do it, or when you are the 2nd
    $('slip-btn-a').hidden = $('slip-btn-b').hidden = opts.mode === '1';
    $('slip-card').hidden = true;
    G.needAllOk = true;   // before the first service: scorer and 2nd referee signal that all is in order
    G.waitAllOk = false; $('btn-allok').hidden = true;
    $('badge-role').textContent = roleName(opts.mode);
    $('sb-set').textContent = 'Set a ' + opts.target;
    if (!G.renderer) G.renderer = new SimRender.Renderer($('sim-canvas'));
    applyGraphics();
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
    const lvl = v => v === 0 ? 'nessuna' : v < 0.7 ? 'bassa' : v < 1.3 ? 'media' : 'alta';
    html += '<p class="form-hint" style="margin-top:10px">Fallosità dei giocatori: ' + lvl(M.foulLevel()) + ' · Condotta scorretta: ' +
      (M.opts.conduct === 'auto' ? 'automatica, a fine set ' : '') + lvl(M.conductLevel()) + '.</p>';
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
        '<div class="end-sig' + (d.warn ? ' warn' : d.ok ? '' : ' ko') + '"><button class="end-sig-head" data-i="' + i + '" aria-expanded="false">' +
          '<span class="end-sig-mark">' + (d.warn ? '!' : d.ok ? '✓' : '✕') + '</span><span>' + esc((SIT[d.sit] || SIT.wrong_whistle).title) +
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
  /** Level of detail of the drawing (option «Grafica»), for the game and for the replays. */
  function applyGraphics() {
    const q = (G.M && G.M.opts.graphics) || 'high';
    [G.renderer, RP.renderer].forEach(r => { if (r) r.setQuality(q); });
  }
  /** Speed of the players' animations between rallies (option «Velocità delle animazioni»): durations are divided by it. */
  function animK() { return (G.M && G.M.opts.animSpeed) || 1; }

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
      by: c.by || (x.kind === 'rally' ? '1° arbitro' : '2° arbitro'), user: !!c.user, ok: c.ok, warn: !!c.warn, yours: c.yours, happened: c.happened || '',
      replay: x.replay || null,
    });
    if (!$('log').hidden) renderLog();
  }
  function logHtml(list) {
    if (!list.length) return '<p class="form-hint">Ancora nessuna azione.</p>';
    return list.slice().reverse().map(l => {
      const cls = l.user ? (l.warn ? 'warn' : l.ok ? 'ok' : 'ko') : 'ai';
      const n = G.log.indexOf(l) + 1;
      return '<div class="lg-item ' + cls + '">' +
        '<div class="lg-top"><span class="lg-n">' + n + '. ' + esc(LOG_KIND[l.kind] || 'Evento') + '</span><span class="lg-score">' + esc(l.before === l.after ? l.after : l.before + ' → ' + l.after) + '</span></div>' +
        '<div class="lg-what">' + esc(l.outcome) + '</div>' +
        (l.happened ? '<div class="lg-hap">' + esc(l.happened) + '</div>' : '') +
        '<div class="lg-by"><span class="lg-badge">' + esc(l.by) + (l.user ? ' · tu' : '') + '</span>' +
          (l.user ? (l.warn ? '<span class="lg-res warn">! punto giusto, fallo diverso: ' + esc(l.yours || '') + '</span>' : l.ok ? '<span class="lg-res ok">✓ corretta</span>' : '<span class="lg-res ko">✕ tua decisione: ' + esc(l.yours || '') + '</span>') : '') +
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
    // The starting line-up slips can be checked from the decision window too (1st referee alone, 2nd referee)
    const row = $('dec-log').parentNode;
    if (G.M && G.M.opts.mode !== '1' && row && !row.querySelector('[data-slip]')) {
      ['A', 'B'].forEach(T => {
        const b = document.createElement('button');
        b.className = 'btn btn-ghost btn-sm'; b.dataset.slip = T; b.textContent = 'Formazione ' + T;
        b.addEventListener('click', () => openSlip(T));
        row.appendChild(b);
      });
    }
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
    // Irregular requests are spotted by the scorer (checks on the score sheet), who tells the 2nd referee
    if ((e.kind === 'sub' || e.kind === 'timeout' || (e.kind === 'libero' && e.user)) && e.expected && e.expected !== 'authorize' && !e.scorer) {
      e.scorer = true;
      e.text += ' Il segnapunti avvisa a voce il 2° arbitro: dal referto la richiesta risulta irregolare.';
      e.scorerSays = 'ko';
      banner('Segnapunti', 'Avvisa il 2° arbitro: richiesta irregolare della ' + E.TEAM_DEF[e.team].label + '.', 2200);
    } else if ((e.kind === 'sub' || e.kind === 'timeout' || (e.kind === 'libero' && e.user)) && !e.scorer) {
      // Regular requests: the scorer checks the score sheet and confirms that everything is in order
      e.scorer = true;
      e.scorerSays = 'ok';
      e.text += ' Il segnapunti controlla il referto: la richiesta è regolare.';
      banner('Segnapunti', 'Dal referto è tutto regolare: ' + ({ sub: 'sostituzione', timeout: 'tempo di riposo', libero: 'rimpiazzo del Libero' })[e.kind] + ' della ' + E.TEAM_DEF[e.team].label + '.', 2200);
    }
    if (e.user && (e.kind === 'conduct' || e.kind === 'sub' || e.kind === 'timeout')) {
      // Misconduct and requests are whistled by the referee first (then the decision, with its signal)
      G.pendingConduct = e;
      hint(e.kind === 'conduct' ? 'Comportamento scorretto: fischia e sanziona' : e.kind === 'sub' ? 'Richiesta di sostituzione: fischia e decidi' : 'Richiesta di tempo di riposo: fischia e decidi');
      $('btn-whistle').classList.add('pulse');
      later(() => { if (G.pendingConduct === e && !G.modal) { e.lateWhistle = true; whistle(); openPendingEvent(e); } }, 7000);
    } else if (e.user) {
      hint('Decidi sulla richiesta');
      later(() => openEventDecision(e), e.kind === 'libero' ? 700 : 500);
    } else {
      // Handled by the automatic referee
      later(() => aiHandleEvent(e), e.kind === 'libero' && e.regular ? 300 : 900);
    }
  }

  const isDelay = k => k === 'delay_warning' || k === 'delay_penalty';
  /** Why a request was rejected (or a Libero replacement is irregular): from the situation's explanation. */
  function reasonOf(e) { const sit = SIT[e.sit]; return sit ? sit.solution : ''; }
  /** The 2nd referee's signal on a request: authorise (time-out / substitution) or reject. */
  function secondSignal(e, k) {
    const W = c => ({ pose: 'whistle', whistle: true, caption: c || 'Fischio', dur: 1.0 });
    if (k === 'authorize') return eventSignal(e, 'authorize');
    if (e.kind === 'libero') return { role: '2', steps: [W('Fischio: rimpiazzo irregolare, va corretto')], note: null };
    return eventSignal(e, 'reject_improper');
  }
  /** A request or misconduct handled by the automatic referee: motivated, with the official signal. */
  function aiHandleEvent(e) {
    const M = G.M, mode = M.opts.mode;
    const exp = e.expected || (e.regular ? 'authorize' : null);
    const who = e.comp === '1' ? '1° arbitro' : '2° arbitro';
    // With you as 1st referee: the 2nd rejects the request, then the delay sanction is yours
    if (mode === '1' && e.comp === '2' && isDelay(exp)) {
      whistle();
      banner('2° arbitro · fischio', (e.kind === 'libero' ? 'Rimpiazzo del Libero irregolare' : 'Richiesta respinta') + ': ' + reasonOf(e), 3400);
      miniSignal(secondSignal(e, 'reject'), '2° arbitro');
      later(() => openDelayDecision(e), 2200);
      return;
    }
    const quiet = e.kind === 'libero' && e.regular;
    let txt = aiEventText(e, exp);
    if (txt && exp !== 'authorize' && e.kind !== 'conduct') txt += ' ' + reasonOf(e);
    G.lastCall = { by: who, user: false, right: aiEventText(e, exp), happened: e.text, quiet };
    if (txt) banner(who, txt, 3000);
    const sig = e.comp === '2' ? (exp === 'authorize' ? secondSignal(e, 'authorize') : secondSignal(e, 'reject')) : eventSignal(e, exp);
    // The 2nd referee first moves to the side of the team that made the request, then makes the signal
    // (substitution: back to the net post, facing the team that asked for it; time-out: on that team's side)
    if (e.comp === '2' && mode === '1' && (e.kind === 'sub' || e.kind === 'timeout')) G.partnerNudge = { x: e.kind === 'sub' ? postSide(e.team) : clamp(M.side[e.team] * 1.6, -MOVE['2'].max, MOVE['2'].max), until: performance.now() + 4500 };
    if (!quiet) miniSignal(sig, who);
    const dur = sig && sig.steps ? sig.steps.reduce((a, x) => a + (x.dur || 1.6), 0) * 1000 : 0;
    finishEvent(e, txt ? Math.max(1900, dur * 0.85) : 500);
  }
  /** You are the 1st referee: after the 2nd rejected a request, decide the delay sanction. */
  function openDelayDecision(e) {
    const opts = { reject_improper: 'Nessuna sanzione (richiesta impropria)', delay_warning: 'Avvertimento per ritardo', delay_penalty: 'Penalizzazione per ritardo' };
    $('decision-title').textContent = 'Il 2° arbitro ha respinto: decidi tu la sanzione';
    $('decision-sub').textContent = '';
    let html = '<div class="dec-event">' + esc(e.text) + '<br><small>2° arbitro: ' + esc(reasonOf(e)) + '</small></div><div class="dec-grid" style="margin-top:12px">';
    Object.keys(opts).forEach(k => { html += '<button class="dec-btn" data-k="' + k + '">' + esc(opts[k]) + '</button>'; });
    html += '</div><div class="dec-back"><button class="btn btn-ghost btn-sm" id="dec-help">Aiuto</button> <button class="btn btn-ghost btn-sm" id="dec-sheet">Referto</button> <button class="btn btn-ghost btn-sm" id="dec-log">Cronologia</button></div>';
    $('decision-body').innerHTML = html;
    bindDecLog();
    $('decision-body').querySelectorAll('[data-k]').forEach(b => b.addEventListener('click', () => { closeModal('decision-modal'); evaluateEvent(e, b.dataset.k); }));
    $('dec-help').addEventListener('click', () => openHelp());
    $('dec-sheet').addEventListener('click', () => toggleSheet(true));
    openModal('decision-modal');
  }
  /** You are the 2nd referee: authorise or reject; a delay sanction is then given by the 1st. */
  function evaluateRequest2(e, choice) {
    const exp = e.expected || 'authorize';
    const expC = exp === 'authorize' ? 'authorize' : 'reject';
    const ok = choice === expC;
    record(e.sit, ok, e.helped);
    const L = e.kind === 'libero' ? { authorize: 'Regolare: lascia proseguire', reject: 'Irregolare: fischia e fai correggere' } : { authorize: 'Autorizza', reject: 'Respingi la richiesta' };
    const lbl = E.TEAM_DEF[e.team].label;
    if (choice === 'authorize' && (e.kind === 'sub' || e.kind === 'timeout')) {
      G.selfNudge = { x: e.kind === 'sub' ? postSide(e.team) : sideOfTeam2(e.team), until: performance.now() + 4200 };
      // Substitution: you turn towards the team that asked for it (first person)
      if (e.kind === 'sub') G.r2Face = { x: G.M.side[e.team] * 7, until: performance.now() + 4200 };
    }
    showFeedback({
      ok, sitId: e.sit,
      quiet: ok && exp === 'authorize',
      happened: e.text,
      yours: L[choice],
      right: L[expC] + (isDelay(exp) ? '; poi il 1° arbitro: ' + (exp === 'delay_warning' ? 'avvertimento' : 'penalizzazione') + ' per ritardo alla ' + lbl : ''),
      note: [exp !== 'authorize' ? 'Motivo: ' + reasonOf(e) + (isDelay(exp) ? ' La sanzione per ritardo la decide il 1° arbitro.' : '') : '',
        e.lateWhistle ? 'La richiesta va accolta (o respinta) subito con il fischio.' : ''].filter(Boolean).join(' ') || null,
      context: e.text,
      signal: secondSignal(e, expC),
      yourSignal: secondSignal(e, choice),
    }, () => {
      if (G.M.opts.scoreMine && choice !== expC) e.expected = choice === 'authorize' ? 'authorize' : 'reject_improper';
      if (isDelay(e.expected)) {
        // The 1st referee gives the delay sanction
        whistle();
        banner('1° arbitro', (e.expected === 'delay_warning' ? 'Avvertimento' : 'Penalizzazione') + ' per ritardo alla ' + lbl + '.', 2800);
        miniSignal(eventSignal(e, e.expected), '1° arbitro');
        finishEvent(e, 2800);
      } else finishEvent(e, 400);
    });
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
    // Disciplinary sanctions: the player (or the game captain) comes to the referee's chair (IT 3.6.1, 3.6.2)
    if (e.kind === 'conduct' && startApproach(e, e.expected)) delay = Math.max(delay || 0, G.approach.total * 1000 + 300);
    const res = G.M.applyBetween(e);
    updateScore();
    // Optional animation of substitutions and Libero replacements (the players walk in and out)
    const swapped = (e.kind === 'sub' || e.kind === 'libero') && (e.expected === 'authorize' || (e.regular && !e.expected));
    if (G.M.opts.subAnim && swapped && e.inn && G.M.pos[e.team + e.inn]) {
      const M = G.M, q = M.pos[e.team + e.inn];
      G.subAnim = { T: e.team, inn: e.inn, out: e.out, court: { x: q.x, y: q.y }, zone: [M.side[e.team] * (e.kind === 'sub' ? 1.6 : 6.2), 5.1], t0: G.clock || 0, dur: 2.6 / animK() };
      delay = Math.max(delay || 0, Math.round(G.subAnim.dur * 1000) + 100);
    }
    logEntry({ kind: e.kind, before, outcome: (G.lastCall && G.lastCall.right) || aiEventText(e, e.expected || (e.regular ? 'authorize' : null)) || e.text,
      replay: { snap, from: 0, to: 4, focus: 1.6, label: LOG_KIND[e.kind] || 'Richiesta', event: true } });
    if ((e.kind === 'timeout' || e.kind === 'sub') && e.expected === 'authorize') G.needAllOk = true;
    if (e.kind === 'conduct' && res.expelled != null) {
      const wait = G.approach ? Math.round((G.approach.walk + G.approach.hold) * 1000) + 200 : 300;
      later(() => applyExpulsion(e.team, res.expelled, res.outFor), wait);
      delay = Math.max(delay || 0, wait + 3200);
    }
    // The captain has left the court: a game captain must be designated. With a 2nd referee it is the 2nd who asks
    if ((e.kind === 'sub' || e.kind === 'libero') && captainToAsk(e.team)) {
      const T = e.team, M = G.M;
      if (M.opts.mode === '1') { later(() => askCaptain(T, false), 900); delay = Math.max(delay || 0, 3400); }
      else banner('Capitano non in campo', 'Il capitano della ' + E.TEAM_DEF[T].label + ' non è più in campo: ' + (M.opts.mode === '2' ? 'da 2° arbitro' : 'da arbitro unico') + ' chiedi chi è il capitano in gioco (fischio libero o referto).', 3600);
    }
    if (e.kind === 'timeout' && (e.expected === 'authorize')) {
      startHuddle();
      if (G.M.opts.waitTimeout) {
        // The whole 30 seconds, with a countdown (can be cut short)
        G.event = null;
        if (res.followUp && res.followUp.length) G.queue.unshift(...res.followUp);
        return startTimeoutWait(e.team);
      }
      banner('Tempo di riposo', E.TEAM_DEF[e.team].label + ' · 30 secondi', 2600);
      delay = Math.max(delay || 0, 3800);
    }
    if (res.point) {
      const show = () => banner('Punto', 'Punto e servizio alla ' + E.TEAM_DEF[res.point].label + '.', 2200);
      if (e.kind === 'conduct' && G.approach) later(show, Math.round((G.approach.walk + G.approach.hold) * 1000)); else show();
    }
    if (res.followUp && res.followUp.length) G.queue.unshift(...res.followUp);
    G.event = null;
    later(() => { if (G.phase === 'break') nextEvent(); }, delay || 400);
  }

  /**
   * Sanctions for misconduct (IT 3.6.1, 3.6.2): the 1st referee whistles and calls to the chair the player on
   * court who is sanctioned, or the game captain (verbal warning, or a member not on court: then that member
   * stands up and raises an arm while the card is shown). Delay sanctions do not call anyone (IT 4.5).
   */
  function startApproach(e, k) {
    const LBL = { verbal: 'avvertimento verbale', yellow: 'avvertimento ufficiale (giallo)', red: 'penalizzazione (rosso)', expulsion: 'espulsione', disqualification: 'squalifica' };
    if (!LBL[k]) return false;
    const M = G.M, T = e.team, onC = [0, 1, 2, 3, 4, 5].map(i => M.onCourt(T, i));
    const direct = k !== 'verbal' && typeof e.who === 'number' && onC.includes(e.who);
    const walker = direct ? e.who : M.gameCaptain(T);
    const key = T + walker, q = M.pos[key];
    if (!q) return false;
    const to = { x: M.side[T] * 0.9, y: -4.7 };
    const walk = clamp(Math.hypot(to.x - q.x, to.y - q.y) / 3.2, 0.8, 2.2) / animK(), hold = 2.0 / animK();
    const standing = !direct && k !== 'verbal' ? e.who : null;
    G.approach = { key, T, from: { x: q.x, y: q.y }, to, t0: G.clock || 0, walk, hold, total: walk * 2 + hold, standing };
    const who = direct ? 'Il n. ' + walker : 'Il capitano in gioco (n. ' + walker + ')';
    banner('Al seggiolone', who + ' si avvicina al 1° arbitro: ' + LBL[k] +
      (standing != null ? (standing === 'coach' ? '; l’allenatore si alza e alza il braccio' : '; il n. ' + standing + ' si alza e alza il braccio') : '') + '.', Math.round((walk + hold) * 1000) + 600);
    return true;
  }

  /**
   * During a time-out the players of both teams leave the court and gather around their coach, by their bench;
   * they walk back to their positions before the next service (from these positions).
   */
  function startHuddle() {
    const M = G.M, from = {}, to = {};
    ['A', 'B'].forEach(T => {
      const b = BENCH[T], cx = (b.x0 + b.x1) / 2 + M.side[T] * -0.2;
      const on = [0, 1, 2, 3, 4, 5].map(k => M.onCourt(T, k));
      on.forEach((n, i) => {
        const key = T + n, a = (i / on.length) * Math.PI * 2;
        const q = M.pos[key] || { x: M.side[T] * 4, y: 0 };
        from[key] = { x: q.x, y: q.y };
        to[key] = { x: cx + Math.cos(a) * 0.95, y: 5.7 + Math.sin(a) * 0.55 };
        M.pos[key] = to[key];
      });
    });
    G.huddle = { from, to, t0: G.clock || 0, dur: 1.8 / animK() };
  }

  /** Time-out of 30 seconds, waited in full (option): countdown, then the game goes on. */
  function startTimeoutWait(team) {
    const tok = G.toWait = { left: 30, team };
    $('to-wait').hidden = false;
    $('to-wait-team').textContent = E.TEAM_DEF[team].label;
    const end = () => { if (G.toWait !== tok) return; G.toWait = null; $('to-wait').hidden = true; if (G.phase === 'break') nextEvent(); };
    tok.end = end;
    later(end, 30000);
  }

  function openEventDecision(e) {
    if (G.phase !== 'break') return;
    const asSecond = G.M.opts.mode === '2' && e.kind !== 'conduct';
    const opts = asSecond ? (e.kind === 'libero' ? { authorize: 'Regolare: lascia proseguire', reject: 'Irregolare: fischia e fai correggere' } : { authorize: 'Autorizza', reject: 'Respingi la richiesta' })
      : e.kind === 'conduct' ? E.CONDUCT_DECISIONS
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
      if (asSecond) evaluateRequest2(e, b.dataset.k); else evaluateEvent(e, b.dataset.k);
    }));
    $('dec-help').addEventListener('click', () => openHelp());
    $('dec-sheet').addEventListener('click', () => toggleSheet(true));
    openModal('decision-modal');
  }

  function evaluateEvent(e, choice, target) {
    trace('evaluateEvent ' + e.kind);
    const exp = e.expected || 'authorize';
    const conduct = e.kind === 'conduct';
    const whoOk = !conduct || choice !== exp || targetOk(e, exp, target);
    const viaOk = !conduct || !viaChoice(exp) || choice !== exp || !whoOk || !target || target.via === rightVia(target.team, target.who, exp);
    const ok = choice === exp && whoOk && viaOk;
    const sitId = e.sit;
    record(sitId, ok, e.helped);
    const opts = e.kind === 'conduct' ? E.CONDUCT_DECISIONS : E.REQUEST_DECISIONS;
    const label = k => (e.kind === 'libero' && k === 'authorize') ? 'Lasciare proseguire (regolare)' : opts[k];
    showFeedback({
      ok, sitId,
      // Routine correct calls (authorised requests, regular Libero replacements) do not stop the game
      quiet: ok && exp === 'authorize',
      happened: e.text,
      yours: label(choice) + (conduct && choice !== 'none' ? ' ' + targetLabel(target) : ''),
      right: label(exp) + (conduct && rightTarget(e, exp) ? ' ' + targetLabel(rightTarget(e, exp)) : ''),
      note: [
        conduct && choice === exp && !whoOk ? 'Il provvedimento era giusto, ma il destinatario no: va dato ' + targetLabel(rightTarget(e, exp)) + '.' : '',
        conduct && choice === exp && whoOk && !viaOk ? (exp === 'verbal'
          ? 'Il provvedimento era giusto, ma l’hai comunicato direttamente al giocatore: l’avvertimento verbale non è personale, si dà alla squadra tramite il capitano in gioco (n. ' + G.M.gameCaptain(e.team) + ') (Regole di gioco 21.1).'
          : rightVia(target.team, target.who) === 'player'
          ? 'Il cartellino a un giocatore in campo si mostra direttamente a lui, invitandolo al seggiolone (Indicazioni tecniche 3.6.2).'
          : 'A un componente non in campo (panchina, allenatore) la sanzione si comunica tramite il capitano in gioco, poi si mostra il cartellino al componente che si alza (Indicazioni tecniche 3.6.2).') : '',
        conduct && exp === 'verbal' && viaOk ? 'L’avvertimento verbale non è personale: si dà alla squadra tramite il capitano in gioco (n. ' + G.M.gameCaptain(e.team) + ').' : '',
        e.lateWhistle ? (conduct ? 'Andava fischiato subito, appena visto il comportamento.' : 'La richiesta va accolta (o respinta) subito con il fischio.') : '',
      ].filter(Boolean).join(' ') || null,
      context: e.text,
      signal: eventSignal(e, exp),
      yourSignal: conduct && target ? eventSignal(Object.assign({}, e, { team: target.team, who: target.who }), choice) : eventSignal(e, choice),
    }, () => {
      // "Vale la mia decisione": your choice is applied, even if wrong
      if (G.M.opts.scoreMine) {
        e.expected = choice;
        if (conduct && target && typeof target.who === 'number') { e.team = target.team; e.who = target.who; }
      }
      finishEvent(e, 400);
    });
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
    G.aiWhistleT = null;
    G.collabSignal = null; G.collabUsed = false; G.firstMisses = undefined;
    $('collab-panel').hidden = true;
    G.helped = false;
    G.deadAt = null;
    G.netShake = null;
    G.authorized = false;
    G.huddle = null;
    G.approach = null;
    G.rallySigs = [];
    G.authSig = false;
    G.r2HoldSet = false; G.r2HoldUntil = null; G.r2Face = null;
    if (G.needAllOk) { G.needAllOk = false; allOk(); }
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
    // Real time since the last frame (capped, e.g. after the tab was in the background), played in steps of at
    // most 50 ms: on a slow device the frames are fewer but the game keeps its real pace
    let left = Math.min(0.25, (now - (G.lastFrame || now)) / 1000);
    G.lastFrame = now;
    const canCollab = G.M && G.M.opts.mode === '2' && G.phase === 'rally' && !G.whistled;
    $('btn-collab').hidden = !canCollab;
    // The collaboration panel belongs to the rally: it closes as soon as the rally is over
    if (!canCollab && !$('collab-panel').hidden) $('collab-panel').hidden = true;
    // While choosing the collaboration signal the play goes on in slow motion
    const k = (G.speed || 1) * (canCollab && !$('collab-panel').hidden ? 0.3 : 1);
    do {
      const dt = Math.min(0.05, left);
      left -= dt;
      const running = !G.paused && !G.modal;
      if (running) { step(dt * k); updateMovement(dt * k); G.clock = (G.clock || 0) + dt * k; }
      runTimers(dt * k, running);
    } while (left > 1e-6 && G.M && G.phase !== 'idle' && G.phase !== 'over');
    updateClocks();
    draw();
  }

  /** Game clock (time played, without pauses and decision windows) and seconds since the service authorisation. */
  function updateClocks() {
    const c = Math.floor(G.clock || 0);
    const txt = Math.floor(c / 60) + ':' + String(c % 60).padStart(2, '0');
    if ($('badge-clock').textContent !== '⏱ ' + txt) $('badge-clock').textContent = '⏱ ' + txt;
    const R = G.R;
    const showServe = R && G.authorized && (G.phase === 'rally' || G.phase === 'decision') && G.t <= R.tHit + 1.2;
    $('badge-serve').hidden = !showServe;
    if (showServe) {
      const sec = Math.min(G.t, R.tHit);
      $('badge-serve').textContent = 'Servizio: ' + sec.toFixed(1).replace('.', ',') + ' s';
      $('badge-serve').classList.toggle('late', sec > 8);
    }
    if (G.toWait) {
      const left = Math.max(0, Math.ceil(30 - ((G.clock || 0) - (G.toWait.t0 == null ? (G.toWait.t0 = G.clock || 0) : G.toWait.t0))));
      $('to-wait-left').textContent = left;
    }
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
        // The 1st referee's authorisation signal, also live on his character (seen from your position): arm
        // towards the serving team, whistle (when the arm is out), then the arm folds
        if (G.prepT >= 1.0 && !G.authSig && !G.waitAllOk) {
          G.authSig = true;
          miniSignal({ role: '1', steps: SimGestures.authorizeSteps(handFor(M.serving, '1')) }, '1° arbitro · autorizzazione al servizio');
        }
        if (G.authSig && G.prepT >= 2.6) { G.authorized = true; whistle(); banner('1° arbitro', 'Autorizza il servizio.', 1200); beginRally(); }
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
      R.events.forEach(ev => { if (ev.kind === 'net' && ev.t > prevT && ev.t <= G.t) G.netShake = { y: ev.y, t0: G.t, w0: performance.now(), amp: ev.strong ? 0.09 : 0.05 }; });
      // The 2nd referee helps the 1st with ball-handling faults: an unofficial signal at mid-chest, a small step
      if (M.opts.mode === '1' && !G.whistled) {
        const hf = R.faults.find(f => HANDLING[f.key] && G.t >= f.t + 0.25 && G.t < f.t + 1.5);
        if (hf && G.collab !== hf) {
          G.collab = hf;
          if (Math.random() < 0.85) {
            const pose = { four_hits: 'chest4', double_contact: 'chest2' }[hf.key];
            miniSignal({ role: '2', steps: [{ pose, side: handFor(hf.team, '2'), caption: E.DECISIONS[hf.key].label + ' della ' + E.TEAM_DEF[hf.team].label + ' (segnale non ufficiale, al petto)', dur: 2.4 }] }, '2° arbitro · collaborazione');
            G.partnerNudge = { x: collabStep(G.partnerX, hf.team), until: performance.now() + 2500 };
          }
        }
      }
      if (M.opts.mode === '1' && R.natural && R.natural.key === 'touched_out' && !R.faults.length && G.t >= R.natural.t + 0.1 && G.collab !== R.natural) {
        G.collab = R.natural;
        if (Math.random() < 0.85) {
          miniSignal({ role: '2', steps: [{ pose: 'chestTouched', caption: 'Palla toccata dalla ' + E.TEAM_DEF[R.natural.team].label + ' (segnale discreto, al petto)', dur: 2.4 }] }, '2° arbitro · collaborazione');
          G.partnerNudge = { x: collabStep(G.partnerX, R.natural.team), until: performance.now() + 2500 };
        }
      }
      // The automatic referee whistles faults of their competence
      for (const f of R.faults) {
        if (G.whistled) break;
        const userDecides = M.userDecides(f.comp);
        const partner = M.opts.mode !== 'solo';
        if (!userDecides && HANDLING[f.key] && M.opts.mode === '2') {
          // The 1st referee sometimes misses a ball-handling fault: your collaboration signal helps
          if (G.firstMisses === undefined) G.firstMisses = Math.random() < 0.4;
          const sig = G.collabSignal;
          const helped = sig && sig.key === f.key && sig.team === f.team && sig.t >= f.t - 0.1;
          if (helped && G.t >= sig.t + 0.4) { G.collabUsed = true; return aiWhistle(f, false); }
          if (!G.firstMisses && G.t >= f.t + 0.35) return aiWhistle(f, false);
          if (f.t > G.t) break;
          continue;
        }
        if (!userDecides && G.t >= f.t + 0.35) return aiWhistle(f, false);
        // Faults that the 2nd referee sees best (the centre line, IT 1.2.3): with you as 1st, the 2nd whistles them
        // straight away (you may still whistle first); with you as 2nd, the 1st leaves them to you
        if (SECOND_VIEW[f.key] && M.opts.mode === '1' && G.t >= f.t + 0.3) return aiWhistle(f, false);
        if (SECOND_VIEW[f.key] && M.opts.mode === '2') { if (f.t > G.t) break; continue; }
        if (userDecides && f.comp === 'both' && partner && G.t >= f.t + G.aiPartnerGrace + (M.opts.mode === '2' ? 1.1 : 0.35)) return aiWhistle(f, true);
        if (f.t > G.t) break;
      }
      // Ball dead. With you as 2nd referee the 1st waits a moment longer before whistling, so you have the time
      // to signal (ball touched, out) or to whistle a fault of yours
      if (G.t >= R.endT + (M.opts.mode === '2' ? 1.4 : 0.35) && !G.whistled) {
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
    // Before the service or between rallies: a whistle to sanction someone, ask for the game captain...
    if (G.phase === 'break' || G.phase === 'prep') {
      whistle();
      $('btn-whistle').classList.remove('pulse');
      if (G.pendingConduct) return openPendingEvent(G.pendingConduct);
      return openFreeWhistle();
    }
  }

  /** "Why did you whistle?" when the ball is not in play. */
  function openFreeWhistle() {
    const M = G.M;
    $('decision-title').textContent = 'Hai fischiato: perché?';
    $('decision-sub').textContent = G.phase === 'prep' ? 'Il servizio non è ancora stato autorizzato.' : '';
    let html = '<div class="dec-grid">';
    if (M.opts.mode !== '2') html += '<button class="dec-btn" id="fw-sanction">Sanzione (cartellino o altro)…</button>';
    html += ['A', 'B'].map(T => '<button class="dec-btn" data-cap="' + T + '"><span class="sh-dot" style="background:' + TEAM_COLOR[T] + '"></span> Chiedi il capitano in gioco · ' + esc(E.TEAM_DEF[T].label) + '</button>').join('');
    html += '<button class="dec-btn" id="fw-none">Niente: riprendi</button></div>';
    html += '<div class="dec-back"><button class="btn btn-ghost btn-sm" id="dec-help">Aiuto</button> <button class="btn btn-ghost btn-sm" id="dec-sheet">Referto</button> <button class="btn btn-ghost btn-sm" id="dec-log">Cronologia</button></div>';
    $('decision-body').innerHTML = html;
    bindDecLog();
    $('dec-help').addEventListener('click', () => openHelp());
    $('dec-sheet').addEventListener('click', () => toggleSheet(true));
    $('fw-none').addEventListener('click', () => closeModal('decision-modal'));
    $('decision-body').querySelectorAll('[data-cap]').forEach(b => b.addEventListener('click', () => { closeModal('decision-modal'); askCaptain(b.dataset.cap, true); }));
    if ($('fw-sanction')) $('fw-sanction').addEventListener('click', () => openSanctionPicker({
      title: 'Hai fischiato: quale sanzione?',
      options: SANCTIONS,
      onDone: (k, target) => evaluateFreeSanction(k, target),
      onBack: () => openFreeWhistle(),
    }));
    openModal('decision-modal');
  }

  // ======================================================================================
  // Sanctions: choose the sanction and who gets it (a player, the coach, or the team through the game captain)
  // ======================================================================================
  const SANCTIONS = {
    verbal: 'Avvertimento verbale', yellow: 'Cartellino giallo (avvertimento)', red: 'Cartellino rosso (penalizzazione)',
    expulsion: 'Espulsione (giallo e rosso insieme)', disqualification: 'Squalifica (giallo e rosso separati)',
    delay_warning: 'Avvertimento per ritardo', delay_penalty: 'Penalizzazione per ritardo',
  };
  const sanctionLabel = k => SANCTIONS[k] || E.CONDUCT_DECISIONS[k] || E.REQUEST_DECISIONS[k] || k;
  function targetLabel(t) {
    if (!t) return '';
    const lbl = E.TEAM_DEF[t.team].label;
    return t.who === 'team' ? 'alla ' + lbl + ' (tramite il capitano in gioco)' : t.who === 'coach' ? 'all’allenatore della ' + lbl : 'al n. ' + t.who + ' della ' + lbl + (t.via === 'captain' ? ' (comunicata tramite il capitano in gioco)' : '');
  }
  /**
   * An expelled (for the set) or disqualified (for the match) member goes to the locker room; on court he is
   * substituted straight away: legally, or with an exceptional substitution, or the team is incomplete (Rule 15.8).
   */
  function applyExpulsion(T, who, outFor) {
    const M = G.M, d = E.TEAM_DEF[T];
    const before = M.pos[T + who] ? { x: M.pos[T + who].x, y: M.pos[T + who].y } : null;
    const r = M.forcedSub(T, who, outFor);
    const where = outFor === 'match' ? 'per il resto della gara' : 'fino alla fine del set';
    let txt = 'Il n. ' + who + ' va negli spogliatoi ' + where + '.';
    if (r.kind === 'regular') txt += ' Sostituzione regolamentare: entra il n. ' + r.inn + '.';
    if (r.kind === 'exceptional') txt += ' Non è possibile una sostituzione regolamentare: sostituzione eccezionale con il n. ' + r.inn + '.';
    if (r.kind === 'libero') txt += ' Rientra il n. ' + r.inn + ', che il Libero stava rimpiazzando; il Libero non può più giocare.';
    if (r.kind === 'incomplete') txt += ' Nessuna sostituzione possibile: la ' + d.label + ' è incompleta e perde il set.';
    banner(outFor === 'match' ? 'Squalifica' : 'Espulsione', txt, 3200);
    G.lastSubLog = txt;
    if ((r.kind === 'regular' || r.kind === 'exceptional') && before && M.opts.subAnim) {
      G.subAnim = { T, inn: r.inn, out: who, court: before, zone: [M.side[T] * 1.6, 5.1], t0: G.clock || 0, dur: 2.6 / animK() };
    }
    G.lastCall = { by: '2° arbitro', user: false, happened: txt };
    logEntry({ kind: 'sub', before: M.score.A + '–' + M.score.B, outcome: txt, replay: null });
    updateScore();
    if (!$('sheet').hidden) renderSheet();
    if (r.kind === 'regular' || r.kind === 'exceptional') G.needAllOk = true;
  }

  /** True when the team captain is not on court and the game captain has not been asked yet. */
  function captainToAsk(T) {
    const M = G.M, t = M.teams[T], d = E.TEAM_DEF[T];
    const on = [0, 1, 2, 3, 4, 5].map(k => M.onCourt(T, k));
    return !on.includes(d.captain) && t.gcAsked !== M.gameCaptain(T);
  }

  /** Asks a team who the game captain is (the referee whistles and the team shows it). */
  function askCaptain(T, silent) {
    const M = G.M, n = M.gameCaptain(T), d = E.TEAM_DEF[T];
    if (!silent) whistle();
    M.teams[T].gcAsked = n;
    G.capReveal = { T, n, t0: performance.now(), until: performance.now() + 5000 };
    // Official signal of the request (not a sanction): the index finger touches the chest at the number's height.
    // With a 2nd referee, it is the 2nd who asks; the sole referee asks himself.
    const role = M.opts.mode === 'solo' ? '1' : '2';
    miniSignal({ role, steps: [
      { pose: 'whistle', whistle: true, caption: 'Fischio', dur: 0.9 },
      { pose: 'askCap', side: handFor(T, role), motion: 'tapChest', once: true, caption: 'Chi è il capitano in gioco? L’indice tocca il petto all’altezza del numero', dur: 2.0 },
    ] }, (role === '2' ? (M.opts.mode === '2' ? 'Tu (2° arbitro)' : '2° arbitro') : '1° arbitro') + ' · richiesta del capitano in gioco');
    banner('Richiesta del capitano in gioco · ' + d.label + (role === '2' ? ' (2° arbitro)' : ''), n === d.captain ? 'Il capitano n. ' + n + ' è in campo: è lui il capitano in gioco.' : 'Capitano in gioco: n. ' + n + ' (il capitano n. ' + d.captain + ' non è in campo).', 3200);
    if (!$('sheet').hidden) renderSheet();
    return n;
  }
  /**
   * opts: { title, text, options: {key: label}, fixedKey, onDone(key, {team, who}), onBack }.
   * Uses the decision window: sanction, then team, then member.
   */
  function openSanctionPicker(opts) {
    const st = { key: opts.fixedKey || null, team: null, who: null };
    const render = () => {
      $('decision-title').textContent = opts.title;
      $('decision-sub').textContent = '';
      let html = opts.text ? '<div class="dec-event">' + esc(opts.text) + '</div>' : '';
      html += '<div class="dec-group"><h5>Provvedimento</h5><div class="dec-grid">' + Object.keys(opts.options).map(k =>
        '<button class="dec-btn' + (st.key === k ? ' on' : '') + '" data-s="' + k + '">' + esc(opts.options[k]) + '</button>').join('') + '</div></div>';
      html += '<div class="dec-group"><h5>A chi</h5><div class="dec-grid"><button class="dec-btn" id="pick-court">' + (st.team ? 'Cambia sul campo…' : 'Scegli sul campo…') + '</button>' +
        ['A', 'B'].map(T => '<button class="dec-btn" data-askcap="' + T + '"><span class="sh-dot" style="background:' + TEAM_COLOR[T] + '"></span> Chiedi il capitano in gioco · ' + esc(E.TEAM_DEF[T].label) + '</button>').join('') + '</div>';
      if (st.team) html += '<div class="pick-target">' + esc(targetLabel(st).replace(/^(al|alla|all’) ?/, '')) + '</div>';
      if (st.team && (typeof st.who === 'number' || st.who === 'coach') && st.key && viaChoice(st.key)) {
        html += '<div class="dec-group"><h5>Comunica la sanzione</h5><div class="segmented seg-via">' +
          '<button type="button" data-via="player" class="' + (st.via !== 'captain' ? 'active' : '') + '">Direttamente al giocatore</button>' +
          '<button type="button" data-via="captain" class="' + (st.via === 'captain' ? 'active' : '') + '">Tramite il capitano in gioco</button></div></div>';
      }
      html += '</div>';
      html += '<div class="dec-confirm"><button class="btn btn-ghost" id="san-back">Indietro</button><button class="btn btn-primary" id="san-ok"' + (st.key && st.team && st.who !== null ? '' : ' disabled') + '>Sanziona</button></div>';
      html += '<div class="dec-back"><button class="btn btn-ghost btn-sm" id="dec-help">Aiuto</button> <button class="btn btn-ghost btn-sm" id="dec-sheet">Referto</button> <button class="btn btn-ghost btn-sm" id="dec-log">Cronologia</button></div>';
      $('decision-body').innerHTML = html;
      bindDecLog();
      $('dec-help').addEventListener('click', () => openHelp());
      $('dec-sheet').addEventListener('click', () => toggleSheet(true));
      $('decision-body').querySelectorAll('[data-s]').forEach(b => b.addEventListener('click', () => { st.key = b.dataset.s; render(); }));
      $('decision-body').querySelectorAll('[data-via]').forEach(b => b.addEventListener('click', () => { st.via = b.dataset.via; render(); }));
      const toCourt = () => pickOnCourt(st, () => { render(); openModal('decision-modal'); });
      $('pick-court').addEventListener('click', toCourt);
      // Asking who the game captain is (when the captain is not on court): whistle and signal, then back here
      $('decision-body').querySelectorAll('[data-askcap]').forEach(b => b.addEventListener('click', () => {
        closeModal('decision-modal');
        G.modal = 'askcap'; // the game stays still while the team shows its game captain
        askCaptain(b.dataset.askcap, true);
        later(() => { render(); openModal('decision-modal'); }, 3000);
      }));

      // After choosing the sanction, go straight to the court to pick who gets it
      $('decision-body').querySelectorAll('[data-s]').forEach(b => b.addEventListener('click', () => { if (!st.team) setTimeout(toCourt, 120); }));
      $('san-back').addEventListener('click', () => { if (opts.onBack) opts.onBack(); });
      $('san-ok').addEventListener('click', () => { closeModal('decision-modal'); opts.onDone(st.key, { team: st.team, who: st.who, via: st.via === 'captain' ? 'captain' : 'player' }); });
    };
    render();
    openModal('decision-modal');
  }

  // ---------- Collaboration signals of the 2nd referee (you) to the 1st ----------
  // Collaboration signals exist only for double contact and four hits (not for a catch), plus the ball touched
  // by the team on whose side it went out (Indicazioni tecniche 1.3, 4.3.13)
  const HANDLING = { double_contact: 'Doppio tocco', four_hits: 'Quattro tocchi' };
  const COLLAB = { double_contact: 'Doppio tocco', four_hits: 'Quattro tocchi', touched: 'Palla toccata' };
  /**
   * The 2nd referee's small step while signalling: from where they are, towards the team at fault when it is
   * the team on the other side; they stay still when the team at fault is the one on their side.
   */
  function collabStep(curX, team) {
    const side = G.M.side[team], mine = Math.abs(curX) < 0.3 ? 0 : Math.sign(curX);
    return mine === side ? curX : clamp(curX + side * 0.6, -MOVE['2'].max, MOVE['2'].max);
  }
  function openCollab() {
    if (G.phase !== 'rally' || G.whistled) return;
    const p = $('collab-panel');
    if (!p.hidden) { p.hidden = true; return; }
    $('cp-grid').innerHTML = Object.keys(COLLAB).map(k => ['A', 'B'].map(T =>
      '<button class="cp-btn" data-k="' + k + '" data-t="' + T + '"><span class="sh-dot" style="background:' + TEAM_COLOR[T] + '"></span>' + COLLAB[k] + ' · ' + T + '</button>').join('')).join('');
    $('cp-grid').querySelectorAll('[data-k]').forEach(b => b.addEventListener('click', () => sendCollab(b.dataset.k, b.dataset.t)));
    p.hidden = false;
  }
  function sendCollab(key, team) {
    $('collab-panel').hidden = true;
    if (G.phase !== 'rally' || G.whistled) return;
    G.collabSignal = { key, team, t: G.t };
    const pose = { four_hits: 'chest4', double_contact: 'chest2', touched: 'chestTouched' }[key];
    miniSignal({ role: '2', steps: [{ pose, side: handFor(team, '2'), caption: COLLAB[key] + ' della ' + E.TEAM_DEF[team].label + ' (al petto, con un passo verso la squadra)', dur: 2.0 }] }, 'Il tuo segnale al 1° arbitro');
    // A small step towards the team at fault
    G.refTarget = collabStep(G.refX, team);
    G.manualUntil = performance.now() + 1500;
  }
  /** At the end of the rally: how your collaboration went (mode 2). */
  function judgeCollab(R) {
    if (G.M.opts.mode !== '2') return;
    const sig = G.collabSignal;
    const hf = R.faults.find(f => HANDLING[f.key]);
    const touched = R.natural && R.natural.key === 'touched_out' && !R.faults.length;
    if (sig && sig.key === 'touched') {
      const ok = touched && sig.team === R.natural.team;
      record(ok ? 'collab_ok' : 'collab_wrong', !!ok, false);
      if (!ok) toast('Il tuo segnale di palla toccata non era corretto: il 1° arbitro lo ha ignorato.');
    } else if (sig) {
      const ok = hf && sig.key === hf.key && sig.team === hf.team && sig.t >= hf.t - 0.1;
      record(ok ? 'collab_ok' : 'collab_wrong', !!ok, false);
      if (!ok) toast('Il tuo segnale di collaborazione non corrispondeva a un fallo: il 1° arbitro lo ha ignorato.');
    } else if ((hf || touched) && G.firstMisses && !G.collabUsed) {
      record('collab_missed', false, false);
    }
  }

  // ---------- Picking a person on the court (players, bench, coaches), with a light hover ----------
  const PP = { active: false, hover: null, chosen: null, st: null, done: null };
  function pickOnCourt(st, done) {
    PP.active = true; PP.hover = null;
    PP.chosen = st.team && st.who !== 'team' ? (st.who === 'coach' ? 'coach' + st.team : st.team + st.who) : null;
    PP.st = st; PP.done = done;
    $('decision-modal').hidden = true;
    G.modal = 'person';
    $('person-bar').hidden = false;
    $('pb-sanction').textContent = st.key ? sanctionLabel(st.key) : 'Sanzione';
  }
  function endPick(apply) {
    PP.active = false;
    $('person-bar').hidden = true;
    $('sim-canvas').style.cursor = '';
    if (apply !== false && PP.done) PP.done();
  }
  function choosePerson(id) {
    const st = PP.st;
    if (/^coach/.test(id)) { st.team = id.slice(5); st.who = 'coach'; }
    else { st.team = id[0]; st.who = +id.slice(1); }
    PP.chosen = id;
    endPick();
  }
  function canvasPoint(ev) {
    const r = $('sim-canvas').getBoundingClientRect();
    return [ev.clientX - r.left, ev.clientY - r.top];
  }
  function bindPersonPicking() {
    const cv = $('sim-canvas');
    cv.addEventListener('pointermove', ev => {
      if (!PP.active) return;
      const [x, y] = canvasPoint(ev);
      PP.hover = G.renderer.hitTest(x, y);
      cv.style.cursor = PP.hover ? 'pointer' : '';
    });
    cv.addEventListener('pointerleave', () => { PP.hover = null; });
    cv.addEventListener('click', ev => {
      if (!PP.active) return;
      const [x, y] = canvasPoint(ev);
      const id = G.renderer.hitTest(x, y);
      if (id) choosePerson(id);
    });
    $('pb-cancel').addEventListener('click', () => endPick());
  }

  function openPendingEvent(e) {
    if (e.kind === 'conduct') return openConductPicker(e);
    G.pendingConduct = null;
    $('btn-whistle').classList.remove('pulse');
    hint('');
    openEventDecision(e);
  }

  function openConductPicker(e) {
    G.pendingConduct = null;
    $('btn-whistle').classList.remove('pulse');
    hint('');
    openSanctionPicker({
      title: 'Condotta di un giocatore: che provvedimento prendi?',
      text: e.text,
      options: E.CONDUCT_DECISIONS,
      onDone: (k, target) => evaluateEvent(e, k, target),
      onBack: null,
    });
  }

  /** Who should get the sanction: the offender for cards, the team (through the game captain) for warnings and delays. */
  function rightTarget(e, exp) {
    if (exp === 'none') return null;
    if (exp === 'verbal' || exp === 'delay_warning' || exp === 'delay_penalty') return { team: e.team, who: 'team' };
    return { team: e.team, who: e.who };
  }
  function targetOk(e, exp, t) {
    const r = rightTarget(e, exp);
    if (!r) return true;
    if (!t || t.team !== r.team) return false;
    if (r.who === 'team') return true;   // a team warning: whoever you point at, it is given through the game captain
    return t.who === r.who;
  }
  const CARDS = { yellow: 1, red: 1, expulsion: 1, disqualification: 1 };
  /**
   * How a card is communicated (Indicazioni tecniche 3.3.1, 3.6.2): directly to an athlete on court; through the
   * game captain for a member who is not on court (bench, coach, staff). The verbal warning always goes through
   * the game captain (3.6.1).
   */
  /** Sanctions for which you choose how to communicate them: the cards and the verbal warning. */
  const viaChoice = k => !!CARDS[k] || k === 'verbal';
  function rightVia(team, who, key) {
    // The verbal warning is not personal: it is always given to the team through the game captain
    if (key === 'verbal' || typeof who !== 'number') return 'captain';
    return [0, 1, 2, 3, 4, 5].map(k => G.M.onCourt(team, k)).includes(who) ? 'player' : 'captain';
  }

  /** A sanction whistled between rallies when nothing happened. */
  function evaluateFreeSanction(k, target) {
    record('sanction_no_misconduct', false, false);
    showFeedback({
      ok: false, sitId: 'sanction_no_misconduct',
      happened: 'Nessun componente delle squadre ha avuto un comportamento scorretto.',
      yours: sanctionLabel(k) + ' ' + targetLabel(target),
      right: 'Nessun provvedimento',
      note: null,
      signal: { role: '1', steps: [], note: 'Nessuna segnaletica: non c’era nulla da sanzionare.' },
      yourSignal: eventSignal({ kind: 'conduct', team: target.team, who: target.who }, k),
    }, () => {});
  }

  /** A sanction chosen after whistling during a rally: there was no misconduct during the play. */
  function evaluateRallySanction(k, target, whistleT) {
    const M = G.M, R = G.R;
    const tr = M.truth(R, whistleT);
    const exp = M.expected(R, tr);
    const sitId = tr.fault ? tr.fault.sit : 'sanction_no_misconduct';
    record(sitId, false, G.helped);
    showFeedback({
      ok: false, sitId,
      happened: tr.fault ? tr.fault.text + ' Nessuno ha avuto un comportamento da sanzionare.' : 'Durante l’azione nessuno ha avuto un comportamento da sanzionare.',
      yours: sanctionLabel(k) + ' ' + targetLabel(target),
      right: decisionLabel(exp),
      note: tr.fault ? null : 'Fischio senza fallo: l’azione va rigiocata.',
      signal: rallySignal(exp, tr.fault ? tr.fault.comp : null),
      yourSignal: eventSignal({ kind: 'conduct', team: target.team, who: target.who }, k),
      replay: replayInfo(R, tr.fault, false),
    }, () => afterRally(applied(exp, { key: 'no_fault', point: null })));
  }

  /**
   * What happens on the scoreboard: the correct decision (default) or, with the option "Vale la mia decisione",
   * the one you took, even if wrong.
   */
  function applied(exp, dec) {
    if (!G.M.opts.scoreMine || !dec) return exp;
    if (dec.key === 'no_fault' || dec.key === 'double_fault' || dec.key === 'serve_early') return { key: dec.key, point: null };
    return { key: dec.key, point: dec.point || null };
  }

  /** Faults of both referees that, in practice, the 2nd sees much better (Indicazioni tecniche 1.2.3, 4.3.15). */
  const SECOND_VIEW = { centre_line: 1 };

  function aiWhistle(f, userMissed) {
    const M = G.M;
    G.whistled = true;
    G.aiWhistleT = G.t;
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
    banner(who + ' · fischio', E.DECISIONS[f.key].label + (exp.point ? ': punto alla ' + E.TEAM_DEF[exp.point].label : '') + '. ' + f.text, 3600);
    const sig = rallySignal(exp, f.comp, false, M.opts.mode === '2' ? '1' : '2');
    if (sig.role === '2' && exp.point) G.partnerNudge = { x: sideOfTeam2(E.other(exp.point)), until: performance.now() + 4200 };
    miniSignal(sig, who);
    later(() => afterRally(exp), Math.max(2400, sig.steps.reduce((a, x) => a + (x.dur || 1.6), 0) * 1000 * 0.8));
  }

  function ballDead() {
    const M = G.M, R = G.R;
    G.whistled = true;
    G.aiWhistleT = G.t;
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
      // A ball-handling fault the 1st referee did not see (and you did not signal): the rally counts as played
      const unseen = tr.fault && HANDLING[tr.fault.key] && G.firstMisses && !G.collabUsed && R.natural;
      // A ball touched by the block that the 1st did not see: without your signal he calls it out
      if (G.firstMisses === undefined) G.firstMisses = Math.random() < 0.4;
      const sigT = G.collabSignal && G.collabSignal.key === 'touched' && G.collabSignal.team === (R.natural && R.natural.team);
      const touchUnseen = !tr.fault && R.natural && R.natural.key === 'touched_out' && G.firstMisses && !sigT;
      if (sigT) G.collabUsed = true;
      let res = unseen ? M.expected(R, { natural: R.natural }) : exp;
      if (touchUnseen) res = { key: 'out', point: R.natural.team };
      G.lastCall = { by: '1° arbitro', user: false, happened: unseen ? tr.fault.text + ' Il 1° arbitro non l’ha visto.' : R.natural ? naturalText(R.natural) : '' };
      banner('1° arbitro', decisionLabel(res) + '.' + (unseen ? ' Non ha visto: ' + tr.fault.text + ' Il tuo segnale di collaborazione l’avrebbe aiutato.' : '') +
        (touchUnseen ? ' Non ha visto il tocco del muro: con il segnale di palla toccata l’avresti aiutato.' : ''), unseen || touchUnseen ? 4200 : 2200);
      const sig1 = rallySignal(res, null, !unseen && !tr.fault && R.natural && R.natural.noWhistle, '1');
      miniSignal(sig1, '1° arbitro');
      later(() => afterRally(res), unseen || touchUnseen ? 3200 : 1800);
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
    if (whistleT !== null && M.opts.mode !== '2') html += '<div class="dec-group"><h5>Sanzioni</h5><div class="dec-grid"><button class="dec-btn" id="dec-sanction">Cartellino o altra sanzione…</button></div></div>';
    html += '<div class="dec-back"><button class="btn btn-ghost btn-sm" id="dec-help">Aiuto</button> <button class="btn btn-ghost btn-sm" id="dec-sheet">Referto</button> <button class="btn btn-ghost btn-sm" id="dec-top">Vista dall’alto</button> <button class="btn btn-ghost btn-sm" id="dec-log">Cronologia</button></div>';
    $('decision-body').innerHTML = html;
    bindDecLog();
    $('decision-body').querySelectorAll('[data-k]').forEach(b => b.addEventListener('click', () => chooseTeam(b.dataset.k, whistleT)));
    if ($('dec-sanction')) $('dec-sanction').addEventListener('click', () => openSanctionPicker({
      title: 'Hai fischiato: quale sanzione?', options: SANCTIONS,
      onDone: (k, target) => evaluateRallySanction(k, target, whistleT),
      onBack: () => openRallyDecision(whistleT),
    }));
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
    // A whistle without a fault is always an error, even when it is then handled correctly (rally replayed)
    const handled = tr.wrongWhistle && (dec.key === 'no_fault' || dec.key === 'double_fault');
    let ok = !tr.wrongWhistle && dec.key === exp.key && (exp.point === null || dec.point === exp.point);
    // Same point, different reason: the rally had already ended with an earlier fault. It counts as correct,
    // with a warning ("attenzione") about the fault to whistle and its signal.
    const warn = !ok && !tr.wrongWhistle && !!tr.fault && !!exp.point && dec.point === exp.point && dec.key !== exp.key;
    if (warn) {
      ok = true;
      note = 'Attenzione: il punto è giusto, ma l’azione era già finita prima, con il fallo «' + E.DECISIONS[exp.key].label + '»' +
        (tr.missed ? ', che andava fischiato subito' : '') + ': la decisione e la segnaletica corrette sono quelle di questo fallo.';
    }
    if (tr.wrongWhistle) note = (note ? note + ' ' : '') + (handled
      ? 'Hai gestito bene il fischio sbagliato (azione da ripetere), ma fischiare senza fallo resta un errore grave.'
      : 'Fischiare senza fallo è un errore grave: va riconosciuto e l’azione si ripete con la segnaletica del doppio fallo.');
    record(sitId, ok, G.helped, warn);
    if (M.opts.mode === '2' && whistleT != null && dec.point && FAULT_SIGNAL[dec.key]) G.selfNudge = { x: sideOfTeam2(E.other(dec.point)), until: performance.now() + 4200 };
    showFeedback({
      ok, warn, sitId, happened, note,
      yours: E.DECISIONS[dec.key].label + (dec.point ? ' · punto alla ' + E.TEAM_DEF[dec.point].label : ''),
      right: decisionLabel(exp),
      context: happened,
      quiet: ok && !tr.fault && !tr.wrongWhistle && !R.trap && !(R.natural && R.natural.borderline) && sitId !== 'touched_out',
      signal: rallySignal(exp, tr.fault ? tr.fault.comp : null, !tr.fault && !tr.wrongWhistle && R.natural && R.natural.noWhistle),
      // The signal for the decision the user took (shown even when it is wrong)
      yourSignal: rallySignal(dec, null, !whistleT && (dec.key === 'in' || dec.key === 'out') && R.natural && R.natural.noWhistle, M.opts.mode === '2' ? '2' : null),
      replay: replayInfo(R, tr.fault, !!tr.missed),
    }, () => afterRally(applied(exp, dec)));
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
    judgeCollab(R);
    $('collab-panel').hidden = true;
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
    // When you are the 2nd referee and you whistled, the 1st then shows the team to serve. (The 2nd repeats the
    // team-to-serve signal only after their own whistle, and never makes the authorisation signal.)
    if (exp.point && M.opts.mode === '2' && G.whistleT != null && !G.liveSig) {
      const pr = '1';
      G.liveSig = { role: pr, t0: performance.now(), sides: sidesFor(pr), steps: [
        { pose: 'rest', caption: '', dur: 0.4 },
        { pose: 'serve', side: handFor(exp.point, pr), caption: '', dur: 1.6 },
        { pose: 'rest', caption: '', dur: 0.4 }] };
    }
    const rpi = replayInfo(R, f0 && f0.key === exp.key ? f0 : null, false);
    (G.rallySigs || []).forEach(x => attachSig(rpi, x.sig, x.t));
    // After your whistle as 2nd referee the 1st shows the team to serve (recorded for the replay too)
    if (G.liveSig && G.liveSig.role === '1' && exp.point && M.opts.mode === '2' && G.whistleT != null && rpi) {
      const ends = (rpi.sigs || []).map(x => x.t + x.sig.steps.reduce((a, s) => a + (s.dur || 1.6), 0));
      attachSig(rpi, { role: '1', steps: G.liveSig.steps }, ends.length ? Math.max(...ends) : G.whistleT);
    }
    G.rallySigs = [];
    logEntry({ kind: 'rally', before, outcome: replay ? decisionLabel(exp) + ' · si ripete' : decisionLabel(exp), replay: rpi });
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
    touched_out: { pose: 'touched', motion: 'brush', name: 'Palla toccata: mano verticale in alto, dorso verso i campi; l’altra mano, palmo in basso, sfiora le dita una volta, da vicino al corpo verso l’esterno (in avanti)' },
    serve_foot: { pose: 'line', name: 'Fallo del giocatore al servizio: indica la linea di fondo, senza oscillare il braccio' },
    serve_8s: { pose: 'eight', name: 'Ritardo al servizio: otto dita divaricate' },
    serve_net: { pose: 'net', name: 'Palla di servizio che non supera la rete: braccio teso dritto verso il segnapunti all’altezza della spalla, mano aperta in prosecuzione del braccio' },
    pos_fault: { pose: 'position', motion: 'circle', name: 'Fallo di posizione: indice rivolto verso il pavimento, con un movimento circolare' },
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

  function rallySignal(exp, faultComp, noWhistle, roleOverride) {
    const M = G.M;
    const lbl = T => E.TEAM_DEF[T].label;
    // Whistle by mistake: the rally is replayed, with the double-fault signal (Casistica: errore arbitrale)
    if (exp.key === 'no_fault') return { role: '1', steps: [
      { pose: 'whistle', whistle: true, caption: 'Fischio (per errore)', dur: 1.0 },
      { pose: 'doubleFault', caption: 'Azione da ripetere: segnaletica del doppio fallo, pollici alzati', dur: 1.8 },
      { pose: 'serve', side: handFor(M.serving, '1'), caption: 'Servizio alla squadra che aveva servito', dur: 1.6 },
    ], note: 'Fischiare senza fallo è un errore grave: l’azione si ripete, con la segnaletica del doppio fallo.' };
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
    const role = roleOverride || signalRole(exp.key, faultComp);
    const steps = [];
    if (!noWhistle) steps.push({ pose: 'whistle', whistle: true, caption: 'Fischio di fine azione', dur: 1.0 });
    if (role === '1') {
      steps.push({ pose: 'serve', side: handFor(exp.point, '1'), caption: 'Servizio alla ' + lbl(exp.point), dur: 1.5 });
      steps.push({ pose: sig.pose, side: handFor(faultTeam, '1'), motion: sig.motion, caption: sig.name, dur: 2.2 });
    } else {
      steps.push({ pose: 'rest', caption: 'Si porta dal lato della squadra in fallo (' + lbl(faultTeam) + ')', dur: 0.9, moveTo: sideOfTeam2(faultTeam) });
      steps.push({ pose: sig.pose, side: handFor(faultTeam, '2'), motion: sig.motion, caption: sig.name, dur: 2.2 });
      steps.push({ pose: 'rest', caption: 'Attende che il 1° arbitro assegni il servizio', dur: 1.2 });
      steps.push({ pose: 'serve', side: handFor(exp.point, '2'), caption: 'Ripete il gesto del 1° arbitro: servizio alla ' + lbl(exp.point), dur: 1.6 });
    }
    steps.push({ pose: 'rest', caption: 'Posizione di riposo', dur: 0.6 });
    return { role, steps, note: noWhistle ? 'Servizio senza tocchi: nel 2026/27 non si fischia, si esegue solo la segnaletica (Normative federali 4.7).' : null };
  }

  function eventSignalRaw(e, exp) {
    const lbl = E.TEAM_DEF[e.team].label;
    const W = c => ({ pose: 'whistle', whistle: true, caption: c || 'Fischio', dur: 1.0 });
    if (e.kind === 'libero' && exp === 'authorize') return { role: R2(), steps: [], note: 'Nessun gesto: i rimpiazzi regolari del Libero non si autorizzano con il fischio.' };
    if (e.kind === 'conduct') {
      const s = handFor(e.team, '1');
      if (exp === 'verbal') return { role: '1', steps: [], note: 'Avvertimento verbale: nessun cartellino, si comunica al capitano in gioco.' };
      if (exp === 'none') return { role: '1', steps: [] };
      const steps = [W('Fischio'), { pose: ({ yellow: 'yellow', red: 'red', expulsion: 'expulsion', disqualification: 'disqualification', delay_warning: 'delayY', delay_penalty: 'delayR' })[exp] || 'yellow', side: s,
        caption: (({ yellow: 'Cartellino giallo (avvertimento ufficiale)', red: 'Cartellino rosso (penalizzazione)', expulsion: 'Giallo e rosso insieme (espulsione)', disqualification: 'Giallo e rosso separati (squalifica)', delay_warning: 'Avvertimento per ritardo: cartellino giallo al polso', delay_penalty: 'Penalizzazione per ritardo: cartellino rosso al polso' }[exp]) || sanctionLabel(exp)) + ' ' + (e.who === 'team' ? 'alla squadra' : e.who === 'coach' ? 'all’allenatore' : 'al n. ' + e.who), dur: 2.2 }];
      if (exp === 'red' || exp === 'delay_penalty') steps.push({ pose: 'serve', side: handFor(E.other(e.team), '1'), caption: 'Servizio alla squadra avversaria', dur: 1.5 });
      const note = exp === 'delay_warning' || exp === 'delay_penalty'
        ? 'Per i ritardi di gioco non si chiama nessuno al seggiolone: il cartellino si porta al polso dal lato della squadra sanzionata (Indicazioni tecniche 4.5).'
        : 'Prima del cartellino il giocatore in campo (o il capitano in gioco, per chi non è in campo) viene invitato ad avvicinarsi al seggiolone (Indicazioni tecniche 3.6.2).';
      return { role: '1', steps, note };
    }
    const MOVE2 = R2() === '1' ? [] : [{ pose: 'rest', caption: e.kind === 'sub'
      ? 'Si porta con le spalle al palo della rete, rivolto verso la squadra che ha chiesto la sostituzione (' + lbl + ')'
      : 'Si sposta dal lato della squadra che ha fatto la richiesta (' + lbl + ')', dur: 0.9 }];
    if (exp === 'authorize' && e.kind === 'timeout') return { role: R2(), steps: [W('Fischio'), ...MOVE2, { pose: 'timeout', caption: 'Tempo di riposo: una mano verticale col palmo verso l’interno, l’altra sopra col palmo in basso (T)', dur: 1.8 }, { pose: 'serve', side: handFor(e.team, R2()), caption: 'Indica la squadra che lo ha chiesto (' + lbl + ')', dur: 1.4 }] };
    if (exp === 'authorize' && e.kind === 'sub') return { role: R2(), steps: [W('Fischio'), ...MOVE2, { pose: 'sub', motion: 'roll', once: true, caption: 'Sostituzione: pugni chiusi, gli avambracci fanno un giro completo uno intorno all’altro', dur: 2.0 }] };
    if (exp === 'reject_improper') return { role: R2(), steps: [W('Fischio'), { pose: 'improper', side: handFor(e.team, R2()), caption: 'Richiesta impropria respinta: braccio teso verso la squadra ma più in basso della spalla, palmo verso l’esterno e dorso verso il campo', dur: 2.0 }] };
    if (exp === 'delay_warning' || exp === 'delay_penalty') {
      const steps = [W('Il 1° arbitro fischia'), { pose: exp === 'delay_warning' ? 'delayY' : 'delayR', side: handFor(e.team, '1'), caption: (exp === 'delay_warning' ? 'Avvertimento per ritardo: cartellino giallo' : 'Penalizzazione per ritardo: cartellino rosso') + ' al polso dal lato della ' + lbl, dur: 2.2 }];
      if (exp === 'delay_penalty') steps.push({ pose: 'serve', side: handFor(E.other(e.team), '1'), caption: 'Servizio alla squadra avversaria', dur: 1.5 });
      return { role: '1', steps };
    }
    return { role: R2(), steps: [] };
  }
  /** Role whose view the 2nd referee's signals are drawn from: the 1st's when refereeing alone. */
  function R2() { return G.M.opts.mode === 'solo' ? '1' : '2'; }
  function eventSignal(e, exp) { return eventSignalRaw(e, exp); }


  /** Which team the referee of `role` sees on the left and on the right (1st: A left; 2nd: the opposite). */
  function sidesFor(role) {
    const leftTeam = G.M.side.A * (role === '2' ? -1 : 1) < 0 ? 'A' : 'B';
    return { left: leftTeam, right: leftTeam === 'A' ? 'B' : 'A' };
  }

  /** Small animated referee in a corner, for routine calls that do not open the feedback. */
  function miniSignal(sig, who) {
    if (!sig || !sig.steps.length) return;
    const box = $('gesture-mini');
    box.hidden = false;
    $('gm-who').textContent = who || (sig.role === '2' ? '2° arbitro' : '1° arbitro');
    // The automatic referee also makes the signal "live", on their character in the scene
    const mode = G.M.opts.mode, mine = mode === '2' ? '2' : '1';
    if (G.R && (G.phase === 'rally' || G.phase === 'decision')) (G.rallySigs = G.rallySigs || []).push({ sig, t: G.t });
    if (mode !== 'solo' && sig.role !== mine) G.liveSig = { role: sig.role, steps: sig.steps, t0: performance.now(), sides: sidesFor(sig.role) };
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
  /**
   * The referee's signal in the replay: it starts at the whistle (or at the end of the rally when there is no
   * whistle) and the recording runs until the signal is over.
   */
  function attachSig(rp, sig, at) {
    if (!rp || !sig || !sig.steps || !sig.steps.length) return;
    const start = at != null ? at : rp.whistle != null ? rp.whistle : rp.focus + 0.3;
    const total = sig.steps.reduce((a, x) => a + (x.dur || 1.6), 0);
    let lead = 0;
    for (const st of sig.steps) { if (!st.whistle) break; lead += st.dur || 1.6; }
    rp.sigs = rp.sigs || [];
    if (rp.sigs.some(x => x.sig === sig || (x.sig.role === sig.role && Math.abs(x.t - start) < 0.05))) return;
    rp.sigs.push({ sig, t: start, mark: start + lead, total });
    rp.to = Math.max(rp.to, start + total + 0.3);
  }

  /**
   * Replay: where the 2nd referee is at time t when his signal includes moving to a side (moveTo step):
   * from where he was to that side during the step, then he stays there. null when there is no such move.
   */
  function replay2X(t, base) {
    const I = RP.info;
    if (!I || !I.sigs) return null;
    let x = null;
    I.sigs.filter(s => s.sig.role === '2' && t >= s.t).forEach(s => {
      let at = s.t;
      for (const st of s.sig.steps) {
        const d = st.dur || 1.6;
        if (st.moveTo != null && t >= at) { const k = clamp((t - at) / d, 0, 1), e = k * k * (3 - 2 * k); x = base + (st.moveTo - base) * e; }
        at += d;
      }
    });
    return x;
  }

  /** The signal of a referee that is being made at time t of the replay (the latest started one). */
  function activeSig(info, role, t) {
    let best = null;
    (info.sigs || []).forEach(x => { if (x.sig.role === role && t >= x.t && t <= x.t + x.total + 0.4 && (!best || x.t > best.t)) best = x; });
    return best;
  }

  function showFeedback(info, onContinue) {
    $('collab-panel').hidden = true;
    // The signal made by whoever decided (you, or the other referee when you did not whistle) goes in the replay
    if (info.replay) {
      const own = !info.by && info.yourSignal && info.yourSignal.steps && info.yourSignal.steps.length;
      const sg = own ? info.yourSignal : info.signal;
      // The signals made during the rally by the other referee (collaboration, whistle), then the decision's one
      if (G.R && info.replay.R === G.R) {
        (G.rallySigs || []).forEach(x => attachSig(info.replay, x.sig, x.t));
        if (own) (G.rallySigs = G.rallySigs || []).push({ sig: sg, t: info.replay.whistle != null ? info.replay.whistle : info.replay.focus + 0.3 });
      }
      attachSig(info.replay, sg);
    }
    G.lastCall = { by: info.by || userRole(), user: !info.by, ok: info.ok, warn: !!info.warn, yours: info.yours, right: info.right, happened: info.happened };
    const last = G.decisions[G.decisions.length - 1];
    if (last && !last.signal && info.signal && info.signal.steps.length) {
      last.signal = info.signal;
      last.sides = sidesFor(info.signal.role);
      last.right = info.right;
    }
    // Routine correct calls (clear ball in/out) close quickly, without interrupting the game
    if (info.quiet && !info.warn) {
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
  function replayInfo(R, fault, missed, whistleAt) {
    if (!R) return null;
    const focus = fault ? fault.t : (R.natural ? R.natural.t : R.endT);
    // When the whistle came: the user's, or (rally ended without it / another referee) the current game time
    const wT = whistleAt != null ? whistleAt : R === G.R ? (G.whistleT != null ? G.whistleT : G.aiWhistleT != null ? G.aiWhistleT : G.t) : null;
    const start = Math.max(0, Math.min(focus - 3.2, R.tHit - 0.6, wT != null ? wT - 1 : Infinity));
    const end = Math.max(Math.min(R.endT + 0.4, focus + 1.6), wT != null ? wT + 0.8 : 0);
    return { R, focus, whistle: wT, from: Math.max(start, Math.min(focus, wT != null ? wT : focus) - 4.5), to: end, missed,
      label: fault ? 'Momento del fallo' : 'Fine dell’azione' };
  }
  const RP = { raf: 0, playing: true, slow: false, top: false, t: 0, renderer: null, info: null, last: 0 };
  function openReplay(info) {
    if (!info) return;
    RP.info = info; RP.t = info.from; RP.playing = true; RP.last = performance.now(); RP.top = false;
    // The replay can be watched from the 1st referee's chair, from the 2nd referee's place (if there is a 2nd) or from above
    RP.view = G.view || 'first';
    $('rp-v2').hidden = !G.M || G.M.opts.mode === 'solo';
    $('rp-range').min = info.from; $('rp-range').max = info.to; $('rp-range').step = 0.01;
    const pos = t => ((t - info.from) / (info.to - info.from) * 100) + '%';
    // Each moment is a band over its whole duration (semi-transparent, the time bar stays visible)
    const band = (el, a, b) => { const x0 = Math.max(info.from, a), x1 = Math.min(info.to, Math.max(b, a + 0.12)); el.style.left = pos(x0); el.style.width = 'calc(' + pos(x1) + ' - ' + pos(x0) + ')'; };
    band($('rp-mark'), info.focus - 0.15, info.focus + 0.45);
    $('rp-mark').title = info.label;
    $('rp-mark-w').hidden = info.whistle == null;
    if (info.whistle != null) band($('rp-mark-w'), info.whistle, info.whistle + 0.6);
    $('rp-legend-f').textContent = info.label;
    $('rp-legend-w').hidden = info.whistle == null;
    const sigs = info.sigs || [];
    $('rp-legend-s').hidden = !sigs.length;
    $('rp-mark-s').hidden = true;
    document.querySelectorAll('.rp-bar .rp-mark.s.extra').forEach(x => x.remove());
    sigs.forEach(x => {
      const m = document.createElement('span');
      m.className = 'rp-mark s extra';
      m.title = 'Segnaletica · ' + (x.sig.role === '2' ? '2° arbitro' : '1° arbitro');
      band(m, x.mark, x.t + x.total);
      $('rp-mark-s').parentNode.appendChild(m);
    });
    $('rp-sig').hidden = true; $('rp-sig2').hidden = true;
    $('replay-title').textContent = info.title || 'Registrazione dell’azione';
    $('rp-note').textContent = info.event ? info.snap.event.text : info.missed ? 'Il fallo non è stato fischiato: guarda il momento evidenziato sulla barra.' : 'Rivedi l’azione: il segno sulla barra indica il momento decisivo.';
    openModal('replay-modal');
    if (!RP.renderer) RP.renderer = new SimRender.Renderer($('rp-canvas'));
    applyGraphics();
    RP.renderer.resize();
    RP.renderer.setView(G.view);
    RP.baseX = G.refX || 0;
    RP.renderer.setRefX && RP.renderer.setRefX(RP.baseX);
    replayButtons();
    cancelAnimationFrame(RP.raf);
    RP.raf = requestAnimationFrame(replayTick);
  }
  function replayButtons() {
    $('rp-play').textContent = RP.playing ? '❚❚ Pausa' : '▶ Riproduci';
    $('rp-slow').classList.toggle('active', RP.slow);
    $('rp-top').classList.toggle('active', RP.top);
    $('rp-v1').classList.toggle('active', !RP.top && RP.view === 'first');
    $('rp-v2').classList.toggle('active', !RP.top && RP.view === 'second');
  }
  function replayTick(now) {
    const info = RP.info;
    if (!info || $('replay-modal').hidden) return;
    const dt = Math.min(0.25, (now - RP.last) / 1000);
    RP.last = now;
    if (RP.playing) {
      // Slower around the decisive moment, and always when "Rallenty" is on
      const near = Math.abs(RP.t - info.focus) < 0.6;
      RP.t += dt * (RP.slow ? 0.3 : near ? 0.5 : 1);
      if (RP.t > info.to) RP.t = info.from;
    }
    $('rp-range').value = RP.t;
    const nearF = Math.abs(RP.t - info.focus) < 0.35, nearW = info.whistle != null && Math.abs(RP.t - info.whistle) < 0.35;
    $('rp-badge').hidden = !(nearF || nearW);
    $('rp-badge').textContent = nearW && (!nearF || Math.abs(RP.t - info.whistle) < Math.abs(RP.t - info.focus)) ? 'Fischio' : info.label;
    $('rp-badge').classList.toggle('w', !$('rp-badge').hidden && $('rp-badge').textContent === 'Fischio');
    // The camera stands where the referee of that view is (the 2nd moving to a side if his signal says so)
    if (G.M && RP.renderer.setRefX && !RP.top) {
      const rx = !info.snap && refsAt(info.R, RP.t, info.whistle);
      const m = G.M.opts.mode, x1 = rx ? rx.x1 : m === '2' ? G.partnerX : G.refX, x2 = rx ? rx.x2 : m === '2' ? G.refX : G.partnerX;
      if (RP.view === 'second') { const mx = replay2X(RP.t, x2 || 0); RP.renderer.setRefX(mx != null ? mx : (x2 || 0)); }
      else RP.renderer.setRefX(x1 || 0);
    }
    draw({ renderer: RP.renderer, R: info.R || null, t: RP.t, snap: info.snap });
    // The referees' signals (1st and 2nd), each in its own box, in sync with the recording
    [['1', ''], ['2', '2']].forEach(([role, id]) => {
      const x = activeSig(info, role, RP.t), box = $('rp-sig' + id);
      box.hidden = !x;
      if (x) {
        $('rp-sig' + id + '-who').textContent = 'Segnaletica · ' + (role === '2' ? '2° arbitro' : '1° arbitro');
        $('rp-sig' + id + '-cap').textContent = SimGestures.frame($('rp-sig' + id + '-fig'), x.sig.steps, RP.t - x.t, sidesFor(role));
      }
    });
    RP.raf = requestAnimationFrame(replayTick);
  }
  /**
   * Replay of a reported rally (from Gestione › Segnalazioni): the report holds the whole recorded action
   * (formations, movements, ball, faults, whistle), so it is rebuilt here and played in the replay window.
   */
  function openReportedReplay() {
    let d = null;
    try { d = JSON.parse(localStorage.getItem('fipav-sim-replay') || 'null'); } catch (e) { d = null; }
    if (!d || !d.action || !d.action.moves) {
      $('boot-message').textContent = '';
      banner('Registrazione non disponibile', 'Apri «Rivedi azione» dalla segnalazione nel pannello di gestione.', 5000);
      return;
    }
    const o = d.options || {};
    const M = new E.Match({ mode: d.mode || '1', freq: o.freq, fouls: o.fouls, formations: o.formations || 'easy', conduct: o.conduct, libero: o.libero !== false, target: d.target || 25 });
    ['A', 'B'].forEach(T => {
      const t = M.teams[T];
      if (d.lineups && d.lineups[T]) { t.lineup = d.lineups[T].slice(); t.libero.on = null; }
      if (d.sides) M.side[T] = d.sides[T];
    });
    if (d.score) { const [a, b] = String(d.score).split('-').map(Number); M.score = { A: a || 0, B: b || 0 }; }
    G.M = M; G.R = null; G.view = d.mode === '2' ? 'second' : 'first'; G.refX = 0; G.partnerX = 0; G.prepFrom = {};
    const a = d.action;
    const sv = (a.server && a.server.team) || d.serving || 'A';
    const R = { plan: a.plan || {}, tHit: a.tHit, endT: a.endT, server: a.server, serving: sv, receiving: E.other(sv), faults: a.faults || [], natural: a.natural, trap: a.trap,
      formations: a.formations || {}, events: a.events || [], ball: a.ball || [], keys: a.keys || {}, moves: a.moves || {}, holdUntil: a.holdUntil || null, slow: [] };
    const fault = (a.faults || [])[0] || null;
    const info = replayInfo(R, fault && Math.abs(fault.t - a.focus) < 0.01 ? fault : null, false, a.whistle != null ? a.whistle : null);
    if (a.focus != null) info.focus = a.focus;
    info.title = 'Azione segnalata · ' + (d.code || ('SIM-' + d.sit)) + ' · ' + (d.role || '') + ' · ' + (d.score || '');
    openReplay(info);
    RP.locked = true;
    $('rp-close').hidden = true;
    $('rp-note').textContent = [d.happened, d.yours ? 'Decisione presa: ' + d.yours : '', d.right ? 'Indicata come corretta: ' + d.right : '', d.reportText ? 'Segnalazione: «' + d.reportText + '»' : ''].filter(Boolean).join(' · ');
  }

  function closeReplay() {
    // A reported action opened from Gestione › Segnalazioni: the window stays open (there is no match behind it)
    if (RP.locked) return;
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
    c.className = 'dcard ' + (info.warn ? 'warn' : info.ok ? 'ok' : 'ko');
    $('dc-icon').textContent = info.warn ? '!' : info.ok ? '✓' : '✕';
    $('dc-title').textContent = info.warn ? 'Attenzione: punto giusto, fallo diverso' : info.ok ? 'Decisione corretta' : 'Decisione da rivedere';
    $('dc-yours').textContent = info.yours + (info.warn ? ' · da fischiare: ' + info.right : info.ok ? '' : ' · corretta: ' + info.right);
    // With a warning, the card shows the correct signal (the fault that ended the rally)
    const sig = ((info.ok || info.warn) ? info.signal : info.yourSignal) || { role: '1', steps: [] };
    if (!sig.steps.length && !sig.note) sig.note = 'Nessuna segnaletica.';
    $('dc-sig').classList.toggle('ko', !info.ok);
    $('dc-sig').classList.toggle('warn', !!info.warn);
    $('dc-sig-label').textContent = info.warn ? 'Segnaletica corretta (del fallo che ha chiuso l’azione)' : 'La tua segnaletica';
    $('dc-replay').hidden = !info.replay;
    fillSignal('dc', sig);
    c.hidden = false;
    G.modal = 'dcard';
    setTimeout(() => $(info.ok && !info.warn ? 'dc-continue' : 'dc-details').focus(), 50);
    // A correct call goes on by itself once the signal is over (not with a warning: it is worth reading)
    if (info.ok && !info.warn) {
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
    $('fb-head').className = 'fb-head ' + (info.warn ? 'warn' : info.ok ? 'ok' : 'ko');
    $('fb-icon').textContent = info.warn ? '!' : info.ok ? '✓' : '✕';
    $('feedback-title').textContent = info.warn ? 'Attenzione: punto giusto, fallo diverso' : info.ok ? 'Decisione corretta' : 'Decisione da rivedere';
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
    if ((!info.ok || info.warn) && ys && (ys.steps.length || ys.note)) {
      y.hidden = false;
      y.classList.toggle('warn', !!info.warn);
      $('fy-mark').innerHTML = info.warn ? '<span>!</span> Gesto da correggere: il punto è giusto, ma il fallo da segnalare è un altro' : '<span>✕</span> Errore';
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
  /**
   * Everything an admin needs to understand (and recreate) the reported action: match state, decisions and,
   * for a rally, its whole timeline (ball, players, faults), since the recording is not available later.
   */
  function reportDetail(info) {
    const M = G.M;
    const lineup = T => [0, 1, 2, 3, 4, 5].map(k => M.onCourt(T, k));
    const d = {
      v: 1, mode: M.opts.mode, role: roleName(M.opts.mode), score: M.score.A + '-' + M.score.B, target: M.opts.target,
      serving: M.serving, sit: info.sitId, happened: info.happened || '', yours: info.yours || '', right: info.right || '', note: info.note || null,
      lineups: { A: lineup('A'), B: lineup('B') }, libero: { A: M.teams.A.libero.on, B: M.teams.B.libero.on }, sides: { A: M.side.A, B: M.side.B },
      timeouts: { A: M.teams.A.timeouts, B: M.teams.B.timeouts }, subs: { A: M.teams.A.subs.count, B: M.teams.B.subs.count },
      sanctions: { A: M.teams.A.sanctions.slice(), B: M.teams.B.sanctions.slice() },
      options: { freq: M.opts.freq, fouls: M.opts.fouls, formations: M.opts.formations || 'easy', conduct: M.opts.conduct, aids: M.opts.aids, scoreMine: !!M.opts.scoreMine, libero: M.opts.libero },
      clock: Math.round(G.clock || 0), rally: M.completedRallies + 1,
    };
    const rp = info.replay, R = rp && rp.R;
    if (R) {
      d.action = {
        plan: R.plan, tHit: R.tHit, endT: R.endT, whistle: rp.whistle, focus: rp.focus, server: R.server, serving: R.serving, receiving: R.receiving,
        faults: R.faults, natural: R.natural, trap: R.trap || null, formations: R.formations,
        events: R.events, ball: R.ball, keys: R.keys, moves: R.moves, holdUntil: R.holdUntil || null,
      };
    } else if (G.event) {
      const e = G.event;
      d.event = { kind: e.kind, team: e.team, who: e.who, out: e.out, inn: e.inn, expected: e.expected, text: e.text, comp: e.comp };
    }
    return d;
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
      detail: reportDetail(fbInfo),
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
      '1': ['Prima del servizio: le squadre sono pronte e il giocatore al servizio ha la palla? Allora fischia e autorizza.', 'Al colpo di servizio: piedi del giocatore al servizio e posizioni della squadra al servizio.', 'Durante l’azione: tocchi di palla (trattenuta, doppio, quattro tocchi), rete dal lato dell’attacco, attacchi di difensori e Libero.', 'Alla fine: palla dentro, fuori o toccata.'],
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
      const onC = [0, 1, 2, 3, 4, 5].map(k => M.onCourt(T, k)).includes(d.captain);
      html += line('Capitano in gioco', onC ? 'n. ' + d.captain + ' (il capitano, in campo)'
        : (t.gcAsked && t.gcAsked === M.gameCaptain(T) ? 'n. ' + t.gcAsked + ' · ' : 'capitano n. ' + d.captain + ' non in campo · ') + '<button class="btn btn-ghost btn-sm sh-cap" data-cap="' + T + '">Fischia e chiedi</button>');
      html += line('Formazione iniziale', d.lineup.map((n, i) => roman[i] + ' ' + n).join(' · '));
      html += line('Tempi di riposo', t.timeouts + ' di 2');
      html += line('Sostituzioni', t.subs.count + ' di ' + E.MAX_SUBS + (t.subs.list.length ? ': ' + t.subs.list.join(', ') : ''));
      html += line('Rimpiazzi del Libero', t.libero.log.length ? t.libero.log.slice(-4).join(', ') + (t.libero.lastRally === M.completedRallies ? ' (in questa interruzione)' : '') : '—');
      html += line('Richieste improprie', t.improper ? String(t.improper) : '—');
      html += line('Sanzioni', t.sanctions.length ? t.sanctions.join(', ') : '—');
      html += '</div>';
    });
    $('sheet-body').innerHTML = html;
    $('sheet-body').querySelectorAll('[data-cap]').forEach(b => b.addEventListener('click', () => askCaptain(b.dataset.cap)));
  }

  // ======================================================================================
  // Modals and pause
  // ======================================================================================
  function openModal(id) {
    $(id).hidden = false; G.modal = id;
    // Score and line-up slips stay above the decision windows only (they can be checked while deciding)
    if (id !== 'decision-modal') $('slip-card').hidden = true;
    syncScoreFront();
  }
  function syncScoreFront() { /* the scoreboard stays under every window (the slips open from the decision window) */ }
  function closeModal(id) {
    $(id).hidden = true;
    const still = ['replay-modal', 'decision-modal', 'feedback-modal', 'help-modal', 'pause-modal', 'report-modal', 'opts-modal'].find(m => !$(m).hidden);
    G.modal = still || (!$('pick-layer').hidden ? 'pick' : !$('dcard').hidden ? 'dcard' : PP.active ? 'person' : null);
    syncScoreFront();
  }
  function closeModals() { if (G.toWait) { G.toWait = null; $('to-wait').hidden = true; } cancelAnimationFrame(RP.raf); $('peek-bar').hidden = true; G.pendingConduct = null; if (PP.active) endPick(false); ['replay-modal', 'decision-modal', 'feedback-modal', 'help-modal', 'pause-modal', 'report-modal'].forEach(m => { $(m).hidden = true; }); closeCard(); G.modal = null; }

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
  /** The member not on court who gets a card through the game captain stands up while the captain is at the chair. */
  function approachStanding(T, who) {
    const A = G.approach;
    if (!A || A.T !== T || A.standing !== who || G.phase !== 'break') return false;
    const c = (G.clock || 0) - A.t0;
    return c >= A.walk * 0.8 && c <= A.walk + A.hold + 0.3;
  }

  function personFor(T, num, p) {
    const d = E.TEAM_DEF[T];
    const isL = num === d.libero;
    return {
      id: T + num, x: p.x, y: p.y, z: p.z || 0, pose: p.pose || 'ready', number: num,
      jersey: isL ? d.liberoJersey : d.jersey, numberColor: isL ? d.liberoNumber : '#fff',
      shorts: d.shorts, trim: isL ? null : d.trim, hair: d.hair, skin: (num * 7) % 4,
      captain: num === d.captain, h: 1.82 + ((num * 13) % 9) / 50, hairStyle: (num * 5) % 3,
    };
  }

  /** Ball contacts of each player in a rally (key -> [{ t, what }]), built once per rally (not stored in it). */
  const CONTACTS = new WeakMap();
  /**
   * What a player is doing around time t, for the detailed graphics (hands on the ball, run-up, jumps):
   * the contacts close in time (dt = t - contact), the jump around t (dt from its peak, its kind from the pose),
   * the held ball (k from 0 to 1 while the setter holds it).
   */
  function actOf(R, key, t) {
    let c = CONTACTS.get(R);
    if (!c) {
      c = {};
      R.events.forEach(e => {
        if ((e.kind === 'contact' || e.kind === 'block-touch') && e.team) (c[e.team + e.num] = c[e.team + e.num] || []).push({ t: e.t, what: e.kind === 'block-touch' ? 'block' : e.what });
      });
      CONTACTS.set(R, c);
    }
    const tr = R.moves[key], a = { touches: (c[key] || []).map(q => ({ dt: t - q.t, what: q.what })).filter(q => q.dt > -1.6 && q.dt < 1.2) };
    const z = tr.z;
    for (let i = 1; i < z.length - 1; i++) {
      if (!(z[i].z > 0)) continue;
      const t0 = z[i - 1].t, tp = z[i].t, t1 = z[i + 1].t;
      if (t < t0 - 0.7 || t > t1 + 0.45) continue;
      let kind = 'ready';
      for (const q of tr.pose) if (q.t <= tp) kind = q.pose;
      a.jump = { dt: t - tp, rise: tp - t0, fall: t1 - tp, kind };
      break;
    }
    const h = R.holdUntil;
    if (h && t >= h.t0 && t <= h.t1 + 0.6 && (c[key] || []).some(q => Math.abs(q.t - h.t0) < 0.01)) {
      if (t <= h.t1) a.hold = (t - h.t0) / Math.max(0.01, h.t1 - h.t0);
      else a.released = t - h.t1;
    }
    return a;
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
        if (event && event.team === T && (event.kind === 'sub' || event.kind === 'libero') && event.inn === n) return;
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
          // Walking to the bench for a time-out
          const H = !rp && G.huddle;
          if (H && H.from[key] && phase === 'break') {
            const k = clamp(((G.clock || 0) - H.t0) / H.dur, 0, 1), e2 = k * k * (3 - 2 * k);
            p = { x: H.from[key].x + (H.to[key].x - H.from[key].x) * e2, y: H.from[key].y + (H.to[key].y - H.from[key].y) * e2, z: 0, pose: k < 1 ? 'walk' : 'stand' };
          }
          // Called to the referee's chair for a sanction, then back
          const A = !rp && G.approach;
          if (A && A.key === key && phase === 'break') {
            const c = (G.clock || 0) - A.t0;
            const u = c < A.walk ? c / A.walk : c < A.walk + A.hold ? 1 : Math.max(0, 1 - (c - A.walk - A.hold) / A.walk);
            const e2 = u * u * (3 - 2 * u), moving = (c < A.walk) || (c > A.walk + A.hold && u > 0);
            p = { x: A.from.x + (A.to.x - A.from.x) * e2, y: A.from.y + (A.to.y - A.from.y) * e2, z: 0, pose: moving ? 'walk' : 'stand', atChair: c >= A.walk && c <= A.walk + A.hold };
          }
        }
        const SA = !rp && G.subAnim;
        if (SA && SA.T === T && SA.inn === n) {
          const k = clamp(((G.clock || 0) - SA.t0) / SA.dur, 0, 1), e2 = k * k * (3 - 2 * k);
          p = { x: SA.zone[0] + (SA.court.x - SA.zone[0]) * e2, y: SA.zone[1] + (SA.court.y - SA.zone[1]) * e2, z: 0, pose: k < 1 ? 'walk' : 'ready' };
        }
        const person = personFor(T, n, p);
        if (R && (phase === 'rally' || phase === 'decision') && R.moves[key]) person.act = actOf(R, key, t);
        // The server bouncing the ball before the toss
        if (R && R.server.team === T && R.server.num === n && (phase === 'prep' || ((phase === 'rally' || phase === 'decision') && t < R.ball[0].t0))) (person.act = person.act || {}).dribble = true;
        if (R && (person.pose === 'attack' || person.pose === 'serve') && R.moves[key]) {
          // Arm swing: cocked while rising, hitting at the top of the jump, following through while landing
          const a = M.playerAt(R, key, t - 0.08), b2 = M.playerAt(R, key, t + 0.08);
          person.swing = clamp(0.5 - ((b2 ? b2.z : 0) - (a ? a.z : 0)) * 3.2, 0, 1);
        }
        if (p.atChair) { person.bubble = 'Al seggiolone'; person.highlight = 'rgba(255,213,79,.55)'; }
        if (!rp && G.capReveal && G.capReveal.T === T && G.capReveal.n === n && performance.now() < G.capReveal.until) {
          // The game captain raises an arm: to be identified, and to show the referees he knows he is the game captain
          person.bubble = 'Capitano in gioco'; person.highlight = 'rgba(255,213,79,.55)';
          if (performance.now() - G.capReveal.t0 > 1100) person.pose = 'wave';
        }
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
      let bench = d.roster.filter(n => !onCourtNums.includes(n) && !(rp && rp.snap ? false : team.out && team.out[n]));
      if (!onCourtNums.includes(d.libero)) bench.push(d.libero);
      const e = event;
      bench = bench.filter(n => !(e && e.team === T && e.inn === n));
      // Substitution animation: the player going out walks from the court to the zone
      const SA = !rp && G.subAnim;
      if (SA && SA.T === T) {
        const k = clamp(((G.clock || 0) - SA.t0) / SA.dur, 0, 1);
        if (k < 1) {
          bench = bench.filter(n => n !== SA.out);
          const e2 = k * k * (3 - 2 * k);
          people.push(personFor(T, SA.out, { x: SA.court.x + (SA.zone[0] - SA.court.x) * e2 + M.side[T] * 0.5 * e2, y: SA.court.y + (SA.zone[1] - SA.court.y) * e2, pose: 'walk' }));
        }
      }
      bench.forEach((n, i) => {
        const x = b.x0 + (b.x1 - b.x0) * (i + 0.5) / Math.max(bench.length, 1);
        const q = personFor(T, n, { x, y: 7.65, pose: 'sit' });
        if (e && e.kind === 'conduct' && e.team === T && e.who === n) { q.bubble = '!!'; q.bubbleColor = '#ffcdd2'; q.pose = 'stand'; q.highlight = 'rgba(255,213,79,.55)'; }
        if (approachStanding(T, n)) { q.pose = 'wave'; q.bubble = 'Si alza'; q.highlight = 'rgba(255,213,79,.55)'; }
        people.push(q);
      });
      const coach = { id: 'coach' + T, x: b.coach, y: 6.9, pose: 'stand', jersey: d.jersey, shorts: '#262c38', outfit: 'staff', collar: '#ffffff', hair: '#2b2420', h: 1.8, skin: T === 'A' ? 1 : 2, label: 'All. ' + T };
      if (e && e.team === T && e.kind === 'timeout') { coach.pose = 'gesture-t'; coach.bubble = 'T'; }
      if (approachStanding(T, 'coach')) { coach.pose = 'wave'; coach.bubble = 'Si alza'; coach.highlight = 'rgba(255,213,79,.55)'; }
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
    // Replay: where the referees were at that moment of the rally
    const rx = rp && !snap ? refsAt(R, t, RP.info && RP.info.whistle) : null;
    const firstX = rx ? rx.x1 : mode === '2' ? G.partnerX : G.refX;
    const secondX = rx ? rx.x2 : mode === '2' ? G.refX : G.partnerX;
    const live = role => {
      let L = G.liveSig, el;
      if (rp) {
        // Replay: the recorded signal of that referee, at the replay time
        const I = RP.info;
        const x = I && activeSig(I, role, rp.t);
        if (!x) return null;
        L = { steps: x.sig.steps, sides: sidesFor(role) };
        el = rp.t - x.t;
      } else {
        if (!L || L.role !== role) return null;
        el = (performance.now() - L.t0) / 1000;
      }
      const total = L.steps.reduce((a, x) => a + (x.dur || 1.6), 0);
      if (el > total + 0.5) { if (!rp) G.liveSig = null; return null; }
      const a = SimGestures.liveArms(L.steps, el);
      // Which way the drawing's left arm points on screen: towards the team the signal puts on the left
      const x = role === '1' ? firstX : secondX, y = role === '1' ? -5.6 : 5.25;
      const pr = renderer.cam.project([x, y, 1.6]), pt = renderer.cam.project([M.side[L.sides.left] * 5, y, 1.6]);
      a.flip = pr && pt && pt.x > pr.x ? -1 : 1;
      return a;
    };
    const ref1 = { x: firstX, y: -5.6, z: 1.55, pose: 'stand', live: live('1'), jersey: '#1f2633', shorts: '#14171d', outfit: 'ref', collar: '#e9edf3', hair: '#2b2420', h: 1.8, skin: 0, label: '1° arbitro', hiddenIn: 'first' };
    people.push(ref1);
    let ref2 = null;
    if (mode !== 'solo') {
      let sx = secondX;
      // Replay: the 2nd referee moves to the side of the team at fault before the signal, as in the recording
      if (rp) { const mx = replay2X(rp.t, sx); if (mx != null) sx = mx; }
      const atPost = !rp && timeoutStill() && Math.abs(sx) < 0.25;
      ref2 = { x: sx, y: 5.25, pose: 'stand', back: atPost, live: live('2'), jersey: '#1f2633', shorts: '#14171d', outfit: 'ref', collar: '#e9edf3', hair: '#4a3426', hairStyle: 1, h: 1.78, skin: 1, label: '2° arbitro', hiddenIn: 'second' };
      people.push(ref2);
    }
    const scorer = { x: 0, y: 7.55, pose: 'sit', jersey: '#5b6b88', shorts: '#2a3140', outfit: 'staff', collar: '#cfd6e2', hair: '#1f1712', hairStyle: 2, h: 1.74, skin: 2, label: 'Segnapunti' };
    // "All in order" of the scorer (arms raised, seated)
    if (!rp && G.allOk) {
      const el = (performance.now() - G.allOk.t0) / 1000;
      if (el < 2.1) { scorer.live = SimGestures.liveArms(ALL_OK, el); scorer.live.flip = 1; } else G.allOk = null;
    }
    // The scorer's check of the request on the score sheet
    if (event && event.scorerSays === 'ok') { scorer.bubble = '✓ Regolare'; scorer.bubbleColor = '#c8f0d4'; }
    if (event && event.scorerSays === 'ko') { scorer.bubble = '✕ Irregolare'; scorer.bubbleColor = '#ffcdd2'; }
    people.push(scorer);

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
      // Where the referees look: at the service the 1st watches the serving team, the 2nd the receiving team
      // (positions); then both follow the ball
      const svc = phase === 'prep' || ((phase === 'rally' || phase === 'decision') && t < R.tHit + 0.25);
      ref1.lookX = svc ? M.side[R.serving] * 7 : ball ? ball.x : null;
      if (ref2) ref2.lookX = svc ? M.side[R.receiving] * 4.5 : ball ? ball.x : null;
    }

    // Net shake and impact mark (visual aids)
    let netShake = null;
    const shake = rp ? (() => { if (!R) return null; const n = R.events.filter(ev => ev.kind === 'net' && ev.t <= t).pop(); return n ? { y: n.y, t0: n.t, amp: n.strong ? 0.09 : 0.05 } : null; })() : G.netShake;
    if (shake && R) {
      // The net keeps swinging for a few seconds, slowly settling (live: real time, so it goes on after the whistle)
      const age = !rp && shake.w0 ? (performance.now() - shake.w0) / 1000 * (G.speed || 1) : t - shake.t0;
      if (age >= 0 && age < 3.2) netShake = { y: shake.y, amp: shake.amp * Math.exp(-age * 1.25) * Math.min(1, age / 0.06 + 0.3), phase: age * 21 };
    }
    const markers = [];
    if (R && M.opts.aids && R.natural && R.natural.land && R.natural.borderline && t >= R.natural.t) {
      markers.push({ x: R.natural.land[0], y: R.natural.land[1], r: E.BALL_R, color: '#ffd54f', fill: 'rgba(255,213,79,.35)' });
    }

    // The referee turns their head to follow the play (the server before the service, then the ball)
    if (rp ? !RP.top : !G.top) {
      let lx = 0;
      // At the service the 1st referee watches the serving team, the 2nd the receiving team (positions)
      const serviceTime = R && (phase === 'prep' || t < R.tHit + 0.25);
      if (serviceTime && (rp ? RP.view : G.view) === 'second') lx = M.side[R.receiving] * 3.2;
      else if (ball) lx = ball.x * 0.4;
      else if (R) lx = M.side[R.server.team] * 3.2;
      const dt = Math.min(0.05, (performance.now() - ((rp ? RP.lastLook : G.lastLook) || performance.now())) / 1000);
      if (rp) RP.lastLook = performance.now(); else G.lastLook = performance.now();
      renderer.lookAt(clamp(Number.isFinite(lx) ? lx : 0, -3.6, 3.6), rp ? dt : (G.paused || G.modal ? 0 : dt));
      // You as 2nd referee: during a time-out you face the scorer (back to the post); authorising a substitution
      // you turn towards the team that asked for it
      if (!rp && G.view === 'second' && renderer.setGaze) {
        const gz = timeoutStill() ? { scorer: true } : (G.r2Face && performance.now() < G.r2Face.until ? { x: G.r2Face.x } : null);
        renderer.setGaze(gz, G.paused || G.modal ? 0 : dt);
      }
    }
    if (!rp && PP.active) people.forEach(q => {
      if (!q.id) return;
      if (q.id === PP.chosen) q.highlight = 'rgba(255,213,79,.7)';
      else if (q.id === PP.hover) q.highlight = 'rgba(255,255,255,.5)';
    });
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
    $('btn-collab').addEventListener('click', openCollab);
    $('btn-pause').addEventListener('click', pause);
    $('btn-opts').addEventListener('click', openOptions);
    $('btn-allok').addEventListener('click', userAllOk);
    ['a', 'b'].forEach(x => $('slip-btn-' + x).addEventListener('click', () => openSlip(x.toUpperCase())));
    $('slip-close').addEventListener('click', () => { $('slip-card').hidden = true; });
    $('pause-opts').addEventListener('click', openOptions);
    $('opts-apply').addEventListener('click', () => closeOptions(true));
    $('opts-cancel').addEventListener('click', () => closeOptions(false));
    $('pause-resume').addEventListener('click', resume);
    $('pause-end').addEventListener('click', () => { closeModals(); endMatch(true); });
    $('btn-view').addEventListener('click', () => { G.top = !G.top; applyView(); });
    $('btn-sheet').addEventListener('click', () => toggleSheet());
    $('sheet-close').addEventListener('click', () => toggleSheet(false));
    $('btn-log').addEventListener('click', () => toggleLog());
    $('peek-back').addEventListener('click', endPeek);
    $('to-wait-skip').addEventListener('click', () => { if (G.toWait && G.toWait.end) G.toWait.end(); });
    bindPersonPicking();
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
    $('rp-top').addEventListener('click', () => { RP.top = !RP.top; RP.renderer.setView(RP.top ? 'top' : RP.view); replayButtons(); });
    $('rp-v1').addEventListener('click', () => { RP.top = false; RP.view = 'first'; RP.renderer.setView('first'); replayButtons(); });
    $('rp-v2').addEventListener('click', () => { RP.top = false; RP.view = 'second'; RP.renderer.setView('second'); replayButtons(); });
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
      else if (e.key === 'Escape' && PP.active) { e.preventDefault(); endPick(); }
      else if (e.key === ' ' && G.modal === 'replay-modal') { e.preventDefault(); RP.playing = !RP.playing; replayButtons(); }
      else if (e.key === 'Escape' && G.modal === 'opts-modal') closeOptions(false);
      else if (G.modal === 'opts-modal') return;
      else if (e.key === 'Escape' && G.modal === 'pause-modal') resume();
      else if (k === 'p') pause();
      else if (k === 'v' && !G.modal) { G.top = !G.top; applyView(); }
      else if (k === 'r') toggleSheet();
      else if (k === 'c') toggleLog();
      else if (k === 'h' && !G.modal) openHelp();
      else if (k === 'o' && (!G.modal || G.modal === 'pause-modal')) openOptions();
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
        $('boot-message').textContent = 'La simulazione partita non è disponibile per il tuo account.';
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
      if (location.hash === '#replay') openReportedReplay();
      // Exposed for automated tests
      window.SimDebug = { G, startMatch, userWhistle, authorize, evaluateRally, endMatch, updateScore, askCaptain, RP };
    } catch (e) {
      $('boot-message').textContent = e.message || 'Errore durante il caricamento.';
    }
  })();
})();
