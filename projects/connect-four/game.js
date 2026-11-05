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

// Audio context
let audioCtx = null;

// Customization preferences
const customPrefs = {
    palette: 'fuchsia_gold',
    p1Color: '#D946EF',
    p2Color: '#EAB308',
    discStyle: 'glossy',
    boardHue: 'plum',
    sound: true,
    guides: true,
    animSpeed: 'normal',
};

// Preset color pairs
const PALETTES = {
    fuchsia_gold:   { p1: '#D946EF', p2: '#EAB308', p1G: 'radial-gradient(circle at 35% 30%, #FDF4FF 0%, #F472B6 28%, #D946EF 68%, #A21CAF 100%)', p2G: 'radial-gradient(circle at 35% 30%, #FEFCE8 0%, #FDE047 28%, #EAB308 68%, #CA8A04 100%)' },
    red_blue:       { p1: '#EF4444', p2: '#2563EB', p1G: 'radial-gradient(circle at 35% 30%, #FFE4E6 0%, #F87171 28%, #EF4444 68%, #B91C1C 100%)', p2G: 'radial-gradient(circle at 35% 30%, #EFF6FF 0%, #60A5FA 28%, #2563EB 68%, #1D4ED8 100%)' },
    black_white:    { p1: '#F8FAFC', p2: '#18181B', p1G: 'radial-gradient(circle at 35% 30%, #FFFFFF 0%, #F1F5F9 28%, #E2E8F0 68%, #94A3B8 100%)', p2G: 'radial-gradient(circle at 35% 30%, #71717A 0%, #3F3F46 28%, #18181B 68%, #09090B 100%)' },
    neon_cyan:      { p1: '#FF007F', p2: '#06B6D4', p1G: 'radial-gradient(circle at 35% 30%, #FFF0F7 0%, #FF66B2 30%, #FF007F 70%, #B30059 100%)', p2G: 'radial-gradient(circle at 35% 30%, #ECFEFF 0%, #67E8F9 30%, #06B6D4 70%, #0891B2 100%)' },
    sunset_amber:   { p1: '#F97316', p2: '#FBBF24', p1G: 'radial-gradient(circle at 35% 30%, #FFF7ED 0%, #FB923C 30%, #EA580C 70%, #9A3412 100%)', p2G: 'radial-gradient(circle at 35% 30%, #FEFCE8 0%, #FCD34D 30%, #F59E0B 70%, #B45309 100%)' },
    emerald_violet: { p1: '#10B981', p2: '#8B5CF6', p1G: 'radial-gradient(circle at 35% 30%, #ECFDF5 0%, #34D399 30%, #059669 70%, #047857 100%)', p2G: 'radial-gradient(circle at 35% 30%, #F5F3FF 0%, #A78BFA 30%, #7C3AED 70%, #5B21B6 100%)' },
    orange_cobalt:  { p1: '#FF6B4A', p2: '#1D4ED8', p1G: 'radial-gradient(circle at 35% 30%, #FFF1EE 0%, #FF8A65 28%, #FF6B4A 68%, #C2410C 100%)', p2G: 'radial-gradient(circle at 35% 30%, #DBEAFE 0%, #60A5FA 28%, #1D4ED8 68%, #1E3A8A 100%)' },
    lime_pink:      { p1: '#84CC16', p2: '#EC4899', p1G: 'radial-gradient(circle at 35% 30%, #F7FEE7 0%, #A3E635 28%, #84CC16 68%, #4D7C0F 100%)', p2G: 'radial-gradient(circle at 35% 30%, #FDF2F8 0%, #F472B6 28%, #EC4899 68%, #BE185D 100%)' },
    gold_silver:    { p1: '#EAB308', p2: '#CBD5E1', p1G: 'radial-gradient(circle at 35% 30%, #FEFCE8 0%, #FDE047 28%, #EAB308 68%, #A16207 100%)', p2G: 'radial-gradient(circle at 35% 30%, #FFFFFF 0%, #E2E8F0 28%, #CBD5E1 68%, #64748B 100%)' },
    ruby_yellow:    { p1: '#EF4444', p2: '#FACC15', p1G: 'radial-gradient(circle at 35% 30%, #FEF2F2 0%, #F87171 30%, #DC2626 70%, #991B1B 100%)', p2G: 'radial-gradient(circle at 35% 30%, #FEFCE8 0%, #FDE047 30%, #EAB308 70%, #A16207 100%)' },
    lilac_mint:     { p1: '#A855F7', p2: '#14B8A6', p1G: 'radial-gradient(circle at 35% 30%, #FAF5FF 0%, #C084FC 30%, #9333EA 70%, #6B21A8 100%)', p2G: 'radial-gradient(circle at 35% 30%, #F0FDFA 0%, #5EEAD4 30%, #0D9488 70%, #115E59 100%)' },
};

