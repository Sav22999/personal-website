<?php
/*
 * Daily clean-up: see README.md.
 * Games (Connect Four, Chess, FaceGuess): deletes the rooms with no changes and no connected player
 * for ROOM_RETENTION_HOURS; players, chat and cards go with them through the foreign keys.
 * Apps (FoodTrack, Giftwise, Fischietto): deletes expired sessions and old sign-in attempts and blanks the IP and browser
 * still stored on sessions from older versions. It never touches the users' own data.
 *
 * URL:          https://www.saveriomorelli.com/projects/cron/cleanup.php?key=SECRET_KEY
 * Command line: php cleanup.php (no key needed)
 */

const ROOM_RETENTION_HOURS = 24;
const GAMES = ['c4' => 'connect-four', 'chess' => 'chess', 'fg' => 'faceguess']; // table prefix => project
const APPS = ['foodtrack' => 'ft_', 'giftwise' => 'gw_']; // project => default table prefix
const ATTEMPT_RETENTION_HOURS = 2; // the longest rate limit looks back 60 minutes: older IPs serve no purpose

header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

function finish($status, $body)
{
    http_response_code($status);
    echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
    exit($status === 200 ? 0 : 1);
}

function connectTo($host, $port, $name, $user, $pass)
{
    return new PDO("mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
    ]);
}

function cleanTables($db, $prefix, $attemptsTable)
{
    return [
        'sessions_anonymised' => $db->exec("UPDATE `{$prefix}auth_tokens` SET ip = '', user_agent = '' WHERE ip <> '' OR user_agent <> ''"),
        'sessions' => $db->exec("DELETE FROM `{$prefix}auth_tokens` WHERE expires_at < UTC_TIMESTAMP()"),
        'attempts' => $db->exec("DELETE FROM `{$prefix}{$attemptsTable}` WHERE created_at < UTC_TIMESTAMP() - INTERVAL " . ATTEMPT_RETENTION_HOURS . " HOUR"),
    ];
}

function validPrefix($prefix, $default)
{
    return preg_match('/^[A-Za-z0-9_]{0,20}$/', (string)$prefix) ? $prefix : $default;
}

// Each app has its own credentials.php: its variables stay inside this function
function cleanApp($project, $defaultPrefix)
{
    $file = __DIR__ . "/../$project/credentials.php";
    if (!is_file($file)) throw new RuntimeException("$project/credentials.php not found");
    $table_prefix = $defaultPrefix;
    require $file;
    $host = (string)$localhost_db;
    $port = 3306;
    if (preg_match('/^(.+):(\d+)$/', $host, $m)) {
        $host = $m[1];
        $port = (int)$m[2];
    }
    return cleanTables(connectTo($host, $port, $database_name, $username_db, $password_db), validPrefix($table_prefix ?: $defaultPrefix, $defaultPrefix), 'attempts');
}

// Fischietto keeps its settings in a different file and format
function cleanFischietto()
{
    $file = __DIR__ . '/../ripasso-fipav/lib/config.php';
    if (!is_file($file)) throw new RuntimeException('ripasso-fipav/lib/config.php not found');
    $config = require $file;
    $c = $config['db'];
    $db = connectTo($c['host'], (int)($c['port'] ?? 3306), $c['name'], $c['user'], $c['pass']);
    return cleanTables($db, validPrefix($config['table_prefix'] ?? 'fr_', 'fr_'), 'login_attempts');
}

if (PHP_SAPI !== 'cli') {
    $credentials = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
    $secret = (string)($credentials['cron_key'] ?? '');
    if (strlen($secret) < 32) finish(503, ['error' => 'cron_key is not configured']);
    if (!hash_equals($secret, (string)($_GET['key'] ?? ''))) finish(403, ['error' => 'forbidden']);
}

$failed = false;

// Apps first: db.php below stops the script when the games' database is down
$expired = [];
foreach (array_merge(array_keys(APPS), ['fischietto']) as $project) {
    try {
        $expired[$project] = $project === 'fischietto' ? cleanFischietto() : cleanApp($project, APPS[$project]);
    } catch (Throwable $e) {
        error_log("Clean-up of $project failed: " . $e->getMessage());
        $expired[$project] = 'failed: ' . ($e instanceof PDOException ? 'SQLSTATE ' . $e->getCode() : 'credentials not found');
        $failed = true;
    }
}

// The three games share one database: any of their connections reaches all the tables
require __DIR__ . '/../connect-four/api/db.php';

$age = 'NOW() - INTERVAL ' . ROOM_RETENTION_HOURS . ' HOUR';
$deleted = [];
foreach (GAMES as $prefix => $game) {
    try {
        $deleted[$game] = $pdo->exec("DELETE r FROM {$prefix}_rooms r WHERE r.updated_at < $age
            AND NOT EXISTS (SELECT 1 FROM {$prefix}_players p WHERE p.room_id = r.id AND p.last_seen >= $age)");
    } catch (PDOException $e) {
        error_log("Clean-up of $game failed: " . $e->getMessage());
        $deleted[$game] = 'failed: SQLSTATE ' . $e->getCode(); // 42S02 = missing table: run the game's api/setup.php
        $failed = true;
    }
}

finish($failed ? 500 : 200, ['ok' => !$failed, 'deleted_rooms' => $deleted, 'deleted_expired' => $expired]);
