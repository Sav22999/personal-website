<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/engine.php';

const ONLINE_WINDOW_SECONDS = 15;
const LANGS = ['en', 'it', 'es', 'fr', 'de'];
const TIME_CONTROLS = [0, 300, 600, 900];
const RANDOM_TIME_CONTROL = 600;
const ABANDON_SECONDS = 60;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create_room':  createRoom(); break;
    case 'join_room':    joinRoom(); break;
    case 'find_match':   findMatch(); break;
    case 'poll':         pollState(); break;
    case 'move':         makeMove(); break;
    case 'resign':       resign(); break;
    case 'offer_draw':   offerDraw(); break;
    case 'answer_draw':  answerDraw(); break;
    case 'rematch':      requestRematch(); break;
    case 'claim_win':    claimWin(); break;
    case 'leave':        leaveRoom(); break;
    default:
        echo json_encode(['error' => 'Unknown action']);
}

function respondError($code) { echo json_encode(['error' => $code]); }
function nowMs() { return (int)floor(microtime(true) * 1000); }
function other($slot) { return (int)$slot === 1 ? 2 : 1; }

function cleanName($raw, $fallback)
{
    $name = trim((string)$raw);
    return mb_substr($name === '' ? $fallback : $name, 0, 30);
}

function generateRoomCode()
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
    return $code;
}

function moveList($room) { return $room['moves'] === '' ? [] : explode(' ', $room['moves']); }
function sanList($room) { return $room['sans'] === '' ? [] : explode(' ', $room['sans']); }
// Whoever has white moves on even plies.
function slotToMove($room) { return count(moveList($room)) % 2 === 0 ? (int)$room['white_slot'] : other($room['white_slot']); }
// The clock only runs once both players have made their first move.
function clockRunning($room) { return (int)$room['time_control'] > 0 && $room['status'] === 'playing' && count(moveList($room)) >= 2; }

function insertRoomWithHost($name, $timeControl, $isPublic, $lang)
{
    global $pdo;
    for ($i = 0; $i < 10; $i++) {
        $code = generateRoomCode();
        try {
            $ms = $timeControl * 1000;
            $pdo->prepare("INSERT INTO chess_rooms (code, is_public, lang, time_control, white_slot, moves, sans, white_ms, black_ms) VALUES (?, ?, ?, ?, ?, '', '', ?, ?)")
                ->execute([$code, $isPublic ? 1 : 0, $lang, $timeControl, random_int(1, 2), $ms, $ms]);
            $roomId = $pdo->lastInsertId();
            $token = bin2hex(random_bytes(32));
            $pdo->prepare("INSERT INTO chess_players (room_id, slot, token, name) VALUES (?, 1, ?, ?)")->execute([$roomId, $token, $name]);
            return ['code' => $code, 'token' => $token];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) continue;
            throw $e;
        }
    }
    return null;
}

function startGame($roomId)
{
    global $pdo;
    $pdo->prepare("UPDATE chess_rooms SET status = 'playing', turn_started_ms = ? WHERE id = ?")->execute([nowMs(), $roomId]);
}

function addSecondPlayer($roomId, $name)
{
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $pdo->prepare("INSERT INTO chess_players (room_id, slot, token, name) VALUES (?, 2, ?, ?)")->execute([$roomId, $token, $name]);
    startGame($roomId);
    return $token;
}

function createRoom()
{
    $name = cleanName($_POST['name'] ?? '', 'Player 1');
    $tc = (int)($_POST['time'] ?? 0);
    if (!in_array($tc, TIME_CONTROLS, true)) $tc = 0;
    $room = insertRoomWithHost($name, $tc, false, null);
    if (!$room) { respondError('create_failed'); return; }
    echo json_encode(['success' => true, 'code' => $room['code'], 'token' => $room['token']]);
}

function lockRoomByCode($code)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM chess_rooms WHERE code = ? FOR UPDATE");
    $stmt->execute([$code]);
    return $stmt->fetch();
}

