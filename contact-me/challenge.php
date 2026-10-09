<?php

header('Content-Type: application/json');

$config = require dirname(__DIR__) . '/.mail-config.php';
$secret = $config['altcha_secret'];

$expires = time() + 300;
$salt = bin2hex(random_bytes(12)) . '?expires=' . $expires;
$maxnumber = 1000000;
$number = random_int(50000, $maxnumber);
$challenge = hash('sha256', $salt . $number);
$signature = hash_hmac('sha256', $challenge, $secret);

echo json_encode([
    'algorithm' => 'SHA-256',
    'challenge' => $challenge,
    'maxnumber' => $maxnumber,
    'salt' => $salt,
    'signature' => $signature,
]);
