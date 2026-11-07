<?php
/**
 * Fucina — evening reminders, to run once a day around 18:00 with the same key as the clean-up:
 *   https://www.saveriomorelli.com/projects/fucina/reminders.php?key=SECRET_KEY    or    php reminders.php
 * One notification at most per person and day: the streak at risk first, otherwise the daily challenge.
 */

declare(strict_types=1);

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/courses.php';
require __DIR__ . '/lib/progress.php';

header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

if (PHP_SAPI !== 'cli') {
    $file = __DIR__ . '/../cron/credentials.php';
    $secret = (string)((is_file($file) ? require $file : [])['cron_key'] ?? '');
    if (strlen($secret) < 32 || !hash_equals($secret, (string)($_GET['key'] ?? ''))) {
        http_response_code(403);
        echo json_encode(['error' => 'forbidden']);
        exit;
    }
}

$today = today();
$rows = db()->prepare('SELECT DISTINCT u.id, u.streak, u.last_day, u.notify_off, d.day AS done FROM ' . t('users') . ' u JOIN ' . t('push_subscriptions') . ' s ON s.user_id = u.id
    LEFT JOIN ' . t('daily_results') . " d ON d.user_id = u.id AND d.day = ? WHERE u.status = 'active' AND (u.reminded_on IS NULL OR u.reminded_on < ?)");
$rows->execute([$today, $today]);
$sent = ['streak' => 0, 'daily' => 0];
$mark = db()->prepare('UPDATE ' . t('users') . ' SET reminded_on = ? WHERE id = ?');
foreach ($rows->fetchAll() as $u) {
    $id = (int)$u['id'];
    $kind = null;
    if ((int)$u['streak'] > 0 && $u['last_day'] === day_offset($today, -1) && notify_user($id, 'streak', ['days' => (int)$u['streak']])) $kind = 'streak';
    elseif (!$u['done'] && notify_user($id, 'daily')) $kind = 'daily';
    if ($kind) {
        $sent[$kind]++;
        $mark->execute([$today, $id]);
    }
}
echo json_encode(['ok' => true, 'sent' => $sent], JSON_PRETTY_PRINT) . "\n";
