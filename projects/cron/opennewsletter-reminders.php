<?php
/**
 * OpenNewsletter — reminders: see README.md. Run every 30 minutes, like opennewsletter-schedule.php (or hourly).
 * When a newsletter waiting for approval is less than 24 hours from its proposed slot, the publishers of its group
 * (and the admins and owner) get one email reminding them to approve or reject it. At most one reminder per proposal.
 *
 * URL:          https://www.saveriomorelli.com/projects/cron/opennewsletter-reminders.php?key=SECRET_KEY
 * Command line: php opennewsletter-reminders.php (no key needed)
 */

declare(strict_types=1);

require __DIR__ . '/../opennewsletter/lib/bootstrap.php';
require __DIR__ . '/../opennewsletter/lib/newsletters.php';

header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

function finish_on_reminders(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
    exit($status === 200 ? 0 : 1);
}

if (PHP_SAPI !== 'cli') {
    $cron = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
    $secret = (string)($cron['cron_key'] ?? '');
    if (strlen($secret) < 32) finish_on_reminders(503, ['error' => 'cron_key is not configured']);
    if (!hash_equals($secret, (string)($_GET['key'] ?? ''))) finish_on_reminders(403, ['error' => 'forbidden']);
}

// Moved to www.illerom.eu: the job runs there
require __DIR__ . '/illerom.php';
if ($moved = illerom_cron(basename(__FILE__))) finish_on_reminders($moved[0], $moved[1]);

try {
    if (!credentials()) throw new RuntimeException('opennewsletter/credentials.php not found');
    finish_on_reminders(200, ['ok' => true, 'reminded' => remind_pending()]);
} catch (Throwable $e) {
    error_log('OpenNewsletter reminders failed: ' . $e->getMessage());
    finish_on_reminders(500, ['ok' => false, 'error' => 'failed: ' . ($e instanceof PDOException ? 'SQLSTATE ' . $e->getCode() : 'see the log')]);
}
