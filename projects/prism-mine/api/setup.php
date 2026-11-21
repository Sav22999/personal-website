<?php
require_once __DIR__ . '/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `pm_rooms` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `code` VARCHAR(8) NOT NULL UNIQUE,
        `status` ENUM('waiting','placing','playing','finished') NOT NULL DEFAULT 'waiting',
        `is_public` TINYINT(1) NOT NULL DEFAULT 0,
        `lang` VARCHAR(5) DEFAULT NULL,
        `level` VARCHAR(8) NOT NULL DEFAULT 'medium',
        `time_control` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Seconds per player, 0 = no clock',
        `round` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        `first_slot` TINYINT UNSIGNED NOT NULL DEFAULT 1,
        `mine` VARCHAR(200) NOT NULL DEFAULT '' COMMENT 'Set of gems of the round (only shape and colour are used)',
        `p1_mine` VARCHAR(200) DEFAULT NULL COMMENT 'Mine hidden by player 1: shape,colour,r,c,rot;... never sent to the other player before the end',
        `p2_mine` VARCHAR(200) DEFAULT NULL,
        `actions` TEXT NOT NULL COMMENT 'JSON list of probes and guesses',
        `p1_ms` BIGINT NOT NULL DEFAULT 0,
        `p2_ms` BIGINT NOT NULL DEFAULT 0,
        `turn_started_ms` BIGINT NOT NULL DEFAULT 0,
        `place_started_ms` BIGINT NOT NULL DEFAULT 0,
        `result` VARCHAR(8) DEFAULT NULL COMMENT '1-0 or 0-1',
        `reason` VARCHAR(20) DEFAULT NULL COMMENT 'solved, strikes, resign, timeout, abandon',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_matchmaking` (`is_public`, `status`, `lang`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `pm_players` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `room_id` INT UNSIGNED NOT NULL,
        `slot` TINYINT UNSIGNED NOT NULL COMMENT '1 or 2',
        `token` VARCHAR(64) NOT NULL UNIQUE,
        `name` VARCHAR(50) NOT NULL DEFAULT 'Player',
        `wins` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        `wants_rematch` TINYINT(1) NOT NULL DEFAULT 0,
        `is_online` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = left the room for good',
        `last_seen` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `joined_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`room_id`) REFERENCES `pm_rooms`(`id`) ON DELETE CASCADE,
        INDEX `idx_room` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `pm_messages` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `room_id` INT UNSIGNED NOT NULL,
        `slot` TINYINT UNSIGNED NOT NULL COMMENT 'Sender: 1 or 2',
        `body` VARCHAR(200) NOT NULL COMMENT 'Plain text only',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`room_id`) REFERENCES `pm_rooms`(`id`) ON DELETE CASCADE,
        INDEX `idx_room_msg` (`room_id`, `id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

$results = [];
foreach ($queries as $i => $sql) {
    try {
        $pdo->exec($sql);
        $results[] = 'Table ' . ($i + 1) . ' OK.';
    } catch (PDOException $e) {
        error_log('[prism-mine] setup: ' . $e->getMessage());
        $results[] = 'Table ' . ($i + 1) . ' error (details in the server log).';
    }
}

// Tables created by an older version: add what is missing, without touching the data
$columns = [
    'p1_mine' => "ALTER TABLE `pm_rooms` ADD COLUMN `p1_mine` VARCHAR(200) DEFAULT NULL AFTER `mine`",
    'p2_mine' => "ALTER TABLE `pm_rooms` ADD COLUMN `p2_mine` VARCHAR(200) DEFAULT NULL AFTER `p1_mine`",
    'place_started_ms' => "ALTER TABLE `pm_rooms` ADD COLUMN `place_started_ms` BIGINT NOT NULL DEFAULT 0 AFTER `turn_started_ms`",
];
try {
    $existing = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pm_rooms'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($columns as $name => $sql) {
        if (!in_array($name, $existing, true)) {
            $pdo->exec($sql);
            $results[] = "Column $name added.";
        }
    }
    $pdo->exec("ALTER TABLE `pm_rooms` MODIFY `status` ENUM('waiting','placing','playing','finished') NOT NULL DEFAULT 'waiting'");
    $results[] = 'Rooms table up to date.';
} catch (PDOException $e) {
    error_log('[prism-mine] setup: ' . $e->getMessage());
    $results[] = 'Update error (details in the server log).';
}

header('Content-Type: application/json');
echo json_encode([
    'status' => 'complete',
    'results' => $results,
], JSON_PRETTY_PRINT);
