<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/include/email-template.php';

$config = require dirname(__DIR__) . '/.mail-config.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

function get_antispam_dir()
{
    $dir = sys_get_temp_dir() . '/saveriomorelli_antispam';
    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
    }
    return $dir;
}

function cleanup_expired_files($dir, $max_age = 3600)
{
    $now = time();
    foreach (glob($dir . '/*') as $file) {
        if ($now - filemtime($file) > $max_age) {
            @unlink($file);
        }
    }
}

function is_rate_limited($ip, $max_per_hour = 5)
{
    $dir = get_antispam_dir() . '/rate';
    if (!is_dir($dir)) mkdir($dir, 0700, true);
    cleanup_expired_files($dir);

    $key = hash('sha256', $ip) . '_' . date('YmdH');
    $file = $dir . '/' . $key;

    $count = 0;
    if (file_exists($file)) {
        $count = (int)file_get_contents($file);
    }

    if ($count >= $max_per_hour) return true;

    file_put_contents($file, $count + 1, LOCK_EX);
    return false;
}

function is_challenge_replayed($signature)
{
    $dir = get_antispam_dir() . '/used';
    if (!is_dir($dir)) mkdir($dir, 0700, true);
    cleanup_expired_files($dir);

    $file = $dir . '/' . $signature;
    if (file_exists($file)) return true;

    file_put_contents($file, time(), LOCK_EX);
    return false;
}

function get_client_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // Behind an internal proxy the real client is the last hop it appended, not the (spoofable) first one
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwarded = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        $last = end($forwarded);
        if (filter_var($last, FILTER_VALIDATE_IP)) $ip = $last;
    }

    return $ip;
}

function is_valid_form_token($token, $secret, $min_seconds = 3, $max_seconds = 7200)
{
    $parts = explode('.', $token);
    if (count($parts) !== 2 || !ctype_digit($parts[0])) return false;

    [$ts, $signature] = $parts;
    if (!hash_equals(hash_hmac('sha256', 'form:' . $ts, $secret), $signature)) return false;

    $elapsed = time() - (int)$ts;
    return $elapsed >= $min_seconds && $elapsed <= $max_seconds;
}

function looks_like_spam($name, $message)
{
    $text = $name . ' ' . $message;

    foreach (preg_split('/\s+/', $text) as $word) {
        if (mb_strlen($word) >= 6 && preg_match_all('/[a-z][A-Z]/', $word) >= 3) return true;
    }

    if (mb_strlen($message) > 20 && !preg_match('/\s/', $message)) return true;

    $letters = preg_match_all('/\pL/u', $text);
    $vowels = preg_match_all('/[aeiouyàèéìòù]/iu', $text);
    if ($letters > 10 && $vowels / $letters < 0.2) return true;

    $len = mb_strlen($message);
    if ($len > 0) {
        $digit_count = preg_match_all('/\d/', $message);
        if ($len > 20 && $digit_count / $len > 0.4) return true;
    }

    if (preg_match('/(.)\1{7,}/', $message)) return true;

    if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $message)) return true;

    return false;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$name = trim($input['name'] ?? '');
$reason = trim($input['reason'] ?? '');
$email = trim($input['email'] ?? '');
$message = trim($input['message'] ?? '');
$os = trim($input['os'] ?? '');
$browser = trim($input['browser'] ?? '');
$language = trim($input['language'] ?? '');
$project = trim($input['project'] ?? '');
$project_version = trim($input['project_version'] ?? '');
$altcha = $input['altcha'] ?? '';
$honeypot = trim($input['company'] ?? '');
$form_token = $input['ts'] ?? '';

if ($honeypot !== '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Verification failed. Please try again.']);
    exit;
}

if (!$name || !$reason || !$email || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$valid_reasons = ['Project request', 'General', 'Bug report', 'Feature request', 'Collaboration', 'Other'];
if (!in_array($reason, $valid_reasons)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid reason selected.']);
    exit;
}

if (!is_valid_form_token($form_token, $config['altcha_secret'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Verification failed. Please reload the page and try again.']);
    exit;
}

if (is_rate_limited(get_client_ip())) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many messages. Please try again later.']);
    exit;
}

if (!$altcha) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please complete the verification.']);
    exit;
}

$payload = json_decode(base64_decode($altcha), true);
if (!$payload || !isset($payload['algorithm'], $payload['challenge'], $payload['number'], $payload['salt'], $payload['signature'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid verification.']);
    exit;
}

if (preg_match('/\?expires=(\d+)/', $payload['salt'], $m)) {
    if (time() > (int)$m[1]) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Verification expired. Please reload and try again.']);
        exit;
    }
}

$expected_challenge = hash('sha256', $payload['salt'] . $payload['number']);
$expected_signature = hash_hmac('sha256', $expected_challenge, $config['altcha_secret']);

if (!hash_equals($expected_challenge, $payload['challenge']) || !hash_equals($expected_signature, $payload['signature'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Verification failed. Please try again.']);
    exit;
}

if (is_challenge_replayed($payload['signature'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Verification already used. Please reload and try again.']);
    exit;
}

if (looks_like_spam($name, $message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Your message was flagged as spam. Please rephrase and try again.']);
    exit;
}

$data = [
    'name' => $name,
    'reason' => $reason,
    'email' => $email,
    'message' => $message,
    'os' => $os,
    'browser' => $browser,
    'language' => $language,
    'project' => $project,
    'project_version' => $project_version,
];

try {
    $dsn = sprintf(
        'smtps://%s:%s@%s:%d',
        urlencode($config['smtp_user']),
        urlencode($config['smtp_password']),
        $config['smtp_host'],
        $config['smtp_port']
    );
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);

    $notification = render_email('notification', $data);
    $email_to_owner = (new Email())
        ->from($config['from_name'] . ' <' . $config['from_email'] . '>')
        ->to($config['to_email'])
        ->replyTo($email)
        ->subject($notification['subject'])
        ->html($notification['html']);
    $mailer->send($email_to_owner);

    echo json_encode(['success' => true, 'message' => "Message sent successfully! I'll get back to you as soon as possible."]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again later.']);
}
