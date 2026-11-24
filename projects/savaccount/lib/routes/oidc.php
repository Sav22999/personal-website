<?php
/** SavAccount — the consent page (oauth.*) and the OpenID Connect endpoints (oidc.*). */

declare(strict_types=1);

$request = fn() => authz_request(is_array(body()['request'] ?? null) ? body()['request'] : []);

return [
    /** What the authorization page has to show. Never issues anything. */
    'oauth.check' => function () use ($request) {
        rate_limit('authz', ip_subject(), 120, 10);
        $req = $request();
        $s = session();
        $status = authz_status($req, $s, 30);
        if (in_array('none', $req['prompt'], true) && $status !== 'ready') {
            authz_error($req, $status === 'consent' ? 'consent_required' : ($status === 'mfa_setup' ? 'interaction_required' : 'login_required'));
        }
        $out = ['status' => $status, 'client' => public_client($req['client']), 'loginHint' => $req['login_hint']];
        if ($s && in_array($status, ['consent', 'ready'], true)) {
            $g = grant_row($s, $req['client']);
            $had = $g ? explode(' ', $g['scopes']) : [];
            $u = $s['user'];
            $out += [
                'scopes' => array_map(fn($x) => ['scope' => $x, 'required' => in_array($x, $req['required'], true), 'granted' => in_array($x, $had, true)], $req['scopes']),
                'emails' => array_map(fn($e) => ['id' => (int)$e['id'], 'email' => $e['email']], verified_emails($u['id'])),
                'emailId' => $g ? (int)grant_email($g)['id'] : (int)primary_email($u['id'])['id'],
                'preview' => ['name' => trim($u['first_name'] . ' ' . $u['last_name']), 'nickname' => $u['nickname'], 'birthdate' => $u['birthdate']],
            ];
        }
        ok($out);
    },

    'oauth.approve' => function () use ($request) {
        $s = writer();
        $req = $request();
        $status = authz_status($req, $s, 600);
        if (!in_array($status, ['consent', 'ready'], true)) fail('reauth', 409, ['status' => $status]);
        $uid = (int)$s['user_id'];
        $g = grant_row($s, $req['client']);
        if (isset(body()['scopes'])) {
            // the choice made on the page
            $chosen = array_filter((array)body()['scopes'], 'is_string');
            $now = array_merge($req['required'], array_intersect($req['optional'], $chosen));
            $emailId = (int)(body()['emailId'] ?? 0);
            if (!in_array($emailId, array_map(fn($e) => (int)$e['id'], verified_emails($uid)), true)) fail('bad_request');
            $kept = $g ? array_diff(explode(' ', $g['scopes']), $req['scopes']) : [];
            $all = implode(' ', scope_sort(array_merge($kept, $now)));
            q('INSERT INTO ' . t('grants') . ' (user_id, client_id, scopes, email_id, created_at, updated_at) VALUES (?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE scopes = VALUES(scopes), email_id = VALUES(email_id), updated_at = VALUES(updated_at)', [$uid, $req['client']['id'], $all, $emailId, now_utc(), now_utc()]);
            log_event($uid, $g ? 'app_updated' : 'app_connected', $req['client']['name']);
        } else {
            if ($status !== 'ready') fail('reauth', 409, ['status' => $status]);
            $now = array_intersect($req['scopes'], explode(' ', $g['scopes']));
        }
        $code = new_token();
        q('INSERT INTO ' . t('codes') . ' (code_hash, family, client_id, user_id, redirect_uri, scopes, nonce, pkce, amr, auth_time, expires_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)',
            [token_hash($code), bin2hex(random_bytes(16)), $req['client']['id'], $uid, $req['redirect_uri'], implode(' ', scope_sort($now)), $req['nonce'], $req['pkce'], $s['amr'], $s['auth_time'], now_utc(CODE_SECONDS)]);
        $p = ['code' => $code, 'iss' => cfg('site')];
        if ($req['state'] !== '') $p['state'] = $req['state'];
        ok(['status' => 'redirect', 'url' => with_query($req['redirect_uri'], $p)]);
    },

    'oauth.deny' => fn() => authz_error($request(), 'access_denied'),

    /** Sign-out asked by a service: where to go afterwards, only if the service registered that address. */
    'oauth.logoutCheck' => function () {
        $p = is_array(body()['request'] ?? null) ? body()['request'] : [];
        $claims = is_string($p['id_token_hint'] ?? null) && strlen($p['id_token_hint']) < 4000 ? read_own_jwt($p['id_token_hint']) : null;
        $client = client_by_id($claims['aud'] ?? ($p['client_id'] ?? ''));
        $uri = $p['post_logout_redirect_uri'] ?? '';
        $url = null;
        if ($client && is_string($uri) && in_array($uri, json_decode($client['logout_uris'], true) ?: [], true)) {
            $state = $p['state'] ?? '';
            $url = is_string($state) && $state !== '' && strlen($state) <= 1000 ? with_query($uri, ['state' => $state]) : $uri;
        }
        ok(['client' => $client ? public_client($client) : null, 'url' => $url, 'signedIn' => session() !== null]);
    },

    // ---------- endpoints called by the services

    'oidc.config' => function () {
        $i = cfg('site');
        header('Cache-Control: public, max-age=3600');
        send_json([
            'issuer' => $i,
            'authorization_endpoint' => "$i/authorize",
            'token_endpoint' => "$i/oauth/token",
            'userinfo_endpoint' => "$i/oauth/userinfo",
            'revocation_endpoint' => "$i/oauth/revoke",
            'jwks_uri' => "$i/oauth/jwks",
            'end_session_endpoint' => "$i/logout",
            'scopes_supported' => SCOPES,
            'response_types_supported' => ['code'],
            'response_modes_supported' => ['query'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'subject_types_supported' => ['pairwise', 'public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'token_endpoint_auth_methods_supported' => ['client_secret_basic', 'client_secret_post', 'none'],
            'revocation_endpoint_auth_methods_supported' => ['client_secret_basic', 'client_secret_post', 'none'],
            'code_challenge_methods_supported' => ['S256'],
            'prompt_values_supported' => ['none', 'login', 'consent'],
            'claims_supported' => ['sub', 'iss', 'aud', 'exp', 'iat', 'auth_time', 'nonce', 'amr', 'at_hash', 'name', 'given_name', 'family_name', 'nickname', 'locale', 'email', 'email_verified', 'birthdate'],
            'ui_locales_supported' => ['it', 'en'],
            'authorization_response_iss_parameter_supported' => true,
            'request_parameter_supported' => false,
            'request_uri_parameter_supported' => false,
            'claims_parameter_supported' => false,
            'service_documentation' => "$i/developers",
        ]);
    },

    'oidc.jwks' => function () {
        header('Cache-Control: public, max-age=3600');
        send_json(jwks());
    },

    'oidc.token' => function () {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') oauth_fail('invalid_request', 'POST expected', 405);
        $p = form_params();
        $client = token_client($p);
        $type = $p['grant_type'] ?? '';

        if ($type === 'authorization_code') {
            $code = $p['code'] ?? '';
            $c = looks_like_token($code) ? one('SELECT * FROM ' . t('codes') . ' WHERE code_hash = ?', [token_hash($code)]) : null;
            if (!$c || (int)$c['client_id'] !== (int)$client['id']) oauth_fail('invalid_grant');
            if (q('UPDATE ' . t('codes') . ' SET used_at = ? WHERE id = ? AND used_at IS NULL', [now_utc(), $c['id']])->rowCount() !== 1) {
                revoke_family($c['family']); // a code used twice: whoever got the first tokens loses them
                oauth_fail('invalid_grant');
            }
            $verifier = $p['code_verifier'] ?? '';
            if (ts($c['expires_at']) < time() || ($p['redirect_uri'] ?? '') !== $c['redirect_uri']
                || !preg_match('/^[A-Za-z0-9._~-]{43,128}$/', $verifier) || !hash_equals($c['pkce'], b64u(hash('sha256', $verifier, true)))) oauth_fail('invalid_grant');
            $live = live_grant($c);
            if (!$live) oauth_fail('invalid_grant');
            $c['scopes'] = implode(' ', $live['scopes']);
            issue_tokens($client, $live['user'], $live['grant'], $c, $c['nonce']);
        }

        if ($type === 'refresh_token') {
            $token = $p['refresh_token'] ?? '';
            $r = looks_like_token($token) ? one('SELECT * FROM ' . t('tokens') . " WHERE token_hash = ? AND kind = 'refresh'", [token_hash($token)]) : null;
            if (!$r || (int)$r['client_id'] !== (int)$client['id'] || ts($r['expires_at']) < time()) oauth_fail('invalid_grant');
            if (q('UPDATE ' . t('tokens') . ' SET used_at = ? WHERE id = ? AND used_at IS NULL', [now_utc(), $r['id']])->rowCount() !== 1) {
                revoke_family($r['family']); // reused: stolen, or stolen from
                log_event((int)$r['user_id'], 'app_token_reuse', $client['name']);
                oauth_fail('invalid_grant');
            }
            $live = live_grant($r);
            if (!$live) oauth_fail('invalid_grant');
            $scopes = $live['scopes'];
            if (($p['scope'] ?? '') !== '') {
                $asked = explode(' ', $p['scope']);
                if (array_diff($asked, $scopes)) oauth_fail('invalid_scope');
                $scopes = scope_sort(array_merge($asked, ['openid'], array_intersect(['offline_access'], $scopes)));
            }
            $r['scopes'] = implode(' ', $scopes);
            issue_tokens($client, $live['user'], $live['grant'], $r, '');
        }
        oauth_fail('unsupported_grant_type');
    },

    'oidc.userinfo' => function () {
        $bad = function () {
            header('WWW-Authenticate: Bearer error="invalid_token"');
            oauth_fail('invalid_token', '', 401);
        };
        if (!preg_match('/^Bearer\s+(\S+)$/i', authorization_header(), $m) || !looks_like_token($m[1])) $bad();
        $a = one('SELECT * FROM ' . t('tokens') . " WHERE token_hash = ? AND kind = 'access' AND expires_at > ?", [token_hash($m[1]), now_utc()]);
        $live = $a ? live_grant($a) : null;
        if (!$live) $bad();
        send_json(claims_for($live['client'], $live['user'], $live['grant'], $live['scopes']));
    },

    'oidc.revoke' => function () {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') oauth_fail('invalid_request', 'POST expected', 405);
        $p = form_params();
        $client = token_client($p);
        $token = $p['token'] ?? '';
        $r = looks_like_token($token) ? one('SELECT * FROM ' . t('tokens') . ' WHERE token_hash = ? AND client_id = ?', [token_hash($token), $client['id']]) : null;
        if ($r && $r['kind'] === 'refresh') revoke_family($r['family']);
        elseif ($r) q('DELETE FROM ' . t('tokens') . ' WHERE id = ?', [$r['id']]);
        http_response_code(200);
        header('Cache-Control: no-store');
        exit;
    },
];
