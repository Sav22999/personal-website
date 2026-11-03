<?php
/**
 * FoodTrack — shared bootstrap (API, installer, admin portal).
 * Compatible with PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+.
 */

declare(strict_types=1);

const ROLE_USER   = 1;   // normal user: uses the app
const ROLE_ADMIN  = 9;   // administrator: manages recipes, catalog and users
const ROLE_MASTER = 10;  // master admin (exactly one): also manages administrators

const ROLE_LABELS = [
    ROLE_USER   => 'Utente',
    ROLE_ADMIN  => 'Amministratore',
    ROLE_MASTER => 'Master admin',
];

const AUTH_COOKIE   = 'foodtrack_auth';
const SCHEMA_VERSION = 1;

const UNITS = ['pz', 'g', 'kg', 'ml', 'cl', 'l', 'conf', 'cucchiai', 'cucchiaini', 'q.b.'];
const NUTRISCORES = ['A', 'B', 'C', 'D', 'E'];
const MEAL_SLOTS = ['colazione', 'pranzo', 'merenda', 'cena'];
const DIFFICULTIES = ['facile', 'media', 'difficile'];

/** Path of the (git-ignored) credentials file. */
function credentials_file(): string
{
    $env = getenv('FOODTRACK_CREDENTIALS');
    return $env ?: dirname(__DIR__) . '/credentials.php';
}

/**
 * Reads credentials.php (same variables as credentials-sample.php).
 * Returns null when the file does not exist yet (app not installed).
 */
function credentials(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $file = credentials_file();
    if (!is_file($file)) return $cfg = null;
    $localhost_db = $username_db = $password_db = $database_name = null;
    $table_prefix = 'ft_';
    $debug = false;
    require $file;
    if (!$localhost_db || !$username_db || !$database_name) return $cfg = null;
    return $cfg = [
        'host'   => (string)$localhost_db,
        'user'   => (string)$username_db,
        'pass'   => (string)$password_db,
        'name'   => (string)$database_name,
        'prefix' => (string)($table_prefix ?: 'ft_'),
        'debug'  => (bool)$debug,
    ];
}

function config_value(string $key, $default = null)
{
    $c = credentials();
    return $c[$key] ?? $default;
}

/** Table name with prefix, already quoted: t('users') -> `ft_users`. */
function t(string $name): string
{
    $prefix = (string)config_value('prefix', 'ft_');
    if (!preg_match('/^[A-Za-z0-9_]{0,20}$/', $prefix)) throw new RuntimeException('Prefisso tabelle non valido');
    return '`' . $prefix . $name . '`';
}

function pdo_connect(string $host, string $user, string $pass, string $name): PDO
{
    $port = 3306;
    if (preg_match('/^(.+):(\d+)$/', $host, $m)) { $host = $m[1]; $port = (int)$m[2]; }
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $name);
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 10,
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
    }
    return $pdo;
}

function now_utc(): string { return gmdate('Y-m-d H:i:s'); }

function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') return true;
    if (($_SERVER['SERVER_PORT'] ?? '') === '443') return true;
    return strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

function client_ip(): string { return substr((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45); }

/** Folder of the app (e.g. "/projects/foodtrack/"), used as the cookie path. */
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

function fail(string $message, int $code = 400, string $errorCode = 'error'): void
{
    send_json(['ok' => false, 'error' => $errorCode, 'message' => $message], $code);
}

function body(): array
{
    static $b = null;
    if ($b !== null) return $b;
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) return $b = [];
    $d = json_decode($raw, true);
    if (!is_array($d)) fail('Richiesta non valida.', 400, 'bad_json');
    return $b = $d;
}

function s(array $src, string $key, int $max = 255, bool $required = false, string $label = ''): string
{
    $v = $src[$key] ?? '';
    if (!is_string($v) && !is_numeric($v)) $v = '';
    $v = trim((string)$v);
    $label = $label ?: $key;
    if ($required && $v === '') fail("Il campo «{$label}» è obbligatorio.", 422, 'validation');
    if (mb_strlen($v) > $max) fail("Il campo «{$label}» è troppo lungo (max {$max} caratteri).", 422, 'validation');
    return $v;
}

function int_or_null($v): ?int
{
    if ($v === null || $v === '' || $v === false) return null;
    if (!is_numeric($v)) return null;
    return (int)$v;
}

function num_or_null($v): ?float
{
    if ($v === null || $v === '') return null;
    $v = str_replace(',', '.', (string)$v);
    if (!is_numeric($v)) return null;
    $f = (float)$v;
    if ($f < 0 || $f > 999999) return null;
    return round($f, 3);
}

function date_or_null($v): ?string
{
    $v = trim((string)($v ?? ''));
    if ($v === '') return null;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) fail('Data non valida.', 422, 'validation');
    [$y, $m, $d] = array_map('intval', explode('-', $v));
    if (!checkdate($m, $d, $y)) fail('Data non valida.', 422, 'validation');
    return $v;
}

function unit_or_default($v): string
{
    $v = trim((string)($v ?? ''));
    return in_array($v, UNITS, true) ? $v : ($v === '' ? 'pz' : mb_substr($v, 0, 16));
}

function icon_name($v, string $folder, string $fallback): string
{
    $v = (string)($v ?? '');
    if (preg_match('/^[a-z0-9_]{1,40}$/', $v) && is_file(dirname(__DIR__) . "/assets/img/{$folder}/{$v}.png")) return $v;
    return $fallback;
}

// ---------- Sessions (random token in an HttpOnly cookie, SHA-256 in the database) ----------

