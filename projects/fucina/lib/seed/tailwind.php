<?php
/** Tailwind CSS: utility classes, layout, responsive design, states and components. */

return [
    'id' => 'tailwind', 'family' => 'tailwind', 'after' => 'css-base',
    'title' => L('Tailwind CSS', 'Tailwind CSS'), 'short' => L('Tailwind', 'Tailwind'), 'level' => L('Base', 'Basics'),
    'desc' => L('Lo stile scritto con le classi: spazi, colori, flex e griglie, schermi diversi, stati e componenti.', 'Styling with classes: spacing, colours, flex and grids, screen sizes, states and components.'),
    'lessons' => [
        lesson('utilities', L('Le classi di utilità', 'Utility classes'), [
            P('Tailwind non ha componenti già pronti: ha migliaia di **classi piccole**, una per ogni proprietà. `p-4` mette il padding, `text-lg` ingrandisce il testo, `bg-white` colora lo sfondo. Si combinano direttamente nell’HTML.', 'Tailwind has no ready-made components: it has thousands of **small classes**, one per property. `p-4` sets padding, `text-lg` enlarges text, `bg-white` colours the background. You combine them right in the HTML.'),
            C('<div class="p-4 bg-white rounded-lg shadow">
  <h2 class="text-lg font-bold">Ciao</h2>
  <p class="text-gray-600">Stile con le classi.</p>
</div>', 'html'),
            P('I numeri seguono una scala: `p-1` è 0,25rem, `p-4` è 1rem, `p-8` è 2rem. Lo stesso vale per `m-`, `gap-`, `w-` e `h-`.', 'Numbers follow a scale: `p-1` is 0.25rem, `p-4` is 1rem, `p-8` is 2rem. The same goes for `m-`, `gap-`, `w-` and `h-`.'),
        ], [
            Q('u1', L('Che cosa fa `p-4`?', 'What does `p-4` do?'), [L('Padding di 1rem', 'Padding of 1rem'), L('Margine di 4px', 'Margin of 4px'), L('4 paragrafi', '4 paragraphs'), L('Posizione 4', 'Position 4')], 0, L('La scala di Tailwind va a passi di 0,25rem.', 'Tailwind’s scale goes in steps of 0.25rem.')),
            Q('u2', L('Dove si scrivono le classi di Tailwind?', 'Where do you write Tailwind classes?'), [L('Nell’attributo `class` dell’HTML', 'In the HTML `class` attribute'), L('In un file .js', 'In a .js file'), L('Nel `<title>`', 'In the `<title>`'), L('Nel database', 'In the database')], 0, L('Lo stile vive accanto al markup.', 'Style lives next to the markup.')),
            Q('u3', L('Quale classe rende il testo in grassetto?', 'Which class makes text bold?'), ['font-bold', 'text-bold', 'bold', 'weight-700'], 0, L('`font-` riguarda peso e famiglia.', '`font-` is about weight and family.')),
        ], task('html', L('Dai al `<div>` le classi `p-4`, `bg-white` e `rounded-lg`.', 'Give the `<div>` the classes `p-4`, `bg-white` and `rounded-lg`.'), '<div class="">Carta</div>', [re('class="[^"]*\bp-4\b', L('C’è `p-4`', 'There is `p-4`')), re('class="[^"]*\bbg-white\b', L('C’è `bg-white`', 'There is `bg-white`')), re('class="[^"]*\brounded-lg\b', L('C’è `rounded-lg`', 'There is `rounded-lg`'))], '', '<div class="p-4 bg-white rounded-lg">Carta</div>'), [
            dMatch(pMatch(), [['p-4', L('Padding 1rem', 'Padding 1rem')], ['m-2', L('Margine 0,5rem', 'Margin 0.5rem')], ['text-lg', L('Testo grande', 'Large text')], ['bg-white', L('Sfondo bianco', 'White background')], ['rounded-lg', L('Angoli arrotondati', 'Rounded corners')]], L('Una classe, una proprietà.', 'One class, one property.')),
            dFlash(pFlash(), [[L('Tailwind ha bottoni già pronti', 'Tailwind has ready-made buttons'), false], [L('`p-8` è più grande di `p-4`', '`p-8` is bigger than `p-4`'), true], [L('Le classi si possono combinare', 'Classes can be combined'), true], [L('`text-gray-600` cambia il colore del testo', '`text-gray-600` changes the text colour'), true]], L('Tailwind dà i mattoni, non i componenti.', 'Tailwind gives bricks, not components.')),
        ]),
        lesson('flex-grid', L('Flex e griglie', 'Flex and grids'), [
            P('`flex` attiva Flexbox; `items-center` allinea in verticale, `justify-between` distribuisce in orizzontale, `gap-4` spazia gli elementi.', '`flex` turns on Flexbox; `items-center` aligns vertically, `justify-between` spreads horizontally, `gap-4` spaces the items.'),
            C('<nav class="flex items-center justify-between gap-4 p-4">
  <a href="/">Logo</a>
  <ul class="flex gap-6"><li>Corsi</li><li>Giochi</li></ul>
</nav>
<section class="grid grid-cols-3 gap-4">
  <div>1</div><div>2</div><div>3</div>
</section>', 'html'),
            P('`grid grid-cols-3` crea tre colonne uguali; `col-span-2` fa occupare due colonne a un elemento.', '`grid grid-cols-3` makes three equal columns; `col-span-2` makes an item take two columns.'),
        ], [
            Q('f1', L('Quale classe centra gli elementi in verticale in un flex?', 'Which class centres items vertically in a flex?'), ['items-center', 'justify-center', 'text-center', 'align-middle'], 0, L('`items-` lavora sull’asse trasversale.', '`items-` works on the cross axis.')),
            Q('f2', L('Come si fanno 3 colonne?', 'How do you make 3 columns?'), ['grid grid-cols-3', 'flex cols-3', 'columns 3', 'grid-3'], 0, L('Prima `grid`, poi il numero di colonne.', 'First `grid`, then the number of columns.')),
            Q('f3', L('Che cosa fa `gap-4`?', 'What does `gap-4` do?'), [L('Spazio tra gli elementi', 'Space between items'), L('Bordo', 'Border'), L('Ombra', 'Shadow'), L('Margine esterno', 'Outer margin')], 0, L('Funziona con flex e grid.', 'It works with flex and grid.')),
        ], task('html', L('Rendi il `<header>` un flex con `items-center` e `justify-between`.', 'Make the `<header>` a flex with `items-center` and `justify-between`.'), '<header class="">
  <strong>Fucina</strong>
  <a href="#">Entra</a>
</header>', [re('<header class="[^"]*\bflex\b', L('È un flex', 'It is a flex')), re('<header class="[^"]*\bitems-center\b', L('Allineato al centro', 'Centred')), re('<header class="[^"]*\bjustify-between\b', L('Distribuito ai lati', 'Spread to the sides'))], '', '<header class="flex items-center justify-between">'), [
            dBlanks(pFill(), "<div class=\"___ items-center gap-2\">…</div>\n<ul class=\"grid ___ gap-4\">…</ul>", ['flex', 'grid-cols-2'], ['block', 'cols-2', 'inline'], L('Flex in riga, griglia a due colonne.', 'Flex in a row, a two-column grid.')),
            dMatch(pMatch(), [['justify-between', L('Spazio tra gli elementi ai lati', 'Space between, items at the edges')], ['justify-center', L('Tutto al centro', 'Everything centred')], ['items-start', L('Allineati in alto', 'Aligned to the top')], ['flex-col', L('In colonna', 'In a column')]], L('Le classi di Flexbox.', 'The Flexbox classes.')),
        ]),
        lesson('responsive', L('Schermi diversi', 'Different screens'), [
            P('Tailwind è **mobile first**: una classe senza prefisso vale ovunque, una con `md:` vale da 768px in su, `lg:` da 1024px. Così scrivi il telefono e poi aggiungi lo schermo grande.', 'Tailwind is **mobile first**: a class without a prefix works everywhere, one with `md:` from 768px up, `lg:` from 1024px. You write the phone first and then add the big screen.'),
            C('<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
  …
</div>
<p class="text-base md:text-lg">Testo che cresce</p>
<aside class="hidden lg:block">Solo su desktop</aside>', 'html'),
            P('`hidden lg:block` nasconde un elemento sui telefoni e lo mostra su schermi grandi.', '`hidden lg:block` hides an element on phones and shows it on large screens.'),
        ], [
            Q('r1', L('Da quale larghezza vale `md:`?', 'From which width does `md:` apply?'), ['768px', '640px', '1024px', '320px'], 0, L('`sm` 640, `md` 768, `lg` 1024.', '`sm` 640, `md` 768, `lg` 1024.')),
            Q('r2', L('Che cosa significa mobile first?', 'What does mobile first mean?'), [L('Le classi senza prefisso valgono per tutti, poi si aggiunge per schermi grandi', 'Classes without a prefix apply to all, then you add for big screens'), L('Il sito funziona solo sui telefoni', 'The site only works on phones'), L('Si scrive prima il CSS desktop', 'You write desktop CSS first'), L('Le immagini sono piccole', 'Images are small')], 0, L('Si parte dallo schermo più piccolo.', 'You start from the smallest screen.')),
            Q('r3', L('Come mostri un elemento solo da `lg` in su?', 'How do you show an element only from `lg` up?'), ['hidden lg:block', 'lg:hidden', 'block lg:hidden', 'show-lg'], 0, L('Nascosto di base, visibile da `lg`.', 'Hidden by default, visible from `lg`.')),
        ], task('html', L('Fai una griglia di 1 colonna che diventa di 3 da `md` in su.', 'Make a grid of 1 column that becomes 3 from `md` up.'), '<section class="">
  <div>A</div><div>B</div><div>C</div>
</section>', [re('class="[^"]*\bgrid-cols-1\b', L('Una colonna di base', 'One column by default')), re('class="[^"]*\bmd:grid-cols-3\b', L('Tre colonne da `md`', 'Three columns from `md`'))], '', '<section class="grid grid-cols-1 md:grid-cols-3">'), [
            dOrder(pSort(), ['sm: 640px', 'md: 768px', 'lg: 1024px', 'xl: 1280px', '2xl: 1536px'], L('I punti di rottura, dal più piccolo.', 'The breakpoints, from the smallest.')),
            dFlash(pFlash(), [[L('`md:text-lg` vale anche sui telefoni', '`md:text-lg` also applies on phones'), false], [L('`lg:` vale da 1024px in su', '`lg:` applies from 1024px up'), true], [L('Una classe senza prefisso vale ovunque', 'A class without a prefix applies everywhere'), true]], L('Il prefisso dice da dove in su.', 'The prefix says from where up.')),
        ]),
        lesson('states', L('Stati, colori e tema scuro', 'States, colours and dark mode'), [
            P('I prefissi di stato applicano una classe solo in certi momenti: `hover:` al passaggio del mouse, `focus:` quando un campo è attivo, `disabled:` sui controlli disattivati. `dark:` vale con il tema scuro.', 'State prefixes apply a class only at certain moments: `hover:` on mouse over, `focus:` when a field is active, `disabled:` on disabled controls. `dark:` applies with the dark theme.'),
            C('<button class="bg-orange-500 hover:bg-orange-600 focus:ring-4
               text-white px-4 py-2 rounded-full
               disabled:opacity-50 dark:bg-orange-400">
  Salva
</button>', 'html'),
            P('I colori hanno una scala da 50 (chiarissimo) a 950 (scurissimo): `bg-orange-500` è il tono medio.', 'Colours have a scale from 50 (lightest) to 950 (darkest): `bg-orange-500` is the middle shade.'),
        ], [
            Q('s1', L('Quale prefisso vale al passaggio del mouse?', 'Which prefix applies on mouse over?'), ['hover:', 'focus:', 'active:', 'over:'], 0, L('Come la pseudo-classe `:hover`.', 'Like the `:hover` pseudo-class.')),
            Q('s2', L('Quale tono è più scuro?', 'Which shade is darker?'), ['bg-blue-900', 'bg-blue-100', 'bg-blue-300', 'bg-blue-50'], 0, L('Più alto il numero, più scuro.', 'The higher the number, the darker.')),
            Q('s3', L('Che cosa fa `dark:bg-gray-900`?', 'What does `dark:bg-gray-900` do?'), [L('Sfondo scuro solo con il tema scuro', 'Dark background only in dark mode'), L('Sfondo sempre scuro', 'Always a dark background'), L('Nasconde l’elemento', 'Hides the element'), L('Niente', 'Nothing')], 0, L('Il prefisso `dark:` segue il tema.', 'The `dark:` prefix follows the theme.')),
        ], task('html', L('Al bottone aggiungi `hover:bg-orange-600` e `focus:ring-4`.', 'Add `hover:bg-orange-600` and `focus:ring-4` to the button.'), '<button class="bg-orange-500 text-white px-4 py-2">Vai</button>', [re('class="[^"]*\bhover:bg-orange-600\b', L('Cambia al passaggio del mouse', 'It changes on hover')), re('class="[^"]*\bfocus:ring-4\b', L('Si vede quando ha il focus', 'It shows when focused'))], '', '<button class="bg-orange-500 hover:bg-orange-600 focus:ring-4 text-white px-4 py-2">Vai</button>'), [
            dMatch(pMatch(), [['hover:', L('Mouse sopra', 'Mouse over')], ['focus:', L('Campo attivo', 'Active field')], ['disabled:', L('Controllo disattivato', 'Disabled control')], ['dark:', L('Tema scuro', 'Dark theme')], ['md:', L('Da 768px', 'From 768px')]], L('I prefissi di Tailwind.', 'Tailwind’s prefixes.')),
            dBug(pBug(), ['<button class="bg-sky-500', '  hover-bg-sky-600', '  text-white rounded px-4 py-2">', '  Invia', '</button>'], 1, '  hover:bg-sky-600', L('Il prefisso si separa con i due punti, non con un trattino.', 'The prefix is separated by a colon, not a dash.')),
        ]),
        lesson('config', L('Componenti e configurazione', 'Components and configuration'), [
            P('Le stesse classi ripetute in molti punti si raccolgono in un **componente** (in React, Vue, un partial…) oppure con `@apply` in un file CSS. Il file di configurazione o il blocco `@theme` aggiunge colori e font del tuo marchio.', 'The same classes repeated in many places go into a **component** (in React, Vue, a partial…) or into a CSS file with `@apply`. The configuration file or the `@theme` block adds your brand’s colours and fonts.'),
            C('@import "tailwindcss";

@theme {
  --color-brace: #ff5b2e;
  --font-display: "Fredoka", sans-serif;
}

.btn {
  @apply px-4 py-2 rounded-full bg-brace text-white;
}', 'css'),
            P('Tailwind genera solo le classi che usi davvero: il CSS finale resta piccolo.', 'Tailwind only generates the classes you actually use: the final CSS stays small.'),
        ], [
            Q('c1', L('Che cosa fa `@apply`?', 'What does `@apply` do?'), [L('Raccoglie classi di utilità in una regola CSS', 'Gathers utility classes into a CSS rule'), L('Importa un file', 'Imports a file'), L('Applica un tema scuro', 'Applies a dark theme'), L('Cancella le classi', 'Deletes classes')], 0, L('Utile per pochi componenti ripetuti.', 'Useful for a few repeated components.')),
            Q('c2', L('Dove aggiungi il colore del tuo marchio?', 'Where do you add your brand colour?'), [L('Nel tema (`@theme` o configurazione)', 'In the theme (`@theme` or config)'), L('In ogni classe a mano', 'In each class by hand'), L('Nell’HTML con `style`', 'In the HTML with `style`'), L('Non si può', 'You cannot')], 0, L('Poi diventa una classe come `bg-brace`.', 'Then it becomes a class like `bg-brace`.')),
            Q('c3', L('Perché il CSS di Tailwind resta piccolo?', 'Why does Tailwind’s CSS stay small?'), [L('Genera solo le classi usate', 'It generates only the classes used'), L('Comprime le immagini', 'It compresses images'), L('Non usa CSS', 'It uses no CSS'), L('Lo carica da Google', 'It loads it from Google')], 0, L('Analizza i tuoi file e tiene ciò che serve.', 'It scans your files and keeps what is needed.')),
        ], task('css', L('Crea la regola `.card` che con `@apply` usa `p-6`, `rounded-xl` e `shadow`.', 'Create the rule `.card` that uses `p-6`, `rounded-xl` and `shadow` with `@apply`.'), '', [re('\.card\s*\{[^}]*@apply[^;]*\bp-6\b', L('C’è `p-6`', 'There is `p-6`')), re('@apply[^;]*\brounded-xl\b', L('C’è `rounded-xl`', 'There is `rounded-xl`')), re('@apply[^;]*\bshadow\b', L('C’è `shadow`', 'There is `shadow`'))], '<div class="card">Carta</div>', '.card {
  @apply p-6 rounded-xl shadow;
}'), [
            dBlanks(pFill(), "@___ \"tailwindcss\";\n@theme {\n  --color-___: #ff5b2e;\n}\n.btn { @___ px-4 py-2 bg-brace; }", ['import', 'brace', 'apply'], ['include', 'use', 'mixin'], L('Importa, definisci il colore, raccogli le classi.', 'Import, define the colour, gather the classes.')),
            dFlash(pFlash(), [[L('Con `@theme` si aggiungono colori propri', '`@theme` adds your own colours'), true], [L('Bisogna scrivere CSS per ogni bottone', 'You must write CSS for every button'), false], [L('Le classi inutilizzate finiscono nel CSS finale', 'Unused classes end up in the final CSS'), false]], L('Si genera solo ciò che serve.', 'Only what is needed is generated.')),
        ]),
    ],
];
