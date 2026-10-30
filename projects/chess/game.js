const API_URL = 'api/index.php';
const POLL_MS = 1500;
const CLAIM_AFTER_S = 60;
const MATERIAL = { p: 1, n: 3, b: 3, r: 5, q: 9, k: 0 };
const START_COUNT = { p: 8, n: 2, b: 2, r: 2, q: 1 };

const $ = (id) => document.getElementById(id);

let playerName = '';
let session = null;     // the game on screen (null in the lobby)
let sessionSeq = 0;     // bumps on every new session so late callbacks can tell they're stale
let pollTimer = null;
let tipsOn = true;

function storageGet(key) {
    try { return localStorage.getItem(key); } catch (e) { return null; }
}
function storageSet(key, value) {
    try { localStorage.setItem(key, value); } catch (e) { /* storage unavailable */ }
}
function sessionGet(key) {
    try { return sessionStorage.getItem(key); } catch (e) { return null; }
}
function sessionSet(key, value) {
    try { value === null ? sessionStorage.removeItem(key) : sessionStorage.setItem(key, value); } catch (e) { /* storage unavailable */ }
}

// --- UI helpers ---
function showScreen(id) {
    document.querySelectorAll('.screen').forEach(s => s.classList.toggle('active', s.id === 'screen-' + id));
}

function showModal(id, visible) {
    $(id).classList.toggle('hidden', !visible);
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
    showToast(t('err_' + code) !== 'err_' + code ? t('err_' + code) : t('err_generic'));
}

// Moves are shown in figurine notation: a small (always light) piece instead of the English letter, files in capitals.
const FIGURINES = { K: '♔', Q: '♕', R: '♖', B: '♗', N: '♘' };
const upperFiles = (str) => str.replace(/[a-h]/g, c => c.toUpperCase());

function sanHtml(san) {
    if (san.startsWith('O-O')) return escapeHtml(san);
    const icon = (letter) => `<span class="san-pc">${PIECE_SVG.html(letter)}</span>`;
    const m = san.match(/^([KQRBN]?)([^=]*)(?:=([QRBN]))?(.*)$/);
    return (m[1] ? icon(m[1]) : '') + escapeHtml(upperFiles(m[2])) + (m[3] ? '=' + icon(m[3]) : '') + escapeHtml(m[4]);
}

