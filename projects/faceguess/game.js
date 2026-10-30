const API_URL = 'api/index.php';
const POLL_MS = 2000;

const $ = (id) => document.getElementById(id);

function freshState() {
    return {
        token: null,
        code: null,
        slot: null,
        status: 'lobby',
        cards: [],
        myPosition: null,
        currentTurn: null,
        guessMode: false,
        pendingGuessPosition: null,
        pollTimer: null,
        lastData: null,
        logHash: '',
        round: null,
        mode: 'online',
        isPublic: false,
        lastTurnMine: null,
    };
}

let gameState = freshState();
let playerName = '';

function storageGet(key) {
    try { return localStorage.getItem(key); } catch (e) { return null; }
}
function storageSet(key, value) {
    try { localStorage.setItem(key, value); } catch (e) { /* storage unavailable */ }
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

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function cardAt(position) {
    const card = gameState.cards.find(c => c.position === position);
    return card ? card.card_data : null;
}

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
    if (step === 1) {
        $('player-name').focus();
    } else {
        $('greeting-text').textContent = t('greeting', { name: playerName });
        if ($('room-code').value) $('room-code').focus();
    }
}

function setBusy(button, busy) {
    button.disabled = busy;
    button.classList.toggle('loading', busy);
}

function selectedPlayMode() {
    const checked = document.querySelector('input[name="play-mode"]:checked');
    return checked ? checked.value : 'online';
}

async function createRoom() {
    const btn = $('btn-create');
    const mode = selectedPlayMode();
    setBusy(btn, true);
    const result = await api('create_room', { name: playerName, mode });
    setBusy(btn, false);

    if (result.error) return showError(result.error);

    Object.assign(gameState, { token: result.token, code: result.code, slot: result.slot, status: 'waiting', mode, isPublic: false });
    showWaiting();
    startPolling();
}

async function findRandomMatch() {
    const btn = $('btn-random');
    setBusy(btn, true);
    const result = await api('find_match', { name: playerName, lang: currentLang });
    setBusy(btn, false);

    if (result.error) return showError(result.error);

    Object.assign(gameState, { token: result.token, slot: result.slot, status: 'waiting', mode: 'online', isPublic: true });
    if (!result.matched) showWaiting();
    startPolling();
}

function languageName(lang) {
    return t('lang_' + lang);
}

function showWaiting() {
    const isPublic = gameState.isPublic;
    $('waiting-title').textContent = isPublic ? t('searching_title') : t('waiting_title');
    $('waiting-public-desc').textContent = isPublic ? t('searching_desc', { language: languageName(currentLang) }) : '';
    $('waiting-public-desc').classList.toggle('hidden', !isPublic);
    $('waiting-private').classList.toggle('hidden', isPublic);
    $('btn-share-link').classList.toggle('hidden', isPublic);
    // Phones get the system share sheet (WhatsApp, Messages…); everyone keeps "copy link".
    $('btn-native-share').classList.toggle('hidden', isPublic || typeof navigator.share !== 'function');
    $('btn-leave-waiting').textContent = isPublic ? t('cancel') : t('leave');
    $('display-code').textContent = gameState.code || '';
    $('waiting-mode-note').textContent = gameState.mode === 'inperson' ? t('waiting_inperson_note') : '';
    showScreen('waiting');
}

function applyModeUI() {
    const inPerson = gameState.mode === 'inperson';
    $('mode-badge').classList.toggle('hidden', !inPerson);
    $('screen-game').classList.toggle('mode-inperson', inPerson);
}

async function endTurn() {
    const btn = $('btn-end-turn');
    setBusy(btn, true);
    const result = await api('end_turn', { token: gameState.token });
    setBusy(btn, false);
    if (result.error) return showError(result.error);
    poll();
}

async function joinRoom() {
    const code = normalizeCode($('room-code').value);
    if (code.length !== 6) return showError('code_required');

    const btn = $('btn-join');
    setBusy(btn, true);
    const result = await api('join_room', { name: playerName, code });
    setBusy(btn, false);

    if (result.error) return showError(result.error);

    Object.assign(gameState, { token: result.token, code: result.code, slot: result.slot, status: 'waiting' });
    startPolling();
}

function normalizeCode(value) {
    return value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
}

function inviteLink() {
    return `${location.origin}${location.pathname}?code=${gameState.code}`;
}

async function copyText(text, okMessage) {
    try {
        await navigator.clipboard.writeText(text);
        showToast(okMessage);
    } catch (e) {
        showToast(text);
    }
}

// --- Polling ---
function startPolling() {
    stopPolling();
    poll();
    gameState.pollTimer = setInterval(poll, POLL_MS);
}

function stopPolling() {
    clearInterval(gameState.pollTimer);
    gameState.pollTimer = null;
}

async function poll() {
    const token = gameState.token;
    if (!token) return;
    const data = await api('poll', { token });
    // The player may have left (or started another game) while this request was in flight.
    if (gameState.token !== token) return;

    if (data.error) {
        if (data.error === 'invalid_session') {
            stopPolling();
            showError('invalid_session');
            backToLobby();
        }
        return;
    }

    gameState.lastData = data;
    gameState.slot = data.mySlot;
    gameState.code = data.code;
    gameState.mode = data.mode;
    gameState.isPublic = data.isPublic;

    if (data.status === 'waiting') return;

    if (gameState.round !== null && data.round !== gameState.round) startNewRound(data.round);
    gameState.round = data.round;

    gameState.currentTurn = data.currentTurn;
    gameState.cards = data.cards || [];
    gameState.myPosition = data.myPosition;

    if (gameState.status !== 'playing' && gameState.status !== 'finished') {
        gameState.status = 'playing';
        showScreen('game');
        applyModeUI();
        renderMyCardThumb();
        setTimeout(() => openMyCard(), 450);
    }

    updateBoard();
    updatePlayersStatus(data.players);
    renderSideScore(data);
    if (data.status === 'playing' && checkOpponentPresence(data)) return;
    updateQuestionLog(data.guesses || []);

    if (data.status === 'finished') {
        if (gameState.status !== 'finished') {
            gameState.status = 'finished';
            showGameOver(data);
        }
        updateRematchUI(data);
        return;
    }

    updateTurnIndicator(data);
    updateActionBar(data);
    // The action bar height depends on what was just rendered, so re-fit once layout settles.
    requestAnimationFrame(fitBoard);
}

