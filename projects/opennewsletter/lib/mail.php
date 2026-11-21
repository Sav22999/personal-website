<?php
/**
 * OpenNewsletter — outgoing email.
 *
 * Two kinds of sender:
 *   - the platform (account confirmations, invitations, approval requests and results): $mail_config in
 *     credentials.php, otherwise the site's contact-form settings (/.mail-config.php in the site root);
 *   - each group, for its newsletters and its subscribers (double opt-in, unsubscribe confirmation): the SMTP server
 *     its admin configured, stored encrypted (groups.smtp_sealed, see seal() in bootstrap.php).
 * Platform messages leave after the JSON response (fastcgi_finish_request). Without SMTP and with $debug on, the
 * links are written to the PHP error log so the flows can be tested locally.
 */

declare(strict_types=1);

// ---------------------------------------------------------------- platform sender

/** SMTP settings of the platform, or null when sending email is not configured. */
function mail_settings(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $cfg = null;
    $c = config_value('mail', null);
    if ($c === false) return $cfg; // switched off in credentials.php: never fall back to the site's settings
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

/** Adds a platform message to the queue sent at the end of the request (or right away from the command line). */
function queue_mail(string $to, string $name, string $subject, string $html, string $text): void
{
    static $queue = null;
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return;
    if (!mail_settings()) {
        if (config_value('debug', false)) error_log("OpenNewsletter mail (not sent, no SMTP) to $to: $subject\n$text");
        return;
    }
    if (PHP_SAPI === 'cli') {
        $r = smtp_send_all(mail_settings(), [compact('to', 'name', 'subject', 'html', 'text')]);
        if (!$r[0]['ok']) error_log('OpenNewsletter mail not sent: ' . $r[0]['error']);
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
                if (!$r['ok']) error_log('OpenNewsletter mail #' . $i . ' not sent: ' . $r['error']);
            }
        });
    }
    $queue->append(compact('to', 'name', 'subject', 'html', 'text'));
}

const VERIFY_HOURS = 48;

