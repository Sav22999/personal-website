// Connect Four / Forza Quattro main client controller.
const API_URL = 'api/index.php';
const POLL_MS = 1500;
const CLAIM_AFTER_S = 60;

const $ = (id) => document.getElementById(id);

// --- State Variables ---
let playerName = '';
let session = null;      // Active game session
let sessionSeq = 0;      // Counter for stale callback rejection
let pollTimer = null;
let clockInterval = null;
let aiWorker = null;
let aiRequestSeq = 0;    // Identifies the latest computer move request

// Audio context
let audioCtx = null;

// Customization preferences
const customPrefs = {
    palette: 'fuchsia_gold',
    discStyle: 'glossy',
    boardHue: 'plum',
    sound: true,
    guides: true,
    animSpeed: 'normal',
};

// Preset color pairs
const PALETTES = {
    fuchsia_gold: {
        p1: '#D946EF',
        p2: '#EAB308',
        p1G: 'radial-gradient(circle at 35% 30%, #FDF4FF 0%, #F472B6 28%, #D946EF 68%, #A21CAF 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #FEFCE8 0%, #FDE047 28%, #EAB308 68%, #CA8A04 100%)'
    },
    red_blue: {
        p1: '#EF4444',
        p2: '#2563EB',
        p1G: 'radial-gradient(circle at 35% 30%, #FFE4E6 0%, #F87171 28%, #EF4444 68%, #B91C1C 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #EFF6FF 0%, #60A5FA 28%, #2563EB 68%, #1D4ED8 100%)'
    },
    black_white: {
        p1: '#F8FAFC',
        p2: '#18181B',
        p1G: 'radial-gradient(circle at 35% 30%, #FFFFFF 0%, #F1F5F9 28%, #E2E8F0 68%, #94A3B8 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #71717A 0%, #3F3F46 28%, #18181B 68%, #09090B 100%)'
    },
    neon_cyan: {
        p1: '#FF007F',
        p2: '#06B6D4',
        p1G: 'radial-gradient(circle at 35% 30%, #FFF0F7 0%, #FF66B2 30%, #FF007F 70%, #B30059 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #ECFEFF 0%, #67E8F9 30%, #06B6D4 70%, #0891B2 100%)'
    },
    sunset_amber: {
        p1: '#F97316',
        p2: '#FBBF24',
        p1G: 'radial-gradient(circle at 35% 30%, #FFF7ED 0%, #FB923C 30%, #EA580C 70%, #9A3412 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #FEFCE8 0%, #FCD34D 30%, #F59E0B 70%, #B45309 100%)'
    },
    emerald_violet: {
        p1: '#10B981',
        p2: '#8B5CF6',
        p1G: 'radial-gradient(circle at 35% 30%, #ECFDF5 0%, #34D399 30%, #059669 70%, #047857 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #F5F3FF 0%, #A78BFA 30%, #7C3AED 70%, #5B21B6 100%)'
    },
    orange_cobalt: {
        p1: '#FF6B4A',
        p2: '#1D4ED8',
        p1G: 'radial-gradient(circle at 35% 30%, #FFF1EE 0%, #FF8A65 28%, #FF6B4A 68%, #C2410C 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #DBEAFE 0%, #60A5FA 28%, #1D4ED8 68%, #1E3A8A 100%)'
    },
    lime_pink: {
        p1: '#84CC16',
        p2: '#EC4899',
        p1G: 'radial-gradient(circle at 35% 30%, #F7FEE7 0%, #A3E635 28%, #84CC16 68%, #4D7C0F 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #FDF2F8 0%, #F472B6 28%, #EC4899 68%, #BE185D 100%)'
    },
    gold_silver: {
        p1: '#EAB308',
        p2: '#CBD5E1',
        p1G: 'radial-gradient(circle at 35% 30%, #FEFCE8 0%, #FDE047 28%, #EAB308 68%, #A16207 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #FFFFFF 0%, #E2E8F0 28%, #CBD5E1 68%, #64748B 100%)'
    },
    ruby_yellow: {
        p1: '#EF4444',
        p2: '#FACC15',
        p1G: 'radial-gradient(circle at 35% 30%, #FEF2F2 0%, #F87171 30%, #DC2626 70%, #991B1B 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #FEFCE8 0%, #FDE047 30%, #EAB308 70%, #A16207 100%)'
    },
    lilac_mint: {
        p1: '#A855F7',
        p2: '#14B8A6',
        p1G: 'radial-gradient(circle at 35% 30%, #FAF5FF 0%, #C084FC 30%, #9333EA 70%, #6B21A8 100%)',
        p2G: 'radial-gradient(circle at 35% 30%, #F0FDFA 0%, #5EEAD4 30%, #0D9488 70%, #115E59 100%)'
    },
};

// --- Storage Helpers ---
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

// The language in its own words: italiano, English, español…
function languageName() {
    try {
        return new Intl.DisplayNames([currentLang], {type: 'language'}).of(currentLang);
    } catch (e) {
        return currentLang.toUpperCase();
    }
}

const formatPoints = (points2) => Math.floor(points2 / 2) + (points2 % 2 ? '½' : '');

