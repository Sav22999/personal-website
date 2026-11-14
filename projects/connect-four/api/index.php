<?php
header('Content-Type: application/json');
// An unexpected error answers with a generic message: the details (SQL, paths) only go to the server log
set_exception_handler(function (Throwable $e) {
    error_log('[connect-four] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) http_response_code(500);
    echo json_encode(['error' => 'Server error']);
});

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/engine.php';

const ONLINE_WINDOW_SECONDS = 15;
const LANGS = ['en', 'it', 'es', 'fr', 'de'];
const TIME_CONTROLS = [0, 180, 300, 600];
const RANDOM_TIME_CONTROL = 300; // 5 minutes per player
const ABANDON_SECONDS = 60;
const CHAT_MAX_LENGTH = 200;
const CHAT_BURST = 5; // messages allowed per player every 10 seconds
const CHAT_HISTORY = 50;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create_room':
        createRoom();
        break;
    case 'join_room':
        joinRoom();
        break;
    case 'find_match':
        findMatch();
        break;
    case 'poll':
        pollState();
        break;
    case 'move':
        makeMove();
        break;
    case 'resign':
        resign();
        break;
    case 'offer_draw':
        offerDraw();
        break;
    case 'answer_draw':
        answerDraw();
        break;
    case 'rematch':
        requestRematch();
        break;
    case 'claim_win':
        claimWin();
        break;
    case 'leave':
        leaveRoom();
        break;
    case 'send_message':
        sendMessage();
        break;
    default:
        echo json_encode(['error' => 'Unknown action']);
}

function respondError($code)
{
    echo json_encode(['error' => $code]);
}

function nowMs()
{
    return (int)floor(microtime(true) * 1000);
}

function other($slot)
{
    return (int)$slot === 1 ? 2 : 1;
}

function cleanName($raw, $fallback)
{
    $name = trim((string)$raw);
    return mb_substr($name === '' ? $fallback : $name, 0, 30);
}

function cleanDiscColor($raw, $fallback)
{
    $color = preg_replace('/[^a-zA-Z0-9_\-#]/', '', (string)$raw);
    return mb_substr($color === '' ? $fallback : $color, 0, 20);
}

function generateRoomCode()
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
    return $code;
}

function moveList($room)
{
    $moves = trim((string)$room['moves']);
    return $moves === '' ? [] : explode(' ', $moves);
}

function slotToMove($room)
{
    $moves = moveList($room);
    return count($moves) % 2 === 0 ? (int)$room['first_slot'] : other($room['first_slot']);
}

function clockRunning($room)
{
    return (int)$room['time_control'] > 0 && $room['status'] === 'playing';
}

