// Prism Mine main client controller: lobby, solo and daily mines, online duels.
const API_URL = 'api/index.php';
const POLL_MS = 1500;
const AUTO_WIN_S = 120; // the same as ABANDON_SECONDS in api/index.php
const PENALTY = 3; // a wrong answer or a revealed gem costs as much as three beams
const MAX_STRIKES = 3;

const $ = (id) => document.getElementById(id);
const P = PrismMine;
const N = P.SIZE;
const other = (slot) => slot === 1 ? 2 : 1;
const SVG_NS = 'http://www.w3.org/2000/svg';

let playerName = '';
let session = null;
let sessionSeq = 0;
let pollTimer = null;
let clockInterval = null;
let offlineTimer = null;
let audioCtx = null;

const prefs = {sound: true, check: true, paths: true};

// Colours of the gems and of the light that comes out
const GEM_FILL = {R: '#E5484D', Y: '#F5C542', B: '#3E7BFA', W: '#F4F1EA', C: 'rgba(220, 240, 255, 0.28)', K: '#1C1917'};
const MIX = {
    '': '#CFC6BC', R: '#E5484D', Y: '#F5C542', B: '#3E7BFA', W: '#F7F4EE',
    RY: '#F28C28', BR: '#8E4EC6', BY: '#3DAA5C', BRY: '#4A3B32',
    RW: '#F4A1B0', WY: '#FBE7A1', BW: '#9CC3FF', RWY: '#F9C08A', BRW: '#C7A6E8', BWY: '#A7DDB4', BRWY: '#9A9087',
};
const LIGHT_MIX = new Set(['', 'Y', 'W', 'WY', 'RW', 'BW', 'RWY', 'BWY', 'RY']);
const CELL_POLY = {
    S: '0,0 1,0 1,1 0,1', NE: '0,0 1,0 1,1', SE: '1,0 1,1 0,1', SW: '0,0 1,1 0,1', NW: '0,0 1,0 0,1',
};

// --- Storage ---
function storageGet(k) {
    try {
        return localStorage.getItem(k);
    } catch (e) {
        return null;
    }
}

function storageSet(k, v) {
    try {
        localStorage.setItem(k, v);
    } catch (e) {
    }
}

function sessionGet(k) {
    try {
        return sessionStorage.getItem(k);
    } catch (e) {
        return null;
    }
}

function sessionSet(k, v) {
    try {
        v === null ? sessionStorage.removeItem(k) : sessionStorage.setItem(k, v);
    } catch (e) {
    }
}

function escapeHtml(str) {
    const el = document.createElement('div');
    el.textContent = str;
    return el.innerHTML;
}

function languageName() {
    try {
        return new Intl.DisplayNames([currentLang], {type: 'language'}).of(currentLang);
    } catch (e) {
        return currentLang.toUpperCase();
    }
}

const todayKey = () => {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};

const colorWords = (mix) => mix === '' ? t('color_none') : mix.split('').map(c => t('color_' + c)).join(' + ');
const gemName = (gem) => t('gem_' + gem.shape) + ' · ' + t('color_' + gem.color);

// --- Sounds ---
function initAudio() {
    if (!audioCtx) {
        const AudioClass = window.AudioContext || window.webkitAudioContext;
        if (AudioClass) audioCtx = new AudioClass();
    }
    if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
}

function tone(freq, endFreq, duration, gainValue, delay = 0, type = 'sine') {
    const now = audioCtx.currentTime + delay;
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.type = type;
    osc.frequency.setValueAtTime(freq, now);
    osc.frequency.exponentialRampToValueAtTime(endFreq, now + duration);
    gain.gain.setValueAtTime(gainValue, now);
    gain.gain.exponentialRampToValueAtTime(0.001, now + duration);
    osc.connect(gain).connect(audioCtx.destination);
    osc.start(now);
    osc.stop(now + duration + 0.02);
}

function playSound(kind, colors = '') {
    if (!prefs.sound) return;
    try {
        initAudio();
        if (!audioCtx) return;
        if (kind === 'beam') {
            tone(880, 1320, 0.12, 0.08, 0, 'triangle');
            // One chime per colour picked up
            colors.split('').forEach((c, i) => tone({R: 523, Y: 659, B: 784, W: 1047}[c], {R: 523, Y: 659, B: 784, W: 1047}[c], 0.35, 0.09, 0.12 + i * 0.09));
        } else if (kind === 'absorbed') {
            tone(220, 60, 0.4, 0.25, 0, 'triangle');
        } else if (kind === 'place') {
            tone(1200, 900, 0.06, 0.08);
        } else if (kind === 'right') {
            [523, 659, 784, 1047, 1319].forEach((f, i) => tone(f, f, 0.35, 0.14, i * 0.09, 'triangle'));
        } else if (kind === 'wrong') {
            tone(300, 180, 0.3, 0.2, 0, 'square');
        } else if (kind === 'lose') {
            [392, 330, 262].forEach((f, i) => tone(f, f * 0.98, 0.4, 0.15, i * 0.16, 'triangle'));
        }
    } catch (e) {
    }
}

// --- UI helpers ---
function showScreen(id) {
    document.querySelectorAll('.screen').forEach(s => s.classList.toggle('active', s.id === 'screen-' + id));
}

function showModal(id, visible) {
    const el = $(id);
    if (el) el.classList.toggle('hidden', !visible);
}

let toastTimer = null;