// --- Web Audio Synthesizer ---
function initAudio() {
    if (!audioCtx) {
        const AudioClass = window.AudioContext || window.webkitAudioContext;
        if (AudioClass) audioCtx = new AudioClass();
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
}

function playDropSound(row = 5) {
    if (!customPrefs.sound) return;
    try {
        initAudio();
        if (!audioCtx) return;
        const now = audioCtx.currentTime;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();

        // Higher pitch for top rows, deeper resonant pitch for bottom rows
        const baseFreq = 360 - (row * 35);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(baseFreq + 140, now);
        osc.frequency.exponentialRampToValueAtTime(baseFreq, now + 0.08);

        gain.gain.setValueAtTime(0.35, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.13);
    } catch (e) {
    }
}

function playWinSound() {
    if (!customPrefs.sound) return;
    try {
        initAudio();
        if (!audioCtx) return;
        const notes = [440, 554, 659, 880]; // A major chord fanfare
        notes.forEach((freq, idx) => {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            const start = audioCtx.currentTime + idx * 0.1;
            osc.frequency.setValueAtTime(freq, start);
            gain.gain.setValueAtTime(0.25, start);
            gain.gain.exponentialRampToValueAtTime(0.001, start + 0.35);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start(start);
            osc.stop(start + 0.36);
        });
    } catch (e) {
    }
}

// --- UI Helpers ---
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
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add('visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('visible'), 2600);
}

function showError(code) {
    const key = 'err_' + code;
    showToast(t(key) !== key ? t(key) : t('err_generic'));
}

// Closes the topmost open sheet; the result of the game stays open until the player chooses
function closeTopModal() {
    const open = [...document.querySelectorAll('.modal:not(.hidden)')].filter(m => m.id !== 'gameover-overlay').pop();
    if (!open) return false;
    showModal(open.id, false);
    return true;
}

async function copyText(text, okMessage) {
    try {
        await navigator.clipboard.writeText(text);
    } catch (e) {
        // Not available everywhere (older WebViews, plain http)
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

function applyCustomPrefs() {
    document.body.setAttribute('data-board', customPrefs.boardHue);
    document.body.setAttribute('data-disc-style', customPrefs.discStyle);
    document.body.setAttribute('data-palette', customPrefs.palette);
    const p = PALETTES[customPrefs.palette] || PALETTES.fuchsia_gold;
    document.documentElement.style.setProperty('--disc-p1-grad', p.p1G);
    document.documentElement.style.setProperty('--disc-p1-color', p.p1);
    document.documentElement.style.setProperty('--disc-p2-grad', p.p2G);
    document.documentElement.style.setProperty('--disc-p2-color', p.p2);

    const boardWrap = $('board-wrap');
    if (boardWrap) {
        boardWrap.classList.toggle('anim-fast', customPrefs.animSpeed === 'fast');
        boardWrap.classList.toggle('anim-instant', customPrefs.animSpeed === 'instant');
    }

    // Refresh active disc styles on board if present
    document.querySelectorAll('.c4-disc').forEach(disc => {
        disc.className = disc.className.replace(/\bstyle-\w+/g, '').trim() + ' style-' + customPrefs.discStyle;
    });

    // Update audio toggle icon appearance
    const audioBtn = $('btn-audio-toggle');
    if (audioBtn) {
        audioBtn.style.opacity = customPrefs.sound ? '1' : '0.4';
    }

    updateCustomPreview();
    renderLogoFaces();
    if (session) updatePlayerBars();
}

// --- API Client ---
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

// --- Board DOM Generation ---
function buildBoardDOM() {
    const boardEl = $('c4-board');
    const guidesEl = $('column-guides');
    if (!boardEl || !guidesEl) return;

    boardEl.innerHTML = '';
    guidesEl.innerHTML = '';

    for (let c = 0; c < ConnectFour.COLS; c++) {
        // Guide indicator above column
        const guide = document.createElement('div');
        guide.className = 'guide-indicator';
        guide.dataset.col = c;
        guide.innerHTML = `<svg class="guide-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>`;
        guidesEl.appendChild(guide);

        // Column in board
        const colEl = document.createElement('div');
        colEl.className = 'c4-column';
        colEl.dataset.col = c;
        colEl.tabIndex = 0;
        colEl.setAttribute('role', 'button');
        colEl.setAttribute('aria-label', t('col_label', {col: c + 1}));

        for (let r = 0; r < ConnectFour.ROWS; r++) {
            const cell = document.createElement('div');
            cell.className = 'c4-cell';
            cell.dataset.row = r;
            cell.dataset.col = c;
            colEl.appendChild(cell);
        }

        colEl.addEventListener('mouseenter', () => onColumnHover(c, true));
        colEl.addEventListener('mouseleave', () => onColumnHover(c, false));
        colEl.addEventListener('click', () => onColumnClick(c));
        colEl.addEventListener('keydown', (e) => onColumnKey(e, c));

        boardEl.appendChild(colEl);
    }
}

function onColumnHover(col, active) {
    const guide = document.querySelector(`.guide-indicator[data-col="${col}"]`);
    if (!guide) return;
    guide.classList.toggle('active', active && customPrefs.guides && isMyTurn());
}

function clearGuides() {
    document.querySelectorAll('.guide-indicator.active').forEach(g => g.classList.remove('active'));
}

// Enter or Space drops a disc, the arrows move between the columns
function onColumnKey(e, col) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        onColumnClick(col);
    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
        e.preventDefault();
        const next = document.querySelector(`.c4-column[data-col="${col + (e.key === 'ArrowLeft' ? -1 : 1)}"]`);
        if (next) next.focus();
    }
}

function isMyTurn() {
    if (!session || session.status !== 'playing') return false;
    if (session.mode === 'local') return true;
    if (session.mode === 'ai') return session.turnSlot === session.playerSlot;
    if (session.mode === 'online') return session.slotToMove === session.mySlot && !session.moveInFlight;
    return false;
}

// --- Column Drop Action ---
function onColumnClick(col) {
    if (!session || session.status !== 'playing' || !isMyTurn()) return;

    const row = ConnectFour.getLowestEmptyRow(session.board, col);
    if (row === -1) {
        showToast(t('err_illegal_move'));
        return;
    }

    if (session.mode === 'local') {
        placeDisc(col, row, session.turnSlot);
    } else if (session.mode === 'ai') {
        executePlayerMoveVsAI(col, row);
    } else if (session.mode === 'online') {
        executeOnlineMove(col);
    }
}

function renderDisc(row, col, player, isNew = false) {
    const cell = document.querySelector(`.c4-cell[data-row="${row}"][data-col="${col}"]`);
    if (!cell) return;

    let disc = cell.querySelector('.c4-disc');
    if (!disc) {
        disc = document.createElement('div');
        disc.className = `c4-disc p${player} style-${customPrefs.discStyle}`;
        if (isNew && customPrefs.animSpeed !== 'instant') {
            disc.classList.add(`anim-drop-${row}`);
        }
        cell.appendChild(disc);
    }
}

function clearBoardDOM() {
    document.querySelectorAll('.c4-cell').forEach(c => c.innerHTML = '');
    document.querySelectorAll('.c4-column').forEach(c => c.classList.remove('full'));
    const svg = $('c4-winning-line');
    if (svg) svg.innerHTML = '';
}

function updateFullColumnsUI() {
    if (!session) return;
    for (let c = 0; c < ConnectFour.COLS; c++) {
        const full = session.board[0][c] !== 0;
        const colEl = document.querySelector(`.c4-column[data-col="${c}"]`);
        if (colEl) colEl.classList.toggle('full', full);
    }
}

function drawWinningLine(line) {
    if (!line || line.length < 4) return;
    const svg = $('c4-winning-line');
    if (!svg) return;

    // Highlight winning discs
    line.forEach(({r, c}) => {
        const disc = document.querySelector(`.c4-cell[data-row="${r}"][data-col="${c}"] .c4-disc`);
        if (disc) disc.classList.add('winning');
    });

    // Measured on the real cells, so the line stays centred on the discs at any size
    const wrap = $('board-wrap').getBoundingClientRect();
    const scale = wrap.width / 700;
    const dots = line.map(({r, c}) => {
        const box = document.querySelector(`.c4-cell[data-row="${r}"][data-col="${c}"]`).getBoundingClientRect();
        return {x: box.left + box.width / 2 - wrap.left, y: box.top + box.height / 2 - wrap.top};
    });
    const first = dots[0];
    const last = dots[dots.length - 1];

    svg.setAttribute('viewBox', `0 0 ${wrap.width} ${wrap.height}`);
    svg.setAttribute('preserveAspectRatio', 'none');
    svg.innerHTML = `
        <line x1="${first.x}" y1="${first.y}" x2="${last.x}" y2="${last.y}" stroke="#FFFFFF" stroke-width="${12 * scale}" stroke-linecap="round" opacity="0.95" style="filter: drop-shadow(0 0 ${10 * scale}px var(--accent));"/>
        ${dots.map(d => `<circle cx="${d.x}" cy="${d.y}" r="${11 * scale}" fill="#FFFFFF"/>`).join('')}
    `;
}

// --- Moves History List ---
function addMoveToHistory(ply, col, player) {
    const list = $('move-list');
    if (!list) return;
    const item = document.createElement('li');
    item.className = 'move-item';
    item.innerHTML = `<span class="move-num">#${ply + 1}</span> <span>${t('col_label', {col: col + 1})}</span> <span class="palette-dot" style="width:12px;height:12px;background:var(--disc-p${player}-color);"></span>`;
    list.appendChild(item);
    list.scrollTop = list.scrollHeight;
}

function clearMoveHistory() {
    const list = $('move-list');
    if (list) list.innerHTML = '';
}

// --- Turn & Header Indicators ---
function updateTurnIndicator() {
    const ind = $('turn-indicator');
    if (!ind || !session) return;

    if (session.status !== 'playing') {
        ind.textContent = t('game_over');
        return;
    }

    const currentSlot = session.mode === 'online' ? session.slotToMove : session.turnSlot;
    const name = currentSlot === 1 ? session.p1Name : session.p2Name;
    const dot = `<span class="turn-disc-dot" style="background:var(--disc-p${currentSlot}-color); color:var(--disc-p${currentSlot}-color);"></span>`;

    if (session.mode === 'ai' && currentSlot !== session.playerSlot) {
        ind.innerHTML = `<span class="online-dot" style="background:var(--accent);"></span> <span>${escapeHtml(t('computer_thinking'))}</span>`;
    } else if (session.mode !== 'local' && currentSlot === session.mySlot) {
        ind.innerHTML = `${dot} <span>${escapeHtml(t('your_turn'))}</span>`;
    } else {
        ind.innerHTML = `${dot} <span>${escapeHtml(t('their_turn', {name}))}</span>`;
    }

    // Toggle active player bar
    const barTop = $('bar-top');
    const barBottom = $('bar-bottom');
    if (barTop && barBottom) {
        const topSlot = session.mySlot === 2 ? 1 : 2;
        const bottomSlot = session.mySlot === 2 ? 2 : 1;
        barTop.classList.toggle('active-turn', currentSlot === topSlot);
        barBottom.classList.toggle('active-turn', currentSlot === bottomSlot);
    }
    renderActions();
}

// Shows only the actions that make sense right now
function renderActions() {
    if (!session) return;
    const over = session.status === 'finished';
    const online = session.mode === 'online';
    const myTurn = isMyTurn();
    const undoable = session.moves.length >= (session.mode === 'ai' ? 2 : 1);

    $('btn-hint').classList.toggle('hidden', online || over);
    $('btn-undo').classList.toggle('hidden', online || over);
    $('btn-draw').classList.toggle('hidden', !online || over);
    $('btn-resign').classList.toggle('hidden', over);
    $('btn-result').classList.toggle('hidden', !over);
    $('btn-chat-toggle').classList.toggle('hidden', !online);
    $('btn-moves-toggle').classList.toggle('hidden', online);

    $('btn-hint').disabled = !myTurn;
    $('btn-undo').disabled = !myTurn || !undoable;
    $('btn-draw').disabled = online && session.drawOffer === session.mySlot;
}

// ==================== SHARED MOVE LOGIC ====================
// Drops a disc in a game played on this device. Returns true when that move ended the game.
function placeDisc(col, row, player) {
    session.board[row][col] = player;
    session.moves.push(col);

    renderDisc(row, col, player, true);
    playDropSound(row);
    addMoveToHistory(session.moves.length - 1, col, player);
    updateFullColumnsUI();
    clearGuides();

    const winLine = ConnectFour.checkWinAt(session.board, row, col);
    if (winLine) {
        endGame(player, 'connect4', winLine);
        return true;
    }
    if (ConnectFour.isBoardFull(session.board)) {
        endGame(0, 'draw_board');
        return true;
    }

    session.turnSlot = player === 1 ? 2 : 1;
    session.turnStartedMs = Date.now();
    updateTurnIndicator();
    return false;
}

// ==================== LOCAL 2-PLAYER MODE ====================
function startLocalGame() {
    initAudio();
    const p1 = playerName || t('player_one');
    const p2Input = $('local-opponent').value.trim();
    const p2 = p2Input !== '' ? p2Input : t('player_two');
    const tcRadio = document.querySelector('input[name="time-local"]:checked');
    const tc = tcRadio ? parseInt(tcRadio.value, 10) * 60 : 0;

    sessionSeq++;
    session = {
        mode: 'local',
        seq: sessionSeq,
        status: 'playing',
        p1Name: p1,
        p2Name: p2,
        mySlot: 1,
        firstSlot: 1,
        turnSlot: 1,
        moves: [],
        board: ConnectFour.createBoard(),
        p1Points: 0,
        p2Points: 0,
        timeControl: tc,
        p1Ms: tc * 1000,
        p2Ms: tc * 1000,
        turnStartedMs: Date.now(),
    };

    setupGameScreenUI();
    showScreen('game');
    startClock();
}

// ==================== COMPUTER AI MODE ====================
function startAIGame() {
    initAudio();
    const p1 = playerName || t('player_one');
    const lvlRadio = document.querySelector('input[name="ai-level"]:checked');
    const level = lvlRadio ? lvlRadio.value : 'medium';
    const colorRadio = document.querySelector('input[name="ai-color"]:checked');
    const colorPref = colorRadio ? colorRadio.value : '1';

    let playerSlot = 1;
    if (colorPref === '2') playerSlot = 2;
    else if (colorPref === 'r') playerSlot = Math.random() < 0.5 ? 1 : 2;

    const tcRadio = document.querySelector('input[name="time-ai"]:checked');
    const tc = tcRadio ? parseInt(tcRadio.value, 10) * 60 : 0;

    sessionSeq++;
    session = {
        mode: 'ai',
        seq: sessionSeq,
        status: 'playing',
        p1Name: playerSlot === 1 ? p1 : t('computer'),
        p2Name: playerSlot === 2 ? p1 : t('computer'),
        mySlot: playerSlot,
        playerSlot,
        aiLevel: level,
        firstSlot: 1,
        turnSlot: 1,
        moves: [],
        board: ConnectFour.createBoard(),
        p1Points: 0,
        p2Points: 0,
        timeControl: tc,
        p1Ms: tc * 1000,
        p2Ms: tc * 1000,
        turnStartedMs: Date.now(),
    };

    setupGameScreenUI();
    showScreen('game');
    startClock();

    if (playerSlot === 2) {
        triggerAIMove();
    }
}

function executePlayerMoveVsAI(col, row) {
    if (!placeDisc(col, row, session.playerSlot)) triggerAIMove();
}

function getAIWorker() {
    if (!aiWorker && typeof Worker !== 'undefined') {
        try {
            aiWorker = new Worker('ai-worker.js?v=' + document.body.dataset.v);
        } catch (err) {
            aiWorker = null;
        }
    }
    return aiWorker;
}

// The search runs in a worker so the page stays smooth; without one it runs here
function computeMove(board, player, level, onDone) {
    const id = ++aiRequestSeq;
    const here = () => onDone(ConnectFourAI.bestMove(board, player, level));
    const worker = getAIWorker();
    if (!worker) {
        here();
        return;
    }
    worker.onmessage = (e) => {
        if (e.data.id === id) onDone(e.data.col);
    };
    worker.onerror = () => {
        aiWorker = null;
        here();
    };
    worker.postMessage({id, board, player, level});
}

function triggerAIMove() {
    if (!session || session.status !== 'playing') return;
    const s = session;
    const aiSlot = s.playerSlot === 1 ? 2 : 1;
    const request = ++aiRequestSeq;
    s.aiRequest = request;
    const current = () => session === s && s.aiRequest === request && s.status === 'playing';

    setTimeout(() => {
        if (!current()) return;
        computeMove(s.board, aiSlot, s.aiLevel, (col) => {
            if (current()) executeAIMove(col, aiSlot);
        });
    }, 320);
}

function executeAIMove(col, aiSlot) {
    const row = ConnectFour.getLowestEmptyRow(session.board, col);
    if (row !== -1) placeDisc(col, row, aiSlot);
}

// ==================== ONLINE MULTIPLAYER ====================
async function createOnlineGame() {
    initAudio();
    const btn = $('btn-create');
    setBusy(btn, true);

    const tcRadio = document.querySelector('input[name="time-online"]:checked');
    const tc = tcRadio ? parseInt(tcRadio.value, 10) * 60 : 0;

    const res = await api('create_room', {
        name: playerName || t('player_one'),
        disc_color: customPrefs.palette,
        time: tc,
    });
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

    const res = await api('join_room', {
        code,
        name: playerName || t('player_two'),
        disc_color: customPrefs.palette,
    });
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

    const res = await api('find_match', {
        name: playerName || t('player_one'),
        disc_color: customPrefs.palette,
        lang: currentLang,
    });
    setBusy(btn, false);

    if (res.error) {
        showError(res.error);
        return;
    }

    setupOnlineSession('', res.token, res.matched ? 2 : 1, true);
}

function setupOnlineSession(code, token, slot, isPublic) {
    sessionSeq++;
    session = {
        mode: 'online',
        seq: sessionSeq,
        status: 'waiting',
        code,
        token,
        mySlot: slot,
        isPublic,
        firstSlot: 1,
        slotToMove: 1,
        moves: [],
        board: ConnectFour.createBoard(),
        p1Name: slot === 1 ? playerName : t('player_one'),
        p2Name: slot === 2 ? playerName : t('player_two'),
        p1Points: 0,
        p2Points: 0,
        timeControl: 0,
        p1Ms: 0,
        p2Ms: 0,
        turnStartedMs: Date.now(),
        lastMessageId: 0,
        players: [],
        drawOffer: null,
        rematchSent: false,
    };
    sessionSet('c4_session', JSON.stringify({code, token, slot, isPublic}));
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
    const res = await api('poll', {
        token: s.token,
        since: s.lastMessageId || 0,
    });
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
    s.firstSlot = data.firstSlot;
    s.mySlot = data.mySlot; // Two players searching at once can be moved to the other's room
    s.timeControl = data.timeControl;
    s.p1Ms = data.p1Ms;
    s.p2Ms = data.p2Ms;
    s.turnStartedMs = Date.now();
    s.players = data.players;
    s.drawOffer = data.drawOffer;

    const p1 = data.players.find(p => p.slot === 1);
    const p2 = data.players.find(p => p.slot === 2);
    if (p1) {
        s.p1Name = p1.name;
        s.p1Points = p1.points2;
    }
    if (p2) {
        s.p2Name = p2.name;
        s.p2Points = p2.points2;
    }

    // Both players accepted the rematch: a new round starts from an empty board
    if (s.round && data.round !== s.round) {
        s.status = 'waiting';
        s.moves = [];
        s.board = ConnectFour.createBoard();
        s.rematchSent = false;
        showModal('gameover-overlay', false);
    }
    s.round = data.round;

    if (s.status === 'waiting' && data.status !== 'waiting') {
        s.status = 'playing';
        setupGameScreenUI();
        showScreen('game');
        startClock();
    }

    // Moves sync (a reload brings many at once: they appear without the drop animation)
    const serverMoves = data.moves || [];
    const missing = serverMoves.length - s.moves.length;
    if (missing > 0) {
        for (let i = s.moves.length; i < serverMoves.length; i++) {
            const col = parseInt(serverMoves[i], 10);
            const player = (i % 2 === 0) ? s.firstSlot : (s.firstSlot === 1 ? 2 : 1);
            const row = ConnectFour.getLowestEmptyRow(s.board, col);
            if (row === -1) continue;
            s.board[row][col] = player;
            renderDisc(row, col, player, missing === 1);
            if (missing === 1) playDropSound(row);
            addMoveToHistory(i, col, player);
        }
        s.moves = serverMoves;
        updateFullColumnsUI();
        clearGuides();
    }

    s.slotToMove = (s.moves.length % 2 === 0) ? s.firstSlot : (s.firstSlot === 1 ? 2 : 1);

    if (data.status === 'finished' && s.status !== 'finished') {
        const winLine = data.winningLine ? data.winningLine.split('-').map(part => {
            const [r, c] = part.split(',').map(Number);
            return {r, c};
        }) : null;
        const winner = data.result === '1-0' ? 1 : (data.result === '0-1' ? 2 : 0);
        endGame(winner, data.reason, winLine);
    } else if (s.status !== 'waiting') {
        updateTurnIndicator();
    }

    if (s.status !== 'waiting') {
        updatePlayerBars();
        updateClockDisplay();
        renderBanner();
        renderRematchStatus();
    }
    addChatMessages(data.messages || []);
}

// Draw offers and an opponent who lost the connection
function renderBanner() {
    const banner = $('banner');
    const s = session;
    let html = '';
    let warn = false;

    if (s && s.mode === 'online' && s.status === 'playing') {
        const opp = s.players.find(p => p.slot !== s.mySlot);
        if (s.drawOffer !== null && s.drawOffer !== s.mySlot) {
            html = `<span>${escapeHtml(t('draw_offer_received', {name: opp ? opp.name : ''}))}</span>
                <button type="button" class="btn btn-primary" data-draw-answer="1">${escapeHtml(t('accept'))}</button>
                <button type="button" class="btn btn-secondary" data-draw-answer="0">${escapeHtml(t('decline'))}</button>`;
        } else if (opp && !opp.online) {
            warn = true;
            html = `<span>${escapeHtml(t('opponent_offline', {sec: Math.min(opp.idle, CLAIM_AFTER_S)}))}</span>`;
            if (opp.idle >= CLAIM_AFTER_S || opp.left) {
                html += `<button type="button" class="btn btn-accent" data-claim-win>${escapeHtml(t('claim_win'))}</button>`;
            }
        }
    }

    if (banner.dataset.html === html) return; // Buttons stay clickable between polls
    banner.dataset.html = html;
    banner.innerHTML = html;
    banner.classList.toggle('warn', warn);
    banner.classList.toggle('hidden', !html);
}

async function answerDraw(accept) {
    const s = session;
    const res = await api('answer_draw', {token: s.token, accept: accept ? 1 : 0});
    if (res.error) showError(res.error);
    pollServer();
}

async function claimWin() {
    const res = await api('claim_win', {token: session.token});
    if (res.error) showError(res.error);
    pollServer();
}

async function executeOnlineMove(col) {
    const s = session;
    s.moveInFlight = true; // A double tap must not send the same turn twice
    clearGuides();
    const res = await api('move', {
        token: s.token,
        col,
        ply: s.moves.length,
    });
    s.moveInFlight = false;
    if (session !== s) return;
    if (res.error) showError(res.error);
    pollServer();
}

// --- Clocks ---
function startClock() {
    clearInterval(clockInterval);
    if (!session || session.timeControl <= 0) return;
    session.turnStartedMs = Date.now();

    clockInterval = setInterval(() => {
        if (!session || session.status !== 'playing') {
            clearInterval(clockInterval);
            return;
        }

        const now = Date.now();
        const elapsed = now - session.turnStartedMs;
        session.turnStartedMs = now;

        const currentSlot = session.mode === 'online' ? session.slotToMove : session.turnSlot;
        if (currentSlot === 1) session.p1Ms = Math.max(0, session.p1Ms - elapsed);
        else session.p2Ms = Math.max(0, session.p2Ms - elapsed);

        updateClockDisplay();

        if (session.mode !== 'online') {
            if (session.p1Ms <= 0) endGame(2, 'timeout');
            else if (session.p2Ms <= 0) endGame(1, 'timeout');
        }
    }, 250);
}

function updateClockDisplay() {
    const fmt = (ms) => {
        const s = Math.ceil(ms / 1000);
        const m = Math.floor(s / 60);
        const remS = s % 60;
        return `${m}:${remS < 10 ? '0' : ''}${remS}`;
    };

    const topSlot = session.mySlot === 2 ? 1 : 2;
    const bottomSlot = session.mySlot === 2 ? 2 : 1;

    const clockTop = $('clock-top');
    const clockBottom = $('clock-bottom');

    if (clockTop && clockBottom) {
        if (session.timeControl > 0) {
            clockTop.textContent = fmt(topSlot === 1 ? session.p1Ms : session.p2Ms);
            clockBottom.textContent = fmt(bottomSlot === 1 ? session.p1Ms : session.p2Ms);
            clockTop.classList.toggle('low-time', (topSlot === 1 ? session.p1Ms : session.p2Ms) < 30000);
            clockBottom.classList.toggle('low-time', (bottomSlot === 1 ? session.p1Ms : session.p2Ms) < 30000);
            clockTop.classList.remove('hidden');
            clockBottom.classList.remove('hidden');
        } else {
            clockTop.classList.add('hidden');
            clockBottom.classList.add('hidden');
        }
    }
}

// --- Game UI Setup ---
function setupGameScreenUI() {
    clearBoardDOM();
    clearMoveHistory();
    buildBoardDOM();
    applyCustomPrefs();

    $('mode-badge').textContent = t('tab_' + session.mode);

    updatePlayerBars();

    // Controls visibility based on mode
    const isOnline = session.mode === 'online';
    $('chat').classList.toggle('hidden', !isOnline);
    $('btn-chat-toggle').classList.remove('has-unread');
    closePanel();
    renderBanner();

    updateTurnIndicator();
    updateClockDisplay();
}

function updatePlayerBars() {
    const topSlot = session.mySlot === 2 ? 1 : 2;
    const bottomSlot = session.mySlot === 2 ? 2 : 1;
    const isFlat = customPrefs.discStyle === 'flat';

    [['top', topSlot], ['bottom', bottomSlot]].forEach(([side, slot]) => {
        $('name-' + side).textContent = slot === 1 ? session.p1Name : session.p2Name;
        $('avatar-' + side).style.background = `var(--disc-p${slot}-${isFlat ? 'color' : 'grad'})`;
        $('score-' + side).textContent = formatPoints(slot === 1 ? session.p1Points : session.p2Points) + ' pt';

        // Connection dot only where there is a connection to show
        const dot = $('dot-online-' + side);
        const player = session.mode === 'online' ? (session.players || []).find(p => p.slot === slot) : null;
        dot.classList.toggle('hidden', !player);
        dot.classList.toggle('offline', !!player && !player.online);
    });
}

// --- End Game Flow ---
const GAMEOVER_ICONS = {
    win: `<svg viewBox="0 0 60 60" width="56" height="56"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--green)" stroke-width="3"/><path d="M18 30l8 8 16-16" fill="none" stroke="var(--green)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    lose: `<svg viewBox="0 0 60 60" width="56" height="56"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--red)" stroke-width="3"/><path d="M20 20l20 20M40 20l-20 20" fill="none" stroke="var(--red)" stroke-width="3" stroke-linecap="round"/></svg>`,
    draw: `<svg viewBox="0 0 60 60" width="56" height="56"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--yellow)" stroke-width="3"/><path d="M19 25h22M19 35h22" fill="none" stroke="var(--yellow)" stroke-width="3" stroke-linecap="round"/></svg>`,
    trophy: `<svg viewBox="0 0 60 60" width="56" height="56" fill="none" stroke="var(--yellow)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10h20v14a10 10 0 01-20 0z"/><path d="M20 14h-7a6 6 0 006 8M40 14h7a6 6 0 01-6 8M30 34v8M22 50h16M24 42h12v8H24z"/></svg>`,
};

