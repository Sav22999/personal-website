<?php
/**
 * Rendicontazione Ore — data models & business logic.
 * WorkDays, WorkShifts, Users, Presets, and aggregation summaries.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

// Italian month names
const MONTH_NAMES = [
    1 => 'Gennaio',
    2 => 'Febbraio',
    3 => 'Marzo',
    4 => 'Aprile',
    5 => 'Maggio',
    6 => 'Giugno',
    7 => 'Luglio',
    8 => 'Agosto',
    9 => 'Settembre',
    10 => 'Ottobre',
    11 => 'Novembre',
    12 => 'Dicembre',
];

const DAY_NAMES = [
    'Sun' => 'Domenica',
    'Mon' => 'Lunedì',
    'Tue' => 'Martedì',
    'Wed' => 'Mercoledì',
    'Thu' => 'Giovedì',
    'Fri' => 'Venerdì',
    'Sat' => 'Sabato',
];

// --- USERS MANAGEMENT ---

function user_get_by_id(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    return $row ?: null;
}

function user_get_by_email(string $email): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE LOWER(email) = ?');
    $st->execute([mb_strtolower(trim($email))]);
    $row = $st->fetch();
    return $row ?: null;
}

function user_get_by_google(string $sub): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE google_sub = ?');
    $st->execute([$sub]);
    $row = $st->fetch();
    return $row ?: null;
}

function user_create(array $data): int
{
    $pdo = db();
    $now = now_utc();
    $st = $pdo->prepare('
        INSERT INTO ' . t('users') . '
        (name, email, google_sub, avatar_url, role, status, notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $st->execute([
        trim((string)$data['name']),
        mb_strtolower(trim((string)$data['email'])),
        !empty($data['google_sub']) ? (string)$data['google_sub'] : null,
        !empty($data['avatar_url']) ? (string)$data['avatar_url'] : null,
        (int)($data['role'] ?? ROLE_USER),
        (string)($data['status'] ?? STATUS_PENDING),
        !empty($data['notes']) ? (string)$data['notes'] : null,
        $now,
    ]);
    return (int)$pdo->lastInsertId();
}

function user_update(int $id, array $data): void
{
    $fields = [];
    $params = [];
    foreach (['name', 'avatar_url', 'role', 'status', 'notes'] as $k) {
        if (array_key_exists($k, $data)) {
            $fields[] = "`$k` = ?";
            $params[] = $data[$k];
        }
    }
    if (!$fields) return;
    $params[] = $id;
    $sql = 'UPDATE ' . t('users') . ' SET ' . implode(', ', $fields) . ' WHERE id = ?';
    db()->prepare($sql)->execute($params);
}

function users_list(?string $statusFilter = null): array
{
    $sql = 'SELECT id, name, email, google_sub, avatar_url, role, status, notes, created_at, last_login_at FROM ' . t('users');
    $params = [];
    if ($statusFilter !== null && $statusFilter !== '') {
        $sql .= ' WHERE status = ?';
        $params[] = $statusFilter;
    }
    $sql .= ' ORDER BY CASE WHEN status = "pending" THEN 0 ELSE 1 END, role DESC, id ASC';
    $st = db()->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll();

    return array_map(function ($r) {
        return [
            'id' => (int)$r['id'],
            'name' => (string)$r['name'],
            'email' => (string)$r['email'],
            'role' => (int)$r['role'],
            'role_label' => ROLE_LABELS[(int)$r['role']] ?? 'Utente',
            'status' => (string)$r['status'],
            'status_label' => STATUS_LABELS[(string)$r['status']] ?? $r['status'],
            'avatar_url' => (string)($r['avatar_url'] ?? ''),
            'notes' => (string)($r['notes'] ?? ''),
            'created_at' => (string)$r['created_at'],
            'last_login_at' => (string)($r['last_login_at'] ?? ''),
        ];
    }, $rows);
}

function get_pending_users_count(): int
{
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('users') . ' WHERE status = ?');
    $st->execute([STATUS_PENDING]);
    return (int)$st->fetchColumn();
}

// --- WORK DAYS & SHIFTS ---

/**
 * Returns a complete JSON-ready snapshot of a work day and its shifts.
 */
