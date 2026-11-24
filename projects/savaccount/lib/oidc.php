<?php
/**
 * SavAccount — OpenID Connect provider (authorization code + PKCE, ID tokens RS256, opaque access tokens, rotating
 * refresh tokens). The issuer is savaccount.eu: its gateway exposes the endpoints.
 */

declare(strict_types=1);

const SCOPES = ['openid', 'profile', 'email', 'birthdate', 'offline_access'];
const CODE_SECONDS = 60;
const ACCESS_SECONDS = 3600;
const ID_SECONDS = 600;
const REFRESH_DAYS = 60;

// ---------- Services ----------

function client_row(int $id): ?array
{
    return one('SELECT * FROM ' . t('clients') . ' WHERE id = ?', [$id]);
}

function client_by_id($clientId): ?array
{
    return is_string($clientId) && preg_match('/^[a-z0-9]{24}$/', $clientId) ? one('SELECT * FROM ' . t('clients') . ' WHERE client_id = ?', [$clientId]) : null;
}

/** What users see of a service. */
function public_client(array $c): array
{
    return ['name' => $c['name'], 'description' => $c['description'], 'homepage' => $c['homepage_url'], 'logo' => $c['logo_url'],
        'privacy' => $c['privacy_url'], 'terms' => $c['terms_url'], 'active' => $c['status'] === 'active'];
}

function scope_sort(array $scopes): array
{
    return array_values(array_intersect(SCOPES, $scopes));
}

function client_scopes(array $c): array
{
    return scope_sort(array_merge(explode(' ', $c['scopes_required']), explode(' ', $c['scopes_optional'])));
}

/** The address a service receives: the chosen one, or the primary if it was removed. */
function grant_email(array $g): array
{
    $e = $g['email_id'] ? one('SELECT * FROM ' . t('emails') . ' WHERE id = ? AND user_id = ? AND verified_at IS NOT NULL', [$g['email_id'], $g['user_id']]) : null;
    return $e ?: primary_email((int)$g['user_id']);
}

/** The identifier of the user for one service: with pairwise ids two services cannot compare their users. */
function subject_for(array $client, array $user): string
{
    return $client['pairwise'] ? b64u(hash_hmac('sha256', $client['client_id'] . '|' . $user['uid'], subkey('pairwise'), true)) : $user['uid'];
}

function claims_for(array $client, array $user, array $grant, array $scopes): array
{
    $c = ['sub' => subject_for($client, $user)];
    if (in_array('profile', $scopes, true)) {
        $c += ['name' => trim($user['first_name'] . ' ' . $user['last_name']), 'given_name' => $user['first_name'], 'family_name' => $user['last_name'], 'locale' => $user['locale']];
        if ($user['nickname'] !== '') $c['nickname'] = $user['nickname'];
    }
    if (in_array('email', $scopes, true)) $c += ['email' => grant_email($grant)['email'], 'email_verified' => true];
    if (in_array('birthdate', $scopes, true) && $user['birthdate']) $c['birthdate'] = $user['birthdate'];
    return $c;
}

// ---------- Signing keys and ID tokens ----------

function signing_key(): array
{
    $k = one('SELECT * FROM ' . t('signing_keys') . ' WHERE retired_at IS NULL ORDER BY id DESC LIMIT 1');
    return $k ?: create_signing_key();
}

function create_signing_key(): array
{
    $opt = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
    $res = @openssl_pkey_new($opt);
    if (!$res) { // shared hosting without an openssl.cnf
        $opt['config'] = __DIR__ . '/openssl.cnf';
        $res = openssl_pkey_new($opt);
    }
    $pem = '';
    if (!$res || !openssl_pkey_export($res, $pem, null, $opt)) throw new RuntimeException('cannot create the signing key');
    $public = openssl_pkey_get_details($res)['key'];
    $kid = substr(hash('sha256', $public), 0, 16);
    q('INSERT INTO ' . t('signing_keys') . ' (kid, public_pem, private_enc, created_at) VALUES (?,?,?,?)', [$kid, $public, encrypt($pem, 'key:' . $kid), now_utc()]);
    return one('SELECT * FROM ' . t('signing_keys') . ' WHERE kid = ?', [$kid]);
}

