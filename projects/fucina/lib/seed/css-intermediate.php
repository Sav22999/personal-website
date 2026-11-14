<?php
/** CSS intermediate: the step before flexbox and grid. */

$page = '<nav class="menu"><a href="#">Home</a><a href="#">Corsi</a></nav>
<h1 class="titolo">Fucina</h1>
<p class="nota">Una nota.</p>
<ul class="lista"><li>Uno</li><li>Due</li><li>Tre</li></ul>
<button class="btn">Inizia</button>';

return [
    'id' => 'css-intermediate', 'family' => 'css', 'after' => 'css-base',
    'title' => L('CSS intermedio', 'Intermediate CSS'), 'short' => L('CSS intermedio', 'CSS Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Selettori avanzati, stati, tipografia, overflow e stile di menu e bottoni.', 'Advanced selectors, states, typography, overflow and styling menus and buttons.'),
    'lessons' => [
        lesson('combinators', L('Combinatori e attributi', 'Combinators and attributes'), [
            P('I combinatori legano due selettori: lo spazio significa «dentro», `>` «figlio diretto», `+` «subito dopo», `~` «fratelli successivi». Con le parentesi quadre scegli in base a un attributo.', 'Combinators link two selectors: a space means “inside”, `>` “direct child”, `+` “right after”, `~` “following siblings”. Square brackets pick by attribute.'),
            C('ul > li { list-style: none; }
h1 + p { font-size: 1.2rem; }
a[href^="https"] { color: green; }
input[type="email"] { border-color: blue; }', 'css'),
            P('`^=` significa «inizia con», `$=` «finisce con», `*=` «contiene».', '`^=` means “starts with”, `$=` “ends with”, `*=` “contains”.'),
        ], [
            Q('c1', L('Che cosa sceglie `ul > li`?', 'What does `ul > li` pick?'), [L('I figli diretti', 'The direct children'), L('Tutti i discendenti', 'All descendants'), L('Il primo', 'The first one'), L('Nulla', 'Nothing')], 0, L('Il `>` si ferma al primo livello.', 'The `>` stops at the first level.')),
            Q('c2', L('Che cosa sceglie `h1 + p`?', 'What does `h1 + p` pick?'), [L('Il `p` subito dopo `h1`', 'The `p` right after `h1`'), L('Tutti i `p`', 'All `p`s'), L('`h1` e `p`', '`h1` and `p`'), L('I `p` dentro `h1`', 'The `p`s inside `h1`')], 0, L('`+` è il fratello adiacente.', '`+` is the adjacent sibling.')),
            Q('c3', L('Che cosa significa `[href$=".pdf"]`?', 'What does `[href$=".pdf"]` mean?'), [L('Finisce con .pdf', 'Ends with .pdf'), L('Inizia con .pdf', 'Starts with .pdf'), L('Contiene pdf', 'Contains pdf'), L('Costa pdf', 'Costs pdf')], 0, L('Il dollaro indica la fine.', 'The dollar marks the end.')),
        ], task('css', L('Togli i pallini ai figli diretti di `.lista` (`list-style-type: none`) e rendi blu (`blue`) il `.nota` subito dopo `.titolo`.', 'Remove the bullets from the direct children of `.lista` (`list-style-type: none`) and make blue (`blue`) the `.nota` right after `.titolo`.'), '', [css('.lista li', 'list-style-type', 'none', L('Niente pallini', 'No bullets')), css('.nota', 'color', 'rgb(0, 0, 255)', L('La nota è blu', 'The note is blue'))], $page, ".lista > li {\n  list-style-type: none;\n}\n.titolo + .nota {\n  color: blue;\n}"), [
            dMatch(pMatch(), [['a b', L('`b` dentro `a`', '`b` inside `a`')], ['a > b', L('`b` figlio diretto', '`b` direct child')], ['a + b', L('`b` subito dopo', '`b` right after')], ['a ~ b', L('`b` fratelli successivi', '`b` following siblings')], ['a, b', L('Sia `a` che `b`', 'Both `a` and `b`')]], L('Un simbolo cambia tutto il significato.', 'One symbol changes the whole meaning.')),
            dBlanks(pFill(), 'a[href___"https"] { color: green; }' . "\n" . 'a[href___".pdf"] { color: red; }', ['^=', '$='], ['*=', '~=', '=='], L('`^=` per l’inizio, `$=` per la fine.', '`^=` for the start, `$=` for the end.')),
        ]),
        lesson('states', L('Stati e interazioni', 'States and interactions'), [
            P('Le pseudo-classi reagiscono a quello che fa l’utente: `:hover` al passaggio, `:active` alla pressione, `:focus` sul campo attivo, `:focus-visible` quando si usa la tastiera, `:disabled` sui controlli spenti.', 'Pseudo-classes react to what the user does: `:hover` on passing over, `:active` on pressing, `:focus` on the active field, `:focus-visible` when using the keyboard, `:disabled` on switched-off controls.'),
            C('.btn:hover { background: tomato; }
.btn:active { transform: translateY(2px); }
.btn:focus-visible { outline: 3px solid gold; }
.btn:disabled { opacity: .5; cursor: not-allowed; }
input:invalid { border-color: red; }', 'css'),
            Tip('Non togliere mai `outline` senza sostituirlo: chi usa la tastiera si perderebbe.', 'Never remove `outline` without replacing it: keyboard users would get lost.'),
        ], [
            Q('s1', L('Quale pseudo-classe vale mentre premi?', 'Which pseudo-class applies while pressing?'), [':active', ':press', ':hover', ':down'], 0, L('`:hover` è il passaggio del mouse.', '`:hover` is the mouse passing over.')),
            Q('s2', L('Perché `:focus-visible` è utile?', 'Why is `:focus-visible` useful?'), [L('Mostra il focus a chi usa la tastiera', 'It shows focus to keyboard users'), L('Nasconde il mouse', 'It hides the mouse'), L('Cambia il font', 'It changes the font'), L('Blocca il click', 'It blocks the click')], 0, L('Non disturba chi clicca col mouse.', 'It does not bother those clicking with a mouse.')),
            Q('s3', L('Quando vale `input:invalid`?', 'When does `input:invalid` apply?'), [L('Il valore non rispetta le regole', 'The value breaks the rules'), L('Il campo è vuoto sempre', 'The field is always empty'), L('Il campo è nascosto', 'The field is hidden'), L('Mai', 'Never')], 0, L('Dipende da `required`, `type`, `pattern`.', 'It depends on `required`, `type`, `pattern`.')),
        ], task('css', L('Scrivi `.btn:hover` con `background: tomato` e `.btn:focus-visible` con un `outline`.', 'Write `.btn:hover` with `background: tomato` and `.btn:focus-visible` with an `outline`.'), '', [re('\.btn:hover\s*\{[^}]*tomato', L('`:hover` è tomato', '`:hover` is tomato')), re('\.btn:focus-visible\s*\{[^}]*outline', L('`:focus-visible` ha un outline', '`:focus-visible` has an outline'))], $page, ".btn:hover {\n  background: tomato;\n}\n.btn:focus-visible {\n  outline: 3px solid gold;\n}"), [
            dMatch(pMatch(), [[':hover', L('Mouse sopra', 'Mouse over')], [':active', L('Mentre premi', 'While pressing')], [':focus', L('Campo attivo', 'Active field')], [':disabled', L('Controllo spento', 'Switched-off control')], [':checked', L('Casella spuntata', 'Ticked box')]], L('Gli stati rendono viva un’interfaccia.', 'States bring an interface to life.')),
            dBug(pBug(), ['.btn {', '  outline: none;', '  padding: 8px;', '}'], 1, '  outline: 3px solid gold;', L('Togliere `outline` lascia senza guida chi naviga con la tastiera.', 'Removing `outline` leaves keyboard users with no guide.')),
        ]),
        lesson('typography', L('Tipografia', 'Typography'), [
            P('Il testo è quasi tutto il web. `line-height` dà respiro, `letter-spacing` spazia le lettere, `text-transform` cambia maiuscole e minuscole, `font-weight` il peso. Un buon testo sta tra 45 e 75 caratteri per riga.', 'Text is nearly all of the web. `line-height` gives room, `letter-spacing` spaces letters, `text-transform` changes case, `font-weight` the weight. Good text sits between 45 and 75 characters per line.'),
            C('@font-face {
  font-family: "Forgia";
  src: url("forgia.woff2") format("woff2");
  font-display: swap;
}
p {
  font-family: "Forgia", sans-serif;
  line-height: 1.6;
  max-width: 65ch;
}', 'css'),
            P('`ch` è la larghezza del carattere zero: `65ch` è una riga comoda. `font-display: swap` mostra subito il testo mentre il font arriva.', '`ch` is the width of the zero character: `65ch` is a comfortable line. `font-display: swap` shows text at once while the font arrives.'),
        ], [
            Q('t1', L('Quale proprietà cambia lo spazio tra le righe?', 'Which property changes the space between lines?'), ['line-height', 'row-gap', 'letter-spacing', 'margin'], 0, L('Un valore tra 1.4 e 1.7 è comodo.', 'A value from 1.4 to 1.7 is comfortable.')),
            Q('t2', L('Che cos’è `65ch`?', 'What is `65ch`?'), [L('Circa 65 caratteri', 'About 65 characters'), L('65 pixel', '65 pixels'), L('65% dello schermo', '65% of the screen'), L('65 righe', '65 lines')], 0, L('Si basa sulla larghezza dello zero.', 'It is based on the width of the zero.')),
            Q('t3', L('Che cosa carica un font personale?', 'What loads a custom font?'), ['@font-face', '@import-font', 'font-load', '@text'], 0, L('Si indicano nome e file.', 'You give a name and a file.')),
        ], task('css', L('Dai a `.nota` un `line-height` di `1.6` e `text-transform: uppercase`.', 'Give `.nota` a `line-height` of `1.6` and `text-transform: uppercase`.'), '.nota {

}', [re('line-height\s*:\s*1\.6', L('L’interlinea è 1.6', 'The line height is 1.6')), css('.nota', 'text-transform', 'uppercase', L('Il testo è in maiuscolo', 'The text is uppercase'))], $page, ".nota {\n  line-height: 1.6;\n  text-transform: uppercase;\n}"), [
            dMatch(pMatch(), [['line-height', L('Spazio tra le righe', 'Space between lines')], ['letter-spacing', L('Spazio tra le lettere', 'Space between letters')], ['text-transform', L('Maiuscole e minuscole', 'Upper and lower case')], ['font-weight', L('Peso del carattere', 'Font weight')], ['text-align', L('Allineamento', 'Alignment')]], L('Piccoli ritocchi, enorme leggibilità.', 'Small touches, huge readability.')),
            dFlash(pFlash(), [[L('Righe lunghissime sono difficili da leggere', 'Very long lines are hard to read'), true], [L('`font-display: swap` nasconde il testo per sempre', '`font-display: swap` hides the text for ever'), false], [L('`woff2` è un formato per font', '`woff2` is a font format'), true], [L('`font-weight: 400` è il peso normale', '`font-weight: 400` is the normal weight'), true], [L('Il font va sempre scritto senza alternative', 'A font is always written with no fallback'), false]], L('Dai sempre un’alternativa dopo il tuo font.', 'Always give a fallback after your font.')),
        ]),
        lesson('overflow', L('Overflow e livelli', 'Overflow and layers'), [
            P('Quando il contenuto è più grande della scatola, `overflow` decide che cosa fare: `visible` lo lascia uscire, `hidden` lo taglia, `auto` aggiunge le barre se servono. `z-index` sceglie chi sta sopra tra gli elementi posizionati.', 'When content is bigger than its box, `overflow` decides what to do: `visible` lets it out, `hidden` cuts it, `auto` adds scrollbars if needed. `z-index` picks who is on top among positioned elements.'),
            C('.riquadro {
  height: 120px;
  overflow: auto;
}
.finestra {
  position: fixed;
  z-index: 10;
}
.testo {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}', 'css'),
            P('Le ultime tre regole, insieme, troncano una riga con i puntini di sospensione.', 'The last three rules, together, cut a line with an ellipsis.'),
        ], [
            Q('o1', L('Quale valore mette le barre solo se servono?', 'Which value adds scrollbars only if needed?'), ['auto', 'scroll', 'hidden', 'bars'], 0, L('`scroll` le mostra sempre.', '`scroll` always shows them.')),
            Q('o2', L('`z-index` funziona su un elemento `static`?', 'Does `z-index` work on a `static` element?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo sui link', 'Only on links'), L('Solo su `body`', 'Only on `body`')], 0, L('Serve un elemento posizionato.', 'It needs a positioned element.')),
            Q('o3', L('Che cosa aggiunge `text-overflow: ellipsis`?', 'What does `text-overflow: ellipsis` add?'), [L('I puntini', 'An ellipsis'), L('Una barra', 'A scrollbar'), L('Un’ombra', 'A shadow'), L('Un link', 'A link')], 0, L('Funziona con `overflow: hidden`.', 'It works with `overflow: hidden`.')),
        ], task('css', L('Dai a `.lista` un `height` di `40px` e `overflow: auto`.', 'Give `.lista` a `height` of `40px` and `overflow: auto`.'), '.lista {

}', [css('.lista', 'height', '40px', L('L’altezza è 40px', 'The height is 40px')), css('.lista', 'overflow-y', 'auto', L('Si scorre se serve', 'It scrolls if needed'))], $page, ".lista {\n  height: 40px;\n  overflow: auto;\n}"), [
            dBlanks(pFill(), ".riga {\n  white-space: ___;\n  overflow: ___;\n  text-overflow: ___;\n}", ['nowrap', 'hidden', 'ellipsis'], ['wrap', 'auto', 'dots'], L('Una ricetta da ricordare per i testi troppo lunghi.', 'A recipe to remember for text that is too long.')),
            dBug(pBug(), ['.finestra {', '  z-index: 100;', '  background: white;', '}'], 1, '  position: fixed; z-index: 100;', L('Senza `position`, `z-index` non ha effetto.', 'Without `position`, `z-index` has no effect.')),
        ]),
        lesson('components', L('Bottoni e menu', 'Buttons and menus'), [
            P('Con quello che sai puoi già costruire componenti veri. Un bottone è una scatola con padding, angoli, colori e stati. Un menu è una lista di link senza pallini, con gli elementi in riga.', 'With what you know you can already build real components. A button is a box with padding, corners, colours and states. A menu is a list of links without bullets, with items in a row.'),
            C('.btn {
  display: inline-block;
  padding: 10px 18px;
  border: 0;
  border-radius: 12px;
  background: #FF5B2E;
  color: white;
  cursor: pointer;
}
.menu a {
  display: inline-block;
  padding: 8px 12px;
  text-decoration: none;
}', 'css'),
            Tip('Un bottone deve essere facile da toccare: almeno 44 pixel di altezza su telefono.', 'A button must be easy to tap: at least 44 pixels high on phones.'),
        ], [
            Q('b1', L('Che cosa toglie la sottolineatura?', 'What removes the underline?'), ['text-decoration: none', 'underline: 0', 'border: none', 'font-style: none'], 0, L('I link la hanno di serie.', 'Links have it by default.')),
            Q('b2', L('Quale cursore indica che si può cliccare?', 'Which cursor shows you can click?'), ['pointer', 'hand', 'click', 'finger'], 0, L('`cursor: pointer`.', '`cursor: pointer`.')),
            Q('b3', L('Quanto dovrebbe essere alto un bottone su telefono?', 'How high should a button be on phones?'), [L('Almeno 44px', 'At least 44px'), L('10px', '10px'), L('Non importa', 'It does not matter'), L('500px', '500px')], 0, L('Le dita sono meno precise del mouse.', 'Fingers are less precise than a mouse.')),
        ], task('css', L('Stila `.btn`: `padding` di `12px`, `border-radius` di `12px`, `cursor: pointer`.', 'Style `.btn`: `padding` of `12px`, `border-radius` of `12px`, `cursor: pointer`.'), '.btn {

}', [css('.btn', 'padding-top', '12px', L('Il padding è 12px', 'The padding is 12px')), css('.btn', 'border-top-left-radius', '12px', L('Gli angoli sono tondi', 'The corners are round')), css('.btn', 'cursor', 'pointer', L('Il cursore è giusto', 'The cursor is right'))], $page, ".btn {\n  padding: 12px;\n  border-radius: 12px;\n  cursor: pointer;\n}"), [
            dOrder(pSort(), ['.btn {', '  padding: 10px 18px;', '  border-radius: 12px;', '}', '.btn:hover {', '  background: tomato;', '}'], L('Prima lo stato normale, poi quello al passaggio.', 'First the normal state, then the hover one.')),
            dFlash(pFlash(), [[L('Un link può sembrare un bottone', 'A link can look like a button'), true], [L('`cursor: pointer` serve sui bottoni', '`cursor: pointer` is for buttons'), true], [L('`padding` allarga l’area da toccare', '`padding` widens the tap area'), true], [L('`text-decoration: none` toglie il colore', '`text-decoration: none` removes the colour'), false]], L('Il `padding` rende più grande anche l’area cliccabile.', '`padding` also enlarges the clickable area.')),
        ]),
    ],
];
