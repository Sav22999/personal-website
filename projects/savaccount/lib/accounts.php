<?php
/** SavAccount — rules shared by the account routes: names, passwords, addresses, challenges, sign-in. */

declare(strict_types=1);

// ---------- Validation ----------

/** Letters, spaces, apostrophes and hyphens only: names end up in emails, so no links or markup can hide in them. */
function name_field(string $key): string
{
    $v = preg_replace('/\s+/u', ' ', s($key, 60));
    if ($v === '' || !preg_match("/^\p{L}[\p{L}\p{M}'’ \-]*(?:\. ?[\p{L}\p{M}'’ \-]*)?$/u", $v) || preg_match('/\.\S/u', $v)) fail('name_invalid', 422, ['field' => $key]);
    return $v;
}

function password_field(string $key, array $personal = []): string
{
    $pw = raw($key);
    if (mb_strlen($pw) < PASSWORD_MIN) fail('password_short', 422, ['min' => PASSWORD_MIN]);
    if (mb_strlen($pw) > PASSWORD_MAX) fail('password_long', 422, ['max' => PASSWORD_MAX]);
    if (count(array_unique(mb_str_split($pw))) < 5) fail('password_weak', 422);
    $low = mb_strtolower($pw);
    foreach (['password', 'savaccount', '1234567', 'qwertyui', 'abcdefgh'] as $bad) {
        if (strpos($low, $bad) !== false && mb_strlen($pw) < mb_strlen($bad) + 6) fail('password_weak', 422);
    }
    foreach ($personal as $p) {
        $p = mb_strtolower((string)$p);
        if (mb_strlen($p) >= 4 && strpos($low, $p) !== false && mb_strlen($pw) < mb_strlen($p) + 8) fail('password_personal', 422);
    }
    if (cfg('pwned') && password_is_pwned($pw)) fail('password_pwned', 422);
    return $pw;
}

/** Have I Been Pwned: only the first 5 characters of the SHA-1 leave the server. No answer = not blocked. */
function password_is_pwned(string $pw): bool
{
    $sha = strtoupper(sha1($pw));
    if (!function_exists('curl_init')) return false;
    $ch = curl_init('https://api.pwnedpasswords.com/range/' . substr($sha, 0, 5));
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 3, CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_HTTPHEADER => ['Add-Padding: true'], CURLOPT_USERAGENT => 'password-check']);
    $list = curl_exec($ch);
    if (!is_string($list) || curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) return false;
    return preg_match('/^' . substr($sha, 5) . ':([1-9]\d*)\s*$/m', $list) === 1;
}

function mask_email(string $email): string
{
    [$local, $domain] = explode('@', $email, 2);
    return mb_substr($local, 0, 1) . '•••' . (mb_strlen($local) > 3 ? mb_substr($local, -1) : '') . '@' . $domain;
}

// ---------- Addresses ----------

function user_by_verified_email(string $email): ?array
{
    $u = one('SELECT u.* FROM ' . t('emails') . ' e JOIN ' . t('users') . ' u ON u.id = e.user_id WHERE e.verified_key = ?', [$email]);
    if ($u) $u['id'] = (int)$u['id'];
    return $u;
}

function primary_email(int $userId): array
{
    $e = one('SELECT * FROM ' . t('emails') . ' WHERE user_id = ? ORDER BY is_primary DESC, id LIMIT 1', [$userId]);
    if (!$e) throw new RuntimeException('account without email');
    return $e;
}

function verified_emails(int $userId): array
{
    return all('SELECT * FROM ' . t('emails') . ' WHERE user_id = ? AND verified_at IS NOT NULL ORDER BY is_primary DESC, id', [$userId]);
}

/** The address belongs to this account from now on; unconfirmed claims of anyone else on it are dropped. */
function mark_verified(int $emailId): void
{
    transaction(function () use ($emailId) {
        $e = one('SELECT * FROM ' . t('emails') . ' WHERE id = ? FOR UPDATE', [$emailId]);
        if (!$e) fail('challenge_expired', 410);
        try {
            q('UPDATE ' . t('emails') . ' SET verified_at = COALESCE(verified_at, ?), verified_key = email WHERE id = ?', [now_utc(), $emailId]);
        } catch (PDOException $x) {
            if ($x->getCode() === '23000') { db()->rollBack(); fail('email_taken', 409); }
            throw $x;
        }
        q('DELETE u FROM ' . t('users') . ' u JOIN ' . t('emails') . " e ON e.user_id = u.id WHERE e.email = ? AND u.status = 'pending' AND u.id <> ?", [$e['email'], $e['user_id']]);
        q('DELETE FROM ' . t('emails') . ' WHERE email = ? AND verified_at IS NULL', [$e['email']]);
    });
}