// Plain-text version for toasts.
function sanText(san) {
    if (san.startsWith('O-O')) return san;
    return upperFiles(san.replace(/^[KQRBN]/, p => FIGURINES[p])).replace(/=([QRBN])/, (_, p) => '=' + FIGURINES[p]);
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function setBusy(button, busy) {
    button.disabled = busy;
    button.classList.toggle('loading', busy);
}

let confirmAction = null;
function openConfirm(title, text, yesLabel, onYes) {
    $('confirm-title').textContent = title;
    $('confirm-text').textContent = text;
    $('btn-confirm-yes').textContent = yesLabel;
    confirmAction = onYes;
    showModal('confirm-overlay', true);
}

const other = (c) => (c === 'w' ? 'b' : 'w');
const pieceName = (p) => t('piece_' + p.toLowerCase());
const fmtPoints = (p2) => (p2 >= 2 || p2 === 0 ? String(Math.floor(p2 / 2)) : '') + (p2 % 2 ? '½' : '');

// --- API ---
async function api(action, data = {}) {
    try {
        if (action === 'poll') {
            const resp = await fetch(`${API_URL}?${new URLSearchParams({ action, ...data })}`, { cache: 'no-store' });
            return await resp.json();
        }
        const form = new FormData();
        form.append('action', action);
        for (const [k, v] of Object.entries(data)) form.append(k, v);
        const resp = await fetch(API_URL, { method: 'POST', body: form });
        return await resp.json();
    } catch (e) {
        return { error: 'network' };
    }
}

// --- Computer (runs in a worker so the board stays responsive) ---
let worker = null;
let workerSeq = 0;
const workerWaiters = new Map();
function askEngine(state, level) {
    if (worker === null) {
        try {
            worker = new Worker('ai-worker.js?v=1');
            worker.onmessage = (e) => {
                const done = workerWaiters.get(e.data.id);
                workerWaiters.delete(e.data.id);
                if (done) done(e.data.uci);
            };
            worker.onerror = () => { worker = false; };
        } catch (e) {
            worker = false;
        }
    }
    if (!worker) return new Promise(resolve => setTimeout(() => resolve(ChessAI.bestMove(state, level)), 30));
    const id = ++workerSeq;
    return new Promise(resolve => {
        workerWaiters.set(id, resolve);
        worker.postMessage({ id, state, level });
    });
}

// --- Session ---
// players: [{ name, color, points2, online, left, idle, me }]
function newSession(mode, opts) {
    sessionSeq++;
    return {
        seq: sessionSeq,
        mode,
        chess: new Chess.Game(),
        players: opts.players,
        myColor: opts.myColor || null,
        level: opts.level || null,
        timeMs: opts.timeMs || 0,
        clocks: { w: opts.timeMs || 0, b: opts.timeMs || 0 },
        turnStartedAt: 0,
        viewColor: opts.myColor || 'w',
        manualFlip: false,
        selected: null,
        hint: null,
        aiThinking: false,
        over: null,
        // Online only
        token: opts.token || null,
        code: null,
        isPublic: false,
        round: null,
        pending: false,
        server: null,
        serverAt: 0,
        drawOffer: null,
        rematchSent: false,
        chat: { lastId: 0, messages: [], unread: 0 },
    };
}

const playerOf = (color) => session.players.find(p => p.color === color);
const isOnline = () => session && session.mode === 'online';
const autoFlipOn = () => storageGet('chess_autoflip') !== '0';

function bottomColor() {
    const base = session.mode === 'local' && autoFlipOn() ? session.viewColor : (session.myColor || 'w');
    return session.manualFlip ? other(base) : base;
}

// Whether the person at the screen can move the side to move right now.
function canMoveNow() {
    if (!session || session.over || session.pending || session.aiThinking) return false;
    if (session.mode === 'local') return true;
    if (isOnline() && (!session.server || session.server.status !== 'playing')) return false;
    return session.chess.turn === session.myColor;
}

function actionable(sq) {
    const p = session.chess.state.board[sq];
    return !!p && Chess.colorOf(p) === session.chess.turn && canMoveNow();
}

// --- Lobby ---
function goToStep(step) {
    $('step-name').classList.toggle('active', step === 1);
    $('step-mode').classList.toggle('active', step === 2);
    $('screen-lobby').classList.toggle('compact', step === 2);
    $('dot-1').classList.toggle('active', step === 1);
    $('dot-1').classList.toggle('done', step === 2);
    $('dot-2').classList.toggle('active', step === 2);
    $('btn-step-1').disabled = step !== 2;
    $('btn-step-1').title = step === 2 ? t('change_name') : '';
    if (step === 1) $('player-name').focus();
    else $('greeting-text').textContent = t('greeting', { name: playerName });
}

function selectTab(tab) {
    document.querySelectorAll('.mode-tab').forEach(b => {
        b.classList.toggle('active', b.dataset.tab === tab);
        b.setAttribute('aria-selected', String(b.dataset.tab === tab));
    });
    document.querySelectorAll('.mode-panel').forEach(p => p.classList.toggle('active', p.dataset.panel === tab));
}

const radioValue = (name) => (document.querySelector(`input[name="${name}"]:checked`) || {}).value;

function normalizeCode(value) {
    return value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
}

function inviteLink() {
    return `${location.origin}${location.pathname}?code=${session.code}`;
}

async function copyText(text, okMessage) {
    try {
        await navigator.clipboard.writeText(text);
        showToast(okMessage);
    } catch (e) {
        showToast(text);
    }
}

async function shareInvite() {
    try {
        await navigator.share({ title: 'Chess', text: t('share_text', { code: session.code }), url: inviteLink() });
    } catch (e) {
        if (e && e.name !== 'AbortError') copyText(inviteLink(), t('link_copied'));
    }
}

function startOnline(token, extra = {}) {
    session = newSession('online', { token, players: [] });
    Object.assign(session, extra);
    sessionSet('chess_token', token);
    startPolling();
}

async function createRoom() {
    const btn = $('btn-create');
    setBusy(btn, true);
    const result = await api('create_room', { name: playerName, time: radioValue('time-online') * 60 });
    setBusy(btn, false);
    if (result.error) return showError(result.error);
    startOnline(result.token, { code: result.code });
    showWaiting();
}

async function joinRoom() {
    const code = normalizeCode($('room-code').value);
    if (code.length !== 6) return showError('code_required');
    const btn = $('btn-join');
    setBusy(btn, true);
    const result = await api('join_room', { name: playerName, code });
    setBusy(btn, false);
    if (result.error) return showError(result.error);
    startOnline(result.token, { code: result.code });
}

async function findRandomMatch() {
    const btn = $('btn-random');
    setBusy(btn, true);
    const result = await api('find_match', { name: playerName, lang: currentLang });
    setBusy(btn, false);
    if (result.error) return showError(result.error);
    startOnline(result.token, { isPublic: true });
    if (!result.matched) showWaiting();
}

function showWaiting() {
    const pub = session.isPublic;
    $('waiting-title').textContent = pub ? t('searching_title') : t('waiting_title');
    $('waiting-public-desc').textContent = pub ? t('searching_desc', { language: t('lang_' + currentLang) }) : '';
    $('waiting-public-desc').classList.toggle('hidden', !pub);
    $('waiting-private').classList.toggle('hidden', pub);
    $('btn-share-link').classList.toggle('hidden', pub);
    $('btn-native-share').classList.toggle('hidden', pub || typeof navigator.share !== 'function');
    $('btn-leave-waiting').textContent = pub ? t('cancel') : t('leave');
    $('display-code').textContent = session.code || '';
    const tc = session.server ? session.server.timeControl : radioValue('time-online') * 60;
    $('waiting-time-note').textContent = tc ? t('time_note', { min: tc / 60 }) : t('time_none');
    showScreen('waiting');
}

function startLocal() {
    const opponent = $('local-opponent').value.trim().slice(0, 30) || t('player_two');
    storageSet('chess_opponent', opponent);
    session = newSession('local', {
        players: [{ name: playerName, color: 'w', points2: 0, me: true }, { name: opponent, color: 'b', points2: 0 }],
        timeMs: radioValue('time-local') * 60000,
    });
    enterGame();
}

function startAi() {
    let color = radioValue('ai-color');
    const level = radioValue('ai-level');
    session = newSession('ai', {
        players: [{ name: playerName, color: 'w', points2: 0, me: true }, { name: t('computer'), color: 'b', points2: 0, computer: true }],
        myColor: 'w',
        level,
        timeMs: radioValue('time-ai') * 60000,
    });
    session.colorChoice = color;
    assignAiColors();
    enterGame();
}

function assignAiColors() {
    const choice = session.colorChoice;
    const mine = choice === 'r' ? (Math.random() < 0.5 ? 'w' : 'b') : choice;
    session.myColor = mine;
    session.players[0].color = mine;
    session.players[1].color = other(mine);
}

function enterGame() {
    showModal('gameover-overlay', false);
    // Local clocks start with the game; online ones come from the server.
    if (!isOnline()) session.turnStartedAt = performance.now();
    $('board-arrows').innerHTML = '';
    showScreen('game');
    renderAll();
    fitBoard();
    requestAnimationFrame(fitBoard);
    maybeComputerMove();
}

// --- Online polling ---
function startPolling() {
    stopPolling();
    poll();
    pollTimer = setInterval(poll, POLL_MS);
}

function stopPolling() {
    clearInterval(pollTimer);
    pollTimer = null;
}

async function poll() {
    if (!isOnline()) return;
    const s = session;
    const data = await api('poll', { token: s.token, since: s.chat.lastId });
    // The player may have left (or started another game) while this request was in flight.
    if (session !== s) return;
    if (data.error) {
        if (data.error === 'invalid_session') {
            showError('invalid_session');
            backToLobby();
        }
        return;
    }
    syncOnline(data);
}

function syncOnline(data) {
    const s = session;
    const firstSync = !s.server;
    s.server = data;
    // Messages already there when the game opens (e.g. after a reload) arrive quietly.
    if (data.messages && data.messages.length) addChatMessages(data.messages, firstSync);
    if (firstSync) renderChat(true);
    s.serverAt = performance.now();
    s.code = data.code;
    s.isPublic = data.isPublic;
    s.myColor = data.myColor;
    s.drawOffer = data.drawOffer;
    s.players = data.players.map(p => ({ ...p, me: p.slot === data.mySlot }));

    if (data.status === 'waiting') {
        if (!$('screen-waiting').classList.contains('active')) showWaiting();
        return;
    }

    // A new round (rematch) starts from an empty board with swapped colours.
    if (s.round !== null && data.round !== s.round) {
        s.chess = new Chess.Game();
        s.over = null;
        s.rematchSent = false;
        s.selected = null;
        s.hint = null;
        showModal('gameover-overlay', false);
        showToast(t('new_round', { color: t(data.myColor === 'w' ? 'white' : 'black') }));
    }
    s.round = data.round;
    s.viewColor = data.myColor;

    let animate = null;
    if (!s.pending) {
        const local = s.chess.history.map(h => h.uci);
        const same = data.moves.length >= local.length && local.every((u, i) => u === data.moves[i]);
        if (same && data.moves.length === local.length + 1) {
            const entry = s.chess.move(data.moves[data.moves.length - 1]);
            if (entry) animate = entry;
            s.selected = null;
        } else if (!same || data.moves.length !== local.length) {
            s.chess = Chess.Game.fromMoves(data.moves);
            s.selected = null;
        }
    }

    if (firstSync || $('screen-waiting').classList.contains('active')) {
        showScreen('game');
        fitBoard();
        requestAnimationFrame(fitBoard);
    }

    if (data.status === 'finished' && !s.over && data.result) {
        s.over = { result: data.result, reason: data.reason };
        renderAll(animate);
        showGameOver();
        return;
    }
    if (data.status === 'playing' && s.over) s.over = null;
    renderAll(animate);
    if (s.over) updateRematchUI();
}

// --- Moving ---
function targetsFrom(sq) {
    const p = session.chess.state.board[sq];
    if (!p) return [];
    const color = Chess.colorOf(p);
    const state = session.chess.state;
    const s = color === state.turn ? state : { ...state, turn: color, ep: -1 };
    return Chess.legalMoves(s).filter(m => m.from === sq);
}

function select(sq) {
    session.selected = sq;
    renderBoard();
    renderTip();
}

function clearSelection() {
    if (session.selected === null) return;
    session.selected = null;
    renderBoard();
    renderTip();
}

function tryMove(from, to) {
    const moves = session.chess.legal().filter(m => m.from === from && m.to === to);
    if (!moves.length) return false;
    if (moves.length > 1 && options.autoQueen) commitMove(Chess.sqName(from) + Chess.sqName(to) + 'q');
    else if (moves.length > 1) showPromotion(from, to);
    else commitMove(Chess.toUci(moves[0]));
    return true;
}

function showPromotion(from, to) {
    const color = session.chess.turn;
    const box = $('promo-picker');
    box.innerHTML = `<div class="promo-box"><p>${escapeHtml(t('promote_to'))}</p><div class="promo-options">${['q', 'r', 'b', 'n'].map(p =>
        `<button type="button" data-promo="${p}" title="${escapeHtml(pieceName(p))}">${PIECE_SVG.html(color === 'w' ? p.toUpperCase() : p)}</button>`).join('')}</div></div>`;
    box.classList.remove('hidden');
    box.onclick = (e) => {
        const btn = e.target.closest('[data-promo]');
        box.classList.add('hidden');
        if (btn) commitMove(Chess.sqName(from) + Chess.sqName(to) + btn.dataset.promo);
        else clearSelection();
    };
}

function commitMove(uci) {
    const s = session;
    const mover = s.chess.turn;
    const now = performance.now();
    if (s.mode !== 'online' && localClockRunning()) s.clocks[mover] = Math.max(0, s.clocks[mover] - (now - s.turnStartedAt));
    const entry = s.chess.move(uci);
    if (!entry) return;
    s.turnStartedAt = now;
    s.selected = null;
    s.hint = null;

    if (s.mode !== 'online') {
        const out = s.chess.outcome();
        renderAll(entry);
        if (out.over) return finishLocal(out.result, out.reason);
        if (s.mode === 'local' && autoFlipOn()) {
            const seq = s.seq;
            setTimeout(() => {
                if (session && session.seq === seq && !session.over) {
                    session.viewColor = session.chess.turn;
                    renderAll();
                }
            }, 520);
        }
        maybeComputerMove();
        return;
    }

    s.pending = true;
    renderAll(entry);
    api('move', { token: s.token, uci, ply: s.chess.history.length - 1 }).then(result => {
        if (session !== s) return;
        s.pending = false;
        if (result.error) {
            showError(result.error);
            s.chess = Chess.Game.fromMoves(s.server ? s.server.moves : []);
            renderAll();
        }
        poll();
    });
}

function maybeComputerMove() {
    const s = session;
    if (s.mode !== 'ai' || s.over || s.chess.turn === s.myColor || s.aiThinking) return;
    s.aiThinking = true;
    renderAll();
    const started = performance.now();
    const ply = s.chess.history.length;
    askEngine(s.chess.state, s.level).then(uci => {
        const wait = Math.max(0, 450 - (performance.now() - started));
        setTimeout(() => {
            // Undo or a new game may have happened meanwhile.
            if (session !== s || s.chess.history.length !== ply || !s.aiThinking) return;
            s.aiThinking = false;
            if (uci) commitMove(uci);
            else renderAll();
        }, wait);
    });
}

// --- Clocks ---
function localClockRunning() {
    return session.timeMs > 0 && !session.over;
}

// Remaining time per colour as of now, or null without a clock.
function clockValues() {
    const s = session;
    if (isOnline()) {
        const d = s.server;
        if (!d || !d.timeControl) return null;
        const v = { w: d.whiteMs, b: d.blackMs };
        if (d.clockRunning && !s.over) {
            const toMove = d.moves.length % 2 === 0 ? 'w' : 'b';
            v[toMove] = Math.max(0, v[toMove] - (performance.now() - s.serverAt));
        }
        return { values: v, running: d.clockRunning && !s.over ? (d.moves.length % 2 === 0 ? 'w' : 'b') : null };
    }
    if (!s.timeMs) return null;
    const v = { ...s.clocks };
    const running = localClockRunning() ? s.chess.turn : null;
    if (running) v[running] = Math.max(0, v[running] - (performance.now() - s.turnStartedAt));
    return { values: v, running };
}

function fmtClock(ms) {
    const total = Math.ceil(ms / 1000);
    if (ms < 10000) return `0:${(ms / 1000).toFixed(1).padStart(4, '0')}`;
    return `${Math.floor(total / 60)}:${String(total % 60).padStart(2, '0')}`;
}

let lastFlagPoll = 0;
function tickClocks() {
    if (!session || !$('screen-game').classList.contains('active')) return;
    const c = clockValues();
    if (!c) return;
    document.querySelectorAll('.clock[data-color]').forEach(el => {
        const color = el.dataset.color;
        el.textContent = fmtClock(c.values[color]);
        el.classList.toggle('running', c.running === color);
        el.classList.toggle('low', c.values[color] < 20000);
    });
    if (c.running && c.values[c.running] <= 0) {
        if (isOnline()) {
            if (performance.now() - lastFlagPoll > 1000) { lastFlagPoll = performance.now(); poll(); }
        } else {
            session.clocks[c.running] = 0;
            const winner = other(c.running);
            const canMate = canDeliverMate(session.chess.state, winner);
            finishLocal(canMate ? (winner === 'w' ? '1-0' : '0-1') : '1/2-1/2', canMate ? 'timeout' : 'timeout_draw');
        }
    }
}

function canDeliverMate(state, color) {
    let minors = 0;
    for (const p of state.board) {
        if (!p || Chess.colorOf(p) !== color) continue;
        const t = p.toLowerCase();
        if (t === 'p' || t === 'r' || t === 'q') return true;
        if (t === 'b' || t === 'n') minors++;
    }
    return minors >= 2;
}

// --- Rendering ---
function displayIndex(sq) {
    return bottomColor() === 'w' ? sq : 63 - sq;
}

function renderAll(animate = null) {
    if (!session) return;
    renderBoard(animate);
    renderBars();
    renderHeader();
    renderMoves();
    renderActions();
    renderBanner();
    renderTip();
    tickClocks();
}

let drag = null;
let renderDeferred = false;

function renderBoard(animate = null) {
    if (drag && drag.started) { renderDeferred = true; return; }
    const s = session;
    const state = s.chess.state;
    const last = s.chess.history[s.chess.history.length - 1];
    const checkSq = Chess.inCheck(state, state.turn) ? state.board.findIndex(p => p === (state.turn === 'w' ? 'K' : 'k')) : -1;
    const analysis = s.selected !== null ? Tips.analyze(state, s.selected) : null;
    const targetMap = new Map(analysis ? analysis.targets.map(x => [x.to, x]) : []);
    const showTargets = analysis && (actionable(s.selected) || tipsOn);
    const flipped = bottomColor() === 'b';
    const hanging = tipsOn && !s.over ? new Set(Tips.hanging(state, s.mode === 'online' || s.mode === 'ai' ? s.myColor : state.turn).map(h => h.sq)) : new Set();

    let html = '';
    for (let i = 0; i < 64; i++) {
        const sq = flipped ? 63 - i : i;
        const r = Chess.row(sq);
        const c = Chess.col(sq);
        const cls = ['sq', (r + c) % 2 === 0 ? 'light' : 'dark'];
        const p = state.board[sq];
        if (last && (sq === last.from || sq === last.to)) cls.push('last');
        if (sq === s.selected) cls.push('sel');
        if (sq === checkSq) cls.push('check');
        if (s.hint && sq === s.hint.from) cls.push('hint-from');
        if (s.hint && sq === s.hint.to) cls.push('hint-to');
        if (p && actionable(sq)) cls.push('mine');
        if (hanging.has(sq)) cls.push('attacked-piece');
        let mark = '';
        const target = showTargets ? targetMap.get(sq) : null;
        if (target) {
            cls.push('target');
            if (target.captured) cls.push('capture');
            else cls.push(target.unsafe ? 'unsafe' : 'safe');
            if (target.special) cls.push('special');
            if (target.check) cls.push('gives-check');
            mark = `<span class="mark"></span><span class="mark-check">${target.mate ? '#' : '+'}</span>`;
        }
        const coords = (i % 8 === 0 ? `<span class="coord rank">${8 - r}</span>` : '') + (i >= 56 ? `<span class="coord file">${'ABCDEFGH'[c]}</span>` : '');
        html += `<div class="${cls.join(' ')}" data-sq="${sq}">${coords}${p ? PIECE_SVG.html(p) : ''}${mark}</div>`;
    }
    const board = $('board');
    const fromRect = animate ? squareRect(animate.from) : null;
    board.innerHTML = html;
    board.classList.toggle('tips-on', tipsOn);
    // Looking at moves that can't be played right now: markers turn grey.
    board.classList.toggle('inspect', !!showTargets && !actionable(s.selected));
    drawArrows();
    if (animate && fromRect) {
        animatePiece(animate.to, fromRect);
        if (animate.captured) animateCapture(animate);
    }
}

function squareEl(sq) {
    return $('board').querySelector(`[data-sq="${sq}"]`);
}

function squareRect(sq) {
    const el = squareEl(sq);
    return el ? el.getBoundingClientRect() : null;
}

function animatePiece(to, fromRect) {
    const speed = ANIM_SPEED[options.anim];
    if (!speed) return;
    const el = squareEl(to);
    const pc = el && el.querySelector('.pc');
    if (!pc) return;
    const toRect = el.getBoundingClientRect();
    pc.classList.add('anim');
    pc.style.transition = 'none';
    pc.style.transform = `translate(${fromRect.left - toRect.left}px, ${fromRect.top - toRect.top}px)`;
    pc.getBoundingClientRect();
    pc.style.transition = `transform ${220 * speed}ms cubic-bezier(0.4, 0, 0.2, 1)`;
    pc.style.transform = '';
    setTimeout(() => { pc.classList.remove('anim'); pc.style.transition = ''; }, 260 * speed);
}

// The taken piece stays on its square for a moment, then spins away under the arriving piece.
function animateCapture(entry) {
    if (!ANIM_SPEED[options.anim]) return;
    const sq = entry.ep ? Chess.row(entry.from) * 8 + Chess.col(entry.to) : entry.to;
    const el = squareEl(sq);
    if (!el) return;
    el.insertAdjacentHTML('beforeend', '<span class="capture-burst"></span>' + PIECE_SVG.html(entry.captured));
    const ghost = el.lastElementChild;
    ghost.classList.add('capture-ghost');
    const burst = ghost.previousElementSibling;
    setTimeout(() => { ghost.remove(); burst.remove(); }, 750 * ANIM_SPEED[options.anim]);
}

function drawArrows() {
    const svg = $('board-arrows');
    const h = session.hint;
    if (!h) { svg.innerHTML = ''; return; }
    const center = (sq) => {
        const di = displayIndex(sq);
        return [(di % 8) * 100 + 50, Math.floor(di / 8) * 100 + 50];
    };
    const [x1, y1] = center(h.from);
    const [x2, y2] = center(h.to);
    const angle = Math.atan2(y2 - y1, x2 - x1);
    const len = Math.hypot(x2 - x1, y2 - y1);
    const ex = x1 + Math.cos(angle) * (len - 34);
    const ey = y1 + Math.sin(angle) * (len - 34);
    const back = (d) => [x2 - Math.cos(angle) * d, y2 - Math.sin(angle) * d];
    const [bx, by] = back(48);
    const tip = `${x2 - Math.cos(angle) * 12},${y2 - Math.sin(angle) * 12}`;
    const left = `${bx + Math.cos(angle + Math.PI / 2) * 26},${by + Math.sin(angle + Math.PI / 2) * 26}`;
    const right = `${bx + Math.cos(angle - Math.PI / 2) * 26},${by + Math.sin(angle - Math.PI / 2) * 26}`;
    svg.innerHTML = `<line x1="${x1}" y1="${y1}" x2="${ex}" y2="${ey}" stroke-width="18"/><polygon points="${tip} ${left} ${right}"/>`;
}

// Pieces each side has taken, and who is ahead in material.
function capturedSummary() {
    const count = { w: {}, b: {} };
    for (const p of session.chess.state.board) {
        if (!p) continue;
        const c = Chess.colorOf(p);
        const k = p.toLowerCase();
        count[c][k] = (count[c][k] || 0) + 1;
    }
    const taken = { w: [], b: [] };
    let material = 0;
    for (const color of ['w', 'b']) {
        for (const k of ['q', 'r', 'b', 'n', 'p']) {
            const missing = Math.max(0, START_COUNT[k] - (count[color][k] || 0));
            for (let i = 0; i < missing; i++) taken[other(color)].push(color === 'w' ? k.toUpperCase() : k);
        }
        for (const [k, n] of Object.entries(count[color])) material += (color === 'w' ? 1 : -1) * MATERIAL[k] * n;
    }
    return { taken, material };
}

function renderBars() {
    const s = session;
    const bottom = bottomColor();
    const { taken, material } = capturedSummary();
    const clocks = clockValues();
    const anyScore = s.players.some(p => p.points2 > 0);
    const bar = (color) => {
        const p = playerOf(color) || { name: t(color === 'w' ? 'white' : 'black') };
        const adv = color === 'w' ? material : -material;
        const thinking = s.mode === 'ai' && s.aiThinking && color !== s.myColor ? '<span class="thinking"><i></i><i></i><i></i></span>' : '';
        const dot = isOnline() && !p.me ? `<span class="online-dot${p.online ? ' on' : ''}" title="${escapeHtml(t(p.online ? 'online' : 'offline'))}"></span>` : '';
        const score = anyScore ? `<span class="pb-score">${fmtPoints(p.points2 || 0)}</span>` : '';
        const label = s.mode === 'ai' && p.computer ? `${t('computer')} · ${t('level_' + s.level)}` : p.name;
        return `<div class="pb-avatar">${PIECE_SVG.html(color === 'w' ? 'K' : 'k')}</div>
            <div class="pb-info">
                <span class="pb-name">${dot}<span>${escapeHtml(label)}</span>${score}${thinking}</span>
                <span class="pb-captured">${taken[color].map(PIECE_SVG.html).join('')}${adv > 0 ? `<span class="pb-adv">+${adv}</span>` : ''}</span>
            </div>
            ${clocks ? `<div class="clock" data-color="${color}"></div>` : ''}`;
    };
    for (const [id, color] of [['bar-top', other(bottom)], ['bar-bottom', bottom]]) {
        const el = $(id);
        el.innerHTML = bar(color);
        el.classList.toggle('to-move', !s.over && s.chess.turn === color);
    }
}

function renderHeader() {
    const s = session;
    const el = $('turn-indicator');
    const turn = s.chess.turn;
    let text;
    let cls = 'their-turn';
    if (s.over) {
        text = t('game_over');
        cls = 'done';
    } else if (s.mode === 'local') {
        text = t('to_move', { name: playerOf(turn).name });
        cls = 'my-turn';
    } else if (turn === s.myColor) {
        text = t('your_move');
        cls = 'my-turn';
    } else {
        text = s.mode === 'ai' ? t('computer_thinking') : t('their_move', { name: (playerOf(turn) || {}).name || '' });
    }
    if (!s.over && s.chess.inCheck()) text += ' · ' + t('check');
    el.textContent = text;
    el.className = 'turn-indicator ' + cls;

    let badge = '';
    if (s.mode === 'local') badge = t('tab_local');
    else if (s.mode === 'ai') badge = t('level_' + s.level);
    else if (s.server && s.server.timeControl) badge = t('time_note', { min: s.server.timeControl / 60 });
    $('mode-badge').textContent = badge;
}

function renderMoves() {
    const list = $('move-list');
    const h = session.chess.history;
    let html = '';
    for (let i = 0; i < h.length; i += 2) {
        const cur = (j) => (j === h.length - 1 ? ' current' : '');
        html += `<li class="move-row"><span class="move-num">${i / 2 + 1}.</span><span class="move-san${cur(i)}">${sanHtml(h[i].san)}</span>${h[i + 1] ? `<span class="move-san${cur(i + 1)}">${sanHtml(h[i + 1].san)}</span>` : ''}</li>`;
    }
    list.innerHTML = html;
    list.dataset.empty = t('moves_empty');
    const current = list.querySelector('.current');
    if (current) {
        if (list.scrollWidth > list.clientWidth + 2) list.scrollLeft = list.scrollWidth;
        else list.scrollTop = list.scrollHeight;
    }
}

function renderActions() {
    const s = session;
    const over = !!s.over;
    const local = s.mode !== 'online';
    const tips = $('btn-tips');
    tips.setAttribute('aria-pressed', String(tipsOn));
    $('btn-hint').classList.toggle('hidden', !local);
    $('btn-hint').disabled = over || !canMoveNow();
    $('btn-undo').classList.toggle('hidden', !local);
    const humanMoves = s.mode === 'ai' ? s.chess.history.filter((h, i) => (i % 2 === 0 ? 'w' : 'b') === s.myColor).length : s.chess.history.length;
    // Taking moves back would make the clocks meaningless.
    $('btn-undo').disabled = over || humanMoves === 0 || s.timeMs > 0;
    $('btn-flip').classList.toggle('hidden', false);
    const myOfferPending = isOnline() && s.drawOffer !== null && s.server && s.drawOffer === s.server.mySlot;
    $('btn-draw').disabled = over || s.chess.history.length < 2 || myOfferPending || (isOnline() && (!s.server || s.server.status !== 'playing'));
    $('btn-draw').querySelector('span').textContent = myOfferPending ? t('draw_offered') : t('draw');
    $('btn-resign').disabled = over || (isOnline() && (!s.server || s.server.status !== 'playing'));
    $('btn-new-game').classList.toggle('hidden', !over);
    $('btn-chat').classList.toggle('hidden', local);
    $('chat').classList.toggle('hidden', local);
    const opp = isOnline() && s.server ? s.server.players.find(p => p.slot !== s.server.mySlot) : null;
    $('chat-input').disabled = local || !opp || opp.left;
    $('btn-new-game').querySelector('span').textContent = isOnline() ? t('rematch') : t('play_again');
    $('screen-game').classList.toggle('tips-enabled', tipsOn && !over);
}

// --- Chat (online only, plain text) ---
const wideLayout = window.matchMedia('(min-width: 900px) and (orientation: landscape)');
const chatVisible = () => wideLayout.matches || document.body.classList.contains('chat-open');

function chatName(slot) {
    const p = session.server && session.server.players.find(x => x.slot === slot);
    return p ? p.name : '';
}

function addChatMessages(list, quiet = false) {
    const s = session;
    const fresh = list.filter(m => m.id > s.chat.lastId);
    if (!fresh.length) return;
    s.chat.messages.push(...fresh);
    s.chat.lastId = fresh[fresh.length - 1].id;
    if (s.chat.messages.length > 100) s.chat.messages.splice(0, s.chat.messages.length - 100);
    const theirs = fresh.filter(m => m.slot !== s.server.mySlot);
    if (!quiet && theirs.length && !chatVisible()) {
        s.chat.unread += theirs.length;
        const last = theirs[theirs.length - 1];
        showToast(`${chatName(last.slot)}: ${last.text.length > 70 ? last.text.slice(0, 70) + '…' : last.text}`);
    }
    renderChat();
}

function renderChat(stickToBottom = false) {
    const s = session;
    if (!isOnline()) return;
    const list = $('chat-list');
    const nearBottom = list.scrollHeight - list.scrollTop - list.clientHeight < 48;
    const msgs = s.chat.messages;
    list.innerHTML = !msgs.length
        ? `<li class="chat-empty">${escapeHtml(t('chat_empty'))}</li>`
        : msgs.map((m, i) => {
            const mine = !!s.server && m.slot === s.server.mySlot;
            const author = i === 0 || msgs[i - 1].slot !== m.slot
                ? `<span class="chat-author">${escapeHtml(mine ? t('chat_you') : chatName(m.slot))}</span>` : '';
            return `<li class="chat-msg ${mine ? 'mine' : 'theirs'}">${author}<span class="chat-text">${escapeHtml(m.text)}</span></li>`;
        }).join('');
    if (stickToBottom || nearBottom) list.scrollTop = list.scrollHeight;
    const badge = $('chat-badge');
    badge.textContent = s.chat.unread > 9 ? '9+' : String(s.chat.unread);
    badge.classList.toggle('hidden', !s.chat.unread);
}

function openChat() {
    document.body.classList.add('chat-open');
    session.chat.unread = 0;
    renderChat(true);
    // On touch screens the keyboard would cover the messages, so only focus with a mouse.
    if (!window.matchMedia('(pointer: coarse)').matches) $('chat-input').focus();
}

function closeChat() {
    document.body.classList.remove('chat-open');
    $('chat-input').blur();
}

async function sendChat(e) {
    e.preventDefault();
    const s = session;
    const input = $('chat-input');
    const text = input.value.trim();
    if (!text || !isOnline()) return;
    input.value = '';
    const res = await api('send_message', { token: s.token, text });
    if (session !== s) return;
    if (res.error) {
        if (!input.value) input.value = text;
        showError(res.error);
        return;
    }
    addChatMessages([res.message], true);
    renderChat(true);
}

function renderBanner() {
    const s = session;
    const banner = $('banner');
    let html = '';
    let warn = false;
    if (isOnline() && s.server && !s.over) {
        const opp = s.players.find(p => !p.me);
        const theirOffer = s.drawOffer !== null && s.drawOffer !== s.server.mySlot;
        if (theirOffer) {
            html = `<span>${escapeHtml(t('draw_offer_received', { name: opp ? opp.name : '' }))}</span>
                <button type="button" class="btn btn-primary" data-draw-answer="1">${escapeHtml(t('accept'))}</button>
                <button type="button" class="btn btn-secondary" data-draw-answer="0">${escapeHtml(t('decline'))}</button>`;
        } else if (opp && !opp.online && s.server.status === 'playing') {
            warn = true;
            html = `<span>${escapeHtml(t('opponent_offline', { name: opp.name }))}</span>`;
            if (opp.idle >= CLAIM_AFTER_S || opp.left) html += `<button type="button" class="btn btn-accent" id="btn-claim">${escapeHtml(t('claim_win'))}</button>`;
        }
    }
    const changed = banner.innerHTML !== html;
    banner.innerHTML = html;
    banner.classList.toggle('warn', warn);
    if (changed && banner.classList.contains('hidden') === !!html) {
        banner.classList.toggle('hidden', !html);
        requestAnimationFrame(fitBoard);
    }
}

// --- Tips panel ---
const BULB = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7c.6.5 1 1.3 1 2.1V17h6v-.2c0-.8.4-1.6 1-2.1A7 7 0 0012 2z"/></svg>';
const fact = (kind, text, extra = '') => `<li><span class="ti ${kind}"></span><span class="${extra}">${text}</span></li>`;
const on = (sq) => `<strong>${upperFiles(Chess.sqName(sq))}</strong>`;

function renderTip() {
    const s = session;
    const card = $('tip-card');
    if (!tipsOn || s.over) {
        card.classList.remove('visible');
        return;
    }
    card.classList.add('visible');
    const state = s.chess.state;
    let html;
    if (s.hint) {
        html = `<div class="tip-empty">${BULB}<span>${t('hint_text', { san: `<strong>${sanHtml(s.hint.san)}</strong>` })}</span></div>`;
    } else if (s.selected !== null && state.board[s.selected]) {
        html = pieceTip(Tips.analyze(state, s.selected));
    } else {
        const me = s.mode === 'local' ? state.turn : s.myColor;
        const lines = [];
        if (Chess.inCheck(state, me) && state.turn === me) lines.push(t('tip_in_check'));
        const hang = Tips.hanging(state, me).slice(0, 2);
        for (const h of hang) lines.push(t('tip_hanging', { piece: escapeHtml(pieceName(h.piece)), sq: on(h.sq) }));
        if (!lines.length) lines.push(t(state.turn === me || s.mode === 'local' ? 'tip_select' : 'tip_select_wait'));
        html = `<div class="tip-empty">${BULB}<span>${lines.join('<br>')}</span></div>`;
    }
    card.innerHTML = `<div class="tip-inner">${html}</div>`;
}

function pieceTip(a) {
    const name = escapeHtml(pieceName(a.piece));
    const owner = a.inspectOnly && (session.mode === 'local' || a.color !== session.myColor) ? t('tip_opponent_piece') : t('tip_on_square', { sq: upperFiles(Chess.sqName(a.sq)) });
    const facts = [];
    const n = a.targets.length;
    if (a.inspectOnly) facts.push(fact('special', t(a.color === session.myColor ? 'tip_not_turn_mine' : 'tip_threats')));
    if (a.pinned) facts.push(fact('warn', t(n ? 'tip_pinned_some' : 'tip_pinned'), 'warn-text'));
    if (a.attacked && a.type !== 'k') facts.push(fact('warn', t(a.defended ? 'tip_attacked_defended' : 'tip_attacked'), 'warn-text'));
    if (a.type === 'k' && a.attacked) facts.push(fact('warn', t('tip_in_check'), 'warn-text'));
    if (!n && !a.pinned) facts.push(fact('unsafe', t('tip_no_moves')));
    if (n) {
        const unsafe = a.targets.filter(x => !x.captured && x.unsafe).length;
        facts.push(fact('safe', t('tip_moves', { n })));
        if (unsafe) facts.push(fact('unsafe', t('tip_unsafe', { n: unsafe })));
    }
    for (const x of a.targets.filter(x => x.captured).slice(0, 3)) {
        facts.push(fact('capture', t(x.unsafe ? 'tip_capture_trade' : 'tip_capture_free', { piece: escapeHtml(pieceName(x.captured)), sq: on(x.to) })));
    }
    const mates = a.targets.filter(x => x.mate);
    const checks = a.targets.filter(x => x.check && !x.mate);
    if (mates.length) facts.push(fact('check', t('tip_mate', { san: `<strong>${sanHtml(mates[0].san)}</strong>` })));
    if (checks.length) facts.push(fact('check', t('tip_checks', { list: checks.slice(0, 3).map(x => `<strong>${sanHtml(x.san)}</strong>`).join(', ') })));
    const specials = new Set(a.targets.map(x => x.special).filter(Boolean));
    for (const sp of specials) facts.push(fact('special', t('tip_special_' + sp)));
    return `<div class="tip-head"><span class="tip-piece">${PIECE_SVG.html(a.piece)}</span><div><strong>${name}</strong><small>${escapeHtml(owner)}</small></div></div>
        <p class="tip-how">${escapeHtml(t('how_' + a.type))}</p>
        <ul class="tip-facts">${facts.join('')}</ul>`;
}

function toggleTips() {
    tipsOn = !tipsOn;
    storageSet('chess_tips', tipsOn ? '1' : '0');
    showToast(t(tipsOn ? 'tips_on' : 'tips_off'));
    renderAll();
    requestAnimationFrame(fitBoard);
}

async function showHint() {
    const s = session;
    if (!canMoveNow()) return;
    const btn = $('btn-hint');
    setBusy(btn, true);
    const ply = s.chess.history.length;
    const uci = await askEngine(s.chess.state, 'hard');
    setBusy(btn, false);
    if (session !== s || s.chess.history.length !== ply || !uci) return renderActions();
    const move = s.chess.legal().find(m => Chess.toUci(m) === uci);
    s.hint = { from: move.from, to: move.to, san: Chess.toSAN(s.chess.state, move) };
    s.selected = null;
    if (!tipsOn) showToast(t('hint_text', { san: sanText(s.hint.san) }));
    renderAll();
}

// --- Board input: tap a piece then a square, or drag ---
function squareFromPoint(x, y) {
    const rect = $('board').getBoundingClientRect();
    if (x < rect.left || y < rect.top || x >= rect.right || y >= rect.bottom) return -1;
    const c = Math.floor((x - rect.left) / (rect.width / 8));
    const r = Math.floor((y - rect.top) / (rect.height / 8));
    const di = r * 8 + c;
    return bottomColor() === 'w' ? di : 63 - di;
}

function onBoardDown(e) {
    if (!session || e.button > 0) return;
    const sq = squareFromPoint(e.clientX, e.clientY);
    if (sq < 0) return;
    $('promo-picker').classList.add('hidden');
    const s = session;
    if (s.selected !== null && actionable(s.selected) && tryMoveIfTarget(s.selected, sq)) return;
    const p = s.chess.state.board[sq];
    if (p && (actionable(sq) || tipsOn)) {
        const wasSelected = s.selected === sq;
        if (s.hint) s.hint = null;
        select(sq);
        drag = { sq, x: e.clientX, y: e.clientY, started: false, wasSelected, can: actionable(sq), ghost: null, hover: -1 };
        $('board').setPointerCapture(e.pointerId);
    } else {
        clearSelection();
    }
}

function tryMoveIfTarget(from, to) {
    return session.chess.legal().some(m => m.from === from && m.to === to) && tryMove(from, to);
}

function onBoardMove(e) {
    if (!drag || !drag.can) return;
    if (!drag.started) {
        if (Math.hypot(e.clientX - drag.x, e.clientY - drag.y) < 6) return;
        const el = squareEl(drag.sq);
        const pc = el && el.querySelector('.pc');
        if (!pc) return;
        drag.started = true;
        const size = el.getBoundingClientRect().width;
        const ghost = pc.cloneNode(true);
        ghost.classList.add('dragging');
        ghost.style.width = ghost.style.height = size + 'px';
        document.body.appendChild(ghost);
        drag.ghost = ghost;
        drag.size = size;
        el.classList.add('drag-origin');
    }
    drag.ghost.style.left = (e.clientX - drag.size / 2) + 'px';
    drag.ghost.style.top = (e.clientY - drag.size / 2) + 'px';
    const over = squareFromPoint(e.clientX, e.clientY);
    if (over !== drag.hover) {
        if (drag.hover >= 0) { const prev = squareEl(drag.hover); if (prev) prev.classList.remove('hover'); }
        drag.hover = over;
        const cur = over >= 0 ? squareEl(over) : null;
        if (cur && cur.classList.contains('target')) cur.classList.add('hover');
    }
}

function endDrag() {
    if (drag && drag.ghost) drag.ghost.remove();
    const d = drag;
    drag = null;
    if (d && d.started) {
        const el = squareEl(d.sq);
        if (el) el.classList.remove('drag-origin');
        if (renderDeferred) { renderDeferred = false; renderAll(); }
    }
    return d;
}

function onBoardUp(e) {
    if (!drag) return;
    const d = endDrag();
    if (d.started) {
        const to = squareFromPoint(e.clientX, e.clientY);
        if (to >= 0 && to !== d.sq && actionable(d.sq) && tryMoveIfTarget(d.sq, to)) return;
        renderBoard();
    } else if (d.wasSelected) {
        clearSelection();
    }
}

// --- Actions ---
function undoMove() {
    const s = session;
    if (s.over || s.mode === 'online') return;
    let n = 1;
    if (s.mode === 'ai') {
        if (s.aiThinking) { s.aiThinking = false; n = 1; }
        else n = s.chess.turn === s.myColor ? 2 : 1;
    }
    const moves = s.chess.history.map(h => h.uci).slice(0, Math.max(0, s.chess.history.length - n));
    s.chess = Chess.Game.fromMoves(moves);
    s.selected = null;
    s.hint = null;
    s.turnStartedAt = performance.now();
    if (s.mode === 'local' && autoFlipOn()) s.viewColor = s.chess.turn;
    renderAll();
    maybeComputerMove();
}

function flipBoard() {
    session.manualFlip = !session.manualFlip;
    renderAll();
}

function offerDraw() {
    const s = session;
    if (s.mode === 'online') {
        api('offer_draw', { token: s.token }).then(r => {
            if (r.error) return showError(r.error);
            showToast(t('draw_sent'));
            poll();
        });
    } else if (s.mode === 'local') {
        openConfirm(t('draw_title_q'), t('draw_local_text'), t('agree_draw'), () => finishLocal('1/2-1/2', 'agreement'));
    } else {
        // The computer only accepts when it is clearly worse off.
        const aiColor = other(s.myColor);
        const score = ChessAI.evaluate(s.chess.state) * (s.chess.turn === aiColor ? 1 : -1);
        if (score < -150) finishLocal('1/2-1/2', 'agreement');
        else showToast(t('draw_declined_ai'));
    }
}

function answerDraw(accept) {
    api('answer_draw', { token: session.token, accept: accept ? '1' : '0' }).then(r => {
        if (r.error) showError(r.error);
        poll();
    });
}

function resign() {
    const s = session;
    const loser = s.mode === 'local' ? s.chess.turn : s.myColor;
    const text = s.mode === 'local' ? t('resign_local_text', { name: playerOf(loser).name }) : t('resign_text');
    openConfirm(t('resign_title'), text, t('resign'), () => {
        if (s.mode === 'online') {
            api('resign', { token: s.token }).then(r => { if (r.error) showError(r.error); poll(); });
        } else {
            finishLocal(loser === 'w' ? '0-1' : '1-0', 'resign');
        }
    });
}

async function claimWin() {
    const r = await api('claim_win', { token: session.token });
    if (r.error) showError(r.error);
    poll();
}

function finishLocal(result, reason) {
    const s = session;
    if (s.over) return;
    s.over = { result, reason };
    s.aiThinking = false;
    s.selected = null;
    s.hint = null;
    if (result === '1/2-1/2') s.players.forEach(p => { p.points2 += 1; });
    else playerOf(result === '1-0' ? 'w' : 'b').points2 += 2;
    renderAll();
    setTimeout(() => { if (session === s && s.over) showGameOver(); }, 450);
}

// --- Game over ---
const GAMEOVER_ICONS = {
    win: `<svg viewBox="0 0 60 60" width="60" height="60"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--green)" stroke-width="3"/><path d="M18 30l8 8 16-16" fill="none" stroke="var(--green)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    lose: `<svg viewBox="0 0 60 60" width="60" height="60"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--red)" stroke-width="3"/><path d="M20 20l20 20M40 20l-20 20" fill="none" stroke="var(--red)" stroke-width="3" stroke-linecap="round"/></svg>`,
    draw: `<svg viewBox="0 0 60 60" width="60" height="60"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--yellow)" stroke-width="3"/><path d="M19 25h22M19 35h22" fill="none" stroke="var(--yellow)" stroke-width="3" stroke-linecap="round"/></svg>`,
    trophy: `<svg viewBox="0 0 60 60" width="60" height="60" fill="none" stroke="var(--yellow)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10h20v14a10 10 0 01-20 0z"/><path d="M20 14h-7a6 6 0 006 8M40 14h7a6 6 0 01-6 8M30 34v8M22 50h16M24 42h12v8H24z"/></svg>`,
};