// --- Storage Helpers ---
function storageGet(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
function storageSet(k, v) { try { localStorage.setItem(k, v); } catch (e) {} }

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
    } catch (e) {}
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
    } catch (e) {}
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
}

// --- API Client ---
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
        return { error: 'network_error' };
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

        boardEl.appendChild(colEl);
    }
}

function onColumnHover(col, active) {
    if (!customPrefs.guides || !session || session.status !== 'playing' || !isMyTurn()) return;
    const guide = document.querySelector(`.guide-indicator[data-col="${col}"]`);
    if (guide) guide.classList.toggle('active', active);
}

function isMyTurn() {
    if (!session || session.status !== 'playing') return false;
    if (session.mode === 'local') return true;
    if (session.mode === 'ai') return session.turnSlot === session.playerSlot;
    if (session.mode === 'online') return session.slotToMove === session.mySlot;
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
        executeLocalMove(col, row);
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
    line.forEach(({ r, c }) => {
        const disc = document.querySelector(`.c4-cell[data-row="${r}"][data-col="${c}"] .c4-disc`);
        if (disc) disc.classList.add('winning');
    });

    // Scaled coordinates based on 700x600 viewBox
    const x1 = line[0].c * 100 + 50;
    const y1 = line[0].r * 100 + 50;
    const x2 = line[3].c * 100 + 50;
    const y2 = line[3].r * 100 + 50;

    svg.setAttribute('viewBox', '0 0 700 600');
    svg.setAttribute('preserveAspectRatio', 'none');
    svg.innerHTML = `
        <line x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}" stroke="#FFFFFF" stroke-width="12" stroke-linecap="round" opacity="0.95" style="filter: drop-shadow(0 0 10px var(--accent));"/>
        <circle cx="${x1}" cy="${y1}" r="12" fill="#FFFFFF"/>
        <circle cx="${line[1].c * 100 + 50}" cy="${line[1].r * 100 + 50}" r="10" fill="#FFFFFF"/>
        <circle cx="${line[2].c * 100 + 50}" cy="${line[2].r * 100 + 50}" r="10" fill="#FFFFFF"/>
        <circle cx="${x2}" cy="${y2}" r="12" fill="#FFFFFF"/>
    `;
}

