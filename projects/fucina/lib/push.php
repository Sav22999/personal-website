<?php
/**
 * Fucina — notifications. A notification is a row of the queue, and Web Push only wakes the service worker,
 * which then asks the API what is new with the session of the user. So the push services never see any content
 * and no payload encryption is needed. The VAPID key pair is created on first use and kept in the meta table.
 */

declare(strict_types=1);

const NOTIFY_KINDS = ['daily', 'streak', 'prizes', 'friends'];
const NOTIFY_LIVE = ['prizes', 'friends'];
const NOTIFY_SCHEDULED = ['daily', 'streak'];
const DEFAULT_NOTIFY_HOURS = ['daily' => 18, 'streak' => 18];
/** Hosts of the browsers' push services: subscriptions pointing anywhere else are refused. */
const PUSH_HOSTS = ['fcm.googleapis.com', 'android.googleapis.com', '.push.services.mozilla.com', '.push.apple.com', '.notify.windows.com'];
const NOTIFY_PAGES = ['daily' => './#/daily', 'streak' => './#/home', 'prizes' => './#/board', 'friends' => './#/friends'];

/** The kinds a user has turned off. */
function notify_off(?string $json): array
{
    return array_values(array_intersect(json_decode((string)$json, true) ?: [], NOTIFY_KINDS));
}

/** Chosen hours for scheduled reminders (whole hours 0-23) in server timezone (Europe/Rome). */
function notify_hours(?string $json): array
{
    $saved = json_decode((string)$json, true);
    $res = DEFAULT_NOTIFY_HOURS;
    if (is_array($saved)) {
        foreach (NOTIFY_SCHEDULED as $k) {
            if (isset($saved[$k]) && is_numeric($saved[$k])) {
                $h = (int)$saved[$k];
                if ($h >= 0 && $h <= 23) $res[$k] = $h;
            }
        }
    }
    return $res;
}

/** The text of a notification, in the language of the user: the service worker has no dictionary. */
function notify_text(string $kind, array $d, string $lang): string
{
    $it = $lang === 'it';
    switch ($kind) {
        case 'daily':
            return $it ? 'La sfida del giorno ti aspetta: il podio è ancora libero!' : 'The daily challenge is waiting: the podium is still open!';
        case 'streak':
            return $it ? "Serie di {$d['days']} giorni a rischio: accendi la forgia oggi!" : "Your {$d['days']}-day streak is at risk: light the forge today!";
        case 'prizes':
            return $it ? "Podio! {$d['place']}° nella sfida di ieri: +{$d['points']} Scintille." : "Podium! Number {$d['place']} in yesterday’s challenge: +{$d['points']} Sparks.";
        case 'friends':
            return $it ? "{$d['name']} ti ha aggiunto tra gli amici." : "{$d['name']} added you as a friend.";
    }
    return '';
}

/** Queues a notification, unless the user turned that kind off or has no device to tell. It leaves after the response. */
function notify_user(int $uid, string $kind, array $data = []): bool
{
    $st = db()->prepare('SELECT u.notify_off, COUNT(s.id) AS devices FROM ' . t('users') . ' u LEFT JOIN ' . t('push_subscriptions') . ' s ON s.user_id = u.id WHERE u.id = ? GROUP BY u.id');
    $st->execute([$uid]);
    $u = $st->fetch();
    if (!$u || !(int)$u['devices'] || in_array($kind, notify_off($u['notify_off']), true)) return false;
    db()->prepare('INSERT INTO ' . t('notifications') . ' (user_id, kind, data, created_at) VALUES (?,?,?,?)')->execute([$uid, $kind, json_encode($data, JSON_UNESCAPED_UNICODE), now_utc()]);
    queue_push($uid);
    return true;
}

function queue_push(int $uid): void
{
    static $queue = null;
    if ($queue === null) {
        $queue = new ArrayObject();
        $list = $queue;
        register_shutdown_function(function () use ($list) {
            if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
            try {
                send_pushes(array_keys((array)$list));
            } catch (Throwable $e) {
                error_log('Fucina push: ' . $e->getMessage());
            }
        });
    }
    $queue[$uid] = true;
}