function showGameOver() {
    const s = session;
    const { result, reason } = s.over;
    const draw = result === '1/2-1/2';
    const winnerColor = draw ? null : (result === '1-0' ? 'w' : 'b');
    const winner = winnerColor ? playerOf(winnerColor) : null;
    const loser = winnerColor ? playerOf(other(winnerColor)) : null;
    let title;
    let icon;
    if (draw) { title = t('draw_result'); icon = 'draw'; }
    else if (s.mode === 'local') { title = t('player_wins', { name: winner.name }); icon = 'trophy'; }
    else if (winnerColor === s.myColor) { title = t('you_win'); icon = 'win'; }
    else { title = t('you_lose'); icon = 'lose'; }

    // The side whose clock ran out is the one that didn't win (or either side in a flag-fall draw).
    const flagged = reason === 'timeout_draw' ? playerOf(s.chess.turn) : loser;
    const names = { winner: winner ? winner.name : '', loser: loser ? loser.name : '', flagged: flagged ? flagged.name : '' };
    $('gameover-title').textContent = title;
    $('gameover-text').textContent = t('reason_' + reason, names);
    $('gameover-icon').innerHTML = GAMEOVER_ICONS[icon];
    const [a, b] = s.players;
    $('score-line').innerHTML = a && b ? `<span>${escapeHtml(a.name)}</span><strong>${fmtPoints(a.points2)} – ${fmtPoints(b.points2)}</strong><span>${escapeHtml(b.name)}</span>` : '';
    $('btn-gameover-home').textContent = isOnline() && s.isPublic ? t('play_someone_else') : t('back_home');
    updateRematchUI();
    showModal('confirm-overlay', false);
    $('promo-picker').classList.add('hidden');
    showModal('gameover-overlay', true);
    if (icon === 'win' || icon === 'trophy') spawnConfetti();
}

