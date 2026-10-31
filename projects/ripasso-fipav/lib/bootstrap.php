<?php
/**
 * Bootstrap shared by the API and the installer.
 * Compatible with PHP 7.4+ and MySQL 5.7+/MariaDB 10.3+.
 */

declare(strict_types=1);

const PERM_NONE   = 0;   // no access (pending or revoked account)
const PERM_USER   = 1;   // can use the portal
const PERM_EDITOR = 5;   // content moderator: questions, references, reports
const PERM_ADMIN  = 9;   // administrator: + manages users and moderators
const PERM_MASTER = 10;  // master admin (only one): + manages administrators

const PERMISSION_LEVELS = [
    PERM_NONE   => 'Nessun accesso',
    PERM_USER   => 'Utente',
    PERM_EDITOR => 'Moderatore contenuti',
    PERM_ADMIN  => 'Amministratore',
    PERM_MASTER => 'Master admin',
];

/** Levels that can be assigned from the panel (the master role is only handed over through the transfer). */
const ASSIGNABLE_LEVELS = [PERM_USER, PERM_EDITOR, PERM_ADMIN];

const USER_STATUSES = ['pending', 'active', 'disabled'];
const SOURCES = ['Regole di gioco', 'Casistica', 'Indicazioni tecniche', 'Normative federali', 'Vademecum'];
const QUESTION_TYPES = ['multiple-choice', 'yes-no', 'multi-select'];
const DIFFICULTIES = ['e', 'm', 'd', 'dd'];
const STATE_KEYS = ['data', 'suspended', 'autosave'];

const AUTH_COOKIE = 'fipav_auth';

function config(): array
{
    static $cfg = null;
    if ($cfg === null) {
        // FIPAV_CONFIG points to a different file (local testing only)
        $file = getenv('FIPAV_CONFIG') ?: __DIR__ . '/config.php';
        if (!is_file($file)) {
            throw new RuntimeException('Manca lib/config.php: copia lib/config.example.php e inserisci i dati del database.');
        }
        $cfg = require $file;
    }
    return $cfg;
}

function t(string $name): string
{
    $prefix = (string)(config()['table_prefix'] ?? 'fr_');
    if (!preg_match('/^[A-Za-z0-9_]*$/', $prefix)) {
        throw new RuntimeException('table_prefix non valido');
    }
    return '`' . $prefix . $name . '`';
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = config()['db'];
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $c['host'], (int)($c['port'] ?? 3306), $c['name'], $c['charset'] ?? 'utf8mb4'
        );
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("SET time_zone = '+00:00'");
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
    if (strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') return true;
    return false;
}

function client_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

/** Path of the portal folder (e.g. "/projects/ripasso-fipav/"), used for the cookie. */
function app_base_path(): string
{
    $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/'));
    $dir = dirname($script);
    if (basename($dir) === 'api') $dir = dirname($dir);
    $dir = rtrim($dir, '/');
    return $dir === '' ? '/' : $dir . '/';
}

// ---------- JSON responses ----------

function send_json($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fail(string $message, int $code = 400, string $errorCode = 'error', array $extra = []): void
{
    send_json(array_merge(['ok' => false, 'error' => $errorCode, 'message' => $message], $extra), $code);
}

function request_body(): array
{
    static $body = null;
    if ($body !== null) return $body;
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) return $body = [];
    $data = json_decode($raw, true);
    if (!is_array($data)) fail('Richiesta non valida (JSON atteso).', 400, 'bad_json');
    return $body = $data;
}

function str_param(array $src, string $key, int $maxLen = 0, bool $required = false): string
{
    $v = $src[$key] ?? '';
    if (!is_string($v) && !is_numeric($v)) $v = '';
    $v = trim((string)$v);
    if ($required && $v === '') fail("Il campo \"$key\" è obbligatorio.", 422, 'validation');
    if ($maxLen > 0 && mb_strlen($v) > $maxLen) fail("Il campo \"$key\" è troppo lungo (max $maxLen caratteri).", 422, 'validation');
    return $v;
}

// ---------- Authentication (token in an HttpOnly cookie, hash in the database) ----------

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

