<?php
/**
 * OpenNewsletter — public unsubscription page.
 *   unsubscribe.php?t=TOKEN     from the link of a newsletter: one button (and an optional reason)
 *   unsubscribe.php?g=SLUG      without the link: email + optional reason; the address gets a link to confirm
 * A POST with "List-Unsubscribe=One-Click" (RFC 8058, sent by Gmail, Apple Mail…) unsubscribes at once.
 */

declare(strict_types=1);

require __DIR__ . '/lib/public-page.php';

public_headers(false);
if (!credentials()) {
    http_response_code(503);
    exit('OpenNewsletter is not installed.');
}

$token = preg_replace('/[^a-f0-9]/', '', (string)($_GET['t'] ?? ''));
$sub = $token !== '' ? subscriber_by_token($token) : null;

// One-click unsubscribe from the mail client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['List-Unsubscribe'] ?? '') === 'One-Click') {
    if ($sub) unsubscribe($sub, 'one-click');
    header('Content-Type: text/plain');
    echo $sub ? 'unsubscribed' : 'not found';
    exit;
}

$g = $sub ? group_row((int)$sub['group_id']) : group_by_slug((string)($_GET['g'] ?? ''));
if (!$g) not_found_page(pub_lang(PUB_LANGS), false);

$langs = group_langs($g);
$lang = $sub && !isset($_GET['lang']) ? $sub['lang'] : pub_lang($langs);
$P = pub_t($lang);
$vars = ['group' => $g['name'], 'email' => $sub['email'] ?? ''];

page_open($g, $lang, fill($P['unsub_title'], $vars), false);
echo '<div id="pub" data-strings="' . page_strings($lang) . '">';

$reasons = '<fieldset class="pub-reasons"><legend>' . e($P['reason']) . '</legend>';
foreach (UNSUB_REASONS as $r) $reasons .= '<label class="pub-check-row"><input type="radio" name="reason" value="' . $r . '"><span>' . e($P['reason_' . $r]) . '</span></label>';
$reasons .= '<label class="pub-field"><span>' . e($P['reason_note']) . '</span><textarea name="note" rows="2" maxlength="400"></textarea></label></fieldset>';

echo '<section data-step="form"><h1>' . e(fill($P['unsub_title'], $vars)) . '</h1>';
if ($sub && $sub['status'] === 'unsubscribed') {
    echo '<p>' . e(fill($P['unsub_done_body'], $vars)) . '</p><a class="pub-btn pub-btn-ghost" href="subscribe.php?g=' . e(rawurlencode($g['slug'])) . '&lang=' . e($lang) . '">' . e($P['resubscribe']) . '</a>';
} elseif ($sub) {
    echo '<form class="pub-form" data-unsub-token="' . e($token) . '" novalidate><p>' . e(fill($P['unsub_token'], $vars)) . '</p>' . $reasons
        . '<button class="pub-btn" type="submit">' . e($P['unsub_button']) . '</button><p class="pub-error" role="alert" hidden></p></form>';
} else {
    echo '<form class="pub-form" data-unsub-group="' . e($g['slug']) . '" novalidate><p>' . e($P['unsub_intro']) . '</p>'
        . '<label class="pub-field"><span>' . e($P['email']) . '</span><input type="email" name="email" required autocomplete="email" maxlength="190"></label>' . $reasons
        . '<button class="pub-btn" type="submit">' . e($P['unsub_button']) . '</button><p class="pub-error" role="alert" hidden></p></form>';
}
echo '</section>';
echo '<section data-step="done" hidden><div class="pub-check" aria-hidden="true"></div><h1>' . e($P['unsub_done_title']) . '</h1><p>' . e(fill($P['unsub_done_body'], $vars)) . '</p>'
    . '<a class="pub-btn pub-btn-ghost" href="subscribe.php?g=' . e(rawurlencode($g['slug'])) . '&lang=' . e($lang) . '">' . e($P['resubscribe']) . '</a></section>';
echo '<section data-step="sent" hidden><div class="pub-check pub-mail" aria-hidden="true"></div><p>' . e($P['unsub_check']) . '</p></section>';
echo '</div>';
page_close($lang, false);
