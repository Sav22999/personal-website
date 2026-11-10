<?php
/**
 * ThinkHop — shared bootstrap (API and installer).
 * Compatible with PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+.
 *
 * Errors are sent as codes: the app translates them (see err_* in i18n.js), so the server needs no translations.
 */

declare(strict_types=1);

const APP_NAME = 'ThinkHop';
const AUTH_COOKIE = 'thinkhop_auth';
const SCHEMA_VERSION = 12;
const START_POINTS = 100; // START_POINTS in engine.js: the gift every wallet starts with
const LANGS = ['en', 'it', 'es', 'fr', 'de'];
const REQUIRED_LANGS = ['it', 'en']; // a question of the admin panel needs at least these
const ROLE_ADMIN = 9;
const MAX_STAGE = 1000; // STAGES in engine.js
const TIERS = ['novice', 'easy', 'medium', 'hard', 'expert', 'master', 'legend'];
const QUESTION_TYPES = ['sign', 'sequence', 'symbols', 'oddone', 'order', 'weird', 'nonsense', 'riddle', 'pattern', 'letters', 'intruder', 'grid', 'compass', 'lamps', 'code', 'ifthen', 'liars', 'boxes', 'whoowns', 'days', 'trap', 'scales', 'cups', 'locks', 'suspects', 'alchemy', 'seats', 'mirror', 'gears', 'lasers', 'cubes', 'pipes', 'mobiles', 'origami'];
const MAX_DIFFICULTY = 9.99;
const MAX_FRIENDS = 200;

// ---------- Configuration ----------

function credentials_file(): string
{
    $env = getenv('THINKHOP_CREDENTIALS');
    return $env ?: dirname(__DIR__) . '/credentials.php';
}

/** Reads credentials.php (see credentials-sample.php). Null when the accounts are not installed yet. */
function credentials(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $file = credentials_file();
    if (!is_file($file)) return $cfg = null;
    $localhost_db = $username_db = $password_db = $database_name = null;
    $table_prefix = 'th_';
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
        'prefix' => (string)($table_prefix ?: 'th_'),
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

/** Table name with prefix, already quoted: t('users') -> `th_users`. */
function t(string $name): string
{
    $prefix = (string)config_value('prefix', 'th_');
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

/** Folder of the app (e.g. "/projects/thinkhop/"), used as the cookie path. */
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

function fail(string $errorCode, int $code = 400): void
{
    send_json(['ok' => false, 'error' => $errorCode], $code);
}

function body(): array
{
    static $b = null;
    if ($b !== null) return $b;
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) return $b = [];
    $d = json_decode($raw, true);
    if (!is_array($d)) fail('bad_request', 400);
    return $b = $d;
}

/** Trimmed string from the request, without control characters, cut at $max characters. */
function s(array $src, string $key, int $max = 255): string
{
    $v = $src[$key] ?? '';
    if (!is_string($v) && !is_numeric($v)) $v = '';
    $v = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string)$v) ?? '');
    return mb_substr($v, 0, $max);
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
    db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE expires_at < UTC_TIMESTAMP()')->execute();
    db()->prepare('INSERT INTO ' . t('auth_tokens') . ' (token_hash, user_id, csrf_token, created_at, last_seen_at, expires_at) VALUES (?,?,?,?,?,?)')
        ->execute([hash('sha256', $token), $userId, $csrf, now_utc(), now_utc(), gmdate('Y-m-d H:i:s', $expires)]);
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
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) return $cached = null;
    $st = db()->prepare('SELECT u.*, a.csrf_token, a.last_seen_at AS token_seen, a.token_hash FROM ' . t('auth_tokens') . ' a
        JOIN ' . t('users') . ' u ON u.id = a.user_id WHERE a.token_hash = ? AND a.expires_at > UTC_TIMESTAMP()');
    $st->execute([hash('sha256', $token)]);
    $u = $st->fetch();
    if (!$u) return $cached = null;
    if (strtotime($u['token_seen'] . ' UTC') < time() - 300) {
        db()->prepare('UPDATE ' . t('auth_tokens') . ' SET last_seen_at = ? WHERE token_hash = ?')->execute([now_utc(), $u['token_hash']]);
    }
    unset($u['token_hash'], $u['token_seen']);
    $u['id'] = (int)$u['id'];
    return $cached = $u;
}

