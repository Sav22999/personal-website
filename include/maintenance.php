<?php
$maintenance_bypass_active = false;
$maintenance_file = __DIR__ . '/../.maintenance';

if (!file_exists($maintenance_file)) return;
$maintenance_key = trim(file_get_contents($maintenance_file));
if ($maintenance_key === '') return;

$key_hash = hash('sha256', $maintenance_key);

if (isset($_GET['bypass']) && $_GET['bypass'] === $maintenance_key) {
    setcookie('maintenance_bypass', $key_hash, [
        'expires' => time() + 86400,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'samesite' => 'Lax',
    ]);
    $maintenance_bypass_active = true;
    return;
}

if (isset($_COOKIE['maintenance_bypass']) && $_COOKIE['maintenance_bypass'] === $key_hash) {
    $maintenance_bypass_active = true;
    return;
}

while (ob_get_level()) ob_end_clean();
http_response_code(503);
header('Retry-After: 3600');
include __DIR__ . '/maintenance-page.php';
exit;