function startNewRound(round) {
    Object.assign(gameState, {
        status: 'starting',
        logHash: '',
        guessMode: false,
        pendingGuessPosition: null,
    });
    ['gameover-overlay', 'confirm-overlay', 'mycard-overlay'].forEach(id => showModal(id, false));
    closeZoom();
    $('guess-banner').classList.add('hidden');
    $('game-board').innerHTML = '';
    $('question-log').innerHTML = '';
    $('log-wrap').classList.add('empty');
    $('log-wrap').classList.remove('expanded', 'has-more');
    $('answer-thumb').innerHTML = '';
    delete $('answer-thumb').dataset.ready;
    $('btn-rematch').classList.remove('loading');
    showToast(t('new_round', { round }));
}

// Returns true when the opponent has left for good and the game can't go on.
function checkOpponentPresence(data) {
    const { opp } = playersOf(data);
    if (opp && opp.left) {
        stopPolling();
        exitGuessMode();
        closeZoom();
        ['mycard-overlay', 'confirm-overlay'].forEach(id => showModal(id, false));
        $('disconnect-banner').classList.add('hidden');
        $('left-title').textContent = t('opponent_left_title', { name: opp.name });
        showModal('left-overlay', true);
        return true;
    }
    const disconnected = opp && !opp.online;
    $('disconnect-banner').classList.toggle('hidden', !disconnected);
    if (disconnected) $('disconnect-banner').textContent = t('opponent_disconnected', { name: opp.name });
    return false;
}

function playersOf(data) {
    return {
        me: data.players.find(p => p.slot === data.mySlot),
        opp: data.players.find(p => p.slot !== data.mySlot),
    };
}

function updateRematchUI(data) {
    const { me, opp } = playersOf(data);
    const btn = $('btn-rematch');
    const status = $('rematch-status');
    const oppName = opp ? opp.name : t('opponent');

    if (me && opp) {
        $('score-line').innerHTML = `<span>${escapeHtml(me.name)}</span><strong>${me.wins} – ${opp.wins}</strong><span>${escapeHtml(opp.name)}</span>`;
    }

    let label = t('rematch', { name: oppName });
    let message = '';
    let tone = '';
    let enabled = true;

    if (!opp || !opp.online) {
        message = t('opponent_left', { name: oppName });
        tone = 'warn';
        enabled = false;
    } else if (me.wantsRematch) {
        label = t('rematch_waiting', { name: oppName });
        enabled = false;
    } else if (opp.wantsRematch) {
        label = t('rematch_accept');
        message = t('rematch_invited', { name: oppName });
        tone = 'invite';
    }

    if (!btn.classList.contains('loading')) {
        btn.textContent = label;
        btn.disabled = !enabled;
    }
    status.textContent = message;
    status.className = 'rematch-status' + (tone ? ' ' + tone : '');
}

async function requestRematch() {
    const btn = $('btn-rematch');
    setBusy(btn, true);
    const result = await api('rematch', { token: gameState.token });
    setBusy(btn, false);
    if (result.error) showError(result.error);
    poll();
}

// --- Board ---
const HIDDEN_ICON = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
// Shown over a hidden character when it is enlarged (zoom) or in the rules illustration.
const hiddenBadge = () => `<span class="hidden-badge">${HIDDEN_ICON}<span>${escapeHtml(t('hidden_label'))}</span></span>`;

// Back of a turned-down card, as on the physical board game.
const CARD_BACK = `<div class="card-face card-back" aria-hidden="true"><img src="icons/favicon.svg" alt="" draggable="false"><span class="card-back-icon">${HIDDEN_ICON}</span></div>`;

function updateBoard() {
    const board = $('game-board');
    board.classList.toggle('guess-mode', gameState.guessMode);

    if (board.children.length !== gameState.cards.length) {
        board.innerHTML = '';
        gameState.cards.forEach((card, i) => {
            const el = document.createElement('div');
            el.className = 'char-card card-enter';
            el.setAttribute('role', 'button');
            el.tabIndex = 0;
            el.style.animationDelay = `${i * 0.025}s`;
            el.innerHTML = `<div class="card-avatar"><div class="card-flip">
                    <div class="card-face card-front">${renderCharacterSVG(card.card_data)}</div>${CARD_BACK}
                </div></div>
                <div class="card-name">${escapeHtml(card.card_data.name)}</div>
                <button type="button" class="card-zoom" aria-label="${escapeHtml(t('zoom'))}" title="${escapeHtml(t('zoom'))}">${ZOOM_ICON}</button>`;

            el.addEventListener('animationend', (e) => {
                if (e.target === el) el.classList.remove('card-enter');
            });
            el.querySelector('.card-zoom').addEventListener('click', (e) => {
                e.stopPropagation();
                openZoom(card.position);
            });
            attachLongPress(el, () => openZoom(card.position));
            el.addEventListener('click', () => {
                if (el.dataset.longPressed) {
                    delete el.dataset.longPressed;
                    return;
                }
                handleCardClick(card.position);
            });
            el.addEventListener('keydown', (e) => {
                if (e.target !== el) return;
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handleCardClick(card.position);
                } else if (e.key.toLowerCase() === 'z') {
                    openZoom(card.position);
                }
            });
            board.appendChild(el);
        });
        fitBoard();
    }

    gameState.cards.forEach((card, i) => {
        const el = board.children[i];
        el.classList.toggle('hidden-card', card.is_hidden);
        el.setAttribute('aria-pressed', String(card.is_hidden));
        el.setAttribute('aria-label', card.is_hidden ? `${card.card_data.name} (${t('hidden_label')})` : card.card_data.name);
    });
}

