<?php
/**
 * OpenNewsletter — the newsletter document and its email.
 *
 * A document is JSON: {theme: {...}, blocks: [...]}. The editor (assets/editor.js) builds it with drag and drop;
 * clean_doc() rebuilds it field by field (unknown keys dropped, strings cut, colours checked, links only http(s) or
 * mailto, rich text reduced to a few safe tags), and render_email() turns it into the table-based HTML that email
 * clients understand, plus a plain-text copy. The editor's preview is this same HTML (api nl.preview), so what
 * moderators see is exactly what subscribers receive.
 *
 * Every block also has a style (block_style()): space above and below, side padding, typeface, text size, line height,
 * text and background colour. Nothing else adds space: paragraphs have a gap only between them, never after the last
 * one. Keep BLOCK_SPACE and the defaults in step with STYLE_DEFAULTS in assets/editor.js.
 *
 * Every email ends with the footer of the platform: why the subscriber receives it, the unsubscribe link and
 * "Newsletter created with [logo] OpenNewsletter".
 */

declare(strict_types=1);

require_once __DIR__ . '/public-i18n.php';

const BLOCK_TYPES = ['heading', 'text', 'image', 'button', 'divider', 'spacer', 'quote', 'card', 'media', 'social', 'columns'];
// Blocks that can go inside a column (no columns inside columns)
const INNER_TYPES = ['heading', 'text', 'image', 'button', 'divider', 'spacer', 'quote', 'card', 'social'];
// Widths (%) of the columns: the first key of each count is the default
const COLUMN_LAYOUTS = [2 => ['50-50', '33-67', '67-33', '40-60', '60-40'], 3 => ['33-33-33', '25-50-25']];
// Typefaces that every mail client has (web fonts are ignored by most of them)
const FONTS = [
    'sans' => 'Helvetica, Arial, sans-serif',
    'system' => '-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif',
    'verdana' => 'Verdana, Geneva, sans-serif',
    'tahoma' => 'Tahoma, Verdana, sans-serif',
    'rounded' => '\'Trebuchet MS\', \'Segoe UI\', sans-serif',
    'serif' => 'Georgia, \'Times New Roman\', serif',
    'times' => '\'Times New Roman\', Times, serif',
    'palatino' => '\'Palatino Linotype\', Palatino, \'Book Antiqua\', Georgia, serif',
    'garamond' => 'Garamond, Baskerville, \'Baskerville Old Face\', Georgia, serif',
    'mono' => '\'Courier New\', Courier, monospace',
];
// 'custom' is a link with its own label ("Spotify", "Blog"…): a block may have several
const SOCIALS = ['website', 'facebook', 'instagram', 'x', 'linkedin', 'youtube', 'tiktok', 'mastodon', 'telegram', 'whatsapp', 'custom'];
const SOCIAL_NAMES = ['website' => 'Web', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'x' => 'X', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'mastodon' => 'Mastodon', 'telegram' => 'Telegram', 'whatsapp' => 'WhatsApp', 'custom' => 'Link'];
// Default text size and line height of each block (px, ratio); 0 = not a text block
const BLOCK_TEXT = ['heading' => [0, 1.22], 'text' => [16, 1.6], 'quote' => [19, 1.5], 'button' => [16, 1.2], 'card' => [15, 1.55], 'media' => [15, 1.55], 'social' => [13, 1.2], 'image' => [0, 0], 'divider' => [0, 0], 'spacer' => [0, 0], 'columns' => [0, 0]];
const HEADING_SIZES = [1 => 30, 2 => 23, 3 => 18];

function default_theme(): array
{
    return ['bg' => '#F3F1FA', 'card' => '#FFFFFF', 'text' => '#1F1D2E', 'accent' => '#5B3DF5', 'font' => 'sans', 'radius' => 14, 'width' => 600];
}

/** Space above and below each block when the author has not changed it: the same on both sides. Inside a column the
 *  side margins are 0 (the column already has its place on the page). */
function default_style(string $type, bool $inner = false): array
{
    $v = $type === 'divider' ? 12 : 8;
    return ['pt' => $v, 'pb' => $v, 'px' => $inner ? 0 : 32, 'font' => '', 'size' => 0, 'lh' => 0, 'weight' => '', 'color' => '', 'bg' => ''];
}

// ---------------------------------------------------------------- cleaning

function clean_color($v, string $fallback): string
{
    return is_string($v) && preg_match('/^#[0-9a-f]{6}$/i', $v) ? strtoupper($v) : $fallback;
}