/** The shop as one player sees it: items on sale, plus those already bought even if taken off sale later. */
function avatar_shop(int $uid): array
{
    $st = db()->prepare('SELECT i.id, i.kind, i.price, i.enabled, (u.item_id IS NOT NULL) AS owned FROM ' . t('avatar_items') . ' i
        LEFT JOIN ' . t('user_items') . ' u ON u.item_id = i.id AND u.user_id = ? ORDER BY i.kind, i.sort');
    $st->execute([$uid]);
    $out = [];
    foreach ($st->fetchAll() as $r) {
        $owned = (bool)$r['owned'] || (int)$r['price'] === 0;
        if (!$r['enabled'] && !$r['owned']) continue;
        $out[] = ['id' => $r['id'], 'kind' => $r['kind'], 'price' => (int)$r['price'], 'owned' => $owned];
    }
    return $out;
}

function avatar_spent(int $uid): int
{
    $st = db()->prepare('SELECT COALESCE(SUM(price), 0) FROM ' . t('user_items') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    return (int)$st->fetchColumn();
}

function user_balance(array $u): int
{
    $uid = (int)$u['id'];
    $st = db()->prepare('SELECT COALESCE(SUM(score), 0) - COALESCE(SUM(spent), 0) FROM ' . t('games') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    return max(0, START_POINTS + (int)$st->fetchColumn() + (int)($u['points_adjust'] ?? 0) - avatar_spent($uid));
}

function public_user(array $u): array
{
    $uid = (int)$u['id'];
    return [
        'id' => $uid,
        'name' => $u['name'],
        'username' => $u['username'] ?? null,
        'avatar' => $u['avatar'] ?? null,
        'ranking' => (int)($u['show_in_ranking'] ?? 1) === 1,
        'email' => $u['email'],
        'isAdmin' => (int)($u['role'] ?? 0) >= ROLE_ADMIN,
        'hasPassword' => ($u['password_hash'] ?? '') !== '',
        'googleLinked' => !empty($u['google_sub']),
        'verified' => !empty($u['email_verified_at']),
        'mustVerify' => must_verify($u),
        'pointsAdjust' => (int)($u['points_adjust'] ?? 0),
        'avatarSpent' => avatar_spent($uid),
        'shop' => avatar_shop($uid),
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
    } catch (PDOException $e) {
        if ($e->getCode() !== '42S02') throw $e;
        $v = 0; // credentials.php is there but the tables are not: they are created now, so accounts work at once
    }
    if ($v < SCHEMA_VERSION) {
        require_once __DIR__ . '/schema.php';
        install_schema(db(), $c['prefix']);
    }
}

/** An account made with email and password is usable only once its address is confirmed (see lib/mail.php). */
function must_verify(array $u): bool
{
    return empty($u['email_verified_at']) && verification_required();
}

/** $unverifiedOk: the few calls an account can make before confirming its email (resend the link, delete itself). */
function require_user(bool $unverifiedOk = false): array
{
    $u = current_user();
    if (!$u) fail('unauthenticated', 401);
    if ($u['status'] !== 'active') fail('disabled', 403);
    if (!$unverifiedOk && must_verify($u)) fail('email_unverified', 403);
    return $u;
}

function require_admin(): array
{
    $u = require_user();
    if ((int)$u['role'] < ROLE_ADMIN) fail('forbidden', 403);
    return $u;
}

function require_csrf(array $u): void
{
    $sent = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($sent === '' || !hash_equals((string)$u['csrf_token'], $sent)) fail('csrf', 403);
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
    // The IP addresses of attempts are kept for 48 hours at most (see privacy.html)
    db()->prepare('DELETE FROM ' . t('attempts') . ' WHERE created_at < ?')->execute([gmdate('Y-m-d H:i:s', time() - 48 * 3600)]);
    db()->prepare('INSERT INTO ' . t('attempts') . ' (kind, ip, created_at) VALUES (?,?,?)')->execute([$kind, client_ip(), now_utc()]);
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
    if ($clientId === '') fail('google_disabled', 400);
    if (!preg_match('/^[A-Za-z0-9_\-]+\.[A-Za-z0-9_\-]+\.[A-Za-z0-9_\-]+$/', $credential) || strlen($credential) > 4096) fail('google_token', 422);
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($credential);
    $raw = false;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_CONNECTTIMEOUT => 5]);
        $raw = curl_exec($ch);
        if ((int)curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) $raw = false;
    } else {
        $raw = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 8]]));
    }
    $c = $raw ? json_decode((string)$raw, true) : null;
    if (!is_array($c) || empty($c['sub'])) fail('google_token', 401);
    if (!hash_equals($clientId, (string)($c['aud'] ?? ''))) fail('google_token', 401);
    if (!in_array($c['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)) fail('google_token', 401);
    if ((int)($c['exp'] ?? 0) < time()) fail('google_token', 401);
    if (empty($c['email']) || !in_array($c['email_verified'] ?? '', ['true', true, 1, '1'], true)) fail('google_email', 401);
    return ['sub' => substr((string)$c['sub'], 0, 64), 'email' => mb_strtolower((string)$c['email']), 'name' => mb_substr(trim((string)($c['name'] ?? '')), 0, 80)];
}

require_once __DIR__ . '/mail.php';