function endGame(winner, reason, winLine = null) {
    const s = session;
    if (!s || s.status === 'finished') return;
    s.status = 'finished';
    clearInterval(clockInterval);

    if (winLine) {
        drawWinningLine(winLine);
        playWinSound();
    }

    const titleEl = $('gameover-title');
    const textEl = $('gameover-text');
    const iconEl = $('gameover-icon');

    const p1 = s.p1Name;
    const p2 = s.p2Name;
    const winName = winner === 1 ? p1 : p2;
    const isMe = s.mode !== 'local' && winner === s.mySlot;

    if (winner === 0) {
        titleEl.textContent = t('game_draw');
        textEl.textContent = t('reason_' + reason);
        iconEl.innerHTML = GAMEOVER_ICONS.draw;
    } else {
        if (s.mode === 'local') {
            titleEl.textContent = t('won_the_game', {name: winName});
            iconEl.innerHTML = GAMEOVER_ICONS.trophy;
        } else if (isMe) {
            titleEl.textContent = t('you_won');
            iconEl.innerHTML = GAMEOVER_ICONS.win;
        } else {
            titleEl.textContent = t('you_lost');
            iconEl.innerHTML = GAMEOVER_ICONS.lose;
        }
        textEl.textContent = t('reason_' + reason, {name: winner === 1 ? p2 : p1});
    }

    // Online the server keeps the score
    if (s.mode !== 'online') {
        if (winner === 0) {
            s.p1Points += 1;
            s.p2Points += 1;
        } else if (winner === 1) s.p1Points += 2;
        else s.p2Points += 2;
    }

    $('score-line').textContent = t('score_tracker', {
        p1,
        s1: formatPoints(s.p1Points),
        p2,
        s2: formatPoints(s.p2Points)
    });
    $('btn-rematch').disabled = false;
    $('rematch-status').textContent = '';

    const ind = $('turn-indicator');
    if (ind) {
        if (winner === 0) ind.textContent = t('game_draw');
        else if (s.mode === 'local') ind.textContent = t('won_the_game', {name: winName});
        else ind.textContent = isMe ? t('you_won') : t('you_lost');
    }
    $('bar-top').classList.remove('active-turn');
    $('bar-bottom').classList.remove('active-turn');

    updatePlayerBars();
    renderActions();
    renderBanner();
    clearGuides();

    setTimeout(() => {
        if (session === s && s.status === 'finished') showModal('gameover-overlay', true);
    }, winLine ? 1000 : 300);
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

// Rematch click
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
        pollServer(); // The result stays open until the opponent answers
        return;
    }

    // Swap who starts
    s.aiRequest = 0;
    s.firstSlot = s.firstSlot === 1 ? 2 : 1;
    s.turnSlot = s.firstSlot;
    s.moves = [];
    s.board = ConnectFour.createBoard();
    s.status = 'playing';
    if (s.timeControl > 0) {
        s.p1Ms = s.timeControl * 1000;
        s.p2Ms = s.timeControl * 1000;
    }

    showModal('gameover-overlay', false);
    setupGameScreenUI();
    startClock();

    if (s.mode === 'ai' && s.firstSlot !== s.playerSlot) {
        triggerAIMove();
    }
}