function lockRoom($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM chess_rooms WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function joinRoom()
{
    global $pdo;
    $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $_POST['code'] ?? ''));
    $name = cleanName($_POST['name'] ?? '', 'Player 2');
    if ($code === '') { respondError('code_required'); return; }

    $pdo->beginTransaction();
    try {
        $room = lockRoomByCode($code);
        $error = null;
        if (!$room) $error = 'room_not_found';
        elseif ($room['status'] === 'finished') $error = 'room_finished';
        else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM chess_players WHERE room_id = ?");
            $stmt->execute([$room['id']]);
            if ((int)$stmt->fetchColumn() >= 2) $error = 'room_full';
        }
        if ($error) { $pdo->rollBack(); respondError($error); return; }
        $token = addSecondPlayer($room['id'], $name);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true, 'code' => $code, 'token' => $token]);
}

// Oldest public room in the same language whose host is still connected and alone.
function findOpenPublicRoom($lang, $excludeRoomId = 0)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.id FROM chess_rooms r
        JOIN chess_players host ON host.room_id = r.id AND host.slot = 1
        WHERE r.is_public = 1 AND r.status = 'waiting' AND r.lang = ? AND r.id <> ?
          AND host.is_online = 1 AND host.last_seen > NOW() - INTERVAL " . ONLINE_WINDOW_SECONDS . " SECOND
          AND NOT EXISTS (SELECT 1 FROM chess_players p2 WHERE p2.room_id = r.id AND p2.slot = 2)
        ORDER BY r.id LIMIT 1 FOR UPDATE
    ");
    $stmt->execute([$lang, $excludeRoomId]);
    $id = $stmt->fetchColumn();
    return $id ? (int)$id : null;
}

