<?php
/**
 * OpenNewsletter — shared bootstrap (API, public pages, installer and the cron scripts in projects/cron).
 * Compatible with PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+.
 *
 * Errors are sent as codes: the app translates them (see err_* in assets/i18n.js).
 * Accounts: email + password (the address must be confirmed before the app opens) or Google (already confirmed by
 * Google). The email of an account is unique and can never be changed.
 */

declare(strict_types=1);

const APP_NAME = 'OpenNewsletter';
const AUTH_COOKIE = 'opennewsletter_auth';
const SCHEMA_VERSION = 3; // 2: groups.rate_per_hour, 3: users.fav_templates, subscribers.source 'import'
const LANGS = ['en', 'it']; // languages of the app itself
// Languages a group can collect subscribers in (and write newsletters for)
const SUB_LANGS = ['it', 'en', 'fr', 'de', 'es', 'pt', 'nl', 'pl', 'ro', 'sv', 'el', 'cs', 'hu', 'da', 'fi', 'hr', 'sl', 'sk', 'bg', 'ca'];
// Roles inside a group, from the weakest: each one can do everything the ones before it can
const ROLES = ['user', 'moderator', 'publisher', 'admin', 'owner'];
const SLOT_MINUTES = 30; // newsletters leave at :00 or :30 (the cron runs every 30 minutes)
const MAX_DOC_BYTES = 300000;
const MAX_IMAGE_BYTES = 1500000;

// ---------- Configuration ----------

function credentials_file(): string
{
    $env = getenv('OPENNEWSLETTER_CREDENTIALS');
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
    $table_prefix = 'on_';
    $debug = false;
    $google_client_id = '';
    $mail_config = null;
    $secret_key = '';
    $master_emails = [];
    $default_timezone = 'Europe/Rome';
    require $file;
    if (!$localhost_db || !$username_db || !$database_name) return $cfg = null;
    return $cfg = [
        'host' => (string)$localhost_db,
        'user' => (string)$username_db,
        'pass' => (string)$password_db,
        'name' => (string)$database_name,
        'prefix' => (string)($table_prefix ?: 'on_'),
        'debug' => (bool)$debug,
        'google_client_id' => trim((string)$google_client_id),
        'mail' => $mail_config === false ? false : (is_array($mail_config) ? $mail_config : null),
        'secret_key' => (string)$secret_key,
        'master_emails' => array_values(array_filter(array_map(fn($e) => mb_strtolower(trim((string)$e)), (array)$master_emails))),
        'timezone' => in_array((string)$default_timezone, timezone_identifiers_list(), true) ? (string)$default_timezone : 'Europe/Rome',
    ];
}

function config_value(string $key, $default = null)
{
    $c = credentials();
    return $c[$key] ?? $default;
}

