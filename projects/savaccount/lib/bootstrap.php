<?php
/**
 * SavAccount — shared bootstrap (configuration, database, crypto, sessions, rate limits).
 * Compatible with PHP 7.4+ and MySQL 5.7+ / MariaDB 10.3+.
 *
 * Browsers never talk to this API: they talk to savaccount.eu, whose gateway forwards the calls here with a shared
 * key (X-Sav-Gateway), the client address and the session token. Without that key every call is refused.
 * Errors are sent as codes: the site translates them.
 */

declare(strict_types=1);

const APP_NAME = 'SavAccount';
const SCHEMA_VERSION = 1;
const PASSWORD_MIN = 10;
const PASSWORD_MAX = 128;
const MAX_EMAILS = 10;             // addresses per account
const CODE_MINUTES = 15;           // email codes and links
const CODE_ATTEMPTS = 5;           // wrong codes before the challenge dies
const SUDO_MINUTES = 10;           // how long a password confirmation lasts
const SESSION_DAYS = 30;           // "keep me signed in"
const SESSION_HOURS = 12;          // otherwise
const PENDING_HOURS = 48;          // accounts never confirmed are deleted after this
const EVENT_DAYS = 90;             // security log

// ---------- Configuration ----------

function credentials_file(): string
{
    $env = getenv('SAVACCOUNT_CREDENTIALS');
    return $env ?: dirname(__DIR__) . '/credentials.php';
}

/** Reads credentials.php (see credentials-sample.php). Null when it is missing or incomplete. */
function credentials(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $file = credentials_file();
    if (!is_file($file)) return $cfg = null;
    $localhost_db = $username_db = $password_db = $database_name = null;
    $table_prefix = 'sva_';
    $enabled = false;
    $debug = false;
    $secret_key = $gateway_key = '';
    $site_url = 'https://www.savaccount.eu';
    $master_emails = [];
    $mail_config = null;
    $check_pwned_passwords = true;
    require $file;
    if (!$localhost_db || !$username_db || !$database_name) return $cfg = null;
    if (strlen((string)$secret_key) < 64 || strlen((string)$gateway_key) < 64) return $cfg = null; // no weak keys
    return $cfg = [
        'host' => (string)$localhost_db,
        'user' => (string)$username_db,
        'pass' => (string)$password_db,
        'name' => (string)$database_name,
        'prefix' => (string)$table_prefix,
        'enabled' => (bool)$enabled,
        'debug' => (bool)$debug,
        'secret' => (string)$secret_key,
        'gateway' => (string)$gateway_key,
        'site' => rtrim((string)$site_url, '/'),
        'masters' => array_values(array_filter(array_map(fn($e) => norm_email((string)$e), (array)$master_emails))),
        'mail' => is_array($mail_config) && !empty($mail_config['smtp_host']) && !empty($mail_config['smtp_password']) ? $mail_config : null,
        'pwned' => (bool)$check_pwned_passwords,
    ];
}

function cfg(string $key)
{
    return credentials()[$key] ?? null;
}

/** Table name with prefix, quoted: t('users') -> `sva_users`. */
function t(string $name): string
{
    $prefix = (string)cfg('prefix');
    if (!preg_match('/^[A-Za-z0-9_]{1,20}$/', $prefix)) throw new RuntimeException('Invalid table prefix');
    return '`' . $prefix . $name . '`';
}

// ---------- Database ----------

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;
    $c = credentials();
    if (!$c) throw new RuntimeException('not_installed');
    $host = $c['host'];
    $port = 3306;
    if (preg_match('/^(.+):(\d+)$/', $host, $m)) [, $host, $port] = [$m[0], $m[1], (int)$m[2]];
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $c['name']), $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10,
    ]);
    $pdo->exec("SET time_zone = '+00:00', SESSION sql_mode = 'STRICT_ALL_TABLES'");
    try {
        $v = (int)$pdo->query('SELECT v FROM ' . t('meta') . " WHERE k = 'schema_version'")->fetchColumn();
    } catch (PDOException $e) {
        if ($e->getCode() !== '42S02') throw $e;
        $v = 0;
    }
    if ($v < SCHEMA_VERSION) {
        require_once __DIR__ . '/schema.php';
        install_schema($pdo, $c['prefix']);
    }
    return $pdo;
}

