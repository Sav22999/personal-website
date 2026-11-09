<?php
/**
 * ThinkHop — JSON API.  api/index.php?r=<action>
 *
 * Public:   auth.config, auth.me, auth.register, auth.login, auth.google, auth.logout, auth.verify, board.daily
 * Before the email is confirmed only auth.resend and auth.delete work (see must_verify in lib/bootstrap.php).
 * Account:  auth.profile, auth.username, auth.ranking, auth.password, auth.delete, avatar.buy, avatar.set, sync,
 *           friends.list, friends.search, friends.add, friends.remove, notify.get, notify.save, notify.pending, push.key, push.subscribe, push.unsubscribe
 * Admin:    admin.player (everything about one player, read only), admin.player.points, admin.overview, admin.settings.save, admin.questions, admin.question.save, admin.question.delete,
 *           admin.games, admin.game.delete, admin.players, admin.player.update, admin.player.delete, admin.avatars, admin.avatar.save
 *
 * The game itself needs no server: without an account (or without credentials.php) it stays on the device.
 * Every POST of a signed-in user needs the X-CSRF-Token header (given at sign-in).
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/push.php';

header('X-Robots-Tag: noindex');
set_exception_handler(function (Throwable $e) {
    if ($e->getMessage() === 'not_installed') fail('not_installed', 503);
    error_log('ThinkHop API: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    send_json(['ok' => false, 'error' => 'server', 'detail' => $e->getMessage(), 'file' => basename($e->getFile()) . ':' . $e->getLine()], 500);
});

$r = (string)($_GET['r'] ?? '');

// Without credentials the accounts are simply switched off: the app hides them
if ($r === 'auth.config' && !credentials()) send_json(['ok' => true, 'accounts' => false, 'googleClientId' => '', 'game' => null]);
if (!credentials()) fail('not_installed', 503);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json') !== 0) fail('bad_request', 415);

function only(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) fail('bad_request', 405);
}

function writer(bool $unverifiedOk = false): array
{
    only('POST');
    $u = require_user($unverifiedOk);
    require_csrf($u);
    return $u;
}

function validate_password(string $pw): void
{
    if (mb_strlen($pw) < 8) fail('password_short', 422);
    if (mb_strlen($pw) > 200) fail('password_long', 422);
}

function valid_name(string $name): void
{
    if (mb_strlen($name) < 2) fail('name_required', 422);
}

/** The username is the only public data of an account. Unique whatever the case, thanks to the collation. */
function clean_username(array $b, int $self = 0): string
{
    $name = trim((string)($b['username'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $name)) fail('username_invalid', 422);
    $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE username = ? AND id <> ?');
    $st->execute([$name, $self]);
    if ($st->fetchColumn()) fail('username_taken', 409);
    return $name;
}

function mail_lang(array $b): string
{
    $l = (string)($b['lang'] ?? '');
    return in_array($l, LANGS, true) ? $l : 'en';
}

function valid_email(string $email): void
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('email_invalid', 422);
}

function user_row(int $id): array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: [];
}

function new_user(string $name, string $username, string $email, string $hash, ?string $googleSub): int
{
    db()->prepare('INSERT INTO ' . t('users') . ' (name, username, email, password_hash, google_sub, created_at, last_login_at) VALUES (?,?,?,?,?,?,?)')
        ->execute([$name, $username, $email, $hash, $googleSub, now_utc(), now_utc()]);
    return (int)db()->lastInsertId();
}

function signed_in(int $uid): void
{
    db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $uid]);
    $csrf = create_auth_session($uid);
    send_json(['ok' => true, 'user' => public_user(user_row($uid)), 'csrf' => $csrf]);
}

/** Checks one game sent by the device; null when it makes no sense. */
function clean_game(array $g): ?array
{
    $uid = (string)($g['uid'] ?? '');
    $mode = (string)($g['mode'] ?? '');
    $day = (string)($g['day'] ?? '');
    if (!preg_match('/^[a-z0-9]{16}$/', $uid)) return null;
    if (!in_array($mode, ['level', 'survival', 'daily'], true)) return null;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) || !checkdate((int)substr($day, 5, 2), (int)substr($day, 8, 2), (int)substr($day, 0, 4))) return null;
    if (strtotime($day . ' UTC') > time() + 2 * 86400) return null;
    $total = (int)($g['total'] ?? 0);
    $correct = (int)($g['correct'] ?? 0);
    if ($total < 1 || $total > 1000 || $correct < 0 || $correct > $total) return null;
    // The daily challenge feeds the public ranking: 10 questions can't earn more than this
    if ($mode === 'daily' && ($total !== 10 || (int)($g['score'] ?? 0) > 10000)) return null;
    $cats = [];
    foreach ((array)($g['cats'] ?? []) as $k => $v) {
        if (!is_string($k) || !preg_match('/^[a-z]{1,12}$/', $k) || !is_array($v) || count($cats) >= 50) continue;
        $cats[$k] = [max(0, min(1000, (int)($v[0] ?? 0))), max(0, min(1000, (int)($v[1] ?? 0)))];
    }
    return [
        'uid' => $uid, 'mode' => $mode,
        'stage' => $mode === 'level' ? max(1, min(MAX_STAGE, (int)($g['stage'] ?? 1))) : 0,
        'stars' => max(0, min(3, (int)($g['stars'] ?? 0))),
        'score' => max(0, min(1000000, (int)($g['score'] ?? 0))),
        'spent' => max(0, min(1000000, (int)($g['spent'] ?? 0))),
        'correct' => $correct, 'total' => $total,
        'seconds' => max(0, min(86400, (int)($g['seconds'] ?? 0))),
        'cats' => json_encode((object)$cats), 'day' => $day,
    ];
}