// Leaves the current game (the server is told, so the opponent is not left waiting)
async function leaveGame() {
    const s = session;
    resetToLobby();
    if (s && s.mode === 'online' && s.token) await api('leave', {token: s.token});
}

function resetToLobby() {
    clearInterval(pollTimer);
    clearInterval(clockInterval);
    session = null;
    sessionSet('c4_session', null);
    document.querySelectorAll('.modal').forEach(m => m.classList.add('hidden'));
    closePanel();
    renderBanner();
    showScreen('lobby');
}

function requestLeave() {
    if (!session) return;
    const started = session.status === 'playing' && (session.mode === 'online' || session.moves.length > 0);
    if (started) openConfirm(t('confirm_leave_title'), t('confirm_leave_text'), t('leave'), leaveGame);
    else leaveGame();
}

// Side panel: on phones it slides up from the action bar
function togglePanel() {
    const open = !$('side-panel').classList.contains('open');
    $('side-panel').classList.toggle('open', open);
    $('chat-backdrop').classList.toggle('open', open);
    if (open) $('btn-chat-toggle').classList.remove('has-unread');
}

function closePanel() {
    $('side-panel').classList.remove('open');
    $('chat-backdrop').classList.remove('open');
}

// Android back key (see MainActivity): true means the page used it
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