function clean_text($v, int $max): string
{
    if (!is_string($v) && !is_numeric($v)) return '';
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', str_replace(["\r\n", "\r"], "\n", (string)$v)) ?? '';
    return mb_substr(trim($v), 0, $max);
}

function clean_url($v, bool $allowMail = true): string
{
    $v = clean_text($v, 1000);
    if ($v === '') return '';
    if (preg_match('~^https?://[^\s<>"]+$~i', $v)) return $v;
    if ($allowMail && preg_match('~^mailto:[^\s<>"]+$~i', $v)) return $v;
    if (preg_match('~^img:[a-f0-9]{20}$~', $v)) return $v; // an image uploaded to this group
    return '';
}

function clean_align($v): string
{
    return in_array($v, ['left', 'center', 'right'], true) ? $v : 'left';
}

function clamp_int($v, int $min, int $max, int $default): int
{
    return is_numeric($v) ? max($min, min($max, (int)$v)) : $default;
}

/** Rich text from the editor: only b, strong, i, em, u, s, a (http/https/mailto), br, p, ul, ol, li survive. */
function clean_rich($html): string
{
    if (!is_string($html) || trim($html) === '') return '';
    $html = mb_substr($html, 0, 20000);
    $doc = new DOMDocument();
    $prev = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="r">' . $html . '</div>', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);
    $root = $doc->getElementById('r');
    if (!$root) return '';
    $out = rich_children($root);
    $out = preg_replace('~(<br>\s*)+</p>~', '</p>', $out) ?? $out;
    $out = preg_replace('~<p>\s*</p>~', '', $out) ?? $out;
    return trim($out);
}

function rich_children(DOMNode $node): string
{
    $allowed = ['b' => 'strong', 'strong' => 'strong', 'i' => 'em', 'em' => 'em', 'u' => 'u', 's' => 's', 'strike' => 's', 'p' => 'p', 'div' => 'p', 'ul' => 'ul', 'ol' => 'ol', 'li' => 'li', 'h1' => 'p', 'h2' => 'p', 'h3' => 'p', 'h4' => 'p', 'blockquote' => 'p'];
    $out = '';
    foreach ($node->childNodes as $c) {
        if ($c instanceof DOMText) {
            $out .= e($c->nodeValue ?? '');
            continue;
        }
        if (!$c instanceof DOMElement) continue;
        $tag = strtolower($c->tagName);
        if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'head', 'title', 'template', 'svg', 'math'], true)) continue;
        if ($tag === 'br') {
            $out .= '<br>';
            continue;
        }
        $inner = rich_children($c);
        if ($tag === 'a') {
            $href = clean_url($c->getAttribute('href'));
            $out .= $href !== '' && strpos($href, 'img:') !== 0 ? '<a href="' . e($href) . '">' . $inner . '</a>' : $inner;
            continue;
        }
        if (isset($allowed[$tag])) {
            $t = $allowed[$tag];
            // a div inside a p (pasted content) would nest paragraphs: keep its text only
            if ($t === 'p' && $node instanceof DOMElement && in_array(strtolower($node->tagName), ['p', 'li'], true)) $out .= $inner . '<br>';
            else $out .= "<$t>$inner</$t>";
            continue;
        }
        $out .= $inner; // span, font, … : the text stays, the tag goes
    }
    return $out;
}

/** The style of one block: spacing, typeface, text size and line height, colours. Missing values take the defaults. */
function clean_style($s, string $type, bool $inner = false): array
{
    $d = default_style($type, $inner);
    $s = is_array($s) ? $s : [];
    return [
        'pt' => clamp_int($s['pt'] ?? null, 0, 96, $d['pt']), 'pb' => clamp_int($s['pb'] ?? null, 0, 96, $d['pb']), 'px' => clamp_int($s['px'] ?? null, 0, 64, $d['px']),
        'font' => isset(FONTS[$s['font'] ?? '']) ? $s['font'] : '', 'size' => clamp_int($s['size'] ?? 0, 0, 64, 0),
        'lh' => is_numeric($s['lh'] ?? null) && (float)$s['lh'] >= 1 && (float)$s['lh'] <= 2.4 ? round((float)$s['lh'], 2) : 0,
        'weight' => in_array($s['weight'] ?? '', ['400', '700', '800'], true) ? $s['weight'] : '',
        'color' => clean_color($s['color'] ?? '', ''), 'bg' => clean_color($s['bg'] ?? '', ''),
    ];
}

