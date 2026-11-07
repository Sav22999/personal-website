<?php
/**
 * ThinkHop — database tables (all prefixed, default "th_"). Every statement is idempotent.
 *
 * The questions never reach the server: it only keeps the account and the summary of each finished game,
 * so the statistics are the same on every device.
 */

declare(strict_types=1);

function schema_statements(string $p): array
{
    $e = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    return [
        "CREATE TABLE IF NOT EXISTS `{$p}meta` (
            `k` VARCHAR(40) NOT NULL PRIMARY KEY,
            `v` VARCHAR(255) NOT NULL
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(80) NOT NULL,
            `username` VARCHAR(20) NULL,
            `show_in_ranking` TINYINT(1) NOT NULL DEFAULT 1,
            `notify_off` VARCHAR(200) NULL,
            `notify_lang` CHAR(2) NULL,
            `email` VARCHAR(190) NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL DEFAULT '',
            `google_sub` VARCHAR(64) NULL,
            `role` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
            `created_at` DATETIME NOT NULL,
            `last_login_at` DATETIME NULL,
            UNIQUE KEY `uq_email` (`email`),
            UNIQUE KEY `uq_username` (`username`),
            UNIQUE KEY `uq_google` (`google_sub`)
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}auth_tokens` (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `csrf_token` CHAR(48) NOT NULL,
            `created_at` DATETIME NOT NULL,
            `last_seen_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`),
            CONSTRAINT `fk_{$p}tok_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}attempts` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `kind` VARCHAR(20) NOT NULL,
            `ip` VARCHAR(45) NOT NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_kind_ip` (`kind`, `ip`, `created_at`)
        ) $e",
        // One row per finished game. `uid` is made by the device: sending the same game twice changes nothing.
        "CREATE TABLE IF NOT EXISTS `{$p}games` (
            `user_id` INT UNSIGNED NOT NULL,
            `uid` CHAR(16) NOT NULL,
            `mode` ENUM('level','survival','daily') NOT NULL,
            `stage` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `stars` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `score` INT UNSIGNED NOT NULL,
            `spent` INT UNSIGNED NOT NULL DEFAULT 0,
            `correct` SMALLINT UNSIGNED NOT NULL,
            `total` SMALLINT UNSIGNED NOT NULL,
            `seconds` INT UNSIGNED NOT NULL,
            `cats` VARCHAR(400) NOT NULL DEFAULT '{}',
            `day` DATE NOT NULL,
            `played_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `uid`),
            KEY `ix_user_day` (`user_id`, `day`),
            KEY `ix_played` (`played_at`),
            CONSTRAINT `fk_{$p}game_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // Questions written in the admin panel: `data` is {"it": [question, explanation, [right, wrong, wrong, wrong]], "en": […], …}
        "CREATE TABLE IF NOT EXISTS `{$p}questions` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `tier` ENUM('easy','medium','hard','expert') NOT NULL,
            `difficulty` DECIMAL(3,2) NOT NULL DEFAULT 0.25,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `data` TEXT NOT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL
        ) $e",
        // One row for each friend added: it goes one way, and only the username is ever shown
        "CREATE TABLE IF NOT EXISTS `{$p}friends` (
            `user_id` INT UNSIGNED NOT NULL,
            `friend_id` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `friend_id`),
            KEY `ix_friend` (`friend_id`),
            CONSTRAINT `fk_{$p}fr_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}fr_friend` FOREIGN KEY (`friend_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // Devices to wake up with Web Push (see lib/push.php)
        "CREATE TABLE IF NOT EXISTS `{$p}push_subscriptions` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `endpoint` VARCHAR(700) NOT NULL,
            `endpoint_hash` CHAR(64) NOT NULL,
            `last_push_at` DATETIME NULL,
            `created_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_endpoint` (`endpoint_hash`),
            KEY `ix_user` (`user_id`),
            CONSTRAINT `fk_{$p}push_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // What is waiting to be told: rows go as soon as the device reads them, or after 3 days
        "CREATE TABLE IF NOT EXISTS `{$p}notifications` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `type` VARCHAR(20) NOT NULL,
            `day` CHAR(10) NOT NULL,
            `ref` VARCHAR(20) NOT NULL DEFAULT '',
            `data` VARCHAR(200) NOT NULL DEFAULT '{}',
            `created_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_once` (`user_id`, `type`, `day`, `ref`),
            CONSTRAINT `fk_{$p}note_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // What the admin panel changes in the game (see game_settings in api/index.php)
        "CREATE TABLE IF NOT EXISTS `{$p}settings` (
            `k` VARCHAR(40) NOT NULL PRIMARY KEY,
            `v` TEXT NOT NULL
        ) $e",
    ];
}

function has_column(PDO $pdo, string $table, string $column): bool
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $st->execute([$table, $column]);
    return (int)$st->fetchColumn() > 0;
}

function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    // Version 2: the expert difficulty
    $pdo->exec("ALTER TABLE `{$prefix}questions` MODIFY `tier` ENUM('easy','medium','hard','expert') NOT NULL");
    // Version 3: public usernames and the numeric difficulty of the questions
    if (!has_column($pdo, $prefix . 'users', 'username')) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `username` VARCHAR(20) NULL AFTER `name`, ADD UNIQUE KEY `uq_username` (`username`)");
    if (!has_column($pdo, $prefix . 'questions', 'difficulty')) {
        $pdo->exec("ALTER TABLE `{$prefix}questions` ADD COLUMN `difficulty` DECIMAL(3,2) NOT NULL DEFAULT 0.25 AFTER `tier`");
        $pdo->exec("UPDATE `{$prefix}questions` SET difficulty = CASE tier WHEN 'easy' THEN 0.25 WHEN 'medium' THEN 0.75 WHEN 'hard' THEN 1.25 ELSE 1.75 END");
    }
    // Version 4: everyone chooses whether to appear in the ranking
    if (!has_column($pdo, $prefix . 'users', 'show_in_ranking')) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `show_in_ranking` TINYINT(1) NOT NULL DEFAULT 1 AFTER `username`");
    // Version 5: notifications, each type on or off
    if (!has_column($pdo, $prefix . 'users', 'notify_off')) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `notify_off` VARCHAR(200) NULL AFTER `show_in_ranking`, ADD COLUMN `notify_lang` CHAR(2) NULL AFTER `notify_off`");
    $st = $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)");
    $st->execute([(string)SCHEMA_VERSION]);
}
