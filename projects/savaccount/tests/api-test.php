<?php
/**
 * SavAccount — end-to-end test of the API, without the gateway.
 * Only on a throwaway database:  SAVACCOUNT_TEST_URL=http://127.0.0.1:8461/api/index.php SAVACCOUNT_CREDENTIALS=… SAVACCOUNT_MAIL_DUMP=… php tests/api-test.php
 * Refuses to run when the database name does not end with "_test".
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit;
require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/totp.php';
if (!credentials() || !cfg('debug') || substr((string)cfg('name'), -5) !== '_test') exit("Only on a throwaway database (…_test, debug on).\n");
$url = (string)getenv('SAVACCOUNT_TEST_URL');
$dump = (string)getenv('SAVACCOUNT_MAIL_DUMP');

$pass = $failed = 0;
function check(string $what, bool $ok, $info = null): void
{
    global $pass, $failed;
    $ok ? $pass++ : $failed++;
    if (!$ok) echo "FAIL  $what  " . json_encode($info) . "\n";
}

/** One browser: keeps its session like the gateway's cookie does. */
final class Browser
{
    public string $token = '';
    public string $csrf = '';
    public string $ip;
    public array $headers = [];
    public string $ua;

    public function __construct(string $ip = '192.0.2.10', string $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X) Firefox/140.0')
    {
        $this->ip = $ip;
        $this->ua = $ua;
    }

    public function call(string $r, ?array $body = null, array $opt = []): array
    {
        global $url;
        $h = ['X-Sav-Client-IP: ' . $this->ip, 'X-Sav-User-Agent: ' . $this->ua];
        if (empty($opt['nokey'])) $h[] = 'X-Sav-Gateway: ' . cfg('gateway');
        if ($this->token !== '') $h[] = 'X-Sav-Token: ' . $this->token;
        if ($this->csrf !== '' && empty($opt['nocsrf'])) $h[] = 'X-CSRF-Token: ' . $this->csrf;
        if (isset($opt['auth'])) $h[] = 'X-Sav-Authorization: ' . $opt['auth'];
        $h[] = 'Content-Type: ' . (isset($opt['form']) ? 'application/x-www-form-urlencoded' : 'application/json');
        $this->headers = [];
        $ch = curl_init("$url?r=$r");
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => $h, CURLOPT_HEADERFUNCTION => function ($ch, $l) {
            $p = explode(':', $l, 2);
            if (count($p) === 2) $this->headers[strtolower($p[0])] = trim($p[1]);
            return strlen($l);
        }]);
        if ($body !== null || isset($opt['form'])) curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => isset($opt['form']) ? http_build_query($opt['form']) : json_encode((object)$body)]);
        $out = (string)curl_exec($ch);
        if (preg_match('/^([^;]*);/', $this->headers['x-sav-set-token'] ?? '', $m)) $this->token = $m[1];
        $d = json_decode($out, true) ?: ['raw' => $out];
        if (!empty($d['csrf'])) $this->csrf = $d['csrf'];
        return $d + ['http' => (int)curl_getinfo($ch, CURLINFO_HTTP_CODE)];
    }
}

function mails(): array
{
    global $dump;
    usleep(150000);
    return array_map(fn($l) => json_decode($l, true), is_file($dump) ? array_filter(explode("\n", file_get_contents($dump))) : []);
}

function last_mail(string $to): array
{
    foreach (array_reverse(mails()) as $m) if ($m['to'] === $to) return $m;
    return ['subject' => '', 'text' => ''];
}

function code_to(string $to): string
{
    return preg_match('/\b(\d{6})\b/', last_mail($to)['subject'], $m) ? $m[1] : '';
}

function link_to(string $to): string
{
    return preg_match('/#t=([A-Za-z0-9_-]{43})/', last_mail($to)['text'], $m) ? $m[1] : '';
}

// ---------- empty tables
@unlink($dump);
db();
db()->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach (['users', 'emails', 'sessions', 'challenges', 'recovery_codes', 'attempts', 'events', 'clients', 'grants', 'codes', 'tokens', 'signing_keys'] as $x) db()->exec('TRUNCATE ' . t($x));
db()->exec('SET FOREIGN_KEY_CHECKS = 1');
$limits = fn() => db()->exec('TRUNCATE ' . t('attempts'));
$pw = 'Correct-horse-battery-9';

// ---------- gateway
$b = new Browser();
check('no gateway key', $b->call('auth.me', null, ['nokey' => true])['http'] === 403);
check('health is open', $b->call('health', null, ['nokey' => true])['http'] === 200);
check('unknown action', $b->call('auth.nothing', [])['http'] === 404);
check('GET on a POST action', $b->call('auth.login')['http'] === 405);

