<?php
/**
 * SavAccount — API, called only by the gateway of savaccount.eu:  api/index.php?r=<action>
 * Actions: lib/routes/*.php. Every POST of a signed-in user needs the X-CSRF-Token header.
 */

declare(strict_types=1);

ini_set('display_errors', '0');
require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/mail.php';
require __DIR__ . '/../lib/totp.php';
require __DIR__ . '/../lib/accounts.php';
require __DIR__ . '/../lib/oidc.php';

header('X-Robots-Tag: noindex');
set_exception_handler(function (Throwable $e) {
    // the message may contain data of the request: only its kind and place are logged
    error_log('SavAccount API: ' . get_class($e) . ' ' . $e->getCode() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine());
    send_json(['ok' => false, 'error' => 'server', 'detail' => cfg('debug') ? $e->getMessage() : null], 500);
});

$r = (string)($_GET['r'] ?? '');

if ($r === 'health') {
    // for the status page: no key needed, nothing revealed
    if (!credentials() || !cfg('enabled')) fail('unavailable', 503);
    db();
    ok();
}
if (!credentials()) fail('unavailable', 503);
require_gateway();
if (!cfg('enabled')) fail('unavailable', 503);

$oidc = strpos($r, 'oidc.') === 0;
$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET', 'POST'], true)) fail('bad_method', 405);
if (!$oidc && $method === 'POST' && stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json') !== 0) fail('bad_request', 415);

// what can be read with GET; everything else is a POST
$readable = ['auth.me', 'emails.list', 'sessions.list', 'events.list', 'apps.list', 'admin.clients', 'oidc.config', 'oidc.jwks', 'oidc.userinfo'];
if ($method === 'GET' && !in_array($r, $readable, true)) fail('bad_method', 405);

$file = ['auth' => 'auth', 'profile' => 'account', 'emails' => 'account', 'password' => 'account', '2fa' => 'account', 'sessions' => 'account', 'events' => 'account',
    'apps' => 'account', 'account' => 'account', 'oauth' => 'oidc', 'oidc' => 'oidc', 'admin' => 'admin'][explode('.', $r)[0]] ?? null;
$routes = $file ? require __DIR__ . "/../lib/routes/$file.php" : [];
if (!isset($routes[$r])) fail('not_found', 404);
housekeeping();
$routes[$r]();