function updateRematchUI() {
    const s = session;
    const btn = $('btn-rematch');
    const status = $('rematch-status');
    status.className = 'rematch-status';
    status.textContent = '';
    if (!isOnline()) {
        btn.textContent = s.mode === 'local' ? t('play_again_swap') : t('play_again');
        btn.disabled = false;
        return;
    }
    const opp = s.players.find(p => !p.me);
    const me = s.players.find(p => p.me);
    btn.textContent = t('rematch');
    if (!opp || opp.left) {
        btn.disabled = true;
        status.textContent = t('opponent_left', { name: opp ? opp.name : '' });
        status.classList.add('warn');
    } else if (me && me.wantsRematch) {
        btn.disabled = true;
        status.textContent = t('rematch_waiting', { name: opp.name });
    } else {
        btn.disabled = false;
        if (opp.wantsRematch) {
            status.textContent = t('rematch_invite', { name: opp.name });
            status.classList.add('invite');
        }
    }
}

async function playAgain() {
    const s = session;
    if (s.mode === 'online') {
        setBusy($('btn-rematch'), true);
        const r = await api('rematch', { token: s.token });
        setBusy($('btn-rematch'), false);
        if (r.error) return showError(r.error);
        poll();
        return;
    }
    const players = s.players;
    if (s.mode === 'local') players.forEach(p => { p.color = other(p.color); });
    const next = newSession(s.mode, { players, timeMs: s.timeMs, level: s.level, myColor: s.myColor });
    next.colorChoice = s.colorChoice;
    session = next;
    if (s.mode === 'ai') {
        if (s.colorChoice === 'r') assignAiColors();
        else { session.myColor = s.myColor === 'w' ? 'b' : 'w'; session.players[0].color = session.myColor; session.players[1].color = other(session.myColor); }
        session.viewColor = session.myColor;
    }
    enterGame();
    showToast(t('new_round', { color: t((session.mode === 'local' ? players[0].color : session.myColor) === 'w' ? 'white' : 'black') }));
}

