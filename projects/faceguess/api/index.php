<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/characters.php';

const BOARD_SIZE = 24;
const ONLINE_WINDOW_SECONDS = 15;
const MODES = ['online', 'inperson'];
const LANGS = ['en', 'it', 'es', 'fr', 'de'];

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create_room':     createRoom(); break;
    case 'join_room':       joinRoom(); break;
    case 'find_match':      findMatch(); break;
    case 'poll':            pollState(); break;
    case 'toggle_card':     toggleCard(); break;
    case 'ask_question':    askQuestion(); break;
    case 'answer_question': answerQuestion(); break;
    case 'end_turn':        endTurn(); break;
    case 'final_guess':     finalGuess(); break;
    case 'concede':         concede(); break;
    case 'rematch':         requestRematch(); break;
    case 'leave':           leaveRoom(); break;
    default:
        echo json_encode(['error' => 'Unknown action']);
}

function respondError($code) {
    echo json_encode(['error' => $code]);
}

function cleanName($raw, $fallback) {
    $name = trim((string)$raw);
    if ($name === '') $name = $fallback;
    return mb_substr($name, 0, 30);
}

function generateRoomCode() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

function insertRoomWithHost($name, $mode, $isPublic, $lang) {
    global $pdo;

    for ($i = 0; $i < 10; $i++) {
        $code = generateRoomCode();
        try {
            $pdo->prepare("INSERT INTO fg_rooms (code, mode, is_public, lang) VALUES (?, ?, ?, ?)")
                ->execute([$code, $mode, $isPublic ? 1 : 0, $lang]);
            $roomId = $pdo->lastInsertId();
            $token = bin2hex(random_bytes(32));
            $pdo->prepare("INSERT INTO fg_players (room_id, slot, token, name) VALUES (?, 1, ?, ?)")
                ->execute([$roomId, $token, $name]);
            return ['code' => $code, 'token' => $token];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) continue;
            throw $e;
        }
    }
    return null;
}

function addSecondPlayer($roomId, $name) {
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $pdo->prepare("INSERT INTO fg_players (room_id, slot, token, name) VALUES (?, 2, ?, ?)")
        ->execute([$roomId, $token, $name]);
    initializeGame($roomId);
    return $token;
}

function createRoom() {
    $name = cleanName($_POST['name'] ?? '', 'Player 1');
    $mode = in_array($_POST['mode'] ?? '', MODES, true) ? $_POST['mode'] : 'online';

    $room = insertRoomWithHost($name, $mode, false, null);
    if (!$room) {
        respondError('create_failed');
        return;
    }
    echo json_encode(['success' => true, 'code' => $room['code'], 'token' => $room['token'], 'slot' => 1]);
}

function joinRoom() {
    global $pdo;

    $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $_POST['code'] ?? ''));
    $name = cleanName($_POST['name'] ?? '', 'Player 2');

    if ($code === '') {
        respondError('code_required');
        return;
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT * FROM fg_rooms WHERE code = ? FOR UPDATE");
        $stmt->execute([$code]);
        $room = $stmt->fetch();

        $error = null;
        if (!$room) {
            $error = 'room_not_found';
        } elseif ($room['status'] === 'finished') {
            $error = 'room_finished';
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM fg_players WHERE room_id = ?");
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

    echo json_encode(['success' => true, 'code' => $code, 'token' => $token, 'slot' => 2]);
}

// Oldest public room in the same language whose host is still connected and alone.
function findOpenPublicRoom($lang, $excludeRoomId = 0) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.id
        FROM fg_rooms r
        JOIN fg_players host ON host.room_id = r.id AND host.slot = 1
        WHERE r.is_public = 1 AND r.status = 'waiting' AND r.lang = ? AND r.id <> ?
          AND host.is_online = 1 AND host.last_seen > NOW() - INTERVAL " . ONLINE_WINDOW_SECONDS . " SECOND
          AND NOT EXISTS (SELECT 1 FROM fg_players p2 WHERE p2.room_id = r.id AND p2.slot = 2)
        ORDER BY r.id
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute([$lang, $excludeRoomId]);
    $id = $stmt->fetchColumn();
    return $id ? (int)$id : null;
}