function findMatch()
{
    global $pdo;
    $name = cleanName($_POST['name'] ?? '', 'Player');
    $lang = in_array($_POST['lang'] ?? '', LANGS, true) ? $_POST['lang'] : 'en';

    $pdo->beginTransaction();
    try {
        $roomId = findOpenPublicRoom($lang);
        if ($roomId) {
            $token = addSecondPlayer($roomId, $name);
            $pdo->commit();
            echo json_encode(['success' => true, 'token' => $token, 'matched' => true]);
            return;
        }
        $room = insertRoomWithHost($name, RANDOM_TIME_CONTROL, true, $lang);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    if (!$room) { respondError('create_failed'); return; }
    echo json_encode(['success' => true, 'token' => $room['token'], 'matched' => false]);
}

// Two players searching at the same moment can each open a room; the newer one moves into the older one.
function mergeWaitingPublicRoom($player)
{
    global $pdo;
    $pdo->beginTransaction();
    try {
        $own = lockRoom($player['room_id']);
        $targetId = $own && $own['status'] === 'waiting' ? findOpenPublicRoom($player['lang'], (int)$player['room_id']) : null;
        if (!$targetId || $targetId > (int)$player['room_id']) { $pdo->rollBack(); return false; }
        $pdo->prepare("UPDATE chess_players SET room_id = ?, slot = 2 WHERE id = ?")->execute([$targetId, $player['id']]);
        $pdo->prepare("UPDATE chess_rooms SET status = 'finished' WHERE id = ?")->execute([$player['room_id']]);
        startGame($targetId);
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        if (in_array($e->getCode(), ['40001', 'HY000'], true)) return false;
        throw $e;
    }
}

function getPlayer($token)
{
    global $pdo;
    if (!is_string($token) || $token === '') return null;
    $stmt = $pdo->prepare("SELECT p.*, r.lang, r.status AS room_status FROM chess_players p JOIN chess_rooms r ON r.id = p.room_id WHERE p.token = ? AND p.is_online = 1");
    $stmt->execute([$token]);
    $player = $stmt->fetch();
    if (!$player) return null;
    // is_online = 0 means the player left for good; a request still in flight must not bring them back.
    $pdo->prepare("UPDATE chess_players SET last_seen = NOW() WHERE id = ?")->execute([$player['id']]);
    return $player;
}

// $winnerSlot 0 = draw.
function finishGame($room, $winnerSlot, $reason)
{
    global $pdo;
    $result = $winnerSlot === 0 ? '1/2-1/2' : ($winnerSlot === (int)$room['white_slot'] ? '1-0' : '0-1');
    $pdo->prepare("UPDATE chess_rooms SET status = 'finished', result = ?, reason = ?, draw_offer_slot = NULL, white_ms = ?, black_ms = ? WHERE id = ?")
        ->execute([$result, $reason, $room['white_ms'], $room['black_ms'], $room['id']]);
    if ($winnerSlot === 0) {
        $pdo->prepare("UPDATE chess_players SET points2 = points2 + 1 WHERE room_id = ?")->execute([$room['id']]);
    } else {
        $pdo->prepare("UPDATE chess_players SET points2 = points2 + 2 WHERE room_id = ? AND slot = ?")->execute([$room['id'], $winnerSlot]);
    }
}

// Whether the given colour still has enough material to deliver mate (decides flag-fall results).
function canMate($state, $color)
{
    $minors = 0;
    foreach ($state['board'] as $p) {
        if ($p === null || ($color === 'w') !== ctype_upper($p)) continue;
        $t = strtolower($p);
        if (in_array($t, ['p', 'r', 'q'], true)) return true;
        if ($t === 'b' || $t === 'n') $minors++;
    }
    return $minors >= 2;
}

// Ends the game if the player to move has run out of time. Expects the room row to be locked.
function checkFlag(&$room)
{
    if (!clockRunning($room)) return false;
    $mover = slotToMove($room);
    $moverIsWhite = $mover === (int)$room['white_slot'];
    $key = $moverIsWhite ? 'white_ms' : 'black_ms';
    $left = (int)$room[$key] - (nowMs() - (int)$room['turn_started_ms']);
    if ($left > 0) return false;

    $room[$key] = 0;
    $replay = ChessEngine::replay(moveList($room));
    $opponentCanMate = $replay ? canMate($replay[0], $moverIsWhite ? 'b' : 'w') : true;
    finishGame($room, $opponentCanMate ? other($mover) : 0, $opponentCanMate ? 'timeout' : 'timeout_draw');
    $room['status'] = 'finished';
    return true;
}

function pollState()
{
    global $pdo;
    $token = $_GET['token'] ?? '';
    $player = getPlayer($token);
    if (!$player) { respondError('invalid_session'); return; }

    if ($player['room_status'] === 'waiting' && mergeWaitingPublicRoom($player)) $player = getPlayer($token);

    $pdo->beginTransaction();
    $room = lockRoom($player['room_id']);
    checkFlag($room);
    $pdo->commit();
    $room = fetchRoom($player['room_id']);

    $stmt = $pdo->prepare("SELECT slot, name, points2, wants_rematch, is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM chess_players WHERE room_id = ? ORDER BY slot");
    $stmt->execute([$room['id']]);
    $players = array_map(fn($p) => [
        'slot' => (int)$p['slot'],
        'name' => $p['name'],
        'color' => (int)$p['slot'] === (int)$room['white_slot'] ? 'w' : 'b',
        'online' => $p['is_online'] && (int)$p['idle'] < ONLINE_WINDOW_SECONDS,
        'idle' => (int)$p['idle'],
        'left' => !$p['is_online'],
        'points2' => (int)$p['points2'],
        'wantsRematch' => (bool)$p['wants_rematch'],
    ], $stmt->fetchAll());

    // Clocks as seen right now: the side to move is still losing time.
    $whiteMs = (int)$room['white_ms'];
    $blackMs = (int)$room['black_ms'];
    if (clockRunning($room)) {
        $elapsed = nowMs() - (int)$room['turn_started_ms'];
        if (slotToMove($room) === (int)$room['white_slot']) $whiteMs = max(0, $whiteMs - $elapsed);
        else $blackMs = max(0, $blackMs - $elapsed);
    }

    echo json_encode([
        'status' => $room['status'],
        'code' => $room['code'],
        'isPublic' => (bool)$room['is_public'],
        'round' => (int)$room['round'],
        'mySlot' => (int)$player['slot'],
        'myColor' => (int)$player['slot'] === (int)$room['white_slot'] ? 'w' : 'b',
        'timeControl' => (int)$room['time_control'],
        'moves' => moveList($room),
        'sans' => sanList($room),
        'whiteMs' => $whiteMs,
        'blackMs' => $blackMs,
        'clockRunning' => clockRunning($room),
        'drawOffer' => $room['draw_offer_slot'] === null ? null : (int)$room['draw_offer_slot'],
        'result' => $room['result'],
        'reason' => $room['reason'],
        'players' => $players,
    ]);
}

function fetchRoom($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM chess_rooms WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function makeMove()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    $uci = strtolower(preg_replace('/[^a-h1-8qrbn]/i', '', $_POST['uci'] ?? ''));
    $ply = (int)($_POST['ply'] ?? -1);
    if (!$player || strlen($uci) < 4) { respondError('invalid_request'); return; }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'playing' || checkFlag($room)) { $pdo->commit(); respondError('game_not_active'); return; }
        $moves = moveList($room);
        if (slotToMove($room) !== (int)$player['slot']) { $pdo->rollBack(); respondError('not_your_turn'); return; }
        if ($ply !== count($moves)) { $pdo->rollBack(); respondError('out_of_sync'); return; }

        $replay = ChessEngine::replay($moves);
        [$state, $keys] = $replay;
        $legal = ChessEngine::legalMoves($state);
        $move = null;
        foreach ($legal as $m) if (ChessEngine::toUci($m) === $uci) { $move = $m; break; }
        if (!$move) { $pdo->rollBack(); respondError('illegal_move'); return; }

        $san = ChessEngine::toSAN($state, $move, $legal);
        $next = ChessEngine::applyMove($state, $move);
        $keys[] = ChessEngine::positionKey($next);

        if (clockRunning($room)) {
            $key = (int)$player['slot'] === (int)$room['white_slot'] ? 'white_ms' : 'black_ms';
            $room[$key] = max(0, (int)$room[$key] - (nowMs() - (int)$room['turn_started_ms']));
        }

        $moves[] = $uci;
        $sans = sanList($room);
        $sans[] = $san;
        // Moving declines a draw offer made by the opponent.
        $drawOffer = $room['draw_offer_slot'] !== null && (int)$room['draw_offer_slot'] === (int)$player['slot'] ? $room['draw_offer_slot'] : null;
        $pdo->prepare("UPDATE chess_rooms SET moves = ?, sans = ?, white_ms = ?, black_ms = ?, turn_started_ms = ?, draw_offer_slot = ? WHERE id = ?")
            ->execute([implode(' ', $moves), implode(' ', $sans), $room['white_ms'], $room['black_ms'], nowMs(), $drawOffer, $room['id']]);

        $outcome = ChessEngine::outcome($next, $keys);
        if ($outcome['over']) {
            $winner = $outcome['result'] === '1/2-1/2' ? 0 : ($outcome['result'] === '1-0' ? (int)$room['white_slot'] : other($room['white_slot']));
            finishGame($room, $winner, $outcome['reason']);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true, 'san' => $san, 'over' => $outcome['over']]);
}

