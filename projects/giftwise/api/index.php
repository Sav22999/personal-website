<?php
/**
 * Giftwise — JSON API.  api/index.php?r=<action>
 *
 * Public:   auth.config, auth.me, auth.register, auth.login, auth.google, auth.logout, invite.info
 * Account:  state, auth.profile, auth.password, auth.delete
 * People:   user.search, user.get, username.check, follow, unfollow, follow.accept, follow.decline, follower.remove,
 *           invite.accept, account.username, account.privacy
 *           (you see the lists of the people you follow; following needs their approval unless they accept everyone)
 * Lists:    list.save, list.delete, list.archive, item.save, item.delete, link.preview
 * Friends' lists (secret coordination): list.view, claim.set, comment.add, comment.delete
 *
 * THE RULE: claims and comments are about a list the user does NOT own (only followers of the owner see it). The owner never receives them:
 * state/list.view build the owner's data without them, and claim/comment actions refuse the owner.
 * Every POST needs the X-CSRF-Token header (given at sign-in).
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/mail.php';

header('X-Robots-Tag: noindex');
set_exception_handler(function (Throwable $e) {
    if ($e->getMessage() === 'not_installed') fail('Giftwise is not installed yet: open install-db.php.', 503, 'not_installed');
    error_log('Giftwise API: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    $debug = (bool)config_value('debug', false);
    send_json(['ok' => false, 'error' => 'server', 'message' => tr('Internal server error.') . ($debug ? ' ' . $e->getMessage() : '')], 500);
});

if (!credentials()) fail('Giftwise is not installed yet: open install-db.php.', 503, 'not_installed');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json') !== 0) fail('Unsupported Content-Type.', 415, 'content_type');

$r = (string)($_GET['r'] ?? '');

function only(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) fail('Method not allowed.', 405, 'method');
}

function reader(): array
{
    only('GET');
    return require_user();
}

function writer(): array
{
    only('POST');
    $u = require_user();
    require_csrf($u);
    return $u;
}

// ---------------------------------------------------------------- helpers

/** Logs an action into the user's activity history (for audit and undo). */
function log_history(int $userId, string $action, ?int $listId, string $listTitle, ?int $itemId, string $itemTitle, array $details): int
{
    $now = now_utc();
    $sql = 'INSERT INTO ' . t('history') . ' (user_id, action, list_id, list_title, item_id, item_title, details, undone, created_at) VALUES (?,?,?,?,?,?,?,0,?)';
    $params = [$userId, $action, $listId, $listTitle, $itemId, $itemTitle, json_encode($details, JSON_UNESCAPED_UNICODE), $now];
    try {
        $st = db()->prepare($sql);
        $st->execute($params);
        return (int)db()->lastInsertId();
    } catch (Throwable $e) {
        try {
            require_once __DIR__ . '/../lib/schema.php';
            install_schema(db(), (string)config_value('prefix', 'gw_'));
            $st = db()->prepare($sql);
            $st->execute($params);
            return (int)db()->lastInsertId();
        } catch (Throwable $ignored) {
            return 0;
        }
    }
}

function pair(int $x, int $y): array { return $x < $y ? [$x, $y] : [$y, $x]; }

function user_row(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function follow_row(int $follower, int $followee): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('follows') . ' WHERE follower_id = ? AND followee_id = ?');
    $st->execute([$follower, $followee]);
    return $st->fetch() ?: null;
}

/** $follower follows $followee (accepted). */
function follows(int $follower, int $followee): bool
{
    $f = follow_row($follower, $followee);
    return $f && $f['status'] === 'accepted';
}

