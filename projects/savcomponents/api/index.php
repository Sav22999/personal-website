<?php
/**
 * SavComponents — JSON API.  api/index.php?r=<action>
 *
 * Accounts:   auth.config, auth.me, auth.register, auth.login, auth.resend, auth.verify, auth.forgot, auth.reset,
 *             auth.logout, auth.profile, auth.password, auth.delete
 * Favourites: fav.list, fav.set
 * Saved:      item.list, item.save, item.delete   (kind "theme" or "component")
 * Public:     theme.css   (the CSS of a saved theme, linked from any external site)
 * Master:     admin.stats, admin.users, admin.user, admin.status, admin.verify, admin.resend, admin.delete
 *
 * Every POST of a signed-in user needs the X-CSRF-Token header (given at sign-in).
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';

header('X-Robots-Tag: noindex');
set_exception_handler(function (Throwable $e) {
    if ($e->getMessage() === 'not_installed') fail('not_installed', 503);
    error_log('SavComponents API: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    send_json(['ok' => false, 'error' => 'server', 'detail' => config_value('debug', false) ? $e->getMessage() : null], 500);
});

$r = (string)($_GET['r'] ?? '');

if (credentials()) cors();
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; } // CORS preflight

if ($r === 'auth.config' && !credentials()) send_json(['ok' => true, 'installed' => false]);
if (!credentials()) fail('not_installed', 503);

// ================================================================ public theme CSS (no account needed)

if ($r === 'theme.css') {
    $id = (string)($_GET['id'] ?? '');
    $row = null;
    if (preg_match('/^[a-z0-9]{16}$/', $id)) {
        $st = db()->prepare('SELECT i.name, i.config, i.updated_at FROM ' . t('items') . ' i JOIN ' . t('users') . " u ON u.id = i.user_id
            WHERE i.public_id = ? AND i.kind = 'theme' AND u.status = 'active'");
        $st->execute([$id]);
        $row = $st->fetch();
    }
    header('Content-Type: text/css; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Access-Control-Allow-Origin: *');
    if (!$row) {
        http_response_code(404);
        header('Cache-Control: no-store');
        echo "/* SavComponents: this theme does not exist (any more). */\n";
        exit;
    }
    header('Cache-Control: public, max-age=300');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($row['updated_at'] . ' UTC')) . ' GMT');
    echo theme_css((string)$row['name'], json_decode((string)$row['config'], true) ?: []);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json') !== 0) fail('bad_request', 415);

// ================================================================ helpers

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

function reader(): array
{
    only('GET');
    return require_user();
}

function master_writer(): array
{
    $u = writer();
    if (!is_master($u)) fail('forbidden', 403);
    return $u;
}

function master_reader(): array
{
    $u = reader();
    if (!is_master($u)) fail('forbidden', 403);
    return $u;
}

function validate_password(string $pw): void
{
    if (mb_strlen($pw) < 8) fail('password_short', 422);
    if (mb_strlen($pw) > 200) fail('password_long', 422);
}

function user_row(int $id): array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: [];
}

function user_by_email(string $email): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
    $st->execute([$email]);
    return $st->fetch() ?: null;
}

function signed_in(int $uid): void
{
    db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ?, last_seen_at = ? WHERE id = ?')->execute([now_utc(), now_utc(), $uid]);
    $csrf = create_auth_session($uid);
    send_json(['ok' => true, 'user' => public_user(user_row($uid)), 'csrf' => $csrf, 'token' => $GLOBALS['sc_new_token'] ?? null]);
}

/** The account a single-use link belongs to, or null. The link is used up. */
function use_email_token(string $token, string $kind): ?int
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) return null;
    $st = db()->prepare('SELECT user_id FROM ' . t('email_tokens') . ' WHERE token_hash = ? AND kind = ? AND expires_at > ?');
    $st->execute([hash('sha256', $token), $kind, now_utc()]);
    $uid = (int)$st->fetchColumn();
    if (!$uid) return null;
    db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ? AND kind = ?')->execute([$uid, $kind]);
    return $uid;
}