// ---------- registration
$reg = ['firstName' => 'Ada', 'lastName' => "D'Angelo-Rossi", 'email' => 'Ada@Example.test', 'password' => $pw, 'terms' => true, 'locale' => 'it'];
foreach ([['password' => 'short', 'e' => 'password_short'], ['password' => 'aaaaaaaaaaaa', 'e' => 'password_weak'], ['password' => 'password123', 'e' => 'password_weak'], ['email' => 'nope', 'e' => 'email_invalid'],
    ['firstName' => 'www.evil.test', 'e' => 'name_invalid'], ['firstName' => '<b>x</b>', 'e' => 'name_invalid'], ['lastName' => '', 'e' => 'name_invalid'], ['terms' => false, 'e' => 'terms_required']] as $bad) {
    check('register refuses ' . $bad['e'], $b->call('auth.register', $bad + $reg)['error'] === $bad['e'], $bad);
}
$limits();
$r = $b->call('auth.register', $reg);
check('register', $r['http'] === 200 && strlen($r['challenge']) === 43, $r);
$ch = $r['challenge'];
check('not signed in yet', $b->call('auth.me')['user'] === null);
check('wrong code', $b->call('auth.verify', ['challenge' => $ch, 'code' => code_to('ada@example.test') === '000000' ? '000001' : '000000'])['error'] === 'code_wrong');
$r = $b->call('auth.verify', ['challenge' => $ch, 'code' => code_to('ada@example.test')]);
check('verify signs in', ($r['user']['firstName'] ?? '') === 'Ada' && $b->token !== '', $r);
check('code is single use', $b->call('auth.verify', ['challenge' => $ch, 'code' => '123456'])['http'] === 410);
check('the password is stored as Argon2id', strpos((string)one('SELECT password_hash FROM ' . t('users'))['password_hash'], '$argon2id$') === 0);

$x = new Browser('192.0.2.20');
$n = count(mails());
$r = $x->call('auth.register', ['firstName' => 'Eve'] + $reg);
check('existing address: same answer', $r['http'] === 200 && strlen($r['challenge']) === 43);
check('existing address: owner is told', strpos(last_mail('ada@example.test')['subject'], 'Hai già') === 0 && count(mails()) === $n + 1);
check('existing address: never completes', $x->call('auth.verify', ['challenge' => $r['challenge'], 'code' => '123456'])['error'] === 'code_wrong');

// five wrong codes kill a challenge
$r = $x->call('auth.register', ['email' => 'bob@example.test'] + $reg);
for ($i = 0; $i < 5; $i++) $x->call('auth.verify', ['challenge' => $r['challenge'], 'code' => 'x']);
check('challenge dies after 5 attempts', $x->call('auth.verify', ['challenge' => $r['challenge'], 'code' => code_to('bob@example.test')])['http'] === 410);
check('resend too soon', $x->call('auth.resend', ['challenge' => $r['challenge']])['error'] === 'too_soon');

// link: the person who opens it must know the password
$r = $x->call('auth.register', ['email' => 'carl@example.test'] + $reg);
$l = link_to('carl@example.test');
check('link info', $x->call('auth.link.info', ['token' => $l])['kind'] === 'register');
check('link without the password', (new Browser('192.0.2.30'))->call('auth.link', ['token' => $l, 'password' => 'another-password-1'])['error'] === 'password_wrong');
$carl = new Browser('192.0.2.31');
check('link with the password', ($carl->call('auth.link', ['token' => $l, 'password' => $pw])['user']['email'] ?? '') === 'carl@example.test');
check('link is single use', $carl->call('auth.link', ['token' => $l, 'password' => $pw])['http'] === 410);

// two unconfirmed accounts on one address: the first to confirm wins
$limits();
$r1 = $x->call('auth.register', ['email' => 'dan@example.test', 'password' => 'Attacker-password-1'] + $reg);
$r2 = $x->call('auth.register', ['email' => 'dan@example.test'] + $reg);
$dan = new Browser('192.0.2.40');
check('second claim confirms', ($dan->call('auth.verify', ['challenge' => $r2['challenge'], 'code' => code_to('dan@example.test')])['user']['email'] ?? '') === 'dan@example.test');
check('the other claim is gone', $x->call('auth.verify', ['challenge' => $r1['challenge'], 'code' => '123456'])['http'] === 410);
check('its password does not work', (new Browser('192.0.2.41'))->call('auth.login', ['email' => 'dan@example.test', 'password' => 'Attacker-password-1'])['error'] === 'login_failed');

