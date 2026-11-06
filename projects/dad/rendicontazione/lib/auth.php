<?php
/**
 * Rendicontazione Ore: accounts, sessions and Sign in with Google.
 *
 * Roles: 1 Utente (only their own hours), 9 Amministratore (everyone's hours, approvals, undo),
 * 10 Master Admin (also chooses who is an administrator).
 */

declare(strict_types=1);

const ROLE_USER = 1;
const ROLE_ADMIN = 9;
const ROLE_MASTER = 10;

const STATUS_PENDING = 'pending';
const STATUS_ACTIVE = 'active';
const STATUS_REJECTED = 'rejected';
const STATUS_DISABLED = 'disabled';

const AUTH_COOKIE = 'dad_rend_auth';
const SESSION_DAYS = 180;

/** Web path of the app, e.g. /projects/dad/rendicontazione/ (from the running script). */
function app_base_path(): string
{
    $script = (string)($_SERVER['SCRIPT_NAME'] ?? '');
    $dir = preg_replace('#/api(/index\.php)?$#', '', dirname($script));
    return rtrim((string)$dir, '/') . '/';
}

function set_auth_cookie(string $value, int $expires): void
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    setcookie(AUTH_COOKIE, $value, [
        'expires' => $expires,
        'path' => app_base_path(),
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function user_by(string $column, string $value): ?array
{
    if (!in_array($column, ['id', 'email', 'google_sub'], true)) {
        throw new InvalidArgumentException('Invalid column');
    }
    $st = db()->prepare('SELECT * FROM ' . t('users') . " WHERE `$column` = ?");
    $st->execute([$value]);
    return $st->fetch() ?: null;
}

function user_create(string $name, string $email, int $role, string $status, ?string $googleSub = null, ?string $avatar = null): int
{
    $st = db()->prepare('INSERT INTO ' . t('users') . ' (name, email, google_sub, avatar_url, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $st->execute([$name, $email, $googleSub, $avatar, $role, $status, now_utc()]);
    return (int)db()->lastInsertId();
}

function user_set(int $id, array $fields): void
{
    $sets = [];
    $params = [];
    foreach ($fields as $column => $value) {
        if (!in_array($column, ['name', 'email', 'avatar_url', 'google_sub', 'role', 'status', 'last_login_at'], true)) {
            throw new InvalidArgumentException('Invalid column');
        }
        $sets[] = "`$column` = ?";
        $params[] = $value;
    }
    if (!$sets) return;
    $params[] = $id;
    db()->prepare('UPDATE ' . t('users') . ' SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);
}

/** The fields of a user that the browser may see. */
function user_public(array $u): array
{
    return [
        'id' => (int)$u['id'],
        'name' => (string)$u['name'],
        'email' => (string)$u['email'],
        'role' => (int)$u['role'],
        'status' => (string)$u['status'],
        'avatar' => (string)($u['avatar_url'] ?? ''),
        'lastLogin' => (string)($u['last_login_at'] ?? ''),
    ];
}

/** Starts a session and sets its cookie. */
function login_user(int $userId): void
{
    $token = bin2hex(random_bytes(32));
    $expires = time() + 86400 * SESSION_DAYS;
    db()->prepare('INSERT INTO ' . t('auth_tokens') . ' (token_hash, user_id, ip, user_agent, created_at, expires_at) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([
            hash('sha256', $token),
            $userId,
            substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 250),
            now_utc(),
            gmdate('Y-m-d H:i:s', $expires),
        ]);
    user_set($userId, ['last_login_at' => now_utc()]);
    set_auth_cookie($token, $expires);
}

function logout_user(): void
{
    $raw = (string)($_COOKIE[AUTH_COOKIE] ?? '');
    if ($raw !== '') {
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE token_hash = ?')->execute([hash('sha256', $raw)]);
    }
    set_auth_cookie('', time() - 3600);
}

/**
 * The signed-in user, or null. The session renews itself when used, so whoever
 * opens the app regularly never has to sign in again.
 */
function current_user(): ?array
{
    static $u = false;
    if ($u !== false) return $u;

    $raw = (string)($_COOKIE[AUTH_COOKIE] ?? '');
    if (!preg_match('/^[a-f0-9]{64}$/', $raw)) return $u = null;

    $hash = hash('sha256', $raw);
    $st = db()->prepare('SELECT u.*, a.expires_at AS session_expires FROM ' . t('users') . ' u INNER JOIN ' . t('auth_tokens') . ' a ON a.user_id = u.id WHERE a.token_hash = ? AND a.expires_at > ?');
    $st->execute([$hash, now_utc()]);
    $row = $st->fetch();
    if (!$row) return $u = null;

    $left = strtotime($row['session_expires'] . ' UTC') - time();
    if ($left < 86400 * (SESSION_DAYS - 7)) {
        $expires = time() + 86400 * SESSION_DAYS;
        db()->prepare('UPDATE ' . t('auth_tokens') . ' SET expires_at = ? WHERE token_hash = ?')->execute([gmdate('Y-m-d H:i:s', $expires), $hash]);
        set_auth_cookie($raw, $expires);
    }
    $row['id'] = (int)$row['id'];
    $row['role'] = (int)$row['role'];
    return $u = $row;
}

function require_active(): array
{
    $u = current_user();
    if (!$u) fail('Devi accedere di nuovo.', 401, 'unauthorized');
    if ($u['status'] !== STATUS_ACTIVE) fail('Il tuo account non è attivo.', 403, 'inactive');
    return $u;
}

function require_admin(): array
{
    $u = require_active();
    if ($u['role'] < ROLE_ADMIN) fail('Questa funzione è riservata agli amministratori.', 403, 'forbidden');
    return $u;
}

function require_master(): array
{
    $u = require_active();
    if ($u['role'] < ROLE_MASTER) fail('Questa funzione è riservata al Master Admin.', 403, 'forbidden');
    return $u;
}

/**
 * The body of a web page, whatever its HTTP status, or null if it cannot be reached.
 * cURL comes first because hosts such as Aruba forbid opening URLs with file_get_contents.
 */
function http_get(string $url): ?string
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_CONNECTTIMEOUT => 5]);
        $body = curl_exec($ch);
        if ($body === false) error_log('rendicontazione: cURL: ' . curl_error($ch));
    } else {
        $context = stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]);
        $body = @file_get_contents($url, false, $context);
        if ($body === false) error_log('rendicontazione: neither cURL nor allow_url_fopen can reach ' . parse_url($url, PHP_URL_HOST));
    }
    return is_string($body) ? $body : null;
}