function spawnConfetti() {
    const colors = ['#1E9E5E', '#6FDDA0', '#FFEAA7', '#FF6B6B', '#74B9FF', '#fdcb6e'];
    for (let i = 0; i < 60; i++) {
        const el = document.createElement('div');
        el.className = 'confetti';
        el.style.left = Math.random() * 100 + 'vw';
        el.style.background = colors[i % colors.length];
        el.style.animationDelay = Math.random() * 1.5 + 's';
        el.style.animationDuration = (2 + Math.random() * 2) + 's';
        el.style.width = el.style.height = (6 + Math.random() * 8) + 'px';
        el.style.borderRadius = i % 2 ? '50%' : '2px';
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 5000);
    }
}

// --- Layout: the largest board that fits with both player bars ---
function fitBoard() {
    if (!session || !$('screen-game').classList.contains('active')) return;
    const area = $('board-area');
    const style = getComputedStyle(area);
    const banner = $('banner');
    const w = area.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
    const h = area.clientHeight - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom)
        - $('bar-top').offsetHeight - $('bar-bottom').offsetHeight - 12
        - (banner.classList.contains('hidden') ? 0 : banner.offsetHeight + 8);
    const size = Math.max(208, Math.floor(Math.min(w, h, 760) / 8) * 8);
    $('screen-game').style.setProperty('--board-size', size + 'px');
    if (session.hint) drawArrows();
}

