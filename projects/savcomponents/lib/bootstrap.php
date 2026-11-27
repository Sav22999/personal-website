<?php
/**
 * SavComponents — shared bootstrap (API, installer, public theme CSS).
 * Compatible with PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+.
 *
 * Accounts exist only to keep favourite components and saved configurations (themes and single components).
 * Email + password, or SavAccount (lib/savaccount.php); the address must be confirmed before saving anything, and it can never be changed.
 * Errors are sent as codes: docs/account.js translates them.
 */

declare(strict_types=1);

const APP_NAME = 'SavComponents';
const AUTH_COOKIE = 'savcomponents_auth';
const SCHEMA_VERSION = 3;  // 2: academy (progress of SavComponents Academy); 3: SavAccount (users.sav_sub, sav_flows)
const MAX_ACADEMY_BYTES = 150000; // JSON of the Academy progress of one account
const MAX_ITEMS = 200;          // saved themes + components per account
const MAX_CONFIG_BYTES = 20000; // JSON of one saved item
const STYLES = ['soft', 'tilt', 'sharp', 'brutal', 'glass']; // see dist/sav.css
const MODES = ['light', 'dark', 'auto'];

// ---------- Configuration ----------

function credentials_file(): string
{
    $env = getenv('SAVCOMPONENTS_CREDENTIALS');
    return $env ?: dirname(__DIR__) . '/credentials.php';
}

/** Reads credentials.php (see credentials-sample.php). Null when accounts are not installed. */
function credentials(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $file = credentials_file();
    if (!is_file($file)) return $cfg = null;
    $localhost_db = $username_db = $password_db = $database_name = null;
    $table_prefix = 'sc_';
    $debug = false;
    $mail_config = null;
    $master_emails = [];
    $portal_url = 'https://savcomponents.com/';
    $allowed_origins = ['https://savcomponents.com', 'https://www.savcomponents.com'];
    $savaccount_client_id = $savaccount_client_secret = $savaccount_redirect_uri = '';
    $savaccount_issuer = 'https://www.savaccount.eu';
    require $file;
    if (!$localhost_db || !$username_db || !$database_name) return $cfg = null;
    return $cfg = [
        'host' => (string)$localhost_db,
        'user' => (string)$username_db,
        'pass' => (string)$password_db,
        'name' => (string)$database_name,
        'prefix' => (string)($table_prefix ?: 'sc_'),
        'debug' => (bool)$debug,
        'mail' => $mail_config === false ? false : (is_array($mail_config) ? $mail_config : null),
        'master_emails' => array_values(array_filter(array_map(fn($e) => mb_strtolower(trim((string)$e)), (array)$master_emails))),
        'portal_url' => rtrim((string)$portal_url, '/') . '/',
        'allowed_origins' => array_values(array_map(fn($o) => rtrim((string)$o, '/'), (array)$allowed_origins)),
        'savaccount_client_id' => (string)$savaccount_client_id,
        'savaccount_client_secret' => (string)$savaccount_client_secret,
        'savaccount_redirect_uri' => (string)$savaccount_redirect_uri,
        'savaccount_issuer' => (string)($savaccount_issuer ?: 'https://www.savaccount.eu'),
    ];
}

function config_value(string $key, $default = null)
{
    $c = credentials();
    return $c[$key] ?? $default;
}

/** Table name with prefix, already quoted: t('users') -> `sc_users`. */
function t(string $name): string
{
    $prefix = (string)config_value('prefix', 'sc_');
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

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = credentials();
        if (!$c) throw new RuntimeException('not_installed');
        $pdo = pdo_connect($c['host'], $c['user'], $c['pass'], $c['name']);
        ensure_schema($pdo, $c['prefix']);
    }
    return $pdo;
}

/** Creates or upgrades the tables when the saved schema version is older (one cheap read per request). */
function ensure_schema(PDO $pdo, string $prefix): void
{
    try {
        $v = (int)$pdo->query("SELECT v FROM `{$prefix}meta` WHERE k = 'schema_version'")->fetchColumn();
    } catch (PDOException $e) {
        if ($e->getCode() !== '42S02') throw $e;
        $v = 0;
    }
    if ($v < SCHEMA_VERSION) {
        require_once __DIR__ . '/schema.php';
        install_schema($pdo, $prefix);
    }
}

function now_utc(): string
{
    return gmdate('Y-m-d H:i:s');
}

