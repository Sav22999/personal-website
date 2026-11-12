<?php
/**
 * Forkville — JSON API.  api/index.php?r=<action>
 *
 * Public:   auth.config, auth.me, auth.register, auth.login, auth.google, auth.logout, auth.verify
 * Before the email is confirmed only auth.resend and auth.delete work (see must_verify in lib/bootstrap.php).
 * Account:  auth.password, auth.delete
 * Game:     game.kind, game.ledger, game.style, game.candidates, game.assign, game.promote, game.status, game.reviews, game.catalog, game.state, game.report, game.layout, game.hire, game.fire, game.expand, game.rename,
 *           game.learn, game.upgrade, game.menu, game.plant, game.harvest, game.market, game.wish
 * Social:   social.friends, social.search, social.request, social.accept, social.decline, social.remove,
 *           social.visit, social.rate, social.gift, social.inbox, social.claim, social.board
 *
 * Admin:    admin.* (see lib/admin-api.php), only for accounts with role ≥ ROLE_ADMIN.
 *
 * Every POST of a signed-in user needs the X-CSRF-Token header (given at sign-in).
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/economy.php';
require __DIR__ . '/../lib/social.php';

header('X-Robots-Tag: noindex');
set_exception_handler(function (Throwable $e) {
    if ($e->getMessage() === 'not_installed') fail('not_installed', 503);
    if (db_in_transaction()) db()->rollBack();
    error_log('Forkville API: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    send_json(['ok' => false, 'error' => 'server', 'detail' => config_value('debug', false) ? $e->getMessage() : null], 500);
});

$r = (string)($_GET['r'] ?? '');

if ($r === 'auth.config' && !credentials()) send_json(['ok' => true, 'installed' => false, 'googleClientId' => '']);
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

/** Runs a change on the player's restaurant inside a transaction, then answers with the new state. */
/** The copy of the game (browser tab or device) sending this request: the app makes up an id each time it opens. */
function client_instance(): string
{
    $i = (string)($_SERVER['HTTP_X_INSTANCE'] ?? '');
    return preg_match('/^[a-z0-9]{8,24}$/', $i) ? $i : '';
}

function mutate(array $u, callable $fn): void
{
    global $r;
    db()->beginTransaction();
    $rest = load_restaurant((int)$u['id'], true);
    if (!$rest) {
        db()->rollBack();
        fail('no_restaurant', 409);
    }
    // Only one copy of the game plays at a time: the last one opened. An older tab gets told to step aside.
    $inst = client_instance();
    if ($inst !== '' && !empty($rest['instance']) && $rest['instance'] !== $inst) {
        db()->rollBack();
        fail('other_instance', 409);
    }
    $before = $rest['coins'];
    $extra = $fn($rest) ?: [];
    // Whatever changed the coins and was not written yet goes to the statement under the action's name
    $delta = $rest['coins'] - $before - ledger_recorded();
    if ($delta !== 0) ledger((int)$u['id'], substr((string)$r, 5), $delta, ledger_detail((string)$r, $extra, body()));
    save_restaurant($rest);
    db()->commit();
    send_json(game_state((int)$u['id']) + ['result' => $extra]);
}

/** A short detail for the statement: what was bought, for whom. */
function ledger_detail(string $route, $extra, array $b): string
{
    switch ($route) {
        case 'game.layout': return (string)(($extra['spent'] ?? 0) . '|' . ($extra['refund'] ?? 0));
        case 'game.hire': return (string)($extra['hired']['name'] ?? '') . '|' . (string)($b['role'] ?? '');
        case 'game.market': return (string)($b['ing'] ?? '') . '|' . (int)($b['qty'] ?? 1);
        case 'game.plant': return (string)($b['crop'] ?? '');
        case 'game.expand': return (string)($extra['size'] ?? '');
        case 'game.parking': return (string)($extra['parking'] ?? '');
        case 'game.floor': return (string)($extra['floor'] ?? '');
        case 'game.door': return (string)($extra['style'] ?? '');
        case 'game.promote': return (string)($extra['rank'] ?? '');
        case 'game.kind': return (string)($extra['kind'] ?? '');
        default: return '';
    }
}

