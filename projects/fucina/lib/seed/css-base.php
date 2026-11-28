<?php
/** CSS base: selectors, colours, box model, layout basics and the cascade; backgrounds, units, linking CSS and flexible sizes are in extra-levels.php. */

$page = '<h1 class="titolo">Fucina</h1>
<p>Il primo paragrafo.</p>
<p class="nota">Una nota.</p>
<div id="box">Una scatola</div>
<a href="#">Un link</a>';

/** The box model as four nested layers, with its labels in one language. The margin's dashes march slowly. */
if (!function_exists('box_model_svg')) {
    function box_model_svg(string $margin, string $border, string $padding, string $content): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.ants{animation:march 6s linear infinite}@keyframes march{to{stroke-dashoffset:-60}}@media (prefers-reduced-motion:reduce){.ants{animation:none}}</style>
<rect class="ants" x="10" y="10" width="620" height="280" rx="18" fill="#FFF3EA" stroke="#E8590C" stroke-width="2" stroke-dasharray="10 5"/>
<text x="30" y="38" font-weight="700" fill="#B84508">{$margin}</text>
<rect x="70" y="56" width="500" height="200" rx="14" fill="#1E1C52"/>
<text x="88" y="80" font-weight="700" fill="#FFFFFF">{$border}</text>
<rect x="84" y="92" width="472" height="150" rx="10" fill="#D5F6EA"/>
<text x="102" y="116" font-weight="700" fill="#0B7F5C">{$padding}</text>
<rect x="160" y="128" width="320" height="90" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="320" y="179" text-anchor="middle" font-weight="700" font-size="17" fill="#1B66BC">{$content}</text>
</svg>
SVG;
    }
}

/** The anatomy of a rule: selector, declaration, property and value. $t: selector, declaration, property, value, rule. */
if (!function_exists('fig_cssbase_rule')) {
    function fig_cssbase_rule(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g font-family="ui-monospace, Menlo, Consolas, monospace" font-size="17" text-anchor="middle">
<rect x="135" y="86" width="96" height="40" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="183" y="112">.nota</text>
<text x="251" y="112">{</text>
<rect x="271" y="86" width="96" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="319" y="112">color</text>
<text x="380" y="112">:</text>
<rect x="393" y="86" width="86" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="436" y="112">navy</text>
<text x="490" y="112">;</text>
<text x="515" y="112">}</text>
</g>
<g font-weight="700" text-anchor="middle">
<text x="183" y="66" fill="#B84508">{$t[0]}</text>
<path d="M271 80v-8h224v8" fill="none" stroke="#7048E8" stroke-width="2"/>
<text x="383" y="60" fill="#5B37D0">{$t[1]}</text>
<text x="319" y="150" fill="#1B66BC">{$t[2]}</text>
<text x="436" y="150" fill="#0B7F5C">{$t[3]}</text>
</g>
<path d="M135 166v8h385v-8" fill="none" stroke="#8A87B0" stroke-width="2"/>
<text x="327" y="196" text-anchor="middle" fill="#55527A">{$t[4]}</text>
</svg>
SVG;
    }
}

/** block, inline and inline-block side by side. $t: three names' notes. */
if (!function_exists('fig_cssbase_display')) {
    function fig_cssbase_display(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="700" font-size="15">
<text x="20" y="48">block</text><text x="20" y="146">inline</text><text x="20" y="242">inline-block</text>
</g>
<g font-size="13" fill="#55527A"><text x="20" y="68">{$t[0]}</text><text x="20" y="166">{$t[1]}</text><text x="20" y="262">{$t[2]}</text></g>
<path d="M20 100H620M20 196H620" stroke="#E4E2F0" stroke-width="2"/>
<rect x="200" y="18" width="420" height="30" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="216" y="38" font-family="ui-monospace, Menlo, monospace" font-size="14">&lt;p&gt;</text>
<rect x="200" y="56" width="420" height="30" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="216" y="76" font-family="ui-monospace, Menlo, monospace" font-size="14">&lt;div&gt;</text>
<g fill="#D9D7E8"><rect x="200" y="126" width="220" height="12" rx="6"/><rect x="320" y="156" width="300" height="12" rx="6"/><rect x="200" y="226" width="140" height="12" rx="6"/><rect x="474" y="226" width="146" height="12" rx="6"/><rect x="200" y="262" width="360" height="12" rx="6"/></g>
<rect x="428" y="120" width="192" height="24" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<text x="440" y="137" font-family="ui-monospace, Menlo, monospace" font-size="14">{$t[3]}</text>
<rect x="200" y="150" width="112" height="24" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<text x="212" y="167" font-family="ui-monospace, Menlo, monospace" font-size="14">{$t[4]}</text>
<rect x="348" y="204" width="118" height="44" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="407" y="231" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14">a.bottone</text>
</svg>
SVG;
    }
}