const MAIL_TEXT = [
    'en' => [
        'footer' => 'Automatic email sent by OpenNewsletter. Please do not reply to this message.',
        'verify_subject' => 'Confirm your email for OpenNewsletter', 'verify_title' => 'Welcome to OpenNewsletter',
        'verify_body' => 'Confirm that this address is yours to start creating newsletters.', 'verify_button' => 'Confirm my email',
        'verify_expires' => 'The link works for 48 hours. If you didn’t create an account, you can ignore this email.',
        'hello' => 'Hi {name},',
        'invite_subject' => 'You have been added to {group} on OpenNewsletter', 'invite_title' => 'A new group for you',
        'invite_body' => '{by} added you to the group “{group}” with the role {role}.', 'invite_signup' => 'Create your account with this email address to join it.',
        'invite_button' => 'Open OpenNewsletter',
        'review_subject' => 'To approve: “{subject}”', 'review_title' => 'A newsletter is waiting for you',
        'review_body' => '{by} proposed the newsletter “{subject}” for {group}, to be sent on {when}.', 'review_button' => 'Review it',
        'remind_subject' => 'Reminder: “{subject}” is still waiting for approval', 'remind_title' => 'Its time slot is close',
        'remind_body' => 'The newsletter “{subject}” of {group} should leave on {when}, but nobody has approved it yet. If it is not approved by then, it goes back to its author.',
        'approved_subject' => 'Approved: “{subject}”', 'approved_title' => 'Your newsletter is scheduled',
        'approved_body' => '{by} approved “{subject}”: it leaves on {when}.',
        'rejected_subject' => 'Not approved: “{subject}”', 'rejected_title' => 'Your newsletter needs changes',
        'rejected_body' => '{by} did not approve “{subject}”.', 'note' => 'Note:',
        'expired_subject' => 'Time slot missed: “{subject}”', 'expired_title' => 'Nobody approved it in time',
        'expired_body' => 'The newsletter “{subject}” of {group} was not approved before {when}. It is a draft again: choose a new time and propose it again.',
        'sent_subject' => 'Sent: “{subject}”', 'sent_title' => 'Your newsletter has left',
        'sent_body' => '“{subject}” reached {sent} subscribers of {group}.', 'sent_failed' => '{failed} messages could not be delivered.',
        'open_button' => 'Open it',
        'roles' => ['user' => 'user', 'moderator' => 'moderator', 'publisher' => 'publisher', 'admin' => 'administrator', 'owner' => 'owner'],
    ],
    'it' => [
        'footer' => 'Email automatica inviata da OpenNewsletter. Non rispondere a questo messaggio.',
        'verify_subject' => 'Conferma la tua email per OpenNewsletter', 'verify_title' => 'Benvenuto su OpenNewsletter',
        'verify_body' => 'Conferma che questo indirizzo è tuo per iniziare a creare newsletter.', 'verify_button' => 'Conferma la mia email',
        'verify_expires' => 'Il link vale 48 ore. Se non hai creato un account, puoi ignorare questa email.',
        'hello' => 'Ciao {name},',
        'invite_subject' => 'Sei stato aggiunto a {group} su OpenNewsletter', 'invite_title' => 'Un nuovo gruppo per te',
        'invite_body' => '{by} ti ha aggiunto al gruppo «{group}» con il ruolo {role}.', 'invite_signup' => 'Crea il tuo account con questo indirizzo email per entrare.',
        'invite_button' => 'Apri OpenNewsletter',
        'review_subject' => 'Da approvare: «{subject}»', 'review_title' => 'Una newsletter ti aspetta',
        'review_body' => '{by} ha proposto la newsletter «{subject}» per {group}, da inviare il {when}.', 'review_button' => 'Valutala',
        'remind_subject' => 'Promemoria: «{subject}» aspetta ancora l’approvazione', 'remind_title' => 'L’orario di invio è vicino',
        'remind_body' => 'La newsletter «{subject}» di {group} dovrebbe partire il {when}, ma nessuno l’ha ancora approvata. Se non viene approvata entro allora, torna al suo autore.',
        'approved_subject' => 'Approvata: «{subject}»', 'approved_title' => 'La tua newsletter è programmata',
        'approved_body' => '{by} ha approvato «{subject}»: parte il {when}.',
        'rejected_subject' => 'Non approvata: «{subject}»', 'rejected_title' => 'La tua newsletter va modificata',
        'rejected_body' => '{by} non ha approvato «{subject}».', 'note' => 'Nota:',
        'expired_subject' => 'Orario mancato: «{subject}»', 'expired_title' => 'Nessuno l’ha approvata in tempo',
        'expired_body' => 'La newsletter «{subject}» di {group} non è stata approvata entro il {when}. È tornata una bozza: scegli un nuovo orario e proponila di nuovo.',
        'sent_subject' => 'Inviata: «{subject}»', 'sent_title' => 'La tua newsletter è partita',
        'sent_body' => '«{subject}» ha raggiunto {sent} iscritti di {group}.', 'sent_failed' => '{failed} messaggi non sono stati consegnati.',
        'open_button' => 'Aprila',
        'roles' => ['user' => 'utilizzatore', 'moderator' => 'moderatore', 'publisher' => 'publisher', 'admin' => 'amministratore', 'owner' => 'proprietario'],
    ],
];

function mail_t(string $lang): array
{
    return MAIL_TEXT[$lang] ?? MAIL_TEXT['en'];
}

function fill(string $s, array $vars): string
{
    foreach ($vars as $k => $v) $s = str_replace('{' . $k . '}', (string)$v, $s);
    return $s;
}

function mail_button(string $url, string $label, string $color = '#5B3DF5'): string
{
    return '<table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:999px;background:' . e($color) . '">'
        . '<a href="' . e($url) . '" style="display:inline-block;padding:12px 26px;color:#ffffff;text-decoration:none;font-weight:700;font-size:15px">' . e($label) . '</a></td></tr></table>';
}