// ---------- signing in
$limits();
$l1 = (new Browser('192.0.2.50'))->call('auth.login', ['email' => 'ada@example.test', 'password' => 'wrong']);
$l2 = (new Browser('192.0.2.50'))->call('auth.login', ['email' => 'nobody@example.test', 'password' => 'wrong']);
check('same answer for unknown accounts', $l1 === $l2 && $l1['error'] === 'login_failed');
$x = new Browser('192.0.2.51');
for ($i = 0; $i < 10; $i++) $x->call('auth.login', ['email' => 'carl@example.test', 'password' => "wrong$i"]);
check('too many attempts', $x->call('auth.login', ['email' => 'carl@example.test', 'password' => $pw])['error'] === 'too_many');
$limits();
$r = $x->call('auth.register', ['email' => 'late@example.test'] + $reg);
check('unconfirmed account asks to confirm', isset($x->call('auth.login', ['email' => 'late@example.test', 'password' => $pw])['verify']['challenge']));
$n = count(mails());
$phone = new Browser('198.51.100.7', 'Mozilla/5.0 (iPhone) Safari/605');
check('new device signs in', isset($phone->call('auth.login', ['email' => 'ADA@example.test', 'password' => $pw, 'remember' => true])['user']));
check('new device email', strpos(last_mail('ada@example.test')['subject'], 'Nuovo accesso') === 0);
check('remember lasts 30 days', strpos($phone->headers['x-sav-set-token'], ';2592000') > 0);

// ---------- CSRF, sudo
check('CSRF token needed', $b->call('profile.save', $reg, ['nocsrf' => true])['error'] === 'csrf');
$good = $b->csrf;
$b->csrf = $phone->csrf;
check('CSRF of another session', $b->call('profile.save', $reg)['error'] === 'csrf');
$b->csrf = $good;
$r = $b->call('profile.save', ['firstName' => 'Ada', 'lastName' => 'Lovelace', 'nickname' => 'ada_l', 'birthdate' => '1990-12-10', 'locale' => 'it']);
check('profile', $r['user']['lastName'] === 'Lovelace', $r);
check('bad birth date', $b->call('profile.save', ['firstName' => 'Ada', 'lastName' => 'L', 'birthdate' => '2090-01-01'])['error'] === 'birthdate_invalid');
q('UPDATE ' . t('sessions') . ' SET sudo_until = NULL');
foreach (['emails.add', 'password.change', '2fa.totp.start', 'account.delete', 'account.export', '2fa.recovery'] as $a) check("$a needs sudo", $b->call($a, ['email' => 'z@example.test'])['error'] === 'sudo_required');
check('sudo wrong password', $b->call('auth.sudo', ['password' => 'x'])['error'] === 'password_wrong');
check('sudo', $b->call('auth.sudo', ['password' => $pw])['ok'] === true);

// ---------- more addresses
$r = $b->call('emails.add', ['email' => 'ada2@example.test']);
check('add address', isset($r['challenge']), $r);
check('not usable before confirming', (new Browser('192.0.2.60'))->call('auth.login', ['email' => 'ada2@example.test', 'password' => $pw])['error'] === 'login_failed');
check('cannot be primary yet', $b->call('emails.primary', ['id' => $r['id']])['error'] === 'email_unverified');
check('another account cannot confirm it', $carl->call('emails.verify', ['challenge' => $r['challenge'], 'code' => code_to('ada2@example.test')])['http'] === 410);
check('confirm address', $b->call('emails.verify', ['challenge' => $r['challenge'], 'code' => code_to('ada2@example.test')])['ok'] === true);
$ada2 = $r['id'];
check('sign in with the second address', isset((new Browser('192.0.2.61'))->call('auth.login', ['email' => 'ada2@example.test', 'password' => $pw])['user']));
$r = $b->call('emails.add', ['email' => 'carl@example.test']);
check('address of another account: same answer', isset($r['challenge']));
check('address of another account: its owner is told', strpos(last_mail('carl@example.test')['subject'], 'già associato') > 0);
check('address of another account: refused', $b->call('emails.verify', ['challenge' => $r['challenge'], 'code' => '000000'])['error'] === 'code_wrong');
check('make primary', $b->call('emails.primary', ['id' => $ada2])['ok'] === true);
$emails = $b->call('emails.list')['emails'];
check('primary changed', $emails[0]['email'] === 'ada2@example.test' && $emails[0]['primary']);
check('primary cannot be removed', $b->call('emails.remove', ['id' => $ada2])['error'] === 'email_primary');
$carl->call('auth.sudo', ['password' => $pw]);
check('cannot touch the address of others', $carl->call('emails.remove', ['id' => $ada2])['http'] === 404);
$b->call('emails.primary', ['id' => $emails[1]['id']]);