/** relative, absolute and sticky in three panels; in the third the content scrolls under the bar that sticks. $t: notes. */
if (!function_exists('fig_cssbase_position')) {
    function fig_cssbase_position(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.bar{animation:stick 7s linear infinite}.flow{animation:flow 7s linear infinite}@keyframes stick{0%{transform:translateY(56px)}35%,100%{transform:translateY(0)}}@keyframes flow{0%{transform:translateY(56px)}100%{transform:translateY(-104px)}}@media (prefers-reduced-motion:reduce){.bar,.flow{animation:none}}</style>
<defs><clipPath id="vp"><rect x="446" y="56" width="168" height="160" rx="8"/></clipPath></defs>
<g fill="#F7F6FC" stroke="#D9D7E8" stroke-width="2"><rect x="12" y="12" width="196" height="276" rx="12"/><rect x="222" y="12" width="196" height="276" rx="12"/><rect x="432" y="12" width="196" height="276" rx="12"/></g>
<g font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="700" text-anchor="middle"><text x="110" y="38">relative</text><text x="320" y="38">absolute</text><text x="530" y="38">sticky</text></g>
<rect x="36" y="86" width="110" height="60" rx="10" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 5"/>
<rect x="66" y="112" width="110" height="60" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<path d="M36 86l24 21" stroke="#E8590C" stroke-width="2"/><path d="M60 107l-9-1 4-6z" fill="#E8590C"/>
<text x="121" y="147" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">.card</text>
<rect x="240" y="60" width="160" height="150" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="252" y="198" font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#1B66BC">relative</text>
<rect x="318" y="70" width="72" height="28" rx="8" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="354" y="89" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">absolute</text>
<g clip-path="url(#vp)">
<rect x="446" y="56" width="168" height="160" fill="#FFFFFF"/>
<g class="flow" fill="#D9D7E8"><rect x="458" y="96" width="140" height="10" rx="5"/><rect x="458" y="116" width="110" height="10" rx="5"/><rect x="458" y="136" width="130" height="10" rx="5"/><rect x="458" y="156" width="96" height="10" rx="5"/><rect x="458" y="176" width="140" height="10" rx="5"/><rect x="458" y="196" width="120" height="10" rx="5"/><rect x="458" y="216" width="136" height="10" rx="5"/><rect x="458" y="236" width="100" height="10" rx="5"/><rect x="458" y="256" width="140" height="10" rx="5"/><rect x="458" y="276" width="116" height="10" rx="5"/><rect x="458" y="296" width="130" height="10" rx="5"/><rect x="458" y="316" width="90" height="10" rx="5"/></g>
<g class="bar"><rect x="446" y="56" width="168" height="30" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="530" y="76" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">.barra</text></g>
</g>
<rect x="446" y="56" width="168" height="160" rx="8" fill="none" stroke="#8A87B0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" text-anchor="middle"><text x="110" y="234">top: 20px; left: 30px</text><text x="320" y="234">top: 8px; right: 8px</text><text x="530" y="240">top: 0</text></g>
<g text-anchor="middle" fill="#55527A" font-size="13"><text x="110" y="258">{$t[0]}</text><text x="110" y="276">{$t[1]}</text><text x="320" y="258">{$t[2]}</text><text x="320" y="276">{$t[3]}</text><text x="530" y="262">{$t[4]}</text></g>
</svg>
SVG;
    }
}

