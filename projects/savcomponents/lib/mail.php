<?php
/**
 * SavComponents — outgoing email: "confirm your email" and "choose a new password" links.
 * SMTP: $mail_config in credentials.php, otherwise the site's contact-form settings (/.mail-config.php in the site
 * root). Messages leave after the JSON response. Without SMTP and with $debug on, the links go to the PHP error log.
 * The SMTP client below is the same as OpenNewsletter's.
 */

declare(strict_types=1);

const VERIFY_HOURS = 48;
const RESET_HOURS = 2;

/** SMTP settings, or null when sending email is not configured. */
function mail_settings(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $cfg = null;
    $c = config_value('mail', null);
    if ($c === false) return $cfg;
    if (!is_array($c) || empty($c['smtp_host'])) {
        $site = dirname(__DIR__, 3) . '/.mail-config.php';
        $c = is_file($site) ? (require $site) : null;
    }
    if (is_array($c) && !empty($c['smtp_host']) && !empty($c['from_email'])) {
        $c['from_name'] = $c['from_name'] ?? APP_NAME;
        $cfg = $c;
    }
    return $cfg;
}

/** Queues a message, sent at the end of the request (after the JSON answer has left). */
function queue_mail(string $to, string $name, string $subject, string $html, string $text): void
{
    static $queue = null;
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return;
    if (!mail_settings()) {
        if (config_value('debug', false)) error_log("SavComponents mail (not sent, no SMTP) to $to: $subject\n$text");
        return;
    }
    if ($queue === null) {
        $queue = new ArrayObject();
        register_shutdown_function(function () use ($queue) {
            if (!count($queue)) return;
            if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request();
            ignore_user_abort(true);
            @set_time_limit(60);
            foreach (smtp_send_all(mail_settings(), $queue->getArrayCopy()) as $i => $r) {
                if (!$r['ok']) error_log('SavComponents mail #' . $i . ' not sent: ' . $r['error']);
            }
        });
    }
    $queue->append(compact('to', 'name', 'subject', 'html', 'text'));
}

/** Square, slightly tilted button: the look of the site (inline styles for email clients). No $url: no button. */
function mail_layout(string $title, array $paragraphs, string $url, string $button): string
{
    $p = implode('', array_map(fn($t) => '<p style="margin:0 0 14px">' . nl2br(e($t)) . '</p>', $paragraphs));
    return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($title) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f6f7fb;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;color:#1a1f2e">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb"><tr><td align="center" style="padding:32px 16px">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px">'
        . '<tr><td style="padding:0 4px 18px;font-size:19px;letter-spacing:-.01em">Sav<b>Components</b></td></tr>'
        . '<tr><td style="background:#ffffff;padding:28px;border:1px solid #e2e5ee;font-size:15px;line-height:1.55">'
        . '<h1 style="margin:0 0 16px;font-size:22px;line-height:1.25">' . e($title) . '</h1>' . $p
        . ($url === '' ? '' : '<table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:6px"><tr><td style="background:#66cbff">'
        . '<a href="' . e($url) . '" style="display:inline-block;padding:12px 24px;color:#072536;text-decoration:none;font-weight:700">' . e($button) . '</a></td></tr></table>')
        . '</td></tr><tr><td style="padding:16px 4px 0;font-size:12px;color:#5f6880">Automatic email sent by SavComponents. Please do not reply.</td></tr>'
        . '</table></td></tr></table></body></html>';
}

/** A new single-use link (verify or reset): the older ones of the same kind stop working. */
function email_link(array $user, string $kind): string
{
    $token = bin2hex(random_bytes(32));
    db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ? AND kind = ?')->execute([$user['id'], $kind]);
    db()->prepare('INSERT INTO ' . t('email_tokens') . ' (token_hash, user_id, kind, created_at, expires_at) VALUES (?,?,?,?,?)')
        ->execute([hash('sha256', $token), $user['id'], $kind, now_utc(), gmdate('Y-m-d H:i:s', time() + ($kind === 'verify' ? VERIFY_HOURS : RESET_HOURS) * 3600)]);
    $url = app_url() . '?' . $kind . '=' . $token;
    if (!mail_settings()) error_log(config_value('debug', false) ? "SavComponents $kind link for user #{$user['id']}: $url" : 'SavComponents: email is not configured');
    return $url;
}

function send_verification(array $user): void
{
    $url = email_link($user, 'verify');
    $hi = 'Hi ' . $user['name'] . ',';
    queue_mail((string)$user['email'], (string)$user['name'], 'Confirm your email for SavComponents',
        mail_layout('Welcome to SavComponents', [$hi, 'Confirm that this address is yours to start saving favourite components and your themes.', 'The link works for 48 hours. If you did not create an account, ignore this email.'], $url, 'Confirm my email'),
        "$hi\n\nConfirm your email for SavComponents:\n$url\n\nThe link works for 48 hours.");
}

