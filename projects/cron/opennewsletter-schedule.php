<?php
/**
 * OpenNewsletter — scheduled sending: see README.md. Run every 30 minutes (e.g. 0,30 * * * *): newsletters leave on
 * 30-minute slots, so each run sends the ones whose slot has come.
 *   1. proposals whose slot passed without a publisher's approval go back to their author ("expired", email to the author);
 *   2. approved newsletters whose slot has come start: their recipients are fixed now (active subscribers, of the
 *      newsletter's language if it has one);
 *   3. the queue is sent from each group's own SMTP, at most 400 messages / 4 minutes per run: bigger lists go on at
 *      the next run. When a newsletter is finished its author gets an email with the numbers.
 *
 * URL:          https://www.saveriomorelli.com/projects/cron/opennewsletter-schedule.php?key=SECRET_KEY
 * Command line: php opennewsletter-schedule.php (no key needed)
 */

declare(strict_types=1);

require __DIR__ . '/../opennewsletter/lib/bootstrap.php';
require __DIR__ . '/../opennewsletter/lib/newsletters.php';

header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

function finish_on_schedule(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
    exit($status === 200 ? 0 : 1);
}

if (PHP_SAPI !== 'cli') {
    $cron = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
    $secret = (string)($cron['cron_key'] ?? '');
    if (strlen($secret) < 32) finish_on_schedule(503, ['error' => 'cron_key is not configured']);
    if (!hash_equals($secret, (string)($_GET['key'] ?? ''))) finish_on_schedule(403, ['error' => 'forbidden']);
}

@set_time_limit(300);
ignore_user_abort(true);

// Two runs at the same time (a slow run and the next one) would send the same messages twice
$lock = fopen(sys_get_temp_dir() . '/opennewsletter-schedule.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) finish_on_schedule(200, ['ok' => true, 'skipped' => 'another run is in progress']);

try {
    if (!credentials()) throw new RuntimeException('opennewsletter/credentials.php not found');
    $expired = expire_pending();
    $started = start_due();
    $sent = send_queued();
    finish_on_schedule(200, ['ok' => true, 'expired' => $expired, 'started' => $started, 'messages' => $sent, 'utc' => gmdate('Y-m-d H:i')]);
} catch (Throwable $e) {
    error_log('OpenNewsletter schedule failed: ' . $e->getMessage());
    finish_on_schedule(500, ['ok' => false, 'error' => 'failed: ' . ($e instanceof PDOException ? 'SQLSTATE ' . $e->getCode() : 'see the log')]);
}
