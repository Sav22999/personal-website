<?php
/** CSS avanzato: flexbox, grid, responsive design, custom properties and animation; pseudo-elements, modern CSS, cascade layers, grid areas and modern colours are in extra-levels.php. */

$cards = '<div class="lista">
  <div class="card">Uno</div>
  <div class="card">Due</div>
  <div class="card">Tre</div>
</div>';

/** Flexbox: main and cross axis, and what justify-content does along the main one. $t: main axis, cross axis. */
if (!function_exists('fig_cssadv_flex')) {
    function fig_cssadv_flex(array $t): string
    {
        $row = function (int $y, string $name, array $xs) {
            $o = "<text x=\"356\" y=\"" . ($y - 8) . "\" font-family=\"ui-monospace, Menlo, monospace\" font-size=\"13\">$name</text>"
               . "<rect x=\"356\" y=\"$y\" width=\"264\" height=\"40\" rx=\"8\" fill=\"#F7F6FC\" stroke=\"#8A87B0\" stroke-width=\"2\"/>";
            foreach ($xs as $x) $o .= "<rect x=\"$x\" y=\"" . ($y + 6) . "\" width=\"50\" height=\"28\" rx=\"6\" fill=\"#FFE8D6\" stroke=\"#E8590C\" stroke-width=\"2\"/>";
            return $o;
        };
        $r = $row(48, 'flex-start', [362, 418, 474]) . $row(134, 'center', [409, 465, 521]) . $row(220, 'space-between', [362, 463, 564]);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="20" y="30" font-family="ui-monospace, Menlo, monospace" font-weight="700">display: flex</text>
<rect x="20" y="44" width="260" height="150" rx="12" fill="#F7F6FC" stroke="#2F8CF0" stroke-width="2"/>
<rect x="36" y="74" width="62" height="70" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<rect x="118" y="88" width="62" height="42" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<rect x="200" y="64" width="62" height="90" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" text-anchor="middle"><text x="67" y="114">1</text><text x="149" y="114">2</text><text x="231" y="114">3</text></g>
<path d="M30 216H266" stroke="#E8590C" stroke-width="3"/><path d="M274 216l-12-6v12z" fill="#E8590C"/>
<text x="20" y="240" font-weight="700" fill="#B84508">{$t[0]}</text><text x="20" y="260" font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#B84508">justify-content</text>
<path d="M296 52V180" stroke="#2F8CF0" stroke-width="3"/><path d="M296 188l-6-12h12z" fill="#2F8CF0"/>
<text x="316" y="120" transform="rotate(90 316 120)" text-anchor="middle" font-size="13" fill="#1B66BC"><tspan font-weight="700">{$t[1]}</tspan> · <tspan font-family="ui-monospace, Menlo, monospace">align-items</tspan></text>
$r
</svg>
SVG;
    }
}

/** Grid: numbered lines, columns 200px 1fr 1fr, an item from line 1 to line 3. Labels are code and numbers. */
if (!function_exists('fig_cssadv_grid')) {
    function fig_cssadv_grid(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="ui-monospace, Menlo, Consolas, monospace" font-size="14" fill="#1E1C52">
<g stroke="#7048E8" stroke-width="2" stroke-dasharray="5 4"><path d="M40 40V206M226 40V206M416 40V206M600 40V206"/></g>
<g fill="#ECE6FF" stroke="#7048E8" stroke-width="2"><circle cx="40" cy="28" r="13"/><circle cx="226" cy="28" r="13"/><circle cx="416" cy="28" r="13"/><circle cx="600" cy="28" r="13"/></g>
<g text-anchor="middle" font-weight="700" font-size="13"><text x="40" y="33">1</text><text x="226" y="33">2</text><text x="416" y="33">3</text><text x="600" y="33">4</text></g>
<rect x="40" y="54" width="370" height="64" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<text x="225" y="91" text-anchor="middle">.hero { grid-column: 1 / 3 }</text>
<rect x="422" y="54" width="178" height="64" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"><rect x="40" y="130" width="180" height="64" rx="8"/><rect x="232" y="130" width="178" height="64" rx="8"/><rect x="422" y="130" width="178" height="64" rx="8"/></g>
<g text-anchor="middle" fill="#55527A"><text x="130" y="232">200px</text><text x="321" y="232">1fr</text><text x="511" y="232">1fr</text></g>
<text x="416" y="248" text-anchor="middle" font-size="12" fill="#7048E8">gap: 12px</text>
</svg>
SVG;
    }
}

