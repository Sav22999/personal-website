<?php
/**
 * ThinkHop — outgoing email: only the link that confirms the email address of an account.
 *
 * SMTP settings, in order:
 *   1. $mail_config in credentials.php (see credentials-sample.php);
 *   2. otherwise the site's contact-form settings, /.mail-config.php in the site root.
 * Without settings nothing can be sent, and then the email check is not required (see verification_required()).
 * Messages leave after the JSON response (fastcgi_finish_request), so signing up is never slowed down.
 * The SMTP client is the same as Giftwise and FoodTrack.
 */

declare(strict_types=1);

/** SMTP settings, or null when sending email is not configured. */
function mail_settings(): ?array
{
    static $cfg = false;
    if ($cfg !== false) return $cfg;
    $cfg = null;
    $c = config_value('mail', null);
    if (!is_array($c) || empty($c['smtp_host'])) {
        $site = dirname(__DIR__, 3) . '/.mail-config.php';
        $c = is_file($site) ? (require $site) : null;
    }
    if (is_array($c) && !empty($c['smtp_host']) && !empty($c['from_email'])) $cfg = $c;
    return $cfg;
}

/** Adds a message to the queue sent at the end of the request. */
function queue_mail(string $to, string $name, string $subject, string $html, string $text): void
{
    static $queue = null;
    if (!mail_settings() || !filter_var($to, FILTER_VALIDATE_EMAIL)) return;
    if ($queue === null) {
        $queue = new ArrayObject();
        register_shutdown_function(function () use ($queue) {
            if (!count($queue)) return;
            if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request();
            ignore_user_abort(true);
            @set_time_limit(60);
            foreach (th_send_mails($queue->getArrayCopy()) as $i => $r) {
                if (!$r['ok']) error_log('ThinkHop mail to message #' . $i . ' not sent: ' . $r['error']);
            }
        });
    }
    $queue->append(compact('to', 'name', 'subject', 'html', 'text'));
}

// ---------------------------------------------------------------- the email check

const VERIFY_HOURS = 48;

const MAIL_TEXT = [
    'en' => ['subject' => 'Confirm your email for ThinkHop', 'title' => 'One last hop', 'hello' => 'Hi {name},', 'body' => 'Confirm that this address is yours to start using your ThinkHop account.', 'button' => 'Confirm my email', 'expires' => 'The link works for 48 hours. If you didn’t create an account, you can ignore this email.', 'footer' => 'Automatic email sent by ThinkHop. Please do not reply to this message.'],
    'it' => ['subject' => 'Conferma la tua email per ThinkHop', 'title' => 'Ultimo salto', 'hello' => 'Ciao {name},', 'body' => 'Conferma che questo indirizzo è tuo per iniziare a usare il tuo account ThinkHop.', 'button' => 'Conferma la mia email', 'expires' => 'Il link vale 48 ore. Se non hai creato un account, puoi ignorare questa email.', 'footer' => 'Email automatica inviata da ThinkHop. Non rispondere a questo messaggio.'],
    'es' => ['subject' => 'Confirma tu correo para ThinkHop', 'title' => 'Un último salto', 'hello' => 'Hola, {name}:', 'body' => 'Confirma que esta dirección es tuya para empezar a usar tu cuenta de ThinkHop.', 'button' => 'Confirmar mi correo', 'expires' => 'El enlace vale 48 horas. Si no has creado una cuenta, puedes ignorar este correo.', 'footer' => 'Correo automático enviado por ThinkHop. No respondas a este mensaje.'],
    'fr' => ['subject' => 'Confirme ton e-mail pour ThinkHop', 'title' => 'Un dernier saut', 'hello' => 'Bonjour {name},', 'body' => 'Confirme que cette adresse est bien la tienne pour commencer à utiliser ton compte ThinkHop.', 'button' => 'Confirmer mon e-mail', 'expires' => 'Le lien est valable 48 heures. Si tu n’as pas créé de compte, ignore cet e-mail.', 'footer' => 'E-mail automatique envoyé par ThinkHop. Merci de ne pas y répondre.'],
    'de' => ['subject' => 'Bestätige deine E-Mail für ThinkHop', 'title' => 'Ein letzter Sprung', 'hello' => 'Hallo {name},', 'body' => 'Bestätige, dass diese Adresse dir gehört, um dein ThinkHop-Konto zu nutzen.', 'button' => 'E-Mail bestätigen', 'expires' => 'Der Link gilt 48 Stunden. Wenn du kein Konto erstellt hast, ignoriere diese E-Mail.', 'footer' => 'Automatische E-Mail von ThinkHop. Bitte antworte nicht auf diese Nachricht.'],
];