function showToast(msg) {
    const toast = $('toast');
    toast.textContent = msg;
    toast.classList.add('visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('visible'), 2800);
}

function showError(code) {
    const key = 'err_' + code;
    showToast(t(key) !== key ? t(key) : t('err_generic'));
}

const STICKY_MODALS = ['gameover-overlay', 'offline-overlay'];

function closeTopModal() {
    const open = [...document.querySelectorAll('.modal:not(.hidden)')].filter(m => !STICKY_MODALS.includes(m.id)).pop();
    if (!open) return false;
    showModal(open.id, false);
    return true;
}

async function copyText(text, okMessage) {
    try {
        await navigator.clipboard.writeText(text);
    } catch (e) {
        const area = document.createElement('textarea');
        area.value = text;
        area.style.cssText = 'position:fixed;opacity:0';
        document.body.appendChild(area);
        area.select();
        try {
            document.execCommand('copy');
        } catch (e2) {
        }
        area.remove();
    }
    showToast(okMessage);
}

let confirmAction = null;

function openConfirm(title, text, yesLabel, onYes) {
    $('confirm-title').textContent = title;
    $('confirm-text').textContent = text;
    $('btn-confirm-yes').textContent = yesLabel;
    confirmAction = onYes;
    showModal('confirm-overlay', true);
}

function setBusy(btn, busy) {
    btn.disabled = busy;
    btn.classList.toggle('loading', busy);
}

function savePrefs() {
    storageSet('pm_prefs', JSON.stringify(prefs));
    $('btn-audio-toggle').classList.toggle('muted-icon', !prefs.sound);
    if (session) renderAll();
}

async function api(action, data = {}) {
    try {
        if (action === 'poll') {
            const resp = await fetch(`${API_URL}?${new URLSearchParams({action, ...data})}`, {cache: 'no-store'});
            return await resp.json();
        }
        const form = new FormData();
        form.append('action', action);
        for (const [k, v] of Object.entries(data)) form.append(k, v);
        const resp = await fetch(API_URL, {method: 'POST', body: form});
        return await resp.json();
    } catch (e) {
        return {error: 'network_error'};
    }
}

// ==================== BOARD GEOMETRY ====================
// The board is one SVG: a 10-unit margin for the ports, then 8 x 8 cells of 10 units
const cellX = (c) => 10 + c * 10;
const cellY = (r) => 10 + r * 10;

function portCenter(p) {
    const side = Math.floor(p / N), k = p % N;
    if (side === 0) return [15 + 10 * k, 5];
    if (side === 1) return [95, 15 + 10 * k];
    if (side === 2) return [15 + 10 * (N - 1 - k), 95];
    return [5, 15 + 10 * (N - 1 - k)];
}

const svg = (tag, attrs = {}) => {
    const el = document.createElementNS(SVG_NS, tag);
    for (const [k, v] of Object.entries(attrs)) el.setAttribute(k, v);
    return el;
};

function gemSvg(gem, cls) {
    const g = svg('g', {class: `gem gem-${gem.color} ${cls || ''}`});
    const cells = P.gemCells(gem) || [];
    cells.forEach(({r, c, t: type}) => {
        const pts = CELL_POLY[type].split(' ').map(pt => {
            const [x, y] = pt.split(',').map(Number);
            return `${cellX(c) + x * 10},${cellY(r) + y * 10}`;
        }).join(' ');
        g.appendChild(svg('polygon', {points: pts, fill: GEM_FILL[gem.color]}));
    });
    return g;
}

// A small picture of a shape, for the tray and the rules
function shapeIcon(shape, color, size = 34) {
    const cells = P.orientation(shape, 0);
    const h = Math.max(...cells.map(c => c[0])) + 1;
    const w = Math.max(...cells.map(c => c[1])) + 1;
    const polys = cells.map(([r, c, type]) => `<polygon points="${CELL_POLY[type].split(' ').map(pt => {
        const [x, y] = pt.split(',').map(Number);
        return `${(c + x) * 10},${(r + y) * 10}`;
    }).join(' ')}" fill="${GEM_FILL[color]}"/>`).join('');
    return `<svg class="shape-icon gem-${color}" viewBox="-1 -1 ${w * 10 + 2} ${h * 10 + 2}" width="${size}" height="${size}" aria-hidden="true">${polys}</svg>`;
}

// ==================== GAME STATE ====================
// probes: [{s, p, exit, colors}]; guesses: [{s, ok}]
// In a duel each player sends beams into the other's mine: the sketch only deals with the player's own beams
const myProbes = () => session.mode === 'online' ? session.probes.filter(pr => pr.s === session.mySlot) : session.probes;
const theirProbes = () => session.probes.filter(pr => pr.s !== session.mySlot);
const placingMine = () => session.mode === 'online' && session.status === 'placing';
const viewingMine = () => session.mode === 'online' && session.view === 'mine' && (session.status === 'playing' || session.status === 'finished');

const usedPorts = () => {
    const used = new Set();
    myProbes().forEach(pr => {
        used.add(pr.p);
        if (pr.exit !== null) used.add(pr.exit);
    });
    return used;
};

function draftGems() {
    return session.gems.map((g, i) => session.draft.placed[i] ? {...g, ...session.draft.placed[i]} : null);
}

function draftComplete() {
    return session.draft.placed.every(Boolean);
}

// How the sketch answers every beam fired so far: true = same answer as the mine, null = not one of the player's beams
function sketchCheck() {
    if (placingMine()) return null;
    const gems = draftGems();
    const placedOnly = gems.filter(Boolean);
    const map = P.buildMap(placedOnly);
    if (!map) return null;
    const mine = new Set(myProbes());
    return session.probes.map(pr => {
        if (!mine.has(pr)) return null;
        const res = P.trace(map, placedOnly, pr.p);
        return res.exit === pr.exit && res.colors === pr.colors;
    });
}

function isMyTurn() {
    const s = session;
    if (!s || s.status !== 'playing' || s.mode === 'example' || viewingMine()) return false;
    if (s.mode !== 'online') return true;
    return s.turn === s.mySlot && !s.inFlight;
}

function scoreOf(s) {
    return s.probes.filter(pr => pr.s === s.mySlot).length + PENALTY * (s.wrong + s.hints);
}

function nameOf(slot) {
    return session.names[slot] || t(slot === 1 ? 'player_one' : 'player_two');
}

// ==================== RENDER ====================
function renderAll() {
    if (!session) return;
    renderBoard();
    renderTray();
    renderStatus();
    renderLog();
    renderActions();
}

function renderBoard() {
    const s = session;
    const board = $('mine-board');
    board.innerHTML = '';
    const over = s.status === 'finished';

    board.appendChild(svg('rect', {x: 9.5, y: 9.5, width: 81, height: 81, rx: 2.5, class: 'mine-floor'}));
    for (let r = 0; r < N; r++) for (let c = 0; c < N; c++) {
        board.appendChild(svg('rect', {x: cellX(c) + 0.3, y: cellY(r) + 0.3, width: 9.4, height: 9.4, rx: 1.2, class: 'cell', 'data-r': r, 'data-c': c}));
    }

    // Own mine (duel), the real mine once the game is over, or the sketch (also used to place the own gems)
    const ownView = viewingMine();
    const lockedPlacement = placingMine() && s.myMine;
    if (ownView || lockedPlacement) (s.myMine || []).forEach(g => board.appendChild(gemSvg(g, 'own')));
    else if (over && s.hidden) s.hidden.forEach(g => board.appendChild(gemSvg(g, 'real')));
    else {
        const d = s.draft;
        const moving = d.drag && d.drag.moved;
        draftGems().forEach((g, i) => {
            if (!g) return;
            let cls = d.locked.has(i) ? 'locked' : '';
            if (moving && d.drag.i === i) cls += ' lifted';
            else if (d.active === i && editable()) cls += ' active';
            board.appendChild(gemSvg(g, cls));
        });
        const ghost = draftPreview();
        if (ghost) board.appendChild(gemSvg(ghost.gem, ghost.ok ? 'ghost' : 'ghost bad'));
    }

    board.classList.toggle('editing', editable());
    renderGemToolbar();
    const pathLayer = svg('g', {class: 'path-layer', id: 'path-layer'});
    board.appendChild(pathLayer);
    // The example shows the real path of the beam it just sent: in a real game it stays hidden
    if (s.mode === 'example' && s.examplePort !== null) drawPath(pathLayer, s.examplePort, s.hidden);

    // Ports: a used one shows the colour that came out (own mine: the beams the opponent sent into it)
    const used = new Map();
    const check = prefs.check && !over && !ownView ? sketchCheck() : null;
    const shown = new Set(placingMine() ? [] : ownView ? theirProbes() : myProbes());
    s.probes.forEach((pr, n) => {
        if (!shown.has(pr)) return;
        const info = {n, pr, ok: check ? check[n] : null};
        used.set(pr.p, info);
        if (pr.exit !== null && pr.exit !== pr.p) used.set(pr.exit, info);
    });
    const canProbe = isMyTurn();
    for (let p = 0; p < P.PORTS; p++) {
        const [x, y] = portCenter(p);
        const info = used.get(p);
        const g = svg('g', {class: 'port' + (info ? ' used' : canProbe ? ' free' : ''), 'data-p': p, tabindex: 0, role: 'button'});
        let label = t('port_label', {n: p + 1});
        const tile = svg('rect', {x: x - 4.2, y: y - 4.2, width: 8.4, height: 8.4, rx: 2, class: 'port-tile'});
        g.appendChild(tile);
        if (info) {
            const absorbed = info.pr.exit === null;
            tile.style.fill = absorbed ? '#0D0B0A' : MIX[info.pr.colors];
            g.classList.add(absorbed ? 'absorbed' : (LIGHT_MIX.has(info.pr.colors) ? 'light' : 'dark'));
            if (info.pr.exit === info.pr.p) g.classList.add('returned');
            if (info.ok === true) g.classList.add('ok');
            if (info.ok === false) g.classList.add('bad');
            label += ', ' + probeText(info.pr);
            if (s.lastProbe === info.n) g.classList.add('last');
        }
        const text = svg('text', {x, y: y + 1.35, class: 'port-num'});
        text.textContent = info && info.pr.exit === null ? '✕' : String(p + 1);
        g.appendChild(text);
        g.setAttribute('aria-label', label);
        board.appendChild(g);
    }
}

function probeText(pr) {
    if (pr.exit === null) return t('beam_absorbed', {from: pr.p + 1});
    if (pr.exit === pr.p) return t('beam_back', {from: pr.p + 1, colors: colorWords(pr.colors)});
    return t('beam_out', {from: pr.p + 1, to: pr.exit + 1, colors: colorWords(pr.colors)});
}

// Draws where a beam from port p would go through the sketch
function showPath(p) {
    const s = session;
    if (!s || s.mode === 'example') return; // the example draws its own beam
    const layer = $('path-layer');
    if (!layer) return;
    layer.innerHTML = '';
    if (!prefs.paths || s.status === 'finished' || placingMine()) return;
    drawPath(layer, p, viewingMine() ? s.myMine : draftGems().filter(Boolean));
}

function drawPath(layer, p, placed) {
    const map = P.buildMap(placed);
    if (!map) return;
    const res = P.trace(map, placed, p);
    const pts = [portCenter(p)];
    res.path.forEach(([r, c]) => pts.push([cellX(c) + 5, cellY(r) + 5]));
    if (res.exit !== null) pts.push(portCenter(res.exit));
    layer.appendChild(svg('polyline', {
        points: pts.map(([x, y]) => `${x},${y}`).join(' '),
        class: 'beam-path',
        stroke: res.exit === null ? '#7A6F66' : MIX[res.colors],
    }));
}

function renderTray() {
    const s = session;
    const d = s.draft;
    const over = s.status === 'finished';
    $('tray').innerHTML = s.gems.map((g, i) => `<li><button type="button" class="tray-gem ${d.placed[i] ? 'placed' : ''} ${d.selected === i || d.active === i ? 'selected' : ''} ${d.locked.has(i) ? 'locked' : ''}" data-i="${i}" ${over || d.locked.has(i) ? 'disabled' : ''} title="${escapeHtml(gemName(g))}" aria-label="${escapeHtml(gemName(g))}">${shapeIcon(g.shape, g.color)}</button></li>`).join('');
    $('tray').classList.toggle('hidden', viewingMine() || (placingMine() && !!s.myMine));
    $('place-tip').classList.toggle('hidden', !editable() || (s.mode !== 'online' && myProbes().length > 3 && !placingMine()));
    const check = prefs.check ? sketchCheck() : null;
    let status = '';
    if (placingMine()) status = s.myMine ? t('waiting_hide', {name: nameOf(other(s.mySlot))}) : t('hide_hint');
    else if (viewingMine()) status = t('mine_view_note', {name: nameOf(other(s.mySlot))});
    else if (!over) {
        if (!myProbes().length) status = t('sketch_start');
        else if (check) {
            const mine = check.filter(x => x !== null);
            status = t('sketch_explains', {n: mine.filter(Boolean).length, total: mine.length});
        }
    }
    $('sketch-status').textContent = status;
}

function renderStatus() {
    const s = session;
    const online = s.mode === 'online';
    $('miner-opp').classList.toggle('hidden', !online);
    $('name-me').textContent = online ? nameOf(s.mySlot) : (playerName || t('player_one'));
    if (online) {
        const strikes = (slot) => '✕'.repeat(s.guesses.filter(g => g.s === slot && !g.ok).length);
        [['me', s.mySlot], ['opp', other(s.mySlot)]].forEach(([side, slot]) => {
            $('name-' + side).textContent = nameOf(slot);
            $('sub-' + side).textContent = t('wins_count', {n: s.wins[slot] || 0}) + (strikes(slot) ? ' · ' + strikes(slot) : '');
            $('miner-' + side).classList.toggle('active-turn', s.status === 'playing' && s.turn === slot);
            const player = (s.players || []).find(p => p.slot === slot);
            $('miner-' + side).classList.toggle('offline', !!player && !player.online);
        });
    } else if (s.mode === 'example') {
        $('name-me').textContent = t('example_title');
        $('sub-me').textContent = t('score_now', {beams: s.probes.length, score: scoreOf(s)});
        $('miner-me').classList.remove('active-turn');
    } else {
        $('sub-me').textContent = t('score_now', {beams: s.probes.length, score: scoreOf(s)});
        $('miner-me').classList.remove('active-turn');
    }
    renderClocks();

    let text = '', cls = '';
    if (placingMine()) {
        text = s.myMine ? t('waiting_hide', {name: nameOf(other(s.mySlot))}) : t('hide_title');
        cls = s.myMine ? '' : 'mine';
    } else if (s.status === 'playing') {
        if (s.mode === 'example') text = t('example_step', {n: s.exampleStep + 1, total: EXAMPLE.steps.length});
        else if (!online) text = s.mode === 'daily' ? t('daily_label', {date: s.date}) : t('level_' + s.level);
        else if (s.turn === s.mySlot) {
            text = t('your_turn');
            cls = 'mine';
        } else text = t('their_turn', {name: nameOf(s.turn)});
    } else if (s.status === 'finished') {
        text = $('gameover-title').textContent;
        cls = 'over';
    }
    $('turn-indicator').textContent = text;
    $('turn-indicator').className = 'turn-indicator ' + cls;
}

function renderClocks() {
    const s = session;
    const show = s.mode === 'online' && s.timeControl > 0;
    const fmt = (ms) => {
        const sec = Math.ceil(ms / 1000);
        return `${Math.floor(sec / 60)}:${String(sec % 60).padStart(2, '0')}`;
    };
    [['me', s.mySlot], ['opp', other(s.mySlot)]].forEach(([side, slot]) => {
        const el = $('clock-' + side);
        el.classList.toggle('hidden', !show);
        if (!show) return;
        el.textContent = fmt(s.ms[slot]);
        el.classList.toggle('low-time', s.ms[slot] < 30000);
        el.classList.toggle('running', s.status === 'playing' && s.turn === slot);
    });
}

function renderLog() {
    const s = session;
    const check = prefs.check && s.status !== 'finished' ? sketchCheck() : null;
    const items = [];
    let gi = 0;
    // Beams and answers in the order they happened
    s.history.forEach(h => {
        if (h.t === 'p') {
            const pr = s.probes[h.i];
            const sw = pr.exit === null ? '#0D0B0A' : MIX[pr.colors];
            const who = s.mode === 'online' ? `<span class="log-who">${escapeHtml(nameOf(pr.s))}${pr.s !== s.mySlot ? ' ' + escapeHtml(t('log_in_your_mine')) : ''}</span>` : '';
            const mark = check && check[h.i] !== null ? `<span class="log-check ${check[h.i] ? 'ok' : 'bad'}" aria-label="${escapeHtml(t(check[h.i] ? 'sketch_ok' : 'sketch_bad'))}">${check[h.i] ? '✓' : '✗'}</span>` : '';
            items.push(`<li class="log-item"><span class="log-n">${h.i + 1}</span><span class="log-swatch" style="background:${sw}"></span><span class="log-text">${who}${escapeHtml(probeText(pr))}</span>${mark}</li>`);
        } else {
            const g = s.guesses[gi++];
            items.push(`<li class="log-item answer ${g.ok ? 'ok' : 'bad'}"><span class="log-text">${escapeHtml(t(g.ok ? 'log_answer_ok' : 'log_answer_bad', {name: s.mode === 'online' ? nameOf(g.s) : t('you')}))}</span></li>`);
        }
    });
    $('log-list').innerHTML = items.join('') || `<li class="log-empty muted">${escapeHtml(t('log_empty'))}</li>`;
    $('log-list').scrollTop = $('log-list').scrollHeight;
}

function renderActions() {
    const s = session;
    const over = s.status === 'finished';
    const online = s.mode === 'online';
    const example = s.mode === 'example';
    $('example-card').classList.toggle('hidden', !example);
    $('tray').closest('.draft-bar').classList.toggle('example', example);
    $('screen-game').classList.toggle('example-mode', example);
    $('game-actions').classList.toggle('hidden', example);
    $('btn-resign').classList.toggle('hidden', example || over);
    if (example) {
        $('btn-hint').classList.add('hidden');
        $('btn-result').classList.add('hidden');
        $('btn-chat-toggle').classList.add('hidden');
        document.querySelector('.draft-tools').classList.add('hidden');
        return;
    }
    const placing = placingMine();
    const placed = placing && !!s.myMine;
    $('btn-hint').classList.toggle('hidden', online || over);
    $('btn-hint').disabled = s.draft.locked.size >= s.gems.length - 1;
    $('btn-resign').classList.toggle('hidden', over);
    $('btn-result').classList.toggle('hidden', !over);
    $('btn-chat-toggle').classList.toggle('hidden', !online);
    $('btn-random-place').classList.toggle('hidden', !placing);
    $('btn-answer').textContent = placing ? t('ready') : t('answer');
    $('btn-answer').disabled = placing ? (placed || !draftComplete()) : (over || !draftComplete() || !isMyTurn());
    $('btn-clear').textContent = placing ? t('clear_mine') : t('clear_sketch');
    $('btn-rotate').disabled = over || placed;
    $('btn-clear').disabled = over || placed;
    $('btn-random-place').disabled = placed;
    document.querySelector('.draft-tools').classList.toggle('hidden', over || viewingMine() || placed);
    const tabs = online && (s.status === 'playing' || s.status === 'finished');
    $('view-tabs').classList.toggle('hidden', !tabs);
    document.querySelectorAll('#view-tabs [data-view]').forEach(b => {
        const on = (s.view || 'target') === b.dataset.view;
        b.classList.toggle('active', on);
        b.setAttribute('aria-selected', String(on));
    });
}

// ==================== SKETCH (placing gems) ====================
// The same tools place the own gems in a duel and the gems of the sketch while solving.
// selected = gem of the tray waiting to be placed; active = gem on the board with its toolbar;
// drag = a gem moving under the mouse or finger (from the tray or from the board).
function newDraft(count) {
    return {placed: new Array(count).fill(null), selected: null, active: null, rot: 0, hover: null, locked: new Set(), drag: null};
}

function gemBox(shape, rot) {
    const cells = P.orientation(shape, rot);
    return {h: Math.max(...cells.map(c => c[0])) + 1, w: Math.max(...cells.map(c => c[1])) + 1};
}

// Gem i at (r, c) with that rotation, kept inside the mine; ok = it overlaps no other gem
function candidate(i, r, c, rot) {
    const s = session;
    const {h, w} = gemBox(s.gems[i].shape, rot);
    const gem = {...s.gems[i], r: Math.max(0, Math.min(r, N - h)), c: Math.max(0, Math.min(c, N - w)), rot};
    const others = draftGems().map((x, k) => k === i ? null : x).filter(Boolean);
    return {gem, ok: !!P.buildMap([...others, gem])};
}

function draftPreview() {
    const s = session;
    const d = s.draft;
    if (!editable()) return null;
    if (d.drag && d.drag.moved && d.drag.cell) {
        const {i, offR, offC, rot, cell} = d.drag;
        return candidate(i, cell.r - offR, cell.c - offC, rot);
    }
    if (d.selected === null || !d.hover) return null;
    const {h, w} = gemBox(s.gems[d.selected].shape, d.rot);
    return candidate(d.selected, d.hover.r - Math.floor((h - 1) / 2), d.hover.c - Math.floor((w - 1) / 2), d.rot);
}

function gemAt(r, c) {
    return draftGems().findIndex(g => g && P.gemCells(g).some(cell => cell.r === r && cell.c === c));
}

// The sketch can be changed while solving, and while hiding the own gems until they are confirmed
function editable() {
    const s = session;
    if (!s || s.mode === 'example' || viewingMine()) return false;
    if (placingMine()) return !s.myMine;
    return s.status === 'playing';
}

function sketchChanged() {
    saveLocalGame();
    renderAll();
}

// Mine cell under a pointer; a finger hides what is under it, so on touch the gem goes a little above it
function cellAt(e, lift = false) {
    const board = $('mine-board');
    const pt = board.createSVGPoint();
    pt.x = e.clientX;
    pt.y = e.clientY;
    const loc = pt.matrixTransform(board.getScreenCTM().inverse());
    const y = loc.y - (lift ? 9 : 0);
    const r = Math.floor((y - 10) / 10), c = Math.floor((loc.x - 10) / 10);
    return {r, c, inside: loc.x >= 0 && loc.x <= 100 && loc.y >= 0 && loc.y <= 100, onGrid: r >= 0 && r < N && c >= 0 && c < N};
}

function startDrag(e, i, offR, offC, from) {
    const d = session.draft;
    d.drag = {i, offR, offC, rot: from ? from.rot : d.rot, from, x: e.clientX, y: e.clientY, moved: false, cell: null, touch: e.pointerType !== 'mouse'};
}

function onBoardPointerDown(e) {
    if (!editable() || e.button > 0 || e.target.closest('.port')) return;
    const d = session.draft;
    const cell = cellAt(e);
    if (!cell.onGrid) return;
    const i = gemAt(cell.r, cell.c);
    if (i !== -1) {
        if (d.locked.has(i)) {
            showToast(t('gem_locked'));
            return;
        }
        const g = d.placed[i];
        startDrag(e, i, cell.r - g.r, cell.c - g.c, g);
    } else {
        // An empty cell: it takes the gem chosen in the tray, if any; otherwise it only lets go of the selection
        const next = d.selected;
        if (next === null) {
            if (d.active !== null) {
                d.active = null;
                renderAll();
            }
            return;
        }
        const {h, w} = gemBox(session.gems[next].shape, d.rot);
        d.selected = next;
        startDrag(e, next, Math.floor((h - 1) / 2), Math.floor((w - 1) / 2), null);
        d.drag.cell = {r: cell.r, c: cell.c};
    }
    e.preventDefault();
}

function onTrayPointerDown(e) {
    const btn = e.target.closest('.tray-gem');
    if (!btn || btn.disabled || !editable() || e.button > 0) return;
    const d = session.draft;
    const i = Number(btn.dataset.i);
    const from = d.placed[i];
    const {h, w} = gemBox(session.gems[i].shape, from ? from.rot : d.rot);
    startDrag(e, i, Math.floor((h - 1) / 2), Math.floor((w - 1) / 2), from);
    d.drag.fromTray = true;
    e.preventDefault();
}

let dragFrame = 0;

function onPointerMove(e) {
    const s = session;
    if (!s || !s.draft) return;
    const d = s.draft;
    if (!d.drag) {
        // Mouse only: the gem chosen in the tray follows the pointer before the click
        if (e.pointerType === 'mouse' && d.selected !== null && editable() && e.target.closest && e.target.closest('#mine-board')) {
            const cell = cellAt(e);
            const hover = cell.onGrid ? {r: cell.r, c: cell.c} : null;
            if (JSON.stringify(hover) !== JSON.stringify(d.hover)) {
                d.hover = hover;
                renderBoard();
            }
        }
        return;
    }
    if (!d.drag.moved && Math.hypot(e.clientX - d.drag.x, e.clientY - d.drag.y) < 6) return;
    d.drag.moved = true;
    const cell = cellAt(e, d.drag.touch);
    d.drag.cell = cell.onGrid || cell.inside ? {r: cell.r, c: cell.c} : null;
    d.drag.outside = !cell.inside;
    if (!dragFrame) dragFrame = requestAnimationFrame(() => {
        dragFrame = 0;
        if (session) renderBoard();
    });
    e.preventDefault();
}

function onPointerUp(e) {
    const s = session;
    if (!s || !s.draft || !s.draft.drag) return;
    const d = s.draft;
    const drag = d.drag;
    d.drag = null;
    if (!drag.moved) {
        if (drag.fromTray) {
            // A tap on the tray: choose that gem (or select it where it lies)
            if (drag.from) {
                d.active = d.active === drag.i ? null : drag.i;
                d.selected = null;
            } else {
                d.selected = d.selected === drag.i ? null : drag.i;
                d.active = null;
            }
            renderAll();
            return;
        }
        if (drag.from) {
            // A tap on a gem selects it; a second tap turns it
            if (d.active === drag.i) rotateActive();
            else {
                d.active = drag.i;
                d.selected = null;
                renderAll();
            }
            return;
        }
        // A tap on an empty cell drops the chosen gem there
        placeGem(drag.i, candidate(drag.i, drag.cell.r - drag.offR, drag.cell.c - drag.offC, drag.rot));
        return;
    }
    if (drag.outside || !drag.cell) {
        // Dropped outside the mine: back to the tray
        if (drag.from) {
            d.placed[drag.i] = null;
            d.active = null;
            d.selected = null;
            sketchChanged();
        } else renderAll();
        return;
    }
    const cand = candidate(drag.i, drag.cell.r - drag.offR, drag.cell.c - drag.offC, drag.rot);
    if (!cand.ok) {
        showToast(t('err_overlap'));
        renderAll();
        return;
    }
    placeGem(drag.i, cand);
}

function placeGem(i, cand) {
    const d = session.draft;
    if (!cand.ok) {
        showToast(t('err_overlap'));
        renderBoard();
        return;
    }
    d.placed[i] = {r: cand.gem.r, c: cand.gem.c, rot: cand.gem.rot};
    // The gem just placed stays selected; the next one is chosen by the player
    d.active = i;
    d.selected = null;
    d.hover = null;
    d.rot = 0;
    playSound('place');
    sketchChanged();
}

// Turns the selected gem where it lies; if it does not fit, it moves by one cell to make room
function rotateActive() {
    const s = session;
    if (!editable()) return;
    const d = s.draft;
    if (d.drag) {
        d.drag.rot = (d.drag.rot + 1) % P.ORIENTATIONS[s.gems[d.drag.i].shape].length;
        renderBoard();
        return;
    }
    if (d.active === null || !d.placed[d.active]) {
        if (d.selected === null) return showToast(t('rotate_hint'));
        d.rot = (d.rot + 1) % P.ORIENTATIONS[s.gems[d.selected].shape].length;
        renderBoard();
        return;
    }
    const i = d.active;
    const g = d.placed[i];
    const count = P.ORIENTATIONS[s.gems[i].shape].length;
    if (count === 1) return showToast(t('rotate_same'));
    const rot = (g.rot + 1) % count;
    for (const [dr, dc] of [[0, 0], [0, -1], [-1, 0], [-1, -1], [1, 0], [0, 1], [1, 1], [-1, 1], [1, -1], [0, -2], [-2, 0]]) {
        const cand = candidate(i, g.r + dr, g.c + dc, rot);
        if (cand.ok) {
            d.placed[i] = {r: cand.gem.r, c: cand.gem.c, rot};
            playSound('place');
            sketchChanged();
            return;
        }
    }
    showToast(t('rotate_no_room'));
}

function removeActive() {
    const d = session && session.draft;
    if (!editable() || !d || d.active === null) return;
    d.placed[d.active] = null;
    d.selected = null;
    d.active = null;
    sketchChanged();
}

function moveActive(dr, dc) {
    const d = session.draft;
    const g = d.placed[d.active];
    const cand = candidate(d.active, g.r + dr, g.c + dc, g.rot);
    if (cand.ok && (cand.gem.r !== g.r || cand.gem.c !== g.c)) {
        d.placed[d.active] = {r: cand.gem.r, c: cand.gem.c, rot: g.rot};
        sketchChanged();
    }
}

// The small Rotate / Remove bar floats over the selected gem
function renderGemToolbar() {
    const bar = $('gem-toolbar');
    const s = session;
    const d = s && s.draft;
    const g = d && d.active !== null && !(d.drag && d.drag.moved) && editable() ? draftGems()[d.active] : null;
    bar.classList.toggle('hidden', !g);
    if (!g) return;
    const cells = P.gemCells(g);
    const top = Math.min(...cells.map(x => x.r)), bottom = Math.max(...cells.map(x => x.r)) + 1;
    const left = Math.min(...cells.map(x => x.c)), right = Math.max(...cells.map(x => x.c)) + 1;
    const below = top < 2;
    bar.style.left = Math.max(22, Math.min(78, 10 + (left + right) * 5)) + '%'; // the bar stays over the mine
    bar.style.top = (10 + (below ? bottom : top) * 10) + '%';
    bar.classList.toggle('below', below);
}

// ==================== PROBES AND ANSWERS ====================
function onPortActivate(p) {
    const s = session;
    if (!s || !isMyTurn()) return;
    if (usedPorts().has(p)) {
        const pr = myProbes().find(x => x.p === p || x.exit === p);
        if (pr) showToast(probeText(pr));
        return;
    }
    if (s.mode === 'online') probeOnline(p);
    else {
        const map = P.buildMap(s.hidden);
        const res = P.trace(map, s.hidden, p);
        addProbe({s: s.mySlot, p, exit: res.exit, colors: res.colors});
        saveLocalGame();
        renderAll();
    }
}

function addProbe(pr, quiet = false) {
    const s = session;
    s.probes.push(pr);
    s.history.push({t: 'p', i: s.probes.length - 1});
    s.lastProbe = s.probes.length - 1;
    if (!quiet) {
        playSound(pr.exit === null ? 'absorbed' : 'beam', pr.colors);
        // In a duel the opponent's beams went into the own mine
        showToast(s.mode === 'online' && pr.s !== s.mySlot ? `${nameOf(pr.s)} ${t('log_in_your_mine')}: ${probeText(pr)}` : probeText(pr));
    }
}

// Duel: the gems placed on the board become the own hidden mine
async function confirmMine() {
    const s = session;
    if (!s || !placingMine() || s.myMine || !draftComplete()) return;
    const layout = draftGems();
    setBusy($('btn-answer'), true);
    const res = await api('place', {token: s.token, layout: P.encode(layout)});
    setBusy($('btn-answer'), false);
    if (session !== s) return;
    if (res.error) return showError(res.error);
    s.myMine = layout;
    s.draft = newDraft(s.gems.length);
    playSound('place');
    renderAll();
    pollServer();
}

async function submitAnswer() {
    const s = session;
    if (placingMine()) return confirmMine();
    if (!s || !draftComplete() || !isMyTurn()) return;
    const answer = draftGems();
    if (s.mode === 'online') {
        s.inFlight = true;
        setBusy($('btn-answer'), true);
        const res = await api('guess', {token: s.token, layout: P.encode(answer), n: s.history.length});
        setBusy($('btn-answer'), false);
        s.inFlight = false;
        if (session !== s) return;
        if (res.error) showError(res.error);
        else if (!res.ok) {
            playSound('wrong');
            showToast(t('answer_wrong_duel'));
        }
        pollServer();
        return;
    }
    if (P.isCorrect(answer, s.hidden)) {
        s.guesses.push({s: s.mySlot, ok: true});
        s.history.push({t: 'g'});
        endGame(true, 'solved');
    } else {
        s.wrong++;
        s.guesses.push({s: s.mySlot, ok: false});
        s.history.push({t: 'g'});
        playSound('wrong');
        showToast(t('answer_wrong', {n: PENALTY}));
        saveLocalGame();
        renderAll();
    }
}

// Solo: puts one hidden gem in its true place in the sketch, for a penalty
function revealGem() {
    const s = session;
    if (!s || s.mode === 'online' || s.status !== 'playing') return;
    openConfirm(t('hint_title'), t('hint_text', {n: PENALTY}), t('hint'), () => {
        const d = s.draft;
        // A gem not yet locked, preferably one the sketch has in the wrong place
        const candidates = s.gems.map((g, i) => i).filter(i => !d.locked.has(i));
        const wrongOnes = candidates.filter(i => {
            const real = s.hidden.find((h, k) => h.shape === s.gems[i].shape && h.color === s.gems[i].color);
            const mine = d.placed[i];
            return !mine || mine.r !== real.r || mine.c !== real.c || mine.rot !== real.rot;
        });
        const pool = wrongOnes.length ? wrongOnes : candidates;
        const i = pool[Math.floor(Math.random() * pool.length)];
        const real = s.hidden.find(h => h.shape === s.gems[i].shape && h.color === s.gems[i].color);
        // Gems of the sketch lying where the revealed one goes move back to the tray
        d.placed[i] = null;
        const placedNow = draftGems();
        const target = {...s.gems[i], r: real.r, c: real.c, rot: real.rot};
        const targetCells = P.gemCells(target).map(c => c.r * N + c.c);
        placedNow.forEach((g, k) => {
            if (g && P.gemCells(g).some(c => targetCells.includes(c.r * N + c.c))) d.placed[k] = null;
        });
        d.placed[i] = {r: real.r, c: real.c, rot: real.rot};
        d.locked.add(i);
        if (d.selected === i) d.selected = null;
        s.hints++;
        playSound('place');
        showToast(t('hint_done', {gem: gemName(s.gems[i])}));
        saveLocalGame();
        renderAll();
    });
}

// ==================== SOLO AND DAILY ====================
function baseSession(mode, extra) {
    sessionSeq++;
    return Object.assign({
        mode, seq: sessionSeq, status: 'playing', mySlot: 1, names: {1: playerName, 2: ''}, wins: {1: 0, 2: 0},
        probes: [], guesses: [], history: [], wrong: 0, hints: 0, turn: 1, ms: {1: 0, 2: 0}, timeControl: 0, lastProbe: null,
    }, extra);
}

function sortedGems(list) {
    return list.map(g => ({shape: g.shape, color: g.color})).sort((a, b) => a.shape.localeCompare(b.shape));
}

function startSolo(level, daily = false) {
    initAudio();
    const date = todayKey();
    const hidden = daily ? P.generate('medium', P.seededRandom('prism-mine-' + date)) : P.generate(level);
    const gems = sortedGems(hidden);
    session = baseSession(daily ? 'daily' : 'solo', {level: daily ? 'medium' : level, date, hidden, gems, draft: newDraft(gems.length)});
    if (daily) session.counted = !dailyRecord();
    enterGameScreen();
}

// An unfinished daily mine survives a reload
function saveLocalGame() {
    const s = session;
    if (!s || s.mode !== 'daily' || s.status !== 'playing') return;
    storageSet('pm_daily_progress', JSON.stringify({date: s.date, mine: P.encode(s.hidden), probes: s.probes, guesses: s.guesses, history: s.history, wrong: s.wrong, hints: s.hints, placed: s.draft.placed, locked: [...s.draft.locked]}));
}

function resumeDaily() {
    try {
        const saved = JSON.parse(storageGet('pm_daily_progress') || 'null');
        if (!saved || saved.date !== session.date || (saved.mine && saved.mine !== P.encode(session.hidden))) return;
        Object.assign(session, {probes: saved.probes, guesses: saved.guesses, history: saved.history, wrong: saved.wrong, hints: saved.hints});
        session.draft.placed = saved.placed;
        session.draft.locked = new Set(saved.locked);
    } catch (e) {
    }
}

function dailyRecord() {
    try {
        const rec = JSON.parse(storageGet('pm_daily') || 'null');
        return rec && rec.date === todayKey() ? rec : null;
    } catch (e) {
        return null;
    }
}

function renderDailyStatus() {
    const rec = dailyRecord();
    $('daily-status').textContent = rec ? (rec.solved ? t('daily_done', {score: rec.score, beams: rec.beams}) : t('daily_failed')) : t('daily_new');
    $('btn-start-daily').textContent = rec ? t('daily_again') : t('daily_btn');
}

function renderLevelNote() {
    const level = document.querySelector('input[name="solo-level"]:checked').value;
    const best = storageGet('pm_best_' + level);
    const cfg = P.LEVELS[level];
    $('level-note').textContent = t('level_note_' + level, {n: cfg.gems}) + (best ? ' ' + t('best_score', {score: best}) : '');
}

function enterGameScreen() {
    const s = session;
    if (s.mode === 'daily') resumeDaily();
    $('chat').classList.toggle('hidden', s.mode !== 'online');
    $('btn-chat-toggle').classList.remove('has-unread');
    $('mode-badge').textContent = s.mode === 'online' ? t('tab_online') : s.mode === 'daily' ? t('tab_daily') : s.mode === 'example' ? t('example_badge') : t('level_' + s.level);
    closePanel();
    showModal('gameover-overlay', false);
    showScreen('game');
    renderAll();
}

// ==================== EXAMPLE GAME ====================
// A fixed mine and a few beams, each one teaching a rule; the player only moves forward and back
const EXAMPLE = {
    mine: [
        {shape: 'cube', color: 'R', r: 2, c: 2, rot: 0},
        {shape: 'shard', color: 'B', r: 5, c: 5, rot: 0},
        {shape: 'slab', color: 'Y', r: 1, c: 5, rot: 1},
    ],
    steps: [
        {key: 'ex_intro'},
        {key: 'ex_empty', port: 0},
        {key: 'ex_flat', port: 29},
        {key: 'ex_cube', port: 2, place: 'cube'},
        {key: 'ex_shard_flat', port: 13},
        {key: 'ex_shard', port: 18, place: 'shard'},
        {key: 'ex_turn', port: 5, place: 'slab'},
        {key: 'ex_mix', port: 26},
        {key: 'ex_answer', answer: true},
    ],
};

function startExample() {
    initAudio();
    const hidden = EXAMPLE.mine.map(g => ({...g}));
    const gems = sortedGems(hidden);
    session = baseSession('example', {level: 'easy', hidden, gems, draft: newDraft(gems.length), exampleStep: 0, examplePort: null});
    showModal('howto-overlay', false);
    enterGameScreen();
    showExampleStep(0);
}

// Rebuilds the state of step k from scratch, so going back is as easy as going forward
function showExampleStep(k) {
    const s = session;
    if (!s || s.mode !== 'example') return;
    const steps = EXAMPLE.steps;
    s.exampleStep = Math.max(0, Math.min(k, steps.length - 1));
    s.probes = [];
    s.history = [];
    s.guesses = [];
    s.draft = newDraft(s.gems.length);
    s.examplePort = null;
    s.lastProbe = null;
    const map = P.buildMap(s.hidden);
    for (let i = 0; i <= s.exampleStep; i++) {
        const step = steps[i];
        if (step.port !== undefined) {
            const res = P.trace(map, s.hidden, step.port);
            addProbe({s: 1, p: step.port, exit: res.exit, colors: res.colors}, true);
        }
        if (step.place) {
            const gi = s.gems.findIndex(g => g.shape === step.place);
            const real = s.hidden.find(h => h.shape === step.place);
            s.draft.placed[gi] = {r: real.r, c: real.c, rot: real.rot};
        }
        if (step.answer) {
            s.guesses.push({s: 1, ok: true});
            s.history.push({t: 'g'});
        }
    }
    const current = steps[s.exampleStep];
    s.examplePort = current.port !== undefined ? current.port : null;
    if (current.port !== undefined) {
        const last = s.probes[s.probes.length - 1];
        playSound(last.exit === null ? 'absorbed' : 'beam', last.colors);
    } else if (current.answer) playSound('right');
    renderAll();
    $('example-step').textContent = t('example_step', {n: s.exampleStep + 1, total: steps.length});
    $('example-text').innerHTML = t(current.key);
    $('btn-ex-prev').disabled = s.exampleStep === 0;
    const lastStep = s.exampleStep === steps.length - 1;
    $('btn-ex-next').textContent = lastStep ? t('example_play') : t('example_next');
}

// ==================== ONLINE DUEL ====================
async function createOnlineGame() {
    initAudio();
    const btn = $('btn-create');
    setBusy(btn, true);
    const res = await api('create_room', {
        name: playerName || t('player_one'),
        time: parseInt(document.querySelector('input[name="time-online"]:checked').value, 10) * 60,
        level: document.querySelector('input[name="online-level"]:checked').value,
    });
    setBusy(btn, false);
    if (res.error) return showError(res.error);
    setupOnlineSession(res.code, res.token, 1, false);
}

async function joinOnlineGame() {
    initAudio();
    const code = $('room-code').value.trim().toUpperCase();
    if (!code) return;
    const btn = $('btn-join');
    setBusy(btn, true);
    const res = await api('join_room', {code, name: playerName || t('player_two')});
    setBusy(btn, false);
    if (res.error) return showError(res.error);
    setupOnlineSession(res.code, res.token, 2, false);
}

async function findRandomGame() {
    initAudio();
    const btn = $('btn-random');
    setBusy(btn, true);
    const res = await api('find_match', {name: playerName || t('player_one'), lang: currentLang});
    setBusy(btn, false);
    if (res.error) return showError(res.error);
    setupOnlineSession('', res.token, res.matched ? 2 : 1, true);
}

function setupOnlineSession(code, token, slot, isPublic) {
    session = baseSession('online', {
        status: 'waiting', code, token, mySlot: slot, isPublic, names: {1: '', 2: ''}, lastMessageId: 0, players: [],
        gems: [], draft: newDraft(0), round: 0, rematchSent: false, hidden: null, myMine: null, view: 'target', placeLeftMs: 0,
    });
    session.names[slot] = playerName;
    sessionSet('pm_session', JSON.stringify({code, token, slot, isPublic}));
    $('chat-list').innerHTML = '';
    if (isPublic) {
        $('waiting-title').textContent = t('searching_title');
        $('waiting-public-desc').textContent = t('searching_desc', {language: languageName()});
        $('waiting-private').classList.add('hidden');
    } else {
        $('waiting-title').textContent = t('waiting_title');
        $('waiting-public-desc').textContent = '';
        $('waiting-private').classList.remove('hidden');
        $('display-code').textContent = code;
    }
    showScreen('waiting');
    clearInterval(pollTimer);
    pollTimer = setInterval(pollServer, POLL_MS);
    pollServer();
}

async function pollServer() {
    const s = session;
    if (!s || s.mode !== 'online' || !s.token) return;
    if (s.polling) {
        s.pollAgain = true;
        return;
    }
    s.polling = true;
    const res = await api('poll', {token: s.token, since: s.lastMessageId || 0});
    s.polling = false;
    if (session !== s) return;
    if (res.error) {
        if (res.error === 'invalid_session') {
            showError('invalid_session');
            resetToLobby();
        }
        return;
    }
    handlePollUpdate(res);
    if (s.pollAgain) {
        s.pollAgain = false;
        pollServer();
    }
}

function handlePollUpdate(data) {
    const s = session;
    s.code = data.code;
    s.mySlot = data.mySlot;
    s.timeControl = data.timeControl;
    s.level = data.level;
    s.ms = {1: data.p1Ms, 2: data.p2Ms};
    s.clockSyncedAt = Date.now();
    s.players = data.players;
    data.players.forEach(p => {
        s.names[p.slot] = p.name;
        s.wins[p.slot] = p.wins;
    });

    s.placeLeftMs = data.placeLeftMs;
    s.placeSyncedAt = Date.now();

    // A new round (or the first one): new gems to hide and an empty sketch
    if (data.round !== s.round) {
        s.round = data.round;
        s.gems = data.gems;
        s.draft = newDraft(s.gems.length);
        s.probes = [];
        s.guesses = [];
        s.history = [];
        s.hidden = null;
        s.myMine = null;
        s.view = 'target';
        s.rematchSent = false;
        if (s.status === 'finished') s.status = data.status === 'finished' ? 'playing' : data.status;
        showModal('gameover-overlay', false);
    }
    if (data.myMine && !s.myMine) s.myMine = P.decode(data.myMine);

    if (s.status === 'waiting' && data.status !== 'waiting') {
        s.status = data.status === 'finished' ? 'playing' : data.status;
        enterGameScreen();
    }
    // Both mines are hidden: the duel starts, with an empty sketch of the other mine
    if (s.status === 'placing' && (data.status === 'playing' || data.status === 'finished')) {
        s.status = 'playing';
        s.draft = newDraft(s.gems.length);
        s.view = 'target';
        playSound('place');
        showToast(t('duel_starts'));
    }

    // New actions since the last poll: a reload brings many at once, shown quietly
    const fresh = data.actions.slice(s.history.length);
    fresh.forEach((a, k) => {
        if (a.t === 'p') addProbe({s: a.s, p: a.p, exit: a.exit, colors: a.colors}, fresh.length > 2 || k < fresh.length - 1);
        else {
            s.guesses.push({s: a.s, ok: a.ok});
            s.history.push({t: 'g'});
            if (!a.ok && a.s !== s.mySlot && fresh.length <= 2) showToast(t('opp_wrong', {name: nameOf(a.s)}));
        }
    });
    s.turn = data.turn;

    if (data.status === 'finished' && s.status !== 'finished') {
        s.hidden = P.decode(data.oppMine);
        const winner = data.result === '1-0' ? 1 : 2;
        endGame(winner === s.mySlot, data.reason, winner);
    }

    if (s.status !== 'waiting') {
        renderAll();
        renderOffline();
        renderRematchStatus();
        startClock();
    }
    addChatMessages(data.messages || []);
}

async function probeOnline(p) {
    const s = session;
    s.inFlight = true;
    renderBoard();
    const res = await api('probe', {token: s.token, port: p, n: s.history.length});
    s.inFlight = false;
    if (session !== s) return;
    if (res.error) showError(res.error);
    pollServer();
}

// Online clocks tick here between polls; while hiding the gems the countdown of the placement shows instead
function startClock() {
    clearInterval(clockInterval);
    const s = session;
    if (!s || s.mode !== 'online') return;
    clockInterval = setInterval(() => {
        if (session !== s || s.status === 'finished') return clearInterval(clockInterval);
        // The countdown has a line of its own: the polls redraw the rest without touching it
        const countdown = $('place-countdown');
        const counting = s.status === 'placing' && !s.myMine && s.placeLeftMs > 0;
        countdown.classList.toggle('hidden', !counting);
        if (counting) {
            const left = Math.ceil(Math.max(0, s.placeLeftMs - (Date.now() - s.placeSyncedAt)) / 1000);
            const text = t('place_time_left', {sec: left});
            if (countdown.textContent !== text) countdown.textContent = text;
            countdown.classList.toggle('urgent', left <= 15);
        }
        if (s.status === 'placing') return;
        if (s.status !== 'playing' || s.timeControl <= 0) return;
        const now = Date.now();
        s.ms[s.turn] = Math.max(0, s.ms[s.turn] - (now - s.clockSyncedAt));
        s.clockSyncedAt = now;
        renderClocks();
    }, 250);
}

// The opponent lost the connection: a countdown to the automatic win (the server decides at zero)
function renderOffline() {
    const s = session;
    const opp = s && s.mode === 'online' && (s.status === 'playing' || s.status === 'placing') ? s.players.find(p => p.slot !== s.mySlot) : null;
    if (!opp || opp.online) {
        if (s) s.offlineDeadline = null;
        clearInterval(offlineTimer);
        offlineTimer = null;
        showModal('offline-overlay', false);
        return;
    }
    const deadline = Date.now() + Math.max(0, AUTO_WIN_S - opp.idle) * 1000;
    if (!s.offlineDeadline || Math.abs(s.offlineDeadline - deadline) > 2500) s.offlineDeadline = deadline;
    $('offline-title').textContent = t('offline_title', {name: opp.name});
    showModal('offline-overlay', true);
    if (!offlineTimer) offlineTimer = setInterval(tickOffline, 250);
    tickOffline();
}

function tickOffline() {
    const s = session;
    if (!s || !s.offlineDeadline) return;
    const left = Math.max(0, s.offlineDeadline - Date.now());
    const sec = Math.ceil(left / 1000);
    $('offline-count').textContent = `${Math.floor(sec / 60)}:${String(sec % 60).padStart(2, '0')}`;
    $('offline-ring').style.strokeDashoffset = String(283 * (1 - left / (AUTO_WIN_S * 1000)));
    $('offline-text').textContent = left > 0 ? t('offline_text') : t('offline_now');
}

function renderRematchStatus() {
    const s = session;
    if (!s || s.mode !== 'online' || s.status !== 'finished') return;
    const me = s.players.find(p => p.slot === s.mySlot);
    const opp = s.players.find(p => p.slot !== s.mySlot);
    if (me && me.wantsRematch) s.rematchSent = true;
    let status = '';
    if (!opp || opp.left) status = t('opponent_left');
    else if (s.rematchSent) status = t('rematch_requested');
    else if (opp.wantsRematch) status = t('rematch_offer', {name: opp.name});
    $('rematch-status').textContent = status;
    $('btn-rematch').disabled = s.rematchSent || !opp || opp.left;
}

function addChatMessages(list) {
    const s = session;
    list.forEach(msg => {
        if (msg.id <= s.lastMessageId) return;
        s.lastMessageId = msg.id;
        const mine = msg.slot === s.mySlot;
        const li = document.createElement('li');
        li.className = `chat-msg ${mine ? 'me' : 'them'}`;
        li.textContent = msg.text;
        $('chat-list').appendChild(li);
        $('chat-list').scrollTop = $('chat-list').scrollHeight;
        if (!mine && !$('side-panel').classList.contains('open')) $('btn-chat-toggle').classList.add('has-unread');
    });
}

// ==================== END OF GAME ====================
const GAMEOVER_ICONS = {
    win: `<svg viewBox="0 0 60 60" width="60" height="60" aria-hidden="true"><path d="M15 22h30l8 10-23 20L7 32z" fill="var(--accent)" opacity=".9"/><path d="M7 32h46M30 52l-7-20 7-10 7 10z" fill="none" stroke="var(--accent-ink)" stroke-width="2" stroke-linejoin="round"/><path d="M30 6v6M14 10l4 4M46 10l-4 4" stroke="var(--accent)" stroke-width="3" stroke-linecap="round"/></svg>`,
    lose: `<svg viewBox="0 0 60 60" width="60" height="60" fill="none" stroke="var(--muted)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 22h30l8 10-23 20L7 32z"/><path d="M7 32h46M24 14l12 12M36 14L24 26"/></svg>`,
};

function endGame(won, reason, winnerSlot = 1) {
    const s = session;
    if (!s || s.status === 'finished') return;
    s.status = 'finished';
    clearInterval(clockInterval);
    const online = s.mode === 'online';
    if (!online) storageSet('pm_daily_progress', '');
    $('gameover-icon').innerHTML = won ? GAMEOVER_ICONS.win : GAMEOVER_ICONS.lose;
    playSound(won ? 'right' : 'lose');
    $('btn-share-result').classList.add('hidden');
    $('rematch-status').textContent = '';
    $('btn-rematch').disabled = false;

    if (online) {
        $('gameover-title').textContent = won ? t('you_won') : t('you_lost');
        $('gameover-text').textContent = t('reason_' + reason, {name: nameOf(other(winnerSlot)), winner: nameOf(winnerSlot)});
        $('gameover-score').textContent = '';
        $('score-line').textContent = t('score_tracker', {p1: nameOf(1), s1: s.wins[1] || 0, p2: nameOf(2), s2: s.wins[2] || 0});
        $('btn-rematch').textContent = t('rematch');
    } else {
        const score = scoreOf(s);
        $('gameover-title').textContent = won ? t('solved_title') : t('gave_up_title');
        $('gameover-text').textContent = won ? t('solved_text', {beams: s.probes.length, wrong: s.wrong, hints: s.hints}) : t('gave_up_text');
        $('gameover-score').textContent = won ? t('score_final', {score}) : '';
        $('score-line').textContent = '';
        $('btn-rematch').textContent = s.mode === 'daily' ? t('back_home') : t('play_again');
        if (s.mode === 'solo' && won) {
            const key = 'pm_best_' + s.level;
            const best = Number(storageGet(key)) || 0;
            if (!best || score < best) {
                storageSet(key, String(score));
                $('score-line').textContent = t('new_best');
            } else $('score-line').textContent = t('best_score', {score: best});
        }
        if (s.mode === 'daily') {
            if (s.counted) storageSet('pm_daily', JSON.stringify({date: s.date, solved: won, score, beams: s.probes.length}));
            if (won) $('btn-share-result').classList.remove('hidden');
            if (!s.counted) $('score-line').textContent = t('daily_not_counted');
        }
    }
    renderAll();
    renderOffline();
    const seq = s.seq;
    setTimeout(() => {
        if (session && session.seq === seq && session.status === 'finished') showModal('gameover-overlay', true);
    }, 700);
}

async function triggerRematch() {
    const s = session;
    if (!s) return;
    if (s.mode === 'online') {
        s.rematchSent = true;
        renderRematchStatus();
        const res = await api('rematch', {token: s.token});
        if (res.error) {
            s.rematchSent = false;
            showError(res.error);
        }
        pollServer();
        return;
    }
    if (s.mode === 'daily') return leaveGame();
    startSolo(s.level);
}

function shareResult() {
    const s = session;
    const squares = s.probes.map(pr => pr.exit === null ? '⬛' : ({'': '⬜', R: '🟥', Y: '🟨', B: '🟦', W: '⬜', RY: '🟧', BR: '🟪', BY: '🟩'}[pr.colors] || '🟫')).join('');
    copyText(`Prism Mine ${s.date}: ${t('share_line', {score: scoreOf(s), beams: s.probes.length})}\n${squares}\nhttps://www.saveriomorelli.com/projects/prism-mine/`, t('result_copied'));
}

async function leaveGame() {
    const s = session;
    resetToLobby();
    if (s && s.mode === 'online' && s.token) await api('leave', {token: s.token});
}

function resetToLobby() {
    $('place-countdown').classList.add('hidden');
    clearInterval(pollTimer);
    clearInterval(clockInterval);
    session = null;
    sessionSet('pm_session', null);
    renderOffline();
    document.querySelectorAll('.modal').forEach(m => m.classList.add('hidden'));
    closePanel();
    renderDailyStatus();
    renderLevelNote();
    showScreen('lobby');
}

function requestLeave() {
    const s = session;
    if (!s) return;
    if (s.mode === 'online' && (s.status === 'playing' || s.status === 'placing')) openConfirm(t('confirm_leave_title'), t('confirm_leave_text'), t('leave'), leaveGame);
    else leaveGame();
}

function requestResign() {
    const s = session;
    const active = () => session && (session.status === 'playing' || placingMine());
    if (!s || !active()) return;
    openConfirm(t('confirm_resign_title'), t(s.mode === 'online' ? 'confirm_resign_duel' : 'confirm_resign_text'), t('resign'), async () => {
        if (!active()) return;
        if (s.mode === 'online') {
            const res = await api('resign', {token: s.token});
            if (res.error) showError(res.error);
            pollServer();
        } else endGame(false, 'resign');
    });
}

function togglePanel() {
    const open = !$('side-panel').classList.contains('open');
    $('side-panel').classList.toggle('open', open);
    $('panel-backdrop').classList.toggle('open', open);
    if (open) $('btn-chat-toggle').classList.remove('has-unread');
}

function closePanel() {
    $('side-panel').classList.remove('open');
    $('panel-backdrop').classList.remove('open');
}

window.onAndroidBack = function () {
    if (closeTopModal()) return true;
    if ($('side-panel').classList.contains('open')) {
        closePanel();
        return true;
    }
    if (!$('gameover-overlay').classList.contains('hidden')) {
        showModal('gameover-overlay', false);
        return true;
    }
    if (session) {
        requestLeave();
        return true;
    }
    return false;
};

// ==================== SHEETS ====================
function renderRulesModal() {
    const r = RULES_TEXT[currentLang] || RULES_TEXT.en;
    const list = (items) => `<ul>${items.map(i => `<li>${i}</li>`).join('')}</ul>`;
    const shapes = P.SHAPE_KEYS.map(k => `<li class="rule-gem">${gemDiagram(k)}<div><strong>${escapeHtml(t('gem_' + k))}</strong><p>${escapeHtml(t('bounce_' + k))}</p></div></li>`).join('');
    const colors = ['R', 'Y', 'B', 'W', 'C', 'K'].map(c => `<li>${shapeIcon('cube', c, 18)}<span>${escapeHtml(t('color_' + c))}</span><small>${escapeHtml(t('color_' + c + '_desc'))}</small></li>`).join('');
    const mixes = ['RY', 'BR', 'BY', 'BRY', 'RW', 'BW'].map(m => `<li><span class="mix-swatch" style="background:${MIX[m]}"></span><span>${escapeHtml(colorWords(m))}</span></li>`).join('');
    $('rules-body').innerHTML = `
        <section><h4>${r.goal_t}</h4><p>${r.goal}</p></section>
        <section><h4>${r.beam_t}</h4><p>${r.beam}</p></section>
        <section><h4>${r.gems_t}</h4><p>${r.gems}</p></section>
        <section><h4>${escapeHtml(t('bounce_title'))}</h4><p>${escapeHtml(t('bounce_intro'))}</p><ul class="rule-gems">${shapes}</ul></section>
        <section><h4>${r.colors_t}</h4><p>${r.colors}</p><ul class="rule-colors">${colors}</ul><ul class="rule-mix">${mixes}</ul></section>
        <section><h4>${r.answer_t}</h4><p>${r.answer}</p></section>
        <section><h4>${r.duel_t}</h4>${list(r.duel)}</section>
        <section><h4>${r.tips_t}</h4>${list(r.tips)}</section>
        <section class="rules-credits"><h4>${escapeHtml(t('credits_title'))}</h4><p>${escapeHtml(t('credits_text'))}</p></section>`;
}

function renderHowToModal() {
    const target = $('howto-sheet-body');
    target.innerHTML = '';
    const copy = $('howto-list').cloneNode(true);
    copy.removeAttribute('id');
    target.appendChild(copy);
}

function renderReleasesModal() {
    $('release-list').innerHTML = RELEASES.map(rel => {
        const notes = (rel.notes[currentLang] || rel.notes.en || []).map(n => `<li>${n}</li>`).join('');
        return `<div class="release"><strong>v${rel.version}</strong><ul>${notes}</ul></div>`;
    }).join('');
}

// A piece of mine with real beams, traced by the engine: used by the lobby art and by the rules.
// crop = [firstRow, firstCol, lastRow, lastCol]. A beam that comes straight back is drawn with two arrow heads.
let diagramSeq = 0;

function beamDiagram(gems, ports, crop, cls = '') {
    const [r0, c0, r1, c1] = crop;
    const inside = (r, c) => r >= r0 && r <= r1 && c >= c0 && c <= c1;
    const center = (r, c) => [(c - c0) * 10 + 5, (r - r0) * 10 + 5];
    const defs = [];
    // One arrow head per beam, in the colour of its beam
    const arrow = (color, reverse) => {
        const id = 'dg' + (++diagramSeq);
        defs.push(`<marker id="${id}" viewBox="0 0 10 10" refX="7" refY="5" markerWidth="4" markerHeight="4" orient="${reverse ? 'auto-start-reverse' : 'auto'}"><path d="M0 0L10 5L0 10z" fill="${color}"/></marker>`);
        return `url(#${id})`;
    };
    let cells = '';
    for (let r = r0; r <= r1; r++) for (let c = c0; c <= c1; c++) cells += `<rect x="${(c - c0) * 10 + 0.4}" y="${(r - r0) * 10 + 0.4}" width="9.2" height="9.2" rx="1.2" class="dg-cell"/>`;
    const polys = gems.map(g => (P.gemCells(g) || []).filter(x => inside(x.r, x.c)).map(({r, c, t: type}) => `<polygon points="${CELL_POLY[type].split(' ').map(pt => {
        const [x, y] = pt.split(',').map(Number);
        return `${(c - c0 + x) * 10},${(r - r0 + y) * 10}`;
    }).join(' ')}" fill="${GEM_FILL[g.color]}"/>`).join('')).join('');
    const map = P.buildMap(gems);
    const STEP = [[0, -1], [1, 0], [0, 1], [-1, 0]]; // x, y for up, right, down, left
    const line = (pts) => pts.map(q => q.map(v => Math.round(v * 10) / 10).join(',')).join(' ');
    const beams = ports.map(p => {
        const res = P.trace(map, gems, p);
        const path = res.path.filter(([r, c]) => inside(r, c));
        if (!path.length) return '';
        const [fr, fc, fd] = path[0];
        const start = center(fr, fc).map((v, k) => v - STEP[fd][k] * 7);
        const color = res.exit === null ? '#7A6F66' : MIX[res.colors];
        if (res.exit === p) {
            // Straight back: from the edge to the face it bounced off, with arrow heads both ways
            let k = path.findIndex((q, i) => i > 0 && i + 1 < path.length && path[i + 1][0] === path[i - 1][0] && path[i + 1][1] === path[i - 1][1]);
            if (k < 0) k = 0;
            const face = k === 0 ? center(fr, fc).map((v, i) => v - STEP[fd][i] * 5)
                : center(path[k][0], path[k][1]).map((v, i) => (v + center(path[k - 1][0], path[k - 1][1])[i]) / 2);
            const pts = [start, ...path.slice(0, k).map(([r, c]) => center(r, c)), face];
            return `<polyline points="${line(pts)}" class="dg-beam" stroke="${color}" marker-start="${arrow(color, true)}" marker-end="${arrow(color, false)}"/>`;
        }
        const pts = [start, ...path.map(([r, c]) => center(r, c))];
        if (res.exit !== null) {
            const [lr, lc] = path[path.length - 1];
            const outDir = Math.floor(res.exit / P.SIZE); // the side it leaves by is also its direction
            pts.push(center(lr, lc).map((v, k) => v + STEP[outDir][k] * 7));
        }
        return `<polyline points="${line(pts)}" class="dg-beam" stroke="${color}" marker-end="${arrow(color, false)}"/>`;
    }).join('');
    const w = (c1 - c0 + 1) * 10, h = (r1 - r0 + 1) * 10;
    return `<svg class="diagram ${cls}" viewBox="-3 -3 ${w + 6} ${h + 6}" aria-hidden="true"><defs>${defs.join('')}</defs><rect x="-1.5" y="-1.5" width="${w + 3}" height="${h + 3}" rx="3" class="dg-floor"/>${cells}${polys}${beams}</svg>`;
}

// The lobby art: a real beam that bounces off two gems and comes out orange (red + yellow)
function renderHeroArt() {
    const gems = [{shape: 'diamond', color: 'B', r: 1, c: 1, rot: 0}, {shape: 'pyramid', color: 'R', r: 5, c: 5, rot: 1}, {shape: 'shard', color: 'Y', r: 1, c: 6, rot: 2}];
    $('hero-art').innerHTML = beamDiagram(gems, [9, 2], [0, 0, 7, 7], 'hero');
}

// How one gem sends the beams back: a turning beam and one that comes back, when the shape has both
function gemDiagram(shape) {
    const gem = {shape, color: 'B', r: 3, c: 3, rot: 0};
    const map = P.buildMap([gem]);
    const hits = [];
    for (let p = 0; p < P.PORTS; p++) {
        const res = P.trace(map, [gem], p);
        if (res.colors) hits.push({p, back: res.exit === p});
    }
    const turn = hits.find(h => !h.back);
    const back = hits.filter(h => h.back).find(h => !turn || Math.floor(h.p / P.SIZE) !== Math.floor(turn.p / P.SIZE)) || hits.find(h => h.back);
    const side = (h) => Math.floor(h.p / P.SIZE);
    // Shapes that never turn the beam: show two bounces from different sides
    const second = turn ? (back || hits.find(h => !h.back && h.p !== turn.p && h.p !== P.trace(map, [gem], turn.p).exit))
        : hits.filter(h => h.back).find(h => side(h) !== side(back));
    return beamDiagram([gem], [turn || back, second].filter(Boolean).map(h => h.p), [2, 2, 5, 5], 'rule-diagram');
}

function setLobbyStep(step) {
    $('step-name').classList.toggle('active', step === 1);
    $('step-mode').classList.toggle('active', step === 2);
    if (step === 2) $('greeting-text').textContent = t('greeting', {name: playerName});
}

window.onLanguageChanged = function () {
    $('random-desc').textContent = t('random_desc', {language: languageName()});
    if (playerName) $('greeting-text').textContent = t('greeting', {name: playerName});
    $('app-version').textContent = 'v' + APP_VERSION;
    renderDailyStatus();
    renderLevelNote();
    if (session) {
        $('mode-badge').textContent = session.mode === 'online' ? t('tab_online') : session.mode === 'daily' ? t('tab_daily') : session.mode === 'example' ? t('example_badge') : t('level_' + session.level);
        if (session.mode === 'example') showExampleStep(session.exampleStep);
        else renderAll();
    }
    if (!$('rules-overlay').classList.contains('hidden')) renderRulesModal();
};

// ==================== BOOT ====================
document.addEventListener('DOMContentLoaded', () => {
    setLanguage(getInitialLang());
    try {
        Object.assign(prefs, JSON.parse(storageGet('pm_prefs') || '{}'));
    } catch (e) {
    }
    $('btn-audio-toggle').classList.toggle('muted-icon', !prefs.sound);
    renderHeroArt();
    renderDailyStatus();
    renderLevelNote();

    playerName = storageGet('pm_name') || '';
    $('player-name').value = playerName;
    $('btn-continue').disabled = !playerName;
    setLobbyStep(playerName ? 2 : 1);

    $('player-name').addEventListener('input', (e) => $('btn-continue').disabled = e.target.value.trim() === '');
    $('step-name').addEventListener('submit', (e) => {
        e.preventDefault();
        const val = $('player-name').value.trim();
        if (!val) return;
        playerName = val;
        storageSet('pm_name', playerName);
        setLobbyStep(2);
    });
    $('btn-change-name').addEventListener('click', () => {
        setLobbyStep(1);
        $('player-name').focus();
        $('player-name').select();
    });

    document.querySelectorAll('.mode-tab').forEach(tab => tab.addEventListener('click', () => {
        document.querySelectorAll('.mode-tab').forEach(x => x.classList.toggle('active', x === tab));
        document.querySelectorAll('.mode-panel').forEach(p => p.classList.toggle('active', p.dataset.panel === tab.dataset.tab));
    }));
    document.querySelectorAll('input[name="solo-level"]').forEach(r => r.addEventListener('change', renderLevelNote));

    $('btn-start-solo').addEventListener('click', () => startSolo(document.querySelector('input[name="solo-level"]:checked').value));
    $('btn-start-daily').addEventListener('click', () => startSolo('medium', true));
    $('btn-create').addEventListener('click', createOnlineGame);
    $('btn-random').addEventListener('click', findRandomGame);
    $('room-code').addEventListener('input', (e) => {
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        $('btn-join').disabled = e.target.value.length !== 6;
    });
    $('card-join').addEventListener('submit', (e) => {
        e.preventDefault();
        joinOnlineGame();
    });

    // Board: ports are clicked; gems are tapped, dragged and dropped
    const board = $('mine-board');
    board.addEventListener('click', (e) => {
        const port = e.target.closest('.port');
        if (port) onPortActivate(Number(port.dataset.p));
    });
    board.addEventListener('pointerdown', onBoardPointerDown);
    $('tray').addEventListener('pointerdown', onTrayPointerDown);
    window.addEventListener('pointermove', onPointerMove, {passive: false});
    window.addEventListener('pointerup', onPointerUp);
    window.addEventListener('pointercancel', () => {
        if (session && session.draft && session.draft.drag) {
            session.draft.drag = null;
            renderAll();
        }
    });
    board.addEventListener('pointerleave', () => {
        if (session && session.draft.hover) {
            session.draft.hover = null;
            renderBoard();
        }
    });
    board.addEventListener('pointerover', (e) => {
        const port = e.target.closest('.port');
        if (port) showPath(Number(port.dataset.p));
    });
    board.addEventListener('pointerout', (e) => {
        if (e.target.closest('.port')) showPath(-1);
    });
    board.addEventListener('focusin', (e) => {
        const port = e.target.closest('.port');
        if (port) showPath(Number(port.dataset.p));
    });
    board.addEventListener('keydown', (e) => {
        const port = e.target.closest('.port');
        if (port && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            onPortActivate(Number(port.dataset.p));
        }
    });
    // Keyboard on the tray: Enter chooses a gem (or selects it on the board)
    $('tray').addEventListener('keydown', (e) => {
        const btn = e.target.closest('.tray-gem');
        if (!btn || !editable() || (e.key !== 'Enter' && e.key !== ' ')) return;
        e.preventDefault();
        const d = session.draft;
        const i = Number(btn.dataset.i);
        if (d.placed[i]) d.active = i;
        else d.selected = i;
        renderAll();
    });
    $('btn-rotate').addEventListener('click', rotateActive);
    $('btn-gem-rotate').addEventListener('click', rotateActive);
    $('btn-gem-remove').addEventListener('click', removeActive);
    $('btn-clear').addEventListener('click', () => {
        const s = session;
        if (!editable()) return;
        s.draft.placed = s.draft.placed.map((x, i) => s.draft.locked.has(i) ? x : null);
        s.draft.selected = null;
        s.draft.active = null;
        sketchChanged();
    });
    $('btn-answer').addEventListener('click', submitAnswer);
    $('btn-random-place').addEventListener('click', () => {
        const s = session;
        if (!placingMine() || s.myMine) return;
        const layout = P.randomLayout(s.gems);
        s.draft = newDraft(s.gems.length);
        layout.forEach((g, i) => s.draft.placed[i] = {r: g.r, c: g.c, rot: g.rot});
        playSound('place');
        renderAll();
    });
    document.querySelectorAll('#view-tabs [data-view]').forEach(b => b.addEventListener('click', () => {
        if (!session) return;
        session.view = b.dataset.view;
        session.draft.hover = null;
        renderAll();
    }));
    // Keyboard: R turns, the arrows move the selected gem, Delete puts it back, Esc lets it go
    document.addEventListener('keydown', (e) => {
        if (!session || !session.draft || !editable() || e.target.closest('input, textarea') || document.querySelector('.modal:not(.hidden)')) return;
        const d = session.draft;
        const moves = {ArrowUp: [-1, 0], ArrowDown: [1, 0], ArrowLeft: [0, -1], ArrowRight: [0, 1]};
        if (e.key === 'r' || e.key === 'R') rotateActive();
        else if (d.active !== null && moves[e.key]) {
            e.preventDefault();
            moveActive(...moves[e.key]);
        } else if (d.active !== null && (e.key === 'Delete' || e.key === 'Backspace')) {
            e.preventDefault();
            removeActive();
        } else if (e.key === 'Escape' && (d.active !== null || d.selected !== null)) {
            d.active = null;
            d.selected = null;
            renderAll();
        }
    });

    $('btn-hint').addEventListener('click', revealGem);
    document.querySelectorAll('[data-start-example]').forEach(b => b.addEventListener('click', startExample));
    $('btn-ex-prev').addEventListener('click', () => showExampleStep(session.exampleStep - 1));
    $('btn-ex-next').addEventListener('click', () => {
        if (session.exampleStep >= EXAMPLE.steps.length - 1) {
            leaveGame();
            document.querySelector('.mode-tab[data-tab="solo"]').click();
            document.querySelector('input[name="solo-level"][value="easy"]').checked = true;
            startSolo('easy');
        } else showExampleStep(session.exampleStep + 1);
    });
    document.addEventListener('keydown', (e) => {
        if (!session || session.mode !== 'example' || e.target.closest('input, textarea') || document.querySelector('.modal:not(.hidden)')) return;
        if (e.key === 'ArrowRight') $('btn-ex-next').click();
        if (e.key === 'ArrowLeft' && session.exampleStep > 0) showExampleStep(session.exampleStep - 1);
    });
    $('btn-resign').addEventListener('click', requestResign);
    $('btn-result').addEventListener('click', () => showModal('gameover-overlay', true));
    $('btn-leave-game').addEventListener('click', requestLeave);
    document.querySelector('.game-title').addEventListener('click', (e) => {
        e.preventDefault();
        requestLeave();
    });
    $('btn-leave-waiting').addEventListener('click', leaveGame);
    $('btn-audio-toggle').addEventListener('click', () => {
        prefs.sound = !prefs.sound;
        savePrefs();
        showToast(prefs.sound ? t('sound_on') : t('sound_off'));
    });
    $('btn-chat-toggle').addEventListener('click', togglePanel);
    $('btn-panel-toggle').addEventListener('click', togglePanel);
    $('btn-panel-close').addEventListener('click', closePanel);
    $('panel-backdrop').addEventListener('click', closePanel);

    document.querySelectorAll('.lang-btn, .lang-option').forEach(btn => btn.addEventListener('click', () => {
        setLanguage(btn.dataset.lang);
        showModal('lang-overlay', false);
    }));
    $('btn-lang-menu').addEventListener('click', () => showModal('lang-overlay', true));
    document.querySelectorAll('[data-open-rules]').forEach(b => b.addEventListener('click', () => {
        renderRulesModal();
        showModal('rules-overlay', true);
    }));
    document.querySelectorAll('[data-open-settings]').forEach(b => b.addEventListener('click', () => {
        $('opt-sound').checked = prefs.sound;
        $('opt-check').checked = prefs.check;
        $('opt-paths').checked = prefs.paths;
        showModal('settings-overlay', true);
    }));
    [['opt-sound', 'sound'], ['opt-check', 'check'], ['opt-paths', 'paths']].forEach(([id, key]) => $(id).addEventListener('change', () => {
        prefs[key] = $(id).checked;
        savePrefs();
    }));
    $('btn-howto').addEventListener('click', () => {
        renderHowToModal();
        showModal('howto-overlay', true);
    });
    $('btn-version').addEventListener('click', () => {
        renderReleasesModal();
        showModal('release-overlay', true);
    });
    $('btn-donate').addEventListener('click', () => showModal('donate-overlay', true));
    document.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => showModal(b.closest('.modal').id, false)));
    $('btn-confirm-no').addEventListener('click', () => showModal('confirm-overlay', false));
    $('btn-confirm-yes').addEventListener('click', () => {
        showModal('confirm-overlay', false);
        if (typeof confirmAction === 'function') confirmAction();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeTopModal();
    });
    document.querySelectorAll('.modal').forEach(modal => {
        if (STICKY_MODALS.includes(modal.id)) return;
        modal.addEventListener('click', (e) => {
            if (e.target === modal) showModal(modal.id, false);
        });
    });

    $('btn-rematch').addEventListener('click', triggerRematch);
    $('btn-share-result').addEventListener('click', shareResult);
    $('btn-review').addEventListener('click', () => showModal('gameover-overlay', false));
    $('btn-gameover-home').addEventListener('click', leaveGame);
    $('turn-indicator').addEventListener('click', () => {
        if (session && session.status === 'finished') showModal('gameover-overlay', true);
    });

    $('chat-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = $('chat-input');
        const text = input.value.trim();
        if (!text || !session || session.mode !== 'online') return;
        input.value = '';
        const res = await api('send_message', {token: session.token, text});
        if (res.error) {
            input.value = text;
            showError(res.error);
        }
        pollServer();
    });
    $('btn-copy-code').addEventListener('click', () => {
        if (session && session.code) copyText(session.code, t('copied'));
    });
    $('btn-share-link').addEventListener('click', () => {
        if (session && session.code) copyText(`${location.origin}${location.pathname}?join=${session.code}`, t('link_copied'));
    });
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) pollServer();
    });

    const joinCode = new URLSearchParams(location.search).get('join');
    if (joinCode) {
        const clean = joinCode.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
        $('room-code').value = clean;
        document.querySelector('.mode-tab[data-tab="online"]').click();
        $('btn-join').disabled = clean.length !== 6;
        $('card-join').classList.add('highlight');
    }
    // Shortcuts of the installed app (manifest.webmanifest): ?play=daily or ?play=example
    const play = new URLSearchParams(location.search).get('play');
    if (play === 'example') startExample();
    else if (play === 'daily') {
        document.querySelector('.mode-tab[data-tab="daily"]').click();
        if (playerName) startSolo('medium', true);
    }

    const saved = sessionGet('pm_session');
    if (saved) {
        try {
            const {code, token, slot, isPublic} = JSON.parse(saved);
            setupOnlineSession(code, token, slot, isPublic);
        } catch (e) {
            sessionSet('pm_session', null);
        }
    }
    $('app-version').textContent = 'v' + APP_VERSION;
});

if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => {
}));