// Picks the column count that gives the largest cards while fitting the whole board in view.
const CARD_MAX = 170;
const CARD_MIN = 56;
const CARD_READABLE = 64;
function fitBoard() {
    const container = $('board-container');
    const board = $('game-board');
    const count = board.children.length;
    if (!count || !container.clientWidth) return;

    const style = getComputedStyle(container);
    const banners = [...container.children].filter(el => el !== board && !el.classList.contains('hidden'))
        .reduce((h, el) => h + el.offsetHeight + 12, 0);
    const width = container.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
    const height = container.clientHeight - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom) - banners;
    const small = width < 520;
    const gap = small ? 7 : 10;
    const nameHeight = small ? 21 : 27;

    const layouts = [3, 4, 5, 6, 8, 12];
    const byWidth = (cols) => (width - gap * (cols - 1)) / cols;
    let best = null;
    for (const cols of layouts) {
        const rows = Math.ceil(count / cols);
        const byHeight = (height - gap * (rows - 1)) / rows - nameHeight;
        const size = Math.min(byWidth(cols), byHeight, CARD_MAX);
        if (!best || size > best.size + 0.5) best = { cols, size };
    }
    // Too little room to show everything (tiny or very short screens): use as many columns as
    // still give readable cards, and let the board scroll.
    if (best.size < CARD_MIN) {
        const cols = [...layouts].reverse().find(c => byWidth(c) >= CARD_READABLE) || layouts[0];
        best = { cols, size: Math.min(byWidth(cols), CARD_MAX) };
    }

    board.style.setProperty('--cols', best.cols);
    board.style.setProperty('--gap', gap + 'px');
    board.style.setProperty('--board-w', Math.floor(best.size * best.cols + gap * (best.cols - 1)) + 'px');
    board.classList.toggle('compact', best.size < 96);
}

function handleCardClick(position) {
    const card = gameState.cards.find(c => c.position === position);
    if (!card) return;

    if (gameState.guessMode) {
        if (!card.is_hidden) askGuessConfirmation(card);
        return;
    }
    toggleCardHidden(card);
}

function askGuessConfirmation(card) {
    gameState.pendingGuessPosition = card.position;
    $('confirm-title').textContent = t('confirm_title', { name: card.card_data.name });
    $('confirm-warning').textContent = t('final_guess_warning');
    $('confirm-avatar').innerHTML = renderCharacterSVG(card.card_data);
    showModal('confirm-overlay', true);
}

function toggleCardHidden(card) {
    card.is_hidden = !card.is_hidden;
    updateBoard();
    api('toggle_card', { token: gameState.token, card_id: card.id }).then(result => {
        if (result.error) {
            card.is_hidden = !card.is_hidden;
            updateBoard();
            showError(result.error);
        }
    });
}

function attachLongPress(el, onLongPress) {
    let timer = null;
    let startX = 0, startY = 0;
    const cancel = () => { clearTimeout(timer); timer = null; };

    el.addEventListener('pointerdown', (e) => {
        if (e.pointerType === 'mouse' || e.target.closest('.card-zoom')) return;
        startX = e.clientX;
        startY = e.clientY;
        timer = setTimeout(() => {
            timer = null;
            el.dataset.longPressed = '1';
            if (navigator.vibrate) navigator.vibrate(15);
            onLongPress();
        }, 450);
    });
    el.addEventListener('pointermove', (e) => {
        if (timer && Math.hypot(e.clientX - startX, e.clientY - startY) > 10) cancel();
    });
    ['pointerup', 'pointercancel', 'pointerleave'].forEach(type => el.addEventListener(type, cancel));
    el.addEventListener('contextmenu', (e) => e.preventDefault());
}

// --- Zoom ---
const ZOOM_ICON = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.5 15.5L21 21M10.5 7.5v6M7.5 10.5h6"/></svg>`;
let zoomIndex = null;

function openZoom(position) {
    zoomIndex = gameState.cards.findIndex(c => c.position === position);
    if (zoomIndex < 0) return;
    renderZoom();
    showModal('zoom-overlay', true);
}

function closeZoom() {
    zoomIndex = null;
    showModal('zoom-overlay', false);
}

function stepZoom(delta) {
    if (zoomIndex === null) return;
    const n = gameState.cards.length;
    zoomIndex = (zoomIndex + delta + n) % n;
    renderZoom(delta);
}

function renderZoom(direction = 0) {
    const card = gameState.cards[zoomIndex];
    const avatar = $('zoom-avatar');
    avatar.innerHTML = renderCharacterSVG(card.card_data) + hiddenBadge();
    avatar.classList.toggle('is-hidden', card.is_hidden);
    $('zoom-name').classList.toggle('is-hidden', card.is_hidden);
    avatar.classList.remove('slide-left', 'slide-right');
    if (direction) {
        void avatar.offsetWidth;
        avatar.classList.add(direction > 0 ? 'slide-left' : 'slide-right');
    }
    $('zoom-name').textContent = card.card_data.name;
    $('zoom-counter').textContent = `${zoomIndex + 1} / ${gameState.cards.length}`;

    const btn = $('btn-zoom-toggle');
    if (gameState.guessMode) {
        btn.textContent = t('guess_this');
        btn.disabled = card.is_hidden;
        btn.className = 'btn btn-accent';
    } else {
        btn.textContent = card.is_hidden ? t('show_card') : t('hide_card');
        btn.disabled = false;
        btn.className = 'btn btn-secondary';
    }
}