/** Four rules with their specificity scores, the id wins. $t: id, class, tag, wins. */
if (!function_exists('fig_cssbase_specificity')) {
    function fig_cssbase_specificity(array $t): string
    {
        $row = function (int $y, string $sel, string $decl, array $n) {
            $s = "<text x=\"34\" y=\"" . ($y + 25) . "\" font-family=\"ui-monospace, Menlo, monospace\" font-weight=\"700\">$sel</text>"
               . "<text x=\"120\" y=\"" . ($y + 25) . "\" font-family=\"ui-monospace, Menlo, monospace\" fill=\"#55527A\">$decl</text>";
            foreach ([[318, '#ECE6FF', '#7048E8'], [390, '#DCEBFF', '#2F8CF0'], [462, '#FFE8D6', '#E8590C']] as $i => [$x, $f, $k]) {
                $s .= "<rect x=\"$x\" y=\"" . ($y + 4) . "\" width=\"56\" height=\"30\" rx=\"8\" fill=\"" . ($n[$i] ? $f : '#FFFFFF') . "\" stroke=\"$k\" stroke-width=\"2\"/>"
                    . "<text x=\"" . ($x + 28) . "\" y=\"" . ($y + 25) . "\" text-anchor=\"middle\" font-weight=\"700\" font-size=\"16\">{$n[$i]}</text>";
            }
            return $s;
        };
        $rows = $row(58, 'p', 'color: black', [0, 0, 1]) . $row(106, '.nota', 'color: blue', [0, 1, 0])
              . $row(154, 'p.nota', 'color: green', [0, 1, 1]) . $row(202, '#box', 'color: red', [1, 0, 0]);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g font-weight="700" text-anchor="middle" font-size="14"><text x="346" y="44" fill="#5B37D0">{$t[0]}</text><text x="418" y="44" fill="#1B66BC">{$t[1]}</text><text x="490" y="44" fill="#B84508">{$t[2]}</text></g>
<rect x="20" y="200" width="600" height="42" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
$rows
<text x="560" y="227" text-anchor="middle" font-weight="700" fill="#0B7F5C">{$t[3]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'css-base', 'family' => 'css', 'after' => 'html-base',
    'title' => L('CSS base', 'CSS Basics'), 'short' => L('CSS base', 'CSS Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Dai forma alle pagine: selettori, colori, testi, box model, posizionamento, cascata, sfondi, unità di misura e dimensioni flessibili.', 'Give pages their shape: selectors, colours, text, box model, positioning, the cascade, backgrounds, units and flexible sizes.'),
    'lessons' => [
        lesson('selectors', L('Regole e selettori', 'Rules and selectors'), [
            P('Il CSS descrive l’**aspetto** della pagina con delle regole. Una regola ha un selettore, che sceglie gli elementi, e delle dichiarazioni `proprietà: valore`.', 'CSS describes the **look** of the page with rules. A rule has a selector, which picks the elements, and `property: value` declarations.'),
            Fig(L(fig_cssbase_rule(['selettore', 'dichiarazione', 'proprietà', 'valore', 'regola = selettore + { dichiarazioni }']), fig_cssbase_rule(['selector', 'declaration', 'property', 'value', 'rule = selector + { declarations }'])),
                L('La regola `.nota { color: navy; }` scomposta: `.nota` è il selettore; `color: navy;` è una dichiarazione, fatta della proprietà `color` e del valore `navy`.', 'The rule `.nota { color: navy; }` taken apart: `.nota` is the selector; `color: navy;` is a declaration, made of the property `color` and the value `navy`.'),
                L('Il selettore sceglie *chi*, le dichiarazioni dicono *come*.', 'The selector picks *who*, the declarations say *how*.')),
            C('p { color: navy; }          /* tutti i paragrafi */
.nota { font-size: 14px; }  /* la classe nota */
#box { width: 200px; }      /* l’id box */
nav a { color: red; }       /* i link dentro nav */
a:hover { color: green; }   /* il link sotto al mouse */', 'css', L('<p>Un paragrafo blu scuro.</p>
<p class="nota">Una nota più piccola.</p>
<div id="box" style="background:#ffe8d6">Il box largo 200px</div>
<nav><a href="#">Link nel menu: rosso</a></nav>
<p><a href="#">Link fuori dal menu: passaci sopra</a></p>', '<p>A dark blue paragraph.</p>
<p class="nota">A smaller note.</p>
<div id="box" style="background:#ffe8d6">The box, 200px wide</div>
<nav><a href="#">Link in the menu: red</a></nav>
<p><a href="#">Link outside the menu: hover over it</a></p>')),
            P('Una classe si riusa quante volte vuoi, un `id` è unico nella pagina. `:hover` è una pseudo-classe: indica uno stato dell’elemento. Lo spazio tra due selettori significa «dentro».', 'A class can be reused as often as you want, an `id` is unique in the page. `:hover` is a pseudo-class: it marks a state of the element. The space between two selectors means “inside”.'),
        ], [
            Q('s1', L('Quale selettore sceglie la classe `nota`?', 'Which selector picks the class `nota`?'), ['#nota', '.nota', 'nota', '*nota'], 1, L('Il punto indica la classe, il cancelletto l’id.', 'The dot marks a class, the hash an id.')),
            Q('s2', L('Che cosa sceglie `nav a`?', 'What does `nav a` pick?'), [L('`nav` e tutti i link', '`nav` and all the links'), L('I link dentro `nav`', 'The links inside `nav`'), L('Solo il primo link', 'Only the first link'), L('Nulla', 'Nothing')], 1, L('Lo spazio è il combinatore discendente.', 'The space is the descendant combinator.')),
            Q('s3', L('Che cos’è `:hover`?', 'What is `:hover`?'), [L('Una pseudo-classe', 'A pseudo-class'), L('Una proprietà', 'A property'), L('Un id', 'An id'), L('Un tag', 'A tag')], 0, L('Le pseudo-classi indicano uno stato o una posizione.', 'Pseudo-classes mark a state or a position.')),
        ], task('css', L('Colora i paragrafi con la classe `nota` di rosso (`red`) e imposta a `200px` la larghezza dell’id `box`.', 'Colour the paragraphs with the `nota` class red (`red`) and set the width of the `box` id to `200px`.'), '/* Scrivi qui le tue regole */
', [css('.nota', 'color', 'rgb(255, 0, 0)', L('La nota è rossa', 'The note is red')), css('#box', 'width', '200px', L('Il box è largo 200px', 'The box is 200px wide'))], $page)),

        lesson('colors', L('Colori e testi', 'Colours and text'), [
            P('Un colore si può scrivere in molti modi: per nome, in esadecimale, in `rgb()` o in `hsl()`. Un ultimo valore dopo la barra indica la trasparenza.', 'A colour can be written in many ways: by name, in hexadecimal, in `rgb()` or in `hsl()`. A final value after a slash sets the transparency.'),
            C('h1 {
  color: #E8590C;
  background-color: rgb(255 240 230);
  font-family: Georgia, serif;
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.3;
  text-align: center;
}', 'css', L('<h1>Un titolo arancione</h1>', '<h1>An orange heading</h1>')),
            P('`font-family` indica un elenco di caratteri, dal preferito al generico. `rem` è relativo alla dimensione di base del documento: si adatta alle impostazioni dell’utente meglio di `px`.', '`font-family` lists fonts, from the preferred one to a generic one. `rem` is relative to the base size of the document: it respects user settings better than `px`.'),
            Tip('Controlla sempre il contrasto: un testo poco leggibile è un testo perso.', 'Always check the contrast: text that is hard to read is lost text.'),
        ], [
            Q('c1', L('Quale proprietà cambia il colore del testo?', 'Which property changes the text colour?'), ['text-color', 'font-color', 'color', 'foreground'], 2, L('Si chiama semplicemente `color`.', 'It is simply called `color`.')),
            Q('c2', L('Quanto vale `2rem` se la base è 16px?', 'What is `2rem` when the base is 16px?'), ['2px', '16px', '32px', '200px'], 2, L('`rem` moltiplica la dimensione di base: 2 × 16.', '`rem` multiplies the base size: 2 × 16.')),
            Q('c3', L('Perché `font-family` elenca più caratteri?', 'Why does `font-family` list several fonts?'), [L('Li usa tutti insieme', 'It uses them all together'), L('Se il primo manca usa il successivo', 'If the first is missing it uses the next'), L('Per il grassetto', 'For bold'), L('È obbligatorio', 'It is mandatory')], 1, L('L’ultimo di solito è una famiglia generica come `serif`.', 'The last one is usually a generic family such as `serif`.')),
        ], task('css', L('Centra il testo di `.titolo` e rendilo di colore `#E8590C`.', 'Centre the text of `.titolo` and give it the colour `#E8590C`.'), '.titolo {

}', [css('.titolo', 'text-align', 'center', L('Il titolo è centrato', 'The heading is centred')), css('.titolo', 'color', 'rgb(232, 89, 12)', L('Il colore è #E8590C', 'The colour is #E8590C'))], $page)),

        lesson('box', L('Il box model', 'The box model'), [
            P('Ogni elemento è una scatola fatta di quattro strati: il **contenuto**, il `padding` (spazio interno), il `border` e il `margin` (spazio esterno).', 'Every element is a box made of four layers: the **content**, the `padding` (inner space), the `border` and the `margin` (outer space).'),
            Fig(L(box_model_svg('margine', 'bordo', 'padding', 'contenuto'), box_model_svg('margin', 'border', 'padding', 'content')),
                L('Quattro rettangoli uno dentro l’altro: all’esterno il margine tratteggiato, poi il bordo scuro, poi il padding verde, al centro il contenuto azzurro.', 'Four rectangles one inside the other: outside the dashed margin, then the dark border, then the green padding, the blue content in the middle.'),
                L('Dall’esterno all’interno: margine, bordo, padding, contenuto.', 'From the outside in: margin, border, padding, content.')),
            C('* { box-sizing: border-box; }

.card {
  width: 300px;
  padding: 16px;
  border: 1px solid #ccc;
  margin: 0 auto;
  border-radius: 12px;
}', 'css', L('<div class="card">Una card larga 300px, padding compreso</div>', '<div class="card">A card 300px wide, padding included</div>')),
            P('Con `box-sizing: border-box` la larghezza comprende padding e bordo, ed è molto più facile fare i conti. `margin: 0 auto` centra un blocco con una larghezza. Con due valori, il primo è sopra e sotto, il secondo a destra e sinistra.', 'With `box-sizing: border-box` the width includes padding and border, which makes the maths far easier. `margin: 0 auto` centres a block with a width. With two values, the first is top and bottom, the second right and left.'),
        ], [
            Q('b1', L('Quale strato è lo spazio interno?', 'Which layer is the inner space?'), ['margin', 'padding', 'border', 'outline'], 1, L('Il `padding` sta tra il contenuto e il bordo.', '`padding` sits between the content and the border.')),
            Q('b2', L('Con `border-box`, una scatola `width: 300px` e `padding: 20px` è larga…', 'With `border-box`, a box with `width: 300px` and `padding: 20px` is…'), [L('300px', '300px wide'), L('320px', '320px wide'), L('340px', '340px wide'), L('260px', '260px wide')], 0, L('Il padding sta dentro i 300px: resta meno spazio al contenuto.', 'The padding fits within the 300px: less room remains for the content.')),
            Q('b3', L('Che cosa fa `margin: 0 auto`?', 'What does `margin: 0 auto` do?'), [L('Toglie il bordo', 'Removes the border'), L('Centra orizzontalmente un blocco', 'Centres a block horizontally'), L('Centra il testo', 'Centres the text'), L('Nasconde il blocco', 'Hides the block')], 1, L('I margini laterali automatici si dividono lo spazio libero.', 'Automatic side margins share the free space.')),
        ], task('css', L('Dai a `#box` un `padding` di `20px` e un `border-radius` di `12px`.', 'Give `#box` a `padding` of `20px` and a `border-radius` of `12px`.'), '#box {
  background: #ffe8d6;
}', [css('#box', 'padding-top', '20px', L('Il padding è 20px', 'The padding is 20px')), css('#box', 'border-top-left-radius', '12px', L('Gli angoli sono arrotondati', 'The corners are rounded'))], $page)),

        lesson('display', L('Display: blocchi e righe', 'Display: blocks and inlines'), [
            P('`display` decide come si comporta la scatola. Un elemento `block` occupa tutta la riga, come `<p>` e `<div>`. Un elemento `inline` scorre nel testo, come `<a>` e `<strong>`, e ignora `width` e `height`.', '`display` decides how the box behaves. A `block` element takes the whole line, like `<p>` and `<div>`. An `inline` element flows in the text, like `<a>` and `<strong>`, and ignores `width` and `height`.'),
            Fig(L(fig_cssbase_display(['tutta la riga, in pila', 'scorre nel testo', 'nel testo, con misure', '&lt;a&gt; un link che va', 'a capo&lt;/a&gt;'])
                    , fig_cssbase_display(['whole line, stacked', 'flows in the text', 'in the text, with sizes', '&lt;a&gt; a link that', 'wraps&lt;/a&gt;'])),
                L('Tre righe a confronto: due blocchi che occupano tutta la larghezza uno sotto l’altro; un link inline che scorre nel testo e va a capo; un bottone inline-block, più alto, seduto nella riga del testo.', 'Three rows compared: two blocks taking the full width one below the other; an inline link flowing in the text and wrapping onto the next line; a taller inline-block button sitting in the line of text.'),
                L('Solo `block` e `inline-block` accettano `width` e `height`.', 'Only `block` and `inline-block` accept `width` and `height`.')),
            C('a.bottone { display: inline-block; padding: 8px 16px; }
.nascosto { display: none; }
.invisibile { visibility: hidden; }', 'css', L('<p>Testo con <a class="bottone" href="#" style="background:#ffe8d6">un bottone</a> in mezzo alla riga.</p>
<p class="nascosto">Questo paragrafo non si vede.</p>
<p>Prima [<span class="invisibile">nascosto</span>] dopo: lo spazio resta.</p>', '<p>Text with <a class="bottone" href="#" style="background:#ffe8d6">a button</a> in the middle of the line.</p>
<p class="nascosto">This paragraph is not shown.</p>
<p>Before [<span class="invisibile">hidden</span>] after: the space stays.</p>')),
            P('`inline-block` scorre nel testo ma accetta le dimensioni. `display: none` toglie l’elemento dalla pagina, mentre `visibility: hidden` lo nasconde lasciando il suo spazio.', '`inline-block` flows in the text but accepts sizes. `display: none` takes the element off the page, while `visibility: hidden` hides it and leaves its space.'),
        ], [
            Q('d1', L('Quale elemento è `inline` di natura?', 'Which element is `inline` by default?'), ['<div>', '<p>', '<a>', '<h1>'], 2, L('`<a>` scorre nel testo senza andare a capo.', '`<a>` flows in the text without a line break.')),
            Q('d2', L('Qual è la differenza tra `display: none` e `visibility: hidden`?', 'What is the difference between `display: none` and `visibility: hidden`?'), [L('Nessuna', 'None'), L('Il primo toglie anche lo spazio', 'The first removes the space too'), L('Il secondo toglie lo spazio', 'The second removes the space'), L('Il primo è solo per il testo', 'The first is only for text')], 1, L('`visibility: hidden` lascia un buco della stessa misura.', '`visibility: hidden` leaves a hole of the same size.')),
            Q('d3', L('Quale valore scorre nel testo e accetta `width`?', 'Which value flows in the text and accepts `width`?'), ['block', 'inline', 'inline-block', 'flow'], 2, L('È una via di mezzo tra `inline` e `block`.', 'It is halfway between `inline` and `block`.')),
        ], task('css', L('Fai diventare il link un `inline-block` con `padding` di `10px`, e nascondi `.nota` con `display: none`.', 'Turn the link into an `inline-block` with `10px` of `padding`, and hide `.nota` with `display: none`.'), '', [css('a', 'display', 'inline-block', L('Il link è inline-block', 'The link is inline-block')), css('a', 'padding-left', '10px', L('Il link ha 10px di padding', 'The link has 10px of padding')), css('.nota', 'display', 'none', L('La nota è nascosta', 'The note is hidden'))], $page)),

        lesson('position', L('Il posizionamento', 'Positioning'), [
            P('`position` cambia il modo in cui un elemento si colloca rispetto al flusso normale. Il valore predefinito è `static`. Con `relative` l’elemento resta al suo posto e fa da riferimento per i figli `absolute`.', '`position` changes how an element is placed relative to the normal flow. The default value is `static`. With `relative` the element stays in place and acts as a reference for `absolute` children.'),
            Fig(L(fig_cssbase_position(['resta nel flusso:', 'si sposta il disegno', 'fuori dal flusso,', 'contro il genitore', 'scorre, poi si ferma']), fig_cssbase_position(['stays in the flow:', 'only the drawing moves', 'out of the flow,', 'against its parent', 'scrolls, then sticks'])),
                L('Tre riquadri. Relative: la scatola è spostata di 20px in basso e 30px a destra, e il suo posto originale resta vuoto, tratteggiato. Absolute: un’etichetta nell’angolo in alto a destra di un contenitore relative. Sticky: una barra in cima a un’area che scorre, ferma mentre il contenuto le passa sotto.', 'Three panels. Relative: the box is moved 20px down and 30px right, and its original place stays empty, dashed. Absolute: a label in the top right corner of a relative container. Sticky: a bar at the top of a scrolling area, still while the content passes under it.'),
                L('`absolute` si misura dal primo antenato posizionato, `sticky` dalla soglia `top`.', '`absolute` is measured from the nearest positioned ancestor, `sticky` from its `top` threshold.')),
            C('.card { position: relative; }
.etichetta { position: absolute; top: 8px; right: 8px; }
.barra { position: sticky; top: 0; }
.chat { position: fixed; bottom: 16px; right: 16px; z-index: 10; }', 'css', L('<div class="card" style="border:2px solid #2f8cf0;padding:24px;border-radius:12px">Una card <span class="etichetta" style="background:#ffe3ee;padding:2px 8px">Nuovo</span></div>
<button class="chat">Chat</button>', '<div class="card" style="border:2px solid #2f8cf0;padding:24px;border-radius:12px">A card <span class="etichetta" style="background:#ffe3ee;padding:2px 8px">New</span></div>
<button class="chat">Chat</button>')),
            P('`fixed` resta fermo rispetto allo schermo, `sticky` scorre e poi si ferma alla soglia indicata. `z-index` decide chi sta sopra tra gli elementi posizionati.', '`fixed` stays still relative to the screen, `sticky` scrolls and then stops at the given threshold. `z-index` decides who is on top among positioned elements.'),
            Tip('Un `absolute` senza un antenato posizionato si misura dalla pagina intera e finisce chissà dove: per questo quasi sempre lo accompagna un `position: relative` sul contenitore. `sticky` invece non funziona se un antenato ha `overflow: hidden`, e senza una soglia come `top: 0` non si ferma mai.', 'An `absolute` with no positioned ancestor is measured from the whole page and ends up who knows where: that is why it is nearly always paired with `position: relative` on the container. `sticky`, on the other hand, does not work if an ancestor has `overflow: hidden`, and without a threshold such as `top: 0` it never sticks.'),
        ], [
            Q('p1', L('Rispetto a cosa si posiziona un elemento `absolute`?', 'What is an `absolute` element positioned against?'), [L('Sempre la pagina', 'Always the page'), L('Il primo antenato posizionato', 'The nearest positioned ancestor'), L('L’elemento precedente', 'The previous element'), L('Lo schermo', 'The screen')], 1, L('Per questo si mette `position: relative` sul contenitore.', 'That is why you set `position: relative` on the container.')),
            Q('p2', L('Quale valore resta fermo durante lo scorrimento della pagina?', 'Which value stays still while the page scrolls?'), ['static', 'relative', 'fixed', 'float'], 2, L('`fixed` si aggancia allo schermo.', '`fixed` is anchored to the screen.')),
            Q('p3', L('Quale proprietà decide cosa sta sopra?', 'Which property decides what is on top?'), ['z-index', 'layer', 'top', 'order-top'], 0, L('Funziona sugli elementi posizionati e sui figli di flex e grid.', 'It works on positioned elements and on flex and grid children.')),
        ], task('css', L('Rendi `#box` `relative` e sposta `.nota` in `absolute` con `top: 0`.', 'Make `#box` `relative` and move `.nota` to `absolute` with `top: 0`.'), '', [css('#box', 'position', 'relative', L('Il box è relative', 'The box is relative')), css('.nota', 'position', 'absolute', L('La nota è absolute', 'The note is absolute')), css('.nota', 'top', '0px', L('La nota è in alto', 'The note is at the top'))], $page)),

        lesson('cascade', L('Cascata e specificità', 'Cascade and specificity'), [
            P('Quando più regole riguardano lo stesso elemento, vince quella con la **specificità** maggiore. L’`id` batte la classe, la classe batte il tag. A parità di specificità vince l’ultima scritta.', 'When several rules target the same element, the one with the higher **specificity** wins. An `id` beats a class, a class beats a tag. With equal specificity the last one written wins.'),
            C('p { color: black; }        /* 0,0,1 */
.nota { color: blue; }     /* 0,1,0 → vince */
p.nota { color: green; }   /* 0,1,1 → vince ancora */
#box { color: red; }       /* 1,0,0 → batte tutte */', 'css', L('<p>Un paragrafo: nero</p>
<p class="nota">p.nota: verde, batte .nota</p>
<p class="nota" id="box">#box: rosso, batte tutte</p>', '<p>A paragraph: black</p>
<p class="nota">p.nota: green, beats .nota</p>
<p class="nota" id="box">#box: red, beats them all</p>')),
            P('I tre numeri si contano così: quanti `id`, quante classi (con attributi e pseudo-classi), quanti tag. Si confrontano da sinistra, come le cifre di un numero: un solo `id` batte qualsiasi quantità di classi.', 'The three numbers are counted like this: how many `id`s, how many classes (attributes and pseudo-classes included), how many tags. They are compared from the left, like the digits of a number: a single `id` beats any number of classes.'),
            Fig(L(fig_cssbase_specificity(['id', 'classi', 'tag', 'vince']), fig_cssbase_specificity(['id', 'classes', 'tags', 'wins'])),
                L('Una tabella con quattro regole e i loro punteggi id, classi, tag: `p` 0,0,1; `.nota` 0,1,0; `p.nota` 0,1,1; `#box` 1,0,0, evidenziata come vincitrice.', 'A table of four rules with their id, class and tag scores: `p` 0,0,1; `.nota` 0,1,0; `p.nota` 0,1,1; `#box` 1,0,0, highlighted as the winner.'),
                L('Si legge da sinistra: la prima colonna diversa decide.', 'Read from the left: the first column that differs decides.')),
            P('Alcune proprietà, come `color` e `font-family`, si **ereditano** dai genitori; altre, come `margin` e `border`, no. `!important` scavalca tutto: evitalo, perché rende il CSS difficile da correggere.', 'Some properties, like `color` and `font-family`, are **inherited** from parents; others, like `margin` and `border`, are not. `!important` overrides everything: avoid it, because it makes CSS hard to fix.'),
        ], [
            Q('k1', L('Chi vince tra `.nota` e `#box` sullo stesso elemento?', 'Which wins between `.nota` and `#box` on the same element?'), ['.nota', '#box', L('L’ultima scritta', 'The last one written'), L('Nessuna', 'Neither')], 1, L('L’`id` ha una specificità più alta della classe.', 'An `id` has higher specificity than a class.')),
            Q('k2', L('Con la stessa specificità chi vince?', 'With the same specificity, which wins?'), [L('La prima scritta', 'The first written'), L('L’ultima scritta', 'The last written'), L('La più corta', 'The shortest'), L('Nessuna', 'Neither')], 1, L('È il senso della parola «cascata».', 'That is the meaning of the word “cascade”.')),
            Q('k3', L('Quale proprietà si eredita?', 'Which property is inherited?'), ['margin', 'border', 'color', 'padding'], 2, L('Il colore del testo passa dai genitori ai figli.', 'The text colour passes from parents to children.')),
        ], task('css', L('Rendi blu (`blue`) il testo di `p.nota` senza toccare la regola esistente e senza `!important`.', 'Make the text of `p.nota` blue (`blue`) without touching the existing rule and without `!important`.'), '.nota { color: red; }

', [css('p.nota', 'color', 'rgb(0, 0, 255)', L('La nota è blu', 'The note is blue')), re('(?<![\s\S])(?![\s\S]*!important)', L('Niente `!important`', 'No `!important`'))], $page)),
    ],
];
