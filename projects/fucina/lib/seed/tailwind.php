<?php
/** Tailwind CSS v4: utility classes, layout, responsive design, states and components, installation, custom values and utilities, container queries. */

return [
    'id' => 'tailwind', 'family' => 'tailwind', 'after' => 'css-base',
    'title' => L('Tailwind CSS', 'Tailwind CSS'), 'short' => L('Tailwind', 'Tailwind'), 'level' => L('Base', 'Basics'),
    'desc' => L('Lo stile scritto con le classi di Tailwind v4: spazi, colori, flex e griglie, schermi diversi, stati, group e peer, valori e utilità su misura, container query e installazione.', 'Styling with Tailwind v4 classes: spacing, colours, flex and grids, screen sizes, states, group and peer, custom values and utilities, container queries and installation.'),
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
            dFlash(pFlash(), [[L('`md:text-lg` vale anche sui telefoni', '`md:text-lg` also applies on phones'), false], [L('`lg:` vale da 1024px in su', '`lg:` applies from 1024px up'), true], [L('Una classe senza prefisso vale ovunque', 'A class without a prefix applies everywhere'), true], [L('`sm:` vale da 640px in su', '`sm:` applies from 640px up'), true]], L('Il prefisso dice da dove in su.', 'The prefix says from where up.')),
        ]),
        lesson('states', L('Stati, colori e tema scuro', 'States, colours and dark mode'), [
            P('I prefissi di stato applicano una classe solo in certi momenti: `hover:` al passaggio del mouse, `focus:` quando un campo è attivo, `disabled:` sui controlli disattivati. `dark:` vale con il tema scuro.', 'State prefixes apply a class only at certain moments: `hover:` on mouse over, `focus:` when a field is active, `disabled:` on disabled controls. `dark:` applies with the dark theme.'),
            C('<button class="bg-orange-500 hover:bg-orange-600 focus:ring-4
               text-white px-4 py-2 rounded-full
               disabled:opacity-50 dark:bg-orange-400">
  Salva
</button>', 'html'),
            P('I colori hanno una scala da 50 (chiarissimo) a 950 (scurissimo): `bg-orange-500` è il tono medio. Di base `dark:` segue il tema del sistema (`prefers-color-scheme`), e in v4 `hover:` vale solo sui dispositivi che hanno davvero un puntatore.', 'Colours have a scale from 50 (lightest) to 950 (darkest): `bg-orange-500` is the middle shade. By default `dark:` follows the system theme (`prefers-color-scheme`), and in v4 `hover:` only applies on devices that really have a pointer that can hover.'),
        ], [
            Q('s1', L('Quale prefisso vale al passaggio del mouse?', 'Which prefix applies on mouse over?'), ['hover:', 'focus:', 'active:', 'over:'], 0, L('Come la pseudo-classe `:hover`.', 'Like the `:hover` pseudo-class.')),
            Q('s2', L('Quale tono è più scuro?', 'Which shade is darker?'), ['bg-blue-900', 'bg-blue-100', 'bg-blue-300', 'bg-blue-50'], 0, L('Più alto il numero, più scuro.', 'The higher the number, the darker.')),
            Q('s3', L('Che cosa fa `dark:bg-gray-900`?', 'What does `dark:bg-gray-900` do?'), [L('Sfondo scuro solo con il tema scuro', 'Dark background only in dark mode'), L('Sfondo sempre scuro', 'Always a dark background'), L('Nasconde l’elemento', 'Hides the element'), L('Niente', 'Nothing')], 0, L('Il prefisso `dark:` segue il tema.', 'The `dark:` prefix follows the theme.')),
        ], task('html', L('Al bottone aggiungi `hover:bg-orange-600` e `focus:ring-4`.', 'Add `hover:bg-orange-600` and `focus:ring-4` to the button.'), '<button class="bg-orange-500 text-white px-4 py-2">Vai</button>', [re('class="[^"]*\bhover:bg-orange-600\b', L('Cambia al passaggio del mouse', 'It changes on hover')), re('class="[^"]*\bfocus:ring-4\b', L('Si vede quando ha il focus', 'It shows when focused'))], '', '<button class="bg-orange-500 hover:bg-orange-600 focus:ring-4 text-white px-4 py-2">Vai</button>'), [
            dMatch(pMatch(), [['hover:', L('Mouse sopra', 'Mouse over')], ['focus:', L('Campo attivo', 'Active field')], ['disabled:', L('Controllo disattivato', 'Disabled control')], ['dark:', L('Tema scuro', 'Dark theme')], ['md:', L('Da 768px', 'From 768px')]], L('I prefissi di Tailwind.', 'Tailwind’s prefixes.')),
            dBug(pBug(), ['<button class="bg-sky-500', '  hover-bg-sky-600', '  text-white rounded px-4 py-2">', '  Invia', '</button>'], 1, '  hover:bg-sky-600', L('Il prefisso si separa con i due punti, non con un trattino.', 'The prefix is separated by a colon, not a dash.')),
        ]),
        lesson('config', L('Componenti e configurazione', 'Components and configuration'), [
            P('Le stesse classi ripetute in molti punti si raccolgono in un **componente** (in React, Vue, un partial…) oppure con `@apply` in un file CSS. Da Tailwind v4 la configurazione si scrive nel CSS: il blocco `@theme` aggiunge colori e font del tuo marchio. Un vecchio `tailwind.config.js` si può ancora caricare con `@config`.', 'The same classes repeated in many places go into a **component** (in React, Vue, a partial…) or into a CSS file with `@apply`. Since Tailwind v4 configuration is written in CSS: the `@theme` block adds your brand’s colours and fonts. An old `tailwind.config.js` can still be loaded with `@config`.'),
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
            dFlash(pFlash(), [[L('Con `@theme` si aggiungono colori propri', '`@theme` adds your own colours'), true], [L('Bisogna scrivere CSS per ogni bottone', 'You must write CSS for every button'), false], [L('Le classi inutilizzate finiscono nel CSS finale', 'Unused classes end up in the final CSS'), false], [L('Le classi di Tailwind si possono combinare nello stesso elemento', 'Tailwind classes can be combined on the same element'), true]], L('Si genera solo ciò che serve.', 'Only what is needed is generated.')),
        ]),
        lesson('install', L('Installare Tailwind v4', 'Installing Tailwind v4'), [
            P('Tailwind è un programma che legge i tuoi file, trova le classi che usi e genera il CSS. Con Vite si installa il pacchetto `tailwindcss` insieme al plugin `@tailwindcss/vite`, si aggiunge il plugin alla configurazione e nel file CSS basta una riga: `@import "tailwindcss";`.', 'Tailwind is a program that reads your files, finds the classes you use and generates the CSS. With Vite you install the `tailwindcss` package together with the `@tailwindcss/vite` plugin, add the plugin to the configuration, and in the CSS file one line is enough: `@import "tailwindcss";`.'),
            C('npm install tailwindcss @tailwindcss/vite

// vite.config.js
import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [tailwindcss()],
});', 'js'),
            C('/* src/style.css */
@import "tailwindcss";
@source "../node_modules/mia-libreria";', 'css'),
            P('Tailwind trova da solo i file da analizzare e salta quelli elencati in `.gitignore`. Con `@source` aggiungi una cartella che non vedrebbe, come una libreria in `node_modules`. Senza Vite c’è la riga di comando: `npx @tailwindcss/cli -i input.css -o output.css --watch`.', 'Tailwind finds the files to scan by itself and skips those listed in `.gitignore`. With `@source` you add a folder it would not see, like a library in `node_modules`. Without Vite there is the command line: `npx @tailwindcss/cli -i input.css -o output.css --watch`.'),
        ], [
            Q('n1', L('Che cosa scrivi nel file CSS per usare Tailwind v4?', 'What do you write in the CSS file to use Tailwind v4?'), ['@tailwind base;', '@use "tailwind";', '@import "tailwindcss";', '<script src="tailwind.js">'], 2, L('Le vecchie direttive `@tailwind` erano di v3.', 'The old `@tailwind` directives belonged to v3.')),
            Q('n2', L('A che cosa serve `@source`?', 'What is `@source` for?'), [L('A indicare file in più da analizzare in cerca di classi', 'To point to extra files to scan for classes'), L('A importare un font', 'To import a font'), L('A pubblicare il sito', 'To publish the site'), L('A scegliere il tema scuro', 'To choose the dark theme')], 0, L('Il resto lo trova da solo.', 'It finds the rest by itself.')),
            Q('n3', L('Quali file salta Tailwind quando cerca le classi?', 'Which files does Tailwind skip when looking for classes?'), [L('Tutti i file HTML', 'All HTML files'), L('I file con più di cento righe', 'Files with more than a hundred lines'), L('I file JavaScript', 'JavaScript files'), L('Quelli elencati in `.gitignore`', 'Those listed in `.gitignore`')], 3, L('Per includerne uno escluso si usa `@source`.', 'To include an excluded one you use `@source`.')),
        ], task('css', L('Scrivi il file CSS: importa Tailwind e aggiungi con `@source` la cartella `../componenti`.', 'Write the CSS file: import Tailwind and add the folder `../componenti` with `@source`.'), '', [re('@import\s+["\']tailwindcss["\']\s*;', L('Tailwind è importato', 'Tailwind is imported')), re('@source\s+["\']\.\./componenti/?["\']\s*;', L('La cartella è aggiunta', 'The folder is added'))], '<p class="text-lg">Ciao</p>', "@import \"tailwindcss\";\n@source \"../componenti\";"), [
            dOrder(pSort(), ['npm install tailwindcss @tailwindcss/vite', L('aggiungi tailwindcss() ai plugin di Vite', 'add tailwindcss() to the Vite plugins'), L('scrivi @import "tailwindcss"; nel CSS', 'write @import "tailwindcss"; in the CSS'), 'npm run dev'], L('Installa, configura, importa, avvia.', 'Install, configure, import, start.')),
            dFlash(pFlash(), [[L('In v4 la riga principale è `@import "tailwindcss";`', 'In v4 the main line is `@import "tailwindcss";`'), true], [L('Bisogna elencare a mano ogni file HTML', 'You must list every HTML file by hand'), false], [L('Esiste un plugin ufficiale per Vite', 'There is an official Vite plugin'), true], [L('Tailwind genera tutte le classi possibili nel CSS finale', 'Tailwind puts every possible class in the final CSS'), false]], L('Poche righe di installazione, poi solo classi.', 'A few lines of setup, then just classes.')),
        ]),
        lesson('arbitrary', L('Valori su misura', 'Custom values'), [
            P('Quando la scala non basta, metti il valore tra parentesi quadre: `w-[37px]`, `bg-[#bada55]`, `grid-cols-[200px_1fr]` (il trattino basso sta per lo spazio). Per leggere una variabile CSS si usano le parentesi tonde: `bg-(--brand)`.', 'When the scale is not enough, put the value in square brackets: `w-[37px]`, `bg-[#bada55]`, `grid-cols-[200px_1fr]` (the underscore stands for a space). To read a CSS variable you use round brackets: `bg-(--brand)`.'),
            C('<div class="w-[37px] bg-[#bada55]"></div>
<div class="grid grid-cols-[200px_1fr] gap-4">…</div>
<p class="bg-(--brand) text-white">Colore del marchio</p>
<div class="[mask-type:luminance]">Proprietà su misura</div>
<p class="text-red-500!">Importante</p>', 'html'),
            P('Con `[proprietà:valore]` scrivi perfino una proprietà che Tailwind non ha. Il `!` finale rende la classe `!important`. Usa i valori su misura come eccezione: se ripeti lo stesso valore, aggiungilo al tema con `@theme`.', 'With `[property:value]` you can even write a property Tailwind does not have. A trailing `!` makes the class `!important`. Use custom values as the exception: if you repeat the same value, add it to the theme with `@theme`.'),
        ], [
            Q('a1', L('Come scrivi una larghezza di esattamente 37px?', 'How do you write a width of exactly 37px?'), ['w-37', 'w-[37px]', 'w(37px)', 'width-37px'], 1, L('Le parentesi quadre contengono un valore qualsiasi.', 'Square brackets hold any value.')),
            Q('a2', L('In v4, come usi la variabile `--brand` come sfondo?', 'In v4, how do you use the `--brand` variable as a background?'), ['bg-[--brand]', 'bg-var-brand', 'bg-$brand', 'bg-(--brand)'], 3, L('In v4 le variabili vanno tra parentesi tonde.', 'In v4 variables go in round brackets.')),
            Q('a3', L('Che cosa indica il trattino basso in `grid-cols-[200px_1fr]`?', 'What does the underscore mean in `grid-cols-[200px_1fr]`?'), [L('Uno spazio', 'A space'), L('Una sottrazione', 'A subtraction'), L('Un commento', 'A comment'), L('Un errore', 'A mistake')], 0, L('Nelle classi non si possono scrivere spazi.', 'Spaces cannot be written in classes.')),
        ], task('html', L('Dai al `div` una larghezza di `250px` e lo sfondo `#ffe8d6` con due valori su misura.', 'Give the `div` a width of `250px` and the background `#ffe8d6` with two custom values.'), '<div class="p-4">Carta</div>', [re('class="[^"]*\bw-\[250px\]', L('La larghezza è 250px', 'The width is 250px')), re('class="[^"]*\bbg-\[#ffe8d6\]', L('Lo sfondo è giusto', 'The background is right'))], '', '<div class="p-4 w-[250px] bg-[#ffe8d6]">Carta</div>'), [
            dMatch(pMatch(), [['w-[37px]', L('Valore su misura', 'Custom value')], ['bg-(--brand)', L('Variabile CSS', 'CSS variable')], ['[mask-type:luminance]', L('Proprietà su misura', 'Custom property')], ['text-red-500!', L('Classe importante', 'Important class')], ['grid-cols-[1fr_2fr]', L('Spazio scritto con `_`', 'Space written as `_`')]], L('Una via d’uscita quando la scala non basta.', 'A way out when the scale is not enough.')),
            dBug(pBug(), ['<div class="grid', '  grid-cols-[200px 1fr]', '  gap-4">'], 1, '  grid-cols-[200px_1fr]', L('Lo spazio spezzerebbe la classe in due: si scrive `_`.', 'The space would split the class in two: you write `_`.')),
        ]),
        lesson('group-peer', L('Group, peer e altri stati', 'Group, peer and other states'), [
            P('Per reagire allo stato di un altro elemento: segna il genitore con `group` e usa `group-hover:` sui figli; segna un fratello con `peer` e usa `peer-invalid:` (o un altro stato) sugli elementi che **lo seguono**. Il `peer` deve venire prima nel codice.', 'To react to another element’s state: mark the parent with `group` and use `group-hover:` on the children; mark a sibling with `peer` and use `peer-invalid:` (or another state) on the elements that **follow it**. The `peer` must come first in the code.'),
            C('<a href="#" class="group block p-4 rounded-lg hover:bg-orange-500">
  <h3 class="font-bold group-hover:text-white">Corso CSS</h3>
  <p class="text-gray-600 group-hover:text-orange-100">10 livelli</p>
</a>

<input type="email" class="peer border rounded px-2">
<p class="hidden peer-invalid:block text-red-600">Email non valida</p>

<label class="has-checked:bg-orange-50 p-2"><input type="checkbox"> Newsletter</label>
<button aria-expanded="false" class="aria-expanded:rotate-180">▾</button>', 'html'),
            P('Altri prefissi utili: `has-checked:` reagisce a ciò che l’elemento contiene, `aria-expanded:` e `data-[stato=aperto]:` leggono gli attributi, `not-` nega uno stato, `*:` applica una classe a tutti i figli diretti.', 'Other handy prefixes: `has-checked:` reacts to what the element contains, `aria-expanded:` and `data-[stato=aperto]:` read attributes, `not-` negates a state, `*:` applies a class to every direct child.'),
        ], [
            Q('g1', L('Che cosa serve sul genitore per usare `group-hover:` sui figli?', 'What does the parent need for `group-hover:` on its children?'), [L('La classe `group`', 'The `group` class'), L('La classe `parent`', 'The `parent` class'), L('L’attributo `hover`', 'The `hover` attribute'), L('Niente', 'Nothing')], 0, L('`group` segna l’elemento da osservare.', '`group` marks the element to watch.')),
            Q('g2', L('Dove deve stare l’elemento con `peer`?', 'Where must the element with `peer` sit?'), [L('Dopo l’elemento che reagisce', 'After the element that reacts'), L('Prima, come fratello dell’elemento che reagisce', 'Before, as a sibling of the element that reacts'), L('Dentro l’elemento che reagisce', 'Inside the element that reacts'), L('Ovunque nella pagina', 'Anywhere on the page')], 1, L('Il CSS sa guardare solo ai fratelli precedenti.', 'CSS can only look at previous siblings.')),
            Q('g3', L('Quale prefisso reagisce a una casella spuntata contenuta nell’elemento?', 'Which prefix reacts to a ticked box inside the element?'), ['peer-checked:', 'group-checked:', 'checked:', 'has-checked:'], 3, L('`has-` guarda dentro, come `:has()`.', '`has-` looks inside, like `:has()`.')),
        ], task('html', L('Mostra il messaggio solo quando l’email non è valida: dai `peer` all’`input` e `hidden peer-invalid:block` al paragrafo.', 'Show the message only when the email is invalid: give `peer` to the `input` and `hidden peer-invalid:block` to the paragraph.'), '<input type="email" class="border px-2">
<p class="text-red-600">Email non valida</p>', [re('<input[^>]*class="[^"]*\bpeer\b', L('L’input è il `peer`', 'The input is the `peer`')), re('<p[^>]*class="[^"]*\bhidden\b', L('Il messaggio parte nascosto', 'The message starts hidden')), re('<p[^>]*class="[^"]*\bpeer-invalid:block\b', L('Compare se l’email non è valida', 'It shows if the email is invalid'))], '', "<input type=\"email\" class=\"peer border px-2\">\n<p class=\"hidden peer-invalid:block text-red-600\">Email non valida</p>"), [
            dMatch(pMatch(), [['group-hover:', L('Il genitore è sotto il mouse', 'The parent is under the mouse')], ['peer-invalid:', L('Un fratello precedente non è valido', 'A previous sibling is invalid')], ['has-checked:', L('Contiene una casella spuntata', 'It holds a ticked box')], ['aria-expanded:', L('Attributo `aria-expanded="true"`', 'Attribute `aria-expanded="true"`')], ['*:', L('Tutti i figli diretti', 'All direct children')]], L('Stati che passano da un elemento all’altro.', 'States passed from one element to another.')),
            dBug(pBug(), ['<input type="email" class="border px-2">', '<p class="hidden peer-invalid:block">Errore</p>'], 0, '<input type="email" class="peer border px-2">', L('Senza la classe `peer` sull’input, `peer-invalid:` non ha nessuno da osservare.', 'Without the `peer` class on the input, `peer-invalid:` has nothing to watch.')),
        ]),
        lesson('custom', L('Utilità e varianti su misura', 'Custom utilities and variants'), [
            P('In v4 le tue classi si definiscono nel CSS. `@utility` crea una nuova classe che funziona con tutti i prefissi (`hover:`, `md:`…). `@custom-variant` crea un nuovo prefisso. `@plugin` carica un plugin, come quello ufficiale per la tipografia.', 'In v4 your own classes are defined in CSS. `@utility` creates a new class that works with every prefix (`hover:`, `md:`…). `@custom-variant` creates a new prefix. `@plugin` loads a plugin, like the official typography one.'),
            C('@import "tailwindcss";
@plugin "@tailwindcss/typography";

@utility scorri-nascosto {
  scrollbar-width: none;
}

@custom-variant dark (&:where(.dark, .dark *));
@custom-variant tema-notte (&:where([data-tema="notte"] *));', 'css'),
            P('La variante `dark` riscritta così segue la classe `.dark` su un antenato invece del tema di sistema: utile se il sito ha un interruttore. Con il plugin di tipografia la classe `prose` dà uno stile curato a un intero articolo.', 'Rewritten like this, the `dark` variant follows the `.dark` class on an ancestor instead of the system theme: useful if the site has a toggle. With the typography plugin the `prose` class gives a whole article a polished style.'),
        ], [
            Q('c1', L('Come crei in v4 una classe che funziona anche con `hover:` e `md:`?', 'How do you create in v4 a class that also works with `hover:` and `md:`?'), [L('Con una regola CSS normale', 'With a normal CSS rule'), L('Con `@apply` in un file JavaScript', 'With `@apply` in a JavaScript file'), L('Con `@utility`', 'With `@utility`'), L('Con `@layer base`', 'With `@layer base`')], 2, L('Tailwind la tratta come una delle sue.', 'Tailwind treats it as one of its own.')),
            Q('c2', L('Che cosa crea `@custom-variant`?', 'What does `@custom-variant` create?'), [L('Un nuovo colore', 'A new colour'), L('Un nuovo breakpoint fisso', 'A new fixed breakpoint'), L('Un nuovo file CSS', 'A new CSS file'), L('Un nuovo prefisso, come `tema-notte:`', 'A new prefix, like `tema-notte:`')], 3, L('Il selettore decide quando il prefisso vale.', 'The selector decides when the prefix applies.')),
            Q('c3', L('Come carichi un plugin in v4?', 'How do you load a plugin in v4?'), ['@plugin "@tailwindcss/typography";', 'plugins: [typography]', '@use "typography";', '<script src="typography.js">'], 0, L('Direttamente nel CSS, come il resto della configurazione.', 'Right in the CSS, like the rest of the configuration.')),
        ], task('css', L('Crea con `@utility` la classe `centro` con `display: grid` e `place-items: center`.', 'Create with `@utility` the `centro` class with `display: grid` and `place-items: center`.'), '@import "tailwindcss";
', [re('@utility\s+centro\s*\{', L('Definisci `centro`', 'You define `centro`')), re('@utility\s+centro\s*\{[^}]*display\s*:\s*grid', L('Usa la griglia', 'It uses grid')), re('@utility\s+centro\s*\{[^}]*place-items\s*:\s*center', L('Centra il contenuto', 'It centres the content'))], '<div class="centro h-40">Al centro</div>', "@import \"tailwindcss\";\n\n@utility centro {\n  display: grid;\n  place-items: center;\n}"), [
            dBlanks(pFill(), "@import \"tailwindcss\";\n___ \"@tailwindcss/typography\";\n___ scorri-nascosto {\n  scrollbar-width: none;\n}\n___ dark (&:where(.dark, .dark *));", ['@plugin', '@utility', '@custom-variant'], ['@include', '@mixin', '@media'], L('Plugin, classe nuova, prefisso nuovo.', 'Plugin, new class, new prefix.')),
            dFlash(pFlash(), [[L('Una classe creata con `@utility` funziona con `hover:`', 'A class created with `@utility` works with `hover:`'), true], [L('In v4 `dark:` di base segue la classe `.dark`', 'In v4 `dark:` follows the `.dark` class by default'), false], [L('`prose` arriva dal plugin di tipografia', '`prose` comes from the typography plugin'), true], [L('I plugin in v4 si caricano solo da un file JavaScript', 'In v4 plugins can only be loaded from a JavaScript file'), false]], L('Di base `dark:` segue il sistema; per una classe serve `@custom-variant`.', 'By default `dark:` follows the system; a class needs `@custom-variant`.')),
        ]),
        lesson('container-motion', L('Container query e movimento', 'Container queries and motion'), [
            P('Le container query sono integrate in v4: segna il genitore con `@container` e usa sui figli prefissi come `@md:`, che guardano la larghezza del contenitore invece dello schermo. `@md` vale da 28rem (448px) di contenitore; `@max-md:` vale sotto.', 'Container queries are built into v4: mark the parent with `@container` and use prefixes like `@md:` on the children, which look at the container’s width instead of the screen. `@md` applies from a 28rem (448px) container; `@max-md:` applies below it.'),
            C('<div class="@container">
  <article class="flex flex-col @md:flex-row gap-4">
    <img class="@md:w-48" src="foto.jpg" alt="Forgia">
    <p>La card si adatta allo spazio che ha.</p>
  </article>
</div>

<button class="transition duration-300 ease-out hover:-translate-y-1
               motion-reduce:transition-none">Salva</button>
<svg class="animate-spin motion-reduce:hidden" aria-hidden="true">…</svg>', 'html'),
            P('`transition`, `duration-300` ed `ease-out` animano i cambi di stato; `animate-spin`, `animate-pulse` e `animate-bounce` sono animazioni pronte. `motion-reduce:` vale per chi chiede meno movimento, `motion-safe:` solo per chi non lo chiede.', '`transition`, `duration-300` and `ease-out` animate state changes; `animate-spin`, `animate-pulse` and `animate-bounce` are ready-made animations. `motion-reduce:` applies to people asking for less motion, `motion-safe:` only to those who do not.'),
        ], [
            Q('q1', L('A che cosa guarda `@md:flex-row`?', 'What does `@md:flex-row` look at?'), [L('Alla larghezza dello schermo', 'The screen width'), L('Al sistema operativo', 'The operating system'), L('Alla larghezza del contenitore segnato con `@container`', 'The width of the container marked with `@container`'), L('All’altezza della pagina', 'The page height')], 2, L('`md:` guarda lo schermo, `@md:` il contenitore.', '`md:` looks at the screen, `@md:` at the container.')),
            Q('q2', L('Quale prefisso vale per chi chiede meno movimento?', 'Which prefix applies to people asking for less motion?'), ['motion-safe:', 'motion-reduce:', 'reduce:', 'still:'], 1, L('Segue `prefers-reduced-motion`.', 'It follows `prefers-reduced-motion`.')),
            Q('q3', L('Che cosa fa `duration-300` insieme a `transition`?', 'What does `duration-300` do together with `transition`?'), [L('Ritarda la pagina di 300ms', 'It delays the page by 300ms'), L('Ripete l’animazione 300 volte', 'It repeats the animation 300 times'), L('Ingrandisce del 300%', 'It enlarges by 300%'), L('Fa durare la transizione 300ms', 'It makes the transition last 300ms')], 3, L('Il numero è in millisecondi.', 'The number is in milliseconds.')),
        ], task('html', L('Segna il `div` esterno con `@container` e fai passare l’`article` da `flex-col` a `@md:flex-row`.', 'Mark the outer `div` with `@container` and make the `article` go from `flex-col` to `@md:flex-row`.'), '<div class="">
  <article class="flex gap-4">
    <img src="foto.jpg" alt="Forgia">
    <p>Testo</p>
  </article>
</div>', [re('<div[^>]*class="[^"]*@container', L('Il contenitore è segnato', 'The container is marked')), re('<article[^>]*class="[^"]*\bflex-col\b', L('In colonna di base', 'A column by default')), re('<article[^>]*class="[^"]*@md:flex-row', L('In riga da `@md`', 'A row from `@md`'))], '', "<div class=\"@container\">\n  <article class=\"flex flex-col @md:flex-row gap-4\">\n    <img src=\"foto.jpg\" alt=\"Forgia\">\n    <p>Testo</p>\n  </article>\n</div>"), [
            dMatch(pMatch(), [['@container', L('Segna il contenitore', 'Marks the container')], ['@md:', L('Contenitore da 28rem in su', 'Container from 28rem up')], ['md:', L('Schermo da 768px in su', 'Screen from 768px up')], ['animate-spin', L('Rotazione continua', 'Continuous spin')], ['motion-reduce:', L('Chi chiede meno movimento', 'People asking for less motion')]], L('Componenti che si adattano e si muovono con garbo.', 'Components that adapt and move gracefully.')),
            dFlash(pFlash(), [[L('In v4 le container query non richiedono plugin', 'In v4 container queries need no plugin'), true], [L('`@md:` guarda la larghezza dello schermo', '`@md:` looks at the screen width'), false], [L('`motion-safe:` vale solo per chi non chiede meno movimento', '`motion-safe:` only applies to people not asking for less motion'), true], [L('`animate-pulse` richiede JavaScript', '`animate-pulse` needs JavaScript'), false]], L('Lo spazio del componente conta più dello schermo.', 'The component’s space counts more than the screen.')),
        ]),
    ],
];