/** Fails when there is no room for one more worker in this role. */
function role_room(array $rest, string $role): void
{
    $n = count_staff($rest, $role);
    if ($role === 'chef' && $n >= count_items($rest, 'stove')) fail('need_stove', 422);
    if ($role === 'waiter' && $n >= max_waiters($rest['level'])) fail('staff_full', 422);
    if ($role === 'cleaner' && $n >= max_cleaners($rest['level'])) fail('staff_full', 422);
    if ($role === 'manager' && $n >= max_managers($rest['level'])) fail($rest['level'] < MANAGER_LEVEL ? 'level_locked' : 'staff_full', 422);
    if ($role === 'cashier') {
        if ($rest['level'] < CASHIER_LEVEL) fail('level_locked', 422);
        if ($n >= max_cashiers($rest['level'])) fail('staff_full', 422);
        if (!has_tag($rest, 'register')) fail('need_register', 422);
    }
}

function dish_key(array $b): string
{
    $d = (string)($b['dish'] ?? '');
    if (!isset(dishes()[$d])) fail('bad_request', 422);
    return $d;
}

// The admin panel (admin.html): its own file, loaded only for its routes
if (strncmp($r, 'admin.', 6) === 0) {
    require __DIR__ . '/../lib/admin-api.php';
    admin_route($r);
}

