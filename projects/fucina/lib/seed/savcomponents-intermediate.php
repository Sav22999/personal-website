<?php
/** SavComponents intermediate: colour modes and styles, tokens and custom themes, overlays, navigation, events and validation, data and media, icons and integration, side menus and breadcrumbs, phones. */

/** A light page with a dark section inside: the mode class works on any container. */
if (!function_exists('fig_savc_scope')) {
    function fig_savc_scope(string $note1, string $note2): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="16" y="16" width="608" height="268" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="32" y="44" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">&lt;html class="sav-theme-light"&gt;</text>
<rect x="32" y="68" width="112" height="36" rx="8" fill="#66CBFF"/>
<rect x="56" y="82" width="64" height="8" rx="4" fill="#072536"/>
<rect x="32" y="120" width="250" height="88" rx="12" fill="#FFFFFF" stroke="#C9C7DD" stroke-width="2"/>
<rect x="48" y="140" width="150" height="10" rx="5" fill="#1E1C52"/>
<rect x="48" y="162" width="210" height="8" rx="4" fill="#B9B6D3"/>
<rect x="48" y="180" width="180" height="8" rx="4" fill="#B9B6D3"/>
<text x="32" y="240">{$note1}</text>
<text x="32" y="262">{$note2}</text>
<rect x="310" y="60" width="298" height="208" rx="12" fill="#1E1C52"/>
<text x="326" y="86" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#FFFFFF">&lt;section class="sav-theme-dark"&gt;</text>
<rect x="326" y="104" width="112" height="36" rx="8" fill="#66CBFF"/>
<rect x="350" y="118" width="64" height="8" rx="4" fill="#072536"/>
<rect x="326" y="156" width="266" height="92" rx="12" fill="#2E2B6B" stroke="#55527A" stroke-width="2"/>
<rect x="342" y="176" width="150" height="10" rx="5" fill="#E8EBF3"/>
<rect x="342" y="198" width="220" height="8" rx="4" fill="#8A87B0"/>
<rect x="342" y="216" width="190" height="8" rx="4" fill="#8A87B0"/>
</svg>
SVG;
    }
}

/** The five styles drawn as a button and a card each: only shape and depth change. No words, only the style names. */
if (!function_exists('fig_savc_styles')) {
    function fig_savc_styles(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g font-weight="700" text-anchor="middle">
<text x="76" y="36">soft</text><text x="198" y="36">tilt</text><text x="320" y="36">sharp</text><text x="442" y="36">brutal</text><text x="564" y="36">glass</text>
</g>
<g font-size="14" font-weight="700" text-anchor="middle">
<rect x="32" y="63" width="88" height="34" rx="8" fill="#1E1C52" opacity="0.12"/>
<rect x="32" y="60" width="88" height="34" rx="8" fill="#2F8CF0"/>
<text x="76" y="82" fill="#FFFFFF">OK</text>
<rect x="154" y="60" width="88" height="34" fill="#2F8CF0" transform="rotate(-4 198 77)"/>
<text x="198" y="82" fill="#FFFFFF">OK</text>
<rect x="282" y="66" width="88" height="34" rx="2" fill="#2F8CF0" opacity="0.25"/>
<rect x="276" y="60" width="88" height="34" rx="2" fill="#2F8CF0"/>
<text x="320" y="82" fill="#FFFFFF" font-size="13" letter-spacing="1">OK</text>
<rect x="401" y="63" width="88" height="34" rx="5" fill="#1E1C52"/>
<rect x="398" y="60" width="88" height="34" rx="5" fill="#2F8CF0" stroke="#1E1C52" stroke-width="2.5"/>
<text x="442" y="82" fill="#FFFFFF" font-weight="800">OK</text>
</g>
<rect x="506" y="48" width="118" height="186" rx="14" fill="#DCEBFF"/>
<circle cx="546" cy="176" r="32" fill="#F03E82" opacity="0.55"/>
<circle cx="596" cy="130" r="28" fill="#7048E8" opacity="0.5"/>
<circle cx="592" cy="208" r="24" fill="#E8590C" opacity="0.5"/>
<rect x="520" y="60" width="88" height="34" rx="14" fill="#2F8CF0" opacity="0.85" stroke="#FFFFFF" stroke-width="2"/>
<text x="564" y="82" fill="#FFFFFF" font-size="14" font-weight="700" text-anchor="middle">OK</text>
<rect x="28" y="124" width="96" height="96" rx="14" fill="#1E1C52" opacity="0.10"/>
<rect x="28" y="118" width="96" height="96" rx="14" fill="#FFFFFF" stroke="#E2E5EE" stroke-width="2"/>
<rect x="150" y="118" width="96" height="96" fill="#DCEBFF" transform="rotate(3 198 166)"/>
<rect x="150" y="118" width="96" height="96" fill="#FFFFFF" stroke="#C9C7DD" stroke-width="2" transform="rotate(-2 198 166)"/>
<rect x="278" y="124" width="96" height="96" rx="3" fill="#2F8CF0" opacity="0.22"/>
<rect x="272" y="118" width="96" height="96" rx="3" fill="#FFFFFF" stroke="#C9C7DD" stroke-width="2"/>
<rect x="399" y="123" width="96" height="96" rx="8" fill="#1E1C52"/>
<rect x="394" y="118" width="96" height="96" rx="8" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2.5"/>
<rect x="516" y="118" width="96" height="96" rx="20" fill="#FFFFFF" fill-opacity="0.55" stroke="#FFFFFF" stroke-width="2"/>
<g fill="#1E1C52">
<rect x="44" y="140" width="56" height="9" rx="4"/><rect x="166" y="140" width="56" height="9" rx="1" transform="rotate(0)"/><rect x="288" y="140" width="56" height="9" rx="1"/><rect x="410" y="140" width="56" height="9" rx="2"/><rect x="532" y="140" width="56" height="9" rx="4"/>
</g>
<g fill="#B9B6D3">
<rect x="44" y="162" width="64" height="7" rx="3"/><rect x="166" y="162" width="64" height="7"/><rect x="288" y="162" width="64" height="7"/><rect x="410" y="162" width="64" height="7" rx="2"/><rect x="532" y="162" width="64" height="7" rx="3" fill="#55527A" opacity="0.5"/>
<rect x="44" y="178" width="48" height="7" rx="3"/><rect x="166" y="178" width="48" height="7"/><rect x="288" y="178" width="48" height="7"/><rect x="410" y="178" width="48" height="7" rx="2"/><rect x="532" y="178" width="48" height="7" rx="3" fill="#55527A" opacity="0.5"/>
</g>
</svg>
SVG;
    }
}

/** A token set on :root reaches every component; a container that sets it again wins for what is inside it. */
if (!function_exists('fig_savc_tokens')) {
    function fig_savc_tokens(string $inherits, string $closest1, string $closest2): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs>
<marker id="o" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#E8590C"/></marker>
<marker id="g" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#0B7F5C"/></marker>
</defs>
<g font-family="ui-monospace, Menlo, monospace" font-size="13">
<rect x="160" y="16" width="290" height="62" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="176" y="40" font-weight="700" fill="#B84508">:root</text>
<text x="176" y="64">--sav-color-primary: #e8590c</text>
<rect x="340" y="124" width="284" height="62" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="356" y="148" font-weight="700" fill="#0B7F5C">.promo</text>
<text x="356" y="172">--sav-color-primary: #118557</text>
<rect x="40" y="232" width="200" height="44" rx="10" fill="#E8590C"/>
<text x="140" y="259" text-anchor="middle" font-size="15" font-weight="700">&lt;sav-button&gt;</text>
<rect x="382" y="232" width="200" height="44" rx="10" fill="#118557"/>
<text x="482" y="259" text-anchor="middle" font-size="15" font-weight="700" fill="#FFFFFF">&lt;sav-button&gt;</text>
</g>
<path d="M240 80 L150 224" fill="none" stroke="#E8590C" stroke-width="2" marker-end="url(#o)"/>
<path d="M370 80 L446 118" fill="none" stroke="#E8590C" stroke-width="2" stroke-dasharray="6 4" marker-end="url(#o)"/>
<path d="M482 188 V224" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#g)"/>
<text x="40" y="160" font-weight="700" fill="#B84508">{$inherits}</text>
<text x="382" y="300" fill="#0B7F5C" font-weight="700">{$closest1}</text>
<text x="382" y="316" fill="#0B7F5C" font-size="13">{$closest2}</text>
</svg>
SVG;
    }
}

/** Opening and closing a dialog: the method, the event at the start, the animation, the event at the end. */
if (!function_exists('fig_savc_dialog')) {
    function fig_savc_dialog(string $opening, string $closing, string $anim, string $start, string $end, string $open, string $closers): string
    {
        $row = function (int $y, string $title, string $call, string $first, string $after, string $tail) use ($anim, $start, $end): string {
            $t = $y - 28; $m = $y + 18; $b = $y + 58;
            return <<<ROW
<text x="16" y="{$t}" font-weight="700">{$title}</text>
<rect x="16" y="{$y}" width="112" height="36" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="72" y="{$m}" dy="5" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">{$call}</text>
<path d="M130 {$m} H146" stroke="#8A87B0" stroke-width="2" marker-end="url(#k)"/>
<rect x="150" y="{$y}" width="118" height="36" rx="18" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="209" y="{$m}" dy="5" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#1B66BC">{$first}</text>
<text x="209" y="{$b}" text-anchor="middle" font-size="13" fill="#55527A">{$start}</text>
<rect x="276" y="{$y}" width="140" height="36" rx="8" fill="url(#h)" stroke="#8A87B0" stroke-width="2"/>
<text x="346" y="{$m}" dy="5" text-anchor="middle" font-size="13" font-weight="700">{$anim}</text>
<rect x="424" y="{$y}" width="140" height="36" rx="18" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="494" y="{$m}" dy="5" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#0B7F5C">{$after}</text>
<text x="494" y="{$b}" text-anchor="middle" font-size="13" fill="#55527A">{$end}</text>
<text x="576" y="{$m}" dy="5" font-size="13" font-weight="700">{$tail}</text>
ROW;
        };
        $r1 = $row(56, $opening, 'show()', 'sav-show', 'sav-after-show', $open);
        $r2 = $row(176, $closing, 'hide()', 'sav-hide', 'sav-after-hide', '');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs>
<marker id="k" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0 0L10 5L0 10z" fill="#8A87B0"/></marker>
<pattern id="h" width="10" height="10" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="10" height="10" fill="#F1F0F8"/><rect width="4" height="10" fill="#E3E1F0"/></pattern>
</defs>
{$r1}
{$r2}
<text x="16" y="268" font-size="13" fill="#55527A">{$closers}</text>
</svg>
SVG;
    }
}

/** A header with room shows its links and search; a narrow one folds them into the menu button. */
if (!function_exists('fig_savc_header')) {
    function fig_savc_header(string $wide, string $narrow, string $l1, string $l2, string $fold1, string $fold2): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#55527A"/></marker></defs>
<text x="16" y="28" font-weight="700">{$wide}</text>
<rect x="16" y="40" width="608" height="56" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="30" y="52" width="92" height="32" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="76" y="73" text-anchor="middle" font-weight="700" fill="#5A35D6">Fucina</text>
<text x="146" y="73" font-weight="700">{$l1}</text>
<text x="226" y="73" font-weight="700">{$l2}</text>
<rect x="320" y="52" width="170" height="32" rx="16" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<circle cx="340" cy="67" r="6" fill="none" stroke="#1B66BC" stroke-width="2"/><path d="M345 72 l5 5" stroke="#1B66BC" stroke-width="2"/>
<rect x="508" y="52" width="102" height="32" rx="8" fill="#E8590C"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#55527A">
<text x="30" y="118">slot="logo"</text>
<text x="146" y="118">&lt;a&gt; &lt;a&gt;</text>
<text x="320" y="118">slot="search"</text>
<text x="508" y="118">slot="actions"</text>
</g>
<text x="16" y="166" font-weight="700">{$narrow}</text>
<rect x="16" y="178" width="300" height="56" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="30" y="190" width="92" height="32" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="76" y="211" text-anchor="middle" font-weight="700" fill="#5A35D6">Fucina</text>
<rect x="158" y="190" width="40" height="32" rx="8" fill="#F1F0F8" stroke="#1E1C52" stroke-width="2"/>
<path d="M168 199 h20 M168 206 h20 M168 213 h20" stroke="#1E1C52" stroke-width="2"/>
<rect x="210" y="190" width="92" height="32" rx="8" fill="#E8590C"/>
<path d="M372 206 H206" fill="none" stroke="#55527A" stroke-width="2" stroke-dasharray="5 4" marker-end="url(#a)"/>
<text x="384" y="202">{$fold1}</text>
<text x="384" y="222">{$fold2}</text>
</svg>
SVG;
    }
}

