<?php
/**
 * Rendicontazione Ore — shared bootstrap.
 * Multi-role authentication (User, Admin, Master Admin),
 * Google Sign-In verification, and resilient database connection
 * (MySQL primary with transparent SQLite fallback for local development).
 */

declare(strict_types=1);

const ROLE_USER = 1;     // Utente (lavoratore/papà): gestisce le proprie ore
const ROLE_ADMIN = 9;    // Amministratore: approva utenti, gestisce orari e report
const ROLE_MASTER = 10;  // Master Admin (Saverio): controlla amministratori e impostazioni

const ROLE_LABELS = [
    ROLE_USER => 'Utente',
    ROLE_ADMIN => 'Amministratore',
    ROLE_MASTER => 'Master Admin',
];

const STATUS_PENDING = 'pending';
const STATUS_ACTIVE = 'active';
const STATUS_REJECTED = 'rejected';
const STATUS_DISABLED = 'disabled';

const STATUS_LABELS = [
    STATUS_PENDING => 'In attesa di approvazione',
    STATUS_ACTIVE => 'Attivo',
    STATUS_REJECTED => 'Rifiutato',
    STATUS_DISABLED => 'Disabilitato',
];

const AUTH_COOKIE = 'dad_rend_auth';
const SCHEMA_VERSION = 1;

/** Path of the credentials file. */
function credentials_file(): string
{
    $env = getenv('DAD_REND_CREDENTIALS');
    return $env ?: dirname(__DIR__) . '/credentials.php';
}

/** Reads credentials.php. Returns null if missing. */
function credentials(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $file = credentials_file();
    if (!is_file($file)) return $cfg = null;

    $localhost_db = $username_db = $password_db = $database_name = null;
    $table_prefix = 'dad_rend_';
    $google_client_id = '';
    $master_admin_email = 'saverio.morelli@gmail.com';
    $debug = false;

    require $file;

    return $cfg = [
        'host' => (string)($localhost_db ?? ''),
        'user' => (string)($username_db ?? ''),
        'pass' => (string)($password_db ?? ''),
        'name' => (string)($database_name ?? ''),
        'prefix' => (string)($table_prefix ?: 'dad_rend_'),
        'google_client_id' => trim((string)$google_client_id),
        'master_admin_email' => mb_strtolower(trim((string)$master_admin_email)),
        'debug' => (bool)$debug,
    ];
}

function config_value(string $key, $default = null)
{
    $c = credentials();
    return $c[$key] ?? $default;
}

/** Table name with prefix, quoted: t('users') -> `dad_rend_users`. */
function t(string $name): string
{
    $prefix = (string)config_value('prefix', 'dad_rend_');
    if (!preg_match('/^[A-Za-z0-9_]{0,25}$/', $prefix)) {
        throw new RuntimeException('Prefisso tabelle non valido');
    }
    return '`' . $prefix . $name . '`';
}

/** Check if current DB connection is SQLite. */
function is_sqlite(?PDO $pdo = null): bool
{
    $p = $pdo ?: db();
    return $p->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';
}

/** Connect to MySQL via PDO with strict timeout. */
function pdo_mysql(string $host, string $user, string $pass, string $name, int $timeout = 3): PDO
{
    $port = 3306;
    if (preg_match('/^(.+):(\d+)$/', $host, $m)) {
        $host = $m[1];
        $port = (int)$m[2];
    }
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $name);
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => $timeout,
    ]);
    $pdo->exec("SET time_zone = '+00:00'");
    return $pdo;
}

/** Connect to local SQLite fallback database. */
function pdo_sqlite(): PDO
{
    $dataDir = dirname(__DIR__) . '/data';
    if (!is_dir($dataDir)) {
        @mkdir($dataDir, 0755, true);
    }
    $dbPath = $dataDir . '/rendicontazione.sqlite';
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    $pdo->exec('PRAGMA journal_mode = WAL;');
    return $pdo;
}

/**
 * Main database provider.
 * Connects to MySQL. If MySQL is unreachable (local dev or timeout),
 * gracefully falls back to local SQLite storage.
 */
