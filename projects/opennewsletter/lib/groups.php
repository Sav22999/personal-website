<?php
/**
 * OpenNewsletter — groups, roles and subscribers.
 *
 * Roles, from the weakest (each one can do everything the ones before it can):
 *   user       sees the group's newsletters and numbers
 *   moderator  writes newsletters and templates, proposes a date and time (30-minute slots), sends tests to itself
 *   publisher  approves or rejects the proposals, reschedules or cancels them, sees the subscribers
 *   admin      configures the outgoing address, the subscription pages, the terms and languages, manages subscribers
 *              and the members up to publisher
 *   owner      who created the group: also names and removes admins, and can delete the group
 * The master admins of the platform (credentials.php) see every group read-only, never its SMTP settings.
 */

declare(strict_types=1);

function role_rank(string $role): int
{
    $i = array_search($role, ROLES, true);
    return $i === false ? -1 : (int)$i;
}

function group_row(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('groups') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function group_by_slug(string $slug): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('groups') . ' WHERE slug = ?');
    $st->execute([mb_strtolower($slug)]);
    return $st->fetch() ?: null;
}

function member_role(int $groupId, int $userId): ?string
{
    $st = db()->prepare('SELECT role FROM ' . t('members') . ' WHERE group_id = ? AND user_id = ?');
    $st->execute([$groupId, $userId]);
    $r = $st->fetchColumn();
    return $r === false ? null : (string)$r;
}

/**
 * The group a call works on, checked against the role it needs. Returns [group, role].
 * A disabled group (by a master admin) can still be read but not changed.
 */
function require_group(array $u, int $groupId, string $minRole, bool $write = true): array
{
    $g = $groupId ? group_row($groupId) : null;
    if (!$g) fail('group_not_found', 404);
    $role = member_role((int)$g['id'], (int)$u['id']);
    if ($role === null || role_rank($role) < role_rank($minRole)) fail($role === null ? 'group_not_found' : 'forbidden', $role === null ? 404 : 403);
    if ($write && $g['status'] !== 'active') fail('group_disabled', 423);
    return [$g, $role];
}

function group_langs(array $g): array
{
    $l = json_decode((string)$g['langs'], true);
    $l = is_array($l) ? array_values(array_intersect($l, SUB_LANGS)) : [];
    return $l ?: ['en'];
}

/** Customisation of the subscription pages (and of the widget). */
/** Looks of the subscription page and widget: a card, no frame, a coloured outline, a full-colour card, one line. */
const PAGE_STYLES = ['card', 'minimal', 'outline', 'bold', 'compact'];

/**
 * Typefaces of the subscription page and widget: [name, CSS family, folder in fonts/ with regular.ttf and bold.ttf
 * (null: already loaded or installed on every device)]. The page loads only the one chosen. Same list in app.js.
 */
const PAGE_FONTS = [
    'inter' => ['Inter + Poppins', null, null],
    'poppins' => ['Poppins', '"Poppins", sans-serif', null],
    'montserrat' => ['Montserrat', '"Montserrat", sans-serif', 'montserrat'],
    'opensans' => ['Open Sans', '"Open Sans", sans-serif', 'opensans'],
    'sourcesans' => ['Source Sans 3', '"Source Sans 3", sans-serif', 'sourcesans'],
    'lora' => ['Lora', '"Lora", Georgia, serif', 'lora'],
    'playfair' => ['Playfair Display', '"Playfair Display", Georgia, serif', 'playfair'],
    'system' => ['System', '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif', null],
    'georgia' => ['Georgia', 'Georgia, "Times New Roman", serif', null],
    'mono' => ['Monospace', 'ui-monospace, "SF Mono", Menlo, Consolas, "Courier New", monospace', null],
];

function default_page(): array
{
    return ['title' => '', 'intro' => '', 'button' => '', 'success' => '', 'accent' => '#5B3DF5', 'logo' => '', 'theme' => 'light', 'shape' => 'round', 'style' => 'card', 'font' => 'inter', 'showLang' => true, 'showCount' => false];
}