// --- Navigation ---
function backToLobby() {
    stopPolling();
    document.body.classList.remove('chat-open');
    $('chat-list').innerHTML = '';
    $('chat-input').value = '';
    endDrag();
    session = null;
    sessionSeq++;
    sessionSet('chess_token', null);
    ['gameover-overlay', 'confirm-overlay'].forEach(id => showModal(id, false));
    $('board').innerHTML = '';
    $('banner').classList.add('hidden');
    $('promo-picker').classList.add('hidden');
    $('room-code').value = '';
    $('btn-join').disabled = true;
    showScreen('lobby');
    goToStep(playerName ? 2 : 1);
}

async function leaveGame() {
    if (isOnline() && session.token) await api('leave', { token: session.token });
    backToLobby();
}

function confirmLeave() {
    const s = session;
    const inProgress = !s.over && s.chess.history.length > 0;
    if (isOnline() && s.server && s.server.status === 'playing') {
        openConfirm(t('leave_title'), t('leave_online_text'), t('leave'), leaveGame);
    } else if (!isOnline() && inProgress) {
        openConfirm(t('leave_title'), t('leave_local_text'), t('leave'), leaveGame);
    } else {
        leaveGame();
    }
}

// --- Rules & release notes ---
function openRules() {
    renderRules();
    showModal('rules-overlay', true);
    $('rules-overlay').querySelector('.modal-content').scrollTop = 0;
}