// --- Moves History List ---
function addMoveToHistory(ply, col, player) {
    const list = $('move-list');
    if (!list) return;
    const item = document.createElement('li');
    item.className = 'move-item';
    item.innerHTML = `<span class="move-num">#${ply + 1}</span> <span>${t('col_label', { col: col + 1 })}</span> <span class="palette-dot" style="width:12px;height:12px;background:var(--disc-p${player}-color);"></span>`;
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

    let isMy = isMyTurn();
    let currentSlot = session.mode === 'online' ? session.slotToMove : session.turnSlot;
    let name = currentSlot === 1 ? session.p1Name : session.p2Name;

    if (session.mode === 'ai' && currentSlot !== session.playerSlot) {
        ind.innerHTML = `<span class="online-dot" style="background:var(--accent);"></span> <span>${t('computer_thinking')}</span>`;
    } else if (isMy) {
        ind.innerHTML = `<span class="turn-disc-dot" style="background:var(--disc-p${currentSlot}-color); color:var(--disc-p${currentSlot}-color);"></span> <span>${t('your_turn')}</span>`;
    } else {
        ind.innerHTML = `<span class="turn-disc-dot" style="background:var(--disc-p${currentSlot}-color); color:var(--disc-p${currentSlot}-color);"></span> <span>${t('their_turn', { name })}</span>`;
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

function executeLocalMove(col, row) {
    const player = session.turnSlot;
    session.board[row][col] = player;
    session.moves.push(col);

    renderDisc(row, col, player, true);
    playDropSound(row);
    addMoveToHistory(session.moves.length - 1, col, player);
    updateFullColumnsUI();

    const winLine = ConnectFour.checkWinAt(session.board, row, col);
    if (winLine) {
        endGame(player, 'connect4', winLine);
        return;
    }

    if (ConnectFour.isBoardFull(session.board)) {
        endGame(0, 'draw_board');
        return;
    }

    session.turnSlot = session.turnSlot === 1 ? 2 : 1;
    session.turnStartedMs = Date.now();
    updateTurnIndicator();
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
    const player = session.playerSlot;
    session.board[row][col] = player;
    session.moves.push(col);

    renderDisc(row, col, player, true);
    playDropSound(row);
    addMoveToHistory(session.moves.length - 1, col, player);
    updateFullColumnsUI();

    const winLine = ConnectFour.checkWinAt(session.board, row, col);
    if (winLine) {
        endGame(player, 'connect4', winLine);
        return;
    }

    if (ConnectFour.isBoardFull(session.board)) {
        endGame(0, 'draw_board');
        return;
    }

    session.turnSlot = player === 1 ? 2 : 1;
    updateTurnIndicator();
    triggerAIMove();
}

function triggerAIMove() {
    if (!session || session.status !== 'playing') return;
    const aiSlot = session.playerSlot === 1 ? 2 : 1;
    const currentSeq = session.seq;

    setTimeout(() => {
        if (!session || session.seq !== currentSeq || session.status !== 'playing') return;

        let bestCol = -1;
        if (typeof Worker !== 'undefined') {
            try {
                if (!aiWorker) aiWorker = new Worker('ai-worker.js');
                const reqId = Date.now();
                aiWorker.onmessage = (e) => {
                    if (session && session.seq === currentSeq && session.status === 'playing') {
                        executeAIMove(e.data.col, aiSlot);
                    }
                };
                aiWorker.postMessage({
                    id: reqId,
                    board: session.board,
                    player: aiSlot,
                    level: session.aiLevel,
                });
                return;
            } catch (err) {}
        }

        bestCol = ConnectFourAI.bestMove(session.board, aiSlot, session.aiLevel);
        executeAIMove(bestCol, aiSlot);
    }, 320);
}

function executeAIMove(col, aiSlot) {
    if (!session || session.status !== 'playing') return;
    const row = ConnectFour.getLowestEmptyRow(session.board, col);
    if (row === -1) return;

    session.board[row][col] = aiSlot;
    session.moves.push(col);

    renderDisc(row, col, aiSlot, true);
    playDropSound(row);
    addMoveToHistory(session.moves.length - 1, col, aiSlot);
    updateFullColumnsUI();

    const winLine = ConnectFour.checkWinAt(session.board, row, col);
    if (winLine) {
        endGame(aiSlot, 'connect4', winLine);
        return;
    }

    if (ConnectFour.isBoardFull(session.board)) {
        endGame(0, 'draw_board');
        return;
    }

    session.turnSlot = session.playerSlot;
    session.turnStartedMs = Date.now();
    updateTurnIndicator();
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
        lastMessageId: 0,
    };

    if (isPublic) {
        $('waiting-title').textContent = t('searching_title');
        $('waiting-public-desc').textContent = t('searching_desc', { language: currentLang.toUpperCase() });
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
    if (!session || session.mode !== 'online' || !session.token) return;

    const res = await api('poll', {
        token: session.token,
        since: session.lastMessageId || 0,
    });

    if (res.error) {
        if (res.error === 'invalid_session') {
            clearInterval(pollTimer);
            showError('invalid_session');
            showScreen('lobby');
        }
        return;
    }

    handlePollUpdate(res);
}

function handlePollUpdate(data) {
    if (!session) return;

    session.code = data.code;
    session.firstSlot = data.firstSlot;
    session.timeControl = data.timeControl;
    session.p1Ms = data.p1Ms;
    session.p2Ms = data.p2Ms;

    const p1 = data.players.find(p => p.slot === 1);
    const p2 = data.players.find(p => p.slot === 2);
    if (p1) { session.p1Name = p1.name; session.p1Points = p1.points2; }
    if (p2) { session.p2Name = p2.name; session.p2Points = p2.points2; }

    // State transition: Waiting -> Playing
    if (session.status === 'waiting' && data.status === 'playing') {
        session.status = 'playing';
        setupGameScreenUI();
        showScreen('game');
        startClock();
    }

    // Moves sync
    const serverMoves = data.moves || [];
    if (serverMoves.length > session.moves.length) {
        for (let i = session.moves.length; i < serverMoves.length; i++) {
            const col = parseInt(serverMoves[i], 10);
            const player = (i % 2 === 0) ? session.firstSlot : (session.firstSlot === 1 ? 2 : 1);
            const row = ConnectFour.getLowestEmptyRow(session.board, col);
            if (row !== -1) {
                session.board[row][col] = player;
                renderDisc(row, col, player, true);
                playDropSound(row);
                addMoveToHistory(i, col, player);
            }
        }
        session.moves = serverMoves;
        updateFullColumnsUI();
    }

    session.slotToMove = (session.moves.length % 2 === 0) ? session.firstSlot : (session.firstSlot === 1 ? 2 : 1);
    updateTurnIndicator();

    // Check game termination from server
    if (data.status === 'finished' && session.status !== 'finished') {
        let winLine = null;
        if (data.winningLine) {
            winLine = data.winningLine.split('-').map(s => {
                const [r, c] = s.split(',').map(Number);
                return { r, c };
            });
        }
        const winner = data.result === '1-0' ? 1 : (data.result === '0-1' ? 2 : 0);
        endGame(winner, data.reason, winLine);
    }

    // Incoming Chat messages
    if (data.messages && data.messages.length > 0) {
        data.messages.forEach(msg => {
            appendChatMessage(msg.slot === session.mySlot ? 'me' : 'them', msg.text);
            session.lastMessageId = Math.max(session.lastMessageId || 0, msg.id);
        });
    }

    // Draw offer notification
    if (data.drawOffer !== null && data.drawOffer !== session.mySlot && !session.drawOfferPrompted) {
        session.drawOfferPrompted = true;
        const oppName = session.mySlot === 1 ? session.p2Name : session.p1Name;
        openConfirm(t('draw'), t('draw_offer_received', { name: oppName }), t('accept'), async () => {
            await api('answer_draw', { token: session.token, accept: 1 });
            session.drawOfferPrompted = false;
        });
    }
}

async function executeOnlineMove(col) {
    const ply = session.moves.length;
    const res = await api('move', {
        token: session.token,
        col,
        ply,
    });

    if (res.error) {
        showError(res.error);
        pollServer();
    } else {
        pollServer();
    }
}

// --- Clocks ---
function startClock() {
    clearInterval(clockInterval);
    if (!session || session.timeControl <= 0) return;

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

    // Player bars
    const topSlot = session.mySlot === 2 ? 1 : 2;
    const bottomSlot = session.mySlot === 2 ? 2 : 1;

    const nameTop = topSlot === 1 ? session.p1Name : session.p2Name;
    const nameBottom = bottomSlot === 1 ? session.p1Name : session.p2Name;

    $('name-top').textContent = nameTop;
    $('name-bottom').textContent = nameBottom;

    const isFlat = customPrefs.discStyle === 'flat';
    $('avatar-top').style.background = isFlat ? `var(--disc-p${topSlot}-color)` : `var(--disc-p${topSlot}-grad)`;
    $('avatar-bottom').style.background = isFlat ? `var(--disc-p${bottomSlot}-color)` : `var(--disc-p${bottomSlot}-grad)`;

    const ptsTop = topSlot === 1 ? session.p1Points : session.p2Points;
    const ptsBottom = bottomSlot === 1 ? session.p1Points : session.p2Points;
    $('score-top').textContent = Math.floor(ptsTop / 2) + (ptsTop % 2 ? '½' : '') + ' pt';
    $('score-bottom').textContent = Math.floor(ptsBottom / 2) + (ptsBottom % 2 ? '½' : '') + ' pt';

    // Controls visibility based on mode
    const isOnline = session.mode === 'online';
    $('chat').classList.toggle('hidden', !isOnline);
    $('btn-hint').classList.toggle('hidden', isOnline);
    $('btn-undo').classList.toggle('hidden', isOnline);
    $('btn-draw').classList.toggle('hidden', !isOnline);
    $('btn-chat-toggle').classList.toggle('hidden', !isOnline);

    updateTurnIndicator();
    updateClockDisplay();
}

// --- End Game Flow ---
const GAMEOVER_ICONS = {
    win: `<svg viewBox="0 0 60 60" width="56" height="56"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--green)" stroke-width="3"/><path d="M18 30l8 8 16-16" fill="none" stroke="var(--green)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    lose: `<svg viewBox="0 0 60 60" width="56" height="56"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--red)" stroke-width="3"/><path d="M20 20l20 20M40 20l-20 20" fill="none" stroke="var(--red)" stroke-width="3" stroke-linecap="round"/></svg>`,
    draw: `<svg viewBox="0 0 60 60" width="56" height="56"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--yellow)" stroke-width="3"/><path d="M19 25h22M19 35h22" fill="none" stroke="var(--yellow)" stroke-width="3" stroke-linecap="round"/></svg>`,
    trophy: `<svg viewBox="0 0 60 60" width="56" height="56" fill="none" stroke="var(--yellow)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10h20v14a10 10 0 01-20 0z"/><path d="M20 14h-7a6 6 0 006 8M40 14h7a6 6 0 01-6 8M30 34v8M22 50h16M24 42h12v8H24z"/></svg>`,
};

function endGame(winner, reason, winLine = null) {
    if (!session) return;
    session.status = 'finished';
    clearInterval(clockInterval);

    if (winLine) {
        drawWinningLine(winLine);
        playWinSound();
    }

    const titleEl = $('gameover-title');
    const textEl = $('gameover-text');
    const scoreEl = $('score-line');
    const iconEl = $('gameover-icon');

    const p1 = session.p1Name;
    const p2 = session.p2Name;

    let isMe = false;
    let winName = '';

    if (winner === 0) {
        titleEl.textContent = t('game_draw');
        textEl.textContent = t('reason_' + reason) || '';
        iconEl.innerHTML = GAMEOVER_ICONS.draw;
    } else {
        winName = winner === 1 ? p1 : p2;
        isMe = (session.mode === 'online' && winner === session.mySlot) ||
               (session.mode === 'ai' && winner === session.playerSlot);

        if (session.mode === 'local') {
            titleEl.textContent = t('won_the_game', { name: winName });
            iconEl.innerHTML = GAMEOVER_ICONS.trophy;
        } else if (isMe) {
            titleEl.textContent = t('you_won');
            iconEl.innerHTML = GAMEOVER_ICONS.win;
        } else {
            titleEl.textContent = t('you_lost');
            iconEl.innerHTML = GAMEOVER_ICONS.lose;
        }
        textEl.textContent = t('reason_' + reason, { name: winner === 1 ? p2 : p1 }) || '';

        if (winner === 1) session.p1Points += 2;
        else session.p2Points += 2;
    }

    scoreEl.textContent = t('score_tracker', {
        p1, s1: Math.floor(session.p1Points / 2) + (session.p1Points % 2 ? '½' : ''),
        p2, s2: Math.floor(session.p2Points / 2) + (session.p2Points % 2 ? '½' : '')
    });

    $('btn-rematch').textContent = t('rematch');
    $('rematch-status').textContent = '';

    const ind = $('turn-indicator');
    if (ind) {
        if (session.mode !== 'local') {
            ind.textContent = isMe ? t('you_won') : (winner === 0 ? t('game_draw') : t('you_lost'));
        } else {
            ind.textContent = winner === 0 ? t('game_draw') : t('won_the_game', { name: winName });
        }
    }
    const barTop = $('bar-top');
    const barBottom = $('bar-bottom');
    if (barTop) barTop.classList.remove('active-turn');
    if (barBottom) barBottom.classList.remove('active-turn');

    setTimeout(() => {
        showModal('gameover-overlay', true);
    }, 700);
}

// Rematch click
async function triggerRematch() {
    if (!session) return;

    if (session.mode === 'online') {
        $('rematch-status').textContent = t('rematch_requested');
        await api('rematch', { token: session.token });
        showModal('gameover-overlay', false);
    } else {
        // Swap who starts
        session.firstSlot = session.firstSlot === 1 ? 2 : 1;
        session.turnSlot = session.firstSlot;
        session.moves = [];
        session.board = ConnectFour.createBoard();
        session.status = 'playing';
        session.turnStartedMs = Date.now();
        if (session.timeControl > 0) {
            session.p1Ms = session.timeControl * 1000;
            session.p2Ms = session.timeControl * 1000;
        }

        showModal('gameover-overlay', false);
        setupGameScreenUI();
        startClock();

        if (session.mode === 'ai' && session.firstSlot !== session.playerSlot) {
            triggerAIMove();
        }
    }
}

// Hint & Undo
function triggerHint() {
    if (!session || session.status !== 'playing' || !isMyTurn()) return;
    const currentSlot = session.mode === 'ai' ? session.playerSlot : session.turnSlot;
    const bestCol = ConnectFourAI.bestMove(session.board, currentSlot, 'hard');
    if (bestCol !== -1) {
        const guide = document.querySelector(`.guide-indicator[data-col="${bestCol}"]`);
        if (guide) {
            guide.classList.add('active');
            showToast(t('hint') + ': ' + t('col_label', { col: bestCol + 1 }));
            setTimeout(() => guide.classList.remove('active'), 2500);
        }
    }
}

function triggerUndo() {
    if (!session || session.status !== 'playing' || session.mode === 'online') return;
    if (session.moves.length === 0) return;

    const countToPop = (session.mode === 'ai') ? 2 : 1;
    for (let i = 0; i < countToPop; i++) {
        if (session.moves.length === 0) break;
        session.moves.pop();
    }

    session.board = ConnectFour.createBoard();
    clearBoardDOM();
    clearMoveHistory();

    for (let ply = 0; ply < session.moves.length; ply++) {
        const col = session.moves[ply];
        const p = (ply % 2 === 0) ? session.firstSlot : (session.firstSlot === 1 ? 2 : 1);
        const r = ConnectFour.getLowestEmptyRow(session.board, col);
        session.board[r][col] = p;
        renderDisc(r, col, p, false);
        addMoveToHistory(ply, col, p);
    }

    session.turnSlot = (session.moves.length % 2 === 0) ? session.firstSlot : (session.firstSlot === 1 ? 2 : 1);
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
        try { Object.assign(customPrefs, JSON.parse(storedPrefs)); } catch (e) {}
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
        openConfirm(t('confirm_resign_title'), t('confirm_resign_text'), t('resign'), async () => {
            showModal('confirm-overlay', false);
            if (session && session.mode === 'online') {
                await api('resign', { token: session.token });
            } else if (session) {
                const opp = session.turnSlot === 1 ? 2 : 1;
                endGame(opp, 'resign');
            }
        });
    });

    $('btn-draw').addEventListener('click', () => {
        if (!session || session.mode !== 'online') return;
        openConfirm(t('confirm_draw_title'), t('confirm_draw_text'), t('draw'), async () => {
            showModal('confirm-overlay', false);
            await api('offer_draw', { token: session.token });
            showToast(t('draw_offered'));
        });
    });

    $('btn-leave-game').addEventListener('click', () => {
        openConfirm(t('confirm_leave_title'), t('confirm_leave_text'), t('leave'), async () => {
            showModal('confirm-overlay', false);
            if (session && session.mode === 'online') {
                await api('leave', { token: session.token });
                clearInterval(pollTimer);
            }
            clearInterval(clockInterval);
            session = null;
            showScreen('lobby');
        });
    });

    $('btn-leave-waiting').addEventListener('click', async () => {
        if (session && session.token) {
            await api('leave', { token: session.token });
        }
        clearInterval(pollTimer);
        session = null;
        showScreen('lobby');
    });

    // Audio toggle in header
    $('btn-audio-toggle').addEventListener('click', () => {
        customPrefs.sound = !customPrefs.sound;
        saveAndApplyCustomPrefs();
        showToast(customPrefs.sound ? t('sound_on') : t('sound_off'));
    });

    // Mobile chat toggle
    const sidePanel = $('side-panel');
    const backdrop = $('chat-backdrop');
    $('btn-chat-toggle').addEventListener('click', () => {
        sidePanel.classList.toggle('open');
        backdrop.classList.toggle('open');
    });
    $('btn-chat-close').addEventListener('click', () => {
        sidePanel.classList.remove('open');
        backdrop.classList.remove('open');
    });
    backdrop.addEventListener('click', () => {
        sidePanel.classList.remove('open');
        backdrop.classList.remove('open');
    });

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
        if (typeof confirmAction === 'function') confirmAction();
    });

    // Game over actions
    $('btn-rematch').addEventListener('click', triggerRematch);
    $('btn-review').addEventListener('click', () => showModal('gameover-overlay', false));
    $('btn-gameover-home').addEventListener('click', () => {
        showModal('gameover-overlay', false);
        showScreen('lobby');
    });
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
        appendChatMessage('me', text);
        const res = await api('send_message', { token: session.token, text });
        if (res.error) showError(res.error);
    });

    // Waiting share buttons
    $('btn-copy-code').addEventListener('click', () => {
        if (!session || !session.code) return;
        navigator.clipboard.writeText(session.code);
        showToast(t('copied'));
    });
    $('btn-share-link').addEventListener('click', () => {
        if (!session || !session.code) return;
        const url = `${window.location.origin}${window.location.pathname}?join=${session.code}`;
        navigator.clipboard.writeText(url);
        showToast(t('link_copied'));
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
        $('greeting-text').textContent = t('greeting', { name: playerName });
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

window.onLanguageChanged = function(lang) {
    if (playerName) {
        const greeting = $('greeting-text');
        if (greeting) greeting.textContent = t('greeting', { name: playerName });
    }
    if (session) {
        const modeBadge = $('mode-badge');
        if (modeBadge) modeBadge.textContent = t('tab_' + session.mode);
        updateTurnIndicator();
    }
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
        soundSw.onchange = () => { customPrefs.sound = soundSw.checked; saveAndApplyCustomPrefs(); };
    }

    const guideSw = $('opt-guides');
    if (guideSw) {
        guideSw.checked = customPrefs.guides;
        guideSw.onchange = () => { customPrefs.guides = guideSw.checked; saveAndApplyCustomPrefs(); };
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