function insertRoomWithHost($name, $discColor, $timeControl, $isPublic, $lang)
{
    global $pdo;
    for ($i = 0; $i < 10; $i++) {
        $code = generateRoomCode();
        try {
            $ms = $timeControl * 1000;
            $pdo->prepare("INSERT INTO c4_rooms (code, is_public, lang, time_control, first_slot, moves, p1_ms, p2_ms) VALUES (?, ?, ?, ?, 1, '', ?, ?)")
                ->execute([$code, $isPublic ? 1 : 0, $lang, $timeControl, $ms, $ms]);
            $roomId = $pdo->lastInsertId();
            $token = bin2hex(random_bytes(32));
            $pdo->prepare("INSERT INTO c4_players (room_id, slot, token, name, disc_color) VALUES (?, 1, ?, ?, ?)")
                ->execute([$roomId, $token, $name, $discColor]);
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
    $pdo->prepare("UPDATE c4_rooms SET status = 'playing', turn_started_ms = ? WHERE id = ?")->execute([nowMs(), $roomId]);
}

function addSecondPlayer($roomId, $name, $discColor)
{
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $pdo->prepare("INSERT INTO c4_players (room_id, slot, token, name, disc_color) VALUES (?, 2, ?, ?, ?)")
        ->execute([$roomId, $token, $name, $discColor]);
    startGame($roomId);
    return $token;
}

function createRoom()
{
    $name = cleanName($_POST['name'] ?? '', 'Player 1');
    $color = cleanDiscColor($_POST['disc_color'] ?? '', 'fuchsia');
    $tc = (int)($_POST['time'] ?? 0);
    if (!in_array($tc, TIME_CONTROLS, true)) $tc = 0;
    $room = insertRoomWithHost($name, $color, $tc, false, null);
    if (!$room) {
        respondError('create_failed');
        return;
    }
    echo json_encode(['success' => true, 'code' => $room['code'], 'token' => $room['token']]);
}

function lockRoomByCode($code)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM c4_rooms WHERE code = ? FOR UPDATE");
    $stmt->execute([$code]);
    return $stmt->fetch();
}

function lockRoom($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM c4_rooms WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function fetchRoom($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM c4_rooms WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function joinRoom()
{
    global $pdo;
    $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $_POST['code'] ?? ''));
    $name = cleanName($_POST['name'] ?? '', 'Player 2');
    $color = cleanDiscColor($_POST['disc_color'] ?? '', 'gold');
    if ($code === '') {
        respondError('code_required');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoomByCode($code);
        $error = null;
        if (!$room) $error = 'room_not_found';
        elseif ($room['status'] === 'finished') $error = 'room_finished';
        else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM c4_players WHERE room_id = ?");
            $stmt->execute([$room['id']]);
            if ((int)$stmt->fetchColumn() >= 2) $error = 'room_full';
        }
        if ($error) {
            $pdo->rollBack();
            respondError($error);
            return;
        }
        $token = addSecondPlayer($room['id'], $name, $color);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true, 'code' => $code, 'token' => $token]);
}

function findOpenPublicRoom($lang, $excludeRoomId = 0)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.id FROM c4_rooms r
        JOIN c4_players host ON host.room_id = r.id AND host.slot = 1
        WHERE r.is_public = 1 AND r.status = 'waiting' AND r.lang = ? AND r.id <> ?
          AND host.is_online = 1 AND host.last_seen > NOW() - INTERVAL " . ONLINE_WINDOW_SECONDS . " SECOND
          AND NOT EXISTS (SELECT 1 FROM c4_players p2 WHERE p2.room_id = r.id AND p2.slot = 2)
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
    $color = cleanDiscColor($_POST['disc_color'] ?? '', 'fuchsia');
    $lang = in_array($_POST['lang'] ?? '', LANGS, true) ? $_POST['lang'] : 'en';

    $pdo->beginTransaction();
    try {
        $roomId = findOpenPublicRoom($lang);
        if ($roomId) {
            $token = addSecondPlayer($roomId, $name, $color);
            $pdo->commit();
            echo json_encode(['success' => true, 'token' => $token, 'matched' => true]);
            return;
        }
        $room = insertRoomWithHost($name, $color, RANDOM_TIME_CONTROL, true, $lang);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    if (!$room) {
        respondError('create_failed');
        return;
    }
    echo json_encode(['success' => true, 'token' => $room['token'], 'matched' => false]);
}