switch ($r) {

    // ---------- accounts (same flow as ThinkHop, but the email check is always required) ----------

    case 'auth.config':
        send_json(['ok' => true, 'installed' => true, 'googleClientId' => (string)config_value('google_client_id', ''), 'mailReady' => mail_settings() !== null]);

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
        record_attempt('register');
        // The same answer whether the address is new or already registered, so nobody can find out who has an account:
        // the account is used after opening the link of the email, which also signs in (auth.verify)
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        if (!$row) send_verification(user_row(new_user($name, $username, $email, $hash, null)), mail_lang($b));
        elseif (empty($row['email_verified_at']) && $row['status'] === 'active' && !too_many_attempts('verify_mail', 3, 60)) {
            // Whoever never confirmed the address gets the link again; whoever did just signs in
            record_attempt('verify_mail');
            send_verification($row, mail_lang($b));
        }
        send_json(['ok' => true, 'checkEmail' => true]);

    case 'auth.login':
        only('POST');
        if (too_many_attempts('login', 10, 15)) fail('rate_limited', 429);
        $b = body();
        $email = mb_strtolower(s($b, 'email', 190));
        $pw = (string)($b['password'] ?? '');
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        // One answer for an unknown address, a wrong password and a Google-only account, in about the same time
        $ok = $row && $row['password_hash'] !== '' ? password_verify($pw, $row['password_hash']) : password_hash($pw, PASSWORD_DEFAULT) === '';
        if (!$ok) {
            record_attempt('login');
            fail('bad_credentials', 401);
        }
        if ($row['status'] !== 'active') fail('disabled', 403);
        clear_attempts('login');
        if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $row['id']]);
        signed_in((int)$row['id']);

    case 'auth.google':
        // A new account needs the privacy consent and a username: the first call answers "needs_signup"
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
            if (empty($b['acceptPrivacy'])) send_json(['ok' => false, 'error' => 'needs_signup', 'name' => $g['name']], 200);
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
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]); // restaurant and everything else follow (ON DELETE CASCADE)
        set_auth_cookie('', time() - 3600);
        send_json(['ok' => true]);

    // ---------- the game ----------

    case 'game.catalog':
        only('GET');
        header('Cache-Control: public, max-age=300');
        send_json(['ok' => true, 'catalog' => catalog_out()]);

    case 'game.state':
        // Opening the game: creates the restaurant the first time, then pays what it earned while the player was away
        $u = writer();
        if (!load_restaurant($u['id'])) create_restaurant($u);
        db()->beginTransaction();
        $rest = load_restaurant($u['id'], true);
        if (client_instance() !== '') $rest['instance'] = client_instance(); // opening the game here makes this the copy that plays
        $away = apply_offline($rest);
        $wages = pay_wages($rest); // salaries of the days that ended since the last visit
        save_restaurant($rest);
        db()->commit();
        send_json(game_state($u['id'], $away) + ['wages' => $wages]);

    case 'game.report':
        $u = writer();
        mutate($u, function (array &$rest) {
            $res = apply_report($rest, body());
            $res['wages'] = pay_wages($rest); // a day may end while playing
            return $res;
        });

    case 'game.layout':
        $u = writer();
        mutate($u, fn(array &$rest) => apply_layout($rest, body()));

    case 'game.candidates':
        // People looking for a job (with their CV); refresh = a new list for CANDIDATE_REFRESH coins
        $u = writer();
        $fresh = !empty(body()['refresh']);
        mutate($u, function (array &$rest) use ($fresh) {
            if ($fresh) spend_coins($rest, CANDIDATE_REFRESH);
            return ['candidates' => candidates_for($rest, $fresh)];
        });

    case 'game.hire':
        // Hire candidate #idx in a role (it can be changed later with game.assign)
        $u = writer();
        $b = body();
        $role = (string)($b['role'] ?? '');
        $idx = (int)($b['idx'] ?? -1);
        if (!in_array($role, ROLES, true)) fail('bad_request', 422);
        mutate($u, function (array &$rest) use ($role, $idx) {
            $list = candidates_for($rest);
            if (!isset($list[$idx])) fail('not_found', 404);
            role_room($rest, $role);
            spend_coins($rest, $list[$idx]['cost']);
            $id = 1;
            foreach ($rest['staff'] as $s) $id = max($id, (int)$s['id'] + 1);
            $c = $list[$idx];
            $new = ['id' => $id, 'role' => $role, 'name' => $c['name'], 'color' => $c['color'], 'trait' => $c['trait'], 'skills' => $c['skills'],
                'outfit' => 'default', 'hair' => $c['hair'] ?? 'short', 'hairColor' => $c['hairColor'] ?? HAIR_COLORS[0],
                'glasses' => $c['glasses'] ?? 'none', 'facial' => $c['facial'] ?? 'none', 'headwear' => 'none'];
            if ($role === 'manager') $new['rank'] = 1;
            $rest['staff'][] = $new;
            array_splice($list, $idx, 1);
            $rest['candidates'] = json_encode(['day' => today_utc(), 'list' => $list]);
            return ['hired' => $new];
        });

    case 'game.assign':
        // A worker changes role (their CV stays the same: they may be better or worse at it)
        $u = writer();
        $b = body();
        $id = (int)($b['id'] ?? 0);
        $role = (string)($b['role'] ?? '');
        if (!in_array($role, ROLES, true)) fail('bad_request', 422);
        mutate($u, function (array &$rest) use ($id, $role) {
            foreach ($rest['staff'] as &$st) {
                if ((int)$st['id'] !== $id) continue;
                if ($st['role'] === $role) return null;
                if (in_array($st['role'], ['chef', 'waiter'], true) && count_staff($rest, $st['role']) <= 1) fail('staff_last', 422);
                role_room($rest, $role);
                $st['skills'] = staff_skills($st);
                $st['role'] = $role;
                if ($role === 'manager' && empty($st['rank'])) $st['rank'] = 1;
                return ['role' => $role];
            }
            fail('not_found', 404);
        });

    case 'game.fire':
        $u = writer();
        $id = (int)(body()['id'] ?? 0);
        mutate($u, function (array &$rest) use ($id) {
            foreach ($rest['staff'] as $i => $s) {
                if ((int)$s['id'] !== $id) continue;
                if (!in_array($s['role'], ['cleaner', 'manager'], true) && count_staff($rest, $s['role']) <= 1) fail('staff_last', 422);
                array_splice($rest['staff'], $i, 1);
                return null;
            }
            fail('not_found', 404);
        });

    case 'game.style':
        // New uniform (bought once per worker, at the uniform's price) and/or a visit to the hairdresser
        $u = writer();
        $b = body();
        $id = (int)($b['id'] ?? 0);
        mutate($u, function (array &$rest) use ($id, $b) {
            foreach ($rest['staff'] as &$st) {
                if ((int)$st['id'] !== $id) continue;
                $cost = 0;
                if (isset($b['outfit']) && $b['outfit'] !== ($st['outfit'] ?? 'default')) {
                    $o = (string)$b['outfit'];
                    if (!isset(UNIFORMS[$o])) fail('bad_request', 422);
                    if (UNIFORMS[$o][1] > $rest['level']) fail('level_locked', 422);
                    $cost += UNIFORMS[$o][0];
                    $st['outfit'] = $o;
                }
                $hair = isset($b['hair']) ? (string)$b['hair'] : ($st['hair'] ?? 'short');
                $color = isset($b['hairColor']) ? (string)$b['hairColor'] : ($st['hairColor'] ?? HAIR_COLORS[0]);
                if ($hair !== ($st['hair'] ?? '') || $color !== ($st['hairColor'] ?? '')) {
                    if (!in_array($hair, HAIRSTYLES, true) || !in_array($color, HAIR_COLORS, true)) fail('bad_request', 422);
                    $cost += HAIR_PRICE;
                    $st['hair'] = $hair;
                    $st['hairColor'] = $color;
                }
                foreach (ACCESSORIES as $slot => $options) {
                    if (!isset($b[$slot]) || (string)$b[$slot] === ($st[$slot] ?? 'none')) continue;
                    if (!in_array((string)$b[$slot], $options, true)) fail('bad_request', 422);
                    $cost += ACCESSORY_PRICE;
                    $st[$slot] = (string)$b[$slot];
                }
                spend_coins($rest, $cost);
                return ['spent' => $cost];
            }
            fail('not_found', 404);
        });

    case 'game.promote':
        // A manager's rank goes up: more hours of running the restaurant while the player is away
        $u = writer();
        $id = (int)(body()['id'] ?? 0);
        mutate($u, function (array &$rest) use ($id) {
            foreach ($rest['staff'] as &$st) {
                if ((int)$st['id'] !== $id || $st['role'] !== 'manager') continue;
                $next = (int)($st['rank'] ?? 1) + 1;
                if (!isset(MANAGER_PROMOTE[$next])) fail('max_level', 422);
                spend_coins($rest, MANAGER_PROMOTE[$next]);
                $st['rank'] = $next;
                return ['rank' => $next];
            }
            fail('not_found', 404);
        });

    case 'game.expand':
        $u = writer();
        $dir = (string)(body()['dir'] ?? '');
        $fl = (int)(body()['floor'] ?? 0);
        if ($dir !== 'col' && $dir !== 'row') fail('bad_request', 422);
        mutate($u, function (array &$rest) use ($dir, $fl) {
            if ($fl > 0) {
                // An upper floor grows within the floor below it: to the right, or to the left when the right is full
                $f = $rest['floors'][$fl - 1] ?? null;
                if (!$f) fail('not_found', 404);
                [$fw, $fd, $x0] = $f;
                $below = floor_rect($fl - 1, $rest['size'], $rest['depth'], $rest['floors']);
                if ($dir === 'row') {
                    if ($fd + 1 > $below[2]) fail('floor_below', 422);
                    if ($fd >= max_side($rest['level'])) fail('level_locked', 422);
                    $fd++;
                } else {
                    if ($fw >= max_side($rest['level'])) fail('level_locked', 422);
                    if ($x0 + $fw + 1 <= $below[0] + $below[1]) $fw++;
                    elseif ($x0 - 1 >= $below[0]) {
                        $x0--;
                        $fw++;
                    } else fail('floor_below', 422);
                }
                spend_coins($rest, (int)round(expand_cost($f[0], $f[1], $dir) * 0.8));
                $rest['floors'][$fl - 1] = [$fw, $fd, $x0];
                return ['size' => "$fw×$fd", 'dir' => $dir, 'floor' => $fl];
            }
            $w = $rest['size'];
            $d = $rest['depth'];
            if (($dir === 'col' ? $w : $d) >= max_side($rest['level'])) fail(($dir === 'col' ? $w : $d) >= MAX_SIDE ? 'max_size' : 'level_locked', 422);
            spend_coins($rest, expand_cost($w, $d, $dir));
            if ($dir === 'row') {
                // A new row at the front: the front wall, the door and the terrace move out towards the street
                foreach ($rest['layout'] as &$it) if ((items()[$it['k']][0] ?? '') === 'outdoor' && $it['z'] >= $d) $it['z']++;
                unset($it);
                $rest['depth'] = $d + 1;
            } else {
                // A new column on the right (the door stays where it is); the east yard moves out
                foreach ($rest['layout'] as &$it) if ((items()[$it['k']][0] ?? '') === 'outdoor' && $it['x'] >= $w) $it['x']++;
                unset($it);
                $rest['size'] = $w + 1;
            }
            return ['size' => $rest['size'] . '×' . $rest['depth'], 'dir' => $dir];
        });

    case 'game.floor':
        // A new storey: small at first, at the back left of the floor below
        $u = writer();
        mutate($u, function (array &$rest) {
            $next = count($rest['floors']) + 1;
            if (!isset(FLOORS[$next])) fail('max_size', 422);
            if (FLOORS[$next][0] > $rest['level']) fail('level_locked', 422);
            $below = floor_rect($next - 1, $rest['size'], $rest['depth'], $rest['floors']);
            spend_coins($rest, FLOORS[$next][1]);
            $rest['floors'][] = [min(FLOOR_START, $below[1]), min(FLOOR_START, $below[2]), $below[0]];
            return ['floor' => $next];
        });

    case 'game.door':
        // Move the door along the front wall and/or give it another style
        $u = writer();
        $b = body();
        mutate($u, function (array &$rest) use ($b) {
            $cost = 0;
            if (isset($b['style']) && $b['style'] !== $rest['door_style']) {
                $st = (string)$b['style'];
                if (!isset(DOORS[$st])) fail('bad_request', 422);
                if (DOORS[$st][1] > $rest['level']) fail('level_locked', 422);
                $cost += DOORS[$st][0];
                $rest['door_style'] = $st;
            }
            if (isset($b['x']) && (int)$b['x'] !== $rest['door']) {
                $x = (int)$b['x'];
                if ($x < 1 || $x > $rest['size'] - 2) fail('door_spot', 422); // not in a corner
                // The tile inside and the path outside must be free (rugs may stay)
                foreach ($rest['layout'] as $it) {
                    $cat = items()[$it['k']][0] ?? '';
                    if ($cat === 'rug' || ($it['f'] ?? 0)) continue;
                    foreach (footprint($it) as [$tx, $tz]) if ($tx === $x && ($tz === $rest['depth'] - 1 || ($cat === 'outdoor' && $tz >= $rest['depth']))) fail('door_spot', 422);
                }
                $cost += DOOR_MOVE;
                $rest['door'] = $x;
            }
            spend_coins($rest, $cost);
            return ['door' => $rest['door'], 'style' => $rest['door_style']];
        });

    case 'game.parking':
        $u = writer();
        mutate($u, function (array &$rest) {
            $next = ($rest['parking'] ?? 0) + 1;
            if (!isset(PARKING[$next])) fail('max_size', 422);
            if (PARKING[$next][1] > $rest['level']) fail('level_locked', 422);
            spend_coins($rest, PARKING[$next][0]);
            $rest['parking'] = $next;
            return ['parking' => $next];
        });

    case 'game.rename':
        $u = writer();
        $name = s(body(), 'name', 40);
        if (mb_strlen($name) < 2) fail('name_required', 422);
        mutate($u, function (array &$rest) use ($name) {
            $rest['name'] = $name;
        });

    case 'game.learn':
    case 'game.upgrade':
        $u = writer();
        $dish = dish_key(body());
        mutate($u, function (array &$rest) use ($dish, $r) {
            $known = load_recipes((int)$rest['user_id']);
            if ($r === 'game.learn') {
                if (isset($known[$dish])) fail('already_known', 409);
                if (!dishes()[$dish][5]) fail('not_on_sale', 422);
                if (dishes()[$dish][3] > $rest['level']) fail('level_locked', 422);
                spend_ingredients((int)$rest['user_id'], dish_cost($dish, 1));
                db()->prepare('INSERT INTO ' . t('recipes') . ' (user_id, dish, level) VALUES (?,?,1)')->execute([$rest['user_id'], $dish]);
                add_xp($rest, 10);
            } else {
                $lvl = $known[$dish] ?? 0;
                if (!$lvl) fail('not_known', 422);
                if ($lvl >= DISH_MAX_LEVEL) fail('max_level', 422);
                spend_ingredients((int)$rest['user_id'], dish_cost($dish, $lvl + 1));
                db()->prepare('UPDATE ' . t('recipes') . ' SET level = ? WHERE user_id = ? AND dish = ?')->execute([$lvl + 1, $rest['user_id'], $dish]);
                add_xp($rest, 10 * ($lvl + 1));
            }
            refresh_popularity($rest);
            return ['dish' => $dish];
        });

    case 'game.menu':
        $u = writer();
        $list = array_values(array_unique(array_map('strval', (array)(body()['dishes'] ?? []))));
        mutate($u, function (array &$rest) use ($list) {
            $known = load_recipes((int)$rest['user_id']);
            if (count($list) < 1) fail('menu_empty', 422);
            if (count($list) > max_menu($rest['level'])) fail('menu_full', 422);
            foreach ($list as $d) if (!isset($known[$d])) fail('not_known', 422);
            $rest['menu'] = $list;
            refresh_popularity($rest);
        });

    case 'game.plant':
        $u = writer();
        $b = body();
        $plot = (int)($b['plot'] ?? -1);
        $crop = (string)($b['crop'] ?? '');
        if (!isset(crops()[$crop])) fail('bad_request', 422);
        mutate($u, function (array &$rest) use ($plot, $crop) {
            if ($plot < 0 || $plot >= garden_plots($rest['level'])) fail('bad_request', 422);
            if (!crops()[$crop][4]) fail('not_on_sale', 422);
            if (crops()[$crop][3] > $rest['level']) fail('level_locked', 422);
            spend_coins($rest, crops()[$crop][0]);
            $ins = db()->prepare('INSERT IGNORE INTO ' . t('garden') . ' (user_id, plot, crop, planted_at, ready_at) VALUES (?,?,?,?,?)');
            $ins->execute([$rest['user_id'], $plot, $crop, now_utc(), gmdate('Y-m-d H:i:s', time() + crops()[$crop][1])]);
            if (!$ins->rowCount()) fail('plot_busy', 409);
        });

    case 'game.harvest':
        // plot = −1 harvests every ripe plot
        $u = writer();
        $plot = (int)(body()['plot'] ?? -1);
        mutate($u, function (array &$rest) use ($plot) {
            $st = db()->prepare('SELECT plot, crop FROM ' . t('garden') . ' WHERE user_id = ? AND ready_at <= ?' . ($plot >= 0 ? ' AND plot = ?' : '') . ' FOR UPDATE');
            $st->execute($plot >= 0 ? [$rest['user_id'], now_utc(), $plot] : [$rest['user_id'], now_utc()]);
            $got = [];
            foreach ($st->fetchAll() as $g) {
                $n = crops()[$g['crop']][2] ?? 1;
                add_ingredient((int)$rest['user_id'], $g['crop'], $n);
                $got[$g['crop']] = ($got[$g['crop']] ?? 0) + $n;
                db()->prepare('DELETE FROM ' . t('garden') . ' WHERE user_id = ? AND plot = ?')->execute([$rest['user_id'], $g['plot']]);
                add_xp($rest, rule('xp_harvest'));
            }
            if (!$got) fail('not_ripe', 422);
            return ['got' => $got];
        });

    case 'game.market':
        $u = writer();
        $b = body();
        $ing = (string)($b['ing'] ?? '');
        $qty = max(1, min(rule('market_limit'), (int)($b['qty'] ?? 1)));
        if (!in_array($ing, market_today(), true)) fail('not_on_sale', 422);
        mutate($u, function (array &$rest) use ($ing, $qty) {
            $st = db()->prepare('SELECT qty FROM ' . t('market_buys') . ' WHERE user_id = ? AND day = ? AND ing = ? FOR UPDATE');
            $st->execute([$rest['user_id'], today_utc(), $ing]);
            if ((int)$st->fetchColumn() + $qty > rule('market_limit')) fail('market_limit', 422);
            spend_coins($rest, market_prices()[$ing] * $qty);
            db()->prepare('INSERT INTO ' . t('market_buys') . ' (user_id, day, ing, qty) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)')->execute([$rest['user_id'], today_utc(), $ing, $qty]);
            add_ingredient((int)$rest['user_id'], $ing, $qty);
        });

    case 'game.kind':
        // Kind of restaurant: generic, fast food, café, inn, luxury
        $u = writer();
        $kind = (string)(body()['kind'] ?? '');
        if (!isset(KINDS[$kind])) fail('bad_request', 422);
        mutate($u, function (array &$rest) use ($kind) {
            if (($rest['kind'] ?? 'generic') === $kind) return null;
            if (KINDS[$kind][3] > $rest['level']) fail('level_locked', 422);
            spend_coins($rest, KINDS[$kind][4]);
            $rest['kind'] = $kind;
            return ['kind' => $kind];
        });

    case 'game.status':
        // Open, not taking new customers (paused), or closed
        $u = writer();
        $status = (string)(body()['status'] ?? '');
        if (!in_array($status, ['open', 'paused', 'closed'], true)) fail('bad_request', 422);
        mutate($u, function (array &$rest) use ($status) {
            set_status($rest, $status);
            $rest['last_report_at'] = now_utc();
        });

    case 'game.ledger':
        // Read-only statement: nothing can be undone from here
        $u = writer();
        $day = (string)(body()['day'] ?? today_utc());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) $day = today_utc();
        send_json(['ok' => true] + ledger_out($u['id'], $day));

    case 'game.reviews':
        $u = writer();
        send_json(['ok' => true] + reviews_out($u['id']));

    case 'game.wish':
        $u = writer();
        $ing = (string)(body()['ing'] ?? '');
        if ($ing !== '' && !isset(INGREDIENTS[$ing])) fail('bad_request', 422);
        mutate($u, function (array &$rest) use ($ing) {
            $rest['wish'] = $ing === '' ? null : $ing;
        });

    // ---------- friends, visits, gifts, leaderboards ----------

    case 'social.friends':
        $u = writer();
        send_json(['ok' => true] + friends_out($u['id']));

    case 'social.search':
        $u = writer();
        $q = trim((string)(body()['q'] ?? ''));
        if (!preg_match('/^[A-Za-z0-9_]{2,20}$/', $q)) send_json(['ok' => true, 'players' => []]);
        $st = db()->prepare('SELECT u.username, f.accepted FROM ' . t('users') . ' u LEFT JOIN ' . t('friends') . " f ON f.friend_id = u.id AND f.user_id = ?
            WHERE u.username LIKE ? AND u.id <> ? AND u.status = 'active' AND u.email_verified_at IS NOT NULL ORDER BY u.username LIMIT 20");
        $st->execute([$u['id'], str_replace('_', '\\_', $q) . '%', $u['id']]);
        send_json(['ok' => true, 'players' => array_map(fn($x) => ['username' => $x['username'], 'status' => $x['accepted'] === null ? 'none' : ((int)$x['accepted'] ? 'friend' : 'requested')], $st->fetchAll())]);

    case 'social.request':
        $u = writer();
        $f = user_by_username((string)(body()['username'] ?? ''));
        if ($f['id'] === $u['id']) fail('not_yourself', 422);
        $status = friend_request($u['id'], $f['id']);
        send_json(['ok' => true, 'status' => $status] + friends_out($u['id']));

    case 'social.accept':
    case 'social.decline':
    case 'social.remove':
        $u = writer();
        $f = user_by_username((string)(body()['username'] ?? ''));
        if ($r === 'social.accept') friend_accept($u['id'], $f['id']);
        else friend_remove($u['id'], $f['id']);
        send_json(['ok' => true] + friends_out($u['id']));

    case 'social.visit':
        $u = writer();
        $f = require_friend($u, (string)(body()['username'] ?? ''));
        send_json(['ok' => true, 'username' => $f['username']] + visit($u['id'], $f['id']));

    case 'social.rate':
        $u = writer();
        $b = body();
        $f = require_friend($u, (string)($b['username'] ?? ''));
        rate_visit($u['id'], $f['id'], (int)($b['stars'] ?? 0));
        send_json(['ok' => true]);

    case 'social.tip':
        $u = writer();
        $b = body();
        $f = require_friend($u, (string)($b['username'] ?? ''));
        send_json(['ok' => true] + tip_friend($u['id'], $f['id'], (int)($b['coins'] ?? 0)));

    case 'social.gift':
        $u = writer();
        $b = body();
        $f = require_friend($u, (string)($b['username'] ?? ''));
        send_gift($u['id'], $f['id'], (string)($b['ing'] ?? ''));
        send_json(['ok' => true] + friends_out($u['id']));

    case 'social.inbox':
        $u = writer();
        send_json(['ok' => true, 'gifts' => inbox_out($u['id'])] + friends_out($u['id']));

    case 'social.claim':
        $u = writer();
        $got = claim_gifts($u['id'], (int)(body()['id'] ?? 0));
        send_json(game_state($u['id']) + ['result' => ['got' => (object)$got]]);

    case 'social.board':
        $u = writer();
        $b = body();
        $scope = ($b['scope'] ?? '') === 'global' ? 'global' : 'friends';
        $period = ($b['period'] ?? '') === 'all' ? 'all' : 'week';
        send_json(['ok' => true, 'rows' => board_out($u['id'], $scope, $period), 'me' => $u['username']]);

    default:
        fail('not_found', 404);
}