function zoomAction() {
    const card = gameState.cards[zoomIndex];
    if (!card) return;
    if (gameState.guessMode) {
        closeZoom();
        askGuessConfirmation(card);
        return;
    }
    toggleCardHidden(card);
    renderZoom();
}

// --- Status & turn ---
function opponentName() {
    const data = gameState.lastData;
    const opp = data && data.players.find(p => p.slot !== gameState.slot);
    return opp ? opp.name : t('opponent');
}

function updateTurnIndicator(data) {
    const el = $('turn-indicator');
    const isMyTurn = data.currentTurn === data.mySlot;
    el.className = 'turn-indicator ' + (isMyTurn ? 'my-turn' : 'their-turn');
    el.textContent = isMyTurn ? t('your_turn') : t('their_turn', { name: opponentName() });
}

function updatePlayersStatus(players) {
    $('players-status').innerHTML = players.map(p =>
        `<span class="player-dot ${p.online ? 'online' : ''}" title="${escapeHtml(p.name)}"></span>`
    ).join('');
}

function updateLogToggleLabel() {
    const expanded = $('log-wrap').classList.contains('expanded');
    const btn = $('log-toggle');
    btn.setAttribute('aria-expanded', String(expanded));
    btn.title = expanded ? t('log_collapse') : t('log_expand');
    btn.setAttribute('aria-label', btn.title);
}

function toggleLog(force) {
    const wrap = $('log-wrap');
    wrap.classList.toggle('expanded', force);
    updateLogToggleLabel();
    const log = $('question-log');
    log.scrollTop = log.scrollHeight;
    requestAnimationFrame(fitBoard);
}

function updateQuestionLog(guesses) {
    const log = $('question-log');
    const items = guesses.slice(0, 30).reverse();

    const hash = currentLang + '|' + items.map(g => g.id + ':' + (g.answer || '')).join(',');
    if (hash === gameState.logHash) return;
    gameState.logHash = hash;

    log.innerHTML = items.map(g => {
        const side = g.player_slot === gameState.slot ? 'mine' : 'theirs';
        const who = `<span class="q-name">${escapeHtml(g.player_name)}</span>`;
        if (g.is_final_guess) {
            const guessed = cardAt(g.guessed_position);
            const name = guessed ? escapeHtml(guessed.name) : '?';
            const verdict = g.is_correct
                ? `<span class="q-answer yes">${t('correct')}</span>`
                : `<span class="q-answer no">${t('wrong')}</span>`;
            return `<div class="q-entry ${side} final">${who} ${t('guessed')} <strong>${name}</strong> — ${verdict}</div>`;
        }
        const answer = g.answer
            ? `<span class="q-answer ${g.answer}">${t(g.answer)}</span>`
            : `<span class="q-answer pending">${t('waiting_answer')}</span>`;
        return `<div class="q-entry ${side}">${who}: “${escapeHtml(g.question)}” — ${answer}</div>`;
    }).join('');

    const wrap = $('log-wrap');
    wrap.classList.toggle('empty', items.length === 0);
    $('side-log-empty').classList.toggle('hidden', items.length > 0);
    wrap.classList.toggle('has-more', items.length > 1);
    $('log-toggle-count').textContent = items.length;
    updateLogToggleLabel();
    log.scrollTop = log.scrollHeight;
}

function updateActionBar(data) {
    const inPerson = data.mode === 'inperson';
    const isMyTurn = data.currentTurn === data.mySlot;

    // The first player guessed right: the other one gets a single last guess for a draw.
    const lastChance = data.pendingWinner !== null && data.pendingWinner !== undefined;
    $('lastchance-section').classList.toggle('hidden', !lastChance || data.pendingWinner === data.mySlot);
    if (lastChance) {
        ['answer-section', 'ask-section', 'inperson-section'].forEach(id => $(id).classList.add('hidden'));
        const iAmWaiting = data.pendingWinner === data.mySlot;
        $('wait-section').classList.toggle('hidden', !iAmWaiting);
        if (iAmWaiting) {
            $('wait-section').textContent = t('last_chance_wait', { name: opponentName() });
            if (gameState.guessMode) exitGuessMode();
        } else {
            $('lastchance-title').textContent = t('last_chance_title', { name: opponentName() });
        }
        return;
    }

    $('inperson-section').classList.toggle('hidden', !inPerson);
    if (inPerson) {
        ['answer-section', 'ask-section', 'wait-section'].forEach(id => $(id).classList.add('hidden'));
        updateInPersonBar(isMyTurn);
        return;
    }
    const pending = (data.guesses || []).find(g => !g.is_final_guess && g.answer === null);
    const mustAnswer = pending && pending.player_slot !== data.mySlot;

    $('answer-section').classList.toggle('hidden', !mustAnswer);
    $('ask-section').classList.toggle('hidden', !(isMyTurn && !pending));
    $('wait-section').classList.toggle('hidden', mustAnswer || (isMyTurn && !pending));

    if (mustAnswer) {
        $('pending-question').innerHTML = `<span class="q-name">${escapeHtml(pending.player_name)}</span> ${t('asks')}: “${escapeHtml(pending.question)}”`;
        $('answer-section').dataset.guessId = pending.id;
        const mine = cardAt(gameState.myPosition);
        if (mine && !$('answer-thumb').dataset.ready) {
            $('answer-thumb').innerHTML = renderCharacterSVG(mine);
            $('answer-thumb').dataset.ready = '1';
        }
    } else if (isMyTurn && pending) {
        $('wait-section').textContent = t('waiting_their_answer', { name: opponentName() });
    } else if (!isMyTurn) {
        $('wait-section').textContent = t('waiting_their_question', { name: opponentName() });
    }

    if (!isMyTurn && gameState.guessMode) exitGuessMode();
}

