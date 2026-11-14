<?php
/** Registration, sign-in and user management. */

declare(strict_types=1);

const LOGIN_WINDOW_MIN = 15;
const LOGIN_MAX_PER_MAIL = 8;
const LOGIN_MAX_PER_IP = 30;

function normalize_email(string $email): string
{
    return mb_strtolower(trim($email));
}

const PASSWORD_MIN_LENGTH = 10;

/**
 * Rules for new passwords: at least 10 characters and at least 3 kinds among
 * lowercase, uppercase, digits and symbols; must not contain the name or the email.
 */
function validate_password(string $pw, string $email = '', string $name = ''): void
{
    $len = mb_strlen($pw);
    if ($len < PASSWORD_MIN_LENGTH) fail('La password deve avere almeno ' . PASSWORD_MIN_LENGTH . ' caratteri.', 422, 'weak_password');
    if ($len > 200) fail('La password è troppo lunga.', 422, 'validation');
    $kinds = (int)preg_match('/\p{Ll}/u', $pw) + (int)preg_match('/\p{Lu}/u', $pw)
        + (int)preg_match('/\d/', $pw) + (int)preg_match('/[^\p{L}\d]/u', $pw);
    if ($kinds < 3) fail('Usa almeno tre tipi di caratteri tra minuscole, maiuscole, numeri e simboli.', 422, 'weak_password');
    $lower = mb_strtolower($pw);
    $local = mb_strtolower((string)strstr($email, '@', true));
    if (mb_strlen($local) >= 4 && mb_strpos($lower, $local) !== false) fail('La password non può contenere il tuo indirizzo email.', 422, 'weak_password');
    foreach (preg_split('/\s+/', mb_strtolower($name)) as $part) {
        if (mb_strlen($part) >= 4 && mb_strpos($lower, $part) !== false) fail('La password non può contenere il tuo nome.', 422, 'weak_password');
    }
    if (preg_match('/^(.)\1+$/u', $pw) || preg_match('/(password|qwerty|123456|fipav|arbitro|pallavolo)/i', $pw)) {
        fail('Password troppo facile da indovinare: scegline una meno prevedibile.', 422, 'weak_password');
    }
}

function find_user_by_email(string $email): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
    $st->execute([normalize_email($email)]);
    $u = $st->fetch();
    return $u ?: null;
}

function find_user(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    $u = $st->fetch();
    return $u ?: null;
}

function register_user(array $in): array
{
    $name = str_param($in, 'name', 100, true);
    if (mb_strlen($name) < 2) fail('Inserisci nome e cognome.', 422, 'validation');
    $email = normalize_email(str_param($in, 'email', 190, true));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Indirizzo email non valido.', 422, 'validation');
    $password = (string)($in['password'] ?? '');
    validate_password($password, $email, $name);

    // Anti-abuse limit: at most 5 registrations per hour from the same IP
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('login_attempts') . " WHERE ip = ? AND email = '#register' AND created_at > (UTC_TIMESTAMP() - INTERVAL 1 HOUR)");
    $st->execute([client_ip()]);
    if ((int)$st->fetchColumn() >= 5) fail('Troppe registrazioni da questa rete. Riprova più tardi.', 429, 'rate_limited');
    db()->prepare('INSERT INTO ' . t('login_attempts') . " (ip, email, success, created_at) VALUES (?, '#register', 1, ?)")->execute([client_ip(), now_utc()]);

    // An address already registered gets the same answer as a new one, so nobody can find out who has an account.
    // Whoever never confirmed it receives the link again (at most every 10 minutes); the others just sign in
    $existing = find_user_by_email($email);
    if ($existing) {
        password_hash($password, PASSWORD_DEFAULT); // the same time as a new registration
        if (must_verify($existing)) send_verification($existing, 10);
        return ['name' => $name, 'email' => $email];
    }

    db()->prepare('INSERT INTO ' . t('users') . " (name, email, password_hash, permissions, status, created_at) VALUES (?,?,?,?, 'pending', ?)")
        ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), PERM_NONE, now_utc()]);

    send_verification(find_user_by_email($email));
    notify_admin_new_user($name, $email);
    return ['name' => $name, 'email' => $email];
}

