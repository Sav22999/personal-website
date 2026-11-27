<?php
/**
 * SavComponents — JSON API.  api/index.php?r=<action>
 *
 * Accounts:   auth.config, auth.me, auth.register, auth.login, auth.resend, auth.verify, auth.forgot, auth.reset,
 *             auth.logout, auth.profile, auth.password, auth.delete
 * SavAccount: auth.sav.start, auth.sav.callback (SavAccount comes back there), auth.sav.finish, auth.sav.create,
 *             auth.sav.code, auth.sav.link, auth.sav.unlink   (see lib/savaccount.php)
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

if ($r === 'auth.sav.callback') sav_callback(); // a page SavAccount opens (it redirects back to the portal), not a JSON call

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
    $out = ['tag' => $tag, 'attrs' => $attrs, 'text' => $text, 'tokens' => clean_tokens($raw['tokens'] ?? [])];
    // saved from the component builder: how the preview looked, the events to listen to, the element id and the HTML inside
    // (the HTML is only ever shown back to its owner, and the portal cleans it again before drawing it)
    if (is_array($raw['builder'] ?? null)) {
        $b = $raw['builder'];
        $pick = fn($v, array $ok, string $def) => in_array($v, $ok, true) ? $v : $def;
        $events = [];
        foreach ((array)($b['events'] ?? []) as $e) {
            if (is_string($e) && preg_match('/^[a-z][a-z-]{0,40}$/', $e)) $events[] = $e;
            if (count($events) >= 12) break;
        }
        $id = s($b, 'id', 60);
        $out['builder'] = [
            'style' => $pick($b['style'] ?? '', STYLES, 'soft'),
            'mode' => $pick($b['mode'] ?? '', ['light', 'dark'], 'light'),
            'device' => $pick($b['device'] ?? '', ['desktop', 'tablet', 'phone'], 'desktop'),
            'bg' => $pick($b['bg'] ?? '', ['page', 'surface', 'dots', 'colour'], 'page'),
            'events' => $events,
            'id' => preg_match('/^[A-Za-z][\w-]{0,59}$/', $id) ? $id : '',
            'children' => s($b, 'children', 4000),
        ];
    }
    return $out;
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

function academy_out(array $a): array
{
    return [
        'username' => $a['username'], 'onBoard' => (bool)$a['on_board'], 'xp' => (int)$a['xp'], 'level' => (int)$a['level'],
        'streak' => (int)$a['streak'], 'progress' => json_decode((string)$a['progress'], true) ?: new stdClass(), 'updatedAt' => $a['updated_at'],
    ];
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
        send_json(['ok' => true, 'installed' => true, 'mailReady' => mail_settings() !== null, 'savaccount' => sav_enabled()]);

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
        $hash = (string)user_row($u['id'])['password_hash'];
        // an account made with SavAccount has no password yet: it chooses one without the current one
        if ($hash !== '' && !password_verify((string)($b['current'] ?? ''), $hash)) fail('bad_password', 422);
        $pw = (string)($b['password'] ?? '');
        validate_password($pw);
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
        // other devices are signed out, this one stays
        [$token] = request_token();
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ? AND token_hash <> ?')->execute([$u['id'], hash('sha256', $token)]);
        send_json(['ok' => true]);

    case 'auth.delete':
        $u = writer(true);
        $row = user_row($u['id']);
        // with a password it is asked again; without one (SavAccount only) the email address is written to confirm
        $okDel = (string)$row['password_hash'] !== '' ? password_verify((string)(body()['password'] ?? ''), $row['password_hash'])
            : mb_strtolower(s(body(), 'confirm', 190)) === mb_strtolower((string)$row['email']);
        if (!$okDel) fail('bad_password', 422);
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]); // favourites, items and sessions go with it
        set_auth_cookie('', time() - 3600);
        send_json(['ok' => true]);

    // ================================================================ SavAccount (lib/savaccount.php)

    case 'auth.sav.start':
        // {mode: login|connect, key, back: docs|academy} -> {url}: the page goes there; connect links the signed-in account
        only('POST');
        if (!sav_enabled()) fail('sav_off', 503);
        if (too_many_attempts('sav', 30, 15)) fail('rate_limited', 429);
        record_attempt('sav');
        $b = body();
        $mode = ($b['mode'] ?? '') === 'connect' ? 'connect' : 'login';
        $back = array_key_exists((string)($b['back'] ?? ''), SAV_BACK) ? (string)$b['back'] : 'docs';
        $uid = null;
        if ($mode === 'connect') $uid = (int)writer()['id'];
        send_json(['ok' => true, 'url' => sav_start($mode, sav_key($b), $back, $uid)]);

    case 'auth.sav.finish':
        // {handoff, key}: what SavAccount said. Signed in, linked, or what is left to do (a new account, or proving an existing one)
        only('POST');
        if (too_many_attempts('sav_finish', 30, 15)) fail('rate_limited', 429);
        $b = body();
        $h = (string)($b['handoff'] ?? '');
        $f = preg_match('/^[a-f0-9]{64}$/', $h) ? sav_flow_row('handoff_hash = ? AND expires_at > ?', [hash('sha256', $h), now_utc()]) : null;
        if (!$f || !hash_equals((string)$f['key_hash'], sav_key($b))) {
            record_attempt('sav_finish');
            fail('sav_expired', 410);
        }
        if (in_array($f['result'], ['taken', 'disabled', 'failed'], true)) {
            sav_drop($f);
            fail($f['result'] === 'disabled' ? 'disabled' : ($f['result'] === 'taken' ? 'sav_taken' : 'sav_failed'), 409);
        }
        if ($f['result'] === 'in') {
            sav_drop($f);
            signed_in((int)$f['target_id']);
        }
        if ($f['result'] === 'connected') {
            $u = current_user();
            if (!$u || (int)$u['id'] !== (int)$f['target_id']) fail('sav_expired', 410);
            sav_drop($f);
            sav_link((int)$u['id'], (string)$f['sub']);
            send_json(['ok' => true, 'connected' => true, 'user' => public_user(user_row($u['id']))]);
        }
        if ($f['result'] === 'signup') send_json(['ok' => true, 'signup' => ['name' => $f['name'], 'email' => $f['email']]]);
        if ($f['result'] === 'link') {
            $u = user_row((int)$f['target_id']);
            send_json(['ok' => true, 'link' => ['email' => $f['email'], 'hasPassword' => (string)($u['password_hash'] ?? '') !== '']]);
        }
        fail('sav_expired', 410);

    case 'auth.sav.create':
        // {handoff, key, name, acceptPrivacy}: a new account for an address nobody has, confirmed by SavAccount
        only('POST');
        $b = body();
        $f = sav_flow_for($b, ['signup']);
        $name = s($b, 'name', 80);
        if (mb_strlen($name) < 2) fail('name_required', 422);
        if (empty($b['acceptPrivacy'])) fail('privacy_required', 422);
        // someone may have taken the address or this SavAccount in the meantime
        if (user_by_email((string)$f['email']) || sav_user('sav_sub = ?', [(string)$f['sub']])) {
            sav_drop($f);
            fail('sav_expired', 410);
        }
        db()->prepare('INSERT INTO ' . t('users') . " (name, email, email_verified_at, password_hash, sav_sub, created_at) VALUES (?,?,?,'',?,?)")
            ->execute([$name, $f['email'], now_utc(), $f['sub'], now_utc()]);
        $uid = (int)db()->lastInsertId();
        sav_drop($f);
        signed_in($uid);

    case 'auth.sav.code':
        // {handoff, key}: sends a code to the address of the existing account, to prove it is theirs
        only('POST');
        $b = body();
        $f = sav_flow_for($b, ['link']);
        if (too_many_attempts('sav_code', 3, 15)) fail('rate_limited', 429);
        record_attempt('sav_code');
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        db()->prepare('UPDATE ' . t('sav_flows') . ' SET code_hash = ?, code_tries = 0, code_until = ? WHERE id = ?')
            ->execute([hash('sha256', $f['id'] . ':' . $code), gmdate('Y-m-d H:i:s', time() + SAV_CODE_MINUTES * 60), $f['id']]);
        send_sav_code(user_row((int)$f['target_id']), $code);
        send_json(['ok' => true]);

    case 'auth.sav.link':
        // {handoff, key, password | code}: proves the existing account, joins the SavAccount to it and signs in
        only('POST');
        $b = body();
        $f = sav_flow_for($b, ['link']);
        if (too_many_attempts('sav_link', 10, 15)) fail('rate_limited', 429);
        $u = user_row((int)$f['target_id']);
        if (!$u || $u['status'] !== 'active') { sav_drop($f); fail('disabled', 403); }
        $ok = false;
        if (isset($b['code'])) {
            // counted before it is compared; after 5 wrong codes a new one has to be asked for
            if (!$f['code_hash'] || (int)$f['code_tries'] >= SAV_CODE_TRIES || strtotime($f['code_until'] . ' UTC') < time()) fail('sav_code_expired', 410);
            db()->prepare('UPDATE ' . t('sav_flows') . ' SET code_tries = code_tries + 1 WHERE id = ?')->execute([$f['id']]);
            $ok = hash_equals((string)$f['code_hash'], hash('sha256', $f['id'] . ':' . s($b, 'code', 12)));
            if (!$ok) fail('sav_code_wrong', 422);
        } else {
            $ok = (string)$u['password_hash'] !== '' && password_verify((string)($b['password'] ?? ''), $u['password_hash']);
            if (!$ok) {
                record_attempt('sav_link');
                fail('bad_password', 422);
            }
        }
        sav_drop($f);
        sav_link((int)$u['id'], (string)$f['sub']);
        signed_in((int)$u['id']);

    case 'auth.sav.unlink':
        // only when the account has a password: it always keeps a way to sign in
        $u = writer();
        $row = user_row($u['id']);
        if (empty($row['sav_sub'])) send_json(['ok' => true, 'user' => public_user($row)]);
        if ((string)$row['password_hash'] === '') fail('sav_last_method', 409);
        db()->prepare('UPDATE ' . t('users') . ' SET sav_sub = NULL WHERE id = ?')->execute([$u['id']]);
        send_sav_notice($row, false);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

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
            // SavComponents Academy: accounts with progress, those on the leaderboard, those active in the last 30 days
            'learners' => $q('SELECT COUNT(*) FROM ' . t('academy') . ' WHERE xp > 0'),
            'onBoard' => $q('SELECT COUNT(*) FROM ' . t('academy') . ' WHERE on_board = 1'),
            'learners30' => $q('SELECT COUNT(*) FROM ' . t('academy') . ' WHERE xp > 0 AND updated_at > UTC_TIMESTAMP() - INTERVAL 30 DAY'),
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

    // ================================================================ SavComponents Academy (savcomponents.com/app)

    case 'academy.get':
        $u = reader();
        $st = db()->prepare('SELECT * FROM ' . t('academy') . ' WHERE user_id = ?');
        $st->execute([$u['id']]);
        $a = $st->fetch();
        send_json(['ok' => true, 'academy' => $a ? academy_out($a) : null]);

    case 'academy.save':
        // the whole progress, already merged by the app with what it read (several devices)
        $u = writer();
        $b = body();
        if (!is_array($b['progress'] ?? null)) fail('bad_request', 422);
        $json = json_encode($b['progress'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false || strlen($json) > MAX_ACADEMY_BYTES) fail('too_large', 413);
        $xp = max(0, min(10000000, (int)($b['xp'] ?? 0)));
        $level = max(1, min(999, (int)($b['level'] ?? 1)));
        $streak = max(0, min(9999, (int)($b['streak'] ?? 0)));
        db()->prepare('INSERT INTO ' . t('academy') . ' (user_id, xp, level, streak, progress, updated_at) VALUES (?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE xp = VALUES(xp), level = VALUES(level), streak = VALUES(streak), progress = VALUES(progress), updated_at = VALUES(updated_at)')
            ->execute([$u['id'], $xp, $level, $streak, $json, now_utc()]);
        send_json(['ok' => true, 'updatedAt' => now_utc()]);

    case 'academy.profile':
        // the username (the only thing other people see) and whether the account is on the leaderboard
        $u = writer();
        $b = body();
        $name = s($b, 'username', 24);
        if ($name !== '' && !preg_match('/^[A-Za-z0-9_]{3,20}$/', $name)) fail('username_invalid', 422);
        $board = !empty($b['onBoard']) && $name !== '';
        $st = db()->prepare('SELECT user_id FROM ' . t('academy') . ' WHERE username = ? AND user_id <> ?');
        $st->execute([$name, $u['id']]);
        if ($name !== '' && $st->fetch()) fail('username_taken', 409);
        db()->prepare('INSERT INTO ' . t('academy') . " (user_id, username, on_board, progress, updated_at) VALUES (?,?,?,'{}',?)
            ON DUPLICATE KEY UPDATE username = VALUES(username), on_board = VALUES(on_board)")
            ->execute([$u['id'], $name === '' ? null : $name, $board ? 1 : 0, now_utc()]);
        send_json(['ok' => true]);

    case 'academy.board':
        // public: the 20 learners with most XP among those who joined the leaderboard (username, level, XP, streak)
        only('GET');
        if (too_many_attempts('board', 120, 10)) fail('rate_limited', 429);
        record_attempt('board');
        $st = db()->query('SELECT a.username, a.xp, a.level, a.streak FROM ' . t('academy') . ' a JOIN ' . t('users') . " u ON u.id = a.user_id
            WHERE a.on_board = 1 AND a.username IS NOT NULL AND u.status = 'active' ORDER BY a.xp DESC, a.updated_at ASC LIMIT 20");
        $top = array_map(fn($x) => ['username' => $x['username'], 'xp' => (int)$x['xp'], 'level' => (int)$x['level'], 'streak' => (int)$x['streak']], $st->fetchAll());
        $me = null;
        $cu = current_user();
        if ($cu) {
            $m = db()->prepare('SELECT xp, on_board FROM ' . t('academy') . ' WHERE user_id = ?');
            $m->execute([$cu['id']]);
            $row = $m->fetch();
            if ($row && (int)$row['on_board'] === 1) {
                $rank = db()->prepare('SELECT COUNT(*) FROM ' . t('academy') . ' WHERE on_board = 1 AND username IS NOT NULL AND xp > ?');
                $rank->execute([(int)$row['xp']]);
                $me = ['rank' => (int)$rank->fetchColumn() + 1];
            }
        }
        send_json(['ok' => true, 'top' => $top, 'me' => $me]);

    default:
        fail('not_found', 404);
}
