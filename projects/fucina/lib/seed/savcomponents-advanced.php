<?php
/** SavComponents advanced: ::part() and component variables, shapes and Tilt, live data and server search, charts, drag and drop, frameworks and server rendering, hosting, upgrades and accessibility. */

/** A card's light DOM and shadow DOM, and which rules reach what: your content yes, named parts yes, tokens inherited, anything else no. */
if (!function_exists('fig_savc_shadow')) {
    function fig_savc_shadow(string $yours, string $part, string $tokens, string $none, string $noPart): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 330" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs>
<marker id="g" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#0B7F5C"/></marker>
<marker id="p" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#7048E8"/></marker>
<marker id="o" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#E8590C"/></marker>
</defs>
<rect x="16" y="16" width="340" height="298" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="32" y="56" width="308" height="50" rx="10" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/>
<rect x="32" y="122" width="308" height="176" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2" stroke-dasharray="7 5"/>
<rect x="48" y="160" width="276" height="36" rx="8" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/>
<rect x="48" y="204" width="276" height="36" rx="8" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/>
<rect x="48" y="248" width="276" height="36" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13">
<text x="32" y="42" font-weight="700">&lt;sav-card&gt;</text>
<text x="48" y="86">&lt;h3 slot="header"&gt;</text>
<text x="250" y="86" font-weight="700" fill="#0B7F5C">light DOM</text>
<text x="48" y="146" font-weight="700" fill="#5A35D6">shadow DOM</text>
<text x="64" y="183" fill="#5A35D6" font-weight="700">part="header"</text>
<text x="64" y="227" fill="#5A35D6" font-weight="700">part="body"</text>
<text x="64" y="271" fill="#55527A">&lt;div&gt;</text>
</g>
<text x="120" y="271" font-size="13" fill="#55527A">{$noPart}</text>
<path d="M372 81 H346" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#g)"/>
<path d="M372 178 H330" fill="none" stroke="#7048E8" stroke-width="2" marker-end="url(#p)"/>
<path d="M372 222 H330" fill="none" stroke="#E8590C" stroke-width="2" stroke-dasharray="5 4" marker-end="url(#o)"/>
<path d="M372 266 H350" fill="none" stroke="#F03E82" stroke-width="2"/>
<circle cx="344" cy="266" r="9" fill="#F03E82"/><path d="M340 262 l8 8 M348 262 l-8 8" stroke="#FFFFFF" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">
<text x="384" y="80" fill="#0B7F5C">sav-card h3 {…}</text>
<text x="384" y="177" fill="#5A35D6">sav-card::part(header)</text>
<text x="384" y="221" fill="#B84508">--sav-color-primary</text>
<text x="384" y="265" fill="#B0245A">sav-card div {…}</text>
</g>
<g font-size="13">
<text x="384" y="99">{$yours}</text>
<text x="384" y="196">{$part}</text>
<text x="384" y="240">{$tokens}</text>
<text x="384" y="284">{$none}</text>
</g>
</svg>
SVG;
    }
}

/** Tilt: the same lift at the corners makes a short element lean more than a long one; --sav-tilt gives one angle to all. */
if (!function_exists('fig_savc_tilt')) {
    function fig_savc_tilt(string $long, string $shortNote, string $longNote, string $same, string $exaggerated): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 310" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="16" y="30" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">--sav-tilt-lift: -3px</text>
<rect x="60" y="70" width="100" height="40" fill="#2F8CF0" transform="rotate(-6.84 110 90)"/>
<text x="110" y="95" text-anchor="middle" font-weight="700" fill="#FFFFFF">OK</text>
<path d="M62.7 115.8 H178" stroke="#8A87B0" stroke-width="1.5" stroke-dasharray="4 3"/>
<path d="M172 115.8 V104" stroke="#E8590C" stroke-width="2"/>
<text x="180" y="114" font-size="13" font-weight="700" fill="#B84508">3px</text>
<rect x="280" y="70" width="300" height="40" fill="#2F8CF0" transform="rotate(-2.29 430 90)"/>
<text x="430" y="95" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$long}</text>
<path d="M280.9 116 H598" stroke="#8A87B0" stroke-width="1.5" stroke-dasharray="4 3"/>
<path d="M592 116 V104" stroke="#E8590C" stroke-width="2"/>
<text x="600" y="114" font-size="13" font-weight="700" fill="#B84508">3px</text>
<text x="60" y="152" font-size="13">{$shortNote}</text>
<text x="280" y="152" font-size="13">{$longNote}</text>
<text x="16" y="198" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">--sav-tilt: -2deg</text>
<text x="180" y="198" font-size="13">{$same}</text>
<rect x="60" y="216" width="100" height="40" fill="#0B7F5C" transform="rotate(-4 110 236)"/>
<text x="110" y="241" text-anchor="middle" font-weight="700" fill="#FFFFFF">OK</text>
<rect x="280" y="216" width="300" height="40" fill="#0B7F5C" transform="rotate(-4 430 236)"/>
<text x="430" y="241" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$long}</text>
<text x="16" y="300" font-size="13" fill="#55527A">{$exaggerated}</text>
</svg>
SVG;
    }
}

/** Two searches in flight: the answer to the older query arrives last and must be ignored. */
if (!function_exists('fig_savc_stale')) {
    function fig_savc_stale(string $page, string $shown, string $late1, string $late2, string $time): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs>
<marker id="b" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#2F8CF0"/></marker>
<marker id="g" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#0B7F5C"/></marker>
<marker id="k" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#8A87B0"/></marker>
</defs>
<text x="16" y="85" font-weight="700">{$page}</text>
<text x="16" y="195" font-weight="700">server</text>
<path d="M90 80 H620" stroke="#1E1C52" stroke-width="2"/>
<path d="M90 190 H620" stroke="#1E1C52" stroke-width="2"/>
<rect x="100" y="28" width="56" height="28" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="128" y="47" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#1B66BC">Ud</text>
<rect x="196" y="28" width="64" height="28" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="228" y="47" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#0B7F5C">Udi</text>
<path d="M128 82 L190 184" fill="none" stroke="#2F8CF0" stroke-width="2" marker-end="url(#b)"/>
<path d="M228 82 L280 184" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#g)"/>
<path d="M300 188 L356 86" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#g)"/>
<path d="M420 188 L486 86" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4" marker-end="url(#k)"/>
<circle cx="360" cy="80" r="10" fill="#0B7F5C"/><path d="M355 80 l4 4 l7 -8" fill="none" stroke="#FFFFFF" stroke-width="2"/>
<circle cx="490" cy="80" r="10" fill="#F03E82"/><path d="M486 76 l8 8 M494 76 l-8 8" stroke="#FFFFFF" stroke-width="2"/>
<text x="348" y="70" text-anchor="end" font-size="13" font-weight="700" fill="#0B7F5C">{$shown}</text>
<text x="470" y="36" font-size="13" font-weight="700" fill="#B0245A">{$late1}</text>
<text x="470" y="54" font-size="13" fill="#B0245A">{$late2}</text>
<text x="16" y="250" font-family="ui-monospace, Menlo, monospace" font-size="13">if (q !== cerca.value.trim()) return;</text>
<text x="620" y="215" text-anchor="end" font-size="13" fill="#55527A">{$time} →</text>
</svg>
SVG;
    }
}

/** Server rendering: the tags leave the server as HTML and come alive only when sav.js runs in the browser. */
if (!function_exists('fig_savc_ssr')) {
    function fig_savc_ssr(string $t1, string $t2, string $t3, string $n1, string $n2, string $n3, string $here): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#55527A"/></marker></defs>
<rect x="16" y="16" width="184" height="170" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="228" y="16" width="184" height="170" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="440" y="16" width="184" height="170" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<path d="M202 100 H222" stroke="#55527A" stroke-width="2" marker-end="url(#a)"/>
<path d="M414 100 H434" stroke="#55527A" stroke-width="2" marker-end="url(#a)"/>
<text x="32" y="44" font-weight="700">{$t1}</text>
<text x="244" y="44" font-weight="700" fill="#B84508">{$t2}</text>
<text x="456" y="44" font-weight="700" fill="#0B7F5C">{$t3}</text>
<g font-family="ui-monospace, Menlo, monospace" font-size="13">
<text x="32" y="90">&lt;sav-button&gt;</text>
<text x="44" y="110">…</text>
<text x="32" y="130">&lt;/sav-button&gt;</text>
<text x="32" y="168" font-weight="700" fill="#B0245A">Sav: undefined</text>
<text x="456" y="168" font-weight="700" fill="#0B7F5C">Sav ✓</text>
</g>
<rect x="260" y="78" width="120" height="38" rx="8" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/>
<rect x="282" y="93" width="76" height="8" rx="4" fill="#B9B6D3"/>
<text x="244" y="150" font-size="13">{$n2}</text>
<rect x="472" y="78" width="120" height="38" rx="8" fill="#2F8CF0"/>
<rect x="494" y="93" width="76" height="8" rx="4" fill="#FFFFFF"/>
<text x="456" y="146" font-size="13">{$n3}</text>
<text x="32" y="150" font-size="13">{$n1}</text>
<rect x="440" y="198" width="184" height="40" rx="10" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/>
<text x="532" y="223" text-anchor="middle" font-size="13" font-weight="700" fill="#0B7F5C">{$here}</text>
<path d="M532 188 V196" stroke="#0B7F5C" stroke-width="2"/>
</svg>
SVG;
    }
}

