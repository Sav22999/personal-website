<?php
/** SavAccount — the administrators' panel: which services can use SavAccount, and what they may ask for. */

declare(strict_types=1);

$reader = fn() => require_master(require_session());
$writer = fn() => require_master(sudo_writer());

$find = function (): array {
    $c = client_row((int)(body()['id'] ?? 0));
    if (!$c) fail('not_found', 404);
    return $c;
};

$link = function (string $key): string {
    $v = s($key, 500);
    if ($v === '') return '';
    if (!preg_match('~^https://[^\s<>"\']+$~', $v) || !filter_var($v, FILTER_VALIDATE_URL)) fail('url_invalid', 422, ['field' => $key]);
    return $v;
};

/** Addresses to come back to: https, loopback while developing, or an app scheme for native apps. No wildcards. */
$uris = function (string $key, bool $public, bool $needed): array {
    $out = [];
    foreach ((array)(body()[$key] ?? []) as $u) {
        if (!is_string($u) || trim($u) === '') continue;
        $u = trim($u);
        $https = preg_match('~^https://[a-z0-9.-]+(:\d+)?(/[^\s#*<>"]*)?$~i', $u) && filter_var($u, FILTER_VALIDATE_URL);
        $local = preg_match('~^http://(localhost|127\.0\.0\.1|\[::1\])(:\d{1,5})?(/[^\s#*<>"]*)?$~', $u);
        $app = $public && preg_match('~^[a-z][a-z0-9-]*(\.[a-z0-9-]+)+:/[^\s#*<>"]*$~', $u); // com.example.app:/callback
        if (strlen($u) > 500 || !($https || $local || $app)) fail('url_invalid', 422, ['field' => $key, 'value' => mb_substr($u, 0, 100)]);
        $out[] = $u;
    }
    $out = array_values(array_unique($out));
    if (count($out) > 10 || ($needed && !$out)) fail('uris_count', 422, ['field' => $key]);
    return $out;
};

$show = function (array $c): array {
    return public_client($c) + [
        'id' => (int)$c['id'], 'clientId' => $c['client_id'], 'type' => $c['type'],
        'redirectUris' => json_decode($c['redirect_uris'], true), 'logoutUris' => json_decode($c['logout_uris'], true),
        'required' => explode(' ', $c['scopes_required']), 'optional' => array_values(array_filter(explode(' ', $c['scopes_optional']))),
        'pairwise' => (bool)$c['pairwise'], 'require2fa' => (bool)$c['require_2fa'],
        'users' => (int)q('SELECT COUNT(*) FROM ' . t('grants') . ' WHERE client_id = ?', [$c['id']])->fetchColumn(),
        'lastUsedAt' => iso(q('SELECT MAX(last_used_at) FROM ' . t('grants') . ' WHERE client_id = ?', [$c['id']])->fetchColumn() ?: null),
        'createdAt' => iso($c['created_at']),
    ];
};

