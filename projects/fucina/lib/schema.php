<?php
/**
 * Fucina — database tables (all prefixed, default "fc_"). Every statement is idempotent.
 *
 * Courses, lessons, questions and games live here and are managed from the admin portal; lib/seed/ only provides the
 * first catalogue. Texts are JSON objects with one key per language: {"it": "…", "en": "…"}.
 * Progress refers to a lesson by its course and slug, so a lesson can be rewritten without losing it.
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
            `username` VARCHAR(20) NULL,
            `email` VARCHAR(190) NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL DEFAULT '',
            `google_sub` VARCHAR(64) NULL,
            `lang` CHAR(2) NOT NULL DEFAULT 'en',
            `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
            `role` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `xp` INT UNSIGNED NOT NULL DEFAULT 0,
            `points` INT UNSIGNED NOT NULL DEFAULT 0,
            `streak` INT UNSIGNED NOT NULL DEFAULT 0,
            `best_streak` INT UNSIGNED NOT NULL DEFAULT 0,
            `win_streak` INT UNSIGNED NOT NULL DEFAULT 0,
            `best_win_streak` INT UNSIGNED NOT NULL DEFAULT 0,
            `drill_streak` INT UNSIGNED NOT NULL DEFAULT 0,
            `best_drill_streak` INT UNSIGNED NOT NULL DEFAULT 0,
            `on_board` TINYINT(1) NOT NULL DEFAULT 1,
            `last_day` DATE NULL,
            `created_at` DATETIME NOT NULL,
            `last_login_at` DATETIME NULL,
            `last_seen_at` DATETIME NULL,
            UNIQUE KEY `uq_email` (`email`),
            UNIQUE KEY `uq_username` (`username`),
            UNIQUE KEY `uq_google` (`google_sub`)
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
        "CREATE TABLE IF NOT EXISTS `{$p}settings` (
            `k` VARCHAR(40) NOT NULL PRIMARY KEY,
            `v` VARCHAR(255) NOT NULL
        ) $e",
        // after_id = the course to finish first; data = title, short, desc, level, each with its languages
        "CREATE TABLE IF NOT EXISTS `{$p}courses` (
            `id` VARCHAR(30) NOT NULL PRIMARY KEY,
            `family` VARCHAR(20) NOT NULL,
            `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `after_id` VARCHAR(30) NULL,
            `status` ENUM('published','draft') NOT NULL DEFAULT 'draft',
            `data` TEXT NOT NULL
        ) $e",
        // difficulty: 1 easy, 2 medium, 3 hard
        "CREATE TABLE IF NOT EXISTS `{$p}lessons` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `course` VARCHAR(30) NOT NULL,
            `slug` VARCHAR(40) NOT NULL,
            `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `difficulty` TINYINT UNSIGNED NOT NULL DEFAULT 1,
            `status` ENUM('published','draft') NOT NULL DEFAULT 'draft',
            `title` TEXT NOT NULL,
            `body` MEDIUMTEXT NOT NULL,
            `task` MEDIUMTEXT NULL,
            UNIQUE KEY `uq_slug` (`course`, `slug`),
            CONSTRAINT `fk_{$p}le_course` FOREIGN KEY (`course`) REFERENCES `{$p}courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) $e",
        // hint = optional written hint, on top of the wrong options a paid hint removes
        "CREATE TABLE IF NOT EXISTS `{$p}questions` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `lesson_id` INT UNSIGNED NOT NULL,
            `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `difficulty` TINYINT UNSIGNED NOT NULL DEFAULT 1,
            `q` TEXT NOT NULL,
            `options` TEXT NOT NULL,
            `answer` TINYINT UNSIGNED NOT NULL,
            `why` TEXT NOT NULL,
            `hint` TEXT NULL,
            KEY `ix_lesson` (`lesson_id`, `sort`),
            CONSTRAINT `fk_{$p}qu_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `{$p}lessons` (`id`) ON DELETE CASCADE
        ) $e",
        // Short playful exercises of a level: see lib/drills.php. data = texts, content and solution, as JSON
        "CREATE TABLE IF NOT EXISTS `{$p}drills` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `lesson_id` INT UNSIGNED NOT NULL,
            `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `kind` VARCHAR(10) NOT NULL,
            `data` MEDIUMTEXT NOT NULL,
            KEY `ix_lesson` (`lesson_id`, `sort`),
            CONSTRAINT `fk_{$p}dr_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `{$p}lessons` (`id`) ON DELETE CASCADE
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}games` (
            `id` VARCHAR(20) NOT NULL PRIMARY KEY,
            `enabled` TINYINT(1) NOT NULL DEFAULT 1,
            `sort` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `levels` TINYINT UNSIGNED NOT NULL DEFAULT 6,
            `data` TEXT NOT NULL
        ) $e",
        // Content of a game for a level: cards, selectors, snippets
        "CREATE TABLE IF NOT EXISTS `{$p}game_items` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `game` VARCHAR(20) NOT NULL,
            `level` TINYINT UNSIGNED NOT NULL DEFAULT 1,
            `status` ENUM('published','draft') NOT NULL DEFAULT 'published',
            `data` TEXT NOT NULL,
            KEY `ix_game` (`game`, `level`),
            CONSTRAINT `fk_{$p}gi_game` FOREIGN KEY (`game`) REFERENCES `{$p}games` (`id`) ON DELETE CASCADE
        ) $e",
        // One row per level won: the next level unlocks from here
        "CREATE TABLE IF NOT EXISTS `{$p}game_progress` (
            `user_id` INT UNSIGNED NOT NULL,
            `game` VARCHAR(20) NOT NULL,
            `level` TINYINT UNSIGNED NOT NULL,
            `stars` TINYINT UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (`user_id`, `game`, `level`),
            CONSTRAINT `fk_{$p}gp_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // A hint is paid once: asking again for the same one is free
        "CREATE TABLE IF NOT EXISTS `{$p}hints` (
            `user_id` INT UNSIGNED NOT NULL,
            `ref` VARCHAR(60) NOT NULL,
            `cost` SMALLINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `ref`),
            CONSTRAINT `fk_{$p}hi_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // One row per completed lesson; stars = best result so far
        "CREATE TABLE IF NOT EXISTS `{$p}lesson_progress` (
            `user_id` INT UNSIGNED NOT NULL,
            `course` VARCHAR(30) NOT NULL,
            `lesson` VARCHAR(40) NOT NULL,
            `stars` TINYINT UNSIGNED NOT NULL DEFAULT 1,
            `task_done` TINYINT(1) NOT NULL DEFAULT 0,
            `completed_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `course`, `lesson`),
            CONSTRAINT `fk_{$p}lp_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // What happened on each day: feeds the statistics, the activity calendar and the daily XP cap of the games
        "CREATE TABLE IF NOT EXISTS `{$p}activity` (
            `user_id` INT UNSIGNED NOT NULL,
            `day` DATE NOT NULL,
            `role` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `xp` INT UNSIGNED NOT NULL DEFAULT 0,
            `points` INT UNSIGNED NOT NULL DEFAULT 0,
            `game_xp` INT UNSIGNED NOT NULL DEFAULT 0,
            `lessons` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            `games` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`user_id`, `day`),
            CONSTRAINT `fk_{$p}ac_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}game_results` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `game` VARCHAR(20) NOT NULL,
            `score` INT UNSIGNED NOT NULL DEFAULT 0,
            `stars` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `won` TINYINT(1) NOT NULL DEFAULT 0,
            `seconds` INT UNSIGNED NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            KEY `ix_user_game` (`user_id`, `game`),
            CONSTRAINT `fk_{$p}gr_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}daily_results` (
            `user_id` INT UNSIGNED NOT NULL,
            `day` DATE NOT NULL,
            `correct` TINYINT UNSIGNED NOT NULL,
            `total` TINYINT UNSIGNED NOT NULL,
            `done_at` DATETIME NULL,
            PRIMARY KEY (`user_id`, `day`),
            CONSTRAINT `fk_{$p}dr_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // The questions of a final exam are chosen by the server and graded against this row
        "CREATE TABLE IF NOT EXISTS `{$p}exams` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `course` VARCHAR(30) NOT NULL,
            `questions` TEXT NOT NULL,
            `score` TINYINT UNSIGNED NULL,
            `started_at` DATETIME NOT NULL,
            `submitted_at` DATETIME NULL,
            KEY `ix_user_course` (`user_id`, `course`),
            CONSTRAINT `fk_{$p}ex_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}drill_done` (
            `user_id` INT UNSIGNED NOT NULL,
            `drill_id` INT UNSIGNED NOT NULL,
            `stars` TINYINT UNSIGNED NOT NULL,
            `done_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `drill_id`),
            CONSTRAINT `fk_{$p}dd_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}dd_drill` FOREIGN KEY (`drill_id`) REFERENCES `{$p}drills` (`id`) ON DELETE CASCADE
        ) $e",
        // Friends go one way, like following: adding someone shows you their public results, and nothing else
        "CREATE TABLE IF NOT EXISTS `{$p}friends` (
            `user_id` INT UNSIGNED NOT NULL,
            `friend_id` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `friend_id`),
            CONSTRAINT `fk_{$p}fr_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_{$p}fr_friend` FOREIGN KEY (`friend_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // Prizes of the daily leaderboard: one row per day and place, so a day can never be paid twice
        "CREATE TABLE IF NOT EXISTS `{$p}board_prizes` (
            `day` DATE NOT NULL,
            `place` TINYINT UNSIGNED NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `participants` INT UNSIGNED NOT NULL,
            `points` INT UNSIGNED NOT NULL,
            `seen` TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`day`, `place`),
            KEY `ix_user` (`user_id`, `seen`),
            CONSTRAINT `fk_{$p}bp_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        // code = the public, unguessable reference of certificate.php
        "CREATE TABLE IF NOT EXISTS `{$p}certificates` (
            `code` CHAR(12) NOT NULL PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `course` VARCHAR(30) NOT NULL,
            `name` VARCHAR(80) NOT NULL,
            `score` TINYINT UNSIGNED NOT NULL,
            `issued_at` DATETIME NOT NULL,
            UNIQUE KEY `uq_user_course` (`user_id`, `course`),
            CONSTRAINT `fk_{$p}ce_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
        "CREATE TABLE IF NOT EXISTS `{$p}badges` (
            `user_id` INT UNSIGNED NOT NULL,
            `badge` VARCHAR(30) NOT NULL,
            `earned_at` DATETIME NOT NULL,
            PRIMARY KEY (`user_id`, `badge`),
            CONSTRAINT `fk_{$p}ba_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
        ) $e",
    ];
}

function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    // Columns added after the first version: an existing table is not changed by CREATE TABLE IF NOT EXISTS
    $columns = $pdo->query("SHOW COLUMNS FROM `{$prefix}users`")->fetchAll(PDO::FETCH_COLUMN);
    foreach (['win_streak', 'best_win_streak', 'drill_streak', 'best_drill_streak'] as $c) {
        if (!in_array($c, $columns, true)) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `$c` INT UNSIGNED NOT NULL DEFAULT 0");
    }
    if (!in_array('on_board', $columns, true)) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `on_board` TINYINT(1) NOT NULL DEFAULT 1");
    if (!in_array('username', $columns, true)) $pdo->exec("ALTER TABLE `{$prefix}users` ADD COLUMN `username` VARCHAR(20) NULL AFTER `name`, ADD UNIQUE KEY `uq_username` (`username`)");
    $columns = $pdo->query("SHOW COLUMNS FROM `{$prefix}daily_results`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('done_at', $columns, true)) $pdo->exec("ALTER TABLE `{$prefix}daily_results` ADD COLUMN `done_at` DATETIME NULL");
    require_once __DIR__ . '/seed.php';
    install_seed($pdo, $prefix);
    install_seed_drills($pdo, $prefix);
    // Last: if something above fails, the next request tries again
    $st = $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)");
    $st->execute([(string)SCHEMA_VERSION]);
}
