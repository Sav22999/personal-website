<?php
require_once __DIR__ . '/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `fg_rooms` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `code` VARCHAR(8) NOT NULL UNIQUE,
        `status` ENUM('waiting','playing','finished') NOT NULL DEFAULT 'waiting',
        `mode` VARCHAR(10) NOT NULL DEFAULT 'online',
        `is_public` TINYINT(1) NOT NULL DEFAULT 0,
        `lang` VARCHAR(5) DEFAULT NULL,
        `current_turn` TINYINT UNSIGNED DEFAULT NULL,
        `round` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        `first_turn` TINYINT UNSIGNED NOT NULL DEFAULT 1,
        `winner_slot` TINYINT UNSIGNED DEFAULT NULL COMMENT '0 = draw',
        `pending_winner` TINYINT UNSIGNED DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_code` (`code`),
        INDEX `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `fg_players` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `room_id` INT UNSIGNED NOT NULL,
        `slot` TINYINT UNSIGNED NOT NULL COMMENT '1 or 2',
        `token` VARCHAR(64) NOT NULL UNIQUE,
        `name` VARCHAR(50) NOT NULL DEFAULT 'Player',
        `own_position` TINYINT UNSIGNED DEFAULT NULL COMMENT 'Board position of the character this player holds',
        `wants_rematch` TINYINT(1) NOT NULL DEFAULT 0,
        `wins` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        `is_online` TINYINT(1) NOT NULL DEFAULT 1,
        `last_seen` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `joined_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`room_id`) REFERENCES `fg_rooms`(`id`) ON DELETE CASCADE,
        INDEX `idx_token` (`token`),
        INDEX `idx_room` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `fg_board_cards` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `room_id` INT UNSIGNED NOT NULL,
        `position` TINYINT UNSIGNED NOT NULL,
        `card_data` TEXT NOT NULL COMMENT 'JSON attributes',
        FOREIGN KEY (`room_id`) REFERENCES `fg_rooms`(`id`) ON DELETE CASCADE,
        INDEX `idx_room_pos` (`room_id`, `position`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `fg_player_cards` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `player_id` INT UNSIGNED NOT NULL,
        `card_id` INT UNSIGNED NOT NULL,
        `is_hidden` TINYINT(1) NOT NULL DEFAULT 0,
        FOREIGN KEY (`player_id`) REFERENCES `fg_players`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`card_id`) REFERENCES `fg_board_cards`(`id`) ON DELETE CASCADE,
        UNIQUE KEY `uq_player_card` (`player_id`, `card_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `fg_guesses` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `room_id` INT UNSIGNED NOT NULL,
        `player_id` INT UNSIGNED NOT NULL,
        `question` VARCHAR(255) NOT NULL,
        `answer` ENUM('yes','no') DEFAULT NULL,
        `is_final_guess` TINYINT(1) NOT NULL DEFAULT 0,
        `guessed_position` TINYINT UNSIGNED DEFAULT NULL,
        `is_correct` TINYINT(1) DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`room_id`) REFERENCES `fg_rooms`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`player_id`) REFERENCES `fg_players`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

$results = [];
foreach ($queries as $i => $sql) {
    try {
        $pdo->exec($sql);
        $results[] = "Table " . ($i + 1) . " OK.";
    } catch (PDOException $e) {
        $results[] = "Table " . ($i + 1) . " error: " . $e->getMessage();
    }
}

function columnExists($pdo, $table, $column) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    if (!columnExists($pdo, 'fg_players', 'own_position')) {
        $pdo->exec("ALTER TABLE `fg_players` ADD COLUMN `own_position` TINYINT UNSIGNED DEFAULT NULL AFTER `name`");
        $results[] = "Added fg_players.own_position.";
    }
    if (!columnExists($pdo, 'fg_players', 'wants_rematch')) {
        $pdo->exec("ALTER TABLE `fg_players` ADD COLUMN `wants_rematch` TINYINT(1) NOT NULL DEFAULT 0 AFTER `own_position`");
        $results[] = "Added fg_players.wants_rematch.";
    }
    if (!columnExists($pdo, 'fg_players', 'wins')) {
        $pdo->exec("ALTER TABLE `fg_players` ADD COLUMN `wins` SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER `wants_rematch`");
        $results[] = "Added fg_players.wins.";
    }
    if (!columnExists($pdo, 'fg_rooms', 'round')) {
        $pdo->exec("ALTER TABLE `fg_rooms` ADD COLUMN `round` SMALLINT UNSIGNED NOT NULL DEFAULT 1 AFTER `current_turn`");
        $results[] = "Added fg_rooms.round.";
    }
    $roomColumns = [
        'mode' => "ADD COLUMN `mode` VARCHAR(10) NOT NULL DEFAULT 'online' AFTER `status`",
        'is_public' => "ADD COLUMN `is_public` TINYINT(1) NOT NULL DEFAULT 0 AFTER `mode`",
        'lang' => "ADD COLUMN `lang` VARCHAR(5) DEFAULT NULL AFTER `is_public`",
        'winner_slot' => "ADD COLUMN `winner_slot` TINYINT UNSIGNED DEFAULT NULL AFTER `round`",
        'first_turn' => "ADD COLUMN `first_turn` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `current_turn`",
        'pending_winner' => "ADD COLUMN `pending_winner` TINYINT UNSIGNED DEFAULT NULL COMMENT 'Slot that guessed right first; the opponent gets a last chance' AFTER `winner_slot`",
    ];
    foreach ($roomColumns as $column => $ddl) {
        if (!columnExists($pdo, 'fg_rooms', $column)) {
            $pdo->exec("ALTER TABLE `fg_rooms` $ddl");
            $results[] = "Added fg_rooms.$column.";
        }
    }
    $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'fg_rooms' AND INDEX_NAME = 'idx_matchmaking'");
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("ALTER TABLE `fg_rooms` ADD INDEX `idx_matchmaking` (`is_public`, `status`, `lang`)");
        $results[] = "Added fg_rooms.idx_matchmaking.";
    }
    if (columnExists($pdo, 'fg_players', 'target_card')) {
        $pdo->exec("ALTER TABLE `fg_players` DROP COLUMN `target_card`");
        $results[] = "Dropped obsolete fg_players.target_card.";
    }
} catch (PDOException $e) {
    $results[] = "Migration error: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode(['results' => $results]);