function updateInPersonBar(isMyTurn) {
    const section = $('inperson-section');
    section.classList.toggle('mine', isMyTurn);
    section.classList.toggle('theirs', !isMyTurn);
    $('inperson-actions').classList.toggle('hidden', !isMyTurn);
    $('inperson-title').textContent = isMyTurn ? t('inperson_my_title') : t('inperson_their_title', { name: opponentName() });
    $('inperson-text').textContent = isMyTurn ? t('inperson_my_text') : t('inperson_their_text', { name: opponentName() });

    if (gameState.lastTurnMine === false && isMyTurn) {
        showToast(t('turn_passed_to_you'));
        if (navigator.vibrate) navigator.vibrate([40, 60, 40]);
    }
    gameState.lastTurnMine = isMyTurn;
    if (!isMyTurn && gameState.guessMode) exitGuessMode();
}

// --- Actions ---
async function askQuestion() {
    const input = $('question-input');
    const question = input.value.trim();
    if (!question) return showToast(t('enter_question'));

    const result = await api('ask_question', { token: gameState.token, question });
    if (result.error) return showError(result.error);

    input.value = '';
    poll();
}

async function answerQuestion(answer) {
    const buttons = document.querySelectorAll('#answer-section .btn');
    buttons.forEach(b => b.disabled = true);
    const result = await api('answer_question', {
        token: gameState.token,
        guess_id: $('answer-section').dataset.guessId,
        answer,
    });
    buttons.forEach(b => b.disabled = false);
    if (result.error) return showError(result.error);
    poll();
}

function enterGuessMode() {
    if (gameState.currentTurn !== gameState.slot) return showError('not_your_turn');
    gameState.guessMode = true;
    $('guess-banner').classList.remove('hidden');
    fitBoard();
    updateBoard();
}

function exitGuessMode() {
    gameState.guessMode = false;
    gameState.pendingGuessPosition = null;
    $('guess-banner').classList.add('hidden');
    showModal('confirm-overlay', false);
    updateBoard();
    fitBoard();
}

async function confirmFinalGuess() {
    const position = gameState.pendingGuessPosition;
    exitGuessMode();
    if (position === null) return;

    const result = await api('final_guess', { token: gameState.token, position });
    if (result.error) return showError(result.error);

    if (result.lastChance) showToast(t('last_chance_wait', { name: opponentName() }));
    poll();
}

async function concedeLastChance() {
    if (!confirm(t('concede_confirm', { name: opponentName() }))) return;
    const result = await api('concede', { token: gameState.token });
    if (result.error) return showError(result.error);
    poll();
}

// --- Own character ---
function renderMyCardThumb() {
    const mine = cardAt(gameState.myPosition);
    if (!mine) return;
    $('my-card-thumb').innerHTML = renderCharacterSVG(mine);
    $('side-me-thumb').innerHTML = renderCharacterSVG(mine);
    $('side-me-name').textContent = mine.name;
}

function renderSideScore(data) {
    const { me, opp } = playersOf(data);
    if (!me || !opp) return;
    $('side-score').innerHTML = `${escapeHtml(me.name)} – ${escapeHtml(opp.name)}<strong>${me.wins || 0} – ${opp.wins || 0}</strong>`;
}

// --- Question placeholder: rotating examples ---
const PLACEHOLDER_MS = 3200;
let placeholderIndex = 0;
function showNextExample() {
    const input = $('question-input');
    const ex = ASK_EXAMPLES[currentLang] || ASK_EXAMPLES.en;
    if (input.value || document.activeElement === input && input.placeholder) return;
    placeholderIndex = (placeholderIndex + 1) % ex.items.length;
    input.classList.remove('ph-in');
    void input.offsetWidth;
    input.placeholder = `${ex.prefix} ${ex.items[placeholderIndex]}`;
    input.classList.add('ph-in');
}

function resetExamples() {
    const ex = ASK_EXAMPLES[currentLang] || ASK_EXAMPLES.en;
    placeholderIndex = 0;
    $('question-input').placeholder = `${ex.prefix} ${ex.items[0]}`;
}

// --- Game rules (with illustrated examples built from real characters) ---
const DEMO_BASE = { eyeColor: 'brown', eyebrows: 'medium', nose: 'medium', mouth: 'smile', hat: 'none', freckles: false, makeup: 'none', earrings: 'none', facialHair: 'none' };
const DEMO_CHARACTERS = [
    { name: 'Emma', gender: 'female', faceShape: 'oval', skinTone: 'fair', hairStyle: 'bob', hairColor: 'red', shirtColor: 'purple', glasses: 'none' },
    { name: 'Noah', gender: 'male', faceShape: 'square', skinTone: 'brown', hairStyle: 'short', hairColor: 'black', shirtColor: 'yellow', glasses: 'round' },
    { name: 'Mia', gender: 'female', faceShape: 'heart', skinTone: 'tan', hairStyle: 'long-straight', hairColor: 'blonde', shirtColor: 'green', glasses: 'none' },
    { name: 'Leo', gender: 'male', faceShape: 'round', skinTone: 'light', hairStyle: 'curly', hairColor: 'brown', shirtColor: 'blue', glasses: 'none', facialHair: 'beard' },
    { name: 'Ivy', gender: 'female', faceShape: 'oval', skinTone: 'dark', hairStyle: 'braids', hairColor: 'black', shirtColor: 'orange', glasses: 'square' },
    { name: 'Max', gender: 'male', faceShape: 'long', skinTone: 'medium', hairStyle: 'side-part', hairColor: 'gray', shirtColor: 'red', glasses: 'none', hat: 'cap' },
    { name: 'Zoe', gender: 'female', faceShape: 'round', skinTone: 'light', hairStyle: 'ponytail', hairColor: 'brown', shirtColor: 'blue', glasses: 'round' },
    { name: 'Kai', gender: 'male', faceShape: 'oval', skinTone: 'tan', hairStyle: 'buzz', hairColor: 'black', shirtColor: 'green', glasses: 'none' },
].map(c => ({ ...DEMO_BASE, ...c }));
const demo = (name) => DEMO_CHARACTERS.find(c => c.name === name);