function mergeWaitingPublicRoom($player)
{
    global $pdo;
    $pdo->beginTransaction();
    try {
        $own = lockRoom($player['room_id']);
        $targetId = $own && $own['status'] === 'waiting' ? findOpenPublicRoom($player['lang'], (int)$player['room_id']) : null;
        if (!$targetId || $targetId > (int)$player['room_id']) {
            $pdo->rollBack();
            return false;
        }
        $pdo->prepare("UPDATE c4_players SET room_id = ?, slot = 2 WHERE id = ?")->execute([$targetId, $player['id']]);
        $pdo->prepare("UPDATE c4_rooms SET status = 'finished' WHERE id = ?")->execute([$player['room_id']]);
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
    $stmt = $pdo->prepare("SELECT p.*, r.lang, r.status AS room_status FROM c4_players p JOIN c4_rooms r ON r.id = p.room_id WHERE p.token = ? AND p.is_online = 1");
    $stmt->execute([$token]);
    $player = $stmt->fetch();
    if (!$player) return null;
    $pdo->prepare("UPDATE c4_players SET last_seen = NOW() WHERE id = ?")->execute([$player['id']]);
    return $player;
}

function finishGame($room, $winnerSlot, $reason, $winningLine = null)
{
    global $pdo;
    $result = $winnerSlot === 0 ? '1/2-1/2' : ($winnerSlot === 1 ? '1-0' : '0-1');
    $pdo->prepare("UPDATE c4_rooms SET status = 'finished', result = ?, reason = ?, winning_line = ?, draw_offer_slot = NULL, p1_ms = ?, p2_ms = ? WHERE id = ?")
        ->execute([$result, $reason, $winningLine, $room['p1_ms'], $room['p2_ms'], $room['id']]);
    if ($winnerSlot === 0) {
        $pdo->prepare("UPDATE c4_players SET points2 = points2 + 1 WHERE room_id = ?")->execute([$room['id']]);
    } else {
        $pdo->prepare("UPDATE c4_players SET points2 = points2 + 2 WHERE room_id = ? AND slot = ?")->execute([$room['id'], $winnerSlot]);
    }
}

function checkFlag(&$room)
{
    if (!clockRunning($room)) return false;
    $mover = slotToMove($room);
    $key = $mover === 1 ? 'p1_ms' : 'p2_ms';
    $left = (int)$room[$key] - (nowMs() - (int)$room['turn_started_ms']);
    if ($left > 0) return false;

    $room[$key] = 0;
    finishGame($room, other($mover), 'timeout');
    $room['status'] = 'finished';
    return true;
}

function pollState()
{
    global $pdo;
    $token = $_GET['token'] ?? '';
    $player = getPlayer($token);
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    if ($player['room_status'] === 'waiting' && mergeWaitingPublicRoom($player)) {
        $player = getPlayer($token);
    }

    $pdo->beginTransaction();
    $room = lockRoom($player['room_id']);
    checkFlag($room);
    $pdo->commit();
    $room = fetchRoom($player['room_id']);

    $stmt = $pdo->prepare("SELECT slot, name, disc_color, points2, wants_rematch, is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM c4_players WHERE room_id = ? ORDER BY slot");
    $stmt->execute([$room['id']]);
    $players = array_map(fn($p) => [
        'slot' => (int)$p['slot'],
        'name' => $p['name'],
        'discColor' => $p['disc_color'] ?: ($p['slot'] == 1 ? 'fuchsia' : 'gold'),
        'online' => (bool)$p['is_online'] && (int)$p['idle'] < ONLINE_WINDOW_SECONDS,
        'idle' => (int)$p['idle'],
        'left' => !$p['is_online'],
        'points2' => (int)$p['points2'],
        'wantsRematch' => (bool)$p['wants_rematch'],
    ], $stmt->fetchAll());

    $p1Ms = (int)$room['p1_ms'];
    $p2Ms = (int)$room['p2_ms'];
    if (clockRunning($room)) {
        $elapsed = nowMs() - (int)$room['turn_started_ms'];
        if (slotToMove($room) === 1) $p1Ms = max(0, $p1Ms - $elapsed);
        else $p2Ms = max(0, $p2Ms - $elapsed);
    }

    echo json_encode([
        'status' => $room['status'],
        'code' => $room['code'],
        'isPublic' => (bool)$room['is_public'],
        'round' => (int)$room['round'],
        'firstSlot' => (int)$room['first_slot'],
        'mySlot' => (int)$player['slot'],
        'timeControl' => (int)$room['time_control'],
        'moves' => moveList($room),
        'p1Ms' => $p1Ms,
        'p2Ms' => $p2Ms,
        'clockRunning' => clockRunning($room),
        'drawOffer' => $room['draw_offer_slot'] === null ? null : (int)$room['draw_offer_slot'],
        'result' => $room['result'],
        'reason' => $room['reason'],
        'winningLine' => $room['winning_line'],
        'players' => $players,
        'messages' => chatMessages($room['id'], (int)($_GET['since'] ?? 0)),
    ]);
}

function cleanMessage($raw)
{
    if (!is_string($raw)) return '';
    $text = preg_replace('/[\p{Cc}\p{Cf}]+/u', ' ', $raw) ?? '';
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    return mb_substr($text, 0, CHAT_MAX_LENGTH);
}

function formatMessage($row)
{
    return ['id' => (int)$row['id'], 'slot' => (int)$row['slot'], 'text' => $row['body']];
}

function chatMessages($roomId, $since)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, slot, body FROM c4_messages WHERE room_id = ? AND id > ? ORDER BY id DESC LIMIT " . CHAT_HISTORY);
        $stmt->execute([$roomId, max(0, $since)]);
        return array_map('formatMessage', array_reverse($stmt->fetchAll()));
    } catch (PDOException $e) {
        return [];
    }
}

