<?php
/**
 * Notifications for the Android apps through Firebase Cloud Messaging (HTTP v1 API).
 *
 * A WebView cannot receive Web Push, so the app registers its FCM token as one more push subscription, with the
 * endpoint "fcm:<token>". Same idea as Web Push: the message carries no content, it only wakes the app, which asks
 * the API what is new with the session of its WebView. So Google never sees any data.
 *
 * The key of the Firebase service account is shared by all the apps of the site: /.fcm-service-account.json in the
 * site root (hidden files are blocked from the browser by .htaccess, and it is git-ignored). Without it the Android
 * devices are simply not woken. The same file is copied in every project that sends notifications.
 */

declare(strict_types=1);

const FCM_PREFIX = 'fcm:';

function fcm_is(string $endpoint): bool
{
    return strncmp($endpoint, FCM_PREFIX, strlen(FCM_PREFIX)) === 0;
}

/** A device token as Firebase gives it (letters, digits, - _ :). */
function fcm_endpoint_valid(string $endpoint): bool
{
    return preg_match('/^fcm:[A-Za-z0-9_:\-]{20,600}$/', $endpoint) === 1;
}

function fcm_account(): ?array
{
    static $account = false;
    if ($account !== false) return $account;
    $file = getenv('FCM_SERVICE_ACCOUNT') ?: dirname(__DIR__, 3) . '/.fcm-service-account.json';
    $d = is_file($file) ? json_decode((string)file_get_contents($file), true) : null;
    $ok = is_array($d) && !empty($d['client_email']) && !empty($d['private_key']) && !empty($d['project_id']);
    return $account = $ok ? $d : null;
}

/** An OAuth access token for FCM, kept in the temporary folder while it is valid (one hour). */
function fcm_access_token(array $account): ?string
{
    $cache = sys_get_temp_dir() . '/fcm-' . md5($account['client_email']) . '.json';
    $saved = is_file($cache) ? json_decode((string)@file_get_contents($cache), true) : null;
    if (is_array($saved) && ($saved['exp'] ?? 0) > time() + 120 && !empty($saved['token'])) return $saved['token'];

    $enc = fn(string $s): string => rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    $now = time();
    $data = $enc(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])) . '.' . $enc(json_encode([
            'iss' => $account['client_email'], 'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token', 'iat' => $now, 'exp' => $now + 3600,
        ]));
    if (!openssl_sign($data, $signature, $account['private_key'], OPENSSL_ALGO_SHA256)) return null;

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_TIMEOUT => 6,
        CURLOPT_POSTFIELDS => http_build_query(['grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $data . '.' . $enc($signature)]),
    ]);
    $res = json_decode((string)curl_exec($ch), true);
    if (empty($res['access_token'])) {
        error_log('[fcm] no access token: ' . json_encode($res['error'] ?? $res));
        return null;
    }
    if (@file_put_contents($cache, json_encode(['token' => $res['access_token'], 'exp' => $now + (int)($res['expires_in'] ?? 3600)]), LOCK_EX)) @chmod($cache, 0600);
    return $res['access_token'];
}

/**
 * Wakes these Android devices ([subscription id => "fcm:<token>"]).
 * Returns the ids of the devices that no longer exist (app uninstalled or token replaced), to delete.
 */
function fcm_send(array $subs): array
{
    if (!$subs || !function_exists('curl_multi_init') || !($account = fcm_account()) || !($token = fcm_access_token($account))) return [];
    $url = 'https://fcm.googleapis.com/v1/projects/' . rawurlencode($account['project_id']) . '/messages:send';
    $mh = curl_multi_init();
    $handles = [];
    foreach ($subs as $id => $endpoint) {
        if (!fcm_endpoint_valid($endpoint)) continue;
        $body = json_encode(['message' => [
            'token' => substr($endpoint, strlen(FCM_PREFIX)),
            'data' => ['wake' => '1'],
            'android' => ['priority' => 'high', 'ttl' => '43200s'],
        ]]);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Content-Type: application/json'],
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$id] = $ch;
    }
    do {
        curl_multi_exec($mh, $running);
        if ($running) curl_multi_select($mh, 1);
    } while ($running);
    $gone = [];
    foreach ($handles as $id => $ch) {
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $text = (string)curl_multi_getcontent($ch);
        // 404 UNREGISTERED: the app was uninstalled or the token replaced. 400 with a bad token: it never was valid.
        if ($code === 404 || ($code === 400 && strpos($text, 'registration token') !== false)) $gone[] = $id;
        elseif ($code >= 400) error_log('[fcm] HTTP ' . $code . ': ' . substr($text, 0, 300));
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);
    return $gone;
}