/** Absolute URL of the app (e.g. https://www.saveriomorelli.com/projects/thinkhop/). */
function app_url(): string
{
    $host = preg_replace('/[^a-z0-9.:-]/i', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
    return (is_https() ? 'https://' : 'http://') . $host . app_base_path();
}

/** The check is required only when an email can actually be sent: otherwise nobody could ever get in. */
function verification_required(): bool
{
    return mail_settings() !== null;
}

/** A new single-use link for this account; the older ones stop working. */
function send_verification(array $user, string $lang): void
{
    if (!mail_settings()) return;
    $token = bin2hex(random_bytes(32));
    db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ?')->execute([$user['id']]);
    db()->prepare('INSERT INTO ' . t('email_tokens') . ' (token_hash, user_id, created_at, expires_at) VALUES (?,?,?,?)')
        ->execute([hash('sha256', $token), $user['id'], now_utc(), gmdate('Y-m-d H:i:s', time() + VERIFY_HOURS * 3600)]);
    $T = MAIL_TEXT[$lang] ?? MAIL_TEXT['en'];
    $url = app_url() . '?verify=' . $token;
    $hello = str_replace('{name}', (string)$user['name'], $T['hello']);
    $html = mail_layout($lang, $T['title'], [
        '<p style="margin:0 0 12px">' . e($hello) . '</p>',
        '<p style="margin:0 0 20px">' . e($T['body']) . '</p>',
        mail_button($url, $T['button']),
        '<p style="margin:20px 0 0;font-size:13px;color:#625C7E">' . e($T['expires']) . '</p>',
    ]);
    $text = "$hello\n\n{$T['body']}\n\n$url\n\n{$T['expires']}";
    queue_mail((string)$user['email'], (string)$user['name'], $T['subject'], $html, $text);
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function mail_button(string $url, string $label): string
{
    return '<table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:12px;background:#FFD23F;border:2px solid #1B1640">'
        . '<a href="' . e($url) . '" style="display:inline-block;padding:12px 24px;color:#1B1640;text-decoration:none;font-weight:800;font-size:15px">' . e($label) . '</a></td></tr></table>';
}

/** ThinkHop email layout: logo, title, content card, footer (inline styles for email clients). */
function mail_layout(string $lang, string $title, array $blocks): string
{
    $T = MAIL_TEXT[$lang] ?? MAIL_TEXT['en'];
    $logo = app_url() . 'icons/icon-192.png';
    return '<!DOCTYPE html><html lang="' . e($lang) . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($title) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#FFFBF2;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;color:#1B1640">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FFFBF2"><tr><td align="center" style="padding:32px 16px">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px">'
        . '<tr><td style="padding:0 4px 18px"><img src="' . e($logo) . '" width="40" height="40" alt="" style="vertical-align:middle;border-radius:10px"> '
        . '<span style="font-size:20px;font-weight:800;vertical-align:middle;letter-spacing:-.02em">ThinkHop</span></td></tr>'
        . '<tr><td style="background:#ffffff;border-radius:16px;padding:28px 28px 26px;border:2px solid #1B1640;font-size:15px;line-height:1.55">'
        . '<h1 style="margin:0 0 16px;font-size:22px;line-height:1.25;letter-spacing:-.02em">' . e($title) . '</h1>'
        . implode('', $blocks)
        . '</td></tr><tr><td style="padding:16px 4px 0;font-size:12px;color:#625C7E">' . e($T['footer']) . '</td></tr>'
        . '</table></td></tr></table></body></html>';
}

// ---------------------------------------------------------------- minimal SMTP client (no libraries)

/** Sends the messages over one SMTP connection. Returns one ['ok' => bool, 'error' => string] per message. */
function th_send_mails(array $messages): array
{
    $cfg = mail_settings();
    if (!$cfg) return array_fill(0, count($messages), ['ok' => false, 'error' => 'mail not configured']);
    $out = [];
    try {
        $c = new ThSmtp($cfg);
        foreach ($messages as $m) {
            try {
                $c->send($m['to'], $m['name'], $m['subject'], $m['html'], $m['text']);
                $out[] = ['ok' => true, 'error' => ''];
            } catch (Throwable $e) {
                $out[] = ['ok' => false, 'error' => $e->getMessage()];
                $c->reset();
            }
        }
        $c->close();
    } catch (Throwable $e) {
        while (count($out) < count($messages)) $out[] = ['ok' => false, 'error' => $e->getMessage()];
    }
    return $out;
}

final class ThSmtp
{
    private $fp;
    private array $c;

    public function __construct(array $cfg)
    {
        $this->c = $cfg;
        $enc = strtolower((string)($cfg['smtp_encryption'] ?? 'ssl'));
        $port = (int)($cfg['smtp_port'] ?? ($enc === 'ssl' ? 465 : 587));
        $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'peer_name' => $cfg['smtp_host']]]);
        $fp = @stream_socket_client(($enc === 'ssl' ? 'ssl://' : 'tcp://') . $cfg['smtp_host'] . ':' . $port, $no, $str, 15, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) throw new RuntimeException('SMTP connection failed (' . $no . ')');
        stream_set_timeout($fp, 15);
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

    public function send(string $to, string $name, string $subject, string $html, string $text): void
    {
        $from = (string)$this->c['from_email'];
        $this->cmd('MAIL FROM:<' . $from . '>', 250);
        $this->cmd('RCPT TO:<' . str_replace(["\r", "\n", '<', '>'], '', $to) . '>', [250, 251], 'recipient refused');
        $this->cmd('DATA', 354);
        $b = 'gw-' . bin2hex(random_bytes(12));
        $domain = substr(strrchr($from, '@') ?: '@localhost', 1);
        $h = [
            'Date: ' . date('r'),
            'From: ' . self::addr($from, (string)($this->c['from_name'] ?? 'ThinkHop')),
            'To: ' . self::addr($to, $name),
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>',
            'MIME-Version: 1.0',
            'Auto-Submitted: auto-generated',
            'Content-Type: multipart/alternative; boundary="' . $b . '"',
        ];
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