// ---------- saved configurations ----------

/** A CSS custom property value that cannot break out of its declaration. */
function safe_css_value($v): ?string
{
    if (!is_string($v) && !is_numeric($v)) return null;
    $v = trim((string)$v);
    if ($v === '' || mb_strlen($v) > 200) return null;
    if (preg_match('/[;{}<>\\\\]|\/\*|\*\/|@import|url\s*\(|expression\s*\(/i', $v)) return null;
    return $v;
}

/** Keeps only --sav-* tokens with safe values. */
function clean_tokens($raw): array
{
    $out = [];
    if (!is_array($raw)) return $out;
    foreach ($raw as $k => $v) {
        if (!is_string($k) || !preg_match('/^--sav-[a-z0-9-]{1,40}$/', $k)) continue;
        $v = safe_css_value($v);
        if ($v !== null) $out[$k] = $v;
        if (count($out) >= 60) break;
    }
    return $out;
}


/** Validates a configuration and returns the clean version. */
function clean_config(string $kind, $raw): array
{
    if (!is_array($raw)) fail('config_invalid', 422);
    if ($kind === 'theme') {
        $style = in_array($raw['style'] ?? '', STYLES, true) ? $raw['style'] : 'soft';
        $mode = in_array($raw['mode'] ?? '', MODES, true) ? $raw['mode'] : 'auto';
        $settings = [];
        foreach ((array)($raw['settings'] ?? []) as $k => $v) {
            if (is_string($k) && preg_match('/^[a-z]{1,20}$/', $k) && (is_string($v) || is_int($v) || is_float($v)) && mb_strlen((string)$v) <= 40) $settings[$k] = $v;
        }
        return ['style' => $style, 'mode' => $mode, 'tokens' => clean_tokens($raw['tokens'] ?? []), 'settings' => $settings];
    }
    // component: its attributes, the text inside and token overrides on the element
    $attrs = [];
    foreach ((array)($raw['attrs'] ?? []) as $k => $v) {
        if (!is_string($k) || !preg_match('/^[a-z][a-z0-9-]{0,30}$/', $k) || in_array($k, ['style', 'id', 'class', 'slot'], true) || strpos($k, 'on') === 0) continue;
        if ($v === true) $attrs[$k] = true;
        elseif ((is_string($v) || is_numeric($v)) && mb_strlen((string)$v) <= 200) $attrs[$k] = (string)$v;
        if (count($attrs) >= 30) break;
    }
    $text = s($raw, 'text', 200);
    $tag = is_string($raw['tag'] ?? null) && preg_match('/^sav-[a-z][a-z-]{0,30}$/', $raw['tag']) ? $raw['tag'] : null;
    return ['tag' => $tag, 'attrs' => $attrs, 'text' => $text, 'tokens' => clean_tokens($raw['tokens'] ?? [])];
}

function item_out(array $i): array
{
    return [
        'id' => (int)$i['id'], 'publicId' => $i['public_id'], 'kind' => $i['kind'], 'component' => $i['component'], 'name' => $i['name'],
        'config' => json_decode((string)$i['config'], true) ?: [], 'createdAt' => iso($i['created_at']), 'updatedAt' => iso($i['updated_at']),
        'cssUrl' => $i['kind'] === 'theme' ? app_url() . 'theme/' . $i['public_id'] . '.css' : null,
    ];
}

/** Token blocks of sav.css by selector (".sav-style-tilt", ".sav-theme-dark"…), so a theme link needs no classes. */
function css_block(string $selector): string
{
    static $css = null;
    // a copy of the library's sav.css (tools/release.sh in the savcomponents repo updates it)
    $css ??= (string)@file_get_contents(dirname(__DIR__) . '/lib/sav.css');
    if (!preg_match('/\n' . preg_quote($selector, '/') . ' \{\n(.*?)\n\}/s', $css, $m)) return '';
    return $m[1];
}