function findMatch() {
    global $pdo;

    $name = cleanName($_POST['name'] ?? '', 'Player');
    $lang = in_array($_POST['lang'] ?? '', LANGS, true) ? $_POST['lang'] : 'en';

    $pdo->beginTransaction();
    try {
        $roomId = findOpenPublicRoom($lang);
        if ($roomId) {
            $token = addSecondPlayer($roomId, $name);
            $pdo->commit();
            echo json_encode(['success' => true, 'token' => $token, 'slot' => 2, 'matched' => true]);
            return;
        }

        $room = insertRoomWithHost($name, 'online', true, $lang);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    if (!$room) {
        respondError('create_failed');
        return;
    }
    echo json_encode(['success' => true, 'token' => $room['token'], 'slot' => 1, 'matched' => false]);
}

// Two players searching at the same moment can each open a room; the newer one moves into the older one.
function mergeWaitingPublicRoom($player) {
    global $pdo;

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT status FROM fg_rooms WHERE id = ? FOR UPDATE");
        $stmt->execute([$player['room_id']]);
        $stillWaiting = $stmt->fetchColumn() === 'waiting';

        $targetId = $stillWaiting ? findOpenPublicRoom($player['lang'], (int)$player['room_id']) : null;
        if (!$targetId || $targetId > (int)$player['room_id']) {
            $pdo->rollBack();
            return false;
        }
        $pdo->prepare("UPDATE fg_players SET room_id = ?, slot = 2 WHERE id = ?")->execute([$targetId, $player['id']]);
        $pdo->prepare("UPDATE fg_rooms SET status = 'finished' WHERE id = ?")->execute([$player['room_id']]);
        initializeGame($targetId);
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        // Lock contention with another matchmaking request: the next poll simply retries.
        if (in_array($e->getCode(), ['40001', 'HY000'], true)) return false;
        throw $e;
    }
}