function notify_admin_new_user(string $name, string $email): void
{
    $cfg = config();
    $to = trim((string)($cfg['notify_email'] ?? ''));
    if ($to === '') return;
    if (mail_enabled()) {
        $m = render_registration_notice($name, $email, account_email_links());
        send_mail($to, '', $m['subject'], $m['html'], $m['text']);
        return;
    }
    // Fallback without the SMTP mailbox: PHP mail()
    $from = trim((string)($cfg['mail_from'] ?? ''));
    if ($from === '') return;
    $url = trim((string)($cfg['public_url'] ?? ''));
    $subject = '=?UTF-8?B?' . base64_encode('Fischietto: nuova registrazione da abilitare') . '?=';
    $body = "Nuovo utente in attesa di abilitazione:\n\nNome: $name\nEmail: $email\n"
        . ($url !== '' ? "\nGestisci gli utenti: " . rtrim($url, '/') . "/admin.html#utenti\n" : '');
    $headers = "From: $from\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    @mail($to, $subject, $body, $headers, '-f' . $from);
}

function login_user(array $in): array
{
    $email = normalize_email(str_param($in, 'email', 190, true));
    $password = (string)($in['password'] ?? '');
    $ip = client_ip();
    $emailKey = hash('sha256', $email); // The rate limit only has to recognise the address, not read it
    $pdo = db();
    ensure_schema();

    $st = $pdo->prepare('SELECT
        SUM(CASE WHEN email = ? THEN 1 ELSE 0 END) AS by_mail,
        COUNT(*) AS by_ip
        FROM ' . t('login_attempts') . ' WHERE success = 0 AND ip = ? AND created_at > (UTC_TIMESTAMP() - INTERVAL ' . LOGIN_WINDOW_MIN . ' MINUTE)');
    $st->execute([$emailKey, $ip]);
    $c = $st->fetch();
    $st2 = $pdo->prepare('SELECT COUNT(*) FROM ' . t('login_attempts') . ' WHERE success = 0 AND email = ? AND created_at > (UTC_TIMESTAMP() - INTERVAL ' . LOGIN_WINDOW_MIN . ' MINUTE)');
    $st2->execute([$emailKey]);
    if ((int)$st2->fetchColumn() >= LOGIN_MAX_PER_MAIL || (int)$c['by_ip'] >= LOGIN_MAX_PER_IP) {
        fail('Troppi tentativi falliti. Riprova tra ' . LOGIN_WINDOW_MIN . ' minuti.', 429, 'rate_limited');
    }

    $user = find_user_by_email($email);
    // An unknown address costs the same time as a wrong password: the response time does not reveal who has an account
    if ($user) $ok = password_verify($password, $user['password_hash']);
    else $ok = password_hash($password, PASSWORD_DEFAULT) === ''; // as slow as a check, always false
    if (!$ok) {
        $pdo->prepare('INSERT INTO ' . t('login_attempts') . ' (ip, email, success, created_at) VALUES (?,?,0,?)')->execute([$ip, $emailKey, now_utc()]);
        fail('Email o password non corretti.', 401, 'bad_credentials');
    }

    // The address comes first: the link is sent again at most every 10 minutes
    if (must_verify($user)) {
        send_verification($user, 10);
        fail('Conferma la tua email: ti abbiamo inviato un link a ' . $user['email'] . '. Aprilo, poi accedi di nuovo. Controlla anche lo spam.', 403, 'email_unverified');
    }
    if ($user['status'] === 'pending') fail('Il tuo account è in attesa di abilitazione da parte di un amministratore.', 403, 'pending');
    if ($user['status'] === 'disabled') fail('Il tuo account è stato disabilitato. Contatta l\'amministratore.', 403, 'disabled');
    if ((int)$user['permissions'] < PERM_USER) fail('Il tuo account non ha ancora i permessi di accesso.', 403, 'pending');

    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        $pdo->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
    }
    $pdo->prepare('UPDATE ' . t('users') . ' SET last_login_at = ?, last_seen_at = ? WHERE id = ?')->execute([now_utc(), now_utc(), $user['id']]);
    $sess = create_auth_session((int)$user['id']);
    return ['user' => public_user($user), 'csrf' => $sess['csrf']];
}

// ---------------- Email check ----------------

const VERIFY_HOURS = 48;

