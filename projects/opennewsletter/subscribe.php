<?php
/**
 * OpenNewsletter — public subscription page of a group.
 *   subscribe.php?g=SLUG                  the form (email, language, terms)
 *   subscribe.php?g=SLUG&embed=1          the same, compact, inside the widget's iframe (widget.js)
 *   subscribe.php?g=SLUG&confirm=TOKEN    the double opt-in: a button confirms (mail scanners that open links do nothing)
 * The language comes from ?lang= or the browser, among the group's languages; the browser's language and name are
 * recorded with the subscription.
 */

declare(strict_types=1);

require __DIR__ . '/lib/public-page.php';

$embed = !empty($_GET['embed']);
public_headers($embed);
if (!credentials()) {
    http_response_code(503);
    exit('OpenNewsletter is not installed.');
}
remember_app_url();

$g = group_by_slug((string)($_GET['g'] ?? ''));
if (!$g || $g['status'] !== 'active') not_found_page(pub_lang(PUB_LANGS), $embed);

$langs = group_langs($g);
$lang = pub_lang($langs);
$P = pub_t($lang);
$preview = preview_page($g); // live preview of unsaved settings (group admins only)
$page = $preview ?: group_page($g);
$vars = ['group' => $g['name']];
$title = $page['title'] ?: fill($P['sub_title'], $vars);
$confirm = (string)($_GET['confirm'] ?? '');

page_open($g, $lang, $title, $embed, $preview);
echo '<div id="pub" data-strings="' . page_strings($lang) . '">';

if ($confirm !== '') {
    // ---- double opt-in
    echo '<section data-step="confirm"><h1>' . e($P['confirm_title']) . '</h1><p>' . e(fill($P['confirm_body'], $vars)) . '</p>'
        . '<button class="pub-btn" type="button" data-confirm="' . e(preg_replace('/[^a-f0-9]/', '', $confirm)) . '">' . e($P['confirm_button']) . '</button>'
        . '<p class="pub-error" hidden></p></section>'
        . '<section data-step="done" hidden><div class="pub-check" aria-hidden="true"></div><h1>' . e($P['confirmed_title']) . '</h1>'
        . '<p data-done-text="' . e($page['success'] ?: fill($P['confirmed_body'], $vars)) . '"></p></section>';
} elseif (!group_mailer($g)) {
    echo '<h1>' . e($title) . '</h1><p>' . e($P['not_ready']) . '</p>';
} else {
    // ---- the form
    echo '<section data-step="form"><h1>' . e($title) . '</h1>';
    echo '<p class="pub-intro">' . nl2br(e($page['intro'] ?: ($g['description'] ?: $P['sub_intro']))) . '</p>';
    if ($page['showCount']) {
        $n = subscriber_counts((int)$g['id'])['active'];
        if ($n >= 10) echo '<p class="pub-count"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.3c2.2.7 3.5 2.8 3.5 5.7"/></svg> ' . e(number_format($n, 0, ',', '.')) . '</p>';
    }
    echo '<form class="pub-form" data-group="' . e($g['slug']) . '" data-source="' . ($embed ? 'widget' : 'page') . '" novalidate>';
    $compact = (!$preview && in_array($_GET['style'] ?? '', PAGE_STYLES, true) ? $_GET['style'] : $page['style']) === 'compact';
    $emailField = '<label class="pub-field"><span>' . e($P['email']) . '</span><input type="email" name="email" required autocomplete="email" inputmode="email" maxlength="190" placeholder="name@example.com"></label>';
    $submit = '<button class="pub-btn" type="submit">' . e($page['button'] ?: $P['button']) . '</button>';
    // compact: the address and the button on one line, the rest below
    echo $compact ? '<div class="pub-inline">' . $emailField . $submit . '</div>' : $emailField;
    if (count($langs) > 1 && $page['showLang']) echo '<label class="pub-field"><span>' . e($P['lang']) . '</span><select name="lang">' . lang_select($langs, $lang) . '</select></label>';
    else echo '<input type="hidden" name="lang" value="' . e($lang) . '">';
    // honeypot: hidden from people, filled by bots
    echo '<label class="pub-hp" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>';
    $terms = '<a href="terms.php?lang=' . e($lang) . '" target="_blank" rel="noopener">' . e($P['terms_link']) . '</a>';
    echo '<label class="pub-check-row"><input type="checkbox" name="acceptPlatform" required><span>' . str_replace('{terms}', $terms, e($P['accept_platform'])) . '</span></label>';
    if ((string)$g['terms'] !== '') {
        echo '<label class="pub-check-row"><input type="checkbox" name="acceptGroup" required><span>' . e(fill($P['accept_group'], $vars)) . ' <button type="button" class="pub-link" data-toggle-terms>' . e($P['read']) . '</button></span></label>';
        echo '<div class="pub-terms" hidden>' . nl2br(e((string)$g['terms'])) . '</div>';
    }
    if (!$compact) echo $submit;
    echo '<p class="pub-error" role="alert" hidden></p>';
    echo '<p class="pub-note">' . e($P['privacy']) . '</p>';
    echo '</form></section>';
    echo '<section data-step="sent" hidden><div class="pub-check pub-mail" aria-hidden="true"></div><p>' . e($P['check_email']) . '</p></section>';
}
echo '</div>';
page_close($lang, $embed);