function theme_css(string $name, array $cfg): string
{
    $name = preg_replace('/[^\p{L}\p{N} \-_.]/u', '', $name);
    $out = "/* SavComponents theme \"$name\" — load it after sav.css. It changes with the saved theme. */\n";
    $mode = $cfg['mode'] ?? 'auto';
    if ($mode === 'dark') $out .= ":root {\n" . css_block('.sav-theme-dark') . "\n}\n";
    if ($mode === 'auto') $out .= "@media (prefers-color-scheme: dark) {\n  :root {\n" . css_block('.sav-theme-dark') . "\n  }\n}\n";
    $style = $cfg['style'] ?? 'soft';
    if ($style !== 'soft' && in_array($style, STYLES, true)) $out .= "/* style: $style */\n:root {\n" . css_block('.sav-style-' . $style) . "\n}\n";
    $tokens = clean_tokens($cfg['tokens'] ?? []);
    if ($tokens) {
        $out .= ":root {\n";
        foreach ($tokens as $k => $v) $out .= "  $k: $v;\n";
        $out .= "}\n";
    }
    return $out;
}

function count_items(int $uid): int
{
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('items') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    return (int)$st->fetchColumn();
}

function admin_user_out(array $u): array
{
    return [
        'id' => (int)$u['id'], 'name' => $u['name'], 'email' => $u['email'], 'verified' => !empty($u['email_verified_at']),
        'status' => $u['status'], 'master' => is_master($u), 'createdAt' => iso($u['created_at']), 'lastLoginAt' => iso($u['last_login_at']),
        'lastSeenAt' => iso($u['last_seen_at']), 'themes' => (int)($u['themes'] ?? 0), 'components' => (int)($u['components'] ?? 0), 'favorites' => (int)($u['favorites'] ?? 0),
    ];
}

/** A master can act on other accounts, never on itself (it would lock itself out). */
function target_user(array $me): array
{
    $id = (int)(body()['id'] ?? 0);
    $u = $id ? user_row($id) : [];
    if (!$u) fail('not_found', 404);
    if ((int)$u['id'] === (int)$me['id']) fail('not_yourself', 409);
    return $u;
}