function group_page(array $g): array
{
    $p = json_decode((string)($g['page'] ?? ''), true);
    return array_merge(default_page(), is_array($p) ? array_intersect_key($p, default_page()) : []);
}

function clean_page($p): array
{
    require_once __DIR__ . '/render.php';
    $p = is_array($p) ? $p : [];
    $d = default_page();
    return [
        'title' => clean_text($p['title'] ?? '', 120), 'intro' => clean_text($p['intro'] ?? '', 600), 'button' => clean_text($p['button'] ?? '', 40),
        'success' => clean_text($p['success'] ?? '', 400), 'accent' => clean_color($p['accent'] ?? '', $d['accent']),
        'logo' => clean_url($p['logo'] ?? '', false), 'theme' => in_array($p['theme'] ?? '', ['light', 'dark', 'auto'], true) ? $p['theme'] : 'light',
        'shape' => in_array($p['shape'] ?? '', ['round', 'soft', 'square'], true) ? $p['shape'] : 'round',
        'style' => in_array($p['style'] ?? '', PAGE_STYLES, true) ? $p['style'] : 'card',
        'font' => isset(PAGE_FONTS[$p['font'] ?? '']) ? $p['font'] : 'inter',
        'showLang' => !empty($p['showLang']), 'showCount' => !empty($p['showCount']),
    ];
}

/** What a member of the group sees about it. */
function public_group(array $g, string $role): array
{
    $counts = subscriber_counts((int)$g['id']);
    $out = [
        'id' => (int)$g['id'], 'slug' => $g['slug'], 'name' => $g['name'], 'description' => $g['description'], 'status' => $g['status'],
        'role' => $role, 'timezone' => $g['timezone'], 'langs' => group_langs($g), 'page' => group_page($g),
        'terms' => (string)($g['terms'] ?? ''), 'fromEmail' => $g['from_email'], 'fromName' => $g['from_name'], 'replyTo' => $g['reply_to'],
        'sendingReady' => !empty($g['smtp_sealed']) && $g['from_email'] !== '', 'ratePerHour' => (int)($g['rate_per_hour'] ?? 200), 'smtpCheckedAt' => iso($g['smtp_checked_at']),
        'subscribers' => $counts, 'createdAt' => iso($g['created_at']),
        'links' => [
            'subscribe' => app_url() . 'subscribe.php?g=' . rawurlencode($g['slug']),
            'unsubscribe' => app_url() . 'unsubscribe.php?g=' . rawurlencode($g['slug']),
            'widget' => app_url() . 'widget.js',
        ],
    ];
    // SMTP settings without the password, only for admins (to edit them)
    if (role_rank($role) >= role_rank('admin') && !empty($g['smtp_sealed'])) {
        $s = unseal((string)$g['smtp_sealed']) ?: [];
        $out['smtp'] = ['host' => $s['smtp_host'] ?? '', 'port' => (int)($s['smtp_port'] ?? 0), 'encryption' => $s['smtp_encryption'] ?? 'ssl', 'user' => $s['smtp_user'] ?? '', 'hasPassword' => ($s['smtp_password'] ?? '') !== ''];
    }
    return $out;
}

function subscriber_counts(int $groupId): array
{
    $st = db()->prepare('SELECT status, lang, COUNT(*) n FROM ' . t('subscribers') . ' WHERE group_id = ? GROUP BY status, lang');
    $st->execute([$groupId]);
    $out = ['active' => 0, 'pending' => 0, 'unsubscribed' => 0, 'byLang' => []];
    foreach ($st->fetchAll() as $r) {
        $out[$r['status']] += (int)$r['n'];
        if ($r['status'] === 'active') $out['byLang'][$r['lang']] = (int)$r['n'];
    }
    return $out;
}

