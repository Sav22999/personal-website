<?php
/**
 * OpenNewsletter — the life of a newsletter and its sending.
 *
 *   draft ──submit (moderator, proposes a 30-minute slot)──> pending
 *   pending ──approve (publisher, may move the slot)──> scheduled ──cron at the slot──> sending ──> sent
 *   pending ──reject (with a note)──> rejected (editable again, like a draft)
 *   pending ──slot passed without approval (cron)──> expired (editable again)
 *   pending / scheduled ──withdraw / cancel──> draft / cancelled
 * Content can change only in draft, rejected and expired: what a publisher approves is what leaves.
 *
 * The cron scripts in projects/cron call run_due(), expire_pending() and remind_pending().
 */

declare(strict_types=1);

require_once __DIR__ . '/render.php';
require_once __DIR__ . '/groups.php';

const EDITABLE = ['draft', 'rejected', 'expired'];
const BATCH_LIMIT = 400;      // messages per cron run (the next run goes on from where this one stopped)
const BATCH_SECONDS = 240;    // and at most this long
const SEND_PAUSE_MS = 150;    // between two messages: no bursts towards the provider
const STALE_DAYS = 3;         // a newsletter still sending after this long gives up on what is left
const REMIND_HOURS = 24;      // publishers are reminded once when a pending slot is this close

function nl_row(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('newsletters') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function user_names(array $ids): array
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    if (!$ids) return [];
    $st = db()->prepare('SELECT id, name FROM ' . t('users') . ' WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')');
    $st->execute($ids);
    $out = [];
    foreach ($st->fetchAll() as $r) $out[(int)$r['id']] = $r['name'];
    return $out;
}

function public_nl(array $n, bool $withDoc = false, array $names = []): array
{
    $out = [
        'id' => (int)$n['id'], 'groupId' => (int)$n['group_id'], 'subject' => $n['subject'], 'preheader' => $n['preheader'], 'lang' => $n['lang'],
        'status' => $n['status'], 'authorId' => $n['author_id'] ? (int)$n['author_id'] : null, 'author' => $names[(int)$n['author_id']] ?? null,
        'reviewer' => $names[(int)$n['reviewer_id']] ?? null, 'proposedAt' => iso($n['proposed_at']), 'scheduledAt' => iso($n['scheduled_at']),
        'submittedAt' => iso($n['submitted_at']), 'reviewedAt' => iso($n['reviewed_at']), 'reviewNote' => $n['review_note'],
        'sentAt' => iso($n['sent_at']), 'startedAt' => iso($n['started_at']), 'total' => (int)$n['total'], 'sent' => (int)$n['sent_count'], 'failed' => (int)$n['failed_count'],
        'rev' => (int)$n['rev'], 'createdAt' => iso($n['created_at']), 'updatedAt' => iso($n['updated_at']),
    ];
    if ($withDoc) $out['doc'] = json_decode((string)$n['doc'], true) ?: ['theme' => default_theme(), 'blocks' => []];
    return $out;
}

/**
 * A moment chosen in the group's timezone ("2026-11-20", "18:30") as UTC, on a 30-minute slot and in the future.
 * Returns 'Y-m-d H:i:s' UTC or fails.
 */
function parse_slot(string $date, string $time, string $tz): string
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^([01]\d|2[0-3]):(00|30)$/', $time)) fail('slot_invalid', 422);
    try {
        $d = new DateTimeImmutable("$date $time:00", new DateTimeZone($tz));
    } catch (Throwable $e) {
        fail('slot_invalid', 422);
    }
    $ts = $d->getTimestamp();
    if ($ts % (SLOT_MINUTES * 60) !== 0) fail('slot_invalid', 422); // e.g. timezones with odd offsets
    if ($ts < next_slot(time())) fail('slot_past', 422);
    if ($ts > time() + 400 * 86400) fail('slot_invalid', 422);
    return gmdate('Y-m-d H:i:s', $ts);
}

function format_when(string $utc, string $tz, string $lang): string
{
    try {
        $d = (new DateTimeImmutable($utc, new DateTimeZone('UTC')))->setTimezone(new DateTimeZone($tz));
    } catch (Throwable $e) {
        return $utc . ' UTC';
    }
    return $d->format($lang === 'en' ? 'M j, Y \a\t H:i' : 'd/m/Y \o\r\e H:i') . ' (' . $tz . ')';
}