function send_reset(array $user): void
{
    $url = email_link($user, 'reset');
    $hi = 'Hi ' . $user['name'] . ',';
    queue_mail((string)$user['email'], (string)$user['name'], 'Choose a new SavComponents password',
        mail_layout('Choose a new password', [$hi, 'Someone (hopefully you) asked to reset the password of your SavComponents account.', 'The link works for 2 hours. If it was not you, ignore this email: your password does not change.'], $url, 'Choose a new password'),
        "$hi\n\nChoose a new SavComponents password:\n$url\n\nThe link works for 2 hours.");
}

/** The code that proves an account is yours before a SavAccount is joined to it. */
function send_sav_code(array $user, string $code): void
{
    $hi = 'Hi ' . $user['name'] . ',';
    $p = [$hi, 'Someone (hopefully you) wants to sign in to your SavComponents account with SavAccount. To join them, enter this code:', $code,
        'It works for 15 minutes. If it was not you, ignore this email: nothing changes.'];
    queue_mail((string)$user['email'], (string)$user['name'], "$code is your SavComponents code", mail_layout('Join your SavAccount', $p, '', ''),
        "$hi\n\nTo join your SavAccount to your SavComponents account, enter this code: $code\n\nIt works for 15 minutes. If it was not you, ignore this email.");
    if (!mail_settings() && config_value('debug', false)) error_log("SavComponents SavAccount code for user #{$user['id']}: $code");
}

/** A SavAccount was linked to the account, or unlinked from it. */
function send_sav_notice(array $user, bool $linked): void
{
    $hi = 'Hi ' . $user['name'] . ',';
    $p = $linked
        ? [$hi, 'From now on you can also sign in to SavComponents with SavAccount.', 'If it was not you, sign in, unlink SavAccount in the settings of your account and change your password.']
        : [$hi, 'You can no longer sign in to SavComponents with SavAccount.', 'If it was not you, sign in and change your password.'];
    queue_mail((string)$user['email'], (string)$user['name'], $linked ? 'SavAccount linked to your SavComponents account' : 'SavAccount unlinked from your SavComponents account',
        mail_layout($linked ? 'SavAccount linked' : 'SavAccount unlinked', $p, app_url() . '#account', 'Open my account'), implode("\n\n", $p));
}

// ---------------------------------------------------------------- minimal SMTP client (no libraries)

/**
 * Sends the messages over one SMTP connection. Each message: to, name, subject, html, text and optionally headers
 * (extra header lines). Returns one ['ok' => bool, 'temp' => bool, 'error' => string] per message, in the same order.
 *
 * temp = the server said "not now" (a 4xx reply: too many messages, greylisting, mailbox busy) or the connection
 * failed: the message should be tried again later. A temporary error stops the batch at once (the server is throttling
 * us: insisting would only get the address blocked). A permanent error (5xx: unknown address…) fails that message only.
 * $after($i, $result) runs after each message; $pauseMs waits between messages (gentler on the provider's limits).
 */
function smtp_send_all(?array $cfg, array $messages, ?callable $after = null, int $pauseMs = 0): array
{
    if (!$cfg) return array_map(fn() => ['ok' => false, 'temp' => true, 'error' => 'mail not configured'], $messages);
    $out = [];
    $stop = false;
    try {
        $c = new ScSmtp($cfg);
        $first = true;
        foreach ($messages as $i => $m) {
            if (!$first && $pauseMs > 0) usleep($pauseMs * 1000);
            $first = false;
            try {
                $c->send($m['to'], $m['name'] ?? '', $m['subject'], $m['html'], $m['text'], $m['headers'] ?? []);
                $out[$i] = ['ok' => true, 'temp' => false, 'error' => ''];
            } catch (Throwable $e) {
                $temp = smtp_error_is_temporary($e->getMessage());
                $out[$i] = ['ok' => false, 'temp' => $temp, 'error' => $e->getMessage()];
                if ($temp) $stop = true;
                else $c->reset();
            }
            if ($after) $after($i, $out[$i]);
            if ($stop) break;
        }
        $c->close();
    } catch (Throwable $e) {
        // could not connect or log in: nothing was refused, everything is tried again later
        foreach ($messages as $i => $m) {
            if (isset($out[$i])) continue;
            $out[$i] = ['ok' => false, 'temp' => true, 'error' => $e->getMessage()];
            if ($after) $after($i, $out[$i]);
        }
    }
    // messages after a temporary stop were not attempted
    foreach ($messages as $i => $m) if (!isset($out[$i])) $out[$i] = ['ok' => false, 'temp' => true, 'error' => 'not attempted'];
    ksort($out);
    return $out;
}