/** Keys retired less than a day ago are still published, so the tokens they signed can be checked. */
function jwks(): array
{
    signing_key();
    $keys = [];
    foreach (all('SELECT * FROM ' . t('signing_keys') . ' WHERE retired_at IS NULL OR retired_at > ? ORDER BY id DESC', [now_utc(-86400)]) as $k) {
        $rsa = openssl_pkey_get_details(openssl_pkey_get_public($k['public_pem']))['rsa'];
        $keys[] = ['kty' => 'RSA', 'use' => 'sig', 'alg' => 'RS256', 'kid' => $k['kid'], 'n' => b64u($rsa['n']), 'e' => b64u($rsa['e'])];
    }
    return ['keys' => $keys];
}

function jwt(array $claims): string
{
    $k = signing_key();
    $json = fn($a) => b64u(json_encode($a, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    $in = $json(['alg' => 'RS256', 'typ' => 'JWT', 'kid' => $k['kid']]) . '.' . $json($claims);
    $sig = '';
    if (!openssl_sign($in, $sig, decrypt($k['private_enc'], 'key:' . $k['kid']), OPENSSL_ALGO_SHA256)) throw new RuntimeException('cannot sign');
    return $in . '.' . b64u($sig);
}

/** Claims of an ID token we signed (its expiry does not matter: it only hints at who is signing out). */
function read_own_jwt(string $jwt): ?array
{
    $p = explode('.', $jwt);
    if (count($p) !== 3) return null;
    $dec = fn($x) => (string)base64_decode(strtr($x, '-_', '+/'));
    $h = json_decode($dec($p[0]), true);
    if (!is_array($h) || ($h['alg'] ?? '') !== 'RS256' || !is_string($h['kid'] ?? null)) return null;
    $k = one('SELECT public_pem FROM ' . t('signing_keys') . ' WHERE kid = ?', [$h['kid']]);
    if (!$k || openssl_verify($p[0] . '.' . $p[1], $dec($p[2]), $k['public_pem'], OPENSSL_ALGO_SHA256) !== 1) return null;
    $c = json_decode($dec($p[1]), true);
    return is_array($c) && ($c['iss'] ?? '') === cfg('site') ? $c : null;
}

// ---------- Authorization requests ----------

function with_query(string $uri, array $params): string
{
    return $uri . (strpos($uri, '?') === false ? '?' : '&') . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}

/** Error sent back to the service, through the browser. */
function authz_error(array $req, string $error, string $description = ''): void
{
    $p = ['error' => $error, 'iss' => cfg('site')];
    if ($description !== '') $p['error_description'] = $description;
    if ($req['state'] !== '') $p['state'] = $req['state'];
    ok(['status' => 'redirect', 'url' => with_query($req['redirect_uri'], $p)]);
}

/**
 * Checks an authorization request. A wrong service or redirect address never redirects: the person is told here.
 * Everything else goes back to the service as an OAuth error.
 */
function authz_request(array $p): array
{
    $get = fn(string $k, int $max) => isset($p[$k]) && is_string($p[$k]) && strlen($p[$k]) <= $max ? $p[$k] : '';
    $client = client_by_id($get('client_id', 24));
    if (!$client) fail('client_unknown', 404);
    $redirect = $get('redirect_uri', 500);
    if ($redirect === '' || !redirect_allowed($client, $redirect)) fail('redirect_invalid', 400, ['client' => $client['name']]);
    if ($client['status'] !== 'active') fail('client_disabled', 403, ['client' => $client['name']]);

    $req = ['client' => $client, 'redirect_uri' => $redirect, 'state' => $get('state', 1000), 'nonce' => $get('nonce', 255), 'pkce' => $get('code_challenge', 43)];
    if (isset($p['state']) && $req['state'] === '' && $p['state'] !== '') authz_error($req, 'invalid_request', 'state too long');
    if (isset($p['nonce']) && $req['nonce'] === '' && $p['nonce'] !== '') authz_error($req, 'invalid_request', 'nonce too long');
    if (isset($p['request'])) authz_error($req, 'request_not_supported');
    if (isset($p['request_uri'])) authz_error($req, 'request_uri_not_supported');
    if ($get('response_type', 50) !== 'code') authz_error($req, 'unsupported_response_type', 'only code');
    if (!in_array($get('response_mode', 20), ['', 'query'], true)) authz_error($req, 'invalid_request', 'response_mode must be query');
    if ($get('code_challenge_method', 10) !== 'S256' || !preg_match('/^[A-Za-z0-9_-]{43}$/', $req['pkce'])) authz_error($req, 'invalid_request', 'PKCE with S256 is required');
    $asked = array_filter(explode(' ', $get('scope', 300)));
    if (!in_array('openid', $asked, true)) authz_error($req, 'invalid_scope', 'the openid scope is required');
    $required = explode(' ', $client['scopes_required']);
    $req['scopes'] = scope_sort(array_intersect($asked, client_scopes($client)));
    $req['required'] = array_values(array_intersect($req['scopes'], $required));
    $req['optional'] = array_values(array_diff($req['scopes'], $required));
    $req['prompt'] = array_filter(explode(' ', $get('prompt', 60)));
    if (in_array('none', $req['prompt'], true) && count($req['prompt']) > 1) authz_error($req, 'invalid_request', 'prompt=none must be alone');
    $max = $get('max_age', 10);
    if ($max !== '' && !ctype_digit($max)) authz_error($req, 'invalid_request', 'max_age');
    $req['max_age'] = $max === '' ? null : (int)$max;
    $req['login_hint'] = $get('login_hint', 190);
    return $req;
}

/** Exact match. Native apps on the loopback address may use any port (RFC 8252). */
function redirect_allowed(array $client, string $uri): bool
{
    $list = json_decode($client['redirect_uris'], true) ?: [];
    if (in_array($uri, $list, true)) return true;
    $strip = fn($u) => preg_match('~^http://(127\.0\.0\.1|\[::1\])(?::\d{1,5})?(/[^#]*)?$~', $u, $m) ? 'http://' . $m[1] . ($m[2] ?? '') : null;
    $bare = $strip($uri);
    if ($bare === null || $client['type'] !== 'public') return false;
    foreach ($list as $u) if ($strip($u) === $bare) return true;
    return false;
}

/**
 * Where the request stands for this browser: 'login', 'reauth', 'mfa_setup', 'consent' or 'ready'.
 * $grace: seconds a fresh sign-in stays fresh (longer when approving: reading the consent takes time).
 */
function authz_status(array $req, ?array $s, int $grace): string
{
    if (!$s) return 'login';
    $age = time() - ts($s['auth_time']);
    if (in_array('login', $req['prompt'], true) && $age > $grace) return 'reauth';
    if ($req['max_age'] !== null && $age > max($req['max_age'], $grace)) return 'reauth';
    if ($req['client']['require_2fa']) {
        if (!has_2fa($s['user'])) return 'mfa_setup';
        if (strpos((string)$s['amr'], 'mfa') === false) return 'reauth';
    }
    $g = grant_row($s, $req['client']);
    if (!$g || array_diff($req['required'], explode(' ', $g['scopes'])) || in_array('consent', $req['prompt'], true)) return 'consent';
    return 'ready';
}

function grant_row(array $s, array $client): ?array
{
    return one('SELECT * FROM ' . t('grants') . ' WHERE user_id = ? AND client_id = ?', [$s['user_id'], $client['id']]);
}

// ---------- Tokens ----------

/** OAuth error answer (RFC 6749). */
function oauth_fail(string $error, string $description = '', int $code = 400): void
{
    header('Pragma: no-cache');
    send_json(['error' => $error] + ($description !== '' ? ['error_description' => $description] : []), $code);
}

/** The parameters of a form post; a repeated or structured parameter is refused. */
function form_params(): array
{
    $raw = (string)file_get_contents('php://input', false, null, 0, 16385);
    if (strlen($raw) > 16384 || stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/x-www-form-urlencoded') !== 0) oauth_fail('invalid_request', 'form-urlencoded body expected');
    $out = [];
    foreach (array_filter(explode('&', $raw)) as $pair) {
        [$k, $v] = array_map('urldecode', explode('=', $pair, 2) + [1 => '']);
        if (isset($out[$k])) oauth_fail('invalid_request', 'repeated parameter');
        $out[$k] = $v;
    }
    return $out;
}

function authorization_header(): string
{
    return (string)($_SERVER['HTTP_X_SAV_AUTHORIZATION'] ?? '');
}

/** The service making the call, authenticated. Public ones have no secret (PKCE protects them). */
function token_client(array $p): array
{
    if (rate_count('client', ip_subject(), 10) >= 30) oauth_fail('slow_down', 'too many failed attempts', 429);
    $id = $p['client_id'] ?? '';
    $secret = $p['client_secret'] ?? '';
    $basic = false;
    if (preg_match('/^Basic\s+([A-Za-z0-9+\/=]+)$/i', authorization_header(), $m)) {
        $basic = true;
        if ($secret !== '') oauth_fail('invalid_request', 'use one way of authenticating');
        [$bid, $secret] = array_map('urldecode', explode(':', (string)base64_decode($m[1]), 2) + [1 => '']);
        if ($id !== '' && $id !== $bid) oauth_fail('invalid_request', 'client_id mismatch');
        $id = $bid;
    }
    $c = client_by_id($id);
    $good = $c && $c['status'] === 'active' && ($c['type'] === 'public'
        ? $secret === ''
        : hash_equals((string)$c['secret_hash'], token_hash($secret)));
    if (!$good) {
        rate_hit('client', ip_subject());
        if ($basic) header('WWW-Authenticate: Basic realm="SavAccount"');
        oauth_fail('invalid_client', '', 401);
    }
    return $c;
}

function issue_tokens(array $client, array $user, array $grant, array $t, string $nonce): void
{
    $scopes = explode(' ', $t['scopes']);
    $access = new_token();
    $refresh = in_array('offline_access', $scopes, true) ? new_token() : null;
    $add = fn(string $token, string $kind, int $life) => q('INSERT INTO ' . t('tokens') . ' (token_hash, kind, family, client_id, user_id, scopes, amr, auth_time, created_at, expires_at) VALUES (?,?,?,?,?,?,?,?,?,?)',
        [token_hash($token), $kind, $t['family'], $client['id'], $user['id'], $t['scopes'], $t['amr'], $t['auth_time'], now_utc(), now_utc($life)]);
    $add($access, 'access', ACCESS_SECONDS);
    if ($refresh) $add($refresh, 'refresh', REFRESH_DAYS * 86400);
    q('UPDATE ' . t('grants') . ' SET last_used_at = ? WHERE id = ?', [now_utc(), $grant['id']]);
    $id = ['iss' => cfg('site'), 'aud' => $client['client_id'], 'iat' => time(), 'exp' => time() + ID_SECONDS, 'auth_time' => ts($t['auth_time']), 'amr' => explode(' ', $t['amr']),
        'at_hash' => b64u(substr(hash('sha256', $access, true), 0, 16))] + claims_for($client, $user, $grant, $scopes);
    if ($nonce !== '') $id['nonce'] = $nonce;
    header('Pragma: no-cache');
    send_json(['access_token' => $access, 'token_type' => 'Bearer', 'expires_in' => ACCESS_SECONDS, 'id_token' => jwt($id), 'scope' => implode(' ', $scopes)] + ($refresh ? ['refresh_token' => $refresh] : []));
}

/** Account active and service still allowed, with the scopes the user has not taken back. */
function live_grant(array $t): ?array
{
    $u = user_row((int)$t['user_id']);
    $g = one('SELECT * FROM ' . t('grants') . ' WHERE user_id = ? AND client_id = ?', [$t['user_id'], $t['client_id']]);
    $c = client_row((int)$t['client_id']);
    if (!$u || $u['status'] !== 'active' || !$g || !$c || $c['status'] !== 'active') return null;
    $scopes = scope_sort(array_intersect(explode(' ', $t['scopes']), explode(' ', $g['scopes']), client_scopes($c)));
    return ['user' => $u, 'grant' => $g, 'client' => $c, 'scopes' => $scopes];
}

function revoke_family(string $family): void
{
    q('DELETE FROM ' . t('tokens') . ' WHERE family = ?', [$family]);
}
