<?php
header('Content-Type: application/json');
// An unexpected error answers with a generic message: the details (SQL, paths) only go to the server log
set_exception_handler(function (Throwable $e) {
    error_log('[sea-battle] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) http_response_code(500);
    echo json_encode(['error' => 'Server error']);
});

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/engine.php';

const ONLINE_WINDOW_SECONDS = 15;
const LANGS = ['en', 'it', 'es', 'fr', 'de'];
const TIME_CONTROLS = [0, 180, 300, 600];
const RANDOM_TIME_CONTROL = 300; // 5 minutes per player
const PLACE_SECONDS = 120; // after this, a late player's fleet is placed at random
const ABANDON_SECONDS = 120; // an opponent gone this long loses automatically
const CHAT_MAX_LENGTH = 200;
const CHAT_BURST = 5; // messages allowed per player every 10 seconds
const CHAT_HISTORY = 50;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$actions = [
    'create_room' => 'createRoom',
    'join_room' => 'joinRoom',
    'find_match' => 'findMatch',
    'poll' => 'pollState',
    'place' => 'placeFleet',
    'fire' => 'fire',
    'resign' => 'resign',
    'rematch' => 'requestRematch',
    'claim_win' => 'claimWin',
    'leave' => 'leaveRoom',
    'send_message' => 'sendMessage',
];

if (isset($actions[$action])) $actions[$action]();
else echo json_encode(['error' => 'Unknown action']);

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
    $name = trim(preg_replace('/[\p{Cc}\p{Cf}]+/u', '', (string)$raw) ?? '');
    return mb_substr($name === '' ? $fallback : $name, 0, 30);
}

function generateRoomCode()
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
    return $code;
}

function fleetsOf($room)
{
    return [1 => SeaBattleEngine::decodeFleet($room['p1_fleet']), 2 => SeaBattleEngine::decodeFleet($room['p2_fleet'])];
}

// The battle so far: null before both fleets are down
function battleState($room)
{
    $fleets = fleetsOf($room);
    if (!$fleets[1] || !$fleets[2]) return null;
    return SeaBattleEngine::replay($fleets, SeaBattleEngine::parseShots($room['shots']), (int)$room['first_slot'], (bool)$room['bonus_shot']);
}

function slotToFire($room)
{
    $state = battleState($room);
    return $state ? $state['turn'] : (int)$room['first_slot'];
}

function clockRunning($room)
{
    return (int)$room['time_control'] > 0 && $room['status'] === 'playing';
}

function insertRoomWithHost($name, $timeControl, $bonus, $isPublic, $lang)
{
    global $pdo;
    for ($i = 0; $i < 10; $i++) {
        $code = generateRoomCode();
        try {
            $ms = $timeControl * 1000;
            $pdo->prepare("INSERT INTO sb_rooms (code, is_public, lang, time_control, bonus_shot, first_slot, shots, p1_ms, p2_ms) VALUES (?, ?, ?, ?, ?, 1, '', ?, ?)")
                ->execute([$code, $isPublic ? 1 : 0, $lang, $timeControl, $bonus ? 1 : 0, $ms, $ms]);
            $roomId = $pdo->lastInsertId();
            $token = bin2hex(random_bytes(32));
            $pdo->prepare("INSERT INTO sb_players (room_id, slot, token, name) VALUES (?, 1, ?, ?)")
                ->execute([$roomId, $token, $name]);
            return ['code' => $code, 'token' => $token];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) continue;
            throw $e;
        }
    }
    return null;
}

// Both players are in: the fleets go down first
function startPlacing($roomId)
{
    global $pdo;
    $pdo->prepare("UPDATE sb_rooms SET status = 'placing', place_started_ms = ? WHERE id = ?")->execute([nowMs(), $roomId]);
}

function addSecondPlayer($roomId, $name)
{
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $pdo->prepare("INSERT INTO sb_players (room_id, slot, token, name) VALUES (?, 2, ?, ?)")
        ->execute([$roomId, $token, $name]);
    startPlacing($roomId);
    return $token;
}

function createRoom()
{
    $name = cleanName($_POST['name'] ?? '', 'Player 1');
    $tc = (int)($_POST['time'] ?? 0);
    if (!in_array($tc, TIME_CONTROLS, true)) $tc = 0;
    $room = insertRoomWithHost($name, $tc, !empty($_POST['bonus']), false, null);
    if (!$room) {
        respondError('create_failed');
        return;
    }
    echo json_encode(['success' => true, 'code' => $room['code'], 'token' => $room['token']]);
}

function lockRoomByCode($code)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM sb_rooms WHERE code = ? FOR UPDATE");
    $stmt->execute([$code]);
    return $stmt->fetch();
}

