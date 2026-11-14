<?php
/**
 * MyCurriculum — database tables (all prefixed, default "mc_"). Every statement is idempotent.
 *
 * One CV per account, kept as JSON on one row: settings (template, colours, what is public), profile (data shared
 * by every language) and content (one full copy of the texts per language). They are always read and saved together.
 * The profile photo has its own row so the CV can be loaded without it.
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
        // username: the public address of the CV (?user=…). email: unique and never changed.
        "CREATE TABLE IF NOT EXISTS `{$p}users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(80) NOT NULL,
            `username` VARCHAR(30) NOT NULL,
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
        "CREATE TABLE IF NOT EXISTS `{$p}cvs` (
            `user_id` INT UNSIGNED NOT NULL PRIMARY KEY,
            `settings` TEXT NOT NULL,
            `profile` TEXT NOT NULL,
            `content` MEDIUMTEXT NOT NULL,
            `views` INT UNSIGNED NOT NULL DEFAULT 0,
            `rev` INT UNSIGNED NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            " . $user('cv_user') . "
        ) $e",
        // The photo is already a small square JPEG (the browser crops and shrinks it before sending)
        "CREATE TABLE IF NOT EXISTS `{$p}photos` (
            `user_id` INT UNSIGNED NOT NULL PRIMARY KEY,
            `mime` VARCHAR(20) NOT NULL,
            `data` MEDIUMBLOB NOT NULL,
            `hash` CHAR(16) NOT NULL,
            `updated_at` DATETIME NOT NULL,
            " . $user('photo_user') . "
        ) $e",
    ];
}

function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    // v2: the revision of each CV (tables created by v1 do not have it)
    $has = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$prefix}cvs' AND COLUMN_NAME = 'rev'")->fetchColumn();
    if (!(int)$has) $pdo->exec("ALTER TABLE `{$prefix}cvs` ADD `rev` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `views`");
    $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")
        ->execute([(string)SCHEMA_VERSION]);
}
