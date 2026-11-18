<?php
/** CSS intermediate: the step before flexbox and grid (selectors, states, typography, overflow, components, logical properties, forms, images and filters). */

$page = '<nav class="menu"><a href="#">Home</a><a href="#">Corsi</a></nav>
<h1 class="titolo">Fucina</h1>
<p class="nota">Una nota.</p>
<ul class="lista"><li>Uno</li><li>Due</li><li>Tre</li></ul>
<button class="btn">Inizia</button>';

return [
    'id' => 'css-intermediate', 'family' => 'css', 'after' => 'css-base',
    'title' => L('CSS intermedio', 'Intermediate CSS'), 'short' => L('CSS intermedio', 'CSS Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Selettori avanzati, stati, tipografia, overflow, menu e bottoni, proprietà logiche, stile dei moduli, immagini e filtri.', 'Advanced selectors, states, typography, overflow, menus and buttons, logical properties, form styling, images and filters.'),
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
            Q('o2', L('`z-index` funziona su un normale elemento `static`, fuori da flex e grid?', 'Does `z-index` work on a plain `static` element, outside flex and grid?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo sui link', 'Only on links'), L('Solo su `body`', 'Only on `body`')], 0, L('Serve un elemento posizionato, oppure un figlio di un contenitore flex o grid.', 'It needs a positioned element, or a child of a flex or grid container.')),
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
        lesson('logical', L('Proprietà logiche', 'Logical properties'), [
            P('Le proprietà logiche descrivono i lati seguendo la direzione del testo, non lo schermo. `inline` è la direzione della riga (orizzontale in italiano), `block` quella in cui le righe si impilano. `margin-inline-start` è il margine sinistro in italiano e quello destro in arabo.', 'Logical properties describe sides by the direction of the text, not by the screen. `inline` is the direction of the line (horizontal in English), `block` the one in which lines stack. `margin-inline-start` is the left margin in English and the right one in Arabic.'),
            C('.card {
  padding-block: 16px;      /* sopra e sotto */
  padding-inline: 24px;     /* ai lati */
  margin-inline: auto;      /* centra il blocco */
  border-inline-start: 4px solid #FF5B2E;
  inline-size: min(100%, 40rem); /* la larghezza */
}', 'css'),
            P('`inline-size` è la larghezza, `block-size` l’altezza. Con `dir="rtl"` sulla pagina tutto si ribalta da solo, senza regole nuove.', '`inline-size` is the width, `block-size` the height. With `dir="rtl"` on the page everything flips by itself, with no new rules.'),
        ], [
            Q('l1', L('In italiano, qual è la direzione `inline`?', 'In English, what is the `inline` direction?'), [L('Verticale', 'Vertical'), L('Orizzontale', 'Horizontal'), L('Diagonale', 'Diagonal'), L('Dipende dallo schermo', 'It depends on the screen')], 1, L('È la direzione in cui scorre una riga di testo.', 'It is the direction a line of text runs in.')),
            Q('l2', L('Che cosa fa `margin-inline: auto` su un blocco con una larghezza?', 'What does `margin-inline: auto` do on a block with a width?'), [L('Toglie tutti i margini', 'It removes all margins'), L('Lo centra in verticale', 'It centres it vertically'), L('Lo centra orizzontalmente', 'It centres it horizontally'), L('Niente, non è valido', 'Nothing, it is not valid')], 2, L('È il `margin: 0 auto` logico.', 'It is the logical `margin: 0 auto`.')),
            Q('l3', L('Quale proprietà è la larghezza logica?', 'Which property is the logical width?'), ['block-size', 'line-width', 'size-x', 'inline-size'], 3, L('`block-size` è l’altezza.', '`block-size` is the height.')),
        ], task('css', L('Con le proprietà logiche dai a `.nota` un `padding-inline` di `20px` e un `border-inline-start` di `4px solid orange`.', 'Using logical properties, give `.nota` a `padding-inline` of `20px` and a `border-inline-start` of `4px solid orange`.'), '.nota {

}', [re('padding-inline\s*:\s*20px', L('Usi `padding-inline`', 'You use `padding-inline`')), re('border-inline-start\s*:', L('Usi `border-inline-start`', 'You use `border-inline-start`')), css('.nota', 'padding-left', '20px', L('Il padding laterale è 20px', 'The side padding is 20px')), css('.nota', 'border-left-width', '4px', L('Il bordo d’inizio è 4px', 'The start border is 4px'))], $page, ".nota {\n  padding-inline: 20px;\n  border-inline-start: 4px solid orange;\n}"), [
            dMatch(pMatch(), [['padding-inline', L('Spazio ai lati della riga', 'Space at the sides of the line')], ['padding-block', L('Spazio sopra e sotto', 'Space above and below')], ['inline-size', L('Larghezza', 'Width')], ['block-size', L('Altezza', 'Height')], ['margin-inline-start', L('Margine all’inizio della riga', 'Margin at the start of the line')]], L('Lati che seguono la lingua, non lo schermo.', 'Sides that follow the language, not the screen.')),
            dFlash(pFlash(), [[L('In arabo `inline-start` è a destra', 'In Arabic `inline-start` is on the right'), true], [L('In italiano `block` è la direzione orizzontale', 'In English `block` is the horizontal direction'), false], [L('`padding-inline: 8px` vale per entrambi i lati', '`padding-inline: 8px` applies to both sides'), true], [L('Le proprietà logiche richiedono JavaScript', 'Logical properties need JavaScript'), false]], L('Scrivi una volta, vale per ogni lingua.', 'Write once, it works for every language.')),
        ]),
        lesson('forms-style', L('Stile dei moduli', 'Styling forms'), [
            P('Anche i campi dei moduli si possono stilare. `accent-color` colora in una riga checkbox, radio e cursori. `:user-invalid` segna un campo sbagliato solo dopo che l’utente l’ha toccato, mentre `:invalid` scatta già all’apertura della pagina. `::placeholder` dà lo stile al testo di esempio.', 'Form fields can be styled too. `accent-color` colours checkboxes, radios and sliders in one line. `:user-invalid` marks a field wrong only after the user has touched it, while `:invalid` already fires when the page opens. `::placeholder` styles the sample text.'),
            C('input, select, textarea {
  font: inherit;
  padding: 8px 12px;
  border: 1px solid #999;
  border-radius: 8px;
}
:root { accent-color: #FF5B2E; }
input:user-invalid { border-color: crimson; }
input::placeholder { color: #767676; }
input:focus-visible { outline: 3px solid gold; }', 'css'),
            P('I campi di serie non ereditano il carattere della pagina: `font: inherit` sistema tutto. Un segnaposto chiaro deve comunque avere abbastanza contrasto, e non sostituisce la `label`.', 'Fields do not inherit the page font by default: `font: inherit` fixes it. A light placeholder still needs enough contrast, and it does not replace the `label`.'),
        ], [
            Q('s1', L('Quale proprietà colora checkbox e radio?', 'Which property colours checkboxes and radios?'), ['color', 'fill', 'accent-color', 'tint'], 2, L('Una riga sola, anche su `:root`, perché si eredita.', 'A single line, even on `:root`, because it is inherited.')),
            Q('s2', L('Qual è la differenza tra `:user-invalid` e `:invalid`?', 'What is the difference between `:user-invalid` and `:invalid`?'), [L('`:user-invalid` scatta solo dopo che l’utente ha interagito', '`:user-invalid` only fires after the user has interacted'), L('Nessuna, sono sinonimi', 'None, they are synonyms'), L('`:user-invalid` controlla i dati sul server', '`:user-invalid` checks the data on the server'), L('`:invalid` vale solo dopo l’invio', '`:invalid` only applies after submitting')], 0, L('Niente bordi rossi prima ancora di cominciare a scrivere.', 'No red borders before you even start typing.')),
            Q('s3', L('Perché scrivere `font: inherit` sui campi?', 'Why write `font: inherit` on fields?'), [L('Rende il testo in grassetto', 'It makes the text bold'), L('È obbligatorio nei moduli', 'It is mandatory in forms'), L('Velocizza l’invio del modulo', 'It speeds up sending the form'), L('Di serie i campi non ereditano il font della pagina', 'By default fields do not inherit the page font')], 3, L('Altrimenti il testo dei campi sembra di un’altra pagina.', 'Otherwise the field text looks like it belongs to another page.')),
        ], task('css', L('Imposta su `:root` un `accent-color` `#FF5B2E`, dai agli `input` `font: inherit` e un bordo `crimson` agli `input:user-invalid`.', 'Set an `accent-color` of `#FF5B2E` on `:root`, give `input` elements `font: inherit` and a `crimson` border to `input:user-invalid`.'), '', [css('input[type="checkbox"]', 'accent-color', 'rgb(255, 91, 46)', L('La casella usa il colore del marchio', 'The checkbox uses the brand colour')), re('input\s*\{[^}]*font\s*:\s*inherit', L('I campi ereditano il font', 'Fields inherit the font')), re('input:user-invalid\s*\{[^}]*border(-color)?\s*:[^;}]*crimson', L('L’errore si vede dopo il tocco', 'The error shows after touching'))], '<form>
  <label for="m">Email</label>
  <input id="m" type="email" required>
  <label><input type="checkbox" checked> Ricordami</label>
</form>', ":root { accent-color: #FF5B2E; }\ninput { font: inherit; }\ninput:user-invalid { border-color: crimson; }"), [
            dBlanks(pFill(), ":root { ___: #FF5B2E; }\ninput { font: ___; }\ninput:___ { border-color: crimson; }", ['accent-color', 'inherit', 'user-invalid'], ['color', 'initial', 'hover'], L('Colore dei controlli, font della pagina, errore dopo il tocco.', 'Control colour, page font, error after touching.')),
            dMatch(pMatch(), [['accent-color', L('Colore di checkbox e radio', 'Colour of checkboxes and radios')], [':user-invalid', L('Errore dopo l’interazione', 'Error after interaction')], ['::placeholder', L('Testo di esempio', 'Sample text')], ['font: inherit', L('Il font della pagina', 'The page font')], [':focus-visible', L('Focus da tastiera', 'Keyboard focus')]], L('Moduli belli e chiari, senza librerie.', 'Nice, clear forms, with no libraries.')),
        ]),
        lesson('images-filters', L('Immagini e filtri', 'Images and filters'), [
            P('`object-fit` decide come un’immagine riempie la sua scatola: `cover` la riempie tutta e taglia ciò che avanza, `contain` la mostra intera e lascia spazio vuoto. `object-position` sceglie quale parte resta visibile.', '`object-fit` decides how an image fills its box: `cover` fills it all and crops what is left over, `contain` shows it whole and leaves empty space. `object-position` picks which part stays visible.'),
            C('.copertina {
  width: 100%;
  aspect-ratio: 16 / 9;
  object-fit: cover;
  object-position: center top;
}
.spento { filter: grayscale(1); }
.spento:hover { filter: none; }
.vetro {
  background: rgb(255 255 255 / .6);
  backdrop-filter: blur(8px);
}', 'css'),
            P('`filter` applica effetti da fotoritocco all’elemento stesso: `blur()`, `brightness()`, `grayscale()`, `contrast()`. `backdrop-filter` li applica a ciò che sta **dietro** l’elemento, per l’effetto vetro smerigliato.', '`filter` applies photo-editing effects to the element itself: `blur()`, `brightness()`, `grayscale()`, `contrast()`. `backdrop-filter` applies them to what is **behind** the element, for the frosted-glass effect.'),
        ], [
            Q('o1', L('Quale valore di `object-fit` riempie la scatola tagliando l’immagine?', 'Which `object-fit` value fills the box by cropping the image?'), ['contain', 'fill', 'none', 'cover'], 3, L('`fill` invece la deforma per farla entrare.', '`fill` stretches it to fit instead.')),
            Q('o2', L('Che cosa fa `filter: grayscale(1)`?', 'What does `filter: grayscale(1)` do?'), [L('Rende l’elemento in bianco e nero', 'It makes the element black and white'), L('Nasconde l’elemento', 'It hides the element'), L('Inverte i colori', 'It inverts the colours'), L('Lo sfoca', 'It blurs it')], 0, L('`0` lascia i colori, `1` li toglie del tutto.', '`0` keeps the colours, `1` removes them completely.')),
            Q('o3', L('Su che cosa agisce `backdrop-filter`?', 'What does `backdrop-filter` act on?'), [L('Sull’elemento stesso', 'On the element itself'), L('Su ciò che sta dietro l’elemento', 'On what is behind the element'), L('Solo sul testo', 'Only on the text'), L('Solo sui figli', 'Only on the children')], 1, L('Serve uno sfondo almeno in parte trasparente per vedere l’effetto.', 'It needs an at least partly transparent background to show.')),
        ], task('css', L('Dai a `.copertina` `object-fit: cover` e un filtro `grayscale` che la renda in bianco e nero.', 'Give `.copertina` `object-fit: cover` and a `grayscale` filter that makes it black and white.'), ".copertina {\n  width: 100%;\n  aspect-ratio: 16 / 9;\n}", [css('.copertina', 'object-fit', 'cover', L('L’immagine riempie la scatola', 'The image fills the box')), re('filter\s*:\s*grayscale\(\s*(1|100%)\s*\)', L('È in bianco e nero', 'It is black and white'))], '<img class="copertina" src="paesaggio.jpg" alt="Un paesaggio">', ".copertina {\n  width: 100%;\n  aspect-ratio: 16 / 9;\n  object-fit: cover;\n  filter: grayscale(1);\n}"), [
            dMatch(pMatch(), [['cover', L('Riempie e taglia', 'Fills and crops')], ['contain', L('Intera, con spazio vuoto', 'Whole, with empty space')], ['blur()', L('Sfoca', 'Blurs')], ['grayscale()', L('Toglie i colori', 'Removes the colours')], ['backdrop-filter', L('Effetto su ciò che sta dietro', 'Effect on what is behind')]], L('Effetti da fotoritocco, scritti in CSS.', 'Photo-editing effects, written in CSS.')),
            dFlash(pFlash(), [[L('`object-fit: cover` può tagliare i bordi dell’immagine', '`object-fit: cover` can crop the image edges'), true], [L('`filter` modifica il file dell’immagine sul server', '`filter` changes the image file on the server'), false], [L('Si possono combinare più filtri nella stessa regola', 'Several filters can be combined in one rule'), true], [L('`contain` deforma l’immagine', '`contain` stretches the image'), false]], L('Il file resta com’è: cambia solo come lo disegna il browser.', 'The file stays as it is: only how the browser draws it changes.')),
        ]),
    ],
];
