<?php
/** SavAccount — the signed-in user: profile, addresses, security, sessions, connected services, data. */

declare(strict_types=1);

function first_factor_codes(array $u): ?array
{
    // recovery codes come with the first second step that is turned on
    $n = (int)q('SELECT COUNT(*) FROM ' . t('recovery_codes') . ' WHERE user_id = ?', [$u['id']])->fetchColumn();
    return $n ? null : new_recovery_codes($u);
}

function mark_session_mfa(array $s): void
{
    q('UPDATE ' . t('sessions') . " SET amr = 'pwd otp mfa' WHERE id = ?", [$s['id']]);
}

function after_2fa_off(array $u): void
{
    if (!has_2fa(user_row($u['id']))) q('DELETE FROM ' . t('recovery_codes') . ' WHERE user_id = ?', [$u['id']]);
}

function own_email(array $s): array
{
    $e = one('SELECT * FROM ' . t('emails') . ' WHERE id = ? AND user_id = ?', [(int)(body()['id'] ?? 0), $s['user_id']]);
    if (!$e) fail('not_found', 404);
    return $e;
}

return [
    'profile.save' => function () {
        $s = writer();
        $first = name_field('firstName');
        $last = name_field('lastName');
        $nick = s('nickname', 40);
        if ($nick !== '' && !preg_match('/^[\p{L}\p{N}\p{M} ._\-\'’]+$/u', $nick)) fail('nickname_invalid', 422);
        $birth = s('birthdate', 10);
        if ($birth !== '') {
            $d = DateTime::createFromFormat('!Y-m-d', $birth);
            if (!$d || $d->format('Y-m-d') !== $birth || $d->getTimestamp() > time() || (int)$d->format('Y') < 1900) fail('birthdate_invalid', 422);
        }
        q('UPDATE ' . t('users') . ' SET first_name = ?, last_name = ?, nickname = ?, birthdate = ?, locale = ? WHERE id = ?',
            [$first, $last, $nick, $birth ?: null, s('locale', 2) === 'en' ? 'en' : 'it', $s['user_id']]);
        log_event((int)$s['user_id'], 'profile');
        ok(me(['user' => user_row((int)$s['user_id'])] + $s));
    },

    // ---------- addresses

    'emails.list' => function () {
        $s = require_session();
        ok(['emails' => array_map(fn($e) => ['id' => (int)$e['id'], 'email' => $e['email'], 'verified' => $e['verified_at'] !== null, 'primary' => (bool)$e['is_primary'], 'createdAt' => iso($e['created_at'])],
            all('SELECT * FROM ' . t('emails') . ' WHERE user_id = ? ORDER BY is_primary DESC, id', [$s['user_id']])), 'max' => MAX_EMAILS]);
    },

    'emails.add' => function () {
        $s = sudo_writer();
        if (!mail_ready()) fail('mail_unavailable', 503);
        $email = email_field();
        $uid = (int)$s['user_id'];
        $mine = one('SELECT * FROM ' . t('emails') . ' WHERE user_id = ? AND email = ?', [$uid, $email]);
        if ($mine && $mine['verified_at']) fail('email_yours', 409);
        if (!$mine && (int)q('SELECT COUNT(*) FROM ' . t('emails') . ' WHERE user_id = ?', [$uid])->fetchColumn() >= MAX_EMAILS) fail('emails_max', 409);
        rate_limit('mail', $email, 5, 60);
        rate_limit('mail', 'user:' . $uid, 8, 60);
        $owner = user_by_verified_email($email);
        if (!$mine) {
            q('INSERT INTO ' . t('emails') . ' (user_id, email, created_at) VALUES (?,?,?)', [$uid, $email, now_utc()]);
            $mine = ['id' => (int)db()->lastInsertId()];
        }
        // an address of someone else gets a notice instead of the code; the answer is the same
        $c = new_challenge('email', $owner ? null : $uid, $owner ? null : (int)$mine['id']);
        send_challenge($c['row']);
        if ($owner) send_mail('email_taken', $email, $owner['locale'], ['name' => '']);
        ok(['challenge' => $c['token'], 'id' => (int)$mine['id']]);
    },

    'emails.verify' => function () {
        $s = writer();
        rate_limit('code', ip_subject(), 60, 60);
        $c = challenge(body()['challenge'] ?? '', ['email']);
        if ($c['user_id'] && (int)$c['user_id'] !== (int)$s['user_id']) fail('challenge_expired', 410);
        check_code($c, s('code', 6));
        consume($c);
        mark_verified((int)$c['email_id']);
        notify($s['user'], 'email_added');
        ok();
    },

    'emails.primary' => function () {
        $s = sudo_writer();
        $e = own_email($s);
        if (!$e['verified_at']) fail('email_unverified', 409);
        $old = primary_email((int)$s['user_id']);
        transaction(function () use ($s, $e) {
            q('UPDATE ' . t('emails') . ' SET is_primary = (id = ?) WHERE user_id = ?', [$e['id'], $s['user_id']]);
        });
        // the previous address is told too: it is the one a thief would want to silence
        send_mail('security', $old['email'], $s['user']['locale'], ['name' => $s['user']['first_name'], 'what' => security_words('email_primary', $s['user']['locale'])], cfg('site') . '/security');
        log_event((int)$s['user_id'], 'email_primary');
        ok();
    },

    'emails.remove' => function () {
        $s = sudo_writer();
        $e = own_email($s);
        if ($e['is_primary']) fail('email_primary', 409);
        q('DELETE FROM ' . t('emails') . ' WHERE id = ?', [$e['id']]);
        if ($e['verified_at']) {
            notify($s['user'], 'email_removed');
            send_mail('security', $e['email'], $s['user']['locale'], ['name' => $s['user']['first_name'], 'what' => security_words('email_removed', $s['user']['locale'])], cfg('site') . '/security');
        }
        ok();
    },

    // ---------- password and second step

    'password.change' => function () {
        $s = sudo_writer();
        $u = $s['user'];
        $pw = password_field('password', [$u['first_name'], $u['last_name']]);
        q('UPDATE ' . t('users') . ' SET password_hash = ?, password_changed_at = ? WHERE id = ?', [hash_password($pw), now_utc(), $u['id']]);
        end_sessions($u['id'], $s['id']);
        q('DELETE FROM ' . t('challenges') . ' WHERE user_id = ?', [$u['id']]);
        log_event($u['id'], 'password_changed');
        send_mail('password_changed', primary_email($u['id'])['email'], $u['locale'], ['name' => $u['first_name']], cfg('site') . '/forgot');
        ok();
    },

    '2fa.totp.start' => function () {
        $s = sudo_writer();
        $u = $s['user'];
        if ($u['totp_enabled_at']) fail('already_on', 409);
        $secret = random_bytes(20);
        q('UPDATE ' . t('users') . ' SET totp_pending = ? WHERE id = ?', [encrypt($secret, 'totp:' . $u['uid']), $u['id']]);
        $b32 = base32_encode_bin($secret);
        $label = rawurlencode('SavAccount:' . primary_email($u['id'])['email']);
        ok(['secret' => $b32, 'uri' => "otpauth://totp/$label?secret=$b32&issuer=SavAccount&algorithm=SHA1&digits=6&period=30"]);
    },

    '2fa.totp.enable' => function () {
        $s = sudo_writer();
        $u = $s['user'];
        rate_limit('mfa', 'user:' . $u['id'], 15, 15);
        if ($u['totp_enabled_at'] || !$u['totp_pending']) fail('challenge_expired', 410);
        $step = totp_match(decrypt($u['totp_pending'], 'totp:' . $u['uid']), s('code', 6));
        if ($step === null) fail('code_wrong', 422);
        q('UPDATE ' . t('users') . ' SET totp_secret = totp_pending, totp_pending = NULL, totp_enabled_at = ?, totp_last_step = ? WHERE id = ?', [now_utc(), $step, $u['id']]);
        mark_session_mfa($s);
        $codes = first_factor_codes($u);
        notify($u, 'totp_on');
        ok(['recoveryCodes' => $codes]);
    },

    '2fa.totp.disable' => function () {
        $s = sudo_writer();
        q('UPDATE ' . t('users') . ' SET totp_secret = NULL, totp_pending = NULL, totp_enabled_at = NULL WHERE id = ?', [$s['user_id']]);
        after_2fa_off($s['user']);
        notify($s['user'], 'totp_off');
        ok();
    },

    '2fa.email.start' => function () {
        $s = sudo_writer();
        if (!mail_ready()) fail('mail_unavailable', 503);
        if ($s['user']['email_2fa']) fail('already_on', 409);
        rate_limit('mail', 'user:' . $s['user_id'], 8, 60);
        $c = new_challenge('email2fa', (int)$s['user_id'], null);
        send_challenge($c['row']);
        ok(['challenge' => $c['token'], 'sentTo' => mask_email(primary_email((int)$s['user_id'])['email'])]);
    },

    '2fa.email.enable' => function () {
        $s = sudo_writer();
        $c = challenge(body()['challenge'] ?? '', ['email2fa']);
        if ((int)$c['user_id'] !== (int)$s['user_id']) fail('challenge_expired', 410);
        check_code($c, s('code', 6));
        consume($c);
        q('UPDATE ' . t('users') . ' SET email_2fa = 1 WHERE id = ?', [$s['user_id']]);
        mark_session_mfa($s);
        $codes = first_factor_codes($s['user']);
        notify($s['user'], 'email2fa_on');
        ok(['recoveryCodes' => $codes]);
    },

    '2fa.email.disable' => function () {
        $s = sudo_writer();
        q('UPDATE ' . t('users') . ' SET email_2fa = 0 WHERE id = ?', [$s['user_id']]);
        after_2fa_off($s['user']);
        notify($s['user'], 'email2fa_off');
        ok();
    },

    '2fa.recovery' => function () {
        $s = sudo_writer();
        if (!has_2fa($s['user'])) fail('bad_request');
        $codes = new_recovery_codes($s['user']);
        notify($s['user'], 'recovery_new');
        ok(['recoveryCodes' => $codes]);
    },

    // ---------- sessions and security log

    'sessions.list' => function () {
        $s = require_session();
        ok(['sessions' => array_map(fn($r) => ['id' => (int)$r['id'], 'device' => $r['device'], 'ip' => $r['ip'], 'createdAt' => iso($r['created_at']), 'lastSeenAt' => iso($r['last_seen_at']), 'current' => (int)$r['id'] === $s['id']],
            all('SELECT * FROM ' . t('sessions') . ' WHERE user_id = ? AND expires_at > ? ORDER BY last_seen_at DESC', [$s['user_id'], now_utc()]))]);
    },

    'sessions.revoke' => function () {
        $s = writer();
        $id = (int)(body()['id'] ?? 0);
        if ($id === $s['id']) fail('bad_request');
        q('DELETE FROM ' . t('sessions') . ' WHERE id = ? AND user_id = ?', [$id, $s['user_id']]);
        log_event((int)$s['user_id'], 'session_revoked');
        ok();
    },

    'sessions.revokeOthers' => function () {
        $s = writer();
        end_sessions((int)$s['user_id'], $s['id']);
        log_event((int)$s['user_id'], 'session_revoked');
        ok();
    },

    'events.list' => function () {
        $s = require_session();
        ok(['events' => array_map(fn($r) => ['kind' => $r['kind'], 'detail' => $r['detail'], 'device' => $r['device'], 'ip' => $r['ip'], 'at' => iso($r['created_at'])],
            all('SELECT * FROM ' . t('events') . ' WHERE user_id = ? AND created_at > ? ORDER BY id DESC LIMIT 60', [$s['user_id'], now_utc(-EVENT_DAYS * 86400)]))]);
    },

    // ---------- connected services

    'apps.list' => function () {
        $s = require_session();
        ok(['apps' => array_map(function ($g) use ($s) {
            return [
                'id' => (int)$g['id'],
                'client' => public_client(client_row((int)$g['client_id'])),
                'scopes' => explode(' ', $g['scopes']),
                'required' => explode(' ', $g['scopes_required']),
                'optional' => array_values(array_filter(explode(' ', $g['scopes_optional']))),
                'emailId' => (int)grant_email($g)['id'],
                'createdAt' => iso($g['created_at']),
                'lastUsedAt' => iso($g['last_used_at']),
            ];
        }, all('SELECT g.*, c.scopes_required, c.scopes_optional FROM ' . t('grants') . ' g JOIN ' . t('clients') . ' c ON c.id = g.client_id WHERE g.user_id = ? ORDER BY g.last_used_at DESC, g.id DESC', [$s['user_id']])),
            'emails' => array_map(fn($e) => ['id' => (int)$e['id'], 'email' => $e['email']], verified_emails((int)$s['user_id']))]);
    },

    /** Changes what a service receives: optional data on or off, and which address. */
    'apps.update' => function () {
        $s = writer();
        $g = one('SELECT g.*, c.scopes_optional FROM ' . t('grants') . ' g JOIN ' . t('clients') . ' c ON c.id = g.client_id WHERE g.id = ? AND g.user_id = ?', [(int)(body()['id'] ?? 0), $s['user_id']]);
        if (!$g) fail('not_found', 404);
        $optional = array_filter(explode(' ', $g['scopes_optional']));
        $wanted = array_filter((array)(body()['scopes'] ?? []), 'is_string');
        $kept = array_diff(explode(' ', $g['scopes']), $optional);
        $scopes = scope_sort(array_merge($kept, array_intersect($optional, $wanted)));
        $emailId = (int)(body()['emailId'] ?? 0);
        if (!in_array($emailId, array_map(fn($e) => (int)$e['id'], verified_emails((int)$s['user_id'])), true)) fail('bad_request');
        q('UPDATE ' . t('grants') . ' SET scopes = ?, email_id = ?, updated_at = ? WHERE id = ?', [implode(' ', $scopes), $emailId, now_utc(), $g['id']]);
        log_event((int)$s['user_id'], 'app_updated', client_row((int)$g['client_id'])['name']);
        ok();
    },

    'apps.revoke' => function () {
        $s = writer();
        $g = one('SELECT * FROM ' . t('grants') . ' WHERE id = ? AND user_id = ?', [(int)(body()['id'] ?? 0), $s['user_id']]);
        if (!$g) fail('not_found', 404);
        transaction(function () use ($g) {
            foreach (['tokens', 'codes'] as $table) q('DELETE FROM ' . t($table) . ' WHERE user_id = ? AND client_id = ?', [$g['user_id'], $g['client_id']]);
            q('DELETE FROM ' . t('grants') . ' WHERE id = ?', [$g['id']]);
        });
        log_event((int)$s['user_id'], 'app_revoked', client_row((int)$g['client_id'])['name']);
        ok();
    },

    // ---------- data

    'account.export' => function () {
        $s = sudo_writer();
        $u = $s['user'];
        $pick = fn(array $rows, array $cols) => array_map(fn($r) => array_intersect_key($r, array_flip($cols)), $rows);
        log_event($u['id'], 'export');
        ok(['data' => [
            'exportedAt' => iso(now_utc()),
            'profile' => ['id' => $u['uid'], 'firstName' => $u['first_name'], 'lastName' => $u['last_name'], 'nickname' => $u['nickname'], 'birthdate' => $u['birthdate'], 'locale' => $u['locale'],
                'createdAt' => iso($u['created_at']), 'termsAcceptedAt' => iso($u['terms_at']), 'authenticatorApp' => !empty($u['totp_enabled_at']), 'emailTwoStep' => !empty($u['email_2fa'])],
            'emails' => $pick(all('SELECT * FROM ' . t('emails') . ' WHERE user_id = ?', [$u['id']]), ['email', 'verified_at', 'is_primary', 'created_at']),
            'services' => all('SELECT c.name, g.scopes, g.created_at, g.last_used_at FROM ' . t('grants') . ' g JOIN ' . t('clients') . ' c ON c.id = g.client_id WHERE g.user_id = ?', [$u['id']]),
            'sessions' => $pick(all('SELECT * FROM ' . t('sessions') . ' WHERE user_id = ?', [$u['id']]), ['device', 'ip', 'created_at', 'last_seen_at', 'expires_at']),
            'securityLog' => $pick(all('SELECT * FROM ' . t('events') . ' WHERE user_id = ? ORDER BY id', [$u['id']]), ['kind', 'detail', 'device', 'ip', 'created_at']),
        ]]);
    },

    'account.delete' => function () {
        $s = sudo_writer();
        $u = $s['user'];
        rate_limit('sudo', 'user:' . $u['id'], 8, 15);
        if (!check_password($u['password_hash'], raw('password'))) fail('password_wrong', 422);
        $to = primary_email($u['id'])['email'];
        q('DELETE FROM ' . t('users') . ' WHERE id = ?', [$u['id']]);
        send_mail('deleted', $to, $u['locale'], ['name' => $u['first_name']]);
        header('X-Sav-Set-Token: ;-1');
        ok();
    },
];
