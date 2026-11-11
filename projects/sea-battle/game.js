// Sea Battle main client controller: lobby, fleet placement, battle (online, same device, computer).
const API_URL = 'api/index.php';
const POLL_MS = 1500;
const AUTO_WIN_S = 120; // the same as ABANDON_SECONDS in api/index.php
const AI_DELAY_MS = 700;
const HANDOVER_MS = 1200; // same device: time to see the result before the screen hides the fleets

const $ = (id) => document.getElementById(id);
const N = SeaBattle.SIZE;
const other = (slot) => slot === 1 ? 2 : 1;

let playerName = '';
let session = null;
let sessionSeq = 0;
let pollTimer = null;
let clockInterval = null;
let placeTimer = null;
let audioCtx = null;

const prefs = {sound: true, automark: true, coords: true, hull: 'steel'};

const HULLS = {
    steel: ['#8A9BAE', '#4E5F72'],
    navy: ['#5677A8', '#28406A'],
    olive: ['#8C9466', '#525A35'],
    sand: ['#C9B48A', '#8A744C'],
    graphite: ['#6B7280', '#2F343C'],
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

// Names and chat come from other players: never put them in the page as HTML
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

const shipName = (k) => t('ship_' + SeaBattle.SHIP_KEYS[k]);

// --- Sounds (synthesised, no files) ---
function initAudio() {
    if (!audioCtx) {
        const AudioClass = window.AudioContext || window.webkitAudioContext;
        if (AudioClass) audioCtx = new AudioClass();
    }
    if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
}

function noiseBurst(duration, filterFreq, gainValue, delay = 0, type = 'lowpass') {
    const now = audioCtx.currentTime + delay;
    const buffer = audioCtx.createBuffer(1, Math.floor(audioCtx.sampleRate * duration), audioCtx.sampleRate);
    const data = buffer.getChannelData(0);
    for (let i = 0; i < data.length; i++) data[i] = (Math.random() * 2 - 1) * Math.pow(1 - i / data.length, 2);
    const src = audioCtx.createBufferSource();
    src.buffer = buffer;
    const filter = audioCtx.createBiquadFilter();
    filter.type = type;
    filter.frequency.value = filterFreq;
    const gain = audioCtx.createGain();
    gain.gain.setValueAtTime(gainValue, now);
    gain.gain.exponentialRampToValueAtTime(0.001, now + duration);
    src.connect(filter).connect(gain).connect(audioCtx.destination);
    src.start(now);
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

function playSound(kind) {
    if (!prefs.sound) return;
    try {
        initAudio();
        if (!audioCtx) return;
        if (kind === 'miss') {
            noiseBurst(0.45, 1400, 0.35, 0, 'bandpass');
            tone(900, 300, 0.25, 0.06);
        } else if (kind === 'hit') {
            noiseBurst(0.6, 500, 0.7);
            tone(140, 45, 0.5, 0.35, 0, 'triangle');
        } else if (kind === 'sunk') {
            noiseBurst(0.9, 420, 0.75);
            tone(160, 40, 0.8, 0.35, 0, 'triangle');
            tone(520, 260, 0.6, 0.08, 0.35);
        } else if (kind === 'win') {
            [523, 659, 784, 1047].forEach((f, i) => tone(f, f, 0.32, 0.18, i * 0.11, 'triangle'));
        } else if (kind === 'lose') {
            [392, 330, 262].forEach((f, i) => tone(f, f * 0.98, 0.4, 0.15, i * 0.16, 'triangle'));
        } else if (kind === 'place') {
            tone(660, 520, 0.08, 0.12);
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
    toastTimer = setTimeout(() => toast.classList.remove('visible'), 2600);
}

function showError(code) {
    const key = 'err_' + code;
    showToast(t(key) !== key ? t(key) : t('err_generic'));
}

function closeTopModal() {
    const open = [...document.querySelectorAll('.modal:not(.hidden)')].filter(m => m.id !== 'gameover-overlay' && m.id !== 'offline-overlay').pop();
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

function applyPrefs() {
    const hull = HULLS[prefs.hull] || HULLS.steel;
    document.documentElement.style.setProperty('--hull', hull[0]);
    document.documentElement.style.setProperty('--hull-dark', hull[1]);
    document.body.classList.toggle('no-coords', !prefs.coords);
    $('btn-audio-toggle').classList.toggle('muted-icon', !prefs.sound);
    if (session) renderAll();
}

function savePrefs() {
    storageSet('sb_prefs', JSON.stringify(prefs));
    applyPrefs();
}

// --- API ---
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

// ==================== GRIDS ====================
// Each grid is a CSS grid: one row and one column of coordinates, then 10 x 10 cells; ships lie on top.
function buildGrid(el, clickable) {
    el.innerHTML = '';
    for (let c = 0; c < N; c++) {
        const lab = document.createElement('span');
        lab.className = 'grid-label';
        lab.textContent = 'ABCDEFGHIJ'[c];
        lab.style.gridArea = `1 / ${c + 2}`;
        el.appendChild(lab);
    }
    for (let r = 0; r < N; r++) {
        const lab = document.createElement('span');
        lab.className = 'grid-label';
        lab.textContent = r + 1;
        lab.style.gridArea = `${r + 2} / 1`;
        el.appendChild(lab);
    }
    for (let i = 0; i < N * N; i++) {
        const cell = document.createElement(clickable ? 'button' : 'div');
        if (clickable) cell.type = 'button';
        cell.className = 'cell';
        cell.dataset.i = i;
        cell.style.gridArea = `${Math.floor(i / N) + 2} / ${i % N + 2}`;
        el.appendChild(cell);
    }
}

function shipEl(ship, len, extra) {
    const el = document.createElement('div');
    el.className = `ship ship-${ship.d} ${extra}`;
    el.style.gridArea = `${ship.r + 2} / ${ship.c + 2} / span ${ship.d === 'v' ? len : 1} / span ${ship.d === 'h' ? len : 1}`;
    el.innerHTML = '<span class="ship-deck"></span>';
    return el;
}

// view: {ships: [{k, ship, cls}], marks: Map(cell -> class), last, fresh, disabled: Set, label: fn}
function renderGrid(el, view) {
    el.querySelectorAll('.ship').forEach(s => s.remove());
    const cells = el.querySelectorAll('.cell');
    cells.forEach(cell => {
        const i = Number(cell.dataset.i);
        const mark = view.marks.get(i) || '';
        cell.className = 'cell' + (mark ? ' ' + mark : '') + (view.last === i ? ' c-last' : '') + (view.fresh === i ? ' c-fresh' : '');
        if (cell.tagName === 'BUTTON') {
            cell.disabled = !!(view.disabled && view.disabled.has(i));
            const state = {'c-miss': 'aria_miss', 'c-hit': 'aria_hit', 'c-sunk': 'aria_sunk', 'c-cleared': 'aria_cleared'}[mark];
            cell.setAttribute('aria-label', SeaBattle.label(i) + (state ? ', ' + t(state) : ''));
        }
    });
    view.ships.forEach(({k, ship, cls}) => el.appendChild(shipEl(ship, SeaBattle.FLEET[k], cls || '')));
}

function renderPips(el, sunkSet) {
    el.innerHTML = SeaBattle.FLEET.map((len, k) => `<li class="pip ${sunkSet.has(k) ? 'sunk' : ''}" title="${escapeHtml(shipName(k))}">${'<i></i>'.repeat(len)}</li>`).join('');
}

// ==================== GAME STATE HELPERS ====================
// Shots are {s: shooter, i: cell, r: 'miss' | 'hit' | 'sunk', k: ship index when sunk}
const shotsBy = (slot) => session.shots.filter(s => s.s === slot);

// The ships of `slot` that the other player has sunk, with their position
function sunkShips(slot) {
    const out = [];
    shotsBy(other(slot)).forEach(s => {
        if (s.r !== 'sunk') return;
        const ship = session.fleets[slot] ? session.fleets[slot][s.k] : (session.oppShips || []).find(o => o.k === s.k);
        if (ship) out.push({k: s.k, ship});
    });
    return out;
}

// What `shooter` knows about the other fleet, as the computer player reads it
function knowledgeOf(shooter) {
    const k = new Array(N * N).fill(0);
    shotsBy(shooter).forEach(s => k[s.i] = s.r === 'miss' ? 1 : 2);
    const sunk = sunkShips(other(shooter));
    sunk.forEach(({k: idx, ship}) => {
        const cells = SeaBattle.shipCells(ship, SeaBattle.FLEET[idx]);
        cells.forEach(i => k[i] = 3);
        SeaBattle.around(cells).forEach(i => {
            if (k[i] === 0) k[i] = 1;
        });
    });
    const remaining = SeaBattle.FLEET.filter((_, idx) => !sunk.some(s => s.k === idx));
    return {k, remaining};
}

function isMyTurn() {
    const s = session;
    if (!s || s.status !== 'playing' || s.locked) return false;
    if (s.mode === 'online') return s.turn === s.mySlot && !s.moveInFlight;
    if (s.mode === 'ai') return s.turn === 1;
    return s.turn === s.viewer;
}

function nameOf(slot) {
    return session.names[slot] || t(slot === 1 ? 'player_one' : 'player_two');
}

// ==================== RENDER ====================
function renderAll() {
    const s = session;
    if (!s) return;
    const placing = s.status === 'placing' || (s.status === 'waiting' && s.mode !== 'online');
    $('placement').classList.toggle('hidden', !placing);
    $('battle').classList.toggle('hidden', placing);
    if (placing) renderPlacement();
    else renderBattle();
    renderScoreboard();
    renderTurnIndicator();
    renderActions();
}

function renderScoreboard() {
    const s = session;
    const me = s.viewer;
    const opp = other(me);
    [['me', me], ['opp', opp]].forEach(([side, slot]) => {
        $('name-' + side).textContent = nameOf(slot);
        $('wins-' + side).textContent = t('wins_count', {n: s.wins[slot] || 0});
        const player = s.mode === 'online' ? (s.players || []).find(p => p.slot === slot) : null;
        $('dot-' + side).classList.toggle('hidden', !player);
        $('dot-' + side).classList.toggle('offline', !!player && !player.online);
        $('cap-' + side).classList.toggle('active-turn', s.status === 'playing' && s.turn === slot);
    });
    renderClocks();
}

function renderClocks() {
    const s = session;
    const show = s.mode === 'online' && s.timeControl > 0;
    const fmt = (ms) => {
        const sec = Math.ceil(ms / 1000);
        return `${Math.floor(sec / 60)}:${String(sec % 60).padStart(2, '0')}`;
    };
    [['me', s.viewer], ['opp', other(s.viewer)]].forEach(([side, slot]) => {
        const el = $('clock-' + side);
        el.classList.toggle('hidden', !show);
        if (!show) return;
        el.textContent = fmt(s.ms[slot]);
        el.classList.toggle('low-time', s.ms[slot] < 30000);
        el.classList.toggle('running', s.status === 'playing' && s.turn === slot);
    });
}

function renderTurnIndicator() {
    const s = session;
    const ind = $('turn-indicator');
    let text = '';
    let cls = '';
    if (s.status === 'placing' || s.status === 'waiting') {
        if (s.mode === 'online' && s.myFleet) text = t('waiting_deploy', {name: nameOf(other(s.mySlot))});
        else text = t('place_title');
    } else if (s.status === 'playing') {
        if (s.locked) text = t('stand_by');
        else if (s.mode === 'local') {
            text = t('turn_of', {name: nameOf(s.turn)});
            cls = 'mine';
        } else if (isMyTurn() || (s.mode === 'online' && s.turn === s.mySlot)) {
            text = t('your_turn');
            cls = 'mine';
        } else text = s.mode === 'ai' ? t('computer_aiming') : t('their_turn', {name: nameOf(s.turn)});
    } else if (s.status === 'finished') {
        text = s.winner === 0 ? t('game_over') : s.mode === 'local' ? t('won_the_game', {name: nameOf(s.winner)}) : (s.winner === s.viewer ? t('you_won') : t('you_lost'));
        cls = 'over';
    }
    ind.textContent = text;
    ind.className = 'turn-indicator ' + cls;
}

function renderActions() {
    const s = session;
    const over = s.status === 'finished';
    const online = s.mode === 'online';
    const battle = s.status === 'playing';
    // While the fleets go down the header is enough: the bar would only push the Ready button out of sight
    $('game-actions').classList.toggle('hidden', !battle && !over);
    $('btn-hint').classList.toggle('hidden', online || over || !battle);
    $('btn-hint').disabled = !isMyTurn();
    $('btn-resign').classList.toggle('hidden', over);
    $('btn-result').classList.toggle('hidden', !over);
    $('btn-chat-toggle').classList.toggle('hidden', !online);
    $('btn-panel-toggle').classList.toggle('hidden', online || s.status === 'placing');
}

function renderBattle() {
    const s = session;
    const me = s.viewer;
    const opp = other(me);
    const over = s.status === 'finished';

    // Enemy waters: my shots
    const enemyMarks = new Map();
    const myShots = shotsBy(me);
    const oppSunk = sunkShips(opp);
    const disabled = new Set();
    myShots.forEach(sh => {
        enemyMarks.set(sh.i, sh.r === 'miss' ? 'c-miss' : 'c-hit');
        disabled.add(sh.i);
    });
    oppSunk.forEach(({k, ship}) => {
        const cells = SeaBattle.shipCells(ship, SeaBattle.FLEET[k]);
        cells.forEach(i => enemyMarks.set(i, 'c-sunk'));
        if (prefs.automark) SeaBattle.around(cells).forEach(i => {
            if (!enemyMarks.has(i)) {
                enemyMarks.set(i, 'c-cleared');
                disabled.add(i);
            }
        });
    });
    const enemyShips = oppSunk.map(({k, ship}) => ({k, ship, cls: 'sunk'}));
    // At the end the whole enemy fleet is shown
    if (over) {
        const fleet = s.fleets[opp] || null;
        const known = new Set(oppSunk.map(o => o.k));
        if (fleet) fleet.forEach((ship, k) => {
            if (!known.has(k)) enemyShips.push({k, ship, cls: 'revealed'});
        });
        else (s.oppShips || []).forEach(o => {
            if (!known.has(o.k)) enemyShips.push({k: o.k, ship: o, cls: 'revealed'});
        });
    }
    const lastMine = myShots.length ? myShots[myShots.length - 1].i : null;
    const canFire = isMyTurn();
    if (!canFire) for (let i = 0; i < N * N; i++) disabled.add(i);
    renderGrid($('grid-enemy'), {
        ships: enemyShips, marks: enemyMarks, last: lastMine,
        fresh: s.fresh && s.fresh.s === me ? s.fresh.i : null, disabled,
    });
    $('grid-enemy').classList.toggle('armed', canFire);

    // My waters: my fleet and the shots it took
    const ownMarks = new Map();
    const theirShots = shotsBy(opp);
    theirShots.forEach(sh => ownMarks.set(sh.i, sh.r === 'miss' ? 'c-miss' : 'c-hit'));
    const mySunk = sunkShips(me);
    const mySunkSet = new Set(mySunk.map(o => o.k));
    mySunk.forEach(({k, ship}) => SeaBattle.shipCells(ship, SeaBattle.FLEET[k]).forEach(i => ownMarks.set(i, 'c-sunk')));
    const myFleet = s.fleets[me] || [];
    renderGrid($('grid-own'), {
        ships: myFleet.map((ship, k) => ({k, ship, cls: mySunkSet.has(k) ? 'sunk' : ''})),
        marks: ownMarks,
        last: theirShots.length ? theirShots[theirShots.length - 1].i : null,
        fresh: s.fresh && s.fresh.s === opp ? s.fresh.i : null,
    });

    renderPips($('pips-enemy'), new Set(oppSunk.map(o => o.k)));
    renderPips($('pips-own'), mySunkSet);

    // The board where the action is gets the room
    const defending = s.status === 'playing' && s.turn !== me && s.mode !== 'local';
    $('battle').classList.toggle('defending', defending);
    $('title-own').textContent = s.mode === 'local' ? t('fleet_of', {name: nameOf(me)}) : t('your_fleet');
}

// ==================== PLACEMENT ====================
function newDraft(randomStart = true) {
    return {ships: randomStart ? SeaBattle.randomFleet() : SeaBattle.FLEET.map(() => null), selected: null, dir: 'h', hover: null};
}

function draftPreview(d) {
    if (d.selected === null || d.hover === null) return null;
    const len = SeaBattle.FLEET[d.selected];
    let r = Math.floor(d.hover / N), c = d.hover % N;
    if (d.dir === 'h') c = Math.min(c, N - len);
    else r = Math.min(r, N - len);
    const ship = {r, c, d: d.dir};
    return {ship, ok: SeaBattle.canPlace(d.ships, d.selected, ship)};
}

function placementLocked() {
    const s = session;
    return s.mode === 'online' && !!s.myFleet;
}

function renderPlacement() {
    const s = session;
    const locked = placementLocked();
    const d = s.draft;
    const ships = locked ? s.myFleet : d.ships;
    const marks = new Map();
    const preview = locked ? null : draftPreview(d);
    if (preview) SeaBattle.shipCells(preview.ship, SeaBattle.FLEET[d.selected]).forEach(i => marks.set(i, preview.ok ? 'c-preview' : 'c-preview-bad'));
    const view = ships.map((ship, k) => ship ? {k, ship, cls: (!locked && k === d.selected) ? 'lifted' : ''} : null).filter(Boolean);
    if (preview && preview.ok) view.push({k: d.selected, ship: preview.ship, cls: 'ghost'});
    renderGrid($('grid-place'), {ships: view, marks, last: null});
    $('grid-place').classList.toggle('locked', locked);

    $('dock').innerHTML = SeaBattle.FLEET.map((len, k) => {
        const placed = !!ships[k];
        const sel = !locked && d.selected === k;
        return `<li><button type="button" class="dock-ship ${placed ? 'placed' : ''} ${sel ? 'selected' : ''}" data-k="${k}" ${locked ? 'disabled' : ''}>
            <span class="dock-hull">${'<i></i>'.repeat(len)}</span>
            <span class="dock-name">${escapeHtml(shipName(k))}</span>
            <span class="dock-len">${len}</span>
        </button></li>`;
    }).join('');

    const complete = ships.every(Boolean);
    $('btn-ready').disabled = locked || !complete;
    $('btn-ready').classList.toggle('hidden', locked);
    ['btn-rotate', 'btn-random-fleet', 'btn-clear-fleet'].forEach(id => $(id).disabled = locked);
    $('place-hint').textContent = locked ? t('fleet_ready') : t('place_hint');
    $('place-waiting').textContent = locked ? t('waiting_deploy', {name: nameOf(other(s.mySlot))}) : '';
    $('placement').querySelector('.board-title').textContent = s.mode === 'local' ? t('fleet_of', {name: nameOf(s.viewer)}) : t('place_title');
}

function onPlaceCellHover(i) {
    const d = session && session.draft;
    if (!d || placementLocked() || d.hover === i) return;
    d.hover = i;
    renderPlacement();
}

function onPlaceCellClick(i) {
    const s = session;
    if (!s || placementLocked()) return;
    const d = s.draft;
    if (d.selected === null) {
        // Pick up the ship lying there
        const k = d.ships.findIndex((ship, k) => ship && SeaBattle.shipCells(ship, SeaBattle.FLEET[k]).includes(i));
        if (k === -1) {
            const free = d.ships.findIndex(ship => !ship);
            if (free === -1) return;
            d.selected = free;
        } else {
            d.selected = k;
            d.dir = d.ships[k].d;
            d.liftedFrom = d.ships[k];
            d.ships[k] = null;
            d.hover = i;
            renderPlacement();
            return;
        }
    }
    d.hover = i;
    const preview = draftPreview(d);
    if (!preview || !preview.ok) {
        showToast(t('err_cant_place'));
        renderPlacement();
        return;
    }
    d.ships[d.selected] = preview.ship;
    d.lastPlaced = d.selected;
    d.liftedFrom = null;
    const next = d.ships.findIndex(ship => !ship);
    d.selected = next === -1 ? null : next;
    playSound('place');
    renderPlacement();
}

function selectDockShip(k) {
    const d = session.draft;
    // A ship picked up and not put down again goes back where it was
    if (d.selected !== null && d.liftedFrom && !d.ships[d.selected]) d.ships[d.selected] = d.liftedFrom;
    d.liftedFrom = null;
    if (d.selected === k) {
        d.selected = null;
        renderPlacement();
        return;
    }
    if (d.ships[k]) {
        d.liftedFrom = d.ships[k];
        d.dir = d.ships[k].d;
        d.ships[k] = null;
    }
    d.selected = k;
    renderPlacement();
}

function rotateDraft() {
    const s = session;
    if (!s || placementLocked() || !s.draft) return;
    const d = s.draft;
    if (d.selected !== null) {
        d.dir = d.dir === 'h' ? 'v' : 'h';
        renderPlacement();
        return;
    }
    // Nothing in hand: turn the last ship placed, where it lies
    const k = d.lastPlaced ?? -1;
    if (k < 0 || !d.ships[k]) return;
    const turned = {...d.ships[k], d: d.ships[k].d === 'h' ? 'v' : 'h'};
    if (SeaBattle.canPlace(d.ships, k, turned)) {
        d.ships[k] = turned;
        renderPlacement();
    } else showToast(t('err_cant_place'));
}

async function confirmFleet() {
    const s = session;
    const fleet = s.draft.ships;
    if (!SeaBattle.isValidFleet(fleet)) return;
    if (s.mode === 'online') {
        setBusy($('btn-ready'), true);
        const res = await api('place', {token: s.token, fleet: SeaBattle.encodeFleet(fleet)});
        setBusy($('btn-ready'), false);
        if (session !== s) return;
        if (res.error) {
            showError(res.error);
            return;
        }
        s.myFleet = fleet;
        s.fleets[s.mySlot] = fleet;
        renderAll();
        pollServer();
        return;
    }
    s.fleets[s.viewer] = fleet;
    if (s.mode === 'ai') {
        s.fleets[2] = SeaBattleAI.placeFleet(s.aiLevel);
        startBattle();
        return;
    }
    // Same device: the second captain deploys, then the battle starts
    if (!s.fleets[other(s.viewer)]) {
        s.draft = newDraft();
        showCurtain(other(s.viewer), 'curtain_place');
    } else {
        startBattle();
        showCurtain(s.turn, 'curtain_turn');
    }
}

// ==================== LOCAL GAMES (same device, computer) ====================
function baseSession(mode, extra) {
    sessionSeq++;
    return Object.assign({
        mode,
        seq: sessionSeq,
        status: 'placing',
        names: {1: '', 2: ''},
        wins: {1: 0, 2: 0},
        viewer: 1,
        mySlot: 1,
        fleets: {1: null, 2: null},
        shots: [],
        turn: 1,
        firstSlot: 1,
        bonus: false,
        timeControl: 0,
        ms: {1: 0, 2: 0},
        draft: newDraft(),
        winner: 0,
        round: 1,
    }, extra);
}

function startLocalGame() {
    initAudio();
    const opponent = $('local-opponent').value.trim();
    session = baseSession('local', {
        names: {1: playerName || t('player_one'), 2: opponent || t('player_two')},
        bonus: $('bonus-local').checked,
    });
    enterGameScreen();
    showCurtain(1, 'curtain_place');
}

function startAIGame() {
    initAudio();
    const level = document.querySelector('input[name="ai-level"]:checked').value;
    const first = document.querySelector('input[name="ai-first"]:checked').value;
    const firstSlot = first === 'me' ? 1 : first === 'ai' ? 2 : (Math.random() < 0.5 ? 1 : 2);
    session = baseSession('ai', {
        names: {1: playerName || t('player_one'), 2: t('computer')},
        aiLevel: level,
        firstSlot,
        turn: firstSlot,
        bonus: $('bonus-ai').checked,
    });
    enterGameScreen();
}

function enterGameScreen() {
    $('log-list').innerHTML = '';
    $('chat-list').innerHTML = '';
    $('chat').classList.toggle('hidden', session.mode !== 'online');
    $('btn-chat-toggle').classList.remove('has-unread');
    $('mode-badge').textContent = t('tab_' + session.mode);
    closePanel();
    showModal('gameover-overlay', false);
    showScreen('game');
    renderBanner();
    renderAll();
}

function startBattle() {
    const s = session;
    s.status = 'playing';
    s.turn = s.firstSlot;
    s.viewer = s.mode === 'local' ? s.turn : s.viewer;
    addLog(t('log_start', {name: nameOf(s.turn)}), 'info');
    renderAll();
    if (s.mode === 'ai' && s.turn === 2) scheduleAI();
}

// Resolves a shot in a game played on this device
function fireLocal(slot, cell) {
    const s = session;
    const target = other(slot);
    const prior = shotsBy(slot).map(sh => sh.i);
    if (prior.includes(cell)) return null;
    const res = SeaBattle.resolve(s.fleets[target], [...prior, cell]);
    const r = res.results[res.results.length - 1];
    const shot = {s: slot, i: cell, r, k: r === 'sunk' ? res.map[cell] : null};
    s.shots.push(shot);
    announceShot(shot);
    if (res.allSunk) {
        endGame(slot, 'fleet_sunk');
        return shot;
    }
    if (r === 'miss' || !s.bonus) s.turn = target;
    return shot;
}

function onEnemyCellClick(i) {
    const s = session;
    if (!s || !isMyTurn()) return;
    if (shotsBy(s.viewer).some(sh => sh.i === i)) return;
    if (s.mode === 'online') {
        fireOnline(i);
        return;
    }
    const before = s.turn;
    fireLocal(s.viewer, i);
    if (s.status !== 'playing') return;
    if (s.mode === 'ai') {
        renderAll();
        if (s.turn === 2) scheduleAI();
        return;
    }
    // Same device: when the turn passes, the fleets are hidden before the other captain looks
    if (s.turn !== before) {
        s.locked = true;
        renderAll();
        const seq = s.seq;
        setTimeout(() => {
            if (!session || session.seq !== seq || session.status !== 'playing') return;
            session.locked = false;
            showCurtain(session.turn, 'curtain_turn');
        }, HANDOVER_MS);
    } else renderAll();
}

function scheduleAI() {
    const s = session;
    const seq = s.seq;
    const round = s.round;
    setTimeout(() => {
        if (!session || session.seq !== seq || session.round !== round || session.status !== 'playing' || session.turn !== 2) return;
        const {k, remaining} = knowledgeOf(2);
        const cell = SeaBattleAI.bestShot(k, remaining, s.aiLevel);
        fireLocal(2, cell);
        renderAll();
        if (session.status === 'playing' && session.turn === 2) scheduleAI();
    }, AI_DELAY_MS);
}

function triggerHint() {
    const s = session;
    if (!s || !isMyTurn() || s.mode === 'online') return;
    const {k, remaining} = knowledgeOf(s.viewer);
    const cell = SeaBattleAI.bestShot(k, remaining, 'hard');
    const btn = $('grid-enemy').querySelector(`.cell[data-i="${cell}"]`);
    if (!btn) return;
    btn.classList.add('c-hint');
    btn.focus({preventScroll: true});
    showToast(t('hint_cell', {cell: SeaBattle.label(cell)}));
    setTimeout(() => btn.classList.remove('c-hint'), 2600);
}

// --- Same device screen ---
function showCurtain(slot, kind) {
    const s = session;
    s.curtain = {slot, kind};
    $('curtain-title').textContent = t('curtain_pass', {name: nameOf(slot)});
    $('curtain-text').textContent = t(kind + '_text', {name: nameOf(other(slot))});
    $('btn-curtain').textContent = t('curtain_ready', {name: nameOf(slot)});
    $('curtain').classList.remove('hidden');
    $('btn-curtain').focus();
}

function hideCurtain() {
    const s = session;
    if (!s || !s.curtain) return;
    s.viewer = s.curtain.slot;
    s.curtain = null;
    $('curtain').classList.add('hidden');
    renderAll();
}

// --- Shot feedback ---
function announceShot(shot, quiet = false) {
    const s = session;
    s.fresh = shot;
    const seq = s.seq;
    setTimeout(() => {
        if (session && session.seq === seq && session.fresh === shot) {
            session.fresh = null;
        }
    }, 900);
    const who = nameOf(shot.s);
    const cell = SeaBattle.label(shot.i);
    let text;
    if (shot.r === 'miss') text = t('log_miss', {name: who, cell});
    else if (shot.r === 'hit') text = t('log_hit', {name: who, cell});
    else text = t('log_sunk', {name: who, cell, ship: shipName(shot.k)});
    addLog(text, shot.r, shot.s === s.viewer || (s.mode === 'online' && shot.s === s.mySlot));
    if (quiet) return;
    playSound(shot.r);
    if (shot.r === 'sunk') {
        const mine = s.mode === 'local' ? shot.s === s.viewer : shot.s === s.mySlot;
        showToast(mine ? t('toast_sunk_theirs', {ship: shipName(shot.k)}) : t('toast_sunk_mine', {ship: shipName(shot.k)}));
    }
}

function addLog(text, kind, mine = false) {
    const list = $('log-list');
    const li = document.createElement('li');
    li.className = `log-item ${kind} ${mine ? 'mine' : ''}`;
    li.textContent = text;
    list.appendChild(li);
    list.scrollTop = list.scrollHeight;
}

// ==================== ONLINE ====================
async function createOnlineGame() {
    initAudio();
    const btn = $('btn-create');
    setBusy(btn, true);
    const tc = parseInt(document.querySelector('input[name="time-online"]:checked').value, 10) * 60;
    const res = await api('create_room', {name: playerName || t('player_one'), time: tc, bonus: $('bonus-online').checked ? 1 : 0});
    setBusy(btn, false);
    if (res.error) {
        showError(res.error);
        return;
    }
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
    if (res.error) {
        showError(res.error);
        return;
    }
    setupOnlineSession(res.code, res.token, 2, false);
}

async function findRandomGame() {
    initAudio();
    const btn = $('btn-random');
    setBusy(btn, true);
    const res = await api('find_match', {name: playerName || t('player_one'), lang: currentLang});
    setBusy(btn, false);
    if (res.error) {
        showError(res.error);
        return;
    }
    setupOnlineSession('', res.token, res.matched ? 2 : 1, true);
}

function setupOnlineSession(code, token, slot, isPublic) {
    session = baseSession('online', {
        status: 'waiting',
        code,
        token,
        mySlot: slot,
        viewer: slot,
        isPublic,
        names: {1: slot === 1 ? playerName : '', 2: slot === 2 ? playerName : ''},
        lastMessageId: 0,
        players: [],
        myFleet: null,
        oppShips: [],
        rematchSent: false,
        round: 0,
    });
    sessionSet('sb_session', JSON.stringify({code, token, slot, isPublic}));
    $('chat-list').innerHTML = '';
    $('log-list').innerHTML = '';

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
    startPolling();
}

function startPolling() {
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
    s.mySlot = data.mySlot; // two players searching at once can be moved to the other's room
    s.viewer = data.mySlot;
    s.firstSlot = data.firstSlot;
    s.timeControl = data.timeControl;
    s.bonus = data.bonus;
    s.ms = {1: data.p1Ms, 2: data.p2Ms};
    s.clockSyncedAt = Date.now();
    s.players = data.players;
    s.oppShips = data.oppShips || [];
    s.placeLeftMs = data.placeLeftMs;
    s.placeSyncedAt = Date.now();
    data.players.forEach(p => {
        s.names[p.slot] = p.name;
        s.wins[p.slot] = p.wins;
    });

    // A new round (rematch accepted): new fleets
    if (s.round && data.round !== s.round) {
        s.shots = [];
        s.myFleet = null;
        s.fleets = {1: null, 2: null};
        s.draft = newDraft();
        s.rematchSent = false;
        s.winner = 0;
        s.status = 'placing';
        $('log-list').innerHTML = '';
        showModal('gameover-overlay', false);
    }
    s.round = data.round;

    s.myFleet = data.myFleet;
    s.fleets[s.mySlot] = data.myFleet || null;
    // Once the game is over the whole enemy fleet arrives
    if (data.status === 'finished' && s.oppShips.length === SeaBattle.FLEET.length) {
        const fleet = [];
        s.oppShips.forEach(o => fleet[o.k] = {r: o.r, c: o.c, d: o.d});
        s.fleets[other(s.mySlot)] = fleet;
    }

    const wasWaiting = s.status === 'waiting';
    if (wasWaiting && data.status !== 'waiting') {
        s.status = data.status === 'finished' ? 'playing' : data.status;
        enterGameScreen();
    }
    if (s.status === 'placing' && data.status !== 'placing' && data.status !== 'waiting') {
        s.status = 'playing';
        addLog(t('log_start', {name: nameOf(data.firstSlot)}), 'info');
    }

    // New shots: a reload brings many at once, shown without sound
    const fresh = data.shots.length - s.shots.length;
    if (fresh > 0) {
        const added = data.shots.slice(s.shots.length);
        s.shots = data.shots;
        added.forEach((shot, n) => announceShot(shot, fresh > 2 || n < added.length - 1));
    } else if (fresh < 0) s.shots = data.shots;
    s.turn = data.turn;

    if (data.status === 'finished' && s.status !== 'finished') {
        const winner = data.result === '1-0' ? 1 : data.result === '0-1' ? 2 : 0;
        endGame(winner, data.reason);
    }

    if (s.status !== 'waiting') {
        renderAll();
        renderBanner();
        renderRematchStatus();
        startClock();
    }
    addChatMessages(data.messages || []);
}

async function fireOnline(cell) {
    const s = session;
    s.moveInFlight = true; // a double tap must not send the same shot twice
    renderBattle();
    const res = await api('fire', {token: s.token, cell, n: s.shots.length});
    s.moveInFlight = false;
    if (session !== s) return;
    if (res.error) showError(res.error);
    pollServer();
}

// The opponent lost the connection: a countdown to the automatic win (the server decides at zero)
let offlineTimer = null;

function renderBanner() {
    const s = session;
    const active = s && s.mode === 'online' && (s.status === 'playing' || s.status === 'placing');
    const opp = active ? s.players.find(p => p.slot !== s.mySlot) : null;
    if (!opp || opp.online) {
        if (s) s.offlineDeadline = null;
        clearInterval(offlineTimer);
        offlineTimer = null;
        showModal('offline-overlay', false);
        return;
    }
    // Polls come every 1.5 s: follow the server only when the local countdown drifts away from it
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
    $('offline-ring').style.strokeDashoffset = String(Math.round(1000 * (1 - left / (AUTO_WIN_S * 1000))) / 1000 * 283);
    $('offline-text').textContent = left > 0 ? t('offline_text') : t('offline_now');
}

// Online clocks tick here between polls; placement shows its own countdown
function startClock() {
    clearInterval(clockInterval);
    const s = session;
    if (!s || s.mode !== 'online') return;
    clockInterval = setInterval(() => {
        if (session !== s) {
            clearInterval(clockInterval);
            return;
        }
        if (s.status === 'playing' && s.timeControl > 0) {
            const now = Date.now();
            s.ms[s.turn] = Math.max(0, s.ms[s.turn] - (now - s.clockSyncedAt));
            s.clockSyncedAt = now;
            renderClocks();
        }
        const timer = $('place-timer');
        if (s.status === 'placing' && !s.myFleet && s.placeLeftMs) {
            const left = Math.max(0, s.placeLeftMs - (Date.now() - s.placeSyncedAt));
            timer.textContent = t('place_time_left', {sec: Math.ceil(left / 1000)});
        } else timer.textContent = '';
    }, 250);
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

// ==================== END OF GAME ====================
const GAMEOVER_ICONS = {
    win: `<svg viewBox="0 0 60 60" width="56" height="56" fill="none" stroke="var(--good)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="30" cy="30" r="27"/><path d="M18 30l8 8 16-16"/></svg>`,
    lose: `<svg viewBox="0 0 60 60" width="56" height="56" fill="none" stroke="var(--hit)" stroke-width="3" stroke-linecap="round"><circle cx="30" cy="30" r="27"/><path d="M21 21l18 18M39 21L21 39"/></svg>`,
    trophy: `<svg viewBox="0 0 60 60" width="56" height="56" fill="none" stroke="var(--gold)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10h20v14a10 10 0 01-20 0z"/><path d="M20 14h-7a6 6 0 006 8M40 14h7a6 6 0 01-6 8M30 34v8M22 50h16M24 42h12v8H24z"/></svg>`,
};

function statsFor(slot) {
    const shots = shotsBy(slot);
    const hits = shots.filter(s => s.r !== 'miss').length;
    return {shots: shots.length, hits, acc: shots.length ? Math.round(hits * 100 / shots.length) : 0};
}

function endGame(winner, reason) {
    const s = session;
    if (!s || s.status === 'finished') return;
    s.status = 'finished';
    s.winner = winner;
    s.locked = false;
    clearInterval(clockInterval);
    if (s.mode !== 'online') s.wins[winner] = (s.wins[winner] || 0) + 1;
    // Same device: the winner's view, with the beaten fleet in full
    if (s.mode === 'local') {
        s.viewer = winner;
        $('curtain').classList.add('hidden');
        s.curtain = null;
    }

    const me = s.mode === 'online' ? s.mySlot : s.viewer;
    const won = winner === me;
    if (s.mode === 'local') {
        $('gameover-title').textContent = t('won_the_game', {name: nameOf(winner)});
        $('gameover-icon').innerHTML = GAMEOVER_ICONS.trophy;
        playSound('win');
    } else {
        $('gameover-title').textContent = won ? t('you_won') : t('you_lost');
        $('gameover-icon').innerHTML = won ? GAMEOVER_ICONS.win : GAMEOVER_ICONS.lose;
        playSound(won ? 'win' : 'lose');
    }
    $('gameover-text').textContent = t('reason_' + reason, {name: nameOf(other(winner))});
    $('gameover-stats').innerHTML = [1, 2].map(slot => {
        const st = statsFor(slot);
        return `<div class="stat"><dt>${escapeHtml(nameOf(slot))}</dt><dd>${escapeHtml(t('stats_line', {shots: st.shots, hits: st.hits, acc: st.acc}))}</dd></div>`;
    }).join('');
    $('score-line').textContent = t('score_tracker', {p1: nameOf(1), s1: s.wins[1] || 0, p2: nameOf(2), s2: s.wins[2] || 0});
    $('btn-rematch').disabled = false;
    $('rematch-status').textContent = '';
    addLog($('gameover-title').textContent, 'info');

    renderAll();
    renderBanner();
    const seq = s.seq;
    setTimeout(() => {
        if (session && session.seq === seq && session.status === 'finished') showModal('gameover-overlay', true);
    }, 900);
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
    // The other captain fires first this time
    s.round++;
    s.firstSlot = other(s.firstSlot);
    s.turn = s.firstSlot;
    s.shots = [];
    s.fleets = {1: null, 2: null};
    s.draft = newDraft();
    s.status = 'placing';
    s.winner = 0;
    s.viewer = 1;
    enterGameScreen();
    if (s.mode === 'local') showCurtain(1, 'curtain_place');
}

async function leaveGame() {
    const s = session;
    resetToLobby();
    if (s && s.mode === 'online' && s.token) await api('leave', {token: s.token});
}

function resetToLobby() {
    clearInterval(pollTimer);
    clearInterval(clockInterval);
    session = null;
    sessionSet('sb_session', null);
    document.querySelectorAll('.modal').forEach(m => m.classList.add('hidden'));
    $('curtain').classList.add('hidden');
    closePanel();
    renderBanner();
    showScreen('lobby');
}

function requestLeave() {
    if (!session) return;
    const started = session.status === 'playing' || (session.mode === 'online' && session.status === 'placing');
    if (started) openConfirm(t('confirm_leave_title'), t('confirm_leave_text'), t('leave'), leaveGame);
    else leaveGame();
}

function requestResign() {
    const s = session;
    if (!s || s.status === 'finished' || s.status === 'waiting') return;
    openConfirm(t('confirm_resign_title'), t('confirm_resign_text'), t('resign'), async () => {
        if (!session || session.status === 'finished') return;
        if (s.mode === 'online') {
            const res = await api('resign', {token: s.token});
            if (res.error) showError(res.error);
            pollServer();
        } else {
            // On one device the captain looking at the screen gives up; against the computer it is always you
            const loser = s.mode === 'ai' ? 1 : s.viewer;
            endGame(other(loser), 'resign');
        }
    });
}

// --- Side panel (phones) ---
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

// Android back key: true means the page used it
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

// --- Chat ---
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

// ==================== MODALS ====================
function renderRulesModal() {
    const r = RULES_TEXT[currentLang] || RULES_TEXT.en;
    const list = (items) => `<ul>${items.map(i => `<li>${i}</li>`).join('')}</ul>`;
    const fleet = SeaBattle.FLEET.map((len, k) => `<li><span class="rule-ship">${'<i></i>'.repeat(len)}</span>${escapeHtml(shipName(k))} (${len})</li>`).join('');
    $('rules-body').innerHTML = `
        <section><h4>${r.goal_t}</h4><p>${r.goal}</p></section>
        <section><h4>${r.fleet_t}</h4><p>${r.fleet}</p><ul class="rule-fleet">${fleet}</ul></section>
        <section><h4>${r.battle_t}</h4><p>${r.battle}</p></section>
        <section><h4>${r.bonus_t}</h4><p>${r.bonus}</p></section>
        <section><h4>${r.online_t}</h4>${list(r.online)}</section>
        <section><h4>${r.tips_t}</h4>${list(r.tips)}</section>`;
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

function renderSettingsModal() {
    $('hull-options').innerHTML = Object.entries(HULLS).map(([key, [c1, c2]]) => `
        <button type="button" role="radio" class="hull-option ${prefs.hull === key ? 'active' : ''}" data-hull="${key}" aria-checked="${prefs.hull === key}">
            <span class="hull-swatch" style="background:linear-gradient(180deg, ${c1}, ${c2})"></span>
            <span>${escapeHtml(t('hull_' + key))}</span>
        </button>`).join('');
    $('opt-sound').checked = prefs.sound;
    $('opt-automark').checked = prefs.automark;
    $('opt-coords').checked = prefs.coords;
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
    if (session) {
        $('mode-badge').textContent = t('tab_' + session.mode);
        if (session.mode === 'ai') session.names[2] = t('computer');
        renderAll();
        renderBanner();
        if (session.curtain) showCurtain(session.curtain.slot, session.curtain.kind);
    }
    if (!$('rules-overlay').classList.contains('hidden')) renderRulesModal();
    if (!$('settings-overlay').classList.contains('hidden')) renderSettingsModal();
};

// Arrow keys move between the cells of a grid
function onGridKey(e) {
    const cell = e.target.closest('.cell');
    if (!cell) return;
    const moves = {ArrowLeft: -1, ArrowRight: 1, ArrowUp: -N, ArrowDown: N};
    if (!(e.key in moves)) return;
    e.preventDefault();
    const i = Number(cell.dataset.i);
    const next = i + moves[e.key];
    if (next < 0 || next >= N * N || (Math.abs(moves[e.key]) === 1 && Math.floor(next / N) !== Math.floor(i / N))) return;
    const el = e.currentTarget.querySelector(`.cell[data-i="${next}"]`);
    if (el) el.focus();
    if (e.currentTarget.id === 'grid-place') onPlaceCellHover(next);
}

// ==================== BOOT ====================
document.addEventListener('DOMContentLoaded', () => {
    setLanguage(getInitialLang());

    try {
        Object.assign(prefs, JSON.parse(storageGet('sb_prefs') || '{}'));
    } catch (e) {
    }

    buildGrid($('grid-place'), true);
    buildGrid($('grid-enemy'), true);
    buildGrid($('grid-own'), false);
    applyPrefs();

    playerName = storageGet('sb_name') || '';
    $('player-name').value = playerName;
    $('btn-continue').disabled = !playerName;
    setLobbyStep(playerName ? 2 : 1);

    $('player-name').addEventListener('input', (e) => $('btn-continue').disabled = e.target.value.trim() === '');
    $('step-name').addEventListener('submit', (e) => {
        e.preventDefault();
        const val = $('player-name').value.trim();
        if (!val) return;
        playerName = val;
        storageSet('sb_name', playerName);
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

    $('btn-start-local').addEventListener('click', startLocalGame);
    $('btn-start-ai').addEventListener('click', startAIGame);
    $('btn-create').addEventListener('click', createOnlineGame);
    $('btn-random').addEventListener('click', findRandomGame);
    $('room-code').addEventListener('input', (e) => {
        const input = e.target;
        input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        $('btn-join').disabled = input.value.length !== 6;
    });
    $('card-join').addEventListener('submit', (e) => {
        e.preventDefault();
        joinOnlineGame();
    });

    // Placement
    const placeGrid = $('grid-place');
    placeGrid.addEventListener('click', (e) => {
        const cell = e.target.closest('.cell');
        if (cell) onPlaceCellClick(Number(cell.dataset.i));
    });
    placeGrid.addEventListener('pointerover', (e) => {
        const cell = e.target.closest('.cell');
        if (cell && e.pointerType === 'mouse') onPlaceCellHover(Number(cell.dataset.i));
    });
    placeGrid.addEventListener('focusin', (e) => {
        const cell = e.target.closest('.cell');
        if (cell && cell.matches(':focus-visible')) onPlaceCellHover(Number(cell.dataset.i));
    });
    placeGrid.addEventListener('pointerleave', () => {
        if (session && session.draft) {
            session.draft.hover = null;
            renderPlacement();
        }
    });
    placeGrid.addEventListener('keydown', onGridKey);
    $('dock').addEventListener('click', (e) => {
        const btn = e.target.closest('.dock-ship');
        if (btn && session && !placementLocked()) selectDockShip(Number(btn.dataset.k));
    });
    $('btn-rotate').addEventListener('click', rotateDraft);
    $('btn-random-fleet').addEventListener('click', () => {
        if (!session || placementLocked()) return;
        session.draft = newDraft(true);
        playSound('place');
        renderPlacement();
    });
    $('btn-clear-fleet').addEventListener('click', () => {
        if (!session || placementLocked()) return;
        session.draft = newDraft(false);
        session.draft.selected = 0;
        renderPlacement();
    });
    $('btn-ready').addEventListener('click', confirmFleet);
    document.addEventListener('keydown', (e) => {
        if ((e.key === 'r' || e.key === 'R') && session && session.status === 'placing' && !e.target.closest('input, textarea')) rotateDraft();
    });

    // Battle
    $('grid-enemy').addEventListener('click', (e) => {
        const cell = e.target.closest('.cell');
        if (cell && !cell.disabled) onEnemyCellClick(Number(cell.dataset.i));
    });
    $('grid-enemy').addEventListener('keydown', onGridKey);
    $('btn-hint').addEventListener('click', triggerHint);
    $('btn-resign').addEventListener('click', requestResign);
    $('btn-result').addEventListener('click', () => showModal('gameover-overlay', true));
    $('btn-leave-game').addEventListener('click', requestLeave);
    document.querySelector('.game-title').addEventListener('click', (e) => {
        e.preventDefault();
        requestLeave();
    });
    $('btn-leave-waiting').addEventListener('click', leaveGame);
    $('btn-curtain').addEventListener('click', hideCurtain);

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
        renderSettingsModal();
        showModal('settings-overlay', true);
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

    $('hull-options').addEventListener('click', (e) => {
        const btn = e.target.closest('[data-hull]');
        if (!btn) return;
        prefs.hull = btn.dataset.hull;
        savePrefs();
        renderSettingsModal();
    });
    [['opt-sound', 'sound'], ['opt-automark', 'automark'], ['opt-coords', 'coords']].forEach(([id, key]) => {
        $(id).addEventListener('change', () => {
            prefs[key] = $(id).checked;
            savePrefs();
        });
    });

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
        if (modal.id === 'gameover-overlay' || modal.id === 'offline-overlay') return;
        modal.addEventListener('click', (e) => {
            if (e.target === modal) showModal(modal.id, false);
        });
    });

    $('btn-rematch').addEventListener('click', triggerRematch);
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

    // Invite links: ?join=ABC123
    const joinCode = new URLSearchParams(location.search).get('join');
    if (joinCode) {
        const clean = joinCode.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
        $('room-code').value = clean;
        document.querySelector('.mode-tab[data-tab="online"]').click();
        $('btn-join').disabled = clean.length !== 6;
        $('card-join').classList.add('highlight');
    }

    // Shortcut of the installed app (manifest.webmanifest): ?play=ai opens the game against the computer
    if (new URLSearchParams(location.search).get('play') === 'ai') {
        document.querySelector('.mode-tab[data-tab="ai"]').click();
        if (playerName) startAIGame();
    }

    // A reload does not lose an online game
    const saved = sessionGet('sb_session');
    if (saved) {
        try {
            const {code, token, slot, isPublic} = JSON.parse(saved);
            setupOnlineSession(code, token, slot, isPublic);
        } catch (e) {
            sessionSet('sb_session', null);
        }
    }

    $('app-version').textContent = 'v' + APP_VERSION;
});

if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => {
}));