function set_auth_cookie(string $value, int $expires): void
{
    setcookie(AUTH_COOKIE, $value, [
        'expires'  => $expires,
        'path'     => app_base_path(),
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function create_auth_session(int $userId): string
{
    $token = bin2hex(random_bytes(32));
    $csrf  = bin2hex(random_bytes(24));
    $expires = time() + 60 * 60 * 24 * 60; // 60 days
    db()->prepare('INSERT INTO ' . t('auth_tokens') . ' (token_hash, user_id, csrf_token, user_agent, ip, created_at, last_seen_at, expires_at) VALUES (?,?,?,?,?,?,?,?)')
        ->execute([hash('sha256', $token), $userId, $csrf, substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255), client_ip(), now_utc(), now_utc(), gmdate('Y-m-d H:i:s', $expires)]);
    set_auth_cookie($token, $expires);
    if (random_int(1, 40) === 1) db()->exec('DELETE FROM ' . t('auth_tokens') . ' WHERE expires_at < UTC_TIMESTAMP()');
    return $csrf;
}

function destroy_auth_session(): void
{
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if (is_string($token) && $token !== '') {
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE token_hash = ?')->execute([hash('sha256', $token)]);
    }
    set_auth_cookie('', time() - 3600);
}

function current_user(): ?array
{
    static $cached = false;
    if ($cached !== false) return $cached;
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) return $cached = null;
    $st = db()->prepare(
        'SELECT u.*, a.csrf_token, a.last_seen_at AS token_seen, a.token_hash FROM ' . t('auth_tokens') . ' a
         JOIN ' . t('users') . ' u ON u.id = a.user_id WHERE a.token_hash = ? AND a.expires_at > UTC_TIMESTAMP()'
    );
    $st->execute([hash('sha256', $token)]);
    $u = $st->fetch();
    if (!$u) return $cached = null;
    if (strtotime($u['token_seen'] . ' UTC') < time() - 300) {
        db()->prepare('UPDATE ' . t('auth_tokens') . ' SET last_seen_at = ? WHERE token_hash = ?')->execute([now_utc(), $u['token_hash']]);
        db()->prepare('UPDATE ' . t('users') . ' SET last_seen_at = ? WHERE id = ?')->execute([now_utc(), $u['id']]);
    }
    unset($u['token_hash'], $u['token_seen'], $u['password_hash']);
    $u['id'] = (int)$u['id'];
    $u['role'] = (int)$u['role'];
    $u['active_household_id'] = $u['active_household_id'] !== null ? (int)$u['active_household_id'] : null;
    return $cached = $u;
}

function public_user(array $u): array
{
    $role = (int)$u['role'];
    return [
        'id'        => (int)$u['id'],
        'name'      => $u['name'],
        'email'     => $u['email'],
        'role'      => $role,
        'roleLabel' => ROLE_LABELS[$role] ?? ROLE_LABELS[ROLE_USER],
        'isAdmin'   => $role >= ROLE_ADMIN,
        'isMaster'  => $role >= ROLE_MASTER,
        'status'    => $u['status'],
        'mustChangePassword' => !empty($u['must_change_password']),
        'createdAt' => $u['created_at'] ?? null,
    ];
}

function require_user(int $minRole = ROLE_USER, bool $allowPasswordChange = false): array
{
    $u = current_user();
    if (!$u) fail('Sessione scaduta: accedi di nuovo.', 401, 'unauthenticated');
    if ($u['status'] !== 'active') fail('Il tuo account è stato disabilitato.', 403, 'disabled');
    if ($u['role'] < $minRole) fail('Non hai i permessi per questa operazione.', 403, 'forbidden');
    if (!$allowPasswordChange && !empty($u['must_change_password'])) fail('Prima di continuare devi scegliere una nuova password.', 403, 'must_change_password');
    return $u;
}

function require_csrf(array $u): void
{
    $sent = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($sent === '' || !hash_equals((string)$u['csrf_token'], $sent)) fail('Token di sicurezza non valido: ricarica la pagina.', 403, 'csrf');
}

// ---------- Rate limiting (login / registration / join by code) ----------

function too_many_attempts(string $kind, int $max, int $minutes): bool
{
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('attempts') . ' WHERE kind = ? AND ip = ? AND created_at > ?');
    $st->execute([$kind, client_ip(), gmdate('Y-m-d H:i:s', time() - $minutes * 60)]);
    return (int)$st->fetchColumn() >= $max;
}

function record_attempt(string $kind): void
{
    db()->prepare('INSERT INTO ' . t('attempts') . ' (kind, ip, created_at) VALUES (?,?,?)')->execute([$kind, client_ip(), now_utc()]);
    if (random_int(1, 30) === 1) db()->exec('DELETE FROM ' . t('attempts') . ' WHERE created_at < (UTC_TIMESTAMP() - INTERVAL 1 DAY)');
}

function clear_attempts(string $kind): void
{
    db()->prepare('DELETE FROM ' . t('attempts') . ' WHERE kind = ? AND ip = ?')->execute([$kind, client_ip()]);
}

// ---------- Master admin ----------

/** Guarantees there is exactly one master admin (the oldest active one is kept). */
function normalize_master_admin(): void
{
    $pdo = db();
    $rows = $pdo->query('SELECT id, status FROM ' . t('users') . ' WHERE role >= ' . ROLE_MASTER . " ORDER BY (status = 'active') DESC, id ASC")->fetchAll();
    if (count($rows) <= 1) return;
    $keep = (int)$rows[0]['id'];
    $pdo->prepare('UPDATE ' . t('users') . ' SET role = ? WHERE role >= ? AND id <> ?')->execute([ROLE_ADMIN, ROLE_MASTER, $keep]);
}

function random_code(int $len = 8): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no ambiguous characters (0/O, 1/I)
    $out = '';
    for ($i = 0; $i < $len; $i++) $out .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    return $out;
}
