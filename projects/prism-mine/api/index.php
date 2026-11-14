<?php
header('Content-Type: application/json');
// An unexpected error answers with a generic message: the details (SQL, paths) only go to the server log
set_exception_handler(function (Throwable $e) {
    error_log('[prism-mine] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) http_response_code(500);
    echo json_encode(['error' => 'Server error']);
});

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/engine.php';

// Duel: both players get the same set of gems and hide them in their own mine (like Sea Battle).
// On your turn you send one beam into the other mine or try an answer about it;
// a right answer wins, a wrong one passes the turn and counts as a strike (3 strikes lose).
// The `mine` column only holds the set of gems of the round; p1_mine and p2_mine are the two hidden mines.
const ONLINE_WINDOW_SECONDS = 15;
const LANGS = ['en', 'it', 'es', 'fr', 'de'];
const LEVELS = ['easy', 'medium', 'hard'];
const TIME_CONTROLS = [0, 30, 60, 120]; // seconds per turn; when they run out the turn passes
const RANDOM_TIME_CONTROL = 60;
const MAX_SKIPS_PER_POLL = 20;
const MAX_STRIKES = 3;
const PLACE_SECONDS = 120; // after this, a late player's gems are placed at random
const ABANDON_SECONDS = 120; // an opponent gone this long loses automatically
const CHAT_MAX_LENGTH = 200;
const CHAT_BURST = 5;
const CHAT_HISTORY = 50;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$actions = [
    'create_room' => 'createRoom',
    'join_room' => 'joinRoom',
    'find_match' => 'findMatch',
    'poll' => 'pollState',
    'place' => 'placeGems',
    'probe' => 'probe',
    'guess' => 'guess',
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

function actionList($room)
{
    $list = json_decode((string)$room['actions'], true);
    return is_array($list) ? $list : [];
}

// Every action passes the turn, except the right answer, which ends the game
function slotToAct($room)
{
    return count(actionList($room)) % 2 === 0 ? (int)$room['first_slot'] : other($room['first_slot']);
}

function clockRunning($room)
{
    return (int)$room['time_control'] > 0 && $room['status'] === 'playing';
}

// The size of the mine goes with the level: 6 x 6, 8 x 8 or 10 x 10
function mineSize($room)
{
    return PrismMineEngine::sizeOf((string)$room['level']);
}

function gemSet($room)
{
    return array_map(fn($g) => ['shape' => $g['shape'], 'color' => $g['color']], PrismMineEngine::decode($room['mine'], 0, mineSize($room)) ?? []);
}

// The mine hidden by a player, or null while it is not placed yet
function mineOf($room, $slot)
{
    return PrismMineEngine::decode($room["p{$slot}_mine"] ?? null, 0, mineSize($room));
}

// Ports a player has already lit in the other mine, as entry or exit: a second beam there tells nothing new
function usedPorts($room, $slot)
{
    $mine = mineOf($room, other($slot));
    $map = PrismMineEngine::buildMap($mine, mineSize($room));
    $used = [];
    foreach (actionList($room) as $a) {
        if ($a['t'] !== 'p' || $a['s'] !== (int)$slot) continue;
        $used[$a['p']] = true;
        $res = PrismMineEngine::trace($map, $mine, $a['p']);
        if ($res['exit'] !== null) $used[$res['exit']] = true;
    }
    return $used;
}

function insertRoomWithHost($name, $timeControl, $level, $isPublic, $lang)
{
    global $pdo;
    for ($i = 0; $i < 10; $i++) {
        $code = generateRoomCode();
        try {
            $ms = $timeControl * 1000;
            $pdo->prepare("INSERT INTO pm_rooms (code, is_public, lang, level, time_control, first_slot, mine, actions, p1_ms, p2_ms) VALUES (?, ?, ?, ?, ?, 1, ?, '[]', ?, ?)")
                ->execute([$code, $isPublic ? 1 : 0, $lang, $level, $timeControl, PrismMineEngine::encode(PrismMineEngine::generate($level)), $ms, $ms]);
            $roomId = $pdo->lastInsertId();
            $token = bin2hex(random_bytes(32));
            $pdo->prepare("INSERT INTO pm_players (room_id, slot, token, name) VALUES (?, 1, ?, ?)")->execute([$roomId, $token, $name]);
            return ['code' => $code, 'token' => $token];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) continue;
            throw $e;
        }
    }
    return null;
}

// Both players are in: first each one hides the gems
function startGame($roomId)
{
    global $pdo;
    $pdo->prepare("UPDATE pm_rooms SET status = 'placing', place_started_ms = ? WHERE id = ?")->execute([nowMs(), $roomId]);
}

// The duel starts once both mines are ready
function maybeStartDuel(&$room)
{
    global $pdo;
    if ($room['status'] !== 'placing' || !$room['p1_mine'] || !$room['p2_mine']) return;
    $room['turn_started_ms'] = nowMs(); // the clock (checked right after) starts from here
    $pdo->prepare("UPDATE pm_rooms SET status = 'playing', turn_started_ms = ? WHERE id = ?")->execute([$room['turn_started_ms'], $room['id']]);
    $room['status'] = 'playing';
}

// A player who never confirms gets the gems placed at random, so the other one is not kept waiting
function checkPlacementTime(&$room)
{
    global $pdo;
    if ($room['status'] !== 'placing' || nowMs() - (int)$room['place_started_ms'] < PLACE_SECONDS * 1000) return;
    foreach ([1, 2] as $slot) {
        $key = "p{$slot}_mine";
        if (!$room[$key]) {
            $room[$key] = PrismMineEngine::encode(PrismMineEngine::randomLayout(gemSet($room), mineSize($room)));
            $pdo->prepare("UPDATE pm_rooms SET $key = ? WHERE id = ?")->execute([$room[$key], $room['id']]);
        }
    }
    maybeStartDuel($room);
}

function placeGems()
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
        $key = 'p' . (int)$player['slot'] . '_mine';
        if ($room['status'] !== 'placing' || $room[$key]) {
            $pdo->commit();
            respondError('not_placing');
            return;
        }
        $set = gemSet($room);
        $layout = PrismMineEngine::decode($_POST['layout'] ?? '', count($set), mineSize($room));
        if (!$layout || !PrismMineEngine::sameSet($layout, $set)) {
            $pdo->rollBack();
            respondError('invalid_layout');
            return;
        }
        $room[$key] = PrismMineEngine::encode($layout);
        $pdo->prepare("UPDATE pm_rooms SET $key = ? WHERE id = ?")->execute([$room[$key], $room['id']]);
        maybeStartDuel($room);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}