// ---------- authenticator app
$r = $b->call('2fa.totp.start', []);
$abc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
$bits = '';
foreach (str_split($r['secret']) as $c) $bits .= str_pad(decbin(strpos($abc, $c)), 5, '0', STR_PAD_LEFT);
$secret = '';
foreach (str_split(substr($bits, 0, 160), 8) as $o) $secret .= chr(bindec($o));
check('RFC 6238 vector', totp_at('12345678901234567890', intdiv(1111111109, 30)) === '081804');
$valid = array_map(fn($i) => totp_at($secret, intdiv(time(), 30) + $i), [-1, 0, 1, 2]);
$wrong = current(array_diff(['000000', '111111', '222222', '333333', '444444'], $valid)); // surely not a valid one
check('wrong app code', $b->call('2fa.totp.enable', ['code' => $wrong])['error'] === 'code_wrong');
$r = $b->call('2fa.totp.enable', ['code' => totp_at($secret, intdiv(time(), 30))]);
check('app turned on with recovery codes', count($r['recoveryCodes'] ?? []) === 10, $r);
$recovery = $r['recoveryCodes'];
check('secret is encrypted', strpos((string)one('SELECT totp_secret FROM ' . t('users') . ' WHERE id = 1')['totp_secret'], $secret) === false);
$x = new Browser('192.0.2.70');
$r = $x->call('auth.login', ['email' => 'ada@example.test', 'password' => $pw]);
check('second step asked', ($r['mfa']['methods'] ?? null) === ['totp', 'recovery'] && $x->token === '', $r);
check('no session before the second step', $x->call('auth.me')['user'] === null);
check('a used app code does not work again', $x->call('auth.mfa', ['challenge' => $r['mfa']['challenge'], 'method' => 'totp', 'code' => totp_at($secret, intdiv(time(), 30))])['error'] === 'code_wrong');
check('email step is off', $x->call('auth.mfa', ['challenge' => $r['mfa']['challenge'], 'method' => 'email', 'code' => '123456'])['http'] === 400);
check('resend refused when the email step is off', $x->call('auth.resend', ['challenge' => $r['mfa']['challenge']])['http'] === 400);
$next = totp_at($secret, intdiv(time(), 30) + 1);
$r2 = $x->call('auth.mfa', ['challenge' => $r['mfa']['challenge'], 'method' => 'totp', 'code' => $next]);
check('app code signs in', ($r2['user']['mfa'] ?? false) === true, $r2);
$y = new Browser('192.0.2.71');
$r = $y->call('auth.login', ['email' => 'ada@example.test', 'password' => $pw]);
check('recovery code', isset($y->call('auth.mfa', ['challenge' => $r['mfa']['challenge'], 'method' => 'recovery', 'code' => strtolower($recovery[0])])['user']));
$y = new Browser('192.0.2.72');
$r = $y->call('auth.login', ['email' => 'ada@example.test', 'password' => $pw]);
check('recovery code works once', $y->call('auth.mfa', ['challenge' => $r['mfa']['challenge'], 'method' => 'recovery', 'code' => $recovery[0]])['error'] === 'code_wrong');

// ---------- email step
$limits();
$r = $b->call('2fa.email.start', []);
check('email step on', $b->call('2fa.email.enable', ['challenge' => $r['challenge'], 'code' => code_to('ada@example.test')])['recoveryCodes'] === null);
$y = new Browser('192.0.2.73');
$r = $y->call('auth.login', ['email' => 'ada@example.test', 'password' => $pw]);
check('both methods', $r['mfa']['methods'] === ['totp', 'email', 'recovery']);
$y->call('auth.resend', ['challenge' => $r['mfa']['challenge']]);
check('email code signs in', isset($y->call('auth.mfa', ['challenge' => $r['mfa']['challenge'], 'method' => 'email', 'code' => code_to('ada@example.test')])['user']));
check('sudo asks for the second step', isset($y->call('auth.sudo', ['password' => $pw])['mfa']));
check('a sudo challenge of another session', (function () use ($y, $b, $recovery, $pw) {
    $r = $y->call('auth.sudo', ['password' => $pw]);
    return $b->call('auth.mfa', ['challenge' => $r['mfa']['challenge'], 'method' => 'recovery', 'code' => $recovery[1]])['http'] === 403;
})());

// ---------- new password
$limits();
$f = new Browser('192.0.2.80');
$n = count(mails());
$r = $f->call('auth.forgot', ['email' => 'nobody@example.test']);
check('forgot: unknown address, same answer, no email', isset($r['challenge']) && count(mails()) === $n);
$r = $f->call('auth.forgot', ['email' => 'carl@example.test']);
$l = link_to('carl@example.test');
check('reset weak password keeps the code', $f->call('auth.reset', ['challenge' => $r['challenge'], 'code' => code_to('carl@example.test'), 'password' => 'x'])['error'] === 'password_short');
$pw2 = 'Brand-new-secret-77';
check('reset', $f->call('auth.reset', ['challenge' => $r['challenge'], 'code' => code_to('carl@example.test'), 'password' => $pw2])['ok'] === true);
check('the link died with the code', $f->call('auth.reset', ['token' => $l, 'password' => $pw2])['http'] === 410);
check('reset closes the sessions', $carl->call('auth.me')['user'] === null);
check('old password refused', $f->call('auth.login', ['email' => 'carl@example.test', 'password' => $pw])['error'] === 'login_failed');
check('new password works', isset($carl->call('auth.login', ['email' => 'carl@example.test', 'password' => $pw2])['user']));
$f->call('auth.forgot', ['email' => 'dan@example.test']);
check('reset with the link', $f->call('auth.reset', ['token' => link_to('dan@example.test'), 'password' => $pw2])['ok'] === true);
$r = $f->call('auth.forgot', ['email' => 'ada@example.test']);
$f->call('auth.reset', ['challenge' => $r['challenge'], 'code' => code_to('ada@example.test'), 'password' => $pw]);
check('reset does not skip the second step', isset($f->call('auth.login', ['email' => 'ada@example.test', 'password' => $pw])['mfa']));

