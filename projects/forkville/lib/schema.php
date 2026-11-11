<?php
/**
 * Forkville — database tables (all prefixed, default "fv_"). Every statement is idempotent.
 *
 * The restaurant keeps its layout, staff and menu as JSON on one row: they are always read and written
 * together. Ingredients, recipes and garden plots have their own rows so they can be changed one at a time.
 */

declare(strict_types=1);

function schema_statements(string $p): array
{
    $e = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $user = fn(string $name) => "CONSTRAINT `fk_{$p}{$name}` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE";
    return [
        "CREATE TABLE IF NOT EXISTS `{$p}meta` (
            `k` VARCHAR(40) NOT NULL PRIMARY KEY,
            `v` VARCHAR(255) NOT NULL
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(80) NOT NULL,
            `username` VARCHAR(20) NOT NULL,
            `email` VARCHAR(190) NOT NULL,
            `email_verified_at` DATETIME NULL,
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
            " . $user('tok_user') . "
        ) $e",
        // Single-use links that confirm an email address: only their hash is kept
        "CREATE TABLE IF NOT EXISTS `{$p}email_tokens` (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`),
            " . $user('etok_user') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}attempts` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `kind` VARCHAR(20) NOT NULL,
            `ip` VARCHAR(45) NOT NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_kind_ip` (`kind`, `ip`, `created_at`)
        ) $e",
        // One restaurant per player
        "CREATE TABLE IF NOT EXISTS `{$p}restaurants` (
            `user_id` INT UNSIGNED NOT NULL PRIMARY KEY,
            `name` VARCHAR(40) NOT NULL,
            `kind` VARCHAR(16) NOT NULL DEFAULT 'generic',
            `coins` BIGINT NOT NULL DEFAULT 0,
            `xp` INT UNSIGNED NOT NULL DEFAULT 0,
            `level` TINYINT UNSIGNED NOT NULL DEFAULT 1,
            `size` TINYINT UNSIGNED NOT NULL DEFAULT 10,
            `depth` TINYINT UNSIGNED NOT NULL DEFAULT 10,
            `parking` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `instance` VARCHAR(24) NULL,
            `floors` VARCHAR(200) NULL,
            `door` TINYINT UNSIGNED NULL,
            `door_style` VARCHAR(20) NOT NULL DEFAULT 'wood',
            `floor` VARCHAR(30) NOT NULL DEFAULT 'floor_oak',
            `wall` VARCHAR(30) NOT NULL DEFAULT 'wall_cream',
            `layout` MEDIUMTEXT NOT NULL,
            `staff` TEXT NOT NULL,
            `candidates` TEXT NULL,
            `menu` VARCHAR(400) NOT NULL DEFAULT '[]',
            `finishes` VARCHAR(600) NOT NULL DEFAULT '[\"floor_oak\",\"wall_cream\"]',
            `wish` VARCHAR(20) NULL,
            `status` ENUM('open','paused','closed') NOT NULL DEFAULT 'open',
            `paid_day` DATE NULL,
            `closed_from` DATETIME NULL,
            `closed_secs` INT UNSIGNED NOT NULL DEFAULT 0,
            `game_minutes` BIGINT UNSIGNED NOT NULL DEFAULT 540,
            `popularity` INT UNSIGNED NOT NULL DEFAULT 10,
            `rating_sum` INT UNSIGNED NOT NULL DEFAULT 0,
            `rating_n` INT UNSIGNED NOT NULL DEFAULT 0,
            `friend_sum` INT UNSIGNED NOT NULL DEFAULT 0,
            `friend_n` INT UNSIGNED NOT NULL DEFAULT 0,
            `served_total` INT UNSIGNED NOT NULL DEFAULT 0,
            `next_item_id` INT UNSIGNED NOT NULL DEFAULT 1,
            `last_report_at` DATETIME NOT NULL,
            `last_seen_at` DATETIME NOT NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_popularity` (`popularity`),
            " . $user('rest_user') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}ingredients` (
            `user_id` INT UNSIGNED NOT NULL,
            `ing` VARCHAR(20) NOT NULL,
            `qty` INT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`user_id`, `ing`),
            " . $user('ing_user') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}recipes` (
            `user_id` INT UNSIGNED NOT NULL,
            `dish` VARCHAR(30) NOT NULL,
            `level` TINYINT UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (`user_id`, `dish`),
            " . $user('rec_user') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}garden` (
            `user_id` INT UNSIGNED NOT NULL,
            `plot` TINYINT UNSIGNED NOT NULL,
            `crop` VARCHAR(20) NOT NULL,
            `planted_at` DATETIME NOT NULL,
            `ready_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `plot`),
            " . $user('gar_user') . "
        ) $e",
        // What each player bought at the market today (the limit is per ingredient per day)
        "CREATE TABLE IF NOT EXISTS `{$p}market_buys` (
            `user_id` INT UNSIGNED NOT NULL,
            `day` DATE NOT NULL,
            `ing` VARCHAR(20) NOT NULL,
            `qty` INT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`user_id`, `day`, `ing`),
            " . $user('mkt_user') . "
        ) $e",
        // Friendships are stored in both directions once accepted; a pending request is one row with accepted = 0
        "CREATE TABLE IF NOT EXISTS `{$p}friends` (
            `user_id` INT UNSIGNED NOT NULL,
            `friend_id` INT UNSIGNED NOT NULL,
            `accepted` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `friend_id`),
            KEY `ix_friend` (`friend_id`),
            " . $user('fr_user') . ",
            CONSTRAINT `fk_{$p}fr_friend` FOREIGN KEY (`friend_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // One paid visit per visitor, host and day; the star rating is optional
        "CREATE TABLE IF NOT EXISTS `{$p}visits` (
            `user_id` INT UNSIGNED NOT NULL,
            `host_id` INT UNSIGNED NOT NULL,
            `day` DATE NOT NULL,
            `rating` TINYINT UNSIGNED NULL,
            `tip` INT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`user_id`, `host_id`, `day`),
            KEY `ix_host` (`host_id`, `day`),
            " . $user('vis_user') . ",
            CONSTRAINT `fk_{$p}vis_host` FOREIGN KEY (`host_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // One free ingredient per giver, receiver and day; the receiver claims it from the inbox
        "CREATE TABLE IF NOT EXISTS `{$p}gifts` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `to_id` INT UNSIGNED NOT NULL,
            `day` DATE NOT NULL,
            `ing` VARCHAR(20) NOT NULL,
            `claimed_at` DATETIME NULL,
            `created_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_day` (`user_id`, `to_id`, `day`),
            KEY `ix_to` (`to_id`, `claimed_at`),
            " . $user('gift_user') . ",
            CONSTRAINT `fk_{$p}gift_to` FOREIGN KEY (`to_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // Customer reviews: the latest ones of each restaurant (older ones are pruned)
        "CREATE TABLE IF NOT EXISTS `{$p}reviews` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `stars` TINYINT UNSIGNED NOT NULL,
            `dish` VARCHAR(30) NOT NULL,
            `name` VARCHAR(20) NOT NULL,
            `good` VARCHAR(120) NOT NULL DEFAULT '',
            `bad` VARCHAR(120) NOT NULL DEFAULT '',
            `created_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`, `id`),
            " . $user('rev_user') . "
        ) $e",
        // Every coin earned or spent (the daily statement in the app); kept for 30 days
        "CREATE TABLE IF NOT EXISTS `{$p}ledger` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `kind` VARCHAR(24) NOT NULL,
            `amount` INT NOT NULL,
            `detail` VARCHAR(120) NOT NULL DEFAULT '',
            `created_at` DATETIME NOT NULL,
            KEY `ix_user_time` (`user_id`, `created_at`),
            " . $user('led_user') . "
        ) $e",
        // Settings changed from the admin panel (key 'catalog': prices and rules, see lib/catalog.php)
        "CREATE TABLE IF NOT EXISTS `{$p}settings` (
            `k` VARCHAR(40) NOT NULL PRIMARY KEY,
            `v` MEDIUMTEXT NOT NULL,
            `updated_at` DATETIME NOT NULL
        ) $e",
        // What the administrators changed, readable in the admin panel
        "CREATE TABLE IF NOT EXISTS `{$p}admin_log` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `admin_id` INT UNSIGNED NULL,
            `user_id` INT UNSIGNED NULL,
            `action` VARCHAR(40) NOT NULL,
            `detail` VARCHAR(500) NOT NULL DEFAULT '',
            `created_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`, `created_at`),
            KEY `ix_time` (`created_at`)
        ) $e",
        // Coins earned per ISO week: the weekly leaderboard
        "CREATE TABLE IF NOT EXISTS `{$p}weekly` (
            `user_id` INT UNSIGNED NOT NULL,
            `week` CHAR(8) NOT NULL,
            `coins` INT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`user_id`, `week`),
            KEY `ix_week` (`week`, `coins`),
            " . $user('wk_user') . "
        ) $e",
    ];
}

