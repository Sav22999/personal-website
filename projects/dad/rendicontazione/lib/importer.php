<?php
/**
 * Rendicontazione Ore — CSV / Excel importer.
 * Parses the Excel/CSV timesheet format and saves work days & shifts.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/models.php';

function parse_time_string_to_hours(string $val): float
{
    $val = trim($val);
    if ($val === '' || $val === '00:00:00' || $val === '0') return 0.0;
    if (preg_match('/^(\d+):(\d{2})(?::(\d{2}))?$/', $val, $m)) {
        $h = (int)$m[1];
        $min = (int)$m[2];
        $sec = (int)($m[3] ?? 0);
        return round($h + ($min / 60) + ($sec / 3600), 4);
    }
    return (float)$val;
}

function parse_date_with_context(string $raw, int $year, int $month): ?string
{
    $raw = trim($raw);
    if ($raw === '') return null;
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $raw, $m)) {
        return $raw;
    }
    if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/', $raw, $m)) {
        $d = (int)$m[1];
        $mo = (int)$m[2];
        $y = (int)$m[3];
        if ($y < 100) $y += 2000;
        return sprintf('%04d-%02d-%02d', $y, $mo, $d);
    }
    if (preg_match('/(\d{1,2})/', $raw, $m)) {
        $d = (int)$m[1];
        if ($d >= 1 && $d <= 31) {
            return sprintf('%04d-%02d-%02d', $year, $month, $d);
        }
    }
    return null;
}

/**
 * Imports CSV data into the work_days and work_shifts tables for a user.
 * Returns statistics about imported days and shifts.
 */
function import_timesheet_csv(int $userId, string $csvContent, int $defaultYear = 2026): array
{
    $lines = preg_split('/\r\n|\r|\n/', $csvContent);
    $currentMonth = 0;
    $currentDayRaw = null;
    $groupedDays = [];

    foreach ($lines as $line) {
        if (strpos($line, 'Data,Orario inizio') === 0 || strpos($line, 'Data,Orario Inizio') === 0) {
            $currentMonth++;
            $currentDayRaw = null;
            continue;
        }
        if ($currentMonth === 0) {
            // Check if line indicates year
            if (preg_match('/^(\d{4})/i', $line, $m)) {
                $defaultYear = (int)$m[1];
            }
            continue;
        }
        if (trim($line) === '') continue;

        $row = str_getcsv($line, ',', '"', '\\');
        $dateCol = trim($row[0] ?? '');
        $startCol = trim($row[1] ?? '');
        $endCol = trim($row[2] ?? '');
        $overtimeCol = trim($row[3] ?? '');
        $ordCol = trim($row[4] ?? '');
        $strCol = trim($row[5] ?? '');
        $noteCol = trim($row[7] ?? '');

        // Skip completely empty placeholder rows
        if ($dateCol === '' && $startCol === '' && $endCol === '' && ($noteCol === '' || $noteCol === 'False')) {
            continue;
        }

        if ($dateCol !== '') {
            $currentDayRaw = $dateCol;
        }

        $dateStr = parse_date_with_context($currentDayRaw, $defaultYear, $currentMonth);
        if (!$dateStr) continue;

        if (!isset($groupedDays[$dateStr])) {
            $groupedDays[$dateStr] = [
                'day_type' => 'work',
                'notes' => [],
                'shifts' => [],
            ];
        }

        if ($noteCol !== '') {
            $groupedDays[$dateStr]['notes'][] = $noteCol;
            $noteLower = mb_strtolower($noteCol);
            if (strpos($noteLower, 'malattia') !== false) {
                $groupedDays[$dateStr]['day_type'] = 'malattia';
            } elseif (strpos($noteLower, 'ferie') !== false) {
                $groupedDays[$dateStr]['day_type'] = 'ferie';
            } elseif (strpos($noteLower, 'permesso') !== false) {
                $groupedDays[$dateStr]['day_type'] = 'permesso';
            } elseif (strpos($noteLower, 'pioggia') !== false) {
                $groupedDays[$dateStr]['day_type'] = 'pioggia';
            }
        }

        $isOvertime = (strtolower($overtimeCol) === 'true' || $overtimeCol === '1');
        $ordHours = parse_time_string_to_hours($ordCol);
        $strHours = parse_time_string_to_hours($strCol);

        if ($startCol !== '' || $endCol !== '' || $ordHours > 0 || $strHours > 0) {
            $groupedDays[$dateStr]['shifts'][] = [
                'start_time' => $startCol ?: null,
                'end_time' => $endCol ?: null,
                'is_overtime' => $isOvertime,
                'hours_ordinary' => $ordHours,
                'hours_overtime' => $strHours,
                'notes' => $noteCol ?: null,
            ];
        }
    }

    $daysSaved = 0;
    $shiftsSaved = 0;

    foreach ($groupedDays as $dateStr => $dayInfo) {
        $combinedNotes = implode(' | ', array_unique($dayInfo['notes']));
        work_day_save($userId, $dateStr, $dayInfo['day_type'], $combinedNotes ?: null, $dayInfo['shifts']);
        $daysSaved++;
        $shiftsSaved += count($dayInfo['shifts']);
    }

    return [
        'ok' => true,
        'days_count' => $daysSaved,
        'shifts_count' => $shiftsSaved,
        'date_min' => array_key_first($groupedDays),
        'date_max' => array_key_last($groupedDays),
    ];
}