/** "Circolo Scacchi Milano" -> "circolo-scacchi-milano", or "-2" and so on when taken. */
function suggest_slug(string $name): string
{
    $base = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name;
    $base = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($base)) ?? '', '-');
    $base = substr($base, 0, 32) ?: 'newsletter';
    if (strlen($base) < 3) $base .= '-news';
    $st = db()->prepare('SELECT 1 FROM ' . t('groups') . ' WHERE slug = ?');
    for ($i = 1; $i < 50; $i++) {
        $try = $i === 1 ? $base : $base . '-' . $i;
        $st->execute([$try]);
        if (!$st->fetchColumn()) return $try;
    }
    return $base . '-' . bin2hex(random_bytes(3));
}

function clean_slug(string $raw, int $self = 0): string
{
    $slug = mb_strtolower(trim($raw));
    if (!preg_match('/^[a-z0-9][a-z0-9-]{1,38}[a-z0-9]$/', $slug) || strpos($slug, '--') !== false) fail('slug_invalid', 422);
    $st = db()->prepare('SELECT 1 FROM ' . t('groups') . ' WHERE slug = ? AND id <> ?');
    $st->execute([$slug, $self]);
    if ($st->fetchColumn()) fail('slug_taken', 409);
    return $slug;
}

/** Turns the invitations waiting for this address into memberships (once the address is confirmed). */
function accept_invites(array $user): void
{
    if (empty($user['email_verified_at'])) return;
    $st = db()->prepare('SELECT * FROM ' . t('invites') . ' WHERE email = ?');
    $st->execute([mb_strtolower((string)$user['email'])]);
    foreach ($st->fetchAll() as $inv) {
        db()->prepare('INSERT IGNORE INTO ' . t('members') . ' (group_id, user_id, role, created_at) VALUES (?,?,?,?)')
            ->execute([$inv['group_id'], $user['id'], $inv['role'], now_utc()]);
        db()->prepare('DELETE FROM ' . t('invites') . ' WHERE id = ?')->execute([$inv['id']]);
    }
}