/**
 * Checks a Google ID token with Google's tokeninfo endpoint.
 * Returns sub, email, name and picture.
 */
function verify_google_token(string $credential): array
{
    $clientId = (string)config_value('google_client_id', '');
    if ($clientId === '') fail("L'accesso con Google non è configurato.", 400, 'google_disabled');
    if (strlen($credential) > 4096 || !preg_match('/^[A-Za-z0-9_\-]+\.[A-Za-z0-9_\-]+\.[A-Za-z0-9_\-]+$/', $credential)) {
        fail('Accesso con Google non riuscito. Riprova.', 422, 'google_token');
    }
    $response = http_get('https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($credential));
    $c = $response === null ? null : json_decode($response, true);
    if (!is_array($c)) fail('Non riesco a contattare Google. Riprova tra poco.', 502, 'google_network');

    $valid = is_array($c)
        && !empty($c['sub'])
        && hash_equals($clientId, (string)($c['aud'] ?? ''))
        && in_array($c['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)
        && (int)($c['exp'] ?? 0) >= time();
    if (!$valid) fail('Accesso con Google non riuscito. Riprova.', 401, 'google_token');
    if (empty($c['email']) || !in_array($c['email_verified'] ?? '', ['true', true, 1, '1'], true)) {
        fail("L'email del tuo account Google non è verificata.", 401, 'google_email');
    }
    return [
        'sub' => (string)$c['sub'],
        'email' => mb_strtolower((string)$c['email']),
        'name' => (string)($c['name'] ?? $c['email']),
        'picture' => (string)($c['picture'] ?? ''),
    ];
}

/**
 * Finds or creates the account of a Google identity. New accounts wait for approval; the only
 * exception is the email in credentials.php, which is always the Master Admin.
 */
function user_from_google(array $g): array
{
    $user = user_by('google_sub', $g['sub']) ?: user_by('email', $g['email']);
    $master = (string)config_value('master_admin_email', '');
    $isMaster = $master !== '' && hash_equals($master, $g['email']);

    if (!$user) {
        $id = user_create(
            mb_substr($g['name'], 0, 100),
            $g['email'],
            $isMaster ? ROLE_MASTER : ROLE_USER,
            $isMaster ? STATUS_ACTIVE : STATUS_PENDING,
            $g['sub'],
            mb_substr($g['picture'], 0, 255) ?: null
        );
        return user_by('id', (string)$id);
    }

    $update = ['avatar_url' => mb_substr($g['picture'], 0, 255) ?: null];
    if (empty($user['google_sub'])) $update['google_sub'] = $g['sub'];
    if ($isMaster) {
        $update['role'] = ROLE_MASTER;
        $update['status'] = STATUS_ACTIVE;
    }
    user_set((int)$user['id'], $update);
    return user_by('id', (string)$user['id']);
}

/** Active users, for the admin's "whose hours" switcher. */
function workers_list(): array
{
    $st = db()->prepare('SELECT u.id, u.name, u.role, (SELECT COUNT(*) FROM ' . t('days') . ' d WHERE d.user_id = u.id) AS day_count FROM ' . t('users') . ' u WHERE u.status = ? ORDER BY u.role ASC, u.name ASC');
    $st->execute([STATUS_ACTIVE]);
    return array_map(function ($r) {
        return ['id' => (int)$r['id'], 'name' => (string)$r['name'], 'role' => (int)$r['role'], 'days' => (int)$r['day_count']];
    }, $st->fetchAll());
}

function pending_count(): int
{
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('users') . ' WHERE status = ?');
    $st->execute([STATUS_PENDING]);
    return (int)$st->fetchColumn();
}

function users_list(): array
{
    $rows = db()->query('SELECT u.*, (SELECT COUNT(*) FROM ' . t('days') . ' d WHERE d.user_id = u.id) AS day_count FROM ' . t('users') . " u ORDER BY CASE WHEN u.status = 'pending' THEN 0 ELSE 1 END, u.role DESC, u.name ASC")->fetchAll();
    return array_map(function ($r) {
        return user_public($r) + ['days' => (int)$r['day_count']];
    }, $rows);
}

/** Removes an account with no hours. Whoever has hours can only be disabled, so nothing recorded gets lost. */
function user_delete(int $id): void
{
    in_transaction(function () use ($id) {
        foreach (['auth_tokens' => 'user_id', 'schedules' => 'user_id', 'history' => 'target_user_id', 'users' => 'id'] as $table => $column) {
            db()->prepare('DELETE FROM ' . t($table) . " WHERE `$column` = ?")->execute([$id]);
        }
    });
}