/** A self-hosted copy: the asset sets are looked for next to the version folder, unless data-icon-base points elsewhere. */
if (!function_exists('fig_savc_host')) {
    function fig_savc_host(string $server, string $next1, string $next2, string $instead, string $fallback1, string $fallback2): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs>
<marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#0B7F5C"/></marker>
<marker id="p" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#7048E8"/></marker>
</defs>
<rect x="16" y="16" width="330" height="268" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="32" y="44" font-weight="700" fill="#1B66BC">{$server}</text>
<g font-family="ui-monospace, Menlo, monospace" font-size="13">
<text x="32" y="74" font-weight="700">/vendor/sav/</text>
<rect x="44" y="86" width="286" height="58" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="56" y="108" font-weight="700">v/1.0.0/</text>
<text x="68" y="130">sav.css  sav.js</text>
<rect x="44" y="156" width="150" height="28" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="56" y="175" font-weight="700" fill="#0B7F5C">icons/1.0.0/</text>
<rect x="44" y="192" width="150" height="28" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="56" y="211" font-weight="700" fill="#0B7F5C">flags/1.0.0/</text>
<rect x="44" y="228" width="150" height="28" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="56" y="247" font-weight="700" fill="#0B7F5C">maps/1.0.0/</text>
</g>
<path d="M200 125 C250 140 250 190 200 205" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#a)"/>
<text x="232" y="196" font-size="13" fill="#0B7F5C" font-weight="700">{$next1}</text>
<text x="232" y="214" font-size="13" fill="#0B7F5C">{$next2}</text>
<rect x="392" y="30" width="232" height="96" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="408" y="58" font-weight="700" fill="#5A35D6">cdn.example.com</text>
<text x="408" y="82" font-family="ui-monospace, Menlo, monospace" font-size="13">/sav/icons/1.0.0/</text>
<text x="408" y="108" font-size="13" fill="#5A35D6">{$instead}</text>
<path d="M196 170 C300 170 320 80 386 78" fill="none" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4" marker-end="url(#p)"/>
<text x="358" y="152" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#5A35D6">data-icon-base</text>
<rect x="392" y="170" width="232" height="96" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="408" y="198" font-weight="700">savcomponents.com</text>
<text x="408" y="222" font-family="ui-monospace, Menlo, monospace" font-size="13">/icons/1.0.0/</text>
<text x="408" y="244" font-size="13" fill="#55527A">{$fallback1}</text>
<text x="408" y="260" font-size="13" fill="#55527A">{$fallback2}</text>
</svg>
SVG;
    }
}

$scaPage = '<sav-dialog class="ampio" label="Dettagli ordine" open>Tre articoli in arrivo.</sav-dialog>
<sav-card>
  <h3 slot="header">Ordine 1042</h3>
  <p>Consegna prevista domani.</p>
</sav-card>
<sav-avatar label="Ada Lovelace"></sav-avatar>';