function notify(array $user, string $what): void
{
    $e = primary_email($user['id']);
    log_event($user['id'], $what);
    send_mail('security', $e['email'], $user['locale'], ['name' => $user['first_name'], 'what' => security_words($what, $user['locale'])], cfg('site') . '/security');
}

// ---------- Challenges: a step to confirm with a code (or a link) ----------

function code_hash(string $tokenHash, string $code): string
{
    return keyed_hash('code', $tokenHash . ':' . $code);
}

/** A challenge without a user looks the same from outside and can never be completed. */
function new_challenge(string $kind, ?int $userId, ?int $emailId, array $data = []): array
{
    $token = new_token();
    q('INSERT INTO ' . t('challenges') . ' (token_hash, kind, user_id, email_id, data, created_at, expires_at) VALUES (?,?,?,?,?,?,?)',
        [token_hash($token), $kind, $userId, $emailId, json_encode($data), now_utc(), now_utc(CODE_MINUTES * 60)]);
    return ['token' => $token, 'row' => challenge($token, [$kind])];
}

function challenge($token, array $kinds): array
{
    $c = looks_like_token($token) ? one('SELECT * FROM ' . t('challenges') . ' WHERE token_hash = ? AND expires_at > ?', [token_hash($token), now_utc()]) : null;
    if (!$c || !in_array($c['kind'], $kinds, true)) fail('challenge_expired', 410);
    $c['data'] = json_decode((string)$c['data'], true) ?: [];
    return $c;
}

/** New code (and link); resending gives a fresh set of attempts but is limited per address. */
function refresh_challenge(array $c, bool $link): array
{
    $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $linkToken = $link ? new_token() : null;
    q('UPDATE ' . t('challenges') . ' SET code_hash = ?, link_hash = ?, attempts = 0, sent_at = ?, expires_at = ? WHERE id = ?',
        [code_hash($c['token_hash'], $code), $linkToken ? token_hash($linkToken) : null, now_utc(), now_utc(CODE_MINUTES * 60), $c['id']]);
    return [$code, $linkToken];
}

/** Writes and sends the email of a challenge. */
function send_challenge(array $c): void
{
    $withLink = in_array($c['kind'], ['register', 'email', 'reset'], true);
    [$code, $link] = refresh_challenge($c, $withLink);
    if (!$c['user_id']) return; // nothing to send, same answer
    $u = user_row((int)$c['user_id']);
    $to = $c['email_id'] ? (string)q('SELECT email FROM ' . t('emails') . ' WHERE id = ?', [$c['email_id']])->fetchColumn() : primary_email((int)$c['user_id'])['email'];
    $kind = ['register' => 'register', 'email' => 'email', 'reset' => 'reset', 'login' => 'mfa', 'email2fa' => 'mfa'][$c['kind']];
    $url = !$withLink ? '' : cfg('site') . ($c['kind'] === 'reset' ? '/reset' : '/link') . '#t=' . $link;
    send_mail($kind, $to, $u['locale'], ['name' => $u['first_name'], 'code' => $code], $url);
}

/** Counts the attempt before looking at the code, so parallel guesses cannot go past the limit. */
function count_attempt(array $c): void
{
    $n = q('UPDATE ' . t('challenges') . ' SET attempts = attempts + 1 WHERE id = ? AND attempts < ?', [$c['id'], CODE_ATTEMPTS])->rowCount();
    if ($n !== 1) fail('challenge_expired', 410);
}

function check_code(array $c, string $code): void
{
    count_attempt($c);
    if (!$c['user_id'] || !$c['code_hash'] || !preg_match('/^\d{6}$/', $code) || !hash_equals($c['code_hash'], code_hash($c['token_hash'], $code))) fail('code_wrong', 422);
}

/** Single use: of two requests at the same time only one goes on. */
function consume(array $c): void
{
    if (q('DELETE FROM ' . t('challenges') . ' WHERE id = ?', [$c['id']])->rowCount() !== 1) fail('challenge_expired', 410);
}