/** Layout of the platform's own emails: logo, title, content card, footer (inline styles for email clients). */
function mail_layout(string $lang, string $title, array $blocks, string $footer = ''): string
{
    $T = mail_t($lang);
    $logo = app_url() . 'icons/icon-192.png';
    return '<!DOCTYPE html><html lang="' . e($lang) . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($title) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#F3F1FA;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;color:#17152B">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F3F1FA"><tr><td align="center" style="padding:32px 16px">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px">'
        . '<tr><td style="padding:0 4px 18px"><img src="' . e($logo) . '" width="36" height="36" alt="" style="vertical-align:middle;border-radius:9px"> '
        . '<span style="font-size:19px;font-weight:800;vertical-align:middle;letter-spacing:-.02em">OpenNewsletter</span></td></tr>'
        . '<tr><td style="background:#ffffff;border-radius:18px;padding:28px 28px 26px;border:1px solid #E4E0F2;font-size:15px;line-height:1.55">'
        . '<h1 style="margin:0 0 16px;font-size:22px;line-height:1.25;letter-spacing:-.02em">' . e($title) . '</h1>'
        . implode('', $blocks)
        . '</td></tr><tr><td style="padding:16px 4px 0;font-size:12px;color:#6E6A85">' . e($footer ?: $T['footer']) . '</td></tr>'
        . '</table></td></tr></table></body></html>';
}

function p_html(string $text, string $style = 'margin:0 0 14px'): string
{
    return '<p style="' . $style . '">' . nl2br(e($text)) . '</p>';
}

/** A platform notice: title, paragraphs, optional button. */
function send_notice(string $to, string $name, string $lang, string $subject, string $title, array $paragraphs, ?string $url = null, string $button = ''): void
{
    $blocks = array_map(fn($p) => p_html($p), array_filter($paragraphs, fn($p) => $p !== ''));
    if ($url) $blocks[] = '<div style="padding-top:6px">' . mail_button($url, $button) . '</div>';
    $text = implode("\n\n", array_filter($paragraphs, fn($p) => $p !== '')) . ($url ? "\n\n$url" : '');
    queue_mail($to, $name, $subject, mail_layout($lang, $title, $blocks), $text);
}

/** A new single-use link for this account; the older ones stop working. */
function send_verification(array $user, string $lang): void
{
    $token = bin2hex(random_bytes(32));
    db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ?')->execute([$user['id']]);
    db()->prepare('INSERT INTO ' . t('email_tokens') . ' (token_hash, user_id, created_at, expires_at) VALUES (?,?,?,?)')
        ->execute([hash('sha256', $token), $user['id'], now_utc(), gmdate('Y-m-d H:i:s', time() + VERIFY_HOURS * 3600)]);
    $T = mail_t($lang);
    $url = app_url() . '?verify=' . $token;
    if (!mail_settings()) {
        error_log(config_value('debug', false) ? 'OpenNewsletter verification link for user #' . $user['id'] . ': ' . $url : 'OpenNewsletter: email is not configured, accounts cannot be confirmed');
        return;
    }
    send_notice((string)$user['email'], (string)$user['name'], $lang, $T['verify_subject'], $T['verify_title'],
        [fill($T['hello'], ['name' => $user['name']]), $T['verify_body'], $T['verify_expires']], $url, $T['verify_button']);
}

// ---------------------------------------------------------------- group sender (SMTP of each group)

/** The SMTP settings of a group, ready for smtp_send_all(), or null when its admin has not configured them. */
function group_mailer(array $g): ?array
{
    if (empty($g['smtp_sealed']) || empty($g['from_email'])) return null;
    $s = unseal((string)$g['smtp_sealed']);
    if (!$s || empty($s['smtp_host'])) return null;
    $s['from_email'] = $g['from_email'];
    $s['from_name'] = $g['from_name'] ?: $g['name'];
    $s['reply_to'] = $g['reply_to'] ?? '';
    return $s;
}

/**
 * The SMTP host of a group must be a public server: no loopback, private or reserved addresses (otherwise anyone who
 * creates a group could make this server knock on machines of its own network).
 */
function smtp_host_allowed(string $host): bool
{
    if (!preg_match('/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/i', $host) && !filter_var($host, FILTER_VALIDATE_IP)) return false;
    if (config_value('debug', false) && in_array($host, ['127.0.0.1', 'localhost'], true)) return true; // local tests (a fake SMTP)
    $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
    if (!$ips) return false;
    foreach ($ips as $ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return false;
    }
    return true;
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
        $c = new OnSmtp($cfg);
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

final class OnSmtp
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
        $b = 'on-' . bin2hex(random_bytes(12));
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
