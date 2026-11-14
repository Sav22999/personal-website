<?php
/**
 * MyCurriculum — JSON API.  api/index.php?r=<action>
 *
 * Public:   auth.config, auth.me, auth.register, auth.login, auth.google, auth.logout, auth.verify,
 *           cv.public (the public copy of a CV), cv.photo (its photo)
 * Before the email is confirmed only auth.resend and auth.delete work (see must_verify in lib/bootstrap.php).
 * Account:  auth.password, auth.username, auth.delete
 * CV:       cv.get, cv.save, cv.photoSet, cv.photoDelete
 *
 * Every POST of a signed-in user needs the X-CSRF-Token header (given at sign-in).
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/cv.php';

header('X-Robots-Tag: noindex');
set_exception_handler(function (Throwable $e) {
    if ($e->getMessage() === 'not_installed') fail('not_installed', 503);
    if (db_in_transaction()) db()->rollBack();
    error_log('MyCurriculum API: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
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

/** The username is the address of the public CV (?user=…): letters, digits and dashes, unique whatever the case. */
function clean_username(array $b, int $self = 0): string
{
    $name = mb_strtolower(trim((string)($b['username'] ?? '')));
    if (!preg_match('/^[a-z0-9][a-z0-9-]{1,28}[a-z0-9]$/', $name) || strpos($name, '--') !== false) fail('username_invalid', 422);
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

function cv_lang(array $b): string
{
    $l = (string)($b['cvLang'] ?? $b['lang'] ?? '');
    return in_array($l, CV_LANGS, true) ? $l : 'en';
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

/** New account and its first CV, already filled with what we know (name, email) in the language of the app. */
function new_user(string $name, string $username, string $email, string $hash, ?string $googleSub, string $lang): int
{
    db()->prepare('INSERT INTO ' . t('users') . ' (name, username, email, password_hash, google_sub, created_at, last_login_at) VALUES (?,?,?,?,?,?,?)')
        ->execute([$name, $username, $email, $hash, $googleSub, now_utc(), now_utc()]);
    $id = (int)db()->lastInsertId();
    $cv = new_cv(['name' => $name, 'email' => $email], $lang);
    save_cv($id, $cv['settings'], $cv['profile'], $cv['content']);
    return $id;
}

function signed_in(int $uid): void
{
    db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $uid]);
    $csrf = create_auth_session($uid);
    send_json(['ok' => true, 'user' => public_user(user_row($uid)), 'csrf' => $csrf]);
}

function owner_cv(array $u): array
{
    $cv = load_cv((int)$u['id']);
    if (!$cv) {
        $cv = new_cv($u, 'en');
        save_cv((int)$u['id'], $cv['settings'], $cv['profile'], $cv['content']);
        $cv = load_cv((int)$u['id']);
    }
    $hash = photo_hash((int)$u['id']);
    $cv['photo'] = $hash ? 'api/?r=cv.photo&user=' . rawurlencode((string)$u['username']) . '&v=' . $hash : null;
    $cv['username'] = $u['username'];
    return $cv;
}

