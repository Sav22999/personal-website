<?php
/**
 * Outgoing email over SMTP (no external libraries).
 *
 * The mailbox details live in lib/mail-config.php, which is excluded from git and blocked
 * to the browser (lib/.htaccess). See lib/mail-config.example.php for the keys.
 * Supports implicit TLS ("ssl", port 465), STARTTLS ("tls", port 587) and, for local tests only, "none".
 */

/** SMTP settings, or null when sending email is not configured. */
function mail_settings(): ?array
{
    static $cfg = false;
    if ($cfg === false) {
        $cfg = null;
        $file = getenv('FIPAV_MAIL_CONFIG') ?: __DIR__ . '/mail-config.php';
        if (is_file($file)) {
            $c = require $file;
            if (is_array($c) && !empty($c['smtp_host']) && !empty($c['from_email'])) $cfg = $c;
        }
    }
    return $cfg;
}

function mail_enabled(): bool
{
    return mail_settings() !== null;
}

/** "Name <address>" header value, with the name encoded when it is not plain ASCII. */
function mail_address(string $email, string $name = ''): string
{
    $email = str_replace(["\r", "\n", '<', '>'], '', $email);
    $name = trim(str_replace(["\r", "\n", '"'], '', $name));
    if ($name === '') return $email;
    $enc = preg_match('/[^\x20-\x7E]/', $name) ? '=?UTF-8?B?' . base64_encode($name) . '?=' : '"' . $name . '"';
    return $enc . ' <' . $email . '>';
}

/** Minimal SMTP client: one connection, many messages. */
class SmtpClient
{
    private $fp = null;
    private array $cfg;

    public function __construct(array $cfg)
    {
        $this->cfg = $cfg;
    }