const RULE_ICONS = {
    start: '<path d="M5 21V4M5 4h11l-2 4 2 4H5"/>',
    q: '<path d="M21 12a8 8 0 01-11.6 7.1L4 20l1-4.6A8 8 0 1121 12z"/>',
    ok: '<path d="M5 12l5 5L20 7"/>',
    ko: '<path d="M6 6l12 12M18 6L6 18"/>',
    win: '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4zM17 6h3a3 3 0 01-3 4M7 6H4a3 3 0 003 4"/>',
    draw: '<path d="M5 9h14M5 15h14"/>',
    clock: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
};
const ruleIcon = (kind) => `<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${RULE_ICONS[kind]}</svg>`;

function renderExampleTimeline(title, steps, kinds) {
    const rows = steps.map((text, i) => {
        const [kind, who] = kinds[i].split(':');
        const avatar = who ? `<span class="tl-avatar">${renderCharacterSVG(demo(who))}</span>` : '';
        return `<li class="tl-step tl-${kind}"><span class="tl-icon">${ruleIcon(kind)}</span><span class="tl-text">${escapeHtml(text)}</span>${avatar}</li>`;
    }).join('');
    return `<figure class="rule-example"><figcaption>${escapeHtml(title)}</figcaption><ol class="timeline">${rows}</ol></figure>`;
}

function renderRules() {
    const r = RULES[currentLang] || RULES.en;
    const ex = ASK_EXAMPLES[currentLang] || ASK_EXAMPLES.en;
    const miniBoard = DEMO_CHARACTERS.map(c => {
        const out = c.glasses !== 'none';
        return `<div class="mini-card${out ? ' out' : ''}"><div class="mini-face">${renderCharacterSVG(c)}${out ? `<span class="mini-badge">${HIDDEN_ICON}</span>` : ''}</div><span class="mini-name">${escapeHtml(c.name)}</span></div>`;
    }).join('');

    $('rules-title').textContent = r.title;
    $('rules-body').innerHTML = `
        <section><h4>${escapeHtml(r.goal_t)}</h4><p>${escapeHtml(r.goal)}</p></section>
        <section><h4>${escapeHtml(r.setup_t)}</h4><p>${escapeHtml(r.setup)}</p></section>
        <section>
            <h4>${escapeHtml(r.turn_t)}</h4>
            <ul class="rule-list"><li>${escapeHtml(r.turn_online)}</li><li>${escapeHtml(r.turn_inperson)}</li></ul>
            <p>${escapeHtml(r.turn_hide)}</p>
            <figure class="rule-shot">
                <div class="shot-chat">
                    <div class="q-entry mine"><span class="q-name">Alice</span>: “${escapeHtml(ex.items[0])}” — <span class="q-answer no">${escapeHtml(t('no'))}</span></div>
                </div>
                <div class="mini-board">${miniBoard}</div>
                <figcaption>${escapeHtml(r.ex_question)}</figcaption>
            </figure>
        </section>
        <section>
            <h4>${escapeHtml(r.guess_t)}</h4>
            <p>${escapeHtml(r.guess)}</p>
            <ul class="rule-cases">
                <li class="case-ko"><span class="tl-icon">${ruleIcon('ko')}</span><span>${escapeHtml(r.end_wrong)}</span></li>
                <li class="case-win"><span class="tl-icon">${ruleIcon('win')}</span><span>${escapeHtml(r.end_second)}</span></li>
                <li class="case-clock"><span class="tl-icon">${ruleIcon('clock')}</span><span>${escapeHtml(r.end_first)}</span></li>
                <li class="case-draw"><span class="tl-icon">${ruleIcon('draw')}</span><span>${escapeHtml(r.end_draw)}</span></li>
            </ul>
        </section>
        <section>
            <h4>${escapeHtml(r.examples_t)}</h4>
            <div class="rule-examples">
                ${renderExampleTimeline(r.ex1_t, r.ex1, ['start', 'ok:Noah', 'ok:Mia', 'draw'])}
                ${renderExampleTimeline(r.ex2_t, r.ex2, ['start', 'q', 'ok:Noah', 'win'])}
                ${renderExampleTimeline(r.ex3_t, r.ex3, ['ko:Mia', 'win'])}
            </div>
        </section>
        <section><h4>${escapeHtml(r.rematch_t)}</h4><p>${escapeHtml(r.rematch)}</p></section>`;
}

function openRules() {
    renderRules();
    showModal('rules-overlay', true);
    $('rules-overlay').querySelector('.modal-content').scrollTop = 0;
}

// --- Help & release notes ---
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

function openReleaseNotes() {
    renderReleaseNotes();
    showModal('release-overlay', true);
}

async function shareInvite() {
    try {
        await navigator.share({ title: 'FaceGuess', text: t('share_text', { code: gameState.code }), url: inviteLink() });
    } catch (e) {
        if (e && e.name !== 'AbortError') copyText(inviteLink(), t('link_copied'));
    }
}

function openMyCard() {
    const mine = cardAt(gameState.myPosition);
    if (!mine) return;
    $('mycard-display').innerHTML = renderCharacterSVG(mine);
    $('mycard-name').textContent = mine.name;
    showModal('mycard-overlay', true);
}

// --- Game over ---
// Explains how the round was decided: a correct guess, a wrong guess, a missed last chance or a draw.
function gameOverText(data, won, theirs) {
    const opp = opponentName();
    if (data.winnerSlot === 0) return t('draw_text');

    const finals = (data.guesses || []).filter(g => g.is_final_guess);
    const last = finals[0];
    const hadLastChance = finals.length > 1 && finals[1].is_correct;

    if (won) {
        if (last && last.player_slot !== gameState.slot && !last.is_correct) {
            return hadLastChance ? t('won_last_chance', { name: opp }) : t('won_opponent_mistake', { name: opp });
        }
        return t('correct_guess', { name: theirs ? theirs.name : '' });
    }
    if (last && last.player_slot === gameState.slot && !last.is_correct) {
        return hadLastChance ? t('lost_last_chance', { name: opp }) : t('lost_wrong_guess', { name: opp });
    }
    return t('opponent_won', { name: opp });
}