switch ($r) {

    // ---------- accounts (same flow as Forkville) ----------

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
        if (empty($b['acceptPrivacy'])) fail('privacy_required', 422);
        // The same answer whether the address is new or already registered, so nobody can find out who has an account
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        $username = $row ? '' : clean_username($b);
        record_attempt('register');
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        if (!$row) send_verification(user_row(new_user($name, $username, $email, $hash, null, cv_lang($b))), mail_lang($b));
        elseif (empty($row['email_verified_at']) && $row['status'] === 'active' && !too_many_attempts('verify_mail', 3, 60)) {
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
        // Google already proves the address: no email check. A new account needs the privacy consent and the
        // address of its public CV, so the first call answers "needs_signup".
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
            // Same address already registered: the Google account is linked to it. If nobody confirmed that
            // address, the password someone else may have chosen is dropped.
            if ($row) db()->prepare('UPDATE ' . t('users') . ' SET google_sub = ?, password_hash = IF(email_verified_at IS NULL, \'\', password_hash), email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')
                ->execute([$g['sub'], now_utc(), $row['id']]);
        }
        if (!$row) {
            if (empty($b['acceptPrivacy'])) send_json(['ok' => false, 'error' => 'needs_signup', 'name' => $g['name'], 'suggest' => suggest_username($g['name'] ?: explode('@', $g['email'])[0])], 200);
            if (too_many_attempts('register', 5, 60)) fail('rate_limited', 429);
            $username = clean_username($b);
            record_attempt('register');
            $row = user_row(new_user($g['name'] ?: explode('@', $g['email'])[0], $username, $g['email'], '', $g['sub'], cv_lang($b)));
            db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        }
        if ($row['status'] !== 'active') fail('disabled', 403);
        signed_in((int)$row['id']);

    case 'auth.suggest':
        // A free address for the public CV, made from a name
        only('GET');
        send_json(['ok' => true, 'username' => suggest_username(s($_GET, 'name', 80))]);

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

    case 'auth.username':
        // The address of the public CV can change; the email of the account never does
        $u = writer();
        if (too_many_attempts('username', 10, 60)) fail('rate_limited', 429);
        record_attempt('username');
        $name = clean_username(body(), (int)$u['id']);
        db()->prepare('UPDATE ' . t('users') . ' SET username = ? WHERE id = ?')->execute([$name, $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.delete':
        $u = writer(true);
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['password'] ?? ''), $row['password_hash'])) fail('bad_password', 422);
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]); // CV, photo and sessions follow (ON DELETE CASCADE)
        set_auth_cookie('', time() - 3600);
        send_json(['ok' => true]);

    // ---------- the CV ----------

    case 'cv.get':
        only('GET');
        $u = require_user();
        send_json(['ok' => true, 'cv' => owner_cv($u)]);

    case 'cv.save':
        // The whole document at once (the editor saves a few seconds after the last change)
        $u = writer();
        $b = body();
        $content = clean_content($b['content'] ?? null, clean_custom_langs($b['settings']['customLangs'] ?? null));
        if (!$content) fail('cv_no_language', 422);
        foreach ($content as $c) if (strlen(json_out($c)) > MAX_CV_BYTES) fail('cv_too_big', 413);
        $settings = clean_settings($b['settings'] ?? null, array_keys($content));
        // Languages removed from the settings are removed from the content too
        $content = array_intersect_key($content, array_flip($settings['langs']));
        // base: the revision the editor knows; a newer one means another window or device saved meanwhile
        $base = isset($b['base']) && is_numeric($b['base']) ? (int)$b['base'] : null;
        $rev = save_cv((int)$u['id'], $settings, clean_profile($b['profile'] ?? null), $content, $base);
        if ($rev === null) fail('cv_conflict', 409);
        send_json(['ok' => true, 'savedAt' => now_utc(), 'rev' => $rev]);

    case 'cv.photoSet':
        $u = writer();
        $data = (string)(body()['data'] ?? '');
        if (!preg_match('~^data:image/(jpeg|png|webp);base64,([A-Za-z0-9+/=]+)$~', $data, $m)) fail('photo_invalid', 422);
        $bin = base64_decode($m[2], true);
        if ($bin === false || strlen($bin) > MAX_PHOTO_BYTES) fail('photo_too_big', 413);
        $info = @getimagesizefromstring($bin);
        if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'], true) || $info[0] > 1200 || $info[1] > 1200) fail('photo_invalid', 422);
        $hash = substr(hash('sha256', $bin), 0, 16);
        db()->prepare('INSERT INTO ' . t('photos') . ' (user_id, mime, data, hash, updated_at) VALUES (?,?,?,?,?)
            ON DUPLICATE KEY UPDATE mime = VALUES(mime), data = VALUES(data), hash = VALUES(hash), updated_at = VALUES(updated_at)')
            ->execute([$u['id'], $info['mime'], $bin, $hash, now_utc()]);
        send_json(['ok' => true, 'photo' => 'api/?r=cv.photo&user=' . rawurlencode((string)$u['username']) . '&v=' . $hash]);

    case 'cv.photoDelete':
        $u = writer();
        db()->prepare('DELETE FROM ' . t('photos') . ' WHERE user_id = ?')->execute([$u['id']]);
        send_json(['ok' => true]);

    case 'cv.public':
        // The CV as a visitor sees it. The owner can preview it even while it is not public.
        only('GET');
        $owner = user_by_username(s($_GET, 'user', 30));
        $cv = $owner ? load_cv((int)$owner['id']) : null;
        $me = current_user();
        $isOwner = $owner && $me && (int)$me['id'] === (int)$owner['id'];
        if (!$cv || (!$cv['settings']['public'] && !$isOwner)) fail('cv_not_found', 404);
        send_json(['ok' => true, 'cv' => public_cv($owner, $cv), 'preview' => !$cv['settings']['public']]);

    case 'cv.photo':
        only('GET');
        $owner = user_by_username(s($_GET, 'user', 30));
        $cv = $owner ? load_cv((int)$owner['id']) : null;
        $me = current_user();
        $isOwner = $owner && $me && (int)$me['id'] === (int)$owner['id'];
        // A hidden photo, or the photo of a private CV, is served only to its owner
        if (!$cv || (!$isOwner && (!$cv['settings']['public'] || !$cv['settings']['show']['photo']))) {
            http_response_code(404);
            exit;
        }
        $st = db()->prepare('SELECT mime, data FROM ' . t('photos') . ' WHERE user_id = ?');
        $st->execute([$owner['id']]);
        $p = $st->fetch();
        if (!$p) {
            http_response_code(404);
            exit;
        }
        header('Content-Type: ' . $p['mime']);
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: ' . ($isOwner ? 'private, no-store' : 'public, max-age=86400'));
        echo $p['data'];
        exit;

    default:
        fail('not_found', 404);
}

/** "Mario Rossi" -> "mario-rossi", or "mario-rossi-2" when taken. */
function suggest_username(string $name): string
{
    $base = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name;
    $base = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($base)) ?? '', '-');
    $base = substr($base, 0, 24) ?: 'cv';
    if (strlen($base) < 3) $base .= '-cv';
    $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE username = ?');
    for ($i = 1; $i < 50; $i++) {
        $try = $i === 1 ? $base : $base . '-' . $i;
        $st->execute([$try]);
        if (!$st->fetchColumn()) return $try;
    }
    return $base . '-' . bin2hex(random_bytes(3));
}