function clean_block($b, bool $inner = false): ?array
{
    if (!is_array($b) || !in_array($b['type'] ?? '', $inner ? INNER_TYPES : BLOCK_TYPES, true)) return null;
    $type = $b['type'];
    $id = is_string($b['id'] ?? null) && preg_match('/^[a-z0-9]{4,16}$/', $b['id']) ? $b['id'] : substr(bin2hex(random_bytes(5)), 0, 8);
    $o = ['id' => $id, 'type' => $type];
    switch ($type) {
        case 'heading':
            $o += ['text' => clean_text($b['text'] ?? '', 300), 'level' => clamp_int($b['level'] ?? 1, 1, 3, 1), 'align' => clean_align($b['align'] ?? 'left')];
            break;
        case 'text':
            $o += ['html' => clean_rich($b['html'] ?? ''), 'align' => clean_align($b['align'] ?? 'left')];
            break;
        case 'image':
            $o += ['src' => clean_url($b['src'] ?? '', false), 'alt' => clean_text($b['alt'] ?? '', 200), 'href' => clean_url($b['href'] ?? ''),
                'width' => clamp_int($b['width'] ?? 100, 20, 100, 100), 'align' => clean_align($b['align'] ?? 'center'), 'rounded' => !empty($b['rounded'])];
            break;
        case 'button':
            $o += ['label' => clean_text($b['label'] ?? '', 80), 'href' => clean_url($b['href'] ?? ''), 'align' => clean_align($b['align'] ?? 'center'),
                'color' => clean_color($b['color'] ?? '', ''), 'full' => !empty($b['full'])];
            break;
        case 'divider':
            $o += ['color' => clean_color($b['color'] ?? '', ''), 'thickness' => clamp_int($b['thickness'] ?? 1, 1, 8, 1)];
            break;
        case 'spacer':
            $o += ['size' => clamp_int($b['size'] ?? 24, 4, 120, 24)];
            break;
        case 'quote':
            $o += ['text' => clean_text($b['text'] ?? '', 1000), 'cite' => clean_text($b['cite'] ?? '', 120)];
            break;
        case 'card':
            $o += ['src' => clean_url($b['src'] ?? '', false), 'title' => clean_text($b['title'] ?? '', 200), 'html' => clean_rich($b['html'] ?? ''),
                'label' => clean_text($b['label'] ?? '', 80), 'href' => clean_url($b['href'] ?? '')];
            break;
        case 'media':
            // image beside text: the image on the left or right, the text aligned to the top, middle or bottom of it
            $o += ['src' => clean_url($b['src'] ?? '', false), 'alt' => clean_text($b['alt'] ?? '', 200), 'title' => clean_text($b['title'] ?? '', 200),
                'html' => clean_rich($b['html'] ?? ''), 'label' => clean_text($b['label'] ?? '', 80), 'href' => clean_url($b['href'] ?? ''),
                'side' => ($b['side'] ?? '') === 'right' ? 'right' : 'left', 'imgWidth' => clamp_int($b['imgWidth'] ?? 40, 25, 60, 40),
                'valign' => in_array($b['valign'] ?? '', ['top', 'middle', 'bottom'], true) ? $b['valign'] : 'middle', 'rounded' => !empty($b['rounded'] ?? true),
                'gap' => clamp_int($b['gap'] ?? 20, 8, 48, 20)];
            break;
        case 'columns':
            // side by side: 2 or 3 columns, each with its own blocks
            $count = in_array((int)($b['count'] ?? 2), [2, 3], true) ? (int)$b['count'] : 2;
            $layout = in_array($b['layout'] ?? '', COLUMN_LAYOUTS[$count], true) ? $b['layout'] : COLUMN_LAYOUTS[$count][0];
            $cols = [];
            $src = is_array($b['cols'] ?? null) ? array_values($b['cols']) : [];
            for ($k = 0; $k < $count; $k++) {
                $list = [];
                foreach (array_slice(is_array($src[$k]['blocks'] ?? null) ? $src[$k]['blocks'] : [], 0, 30) as $ib) {
                    $c = clean_block($ib, true);
                    if ($c) $list[] = $c;
                }
                $cols[] = ['blocks' => $list];
            }
            $o += ['count' => $count, 'layout' => $layout, 'cols' => $cols, 'gap' => clamp_int($b['gap'] ?? 24, 0, 48, 24),
                'valign' => in_array($b['valign'] ?? '', ['top', 'middle', 'bottom'], true) ? $b['valign'] : 'top', 'stack' => !array_key_exists('stack', $b) || !empty($b['stack'])];
            break;
        case 'social':
            $links = [];
            // every link is kept as typed (an address still being written must not vanish on save);
            // only the complete ones end up in the email (social_url())
            foreach (array_slice(is_array($b['links'] ?? null) ? $b['links'] : [], 0, 10) as $l) {
                if (!is_array($l) || !in_array($l['kind'] ?? '', SOCIALS, true)) continue;
                $link = ['kind' => $l['kind'], 'url' => clean_text($l['url'] ?? '', 500)];
                if ($l['kind'] === 'custom') $link['label'] = clean_text($l['label'] ?? '', 40);
                $links[] = $link;
            }
            $o += ['links' => $links, 'align' => clean_align($b['align'] ?? 'center')];
            break;
    }
    if ($type !== 'spacer') $o['style'] = clean_style($b['style'] ?? null, $type, $inner);
    return $o;
}

