<?php
/*
 * Daily clean-up: see README.md.
 * Games (Connect Four, Chess, FaceGuess, Sea Battle, Prism Mine): deletes the rooms with no changes and no connected player
 * for ROOM_RETENTION_HOURS; players, chat and cards go with them through the foreign keys.
 * Apps (FoodTrack, Giftwise, Fucina, ThinkHop, Forkville, MyCurriculum, OpenNewsletter, Fischietto, Rendicontazione Ore):
 * deletes expired sessions and old sign-in attempts and blanks the IP and browser still stored on
 * sessions from older versions. It never touches the users' own data.
 * OpenNewsletter also deletes the subscriptions never confirmed within PENDING_SUBSCRIBER_DAYS (as its terms promise)
 * and the expired email-confirmation links of accounts.
 * SavAccount cleans itself on savaccount.eu: here its clean-up is only started.
 *
 * URL:          https://www.saveriomorelli.com/projects/cron/cleanup.php?key=SECRET_KEY
 * Command line: php cleanup.php (no key needed)
 */

const ROOM_RETENTION_HOURS = 24;
const GAMES = ['c4' => 'connect-four', 'chess' => 'chess', 'fg' => 'faceguess', 'sb' => 'sea-battle', 'pm' => 'prism-mine']; // table prefix => project
const APPS = ['foodtrack' => 'ft_', 'giftwise' => 'gw_', 'fucina' => 'fc_', 'thinkhop' => 'th_', 'forkville' => 'fv_', 'mycurriculum' => 'mc_', 'opennewsletter' => 'on_', 'savcomponents' => 'sc_', 'dad/rendicontazione' => 'dad_rend_']; // project => default table prefix
const NO_SESSION_DETAILS = ['thinkhop', 'forkville', 'mycurriculum', 'opennewsletter', 'savcomponents']; // these apps never stored the IP and browser on their sessions: no such columns
const NO_ATTEMPTS = ['dad/rendicontazione']; // Google-only sign-in, no rate-limit attempts table
const ATTEMPT_RETENTION_HOURS = 2; // the longest rate limit looks back 60 minutes: older IPs serve no purpose
const PENDING_SUBSCRIBER_DAYS = 7; // OpenNewsletter: a confirmation link works 7 days, then the address is forgotten
const UNCONFIRMED_ACCOUNT_DAYS = 7; // SavComponents: an account never confirmed (the link works 48 hours) is removed after 7 days

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

function cleanTables($db, $prefix, $attemptsTable, $anonymise = true)
{
    return [
        'sessions_anonymised' => $anonymise ? $db->exec("UPDATE `{$prefix}auth_tokens` SET ip = '', user_agent = '' WHERE ip <> '' OR user_agent <> ''") : 0,
        'sessions' => $db->exec("DELETE FROM `{$prefix}auth_tokens` WHERE expires_at < UTC_TIMESTAMP()"),
        'attempts' => $attemptsTable !== null ? $db->exec("DELETE FROM `{$prefix}{$attemptsTable}` WHERE created_at < UTC_TIMESTAMP() - INTERVAL " . ATTEMPT_RETENTION_HOURS . " HOUR") : 0,
    ];
}

// SavAccount has its own server and settings: it is asked to clean itself, with a key that only opens that
function cleanSavAccount()
{
    $credentials = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
    $key = (string)($credentials['savaccount_key'] ?? '');
    if ($key === '' || !function_exists('curl_init')) throw new RuntimeException('savaccount_key is not configured');
    $ch = curl_init('https://www.savaccount.eu/cron/cleanup');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 60, CURLOPT_HTTPHEADER => ['X-Cron-Key: ' . $key]]);
    $answer = json_decode((string)curl_exec($ch), true);
    if (!is_array($answer) || empty($answer['ok'])) throw new RuntimeException('savaccount.eu answered ' . (int)curl_getinfo($ch, CURLINFO_HTTP_CODE));
    return $answer['deleted'];
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
    $prefix = validPrefix($table_prefix ?: $defaultPrefix, $defaultPrefix);
    $attempts = in_array($project, NO_ATTEMPTS, true) ? null : 'attempts';
    $db = connectTo($host, $port, $database_name, $username_db, $password_db);
    $result = cleanTables($db, $prefix, $attempts, !in_array($project, NO_SESSION_DETAILS, true));
    if ($project === 'savcomponents') {
        $result['email_links'] = $db->exec("DELETE FROM `{$prefix}email_tokens` WHERE expires_at < UTC_TIMESTAMP()");
        $result['unconfirmed_accounts'] = $db->exec("DELETE FROM `{$prefix}users` WHERE email_verified_at IS NULL AND created_at < UTC_TIMESTAMP() - INTERVAL " . UNCONFIRMED_ACCOUNT_DAYS . " DAY");
    }
    if ($project === 'opennewsletter') {
        $result['pending_subscribers'] = $db->exec("DELETE FROM `{$prefix}subscribers` WHERE status = 'pending' AND COALESCE(confirm_sent_at, created_at) < UTC_TIMESTAMP() - INTERVAL " . PENDING_SUBSCRIBER_DAYS . " DAY");
        $result['email_links'] = $db->exec("DELETE FROM `{$prefix}email_tokens` WHERE expires_at < UTC_TIMESTAMP()");
    }
    return $result;
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

// The projects moved to www.illerom.eu are cleaned there: here only the others
require __DIR__ . '/illerom.php';
$moved = illerom_cron('cleanup.php');
const STAY = ['savcomponents', 'dad/rendicontazione', 'fischietto'];

$failed = $moved !== null && $moved[0] !== 200;

// Apps first: db.php below stops the script when the games' database is down
$expired = [];
foreach (array_merge(array_keys(APPS), ['fischietto']) as $project) {
    if ($moved !== null && !in_array($project, STAY, true)) continue;
    try {
        $expired[$project] = $project === 'fischietto' ? cleanFischietto() : cleanApp($project, APPS[$project]);
    } catch (Throwable $e) {
        error_log("Clean-up of $project failed: " . $e->getMessage());
        $expired[$project] = 'failed: ' . ($e instanceof PDOException ? 'SQLSTATE ' . $e->getCode() : 'credentials not found');
        $failed = true;
    }
}

try {
    $expired['savaccount'] = cleanSavAccount();
} catch (Throwable $e) {
    error_log('Clean-up of savaccount failed: ' . $e->getMessage());
    $expired['savaccount'] = 'failed: no valid answer from savaccount.eu';
    $failed = true;
}

if ($moved !== null) finish($failed ? 500 : 200, ['ok' => !$failed, 'deleted_expired' => $expired, 'illerom' => $moved[1]]);

// The games share one database: any of their connections reaches all the tables
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
