<?php
/*
 * Daily clean-up of the online games (Connect Four, Chess, FaceGuess): see README.md.
 * Deletes the rooms with no changes and no connected player for ROOM_RETENTION_HOURS;
 * players, chat and cards go with them through the foreign keys.
 *
 * URL:          https://www.saveriomorelli.com/projects/cron/cleanup.php?key=SECRET_KEY
 * Command line: php cleanup.php (no key needed)
 */

const ROOM_RETENTION_HOURS = 24;
const GAMES = ['c4' => 'connect-four', 'chess' => 'chess', 'fg' => 'faceguess']; // table prefix => project

header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

function finish($status, $body)
{
    http_response_code($status);
    echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
    exit($status === 200 ? 0 : 1);
}

if (PHP_SAPI !== 'cli') {
    $credentials = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
    $secret = (string)($credentials['cron_key'] ?? '');
    if (strlen($secret) < 32) finish(503, ['error' => 'cron_key is not configured']);
    if (!hash_equals($secret, (string)($_GET['key'] ?? ''))) finish(403, ['error' => 'forbidden']);
}

// The three games share one database: any of their connections reaches all the tables
require __DIR__ . '/../connect-four/api/db.php';

$age = 'NOW() - INTERVAL ' . ROOM_RETENTION_HOURS . ' HOUR';
$deleted = [];
$failed = false;
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

finish($failed ? 500 : 200, ['ok' => !$failed, 'deleted_rooms' => $deleted]);