function renderStylePicker() {
    $('style-grid').innerHTML = PIECE_SVG.STYLES.map(name => {
        const squares = ['N', 'p', 'B', 'q'].map((p, i) => `<span class="sq ${i % 2 ? 'dark' : 'light'}">${PIECE_SVG.preview(p, name)}</span>`).join('');
        const on = name === PIECE_SVG.style;
        return `<button type="button" class="style-option${on ? ' active' : ''}" data-style="${name}" data-pieces="${name}" aria-pressed="${on}">
            <span class="style-preview">${squares}</span><span class="style-name">${escapeHtml(t('style_' + name))}</span>
        </button>`;
    }).join('');
}

const BOARD_THEMES = ['classic', 'green', 'brown', 'blue', 'grey', 'purple', 'coral'];

// Game options. Most work through attributes on <html> (see style.css), so changes show at once.
const DEFAULT_OPTIONS = { coords: true, lastMove: true, dots: true, autoQueen: false, anim: 'normal' };
const ANIM_SPEED = { normal: 1, fast: 0.5, off: 0 };
let options = { ...DEFAULT_OPTIONS };

function loadOptions() {
    try { options = { ...DEFAULT_OPTIONS, ...JSON.parse(storageGet('chess_options') || '{}') }; } catch (e) { options = { ...DEFAULT_OPTIONS }; }
    if (!(options.anim in ANIM_SPEED)) options.anim = 'normal';
    applyOptions();
}

function applyOptions() {
    const root = document.documentElement.dataset;
    root.coords = options.coords ? 'on' : 'off';
    root.lastMove = options.lastMove ? 'on' : 'off';
    root.dots = options.dots ? 'on' : 'off';
    root.anim = options.anim;
    document.documentElement.style.setProperty('--anim-k', ANIM_SPEED[options.anim] || 1);
}

function renderOptions() {
    document.querySelectorAll('[data-opt]').forEach(i => { i.checked = !!options[i.dataset.opt]; });
    document.querySelectorAll('[data-anim-opt]').forEach(b => {
        const on = b.dataset.animOpt === options.anim;
        b.classList.toggle('active', on);
        b.setAttribute('aria-checked', on);
    });
}

function setOption(key, value) {
    options[key] = value;
    storageSet('chess_options', JSON.stringify(options));
    applyOptions();
    renderOptions();
}

function renderBoardPicker() {
    const current = document.documentElement.dataset.board;
    $('board-grid').innerHTML = BOARD_THEMES.map(name => {
        const squares = [['light', 'n'], ['dark', 'Q'], ['dark', 'p'], ['light', 'B']].map(([c, p]) => `<span class="sq ${c}">${PIECE_SVG.html(p)}</span>`).join('');
        const on = name === current;
        return `<button type="button" class="style-option${on ? ' active' : ''}" data-board-theme="${name}" data-board="${name}" aria-pressed="${on}">
            <span class="board-preview">${squares}</span><span class="style-name">${escapeHtml(t('board_' + name))}</span>
        </button>`;
    }).join('');
}

// A fixed position for the Customise preview: last move highlighted, the white knight's moves shown.
// Colours, pieces and the options all come from CSS, so it is drawn only once.
function renderCustomPreview() {
    const box = $('custom-preview');
    if (box.childElementCount) return;
    const game = Chess.Game.fromMoves(['e2e4', 'e7e5', 'g1f3', 'b8c6', 'f1c4', 'g8f6']);
    const state = game.state;
    const last = game.history[game.history.length - 1];
    const knight = Chess.sqIndex('f3');
    const targets = new Map(Tips.analyze(state, knight).targets.map(x => [x.to, x]));
    let html = '';
    for (let sq = 0; sq < 64; sq++) {
        const r = Chess.row(sq);
        const c = Chess.col(sq);
        const cls = ['sq', (r + c) % 2 === 0 ? 'light' : 'dark'];
        if (sq === last.from || sq === last.to) cls.push('last');
        if (sq === knight) cls.push('sel');
        const x = targets.get(sq);
        if (x) cls.push('target', x.captured ? 'capture' : 'safe');
        const coords = (c === 0 ? `<span class="coord rank">${8 - r}</span>` : '') + (r === 7 ? `<span class="coord file">${'ABCDEFGH'[c]}</span>` : '');
        const p = state.board[sq];
        html += `<div class="${cls.join(' ')}">${coords}${p ? PIECE_SVG.html(p) : ''}${x ? '<span class="mark"></span>' : ''}</div>`;
    }
    box.innerHTML = html;
}

function setBoardTheme(name) {
    if (!BOARD_THEMES.includes(name)) name = 'classic';
    document.documentElement.dataset.board = name;
    storageSet('chess_board', name);
    document.querySelectorAll('[data-board-theme]').forEach(o => {
        const on = o.dataset.boardTheme === name;
        o.classList.toggle('active', on);
        o.setAttribute('aria-pressed', on);
    });
}

function setPieceStyle(name) {
    PIECE_SVG.setStyle(name);
    storageSet('chess_pieces', PIECE_SVG.style);
    document.querySelectorAll('.style-option').forEach(o => {
        const on = o.dataset.style === PIECE_SVG.style;
        o.classList.toggle('active', on);
        o.setAttribute('aria-pressed', on);
    });
}

function renderReleaseNotes() {
    $('release-list').innerHTML = RELEASES.map((r, i) => {
        const notes = r.notes[currentLang] || r.notes.en;
        const tag = i === 0 ? `<span class="release-tag">${escapeHtml(t('latest'))}</span>` : '';
        return `<section class="release${i === 0 ? ' current' : ''}">
            <div class="release-head"><strong>v${escapeHtml(r.version)}</strong>${tag}</div>
            <ul>${notes.map(n => `<li>${escapeHtml(n)}</li>`).join('')}</ul>
        </section>`;
    }).join('');
}