function lockRoom($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM sb_rooms WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function fetchRoom($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM sb_rooms WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function joinRoom()
{
    global $pdo;
    $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $_POST['code'] ?? ''));
    $name = cleanName($_POST['name'] ?? '', 'Player 2');
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
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM sb_players WHERE room_id = ?");
            $stmt->execute([$room['id']]);
            if ((int)$stmt->fetchColumn() >= 2) $error = 'room_full';
        }
        if ($error) {
            $pdo->rollBack();
            respondError($error);
            return;
        }
        $token = addSecondPlayer($room['id'], $name);
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
        SELECT r.id FROM sb_rooms r
        JOIN sb_players host ON host.room_id = r.id AND host.slot = 1
        WHERE r.is_public = 1 AND r.status = 'waiting' AND r.lang = ? AND r.id <> ?
          AND host.is_online = 1 AND host.last_seen > NOW() - INTERVAL " . ONLINE_WINDOW_SECONDS . " SECOND
          AND NOT EXISTS (SELECT 1 FROM sb_players p2 WHERE p2.room_id = r.id AND p2.slot = 2)
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
        $room = insertRoomWithHost($name, RANDOM_TIME_CONTROL, false, true, $lang);
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

// Two players searching at the same moment: the newer one moves into the older room
function mergeWaitingPublicRoom($player)
{
    global $pdo;
    $pdo->beginTransaction();
    try {
        $own = lockRoom($player['room_id']);
        $targetId = $own && $own['status'] === 'waiting' && $own['is_public'] ? findOpenPublicRoom($player['lang'], (int)$player['room_id']) : null;
        if (!$targetId || $targetId > (int)$player['room_id']) {
            $pdo->rollBack();
            return false;
        }
        $pdo->prepare("UPDATE sb_players SET room_id = ?, slot = 2 WHERE id = ?")->execute([$targetId, $player['id']]);
        $pdo->prepare("UPDATE sb_rooms SET status = 'finished' WHERE id = ?")->execute([$player['room_id']]);
        startPlacing($targetId);
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
    $stmt = $pdo->prepare("SELECT p.*, r.lang, r.status AS room_status FROM sb_players p JOIN sb_rooms r ON r.id = p.room_id WHERE p.token = ? AND p.is_online = 1");
    $stmt->execute([$token]);
    $player = $stmt->fetch();
    if (!$player) return null;
    $pdo->prepare("UPDATE sb_players SET last_seen = NOW() WHERE id = ?")->execute([$player['id']]);
    return $player;
}

function finishGame($room, $winnerSlot, $reason)
{
    global $pdo;
    $result = $winnerSlot === 1 ? '1-0' : '0-1';
    $pdo->prepare("UPDATE sb_rooms SET status = 'finished', result = ?, reason = ?, p1_ms = ?, p2_ms = ? WHERE id = ?")
        ->execute([$result, $reason, $room['p1_ms'], $room['p2_ms'], $room['id']]);
    $pdo->prepare("UPDATE sb_players SET wins = wins + 1 WHERE room_id = ? AND slot = ?")->execute([$room['id'], $winnerSlot]);
}

function checkFlag(&$room)
{
    if (!clockRunning($room)) return false;
    $shooter = slotToFire($room);
    $key = $shooter === 1 ? 'p1_ms' : 'p2_ms';
    $left = (int)$room[$key] - (nowMs() - (int)$room['turn_started_ms']);
    if ($left > 0) return false;

    $room[$key] = 0;
    finishGame($room, other($shooter), 'timeout');
    $room['status'] = 'finished';
    return true;
}

// The battle starts once both fleets are down
function maybeStartBattle(&$room)
{
    global $pdo;
    if ($room['status'] !== 'placing' || !$room['p1_fleet'] || !$room['p2_fleet']) return;
    $pdo->prepare("UPDATE sb_rooms SET status = 'playing', turn_started_ms = ? WHERE id = ?")->execute([nowMs(), $room['id']]);
    $room['status'] = 'playing';
}

// A player who never confirms gets a random fleet, so the other one is not kept waiting
function checkPlacementTime(&$room)
{
    global $pdo;
    if ($room['status'] !== 'placing' || nowMs() - (int)$room['place_started_ms'] < PLACE_SECONDS * 1000) return;
    foreach ([1, 2] as $slot) {
        $key = "p{$slot}_fleet";
        if (!$room[$key]) {
            $room[$key] = SeaBattleEngine::encodeFleet(SeaBattleEngine::randomFleet());
            $pdo->prepare("UPDATE sb_rooms SET $key = ? WHERE id = ?")->execute([$room[$key], $room['id']]);
        }
    }
    maybeStartBattle($room);
}

// The opponent lost the connection (or left) for ABANDON_SECONDS: the player still here wins
function checkAbandon(&$room, $mySlot)
{
    global $pdo;
    if (!in_array($room['status'], ['placing', 'playing'], true)) return false;
    $stmt = $pdo->prepare("SELECT is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM sb_players WHERE room_id = ? AND slot = ?");
    $stmt->execute([$room['id'], other($mySlot)]);
    $opp = $stmt->fetch();
    if (!$opp || ($opp['is_online'] && (int)$opp['idle'] < ABANDON_SECONDS)) return false;
    finishGame($room, (int)$mySlot, 'abandon');
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
    try {
        $room = lockRoom($player['room_id']);
        checkPlacementTime($room);
        checkFlag($room);
        checkAbandon($room, (int)$player['slot']);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    $room = fetchRoom($player['room_id']);
    $mySlot = (int)$player['slot'];

    $stmt = $pdo->prepare("SELECT slot, name, wins, wants_rematch, is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM sb_players WHERE room_id = ? ORDER BY slot");
    $stmt->execute([$room['id']]);
    $players = array_map(fn($p) => [
        'slot' => (int)$p['slot'],
        'name' => $p['name'],
        'online' => (bool)$p['is_online'] && (int)$p['idle'] < ONLINE_WINDOW_SECONDS,
        'idle' => (int)$p['idle'],
        'left' => !$p['is_online'],
        'wins' => (int)$p['wins'],
        'wantsRematch' => (bool)$p['wants_rematch'],
    ], $stmt->fetchAll());

    $fleets = fleetsOf($room);
    $oppSlot = other($mySlot);
    $state = battleState($room);
    $shots = [];
    $oppSunk = [];
    if ($state) {
        foreach ($state['results'] as $r) {
            $shots[] = ['s' => $r['slot'], 'i' => $r['cell'], 'r' => $r['result'], 'k' => $r['ship']];
        }
        // The other fleet stays secret: only its sunk ships, or all of it once the game is over
        $show = $room['status'] === 'finished' ? array_keys(SeaBattleEngine::FLEET) : $state['sunk'][$oppSlot];
        foreach ($show as $s) $oppSunk[] = ['k' => $s] + $fleets[$oppSlot][$s];
    }

    $p1Ms = (int)$room['p1_ms'];
    $p2Ms = (int)$room['p2_ms'];
    $turn = $state ? $state['turn'] : (int)$room['first_slot'];
    if (clockRunning($room)) {
        $elapsed = nowMs() - (int)$room['turn_started_ms'];
        if ($turn === 1) $p1Ms = max(0, $p1Ms - $elapsed);
        else $p2Ms = max(0, $p2Ms - $elapsed);
    }

    echo json_encode([
        'status' => $room['status'],
        'code' => $room['code'],
        'isPublic' => (bool)$room['is_public'],
        'round' => (int)$room['round'],
        'firstSlot' => (int)$room['first_slot'],
        'mySlot' => $mySlot,
        'timeControl' => (int)$room['time_control'],
        'bonus' => (bool)$room['bonus_shot'],
        'myFleet' => $fleets[$mySlot],
        'oppReady' => $fleets[$oppSlot] !== null,
        'placeLeftMs' => $room['status'] === 'placing' ? max(0, PLACE_SECONDS * 1000 - (nowMs() - (int)$room['place_started_ms'])) : 0,
        'shots' => $shots,
        'oppShips' => $oppSunk,
        'turn' => $turn,
        'p1Ms' => $p1Ms,
        'p2Ms' => $p2Ms,
        'clockRunning' => clockRunning($room),
        'result' => $room['result'],
        'reason' => $room['reason'],
        'players' => $players,
        'messages' => chatMessages($room['id'], (int)($_GET['since'] ?? 0)),
    ]);
}

function placeFleet()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }
    $fleet = SeaBattleEngine::decodeFleet($_POST['fleet'] ?? '');
    if (!$fleet) {
        respondError('invalid_fleet');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        $key = 'p' . (int)$player['slot'] . '_fleet';
        if ($room['status'] !== 'placing' || $room[$key]) {
            $pdo->commit();
            respondError('not_placing');
            return;
        }
        $room[$key] = SeaBattleEngine::encodeFleet($fleet);
        $pdo->prepare("UPDATE sb_rooms SET $key = ? WHERE id = ?")->execute([$room[$key], $room['id']]);
        maybeStartBattle($room);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}

function fire()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    $cell = isset($_POST['cell']) ? (int)$_POST['cell'] : -1;
    $n = (int)($_POST['n'] ?? -1);
    if (!$player || $cell < 0 || $cell >= SeaBattleEngine::SIZE * SeaBattleEngine::SIZE) {
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
        $shots = SeaBattleEngine::parseShots($room['shots']);
        if ($n !== count($shots)) {
            $pdo->rollBack();
            respondError('out_of_sync');
            return;
        }
        if (slotToFire($room) !== (int)$player['slot']) {
            $pdo->rollBack();
            respondError('not_your_turn');
            return;
        }
        $shots[] = ['slot' => (int)$player['slot'], 'cell' => $cell];
        $state = SeaBattleEngine::replay(fleetsOf($room), $shots, (int)$room['first_slot'], (bool)$room['bonus_shot']);
        if ($state === null) {
            $pdo->rollBack();
            respondError('already_fired');
            return;
        }

        // Only the shooter's clock runs: a bonus shot keeps it running
        $now = nowMs();
        if ((int)$room['time_control'] > 0) {
            $key = (int)$player['slot'] === 1 ? 'p1_ms' : 'p2_ms';
            $room[$key] = max(0, (int)$room[$key] - ($now - (int)$room['turn_started_ms']));
        }
        $room['shots'] = SeaBattleEngine::formatShots($shots);
        $pdo->prepare("UPDATE sb_rooms SET shots = ?, p1_ms = ?, p2_ms = ?, turn_started_ms = ? WHERE id = ?")
            ->execute([$room['shots'], $room['p1_ms'], $room['p2_ms'], $now, $room['id']]);
        if ($state['winner'] !== 0) finishGame($room, $state['winner'], 'fleet_sunk');
        $pdo->commit();
        $last = end($state['results']);
        echo json_encode(['success' => true, 'result' => $last['result'], 'ship' => $last['ship']]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
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
        $stmt = $pdo->prepare("SELECT id, slot, body FROM sb_messages WHERE room_id = ? AND id > ? ORDER BY id DESC LIMIT " . CHAT_HISTORY);
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

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sb_messages WHERE room_id = ? AND slot = ? AND created_at > NOW() - INTERVAL 10 SECOND");
    $stmt->execute([$player['room_id'], $player['slot']]);
    if ((int)$stmt->fetchColumn() >= CHAT_BURST) {
        respondError('too_fast');
        return;
    }

    $pdo->prepare("INSERT INTO sb_messages (room_id, slot, body) VALUES (?, ?, ?)")->execute([$player['room_id'], $player['slot'], $text]);
    $id = (int)$pdo->lastInsertId();
    echo json_encode(['success' => true, 'message' => formatMessage(['id' => $id, 'slot' => $player['slot'], 'body' => $text])]);
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
        if (!in_array($room['status'], ['placing', 'playing'], true)) {
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

        $pdo->prepare("UPDATE sb_players SET wants_rematch = 1 WHERE id = ?")->execute([$player['id']]);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sb_players WHERE room_id = ? AND wants_rematch = 1 AND is_online = 1");
        $stmt->execute([$room['id']]);
        if ((int)$stmt->fetchColumn() >= 2) {
            // Both want a rematch: new fleets, and the other player fires first
            $ms = (int)$room['time_control'] * 1000;
            $pdo->prepare("UPDATE sb_rooms SET status = 'placing', round = round + 1, first_slot = ?, p1_fleet = NULL, p2_fleet = NULL, shots = '', p1_ms = ?, p2_ms = ?, place_started_ms = ?, turn_started_ms = 0, result = NULL, reason = NULL WHERE id = ?")
                ->execute([other($room['first_slot']), $ms, $ms, nowMs(), $room['id']]);
            $pdo->prepare("UPDATE sb_players SET wants_rematch = 0 WHERE room_id = ?")->execute([$room['id']]);
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
        if (!in_array($room['status'], ['placing', 'playing'], true)) {
            $pdo->commit();
            respondError('game_not_active');
            return;
        }

        $stmt = $pdo->prepare("SELECT is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM sb_players WHERE room_id = ? AND slot = ?");
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
        $pdo->prepare("UPDATE sb_players SET is_online = 0 WHERE id = ?")->execute([$player['id']]);
        if ($room && in_array($room['status'], ['placing', 'playing'], true)) {
            finishGame($room, other($player['slot']), 'abandon');
        } elseif ($room && $room['status'] === 'waiting') {
            // Nobody is left in the room: a late friend must not join an empty game
            $pdo->prepare("UPDATE sb_rooms SET status = 'finished' WHERE id = ?")->execute([$room['id']]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}