// Hint & Undo
function triggerHint() {
    if (!session || !isMyTurn() || session.mode === 'online') return;
    const s = session;
    const ply = s.moves.length;
    const currentSlot = s.mode === 'ai' ? s.playerSlot : s.turnSlot;
    $('btn-hint').disabled = true;

    computeMove(s.board, currentSlot, 'hard', (bestCol) => {
        if (session !== s || s.moves.length !== ply || s.status !== 'playing') return;
        renderActions();
        const guide = document.querySelector(`.guide-indicator[data-col="${bestCol}"]`);
        if (!guide) return;
        guide.classList.add('active');
        showToast(t('hint') + ': ' + t('col_label', {col: bestCol + 1}));
        setTimeout(() => guide.classList.remove('active'), 2500);
    });
}

function triggerUndo() {
    const s = session;
    if (!s || s.mode === 'online' || !isMyTurn()) return;

    // Against the computer its reply goes too, so it is your turn again
    const countToPop = s.mode === 'ai' ? 2 : 1;
    if (s.moves.length < countToPop) return;
    s.moves.length -= countToPop;

    s.aiRequest = 0;
    s.board = ConnectFour.createBoard();
    clearBoardDOM();
    clearMoveHistory();

    for (let ply = 0; ply < s.moves.length; ply++) {
        const col = s.moves[ply];
        const p = (ply % 2 === 0) ? s.firstSlot : (s.firstSlot === 1 ? 2 : 1);
        const r = ConnectFour.getLowestEmptyRow(s.board, col);
        s.board[r][col] = p;
        renderDisc(r, col, p, false);
        addMoveToHistory(ply, col, p);
    }

    s.turnSlot = (s.moves.length % 2 === 0) ? s.firstSlot : (s.firstSlot === 1 ? 2 : 1);
    s.turnStartedMs = Date.now();
    updateFullColumnsUI();
    updateTurnIndicator();
}

