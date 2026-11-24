<?php
/** SavAccount — authenticator apps (TOTP, RFC 6238: SHA-1, 6 digits, 30 seconds) and recovery codes. */

declare(strict_types=1);

function base32_encode_bin(string $bin): string
{
    $abc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $bits = '';
    foreach (str_split($bin) as $c) $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
    $out = '';
    foreach (str_split($bits, 5) as $chunk) $out .= $abc[bindec(str_pad($chunk, 5, '0'))];
    return $out;
}

function totp_at(string $secret, int $step): string
{
    $h = hash_hmac('sha1', pack('J', $step), $secret, true);
    $o = ord($h[19]) & 0xf;
    $n = unpack('N', substr($h, $o, 4))[1] & 0x7fffffff;
    return str_pad((string)($n % 1000000), 6, '0', STR_PAD_LEFT);
}

/** The time step the code belongs to (one step of tolerance), or null. Steps up to $after were already used. */
function totp_match(string $secret, string $code, int $after = 0): ?int
{
    $now = intdiv(time(), 30);
    $found = null;
    foreach ([$now - 1, $now, $now + 1] as $step) {
        if (hash_equals(totp_at($secret, $step), $code) && $step > $after) $found = $step;
    }
    return $found;
}

/** Checks a code of the user's app; each code works once. */
function totp_check(array $user, string $code): bool
{
    if (empty($user['totp_enabled_at']) || !preg_match('/^\d{6}$/', $code)) return false;
    $step = totp_match(decrypt((string)$user['totp_secret'], 'totp:' . $user['uid']), $code, (int)$user['totp_last_step']);
    if ($step === null) return false;
    return q('UPDATE ' . t('users') . ' SET totp_last_step = ? WHERE id = ? AND totp_last_step < ?', [$step, $user['id'], $step])->rowCount() === 1;
}

function recovery_hash(array $user, string $code): string
{
    return keyed_hash('recovery', $user['uid'] . ':' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code)));
}

/** Ten new codes; the old ones stop working. Returned only once. */
function new_recovery_codes(array $user): array
{
    $abc = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no 0/O, 1/I
    $codes = [];
    transaction(function () use ($user, $abc, &$codes) {
        q('DELETE FROM ' . t('recovery_codes') . ' WHERE user_id = ?', [$user['id']]);
        while (count($codes) < 10) {
            $c = '';
            for ($i = 0; $i < 10; $i++) $c .= $abc[random_int(0, 31)];
            $codes[] = substr($c, 0, 5) . '-' . substr($c, 5);
            q('INSERT INTO ' . t('recovery_codes') . ' (user_id, code_hash) VALUES (?,?)', [$user['id'], recovery_hash($user, $c)]);
        }
    });
    return $codes;
}

function use_recovery_code(array $user, string $code): bool
{
    return q('DELETE FROM ' . t('recovery_codes') . ' WHERE user_id = ? AND code_hash = ?', [$user['id'], recovery_hash($user, $code)])->rowCount() === 1;
}
