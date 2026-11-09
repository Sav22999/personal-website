<?php
/**
 * ThinkHop — database tables (all prefixed, default "th_"). Every statement is idempotent.
 *
 * The questions never reach the server: it only keeps the account and the summary of each finished game,
 * so the statistics are the same on every device.
 */

declare(strict_types=1);

const AVATAR_ITEMS = [
    // Characters
    'bit' => ['character', 0], 'pixel' => ['character', 0], 'duck' => ['character', 1000], 'hamster' => ['character', 1200],
    'frog' => ['character', 1500], 'owl' => ['character', 1800], 'hedgehog' => ['character', 2200], 'turtle' => ['character', 2500],
    'fox' => ['character', 3500], 'crab' => ['character', 4000], 'bee' => ['character', 4500], 'axolotl' => ['character', 5000],
    'octopus' => ['character', 6000], 'ghost' => ['character', 6500], 'snake' => ['character', 7000], 'racoon' => ['character', 8000],
    'sloth' => ['character', 9000], 'snail' => ['character', 10000], 'penguin' => ['character', 11000], 'gopher' => ['character', 12000],
    'corgi' => ['character', 14000], 'panda' => ['character', 16000], 'chameleon' => ['character', 18000], 'koala' => ['character', 20000],
    'alien' => ['character', 22000], 'redpanda' => ['character', 25000], 'narwhal' => ['character', 28000], 'shark' => ['character', 32000],
    'dragon' => ['character', 36000], 'viking' => ['character', 40000], 'pirate' => ['character', 42000], 'ninja' => ['character', 45000],
    'anvil' => ['character', 48000], 'wizard' => ['character', 52000], 'cyborg' => ['character', 55000],
    'punto' => ['character', 50000], 'sillo' => ['character', 50000], 'dedo' => ['character', 50000], 'neuro' => ['character', 50000],
    'favilla' => ['character', 50000], 'astronaut' => ['character', 50000], 'griffin' => ['character', 65000], 'phoenix' => ['character', 80000], 'unicorn' => ['character', 100000],
    // Accessories
    'glasses' => ['accessory', 0], 'bandage' => ['accessory', 1000], 'bowtie' => ['accessory', 1200], 'flower' => ['accessory', 1500],
    'sprout' => ['accessory', 1800], 'mustache' => ['accessory', 2200], 'bandana' => ['accessory', 2500], 'beanie' => ['accessory', 3500],
    'cap' => ['accessory', 4000], 'beret' => ['accessory', 4500], 'eyepatch' => ['accessory', 5500], 'scarf' => ['accessory', 6500],
    'heartglasses' => ['accessory', 7500], 'shades' => ['accessory', 9000], 'antlers' => ['accessory', 11000], 'monocle' => ['accessory', 13000],
    'chef' => ['accessory', 15000], 'coffee' => ['accessory', 18000], 'gradcap' => ['accessory', 21000], 'headphones' => ['accessory', 25000],
    'tophat' => ['accessory', 30000], 'halo' => ['accessory', 35000], 'vikinghelm' => ['accessory', 40000], 'wizardhat' => ['accessory', 45000],
    'vr' => ['accessory', 50000], 'tiara' => ['accessory', 55000], 'crown' => ['accessory', 75000],
    // Frames
    'ring' => ['frame', 0], 'dashed' => ['frame', 1500], 'wood' => ['frame', 2000], 'copper_ring' => ['frame', 2800],
    'pixel_frame' => ['frame', 4000], 'neon' => ['frame', 6500], 'ice' => ['frame', 8500], 'emerald' => ['frame', 12000],
    'gear' => ['frame', 15000], 'sakura' => ['frame', 18000], 'gold_ring' => ['frame', 22000], 'circuit' => ['frame', 25000],
    'lightning_ring' => ['frame', 32000], 'laurel' => ['frame', 40000], 'rainbow_ring' => ['frame', 48000], 'fire_ring' => ['frame', 55000],
    'galaxy_ring' => ['frame', 75000], 'royal' => ['frame', 100000],
    // Backgrounds
    'violet' => ['background', 0], 'night' => ['background', 0], 'ember' => ['background', 1000], 'mint' => ['background', 1000],
    'sky' => ['background', 1200], 'ocean' => ['background', 1500], 'forest' => ['background', 1800], 'lavender' => ['background', 2000],
    'coral' => ['background', 2200], 'teal' => ['background', 2500], 'crimson' => ['background', 3500], 'indigo' => ['background', 4000],
    'amber' => ['background', 4500], 'slate' => ['background', 5000], 'gold' => ['background', 6000], 'pink' => ['background', 6500],
    'coal' => ['background', 7000], 'cherry' => ['background', 8500], 'copper' => ['background', 10000], 'arctic' => ['background', 12000],
    'jungle' => ['background', 15000], 'aurora' => ['background', 18000], 'terminal' => ['background', 20000], 'cyber' => ['background', 22000],
    'magma' => ['background', 25000], 'synthwave' => ['background', 30000], 'sunset' => ['background', 35000], 'nebula' => ['background', 40000],
    'forge' => ['background', 45000], 'matrix' => ['background', 50000], 'blueprint' => ['background', 55000], 'prism' => ['background', 65000],
];

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
            `avatar` VARCHAR(80) NULL,
            `show_in_ranking` TINYINT(1) NOT NULL DEFAULT 1,
            `notify_off` VARCHAR(200) NULL,
            `notify_hours` VARCHAR(200) NULL,
            `notify_lang` CHAR(2) NULL,
            `email_verified_at` DATETIME NULL,
            `points_adjust` INT NOT NULL DEFAULT 0,
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
        "CREATE TABLE IF NOT EXISTS `{$p}avatar_items` (
            `id` VARCHAR(30) NOT NULL PRIMARY KEY,
            `kind` VARCHAR(12) NOT NULL,
            `price` INT UNSIGNED NOT NULL DEFAULT 0,
            `enabled` TINYINT(1) NOT NULL DEFAULT 1,
            `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 0
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}user_items` (
            `user_id` INT UNSIGNED NOT NULL,
            `item_id` VARCHAR(30) NOT NULL,
            `price` INT UNSIGNED NOT NULL DEFAULT 0,
            `bought_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `item_id`),
            CONSTRAINT `fk_{$p}uitem_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
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
        // Single-use links that confirm an email address: only their hash is kept
        "CREATE TABLE IF NOT EXISTS `{$p}email_tokens` (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`),
            CONSTRAINT `fk_{$p}etok_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // What the administrators changed on an account, readable in the admin panel
        "CREATE TABLE IF NOT EXISTS `{$p}admin_log` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `admin_id` INT UNSIGNED NULL,
            `action` VARCHAR(30) NOT NULL,
            `detail` VARCHAR(500) NOT NULL DEFAULT '{}',
            `created_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`, `created_at`),
            CONSTRAINT `fk_{$p}log_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}log_admin` FOREIGN KEY (`admin_id`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
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
        // What there is to tell: a row is kept 3 days after it is read, so the same reminder is never sent twice
        "CREATE TABLE IF NOT EXISTS `{$p}notifications` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `type` VARCHAR(20) NOT NULL,
            `day` CHAR(10) NOT NULL,
            `ref` VARCHAR(20) NOT NULL DEFAULT '',
            `data` VARCHAR(200) NOT NULL DEFAULT '{}',
            `created_at` DATETIME NOT NULL,
            `sent_at` DATETIME NULL,
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
    // Version 6: notifications read are marked, not deleted
    if (!has_column($pdo, $prefix . 'notifications', 'sent_at')) $pdo->exec("ALTER TABLE `{$prefix}notifications` ADD COLUMN `sent_at` DATETIME NULL AFTER `created_at`");
    // Version 7: confirmed email addresses. Google already checked its accounts; administrators keep their access
    if (!has_column($pdo, $prefix . 'users', 'email_verified_at')) {
        $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `email_verified_at` DATETIME NULL AFTER `notify_lang`");
        $pdo->exec("UPDATE `{$prefix}users` SET email_verified_at = UTC_TIMESTAMP() WHERE google_sub IS NOT NULL OR role >= " . ROLE_ADMIN);
    }
    // Version 8: points changed by an administrator
    if (!has_column($pdo, $prefix . 'users', 'points_adjust')) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `points_adjust` INT NOT NULL DEFAULT 0 AFTER `email_verified_at`");
    // Version 9: profile pictures and the avatar shop
    if (!has_column($pdo, $prefix . 'users', 'avatar')) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `avatar` VARCHAR(80) NULL AFTER `username`");
    // Version 10: rebalanced avatar shop prices
    $ins = $pdo->prepare("INSERT INTO `{$prefix}avatar_items` (id, kind, price, sort) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE price = VALUES(price)");
    $sort = 0;
    foreach (AVATAR_ITEMS as $id => [$kind, $price]) $ins->execute([$id, $kind, $price, $sort++]);
    // Version 11: custom notification hours for non-live reminders
    if (!has_column($pdo, $prefix . 'users', 'notify_hours')) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `notify_hours` VARCHAR(200) NULL AFTER `notify_off`");
    $st = $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)");
    $st->execute([(string)SCHEMA_VERSION]);
}