function set_follow(int $follower, int $followee, string $status): void
{
    if ($follower === $followee) return;
    db()->prepare('INSERT INTO ' . t('follows') . ' (follower_id, followee_id, status, created_at, accepted_at) VALUES (?,?,?,?,?)
        ON DUPLICATE KEY UPDATE status = IF(status = \'accepted\', status, VALUES(status)), accepted_at = IF(status = \'accepted\' AND accepted_at IS NOT NULL, accepted_at, VALUES(accepted_at))')
        ->execute([$follower, $followee, $status, now_utc(), $status === 'accepted' ? now_utc() : null]);
}

/** Stops $follower following $followee and drops the lists $followee had shared with them specifically. */
function drop_follow(int $follower, int $followee): void
{
    db()->prepare('DELETE FROM ' . t('follows') . ' WHERE follower_id = ? AND followee_id = ?')->execute([$follower, $followee]);
    db()->prepare('DELETE v FROM ' . t('list_viewers') . ' v JOIN ' . t('lists') . ' l ON l.id = v.list_id WHERE l.owner_id = ? AND v.user_id = ?')->execute([$followee, $follower]);
}

/** Accepted followers of $me (the people who can see my lists). */
function follower_ids(int $me): array
{
    $st = db()->prepare('SELECT follower_id FROM ' . t('follows') . " WHERE followee_id = ? AND status = 'accepted'");
    $st->execute([$me]);
    return array_map('intval', array_column($st->fetchAll(), 'follower_id'));
}

/** Username: 3–20 characters, lower case letters, digits, dot and underscore, starting/ending with a letter or digit. */
function valid_username($v): string
{
    $u = strtolower(trim(ltrim((string)$v, '@')));
    if (!preg_match('/^[a-z0-9](?:[a-z0-9._]{1,18})[a-z0-9]$/', $u) || strpos($u, '..') !== false) fail('The username can have 3–20 letters, digits, dots and underscores (no accents or spaces).', 422, 'validation');
    if (in_array($u, ['admin', 'giftwise', 'support', 'help', 'root', 'system', 'api', 'null', 'undefined'], true)) fail('This username is not available.', 409, 'username_taken');
    return $u;
}

function username_taken(string $u, int $exceptId = 0): bool
{
    $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE username = ? AND id <> ?');
    $st->execute([$u, $exceptId]);
    return (bool)$st->fetchColumn();
}

/** What $me and $other are to each other: following = me → other, followsMe = other → me ('none' | 'pending' | 'accepted'). */
function relation(int $me, int $other): array
{
    $a = follow_row($me, $other);
    $b = follow_row($other, $me);
    return ['following' => $a ? $a['status'] : 'none', 'followsMe' => $b ? $b['status'] : 'none'];
}

function person_out(array $u, int $me): array
{
    return ['id' => (int)$u['id'], 'name' => $u['name'], 'username' => $u['username']] + relation($me, (int)$u['id']);
}

function unique_invite_code(): string
{
    for ($i = 0; $i < 20; $i++) {
        $c = random_code(10);
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE invite_code = ?');
        $st->execute([$c]);
        if (!$st->fetchColumn()) return $c;
    }
    throw new RuntimeException('invite code');
}

function list_row(int $id): array
{
    $st = db()->prepare('SELECT * FROM ' . t('lists') . ' WHERE id = ?');
    $st->execute([$id]);
    $l = $st->fetch();
    if (!$l) fail('List not found.', 404, 'not_found');
    return $l;
}

/** The list owned by $me (or 404). */
function own_list(int $id, int $me): array
{
    $l = list_row($id);
    if ((int)$l['owner_id'] !== $me) fail('List not found.', 404, 'not_found');
    return $l;
}

/** A list of someone $me follows — never the owner's own list (the owner must not see the coordination). */
function friend_list(int $id, int $me): array
{
    $l = list_row($id);
    if ((int)$l['owner_id'] === $me) fail('This is your list: what your friends decide stays a surprise.', 403, 'owner');
    if (!can_view((array)$l, $me)) fail('List not found.', 404, 'not_found');
    return $l;
}

function can_view(array $l, int $me): bool
{
    if ((int)$l['owner_id'] === $me) return true;
    if (!empty($l['archived']) || !follows($me, (int)$l['owner_id'])) return false;
    if ($l['visibility'] === 'friends') return true;
    $st = db()->prepare('SELECT 1 FROM ' . t('list_viewers') . ' WHERE list_id = ? AND user_id = ?');
    $st->execute([$l['id'], $me]);
    return (bool)$st->fetchColumn();
}

function item_out(array $i): array
{
    return [
        'id' => (int)$i['id'], 'listId' => (int)$i['list_id'], 'title' => $i['title'], 'url' => $i['url'], 'image' => $i['image_url'],
        'price' => $i['price'] !== null ? (float)$i['price'] : null, 'currency' => $i['currency'], 'notes' => $i['notes'],
        'priority' => (int)$i['priority'], 'quantity' => (int)$i['quantity'], 'createdAt' => $i['created_at'], 'updatedAt' => $i['updated_at'],
    ];
}

function list_out(array $l): array
{
    return [
        'id' => (int)$l['id'], 'ownerId' => (int)$l['owner_id'], 'title' => $l['title'], 'occasion' => $l['occasion'], 'eventDate' => $l['event_date'],
        'description' => $l['description'], 'visibility' => $l['visibility'], 'archived' => (bool)$l['archived'], 'createdAt' => $l['created_at'], 'updatedAt' => $l['updated_at'],
    ];
}

function items_of(array $listIds): array
{
    if (!$listIds) return [];
    $in = implode(',', array_map('intval', $listIds));
    return db()->query('SELECT * FROM ' . t('items') . " WHERE list_id IN ($in) ORDER BY priority DESC, sort, id")->fetchAll();
}

/** Everything the app needs. The owner's own lists come WITHOUT claims or comments. */
function state_payload(array $u): array
{
    $me = (int)$u['id'];
    // People: who I follow, who follows me, requests waiting
    $following = $followers = $incoming = $outgoing = [];
    $st = db()->prepare('SELECT f.*, u.id AS uid, u.name, u.username FROM ' . t('follows') . ' f JOIN ' . t('users') . ' u ON u.id = IF(f.follower_id = ?, f.followee_id, f.follower_id)
        WHERE (f.follower_id = ? OR f.followee_id = ?) AND u.status = \'active\' ORDER BY u.name');
    $st->execute([$me, $me, $me]);
    foreach ($st->fetchAll() as $f) {
        $p = ['id' => (int)$f['uid'], 'name' => $f['name'], 'username' => $f['username']];
        $mine = (int)$f['follower_id'] === $me;
        if ($f['status'] === 'accepted') { if ($mine) $following[] = $p + ['since' => $f['accepted_at']]; else $followers[] = $p + ['since' => $f['accepted_at']]; }
        elseif ($mine) $outgoing[] = $p + ['at' => $f['created_at']];
        else $incoming[] = $p + ['at' => $f['created_at']];
    }

    // My lists (no secrets)
    $st = db()->prepare('SELECT * FROM ' . t('lists') . ' WHERE owner_id = ? ORDER BY archived, (event_date IS NULL), event_date, id DESC');
    $st->execute([$me]);
    $mine = $st->fetchAll();
    $ids = array_map(fn($l) => (int)$l['id'], $mine);
    $viewers = [];
    if ($ids) foreach (db()->query('SELECT list_id, user_id FROM ' . t('list_viewers') . ' WHERE list_id IN (' . implode(',', $ids) . ')') as $v) $viewers[(int)$v['list_id']][] = (int)$v['user_id'];
    $items = [];
    foreach (items_of($ids) as $i) $items[(int)$i['list_id']][] = item_out($i);
    $myLists = array_map(fn($l) => list_out($l) + ['items' => $items[(int)$l['id']] ?? [], 'viewers' => $viewers[(int)$l['id']] ?? []], $mine);

    // Friends' lists I can see, with a summary of the coordination
    $friendLists = []; // lists of the people I follow
    $fids = array_map(fn($f) => $f['id'], $following);
    if ($fids) {
        $in = implode(',', $fids);
        $st = db()->prepare('SELECT l.*, u.name AS owner_name FROM ' . t('lists') . ' l JOIN ' . t('users') . " u ON u.id = l.owner_id
            WHERE l.owner_id IN ($in) AND l.archived = 0 AND (l.visibility = 'friends' OR EXISTS (SELECT 1 FROM " . t('list_viewers') . ' v WHERE v.list_id = l.id AND v.user_id = ?))
            ORDER BY (l.event_date IS NULL OR l.event_date < CURDATE()), l.event_date, l.updated_at DESC');
        $st->execute([$me]);
        $rows = $st->fetchAll();
        $lids = array_map(fn($l) => (int)$l['id'], $rows);
        $count = $taken = $consid = $mineC = $comments = [];
        if ($lids) {
            $lin = implode(',', $lids);
            foreach (db()->query('SELECT list_id, COUNT(*) n FROM ' . t('items') . " WHERE list_id IN ($lin) GROUP BY list_id") as $x) $count[(int)$x['list_id']] = (int)$x['n'];
            foreach (db()->query('SELECT i.list_id, c.status, COUNT(DISTINCT c.item_id) n FROM ' . t('claims') . ' c JOIN ' . t('items') . " i ON i.id = c.item_id WHERE i.list_id IN ($lin) GROUP BY i.list_id, c.status") as $x) {
                if ($x['status'] === 'taken') $taken[(int)$x['list_id']] = (int)$x['n']; else $consid[(int)$x['list_id']] = (int)$x['n'];
            }
            $st2 = db()->prepare('SELECT i.list_id, COUNT(*) n FROM ' . t('claims') . ' c JOIN ' . t('items') . " i ON i.id = c.item_id WHERE i.list_id IN ($lin) AND c.user_id = ? GROUP BY i.list_id");
            $st2->execute([$me]);
            foreach ($st2 as $x) $mineC[(int)$x['list_id']] = (int)$x['n'];
            foreach (db()->query('SELECT list_id, COUNT(*) n, MAX(created_at) last FROM ' . t('comments') . " WHERE list_id IN ($lin) GROUP BY list_id") as $x) $comments[(int)$x['list_id']] = ['n' => (int)$x['n'], 'last' => $x['last']];
        }
        foreach ($rows as $l) {
            $id = (int)$l['id'];
            $friendLists[] = list_out($l) + [
                'owner' => ['id' => (int)$l['owner_id'], 'name' => $l['owner_name']],
                'itemCount' => $count[$id] ?? 0, 'takenCount' => $taken[$id] ?? 0, 'consideringCount' => $consid[$id] ?? 0,
                'myClaims' => $mineC[$id] ?? 0, 'commentCount' => $comments[$id]['n'] ?? 0, 'lastComment' => $comments[$id]['last'] ?? null,
            ];
        }
    }
    return ['user' => public_user(user_row($me)), 'following' => $following, 'followers' => $followers, 'requests' => ['incoming' => $incoming, 'outgoing' => $outgoing], 'myLists' => $myLists, 'friendLists' => $friendLists];
}

/** A friend's list with items, claims and the friends' conversation. */
function friend_view(array $l, int $me): array
{
    $lid = (int)$l['id'];
    $items = items_of([$lid]);
    $claims = [];
    if ($items) {
        $iin = implode(',', array_map(fn($i) => (int)$i['id'], $items));
        foreach (db()->query('SELECT c.item_id, c.user_id, c.status, c.updated_at, u.name FROM ' . t('claims') . ' c JOIN ' . t('users') . " u ON u.id = c.user_id WHERE c.item_id IN ($iin) ORDER BY c.updated_at") as $c) {
            $claims[(int)$c['item_id']][] = ['userId' => (int)$c['user_id'], 'name' => $c['name'], 'status' => $c['status'], 'at' => $c['updated_at']];
        }
    }
    $st = db()->prepare('SELECT c.id, c.item_id, c.user_id, c.body, c.created_at, u.name FROM ' . t('comments') . ' c JOIN ' . t('users') . ' u ON u.id = c.user_id WHERE c.list_id = ? ORDER BY c.created_at, c.id');
    $st->execute([$lid]);
    $comments = array_map(fn($c) => ['id' => (int)$c['id'], 'itemId' => $c['item_id'] !== null ? (int)$c['item_id'] : null, 'userId' => (int)$c['user_id'], 'name' => $c['name'], 'body' => $c['body'], 'at' => $c['created_at']], $st->fetchAll());
    $owner = user_row((int)$l['owner_id']);
    return [
        'list' => list_out($l) + ['owner' => ['id' => (int)$owner['id'], 'name' => $owner['name']]],
        'items' => array_map(fn($i) => item_out($i) + ['claims' => $claims[(int)$i['id']] ?? []], $items),
        'comments' => $comments,
    ];
}

function validate_password(string $pw): void
{
    if (mb_strlen($pw) < 8) fail('The password must be at least 8 characters long.', 422, 'validation');
    if (mb_strlen($pw) > 200) fail('The password is too long.', 422, 'validation');
}

function new_user(string $name, string $email, string $hash, ?string $googleSub, string $username = ''): int
{
    require_once __DIR__ . '/../lib/schema.php';
    if ($username === '' || username_taken($username)) $username = suggest_username_raw(db(), (string)config_value('prefix', 'gw_'), $name, $email);
    db()->prepare('INSERT INTO ' . t('users') . " (name, email, password_hash, google_sub, invite_code, username, lang, status, created_at, last_login_at) VALUES (?,?,?,?,?,?,?, 'active', ?, ?)")
        ->execute([$name, $email, $hash, $googleSub, unique_invite_code(), $username, lang(), now_utc(), now_utc()]);
    return (int)db()->lastInsertId();
}

/** Through someone's invite link you follow each other straight away. */
function apply_invite(int $uid, string $code): void
{
    $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
    if ($code === '') return;
    $st = db()->prepare('SELECT id FROM ' . t('users') . " WHERE invite_code = ? AND status = 'active'");
    $st->execute([$code]);
    $inviter = (int)$st->fetchColumn();
    if ($inviter && $inviter !== $uid) {
        $was = follows($uid, $inviter) && follows($inviter, $uid);
        set_follow($uid, $inviter, 'accepted'); set_follow($inviter, $uid, 'accepted');
        if (!$was) mail_friend_joined(user_row($inviter), user_row($uid));
    }
}

// ---------------------------------------------------------------- routes

switch ($r) {

    // ======================================================== authentication

    case 'auth.config':
        send_json(['ok' => true, 'googleClientId' => (string)config_value('google_client_id', '')]);

    case 'auth.me':
        only('GET');
        $u = current_user();
        if (!$u || $u['status'] !== 'active') send_json(['ok' => true, 'user' => null]);
        send_json(['ok' => true, 'user' => public_user(user_row((int)$u['id'])), 'csrf' => $u['csrf_token']]);

    case 'auth.register':
        only('POST');
        if (too_many_attempts('register', 5, 60)) fail('Too many sign-ups from this network: try again later.', 429, 'rate_limited');
        $b = body();
        $name = s($b, 'name', 80, true, 'Name');
        $email = mb_strtolower(s($b, 'email', 190, true, 'Email'));
        $pw = (string)($b['password'] ?? '');
        if (mb_strlen($name) < 2) fail('Enter your name.', 422, 'validation');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.', 422, 'validation');
        validate_password($pw);
        if (empty($b['acceptPrivacy'])) fail('To create the account you must accept the privacy policy.', 422, 'validation');
        $username = trim((string)($b['username'] ?? '')) !== '' ? valid_username($b['username']) : '';
        if ($username !== '' && username_taken($username)) fail('This username is already taken.', 409, 'username_taken');
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetchColumn()) fail('An account with this email already exists: sign in.', 409, 'email_taken');
        record_attempt('register');
        $uid = new_user($name, $email, password_hash($pw, PASSWORD_DEFAULT), null, $username);
        apply_invite($uid, (string)($b['inviteCode'] ?? ''));
        mail_account_created(user_row($uid), 'email');
        $csrf = create_auth_session($uid);
        send_json(['ok' => true, 'user' => public_user(user_row($uid)), 'csrf' => $csrf]);

    case 'auth.login':
        only('POST');
        if (too_many_attempts('login', 10, 15)) fail('Too many attempts: try again in a few minutes.', 429, 'rate_limited');
        $b = body();
        $email = mb_strtolower(s($b, 'email', 190));
        $pw = (string)($b['password'] ?? '');
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        if (!$row || $row['password_hash'] === '' || !password_verify($pw, $row['password_hash'])) {
            record_attempt('login');
            if ($row && $row['password_hash'] === '' && $row['google_sub']) fail('This account uses Google: sign in with “Continue with Google”.', 401, 'use_google');
            fail('Wrong email or password.', 401, 'bad_credentials');
        }
        if ($row['status'] !== 'active') fail('Your account has been disabled.', 403, 'disabled');
        clear_attempts('login');
        if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $row['id']]);
        apply_invite((int)$row['id'], (string)($b['inviteCode'] ?? ''));
        db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        $csrf = create_auth_session((int)$row['id']);
        send_json(['ok' => true, 'user' => public_user(user_row((int)$row['id'])), 'csrf' => $csrf]);

    case 'auth.google':
        // Sign in (or sign up) with Google. A new account needs the privacy consent: the first call answers
        // "needs_signup" and the app asks for it, then calls again.
        only('POST');
        if (too_many_attempts('login', 20, 15)) fail('Too many attempts: try again in a few minutes.', 429, 'rate_limited');
        $b = body();
        $g = verify_google_token((string)($b['credential'] ?? ''));
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE google_sub = ?');
        $st->execute([$g['sub']]);
        $row = $st->fetch();
        if (!$row) {
            $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
            $st->execute([$g['email']]);
            $row = $st->fetch();
            if ($row) db()->prepare('UPDATE ' . t('users') . ' SET google_sub = ? WHERE id = ?')->execute([$g['sub'], $row['id']]);
        }
        if (!$row) {
            if (empty($b['acceptPrivacy'])) send_json(['ok' => false, 'error' => 'needs_signup', 'message' => tr('Create your Giftwise account'), 'name' => $g['name'], 'email' => $g['email']], 200);
            if (too_many_attempts('register', 5, 60)) fail('Too many sign-ups from this network: try again later.', 429, 'rate_limited');
            record_attempt('register');
            $uid = new_user($g['name'] ?: explode('@', $g['email'])[0], $g['email'], '', $g['sub']);
            $row = user_row($uid);
            mail_account_created($row, 'google');
        }
        if ($row['status'] !== 'active') fail('Your account has been disabled.', 403, 'disabled');
        apply_invite((int)$row['id'], (string)($b['inviteCode'] ?? ''));
        db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        $csrf = create_auth_session((int)$row['id']);
        send_json(['ok' => true, 'user' => public_user(user_row((int)$row['id'])), 'csrf' => $csrf]);

    case 'auth.logout':
        only('POST');
        $u = current_user();
        if ($u) require_csrf($u);
        destroy_auth_session();
        send_json(['ok' => true]);

    case 'auth.profile':
        $u = writer();
        $b = body();
        $name = s($b, 'name', 80, true, 'Name');
        $email = mb_strtolower(s($b, 'email', 190, true, 'Email'));
        if (mb_strlen($name) < 2) fail('Enter your name.', 422, 'validation');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.', 422, 'validation');
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE email = ? AND id <> ?');
        $st->execute([$email, $u['id']]);
        if ($st->fetchColumn()) fail('This email is already used by another account.', 409, 'email_taken');
        db()->prepare('UPDATE ' . t('users') . ' SET name = ?, email = ? WHERE id = ?')->execute([$name, $email, $u['id']]);
        send_json(['ok' => true] + state_payload($u));

    case 'auth.password':
        $u = writer();
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['current'] ?? ''), $row['password_hash'])) fail('The current password is not correct.', 422, 'bad_password');
        $pw = (string)($b['password'] ?? '');
        validate_password($pw);
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
        send_json(['ok' => true]);

    case 'auth.delete':
        $u = writer();
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['password'] ?? ''), $row['password_hash'])) fail('The password is not correct.', 422, 'bad_password');
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]); // lists, items, follows, claims and comments follow (ON DELETE CASCADE)
        set_auth_cookie('', time() - 3600);
        send_json(['ok' => true]);

    // ======================================================== state

    case 'state':
        only('GET');
        $u = require_user();
        if (($u['lang'] ?? '') !== lang()) db()->prepare('UPDATE ' . t('users') . ' SET lang = ? WHERE id = ?')->execute([lang(), $u['id']]); // emails in the user's language
        send_json(['ok' => true] + state_payload($u));

    // ======================================================== friends

    case 'invite.info':
        // Who invited me (only the name), shown before signing up
        only('GET');
        if (too_many_attempts('invite', 60, 10)) fail('Too many attempts: try again in a few minutes.', 429, 'rate_limited');
        record_attempt('invite');
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string)($_GET['code'] ?? '')));
        $st = db()->prepare('SELECT name FROM ' . t('users') . " WHERE invite_code = ? AND status = 'active'");
        $st->execute([$code]);
        $name = $st->fetchColumn();
        if (!$name) fail('This invite link is not valid.', 404, 'bad_code');
        send_json(['ok' => true, 'name' => first_name((string)$name)]);

    case 'invite.accept':
        $u = writer();
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string)(body()['code'] ?? '')));
        $st = db()->prepare('SELECT * FROM ' . t('users') . " WHERE invite_code = ? AND status = 'active'");
        $st->execute([$code]);
        $inviter = $st->fetch();
        if (!$inviter) fail('This invite link is not valid.', 404, 'bad_code');
        if ((int)$inviter['id'] === $u['id']) fail('This is your own invite link: share it with your friends.', 422, 'own_code');
        $iid = (int)$inviter['id'];
        $was = follows($u['id'], $iid) && follows($iid, $u['id']);
        set_follow($u['id'], $iid, 'accepted'); set_follow($iid, $u['id'], 'accepted');
        if (!$was) mail_friend_joined($inviter, user_row($u['id']));
        send_json(['ok' => true, 'friendName' => $inviter['name'], 'already' => $was] + state_payload($u));

    case 'username.check':
        only('GET');
        $u = current_user();
        $name = strtolower(trim(ltrim((string)($_GET['u'] ?? ''), '@')));
        $okFormat = (bool)preg_match('/^[a-z0-9](?:[a-z0-9._]{1,18})[a-z0-9]$/', $name) && strpos($name, '..') === false;
        $available = $okFormat && !username_taken($name, $u ? (int)$u['id'] : 0);
        $suggestion = '';
        if ($okFormat && !$available) { require_once __DIR__ . '/../lib/schema.php'; $suggestion = suggest_username_raw(db(), (string)config_value('prefix', 'gw_'), str_replace(['.', '_'], ' ', $name), ''); }
        send_json(['ok' => true, 'username' => $name, 'valid' => $okFormat, 'available' => $available, 'suggestion' => $suggestion]);

    case 'user.search':
        // By username (beginning of it, "@" optional) or by exact email. Disabled accounts and myself are left out.
        only('GET');
        $u = require_user();
        if (too_many_attempts('search', 120, 10)) fail('Too many requests: try again in a few minutes.', 429, 'rate_limited');
        record_attempt('search');
        $q = trim((string)($_GET['q'] ?? ''));
        $out = [];
        if (filter_var($q, FILTER_VALIDATE_EMAIL)) {
            $st = db()->prepare('SELECT * FROM ' . t('users') . " WHERE email = ? AND status = 'active' AND id <> ?");
            $st->execute([mb_strtolower($q), $u['id']]);
        } else {
            $n = strtolower(ltrim($q, '@'));
            if (!preg_match('/^[a-z0-9._]{2,20}$/', $n)) send_json(['ok' => true, 'results' => []]);
            $st = db()->prepare('SELECT * FROM ' . t('users') . " WHERE username LIKE ? AND status = 'active' AND id <> ? ORDER BY (username = ?) DESC, CHAR_LENGTH(username), username LIMIT 10");
            $st->execute([addcslashes($n, '%_\\') . '%', $u['id'], $n]);
        }
        foreach ($st->fetchAll() as $row) $out[] = person_out($row, $u['id']);
        send_json(['ok' => true, 'results' => $out]);

    case 'user.get':
        only('GET');
        $u = require_user();
        $row = user_row((int)($_GET['id'] ?? 0));
        if (!$row || $row['status'] !== 'active') fail('User not found.', 404, 'not_found');
        send_json(['ok' => true, 'person' => person_out($row, $u['id']) + ['followPolicy' => $row['follow_policy']]]);

    case 'follow':
        $u = writer();
        if (too_many_attempts('follow', 60, 60)) fail('Too many requests: try again later.', 429, 'rate_limited');
        record_attempt('follow');
        $oid = (int)(body()['userId'] ?? 0);
        $other = user_row($oid);
        if (!$other || $other['status'] !== 'active') fail('User not found.', 404, 'not_found');
        if ($oid === $u['id']) fail('You cannot follow yourself.', 422, 'self');
        $f = follow_row($u['id'], $oid);
        if ($f && $f['status'] === 'accepted') fail('You already follow this person.', 409, 'already');
        // Accepted right away when they accept everyone, or when they already follow me
        $auto = $other['follow_policy'] === 'open' || follows($oid, $u['id']);
        set_follow($u['id'], $oid, $auto ? 'accepted' : 'pending');
        if (!$auto && !$f) mail_friend_request($other, user_row($u['id']));
        send_json(['ok' => true, 'result' => $auto ? 'following' : 'requested'] + state_payload($u));

    case 'unfollow':
        // Stop following, or cancel a request sent
        $u = writer();
        drop_follow($u['id'], (int)(body()['userId'] ?? 0));
        send_json(['ok' => true] + state_payload($u));

    case 'follow.accept':
        $u = writer();
        $oid = (int)(body()['userId'] ?? 0);
        $f = follow_row($oid, $u['id']);
        if (!$f || $f['status'] !== 'pending') fail('Request not found.', 404, 'not_found');
        db()->prepare('UPDATE ' . t('follows') . " SET status = 'accepted', accepted_at = ? WHERE follower_id = ? AND followee_id = ?")->execute([now_utc(), $oid, $u['id']]);
        send_json(['ok' => true] + state_payload($u));

    case 'follow.decline':
    case 'follower.remove':
        // Refuse a request, or remove someone from my followers (they no longer see my lists)
        $u = writer();
        drop_follow((int)(body()['userId'] ?? 0), $u['id']);
        send_json(['ok' => true] + state_payload($u));

    case 'account.username':
        $u = writer();
        $name = valid_username(body()['username'] ?? '');
        if (username_taken($name, $u['id'])) fail('This username is already taken.', 409, 'username_taken');
        db()->prepare('UPDATE ' . t('users') . ' SET username = ? WHERE id = ?')->execute([$name, $u['id']]);
        send_json(['ok' => true] + state_payload($u));

    case 'account.privacy':
        $u = writer();
        $policy = (body()['followPolicy'] ?? '') === 'open' ? 'open' : 'approve';
        db()->prepare('UPDATE ' . t('users') . ' SET follow_policy = ? WHERE id = ?')->execute([$policy, $u['id']]);
        if ($policy === 'open') db()->prepare('UPDATE ' . t('follows') . " SET status = 'accepted', accepted_at = ? WHERE followee_id = ? AND status = 'pending'")->execute([now_utc(), $u['id']]);
        send_json(['ok' => true] + state_payload($u));

    // ======================================================== my lists

    case 'list.save':
        $u = writer();
        $b = body();
        $id = (int)($b['id'] ?? 0);
        $title = s($b, 'title', 80, true, 'Title');
        $occasion = in_array($b['occasion'] ?? '', OCCASIONS, true) ? $b['occasion'] : 'other';
        $date = date_or_null($b['eventDate'] ?? null);
        $desc = s($b, 'description', 500, false, 'Description');
        $vis = ($b['visibility'] ?? '') === 'selected' ? 'selected' : 'friends';
        $fr = follower_ids($u['id']);
        $viewers = array_values(array_unique(array_filter(array_map('intval', is_array($b['viewers'] ?? null) ? $b['viewers'] : []), fn($v) => in_array($v, $fr, true))));
        if ($id) {
            $prev = own_list($id, $u['id']);
            $prevViewers = db()->query('SELECT user_id FROM ' . t('list_viewers') . ' WHERE list_id = ' . $id)->fetchAll(PDO::FETCH_COLUMN);
            $prev['viewers'] = array_map('intval', $prevViewers);
            db()->prepare('UPDATE ' . t('lists') . ' SET title = ?, occasion = ?, event_date = ?, description = ?, visibility = ?, updated_at = ? WHERE id = ?')
                ->execute([$title, $occasion, $date, $desc, $vis, now_utc(), $id]);
            log_history($u['id'], 'list_edit', $id, $title, null, '', ['prev_snapshot' => $prev]);
        } else {
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('lists') . ' WHERE owner_id = ?');
            $st->execute([$u['id']]);
            if ((int)$st->fetchColumn() >= 100) fail('You reached the maximum number of lists (100).', 422, 'limit');
            db()->prepare('INSERT INTO ' . t('lists') . ' (owner_id, title, occasion, event_date, description, visibility, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$u['id'], $title, $occasion, $date, $desc, $vis, now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
            log_history($u['id'], 'list_create', $id, $title, null, '', []);
        }
        db()->prepare('DELETE FROM ' . t('list_viewers') . ' WHERE list_id = ?')->execute([$id]);
        if ($vis === 'selected') {
            $ins = db()->prepare('INSERT IGNORE INTO ' . t('list_viewers') . ' (list_id, user_id) VALUES (?,?)');
            foreach ($viewers as $v) $ins->execute([$id, $v]);
        }
        send_json(['ok' => true, 'id' => $id] + state_payload($u));

    case 'list.archive':
        $u = writer();
        $b = body();
        $l = own_list((int)($b['id'] ?? 0), $u['id']);
        $archived = !empty($b['archived']) ? 1 : 0;
        db()->prepare('UPDATE ' . t('lists') . ' SET archived = ?, updated_at = ? WHERE id = ?')->execute([$archived, now_utc(), (int)$b['id']]);
        log_history($u['id'], 'list_archive', (int)$l['id'], (string)$l['title'], null, '', ['archived' => $archived]);
        send_json(['ok' => true] + state_payload($u));

    case 'list.delete':
        $u = writer();
        $id = (int)(body()['id'] ?? 0);
        $l = own_list($id, $u['id']);
        $items = db()->query('SELECT * FROM ' . t('items') . ' WHERE list_id = ' . $id)->fetchAll();
        $viewers = db()->query('SELECT user_id FROM ' . t('list_viewers') . ' WHERE list_id = ' . $id)->fetchAll(PDO::FETCH_COLUMN);
        $l['items'] = $items;
        $l['viewers'] = array_map('intval', $viewers);
        db()->prepare('DELETE FROM ' . t('lists') . ' WHERE id = ?')->execute([$id]);
        log_history($u['id'], 'list_delete', $id, (string)$l['title'], null, '', ['list_snapshot' => $l]);
        send_json(['ok' => true] + state_payload($u));

    case 'item.save':
        $u = writer();
        $b = body();
        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $st = db()->prepare('SELECT * FROM ' . t('items') . ' WHERE id = ?');
            $st->execute([$id]);
            $prev = $st->fetch();
            if (!$prev) fail('Wish not found.', 404, 'not_found');
            $lid = (int)$prev['list_id'];
        } else {
            $prev = null;
            $lid = (int)($b['listId'] ?? 0);
        }
        $ownL = own_list($lid, $u['id']);
        $newList = (int)($b['listId'] ?? $lid);
        if ($newList !== $lid) own_list($newList, $u['id']); // moved to another of my lists
        $title = s($b, 'title', 160, true, 'Title');
        $url = url_or_empty($b['url'] ?? '');
        $img = url_or_empty($b['image'] ?? '');
        $price = isset($b['price']) && $b['price'] !== '' && $b['price'] !== null ? round((float)str_replace(',', '.', (string)$b['price']), 2) : null;
        if ($price !== null && ($price < 0 || $price > 99999999)) fail('Invalid price.', 422, 'validation');
        $cur = in_array($b['currency'] ?? '', CURRENCIES, true) ? $b['currency'] : 'EUR';
        $notes = s($b, 'notes', 1000, false, 'Notes');
        $prio = max(0, min(2, (int)($b['priority'] ?? 0)));
        $qty = max(1, min(99, (int)($b['quantity'] ?? 1)));
        if ($id) {
            db()->prepare('UPDATE ' . t('items') . ' SET list_id = ?, title = ?, url = ?, image_url = ?, price = ?, currency = ?, notes = ?, priority = ?, quantity = ?, updated_at = ? WHERE id = ?')
                ->execute([$newList, $title, $url, $img, $price, $cur, $notes, $prio, $qty, now_utc(), $id]);
            log_history($u['id'], 'wish_edit', $newList, (string)$ownL['title'], $id, $title, ['prev_snapshot' => $prev]);
        } else {
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('items') . ' WHERE list_id = ?');
            $st->execute([$lid]);
            if ((int)$st->fetchColumn() >= 300) fail('This list is full (300 wishes).', 422, 'limit');
            db()->prepare('INSERT INTO ' . t('items') . ' (list_id, title, url, image_url, price, currency, notes, priority, quantity, sort, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$lid, $title, $url, $img, $price, $cur, $notes, $prio, $qty, time(), now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
            log_history($u['id'], 'wish_add', $lid, (string)$ownL['title'], $id, $title, ['item_snapshot' => ['list_id' => $lid, 'title' => $title, 'url' => $url, 'image_url' => $img, 'price' => $price, 'currency' => $cur, 'notes' => $notes, 'priority' => $prio, 'quantity' => $qty]]);
        }
        db()->prepare('UPDATE ' . t('lists') . ' SET updated_at = ? WHERE id IN (?, ?)')->execute([now_utc(), $lid, $newList]);
        send_json(['ok' => true, 'id' => $id] + state_payload($u));

    case 'item.delete':
        $u = writer();
        $id = (int)(body()['id'] ?? 0);
        $st = db()->prepare('SELECT i.*, l.title AS list_title FROM ' . t('items') . ' i JOIN ' . t('lists') . ' l ON l.id = i.list_id WHERE i.id = ?');
        $st->execute([$id]);
        $it = $st->fetch();
        if (!$it) fail('Wish not found.', 404, 'not_found');
        own_list((int)$it['list_id'], $u['id']);
        db()->prepare('DELETE FROM ' . t('items') . ' WHERE id = ?')->execute([$id]);
        log_history($u['id'], 'wish_delete', (int)$it['list_id'], (string)$it['list_title'], $id, (string)$it['title'], ['item_snapshot' => $it]);
        send_json(['ok' => true] + state_payload($u));

    case 'link.preview':
        $u = writer();
        if (too_many_attempts('preview', 40, 10)) fail('Too many requests: try again in a few minutes.', 429, 'rate_limited');
        record_attempt('preview');
        $url = url_or_empty(body()['url'] ?? '');
        if ($url === '') fail('Paste a link first.', 422, 'validation');
        require_once __DIR__ . '/../lib/preview.php';
        send_json(['ok' => true, 'preview' => preview_link($url)]);

    // ======================================================== friends' lists (the secret part)

    case 'list.view':
        only('GET');
        $u = require_user();
        $l = friend_list((int)($_GET['id'] ?? 0), $u['id']);
        send_json(['ok' => true] + friend_view($l, $u['id']));

    case 'claim.set':
        $u = writer();
        $b = body();
        $iid = (int)($b['itemId'] ?? 0);
        $st = db()->prepare('SELECT list_id FROM ' . t('items') . ' WHERE id = ?');
        $st->execute([$iid]);
        $lid = (int)$st->fetchColumn();
        if (!$lid) fail('This wish has been removed from the list.', 404, 'not_found');
        $l = friend_list($lid, $u['id']);
        $prevClaim = (string)(db()->query('SELECT status FROM ' . t('claims') . ' WHERE item_id = ' . $iid . ' AND user_id = ' . $u['id'])->fetchColumn() ?: '');
        $itTitle = (string)(db()->query('SELECT title FROM ' . t('items') . ' WHERE id = ' . $iid)->fetchColumn() ?: '');
        $status = (string)($b['status'] ?? '');
        if (in_array($status, CLAIM_STATES, true)) {
            db()->prepare('INSERT INTO ' . t('claims') . ' (item_id, user_id, status, created_at, updated_at) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = VALUES(updated_at)')
                ->execute([$iid, $u['id'], $status, now_utc(), now_utc()]);
        } else {
            db()->prepare('DELETE FROM ' . t('claims') . ' WHERE item_id = ? AND user_id = ?')->execute([$iid, $u['id']]);
        }
        log_history($u['id'], 'claim_set', $lid, (string)$l['title'], $iid, $itTitle, ['prev_status' => $prevClaim, 'new_status' => $status]);
        send_json(['ok' => true] + friend_view($l, $u['id']));

    case 'comment.add':
        $u = writer();
        $b = body();
        $l = friend_list((int)($b['listId'] ?? 0), $u['id']);
        $iid = int_or_null($b['itemId'] ?? null);
        if ($iid !== null) {
            $st = db()->prepare('SELECT 1 FROM ' . t('items') . ' WHERE id = ? AND list_id = ?');
            $st->execute([$iid, $l['id']]);
            if (!$st->fetchColumn()) fail('This wish has been removed from the list.', 404, 'not_found');
        }
        $text = s($b, 'body', 1000, true, 'Message');
        if (too_many_attempts('comment', 60, 5)) fail('Too many messages: wait a moment.', 429, 'rate_limited');
        record_attempt('comment');
        db()->prepare('INSERT INTO ' . t('comments') . ' (list_id, item_id, user_id, body, created_at) VALUES (?,?,?,?,?)')->execute([$l['id'], $iid, $u['id'], $text, now_utc()]);
        send_json(['ok' => true] + friend_view($l, $u['id']));

    case 'comment.delete':
        $u = writer();
        $id = (int)(body()['id'] ?? 0);
        $st = db()->prepare('SELECT list_id FROM ' . t('comments') . ' WHERE id = ? AND user_id = ?');
        $st->execute([$id, $u['id']]);
        $lid = (int)$st->fetchColumn();
        if (!$lid) fail('Message not found.', 404, 'not_found');
        db()->prepare('DELETE FROM ' . t('comments') . ' WHERE id = ?')->execute([$id]);
        $l = list_row($lid);
        send_json(['ok' => true] + (can_view($l, $u['id']) && (int)$l['owner_id'] !== $u['id'] ? friend_view($l, $u['id']) : []));

    // ======================================================== activity history & undo

    case 'history.list':
        only('GET');
        $u = require_user();
        try {
            $st = db()->prepare('SELECT id, user_id, action, list_id, list_title, item_id, item_title, details, undone, created_at FROM ' . t('history') . ' WHERE user_id = ? ORDER BY id DESC LIMIT 100');
            $st->execute([$u['id']]);
            $rows = array_map(function ($r) {
                return [
                    'id' => (int)$r['id'],
                    'userId' => (int)$r['user_id'],
                    'action' => $r['action'],
                    'listId' => $r['list_id'] ? (int)$r['list_id'] : null,
                    'listTitle' => $r['list_title'],
                    'itemId' => $r['item_id'] ? (int)$r['item_id'] : null,
                    'itemTitle' => $r['item_title'],
                    'details' => json_decode((string)$r['details'], true) ?: [],
                    'undone' => (bool)$r['undone'],
                    'createdAt' => $r['created_at'],
                ];
            }, $st->fetchAll());
        } catch (Throwable $e) {
            try {
                require_once __DIR__ . '/../lib/schema.php';
                install_schema(db(), (string)config_value('prefix', 'gw_'));
            } catch (Throwable $ignored) {}
            $rows = [];
        }
        send_json(['ok' => true, 'history' => $rows]);

    case 'history.undo':
        $u = writer();
        $id = (int)(body()['id'] ?? 0);
        $st = db()->prepare('SELECT * FROM ' . t('history') . ' WHERE id = ? AND user_id = ?');
        $st->execute([$id, $u['id']]);
        $h = $st->fetch();
        if (!$h) fail('History entry not found.', 404);
        if ($h['undone']) fail('Action already undone.', 400);

        $act = $h['action'];
        $details = json_decode((string)$h['details'], true) ?: [];

        db()->beginTransaction();
        if ($act === 'wish_add') {
            $itemId = (int)$h['item_id'];
            if ($itemId) {
                $st = db()->prepare('SELECT i.id FROM ' . t('items') . ' i JOIN ' . t('lists') . ' l ON l.id = i.list_id WHERE i.id = ? AND l.owner_id = ?');
                $st->execute([$itemId, $u['id']]);
                if ($st->fetchColumn()) {
                    db()->prepare('DELETE FROM ' . t('items') . ' WHERE id = ?')->execute([$itemId]);
                }
            }
        } elseif ($act === 'wish_delete') {
            $snap = $details['item_snapshot'] ?? [];
            if ($snap) {
                $lid = (int)($snap['list_id'] ?? 0);
                $st = db()->prepare('SELECT id FROM ' . t('lists') . ' WHERE id = ? AND owner_id = ?');
                $st->execute([$lid, $u['id']]);
                if (!$st->fetchColumn()) {
                    $lid = (int)db()->query('SELECT id FROM ' . t('lists') . ' WHERE owner_id = ' . $u['id'] . ' ORDER BY id DESC LIMIT 1')->fetchColumn();
                }
                if ($lid) {
                    db()->prepare('INSERT INTO ' . t('items') . ' (list_id, title, url, image_url, price, currency, notes, priority, quantity, sort, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                        ->execute([$lid, $snap['title'], $snap['url'] ?? '', $snap['image_url'] ?? '', $snap['price'] !== null ? $snap['price'] : null, $snap['currency'] ?? 'EUR', $snap['notes'] ?? '', (int)($snap['priority'] ?? 0), (int)($snap['quantity'] ?? 1), time(), now_utc(), now_utc()]);
                }
            }
        } elseif ($act === 'wish_edit') {
            $prev = $details['prev_snapshot'] ?? [];
            $itemId = (int)$h['item_id'];
            if ($prev && $itemId) {
                $st = db()->prepare('SELECT i.id FROM ' . t('items') . ' i JOIN ' . t('lists') . ' l ON l.id = i.list_id WHERE i.id = ? AND l.owner_id = ?');
                $st->execute([$itemId, $u['id']]);
                if ($st->fetchColumn()) {
                    $prevListId = (int)$prev['list_id'];
                    $stList = db()->prepare('SELECT id FROM ' . t('lists') . ' WHERE id = ? AND owner_id = ?');
                    $stList->execute([$prevListId, $u['id']]);
                    if (!$stList->fetchColumn()) $prevListId = (int)$h['list_id'];
                    db()->prepare('UPDATE ' . t('items') . ' SET list_id = ?, title = ?, url = ?, image_url = ?, price = ?, currency = ?, notes = ?, priority = ?, quantity = ?, updated_at = ? WHERE id = ?')
                        ->execute([$prevListId, $prev['title'], $prev['url'] ?? '', $prev['image_url'] ?? '', $prev['price'] !== null ? $prev['price'] : null, $prev['currency'] ?? 'EUR', $prev['notes'] ?? '', (int)($prev['priority'] ?? 0), (int)($prev['quantity'] ?? 1), now_utc(), $itemId]);
                }
            }
        } elseif ($act === 'list_create') {
            $listId = (int)$h['list_id'];
            if ($listId) {
                db()->prepare('DELETE FROM ' . t('lists') . ' WHERE id = ? AND owner_id = ?')->execute([$listId, $u['id']]);
            }
        } elseif ($act === 'list_edit') {
            $prev = $details['prev_snapshot'] ?? [];
            $listId = (int)$h['list_id'];
            if ($prev && $listId) {
                $st = db()->prepare('SELECT id FROM ' . t('lists') . ' WHERE id = ? AND owner_id = ?');
                $st->execute([$listId, $u['id']]);
                if ($st->fetchColumn()) {
                    db()->prepare('UPDATE ' . t('lists') . ' SET title = ?, occasion = ?, event_date = ?, description = ?, visibility = ?, updated_at = ? WHERE id = ?')
                        ->execute([$prev['title'], $prev['occasion'] ?? 'other', $prev['event_date'] ?? null, $prev['description'] ?? '', $prev['visibility'] ?? 'friends', now_utc(), $listId]);
                    db()->prepare('DELETE FROM ' . t('list_viewers') . ' WHERE list_id = ?')->execute([$listId]);
                    if (($prev['visibility'] ?? '') === 'selected' && !empty($prev['viewers'])) {
                        $ins = db()->prepare('INSERT IGNORE INTO ' . t('list_viewers') . ' (list_id, user_id) VALUES (?,?)');
                        foreach ($prev['viewers'] as $v) $ins->execute([$listId, (int)$v]);
                    }
                }
            }
        } elseif ($act === 'list_archive') {
            $listId = (int)$h['list_id'];
            $wasArchived = !empty($details['archived']);
            db()->prepare('UPDATE ' . t('lists') . ' SET archived = ?, updated_at = ? WHERE id = ? AND owner_id = ?')
                ->execute([$wasArchived ? 0 : 1, now_utc(), $listId, $u['id']]);
        } elseif ($act === 'list_delete') {
            $snap = $details['list_snapshot'] ?? [];
            if ($snap) {
                db()->prepare('INSERT INTO ' . t('lists') . ' (owner_id, title, occasion, event_date, description, visibility, archived, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?)')
                    ->execute([$u['id'], $snap['title'], $snap['occasion'] ?? 'other', $snap['event_date'] ?? null, $snap['description'] ?? '', $snap['visibility'] ?? 'friends', (int)($snap['archived'] ?? 0), now_utc(), now_utc()]);
                $newListId = (int)db()->lastInsertId();
                if (($snap['visibility'] ?? '') === 'selected' && !empty($snap['viewers'])) {
                    $insV = db()->prepare('INSERT IGNORE INTO ' . t('list_viewers') . ' (list_id, user_id) VALUES (?,?)');
                    foreach ($snap['viewers'] as $v) $insV->execute([$newListId, (int)$v]);
                }
                if (!empty($snap['items'])) {
                    $insI = db()->prepare('INSERT INTO ' . t('items') . ' (list_id, title, url, image_url, price, currency, notes, priority, quantity, sort, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
                    foreach ($snap['items'] as $it) {
                        $insI->execute([$newListId, $it['title'], $it['url'] ?? '', $it['image_url'] ?? '', $it['price'] !== null ? $it['price'] : null, $it['currency'] ?? 'EUR', $it['notes'] ?? '', (int)($it['priority'] ?? 0), (int)($it['quantity'] ?? 1), (int)($it['sort'] ?? time()), now_utc(), now_utc()]);
                    }
                }
            }
        } elseif ($act === 'claim_set') {
            $prevStatus = (string)($details['prev_status'] ?? '');
            $itemId = (int)$h['item_id'];
            if ($itemId) {
                if (in_array($prevStatus, CLAIM_STATES, true)) {
                    db()->prepare('INSERT INTO ' . t('claims') . ' (item_id, user_id, status, created_at, updated_at) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = VALUES(updated_at)')
                        ->execute([$itemId, $u['id'], $prevStatus, now_utc(), now_utc()]);
                } else {
                    db()->prepare('DELETE FROM ' . t('claims') . ' WHERE item_id = ? AND user_id = ?')->execute([$itemId, $u['id']]);
                }
            }
        }

        db()->prepare('UPDATE ' . t('history') . ' SET undone = 1 WHERE id = ?')->execute([$id]);
        db()->commit();

        send_json(['ok' => true] + state_payload($u));

    default:
        fail('Unknown action.', 404, 'unknown_route');
}