function create_auth_session(int $userId): array
{
    $token = bin2hex(random_bytes(32));
    $csrf  = bin2hex(random_bytes(24));
    $lifetime = (int)(config()['session_lifetime'] ?? 2592000);
    $expires = time() + $lifetime;
    $st = db()->prepare('INSERT INTO ' . t('auth_tokens') . ' (token_hash, user_id, csrf_token, user_agent, ip, created_at, last_seen_at, expires_at) VALUES (?,?,?,?,?,?,?,?)');
    $st->execute([
        hash('sha256', $token), $userId, $csrf,
        substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255), client_ip(),
        now_utc(), now_utc(), gmdate('Y-m-d H:i:s', $expires),
    ]);
    set_auth_cookie($token, $expires);
    // Occasional cleanup of expired tokens
    if (random_int(1, 50) === 1) {
        db()->exec('DELETE FROM ' . t('auth_tokens') . " WHERE expires_at < UTC_TIMESTAMP()");
    }
    return ['csrf' => $csrf];
}

function destroy_auth_session(): void
{
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if (is_string($token) && $token !== '') {
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE token_hash = ?')->execute([hash('sha256', $token)]);
    }
    set_auth_cookie('', time() - 3600);
}

/** Returns the signed-in user (re-read from the database on every request) or null. */
function current_session(): ?array
{
    static $cached = false;
    if ($cached !== false) return $cached;
    ensure_schema();
    $token = $_COOKIE[AUTH_COOKIE] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) return $cached = null;
    $st = db()->prepare(
        'SELECT u.id, u.name, u.email, u.permissions, u.status, u.must_change_password, u.created_at, a.csrf_token, a.last_seen_at, a.token_hash
         FROM ' . t('auth_tokens') . ' a JOIN ' . t('users') . ' u ON u.id = a.user_id
         WHERE a.token_hash = ? AND a.expires_at > UTC_TIMESTAMP()'
    );
    $st->execute([hash('sha256', $token)]);
    $row = $st->fetch();
    if (!$row) return $cached = null;
    // Update "last seen" at most every 5 minutes
    if (strtotime($row['last_seen_at'] . ' UTC') < time() - 300) {
        db()->prepare('UPDATE ' . t('auth_tokens') . ' SET last_seen_at = ? WHERE token_hash = ?')->execute([now_utc(), $row['token_hash']]);
        db()->prepare('UPDATE ' . t('users') . ' SET last_seen_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
    }
    $row['id'] = (int)$row['id'];
    $row['permissions'] = (int)$row['permissions'];
    $row['must_change_password'] = (int)$row['must_change_password'];
    unset($row['token_hash']);
    return $cached = $row;
}

function public_user(array $u): array
{
    $p = (int)$u['permissions'];
    $can = user_can_use($u);
    return [
        'id'             => (int)$u['id'],
        'name'           => $u['name'],
        'email'          => $u['email'],
        'permissions'    => $p,
        'status'         => $u['status'],
        'role'           => role_label($p),
        'canUse'         => $can,
        'canEdit'        => $can && $p >= PERM_EDITOR,
        'canManageUsers' => $can && $p >= PERM_ADMIN,
        'isMaster'       => $can && $p >= PERM_MASTER,
        'mustChangePassword' => !empty($u['must_change_password']),
        'createdAt'      => $u['created_at'] ?? null,
        'gravatar'       => gravatar_hash((string)$u['email']),
    ];
}

/** Gravatar hash: SHA-256 of the normalized email (the email itself is never sent in clear text). */
function gravatar_hash(string $email): string
{
    return hash('sha256', mb_strtolower(trim($email)));
}

function role_label(int $p): string
{
    $label = PERMISSION_LEVELS[PERM_NONE];
    foreach (PERMISSION_LEVELS as $level => $name) {
        if ($p >= $level) $label = $name;
    }
    return $label;
}

function user_can_use(array $u): bool
{
    return $u['status'] === 'active' && (int)$u['permissions'] >= PERM_USER;
}

/**
 * Stops the request if the user is not signed in, not enabled or below the required level.
 * Users who must change their password (after a reset) can only use actions allowed with $allowPasswordChange.
 */
function require_user(int $minPermission = PERM_USER, bool $allowPasswordChange = false): array
{
    $u = current_session();
    if (!$u) fail('Sessione scaduta: accedi di nuovo.', 401, 'unauthenticated');
    if ($u['status'] === 'pending') fail('Il tuo account è in attesa di abilitazione da parte di un amministratore.', 403, 'pending');
    if ($u['status'] === 'disabled') fail('Il tuo account è stato disabilitato.', 403, 'disabled');
    if ($u['permissions'] < max(PERM_USER, $minPermission)) fail('Non hai i permessi per questa operazione.', 403, 'forbidden');
    if (!$allowPasswordChange && !empty($u['must_change_password'])) {
        fail('Prima di continuare devi scegliere una nuova password.', 403, 'must_change_password');
    }
    return $u;
}

