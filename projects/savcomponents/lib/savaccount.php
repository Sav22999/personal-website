<?php
/**
 * SavComponents — "Continue with SavAccount" (https://www.savaccount.eu, an OpenID Connect provider).
 *
 * The portal (savcomponents.com) and this API (saveriomorelli.com) are on different sites, so no cookie can carry the
 * sign-in: the browser makes a random key, keeps it in sessionStorage and sends it to start the flow; only that browser
 * can finish it. The code is exchanged here (code flow, PKCE S256, the client secret never leaves the server), and the
 * portal gets back a single-use hand-off code that works only together with its key.
 *
 * The rules (the same as illerom/identity, shared by the other projects):
 *   - a SavAccount already linked to an account signs in;
 *   - an address nobody has starts a new account, after the privacy notice is accepted;
 *   - an address whose account is confirmed is never joined in silence: the person proves the account is theirs with its
 *     password or with a code sent to the address;
 *   - an account whose address was never confirmed may have been opened by anyone: SavAccount proves the address, so it
 *     takes the account, and the password and sessions it had are dropped.
 * An account keeps at least one way to sign in: SavAccount can be unlinked only when the account has a password.
 */

declare(strict_types=1);

const SAV_FLOW_MINUTES = 15;   // from pressing the button to finishing (new account, proof of an existing one)
const SAV_CODE_MINUTES = 15;   // the code sent to prove an existing account
const SAV_CODE_TRIES = 5;
const SAV_BACK = ['docs' => '', 'academy' => 'academy/']; // where the portal wants to come back (relative to portal_url)

function sav_issuer(): string
{
    return rtrim((string)config_value('savaccount_issuer', 'https://www.savaccount.eu'), '/');
}

function sav_enabled(): bool
{
    return (string)config_value('savaccount_client_id', '') !== '' && (string)config_value('savaccount_client_secret', '') !== '' && function_exists('curl_init');
}