function work_day_get_snapshot(int $userId, string $date): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('work_days') . ' WHERE user_id = ? AND work_date = ?');
    $st->execute([$userId, $date]);
    $day = $st->fetch();
    if (!$day) return null;

    $stShifts = db()->prepare('SELECT * FROM ' . t('work_shifts') . ' WHERE day_id = ? ORDER BY sort_order ASC, id ASC');
    $stShifts->execute([(int)$day['id']]);
    $shifts = $stShifts->fetchAll();

    $cleanShifts = [];
    foreach ($shifts as $s) {
        $cleanShifts[] = [
            'start_time' => $s['start_time'],
            'end_time' => $s['end_time'],
            'is_overtime' => (bool)$s['is_overtime'],
            'hours_ordinary' => (float)$s['hours_ordinary'],
            'hours_overtime' => (float)$s['hours_overtime'],
            'notes' => $s['notes'],
        ];
    }

    return [
        'work_date' => (string)$day['work_date'],
        'day_type' => (string)$day['day_type'],
        'notes' => $day['notes'] ? (string)$day['notes'] : null,
        'shifts' => $cleanShifts,
    ];
}

/**
 * Returns work days and shifts for a user in a given year and month.
 * Computes daily totals and progressive/cumulative month totals.
 */
function work_days_get_month(int $userId, int $year, int $month): array
{
    $startOfMonth = sprintf('%04d-%02d-01', $year, $month);
    $daysInMonth = (int)date('t', strtotime($startOfMonth));
    $endOfMonth = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

    // Fetch existing days
    $st = db()->prepare('
        SELECT * FROM ' . t('work_days') . '
        WHERE user_id = ? AND work_date >= ? AND work_date <= ?
        ORDER BY work_date ASC
    ');
    $st->execute([$userId, $startOfMonth, $endOfMonth]);
    $daysDb = $st->fetchAll();

    $daysMap = [];
    $dayIds = [];
    foreach ($daysDb as $d) {
        $daysMap[$d['work_date']] = $d;
        $dayIds[] = (int)$d['id'];
    }

    // Fetch shifts for these days
    $shiftsByDay = [];
    if (!empty($dayIds)) {
        $inClause = implode(',', $dayIds);
        $st = db()->query('
            SELECT * FROM ' . t('work_shifts') . '
            WHERE day_id IN (' . $inClause . ')
            ORDER BY day_id ASC, sort_order ASC, id ASC
        ');
        while ($sh = $st->fetch()) {
            $shiftsByDay[(int)$sh['day_id']][] = [
                'id' => (int)$sh['id'],
                'sort_order' => (int)$sh['sort_order'],
                'start_time' => (string)($sh['start_time'] ?? ''),
                'end_time' => (string)($sh['end_time'] ?? ''),
                'is_overtime' => (bool)$sh['is_overtime'],
                'hours_ordinary' => (float)$sh['hours_ordinary'],
                'hours_overtime' => (float)$sh['hours_overtime'],
                'hours_total' => round((float)$sh['hours_ordinary'] + (float)$sh['hours_overtime'], 2),
                'notes' => (string)($sh['notes'] ?? ''),
            ];
        }
    }

    // Build complete month list (day 1 to N)
    $cumulativeHours = 0.0;
    $cumulativeOrdinary = 0.0;
    $cumulativeOvertime = 0.0;

    $daysResult = [];

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $ts = strtotime($dateStr);
        $dayOfWeekShort = date('D', $ts);
        $dayOfWeekNum = (int)date('N', $ts); // 1 = Lunedì, 7 = Domenica
        $isWeekend = ($dayOfWeekNum >= 6);

        $dayRecord = $daysMap[$dateStr] ?? null;
        $shifts = $dayRecord ? ($shiftsByDay[(int)$dayRecord['id']] ?? []) : [];

        $dayOrdinary = 0.0;
        $dayOvertime = 0.0;
        foreach ($shifts as $s) {
            $dayOrdinary += $s['hours_ordinary'];
            $dayOvertime += $s['hours_overtime'];
        }
        $dayTotal = round($dayOrdinary + $dayOvertime, 2);

        $hasEntry = ($dayRecord !== null || count($shifts) > 0);
        if ($hasEntry) {
            $cumulativeHours = round($cumulativeHours + $dayTotal, 2);
            $cumulativeOrdinary = round($cumulativeOrdinary + $dayOrdinary, 2);
            $cumulativeOvertime = round($cumulativeOvertime + $dayOvertime, 2);
        }

        $daysResult[] = [
            'date' => $dateStr,
            'day_number' => $day,
            'day_name' => DAY_NAMES[$dayOfWeekShort] ?? $dayOfWeekShort,
            'is_weekend' => $isWeekend,
            'has_entry' => $hasEntry,
            'day_id' => $dayRecord ? (int)$dayRecord['id'] : null,
            'day_type' => $dayRecord ? (string)$dayRecord['day_type'] : ($isWeekend ? 'riposo' : 'work'),
            'notes' => $dayRecord ? (string)($dayRecord['notes'] ?? '') : '',
            'shifts' => $shifts,
            'hours_ordinary' => $dayOrdinary,
            'hours_overtime' => $dayOvertime,
            'hours_total' => $dayTotal,
            'hours_ordinary_formatted' => format_hours_hms($dayOrdinary),
            'hours_overtime_formatted' => format_hours_hms($dayOvertime),
            'hours_total_formatted' => format_hours_hms($dayTotal),
            'cumulative_hours' => $cumulativeHours,
            'cumulative_hours_formatted' => format_hours_hms($cumulativeHours),
        ];
    }

    return [
        'year' => $year,
        'month' => $month,
        'month_name' => MONTH_NAMES[$month] ?? "Mese $month",
        'days' => $daysResult,
        'totals' => [
            'ordinary_hours' => $cumulativeOrdinary,
            'overtime_hours' => $cumulativeOvertime,
            'total_hours' => $cumulativeHours,
            'ordinary_formatted' => format_hours_hms($cumulativeOrdinary),
            'overtime_formatted' => format_hours_hms($cumulativeOvertime),
            'total_formatted' => format_hours_hms($cumulativeHours),
            'total_human' => format_hours_human($cumulativeHours),
            'days_worked' => count(array_filter($daysResult, fn($d) => $d['hours_total'] > 0)),
        ],
    ];
}

/**
 * Returns yearly overview across all 12 months for a given user.
 */
function work_days_get_year_summary(int $userId, int $year): array
{
    $startOfYear = sprintf('%04d-01-01', $year);
    $endOfYear = sprintf('%04d-12-31', $year);

    $st = db()->prepare('
        SELECT
            d.work_date,
            d.day_type,
            d.notes AS day_notes,
            s.hours_ordinary,
            s.hours_overtime,
            s.notes AS shift_notes
        FROM ' . t('work_days') . ' d
        LEFT JOIN ' . t('work_shifts') . ' s ON s.day_id = d.id
        WHERE d.user_id = ? AND d.work_date >= ? AND d.work_date <= ?
        ORDER BY d.work_date ASC
    ');
    $st->execute([$userId, $startOfYear, $endOfYear]);
    $rows = $st->fetchAll();

    $monthsData = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthsData[$m] = [
            'month' => $m,
            'month_name' => MONTH_NAMES[$m],
            'ordinary_hours' => 0.0,
            'overtime_hours' => 0.0,
            'total_hours' => 0.0,
            'days_worked_set' => [],
            'rain_days_set' => [],
            'absence_days_set' => [],
        ];
    }

    foreach ($rows as $r) {
        $m = (int)date('n', strtotime($r['work_date']));
        $ord = (float)($r['hours_ordinary'] ?? 0);
        $over = (float)($r['hours_overtime'] ?? 0);
        $monthsData[$m]['ordinary_hours'] += $ord;
        $monthsData[$m]['overtime_hours'] += $over;

        if (($ord + $over) > 0) {
            $monthsData[$m]['days_worked_set'][$r['work_date']] = true;
        }

        $allNotes = mb_strtolower(($r['day_notes'] ?? '') . ' ' . ($r['shift_notes'] ?? ''));
        if (strpos($allNotes, 'pioggia') !== false) {
            $monthsData[$m]['rain_days_set'][$r['work_date']] = true;
        }
        if (in_array($r['day_type'] ?? '', ['malattia', 'ferie', 'permesso'], true) ||
            preg_match('/(malattia|ferie|permesso)/i', $allNotes)) {
            $monthsData[$m]['absence_days_set'][$r['work_date']] = true;
        }
    }

    $summaryMonths = [];
    $yearOrdinary = 0.0;
    $yearOvertime = 0.0;
    $yearTotal = 0.0;
    $yearDaysWorked = 0;
    $yearRainDays = 0;

    foreach ($monthsData as $m => $d) {
        $ord = round($d['ordinary_hours'], 2);
        $over = round($d['overtime_hours'], 2);
        $tot = round($ord + $over, 2);
        $workedCount = count($d['days_worked_set']);
        $rainCount = count($d['rain_days_set']);
        $absenceCount = count($d['absence_days_set']);

        $yearOrdinary += $ord;
        $yearOvertime += $over;
        $yearTotal += $tot;
        $yearDaysWorked += $workedCount;
        $yearRainDays += $rainCount;

        $summaryMonths[] = [
            'month' => $m,
            'month_name' => $d['month_name'],
            'ordinary_hours' => $ord,
            'overtime_hours' => $over,
            'total_hours' => $tot,
            'ordinary_formatted' => format_hours_hms($ord),
            'overtime_formatted' => format_hours_hms($over),
            'total_formatted' => format_hours_hms($tot),
            'total_human' => format_hours_human($tot),
            'days_worked' => $workedCount,
            'rain_days' => $rainCount,
            'absence_days' => $absenceCount,
        ];
    }

    return [
        'year' => $year,
        'months' => $summaryMonths,
        'totals' => [
            'ordinary_hours' => round($yearOrdinary, 2),
            'overtime_hours' => round($yearOvertime, 2),
            'total_hours' => round($yearTotal, 2),
            'ordinary_formatted' => format_hours_hms($yearOrdinary),
            'overtime_formatted' => format_hours_hms($yearOvertime),
            'total_formatted' => format_hours_hms($yearTotal),
            'total_human' => format_hours_human($yearTotal),
            'days_worked' => $yearDaysWorked,
            'rain_days' => $yearRainDays,
            'avg_hours_per_worked_day' => $yearDaysWorked > 0 ? round($yearTotal / $yearDaysWorked, 2) : 0.0,
        ],
    ];
}

/**
 * Saves or updates a work day and its shifts.
 * Replaces any existing shifts for that day atomically.
 * Automatically tracks changes in audit_log.
 */
function work_day_save(int $userId, string $date, string $dayType = 'work', ?string $notes = null, array $shifts = [], bool $recordAudit = true, ?int $actorUserId = null): int
{
    $beforeSnapshot = $recordAudit ? work_day_get_snapshot($userId, $date) : null;

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $now = now_utc();

        // 1. Find or create work_days row
        $st = $pdo->prepare('SELECT id FROM ' . t('work_days') . ' WHERE user_id = ? AND work_date = ?');
        $st->execute([$userId, $date]);
        $dayId = $st->fetchColumn();

        if ($dayId) {
            $dayId = (int)$dayId;
            $st = $pdo->prepare('
                UPDATE ' . t('work_days') . '
                SET day_type = ?, notes = ?, updated_at = ?
                WHERE id = ?
            ');
            $st->execute([$dayType, $notes, $now, $dayId]);

            // Clear previous shifts
            $pdo->prepare('DELETE FROM ' . t('work_shifts') . ' WHERE day_id = ?')->execute([$dayId]);
        } else {
            $st = $pdo->prepare('
                INSERT INTO ' . t('work_days') . '
                (user_id, work_date, day_type, notes, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $st->execute([$userId, $date, $dayType, $notes, $now, $now]);
            $dayId = (int)$pdo->lastInsertId();
        }

        // 2. Insert new shifts
        $sort = 0;
        $insertShift = $pdo->prepare('
            INSERT INTO ' . t('work_shifts') . '
            (day_id, user_id, sort_order, start_time, end_time, is_overtime, hours_ordinary, hours_overtime, notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $totalOrd = 0.0;
        $totalOver = 0.0;

        foreach ($shifts as $s) {
            $start = !empty($s['start_time']) ? trim((string)$s['start_time']) : null;
            $end = !empty($s['end_time']) ? trim((string)$s['end_time']) : null;
            $isOvertime = !empty($s['is_overtime']) ? 1 : 0;
            $shiftNotes = !empty($s['notes']) ? trim((string)$s['notes']) : null;

            // Determine hours
            $hoursOrd = 0.0;
            $hoursOver = 0.0;

            if (isset($s['hours_ordinary']) && $s['hours_ordinary'] !== '') {
                $hoursOrd = (float)$s['hours_ordinary'];
            }
            if (isset($s['hours_overtime']) && $s['hours_overtime'] !== '') {
                $hoursOver = (float)$s['hours_overtime'];
            }

            // If hours were not explicitly provided, calculate duration from start and end
            if ($hoursOrd == 0.0 && $hoursOver == 0.0 && $start && $end) {
                $dur = calculate_hours($start, $end);
                if ($isOvertime) {
                    $hoursOver = $dur;
                } else {
                    $hoursOrd = $dur;
                }
            }

            $totalOrd += $hoursOrd;
            $totalOver += $hoursOver;

            $insertShift->execute([
                $dayId,
                $userId,
                $sort++,
                $start,
                $end,
                $isOvertime,
                $hoursOrd,
                $hoursOver,
                $shiftNotes,
                $now,
            ]);
        }

        $pdo->commit();

        if ($recordAudit) {
            $afterSnapshot = work_day_get_snapshot($userId, $date);
            $tot = round($totalOrd + $totalOver, 2);
            $action = ($beforeSnapshot === null) ? 'day.create' : 'day.update';
            $actionLabel = ($beforeSnapshot === null) ? 'Aggiunta giornata ' : 'Modificata giornata ';
            $summary = $actionLabel . $date . ' (' . format_hours_human($tot);
            if ($totalOver > 0) $summary .= ', di cui ' . format_hours_human($totalOver) . ' straord.';
            if ($dayType !== 'work') $summary .= " - $dayType";
            $summary .= ')';

            $actor = $actorUserId ?: (current_user()['id'] ?? $userId);
            audit_log_record($actor, $userId, $action, 'work_day', $date, $summary, $beforeSnapshot, $afterSnapshot);
        }

        return $dayId;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Deletes a work day and its shifts.
 * Automatically records audit trail with before snapshot so it can be restored.
 */
function work_day_delete(int $userId, string $date, bool $recordAudit = true, ?int $actorUserId = null): void
{
    $beforeSnapshot = $recordAudit ? work_day_get_snapshot($userId, $date) : null;

    $st = db()->prepare('SELECT id FROM ' . t('work_days') . ' WHERE user_id = ? AND work_date = ?');
    $st->execute([$userId, $date]);
    $dayId = $st->fetchColumn();
    if ($dayId) {
        db()->prepare('DELETE FROM ' . t('work_days') . ' WHERE id = ?')->execute([(int)$dayId]);
    }

    if ($recordAudit && $beforeSnapshot) {
        $actor = $actorUserId ?: (current_user()['id'] ?? $userId);
        audit_log_record($actor, $userId, 'day.delete', 'work_day', $date, "Eliminata giornata $date", $beforeSnapshot, null);
    }
}

// --- CRONOLOGIA & AUDIT LOG ---

function audit_log_record(int $userId, int $targetUserId, string $action, string $entityType, string $entityId, string $summary, ?array $before = null, ?array $after = null): int
{
    $pdo = db();
    $now = now_utc();
    $st = $pdo->prepare('
        INSERT INTO ' . t('audit_log') . '
        (user_id, target_user_id, action, entity_type, entity_id, summary, before_state, after_state, is_undone, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?)
    ');
    $st->execute([
        $userId,
        $targetUserId,
        $action,
        $entityType,
        $entityId,
        $summary,
        $before !== null ? json_encode($before, JSON_UNESCAPED_UNICODE) : null,
        $after !== null ? json_encode($after, JSON_UNESCAPED_UNICODE) : null,
        $now,
    ]);
    return (int)$pdo->lastInsertId();
}

function audit_log_list(int $viewerUserId, int $targetUserId = 0, int $limit = 60, int $offset = 0): array
{
    $viewer = user_get_by_id($viewerUserId);
    $isAdmin = $viewer && ((int)$viewer['role'] >= ROLE_ADMIN);

    $sql = '
        SELECT
            a.*,
            u.name AS actor_name,
            u.email AS actor_email,
            u.role AS actor_role,
            tu.name AS target_name,
            tu.email AS target_email,
            und.name AS undone_by_name
        FROM ' . t('audit_log') . ' a
        LEFT JOIN ' . t('users') . ' u ON u.id = a.user_id
        LEFT JOIN ' . t('users') . ' tu ON tu.id = a.target_user_id
        LEFT JOIN ' . t('users') . ' und ON und.id = a.undone_by_user_id
    ';

    $params = [];
    if (!$isAdmin) {
        // Standard user only sees changes to their own sheet
        $sql .= ' WHERE a.target_user_id = ?';
        $params[] = $viewerUserId;
    } elseif ($targetUserId > 0) {
        $sql .= ' WHERE a.target_user_id = ?';
        $params[] = $targetUserId;
    }

    $sql .= ' ORDER BY a.id DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;

    $st = db()->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll();

    return array_map(function ($r) use ($isAdmin) {
        $isUndoableAction = in_array($r['action'], ['day.create', 'day.update', 'day.delete', 'day.preset'], true);
        return [
            'id' => (int)$r['id'],
            'action' => (string)$r['action'],
            'entity_type' => (string)$r['entity_type'],
            'entity_id' => (string)$r['entity_id'],
            'summary' => (string)$r['summary'],
            'user_id' => (int)$r['user_id'],
            'actor_name' => (string)($r['actor_name'] ?? 'Utente'),
            'actor_role' => (int)($r['actor_role'] ?? 1),
            'actor_role_label' => ROLE_LABELS[(int)($r['actor_role'] ?? 1)] ?? 'Utente',
            'target_user_id' => (int)$r['target_user_id'],
            'target_name' => (string)($r['target_name'] ?? 'Lavoratore'),
            'is_undone' => (bool)$r['is_undone'],
            'undone_at' => (string)($r['undone_at'] ?? ''),
            'undone_by_name' => (string)($r['undone_by_name'] ?? ''),
            // CRITICAL: only administrators can undo changes!
            'can_undo' => $isAdmin && $isUndoableAction && !(bool)$r['is_undone'],
            'has_before' => !empty($r['before_state']),
            'has_after' => !empty($r['after_state']),
            'created_at' => (string)$r['created_at'],
        ];
    }, $rows);
}

function audit_log_undo(int $logId, int $adminUserId): array
{
    $admin = user_get_by_id($adminUserId);
    if (!$admin || (int)$admin['role'] < ROLE_ADMIN) {
        throw new RuntimeException('Solo gli amministratori possono annullare le modifiche.');
    }

    $st = db()->prepare('SELECT * FROM ' . t('audit_log') . ' WHERE id = ?');
    $st->execute([$logId]);
    $entry = $st->fetch();
    if (!$entry) {
        throw new RuntimeException('Voce di cronologia non trovata.');
    }

    if ((int)$entry['is_undone'] === 1) {
        throw new RuntimeException('Questa modifica è già stata annullata in precedenza.');
    }

    $targetUserId = (int)$entry['target_user_id'];
    $date = (string)$entry['entity_id'];
    $action = (string)$entry['action'];

    $beforeState = !empty($entry['before_state']) ? json_decode((string)$entry['before_state'], true) : null;
    $afterState = !empty($entry['after_state']) ? json_decode((string)$entry['after_state'], true) : null;

    $now = now_utc();

    // Revert logic:
    if ($action === 'day.create') {
        // It was a newly added day -> undoing means deleting the day!
        work_day_delete($targetUserId, $date, false);
    } elseif ($action === 'day.delete') {
        // It was deleted -> undoing means restoring beforeState!
        if ($beforeState) {
            work_day_save($targetUserId, $date, $beforeState['day_type'] ?? 'work', $beforeState['notes'] ?? null, $beforeState['shifts'] ?? [], false);
        }
    } elseif ($action === 'day.update' || $action === 'day.preset') {
        // It was updated -> restore beforeState!
        if ($beforeState) {
            work_day_save($targetUserId, $date, $beforeState['day_type'] ?? 'work', $beforeState['notes'] ?? null, $beforeState['shifts'] ?? [], false);
        } else {
            work_day_delete($targetUserId, $date, false);
        }
    } else {
        throw new RuntimeException('Questo tipo di operazione non supporta l\'annullamento automatico.');
    }

    // Mark as undone
    db()->prepare('
        UPDATE ' . t('audit_log') . '
        SET is_undone = 1, undone_at = ?, undone_by_user_id = ?
        WHERE id = ?
    ')->execute([$now, $adminUserId, $logId]);

    // Record an audit log for the undo itself
    audit_log_record(
        $adminUserId,
        $targetUserId,
        'day.rollback',
        'audit_log',
        (string)$logId,
        'Annullata modifica #' . $logId . ' (' . $entry['summary'] . ')',
        $afterState,
        $beforeState
    );

    $parts = explode('-', $date);
    $updatedMonth = work_days_get_month($targetUserId, (int)$parts[0], (int)$parts[1]);

    return [
        'ok' => true,
        'undone_id' => $logId,
        'target_user_id' => $targetUserId,
        'date' => $date,
        'month_data' => $updatedMonth,
    ];
}

// --- PRESETS ---

function presets_get_for_user(int $userId): array
{
    $st = db()->prepare('SELECT * FROM ' . t('presets') . ' WHERE user_id = ? ORDER BY is_default DESC, id ASC');
    $st->execute([$userId]);
    $rows = $st->fetchAll();

    if (!empty($rows)) {
        return array_map(function ($r) {
            return [
                'id' => (int)$r['id'],
                'name' => (string)$r['name'],
                'shifts' => json_decode((string)$r['shifts_json'], true) ?: [],
                'is_default' => (bool)$r['is_default'],
            ];
        }, $rows);
    }

    // Standard preset defaults
    return [
        [
            'id' => 1,
            'name' => 'Turno Invernale Standard (9 ore: 8 ord + 1 straord)',
            'is_default' => true,
            'shifts' => [
                ['start_time' => '07:00', 'end_time' => '12:00', 'is_overtime' => false, 'notes' => ''],
                ['start_time' => '13:00', 'end_time' => '16:00', 'is_overtime' => false, 'notes' => ''],
                ['start_time' => '16:00', 'end_time' => '17:00', 'is_overtime' => true, 'notes' => ''],
            ],
        ],
        [
            'id' => 2,
            'name' => 'Turno Estivo Continuato (9 ore: 8 ord + 1 straord)',
            'is_default' => false,
            'shifts' => [
                ['start_time' => '06:00', 'end_time' => '14:00', 'is_overtime' => false, 'notes' => ''],
                ['start_time' => '14:00', 'end_time' => '15:00', 'is_overtime' => true, 'notes' => ''],
            ],
        ],
        [
            'id' => 3,
            'name' => 'Sabato Straordinario (5 ore)',
            'is_default' => false,
            'shifts' => [
                ['start_time' => '07:00', 'end_time' => '12:00', 'is_overtime' => true, 'notes' => ''],
            ],
        ],
        [
            'id' => 4,
            'name' => 'Solo Mattina (07:00 - 12:00, 5 ore ord.)',
            'is_default' => false,
            'shifts' => [
                ['start_time' => '07:00', 'end_time' => '12:00', 'is_overtime' => false, 'notes' => ''],
            ],
        ],
        [
            'id' => 5,
            'name' => 'Solo Pomeriggio (13:00 - 16:00, 3 ore ord.)',
            'is_default' => false,
            'shifts' => [
                ['start_time' => '13:00', 'end_time' => '16:00', 'is_overtime' => false, 'notes' => ''],
            ],
        ],
    ];
}