// --- Setup ---
function onLanguageChange() {
    if (isOnline()) renderChat();
    if (playerName) $('greeting-text').textContent = t('greeting', { name: playerName });
    $('random-desc').textContent = t('random_desc', { language: t('lang_' + currentLang) });
    $('lang-current').textContent = currentLang.toUpperCase();
    document.querySelectorAll('.lang-option').forEach(o => {
        const active = o.dataset.lang === currentLang;
        o.classList.toggle('active', active);
        o.setAttribute('aria-selected', String(active));
    });
    if (!$('release-overlay').classList.contains('hidden')) renderReleaseNotes();
    if (!$('rules-overlay').classList.contains('hidden')) renderRules();
    if (!$('style-overlay').classList.contains('hidden')) { renderStylePicker(); renderBoardPicker(); }
    if (session && $('screen-game').classList.contains('active')) {
        if (session.mode === 'ai') session.players[1].name = t('computer');
        renderAll();
        if (session.over && !$('gameover-overlay').classList.contains('hidden')) showGameOver();
    }
    if (session && $('screen-waiting').classList.contains('active')) showWaiting();
}

function init() {
    document.body.insertAdjacentHTML('afterbegin', PIECE_SVG.sprite);
    PIECE_SVG.setStyle(storageGet('chess_pieces') || 'classic');
    loadOptions();
    const savedBoard = storageGet('chess_board');
    document.documentElement.dataset.board = BOARD_THEMES.includes(savedBoard) ? savedBoard : 'classic';
    const howtoCopy = $('howto-list').cloneNode(true);
    howtoCopy.removeAttribute('id');
    $('howto-sheet-body').appendChild(howtoCopy);
    document.querySelectorAll('.logo-face').forEach(el => el.remove());
    $('logo-faces').innerHTML = ['n', 'K', 'q'].map((p, i) => `<span class="logo-face f${i + 1}">${PIECE_SVG.html(p)}</span>`).join('');

    initI18n();
    tipsOn = storageGet('chess_tips') !== '0';
    $('app-version').textContent = 'v' + APP_VERSION;

    $('btn-version').addEventListener('click', () => { renderReleaseNotes(); showModal('release-overlay', true); });
    $('btn-close-release').addEventListener('click', () => showModal('release-overlay', false));
    $('btn-howto').addEventListener('click', () => showModal('howto-overlay', true));
    $('btn-close-howto').addEventListener('click', () => showModal('howto-overlay', false));
    $('btn-step-1').addEventListener('click', () => goToStep(1));
    $('btn-lang-menu').addEventListener('click', () => showModal('lang-overlay', true));
    document.querySelectorAll('.lang-option').forEach(o => o.addEventListener('click', () => {
        setLanguage(o.dataset.lang);
        setTimeout(() => showModal('lang-overlay', false), 180);
    }));
    document.querySelectorAll('[data-open-rules]').forEach(b => b.addEventListener('click', () => {
        showModal('howto-overlay', false);
        openRules();
    }));
    $('btn-close-rules').addEventListener('click', () => showModal('rules-overlay', false));
    document.querySelectorAll('[data-open-style]').forEach(b => b.addEventListener('click', () => {
        renderStylePicker();
        renderBoardPicker();
        renderOptions();
        renderCustomPreview();
        showModal('style-overlay', true);
    }));
    $('btn-close-style').addEventListener('click', () => showModal('style-overlay', false));
    $('btn-chat').addEventListener('click', openChat);
    $('btn-chat-close').addEventListener('click', closeChat);
    $('chat-backdrop').addEventListener('click', closeChat);
    $('chat-form').addEventListener('submit', sendChat);
    $('style-grid').addEventListener('click', (e) => {
        const o = e.target.closest('.style-option');
        if (o) setPieceStyle(o.dataset.style);
    });
    document.querySelectorAll('[data-opt]').forEach(i => i.addEventListener('change', () => setOption(i.dataset.opt, i.checked)));
    document.querySelectorAll('[data-anim-opt]').forEach(b => b.addEventListener('click', () => setOption('anim', b.dataset.animOpt)));
    $('board-grid').addEventListener('click', (e) => {
        const o = e.target.closest('[data-board-theme]');
        if (o) setBoardTheme(o.dataset.boardTheme);
    });
    document.querySelectorAll('.mode-tab').forEach(b => b.addEventListener('click', () => selectTab(b.dataset.tab)));

    const nameInput = $('player-name');
    const codeInput = $('room-code');
    playerName = (storageGet('chess_name') || '').trim();
    nameInput.value = playerName;
    $('btn-continue').disabled = !playerName;
    $('local-opponent').value = storageGet('chess_opponent') || '';

    const urlCode = normalizeCode(new URLSearchParams(location.search).get('code') || '');
    if (urlCode) {
        codeInput.value = urlCode;
        $('btn-join').disabled = urlCode.length !== 6;
        $('card-join').classList.add('highlight');
    }

    nameInput.addEventListener('input', () => { $('btn-continue').disabled = nameInput.value.trim() === ''; });
    $('step-name').addEventListener('submit', (e) => {
        e.preventDefault();
        playerName = nameInput.value.trim().slice(0, 30);
        if (!playerName) return;
        storageSet('chess_name', playerName);
        goToStep(2);
    });
    $('btn-change-name').addEventListener('click', () => goToStep(1));
    codeInput.addEventListener('input', () => {
        const pos = codeInput.selectionStart;
        codeInput.value = normalizeCode(codeInput.value);
        codeInput.setSelectionRange(pos, pos);
        $('btn-join').disabled = codeInput.value.length !== 6;
    });
    $('btn-create').addEventListener('click', createRoom);
    $('card-join').addEventListener('submit', (e) => { e.preventDefault(); joinRoom(); });
    $('btn-random').addEventListener('click', findRandomMatch);
    $('btn-start-local').addEventListener('click', startLocal);
    $('btn-start-ai').addEventListener('click', startAi);

    $('btn-copy-code').addEventListener('click', () => copyText(session.code, t('copied')));
    $('btn-share-link').addEventListener('click', () => copyText(inviteLink(), t('link_copied')));
    $('btn-native-share').addEventListener('click', shareInvite);
    $('btn-leave-waiting').addEventListener('click', leaveGame);
    $('btn-leave-game').addEventListener('click', confirmLeave);

    const board = $('board');
    board.addEventListener('pointerdown', onBoardDown);
    board.addEventListener('pointermove', onBoardMove);
    board.addEventListener('pointerup', onBoardUp);
    board.addEventListener('pointercancel', () => { endDrag(); if (session) renderBoard(); });
    board.addEventListener('contextmenu', (e) => e.preventDefault());

    $('btn-tips').addEventListener('click', toggleTips);
    $('btn-hint').addEventListener('click', showHint);
    $('btn-undo').addEventListener('click', undoMove);
    $('btn-flip').addEventListener('click', flipBoard);
    $('btn-draw').addEventListener('click', offerDraw);
    $('btn-resign').addEventListener('click', resign);
    $('btn-new-game').addEventListener('click', () => (session.over && isOnline() ? showGameOver() : playAgain()));
    $('tip-card').addEventListener('click', () => $('tip-card').classList.toggle('expanded'));
    $('banner').addEventListener('click', (e) => {
        const answer = e.target.closest('[data-draw-answer]');
        if (answer) answerDraw(answer.dataset.drawAnswer === '1');
        if (e.target.closest('#btn-claim')) claimWin();
    });

    $('btn-confirm-no').addEventListener('click', () => showModal('confirm-overlay', false));
    $('btn-confirm-yes').addEventListener('click', () => {
        showModal('confirm-overlay', false);
        const action = confirmAction;
        confirmAction = null;
        if (action) action();
    });
    $('btn-rematch').addEventListener('click', playAgain);
    $('btn-review').addEventListener('click', () => showModal('gameover-overlay', false));
    $('btn-gameover-home').addEventListener('click', leaveGame);

    document.querySelectorAll('.modal').forEach(m => m.addEventListener('click', (e) => {
        if (e.target === m) showModal(m.id, false);
    }));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (document.body.classList.contains('chat-open')) closeChat();
            document.querySelectorAll('.modal:not(.hidden)').forEach(m => showModal(m.id, false));
            if (session) clearSelection();
        }
    });

    if ('ResizeObserver' in window) new ResizeObserver(() => fitBoard()).observe($('board-area'));
    else window.addEventListener('resize', fitBoard);
    setInterval(tickClocks, 200);

    onLanguageChange();
    goToStep(playerName ? 2 : 1);
    if (urlCode) selectTab('online');

    // An online game survives a page reload in the same tab.
    const savedToken = sessionGet('chess_token');
    if (savedToken && playerName) startOnline(savedToken);
}

document.addEventListener('DOMContentLoaded', init);