// ---------- administrators
$limits();
$adm = new Browser('192.0.2.90');
check('not an admin', $carl->call('admin.clients')['http'] === 403);
$r = $adm->call('auth.register', ['email' => 'admin@example.test', 'firstName' => 'Root'] + $reg);
check('an unconfirmed admin address gives nothing', $adm->call('admin.clients')['http'] === 401);
$r = $adm->call('auth.verify', ['challenge' => $r['challenge'], 'code' => code_to('admin@example.test')]);
check('admin', $r['user']['admin'] === true);
check('admin needs the second step', $adm->call('admin.clients')['error'] === 'mfa_required');
$r = $adm->call('2fa.email.start', []);
$adm->call('2fa.email.enable', ['challenge' => $r['challenge'], 'code' => code_to('admin@example.test')]);
check('admin panel', $adm->call('admin.clients')['ok'] === true);
$svc = ['name' => 'Demo', 'description' => 'A service', 'homepage' => 'https://demo.example', 'redirectUris' => ['https://demo.example/callback'], 'logoutUris' => ['https://demo.example/bye'],
    'type' => 'confidential', 'required' => ['profile', 'email'], 'optional' => ['birthdate', 'offline_access']];
foreach (['http://demo.example/cb', 'https://demo.example/*', 'https://demo.example/cb#x', 'javascript:alert(1)', 'com.example.app:/cb'] as $u) check("redirect refused: $u", $adm->call('admin.client.save', ['redirectUris' => [$u]] + $svc)['error'] === 'url_invalid');
check('logo must be https', $adm->call('admin.client.save', ['logo' => 'javascript:alert(1)'] + $svc)['error'] === 'url_invalid');
$r = $adm->call('admin.client.save', $svc);
$cid = $r['client']['clientId'];
$sec = $r['secret'];
$svcId = $r['client']['id'];
check('service created', strlen($cid) === 24 && strlen($sec) === 43, $r);
check('others cannot create services', $carl->call('admin.client.save', $svc)['http'] === 403);
check('the secret is not shown again', !isset($adm->call('admin.client.save', ['id' => $svcId] + $svc)['secret']));
$pub = $adm->call('admin.client.save', ['name' => 'Mobile', 'type' => 'public', 'redirectUris' => ['com.example.app:/cb', 'http://127.0.0.1/cb']] + $svc);
check('public service without secret', $pub['secret'] === null, $pub);