/** A new single-use link (the older ones stop working). $minutes: skip if one was sent more recently than this. */
function send_verification(?array $user, int $minutes = 0): void
{
    if (!$user || !mail_enabled()) return;
    if ($minutes > 0) {
        $st = db()->prepare('SELECT COUNT(*) FROM ' . t('email_tokens') . ' WHERE user_id = ? AND created_at > (UTC_TIMESTAMP() - INTERVAL ' . (int)$minutes . ' MINUTE)');
        $st->execute([$user['id']]);
        if ((int)$st->fetchColumn() > 0) return;
    }
    $token = bin2hex(random_bytes(32));
    db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ?')->execute([$user['id']]);
    db()->prepare('INSERT INTO ' . t('email_tokens') . ' (token_hash, user_id, created_at, expires_at) VALUES (?,?,?,?)')
        ->execute([hash('sha256', $token), $user['id'], now_utc(), gmdate('Y-m-d H:i:s', time() + VERIFY_HOURS * 3600)]);
    $links = account_email_links();
    $url = $links['portal_url'] . '?verify=' . $token;
    $first = trim(explode(' ', trim((string)$user['name']))[0]) ?: (string)$user['name'];
    $html = email_layout('<h1 style="margin:0 0 14px;font-size:22px;color:#13203A">Conferma la tua email</h1>'
        . '<p style="margin:0 0 18px;font-size:15px;line-height:1.65;color:#4B5B78">Ciao ' . email_esc($first) . ', conferma che questo indirizzo è tuo per usare il tuo account Fischietto.</p>'
        . email_button('Conferma la mia email', $url)
        . '<p style="margin:18px 0 0;font-size:13px;line-height:1.6;color:#4B5B78">Il link vale 48 ore. Se non hai creato un account, puoi ignorare questa email.</p>',
        ['preheader' => 'Conferma la tua email per Fischietto', 'icon_url' => $links['icon_url'], 'home_url' => $links['portal_url'], 'credit_url' => $links['credit_url']]);
    $text = "Ciao $first,\n\nconferma che questo indirizzo è tuo per usare il tuo account Fischietto:\n$url\n\nIl link vale 48 ore. Se non hai creato un account, puoi ignorare questa email.\n";
    $r = send_mail((string)$user['email'], (string)$user['name'], 'Conferma la tua email per Fischietto', $html, $text);
    if (empty($r['sent']) && empty($r['ok'])) error_log('[fischietto] verification email not sent to user #' . $user['id']);
}

/** The link of the email, confirmed by a button in the portal (mail scanners that open links change nothing). */
function verify_email_token(string $token): array
{
    $st = db()->prepare('SELECT user_id FROM ' . t('email_tokens') . ' WHERE token_hash = ? AND expires_at > ?');
    $st->execute([hash('sha256', $token), now_utc()]);
    $uid = (int)$st->fetchColumn();
    if (!$uid) fail('Questo link è scaduto o è già stato usato: accedi per riceverne uno nuovo.', 410, 'token_invalid');
    db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')->execute([now_utc(), $uid]);
    db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ?')->execute([$uid]);
    return find_user($uid);
}

function change_own_password(array $session, array $in): array
{
    $current = (string)($in['current'] ?? '');
    $new = (string)($in['new'] ?? '');
    $u = find_user($session['id']);
    if (!$u || !password_verify($current, $u['password_hash'])) fail('La password attuale non è corretta.', 422, 'bad_current');
    if (hash_equals($current, $new)) fail('La nuova password deve essere diversa da quella attuale.', 422, 'weak_password');
    validate_password($new, $u['email'], $u['name']);
    db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ?, must_change_password = 0 WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $u['id']]);
    // Close the other open sessions, keep the current one
    $token = (string)($_COOKIE[AUTH_COOKIE] ?? '');
    db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ? AND token_hash <> ?')->execute([$u['id'], hash('sha256', $token)]);
    return find_user($u['id']);
}

// ---------------- Administration ----------------

/**
 * Who can manage whom:
 *  - the master admin (10) manages everyone except themselves;
 *  - an administrator (9) only manages users and moderators (level < 9), never other administrators or the master admin.
 */
function can_manage_user(array $actor, array $target): bool
{
    if ((int)$target['id'] === (int)$actor['id']) return false;
    $a = (int)$actor['permissions'];
    $t = (int)$target['permissions'];
    if ($t >= PERM_MASTER) return false;
    if ($a >= PERM_MASTER) return true;
    return $a >= PERM_ADMIN && $t < PERM_ADMIN;
}

function require_manageable(array $actor, array $target): void
{
    if ((int)$target['id'] === (int)$actor['id']) fail('Non puoi modificare il tuo account da qui: usa il menu del tuo profilo.', 422, 'self_lockout');
    if ((int)$target['permissions'] >= PERM_MASTER) fail('Il master admin non può essere modificato.', 403, 'forbidden');
    if (!can_manage_user($actor, $target)) fail('Solo il master admin può gestire gli altri amministratori.', 403, 'forbidden');
}