return [
    'id' => 'savcomponents-advanced', 'family' => 'savcomponents', 'after' => 'savcomponents-intermediate', 'categories' => ['frontend', 'design'],
    'title' => L('SavComponents avanzato', 'Advanced SavComponents'), 'short' => L('SavComponents avanzato', 'Advanced SavComponents'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Spingere la libreria oltre le basi: stile profondo con ::part(), forme e Tilt su misura, dati dal vivo e ricerca dal server, grafici, trascinamento, framework e rendering sul server, ospitare, aggiornare e accessibilità.', 'Taking the library beyond the basics: deep styling with ::part(), shapes and a custom Tilt, live data and server search, charts, drag and drop, frameworks and server rendering, hosting, upgrading and accessibility.'),
    'lessons' => [
        lesson('parts-variables', L('Stile profondo: ::part() e variabili', 'Deep styling: ::part() and variables'), [
            P('Ogni componente disegna il suo interno in uno **shadow DOM**: i tuoi selettori non ci entrano, quindi `sav-input input { … }` non trova niente. Il contenuto che scrivi tu tra i tag invece resta nella pagina: `sav-card h3 { … }` funziona. Per cambiare l’interno hai tre porte, dalla più stabile alla più specifica: i **token** `--sav-*`, le **variabili del componente** e **`::part()`**.', 'Every component draws its inside in a **shadow DOM**: your selectors cannot get in, so `sav-input input { … }` finds nothing. The content you write between the tags stays in the page instead: `sav-card h3 { … }` works. To change the inside you have three doors, from the most stable to the most specific: the `--sav-*` **tokens**, the **component variables** and **`::part()`**.'),
            Fig(L(fig_savc_shadow('il tuo contenuto: raggiunto', 'una parte con nome: raggiunta', 'i token: ereditati anche dentro', 'nessuna porta: non entra', 'senza part'), fig_savc_shadow('your content: reached', 'a named part: reached', 'tokens: inherited inside too', 'no door: it cannot get in', 'no part')),
                L('Una sav-card: in alto, nel light DOM, l’h3 con slot header scritto da te; sotto, tratteggiato, lo shadow DOM con part header, part body e un div senza part. A destra quattro regole: sav-card h3 raggiunge l’h3, sav-card::part(header) raggiunge la parte, il token --sav-color-primary entra per eredità, sav-card div si ferma al bordo.', 'A sav-card: at the top, in the light DOM, the h3 with slot header that you wrote; below, dashed, the shadow DOM with part header, part body and a div with no part. On the right four rules: sav-card h3 reaches the h3, sav-card::part(header) reaches the part, the --sav-color-primary token gets in by inheritance, sav-card div stops at the border.'),
                L('Il bordo tratteggiato lascia passare solo token e parti con nome.', 'The dashed border lets through only tokens and named parts.')),
            P('Molti componenti leggono variabili CSS proprie: `--width` per `<sav-dialog>` (predefinita 32rem), `--size` per `<sav-drawer>` e `<sav-avatar>`, `--height` per `<sav-progress-bar>`, `--max-height` per `<sav-code-block>`, `--sav-card-padding` per la card. Si impostano come qualsiasi proprietà, su un elemento o su una classe.', 'Many components read CSS variables of their own: `--width` for `<sav-dialog>` (default 32rem), `--size` for `<sav-drawer>` and `<sav-avatar>`, `--height` for `<sav-progress-bar>`, `--max-height` for `<sav-code-block>`, `--sav-card-padding` for the card. You set them like any property, on an element or on a class.'),
            C('sav-dialog.ampio { --width: 48rem; }
sav-avatar { --size: 4rem; }
sav-progress-bar { --height: 1rem; }

/* le parti: pezzi interni con un nome */
sav-card::part(header) { text-transform: uppercase; letter-spacing: .05em; }
sav-input::part(label) { color: var(--sav-color-text-muted); }
sav-button::part(label):hover { text-decoration: underline; }', 'css', L('<sav-card>
  <h3 slot="header">Ordine 1042</h3>
  <p>Consegna prevista domani.</p>
</sav-card>
<sav-input label="Email" placeholder="ada@example.com"></sav-input>
<p><sav-avatar label="Ada Lovelace"></sav-avatar></p>
<sav-progress-bar value="60"></sav-progress-bar>
<p><sav-button>Conferma</sav-button></p>', '<sav-card>
  <h3 slot="header">Order 1042</h3>
  <p>Delivery expected tomorrow.</p>
</sav-card>
<sav-input label="Email" placeholder="ada@example.com"></sav-input>
<p><sav-avatar label="Ada Lovelace"></sav-avatar></p>
<sav-progress-bar value="60"></sav-progress-bar>
<p><sav-button>Confirm</sav-button></p>')),
            P('Le **parti** sono i pezzi interni a cui il componente ha dato un nome, elencati sulla pagina di ogni componente: `base`, `label` e `spinner` del pulsante; `header`, `body` e `footer` della card; `label`, `input` e `help-text` dei campi. Con `::part(nome)` li raggiungi, anche con `:hover` o `:focus-visible`, ma non puoi scendere oltre: `::part(base) span` non vale.', '**Parts** are the inner pieces the component has named, listed on the page of every component: `base`, `label` and `spinner` of the button; `header`, `body` and `footer` of the card; `label`, `input` and `help-text` of fields. With `::part(name)` you reach them, also with `:hover` or `:focus-visible`, but you cannot go deeper: `::part(base) span` is not valid.'),
            Tip('Parti prima dai token: la promessa di stabilità della libreria riguarda tag, attributi e token. `::part()` ti lega di più alla struttura interna, quindi usalo per gli ultimi ritocchi.', 'Start from tokens: the library’s stability promise covers tags, attributes and tokens. `::part()` ties you more to the inner structure, so keep it for the final touches.'),
        ], [
            Q('pv1', L('Quale di queste regole non raggiunge niente?', 'Which of these rules reaches nothing?'), ['sav-card h3 { color: red }', 'sav-card::part(header) { color: red }', 'sav-input input { color: red }', ':root { --sav-radius: 0 }'], 2, L('L’`<input>` vive nello shadow DOM del campo: si raggiunge con `::part(input)`.', 'The `<input>` lives in the field’s shadow DOM: you reach it with `::part(input)`.')),
            Q('pv2', L('Come allarghi un solo dialogo?', 'How do you widen a single dialog?'), ['width="48rem"', 'size="xl"', 'style="--width: 48rem"', 'style="max-width: 48rem"'], 2, L('Il dialogo legge la variabile `--width`.', 'The dialog reads the `--width` variable.')),
            Q('pv3', L('Dove trovi i nomi delle parti di un componente?', 'Where do you find the names of a component’s parts?'), [L('Negli attributi `part` che scrivi tu nell’HTML', 'In the `part` attributes you write in the HTML'), L('Nel file `sav.css`', 'In the `sav.css` file'), L('Da nessuna parte: si indovinano', 'Nowhere: you guess them'), L('Sulla pagina del componente nella documentazione', 'On the component’s page in the documentation')], 3, L('Ogni pagina ha la sezione delle parti CSS.', 'Every page has a CSS parts section.')),
        ], task('css', L('Allarga il dialogo `.ampio` impostando `--width` a `48rem`, scrivi in maiuscolo la parte `header` di ogni `sav-card` e porta `--size` di `sav-avatar` a `4rem`.', 'Widen the `.ampio` dialog by setting `--width` to `48rem`, write the `header` part of every `sav-card` in upper case and set `sav-avatar`’s `--size` to `4rem`.'), '', [re('sav-dialog\.ampio\s*\{[^}]*--width\s*:\s*48rem', L('Dialogo più largo', 'Wider dialog')), re('sav-card\s*::part\(\s*header\s*\)\s*\{[^}]*text-transform\s*:\s*uppercase', L('Intestazione della card in maiuscolo', 'Card header in upper case')), re('sav-avatar\s*\{[^}]*--size\s*:\s*4rem', L('Avatar più grande', 'Larger avatar'))], $scaPage, "sav-dialog.ampio { --width: 48rem; }\nsav-card::part(header) { text-transform: uppercase; }\nsav-avatar { --size: 4rem; }"), [
            dMatch(pMatch(), [['--sav-color-primary', L('Token: vale per tutti', 'Token: applies to all')], ['--width', L('Variabile del dialogo', 'Dialog variable')], ['::part(header)', L('Un pezzo interno con nome', 'A named inner piece')], ['sav-card h3', L('Contenuto tuo tra i tag', 'Your own content between the tags')], ['shadow DOM', L('L’interno protetto del componente', 'The component’s protected inside')]], L('Le porte per lo stile.', 'The doors to styling.')),
            dBug(pBug(), ['sav-input input {', '  font-family: var(--sav-font-mono);', '}'], 0, 'sav-input::part(input) {', L('L’input interno si raggiunge solo con la sua parte.', 'The inner input is only reached through its part.')),
        ]),
        lesson('shapes-tilt', L('Forme e Tilt su misura', 'Shapes and a custom Tilt'), [
            P('L’attributo **`shape`** (`tilt`, `squared` o `rounded`) funziona su qualsiasi componente e cambia solo quello, qualunque sia lo stile della pagina. Messo su un contenitore qualsiasi, per esempio un `<div shape="rounded">`, vale per tutto quello che c’è dentro.', 'The **`shape`** attribute (`tilt`, `squared` or `rounded`) works on any component and changes only that one, whatever the page style. Put on any container, for example a `<div shape="rounded">`, it applies to everything inside.'),
            P('Nello stile **Tilt** lo sfondo pende mentre il testo resta dritto. Per impostazione ogni elemento alza gli angoli della stessa misura, **`--sav-tilt-lift: -3px`**: per questo un pulsante lungo pende meno di uno corto. Se vuoi lo stesso angolo per tutti, imposta **`--sav-tilt`** (per esempio `-2deg`); con `0deg` gli elementi tornano dritti.', 'In the **Tilt** style the background leans while the text stays upright. By default every element lifts its corners by the same amount, **`--sav-tilt-lift: -3px`**: that is why a long button leans less than a short one. If you want the same angle for everything, set **`--sav-tilt`** (for example `-2deg`); with `0deg` elements stand straight again.'),
            Fig(L(fig_savc_tilt('Iscriviti alla newsletter', 'base corta: pende di più', 'base lunga, stessa alzata: pende di meno', 'stesso angolo per tutti', 'Inclinazioni esagerate per vederle meglio.'), fig_savc_tilt('Subscribe to the newsletter', 'short base: leans more', 'long base, same lift: leans less', 'the same angle for all', 'Leans exaggerated so you can see them.')),
                L('In alto, con --sav-tilt-lift: -3px, un pulsante corto OK e uno lungo hanno lo stesso dislivello di 3px tra gli angoli, quindi il corto è molto più inclinato. In basso, con --sav-tilt: -2deg, i due pulsanti hanno lo stesso angolo.', 'At the top, with --sav-tilt-lift: -3px, a short OK button and a long one have the same 3px rise between their corners, so the short one leans far more. At the bottom, with --sav-tilt: -2deg, both buttons have the same angle.'),
                L('Alzata fissa: l’angolo dipende dalla larghezza. Angolo fisso: è uguale per tutti.', 'Fixed lift: the angle depends on the width. Fixed angle: it is the same for all.')),
            C(L('<html class="sav-style-tilt">
…
<style>
  .sav-style-tilt { --sav-tilt-lift: -5px; }   /* più inclinato */
  .uniforme { --sav-tilt: -2deg; }              /* stesso angolo per tutti */
  .sav-style-mio {                              /* uno stile tuo */
    --sav-radius: 20px;
    --sav-radius-lg: 28px;
    --sav-border-width: 2px;
  }
</style>

<div shape="rounded">
  <sav-button>Arrotondato</sav-button>
  <sav-card>Anche io</sav-card>
</div>
<sav-button shape="tilt">Solo io pendo</sav-button>', '<html class="sav-style-tilt">
…
<style>
  .sav-style-tilt { --sav-tilt-lift: -5px; }   /* leans more */  
  .uniforme { --sav-tilt: -2deg; }              /* same angle for all */     
  .sav-style-mio {                              /* a style of your own */
    --sav-radius: 20px;
    --sav-radius-lg: 28px;
    --sav-border-width: 2px;
  }
</style>

<div shape="rounded">
  <sav-button>Rounded</sav-button>
  <sav-card>Me too</sav-card>
</div>
<sav-button shape="tilt">Only I lean</sav-button>'), 'html'),
            P('Uno stile, in fondo, è **una classe con qualche token**: per creare un aspetto tuo puoi scrivere una classe che cambia raggi, bordi e ombre, e combinarla con qualsiasi colore e con la modalità scura, proprio come le cinque classi `sav-style-*`.', 'A style, after all, is **a class with a few tokens**: to create a look of your own you can write a class that changes radii, borders and shadows, and combine it with any colour and with dark mode, just like the five `sav-style-*` classes.'),
        ], [
            Q('st1', L('Che cosa fa `shape="rounded"` su un `<div>`?', 'What does `shape="rounded"` do on a `<div>`?'), [L('Arrotonda tutti i componenti al suo interno', 'Rounds every component inside it'), L('Arrotonda solo gli angoli del div', 'Rounds only the div’s corners'), L('Niente: vale solo sui tag sav-', 'Nothing: it only works on sav- tags'), L('Cambia lo stile di tutta la pagina', 'Changes the style of the whole page')], 0, L('Su un contenitore vale per tutto ciò che contiene.', 'On a container it applies to everything inside.')),
            Q('st2', L('Nello stile Tilt, perché un pulsante lungo pende meno di uno corto?', 'In the Tilt style, why does a long button lean less than a short one?'), [L('Perché i pulsanti lunghi usano `shape="squared"`', 'Because long buttons use `shape="squared"`'), L('Perché ogni elemento alza gli angoli della stessa misura', 'Because every element lifts its corners by the same amount'), L('Per un errore di arrotondamento del browser', 'Because of a browser rounding error'), L('Perché l’angolo dipende dal numero di lettere', 'Because the angle depends on the number of letters')], 1, L('Stesso sollevamento su una base più lunga: angolo più piccolo.', 'Same lift over a longer base: a smaller angle.')),
            Q('st3', L('Come dai a tutti gli elementi lo stesso angolo di −2°?', 'How do you give every element the same −2° angle?'), ['transform: rotate(-2deg)', 'shape="tilt-2"', '--sav-tilt-lift: -2deg', '--sav-tilt: -2deg'], 3, L('`--sav-tilt-lift` è una lunghezza, non un angolo.', '`--sav-tilt-lift` is a length, not an angle.')),
        ], task('html', L('Metti `shape="rounded"` su un `<div>` che contiene un `<sav-button>` e una `<sav-card>`; fuori dal div aggiungi un `<sav-button>` con `shape="tilt"`.', 'Put `shape="rounded"` on a `<div>` holding a `<sav-button>` and a `<sav-card>`; outside the div add a `<sav-button>` with `shape="tilt"`.'), '', [has('div[shape="rounded"] sav-button', L('Pulsante arrotondato nel div', 'Rounded button in the div')), has('div[shape="rounded"] sav-card', L('Card arrotondata nel div', 'Rounded card in the div')), has('sav-button[shape="tilt"]:not([shape="rounded"] *)', L('Un pulsante inclinato da solo', 'A tilted button on its own'))], '', "<div shape=\"rounded\">\n  <sav-button>Arrotondato</sav-button>\n  <sav-card>Anche io</sav-card>\n</div>\n<sav-button shape=\"tilt\">Solo io pendo</sav-button>"), [
            dMatch(pMatch(), [['shape="tilt"', L('Inclinato, qualunque stile', 'Leaning, whatever the style')], ['shape="squared"', L('Angoli vivi', 'Sharp corners')], ['shape="rounded"', L('Angoli molto arrotondati', 'Very rounded corners')], ['--sav-tilt-lift', L('Sollevamento degli angoli', 'Lift of the corners')], ['--sav-tilt', L('Angolo fisso per tutti', 'Fixed angle for all')]], L('Forme e inclinazione.', 'Shapes and lean.')),
            dFlash(pFlash(), [[L('shape funziona anche su un div qualsiasi', 'shape also works on any div'), true], [L('--sav-tilt-lift si scrive in gradi', '--sav-tilt-lift is written in degrees'), false], [L('Uno stile è una classe con qualche token', 'A style is a class with a few tokens'), true], [L('shape="tilt" funziona solo nella pagina in stile Tilt', 'shape="tilt" only works in a Tilt-styled page'), false]], L('Forme indipendenti dallo stile.', 'Shapes independent of the style.')),
        ]),
        lesson('live-data', L('Dati dal vivo: proprietà e ricerca dal server', 'Live data: properties and server search'), [
            P('Gli attributi sono sempre testo. Per i dati complessi usa le **proprietà**: `grafico.data = { labels, series }` passa un oggetto senza trasformarlo in JSON; `select.value` è una stringa, o un **array** con `multiple`; il `value` di un dropdown con `multiple` è l’array delle voci spuntate. Le proprietà si leggono e si scrivono quando il componente è definito, quindi aspetta `customElements.whenDefined()` se lo script potrebbe arrivare prima.', 'Attributes are always text. For complex data use **properties**: `chart.data = { labels, series }` passes an object without turning it into JSON; `select.value` is a string, or an **array** with `multiple`; the `value` of a dropdown with `multiple` is the array of ticked items. Properties are read and written once the component is defined, so wait for `customElements.whenDefined()` if the script might come first.'),
            C('await customElements.whenDefined("sav-chart-line");
const grafico = document.querySelector("#visite");
grafico.data = { labels: ["Lun", "Mar", "Mer"], series: [{ name: "Sito", values: [120, 150, 90] }] };

const citta = document.querySelector("sav-select[multiple]");
citta.value = ["ud", "ts"];         // scrive
console.log(citta.value);           // ["ud", "ts"]', 'js', L('<sav-chart-line id="visite" label="Visite"></sav-chart-line>
<sav-select name="citta" label="Città" multiple>
  <option value="ud">Udine</option>
  <option value="ts">Trieste</option>
  <option value="pn">Pordenone</option>
</sav-select>
<pre id="out"></pre>
<script type="module">
  await customElements.whenDefined("sav-chart-line");
  const grafico = document.querySelector("#visite");
  grafico.data = { labels: ["Lun", "Mar", "Mer"], series: [{ name: "Sito", values: [120, 150, 90] }] };
  const citta = document.querySelector("sav-select[multiple]");
  citta.value = ["ud", "ts"];
  document.querySelector("#out").textContent = "citta.value = " + JSON.stringify(citta.value);
</script>', '<sav-chart-line id="visite" label="Visits"></sav-chart-line>
<sav-select name="citta" label="City" multiple>
  <option value="ud">Udine</option>
  <option value="ts">Trieste</option>
  <option value="pn">Pordenone</option>
</sav-select>
<pre id="out"></pre>
<script type="module">
  await customElements.whenDefined("sav-chart-line");
  const grafico = document.querySelector("#visite");
  grafico.data = { labels: ["Mon", "Tue", "Wed"], series: [{ name: "Site", values: [120, 150, 90] }] };
  const citta = document.querySelector("sav-select[multiple]");
  citta.value = ["ud", "ts"];
  document.querySelector("#out").textContent = "citta.value = " + JSON.stringify(citta.value);
</script>')),
            P('Per una **ricerca sul server**, `<sav-search>` emette **`sav-query`** mentre scrivi (con `detail.query`, già ritardato di `debounce` millisecondi, 120 se non lo cambi): chiedi i risultati e assegnali a **`.suggestions`**, come stringhe o oggetti `{ label, value, description, href }`. Con `no-filter` il campo li mostra così come arrivano; `sav-pick` dice quale è stato scelto (si può annullare) e `sav-search` arriva con Invio senza scegliere.', 'For a **server search**, `<sav-search>` emits **`sav-query`** as you type (with `detail.query`, already delayed by `debounce` milliseconds, 120 unless you change it): fetch the results and assign them to **`.suggestions`**, as strings or `{ label, value, description, href }` objects. With `no-filter` the field shows them as they arrive; `sav-pick` says which one was chosen (it can be cancelled) and `sav-search` arrives on Enter without a choice.'),
            Fig(L(fig_savc_stale('pagina', 'risultati di Udi: mostrati', 'Ud arriva per ultima:', 'va ignorata', 'tempo'), fig_savc_stale('page', 'results for Udi: shown', 'Ud arrives last:', 'it must be ignored', 'time')),
                L('Una linea del tempo con la pagina in alto e il server in basso. Parte la richiesta per «Ud», poi quella per «Udi». La risposta per «Udi» torna prima ed è mostrata; quella per «Ud» torna dopo, tratteggiata, ed è scartata. Sotto, la riga di codice che la scarta.', 'A timeline with the page at the top and the server at the bottom. The request for “Ud” leaves, then the one for “Udi”. The answer for “Udi” comes back first and is shown; the one for “Ud” comes back later, dashed, and is thrown away. Below, the line of code that throws it away.'),
                L('Le risposte non arrivano per forza in ordine: conta solo quella dell’ultima query.', 'Answers do not always arrive in order: only the one for the latest query counts.')),
            C('const cerca = document.querySelector("#cerca"); // <sav-search id="cerca" no-filter min-chars="2">
cerca.addEventListener("sav-query", async (e) => {
  const q = e.detail.query;
  const r = await fetch("/api/citta?q=" + encodeURIComponent(q));
  if (q !== cerca.value.trim()) return; // risposta vecchia: ignorala
  cerca.suggestions = await r.json();
});
cerca.addEventListener("sav-pick", (e) => console.log("scelto", e.detail.value));', 'js', L('<sav-search id="cerca" no-filter min-chars="2" label="Città" placeholder="Scrivi almeno due lettere…"></sav-search>
<p id="scelta"></p>
<script>
  // Qui il server è finto: risponde dopo 300 ms con le città che contengono la query
  const CITTA = ["Udine", "Trieste", "Pordenone", "Gorizia", "Tolmezzo", "Cividale"];
  const server = (q) => new Promise((ok) => setTimeout(() => ok(CITTA.filter((c) => c.toLowerCase().includes(q.toLowerCase()))), 300));
  const cerca = document.querySelector("#cerca");
  cerca.addEventListener("sav-query", async (e) => {
    const q = e.detail.query;
    const lista = await server(q);
    if (q !== cerca.value.trim()) return; // risposta vecchia: ignorala
    cerca.suggestions = lista;
  });
  cerca.addEventListener("sav-pick", (e) => document.querySelector("#scelta").textContent = "scelto: " + e.detail.value);
</script>', '<sav-search id="cerca" no-filter min-chars="2" label="City" placeholder="Type at least two letters…"></sav-search>
<p id="scelta"></p>
<script>
  // A fake server here: after 300 ms it answers with the cities containing the query
  const CITTA = ["Udine", "Trieste", "Pordenone", "Gorizia", "Tolmezzo", "Cividale"];
  const server = (q) => new Promise((ok) => setTimeout(() => ok(CITTA.filter((c) => c.toLowerCase().includes(q.toLowerCase()))), 300));
  const cerca = document.querySelector("#cerca");
  cerca.addEventListener("sav-query", async (e) => {
    const q = e.detail.query;
    const lista = await server(q);
    if (q !== cerca.value.trim()) return; // an old answer: ignore it
    cerca.suggestions = lista;
  });
  cerca.addEventListener("sav-pick", (e) => document.querySelector("#scelta").textContent = "picked: " + e.detail.value);
</script>')),
        ], [
            Q('lv1', L('Come passi a un grafico un oggetto JavaScript senza trasformarlo in testo?', 'How do you pass a JavaScript object to a chart without turning it into text?'), ['grafico.setAttribute("data", oggetto)', 'grafico.data = oggetto', 'grafico.innerHTML = oggetto', 'Sav.chart(grafico, oggetto)'], 1, L('Con `setAttribute` l’oggetto diventerebbe la stringa «[object Object]».', 'With `setAttribute` the object would become the string “[object Object]”.')),
            Q('lv2', L('Che cosa fa `no-filter` su `<sav-search>`?', 'What does `no-filter` do on `<sav-search>`?'), [L('Mostra i suggerimenti come li manda la pagina, senza filtrarli', 'Shows the suggestions as the page sends them, without filtering'), L('Disattiva i suggerimenti', 'Turns suggestions off'), L('Toglie la ✕ che svuota il campo', 'Removes the ✕ that empties the field'), L('Accetta qualsiasi carattere', 'Accepts any character')], 0, L('Il filtro lo ha già fatto il server.', 'The server has already filtered them.')),
            Q('lv3', L('Con `<sav-select multiple>`, che cosa restituisce la proprietà `.value`?', 'With `<sav-select multiple>`, what does the `.value` property return?'), [L('Il numero di voci scelte', 'The number of chosen entries'), L('Solo il primo valore', 'Only the first value'), L('Un array dei valori scelti', 'An array of the chosen values'), L('Un oggetto FormData', 'A FormData object')], 2, L('Nell’attributo `value` invece la lista è separata da virgole.', 'In the `value` attribute the list is comma-separated instead.')),
        ], task('js', L('Quando `cerca` emette `sav-query`, chiama `fetch` con la query (`e.detail.query`) e assegna la risposta JSON alla proprietà `suggestions` del campo.', 'When `cerca` emits `sav-query`, call `fetch` with the query (`e.detail.query`) and assign the JSON response to the field’s `suggestions` property.'), "const cerca = document.querySelector(\"#cerca\");\n", [re('addEventListener\(\s*["\']sav-query["\']', L('Ascolti sav-query', 'You listen to sav-query')), re('detail\.query', L('Usi la query', 'You use the query')), re('fetch\(', L('Chiedi i risultati', 'You fetch the results')), re('\.suggestions\s*=', L('Assegni i suggerimenti', 'You assign the suggestions'))], '', "cerca.addEventListener(\"sav-query\", async (e) => {\n  const r = await fetch(\"/api/citta?q=\" + encodeURIComponent(e.detail.query));\n  cerca.suggestions = await r.json();\n});"), [
            dOrder(pSort(), ['cerca.addEventListener("sav-query", async (e) => {', '  const r = await fetch("/api/citta?q=" + e.detail.query);', '  cerca.suggestions = await r.json();', '});'], L('Ascolti, chiedi, assegni.', 'Listen, fetch, assign.')),
            dBlanks(pFill(), 'grafico.___ = { labels: ["Lun"], series: [{ name: "Sito", values: [9] }] };' . "\n" . 'citta.value = ["ud", "ts"]; // con ___', ['data', 'multiple'], ['dataset', 'searchable'], L('Proprietà per dati complessi.', 'Properties for complex data.')),
        ]),
        lesson('charts', L('Grafici in profondità', 'Charts in depth'), [
            P('`<sav-chart-bar>` e `<sav-chart-line>` accettano più serie: `{ "labels": […], "series": [{ "name": …, "values": […], "color": … }] }` (il colore è facoltativo). Le barre possono essere `orientation="horizontal"`, `stacked`, con `gap` da 0 a 0.9, `show-values` e un `max` per la scala; le linee sono aree (`type="area"`, predefinito) o linee (`type="line"`), con `smooth`, `points` e `stacked`. Con più serie compare la legenda (`no-legend` la toglie).', '`<sav-chart-bar>` and `<sav-chart-line>` accept several series: `{ "labels": […], "series": [{ "name": …, "values": […], "color": … }] }` (the colour is optional). Bars can be `orientation="horizontal"`, `stacked`, with `gap` from 0 to 0.9, `show-values` and a `max` for the scale; lines are areas (`type="area"`, the default) or lines (`type="line"`), with `smooth`, `points` and `stacked`. With several series a legend appears (`no-legend` removes it).'),
            C(L('<sav-chart-bar label="Iscritti per trimestre" stacked show-values
  data=\'{"labels": ["T1", "T2", "T3"],
         "series": [{"name": "Web", "values": [40, 55, 70]},
                    {"name": "Mobile", "values": [20, 35, 50]}]}\'></sav-chart-bar>

<sav-chart-pie label="Dispositivi" type="donut" center="72% mobile"
  data=\'{"labels": ["Mobile", "Desktop"], "values": [72, 28]}\'></sav-chart-pie>

<sav-chart-map label="Visite per paese" focus="IT FR" data=\'{"IT": 40, "FRA": 12}\'></sav-chart-map>', '<sav-chart-bar label="Learners per quarter" stacked show-values
  data=\'{"labels": ["Q1", "Q2", "Q3"],
         "series": [{"name": "Web", "values": [40, 55, 70]},
                    {"name": "Mobile", "values": [20, 35, 50]}]}\'></sav-chart-bar>

<sav-chart-pie label="Devices" type="donut" center="72% mobile"
  data=\'{"labels": ["Mobile", "Desktop"], "values": [72, 28]}\'></sav-chart-pie>

<sav-chart-map label="Visits per country" focus="IT FR" data=\'{"IT": 40, "FRA": 12}\'></sav-chart-map>'), 'html'),
            P('I colori vengono da **`--sav-chart-1` … `--sav-chart-8`** (il primo dal colore primario): ridefinirli su un contenitore cambia tutti i grafici al suo interno. Ogni grafico ha un riassunto per gli screen reader (il `label` più i valori principali) e una tabella nascosta con i numeri.', 'The colours come from **`--sav-chart-1` … `--sav-chart-8`** (the first from the primary colour): redefining them on a container changes every chart inside it. Each chart has a summary for screen readers (the `label` plus the main values) and a hidden table with the numbers.'),
            P('Due grafici hanno dati diversi: `<sav-chart-activity>` è un calendario a quadretti con `{ "2026-11-16": 40, … }` (valori da 0 a 100, periodo con `start` ed `end`); `<sav-chart-map>` colora il mondo con chiavi alpha-2 o alpha-3 (`{"IT": 40, "FRA": 12}`), evidenzia con `highlight`, ingrandisce con `focus` ed emette `sav-country` al clic su un paese.', 'Two charts take different data: `<sav-chart-activity>` is a calendar of squares with `{ "2026-11-16": 40, … }` (values from 0 to 100, period with `start` and `end`); `<sav-chart-map>` colours the world with alpha-2 or alpha-3 keys (`{"IT": 40, "FRA": 12}`), highlights with `highlight`, zooms with `focus` and emits `sav-country` when a country is clicked.'),
        ], [
            Q('ch1', L('Come metti una sopra l’altra le serie di un `<sav-chart-bar>`?', 'How do you pile up the series of a `<sav-chart-bar>`?'), ['mode="stack"', 'stack="true"', 'stacked', 'series-stacked'], 2, L('È un attributo booleano, come per le aree.', 'It is a boolean attribute, as for areas.')),
            Q('ch2', L('Quale formato accetta `<sav-chart-map>`?', 'Which format does `<sav-chart-map>` accept?'), [L('Un oggetto con i codici dei paesi come chiavi', 'An object with country codes as keys'), L('Una lista di coordinate GPS', 'A list of GPS coordinates'), L('Un file GeoJSON da caricare', 'A GeoJSON file to load'), L('Una tabella HTML dentro il tag', 'An HTML table inside the tag')], 0, L('Per esempio `{"IT": 40, "FRA": 12}`.', 'For example `{"IT": 40, "FRA": 12}`.')),
            Q('ch3', L('Come cambi il colore della seconda serie in tutti i grafici di una sezione?', 'How do you change the colour of the second series in every chart of a section?'), ['--sav-color-chart', '--sav-chart-2', '--sav-series-2', 'color-2="…"'], 1, L('Le variabili vanno da `--sav-chart-1` a `--sav-chart-8`.', 'The variables go from `--sav-chart-1` to `--sav-chart-8`.')),
        ], task('html', L('Crea un `<sav-chart-bar>` con `label`, `stacked` e un `data` JSON con `labels` e due `series` (ognuna con `name` e `values`); poi un `<sav-chart-pie>` a ciambella (`type="donut"`) con un testo al centro (`center`).', 'Create a `<sav-chart-bar>` with `label`, `stacked` and a JSON `data` with `labels` and two `series` (each with `name` and `values`); then a donut `<sav-chart-pie>` (`type="donut"`) with a text in the centre (`center`).'), '', [has('sav-chart-bar[label][stacked]', L('Barre impilate con label', 'Stacked bars with a label')), has('sav-chart-bar[data*="series"][data*="name"][data*="values"]', L('Serie con nome e valori', 'Series with name and values')), has('sav-chart-pie[type="donut"][center]', L('Ciambella con testo al centro', 'Donut with a centre text'))], '', "<sav-chart-bar label=\"Iscritti\" stacked\n  data='{\"labels\": [\"T1\", \"T2\"], \"series\": [{\"name\": \"Web\", \"values\": [40, 55]}, {\"name\": \"Mobile\", \"values\": [20, 35]}]}'></sav-chart-bar>\n<sav-chart-pie label=\"Dispositivi\" type=\"donut\" center=\"72%\" data='{\"labels\": [\"Mobile\", \"Desktop\"], \"values\": [72, 28]}'></sav-chart-pie>"), [
            dMatch(pMatch(), [['stacked', L('Serie impilate', 'Stacked series')], ['orientation="horizontal"', L('Barre orizzontali', 'Horizontal bars')], ['type="donut"', L('Torta a ciambella', 'Donut pie')], ['focus', L('Ingrandisce dei paesi', 'Zooms on countries')], ['--sav-chart-1', L('Colore della prima serie', 'Colour of the first series')]], L('Le opzioni dei grafici.', 'Chart options.')),
            dBug(pBug(), ['<sav-chart-line label="Visite" smooth', '  data=\'{"labels": ["Lun", "Mar"], series: [{"name": "Sito", "values": [10, 14]}]}\'>', '</sav-chart-line>'], 1, '  data=\'{"labels": ["Lun", "Mar"], "series": [{"name": "Sito", "values": [10, 14]}]}\'>', L('In JSON anche le chiavi vanno tra virgolette doppie.', 'In JSON the keys need double quotes too.')),
        ]),
        lesson('drag-drop', L('Trascinare: sortable e drop zone', 'Dragging: sortable and drop zone'), [
            P('**`<sav-sortable>`** rende riordinabili i suoi figli, qualsiasi elemento siano: con mouse, dito o penna, e con la **tastiera** (Spazio prende, le frecce spostano, Spazio lascia, Esc annulla), annunciando ogni spostamento agli screen reader. `direction` è `vertical` o `horizontal`, `handle` indica la parte da cui si afferra, `data-no-drag` blocca un figlio. Dopo ogni spostamento arriva **`sav-sort`** con `from`, `to`, `item` e `order`, la lista dei `data-id` nel nuovo ordine.', '**`<sav-sortable>`** makes its children reorderable, whatever elements they are: with mouse, finger or pen, and with the **keyboard** (Space picks up, the arrows move, Space drops, Esc cancels), announcing every move to screen readers. `direction` is `vertical` or `horizontal`, `handle` names the part to grab, `data-no-drag` locks a child. After each move comes **`sav-sort`** with `from`, `to`, `item` and `order`, the list of `data-id`s in the new order.'),
            C(L('<sav-sortable id="passi">
  <sav-card data-id="progetta">Progetta</sav-card>
  <sav-card data-id="costruisci">Costruisci</sav-card>
  <sav-card data-id="pubblica">Pubblica</sav-card>
</sav-sortable>

<form action="/carica" method="post" enctype="multipart/form-data">
  <sav-dropzone name="foto" accept="image/*,.pdf" max-size="2000000" multiple
    help="PNG, JPG o PDF, fino a 2 MB"></sav-dropzone>
  <sav-button type="submit">Carica</sav-button>
</form>', '<sav-sortable id="passi">
  <sav-card data-id="progetta">Design</sav-card>
  <sav-card data-id="costruisci">Build</sav-card>
  <sav-card data-id="pubblica">Publish</sav-card>
</sav-sortable>

<form action="/upload" method="post" enctype="multipart/form-data">
  <sav-dropzone name="foto" accept="image/*,.pdf" max-size="2000000" multiple
    help="PNG, JPG or PDF, up to 2 MB"></sav-dropzone>
  <sav-button type="submit">Upload</sav-button>
</form>'), 'html'),
            P('**`<sav-dropzone>`** accetta ciò che trascini da fuori la pagina: file (`kind="files"`, predefinito), testo e link da altre pagine (`kind="data"`, anche incollati) o entrambi (`any`). `accept` filtra i tipi, `max-size` (in byte) rifiuta i file troppo grandi, `multiple` ne accetta più d’uno. È un campo di form: con `name` i file partono con il form (che vuole `enctype="multipart/form-data"`); da JavaScript ascolti `change` (`detail.files`) o leggi la proprietà `files`.', '**`<sav-dropzone>`** accepts what you drag in from outside the page: files (`kind="files"`, the default), text and links from other pages (`kind="data"`, also pasted) or both (`any`). `accept` filters the types, `max-size` (in bytes) refuses files that are too large, `multiple` accepts more than one. It is a form field: with `name` the files are sent with the form (which needs `enctype="multipart/form-data"`); from JavaScript you listen to `change` (`detail.files`) or read the `files` property.'),
        ], [
            Q('dd1', L('Con la tastiera, come sposti un elemento in `<sav-sortable>`?', 'With the keyboard, how do you move an item in `<sav-sortable>`?'), [L('Tab per prenderlo e Invio per lasciarlo', 'Tab to pick it up and Enter to drop it'), L('Spazio per prenderlo, frecce per spostarlo, Spazio per lasciarlo', 'Space to pick it up, arrows to move it, Space to drop it'), L('Non si può: serve il mouse', 'You cannot: you need the mouse'), L('Ctrl e le frecce, senza prenderlo', 'Ctrl and the arrows, without picking it up')], 1, L('Esc annulla e lo riporta al suo posto.', 'Esc cancels and puts it back.')),
            Q('dd2', L('Che cosa contiene `e.detail.order` nell’evento `sav-sort`?', 'What does `e.detail.order` contain in the `sav-sort` event?'), [L('L’indice dell’elemento spostato', 'The index of the moved item'), L('L’HTML della lista', 'The list’s HTML'), L('I `data-id` degli elementi nel nuovo ordine', 'The `data-id`s of the items in the new order'), L('Il numero di spostamenti fatti', 'The number of moves made')], 2, L('Gli indici sono in `from` e `to`.', 'The indexes are in `from` and `to`.')),
            Q('dd3', L('Che cosa fa `max-size="2000000"` su `<sav-dropzone>`?', 'What does `max-size="2000000"` do on `<sav-dropzone>`?'), [L('Accetta al massimo due file', 'Accepts two files at most'), L('Rifiuta i file più grandi di circa 2 MB', 'Refuses files larger than about 2 MB'), L('Comprime i file a 2 MB', 'Compresses files to 2 MB'), L('Limita l’area a 2000 pixel', 'Limits the area to 2000 pixels')], 1, L('Il valore è in byte.', 'The value is in bytes.')),
        ], task('js', L('Ascolta `sav-sort` sulla lista `passi` e invia il nuovo ordine (`e.detail.order`) al server con `fetch` in `POST`, trasformato in JSON con `JSON.stringify`.', 'Listen to `sav-sort` on the `passi` list and send the new order (`e.detail.order`) to the server with a `POST` `fetch`, turned into JSON with `JSON.stringify`.'), "const passi = document.querySelector(\"#passi\");\n", [re('addEventListener\(\s*["\']sav-sort["\']', L('Ascolti sav-sort', 'You listen to sav-sort')), re('detail\.order', L('Leggi il nuovo ordine', 'You read the new order')), re('fetch\(', L('Chiami il server', 'You call the server')), re('method\s*:\s*["\']post["\']', L('Usi POST', 'You use POST')), re('JSON\.stringify\(', L('Mandi JSON', 'You send JSON'))], '', "passi.addEventListener(\"sav-sort\", (e) => {\n  fetch(\"/api/ordine\", {\n    method: \"POST\",\n    headers: { \"Content-Type\": \"application/json\" },\n    body: JSON.stringify(e.detail.order),\n  });\n});"), [
            dMatch(pMatch(), [['handle', L('Parte da cui si afferra', 'Part to grab')], ['data-id', L('Nome nell’ordine', 'Name in the order')], ['data-no-drag', L('Figlio che non si sposta', 'Child that does not move')], ['kind="data"', L('Testo e link trascinati', 'Dragged text and links')], ['accept', L('Tipi di file ammessi', 'Allowed file types')]], L('Trascinare con controllo.', 'Dragging under control.')),
            dFlash(pFlash(), [[L('sav-sortable funziona anche con la tastiera', 'sav-sortable also works with the keyboard'), true], [L('I figli di sav-sortable devono essere sav-card', 'The children of sav-sortable must be sav-cards'), false], [L('sav-dropzone accetta anche testo incollato con kind="data"', 'sav-dropzone also accepts pasted text with kind="data"'), true], [L('Per inviare file con il form basta method="get"', 'To send files with the form method="get" is enough'), false]], L('Qualsiasi figlio; per i file serve multipart.', 'Any child; files need multipart.')),
        ]),
        lesson('frameworks-ssr', L('Framework, TypeScript e server', 'Frameworks, TypeScript and the server'), [
            P('Carica i due file una sola volta nello scheletro della pagina (`index.html`, il layout principale) e usa i tag nei template. Ogni framework deve solo sapere che i tag `sav-*` non sono componenti suoi. **Vue**: `isCustomElement` nelle opzioni del compilatore; leghi i valori con `:value` e ascolti con `@change` o `@sav-select` (`v-model` è per i componenti Vue). **Angular**: `CUSTOM_ELEMENTS_SCHEMA` nel componente; `[attr.nome]` per gli attributi, `[nome]` per le proprietà, `(change)` e `(sav-select)` per gli eventi.', 'Load the two files once in the page shell (`index.html`, the root layout) and use the tags in your templates. Each framework only needs to know that `sav-*` tags are not its own components. **Vue**: `isCustomElement` in the compiler options; bind values with `:value` and listen with `@change` or `@sav-select` (`v-model` is for Vue components). **Angular**: `CUSTOM_ELEMENTS_SCHEMA` in the component; `[attr.name]` for attributes, `[name]` for properties, `(change)` and `(sav-select)` for events.'),
            C('// vite.config.js (Vue)
import vue from "@vitejs/plugin-vue";

export default {
  plugins: [
    vue({ template: { compilerOptions: { isCustomElement: (tag) => tag.startsWith("sav-") } } }),
  ],
};', 'js'),
            P('**React** 19 passa proprietà ed eventi ai custom element direttamente; per gli eventi con il trattino come `sav-select`, un `ref` con `addEventListener` (tolto nella pulizia dell’effect) funziona in ogni versione. **Svelte** li supporta così come sono. Con **TypeScript** la libreria non ha ancora un file di tipi: dichiari una volta la variabile globale `Sav` e, in TSX, i tag `sav-${string}`.', '**React** 19 passes properties and events to custom elements directly; for dashed events like `sav-select`, a `ref` with `addEventListener` (removed in the effect’s cleanup) works in every version. **Svelte** supports them as they are. With **TypeScript** the library has no type file yet: you declare the global `Sav` once and, in TSX, the `sav-${string}` tags.'),
            C('// sav.d.ts
declare const Sav: {
  version: string;
  toast(message: string, options?: { variant?: string; duration?: number }): HTMLElement;
  setMode(mode: "light" | "dark" | "auto"): void;
};
declare namespace JSX {
  interface IntrinsicElements { [tag: `sav-${string}`]: Record<string, unknown> }
}', 'ts'),
            Tip('Con Next.js, Nuxt, SvelteKit o Astro i tag escono dal server come HTML e prendono vita quando `sav.js` si carica nel browser: chiama `Sav` o leggi le proprietà dei componenti solo nel browser (in un effect, in `onMounted`, in `onMount`).', 'With Next.js, Nuxt, SvelteKit or Astro the tags leave the server as HTML and come to life when `sav.js` loads in the browser: call `Sav` or read component properties only in the browser (in an effect, in `onMounted`, in `onMount`).'),
            Fig(L(fig_savc_ssr('1 · Server', '2 · Browser: HTML', '3 · Browser: sav.js', 'solo testo HTML', 'tag non ancora definito', 'componente vivo', 'qui: effect, onMounted…'), fig_savc_ssr('1 · Server', '2 · Browser: HTML', '3 · Browser: sav.js', 'just HTML text', 'tag not defined yet', 'live component', 'here: effect, onMounted…')),
                L('Tre fasi. Sul server il sav-button è solo testo e Sav non esiste. Nel browser l’HTML arriva ma il tag non è ancora definito. Quando sav.js gira il componente prende vita e Sav c’è: è lì che va il tuo codice, in un effect o in onMounted.', 'Three stages. On the server the sav-button is just text and Sav does not exist. In the browser the HTML arrives but the tag is not defined yet. Once sav.js runs the component comes alive and Sav is there: that is where your code goes, in an effect or in onMounted.'),
                L('Il server scrive i tag; solo il browser li fa funzionare.', 'The server writes the tags; only the browser makes them work.')),
        ], [
            Q('fw1', L('Che cosa serve a Vue per usare i tag `sav-*`?', 'What does Vue need to use the `sav-*` tags?'), [L('Un plugin SavComponents per Vue', 'A SavComponents plugin for Vue'), L('`isCustomElement` nelle opzioni del compilatore', '`isCustomElement` in the compiler options'), L('`v-model` su ogni campo', '`v-model` on every field'), L('Registrarli con `app.component()`', 'Registering them with `app.component()`')], 1, L('Così Vue non li cerca tra i suoi componenti.', 'That way Vue does not look for them among its components.')),
            Q('fw2', L('In Angular, come imposti un attributo su un componente sav?', 'In Angular, how do you set an attribute on a sav component?'), ['(nome)="…"', 'v-bind:nome="…"', '[attr.nome]="…"', '{nome}'], 2, L('`[nome]` invece imposta la proprietà.', '`[name]` sets the property instead.')),
            Q('fw3', L('Con Next.js, dove chiami `Sav.setMode()`?', 'With Next.js, where do you call `Sav.setMode()`?'), [L('Nel codice che gira sul server', 'In the code that runs on the server'), L('Nel file di configurazione', 'In the configuration file'), L('Solo nel browser, per esempio in un effect', 'Only in the browser, for example in an effect'), L('In un middleware', 'In a middleware')], 2, L('Sul server `Sav` non esiste: lo crea lo script nel browser.', 'On the server `Sav` does not exist: the script creates it in the browser.')),
        ], task('js', L('Nel file di configurazione di Vite, passa al plugin `vue()` le opzioni `template.compilerOptions` con un `isCustomElement` che riconosce i tag che iniziano con `sav-`.', 'In the Vite configuration file, pass the `vue()` plugin the `template.compilerOptions` options with an `isCustomElement` that recognises tags starting with `sav-`.'), "import vue from \"@vitejs/plugin-vue\";\n\nexport default {\n  plugins: [vue()],\n};", [re('compilerOptions', L('Opzioni del compilatore', 'Compiler options')), re('isCustomElement\s*:', L('Funzione isCustomElement', 'isCustomElement function')), re('startsWith\(\s*["\'`]sav-["\'`]\s*\)', L('Riconosci i tag sav-', 'You recognise the sav- tags'))], '', "import vue from \"@vitejs/plugin-vue\";\n\nexport default {\n  plugins: [vue({ template: { compilerOptions: { isCustomElement: (tag) => tag.startsWith(\"sav-\") } } })],\n};"), [
            dMatch(pMatch(), [['Vue', 'isCustomElement'], ['Angular', 'CUSTOM_ELEMENTS_SCHEMA'], ['React', L('ref e addEventListener', 'ref and addEventListener')], ['TypeScript', L('declare const Sav', 'declare const Sav')], [L('Server', 'Server'), L('Sav solo nel browser', 'Sav only in the browser')]], L('Ogni ambiente, la sua chiave.', 'Each environment, its own key.')),
            dBlanks(pFill(), 'useEffect(() => {' . "\n" . '  const el = menu.current;' . "\n" . '  el.___("sav-select", onPick);' . "\n" . '  return () => el.___("sav-select", onPick);' . "\n" . '}, []);', ['addEventListener', 'removeEventListener'], ['onEvent', 'dispatchEvent'], L('Collegare e scollegare un evento in React.', 'Wiring and unwiring an event in React.')),
        ]),
        lesson('hosting-a11y', L('Ospitare, aggiornare e accessibilità', 'Hosting, upgrading and accessibility'), [
            P('Un indirizzo con la versione, come `/v/1.0.0/`, non cambia mai. Dentro la stessa versione principale ogni aggiornamento è sicuro (stessi tag, attributi e token): passi da 1.0.0 a 1.x cambiando il numero, dopo aver letto il changelog; solo una 2.x può chiederti modifiche. Da JavaScript `Sav.version` dice quale stai usando, e caricare lo script due volte non fa danni: la seconda copia si ferma se trova già `Sav`.', 'An address with the version, like `/v/1.0.0/`, never changes. Within the same major version every update is safe (same tags, attributes and tokens): you go from 1.0.0 to 1.x by changing the number, after reading the changelog; only a 2.x can ask you for changes. From JavaScript `Sav.version` tells you which one you use, and loading the script twice does no harm: the second copy stops if it already finds `Sav`.'),
            P('Puoi anche scaricare `sav.css` e `sav.js` e **ospitarli tu**. Icone, bandiere e mappa sono insiemi a parte con una versione propria (`Sav.iconsVersion`, `Sav.flagsVersion`, `Sav.mapsVersion`): la libreria li cerca accanto alla cartella di `sav.js`, altrimenti su savcomponents.com. Per servirli da un altro indirizzo usa `data-icon-base`, `data-flag-base` e `data-map-base` sul tag script, oppure `Sav.iconBase`, `Sav.flagBase` e `Sav.mapBase`.', 'You can also download `sav.css` and `sav.js` and **host them yourself**. Icons, flags and the map are separate sets with their own version (`Sav.iconsVersion`, `Sav.flagsVersion`, `Sav.mapsVersion`): the library looks for them next to the folder of `sav.js`, otherwise on savcomponents.com. To serve them from another address use `data-icon-base`, `data-flag-base` and `data-map-base` on the script tag, or `Sav.iconBase`, `Sav.flagBase` and `Sav.mapBase`.'),
            Fig(L(fig_savc_host('il tuo server', 'cercati accanto', 'alla cartella v/', 'con data-icon-base', 'se sav.js non sta in /v/…/', 'o /dist/'), fig_savc_host('your server', 'looked for next', 'to the v/ folder', 'with data-icon-base', 'if sav.js is not in /v/…/', 'or /dist/')),
                L('Il tuo server con la cartella /vendor/sav/ che contiene v/1.0.0/ con sav.css e sav.js e, accanto, icons/1.0.0/, flags/1.0.0/ e maps/1.0.0/, dove la libreria li cerca. Una freccia tratteggiata data-icon-base porta invece le icone su cdn.example.com; sotto, savcomponents.com è il ripiego per una copia fuori da /v/…/ o /dist/.', 'Your server with the /vendor/sav/ folder holding v/1.0.0/ with sav.css and sav.js and, next to it, icons/1.0.0/, flags/1.0.0/ and maps/1.0.0/, where the library looks for them. A dashed data-icon-base arrow sends the icons to cdn.example.com instead; below, savcomponents.com is the fallback for a copy outside /v/…/ or /dist/.'),
                L('Copia le cartelle degli insiemi accanto a `v/` e non serve altro.', 'Copy the set folders next to `v/` and nothing else is needed.')),
            C('<link rel="stylesheet" href="/vendor/sav/v/1.0.0/sav.css">
<script src="/vendor/sav/v/1.0.0/sav.js" defer
  data-icon-base="https://cdn.example.com/sav/icons/1.0.0/"></script>', 'html'),
            P('La libreria punta al livello **WCAG 2.2 AA**: tastiera, focus e ruoli ARIA sono già curati, il dialogo tiene il focus dentro e lo restituisce alla chiusura, e le animazioni rallentano o si fermano con `prefers-reduced-motion`. Il contrasto dei colori predefiniti è misurato; i token `--sav-tint` (quanto colore entra nel testo colorato) e `--sav-indicator-l` (indicatori un po’ più scuri in modalità chiara) servono a mantenerlo. Se cambi i colori del tema, il contrasto torna però una tua responsabilità: misuralo.', 'The library aims at **WCAG 2.2 AA**: keyboard, focus and ARIA roles are already handled, the dialog keeps focus inside and gives it back on closing, and animations slow down or stop with `prefers-reduced-motion`. The contrast of the default colours is measured; the `--sav-tint` token (how much colour goes into coloured text) and `--sav-indicator-l` (slightly darker indicators in light mode) are there to keep it. If you change the theme colours, though, contrast becomes your responsibility again: measure it.'),
        ], [
            Q('ha1', L('Che cosa garantisce un aggiornamento da 1.0.0 a una 1.x?', 'What does an update from 1.0.0 to a 1.x guarantee?'), [L('Che il sito si aggiorni da solo', 'That the site updates itself'), L('Nuovi nomi per i tag', 'New names for the tags'), L('Che i temi salvati si cancellino', 'That saved themes get deleted'), L('Stessi tag, attributi e token: niente da modificare', 'Same tags, attributes and tokens: nothing to change')], 3, L('Solo una nuova versione principale può chiedere modifiche.', 'Only a new major version can ask for changes.')),
            Q('ha2', L('Ospiti `sav.js` sul tuo server ma vuoi le icone da un tuo CDN: che cosa usi?', 'You host `sav.js` on your server but want the icons from your own CDN: what do you use?'), ['Sav.version', '<link rel="icons">', 'data-icon-base sul tag script', 'icon-src su ogni sav-icon'], 2, L('In alternativa, `Sav.iconBase` da JavaScript.', 'Alternatively, `Sav.iconBase` from JavaScript.')),
            Q('ha3', L('Che cosa succede se `sav.js` viene caricato due volte?', 'What happens if `sav.js` is loaded twice?'), [L('Ogni componente compare due volte', 'Every component appears twice'), L('La seconda copia si ferma subito perché trova già Sav', 'The second copy stops at once because it finds Sav already'), L('Il browser blocca la pagina con un errore', 'The browser blocks the page with an error'), L('La seconda copia cancella la prima', 'The second copy wipes out the first')], 1, L('Anche i tag già definiti non vengono ridefiniti.', 'Tags already defined are not defined again either.')),
        ], task('html', L('Nel `<head>` collega `sav.css` e `sav.js` dalla tua cartella `/vendor/sav/v/1.0.0/` (lo script con `defer`) e fai caricare le icone da un altro indirizzo con `data-icon-base`.', 'In `<head>` link `sav.css` and `sav.js` from your `/vendor/sav/v/1.0.0/` folder (the script with `defer`) and make the icons load from another address with `data-icon-base`.'), "<!doctype html>\n<html lang=\"it\">\n<head>\n  <meta charset=\"utf-8\">\n  \n</head>\n<body class=\"sav-base\">\n  <sav-icon name=\"rocket\"></sav-icon>\n</body>\n</html>", [has('head link[rel="stylesheet"][href*="/v/1.0.0/sav.css"]', L('Foglio di stile con la versione', 'Versioned stylesheet')), has('head script[src*="/v/1.0.0/sav.js"][defer]', L('Script con la versione e defer', 'Versioned script with defer')), has('head script[data-icon-base]', L('Icone da un altro indirizzo', 'Icons from another address'))], '', "<link rel=\"stylesheet\" href=\"/vendor/sav/v/1.0.0/sav.css\">\n<script src=\"/vendor/sav/v/1.0.0/sav.js\" defer data-icon-base=\"https://cdn.example.com/sav/icons/1.0.0/\"></script>"), [
            dFlash(pFlash(), [[L('Un indirizzo /v/1.0.0/ non cambia mai', 'A /v/1.0.0/ address never changes'), true], [L('Le icone sono copiate dentro ogni versione della libreria', 'The icons are copied into every library version'), false], [L('Il dialogo restituisce il focus quando si chiude', 'The dialog gives focus back when it closes'), true], [L('Con colori tuoi il contrasto è garantito dalla libreria', 'With your own colours the library guarantees contrast'), false]], L('Versioni fisse, risorse condivise, contrasto da verificare.', 'Fixed versions, shared assets, contrast to check.')),
            dMatch(pMatch(), [['Sav.version', L('Versione della libreria', 'Library version')], ['Sav.iconsVersion', L('Versione del set di icone', 'Icon set version')], ['data-icon-base', L('Icone da un altro indirizzo', 'Icons from another address')], ['--sav-tint', L('Colore nel testo colorato', 'Colour in coloured text')], ['prefers-reduced-motion', L('Meno animazioni', 'Less motion')]], L('Ospitare e rendere accessibile.', 'Hosting and making accessible.')),
        ]),
    ],
];