/** The address SavAccount comes back to: exactly the one registered for the service. */
function sav_redirect_uri(): string
{
    $set = (string)config_value('savaccount_redirect_uri', '');
    if ($set !== '') return $set;
    $dir = rtrim(dirname(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/api/index.php'))), '/');
    return (is_https() ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $dir . '/?r=auth.sav.callback';
}

function sav_b64u(string $s): string
{
    return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}

function sav_key(array $b): string
{
    $k = (string)($b['key'] ?? '');
    if (!preg_match('/^[a-f0-9]{64}$/', $k)) fail('bad_request', 422);
    return hash('sha256', $k);
}

function sav_back_url(string $back, string $query): string
{
    return app_url() . (SAV_BACK[$back] ?? '') . '?' . $query;
}

/** Starts a flow and gives the address of SavAccount. $uid: the signed-in account that links its SavAccount (mode connect). */
function sav_start(string $mode, string $keyHash, string $back, ?int $uid): string
{
    $state = sav_b64u(random_bytes(32));
    $nonce = sav_b64u(random_bytes(24));
    $verifier = sav_b64u(random_bytes(48));
    db()->prepare('DELETE FROM ' . t('sav_flows') . ' WHERE expires_at < ?')->execute([now_utc()]);
    db()->prepare('INSERT INTO ' . t('sav_flows') . ' (key_hash, state_hash, mode, back, user_id, nonce, verifier, created_at, expires_at) VALUES (?,?,?,?,?,?,?,?,?)')
        ->execute([$keyHash, hash('sha256', $state), $mode, $back, $uid, $nonce, $verifier, now_utc(), gmdate('Y-m-d H:i:s', time() + SAV_FLOW_MINUTES * 60)]);
    return sav_issuer() . '/authorize?' . http_build_query([
            'response_type' => 'code', 'client_id' => config_value('savaccount_client_id', ''), 'redirect_uri' => sav_redirect_uri(),
            'scope' => 'openid profile email', 'state' => $state, 'nonce' => $nonce,
            'code_challenge' => sav_b64u(hash('sha256', $verifier, true)), 'code_challenge_method' => 'S256', 'ui_locales' => 'en',
        ], '', '&', PHP_QUERY_RFC3986);
}

/** Exchanges the code for the identity (sub, verified email, name); null when SavAccount does not confirm it. */
function sav_identity(array $flow, string $code): ?array
{
    $id = (string)config_value('savaccount_client_id', '');
    $ch = curl_init(sav_issuer() . '/oauth/token');
    curl_setopt_array($ch, [
        CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_TIMEOUT => 20, CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query(['grant_type' => 'authorization_code', 'code' => $code, 'redirect_uri' => sav_redirect_uri(),
            'code_verifier' => $flow['verifier'], 'client_id' => $id, 'client_secret' => (string)config_value('savaccount_client_secret', '')], '', '&', PHP_QUERY_RFC3986),
    ]);
    $raw = (string)curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $d = json_decode($raw, true);
    if ($http !== 200 || !is_array($d) || !is_string($d['id_token'] ?? null)) {
        error_log('SavComponents SavAccount: token endpoint answered ' . $http . ' ' . (is_array($d) ? (string)($d['error'] ?? '') : curl_error($ch)));
        return null;
    }
    // The token comes straight from SavAccount over TLS, so its claims are enough (OpenID Connect Core 3.1.3.7)
    $parts = explode('.', $d['id_token']);
    $c = count($parts) === 3 ? json_decode((string)base64_decode(strtr($parts[1], '-_', '+/')), true) : null;
    $aud = is_array($c) ? ($c['aud'] ?? null) : null;
    $ok = is_array($c) && ($c['iss'] ?? '') === sav_issuer() && ($aud === $id || $aud === [$id]) && (int)($c['exp'] ?? 0) > time() - 60
        && is_string($c['nonce'] ?? null) && hash_equals((string)$flow['nonce'], $c['nonce'])
        && is_string($c['sub'] ?? null) && $c['sub'] !== '' && strlen($c['sub']) <= 190
        && ($c['email_verified'] ?? false) === true && filter_var($c['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$ok) {
        error_log('SavComponents SavAccount: the ID token is not valid');
        return null;
    }
    $email = mb_strtolower((string)$c['email']);
    $name = trim((string)(($c['nickname'] ?? '') !== '' ? $c['nickname'] : ($c['name'] ?? '')));
    if (mb_strlen($name) < 2) $name = (string)strstr($email, '@', true);
    return ['sub' => $c['sub'], 'email' => mb_substr($email, 0, 190), 'name' => mb_substr($name, 0, 80)];
}

function sav_user(string $where, array $args): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . " WHERE $where");
    $st->execute($args);
    return $st->fetch() ?: null;
}

/** SavAccount comes back here (a full page, not a fetch): it records the result and sends the browser back to the portal. */
function sav_callback(): void
{
    header('Cache-Control: no-store');
    header('Referrer-Policy: no-referrer');
    $state = $_GET['state'] ?? '';
    $flow = is_string($state) && $state !== '' ? sav_flow_row('state_hash = ? AND result IS NULL AND expires_at > ?', [hash('sha256', $state), now_utc()]) : null;
    $go = function (string $query, string $back = 'docs') {
        header('Location: ' . sav_back_url($back, $query), true, 302);
        exit;
    };
    if (!$flow) $go('sav_error=expired');
    $back = (string)$flow['back'];
    $drop = fn() => db()->prepare('DELETE FROM ' . t('sav_flows') . ' WHERE id = ?')->execute([$flow['id']]);
    if (!sav_enabled()) { $drop(); $go('sav_error=failed', $back); }
    if (isset($_GET['iss']) && $_GET['iss'] !== sav_issuer()) { $drop(); $go('sav_error=failed', $back); }
    if (isset($_GET['error'])) { $drop(); $go('sav_error=' . ($_GET['error'] === 'access_denied' ? 'cancelled' : 'failed'), $back); }
    $code = $_GET['code'] ?? '';
    $who = is_string($code) && $code !== '' && strlen($code) <= 500 ? sav_identity($flow, $code) : null;
    if (!$who) { $drop(); $go('sav_error=failed', $back); }

    $result = 'failed';
    $target = null;
    if ($flow['mode'] === 'connect') {
        $other = sav_user('sav_sub = ? AND id <> ?', [$who['sub'], (int)$flow['user_id']]);
        $result = $other ? 'taken' : 'connected';
        $target = (int)$flow['user_id'];
    } else {
        $u = sav_user('sav_sub = ?', [$who['sub']]);
        if ($u) {
            $result = $u['status'] === 'active' ? 'in' : 'disabled';
            $target = (int)$u['id'];
        } elseif ($u = user_by_email($who['email'])) {
            $target = (int)$u['id'];
            if ($u['status'] !== 'active') $result = 'disabled';
            elseif (empty($u['email_verified_at'])) {
                // never confirmed: whoever proves the address takes the account; its password and sessions go
                db()->prepare('UPDATE ' . t('users') . " SET sav_sub = ?, email_verified_at = ?, password_hash = '' WHERE id = ?")->execute([$who['sub'], now_utc(), $target]);
                db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$target]);
                db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ?')->execute([$target]);
                $result = 'in';
            } else $result = 'link';
        } else $result = 'signup';
    }
    $handoff = bin2hex(random_bytes(32));
    db()->prepare('UPDATE ' . t('sav_flows') . ' SET result = ?, target_id = ?, sub = ?, email = ?, name = ?, handoff_hash = ?, expires_at = ? WHERE id = ?')
        ->execute([$result, $target, $who['sub'], $who['email'], $who['name'], hash('sha256', $handoff), gmdate('Y-m-d H:i:s', time() + SAV_FLOW_MINUTES * 60), $flow['id']]);
    $go('sav=' . $handoff, $back);
}

function sav_flow_row(string $where, array $args): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('sav_flows') . " WHERE $where");
    $st->execute($args);
    return $st->fetch() ?: null;
}

/** The flow a hand-off code belongs to, only for the browser that started it; null when it expired or is not theirs. */
function sav_flow_for(array $b, array $results): array
{
    $h = (string)($b['handoff'] ?? '');
    if (!preg_match('/^[a-f0-9]{64}$/', $h)) fail('sav_expired', 410);
    $f = sav_flow_row('handoff_hash = ? AND expires_at > ?', [hash('sha256', $h), now_utc()]);
    if (!$f || !hash_equals((string)$f['key_hash'], sav_key($b)) || !in_array($f['result'], $results, true)) fail('sav_expired', 410);
    return $f;
}

function sav_drop(array $f): void
{
    db()->prepare('DELETE FROM ' . t('sav_flows') . ' WHERE id = ?')->execute([$f['id']]);
}

/** Links a SavAccount to an account (refusing one already used by another account) and tells the account by email. */
function sav_link(int $uid, string $sub): void
{
    if (sav_user('sav_sub = ? AND id <> ?', [$sub, $uid])) fail('sav_taken', 409);
    $u = user_row($uid);
    db()->prepare('UPDATE ' . t('users') . ' SET sav_sub = ? WHERE id = ?')->execute([$sub, $uid]);
    if ((string)($u['sav_sub'] ?? '') !== $sub) send_sav_notice($u, true);
}
