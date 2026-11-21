<?php
/**
 * Rendicontazione Ore: database schema, valid on MySQL/MariaDB and on SQLite (local development).
 *
 * The "users" and "auth_tokens" tables keep the columns of schema v1, so accounts created by the
 * first version survive. Its work_days/work_shifts/presets/audit_log tables are simply not used.
 */

declare(strict_types=1);

const SCHEMA_VERSION = 2;

function schema_statements(bool $sqlite): array
{
    $id = $sqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
    $ref = $sqlite ? 'INTEGER' : 'INT UNSIGNED';
    $big = $sqlite ? 'TEXT' : 'MEDIUMTEXT';
    $engine = $sqlite ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    $tables = [
        'CREATE TABLE IF NOT EXISTS ' . t('meta') . ' (
            `k` VARCHAR(64) NOT NULL PRIMARY KEY,
            `v` TEXT NOT NULL
        )' . $engine,

        'CREATE TABLE IF NOT EXISTS ' . t('users') . " (
            `id` $id,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(190) NOT NULL UNIQUE,
            `google_sub` VARCHAR(64) NULL UNIQUE,
            `avatar_url` VARCHAR(255) NULL,
            `role` INTEGER NOT NULL DEFAULT 1,
            `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
            `notes` VARCHAR(255) NULL,
            `created_at` DATETIME NOT NULL,
            `last_login_at` DATETIME NULL
        )" . $engine,

        'CREATE TABLE IF NOT EXISTS ' . t('auth_tokens') . " (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` $ref NOT NULL,
            `ip` VARCHAR(45) NULL,
            `user_agent` VARCHAR(255) NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL
        )" . $engine,

        // One row per day: the time slots are a small JSON list, the minutes are kept for quick sums
        'CREATE TABLE IF NOT EXISTS ' . t('days') . " (
            `id` $id,
            `user_id` $ref NOT NULL,
            `work_date` DATE NOT NULL,
            `kind` VARCHAR(20) NOT NULL DEFAULT 'lavoro',
            `note` TEXT NULL,
            `slots_json` TEXT NOT NULL,
            `ordinary_min` INTEGER NOT NULL DEFAULT 0,
            `overtime_min` INTEGER NOT NULL DEFAULT 0,
            `updated_at` DATETIME NOT NULL,
            `updated_by` $ref NOT NULL,
            UNIQUE (`user_id`, `work_date`)
        )" . $engine,

        'CREATE TABLE IF NOT EXISTS ' . t('schedules') . " (
            `user_id` $ref NOT NULL PRIMARY KEY,
            `presets_json` TEXT NOT NULL,
            `updated_at` DATETIME NOT NULL
        )" . $engine,

        // "changes" maps each date to its {before, after} snapshots: that is what makes undo possible
        'CREATE TABLE IF NOT EXISTS ' . t('history') . " (
            `id` $id,
            `actor_id` $ref NOT NULL,
            `target_user_id` $ref NOT NULL,
            `action` VARCHAR(30) NOT NULL,
            `summary` VARCHAR(255) NULL,
            `changes` $big NULL,
            `undo_of` $ref NULL,
            `undone_at` DATETIME NULL,
            `undone_by` $ref NULL,
            `created_at` DATETIME NOT NULL
        )" . $engine,
    ];

    $tables[] = $sqlite
        ? 'CREATE INDEX IF NOT EXISTS ' . index_name('history_target') . ' ON ' . t('history') . ' (`target_user_id`, `id`)'
        : null;

    return array_values(array_filter($tables));
}

function index_name(string $name): string
{
    return '`' . (string)config_value('prefix', 'dad_rend_') . $name . '`';
}

/** Creates the missing tables once, then remembers the schema version. */
function ensure_schema(PDO $pdo): void
{
    try {
        $version = (int)$pdo->query('SELECT v FROM ' . t('meta') . " WHERE k = 'schema_version'")->fetchColumn();
    } catch (Throwable $e) {
        $version = 0;
    }
    if ($version >= SCHEMA_VERSION) return;

    $sqlite = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';
    foreach (schema_statements($sqlite) as $sql) {
        $pdo->exec($sql);
    }
    if (!$sqlite) {
        try {
            $pdo->exec('ALTER TABLE ' . t('history') . ' ADD INDEX ' . index_name('history_target') . ' (`target_user_id`, `id`)');
        } catch (Throwable $e) {
            // already there
        }
    }
    $pdo->prepare('DELETE FROM ' . t('meta') . " WHERE k = 'schema_version'")->execute();
    $pdo->prepare('INSERT INTO ' . t('meta') . " (k, v) VALUES ('schema_version', ?)")->execute([(string)SCHEMA_VERSION]);
}
