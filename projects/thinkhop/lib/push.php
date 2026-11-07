<?php
/**
 * ThinkHop — notifications. Same idea as FoodTrack: a push carries no content, it only wakes the service worker,
 * which asks the API what is new with the player's session. So the push services never see any data.
 * The VAPID key pair is created on first use and kept in the meta table.
 */

declare(strict_types=1);

/** What a player can turn on and off, one by one. */
const NOTIFY_TYPES = ['daily', 'streak', 'friends', 'overtaken'];
/** Hosts of the browsers' push services: subscriptions pointing anywhere else are refused. */
const PUSH_HOSTS = ['fcm.googleapis.com', 'android.googleapis.com', '.push.services.mozilla.com', '.push.apple.com', '.notify.windows.com'];
const PUSH_SUBJECT = 'https://www.saveriomorelli.com/projects/thinkhop/';
const MAX_DEVICES = 10;

const NOTIFY_TEXTS = [
    'en' => ['daily' => 'Dedo has fresh questions: today’s challenge is waiting for you!', 'streak' => 'Punto is worried: your {n}-day streak ends today!', 'friends' => '{name} added you as a friend.', 'overtaken' => '{name} overtook you in today’s ranking!'],
    'it' => ['daily' => 'Dedo ha domande fresche: la sfida di oggi ti aspetta!', 'streak' => 'Punto è in ansia: la tua serie di {n} giorni finisce oggi!', 'friends' => '{name} ti ha aggiunto agli amici.', 'overtaken' => '{name} ti ha superato nella classifica di oggi!'],
    'es' => ['daily' => 'Dedo trae preguntas frescas: ¡el desafío de hoy te espera!', 'streak' => 'Punto está preocupado: ¡tu racha de {n} días termina hoy!', 'friends' => '{name} te ha añadido a sus amigos.', 'overtaken' => '¡{name} te ha adelantado en la clasificación de hoy!'],
    'fr' => ['daily' => 'Dedo a des questions fraîches : le défi du jour t’attend !', 'streak' => 'Punto s’inquiète : ta série de {n} jours se termine aujourd’hui !', 'friends' => '{name} t’a ajouté à ses amis.', 'overtaken' => '{name} t’a dépassé au classement du jour !'],
    'de' => ['daily' => 'Dedo hat frische Fragen: Die Tagesaufgabe wartet auf dich!', 'streak' => 'Punto macht sich Sorgen: Deine Serie von {n} Tagen endet heute!', 'friends' => '{name} hat dich als Freund hinzugefügt.', 'overtaken' => '{name} hat dich in der Tagesrangliste überholt!'],
];

function notify_off(array $user): array
{
    return array_values(array_intersect(json_decode((string)($user['notify_off'] ?? ''), true) ?: [], NOTIFY_TYPES));
}

function notify_text(string $lang, string $type, array $data): string
{
    $text = (NOTIFY_TEXTS[$lang] ?? NOTIFY_TEXTS['en'])[$type];
    foreach ($data as $k => $v) $text = str_replace('{' . $k . '}', (string)$v, $text);
    return $text;
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
    return vapid_keys_saved() ?: $keys;
}

function vapid_keys_saved(): ?array
{
    $get = db()->prepare('SELECT v FROM ' . t('meta') . " WHERE k = 'vapid_keys'");
    $get->execute();
    $saved = json_decode((string)$get->fetchColumn(), true);
    return is_array($saved) ? $saved : null;
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
function vapid_header(string $endpoint, array $keys): ?string
{
    $p = parse_url($endpoint);
    $origin = $p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
    $data = b64url(json_encode(['typ' => 'JWT', 'alg' => 'ES256'])) . '.' . b64url(json_encode(['aud' => $origin, 'exp' => time() + 43200, 'sub' => PUSH_SUBJECT]));
    if (!openssl_sign($data, $der, $keys['private'], OPENSSL_ALGO_SHA256)) return null;
    return 'vapid t=' . $data . '.' . b64url(der_to_raw($der)) . ', k=' . $keys['public'];
}

/**
 * Keeps a notification for a player and wakes their devices after the response. The same day, type and ref can only
 * be stored once, and nothing is stored for who turned that type off or has no device to tell.
 */
function notify_user(int $userId, string $type, string $day, string $ref, array $data): bool
{
    $st = db()->prepare('SELECT u.notify_off, COUNT(s.id) AS devices FROM ' . t('users') . ' u LEFT JOIN ' . t('push_subscriptions') . ' s ON s.user_id = u.id WHERE u.id = ? GROUP BY u.id');
    $st->execute([$userId]);
    $row = $st->fetch();
    if (!$row || !(int)$row['devices'] || in_array($type, notify_off($row), true)) return false;
    $ins = db()->prepare('INSERT IGNORE INTO ' . t('notifications') . ' (user_id, type, day, ref, data, created_at) VALUES (?,?,?,?,?,?)');
    $ins->execute([$userId, $type, $day, $ref, json_encode($data, JSON_UNESCAPED_UNICODE), now_utc()]);
    if (!$ins->rowCount()) return false;
    queue_push($userId);
    return true;
}

function queue_push(int $userId): void
{
    static $queue = null;
    if ($queue === null) {
        $queue = new ArrayObject();
        $list = $queue;
        register_shutdown_function(function () use ($list) {
            if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
            try {
                send_pushes(array_values(array_unique((array)$list)));
            } catch (Throwable $e) {
                error_log('[thinkhop] push: ' . $e->getMessage());
            }
        });
    }
    $queue[] = $userId;
}

/** Wakes up the devices of these players. */
function send_pushes(array $userIds): void
{
    if (!$userIds || !function_exists('curl_multi_init') || !($keys = vapid_keys())) return;
    $marks = implode(',', array_fill(0, count($userIds), '?'));
    $st = db()->prepare('SELECT id, endpoint FROM ' . t('push_subscriptions') . " WHERE user_id IN ($marks)");
    $st->execute($userIds);
    $mh = curl_multi_init();
    $handles = [];
    foreach ($st->fetchAll() as $s) {
        if (!push_endpoint_allowed($s['endpoint'])) continue;
        $auth = vapid_header($s['endpoint'], $keys);
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

/** Tells the friends who just lost a place in today's ranking. Who stays out of the ranking overtakes nobody. */
function notify_overtaken(array $me, array $game): void
{
    if ($game['mode'] !== 'daily' || empty($me['username']) || !(int)($me['show_in_ranking'] ?? 1)) return;
    $st = db()->prepare('SELECT f.user_id FROM ' . t('friends') . ' f JOIN ' . t('games') . " g ON g.user_id = f.user_id AND g.mode = 'daily' AND g.day = ?
        WHERE f.friend_id = ? GROUP BY f.user_id HAVING MAX(g.score) < ?");
    $st->execute([$game['day'], $me['id'], $game['score']]);
    foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $id) notify_user((int)$id, 'overtaken', $game['day'], $me['username'], ['name' => $me['username']]);
}