function resign()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) { respondError('invalid_session'); return; }
    $pdo->beginTransaction();
    $room = lockRoom($player['room_id']);
    if ($room['status'] !== 'playing') { $pdo->rollBack(); respondError('game_not_active'); return; }
    finishGame($room, other($player['slot']), 'resign');
    $pdo->commit();
    echo json_encode(['success' => true]);
}

function offerDraw()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) { respondError('invalid_session'); return; }
    $stmt = $pdo->prepare("UPDATE chess_rooms SET draw_offer_slot = ? WHERE id = ? AND status = 'playing' AND draw_offer_slot IS NULL");
    $stmt->execute([(int)$player['slot'], $player['room_id']]);
    echo json_encode(['success' => $stmt->rowCount() > 0]);
}

function answerDraw()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    $accept = ($_POST['accept'] ?? '') === '1';
    if (!$player) { respondError('invalid_session'); return; }
    $pdo->beginTransaction();
    $room = lockRoom($player['room_id']);
    if ($room['status'] !== 'playing' || $room['draw_offer_slot'] === null || (int)$room['draw_offer_slot'] === (int)$player['slot']) {
        $pdo->rollBack(); respondError('invalid_request'); return;
    }
    if ($accept) finishGame($room, 0, 'agreement');
    else $pdo->prepare("UPDATE chess_rooms SET draw_offer_slot = NULL WHERE id = ?")->execute([$room['id']]);
    $pdo->commit();
    echo json_encode(['success' => true]);
}