function clean_doc($d): array
{
    $d = is_array($d) ? $d : [];
    $t = is_array($d['theme'] ?? null) ? $d['theme'] : [];
    $def = default_theme();
    $theme = [
        'bg' => clean_color($t['bg'] ?? '', $def['bg']), 'card' => clean_color($t['card'] ?? '', $def['card']),
        'text' => clean_color($t['text'] ?? '', $def['text']), 'accent' => clean_color($t['accent'] ?? '', $def['accent']),
        'font' => isset(FONTS[$t['font'] ?? '']) ? $t['font'] : 'sans', 'radius' => clamp_int($t['radius'] ?? 14, 0, 28, 14),
        'width' => clamp_int($t['width'] ?? 600, 480, 720, 600),
    ];
    $blocks = [];
    foreach (array_slice(is_array($d['blocks'] ?? null) ? $d['blocks'] : [], 0, 120) as $b) {
        $c = clean_block($b);
        if ($c) $blocks[] = $c;
    }
    return ['theme' => $theme, 'blocks' => $blocks];
}

/** The text of a social link: the network's name, or the label of a custom link. */
function social_name(array $l): string
{
    if ($l['kind'] === 'custom') return trim((string)($l['label'] ?? '')) ?: 'Link';
    return SOCIAL_NAMES[$l['kind']] ?? 'Link';
}

/** A social link ready for the email: "instagram.com/name" gets https://, anything else that is not a full address is dropped. */
function social_url(string $url): string
{
    $url = trim($url);
    if ($url !== '' && !preg_match('~^[a-z][a-z0-9+.-]*:~i', $url) && preg_match('~^(www\.)?[a-z0-9-]+(\.[a-z0-9-]+)+(/\S*)?$~i', $url)) $url = 'https://' . $url;
    $url = clean_url($url, false);
    return $url !== '' && strpos($url, 'img:') !== 0 && preg_match('~^https?://[^/\s]+\.[^/\s]+~i', $url) ? $url : '';
}

function doc_is_empty(array $doc): bool
{
    $all = [];
    foreach ($doc['blocks'] as $b) {
        $all[] = $b;
        if ($b['type'] === 'columns') foreach ($b['cols'] as $c) foreach ($c['blocks'] as $ib) $all[] = $ib;
    }
    foreach ($all as $b) {
        if ($b['type'] === 'columns') continue;
        if (in_array($b['type'], ['divider', 'spacer'], true)) continue;
        if (trim(strip_tags(($b['text'] ?? '') . ($b['html'] ?? '') . ($b['label'] ?? '') . ($b['title'] ?? '') . ($b['src'] ?? ''))) !== '' || array_filter($b['links'] ?? [], fn($l) => social_url($l['url']) !== '')) return false;
    }
    return true;
}

// ---------------------------------------------------------------- rendering

function image_url(string $src): string
{
    if (strpos($src, 'img:') === 0) return app_url() . 'api/?r=img&id=' . substr($src, 4);
    return $src;
}

/**
 * Inline styles for the safe rich text (email clients ignore <style> blocks more often than not).
 * Paragraphs and lists are separated by a gap; the last one has no margin, so the block ends where its text ends.
 */
function style_rich(string $html, array $th, string $align, string $color): string
{
    if (strpos($html, '<p') !== 0 && strpos($html, '<ul') !== 0 && strpos($html, '<ol') !== 0) $html = '<p>' . $html . '</p>';
    $gap = 'margin:0 0 1em;';
    $html = str_replace(['<p>', '<ul>', '<ol>', '<li>'], ['<p style="' . $gap . 'text-align:' . $align . '">', '<ul style="' . $gap . 'padding-left:22px">', '<ol style="' . $gap . 'padding-left:22px">', '<li style="margin:0 0 .3em">'], $html);
    $html = str_replace('<a href=', '<a style="color:' . $th['accent'] . ';text-decoration:underline" href=', $html);
    // no margin after the last paragraph or list
    if (preg_match('~^(.*)(<(?:p|ul|ol) style=")' . preg_quote($gap, '~') . '(.*)$~s', $html, $m)) $html = $m[1] . $m[2] . 'margin:0;' . $m[3];
    return '<div style="color:' . $color . '">' . $html . '</div>';
}

