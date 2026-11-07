<?php
/** CSS avanzato: flexbox, grid, responsive design, custom properties and animation. */

$cards = '<div class="lista">
  <div class="card">Uno</div>
  <div class="card">Due</div>
  <div class="card">Tre</div>
</div>';

return [
    'id' => 'css-advanced', 'family' => 'css', 'after' => 'css-intermediate',
    'title' => L('CSS avanzato', 'Advanced CSS'), 'short' => L('CSS avanzato', 'Advanced CSS'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Impaginazioni moderne: flexbox, grid, responsive, variabili, transizioni e animazioni.', 'Modern layouts: flexbox, grid, responsive design, variables, transitions and animations.'),
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
.card { flex: 1; }', 'css'),
            P('`justify-content` distribuisce lungo l’asse principale, `align-items` lungo quello perpendicolare. `gap` è lo spazio tra i figli. `flex: 1` su un figlio gli fa prendere lo spazio libero.', '`justify-content` distributes along the main axis, `align-items` along the cross axis. `gap` is the space between children. `flex: 1` on a child makes it take the free space.'),
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
}', 'css'),
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
h1 { font-size: clamp(1.5rem, 5vw, 3rem); }', 'css'),
            P('`clamp(min, ideale, max)` fa crescere un valore entro due limiti. `vw` è l’1% della larghezza dello schermo. `max-width: 100%` impedisce alle immagini di uscire dal contenitore.', '`clamp(min, ideal, max)` grows a value within two limits. `vw` is 1% of the screen width. `max-width: 100%` stops images overflowing their container.'),
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
}', 'css'),
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
.scintilla { animation: pulsa 1s infinite alternate; }', 'css'),
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
