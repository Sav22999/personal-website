<?php
// Copy this file to db.php and fill in the real values. db.php is ignored by git.
$localhost_db = "localhost";
$username_db = "your_db_user";
$password_db = "your_db_password";
$database_name = "your_db_name";

$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host=$localhost_db;dbname=$database_name;charset=utf8mb4",
        $username_db,
        $password_db,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
