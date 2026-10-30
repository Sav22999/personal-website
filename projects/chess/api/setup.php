<?php
require_once __DIR__ . '/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `chess_rooms` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `code` VARCHAR(8) NOT NULL UNIQUE,
        `status` ENUM('waiting','playing','finished') NOT NULL DEFAULT 'waiting',
        `is_public` TINYINT(1) NOT NULL DEFAULT 0,
        `lang` VARCHAR(5) DEFAULT NULL,
        `time_control` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Seconds per player, 0 = no clock',
        `round` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        `white_slot` TINYINT UNSIGNED NOT NULL DEFAULT 1,
        `moves` TEXT NOT NULL COMMENT 'UCI moves separated by spaces',
        `sans` TEXT NOT NULL COMMENT 'SAN moves separated by spaces',
        `white_ms` BIGINT NOT NULL DEFAULT 0,
        `black_ms` BIGINT NOT NULL DEFAULT 0,
        `turn_started_ms` BIGINT NOT NULL DEFAULT 0,
        `draw_offer_slot` TINYINT UNSIGNED DEFAULT NULL,
        `result` VARCHAR(8) DEFAULT NULL,
        `reason` VARCHAR(20) DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_matchmaking` (`is_public`, `status`, `lang`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `chess_players` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `room_id` INT UNSIGNED NOT NULL,
        `slot` TINYINT UNSIGNED NOT NULL COMMENT '1 or 2',
        `token` VARCHAR(64) NOT NULL UNIQUE,
        `name` VARCHAR(50) NOT NULL DEFAULT 'Player',
        `points2` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Score x2: win = 2, draw = 1',
        `wants_rematch` TINYINT(1) NOT NULL DEFAULT 0,
        `is_online` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = left the room for good',
        `last_seen` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `joined_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`room_id`) REFERENCES `chess_rooms`(`id`) ON DELETE CASCADE,
        INDEX `idx_room` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

$results = [];
foreach ($queries as $i => $sql) {
    try {
        $pdo->exec($sql);
        $results[] = 'Table ' . ($i + 1) . ' OK.';
    } catch (PDOException $e) {
        $results[] = 'Table ' . ($i + 1) . ' error: ' . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode(['results' => $results]);
