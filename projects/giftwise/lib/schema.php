<?php
/**
 * Giftwise — database tables (all prefixed, default "gw_"). Every statement is idempotent.
 *
 * Privacy model: claims ("I'm thinking about it" / "I'll get it") and comments belong to the FOLLOWERS
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
            `username` VARCHAR(20) NULL,
            `follow_policy` ENUM('approve','open') NOT NULL DEFAULT 'approve',
            `lang` CHAR(2) NOT NULL DEFAULT 'en',
            `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
            `email_verified_at` DATETIME NULL,
            `created_at` DATETIME NOT NULL,
            `last_login_at` DATETIME NULL,
            `last_seen_at` DATETIME NULL,
            UNIQUE KEY `uq_email` (`email`),
            UNIQUE KEY `uq_google` (`google_sub`),
            UNIQUE KEY `uq_invite` (`invite_code`),
            UNIQUE KEY `uq_username` (`username`)
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
        // follower follows followee: accepted = the follower sees the followee's lists (pending = waiting for approval)
        "CREATE TABLE IF NOT EXISTS `{$p}follows` (
            `follower_id` INT UNSIGNED NOT NULL,
            `followee_id` INT UNSIGNED NOT NULL,
            `status` ENUM('pending','accepted') NOT NULL DEFAULT 'pending',
            `created_at` DATETIME NOT NULL,
            `accepted_at` DATETIME NULL,
            PRIMARY KEY (`follower_id`, `followee_id`),
            KEY `ix_followee` (`followee_id`, `status`),
            CONSTRAINT `fk_{$p}fo_er` FOREIGN KEY (`follower_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}fo_ee` FOREIGN KEY (`followee_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
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
        // Single-use links that confirm an email address: only their hash is kept
        "CREATE TABLE IF NOT EXISTS `{$p}email_tokens` (
            `token_hash` CHAR(64) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NOT NULL,
            KEY `ix_user` (`user_id`),
            CONSTRAINT `fk_{$p}etok_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
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
        // User activity history (wish added, edited, deleted, list created, edited, archived, deleted, claim set) with undo support
        "CREATE TABLE IF NOT EXISTS `{$p}history` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `action` VARCHAR(32) NOT NULL,
            `list_id` INT UNSIGNED NULL,
            `list_title` VARCHAR(80) NOT NULL DEFAULT '',
            `item_id` INT UNSIGNED NULL,
            `item_title` VARCHAR(160) NOT NULL DEFAULT '',
            `details` MEDIUMTEXT NULL,
            `undone` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            KEY `ix_user_created` (`user_id`, `created_at`),
            CONSTRAINT `fk_{$p}hist_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
    ];
}

/**
 * Upgrades a database created by an older version (every step is idempotent).
 *  v2: public usernames and "follow" instead of mutual friendships (each friendship becomes two follows)
 *  v3: user activity history table with undo support
 */
function migrate_schema(PDO $pdo, string $p, int $from): void
{
    if ($from < 2) {
        $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}users`")->fetchAll(), 'Field');
        if (!in_array('username', $cols, true)) $pdo->exec("ALTER TABLE `{$p}users` ADD COLUMN `username` VARCHAR(20) NULL AFTER `invite_code`, ADD UNIQUE KEY `uq_username` (`username`)");
        if (!in_array('follow_policy', $cols, true)) $pdo->exec("ALTER TABLE `{$p}users` ADD COLUMN `follow_policy` ENUM('approve','open') NOT NULL DEFAULT 'approve' AFTER `username`");
        foreach ($pdo->query("SELECT id, name, email FROM `{$p}users` WHERE username IS NULL")->fetchAll() as $u) {
            $pdo->prepare("UPDATE `{$p}users` SET username = ? WHERE id = ?")->execute([suggest_username_raw($pdo, $p, (string)$u['name'], (string)$u['email']), $u['id']]);
        }
        $has = $pdo->query("SHOW TABLES LIKE '{$p}friendships'")->fetchColumn();
        if ($has) {
            $ins = $pdo->prepare("INSERT IGNORE INTO `{$p}follows` (follower_id, followee_id, status, created_at, accepted_at) VALUES (?,?,?,?,?)");
            foreach ($pdo->query("SELECT * FROM `{$p}friendships`")->fetchAll() as $f) {
                if ($f['status'] === 'accepted') {
                    $ins->execute([$f['user_a'], $f['user_b'], 'accepted', $f['created_at'], $f['accepted_at']]);
                    $ins->execute([$f['user_b'], $f['user_a'], 'accepted', $f['created_at'], $f['accepted_at']]);
                } else {
                    $other = (int)$f['requested_by'] === (int)$f['user_a'] ? $f['user_b'] : $f['user_a'];
                    $ins->execute([$f['requested_by'], $other, 'pending', $f['created_at'], null]);
                }
            }
        }
    }
    if ($from < 4) {
        // Confirmed email addresses: Google already checked its accounts, the others confirm theirs with a link
        $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}users`")->fetchAll(), 'Field');
        if (!in_array('email_verified_at', $cols, true)) {
            $pdo->exec("ALTER TABLE `{$p}users` ADD COLUMN `email_verified_at` DATETIME NULL");
            $pdo->exec("UPDATE `{$p}users` SET email_verified_at = UTC_TIMESTAMP() WHERE google_sub IS NOT NULL");
        }
    }
    if ($from < 3) {
        $has = $pdo->query("SHOW TABLES LIKE '{$p}history'")->fetchColumn();
        if (!$has) {
            $e = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
            $pdo->exec("CREATE TABLE IF NOT EXISTS `{$p}history` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT UNSIGNED NOT NULL,
                `action` VARCHAR(32) NOT NULL,
                `list_id` INT UNSIGNED NULL,
                `list_title` VARCHAR(80) NOT NULL DEFAULT '',
                `item_id` INT UNSIGNED NULL,
                `item_title` VARCHAR(160) NOT NULL DEFAULT '',
                `details` MEDIUMTEXT NULL,
                `undone` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME NOT NULL,
                KEY `ix_user_created` (`user_id`, `created_at`),
                CONSTRAINT `fk_{$p}hist_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
            ) $e");
        }
    }
}

/** A free username from a name ("Anna Rossi" → "anna.rossi", "anna.rossi2"…), with an explicit prefix. */
function suggest_username_raw(PDO $pdo, string $p, string $name, string $email): string
{
    $base = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '.', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: ''), '.'));
    if (strlen($base) < 3) $base = strtolower(preg_replace('/[^a-z0-9]+/i', '', explode('@', $email)[0]));
    if (strlen($base) < 3) $base = 'user';
    $base = substr($base, 0, 16);
    $st = $pdo->prepare("SELECT 1 FROM `{$p}users` WHERE username = ?");
    for ($i = 0; $i < 200; $i++) {
        $u = $i === 0 ? $base : $base . ($i < 50 ? $i + 1 : random_int(100, 9999));
        $st->execute([$u]);
        if (!$st->fetchColumn()) return $u;
    }
    return $base . bin2hex(random_bytes(2));
}

/** Creates the tables (idempotent), upgrades older ones and records the schema version. */
function install_schema(PDO $pdo, string $prefix): void
{
    $from = 0;
    try {
        $from = (int)$pdo->query("SELECT v FROM `{$prefix}meta` WHERE k = 'schema_version'")->fetchColumn();
    } catch (Throwable $e) {
        $from = 0;
    }
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    if ($from > 0 && $from < SCHEMA_VERSION) migrate_schema($pdo, $prefix, $from); // after the new tables exist
    $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute([(string)SCHEMA_VERSION]);
}
