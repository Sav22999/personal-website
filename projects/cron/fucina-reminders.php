<?php
/**
 * Fucina reminders: see README.md. Run hourly (e.g. 0 * * * *) or once a day in the afternoon.
 * Each user receives at most one reminder per day, sent when the server hour reaches
 * the user's chosen reminder hour in server timezone (Europe/Rome).
 *
 * URL:          https://www.saveriomorelli.com/projects/cron/fucina-reminders.php?key=SECRET_KEY
 * Command line: php fucina-reminders.php (no key needed, use --force to trigger immediately)
 */

declare(strict_types=1);

require __DIR__ . '/../fucina/lib/bootstrap.php';
require __DIR__ . '/../fucina/lib/courses.php';
require __DIR__ . '/../fucina/lib/progress.php';

header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

function finish_fucina_reminders(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
    exit($status === 200 ? 0 : 1);
}

if (PHP_SAPI !== 'cli') {
    $cron = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
    $secret = (string)($cron['cron_key'] ?? '');
    if (strlen($secret) < 32) finish_fucina_reminders(503, ['error' => 'cron_key is not configured']);
    if (!hash_equals($secret, (string)($_GET['key'] ?? ''))) finish_fucina_reminders(403, ['error' => 'forbidden']);
}

try {
    $now = new DateTimeImmutable('now', new DateTimeZone(APP_TIMEZONE));
    $today = $now->format('Y-m-d');
    $currentHour = (int)$now->format('G');
    $force = isset($_GET['force']) || in_array('--force', $argv ?? [], true);

    $rows = db()->prepare('SELECT DISTINCT u.id, u.streak, u.last_day, u.notify_off, u.notify_hours, d.day AS done FROM ' . t('users') . ' u JOIN ' . t('push_subscriptions') . ' s ON s.user_id = u.id
        LEFT JOIN ' . t('daily_results') . " d ON d.user_id = u.id AND d.day = ? WHERE u.status = 'active' AND (u.reminded_on IS NULL OR u.reminded_on < ?)");
    $rows->execute([$today, $today]);
    $sent = ['streak' => 0, 'daily' => 0];
    $mark = db()->prepare('UPDATE ' . t('users') . ' SET reminded_on = ? WHERE id = ?');
    foreach ($rows->fetchAll() as $u) {
        $id = (int)$u['id'];
        $off = notify_off($u['notify_off'] ?? null);
        $hours = notify_hours($u['notify_hours'] ?? null);
        $kind = null;

        if ((int)$u['streak'] > 0 && $u['last_day'] === day_offset($today, -1) && !in_array('streak', $off, true)) {
            $targetHour = $hours['streak'] ?? 18;
            if ($force || $currentHour >= $targetHour) {
                if (notify_user($id, 'streak', ['days' => (int)$u['streak']])) $kind = 'streak';
            }
        } elseif (!$u['done'] && !in_array('daily', $off, true)) {
            $targetHour = $hours['daily'] ?? 18;
            if ($force || $currentHour >= $targetHour) {
                if (notify_user($id, 'daily')) $kind = 'daily';
            }
        }

        if ($kind) {
            $sent[$kind]++;
            $mark->execute([$today, $id]);
        }
    }
    finish_fucina_reminders(200, ['ok' => true, 'sent' => $sent, 'server_hour' => $currentHour]);
} catch (Throwable $e) {
    error_log('Fucina reminders failed: ' . $e->getMessage());
    finish_fucina_reminders(500, ['ok' => false, 'error' => 'failed: ' . ($e instanceof PDOException ? 'SQLSTATE ' . $e->getCode() : 'see the log')]);
}
