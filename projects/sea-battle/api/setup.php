<?php
require_once __DIR__ . '/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `sb_rooms` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `code` VARCHAR(8) NOT NULL UNIQUE,
        `status` ENUM('waiting','placing','playing','finished') NOT NULL DEFAULT 'waiting',
        `is_public` TINYINT(1) NOT NULL DEFAULT 0,
        `lang` VARCHAR(5) DEFAULT NULL,
        `time_control` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Seconds per player, 0 = no clock',
        `bonus_shot` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = a hit gives another shot',
        `round` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        `first_slot` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Slot that fires first (1 or 2)',
        `p1_fleet` VARCHAR(60) DEFAULT NULL COMMENT 'r,c,d;... never sent to the other player',
        `p2_fleet` VARCHAR(60) DEFAULT NULL,
        `shots` TEXT NOT NULL COMMENT 'slot:cell separated by spaces',
        `p1_ms` BIGINT NOT NULL DEFAULT 0,
        `p2_ms` BIGINT NOT NULL DEFAULT 0,
        `turn_started_ms` BIGINT NOT NULL DEFAULT 0,
        `place_started_ms` BIGINT NOT NULL DEFAULT 0,
        `result` VARCHAR(8) DEFAULT NULL COMMENT '1-0 or 0-1',
        `reason` VARCHAR(20) DEFAULT NULL COMMENT 'fleet_sunk, resign, timeout, abandon',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_matchmaking` (`is_public`, `status`, `lang`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `sb_players` (
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
        FOREIGN KEY (`room_id`) REFERENCES `sb_rooms`(`id`) ON DELETE CASCADE,
        INDEX `idx_room` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `sb_messages` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `room_id` INT UNSIGNED NOT NULL,
        `slot` TINYINT UNSIGNED NOT NULL COMMENT 'Sender: 1 or 2',
        `body` VARCHAR(200) NOT NULL COMMENT 'Plain text only',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`room_id`) REFERENCES `sb_rooms`(`id`) ON DELETE CASCADE,
        INDEX `idx_room_msg` (`room_id`, `id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

$results = [];
foreach ($queries as $i => $sql) {
    try {
        $pdo->exec($sql);
        $results[] = 'Table ' . ($i + 1) . ' OK.';
    } catch (PDOException $e) {
        error_log('[sea-battle] setup: ' . $e->getMessage());
        $results[] = 'Table ' . ($i + 1) . ' error (details in the server log).';
    }
}

header('Content-Type: application/json');
echo json_encode([
    'status' => 'complete',
    'results' => $results,
], JSON_PRETTY_PRINT);
