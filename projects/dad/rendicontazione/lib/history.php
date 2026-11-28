<?php
/**
 * Rendicontazione Ore: the history of changes and undo.
 *
 * Every change of hours stores, for each date it touched, the day before and after it. Anyone can
 * read the history of their own hours; only administrators can undo. Changes to accounts and to the
 * quick schedules are listed too (administrators only) but they have no snapshots and cannot be undone.
 */

declare(strict_types=1);

const UNDOABLE_ACTIONS = ['day.add', 'day.edit', 'day.delete', 'import', 'undo'];

function history_add(int $actorId, int $targetUserId, string $action, ?string $summary = null, ?array $changes = null, ?int $undoOf = null): int
{
    db()->prepare('INSERT INTO ' . t('history') . ' (actor_id, target_user_id, action, summary, changes, undo_of, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)')
        ->execute([
            $actorId,
            $targetUserId,
            $action,
            $summary !== null ? mb_substr($summary, 0, 255) : null,
            $changes !== null ? json_encode($changes, JSON_UNESCAPED_UNICODE) : null,
            $undoOf,
            now_utc(),
        ]);
    return (int)db()->lastInsertId();
}

/** Saves a day (null deletes it) and records the change. Returns false when nothing changed. */
function day_change(int $actorId, int $userId, string $date, ?array $snapshot): bool
{
    return in_transaction(function () use ($actorId, $userId, $date, $snapshot) {
        $before = day_get($userId, $date);
        if ($before === $snapshot) return false;

        day_put($userId, $date, $snapshot, $actorId);
        $action = $before === null ? 'day.add' : ($snapshot === null ? 'day.delete' : 'day.edit');
        history_add($actorId, $userId, $action, null, [$date => ['before' => $before, 'after' => $snapshot]]);
        return true;
    });
}

/** Applies many days as one change, so a whole import can be undone in one go. */
function days_import(int $actorId, int $userId, array $snapshots, string $summary): int
{
    return in_transaction(function () use ($actorId, $userId, $snapshots, $summary) {
        $changes = [];
        foreach ($snapshots as $date => $snapshot) {
            $before = day_get($userId, $date);
            if ($before === $snapshot) continue;
            day_put($userId, $date, $snapshot, $actorId);
            $changes[$date] = ['before' => $before, 'after' => $snapshot];
        }
        if ($changes) history_add($actorId, $userId, 'import', $summary, $changes);
        return count($changes);
    });
}

/** Latest entries about a user, newest first. Users only see the changes to their hours. */
function history_list(int $targetUserId, bool $viewerIsAdmin, int $limit, int $beforeId): array
{
    $sql = 'SELECT h.*, a.name AS actor_name, b.name AS undone_by_name FROM ' . t('history') . ' h
        LEFT JOIN ' . t('users') . ' a ON a.id = h.actor_id
        LEFT JOIN ' . t('users') . ' b ON b.id = h.undone_by
        WHERE h.target_user_id = ?';
    $params = [$targetUserId];
    if (!$viewerIsAdmin) {
        $sql .= " AND h.action IN ('day.add', 'day.edit', 'day.delete', 'import', 'undo')";
    }
    if ($beforeId > 0) {
        $sql .= ' AND h.id < ?';
        $params[] = $beforeId;
    }
    $sql .= ' ORDER BY h.id DESC LIMIT ' . ($limit + 1);

    $st = db()->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll();
    $more = count($rows) > $limit;
    $rows = array_slice($rows, 0, $limit);

    $entries = array_map(function ($r) use ($viewerIsAdmin) {
        $changes = $r['changes'] ? (json_decode((string)$r['changes'], true) ?: []) : [];
        $undone = !empty($r['undone_at']);
        $undoable = in_array($r['action'], UNDOABLE_ACTIONS, true) && $changes && !$undone;
        $dates = array_keys($changes);
        sort($dates);
        return [
            'id' => (int)$r['id'],
            'action' => (string)$r['action'],
            'actor' => (string)($r['actor_name'] ?? 'Utente rimosso'),
            'summary' => (string)($r['summary'] ?? ''),
            'at' => (string)$r['created_at'],
            'dates' => $dates,
            // the detail of a single day; an import is only described by its summary
            'change' => count($changes) === 1 ? reset($changes) : null,
            'undone' => $undone ? ['at' => (string)$r['undone_at'], 'by' => (string)($r['undone_by_name'] ?? '')] : null,
            'canUndo' => $viewerIsAdmin && $undoable,
        ];
    }, $rows);

    return ['entries' => $entries, 'more' => $more];
}

/**
 * Undoes an entry: every date it touched goes back to how it was before.
 * If a date was changed again afterwards the undo would also wipe that newer change, so it stops
 * and reports those dates, unless $force confirms it. Undoing an undo restores the change.
 */
function history_undo(int $entryId, array $admin, bool $force): array
{
    return in_transaction(function () use ($entryId, $admin, $force) {
        $st = db()->prepare('SELECT * FROM ' . t('history') . ' WHERE id = ?');
        $st->execute([$entryId]);
        $entry = $st->fetch();
        if (!$entry) throw new UserError('Questa voce della cronologia non esiste più.');
        if (!empty($entry['undone_at'])) throw new UserError('Questa modifica è già stata annullata.');
        $changes = $entry['changes'] ? json_decode((string)$entry['changes'], true) : null;
        if (!$changes || !in_array($entry['action'], UNDOABLE_ACTIONS, true)) {
            throw new UserError('Questo tipo di modifica non si può annullare.');
        }

        $userId = (int)$entry['target_user_id'];
        $conflicts = [];
        $reverts = [];
        foreach ($changes as $date => $change) {
            $current = day_get($userId, $date);
            if ($current !== $change['after']) $conflicts[] = $date;
            if ($current !== $change['before']) {
                $reverts[$date] = ['before' => $current, 'after' => $change['before']];
            }
        }
        if ($conflicts && !$force) {
            sort($conflicts);
            return ['ok' => false, 'conflicts' => $conflicts];
        }

        foreach ($reverts as $date => $revert) {
            day_put($userId, $date, $revert['after'], (int)$admin['id']);
        }
        db()->prepare('UPDATE ' . t('history') . ' SET undone_at = ?, undone_by = ? WHERE id = ?')
            ->execute([now_utc(), $admin['id'], $entryId]);
        if ($entry['action'] === 'undo' && $entry['undo_of']) {
            db()->prepare('UPDATE ' . t('history') . ' SET undone_at = NULL, undone_by = NULL WHERE id = ?')
                ->execute([$entry['undo_of']]);
        }
        history_add((int)$admin['id'], $userId, 'undo', null, $reverts ?: null, $entryId);
        return ['ok' => true, 'dates' => array_keys($changes)];
    });
}