/** Mobile first: base rules for every screen, a media query that adds columns from 700px. $t: labels. */
if (!function_exists('fig_cssadv_mobile')) {
    function fig_cssadv_mobile(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="40" y="20" width="110" height="180" rx="14" fill="#F7F6FC" stroke="#8A87B0" stroke-width="2"/>
<g fill="#FFE8D6" stroke="#E8590C" stroke-width="2"><rect x="54" y="40" width="82" height="40" rx="6"/><rect x="54" y="90" width="82" height="40" rx="6"/><rect x="54" y="140" width="82" height="40" rx="6"/></g>
<rect x="300" y="20" width="320" height="180" rx="14" fill="#F7F6FC" stroke="#8A87B0" stroke-width="2"/>
<g fill="#FFE8D6" stroke="#E8590C" stroke-width="2"><rect x="316" y="40" width="138" height="66" rx="6"/><rect x="466" y="40" width="138" height="66" rx="6"/><rect x="316" y="116" width="138" height="66" rx="6"/></g>
<path d="M20 228H620" stroke="#8A87B0" stroke-width="2"/><path d="M626 228l-10-5v10z" fill="#8A87B0"/>
<path d="M240 218v20" stroke="#7048E8" stroke-width="3"/>
<text x="20" y="250" font-family="ui-monospace, Menlo, monospace" font-size="13">0</text><text x="240" y="250" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#5B37D0" font-weight="700">700px</text>
<rect x="20" y="258" width="600" height="0"/>
<text x="95" y="270" text-anchor="middle" font-size="13" fill="#55527A">{$t[0]}</text>
<text x="460" y="270" text-anchor="middle" font-size="13" fill="#5B37D0">{$t[1]}</text>
<text x="95" y="216" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12">1 col</text>
<text x="460" y="216" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12">1fr 1fr</text>
</svg>
SVG;
    }
}