function sendMessage()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }
    if ($player['room_status'] === 'waiting') {
        respondError('not_started');
        return;
    }
    $text = cleanMessage($_POST['text'] ?? '');
    if ($text === '') {
        respondError('empty_message');
        return;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM c4_messages WHERE room_id = ? AND slot = ? AND created_at > NOW() - INTERVAL 10 SECOND");
    $stmt->execute([$player['room_id'], $player['slot']]);
    if ((int)$stmt->fetchColumn() >= CHAT_BURST) {
        respondError('too_fast');
        return;
    }

    $pdo->prepare("INSERT INTO c4_messages (room_id, slot, body) VALUES (?, ?, ?)")->execute([$player['room_id'], $player['slot'], $text]);
    $id = (int)$pdo->lastInsertId();
    echo json_encode(['success' => true, 'message' => formatMessage(['id' => $id, 'slot' => $player['slot'], 'body' => $text])]);
}

function makeMove()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    $col = isset($_POST['col']) ? (int)$_POST['col'] : -1;
    $ply = (int)($_POST['ply'] ?? -1);
    if (!$player || $col < 0 || $col >= ConnectFourEngine::COLS) {
        respondError('invalid_request');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'playing' || checkFlag($room)) {
            $pdo->commit();
            respondError('game_not_active');
            return;
        }
        $moves = moveList($room);
        if (slotToMove($room) !== (int)$player['slot']) {
            $pdo->rollBack();
            respondError('not_your_turn');
            return;
        }
        if ($ply !== count($moves)) {
            $pdo->rollBack();
            respondError('out_of_sync');
            return;
        }

        $tentativeMoves = array_merge($moves, [$col]);
        $replay = ConnectFourEngine::replay($tentativeMoves, (int)$room['first_slot']);
        if ($replay === null) {
            $pdo->rollBack();
            respondError('illegal_move');
            return;
        }

        // Deduct clock for player who just moved
        $now = nowMs();
        $p1Ms = (int)$room['p1_ms'];
        $p2Ms = (int)$room['p2_ms'];
        if ((int)$room['time_control'] > 0) {
            $elapsed = $now - (int)$room['turn_started_ms'];
            if ((int)$player['slot'] === 1) $p1Ms = max(0, $p1Ms - $elapsed);
            else $p2Ms = max(0, $p2Ms - $elapsed);
        }

        $newMovesStr = implode(' ', $tentativeMoves);
        $winningLineStr = ConnectFourEngine::formatWinningLine($replay['winning_line']);

        // The move is always stored, the winning one too: clients learn it from the next poll
        $pdo->prepare("UPDATE c4_rooms SET moves = ?, p1_ms = ?, p2_ms = ?, turn_started_ms = ?, draw_offer_slot = NULL WHERE id = ?")
            ->execute([$newMovesStr, $p1Ms, $p2Ms, $now, $room['id']]);

        $room['p1_ms'] = $p1Ms;
        $room['p2_ms'] = $p2Ms;
        if ($replay['winner'] !== 0) {
            finishGame($room, $replay['winner'], 'connect4', $winningLineStr);
        } elseif ($replay['is_full']) {
            finishGame($room, 0, 'draw_board');
        }

        $pdo->commit();
        echo json_encode([
            'success' => true,
            'col' => $col,
            'landed' => $replay['last_move'],
            'winner' => $replay['winner'],
            'winningLine' => $replay['winning_line'],
            'isFull' => $replay['is_full'],
        ]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function resign()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'playing') {
            $pdo->commit();
            respondError('game_not_active');
            return;
        }
        finishGame($room, other($player['slot']), 'resign');
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}