/** Table name with prefix, already quoted: t('users') -> `on_users`. */
function t(string $name): string
{
    $prefix = (string)config_value('prefix', 'on_');
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

/** Folder of the app (e.g. "/projects/opennewsletter/"), used as the cookie path. */
function app_base_path(): string
{
    $dir = dirname(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/')));
    if (basename($dir) === 'api') $dir = dirname($dir);
    $dir = rtrim($dir, '/');
    return $dir === '' ? '/' : $dir . '/';
}

/**
 * Absolute URL of the app (e.g. https://www.saveriomorelli.com/projects/opennewsletter/).
 * The cron scripts have no request: they use the address saved by the last web request (meta app_url).
 */
function app_url(): string
{
    static $url = null;
    if ($url !== null) return $url;
    if (PHP_SAPI === 'cli' || !isset($_SERVER['HTTP_HOST']) || basename(dirname((string)($_SERVER['SCRIPT_NAME'] ?? ''))) === 'cron') {
        $saved = '';
        try {
            $saved = (string)db()->query('SELECT v FROM ' . t('meta') . " WHERE k = 'app_url'")->fetchColumn();
        } catch (Throwable $e) { /* not installed yet */ }
        return $url = $saved ?: 'https://www.saveriomorelli.com/projects/opennewsletter/';
    }
    $host = preg_replace('/[^a-z0-9.:-]/i', '', (string)$_SERVER['HTTP_HOST']);
    $url = (is_https() ? 'https://' : 'http://') . $host . app_base_path();
    return $url;
}

/** Remembers the public address of the app for the cron scripts (one write a day at most). */
function remember_app_url(): void
{
    if (PHP_SAPI === 'cli' || !isset($_SERVER['HTTP_HOST'])) return;
    if (is_file(dirname(__DIR__, 2) . '/.illerom-live')) return; // moved: the public address is the one on www.illerom.eu
    $url = app_url();
    try {
        $st = db()->query('SELECT v FROM ' . t('meta') . " WHERE k = 'app_url'");
        if ((string)$st->fetchColumn() !== $url) {
            db()->prepare('INSERT INTO ' . t('meta') . " (k, v) VALUES ('app_url', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute([$url]);
        }
    } catch (Throwable $e) { /* not important */ }
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
    if (db_in_transaction()) db()->rollBack();
    send_json(['ok' => false, 'error' => $errorCode], $code);
}

function db_in_transaction(): bool
{
    try {
        return credentials() !== null && db()->inTransaction();
    } catch (Throwable $e) {
        return false;
    }
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

/** Like s() but keeps the line breaks (longer texts: terms, page intro). */
function multiline(array $src, string $key, int $max = 5000): string
{
    $v = $src[$key] ?? '';
    if (!is_string($v)) $v = '';
    $v = str_replace(["\r\n", "\r"], "\n", $v);
    $v = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v) ?? '');
    return mb_substr($v, 0, $max);
}

function json_out($v): string
{
    return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
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
    $expires = time() + 60 * 60 * 24 * 30; // 30 days
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

/** Master admin of the whole platform: a confirmed account whose email is listed in credentials.php. */
function is_master(array $u): bool
{
    return !empty($u['email_verified_at']) && in_array(mb_strtolower((string)$u['email']), (array)config_value('master_emails', []), true);
}

function public_user(array $u): array
{
    return [
        'id' => (int)$u['id'],
        'name' => $u['name'],
        'email' => $u['email'],
        'lang' => $u['ui_lang'] ?: 'en',
        'hasPassword' => ($u['password_hash'] ?? '') !== '',
        'googleLinked' => !empty($u['google_sub']),
        'verified' => !empty($u['email_verified_at']),
        'master' => is_master($u),
        'mailReady' => mail_settings() !== null,
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
        $v = 0; // credentials.php is there but the tables are not: they are created now
    }
    if ($v < SCHEMA_VERSION) {
        require_once __DIR__ . '/schema.php';
        install_schema(db(), $c['prefix']);
    }
}

/** $unverifiedOk: the few calls an account can make before confirming its email (resend the link, delete itself). */
function require_user(bool $unverifiedOk = false): array
{
    $u = current_user();
    if (!$u) fail('unauthenticated', 401);
    if ($u['status'] !== 'active') fail('disabled', 403);
    if (!$unverifiedOk && empty($u['email_verified_at'])) fail('email_unverified', 403);
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

// ---------- Secrets of the groups (SMTP settings), encrypted with $secret_key ----------

function secret_key(): string
{
    $hex = (string)config_value('secret_key', '');
    if (!preg_match('/^[a-f0-9]{64}$/i', $hex)) throw new RuntimeException('secret_key is missing or invalid in credentials.php');
    return hex2bin($hex);
}

function seal(array $data): string
{
    $iv = random_bytes(12);
    $tag = '';
    $c = openssl_encrypt(json_out($data), 'aes-256-gcm', secret_key(), OPENSSL_RAW_DATA, $iv, $tag);
    if ($c === false) throw new RuntimeException('encryption failed');
    return 'v1:' . base64_encode($iv . $tag . $c);
}

function unseal(?string $blob): ?array
{
    if (!$blob || strpos($blob, 'v1:') !== 0) return null;
    $raw = base64_decode(substr($blob, 3), true);
    if ($raw === false || strlen($raw) < 29) return null;
    $plain = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', secret_key(), OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
    $d = $plain === false ? null : json_decode($plain, true);
    return is_array($d) ? $d : null;
}

// ---------- Small helpers ----------

/** The browser family from the User-Agent (only the name is stored, never the whole string). */
function browser_name(string $ua): string
{
    $map = [
        '/SamsungBrowser\//' => 'Samsung Internet', '/OPR\/|Opera/' => 'Opera', '/Edg(e|A|iOS)?\//' => 'Edge', '/Vivaldi\//' => 'Vivaldi',
        '/YaBrowser\//' => 'Yandex', '/Brave/' => 'Brave', '/Firefox\/|FxiOS\//' => 'Firefox', '/CriOS\/|Chrome\//' => 'Chrome', '/Safari\//' => 'Safari',
    ];
    foreach ($map as $re => $name) if (preg_match($re, $ua)) return $name;
    return $ua === '' ? '' : 'Other';
}

/** First language of a list (navigator.language or Accept-Language), as a two-letter code. */
function lang_code(string $raw): string
{
    return preg_match('/^\s*([a-z]{2,3})/i', $raw, $m) ? strtolower($m[1]) : '';
}

/** A UTC datetime of the database as ISO 8601 for the browser. */
function iso(?string $utc): ?string
{
    return $utc ? gmdate('Y-m-d\TH:i:s\Z', strtotime($utc . ' UTC')) : null;
}

/** Next free slot of 30 minutes after a moment (UTC timestamp). */
function next_slot(int $ts): int
{
    $step = SLOT_MINUTES * 60;
    return (int)(floor($ts / $step) + 1) * $step;
}

require_once __DIR__ . '/mail.php';