function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    // Version 3: open / no new customers / closed
    $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $st->execute([$prefix . 'restaurants', 'status']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `status` ENUM('open','paused','closed') NOT NULL DEFAULT 'open' AFTER `wish`");
    // Version 4: people looking for a job (with their CV)
    $st->execute([$prefix . 'restaurants', 'candidates']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `candidates` TEXT NULL AFTER `staff`");
    // Version 6: kind of restaurant
    $st->execute([$prefix . 'restaurants', 'kind']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `kind` VARCHAR(16) NOT NULL DEFAULT 'generic' AFTER `name`");
    // Version 7: the last day wages were paid
    $st->execute([$prefix . 'restaurants', 'paid_day']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `paid_day` DATE NULL AFTER `status`");
    // Version 8: time spent closed (the staff go home and are not paid)
    $st->execute([$prefix . 'restaurants', 'closed_secs']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `closed_from` DATETIME NULL AFTER `paid_day`, ADD COLUMN `closed_secs` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `closed_from`");
    // Version 9: the restaurant's own calendar (game minutes since it opened: year 0, 1 March, 09:00)
    $st->execute([$prefix . 'restaurants', 'game_minutes']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `game_minutes` BIGINT UNSIGNED NOT NULL DEFAULT 540 AFTER `closed_secs`");
    // Version 10: rooms grow by rows and columns (width = size, depth), the car park, tips between friends
    $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $st->execute([$prefix . 'restaurants', 'depth']);
    if (!(int)$st->fetchColumn()) {
        $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `depth` TINYINT UNSIGNED NOT NULL DEFAULT 10 AFTER `size`, ADD COLUMN `parking` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `depth`");
        $pdo->exec("UPDATE `{$prefix}restaurants` SET depth = size");
    }
    $st->execute([$prefix . 'restaurants', 'instance']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `instance` VARCHAR(24) NULL AFTER `parking`"); // the one open copy of the game
    $st->execute([$prefix . 'restaurants', 'floors']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `floors` VARCHAR(200) NULL AFTER `instance`"); // version 12: upper floors
    $st->execute([$prefix . 'restaurants', 'door']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}restaurants` ADD COLUMN `door` TINYINT UNSIGNED NULL AFTER `floors`, ADD COLUMN `door_style` VARCHAR(20) NOT NULL DEFAULT 'wood' AFTER `door`"); // version 13: the door moves
    $st->execute([$prefix . 'visits', 'tip']);
    if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `{$prefix}visits` ADD COLUMN `tip` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `rating`");
    $st = $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)");
    $st->execute([(string)SCHEMA_VERSION]);
}
