<?php
/** Intermediate UI: components and states, design tokens and design systems, icons and imagery, motion and micro-interactions, handoff, data visualisation, variable fonts and fluid type. */

/** Figures of this course, each with its words in one language ('it' or 'en'). */
if (!function_exists('fig_uiint_states')) {
    /** The same button in six states. */
    function fig_uiint_states(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Salva', 'normale', 'hover', 'focus', 'premuto', 'disattivato', 'caricamento']
            : ['Save', 'default', 'hover', 'focus', 'pressed', 'disabled', 'loading'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 170" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.sp{transform-origin:568px 70px;animation:sp 3s linear infinite}@keyframes sp{to{transform:rotate(360deg)}}@media (prefers-reduced-motion:reduce){.sp{animation:none}}</style>
<rect x="14" y="48" width="90" height="44" rx="10" fill="#1B66BC"/>
<rect x="118" y="48" width="90" height="44" rx="10" fill="#14508F"/>
<path d="M186 76 l0 18 l5 -4 l3 7 l3 -1.5 l-3 -7 l6 0 z" fill="#FFFFFF" stroke="#1E1C52" stroke-width="1.5" stroke-linejoin="round"/>
<rect x="216" y="42" width="102" height="56" rx="14" fill="none" stroke="#7048E8" stroke-width="3"/>
<rect x="222" y="48" width="90" height="44" rx="10" fill="#1B66BC"/>
<rect x="326" y="51" width="90" height="44" rx="10" fill="#0E3D70"/>
<rect x="430" y="48" width="90" height="44" rx="10" fill="#E4E2F0"/>
<rect x="534" y="48" width="90" height="44" rx="10" fill="#1B66BC"/>
<g font-weight="700" text-anchor="middle" fill="#FFFFFF"><text x="59" y="75">{$t[0]}</text><text x="163" y="75">{$t[0]}</text><text x="267" y="75">{$t[0]}</text><text x="371" y="78">{$t[0]}</text></g>
<text x="475" y="75" text-anchor="middle" font-weight="700" fill="#8A87B0">{$t[0]}</text>
<circle cx="568" cy="70" r="10" fill="none" stroke="#5C8FD0" stroke-width="3"/>
<path class="sp" d="M568 60 a10 10 0 0 1 10 10" fill="none" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
<g font-size="13" fill="#5A5880" text-anchor="middle"><text x="59" y="126">{$t[1]}</text><text x="163" y="126">{$t[2]}</text><text x="267" y="126">{$t[3]}</text><text x="371" y="126">{$t[4]}</text><text x="475" y="126">{$t[5]}</text><text x="579" y="126">{$t[6]}</text></g>
<g font-size="13" fill="#8A87B0" text-anchor="middle"><text x="163" y="146">:hover</text><text x="267" y="146">:focus-visible</text><text x="371" y="146">:active</text><text x="475" y="146">:disabled</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uiint_tokens')) {
    /** Primitive, semantic and component: the theme only changes what the semantic token points to. */
    function fig_uiint_tokens(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Primitivo', 'Semantico', 'Componente', 'Tema chiaro', 'Tema scuro', 'Invia', 'il componente non cambia: cambia a che cosa punta il token semantico']
            : ['Primitive', 'Semantic', 'Component', 'Light theme', 'Dark theme', 'Send', 'the component does not change: what the semantic token points to does'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0 L10 5 L0 10 z" fill="#8A87B0"/></marker></defs>
<g font-weight="700" text-anchor="middle" font-size="15"><text x="225" y="24">{$t[0]}</text><text x="395" y="24">{$t[1]}</text><text x="560" y="24">{$t[2]}</text></g>
<g font-size="13" font-weight="700" fill="#5A5880"><text x="16" y="82">{$t[3]}</text><text x="16" y="192">{$t[4]}</text></g>
<rect x="150" y="50" width="150" height="56" rx="10" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="162" y="62" width="32" height="32" rx="6" fill="#1B66BC"/><text x="202" y="78" font-family="ui-monospace, monospace" font-size="13">--blue-600</text><text x="202" y="96" font-size="13" fill="#5A5880">#1B66BC</text>
<rect x="150" y="160" width="150" height="56" rx="10" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="162" y="172" width="32" height="32" rx="6" fill="#8AB8F5"/><text x="202" y="188" font-family="ui-monospace, monospace" font-size="13">--blue-300</text><text x="202" y="206" font-size="13" fill="#5A5880">#8AB8F5</text>
<rect x="320" y="105" width="150" height="56" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="395" y="138" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13" font-weight="700" fill="#5F3DC4">--color-action</text>
<path d="M300 78 C 312 78, 312 120, 318 125" fill="none" stroke="#8A87B0" stroke-width="2" marker-end="url(#a)"/>
<path d="M300 188 C 312 188, 312 146, 318 141" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4" marker-end="url(#a)"/>
<path d="M470 133 H498" stroke="#8A87B0" stroke-width="2" marker-end="url(#a)"/>
<rect x="500" y="50" width="120" height="56" rx="10" fill="#FFFFFF" stroke="#E4E2F0" stroke-width="2"/>
<rect x="515" y="60" width="90" height="36" rx="8" fill="#1B66BC"/><text x="560" y="83" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[5]}</text>
<rect x="500" y="160" width="120" height="56" rx="10" fill="#1E1C52"/>
<rect x="515" y="170" width="90" height="36" rx="8" fill="#8AB8F5"/><text x="560" y="193" text-anchor="middle" font-weight="700" fill="#1E1C52">{$t[5]}</text>
<path d="M560 106 V160" stroke="#E4E2F0" stroke-width="2"/>
<text x="320" y="262" text-anchor="middle" font-size="13" fill="#5A5880">{$t[6]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uiint_icons')) {
    /** An icon on its 24×24 grid, then a consistent family next to a mixed one. */
    function fig_uiint_icons(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Griglia 24 × 24', 'tratto 2 px, margine 2 px', 'Coerenti', 'Mescolate', 'stessa famiglia, stesso tratto', 'pieno, sottile e spesso insieme']
            : ['24 × 24 grid', '2 px stroke, 2 px padding', 'Consistent', 'Mixed', 'same family, same stroke', 'filled, thin and thick together'];
        $grid = '';
        for ($i = 0; $i <= 24; $i += 2) $grid .= '<line x1="' . (30 + $i * 7) . '" y1="40" x2="' . (30 + $i * 7) . '" y2="208" stroke="#E4E2F0" stroke-width="1"/><line x1="30" y1="' . (40 + $i * 7) . '" x2="198" y2="' . (40 + $i * 7) . '" stroke="#E4E2F0" stroke-width="1"/>';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="114" y="26" text-anchor="middle" font-size="15" font-weight="700">{$t[0]}</text>
{$grid}
<rect x="44" y="54" width="140" height="140" fill="none" stroke="#F03E82" stroke-width="1.5" stroke-dasharray="4 3"/>
<path d="M58 124 L114 68 L170 124 M72 110 V180 H156 V110 M100 180 V142 H128 V180" fill="none" stroke="#1E1C52" stroke-width="14" stroke-linejoin="round" stroke-linecap="round"/>
<text x="114" y="232" text-anchor="middle" font-size="13" fill="#5A5880">{$t[1]}</text>
<line x1="240" y1="30" x2="240" y2="240" stroke="#E4E2F0" stroke-width="2"/>
<text x="270" y="60" font-weight="700" fill="#0B7F5C">✓ {$t[2]}</text>
<g fill="none" stroke="#1E1C52" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
<path d="M272 98 L288 82 L304 98 M276 94 V112 H300 V94"/>
<circle cx="350" cy="94" r="10"/><path d="M357 101 L366 110"/>
<circle cx="418" cy="96" r="5"/><circle cx="418" cy="96" r="13" stroke-dasharray="5 3.2"/>
<path d="M466 86 H494 M472 86 V112 H488 V86 M476 82 H484"/></g>
<text x="272" y="138" font-size="13" fill="#5A5880">{$t[4]}</text>
<text x="270" y="180" font-weight="700" fill="#C2255C">✗ {$t[3]}</text>
<path d="M272 218 L288 202 L304 218 V232 H272 Z" fill="#1E1C52"/>
<g fill="none" stroke="#1E1C52" stroke-linecap="round"><circle cx="350" cy="214" r="10" stroke-width="1"/><path d="M357 221 L366 230" stroke-width="1"/>
<circle cx="418" cy="216" r="11" stroke-width="5"/><path d="M466 206 H494 M472 206 V232 H488 V206" stroke-width="2.5"/></g>
<text x="272" y="254" font-size="13" fill="#5A5880">{$t[5]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uiint_easing')) {
    /** Three easing curves and three dots moving with them: linear, ease-out, ease-in. */
    function fig_uiint_easing(string $lg): string
    {
        $t = $lg === 'it'
            ? ['lineare', 'ease-out', 'ease-in', 'meccanico', 'naturale per ciò che entra', 'per ciò che esce', 'tempo →']
            : ['linear', 'ease-out', 'ease-in', 'mechanical', 'natural for things entering', 'for things leaving', 'time →'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.m{animation:m 4s infinite}.l{animation-timing-function:linear}.o{animation-timing-function:cubic-bezier(0,0,.2,1)}.i{animation-timing-function:cubic-bezier(.4,0,1,1)}@keyframes m{0%{transform:translateX(0)}50%,100%{transform:translateX(260px)}}@media (prefers-reduced-motion:reduce){.m{animation:none}}</style>
<g fill="#F1F0F8"><rect x="20" y="20" width="80" height="80" rx="8"/><rect x="20" y="110" width="80" height="80" rx="8"/><rect x="20" y="200" width="80" height="80" rx="8"/></g>
<g fill="none" stroke-width="3" stroke-linecap="round">
<path d="M28 92 L92 28" stroke="#8A87B0"/>
<path d="M28 182 C 28 182, 41 118, 92 118" stroke="#0B7F5C"/>
<path d="M28 272 C 54 272, 92 272, 92 208" stroke="#2F8CF0"/></g>
<g stroke="#E4E2F0" stroke-width="2"><line x1="160" y1="60" x2="440" y2="60"/><line x1="160" y1="150" x2="440" y2="150"/><line x1="160" y1="240" x2="440" y2="240"/></g>
<circle class="m l" cx="170" cy="60" r="12" fill="#8A87B0"/>
<circle class="m o" cx="170" cy="150" r="12" fill="#0B7F5C"/>
<circle class="m i" cx="170" cy="240" r="12" fill="#2F8CF0"/>
<g font-weight="700"><text x="112" y="45">{$t[0]}</text><text x="112" y="135" fill="#0B7F5C">{$t[1]}</text><text x="112" y="225" fill="#1B66BC">{$t[2]}</text></g>
<g font-size="13" fill="#5A5880"><text x="454" y="65">{$t[3]}</text><text x="454" y="155">{$t[4]}</text><text x="454" y="245">{$t[5]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uiint_charts')) {
    /** Four small charts, each with the question it answers. */
    function fig_uiint_charts(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Barre', 'Linee', 'Dispersione', 'Torta', 'confrontare categorie', 'andamento nel tempo', 'relazione tra due misure', 'poche parti di un intero']
            : ['Bars', 'Lines', 'Scatter', 'Pie', 'compare categories', 'trend over time', 'relation of two measures', 'a few parts of a whole'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<g stroke="#1E1C52" stroke-width="2"><line x1="20" y1="160" x2="140" y2="160"/><line x1="180" y1="160" x2="300" y2="160"/><line x1="180" y1="40" x2="180" y2="160"/><line x1="340" y1="160" x2="460" y2="160"/><line x1="340" y1="40" x2="340" y2="160"/></g>
<g fill="#C9C7DE"><rect x="28" y="100" width="20" height="60" rx="3"/><rect x="56" y="70" width="20" height="90" rx="3"/><rect x="112" y="110" width="20" height="50" rx="3"/></g><rect x="84" y="46" width="20" height="114" rx="3" fill="#E8590C"/>
<path d="M186 140 L210 128 L234 132 L258 100 L282 84 L296 60" fill="none" stroke="#2F8CF0" stroke-width="3" stroke-linejoin="round"/>
<g fill="#7048E8"><circle cx="352" cy="146" r="4"/><circle cx="364" cy="136" r="4"/><circle cx="374" cy="128" r="4"/><circle cx="386" cy="132" r="4"/><circle cx="396" cy="110" r="4"/><circle cx="410" cy="102" r="4"/><circle cx="420" cy="90" r="4"/><circle cx="432" cy="84" r="4"/><circle cx="446" cy="62" r="4"/><circle cx="440" cy="76" r="4"/></g>
<circle cx="560" cy="100" r="56" fill="#C9C7DE"/>
<path d="M560 100 L560 44 A56 56 0 0 1 613 117 Z" fill="#0B7F5C"/>
<path d="M560 100 L613 117 A56 56 0 0 1 530 147 Z" fill="#8A87B0"/>
<g font-weight="700" font-size="15" text-anchor="middle"><text x="80" y="190">{$t[0]}</text><text x="240" y="190">{$t[1]}</text><text x="400" y="190">{$t[2]}</text><text x="560" y="190">{$t[3]}</text></g>
<g fill="#5A5880" text-anchor="middle"><text x="80" y="210">{$t[4]}</text><text x="240" y="210">{$t[5]}</text><text x="400" y="210">{$t[6]}</text><text x="560" y="210">{$t[7]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uiint_clamp')) {
    /** clamp(1.75rem, 1rem + 3vw, 3rem): the size stays at the minimum, grows with the window, then stops at the maximum. */
    function fig_uiint_clamp(string $lg): string
    {
        $t = $lg === 'it'
            ? ['dimensione del titolo', 'larghezza della finestra', 'minimo 28 px', 'cresce: 1rem + 3vw', 'massimo 48 px']
            : ['heading size', 'window width', 'minimum 28 px', 'grows: 1rem + 3vw', 'maximum 48 px'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<line x1="80" y1="220" x2="610" y2="220" stroke="#1E1C52" stroke-width="2"/>
<line x1="80" y1="30" x2="80" y2="220" stroke="#1E1C52" stroke-width="2"/>
<g stroke="#E4E2F0" stroke-width="1"><line x1="80" y1="160" x2="610" y2="160"/><line x1="80" y1="60" x2="610" y2="60"/></g>
<g text-anchor="end" fill="#5A5880"><text x="72" y="164">28</text><text x="72" y="64">48</text><text x="72" y="224">20</text></g>
<g text-anchor="middle" fill="#5A5880"><text x="80" y="238">320</text><text x="133" y="238">400</text><text x="578" y="238">1067</text><text x="400" y="238">800</text></g>
<path d="M80 160 H133 L578 60 H610" fill="none" stroke="#7048E8" stroke-width="3" stroke-linejoin="round"/>
<g stroke="#7048E8" stroke-width="2" stroke-dasharray="4 4"><line x1="133" y1="160" x2="133" y2="220"/><line x1="578" y1="60" x2="578" y2="220"/></g>
<text x="88" y="184" fill="#5F3DC4" font-weight="700">{$t[2]}</text>
<text x="350" y="100" fill="#5F3DC4" font-weight="700" transform="rotate(-12.7 350 100)">{$t[3]}</text>
<text x="600" y="48" text-anchor="end" fill="#5F3DC4" font-weight="700">{$t[4]}</text>
<text x="345" y="262" text-anchor="middle" font-size="14" font-weight="700">{$t[1]} (px)</text>
<text x="24" y="125" text-anchor="middle" font-size="14" font-weight="700" transform="rotate(-90 24 125)">{$t[0]} (px)</text>
</svg>
SVG;
    }
}

return [
    'id' => 'ui-intermediate', 'family' => 'ui', 'after' => 'ui-base', 'categories' => ['design', 'frontend'],
    'title' => L('UI: livello intermedio', 'UI Intermediate'), 'short' => L('UI intermedio', 'UI Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Componenti e stati, design token e design system, icone e immagini, movimento e micro-interazioni, handoff, grafici e tipografia avanzata: dalle schermate a un sistema coerente.', 'Components and states, design tokens and design systems, icons and imagery, motion and micro-interactions, handoff, charts and advanced typography: from screens to a consistent system.'),
    'lessons' => [
        lesson('states', L('Componenti e stati', 'Components and states'), [
            P('Un’interfaccia è fatta di **componenti** riutilizzabili: pulsanti, campi, card, menu. Ogni componente ha più **stati** da progettare: **normale**, **hover**, **focus** (da tastiera), **attivo** (premuto), **disattivato**, **caricamento**, **errore**, **selezionato**. Dimenticarne uno significa lasciare lo sviluppatore a inventarlo.', 'An interface is made of reusable **components**: buttons, fields, cards, menus. Each component has several **states** to design: **default**, **hover**, **focus** (keyboard), **active** (pressed), **disabled**, **loading**, **error**, **selected**. Forgetting one means leaving the developer to invent it.'),
            Fig(L(fig_uiint_states('it'), fig_uiint_states('en')),
                L('Lo stesso pulsante «Salva» sei volte: normale; hover, più scuro con il cursore sopra; focus, con un anello viola attorno; premuto, più scuro e spostato in basso; disattivato, grigio chiaro; caricamento, con una rotellina al posto del testo. Sotto ciascuno il nome dello stato e la pseudo-classe CSS.', 'The same “Save” button six times: default; hover, darker with the cursor over it; focus, with a purple ring around it; pressed, darker and shifted down; disabled, light grey; loading, with a spinner instead of the label. Below each, the state’s name and its CSS pseudo-class.'),
                L('Ogni stato va disegnato: chi usa la tastiera vede solo il focus.', 'Every state must be designed: keyboard users only see the focus.')),
            P('Anche le schermate hanno stati: **vuoto** (nessun dato ancora: spiega e invita all’azione), **caricamento** (skeleton screen), **parziale**, **errore** e **ideale**. Uno stato vuoto curato (“Nessun progetto. Crea il primo!”) trasforma un vicolo cieco in un inizio.', 'Screens have states too: **empty** (no data yet: explain and invite action), **loading** (skeleton screen), **partial**, **error** and **ideal**. A well-crafted empty state (“No projects. Create your first!”) turns a dead end into a beginning.'),
            C('.btn          { background: #4f46e5; }
.btn:hover    { background: #4338ca; }
.btn:focus-visible { outline: 3px solid #a5b4fc; }
.btn:active   { transform: translateY(1px); }
.btn:disabled { opacity: .5; cursor: not-allowed; }', 'css', L('<style>.btn{color:#fff;border:0;border-radius:8px;padding:10px 18px;font:600 15px system-ui}</style><button class="btn">Passa sopra o usa Tab</button> <button class="btn" disabled>Disattivato</button>', '<style>.btn{color:#fff;border:0;border-radius:8px;padding:10px 18px;font:600 15px system-ui}</style><button class="btn">Hover me or press Tab</button> <button class="btn" disabled>Disabled</button>')),
        ], [
            Q('s1', L('Quale stato serve a chi usa la tastiera?', 'Which state serves keyboard users?'), [L('Focus', 'Focus'), L('Hover', 'Hover'), L('Disattivato', 'Disabled'), L('Vuoto', 'Empty')], 0, L('Mostra dove si è.', 'Shows where you are.')),
            Q('s2', L('Che cosa deve fare uno stato vuoto?', 'What should an empty state do?'), [L('Spiegare e invitare all’azione', 'Explain and invite action'), L('Restare completamente bianco', 'Stay completely blank'), L('Mostrare un errore', 'Show an error'), L('Ricaricare la pagina', 'Reload the page')], 0, L('Un inizio, non un vicolo cieco.', 'A start, not a dead end.')),
            Q('s3', L('Che cos’è uno skeleton screen?', 'What is a skeleton screen?'), [L('Una sagoma del contenuto durante il caricamento', 'A content outline while loading'), L('Una schermata di Halloween', 'A Halloween screen'), L('Un wireframe stampato', 'A printed wireframe'), L('Un errore del server', 'A server error')], 0, L('Il caricamento sembra più breve.', 'Loading feels shorter.')),
        ], task('css', L('Completa gli stati del `.btn`: uno `:hover` con un `background` diverso, un `:focus-visible` con `outline` e un `:disabled` con `opacity`.', 'Complete the `.btn` states: a `:hover` with a different `background`, a `:focus-visible` with `outline` and a `:disabled` with `opacity`.'), ".btn {\n  background: #4f46e5;\n  color: white;\n}\n", [re('\.btn:hover\s*\{[^}]*background', L('Stato hover', 'Hover state')), re('\.btn:focus-visible\s*\{[^}]*outline', L('Stato focus', 'Focus state')), re('\.btn:disabled\s*\{[^}]*opacity', L('Stato disattivato', 'Disabled state'))], '<button class="btn">Salva</button> <button class="btn" disabled>Salva</button>', ".btn:hover { background: #4338ca; }\n.btn:focus-visible { outline: 3px solid #a5b4fc; }\n.btn:disabled { opacity: .5; }"), [
            dMatch(pMatch(), [['Hover', L('Puntatore sopra', 'Pointer over it')], ['Focus', L('Selezionato da tastiera', 'Selected by keyboard')], [L('Attivo', 'Active'), L('Mentre lo premi', 'While pressing')], [L('Disattivato', 'Disabled'), L('Non disponibile', 'Not available')], [L('Vuoto', 'Empty'), L('Nessun dato ancora', 'No data yet')]], L('Gli stati dei componenti.', 'Component states.')),
            dFlash(pFlash(), [[L('Lo stato di focus va sempre progettato', 'The focus state must always be designed'), true], [L('Uno stato vuoto può restare bianco', 'An empty state may stay blank'), false], [L('Anche il caricamento è uno stato da disegnare', 'Loading is also a state to design'), true], [L('Un pulsante ha un solo stato', 'A button has only one state'), false]], L('Progettare tutti i casi.', 'Designing every case.')),
        ]),
        lesson('tokens', L('Design token e design system', 'Design tokens and design systems'), [
            P('I **design token** sono le decisioni di stile con un nome: colori, dimensioni del testo, spaziature, raggi, ombre. Invece di scrivere `#4f46e5` in cento posti si usa `--color-primary`. I token hanno spesso due livelli: **primitivi** (`--blue-600`) e **semantici** (`--color-action`, `--color-danger`), che dicono il *ruolo* e rendono semplice il tema scuro.', '**Design tokens** are named style decisions: colours, text sizes, spacing, radii, shadows. Instead of writing `#4f46e5` in a hundred places you use `--color-primary`. Tokens often have two levels: **primitives** (`--blue-600`) and **semantic** ones (`--color-action`, `--color-danger`), which state the *role* and make dark themes simple.'),
            Fig(L(fig_uiint_tokens('it'), fig_uiint_tokens('en')),
                L('Tre colonne: primitivo, semantico, componente. Nel tema chiaro il token semantico --color-action punta al primitivo --blue-600, blu scuro; nel tema scuro punta a --blue-300, azzurro. Il pulsante «Invia» usa sempre --color-action e cambia colore da solo.', 'Three columns: primitive, semantic, component. In the light theme the semantic token --color-action points to the primitive --blue-600, a dark blue; in the dark theme it points to --blue-300, a light blue. The “Send” button always uses --color-action and changes colour by itself.'),
                L('I componenti usano solo token semantici: il tema si cambia in un punto.', 'Components use semantic tokens only: the theme changes in one place.')),
            P('Un **design system** riunisce token, componenti (in Figma e nel codice), linee guida d’uso, tono di voce e regole di accessibilità, con una **unica fonte di verità** condivisa tra design e sviluppo. Esempi pubblici: Material Design (Google), Human Interface Guidelines (Apple), Fluent (Microsoft), Carbon (IBM).', 'A **design system** brings together tokens, components (in Figma and code), usage guidelines, tone of voice and accessibility rules, with a **single source of truth** shared by design and development. Public examples: Material Design (Google), Human Interface Guidelines (Apple), Fluent (Microsoft), Carbon (IBM).'),
            C(':root {
  --blue-600: #4f46e5;           /* primitivo */
  --color-action: var(--blue-600); /* semantico */
  --space-2: 16px;
  --radius-m: 8px;
}
.btn { background: var(--color-action); padding: var(--space-2); border-radius: var(--radius-m); }', 'css', L('<button class="btn" style="color:#fff;border:0;font:600 15px system-ui">Invia</button>', '<button class="btn" style="color:#fff;border:0;font:600 15px system-ui">Send</button>')),
        ], [
            Q('k1', L('Che cos’è un design token?', 'What is a design token?'), [L('Una decisione di stile con un nome', 'A named style decision'), L('Una moneta per comprare font', 'A coin to buy fonts'), L('Un’icona del sistema', 'A system icon'), L('Una password del designer', 'A designer’s password')], 0, L('Come --color-primary.', 'Like --color-primary.')),
            Q('k2', L('Che vantaggio danno i token semantici?', 'What do semantic tokens give?'), [L('Indicano il ruolo e facilitano i temi', 'They state the role and ease theming'), L('Rendono il CSS più lungo', 'They make CSS longer'), L('Sostituiscono i componenti', 'They replace components'), L('Nessuno', 'None')], 0, L('Ruolo, non valore.', 'Role, not value.')),
            Q('k3', L('Che cosa contiene un design system?', 'What does a design system contain?'), [L('Token, componenti e linee guida', 'Tokens, components and guidelines'), L('Solo un logo', 'Only a logo'), L('Solo il codice del backend', 'Only backend code'), L('Solo le foto del team', 'Only team photos')], 0, L('Un’unica fonte di verità.', 'A single source of truth.')),
        ], task('css', L('Definisci in `:root` il token `--color-action` e usalo nel `background` di `.btn` con `var()`.', 'Define the `--color-action` token in `:root` and use it in `.btn`’s `background` with `var()`.'), ":root {\n}\n.btn {\n  color: white;\n}\n", [re(':root\s*\{[^}]*--color-action\s*:', L('Token definito', 'Token defined')), re('\.btn\s*\{[^}]*background(-color)?\s*:\s*var\(\s*--color-action\s*\)', L('Token usato', 'Token used'))], '<button class="btn">Invia</button>', ":root {\n  --color-action: #4f46e5;\n}\n.btn {\n  background: var(--color-action);\n}"), [
            dMatch(pMatch(), [[L('Token primitivo', 'Primitive token'), '--blue-600'], [L('Token semantico', 'Semantic token'), '--color-danger'], ['Material Design', 'Google'], ['Fluent', 'Microsoft'], ['Human Interface Guidelines', 'Apple']], L('Sistemi e token.', 'Systems and tokens.')),
            dBlanks(pFill(), ":root {\n  --color-action: #4f46e5;\n}\n.btn {\n  background: ___(___);\n}", ['var', '--color-action'], ['token', '#color-action'], L('I token si leggono con var().', 'Tokens are read with var().')),
        ]),
        lesson('icons', L('Icone e immagini', 'Icons and imagery'), [
            P('Le **icone** funzionano quando sono riconoscibili (casa, lente, ingranaggio, cestino) e **coerenti**: stessa famiglia, stesso spessore del tratto, stessa griglia (24×24 è comune), stesso stile (lineare o pieno). Le icone meno ovvie hanno bisogno di un’**etichetta di testo**: un’icona da sola è ambigua più spesso di quanto pensiamo.', '**Icons** work when they are recognisable (home, magnifier, gear, bin) and **consistent**: same family, same stroke width, same grid (24×24 is common), same style (outline or filled). Less obvious icons need a **text label**: an icon alone is ambiguous more often than we think.'),
            Fig(L(fig_uiint_icons('it'), fig_uiint_icons('en')),
                L('A sinistra l’icona di una casa disegnata su una griglia di 24 per 24 con un margine interno tratteggiato. A destra due righe di icone: casa, lente, ingranaggio e cestino tutte con lo stesso tratto, poi le stesse icone mescolate, una piena, una sottilissima e una molto spessa.', 'On the left a house icon drawn on a 24 by 24 grid with a dashed inner padding. On the right two rows of icons: house, magnifier, gear and bin all with the same stroke, then the same icons mixed, one filled, one very thin and one very thick.'),
                L('Una famiglia coerente si legge come un linguaggio unico.', 'A consistent family reads as a single language.')),
            P('Le **immagini** devono avere un ruolo (mostrare il prodotto, creare atmosfera, spiegare), un **rapporto d’aspetto** coerente (16:9, 4:3, 1:1) e il testo alternativo se informative. Se metti testo sopra una foto, garantisci il contrasto con una **sovrapposizione** scura o sfumata. Per le illustrazioni vale lo stesso principio delle icone: un solo stile.', '**Images** need a role (showing the product, setting the mood, explaining), a consistent **aspect ratio** (16:9, 4:3, 1:1) and alt text when informative. If you put text over a photo, ensure contrast with a dark or gradient **overlay**. For illustrations the same principle as icons applies: a single style.'),
        ], [
            Q('i1', L('Che cosa rende coerente un set di icone?', 'What makes an icon set consistent?'), [L('Stesso stile, tratto e griglia', 'Same style, stroke and grid'), L('Colori tutti diversi', 'All different colours'), L('Icone da famiglie diverse', 'Icons from different families'), L('Dimensioni a caso', 'Random sizes')], 0, L('Una famiglia sola.', 'A single family.')),
            Q('i2', L('Quando un’icona ha bisogno di un’etichetta?', 'When does an icon need a label?'), [L('Quando il significato non è ovvio', 'When its meaning is not obvious'), L('Mai', 'Never'), L('Solo se è blu', 'Only if it is blue'), L('Solo su desktop', 'Only on desktop')], 0, L('Le icone sono spesso ambigue.', 'Icons are often ambiguous.')),
            Q('i3', L('Come garantire leggibilità del testo su una foto?', 'How to ensure text readability over a photo?'), [L('Con una sovrapposizione scura o sfumata', 'With a dark or gradient overlay'), L('Con un font più sottile', 'With a thinner font'), L('Con più colori nel testo', 'With more colours in the text'), L('Non si può', 'It cannot be done')], 0, L('Contrasto prima di tutto.', 'Contrast first.')),
        ], task('css', L('Il titolo `.hero h1` è bianco su una foto. Aggiungi alla `.hero` una sovrapposizione scura con `background-image: linear-gradient(...)` sopra la foto `url(foto.jpg)`.', 'The `.hero h1` heading is white over a photo. Add to `.hero` a dark overlay with `background-image: linear-gradient(...)` above the `url(foto.jpg)` photo.'), ".hero {\n  background-image: url(foto.jpg);\n  color: white;\n}\n", [re('\.hero\s*\{[^}]*background(-image)?\s*:[^;]*linear-gradient\([^;]*url\(', L('Gradiente sopra la foto', 'Gradient over the photo')), re('rgba\(\s*0\s*,\s*0\s*,\s*0|#000|black|rgb\(\s*0\s+0\s+0', L('Una tinta scura', 'A dark tint'))], '<section class="hero"><h1>Estate 2027</h1></section>', ".hero {\n  background-image: linear-gradient(rgba(0, 0, 0, .5), rgba(0, 0, 0, .5)), url(foto.jpg);\n}"), [
            dMatch(pMatch(), [[L('Lente', 'Magnifier'), L('Cerca', 'Search')], [L('Ingranaggio', 'Gear'), L('Impostazioni', 'Settings')], [L('Cestino', 'Bin'), L('Elimina', 'Delete')], ['24×24', L('Griglia comune delle icone', 'Common icon grid')], [L('Sovrapposizione', 'Overlay'), L('Testo leggibile sulla foto', 'Readable text over a photo')]], L('Icone e immagini.', 'Icons and images.')),
            dFlash(pFlash(), [[L('Mescolare icone lineari e piene crea disordine', 'Mixing outline and filled icons creates clutter'), true], [L('Ogni icona è capita da tutti senza etichetta', 'Every icon is understood by all without a label'), false], [L('Le immagini informative hanno bisogno dell’alt', 'Informative images need alt text'), true], [L('Il testo su foto non richiede contrasto', 'Text over photos needs no contrast'), false]], L('Visivi coerenti.', 'Consistent visuals.')),
        ]),
        lesson('motion', L('Movimento e micro-interazioni', 'Motion and micro-interactions'), [
            P('Il **movimento** nelle interfacce ha uno scopo: mostrare **da dove arriva** qualcosa (un pannello che scorre dal lato), **che cosa è cambiato** (un elemento aggiunto alla lista), dare **feedback** (un pulsante che reagisce al tocco). Le durate sono brevi: **100-300 ms** per la maggior parte delle transizioni. Le curve **ease-out** (veloci all’inizio, lente alla fine) sembrano naturali per ciò che entra.', '**Motion** in interfaces has a purpose: showing **where** something comes from (a panel sliding in from the side), **what changed** (an item added to a list), giving **feedback** (a button reacting to touch). Durations are short: **100-300 ms** for most transitions. **Ease-out** curves (fast at the start, slow at the end) feel natural for things entering.'),
            Fig(L(fig_uiint_easing('it'), fig_uiint_easing('en')),
                L('Tre curve di andamento con tre pallini che si muovono da sinistra a destra nello stesso tempo: lineare, a velocità costante; ease-out, che parte veloce e frena alla fine; ease-in, che parte piano e accelera.', 'Three easing curves with three dots moving from left to right in the same time: linear, at constant speed; ease-out, which starts fast and slows down at the end; ease-in, which starts slowly and speeds up.'),
                L('Stessa durata, sensazioni diverse: l’ease-out arriva e si posa.', 'Same duration, different feel: ease-out arrives and settles.')),
            P('Le **micro-interazioni** sono piccoli momenti con uno scopo preciso: il cuore che si riempie al “mi piace”, l’interruttore che scatta, la vibrazione quando il pagamento riesce. Hanno un **innesco**, delle **regole**, un **feedback** e talvolta **ripetizioni**. Rispetta chi chiede meno movimento: con `prefers-reduced-motion` riduci o togli le animazioni.', '**Micro-interactions** are small moments with a precise purpose: the heart filling on “like”, the toggle snapping, the vibration when payment succeeds. They have a **trigger**, **rules**, **feedback** and sometimes **loops**. Respect people who ask for less motion: with `prefers-reduced-motion` reduce or remove animations.'),
        ], [
            Q('m1', L('Quanto dura di solito una transizione di interfaccia?', 'How long does a UI transition usually last?'), [L('Tra 100 e 300 ms', 'Between 100 and 300 ms'), L('Tra 2 e 5 secondi', 'Between 2 and 5 seconds'), L('Un minuto', 'One minute'), L('Meno di 1 ms', 'Under 1 ms')], 0, L('Breve e utile.', 'Short and useful.')),
            Q('m2', L('A che cosa serve `prefers-reduced-motion`?', 'What is `prefers-reduced-motion` for?'), [L('Rispettare chi chiede meno animazioni', 'Respecting those who ask for less animation'), L('Velocizzare il server', 'Speeding up the server'), L('Nascondere le immagini', 'Hiding images'), L('Aumentare le animazioni', 'Increasing animations')], 0, L('Accessibilità del movimento.', 'Motion accessibility.')),
            Q('m3', L('Quale curva è naturale per un elemento che entra?', 'Which curve feels natural for an entering element?'), ['ease-out', 'linear', 'step-end', 'ease-in'], 0, L('Veloce all’inizio, morbida alla fine.', 'Fast at first, soft at the end.')),
        ], task('css', L('Aggiungi a `.pannello` una `transition` di `transform` di `200ms` con `ease-out`, e una media query `prefers-reduced-motion: reduce` che metta `transition: none`.', 'Add to `.pannello` a `transition` of `transform` over `200ms` with `ease-out`, and a `prefers-reduced-motion: reduce` media query setting `transition: none`.'), ".pannello {\n  transform: translateX(0);\n}\n", [re('\.pannello\s*\{[^}]*transition\s*:\s*[^;]*transform[^;]*200ms[^;]*ease-out', L('Transizione breve', 'Short transition')), re('@media\s*\(\s*prefers-reduced-motion\s*:\s*reduce\s*\)\s*\{[^@]*transition\s*:\s*none', L('Rispetta il movimento ridotto', 'Respects reduced motion'))], '<aside class="pannello">Menu</aside>', ".pannello {\n  transition: transform 200ms ease-out;\n}\n@media (prefers-reduced-motion: reduce) {\n  .pannello { transition: none; }\n}"), [
            dMatch(pMatch(), [[L('Innesco', 'Trigger'), L('Ciò che avvia', 'What starts it')], [L('Feedback', 'Feedback'), L('Ciò che l’utente vede o sente', 'What the user sees or feels')], ['ease-out', L('Elementi che entrano', 'Entering elements')], ['200 ms', L('Durata tipica', 'Typical duration')], ['prefers-reduced-motion', L('Meno animazioni', 'Less animation')]], L('Movimento con uno scopo.', 'Motion with a purpose.')),
            dFlash(pFlash(), [[L('Il movimento può mostrare da dove arriva un elemento', 'Motion can show where an element comes from'), true], [L('Animazioni di 3 secondi rendono l’app più veloce', '3-second animations make the app faster'), false], [L('Una micro-interazione ha un innesco e un feedback', 'A micro-interaction has a trigger and feedback'), true], [L('Le preferenze di movimento ridotto vanno ignorate', 'Reduced-motion preferences should be ignored'), false]], L('Animare con misura.', 'Animating with restraint.')),
        ]),
        lesson('handoff', L('Componenti responsive e passaggio agli sviluppatori', 'Responsive components and developer handoff'), [
            P('Un componente ben fatto si **adatta**: con l’**auto layout** di Figma (l’equivalente del flexbox) cresce con il testo, e le **varianti** raccolgono taglie e stati (piccolo/grande, normale/disabilitato) in un solo componente. Nel codice lo stesso componente usa unità relative e si riorganizza con le media o container query.', 'A well-made component **adapts**: with Figma’s **auto layout** (the equivalent of flexbox) it grows with the text, and **variants** gather sizes and states (small/large, normal/disabled) in one component. In code the same component uses relative units and reorganises with media or container queries.'),
            P('Il **passaggio agli sviluppatori** (handoff) funziona quando il design usa gli stessi **token** del codice, ha nomi coerenti con i componenti reali e documenta gli stati: vuoto, caricamento, errore, testo lunghissimo. Meglio una conversazione presto che tante correzioni tardi.', 'The **developer handoff** works when the design uses the same **tokens** as the code, has names consistent with real components and documents the states: empty, loading, error, very long text. Better an early conversation than many late fixes.'),
        ], [
            Q('ho1', L('Che cosa fa l’auto layout di Figma?', 'What does Figma’s auto layout do?'), [L('Fa crescere il componente con il contenuto', 'Makes the component grow with its content'), L('Esporta il codice', 'Exports code'), L('Sceglie i colori', 'Chooses colours'), L('Anima le pagine', 'Animates pages')], 0, L('Come il flexbox.', 'Like flexbox.')),
            Q('ho2', L('A che cosa servono le varianti?', 'What are variants for?'), [L('Raccogliere taglie e stati di un componente', 'Gathering a component’s sizes and states'), L('Creare pagine', 'Creating pages'), L('Tradurre i testi', 'Translating text'), L('Fare i test', 'Running tests')], 0, L('Un solo componente, molte forme.', 'One component, many forms.')),
            Q('ho3', L('Quale stato si dimentica spesso nel design?', 'Which state is often forgotten in design?'), [L('Il testo lunghissimo o l’errore', 'Very long text or the error'), L('Lo stato normale', 'The normal state'), L('Il logo', 'The logo'), L('Il titolo', 'The title')], 0, L('I casi reali rompono i layout.', 'Real cases break layouts.')),
        ], task('text', L('Elenca con `-` quattro stati da documentare per una lista: `vuoto`, `caricamento`, `errore` e uno a scelta.', 'List with `-` four states to document for a list: `empty`, `loading`, `error` and one of your choice.'), '', [re('^-\s*(vuoto|empty)', L('Stato vuoto', 'Empty state')), re('^-\s*(caricamento|loading)', L('Caricamento', 'Loading')), re('^-\s*(errore|error)', L('Errore', 'Error')), re('(^-.*\n){3}^-\s*\S+', L('Quattro stati', 'Four states'))], '', "- vuoto\n- caricamento\n- errore\n- testo lunghissimo"), [
            dMatch(pMatch(), [['Auto layout', L('Cresce con il contenuto', 'Grows with content')], [L('Varianti', 'Variants'), L('Taglie e stati', 'Sizes and states')], ['Token', L('Valori condivisi con il codice', 'Values shared with code')], ['Handoff', L('Passaggio agli sviluppatori', 'Developer handoff')]], L('Dal design al codice.', 'From design to code.')),
            dFlash(pFlash(), [[L('Design e codice dovrebbero usare gli stessi token', 'Design and code should share tokens'), true], [L('Basta disegnare lo stato normale', 'Drawing the normal state is enough'), false], [L('Le container query adattano un componente al suo spazio', 'Container queries adapt a component to its space'), true], [L('L’handoff si fa solo alla fine', 'Handoff happens only at the end'), false]], L('Componenti pronti.', 'Ready components.')),
        ]),
        lesson('dataviz', L('Grafici e visualizzazione dei dati', 'Charts and data visualisation'), [
            P('Il grafico giusto dipende dalla domanda. **Barre** per confrontare categorie (con l’asse che parte da zero), **linee** per l’andamento nel tempo, **dispersione** per la relazione tra due misure, **torta** solo per poche parti di un intero (2-5 fette). Un singolo numero importante sta meglio in una **card KPI** grande con il confronto («+12% rispetto a ottobre»).', 'The right chart depends on the question. **Bars** to compare categories (with the axis starting at zero), **lines** for trends over time, **scatter** for the relationship between two measures, **pie** only for a few parts of a whole (2-5 slices). A single important number works better in a large **KPI card** with a comparison (“+12% on October”).'),
            Fig(L(fig_uiint_charts('it'), fig_uiint_charts('en')),
                L('Quattro piccoli grafici. Barre grigie con una arancione da notare, per confrontare categorie. Una linea che sale, per l’andamento nel tempo. Punti viola che salgono insieme, per la relazione tra due misure. Una torta con tre fette, per poche parti di un intero.', 'Four small charts. Grey bars with one orange bar to notice, to compare categories. A rising line, for a trend over time. Purple dots rising together, for the relation between two measures. A pie with three slices, for a few parts of a whole.'),
                L('Prima la domanda, poi il grafico.', 'The question first, then the chart.')),
            P('Togli il superfluo: niente 3D, griglie leggere, etichette direttamente sulle linee invece di una legenda lontana. Dai al **colore** un ruolo: un accento per il dato da notare, grigio per il contesto; palette **sequenziali** per valori che crescono, **divergenti** per sopra e sotto una soglia, **categoriali** per gruppi diversi. E non affidarti solo al colore: forme, etichette o pattern aiutano chi distingue male i colori.', 'Remove the superfluous: no 3D, light gridlines, labels directly on the lines instead of a distant legend. Give **colour** a role: an accent for the data point to notice, grey for context; **sequential** palettes for growing values, **diverging** ones for above and below a threshold, **categorical** ones for different groups. And never rely on colour alone: shapes, labels or patterns help people who struggle with colours.'),
        ], [
            Q('d1', L('Quale grafico per mostrare le vendite mese per mese in un anno?', 'Which chart shows sales month by month over a year?'), [L('Una torta', 'A pie chart'), L('Un grafico a linee', 'A line chart'), L('Una dispersione', 'A scatter plot'), L('Una card KPI', 'A KPI card')], 1, L('Andamento nel tempo.', 'Trend over time.')),
            Q('d2', L('Perché l’asse di un grafico a barre parte da zero?', 'Why does a bar chart’s axis start at zero?'), [L('Per avere più colori', 'To have more colours'), L('Perché lo impone il foglio di calcolo', 'Because the spreadsheet requires it'), L('Altrimenti le differenze sembrano più grandi', 'Otherwise differences look bigger'), L('Per risparmiare spazio', 'To save space')], 2, L('La lunghezza della barra è il dato.', 'The bar’s length is the data.')),
            Q('d3', L('Che palette usare per temperature sopra e sotto lo zero?', 'Which palette suits temperatures above and below zero?'), [L('Categoriale', 'Categorical'), L('Sequenziale', 'Sequential'), L('Arcobaleno casuale', 'Random rainbow'), L('Divergente', 'Diverging')], 3, L('Due direzioni da una soglia.', 'Two directions from a threshold.')),
        ], task('text', L('Scegli il grafico per tre domande, una riga ciascuna nel formato `domanda: grafico`: visite giorno per giorno, vendite per regione, relazione tra prezzo e voto.', 'Choose the chart for three questions, one line each in the format `question: chart`: daily visits, sales by region, relationship between price and rating.'), '', [re('(visite|visits)[^\n]*:\s*(linee|linea|line)', L('Linee per le visite', 'Lines for visits')), re('(region)[^\n]*:\s*(barre|barra|bar)', L('Barre per le regioni', 'Bars for regions')), re('(prezzo|price|voto|rating)[^\n]*:\s*(dispersione|scatter)', L('Dispersione per prezzo e voto', 'Scatter for price and rating'))], '', "visite giorno per giorno: linee\nvendite per regione: barre\nprezzo e voto: dispersione"), [
            dMatch(pMatch(), [[L('Barre', 'Bars'), L('Confronto tra categorie', 'Comparing categories')], [L('Linee', 'Lines'), L('Andamento nel tempo', 'Trend over time')], [L('Dispersione', 'Scatter'), L('Relazione tra due misure', 'Relationship between two measures')], [L('Palette divergente', 'Diverging palette'), L('Sopra e sotto una soglia', 'Above and below a threshold')], [L('Card KPI', 'KPI card'), L('Un numero importante', 'One important number')]], L('Il grafico giusto.', 'The right chart.')),
            dFlash(pFlash(), [[L('Le etichette dirette evitano di cercare la legenda', 'Direct labels save looking up the legend'), true], [L('Il 3D rende i grafici più precisi', '3D makes charts more accurate'), false], [L('Una torta funziona con poche fette', 'A pie works with few slices'), true], [L('Il colore da solo basta a distinguere le serie', 'Colour alone is enough to tell series apart'), false]], L('Dati chiari.', 'Clear data.')),
        ]),
        lesson('type-advanced', L('Tipografia avanzata: font variabili e testo fluido', 'Advanced typography: variable fonts and fluid type'), [
            P('Un **font variabile** contiene in un solo file molte varianti lungo degli **assi**: peso (`wght`), larghezza (`wdth`), inclinazione, dimensione ottica (`opsz`). Si scarica meno e si può scegliere qualsiasi valore intermedio, come `font-weight: 550`. Per non lasciare il testo invisibile mentre il web font carica si usa `font-display: swap` nella regola `@font-face`.', 'A **variable font** holds many variants in a single file along **axes**: weight (`wght`), width (`wdth`), slant, optical size (`opsz`). It downloads less and lets you pick any value in between, such as `font-weight: 550`. To avoid invisible text while the web font loads, use `font-display: swap` in the `@font-face` rule.'),
            P('Il **testo fluido** cresce con lo schermo senza salti grazie a `clamp(minimo, preferito, massimo)`: per un titolo `font-size: clamp(1.5rem, 1rem + 2vw, 2.5rem)`. Metti sempre una parte in `rem` nel valore preferito, così lo zoom del testo dell’utente continua a funzionare. Per gli **abbinamenti** bastano due famiglie con ruoli chiari (titoli e testo) o una sola famiglia con più pesi; per le cifre in tabella usa `font-variant-numeric: tabular-nums`.', '**Fluid type** grows with the screen without jumps thanks to `clamp(minimum, preferred, maximum)`: for a heading `font-size: clamp(1.5rem, 1rem + 2vw, 2.5rem)`. Always include a `rem` part in the preferred value, so the user’s text zoom keeps working. For **pairings** two families with clear roles (headings and body) are enough, or a single family with several weights; for digits in tables use `font-variant-numeric: tabular-nums`.'),
            Fig(L(fig_uiint_clamp('it'), fig_uiint_clamp('en')),
                L('Un grafico della dimensione del titolo rispetto alla larghezza della finestra. Fino a 400 pixel resta al minimo di 28 pixel, poi sale in linea retta seguendo 1rem + 3vw, e da circa 1067 pixel si ferma al massimo di 48 pixel.', 'A chart of the heading size against the window width. Up to 400 pixels it stays at the 28-pixel minimum, then rises in a straight line following 1rem + 3vw, and from about 1067 pixels it stops at the 48-pixel maximum.'),
                L('`clamp()` fa crescere il testo in modo fluido, ma solo tra due limiti.', '`clamp()` makes text grow smoothly, but only between two limits.')),
            C('@font-face {
  font-family: "Inter";
  src: url(inter-variabile.woff2) format("woff2");
  font-weight: 100 900;
  font-display: swap;
}
h1 { font-size: clamp(1.75rem, 1rem + 3vw, 3rem); font-weight: 650; }', 'css', L('<h1>Un titolo che cresce con lo schermo</h1>', '<h1>A heading that grows with the screen</h1>')),
        ], [
            Q('t1', L('Che cosa contiene un font variabile?', 'What does a variable font contain?'), [L('Un solo peso fisso', 'A single fixed weight'), L('Tante varianti lungo degli assi, in un file', 'Many variants along axes, in one file'), L('Solo caratteri decorativi', 'Only decorative characters'), L('Un’immagine del testo', 'An image of the text')], 1, L('Peso, larghezza, dimensione ottica…', 'Weight, width, optical size…')),
            Q('t2', L('Che cosa fa `clamp(1.5rem, 1rem + 2vw, 2.5rem)`?', 'What does `clamp(1.5rem, 1rem + 2vw, 2.5rem)` do?'), [L('Fissa il testo a 2vw', 'Fixes the text at 2vw'), L('Nasconde il testo su mobile', 'Hides the text on mobile'), L('Fa crescere il testo con lo schermo tra un minimo e un massimo', 'Grows the text with the screen between a minimum and a maximum'), L('Converte i rem in pixel', 'Converts rem to pixels')], 2, L('Fluido ma con limiti.', 'Fluid but bounded.')),
            Q('t3', L('Perché mettere una parte in `rem` nel valore fluido?', 'Why include a `rem` part in the fluid value?'), [L('Per renderlo più corto', 'To make it shorter'), L('È obbligatorio in CSS', 'CSS requires it'), L('Serve ai font variabili', 'Variable fonts need it'), L('Perché lo zoom del testo dell’utente funzioni ancora', 'So the user’s text zoom still works')], 3, L('Solo vw ignorerebbe le preferenze.', 'Pure vw would ignore preferences.')),
        ], task('css', L('Rendi fluido il titolo `h1` con `font-size: clamp(...)`, minimo `1.75rem` e massimo `3rem`, e dagli un peso intermedio con `font-weight: 650`.', 'Make the `h1` heading fluid with `font-size: clamp(...)`, minimum `1.75rem` and maximum `3rem`, and give it an in-between weight with `font-weight: 650`.'), "h1 {\n  font-size: 2rem;\n}\n", [re('h1\s*\{[^}]*font-size\s*:\s*clamp\(\s*1\.75rem\s*,[^;]*,\s*3rem\s*\)', L('Titolo fluido', 'Fluid heading')), css('h1', 'font-weight', '650', L('Peso 650', 'Weight 650'))], '<h1>Fucina</h1>', "h1 {\n  font-size: clamp(1.75rem, 1rem + 3vw, 3rem);\n  font-weight: 650;\n}"), [
            dMatch(pMatch(), [['wght', L('Peso', 'Weight')], ['wdth', L('Larghezza', 'Width')], ['opsz', L('Dimensione ottica', 'Optical size')], ['clamp()', L('Valore tra minimo e massimo', 'Value between minimum and maximum')], ['font-display: swap', L('Testo visibile mentre il font carica', 'Text visible while the font loads')]], L('Tipografia moderna.', 'Modern typography.')),
            dBlanks(pFill(), "h1 {\n  font-size: ___(1.75rem, 1rem + 3vw, 3rem);\n}\n@font-face {\n  font-display: ___;\n}", ['clamp', 'swap'], ['minmax', 'hidden'], L('Testo fluido e caricamento senza testo invisibile.', 'Fluid text and loading without invisible text.')),
        ]),
    ],
];