/** CSRF protection for every request that changes data. */
function require_csrf(array $session): void
{
    $sent = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($sent === '' || !hash_equals((string)$session['csrf_token'], $sent)) {
        fail('Token di sicurezza non valido: ricarica la pagina.', 403, 'csrf');
    }
}

// ---------- Automatic database updates ----------

/**
 * Applies the missing migrations (only once). Called on every authenticated request:
 * it costs one read from the meta table.
 */
function ensure_schema(): void
{
    static $done = false;
    if ($done) return;
    $done = true;
    try {
        $st = db()->prepare('SELECT v FROM ' . t('meta') . " WHERE k = 'schema_version'");
        $st->execute();
        $version = (int)$st->fetchColumn();
    } catch (Throwable $e) {
        return; // portal not installed yet
    }
    if ($version >= 5) return;

    $pdo = db();
    if ($version < 2) {
        // v2: mandatory password change after a reset
        $cols = $pdo->query('SHOW COLUMNS FROM ' . t('users') . " LIKE 'must_change_password'")->fetchAll();
        if (!$cols) {
            $pdo->exec('ALTER TABLE ' . t('users') . " ADD COLUMN `must_change_password` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
        }
        // v2: a single master admin (10); the other administrators become 9
        normalize_master_admin();
        $pdo->prepare('INSERT INTO ' . t('meta') . " (k, v) VALUES ('schema_version', '2') ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute();
    }
    if ($version < 3 && function_exists('documents_table_sql')) {
        // v3: "Other useful documents" managed from the panel
        $pdo->exec(documents_table_sql());
        if (function_exists('seed_documents')) seed_documents();
        $pdo->prepare('INSERT INTO ' . t('meta') . " (k, v) VALUES ('schema_version', '3') ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute();
        if (function_exists('bump_content_version')) bump_content_version();
    }
    if ($version < 4) {
        // v4: season and type of change for references (new or changed rules)
        $have = array_column($pdo->query('SHOW COLUMNS FROM ' . t('refs'))->fetchAll(), 'Field');
        if (!in_array('change_season', $have, true)) {
            $pdo->exec('ALTER TABLE ' . t('refs') . " ADD COLUMN `change_season` VARCHAR(7) NULL AFTER `full_text`, ADD COLUMN `change_type` ENUM('new','changed') NULL AFTER `change_season`, ADD COLUMN `change_note` VARCHAR(300) NOT NULL DEFAULT '' AFTER `change_type`");
        }
        if (function_exists('prefill_ref_changes')) prefill_ref_changes();
        $pdo->prepare('INSERT INTO ' . t('meta') . " (k, v) VALUES ('schema_version', '4') ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute();
        if (function_exists('bump_content_version')) bump_content_version();
    }
    if ($version < 5) {
        // v5: when the "account activated" email was sent (NULL = never: it can be sent from the panel)
        $have = array_column($pdo->query('SHOW COLUMNS FROM ' . t('users'))->fetchAll(), 'Field');
        if (!in_array('activation_mail_at', $have, true)) {
            $pdo->exec('ALTER TABLE ' . t('users') . " ADD COLUMN `activation_mail_at` DATETIME NULL AFTER `approved_by`");
        }
        $pdo->prepare('INSERT INTO ' . t('meta') . " (k, v) VALUES ('schema_version', '5') ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute();
    }
}

/** Makes sure there is exactly one master admin. */
function normalize_master_admin(): void
{
    $pdo = db();
    $rows = $pdo->query('SELECT id, permissions, status FROM ' . t('users') . " WHERE permissions >= " . PERM_ADMIN . " ORDER BY (status = 'active') DESC, id ASC")->fetchAll();
    $masters = array_values(array_filter($rows, function ($r) { return (int)$r['permissions'] >= PERM_MASTER; }));
    $keep = null;
    foreach ($masters as $r) { if ($r['status'] === 'active') { $keep = (int)$r['id']; break; } }
    if ($keep === null && $masters) $keep = (int)$masters[0]['id'];
    if ($keep === null) {
        foreach ($rows as $r) { if ($r['status'] === 'active') { $keep = (int)$r['id']; break; } }
    }
    if ($keep === null) return;
    $pdo->prepare('UPDATE ' . t('users') . ' SET permissions = ? WHERE permissions >= ? AND id <> ?')->execute([PERM_ADMIN, PERM_ADMIN, $keep]);
    $pdo->prepare('UPDATE ' . t('users') . ' SET permissions = ? WHERE id = ?')->execute([PERM_MASTER, $keep]);
}
