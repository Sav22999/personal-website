<?php
/**
 * Rendicontazione Ore: configuration, database connection and JSON responses.
 */

declare(strict_types=1);

date_default_timezone_set('Europe/Rome');

/** An error the person can fix: its message is shown as it is. */
class UserError extends RuntimeException
{
}

/** Path of the credentials file. */
function credentials_file(): string
{
    $env = getenv('DAD_REND_CREDENTIALS');
    return $env ?: dirname(__DIR__) . '/credentials.php';
}

/** Reads credentials.php. Returns null if it is missing. */
function credentials(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $file = credentials_file();
    if (!is_file($file)) return $cfg = null;

    $localhost_db = $username_db = $password_db = $database_name = null;
    $table_prefix = 'dad_rend_';
    $google_client_id = '';
    $master_admin_email = '';
    $debug = false;

    require $file;

    return $cfg = [
        'host' => (string)$localhost_db,
        'user' => (string)$username_db,
        'pass' => (string)$password_db,
        'name' => (string)$database_name,
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

/**
 * True only under PHP's built-in server reached from this same machine. A web server such as
 * Apache can never satisfy it, so nothing that depends on it can be switched on in production.
 */
function is_local_dev(): bool
{
    return PHP_SAPI === 'cli-server'
        && in_array((string)($_SERVER['REMOTE_ADDR'] ?? ''), ['127.0.0.1', '::1'], true);
}

/** Table name with prefix, quoted: t('users') -> `dad_rend_users`. */
function t(string $name): string
{
    $prefix = (string)config_value('prefix', 'dad_rend_');
    if (!preg_match('/^[A-Za-z0-9_]{0,25}$/', $prefix)) {
        throw new RuntimeException('Invalid table prefix');
    }
    return '`' . $prefix . $name . '`';
}

function pdo_mysql(): PDO
{
    $c = credentials();
    if (!$c || $c['host'] === '' || $c['user'] === '' || $c['name'] === '') {
        throw new RuntimeException('Database credentials are missing');
    }
    $host = $c['host'];
    $port = 3306;
    if (preg_match('/^(.+):(\d+)$/', $host, $m)) {
        $host = $m[1];
        $port = (int)$m[2];
    }
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $c['name']), $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $pdo->exec("SET time_zone = '+00:00'");
    return $pdo;
}

function pdo_sqlite(string $path): PDO
{
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA journal_mode = WAL');
    return $pdo;
}

/**
 * The database. On the server it is always MySQL and a failure is an error: falling back to another
 * store would silently split the data. Locally it is always SQLite, so tests cannot touch real data.
 */
function db(?PDO $set = null): PDO
{
    static $pdo = null;
    if ($set !== null) {
        $pdo = $set;
    } elseif ($pdo !== null) {
        return $pdo;
    } else {
        $pdo = is_local_dev() && !getenv('DAD_REND_USE_MYSQL')
            ? pdo_sqlite(dirname(__DIR__) . '/data/dev.sqlite')
            : pdo_mysql();
    }
    require_once __DIR__ . '/schema.php';
    ensure_schema($pdo);
    return $pdo;
}

/** Runs $fn inside a transaction. */
function in_transaction(callable $fn)
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $result = $fn();
        $pdo->commit();
        return $result;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function now_utc(): string
{
    return gmdate('Y-m-d H:i:s');
}

function send_json(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fail(string $message, int $status = 400, string $code = 'error', array $extra = []): void
{
    send_json(array_merge(['ok' => false, 'error' => $message, 'code' => $code], $extra), $status);
}

/** The JSON request body. */
function body(): array
{
    static $b = null;
    if ($b !== null) return $b;
    $data = json_decode((string)file_get_contents('php://input'), true);
    return $b = is_array($data) ? $data : [];
}