return [
    'id' => 'css-advanced', 'family' => 'css', 'after' => 'css-intermediate',
    'title' => L('CSS avanzato', 'Advanced CSS'), 'short' => L('CSS avanzato', 'Advanced CSS'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Impaginazioni moderne: flexbox, grid e aree, responsive, variabili, animazioni, :has() e container query, livelli a cascata e colori moderni.', 'Modern layouts: flexbox, grid and areas, responsive design, variables, animations, :has() and container queries, cascade layers and modern colours.'),
    'lessons' => [
        lesson('flex', L('Flexbox', 'Flexbox'), [
            P('Flexbox dispone i figli lungo **una dimensione**. Si attiva sul contenitore con `display: flex`.', 'Flexbox lays out children along **one dimension**. You turn it on for the container with `display: flex`.'),
            C('.lista {
  display: flex;
  flex-direction: row;
  justify-content: space-between; /* asse principale */
  align-items: center;            /* asse trasversale */
  gap: 16px;
  flex-wrap: wrap;
}
.card { flex: 1; }', 'css', L('<div class="lista" style="background:#f1f0f8;padding:8px">
  <div class="card" style="background:#ffe8d6;padding:8px">Uno</div>
  <div class="card" style="background:#ffe8d6;padding:24px 8px">Due, più alta</div>
  <div class="card" style="background:#ffe8d6;padding:8px">Tre</div>
</div>', '<div class="lista" style="background:#f1f0f8;padding:8px">
  <div class="card" style="background:#ffe8d6;padding:8px">One</div>
  <div class="card" style="background:#ffe8d6;padding:24px 8px">Two, taller</div>
  <div class="card" style="background:#ffe8d6;padding:8px">Three</div>
</div>')),
            P('`justify-content` distribuisce lungo l’asse principale, `align-items` lungo quello perpendicolare. `gap` è lo spazio tra i figli. `flex: 1` su un figlio gli fa prendere lo spazio libero.', '`justify-content` distributes along the main axis, `align-items` along the cross axis. `gap` is the space between children. `flex: 1` on a child makes it take the free space.'),
            Fig(L(fig_cssadv_flex(['asse principale', 'asse trasversale']), fig_cssadv_flex(['main axis', 'cross axis'])),
                L('A sinistra un contenitore flex con tre figli di altezze diverse in fila: una freccia orizzontale indica l’asse principale, governato da `justify-content`; una freccia verticale l’asse trasversale, governato da `align-items`. A destra tre righe mostrano `justify-content` con `flex-start` (tutti a sinistra), `center` (al centro) e `space-between` (agli estremi, con lo spazio in mezzo).', 'On the left a flex container with three children of different heights in a row: a horizontal arrow marks the main axis, governed by `justify-content`; a vertical arrow the cross axis, governed by `align-items`. On the right three rows show `justify-content` with `flex-start` (all on the left), `center` (in the middle) and `space-between` (at the ends, with the space in between).'),
                L('Due assi, due proprietà: `justify-` lungo la riga, `align-` di traverso.', 'Two axes, two properties: `justify-` along the row, `align-` across it.')),
            Tip('Con `flex-direction: column` gli assi si scambiano: `justify-content` lavora in verticale.', 'With `flex-direction: column` the axes swap: `justify-content` works vertically.'),
        ], [
            Q('f1', L('Quale proprietà distribuisce lungo l’asse principale?', 'Which property distributes along the main axis?'), ['align-items', 'justify-content', 'flex-wrap', 'gap'], 1, L('Con `flex-direction: row` l’asse principale è orizzontale.', 'With `flex-direction: row` the main axis is horizontal.')),
            Q('f2', L('Dove si scrive `display: flex`?', 'Where do you write `display: flex`?'), [L('Sul contenitore', 'On the container'), L('Su ogni figlio', 'On each child'), L('Su `html`', 'On `html`'), L('Sul primo figlio', 'On the first child')], 0, L('Il contenitore diventa flex e i figli diretti ne seguono le regole.', 'The container becomes flex and its direct children follow its rules.')),
            Q('f3', L('Come centri un elemento in entrambi i sensi?', 'How do you centre an element both ways?'), ['margin: center', 'align: middle', L('`justify-content` e `align-items` su `center`', '`justify-content` and `align-items` set to `center`'), 'float: center'], 2, L('Sono i due assi del contenitore flex.', 'They are the two axes of the flex container.')),
        ], task('css', L('Disponi `.lista` in flex, con `gap` di `16px` e `justify-content: space-between`.', 'Lay out `.lista` with flex, with a `gap` of `16px` and `justify-content: space-between`.'), '.card { background: #ffe8d6; padding: 12px; }

.lista {

}', [css('.lista', 'display', 'flex', L('La lista è flex', 'The list is flex')), css('.lista', 'row-gap', '16px', L('Il gap è 16px', 'The gap is 16px')), css('.lista', 'justify-content', 'space-between', L('I figli sono distribuiti', 'The children are distributed'))], $cards)),

        lesson('grid', L('CSS Grid', 'CSS Grid'), [
            P('Grid dispone gli elementi in **due dimensioni**: righe e colonne insieme. L’unità `fr` è una frazione dello spazio libero.', 'Grid lays out elements in **two dimensions**: rows and columns together. The `fr` unit is a fraction of the free space.'),
            Fig(fig_cssadv_grid(),
                L('Una griglia di tre colonne, larghe 200px, 1fr e 1fr, con le linee numerate da 1 a 4 sopra. Nella prima riga `.hero` va dalla linea 1 alla linea 3 e occupa le prime due colonne; nella seconda riga tre celle normali.', 'A three-column grid, 200px, 1fr and 1fr wide, with the lines numbered 1 to 4 above. In the first row `.hero` goes from line 1 to line 3 and takes the first two columns; in the second row three normal cells.'),
                L('Si contano le linee, non le colonne: tre colonne hanno quattro linee.', 'You count lines, not columns: three columns have four lines.')),
            C('.lista {
  display: grid;
  grid-template-columns: 200px 1fr 1fr;
  gap: 12px;
}
.hero { grid-column: 1 / 3; } /* dalla linea 1 alla 3 */

/* Colonne che si adattano da sole */
.galleria {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}', 'css', L('<div class="lista">
  <div class="hero" style="background:#ffe8d6;padding:8px">.hero: dalla linea 1 alla 3</div>
  <div style="background:#dcebff;padding:8px">C</div>
  <div style="background:#dcebff;padding:8px">200px</div>
  <div style="background:#dcebff;padding:8px">1fr</div>
  <div style="background:#dcebff;padding:8px">1fr</div>
</div>', '<div class="lista">
  <div class="hero" style="background:#ffe8d6;padding:8px">.hero: from line 1 to 3</div>
  <div style="background:#dcebff;padding:8px">C</div>
  <div style="background:#dcebff;padding:8px">200px</div>
  <div style="background:#dcebff;padding:8px">1fr</div>
  <div style="background:#dcebff;padding:8px">1fr</div>
</div>')),
            P('`repeat(3, 1fr)` crea tre colonne uguali. `minmax()` fissa un minimo e un massimo: insieme a `auto-fit` ottieni una griglia responsive senza media query.', '`repeat(3, 1fr)` creates three equal columns. `minmax()` sets a minimum and a maximum: together with `auto-fit` you get a responsive grid with no media queries.'),
            Tip('Flexbox per una riga di elementi, Grid per una vera impaginazione a righe e colonne.', 'Flexbox for a row of items, Grid for a real layout of rows and columns.'),
        ], [
            Q('g1', L('Che cos’è l’unità `fr`?', 'What is the `fr` unit?'), [L('Una frazione dello spazio libero', 'A fraction of the free space'), L('Un pixel', 'A pixel'), L('La larghezza del font', 'The width of the font'), L('Un frame', 'A frame')], 0, L('`1fr 2fr` divide lo spazio in tre parti: una e due.', '`1fr 2fr` splits the space into three parts: one and two.')),
            Q('g2', L('Come scrivi tre colonne uguali?', 'How do you write three equal columns?'), ['columns: 3', 'grid-template-columns: repeat(3, 1fr)', 'grid: 3', 'flex: 3'], 1, L('`repeat()` evita di ripetere `1fr 1fr 1fr`.', '`repeat()` saves writing `1fr 1fr 1fr`.')),
            Q('g3', L('Che cosa fa `grid-column: 1 / 3`?', 'What does `grid-column: 1 / 3` do?'), [L('Occupa tre colonne', 'It takes three columns'), L('Va dalla linea 1 alla linea 3', 'It goes from line 1 to line 3'), L('Divide per tre', 'It divides by three'), L('Crea tre righe', 'It creates three rows')], 1, L('Le linee sono i bordi tra le colonne: l’elemento occupa due colonne.', 'Lines are the edges between columns: the element takes two columns.')),
        ], task('css', L('Trasforma `.lista` in una griglia di tre colonne uguali con `gap` di `12px`.', 'Turn `.lista` into a grid of three equal columns with a `gap` of `12px`.'), '.card { background: #ffe8d6; padding: 12px; }

', [css('.lista', 'display', 'grid', L('La lista è una griglia', 'The list is a grid')), css('.lista', 'row-gap', '12px', L('Il gap è 12px', 'The gap is 12px')), re('grid-template-columns\s*:\s*(repeat\(\s*3\s*,\s*1fr\s*\)|1fr\s+1fr\s+1fr)', L('Ci sono tre colonne uguali', 'There are three equal columns'))], $cards)),

        lesson('responsive', L('Responsive design', 'Responsive design'), [
            P('Una pagina responsive si adatta a ogni schermo. Si parte dal telefono e con le **media query** si aggiungono regole per gli schermi più grandi: è il metodo *mobile first*.', 'A responsive page adapts to every screen. You start from the phone and add rules for larger screens with **media queries**: that is the *mobile first* method.'),
            C('.lista { display: grid; gap: 12px; }

@media (min-width: 700px) {
  .lista { grid-template-columns: 1fr 1fr; }
}

img { max-width: 100%; height: auto; }
h1 { font-size: clamp(1.5rem, 5vw, 3rem); }', 'css', L('<h1>Un titolo che cresce con lo schermo</h1>
<div class="lista">
  <div style="background:#ffe8d6;padding:8px">Uno</div>
  <div style="background:#ffe8d6;padding:8px">Due: affiancati da 700px in su</div>
</div>', '<h1>A heading that grows with the screen</h1>
<div class="lista">
  <div style="background:#ffe8d6;padding:8px">One</div>
  <div style="background:#ffe8d6;padding:8px">Two: side by side from 700px up</div>
</div>')),
            P('`clamp(min, ideale, max)` fa crescere un valore entro due limiti. `vw` è l’1% della larghezza dello schermo. `max-width: 100%` impedisce alle immagini di uscire dal contenitore.', '`clamp(min, ideal, max)` grows a value within two limits. `vw` is 1% of the screen width. `max-width: 100%` stops images overflowing their container.'),
            Fig(L(fig_cssadv_mobile(['regole di base: per tutti', '@media (min-width: 700px): si aggiunge']), fig_cssadv_mobile(['base rules: for everyone', '@media (min-width: 700px): added on top'])),
                L('Un telefono con tre card una sotto l’altra e uno schermo largo con le stesse card su due colonne. Sotto, un righello da 0 con una tacca a 700px: le regole di base valgono per tutti, la media query aggiunge le due colonne da 700px in su.', 'A phone with three cards one below the other and a wide screen with the same cards in two columns. Below, a ruler from 0 with a mark at 700px: the base rules apply to everyone, the media query adds the two columns from 700px up.'),
                L('`min-width` vuol dire «da qui in su»: le regole si sommano, non si sostituiscono.', '`min-width` means “from here up”: rules add up, they do not replace each other.')),
            P('Perché partire dal telefono? Perché la versione piccola è la più semplice: una colonna, niente affiancamenti. Le media query poi aggiungono solo quello che cambia, e il CSS resta corto. Senza `<meta name="viewport" content="width=device-width">` nel `head`, però, il telefono finge di essere largo 980px e nessuna media query scatta: è la prima cosa da controllare quando «il responsive non funziona».', 'Why start from the phone? Because the small version is the simplest: one column, nothing side by side. The media queries then add only what changes, and the CSS stays short. Without `<meta name="viewport" content="width=device-width">` in the `head`, though, the phone pretends to be 980px wide and no media query fires: it is the first thing to check when “responsive does not work”.'),
        ], [
            Q('r1', L('Che cosa significa *mobile first*?', 'What does *mobile first* mean?'), [L('Sviluppare solo per telefoni', 'Developing for phones only'), L('Partire dal telefono e aggiungere regole per gli schermi grandi', 'Starting from the phone and adding rules for large screens'), L('Usare un’app', 'Using an app'), L('Nascondere il desktop', 'Hiding the desktop')], 1, L('Si usano media query con `min-width`.', 'It uses media queries with `min-width`.')),
            Q('r2', L('Quando si applica `@media (min-width: 700px)`?', 'When does `@media (min-width: 700px)` apply?'), [L('Sotto i 700px', 'Under 700px'), L('Da 700px in su', 'From 700px up'), L('Solo a 700px', 'Only at 700px'), L('Sempre', 'Always')], 1, L('`min-width` significa «almeno questa larghezza».', '`min-width` means “at least this width”.')),
            Q('r3', L('Che cosa fa `clamp(1rem, 4vw, 2rem)`?', 'What does `clamp(1rem, 4vw, 2rem)` do?'), [L('Sceglie a caso', 'Picks at random'), L('Tiene `4vw` tra 1rem e 2rem', 'Keeps `4vw` between 1rem and 2rem'), L('Somma i valori', 'Adds the values'), L('Blocca il font', 'Locks the font')], 1, L('Un valore fluido, con un minimo e un massimo.', 'A fluid value, with a minimum and a maximum.')),
        ], task('css', L('Scrivi una media query `min-width: 700px` che metta `.lista` in `display: flex`.', 'Write a `min-width: 700px` media query that sets `.lista` to `display: flex`.'), '', [re('@media[^{]*min-width\s*:\s*700px', L('C’è la media query', 'The media query is there')), re('@media[^{]*\{[^}]*\.lista\s*\{[^}]*display\s*:\s*flex', L('Dentro c’è `.lista` in flex', '`.lista` is flex inside it'))], $cards)),

        lesson('variables', L('Variabili e funzioni CSS', 'CSS variables and functions'), [
            P('Le **proprietà personalizzate** iniziano con `--` e si leggono con `var()`. A differenza delle variabili di Sass esistono nel browser, quindi si possono cambiare dal vivo, ad esempio per il tema scuro.', '**Custom properties** start with `--` and are read with `var()`. Unlike Sass variables they exist in the browser, so they can change live, for a dark theme for example.'),
            C(':root { --brand: #E8590C; --spazio: 16px; }

.card {
  color: var(--brand);
  padding: calc(var(--spazio) * 2);
  border-color: var(--bordo, #ccc); /* valore di riserva */
}

@media (prefers-color-scheme: dark) {
  :root { --brand: #FFA94D; }
}', 'css', L('<div class="card" style="border:2px solid">Testo color --brand, padding doppio di --spazio, bordo di riserva grigio</div>', '<div class="card" style="border:2px solid">Text in --brand, padding twice --spazio, grey fallback border</div>')),
            P('`calc()` fa i conti anche tra unità diverse, come `calc(100% - 40px)`. Il secondo argomento di `var()` è il valore di riserva, usato se la variabile non esiste.', '`calc()` does maths even between different units, as in `calc(100% - 40px)`. The second argument of `var()` is the fallback, used if the variable does not exist.'),
        ], [
            Q('v1', L('Come si dichiara una variabile CSS?', 'How do you declare a CSS variable?'), ['$brand: red', '--brand: red', '@brand: red', 'var brand = red'], 1, L('Inizia con due trattini e si dichiara dentro un selettore.', 'It starts with two dashes and is declared inside a selector.')),
            Q('v2', L('Dove si dichiarano di solito le variabili globali?', 'Where are global variables usually declared?'), [':root', 'head', 'body::before', '@global'], 0, L('`:root` è l’elemento radice, cioè `html`.', '`:root` is the root element, that is `html`.')),
            Q('v3', L('A cosa serve il secondo argomento di `var()`?', 'What is the second argument of `var()` for?'), [L('Al tipo', 'The type'), L('Al valore di riserva', 'The fallback value'), L('Alla durata', 'The duration'), L('All’unità', 'The unit')], 1, L('Si usa se la variabile non è definita.', 'It is used if the variable is not defined.')),
        ], task('css', L('Dichiara `--brand: #E8590C` in `:root` e usala come `color` di `.card`.', 'Declare `--brand: #E8590C` in `:root` and use it as the `color` of `.card`.'), '', [re(':root\s*\{[^}]*--brand\s*:\s*#e8590c', L('La variabile è dichiarata', 'The variable is declared')), re('\.card\s*\{[^}]*color\s*:\s*var\(\s*--brand', L('`.card` usa `var(--brand)`', '`.card` uses `var(--brand)`')), css('.card', 'color', 'rgb(232, 89, 12)', L('Il colore è applicato', 'The colour is applied'))], $cards)),

        lesson('motion', L('Transizioni e animazioni', 'Transitions and animations'), [
            P('`transition` anima il passaggio tra due stati. `transform` sposta, ruota e ingrandisce senza disturbare l’impaginazione, ed è molto fluido.', '`transition` animates the change between two states. `transform` moves, rotates and scales without disturbing the layout, and is very smooth.'),
            C('.card { transition: transform .2s ease; }
.card:hover { transform: translateY(-4px) scale(1.02); }

@keyframes pulsa {
  from { opacity: 1; }
  to   { opacity: .4; }
}
.scintilla { animation: pulsa 1s infinite alternate; }', 'css', L('<div class="card" style="background:#ffe8d6;padding:16px;max-width:260px">Passa sopra: la card si solleva</div>
<p class="scintilla" style="color:#e8590c;font-weight:700">Scintilla che pulsa</p>', '<div class="card" style="background:#ffe8d6;padding:16px;max-width:260px">Hover: the card lifts</div>
<p class="scintilla" style="color:#e8590c;font-weight:700">A pulsing spark</p>')),
            P('`@keyframes` definisce i passaggi di un’animazione e `animation` la applica. Rispetta chi chiede meno movimento con la media query `prefers-reduced-motion`.', '`@keyframes` defines the steps of an animation and `animation` applies it. Respect those who ask for less motion with the `prefers-reduced-motion` media query.'),
            Tip('Anima `transform` e `opacity`: sono le proprietà più leggere per il browser.', 'Animate `transform` and `opacity`: they are the lightest properties for the browser.'),
        ], [
            Q('t1', L('Quale proprietà anima il passaggio tra due stati?', 'Which property animates the change between two states?'), ['animation-state', 'transition', 'change', 'keyframes'], 1, L('Si scrive sullo stato di partenza, con durata e curva.', 'It goes on the starting state, with duration and curve.')),
            Q('t2', L('Con che cosa si definiscono i passaggi di un’animazione?', 'What defines the steps of an animation?'), ['@media', '@keyframes', '@animate', '@steps'], 1, L('Poi si applica con la proprietà `animation`.', 'It is then applied with the `animation` property.')),
            Q('t3', L('Quale media query rispetta chi chiede meno movimento?', 'Which media query respects those asking for less motion?'), ['prefers-reduced-motion', 'no-animation', 'motion: off', 'reduced-data'], 0, L('Il sistema operativo la espone come preferenza di accessibilità.', 'The operating system exposes it as an accessibility preference.')),
        ], task('css', L('Aggiungi a `.card` una `transition` su `transform` e su `.card:hover` un `transform` a piacere.', 'Add a `transition` on `transform` to `.card` and any `transform` on `.card:hover`.'), '.card { background: #ffe8d6; padding: 12px; }

', [re('transition\s*:[^;}]*transform', L('C’è la transizione', 'The transition is there')), re('\.card:hover\s*\{[^}]*transform\s*:', L('C’è il `transform` su hover', 'There is a `transform` on hover'))], $cards)),
    ],
];
