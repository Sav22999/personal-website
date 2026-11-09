<?php
/*
 * ThinkHop reminders: see README.md. Run hourly (e.g. 0 * * * *) or once a day in the afternoon.
 * Players who have a streak and have not played today are reminded about their streak, everyone else
 * about the daily challenge: at most one notification per player per day, delivered according to each
 * user's chosen reminder hour in server timezone (Europe/Rome).
 *
 * URL:          https://www.saveriomorelli.com/projects/cron/thinkhop-reminders.php?key=SECRET_KEY
 * Command line: php thinkhop-reminders.php (no key needed, use --force to trigger immediately)
 */

require __DIR__ . '/../thinkhop/lib/bootstrap.php';
require __DIR__ . '/../thinkhop/lib/push.php';

header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

function finish_reminders(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
    exit($status === 200 ? 0 : 1);
}

if (PHP_SAPI !== 'cli') {
    $cron = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
    $secret = (string)($cron['cron_key'] ?? '');
    if (strlen($secret) < 32) finish_reminders(503, ['error' => 'cron_key is not configured']);
    if (!hash_equals($secret, (string)($_GET['key'] ?? ''))) finish_reminders(403, ['error' => 'forbidden']);
}

/** Days in a row of daily challenges, ending yesterday. */
function open_streak(int $userId, string $today): int
{
    $st = db()->prepare('SELECT DISTINCT day FROM ' . t('games') . " WHERE user_id = ? AND mode = 'daily' AND day < ? ORDER BY day DESC LIMIT 400");
    $st->execute([$userId, $today]);
    $streak = 0;
    $expected = strtotime($today . ' 12:00:00 UTC') - 86400;
    foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $day) {
        if ($day !== gmdate('Y-m-d', $expected)) break;
        $streak++;
        $expected -= 86400;
    }
    return $streak;
}

try {
    $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Rome'));
    $today = $now->format('Y-m-d');
    $currentHour = (int)$now->format('G');
    $force = isset($_GET['force']) || in_array('--force', $argv ?? [], true);

    db()->prepare('DELETE FROM ' . t('notifications') . ' WHERE created_at < ?')->execute([gmdate('Y-m-d H:i:s', time() - 3 * 86400)]);
    $st = db()->prepare('SELECT DISTINCT s.user_id, u.notify_off, u.notify_hours FROM ' . t('push_subscriptions') . ' s JOIN ' . t('users') . " u ON u.id = s.user_id
        WHERE u.status = 'active' AND NOT EXISTS (SELECT 1 FROM " . t('games') . " g WHERE g.user_id = s.user_id AND g.mode = 'daily' AND g.day = ?)");
    $st->execute([$today]);
    $sent = ['streak' => 0, 'daily' => 0];
    foreach ($st->fetchAll() as $u) {
        $id = (int)$u['user_id'];
        $streak = open_streak($id, $today);
        $off = notify_off($u);
        $hours = notify_hours($u);

        // One reminder a day at most, chosen first: a streak of one day is not worth a warning
        $type = $streak >= 2 && !in_array('streak', $off, true) ? 'streak' : 'daily';
        if (in_array($type, $off, true)) continue;

        $targetHour = $hours[$type] ?? 18;
        // Unless forced, only send if the current server hour has reached or passed the target hour
        if (!$force && $currentHour < $targetHour) {
            continue;
        }

        if (notify_user($id, $type, $today, '', $type === 'streak' ? ['n' => $streak] : [])) $sent[$type]++;
    }
    finish_reminders(200, ['ok' => true, 'sent' => $sent, 'server_hour' => $currentHour]);
} catch (Throwable $e) {
    error_log('ThinkHop reminders failed: ' . $e->getMessage());
    finish_reminders(500, ['ok' => false, 'error' => 'failed: ' . ($e instanceof PDOException ? 'SQLSTATE ' . $e->getCode() : 'see the log')]);
}
