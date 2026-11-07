<?php
/**
 * Fucina — shared bootstrap (API and installer).
 * Compatible with PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+.
 */

declare(strict_types=1);

require_once __DIR__ . '/i18n.php';

const APP_NAME = 'Fucina';
const AUTH_COOKIE = 'fucina_auth';
const SCHEMA_VERSION = 8;
const APP_TIMEZONE = 'Europe/Rome'; // the "day" of streaks and daily challenges

// ---------- Configuration ----------

function credentials_file(): string
{
    $env = getenv('FUCINA_CREDENTIALS');
    return $env ?: dirname(__DIR__) . '/credentials.php';
}

/** Reads credentials.php (see credentials-sample.php). Null when the app is not installed yet. */
function credentials(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $file = credentials_file();
    if (!is_file($file)) return $cfg = null;
    $localhost_db = $username_db = $password_db = $database_name = null;
    $table_prefix = 'fc_';
    $debug = false;
    $google_client_id = '';
    $mail_config = null;
    require $file;
    if (!$localhost_db || !$username_db || !$database_name) return $cfg = null;
    return $cfg = [
        'host' => (string)$localhost_db,
        'user' => (string)$username_db,
        'pass' => (string)$password_db,
        'name' => (string)$database_name,
        'prefix' => (string)($table_prefix ?: 'fc_'),
        'debug' => (bool)$debug,
        'google_client_id' => trim((string)$google_client_id),
        'mail' => is_array($mail_config) ? $mail_config : null,
    ];
}

function config_value(string $key, $default = null)
{
    $c = credentials();
    return $c[$key] ?? $default;
}

/** Table name with prefix, already quoted: t('users') -> `fc_users`. */
function t(string $name): string
{
    $prefix = (string)config_value('prefix', 'fc_');
    if (!preg_match('/^[A-Za-z0-9_]{0,20}$/', $prefix)) throw new RuntimeException('Invalid table prefix');
    return '`' . $prefix . $name . '`';
}

function pdo_connect(string $host, string $user, string $pass, string $name): PDO
{
    $port = 3306;
    if (preg_match('/^(.+):(\d+)$/', $host, $m)) {
        $host = $m[1];
        $port = (int)$m[2];
    }
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $name), $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10,
    ]);
    $pdo->exec("SET time_zone = '+00:00'");
    return $pdo;
}

function db(?PDO $set = null): PDO
{
    static $pdo = null;
    if ($set) return $pdo = $set;
    if ($pdo === null) {
        $c = credentials();
        if (!$c) throw new RuntimeException('not_installed');
        $pdo = pdo_connect($c['host'], $c['user'], $c['pass'], $c['name']);
        ensure_schema();
    }
    return $pdo;
}

function now_utc(): string
{
    return gmdate('Y-m-d H:i:s');
}

function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') return true;
    if (($_SERVER['SERVER_PORT'] ?? '') === '443') return true;
    return strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

function client_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

/** Folder of the app (e.g. "/projects/fucina/"), used as the cookie path. */
function app_base_path(): string
{
    $dir = dirname(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/')));
    if (basename($dir) === 'api') $dir = dirname($dir);
    $dir = rtrim($dir, '/');
    return $dir === '' ? '/' : $dir . '/';
}

// ---------- JSON helpers ----------

function send_json($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fail(string $message, int $code = 400, string $errorCode = 'error', array $vars = []): void
{
    send_json(['ok' => false, 'error' => $errorCode, 'message' => tr($message, $vars)], $code);
}

function body(): array
{
    static $b = null;
    if ($b !== null) return $b;
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) return $b = [];
    $d = json_decode($raw, true);
    if (!is_array($d)) fail('Invalid request.', 400, 'bad_json');
    return $b = $d;
}

/** Trimmed string from the request, without control characters, at most $max characters. */
function s(array $src, string $key, int $max = 255, bool $required = false, string $label = ''): string
{
    $v = $src[$key] ?? '';
    if (!is_string($v) && !is_numeric($v)) $v = '';
    $v = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string)$v) ?? '');
    if ($required && $v === '') fail('{field} is required.', 422, 'validation', ['field' => tr($label ?: $key)]);
    if (mb_strlen($v) > $max) fail('{field} is too long (max {max} characters).', 422, 'validation', ['field' => tr($label ?: $key), 'max' => $max]);
    return $v;
}

function int_or_null($v): ?int
{
    if ($v === null || $v === '') return null;
    return is_numeric($v) ? (int)$v : null;
}

function date_or_null($v): ?string
{
    if (!is_string($v) || $v === '') return null;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) fail('Invalid date.', 422, 'validation');
    [$y, $m, $d] = array_map('intval', explode('-', $v));
    if (!checkdate($m, $d, $y)) fail('Invalid date.', 422, 'validation');
    return $v;
}

