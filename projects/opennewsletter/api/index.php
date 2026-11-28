<?php
/**
 * OpenNewsletter — JSON API.  api/index.php?r=<action>
 *
 * Accounts:     auth.config, auth.me, auth.register, auth.login, auth.google, auth.resend, auth.verify, auth.logout,
 *               auth.password, auth.profile, auth.delete
 * Groups:       group.list, group.create, group.get, group.update, group.page, group.terms, group.smtp, group.smtpTest,
 *               group.smtpClear, group.delete, group.leave, group.slug
 * Members:      member.list, member.add, member.role, member.remove, invite.cancel
 * Subscribers:  sub.list, sub.remove, sub.import, sub.export
 * Templates:    tpl.list, tpl.save, tpl.preview, tpl.delete, tpl.fav
 * Newsletters:  nl.list, nl.get, nl.create, nl.save, nl.duplicate, nl.delete, nl.preview, nl.test,
 *               nl.submit, nl.withdraw, nl.reschedule, nl.approve, nl.reject, nl.cancel
 * Images:       img.upload, img (public)
 * Public:       pub.subscribe, pub.confirm, pub.unsubscribe, pub.unsubRequest (subscription pages and widget)
 * Master admin: master.overview, master.group, master.status, master.preview
 *
 * Every POST of a signed-in user needs the X-CSRF-Token header (given at sign-in). Roles: see lib/groups.php.
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/newsletters.php';
require __DIR__ . '/../lib/templates.php';

header('X-Robots-Tag: noindex');
set_exception_handler(function (Throwable $e) {
    if ($e->getMessage() === 'not_installed') fail('not_installed', 503);
    if (db_in_transaction()) db()->rollBack();
    error_log('OpenNewsletter API: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    send_json(['ok' => false, 'error' => 'server', 'detail' => config_value('debug', false) ? $e->getMessage() : null], 500);
});

$r = (string)($_GET['r'] ?? '');

if ($r === 'auth.config' && !credentials()) send_json(['ok' => true, 'installed' => false, 'googleClientId' => '']);
if (!credentials()) fail('not_installed', 503);

// Pictures are plain GETs (they are shown in emails)
if ($r === 'img') {
    $id = (string)($_GET['id'] ?? '');
    if (!preg_match('/^[a-f0-9]{20}$/', $id)) {
        http_response_code(404);
        exit;
    }
    $st = db()->prepare('SELECT mime, data FROM ' . t('images') . ' WHERE id = ?');
    $st->execute([$id]);
    $img = $st->fetch();
    if (!$img) {
        http_response_code(404);
        exit;
    }
    header('Content-Type: ' . $img['mime']);
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Content-Security-Policy: default-src \'none\'');
    echo $img['data'];
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json') !== 0) fail('bad_request', 415);
remember_app_url();

function only(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) fail('bad_request', 405);
}

function writer(bool $unverifiedOk = false): array
{
    only('POST');
    $u = require_user($unverifiedOk);
    require_csrf($u);
    return $u;
}

function reader(): array
{
    only('GET');
    return require_user();
}

function validate_password(string $pw): void
{
    if (mb_strlen($pw) < 8) fail('password_short', 422);
    if (mb_strlen($pw) > 200) fail('password_long', 422);
}

function ui_lang(array $b): string
{
    $l = (string)($b['lang'] ?? '');
    return in_array($l, LANGS, true) ? $l : 'en';
}

function valid_email(string $email): void
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) fail('email_invalid', 422);
}

function user_row(int $id): array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: [];
}

function new_user(string $name, string $email, string $hash, ?string $googleSub, string $lang): int
{
    db()->prepare('INSERT INTO ' . t('users') . ' (name, email, password_hash, google_sub, ui_lang, created_at, last_login_at) VALUES (?,?,?,?,?,?,?)')
        ->execute([$name, $email, $hash, $googleSub, $lang, now_utc(), now_utc()]);
    return (int)db()->lastInsertId();
}

function signed_in(int $uid): void
{
    db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $uid]);
    accept_invites(user_row($uid));
    $csrf = create_auth_session($uid);
    send_json(['ok' => true, 'user' => public_user(user_row($uid)), 'csrf' => $csrf]);
}

function gid(array $b): int
{
    return (int)($b['group'] ?? $_GET['group'] ?? 0);
}

/** A newsletter of a group the user is in, with the role needed. Returns [newsletter, group, role]. */
function require_nl(array $u, int $id, string $minRole, bool $write = true): array
{
    $n = $id ? nl_row($id) : null;
    if (!$n) fail('nl_not_found', 404);
    [$g, $role] = require_group($u, (int)$n['group_id'], $minRole, $write);
    return [$n, $g, $role];
}

/** Moderators change only their own newsletters; publishers and up change any. */
function can_edit(array $n, array $u, string $role): bool
{
    return role_rank($role) >= role_rank('publisher') || (int)$n['author_id'] === (int)$u['id'];
}

function nl_names(array $rows): array
{
    $ids = [];
    foreach ($rows as $n) array_push($ids, $n['author_id'], $n['reviewer_id']);
    return user_names($ids);
}

function out_nl(int $id, bool $doc = true): array
{
    $n = nl_row($id);
    return public_nl($n, $doc, nl_names([$n]));
}

function decode_doc($raw): array
{
    $doc = clean_doc($raw);
    if (strlen(json_out($doc)) > MAX_DOC_BYTES) fail('doc_too_big', 413);
    return $doc;
}

/** Ids of the built-in templates this user starred (still existing ones only). */
function fav_templates(array $u): array
{
    $f = json_decode((string)($u['fav_templates'] ?? '[]'), true);
    return array_values(array_filter(is_array($f) ? $f : [], fn($id) => is_string($id) && builtin_template($id, 'en') !== null));
}

function require_master(): array
{
    $u = require_user();
    if (!is_master($u)) fail('forbidden', 403);
    return $u;
}

/** The preview HTML of a newsletter (unsubscribe link inactive). */
function preview_html(array $doc, array $g, string $subject, string $preheader, ?string $lang): string
{
    $langs = group_langs($g);
    return render_email($doc, $g, ['lang' => $lang ?: $langs[0], 'subject' => $subject, 'preheader' => $preheader, 'unsubscribe' => '#'])['html'];
}

function subscriber_public(array $s): array
{
    return ['id' => (int)$s['id'], 'email' => $s['email'], 'lang' => $s['lang'], 'browserLang' => $s['browser_lang'], 'browser' => $s['browser'], 'source' => $s['source'],
        'status' => $s['status'], 'createdAt' => iso($s['created_at']), 'confirmedAt' => iso($s['confirmed_at']), 'unsubscribedAt' => iso($s['unsubscribed_at']), 'reason' => $s['unsub_reason']];
}

