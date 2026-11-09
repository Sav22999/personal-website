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
    'bit' => ['character', 0], 'pixel' => ['character', 0], 'duck' => ['character', 40], 'hamster' => ['character', 50],
    'frog' => ['character', 60], 'owl' => ['character', 60], 'hedgehog' => ['character', 70], 'turtle' => ['character', 70],
    'fox' => ['character', 80], 'crab' => ['character', 80], 'bee' => ['character', 80], 'axolotl' => ['character', 90],
    'octopus' => ['character', 100], 'ghost' => ['character', 100], 'snake' => ['character', 100], 'racoon' => ['character', 110],
    'sloth' => ['character', 110], 'snail' => ['character', 120], 'penguin' => ['character', 120], 'gopher' => ['character', 120],
    'corgi' => ['character', 130], 'panda' => ['character', 140], 'chameleon' => ['character', 140], 'koala' => ['character', 150],
    'alien' => ['character', 150], 'redpanda' => ['character', 160], 'narwhal' => ['character', 170], 'shark' => ['character', 180],
    'dragon' => ['character', 200], 'viking' => ['character', 210], 'pirate' => ['character', 210], 'ninja' => ['character', 220],
    'anvil' => ['character', 250], 'wizard' => ['character', 260], 'cyborg' => ['character', 280],
    'punto' => ['character', 300], 'sillo' => ['character', 300], 'dedo' => ['character', 300], 'neuro' => ['character', 300],
    'favilla' => ['character', 300], 'astronaut' => ['character', 300], 'griffin' => ['character', 320], 'phoenix' => ['character', 350], 'unicorn' => ['character', 400],
    // Accessories
    'glasses' => ['accessory', 0], 'bandage' => ['accessory', 30], 'bowtie' => ['accessory', 40], 'flower' => ['accessory', 40],
    'sprout' => ['accessory', 50], 'mustache' => ['accessory', 50], 'bandana' => ['accessory', 60], 'beanie' => ['accessory', 70],
    'cap' => ['accessory', 80], 'beret' => ['accessory', 80], 'eyepatch' => ['accessory', 80], 'scarf' => ['accessory', 90],
    'heartglasses' => ['accessory', 90], 'shades' => ['accessory', 100], 'antlers' => ['accessory', 100], 'monocle' => ['accessory', 110],
    'chef' => ['accessory', 110], 'coffee' => ['accessory', 120], 'gradcap' => ['accessory', 130], 'headphones' => ['accessory', 140],
    'tophat' => ['accessory', 150], 'halo' => ['accessory', 160], 'vikinghelm' => ['accessory', 170], 'wizardhat' => ['accessory', 180],
    'vr' => ['accessory', 200], 'tiara' => ['accessory', 220], 'crown' => ['accessory', 250],
    // Frames
    'ring' => ['frame', 0], 'dashed' => ['frame', 40], 'wood' => ['frame', 50], 'copper_ring' => ['frame', 60],
    'pixel_frame' => ['frame', 70], 'neon' => ['frame', 90], 'ice' => ['frame', 100], 'emerald' => ['frame', 110],
    'gear' => ['frame', 120], 'sakura' => ['frame', 130], 'gold_ring' => ['frame', 140], 'circuit' => ['frame', 150],
    'lightning_ring' => ['frame', 160], 'laurel' => ['frame', 180], 'rainbow_ring' => ['frame', 200], 'fire_ring' => ['frame', 220],
    'galaxy_ring' => ['frame', 260], 'royal' => ['frame', 300],
    // Backgrounds
    'violet' => ['background', 0], 'night' => ['background', 0], 'ember' => ['background', 40], 'mint' => ['background', 40],
    'sky' => ['background', 40], 'ocean' => ['background', 40], 'forest' => ['background', 40], 'lavender' => ['background', 40],
    'coral' => ['background', 40], 'teal' => ['background', 40], 'crimson' => ['background', 50], 'indigo' => ['background', 50],
    'amber' => ['background', 50], 'slate' => ['background', 50], 'gold' => ['background', 60], 'pink' => ['background', 60],
    'coal' => ['background', 60], 'cherry' => ['background', 70], 'copper' => ['background', 90], 'arctic' => ['background', 100],
    'jungle' => ['background', 100], 'aurora' => ['background', 120], 'terminal' => ['background', 120], 'cyber' => ['background', 120],
    'magma' => ['background', 130], 'synthwave' => ['background', 140], 'sunset' => ['background', 150], 'nebula' => ['background', 150],
    'forge' => ['background', 160], 'matrix' => ['background', 170], 'blueprint' => ['background', 180], 'prism' => ['background', 200],
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
    $ins = $pdo->prepare("INSERT IGNORE INTO `{$prefix}avatar_items` (id, kind, price, sort) VALUES (?,?,?,?)");
    $sort = 0;
    foreach (AVATAR_ITEMS as $id => [$kind, $price]) $ins->execute([$id, $kind, $price, $sort++]);
    $st = $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)");
    $st->execute([(string)SCHEMA_VERSION]);
}