function admin_list_users(array $actor): array
{
    $answers = [];
    foreach (db()->query('SELECT user_id, value FROM ' . t('user_state') . " WHERE state_key = 'data'") as $r) {
        $d = json_decode($r['value'], true);
        if (is_array($d) && isset($d['stats'])) {
            $answers[(int)$r['user_id']] = (int)($d['stats']['correct'] ?? 0) + (int)($d['stats']['wrong'] ?? 0);
        }
    }
    $out = [];
    $rows = db()->query('SELECT id, name, email, permissions, status, must_change_password, email_verified_at, created_at, approved_at, activation_mail_at, last_login_at, last_seen_at FROM ' . t('users') . " ORDER BY FIELD(status,'pending','active','disabled'), permissions DESC, created_at DESC");
    foreach ($rows as $u) {
        $u['id'] = (int)$u['id'];
        $u['permissions'] = (int)$u['permissions'];
        $u['must_change_password'] = (bool)$u['must_change_password'];
        $u['verified'] = !empty($u['email_verified_at']);
        unset($u['email_verified_at']);
        $u['role'] = role_label($u['permissions']);
        $u['answers'] = $answers[$u['id']] ?? 0;
        $u['canManage'] = can_manage_user($actor, $u);
        $out[] = $u;
    }
    return $out;
}

function admin_update_user(array $actor, array $in): array
{
    $id = (int)($in['id'] ?? 0);
    $u = find_user($id);
    if (!$u) fail('Utente non trovato.', 404, 'not_found');
    require_manageable($actor, $u);

    $status = array_key_exists('status', $in) ? (string)$in['status'] : $u['status'];
    if (!in_array($status, USER_STATUSES, true)) fail('Stato non valido.', 422, 'validation');
    $perm = (int)$u['permissions'];
    if (array_key_exists('permissions', $in)) {
        $perm = (int)$in['permissions'];
        if ($perm !== (int)$u['permissions'] && !in_array($perm, ASSIGNABLE_LEVELS, true)) {
            fail('Livello non valido: scegli Utente (1), Moderatore contenuti (5) o Amministratore (9).', 422, 'validation');
        }
        if ($perm >= PERM_ADMIN && (int)$actor['permissions'] < PERM_MASTER) {
            fail('Solo il master admin può nominare altri amministratori.', 403, 'forbidden');
        }
    }
    $name = array_key_exists('name', $in) ? str_param($in, 'name', 100, true) : $u['name'];

    // Enabling a user without permissions assigns the base level
    if ($status === 'active' && $perm < PERM_USER) $perm = PERM_USER;

    $approvedAt = $u['approved_at'];
    $approvedBy = $u['approved_by'];
    if ($status === 'active' && $u['status'] !== 'active' && !$approvedAt) {
        $approvedAt = now_utc();
        $approvedBy = $actor['id'];
    }

    db()->prepare('UPDATE ' . t('users') . ' SET name = ?, status = ?, permissions = ?, approved_at = ?, approved_by = ? WHERE id = ?')
        ->execute([$name, $status, $perm, $approvedAt, $approvedBy, $id]);

    // Users who lose access are signed out immediately
    if ($status !== 'active' || $perm < PERM_USER) {
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$id]);
    }
    $user = find_user($id);
    // Enabling an account that never received the "account activated" email sends it now
    if ($status === 'active' && $u['status'] !== 'active' && empty($u['activation_mail_at'])) {
        $GLOBALS['last_account_mail'] = ['kind' => 'approved'] + send_account_emails([$user], 'approved')[0];
        $user = find_user($id);
    } elseif ($status === 'active' && $u['status'] === 'active' && $perm !== (int)$u['permissions'] && mail_enabled()) {
        // Role changed on an active account: tell the user what they can do now
        $GLOBALS['last_account_mail'] = ['kind' => 'role_changed']
            + send_account_emails([$user], 'role_changed', ['old_level' => (int)$u['permissions'], 'new_level' => $perm])[0];
    }
    return $user;
}

// ---------------- Account emails ----------------

/** Links used in the emails. The portal address comes from the current request, so it is always the live one. */
function account_email_links(): array
{
    $host = (string)($_SERVER['HTTP_HOST'] ?? '');
    if ($host !== '' && preg_match('/^[a-z0-9.-]+(:\d+)?$/i', $host)) {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $portal = ($https ? 'https://' : 'http://') . $host . app_base_path();
    } else {
        $portal = rtrim((string)(config()['public_url'] ?? ''), '/') . '/';
    }
    return [
        'portal_url' => $portal,
        'icon_url' => $portal . 'assets/icons/icon-192.png',
        'assets_url' => $portal . 'assets/email/',
        'contact_url' => 'https://www.saveriomorelli.com/contact-me/email/',
        'credit_url' => 'https://www.saveriomorelli.com/',
    ];
}

