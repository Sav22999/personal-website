<?php
/**
 * OpenNewsletter — database tables (all prefixed, default "on_"). Every statement is idempotent.
 *
 * users            accounts (email unique and never changed)
 * groups           one newsletter publisher each: its subscribers, its outgoing address (smtp_sealed, encrypted)
 * members          who is in a group and with which role (user, moderator, publisher, admin, owner)
 * invites          invitations to people without an account yet: they become members once their email is confirmed
 * subscribers      the people who receive a group's newsletters (pending until they confirm the email)
 * templates        layouts saved by a group (the built-in ones live in lib/templates.php)
 * newsletters      draft -> pending (proposed slot) -> scheduled (approved) -> sending -> sent;
 *                  or rejected / expired (slot passed without approval) / cancelled / failed
 * deliveries       one row per subscriber of a newsletter that is being sent (the cron resumes from here)
 * images           pictures uploaded in the editor, public (they are shown in the emails)
 */

declare(strict_types=1);

function schema_statements(string $p): array
{
    $e = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $fk = fn(string $name, string $col, string $table, string $on = 'CASCADE') => "CONSTRAINT `fk_{$p}{$name}` FOREIGN KEY (`$col`) REFERENCES `{$p}$table` (`id`) ON DELETE $on";
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
            `password_hash` VARCHAR(255) NOT NULL DEFAULT '',
            `google_sub` VARCHAR(64) NULL,
            `ui_lang` VARCHAR(5) NOT NULL DEFAULT 'en',
            `fav_templates` VARCHAR(1000) NOT NULL DEFAULT '[]',
            `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
            `created_at` DATETIME NOT NULL,
            `last_login_at` DATETIME NULL,
            UNIQUE KEY `uq_email` (`email`),
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
            " . $fk('tok_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}email_tokens` (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`),
            " . $fk('etok_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}attempts` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `kind` VARCHAR(20) NOT NULL,
            `ip` VARCHAR(45) NOT NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_kind_ip` (`kind`, `ip`, `created_at`)
        ) $e",
        // slug: the public address of the subscription page. langs: JSON list of subscriber languages.
        // page: JSON customisation of the subscription pages. smtp_sealed: SMTP settings encrypted with $secret_key.
        "CREATE TABLE IF NOT EXISTS `{$p}groups` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `slug` VARCHAR(40) NOT NULL,
            `name` VARCHAR(80) NOT NULL,
            `description` VARCHAR(300) NOT NULL DEFAULT '',
            `owner_id` INT UNSIGNED NULL,
            `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
            `timezone` VARCHAR(64) NOT NULL DEFAULT 'Europe/Rome',
            `langs` VARCHAR(255) NOT NULL DEFAULT '[\"en\"]',
            `page` TEXT NULL,
            `terms` TEXT NULL,
            `terms_rev` CHAR(12) NOT NULL DEFAULT '',
            `from_email` VARCHAR(190) NOT NULL DEFAULT '',
            `from_name` VARCHAR(80) NOT NULL DEFAULT '',
            `reply_to` VARCHAR(190) NOT NULL DEFAULT '',
            `smtp_sealed` TEXT NULL,
            `smtp_checked_at` DATETIME NULL,
            `rate_per_hour` SMALLINT UNSIGNED NOT NULL DEFAULT 200,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_slug` (`slug`),
            " . $fk('grp_owner', 'owner_id', 'users', 'SET NULL') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}members` (
            `group_id` INT UNSIGNED NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `role` ENUM('user','moderator','publisher','admin','owner') NOT NULL DEFAULT 'user',
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`group_id`, `user_id`),
            KEY `ix_user` (`user_id`),
            " . $fk('mem_group', 'group_id', 'groups') . ",
            " . $fk('mem_user', 'user_id', 'users') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}invites` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `group_id` INT UNSIGNED NOT NULL,
            `email` VARCHAR(190) NOT NULL,
            `role` ENUM('user','moderator','publisher','admin') NOT NULL DEFAULT 'user',
            `invited_by` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_group_email` (`group_id`, `email`),
            KEY `ix_email` (`email`),
            " . $fk('inv_group', 'group_id', 'groups') . "
        ) $e",
        // token: goes in every email (unsubscribe link). confirm_hash: SHA-256 of the double opt-in link.
        // browser / browser_lang: what the subscription page saw (browser name only, never the whole User-Agent).
        "CREATE TABLE IF NOT EXISTS `{$p}subscribers` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `group_id` INT UNSIGNED NOT NULL,
            `email` VARCHAR(190) NOT NULL,
            `lang` VARCHAR(5) NOT NULL,
            `browser_lang` VARCHAR(5) NOT NULL DEFAULT '',
            `browser` VARCHAR(30) NOT NULL DEFAULT '',
            `source` ENUM('page','widget','import') NOT NULL DEFAULT 'page',
            `status` ENUM('pending','active','unsubscribed') NOT NULL DEFAULT 'pending',
            `token` CHAR(32) NOT NULL,
            `confirm_hash` CHAR(64) NULL,
            `confirm_sent_at` DATETIME NULL,
            `terms_rev` CHAR(12) NOT NULL DEFAULT '',
            `created_at` DATETIME NOT NULL,
            `confirmed_at` DATETIME NULL,
            `unsubscribed_at` DATETIME NULL,
            `unsub_reason` VARCHAR(500) NOT NULL DEFAULT '',
            UNIQUE KEY `uq_group_email` (`group_id`, `email`),
            UNIQUE KEY `uq_token` (`token`),
            KEY `ix_group_status` (`group_id`, `status`, `lang`),
            KEY `ix_confirm` (`confirm_hash`),
            " . $fk('sub_group', 'group_id', 'groups') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}templates` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `group_id` INT UNSIGNED NOT NULL,
            `name` VARCHAR(80) NOT NULL,
            `doc` MEDIUMTEXT NOT NULL,
            `created_by` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            KEY `ix_group` (`group_id`),
            " . $fk('tpl_group', 'group_id', 'groups') . "
        ) $e",
        // lang: the subscribers it goes to (NULL = all languages). proposed_at / scheduled_at: UTC, on a 30-minute slot.
        "CREATE TABLE IF NOT EXISTS `{$p}newsletters` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `group_id` INT UNSIGNED NOT NULL,
            `subject` VARCHAR(200) NOT NULL DEFAULT '',
            `preheader` VARCHAR(200) NOT NULL DEFAULT '',
            `lang` VARCHAR(5) NULL,
            `doc` MEDIUMTEXT NOT NULL,
            `status` ENUM('draft','pending','scheduled','rejected','expired','cancelled','sending','sent','failed') NOT NULL DEFAULT 'draft',
            `author_id` INT UNSIGNED NULL,
            `proposed_at` DATETIME NULL,
            `scheduled_at` DATETIME NULL,
            `submitted_at` DATETIME NULL,
            `reviewer_id` INT UNSIGNED NULL,
            `reviewed_at` DATETIME NULL,
            `review_note` VARCHAR(500) NOT NULL DEFAULT '',
            `reminded_at` DATETIME NULL,
            `started_at` DATETIME NULL,
            `sent_at` DATETIME NULL,
            `total` INT UNSIGNED NOT NULL DEFAULT 0,
            `sent_count` INT UNSIGNED NOT NULL DEFAULT 0,
            `failed_count` INT UNSIGNED NOT NULL DEFAULT 0,
            `rev` INT UNSIGNED NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            KEY `ix_group_status` (`group_id`, `status`),
            KEY `ix_due` (`status`, `scheduled_at`),
            " . $fk('nl_group', 'group_id', 'groups') . ",
            " . $fk('nl_author', 'author_id', 'users', 'SET NULL') . ",
            " . $fk('nl_reviewer', 'reviewer_id', 'users', 'SET NULL') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}deliveries` (
            `newsletter_id` INT UNSIGNED NOT NULL,
            `subscriber_id` INT UNSIGNED NOT NULL,
            `status` ENUM('queued','sent','failed') NOT NULL DEFAULT 'queued',
            `error` VARCHAR(200) NOT NULL DEFAULT '',
            `sent_at` DATETIME NULL,
            PRIMARY KEY (`newsletter_id`, `subscriber_id`),
            KEY `ix_queue` (`newsletter_id`, `status`),
            " . $fk('del_nl', 'newsletter_id', 'newsletters') . ",
            " . $fk('del_sub', 'subscriber_id', 'subscribers') . "
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}images` (
            `id` CHAR(20) NOT NULL PRIMARY KEY,
            `group_id` INT UNSIGNED NOT NULL,
            `mime` VARCHAR(20) NOT NULL,
            `width` SMALLINT UNSIGNED NOT NULL,
            `height` SMALLINT UNSIGNED NOT NULL,
            `bytes` INT UNSIGNED NOT NULL,
            `data` MEDIUMBLOB NOT NULL,
            `created_by` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            KEY `ix_group` (`group_id`),
            " . $fk('img_group', 'group_id', 'groups') . "
        ) $e",
    ];
}

function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    // v3: subscribers imported from a CSV by an admin (source 'import')
    $type = (string)$pdo->query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$prefix}subscribers' AND COLUMN_NAME = 'source'")->fetchColumn();
    if ($type !== '' && strpos($type, 'import') === false) $pdo->exec("ALTER TABLE `{$prefix}subscribers` MODIFY `source` ENUM('page','widget','import') NOT NULL DEFAULT 'page'");
    // v3: the built-in templates each user marked as favourites
    $has = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$prefix}users' AND COLUMN_NAME = 'fav_templates'")->fetchColumn();
    if (!(int)$has) $pdo->exec("ALTER TABLE `{$prefix}users` ADD `fav_templates` VARCHAR(1000) NOT NULL DEFAULT '[]' AFTER `ui_lang`");
    // v2: the hourly sending limit of each group (tables created by v1 do not have it)
    $has = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$prefix}groups' AND COLUMN_NAME = 'rate_per_hour'")->fetchColumn();
    if (!(int)$has) $pdo->exec("ALTER TABLE `{$prefix}groups` ADD `rate_per_hour` SMALLINT UNSIGNED NOT NULL DEFAULT 200 AFTER `smtp_checked_at`");
    $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")
        ->execute([(string)SCHEMA_VERSION]);
}