/** http(s) URL or empty string. */
function url_or_empty($v, int $max = 2000): string
{
    if (!is_string($v)) return '';
    $v = trim($v);
    if ($v === '') return '';
    if (mb_strlen($v) > $max) fail('The link is too long.', 422, 'validation');
    if (!preg_match('#^https?://[^\s<>"]+$#i', $v) || !filter_var($v, FILTER_VALIDATE_URL)) fail('Invalid link: it must start with https://', 422, 'validation');
    return $v;
}

function random_code(int $len = 8): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no ambiguous characters (0/O, 1/I)
    $out = '';
    for ($i = 0; $i < $len; $i++) $out .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    return $out;
}

function first_name(string $name): string
{
    $p = preg_split('/\s+/', trim($name));
    return $p && $p[0] !== '' ? $p[0] : $name;
}

// ---------- Sessions (HttpOnly cookie, only the SHA-256 of the token is stored) ----------

function set_auth_cookie(string $value, int $expires): void
{
    setcookie(AUTH_COOKIE, $value, ['expires' => $expires, 'path' => app_base_path(), 'secure' => is_https(), 'httponly' => true, 'samesite' => 'Lax']);
}

function create_auth_session(int $userId): string
{
    $token = bin2hex(random_bytes(32));
    $csrf = bin2hex(random_bytes(24));
    $expires = time() + 60 * 60 * 24 * 60; // 60 days
    // user_agent and ip stay empty: nothing uses them, so they are not collected
    db()->prepare('INSERT INTO ' . t('auth_tokens') . ' (token_hash, user_id, csrf_token, user_agent, ip, created_at, last_seen_at, expires_at) VALUES (?,?,?,?,?,?,?,?)')
        ->execute([hash('sha256', $token), $userId, $csrf, '', '', now_utc(), now_utc(), gmdate('Y-m-d H:i:s', $expires)]);
    set_auth_cookie($token, $expires);
    return $csrf;
}

function destroy_auth_session(): void
{
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if (is_string($token) && $token !== '') db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE token_hash = ?')->execute([hash('sha256', $token)]);
    set_auth_cookie('', time() - 3600);
}

function current_user(): ?array
{
    static $cached = false;
    if ($cached !== false) return $cached;
    ensure_schema();
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) return $cached = null;
    $st = db()->prepare('SELECT u.*, a.csrf_token, a.last_seen_at AS token_seen, a.token_hash FROM ' . t('auth_tokens') . ' a
        JOIN ' . t('users') . ' u ON u.id = a.user_id WHERE a.token_hash = ? AND a.expires_at > UTC_TIMESTAMP()');
    $st->execute([hash('sha256', $token)]);
    $u = $st->fetch();
    if (!$u) return $cached = null;
    if (strtotime($u['token_seen'] . ' UTC') < time() - 300) {
        db()->prepare('UPDATE ' . t('auth_tokens') . ' SET last_seen_at = ? WHERE token_hash = ?')->execute([now_utc(), $u['token_hash']]);
        db()->prepare('UPDATE ' . t('users') . ' SET last_seen_at = ? WHERE id = ?')->execute([now_utc(), $u['id']]);
    }
    $u['password_hash'] = $u['password_hash'] === '' ? '' : 'x'; // only "has a password?" is kept in memory
    unset($u['token_hash'], $u['token_seen']);
    $u['id'] = (int)$u['id'];
    return $cached = $u;
}

function public_user(array $u): array
{
    return [
        'id' => (int)$u['id'],
        'name' => $u['name'],
        'email' => $u['email'],
        'hasPassword' => ($u['password_hash'] ?? 'x') !== '',
        'googleLinked' => !empty($u['google_sub']),
        'createdAt' => $u['created_at'] ?? null,
        'xp' => (int)($u['xp'] ?? 0),
        'points' => (int)($u['points'] ?? 0),
        'role' => (int)($u['role'] ?? 0),
        'streak' => (int)($u['streak'] ?? 0),
        'bestStreak' => (int)($u['best_streak'] ?? 0),
        'onBoard' => (bool)($u['on_board'] ?? 1),
        'username' => $u['username'] ?? null,
        'notifyOff' => json_decode((string)($u['notify_off'] ?? ''), true) ?: [],
    ];
}

