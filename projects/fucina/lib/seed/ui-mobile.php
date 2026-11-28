<?php
/** UI on mobile: screen sizes and units, safe areas, mobile components, dark mode and platform conventions, first run, tablets and foldables, app icons and launch screens. */

/** Figures of this course, each with its words in one language ('it' or 'en'). */
if (!function_exists('fig_uimob_density')) {
    /** The same 44-point button on screens of density 1×, 2× and 3×: same physical size, more pixels. */
    function fig_uimob_density(string $lg): string
    {
        $t = $lg === 'it'
            ? ['44 pt: stessa misura fisica su ogni schermo', 'pixel']
            : ['44 pt: the same physical size on every screen', 'pixels'];
        $cell = function (int $x, int $n): string {
            $s = '';
            $step = 132 / $n;
            for ($i = 1; $i < $n; $i++) {
                $p = round($x + $i * $step, 2);
                $q = round(60 + $i * $step, 2);
                $s .= "<line x1=\"$p\" y1=\"60\" x2=\"$p\" y2=\"192\"/><line x1=\"$x\" y1=\"$q\" x2=\"" . ($x + 132) . "\" y2=\"$q\"/>";
            }
            return $s;
        };
        $g1 = $cell(40, 4); $g2 = $cell(254, 8); $g3 = $cell(468, 12);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="320" y="30" text-anchor="middle" font-weight="700">{$t[0]}</text>
<g fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"><rect x="40" y="60" width="132" height="132" rx="10"/><rect x="254" y="60" width="132" height="132" rx="10"/><rect x="468" y="60" width="132" height="132" rx="10"/></g>
<g stroke="#2F8CF0" stroke-width="1" stroke-opacity="0.5">{$g1}{$g2}{$g3}</g>
<g text-anchor="middle" font-weight="700"><text x="106" y="222">1× · 44 {$t[1]}</text><text x="320" y="222">2× · 88 {$t[1]}</text><text x="534" y="222">3× · 132 {$t[1]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uimob_safe')) {
    /** A phone screen with the camera island, the home indicator and rounded corners, and the safe area inside them. */
    function fig_uimob_safe(string $lg): string
    {
        $t = $lg === 'it'
            ? ['isola della fotocamera e barra di stato', 'area sicura', 'indicatore home', 'lo sfondo arriva ai bordi', 'i pulsanti restano dentro', 'Continua']
            : ['camera island and status bar', 'safe area', 'home indicator', 'the background reaches the edges', 'buttons stay inside', 'Continue'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 400" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="200" y="10" width="200" height="380" rx="34" fill="#1E1C52"/>
<rect x="208" y="18" width="184" height="364" rx="28" fill="#FFE8D6"/>
<rect x="268" y="28" width="64" height="20" rx="10" fill="#1E1C52"/>
<rect x="216" y="62" width="168" height="282" rx="8" fill="#FFFFFF" fill-opacity="0.75" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="6 4"/>
<rect x="260" y="364" width="80" height="5" rx="2.5" fill="#1E1C52"/>
<g fill="#E4E2F0"><rect x="230" y="80" width="110" height="12" rx="6"/><rect x="230" y="102" width="140" height="10" rx="5"/><rect x="230" y="120" width="120" height="10" rx="5"/><rect x="230" y="148" width="140" height="90" rx="10"/></g>
<rect x="230" y="290" width="140" height="40" rx="20" fill="#0B7F5C"/><text x="300" y="315" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[5]}</text>
<g stroke="#8A87B0" stroke-width="2"><line x1="336" y1="38" x2="420" y2="38"/><line x1="384" y1="200" x2="420" y2="200"/><line x1="344" y1="366" x2="420" y2="366"/><line x1="214" y1="250" x2="180" y2="250"/><line x1="230" y1="310" x2="180" y2="310"/></g>
<g font-size="13"><text x="426" y="43">{$t[0]}</text><text x="426" y="205" font-weight="700" fill="#0B7F5C">{$t[1]}</text><text x="426" y="371">{$t[2]}</text></g>
<g font-size="13" text-anchor="end"><text x="174" y="255" fill="#B84508">{$t[3]}</text><text x="174" y="315" fill="#0B7F5C">{$t[4]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uimob_anatomy')) {
    /** The anatomy of a phone screen and of a bottom sheet. */
    function fig_uimob_anatomy(string $lg): string
    {
        $t = $lg === 'it'
            ? ['barra in alto', 'righe ≥ 48 dp', 'chip per i filtri', 'FAB', 'barra di navigazione', 'bottom sheet', 'Ricette', 'Veloci', 'Vegane', 'Condividi', 'Copia link', 'Elimina']
            : ['top bar', 'rows ≥ 48 dp', 'filter chips', 'FAB', 'navigation bar', 'bottom sheet', 'Recipes', 'Quick', 'Vegan', 'Share', 'Copy link', 'Delete'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 380" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<g transform="translate(24 0)">
<rect x="150" y="10" width="180" height="360" rx="26" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<text x="168" y="50" font-size="15">‹</text><text x="184" y="51" font-size="15" font-weight="700">{$t[6]}</text>
<line x1="151" y1="66" x2="329" y2="66" stroke="#E4E2F0" stroke-width="2"/>
<rect x="166" y="76" width="56" height="24" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="1.5"/><text x="194" y="92" text-anchor="middle" font-size="12">{$t[7]}</text>
<rect x="228" y="76" width="56" height="24" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="1.5"/><text x="256" y="92" text-anchor="middle" font-size="12">{$t[8]}</text>
<g fill="#E4E2F0"><circle cx="180" cy="130" r="12"/><rect x="200" y="122" width="100" height="8" rx="4"/><rect x="200" y="134" width="70" height="7" rx="3.5"/>
<circle cx="180" cy="180" r="12"/><rect x="200" y="172" width="100" height="8" rx="4"/><rect x="200" y="184" width="60" height="7" rx="3.5"/>
<circle cx="180" cy="230" r="12"/><rect x="200" y="222" width="100" height="8" rx="4"/><rect x="200" y="234" width="80" height="7" rx="3.5"/></g>
<g stroke="#E4E2F0" stroke-width="1.5"><line x1="166" y1="155" x2="314" y2="155"/><line x1="166" y1="205" x2="314" y2="205"/></g>
<rect x="268" y="262" width="48" height="48" rx="16" fill="#E8590C"/><path d="M292 276 v20 M282 286 h20" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
<path d="M151 320 h178 v24 a25 25 0 0 1 -25 25 h-128 a25 25 0 0 1 -25 -25 z" fill="#F1F0F8"/>
<g fill="#8A87B0"><circle cx="190" cy="340" r="7"/><circle cx="240" cy="340" r="7"/><circle cx="290" cy="340" r="7"/></g>
<g stroke="#8A87B0" stroke-width="1.5"><line x1="140" y1="44" x2="116" y2="44"/><line x1="140" y1="88" x2="116" y2="88"/><line x1="140" y1="180" x2="116" y2="180"/><line x1="140" y1="340" x2="116" y2="340"/><line x1="322" y1="286" x2="350" y2="286"/></g>
<g text-anchor="end"><text x="110" y="48">{$t[0]}</text><text x="110" y="92">{$t[2]}</text><text x="110" y="184">{$t[1]}</text><text x="110" y="344">{$t[4]}</text></g>
<text x="356" y="290" font-weight="700" fill="#B84508">{$t[3]}</text>
</g>
<rect x="420" y="10" width="180" height="360" rx="26" fill="#8A87B0" stroke="#1E1C52" stroke-width="2"/>
<path d="M421 210 h178 v134 a25 25 0 0 1 -25 25 h-128 a25 25 0 0 1 -25 -25 z" fill="#FFFFFF"/>
<path d="M421 230 a20 20 0 0 1 20 -20 h138 a20 20 0 0 1 20 20" fill="#FFFFFF"/>
<rect x="490" y="220" width="40" height="5" rx="2.5" fill="#C9C7DE"/>
<g font-size="14"><text x="444" y="262">{$t[9]}</text><text x="444" y="302">{$t[10]}</text><text x="444" y="342" fill="#C2255C">{$t[11]}</text></g>
<text x="510" y="196" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[5]}</text>
</svg>
SVG;
    }
}
if (!function_exists('fig_uimob_widths')) {
    /** Compact, medium and expanded widths: bottom bar, rail, rail with list-detail. */
    function fig_uimob_widths(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Compatta', 'Media', 'Espansa', '&lt; 600 dp', '600-839 dp', '≥ 840 dp', 'barra in basso', 'rail laterale', 'rail + elenco e dettaglio']
            : ['Compact', 'Medium', 'Expanded', '&lt; 600 dp', '600-839 dp', '≥ 840 dp', 'bottom bar', 'side rail', 'rail + list and detail'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<rect x="20" y="40" width="110" height="190" rx="16" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<g fill="#E4E2F0"><rect x="32" y="56" width="86" height="30" rx="6"/><rect x="32" y="94" width="86" height="30" rx="6"/><rect x="32" y="132" width="86" height="30" rx="6"/></g>
<path d="M21 196 h108 v18 a15 15 0 0 1 -15 15 h-78 a15 15 0 0 1 -15 -15 z" fill="#FFE8D6"/>
<g fill="#E8590C"><circle cx="45" cy="210" r="5"/><circle cx="75" cy="210" r="5"/><circle cx="105" cy="210" r="5"/></g>
<rect x="160" y="40" width="190" height="190" rx="14" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<path d="M161 54 a13 13 0 0 1 13 -13 h22 v188 h-22 a13 13 0 0 1 -13 -13 z" fill="#DCEBFF"/>
<g fill="#2F8CF0"><circle cx="180" cy="70" r="5"/><circle cx="180" cy="100" r="5"/><circle cx="180" cy="130" r="5"/></g>
<g fill="#E4E2F0"><rect x="208" y="56" width="62" height="70" rx="6"/><rect x="276" y="56" width="62" height="70" rx="6"/><rect x="208" y="134" width="62" height="70" rx="6"/><rect x="276" y="134" width="62" height="70" rx="6"/></g>
<rect x="380" y="40" width="240" height="190" rx="12" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/>
<path d="M381 52 a11 11 0 0 1 11 -11 h18 v188 h-18 a11 11 0 0 1 -11 -11 z" fill="#D5F6EA"/>
<g fill="#0B7F5C"><circle cx="396" cy="70" r="5"/><circle cx="396" cy="100" r="5"/><circle cx="396" cy="130" r="5"/></g>
<rect x="420" y="54" width="70" height="26" rx="5" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="1.5"/>
<g fill="#E4E2F0"><rect x="420" y="86" width="70" height="26" rx="5"/><rect x="420" y="118" width="70" height="26" rx="5"/><rect x="420" y="150" width="70" height="26" rx="5"/>
<rect x="502" y="54" width="104" height="16" rx="6"/><rect x="502" y="80" width="104" height="70" rx="6"/><rect x="502" y="158" width="90" height="8" rx="4"/><rect x="502" y="172" width="70" height="8" rx="4"/></g>
<line x1="496" y1="48" x2="496" y2="222" stroke="#E4E2F0" stroke-width="2"/>
<g font-weight="700" text-anchor="middle" font-size="15"><text x="75" y="28">{$t[0]}</text><text x="255" y="28">{$t[1]}</text><text x="500" y="28">{$t[2]}</text></g>
<g text-anchor="middle" fill="#5A5880"><text x="75" y="252">{$t[3]}</text><text x="255" y="252">{$t[4]}</text><text x="500" y="252">{$t[5]}</text></g>
<g text-anchor="middle" font-weight="700"><text x="75" y="274" fill="#B84508">{$t[6]}</text><text x="255" y="274" fill="#1B66BC">{$t[7]}</text><text x="500" y="274" fill="#0B7F5C">{$t[8]}</text></g>
</svg>
SVG;
    }
}
if (!function_exists('fig_uimob_icon')) {
    /** A layered icon: background and foreground, then the shapes the system can crop it into. */
    function fig_uimob_icon(string $lg): string
    {
        $t = $lg === 'it'
            ? ['sfondo', 'primo piano', 'icona completa', 'forme diverse, stessa icona', 'monocromatica']
            : ['background', 'foreground', 'full icon', 'different shapes, same icon', 'monochrome'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<defs><clipPath id="c1"><circle cx="404" cy="100" r="34"/></clipPath><clipPath id="c2"><rect x="456" y="66" width="68" height="68" rx="22"/></clipPath><clipPath id="c3"><rect x="544" y="66" width="68" height="68" rx="10"/></clipPath><g id="ic"><path d="M0 -28 l26 46 h-52 z" fill="#E8590C"/><circle cx="0" cy="22" r="12" fill="#1E1C52"/></g></defs>
<rect x="20" y="50" width="100" height="100" rx="14" fill="#FFE8D6"/>
<rect x="132" y="50" width="100" height="100" rx="14" fill="none" stroke="#C9C7DE" stroke-width="2" stroke-dasharray="5 4"/>
<use href="#ic" x="182" y="100"/>
<text x="126" y="106" text-anchor="middle" font-size="20" fill="#8A87B0">+</text>
<text x="238" y="106" font-size="20" fill="#8A87B0">=</text>
<rect x="258" y="50" width="100" height="100" rx="14" fill="#FFE8D6"/><use href="#ic" x="308" y="100"/>
<g clip-path="url(#c1)"><rect x="360" y="50" width="90" height="100" fill="#FFE8D6"/><use href="#ic" x="404" y="100"/></g>
<g clip-path="url(#c2)"><rect x="450" y="50" width="80" height="100" fill="#FFE8D6"/><use href="#ic" x="490" y="100"/></g>
<g clip-path="url(#c3)"><rect x="540" y="50" width="80" height="100" fill="#F1F0F8"/><path d="M578 72 l26 46 h-52 z" fill="#5A5880"/><circle cx="578" cy="122" r="12" fill="#1E1C52"/></g>
<g text-anchor="middle" fill="#5A5880"><text x="70" y="172">{$t[0]}</text><text x="182" y="172">{$t[1]}</text><text x="308" y="172">{$t[2]}</text><text x="578" y="172">{$t[4]}</text><text x="447" y="172">{$t[3]}</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'ui-mobile', 'family' => 'ui', 'after' => 'ui-base', 'categories' => ['design', 'mobile'],
    'title' => L('UI su mobile', 'UI on Mobile'), 'short' => L('UI mobile', 'Mobile UI'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Disegnare interfacce per telefono: dimensioni, densità e unità, safe area, componenti mobile, tema scuro e convenzioni di iOS e Android, tablet e pieghevoli, icona dell’app e schermata di avvio.', 'Drawing phone interfaces: sizes, density and units, safe areas, mobile components, dark theme and iOS and Android conventions, tablets and foldables, app icon and launch screen.'),
    'lessons' => [
        lesson('units', L('Schermi, densità e unità', 'Screens, density and units'), [
            P('I telefoni hanno schermi con **densità** diverse: lo stesso pulsante deve avere la stessa dimensione fisica su tutti. Per questo si progetta in unità indipendenti: **punti** (pt) su iOS, **dp** su Android, **px CSS** sul web, che il sistema moltiplica per la densità (2×, 3×). Un iPhone comune è largo circa **390-440 pt**, molti Android **360-412 dp**.', 'Phones have screens with different **densities**: the same button must have the same physical size on all of them. That is why you design in independent units: **points** (pt) on iOS, **dp** on Android, **CSS px** on the web, which the system multiplies by density (2×, 3×). A common iPhone is about **390-440 pt** wide, many Androids **360-412 dp**.'),
            Fig(L(fig_uimob_density('it'), fig_uimob_density('en')),
                L('Tre quadrati della stessa dimensione, ciascuno un pulsante da 44 punti. Il primo è diviso da una griglia rada (densità 1×, 44 pixel), il secondo da una griglia doppia (2×, 88 pixel), il terzo da una griglia tripla (3×, 132 pixel).', 'Three squares of the same size, each a 44-point button. The first is divided by a sparse grid (1× density, 44 pixels), the second by a grid twice as fine (2×, 88 pixels), the third by one three times as fine (3×, 132 pixels).'),
                L('Si progetta in punti o dp: il sistema moltiplica per la densità dello schermo.', 'You design in points or dp: the system multiplies by the screen density.')),
            P('Le immagini vanno fornite in più risoluzioni (`@2x`, `@3x`, o `srcset` sul web) oppure in formato **vettoriale** (SVG), nitido a ogni scala. Per il testo si usano unità che seguono le preferenze dell’utente: **Dynamic Type** su iOS, **sp** su Android, `rem` sul web.', 'Images must be provided in several resolutions (`@2x`, `@3x`, or `srcset` on the web) or in **vector** format (SVG), sharp at every scale. Text uses units that follow user preferences: **Dynamic Type** on iOS, **sp** on Android, `rem` on the web.'),
        ], [
            Q('u1', L('Perché si progetta in pt o dp invece che in pixel fisici?', 'Why design in pt or dp instead of physical pixels?'), [L('Per avere la stessa dimensione su densità diverse', 'For the same size across densities'), L('Perché i pixel sono a pagamento', 'Because pixels cost money'), L('Per rendere le immagini sfocate', 'To make images blurry'), L('Non c’è differenza', 'There is no difference')], 0, L('Il sistema moltiplica per la densità.', 'The system multiplies by density.')),
            Q('u2', L('Quale unità segue la dimensione del testo scelta dall’utente su Android?', 'Which unit follows the user’s text size on Android?'), ['sp', 'px', 'pt fissi', 'cm'], 0, L('Scale-independent pixels.', 'Scale-independent pixels.')),
            Q('u3', L('Quale formato resta nitido a ogni scala?', 'Which format stays sharp at every scale?'), ['SVG', 'JPEG', L('PNG piccolo', 'Small PNG'), 'GIF'], 0, L('Vettoriale.', 'Vector.')),
        ], task('html', L('Fornisci il logo in due risoluzioni con `srcset`: `logo.png 1x` e `logo@2x.png 2x`, con un `alt`.', 'Provide the logo in two resolutions with `srcset`: `logo.png 1x` and `logo@2x.png 2x`, with an `alt`.'), '<img src="logo.png">', [re('srcset="[^"]*logo\.png\s+1x', L('Versione 1x', '1x version')), re('srcset="[^"]*logo@2x\.png\s+2x', L('Versione 2x', '2x version')), has('img[alt]:not([alt=""])', L('Testo alternativo', 'Alt text'))], '', '<img src="logo.png" srcset="logo.png 1x, logo@2x.png 2x" alt="Fucina">'), [
            dMatch(pMatch(), [['pt', 'iOS'], ['dp', 'Android'], ['sp', L('Testo su Android', 'Text on Android')], ['@3x', L('Densità alta', 'High density')], ['SVG', L('Vettoriale', 'Vector')]], L('Unità mobile.', 'Mobile units.')),
            dFlash(pFlash(), [[L('Lo stesso dp ha circa la stessa dimensione fisica ovunque', 'The same dp has about the same physical size everywhere'), true], [L('Basta un’immagine piccola per tutti gli schermi', 'One small image is enough for every screen'), false], [L('Dynamic Type segue le preferenze dell’utente', 'Dynamic Type follows user preferences'), true], [L('Tutti i telefoni hanno la stessa larghezza', 'All phones have the same width'), false]], L('Schermi diversi.', 'Different screens.')),
        ]),
        lesson('safe', L('Safe area, notch e tastiera', 'Safe areas, notches and the keyboard'), [
            P('Lo schermo non è tutto utilizzabile: in alto ci sono **notch** o **Dynamic Island** e la barra di stato, in basso l’**indicatore della home** o la barra dei gesti, ai lati gli angoli arrotondati. La **safe area** è la zona dove i contenuti interattivi sono al sicuro. Gli sfondi possono estendersi fino ai bordi, i pulsanti no.', 'Not all of the screen is usable: at the top there are **notches** or the **Dynamic Island** and the status bar, at the bottom the **home indicator** or gesture bar, at the sides rounded corners. The **safe area** is the zone where interactive content is safe. Backgrounds can extend to the edges, buttons cannot.'),
            Fig(L(fig_uimob_safe('it'), fig_uimob_safe('en')),
                L('Un telefono con l’isola della fotocamera in alto, l’indicatore home in basso e gli angoli arrotondati. Lo sfondo arancione chiaro arriva fino ai bordi; un riquadro tratteggiato verde segna l’area sicura, dentro la quale stanno il contenuto e il pulsante «Continua».', 'A phone with the camera island at the top, the home indicator at the bottom and rounded corners. The light orange background reaches the edges; a dashed green box marks the safe area, which holds the content and the “Continue” button.'),
                L('Lo sfondo può andare ovunque, ciò che si tocca resta nell’area sicura.', 'The background can go anywhere, what you tap stays in the safe area.')),
            P('Sul web si usano `viewport-fit=cover` e le variabili `env(safe-area-inset-bottom)`. Quando appare la **tastiera**, metà schermo sparisce: il campo attivo e il pulsante di invio devono restare visibili, quindi il layout deve scorrere o ridimensionarsi.', 'On the web you use `viewport-fit=cover` and the `env(safe-area-inset-bottom)` variables. When the **keyboard** appears, half the screen disappears: the active field and the submit button must stay visible, so the layout must scroll or resize.'),
            C('.barra-in-basso {
  position: fixed;
  bottom: 0;
  padding-bottom: calc(12px + env(safe-area-inset-bottom));
}', 'css', L('<p style="font:15px system-ui;margin:0 0 64px">Il contenuto scorre sopra la barra fissa.</p><nav class="barra-in-basso" style="left:0;right:0;padding-top:12px;background:#1E1C52;color:#fff;font:600 14px system-ui;display:flex;justify-content:space-around"><span>Home</span><span>Cerca</span><span>Profilo</span></nav>', '<p style="font:15px system-ui;margin:0 0 64px">The content scrolls above the fixed bar.</p><nav class="barra-in-basso" style="left:0;right:0;padding-top:12px;background:#1E1C52;color:#fff;font:600 14px system-ui;display:flex;justify-content:space-around"><span>Home</span><span>Search</span><span>Profile</span></nav>')),
        ], [
            Q('a1', L('Che cos’è la safe area?', 'What is the safe area?'), [L('La zona libera da notch e barre di sistema', 'The zone free of notches and system bars'), L('Una zona protetta da password', 'A password-protected zone'), L('Lo sfondo dell’app', 'The app background'), L('La memoria del telefono', 'The phone’s memory')], 0, L('Lì vanno i controlli.', 'Controls go there.')),
            Q('a2', L('Che cosa può estendersi fino ai bordi?', 'What may extend to the edges?'), [L('Gli sfondi', 'Backgrounds'), L('I pulsanti principali', 'Main buttons'), L('I campi di testo', 'Text fields'), L('I link', 'Links')], 0, L('Decorazione sì, controlli no.', 'Decoration yes, controls no.')),
            Q('a3', L('Che cosa fare quando compare la tastiera?', 'What to do when the keyboard appears?'), [L('Tenere visibili campo e pulsante di invio', 'Keep the field and submit button visible'), L('Nascondere il campo attivo', 'Hide the active field'), L('Chiudere l’app', 'Close the app'), L('Niente', 'Nothing')], 0, L('Il layout si adatta.', 'The layout adapts.')),
        ], task('css', L('Allontana la `.barra` dall’indicatore della home: `padding-bottom` con `env(safe-area-inset-bottom)`.', 'Keep `.barra` away from the home indicator: `padding-bottom` with `env(safe-area-inset-bottom)`.'), ".barra {\n  position: fixed;\n  bottom: 0;\n  left: 0;\n  right: 0;\n}\n", [re('\.barra\s*\{[^}]*padding(-bottom)?\s*:[^;]*env\(\s*safe-area-inset-bottom', L('Rispetta la safe area', 'Respects the safe area'))], '<nav class="barra">Home · Cerca · Profilo</nav>', ".barra {\n  padding-bottom: calc(12px + env(safe-area-inset-bottom));\n}"), [
            dMatch(pMatch(), [['Notch', L('Taglio in alto', 'Cut-out at the top')], [L('Indicatore della home', 'Home indicator'), L('Barra dei gesti in basso', 'Gesture bar at the bottom')], ['safe-area-inset-bottom', L('Spazio sicuro in basso', 'Safe space at the bottom')], ['viewport-fit=cover', L('Usa tutto lo schermo', 'Uses the whole screen')]], L('I bordi del telefono.', 'The phone’s edges.')),
            dBlanks(pFill(), ".barra {\n  padding-bottom: ___(safe-area-inset-___);\n}", ['env', 'bottom'], ['var', 'top'], L('La variabile d’ambiente del bordo inferiore.', 'The environment variable for the bottom edge.')),
        ]),
        lesson('components', L('Componenti mobile', 'Mobile components'), [
            P('Il telefono ha componenti suoi: **barra di navigazione** in alto con titolo e indietro, **tab bar** o **navigation bar** in basso, **bottom sheet** (pannello che sale dal basso), **floating action button** (FAB, Android), **liste** a tutta larghezza con elementi alti almeno 48 dp, **chip** per filtri, **snackbar** o **toast** per brevi conferme, **pull to refresh** per aggiornare.', 'The phone has its own components: a top **navigation bar** with title and back, a bottom **tab bar** or **navigation bar**, **bottom sheets** (panels rising from the bottom), the **floating action button** (FAB, Android), full-width **lists** with items at least 48 dp tall, **chips** for filters, **snackbars** or **toasts** for brief confirmations, **pull to refresh** to update.'),
            Fig(L(fig_uimob_anatomy('it'), fig_uimob_anatomy('en')),
                L('A sinistra una schermata con le sue parti indicate: barra in alto con indietro e titolo, chip per i filtri, un elenco di righe, un pulsante d’azione flottante arancione e la barra di navigazione in basso. A destra un bottom sheet salito dal basso sopra lo schermo scurito, con le azioni Condividi, Copia link ed Elimina.', 'On the left a screen with its parts labelled: top bar with back and title, filter chips, a list of rows, an orange floating action button and the navigation bar at the bottom. On the right a bottom sheet risen from the bottom over the dimmed screen, with the actions Share, Copy link and Delete.'),
                L('Le azioni frequenti stanno in basso, vicino al pollice.', 'Frequent actions sit at the bottom, close to the thumb.')),
            P('Le **finestre modali** a tutto schermo interrompono: usale per compiti brevi e chiusi (scrivere un messaggio), con un’azione chiara per confermare e una per chiudere. Per scelte veloci un bottom sheet è più leggero e resta vicino al pollice.', 'Full-screen **modals** interrupt: use them for short, self-contained tasks (writing a message), with a clear action to confirm and one to close. For quick choices a bottom sheet is lighter and stays near the thumb.'),
        ], [
            Q('c1', L('Che cos’è un bottom sheet?', 'What is a bottom sheet?'), [L('Un pannello che sale dal basso', 'A panel rising from the bottom'), L('Il fondo della pagina web', 'The bottom of a web page'), L('Un foglio di calcolo', 'A spreadsheet'), L('Una notifica di sistema', 'A system notification')], 0, L('Vicino al pollice.', 'Near the thumb.')),
            Q('c2', L('A che cosa serve una snackbar?', 'What is a snackbar for?'), [L('Una breve conferma, a volte con un’azione', 'A brief confirmation, sometimes with an action'), L('Un menu di cibo', 'A food menu'), L('Un errore bloccante', 'A blocking error'), L('La navigazione principale', 'Main navigation')], 0, L('“Messaggio eliminato · Annulla”.', '“Message deleted · Undo”.')),
            Q('c3', L('Quando usare una modale a tutto schermo?', 'When to use a full-screen modal?'), [L('Per un compito breve e chiuso', 'For a short, self-contained task'), L('Per ogni scelta, anche minima', 'For every choice, however small'), L('Per la navigazione principale', 'For main navigation'), L('Mai', 'Never')], 0, L('Interrompe: con giudizio.', 'It interrupts: use with care.')),
        ], task('text', L('Abbina ogni situazione al componente, una riga ciascuna nel formato `situazione: componente`: confermare “messaggio archiviato”, scegliere come condividere una foto, creare una nuova nota.', 'Match each situation with a component, one line each in the format `situation: component`: confirming “message archived”, choosing how to share a photo, creating a new note.'), '', [re('(archiv)[^\n]*:\s*(snackbar|toast)', L('Snackbar per la conferma', 'Snackbar for the confirmation')), re('(condivid|share)[^\n]*:\s*(bottom sheet|foglio)', L('Bottom sheet per condividere', 'Bottom sheet for sharing')), re('(nota|note)[^\n]*:\s*(fab|floating|pulsante|button)', L('FAB per creare', 'FAB to create'))], '', "messaggio archiviato: snackbar\ncondividere una foto: bottom sheet\nnuova nota: FAB"), [
            dMatch(pMatch(), [['FAB', L('Azione principale fluttuante', 'Floating main action')],['Snackbar', L('Breve conferma', 'Brief confirmation')], ['Bottom sheet', L('Opzioni dal basso', 'Options from the bottom')], ['Chip', L('Filtro compatto', 'Compact filter')], ['Pull to refresh', L('Aggiorna trascinando', 'Refresh by pulling')]], L('I componenti del telefono.', 'Phone components.')),
            dFlash(pFlash(), [[L('Gli elementi di una lista dovrebbero essere alti almeno 48 dp', 'List items should be at least 48 dp tall'), true], [L('Ogni scelta merita una modale a tutto schermo', 'Every choice deserves a full-screen modal'), false], [L('Una snackbar può offrire Annulla', 'A snackbar can offer Undo'), true], [L('Il FAB serve per azioni secondarie rare', 'The FAB is for rare secondary actions'), false]], L('Il componente giusto.', 'The right component.')),
        ]),
        lesson('platforms', L('Tema scuro e convenzioni delle piattaforme', 'Dark theme and platform conventions'), [
            P('Il **tema scuro** non è il tema chiaro invertito: Material consiglia sfondi grigi molto scuri invece del nero puro, che crea troppo contrasto e “sbava” sugli OLED in movimento (iOS usa il nero come sfondo di base, ma superfici rialzate più chiare); i colori brillanti si **desaturano** un po’, l’elevazione si mostra con superfici **più chiare** invece che con ombre. Va seguita la preferenza di sistema.', 'The **dark theme** is not the light theme inverted: Material recommends very dark grey backgrounds instead of pure black, which creates too much contrast and smears on OLED in motion (iOS uses black as the base background, but lighter raised surfaces); bright colours get slightly **desaturated**, elevation is shown with **lighter** surfaces instead of shadows. The system preference should be followed.'),
            P('**iOS e Android** hanno convenzioni diverse: su iOS il titolo grande a sinistra, il tasto indietro con la freccia in alto a sinistra e il gesto dal bordo, i tab in basso, i fogli modali; su Android la top app bar, il gesto (o tasto) indietro di sistema, il FAB, Material You con colori presi dallo sfondo. Un’app multipiattaforma può avere un brand unico ma deve rispettare i **comportamenti** che ogni utente si aspetta.', '**iOS and Android** have different conventions: on iOS the large title on the left, the top-left back arrow and the edge swipe, bottom tabs, modal sheets; on Android the top app bar, the system back gesture (or button), the FAB, Material You with colours taken from the wallpaper. A cross-platform app can have a single brand but must respect the **behaviours** each user expects.'),
            C('@media (prefers-color-scheme: dark) {
  :root {
    --bg: #121212;       /* non nero puro */
    --surface: #1e1e1e;  /* più chiaro = più in alto */
    --text: #e6e6e6;
  }
}', 'css', L('<div style="background:var(--bg,#f4f4f6);color:var(--text,#1e1c52);padding:12px;font:15px system-ui"><div style="background:var(--surface,#fff);padding:12px;border-radius:10px">Questa scheda segue il tema del sistema: prova a passare al tema scuro.</div></div>', '<div style="background:var(--bg,#f4f4f6);color:var(--text,#1e1c52);padding:12px;font:15px system-ui"><div style="background:var(--surface,#fff);padding:12px;border-radius:10px">This card follows the system theme: try switching to dark mode.</div></div>')),
        ], [
            Q('p1', L('Perché evitare il nero puro come sfondo scuro?', 'Why avoid pure black as a dark background?'), [L('Troppo contrasto e sbavature su OLED', 'Too much contrast and smearing on OLED'), L('Consuma più batteria', 'It uses more battery'), L('Non esiste su CSS', 'It does not exist in CSS'), L('È vietato da Apple', 'Apple forbids it')], 0, L('Meglio grigi molto scuri.', 'Very dark greys are better.')),
            Q('p2', L('Come si mostra l’elevazione nel tema scuro?', 'How is elevation shown in dark theme?'), [L('Con superfici più chiare', 'With lighter surfaces'), L('Con ombre nere più forti', 'With stronger black shadows'), L('Con bordi rossi', 'With red borders'), L('Non si mostra', 'It is not shown')], 0, L('Più in alto, più chiaro.', 'Higher, lighter.')),
            Q('p3', L('Che cosa deve rispettare un’app multipiattaforma?', 'What must a cross-platform app respect?'), [L('I comportamenti attesi su ogni sistema', 'The expected behaviours on each system'), L('Solo le regole di iOS ovunque', 'Only iOS rules everywhere'), L('Nessuna convenzione', 'No convention'), L('Solo il brand', 'Only the brand')], 0, L('Brand unico, comportamenti nativi.', 'Single brand, native behaviours.')),
        ], task('css', L('Aggiungi il tema scuro: dentro `@media (prefers-color-scheme: dark)` ridefinisci `--bg` con un grigio scuro (non `#000`) e `--text` con un colore chiaro.', 'Add the dark theme: inside `@media (prefers-color-scheme: dark)` redefine `--bg` with a dark grey (not `#000`) and `--text` with a light colour.'), ":root {\n  --bg: #ffffff;\n  --text: #1a1a1a;\n}\nbody {\n  background: var(--bg);\n  color: var(--text);\n}\n", [re('@media\s*\(\s*prefers-color-scheme\s*:\s*dark\s*\)\s*\{[^@]*--bg\s*:', L('Sfondo scuro definito', 'Dark background defined')), re('@media[^@]*--bg\s*:\s*(?!#000\b|#000000\b|black\b)#?[0-9a-z]', L('Non nero puro', 'Not pure black')), re('@media[^@]*--text\s*:', L('Testo chiaro', 'Light text'))], '<p>Ciao</p>', "@media (prefers-color-scheme: dark) {\n  :root {\n    --bg: #121212;\n    --text: #e6e6e6;\n  }\n}"), [
            dMatch(pMatch(), [[L('Titolo grande a sinistra', 'Large title on the left'), 'iOS'], ['FAB', 'Android'], ['Material You', L('Colori dallo sfondo', 'Colours from the wallpaper')], ['#121212', L('Sfondo scuro', 'Dark background')], [L('Superficie più chiara', 'Lighter surface'), L('Più elevazione', 'More elevation')]], L('Scuro e nativo.', 'Dark and native.')),
            dFlash(pFlash(), [[L('Il tema scuro va oltre l’inversione dei colori', 'Dark theme goes beyond inverting colours'), true], [L('Su Android il FAB è una convenzione di iOS', 'On Android the FAB is an iOS convention'), false], [L('Nel tema scuro i colori brillanti si desaturano', 'In dark theme bright colours get desaturated'), true], [L('Ignorare la preferenza di sistema è una buona pratica', 'Ignoring the system preference is good practice'), false]], L('Rispettare la piattaforma.', 'Respecting the platform.')),
        ]),
        lesson('first-run', L('Stati vuoti, onboarding e permessi', 'Empty states, onboarding and permissions'), [
            P('La prima volta che si apre un’app quasi tutto è **vuoto**. Uno stato vuoto ben fatto ha un’illustrazione o un’icona, una frase che spiega cosa comparirà lì e un **pulsante per iniziare**. È spesso la schermata più vista dai nuovi utenti.', 'The first time an app is opened almost everything is **empty**. A good empty state has an illustration or icon, a sentence explaining what will appear there and a **button to start**. It is often the screen new users see most.'),
            P('L’**onboarding** migliore è corto: tre schermate al massimo, saltabili, oppure suggerimenti che appaiono nel momento giusto. Chiedi i **permessi** (notifiche, posizione) quando servono davvero e spiega prima il perché: un rifiuto al primo avvio è difficile da recuperare.', 'The best **onboarding** is short: three screens at most, skippable, or tips appearing at the right moment. Ask for **permissions** (notifications, location) when they are really needed and explain why first: a refusal on first launch is hard to recover.'),
        ], [
            Q('fr1', L('Che cosa serve in uno stato vuoto?', 'What does an empty state need?'), [L('Spiegazione e pulsante per iniziare', 'An explanation and a button to start'), L('Solo la scritta “Vuoto”', 'Just the word “Empty”'), L('Un errore', 'An error'), L('Niente', 'Nothing')], 0, L('Guida il primo passo.', 'It guides the first step.')),
            Q('fr2', L('Com’è un buon onboarding?', 'What is good onboarding like?'), [L('Corto e saltabile', 'Short and skippable'), L('Dieci schermate obbligatorie', 'Ten mandatory screens'), L('Un video di 5 minuti', 'A 5-minute video'), L('Un modulo lungo', 'A long form')], 0, L('Meglio imparare usando.', 'Better to learn by using.')),
            Q('fr3', L('Quando chiedere il permesso per le notifiche?', 'When should you ask for notification permission?'), [L('Quando serve, spiegando il perché', 'When needed, explaining why'), L('Appena si apre l’app', 'As soon as the app opens'), L('Mai', 'Never'), L('Dopo un anno', 'After a year')], 0, L('Un no è difficile da recuperare.', 'A no is hard to recover.')),
        ], task('text', L('Scrivi uno stato vuoto in due righe: `Testo:` con la spiegazione e `Pulsante:` con l’azione.', 'Write an empty state in two lines: `Text:` with the explanation and `Button:` with the action.'), '', [re('^(Testo|Text)\s*:\s*\S.{10,}', L('C’è la spiegazione', 'The explanation is there')), re('^(Pulsante|Button)\s*:\s*\S.{3,}', L('C’è l’azione', 'The action is there'))], '', "Testo: Qui troverai le tue liste della spesa.\nPulsante: Crea la prima lista"), [
            dOrder(pSort(), [L('L’utente tocca “Ricordami”', 'The user taps “Remind me”'), L('Spieghi a che cosa servono le notifiche', 'You explain what notifications are for'), L('Appare la richiesta del sistema', 'The system prompt appears'), L('L’utente sceglie', 'The user chooses')], L('Chiedere un permesso nel momento giusto.', 'Asking for a permission at the right time.')),
            dFlash(pFlash(), [[L('Lo stato vuoto è spesso molto visto dai nuovi utenti', 'The empty state is often seen a lot by new users'), true], [L('Chiedere tutti i permessi al primo avvio è la scelta migliore', 'Asking every permission on first launch is best'), false], [L('L’onboarding dovrebbe poter essere saltato', 'Onboarding should be skippable'), true], [L('Uno stato vuoto non ha bisogno di azioni', 'An empty state needs no actions'), false]], L('Il primo incontro.', 'The first encounter.')),
        ]),
        lesson('adaptive', L('Tablet, pieghevoli e orientamento', 'Tablets, foldables and orientation'), [
            P('Un’app mobile oggi gira anche su **tablet**, su **pieghevoli** (che si aprono da telefono a quasi tablet), in **orizzontale** e, su iPad e Android, in finestre ridimensionabili affiancate ad altre app. Stirare il layout del telefono a tutta larghezza spreca spazio e allunga le righe fino a renderle illeggibili.', 'A mobile app today also runs on **tablets**, on **foldables** (which open from phone to almost tablet size), in **landscape** and, on iPad and Android, in resizable windows next to other apps. Stretching the phone layout to full width wastes space and makes lines unreadably long.'),
            P('Si ragiona per **classi di larghezza**: in Material compact sotto i 600 dp, medium fino a 839, expanded da 840; in Apple le size class compact e regular. Con più spazio la tab bar diventa una **barra laterale** o una rail, una lista diventa **lista-dettaglio** a due colonne, le griglie aggiungono colonne. Sui pieghevoli evita contenuti importanti sulla **piega** e conserva lo stato quando il dispositivo si apre o si chiude.', 'You think in **width classes**: in Material compact under 600 dp, medium up to 839, expanded from 840; at Apple the compact and regular size classes. With more space the tab bar becomes a **sidebar** or a rail, a list becomes a two-column **list-detail**, grids add columns. On foldables avoid important content on the **fold** and keep the state when the device opens or closes.'),
            Fig(L(fig_uimob_widths('it'), fig_uimob_widths('en')),
                L('Tre larghezze della stessa app. Compatta, sotto i 600 dp: un elenco e una barra di navigazione in basso. Media, tra 600 e 839 dp: un rail di navigazione a sinistra e una griglia a due colonne. Espansa, da 840 dp: il rail, l’elenco e il dettaglio affiancati.', 'Three widths of the same app. Compact, under 600 dp: a list and a navigation bar at the bottom. Medium, between 600 and 839 dp: a navigation rail on the left and a two-column grid. Expanded, from 840 dp: the rail, the list and the detail side by side.'),
                L('Con più spazio la navigazione si sposta di lato e il contenuto si affianca.', 'With more space navigation moves to the side and content sits side by side.')),
        ], [
            Q('ad1', L('Che cosa succede spesso alla tab bar su uno schermo largo?', 'What often happens to the tab bar on a wide screen?'), [L('Sparisce del tutto', 'It disappears entirely'), L('Diventa una barra laterale o una rail', 'It becomes a sidebar or a rail'), L('Si raddoppia', 'It doubles'), L('Va in alto a destra', 'It moves to the top right')], 1, L('La navigazione si sposta di lato.', 'Navigation moves to the side.')),
            Q('ad2', L('Perché non stirare il layout del telefono sul tablet?', 'Why not stretch the phone layout on a tablet?'), [L('Il tablet non lo permette', 'The tablet does not allow it'), L('Consuma più batteria', 'It uses more battery'), L('Spreca spazio e allunga troppo le righe', 'It wastes space and makes lines too long'), L('Capovolge le immagini', 'It flips the images')], 2, L('Più spazio, più struttura.', 'More space, more structure.')),
            Q('ad3', L('Che cosa evitare su un telefono pieghevole aperto?', 'What should you avoid on an open foldable?'), [L('Le liste', 'Lists'), L('I colori di brand', 'Brand colours'), L('Il tema scuro', 'Dark theme'), L('Contenuti importanti sulla piega', 'Important content on the fold')], 3, L('La piega può deformare o nascondere.', 'The fold can distort or hide.')),
        ], task('css', L('Da `600px` in su mostra la navigazione laterale: dentro `@media (min-width: 600px)` nascondi `.tab-bar` con `display: none` e mostra `.rail` con `display: flex`.', 'From `600px` up show side navigation: inside `@media (min-width: 600px)` hide `.tab-bar` with `display: none` and show `.rail` with `display: flex`.'), ".tab-bar {\n  display: flex;\n}\n.rail {\n  display: none;\n}\n", [re('@media[^{]*min-width\s*:\s*600px[^{]*\{[^@]*\.tab-bar\s*\{[^}]*display\s*:\s*none', L('Tab bar nascosta da 600px', 'Tab bar hidden from 600px')), re('@media[^{]*min-width\s*:\s*600px[^{]*\{[^@]*\.rail\s*\{[^}]*display\s*:\s*flex', L('Rail visibile da 600px', 'Rail visible from 600px'))], '<nav class="rail">Home · Cerca · Profilo</nav><main>Contenuto</main><nav class="tab-bar">Home · Cerca · Profilo</nav>', ".tab-bar { display: flex; }\n.rail { display: none; }\n@media (min-width: 600px) {\n  .tab-bar { display: none; }\n  .rail { display: flex; }\n}"), [
            dMatch(pMatch(), [['Compact', L('Telefono in verticale', 'Phone in portrait')], ['Medium', L('Pieghevole aperto o tablet piccolo', 'Open foldable or small tablet')], ['Expanded', L('Tablet grande e desktop', 'Large tablet and desktop')], ['Rail', L('Navigazione laterale stretta', 'Narrow side navigation')], [L('Lista-dettaglio', 'List-detail'), L('Due colonne affiancate', 'Two columns side by side')]], L('Classi di larghezza.', 'Width classes.')),
            dFlash(pFlash(), [[L('Su iPad un’app può stare in una finestra ridimensionabile', 'On iPad an app can live in a resizable window'), true], [L('Il layout del telefono va solo stirato sul tablet', 'The phone layout should just be stretched on tablets'), false], [L('Lo stato va conservato quando il pieghevole si apre', 'State should be kept when the foldable opens'), true], [L('L’orientamento orizzontale si può sempre ignorare', 'Landscape can always be ignored'), false]], L('Ogni schermo conta.', 'Every screen counts.')),
        ]),
        lesson('app-icon', L('Icona dell’app e schermata di avvio', 'App icon and launch screen'), [
            P('L’icona è la prima cosa che si vede nello store e sulla Home. Funziona se è **semplice** (una forma riconoscibile, niente testo o foto minuscole), leggibile anche molto piccola e diversa dalle concorrenti. Su **iOS**, dal 2025, le icone si compongono a **livelli** con **Icon Composer** e il sistema le mostra in più aspetti (normale, scuro, trasparente e colorata) con l’effetto Liquid Glass; su **Android** le **icone adattive** hanno primo piano e sfondo separati, che il launcher ritaglia in forme diverse, e le **icone a tema** usano una versione monocromatica colorata dal sistema.', 'The icon is the first thing people see in the store and on the Home Screen. It works if it is **simple** (one recognisable shape, no tiny text or photos), readable even when very small and different from competitors. On **iOS**, since 2025, icons are built in **layers** with **Icon Composer** and the system shows them in several appearances (default, dark, clear and tinted) with the Liquid Glass effect; on **Android**, **adaptive icons** have separate foreground and background layers that the launcher crops into different shapes, and **themed icons** use a monochrome version tinted by the system.'),
            Fig(L(fig_uimob_icon('it'), fig_uimob_icon('en')),
                L('Un livello di sfondo arancione chiaro più un livello in primo piano con un triangolo e un cerchio formano l’icona completa. A destra la stessa icona ritagliata in un cerchio e in un quadrato molto arrotondato, e una versione monocromatica in grigio.', 'A light orange background layer plus a foreground layer with a triangle and a circle make the full icon. On the right the same icon cropped into a circle and into a very rounded square, and a monochrome version in grey.'),
                L('Livelli separati permettono al sistema forme, temi ed effetti diversi.', 'Separate layers let the system apply different shapes, themes and effects.')),
            P('La **schermata di avvio** compare mentre l’app carica: deve durare poco e somigliare alla prima schermata. Su iOS il launch screen è un’anteprima quasi vuota dell’interfaccia, non una pubblicità; su Android 12 e successivi c’è la **Splash Screen API** di sistema, con l’icona al centro. Niente loghi animati lunghi: le persone vogliono entrare.', 'The **launch screen** appears while the app loads: it should be brief and look like the first screen. On iOS the launch screen is an almost empty preview of the interface, not an advert; on Android 12 and later there is the system **Splash Screen API**, with the icon in the centre. No long animated logos: people want to get in.'),
        ], [
            Q('ic1', L('Che cosa rende efficace un’icona d’app?', 'What makes an app icon effective?'), [L('Tanto testo descrittivo', 'Plenty of descriptive text'), L('Una foto ricca di dettagli', 'A highly detailed photo'), L('Una forma semplice e riconoscibile', 'A simple, recognisable shape'), L('Tutti i colori del brand insieme', 'All the brand colours together')], 2, L('Deve leggersi anche piccolissima.', 'It must read even when tiny.')),
            Q('ic2', L('Che cos’è un’icona adattiva su Android?', 'What is an adaptive icon on Android?'), [L('Un’icona animata', 'An animated icon'), L('Un’icona con primo piano e sfondo separati che il launcher ritaglia', 'An icon with separate foreground and background that the launcher crops'), L('Un’icona che cambia ogni giorno', 'An icon that changes every day'), L('Un’icona solo in bianco e nero', 'A black-and-white-only icon')], 1, L('Cerchio, quadrato arrotondato, goccia…', 'Circle, squircle, teardrop…')),
            Q('ic3', L('Come deve essere la schermata di avvio?', 'What should the launch screen be like?'), [L('Una pubblicità di cinque secondi', 'A five-second advert'), L('Un video del logo', 'A video of the logo'), L('Un modulo di registrazione', 'A sign-up form'), L('Breve e simile alla prima schermata', 'Brief and similar to the first screen')], 3, L('Sembra che l’app sia già pronta.', 'It feels like the app is already ready.')),
        ], task('text', L('Descrivi l’icona di un’app di ricette in due righe: `Forma:` (un solo simbolo semplice) e `Livelli:` (che cosa sta in primo piano e che cosa sullo sfondo).', 'Describe the icon of a recipe app in two lines: `Shape:` (one simple symbol) and `Layers:` (what is in the foreground and what in the background).'), "Forma: \nLivelli: ", [re('^(forma|shape)[ \t]*:[ \t]*\S.{3,}', L('Una forma semplice', 'A simple shape')), re('^(livelli|layers)[ \t]*:.*((primo piano|foreground).*(sfondo|background)|(sfondo|background).*(primo piano|foreground))', L('Primo piano e sfondo', 'Foreground and background'))], '', "Forma: una frusta da cucina stilizzata\nLivelli: la frusta bianca in primo piano, uno sfondo arancione a tinta unita"), [
            dMatch(pMatch(), [['Icon Composer', L('Icone iOS a livelli', 'Layered iOS icons')], [L('Icona adattiva', 'Adaptive icon'), L('Primo piano e sfondo separati', 'Separate foreground and background')], [L('Icona a tema', 'Themed icon'), L('Monocromatica, colorata dal sistema', 'Monochrome, tinted by the system')], ['Splash Screen API', L('Avvio di sistema su Android 12+', 'System launch on Android 12+')]], L('Il primo impatto.', 'The first impression.')),
            dFlash(pFlash(), [[L('Su iOS un’icona può apparire scura, trasparente o colorata', 'On iOS an icon can appear dark, clear or tinted'), true], [L('Il testo piccolo nell’icona aiuta a riconoscerla', 'Small text in the icon helps recognition'), false], [L('Su Android il launcher può ritagliare l’icona in forme diverse', 'On Android the launcher can crop the icon into different shapes'), true], [L('Un logo animato lungo all’avvio migliora l’esperienza', 'A long animated logo at launch improves the experience'), false]], L('Icone e avvio.', 'Icons and launch.')),
        ]),
    ],
];
