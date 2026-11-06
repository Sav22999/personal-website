<?php
/**
 * Rendicontazione Ore — 2026 initial data seed.
 * Loads the father's 2026 timesheet records into the database.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/importer.php';

function seed_father_timesheet(int $userId): array
{
    $csvFile = dirname(__DIR__) . '/data/source-2026.csv';
    if (!is_file($csvFile)) {
        return ['ok' => false, 'error' => 'File data/source-2026.csv non trovato.'];
    }

    $csvContent = file_get_contents($csvFile);
    return import_timesheet_csv($userId, $csvContent, 2026);
}