const GAMEOVER_ICONS = {
    win: `<svg viewBox="0 0 60 60" width="60" height="60"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--green)" stroke-width="3"/><path d="M18 30l8 8 16-16" fill="none" stroke="var(--green)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    lose: `<svg viewBox="0 0 60 60" width="60" height="60"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--red)" stroke-width="3"/><path d="M20 20l20 20M40 20l-20 20" fill="none" stroke="var(--red)" stroke-width="3" stroke-linecap="round"/></svg>`,
    draw: `<svg viewBox="0 0 60 60" width="60" height="60"><circle cx="30" cy="30" r="28" fill="none" stroke="var(--yellow)" stroke-width="3"/><path d="M19 25h22M19 35h22" fill="none" stroke="var(--yellow)" stroke-width="3" stroke-linecap="round"/></svg>`,
};

function showGameOver(data) {
    const draw = data.winnerSlot === 0;
    const won = data.winnerSlot === gameState.slot;
    const mine = cardAt(gameState.myPosition);
    const theirs = cardAt(data.opponentPosition);

    $('gameover-title').textContent = draw ? t('draw_title') : (won ? t('you_win') : t('you_lose'));
    $('gameover-text').textContent = gameOverText(data, won, theirs);
    $('gameover-mine').innerHTML = mine ? renderCharacterSVG(mine) : '';
    $('gameover-theirs').innerHTML = theirs ? renderCharacterSVG(theirs) : '';
    $('gameover-icon').innerHTML = GAMEOVER_ICONS[draw ? 'draw' : (won ? 'win' : 'lose')];

    showModal('mycard-overlay', false);
    showModal('confirm-overlay', false);
    closeZoom();
    showModal('gameover-overlay', true);
    if (won) spawnConfetti();
}