    public function connect(): void
    {
        $c = $this->cfg;
        $enc = strtolower((string)($c['smtp_encryption'] ?? 'ssl'));
        $port = (int)($c['smtp_port'] ?? ($enc === 'ssl' ? 465 : 587));
        $verify = ($c['verify_peer'] ?? true) !== false;
        $ctx = stream_context_create(['ssl' => [
            'verify_peer' => $verify, 'verify_peer_name' => $verify, 'allow_self_signed' => !$verify,
            'peer_name' => $c['smtp_host'],
        ]]);
        $remote = ($enc === 'ssl' ? 'ssl://' : 'tcp://') . $c['smtp_host'] . ':' . $port;
        $timeout = (int)($c['timeout'] ?? 15);
        $fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) throw new RuntimeException('connessione al server di posta non riuscita (' . $errstr . ')');
        stream_set_timeout($fp, $timeout);
        $this->fp = $fp;
        $this->expect(220);
        $host = preg_replace('/[^a-z0-9.-]/i', '', (string)($_SERVER['SERVER_NAME'] ?? '')) ?: 'localhost';
        $this->command('EHLO ' . $host, 250);
        if ($enc === 'tls') {
            $this->command('STARTTLS', 220);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
                throw new RuntimeException('connessione cifrata (STARTTLS) non riuscita');
            }
            $this->command('EHLO ' . $host, 250);
        }
        if (!empty($c['smtp_user'])) {
            $this->command('AUTH LOGIN', 334);
            $this->command(base64_encode((string)$c['smtp_user']), 334);
            // The password is never written to logs or error messages
            $this->command(base64_encode((string)($c['smtp_password'] ?? '')), 235, 'autenticazione non riuscita');
        }
    }

    /** Sends one message; $html and $text are the two alternative bodies. */
    public function send(string $to, string $toName, string $subject, string $html, string $text): void
    {
        $c = $this->cfg;
        $from = (string)$c['from_email'];
        $this->command('MAIL FROM:<' . $from . '>', 250);
        $this->command('RCPT TO:<' . str_replace(["\r", "\n", '<', '>'], '', $to) . '>', [250, 251], 'destinatario rifiutato');
        $this->command('DATA', 354);

        $boundary = 'fischietto-' . bin2hex(random_bytes(12));
        $domain = substr(strrchr($from, '@') ?: '@localhost', 1);
        $headers = [
            'Date: ' . date('r'),
            'From: ' . mail_address($from, (string)($c['from_name'] ?? 'Fischietto')),
            'To: ' . mail_address($to, $toName),
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>',
            'MIME-Version: 1.0',
            'Auto-Submitted: auto-generated',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];
        if (!empty($c['reply_to'])) $headers[] = 'Reply-To: ' . mail_address((string)$c['reply_to']);
        $part = function (string $type, string $body) use ($boundary) {
            return '--' . $boundary . "\r\n" . 'Content-Type: ' . $type . '; charset=UTF-8' . "\r\n"
                . "Content-Transfer-Encoding: base64\r\n\r\n" . rtrim(chunk_split(base64_encode($body), 76, "\r\n")) . "\r\n";
        };
        // Base64 lines never start with a dot, so no dot-stuffing is needed
        $data = implode("\r\n", $headers) . "\r\n\r\n"
            . $part('text/plain', $text) . $part('text/html', $html)
            . '--' . $boundary . "--\r\n.";
        $this->command($data, 250, 'messaggio rifiutato dal server');
    }

    public function close(): void
    {
        if ($this->fp) {
            @fwrite($this->fp, "QUIT\r\n");
            @fclose($this->fp);
            $this->fp = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /** @param int|int[] $ok expected reply codes */
    private function command(string $line, $ok, string $what = ''): string
    {
        if (@fwrite($this->fp, $line . "\r\n") === false) throw new RuntimeException('connessione al server di posta interrotta');
        return $this->expect($ok, $what);
    }

    private function expect($ok, string $what = ''): string
    {
        $reply = '';
        while (($line = fgets($this->fp, 1024)) !== false) {
            $reply .= $line;
            if (strlen($line) < 4 || $line[3] !== '-') break;
        }
        $code = (int)substr($reply, 0, 3);
        if (!in_array($code, (array)$ok, true)) {
            $msg = $what !== '' ? $what : 'il server di posta ha risposto in modo inatteso';
            error_log('Fischietto SMTP: ' . $msg . ' (' . trim(substr($reply, 0, 200)) . ')');
            throw new RuntimeException($msg . ($code ? ' (codice ' . $code . ')' : ''));
        }
        return $reply;
    }
}

/**
 * Sends a batch of messages over a single connection.
 * $messages: list of ['to', 'name', 'subject', 'html', 'text'].
 * Returns one result per message: ['ok' => bool, 'error' => string].
 */
function send_mails(array $messages): array
{
    $cfg = mail_settings();
    if (!$cfg) return array_fill(0, count($messages), ['ok' => false, 'error' => 'invio email non configurato (manca lib/mail-config.php)']);
    $results = [];
    $client = new SmtpClient($cfg);
    try {
        $client->connect();
    } catch (Throwable $e) {
        return array_fill(0, count($messages), ['ok' => false, 'error' => $e->getMessage()]);
    }
    foreach ($messages as $m) {
        try {
            $client->send($m['to'], $m['name'] ?? '', $m['subject'], $m['html'], $m['text']);
            $results[] = ['ok' => true, 'error' => ''];
        } catch (Throwable $e) {
            $results[] = ['ok' => false, 'error' => $e->getMessage()];
            // After a failure the session may be in an unknown state: start a fresh one
            $client->close();
            $client = new SmtpClient($cfg);
            try { $client->connect(); } catch (Throwable $e2) {
                while (count($results) < count($messages)) $results[] = ['ok' => false, 'error' => $e2->getMessage()];
                return $results;
            }
        }
    }
    $client->close();
    return $results;
}

function send_mail(string $to, string $name, string $subject, string $html, string $text): array
{
    return send_mails([['to' => $to, 'name' => $name, 'subject' => $subject, 'html' => $html, 'text' => $text]])[0];
}
