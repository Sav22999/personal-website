<?php
/**
 * Giftwise — database tables (all prefixed, default "gw_"). Every statement is idempotent.
 *
 * Privacy model: claims ("I'm thinking about it" / "I'll get it") and comments belong to the FRIENDS
 * of a list owner. The API never returns them to the owner of the list (see api/index.php).
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
            `email` VARCHAR(190) NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL DEFAULT '',
            `google_sub` VARCHAR(64) NULL,
            `invite_code` CHAR(10) NOT NULL,
            `lang` CHAR(2) NOT NULL DEFAULT 'en',
            `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
            `created_at` DATETIME NOT NULL,
            `last_login_at` DATETIME NULL,
            `last_seen_at` DATETIME NULL,
            UNIQUE KEY `uq_email` (`email`),
            UNIQUE KEY `uq_google` (`google_sub`),
            UNIQUE KEY `uq_invite` (`invite_code`)
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}auth_tokens` (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `csrf_token` CHAR(48) NOT NULL,
            `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
            `ip` VARCHAR(45) NOT NULL DEFAULT '',
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
        // One row per pair of users (user_a < user_b); requested_by = who sent the request
        "CREATE TABLE IF NOT EXISTS `{$p}friendships` (
            `user_a` INT UNSIGNED NOT NULL,
            `user_b` INT UNSIGNED NOT NULL,
            `status` ENUM('pending','accepted') NOT NULL DEFAULT 'pending',
            `requested_by` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            `accepted_at` DATETIME NULL,
            PRIMARY KEY (`user_a`, `user_b`),
            KEY `ix_b` (`user_b`),
            CONSTRAINT `fk_{$p}fr_a` FOREIGN KEY (`user_a`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}fr_b` FOREIGN KEY (`user_b`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}lists` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `owner_id` INT UNSIGNED NOT NULL,
            `title` VARCHAR(80) NOT NULL,
            `occasion` VARCHAR(20) NOT NULL DEFAULT 'other',
            `event_date` DATE NULL,
            `description` VARCHAR(500) NOT NULL DEFAULT '',
            `visibility` ENUM('friends','selected') NOT NULL DEFAULT 'friends',
            `archived` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            KEY `ix_owner` (`owner_id`),
            CONSTRAINT `fk_{$p}list_owner` FOREIGN KEY (`owner_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // Friends who can see a list with visibility = 'selected'
        "CREATE TABLE IF NOT EXISTS `{$p}list_viewers` (
            `list_id` INT UNSIGNED NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`list_id`, `user_id`),
            CONSTRAINT `fk_{$p}lv_list` FOREIGN KEY (`list_id`) REFERENCES `{$p}lists` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}lv_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}items` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `list_id` INT UNSIGNED NOT NULL,
            `title` VARCHAR(160) NOT NULL,
            `url` VARCHAR(2000) NOT NULL DEFAULT '',
            `image_url` VARCHAR(2000) NOT NULL DEFAULT '',
            `price` DECIMAL(10,2) NULL,
            `currency` CHAR(3) NOT NULL DEFAULT 'EUR',
            `notes` VARCHAR(1000) NOT NULL DEFAULT '',
            `priority` TINYINT NOT NULL DEFAULT 0,
            `quantity` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            `sort` INT NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            KEY `ix_list` (`list_id`),
            CONSTRAINT `fk_{$p}item_list` FOREIGN KEY (`list_id`) REFERENCES `{$p}lists` (`id`) ON DELETE CASCADE
        ) $e",
        // Secret: what friends plan to buy (never visible to the list owner)
        "CREATE TABLE IF NOT EXISTS `{$p}claims` (
            `item_id` INT UNSIGNED NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `status` ENUM('considering','taken') NOT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            PRIMARY KEY (`item_id`, `user_id`),
            KEY `ix_user` (`user_id`),
            CONSTRAINT `fk_{$p}cl_item` FOREIGN KEY (`item_id`) REFERENCES `{$p}items` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}cl_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // Secret: friends' conversation about a list (item_id NULL) or about one wish
        "CREATE TABLE IF NOT EXISTS `{$p}comments` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `list_id` INT UNSIGNED NOT NULL,
            `item_id` INT UNSIGNED NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `body` VARCHAR(1000) NOT NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_list` (`list_id`, `created_at`),
            KEY `ix_item` (`item_id`),
            CONSTRAINT `fk_{$p}cm_list` FOREIGN KEY (`list_id`) REFERENCES `{$p}lists` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}cm_item` FOREIGN KEY (`item_id`) REFERENCES `{$p}items` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}cm_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
    ];
}

/** Creates the tables (idempotent) and records the schema version. */
function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute([(string)SCHEMA_VERSION]);
}