switch ($r) {

    // ================================================================ accounts

    case 'auth.config':
        send_json(['ok' => true, 'installed' => true, 'googleClientId' => (string)config_value('google_client_id', ''), 'mailReady' => mail_settings() !== null,
            'langs' => SUB_LANGS, 'timezone' => config_value('timezone', 'Europe/Rome')]);

    case 'auth.me':
        only('GET');
        $u = current_user();
        if (!$u || $u['status'] !== 'active') send_json(['ok' => true, 'user' => null]);
        accept_invites($u);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id'])), 'csrf' => $u['csrf_token']]);

    case 'auth.register':
        only('POST');
        if (too_many_attempts('register', 5, 60)) fail('rate_limited', 429);
        $b = body();
        $name = s($b, 'name', 80);
        $email = mb_strtolower(s($b, 'email', 190));
        $pw = (string)($b['password'] ?? '');
        if (mb_strlen($name) < 2) fail('name_required', 422);
        valid_email($email);
        validate_password($pw);
        if (empty($b['acceptTerms'])) fail('terms_required', 422);
        // The same answer whether the address is new or already registered, so nobody can find out who has an account
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        record_attempt('register');
        if (!$row) send_verification(user_row(new_user($name, $email, password_hash($pw, PASSWORD_DEFAULT), null, ui_lang($b))), ui_lang($b));
        elseif (empty($row['email_verified_at']) && $row['status'] === 'active' && !too_many_attempts('verify_mail', 3, 60)) {
            record_attempt('verify_mail');
            send_verification($row, ui_lang($b));
        }
        send_json(['ok' => true, 'checkEmail' => true]);

    case 'auth.login':
        only('POST');
        if (too_many_attempts('login', 10, 15)) fail('rate_limited', 429);
        $b = body();
        $email = mb_strtolower(s($b, 'email', 190));
        $pw = (string)($b['password'] ?? '');
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        // One answer for an unknown address, a wrong password and a Google-only account, in about the same time
        $ok = $row && $row['password_hash'] !== '' ? password_verify($pw, $row['password_hash']) : password_hash($pw, PASSWORD_DEFAULT) === '';
        if (!$ok) {
            record_attempt('login');
            fail('bad_credentials', 401);
        }
        if ($row['status'] !== 'active') fail('disabled', 403);
        clear_attempts('login');
        if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $row['id']]);
        signed_in((int)$row['id']);

    case 'auth.google':
        // Google already proves the address: no email check. A new account must accept the terms first ("needs_signup").
        only('POST');
        if (too_many_attempts('login', 20, 15)) fail('rate_limited', 429);
        $b = body();
        $g = verify_google_token((string)($b['credential'] ?? ''));
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE google_sub = ?');
        $st->execute([$g['sub']]);
        $row = $st->fetch();
        if (!$row) {
            $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
            $st->execute([$g['email']]);
            $row = $st->fetch();
            // Same address already registered: the Google account is linked to it. If nobody confirmed that
            // address, the password someone else may have chosen is dropped.
            if ($row) db()->prepare('UPDATE ' . t('users') . ' SET google_sub = ?, password_hash = IF(email_verified_at IS NULL, \'\', password_hash), email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')
                ->execute([$g['sub'], now_utc(), $row['id']]);
        }
        if (!$row) {
            if (empty($b['acceptTerms'])) send_json(['ok' => false, 'error' => 'needs_signup', 'name' => $g['name']], 200);
            if (too_many_attempts('register', 5, 60)) fail('rate_limited', 429);
            record_attempt('register');
            $row = user_row(new_user($g['name'] ?: explode('@', $g['email'])[0], $g['email'], '', $g['sub'], ui_lang($b)));
            db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        }
        if ($row['status'] !== 'active') fail('disabled', 403);
        signed_in((int)$row['id']);

    case 'auth.resend':
        $u = writer(true);
        if (!empty($u['email_verified_at'])) send_json(['ok' => true, 'user' => public_user(user_row((int)$u['id']))]);
        if (too_many_attempts('verify_mail', 3, 60)) fail('rate_limited', 429);
        record_attempt('verify_mail');
        send_verification(user_row((int)$u['id']), ui_lang(body()));
        send_json(['ok' => true]);

    case 'auth.verify':
        // The link of the email opens the app, which asks for this with a button: mail scanners that open links do nothing
        only('POST');
        if (too_many_attempts('verify', 20, 15)) fail('rate_limited', 429);
        $token = (string)(body()['token'] ?? '');
        $st = db()->prepare('SELECT user_id FROM ' . t('email_tokens') . ' WHERE token_hash = ? AND expires_at > ?');
        $st->execute([hash('sha256', $token), now_utc()]);
        $uid = (int)$st->fetchColumn();
        if (!$uid) {
            record_attempt('verify');
            fail('token_invalid', 410);
        }
        db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?')->execute([now_utc(), $uid]);
        db()->prepare('DELETE FROM ' . t('email_tokens') . ' WHERE user_id = ?')->execute([$uid]);
        if (user_row($uid)['status'] !== 'active') fail('disabled', 403);
        signed_in($uid);

    case 'auth.logout':
        only('POST');
        $u = current_user();
        if ($u) require_csrf($u);
        destroy_auth_session();
        send_json(['ok' => true]);

    case 'auth.password':
        $u = writer();
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['current'] ?? ''), $row['password_hash'])) fail('bad_password', 422);
        $pw = (string)($b['password'] ?? '');
        validate_password($pw);
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.profile':
        // Name and language of the app (also the language of the platform's emails). The email never changes.
        $u = writer(true);
        $b = body();
        $name = s($b, 'name', 80);
        if ($name !== '' && mb_strlen($name) < 2) fail('name_required', 422);
        db()->prepare('UPDATE ' . t('users') . ' SET name = IF(? = \'\', name, ?), ui_lang = ? WHERE id = ?')->execute([$name, $name, ui_lang($b), $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.delete':
        $u = writer(true);
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['password'] ?? ''), $row['password_hash'])) fail('bad_password', 422);
        $own = db()->prepare('SELECT COUNT(*) FROM ' . t('members') . " WHERE user_id = ? AND role = 'owner'");
        $own->execute([$u['id']]);
        if ((int)$own->fetchColumn()) fail('owns_groups', 409); // give the groups to someone else or delete them first
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]);
        set_auth_cookie('', time() - 3600);
        send_json(['ok' => true]);

    // ================================================================ groups

    case 'group.list':
        $u = reader();
        $st = db()->prepare('SELECT g.*, m.role FROM ' . t('members') . ' m JOIN ' . t('groups') . ' g ON g.id = m.group_id WHERE m.user_id = ? ORDER BY g.name');
        $st->execute([$u['id']]);
        $out = [];
        foreach ($st->fetchAll() as $g) {
            $c = subscriber_counts((int)$g['id']);
            $p = db()->prepare('SELECT status, COUNT(*) n FROM ' . t('newsletters') . " WHERE group_id = ? AND status IN ('pending','scheduled','draft','sending') GROUP BY status");
            $p->execute([$g['id']]);
            $nl = array_column($p->fetchAll(), 'n', 'status');
            $out[] = ['id' => (int)$g['id'], 'slug' => $g['slug'], 'name' => $g['name'], 'description' => $g['description'], 'role' => $g['role'], 'status' => $g['status'],
                'subscribers' => $c['active'], 'pending' => (int)($nl['pending'] ?? 0), 'scheduled' => (int)($nl['scheduled'] ?? 0) + (int)($nl['sending'] ?? 0), 'drafts' => (int)($nl['draft'] ?? 0),
                'sendingReady' => !empty($g['smtp_sealed']) && $g['from_email'] !== '', 'accent' => group_page($g)['accent']];
        }
        send_json(['ok' => true, 'groups' => $out]);

    case 'group.slug':
        only('GET');
        require_user();
        send_json(['ok' => true, 'slug' => suggest_slug(s($_GET, 'name', 80))]);

    case 'group.create':
        $u = writer();
        if (too_many_attempts('group', 10, 60)) fail('rate_limited', 429);
        $b = body();
        $name = s($b, 'name', 80);
        if (mb_strlen($name) < 2) fail('name_required', 422);
        $slug = clean_slug(s($b, 'slug', 40) ?: suggest_slug($name));
        $langs = array_values(array_unique(array_intersect((array)($b['langs'] ?? []), SUB_LANGS)));
        if (!$langs) $langs = [in_array($u['ui_lang'], SUB_LANGS, true) ? $u['ui_lang'] : 'en'];
        $tz = (string)($b['timezone'] ?? '');
        if (!in_array($tz, timezone_identifiers_list(), true)) $tz = (string)config_value('timezone', 'Europe/Rome');
        record_attempt('group');
        db()->beginTransaction();
        db()->prepare('INSERT INTO ' . t('groups') . ' (slug, name, description, owner_id, timezone, langs, page, terms, from_name, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$slug, $name, s($b, 'description', 300), $u['id'], $tz, json_out($langs), json_out(default_page()), '', $name, now_utc(), now_utc()]);
        $id = (int)db()->lastInsertId();
        db()->prepare('INSERT INTO ' . t('members') . ' (group_id, user_id, role, created_at) VALUES (?,?,?,?)')->execute([$id, $u['id'], 'owner', now_utc()]);
        db()->commit();
        send_json(['ok' => true, 'group' => public_group(group_row($id), 'owner')]);

    case 'group.get':
        $u = reader();
        [$g, $role] = require_group($u, gid([]), 'user', false);
        send_json(['ok' => true, 'group' => public_group($g, $role)]);

    case 'group.update':
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'admin');
        $name = s($b, 'name', 80);
        if (mb_strlen($name) < 2) fail('name_required', 422);
        $slug = clean_slug(s($b, 'slug', 40), (int)$g['id']);
        $langs = array_values(array_unique(array_intersect((array)($b['langs'] ?? []), SUB_LANGS)));
        if (!$langs) fail('langs_required', 422);
        $tz = (string)($b['timezone'] ?? '');
        if (!in_array($tz, timezone_identifiers_list(), true)) fail('timezone_invalid', 422);
        db()->prepare('UPDATE ' . t('groups') . ' SET name = ?, slug = ?, description = ?, langs = ?, timezone = ?, updated_at = ? WHERE id = ?')
            ->execute([$name, $slug, s($b, 'description', 300), json_out($langs), $tz, now_utc(), $g['id']]);
        send_json(['ok' => true, 'group' => public_group(group_row((int)$g['id']), $role)]);

    case 'group.page':
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'admin');
        db()->prepare('UPDATE ' . t('groups') . ' SET page = ?, updated_at = ? WHERE id = ?')->execute([json_out(clean_page($b['page'] ?? null)), now_utc(), $g['id']]);
        send_json(['ok' => true, 'group' => public_group(group_row((int)$g['id']), $role)]);

    case 'group.terms':
        // A new text gets a new revision: each subscriber keeps the revision it accepted
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'admin');
        $terms = multiline($b, 'terms', 20000);
        $rev = $terms === '' ? '' : substr(hash('sha256', $terms), 0, 12);
        db()->prepare('UPDATE ' . t('groups') . ' SET terms = ?, terms_rev = ?, updated_at = ? WHERE id = ?')->execute([$terms, $rev, now_utc(), $g['id']]);
        send_json(['ok' => true, 'group' => public_group(group_row((int)$g['id']), $role)]);

    case 'group.smtp':
        // The outgoing address of the group. The password is never sent back: an empty one keeps the saved one.
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'admin');
        $host = mb_strtolower(s($b, 'host', 190));
        $enc = in_array($b['encryption'] ?? '', ['ssl', 'tls', 'none'], true) ? $b['encryption'] : 'ssl';
        $port = (int)($b['port'] ?? 0) ?: ($enc === 'ssl' ? 465 : 587);
        $from = mb_strtolower(s($b, 'fromEmail', 190));
        $reply = mb_strtolower(s($b, 'replyTo', 190));
        if (!smtp_host_allowed($host)) fail('smtp_host', 422);
        if ($port < 1 || $port > 65535 || in_array($port, [22, 80, 443, 3306], true)) fail('smtp_port', 422);
        valid_email($from);
        if ($reply !== '') valid_email($reply);
        $old = unseal((string)($g['smtp_sealed'] ?? '')) ?: [];
        $pw = (string)($b['password'] ?? '');
        $sealed = seal(['smtp_host' => $host, 'smtp_port' => $port, 'smtp_encryption' => $enc, 'smtp_user' => s($b, 'user', 190),
            'smtp_password' => $pw !== '' ? mb_substr($pw, 0, 500) : (string)($old['smtp_password'] ?? '')]);
        // messages per hour the provider accepts from this account (the sending spreads over the next runs)
        $rate = max(10, min(5000, (int)($b['ratePerHour'] ?? 200)));
        db()->prepare('UPDATE ' . t('groups') . ' SET smtp_sealed = ?, from_email = ?, from_name = ?, reply_to = ?, rate_per_hour = ?, smtp_checked_at = NULL, updated_at = ? WHERE id = ?')
            ->execute([$sealed, $from, s($b, 'fromName', 80) ?: $g['name'], $reply, $rate, now_utc(), $g['id']]);
        send_json(['ok' => true, 'group' => public_group(group_row((int)$g['id']), $role)]);

    case 'group.smtpTest':
        // Sends right now (not after the response): the admin needs to know whether it worked.
        // To any address the admin types (default: their own), at most 10 tests an hour.
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'admin');
        $to = mb_strtolower(s($b, 'to', 190)) ?: (string)$u['email'];
        valid_email($to);
        if (too_many_attempts('smtp_test', 10, 60)) fail('rate_limited', 429);
        record_attempt('smtp_test');
        $mailer = group_mailer($g);
        if (!$mailer) fail('smtp_missing', 409);
        $L = user_lang($u);
        $subject = $L === 'it' ? 'Prova di invio da OpenNewsletter' : 'Test message from OpenNewsletter';
        $body = $L === 'it' ? 'Se leggi questo messaggio, l’indirizzo di uscita di «' . $g['name'] . '» funziona.' : 'If you can read this, the outgoing address of “' . $g['name'] . '” works.';
        $res = smtp_send_all($mailer, [['to' => $to, 'name' => $to === $u['email'] ? $u['name'] : '', 'subject' => $subject, 'html' => mail_layout($L, $subject, [p_html($body)]), 'text' => $body]]);
        if (!$res[0]['ok']) send_json(['ok' => false, 'error' => 'smtp_failed', 'detail' => $res[0]['error']], 502);
        db()->prepare('UPDATE ' . t('groups') . ' SET smtp_checked_at = ? WHERE id = ?')->execute([now_utc(), $g['id']]);
        send_json(['ok' => true, 'to' => $to, 'group' => public_group(group_row((int)$g['id']), $role)]);

    case 'group.smtpClear':
        $u = writer();
        [$g, $role] = require_group($u, gid(body()), 'admin');
        db()->prepare('UPDATE ' . t('groups') . " SET smtp_sealed = NULL, from_email = '', smtp_checked_at = NULL, updated_at = ? WHERE id = ?")->execute([now_utc(), $g['id']]);
        send_json(['ok' => true, 'group' => public_group(group_row((int)$g['id']), $role)]);

    case 'group.delete':
        $u = writer();
        $b = body();
        [$g] = require_group($u, gid($b), 'owner', false);
        if (s($b, 'confirm', 80) !== $g['name']) fail('confirm_name', 422);
        db()->prepare('DELETE FROM ' . t('groups') . ' WHERE id = ?')->execute([$g['id']]); // everything else follows (ON DELETE CASCADE)
        send_json(['ok' => true]);

    case 'group.leave':
        $u = writer();
        [$g, $role] = require_group($u, gid(body()), 'user', false);
        if ($role === 'owner') fail('owner_cannot_leave', 409);
        db()->prepare('DELETE FROM ' . t('members') . ' WHERE group_id = ? AND user_id = ?')->execute([$g['id'], $u['id']]);
        send_json(['ok' => true]);

    // ================================================================ members

    case 'member.list':
        $u = reader();
        [$g, $role] = require_group($u, gid([]), 'user', false);
        $st = db()->prepare('SELECT u.id, u.name, u.email, m.role, m.created_at FROM ' . t('members') . ' m JOIN ' . t('users') . ' u ON u.id = m.user_id WHERE m.group_id = ? ORDER BY FIELD(m.role, \'owner\',\'admin\',\'publisher\',\'moderator\',\'user\'), u.name');
        $st->execute([$g['id']]);
        $members = array_map(fn($m) => ['id' => (int)$m['id'], 'name' => $m['name'], 'email' => $m['email'], 'role' => $m['role'], 'since' => iso($m['created_at'])], $st->fetchAll());
        $invites = [];
        if (role_rank($role) >= role_rank('admin')) {
            $st = db()->prepare('SELECT id, email, role, created_at FROM ' . t('invites') . ' WHERE group_id = ? ORDER BY created_at DESC');
            $st->execute([$g['id']]);
            $invites = array_map(fn($i) => ['id' => (int)$i['id'], 'email' => $i['email'], 'role' => $i['role'], 'since' => iso($i['created_at'])], $st->fetchAll());
        }
        send_json(['ok' => true, 'members' => $members, 'invites' => $invites]);

    case 'member.add':
        // An existing account joins at once; anyone else gets an invitation that applies once they sign up
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'admin');
        if (too_many_attempts('invite', 30, 60)) fail('rate_limited', 429);
        $email = mb_strtolower(s($b, 'email', 190));
        valid_email($email);
        $newRole = (string)($b['role'] ?? 'user');
        if (!in_array($newRole, ['user', 'moderator', 'publisher', 'admin'], true)) fail('role_invalid', 422);
        if ($newRole === 'admin' && $role !== 'owner') fail('forbidden', 403);
        record_attempt('invite');
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $target = $st->fetch();
        $T = null;
        if ($target && !empty($target['email_verified_at'])) {
            if (member_role((int)$g['id'], (int)$target['id']) !== null) fail('already_member', 409);
            db()->prepare('INSERT INTO ' . t('members') . ' (group_id, user_id, role, created_at) VALUES (?,?,?,?)')->execute([$g['id'], $target['id'], $newRole, now_utc()]);
            $L = user_lang($target);
        } else {
            db()->prepare('INSERT INTO ' . t('invites') . ' (group_id, email, role, invited_by, created_at) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE role = VALUES(role)')
                ->execute([$g['id'], $email, $newRole, $u['id'], now_utc()]);
            $L = user_lang($u);
        }
        $T = mail_t($L);
        $v = ['by' => $u['name'], 'group' => $g['name'], 'role' => $T['roles'][$newRole]];
        send_notice($email, $target['name'] ?? '', $L, fill($T['invite_subject'], $v), $T['invite_title'],
            [fill($T['invite_body'], $v), $target && !empty($target['email_verified_at']) ? '' : $T['invite_signup']], app_url(), $T['invite_button']);
        send_json(['ok' => true, 'joined' => (bool)($target && !empty($target['email_verified_at']))]);

    case 'member.role':
        // Admins move people between user and publisher; only the owner names admins or hands the group over
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'admin');
        $uid = (int)($b['user'] ?? 0);
        $newRole = (string)($b['role'] ?? '');
        $cur = member_role((int)$g['id'], $uid);
        if ($cur === null) fail('member_not_found', 404);
        if (!in_array($newRole, ROLES, true)) fail('role_invalid', 422);
        if ($uid === (int)$u['id']) fail('own_role', 409);
        if ($role !== 'owner' && (role_rank($cur) >= role_rank('admin') || role_rank($newRole) >= role_rank('admin'))) fail('forbidden', 403);
        if ($newRole === 'owner') {
            db()->beginTransaction();
            db()->prepare('UPDATE ' . t('members') . " SET role = 'admin' WHERE group_id = ? AND user_id = ?")->execute([$g['id'], $u['id']]);
            db()->prepare('UPDATE ' . t('members') . " SET role = 'owner' WHERE group_id = ? AND user_id = ?")->execute([$g['id'], $uid]);
            db()->prepare('UPDATE ' . t('groups') . ' SET owner_id = ? WHERE id = ?')->execute([$uid, $g['id']]);
            db()->commit();
        } else {
            db()->prepare('UPDATE ' . t('members') . ' SET role = ? WHERE group_id = ? AND user_id = ?')->execute([$newRole, $g['id'], $uid]);
        }
        send_json(['ok' => true]);

    case 'member.remove':
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'admin');
        $uid = (int)($b['user'] ?? 0);
        $cur = member_role((int)$g['id'], $uid);
        if ($cur === null) fail('member_not_found', 404);
        if ($cur === 'owner' || $uid === (int)$u['id']) fail('forbidden', 403);
        if ($cur === 'admin' && $role !== 'owner') fail('forbidden', 403);
        db()->prepare('DELETE FROM ' . t('members') . ' WHERE group_id = ? AND user_id = ?')->execute([$g['id'], $uid]);
        send_json(['ok' => true]);

    case 'invite.cancel':
        $u = writer();
        $b = body();
        [$g] = require_group($u, gid($b), 'admin');
        db()->prepare('DELETE FROM ' . t('invites') . ' WHERE id = ? AND group_id = ?')->execute([(int)($b['id'] ?? 0), $g['id']]);
        send_json(['ok' => true]);

    // ================================================================ subscribers

    case 'sub.list':
        $u = reader();
        [$g, $role] = require_group($u, gid([]), 'publisher', false);
        $where = ['group_id = ?'];
        $args = [$g['id']];
        $status = (string)($_GET['status'] ?? '');
        if (in_array($status, ['active', 'pending', 'unsubscribed'], true)) {
            $where[] = 'status = ?';
            $args[] = $status;
        }
        $lang = (string)($_GET['lang'] ?? '');
        if (in_array($lang, SUB_LANGS, true)) {
            $where[] = 'lang = ?';
            $args[] = $lang;
        }
        $q = s($_GET, 'q', 100);
        if ($q !== '') {
            $where[] = 'email LIKE ?';
            $args[] = '%' . addcslashes($q, '%_\\') . '%';
        }
        $page = max(0, (int)($_GET['page'] ?? 0));
        $w = implode(' AND ', $where);
        $c = db()->prepare('SELECT COUNT(*) FROM ' . t('subscribers') . " WHERE $w");
        $c->execute($args);
        $st = db()->prepare('SELECT * FROM ' . t('subscribers') . " WHERE $w ORDER BY created_at DESC LIMIT 50 OFFSET " . ($page * 50));
        $st->execute($args);
        send_json(['ok' => true, 'total' => (int)$c->fetchColumn(), 'page' => $page, 'pageSize' => 50, 'subscribers' => array_map('subscriber_public', $st->fetchAll()), 'counts' => subscriber_counts((int)$g['id'])]);

    case 'sub.remove':
        // Erases the address completely (a request under the GDPR): it can subscribe again later
        $u = writer();
        $b = body();
        [$g] = require_group($u, gid($b), 'admin');
        db()->prepare('DELETE FROM ' . t('subscribers') . ' WHERE id = ? AND group_id = ?')->execute([(int)($b['id'] ?? 0), $g['id']]);
        send_json(['ok' => true]);

    case 'sub.import':
        // Addresses from a CSV (parsed in the browser). They skip the confirmation email, so the admin must declare that
        // these people agreed to receive the newsletter. Unsubscribed addresses are never added back.
        $u = writer();
        $b = body();
        [$g] = require_group($u, gid($b), 'admin');
        if (empty($b['consent'])) fail('consent_required', 422);
        $rows = is_array($b['rows'] ?? null) ? $b['rows'] : [];
        if (!$rows) fail('import_empty', 422);
        if (count($rows) > 5000) fail('import_too_big', 413);
        if (too_many_attempts('sub_import', 20, 60)) fail('rate_limited', 429);
        record_attempt('sub_import');
        $langs = group_langs($g);
        $default = in_array($b['lang'] ?? '', $langs, true) ? $b['lang'] : $langs[0];
        $report = ['added' => 0, 'confirmed' => 0, 'already' => 0, 'unsubscribed' => 0, 'invalid' => 0];
        $find = db()->prepare('SELECT id, status FROM ' . t('subscribers') . ' WHERE group_id = ? AND email = ?');
        $add = db()->prepare('INSERT INTO ' . t('subscribers') . " (group_id, email, lang, source, status, token, created_at, confirmed_at) VALUES (?,?,?,'import','active',?,?,?)");
        $confirm = db()->prepare('UPDATE ' . t('subscribers') . " SET status = 'active', confirmed_at = ?, confirm_hash = NULL WHERE id = ?");
        $seen = [];
        db()->beginTransaction();
        foreach ($rows as $row) {
            $email = mb_strtolower(s(is_array($row) ? $row : [], 'email', 190));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || isset($seen[$email])) {
                $report[isset($seen[$email]) ? 'already' : 'invalid']++;
                continue;
            }
            $seen[$email] = true;
            $lang = lang_code(s($row, 'lang', 20));
            $lang = in_array($lang, $langs, true) ? $lang : $default;
            $find->execute([$g['id'], $email]);
            $old = $find->fetch();
            if (!$old) {
                $add->execute([$g['id'], $email, $lang, bin2hex(random_bytes(16)), now_utc(), now_utc()]);
                $report['added']++;
            } elseif ($old['status'] === 'pending') {
                $confirm->execute([now_utc(), $old['id']]);
                $report['confirmed']++;
            } else {
                $report[$old['status'] === 'unsubscribed' ? 'unsubscribed' : 'already']++;
            }
        }
        db()->commit();
        send_json(['ok' => true, 'report' => $report, 'counts' => subscriber_counts((int)$g['id'])]);

    case 'sub.export':
        $u = reader();
        [$g] = require_group($u, gid([]), 'admin', false);
        $st = db()->prepare('SELECT * FROM ' . t('subscribers') . " WHERE group_id = ? AND status = 'active' ORDER BY confirmed_at");
        $st->execute([$g['id']]);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $g['slug'] . '-subscribers-' . gmdate('Y-m-d') . '.csv"');
        header('Cache-Control: no-store');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['email', 'lang', 'browser_lang', 'browser', 'source', 'subscribed_at_utc', 'confirmed_at_utc', 'terms_rev'], ',', '"', '\\');
        foreach ($st->fetchAll() as $s) fputcsv($out, [$s['email'], $s['lang'], $s['browser_lang'], $s['browser'], $s['source'], $s['created_at'], $s['confirmed_at'], $s['terms_rev']], ',', '"', '\\');
        exit;

    // ================================================================ templates

    case 'tpl.list':
        $u = reader();
        [$g] = require_group($u, gid([]), 'user', false);
        $st = db()->prepare('SELECT t.id, t.name, t.doc, t.updated_at, u.name AS author FROM ' . t('templates') . ' t LEFT JOIN ' . t('users') . ' u ON u.id = t.created_by WHERE t.group_id = ? ORDER BY t.updated_at DESC');
        $st->execute([$g['id']]);
        // templates saved by older versions are brought to the current document format (clean_doc), like newsletters
        $own = array_map(fn($t) => ['id' => (int)$t['id'], 'name' => $t['name'], 'author' => $t['author'], 'updatedAt' => iso($t['updated_at']), 'doc' => clean_doc(json_decode($t['doc'], true))], $st->fetchAll());
        send_json(['ok' => true, 'builtin' => array_map(fn($b) => ['doc' => clean_doc($b['doc'])] + $b, builtin_templates(user_lang($u))), 'own' => $own, 'favorites' => fav_templates($u)]);

    case 'tpl.fav':
        // A built-in template marked (or unmarked) as a favourite of this user: it comes first in the pickers
        $u = writer();
        $b = body();
        $id = (string)($b['id'] ?? '');
        if (!builtin_template($id, 'en')) fail('not_found', 404);
        $favs = array_values(array_diff(fav_templates($u), [$id]));
        if (!empty($b['on'])) array_unshift($favs, $id);
        db()->prepare('UPDATE ' . t('users') . ' SET fav_templates = ? WHERE id = ?')->execute([json_out(array_slice($favs, 0, 40)), $u['id']]);
        send_json(['ok' => true, 'favorites' => $favs]);

    case 'tpl.save':
        $u = writer();
        $b = body();
        [$g] = require_group($u, gid($b), 'moderator');
        $name = s($b, 'name', 80);
        if (mb_strlen($name) < 2) fail('name_required', 422);
        $doc = json_out(decode_doc($b['doc'] ?? null));
        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $up = db()->prepare('UPDATE ' . t('templates') . ' SET name = ?, doc = ?, updated_at = ? WHERE id = ? AND group_id = ?');
            $up->execute([$name, $doc, now_utc(), $id, $g['id']]);
        } else {
            $n = db()->prepare('SELECT COUNT(*) FROM ' . t('templates') . ' WHERE group_id = ?');
            $n->execute([$g['id']]);
            if ((int)$n->fetchColumn() >= 100) fail('too_many_templates', 409);
            db()->prepare('INSERT INTO ' . t('templates') . ' (group_id, name, doc, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?)')->execute([$g['id'], $name, $doc, $u['id'], now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
        }
        send_json(['ok' => true, 'id' => $id]);

    case 'tpl.preview':
        // The email of a template being edited, as the group's subscribers would see it
        $u = writer();
        $b = body();
        [$g] = require_group($u, gid($b), 'moderator', false);
        send_json(['ok' => true, 'html' => preview_html(decode_doc($b['doc'] ?? null), $g, '', '', null)]);

    case 'tpl.delete':
        $u = writer();
        $b = body();
        [$g, $role] = require_group($u, gid($b), 'moderator');
        $st = db()->prepare('SELECT created_by FROM ' . t('templates') . ' WHERE id = ? AND group_id = ?');
        $st->execute([(int)($b['id'] ?? 0), $g['id']]);
        $by = $st->fetchColumn();
        if ($by === false) fail('not_found', 404);
        if ((int)$by !== (int)$u['id'] && role_rank($role) < role_rank('publisher')) fail('forbidden', 403);
        db()->prepare('DELETE FROM ' . t('templates') . ' WHERE id = ? AND group_id = ?')->execute([(int)$b['id'], $g['id']]);
        send_json(['ok' => true]);

    // ================================================================ newsletters

    case 'nl.list':
        $u = reader();
        [$g] = require_group($u, gid([]), 'user', false);
        $st = db()->prepare('SELECT * FROM ' . t('newsletters') . ' WHERE group_id = ? ORDER BY COALESCE(scheduled_at, proposed_at, updated_at) DESC LIMIT 500');
        $st->execute([$g['id']]);
        $rows = $st->fetchAll();
        $names = nl_names($rows);
        send_json(['ok' => true, 'newsletters' => array_map(fn($n) => public_nl($n, false, $names), $rows)]);

    case 'nl.get':
        $u = reader();
        [$n, $g, $role] = require_nl($u, (int)($_GET['id'] ?? 0), 'user', false);
        send_json(['ok' => true, 'newsletter' => out_nl((int)$n['id']), 'canEdit' => in_array($n['status'], EDITABLE, true) && role_rank($role) >= role_rank('moderator') && can_edit($n, $u, $role)]);

    case 'nl.create':
        $u = writer();
        $b = body();
        [$g] = require_group($u, gid($b), 'moderator');
        $from = (string)($b['template'] ?? 'b:blank');
        $doc = null;
        if (strpos($from, 'b:') === 0) $doc = builtin_template($from, user_lang($u))['doc'] ?? null;
        elseif (ctype_digit($from)) {
            $st = db()->prepare('SELECT doc FROM ' . t('templates') . ' WHERE id = ? AND group_id = ?');
            $st->execute([(int)$from, $g['id']]);
            $doc = json_decode((string)$st->fetchColumn(), true);
        }
        $doc = clean_doc($doc ?: builtin_template('b:blank', user_lang($u))['doc']);
        $lang = in_array($b['lang'] ?? '', group_langs($g), true) ? $b['lang'] : null;
        db()->prepare('INSERT INTO ' . t('newsletters') . ' (group_id, subject, lang, doc, status, author_id, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)')
            ->execute([$g['id'], s($b, 'subject', 200), $lang, json_out($doc), 'draft', $u['id'], now_utc(), now_utc()]);
        send_json(['ok' => true, 'newsletter' => out_nl((int)db()->lastInsertId())]);

    case 'nl.save':
        // The whole document at once. base: the revision the editor knows (a newer one means someone else saved).
        $u = writer();
        $b = body();
        [$n, $g, $role] = require_nl($u, (int)($b['id'] ?? 0), 'moderator');
        if (!in_array($n['status'], EDITABLE, true)) fail('nl_locked', 409);
        if (!can_edit($n, $u, $role)) fail('forbidden', 403);
        $doc = decode_doc($b['doc'] ?? null);
        $lang = in_array($b['lang'] ?? '', group_langs($g), true) ? $b['lang'] : null;
        $up = db()->prepare('UPDATE ' . t('newsletters') . ' SET subject = ?, preheader = ?, lang = ?, doc = ?, rev = rev + 1, updated_at = ? WHERE id = ? AND rev = ?');
        $up->execute([s($b, 'subject', 200), s($b, 'preheader', 200), $lang, json_out($doc), now_utc(), $n['id'], (int)($b['base'] ?? $n['rev'])]);
        if (!$up->rowCount()) fail('nl_conflict', 409);
        send_json(['ok' => true, 'rev' => (int)$n['rev'] + 1, 'savedAt' => iso(now_utc())]);

    case 'nl.duplicate':
        $u = writer();
        [$n, $g] = require_nl($u, (int)(body()['id'] ?? 0), 'moderator');
        db()->prepare('INSERT INTO ' . t('newsletters') . ' (group_id, subject, preheader, lang, doc, status, author_id, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute([$g['id'], mb_substr($n['subject'], 0, 190), $n['preheader'], $n['lang'], $n['doc'], 'draft', $u['id'], now_utc(), now_utc()]);
        send_json(['ok' => true, 'newsletter' => out_nl((int)db()->lastInsertId())]);

    case 'nl.delete':
        $u = writer();
        [$n, $g, $role] = require_nl($u, (int)(body()['id'] ?? 0), 'moderator');
        if (!in_array($n['status'], array_merge(EDITABLE, ['cancelled']), true)) fail('nl_locked', 409);
        if (!can_edit($n, $u, $role)) fail('forbidden', 403);
        db()->prepare('DELETE FROM ' . t('newsletters') . ' WHERE id = ?')->execute([$n['id']]);
        send_json(['ok' => true]);

    case 'nl.preview':
        // GET: the saved newsletter. POST: the document being edited (not saved yet).
        $u = require_user();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf($u);
            $b = body();
            [$n, $g] = require_nl($u, (int)($b['id'] ?? 0), 'moderator', false);
            $html = preview_html(decode_doc($b['doc'] ?? null), $g, s($b, 'subject', 200), s($b, 'preheader', 200), in_array($b['lang'] ?? '', group_langs($g), true) ? $b['lang'] : null);
        } else {
            [$n, $g] = require_nl($u, (int)($_GET['id'] ?? 0), 'user', false);
            $html = preview_html(clean_doc(json_decode($n['doc'], true)), $g, $n['subject'], $n['preheader'], $n['lang']);
        }
        send_json(['ok' => true, 'html' => $html]);

    case 'nl.test':
        // A copy to the person who asks, from the group's own address
        $u = writer();
        $b = body();
        [$n, $g] = require_nl($u, (int)($b['id'] ?? 0), 'moderator');
        if (too_many_attempts('nl_test', 20, 60)) fail('rate_limited', 429);
        record_attempt('nl_test');
        $mailer = group_mailer($g);
        if (!$mailer) fail('smtp_missing', 409);
        $doc = isset($b['doc']) ? decode_doc($b['doc']) : clean_doc(json_decode($n['doc'], true));
        $subject = s($b, 'subject', 200) ?: $n['subject'];
        $lang = in_array($b['lang'] ?? '', group_langs($g), true) ? $b['lang'] : ($n['lang'] ?: group_langs($g)[0]);
        $mail = render_email($doc, $g, ['lang' => $lang, 'subject' => $subject, 'preheader' => s($b, 'preheader', 200), 'unsubscribe' => '#']);
        $res = smtp_send_all($mailer, [['to' => $u['email'], 'name' => $u['name'], 'subject' => '[TEST] ' . $subject, 'html' => $mail['html'], 'text' => $mail['text']]]);
        if (!$res[0]['ok']) send_json(['ok' => false, 'error' => 'smtp_failed', 'detail' => $res[0]['error']], 502);
        send_json(['ok' => true, 'to' => $u['email']]);

    case 'nl.submit':
        // A moderator proposes a slot; a publisher (or stronger) may schedule it directly
        $u = writer();
        $b = body();
        [$n, $g, $role] = require_nl($u, (int)($b['id'] ?? 0), 'moderator');
        if (!in_array($n['status'], EDITABLE, true)) fail('nl_locked', 409);
        if (!can_edit($n, $u, $role)) fail('forbidden', 403);
        if (trim($n['subject']) === '') fail('subject_required', 422);
        if (doc_is_empty(clean_doc(json_decode($n['doc'], true)))) fail('doc_empty', 422);
        $slot = parse_slot(s($b, 'date', 10), s($b, 'time', 5), $g['timezone']);
        $direct = !empty($b['direct']) && role_rank($role) >= role_rank('publisher');
        if ($direct) {
            if (!group_mailer($g)) fail('smtp_missing', 409);
            db()->prepare('UPDATE ' . t('newsletters') . " SET status = 'scheduled', proposed_at = ?, scheduled_at = ?, submitted_at = ?, reviewer_id = ?, reviewed_at = ?, review_note = '', reminded_at = NULL, updated_at = ? WHERE id = ?")
                ->execute([$slot, $slot, now_utc(), $u['id'], now_utc(), now_utc(), $n['id']]);
        } else {
            db()->prepare('UPDATE ' . t('newsletters') . " SET status = 'pending', proposed_at = ?, scheduled_at = NULL, submitted_at = ?, reviewer_id = NULL, reviewed_at = NULL, review_note = '', reminded_at = NULL, updated_at = ? WHERE id = ?")
                ->execute([$slot, now_utc(), now_utc(), $n['id']]);
            notify_review(nl_row((int)$n['id']), $g, $u);
        }
        send_json(['ok' => true, 'newsletter' => out_nl((int)$n['id'])]);

    case 'nl.withdraw':
        // Back to draft: the author takes a proposal back; a publisher unschedules an approved one
        $u = writer();
        [$n, $g, $role] = require_nl($u, (int)(body()['id'] ?? 0), 'moderator');
        $ok = ($n['status'] === 'pending' && can_edit($n, $u, $role)) || ($n['status'] === 'scheduled' && role_rank($role) >= role_rank('publisher'));
        if (!$ok) fail($n['status'] === 'pending' || $n['status'] === 'scheduled' ? 'forbidden' : 'nl_locked', 409);
        db()->prepare('UPDATE ' . t('newsletters') . " SET status = 'draft', scheduled_at = NULL, updated_at = ? WHERE id = ? AND status IN ('pending','scheduled')")->execute([now_utc(), $n['id']]);
        send_json(['ok' => true, 'newsletter' => out_nl((int)$n['id'])]);

    case 'nl.approve':
        $u = writer();
        $b = body();
        [$n, $g] = require_nl($u, (int)($b['id'] ?? 0), 'publisher');
        if ($n['status'] !== 'pending') fail('nl_locked', 409);
        if (!group_mailer($g)) fail('smtp_missing', 409);
        $slot = ($b['date'] ?? '') !== '' ? parse_slot(s($b, 'date', 10), s($b, 'time', 5), $g['timezone']) : $n['proposed_at'];
        if (strtotime($slot . ' UTC') < next_slot(time())) fail('slot_past', 422);
        $up = db()->prepare('UPDATE ' . t('newsletters') . " SET status = 'scheduled', scheduled_at = ?, reviewer_id = ?, reviewed_at = ?, review_note = ?, updated_at = ? WHERE id = ? AND status = 'pending'");
        $up->execute([$slot, $u['id'], now_utc(), s($b, 'note', 500), now_utc(), $n['id']]);
        if ($up->rowCount()) notify_author(nl_row((int)$n['id']), $g, 'approved', $u);
        send_json(['ok' => true, 'newsletter' => out_nl((int)$n['id'])]);

    case 'nl.reject':
        $u = writer();
        $b = body();
        [$n, $g] = require_nl($u, (int)($b['id'] ?? 0), 'publisher');
        if ($n['status'] !== 'pending') fail('nl_locked', 409);
        $up = db()->prepare('UPDATE ' . t('newsletters') . " SET status = 'rejected', reviewer_id = ?, reviewed_at = ?, review_note = ?, updated_at = ? WHERE id = ? AND status = 'pending'");
        $up->execute([$u['id'], now_utc(), s($b, 'note', 500), now_utc(), $n['id']]);
        if ($up->rowCount()) notify_author(nl_row((int)$n['id']), $g, 'rejected', $u);
        send_json(['ok' => true, 'newsletter' => out_nl((int)$n['id'])]);

    case 'nl.reschedule':
        // A new slot: publishers move a scheduled newsletter; a proposal can be moved by its author or a publisher
        // (it stays a proposal, and the reminder is sent again for the new time)
        $u = writer();
        $b = body();
        [$n, $g, $role] = require_nl($u, (int)($b['id'] ?? 0), 'moderator');
        $pub = role_rank($role) >= role_rank('publisher');
        if (!($n['status'] === 'scheduled' && $pub) && !($n['status'] === 'pending' && ($pub || can_edit($n, $u, $role)))) fail(in_array($n['status'], ['scheduled', 'pending'], true) ? 'forbidden' : 'nl_locked', 409);
        $slot = parse_slot(s($b, 'date', 10), s($b, 'time', 5), $g['timezone']);
        if ($n['status'] === 'scheduled') {
            db()->prepare('UPDATE ' . t('newsletters') . " SET scheduled_at = ?, updated_at = ? WHERE id = ? AND status = 'scheduled'")->execute([$slot, now_utc(), $n['id']]);
        } else {
            db()->prepare('UPDATE ' . t('newsletters') . " SET proposed_at = ?, reminded_at = NULL, updated_at = ? WHERE id = ? AND status = 'pending'")->execute([$slot, now_utc(), $n['id']]);
        }
        send_json(['ok' => true, 'newsletter' => out_nl((int)$n['id'])]);

    case 'nl.cancel':
        $u = writer();
        [$n] = require_nl($u, (int)(body()['id'] ?? 0), 'publisher');
        if (!in_array($n['status'], ['pending', 'scheduled'], true)) fail('nl_locked', 409);
        db()->prepare('UPDATE ' . t('newsletters') . " SET status = 'cancelled', updated_at = ? WHERE id = ?")->execute([now_utc(), $n['id']]);
        send_json(['ok' => true, 'newsletter' => out_nl((int)$n['id'])]);

    // ================================================================ images

    case 'img.upload':
        // The browser already resized it (max 1200 px wide); here it is checked and stored
        $u = writer();
        $b = body();
        [$g] = require_group($u, gid($b), 'moderator');
        if (too_many_attempts('img', 60, 60)) fail('rate_limited', 429);
        $data = (string)($b['data'] ?? '');
        if (!preg_match('~^data:image/(jpeg|png|gif|webp);base64,([A-Za-z0-9+/=]+)$~', $data, $m)) fail('image_invalid', 422);
        $bin = base64_decode($m[2], true);
        if ($bin === false || strlen($bin) > MAX_IMAGE_BYTES) fail('image_too_big', 413);
        $info = @getimagesizefromstring($bin);
        if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true) || $info[0] > 2400 || $info[1] > 4000) fail('image_invalid', 422);
        record_attempt('img');
        $id = substr(hash('sha256', $g['id'] . ':' . $bin), 0, 20);
        db()->prepare('INSERT IGNORE INTO ' . t('images') . ' (id, group_id, mime, width, height, bytes, data, created_by, created_at) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute([$id, $g['id'], $info['mime'], $info[0], $info[1], strlen($bin), $bin, $u['id'], now_utc()]);
        send_json(['ok' => true, 'src' => 'img:' . $id, 'url' => image_url('img:' . $id), 'width' => $info[0], 'height' => $info[1]]);

    // ================================================================ public: subscription pages and widget

    case 'pub.subscribe':
        only('POST');
        $b = body();
        $g = group_by_slug(s($b, 'group', 40));
        if (!$g || $g['status'] !== 'active') fail('group_not_found', 404);
        if (!group_mailer($g)) fail('not_ready', 409);
        if (too_many_attempts('subscribe', 8, 60)) fail('rate_limited', 429);
        $email = mb_strtolower(s($b, 'email', 190));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('email_invalid', 422);
        if (empty($b['acceptPlatform']) || ((string)$g['terms'] !== '' && empty($b['acceptGroup']))) fail('accept_required', 422);
        if (s($b, 'website', 100) !== '') send_json(['ok' => true, 'checkEmail' => true]); // honeypot: bots fill every field
        record_attempt('subscribe');
        $langs = group_langs($g);
        $lang = in_array($b['lang'] ?? '', $langs, true) ? $b['lang'] : $langs[0];
        $browserLang = lang_code(s($b, 'browserLang', 20)) ?: lang_code((string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));
        subscribe_request($g, $email, $lang, substr($browserLang, 0, 5), browser_name((string)($_SERVER['HTTP_USER_AGENT'] ?? '')), ($b['source'] ?? '') === 'widget' ? 'widget' : 'page');
        send_json(['ok' => true, 'checkEmail' => true]);

    case 'pub.confirm':
        only('POST');
        if (too_many_attempts('confirm', 20, 15)) fail('rate_limited', 429);
        $token = (string)(body()['token'] ?? '');
        $st = db()->prepare('SELECT s.*, g.name AS group_name FROM ' . t('subscribers') . ' s JOIN ' . t('groups') . " g ON g.id = s.group_id WHERE s.confirm_hash = ? AND s.status = 'pending' AND s.confirm_sent_at > ?");
        $st->execute([hash('sha256', $token), gmdate('Y-m-d H:i:s', time() - 7 * 86400)]);
        $sub = $st->fetch();
        if (!$sub) {
            record_attempt('confirm');
            fail('token_invalid', 410);
        }
        db()->prepare('UPDATE ' . t('subscribers') . " SET status = 'active', confirmed_at = ?, confirm_hash = NULL, unsubscribed_at = NULL, unsub_reason = '' WHERE id = ?")->execute([now_utc(), $sub['id']]);
        send_json(['ok' => true, 'email' => $sub['email']]);

    case 'pub.unsubscribe':
        // From the link in a newsletter (or in the confirmation email): the token proves the address
        only('POST');
        if (too_many_attempts('unsub', 30, 15)) fail('rate_limited', 429);
        $b = body();
        $sub = subscriber_by_token((string)($b['token'] ?? ''));
        if (!$sub) {
            record_attempt('unsub');
            fail('token_invalid', 410);
        }
        $reason = in_array($b['reason'] ?? '', UNSUB_REASONS, true) ? $b['reason'] : '';
        $note = s($b, 'note', 400);
        unsubscribe($sub, trim($reason . ($note !== '' ? ': ' . $note : '')) ?: (string)$sub['unsub_reason']);
        send_json(['ok' => true]);

    case 'pub.unsubRequest':
        // Without the link: the address only gets an email with it (nobody can unsubscribe someone else)
        only('POST');
        $b = body();
        $g = group_by_slug(s($b, 'group', 40));
        if (!$g) fail('group_not_found', 404);
        if (too_many_attempts('unsub_req', 6, 60)) fail('rate_limited', 429);
        $email = mb_strtolower(s($b, 'email', 190));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('email_invalid', 422);
        record_attempt('unsub_req');
        $st = db()->prepare('SELECT * FROM ' . t('subscribers') . " WHERE group_id = ? AND email = ? AND status <> 'unsubscribed'");
        $st->execute([$g['id'], $email]);
        $sub = $st->fetch();
        if ($sub) {
            $reason = in_array($b['reason'] ?? '', UNSUB_REASONS, true) ? $b['reason'] : '';
            $note = s($b, 'note', 400);
            if ($reason !== '' || $note !== '') db()->prepare('UPDATE ' . t('subscribers') . ' SET unsub_reason = ? WHERE id = ?')->execute([trim($reason . ($note !== '' ? ': ' . $note : '')), $sub['id']]);
            $lang = $sub['lang'];
            $P = pub_t($lang);
            $url = unsubscribe_url($g, $sub['token']) . '&lang=' . $lang;
            if (config_value('debug', false)) error_log("OpenNewsletter unsubscribe link for $email: $url");
            subscriber_mail($g, $email, $lang, fill($P['mail_unsub_subject'], ['group' => $g['name']]), fill($P['mail_unsub_body'], ['group' => $g['name']]), $url, $P['unsub_button']);
        }
        send_json(['ok' => true, 'checkEmail' => true]);

    // ================================================================ master admin (whole platform, read-only on groups)

    case 'master.overview':
        only('GET');
        require_master();
        $groups = db()->query('SELECT g.id, g.slug, g.name, g.status, g.from_email, g.created_at, g.timezone, g.langs, (g.smtp_sealed IS NOT NULL) AS has_smtp, u.name AS owner, u.email AS owner_email,
            (SELECT COUNT(*) FROM ' . t('members') . ' m WHERE m.group_id = g.id) AS members,
            (SELECT COUNT(*) FROM ' . t('subscribers') . " s WHERE s.group_id = g.id AND s.status = 'active') AS subscribers,
            (SELECT COUNT(*) FROM " . t('newsletters') . " n WHERE n.group_id = g.id AND n.status = 'sent') AS sent,
            (SELECT COUNT(*) FROM " . t('newsletters') . " n WHERE n.group_id = g.id AND n.status IN ('pending','scheduled','sending')) AS upcoming
            FROM " . t('groups') . ' g LEFT JOIN ' . t('users') . ' u ON u.id = g.owner_id ORDER BY g.created_at DESC')->fetchAll();
        $totals = [
            'users' => (int)db()->query('SELECT COUNT(*) FROM ' . t('users'))->fetchColumn(),
            'groups' => count($groups),
            'subscribers' => (int)db()->query('SELECT COUNT(*) FROM ' . t('subscribers') . " WHERE status = 'active'")->fetchColumn(),
            'sent' => (int)db()->query('SELECT COALESCE(SUM(sent_count),0) FROM ' . t('newsletters'))->fetchColumn(),
        ];
        send_json(['ok' => true, 'totals' => $totals, 'groups' => array_map(fn($g) => [
            'id' => (int)$g['id'], 'slug' => $g['slug'], 'name' => $g['name'], 'status' => $g['status'], 'fromEmail' => $g['from_email'], 'sendingReady' => (bool)$g['has_smtp'],
            'owner' => $g['owner'], 'ownerEmail' => $g['owner_email'], 'members' => (int)$g['members'], 'subscribers' => (int)$g['subscribers'], 'sent' => (int)$g['sent'],
            'upcoming' => (int)$g['upcoming'], 'createdAt' => iso($g['created_at']), 'langs' => json_decode($g['langs'], true), 'timezone' => $g['timezone']], $groups)]);

    case 'master.group':
        // Everything about one group except its SMTP settings: only the sending address, read-only
        only('GET');
        require_master();
        $g = group_row((int)($_GET['group'] ?? 0));
        if (!$g) fail('group_not_found', 404);
        $info = public_group($g, 'user');
        unset($info['smtp'], $info['smtpCheckedAt']);
        $st = db()->prepare('SELECT u.id, u.name, u.email, m.role, m.created_at FROM ' . t('members') . ' m JOIN ' . t('users') . ' u ON u.id = m.user_id WHERE m.group_id = ? ORDER BY FIELD(m.role, \'owner\',\'admin\',\'publisher\',\'moderator\',\'user\'), u.name');
        $st->execute([$g['id']]);
        $members = array_map(fn($m) => ['id' => (int)$m['id'], 'name' => $m['name'], 'email' => $m['email'], 'role' => $m['role'], 'since' => iso($m['created_at'])], $st->fetchAll());
        $st = db()->prepare('SELECT * FROM ' . t('newsletters') . ' WHERE group_id = ? ORDER BY updated_at DESC LIMIT 200');
        $st->execute([$g['id']]);
        $rows = $st->fetchAll();
        $names = nl_names($rows);
        $st = db()->prepare('SELECT * FROM ' . t('subscribers') . ' WHERE group_id = ? ORDER BY created_at DESC LIMIT 500');
        $st->execute([$g['id']]);
        $reasons = db()->prepare('SELECT unsub_reason, COUNT(*) n FROM ' . t('subscribers') . " WHERE group_id = ? AND status = 'unsubscribed' AND unsub_reason <> '' GROUP BY unsub_reason ORDER BY n DESC LIMIT 20");
        $reasons->execute([$g['id']]);
        send_json(['ok' => true, 'group' => $info, 'members' => $members, 'newsletters' => array_map(fn($n) => public_nl($n, false, $names), $rows),
            'subscribers' => array_map('subscriber_public', $st->fetchAll()), 'reasons' => $reasons->fetchAll()]);

    case 'master.status':
        $u = writer();
        if (!is_master($u)) fail('forbidden', 403);
        $b = body();
        $status = ($b['status'] ?? '') === 'disabled' ? 'disabled' : 'active';
        db()->prepare('UPDATE ' . t('groups') . ' SET status = ?, updated_at = ? WHERE id = ?')->execute([$status, now_utc(), (int)($b['group'] ?? 0)]);
        send_json(['ok' => true]);

    case 'master.preview':
        only('GET');
        require_master();
        $n = nl_row((int)($_GET['id'] ?? 0));
        if (!$n) fail('nl_not_found', 404);
        $g = group_row((int)$n['group_id']);
        send_json(['ok' => true, 'html' => preview_html(clean_doc(json_decode($n['doc'], true)), $g, $n['subject'], $n['preheader'], $n['lang'])]);

    default:
        fail('not_found', 404);
}
