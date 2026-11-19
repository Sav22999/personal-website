<?php
/** Animations with CSS: transitions, transforms, keyframes and how to make them feel good, then 3D, entry and exit, scroll-driven animations and view transitions. */

$page = '<div class="card">Una card</div>
<button class="btn">Inizia</button>
<div class="palla"></div>';

/** A transition between the normal state and :hover: delay, then duration, in both directions. The middle box changes slowly. $t: labels. */
if (!function_exists('fig_cssanim_transition')) {
    function fig_cssanim_transition(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.mid{transform-box:fill-box;transform-origin:center;animation:go 6s ease infinite}@keyframes go{0%,20%{fill:#FF6347;transform:scale(1)}45%,70%{fill:#FFD43B;transform:scale(1.05)}95%,100%{fill:#FF6347;transform:scale(1)}}@media (prefers-reduced-motion:reduce){.mid{animation:none}}</style>
<rect x="30" y="40" width="110" height="60" rx="12" fill="#FF6347"/>
<text x="85" y="76" text-anchor="middle" fill="#FFFFFF" font-weight="700" font-family="ui-monospace, Menlo, monospace">.btn</text>
<text x="85" y="124" text-anchor="middle" font-size="13" fill="#55527A">{$t[0]}</text>
<rect x="500" y="37" width="116" height="66" rx="12" fill="#FFD43B"/>
<text x="558" y="76" text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace">:hover</text>
<text x="558" y="124" text-anchor="middle" font-size="13" fill="#55527A">{$t[1]}</text>
<rect class="mid" x="270" y="40" width="100" height="60" rx="12" fill="#FF6347"/>
<rect x="160" y="160" width="60" height="26" rx="6" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"/>
<rect x="220" y="160" width="260" height="26" rx="6" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<text x="190" y="206" text-anchor="middle" font-size="12" fill="#55527A">{$t[2]}</text>
<text x="350" y="178" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">.3s ease</text>
<text x="350" y="206" text-anchor="middle" font-size="12" fill="#55527A">{$t[3]}</text>
<path d="M150 140H478" stroke="#7048E8" stroke-width="2"/><path d="M488 140l-10-5v10z" fill="#7048E8"/>
<path d="M488 222H160" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4"/><path d="M150 222l10-5v10z" fill="#7048E8"/>
<text x="500" y="226" font-size="13" fill="#5B37D0">{$t[4]}</text>
</svg>
SVG;
    }
}

/** translate, rotate, scale and skew on the same square, the original drawn dashed. Labels are code. */
if (!function_exists('fig_cssanim_transforms')) {
    function fig_cssanim_transforms(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="ui-monospace, Menlo, Consolas, monospace" font-size="13" fill="#1E1C52">
<style>.tf{transform-box:fill-box;transform-origin:center;animation:6s ease-in-out infinite alternate}
.a{transform:translate(28px,-18px);animation-name:a}.b{transform:rotate(25deg);animation-name:b}.c{transform:scale(1.35);animation-name:c}.d{transform:skewX(-20deg);animation-name:d}
@keyframes a{0%,25%{transform:none}75%,100%{transform:translate(28px,-18px)}}@keyframes b{0%,25%{transform:none}75%,100%{transform:rotate(25deg)}}@keyframes c{0%,25%{transform:none}75%,100%{transform:scale(1.35)}}@keyframes d{0%,25%{transform:none}75%,100%{transform:skewX(-20deg)}}
@media (prefers-reduced-motion:reduce){.tf{animation:none}}</style>
<g fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"><rect x="45" y="70" width="70" height="70" rx="8"/><rect x="205" y="70" width="70" height="70" rx="8"/><rect x="365" y="70" width="70" height="70" rx="8"/><rect x="525" y="70" width="70" height="70" rx="8"/></g>
<g fill="#FFE8D6" stroke="#E8590C" stroke-width="2" fill-opacity=".9">
<rect class="tf a" x="45" y="70" width="70" height="70" rx="8"/><rect class="tf b" x="205" y="70" width="70" height="70" rx="8"/><rect class="tf c" x="365" y="70" width="70" height="70" rx="8"/><rect class="tf d" x="525" y="70" width="70" height="70" rx="8"/></g>
<g fill="#7048E8"><circle cx="240" cy="105" r="4"/><circle cx="400" cy="105" r="4"/></g>
<g text-anchor="middle"><text x="80" y="190" font-size="12">translate(28px,-18px)</text><text x="240" y="190">rotate(25deg)</text><text x="400" y="190">scale(1.35)</text><text x="560" y="190">skewX(-20deg)</text></g>
<text x="320" y="30" text-anchor="middle" fill="#5B37D0">● transform-origin: center</text>
</svg>
SVG;
    }
}

/** A keyframed bounce: the ball at 0%, 50% and 100% on a timeline, and one ball that really bounces. Labels are code. */
if (!function_exists('fig_cssanim_keyframes')) {
    function fig_cssanim_keyframes(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="ui-monospace, Menlo, Consolas, monospace" font-size="13" fill="#1E1C52">
<style>.ball{animation:bounce 3s ease-in-out infinite}@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-90px)}}@media (prefers-reduced-motion:reduce){.ball{animation:none}}</style>
<path d="M30 190H130" stroke="#8A87B0" stroke-width="2"/>
<circle class="ball" cx="80" cy="172" r="18" fill="#E8590C"/>
<path d="M200 200H600" stroke="#8A87B0" stroke-width="2"/>
<g fill="#D9D7E8"><circle cx="220" cy="172" r="14"/><circle cx="580" cy="172" r="14"/></g><circle cx="400" cy="82" r="14" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<path d="M220 172C280 70 340 82 400 82C460 82 520 70 580 172" fill="none" stroke="#E8590C" stroke-width="2" stroke-dasharray="5 5"/>
<g fill="#7048E8"><path d="M220 192l8 8-8 8-8-8z"/><path d="M400 192l8 8-8 8-8-8z"/><path d="M580 192l8 8-8 8-8-8z"/></g>
<g text-anchor="middle"><text x="220" y="226" font-weight="700">0%</text><text x="400" y="226" font-weight="700">50%</text><text x="580" y="226" font-weight="700">100%</text>
<text x="220" y="146" fill="#55527A">translateY(0)</text><text x="400" y="52" fill="#55527A">translateY(-30px)</text><text x="580" y="146" fill="#55527A">translateY(0)</text></g>
<text x="80" y="214" text-anchor="middle">infinite</text>
</svg>
SVG;
    }
}

/** Four timing functions: dots drawn at equal moments show where each curve hurries and where it brakes; a dot runs along each track. Labels are code. */
if (!function_exists('fig_cssanim_timing')) {
    function fig_cssanim_timing(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="ui-monospace, Menlo, Consolas, monospace" font-size="13" fill="#1E1C52">
<style>.d0,.d1,.d2,.d3{animation:4s infinite}.d0{animation-name:run;animation-timing-function:linear}.d1{animation-name:run;animation-timing-function:ease-in}.d2{animation-name:run;animation-timing-function:ease-out}.d3{animation-name:run;animation-timing-function:cubic-bezier(.3,1.5,.5,1)}
@keyframes run{0%{transform:translateX(0)}70%,100%{transform:translateX(340px)}}@media (prefers-reduced-motion:reduce){.d0,.d1,.d2,.d3{animation:none}}</style>
<text x="220" y="26" fill="#55527A">0</text><text x="560" y="26" text-anchor="end" fill="#55527A">100%</text>
<text x="20" y="67" font-size="13">linear</text><path d="M220 62H560" stroke="#D9D7E8" stroke-width="2"/><path d="M560 52v20" stroke="#8A87B0" stroke-width="2"/><circle cx="254" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle cx="288" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle cx="322" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle cx="356" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle cx="390" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle cx="424" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle cx="458" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle cx="492" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle cx="526" cy="62" r="5" fill="#8A87B0" opacity=".35"/><circle class="d0" cx="220" cy="62" r="9" fill="#8A87B0"/><text x="20" y="127" font-size="13">ease-in</text><path d="M220 122H560" stroke="#D9D7E8" stroke-width="2"/><path d="M560 112v20" stroke="#8A87B0" stroke-width="2"/><circle cx="226" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle cx="241" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle cx="264" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle cx="293" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle cx="327" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle cx="366" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle cx="409" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle cx="455" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle cx="505" cy="122" r="5" fill="#2F8CF0" opacity=".35"/><circle class="d1" cx="220" cy="122" r="9" fill="#2F8CF0"/><text x="20" y="187" font-size="13">ease-out</text><path d="M220 182H560" stroke="#D9D7E8" stroke-width="2"/><path d="M560 172v20" stroke="#8A87B0" stroke-width="2"/><circle cx="275" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle cx="325" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle cx="371" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle cx="414" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle cx="453" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle cx="487" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle cx="516" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle cx="539" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle cx="554" cy="182" r="5" fill="#0B7F5C" opacity=".35"/><circle class="d2" cx="220" cy="182" r="9" fill="#0B7F5C"/><text x="20" y="240" font-size="13">cubic-bezier</text><text x="20" y="257" font-size="13">(.3,1.5,.5,1)</text><path d="M220 242H560" stroke="#D9D7E8" stroke-width="2"/><path d="M560 232v20" stroke="#8A87B0" stroke-width="2"/><circle cx="370" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle cx="478" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle cx="544" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle cx="577" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle cx="587" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle cx="584" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle cx="576" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle cx="568" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle cx="562" cy="242" r="5" fill="#E8590C" opacity=".35"/><circle class="d3" cx="220" cy="242" r="9" fill="#E8590C"/>
</svg>
SVG;
    }
}