return [
    'admin.clients' => function () use ($reader, $show) {
        $reader();
        $count = fn(string $sql) => (int)q($sql)->fetchColumn();
        ok(['clients' => array_map($show, all('SELECT * FROM ' . t('clients') . ' ORDER BY name')), 'stats' => [
            'users' => $count('SELECT COUNT(*) FROM ' . t('users') . " WHERE status = 'active'"),
            'twoStep' => $count('SELECT COUNT(*) FROM ' . t('users') . " WHERE status = 'active' AND (totp_enabled_at IS NOT NULL OR email_2fa = 1)"),
            'sessions' => $count('SELECT COUNT(*) FROM ' . t('sessions') . ' WHERE expires_at > UTC_TIMESTAMP()'),
        ], 'issuer' => cfg('site'), 'scopes' => SCOPES]);
    },

    'admin.client.save' => function () use ($writer, $find, $link, $uris, $show) {
        $s = $writer();
        $old = !empty(body()['id']) ? $find() : null;
        $name = s('name', 80);
        if (mb_strlen($name) < 2) fail('name_invalid', 422, ['field' => 'name']);
        $type = $old ? $old['type'] : (s('type', 20) === 'public' ? 'public' : 'confidential');
        $required = scope_sort(array_merge(['openid'], array_filter((array)(body()['required'] ?? []), 'is_string')));
        $optional = array_values(array_diff(scope_sort(array_filter((array)(body()['optional'] ?? []), 'is_string')), $required));
        $row = [
            'name' => $name, 'description' => s('description', 300), 'homepage_url' => $link('homepage'), 'logo_url' => $link('logo'),
            'privacy_url' => $link('privacy'), 'terms_url' => $link('terms'),
            'redirect_uris' => json_encode($uris('redirectUris', $type === 'public', true), JSON_UNESCAPED_SLASHES),
            'logout_uris' => json_encode($uris('logoutUris', $type === 'public', false), JSON_UNESCAPED_SLASHES),
            'scopes_required' => implode(' ', $required), 'scopes_optional' => implode(' ', $optional),
            'require_2fa' => empty(body()['require2fa']) ? 0 : 1, 'updated_at' => now_utc(),
        ];
        $secret = null;
        if ($old) {
            // the kind of identifier never changes: the service would no longer recognise its users
            q('UPDATE ' . t('clients') . ' SET ' . implode(', ', array_map(fn($k) => "$k = ?", array_keys($row))) . ' WHERE id = ?', array_merge(array_values($row), [$old['id']]));
            $id = (int)$old['id'];
        } else {
            $secret = $type === 'confidential' ? new_token() : null;
            $row += ['client_id' => bin2hex(random_bytes(12)), 'secret_hash' => $secret ? token_hash($secret) : null, 'type' => $type, 'pairwise' => (body()['pairwise'] ?? true) === false ? 0 : 1, 'created_at' => now_utc()];
            q('INSERT INTO ' . t('clients') . ' (' . implode(', ', array_keys($row)) . ') VALUES (' . implode(',', array_fill(0, count($row), '?')) . ')', array_values($row));
            $id = (int)db()->lastInsertId();
        }
        log_event((int)$s['user_id'], $old ? 'admin_client_saved' : 'admin_client_created', $name);
        ok(['client' => $show(client_row($id)), 'secret' => $secret]);
    },

    'admin.client.secret' => function () use ($writer, $find) {
        $s = $writer();
        $c = $find();
        if ($c['type'] !== 'confidential') fail('bad_request');
        $secret = new_token();
        q('UPDATE ' . t('clients') . ' SET secret_hash = ?, updated_at = ? WHERE id = ?', [token_hash($secret), now_utc(), $c['id']]);
        log_event((int)$s['user_id'], 'admin_client_secret', $c['name']);
        ok(['secret' => $secret]);
    },

    'admin.client.status' => function () use ($writer, $find, $show) {
        $s = $writer();
        $c = $find();
        $on = !empty(body()['active']);
        q('UPDATE ' . t('clients') . ' SET status = ?, updated_at = ? WHERE id = ?', [$on ? 'active' : 'disabled', now_utc(), $c['id']]);
        if (!$on) foreach (['tokens', 'codes'] as $table) q('DELETE FROM ' . t($table) . ' WHERE client_id = ?', [$c['id']]);
        log_event((int)$s['user_id'], $on ? 'admin_client_on' : 'admin_client_off', $c['name']);
        ok(['client' => $show(client_row((int)$c['id']))]);
    },

    'admin.client.delete' => function () use ($writer, $find) {
        $s = $writer();
        $c = $find();
        if (s('confirm', 80) !== $c['name']) fail('confirm_wrong', 422);
        q('DELETE FROM ' . t('clients') . ' WHERE id = ?', [$c['id']]);
        log_event((int)$s['user_id'], 'admin_client_deleted', $c['name']);
        ok();
    },

    'admin.users' => function () use ($reader) {
        $reader();
        $search = '%' . addcslashes(s('search', 60), '%_\\') . '%';
        ok(['users' => array_map(fn($u) => ['id' => (int)$u['id'], 'name' => $u['first_name'] . ' ' . $u['last_name'], 'email' => $u['email'], 'status' => $u['status'],
            'twoStep' => !empty($u['totp_enabled_at']) || !empty($u['email_2fa']), 'createdAt' => iso($u['created_at']), 'lastLoginAt' => iso($u['last_login_at'])],
            all('SELECT u.*, (SELECT e.email FROM ' . t('emails') . ' e WHERE e.user_id = u.id ORDER BY e.is_primary DESC, e.id LIMIT 1) AS email FROM ' . t('users') . ' u
                WHERE CONCAT(u.first_name, \' \', u.last_name) LIKE ? OR EXISTS (SELECT 1 FROM ' . t('emails') . ' e WHERE e.user_id = u.id AND e.email LIKE ?) ORDER BY u.id DESC LIMIT 50', [$search, $search]))]);
    },

    'admin.user.status' => function () use ($writer) {
        $s = $writer();
        $u = user_row((int)(body()['id'] ?? 0));
        if (!$u || $u['status'] === 'pending') fail('not_found', 404);
        if ($u['id'] === (int)$s['user_id'] || is_master($u)) fail('forbidden', 403);
        $on = !empty(body()['active']);
        q('UPDATE ' . t('users') . ' SET status = ? WHERE id = ?', [$on ? 'active' : 'disabled', $u['id']]);
        if (!$on) {
            end_sessions($u['id']);
            q('DELETE FROM ' . t('tokens') . ' WHERE user_id = ?', [$u['id']]);
        }
        log_event((int)$s['user_id'], $on ? 'admin_user_on' : 'admin_user_off', '#' . $u['id']);
        ok();
    },
];
