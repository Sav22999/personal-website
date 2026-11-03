/* Sudoku — game UI: home, board, pencil/pen, tips, checks, undo, timer, saved games and best times. */
(function () {
  'use strict';

  const E = window.SudokuEngine;
  const I = window.SudokuI18n;
  const t = I.t;
  const app = document.getElementById('app');
  const LEVELS = ['easy', 'medium', 'hard', 'expert'];
  const VERSION = (window.SudokuReleases && window.SudokuReleases[0].version) || '2.0.0';

  // ---------- storage ----------
  const store = {
    get(k, d) { try { const v = localStorage.getItem('sudoku.' + k); return v === null ? d : JSON.parse(v); } catch (e) { return d; } },
    set(k, v) { try { localStorage.setItem('sudoku.' + k, JSON.stringify(v)); } catch (e) { /* private mode */ } },
    del(k) { try { localStorage.removeItem('sudoku.' + k); } catch (e) { /* private mode */ } },
  };
  const settings = Object.assign({ conflicts: true, same: true, autoclear: true, timer: true }, store.get('settings', {}));
  const saveSettings = () => store.set('settings', settings);

  // ---------- DOM helpers ----------
  function h(tag, attrs, ...kids) {
    const m = /^([a-z0-9]+)?((?:[.#][\w-]+)*)$/i.exec(tag);
    const el = document.createElement(m[1] || 'div');
    m[2].replace(/([.#])([\w-]+)/g, (_, k, v) => { if (k === '.') el.classList.add(v); else el.id = v; });
    if (attrs && (attrs instanceof Node || Array.isArray(attrs) || typeof attrs !== 'object')) { kids.unshift(attrs); attrs = null; }
    for (const [k, v] of Object.entries(attrs || {})) {
      if (v == null || v === false) continue;
      if (k.startsWith('on')) el.addEventListener(k.slice(2), v);
      else if (k === 'html') el.innerHTML = v;
      else el.setAttribute(k, v === true ? '' : v);
    }
    for (const c of kids.flat(Infinity)) if (c != null && c !== false) el.append(c instanceof Node ? c : document.createTextNode(String(c)));
    return el;
  }
  const P = {
    back: '<path d="M15 18l-6-6 6-6"/>',
    more: '<circle cx="12" cy="5" r="1.4"/><circle cx="12" cy="12" r="1.4"/><circle cx="12" cy="19" r="1.4"/>',
    undo: '<path d="M9 14L4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 010 11H11"/>',
    erase: '<path d="M20 20H9L4 15a2 2 0 010-2.8L13.2 3a2 2 0 012.8 0L21 8a2 2 0 010 2.8L12 20"/><path d="M8.5 9.5l6 6"/>',
    pencil: '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/>',
    pen: '<path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.6 7.6"/><circle cx="11" cy="11" r="2"/>',
    check: '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
    bulb: '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7c.6.5 1 1.3 1 2.1V17h6v-.2c0-.8.4-1.6 1-2.1A7 7 0 0012 2z"/>',
    pause: '<rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/>',
    play: '<path d="M6 4l14 8-14 8z"/>',
    plus: '<path d="M12 5v14M5 12h14"/>',
    restart: '<path d="M3 12a9 9 0 109-9 9.7 9.7 0 00-6.7 2.7L3 8"/><path d="M3 3v5h5"/>',
    grid: '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/>',
    magic: '<path d="M15 4V2M15 16v-2M8 9h2M20 9h2M17.8 11.8L19 13M15 9h0M17.8 6.2L19 5M3 21l9-9M12.2 6.2L11 5"/>',
    gear: '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1.1-1.5 1.7 1.7 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1.1 1.7 1.7 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.8.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.8V9a1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z"/>',
    book: '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/>',
    home: '<path d="M3 11l9-8 9 8v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1z"/>',
    globe: '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>',
    moon: '<path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/>',
    trophy: '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0z"/><path d="M17 5h3v2a3 3 0 01-3 3M7 5H4v2a3 3 0 003 3"/>',
    help: '<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/>',
    tick: '<path d="M5 12l5 5L20 7"/>',
    info: '<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>',
  };
  const icon = name => { const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg'); s.setAttribute('viewBox', '0 0 24 24'); s.setAttribute('class', 'i'); s.setAttribute('aria-hidden', 'true'); s.innerHTML = P[name]; return s; };

  let toastTimer;
  function toast(msg, kind) {
    const el = document.getElementById('toast');
    el.textContent = msg; el.className = 'toast show' + (kind ? ' ' + kind : '');
    clearTimeout(toastTimer); toastTimer = setTimeout(() => { el.className = 'toast'; }, 2200);
  }

  function modal(content, opts) {
    opts = opts || {};
    const box = h('div.box', { role: 'dialog', 'aria-modal': 'true' }, content);
    const m = h('div.modal', { onclick: e => { if (e.target === m && !opts.persistent) close(); } }, box);
    document.body.appendChild(m);
    const key = e => { if (e.key === 'Escape' && !opts.persistent) close(); };
    document.addEventListener('keydown', key, true);
    function close() { document.removeEventListener('keydown', key, true); m.remove(); if (opts.onclose) opts.onclose(); }
    return { close, box };
  }
  function confirmBox(title, text, okLabel) {
    return new Promise(res => {
      const m = modal([h('h3', title), h('p.muted', text), h('div.actions',
        h('button.btn.btn-ghost', { onclick: () => { m.close(); res(false); } }, t('cancel')),
        h('button.btn.btn-primary', { onclick: () => { m.close(); res(true); } }, okLabel))], { onclose: () => res(false) });
    });
  }

  const fmtTime = s => { s = Math.floor(s); const hh = Math.floor(s / 3600), mm = Math.floor(s / 60) % 60, ss = s % 60; return (hh ? hh + ':' + String(mm).padStart(2, '0') : mm) + ':' + String(ss).padStart(2, '0'); };

  // ---------- theme ----------
  function applyTheme() {
    const th = store.get('theme', 'auto');
    if (th === 'auto') document.documentElement.removeAttribute('data-theme'); else document.documentElement.setAttribute('data-theme', th);
    const dark = th === 'dark' || (th === 'auto' && matchMedia('(prefers-color-scheme: dark)').matches);
    document.querySelectorAll('meta[name=theme-color]').forEach(m => { m.removeAttribute('media'); m.content = dark ? '#0B1120' : '#F4F7FC'; });
  }
  try { localStorage.setItem('sudoku.theme', store.get('theme', 'auto')); } catch (e) { /* no storage */ }

  // =================================================================== state
  let G = null;           // current game
  let sel = -1;           // selected cell
  let pencil = false;     // pencil mode
  let choosing = false;   // choosing which note to ink
  let tip = null;         // current tip
  let flash = {};         // index -> 'ok' | 'bad' (temporary)
  let badCells = null;    // cells highlighted by "check the whole board"
  let timer = null;

  function newGame(level) {
    const { puzzle, solution } = E.generate(level);
    G = { level, puzzle, solution, values: puzzle.slice(), notes: new Array(81).fill(0), history: [], elapsed: 0, mistakes: 0, hints: 0, checks: 0, paused: false, done: false, started: Date.now() };
    sel = -1; pencil = false; choosing = false; tip = null; badCells = null;
    store.set('level', level);
    save();
  }
  function save() { if (G && !G.done) store.set('game', Object.assign({}, G, { history: G.history.slice(-200) })); }
  function loadSaved() { const g = store.get('game', null); return g && g.puzzle && g.puzzle.length === 81 && !g.done ? g : null; }

  const isGiven = i => G.puzzle[i] !== 0;
  const noteHas = (i, d) => (G.notes[i] >> d) & 1;

  /** Records the previous state of some cells, for Undo. */
  function record(cells) { G.history.push(cells.map(i => ({ i, v: G.values[i], n: G.notes[i] }))); }

  function undo() {
    const step = G.history.pop();
    if (!step) return;
    for (const c of step) { G.values[c.i] = c.v; G.notes[c.i] = c.n; }
    sel = step[0].i; tip = null; choosing = false;
    save(); paint();
  }

  function input(d) {
    if (G.done || G.paused) return;
    if (sel < 0) { toast(t('select_cell')); return; }
    if (isGiven(sel)) { toast(t('given_cell')); return; }
    if (choosing) {
      choosing = false;
      if (noteHas(sel, d)) placeValue(sel, d); else paint();
      return;
    }
    if (pencil) {
      if (G.values[sel]) return;
      record([sel]);
      G.notes[sel] ^= 1 << d;
      tip = null; save(); paint();
      return;
    }
    placeValue(sel, d);
  }

  function placeValue(i, d) {
    const clearing = G.values[i] === d;
    const touched = [i];
    if (!clearing && settings.autoclear) for (const p of E.PEERS[i]) if (!G.values[p] && noteHas(p, d)) touched.push(p);
    record(touched);
    G.values[i] = clearing ? 0 : d;
    G.notes[i] = 0;
    if (!clearing) {
      if (settings.autoclear) for (const p of touched.slice(1)) G.notes[p] &= ~(1 << d);
      if (G.solution[i] !== d) G.mistakes++;
    }
    tip = null; badCells = null;
    save();
    if (G.values.every((v, k) => v === G.solution[k])) return win();
    paint();
  }

  function erase() {
    if (sel < 0 || isGiven(sel) || G.done) return;
    if (!G.values[sel] && !G.notes[sel]) return;
    record([sel]);
    G.values[sel] = 0; G.notes[sel] = 0; tip = null; choosing = false;
    save(); paint();
  }

  /** "Ink": turns the pencil note of the selected cell into the final number. */
  function ink() {
    if (G.done || G.paused) return;
    if (sel < 0 || isGiven(sel) || G.values[sel]) { toast(t('select_cell')); return; }
    const ds = E.digitsOf(G.notes[sel]);
    if (!ds.length) { toast(t('no_notes')); return; }
    if (ds.length === 1) { choosing = false; placeValue(sel, ds[0]); return; }
    choosing = !choosing;
    paint();
  }

  function checkCell() {
    if (G.done) return;
    if (sel < 0) { toast(t('select_cell')); return; }
    if (isGiven(sel)) { toast(t('given_cell')); return; }
    if (!G.values[sel]) { toast(t('empty_cell')); return; }
    G.checks++;
    const ok = G.values[sel] === G.solution[sel];
    flash[sel] = ok ? 'ok' : 'bad';
    const idx = sel;
    setTimeout(() => { delete flash[idx]; paint(); }, 950);
    toast(t(ok ? 'correct' : 'wrong'), ok ? 'ok' : 'bad');
    save(); paint();
  }

  function checkBoard() {
    const bad = [];
    for (let i = 0; i < 81; i++) if (G.values[i] && G.values[i] !== G.solution[i]) bad.push(i);
    G.checks++;
    badCells = new Set(bad);
    toast(bad.length ? t('board_errors', { n: bad.length }) : t('board_ok'), bad.length ? 'bad' : 'ok');
    setTimeout(() => { badCells = null; paint(); }, 3500);
    save(); paint();
  }

  /** Next tip: a mistake on the board first, then a logical single, otherwise a revealed cell. */
  function showTip() {
    if (G.done || G.paused) return;
    if (tip) { tip = null; paint(); return; }
    const wrong = G.values.findIndex((v, i) => v && v !== G.solution[i]);
    if (wrong >= 0) tip = { type: 'error', index: wrong };
    else {
      const s = E.findSingle(G.values);
      if (s && s.digit === G.solution[s.index]) tip = s;
      else {
        const empty = G.values.map((v, i) => (v ? -1 : i)).filter(i => i >= 0);
        const i = empty[Math.floor(Math.random() * empty.length)];
        tip = { type: 'reveal', index: i, digit: G.solution[i] };
      }
    }
    G.hints++;
    sel = tip.index; choosing = false;
    save(); paint();
  }

  function applyTip() {
    if (!tip) return;
    const tp = tip; tip = null;
    sel = tp.index;
    if (tp.type === 'error') { erase(); return; }
    placeValue(tp.index, tp.digit);
  }

  function autoNotes() {
    const cells = [];
    for (let i = 0; i < 81; i++) if (!G.values[i]) cells.push(i);
    record(cells);
    for (const i of cells) G.notes[i] = E.candidates(G.values, i);
    save(); paint();
  }

  async function restart() {
    if (!await confirmBox(t('confirm_restart'), t('confirm_restart_text'), t('restart_btn'))) return;
    Object.assign(G, { values: G.puzzle.slice(), notes: new Array(81).fill(0), history: [], elapsed: 0, mistakes: 0, hints: 0, checks: 0 });
    sel = -1; tip = null; choosing = false; save(); paint();
  }

  function win() {
    G.done = true; stopTimer();
    const stats = store.get('stats', {});
    const st = stats[G.level] || { solved: 0, best: null };
    st.solved++;
    const record = st.best == null || G.elapsed < st.best;
    if (record) st.best = Math.floor(G.elapsed);
    stats[G.level] = st; store.set('stats', stats);
    store.del('game');
    sel = -1; paint();
    setTimeout(() => {
      const m = modal(h('div.win-box', h('div.trophy', icon('trophy')), h('h3', t('win_t')),
        h('p.muted', t('win', { level: t(G.level).toLowerCase(), time: fmtTime(G.elapsed) })),
        h('p.muted', t('stats_line', { m: G.mistakes, h: G.hints })),
        record ? h('div.record', t('new_record')) : null,
        h('div.stack', { style: 'margin-top:18px' },
          h('button.btn.btn-primary.btn-block', { onclick: () => { m.close(); startGame(G.level); } }, icon('plus'), t('play_again')),
          h('button.btn.btn-ghost.btn-block', { onclick: () => { m.close(); showHome(); } }, t('back_home')))), { persistent: true });
    }, 350);
  }

  // ---------- timer ----------
  function startTimer() {
    stopTimer();
    let last = Date.now();
    timer = setInterval(() => {
      const now = Date.now();
      if (G && !G.paused && !G.done && document.visibilityState === 'visible') {
        G.elapsed += (now - last) / 1000;
        const el = document.getElementById('time');
        if (el) el.textContent = fmtTime(G.elapsed);
        if (Math.floor(G.elapsed) % 5 === 0) save();
      }
      last = now;
    }, 500);
  }
  function stopTimer() { clearInterval(timer); timer = null; }
  function setPaused(p) { if (!G || G.done) return; G.paused = p; save(); paint(); }
  document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'hidden' && G && !G.done && screen === 'game') { G.paused = true; save(); paint(); } });

  // =================================================================== screens
  let screen = 'home';

  function startGame(level) {
    app.replaceChildren(h('div.loading', t('generating')));
    setTimeout(() => { newGame(level); screen = 'game'; renderGame(); startTimer(); }, 30);
  }

  function showHome() {
    screen = 'home'; stopTimer();
    const saved = loadSaved();
    const stats = store.get('stats', {});
    let level = store.get('level', 'medium');
    const levels = h('div.levels', { role: 'radiogroup' });
    LEVELS.forEach((lv, k) => {
      const st = stats[lv];
      const b = h('button.level' + (lv === level ? '.sel' : ''), { type: 'button', role: 'radio', 'aria-checked': String(lv === level), onclick: () => {
        level = lv; store.set('level', lv); levels.querySelectorAll('.level').forEach(x => { x.classList.toggle('sel', x === b); x.setAttribute('aria-checked', String(x === b)); });
      } }, h('strong', t(lv)), h('div.dots', [0, 1, 2, 3].map(j => h('i' + (j <= k ? '.on' : '')))),
        h('small', st ? `${t('best')} ${st.best != null ? fmtTime(st.best) : t('none_yet')} · ${t('played')} ${st.solved}` : `${t('best')} ${t('none_yet')}`));
      levels.appendChild(b);
    });
    app.replaceChildren(h('div.home',
      h('div.topbar', langButton(), h('button.icon-btn', { title: t('theme'), 'aria-label': t('theme'), onclick: cycleTheme }, icon('moon'))),
      h('section.hero', h('img.logo', { src: 'icons/favicon.svg', alt: '' }), h('h1', 'Sudoku'), h('p', t('subtitle'))),
      h('div.home-grid',
        h('section.panel',
          saved ? h('button.resume-card', { type: 'button', onclick: () => resume(saved) }, h('img.mini', { src: 'icons/favicon.svg', alt: '' }),
            h('div', h('strong', t('continue_game')), h('span', `${t(saved.level)} · ${fmtTime(saved.elapsed)}`))) : null,
          h('h2', t('level')), levels,
          h('button.btn.btn-primary.btn-block', { type: 'button', onclick: async () => {
            if (saved && !await confirmBox(t('confirm_new'), t('confirm_new_text'), t('start'))) return;
            startGame(level);
          } }, icon('play'), saved ? t('new_game') : t('play'))),
        h('section.panel.howto', h('h2', t('howto_title')),
          h('ol', [1, 2, 3].map(n => h('li', h('span.n', String(n)), h('div', h('strong', t('howto' + n + '_t')), h('p', t('howto' + n)))))),
          h('div.links', h('button.btn.btn-soft', { type: 'button', onclick: showRules }, icon('book'), t('rules_title'))))),
      h('footer.footer',
        h('a', { href: 'https://www.saveriomorelli.com/', target: '_blank', rel: 'noopener' }, t('credit_by'), ' ', h('strong', 'Saverio Morelli'), ' · ', t('credit_ai')),
        h('button.version-btn', { type: 'button', onclick: showReleases }, `${t('version')} ${VERSION}`))));
    document.title = 'Sudoku – Saverio Morelli';
  }

  function resume(saved) { G = saved; G.paused = false; sel = -1; tip = null; pencil = false; choosing = false; screen = 'game'; renderGame(); startTimer(); }

  function langButton() {
    return h('button.lang-pill', { type: 'button', onclick: showLanguages }, icon('globe'), I.getLang().toUpperCase());
  }
  function showLanguages() {
    const m = modal([h('h3', t('language')), h('div.menu-list.lang-list', Object.entries(I.LANGS).map(([k, label]) =>
      h('button', { type: 'button', onclick: () => { I.setLang(k); m.close(); screen === 'game' ? renderGame() : showHome(); } }, h('span', label), k === I.getLang() ? icon('tick') : null)))]);
  }
  function cycleTheme() {
    const next = { auto: 'light', light: 'dark', dark: 'auto' }[store.get('theme', 'auto')];
    store.set('theme', next); applyTheme(); toast(t('theme') + ': ' + t('theme_' + next));
  }
  function showRules() {
    modal([h('h3', t('rules_title')), h('div.rules', t('rules').map(([a, b]) => [h('h4', a), h('p', b)])),
      h('div.actions', h('button.btn.btn-primary', { onclick: e => e.target.closest('.modal').remove() }, t('got_it')))]);
  }
  function showReleases() {
    const L = I.getLang();
    modal([h('h3', t('release_notes')), h('div.release', (window.SudokuReleases || []).map(r => [h('h4', `${r.version} · ${new Date(r.date).toLocaleDateString(L, { year: 'numeric', month: 'short', day: 'numeric' })}`), h('ul', (r.notes[L] || r.notes.en).map(n => h('li', n)))]))]);
  }

  // =================================================================== game screen
  let boardEl, sideEl;

  function renderGame() {
    boardEl = h('div.board', { role: 'grid', 'aria-label': 'Sudoku' });
    for (let i = 0; i < 81; i++) {
      const r = E.ROW(i), c = E.COL(i);
      boardEl.appendChild(h('button.cell' + (c === 3 || c === 6 ? '.c' + c : '') + (r === 3 || r === 6 ? '.r' + r : ''), { type: 'button', 'data-i': i, role: 'gridcell', onclick: () => select(i) }));
    }
    sideEl = h('div.side');
    app.replaceChildren(h('div.game',
      h('header.game-bar',
        h('button.icon-btn', { type: 'button', title: t('leave'), 'aria-label': t('leave'), onclick: () => { save(); showHome(); } }, icon('back')),
        h('div.title', h('img', { src: 'icons/favicon.svg', alt: '' }), h('strong', 'Sudoku'), h('span.badge', t(G.level))),
        h('button.icon-btn', { type: 'button', id: 'pause-btn', title: t('pause'), 'aria-label': t('pause'), onclick: () => setPaused(!G.paused) }, icon('pause')),
        h('button.icon-btn', { type: 'button', title: t('settings'), 'aria-label': t('settings'), onclick: showMenu }, icon('more'))),
      h('div.stats', h('span', `${t('mistakes')}: `, h('b#mist', '0')), h('span', `${t('hints')}: `, h('b#hint', '0')), settings.timer ? h('span', `${t('time')}: `, h('b#time', fmtTime(G.elapsed))) : null),
      h('div.game-main', h('div.board-wrap', boardEl), sideEl)));
    paint();
  }

  function select(i) { if (G.done) return; if (G.paused) { setPaused(false); return; } sel = i; if (choosing && (G.values[i] || !G.notes[i])) choosing = false; paint(); }

  function paint() {
    if (screen !== 'game' || !boardEl) return;
    const v = G.values;
    const conf = settings.conflicts ? E.conflicts(v) : new Set();
    const selVal = sel >= 0 ? v[sel] : 0;
    const peers = sel >= 0 ? new Set(E.PEERS[sel]) : new Set();
    boardEl.classList.toggle('paused', !!G.paused);
    [...boardEl.children].forEach((cell, i) => {
      const val = v[i];
      cell.className = cell.className.replace(/\b(given|peer|same|sel|conflict|bad|good|tip|flash-ok|flash-bad)\b/g, '').replace(/\s+/g, ' ').trim();
      if (isGiven(i)) cell.classList.add('given');
      if (i === sel) cell.classList.add('sel');
      else if (settings.same && selVal && val === selVal) cell.classList.add('same');
      else if (peers.has(i)) cell.classList.add('peer');
      if (conf.has(i) && !isGiven(i)) cell.classList.add('conflict');
      if (badCells && badCells.has(i)) cell.classList.add('bad');
      if (G.done) cell.classList.add('good');
      if (tip && tip.index === i) cell.classList.add('tip');
      if (flash[i]) cell.classList.add('flash-' + flash[i]);
      cell.replaceChildren();
      if (val) cell.textContent = val;
      else if (G.notes[i]) cell.appendChild(h('div.notes', [1, 2, 3, 4, 5, 6, 7, 8, 9].map(d => h('span' + (settings.same && selVal === d && noteHas(i, d) ? '.hl' : ''), noteHas(i, d) ? String(d) : ''))));
      cell.setAttribute('aria-label', `${E.ROW(i) + 1},${E.COL(i) + 1}: ${val || ''}`);
    });
    const pauseBtn = document.getElementById('pause-btn');
    if (pauseBtn) pauseBtn.replaceChildren(icon(G.paused ? 'play' : 'pause'));
    const wrap = boardEl.parentElement;
    const cover = wrap.querySelector('.pause-cover');
    if (G.paused && !cover) wrap.appendChild(h('div.pause-cover', h('div', h('h3', t('paused')), h('p', t('paused_text')), h('button.btn.btn-primary', { onclick: () => setPaused(false) }, icon('play'), t('resume')))));
    if (!G.paused && cover) cover.remove();
    const mi = document.getElementById('mist'), hi = document.getElementById('hint');
    if (mi) mi.textContent = G.mistakes;
    if (hi) hi.textContent = G.hints;
    paintSide();
  }

  function paintSide() {
    const counts = new Array(10).fill(0);
    G.values.forEach((x, i) => { if (x && x === G.solution[i]) counts[x]++; });
    const selNotes = sel >= 0 && !G.values[sel] ? G.notes[sel] : 0;
    const tool = (name, label, onclick, extra) => h('button.tool' + (extra && extra.on ? '.on' : ''), { type: 'button', onclick, disabled: extra && extra.disabled, title: label }, icon(name), label, extra && extra.pill ? h('span.pill', extra.pill) : null);
    const tools = h('div.tools',
      tool('undo', t('undo'), undo, { disabled: !G.history.length || G.done }),
      tool('erase', t('erase'), erase, { disabled: G.done }),
      tool('pencil', t('pencil'), () => { pencil = !pencil; choosing = false; paint(); }, { on: pencil, pill: pencil ? 'ON' : 'OFF' }),
      tool('pen', t('ink'), ink, { on: choosing, disabled: G.done || !selNotes }),
      tool('check', t('check'), checkCell, { disabled: G.done }),
      tool('bulb', t('tip'), showTip, { on: !!tip, disabled: G.done }));
    const pad = h('div.pad', [1, 2, 3, 4, 5, 6, 7, 8, 9].map(d => {
      const left = 9 - counts[d];
      const isChoice = choosing && ((selNotes >> d) & 1);
      return h('button.key' + (left === 0 ? '.done' : '') + (pencil && !choosing ? '.pencil' : '') + (isChoice ? '.choice' : ''), { type: 'button', 'aria-label': String(d), onclick: () => input(d), disabled: choosing && !isChoice },
        String(d), h('small', left > 0 ? String(left) : ''));
    }));
    const parts = [];
    if (tip) parts.push(tipCard());
    if (choosing) parts.push(h('div.choose-card', icon('pen'), t('choose_note')));
    parts.push(tools, pad);
    sideEl.replaceChildren(...parts);
  }

  function tipCard() {
    const d = tip.digit;
    let title, text;
    if (tip.type === 'error') { title = t('tip_error_t'); text = t('tip_error'); }
    else if (tip.type === 'naked') { title = t('tip_naked_t'); text = t('tip_naked', { d }); }
    else if (tip.type === 'hidden') { title = t('tip_hidden_t', { d }); text = t('tip_hidden', { d, unit: t('unit_' + tip.unit) }); }
    else { title = t('tip_reveal_t'); text = t('tip_reveal', { d }); }
    return h('div.tip-card', h('h3', icon('bulb'), title), h('p', text),
      h('div.row', h('button.btn.btn-ghost', { type: 'button', onclick: () => { tip = null; paint(); } }, t('close')),
        h('button.btn.btn-primary', { type: 'button', onclick: applyTip }, tip.type === 'error' ? t('erase') : t('fill_it'))));
  }

  function showMenu() {
    const item = (ic, label, fn) => h('button', { type: 'button', onclick: () => { m.close(); fn(); } }, icon(ic), label);
    const m = modal([h('h3', 'Sudoku'), h('div.menu-list',
      item('plus', t('new_game'), async () => { if (await confirmBox(t('confirm_new'), t('confirm_new_text'), t('start'))) startGame(G.level); }),
      item('restart', t('restart'), restart),
      h('hr'),
      item('grid', t('check_board'), checkBoard),
      item('magic', t('notes_auto'), autoNotes),
      h('hr'),
      item('gear', t('settings'), showSettings),
      item('book', t('rules_title'), showRules),
      item('home', t('leave'), () => { save(); showHome(); }))]);
  }

  function showSettings() {
    const row = (key, label) => {
      const sw = h('span.switch' + (settings[key] ? '.on' : ''));
      return h('div.setting', { role: 'switch', tabindex: 0, 'aria-checked': String(settings[key]), onclick: e => {
        settings[key] = !settings[key]; saveSettings(); sw.classList.toggle('on', settings[key]); e.currentTarget.setAttribute('aria-checked', String(settings[key]));
        if (key === 'timer') renderGame(); else paint();
      } }, h('span', label), sw);
    };
    const th = store.get('theme', 'auto');
    const seg = h('div.seg', ['auto', 'light', 'dark'].map(k => h('button' + (k === th ? '.sel' : ''), { type: 'button', onclick: e => {
      store.set('theme', k); applyTheme(); seg.querySelectorAll('button').forEach(b => b.classList.toggle('sel', b === e.currentTarget));
    } }, t('theme_' + k))));
    modal([h('h3', t('settings')), row('conflicts', t('set_conflicts')), row('same', t('set_same')), row('autoclear', t('set_autoclear')), row('timer', t('set_timer')),
      h('div.setting', { style: 'display:block' }, h('div', { style: 'margin-bottom:8px' }, t('theme')), seg),
      h('div.setting', { onclick: showLanguages }, h('span', t('language')), h('span', { style: 'color:var(--muted)' }, I.LANGS[I.getLang()]))]);
  }

  // ---------- keyboard ----------
  document.addEventListener('keydown', e => {
    if (screen !== 'game' || !G || document.querySelector('.modal')) return;
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') { e.preventDefault(); undo(); return; }
    if (e.ctrlKey || e.metaKey || e.altKey) return;
    const k = e.key;
    if (/^[1-9]$/.test(k)) { input(Number(k)); return; }
    if (k === 'Backspace' || k === 'Delete' || k === '0') { erase(); return; }
    const mv = { ArrowUp: -9, ArrowDown: 9, ArrowLeft: -1, ArrowRight: 1 }[k];
    if (mv !== undefined) {
      e.preventDefault();
      if (sel < 0) sel = 40;
      else { const r = E.ROW(sel), c = E.COL(sel); sel = mv === -9 ? ((r + 8) % 9) * 9 + c : mv === 9 ? ((r + 1) % 9) * 9 + c : mv === -1 ? r * 9 + (c + 8) % 9 : r * 9 + (c + 1) % 9; }
      paint(); return;
    }
    const lk = k.toLowerCase();
    if (lk === 'p' || lk === 'n') { pencil = !pencil; choosing = false; paint(); }
    else if (lk === 'i') ink();
    else if (lk === 'h') showTip();
    else if (lk === 'c') checkCell();
    else if (k === ' ' || k === 'Escape') { e.preventDefault(); if (k === 'Escape' && (tip || choosing)) { tip = null; choosing = false; paint(); } else if (k === ' ') setPaused(!G.paused); }
  });

  matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
  window.addEventListener('beforeunload', save);
  applyTheme();
  showHome();
})();