/**
 * Sends the account email to each user ($kind: 'approved' or 'rejected') over one connection.
 * For 'approved' it records the date in activation_mail_at. Returns ['sent' => bool, 'error' => string] per user.
 */
function send_account_emails(array $users, string $kind, array $extra = []): array
{
    $links = account_email_links();
    $messages = [];
    foreach ($users as $u) {
        $m = render_account_email($kind, $links + $extra + ['name' => $u['name'], 'email' => $u['email']]);
        $messages[] = ['to' => $u['email'], 'name' => $u['name'], 'subject' => $m['subject'], 'html' => $m['html'], 'text' => $m['text']];
    }
    $out = [];
    foreach (send_mails($messages) as $i => $r) {
        if ($r['ok'] && $kind === 'approved') {
            db()->prepare('UPDATE ' . t('users') . ' SET activation_mail_at = ? WHERE id = ?')->execute([now_utc(), (int)$users[$i]['id']]);
        }
        $out[] = ['sent' => $r['ok'], 'error' => $r['error']];
    }
    return $out;
}

/** Sends (or sends again) the "account activated" email to one active user. */
function admin_send_activation(array $actor, int $id): array
{
    $u = find_user($id);
    if (!$u) fail('Utente non trovato.', 404, 'not_found');
    require_manageable($actor, $u);
    if ($u['status'] !== 'active') fail('L\'email di attivazione si invia solo agli account attivi.', 422, 'validation');
    if (!mail_enabled()) fail('Invio email non configurato: manca lib/mail-config.php.', 503, 'mail_disabled');
    $r = send_account_emails([$u], 'approved')[0];
    if (!$r['sent']) fail('Email non inviata: ' . $r['error'] . '.', 502, 'mail_failed');
    return find_user($id);
}

/** Sends the "account activated" email to every active user (that the actor manages) who never received it. */
function admin_send_activation_all(array $actor): array
{
    if (!mail_enabled()) fail('Invio email non configurato: manca lib/mail-config.php.', 503, 'mail_disabled');
    $rows = db()->query('SELECT * FROM ' . t('users') . " WHERE status = 'active' AND permissions >= " . PERM_USER . ' AND activation_mail_at IS NULL ORDER BY id LIMIT 100')->fetchAll();
    $targets = array_values(array_filter($rows, function ($u) use ($actor) {
        return can_manage_user($actor, $u);
    }));
    if (!$targets) return ['sent' => 0, 'failed' => 0, 'errors' => []];
    $results = send_account_emails($targets, 'approved');
    $errors = [];
    foreach ($results as $i => $r) if (!$r['sent']) $errors[] = $targets[$i]['email'] . ': ' . $r['error'];
    return ['sent' => count($results) - count($errors), 'failed' => count($errors), 'errors' => array_slice($errors, 0, 5)];
}

function generate_temp_password(): string
{
    // No ambiguous characters (0/O, 1/l/I); guarantees lowercase, uppercase, digits and a symbol
    $sets = ['abcdefghjkmnpqrstuvwxyz', 'ABCDEFGHJKLMNPQRSTUVWXYZ', '23456789', '#%+=?@'];
    $all = implode('', $sets);
    $chars = [];
    foreach ($sets as $set) $chars[] = $set[random_int(0, strlen($set) - 1)];
    while (count($chars) < 14) $chars[] = $all[random_int(0, strlen($all) - 1)];
    for ($i = count($chars) - 1; $i > 0; $i--) {
        $j = random_int(0, $i);
        [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
    }
    return implode('', $chars);
}

/** Generates a temporary password: at the next sign-in the user must choose a new one. */
function admin_reset_password(array $actor, int $id): string
{
    $u = find_user($id);
    if (!$u) fail('Utente non trovato.', 404, 'not_found');
    require_manageable($actor, $u);
    $pw = generate_temp_password();
    db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ?, must_change_password = 1 WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
    db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$id]);
    return $pw;
}

function admin_delete_user(array $actor, int $id): void
{
    $u = find_user($id);
    if (!$u) fail('Utente non trovato.', 404, 'not_found');
    require_manageable($actor, $u);
    // Rejecting a registration: tell the person before the data is deleted
    if ($u['status'] === 'pending' && mail_enabled()) {
        $GLOBALS['last_account_mail'] = ['kind' => 'rejected'] + send_account_emails([$u], 'rejected')[0];
    }
    db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$id]);
}

