<?php
/** SavComponents basics: installation, buttons and badges, layout, feedback, forms and choice fields, tags and avatars, lists and accordions, special fields. */

/** The page links two files inside a version folder of the CDN; newer folders appear next to it and the page does not move. */
if (!function_exists('fig_savc_cdn')) {
    function fig_savc_cdn(string $page, string $tokens, string $comps, string $note1, string $note2): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#0B7F5C"/></marker></defs>
<rect x="16" y="24" width="330" height="196" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="32" y="52" font-weight="700" fill="#B84508">{$page}</text>
<g font-family="ui-monospace, Menlo, monospace" font-size="13">
<text x="32" y="86">&lt;head&gt;</text>
<text x="44" y="114">&lt;link … <tspan font-weight="700" fill="#0B7F5C">/v/1.0.0/</tspan>sav.css&gt;</text>
<text x="44" y="142">&lt;script … <tspan font-weight="700" fill="#0B7F5C">/v/1.0.0/</tspan>sav.js&gt;</text>
<text x="32" y="170">&lt;/head&gt;</text>
<text x="32" y="198">&lt;body class="sav-base"&gt;</text>
</g>
<rect x="396" y="16" width="228" height="268" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="412" y="44" font-weight="700" fill="#1B66BC">savcomponents.com</text>
<rect x="412" y="60" width="196" height="100" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="428" y="84" font-weight="700" fill="#0B7F5C">/v/1.0.0/</text>
<text x="440" y="112">sav.css <tspan fill="#55527A" font-size="13">· {$tokens}</tspan></text>
<text x="440" y="140">sav.js <tspan fill="#55527A" font-size="13">· {$comps}</tspan></text>
<rect x="412" y="176" width="196" height="38" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/>
<text x="428" y="200" fill="#55527A">/v/1.1.0/</text>
<rect x="412" y="228" width="196" height="38" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/>
<text x="428" y="252" fill="#55527A">/v/2.0.0/</text>
<path d="M300 110 C350 110 380 108 432 107" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#a)"/>
<path d="M300 138 C350 138 380 136 432 135" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#a)"/>
<text x="16" y="254" font-size="14">{$note1}</text>
<text x="16" y="274" font-size="14">{$note2}</text>
</svg>
SVG;
    }
}

/** The four areas of a card and the markup that fills each one. */
if (!function_exists('fig_savc_card')) {
    function fig_savc_card(string $body, string $text): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs>
<marker id="b" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#2F8CF0"/></marker>
<marker id="p" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#7048E8"/></marker>
<marker id="g" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#0B7F5C"/></marker>
<marker id="o" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#E8590C"/></marker>
</defs>
<g font-family="ui-monospace, Menlo, monospace" font-size="13">
<text x="16" y="40">&lt;sav-card&gt;</text>
<text x="28" y="72">&lt;img <tspan font-weight="700" fill="#1B66BC">slot="media"</tspan>&gt;</text>
<text x="28" y="148">&lt;h3 <tspan font-weight="700" fill="#5A35D6">slot="header"</tspan>&gt;</text>
<text x="28" y="210">&lt;p&gt;{$text}&lt;/p&gt;</text>
<text x="28" y="274">&lt;div <tspan font-weight="700" fill="#B84508">slot="footer"</tspan>&gt;</text>
<text x="16" y="306">&lt;/sav-card&gt;</text>
</g>
<rect x="372" y="12" width="252" height="296" rx="14" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<path d="M372 112 V26 a14 14 0 0 1 14 -14 H610 a14 14 0 0 1 14 14 V112 Z" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<path d="M470 100 L510 58 L536 84 L552 70 L590 100 Z" fill="#2F8CF0" opacity="0.35"/>
<circle cx="574" cy="40" r="10" fill="#2F8CF0" opacity="0.35"/>
<text x="390" y="40" font-weight="700" fill="#1B66BC">media</text>
<rect x="388" y="124" width="220" height="38" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="404" y="148" font-weight="700" fill="#5A35D6">header</text>
<rect x="388" y="174" width="220" height="62" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="404" y="210" font-weight="700" fill="#0B7F5C">{$body}</text>
<rect x="388" y="248" width="220" height="46" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="404" y="276" font-weight="700" fill="#B84508">footer</text>
<rect x="520" y="258" width="76" height="26" rx="8" fill="#E8590C"/>
<path d="M200 67 H368" fill="none" stroke="#2F8CF0" stroke-width="2" marker-end="url(#b)"/>
<path d="M210 143 H384" fill="none" stroke="#7048E8" stroke-width="2" marker-end="url(#p)"/>
<path d="M210 205 H384" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#g)"/>
<path d="M210 269 H384" fill="none" stroke="#E8590C" stroke-width="2" marker-end="url(#o)"/>
</svg>
SVG;
    }
}

/** A form with two named fields and one without a name: only the named ones travel with the form. */
if (!function_exists('fig_savc_form')) {
    function fig_savc_form(string $send, string $sent, string $lost1, string $lost2): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#2F8CF0"/></marker></defs>
<rect x="16" y="16" width="316" height="268" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13">
<text x="32" y="42" font-weight="700">&lt;form&gt;</text>
<text x="32" y="70">&lt;sav-input <tspan font-weight="700" fill="#1B66BC">name="email"</tspan>&gt;</text>
<text x="32" y="132">&lt;sav-checkbox <tspan font-weight="700" fill="#1B66BC">name="privacy"</tspan>&gt;</text>
<text x="32" y="194" fill="#B0245A">&lt;sav-input&gt;</text>
</g>
<rect x="32" y="80" width="284" height="28" rx="8" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="44" y="99">ada@example.com</text>
<rect x="32" y="142" width="22" height="22" rx="6" fill="#2F8CF0"/>
<path d="M37 153 l4 4 l8 -9" fill="none" stroke="#FFFFFF" stroke-width="2.5"/>
<rect x="32" y="204" width="284" height="28" rx="8" fill="#FFFFFF" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4"/>
<text x="44" y="223">Ada</text>
<rect x="32" y="244" width="96" height="30" rx="8" fill="#2F8CF0"/>
<text x="80" y="264" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$send}</text>
<path d="M336 140 H384" fill="none" stroke="#2F8CF0" stroke-width="2" marker-end="url(#a)"/>
<rect x="392" y="60" width="232" height="120" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="408" y="88" font-weight="700" fill="#1B66BC">{$sent}</text>
<g font-family="ui-monospace, Menlo, monospace" font-size="13">
<text x="408" y="122">email=ada@example.com</text>
<text x="408" y="150">privacy=on</text>
</g>
<rect x="392" y="196" width="232" height="66" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="408" y="224" font-weight="700" fill="#B0245A">✕ {$lost1}</text>
<text x="408" y="246" fill="#B0245A">{$lost2}</text>
</svg>
SVG;
    }
}

