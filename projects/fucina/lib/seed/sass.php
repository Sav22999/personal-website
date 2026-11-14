<?php
/** Sass: the original indented syntax. */

return [
    'id' => 'sass', 'family' => 'sass', 'after' => 'scss',
    'title' => L('Sass (sintassi indentata)', 'Sass (indented syntax)'), 'short' => L('Sass', 'Sass'), 'level' => L('Preprocessori', 'Preprocessors'),
    'desc' => L('La sintassi originale di Sass: niente graffe né punti e virgola, conta il rientro.', 'The original Sass syntax: no braces or semicolons, indentation is what counts.'),
    'lessons' => [
        lesson('indented', L('La sintassi indentata', 'The indented syntax'), [
            P('I file `.sass` usano lo stesso linguaggio di SCSS ma senza `{ }` e senza `;`. I blocchi sono definiti dal **rientro**, un po’ come in Python.', '`.sass` files use the same language as SCSS but with no `{ }` and no `;`. Blocks are defined by **indentation**, a bit like Python.'),
            C('$brand: #E8590C

.card
  color: $brand
  padding: 16px

  a
    color: navy

    &:hover
      color: red', 'sass'),
            P('Ogni dichiarazione sta su una riga e il rientro deve essere coerente in tutto il file. Il risultato compilato è identico a quello di SCSS: cambia solo come si scrive.', 'Each declaration sits on its own line and the indentation must be consistent across the file. The compiled result is identical to SCSS: only the writing changes.'),
        ], [
            Q('i1', L('Che cosa manca in un file `.sass`?', 'What is missing from a `.sass` file?'), [L('Le variabili', 'Variables'), L('Le graffe e i punti e virgola', 'Braces and semicolons'), L('L’annidamento', 'Nesting'), L('I mixin', 'Mixins')], 1, L('Al loro posto ci sono gli a capo e il rientro.', 'Line breaks and indentation take their place.')),
            Q('i2', L('Che cosa definisce un blocco in `.sass`?', 'What defines a block in `.sass`?'), [L('Le parentesi', 'Brackets'), L('Il rientro', 'Indentation'), L('La parola `end`', 'The word `end`'), L('Il colore', 'The colour')], 1, L('Un rientro sbagliato cambia il significato o dà errore.', 'Wrong indentation changes the meaning or raises an error.')),
            Q('i3', L('Il CSS prodotto da `.sass` e `.scss` è diverso?', 'Is the CSS produced by `.sass` and `.scss` different?'), [L('Sì, sempre', 'Yes, always'), L('No, è lo stesso', 'No, it is the same'), L('Solo nei colori', 'Only in colours'), L('Solo nelle media query', 'Only in media queries')], 1, L('Sono due sintassi dello stesso linguaggio.', 'They are two syntaxes of the same language.')),
        ], task('sass', L('Riscrivi in Sass: `.titolo { color: red; }`', 'Rewrite in Sass: `.titolo { color: red; }`'), '', [re('^\s*\.titolo\s*$', L('Il selettore sta da solo sulla riga', 'The selector stands alone on its line')), re('^[ \t]+color:\s*red\s*$', L('`color: red` è rientrato, senza `;`', '`color: red` is indented, with no `;`'))])),

        lesson('mixins', L('Mixin con = e +', 'Mixins with = and +'), [
            P('La sintassi indentata ha una scorciatoia: `=` definisce un mixin, come `@mixin`, e `+` lo include, come `@include`.', 'The indented syntax has a shortcut: `=` defines a mixin, like `@mixin`, and `+` includes it, like `@include`.'),
            C('=centrato($direzione: row)
  display: flex
  flex-direction: $direzione
  justify-content: center

.hero
  +centrato(column)', 'sass'),
            P('Anche le forme lunghe `@mixin` e `@include` funzionano, ma le scorciatoie sono l’uso più comune. Le altre regole, come `@use`, `@each` e `@if`, si scrivono come in SCSS, senza graffe.', 'The long forms `@mixin` and `@include` work too, but the shortcuts are the more common use. Other rules, such as `@use`, `@each` and `@if`, are written as in SCSS, with no braces.'),
        ], [
            Q('m1', L('Quale simbolo definisce un mixin in `.sass`?', 'Which symbol defines a mixin in `.sass`?'), ['=', '+', '&', '%'], 0, L('`=` equivale a `@mixin`.', '`=` equals `@mixin`.')),
            Q('m2', L('Quale simbolo include un mixin?', 'Which symbol includes a mixin?'), ['=', '+', '$', '>'], 1, L('`+` equivale a `@include`.', '`+` equals `@include`.')),
            Q('m3', L('`@mixin` funziona anche in `.sass`?', 'Does `@mixin` work in `.sass` too?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo nei partial', 'Only in partials'), L('Solo con `@use`', 'Only with `@use`')], 0, L('Le scorciatoie sono facoltative.', 'The shortcuts are optional.')),
        ], task('sass', L('Definisci con `=` il mixin `tondo` con `border-radius: 50%` e includilo con `+` in `.avatar`.', 'Define the `tondo` mixin with `=`, with `border-radius: 50%`, and include it with `+` in `.avatar`.'), '', [re('^=tondo\b', L('Il mixin è definito con `=`', 'The mixin is defined with `=`')), re('^[ \t]+border-radius:\s*50%\s*$', L('Ha `border-radius: 50%`', 'It has `border-radius: 50%`')), re('^\.avatar\s*\n[ \t]+\+tondo', L('`.avatar` lo include con `+`', '`.avatar` includes it with `+`'))])),

        lesson('choose', L('Sass o SCSS?', 'Sass or SCSS?'), [
            P('I due formati fanno le stesse cose. SCSS è il più diffuso perché è un’estensione del CSS: si copia del CSS esistente e funziona. Sass è più sintetico, ma non accetta il CSS così com’è.', 'The two formats do the same things. SCSS is more common because it extends CSS: paste existing CSS and it works. Sass is more concise, but it does not accept CSS as it is.'),
            C('sass --watch stile.sass:stile.css
sass-convert stile.scss stile.sass   # vecchi strumenti
sass --style=compressed stile.scss stile.css', 'bash'),
            P('Con `--watch` il compilatore ricompila a ogni salvataggio; con `--style=compressed` il CSS esce minimizzato per la pubblicazione. In un progetto si sceglie un solo formato, anche se Sass può caricare file di entrambi i tipi.', 'With `--watch` the compiler rebuilds on every save; with `--style=compressed` the CSS comes out minified for publishing. In a project you pick one format, although Sass can load files of both kinds.'),
            Tip('In un team conviene SCSS, salvo una preferenza chiara: chi conosce il CSS lo legge subito.', 'In a team SCSS is the safer choice, unless there is a clear preference: anyone who knows CSS reads it at once.'),
        ], [
            Q('c1', L('Perché SCSS è il formato più diffuso?', 'Why is SCSS the more common format?'), [L('È più veloce nel browser', 'It is faster in the browser'), L('È un’estensione del CSS', 'It extends CSS'), L('Produce file più piccoli', 'It produces smaller files'), L('È l’unico con i mixin', 'It is the only one with mixins')], 1, L('Il CSS esistente è già SCSS valido.', 'Existing CSS is already valid SCSS.')),
            Q('c2', L('Quale opzione ricompila a ogni salvataggio?', 'Which option recompiles on every save?'), ['--live', '--watch', '--auto', '--save'], 1, L('`--watch` resta in ascolto delle modifiche.', '`--watch` keeps listening for changes.')),
            Q('c3', L('Quale opzione minimizza il CSS?', 'Which option minifies the CSS?'), ['--style=compressed', '--min', '--small', '--zip'], 0, L('Il CSS esce su una sola riga, senza spazi inutili.', 'The CSS comes out on one line, with no needless spaces.')),
        ]),
    ],
];