/** The master admin hands the role to another active user and becomes an administrator (9). */
function admin_transfer_master(array $actor, array $in): array
{
    if ((int)$actor['permissions'] < PERM_MASTER) fail('Solo il master admin può trasferire il proprio ruolo.', 403, 'forbidden');
    $id = (int)($in['id'] ?? 0);
    $target = find_user($id);
    if (!$target || $id === (int)$actor['id']) fail('Utente non valido.', 422, 'validation');
    if ($target['status'] !== 'active' || (int)$target['permissions'] < PERM_USER) fail('Il nuovo master admin deve avere un account attivo.', 422, 'validation');
    $me = find_user((int)$actor['id']);
    if (!password_verify((string)($in['password'] ?? ''), $me['password_hash'])) fail('Password non corretta.', 422, 'bad_current');
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $pdo->prepare('UPDATE ' . t('users') . ' SET permissions = ? WHERE id = ?')->execute([PERM_ADMIN, $actor['id']]);
        $pdo->prepare('UPDATE ' . t('users') . ' SET permissions = ? WHERE id = ?')->execute([PERM_MASTER, $id]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
    $user = find_user($id);
    if (mail_enabled()) {
        $GLOBALS['last_account_mail'] = ['kind' => 'role_changed']
            + send_account_emails([$user], 'role_changed', ['old_level' => (int)$target['permissions'], 'new_level' => PERM_MASTER])[0];
    }
    return $user;
}

/**
 * Read-only study overview of a user for administrators (level >= 9).
 * Returns only learning data (answers, accuracy, weak topics, saved items, reports):
 * no email or other personal details.
 */
function admin_user_stats(int $id): array
{
    $u = find_user($id);
    if (!$u) fail('Utente non trovato.', 404, 'not_found');

    $st = db()->prepare('SELECT state_key, value, updated_at FROM ' . t('user_state') . ' WHERE user_id = ?');
    $st->execute([$id]);
    $state = [];
    $lastSaved = null;
    foreach ($st as $r) {
        $state[$r['state_key']] = json_decode($r['value'], true);
        if ($lastSaved === null || $r['updated_at'] > $lastSaved) $lastSaved = $r['updated_at'];
    }
    $data = is_array($state['data'] ?? null) ? $state['data'] : [];
    $stats = is_array($data['stats'] ?? null) ? $data['stats'] : [];
    $correct = (int)($stats['correct'] ?? 0);
    $wrong = (int)($stats['wrong'] ?? 0);
    $refCorrect = is_array($stats['refCorrect'] ?? null) ? $stats['refCorrect'] : [];
    $refWrong = is_array($stats['refWrong'] ?? null) ? $stats['refWrong'] : [];

    // Reference code -> source and title, to group answers by source
    $refs = [];
    foreach (db()->query('SELECT code, source, title FROM ' . t('refs')) as $r) $refs[$r['code']] = $r;
    $bySource = [];
    foreach (SOURCES as $src) $bySource[$src] = ['correct' => 0, 'wrong' => 0];
    $sourceOf = function (string $code) use ($refs): ?string {
        if (isset($refs[$code])) return $refs[$code]['source'];
        $prefix = strtok($code, '-');
        $found = array_search($prefix, SOURCE_PREFIX, true);
        return $found === false ? null : $found;
    };
    foreach ($refCorrect as $code => $n) {
        $src = $sourceOf((string)$code);
        if ($src) $bySource[$src]['correct'] += (int)$n;
    }
    foreach ($refWrong as $code => $n) {
        $src = $sourceOf((string)$code);
        if ($src) $bySource[$src]['wrong'] += (int)$n;
    }

    // Weakest topics: references with the lowest share of correct answers (at least one mistake)
    $weak = [];
    foreach ($refWrong as $code => $w) {
        $w = (int)$w;
        if ($w <= 0) continue;
        $c = (int)($refCorrect[$code] ?? 0);
        $weak[] = ['code' => (string)$code, 'title' => $refs[$code]['title'] ?? '', 'correct' => $c, 'wrong' => $w, 'pct' => (int)round($c / ($c + $w) * 100)];
    }
    usort($weak, function ($a, $b) {
        return $a['pct'] <=> $b['pct'] ?: $b['wrong'] <=> $a['wrong'];
    });

    $bm = is_array($data['bookmarks'] ?? null) ? $data['bookmarks'] : [];
    $flags = is_array($bm['flagged'] ?? null) ? $bm['flagged'] : [];
    $openFlags = count(array_filter($flags, function ($f) {
        return is_array($f) && empty($f['resolved']);
    }));
    $suspended = $state['suspended'] ?? [];
    $suspendedCount = is_array($suspended) ? (isset($suspended['questionCodes']) ? 1 : count($suspended)) : 0;

    return [
        'name' => $u['name'],
        'role' => role_label((int)$u['permissions']),
        'status' => $u['status'],
        'created_at' => $u['created_at'],
        'last_seen_at' => $u['last_seen_at'] ?: $u['last_login_at'],
        'last_saved_at' => $lastSaved,
        'answers' => ['total' => $correct + $wrong, 'correct' => $correct, 'wrong' => $wrong],
        'by_source' => $bySource,
        'weak_refs' => array_slice($weak, 0, 8),
        'refs_practiced' => count(array_unique(array_merge(array_keys($refCorrect), array_keys($refWrong)))),
        'saved' => ['questions' => count((array)($bm['questions'] ?? [])), 'refs' => count((array)($bm['refs'] ?? []))],
        'flags' => ['open' => $openFlags, 'closed' => count($flags) - $openFlags],
        'suspended_quizzes' => $suspendedCount,
        'quiz_in_progress' => !empty($state['autosave']),
    ];
}

/** Collects the reports of all users (stored in their personal state). */
function admin_list_flags(): array
{
    $out = [];
    // Simulation reports closed by an admin (kept apart from the users' own state, which their app rewrites)
    $closed = json_decode((string)meta_get('sim_flags_closed', '{}'), true) ?: [];
    // Quiz reports live in the "data" slot; match simulation reports (codes "SIM-...") in the "sim" slot
    $rows = db()->query('SELECT s.user_id, s.state_key, s.value, u.name, u.email FROM ' . t('user_state') . ' s JOIN ' . t('users') . " u ON u.id = s.user_id WHERE s.state_key IN ('data', 'sim')");
    foreach ($rows as $r) {
        $d = json_decode($r['value'], true);
        $flags = $r['state_key'] === 'sim' ? ($d['flagged'] ?? []) : ($d['bookmarks']['flagged'] ?? []);
        if (!is_array($flags)) continue;
        foreach ($flags as $f) {
            if (!is_array($f) || empty($f['code'])) continue;
            $sim = $r['state_key'] === 'sim';
            $id = $sim ? sim_flag_id((int)$r['user_id'], (string)($f['date'] ?? ''), (string)$f['code']) : null;
            $out[] = [
                'kind' => $sim ? 'sim' : 'quiz',
                'id' => $id,
                'code' => (string)$f['code'],
                'text' => (string)($f['text'] ?? ''),
                'date' => (string)($f['date'] ?? ''),
                'resolved' => $sim ? isset($closed[$id]) : !empty($f['resolved']),
                'closedAt' => $sim && isset($closed[$id]) ? (string)$closed[$id] : null,
                'context' => mb_substr((string)($f['context'] ?? ''), 0, 600),
                // Simulation: everything needed to understand (and recreate) the action
                'detail' => $sim && isset($f['detail']) && is_array($f['detail']) ? $f['detail'] : null,
                'user' => ['id' => (int)$r['user_id'], 'name' => $r['name'], 'email' => $r['email']],
            ];
        }
    }
    usort($out, function ($a, $b) {
        return strcmp($b['date'], $a['date']);
    });
    return $out;
}

/** Stable id of a simulation report: user, date and code. */
function sim_flag_id(int $userId, string $date, string $code): string
{
    return substr(sha1($userId . '|' . $date . '|' . $code), 0, 16);
}

/** Closes (or reopens) a simulation report. */
function admin_close_sim_flag(string $id, bool $closed): void
{
    if (!preg_match('/^[0-9a-f]{16}$/', $id)) fail('Segnalazione non valida.', 400, 'bad_request');
    $map = json_decode((string)meta_get('sim_flags_closed', '{}'), true) ?: [];
    if ($closed) $map[$id] = gmdate('c'); else unset($map[$id]);
    meta_set('sim_flags_closed', json_encode($map));
}

/**
 * Data for the panel Overview: aggregated and anonymous (no names or emails),
 * so content moderators can see it too.
 */
function admin_overview(): array
{
    $pdo = db();

    // Users: counts by status and recent activity (dates are stored in UTC)
    $u = $pdo->query("SELECT
            COUNT(*) AS total,
            SUM(status = 'active') AS active,
            SUM(status = 'pending') AS pending,
            SUM(status = 'disabled') AS disabled,
            SUM(status = 'active' AND COALESCE(last_seen_at, last_login_at) >= UTC_TIMESTAMP() - INTERVAL 7 DAY) AS seen7,
            SUM(status = 'active' AND COALESCE(last_seen_at, last_login_at) >= UTC_TIMESTAMP() - INTERVAL 30 DAY) AS seen30,
            SUM(created_at >= UTC_TIMESTAMP() - INTERVAL 30 DAY) AS new30
        FROM " . t('users'))->fetch();
    $users = array_map('intval', $u ?: []);

    // Study activity, summed over every user's personal state
    $refs = [];
    foreach ($pdo->query('SELECT code, source, title FROM ' . t('refs')) as $r) $refs[$r['code']] = $r;
    $bySource = [];
    foreach (SOURCES as $src) $bySource[$src] = ['correct' => 0, 'wrong' => 0];
    $refTotals = [];   // code => [correct, wrong, users who got it wrong]
    $correct = $wrong = $learners = $savedQ = $savedR = $inProgress = $suspended = 0;

    $rows = $pdo->query('SELECT s.state_key, s.value FROM ' . t('user_state') . ' s JOIN ' . t('users') . " u ON u.id = s.user_id
        WHERE u.status = 'active' AND s.state_key IN ('data', 'suspended', 'autosave')");
    foreach ($rows as $row) {
        $v = json_decode($row['value'], true);
        if ($row['state_key'] === 'autosave') {
            if (!empty($v)) $inProgress++;
            continue;
        }
        if ($row['state_key'] === 'suspended') {
            if (is_array($v)) $suspended += isset($v['questionCodes']) ? 1 : count($v);
            continue;
        }
        if (!is_array($v)) continue;
        $st = is_array($v['stats'] ?? null) ? $v['stats'] : [];
        $c = (int)($st['correct'] ?? 0);
        $w = (int)($st['wrong'] ?? 0);
        $correct += $c;
        $wrong += $w;
        if ($c + $w > 0) $learners++;
        $bm = is_array($v['bookmarks'] ?? null) ? $v['bookmarks'] : [];
        $savedQ += count((array)($bm['questions'] ?? []));
        $savedR += count((array)($bm['refs'] ?? []));
        foreach (['refCorrect' => 0, 'refWrong' => 1] as $key => $i) {
            if (!is_array($st[$key] ?? null)) continue;
            foreach ($st[$key] as $code => $n) {
                $code = (string)$code;
                $n = (int)$n;
                if ($n <= 0) continue;
                if (!isset($refTotals[$code])) $refTotals[$code] = [0, 0, 0];
                $refTotals[$code][$i] += $n;
                if ($i === 1) $refTotals[$code][2]++;
                $src = $refs[$code]['source'] ?? null;
                if ($src === null) {
                    $found = array_search(strtok($code, '-'), SOURCE_PREFIX, true);
                    $src = $found === false ? null : $found;
                }
                if ($src !== null && isset($bySource[$src])) $bySource[$src][$i ? 'wrong' : 'correct'] += $n;
            }
        }
    }

    // Hardest references: lowest share of correct answers, with enough answers to be meaningful
    $hard = [];
    foreach ($refTotals as $code => [$c, $w, $nUsers]) {
        if ($w === 0 || $c + $w < 3 || !isset($refs[$code])) continue;
        $hard[] = ['code' => $code, 'title' => $refs[$code]['title'], 'source' => $refs[$code]['source'],
            'answers' => $c + $w, 'pct' => (int)round($c / ($c + $w) * 100), 'users' => $nUsers];
    }
    usort($hard, function ($a, $b) {
        return $a['pct'] <=> $b['pct'] ?: $b['answers'] <=> $a['answers'];
    });

    // Official PDFs (fixed, in documenti/): how many and how much space
    $official = ['count' => 0, 'size' => 0];
    foreach (glob(dirname(__DIR__) . '/documenti/*.pdf') ?: [] as $f) {
        $official['count']++;
        $official['size'] += (int)filesize($f);
    }

    return [
        'users' => $users,
        'activity' => [
            'answers' => $correct + $wrong, 'correct' => $correct, 'wrong' => $wrong,
            'learners' => $learners,
            'refs_practiced' => count(array_intersect_key($refTotals, $refs)),
            'saved' => ['questions' => $savedQ, 'refs' => $savedR],
            'quiz_in_progress' => $inProgress, 'suspended_quizzes' => $suspended,
        ],
        'by_source' => $bySource,
        'hard_refs' => array_slice($hard, 0, 6),
        'official_documents' => $official,
    ];
}