/** Applies the database changes of newer versions (one cheap read of the meta table per request). */
function ensure_schema(): void
{
    static $done = false;
    if ($done) return;
    $done = true;
    $c = credentials();
    if (!$c) return;
    try {
        $v = (int)db()->query('SELECT v FROM ' . t('meta') . " WHERE k = 'schema_version'")->fetchColumn();
    } catch (Throwable $e) {
        return; // not installed yet
    }
    if ($v < SCHEMA_VERSION) {
        require_once __DIR__ . '/schema.php';
        // A failed update must not take the app down with it: what is already there keeps working, and it is tried again
        try {
            install_schema(db(), $c['prefix']);
        } catch (Throwable $e) {
            error_log('Fucina update: ' . $e->getMessage());
        }
    }
}

function require_user(): array
{
    $u = current_user();
    if (!$u) fail('Session expired: sign in again.', 401, 'unauthenticated');
    if ($u['status'] !== 'active') fail('Your account has been disabled.', 403, 'disabled');
    return $u;
}

function require_csrf(array $u): void
{
    $sent = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($sent === '' || !hash_equals((string)$u['csrf_token'], $sent)) fail('Invalid security token: reload the page.', 403, 'csrf');
}

// ---------- Rate limiting ----------

function too_many_attempts(string $kind, int $max, int $minutes): bool
{
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('attempts') . ' WHERE kind = ? AND ip = ? AND created_at > ?');
    $st->execute([$kind, client_ip(), gmdate('Y-m-d H:i:s', time() - $minutes * 60)]);
    return (int)$st->fetchColumn() >= $max;
}

function record_attempt(string $kind): void
{
    db()->prepare('INSERT INTO ' . t('attempts') . ' (kind, ip, created_at) VALUES (?,?,?)')->execute([$kind, client_ip(), now_utc()]);
    purge_expired();
}

/**
 * The privacy policy promises it: IP addresses of attempts go after 48 hours, expired sessions go too.
 * Rows are deleted by primary key because a server in safe update mode refuses a DELETE on a column without an index.
 * This is housekeeping: if it fails, signing in must still work.
 */
function purge_expired(): void
{
    $jobs = [
        ['attempts', 'id', 'created_at', gmdate('Y-m-d H:i:s', time() - 48 * 3600)],
        ['auth_tokens', 'token_hash', 'expires_at', now_utc()],
    ];
    try {
        foreach ($jobs as [$table, $key, $column, $before]) {
            $st = db()->prepare("SELECT `$key` FROM " . t($table) . " WHERE `$column` < ? LIMIT 500");
            $st->execute([$before]);
            $keys = $st->fetchAll(PDO::FETCH_COLUMN);
            if ($keys) db()->prepare('DELETE FROM ' . t($table) . " WHERE `$key` IN (" . implode(',', array_fill(0, count($keys), '?')) . ')')->execute($keys);
        }
    } catch (Throwable $e) {
        error_log('Fucina purge: ' . $e->getMessage());
    }
}

function clear_attempts(string $kind): void
{
    db()->prepare('DELETE FROM ' . t('attempts') . ' WHERE kind = ? AND ip = ?')->execute([$kind, client_ip()]);
}

// ---------- Sign in with Google ----------

/** Verifies a Google ID token with Google's tokeninfo endpoint (audience, issuer, expiry, verified email). */
function verify_google_token(string $credential): array
{
    $clientId = (string)config_value('google_client_id', '');
    if ($clientId === '') fail('Google sign-in is not configured.', 400, 'google_disabled');
    if (!preg_match('/^[A-Za-z0-9_\-]+\.[A-Za-z0-9_\-]+\.[A-Za-z0-9_\-]+$/', $credential) || strlen($credential) > 4096) fail('Invalid Google token.', 422, 'google_token');
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($credential);
    $raw = false;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_CONNECTTIMEOUT => 5]);
        $raw = curl_exec($ch);
        if ((int)curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) $raw = false;
        curl_close($ch);
    } else {
        $raw = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 8]]));
    }
    $c = $raw ? json_decode((string)$raw, true) : null;
    if (!is_array($c) || empty($c['sub'])) fail('Google sign-in failed: please try again.', 401, 'google_token');
    if (!hash_equals($clientId, (string)($c['aud'] ?? ''))) fail('This Google token is not meant for Fucina.', 401, 'google_token');
    if (!in_array($c['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)) fail('Invalid Google token.', 401, 'google_token');
    if ((int)($c['exp'] ?? 0) < time()) fail('Google sign-in expired: please try again.', 401, 'google_token');
    if (empty($c['email']) || !in_array($c['email_verified'] ?? '', ['true', true, 1, '1'], true)) fail('The email of your Google account is not verified.', 401, 'google_email');
    return ['sub' => substr((string)$c['sub'], 0, 64), 'email' => mb_strtolower((string)$c['email']), 'name' => mb_substr(trim((string)($c['name'] ?? '')), 0, 80)];
}