function db(?PDO $set = null): PDO
{
    static $pdo = null;
    if ($set !== null) return $pdo = $set;
    if ($pdo !== null) return $pdo;

    $c = credentials();
    $triedMysql = false;

    if ($c && !empty($c['host']) && !empty($c['user']) && !empty($c['name'])) {
        $triedMysql = true;
        try {
            $pdo = pdo_mysql($c['host'], $c['user'], $c['pass'], $c['name'], 2);
        } catch (Throwable $e) {
            // MySQL unreachable (e.g. running locally on Mac while DB is on Aruba)
            $pdo = null;
        }
    }

    if ($pdo === null) {
        $pdo = pdo_sqlite();
    }

    require_once __DIR__ . '/schema.php';
    ensure_schema($pdo);

    return $pdo;
}

function now_utc(): string
{
    return gmdate('Y-m-d H:i:s');
}

/** Output JSON and exit. */
function send_json(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/** Fail with JSON error response. */
function fail(string $message, int $status = 400, ?string $code = null, array $extra = []): void
{
    send_json(array_merge([
        'ok' => false,
        'error' => $message,
        'code' => $code ?: 'error',
    ], $extra), $status);
}

/** Read JSON request body. */
function body(): array
{
    static $b = null;
    if ($b !== null) return $b;
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) return $b = [];
    $data = json_decode($raw, true);
    return $b = is_array($data) ? $data : [];
}

/**
 * Validates Google ID token using Google's public tokeninfo endpoint.
 * Returns array with keys: sub, email, name, picture.
 */
