<?php
/**
 * Rendicontazione Ore — database schema definition & migration.
 * Compatible with both MySQL 5.7+ / MariaDB and SQLite 3.
 */

declare(strict_types=1);

function schema_create_tables(PDO $pdo, string $p = 'dad_rend_'): void
{
    $isSqlite = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';

    if ($isSqlite) {
        $statements = [
            "CREATE TABLE IF NOT EXISTS `{$p}meta` (
                `k` VARCHAR(64) PRIMARY KEY,
                `v` TEXT NOT NULL
            )",

            "CREATE TABLE IF NOT EXISTS `{$p}users` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(190) NOT NULL UNIQUE,
                `google_sub` VARCHAR(64) NULL UNIQUE,
                `avatar_url` VARCHAR(255) NULL,
                `role` INTEGER NOT NULL DEFAULT 1,
                `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
                `notes` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL,
                `last_login_at` DATETIME NULL
            )",

            "CREATE TABLE IF NOT EXISTS `{$p}auth_tokens` (
                `token_hash` CHAR(64) PRIMARY KEY,
                `user_id` INTEGER NOT NULL,
                `ip` VARCHAR(45) NULL,
                `user_agent` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL,
                `expires_at` DATETIME NOT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS `{$p}work_days` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `user_id` INTEGER NOT NULL,
                `work_date` DATE NOT NULL,
                `day_type` VARCHAR(30) NOT NULL DEFAULT 'work',
                `notes` TEXT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                UNIQUE (`user_id`, `work_date`),
                FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS `{$p}work_shifts` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `day_id` INTEGER NOT NULL,
                `user_id` INTEGER NOT NULL,
                `sort_order` INTEGER NOT NULL DEFAULT 0,
                `start_time` VARCHAR(10) NULL,
                `end_time` VARCHAR(10) NULL,
                `is_overtime` INTEGER NOT NULL DEFAULT 0,
                `hours_ordinary` REAL NOT NULL DEFAULT 0.00,
                `hours_overtime` REAL NOT NULL DEFAULT 0.00,
                `notes` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL,
                FOREIGN KEY (`day_id`) REFERENCES `{$p}work_days` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS `{$p}presets` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `user_id` INTEGER NOT NULL,
                `name` VARCHAR(80) NOT NULL,
                `shifts_json` TEXT NOT NULL,
                `is_default` INTEGER NOT NULL DEFAULT 0,
                `created_at` DATETIME NOT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            )",

            "CREATE INDEX IF NOT EXISTS `idx_{$p}work_days_date` ON `{$p}work_days` (`user_id`, `work_date`)",
            "CREATE INDEX IF NOT EXISTS `idx_{$p}shifts_day` ON `{$p}work_shifts` (`day_id`)",
            "CREATE INDEX IF NOT EXISTS `idx_{$p}shifts_user` ON `{$p}work_shifts` (`user_id`)",
        ];
    } else {
        $opts = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
        $statements = [
            "CREATE TABLE IF NOT EXISTS `{$p}meta` (
                `k` VARCHAR(64) NOT NULL,
                `v` MEDIUMTEXT NOT NULL,
                PRIMARY KEY (`k`)
            ) $opts",

            "CREATE TABLE IF NOT EXISTS `{$p}users` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(190) NOT NULL,
                `google_sub` VARCHAR(64) NULL,
                `avatar_url` VARCHAR(255) NULL,
                `role` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 Utente, 9 Admin, 10 Master Admin',
                `status` ENUM('pending','active','rejected','disabled') NOT NULL DEFAULT 'pending',
                `notes` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL,
                `last_login_at` DATETIME NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_email` (`email`),
                UNIQUE KEY `uq_google` (`google_sub`),
                KEY `idx_role_status` (`role`, `status`)
            ) $opts",

            "CREATE TABLE IF NOT EXISTS `{$p}auth_tokens` (
                `token_hash` CHAR(64) NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `ip` VARCHAR(45) NULL,
                `user_agent` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL,
                `expires_at` DATETIME NOT NULL,
                PRIMARY KEY (`token_hash`),
                KEY `idx_user` (`user_id`),
                CONSTRAINT `fk_{$p}tok_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            ) $opts",

            "CREATE TABLE IF NOT EXISTS `{$p}work_days` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `work_date` DATE NOT NULL,
                `day_type` VARCHAR(30) NOT NULL DEFAULT 'work',
                `notes` TEXT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_user_date` (`user_id`, `work_date`),
                KEY `idx_date` (`work_date`),
                CONSTRAINT `fk_{$p}days_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            ) $opts",

            "CREATE TABLE IF NOT EXISTS `{$p}work_shifts` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `day_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
                `start_time` VARCHAR(10) NULL,
                `end_time` VARCHAR(10) NULL,
                `is_overtime` TINYINT(1) NOT NULL DEFAULT 0,
                `hours_ordinary` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `hours_overtime` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `notes` VARCHAR(255) NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_day` (`day_id`),
                KEY `idx_user` (`user_id`),
                CONSTRAINT `fk_{$p}shifts_day` FOREIGN KEY (`day_id`) REFERENCES `{$p}work_days` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_{$p}shifts_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            ) $opts",

            "CREATE TABLE IF NOT EXISTS `{$p}presets` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `name` VARCHAR(80) NOT NULL,
                `shifts_json` TEXT NOT NULL,
                `is_default` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_user` (`user_id`),
                CONSTRAINT `fk_{$p}preset_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            ) $opts",
        ];
    }

    foreach ($statements as $sql) {
        $pdo->exec($sql);
    }
}

/** Ensure schema exists and metadata is initialized. */
function ensure_schema(?PDO $pdo = null): void
{
    static $ensured = false;
    if ($ensured) return;
    $p = (string)config_value('prefix', 'dad_rend_');
    $pdo = $pdo ?: db();

    schema_create_tables($pdo, $p);

    // Record schema version in meta
    $st = $pdo->prepare('SELECT v FROM ' . t('meta') . ' WHERE k = ?');
    $st->execute(['schema_version']);
    $ver = $st->fetchColumn();
    if ($ver === false) {
        $pdo->prepare('INSERT INTO ' . t('meta') . ' (k, v) VALUES (?, ?)')->execute(['schema_version', (string)SCHEMA_VERSION]);
    }

    $ensured = true;
}
