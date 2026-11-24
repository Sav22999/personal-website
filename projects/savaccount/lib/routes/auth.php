<?php
/** SavAccount — creating an account, signing in, new password. */

declare(strict_types=1);

return [
    'auth.me' => fn() => ok(me()),

    'auth.register' => function () {
        rate_limit('register', ip_subject(), 10, 60);
        if (!mail_ready()) fail('mail_unavailable', 503);
        $first = name_field('firstName');
        $last = name_field('lastName');
        $email = email_field();
        $pw = password_field('password', [$first, $last, explode('@', $email)[0]]);
        if ((body()['terms'] ?? null) !== true) fail('terms_required', 422);
        rate_limit('mail', $email, 5, 60);
        $hash = hash_password($pw); // before knowing whether the address is free: same time either way
        $owner = user_by_verified_email($email);
        if ($owner) {
            $c = new_challenge('register', null, null);
            send_challenge($c['row']);
            send_mail('already', $email, $owner['locale'], ['name' => $owner['first_name']], cfg('site') . '/login');
            ok(['challenge' => $c['token']]);
        }
        $c = transaction(function () use ($first, $last, $email, $hash) {
            // at most three unconfirmed accounts for one address: the oldest make room
            foreach (array_slice(all('SELECT u.id FROM ' . t('users') . ' u JOIN ' . t('emails') . " e ON e.user_id = u.id WHERE e.email = ? AND u.status = 'pending' ORDER BY u.id DESC", [$email]), 2) as $old) {
                q('DELETE FROM ' . t('users') . ' WHERE id = ?', [$old['id']]);
            }
            q('INSERT INTO ' . t('users') . ' (uid, first_name, last_name, locale, password_hash, password_changed_at, terms_at, created_at) VALUES (?,?,?,?,?,?,?,?)',
                [bin2hex(random_bytes(16)), $first, $last, locale(), $hash, now_utc(), now_utc(), now_utc()]);
            $uid = (int)db()->lastInsertId();
            q('INSERT INTO ' . t('emails') . ' (user_id, email, created_at) VALUES (?,?,?)', [$uid, $email, now_utc()]);
            return new_challenge('register', $uid, (int)db()->lastInsertId());
        });
        send_challenge($c['row']);
        ok(['challenge' => $c['token']]);
    },

    /** The code of the registration email, typed in the browser that registered. */
    'auth.verify' => function () {
        rate_limit('code', ip_subject(), 60, 60);
        $c = challenge(body()['challenge'] ?? '', ['register']);
        check_code($c, s('code', 6));
        consume($c);
        $u = user_row((int)$c['user_id']);
        activate($u, (int)$c['email_id']);
        finish_login(user_row($u['id']), ['pwd'], !empty(body()['remember']));
    },

    'auth.resend' => function () {
        rate_limit('resend', ip_subject(), 20, 60);
        $c = challenge(body()['challenge'] ?? '', ['register', 'login', 'email', 'reset', 'email2fa']);
        if ($c['sent_at'] && ts($c['sent_at']) > time() - 60) fail('too_soon', 429, ['retry' => 1]);
        if ($c['kind'] === 'login' && $c['user_id'] && empty(user_row((int)$c['user_id'])['email_2fa'])) fail('bad_request');
        rate_limit('mail', $c['user_id'] ? 'user:' . $c['user_id'] : 'challenge:' . $c['id'], 8, 60);
        send_challenge($c);
        ok();
    },

    /** What a link of an email is for (shown before anything happens: mail scanners only open the page). */
    'auth.link.info' => function () {
        rate_limit('code', ip_subject(), 60, 60);
        $c = challenge_by_link(body()['token'] ?? '');
        $e = one('SELECT email FROM ' . t('emails') . ' WHERE id = ?', [$c['email_id'] ?? 0]);
        ok(['kind' => $c['kind'], 'email' => $e ? $e['email'] : null]);
    },

    /**
     * Confirms an address with the link. A new account also asks for its password: so a link opened by mistake cannot
     * confirm an account that someone else created with this address.
     */
    'auth.link' => function () {
        rate_limit('code', ip_subject(), 60, 60);
        $c = challenge_by_link(body()['token'] ?? '');
        $u = user_row((int)$c['user_id']);
        if ($c['kind'] === 'register') {
            count_attempt($c);
            if (!check_password($u['password_hash'], raw('password'))) fail('password_wrong', 422);
            consume($c);
            activate($u, (int)$c['email_id']);
            finish_login(user_row($u['id']), ['pwd'], false);
        }
        if ($c['kind'] === 'email') {
            $s = writer();
            if ((int)$s['user_id'] !== $u['id']) fail('wrong_account', 403);
            consume($c);
            mark_verified((int)$c['email_id']);
            notify($u, 'email_added');
            ok(['kind' => 'email']);
        }
        fail('link_expired', 410);
    },

    'auth.login' => function () {
        $email = email_field();
        $pw = raw('password');
        $remember = !empty(body()['remember']);
        if (rate_count('login', ip_subject(), 15) >= 20 || rate_count('login', $email, 15) >= 10) fail('too_many', 429, ['retry' => 15]);
        $u = user_by_verified_email($email);
        $pending = $u ? null : one('SELECT u.* FROM ' . t('users') . ' u JOIN ' . t('emails') . " e ON e.user_id = u.id WHERE e.email = ? AND u.status = 'pending' ORDER BY u.id DESC LIMIT 1", [$email]);
        // counted first, so parallel guesses cannot go past the limit; given back when the password is right
        rate_hit('login', ip_subject());
        rate_hit('login', $email);
        if (!check_password($u['password_hash'] ?? $pending['password_hash'] ?? null, $pw)) {
            if ($u && rate_count('login', $email, 15) >= 5) log_event($u['id'], 'login_failed');
            fail('login_failed', 401);
        }
        rate_clear('login', $email);
        rate_undo('login', ip_subject());
        if ($pending) {
            if (!mail_ready()) fail('mail_unavailable', 503);
            rate_limit('mail', $email, 5, 60);
            $e = primary_email((int)$pending['id']);
            $c = new_challenge('register', (int)$pending['id'], (int)$e['id']);
            send_challenge($c['row']);
            ok(['verify' => ['challenge' => $c['token']]]);
        }
        if ($u['status'] !== 'active') fail('disabled', 403);
        [$algo, $opt] = password_algo();
        if (password_needs_rehash($u['password_hash'], $algo, $opt)) q('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?', [hash_password($pw), $u['id']]);
        if (has_2fa($u)) ok(['mfa' => start_mfa($u, ['remember' => $remember])]);
        finish_login($u, ['pwd'], $remember);
    },

    /** Second step, of a sign-in or of a password confirmation (sudo). */
    'auth.mfa' => function () {
        rate_limit('code', ip_subject(), 60, 60);
        $c = challenge(body()['challenge'] ?? '', ['login']);
        if (!$c['user_id']) fail('challenge_expired', 410);
        $u = user_row((int)$c['user_id']);
        if (!$u || $u['status'] !== 'active') fail('challenge_expired', 410);
        rate_limit('mfa', 'user:' . $u['id'], 15, 15);
        $method = s('method', 10);
        $code = s('code', 20);
        if ($method === 'email' && !empty($u['email_2fa'])) {
            check_code($c, $code);
            $amr = ['pwd', 'otp', 'mfa'];
        } elseif ($method === 'totp') {
            count_attempt($c);
            if (!totp_check($u, $code)) fail('code_wrong', 422);
            $amr = ['pwd', 'otp', 'mfa'];
        } elseif ($method === 'recovery') {
            count_attempt($c);
            if (!use_recovery_code($u, $code)) fail('code_wrong', 422);
            $amr = ['pwd', 'kba', 'mfa'];
            notify($u, 'recovery_used');
        } else {
            fail('bad_request');
        }
        consume($c);
        rate_clear('mfa', 'user:' . $u['id']);
        if (isset($c['data']['sudo'])) {
            $s = writer();
            if ((int)$c['data']['sudo'] !== $s['id']) fail('forbidden', 403);
            sudo_on($s, true);
        }
        finish_login($u, $amr, !empty($c['data']['remember']));
    },

    /** Confirms the password (and the second step) before a sensitive action. */
    'auth.sudo' => function () {
        $s = writer();
        $u = $s['user'];
        rate_limit('sudo', 'user:' . $u['id'], 8, 15);
        if (!check_password($u['password_hash'], raw('password'))) fail('password_wrong', 422);
        rate_clear('sudo', 'user:' . $u['id']);
        if (has_2fa($u)) ok(['mfa' => start_mfa($u, ['sudo' => $s['id']])]);
        sudo_on($s, false);
    },

    'auth.logout' => function () {
        $s = writer();
        q('DELETE FROM ' . t('sessions') . ' WHERE id = ?', [$s['id']]);
        header('X-Sav-Set-Token: ;-1');
        ok();
    },

    'auth.forgot' => function () {
        rate_limit('forgot', ip_subject(), 10, 60);
        if (!mail_ready()) fail('mail_unavailable', 503);
        $email = email_field();
        rate_limit('mail', $email, 5, 60);
        $u = user_by_verified_email($email);
        if ($u && $u['status'] !== 'active') $u = null;
        $e = $u ? one('SELECT id FROM ' . t('emails') . ' WHERE verified_key = ?', [$email]) : null;
        $c = new_challenge('reset', $u ? $u['id'] : null, $e ? (int)$e['id'] : null);
        send_challenge($c['row']);
        ok(['challenge' => $c['token']]);
    },

    /** New password, with the code or the link. It does not sign in: the second step still applies. */
    'auth.reset' => function () {
        rate_limit('code', ip_subject(), 60, 60);
        $personal = [];
        if (body()['token'] ?? null) {
            $c = challenge_by_link(body()['token']);
            if ($c['kind'] !== 'reset') fail('link_expired', 410);
        } else {
            password_field('password'); // a password that would be refused does not use up the code
            $c = challenge(body()['challenge'] ?? '', ['reset']);
            check_code($c, s('code', 6));
        }
        $u = user_row((int)$c['user_id']);
        $pw = password_field('password', [$u['first_name'], $u['last_name']]);
        consume($c);
        transaction(function () use ($u, $pw) {
            q('UPDATE ' . t('users') . ' SET password_hash = ?, password_changed_at = ? WHERE id = ?', [hash_password($pw), now_utc(), $u['id']]);
            end_sessions($u['id']);
            q('DELETE FROM ' . t('tokens') . ' WHERE user_id = ?', [$u['id']]);
            q('DELETE FROM ' . t('codes') . ' WHERE user_id = ?', [$u['id']]);
            q('DELETE FROM ' . t('challenges') . ' WHERE user_id = ?', [$u['id']]);
            log_event($u['id'], 'password_reset');
        });
        foreach (verified_emails($u['id']) as $e) rate_clear('login', $e['email']);
        send_mail('password_changed', primary_email($u['id'])['email'], $u['locale'], ['name' => $u['first_name']], cfg('site') . '/forgot');
        ok();
    },
];