// Chat UI
function appendChatMessage(type, text) {
    const list = $('chat-list');
    if (!list) return;
    const li = document.createElement('li');
    li.className = `chat-msg ${type}`;
    li.textContent = text;
    list.appendChild(li);
    list.scrollTop = list.scrollHeight;
}

// Messages arrive only from the poll, in order: nothing is shown twice or skipped
function addChatMessages(list) {
    const s = session;
    list.forEach(msg => {
        if (msg.id <= s.lastMessageId) return;
        s.lastMessageId = msg.id;
        const mine = msg.slot === s.mySlot;
        appendChatMessage(mine ? 'me' : 'them', msg.text);
        if (!mine && !$('side-panel').classList.contains('open')) $('btn-chat-toggle').classList.add('has-unread');
    });
}

function setBusy(btn, busy) {
    if (!btn) return;
    btn.disabled = busy;
    btn.classList.toggle('loading', busy);
}

// --- DOM Event Listeners & Bootstrapping ---
document.addEventListener('DOMContentLoaded', () => {
    // 1. Language Initialization
    const initialLang = getInitialLang();
    setLanguage(initialLang);

    // 2. Pre-build board DOM so it renders cleanly even before game starts
    buildBoardDOM();
    renderLogoFaces();

    // 3. Load stored preferences & name
    const storedPrefs = storageGet('c4_prefs');
    if (storedPrefs) {
        try {
            Object.assign(customPrefs, JSON.parse(storedPrefs));
        } catch (e) {
        }
    }
    applyCustomPrefs();

    const storedName = storageGet('c4_name');
    if (storedName) {
        playerName = storedName;
        $('player-name').value = playerName;
        $('btn-continue').disabled = false;
        setLobbyStep(2);
    } else {
        setLobbyStep(1);
    }

    // 4. Name form & Step navigation
    $('btn-step-1').addEventListener('click', () => setLobbyStep(1));

    $('player-name').addEventListener('input', (e) => {
        const val = e.target.value.trim();
        $('btn-continue').disabled = val === '';
    });

    $('step-name').addEventListener('submit', (e) => {
        e.preventDefault();
        const val = $('player-name').value.trim();
        if (!val) return;
        playerName = val;
        storageSet('c4_name', playerName);
        setLobbyStep(2);
    });

    $('btn-change-name').addEventListener('click', () => {
        setLobbyStep(1);
        $('player-name').focus();
        $('player-name').select();
    });

    // 5. Mode tabs
    document.querySelectorAll('.mode-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.mode-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.mode-panel').forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            const panelId = tab.dataset.tab;
            const targetPanel = document.querySelector(`.mode-panel[data-panel="${panelId}"]`);
            if (targetPanel) targetPanel.classList.add('active');
        });
    });

    // 6. Game launchers
    $('btn-start-local').addEventListener('click', startLocalGame);
    $('btn-start-ai').addEventListener('click', startAIGame);
    $('btn-create').addEventListener('click', createOnlineGame);
    $('btn-random').addEventListener('click', findRandomGame);

    $('room-code').addEventListener('input', (e) => {
        const input = e.target;
        const pos = input.selectionStart;
        input.value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        input.setSelectionRange(pos, pos);
        $('btn-join').disabled = input.value.trim().length !== 6;
    });
    $('card-join').addEventListener('submit', (e) => {
        e.preventDefault();
        joinOnlineGame();
    });

    // 7. Action buttons in game
    $('btn-hint').addEventListener('click', triggerHint);
    $('btn-undo').addEventListener('click', triggerUndo);

    $('btn-resign').addEventListener('click', () => {
        if (!session || session.status !== 'playing') return;
        openConfirm(t('confirm_resign_title'), t('confirm_resign_text'), t('resign'), async () => {
            const s = session;
            if (!s || s.status !== 'playing') return;
            if (s.mode === 'online') {
                const res = await api('resign', {token: s.token});
                if (res.error) showError(res.error);
                pollServer();
            } else {
                // On one device the player to move gives up; against the computer it is always you
                const loser = s.mode === 'ai' ? s.playerSlot : s.turnSlot;
                endGame(loser === 1 ? 2 : 1, 'resign');
            }
        });
    });

    $('btn-draw').addEventListener('click', () => {
        if (!session || session.mode !== 'online' || session.status !== 'playing') return;
        openConfirm(t('confirm_draw_title'), t('confirm_draw_text'), t('draw'), async () => {
            const res = await api('offer_draw', {token: session.token});
            if (res.error) showError(res.error);
            else showToast(t('draw_offered'));
            pollServer();
        });
    });

    $('banner').addEventListener('click', (e) => {
        const answer = e.target.closest('[data-draw-answer]');
        if (answer) answerDraw(answer.dataset.drawAnswer === '1');
        if (e.target.closest('[data-claim-win]')) claimWin();
    });

    $('btn-result').addEventListener('click', () => showModal('gameover-overlay', true));
    $('btn-leave-game').addEventListener('click', requestLeave);
    document.querySelector('.game-title').addEventListener('click', (e) => {
        e.preventDefault();
        requestLeave();
    });
    $('btn-leave-waiting').addEventListener('click', leaveGame);

    // Audio toggle in header
    $('btn-audio-toggle').addEventListener('click', () => {
        customPrefs.sound = !customPrefs.sound;
        saveAndApplyCustomPrefs();
        showToast(customPrefs.sound ? t('sound_on') : t('sound_off'));
    });

    // Side panel on phones
    $('btn-chat-toggle').addEventListener('click', togglePanel);
    $('btn-moves-toggle').addEventListener('click', togglePanel);
    $('btn-chat-close').addEventListener('click', closePanel);
    $('chat-backdrop').addEventListener('click', closePanel);

    // Language selection (desktop and mobile)
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.getAttribute('data-lang');
            setLanguage(lang);
        });
    });

    // Mobile language menu
    $('btn-lang-menu').addEventListener('click', () => {
        showModal('lang-overlay', true);
    });
    $('btn-close-lang').addEventListener('click', () => {
        showModal('lang-overlay', false);
    });
    document.querySelectorAll('.lang-option').forEach(opt => {
        opt.addEventListener('click', () => {
            const lang = opt.getAttribute('data-lang');
            setLanguage(lang);
            showModal('lang-overlay', false);
        });
    });

    // 8. Modals triggers
    document.querySelectorAll('[data-open-rules]').forEach(b => b.addEventListener('click', () => {
        renderRulesModal();
        showModal('rules-overlay', true);
    }));
    $('btn-close-rules').addEventListener('click', () => showModal('rules-overlay', false));

    document.querySelectorAll('[data-open-style]').forEach(b => b.addEventListener('click', () => {
        renderCustomiseModal();
        showModal('style-overlay', true);
    }));
    $('btn-close-style').addEventListener('click', () => showModal('style-overlay', false));

    $('btn-howto').addEventListener('click', () => {
        renderHowToModal();
        showModal('howto-overlay', true);
    });
    $('btn-close-howto').addEventListener('click', () => showModal('howto-overlay', false));
    $('btn-close-howto-x').addEventListener('click', () => showModal('howto-overlay', false));

    $('btn-version').addEventListener('click', () => {
        renderReleasesModal();
        showModal('release-overlay', true);
    });
    $('btn-close-release').addEventListener('click', () => showModal('release-overlay', false));
    $('btn-donate')?.addEventListener('click', () => showModal('donate-overlay', true));
    $('btn-close-donate')?.addEventListener('click', () => showModal('donate-overlay', false));

    $('btn-confirm-no').addEventListener('click', () => showModal('confirm-overlay', false));
    $('btn-confirm-yes').addEventListener('click', () => {
        showModal('confirm-overlay', false);
        if (typeof confirmAction === 'function') confirmAction();
    });

    // Sheets close with Esc or a tap outside them
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeTopModal();
    });
    document.querySelectorAll('.modal').forEach(modal => {
        if (modal.id === 'gameover-overlay') return;
        modal.addEventListener('click', (e) => {
            if (e.target === modal) showModal(modal.id, false);
        });
    });

    // Game over actions
    $('btn-rematch').addEventListener('click', triggerRematch);
    $('btn-review').addEventListener('click', () => showModal('gameover-overlay', false));
    $('btn-gameover-home').addEventListener('click', leaveGame);
    $('turn-indicator')?.addEventListener('click', () => {
        if (session && session.status === 'finished') {
            showModal('gameover-overlay', true);
        }
    });

    // Chat form
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

    // Waiting share buttons
    $('btn-copy-code').addEventListener('click', () => {
        if (session && session.code) copyText(session.code, t('copied'));
    });
    $('btn-share-link').addEventListener('click', () => {
        if (!session || !session.code) return;
        copyText(`${window.location.origin}${window.location.pathname}?join=${session.code}`, t('link_copied'));
    });

    // Timers slow down in the background: catch up as soon as the player is back
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) pollServer();
    });

    // Check URL params for quick join (e.g. ?join=ABC123 or ?code=ABC123)
    const urlParams = new URLSearchParams(window.location.search);
    const joinCode = urlParams.get('join') || urlParams.get('code');
    if (joinCode) {
        const clean = joinCode.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
        $('room-code').value = clean;
        document.querySelector('.mode-tab[data-tab="online"]').click();
        $('btn-join').disabled = clean.length !== 6;
        $('card-join').classList.add('highlight');
    }

    // A reload does not lose an online game
    const saved = sessionGet('c4_session');
    if (saved) {
        try {
            const {code, token, slot, isPublic} = JSON.parse(saved);
            setupOnlineSession(code, token, slot, isPublic);
        } catch (e) {
            sessionSet('c4_session', null);
        }
    }

    // App version in footer
    $('app-version').textContent = 'v' + APP_VERSION;
});