function verify_google_token(string $credential): array
{
    $clientId = (string)config_value('google_client_id', '');
    if ($clientId === '') {
        fail('Google Sign-In non è configurato.', 400, 'google_disabled');
    }
    if (!preg_match('/^[A-Za-z0-9_\-]+\.[A-Za-z0-9_\-]+\.[A-Za-z0-9_\-]+$/', $credential) || strlen($credential) > 4096) {
        fail('Token Google non valido.', 422, 'invalid_token');
    }

    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($credential);
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 7,
            'ignore_errors' => true,
            'header' => "User-Agent: RendicontazioneOre/1.0\r\n",
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $res = @file_get_contents($url, false, $ctx);
    if ($res === false) {
        fail('Impossibile verificare il token con i server Google.', 502, 'google_network_error');
    }

    $payload = json_decode($res, true);
    if (!is_array($payload) || empty($payload['sub'])) {
        fail('Verifica Google non riuscita. Riprova.', 401, 'google_token_failed');
    }

    // Verify audience
    if (!hash_equals($clientId, (string)($payload['aud'] ?? ''))) {
        fail('Il token Google non è destinato a questa applicazione.', 401, 'google_aud_mismatch');
    }

    // Verify issuer
    if (!in_array($payload['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)) {
        fail('Emittente token non valido.', 401, 'google_iss_invalid');
    }

    // Verify expiration
    if ((int)($payload['exp'] ?? 0) < time()) {
        fail('Sessione Google scaduta. Effettua nuovamente l\'accesso.', 401, 'google_expired');
    }

    // Verify email verified
    if (empty($payload['email']) || !in_array($payload['email_verified'] ?? '', ['true', true, 1, '1'], true)) {
        fail('L\'indirizzo email dell\'account Google non è verificato.', 401, 'google_email_unverified');
    }

    return [
        'sub' => (string)$payload['sub'],
        'email' => mb_strtolower((string)$payload['email']),
        'name' => (string)($payload['name'] ?? $payload['email']),
        'picture' => (string)($payload['picture'] ?? ''),
    ];
}

/** Get session token from cookie or Authorization header. */
function get_auth_token_string(): ?string
{
    if (!empty($_COOKIE[AUTH_COOKIE])) {
        return (string)$_COOKIE[AUTH_COOKIE];
    }
    $h = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/^Bearer\s+([A-Za-z0-9_\-]{32,128})$/', $h, $m)) {
        return $m[1];
    }
    return null;
}

/**
 * Returns current logged-in user or null.
 */
function current_user(): ?array
{
    static $u = false;
    if ($u !== false) return $u;

    $raw = get_auth_token_string();
    if (!$raw) return $u = null;

    $tokenHash = hash('sha256', $raw);
    $st = db()->prepare('
        SELECT u.* FROM ' . t('users') . ' u
        INNER JOIN ' . t('auth_tokens') . ' t ON t.user_id = u.id
        WHERE t.token_hash = ? AND t.expires_at > ?
    ');
    $st->execute([$tokenHash, now_utc()]);
    $row = $st->fetch();

    if (!$row) return $u = null;

    return $u = [
        'id' => (int)$row['id'],
        'name' => (string)$row['name'],
        'email' => (string)$row['email'],
        'role' => (int)$row['role'],
        'role_label' => ROLE_LABELS[(int)$row['role']] ?? 'Utente',
        'status' => (string)$row['status'],
        'status_label' => STATUS_LABELS[(string)$row['status']] ?? $row['status'],
        'avatar_url' => (string)($row['avatar_url'] ?? ''),
        'google_sub' => (string)($row['google_sub'] ?? ''),
        'created_at' => (string)$row['created_at'],
        'last_login_at' => (string)($row['last_login_at'] ?? ''),
    ];
}

/** Creates an auth session for a user and sets the auth cookie. */
function login_user(int $userId): string
{
    $tokenRaw = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $tokenRaw);
    $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 250);
    $now = now_utc();
    $expires = gmdate('Y-m-d H:i:s', time() + 86400 * 90); // 90 days validity

    $st = db()->prepare('
        INSERT INTO ' . t('auth_tokens') . '
        (token_hash, user_id, ip, user_agent, created_at, expires_at)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $st->execute([$tokenHash, $userId, $ip, $ua, $now, $expires]);

    // Update user last_login_at
    db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([$now, $userId]);

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    setcookie(AUTH_COOKIE, $tokenRaw, [
        'expires' => time() + 86400 * 90,
        'path' => '/projects/dad/rendicontazione/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    return $tokenRaw;
}

/** Destroy auth session. */
function logout_user(): void
{
    $raw = get_auth_token_string();
    if ($raw) {
        $tokenHash = hash('sha256', $raw);
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE token_hash = ?')->execute([$tokenHash]);
    }
    setcookie(AUTH_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/projects/dad/rendicontazione/',
    ]);
}

/** Require authenticated session. */
function require_auth(): array
{
    $u = current_user();
    if (!$u) {
        fail('Accesso non autorizzato. Effettua l\'accesso.', 401, 'unauthorized');
    }
    return $u;
}

/** Require active user (approved by admin). */
function require_active_user(): array
{
    $u = require_auth();
    if ($u['status'] === STATUS_PENDING) {
        fail('Il tuo account è in attesa di approvazione manuale da parte di un amministratore.', 403, 'account_pending', [
            'user' => $u,
        ]);
    }
    if ($u['status'] !== STATUS_ACTIVE) {
        fail('Il tuo account non è attivo o è stato disabilitato.', 403, 'account_inactive', [
            'user' => $u,
        ]);
    }
    return $u;
}

/** Require administrator role. */
function require_admin(): array
{
    $u = require_active_user();
    if ($u['role'] < ROLE_ADMIN) {
        fail('Funzionalità riservata agli amministratori.', 403, 'forbidden');
    }
    return $u;
}

/** Require master admin role. */
function require_master(): array
{
    $u = require_active_user();
    if ($u['role'] < ROLE_MASTER) {
        fail('Funzionalità riservata al Master Admin.', 403, 'forbidden');
    }
    return $u;
}

/** Helper: calculate duration between two times in hours as decimal float. */
function calculate_hours(string $start, string $end): float
{
    $t1 = strtotime('1970-01-01 ' . $start);
    $t2 = strtotime('1970-01-01 ' . $end);
    if ($t1 === false || $t2 === false) return 0.0;
    if ($t2 < $t1) {
        // Crossing midnight
        $t2 += 86400;
    }
    $diffSeconds = $t2 - $t1;
    return round($diffSeconds / 3600, 2);
}

/** Format decimal hours to HH:MM:SS format (e.g. 5.5 -> 05:30:00, 125.0 -> 125:00:00). */
function format_hours_hms(float $hours): string
{
    $totalSeconds = (int)round($hours * 3600);
    $h = floor($totalSeconds / 3600);
    $rem = $totalSeconds % 3600;
    $m = floor($rem / 60);
    $s = $rem % 60;
    return sprintf('%02d:%02d:%02d', $h, $m, $s);
}

/** Format decimal hours to human-readable string (e.g. 8.5 -> "8h 30m"). */
function format_hours_human(float $hours): string
{
    $totalMinutes = (int)round($hours * 60);
    $h = floor($totalMinutes / 60);
    $m = $totalMinutes % 60;
    if ($m === 0) return $h . 'h';
    return sprintf('%dh %02dm', $h, $m);
}