function initializeGame($roomId, $firstTurn = 1) {
    global $pdo;

    $characters = generateCharacters(BOARD_SIZE);

    $insertCard = $pdo->prepare("INSERT INTO fg_board_cards (room_id, position, card_data) VALUES (?, ?, ?)");
    $cardIds = [];
    foreach ($characters as $pos => $char) {
        $insertCard->execute([$roomId, $pos, json_encode($char)]);
        $cardIds[] = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare("SELECT id, slot FROM fg_players WHERE room_id = ? ORDER BY slot");
    $stmt->execute([$roomId]);
    $players = $stmt->fetchAll();

    // Each player holds a different character; the opponent has to guess it.
    $positions = range(0, BOARD_SIZE - 1);
    shuffle($positions);

    $setOwn = $pdo->prepare("UPDATE fg_players SET own_position = ? WHERE id = ?");
    $insertPc = $pdo->prepare("INSERT INTO fg_player_cards (player_id, card_id, is_hidden) VALUES (?, ?, 0)");
    foreach ($players as $i => $player) {
        $setOwn->execute([$positions[$i], $player['id']]);
        foreach ($cardIds as $cardId) {
            $insertPc->execute([$player['id'], $cardId]);
        }
    }

    $pdo->prepare("UPDATE fg_rooms SET status = 'playing', current_turn = ?, first_turn = ?, winner_slot = NULL, pending_winner = NULL WHERE id = ?")
        ->execute([$firstTurn, $firstTurn, $roomId]);
}

function requestRematch() {
    global $pdo;

    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT * FROM fg_rooms WHERE id = ? FOR UPDATE");
        $stmt->execute([$player['room_id']]);
        $room = $stmt->fetch();

        if ($room['status'] !== 'finished') {
            $pdo->rollBack();
            echo json_encode(['success' => true, 'started' => $room['status'] === 'playing']);
            return;
        }

        $opponent = getOpponent($player);
        if (!$opponent || !$opponent['is_online']) {
            $pdo->rollBack();
            respondError('opponent_left');
            return;
        }

        $pdo->prepare("UPDATE fg_players SET wants_rematch = 1 WHERE id = ?")->execute([$player['id']]);

        $started = false;
        if ((int)$opponent['wants_rematch'] === 1) {
            $pdo->prepare("DELETE FROM fg_guesses WHERE room_id = ?")->execute([$room['id']]);
            $pdo->prepare("DELETE FROM fg_board_cards WHERE room_id = ?")->execute([$room['id']]);
            $pdo->prepare("UPDATE fg_players SET wants_rematch = 0, own_position = NULL WHERE room_id = ?")->execute([$room['id']]);
            $nextRound = (int)$room['round'] + 1;
            $pdo->prepare("UPDATE fg_rooms SET round = ? WHERE id = ?")->execute([$nextRound, $room['id']]);
            // Starting player alternates between rounds.
            initializeGame($room['id'], $nextRound % 2 === 0 ? 2 : 1);
            $started = true;
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    echo json_encode(['success' => true, 'started' => $started]);
}

function getPlayer($token) {
    global $pdo;

    if (!is_string($token) || $token === '') return null;

    $stmt = $pdo->prepare("
        SELECT p.*, r.code, r.status AS room_status, r.current_turn, r.round, r.mode, r.is_public, r.lang, r.winner_slot, r.pending_winner, r.first_turn
        FROM fg_players p
        JOIN fg_rooms r ON p.room_id = r.id
        WHERE p.token = ? AND p.is_online = 1
    ");
    $stmt->execute([$token]);
    $player = $stmt->fetch();
    if (!$player) return null;

    // is_online = 0 means the player left for good; a request still in flight must not bring them back.
    $pdo->prepare("UPDATE fg_players SET last_seen = NOW() WHERE id = ?")->execute([$player['id']]);
    return $player;
}

function getOpponent($player) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM fg_players WHERE room_id = ? AND id <> ?");
    $stmt->execute([$player['room_id'], $player['id']]);
    return $stmt->fetch() ?: null;
}

function pollState() {
    global $pdo;

    $token = $_GET['token'] ?? '';
    $player = getPlayer($token);
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    if ($player['room_status'] === 'waiting' && (int)$player['is_public'] === 1 && mergeWaitingPublicRoom($player)) {
        $player = getPlayer($token);
    }

    $roomId = $player['room_id'];

    $stmt = $pdo->prepare("SELECT slot, name, wins, wants_rematch, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS idle, is_online FROM fg_players WHERE room_id = ? ORDER BY slot");
    $stmt->execute([$roomId]);
    $players = array_map(function ($p) {
        return [
            'slot' => (int)$p['slot'],
            'name' => $p['name'],
            'online' => $p['is_online'] && (int)$p['idle'] < ONLINE_WINDOW_SECONDS,
            'left' => !$p['is_online'],
            'wins' => (int)$p['wins'],
            'wantsRematch' => (bool)$p['wants_rematch'],
        ];
    }, $stmt->fetchAll());

    $response = [
        'status' => $player['room_status'],
        'mode' => $player['mode'],
        'isPublic' => (bool)$player['is_public'],
        'code' => $player['code'],
        'round' => (int)$player['round'],
        'currentTurn' => (int)$player['current_turn'],
        'mySlot' => (int)$player['slot'],
        'players' => $players,
        // Slot that already guessed right while the other player takes their last chance.
        'pendingWinner' => $player['pending_winner'] === null ? null : (int)$player['pending_winner'],
    ];

    if ($player['room_status'] === 'waiting') {
        echo json_encode($response);
        return;
    }

    $stmt = $pdo->prepare("
        SELECT bc.id, bc.position, bc.card_data, pc.is_hidden
        FROM fg_board_cards bc
        JOIN fg_player_cards pc ON pc.card_id = bc.id
        WHERE bc.room_id = ? AND pc.player_id = ?
        ORDER BY bc.position
    ");
    $stmt->execute([$roomId, $player['id']]);
    $cards = [];
    foreach ($stmt->fetchAll() as $c) {
        $cards[] = [
            'id' => (int)$c['id'],
            'position' => (int)$c['position'],
            'card_data' => json_decode($c['card_data'], true),
            'is_hidden' => (bool)$c['is_hidden'],
        ];
    }
    $response['cards'] = $cards;
    $response['myPosition'] = $player['own_position'] === null ? null : (int)$player['own_position'];

    $stmt = $pdo->prepare("
        SELECT g.id, g.question, g.answer, g.is_final_guess, g.guessed_position, g.is_correct,
               p.slot AS player_slot, p.name AS player_name
        FROM fg_guesses g
        JOIN fg_players p ON p.id = g.player_id
        WHERE g.room_id = ?
        ORDER BY g.id DESC
        LIMIT 30
    ");
    $stmt->execute([$roomId]);
    $response['guesses'] = array_map(function ($g) {
        return [
            'id' => (int)$g['id'],
            'question' => $g['question'],
            'answer' => $g['answer'],
            'is_final_guess' => (bool)$g['is_final_guess'],
            'guessed_position' => $g['guessed_position'] === null ? null : (int)$g['guessed_position'],
            'is_correct' => $g['is_correct'] === null ? null : (bool)$g['is_correct'],
            'player_slot' => (int)$g['player_slot'],
            'player_name' => $g['player_name'],
        ];
    }, $stmt->fetchAll());

    // The opponent's character is revealed only once the game is over.
    if ($player['room_status'] === 'finished') {
        $opponent = getOpponent($player);
        $response['opponentPosition'] = $opponent ? (int)$opponent['own_position'] : null;
        $response['winnerSlot'] = $player['winner_slot'] === null ? null : (int)$player['winner_slot'];
    }

    echo json_encode($response);
}

function toggleCard() {
    global $pdo;

    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->prepare("UPDATE fg_player_cards SET is_hidden = NOT is_hidden WHERE player_id = ? AND card_id = ?")
        ->execute([$player['id'], (int)($_POST['card_id'] ?? 0)]);

    echo json_encode(['success' => true]);
}

function hasPendingQuestion($roomId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fg_guesses WHERE room_id = ? AND is_final_guess = 0 AND answer IS NULL");
    $stmt->execute([$roomId]);
    return (int)$stmt->fetchColumn() > 0;
}

function askQuestion() {
    global $pdo;

    $player = getPlayer($_POST['token'] ?? '');
    $question = mb_substr(trim($_POST['question'] ?? ''), 0, 255);

    if (!$player || $question === '' || $player['mode'] !== 'online') {
        respondError('invalid_request');
        return;
    }
    if ($player['room_status'] !== 'playing') {
        respondError('game_not_active');
        return;
    }
    if ((int)$player['current_turn'] !== (int)$player['slot']) {
        respondError('not_your_turn');
        return;
    }
    if ($player['pending_winner'] !== null) {
        respondError('last_chance_guess_only');
        return;
    }
    if (hasPendingQuestion($player['room_id'])) {
        respondError('question_pending');
        return;
    }

    $pdo->prepare("INSERT INTO fg_guesses (room_id, player_id, question) VALUES (?, ?, ?)")
        ->execute([$player['room_id'], $player['id'], $question]);

    echo json_encode(['success' => true]);
}

function answerQuestion() {
    global $pdo;

    $player = getPlayer($_POST['token'] ?? '');
    $answer = $_POST['answer'] ?? '';

    if (!$player || !in_array($answer, ['yes', 'no'], true)) {
        respondError('invalid_request');
        return;
    }

    $stmt = $pdo->prepare("SELECT * FROM fg_guesses WHERE id = ? AND room_id = ? AND is_final_guess = 0 AND answer IS NULL");
    $stmt->execute([(int)($_POST['guess_id'] ?? 0), $player['room_id']]);
    $guess = $stmt->fetch();

    if (!$guess || (int)$guess['player_id'] === (int)$player['id']) {
        respondError('invalid_request');
        return;
    }

    $pdo->prepare("UPDATE fg_guesses SET answer = ? WHERE id = ?")->execute([$answer, $guess['id']]);

    // After an answer the turn passes to the player who answered.
    $pdo->prepare("UPDATE fg_rooms SET current_turn = ? WHERE id = ?")
        ->execute([(int)$player['slot'], $player['room_id']]);

    echo json_encode(['success' => true]);
}

// In person the question is asked out loud, so the player hands the turn over explicitly.
function endTurn() {
    global $pdo;

    $player = getPlayer($_POST['token'] ?? '');
    if (!$player || $player['mode'] !== 'inperson') {
        respondError('invalid_request');
        return;
    }
    if ($player['room_status'] !== 'playing') {
        respondError('game_not_active');
        return;
    }
    if ($player['pending_winner'] !== null) {
        respondError('last_chance_guess_only');
        return;
    }

    $nextTurn = (int)$player['slot'] === 1 ? 2 : 1;
    $stmt = $pdo->prepare("UPDATE fg_rooms SET current_turn = ? WHERE id = ? AND status = 'playing' AND current_turn = ?");
    $stmt->execute([$nextTurn, $player['room_id'], (int)$player['slot']]);
    if ($stmt->rowCount() === 0) {
        respondError('not_your_turn');
        return;
    }

    echo json_encode(['success' => true]);
}

function finalGuess() {
    global $pdo;

    $player = getPlayer($_POST['token'] ?? '');
    $position = (int)($_POST['position'] ?? -1);

    if (!$player || $position < 0 || $position >= BOARD_SIZE) {
        respondError('invalid_request');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);

        $error = null;
        if ($room['status'] !== 'playing') {
            $error = 'game_not_active';
        } elseif ((int)$room['current_turn'] !== (int)$player['slot']) {
            $error = 'not_your_turn';
        } elseif (hasPendingQuestion($player['room_id'])) {
            $error = 'question_pending';
        }
        if ($error) {
            $pdo->rollBack();
            respondError($error);
            return;
        }

        $opponent = getOpponent($player);
        $isCorrect = $opponent && (int)$opponent['own_position'] === $position;
        $pendingWinner = $room['pending_winner'] === null ? null : (int)$room['pending_winner'];

        $pdo->prepare("INSERT INTO fg_guesses (room_id, player_id, question, is_final_guess, guessed_position, is_correct) VALUES (?, ?, '', 1, ?, ?)")
            ->execute([$player['room_id'], $player['id'], $position, $isCorrect ? 1 : 0]);

        $lastChance = false;
        if ($pendingWinner !== null) {
            // This was the opponent's last chance: guessing right too is a draw, otherwise the first guesser wins.
            finishRoom($room['id'], $isCorrect ? 0 : $pendingWinner);
        } elseif (!$isCorrect) {
            // A wrong guess loses the game.
            finishRoom($room['id'], (int)$opponent['slot']);
        } elseif ((int)$player['slot'] === (int)$room['first_turn']) {
            // The player who started the round guessed right: the other player has had one turn less,
            // so they get a last chance to guess for a draw.
            $pdo->prepare("UPDATE fg_rooms SET pending_winner = ?, current_turn = ? WHERE id = ?")
                ->execute([(int)$player['slot'], (int)$opponent['slot'], $room['id']]);
            $lastChance = true;
        } else {
            finishRoom($room['id'], (int)$player['slot']);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    echo json_encode(['success' => true, 'correct' => $isCorrect, 'finished' => !$lastChance, 'lastChance' => $lastChance]);
}

// During the last chance the player may give up instead of guessing.
function concede() {
    global $pdo;

    $player = getPlayer($_POST['token'] ?? '');
    if (!$player) {
        respondError('invalid_session');
        return;
    }

    $pdo->beginTransaction();
    try {
        $room = lockRoom($player['room_id']);
        if ($room['status'] !== 'playing' || $room['pending_winner'] === null || (int)$room['current_turn'] !== (int)$player['slot']) {
            $pdo->rollBack();
            respondError('invalid_request');
            return;
        }
        finishRoom($room['id'], (int)$room['pending_winner']);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    echo json_encode(['success' => true]);
}

function lockRoom($roomId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM fg_rooms WHERE id = ? FOR UPDATE");
    $stmt->execute([$roomId]);
    return $stmt->fetch();
}

// $winnerSlot 0 means a draw: nobody's score changes.
function finishRoom($roomId, $winnerSlot) {
    global $pdo;
    $pdo->prepare("UPDATE fg_rooms SET status = 'finished', winner_slot = ?, pending_winner = NULL WHERE id = ?")
        ->execute([$winnerSlot, $roomId]);
    if ($winnerSlot > 0) {
        $pdo->prepare("UPDATE fg_players SET wins = wins + 1 WHERE room_id = ? AND slot = ?")->execute([$roomId, $winnerSlot]);
    }
}

function leaveRoom() {
    global $pdo;

    $token = $_POST['token'] ?? '';
    if ($token === '') {
        respondError('invalid_session');
        return;
    }
    $pdo->prepare("UPDATE fg_players SET is_online = 0 WHERE token = ?")->execute([$token]);
    // A host who leaves before anyone joins closes the room, so random matching skips it.
    $pdo->prepare("UPDATE fg_rooms r JOIN fg_players p ON p.room_id = r.id SET r.status = 'finished' WHERE p.token = ? AND r.status = 'waiting'")
        ->execute([$token]);
    echo json_encode(['success' => true]);
}