/** Runs a prepared statement. */
function q(string $sql, array $args = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($args);
    return $st;
}

function one(string $sql, array $args = []): ?array
{
    return q($sql, $args)->fetch() ?: null;
}

function all(string $sql, array $args = []): array
{
    return q($sql, $args)->fetchAll();
}

function transaction(callable $fn)
{
    $db = db();
    if ($db->inTransaction()) return $fn();
    $db->beginTransaction();
    try {
        $r = $fn();
        $db->commit();
        return $r;
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
}

function now_utc(int $plus = 0): string
{
    return gmdate('Y-m-d H:i:s', time() + $plus);
}

function ts(?string $utc): ?int
{
    return $utc ? strtotime($utc . ' UTC') : null;
}

function iso(?string $utc): ?string
{
    return $utc ? gmdate('Y-m-d\TH:i:s\Z', ts($utc)) : null;
}

// ---------- Crypto ----------

/** A key for one purpose, derived from $secret_key. */
function subkey(string $purpose): string
{
    return hash_hkdf('sha256', (string)cfg('secret'), 32, 'savaccount:' . $purpose);
}

function keyed_hash(string $purpose, string $value): string
{
    return hash_hmac('sha256', $value, subkey($purpose));
}

function b64u(string $bin): string
{
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

function new_token(): string
{
    return b64u(random_bytes(32));
}

/** Tokens are stored only as SHA-256: they have 256 random bits, a slow hash would add nothing. */
function token_hash(string $token): string
{
    return hash('sha256', $token);
}

function looks_like_token($v): bool
{
    return is_string($v) && preg_match('/^[A-Za-z0-9_-]{43}$/', $v) === 1;
}

/** AES-256-GCM. $context binds the ciphertext to its place (a user, a key id): it cannot be moved to another row. */
function encrypt(string $plain, string $context): string
{
    $iv = random_bytes(12);
    $tag = '';
    $ct = openssl_encrypt($plain, 'aes-256-gcm', subkey('encryption'), OPENSSL_RAW_DATA, $iv, $tag, $context);
    if ($ct === false) throw new RuntimeException('encryption failed');
    return base64_encode($iv . $tag . $ct);
}

function decrypt(string $blob, string $context): string
{
    $raw = base64_decode($blob, true);
    if ($raw === false || strlen($raw) < 29) throw new RuntimeException('decryption failed');
    $p = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', subkey('encryption'), OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16), $context);
    if ($p === false) throw new RuntimeException('decryption failed');
    return $p;
}

/** The password mixed with a key that is not in the database (also removes bcrypt's 72-byte limit). */
function peppered(string $password): string
{
    return base64_encode(hash_hmac('sha384', $password, subkey('password'), true));
}

function password_algo(): array
{
    return defined('PASSWORD_ARGON2ID')
        ? [PASSWORD_ARGON2ID, ['memory_cost' => 65536, 'time_cost' => 3, 'threads' => 1]]
        : [PASSWORD_BCRYPT, ['cost' => 12]];
}

function hash_password(string $password): string
{
    [$algo, $opt] = password_algo();
    return (string)password_hash(peppered($password), $algo, $opt);
}

/** Same time whether the account exists or not. */
function check_password(?string $hash, string $password): bool
{
    if ($hash === null || $hash === '') {
        hash_password($password);
        return false;
    }
    return password_verify(peppered($password), $hash);
}

// ---------- Request and response ----------

function client_ip(): string
{
    $ip = (string)($_SERVER['HTTP_X_SAV_CLIENT_IP'] ?? ''); // set by the gateway, which has proved its key
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/** IPv6 clients own a whole /64: rate limits count the network, not the address. */
function ip_subject(): string
{
    $bin = inet_pton(client_ip());
    return strlen((string)$bin) === 16 ? bin2hex(substr($bin, 0, 8)) . '/64' : client_ip();
}

/** What the security log shows: the address without its last part. */
function ip_short(): string
{
    $bin = (string)inet_pton(client_ip());
    if (strlen($bin) === 4) return preg_replace('/\.\d+$/', '.x', client_ip());
    return strlen($bin) === 16 ? inet_ntop(substr($bin, 0, 6) . str_repeat("\0", 10)) : '';
}

function user_agent(): string
{
    return mb_substr(preg_replace('/[^\x20-\x7E]/', '', (string)($_SERVER['HTTP_X_SAV_USER_AGENT'] ?? '')) ?? '', 0, 250);
}

/** "Firefox · Windows": enough to recognise a device, nothing more is kept. */
function device_label(): string
{
    $ua = user_agent();
    $browser = 'Browser';
    foreach (['Edg' => 'Edge', 'OPR/' => 'Opera', 'Firefox' => 'Firefox', 'FxiOS' => 'Firefox', 'CriOS' => 'Chrome', 'Chrome' => 'Chrome', 'Safari' => 'Safari', 'curl' => 'curl'] as $k => $n) {
        if (stripos($ua, $k) !== false) { $browser = $n; break; }
    }
    $os = '';
    foreach (['iPhone' => 'iPhone', 'iPad' => 'iPad', 'Android' => 'Android', 'Windows' => 'Windows', 'CrOS' => 'ChromeOS', 'Mac OS' => 'macOS', 'Linux' => 'Linux'] as $k => $n) {
        if (stripos($ua, $k) !== false) { $os = $n; break; }
    }
    return $os === '' ? $browser : "$browser · $os";
}

function send_json($data, int $code = 200): void
{
    $out = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    if (!headers_sent_cache()) header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    if (!empty($GLOBALS['sva_mail_queue'])) {
        // the answer leaves before the emails do: how long it takes must not tell whether an email was sent
        while (ob_get_level()) ob_end_clean();
        ignore_user_abort(true);
        header('Connection: close');
        header('Content-Length: ' . strlen($out));
        echo $out;
        flush();
        if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
        exit;
    }
    echo $out;
    exit;
}

function headers_sent_cache(): bool
{
    foreach (headers_list() as $h) if (stripos($h, 'cache-control:') === 0) return true;
    return false;
}

function fail(string $error, int $code = 400, array $more = []): void
{
    send_json(['ok' => false, 'error' => $error] + $more, $code);
}

function ok(array $data = []): void
{
    send_json(['ok' => true] + $data);
}

function body(): array
{
    static $b = null;
    if ($b !== null) return $b;
    $raw = (string)file_get_contents('php://input', false, null, 0, 65537);
    if ($raw === '') return $b = [];
    if (strlen($raw) > 65536) fail('too_large', 413);
    $d = json_decode($raw, true, 16);
    if (!is_array($d)) fail('bad_request');
    return $b = $d;
}

/** A trimmed, single-line string of the request. */
function s(string $key, int $max = 255, ?array $src = null): string
{
    $v = ($src ?? body())[$key] ?? '';
    if (!is_string($v) || !mb_check_encoding($v, 'UTF-8')) return '';
    // control characters and the invisible ones that make a text look like another (bidi overrides, zero width)
    $v = preg_replace('/[\x00-\x1F\x7F\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}-\x{2069}\x{FEFF}]/u', '', $v) ?? '';
    return mb_substr(trim($v), 0, $max);
}

/** Passwords are taken as they are. */
function raw(string $key): string
{
    $v = body()[$key] ?? '';
    return is_string($v) && mb_check_encoding($v, 'UTF-8') && strlen($v) <= 1024 ? $v : '';
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function norm_email(string $email): string
{
    return mb_strtolower(trim($email));
}

function email_field(string $key = 'email'): string
{
    $email = norm_email(s($key, 191));
    if (strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) fail('email_invalid', 422);
    return $email;
}

function locale(): string
{
    return s('locale', 2) === 'it' || ($_SERVER['HTTP_X_SAV_LOCALE'] ?? '') === 'it' ? 'it' : 'en';
}

// ---------- Gateway ----------

/** Only savaccount.eu may call this API. */
function require_gateway(): void
{
    $sent = (string)($_SERVER['HTTP_X_SAV_GATEWAY'] ?? '');
    if ($sent === '' || !hash_equals(hash('sha256', (string)cfg('gateway')), hash('sha256', $sent))) fail('forbidden', 403);
}

// ---------- Rate limits (subjects are stored as keyed hashes: no IP address or email in clear) ----------

function rate_count(string $kind, string $subject, int $minutes): int
{
    return (int)q('SELECT COUNT(*) FROM ' . t('attempts') . ' WHERE kind = ? AND subject = ? AND created_at > ?',
        [$kind, keyed_hash('rate', $subject), now_utc(-$minutes * 60)])->fetchColumn();
}

function rate_hit(string $kind, string $subject): void
{
    q('INSERT INTO ' . t('attempts') . ' (kind, subject, created_at) VALUES (?,?,?)', [$kind, keyed_hash('rate', $subject), now_utc()]);
}

function rate_clear(string $kind, string $subject): void
{
    q('DELETE FROM ' . t('attempts') . ' WHERE kind = ? AND subject = ?', [$kind, keyed_hash('rate', $subject)]);
}

/** Takes back the last hit (the attempt turned out to be right). */
function rate_undo(string $kind, string $subject): void
{
    q('DELETE FROM ' . t('attempts') . ' WHERE kind = ? AND subject = ? ORDER BY id DESC LIMIT 1', [$kind, keyed_hash('rate', $subject)]);
}

/** Refuses when there are already $max hits, otherwise counts this one. */
function rate_limit(string $kind, string $subject, int $max, int $minutes): void
{
    if (rate_count($kind, $subject, $minutes) >= $max) fail('too_many', 429, ['retry' => $minutes]);
    rate_hit($kind, $subject);
}

// ---------- Sessions ----------

function user_row(int $id): ?array
{
    $u = one('SELECT * FROM ' . t('users') . ' WHERE id = ?', [$id]);
    if ($u) $u['id'] = (int)$u['id'];
    return $u;
}

/** Creates the session; the gateway turns X-Sav-Set-Token into a HttpOnly cookie of savaccount.eu. */
function create_session(int $userId, array $amr, bool $remember): array
{
    $token = new_token();
    $csrf = new_token();
    $life = $remember ? SESSION_DAYS * 86400 : SESSION_HOURS * 3600;
    q('INSERT INTO ' . t('sessions') . ' (token_hash, user_id, csrf, device, ip, amr, remember, auth_time, sudo_until, created_at, last_seen_at, expires_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
        [token_hash($token), $userId, $csrf, device_label(), ip_short(), implode(' ', $amr), $remember ? 1 : 0, now_utc(), now_utc(SUDO_MINUTES * 60), now_utc(), now_utc(), now_utc($life)]);
    header('X-Sav-Set-Token: ' . $token . ';' . ($remember ? $life : 0));
    q('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?', [now_utc(), $userId]);
    $GLOBALS['sva_session'] = null;
    return ['csrf' => $csrf];
}

/** The signed-in session joined with its user, or null. */
function session(): ?array
{
    if (isset($GLOBALS['sva_session'])) return $GLOBALS['sva_session'];
    $token = $_SERVER['HTTP_X_SAV_TOKEN'] ?? '';
    if (!looks_like_token($token)) return null;
    $s = one('SELECT s.* FROM ' . t('sessions') . ' s JOIN ' . t('users') . " u ON u.id = s.user_id
        WHERE s.token_hash = ? AND s.expires_at > ? AND u.status = 'active'", [token_hash($token), now_utc()]);
    if (!$s) return null;
    if (ts($s['last_seen_at']) < time() - 300) q('UPDATE ' . t('sessions') . ' SET last_seen_at = ?, ip = ? WHERE id = ?', [now_utc(), ip_short(), $s['id']]);
    $s['id'] = (int)$s['id'];
    $s['user'] = user_row((int)$s['user_id']);
    return $GLOBALS['sva_session'] = $s;
}

function require_session(): array
{
    $s = session();
    if (!$s) fail('unauthenticated', 401);
    return $s;
}

/** A signed-in POST: session and CSRF token. */
function writer(): array
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail('bad_method', 405);
    $s = require_session();
    if (!hash_equals($s['csrf'], (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''))) fail('csrf', 403);
    return $s;
}

/** Sensitive actions: the password was confirmed in the last minutes. */
function sudo_writer(): array
{
    $s = writer();
    if (!$s['sudo_until'] || ts($s['sudo_until']) < time()) fail('sudo_required', 403);
    return $s;
}

function end_sessions(int $userId, ?int $exceptId = null): void
{
    q('DELETE FROM ' . t('sessions') . ' WHERE user_id = ? AND id <> ?', [$userId, $exceptId ?? 0]);
}

/** Admin of the services: a verified address listed in credentials.php. Two-step verification is mandatory. */
function is_master(array $user): bool
{
    $masters = (array)cfg('masters');
    if (!$masters) return false;
    foreach (all('SELECT email FROM ' . t('emails') . ' WHERE user_id = ? AND verified_at IS NOT NULL', [$user['id']]) as $r) {
        if (in_array($r['email'], $masters, true)) return true;
    }
    return false;
}

function has_2fa(array $user): bool
{
    return !empty($user['totp_enabled_at']) || !empty($user['email_2fa']);
}

function require_master(array $session): array
{
    if (!is_master($session['user'])) fail('forbidden', 403);
    if (!has_2fa($session['user']) || strpos((string)$session['amr'], 'mfa') === false) fail('mfa_required', 403);
    return $session;
}

// ---------- Security log ----------

function log_event(int $userId, string $kind, string $detail = ''): void
{
    q('INSERT INTO ' . t('events') . ' (user_id, kind, detail, device, ip, created_at) VALUES (?,?,?,?,?,?)',
        [$userId, $kind, mb_substr($detail, 0, 190), device_label(), ip_short(), now_utc()]);
}

/** Now and then, deletes what must not be kept (see the privacy page). */
function housekeeping(): void
{
    if (random_int(1, 40) !== 1) return;
    q('DELETE FROM ' . t('attempts') . ' WHERE created_at < ?', [now_utc(-86400)]);
    q('DELETE FROM ' . t('challenges') . ' WHERE expires_at < ?', [now_utc()]);
    q('DELETE FROM ' . t('sessions') . ' WHERE expires_at < ?', [now_utc()]);
    q('DELETE FROM ' . t('codes') . ' WHERE expires_at < ?', [now_utc(-86400)]);
    q('DELETE FROM ' . t('tokens') . ' WHERE expires_at < ?', [now_utc()]);
    q('DELETE FROM ' . t('events') . ' WHERE created_at < ?', [now_utc(-EVENT_DAYS * 86400)]);
    q('DELETE FROM ' . t('users') . " WHERE status = 'pending' AND created_at < ?", [now_utc(-PENDING_HOURS * 3600)]);
    q('DELETE FROM ' . t('emails') . ' WHERE verified_at IS NULL AND created_at < ?', [now_utc(-PENDING_HOURS * 3600)]);
}