/** Readable text on a coloured background: white or near-black. */
function on_color(string $hex): string
{
    [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
    $l = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return $l > 0.62 ? '#17152B' : '#FFFFFF';
}

/** A colour between two others (t = how much of the second one). */
function mix(string $a, string $b, float $t): string
{
    [$r1, $g1, $b1] = sscanf($a, '#%02x%02x%02x');
    [$r2, $g2, $b2] = sscanf($b, '#%02x%02x%02x');
    return sprintf('#%02X%02X%02X', (int)round($r1 + ($r2 - $r1) * $t), (int)round($g1 + ($g2 - $g1) * $t), (int)round($b1 + ($b2 - $b1) * $t));
}

function button_html(string $label, string $href, string $color, string $align, bool $full, array $th, int $size, string $font): string
{
    $fg = on_color($color);
    $r = min(999, max(4, (int)$th['radius'] * 2));
    $w = $full ? ' width="100%"' : '';
    return '<table role="presentation" cellpadding="0" cellspacing="0"' . $w . ' align="' . $align . '" style="margin:' . ($align === 'center' ? '0 auto' : '0') . '"><tr>'
        . '<td align="center" style="border-radius:' . $r . 'px;background:' . $color . '">'
        . '<a href="' . e($href ?: '#') . '" style="display:' . ($full ? 'block' : 'inline-block') . ';padding:13px 28px;color:' . $fg . ';text-decoration:none;font-weight:700;font-size:' . $size . 'px;font-family:' . $font . ';line-height:1.2;border-radius:' . $r . 'px">' . e($label) . '</a>'
        . '</td></tr></table>';
}

/**
 * The resolved style of a block: what the author chose, or the theme.
 * Returns font (CSS stack), size (px), lh, color, weight, and the padding of its cell.
 */
function block_style(array $b, array $th): array
{
    $s = $b['style'] ?? default_style($b['type']);
    [$size, $lh] = BLOCK_TEXT[$b['type']];
    if ($b['type'] === 'heading') $size = HEADING_SIZES[$b['level']];
    return [
        'font' => FONTS[$s['font'] ?: $th['font']] ?? FONTS['sans'],
        'size' => $s['size'] ?: $size, 'lh' => $s['lh'] ?: $lh, 'color' => $s['color'] ?: $th['text'], 'bg' => $s['bg'],
        'weight' => $s['weight'] ?: ($b['type'] === 'heading' ? '800' : ''),
        'cell' => 'padding:' . $s['pt'] . 'px ' . $s['px'] . 'px ' . $s['pb'] . 'px',
    ];
}

/** The content of a block, without its cell. */
function block_inner(array $b, array $th, array $st): string
{
    $text = 'font-family:' . $st['font'] . ';font-size:' . $st['size'] . 'px;line-height:' . $st['lh'] . ';color:' . $st['color'] . ($st['weight'] ? ';font-weight:' . $st['weight'] : '');
    $inner = $th['width'] - ($b['style']['px'] ?? 32) * 2;
    switch ($b['type']) {
        case 'heading':
            if ($b['text'] === '') return '';
            return '<h' . $b['level'] . ' style="margin:0;' . $text . ';letter-spacing:-.01em;text-align:' . $b['align'] . '">' . nl2br(e($b['text'])) . '</h' . $b['level'] . '>';
        case 'text':
            if ($b['html'] === '') return '';
            return '<div style="' . $text . ';text-align:' . $b['align'] . '">' . style_rich($b['html'], $th, $b['align'], $st['color']) . '</div>';
        case 'image':
            if ($b['src'] === '') return '';
            $w = max(40, (int)round($inner * $b['width'] / 100));
            $img = '<img src="' . e(image_url($b['src'])) . '" width="' . $w . '" alt="' . e($b['alt']) . '" style="display:inline-block;width:100%;max-width:' . $w . 'px;height:auto;border:0;vertical-align:top;' . ($b['rounded'] ? 'border-radius:' . max(6, (int)$th['radius']) . 'px;' : '') . '">';
            if ($b['href'] !== '') $img = '<a href="' . e($b['href']) . '">' . $img . '</a>';
            return '<div style="text-align:' . $b['align'] . ';line-height:0;font-size:0">' . $img . '</div>';
        case 'button':
            if ($b['label'] === '') return '';
            return button_html($b['label'], $b['href'], $b['color'] ?: $th['accent'], $b['align'], $b['full'], $th, $st['size'], $st['font']);
        case 'divider':
            $t = $b['thickness'] ?? 1;
            return '<div style="height:' . $t . 'px;line-height:' . $t . 'px;font-size:1px;background:' . ($b['color'] ?: mix($th['text'], $th['card'], .85)) . '">&nbsp;</div>';
        case 'quote':
            if ($b['text'] === '') return '';
            return '<div style="border-left:4px solid ' . $th['accent'] . ';padding:4px 0 4px 18px;font-style:italic;' . $text . '">'
                . nl2br(e($b['text'])) . ($b['cite'] !== '' ? '<div style="margin-top:8px;font-size:14px;font-style:normal;opacity:.7">— ' . e($b['cite']) . '</div>' : '') . '</div>';
        case 'card':
            $out = '';
            if ($b['src'] !== '') $out .= '<img src="' . e(image_url($b['src'])) . '" width="' . $inner . '" alt="" style="display:block;width:100%;height:auto;border:0">';
            $out .= '<div style="padding:20px 22px 22px">';
            if ($b['title'] !== '') $out .= '<h3 style="margin:0 0 8px;font-family:' . $st['font'] . ';font-size:' . round($st['size'] * 1.33) . 'px;line-height:1.25;color:' . $st['color'] . '">' . e($b['title']) . '</h3>';
            if ($b['html'] !== '') $out .= '<div style="' . $text . '">' . style_rich($b['html'], $th, 'left', $st['color']) . '</div>';
            if ($b['label'] !== '') $out .= '<a href="' . e($b['href'] ?: '#') . '" style="display:inline-block;margin-top:12px;font-family:' . $st['font'] . ';font-weight:700;color:' . $th['accent'] . ';text-decoration:none">' . e($b['label']) . ' &rarr;</a>';
            $out .= '</div>';
            return '<div style="border:1px solid ' . mix($th['text'], $th['card'], .86) . ';border-radius:' . $th['radius'] . 'px;overflow:hidden;background:' . mix($th['accent'], $th['card'], .96) . '">' . $out . '</div>';
        case 'media':
            $imgW = max(60, (int)round(($inner - $b['gap']) * $b['imgWidth'] / 100));
            $img = $b['src'] !== '' ? '<img src="' . e(image_url($b['src'])) . '" width="' . $imgW . '" alt="' . e($b['alt']) . '" style="display:block;width:100%;max-width:' . $imgW . 'px;height:auto;border:0;' . ($b['rounded'] ? 'border-radius:' . max(6, (int)$th['radius']) . 'px;' : '') . '">' : '';
            if ($img !== '' && $b['href'] !== '') $img = '<a href="' . e($b['href']) . '">' . $img . '</a>';
            $body = '';
            if ($b['title'] !== '') $body .= '<h3 style="margin:0 0 8px;font-family:' . $st['font'] . ';font-size:' . round($st['size'] * 1.33) . 'px;line-height:1.25;color:' . $st['color'] . '">' . e($b['title']) . '</h3>';
            if ($b['html'] !== '') $body .= '<div style="' . $text . '">' . style_rich($b['html'], $th, 'left', $st['color']) . '</div>';
            if ($b['label'] !== '') $body .= '<a href="' . e($b['href'] ?: '#') . '" style="display:inline-block;margin-top:12px;font-family:' . $st['font'] . ';font-weight:700;color:' . $th['accent'] . ';text-decoration:none">' . e($b['label']) . ' &rarr;</a>';
            if ($img === '') return $body; // no picture yet: just the text, without an empty column
            $va = $b['valign'];
            $half = (int)floor($b['gap'] / 2);
            // The image cell always comes first in the HTML, so on phones (columns stacked) it is above the text;
            // dir="rtl" on the table puts it on the right on wider screens.
            $right = $b['side'] === 'right';
            $imgTd = '<td class="on-col on-img" dir="ltr" width="' . $b['imgWidth'] . '%" valign="' . $va . '" style="vertical-align:' . $va . ';padding-' . ($right ? 'left' : 'right') . ':' . $half . 'px">' . $img . '</td>';
            $txtTd = '<td class="on-col" dir="ltr" valign="' . $va . '" style="vertical-align:' . $va . ';padding-' . ($right ? 'right' : 'left') . ':' . $half . 'px">' . $body . '</td>';
            return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"' . ($right ? ' dir="rtl"' : '') . '><tr>' . $imgTd . $txtTd . '</tr></table>';
        case 'columns':
            $widths = array_map('intval', explode('-', $b['layout']));
            $n = count($widths);
            $avail = $inner - $b['gap'] * ($n - 1);
            $va = $b['valign'];
            $tds = '';
            foreach ($b['cols'] as $k => $c) {
                $w = max(40, (int)floor($avail * $widths[$k] / 100));
                $rows = implode('', array_map(fn($ib) => block_html($ib, array_merge($th, ['width' => $w])), $c['blocks']));
                $pad = ($k > 0 ? 'padding-left:' . (int)ceil($b['gap'] / 2) . 'px;' : '') . ($k < $n - 1 ? 'padding-right:' . (int)floor($b['gap'] / 2) . 'px;' : '');
                $cls = $b['stack'] ? ' class="on-col' . ($k < $n - 1 ? ' on-cg' : '') . '"' : '';
                $tds .= '<td' . $cls . ' width="' . $widths[$k] . '%" valign="' . $va . '" style="vertical-align:' . $va . ';' . $pad . '">'
                    . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . ($rows ?: '<tr><td>&nbsp;</td></tr>') . '</table></td>';
            }
            return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>' . $tds . '</tr></table>';
        case 'social':
            $links = array_values(array_filter(array_map(fn($l) => ['name' => social_name($l), 'url' => social_url($l['url'])], $b['links']), fn($l) => $l['url'] !== ''));
            if (!$links) return '';
            $pills = array_map(fn($l) => '<a href="' . e($l['url']) . '" style="display:inline-block;margin:3px;padding:8px 14px;border-radius:999px;background:' . mix($th['accent'], $th['card'], .88) . ';color:' . $th['accent'] . ';font-family:' . $st['font'] . ';font-size:' . $st['size'] . 'px;font-weight:700;text-decoration:none">' . e($l['name']) . '</a>', $links);
            return '<div style="text-align:' . $b['align'] . '">' . implode('', $pills) . '</div>';
    }
    return '';
}

function block_html(array $b, array $th): string
{
    if ($b['type'] === 'spacer') return '<tr><td style="height:' . $b['size'] . 'px;line-height:' . $b['size'] . 'px;font-size:1px">&nbsp;</td></tr>';
    $st = block_style($b, $th);
    $inner = block_inner($b, $th, $st);
    if ($inner === '') return '';
    $align = in_array($b['type'], ['button', 'social', 'image'], true) ? ' align="' . $b['align'] . '"' : '';
    return '<tr><td' . $align . ' style="' . $st['cell'] . ($st['bg'] ? ';background:' . $st['bg'] : '') . '">' . $inner . '</td></tr>';
}

function block_text(array $b): string
{
    switch ($b['type']) {
        case 'heading':
            return mb_strtoupper($b['text']);
        case 'text':
            return rich_to_text($b['html']);
        case 'image':
            return $b['href'] !== '' ? ($b['alt'] ?: 'Link') . ': ' . $b['href'] : '';
        case 'button':
            return $b['label'] !== '' ? $b['label'] . ($b['href'] !== '' ? ': ' . $b['href'] : '') : '';
        case 'divider':
            return '———';
        case 'quote':
            return '“' . $b['text'] . '”' . ($b['cite'] !== '' ? ' — ' . $b['cite'] : '');
        case 'columns':
            $parts = [];
            foreach ($b['cols'] as $c) foreach ($c['blocks'] as $ib) $parts[] = block_text($ib);
            return implode("\n\n", array_filter($parts, fn($p) => trim($p) !== ''));
        case 'card':
        case 'media':
            return trim($b['title'] . "\n" . rich_to_text($b['html']) . ($b['label'] !== '' ? "\n" . $b['label'] . ($b['href'] !== '' ? ': ' . $b['href'] : '') : ''));
        case 'social':
            return implode("\n", array_filter(array_map(fn($l) => social_url($l['url']) !== '' ? social_name($l) . ': ' . social_url($l['url']) : '', $b['links'])));
    }
    return '';
}

function rich_to_text(string $html): string
{
    $html = preg_replace_callback('~<a href="([^"]*)">(.*?)</a>~s', fn($m) => $m[2] . ' (' . html_entity_decode($m[1]) . ')', $html) ?? $html;
    $html = str_replace(['<br>', '</p>', '</li>', '<li>'], ["\n", "\n\n", "\n", '• '], $html);
    return trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
}

/**
 * The email of a newsletter.
 * $ctx: lang (of the footer), unsubscribe (URL, '#' in previews), subject, preheader.
 * Returns ['html' => ..., 'text' => ...].
 */
function render_email(array $doc, array $group, array $ctx): array
{
    $th = $doc['theme'];
    $lang = $ctx['lang'] ?? 'en';
    $P = pub_t($lang);
    $rows = implode('', array_map(fn($b) => block_html($b, $th), $doc['blocks']));
    $font = FONTS[$th['font']];
    $muted = mix($th['text'], $th['bg'], .45);
    $unsub = $ctx['unsubscribe'] ?? '#';
    $logo = app_url() . 'icons/icon-64.png';
    $footer = '<tr><td style="padding:22px 24px 8px;text-align:center;font-size:12px;line-height:1.6;color:' . $muted . '">'
        . e(fill($P['footer_why'], ['group' => $group['name']])) . '<br>'
        . '<a href="' . e($unsub) . '" style="color:' . $muted . ';text-decoration:underline">' . e($P['footer_unsub']) . '</a>'
        . '</td></tr>'
        . '<tr><td style="padding:10px 24px 30px;text-align:center;font-size:12px;color:' . $muted . '">'
        . '<a href="' . e(app_url()) . '" style="color:' . $muted . ';text-decoration:none">' . e($P['footer_made']) . ' '
        . '<img src="' . e($logo) . '" width="16" height="16" alt="" style="vertical-align:-3px;border:0;border-radius:4px"> '
        . '<strong style="color:' . $th['text'] . '">OpenNewsletter</strong></a></td></tr>';
    $pre = $ctx['preheader'] ?? '';
    // On phones the two columns of "image + text" blocks stack (clients that ignore <style> keep them side by side)
    $css = '<style>@media only screen and (max-width:520px){.on-col{display:block!important;width:100%!important;padding:0!important}.on-img,.on-cg{padding:0 0 14px!important}.on-col img{max-width:100%!important}}</style>';
    $html = '<!DOCTYPE html><html lang="' . e($lang) . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<meta name="color-scheme" content="light only"><title>' . e($ctx['subject'] ?? '') . '</title>' . $css . '</head>'
        . '<body style="margin:0;padding:0;background:' . $th['bg'] . ';font-family:' . $font . ';color:' . $th['text'] . ';-webkit-text-size-adjust:100%">'
        . ($pre !== '' ? '<div style="display:none;max-height:0;overflow:hidden;opacity:0">' . e($pre) . str_repeat('&#847;&zwnj;&nbsp;', 40) . '</div>' : '')
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $th['bg'] . '"><tr><td align="center" style="padding:28px 12px">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:' . $th['width'] . 'px">'
        . '<tr><td style="background:' . $th['card'] . ';border-radius:' . $th['radius'] . 'px;overflow:hidden">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="height:24px;line-height:24px;font-size:1px">&nbsp;</td></tr>' . $rows
        . '<tr><td style="height:24px;line-height:24px;font-size:1px">&nbsp;</td></tr></table>'
        . '</td></tr>' . $footer . '</table></td></tr></table></body></html>';
    $parts = array_filter(array_map('block_text', $doc['blocks']), fn($s) => trim($s) !== '');
    $text = implode("\n\n", $parts) . "\n\n—\n" . fill($P['footer_why'], ['group' => $group['name']]) . "\n" . $P['footer_unsub'] . ': ' . $unsub
        . "\n" . $P['footer_made'] . ' OpenNewsletter — ' . app_url();
    return ['html' => $html, 'text' => $text];
}

/** URL a subscriber opens to leave (one click, no email to type). */
function unsubscribe_url(array $group, string $token): string
{
    return app_url() . 'unsubscribe.php?t=' . $token;
}

/** Extra headers for one-click unsubscribe in Gmail, Outlook, Apple Mail… (RFC 8058). */
function list_headers(array $group, string $token): array
{
    $u = unsubscribe_url($group, $token);
    return [
        'List-Unsubscribe: <' . $u . '>',
        'List-Unsubscribe-Post: List-Unsubscribe=One-Click',
        'List-Id: ' . preg_replace('/[^\w .-]/u', '', $group['name']) . ' <' . $group['slug'] . '.opennewsletter>',
        'Precedence: bulk',
        'X-Mailer: OpenNewsletter',
    ];
}
