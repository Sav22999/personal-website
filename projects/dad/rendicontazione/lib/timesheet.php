<?php
/**
 * Rendicontazione Ore: days, time slots, monthly and yearly totals, quick schedules.
 *
 * A day "snapshot" is the plain array ['kind' => ..., 'note' => ..., 'slots' => [[start, end, overtime]]]
 * and null means "nothing recorded". All amounts are whole minutes, so totals are always exact.
 */

declare(strict_types=1);

const DAY_KINDS = ['lavoro', 'pioggia', 'permesso', 'ferie', 'malattia'];
const MAX_SLOTS = 8;
const MAX_NOTE = 1000;

const MONTH_NAMES = [1 => 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'];
const WEEKDAY_NAMES = [1 => 'lunedì', 'martedì', 'mercoledì', 'giovedì', 'venerdì', 'sabato', 'domenica'];

/** "07:30" -> 450; null when it is not a valid time. */
function time_to_minutes($value): ?int
{
    if (!is_string($value) || !preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', trim($value), $m)) return null;
    $h = (int)$m[1];
    $min = (int)$m[2];
    return ($h <= 23 && $min <= 59) ? $h * 60 + $min : null;
}

function minutes_to_time(int $minutes): string
{
    return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
}

function valid_date($date): bool
{
    if (!is_string($date) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) return false;
    return checkdate((int)$m[2], (int)$m[3], (int)$m[1]) && (int)$m[1] >= 2000 && (int)$m[1] <= 2100;
}

/** "venerdì 6 novembre 2026" */
function date_label(string $date): string
{
    $ts = strtotime($date . ' 12:00:00');
    return WEEKDAY_NAMES[(int)date('N', $ts)] . ' ' . (int)date('j', $ts) . ' ' . MONTH_NAMES[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

/** Validates time slots: returns them sorted and canonical, or throws a UserError. */
function normalize_slots($slots): array
{
    if (!is_array($slots)) throw new UserError('Gli orari non sono validi.');
    if (count($slots) > MAX_SLOTS) throw new UserError('Puoi inserire al massimo ' . MAX_SLOTS . ' fasce orarie.');

    $parsed = [];
    foreach ($slots as $slot) {
        $start = time_to_minutes($slot['start'] ?? null);
        $end = time_to_minutes($slot['end'] ?? null);
        if ($start === null || $end === null) throw new UserError('Compila sia l\'inizio sia la fine di ogni fascia oraria.');
        if ($end <= $start) {
            throw new UserError('La fine (' . minutes_to_time($end) . ') deve venire dopo l\'inizio (' . minutes_to_time($start) . ').');
        }
        $parsed[] = [$start, $end, !empty($slot['overtime'])];
    }
    usort($parsed, function ($a, $b) {
        return $a[0] <=> $b[0];
    });

    $out = [];
    $previousEnd = -1;
    foreach ($parsed as [$start, $end, $overtime]) {
        if ($start < $previousEnd) {
            throw new UserError('Due fasce orarie si sovrappongono: controlla ' . minutes_to_time($start) . '.');
        }
        $previousEnd = $end;
        $out[] = ['start' => minutes_to_time($start), 'end' => minutes_to_time($end), 'overtime' => $overtime];
    }
    return $out;
}

/** Validates a day coming from outside and returns its snapshot. */
function normalize_day(array $input): array
{
    $kind = (string)($input['kind'] ?? 'lavoro');
    if (!in_array($kind, DAY_KINDS, true)) throw new UserError('Tipo di giornata non valido.');

    $slots = normalize_slots($input['slots'] ?? []);
    $note = trim((string)($input['note'] ?? ''));
    if (mb_strlen($note) > MAX_NOTE) throw new UserError('La nota è troppo lunga (massimo ' . MAX_NOTE . ' caratteri).');
    if ($kind === 'lavoro' && !$slots && $note === '') throw new UserError('Inserisci almeno una fascia oraria.');

    return ['kind' => $kind, 'note' => $note, 'slots' => $slots];
}

/** [ordinary minutes, overtime minutes] of a list of slots. */
function slots_minutes(array $slots): array
{
    $ordinary = 0;
    $overtime = 0;
    foreach ($slots as $s) {
        $duration = time_to_minutes($s['end']) - time_to_minutes($s['start']);
        if ($s['overtime']) {
            $overtime += $duration;
        } else {
            $ordinary += $duration;
        }
    }
    return [$ordinary, $overtime];
}

function row_to_snapshot(array $row): array
{
    return [
        'kind' => (string)$row['kind'],
        'note' => (string)($row['note'] ?? ''),
        'slots' => json_decode((string)$row['slots_json'], true) ?: [],
    ];
}

function day_get(int $userId, string $date): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('days') . ' WHERE user_id = ? AND work_date = ?');
    $st->execute([$userId, $date]);
    $row = $st->fetch();
    return $row ? row_to_snapshot($row) : null;
}

/** Writes a snapshot (null deletes the day). Callers record the change in the history. */
function day_put(int $userId, string $date, ?array $snapshot, int $actorId): void
{
    db()->prepare('DELETE FROM ' . t('days') . ' WHERE user_id = ? AND work_date = ?')->execute([$userId, $date]);
    if ($snapshot === null) return;

    [$ordinary, $overtime] = slots_minutes($snapshot['slots']);
    db()->prepare('INSERT INTO ' . t('days') . ' (user_id, work_date, kind, note, slots_json, ordinary_min, overtime_min, updated_at, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([$userId, $date, $snapshot['kind'], $snapshot['note'], json_encode($snapshot['slots']), $ordinary, $overtime, now_utc(), $actorId]);
}

/** Every recorded day between two dates with its running total, plus the totals of the period. */
function days_between(int $userId, string $from, string $to): array
{
    $st = db()->prepare('SELECT * FROM ' . t('days') . ' WHERE user_id = ? AND work_date >= ? AND work_date <= ? ORDER BY work_date ASC');
    $st->execute([$userId, $from, $to]);

    $days = [];
    $ordinary = 0;
    $overtime = 0;
    $counts = ['worked' => 0, 'pioggia' => 0, 'permesso' => 0, 'ferie' => 0, 'malattia' => 0];
    foreach ($st->fetchAll() as $row) {
        $o = (int)$row['ordinary_min'];
        $x = (int)$row['overtime_min'];
        $ordinary += $o;
        $overtime += $x;
        if ($o + $x > 0) $counts['worked']++;
        if ($row['kind'] !== 'lavoro') $counts[$row['kind']]++;
        $days[] = row_to_snapshot($row) + [
            'date' => substr((string)$row['work_date'], 0, 10),
            'ordinary' => $o,
            'overtime' => $x,
            'running' => $ordinary + $overtime,
        ];
    }
    return [
        'days' => $days,
        'totals' => ['ordinary' => $ordinary, 'overtime' => $overtime, 'total' => $ordinary + $overtime] + $counts,
    ];
}

function month_data(int $userId, int $year, int $month): array
{
    $from = sprintf('%04d-%02d-01', $year, $month);
    return ['year' => $year, 'month' => $month] + days_between($userId, $from, date('Y-m-t', strtotime($from)));
}

/** The Monday-to-Sunday week that contains a date. */
function week_data(int $userId, string $date): array
{
    $noon = strtotime($date . ' 12:00:00');
    $start = date('Y-m-d', strtotime('-' . ((int)date('N', $noon) - 1) . ' days', $noon));
    $end = date('Y-m-d', strtotime('+6 days', strtotime($start . ' 12:00:00')));
    return ['start' => $start, 'end' => $end] + days_between($userId, $start, $end);
}

/** The twelve months of a year and the year's totals. */
function year_data(int $userId, int $year): array
{
    $months = [];
    for ($m = 1; $m <= 12; $m++) {
        $months[$m] = ['month' => $m, 'ordinary' => 0, 'overtime' => 0, 'total' => 0, 'worked' => 0, 'pioggia' => 0, 'permesso' => 0, 'ferie' => 0, 'malattia' => 0];
    }
    $st = db()->prepare('SELECT work_date, kind, ordinary_min, overtime_min FROM ' . t('days') . ' WHERE user_id = ? AND work_date >= ? AND work_date <= ?');
    $st->execute([$userId, "$year-01-01", "$year-12-31"]);
    foreach ($st->fetchAll() as $row) {
        $m = (int)substr((string)$row['work_date'], 5, 2);
        $o = (int)$row['ordinary_min'];
        $x = (int)$row['overtime_min'];
        $months[$m]['ordinary'] += $o;
        $months[$m]['overtime'] += $x;
        $months[$m]['total'] += $o + $x;
        if ($o + $x > 0) $months[$m]['worked']++;
        if ($row['kind'] !== 'lavoro') $months[$m][$row['kind']]++;
    }
    $totals = [];
    foreach (['ordinary', 'overtime', 'total', 'worked', 'pioggia', 'permesso', 'ferie', 'malattia'] as $key) {
        $totals[$key] = array_sum(array_column($months, $key));
    }

    $st = db()->prepare('SELECT MIN(work_date) AS first_date FROM ' . t('days') . ' WHERE user_id = ?');
    $st->execute([$userId]);
    $first = (string)($st->fetchColumn() ?: '');

    return [
        'year' => $year,
        'months' => array_values($months),
        'totals' => $totals,
        'firstYear' => $first !== '' ? (int)substr($first, 0, 4) : $year,
    ];
}

/**
 * The usual hours, learned from the latest days: the hours change with the seasons, so the most
 * frequent pattern of the last five normal days (weekdays and Saturdays apart) beats a fixed setting.
 */
function usual_slots(int $userId): array
{
    $st = db()->prepare('SELECT work_date, slots_json FROM ' . t('days') . " WHERE user_id = ? AND kind = 'lavoro' AND ordinary_min + overtime_min > 0 ORDER BY work_date DESC LIMIT 40");
    $st->execute([$userId]);

    $weekdays = [];
    $saturdays = [];
    foreach ($st->fetchAll() as $row) {
        if ((int)date('N', strtotime((string)$row['work_date'])) === 6) {
            $saturdays[] = (string)$row['slots_json'];
        } else {
            $weekdays[] = (string)$row['slots_json'];
        }
    }
    $pick = function (array $recent, array $fallback): array {
        $counts = array_count_values(array_slice($recent, 0, 5));
        if (!$counts) return $fallback;
        arsort($counts);   // stable: on a tie the most recent pattern stays first
        return json_decode((string)array_key_first($counts), true) ?: $fallback;
    };
    return [
        'weekday' => $pick($weekdays, default_presets()[0]['slots']),
        'saturday' => $pick($saturdays, default_presets()[4]['slots']),
    ];
}

function slot(string $start, string $end, bool $overtime = false): array
{
    return ['start' => $start, 'end' => $end, 'overtime' => $overtime];
}

/** The schedules actually found in the 2026 timesheet. */
function default_presets(): array
{
    return [
        ['name' => 'Mattina e pomeriggio', 'kind' => 'lavoro', 'slots' => [slot('07:00', '12:00'), slot('13:00', '16:00')]],
        ['name' => 'Con 1 ora di straordinario', 'kind' => 'lavoro', 'slots' => [slot('07:00', '12:00'), slot('13:00', '16:00'), slot('16:00', '17:00', true)]],
        ['name' => 'Orario continuato', 'kind' => 'lavoro', 'slots' => [slot('06:00', '14:00')]],
        ['name' => 'Continuato con straordinario', 'kind' => 'lavoro', 'slots' => [slot('06:00', '14:00'), slot('14:00', '15:00', true)]],
        ['name' => 'Sabato', 'kind' => 'lavoro', 'slots' => [slot('07:00', '12:00', true)]],
        ['name' => 'Pioggia, a casa', 'kind' => 'pioggia', 'slots' => [slot('07:00', '11:00')]],
    ];
}

function presets_get(int $userId): array
{
    $st = db()->prepare('SELECT presets_json FROM ' . t('schedules') . ' WHERE user_id = ?');
    $st->execute([$userId]);
    $json = $st->fetchColumn();
    return $json ? (json_decode((string)$json, true) ?: []) : default_presets();
}

function presets_save(int $userId, $presets): array
{
    if (!is_array($presets) || count($presets) > 12) throw new UserError('Puoi salvare al massimo 12 orari rapidi.');
    $clean = [];
    foreach ($presets as $p) {
        $name = trim((string)($p['name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 40) throw new UserError('Ogni orario rapido deve avere un nome (massimo 40 caratteri).');
        $kind = (string)($p['kind'] ?? 'lavoro');
        if (!in_array($kind, DAY_KINDS, true)) throw new UserError('Tipo di giornata non valido.');
        $slots = normalize_slots($p['slots'] ?? []);
        if (!$slots) throw new UserError('Ogni orario rapido deve avere almeno una fascia oraria.');
        $clean[] = ['name' => $name, 'kind' => $kind, 'slots' => $slots];
    }
    in_transaction(function () use ($userId, $clean) {
        db()->prepare('DELETE FROM ' . t('schedules') . ' WHERE user_id = ?')->execute([$userId]);
        db()->prepare('INSERT INTO ' . t('schedules') . ' (user_id, presets_json, updated_at) VALUES (?, ?, ?)')
            ->execute([$userId, json_encode($clean, JSON_UNESCAPED_UNICODE), now_utc()]);
    });
    return $clean;
}