function offerDraw()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'playing') {
            $pdo->commit();
            respondError('game_not_active');
            return;
        }
        if ($room['draw_offer_slot'] !== null) {
            $pdo->commit();
            respondError('already_offered');
            return;
        }
        $pdo->prepare("UPDATE c4_rooms SET draw_offer_slot = ? WHERE id = ?")->execute([$player['slot'], $room['id']]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}

function answerDraw()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    $accept = !empty($_POST['accept']);
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'playing' || (int)$room['draw_offer_slot'] !== other($player['slot'])) {
            $pdo->commit();
            respondError('no_offer');
            return;
        }
        if ($accept) {
            finishGame($room, 0, 'agreement');
        } else {
            $pdo->prepare("UPDATE c4_rooms SET draw_offer_slot = NULL WHERE id = ?")->execute([$room['id']]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}

function requestRematch()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'finished') {
            $pdo->commit();
            respondError('game_not_finished');
            return;
        }

        $pdo->prepare("UPDATE c4_players SET wants_rematch = 1 WHERE id = ?")->execute([$player['id']]);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM c4_players WHERE room_id = ? AND wants_rematch = 1 AND is_online = 1");
        $stmt->execute([$room['id']]);
        if ((int)$stmt->fetchColumn() >= 2) {
            // Both want a rematch: reset game, swap starting player
            $newFirst = other($room['first_slot']);
            $ms = (int)$room['time_control'] * 1000;
            $pdo->prepare("UPDATE c4_rooms SET status = 'playing', round = round + 1, first_slot = ?, moves = '', p1_ms = ?, p2_ms = ?, turn_started_ms = ?, draw_offer_slot = NULL, result = NULL, reason = NULL, winning_line = NULL WHERE id = ?")
                ->execute([$newFirst, $ms, $ms, nowMs(), $room['id']]);
            $pdo->prepare("UPDATE c4_players SET wants_rematch = 0 WHERE room_id = ?")->execute([$room['id']]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}

function claimWin()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'playing') {
            $pdo->commit();
            respondError('game_not_active');
            return;
        }

        $stmt = $pdo->prepare("SELECT is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM c4_players WHERE room_id = ? AND slot = ?");
        $stmt->execute([$room['id'], other($player['slot'])]);
        $opp = $stmt->fetch();
        if (!$opp || ($opp['is_online'] && (int)$opp['idle'] < ABANDON_SECONDS)) {
            $pdo->commit();
            respondError('cannot_claim');
            return;
        }

        finishGame($room, (int)$player['slot'], 'abandon');
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}

function leaveRoom()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        $pdo->prepare("UPDATE c4_players SET is_online = 0 WHERE id = ?")->execute([$player['id']]);
        if ($room && $room['status'] === 'playing') {
            finishGame($room, other($player['slot']), 'abandon');
        } elseif ($room && $room['status'] === 'waiting') {
            // Nobody is left in the room: a late friend must not join an empty game
            $pdo->prepare("UPDATE c4_rooms SET status = 'finished' WHERE id = ?")->execute([$room['id']]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}
