<?php
/**
 * SavComponents — database tables (all prefixed, default "sc_"). Every statement is idempotent.
 *
 * users          accounts (email unique and never changed; status active/disabled, set by a master admin)
 * auth_tokens    sessions (SHA-256 of the cookie token) with their CSRF token
 * email_tokens   single-use links: confirm the email (kind verify) or choose a new password (kind reset)
 * attempts       rate limiting per IP
 * favorites      components a user starred (by their docs id, e.g. "button")
 * items          saved configurations: kind "theme" (style, mode, colours… for the whole site) or
 *                "component" (one component with its attributes and tokens). public_id is the address of the
 *                theme CSS that external sites link to (api/?r=theme.css&id=…).
 * sav_flows      "Continue with SavAccount" in progress: bound to the key of the browser that started it, the state and the
 *                hand-off code (hashes only), what SavAccount answered and what is left to do (new account, proof of an
 *                existing one with a code). They last 15 minutes.
 * academy        SavComponents Academy (savcomponents.com/app): the progress of an account (lessons, quizzes, challenges,
 *                badges, streak) as JSON, its XP and level, and the username shown on the leaderboard, if the user joins it.
 */

declare(strict_types=1);

function schema_statements(string $p): array
{
    $e = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $fk = fn(string $name, string $col, string $table) => "CONSTRAINT `fk_{$p}{$name}` FOREIGN KEY (`$col`) REFERENCES `{$p}$table` (`id`) ON DELETE CASCADE";
    return [
        "CREATE TABLE IF NOT EXISTS `{$p}meta` (
            `k` VARCHAR(40) NOT NULL PRIMARY KEY,
            `v` VARCHAR(255) NOT NULL
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(80) NOT NULL,
            `email` VARCHAR(190) NOT NULL,
            `email_verified_at` DATETIME NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `sav_sub` VARCHAR(190) NULL,
            `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
            `created_at` DATETIME NOT NULL,
            `last_login_at` DATETIME NULL,
            `last_seen_at` DATETIME NULL,
            UNIQUE KEY `uq_email` (`email`),
            UNIQUE KEY `uq_sav` (`sav_sub`)
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}auth_tokens` (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `csrf_token` CHAR(48) NOT NULL,
            `created_at` DATETIME NOT NULL,
            `last_seen_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`),
            " . $fk('tok_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}email_tokens` (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `kind` ENUM('verify','reset') NOT NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`, `kind`),
            " . $fk('etok_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}attempts` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `kind` VARCHAR(20) NOT NULL,
            `ip` VARCHAR(45) NOT NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_kind_ip` (`kind`, `ip`, `created_at`)
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}favorites` (
            `user_id` INT UNSIGNED NOT NULL,
            `component` VARCHAR(40) NOT NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `component`),
            " . $fk('fav_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}items` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `public_id` CHAR(16) NOT NULL,
            `kind` ENUM('theme','component') NOT NULL,
            `component` VARCHAR(40) NULL,
            `name` VARCHAR(80) NOT NULL,
            `config` TEXT NOT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_public` (`public_id`),
            KEY `ix_user` (`user_id`, `kind`),
            " . $fk('item_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}sav_flows` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `key_hash` CHAR(64) NOT NULL,
            `state_hash` CHAR(64) NOT NULL,
            `handoff_hash` CHAR(64) NULL,
            `mode` ENUM('login','connect') NOT NULL,
            `back` VARCHAR(20) NOT NULL,
            `user_id` INT UNSIGNED NULL,
            `nonce` VARCHAR(64) NOT NULL,
            `verifier` VARCHAR(80) NOT NULL,
            `result` VARCHAR(20) NULL,
            `target_id` INT UNSIGNED NULL,
            `sub` VARCHAR(190) NULL,
            `email` VARCHAR(190) NULL,
            `name` VARCHAR(80) NULL,
            `code_hash` CHAR(64) NULL,
            `code_tries` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `code_until` DATETIME NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_state` (`state_hash`),
            UNIQUE KEY `uq_handoff` (`handoff_hash`),
            KEY `ix_expires` (`expires_at`)
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}academy` (
            `user_id` INT UNSIGNED NOT NULL PRIMARY KEY,
            `username` VARCHAR(24) NULL,
            `xp` INT UNSIGNED NOT NULL DEFAULT 0,
            `level` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            `streak` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `on_board` TINYINT(1) NOT NULL DEFAULT 0,
            `progress` MEDIUMTEXT NOT NULL,
            `updated_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_username` (`username`),
            KEY `ix_board` (`on_board`, `xp`),
            " . $fk('academy_user', 'user_id', 'users') . "
        ) $e",
    ];
}

function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    // version 3: tables made before it get the SavAccount column
    if (!$pdo->query("SHOW COLUMNS FROM `{$prefix}users` LIKE 'sav_sub'")->fetch()) {
        $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `sav_sub` VARCHAR(190) NULL AFTER `password_hash`, ADD UNIQUE KEY `uq_sav` (`sav_sub`)");
    }
    $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")
        ->execute([(string)SCHEMA_VERSION]);
}