function nl_link(array $n): string
{
    return app_url() . '#/g/' . $n['group_id'] . '/n/' . $n['id'];
}

function user_lang(array $u): string
{
    return in_array($u['ui_lang'] ?? '', LANGS, true) ? $u['ui_lang'] : 'en';
}

// ---------------------------------------------------------------- notifications (platform email)

function notify_review(array $n, array $g, array $author): void
{
    foreach (members_with_role((int)$g['id'], 'publisher') as $p) {
        if ((int)$p['id'] === (int)$author['id']) continue;
        $L = user_lang($p);
        $T = mail_t($L);
        $v = ['by' => $author['name'], 'subject' => $n['subject'], 'group' => $g['name'], 'when' => format_when($n['proposed_at'], $g['timezone'], $L)];
        send_notice($p['email'], $p['name'], $L, fill($T['review_subject'], $v), $T['review_title'], [fill($T['hello'], ['name' => $p['name']]), fill($T['review_body'], $v)], nl_link($n), $T['review_button']);
    }
}

function notify_author(array $n, array $g, string $kind, ?array $by = null): void
{
    if (!$n['author_id']) return;
    $st = db()->prepare('SELECT * FROM ' . t('users') . " WHERE id = ? AND status = 'active'");
    $st->execute([$n['author_id']]);
    $a = $st->fetch();
    if (!$a || ($by && (int)$by['id'] === (int)$a['id'])) return; // nobody needs an email about their own action
    $L = user_lang($a);
    $T = mail_t($L);
    $when = $n['scheduled_at'] ?: $n['proposed_at'];
    $v = ['by' => $by['name'] ?? '', 'subject' => $n['subject'], 'group' => $g['name'], 'when' => $when ? format_when($when, $g['timezone'], $L) : '',
        'sent' => (int)$n['sent_count'], 'failed' => (int)$n['failed_count']];
    $paras = [fill($T['hello'], ['name' => $a['name']]), fill($T[$kind . '_body'], $v)];
    if ($kind === 'rejected' && $n['review_note'] !== '') $paras[] = $T['note'] . ' ' . $n['review_note'];
    if ($kind === 'sent' && (int)$n['failed_count'] > 0) $paras[] = fill($T['sent_failed'], $v);
    send_notice($a['email'], $a['name'], $L, fill($T[$kind . '_subject'], $v), $T[$kind . '_title'], $paras, nl_link($n), $T['open_button']);
}

// ---------------------------------------------------------------- cron jobs

/** Pending newsletters whose slot arrived without approval go back to their author. */
function expire_pending(): int
{
    $st = db()->prepare('SELECT * FROM ' . t('newsletters') . " WHERE status = 'pending' AND proposed_at <= ?");
    $st->execute([now_utc()]);
    $n = 0;
    foreach ($st->fetchAll() as $row) {
        $up = db()->prepare('UPDATE ' . t('newsletters') . " SET status = 'expired', updated_at = ? WHERE id = ? AND status = 'pending'");
        $up->execute([now_utc(), $row['id']]);
        if (!$up->rowCount()) continue;
        $g = group_row((int)$row['group_id']);
        if ($g) notify_author($row, $g, 'expired');
        $n++;
    }
    return $n;
}

