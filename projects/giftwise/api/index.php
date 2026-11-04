<?php
/**
 * Giftwise — JSON API.  api/index.php?r=<action>
 *
 * Public:   auth.config, auth.me, auth.register, auth.login, auth.google, auth.logout, invite.info
 * Account:  state, auth.profile, auth.password, auth.delete
 * Friends:  friend.request, friend.accept, friend.decline, friend.remove, invite.accept
 * Lists:    list.save, list.delete, list.archive, item.save, item.delete, link.preview
 * Friends' lists (secret coordination): list.view, claim.set, comment.add, comment.delete
 *
 * THE RULE: claims and comments are about a list the user does NOT own. The owner never receives them:
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

function writer(): array
{
    only('POST');
    $u = require_user();
    require_csrf($u);
    return $u;
}

// ---------------------------------------------------------------- helpers

function pair(int $x, int $y): array { return $x < $y ? [$x, $y] : [$y, $x]; }

function user_row(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function friendship(int $me, int $other): ?array
{
    [$a, $b] = pair($me, $other);
    $st = db()->prepare('SELECT * FROM ' . t('friendships') . ' WHERE user_a = ? AND user_b = ?');
    $st->execute([$a, $b]);
    return $st->fetch() ?: null;
}

function are_friends(int $me, int $other): bool
{
    $f = friendship($me, $other);
    return $f && $f['status'] === 'accepted';
}

function make_friends(int $x, int $y, bool $accepted, int $requestedBy): void
{
    if ($x === $y) return;
    [$a, $b] = pair($x, $y);
    db()->prepare('INSERT INTO ' . t('friendships') . ' (user_a, user_b, status, requested_by, created_at, accepted_at) VALUES (?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE status = IF(VALUES(status) = \'accepted\', \'accepted\', status), accepted_at = IF(VALUES(status) = \'accepted\', VALUES(accepted_at), accepted_at)')
        ->execute([$a, $b, $accepted ? 'accepted' : 'pending', $requestedBy, now_utc(), $accepted ? now_utc() : null]);
}

function friend_ids(int $me): array
{
    $st = db()->prepare('SELECT IF(user_a = ?, user_b, user_a) AS id FROM ' . t('friendships') . " WHERE (user_a = ? OR user_b = ?) AND status = 'accepted'");
    $st->execute([$me, $me, $me]);
    return array_map('intval', array_column($st->fetchAll(), 'id'));
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

/** A friend's list that $me can see — never the owner (the owner must not see the coordination). */
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
    if (!empty($l['archived']) || !are_friends($me, (int)$l['owner_id'])) return false;
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
    $friends = [];
    $incoming = [];
    $outgoing = [];
    $st = db()->prepare('SELECT f.*, u.id AS uid, u.name, u.email FROM ' . t('friendships') . ' f JOIN ' . t('users') . ' u ON u.id = IF(f.user_a = ?, f.user_b, f.user_a)
        WHERE (f.user_a = ? OR f.user_b = ?) AND u.status = \'active\' ORDER BY u.name');
    $st->execute([$me, $me, $me]);
    foreach ($st->fetchAll() as $f) {
        $p = ['id' => (int)$f['uid'], 'name' => $f['name'], 'email' => $f['email']];
        if ($f['status'] === 'accepted') $friends[] = $p + ['since' => $f['accepted_at']];
        elseif ((int)$f['requested_by'] === $me) $outgoing[] = $p + ['at' => $f['created_at']];
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
    $friendLists = [];
    $fids = array_map(fn($f) => $f['id'], $friends);
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
    return ['user' => public_user(user_row($me)), 'friends' => $friends, 'requests' => ['incoming' => $incoming, 'outgoing' => $outgoing], 'myLists' => $myLists, 'friendLists' => $friendLists];
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

function new_user(string $name, string $email, string $hash, ?string $googleSub): int
{
    db()->prepare('INSERT INTO ' . t('users') . " (name, email, password_hash, google_sub, invite_code, lang, status, created_at, last_login_at) VALUES (?,?,?,?,?,?, 'active', ?, ?)")
        ->execute([$name, $email, $hash, $googleSub, unique_invite_code(), lang(), now_utc(), now_utc()]);
    return (int)db()->lastInsertId();
}

/** Sign-up through someone's invite link: you become friends straight away. */
function apply_invite(int $uid, string $code): void
{
    $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
    if ($code === '') return;
    $st = db()->prepare('SELECT id FROM ' . t('users') . " WHERE invite_code = ? AND status = 'active'");
    $st->execute([$code]);
    $inviter = (int)$st->fetchColumn();
    if ($inviter && $inviter !== $uid) {
        make_friends($uid, $inviter, true, $inviter);
        mail_friend_joined(user_row($inviter), user_row($uid));
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
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetchColumn()) fail('An account with this email already exists: sign in.', 409, 'email_taken');
        record_attempt('register');
        $uid = new_user($name, $email, password_hash($pw, PASSWORD_DEFAULT), null);
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
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]); // lists, items, friendships, claims and comments follow (ON DELETE CASCADE)
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
        $was = are_friends($u['id'], (int)$inviter['id']);
        make_friends($u['id'], (int)$inviter['id'], true, (int)$inviter['id']);
        if (!$was) mail_friend_joined($inviter, user_row($u['id']));
        send_json(['ok' => true, 'friendName' => $inviter['name'], 'already' => $was] + state_payload($u));

    case 'friend.request':
        $u = writer();
        if (too_many_attempts('friendreq', 30, 60)) fail('Too many requests: try again later.', 429, 'rate_limited');
        record_attempt('friendreq');
        $email = mb_strtolower(s(body(), 'email', 190, true, 'Email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.', 422, 'validation');
        $st = db()->prepare('SELECT * FROM ' . t('users') . " WHERE email = ? AND status = 'active'");
        $st->execute([$email]);
        $other = $st->fetch();
        if (!$other) fail('Nobody uses Giftwise with this email yet: send them your invite link.', 404, 'no_user');
        $oid = (int)$other['id'];
        if ($oid === $u['id']) fail('That is your own email.', 422, 'self');
        $f = friendship($u['id'], $oid);
        if ($f && $f['status'] === 'accepted') fail('You are already friends.', 409, 'already');
        if ($f && (int)$f['requested_by'] === $oid) {
            make_friends($u['id'], $oid, true, $oid); // they had already asked: now you are friends
            send_json(['ok' => true, 'result' => 'accepted'] + state_payload($u));
        }
        if (!$f) {
            make_friends($u['id'], $oid, false, $u['id']);
            mail_friend_request($other, user_row($u['id']));
        }
        send_json(['ok' => true, 'result' => 'sent'] + state_payload($u));

    case 'friend.accept':
        $u = writer();
        $oid = (int)(body()['userId'] ?? 0);
        $f = friendship($u['id'], $oid);
        if (!$f || $f['status'] !== 'pending' || (int)$f['requested_by'] === $u['id']) fail('Request not found.', 404, 'not_found');
        make_friends($u['id'], $oid, true, $oid);
        send_json(['ok' => true] + state_payload($u));

    case 'friend.decline':
    case 'friend.remove':
        // Decline an incoming request, cancel a sent one or remove a friend
        $u = writer();
        $oid = (int)(body()['userId'] ?? 0);
        [$a, $c] = pair($u['id'], $oid);
        db()->prepare('DELETE FROM ' . t('friendships') . ' WHERE user_a = ? AND user_b = ?')->execute([$a, $c]);
        if ($r === 'friend.remove') {
            // They no longer see each other's lists: also drop the specific sharing
            db()->prepare('DELETE v FROM ' . t('list_viewers') . ' v JOIN ' . t('lists') . ' l ON l.id = v.list_id WHERE (l.owner_id = ? AND v.user_id = ?) OR (l.owner_id = ? AND v.user_id = ?)')->execute([$u['id'], $oid, $oid, $u['id']]);
        }
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
        $fr = friend_ids($u['id']);
        $viewers = array_values(array_unique(array_filter(array_map('intval', is_array($b['viewers'] ?? null) ? $b['viewers'] : []), fn($v) => in_array($v, $fr, true))));
        if ($id) {
            own_list($id, $u['id']);
            db()->prepare('UPDATE ' . t('lists') . ' SET title = ?, occasion = ?, event_date = ?, description = ?, visibility = ?, updated_at = ? WHERE id = ?')
                ->execute([$title, $occasion, $date, $desc, $vis, now_utc(), $id]);
        } else {
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('lists') . ' WHERE owner_id = ?');
            $st->execute([$u['id']]);
            if ((int)$st->fetchColumn() >= 100) fail('You reached the maximum number of lists (100).', 422, 'limit');
            db()->prepare('INSERT INTO ' . t('lists') . ' (owner_id, title, occasion, event_date, description, visibility, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$u['id'], $title, $occasion, $date, $desc, $vis, now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
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
        own_list((int)($b['id'] ?? 0), $u['id']);
        db()->prepare('UPDATE ' . t('lists') . ' SET archived = ?, updated_at = ? WHERE id = ?')->execute([!empty($b['archived']) ? 1 : 0, now_utc(), (int)$b['id']]);
        send_json(['ok' => true] + state_payload($u));

    case 'list.delete':
        $u = writer();
        $id = (int)(body()['id'] ?? 0);
        own_list($id, $u['id']);
        db()->prepare('DELETE FROM ' . t('lists') . ' WHERE id = ?')->execute([$id]);
        send_json(['ok' => true] + state_payload($u));

    case 'item.save':
        $u = writer();
        $b = body();
        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $st = db()->prepare('SELECT list_id FROM ' . t('items') . ' WHERE id = ?');
            $st->execute([$id]);
            $lid = (int)$st->fetchColumn();
            if (!$lid) fail('Wish not found.', 404, 'not_found');
        } else {
            $lid = (int)($b['listId'] ?? 0);
        }
        own_list($lid, $u['id']);
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
        } else {
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('items') . ' WHERE list_id = ?');
            $st->execute([$lid]);
            if ((int)$st->fetchColumn() >= 300) fail('This list is full (300 wishes).', 422, 'limit');
            db()->prepare('INSERT INTO ' . t('items') . ' (list_id, title, url, image_url, price, currency, notes, priority, quantity, sort, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$lid, $title, $url, $img, $price, $cur, $notes, $prio, $qty, time(), now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
        }
        db()->prepare('UPDATE ' . t('lists') . ' SET updated_at = ? WHERE id IN (?, ?)')->execute([now_utc(), $lid, $newList]);
        send_json(['ok' => true, 'id' => $id] + state_payload($u));

    case 'item.delete':
        $u = writer();
        $id = (int)(body()['id'] ?? 0);
        $st = db()->prepare('SELECT list_id FROM ' . t('items') . ' WHERE id = ?');
        $st->execute([$id]);
        $lid = (int)$st->fetchColumn();
        if (!$lid) fail('Wish not found.', 404, 'not_found');
        own_list($lid, $u['id']);
        db()->prepare('DELETE FROM ' . t('items') . ' WHERE id = ?')->execute([$id]);
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
        $status = (string)($b['status'] ?? '');
        if (in_array($status, CLAIM_STATES, true)) {
            db()->prepare('INSERT INTO ' . t('claims') . ' (item_id, user_id, status, created_at, updated_at) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = VALUES(updated_at)')
                ->execute([$iid, $u['id'], $status, now_utc(), now_utc()]);
        } else {
            db()->prepare('DELETE FROM ' . t('claims') . ' WHERE item_id = ? AND user_id = ?')->execute([$iid, $u['id']]);
        }
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

    default:
        fail('Unknown action.', 404, 'unknown_route');
}