// ---------- authorization
$verifier = b64u(random_bytes(40));
$pkce = b64u(hash('sha256', $verifier, true));
$q = ['client_id' => $cid, 'redirect_uri' => 'https://demo.example/callback', 'response_type' => 'code', 'scope' => 'openid profile email birthdate offline_access', 'state' => 'st4te', 'nonce' => 'n0nce', 'code_challenge' => $pkce, 'code_challenge_method' => 'S256'];
$au = fn(Browser $b, array $over = [], array $more = []) => $b->call('oauth.check', ['request' => array_filter($over + $q, fn($v) => $v !== null)] + $more);
$anon = new Browser('192.0.2.95');
check('unknown service', $au($anon, ['client_id' => str_repeat('a', 24)])['error'] === 'client_unknown');
foreach (['https://demo.example/callback/', 'https://demo.example/callback?x=1', 'https://evil.example/callback', 'https://demo.example/callback/../x'] as $u) check("redirect mismatch: $u", $au($anon, ['redirect_uri' => $u])['error'] === 'redirect_invalid');
check('PKCE is required', strpos($au($anon, ['code_challenge' => null])['url'] ?? '', 'error=invalid_request') > 0);
check('plain PKCE refused', strpos($au($anon, ['code_challenge_method' => 'plain'])['url'] ?? '', 'error=invalid_request') > 0);
check('implicit flow refused', strpos($au($anon, ['response_type' => 'token'])['url'] ?? '', 'unsupported_response_type') > 0);
check('openid is required', strpos($au($anon, ['scope' => 'email'])['url'] ?? '', 'invalid_scope') > 0);
check('login needed', $au($anon)['status'] === 'login');
check('prompt=none without session', strpos($au($anon, ['prompt' => 'none'])['url'], 'error=login_required') > 0);
check('errors carry state and issuer', strpos($au($anon, ['prompt' => 'none'])['url'], 'state=st4te') > 0 && strpos($au($anon, ['prompt' => 'none'])['url'], 'iss=') > 0);
$r = $au($carl);
check('consent needed', $r['status'] === 'consent' && count($r['scopes']) === 5, $r);
check('approve needs CSRF', $carl->call('oauth.approve', ['request' => $q, 'scopes' => [], 'emailId' => $r['emailId']], ['nocsrf' => true])['error'] === 'csrf');
check('approve without a choice', $carl->call('oauth.approve', ['request' => $q])['error'] === 'reauth');
check('approve with the address of others', $carl->call('oauth.approve', ['request' => $q, 'scopes' => [], 'emailId' => $ada2])['error'] === 'bad_request');
check('deny', strpos($carl->call('oauth.deny', ['request' => $q])['url'], 'error=access_denied') > 0);
$approve = function (Browser $b, array $req, array $scopes) use ($au): string {
    $c = $b->call('oauth.check', ['request' => $req]);
    $r = $b->call('oauth.approve', ['request' => $req] + ($c['status'] === 'consent' ? ['scopes' => $scopes, 'emailId' => $c['emailId']] : []));
    parse_str((string)parse_url($r['url'] ?? '', PHP_URL_QUERY), $p);
    return $p['code'] ?? '';
};
$code = $approve($carl, $q, ['offline_access']); // the birth date is not shared
check('code issued', strlen($code) === 43);
check('second time: ready, nothing to ask', $au($carl)['status'] === 'ready');

// ---------- tokens
$basic = 'Basic ' . base64_encode("$cid:$sec");
$tok = fn(array $form, ?string $auth) => (new Browser('203.0.113.5'))->call('oidc.token', null, ['form' => $form] + ($auth ? ['auth' => $auth] : []));
$ex = ['grant_type' => 'authorization_code', 'code' => $code, 'redirect_uri' => $q['redirect_uri'], 'code_verifier' => $verifier];
check('wrong secret', $tok($ex, 'Basic ' . base64_encode("$cid:nope"))['http'] === 401);
check('no secret', $tok($ex + ['client_id' => $cid], null)['error'] === 'invalid_client');
check('code of another service', $tok($ex + ['client_id' => $pub['client']['clientId']], null)['error'] === 'invalid_grant');
$t1 = $tok($ex, $basic);
check('tokens', isset($t1['access_token'], $t1['id_token'], $t1['refresh_token']) && $t1['scope'] === 'openid profile email offline_access', $t1);
[$h64, $p64, $s64] = explode('.', $t1['id_token']);
$dec = fn($x) => base64_decode(strtr($x, '-_', '+/'));
$claims = json_decode($dec($p64), true);
$jwk = $b->call('oidc.jwks')['keys'][0];
check('key id matches', json_decode($dec($h64), true)['kid'] === $jwk['kid']);
$pem = one('SELECT public_pem FROM ' . t('signing_keys'))['public_pem'];
check('signature', openssl_verify("$h64.$p64", $dec($s64), $pem, OPENSSL_ALGO_SHA256) === 1);
check('JWK is that key', b64u(openssl_pkey_get_details(openssl_pkey_get_public($pem))['rsa']['n']) === $jwk['n'] && !isset($jwk['d']));
check('claims', $claims['iss'] === cfg('site') && $claims['aud'] === $cid && $claims['nonce'] === 'n0nce' && $claims['email'] === 'carl@example.test' && $claims['given_name'] === 'Ada' && !isset($claims['birthdate']), $claims);
check('at_hash', $claims['at_hash'] === b64u(substr(hash('sha256', $t1['access_token'], true), 0, 16)));
$ui = fn(string $a) => (new Browser())->call('oidc.userinfo', null, ['auth' => "Bearer $a"]);
check('userinfo', $ui($t1['access_token'])['sub'] === $claims['sub']);
check('userinfo, bad token', $ui(str_repeat('x', 43))['http'] === 401);
check('a refresh token is not an access token', $ui($t1['refresh_token'])['http'] === 401);
check('code reuse is refused', $tok($ex, $basic)['error'] === 'invalid_grant');
check('code reuse revokes the tokens', $ui($t1['access_token'])['http'] === 401);

