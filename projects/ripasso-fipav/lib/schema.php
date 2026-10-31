<?php
/** Database structure. Used by install.php (only creates missing tables). */

declare(strict_types=1);

const SCHEMA_VERSION = 5;

function schema_statements(): array
{
    $opts = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    return [
        'meta' => 'CREATE TABLE IF NOT EXISTS ' . t('meta') . " (
            `k` VARCHAR(64) NOT NULL PRIMARY KEY,
            `v` VARCHAR(255) NOT NULL
        ) $opts",

        'users' => 'CREATE TABLE IF NOT EXISTS ' . t('users') . " (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(190) NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `permissions` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `status` ENUM('pending','active','disabled') NOT NULL DEFAULT 'pending',
            `must_change_password` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            `approved_at` DATETIME NULL,
            `approved_by` INT UNSIGNED NULL,
            `activation_mail_at` DATETIME NULL,
            `last_login_at` DATETIME NULL,
            `last_seen_at` DATETIME NULL,
            UNIQUE KEY `uq_email` (`email`),
            KEY `idx_status` (`status`)
        ) $opts",

        'auth_tokens' => 'CREATE TABLE IF NOT EXISTS ' . t('auth_tokens') . " (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `csrf_token` CHAR(48) NOT NULL,
            `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
            `ip` VARCHAR(45) NOT NULL DEFAULT '',
            `created_at` DATETIME NOT NULL,
            `last_seen_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            KEY `idx_user` (`user_id`),
            KEY `idx_expires` (`expires_at`),
            CONSTRAINT `fk_" . prefix_raw() . "tok_user` FOREIGN KEY (`user_id`) REFERENCES " . t('users') . " (`id`) ON DELETE CASCADE
        ) $opts",

        'login_attempts' => 'CREATE TABLE IF NOT EXISTS ' . t('login_attempts') . " (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `ip` VARCHAR(45) NOT NULL,
            `email` VARCHAR(190) NOT NULL,
            `success` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            KEY `idx_ip_time` (`ip`, `created_at`),
            KEY `idx_email_time` (`email`, `created_at`)
        ) $opts",

        'refs' => 'CREATE TABLE IF NOT EXISTS ' . t('refs') . " (
            `code` VARCHAR(32) NOT NULL PRIMARY KEY,
            `source` VARCHAR(40) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `summary` TEXT NOT NULL,
            `full_text` MEDIUMTEXT NOT NULL,
            `change_season` VARCHAR(7) NULL,
            `change_type` ENUM('new','changed') NULL,
            `change_note` VARCHAR(300) NOT NULL DEFAULT '',
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            `updated_by` INT UNSIGNED NULL,
            KEY `idx_source` (`source`)
        ) $opts",

        'questions' => 'CREATE TABLE IF NOT EXISTS ' . t('questions') . " (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `code` VARCHAR(32) NOT NULL,
            `text` TEXT NOT NULL,
            `type` ENUM('multiple-choice','yes-no','multi-select') NOT NULL,
            `difficulty` ENUM('e','m','d','dd') NOT NULL DEFAULT 'm',
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            `updated_by` INT UNSIGNED NULL,
            UNIQUE KEY `uq_code` (`code`),
            KEY `idx_active` (`active`)
        ) $opts",

        'answers' => 'CREATE TABLE IF NOT EXISTS ' . t('answers') . " (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `question_id` INT UNSIGNED NOT NULL,
            `position` TINYINT UNSIGNED NOT NULL,
            `value` VARCHAR(8) NOT NULL,
            `text` TEXT NOT NULL,
            `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
            KEY `idx_question` (`question_id`, `position`),
            CONSTRAINT `fk_" . prefix_raw() . "ans_question` FOREIGN KEY (`question_id`) REFERENCES " . t('questions') . " (`id`) ON DELETE CASCADE
        ) $opts",

        'question_refs' => 'CREATE TABLE IF NOT EXISTS ' . t('question_refs') . " (
            `question_id` INT UNSIGNED NOT NULL,
            `ref_code` VARCHAR(32) NOT NULL,
            `position` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`question_id`, `ref_code`),
            KEY `idx_ref` (`ref_code`),
            CONSTRAINT `fk_" . prefix_raw() . "qr_question` FOREIGN KEY (`question_id`) REFERENCES " . t('questions') . " (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_" . prefix_raw() . "qr_ref` FOREIGN KEY (`ref_code`) REFERENCES " . t('refs') . " (`code`) ON UPDATE CASCADE ON DELETE RESTRICT
        ) $opts",

        'documents' => documents_table_sql(),

        'user_state' => 'CREATE TABLE IF NOT EXISTS ' . t('user_state') . " (
            `user_id` INT UNSIGNED NOT NULL,
            `state_key` VARCHAR(32) NOT NULL,
            `value` MEDIUMTEXT NOT NULL,
            `updated_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `state_key`),
            CONSTRAINT `fk_" . prefix_raw() . "state_user` FOREIGN KEY (`user_id`) REFERENCES " . t('users') . " (`id`) ON DELETE CASCADE
        ) $opts",
    ];
}

function documents_table_sql(): string
{
    return 'CREATE TABLE IF NOT EXISTS ' . t('documents') . " (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(160) NOT NULL,
            `edition` VARCHAR(120) NOT NULL DEFAULT '',
            `description` VARCHAR(600) NOT NULL DEFAULT '',
            `file_name` VARCHAR(120) NOT NULL,
            `file_size` INT UNSIGNED NOT NULL DEFAULT 0,
            `position` INT UNSIGNED NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            `updated_by` INT UNSIGNED NULL,
            UNIQUE KEY `uq_file` (`file_name`),
            KEY `idx_position` (`position`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
}

function prefix_raw(): string
{
    return preg_replace('/[^A-Za-z0-9_]/', '', (string)(config()['table_prefix'] ?? 'fr_'));
}

function meta_get(string $k, ?string $default = null): ?string
{
    $st = db()->prepare('SELECT v FROM ' . t('meta') . ' WHERE k = ?');
    $st->execute([$k]);
    $v = $st->fetchColumn();
    return $v === false ? $default : (string)$v;
}

function meta_set(string $k, string $v): void
{
    db()->prepare('INSERT INTO ' . t('meta') . ' (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v)')->execute([$k, $v]);
}