/** What is waiting for a user: it is delivered once, and what is older than a day is no longer news. */
function notify_pending(int $uid): array
{
    $st = db()->prepare('SELECT n.kind, n.data, n.created_at, u.lang FROM ' . t('notifications') . ' n JOIN ' . t('users') . ' u ON u.id = n.user_id WHERE n.user_id = ? ORDER BY n.id');
    $st->execute([$uid]);
    $out = [];
    foreach ($st->fetchAll() as $n) {
        if (strtotime($n['created_at'] . ' UTC') < time() - 86400) continue;
        $out[] = ['tag' => 'fc-' . $n['kind'], 'text' => notify_text($n['kind'], json_decode($n['data'], true) ?: [], (string)$n['lang']), 'page' => NOTIFY_PAGES[$n['kind']]];
    }
    db()->prepare('DELETE FROM ' . t('notifications') . ' WHERE user_id = ?')->execute([$uid]);
    return $out;
}

function push_endpoint_allowed(string $url): bool
{
    $p = parse_url($url);
    if (($p['scheme'] ?? '') !== 'https' || empty($p['host'])) return false;
    foreach (PUSH_HOSTS as $h) {
        if ($h[0] === '.' ? substr($p['host'], -strlen($h)) === $h : $p['host'] === $h) return true;
    }
    return false;
}

function b64url(string $s): string
{
    return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}

/** The VAPID keys: ['private' => PEM, 'public' => base64url], or null when PHP cannot create them. */
function vapid_keys(): ?array
{
    $get = db()->prepare('SELECT v FROM ' . t('meta') . " WHERE k = 'vapid_keys'");
    $get->execute();
    $saved = json_decode((string)$get->fetchColumn(), true);
    if (is_array($saved) && !empty($saved['private'])) return $saved;
    if (!function_exists('openssl_pkey_new')) return null;
    $key = @openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']);
    if (!$key || !openssl_pkey_export($key, $pem)) return null;
    $ec = openssl_pkey_get_details($key)['ec'];
    $keys = ['private' => $pem, 'public' => b64url("\x04" . str_pad($ec['x'], 32, "\0", STR_PAD_LEFT) . str_pad($ec['y'], 32, "\0", STR_PAD_LEFT))];
    db()->prepare('INSERT IGNORE INTO ' . t('meta') . " (k, v) VALUES ('vapid_keys', ?)")->execute([json_encode($keys)]);
    return $keys;
}

/** Converts an ES256 signature from the DER format of OpenSSL to the raw r||s format of JWT. */
function der_to_raw(string $der): string
{
    $pos = 2;
    if (ord($der[1]) & 0x80) $pos += ord($der[1]) & 0x7f;
    $raw = '';
    for ($i = 0; $i < 2; $i++) {
        $len = ord($der[$pos + 1]);
        $raw .= str_pad(ltrim(substr($der, $pos + 2, $len), "\0"), 32, "\0", STR_PAD_LEFT);
        $pos += 2 + $len;
    }
    return $raw;
}

/** The Authorization header of a push request. */
function vapid_header(string $endpoint, array $keys, string $subject): ?string
{
    $p = parse_url($endpoint);
    $origin = $p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
    $data = b64url(json_encode(['typ' => 'JWT', 'alg' => 'ES256'])) . '.' . b64url(json_encode(['aud' => $origin, 'exp' => time() + 43200, 'sub' => $subject]));
    if (!openssl_sign($data, $der, $keys['private'], OPENSSL_ALGO_SHA256)) return null;
    return 'vapid t=' . $data . '.' . b64url(der_to_raw($der)) . ', k=' . $keys['public'];
}

/** Wakes up the devices of some users. A device that no longer exists is forgotten. */
function send_pushes(array $userIds): void
{
    if (!$userIds || !function_exists('curl_multi_init') || !($keys = vapid_keys())) return;
    $in = implode(',', array_map('intval', $userIds));
    $subs = db()->query('SELECT id, endpoint FROM ' . t('push_subscriptions') . " WHERE user_id IN ($in)")->fetchAll();
    $mh = curl_multi_init();
    $handles = [];
    foreach ($subs as $s) {
        $auth = vapid_header($s['endpoint'], $keys, 'https://www.saveriomorelli.com/projects/fucina/');
        if (!$auth) continue;
        $ch = curl_init($s['endpoint']);
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => '', CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_TIMEOUT => 4,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $auth, 'TTL: 43200', 'Urgency: normal', 'Content-Length: 0'],
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[(int)$s['id']] = $ch;
    }
    do {
        curl_multi_exec($mh, $running);
        if ($running) curl_multi_select($mh, 1);
    } while ($running);
    $drop = db()->prepare('DELETE FROM ' . t('push_subscriptions') . ' WHERE id = ?');
    foreach ($handles as $id => $ch) {
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code === 404 || $code === 410) $drop->execute([$id]);
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);
}