$code = $approve($carl, $q, []);
check('wrong PKCE verifier', $tok(['code' => $code, 'code_verifier' => str_repeat('a', 43)] + $ex, $basic)['error'] === 'invalid_grant');
$code = $approve($carl, $q, []);
check('wrong redirect address', $tok(['code' => $code, 'redirect_uri' => 'https://demo.example/other'] + $ex, $basic)['error'] === 'invalid_grant');
$code = $approve($carl, $q, []);
q('UPDATE ' . t('codes') . ' SET expires_at = ? WHERE code_hash = ?', [now_utc(-1), token_hash($code)]);
check('expired code', $tok(['code' => $code] + $ex, $basic)['error'] === 'invalid_grant');
$t2 = $tok(['code' => $approve($carl, $q, [])] + $ex, $basic);
$rf = fn(string $r, ?string $auth = null, array $more = []) => $tok(['grant_type' => 'refresh_token', 'refresh_token' => $r] + $more, $auth);
$t3 = $rf($t2['refresh_token'], $basic);
check('refresh rotates', isset($t3['access_token']) && $t3['refresh_token'] !== $t2['refresh_token'], $t3);
check('refresh cannot widen', $rf($t3['refresh_token'], $basic, ['scope' => 'openid birthdate'])['error'] === 'invalid_scope');
$t2 = $tok(['code' => $approve($carl, $q, [])] + $ex, $basic);
$t3 = $rf($t2['refresh_token'], $basic);
check('reused refresh token refused', $rf($t2['refresh_token'], $basic)['error'] === 'invalid_grant');
check('and its family is revoked', $ui($t3['access_token'])['http'] === 401 && $rf($t3['refresh_token'], $basic)['error'] === 'invalid_grant');
check('client_secret_post', isset($tok(['code' => $approve($carl, $q, [])] + $ex + ['client_id' => $cid, 'client_secret' => $sec], null)['access_token']));

// ---------- public service (mobile app)
$qp = ['client_id' => $pub['client']['clientId'], 'redirect_uri' => 'http://127.0.0.1:53211/cb'] + $q;
check('loopback with any port', $au($carl, $qp)['status'] === 'consent');
check('loopback with another path', $au($carl, ['redirect_uri' => 'http://127.0.0.1:53211/evil'] + $qp)['error'] === 'redirect_invalid');
$code = $approve($carl, $qp, []);
$tp = $tok(['code' => $code, 'redirect_uri' => $qp['redirect_uri'], 'client_id' => $qp['client_id']] + $ex, null);
check('public exchange with PKCE only', isset($tp['access_token']), $tp);
$pc = json_decode($dec(explode('.', $tp['id_token'])[1]), true);
check('pairwise identifiers differ', $pc['sub'] !== $claims['sub']);

// ---------- what the user shares
$apps = $carl->call('apps.list')['apps'];
check('two connected services', count($apps) === 2);
$demo = array_values(array_filter($apps, fn($a) => $a['client']['name'] === 'Demo'))[0];
$ua = $tok(['code' => $approve($carl, $q, [])] + $ex, $basic);
check('share the birth date later', $carl->call('apps.update', ['id' => $demo['id'], 'scopes' => ['birthdate', 'offline_access'], 'emailId' => $demo['emailId']])['ok'] === true);
check('others cannot change it', $b->call('apps.update', ['id' => $demo['id'], 'scopes' => [], 'emailId' => 1])['http'] >= 400);
$carl->call('apps.update', ['id' => $demo['id'], 'scopes' => [], 'emailId' => $demo['emailId']]);
check('required data stays', $ui($ua['access_token'])['email'] === 'carl@example.test');
check('no more refresh once taken back', !isset($rf($ua['refresh_token'], $basic)['refresh_token']));
check('disconnect', $carl->call('apps.revoke', ['id' => $demo['id']])['ok'] === true);
check('tokens stop at once', $ui($ua['access_token'])['http'] === 401);
check('consent is asked again', $au($carl)['status'] === 'consent');

$ur = $tok(['code' => $approve($carl, $q, [])] + $ex, $basic);
$r = (new Browser('203.0.113.6'))->call('oidc.revoke', null, ['form' => ['token' => $ur['access_token']], 'auth' => $basic]);
check('revocation', $r['http'] === 200 && $ui($ur['access_token'])['http'] === 401);

