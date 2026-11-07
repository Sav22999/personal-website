<?php
/**
 * Household activity notifications: what the other members did, and Web Push to wake up the browsers.
 *
 * A push carries no content: it only wakes the service worker, which asks the API what is new (with the
 * user's session). So the push services never see any household data and no payload encryption is needed.
 * The VAPID key pair is created on first use and kept in the meta table.
 */

declare(strict_types=1);

/** Categories a user can turn off: action of the history => category. Other actions are never notified. */
const NOTIFIED_ACTIONS = [
    'add' => 'products', 'delete' => 'products', 'consume' => 'products', 'move_from_list' => 'products',
    'space_create' => 'spaces', 'space_rename' => 'spaces', 'space_delete' => 'spaces',
    'list_create' => 'lists', 'list_rename' => 'lists', 'list_delete' => 'lists', 'listitem_add' => 'lists', 'listitem_addmany' => 'lists',
    'plan_add' => 'plan',
    'household_rename' => 'members', 'household_transfer' => 'members', 'member_join' => 'members', 'member_leave' => 'members', 'member_remove' => 'members',
];
const NOTIFY_CATEGORIES = ['expiry', 'products', 'spaces', 'lists', 'plan', 'members'];
/** Hosts of the browsers' push services: subscriptions pointing anywhere else are refused. */
const PUSH_HOSTS = ['fcm.googleapis.com', 'android.googleapis.com', '.push.services.mozilla.com', '.push.apple.com', '.notify.windows.com'];

function notify_off(array $user): array
{
    return array_values(array_intersect(json_decode((string)($user['notify_off'] ?? ''), true) ?: [], NOTIFY_CATEGORIES));
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

/** Remembers that a household changed: the pushes leave after the response, once per request. */
function queue_push(int $hid, ?int $actorId, string $action): void
{
    static $queue = null;
    if (!isset(NOTIFIED_ACTIONS[$action])) return;
    if ($queue === null) {
        $queue = new ArrayObject();
        $list = $queue;
        register_shutdown_function(function () use ($list) {
            if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
            try {
                send_pushes((array)$list);
            } catch (Throwable $e) {
                error_log('[foodtrack] push: ' . $e->getMessage());
            }
        });
    }
    $queue[] = ['hid' => $hid, 'actor' => (int)$actorId, 'category' => NOTIFIED_ACTIONS[$action]];
}

/** Wakes up the devices of the other members; a device is woken at most every 15 seconds. */
function send_pushes(array $events): void
{
    if (!function_exists('curl_multi_init')) return;
    $subs = [];
    foreach ($events as $e) {
        $st = db()->prepare('SELECT s.id, s.endpoint, u.notify_off FROM ' . t('push_subscriptions') . ' s JOIN ' . t('household_members') . ' m ON m.user_id = s.user_id JOIN ' . t('users') . ' u ON u.id = s.user_id
            WHERE m.household_id = ? AND s.user_id <> ? AND (s.last_push_at IS NULL OR s.last_push_at < ?)');
        $st->execute([$e['hid'], $e['actor'], gmdate('Y-m-d H:i:s', time() - 15)]);
        foreach ($st->fetchAll() as $s) {
            if (!in_array($e['category'], notify_off($s), true)) $subs[(int)$s['id']] = $s['endpoint'];
        }
    }
    if (!$subs || !($keys = vapid_keys())) return;
    $subject = (is_https() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . app_base_path();
    $mh = curl_multi_init();
    $handles = [];
    foreach ($subs as $id => $endpoint) {
        $auth = vapid_header($endpoint, $keys, $subject);
        if (!$auth) continue;
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => '', CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_TIMEOUT => 4,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $auth, 'TTL: 86400', 'Urgency: normal', 'Content-Length: 0'],
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$id] = $ch;
    }
    do {
        curl_multi_exec($mh, $running);
        if ($running) curl_multi_select($mh, 1);
    } while ($running);
    $touch = db()->prepare('UPDATE ' . t('push_subscriptions') . ' SET last_push_at = ? WHERE id = ?');
    $drop = db()->prepare('DELETE FROM ' . t('push_subscriptions') . ' WHERE id = ?');
    foreach ($handles as $id => $ch) {
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code === 404 || $code === 410) $drop->execute([$id]); // the device unsubscribed
        else $touch->execute([now_utc(), $id]);
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);
}

/** Text of one household event, in the language of the request. */
function event_text(array $e): string
{
    $name = $e['item_name'];
    $d = json_decode((string)$e['details'], true) ?: [];
    $n = (int)($d['n'] ?? 0);
    $texts = [
        'add' => 'added “{name}”', 'delete' => 'deleted “{name}”', 'consume' => 'finished “{name}”', 'move_from_list' => 'bought “{name}”',
        'space_create' => 'created the space “{name}”', 'space_rename' => 'renamed a space to “{name}”', 'space_delete' => 'deleted the space “{name}”',
        'list_create' => 'created the list “{name}”', 'list_rename' => 'renamed a list to “{name}”', 'list_delete' => 'deleted the list “{name}”',
        'listitem_add' => 'added “{name}” to a list', 'listitem_addmany' => 'added {n} products to “{name}”', 'plan_add' => 'planned “{name}”',
        'household_rename' => 'renamed the household to “{name}”', 'household_transfer' => 'handed the household over to {name}',
        'member_join' => 'joined the household', 'member_leave' => 'left the household', 'member_remove' => 'removed {name} from the household',
    ];
    return $e['user_name'] . ' ' . tr($texts[$e['action']] ?? 'changed something', ['name' => $name, 'n' => $n]);
}

/** What the other members did after a history id, in the households of the user and for the chosen categories. */
function activity_pending(array $u, int $after): array
{
    $st = db()->prepare('SELECT MAX(h.id) FROM ' . t('history') . ' h JOIN ' . t('household_members') . ' m ON m.household_id = h.household_id WHERE m.user_id = ?');
    $st->execute([$u['id']]);
    $last = (int)$st->fetchColumn();
    if (!$after) return ['lastId' => $last, 'count' => 0, 'text' => null]; // First check on a device: only learn where we are
    $st = db()->prepare('SELECT h.* FROM ' . t('history') . ' h JOIN ' . t('household_members') . ' m ON m.household_id = h.household_id
        WHERE m.user_id = ? AND h.id > ? AND (h.user_id IS NULL OR h.user_id <> ?) ORDER BY h.id LIMIT 200');
    $st->execute([$u['id'], $after, $u['id']]);
    $off = notify_off($u);
    $events = array_values(array_filter($st->fetchAll(), function ($e) use ($off) {
        $category = NOTIFIED_ACTIONS[$e['action']] ?? null;
        return $category && !in_array($category, $off, true);
    }));
    $text = null;
    $count = count($events);
    if ($count === 1) $text = event_text($events[0]);
    elseif ($count > 1) {
        $names = array_unique(array_column($events, 'user_name'));
        $text = count($names) === 1 ? tr('{name} made {n} changes in the household', ['name' => reset($names), 'n' => $count]) : tr('{n} new changes in the household', ['n' => $count]);
    }
    return ['lastId' => max($last, $after), 'count' => $count, 'text' => $text];
}