/** One reminder to the publishers when a pending slot is less than REMIND_HOURS away. */
function remind_pending(): int
{
    $st = db()->prepare('SELECT * FROM ' . t('newsletters') . " WHERE status = 'pending' AND reminded_at IS NULL AND proposed_at <= ? AND submitted_at <= ?");
    $st->execute([gmdate('Y-m-d H:i:s', time() + REMIND_HOURS * 3600), gmdate('Y-m-d H:i:s', time() - 1800)]);
    $n = 0;
    foreach ($st->fetchAll() as $row) {
        db()->prepare('UPDATE ' . t('newsletters') . ' SET reminded_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        $g = group_row((int)$row['group_id']);
        if (!$g || $g['status'] !== 'active') continue;
        foreach (members_with_role((int)$g['id'], 'publisher') as $p) {
            if ((int)$p['id'] === (int)$row['author_id']) continue;
            $L = user_lang($p);
            $T = mail_t($L);
            $v = ['subject' => $row['subject'], 'group' => $g['name'], 'when' => format_when($row['proposed_at'], $g['timezone'], $L)];
            send_notice($p['email'], $p['name'], $L, fill($T['remind_subject'], $v), $T['remind_title'], [fill($T['hello'], ['name' => $p['name']]), fill($T['remind_body'], $v)], nl_link($row), $T['review_button']);
        }
        $n++;
    }
    return $n;
}

/** Scheduled newsletters whose slot arrived: their recipients are fixed now (active subscribers of that language). */
function start_due(): int
{
    $st = db()->prepare('SELECT n.* FROM ' . t('newsletters') . ' n JOIN ' . t('groups') . " g ON g.id = n.group_id
        WHERE n.status = 'scheduled' AND n.scheduled_at <= ? AND g.status = 'active' ORDER BY n.scheduled_at");
    $st->execute([now_utc()]);
    $started = 0;
    foreach ($st->fetchAll() as $n) {
        db()->beginTransaction();
        $lock = db()->prepare('UPDATE ' . t('newsletters') . " SET status = 'sending', started_at = ?, updated_at = ? WHERE id = ? AND status = 'scheduled'");
        $lock->execute([now_utc(), now_utc(), $n['id']]);
        if (!$lock->rowCount()) {
            db()->rollBack();
            continue;
        }
        $sql = 'INSERT IGNORE INTO ' . t('deliveries') . ' (newsletter_id, subscriber_id, status) SELECT ?, id, \'queued\' FROM ' . t('subscribers') . " WHERE group_id = ? AND status = 'active'";
        $args = [$n['id'], $n['group_id']];
        if ($n['lang']) {
            $sql .= ' AND lang = ?';
            $args[] = $n['lang'];
        }
        db()->prepare($sql)->execute($args);
        $total = db()->prepare('SELECT COUNT(*) FROM ' . t('deliveries') . ' WHERE newsletter_id = ?');
        $total->execute([$n['id']]);
        db()->prepare('UPDATE ' . t('newsletters') . ' SET total = ? WHERE id = ?')->execute([(int)$total->fetchColumn(), $n['id']]);
        db()->commit();
        $started++;
    }
    return $started;
}

/**
 * Sends the queued deliveries of the newsletters being sent, within BATCH_LIMIT messages and BATCH_SECONDS, and within
 * each group's hourly limit (groups.rate_per_hour, set by its admins to match their provider). What does not fit is
 * sent by the next runs. The HTML is rendered once per language: only the unsubscribe link changes.
 *
 * A temporary refusal (4xx, connection problems) stops that newsletter for this run and leaves the rest queued; only
 * permanent refusals (5xx) count as failed. A newsletter still not finished after STALE_DAYS gives up on what is left.
 */
function send_queued(): array
{
    $deadline = time() + BATCH_SECONDS;
    $budget = BATCH_LIMIT;
    $report = ['sent' => 0, 'failed' => 0, 'deferred' => 0, 'finished' => 0];
    $list = db()->query('SELECT * FROM ' . t('newsletters') . " WHERE status = 'sending' ORDER BY started_at")->fetchAll();
    foreach ($list as $n) {
        if ($budget <= 0 || time() >= $deadline) break;
        $g = group_row((int)$n['group_id']);
        if (!$g || $g['status'] !== 'active') continue; // a suspended group waits
        $mailer = group_mailer($g);
        $stale = strtotime($n['started_at'] . ' UTC') < time() - STALE_DAYS * 86400;
        if (!$mailer || $stale) {
            // no outgoing address any more, or days of refusals: what is left will not leave
            db()->prepare('UPDATE ' . t('deliveries') . " SET status = 'failed', error = ? WHERE newsletter_id = ? AND status = 'queued'")
                ->execute([$mailer ? 'gave up after ' . STALE_DAYS . ' days' : 'no outgoing address', $n['id']]);
            finish_if_done($n, $g, $report);
            continue;
        }
        // the group's hourly limit, counted on everything it sent in the last 60 minutes. Addresses that were told
        // "try later" go to the end of the queue, so one busy mailbox never holds up the others.
        $st = db()->prepare('SELECT COUNT(*) FROM ' . t('deliveries') . ' d JOIN ' . t('newsletters') . " x ON x.id = d.newsletter_id
            WHERE x.group_id = ? AND d.status = 'sent' AND d.sent_at > ?");
        $st->execute([$g['id'], gmdate('Y-m-d H:i:s', time() - 3600)]);
        $allowed = min($budget, max(0, (int)$g['rate_per_hour'] - (int)$st->fetchColumn()));
        if ($allowed <= 0) continue;

        $doc = clean_doc(json_decode((string)$n['doc'], true));
        $q = db()->prepare('SELECT d.subscriber_id, s.email, s.lang, s.token FROM ' . t('deliveries') . ' d JOIN ' . t('subscribers') . " s ON s.id = d.subscriber_id
            WHERE d.newsletter_id = ? AND d.status = 'queued' AND s.status = 'active' ORDER BY (d.error <> '') , d.subscriber_id LIMIT " . (int)$allowed);
        $q->execute([$n['id']]);
        $rows = $q->fetchAll();
        $rendered = [];
        $messages = [];
        $placeholder = str_repeat('0', 32);
        foreach ($rows as $r) {
            $lang = $r['lang'];
            if (!isset($rendered[$lang])) $rendered[$lang] = render_email($doc, $g, ['lang' => $lang, 'subject' => $n['subject'], 'preheader' => $n['preheader'], 'unsubscribe' => unsubscribe_url($g, $placeholder)]);
            $messages[] = [
                'to' => $r['email'], 'name' => '', 'subject' => $n['subject'],
                'html' => str_replace($placeholder, $r['token'], $rendered[$lang]['html']),
                'text' => str_replace($placeholder, $r['token'], $rendered[$lang]['text']),
                'headers' => list_headers($g, $r['token']),
            ];
        }
        $mark = db()->prepare('UPDATE ' . t('deliveries') . ' SET status = ?, error = ?, sent_at = ? WHERE newsletter_id = ? AND subscriber_id = ?');
        $note = db()->prepare('UPDATE ' . t('deliveries') . ' SET error = ? WHERE newsletter_id = ? AND subscriber_id = ?');
        smtp_send_all($mailer, $messages, function (int $i, array $res) use ($rows, $mark, $note, $n, &$report) {
            if ($res['ok'] || !$res['temp']) {
                $mark->execute([$res['ok'] ? 'sent' : 'failed', mb_substr($res['error'], 0, 200), now_utc(), $n['id'], $rows[$i]['subscriber_id']]);
                $report[$res['ok'] ? 'sent' : 'failed']++;
            } else {
                $note->execute([mb_substr('retry: ' . $res['error'], 0, 200), $n['id'], $rows[$i]['subscriber_id']]); // stays queued
                $report['deferred']++;
            }
        }, SEND_PAUSE_MS);
        $budget -= count($rows);
        finish_if_done($n, $g, $report);
    }
    return $report;
}

/** Updates the numbers of a newsletter being sent and closes it when nothing is queued any more. */
function finish_if_done(array $n, array $g, array &$report): void
{
    // subscribers who left meanwhile are dropped from the queue
    db()->prepare('DELETE d FROM ' . t('deliveries') . ' d JOIN ' . t('subscribers') . " s ON s.id = d.subscriber_id WHERE d.newsletter_id = ? AND d.status = 'queued' AND s.status <> 'active'")->execute([$n['id']]);
    $c = db()->prepare('SELECT status, COUNT(*) c FROM ' . t('deliveries') . ' WHERE newsletter_id = ? GROUP BY status');
    $c->execute([$n['id']]);
    $counts = ['queued' => 0, 'sent' => 0, 'failed' => 0];
    foreach ($c->fetchAll() as $row) $counts[$row['status']] = (int)$row['c'];
    $done = $counts['queued'] === 0;
    db()->prepare('UPDATE ' . t('newsletters') . ' SET sent_count = ?, failed_count = ?, status = ?, sent_at = ?, updated_at = ? WHERE id = ?')
        ->execute([$counts['sent'], $counts['failed'], $done ? ($counts['sent'] === 0 && $counts['failed'] > 0 ? 'failed' : 'sent') : 'sending', $done ? now_utc() : null, now_utc(), $n['id']]);
    if ($done) {
        $report['finished']++;
        notify_author(nl_row((int)$n['id']), $g, 'sent');
    }
}