/** An event born inside the shadow DOM bubbles out of the component up to the document, where it can be cancelled. The dot climbs slowly. */
if (!function_exists('fig_savc_bubble')) {
    function fig_savc_bubble(array $steps): string
    {
        [$s1a, $s1b, $s2a, $s2b, $s3a, $s3b, $s4a, $s4b] = $steps;
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 340" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.dot{animation:up 5s ease-in-out infinite}@keyframes up{0%,12%{transform:translateY(0);opacity:1}80%{transform:translateY(-196px);opacity:1}100%{transform:translateY(-196px);opacity:0}}@media (prefers-reduced-motion:reduce){.dot{animation:none}}</style>
<defs><marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#F03E82"/></marker></defs>
<rect x="16" y="16" width="320" height="308" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="36" y="56" width="280" height="252" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="56" y="96" width="240" height="196" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="76" y="140" width="200" height="132" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">
<text x="32" y="42">document</text>
<text x="52" y="82">&lt;section&gt;</text>
<text x="72" y="122" fill="#5A35D6">&lt;sav-tag removable&gt;</text>
<text x="92" y="164" fill="#5A35D6">shadow DOM</text>
<text x="212" y="80" fill="#B0245A">sav-remove</text>
</g>
<rect x="120" y="208" width="104" height="36" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="138" y="231">Design</text>
<circle cx="202" cy="226" r="11" fill="#F03E82"/>
<path d="M197 221 l10 10 M207 221 l-10 10" stroke="#FFFFFF" stroke-width="2"/>
<path d="M202 210 V36" fill="none" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 5" marker-end="url(#a)"/>
<circle class="dot" cx="202" cy="226" r="7" fill="#F03E82" stroke="#FFFFFF" stroke-width="2"/>
<g font-size="13" font-weight="700" fill="#FFFFFF" text-anchor="middle">
<circle cx="362" cy="40" r="12" fill="#1E1C52"/><text x="362" y="45">1</text>
<circle cx="362" cy="118" r="12" fill="#1E1C52"/><text x="362" y="123">2</text>
<circle cx="362" cy="196" r="12" fill="#1E1C52"/><text x="362" y="201">3</text>
<circle cx="362" cy="274" r="12" fill="#1E1C52"/><text x="362" y="279">4</text>
</g>
<text x="382" y="45" font-weight="700">{$s1a}</text><text x="382" y="65" font-size="13">{$s1b}</text>
<text x="382" y="123" font-weight="700">{$s2a}</text><text x="382" y="143" font-size="13">{$s2b}</text>
<text x="382" y="201" font-weight="700">{$s3a}</text><text x="382" y="221" font-size="13">{$s3b}</text>
<text x="382" y="279" font-weight="700">{$s4a}</text><text x="382" y="299" font-size="13">{$s4b}</text>
</svg>
SVG;
    }
}

/** On a wide screen the list opens next to the field; on a phone it rises from the bottom as a sheet. */
if (!function_exists('fig_savc_sheet')) {
    function fig_savc_sheet(string $wide, string $phone, string $size): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="16" y="28" font-weight="700">{$wide}</text>
<rect x="16" y="40" width="370" height="244" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="16" y="40" width="370" height="26" rx="12" fill="#F1F0F8"/>
<circle cx="34" cy="53" r="5" fill="#C9C7DD"/><circle cx="50" cy="53" r="5" fill="#C9C7DD"/><circle cx="66" cy="53" r="5" fill="#C9C7DD"/>
<text x="40" y="96" font-size="13" font-weight="700">{$size}</text>
<rect x="40" y="104" width="180" height="34" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<path d="M198 117 l6 6 l6 -6" fill="none" stroke="#1E1C52" stroke-width="2"/>
<rect x="40" y="144" width="180" height="102" rx="10" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="46" y="150" width="168" height="28" rx="6" fill="#DCEBFF"/>
<text x="58" y="169">S</text><text x="58" y="201">M</text><text x="58" y="233">L</text>
<text x="420" y="28" font-weight="700">{$phone}</text>
<rect x="430" y="40" width="160" height="252" rx="22" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<text x="446" y="76" font-size="13" font-weight="700">{$size}</text>
<rect x="446" y="84" width="128" height="30" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<path d="M431 130 H589 V270 a21 21 0 0 1 -21 21 H452 a21 21 0 0 1 -21 -21 Z" fill="#1E1C52" opacity="0.35"/>
<path d="M431 160 a14 14 0 0 1 14 -14 H575 a14 14 0 0 1 14 14 V270 a21 21 0 0 1 -21 21 H452 a21 21 0 0 1 -21 -21 Z" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="490" y="154" width="40" height="5" rx="2.5" fill="#8A87B0"/>
<rect x="440" y="170" width="140" height="30" rx="6" fill="#DCEBFF"/>
<text x="454" y="190">S</text><text x="454" y="226">M</text><text x="454" y="262">L</text>
</svg>
SVG;
    }
}

