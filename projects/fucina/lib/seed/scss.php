<?php
/** SCSS: the CSS-like syntax of Sass. Exercises are pattern checks: the browser cannot compile SCSS. */

return [
    'id' => 'scss', 'family' => 'scss', 'after' => 'css-intermediate',
    'title' => L('SCSS', 'SCSS'), 'short' => L('SCSS', 'SCSS'), 'level' => L('Preprocessori', 'Preprocessors'),
    'desc' => L('CSS con i superpoteri: variabili, annidamento, mixin, moduli e cicli.', 'CSS with superpowers: variables, nesting, mixins, modules and loops.'),
    'lessons' => [
        lesson('intro', L('Variabili e compilazione', 'Variables and compilation'), [
            P('SCSS è una sintassi di **Sass**, un preprocessore: scrivi in `.scss` e un compilatore produce il normale `.css` che il browser legge. Ogni file CSS valido è anche SCSS valido.', 'SCSS is a syntax of **Sass**, a preprocessor: you write `.scss` and a compiler produces the normal `.css` the browser reads. Every valid CSS file is also valid SCSS.'),
            C('$brand: #E8590C;
$spazio: 16px;

.card {
  color: $brand;
  padding: $spazio * 2;
}', 'scss'),
            P('Le variabili iniziano con `$` e vengono sostituite durante la compilazione, quindi nel CSS finale non esistono più. Si compila con `sass stile.scss stile.css`.', 'Variables start with `$` and are replaced during compilation, so they no longer exist in the final CSS. You compile with `sass style.scss style.css`.'),
            Tip('Le variabili `$` non cambiano nel browser: per un tema dal vivo servono le variabili CSS.', '`$` variables do not change in the browser: a live theme needs CSS variables.'),
        ], [
            Q('i1', L('Il browser sa leggere un file `.scss`?', 'Can the browser read a `.scss` file?'), [L('Sì, sempre', 'Yes, always'), L('No, va compilato in CSS', 'No, it must be compiled to CSS'), L('Solo Chrome', 'Only Chrome'), L('Solo con JavaScript attivo', 'Only with JavaScript on')], 1, L('Il compilatore trasforma SCSS in CSS.', 'The compiler turns SCSS into CSS.')),
            Q('i2', L('Con quale simbolo inizia una variabile SCSS?', 'Which symbol starts an SCSS variable?'), ['--', '@', '$', '#'], 2, L('`$` per Sass, `--` per le variabili CSS.', '`$` for Sass, `--` for CSS variables.')),
            Q('i3', L('Un file CSS valido è anche SCSS valido?', 'Is a valid CSS file also valid SCSS?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo senza commenti', 'Only with no comments'), L('Solo senza media query', 'Only with no media queries')], 0, L('Per questo si può iniziare rinominando il file.', 'That is why you can start by renaming the file.')),
        ], task('scss', L('Dichiara la variabile `$brand` con il valore `#E8590C` e usala come `color` di `.titolo`.', 'Declare the `$brand` variable with the value `#E8590C` and use it as the `color` of `.titolo`.'), '', [re('\$brand\s*:\s*#e8590c\s*;', L('La variabile è dichiarata', 'The variable is declared')), re('\.titolo\s*\{[^}]*color\s*:\s*\$brand', L('`.titolo` la usa', '`.titolo` uses it'))])),

        lesson('nesting', L('Annidamento e parent selector', 'Nesting and the parent selector'), [
            P('In SCSS le regole si scrivono una dentro l’altra, seguendo la struttura dell’HTML. Il simbolo `&` indica il selettore genitore.', 'In SCSS rules are written inside one another, following the structure of the HTML. The `&` symbol stands for the parent selector.'),
            C('.menu {
  padding: 8px;

  a {
    color: navy;

    &:hover { color: red; }
  }

  &--aperto { display: block; }
}', 'scss'),
            P('Viene compilato in `.menu a`, `.menu a:hover` e `.menu--aperto`. Non annidare troppo: oltre tre livelli i selettori diventano lunghi e difficili da sovrascrivere.', 'It compiles to `.menu a`, `.menu a:hover` and `.menu--aperto`. Do not nest too deep: past three levels selectors become long and hard to override.'),
        ], [
            Q('n1', L('Che cosa indica `&`?', 'What does `&` stand for?'), [L('Il selettore genitore', 'The parent selector'), L('Una variabile', 'A variable'), L('Tutti i figli', 'All the children'), L('Un commento', 'A comment')], 0, L('Viene sostituito dal selettore che lo contiene.', 'It is replaced by the selector containing it.')),
            Q('n2', L('In che cosa si compila `.a { &:hover { … } }`?', 'What does `.a { &:hover { … } }` compile to?'), ['.a :hover', '.a:hover', '.hover .a', ':hover.a'], 1, L('`&` unisce senza spazio.', '`&` joins with no space.')),
            Q('n3', L('Perché non annidare troppo?', 'Why not nest too deep?'), [L('Il compilatore si blocca', 'The compiler stops'), L('I selettori diventano lunghi e difficili da sovrascrivere', 'Selectors become long and hard to override'), L('Il browser rallenta del tutto', 'The browser stalls completely'), L('Non è permesso', 'It is not allowed')], 1, L('Lo stesso annidamento aumenta anche la specificità.', 'Nesting also raises specificity.')),
        ], task('scss', L('Dentro `.card` annida `a` e, con `&:hover`, cambia il `color` del link.', 'Inside `.card` nest `a` and, with `&:hover`, change the `color` of the link.'), '.card {

}', [re('\.card\s*\{[\s\S]*\ba\s*\{', L('`a` è annidato in `.card`', '`a` is nested in `.card`')), re('&:hover\s*\{[^}]*color\s*:', L('C’è `&:hover` con `color`', 'There is `&:hover` with `color`'))])),

        lesson('mixins', L('Mixin ed extend', 'Mixins and extend'), [
            P('Un **mixin** è un blocco di regole riutilizzabile, anche con parametri. Si definisce con `@mixin` e si inserisce con `@include`.', 'A **mixin** is a reusable block of rules, parameters included. It is defined with `@mixin` and inserted with `@include`.'),
            C('@mixin centrato($direzione: row) {
  display: flex;
  flex-direction: $direzione;
  justify-content: center;
  align-items: center;
}

.hero { @include centrato(column); }

%bottone { padding: 8px 16px; border-radius: 8px; }
.salva { @extend %bottone; }', 'scss'),
            P('`$direzione: row` è un valore predefinito. `@extend` fa condividere lo stesso selettore; `%bottone` è un segnaposto, che non produce CSS finché qualcuno non lo estende.', '`$direzione: row` is a default value. `@extend` makes rules share a selector; `%bottone` is a placeholder, which produces no CSS until something extends it.'),
        ], [
            Q('x1', L('Come si usa un mixin?', 'How do you use a mixin?'), ['@use', '@include', '@mixin', '@apply'], 1, L('`@mixin` lo definisce, `@include` lo inserisce.', '`@mixin` defines it, `@include` inserts it.')),
            Q('x2', L('Quale vantaggio hanno i mixin rispetto a `@extend`?', 'What advantage do mixins have over `@extend`?'), [L('Accettano parametri', 'They accept parameters'), L('Sono più corti', 'They are shorter'), L('Non si compilano', 'They are not compiled'), L('Funzionano in HTML', 'They work in HTML')], 0, L('Un mixin produce regole diverse a seconda degli argomenti.', 'A mixin produces different rules depending on its arguments.')),
            Q('x3', L('Che cosa fa un selettore `%segnaposto` da solo?', 'What does a `%placeholder` selector do alone?'), [L('Produce CSS', 'It produces CSS'), L('Nulla finché non viene esteso', 'Nothing until it is extended'), L('Dà errore', 'It raises an error'), L('Crea una classe', 'It creates a class')], 1, L('È pensato solo per `@extend`.', 'It is meant only for `@extend`.')),
        ], task('scss', L('Definisci il mixin `ombra` con un parametro `$colore` e usalo con `@include` in `.card`.', 'Define the `ombra` mixin with a `$colore` parameter and use it with `@include` in `.card`.'), '', [re('@mixin\s+ombra\s*\(\s*\$colore', L('Il mixin ha il parametro', 'The mixin has the parameter')), re('\.card\s*\{[^}]*@include\s+ombra\s*\(', L('`.card` lo include', '`.card` includes it'))])),

        lesson('modules', L('Moduli, mappe e cicli', 'Modules, maps and loops'), [
            P('Il codice si divide in file chiamati **partial**, con il nome che inizia per `_`. Si caricano con `@use`, che dà a ogni file il suo spazio dei nomi. `@import` è deprecato.', 'Code is split into files called **partials**, whose name starts with `_`. They are loaded with `@use`, which gives each file its own namespace. `@import` is deprecated.'),
            C('// _colori.scss
$brand: #E8590C;

// stile.scss
@use "sass:color";
@use "colori";

.card { color: colori.$brand; }
.scuro { color: color.adjust(colori.$brand, $lightness: -10%); }

$temi: (ok: green, errore: red);

@each $nome, $tinta in $temi {
  .avviso-#{$nome} { color: $tinta; }
}', 'scss'),
            P('Le mappe sono coppie chiave-valore. `@each` le percorre e `#{ }` inserisce un valore dentro un nome: da poche righe nascono molte classi.', 'Maps are key-value pairs. `@each` loops over them and `#{ }` puts a value inside a name: a few lines create many classes.'),
        ], [
            Q('o1', L('Con quale regola si carica un modulo?', 'Which rule loads a module?'), ['@use', '@load', '@require', '@module'], 0, L('`@use` sostituisce il vecchio `@import`.', '`@use` replaces the old `@import`.')),
            Q('o2', L('Come si chiama un partial?', 'How is a partial named?'), ['colori.part.scss', '_colori.scss', '#colori.scss', 'colori_.css'], 1, L('Il trattino basso dice di non compilarlo da solo.', 'The underscore says not to compile it alone.')),
            Q('o3', L('A che cosa serve `#{ }`?', 'What is `#{ }` for?'), [L('A commentare', 'To comment'), L('A interpolare un valore in un nome', 'To interpolate a value into a name'), L('A importare', 'To import'), L('A selezionare un id', 'To select an id')], 1, L('Si chiama interpolazione.', 'It is called interpolation.')),
        ], task('scss', L('Carica il modulo `colori` con `@use` e scrivi un `@each` sulla mappa `$temi`.', 'Load the `colori` module with `@use` and write an `@each` over the `$temi` map.'), '$temi: (ok: green, errore: red);

', [re('@use\s+["\']colori["\']', L('C’è `@use "colori"`', 'There is `@use "colori"`')), re('@each\s+\$\w+\s*,\s*\$\w+\s+in\s+\$temi', L('C’è l’`@each` su `$temi`', 'There is the `@each` over `$temi`'))])),
    ],
];
