<?php
/** CSS base: selectors, colours, box model, layout basics and the cascade. */

$page = '<h1 class="titolo">Fucina</h1>
<p>Il primo paragrafo.</p>
<p class="nota">Una nota.</p>
<div id="box">Una scatola</div>
<a href="#">Un link</a>';

return [
    'id' => 'css-base', 'family' => 'css', 'after' => 'html-base',
    'title' => L('CSS base', 'CSS Basics'), 'short' => L('CSS base', 'CSS Basics'), 'level' => L('Base', 'Basic'),
    'desc' => L('Dai forma alle pagine: selettori, colori, testi, box model, posizionamento e cascata.', 'Give pages their shape: selectors, colours, text, box model, positioning and the cascade.'),
    'lessons' => [
        lesson('selectors', L('Regole e selettori', 'Rules and selectors'), [
            P('Il CSS descrive l’**aspetto** della pagina con delle regole. Una regola ha un selettore, che sceglie gli elementi, e delle dichiarazioni `proprietà: valore`.', 'CSS describes the **look** of the page with rules. A rule has a selector, which picks the elements, and `property: value` declarations.'),
            C('p { color: navy; }          /* tutti i paragrafi */
.nota { font-size: 14px; }  /* la classe nota */
#box { width: 200px; }      /* l’id box */
nav a { color: red; }       /* i link dentro nav */
a:hover { color: green; }   /* il link sotto al mouse */', 'css'),
            P('Una classe si riusa quante volte vuoi, un `id` è unico nella pagina. `:hover` è una pseudo-classe: indica uno stato dell’elemento. Lo spazio tra due selettori significa «dentro».', 'A class can be reused as often as you want, an `id` is unique in the page. `:hover` is a pseudo-class: it marks a state of the element. The space between two selectors means “inside”.'),
        ], [
            Q('s1', L('Quale selettore sceglie la classe `nota`?', 'Which selector picks the class `nota`?'), ['#nota', '.nota', 'nota', '*nota'], 1, L('Il punto indica la classe, il cancelletto l’id.', 'The dot marks a class, the hash an id.')),
            Q('s2', L('Che cosa sceglie `nav a`?', 'What does `nav a` pick?'), [L('`nav` e tutti i link', '`nav` and all the links'), L('I link dentro `nav`', 'The links inside `nav`'), L('Solo il primo link', 'Only the first link'), L('Nulla', 'Nothing')], 1, L('Lo spazio è il combinatore discendente.', 'The space is the descendant combinator.')),
            Q('s3', L('Che cos’è `:hover`?', 'What is `:hover`?'), [L('Una pseudo-classe', 'A pseudo-class'), L('Una proprietà', 'A property'), L('Un id', 'An id'), L('Un tag', 'A tag')], 0, L('Le pseudo-classi indicano uno stato o una posizione.', 'Pseudo-classes mark a state or a position.')),
        ], task('css', L('Colora i paragrafi con la classe `nota` di rosso (`red`) e imposta a `200px` la larghezza dell’id `box`.', 'Colour the paragraphs with the `nota` class red (`red`) and set the width of the `box` id to `200px`.'), '/* Scrivi qui le tue regole */
', [css('.nota', 'color', 'rgb(255, 0, 0)', L('La nota è rossa', 'The note is red')), css('#box', 'width', '200px', L('Il box è largo 200px', 'The box is 200px wide'))], $page)),

        lesson('colors', L('Colori e testi', 'Colours and text'), [
            P('Un colore si può scrivere in molti modi: per nome, in esadecimale, in `rgb()` o in `hsl()`. L’ultimo numero o la barra indicano la trasparenza.', 'A colour can be written in many ways: by name, in hexadecimal, in `rgb()` or in `hsl()`. A final value after a slash sets the transparency.'),
            C('h1 {
  color: #E8590C;
  background-color: rgb(255 240 230);
  font-family: Georgia, serif;
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.3;
  text-align: center;
}', 'css'),
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
            C('* { box-sizing: border-box; }

.card {
  width: 300px;
  padding: 16px;
  border: 1px solid #ccc;
  margin: 0 auto;
  border-radius: 12px;
}', 'css'),
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
            C('a.bottone { display: inline-block; padding: 8px 16px; }
.nascosto { display: none; }
.invisibile { visibility: hidden; }', 'css'),
            P('`inline-block` scorre nel testo ma accetta le dimensioni. `display: none` toglie l’elemento dalla pagina, mentre `visibility: hidden` lo nasconde lasciando il suo spazio.', '`inline-block` flows in the text but accepts sizes. `display: none` takes the element off the page, while `visibility: hidden` hides it and leaves its space.'),
        ], [
            Q('d1', L('Quale elemento è `inline` di natura?', 'Which element is `inline` by default?'), ['<div>', '<p>', '<a>', '<h1>'], 2, L('`<a>` scorre nel testo senza andare a capo.', '`<a>` flows in the text without a line break.')),
            Q('d2', L('Qual è la differenza tra `display: none` e `visibility: hidden`?', 'What is the difference between `display: none` and `visibility: hidden`?'), [L('Nessuna', 'None'), L('Il primo toglie anche lo spazio', 'The first removes the space too'), L('Il secondo toglie lo spazio', 'The second removes the space'), L('Il primo è solo per il testo', 'The first is only for text')], 1, L('`visibility: hidden` lascia un buco della stessa misura.', '`visibility: hidden` leaves a hole of the same size.')),
            Q('d3', L('Quale valore scorre nel testo e accetta `width`?', 'Which value flows in the text and accepts `width`?'), ['block', 'inline', 'inline-block', 'flow'], 2, L('È una via di mezzo tra `inline` e `block`.', 'It is halfway between `inline` and `block`.')),
        ], task('css', L('Fai diventare il link un `inline-block` con `padding` di `10px`, e nascondi `.nota` con `display: none`.', 'Turn the link into an `inline-block` with `10px` of `padding`, and hide `.nota` with `display: none`.'), '', [css('a', 'display', 'inline-block', L('Il link è inline-block', 'The link is inline-block')), css('a', 'padding-left', '10px', L('Il link ha 10px di padding', 'The link has 10px of padding')), css('.nota', 'display', 'none', L('La nota è nascosta', 'The note is hidden'))], $page)),

        lesson('position', L('Il posizionamento', 'Positioning'), [
            P('`position` sposta gli elementi fuori dal flusso normale. Il valore predefinito è `static`. Con `relative` l’elemento resta al suo posto e fa da riferimento per i figli `absolute`.', '`position` moves elements out of the normal flow. The default value is `static`. With `relative` the element stays in place and acts as a reference for `absolute` children.'),
            C('.card { position: relative; }
.etichetta { position: absolute; top: 8px; right: 8px; }
.barra { position: sticky; top: 0; }
.chat { position: fixed; bottom: 16px; right: 16px; z-index: 10; }', 'css'),
            P('`fixed` resta fermo rispetto allo schermo, `sticky` scorre e poi si ferma alla soglia indicata. `z-index` decide chi sta sopra tra gli elementi posizionati.', '`fixed` stays still relative to the screen, `sticky` scrolls and then stops at the given threshold. `z-index` decides who is on top among positioned elements.'),
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
#box { color: red; }       /* 1,0,0 → batte tutte */', 'css'),
            P('Alcune proprietà, come `color` e `font-family`, si **ereditano** dai genitori; altre, come `margin` e `border`, no. `!important` scavalca tutto: evitalo, perché rende il CSS difficile da correggere.', 'Some properties, like `color` and `font-family`, are **inherited** from parents; others, like `margin` and `border`, are not. `!important` overrides everything: avoid it, because it makes CSS hard to fix.'),
        ], [
            Q('k1', L('Chi vince tra `.nota` e `#box` sullo stesso elemento?', 'Which wins between `.nota` and `#box` on the same element?'), ['.nota', '#box', L('L’ultima scritta', 'The last one written'), L('Nessuna', 'Neither')], 1, L('L’`id` ha una specificità più alta della classe.', 'An `id` has higher specificity than a class.')),
            Q('k2', L('Con la stessa specificità chi vince?', 'With the same specificity, which wins?'), [L('La prima scritta', 'The first written'), L('L’ultima scritta', 'The last written'), L('La più corta', 'The shortest'), L('Nessuna', 'Neither')], 1, L('È il senso della parola «cascata».', 'That is the meaning of the word “cascade”.')),
            Q('k3', L('Quale proprietà si eredita?', 'Which property is inherited?'), ['margin', 'border', 'color', 'padding'], 2, L('Il colore del testo passa dai genitori ai figli.', 'The text colour passes from parents to children.')),
        ], task('css', L('Rendi blu (`blue`) il testo di `p.nota` senza toccare la regola esistente e senza `!important`.', 'Make the text of `p.nota` blue (`blue`) without touching the existing rule and without `!important`.'), '.nota { color: red; }

', [css('p.nota', 'color', 'rgb(0, 0, 255)', L('La nota è blu', 'The note is blue')), re('^(?![\s\S]*!important)', L('Niente `!important`', 'No `!important`'))], $page)),
    ],
];