/** The browser's work for each frame: animating width or top redoes everything, transform and opacity only the last step. $t: stage names and labels. */
if (!function_exists('fig_cssanim_pipeline')) {
    function fig_cssanim_pipeline(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"><rect x="20" y="80" width="120" height="50" rx="10"/><rect x="180" y="80" width="120" height="50" rx="10"/><rect x="340" y="80" width="120" height="50" rx="10"/></g>
<rect x="500" y="80" width="120" height="50" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g text-anchor="middle" font-weight="700"><text x="80" y="110">{$t[0]}</text><text x="240" y="110">{$t[1]}</text><text x="400" y="110">{$t[2]}</text><text x="560" y="110">{$t[3]}</text></g>
<g stroke="#8A87B0" stroke-width="2"><path d="M140 105H172M300 105H332M460 105H492"/></g>
<path d="M80 150V176H560V150" fill="none" stroke="#F03E82" stroke-width="3" stroke-dasharray="7 5"/>
<text x="320" y="200" text-anchor="middle" fill="#C2255C"><tspan font-family="ui-monospace, Menlo, monospace" font-weight="700">width, top</tspan> · {$t[4]}</text>
<path d="M80 80V40H560V72" fill="none" stroke="#0B7F5C" stroke-width="3"/><path d="M560 80l-6-10h12z" fill="#0B7F5C"/>
<text x="320" y="30" text-anchor="middle" fill="#0B7F5C"><tspan font-family="ui-monospace, Menlo, monospace" font-weight="700">transform, opacity</tspan> · {$t[5]}</text>
</svg>
SVG;
    }
}

/** The same card turned 50 degrees around the vertical axis with a short and a long perspective. $t: notes. */
if (!function_exists('fig_cssanim_perspective')) {
    function fig_cssanim_perspective(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="ui-monospace, Menlo, Consolas, monospace" font-size="14" fill="#1E1C52">
<g fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"><rect x="100" y="50" width="120" height="120" rx="6"/><rect x="420" y="50" width="120" height="120" rx="6"/></g>
<path d="M118 34L210 66V154L118 186Z" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<path d="M440 44L522 58V162L440 176Z" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<g stroke="#7048E8" stroke-width="2" stroke-dasharray="3 4"><path d="M160 24V196M480 24V196"/></g>
<g text-anchor="middle"><text x="160" y="214" font-weight="700">perspective: 200px</text><text x="480" y="214" font-weight="700">perspective: 800px</text></g>
<g font-family="system-ui, sans-serif" font-size="13" fill="#55527A" text-anchor="middle"><text x="160" y="16">{$t[0]}</text><text x="480" y="16">{$t[1]}</text></g>
<text x="320" y="114" text-anchor="middle" fill="#5B37D0">rotate: y 50deg</text>
</svg>
SVG;
    }
}

/** Entry and exit with @starting-style and allow-discrete, as two strips of three moments; a notice fades in and out. $t: labels. */
if (!function_exists('fig_cssanim_entry')) {
    function fig_cssanim_entry(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<g font-weight="700" font-size="14"><text x="20" y="66">{$t[0]}</text><text x="20" y="186">{$t[1]}</text></g>
<g fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"><rect x="120" y="40" width="130" height="50" rx="10"/><rect x="480" y="160" width="130" height="50" rx="10"/></g>
<rect x="300" y="60" width="130" height="50" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2" opacity=".35"/>
<rect x="480" y="40" width="130" height="50" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<rect x="120" y="160" width="130" height="50" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<rect x="300" y="160" width="130" height="50" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2" opacity=".35"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="12" text-anchor="middle">
<text x="185" y="70">display: none</text><text x="365" y="90">@starting-style</text><text x="545" y="70">opacity: 1</text>
<text x="185" y="190">opacity: 1</text><text x="365" y="190">opacity → 0</text><text x="545" y="190">display: none</text></g>
<g stroke="#7048E8" stroke-width="2"><path d="M256 75H292M436 75H472M256 185H292M436 185H472"/></g>
<g fill="#7048E8"><path d="M298 75l-8-4v8zM478 75l-8-4v8zM298 185l-8-4v8zM478 185l-8-4v8z"/></g>
<g fill="#55527A" text-anchor="middle"><text x="185" y="112">{$t[2]}</text><text x="365" y="132">{$t[3]}</text><text x="545" y="112">{$t[4]}</text><text x="545" y="232">{$t[5]}</text></g>
<text x="320" y="262" text-anchor="middle" fill="#5B37D0">{$t[6]}</text>
</svg>
SVG;
    }
}

/** A scroll-driven progress bar: the bar at the top grows as the scrollbar thumb goes down. $t: labels. */
if (!function_exists('fig_cssanim_scroll')) {
    function fig_cssanim_scroll(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<style>.thumb{animation:thumb 6s ease-in-out infinite alternate}.prog{transform-box:fill-box;transform-origin:left;transform:scaleX(.45);animation:prog 6s ease-in-out infinite alternate}.page{animation:page 6s ease-in-out infinite alternate}
@keyframes thumb{from{transform:translateY(0)}to{transform:translateY(120px)}}@keyframes prog{from{transform:scaleX(0)}to{transform:scaleX(1)}}@keyframes page{from{transform:translateY(0)}to{transform:translateY(-140px)}}
@media (prefers-reduced-motion:reduce){.thumb,.prog,.page{animation:none}.thumb{transform:translateY(54px)}.page{transform:translateY(-63px)}}</style>
<defs><clipPath id="win"><rect x="40" y="30" width="300" height="200" rx="10"/></clipPath></defs>
<rect x="40" y="30" width="300" height="200" rx="10" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<g clip-path="url(#win)"><g class="page">
<rect x="60" y="52" width="160" height="14" rx="7" fill="#1E1C52"/>
<g fill="#D9D7E8"><rect x="60" y="80" width="240" height="9" rx="4.5"/><rect x="60" y="96" width="220" height="9" rx="4.5"/><rect x="60" y="112" width="250" height="9" rx="4.5"/><rect x="60" y="128" width="200" height="9" rx="4.5"/>
<rect x="60" y="150" width="250" height="60" rx="8"/><rect x="60" y="224" width="240" height="9" rx="4.5"/><rect x="60" y="240" width="210" height="9" rx="4.5"/><rect x="60" y="256" width="250" height="9" rx="4.5"/><rect x="60" y="272" width="190" height="9" rx="4.5"/>
<rect x="60" y="294" width="250" height="60" rx="8"/><rect x="60" y="368" width="230" height="9" rx="4.5"/></g></g>
<rect class="prog" x="40" y="30" width="300" height="6" fill="#E8590C"/></g>
<rect x="326" y="40" width="8" height="180" rx="4" fill="#F1F0F8"/><rect class="thumb" x="326" y="40" width="8" height="60" rx="4" fill="#7048E8"/>
<text x="370" y="60" font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#B84508">.progresso</text>
<text x="370" y="80" fill="#55527A">{$t[0]}</text>
<text x="370" y="130" font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#5B37D0">animation-timeline:</text>
<text x="370" y="150" font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#5B37D0">scroll()</text>
<text x="370" y="174" fill="#55527A">{$t[1]}</text>
<text x="370" y="192" fill="#55527A">{$t[2]}</text>
</svg>
SVG;
    }
}