// --- Modal Renderers ---
function renderRulesModal() {
    const body = $('rules-body');
    const r = RULES_TEXT[currentLang] || RULES_TEXT.en;
    if (!body || !r) return;

    body.innerHTML = `
        <div class="rules-section">
            <h4 style="color:var(--accent-light); margin-bottom:6px;">${r.goal_t}</h4>
            <p style="color:var(--text-muted); font-size:14px; line-height:1.5;">${r.goal}</p>
        </div>
        <div class="rules-section" style="margin-top:14px;">
            <h4 style="color:var(--accent-light); margin-bottom:6px;">${r.how_t}</h4>
            <p style="color:var(--text-muted); font-size:14px; line-height:1.5;">${r.how}</p>
        </div>
        <div class="rules-section" style="margin-top:14px;">
            <h4 style="color:var(--accent-light); margin-bottom:6px;">${r.win_t}</h4>
            <p style="color:var(--text-muted); font-size:14px; line-height:1.5;">${r.win}</p>
        </div>
        <div class="rules-section" style="margin-top:14px;">
            <h4 style="color:var(--accent-light); margin-bottom:6px;">${r.online_t}</h4>
            <ul style="padding-left:20px; line-height:1.6; font-size:13.5px; color:var(--text-muted);">
                ${r.online.map(item => `<li>${item}</li>`).join('')}
            </ul>
        </div>
        <div class="rules-section" style="margin-top:14px;">
            <h4 style="color:var(--accent-light); margin-bottom:6px;">${r.tips_t}</h4>
            <ul style="padding-left:20px; line-height:1.6; font-size:13.5px; color:var(--text-muted);">
                ${r.tips.map(item => `<li>${item}</li>`).join('')}
            </ul>
        </div>
    `;
}