function iso(?string $utc): ?string
{
    return $utc ? gmdate('Y-m-d\TH:i:s\Z', strtotime($utc . ' UTC')) : null;
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

/** Folder of the app (e.g. "/projects/savcomponents/"), used as the cookie path. */
function app_base_path(): string
{
    $dir = dirname(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/')));
    if (basename($dir) === 'api') $dir = dirname($dir);
    $dir = rtrim($dir, '/');
    return $dir === '' ? '/' : $dir . '/';
}

/** The portal (savcomponents.com): email links and theme links point there. The API lives on saveriomorelli.com. */
function app_url(): string
{
    return (string)config_value('portal_url', 'https://savcomponents.com/');
}

/**
 * CORS: only the portal (and the origins listed in credentials.php) may call the API from a browser.
 * No cookies cross domains: the portal sends the session token in the Authorization header.
 */
function cors(): void
{
    $origin = rtrim((string)($_SERVER['HTTP_ORIGIN'] ?? ''), '/');
    if ($origin === '' || !in_array($origin, (array)config_value('allowed_origins', []), true)) return;
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Sav-Token, X-CSRF-Token');
    header('Access-Control-Max-Age: 600');
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
    if (strlen($raw) > 100000) fail('too_large', 413);
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
    $GLOBALS['sc_new_token'] = $token; // the portal keeps it (Authorization: Bearer), see signed_in()
    return $csrf;
}

/** Session token from "Authorization: Bearer …" (the portal) or from the cookie (same site). */
function request_token(): array
{
    // X-Sav-Token: some Apache setups drop the Authorization header before PHP sees it, a custom header always passes
    $x = (string)($_SERVER['HTTP_X_SAV_TOKEN'] ?? '');
    if (preg_match('/^[a-f0-9]{64}$/i', $x)) return [strtolower($x), true];
    $h = (string)($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
    if ($h === '' && function_exists('getallheaders')) {
        foreach (getallheaders() as $k => $v) if (strcasecmp($k, 'Authorization') === 0) $h = (string)$v;
    }
    if (preg_match('/^Bearer\s+([a-f0-9]{64})$/i', trim($h), $m)) return [strtolower($m[1]), true];
    $c = $_COOKIE[AUTH_COOKIE] ?? '';
    return [is_string($c) ? $c : '', false];
}

function destroy_auth_session(): void
{
    [$token] = request_token();
    if ($token !== '') db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE token_hash = ?')->execute([hash('sha256', $token)]);
    set_auth_cookie('', time() - 3600);
}

function current_user(): ?array
{
    static $cached = false;
    if ($cached !== false) return $cached;
    [$token, $bearer] = request_token();
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) return $cached = null;
    $st = db()->prepare('SELECT u.*, a.csrf_token, a.last_seen_at AS token_seen, a.token_hash FROM ' . t('auth_tokens') . ' a
        JOIN ' . t('users') . ' u ON u.id = a.user_id WHERE a.token_hash = ? AND a.expires_at > UTC_TIMESTAMP()');
    $st->execute([hash('sha256', $token)]);
    $u = $st->fetch();
    if (!$u) return $cached = null;
    if (strtotime($u['token_seen'] . ' UTC') < time() - 300) {
        db()->prepare('UPDATE ' . t('auth_tokens') . ' SET last_seen_at = ? WHERE token_hash = ?')->execute([now_utc(), $u['token_hash']]);
        db()->prepare('UPDATE ' . t('users') . ' SET last_seen_at = ? WHERE id = ?')->execute([now_utc(), $u['id']]);
    }
    unset($u['token_hash'], $u['token_seen']);
    $u['id'] = (int)$u['id'];
    $u['via_bearer'] = $bearer;
    return $cached = $u;
}

/** Master admin: a confirmed account whose email is listed in credentials.php. */
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
        'verified' => !empty($u['email_verified_at']),
        'master' => is_master($u),
        'createdAt' => iso($u['created_at']),
        'hasPassword' => (string)($u['password_hash'] ?? '') !== '', // accounts made with SavAccount have none until they choose one
        'savaccount' => !empty($u['sav_sub']),
    ];
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
    // A token sent in the Authorization header cannot be added by another site on its own: no CSRF risk
    if (!empty($u['via_bearer'])) return;
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
    if (random_int(1, 50) === 1) db()->prepare('DELETE FROM ' . t('attempts') . ' WHERE created_at < ?')->execute([gmdate('Y-m-d H:i:s', time() - 86400)]);
}

function clear_attempts(string $kind): void
{
    db()->prepare('DELETE FROM ' . t('attempts') . ' WHERE kind = ? AND ip = ?')->execute([$kind, client_ip()]);
}

require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/savaccount.php';