/** Badge, chip and tag side by side: a status, a choice, a name. */
if (!function_exists('fig_savc_kin')) {
    function fig_savc_kin(string $msgs, string $vegan, string $gluten, string $rBadge1, string $rBadge2, string $rChip1, string $rChip2, string $rTag1, string $rTag2): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="16" y="16" width="192" height="198" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="224" y="16" width="192" height="198" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="432" y="16" width="192" height="198" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700">
<text x="32" y="44" fill="#B84508">&lt;sav-badge&gt;</text>
<text x="240" y="44" fill="#1B66BC">&lt;sav-chip&gt;</text>
<text x="448" y="44" fill="#55527A">&lt;sav-tag&gt;</text>
</g>
<text x="32" y="90">{$msgs}</text>
<rect x="128" y="74" width="30" height="22" rx="11" fill="#E8590C"/>
<text x="143" y="90" text-anchor="middle" font-size="13" font-weight="700" fill="#FFFFFF">3</text>
<circle cx="38" cy="122" r="6" fill="#0B7F5C"/>
<text x="52" y="127">online</text>
<rect x="240" y="70" width="160" height="30" rx="15" fill="#2F8CF0"/>
<path d="M254 85 l4 4 l8 -9" fill="none" stroke="#FFFFFF" stroke-width="2.5"/>
<text x="274" y="90" font-weight="700" fill="#FFFFFF">{$vegan}</text>
<rect x="240" y="110" width="160" height="30" rx="15" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="256" y="130" fill="#1B66BC">{$gluten}</text>
<rect x="448" y="70" width="104" height="30" rx="8" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="460" y="90">Design</text>
<text x="534" y="90" font-weight="700" fill="#55527A">✕</text>
<text x="32" y="178" font-weight="700" fill="#B84508">{$rBadge1}</text>
<text x="32" y="198" fill="#B84508">{$rBadge2}</text>
<text x="240" y="178" font-weight="700" fill="#1B66BC">{$rChip1}</text>
<text x="240" y="198" fill="#1B66BC">{$rChip2}</text>
<text x="448" y="178" font-weight="700" fill="#55527A">{$rTag1}</text>
<text x="448" y="198" fill="#55527A">{$rTag2}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'savcomponents-base', 'family' => 'savcomponents', 'after' => 'css-base', 'categories' => ['frontend', 'design'],
    'title' => L('SavComponents base', 'SavComponents Basics'), 'short' => L('SavComponents base', 'SavComponents Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Componenti web pronti da usare come normali tag HTML, senza framework né build: installazione, pulsanti e badge, card e griglie, avvisi e toast, campi di form che funzionano con i form nativi, tag e avatar, liste e sezioni a scomparsa, codici e password.', 'Ready-made web components used as plain HTML tags, with no framework and no build step: installation, buttons and badges, cards and grids, alerts and toasts, form fields that work with native forms, tags and avatars, lists and collapsible sections, codes and passwords.'),
    'lessons' => [
        lesson('intro', L('Che cos’è e come si installa', 'What it is and how to install it'), [
            P('**SavComponents** è una libreria open source (licenza MIT) di **componenti web**: ogni componente è un *custom element* standard, cioè un tag HTML come `<sav-button>` o `<sav-input>`. Funziona in una pagina statica, in un template PHP, in React, in Vue o ovunque ci sia HTML: **niente framework, niente build, niente dipendenze**.', '**SavComponents** is an open-source library (MIT licence) of **web components**: each component is a standard *custom element*, that is an HTML tag such as `<sav-button>` or `<sav-input>`. It works in a static page, a PHP template, React, Vue or anywhere there is HTML: **no framework, no build step, no dependencies**.'),
            P('Per installarla bastano due righe nel `<head>`: il foglio di stile `sav.css` (i **token** del tema: colori, raggi, ombre) e lo script `sav.js` (i componenti), caricato con `defer`. La **versione è nell’indirizzo** (`/v/1.0.0/`): quando esce una versione nuova, il tuo sito continua a funzionare come prima finché non decidi tu di aggiornare.', 'Installing it takes two lines in `<head>`: the `sav.css` stylesheet (the theme **tokens**: colours, radii, shadows) and the `sav.js` script (the components), loaded with `defer`. The **version is in the address** (`/v/1.0.0/`): when a new version comes out, your site keeps working as before until you decide to update.'),
            Fig(L(fig_savc_cdn('la tua pagina', 'token', 'componenti', 'Le versioni nuove escono in cartelle nuove: la tua pagina', 'resta sulla 1.0.0 finché non cambi tu il numero.'), fig_savc_cdn('your page', 'tokens', 'components', 'New versions come out in new folders: your page stays', 'on 1.0.0 until you change the number yourself.')),
                L('A sinistra il head della pagina con il link a sav.css e lo script sav.js, entrambi nella cartella /v/1.0.0/; a destra il sito savcomponents.com con la cartella /v/1.0.0/ che contiene i due file e, sotto, le cartelle tratteggiate /v/1.1.0/ e /v/2.0.0/.', 'On the left the page head with the link to sav.css and the sav.js script, both in the /v/1.0.0/ folder; on the right the savcomponents.com site with the /v/1.0.0/ folder holding the two files and, below, the dashed /v/1.1.0/ and /v/2.0.0/ folders.'),
                L('Ogni versione ha la sua cartella: le frecce puntano sempre alla stessa.', 'Each version has its own folder: the arrows always point to the same one.')),
            C(L('<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://savcomponents.com/v/1.0.0/sav.css">
  <script src="https://savcomponents.com/v/1.0.0/sav.js" defer></script>
</head>
<body class="sav-base">
  <sav-button>Ciao!</sav-button>
</body>
</html>', '<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://savcomponents.com/v/1.0.0/sav.css">
  <script src="https://savcomponents.com/v/1.0.0/sav.js" defer></script>
</head>
<body class="sav-base">
  <sav-button>Hello!</sav-button>
</body>
</html>'), 'html'),
            P('La classe facoltativa `sav-base` sul `<body>` dà alla pagina sfondo, colore del testo e carattere del tema. Finché lo script non è caricato, alcuni componenti (come `sav-dialog` o `sav-select`) restano nascosti, così non si vede un lampo di contenuto grezzo.', 'The optional `sav-base` class on `<body>` gives the page the theme’s background, text colour and font. Until the script has loaded, some components (such as `sav-dialog` or `sav-select`) stay hidden, so there is no flash of raw content.'),
            Tip('Tutti i tag iniziano con `sav-`: i nomi dei custom element devono contenere un trattino, così non si scontrano mai con i tag HTML presenti e futuri.', 'Every tag starts with `sav-`: custom element names must contain a hyphen, so they never clash with present and future HTML tags.'),
        ], [
            Q('i1', L('Che cosa serve per usare SavComponents?', 'What do you need to use SavComponents?'), [L('Un foglio di stile e uno script', 'A stylesheet and a script'), L('React e un bundler', 'React and a bundler'), L('Node.js e npm sul server', 'Node.js and npm on the server'), L('Un plugin del browser', 'A browser plugin')], 0, L('`sav.css` per i token, `sav.js` per i componenti.', '`sav.css` for the tokens, `sav.js` for the components.')),
            Q('i2', L('Perché la versione è scritta nell’indirizzo del CDN?', 'Why is the version written in the CDN address?'), [L('Così un aggiornamento non cambia il sito da solo', 'So an update does not change the site by itself'), L('Perché i browser lo richiedono', 'Because browsers require it'), L('Per caricare la pagina più veloce', 'To make the page load faster'), L('Per pagare la licenza', 'To pay for the licence')], 0, L('Aggiorni quando vuoi tu, cambiando il numero.', 'You update when you choose, by changing the number.')),
            Q('i3', L('Che cos’è `<sav-button>` per il browser?', 'What is `<sav-button>` to the browser?'), [L('Un custom element standard', 'A standard custom element'), L('Un componente React', 'A React component'), L('Un tag HTML ufficiale', 'An official HTML tag'), L('Un errore di sintassi', 'A syntax error')], 0, L('I custom element sono parte della piattaforma web.', 'Custom elements are part of the web platform.')),
            Q('i4', L('Che cosa fa la classe `sav-base` sul `<body>`?', 'What does the `sav-base` class on `<body>` do?'), [L('Applica sfondo, testo e carattere del tema', 'Applies the theme’s background, text and font'), L('Carica lo script dei componenti', 'Loads the components script'), L('Attiva la modalità scura', 'Turns on dark mode'), L('È obbligatoria per ogni componente', 'It is required by every component')], 0, L('È facoltativa: i componenti funzionano anche senza.', 'It is optional: components work without it too.')),
        ], task('html', L('Installa SavComponents: nel `<head>` aggiungi il `<link>` a `sav.css` e lo `<script>` di `sav.js` con `defer` (versione `1.0.0`), poi metti nel `<body>` un `<sav-button>` con il testo `Inizia`.', 'Install SavComponents: in `<head>` add the `<link>` to `sav.css` and the `<script>` for `sav.js` with `defer` (version `1.0.0`), then put a `<sav-button>` with the text `Inizia` in `<body>`.'), "<!doctype html>\n<html lang=\"it\">\n<head>\n  <meta charset=\"utf-8\">\n  \n</head>\n<body class=\"sav-base\">\n  \n</body>\n</html>", [has('head link[rel="stylesheet"][href*="/v/1.0.0/sav.css"]', L('Il foglio di stile è collegato', 'The stylesheet is linked')), has('head script[src*="/v/1.0.0/sav.js"][defer]', L('Lo script è caricato con defer', 'The script is loaded with defer')), has('body sav-button', L('C’è il pulsante Inizia', 'The Inizia button is there'), 'Inizia')], '', "<link rel=\"stylesheet\" href=\"https://savcomponents.com/v/1.0.0/sav.css\">\n<script src=\"https://savcomponents.com/v/1.0.0/sav.js\" defer></script>\n…\n<sav-button>Inizia</sav-button>"), [
            dOrder(pSort(), ['<head>', '  <link rel="stylesheet" href="https://savcomponents.com/v/1.0.0/sav.css">', '  <script src="https://savcomponents.com/v/1.0.0/sav.js" defer></script>', '</head>', '<body class="sav-base">'], L('Stile e script nel head, poi il body.', 'Style and script in the head, then the body.')),
            dFlash(pFlash(), [[L('SavComponents richiede un passaggio di build', 'SavComponents needs a build step'), false], [L('I componenti funzionano anche dentro un template PHP', 'The components work inside a PHP template too'), true], [L('Il nome di un custom element deve contenere un trattino', 'A custom element name must contain a hyphen'), true], [L('Con /v/1.0.0/ il sito si aggiorna da solo a ogni versione', 'With /v/1.0.0/ the site updates itself at every release'), false]], L('Due file, nessuna dipendenza.', 'Two files, no dependencies.')),
        ]),
        lesson('buttons', L('Pulsanti, badge e icone', 'Buttons, badges and icons'), [
            P('`<sav-button>` si regola con gli attributi. **`variant`** sceglie il colore di significato: `primary` (predefinito), `neutral`, `success`, `warning`, `danger`, `protected`. **`appearance`** sceglie l’aspetto: pieno (`solid`, predefinito), `outline`, `soft`, `ghost` o `link`. **`size`** è `sm`, `md` (predefinito) o `lg`; `pill`, `circle`, `square` e `block` cambiano la forma.', '`<sav-button>` is tuned with attributes. **`variant`** picks the colour of meaning: `primary` (default), `neutral`, `success`, `warning`, `danger`, `protected`. **`appearance`** picks the look: solid (`solid`, the default), `outline`, `soft`, `ghost` or `link`. **`size`** is `sm`, `md` (default) or `lg`; `pill`, `circle`, `square` and `block` change the shape.'),
            C(L('<sav-button variant="danger" appearance="outline" icon="trash">Elimina</sav-button>
<sav-button variant="success" icon-end="arrow-right" pill>Continua</sav-button>
<sav-button loading>Salvataggio…</sav-button>
<sav-button href="/prezzi" appearance="link">Vedi i prezzi</sav-button>
<sav-button circle icon="plus" label="Aggiungi"></sav-button>', '<sav-button variant="danger" appearance="outline" icon="trash">Delete</sav-button>
<sav-button variant="success" icon-end="arrow-right" pill>Continue</sav-button>
<sav-button loading>Saving…</sav-button>
<sav-button href="/pricing" appearance="link">See pricing</sav-button>
<sav-button circle icon="plus" label="Add"></sav-button>'), 'html'),
            P('`icon` e `icon-end` mettono un’icona prima o dopo il testo; `loading` mostra un indicatore e blocca i clic, come `disabled`. Con `href` il pulsante diventa un link. Un pulsante **solo icona** ha bisogno di `label`: è il nome che legge lo screen reader.', '`icon` and `icon-end` put an icon before or after the text; `loading` shows an indicator and blocks clicks, like `disabled`. With `href` the button becomes a link. An **icon-only** button needs `label`: it is the name the screen reader reads.'),
            P('**`<sav-badge>`** è un’etichetta piccola per stati e conteggi: stessi `variant`, aspetto `soft` o `outline`, forma `pill`, un puntino con `dot`, un battito con `pulse`. **`<sav-icon name="…">`** mostra un’icona da sola; se l’icona ha un significato, dalle un `label`.', '**`<sav-badge>`** is a small label for states and counts: same `variant`s, `soft` or `outline` look, `pill` shape, a dot with `dot`, a heartbeat with `pulse`. **`<sav-icon name="…">`** shows an icon on its own; if the icon carries meaning, give it a `label`.'),
        ], [
            Q('b1', L('Quale attributo sceglie il colore di significato di un pulsante?', 'Which attribute picks a button’s colour of meaning?'), ['variant', 'appearance', 'color', 'theme'], 0, L('`appearance` invece cambia l’aspetto.', '`appearance` changes the look instead.')),
            Q('b2', L('Come si fa un pulsante con il solo contorno?', 'How do you make an outline-only button?'), ['appearance="outline"', 'variant="outline"', 'border="true"', 'style="outline"'], 0, L('Aspetti: outline, soft, ghost, link.', 'Looks: outline, soft, ghost, link.')),
            Q('b3', L('Che cosa serve a un pulsante con la sola icona?', 'What does an icon-only button need?'), [L('Un `label` per gli screen reader', 'A `label` for screen readers'), L('Un `title` lunghissimo', 'A very long `title`'), L('Niente, l’icona basta', 'Nothing, the icon is enough'), L('La forma `pill`', 'The `pill` shape')], 0, L('Senza testo, `label` dà il nome accessibile.', 'Without text, `label` gives the accessible name.')),
            Q('b4', L('Che cosa succede con l’attributo `href` su `<sav-button>`?', 'What happens with the `href` attribute on `<sav-button>`?'), [L('Diventa un link', 'It becomes a link'), L('Invia il form', 'It submits the form'), L('Viene ignorato', 'It is ignored'), L('Scarica un file sempre', 'It always downloads a file')], 0, L('Aspetto da pulsante, comportamento da link.', 'Button looks, link behaviour.')),
        ], task('html', L('Crea un `<sav-button>` `Elimina` di variante `danger` con aspetto `outline`, e accanto un `<sav-badge>` `Nuovo` di variante `success` con forma `pill`.', 'Create an `Elimina` `<sav-button>` with the `danger` variant and the `outline` look, and next to it a `Nuovo` `<sav-badge>` with the `success` variant and the `pill` shape.'), '', [has('sav-button[variant="danger"][appearance="outline"]', L('Pulsante danger con contorno', 'Outline danger button'), 'Elimina'), has('sav-badge[variant="success"][pill]', L('Badge success a pillola', 'Pill success badge'), 'Nuovo')], '', "<sav-button variant=\"danger\" appearance=\"outline\">Elimina</sav-button>\n<sav-badge variant=\"success\" pill>Nuovo</sav-badge>"), [
            dMatch(pMatch(), [['variant', L('Colore di significato', 'Colour of meaning')], ['appearance', L('Pieno, contorno, soft…', 'Solid, outline, soft…')], ['size', 'sm · md · lg'], ['loading', L('Indicatore e clic bloccati', 'Indicator and clicks blocked')], ['label', L('Nome per gli screen reader', 'Name for screen readers')]], L('Attributi di sav-button.', 'Attributes of sav-button.')),
            dBug(pBug(), ['<sav-button variant="primary">Salva</sav-button>', '<sav-button variant="outline">Annulla</sav-button>', '<sav-badge variant="warning">3</sav-badge>'], 1, '<sav-button appearance="outline">Annulla</sav-button>', L('`outline` è un aspetto, non una variante.', '`outline` is a look, not a variant.')),
        ]),
        lesson('layout', L('Card, griglie e titoli', 'Cards, grids and headings'), [
            P('**`<sav-card>`** raccoglie contenuti correlati. Ha quattro zone, gli **slot**: `media` (un’immagine in alto), `header`, il contenuto principale (slot predefinito) e `footer`. Un elemento va in uno slot con l’attributo `slot="…"`; tutto il resto finisce nel corpo. `appearance` può essere `outlined` (predefinito, con il bordo), `elevated` o `flat`, e `interactive` la rende cliccabile con un effetto al passaggio.', '**`<sav-card>`** groups related content. It has four areas, the **slots**: `media` (an image at the top), `header`, the main content (default slot) and `footer`. An element goes into a slot with the `slot="…"` attribute; everything else ends up in the body. `appearance` can be `outlined` (the default, with a border), `elevated` or `flat`, and `interactive` makes it clickable with a hover effect.'),
            Fig(L(fig_savc_card('corpo · slot predefinito', 'Pomodoro…'), fig_savc_card('body · default slot', 'Tomato…')),
                L('A sinistra il markup di una card: un’immagine con slot media, un h3 con slot header, un paragrafo senza slot e un div con slot footer. Le frecce portano ciascuno nella sua zona della card disegnata a destra: media in alto, poi header, corpo e footer con un pulsante.', 'On the left the markup of a card: an image with slot media, an h3 with slot header, a paragraph with no slot and a div with slot footer. Arrows carry each one to its area of the card drawn on the right: media at the top, then header, body and footer with a button.'),
                L('L’attributo `slot` sceglie la zona; senza, il contenuto va nel corpo.', 'The `slot` attribute picks the area; without it, the content goes in the body.')),
            C(L('<sav-card>
  <img slot="media" src="pizza.jpg" alt="Una margherita">
  <h3 slot="header">Margherita</h3>
  <p>Pomodoro, mozzarella e basilico.</p>
  <div slot="footer">
    <sav-button size="sm">Ordina</sav-button>
  </div>
</sav-card>', '<sav-card>
  <img slot="media" src="pizza.jpg" alt="A margherita pizza">
  <h3 slot="header">Margherita</h3>
  <p>Tomato, mozzarella and basil.</p>
  <div slot="footer">
    <sav-button size="sm">Order</sav-button>
  </div>
</sav-card>'), 'html'),
            P('Per l’impaginazione: **`<sav-container>`** centra il contenuto in una colonna con una larghezza massima (`size`, da `sm` a `full`) e lo spazio ai lati (`padding`); **`<sav-grid>`** dispone i figli in colonne con `columns`, oppure crea quante colonne ci stanno con una larghezza minima `min`, e `gap` per lo spazio. **`<sav-heading>`** è un titolo con `level` (1-6), un `eyebrow` sopra e un `subtitle` sotto; **`<sav-divider>`** separa le sezioni.', 'For layout: **`<sav-container>`** centres the content in a column with a maximum width (`size`, from `sm` to `full`) and side spacing (`padding`); **`<sav-grid>`** lays its children out in columns with `columns`, or makes as many columns as fit with a minimum width `min`, and `gap` for the spacing. **`<sav-heading>`** is a heading with a `level` (1-6), an `eyebrow` above and a `subtitle` below; **`<sav-divider>`** separates sections.'),
            C(L('<sav-container>
  <sav-heading level="2" eyebrow="Menu" subtitle="Cotte nel forno a legna">Le nostre pizze</sav-heading>
  <sav-grid min="240px" gap="16px">
    <sav-card>…</sav-card>
    <sav-card>…</sav-card>
    <sav-card>…</sav-card>
  </sav-grid>
</sav-container>', '<sav-container>
  <sav-heading level="2" eyebrow="Menu" subtitle="Baked in a wood-fired oven">Our pizzas</sav-heading>
  <sav-grid min="240px" gap="16px">
    <sav-card>…</sav-card>
    <sav-card>…</sav-card>
    <sav-card>…</sav-card>
  </sav-grid>
</sav-container>'), 'html'),
            Tip('Una griglia con `min` è responsiva da sola: su telefono le card vanno una sotto l’altra, su schermi larghi si affiancano, senza media query.', 'A grid with `min` is responsive by itself: on phones the cards stack, on wide screens they sit side by side, with no media queries.'),
        ], [
            Q('l1', L('Come si mette un titolo nella zona `header` di una card?', 'How do you put a heading in a card’s `header` area?'), [L('Con `slot="header"` sull’elemento', 'With `slot="header"` on the element'), L('Con `class="header"`', 'With `class="header"`'), L('Con `<sav-header>` dentro la card', 'With `<sav-header>` inside the card'), L('Con l’attributo `header` sulla card', 'With the `header` attribute on the card')], 0, L('Gli slot hanno un nome; ciò che non ne indica uno va nel corpo.', 'Slots have names; whatever names none goes in the body.')),
            Q('l2', L('Che cosa fa `<sav-grid min="240px">`?', 'What does `<sav-grid min="240px">` do?'), [L('Crea quante colonne da 240px ci stanno', 'Makes as many 240px columns as fit'), L('Crea sempre 240 colonne', 'Always makes 240 columns'), L('Limita la griglia a 240px', 'Limits the grid to 240px'), L('Imposta lo spazio tra le celle', 'Sets the gap between cells')], 0, L('Per lo spazio c’è `gap`.', 'Spacing is `gap`.')),
            Q('l3', L('Quale attributo rende una card cliccabile con effetto al passaggio?', 'Which attribute makes a card clickable with a hover effect?'), ['interactive', 'clickable', 'hover', 'button'], 0, L('`appearance` invece è elevated o flat.', '`appearance` is elevated or flat instead.')),
        ], task('html', L('Crea una `<sav-card>` con un `<h3>` nello slot `header`, un paragrafo nel corpo e un `<sav-button>` dentro un elemento nello slot `footer`.', 'Create a `<sav-card>` with an `<h3>` in the `header` slot, a paragraph in the body and a `<sav-button>` inside an element in the `footer` slot.'), '', [has('sav-card > h3[slot="header"]', L('Titolo nello slot header', 'Heading in the header slot')), has('sav-card > p:not([slot])', L('Paragrafo nel corpo', 'Paragraph in the body')), has('sav-card > [slot="footer"] sav-button', L('Pulsante nel footer', 'Button in the footer'))], '', "<sav-card>\n  <h3 slot=\"header\">Margherita</h3>\n  <p>Pomodoro e mozzarella.</p>\n  <div slot=\"footer\"><sav-button>Ordina</sav-button></div>\n</sav-card>"), [
            dMatch(pMatch(), [['slot="media"', L('Immagine in alto', 'Image at the top')], ['slot="header"', L('Intestazione', 'Header')], ['slot="footer"', L('Azioni in fondo', 'Actions at the bottom')], ['<sav-grid>', L('Colonne responsive', 'Responsive columns')], ['<sav-container>', L('Larghezza massima centrata', 'Centred max width')]], L('Zone e impaginazione.', 'Areas and layout.')),
            dBlanks(pFill(), '<sav-card ___>' . "\n" . '  <h3 slot="___">Margherita</h3>' . "\n" . '</sav-card>', ['interactive', 'header'], ['clickable', 'title'], L('Card cliccabile con il titolo in alto.', 'Clickable card with the heading on top.')),
        ]),
        lesson('feedback', L('Avvisi, toast e stato', 'Alerts, toasts and status'), [
            P('**`<sav-alert>`** è un messaggio in evidenza nella pagina: `variant` dice il tipo (`success`, `warning`, `danger`…), `heading` aggiunge un titolo, `appearance` sceglie lo stile (`soft`, quello predefinito, `solid`, `outline` o `accent`), `closable` aggiunge la ✕ e `duration` (in millisecondi) lo chiude da solo; il timer si ferma mentre il puntatore è sopra.', '**`<sav-alert>`** is a highlighted message in the page: `variant` gives the type (`success`, `warning`, `danger`…), `heading` adds a title, `appearance` picks the style (`soft`, the default, `solid`, `outline` or `accent`), `closable` adds the ✕ and `duration` (in milliseconds) closes it by itself; the timer pauses while the pointer is over it.'),
            C(L('<sav-alert variant="warning" heading="Connessione lenta" closable>
  Alcune immagini potrebbero metterci un po’.
</sav-alert>', '<sav-alert variant="warning" heading="Slow connection" closable>
  Some images may take a while.
</sav-alert>'), 'html'),
            P('Un **toast** è un avviso che compare un attimo in un angolo dello schermo. Lo crei da JavaScript con **`Sav.toast(messaggio, opzioni)`**: per impostazione resta 4,5 secondi in basso a destra (`bottom-end`); le opzioni accettano `variant`, `heading`, `duration` e `placement`.', 'A **toast** is a notice that appears for a moment in a corner of the screen. You create it from JavaScript with **`Sav.toast(message, options)`**: by default it stays 4.5 seconds at the bottom right (`bottom-end`); the options accept `variant`, `heading`, `duration` and `placement`.'),
            C(L('Sav.toast("Profilo salvato", { variant: "success" });
Sav.toast("Impossibile salvare", { variant: "danger", heading: "Errore", duration: 8000 });', 'Sav.toast("Profile saved", { variant: "success" });
Sav.toast("Could not save", { variant: "danger", heading: "Error", duration: 8000 });'), 'js', L('<sav-button variant="success" id="ok">Salva</sav-button>
<sav-button variant="danger" id="ko">Prova un errore</sav-button>
<script>
  document.querySelector("#ok").addEventListener("click", () => Sav.toast("Profilo salvato", { variant: "success" }));
  document.querySelector("#ko").addEventListener("click", () => Sav.toast("Impossibile salvare", { variant: "danger", heading: "Errore", duration: 8000 }));
</script>', '<sav-button variant="success" id="ok">Save</sav-button>
<sav-button variant="danger" id="ko">Try an error</sav-button>
<script>
  document.querySelector("#ok").addEventListener("click", () => Sav.toast("Profile saved", { variant: "success" }));
  document.querySelector("#ko").addEventListener("click", () => Sav.toast("Could not save", { variant: "danger", heading: "Error", duration: 8000 }));
</script>')),
            P('Per l’attesa: **`<sav-progress-bar value="40" max="100" show-value>`** mostra l’avanzamento (con `indeterminate` quando non sai quanto manca), **`<sav-spinner>`** è una rotella di caricamento e **`<sav-skeleton>`** disegna il segnaposto grigio di un contenuto che sta arrivando.', 'For waiting: **`<sav-progress-bar value="40" max="100" show-value>`** shows progress (with `indeterminate` when you do not know how long is left), **`<sav-spinner>`** is a loading wheel and **`<sav-skeleton>`** draws the grey placeholder of content that is on its way.'),
        ], [
            Q('f1', L('Come si chiude da solo un `<sav-alert>` dopo 5 secondi?', 'How does a `<sav-alert>` close by itself after 5 seconds?'), ['duration="5000"', 'timeout="5"', 'closable="5s"', 'delay="5000"'], 0, L('Il valore è in millisecondi.', 'The value is in milliseconds.')),
            Q('f2', L('Dove compare un toast se non indichi `placement`?', 'Where does a toast appear if you give no `placement`?'), [L('In basso a destra', 'At the bottom right'), L('In alto al centro', 'At the top centre'), L('Al centro dello schermo', 'In the middle of the screen'), L('In cima alla pagina', 'At the top of the page')], 0, L('Il valore predefinito è `bottom-end`.', 'The default is `bottom-end`.')),
            Q('f3', L('Quale componente usi quando non sai quanto manca alla fine?', 'Which component do you use when you do not know how long is left?'), [L('Una progress bar con `indeterminate`', 'A progress bar with `indeterminate`'), L('Un `<sav-alert>` con `duration`', 'A `<sav-alert>` with `duration`'), L('Un `<sav-badge>` con `pulse`', 'A `<sav-badge>` with `pulse`'), L('Un `<sav-divider>`', 'A `<sav-divider>`')], 0, L('Senza percentuale, si mostra solo che sta lavorando.', 'Without a percentage, it only shows that work is going on.')),
        ], task('html', L('Aggiungi un `<sav-alert>` di variante `success` con `heading` `Fatto` e la ✕ per chiuderlo, poi una `<sav-progress-bar>` al `75` su `100` che mostra il valore.', 'Add a `<sav-alert>` with the `success` variant, the `heading` `Fatto` and the ✕ to close it, then a `<sav-progress-bar>` at `75` out of `100` that shows the value.'), '', [has('sav-alert[variant="success"][heading="Fatto"]', L('Avviso di successo con titolo', 'Success alert with heading')), has('sav-alert[closable]', L('Si può chiudere', 'It can be closed')), has('sav-progress-bar[value="75"][max="100"][show-value]', L('Barra al 75% con il valore', 'Bar at 75% with the value'))], '', "<sav-alert variant=\"success\" heading=\"Fatto\" closable>Ordine inviato.</sav-alert>\n<sav-progress-bar value=\"75\" max=\"100\" show-value></sav-progress-bar>"), [
            dMatch(pMatch(), [['<sav-alert>', L('Messaggio nella pagina', 'Message in the page')], ['Sav.toast()', L('Avviso passeggero in un angolo', 'Passing notice in a corner')], ['<sav-spinner>', L('Rotella di caricamento', 'Loading wheel')], ['<sav-skeleton>', L('Segnaposto del contenuto', 'Content placeholder')], ['<sav-progress-bar>', L('Avanzamento', 'Progress')]], L('Il componente giusto per ogni attesa.', 'The right component for every wait.')),
            dBlanks(pFill(), 'Sav.___("Salvato", { variant: "___" });', ['toast', 'success'], ['alert', 'green'], L('Un toast verde di conferma.', 'A green confirmation toast.')),
        ]),
        lesson('forms', L('Campi di testo e form', 'Text fields and forms'), [
            P('I campi di SavComponents sono **associati ai form**: dentro un normale `<form>` inviano il loro valore come un `<input>`, purché abbiano un **`name`**, e rispettano il reset. **`<sav-input>`** ha già etichetta (`label`), testo di aiuto (`help`), `placeholder`, `type` (`text`, `email`, `password`, `number`…), `clearable` per la ✕ che svuota e `password-toggle` per mostrare la password.', 'SavComponents fields are **form-associated**: inside a normal `<form>` they submit their value like an `<input>`, as long as they have a **`name`**, and they respect reset. **`<sav-input>`** already has a label (`label`), help text (`help`), `placeholder`, `type` (`text`, `email`, `password`, `number`…), `clearable` for the ✕ that empties it and `password-toggle` to reveal the password.'),
            Fig(L(fig_savc_form('Invia', 'Parte con il form', 'senza name', 'questo valore non parte'), fig_savc_form('Send', 'Sent with the form', 'no name', 'this value is not sent')),
                L('Un form con tre campi: un sav-input con name email, un sav-checkbox con name privacy spuntato e un sav-input senza name. All’invio partono solo email=ada@example.com e privacy=on; il campo senza name resta fuori.', 'A form with three fields: a sav-input named email, a ticked sav-checkbox named privacy and a sav-input with no name. On sending, only email=ada@example.com and privacy=on travel; the field with no name stays behind.'),
                L('Il `name` è la chiave con cui il valore viaggia: senza, il campo non conta.', 'The `name` is the key the value travels under: without it, the field does not count.')),
            C(L('<form action="/iscrizione" method="post">
  <sav-input name="email" type="email" label="Email" help="Non la condividiamo" required></sav-input>
  <sav-input name="password" type="password" label="Password" minlength="8" password-toggle required></sav-input>
  <sav-textarea name="note" label="Note" maxlength="200" counter></sav-textarea>
  <sav-checkbox name="privacy" required>Accetto l’informativa</sav-checkbox>
  <sav-switch name="newsletter">Ricevi la newsletter</sav-switch>
  <sav-button type="submit">Iscriviti</sav-button>
</form>', '<form action="/signup" method="post">
  <sav-input name="email" type="email" label="Email" help="We never share it" required></sav-input>
  <sav-input name="password" type="password" label="Password" minlength="8" password-toggle required></sav-input>
  <sav-textarea name="notes" label="Notes" maxlength="200" counter></sav-textarea>
  <sav-checkbox name="privacy" required>I accept the privacy policy</sav-checkbox>
  <sav-switch name="newsletter">Get the newsletter</sav-switch>
  <sav-button type="submit">Sign up</sav-button>
</form>'), 'html'),
            P('La **validazione** funziona come quella nativa: `required`, `minlength`, `maxlength`, `min`, `max` e `pattern` bloccano l’invio e mostrano il messaggio sul campo. **`<sav-textarea>`** con `counter` conta i caratteri; **`<sav-checkbox>`** e **`<sav-switch>`** hanno il testo dell’etichetta tra i tag e si spuntano con `checked`.', '**Validation** works like the native one: `required`, `minlength`, `maxlength`, `min`, `max` and `pattern` block submission and show the message on the field. **`<sav-textarea>`** with `counter` counts the characters; **`<sav-checkbox>`** and **`<sav-switch>`** have their label text between the tags and are ticked with `checked`.'),
            Tip('Un `<sav-button type="submit">` dentro il form lo invia, uno con `type="reset"` lo riporta ai valori iniziali: proprio come i pulsanti nativi.', 'A `<sav-button type="submit">` inside the form submits it, one with `type="reset"` brings it back to its initial values: just like native buttons.'),
        ], [
            Q('fo1', L('Che cosa serve perché un `<sav-input>` invii il suo valore con il form?', 'What does a `<sav-input>` need to submit its value with the form?'), [L('Un attributo `name`', 'A `name` attribute'), L('Un attributo `id`', 'An `id` attribute'), L('Una riga di JavaScript', 'A line of JavaScript'), L('La classe `form-control`', 'The `form-control` class')], 0, L('Come per gli input nativi: senza nome, niente invio.', 'Just like native inputs: no name, nothing sent.')),
            Q('fo2', L('Come aggiungi la ✕ che svuota il campo?', 'How do you add the ✕ that empties the field?'), ['clearable', 'closable', 'removable', 'reset'], 0, L('`closable` è degli avvisi, `removable` dei tag.', '`closable` belongs to alerts, `removable` to tags.')),
            Q('fo3', L('Dove si scrive l’etichetta di un `<sav-checkbox>`?', 'Where do you write a `<sav-checkbox>`’s label?'), [L('Tra il tag di apertura e quello di chiusura', 'Between the opening and closing tags'), L('Nell’attributo `value`', 'In the `value` attribute'), L('In un `<label>` esterno obbligatorio', 'In a required external `<label>`'), L('Nell’attributo `title`', 'In the `title` attribute')], 0, L('Il testo va nello slot predefinito.', 'The text goes in the default slot.')),
            Q('fo4', L('Che cosa succede se un `<sav-input required>` è vuoto e invii il form?', 'What happens if a `<sav-input required>` is empty and you submit the form?'), [L('L’invio si blocca e compare il messaggio', 'Submission stops and the message appears'), L('Il form parte lo stesso', 'The form is sent anyway'), L('La pagina si ricarica vuota', 'The page reloads empty'), L('Compare un errore in console', 'An error appears in the console')], 0, L('La validazione è quella del browser.', 'Validation is the browser’s own.')),
        ], task('html', L('Dentro il `<form>` aggiungi un `<sav-input>` con `name="email"`, `type="email"`, `label` e `required`, un `<sav-checkbox name="privacy" required>` e un `<sav-button type="submit">`.', 'Inside the `<form>` add a `<sav-input>` with `name="email"`, `type="email"`, a `label` and `required`, a `<sav-checkbox name="privacy" required>` and a `<sav-button type="submit">`.'), "<form action=\"/iscrizione\" method=\"post\">\n  \n</form>", [has('form sav-input[name="email"][type="email"][label][required]', L('Campo email obbligatorio con etichetta', 'Required email field with a label')), has('form sav-checkbox[name="privacy"][required]', L('Casella della privacy obbligatoria', 'Required privacy checkbox')), has('form sav-button[type="submit"]', L('Pulsante di invio', 'Submit button'))], '', "<sav-input name=\"email\" type=\"email\" label=\"Email\" required></sav-input>\n<sav-checkbox name=\"privacy\" required>Accetto</sav-checkbox>\n<sav-button type=\"submit\">Invia</sav-button>"), [
            dBug(pBug(), ['<form action="/iscrizione" method="post">', '  <sav-input type="email" label="Email" required></sav-input>', '  <sav-button type="submit">Invia</sav-button>', '</form>'], 1, '  <sav-input name="email" type="email" label="Email" required></sav-input>', L('Senza `name` il valore non parte con il form.', 'Without `name` the value is not sent with the form.')),
            dFlash(pFlash(), [[L('sav-input rispetta required e pattern come un input nativo', 'sav-input respects required and pattern like a native input'), true], [L('Serve JavaScript per inviare un form con i componenti', 'You need JavaScript to submit a form with the components'), false], [L('counter su sav-textarea conta i caratteri', 'counter on sav-textarea counts the characters'), true], [L('password-toggle cifra la password nel browser', 'password-toggle encrypts the password in the browser'), false]], L('Componenti che parlano con i form nativi.', 'Components that talk to native forms.')),
        ]),
        lesson('choices', L('Selezioni, date e valutazioni', 'Selects, dates and ratings'), [
            P('**`<sav-select>`** è un menu a tendina costruito con normali `<option>`: con `multiple` si scelgono più voci (mostrate come chip), con `searchable` si filtra scrivendo. Come gli altri campi ha `name`, `label`, `help`, `placeholder` e `required`.', '**`<sav-select>`** is a dropdown built from ordinary `<option>`s: with `multiple` you pick several entries (shown as chips), with `searchable` you filter by typing. Like the other fields it has `name`, `label`, `help`, `placeholder` and `required`.'),
            C(L('<sav-select name="citta" label="Città" placeholder="Scegli…" searchable>
  <option value="ud">Udine</option>
  <option value="ts">Trieste</option>
  <option value="pn">Pordenone</option>
</sav-select>

<sav-radio-group name="piano" label="Piano" value="pro">
  <sav-radio value="free">Gratis</sav-radio>
  <sav-radio value="pro">Pro</sav-radio>
</sav-radio-group>', '<sav-select name="city" label="City" placeholder="Choose…" searchable>
  <option value="ud">Udine</option>
  <option value="ts">Trieste</option>
  <option value="pn">Pordenone</option>
</sav-select>

<sav-radio-group name="plan" label="Plan" value="pro">
  <sav-radio value="free">Free</sav-radio>
  <sav-radio value="pro">Pro</sav-radio>
</sav-radio-group>'), 'html'),
            P('Le scelte uniche si fanno con **`<sav-radio-group>`**: il `name` e il `value` stanno sul gruppo, ogni `<sav-radio>` porta il suo `value`. **`<sav-chip-group>`** mostra opzioni come chip da toccare (`multiple` per sceglierne più d’una).', 'Single choices use **`<sav-radio-group>`**: `name` and `value` sit on the group, each `<sav-radio>` carries its own `value`. **`<sav-chip-group>`** shows options as chips to tap (`multiple` to pick more than one).'),
            P('Altri campi pronti: **`<sav-date-picker>`** e **`<sav-time-picker>`** con calendario e orologio (`min`, `max`, `locale`), **`<sav-range>`** per un valore su una scala (`min`, `max`, `step`, `unit`), **`<sav-rating>`** per le stelle (`max`), **`<sav-country-choice>`** e **`<sav-language-choice>`** per paesi e lingue con bandiere.', 'More ready fields: **`<sav-date-picker>`** and **`<sav-time-picker>`** with a calendar and a clock (`min`, `max`, `locale`), **`<sav-range>`** for a value on a scale (`min`, `max`, `step`, `unit`), **`<sav-rating>`** for stars (`max`), **`<sav-country-choice>`** and **`<sav-language-choice>`** for countries and languages with flags.'),
        ], [
            Q('c1', L('Come si scrivono le voci di un `<sav-select>`?', 'How do you write the entries of a `<sav-select>`?'), [L('Con normali `<option>`', 'With ordinary `<option>`s'), L('In un attributo JSON', 'In a JSON attribute'), L('Con `<sav-item>`', 'With `<sav-item>`'), L('Solo da JavaScript', 'Only from JavaScript')], 0, L('Lo stesso markup di un `<select>` nativo.', 'The same markup as a native `<select>`.')),
            Q('c2', L('In un `<sav-radio-group>`, dove va l’attributo `name`?', 'In a `<sav-radio-group>`, where does the `name` attribute go?'), [L('Sul gruppo', 'On the group'), L('Su ogni `<sav-radio>`', 'On each `<sav-radio>`'), L('Sul primo radio', 'On the first radio'), L('Sul form', 'On the form')], 0, L('Il gruppo invia il valore scelto.', 'The group submits the chosen value.')),
            Q('c3', L('Quale attributo permette di filtrare le voci scrivendo?', 'Which attribute lets you filter the entries by typing?'), ['searchable', 'filter', 'autocomplete', 'multiple'], 0, L('`multiple` serve per scegliere più voci.', '`multiple` is for picking several entries.')),
        ], task('html', L('Crea un `<sav-select name="taglia">` con `label` e tre `<option>` (`S`, `M`, `L`), poi un `<sav-rating name="voto">` con `max="5"`.', 'Create a `<sav-select name="taglia">` with a `label` and three `<option>`s (`S`, `M`, `L`), then a `<sav-rating name="voto">` with `max="5"`.'), '', [has('sav-select[name="taglia"][label]', L('Select con nome ed etichetta', 'Select with name and label')), has('sav-select > option + option + option', L('Tre opzioni', 'Three options')), has('sav-rating[name="voto"][max="5"]', L('Valutazione a 5 stelle', '5-star rating'))], '', "<sav-select name=\"taglia\" label=\"Taglia\">\n  <option value=\"s\">S</option>\n  <option value=\"m\">M</option>\n  <option value=\"l\">L</option>\n</sav-select>\n<sav-rating name=\"voto\" max=\"5\"></sav-rating>"), [
            dMatch(pMatch(), [['<sav-select>', L('Menu a tendina', 'Dropdown')], ['<sav-radio-group>', L('Una scelta fra poche', 'One choice among a few')], ['<sav-date-picker>', L('Calendario', 'Calendar')], ['<sav-range>', L('Valore su una scala', 'Value on a scale')], ['<sav-rating>', L('Stelle', 'Stars')]], L('Un campo per ogni tipo di scelta.', 'A field for every kind of choice.')),
            dOrder(pSort(), ['<sav-radio-group name="piano" label="Piano">', '  <sav-radio value="free">Gratis</sav-radio>', '  <sav-radio value="pro">Pro</sav-radio>', '</sav-radio-group>'], L('Il gruppo avvolge i suoi radio.', 'The group wraps its radios.')),
        ]),
        lesson('tags-avatars', L('Tag, avatar e pulsanti speciali', 'Tags, avatars and special buttons'), [
            P('**`<sav-tag>`** è un’etichetta per parole chiave e filtri: grigia (`neutral`) se non scegli una `variant`, aspetto `soft` o `solid`, forma `pill`. Con **`removable`** compare una ✕: toccandola, il tag si toglie da solo dalla pagina. Non confonderlo con i parenti: il **badge** mostra uno stato o un numero, il **chip** si sceglie (come un filtro acceso o spento), il tag dà solo un nome.', '**`<sav-tag>`** is a label for keywords and filters: grey (`neutral`) if you pick no `variant`, `soft` or `solid` look, `pill` shape. With **`removable`** an ✕ appears: when you tap it, the tag removes itself from the page. Do not mix it up with its relatives: the **badge** shows a status or a number, the **chip** is chosen (like a filter switched on or off), the tag only gives a name.'),
            Fig(L(fig_savc_kin('Messaggi', 'Vegano', 'Senza glutine', 'mostra uno stato', 'o un numero', 'si sceglie:', 'acceso o spento', 'dà un nome:', 'la ✕ lo toglie'), fig_savc_kin('Messages', 'Vegan', 'Gluten-free', 'shows a status', 'or a number', 'is chosen:', 'on or off', 'gives a name:', 'the ✕ removes it')),
                L('Tre riquadri affiancati. Badge: un numero 3 accanto a «Messaggi» e un puntino verde «online», mostra uno stato o un numero. Chip: «Vegano» pieno con la spunta e «Senza glutine» vuoto, si sceglie. Tag: «Design» con la ✕, dà un nome.', 'Three boxes side by side. Badge: a number 3 next to “Messages” and a green “online” dot, it shows a status or a number. Chip: a filled “Vegan” with a tick and an empty “Gluten-free”, it is chosen. Tag: “Design” with an ✕, it gives a name.'),
                L('Si somigliano, ma fanno tre lavori diversi.', 'They look alike, but they do three different jobs.')),
            P('**`<sav-avatar>`** rappresenta una persona. Con `label="Ada Lovelace"` mostra le iniziali (AL) e dà il nome agli screen reader; con `image` mostra la foto e, se non si carica, torna alle iniziali. `size` va da `sm` a `xl`; `shape` può essere `circle`, `rounded`, `square` o `tilt` (un quadrato inclinato).', '**`<sav-avatar>`** stands for a person. With `label="Ada Lovelace"` it shows the initials (AL) and gives screen readers the name; with `image` it shows the photo and, if that fails to load, falls back to the initials. `size` goes from `sm` to `xl`; `shape` can be `circle`, `rounded`, `square` or `tilt` (a leaning square).'),
            C(L('<sav-avatar label="Ada Lovelace" size="lg"></sav-avatar>
<sav-tag removable>Design</sav-tag>
<sav-tag variant="primary" appearance="solid" pill>Nuovo corso</sav-tag>

<sav-button-group label="Allineamento">
  <sav-button appearance="outline" variant="neutral">Sinistra</sav-button>
  <sav-button appearance="outline" variant="neutral">Centro</sav-button>
  <sav-button appearance="outline" variant="neutral">Destra</sav-button>
</sav-button-group>

<sav-copy-button value="FUCINA10"></sav-copy-button>
<sav-favourite-button icon="heart"></sav-favourite-button>', '<sav-avatar label="Ada Lovelace" size="lg"></sav-avatar>
<sav-tag removable>Design</sav-tag>
<sav-tag variant="primary" appearance="solid" pill>New course</sav-tag>

<sav-button-group label="Alignment">
  <sav-button appearance="outline" variant="neutral">Left</sav-button>
  <sav-button appearance="outline" variant="neutral">Centre</sav-button>
  <sav-button appearance="outline" variant="neutral">Right</sav-button>
</sav-button-group>

<sav-copy-button value="FUCINA10"></sav-copy-button>
<sav-favourite-button icon="heart"></sav-favourite-button>'), 'html'),
            P('Tre pulsanti pronti: **`<sav-button-group>`** unisce dei pulsanti in un blocco unico (con `label` per gli screen reader); **`<sav-copy-button>`** copia negli appunti il testo di `value`, oppure quello dell’elemento indicato con `from="id"`, e conferma con una spunta; **`<sav-favourite-button>`** è una stella, un cuore o un segnalibro (`icon`) che si riempie quando è premuto (`pressed`).', 'Three ready-made buttons: **`<sav-button-group>`** joins buttons into a single block (with `label` for screen readers); **`<sav-copy-button>`** copies the text in `value` to the clipboard, or that of the element named with `from="id"`, and confirms with a tick; **`<sav-favourite-button>`** is a star, a heart or a bookmark (`icon`) that fills up when pressed (`pressed`).'),
        ], [
            Q('ta1', L('Che cosa mostra `<sav-avatar label="Ada Lovelace">` senza immagine?', 'What does `<sav-avatar label="Ada Lovelace">` show without an image?'), [L('Un’icona grigia vuota', 'An empty grey icon'), L('Il nome scritto per intero', 'The full name written out'), L('Le iniziali AL', 'The initials AL'), L('Un punto colorato', 'A coloured dot')], 2, L('Le iniziali vengono dalle prime due parole del `label`.', 'The initials come from the first two words of the `label`.')),
            Q('ta2', L('Quale componente si sceglie e si deseleziona come un filtro?', 'Which component is picked and unpicked like a filter?'), ['<sav-tag>', '<sav-badge>', '<sav-label>', '<sav-chip>'], 3, L('Il tag dà un nome, il badge mostra uno stato, il chip si sceglie.', 'The tag gives a name, the badge shows a status, the chip is chosen.')),
            Q('ta3', L('Come fai copiare a `<sav-copy-button>` il testo di un altro elemento?', 'How do you make `<sav-copy-button>` copy the text of another element?'), ['target="id"', 'from="id"', 'copy="id"', 'for="id"'], 1, L('Con `value` invece copi un testo fisso.', 'With `value` you copy a fixed text instead.')),
        ], task('html', L('Crea un `<sav-avatar>` con `label` `Ada Lovelace` e `size="lg"`, un `<sav-tag>` `Design` che si può rimuovere e un `<sav-favourite-button>` a forma di cuore.', 'Create a `<sav-avatar>` with the `label` `Ada Lovelace` and `size="lg"`, a removable `Design` `<sav-tag>` and a heart-shaped `<sav-favourite-button>`.'), '', [has('sav-avatar[label="Ada Lovelace"][size="lg"]', L('Avatar grande con il nome', 'Large avatar with the name')), has('sav-tag[removable]', L('Tag Design rimovibile', 'Removable Design tag'), 'Design'), has('sav-favourite-button[icon="heart"]', L('Pulsante preferito a cuore', 'Heart favourite button'))], '', "<sav-avatar label=\"Ada Lovelace\" size=\"lg\"></sav-avatar>\n<sav-tag removable>Design</sav-tag>\n<sav-favourite-button icon=\"heart\"></sav-favourite-button>"), [
            dMatch(pMatch(), [['<sav-tag>', L('Dà un nome, si può rimuovere', 'Gives a name, can be removed')], ['<sav-badge>', L('Stato o conteggio', 'Status or count')], ['<sav-chip>', L('Si sceglie come un filtro', 'Picked like a filter')], ['<sav-avatar>', L('Foto o iniziali di una persona', 'Photo or initials of a person')], ['<sav-copy-button>', L('Copia negli appunti', 'Copies to the clipboard')]], L('Piccoli componenti, ruoli diversi.', 'Small components, different roles.')),
            dBlanks(pFill(), '<sav-favourite-button icon="___" ___></sav-favourite-button>', ['heart', 'pressed'], ['love', 'checked'], L('Un cuore già premuto.', 'A heart that is already pressed.')),
        ]),
        lesson('lists-details', L('Liste e sezioni a scomparsa', 'Lists and collapsible sections'), [
            P('**`<sav-list>`** mette in fila delle righe `<sav-list-item>`: il testo tra i tag è il titolo, `description` aggiunge una seconda riga, lo slot `prefix` ospita un’icona o un avatar all’inizio e lo slot `suffix` un badge o un valore alla fine. Con `href` la riga diventa un link, con `button` un pulsante. La lista può essere `divided` (linee tra le righe, predefinita), `card` o `plain`.', '**`<sav-list>`** lines up `<sav-list-item>` rows: the text between the tags is the title, `description` adds a second line, the `prefix` slot holds an icon or an avatar at the start and the `suffix` slot a badge or a value at the end. With `href` the row becomes a link, with `button` a button. The list can be `divided` (lines between rows, the default), `card` or `plain`.'),
            C(L('<sav-list appearance="card">
  <sav-list-item href="/profilo" description="Nome, foto ed email">
    <sav-icon slot="prefix" name="user"></sav-icon>Profilo
  </sav-list-item>
  <sav-list-item href="/notifiche" description="Email e push">
    <sav-icon slot="prefix" name="bell"></sav-icon>Notifiche
    <sav-badge slot="suffix" pill>3</sav-badge>
  </sav-list-item>
</sav-list>', '<sav-list appearance="card">
  <sav-list-item href="/profile" description="Name, photo and email">
    <sav-icon slot="prefix" name="user"></sav-icon>Profile
  </sav-list-item>
  <sav-list-item href="/notifications" description="Email and push">
    <sav-icon slot="prefix" name="bell"></sav-icon>Notifications
    <sav-badge slot="suffix" pill>3</sav-badge>
  </sav-list-item>
</sav-list>'), 'html'),
            P('Per le domande frequenti c’è **`<sav-details>`**: `summary` è la riga che si tocca, il contenuto sta tra i tag e `open` lo mostra aperto. Più sezioni dentro **`<sav-accordion>`** stanno insieme; con **`exclusive`** se ne apre una sola alla volta. Ogni sezione emette `sav-show` e `sav-hide` quando si apre e si chiude.', 'For frequently asked questions there is **`<sav-details>`**: `summary` is the line you tap, the content sits between the tags and `open` shows it open. Several sections inside **`<sav-accordion>`** belong together; with **`exclusive`** only one opens at a time. Each section emits `sav-show` and `sav-hide` when it opens and closes.'),
            C(L('<sav-accordion exclusive>
  <sav-details summary="Quanto costa?" open>Niente: Fucina è gratuita.</sav-details>
  <sav-details summary="Serve un account?">Solo per salvare i progressi.</sav-details>
</sav-accordion>

<sav-divider>oppure</sav-divider>', '<sav-accordion exclusive>
  <sav-details summary="How much does it cost?" open>Nothing: Fucina is free.</sav-details>
  <sav-details summary="Do I need an account?">Only to save your progress.</sav-details>
</sav-accordion>

<sav-divider>or</sav-divider>'), 'html'),
            Tip('`<sav-divider>` è una linea tra due sezioni; con un testo dentro diventa un separatore con la scritta («oppure»), con `vertical` separa elementi affiancati.', '`<sav-divider>` is a line between two sections; with text inside it becomes a labelled separator (“or”), with `vertical` it separates items side by side.'),
        ], [
            Q('ld1', L('Che cosa fa `exclusive` su `<sav-accordion>`?', 'What does `exclusive` do on `<sav-accordion>`?'), [L('Tiene aperta una sola sezione alla volta', 'Keeps only one section open at a time'), L('Apre tutte le sezioni insieme', 'Opens every section together'), L('Impedisce di chiudere le sezioni', 'Stops sections from closing'), L('Nasconde le sezioni vuote', 'Hides empty sections')], 0, L('Aprendone una, le altre si chiudono.', 'Opening one closes the others.')),
            Q('ld2', L('Come diventa un link una riga `<sav-list-item>`?', 'How does a `<sav-list-item>` row become a link?'), ['button', 'href', 'link', 'to'], 1, L('Con `button` invece diventa un pulsante.', 'With `button` it becomes a button instead.')),
            Q('ld3', L('Dove scrivi la riga cliccabile di un `<sav-details>`?', 'Where do you write the clickable line of a `<sav-details>`?'), [L('Tra i tag, prima del contenuto', 'Between the tags, before the content'), L('Nell’attributo `label`', 'In the `label` attribute'), L('Nell’attributo `summary`', 'In the `summary` attribute'), L('In un `<h3>` obbligatorio', 'In a required `<h3>`')], 2, L('Tra i tag va il contenuto che si apre (per un titolo ricco c’è lo slot `summary`).', 'Between the tags goes the content that opens (for a rich title there is the `summary` slot).')),
        ], task('html', L('Crea un `<sav-accordion exclusive>` con due `<sav-details>`, ognuno con `summary` e il primo aperto con `open`; poi una `<sav-list>` con un `<sav-list-item>` che ha `href` e `description`.', 'Create a `<sav-accordion exclusive>` with two `<sav-details>`, each with a `summary` and the first open with `open`; then a `<sav-list>` with a `<sav-list-item>` that has `href` and `description`.'), '', [has('sav-accordion[exclusive] > sav-details[summary] + sav-details[summary]', L('Due sezioni con il titolo, una alla volta', 'Two titled sections, one at a time')), has('sav-accordion > sav-details[open]', L('Una sezione aperta', 'An open section')), has('sav-list > sav-list-item[href][description]', L('Riga link con descrizione', 'Link row with a description'))], '', "<sav-accordion exclusive>\n  <sav-details summary=\"Quanto costa?\" open>Niente.</sav-details>\n  <sav-details summary=\"Serve un account?\">No.</sav-details>\n</sav-accordion>\n<sav-list>\n  <sav-list-item href=\"/profilo\" description=\"Nome e foto\">Profilo</sav-list-item>\n</sav-list>"), [
            dOrder(pSort(), ['<sav-list appearance="card">', '  <sav-list-item href="/profilo" description="Nome, foto ed email">', '    <sav-icon slot="prefix" name="user"></sav-icon>Profilo', '  </sav-list-item>', '</sav-list>'], L('La lista avvolge la riga, la riga avvolge l’icona.', 'The list wraps the row, the row wraps the icon.')),
            dBug(pBug(), ['<sav-accordion exclusive>', '  <sav-details title="Spedizione">Due giorni.</sav-details>', '  <sav-details summary="Resi">Entro 30 giorni.</sav-details>', '</sav-accordion>'], 1, '  <sav-details summary="Spedizione">Due giorni.</sav-details>', L('La riga da toccare si scrive in `summary`.', 'The line to tap goes in `summary`.')),
        ]),
        lesson('special-fields', L('Codici, password e ricerca', 'Codes, passwords and search'), [
            P('**`<sav-otp>`** è un codice monouso in caselle separate: `length` dice quante (da 1 a 12, predefinito 6), `type` quali caratteri accetta (`numeric`, il predefinito, `alpha`, `alphanumeric` o `any`), `mask` nasconde le cifre come un PIN e `separator="3"` mette un trattino ogni tre caselle. Incollando un codice intero si riempiono tutte, e sui telefoni la tastiera propone il codice arrivato per SMS.', '**`<sav-otp>`** is a one-time code in separate boxes: `length` says how many (1 to 12, default 6), `type` which characters it accepts (`numeric`, the default, `alpha`, `alphanumeric` or `any`), `mask` hides the digits like a PIN and `separator="3"` adds a dash every three boxes. Pasting a whole code fills them all, and on phones the keyboard offers the code that arrived by SMS.'),
            P('**`<sav-password>`** è un campo password con un indicatore di robustezza e le regole che scegli tu: `min-length` (predefinito 8), `min-uppercase`, `min-lowercase` e `min-digits` (1 ciascuna; 0 toglie la regola), `min-symbols` (predefinito 0). `rules` mostra l’elenco delle regole spuntate mentre scrivi; con **`min-strength`** (da 0 a 4) il form rifiuta le password con un punteggio più basso.', '**`<sav-password>`** is a password field with a strength meter and the rules you choose: `min-length` (default 8), `min-uppercase`, `min-lowercase` and `min-digits` (1 each; 0 removes the rule), `min-symbols` (default 0). `rules` shows the checklist ticking off as you type; with **`min-strength`** (0 to 4) the form refuses passwords with a lower score.'),
            C(L('<form action="/registrati" method="post">
  <sav-password name="password" label="Nuova password" min-length="10" min-symbols="1" rules min-strength="3"></sav-password>
  <sav-otp name="codice" label="Codice ricevuto" length="6" separator="3"></sav-otp>
  <sav-search name="citta" label="Città" placeholder="Cerca…">
    <option>Udine</option>
    <option>Trieste</option>
    <option>Gorizia</option>
  </sav-search>
  <sav-button type="submit">Continua</sav-button>
</form>', '<form action="/register" method="post">
  <sav-password name="password" label="New password" min-length="10" min-symbols="1" rules min-strength="3"></sav-password>
  <sav-otp name="code" label="Code received" length="6" separator="3"></sav-otp>
  <sav-search name="city" label="City" placeholder="Search…">
    <option>Udine</option>
    <option>Trieste</option>
    <option>Gorizia</option>
  </sav-search>
  <sav-button type="submit">Continue</sav-button>
</form>'), 'html'),
            P('**`<sav-search>`** suggerisce mentre scrivi: i suggerimenti vengono dagli `<option>` che contiene, le parti trovate sono evidenziate e si sceglie con le frecce e Invio. `min-chars` dice dopo quanti caratteri iniziare. Tutti e tre sono campi di form: con un `name` il loro valore parte con il form.', '**`<sav-search>`** suggests as you type: the suggestions come from the `<option>`s inside it, the matching parts are highlighted and you choose with the arrows and Enter. `min-chars` says after how many characters to start. All three are form fields: with a `name` their value is sent with the form.'),
        ], [
            Q('sf1', L('Quale `type` di `<sav-otp>` accetta solo cifre?', 'Which `<sav-otp>` `type` accepts digits only?'), ['alpha', 'any', 'numeric', 'digits'], 2, L('È anche il valore predefinito.', 'It is also the default.')),
            Q('sf2', L('Che cosa fa `min-strength="3"` su `<sav-password>`?', 'What does `min-strength="3"` do on `<sav-password>`?'), [L('Mostra tre regole', 'Shows three rules'), L('Il form rifiuta le password con punteggio sotto 3', 'The form refuses passwords scoring below 3'), L('Chiede almeno tre simboli', 'Asks for at least three symbols'), L('Permette tre tentativi', 'Allows three attempts')], 1, L('Il punteggio va da 0 a 4; 0 non rifiuta mai.', 'The score goes from 0 to 4; 0 never refuses.')),
            Q('sf3', L('Da dove prende i suggerimenti un `<sav-search>` senza JavaScript?', 'Where does a `<sav-search>` get its suggestions without JavaScript?'), [L('Dagli `<option>` che contiene', 'From the `<option>`s it contains'), L('Da un motore di ricerca esterno', 'From an external search engine'), L('Dalla cronologia del browser', 'From the browser history'), L('Da un attributo `list`', 'From a `list` attribute')], 0, L('Da JavaScript si possono passare anche suggerimenti dal server.', 'From JavaScript you can also pass suggestions from the server.')),
        ], task('html', L('Dentro il `<form>` aggiungi un `<sav-otp name="codice">` da 6 cifre (`length="6"` e `type="numeric"`) e un `<sav-password name="password">` che mostra le regole (`rules`) e rifiuta le password sotto il punteggio 3.', 'Inside the `<form>` add a 6-digit `<sav-otp name="codice">` (`length="6"` and `type="numeric"`) and a `<sav-password name="password">` that shows the rules (`rules`) and refuses passwords scoring below 3.'), "<form action=\"/verifica\" method=\"post\">\n  \n</form>", [has('form sav-otp[name="codice"][length="6"][type="numeric"]', L('Codice da 6 cifre', '6-digit code')), has('form sav-password[name="password"][rules]', L('Password con le regole', 'Password with the rules')), has('form sav-password[min-strength="3"]', L('Rifiuta le password deboli', 'Refuses weak passwords'))], '', "<sav-otp name=\"codice\" length=\"6\" type=\"numeric\"></sav-otp>\n<sav-password name=\"password\" rules min-strength=\"3\"></sav-password>"), [
            dMatch(pMatch(), [['length', L('Quante caselle', 'How many boxes')], ['mask', L('Nasconde come un PIN', 'Hides like a PIN')], ['rules', L('Elenco delle regole', 'Checklist of rules')], ['min-strength', L('Punteggio minimo', 'Minimum score')], ['min-chars', L('Caratteri prima dei suggerimenti', 'Characters before suggesting')]], L('Gli attributi dei campi speciali.', 'The attributes of special fields.')),
            dFlash(pFlash(), [[L('Incollare un codice intero riempie tutte le caselle di sav-otp', 'Pasting a whole code fills every box of sav-otp'), true], [L('sav-password non lascia mai inviare una password debole', 'sav-password never lets a weak password be sent'), false], [L('min-uppercase="0" toglie la regola delle maiuscole', 'min-uppercase="0" removes the upper-case rule'), true], [L('sav-search ha bisogno di un server per funzionare', 'sav-search needs a server to work'), false]], L('Il rifiuto scatta solo con min-strength.', 'Refusal only kicks in with min-strength.')),
        ]),
    ],
];