switch ($r) {

    // ================================================================ accounts

    case 'auth.config':
        send_json(['ok' => true, 'installed' => true, 'mailReady' => mail_settings() !== null]);

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
        if (mb_strlen($name) < 2) fail('name_required', 422);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('email_invalid', 422);
        validate_password($pw);
        if (empty($b['acceptPrivacy'])) fail('privacy_required', 422);
        record_attempt('register');
        // Same answer whether the address is new or already registered: nobody can find out who has an account
        $row = user_by_email($email);
        if (!$row) {
            db()->prepare('INSERT INTO ' . t('users') . ' (name, email, password_hash, created_at) VALUES (?,?,?,?)')
                ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT), now_utc()]);
            send_verification(user_row((int)db()->lastInsertId()));
        } elseif (empty($row['email_verified_at']) && $row['status'] === 'active' && !too_many_attempts('verify_mail', 3, 60)) {
            record_attempt('verify_mail');
            send_verification($row);
        }
        send_json(['ok' => true, 'checkEmail' => true]);

    case 'auth.login':
        only('POST');
        if (too_many_attempts('login', 10, 15)) fail('rate_limited', 429);
        $b = body();
        $row = user_by_email(mb_strtolower(s($b, 'email', 190)));
        $pw = (string)($b['password'] ?? '');
        // One answer for an unknown address and a wrong password, in about the same time
        $ok = $row ? password_verify($pw, $row['password_hash']) : password_hash($pw, PASSWORD_DEFAULT) === '';
        if (!$ok) {
            record_attempt('login');
            fail('bad_credentials', 401);
        }
        if ($row['status'] !== 'active') fail('disabled', 403);
        clear_attempts('login');
        if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $row['id']]);
        signed_in((int)$row['id']);

    case 'auth.resend':
        $u = writer(true);
        if (!empty($u['email_verified_at'])) send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);
        if (too_many_attempts('verify_mail', 3, 60)) fail('rate_limited', 429);
        record_attempt('verify_mail');
        send_verification(user_row($u['id']));
        send_json(['ok' => true]);

    case 'auth.verify':
        // The email link opens the site, which asks for this with a button: mail scanners that open links do nothing
        only('POST');
        if (too_many_attempts('verify', 20, 15)) fail('rate_limited', 429);
        $uid = use_email_token((string)(body()['token'] ?? ''), 'verify');
        if (!$uid) {
            record_attempt('verify');
            fail('token_invalid', 410);
        }
        db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')->execute([now_utc(), $uid]);
        if (user_row($uid)['status'] !== 'active') fail('disabled', 403);
        signed_in($uid);

    case 'auth.forgot':
        only('POST');
        if (too_many_attempts('forgot', 5, 60)) fail('rate_limited', 429);
        record_attempt('forgot');
        $row = user_by_email(mb_strtolower(s(body(), 'email', 190)));
        if ($row && $row['status'] === 'active') send_reset($row);
        send_json(['ok' => true, 'checkEmail' => true]); // same answer for unknown addresses

    case 'auth.reset':
        only('POST');
        if (too_many_attempts('verify', 20, 15)) fail('rate_limited', 429);
        $b = body();
        $pw = (string)($b['password'] ?? '');
        validate_password($pw);
        $uid = use_email_token((string)($b['token'] ?? ''), 'reset');
        if (!$uid) {
            record_attempt('verify');
            fail('token_invalid', 410);
        }
        // the link proves the address too; every other session of the account is closed
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ?, email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), now_utc(), $uid]);
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$uid]);
        if (user_row($uid)['status'] !== 'active') fail('disabled', 403);
        signed_in($uid);

    case 'auth.logout':
        only('POST');
        $u = current_user();
        if ($u) require_csrf($u);
        destroy_auth_session();
        send_json(['ok' => true]);

    case 'auth.profile':
        $u = writer(true);
        $name = s(body(), 'name', 80);
        if (mb_strlen($name) < 2) fail('name_required', 422);
        db()->prepare('UPDATE ' . t('users') . ' SET name = ? WHERE id = ?')->execute([$name, $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.password':
        $u = writer();
        $b = body();
        if (!password_verify((string)($b['current'] ?? ''), user_row($u['id'])['password_hash'])) fail('bad_password', 422);
        $pw = (string)($b['password'] ?? '');
        validate_password($pw);
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
        // other devices are signed out, this one stays
        [$token] = request_token();
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ? AND token_hash <> ?')->execute([$u['id'], hash('sha256', $token)]);
        send_json(['ok' => true]);

    case 'auth.delete':
        $u = writer(true);
        if (!password_verify((string)(body()['password'] ?? ''), user_row($u['id'])['password_hash'])) fail('bad_password', 422);
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]); // favourites, items and sessions go with it
        set_auth_cookie('', time() - 3600);
        send_json(['ok' => true]);

    // ================================================================ favourites

    case 'fav.list':
        $u = reader();
        $st = db()->prepare('SELECT component FROM ' . t('favorites') . ' WHERE user_id = ? ORDER BY created_at');
        $st->execute([$u['id']]);
        send_json(['ok' => true, 'favorites' => $st->fetchAll(PDO::FETCH_COLUMN)]);

    case 'fav.set':
        $u = writer();
        $b = body();
        $c = s($b, 'component', 40);
        if (!preg_match('/^[a-z][a-z0-9-]{0,39}$/', $c)) fail('bad_request', 422);
        if (!empty($b['on'])) {
            $n = db()->prepare('SELECT COUNT(*) FROM ' . t('favorites') . ' WHERE user_id = ?');
            $n->execute([$u['id']]);
            if ((int)$n->fetchColumn() >= 100) fail('too_many', 409);
            db()->prepare('INSERT IGNORE INTO ' . t('favorites') . ' (user_id, component, created_at) VALUES (?,?,?)')->execute([$u['id'], $c, now_utc()]);
        } else {
            db()->prepare('DELETE FROM ' . t('favorites') . ' WHERE user_id = ? AND component = ?')->execute([$u['id'], $c]);
        }
        send_json(['ok' => true]);

    // ================================================================ saved themes and components

    case 'item.list':
        $u = reader();
        $st = db()->prepare('SELECT * FROM ' . t('items') . ' WHERE user_id = ? ORDER BY updated_at DESC');
        $st->execute([$u['id']]);
        send_json(['ok' => true, 'items' => array_map('item_out', $st->fetchAll())]);

    case 'item.save':
        // new when id is missing; otherwise rename and/or replace the configuration of an own item
        $u = writer();
        $b = body();
        $id = (int)($b['id'] ?? 0);
        $name = s($b, 'name', 80);
        if ($name === '') fail('name_required', 422);
        if ($id) {
            $st = db()->prepare('SELECT * FROM ' . t('items') . ' WHERE id = ? AND user_id = ?');
            $st->execute([$id, $u['id']]);
            $item = $st->fetch();
            if (!$item) fail('not_found', 404);
            $config = array_key_exists('config', $b) ? json_encode(clean_config($item['kind'], $b['config']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $item['config'];
            if (strlen($config) > MAX_CONFIG_BYTES) fail('too_large', 413);
            db()->prepare('UPDATE ' . t('items') . ' SET name = ?, config = ?, updated_at = ? WHERE id = ?')->execute([$name, $config, now_utc(), $id]);
        } else {
            $kind = in_array($b['kind'] ?? '', ['theme', 'component'], true) ? $b['kind'] : fail('bad_request', 422);
            $component = null;
            if ($kind === 'component') {
                $component = s($b, 'component', 40);
                if (!preg_match('/^[a-z][a-z0-9-]{0,39}$/', $component)) fail('bad_request', 422);
            }
            if (count_items($u['id']) >= MAX_ITEMS) fail('too_many', 409);
            $config = json_encode(clean_config($kind, $b['config'] ?? null), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (strlen($config) > MAX_CONFIG_BYTES) fail('too_large', 413);
            $public = bin2hex(random_bytes(8)); // 16 characters: the address of the theme CSS
            db()->prepare('INSERT INTO ' . t('items') . ' (user_id, public_id, kind, component, name, config, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$u['id'], $public, $kind, $component, $name, $config, now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
        }
        $st = db()->prepare('SELECT * FROM ' . t('items') . ' WHERE id = ?');
        $st->execute([$id]);
        send_json(['ok' => true, 'item' => item_out($st->fetch())]);

    case 'item.delete':
        $u = writer();
        db()->prepare('DELETE FROM ' . t('items') . ' WHERE id = ? AND user_id = ?')->execute([(int)(body()['id'] ?? 0), $u['id']]);
        send_json(['ok' => true]);

    // ================================================================ master admin

    case 'admin.stats':
        master_reader();
        $q = fn(string $sql) => (int)db()->query($sql)->fetchColumn();
        send_json(['ok' => true, 'stats' => [
            'users' => $q('SELECT COUNT(*) FROM ' . t('users')),
            'verified' => $q('SELECT COUNT(*) FROM ' . t('users') . ' WHERE email_verified_at IS NOT NULL'),
            'disabled' => $q('SELECT COUNT(*) FROM ' . t('users') . " WHERE status = 'disabled'"),
            'active30' => $q('SELECT COUNT(*) FROM ' . t('users') . ' WHERE last_seen_at > UTC_TIMESTAMP() - INTERVAL 30 DAY'),
            'themes' => $q('SELECT COUNT(*) FROM ' . t('items') . " WHERE kind = 'theme'"),
            'components' => $q('SELECT COUNT(*) FROM ' . t('items') . " WHERE kind = 'component'"),
            'favorites' => $q('SELECT COUNT(*) FROM ' . t('favorites')),
        ], 'topFavorites' => db()->query('SELECT component, COUNT(*) AS n FROM ' . t('favorites') . ' GROUP BY component ORDER BY n DESC LIMIT 8')->fetchAll()]);

    case 'admin.users':
        master_reader();
        $search = s($_GET, 'q', 100);
        $filter = (string)($_GET['filter'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per = 25;
        $where = [];
        $args = [];
        if ($search !== '') {
            $where[] = '(u.email LIKE ? OR u.name LIKE ?)';
            $like = '%' . addcslashes($search, '%_\\') . '%';
            array_push($args, $like, $like);
        }
        if ($filter === 'unverified') $where[] = 'u.email_verified_at IS NULL';
        if ($filter === 'disabled') $where[] = "u.status = 'disabled'";
        $w = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = db()->prepare('SELECT COUNT(*) FROM ' . t('users') . " u $w");
        $total->execute($args);
        $st = db()->prepare('SELECT u.*,
            (SELECT COUNT(*) FROM ' . t('items') . " i WHERE i.user_id = u.id AND i.kind = 'theme') AS themes,
            (SELECT COUNT(*) FROM " . t('items') . " i WHERE i.user_id = u.id AND i.kind = 'component') AS components,
            (SELECT COUNT(*) FROM " . t('favorites') . ' f WHERE f.user_id = u.id) AS favorites
            FROM ' . t('users') . " u $w ORDER BY u.created_at DESC LIMIT $per OFFSET " . (($page - 1) * $per));
        $st->execute($args);
        send_json(['ok' => true, 'users' => array_map('admin_user_out', $st->fetchAll()), 'total' => (int)$total->fetchColumn(), 'page' => $page, 'perPage' => $per]);

    case 'admin.user':
        master_reader();
        $u = user_row((int)($_GET['id'] ?? 0));
        if (!$u) fail('not_found', 404);
        $items = db()->prepare('SELECT * FROM ' . t('items') . ' WHERE user_id = ? ORDER BY updated_at DESC');
        $items->execute([$u['id']]);
        $favs = db()->prepare('SELECT component FROM ' . t('favorites') . ' WHERE user_id = ?');
        $favs->execute([$u['id']]);
        send_json(['ok' => true, 'user' => admin_user_out($u), 'items' => array_map('item_out', $items->fetchAll()), 'favorites' => $favs->fetchAll(PDO::FETCH_COLUMN)]);

    case 'admin.status':
        $me = master_writer();
        $u = target_user($me);
        $status = (body()['status'] ?? '') === 'disabled' ? 'disabled' : 'active';
        db()->prepare('UPDATE ' . t('users') . ' SET status = ? WHERE id = ?')->execute([$status, $u['id']]);
        if ($status === 'disabled') db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$u['id']]); // signed out everywhere
        send_json(['ok' => true, 'user' => admin_user_out(user_row((int)$u['id']))]);

    case 'admin.verify':
        $me = master_writer();
        $u = target_user($me);
        db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')->execute([now_utc(), $u['id']]);
        db()->prepare('DELETE FROM ' . t('email_tokens') . " WHERE user_id = ? AND kind = 'verify'")->execute([$u['id']]);
        send_json(['ok' => true, 'user' => admin_user_out(user_row((int)$u['id']))]);

    case 'admin.resend':
        $me = master_writer();
        $u = target_user($me);
        if (!empty($u['email_verified_at'])) fail('already_verified', 409);
        send_verification($u);
        send_json(['ok' => true]);

    case 'admin.delete':
        $me = master_writer();
        $u = target_user($me);
        if (is_master($u)) fail('is_master', 409); // remove it from $master_emails first
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]);
        send_json(['ok' => true]);

    default:
        fail('not_found', 404);
}