/** A view transition: the photo with the same view-transition-name flies from the list to the detail page. $t: labels. */
if (!function_exists('fig_cssanim_view')) {
    function fig_cssanim_view(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<style>.fly{transform-origin:60px 90px;animation:fly 6s ease-in-out infinite}@keyframes fly{0%,20%{transform:none}55%,100%{transform:translate(320px,-30px) scale(2.75,2.5)}}
.old{animation:old 6s ease-in-out infinite}@keyframes old{0%,20%{opacity:1}55%,100%{opacity:.25}}@media (prefers-reduced-motion:reduce){.fly,.old{animation:none}}</style>
<rect x="30" y="40" width="250" height="170" rx="12" fill="#F7F6FC" stroke="#8A87B0" stroke-width="2"/>
<rect x="350" y="40" width="270" height="190" rx="12" fill="#F7F6FC" stroke="#8A87B0" stroke-width="2"/>
<g class="old" fill="#D9D7E8"><rect x="160" y="90" width="80" height="60" rx="6"/><rect x="60" y="164" width="80" height="30" rx="6"/><rect x="160" y="164" width="80" height="30" rx="6"/></g>
<g fill="#D9D7E8"><rect x="380" y="200" width="200" height="9" rx="4.5"/><rect x="380" y="214" width="150" height="9" rx="4.5"/></g>
<rect x="380" y="60" width="220" height="150" rx="6" fill="none" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4"/>
<rect class="fly" x="60" y="90" width="80" height="60" rx="6" fill="#FFE8D6" stroke="#E8590C" stroke-width="2" vector-effect="non-scaling-stroke"/>
<g font-weight="700" text-anchor="middle"><text x="155" y="30">{$t[0]}</text><text x="485" y="30">{$t[1]}</text></g>
<text x="100" y="80" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#B84508">foto</text>
<text x="320" y="244" text-anchor="middle" fill="#55527A">{$t[2]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'css-animations', 'family' => 'css', 'after' => 'css-intermediate',
    'title' => L('Animazioni con CSS', 'CSS Animations'), 'short' => L('Animazioni', 'Animations'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Transizioni, trasformazioni e 3D, keyframes, tempi, entrate e uscite, animazioni legate allo scorrimento, transizioni di vista e animazioni rispettose.', 'Transitions, transforms and 3D, keyframes, timing, entry and exit, scroll-driven animations, view transitions and respectful animation.'),
    'lessons' => [
        lesson('transitions', L('Transizioni', 'Transitions'), [
            P('Una **transizione** rende graduale un cambiamento. Indichi la proprietà, la durata e, se vuoi, la curva e il ritardo.', 'A **transition** makes a change gradual. You give the property, the duration and, if you want, the curve and the delay.'),
            C('.btn {
  background: tomato;
  transition: background-color .3s ease, transform .2s;
}
.btn:hover {
  background: gold;
  transform: scale(1.05);
}', 'css', L('<button class="btn" style="border:0;padding:12px 20px;border-radius:10px;color:white;font-weight:700">Passaci sopra</button>', '<button class="btn" style="border:0;padding:12px 20px;border-radius:10px;color:white;font-weight:700">Hover over me</button>')),
            P('Si scrive sullo stato normale, non su `:hover`: così vale sia all’andata che al ritorno.', 'It goes on the normal state, not on `:hover`: so it works both ways.'),
            Fig(L(fig_cssanim_transition(['stato normale', 'stato :hover', 'ritardo', 'durata', 'e ritorno']), fig_cssanim_transition(['normal state', ':hover state', 'delay', 'duration', 'and back'])),
                L('Un bottone rosso pomodoro a sinistra e lo stesso bottone giallo oro e un po’ più grande a destra, nello stato `:hover`. In mezzo, un bottone che passa lentamente dall’uno all’altro. Sotto, una barra del tempo: un breve ritardo tratteggiato, poi la durata di .3s; una freccia va verso `:hover` e una tratteggiata torna indietro.', 'A tomato-red button on the left and the same button gold and a little bigger on the right, in the `:hover` state. In the middle, a button slowly changing from one to the other. Below, a time bar: a short dashed delay, then the .3s duration; one arrow goes towards `:hover` and a dashed one comes back.'),
                L('Il browser calcola tutti i passaggi intermedi: tu dai solo l’inizio e la fine.', 'The browser works out every step in between: you only give the start and the end.')),
            P('Non tutto si può animare: colori, misure, posizioni, opacità e trasformazioni sì, perché hanno valori intermedi; `display` o `font-family` no, cambiano di colpo. Evita `transition: all`: anima anche proprietà che non volevi e costa di più. Le durate giuste sono brevi: 150–300 ms per un bottone sembrano reattive, oltre il mezzo secondo l’interfaccia sembra lenta.', 'Not everything can be animated: colours, sizes, positions, opacity and transforms can, because they have values in between; `display` or `font-family` cannot, they change at once. Avoid `transition: all`: it animates properties you did not mean to and costs more. The right durations are short: 150–300 ms feels responsive for a button, past half a second the interface feels slow.'),
        ], [
            Q('t1', L('Dove si scrive `transition`?', 'Where does `transition` go?'), [L('Sullo stato normale', 'On the normal state'), L('Solo su `:hover`', 'Only on `:hover`'), L('Nell’HTML', 'In the HTML'), L('In `@media`', 'In `@media`')], 0, L('Così l’effetto vale nei due sensi.', 'So the effect works both ways.')),
            Q('t2', L('Quanto dura `.3s`?', 'How long is `.3s`?'), [L('300 millisecondi', '300 milliseconds'), L('3 secondi', '3 seconds'), L('30 secondi', '30 seconds'), L('3 minuti', '3 minutes')], 0, L('Tra 150 e 300ms un’interfaccia sembra rapida.', 'Between 150 and 300ms an interface feels quick.')),
            Q('t3', L('Si può animare `display`?', 'Can you animate `display`?'), [L('Di solito no', 'Usually not'), L('Sempre', 'Always'), L('Solo in rosso', 'Only in red'), L('Solo nei link', 'Only in links')], 0, L('Si anima ciò che ha valori intermedi, come `opacity`. `display` non ne ha: al massimo, con `transition-behavior: allow-discrete`, si sceglie quando scatta.', 'You animate what has in-between values, like `opacity`. `display` has none: at most, with `transition-behavior: allow-discrete`, you choose when it flips.')),
        ], task('css', L('Dai a `.btn` una `transition` su `transform` di `.2s`, e su `:hover` un `transform: scale(1.1)`.', 'Give `.btn` a `transition` on `transform` of `.2s`, and on `:hover` a `transform: scale(1.1)`.'), '', [re('\.btn\s*\{[^}]*transition\s*:\s*transform\s+\.?0?\.2s', L('La transizione è giusta', 'The transition is right')), re('\.btn:hover\s*\{[^}]*scale\(1\.1\)', L('Si ingrandisce al passaggio', 'It grows on hover'))], $page, ".btn {\n  transition: transform .2s;\n}\n.btn:hover {\n  transform: scale(1.1);\n}"), [
            dBlanks(pFill(), ".card {\n  ___: transform .2s ___;\n}\n.card:___ {\n  transform: scale(1.1);\n}", ['transition', 'ease', 'hover'], ['animation', 'slow', 'active-on'], L('Proprietà, durata e curva nello stato di partenza.', 'Property, duration and curve on the starting state.')),
            dFlash(pFlash(), [[L('`transition` funziona nei due sensi se sta sullo stato normale', '`transition` works both ways on the normal state'), true], [L('`opacity` si può animare', '`opacity` can be animated'), true], [L('`.2s` sono due secondi', '`.2s` is two seconds'), false], [L('Si possono animare più proprietà insieme', 'Several properties can be animated together'), true], [L('La transizione serve solo ai colori', 'Transitions are only for colours'), false]], L('Brevi e mirate: le transizioni migliori quasi non si notano.', 'Short and focused: the best transitions are barely noticed.')),
        ]),
        lesson('transforms', L('Trasformazioni', 'Transforms'), [
            P('`transform` sposta, ruota, ingrandisce e inclina un elemento senza disturbare gli altri. È anche la proprietà più veloce da animare.', '`transform` moves, rotates, scales and skews an element without disturbing the others. It is also the fastest property to animate.'),
            C('.card {
  transform: translateX(20px) rotate(5deg) scale(1.1);
  transform-origin: top left;
}', 'css', L('<div style="padding:24px 8px 40px"><div class="card" style="width:140px;background:#ffe8d6;border:2px solid #e8590c;padding:12px">Una card spostata, ruotata e ingrandita</div></div>', '<div style="padding:24px 8px 40px"><div class="card" style="width:140px;background:#ffe8d6;border:2px solid #e8590c;padding:12px">A card moved, rotated and enlarged</div></div>')),
            P('L’ordine conta: ogni funzione agisce sul risultato della precedente. `transform-origin` sceglie il punto attorno a cui avviene tutto.', 'Order matters: each function acts on the result of the previous one. `transform-origin` picks the point everything happens around.'),
            Fig(fig_cssanim_transforms(),
                L('Quattro quadrati, ognuno con il suo originale tratteggiato: `translate` lo sposta a destra e in alto, `rotate` lo ruota di 25 gradi, `scale` lo ingrandisce, `skewX` lo inclina. Un punto viola al centro segna `transform-origin`.', 'Four squares, each with its original drawn dashed: `translate` moves it right and up, `rotate` turns it 25 degrees, `scale` enlarges it, `skewX` slants it. A purple dot in the centre marks `transform-origin`.'),
                L('Il quadrato tratteggiato è il posto che l’elemento continua a occupare: gli altri non si spostano.', 'The dashed square is the room the element keeps taking up: the others do not move.')),
            P('Il segreto di `transform` è che lavora dopo l’impaginazione: l’elemento viene disegnato altrove, ma per la pagina resta dov’era, e niente intorno si sposta. Per questo è il modo giusto di far «sollevare» una card o scorrere un pannello. Le unità: `translate` accetta px o percentuali (della scatola stessa: `translateX(-100%)` la sposta di tutta la sua larghezza), `rotate` gradi (`deg`) o giri (`turn`), `scale` un numero, dove 1 è la misura normale.', 'The secret of `transform` is that it works after layout: the element is drawn somewhere else, but for the page it stays where it was, and nothing around it moves. That is why it is the right way to make a card “lift” or a panel slide. The units: `translate` takes px or percentages (of the box itself: `translateX(-100%)` moves it by its whole width), `rotate` degrees (`deg`) or turns (`turn`), `scale` a number, where 1 is the normal size.'),
        ], [
            Q('r1', L('Quale funzione sposta?', 'Which function moves?'), ['translate()', 'move()', 'shift()', 'go()'], 0, L('Esistono `translateX` e `translateY`.', 'There are `translateX` and `translateY`.')),
            Q('r2', L('`transform` sposta gli altri elementi?', 'Does `transform` move other elements?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo i fratelli', 'Only siblings'), L('Solo il genitore', 'Only the parent')], 0, L('Lo spazio occupato resta quello di prima.', 'The space taken stays as before.')),
            Q('r3', L('Che cosa cambia `transform-origin`?', 'What does `transform-origin` change?'), [L('Il punto di riferimento', 'The reference point'), L('La durata', 'The duration'), L('Il colore', 'The colour'), L('La curva', 'The curve')], 0, L('Una rotazione da un angolo sembra una porta.', 'A rotation from a corner looks like a door.')),
        ], task('css', L('Ruota `.card` di `45deg` e spostala di `20px` a destra.', 'Rotate `.card` by `45deg` and move it `20px` to the right.'), '.card {

}', [re('rotate\(45deg\)', L('Ruota di 45 gradi', 'It rotates by 45 degrees')), re('translateX\(20px\)|translate\(20px', L('Si sposta di 20px', 'It moves by 20px'))], $page, ".card {\n  transform: translateX(20px) rotate(45deg);\n}"), [
            dMatch(pMatch(), [['translate()', L('Sposta', 'Moves')], ['rotate()', L('Ruota', 'Rotates')], ['scale()', L('Ingrandisce', 'Scales')], ['skew()', L('Inclina', 'Skews')]], L('Quattro funzioni, infinite combinazioni.', 'Four functions, endless combinations.')),
            dBug(pBug(), ['.card {', '  transform: rotate(45);', '}'], 1, '  transform: rotate(45deg);', L('Una rotazione vuole l’unità: `deg`.', 'A rotation needs its unit: `deg`.')),
        ]),
        lesson('keyframes', L('Keyframes', 'Keyframes'), [
            P('Le transizioni vanno da A a B. Con `@keyframes` disegni una storia a più tappe, che può ripetersi da sola, senza bisogno di un `:hover`.', 'Transitions go from A to B. With `@keyframes` you draw a story of several stages, which can repeat by itself, with no `:hover` needed.'),
            C('@keyframes rimbalzo {
  0%   { transform: translateY(0); }
  50%  { transform: translateY(-30px); }
  100% { transform: translateY(0); }
}
.palla {
  animation: rimbalzo 1s ease-in-out infinite;
}', 'css', '<div style="padding-top:40px"><div class="palla" style="width:40px;height:40px;border-radius:50%;background:#e8590c"></div></div>'),
            P('`animation` riassume nome, durata, curva e ripetizioni. `infinite` non si ferma mai, `alternate` torna indietro ogni due giri.', '`animation` sums up name, duration, curve and repeats. `infinite` never stops, `alternate` goes back every second round.'),
            Fig(fig_cssanim_keyframes(),
                L('A sinistra una palla che rimbalza davvero. A destra la sua storia su una linea del tempo: a 0% sta a terra con `translateY(0)`, a 50% è in alto con `translateY(-30px)`, a 100% torna a terra; una curva tratteggiata collega le tre tappe.', 'On the left a ball that really bounces. On the right its story on a timeline: at 0% it is on the ground with `translateY(0)`, at 50% it is up with `translateY(-30px)`, at 100% it is back on the ground; a dashed curve links the three stages.'),
                L('Tu scrivi le tappe, il browser disegna tutto quello che sta in mezzo.', 'You write the stages, the browser draws everything in between.')),
            P('Le percentuali sono momenti della durata: con `1s`, il 50% arriva dopo mezzo secondo. `from` e `to` sono sinonimi di 0% e 100%, e se una tappa manca il browser usa lo stato normale dell’elemento. Più tappe con lo stesso valore si scrivono insieme, come `0%, 100% { … }`. L’errore classico è sbagliare il nome: se `animation` cita un nome che non esiste, non succede nulla e nessuno ti avvisa.', 'The percentages are moments of the duration: with `1s`, 50% arrives after half a second. `from` and `to` are synonyms of 0% and 100%, and if a stage is missing the browser uses the element’s normal state. Several stages with the same value are written together, as `0%, 100% { … }`. The classic mistake is a wrong name: if `animation` names something that does not exist, nothing happens and nobody warns you.'),
        ], [
            Q('k1', L('Che cosa definisce `@keyframes`?', 'What does `@keyframes` define?'), [L('Le tappe di un’animazione', 'The stages of an animation'), L('Un font', 'A font'), L('Una variabile', 'A variable'), L('Una media query', 'A media query')], 0, L('Si usa poi con `animation`.', 'It is then used with `animation`.')),
            Q('k2', L('Come si ripete per sempre?', 'How does it repeat for ever?'), ['infinite', 'loop', 'always', 'repeat'], 0, L('Oppure un numero di giri.', 'Or a number of rounds.')),
            Q('k3', L('`from` e `to` equivalgono a…', '`from` and `to` are the same as…'), ['0% e 100%', '1 e 2', 'start e end', 'A e B'], 0, L('Per le tappe intermedie servono le percentuali.', 'In-between stages need percentages.')),
        ], task('css', L('Crea `@keyframes pulsa` e applicalo a `.palla` con `animation: pulsa 1s infinite`.', 'Create `@keyframes pulsa` and apply it to `.palla` with `animation: pulsa 1s infinite`.'), '', [re('@keyframes\s+pulsa', L('I keyframes esistono', 'The keyframes exist')), re('\.palla\s*\{[^}]*animation\s*:\s*pulsa\s+1s\s+infinite', L('La palla pulsa', 'The ball pulses'))], $page, "@keyframes pulsa {\n  from { opacity: 1; }\n  to { opacity: .4; }\n}\n.palla {\n  animation: pulsa 1s infinite;\n}"), [
            dOrder(pSort(), ['@keyframes sali {', '  from { opacity: 0; }', '  to { opacity: 1; }', '}', '.card { animation: sali .4s; }'], L('Prima le tappe, poi chi le usa.', 'First the stages, then whoever uses them.')),
            dBug(pBug(), ['@keyframes gira {', '  to { transform: rotate(360deg); }', '}', '.ruota { animation: giro 2s infinite; }'], 3, '.ruota { animation: gira 2s infinite; }', L('Il nome in `animation` deve coincidere con quello dei `@keyframes`.', 'The name in `animation` must match the `@keyframes` one.')),
        ]),
        lesson('timing', L('Tempi e curve', 'Timing and curves'), [
            P('La stessa animazione cambia carattere con la curva: `linear` è meccanica, `ease-out` frena alla fine, `ease-in` parte piano. Con `cubic-bezier()` crei la tua, anche con un rimbalzo.', 'The same animation changes character with its curve: `linear` is mechanical, `ease-out` brakes at the end, `ease-in` starts slowly. With `cubic-bezier()` you make your own, even with a bounce.'),
            Fig(fig_cssanim_timing(),
                L('Quattro binari, uno per curva: linear, ease-in, ease-out e cubic-bezier(.3, 1.5, .5, 1). Su ognuno, dei puntini segnano dove si trova l’elemento in dieci momenti uguali: con linear sono equidistanti, con ease-in fitti all’inizio, con ease-out fitti alla fine, con la curva su misura superano il traguardo e tornano indietro. Un pallino percorre ogni binario.', 'Four tracks, one per curve: linear, ease-in, ease-out and cubic-bezier(.3, 1.5, .5, 1). On each, dots mark where the element is at ten equal moments: with linear they are evenly spaced, with ease-in bunched at the start, with ease-out bunched at the end, with the custom curve they overshoot the finish and come back. A dot runs along each track.'),
                L('Puntini fitti: l’elemento va piano; puntini larghi: va veloce.', 'Close dots: the element is slow; wide dots: it is fast.')),
            C('.card {
  animation-name: entra;
  animation-duration: .4s;
  animation-delay: .1s;
  animation-timing-function: cubic-bezier(.3, 1.5, .5, 1);
  animation-fill-mode: backwards;
}', 'css', L('<style>@keyframes entra { from { opacity: 0; transform: translateY(30px); } }</style>
<div class="card" style="background:#ffe8d6;border:2px solid #e8590c;padding:16px;max-width:240px">Entro con un piccolo rimbalzo</div>', '<style>@keyframes entra { from { opacity: 0; transform: translateY(30px); } }</style>
<div class="card" style="background:#ffe8d6;border:2px solid #e8590c;padding:16px;max-width:240px">I come in with a small bounce</div>')),
            P('`animation-fill-mode` decide lo stato prima e dopo: `forwards` mantiene l’ultima tappa, `backwards` applica subito la prima durante il ritardo.', '`animation-fill-mode` decides the state before and after: `forwards` keeps the last stage, `backwards` applies the first at once during the delay.'),
        ], [
            Q('c1', L('Quale curva frena alla fine?', 'Which curve brakes at the end?'), ['ease-out', 'ease-in', 'linear', 'step'], 0, L('È la più naturale per ciò che entra in scena.', 'It is the most natural for things entering the scene.')),
            Q('c2', L('Che cosa fa `forwards`?', 'What does `forwards` do?'), [L('Tiene l’ultima tappa', 'Keeps the last stage'), L('Salta avanti', 'Skips ahead'), L('Va più veloce', 'Goes faster'), L('Ripete', 'Repeats')], 0, L('Senza, l’elemento torna com’era.', 'Without it, the element goes back to how it was.')),
            Q('c3', L('Perché scaglionare i ritardi in una lista?', 'Why stagger delays in a list?'), [L('Per un effetto a cascata', 'For a cascade effect'), L('Per pesare meno', 'To weigh less'), L('Per la SEO', 'For SEO'), L('Per il colore', 'For colour')], 0, L('L’occhio segue meglio una cosa alla volta.', 'The eye follows one thing at a time better.')),
        ], task('css', L('Dai a `.card` un `animation-delay` di `.2s` e un `animation-fill-mode` di `forwards`.', 'Give `.card` an `animation-delay` of `.2s` and an `animation-fill-mode` of `forwards`.'), '.card {

}', [re('animation-delay\s*:\s*0?\.2s', L('Il ritardo è giusto', 'The delay is right')), re('animation-fill-mode\s*:\s*forwards', L('Mantiene l’ultima tappa', 'It keeps the last stage'))], $page, ".card {\n  animation-delay: .2s;\n  animation-fill-mode: forwards;\n}"), [
            dMatch(pMatch(), [['linear', L('Velocità costante', 'Constant speed')], ['ease-in', L('Parte piano', 'Starts slowly')], ['ease-out', L('Frena alla fine', 'Brakes at the end')], ['cubic-bezier()', L('Curva su misura', 'Custom curve')], ['steps()', L('A scatti', 'In jumps')]], L('La curva è la personalità del movimento.', 'The curve is the personality of the movement.')),
            dFlash(pFlash(), [[L('`animation-delay` aspetta prima di partire', '`animation-delay` waits before starting'), true], [L('`ease-out` accelera alla fine', '`ease-out` speeds up at the end'), false], [L('`cubic-bezier` può creare un rimbalzo', '`cubic-bezier` can create a bounce'), true], [L('`linear` è perfetta per ogni cosa', '`linear` is perfect for everything'), false]], L('Il movimento naturale raramente è lineare.', 'Natural movement is rarely linear.')),
        ]),
        lesson('respect', L('Prestazioni e rispetto', 'Performance and respect'), [
            P('Anima `transform` e `opacity`: il browser li gestisce senza ricalcolare la pagina. Animare `width` o `top` costa molto di più e può scattare.', 'Animate `transform` and `opacity`: the browser handles them without recalculating the page. Animating `width` or `top` costs far more and can stutter.'),
            Fig(L(fig_cssanim_pipeline(['Stile', 'Impaginazione', 'Disegno', 'Composizione', 'rifà tutto a ogni fotogramma', 'salta dritto all’ultimo passo']), fig_cssanim_pipeline(['Style', 'Layout', 'Paint', 'Composite', 'redoes everything every frame', 'jumps straight to the last step'])),
                L('I quattro passi del browser in fila: stile, impaginazione, disegno, composizione. Una linea rosa tratteggiata, per `width` e `top`, li attraversa tutti a ogni fotogramma; una linea verde, per `transform` e `opacity`, salta dallo stile direttamente alla composizione.', 'The browser’s four steps in a row: style, layout, paint, composite. A dashed pink line, for `width` and `top`, goes through all of them every frame; a green line, for `transform` and `opacity`, jumps from style straight to composite.'),
                L('Sessanta fotogrammi al secondo: ogni passo risparmiato si vede.', 'Sixty frames a second: every step saved shows.')),
            C('@media (prefers-reduced-motion: reduce) {
  * {
    animation: none !important;
    transition: none !important;
  }
}', 'css'),
            P('Alcune persone stanno male con il movimento: `prefers-reduced-motion` rispetta la loro impostazione di sistema. Un’animazione deve spiegare, non distrarre.', 'Some people feel ill with movement: `prefers-reduced-motion` respects their system setting. An animation must explain, not distract.'),
            Tip('La regola qui sopra spegne tutto, ma non è l’unica strada: spesso basta sostituire il movimento con una dissolvenza breve, che non dà fastidio. Puoi anche ribaltare la logica e scrivere le animazioni solo dentro `@media (prefers-reduced-motion: no-preference)`: chi non vuole movimento non le riceve proprio.', 'The rule above switches everything off, but it is not the only way: often it is enough to replace the movement with a short fade, which bothers nobody. You can also flip the logic and write animations only inside `@media (prefers-reduced-motion: no-preference)`: people who do not want motion never get them at all.'),
        ], [
            Q('p1', L('Quali proprietà conviene animare?', 'Which properties are best to animate?'), [L('`transform` e `opacity`', '`transform` and `opacity`'), L('`width` e `height`', '`width` and `height`'), L('`margin` e `top`', '`margin` and `top`'), L('`font-size`', '`font-size`')], 0, L('Non costringono a ricalcolare la pagina.', 'They do not force the page to be recalculated.')),
            Q('p2', L('Che cosa rispetta `prefers-reduced-motion`?', 'What does `prefers-reduced-motion` respect?'), [L('Chi chiede meno movimento', 'People asking for less movement'), L('Chi ha uno schermo piccolo', 'People with a small screen'), L('Chi usa Safari', 'Safari users'), L('Chi è offline', 'Offline users')], 0, L('È una preferenza del sistema operativo.', 'It is an operating system preference.')),
            Q('p3', L('A che cosa deve servire un’animazione?', 'What should an animation do?'), [L('A spiegare', 'Explain'), L('A distrarre', 'Distract'), L('A rallentare', 'Slow things down'), L('A stupire sempre', 'Always amaze')], 0, L('Se non aiuta a capire, togliila.', 'If it does not help understanding, remove it.')),
        ], task('css', L('Scrivi la media query `prefers-reduced-motion: reduce` che mette `animation: none` su `.palla`.', 'Write the `prefers-reduced-motion: reduce` media query that sets `animation: none` on `.palla`.'), '', [re('@media\s*\(prefers-reduced-motion:\s*reduce\)', L('La media query è giusta', 'The media query is right')), re('\.palla\s*\{[^}]*animation\s*:\s*none', L('L’animazione si ferma', 'The animation stops'))], $page, "@media (prefers-reduced-motion: reduce) {\n  .palla {\n    animation: none;\n  }\n}"), [
            dBlanks(pFill(), "@media (___: reduce) {\n  .palla { ___: none; }\n}", ['prefers-reduced-motion', 'animation'], ['max-width', 'display', 'motion'], L('Una riga di rispetto per chi soffre il movimento.', 'One line of respect for those who suffer from movement.')),
            dFlash(pFlash(), [[L('Animare `transform` è più leggero di `left`', 'Animating `transform` is lighter than `left`'), true], [L('`opacity` è economica da animare', '`opacity` is cheap to animate'), true], [L('Più animazioni ci sono, meglio è', 'The more animations, the better'), false], [L('Il movimento può far stare male', 'Movement can make people feel ill'), true], [L('Animare `height` è gratis', 'Animating `height` is free'), false]], L('Bella, leggera e rispettosa: così si anima.', 'Beautiful, light and respectful: that is how to animate.')),
        ]),
        lesson('individual-3d', L('Trasformazioni separate e 3D', 'Separate transforms and 3D'), [
            P('Oltre a `transform` esistono le proprietà separate `translate`, `rotate` e `scale`: ognuna si cambia e si anima da sola, senza riscrivere le altre. Il browser le applica sempre in quest’ordine: spostamento, rotazione, scala.', 'Besides `transform` there are the separate properties `translate`, `rotate` and `scale`: each changes and animates on its own, without rewriting the others. The browser always applies them in this order: translation, rotation, scale.'),
            C('.card {
  rotate: 5deg;
  transition: scale .2s, translate .2s;
}
.card:hover { scale: 1.05; translate: 0 -4px; } /* la rotazione resta */

.scena { perspective: 600px; }
.moneta { transition: rotate .6s; }
.moneta:hover { rotate: y 180deg; }
.moneta .retro { backface-visibility: hidden; }', 'css', L('<div class="scena" style="padding:16px"><div class="card" style="width:160px;background:#ffe8d6;border:2px solid #e8590c;padding:12px">Card un po’ ruotata: passaci sopra</div>
<p><span class="moneta" style="display:inline-block;padding:12px 16px;border-radius:50%;background:#ffd43b;font-weight:700">€1</span> la moneta gira</p></div>', '<div class="scena" style="padding:16px"><div class="card" style="width:160px;background:#ffe8d6;border:2px solid #e8590c;padding:12px">A slightly rotated card: hover over it</div>
<p><span class="moneta" style="display:inline-block;padding:12px 16px;border-radius:50%;background:#ffd43b;font-weight:700">€1</span> the coin turns</p></div>')),
            P('`perspective` sul genitore dà profondità alla scena: più il valore è piccolo, più l’effetto è forte. `rotate: y 180deg` gira l’elemento attorno all’asse verticale, come una moneta; `backface-visibility: hidden` nasconde il lato che finisce sul retro.', '`perspective` on the parent gives the scene depth: the smaller the value, the stronger the effect. `rotate: y 180deg` turns the element around the vertical axis, like a coin; `backface-visibility: hidden` hides the side that ends up at the back.'),
            Fig(L(fig_cssanim_perspective(['vicino: effetto forte', 'lontano: effetto leggero']), fig_cssanim_perspective(['close: strong effect', 'far: gentle effect'])),
                L('La stessa card ruotata di 50 gradi attorno all’asse verticale, due volte. Con `perspective: 200px` diventa un trapezio molto deformato, un lato grande e uno piccolo; con `perspective: 800px` la deformazione è leggera. Il contorno tratteggiato mostra la card dritta.', 'The same card turned 50 degrees around the vertical axis, twice. With `perspective: 200px` it becomes a strongly distorted trapezium, one side big and one small; with `perspective: 800px` the distortion is gentle. The dashed outline shows the card straight on.'),
                L('`perspective` è la distanza dell’occhio dalla scena.', '`perspective` is the distance of the eye from the scene.')),
        ], [
            Q('i1', L('Che vantaggio hanno `translate`, `rotate` e `scale` separate?', 'What is the advantage of separate `translate`, `rotate` and `scale`?'), [L('Sono più veloci di `opacity`', 'They are faster than `opacity`'), L('Funzionano senza unità di misura', 'They work without units'), L('Ognuna si cambia senza riscrivere le altre', 'Each one changes without rewriting the others'), L('Spostano anche gli elementi vicini', 'They also move nearby elements')], 2, L('Con `transform` dovresti ripetere tutta la lista di funzioni.', 'With `transform` you would have to repeat the whole list of functions.')),
            Q('i2', L('Che cosa succede se riduci `perspective` da 1000px a 300px?', 'What happens if you reduce `perspective` from 1000px to 300px?'), [L('L’effetto 3D diventa più forte', 'The 3D effect gets stronger'), L('L’effetto 3D sparisce', 'The 3D effect disappears'), L('L’elemento si rimpicciolisce', 'The element shrinks'), L('Niente, conta solo sui telefoni', 'Nothing, it only matters on phones')], 0, L('È come guardare la scena da più vicino.', 'It is like looking at the scene from closer up.')),
            Q('i3', L('Che cosa fa `rotate: y 180deg`?', 'What does `rotate: y 180deg` do?'), [L('Ruota nel piano dello schermo', 'It rotates in the plane of the screen'), L('Sposta l’elemento in verticale', 'It moves the element vertically'), L('Capovolge solo il testo', 'It flips only the text'), L('Gira l’elemento attorno all’asse verticale', 'It turns the element around the vertical axis')], 3, L('Come una moneta che mostra il retro.', 'Like a coin showing its back.')),
        ], task('css', L('Dai a `.card` `rotate: 10deg` e una `transition` su `scale`; su `.card:hover` imposta `scale: 1.1`.', 'Give `.card` `rotate: 10deg` and a `transition` on `scale`; on `.card:hover` set `scale: 1.1`.'), '', [css('.card', 'rotate', '10deg', L('La card è ruotata', 'The card is rotated')), re('\.card\s*\{[^}]*transition\s*:[^;}]*\bscale\b', L('La scala ha una transizione', 'The scale has a transition')), re('\.card:hover\s*\{[^}]*\bscale\s*:\s*1\.1', L('Cresce al passaggio', 'It grows on hover'))], $page, ".card {\n  rotate: 10deg;\n  transition: scale .2s;\n}\n.card:hover {\n  scale: 1.1;\n}"), [
            dMatch(pMatch(), [['translate', L('Sposta', 'Moves')], ['rotate', L('Ruota', 'Rotates')], ['scale', L('Ingrandisce', 'Scales')], ['perspective', L('Profondità della scena', 'Depth of the scene')], ['backface-visibility', L('Mostra o nasconde il retro', 'Shows or hides the back')]], L('Proprietà separate, animazioni più semplici.', 'Separate properties, simpler animations.')),
            dBug(pBug(), ['.scena {', '  perspective: 600px;', '}', '.moneta:hover {', '  rotate: y 180;', '}'], 4, '  rotate: y 180deg;', L('Un angolo vuole la sua unità: `deg`.', 'An angle needs its unit: `deg`.')),
        ]),
        lesson('entry-exit', L('Animare entrate e uscite', 'Animating entry and exit'), [
            P('Un elemento che compare (passa da `display: none` a visibile, o viene appena aggiunto) non ha uno stato «prima», quindi una transizione non saprebbe da dove partire. `@starting-style` gli dà quello stato di partenza. `transition-behavior: allow-discrete`, o la parola `allow-discrete` nella transizione, fa scattare `display` al momento giusto: all’inizio quando entra, alla fine quando esce.', 'An element that appears (goes from `display: none` to visible, or has just been added) has no “before” state, so a transition would not know where to start. `@starting-style` gives it that starting state. `transition-behavior: allow-discrete`, or the word `allow-discrete` in the transition, makes `display` flip at the right moment: at the start when entering, at the end when leaving.'),
            Fig(L(fig_cssanim_entry(['entrata', 'uscita', 'non c’è', 'parte da qui', 'arriva', 'sparisce solo ora', 'allow-discrete: display cambia all’inizio entrando, alla fine uscendo']), fig_cssanim_entry(['entry', 'exit', 'not there', 'starts from here', 'arrives', 'only now it goes', 'allow-discrete: display flips at the start on entry, at the end on exit'])),
                L('Due strisce di tre momenti. Entrata: prima l’avviso non c’è (`display: none`), poi compare trasparente e più in basso come dice `@starting-style`, infine arriva pieno al suo posto. Uscita: l’avviso pieno sfuma verso l’opacità zero e solo alla fine diventa `display: none`.', 'Two strips of three moments. Entry: first the notice is not there (`display: none`), then it appears transparent and lower as `@starting-style` says, last it arrives solid in its place. Exit: the solid notice fades towards zero opacity and only at the end becomes `display: none`.'),
                L('`@starting-style` dà il «prima» che manca a ciò che compare.', '`@starting-style` gives the missing “before” to what appears.')),
            C('.avviso {
  opacity: 1;
  translate: 0 0;
  transition: opacity .3s, translate .3s, display .3s allow-discrete;
}
.avviso[hidden] {
  display: none;
  opacity: 0;
}
@starting-style {
  .avviso { opacity: 0; translate: 0 20px; }
}', 'css', L('<p class="avviso" style="background:#d5f6ea;border:2px solid #0b7f5c;padding:12px;border-radius:10px">Salvato! Sono entrato dal basso, sfumando.</p>', '<p class="avviso" style="background:#d5f6ea;border:2px solid #0b7f5c;padding:12px;border-radius:10px">Saved! I came in from below, fading.</p>')),
            P('Quando togli `hidden`, l’avviso parte trasparente e 20px più in basso e arriva al suo posto; quando lo rimetti, svanisce e solo alla fine sparisce davvero. Nei browser che non conoscono queste regole l’elemento compare e scompare di colpo: nessun danno.', 'When you remove `hidden`, the notice starts transparent and 20px lower and moves into place; when you add it back, it fades and only disappears for real at the end. In browsers that do not know these rules the element appears and disappears at once: no harm done.'),
        ], [
            Q('e1', L('Che cosa definisce `@starting-style`?', 'What does `@starting-style` define?'), [L('Lo stile della pagina all’apertura', 'The page style when it opens'), L('Lo stato da cui parte la transizione d’entrata', 'The state the entry transition starts from'), L('L’ultima tappa di un’animazione', 'The last stage of an animation'), L('Lo stile di stampa', 'The print style')], 1, L('Vale solo nel primo istante in cui l’elemento viene disegnato.', 'It only applies at the first moment the element is drawn.')),
            Q('e2', L('Perché serve `allow-discrete` su `display`?', 'Why does `display` need `allow-discrete`?'), [L('Per animare `display` a valori intermedi', 'To animate `display` through in-between values'), L('Per rendere la transizione più veloce', 'To make the transition faster'), L('Per far scattare `display` al momento giusto', 'To make `display` flip at the right moment'), L('Per nascondere l’elemento ai lettori di schermo', 'To hide the element from screen readers')], 2, L('`display` non ha valori intermedi: si sceglie solo quando cambia.', '`display` has no in-between values: you only choose when it changes.')),
            Q('e3', L('Che cosa succede nei browser che non supportano `@starting-style`?', 'What happens in browsers that do not support `@starting-style`?'), [L('La pagina va in errore', 'The page breaks'), L('L’elemento non compare mai', 'The element never appears'), L('Il CSS viene ignorato tutto', 'All the CSS is ignored'), L('L’elemento compare senza animazione', 'The element appears without animation')], 3, L('È un miglioramento progressivo.', 'It is a progressive enhancement.')),
        ], task('css', L('Scrivi un `@starting-style` che dia a `.card` `opacity: 0`, e dai a `.card` una `transition` su `opacity` di `.4s`.', 'Write a `@starting-style` that gives `.card` `opacity: 0`, and give `.card` a `transition` on `opacity` of `.4s`.'), '', [re('@starting-style\s*\{[^{}]*\.card\s*\{[^}]*opacity\s*:\s*0', L('C’è lo stato di partenza', 'The starting state is there')), re('\.card\s*\{[^}]*transition\s*:[^;}]*opacity\s+0?\.4s', L('L’opacità ha una transizione', 'Opacity has a transition'))], $page, "@starting-style {\n  .card { opacity: 0; }\n}\n.card {\n  transition: opacity .4s;\n}"), [
            dBlanks(pFill(), ".avviso {\n  transition: opacity .3s, display .3s ___;\n}\n___ {\n  .avviso { opacity: 0; }\n}", ['allow-discrete', '@starting-style'], ['ease-out', '@keyframes', '@media'], L('`display` che scatta al momento giusto e uno stato di partenza.', '`display` flipping at the right moment and a starting state.')),
            dFlash(pFlash(), [[L('Un elemento appena comparso non ha uno stato «prima»', 'A newly shown element has no “before” state'), true], [L('Con `allow-discrete`, `display` sfuma tra `none` e `block`', 'With `allow-discrete`, `display` fades between `none` and `block`'), false], [L('`@starting-style` funziona anche con i popover', '`@starting-style` also works with popovers'), true], [L('Per animare un’entrata serve per forza JavaScript', 'Animating an entry always needs JavaScript'), false]], L('Entrate e uscite morbide, scritte solo in CSS.', 'Smooth entries and exits, written in CSS alone.')),
        ]),
        lesson('scroll-driven', L('Animazioni guidate dallo scorrimento', 'Scroll-driven animations'), [
            P('Con `animation-timeline` un’animazione segue lo scorrimento invece dell’orologio. `scroll()` la lega allo scorrimento di un contenitore, per esempio per una barra che mostra quanto hai letto; `view()` al passaggio dell’elemento dentro lo schermo, per farlo apparire mentre entra.', 'With `animation-timeline` an animation follows scrolling instead of the clock. `scroll()` ties it to a container’s scrolling, for example for a bar showing how much you have read; `view()` to the element passing through the screen, to make it appear as it enters.'),
            Fig(L(fig_cssanim_scroll(['cresce da 0 a 100%', 'la barra segue lo scorrimento,', 'non l’orologio']), fig_cssanim_scroll(['grows from 0 to 100%', 'the bar follows the scrolling,', 'not the clock'])),
                L('Una pagina che scorre: mentre il cursore della barra di scorrimento scende, la sottile barra arancione in cima si allunga da sinistra; quando la pagina è in fondo, la barra è piena.', 'A scrolling page: as the scrollbar thumb goes down, the thin orange bar at the top stretches from the left; when the page is at the bottom, the bar is full.'),
                L('Nessuna durata: la «posizione nel tempo» è la posizione nello scorrimento.', 'No duration: the “position in time” is the position in the scroll.')),
            C('@keyframes cresci { from { scale: 0 1; } to { scale: 1 1; } }
.progresso {
  position: fixed; top: 0; left: 0;
  width: 100%; height: 4px;
  background: #FF5B2E;
  transform-origin: left;
  animation: cresci linear both;
  animation-timeline: scroll();
}

@keyframes appari { from { opacity: 0; translate: 0 40px; } }
.card {
  animation: appari linear both;
  animation-timeline: view();
  animation-range: entry 0% cover 30%;
}', 'css'),
            P('Scrivi `animation-timeline` **dopo** la scorciatoia `animation`, che altrimenti la riporta al valore predefinito. Non serve una durata: la dà lo scorrimento. I browser principali le supportano solo nelle versioni più recenti, quindi proteggile con `@supports (animation-timeline: scroll())` e rispetta `prefers-reduced-motion`.', 'Write `animation-timeline` **after** the `animation` shorthand, which would otherwise reset it to its default. No duration is needed: scrolling provides it. The main browsers support them only in their latest versions, so guard them with `@supports (animation-timeline: scroll())` and respect `prefers-reduced-motion`.'),
        ], [
            Q('d1', L('Qual è la differenza tra `scroll()` e `view()`?', 'What is the difference between `scroll()` and `view()`?'), [L('`scroll()` segue lo scorrimento del contenitore, `view()` il passaggio dell’elemento nello schermo', '`scroll()` follows the container’s scrolling, `view()` the element passing through the screen'), L('Sono due nomi della stessa cosa', 'They are two names for the same thing'), L('`view()` funziona solo con i video', '`view()` only works with videos'), L('`scroll()` serve solo in orizzontale', '`scroll()` is only for horizontal scrolling')], 0, L('Barra di avanzamento con `scroll()`, comparse con `view()`.', 'A progress bar with `scroll()`, reveals with `view()`.')),
            Q('d2', L('Perché `animation-timeline` va scritta dopo `animation`?', 'Why must `animation-timeline` come after `animation`?'), [L('Per una questione di stile', 'As a matter of style'), L('Perché la scorciatoia `animation` la azzera', 'Because the `animation` shorthand resets it'), L('Perché il browser legge il CSS al contrario', 'Because the browser reads CSS backwards'), L('Non importa l’ordine', 'The order does not matter')], 1, L('Una scorciatoia reimposta tutte le sue sotto-proprietà.', 'A shorthand resets all of its sub-properties.')),
            Q('d3', L('Che durata serve a un’animazione guidata dallo scorrimento?', 'What duration does a scroll-driven animation need?'), [L('Almeno 1 secondo', 'At least 1 second'), L('Esattamente 100ms', 'Exactly 100ms'), L('Una durata per ogni pagina', 'One duration per page'), L('Nessuna: il tempo lo sostituisce lo scorrimento', 'None: scrolling replaces time')], 3, L('Di solito si usa la curva `linear`, per seguire il dito alla pari.', 'The `linear` curve is usually used, to follow the finger evenly.')),
        ], task('css', L('Scrivi `@keyframes cresci` e applicalo a `.palla` con `animation: cresci linear both` seguita da `animation-timeline: scroll()`.', 'Write `@keyframes cresci` and apply it to `.palla` with `animation: cresci linear both` followed by `animation-timeline: scroll()`.'), '', [re('@keyframes\s+cresci', L('I keyframes esistono', 'The keyframes exist')), re('\.palla\s*\{[^}]*animation\s*:[^;}]*cresci[^}]*animation-timeline\s*:\s*scroll\(\s*\)', L('La palla segue lo scorrimento', 'The ball follows scrolling'))], $page, "@keyframes cresci {\n  from { scale: 0; }\n  to { scale: 1; }\n}\n.palla {\n  animation: cresci linear both;\n  animation-timeline: scroll();\n}"), [
            dOrder(pSort(), ['.progresso {', '  transform-origin: left;', '  animation: cresci linear both;', '  animation-timeline: scroll();', '}'], L('La scorciatoia prima, la linea temporale dopo.', 'The shorthand first, the timeline after.')),
            dMatch(pMatch(), [['scroll()', L('Scorrimento del contenitore', 'Container scrolling')], ['view()', L('Passaggio dell’elemento nello schermo', 'The element passing through the screen')], ['animation-range', L('Da dove a dove gira', 'From where to where it runs')], ['@supports', L('Protegge i browser vecchi', 'Guards old browsers')]], L('Il dito che scorre diventa l’orologio.', 'The scrolling finger becomes the clock.')),
        ]),
        lesson('view-transitions', L('Transizioni di vista', 'View transitions'), [
            P('Le **View Transitions** animano il passaggio tra due stati della pagina: il browser fotografa lo stato vecchio, tu aggiorni il contenuto, e lui sfuma dall’uno all’altro. Nella stessa pagina si avviano da JavaScript con `document.startViewTransition()`; tra due pagine dello stesso sito basta una regola CSS su entrambe.', '**View Transitions** animate the change between two states of the page: the browser snapshots the old state, you update the content, and it fades from one to the other. Within a page you start them from JavaScript with `document.startViewTransition()`; between two pages of the same site one CSS rule on both is enough.'),
            Fig(L(fig_cssanim_view(['stato vecchio: la lista', 'stato nuovo: il dettaglio', 'stesso view-transition-name: la foto vola, il resto sfuma']), fig_cssanim_view(['old state: the list', 'new state: the detail', 'same view-transition-name: the photo flies, the rest fades'])),
                L('A sinistra una lista di miniature, a destra la pagina di dettaglio con una foto grande. La miniatura che si chiama `foto` si sposta e si ingrandisce fino al posto della foto grande, mentre le altre miniature sfumano.', 'On the left a list of thumbnails, on the right the detail page with a big photo. The thumbnail named `foto` moves and grows into the place of the big photo, while the other thumbnails fade.'),
                L('Il browser anima tra due fotografie della pagina: tu cambi solo il contenuto.', 'The browser animates between two snapshots of the page: you only change the content.')),
            C('@view-transition { navigation: auto; }

.foto-prodotto { view-transition-name: foto; }

::view-transition-old(root),
::view-transition-new(root) {
  animation-duration: .4s;
}', 'css'),
            C('document.startViewTransition(() => mostraDettaglio());', 'js'),
            P('Un elemento con lo stesso `view-transition-name` nei due stati «vola» dalla vecchia posizione alla nuova. Ogni nome deve essere unico nella pagina. Nei browser senza supporto la pagina cambia normalmente, quindi è un miglioramento sicuro; ricordati di ridurlo con `prefers-reduced-motion`.', 'An element with the same `view-transition-name` in both states “flies” from the old position to the new one. Each name must be unique on the page. In browsers without support the page changes normally, so it is a safe enhancement; remember to tone it down with `prefers-reduced-motion`.'),
        ], [
            Q('v1', L('Che cosa fa `view-transition-name` su un elemento?', 'What does `view-transition-name` do on an element?'), [L('Gli dà un titolo per i motori di ricerca', 'It gives it a title for search engines'), L('Lo nasconde durante la transizione', 'It hides it during the transition'), L('Lo fa animare dalla vecchia alla nuova posizione', 'It makes it animate from the old position to the new one'), L('Lo carica prima degli altri', 'It loads it before the others')], 2, L('Lo stesso nome lega i due stati dell’elemento.', 'The same name links the element’s two states.')),
            Q('v2', L('Come si attivano le transizioni tra due pagine dello stesso sito?', 'How do you turn on transitions between two pages of the same site?'), ['@view-transition { navigation: auto; }', '<meta name="transition">', 'transition: page 1s;', '@keyframes page'], 0, L('La regola va nel CSS di entrambe le pagine.', 'The rule goes in the CSS of both pages.')),
            Q('v3', L('Che cosa succede in un browser senza supporto?', 'What happens in a browser without support?'), [L('La pagina non si apre', 'The page does not open'), L('La pagina cambia normalmente, senza animazione', 'The page changes normally, without animation'), L('Compare un avviso all’utente', 'A warning appears for the user'), L('Il sito diventa lento', 'The site becomes slow')], 1, L('Nessun danno: solo niente effetto.', 'No harm: just no effect.')),
        ], task('css', L('Attiva le transizioni tra pagine con `@view-transition { navigation: auto; }` e dai a `.card` `view-transition-name: scheda`.', 'Turn on transitions between pages with `@view-transition { navigation: auto; }` and give `.card` `view-transition-name: scheda`.'), '', [re('@view-transition\s*\{[^}]*navigation\s*:\s*auto', L('Le transizioni tra pagine sono attive', 'Transitions between pages are on')), re('\.card\s*\{[^}]*view-transition-name\s*:\s*scheda', L('La card ha un nome di transizione', 'The card has a transition name'))], $page, "@view-transition {\n  navigation: auto;\n}\n.card {\n  view-transition-name: scheda;\n}"), [
            dMatch(pMatch(), [['startViewTransition()', L('Transizione nella stessa pagina', 'Transition within a page')], ['@view-transition', L('Transizione tra pagine', 'Transition between pages')], ['view-transition-name', L('L’elemento che vola', 'The element that flies')], ['::view-transition-old()', L('La foto dello stato vecchio', 'The snapshot of the old state')], ['::view-transition-new()', L('Lo stato nuovo', 'The new state')]], L('Il browser fa le foto, tu decidi il movimento.', 'The browser takes the snapshots, you choose the movement.')),
            dFlash(pFlash(), [[L('Due elementi della stessa pagina possono avere lo stesso `view-transition-name`', 'Two elements on the same page can share a `view-transition-name`'), false], [L('Senza supporto il sito funziona lo stesso', 'Without support the site still works'), true], [L('Le View Transitions richiedono un framework', 'View Transitions need a framework'), false], [L('La durata si cambia con gli pseudo-elementi `::view-transition-*`', 'The duration is changed with the `::view-transition-*` pseudo-elements'), true]], L('Un effetto da app nativa, con poche righe.', 'A native-app feel, in a few lines.')),
        ]),
    ],
];