/** Every finished game of the user, in the same shape the device keeps them. */
function user_games(int $uid): array
{
    $st = db()->prepare('SELECT uid, mode, stage, stars, score, spent, correct, total, seconds, cats, day FROM ' . t('games') . ' WHERE user_id = ? ORDER BY played_at, uid');
    $st->execute([$uid]);
    return array_map(fn($g) => [
        'uid' => $g['uid'], 'mode' => $g['mode'], 'stage' => (int)$g['stage'], 'stars' => (int)$g['stars'], 'score' => (int)$g['score'], 'spent' => (int)$g['spent'], 'correct' => (int)$g['correct'],
        'total' => (int)$g['total'], 'seconds' => (int)$g['seconds'], 'cats' => json_decode($g['cats'], true) ?: new stdClass(), 'day' => $g['day'],
    ], $st->fetchAll());
}

// ---------- what the admin panel changes in the game ----------

/** The settings of the game: stored values over the defaults, which are the same as in engine.js. */
function game_settings(): array
{
    $s = ['levelLength' => 5, 'hintCost' => ['novice' => 20, 'easy' => 50, 'medium' => 100, 'hard' => 150, 'expert' => 200, 'master' => 300, 'legend' => 500], 'disabledCats' => [], 'disabledRiddles' => [], 'difficulty' => (object)[], 'catWeights' => (object)[]];
    try {
        foreach (db()->query('SELECT k, v FROM ' . t('settings')) as $row) {
            if (!array_key_exists($row['k'], $s)) continue;
            $v = json_decode((string)$row['v'], true);
            if ($row['k'] === 'hintCost' && is_array($v)) {
                $s['hintCost'] = array_replace($s['hintCost'], $v);
            } elseif ($row['k'] === 'difficulty' || $row['k'] === 'catWeights') {
                $s[$row['k']] = is_array($v) ? (object)$v : (object)[];
            } else {
                $s[$row['k']] = $v;
            }
        }
    } catch (Throwable $e) {
        // Fallback to defaults if table cannot be queried
    }
    return $s;
}

function question_out(array $q): array
{
    return ['id' => 'c' . $q['id'], 'level' => $q['tier'], 'd' => (float)$q['difficulty'], 'custom' => true, 't' => json_decode($q['data'], true)];
}

/** Settings and active questions, public: the game reads them at start and keeps a copy for offline play. */
function game_config(): array
{
    $list = db()->query('SELECT * FROM ' . t('questions') . ' WHERE active = 1 ORDER BY id')->fetchAll();
    return game_settings() + ['questions' => array_map('question_out', $list)];
}

/** Checks the texts of a question: IT and EN are required, the other languages are all or nothing. */
function clean_question_data(array $texts): array
{
    $out = [];
    foreach (LANGS as $l) {
        $x = $texts[$l] ?? null;
        $q = is_array($x) ? s($x, 'q', 400) : '';
        $w = is_array($x) ? s($x, 'why', 400) : '';
        $o = [];
        foreach (array_slice((array)(is_array($x) ? ($x['options'] ?? []) : []), 0, 4) as $v) $o[] = is_string($v) || is_numeric($v) ? mb_substr(trim((string)$v), 0, 80) : '';
        $o = array_pad($o, 4, '');
        $empty = $q === '' && $w === '' && implode('', $o) === '';
        if ($empty && !in_array($l, REQUIRED_LANGS, true)) continue;
        if ($q === '' || $w === '' || in_array('', $o, true)) send_json(['ok' => false, 'error' => 'question_incomplete', 'lang' => $l], 422);
        if (count(array_unique($o)) !== 4) send_json(['ok' => false, 'error' => 'question_repeated', 'lang' => $l], 422);
        $out[$l] = [$q, $w, $o];
    }
    return $out;
}

function admin_writer(): array
{
    only('POST');
    $u = require_admin();
    require_csrf($u);
    return $u;
}

function target_player(array $me, array $b): array
{
    $id = (int)($b['id'] ?? 0);
    if ($id === (int)$me['id']) fail('not_yourself', 422);
    $row = user_row($id);
    if (!$row) fail('bad_request', 404);
    return $row;
}