function addSecondPlayer($roomId, $name)
{
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $pdo->prepare("INSERT INTO pm_players (room_id, slot, token, name) VALUES (?, 2, ?, ?)")->execute([$roomId, $token, $name]);
    startGame($roomId);
    return $token;
}

function createRoom()
{
    $name = cleanName($_POST['name'] ?? '', 'Player 1');
    $tc = (int)($_POST['time'] ?? 0);
    if (!in_array($tc, TIME_CONTROLS, true)) $tc = 0;
    $level = in_array($_POST['level'] ?? '', LEVELS, true) ? $_POST['level'] : 'medium';
    $room = insertRoomWithHost($name, $tc, $level, false, null);
    if (!$room) {
        respondError('create_failed');
        return;
    }
    echo json_encode(['success' => true, 'code' => $room['code'], 'token' => $room['token']]);
}

function lockRoomByCode($code)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pm_rooms WHERE code = ? FOR UPDATE");
    $stmt->execute([$code]);
    return $stmt->fetch();
}

function lockRoom($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pm_rooms WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function fetchRoom($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pm_rooms WHERE id = ?");
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
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pm_players WHERE room_id = ?");
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
        SELECT r.id FROM pm_rooms r
        JOIN pm_players host ON host.room_id = r.id AND host.slot = 1
        WHERE r.is_public = 1 AND r.status = 'waiting' AND r.lang = ? AND r.id <> ?
          AND host.is_online = 1 AND host.last_seen > NOW() - INTERVAL " . ONLINE_WINDOW_SECONDS . " SECOND
          AND NOT EXISTS (SELECT 1 FROM pm_players p2 WHERE p2.room_id = r.id AND p2.slot = 2)
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
        $room = insertRoomWithHost($name, RANDOM_TIME_CONTROL, 'medium', true, $lang);
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
        $targetId = $own && $own['status'] === 'waiting' && $own['is_public'] ? findOpenPublicRoom($player['lang'], (int)$player['room_id']) : null;
        if (!$targetId || $targetId > (int)$player['room_id']) {
            $pdo->rollBack();
            return false;
        }
        $pdo->prepare("UPDATE pm_players SET room_id = ?, slot = 2 WHERE id = ?")->execute([$targetId, $player['id']]);
        $pdo->prepare("UPDATE pm_rooms SET status = 'finished' WHERE id = ?")->execute([$player['room_id']]);
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
    $stmt = $pdo->prepare("SELECT p.*, r.lang, r.status AS room_status FROM pm_players p JOIN pm_rooms r ON r.id = p.room_id WHERE p.token = ? AND p.is_online = 1");
    $stmt->execute([$token]);
    $player = $stmt->fetch();
    if (!$player) return null;
    $pdo->prepare("UPDATE pm_players SET last_seen = NOW() WHERE id = ?")->execute([$player['id']]);
    return $player;
}

function finishGame($room, $winnerSlot, $reason)
{
    global $pdo;
    $pdo->prepare("UPDATE pm_rooms SET status = 'finished', result = ?, reason = ?, p1_ms = ?, p2_ms = ? WHERE id = ?")
        ->execute([$winnerSlot === 1 ? '1-0' : '0-1', $reason, $room['p1_ms'], $room['p2_ms'], $room['id']]);
    $pdo->prepare("UPDATE pm_players SET wins = wins + 1 WHERE room_id = ? AND slot = ?")->execute([$room['id'], $winnerSlot]);
}

// Time per turn: when it runs out, the turn passes to the other player (a skip in the list of actions).
// Never ends the game, so it always returns false (the callers still treat true as "game over").
function checkFlag(&$room)
{
    global $pdo;
    if (!clockRunning($room)) return false;
    $limit = (int)$room['time_control'] * 1000;
    $list = actionList($room);
    $started = (int)$room['turn_started_ms'];
    $skipped = 0;
    while (nowMs() - $started >= $limit && $skipped < MAX_SKIPS_PER_POLL) {
        $list[] = ['s' => count($list) % 2 === 0 ? (int)$room['first_slot'] : other($room['first_slot']), 't' => 'x'];
        $started += $limit;
        $skipped++;
    }
    if (!$skipped) return false;
    $room['actions'] = json_encode($list);
    $room['turn_started_ms'] = $started;
    $pdo->prepare("UPDATE pm_rooms SET actions = ?, turn_started_ms = ? WHERE id = ?")->execute([$room['actions'], $started, $room['id']]);
    return false;
}

// The opponent lost the connection (or left) for ABANDON_SECONDS: the player still here wins
function checkAbandon(&$room, $mySlot)
{
    global $pdo;
    if (!in_array($room['status'], ['placing', 'playing'], true)) return false;
    $stmt = $pdo->prepare("SELECT is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM pm_players WHERE room_id = ? AND slot = ?");
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
    if ($player['room_status'] === 'waiting' && mergeWaitingPublicRoom($player)) $player = getPlayer($token);

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

    $stmt = $pdo->prepare("SELECT slot, name, wins, wants_rematch, is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM pm_players WHERE room_id = ? ORDER BY slot");
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

    // Every beam with its answer (each one went into the other player's mine); answers only say right or wrong
    $mySlot = (int)$player['slot'];
    $mines = [1 => mineOf($room, 1), 2 => mineOf($room, 2)];
    $size = mineSize($room);
    $maps = [1 => $mines[1] ? PrismMineEngine::buildMap($mines[1], $size) : null, 2 => $mines[2] ? PrismMineEngine::buildMap($mines[2], $size) : null];
    $actions = [];
    foreach (actionList($room) as $a) {
        if ($a['t'] === 'p') {
            $target = other($a['s']);
            if (!$maps[$target]) continue;
            $res = PrismMineEngine::trace($maps[$target], $mines[$target], $a['p']);
            $actions[] = ['s' => $a['s'], 't' => 'p', 'p' => $a['p'], 'exit' => $res['exit'], 'colors' => $res['colors']];
        } elseif ($a['t'] === 'x') $actions[] = ['s' => $a['s'], 't' => 'x'];
        else $actions[] = ['s' => $a['s'], 't' => 'g', 'ok' => $a['ok']];
    }
    $gems = gemSet($room);
    usort($gems, fn($a, $b) => strcmp($a['shape'], $b['shape']));

    $turn = slotToAct($room);
    $turnLeftMs = clockRunning($room) ? max(0, (int)$room['time_control'] * 1000 - (nowMs() - (int)$room['turn_started_ms'])) : 0;

    echo json_encode([
        'status' => $room['status'],
        'code' => $room['code'],
        'isPublic' => (bool)$room['is_public'],
        'round' => (int)$room['round'],
        'level' => $room['level'],
        'size' => mineSize($room),
        'firstSlot' => (int)$room['first_slot'],
        'mySlot' => (int)$player['slot'],
        'timeControl' => (int)$room['time_control'],
        'gems' => $gems,
        'actions' => $actions,
        'myMine' => $room["p{$mySlot}_mine"] ?: null,
        'oppReady' => (bool)$room['p' . other($mySlot) . '_mine'],
        'oppMine' => $room['status'] === 'finished' ? ($room['p' . other($mySlot) . '_mine'] ?: null) : null,
        'placeLeftMs' => $room['status'] === 'placing' ? max(0, PLACE_SECONDS * 1000 - (nowMs() - (int)$room['place_started_ms'])) : 0,
        'turn' => $turn,
        'turnLeftMs' => $turnLeftMs,
        'result' => $room['result'],
        'reason' => $room['reason'],
        'players' => $players,
        'messages' => chatMessages($room['id'], (int)($_GET['since'] ?? 0)),
    ]);
}

// Shared checks for probe and guess: the room locked, the player's turn, in step with the client
function lockForTurn($player, $n)
{
    global $pdo;
    $room = lockRoom($player['room_id']);
    if ($room['status'] !== 'playing' || checkFlag($room)) return [null, 'game_not_active', true];
    if ($n !== count(actionList($room))) return [null, 'out_of_sync', false];
    if (slotToAct($room) !== (int)$player['slot']) return [null, 'not_your_turn', false];
    return [$room, null, false];
}

function saveAction(&$room, $entry, $now)
{
    global $pdo;
    $list = actionList($room);
    $list[] = $entry;
    $room['actions'] = json_encode($list);
    $pdo->prepare("UPDATE pm_rooms SET actions = ?, p1_ms = ?, p2_ms = ?, turn_started_ms = ? WHERE id = ?")
        ->execute([$room['actions'], $room['p1_ms'], $room['p2_ms'], $now, $room['id']]);
}

function probe()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    $port = isset($_POST['port']) ? (int)$_POST['port'] : -1;
    if (!$player || $port < 0 || $port >= 40) { // the exact limit (4 x size) is checked once the room is known
        respondError('invalid_request');
        return;
    }
    $pdo->beginTransaction();
    try {
        [$room, $error, $commit] = lockForTurn($player, (int)($_POST['n'] ?? -1));
        if ($error) {
            $commit ? $pdo->commit() : $pdo->rollBack();
            respondError($error);
            return;
        }
        if ($port >= mineSize($room) * 4) {
            $pdo->rollBack();
            respondError('invalid_request');
            return;
        }
        if (isset(usedPorts($room, $player['slot'])[$port])) {
            $pdo->rollBack();
            respondError('port_used');
            return;
        }
        $now = nowMs();
        saveAction($room, ['s' => (int)$player['slot'], 't' => 'p', 'p' => $port], $now);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    $mine = mineOf($room, other($player['slot']));
    $res = PrismMineEngine::trace(PrismMineEngine::buildMap($mine, mineSize($room)), $mine, $port);
    echo json_encode(['success' => true, 'exit' => $res['exit'], 'colors' => $res['colors']]);
}

function guess()
{
    global $pdo;
    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }
    $pdo->beginTransaction();
    try {
        [$room, $error, $commit] = lockForTurn($player, (int)($_POST['n'] ?? -1));
        if ($error) {
            $commit ? $pdo->commit() : $pdo->rollBack();
            respondError($error);
            return;
        }
        $hidden = mineOf($room, other($player['slot']));
        $answer = PrismMineEngine::decode($_POST['layout'] ?? '', count($hidden), mineSize($room));
        if (!$answer) {
            $pdo->rollBack();
            respondError('invalid_layout');
            return;
        }
        $ok = PrismMineEngine::isCorrect($answer, $hidden, mineSize($room));
        $slot = (int)$player['slot'];
        $now = nowMs();
        saveAction($room, ['s' => $slot, 't' => 'g', 'ok' => $ok], $now);
        if ($ok) finishGame($room, $slot, 'solved');
        else {
            $strikes = count(array_filter(actionList($room), fn($a) => $a['t'] === 'g' && $a['s'] === $slot && !$a['ok']));
            if ($strikes >= MAX_STRIKES) finishGame($room, other($slot), 'strikes');
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true, 'ok' => $ok]);
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
        $stmt = $pdo->prepare("SELECT id, slot, body FROM pm_messages WHERE room_id = ? AND id > ? ORDER BY id DESC LIMIT " . CHAT_HISTORY);
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
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pm_messages WHERE room_id = ? AND slot = ? AND created_at > NOW() - INTERVAL 10 SECOND");
    $stmt->execute([$player['room_id'], $player['slot']]);
    if ((int)$stmt->fetchColumn() >= CHAT_BURST) {
        respondError('too_fast');
        return;
    }
    $pdo->prepare("INSERT INTO pm_messages (room_id, slot, body) VALUES (?, ?, ?)")->execute([$player['room_id'], $player['slot'], $text]);
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
        $pdo->prepare("UPDATE pm_players SET wants_rematch = 1 WHERE id = ?")->execute([$player['id']]);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pm_players WHERE room_id = ? AND wants_rematch = 1 AND is_online = 1");
        $stmt->execute([$room['id']]);
        if ((int)$stmt->fetchColumn() >= 2) {
            // A new set of gems to hide, and the other player starts
            $ms = (int)$room['time_control'] * 1000;
            $pdo->prepare("UPDATE pm_rooms SET status = 'placing', round = round + 1, first_slot = ?, mine = ?, p1_mine = NULL, p2_mine = NULL, actions = '[]', p1_ms = ?, p2_ms = ?, place_started_ms = ?, turn_started_ms = 0, result = NULL, reason = NULL WHERE id = ?")
                ->execute([other($room['first_slot']), PrismMineEngine::encode(PrismMineEngine::generate($room['level'])), $ms, $ms, nowMs(), $room['id']]);
            $pdo->prepare("UPDATE pm_players SET wants_rematch = 0 WHERE room_id = ?")->execute([$room['id']]);
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
        $stmt = $pdo->prepare("SELECT is_online, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle FROM pm_players WHERE room_id = ? AND slot = ?");
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
        $pdo->prepare("UPDATE pm_players SET is_online = 0 WHERE id = ?")->execute([$player['id']]);
        if ($room && in_array($room['status'], ['placing', 'playing'], true)) finishGame($room, other($player['slot']), 'abandon');
        elseif ($room && $room['status'] === 'waiting') $pdo->prepare("UPDATE pm_rooms SET status = 'finished' WHERE id = ?")->execute([$room['id']]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    echo json_encode(['success' => true]);
}
