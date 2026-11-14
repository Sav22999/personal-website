<?php
/** Levels added to the first courses after their first version: the harder ones, at the end of each course. */

$page = '<h1 class="titolo">Fucina</h1>
<p>Il primo paragrafo.</p>
<p class="nota">Una nota.</p>
<div id="box">Una scatola</div>
<a href="#">Un link</a>';

return [
    'html-base' => [
        lesson('entities', L('Caratteri speciali e commenti', 'Special characters and comments'), [
            P('Alcuni caratteri hanno un significato per l’HTML: per mostrare `<` si scrive un’**entità**, come `&lt;`. Le entità iniziano con `&` e finiscono con `;`.', 'Some characters mean something to HTML: to show `<` you write an **entity**, like `&lt;`. Entities start with `&` and end with `;`.'),
            C('<p>2 &lt; 3 &amp; 3 &gt; 2</p>
<p>Fucina &copy; 2026</p>
<!-- Questo non si vede nella pagina -->', 'html'),
            P('I commenti `<!-- … -->` sono note per chi legge il codice: il browser li ignora, ma chiunque può leggerli aprendo il sorgente.', 'Comments `<!-- … -->` are notes for those reading the code: the browser ignores them, but anyone can read them in the source.'),
        ], [
            Q('e1', L('Come si mostra il simbolo `<` nel testo?', 'How do you show the `<` symbol in text?'), ['&lt;', '&gt;', '<<', '\\<'], 0, L('`lt` sta per «less than».', '`lt` stands for “less than”.')),
            Q('e2', L('Come inizia un commento HTML?', 'How does an HTML comment start?'), ['//', '/*', '<!--', '#'], 2, L('Si apre con `<!--` e si chiude con `-->`.', 'It opens with `<!--` and closes with `-->`.')),
            Q('e3', L('Un commento è segreto?', 'Is a comment secret?'), [L('Sì, sempre', 'Yes, always'), L('No, si legge nel sorgente', 'No, it can be read in the source'), L('Solo in `<head>`', 'Only in `<head>`'), L('Solo sul server', 'Only on the server')], 1, L('Mai scrivere password o dati privati in un commento.', 'Never write passwords or private data in a comment.')),
        ], task('html', L('Scrivi un paragrafo che contenga l’entità `&copy;`.', 'Write a paragraph containing the `&copy;` entity.'), '<!-- scrivi qui sotto -->
', [has('p', L('C’è un paragrafo', 'There is a paragraph')), re('&copy;', L('Usi l’entità `&copy;`', 'You use the `&copy;` entity'))], '', '<p>Fucina &copy; 2026</p>'), [
            dMatch(pMatch(), [['&lt;', '<'], ['&gt;', '>'], ['&amp;', '&'], ['&copy;', '©'], ['&euro;', '€']], L('Ogni entità è un modo sicuro di scrivere quel simbolo.', 'Each entity is a safe way of writing that symbol.')),
            dBug(pBug(), ['<p>Il prezzo è 5 &euro;</p>', '<!-- Nota per il team --', '<p>Fine</p>'], 1, '<!-- Nota per il team -->', L('Un commento si chiude con `-->`: senza, nasconde tutto quello che segue.', 'A comment closes with `-->`: without it, it hides everything after it.')),
        ]),
        lesson('page-project', L('Progetto: la tua prima pagina', 'Project: your first page'), [
            P('Ora metti tutto insieme: una pagina con titolo, testo, immagine, lista e link. Ogni pezzo lo conosci già: la bravura sta nell’**ordine**.', 'Now put it all together: a page with a heading, text, an image, a list and a link. You already know every piece: the skill is in the **order**.'),
            C('<h1>Ciao, sono Sav</h1>
<img src="me.jpg" alt="La mia foto">
<p>Imparo a costruire siti.</p>
<ul>
  <li>HTML</li>
  <li>CSS</li>
</ul>
<a href="contatti.html">Scrivimi</a>', 'html'),
            Tip('Scrivi prima il contenuto, poi pensa allo stile: una pagina ben strutturata è già mezza fatta.', 'Write the content first, then think about style: a well-structured page is already half done.'),
        ], [
            Q('p1', L('Quanti `<h1>` conviene avere in una pagina?', 'How many `<h1>` should a page have?'), [L('Uno', 'One'), L('Almeno tre', 'At least three'), L('Uno per paragrafo', 'One per paragraph'), L('Nessuno', 'None')], 0, L('`h1` è il titolo della pagina: gli altri scendono di livello.', '`h1` is the title of the page: the others go down a level.')),
            Q('p2', L('Che cosa manca a `<img src="a.jpg">`?', 'What is `<img src="a.jpg">` missing?'), ['alt', 'href', 'title', 'width'], 0, L('`alt` descrive l’immagine a chi non può vederla.', '`alt` describes the image to those who cannot see it.')),
            Q('p3', L('Che cosa va scritto per primo?', 'What should you write first?'), [L('Il contenuto', 'The content'), L('I colori', 'The colours'), L('Le animazioni', 'The animations'), L('Il logo', 'The logo')], 0, L('Lo stile si appoggia alla struttura, non il contrario.', 'Style rests on the structure, not the other way round.')),
        ], task('html', L('Crea una pagina con un `h1`, un `p`, una lista `ul` con almeno un `li` e un link `a`.', 'Create a page with an `h1`, a `p`, a `ul` list with at least one `li` and an `a` link.'), '', [has('h1', L('C’è un titolo', 'There is a heading')), has('p', L('C’è un paragrafo', 'There is a paragraph')), has('ul li', L('C’è una lista', 'There is a list')), has('a', L('C’è un link', 'There is a link'))], '', "<h1>Ciao</h1>\n<p>Testo</p>\n<ul><li>Uno</li></ul>\n<a href=\"#\">Link</a>"), [
            dOrder(pSort(), ['<body>', '  <h1>Il mio sito</h1>', '  <p>Benvenuto!</p>', '  <a href="info.html">Info</a>', '</body>'], L('Dall’alto in basso, come la leggerà il visitatore.', 'From top to bottom, as the visitor will read it.')),
            dFlash(pFlash(), [[L('`<a>` serve per i link', '`<a>` is for links'), true], [L('`<img>` ha un tag di chiusura', '`<img>` has a closing tag'), false], [L('`<li>` sta dentro `<ul>`', '`<li>` goes inside `<ul>`'), true], [L('`<p>` è un titolo', '`<p>` is a heading'), false]], L('Sai già riconoscere tutti i pezzi di una pagina.', 'You can already recognise every piece of a page.')),
        ]),
    ],
    'html-advanced' => [
        lesson('data-attrs', L('Attributi data e template', 'Data attributes and templates'), [
            P('Gli attributi `data-*` conservano informazioni personalizzate su un elemento: li leggeranno CSS e JavaScript. Il nome dopo `data-` lo scegli tu.', '`data-*` attributes keep custom information on an element, for CSS and JavaScript to read. You choose the name after `data-`.'),
            C('<button data-prodotto="42" data-colore="rosso">Compra</button>

<template id="riga">
  <li class="voce"></li>
</template>', 'html'),
            P('`<template>` contiene HTML che il browser non mostra: è uno stampo da copiare al momento giusto.', '`<template>` holds HTML the browser does not show: it is a mould to copy at the right moment.'),
        ], [
            Q('d1', L('Quale attributo è valido?', 'Which attribute is valid?'), ['data-id', 'id-data', 'my:data', 'data_'], 0, L('Inizia sempre con `data-`.', 'It always starts with `data-`.')),
            Q('d2', L('Il contenuto di `<template>` si vede subito?', 'Is the content of `<template>` shown at once?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo su telefono', 'Only on phones'), L('Solo in grassetto', 'Only in bold')], 1, L('Resta nascosto finché uno script non lo copia.', 'It stays hidden until a script copies it.')),
            Q('d3', L('A che cosa servono i `data-*`?', 'What are `data-*` for?'), [L('A salvare dati su un elemento', 'To keep data on an element'), L('A collegare un database', 'To connect a database'), L('A cambiare font', 'To change fonts'), L('A inviare email', 'To send emails')], 0, L('Sono comodi per passare dati dall’HTML al JavaScript.', 'They are handy for passing data from HTML to JavaScript.')),
        ], task('html', L('Crea un `button` con l’attributo `data-prezzo` uguale a `10`.', 'Create a `button` with the `data-prezzo` attribute equal to `10`.'), '', [has('button[data-prezzo="10"]', L('Il bottone ha `data-prezzo="10"`', 'The button has `data-prezzo="10"`'))], '', '<button data-prezzo="10">Compra</button>'), [
            dBlanks(pFill(), '<li ___-id="7">Martello</li>' . "\n" . '<___ id="stampo">…</template>', ['data', 'template'], ['aria', 'div'], L('`data-` per i dati, `template` per gli stampi.', '`data-` for data, `template` for moulds.')),
            dFlash(pFlash(), [[L('`data-colore` è un attributo valido', '`data-colore` is a valid attribute'), true], [L('`<template>` si vede appena apri la pagina', '`<template>` shows as soon as you open the page'), false], [L('Il CSS può leggere gli attributi data', 'CSS can read data attributes'), true], [L('`data-*` serve solo alle immagini', '`data-*` is only for images'), false]], L('Dati nascosti nell’HTML, pronti per CSS e JavaScript.', 'Data hidden in the HTML, ready for CSS and JavaScript.')),
        ]),
        lesson('seo', L('SEO e condivisione', 'SEO and sharing'), [
            P('I motori di ricerca leggono `title` e `description`. Le reti sociali leggono i tag **Open Graph**, che decidono titolo e immagine quando condividi un link.', 'Search engines read `title` and `description`. Social networks read **Open Graph** tags, which set the title and image when you share a link.'),
            C('<title>Fucina: impara il web giocando</title>
<meta name="description" content="Corsi a livelli di HTML e CSS.">
<meta property="og:title" content="Fucina">
<meta property="og:image" content="https://esempio.it/og.png">
<link rel="canonical" href="https://esempio.it/">', 'html'),
            P('`canonical` indica l’indirizzo ufficiale di una pagina raggiungibile da più URL, così i motori non la considerano un doppione.', '`canonical` gives the official address of a page reachable from several URLs, so engines do not treat it as a duplicate.'),
        ], [
            Q('o1', L('Che cosa decide `og:image`?', 'What does `og:image` decide?'), [L('L’immagine quando condividi', 'The image when you share'), L('Il logo del browser', 'The browser logo'), L('La favicon', 'The favicon'), L('Lo sfondo', 'The background')], 0, L('`og` sta per Open Graph.', '`og` stands for Open Graph.')),
            Q('o2', L('A che cosa serve `rel="canonical"`?', 'What is `rel="canonical"` for?'), [L('Indica l’indirizzo ufficiale', 'It gives the official address'), L('Blocca i motori', 'It blocks engines'), L('Apre una scheda', 'It opens a tab'), L('Carica il CSS', 'It loads CSS')], 0, L('Evita che più URL contino come pagine doppie.', 'It stops several URLs counting as duplicate pages.')),
            Q('o3', L('Dove vanno questi tag?', 'Where do these tags go?'), ['<head>', '<body>', '<footer>', '<main>'], 0, L('Sono metadati: stanno in `head`.', 'They are metadata: they live in `head`.')),
        ], task('html', L('Aggiungi nel `head` un `meta` con `name="description"`.', 'Add to the `head` a `meta` with `name="description"`.'), "<head>\n  <title>Fucina</title>\n</head>", [re('<meta[^>]+name="description"', L('C’è la description', 'The description is there'))], '', "<head>\n  <title>Fucina</title>\n  <meta name=\"description\" content=\"Impara\">\n</head>"), [
            dMatch(pMatch(), [['title', L('Il titolo nei risultati', 'The title in results')], ['description', L('Il riassunto nei risultati', 'The summary in results')], ['og:image', L('L’anteprima sui social', 'The preview on social media')], ['canonical', L('L’indirizzo ufficiale', 'The official address')]], L('Pochi tag decidono come ti vedono motori e reti sociali.', 'A few tags decide how engines and social networks see you.')),
            dBug(pBug(), ['<head>', '  <title>Fucina</title>', '  <meta name="description" value="Impara">', '</head>'], 2, '  <meta name="description" content="Impara">', L('I `meta` usano `content`, non `value`.', '`meta` tags use `content`, not `value`.')),
        ]),
    ],
    'css-base' => [
        lesson('backgrounds', L('Sfondi e ombre', 'Backgrounds and shadows'), [
            P('`background` può essere un colore, un’immagine o una sfumatura. `box-shadow` aggiunge un’ombra alla scatola, `text-shadow` al testo.', '`background` can be a colour, an image or a gradient. `box-shadow` adds a shadow to the box, `text-shadow` to the text.'),
            C('.card {
  background: linear-gradient(135deg, #FF5B2E, #FFC53D);
  box-shadow: 0 4px 12px rgb(0 0 0 / .25);
}', 'css'),
            P('I valori di `box-shadow` sono: spostamento orizzontale, verticale, sfocatura e colore.', 'The values of `box-shadow` are: horizontal offset, vertical offset, blur and colour.'),
        ], [
            Q('g1', L('Quale funzione crea una sfumatura?', 'Which function creates a gradient?'), ['linear-gradient()', 'fade()', 'blend()', 'mix()'], 0, L('Esiste anche `radial-gradient()`.', 'There is also `radial-gradient()`.')),
            Q('g2', L('Che cosa fa il terzo valore di `box-shadow: 0 4px 12px`?', 'What does the third value of `box-shadow: 0 4px 12px` do?'), [L('La sfocatura', 'The blur'), L('Il colore', 'The colour'), L('Il raggio', 'The radius'), L('L’opacità', 'The opacity')], 0, L('Più è alto, più l’ombra è morbida.', 'The higher it is, the softer the shadow.')),
            Q('g3', L('Quale proprietà mette un’ombra al testo?', 'Which property puts a shadow on text?'), ['text-shadow', 'font-shadow', 'shadow', 'box-shadow'], 0, L('`box-shadow` vale per la scatola.', '`box-shadow` is for the box.')),
        ], task('css', L('Dai a `#box` lo sfondo `rgb(255, 197, 61)` e angoli da `16px`.', 'Give `#box` the background `rgb(255, 197, 61)` and `16px` corners.'), "#box {\n\n}", [css('#box', 'background-color', 'rgb(255, 197, 61)', L('Lo sfondo è giusto', 'The background is right')), css('#box', 'border-top-left-radius', '16px', L('Gli angoli sono arrotondati', 'The corners are rounded'))], $page, "#box {\n  background: rgb(255, 197, 61);\n  border-radius: 16px;\n}"), [
            dBlanks(pFill(), ".card {\n  background: ___-gradient(90deg, red, gold);\n  ___: 0 2px 8px gray;\n}", ['linear', 'box-shadow'], ['radial-shadow', 'shadow', 'line'], L('Sfumatura lineare e ombra della scatola.', 'A linear gradient and a box shadow.')),
            dFlash(pFlash(), [[L('Un `background` può essere un’immagine', 'A `background` can be an image'), true], [L('`box-shadow` cambia lo spazio occupato', '`box-shadow` changes the space taken up'), false], [L('Una sfumatura può avere più di due colori', 'A gradient can have more than two colours'), true], [L('`text-shadow` vale per le immagini', '`text-shadow` works on images'), false]], L('Le ombre si disegnano sopra: non spostano nulla.', 'Shadows are painted on top: they move nothing.')),
        ]),
        lesson('units', L('Unità di misura', 'Units'), [
            P('`px` è fisso, `%` dipende dal contenitore, `em` dalla dimensione del testo dell’elemento, `rem` da quella della radice, `vw` e `vh` dallo schermo.', '`px` is fixed, `%` depends on the container, `em` on the element’s text size, `rem` on the root’s, `vw` and `vh` on the screen.'),
            C('.box {
  width: 50%;       /* metà del contenitore */
  padding: 1.5rem;  /* 24px con base 16px */
  height: 100vh;    /* tutta l’altezza dello schermo */
}', 'css'),
            Tip('Per i testi preferisci `rem`: rispetta le impostazioni di chi legge.', 'For text prefer `rem`: it respects the reader’s settings.'),
        ], [
            Q('u1', L('`50%` di che cosa?', '`50%` of what?'), [L('Del contenitore', 'Of the container'), L('Dello schermo, sempre', 'Of the screen, always'), L('Del testo', 'Of the text'), L('Del file', 'Of the file')], 0, L('Le percentuali guardano al genitore.', 'Percentages look at the parent.')),
            Q('u2', L('Quanto vale `100vh`?', 'What is `100vh`?'), [L('Tutta l’altezza dello schermo', 'The full height of the screen'), L('100 pixel', '100 pixels'), L('La larghezza', 'The width'), L('Il doppio del testo', 'Twice the text')], 0, L('`vh` è l’1% dell’altezza della finestra.', '`vh` is 1% of the window height.')),
            Q('u3', L('Da che cosa dipende `rem`?', 'What does `rem` depend on?'), [L('Dalla radice del documento', 'On the root of the document'), L('Dal genitore', 'On the parent'), L('Dallo schermo', 'On the screen'), L('Da nulla', 'On nothing')], 0, L('`r` sta per root.', '`r` stands for root.')),
        ], task('css', L('Imposta `#box` a una larghezza di `50%` e un `padding` di `1rem`.', 'Set `#box` to a width of `50%` and a `padding` of `1rem`.'), "#box {\n\n}", [re('width\s*:\s*50%', L('La larghezza è 50%', 'The width is 50%')), re('padding\s*:\s*1rem', L('Il padding è 1rem', 'The padding is 1rem'))], $page, "#box {\n  width: 50%;\n  padding: 1rem;\n}"), [
            dMatch(pMatch(), [['px', L('Fisso', 'Fixed')], ['%', L('Rispetto al contenitore', 'Relative to the container')], ['rem', L('Rispetto alla radice', 'Relative to the root')], ['vw', L('Rispetto allo schermo', 'Relative to the screen')], ['em', L('Rispetto al testo dell’elemento', 'Relative to the element’s text')]], L('Scegli l’unità in base a che cosa deve seguire la misura.', 'Pick the unit by what the size should follow.')),
            dFlash(pFlash(), [[L('`1rem` di solito vale 16px', '`1rem` is usually 16px'), true], [L('`100vw` è tutta la larghezza dello schermo', '`100vw` is the full width of the screen'), true], [L('`px` si adatta allo schermo', '`px` adapts to the screen'), false], [L('`%` di un’altezza funziona sempre', '`%` of a height always works'), false]], L('Una percentuale d’altezza serve un genitore con altezza definita.', 'A height percentage needs a parent with a defined height.')),
        ]),
    ],
    'css-advanced' => [
        lesson('pseudo', L('Pseudo-elementi', 'Pseudo-elements'), [
            P('`::before` e `::after` creano un elemento finto dentro quello scelto, senza toccare l’HTML. Hanno sempre bisogno di `content`.', '`::before` and `::after` create a fake element inside the chosen one, without touching the HTML. They always need `content`.'),
            C('.nuovo::before {
  content: "★ ";
  color: gold;
}
li:nth-child(odd) { background: #eee; }
p:first-child { font-weight: 700; }', 'css'),
            P('Le pseudo-classi strutturali come `:nth-child()` scelgono gli elementi in base alla posizione: `odd`, `even` o una formula.', 'Structural pseudo-classes like `:nth-child()` pick elements by position: `odd`, `even` or a formula.'),
        ], [
            Q('p1', L('Che cosa serve sempre a `::before`?', 'What does `::before` always need?'), ['content', 'display', 'width', 'color'], 0, L('Senza `content` non viene creato nulla.', 'Without `content` nothing is created.')),
            Q('p2', L('Quali elementi sceglie `li:nth-child(even)`?', 'Which elements does `li:nth-child(even)` pick?'), [L('I pari', 'The even ones'), L('I dispari', 'The odd ones'), L('Il primo', 'The first'), L('Tutti', 'All of them')], 0, L('`even` è pari, `odd` dispari.', '`even` is even, `odd` is odd.')),
            Q('p3', L('Come si scrive uno pseudo-elemento?', 'How is a pseudo-element written?'), [L('Con `::`', 'With `::`'), L('Con `#`', 'With `#`'), L('Con `$`', 'With `$`'), L('Con `@`', 'With `@`')], 0, L('Due punti doppi lo distinguono da una pseudo-classe.', 'The double colon sets it apart from a pseudo-class.')),
        ], task('css', L('Aggiungi prima di `.nota` il testo `"→ "` con `::before`.', 'Add the text `"→ "` before `.nota` using `::before`.'), '', [re('\.nota::before', L('Usi `.nota::before`', 'You use `.nota::before`')), re('content\s*:', L('C’è `content`', '`content` is there'))], $page, ".nota::before {\n  content: \"→ \";\n}"), [
            dBug(pBug(), ['.nuovo::after {', '  testo: "!";', '  color: gold;', '}'], 1, '  content: "!";', L('La proprietà si chiama `content`: senza, lo pseudo-elemento non esiste.', 'The property is called `content`: without it the pseudo-element does not exist.')),
            dMatch(pMatch(), [[':first-child', L('Il primo figlio', 'The first child')], [':last-child', L('L’ultimo figlio', 'The last child')], [':nth-child(3)', L('Il terzo figlio', 'The third child')], [':nth-child(odd)', L('I dispari', 'The odd ones')], ['::after', L('Un elemento finto alla fine', 'A fake element at the end')]], L('Selezioni precise, senza classi in più.', 'Precise selection, with no extra classes.')),
        ]),
        lesson('modern', L('CSS moderno', 'Modern CSS'), [
            P('Il CSS di oggi sa annidare le regole, scegliere un genitore con `:has()` e adattare un componente al suo contenitore con le **container query**.', 'Today’s CSS can nest rules, pick a parent with `:has()` and adapt a component to its container with **container queries**.'),
            C('.card {
  container-type: inline-size;
  &:hover { outline: 2px solid; }
}
.card:has(img) { padding: 0; }
@container (min-width: 400px) {
  .titolo { font-size: 2rem; }
}', 'css'),
            P('`:has()` è il «selettore genitore» atteso per anni: sceglie un elemento in base a ciò che contiene.', '`:has()` is the long-awaited “parent selector”: it picks an element by what it contains.'),
        ], [
            Q('m1', L('Che cosa sceglie `.card:has(img)`?', 'What does `.card:has(img)` pick?'), [L('Le card con un’immagine', 'Cards with an image'), L('Le immagini', 'The images'), L('Tutte le card', 'All cards'), L('Nulla', 'Nothing')], 0, L('Si guarda dentro, ma viene scelto il genitore.', 'It looks inside, but the parent is picked.')),
            Q('m2', L('A che cosa guarda `@container`?', 'What does `@container` look at?'), [L('Al contenitore', 'The container'), L('Allo schermo', 'The screen'), L('Al font', 'The font'), L('Al server', 'The server')], 0, L('Un componente si adatta allo spazio che ha davvero.', 'A component adapts to the space it really has.')),
            Q('m3', L('Il CSS moderno può annidare le regole?', 'Can modern CSS nest rules?'), [L('Sì', 'Yes'), L('No, solo SCSS', 'No, only SCSS'), L('Solo in `@media`', 'Only in `@media`'), L('Solo con JavaScript', 'Only with JavaScript')], 0, L('Il nesting ora è nel CSS nativo, con `&`.', 'Nesting is now in native CSS, with `&`.')),
        ], task('css', L('Con `:has()`, scrivi la regola `.card:has(img)` con `padding: 0`.', 'Using `:has()`, write the rule `.card:has(img)` with `padding: 0`.'), '', [re('\.card:has\(img\)', L('Usi `:has(img)`', 'You use `:has(img)`')), re('padding\s*:\s*0', L('Il padding è 0', 'The padding is 0'))], $page, ".card:has(img) {\n  padding: 0;\n}"), [
            dBlanks(pFill(), ".card___(img) { padding: 0; }\n.card {\n  ___:hover { color: red; }\n}\n___ (min-width: 400px) { … }", [':has', '&', '@container'], [':with', '@media-box', '#'], L('Tre novità che fanno risparmiare molto JavaScript.', 'Three features that save a lot of JavaScript.')),
            dFlash(pFlash(), [[L('`:has()` può scegliere un genitore', '`:has()` can pick a parent'), true], [L('Le container query guardano allo schermo', 'Container queries look at the screen'), false], [L('`&` funziona nel CSS nativo', '`&` works in native CSS'), true], [L('Serve un compilatore per `:has()`', '`:has()` needs a compiler'), false]], L('Tutto questo funziona nel browser, senza strumenti.', 'All of this works in the browser, with no tools.')),
        ]),
    ],
    'scss' => [
        lesson('functions', L('Funzioni e operatori', 'Functions and operators'), [
            P('SCSS sa fare i conti e offre funzioni per i colori. Con `@function` scrivi le tue: restituiscono un valore con `@return`.', 'SCSS can do maths and has colour functions. With `@function` you write your own: they give back a value with `@return`.'),
            C('@use "sass:color";
@use "sass:math";

@function spazio($n) {
  @return $n * 8px;
}
.card {
  padding: spazio(2);
  width: math.div(100%, 3);
  background: color.adjust(#FF5B2E, $lightness: 20%);
}', 'scss'),
            P('Un mixin scrive regole, una funzione restituisce un valore da usare in una proprietà.', 'A mixin writes rules, a function gives back a value to use in a property.'),
        ], [
            Q('f1', L('Che cosa restituisce una `@function`?', 'What does an `@function` give back?'), [L('Un valore', 'A value'), L('Delle regole', 'Rules'), L('Un file', 'A file'), L('Niente', 'Nothing')], 0, L('Le regole sono il lavoro dei mixin.', 'Rules are the job of mixins.')),
            Q('f2', L('Quale parola chiude una funzione?', 'Which word ends a function?'), ['@return', '@end', '@give', '@out'], 0, L('`@return` indica il risultato.', '`@return` gives the result.')),
            Q('f3', L('Quanto vale `spazio(2)` nell’esempio?', 'What is `spazio(2)` in the example?'), ['16px', '8px', '2px', '10px'], 0, L('2 × 8px.', '2 × 8px.')),
        ], task('scss', L('Scrivi una funzione `doppio($n)` che restituisce `$n * 2`.', 'Write a function `doppio($n)` that gives back `$n * 2`.'), '', [re('@function\s+doppio\(\$n\)', L('Definisci `doppio`', 'You define `doppio`')), re('@return\s+\$n\s*\*\s*2', L('Restituisci il doppio', 'You give back the double'))], '', "@function doppio(\$n) {\n  @return \$n * 2;\n}"), [
            dBlanks(pFill(), "___ spazio(\$n) {\n  ___ \$n * 8px;\n}", ['@function', '@return'], ['@mixin', '@include'], L('Funzione e valore restituito.', 'Function and returned value.')),
            dMatch(pMatch(), [['@mixin', L('Scrive regole', 'Writes rules')], ['@function', L('Calcola un valore', 'Works out a value')], ['math.div()', L('Fa una divisione', 'Divides')], ['color.adjust()', L('Modifica un colore', 'Changes a colour')]], L('Scegli lo strumento in base a che cosa deve produrre.', 'Choose the tool by what it must produce.')),
        ]),
        lesson('architecture', L('Organizzare i file', 'Organising the files'), [
            P('In un progetto grande lo stile si divide in **file parziali**, che iniziano con `_`, e un file principale li raccoglie con `@use`.', 'In a large project style is split into **partial files**, starting with `_`, and a main file gathers them with `@use`.'),
            C('// main.scss
@use "base/reset";
@use "base/variabili";
@use "componenti/bottoni";
@use "pagine/home";', 'scss'),
            P('I file parziali non diventano CSS da soli: finiscono tutti nel file compilato. Così il browser scarica un solo foglio di stile.', 'Partial files do not become CSS on their own: they all end up in the compiled file. So the browser downloads one stylesheet.'),
        ], [
            Q('a1', L('Con che cosa inizia un file parziale?', 'What does a partial file start with?'), ['_', '-', '@', '$'], 0, L('Il trattino basso dice al compilatore di non crearne un `.css`.', 'The underscore tells the compiler not to create a `.css` for it.')),
            Q('a2', L('Nel `@use` si scrive il `_`?', 'Do you write the `_` in `@use`?'), [L('No', 'No'), L('Sì, sempre', 'Yes, always'), L('Solo in Sass', 'Only in Sass'), L('Solo due volte', 'Only twice')], 0, L('Basta il nome: `@use "bottoni"`.', 'The name is enough: `@use "bottoni"`.')),
            Q('a3', L('Quanti CSS produce di solito il progetto?', 'How many CSS files does the project usually produce?'), [L('Uno', 'One'), L('Uno per parziale', 'One per partial'), L('Nessuno', 'None'), L('Due', 'Two')], 0, L('Un solo file è più veloce da scaricare.', 'A single file is quicker to download.')),
        ], task('scss', L('In `main.scss`, importa con `@use` i parziali `reset` e `bottoni`.', 'In `main.scss`, load the partials `reset` and `bottoni` with `@use`.'), '// main.scss
', [re('@use\s+"reset"', L('Carichi `reset`', 'You load `reset`')), re('@use\s+"bottoni"', L('Carichi `bottoni`', 'You load `bottoni`'))], '', "@use \"reset\";\n@use \"bottoni\";"), [
            dOrder(pSort(), ['@use "variabili";', '@use "reset";', '@use "componenti";', '@use "pagine";'], L('Dal generale allo specifico: variabili, base, componenti, pagine.', 'From general to specific: variables, base, components, pages.')),
            dFlash(pFlash(), [[L('`_bottoni.scss` è un parziale', '`_bottoni.scss` is a partial'), true], [L('Ogni parziale diventa un `.css`', 'Every partial becomes a `.css`'), false], [L('`@use` si scrive in cima al file', '`@use` goes at the top of the file'), true], [L('Dividere i file rallenta il sito', 'Splitting files slows the site down'), false]], L('Tanti file per chi scrive, uno solo per chi visita.', 'Many files for the author, one for the visitor.')),
        ]),
    ],
    'sass' => [
        lesson('maps-sass', L('Variabili e mappe in Sass', 'Variables and maps in Sass'), [
            P('Nella sintassi indentata variabili e mappe funzionano come in SCSS: cambiano solo graffe e punti e virgola.', 'In the indented syntax variables and maps work as in SCSS: only braces and semicolons change.'),
            C('$temi: (ok: green, ko: red)

@each $nome, $tinta in $temi
  .avviso-#{$nome}
    color: $tinta', 'sass'),
            P('Una mappa si scrive su una riga sola: nella sintassi indentata andare a capo ha un significato.', 'A map is written on one line: in the indented syntax a line break has a meaning.'),
        ], [
            Q('v1', L('Come inizia una variabile Sass?', 'How does a Sass variable start?'), ['$', '@', '--', '#'], 0, L('Identica a SCSS.', 'Same as SCSS.')),
            Q('v2', L('`@each` funziona in Sass?', 'Does `@each` work in Sass?'), [L('Sì', 'Yes'), L('Solo in SCSS', 'Only in SCSS'), L('No', 'No'), L('Solo in CSS', 'Only in CSS')], 0, L('Le funzioni sono le stesse.', 'The features are the same.')),
            Q('v3', L('Che cosa segna il blocco di `@each`?', 'What marks the `@each` block?'), [L('Il rientro', 'The indent'), L('Le graffe', 'The braces'), L('Il punto e virgola', 'The semicolon'), L('Le parentesi', 'The brackets')], 0, L('In Sass il rientro sostituisce le graffe.', 'In Sass indentation replaces braces.')),
        ], task('sass', L('Definisci `$brand: orange` e usalo come `color` di `.titolo`.', 'Define `$brand: orange` and use it as the `color` of `.titolo`.'), '', [re('^\$brand:\s*orange', L('Definisci `$brand`', 'You define `$brand`')), re('^\s+color:\s*\$brand\s*$', L('Lo usi, senza punto e virgola', 'You use it, with no semicolon'))], '', "\$brand: orange\n.titolo\n  color: \$brand"), [
            dBug(pBug(), ['$colore: red', '.card {', '  color: $colore', '}'], 1, '.card', L('In Sass non ci sono graffe: basta il rientro.', 'There are no braces in Sass: the indent is enough.')),
            dOrder(pSort(), ['$temi: (ok: green)', '@each $nome, $tinta in $temi', '  .avviso-#{$nome}', '    color: $tinta'], L('Prima i dati, poi il ciclo, poi quello che genera.', 'First the data, then the loop, then what it generates.')),
        ]),
        lesson('convert', L('Da SCSS a Sass', 'From SCSS to Sass'), [
            P('Passare da una sintassi all’altra è meccanico: togli graffe e punti e virgola, usa `=` al posto di `@mixin` e `+` al posto di `@include` se vuoi la forma breve.', 'Moving between syntaxes is mechanical: remove braces and semicolons, use `=` for `@mixin` and `+` for `@include` if you want the short form.'),
            C('// SCSS
.card { color: red; .titolo { margin: 0; } }

// Sass
.card
  color: red
  .titolo
    margin: 0', 'sass'),
            P('Il comando `sass-convert` lo faceva da solo in passato: oggi conviene scegliere una sintassi e restare coerenti.', 'The `sass-convert` command used to do it for you: today it is best to pick one syntax and stay consistent.'),
        ], [
            Q('c1', L('Che cosa sparisce passando a Sass?', 'What disappears when moving to Sass?'), [L('Graffe e punti e virgola', 'Braces and semicolons'), L('Le variabili', 'Variables'), L('I mixin', 'Mixins'), L('I selettori', 'Selectors')], 0, L('Il resto non cambia.', 'The rest does not change.')),
            Q('c2', L('Che cos’è `=` in Sass?', 'What is `=` in Sass?'), [L('`@mixin`', '`@mixin`'), L('`@include`', '`@include`'), L('`@use`', '`@use`'), L('`@if`', '`@if`')], 0, L('`+` invece è `@include`.', '`+` is `@include` instead.')),
            Q('c3', L('Quale abitudine conviene?', 'Which habit is best?'), [L('Usare una sola sintassi', 'Using one syntax'), L('Alternarle', 'Alternating them'), L('Cambiare ogni file', 'Changing every file'), L('Usarne tre', 'Using three')], 0, L('La coerenza rende il codice più facile da leggere.', 'Consistency makes code easier to read.')),
        ], task('sass', L('Riscrivi in Sass: `.box { color: red; }`', 'Rewrite in Sass: `.box { color: red; }`'), '', [re('^\.box\s*$', L('Il selettore è senza graffa', 'The selector has no brace')), re('^\s+color:\s*red\s*$', L('La proprietà è rientrata e senza `;`', 'The property is indented with no `;`'))], '', ".box\n  color: red"), [
            dMatch(L('Abbina la forma SCSS a quella Sass.', 'Match the SCSS form with the Sass form.'), [['@mixin x', '=x'], ['@include x', '+x'], ['.a { }', '.a'], ['color: red;', 'color: red']], L('Stesso significato, meno simboli.', 'Same meaning, fewer symbols.')),
            dFlash(pFlash(), [[L('Lo stesso CSS si può ottenere da SCSS o Sass', 'The same CSS can come from SCSS or Sass'), true], [L('In Sass i rientri sono facoltativi', 'In Sass indents are optional'), false], [L('`+x` usa un mixin', '`+x` uses a mixin'), true], [L('In Sass ogni riga finisce con `}`', 'In Sass every line ends with `}`'), false]], L('Le due sintassi portano allo stesso risultato.', 'Both syntaxes lead to the same result.')),
        ]),
    ],
];
