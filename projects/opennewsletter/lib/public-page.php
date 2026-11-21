<?php
/**
 * OpenNewsletter — frame of the public pages (subscribe.php, unsubscribe.php): the group's colours, logo and theme,
 * the language picked from the browser, the "Powered by OpenNewsletter" line. The forms are sent by assets/public.js.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/groups.php';
require_once __DIR__ . '/render.php';

/** Security headers; $embeddable: the widget shows this page in an iframe on other sites. */
function public_headers(bool $embeddable): void
{
    header('Content-Type: text/html; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('Cache-Control: no-store');
    header('X-Robots-Tag: noindex');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' https: data:; style-src 'self' 'unsafe-inline'; script-src 'self'; connect-src 'self'; frame-ancestors " . ($embeddable ? '*' : "'self'") . "; base-uri 'none'; form-action 'self'");
}

/**
 * Settings not saved yet, shown by the live preview in the group's settings (?preview=base64 JSON). Only an
 * administrator of the group (signed in, same site) gets them: anyone else sees the saved page.
 */
function preview_page(array $g): ?array
{
    $raw = (string)($_GET['preview'] ?? '');
    if ($raw === '' || strlen($raw) > 8000) return null;
    $u = current_user();
    if (!$u || role_rank((string)member_role((int)$g['id'], (int)$u['id'])) < role_rank('admin')) return null;
    $data = json_decode((string)base64_decode($raw, true), true);
    return is_array($data) ? clean_page($data) : null;
}

function page_open(?array $g, string $lang, string $title, bool $embed, ?array $override = null): void
{
    $p = $override ?: ($g ? group_page($g) : default_page());
    $accent = $p['accent'];
    $radius = ['round' => '999px', 'soft' => '12px', 'square' => '4px'][$p['shape']];
    $theme = $p['theme'];
    // the widget may ask for another look than the one saved (data-style on its div)
    $style = in_array($_GET['style'] ?? '', PAGE_STYLES, true) ? $_GET['style'] : $p['style'];
    $ink = on_color($accent);
    $v = '?v=5';
    echo '<!doctype html><html lang="' . e($lang) . '" data-theme="' . e($theme) . '" data-style="' . e($style) . '"' . ($override ? ' data-preview="1"' : '') . ($embed ? ' data-embed="1"' : '') . '><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">'
        . '<title>' . e($title) . '</title><link rel="icon" href="icons/favicon.svg" type="image/svg+xml">'
        . '<link rel="stylesheet" href="assets/public.css' . $v . '">'
        . '<style>:root{--accent:' . e($accent) . ';--on-accent:' . e($ink) . ';--btn-radius:' . $radius . '}' . page_font_css($p['font'] ?? 'inter') . '</style>'
        . '</head><body><main class="pub"><div class="pub-card">';
    if ($g && $p['logo']) echo '<img class="pub-logo" src="' . e(image_url($p['logo'])) . '" alt="' . e($g['name']) . '">';
}

/** @font-face of the chosen typeface (only that one is downloaded) and the variables public.css uses. */
function page_font_css(string $key): string
{
    [, $family, $dir] = PAGE_FONTS[$key] ?? PAGE_FONTS['inter'];
    if ($family === null) return '';
    $css = '';
    if ($dir) {
        $name = trim(explode(',', $family)[0], '" ');
        foreach (['regular' => 400, 'bold' => 700] as $file => $weight) {
            $css .= '@font-face{font-family:"' . $name . '";src:url("fonts/' . $dir . '/' . $file . '.ttf") format("truetype");font-weight:' . $weight . ';font-display:swap}';
        }
    } elseif ($key === 'poppins') {
        $css .= '@font-face{font-family:"Poppins";src:url("fonts/poppins-regular.ttf") format("truetype");font-weight:400;font-display:swap}';
    }
    return $css . ':root{--pub-font:' . $family . ';--pub-display:' . $family . '}';
}

function page_close(string $lang, bool $embed): void
{
    $P = pub_t($lang);
    echo '</div><a class="pub-powered" href="' . e(app_url()) . '" target="_blank" rel="noopener"><span>' . e($P['powered']) . '</span> '
        . '<img src="icons/favicon.svg" width="16" height="16" alt=""> <strong>OpenNewsletter</strong></a>'
        . '</main><script src="assets/public.js?v=5" defer></script></body></html>';
}

/** Texts the script needs (errors), in the page language. */
function page_strings(string $lang): string
{
    $P = pub_t($lang);
    return e(json_out(['err_email' => $P['err_email'], 'err_accept' => $P['err_accept'], 'err_rate' => $P['err_rate'], 'err_generic' => $P['err_generic'], 'link_invalid' => $P['link_invalid'], 'not_ready' => $P['not_ready']]));
}

function lang_select(array $langs, string $lang): string
{
    $names = ['it' => 'Italiano', 'en' => 'English', 'fr' => 'Français', 'de' => 'Deutsch', 'es' => 'Español', 'pt' => 'Português', 'nl' => 'Nederlands', 'pl' => 'Polski',
        'ro' => 'Română', 'sv' => 'Svenska', 'el' => 'Ελληνικά', 'cs' => 'Čeština', 'hu' => 'Magyar', 'da' => 'Dansk', 'fi' => 'Suomi', 'hr' => 'Hrvatski',
        'sl' => 'Slovenščina', 'sk' => 'Slovenčina', 'bg' => 'Български', 'ca' => 'Català'];
    $out = '';
    foreach ($langs as $l) $out .= '<option value="' . e($l) . '"' . ($l === $lang ? ' selected' : '') . '>' . e($names[$l] ?? strtoupper($l)) . '</option>';
    return $out;
}

function not_found_page(string $lang, bool $embed): void
{
    http_response_code(404);
    $P = pub_t($lang);
    page_open(null, $lang, 'OpenNewsletter', $embed);
    echo '<h1>' . e($P['not_found']) . '</h1>';
    page_close($lang, $embed);
    exit;
}