function spawnConfetti() {
    const colors = ['#6C5CE7', '#A29BFE', '#00B894', '#FFEAA7', '#FF6B6B', '#fd79a8', '#fdcb6e'];
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

// --- Navigation ---
function backToLobby() {
    stopPolling();
    gameState = freshState();
    ['gameover-overlay', 'mycard-overlay', 'confirm-overlay', 'left-overlay'].forEach(id => showModal(id, false));
    $('disconnect-banner').classList.add('hidden');
    closeZoom();
    $('guess-banner').classList.add('hidden');
    $('game-board').innerHTML = '';
    $('question-log').innerHTML = '';
    $('log-wrap').classList.add('empty');
    $('log-wrap').classList.remove('expanded', 'has-more');
    $('my-card-thumb').innerHTML = '';
    $('side-me-thumb').innerHTML = '';
    $('side-me-name').textContent = '';
    $('side-score').innerHTML = '';
    $('side-log-empty').classList.remove('hidden');
    $('answer-thumb').innerHTML = '';
    delete $('answer-thumb').dataset.ready;
    $('room-code').value = '';
    $('btn-join').disabled = true;
    showScreen('lobby');
    goToStep(playerName ? 2 : 1);
}

async function leaveGame() {
    if (gameState.token) await api('leave', { token: gameState.token });
    backToLobby();
}

// --- Setup ---
function onLanguageChange() {
    if (playerName) $('greeting-text').textContent = t('greeting', { name: playerName });
    $('random-desc').textContent = t('random_desc', { language: languageName(currentLang) });
    if (!$('release-overlay').classList.contains('hidden')) renderReleaseNotes();
    if (!$('rules-overlay').classList.contains('hidden')) renderRules();
    document.querySelectorAll('.rules-link-label').forEach(el => { el.textContent = (RULES[currentLang] || RULES.en).title; });
    $('lang-current').textContent = currentLang.toUpperCase();
    resetExamples();
    document.querySelectorAll('.lang-option').forEach(o => {
        const active = o.dataset.lang === currentLang;
        o.classList.toggle('active', active);
        o.setAttribute('aria-selected', String(active));
    });
}

function renderLogoFaces() {
    const samples = [
        { name: 'A', gender: 'female', faceShape: 'oval', skinTone: 'tan', eyeColor: 'green', eyebrows: 'medium', nose: 'small', mouth: 'smile', hairColor: 'black', shirtColor: 'purple', glasses: 'none', hat: 'none', freckles: false, hairStyle: 'bob', facialHair: 'none', makeup: 'lipstick', earrings: 'hoops' },
        { name: 'B', gender: 'male', faceShape: 'square', skinTone: 'fair', eyeColor: 'blue', eyebrows: 'thick', nose: 'medium', mouth: 'grin', hairColor: 'red', shirtColor: 'yellow', glasses: 'round', hat: 'none', freckles: true, hairStyle: 'curly', facialHair: 'none', makeup: 'none', earrings: 'none' },
        { name: 'C', gender: 'male', faceShape: 'round', skinTone: 'dark', eyeColor: 'brown', eyebrows: 'medium', nose: 'medium', mouth: 'smile', hairColor: 'gray', shirtColor: 'green', glasses: 'none', hat: 'cap', freckles: false, hairStyle: 'short', facialHair: 'beard', makeup: 'none', earrings: 'none' },
    ];
    document.querySelectorAll('.logo-face').forEach((el, i) => { el.innerHTML = renderCharacterSVG(samples[i]); });
}

function init() {
    // The phone/tablet "How to play" sheet reuses the desktop list; clone before translations are applied.
    const howtoCopy = $('howto-list').cloneNode(true);
    howtoCopy.removeAttribute('id');
    $('howto-sheet-body').appendChild(howtoCopy);

    initI18n();
    renderLogoFaces();
    $('app-version').textContent = 'v' + APP_VERSION;
    $('btn-version').addEventListener('click', openReleaseNotes);
    $('btn-close-release').addEventListener('click', () => showModal('release-overlay', false));
    $('btn-howto').addEventListener('click', () => showModal('howto-overlay', true));
    $('btn-step-1').addEventListener('click', () => goToStep(1));
    $('btn-lang-menu').addEventListener('click', () => showModal('lang-overlay', true));
    $('btn-guess-last').addEventListener('click', enterGuessMode);
    document.querySelectorAll('[data-open-rules]').forEach(b => b.addEventListener('click', () => {
        showModal('howto-overlay', false);
        openRules();
    }));
    $('btn-close-rules').addEventListener('click', () => showModal('rules-overlay', false));
    setInterval(() => { if (!$('ask-section').classList.contains('hidden')) showNextExample(); }, PLACEHOLDER_MS);
    $('btn-concede').addEventListener('click', concedeLastChance);
    document.querySelectorAll('.lang-option').forEach(o => o.addEventListener('click', () => {
        setLanguage(o.dataset.lang);
        setTimeout(() => showModal('lang-overlay', false), 180);
    }));
    $('btn-close-howto').addEventListener('click', () => showModal('howto-overlay', false));
    $('side-me').addEventListener('click', openMyCard);
    $('btn-native-share').addEventListener('click', shareInvite);

    const nameInput = $('player-name');
    const codeInput = $('room-code');

    playerName = (storageGet('fg_name') || '').trim();
    nameInput.value = playerName;
    $('btn-continue').disabled = !playerName;

    const urlCode = normalizeCode(new URLSearchParams(location.search).get('code') || '');
    if (urlCode) {
        codeInput.value = urlCode;
        $('btn-join').disabled = urlCode.length !== 6;
        $('card-join').classList.add('highlight');
    }

    nameInput.addEventListener('input', () => {
        $('btn-continue').disabled = nameInput.value.trim() === '';
    });
    $('step-name').addEventListener('submit', (e) => {
        e.preventDefault();
        playerName = nameInput.value.trim().slice(0, 30);
        if (!playerName) return;
        storageSet('fg_name', playerName);
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
    $('btn-guess-inperson').addEventListener('click', enterGuessMode);
    $('btn-end-turn').addEventListener('click', endTurn);
    $('log-toggle').addEventListener('click', () => toggleLog());
    $('question-log').addEventListener('click', () => {
        if ($('log-wrap').classList.contains('has-more') && !$('log-wrap').classList.contains('expanded')) toggleLog(true);
    });
    if ('ResizeObserver' in window) new ResizeObserver(() => fitBoard()).observe($('board-container'));
    else window.addEventListener('resize', fitBoard);
    $('btn-left-home').addEventListener('click', leaveGame);
    onLanguageChange();

    $('btn-copy-code').addEventListener('click', () => copyText(gameState.code, t('copied')));
    $('btn-share-link').addEventListener('click', () => copyText(inviteLink(), t('link_copied')));
    $('btn-leave-waiting').addEventListener('click', leaveGame);
    $('btn-leave-game').addEventListener('click', () => {
        if (gameState.status !== 'playing' || confirm(t('leave_confirm'))) leaveGame();
    });

    $('ask-section').addEventListener('submit', (e) => { e.preventDefault(); askQuestion(); });
    $('btn-guess').addEventListener('click', enterGuessMode);
    $('btn-cancel-guess').addEventListener('click', exitGuessMode);
    document.querySelectorAll('#answer-section [data-answer]').forEach(b =>
        b.addEventListener('click', () => answerQuestion(b.dataset.answer)));

    $('btn-my-card').addEventListener('click', openMyCard);
    $('answer-thumb').addEventListener('click', openMyCard);
    $('btn-close-mycard').addEventListener('click', () => showModal('mycard-overlay', false));
    $('btn-confirm-no').addEventListener('click', () => {
        gameState.pendingGuessPosition = null;
        showModal('confirm-overlay', false);
    });
    $('btn-confirm-yes').addEventListener('click', confirmFinalGuess);
    $('btn-rematch').addEventListener('click', requestRematch);
    $('btn-new-opponent').addEventListener('click', leaveGame);

    $('btn-zoom-close').addEventListener('click', closeZoom);
    $('btn-zoom-prev').addEventListener('click', () => stepZoom(-1));
    $('btn-zoom-next').addEventListener('click', () => stepZoom(1));
    $('btn-zoom-toggle').addEventListener('click', zoomAction);
    document.addEventListener('keydown', (e) => {
        if (zoomIndex === null) return;
        if (e.key === 'ArrowLeft') stepZoom(-1);
        else if (e.key === 'ArrowRight') stepZoom(1);
        else if (e.key === 'Escape') closeZoom();
    });
    let swipeX = null;
    $('zoom-avatar').addEventListener('pointerdown', (e) => { swipeX = e.clientX; });
    $('zoom-avatar').addEventListener('pointerup', (e) => {
        if (swipeX === null) return;
        const dx = e.clientX - swipeX;
        swipeX = null;
        if (Math.abs(dx) > 40) stepZoom(dx < 0 ? 1 : -1);
    });

    document.querySelectorAll('.modal').forEach(m => m.addEventListener('click', (e) => {
        if (e.target !== m || m.id === 'gameover-overlay' || m.id === 'left-overlay') return;
        if (m.id === 'zoom-overlay') closeZoom();
        else showModal(m.id, false);
    }));

    goToStep(playerName ? 2 : 1);
}

window.addEventListener('beforeunload', () => {
    if (gameState.token) {
        navigator.sendBeacon(API_URL, new URLSearchParams({ action: 'leave', token: gameState.token }));
    }
});

document.addEventListener('DOMContentLoaded', init);