/** 4xx replies and lost connections are temporary; 5xx replies are permanent. */
function smtp_error_is_temporary(string $error): bool
{
    if (preg_match('/\((\d{3})\)/', $error, $m)) return $m[1][0] === '4' || $m[1] === '000';
    return true; // connection lost, timeout…
}

final class ScSmtp
{
    private $fp;
    private array $c;

    public function __construct(array $cfg)
    {
        $this->c = $cfg;
        $enc = strtolower((string)($cfg['smtp_encryption'] ?? 'ssl'));
        $port = (int)($cfg['smtp_port'] ?? ($enc === 'ssl' ? 465 : 587));
        $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'peer_name' => $cfg['smtp_host']]]);
        $scheme = $enc === 'ssl' ? 'ssl://' : 'tcp://';
        $fp = @stream_socket_client($scheme . $cfg['smtp_host'] . ':' . $port, $no, $str, 15, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) throw new RuntimeException('SMTP connection failed (' . $no . ')');
        stream_set_timeout($fp, 20);
        $this->fp = $fp;
        $this->expect(220);
        $host = preg_replace('/[^a-z0-9.-]/i', '', (string)($_SERVER['SERVER_NAME'] ?? '')) ?: 'localhost';
        $this->cmd('EHLO ' . $host, 250);
        if ($enc === 'tls') {
            $this->cmd('STARTTLS', 220);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) throw new RuntimeException('STARTTLS failed');
            $this->cmd('EHLO ' . $host, 250);
        }
        if (!empty($cfg['smtp_user'])) {
            $this->cmd('AUTH LOGIN', 334);
            $this->cmd(base64_encode((string)$cfg['smtp_user']), 334);
            $this->cmd(base64_encode((string)($cfg['smtp_password'] ?? '')), 235, 'SMTP authentication failed'); // never logged
        }
    }

    private function expect($ok, string $what = ''): void
    {
        $reply = '';
        while (($l = fgets($this->fp, 1024)) !== false) {
            $reply .= $l;
            if (strlen($l) < 4 || $l[3] !== '-') break;
        }
        $code = (int)substr($reply, 0, 3);
        if (!in_array($code, (array)$ok, true)) throw new RuntimeException(($what ?: 'unexpected SMTP reply') . ' (' . $code . ')');
    }

    private function cmd(string $line, $ok, string $what = ''): void
    {
        if (@fwrite($this->fp, $line . "\r\n") === false) throw new RuntimeException('SMTP connection lost');
        $this->expect($ok, $what);
    }

    public function send(string $to, string $name, string $subject, string $html, string $text, array $extra = []): void
    {
        $from = (string)$this->c['from_email'];
        $this->cmd('MAIL FROM:<' . $from . '>', 250);
        $this->cmd('RCPT TO:<' . str_replace(["\r", "\n", '<', '>'], '', $to) . '>', [250, 251], 'recipient refused');
        $this->cmd('DATA', 354);
        $b = 'sc-' . bin2hex(random_bytes(12));
        $domain = substr(strrchr($from, '@') ?: '@localhost', 1);
        $h = [
            'Date: ' . date('r'),
            'From: ' . self::addr($from, (string)($this->c['from_name'] ?? APP_NAME)),
            'To: ' . self::addr($to, $name),
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>',
            'MIME-Version: 1.0',
        ];
        if (!empty($this->c['reply_to']) && filter_var($this->c['reply_to'], FILTER_VALIDATE_EMAIL)) $h[] = 'Reply-To: ' . self::addr((string)$this->c['reply_to']);
        foreach ($extra as $line) $h[] = str_replace(["\r", "\n"], '', (string)$line);
        $h[] = 'Content-Type: multipart/alternative; boundary="' . $b . '"';
        $part = fn(string $type, string $body) => '--' . $b . "\r\nContent-Type: " . $type . "; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . rtrim(chunk_split(base64_encode($body), 76, "\r\n")) . "\r\n";
        // Base64 lines never start with a dot: no dot-stuffing needed
        $this->cmd(implode("\r\n", $h) . "\r\n\r\n" . $part('text/plain', $text) . $part('text/html', $html) . '--' . $b . "--\r\n.", 250, 'message refused');
    }

    private static function addr(string $email, string $name = ''): string
    {
        $email = str_replace(["\r", "\n", '<', '>'], '', $email);
        $name = trim(str_replace(["\r", "\n", '"'], '', $name));
        return $name === '' ? $email : '=?UTF-8?B?' . base64_encode($name) . '?= <' . $email . '>';
    }

    public function reset(): void
    {
        try {
            $this->cmd('RSET', 250);
        } catch (Throwable $e) { /* connection lost */
        }
    }

    public function close(): void
    {
        if ($this->fp) {
            @fwrite($this->fp, "QUIT\r\n");
            @fclose($this->fp);
            $this->fp = null;
        }
    }
}