/** A custom icon: shapes drawn on a 24×24 grid, then stroked by the library in the colour of the text around it. */
if (!function_exists('fig_savc_icon')) {
    function fig_savc_icon(string $grid, string $colour1, string $colour2, string $forge): string
    {
        $lines = '';
        for ($i = 0; $i <= 24; $i += 2) {
            $p = 32 + $i * 8;
            $lines .= "<path d=\"M{$p} 40 V232 M32 " . (40 + $i * 8) . " H224\"/>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#55527A"/></marker></defs>
<rect x="32" y="40" width="192" height="192" fill="#FFFFFF"/>
<g stroke="#E3E1F0" stroke-width="1">{$lines}</g>
<rect x="32" y="40" width="192" height="192" fill="none" stroke="#8A87B0" stroke-width="2"/>
<g transform="translate(32 40) scale(8)" fill="none" stroke="#E8590C" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h16l-3 4H7z"/><path d="M10 14v5M14 14v5"/></g>
<text x="32" y="258" font-weight="700">{$grid}</text>
<text x="32" y="28" font-family="ui-monospace, Menlo, monospace" font-size="13">Sav.registerIcon("incudine", …)</text>
<path d="M240 136 H300" fill="none" stroke="#55527A" stroke-width="2" marker-end="url(#a)"/>
<rect x="316" y="60" width="160" height="44" rx="10" fill="#2F8CF0"/>
<g transform="translate(332 70) scale(1)" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h16l-3 4H7z"/><path d="M10 14v5M14 14v5"/></g>
<text x="366" y="88" font-weight="700" fill="#FFFFFF">{$forge}</text>
<rect x="316" y="120" width="160" height="44" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<g transform="translate(332 130)" fill="none" stroke="#1B66BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h16l-3 4H7z"/><path d="M10 14v5M14 14v5"/></g>
<text x="366" y="148" font-weight="700" fill="#1B66BC">{$forge}</text>
<rect x="316" y="180" width="160" height="44" rx="10" fill="#1E1C52"/>
<g transform="translate(332 190)" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h16l-3 4H7z"/><path d="M10 14v5M14 14v5"/></g>
<text x="366" y="208" font-weight="700" fill="#FFFFFF">{$forge}</text>
<text x="496" y="136">{$colour1}</text>
<text x="496" y="156">{$colour2}</text>
</svg>
SVG;
    }
}

$scPage = '<section class="promo">
  <sav-button>Acquista</sav-button>
  <sav-badge>Nuovo</sav-badge>
</section>
<sav-button variant="neutral">Annulla</sav-button>';

return [
    'id' => 'savcomponents-intermediate', 'family' => 'savcomponents', 'after' => 'savcomponents-base', 'categories' => ['frontend', 'design'],
    'title' => L('SavComponents intermedio', 'Intermediate SavComponents'), 'short' => L('SavComponents intermedio', 'Intermediate SavComponents'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Dai componenti all’interfaccia completa: modalità chiara e scura, i cinque stili, token e temi su misura, dialoghi e drawer, navigazione, eventi e validazione da JavaScript, tabelle, grafici e codice, icone e integrazione nei framework, menu laterali e telefoni.', 'From components to a complete interface: light and dark mode, the five styles, tokens and custom themes, dialogs and drawers, navigation, events and validation from JavaScript, tables, charts and code, icons and framework integration, side menus and phones.'),
    'lessons' => [
        lesson('modes-styles', L('Modalità colore e stili', 'Colour modes and styles'), [
            P('La **modalità colore** è una classe: `sav-theme-light` (quella predefinita), `sav-theme-dark` oppure `sav-theme-auto`, che segue l’impostazione del sistema operativo. Si mette su `<html>` per tutta la pagina, ma anche su **un contenitore qualsiasi**: una sezione scura dentro una pagina chiara è solo una classe in più.', 'The **colour mode** is a class: `sav-theme-light` (the default), `sav-theme-dark` or `sav-theme-auto`, which follows the operating system setting. You put it on `<html>` for the whole page, but also on **any container**: a dark section inside a light page is just one more class.'),
            Fig(L(fig_savc_scope('Stessa classe, qualsiasi contenitore:', 'dentro, tutto eredita i suoi token.'), fig_savc_scope('Same class, any container:', 'everything inside inherits its tokens.')),
                L('Una pagina chiara con la classe sav-theme-light su html, con un pulsante e una card chiari; dentro, una section con la classe sav-theme-dark che contiene lo stesso pulsante e una card scura.', 'A light page with the sav-theme-light class on html, holding a light button and card; inside it, a section with the sav-theme-dark class holding the same button and a dark card.'),
                L('Le classi si annidano: vale quella più vicina al componente.', 'The classes nest: the one closest to the component applies.')),
            P('Gli **stili** cambiano il carattere visivo: `sav-style-soft` (predefinito, non serve scriverlo), `sav-style-tilt`, `sav-style-sharp`, `sav-style-brutal` e `sav-style-glass`. Ogni stile cambia solo i **token** (raggi, bordi, ombre…), quindi si combina con qualsiasi colore e con la modalità scura.', '**Styles** change the visual character: `sav-style-soft` (the default, no need to write it), `sav-style-tilt`, `sav-style-sharp`, `sav-style-brutal` and `sav-style-glass`. Each style only changes **tokens** (radii, borders, shadows…), so it combines with any colour and with dark mode.'),
            Fig(fig_savc_styles(),
                L('Cinque colonne, una per stile, ognuna con un pulsante OK e una card. soft: angoli arrotondati e ombra leggera; tilt: angoli vivi e sfondo inclinato con il testo dritto; sharp: angoli quasi vivi e ombra colorata spostata; brutal: bordo scuro spesso e ombra piena; glass: superfici trasparenti sopra macchie di colore.', 'Five columns, one per style, each with an OK button and a card. soft: rounded corners and a light shadow; tilt: square corners and a leaning background with upright text; sharp: nearly square corners and an offset tinted shadow; brutal: a thick dark border and a solid shadow; glass: see-through surfaces over blobs of colour.'),
                L('Stessi componenti, stessi colori: cambiano solo raggi, bordi e ombre.', 'Same components, same colours: only radii, borders and shadows change.')),
            C(L('<html class="sav-theme-auto sav-style-sharp">
  …
  <section class="sav-theme-dark sav-style-glass">
    <sav-card>Una sezione scura e trasparente</sav-card>
  </section>

  <sav-theme-choice apply></sav-theme-choice>
</html>', '<html class="sav-theme-auto sav-style-sharp">
  …
  <section class="sav-theme-dark sav-style-glass">
    <sav-card>A dark, see-through section</sav-card>
  </section>

  <sav-theme-choice apply></sav-theme-choice>
</html>'), 'html'),
            P('Da JavaScript: `Sav.setMode("dark")`, `Sav.getMode()` e `Sav.setStyle("brutal")` lavorano su `<html>`, oppure sull’elemento che passi come secondo argomento. **`<sav-theme-choice>`** offre all’utente chiaro, scuro e automatico; con `apply` cambia da solo la modalità della pagina. La libreria **non salva** la scelta: se vuoi ricordarla, conservala tu (per esempio in `localStorage`) e riapplicala all’avvio.', 'From JavaScript: `Sav.setMode("dark")`, `Sav.getMode()` and `Sav.setStyle("brutal")` work on `<html>`, or on the element you pass as the second argument. **`<sav-theme-choice>`** offers the user light, dark and automatic; with `apply` it changes the page mode by itself. The library **does not save** the choice: to remember it, store it yourself (for example in `localStorage`) and apply it again on start.'),
        ], [
            Q('m1', L('Quale classe segue la modalità chiara o scura del sistema operativo?', 'Which class follows the operating system’s light or dark mode?'), ['sav-theme-auto', 'sav-theme-system', 'sav-mode-os', 'sav-theme-dark'], 0, L('Si basa su `prefers-color-scheme`.', 'It relies on `prefers-color-scheme`.')),
            Q('m2', L('Si può avere una sola sezione scura in una pagina chiara?', 'Can you have a single dark section in a light page?'), [L('Sì, con la classe sul contenitore', 'Yes, with the class on the container'), L('No, vale solo su `<html>`', 'No, it only works on `<html>`'), L('Solo con un secondo foglio di stile', 'Only with a second stylesheet'), L('Solo dentro un iframe', 'Only inside an iframe')], 0, L('I token si ereditano come ogni proprietà CSS.', 'Tokens are inherited like any CSS property.')),
            Q('m3', L('Perché uno stile come Brutal funziona anche in modalità scura?', 'Why does a style like Brutal work in dark mode too?'), [L('Perché cambia solo i token di forma e profondità', 'Because it only changes shape and depth tokens'), L('Perché contiene già due temi completi', 'Because it already contains two full themes'), L('Perché disattiva i colori', 'Because it turns colours off'), L('Perché si carica dopo lo script', 'Because it loads after the script')], 0, L('Colore e stile sono indipendenti.', 'Colour and style are independent.')),
            Q('m4', L('Che cosa fa `<sav-theme-choice apply>`?', 'What does `<sav-theme-choice apply>` do?'), [L('Cambia la modalità della pagina alla scelta', 'Changes the page mode on choice'), L('Salva la scelta sul server', 'Saves the choice on the server'), L('Applica uno stile a caso', 'Applies a random style'), L('Scarica il tema scuro', 'Downloads the dark theme')], 0, L('Senza `apply` comunica solo il valore scelto.', 'Without `apply` it only reports the chosen value.')),
        ], task('html', L('Rendi la `<section>` scura e in stile Glass con le classi giuste, e aggiungi sotto un `<sav-theme-choice>` che cambia da solo la modalità della pagina.', 'Make the `<section>` dark and Glass-styled with the right classes, and add below it a `<sav-theme-choice>` that changes the page mode by itself.'), "<section class=\"\">\n  <sav-card>Offerta del giorno</sav-card>\n</section>\n", [has('section.sav-theme-dark', L('La sezione è scura', 'The section is dark')), has('section.sav-style-glass', L('La sezione è in stile Glass', 'The section is Glass-styled')), has('sav-theme-choice[apply]', L('La scelta del tema si applica', 'The theme choice applies itself'))], '', "<section class=\"sav-theme-dark sav-style-glass\">\n  <sav-card>Offerta del giorno</sav-card>\n</section>\n<sav-theme-choice apply></sav-theme-choice>"), [
            dMatch(pMatch(), [['sav-theme-dark', L('Modalità scura', 'Dark mode')], ['sav-theme-auto', L('Segue il sistema', 'Follows the system')], ['sav-style-brutal', L('Bordi spessi e ombre piene', 'Thick borders and solid shadows')], ['sav-style-glass', L('Superfici trasparenti', 'Transparent surfaces')], ['Sav.setMode()', L('Cambia modalità da JavaScript', 'Changes mode from JavaScript')]], L('Modalità e stili.', 'Modes and styles.')),
            dFlash(pFlash(), [[L('sav-style-soft è lo stile predefinito', 'sav-style-soft is the default style'), true], [L('La libreria ricorda da sola la modalità scelta', 'The library remembers the chosen mode by itself'), false], [L('Sav.setStyle() accetta un elemento come secondo argomento', 'Sav.setStyle() accepts an element as its second argument'), true], [L('Gli stili funzionano solo in modalità chiara', 'Styles only work in light mode'), false]], L('Colore e carattere, indipendenti.', 'Colour and character, independent.')),
        ]),
        lesson('tokens', L('Token e temi su misura', 'Tokens and custom themes'), [
            P('Ogni componente legge delle **proprietà CSS personalizzate** che iniziano con `--sav-`: i **token**. Ci sono i colori di significato (`--sav-color-primary`, `--sav-color-danger`…), il testo da usare sopra di essi (`--sav-color-on-primary`), superfici e testo (`--sav-color-bg`, `--sav-color-text`), forma (`--sav-radius`, `--sav-border-width`), caratteri (`--sav-font`), dimensioni dei controlli (`--sav-control-md`), ombre e animazioni.', 'Every component reads **CSS custom properties** starting with `--sav-`: the **tokens**. There are the colours of meaning (`--sav-color-primary`, `--sav-color-danger`…), the text to use on top of them (`--sav-color-on-primary`), surfaces and text (`--sav-color-bg`, `--sav-color-text`), shape (`--sav-radius`, `--sav-border-width`), fonts (`--sav-font`), control sizes (`--sav-control-md`), shadows and motion.'),
            C(':root {
  --sav-color-primary: #e8590c;
  --sav-color-on-primary: #ffffff;
  --sav-radius: 12px;
  --sav-font: "Inter", system-ui, sans-serif;
}

/* Solo in questa sezione il primario è verde */
.promo {
  --sav-color-primary: #118557;
}', 'css', L('<section class="promo">
  <sav-button>Acquista</sav-button>
  <sav-badge>Nuovo</sav-badge>
</section>
<sav-button>Accedi</sav-button>
<sav-badge>Nuovo</sav-badge>', '<section class="promo">
  <sav-button>Buy</sav-button>
  <sav-badge>New</sav-badge>
</section>
<sav-button>Sign in</sav-button>
<sav-badge>New</sav-badge>')),
            P('Basta **un valore per colore**: le sfumature (sfondo soft, bordo, testo colorato, focus) ne derivano da sole. Quando cambi un colore pieno, cambia anche il suo `on-` perché il testo sopra resti leggibile. Puoi sovrascrivere i token su `:root`, su un contenitore o su un singolo elemento: vince il più vicino, come sempre in CSS.', '**One value per colour** is enough: the shades (soft background, border, coloured text, focus) derive from it. When you change a solid colour, change its `on-` too so that the text on top stays readable. You can override tokens on `:root`, on a container or on a single element: the closest one wins, as always in CSS.'),
            Fig(L(fig_savc_tokens('eredita', 'vince il valore più vicino:', 'qui il primario è verde'), fig_savc_tokens('inherits', 'the closest value wins:', 'here the primary is green')),
                L(':root imposta --sav-color-primary a #e8590c e un sav-button fuori da .promo lo eredita, arancione. La classe .promo ridefinisce il token a #118557, e il sav-button al suo interno è verde.', ':root sets --sav-color-primary to #e8590c and a sav-button outside .promo inherits it, orange. The .promo class sets the token again to #118557, and the sav-button inside it is green.'),
                L('Un token scende lungo l’albero finché qualcuno non lo ridefinisce.', 'A token flows down the tree until something sets it again.')),
            P('Da JavaScript c’è **`Sav.setTheme({ primary: "#e8590c" }, elemento)`**: accetta nomi brevi (`primary` diventa `--sav-color-primary`) e un valore vuoto toglie il token. Sul sito della libreria il **personalizzatore** mostra le modifiche dal vivo: *Export CSS* ti dà le regole da incollare **dopo** `sav.css`; con un account puoi salvare il tema e collegarlo come foglio di stile, `https://savcomponents.com/theme/<id>.css`.', 'From JavaScript there is **`Sav.setTheme({ primary: "#e8590c" }, element)`**: it accepts short names (`primary` becomes `--sav-color-primary`) and an empty value removes the token. On the library’s site the **customiser** shows changes live: *Export CSS* gives you the rules to paste **after** `sav.css`; with an account you can save the theme and link it as a stylesheet, `https://savcomponents.com/theme/<id>.css`.'),
            Tip('Il CSS esportato va dopo `sav.css`: a parità di specificità vince la regola che arriva per ultima.', 'Exported CSS goes after `sav.css`: with equal specificity the rule that comes last wins.'),
        ], [
            Q('t1', L('Come cambi il colore principale di tutti i componenti?', 'How do you change the main colour of every component?'), [L('Impostando `--sav-color-primary` su `:root`', 'By setting `--sav-color-primary` on `:root`'), L('Modificando il file `sav.js`', 'By editing the `sav.js` file'), L('Con `variant="orange"` su ogni tag', 'With `variant="orange"` on every tag'), L('Con un attributo su `<body>`', 'With an attribute on `<body>`')], 0, L('Un solo token, ereditato ovunque.', 'A single token, inherited everywhere.')),
            Q('t2', L('A che cosa serve `--sav-color-on-primary`?', 'What is `--sav-color-on-primary` for?'), [L('Al testo scritto sopra il colore primario', 'For the text written on top of the primary colour'), L('Al colore primario al passaggio del mouse', 'For the primary colour on hover'), L('Ad attivare il colore primario', 'To switch the primary colour on'), L('Al bordo dei campi di testo', 'For the border of text fields')], 0, L('Va scelto per avere abbastanza contrasto.', 'Pick it for enough contrast.')),
            Q('t3', L('Dove va incollato il CSS esportato dal personalizzatore?', 'Where do you paste the CSS exported by the customiser?'), [L('Dopo `sav.css`', 'After `sav.css`'), L('Prima di `sav.css`', 'Before `sav.css`'), L('Dentro `sav.js`', 'Inside `sav.js`'), L('In un attributo `style` su ogni componente', 'In a `style` attribute on every component')], 0, L('L’ultima regola vince.', 'The last rule wins.')),
            Q('t4', L('Che cosa fa `Sav.setTheme({ primary: "" })`?', 'What does `Sav.setTheme({ primary: "" })` do?'), [L('Toglie il token, tornando al valore del tema', 'Removes the token, going back to the theme’s value'), L('Rende il primario trasparente', 'Makes the primary transparent'), L('Genera un errore', 'Throws an error'), L('Imposta il primario a nero', 'Sets the primary to black')], 0, L('Un valore vuoto rimuove la proprietà.', 'An empty value removes the property.')),
        ], task('css', L('Su `:root` imposta `--sav-color-primary` a `#e8590c` e `--sav-radius` a `12px`; poi fai in modo che solo dentro `.promo` il primario sia `#118557`.', 'On `:root` set `--sav-color-primary` to `#e8590c` and `--sav-radius` to `12px`; then make the primary `#118557` only inside `.promo`.'), '', [re(':root\s*\{[^}]*--sav-color-primary\s*:\s*#e8590c', L('Primario arancione su :root', 'Orange primary on :root')), re(':root\s*\{[^}]*--sav-radius\s*:\s*12px', L('Raggio di 12px', '12px radius')), re('\.promo\s*\{[^}]*--sav-color-primary\s*:\s*#118557', L('Primario verde solo in .promo', 'Green primary only in .promo'))], $scPage, ":root {\n  --sav-color-primary: #e8590c;\n  --sav-radius: 12px;\n}\n.promo {\n  --sav-color-primary: #118557;\n}"), [
            dMatch(pMatch(), [['--sav-color-primary', L('Colore principale', 'Main colour')], ['--sav-color-on-primary', L('Testo sopra il principale', 'Text on the main colour')], ['--sav-radius', L('Arrotondamento', 'Rounding')], ['--sav-font', L('Carattere', 'Font')], ['--sav-control-md', L('Altezza dei controlli', 'Control height')]], L('I token principali.', 'The main tokens.')),
            dBug(pBug(), [':root {', '  --sav-color-primary: #e8590c;', '  --sav-primary-on: #ffffff;', '  --sav-radius: 12px;', '}'], 2, '  --sav-color-on-primary: #ffffff;', L('Il testo sopra il primario si chiama --sav-color-on-primary.', 'The text on the primary is called --sav-color-on-primary.')),
        ]),
        lesson('overlays', L('Dialoghi, drawer e tooltip', 'Dialogs, drawers and tooltips'), [
            P('**`<sav-dialog>`** è una finestra modale: `label` è il titolo, il contenuto va nel corpo e i pulsanti nello slot `footer`. Si apre con l’attributo `open` o con il metodo **`show()`**, si chiude con **`hide()`**, con la ✕, con Esc o toccando lo sfondo. Un elemento interno con l’attributo `data-sav-close` (per esempio il pulsante Annulla) lo chiude senza JavaScript. `persistent` impedisce che un clic fuori dalla finestra la chiuda, `no-close` toglie la ✕ e `sheet-on-mobile` la trasforma in un pannello dal basso sui telefoni.', '**`<sav-dialog>`** is a modal window: `label` is the title, the content goes in the body and the buttons in the `footer` slot. It opens with the `open` attribute or the **`show()`** method, and closes with **`hide()`**, with the ✕, with Esc or by tapping the backdrop. An element inside it with the `data-sav-close` attribute (the Cancel button, say) closes it with no JavaScript. `persistent` stops a click outside the window from closing it, `no-close` removes the ✕ and `sheet-on-mobile` turns it into a bottom sheet on phones.'),
            C(L('<sav-button id="apri">Elimina account</sav-button>

<sav-dialog id="conferma" label="Sei sicuro?">
  L’operazione non si può annullare.
  <div slot="footer">
    <sav-button appearance="ghost" id="no">Annulla</sav-button>
    <sav-button variant="danger">Elimina</sav-button>
  </div>
</sav-dialog>', '<sav-button id="apri">Delete account</sav-button>

<sav-dialog id="conferma" label="Are you sure?">
  This cannot be undone.
  <div slot="footer">
    <sav-button appearance="ghost" id="no">Cancel</sav-button>
    <sav-button variant="danger">Delete</sav-button>
  </div>
</sav-dialog>'), 'html'),
            C('const dialogo = document.querySelector("#conferma");
document.querySelector("#apri").addEventListener("click", () => dialogo.show());
document.querySelector("#no").addEventListener("click", () => dialogo.hide());
dialogo.addEventListener("sav-after-hide", () => console.log("chiuso"));', 'js', L('<sav-button id="apri">Elimina account</sav-button>
<sav-dialog id="conferma" label="Sei sicuro?">
  L’operazione non si può annullare.
  <div slot="footer">
    <sav-button appearance="ghost" id="no">Annulla</sav-button>
    <sav-button variant="danger" data-sav-close>Elimina</sav-button>
  </div>
</sav-dialog>
<p id="log"></p>
<script>
  const dialogo = document.querySelector("#conferma");
  document.querySelector("#apri").addEventListener("click", () => dialogo.show());
  document.querySelector("#no").addEventListener("click", () => dialogo.hide());
  dialogo.addEventListener("sav-after-hide", () => document.querySelector("#log").textContent = "sav-after-hide: chiuso");
</script>', '<sav-button id="apri">Delete account</sav-button>
<sav-dialog id="conferma" label="Are you sure?">
  This cannot be undone.
  <div slot="footer">
    <sav-button appearance="ghost" id="no">Cancel</sav-button>
    <sav-button variant="danger" data-sav-close>Delete</sav-button>
  </div>
</sav-dialog>
<p id="log"></p>
<script>
  const dialogo = document.querySelector("#conferma");
  document.querySelector("#apri").addEventListener("click", () => dialogo.show());
  document.querySelector("#no").addEventListener("click", () => dialogo.hide());
  dialogo.addEventListener("sav-after-hide", () => document.querySelector("#log").textContent = "sav-after-hide: closed");
</script>')),
            P('Il dialogo emette `sav-show` e `sav-hide` all’inizio dell’animazione, `sav-after-show` e `sav-after-hide` alla fine. **`<sav-drawer>`** funziona allo stesso modo ma scivola da un lato (`placement`: `start`, `end`, `top`, `bottom`): perfetto per menu e filtri. Per gli aiuti brevi c’è **`<sav-tooltip content="…">`** attorno a un elemento, e **`<sav-info-tip>`**, una piccola icona con il punto interrogativo che al clic apre una spiegazione (funziona anche sui telefoni).', 'The dialog emits `sav-show` and `sav-hide` when its animation starts, `sav-after-show` and `sav-after-hide` when it ends. **`<sav-drawer>`** works the same way but slides in from a side (`placement`: `start`, `end`, `top`, `bottom`): perfect for menus and filters. For short help there is **`<sav-tooltip content="…">`** around an element, and **`<sav-info-tip>`**, a small question-mark icon that opens an explanation when clicked (it works on phones too).'),
            Fig(L(fig_savc_dialog('Apertura', 'Chiusura', 'animazione', 'all’inizio', 'alla fine', 'aperto', 'La chiusura parte anche da ✕, Esc, un clic sullo sfondo o un elemento con data-sav-close.'), fig_savc_dialog('Opening', 'Closing', 'animation', 'at the start', 'at the end', 'open', 'Closing also starts from the ✕, Esc, a click on the backdrop or an element with data-sav-close.')),
                L('Due righe. Apertura: show(), poi l’evento sav-show all’inizio, l’animazione e sav-after-show alla fine, poi il dialogo è aperto. Chiusura: hide(), sav-hide all’inizio, l’animazione e sav-after-hide alla fine.', 'Two rows. Opening: show(), then the sav-show event at the start, the animation and sav-after-show at the end, then the dialog is open. Closing: hide(), sav-hide at the start, the animation and sav-after-hide at the end.'),
                L('Gli eventi `after` arrivano quando l’animazione è finita.', 'The `after` events arrive once the animation has finished.')),
        ], [
            Q('o1', L('Come apri un `<sav-dialog>` da JavaScript?', 'How do you open a `<sav-dialog>` from JavaScript?'), [L('Con il metodo `show()`', 'With the `show()` method'), L('Con `dialog.visible = 1`', 'With `dialog.visible = 1`'), L('Con `Sav.open(dialog)`', 'With `Sav.open(dialog)`'), L('Con `dialog.style.display`', 'With `dialog.style.display`')], 0, L('E `hide()` per chiuderlo.', 'And `hide()` to close it.')),
            Q('o2', L('Dove vanno i pulsanti di un dialogo?', 'Where do a dialog’s buttons go?'), [L('In un elemento con `slot="footer"`', 'In an element with `slot="footer"`'), L('Nell’attributo `buttons`', 'In the `buttons` attribute'), L('Fuori dal dialogo', 'Outside the dialog'), L('Nello slot `label`', 'In the `label` slot')], 0, L('Il footer resta in fondo alla finestra.', 'The footer stays at the bottom of the window.')),
            Q('o3', L('Quale evento arriva quando l’animazione di chiusura è finita?', 'Which event arrives when the closing animation has finished?'), ['sav-after-hide', 'sav-hide', 'close', 'sav-closed'], 0, L('`sav-hide` arriva all’inizio della chiusura.', '`sav-hide` arrives when closing starts.')),
            Q('o4', L('Quale componente scivola dal bordo dello schermo?', 'Which component slides in from the edge of the screen?'), ['<sav-drawer>', '<sav-tooltip>', '<sav-alert>', '<sav-details>'], 0, L('Scegli il lato con `placement`.', 'Pick the side with `placement`.')),
        ], task('js', L('Trova il dialogo `#conferma` e il pulsante `#apri`; al `click` del pulsante apri il dialogo con `show()`, e quando il dialogo ha finito di chiudersi (`sav-after-hide`) scrivi `chiuso` in console.', 'Find the `#conferma` dialog and the `#apri` button; on the button’s `click` open the dialog with `show()`, and when the dialog has finished closing (`sav-after-hide`) log `chiuso` to the console.'), '', [re('querySelector\(\s*["\']#conferma["\']\s*\)|getElementById\(\s*["\']conferma["\']\s*\)', L('Trovi il dialogo', 'You find the dialog')), re('addEventListener\(\s*["\']click["\'][\s\S]*\.show\(\s*\)', L('Il clic apre il dialogo', 'The click opens the dialog')), re('addEventListener\(\s*["\']sav-after-hide["\'][\s\S]*console\.log\(\s*["\']chiuso["\']', L('Reagisci alla chiusura', 'You react to closing'))], '', "const dialogo = document.querySelector(\"#conferma\");\ndocument.querySelector(\"#apri\").addEventListener(\"click\", () => dialogo.show());\ndialogo.addEventListener(\"sav-after-hide\", () => console.log(\"chiuso\"));"), [
            dOrder(pSort(), ['const dialogo = document.querySelector("#conferma");', 'const apri = document.querySelector("#apri");', 'apri.addEventListener("click", () => {', '  dialogo.show();', '});'], L('Prima trovi gli elementi, poi colleghi il clic.', 'First find the elements, then wire up the click.')),
            dMatch(pMatch(), [['persistent', L('Il clic fuori non la chiude', 'A click outside does not close it')], ['no-close', L('Senza la ✕', 'Without the ✕')], ['sheet-on-mobile', L('Pannello dal basso su telefono', 'Bottom sheet on phones')], ['placement="end"', L('Drawer dal lato finale', 'Drawer from the end side')], ['<sav-tooltip>', L('Aiuto breve al passaggio', 'Short help on hover')]], L('Opzioni degli overlay.', 'Overlay options.')),
        ]),
        lesson('navigation', L('Header, menu e schede', 'Headers, menus and tabs'), [
            P('**`<sav-header>`** è la barra in cima al sito, con gli slot `logo`, `search` e `actions`; i link messi nel corpo diventano la navigazione, che si raccoglie in un pulsante menu quando la barra non ha più spazio (o sotto la larghezza indicata in `breakpoint`). Con `sticky` resta visibile mentre scorri. Sui telefoni **`<sav-bottom-nav>`** con i suoi `<sav-bottom-nav-item>` (`label`, `icon`, `href`, `badge`) è la barra delle app in basso.', '**`<sav-header>`** is the bar at the top of the site, with the `logo`, `search` and `actions` slots; links placed in the body become the navigation, which folds into a menu button when the bar runs out of room (or below the width given in `breakpoint`). With `sticky` it stays visible while you scroll. On phones **`<sav-bottom-nav>`** with its `<sav-bottom-nav-item>`s (`label`, `icon`, `href`, `badge`) is the app bar at the bottom.'),
            Fig(L(fig_savc_header('Barra larga', 'Barra stretta', 'Corsi', 'Giochi', 'link e ricerca finiscono', 'dentro il pulsante menu'), fig_savc_header('Wide bar', 'Narrow bar', 'Courses', 'Games', 'links and search go', 'inside the menu button')),
                L('In alto una barra larga con il logo (slot logo), due link, la ricerca (slot search) e un pulsante (slot actions). Sotto una barra stretta con solo logo, pulsante menu e pulsante: link e ricerca sono finiti nel menu.', 'At the top a wide bar with the logo (logo slot), two links, the search (search slot) and a button (actions slot). Below, a narrow bar with only the logo, the menu button and the button: links and search have moved into the menu.'),
                L('L’header guarda la propria larghezza, non quella dello schermo.', 'The header looks at its own width, not the screen’s.')),
            C(L('<sav-header sticky>
  <a slot="logo" href="/">Fucina</a>
  <a href="/corsi">Corsi</a>
  <a href="/giochi">Giochi</a>
  <sav-button slot="actions" size="sm">Accedi</sav-button>
</sav-header>

<sav-breadcrumb>
  <sav-breadcrumb-item href="/">Home</sav-breadcrumb-item>
  <sav-breadcrumb-item href="/corsi">Corsi</sav-breadcrumb-item>
  <sav-breadcrumb-item>SavComponents</sav-breadcrumb-item>
</sav-breadcrumb>', '<sav-header sticky>
  <a slot="logo" href="/">Fucina</a>
  <a href="/courses">Courses</a>
  <a href="/games">Games</a>
  <sav-button slot="actions" size="sm">Sign in</sav-button>
</sav-header>

<sav-breadcrumb>
  <sav-breadcrumb-item href="/">Home</sav-breadcrumb-item>
  <sav-breadcrumb-item href="/courses">Courses</sav-breadcrumb-item>
  <sav-breadcrumb-item>SavComponents</sav-breadcrumb-item>
</sav-breadcrumb>'), 'html'),
            P('**`<sav-tabs>`** contiene dei `<sav-tab>`: il `label` è il titolo della scheda e il contenuto sta dentro il tag. Con `name` puoi aprire una scheda da codice (`tabs.show("prezzi")`) e riconoscerla nell’evento `sav-tab-show`. **`<sav-dropdown>`** apre un menu: il pulsante va nello slot `trigger`, le voci sono `<sav-menu-item>`, e la scelta arriva con l’evento `sav-select`.', '**`<sav-tabs>`** holds `<sav-tab>`s: the `label` is the tab’s title and the content sits inside the tag. With `name` you can open a tab from code (`tabs.show("prezzi")`) and recognise it in the `sav-tab-show` event. **`<sav-dropdown>`** opens a menu: the button goes in the `trigger` slot, the entries are `<sav-menu-item>`s, and the choice arrives with the `sav-select` event.'),
            C(L('<sav-tabs>
  <sav-tab label="Descrizione" name="info">Il corso in breve…</sav-tab>
  <sav-tab label="Prezzi" name="prezzi">Gratis per sempre.</sav-tab>
</sav-tabs>

<sav-dropdown>
  <sav-button slot="trigger" caret>Azioni</sav-button>
  <sav-menu-item value="modifica">Modifica</sav-menu-item>
  <sav-menu-item value="elimina" variant="danger">Elimina</sav-menu-item>
</sav-dropdown>', '<sav-tabs>
  <sav-tab label="Description" name="info">The course in brief…</sav-tab>
  <sav-tab label="Pricing" name="prezzi">Free forever.</sav-tab>
</sav-tabs>

<sav-dropdown>
  <sav-button slot="trigger" caret>Actions</sav-button>
  <sav-menu-item value="modifica">Edit</sav-menu-item>
  <sav-menu-item value="elimina" variant="danger">Delete</sav-menu-item>
</sav-dropdown>'), 'html'),
        ], [
            Q('n1', L('Dove va il pulsante che apre un `<sav-dropdown>`?', 'Where does the button that opens a `<sav-dropdown>` go?'), [L('Nello slot `trigger`', 'In the `trigger` slot'), L('Nello slot `footer`', 'In the `footer` slot'), L('Dopo il dropdown', 'After the dropdown'), L('In un attributo `button`', 'In a `button` attribute')], 0, L('Le voci invece sono `<sav-menu-item>`.', 'The entries are `<sav-menu-item>`s instead.')),
            Q('n2', L('Che cosa diventa il `label` di un `<sav-tab>`?', 'What does a `<sav-tab>`’s `label` become?'), [L('Il titolo della scheda', 'The tab’s title'), L('Il contenuto della scheda', 'The tab’s content'), L('Un suggerimento al passaggio', 'A hover hint'), L('L’indirizzo di una pagina', 'The address of a page')], 0, L('Il contenuto sta tra i tag.', 'The content sits between the tags.')),
            Q('n3', L('Che cosa fa `sticky` su `<sav-header>`?', 'What does `sticky` do on `<sav-header>`?'), [L('Lo tiene visibile mentre scorri', 'Keeps it visible while you scroll'), L('Lo nasconde su telefono', 'Hides it on phones'), L('Blocca i link', 'Locks the links'), L('Lo fissa in fondo alla pagina', 'Pins it to the bottom of the page')], 0, L('Resta in cima alla finestra.', 'It stays at the top of the window.')),
            Q('n4', L('Quale evento ti dice che l’utente ha cambiato scheda?', 'Which event tells you the user has switched tab?'), ['sav-tab-show', 'sav-select', 'change', 'sav-toggle'], 0, L('Nel `detail` trovi `name`, `index` e `tab`.', 'Its `detail` has `name`, `index` and `tab`.')),
        ], task('html', L('Crea un `<sav-tabs>` con tre `<sav-tab>`, ognuno con `label`, `name` e un contenuto; poi un `<sav-dropdown>` con un `<sav-button>` nello slot `trigger` e almeno due `<sav-menu-item>`.', 'Create a `<sav-tabs>` with three `<sav-tab>`s, each with a `label`, a `name` and some content; then a `<sav-dropdown>` with a `<sav-button>` in the `trigger` slot and at least two `<sav-menu-item>`s.'), '', [has('sav-tabs > sav-tab[label][name] + sav-tab[label][name] + sav-tab[label][name]', L('Tre schede con titolo e nome', 'Three tabs with title and name')), has('sav-dropdown > sav-button[slot="trigger"]', L('Pulsante nello slot trigger', 'Button in the trigger slot')), has('sav-dropdown > sav-menu-item + sav-menu-item', L('Almeno due voci', 'At least two entries'))], '', "<sav-tabs>\n  <sav-tab label=\"Info\" name=\"info\">…</sav-tab>\n  <sav-tab label=\"Prezzi\" name=\"prezzi\">…</sav-tab>\n  <sav-tab label=\"FAQ\" name=\"faq\">…</sav-tab>\n</sav-tabs>\n<sav-dropdown>\n  <sav-button slot=\"trigger\">Azioni</sav-button>\n  <sav-menu-item value=\"a\">Modifica</sav-menu-item>\n  <sav-menu-item value=\"b\">Elimina</sav-menu-item>\n</sav-dropdown>"), [
            dMatch(pMatch(), [['<sav-header>', L('Barra in cima al sito', 'Bar at the top of the site')], ['<sav-bottom-nav>', L('Barra in basso su telefono', 'Bottom bar on phones')], ['<sav-breadcrumb>', L('Dove ti trovi', 'Where you are')], ['<sav-tabs>', L('Contenuti a schede', 'Tabbed content')], ['<sav-dropdown>', L('Menu che si apre', 'Menu that opens')]], L('I componenti di navigazione.', 'Navigation components.')),
            dBug(pBug(), ['<sav-dropdown>', '  <sav-button slot="header">Azioni</sav-button>', '  <sav-menu-item value="modifica">Modifica</sav-menu-item>', '</sav-dropdown>'], 1, '  <sav-button slot="trigger">Azioni</sav-button>', L('Il pulsante di un dropdown va nello slot trigger.', 'A dropdown’s button goes in the trigger slot.')),
        ]),
        lesson('events-validation', L('Eventi e validazione', 'Events and validation'), [
            P('I campi emettono gli eventi di sempre, **`input`** mentre scrivi e **`change`** quando il valore è confermato, e hanno la proprietà **`value`** da leggere e scrivere. Gli eventi propri della libreria iniziano con `sav-` (`sav-clear`, `sav-remove`, `sav-select`, `sav-tab-show`…) e portano i dati in **`event.detail`**.', 'Fields emit the usual events, **`input`** while you type and **`change`** when the value is confirmed, and have the **`value`** property to read and write. The library’s own events start with `sav-` (`sav-clear`, `sav-remove`, `sav-select`, `sav-tab-show`…) and carry their data in **`event.detail`**.'),
            P('Alcuni eventi si possono **annullare** con `event.preventDefault()`: `sav-show` di un dropdown o di un campo a comparsa come il date picker, `sav-close` di un avviso, `sav-remove` di un tag o di un chip. Così puoi, per esempio, chiedere conferma prima di togliere un elemento.', 'Some events can be **cancelled** with `event.preventDefault()`: the `sav-show` of a dropdown or of a pop-up field such as the date picker, an alert’s `sav-close`, a tag’s or a chip’s `sav-remove`. That way you can, for instance, ask for confirmation before removing an item.'),
            Fig(L(fig_savc_bubble(['Clic sulla ✕', 'dentro lo shadow DOM del tag', 'Il tag emette sav-remove', 'bubbles, composed, cancelable', 'Sale fino al document', 'chi ascolta può fare preventDefault()', 'Annullato: il tag resta', 'altrimenti si toglie da solo']), fig_savc_bubble(['Click on the ✕', 'inside the tag’s shadow DOM', 'The tag emits sav-remove', 'bubbles, composed, cancelable', 'It climbs to the document', 'a listener can call preventDefault()', 'Cancelled: the tag stays', 'otherwise it removes itself'])),
                L('Scatole annidate: document, section, sav-tag e, dentro il tag, il suo shadow DOM con la ✕. Una freccia tratteggiata, con un puntino che sale, va dalla ✕ fino al document passando per tutti i contenitori; accanto, quattro passi numerati spiegano il percorso.', 'Nested boxes: document, section, sav-tag and, inside the tag, its shadow DOM with the ✕. A dashed arrow, with a dot climbing it, goes from the ✕ up to the document through every container; beside it, four numbered steps explain the path.'),
                L('`composed` fa uscire l’evento dallo shadow DOM, `bubbles` lo fa salire.', '`composed` lets the event out of the shadow DOM, `bubbles` makes it climb.')),
            C('const pass = document.querySelector("#pass");
const conferma = document.querySelector("#conferma");

conferma.addEventListener("input", () => {
  conferma.setCustomValidity(conferma.value === pass.value ? "" : "Le password non coincidono");
});

document.querySelector("sav-tag").addEventListener("sav-remove", (e) => {
  if (!confirm("Rimuovere il filtro?")) e.preventDefault();
});', 'js', L('<form>
  <sav-input id="pass" type="password" label="Password"></sav-input>
  <sav-input id="conferma" type="password" label="Ripeti la password"></sav-input>
  <sav-button type="submit">Registrati</sav-button>
</form>
<script>
  const pass = document.querySelector("#pass");
  const conferma = document.querySelector("#conferma");
  conferma.addEventListener("input", () => {
    conferma.setCustomValidity(conferma.value === pass.value ? "" : "Le password non coincidono");
  });
</script>', '<form>
  <sav-input id="pass" type="password" label="Password"></sav-input>
  <sav-input id="conferma" type="password" label="Repeat the password"></sav-input>
  <sav-button type="submit">Sign up</sav-button>
</form>
<script>
  const pass = document.querySelector("#pass");
  const conferma = document.querySelector("#conferma");
  conferma.addEventListener("input", () => {
    conferma.setCustomValidity(conferma.value === pass.value ? "" : "The passwords do not match");
  });
</script>')),
            P('I campi espongono la stessa **API di validazione** degli input nativi: `validity`, `validationMessage`, `checkValidity()`, `reportValidity()` e **`setCustomValidity(messaggio)`** per una regola tua (una stringa vuota toglie l’errore). Se lo script potrebbe girare prima che i componenti siano definiti, aspetta con `await customElements.whenDefined("sav-input")`.', 'Fields expose the same **validation API** as native inputs: `validity`, `validationMessage`, `checkValidity()`, `reportValidity()` and **`setCustomValidity(message)`** for a rule of your own (an empty string clears the error). If your script might run before the components are defined, wait with `await customElements.whenDefined("sav-input")`.'),
        ], [
            Q('e1', L('Come impedisci che un tag venga rimosso quando l’utente tocca la ✕?', 'How do you stop a tag from being removed when the user taps the ✕?'), [L('`preventDefault()` nell’evento `sav-remove`', '`preventDefault()` in the `sav-remove` event'), L('`return false` nell’attributo `removable`', '`return false` in the `removable` attribute'), L('Togliendo il tag dal DOM', 'By removing the tag from the DOM'), L('Con `stopPropagation()` sul form', 'With `stopPropagation()` on the form')], 0, L('`sav-remove` è annullabile.', '`sav-remove` is cancelable.')),
            Q('e2', L('Come segnali un errore personalizzato su un `<sav-input>`?', 'How do you flag a custom error on a `<sav-input>`?'), ['setCustomValidity("…")', 'error = "…"', 'Sav.invalid(campo)', 'classList.add("invalid")'], 0, L('Con una stringa vuota l’errore sparisce.', 'An empty string clears the error.')),
            Q('e3', L('Dove trovi i dati di un evento come `sav-tab-show`?', 'Where do you find the data of an event such as `sav-tab-show`?'), ['event.detail', 'event.data', 'event.value', 'event.target.dataset'], 0, L('È lo standard dei `CustomEvent`.', 'It is the `CustomEvent` standard.')),
            Q('e4', L('A che cosa serve `customElements.whenDefined("sav-input")`?', 'What is `customElements.whenDefined("sav-input")` for?'), [L('Ad aspettare che il componente sia pronto', 'To wait until the component is ready'), L('A registrare un nuovo componente', 'To register a new component'), L('A controllare se il campo è valido', 'To check whether the field is valid'), L('A scaricare la libreria', 'To download the library')], 0, L('Restituisce una Promise.', 'It returns a Promise.')),
        ], task('js', L('Quando cambia (`input`) il campo `conferma`, se il suo `value` è diverso da quello di `pass` imposta l’errore `Le password non coincidono` con `setCustomValidity`, altrimenti toglilo con una stringa vuota.', 'When the `conferma` field changes (`input`), if its `value` differs from `pass`’s set the error `Le password non coincidono` with `setCustomValidity`, otherwise clear it with an empty string.'), "const pass = document.querySelector(\"#pass\");\nconst conferma = document.querySelector(\"#conferma\");\n", [re('conferma\.addEventListener\(\s*["\']input["\']', L('Ascolti l’evento input', 'You listen to the input event')), re('\.value\s*[!=]==?\s*(pass|conferma)\.value', L('Confronti i due valori', 'You compare the two values')), re('setCustomValidity\(\s*["\']Le password non coincidono["\']', L('Imposti il messaggio d’errore', 'You set the error message')), re('setCustomValidity\(\s*["\']["\']\s*\)|\?\s*["\']["\']\s*:', L('Togli l’errore quando coincidono', 'You clear the error when they match'))], '', "conferma.addEventListener(\"input\", () => {\n  if (conferma.value !== pass.value) conferma.setCustomValidity(\"Le password non coincidono\");\n  else conferma.setCustomValidity(\"\");\n});"), [
            dBlanks(pFill(), 'tag.addEventListener("sav-___", (e) => {' . "\n" . '  if (!confirm("Sicuro?")) e.___();' . "\n" . '});', ['remove', 'preventDefault'], ['delete', 'stopPropagation'], L('Annullare la rimozione di un tag.', 'Cancelling the removal of a tag.')),
            dFlash(pFlash(), [[L('I campi di SavComponents emettono input e change', 'SavComponents fields emit input and change'), true], [L('Gli eventi sav-* mettono i dati in event.detail', 'sav-* events put their data in event.detail'), true], [L('setCustomValidity("") imposta un errore vuoto che blocca il form', 'setCustomValidity("") sets an empty error that blocks the form'), false], [L('Nessun evento della libreria si può annullare', 'No library event can be cancelled'), false]], L('Eventi standard, più quelli della libreria.', 'Standard events, plus the library’s own.')),
        ]),
        lesson('data-media', L('Tabelle, grafici, codice e media', 'Tables, charts, code and media'), [
            P('**`<sav-table>`** avvolge una normale `<table>` e le dà lo stile del tema: `striped` per le righe alterne, `hover` per evidenziarle, `compact` per stringerle e `sortable` per ordinare toccando le intestazioni (i numeri si ordinano come numeri). I dati restano una tabella HTML vera, leggibile da tutti.', '**`<sav-table>`** wraps an ordinary `<table>` and gives it the theme’s style: `striped` for alternating rows, `hover` to highlight them, `compact` to tighten them and `sortable` to sort by tapping the headers (numbers sort as numbers). The data stays a real HTML table that everyone can read.'),
            C(L('<sav-table striped sortable>
  <table>
    <thead><tr><th>Corso</th><th>Livelli</th></tr></thead>
    <tbody>
      <tr><td>HTML base</td><td>8</td></tr>
      <tr><td>CSS base</td><td>7</td></tr>
    </tbody>
  </table>
</sav-table>', '<sav-table striped sortable>
  <table>
    <thead><tr><th>Course</th><th>Levels</th></tr></thead>
    <tbody>
      <tr><td>HTML Basics</td><td>8</td></tr>
      <tr><td>CSS Basics</td><td>7</td></tr>
    </tbody>
  </table>
</sav-table>'), 'html'),
            P('I **grafici** non richiedono librerie: `<sav-chart-bar>`, `<sav-chart-line>`, `<sav-chart-pie>`, `<sav-chart-radar>`, `<sav-chart-activity>` e `<sav-chart-map>` leggono i dati in **JSON** dall’attributo `data` (o dalla proprietà `.data`): `{"labels": […], "values": […]}` per una serie, `series` per più serie; il calendario di `<sav-chart-activity>` vuole delle date e la mappa i codici dei paesi. Dai sempre un `label` al grafico: è la sua descrizione accessibile.', '**Charts** need no libraries: `<sav-chart-bar>`, `<sav-chart-line>`, `<sav-chart-pie>`, `<sav-chart-radar>`, `<sav-chart-activity>` and `<sav-chart-map>` read their data as **JSON** from the `data` attribute (or the `.data` property): `{"labels": […], "values": […]}` for one series, `series` for several; the `<sav-chart-activity>` calendar wants dates and the map wants country codes. Always give the chart a `label`: it is its accessible description.'),
            C(L('<sav-chart-pie label="Iscritti per corso"
  data=\'{"labels": ["HTML", "CSS", "JS"], "values": [120, 80, 60]}\'></sav-chart-pie>

<sav-code-block language="js" filename="app.js" highlight="2">
  const nome = "Fucina";
  console.log(nome);
</sav-code-block>', '<sav-chart-pie label="Learners per course"
  data=\'{"labels": ["HTML", "CSS", "JS"], "values": [120, 80, 60]}\'></sav-chart-pie>

<sav-code-block language="js" filename="app.js" highlight="2">
  const nome = "Fucina";
  console.log(nome);
</sav-code-block>'), 'html'),
            P('**`<sav-code-block>`** colora il codice scritto fra i tag (toglie da solo il rientro comune), con `filename`, i numeri di riga, le righe evidenziate con `highlight="2,4-6"` e un pulsante per copiare. Per i media: **`<sav-image>`** con `ratio`, `caption` e `zoom`; **`<sav-carousel>`** con dei `<sav-slide>`; **`<sav-video>`** e **`<sav-audio>`** con controlli nello stile del tema.', '**`<sav-code-block>`** highlights the code written between its tags (it strips the shared indentation by itself), with `filename`, line numbers, highlighted lines with `highlight="2,4-6"` and a copy button. For media: **`<sav-image>`** with `ratio`, `caption` and `zoom`; **`<sav-carousel>`** with `<sav-slide>`s; **`<sav-video>`** and **`<sav-audio>`** with controls in the theme’s style.'),
        ], [
            Q('d1', L('Che cosa contiene un `<sav-table>`?', 'What does a `<sav-table>` contain?'), [L('Una normale `<table>` HTML', 'An ordinary HTML `<table>`'), L('Un attributo JSON con le righe', 'A JSON attribute with the rows'), L('Dei `<sav-row>`', 'Some `<sav-row>`s'), L('Un file CSV collegato', 'A linked CSV file')], 0, L('I dati restano HTML accessibile.', 'The data stays accessible HTML.')),
            Q('d2', L('In che formato passi i dati a `<sav-chart-bar>`?', 'In what format do you pass data to `<sav-chart-bar>`?'), [L('JSON nell’attributo `data`', 'JSON in the `data` attribute'), L('Una tabella dentro il tag', 'A table inside the tag'), L('Un file Excel', 'An Excel file'), L('Attributi `value-1`, `value-2`…', '`value-1`, `value-2`… attributes')], 0, L('`labels` e `values`, oppure `series`.', '`labels` and `values`, or `series`.')),
            Q('d3', L('Come evidenzi le righe 3, 4 e 5 in un `<sav-code-block>`?', 'How do you highlight lines 3, 4 and 5 in a `<sav-code-block>`?'), ['highlight="3-5"', 'lines="3,5"', 'mark="3:5"', 'focus="3-5"'], 0, L('Accetta numeri e intervalli separati da virgole.', 'It accepts numbers and ranges separated by commas.')),
            Q('d4', L('Perché dare un `label` a un grafico?', 'Why give a chart a `label`?'), [L('È la sua descrizione accessibile', 'It is its accessible description'), L('Per cambiarne il colore', 'To change its colour'), L('Per esportarlo in PDF', 'To export it as a PDF'), L('Senza non si disegna', 'It does not draw without it')], 0, L('Chi non vede il grafico ne sente il titolo.', 'Whoever cannot see the chart hears its title.')),
        ], task('html', L('Avvolgi una `<table>` (con `<thead>` e almeno un `<th>`) in un `<sav-table>` ordinabile e a righe alterne; poi aggiungi un `<sav-chart-bar>` con un `label` e un `data` JSON con `labels` e `values`.', 'Wrap a `<table>` (with a `<thead>` and at least one `<th>`) in a sortable, striped `<sav-table>`; then add a `<sav-chart-bar>` with a `label` and a JSON `data` with `labels` and `values`.'), '', [has('sav-table[sortable][striped] > table thead th', L('Tabella ordinabile a righe alterne', 'Sortable striped table')), has('sav-chart-bar[label]', L('Il grafico ha un label', 'The chart has a label')), has('sav-chart-bar[data*="labels"][data*="values"]', L('Dati JSON con labels e values', 'JSON data with labels and values'))], '', "<sav-table sortable striped>\n  <table>\n    <thead><tr><th>Corso</th><th>Livelli</th></tr></thead>\n    <tbody><tr><td>HTML</td><td>8</td></tr></tbody>\n  </table>\n</sav-table>\n<sav-chart-bar label=\"Livelli\" data='{\"labels\": [\"HTML\", \"CSS\"], \"values\": [8, 7]}'></sav-chart-bar>"), [
            dMatch(pMatch(), [['striped', L('Righe alterne', 'Alternating rows')], ['sortable', L('Ordina dalle intestazioni', 'Sorts from the headers')], ['<sav-chart-pie>', L('Grafico a torta', 'Pie chart')], ['<sav-code-block>', L('Codice colorato', 'Highlighted code')], ['<sav-carousel>', L('Diapositive che scorrono', 'Sliding slides')]], L('Dati e media.', 'Data and media.')),
            dBug(pBug(), ['<sav-chart-bar label="Visite"', '  data="labels: Lun, Mar; values: 10, 14">', '</sav-chart-bar>'], 1, '  data=\'{"labels": ["Lun", "Mar"], "values": [10, 14]}\'>', L('I dati dei grafici sono JSON.', 'Chart data is JSON.')),
        ]),
        lesson('icons-integration', L('Icone, accessibilità e integrazione', 'Icons, accessibility and integration'), [
            P('Le icone di `<sav-icon>`, `icon` e `icon-end` vengono da un set incluso (in gran parte Lucide) e si caricano quando servono. Puoi aggiungere le tue con **`Sav.registerIcon(nome, forme)`**: passi solo il contenuto dell’SVG (`<path>`, `<circle>`…) disegnato su una griglia 24×24, che la libreria traccia con il colore del testo; i `<sav-icon>` con quel nome si aggiornano subito. Per caricare il set di icone da un altro indirizzo c’è `Sav.iconBase`.', 'The icons of `<sav-icon>`, `icon` and `icon-end` come from an included set (mostly Lucide) and load when needed. You can add your own with **`Sav.registerIcon(name, shapes)`**: you pass only the inside of the SVG (`<path>`, `<circle>`…) drawn on a 24×24 grid, which the library strokes with the text colour; the `<sav-icon>`s with that name update at once. To load the icon set from another address there is `Sav.iconBase`.'),
            Fig(L(fig_savc_icon('griglia 24×24', 'stessa icona,', 'colore del testo', 'Forgia'), fig_savc_icon('24×24 grid', 'same icon,', 'colour of the text', 'Forge')),
                L('A sinistra un’incudine disegnata su una griglia 24×24; una freccia la porta in tre pulsanti «Forgia»: pieno blu con icona bianca, con contorno blu e icona blu, scuro con icona bianca.', 'On the left an anvil drawn on a 24×24 grid; an arrow carries it into three “Forge” buttons: solid blue with a white icon, outlined blue with a blue icon, dark with a white icon.'),
                L('Passi solo le forme: il colore lo decide il testo intorno.', 'You pass only the shapes: the text around decides the colour.')),
            C('Sav.registerIcon("incudine", \'<path d="M4 10h16l-3 4H7z"/><path d="M10 14v5M14 14v5"/>\');

// <sav-button icon="incudine">Forgia</sav-button> ora mostra la tua icona', 'js', L('<script>
  Sav.registerIcon("incudine", \'<path d="M4 10h16l-3 4H7z"/><path d="M10 14v5M14 14v5"/>\');
</script>
<sav-button icon="incudine">Forgia</sav-button>
<sav-button icon="incudine" appearance="outline">Forgia</sav-button>
<sav-icon name="incudine" label="Incudine" style="font-size: 2rem"></sav-icon>', '<script>
  Sav.registerIcon("incudine", \'<path d="M4 10h16l-3 4H7z"/><path d="M10 14v5M14 14v5"/>\');
</script>
<sav-button icon="incudine">Forge</sav-button>
<sav-button icon="incudine" appearance="outline">Forge</sav-button>
<sav-icon name="incudine" label="Anvil" style="font-size: 2rem"></sav-icon>')),
            P('I componenti curano già tastiera, focus e ruoli ARIA, ma i **contenuti restano tuoi**: un `label` per i pulsanti e le icone senza testo, un `alt` per le immagini, un `label` per i grafici, colori del tema con abbastanza contrasto (i token predefiniti sono scelti per il livello AA).', 'The components already take care of keyboard, focus and ARIA roles, but the **content stays yours**: a `label` for buttons and icons without text, an `alt` for images, a `label` for charts, theme colours with enough contrast (the default tokens are chosen for level AA).'),
            P('Essendo tag standard, i componenti funzionano in **qualsiasi framework**: in React, Vue o Angular si scrivono nel template come qualsiasi elemento (a Vue e Angular basta dire che i tag `sav-*` non sono componenti loro: `isCustomElement` e `CUSTOM_ELEMENTS_SCHEMA`), i dati semplici passano come attributi, quelli complessi come proprietà, e gli eventi `sav-*` si ascoltano con `addEventListener` (o con la sintassi del framework per gli eventi personalizzati). In PHP basta stamparli nel template.', 'Being standard tags, the components work in **any framework**: in React, Vue or Angular you write them in the template like any element (Vue and Angular only need to be told that `sav-*` tags are not their own components: `isCustomElement` and `CUSTOM_ELEMENTS_SCHEMA`), simple data goes in as attributes, complex data as properties, and `sav-*` events are listened to with `addEventListener` (or with the framework’s syntax for custom events). In PHP you just print them in the template.'),
            Tip('Per aggiornare la libreria cambia il numero di versione negli indirizzi di `sav.css` e `sav.js` e leggi il changelog: le versioni pubblicate non cambiano mai.', 'To update the library change the version number in the `sav.css` and `sav.js` addresses and read the changelog: published versions never change.'),
        ], [
            Q('ic1', L('Come aggiungi un’icona tua alla libreria?', 'How do you add your own icon to the library?'), ['Sav.registerIcon(nome, svg)', 'Sav.icon.add(svg)', '<sav-icon src="…">', 'Sav.addIcon(svg, nome)'], 0, L('Passi il nome e le forme SVG su una griglia 24×24.', 'You pass the name and the SVG shapes on a 24×24 grid.')),
            Q('ic2', L('Che cosa resta compito tuo per l’accessibilità?', 'What remains your job for accessibility?'), [L('Label, alt e contrasto dei colori', 'Labels, alts and colour contrast'), L('La gestione della tastiera', 'Keyboard handling'), L('I ruoli ARIA di ogni componente', 'Every component’s ARIA roles'), L('Niente, fa tutto la libreria', 'Nothing, the library does it all')], 0, L('La libreria non conosce i tuoi contenuti.', 'The library does not know your content.')),
            Q('ic3', L('Come ascolti `sav-select` di un dropdown in un’app React?', 'How do you listen to a dropdown’s `sav-select` in a React app?'), [L('Con `addEventListener` su un riferimento all’elemento', 'With `addEventListener` on a reference to the element'), L('Non si può, serve una versione React', 'You cannot, you need a React version'), L('Con `onClick` sul dropdown', 'With `onClick` on the dropdown'), L('Con un `<form>` attorno', 'With a `<form>` around it')], 0, L('Sono eventi DOM standard.', 'They are standard DOM events.')),
        ], task('js', L('Registra un’icona chiamata `incudine` con `Sav.registerIcon` (una forma SVG qualsiasi, per esempio un `<path>`), poi trova il primo `sav-button` e impostagli l’attributo `icon` a `incudine` con `setAttribute`.', 'Register an icon called `incudine` with `Sav.registerIcon` (any SVG shape, a `<path>` for instance), then find the first `sav-button` and set its `icon` attribute to `incudine` with `setAttribute`.'), '', [re('Sav\.registerIcon\(\s*["\']incudine["\']\s*,\s*[`"\']\s*<(path|circle|rect|line|polyline|polygon|ellipse|g)\b', L('Registri l’icona con le sue forme', 'You register the icon with its shapes')), re('querySelector\(\s*["\']sav-button["\']\s*\)', L('Trovi il pulsante', 'You find the button')), re('setAttribute\(\s*["\']icon["\']\s*,\s*["\']incudine["\']\s*\)', L('Gli dai l’icona', 'You give it the icon'))], '', "Sav.registerIcon(\"incudine\", '<path d=\"M4 10h16l-3 4H7z\"/>');\ndocument.querySelector(\"sav-button\").setAttribute(\"icon\", \"incudine\");"), [
            dFlash(pFlash(), [[L('I componenti funzionano anche in Vue e Angular', 'The components work in Vue and Angular too'), true], [L('Una versione pubblicata può cambiare senza preavviso', 'A published version can change without notice'), false], [L('Un pulsante solo icona ha bisogno di label', 'An icon-only button needs a label'), true], [L('Le icone vanno tutte scaricate prima di usare la libreria', 'All icons must be downloaded before using the library'), false]], L('Standard, accessibili, stabili.', 'Standard, accessible, stable.')),
            dMatch(pMatch(), [['Sav.registerIcon()', L('Aggiunge un’icona', 'Adds an icon')], ['Sav.toast()', L('Avviso passeggero', 'Passing notice')], ['Sav.setMode()', L('Chiaro o scuro', 'Light or dark')], ['Sav.setStyle()', L('Uno dei cinque stili', 'One of the five styles')], ['Sav.setTheme()', L('Cambia i token', 'Changes tokens')]], L('L’API globale Sav.', 'The global Sav API.')),
        ]),
        lesson('menus-breadcrumbs', L('Menu laterali e percorsi', 'Side menus and breadcrumbs'), [
            P('**`<sav-menu>`** è un menu che resta nella pagina, fatto con gli stessi `<sav-menu-item>` del dropdown: `<sav-menu-label>` dà un titolo a un gruppo, `<sav-divider>` separa. Con **`selectable`** la voce cliccata diventa quella corrente (all’inizio la segni con `current`) e la scelta arriva con l’evento `sav-select` (`detail.value`).', '**`<sav-menu>`** is a menu that stays on the page, made of the same `<sav-menu-item>`s as the dropdown: `<sav-menu-label>` titles a group, `<sav-divider>` separates. With **`selectable`** the clicked item becomes the current one (mark it with `current` at the start) and the choice arrives with the `sav-select` event (`detail.value`).'),
            P('Ha più forme: una lista verticale (una barra laterale, predefinita), una barra orizzontale con `orientation="horizontal"` i cui sottomenu scendono, una colonna di sole icone con **`collapsed`** (le etichette diventano tooltip) e `collapsible`, che aggiunge il pulsante per passare dall’una all’altra. Un sottomenu è un **`<sav-menu slot="submenu">`** dentro una voce; `open` sulla voce lo mostra aperto.', 'It comes in several forms: a vertical list (a sidebar, the default), a horizontal bar with `orientation="horizontal"` whose submenus drop down, a column of icons only with **`collapsed`** (the labels become tooltips) and `collapsible`, which adds the button to switch between the two. A submenu is a **`<sav-menu slot="submenu">`** inside an item; `open` on the item shows it open.'),
            C(L('<sav-menu label="Account" appearance="card" selectable>
  <sav-menu-label>Account</sav-menu-label>
  <sav-menu-item value="profilo" current><sav-icon slot="prefix" name="user"></sav-icon>Profilo</sav-menu-item>
  <sav-menu-item><sav-icon slot="prefix" name="folder"></sav-icon>Progetti
    <sav-menu slot="submenu">
      <sav-menu-item value="sito">Sito</sav-menu-item>
      <sav-menu-item value="app">App</sav-menu-item>
    </sav-menu>
  </sav-menu-item>
  <sav-divider></sav-divider>
  <sav-menu-item value="esci" variant="danger">Esci</sav-menu-item>
</sav-menu>', '<sav-menu label="Account" appearance="card" selectable>
  <sav-menu-label>Account</sav-menu-label>
  <sav-menu-item value="profilo" current><sav-icon slot="prefix" name="user"></sav-icon>Profile</sav-menu-item>
  <sav-menu-item><sav-icon slot="prefix" name="folder"></sav-icon>Projects
    <sav-menu slot="submenu">
      <sav-menu-item value="sito">Website</sav-menu-item>
      <sav-menu-item value="app">App</sav-menu-item>
    </sav-menu>
  </sav-menu-item>
  <sav-divider></sav-divider>
  <sav-menu-item value="esci" variant="danger">Sign out</sav-menu-item>
</sav-menu>'), 'html'),
            P('In **`<sav-breadcrumb>`** l’ultima voce è la pagina corrente: non serve `href` e la libreria la segna da sola per gli screen reader. `separator` cambia il simbolo tra le voci, `appearance="contained"` mette il percorso in una barra e **`max-items`** ripiega le voci di mezzo dietro «…», che si apre con un clic. Nell’header, il link corrente lo segni tu con `aria-current="page"`.', 'In **`<sav-breadcrumb>`** the last item is the current page: it needs no `href` and the library marks it for screen readers by itself. `separator` changes the symbol between items, `appearance="contained"` puts the path in a bar and **`max-items`** folds the middle items behind “…”, which opens with a click. In the header, you mark the current link yourself with `aria-current="page"`.'),
        ], [
            Q('mb1', L('Come metti un sottomenu dentro una voce di `<sav-menu>`?', 'How do you put a submenu inside a `<sav-menu>` item?'), [L('Con un `<sav-menu slot="submenu">` dentro la voce', 'With a `<sav-menu slot="submenu">` inside the item'), L('Con l’attributo `submenu` sulla voce', 'With the `submenu` attribute on the item'), L('Con un `<sav-dropdown>` accanto alla voce', 'With a `<sav-dropdown>` next to the item'), L('Con un tag `<sav-submenu>`', 'With a `<sav-submenu>` tag')], 0, L('Un sottomenu può contenere altri sottomenu.', 'A submenu can hold further submenus.')),
            Q('mb2', L('Che cosa fa `max-items="3"` su `<sav-breadcrumb>`?', 'What does `max-items="3"` do on `<sav-breadcrumb>`?'), [L('Cancella le voci oltre la terza', 'Deletes the items after the third'), L('Ripiega le voci di mezzo dietro «…»', 'Folds the middle items behind “…”'), L('Rifiuta più di tre voci', 'Refuses more than three items'), L('Divide il percorso su tre righe', 'Splits the path over three lines')], 1, L('Restano la prima voce e le ultime; «…» riapre il resto.', 'The first and last items stay; “…” opens the rest.')),
            Q('mb3', L('Quale attributo trasforma un `<sav-menu>` in una colonna di sole icone?', 'Which attribute turns a `<sav-menu>` into a column of icons only?'), ['compact', 'icons-only', 'collapsed', 'mini'], 2, L('`collapsible` aggiunge il pulsante per aprirla e chiuderla.', '`collapsible` adds the button to open and close it.')),
        ], task('html', L('Crea un `<sav-menu>` con `label` e `selectable`, con una voce `current` e una voce che contiene un `<sav-menu slot="submenu">` con almeno una voce; poi un `<sav-breadcrumb max-items="3">` con quattro `<sav-breadcrumb-item>`.', 'Create a `<sav-menu>` with `label` and `selectable`, with a `current` item and an item holding a `<sav-menu slot="submenu">` with at least one item; then a `<sav-breadcrumb max-items="3">` with four `<sav-breadcrumb-item>`s.'), '', [has('sav-menu[label][selectable] > sav-menu-item[current]', L('Menu con la voce corrente', 'Menu with the current item')), has('sav-menu > sav-menu-item > sav-menu[slot="submenu"] > sav-menu-item', L('Un sottomenu dentro una voce', 'A submenu inside an item')), has('sav-breadcrumb[max-items="3"] > sav-breadcrumb-item + sav-breadcrumb-item + sav-breadcrumb-item + sav-breadcrumb-item', L('Percorso di quattro voci ripiegato', 'Folded four-item path'))], '', "<sav-menu label=\"Account\" selectable>\n  <sav-menu-item value=\"profilo\" current>Profilo</sav-menu-item>\n  <sav-menu-item>Progetti\n    <sav-menu slot=\"submenu\">\n      <sav-menu-item value=\"sito\">Sito</sav-menu-item>\n    </sav-menu>\n  </sav-menu-item>\n</sav-menu>\n<sav-breadcrumb max-items=\"3\">\n  <sav-breadcrumb-item href=\"/\">Home</sav-breadcrumb-item>\n  <sav-breadcrumb-item href=\"/corsi\">Corsi</sav-breadcrumb-item>\n  <sav-breadcrumb-item href=\"/corsi/web\">Web</sav-breadcrumb-item>\n  <sav-breadcrumb-item>SavComponents</sav-breadcrumb-item>\n</sav-breadcrumb>"), [
            dOrder(pSort(), ['<sav-menu label="Progetti" selectable>', '  <sav-menu-item>Progetti', '    <sav-menu slot="submenu">', '      <sav-menu-item value="sito">Sito</sav-menu-item>', '    </sav-menu>', '  </sav-menu-item>', '</sav-menu>'], L('Il sottomenu sta dentro la voce, la voce dentro il menu.', 'The submenu sits inside the item, the item inside the menu.')),
            dMatch(pMatch(), [['selectable', L('La voce cliccata diventa corrente', 'The clicked item becomes current')], ['collapsed', L('Solo icone', 'Icons only')], ['orientation="horizontal"', L('Barra con sottomenu che scendono', 'Bar with drop-down submenus')], ['max-items', L('Percorso ripiegato', 'Folded path')], ['aria-current="page"', L('Link corrente nell’header', 'Current link in the header')]], L('Menu e percorsi.', 'Menus and paths.')),
        ]),
        lesson('phones', L('Sui telefoni', 'On phones'), [
            P('Con **`sheet-on-mobile`** i componenti a comparsa (`<sav-select>`, `<sav-date-picker>`, `<sav-time-picker>`, `<sav-dropdown>`, `<sav-info-tip>` e `<sav-dialog>`) su schermi larghi fino a 640px non si aprono più accanto al campo ma salgono dal basso, a tutta larghezza, come un pannello facile da toccare con il pollice.', 'With **`sheet-on-mobile`** the pop-up components (`<sav-select>`, `<sav-date-picker>`, `<sav-time-picker>`, `<sav-dropdown>`, `<sav-info-tip>` and `<sav-dialog>`) on screens up to 640px wide no longer open next to the field but rise from the bottom, full width, as a sheet that is easy to reach with your thumb.'),
            Fig(L(fig_savc_sheet('Oltre 640px: accanto al campo', 'Fino a 640px: dal basso', 'Taglia'), fig_savc_sheet('Over 640px: next to the field', 'Up to 640px: from the bottom', 'Size')),
                L('A sinistra una finestra larga: la lista S, M, L si apre subito sotto il campo Taglia. A destra un telefono: lo sfondo si scurisce e la stessa lista sale dal basso a tutta larghezza, con una maniglia in cima.', 'On the left a wide window: the S, M, L list opens right below the Size field. On the right a phone: the background dims and the same list rises from the bottom at full width, with a handle on top.'),
                L('Stesso componente, stesso valore: cambia solo dove si apre.', 'Same component, same value: only where it opens changes.')),
            P('I campi di scelta possono anche usare il selettore del telefono: **`native-on-mobile`** lo fa solo sugli schermi piccoli, `native` sempre. Il valore resta nello stesso formato (le date sempre ISO, come `2026-11-17`), quindi il tuo codice non cambia.', 'Choice fields can also use the phone’s own picker: **`native-on-mobile`** does it only on small screens, `native` always. The value keeps the same format (dates always ISO, like `2026-11-17`), so your code does not change.'),
            C(L('<sav-select name="taglia" label="Taglia" sheet-on-mobile>
  <option>S</option><option>M</option><option>L</option>
</sav-select>
<sav-date-picker name="data" label="Data" native-on-mobile></sav-date-picker>

<sav-grid columns="4" style="--mobile-columns: 2">…</sav-grid>

<sav-bottom-nav fixed mobile-only>
  <sav-bottom-nav-item icon="home" label="Home" href="/" current></sav-bottom-nav-item>
  <sav-bottom-nav-item icon="search" label="Cerca" href="/cerca"></sav-bottom-nav-item>
  <sav-bottom-nav-item icon="user" label="Profilo" href="/profilo"></sav-bottom-nav-item>
</sav-bottom-nav>', '<sav-select name="taglia" label="Size" sheet-on-mobile>
  <option>S</option><option>M</option><option>L</option>
</sav-select>
<sav-date-picker name="data" label="Date" native-on-mobile></sav-date-picker>

<sav-grid columns="4" style="--mobile-columns: 2">…</sav-grid>

<sav-bottom-nav fixed mobile-only>
  <sav-bottom-nav-item icon="home" label="Home" href="/" current></sav-bottom-nav-item>
  <sav-bottom-nav-item icon="search" label="Search" href="/search"></sav-bottom-nav-item>
  <sav-bottom-nav-item icon="user" label="Profile" href="/profile"></sav-bottom-nav-item>
</sav-bottom-nav>'), 'html'),
            P('Anche l’impaginazione si adatta: una `<sav-grid>` con `columns` passa a una colonna sui telefoni (`keep` tiene le colonne, `--mobile-columns` ne sceglie un altro numero); `<sav-header>` ripiega link e ricerca guardando la propria larghezza; `<sav-bottom-nav>` con `fixed` resta attaccata al fondo e con `mobile-only` sparisce sopra i 760px.', 'Layout adapts too: a `<sav-grid>` with `columns` drops to one column on phones (`keep` keeps the columns, `--mobile-columns` picks another number); `<sav-header>` folds links and search by looking at its own width; `<sav-bottom-nav>` with `fixed` sticks to the bottom and with `mobile-only` disappears above 760px.'),
            Tip('Prova sempre la pagina a 375px di larghezza (la modalità dispositivo degli strumenti per sviluppatori): i pannelli dal basso compaiono solo sotto i 640px.', 'Always try the page at 375px wide (the device mode of the developer tools): bottom sheets only appear below 640px.'),
        ], [
            Q('ph1', L('Che cosa fa `sheet-on-mobile` su un `<sav-select>`?', 'What does `sheet-on-mobile` do on a `<sav-select>`?'), [L('Fino a 640px la lista sale dal basso a tutta larghezza', 'Up to 640px the list rises from the bottom, full width'), L('Usa sempre il menu del sistema operativo', 'Always uses the operating system menu'), L('Nasconde il campo sui telefoni', 'Hides the field on phones'), L('Ingrandisce il testo del campo', 'Enlarges the field’s text')], 0, L('Per il selettore del telefono c’è `native-on-mobile`.', 'For the phone’s own picker there is `native-on-mobile`.')),
            Q('ph2', L('Come mostri una `<sav-bottom-nav>` fissata in basso solo sui telefoni?', 'How do you show a `<sav-bottom-nav>` pinned to the bottom on phones only?'), ['sticky phone', 'position="bottom" small', 'fixed mobile-only', 'responsive bottom'], 2, L('`mobile-only` funziona insieme a `fixed`.', '`mobile-only` works together with `fixed`.')),
            Q('ph3', L('Che cosa fa una `<sav-grid columns="3">` su un telefono?', 'What does a `<sav-grid columns="3">` do on a phone?'), [L('Resta sempre a tre colonne', 'Always stays at three columns'), L('Passa a una colonna, salvo `keep` o `--mobile-columns`', 'Drops to one column, unless `keep` or `--mobile-columns`'), L('Sparisce', 'Disappears'), L('Diventa un carosello', 'Becomes a carousel')], 1, L('Le colonne fisse non ci starebbero su uno schermo stretto.', 'Fixed columns would not fit on a narrow screen.')),
        ], task('html', L('Crea un `<sav-date-picker name="data">` che sui telefoni usa il calendario del sistema, un `<sav-dropdown>` che sui telefoni sale dal basso (con un pulsante nello slot `trigger` e una voce) e una `<sav-bottom-nav>` fissata in basso solo sui telefoni, con tre voci.', 'Create a `<sav-date-picker name="data">` that uses the system calendar on phones, a `<sav-dropdown>` that rises from the bottom on phones (with a button in the `trigger` slot and one item) and a `<sav-bottom-nav>` pinned to the bottom on phones only, with three items.'), '', [has('sav-date-picker[name="data"][native-on-mobile]', L('Calendario del telefono', 'Phone calendar')), has('sav-dropdown[sheet-on-mobile] > [slot="trigger"]', L('Menu che sale dal basso', 'Menu rising from the bottom')), has('sav-bottom-nav[fixed][mobile-only] > sav-bottom-nav-item + sav-bottom-nav-item + sav-bottom-nav-item', L('Barra in basso solo su telefono', 'Bottom bar on phones only'))], '', "<sav-date-picker name=\"data\" native-on-mobile></sav-date-picker>\n<sav-dropdown sheet-on-mobile>\n  <sav-button slot=\"trigger\">Azioni</sav-button>\n  <sav-menu-item value=\"a\">Modifica</sav-menu-item>\n</sav-dropdown>\n<sav-bottom-nav fixed mobile-only>\n  <sav-bottom-nav-item icon=\"home\" label=\"Home\"></sav-bottom-nav-item>\n  <sav-bottom-nav-item icon=\"search\" label=\"Cerca\"></sav-bottom-nav-item>\n  <sav-bottom-nav-item icon=\"user\" label=\"Profilo\"></sav-bottom-nav-item>\n</sav-bottom-nav>"), [
            dFlash(pFlash(), [[L('sheet-on-mobile vale anche per sav-dialog', 'sheet-on-mobile works on sav-dialog too'), true], [L('Con native-on-mobile il date picker restituisce la data nel formato del telefono', 'With native-on-mobile the date picker returns the date in the phone’s format'), false], [L('sav-header si ripiega guardando la propria larghezza', 'sav-header folds by looking at its own width'), true], [L('mobile-only nasconde la bottom nav sui telefoni', 'mobile-only hides the bottom nav on phones'), false]], L('Il valore resta ISO; mobile-only la nasconde sugli schermi larghi.', 'The value stays ISO; mobile-only hides it on wide screens.')),
            dBlanks(pFill(), '<sav-grid columns="4" style="--___: 2">…</sav-grid>' . "\n" . '<sav-select ___>…</sav-select>', ['mobile-columns', 'sheet-on-mobile'], ['phone-columns', 'mobile-sheet'], L('Due colonne sui telefoni e una lista che sale dal basso.', 'Two columns on phones and a list rising from the bottom.')),
        ]),
    ],
];