function setLobbyStep(step) {
    if (step === 1) {
        $('dot-1').className = 'step active';
        $('dot-2').className = 'step';
        $('step-name').classList.add('active');
        $('step-mode').classList.remove('active');
        $('btn-step-1').disabled = true;
    } else {
        $('dot-1').className = 'step done';
        $('dot-2').className = 'step active';
        $('step-name').classList.remove('active');
        $('step-mode').classList.add('active');
        $('btn-step-1').disabled = false;
        $('greeting-text').textContent = t('greeting', {name: playerName});
    }
}

function renderLogoFaces() {
    const el = $('logo-faces');
    if (!el) return;
    el.innerHTML = `
        <span class="logo-face f1"><span class="disc"></span></span>
        <span class="logo-face f2"><span class="disc"></span></span>
        <span class="logo-face f3"><span class="disc"></span></span>
    `;
}

window.onLanguageChanged = function (lang) {
    $('random-desc').textContent = t('random_desc', {language: languageName()});
    if (playerName) {
        const greeting = $('greeting-text');
        if (greeting) greeting.textContent = t('greeting', {name: playerName});
    }
    if (session) {
        const modeBadge = $('mode-badge');
        if (modeBadge) modeBadge.textContent = t('tab_' + session.mode);
        if (session.status === 'playing') updateTurnIndicator();
        renderBanner();
    }
    document.querySelectorAll('.c4-column').forEach(col => {
        col.setAttribute('aria-label', t('col_label', {col: Number(col.dataset.col) + 1}));
    });
    renderHowToModal();
    updateCustomPreview();
    renderCustomiseModal();
    const appVer = $('app-version');
    if (appVer) appVer.textContent = 'v' + APP_VERSION;
};

function renderHowToModal() {
    const list = $('howto-list');
    const target = $('howto-sheet-body');
    if (list && target) {
        target.innerHTML = '';
        const copy = list.cloneNode(true);
        copy.removeAttribute('id');
        target.appendChild(copy);
    }
}

function renderReleasesModal() {
    const list = $('release-list');
    if (!list) return;
    list.innerHTML = RELEASES.map(rel => {
        const notes = (rel.notes[currentLang] || rel.notes.en || []).map(n => `<li>${n}</li>`).join('');
        return `
            <div style="margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:14px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <strong style="color:var(--accent-light); font-size:16px;">v${rel.version}</strong>
                </div>
                <ul style="padding-left:20px; font-size:13px; line-height:1.5; color:var(--text-muted);">${notes}</ul>
            </div>
        `;
    }).join('');
}

// 4x3 Mini Preview Board Generator
function updateCustomPreview() {
    const previewBoard = $('custom-preview-board');
    if (!previewBoard) return;

    previewBoard.innerHTML = '';
    // 3 rows x 4 columns = 12 cells
    // Row 2 (bottom): P1, P2, P1, P2
    // Row 1: empty, P1, P2, empty
    // Row 0 (top): empty, empty, empty, empty
    const layout = [
        [0, 0, 0, 0],
        [0, 1, 2, 0],
        [1, 2, 1, 2],
    ];

    const isFlat = customPrefs.discStyle === 'flat';
    for (let r = 0; r < 3; r++) {
        for (let c = 0; c < 4; c++) {
            const cell = document.createElement('div');
            cell.className = 'preview-cell';
            const val = layout[r][c];
            if (val > 0) {
                const disc = document.createElement('div');
                disc.className = `preview-disc p${val} style-${customPrefs.discStyle}`;
                disc.style.background = isFlat
                    ? (val === 1 ? 'var(--disc-p1-color)' : 'var(--disc-p2-color)')
                    : (val === 1 ? 'var(--disc-p1-grad)' : 'var(--disc-p2-grad)');
                cell.appendChild(disc);
            }
            previewBoard.appendChild(cell);
        }
    }
}

function renderCustomiseModal() {
    const grid = $('palette-grid');
    if (!grid) return;

    const isFlat = customPrefs.discStyle === 'flat';
    grid.innerHTML = Object.entries(PALETTES).map(([key, p]) => `
        <div class="palette-card ${customPrefs.palette === key ? 'active' : ''}" data-palette="${key}">
            <div class="palette-dots">
                <span class="palette-dot" style="background:${isFlat ? p.p1 : p.p1G};"></span>
                <span class="palette-dot" style="background:${isFlat ? p.p2 : p.p2G};"></span>
            </div>
            <span class="palette-name">${t('preset_' + key)}</span>
        </div>
    `).join('');

    grid.querySelectorAll('.palette-card').forEach(card => {
        card.addEventListener('click', () => {
            grid.querySelectorAll('.palette-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            customPrefs.palette = card.dataset.palette;
            saveAndApplyCustomPrefs();
        });
    });

    // Style options
    document.querySelectorAll('[data-disc-style]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.discStyle === customPrefs.discStyle);
        btn.onclick = () => {
            document.querySelectorAll('[data-disc-style]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            customPrefs.discStyle = btn.dataset.discStyle;
            saveAndApplyCustomPrefs();
            renderCustomiseModal();
        };
    });

    // Board options
    document.querySelectorAll('[data-board-hue]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.boardHue === customPrefs.boardHue);
        btn.onclick = () => {
            document.querySelectorAll('[data-board-hue]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            customPrefs.boardHue = btn.dataset.boardHue;
            saveAndApplyCustomPrefs();
        };
    });

    // Switches
    const soundSw = $('opt-sound');
    if (soundSw) {
        soundSw.checked = customPrefs.sound;
        soundSw.onchange = () => {
            customPrefs.sound = soundSw.checked;
            saveAndApplyCustomPrefs();
        };
    }

    const guideSw = $('opt-guides');
    if (guideSw) {
        guideSw.checked = customPrefs.guides;
        guideSw.onchange = () => {
            customPrefs.guides = guideSw.checked;
            saveAndApplyCustomPrefs();
        };
    }

    // Animation speed
    document.querySelectorAll('[data-anim-speed]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.animSpeed === customPrefs.animSpeed);
        btn.onclick = () => {
            document.querySelectorAll('[data-anim-speed]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            customPrefs.animSpeed = btn.dataset.animSpeed;
            saveAndApplyCustomPrefs();
        };
    });

    updateCustomPreview();
    renderLogoFaces();
}

function saveAndApplyCustomPrefs() {
    storageSet('c4_prefs', JSON.stringify(customPrefs));
    applyCustomPrefs();
}

// Lets the game open without a connection (see sw.js)
if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => {
}));