function requestRematch()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) { respondError('invalid_session'); return; }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'finished') { $pdo->rollBack(); echo json_encode(['success' => true, 'started' => $room['status'] === 'playing']); return; }
        $stmt = $pdo->prepare("SELECT * FROM chess_players WHERE room_id = ? AND id <> ?");
        $stmt->execute([$room['id'], $player['id']]);
        $opponent = $stmt->fetch();
        if (!$opponent || !$opponent['is_online']) { $pdo->rollBack(); respondError('opponent_left'); return; }

        $pdo->prepare("UPDATE chess_players SET wants_rematch = 1 WHERE id = ?")->execute([$player['id']]);
        $started = false;
        if ((int)$opponent['wants_rematch'] === 1) {
            $ms = (int)$room['time_control'] * 1000;
            // Colours swap every round.
            $pdo->prepare("UPDATE chess_rooms SET status = 'playing', round = round + 1, white_slot = ?, moves = '', sans = '', white_ms = ?, black_ms = ?, turn_started_ms = ?, draw_offer_slot = NULL, result = NULL, reason = NULL WHERE id = ?")
                ->execute([other($room['white_slot']), $ms, $ms, nowMs(), $room['id']]);
            $pdo->prepare("UPDATE chess_players SET wants_rematch = 0 WHERE room_id = ?")->execute([$room['id']]);
            $started = true;
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true, 'started' => $started]);
}

function leaveRoom()
{
    global $pdo;
    $token = $_POST['token'] ?? '';
    if ($token === '') { respondError('invalid_session'); return; }
    $stmt = $pdo->prepare("SELECT * FROM chess_players WHERE token = ? AND is_online = 1");
    $stmt->execute([$token]);
    $player = $stmt->fetch();
    if (!$player) { echo json_encode(['success' => true]); return; }

    $pdo->beginTransaction();
    try {
        $pdo->prepare("UPDATE chess_players SET is_online = 0 WHERE id = ?")->execute([$player['id']]);
        $room = lockRoom($player['room_id']);
        if ($room['status'] === 'waiting') {
            $pdo->prepare("UPDATE chess_rooms SET status = 'finished' WHERE id = ?")->execute([$room['id']]);
        } elseif ($room['status'] === 'playing') {
            // Leaving a game in progress counts as abandoning it.
            finishGame($room, other($player['slot']), 'abandon');
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}

// The opponent closed the page without leaving: after a minute offline the game can be claimed.
function claimWin()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) { respondError('invalid_session'); return; }
    $pdo->beginTransaction();
    $room = lockRoom($player['room_id']);
    $stmt = $pdo->prepare("SELECT is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM chess_players WHERE room_id = ? AND id <> ?");
    $stmt->execute([$room['id'], $player['id']]);
    $opponent = $stmt->fetch();
    if ($room['status'] !== 'playing' || !$opponent || ($opponent['is_online'] && (int)$opponent['idle'] < ABANDON_SECONDS)) {
        $pdo->rollBack(); respondError('cannot_claim'); return;
    }
    finishGame($room, (int)$player['slot'], 'abandon');
    $pdo->commit();
    echo json_encode(['success' => true]);
}