switch ($r) {

    case 'auth.config':
        // credentials.php is there but install-db.php has not created the tables yet: the game goes on without accounts
        try {
            $game = game_config();
        } catch (PDOException $e) {
            error_log('ThinkHop API: ' . $e->getMessage());
            send_json(['ok' => true, 'accounts' => false, 'installNeeded' => true, 'googleClientId' => '', 'game' => null]);
        }
        $admins = (int)db()->query('SELECT COUNT(*) FROM ' . t('users') . ' WHERE role >= ' . ROLE_ADMIN)->fetchColumn();
        send_json(['ok' => true, 'accounts' => true, 'needsAdmin' => $admins === 0, 'googleClientId' => (string)config_value('google_client_id', ''), 'game' => $game]);

    case 'auth.me':
        only('GET');
        $u = current_user();
        if (!$u || $u['status'] !== 'active') send_json(['ok' => true, 'user' => null]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id'])), 'csrf' => $u['csrf_token']]);

    case 'auth.register':
        only('POST');
        if (too_many_attempts('register', 5, 60)) fail('rate_limited', 429);
        $b = body();
        $name = s($b, 'name', 80);
        $email = mb_strtolower(s($b, 'email', 190));
        $pw = (string)($b['password'] ?? '');
        valid_name($name);
        valid_email($email);
        validate_password($pw);
        $username = clean_username($b);
        if (empty($b['acceptPrivacy'])) fail('privacy_required', 422);
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetchColumn()) fail('email_taken', 409);
        record_attempt('register');
        $id = new_user($name, $username, $email, password_hash($pw, PASSWORD_DEFAULT), null);
        send_verification(user_row($id), mail_lang($b));
        signed_in($id);

    case 'auth.login':
        only('POST');
        if (too_many_attempts('login', 10, 15)) fail('rate_limited', 429);
        $b = body();
        $email = mb_strtolower(s($b, 'email', 190));
        $pw = (string)($b['password'] ?? '');
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        if (!$row || $row['password_hash'] === '' || !password_verify($pw, $row['password_hash'])) {
            record_attempt('login');
            if ($row && $row['password_hash'] === '' && $row['google_sub']) fail('use_google', 401);
            fail('bad_credentials', 401);
        }
        if ($row['status'] !== 'active') fail('disabled', 403);
        clear_attempts('login');
        if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $row['id']]);
        signed_in((int)$row['id']);

    case 'auth.google':
        // Sign in (or sign up) with Google. A new account needs the privacy consent: the first call answers
        // "needs_signup" and the app asks for it, then calls again.
        only('POST');
        if (too_many_attempts('login', 20, 15)) fail('rate_limited', 429);
        $b = body();
        $g = verify_google_token((string)($b['credential'] ?? ''));
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE google_sub = ?');
        $st->execute([$g['sub']]);
        $row = $st->fetch();
        if (!$row) {
            $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
            $st->execute([$g['email']]);
            $row = $st->fetch();
            // Google proves the address: an account nobody confirmed loses the password someone else may have chosen
            if ($row) db()->prepare('UPDATE ' . t('users') . ' SET google_sub = ?, password_hash = IF(email_verified_at IS NULL, \'\', password_hash), email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')
                ->execute([$g['sub'], now_utc(), $row['id']]);
        }
        if (!$row) {
            if (empty($b['acceptPrivacy'])) send_json(['ok' => false, 'error' => 'needs_signup', 'name' => $g['name'], 'email' => $g['email']], 200);
            if (too_many_attempts('register', 5, 60)) fail('rate_limited', 429);
            $username = clean_username($b);
            record_attempt('register');
            $row = user_row(new_user($g['name'] ?: explode('@', $g['email'])[0], $username, $g['email'], '', $g['sub']));
            db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        }
        if ($row['status'] !== 'active') fail('disabled', 403);
        signed_in((int)$row['id']);

    case 'auth.resend':
        $u = writer(true);
        if (!empty($u['email_verified_at'])) send_json(['ok' => true, 'user' => public_user(user_row((int)$u['id']))]);
        if (too_many_attempts('verify_mail', 3, 60)) fail('rate_limited', 429);
        record_attempt('verify_mail');
        send_verification(user_row((int)$u['id']), mail_lang(body()));
        send_json(['ok' => true]);

    case 'auth.verify':
        // The link of the email opens the app, which asks for this with a button: mail scanners that open links do nothing
        only('POST');
        if (too_many_attempts('verify', 20, 15)) fail('rate_limited', 429);
        $token = (string)(body()['token'] ?? '');
        $st = db()->prepare('SELECT user_id FROM ' . t('email_tokens') . ' WHERE token_hash = ? AND expires_at > ?');
        $st->execute([hash('sha256', $token), now_utc()]);
        $uid = (int)$st->fetchColumn();
        if (!$uid) {
            record_attempt('verify');
            fail('token_invalid', 410);
        }
        db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')->execute([now_utc(), $uid]);
        db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ?')->execute([$uid]);
        if (user_row($uid)['status'] !== 'active') fail('disabled', 403);
        signed_in($uid); // a magic link: it also signs in on the device that opened it

    case 'auth.logout':
        only('POST');
        $u = current_user();
        if ($u) require_csrf($u);
        destroy_auth_session();
        send_json(['ok' => true]);

    case 'auth.profile':
        // Only the name: the email is the identity of the account (and of its Google sign-in), so it never changes
        $u = writer();
        $name = s(body(), 'name', 80);
        valid_name($name);
        db()->prepare('UPDATE ' . t('users') . ' SET name = ? WHERE id = ?')->execute([$name, $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.username':
        $u = writer();
        $username = clean_username(body(), (int)$u['id']);
        db()->prepare('UPDATE ' . t('users') . ' SET username = ? WHERE id = ?')->execute([$username, $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.ranking':
        $u = writer();
        db()->prepare('UPDATE ' . t('users') . ' SET show_in_ranking = ? WHERE id = ?')->execute([empty(body()['visible']) ? 0 : 1, $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'avatar.buy':
        // Paid in points; a transaction locks the user row so two taps at once never spend the same points twice
        $u = writer();
        $uid = (int)$u['id'];
        $id = s(body(), 'item', 30);
        $st = db()->prepare('SELECT price FROM ' . t('avatar_items') . ' WHERE id = ? AND enabled = 1');
        $st->execute([$id]);
        $price = $st->fetchColumn();
        if ($price === false) fail('not_found', 404);
        $price = (int)$price;
        $st = db()->prepare('SELECT 1 FROM ' . t('user_items') . ' WHERE user_id = ? AND item_id = ?');
        $st->execute([$uid, $id]);
        if ($st->fetchColumn() || $price === 0) send_json(['ok' => true, 'user' => public_user(user_row($uid))]);
        db()->beginTransaction();
        try {
            $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ? FOR UPDATE');
            $st->execute([$uid]);
            $row = $st->fetch();
            if (!$row || user_balance($row) < $price) {
                db()->rollBack();
                fail('not_enough_points', 422);
            }
            db()->prepare('INSERT IGNORE INTO ' . t('user_items') . ' (user_id, item_id, price, bought_at) VALUES (?,?,?,?)')->execute([$uid, $id, $price, now_utc()]);
            db()->commit();
        } catch (PDOException $e) {
            if (db()->inTransaction()) db()->rollBack();
            throw $e;
        }
        send_json(['ok' => true, 'user' => public_user(user_row($uid))]);

    case 'avatar.set':
        // character:background:accessory:frame; an empty character is the initial. Only what is owned (or free) can be worn.
        $u = writer();
        $uid = (int)$u['id'];
        $b = body();
        $owned = array_column(array_filter(avatar_shop($uid), fn($i) => $i['owned']), 'kind', 'id');
        $character = s($b, 'character', 30);
        $background = s($b, 'background', 30);
        $accessory = s($b, 'accessory', 30);
        $frame = s($b, 'frame', 30);
        if ($character !== '' && ($owned[$character] ?? '') !== 'character') fail('not_owned', 403);
        if ($background !== '' && ($owned[$background] ?? '') !== 'background') fail('not_owned', 403);
        if ($accessory !== '' && ($owned[$accessory] ?? '') !== 'accessory') fail('not_owned', 403);
        if ($frame !== '' && ($owned[$frame] ?? '') !== 'frame') fail('not_owned', 403);
        $code = rtrim("$character:$background:$accessory:$frame", ':');
        db()->prepare('UPDATE ' . t('users') . ' SET avatar = ? WHERE id = ?')->execute([$code === '' ? null : $code, $uid]);
        send_json(['ok' => true, 'user' => public_user(user_row($uid))]);

    case 'board.daily':
        // Only usernames, avatars and results: nothing else about a player leaves the server
        only('POST');
        $b = body();
        $day = (string)($b['day'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) fail('bad_request', 422);
        $u = current_user();
        $me = $u ? (int)$u['id'] : 0;
        $friendsOnly = ($b['scope'] ?? '') === 'friends';
        if ($friendsOnly && !$u) fail('unauthenticated', 401);
        $sql = 'SELECT u.id, u.username, u.avatar, MAX(g.score) AS score, MAX(g.correct) AS correct, MAX(g.total) AS total, MIN(g.seconds) AS seconds
            FROM ' . t('games') . ' g JOIN ' . t('users') . " u ON u.id = g.user_id
            WHERE g.mode = 'daily' AND g.day = ? AND u.username IS NOT NULL AND u.status = 'active'";
        $args = [$day];
        // Who chose to stay out is seen by nobody else: they still find their own result
        $sql .= ' AND (u.show_in_ranking = 1 OR u.id = ?)';
        $args[] = $me;
        if ($friendsOnly) {
            $sql .= ' AND (u.id = ? OR u.id IN (SELECT friend_id FROM ' . t('friends') . ' WHERE user_id = ?))';
            array_push($args, $me, $me);
        }
        $st = db()->prepare($sql . ' GROUP BY u.id, u.username, u.avatar ORDER BY score DESC, seconds ASC, u.username LIMIT 1000');
        $st->execute($args);
        $rows = [];
        foreach ($st->fetchAll() as $i => $x) {
            if ($i >= 50 && (int)$x['id'] !== $me) continue; // the top of the ranking, plus the player wherever they are
            $rows[] = ['rank' => $i + 1, 'username' => $x['username'], 'avatar' => $x['avatar'] ?? null, 'score' => (int)$x['score'], 'correct' => (int)$x['correct'], 'total' => (int)$x['total'], 'seconds' => (int)$x['seconds'], 'me' => (int)$x['id'] === $me];
        }
        send_json(['ok' => true, 'rows' => $rows]);

    case 'friends.list':
        $u = writer();
        $st = db()->prepare('SELECT u.username, u.avatar FROM ' . t('friends') . ' f JOIN ' . t('users') . " u ON u.id = f.friend_id WHERE f.user_id = ? AND u.status = 'active' AND u.username IS NOT NULL ORDER BY u.username");
        $st->execute([$u['id']]);
        send_json(['ok' => true, 'friends' => array_map(fn($x) => ['username' => $x['username'], 'avatar' => $x['avatar'] ?? null], $st->fetchAll())]);

    case 'friends.search':
        $u = writer();
        $q = trim((string)(body()['q'] ?? ''));
        if (!preg_match('/^[A-Za-z0-9_]{2,20}$/', $q)) send_json(['ok' => true, 'players' => []]);
        $st = db()->prepare('SELECT u.username, u.avatar, f.friend_id IS NOT NULL AS friend FROM ' . t('users') . ' u LEFT JOIN ' . t('friends') . " f ON f.friend_id = u.id AND f.user_id = ?
            WHERE u.username LIKE ? AND u.id <> ? AND u.status = 'active' ORDER BY u.username LIMIT 20");
        $st->execute([$u['id'], str_replace('_', '\\_', $q) . '%', $u['id']]);
        send_json(['ok' => true, 'players' => array_map(fn($x) => ['username' => $x['username'], 'avatar' => $x['avatar'] ?? null, 'friend' => (bool)$x['friend']], $st->fetchAll())]);

    case 'friends.add':
    case 'friends.remove':
        $u = writer();
        $st = db()->prepare('SELECT id FROM ' . t('users') . " WHERE username = ? AND status = 'active'");
        $st->execute([(string)(body()['username'] ?? '')]);
        $id = (int)$st->fetchColumn();
        if (!$id || $id === (int)$u['id']) fail('not_found', 404);
        if ($r === 'friends.remove') {
            db()->prepare('DELETE FROM ' . t('friends') . ' WHERE user_id = ? AND friend_id = ?')->execute([$u['id'], $id]);
            send_json(['ok' => true]);
        }
        $st = db()->prepare('SELECT COUNT(*) FROM ' . t('friends') . ' WHERE user_id = ?');
        $st->execute([$u['id']]);
        if ((int)$st->fetchColumn() >= MAX_FRIENDS) fail('too_many_friends', 422);
        $ins = db()->prepare('INSERT IGNORE INTO ' . t('friends') . ' (user_id, friend_id, created_at) VALUES (?,?,?)');
        $ins->execute([$u['id'], $id, now_utc()]);
        $me = user_row((int)$u['id']);
        if ($ins->rowCount() && !empty($me['username'])) notify_user($id, 'friends', gmdate('Y-m-d'), $me['username'], ['name' => $me['username']]);
        send_json(['ok' => true]);

    case 'notify.get':
        $u = writer();
        $row = user_row((int)$u['id']);
        $now = new DateTimeImmutable('now', new DateTimeZone(SERVER_TIMEZONE));
        send_json([
            'ok' => true,
            'types' => NOTIFY_TYPES,
            'live' => NOTIFY_LIVE,
            'scheduled' => NOTIFY_SCHEDULED,
            'off' => notify_off($row),
            'hours' => notify_hours($row),
            'server_tz' => SERVER_TIMEZONE,
            'server_time' => $now->format('H:i'),
            'server_offset' => $now->getOffset() / 3600,
        ]);

    case 'notify.save':
        $u = writer();
        $b = body();
        $off = array_values(array_intersect(NOTIFY_TYPES, (array)($b['off'] ?? [])));
        $rawHours = (array)($b['hours'] ?? []);
        $hours = DEFAULT_NOTIFY_HOURS;
        foreach (NOTIFY_SCHEDULED as $k) {
            if (isset($rawHours[$k]) && is_numeric($rawHours[$k])) {
                $h = max(0, min(23, (int)$rawHours[$k]));
                $hours[$k] = $h;
            }
        }
        db()->prepare('UPDATE ' . t('users') . ' SET notify_off = ?, notify_hours = ? WHERE id = ?')->execute([json_encode($off), json_encode($hours), $u['id']]);
        db()->prepare('DELETE FROM ' . t('notifications') . " WHERE user_id = ? AND sent_at IS NULL AND FIND_IN_SET(type, ?)")->execute([$u['id'], implode(',', $off)]);
        send_json(['ok' => true, 'off' => $off, 'hours' => $hours]);

    case 'notify.pending':
        // Called by the service worker when a push wakes it: what it reads is told once
        only('GET');
        $u = require_user();
        $row = user_row((int)$u['id']);
        $st = db()->prepare('SELECT id, type, data FROM ' . t('notifications') . ' WHERE user_id = ? AND sent_at IS NULL AND created_at > ? ORDER BY id LIMIT 5');
        $st->execute([$u['id'], gmdate('Y-m-d H:i:s', time() - 86400)]);
        $list = $st->fetchAll();
        $out = array_map(fn($n) => ['type' => $n['type'], 'text' => notify_text((string)($row['notify_lang'] ?: 'en'), $n['type'], json_decode($n['data'], true) ?: [])], $list);
        if ($list) db()->prepare('UPDATE ' . t('notifications') . ' SET sent_at = ? WHERE user_id = ? AND sent_at IS NULL AND id <= ?')->execute([now_utc(), $u['id'], end($list)['id']]);
        send_json(['ok' => true, 'items' => $out]);

    case 'push.key':
        only('GET');
        require_user();
        $keys = vapid_keys();
        if (!$keys) fail('push_unavailable', 503);
        send_json(['ok' => true, 'key' => $keys['public']]);

    case 'push.subscribe':
        $u = writer();
        $b = body();
        $endpoint = (string)($b['endpoint'] ?? '');
        if (strlen($endpoint) > 700 || !push_endpoint_allowed($endpoint)) fail('bad_request', 422);
        $st = db()->prepare('SELECT COUNT(*) FROM ' . t('push_subscriptions') . ' WHERE user_id = ?');
        $st->execute([$u['id']]);
        if ((int)$st->fetchColumn() >= MAX_DEVICES) db()->prepare('DELETE FROM ' . t('push_subscriptions') . ' WHERE user_id = ? ORDER BY created_at LIMIT 1')->execute([$u['id']]);
        db()->prepare('INSERT INTO ' . t('push_subscriptions') . ' (user_id, endpoint, endpoint_hash, created_at) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)')
            ->execute([$u['id'], $endpoint, hash('sha256', $endpoint), now_utc()]);
        $lang = (string)($b['lang'] ?? '');
        if (in_array($lang, LANGS, true)) db()->prepare('UPDATE ' . t('users') . ' SET notify_lang = ? WHERE id = ?')->execute([$lang, $u['id']]);
        send_json(['ok' => true]);

    case 'push.unsubscribe':
        $u = writer();
        db()->prepare('DELETE FROM ' . t('push_subscriptions') . ' WHERE user_id = ? AND endpoint_hash = ?')->execute([$u['id'], hash('sha256', (string)(body()['endpoint'] ?? ''))]);
        send_json(['ok' => true]);

    case 'auth.password':
        $u = writer();
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['current'] ?? ''), $row['password_hash'])) fail('bad_password', 422);
        $pw = (string)($b['password'] ?? '');
        validate_password($pw);
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.delete':
        $u = writer(true);
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['password'] ?? ''), $row['password_hash'])) fail('bad_password', 422);
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]); // sessions and games follow (ON DELETE CASCADE)
        set_auth_cookie('', time() - 3600);
        send_json(['ok' => true]);

    case 'sync':
        // The device sends the games the server has not seen yet and gets back all of them
        $u = writer();
        $b = body();
        $ins = db()->prepare('INSERT IGNORE INTO ' . t('games') . ' (user_id, uid, mode, stage, stars, score, spent, correct, total, seconds, cats, day, played_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
        foreach (array_slice((array)($b['games'] ?? []), 0, 500) as $g) {
            $c = is_array($g) ? clean_game($g) : null;
            if (!$c) continue;
            $ins->execute([$u['id'], $c['uid'], $c['mode'], $c['stage'], $c['stars'], $c['score'], $c['spent'], $c['correct'], $c['total'], $c['seconds'], $c['cats'], $c['day'], now_utc()]);
            if ($ins->rowCount()) notify_overtaken(user_row((int)$u['id']), $c);
        }
        send_json(['ok' => true, 'games' => user_games($u['id']), 'user' => public_user(user_row((int)$u['id']))]);

    // ======================================================== admin panel

    case 'admin.overview':
        only('GET');
        require_admin();
        $n = fn(string $sql) => (int)db()->query($sql)->fetchColumn();
        $g = t('games');
        $perCat = [];
        try {
            foreach (db()->query("SELECT cats FROM $g ORDER BY played_at DESC LIMIT 5000") as $row) {
                $parsed = json_decode((string)($row['cats'] ?? ''), true);
                if (!is_array($parsed)) continue;
                foreach ($parsed as $c => $v) {
                    if (!is_array($v)) continue;
                    $perCat[$c][0] = ($perCat[$c][0] ?? 0) + (int)($v[0] ?? 0);
                    $perCat[$c][1] = ($perCat[$c][1] ?? 0) + (int)($v[1] ?? 0);
                }
            }
        } catch (Throwable $e) {
            $perCat = [];
        }
        try {
            $stages = db()->query("SELECT stage, COUNT(*) AS played, SUM(stars > 0) AS passed FROM $g WHERE mode = 'level' GROUP BY stage ORDER BY stage")->fetchAll();
        } catch (Throwable $e) {
            $stages = [];
        }
        send_json(['ok' => true,
            'counts' => [
                'players' => $n('SELECT COUNT(*) FROM ' . t('users')),
                'games' => $n("SELECT COUNT(*) FROM $g"),
                'week' => $n("SELECT COUNT(*) FROM $g WHERE played_at > UTC_TIMESTAMP() - INTERVAL 7 DAY"),
                'questions' => $n('SELECT COUNT(*) FROM ' . t('questions') . ' WHERE active = 1'),
                'right' => $n("SELECT COALESCE(SUM(correct), 0) FROM $g"),
                'answers' => $n("SELECT COALESCE(SUM(total), 0) FROM $g"),
            ],
            'cats' => (object)$perCat,
            'stages' => $stages,
            'settings' => game_settings(),
        ]);

    case 'admin.settings.save':
        admin_writer();
        $b = body();
        $cats = array_values(array_intersect(QUESTION_TYPES, (array)($b['disabledCats'] ?? [])));
        if (count($cats) >= count(QUESTION_TYPES)) fail('no_types_left', 422);
        $riddles = array_values(array_filter((array)($b['disabledRiddles'] ?? []), fn($x) => is_string($x) && preg_match('/^[a-z0-9-]{2,30}$/', $x)));
        $difficulty = [];
        foreach ((array)($b['difficulty'] ?? []) as $k => $v) {
            if (is_string($k) && preg_match('/^[a-z0-9-]{2,30}$/', $k) && is_numeric($v)) $difficulty[$k] = round(max(0, min(MAX_DIFFICULTY, (float)$v)), 2);
        }
        $cost = [];
        foreach (TIERS as $tier) $cost[$tier] = max(0, min(5000, (int)(((array)($b['hintCost'] ?? []))[$tier] ?? 0)));
        $catWeights = [];
        foreach ((array)($b['catWeights'] ?? []) as $k => $v) {
            if (is_string($k) && in_array($k, QUESTION_TYPES, true) && is_numeric($v)) {
                $catWeights[$k] = round(max(0.0, min(1.0, (float)$v)), 2);
            }
        }
        $values = ['levelLength' => max(3, min(10, (int)($b['levelLength'] ?? 5))), 'hintCost' => $cost, 'disabledCats' => $cats, 'disabledRiddles' => $riddles, 'difficulty' => (object)$difficulty, 'catWeights' => (object)$catWeights];
        $st = db()->prepare('INSERT INTO ' . t('settings') . ' (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v)');
        foreach ($values as $k => $v) $st->execute([$k, json_encode($v)]);
        send_json(['ok' => true, 'settings' => game_settings()]);

    case 'admin.questions':
        only('GET');
        require_admin();
        $list = db()->query('SELECT * FROM ' . t('questions') . ' ORDER BY id DESC')->fetchAll();
        send_json(['ok' => true, 'questions' => array_map(fn($q) => ['id' => (int)$q['id'], 'tier' => $q['tier'], 'difficulty' => (float)$q['difficulty'], 'active' => (bool)$q['active'], 'data' => json_decode($q['data'], true)], $list), 'settings' => game_settings()]);

    case 'admin.question.save':
        admin_writer();
        $b = body();
        if (!is_numeric($b['difficulty'] ?? null)) fail('bad_request', 422);
        $difficulty = round(max(0, min(MAX_DIFFICULTY, (float)$b['difficulty'])), 2);
        $tier = TIERS[min(6, (int)floor($difficulty / 1.5))];
        $data = json_encode(clean_question_data((array)($b['texts'] ?? [])), JSON_UNESCAPED_UNICODE);
        $active = empty($b['active']) ? 0 : 1;
        $id = (int)($b['id'] ?? 0);
        if ($id) db()->prepare('UPDATE ' . t('questions') . ' SET tier = ?, difficulty = ?, active = ?, data = ?, updated_at = ? WHERE id = ?')->execute([$tier, $difficulty, $active, $data, now_utc(), $id]);
        else db()->prepare('INSERT INTO ' . t('questions') . ' (tier, difficulty, active, data, created_at, updated_at) VALUES (?,?,?,?,?,?)')->execute([$tier, $difficulty, $active, $data, now_utc(), now_utc()]);
        send_json(['ok' => true]);

    case 'admin.question.delete':
        admin_writer();
        db()->prepare('DELETE FROM ' . t('questions') . ' WHERE id = ?')->execute([(int)(body()['id'] ?? 0)]);
        send_json(['ok' => true]);

    case 'admin.games':
        only('GET');
        require_admin();
        try {
            $games = db()->query('SELECT g.user_id, g.uid, g.mode, g.stage, g.stars, g.score, g.spent, g.correct, g.total, g.seconds, g.played_at, COALESCE(u.name, "–") AS name FROM ' . t('games') . ' g LEFT JOIN ' . t('users') . ' u ON u.id = g.user_id ORDER BY g.played_at DESC LIMIT 200')->fetchAll();
        } catch (Throwable $e) {
            $games = [];
        }
        send_json(['ok' => true, 'games' => $games]);

    case 'admin.game.delete':
        admin_writer();
        $b = body();
        db()->prepare('DELETE FROM ' . t('games') . ' WHERE user_id = ? AND uid = ?')->execute([(int)($b['userId'] ?? 0), (string)($b['uid'] ?? '')]);
        send_json(['ok' => true]);

    case 'admin.players':
        only('GET');
        $me = require_admin();
        $players = db()->query('SELECT u.id, u.name, u.username, u.avatar, u.email, u.role, u.status, u.created_at, u.last_login_at, u.google_sub IS NOT NULL AS google, u.email_verified_at IS NOT NULL AS verified,
            COUNT(g.uid) AS games, GREATEST(0, ' . START_POINTS . ' + COALESCE(SUM(g.score), 0) - COALESCE(SUM(g.spent), 0) - COALESCE((SELECT SUM(price) FROM ' . t('user_items') . ' ui WHERE ui.user_id = u.id), 0) + u.points_adjust) AS points, COALESCE(SUM(g.spent), 0) AS spent, COALESCE(MAX(CASE WHEN g.stars > 0 THEN g.stage END), 0) AS stage
            FROM ' . t('users') . ' u LEFT JOIN ' . t('games') . ' g ON g.user_id = u.id GROUP BY u.id ORDER BY u.created_at DESC LIMIT 1000')->fetchAll();
        send_json(['ok' => true, 'players' => $players, 'me' => $me['id']]);

    case 'admin.player.update':
        $me = admin_writer();
        $b = body();
        $row = target_player($me, $b);
        $status = ($b['status'] ?? $row['status']) === 'disabled' ? 'disabled' : 'active';
        $role = !empty($b['admin']) ? ROLE_ADMIN : 0;
        db()->prepare('UPDATE ' . t('users') . ' SET status = ?, role = ? WHERE id = ?')->execute([$status, $role, $row['id']]);
        if ($status === 'disabled') db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$row['id']]);
        send_json(['ok' => true]);

    case 'admin.player':
        // Read only: what one player did, for the administrators
        $me = admin_writer();
        $id = (int)(body()['id'] ?? 0);
        $row = user_row($id);
        if (!$row) fail('bad_request', 404);
        $g = t('games');
        $one = function (string $sql, array $args) {
            $st = db()->prepare($sql);
            $st->execute($args);
            return $st->fetch() ?: [];
        };
        $all = function (string $sql, array $args) {
            $st = db()->prepare($sql);
            $st->execute($args);
            return $st->fetchAll();
        };
        $sum = $one("SELECT COUNT(*) AS games, COALESCE(SUM(score), 0) AS earned, COALESCE(SUM(spent), 0) AS spent, COALESCE(SUM(correct), 0) AS correct,
            COALESCE(SUM(total), 0) AS total, COALESCE(SUM(seconds), 0) AS seconds, COUNT(DISTINCT CASE WHEN mode = 'level' AND stars > 0 THEN stage END) AS levels,
            COALESCE(MAX(CASE WHEN mode = 'level' AND stars > 0 THEN stage END), 0) AS top_level, COUNT(DISTINCT CASE WHEN mode = 'daily' THEN day END) AS dailies,
            COALESCE(MAX(CASE WHEN mode = 'daily' THEN score END), 0) AS best_daily, COALESCE(MAX(CASE WHEN mode = 'survival' THEN score END), 0) AS best_survival,
            MIN(played_at) AS first_game, MAX(played_at) AS last_game FROM $g WHERE user_id = ?", [$id]);
        $count = fn(string $sql) => (int)($one($sql, [$id])['n'] ?? 0);
        $avSpent = avatar_spent($id);
        send_json(['ok' => true, 'player' => [
            'id' => $id, 'name' => $row['name'], 'username' => $row['username'], 'avatar' => $row['avatar'] ?? null, 'email' => $row['email'], 'status' => $row['status'],
            'admin' => (int)$row['role'] >= ROLE_ADMIN, 'google' => !empty($row['google_sub']), 'password' => $row['password_hash'] !== '',
            'verified' => $row['email_verified_at'], 'ranking' => (int)$row['show_in_ranking'] === 1,
            'createdAt' => $row['created_at'], 'lastLogin' => $row['last_login_at'],
            'devices' => $count('SELECT COUNT(*) AS n FROM ' . t('push_subscriptions') . ' WHERE user_id = ?'),
            'following' => $count('SELECT COUNT(*) AS n FROM ' . t('friends') . ' WHERE user_id = ?'),
            'followers' => $count('SELECT COUNT(*) AS n FROM ' . t('friends') . ' WHERE friend_id = ?'),
            'adjust' => (int)$row['points_adjust'],
            'avatarSpent' => $avSpent,
            'balance' => max(0, START_POINTS + (int)$sum['earned'] - (int)$sum['spent'] - $avSpent + (int)$row['points_adjust']),
        ], 'stats' => $sum,
            'games' => $all("SELECT mode, stage, stars, score, spent, correct, total, seconds, day, played_at FROM $g WHERE user_id = ? ORDER BY played_at DESC, uid LIMIT 100", [$id]),
            'log' => $all('SELECT l.action, l.detail, l.created_at, a.name AS admin FROM ' . t('admin_log') . ' l LEFT JOIN ' . t('users') . ' a ON a.id = l.admin_id WHERE l.user_id = ? ORDER BY l.id DESC LIMIT 30', [$id]),
        ]);

    case 'admin.player.points':
        // The wallet is what the games earned and spent: the change is kept apart, so the games stay as they were played
        $me = admin_writer();
        $b = body();
        $row = target_player($me, $b);
        if (!is_numeric($b['balance'] ?? null)) fail('bad_request', 422);
        $want = max(0, min(10000000, (int)$b['balance']));
        $before = user_balance($row);
        $adjust = $want - $before + (int)$row['points_adjust'];
        db()->prepare('UPDATE ' . t('users') . ' SET points_adjust = ? WHERE id = ?')->execute([$adjust, $row['id']]);
        $note = mb_substr(trim((string)($b['note'] ?? '')), 0, 200);
        db()->prepare('INSERT INTO ' . t('admin_log') . ' (user_id, admin_id, action, detail, created_at) VALUES (?,?,?,?,?)')
            ->execute([$row['id'], $me['id'], 'points', json_encode(['from' => $before, 'to' => $want, 'note' => $note], JSON_UNESCAPED_UNICODE), now_utc()]);
        send_json(['ok' => true, 'balance' => $want]);

    case 'admin.player.delete':
        $me = admin_writer();
        $row = target_player($me, body());
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$row['id']]);
        send_json(['ok' => true]);

    case 'admin.avatars':
        only('GET');
        require_admin();
        $rows = db()->query('SELECT i.id, i.kind, i.price, i.enabled, (SELECT COUNT(*) FROM ' . t('user_items') . ' u WHERE u.item_id = i.id) AS sold FROM ' . t('avatar_items') . ' i ORDER BY i.kind DESC, i.sort')->fetchAll();
        send_json(['ok' => true, 'items' => array_map(fn($r) => ['id' => $r['id'], 'kind' => $r['kind'], 'price' => (int)$r['price'], 'enabled' => (int)$r['enabled'], 'sold' => (int)$r['sold']], $rows)]);

    case 'admin.avatar.save':
        admin_writer();
        $b = body();
        $st = db()->prepare('UPDATE ' . t('avatar_items') . ' SET price = ?, enabled = ? WHERE id = ?');
        $st->execute([max(0, min(1000000, (int)($b['price'] ?? 0))), empty($b['enabled']) ? 0 : 1, (string)($b['id'] ?? '')]);
        send_json(['ok' => true]);

    default:
        fail('bad_request', 404);
}
