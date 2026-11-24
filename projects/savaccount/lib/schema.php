<?php
/**
 * SavAccount — database tables (prefix in credentials.php, default "sva_"). Every statement is idempotent.
 *
 * users           accounts: first and last name, password (Argon2id, peppered), two-step settings
 * emails          addresses of an account; verified_key is the address once verified (unique among all accounts)
 * sessions        browser sessions on savaccount.eu (SHA-256 of the token)
 * challenges      pending steps: confirm an address, second step of a sign-in, new password (codes and links are hashed)
 * recovery_codes  single-use codes for when the second step is not available
 * attempts        rate limits (keyed hash of the IP address or email)
 * events          security log of an account (90 days)
 * clients         the services that can use SavAccount
 * grants          what a user shares with a service
 * codes, tokens   OAuth authorization codes, access and refresh tokens (hashed)
 * signing_keys    RSA keys that sign the ID tokens (private part encrypted)
 */

declare(strict_types=1);

function schema_statements(string $p): array
{
    $e = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $bin = 'CHARACTER SET ascii COLLATE ascii_bin';
    $fk = fn(string $n, string $col, string $table, string $on = 'CASCADE') => "CONSTRAINT `fk_{$p}{$n}` FOREIGN KEY (`$col`) REFERENCES `{$p}$table` (`id`) ON DELETE $on";
    return [
        "CREATE TABLE IF NOT EXISTS `{$p}meta` (`k` VARCHAR(40) NOT NULL PRIMARY KEY, `v` VARCHAR(255) NOT NULL) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `uid` CHAR(32) $bin NOT NULL,
            `first_name` VARCHAR(60) NOT NULL,
            `last_name` VARCHAR(60) NOT NULL,
            `nickname` VARCHAR(40) NOT NULL DEFAULT '',
            `birthdate` DATE NULL,
            `locale` CHAR(2) NOT NULL DEFAULT 'it',
            `password_hash` VARCHAR(255) NOT NULL,
            `password_changed_at` DATETIME NOT NULL,
            `totp_secret` VARCHAR(255) NULL,
            `totp_pending` VARCHAR(255) NULL,
            `totp_enabled_at` DATETIME NULL,
            `totp_last_step` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `email_2fa` TINYINT(1) NOT NULL DEFAULT 0,
            `status` ENUM('pending','active','disabled') NOT NULL DEFAULT 'pending',
            `terms_at` DATETIME NOT NULL,
            `created_at` DATETIME NOT NULL,
            `last_login_at` DATETIME NULL,
            UNIQUE KEY `uq_uid` (`uid`),
            KEY `ix_status` (`status`, `created_at`)
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}emails` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `email` VARCHAR(190) COLLATE utf8mb4_bin NOT NULL,
            `verified_key` VARCHAR(190) COLLATE utf8mb4_bin NULL,
            `verified_at` DATETIME NULL,
            `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_verified` (`verified_key`),
            UNIQUE KEY `uq_user_email` (`user_id`, `email`),
            KEY `ix_email` (`email`),
            " . $fk('em_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}sessions` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `token_hash` CHAR(64) $bin NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `csrf` CHAR(43) $bin NOT NULL,
            `device` VARCHAR(60) NOT NULL,
            `ip` VARCHAR(45) NOT NULL,
            `amr` VARCHAR(60) NOT NULL,
            `remember` TINYINT(1) NOT NULL,
            `auth_time` DATETIME NOT NULL,
            `sudo_until` DATETIME NULL,
            `created_at` DATETIME NOT NULL,
            `last_seen_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_token` (`token_hash`),
            KEY `ix_user` (`user_id`),
            KEY `ix_expires` (`expires_at`),
            " . $fk('se_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}challenges` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `token_hash` CHAR(64) $bin NOT NULL,
            `kind` ENUM('register','login','email','reset','email2fa') NOT NULL,
            `user_id` INT UNSIGNED NULL,
            `email_id` INT UNSIGNED NULL,
            `code_hash` CHAR(64) $bin NULL,
            `link_hash` CHAR(64) $bin NULL,
            `data` VARCHAR(500) NOT NULL DEFAULT '{}',
            `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `sent_at` DATETIME NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_token` (`token_hash`),
            UNIQUE KEY `uq_link` (`link_hash`),
            KEY `ix_expires` (`expires_at`),
            " . $fk('ch_user', 'user_id', 'users') . ",
            " . $fk('ch_email', 'email_id', 'emails') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}recovery_codes` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `code_hash` CHAR(64) $bin NOT NULL,
            UNIQUE KEY `uq_code` (`user_id`, `code_hash`),
            " . $fk('rc_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}attempts` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `kind` VARCHAR(20) NOT NULL,
            `subject` CHAR(64) $bin NOT NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_subject` (`kind`, `subject`, `created_at`),
            KEY `ix_created` (`created_at`)
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}events` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `kind` VARCHAR(30) NOT NULL,
            `detail` VARCHAR(190) NOT NULL DEFAULT '',
            `device` VARCHAR(60) NOT NULL,
            `ip` VARCHAR(45) NOT NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`, `id`),
            KEY `ix_created` (`created_at`),
            " . $fk('ev_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}clients` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `client_id` CHAR(24) $bin NOT NULL,
            `secret_hash` CHAR(64) $bin NULL,
            `type` ENUM('confidential','public') NOT NULL,
            `name` VARCHAR(80) NOT NULL,
            `description` VARCHAR(300) NOT NULL DEFAULT '',
            `homepage_url` VARCHAR(500) NOT NULL DEFAULT '',
            `logo_url` VARCHAR(500) NOT NULL DEFAULT '',
            `privacy_url` VARCHAR(500) NOT NULL DEFAULT '',
            `terms_url` VARCHAR(500) NOT NULL DEFAULT '',
            `redirect_uris` TEXT NOT NULL,
            `logout_uris` TEXT NOT NULL,
            `scopes_required` VARCHAR(200) NOT NULL,
            `scopes_optional` VARCHAR(200) NOT NULL DEFAULT '',
            `pairwise` TINYINT(1) NOT NULL DEFAULT 1,
            `require_2fa` TINYINT(1) NOT NULL DEFAULT 0,
            `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_client` (`client_id`)
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}grants` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `client_id` INT UNSIGNED NOT NULL,
            `scopes` VARCHAR(200) NOT NULL,
            `email_id` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            `last_used_at` DATETIME NULL,
            UNIQUE KEY `uq_grant` (`user_id`, `client_id`),
            " . $fk('gr_user', 'user_id', 'users') . ",
            " . $fk('gr_client', 'client_id', 'clients') . ",
            " . $fk('gr_email', 'email_id', 'emails', 'SET NULL') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}codes` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `code_hash` CHAR(64) $bin NOT NULL,
            `family` CHAR(32) $bin NOT NULL,
            `client_id` INT UNSIGNED NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `redirect_uri` VARCHAR(500) NOT NULL,
            `scopes` VARCHAR(200) NOT NULL,
            `nonce` VARCHAR(255) NOT NULL,
            `pkce` CHAR(43) $bin NOT NULL,
            `amr` VARCHAR(60) NOT NULL,
            `auth_time` DATETIME NOT NULL,
            `used_at` DATETIME NULL,
            `expires_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_code` (`code_hash`),
            " . $fk('co_client', 'client_id', 'clients') . ",
            " . $fk('co_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}tokens` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `token_hash` CHAR(64) $bin NOT NULL,
            `kind` ENUM('access','refresh') NOT NULL,
            `family` CHAR(32) $bin NOT NULL,
            `client_id` INT UNSIGNED NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `scopes` VARCHAR(200) NOT NULL,
            `amr` VARCHAR(60) NOT NULL,
            `auth_time` DATETIME NOT NULL,
            `used_at` DATETIME NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_token` (`token_hash`),
            KEY `ix_family` (`family`),
            KEY `ix_expires` (`expires_at`),
            " . $fk('to_client', 'client_id', 'clients') . ",
            " . $fk('to_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}signing_keys` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `kid` CHAR(16) $bin NOT NULL,
            `public_pem` TEXT NOT NULL,
            `private_enc` TEXT NOT NULL,
            `created_at` DATETIME NOT NULL,
            `retired_at` DATETIME NULL,
            UNIQUE KEY `uq_kid` (`kid`)
        ) $e",
    ];
}

function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute([(string)SCHEMA_VERSION]);
}