/** Members of a group with the given role or stronger (for the approval emails). */
function members_with_role(int $groupId, string $minRole): array
{
    $roles = array_slice(ROLES, role_rank($minRole));
    $in = implode(',', array_fill(0, count($roles), '?'));
    $st = db()->prepare('SELECT u.id, u.name, u.email, u.ui_lang FROM ' . t('members') . ' m JOIN ' . t('users') . " u ON u.id = m.user_id
        WHERE m.group_id = ? AND m.role IN ($in) AND u.status = 'active' AND u.email_verified_at IS NOT NULL");
    $st->execute(array_merge([$groupId], $roles));
    return $st->fetchAll();
}

// ---------------------------------------------------------------- subscribers

/** Messages from a group's own SMTP, sent after the response (like the platform's). */
function queue_group_mail(array $mailer, array $message): void
{
    static $queues = null;
    if (PHP_SAPI === 'cli') {
        $r = smtp_send_all($mailer, [$message]);
        if (!$r[0]['ok']) error_log('OpenNewsletter group mail not sent: ' . $r[0]['error']);
        return;
    }
    if ($queues === null) {
        $queues = new ArrayObject();
        register_shutdown_function(function () use ($queues) {
            if (!count($queues)) return;
            if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request();
            ignore_user_abort(true);
            @set_time_limit(60);
            foreach ($queues as $q) {
                foreach (smtp_send_all($q['mailer'], [$q['message']]) as $r) {
                    if (!$r['ok']) error_log('OpenNewsletter group mail not sent: ' . $r['error']);
                }
            }
        });
    }
    $queues->append(['mailer' => $mailer, 'message' => $message]);
}

/** A short email from the group to one subscriber (double opt-in, unsubscription confirm). */
function subscriber_mail(array $g, string $to, string $lang, string $subject, string $body, string $url, string $button): void
{
    $mailer = group_mailer($g);
    if (!$mailer) return;
    $page = group_page($g);
    $logo = $page['logo'] ? image_url($page['logo']) : '';
    $html = '<!DOCTYPE html><html lang="' . e($lang) . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($subject) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#F4F3F8;font-family:Helvetica,Arial,sans-serif;color:#1F1D2E">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F3F8"><tr><td align="center" style="padding:32px 16px">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:16px"><tr><td style="padding:28px">'
        . ($logo ? '<img src="' . e($logo) . '" alt="" height="44" style="display:block;height:44px;width:auto;margin:0 0 18px;border:0">' : '')
        . '<h1 style="margin:0 0 12px;font-size:21px">' . e($g['name']) . '</h1>'
        . '<p style="margin:0 0 22px;font-size:15px;line-height:1.55">' . e($body) . '</p>'
        . mail_button($url, $button, $page['accent'])
        . '</td></tr></table>'
        . '<p style="margin:16px 0 0;font-size:12px;color:#77748A">' . e(pub_t($lang)['powered']) . ' <a href="' . e(app_url()) . '" style="color:#77748A">OpenNewsletter</a></p>'
        . '</td></tr></table></body></html>';
    queue_group_mail($mailer, ['to' => $to, 'name' => '', 'subject' => $subject, 'html' => $html, 'text' => "$body\n\n$url", 'headers' => ['Auto-Submitted: auto-generated']]);
}

/**
 * Asks to subscribe an address. Always the same answer (nobody learns who is subscribed): a new or pending address
 * gets the confirmation link; an active one gets nothing; an unsubscribed one may subscribe again.
 */
function subscribe_request(array $g, string $email, string $lang, string $browserLang, string $browser, string $source): void
{
    $st = db()->prepare('SELECT * FROM ' . t('subscribers') . ' WHERE group_id = ? AND email = ?');
    $st->execute([$g['id'], $email]);
    $row = $st->fetch();
    if ($row && $row['status'] === 'active') return;
    // at most one confirmation email every 10 minutes for the same address
    if ($row && $row['confirm_sent_at'] && strtotime($row['confirm_sent_at'] . ' UTC') > time() - 600) return;
    $confirm = bin2hex(random_bytes(32));
    if ($row) {
        db()->prepare('UPDATE ' . t('subscribers') . " SET status = 'pending', lang = ?, browser_lang = ?, browser = ?, source = ?, confirm_hash = ?, confirm_sent_at = ?, terms_rev = ?, created_at = IF(status = 'unsubscribed', ?, created_at) WHERE id = ?")
            ->execute([$lang, $browserLang, $browser, $source, hash('sha256', $confirm), now_utc(), $g['terms_rev'], now_utc(), $row['id']]);
    } else {
        db()->prepare('INSERT INTO ' . t('subscribers') . ' (group_id, email, lang, browser_lang, browser, source, status, token, confirm_hash, confirm_sent_at, terms_rev, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$g['id'], $email, $lang, $browserLang, $browser, $source, 'pending', bin2hex(random_bytes(16)), hash('sha256', $confirm), now_utc(), $g['terms_rev'], now_utc()]);
    }
    $P = pub_t($lang);
    $url = app_url() . 'subscribe.php?g=' . rawurlencode($g['slug']) . '&lang=' . $lang . '&confirm=' . $confirm;
    if (config_value('debug', false)) error_log("OpenNewsletter confirm link for $email: $url");
    subscriber_mail($g, $email, $lang, fill($P['mail_confirm_subject'], ['group' => $g['name']]), fill($P['mail_confirm_body'], ['group' => $g['name']]), $url, $P['confirm_button']);
}

function subscriber_by_token(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) return null;
    $st = db()->prepare('SELECT * FROM ' . t('subscribers') . ' WHERE token = ?');
    $st->execute([$token]);
    return $st->fetch() ?: null;
}

function unsubscribe(array $sub, string $reason): void
{
    if ($sub['status'] === 'unsubscribed') return;
    db()->prepare('UPDATE ' . t('subscribers') . " SET status = 'unsubscribed', unsubscribed_at = ?, unsub_reason = ?, confirm_hash = NULL WHERE id = ?")
        ->execute([now_utc(), mb_substr($reason, 0, 500), $sub['id']]);
    // newsletters still leaving skip this address
    db()->prepare('DELETE FROM ' . t('deliveries') . " WHERE subscriber_id = ? AND status = 'queued'")->execute([$sub['id']]);
}

const UNSUB_REASONS = ['too_many', 'not_relevant', 'never', 'other'];