// ---------- prompt and max_age, two-step services, switching a service off
check('fresh sign-in satisfies prompt=login', $au($carl, ['prompt' => 'login', 'state' => 'a'])['status'] !== 'login');
q('UPDATE ' . t('sessions') . ' SET auth_time = ?', [now_utc(-3600)]);
check('prompt=login asks again', $au($carl, ['prompt' => 'login'])['status'] === 'reauth');
check('max_age asks again', $au($carl, ['max_age' => '600'])['status'] === 'reauth');
check('approve refuses a stale sign-in', $carl->call('oauth.approve', ['request' => ['prompt' => 'login'] + $q])['error'] === 'reauth');
$adm->call('auth.me');
q('UPDATE ' . t('sessions') . ' SET sudo_until = ?', [now_utc(600)]);
$adm->call('admin.client.save', ['id' => $svcId, 'require2fa' => true] + $svc);
check('service that wants two steps', $au($carl)['status'] === 'mfa_setup');
$adm->call('admin.client.save', ['id' => $svcId] + $svc);
$carl->call('apps.revoke', ['id' => $carl->call('apps.list')['apps'][0]['id']]);
$demo = array_values(array_filter($carl->call('apps.list')['apps'], fn($a) => $a['client']['name'] === 'Demo'));
if ($demo) $carl->call('apps.revoke', ['id' => $demo[0]['id']]);
$uo = $tok(['code' => $approve($carl, $q, ['offline_access'])] + $ex, $basic);
check('service off', $adm->call('admin.client.status', ['id' => $svcId, 'active' => false])['client']['active'] === false);
check('off: no authorization', $au($carl)['error'] === 'client_disabled');
check('off: no tokens', $ui($uo['access_token'])['http'] === 401 && $rf($uo['refresh_token'], $basic)['http'] === 401);
$adm->call('admin.client.status', ['id' => $svcId, 'active' => true]);
$r = $adm->call('admin.client.secret', ['id' => $svcId]);
check('new secret replaces the old one', $tok($ex, $basic)['http'] === 401 && $tok($ex, 'Basic ' . base64_encode("$cid:{$r['secret']}"))['error'] === 'invalid_grant');
check('sign-out address must be registered', $carl->call('oauth.logoutCheck', ['request' => ['id_token_hint' => $t1['id_token'], 'post_logout_redirect_uri' => 'https://evil.example/']])['url'] === null);
check('sign-out address', $carl->call('oauth.logoutCheck', ['request' => ['id_token_hint' => $t1['id_token'], 'post_logout_redirect_uri' => 'https://demo.example/bye', 'state' => 's']])['url'] === 'https://demo.example/bye?state=s');
$forged = $h64 . '.' . b64u(json_encode(['aud' => $cid, 'iss' => cfg('site')])) . '.' . $s64;
check('forged hint ignored', $carl->call('oauth.logoutCheck', ['request' => ['id_token_hint' => $forged, 'post_logout_redirect_uri' => 'https://demo.example/bye']])['url'] === null);

// ---------- sessions, data, deletion, admin on users
$c2 = new Browser('192.0.2.99');
$c2->call('auth.login', ['email' => 'carl@example.test', 'password' => $pw2]);
$list = $carl->call('sessions.list')['sessions'];
check('sessions', count($list) === 2 && count(array_filter($list, fn($x) => $x['current'])) === 1, $list);
check('full address is not kept', $list[0]['ip'] === '192.0.2.x');
check('cannot end sessions of others', (function () use ($b, $c2, $list) {
    foreach ($list as $l) $b->call('sessions.revoke', ['id' => $l['id']]);
    return $c2->call('auth.me')['user'] !== null;
})());
$carl->call('sessions.revokeOthers', []);
check('end the other sessions', $c2->call('auth.me')['user'] === null && $carl->call('auth.me')['user'] !== null);
$r = $carl->call('account.export', []);
check('export without secrets', isset($r['data']['profile']) && !preg_match('/argon2|token_hash|csrf|totp_secret/', json_encode($r)));
// Ada's unconfirmed claim on Carl's address is found too, under her own primary address
$users = array_values(array_filter($adm->call('admin.users', ['search' => 'carl@'])['users'], fn($x) => $x['email'] === 'carl@example.test'));
check('admin finds a user', count($users) === 1);
$adm->call('admin.user.status', ['id' => $users[0]['id'], 'active' => false]);
check('disabled user is out', $carl->call('auth.me')['user'] === null && $c2->call('auth.login', ['email' => 'carl@example.test', 'password' => $pw2])['error'] === 'disabled');
$adm->call('admin.user.status', ['id' => $users[0]['id'], 'active' => true]);
$carl->call('auth.login', ['email' => 'carl@example.test', 'password' => $pw2]);
check('delete needs the password', $carl->call('account.delete', ['password' => 'x'])['error'] === 'password_wrong');
check('delete', $carl->call('account.delete', ['password' => $pw2])['ok'] === true);
check('nothing is left', (int)q('SELECT COUNT(*) FROM ' . t('emails') . " WHERE email = 'carl@example.test' AND verified_at IS NOT NULL")->fetchColumn() === 0 && (int)q('SELECT COUNT(*) FROM ' . t('grants'))->fetchColumn() === 0);
$out = $adm->token;
check('sign out', $adm->call('auth.logout', [])['ok'] === true);
$adm->token = $out;
check('the old session is dead', $adm->call('auth.me')['user'] === null);
check('no clear tokens or addresses in the rate limits', (int)q('SELECT COUNT(*) FROM ' . t('attempts') . " WHERE subject LIKE '%192.%' OR subject LIKE '%@%'")->fetchColumn() === 0);

echo "$pass passed, $failed failed\n";
exit($failed ? 1 : 0);