function challenge_by_link($token): array
{
    $c = looks_like_token($token) ? one('SELECT * FROM ' . t('challenges') . ' WHERE link_hash = ? AND expires_at > ?', [token_hash($token), now_utc()]) : null;
    if (!$c || !$c['user_id']) fail('link_expired', 410);
    return $c;
}

// ---------- Signing in ----------

function me(?array $s = null): array
{
    $s = $s ?? session();
    if (!$s) return ['user' => null];
    $u = $s['user'];
    $p = primary_email($u['id']);
    return [
        'user' => [
            'id' => $u['uid'],
            'firstName' => $u['first_name'],
            'lastName' => $u['last_name'],
            'nickname' => $u['nickname'],
            'birthdate' => $u['birthdate'],
            'locale' => $u['locale'],
            'email' => $p['email'],
            'createdAt' => iso($u['created_at']),
            'passwordChangedAt' => iso($u['password_changed_at']),
            'totp' => !empty($u['totp_enabled_at']),
            'email2fa' => !empty($u['email_2fa']),
            'recoveryCodes' => (int)q('SELECT COUNT(*) FROM ' . t('recovery_codes') . ' WHERE user_id = ?', [$u['id']])->fetchColumn(),
            'admin' => is_master($u),
            'mfa' => strpos((string)$s['amr'], 'mfa') !== false,
        ],
        'csrf' => $s['csrf'],
        'sudo' => $s['sudo_until'] && ts($s['sudo_until']) > time(),
    ];
}

/** Opens the session and answers. */
function finish_login(array $u, array $amr, bool $remember): void
{
    $known = (int)q('SELECT COUNT(*) FROM ' . t('events') . " WHERE user_id = ? AND kind IN ('login','register') AND device = ?", [$u['id'], device_label()])->fetchColumn();
    $any = (int)q('SELECT COUNT(*) FROM ' . t('events') . " WHERE user_id = ? AND kind IN ('login','register')", [$u['id']])->fetchColumn();
    // signing in again replaces the session this browser had
    $old = $_SERVER['HTTP_X_SAV_TOKEN'] ?? '';
    if (looks_like_token($old)) q('DELETE FROM ' . t('sessions') . ' WHERE token_hash = ?', [token_hash($old)]);
    $s = create_session($u['id'], $amr, $remember);
    log_event($u['id'], 'login');
    if ($any && !$known) send_mail('new_login', primary_email($u['id'])['email'], $u['locale'], ['name' => $u['first_name'], 'device' => device_label(), 'ip' => ip_short()], cfg('site') . '/security');
    $row = ['user' => user_row($u['id']), 'amr' => implode(' ', $amr), 'csrf' => $s['csrf'], 'sudo_until' => now_utc(SUDO_MINUTES * 60)];
    ok(me($row));
}

/** The second step: which ways the user has, and a code by email at once when that is the only one. */
function start_mfa(array $u, array $data): array
{
    $methods = [];
    if (!empty($u['totp_enabled_at'])) $methods[] = 'totp';
    if (!empty($u['email_2fa'])) $methods[] = 'email';
    $c = new_challenge('login', $u['id'], null, $data);
    if ($methods === ['email']) {
        rate_limit('mail', 'user:' . $u['id'], 8, 60);
        send_challenge($c['row']);
    }
    $methods[] = 'recovery';
    return ['challenge' => $c['token'], 'methods' => $methods, 'sentTo' => mask_email(primary_email($u['id'])['email'])];
}

function activate(array $u, int $emailId): void
{
    mark_verified($emailId);
    q('UPDATE ' . t('emails') . ' SET is_primary = 1 WHERE id = ?', [$emailId]);
    q('UPDATE ' . t('users') . " SET status = 'active' WHERE id = ? AND status = 'pending'", [$u['id']]);
    log_event($u['id'], 'register');
}

/** The password was just confirmed: sensitive actions are open for a few minutes. */
function sudo_on(array $s, bool $mfa): void
{
    q('UPDATE ' . t('sessions') . ' SET sudo_until = ?, auth_time = ?' . ($mfa ? ", amr = 'pwd otp mfa'" : '') . ' WHERE id = ?', [now_utc(SUDO_MINUTES * 60), now_utc(), $s['id']]);
    ok(['sudo' => true]);
}
