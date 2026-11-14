<?php
/** HTML intermediate: the step between the first tags and semantic pages. */

return [
    'id' => 'html-intermediate', 'family' => 'html', 'after' => 'html-base',
    'title' => L('HTML intermedio', 'Intermediate HTML'), 'short' => L('HTML intermedio', 'HTML Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Attributi globali, link interni, testo ricco, iframe e moduli più completi.', 'Global attributes, internal links, rich text, iframes and fuller forms.'),
    'lessons' => [
        lesson('attributes', L('Attributi globali', 'Global attributes'), [
            P('Alcuni attributi valgono su quasi tutti i tag: `id` identifica un elemento, `class` lo raggruppa, `title` aggiunge un suggerimento, `hidden` lo nasconde, `lang` ne indica la lingua.', 'Some attributes work on almost every tag: `id` identifies an element, `class` groups it, `title` adds a tooltip, `hidden` hides it, `lang` states its language.'),
            C('<p id="intro" class="nota grande" lang="en" title="Una nota">Hello!</p>
<div hidden>Non mi vedi</div>', 'html'),
            P('Un elemento può avere più classi separate da spazi, ma un solo `id`, unico in tutta la pagina.', 'An element can have several classes separated by spaces, but only one `id`, unique in the whole page.'),
        ], [
            Q('a1', L('Quante classi può avere un elemento?', 'How many classes can an element have?'), [L('Quante vuoi', 'As many as you like'), L('Una', 'One'), L('Due', 'Two'), L('Nessuna', 'None')], 0, L('Si separano con uno spazio.', 'They are separated by a space.')),
            Q('a2', L('Quale attributo nasconde un elemento?', 'Which attribute hides an element?'), ['hidden', 'none', 'invisible', 'off'], 0, L('`hidden` non ha bisogno di un valore.', '`hidden` needs no value.')),
            Q('a3', L('Due elementi possono avere lo stesso `id`?', 'Can two elements share the same `id`?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo i `div`', 'Only `div`s'), L('Solo in `<body>`', 'Only in `<body>`')], 0, L('Un `id` è unico nella pagina.', 'An `id` is unique in the page.')),
        ], task('html', L('Crea un paragrafo con `id="intro"` e le classi `nota` e `grande`.', 'Create a paragraph with `id="intro"` and the classes `nota` and `grande`.'), '', [has('p#intro', L('Il paragrafo ha l’id', 'The paragraph has the id')), has('p.nota.grande', L('Ha entrambe le classi', 'It has both classes'))], '', '<p id="intro" class="nota grande">Ciao</p>'), [
            dMatch(pMatch(), [['id', L('Nome unico', 'Unique name')], ['class', L('Gruppo di elementi', 'Group of elements')], ['title', L('Suggerimento al passaggio', 'Tooltip on hover')], ['hidden', L('Nasconde l’elemento', 'Hides the element')], ['lang', L('Lingua del contenuto', 'Language of the content')]], L('Gli attributi globali funzionano quasi ovunque.', 'Global attributes work almost everywhere.')),
            dBug(pBug(), ['<p class="nota" class="grande">Ciao</p>', '<p class="nota grande">Mondo</p>'], 0, '<p class="nota grande">Ciao</p>', L('Un attributo si scrive una volta sola: le classi si mettono insieme.', 'An attribute is written once only: classes go together.')),
        ]),
        lesson('anchors', L('Link interni e percorsi', 'Internal links and paths'), [
            P('Un link può portare a un punto della stessa pagina: `href="#contatti"` salta all’elemento con `id="contatti"`. I percorsi **relativi** partono dalla pagina attuale, quelli **assoluti** includono tutto l’indirizzo.', 'A link can lead to a spot in the same page: `href="#contatti"` jumps to the element with `id="contatti"`. **Relative** paths start from the current page, **absolute** ones include the whole address.'),
            C('<a href="#contatti">Vai ai contatti</a>
<a href="chi-siamo.html">Stessa cartella</a>
<a href="../index.html">Cartella sopra</a>
<a href="https://esempio.it/">Altro sito</a>
<a href="mailto:ciao@esempio.it">Scrivimi</a>', 'html'),
            P('`mailto:` apre l’email e `tel:` il telefono. `../` sale di una cartella.', '`mailto:` opens email and `tel:` the phone. `../` goes up one folder.'),
        ], [
            Q('h1', L('Che cosa fa `href="#menu"`?', 'What does `href="#menu"` do?'), [L('Salta all’elemento con id menu', 'Jumps to the element with id menu'), L('Apre menu.html', 'Opens menu.html'), L('Cerca su Google', 'Searches Google'), L('Nulla', 'Nothing')], 0, L('Il cancelletto indica un punto della pagina.', 'The hash marks a spot in the page.')),
            Q('h2', L('Che cosa significa `../`?', 'What does `../` mean?'), [L('La cartella sopra', 'The folder above'), L('La cartella sotto', 'The folder below'), L('La home', 'The home page'), L('Il server', 'The server')], 0, L('Due punti risalgono di un livello.', 'Two dots go up one level.')),
            Q('h3', L('Quale link apre il telefono?', 'Which link opens the phone?'), ['tel:', 'phone:', 'call:', 'sms-to:'], 0, L('Sui telefoni avvia la chiamata.', 'On phones it starts the call.')),
        ], task('html', L('Crea un link che salta a `#fine` e un `div` con `id="fine"`.', 'Create a link that jumps to `#fine` and a `div` with `id="fine"`.'), '', [has('a[href="#fine"]', L('Il link punta a #fine', 'The link points to #fine')), has('div#fine', L('Il `div` ha l’id', 'The `div` has the id'))], '', "<a href=\"#fine\">Vai</a>\n<div id=\"fine\">Fine</div>"), [
            dMatch(pMatch(), [['#fine', L('Un punto della pagina', 'A spot in the page')], ['pagina.html', L('Un file vicino', 'A nearby file')], ['../pagina.html', L('Un file una cartella sopra', 'A file one folder up')], ['https://…', L('Un altro sito', 'Another site')], ['mailto:', L('Una email', 'An email')]], L('Il prefisso dice al browser dove andare.', 'The prefix tells the browser where to go.')),
            dBlanks(pFill(), '<a href="___contatti">Contatti</a>' . "\n" . '<section ___="contatti">…</section>', ['#', 'id'], ['.', 'class', 'name'], L('Il cancelletto nel link, l’`id` nella destinazione.', 'The hash in the link, the `id` at the destination.')),
        ]),
        lesson('rich-text', L('Testo ricco', 'Rich text'), [
            P('Oltre a `strong` ed `em` esistono tag per ogni sfumatura del testo: `blockquote` per le citazioni, `code` per il codice, `pre` per il testo preformattato, `abbr` per le sigle, `mark` per evidenziare.', 'Besides `strong` and `em` there are tags for every shade of text: `blockquote` for quotes, `code` for code, `pre` for preformatted text, `abbr` for abbreviations, `mark` for highlights.'),
            C('<blockquote>Il codice è poesia.</blockquote>
<p>Usa <code>console.log</code> per il <abbr title="debug">dbg</abbr>.</p>
<p>La parola <mark>chiave</mark> è qui.</p>
<pre>  rispetta
    gli spazi</pre>', 'html'),
            P('`pre` mantiene spazi e a capo così come li scrivi. Tutti gli altri tag li riducono a un solo spazio.', '`pre` keeps spaces and line breaks exactly as written. All other tags reduce them to a single space.'),
        ], [
            Q('r1', L('Quale tag è per una citazione lunga?', 'Which tag is for a long quote?'), ['<blockquote>', '<quote>', '<cite>', '<q-long>'], 0, L('`<q>` esiste per quelle brevi nel testo.', '`<q>` exists for short ones in the text.')),
            Q('r2', L('Che cosa fa `<pre>`?', 'What does `<pre>` do?'), [L('Mantiene spazi e a capo', 'Keeps spaces and line breaks'), L('Prepara un form', 'Prepares a form'), L('Ingrandisce', 'Enlarges'), L('Precarica', 'Preloads')], 0, L('È perfetto per mostrare codice.', 'It is perfect for showing code.')),
            Q('r3', L('Quale tag evidenzia un testo?', 'Which tag highlights text?'), ['<mark>', '<hl>', '<em>', '<b>'], 0, L('Di solito appare con sfondo giallo.', 'It usually shows with a yellow background.')),
        ], task('html', L('Scrivi un `blockquote` e un paragrafo con dentro una parola in `mark`.', 'Write a `blockquote` and a paragraph with a word in `mark`.'), '', [has('blockquote', L('C’è la citazione', 'The quote is there')), has('p mark', L('C’è la parola evidenziata', 'The highlighted word is there'))], '', "<blockquote>Forgia!</blockquote>\n<p>Una <mark>parola</mark>.</p>"), [
            dMatch(pMatch(), [['<blockquote>', L('Citazione', 'Quote')], ['<code>', L('Pezzo di codice', 'Piece of code')], ['<pre>', L('Testo preformattato', 'Preformatted text')], ['<abbr>', L('Abbreviazione', 'Abbreviation')], ['<mark>', L('Evidenziatore', 'Highlighter')]], L('Il tag giusto dà significato al testo.', 'The right tag gives the text meaning.')),
            dFlash(pFlash(), [[L('In HTML dieci spazi di fila si vedono come uno', 'In HTML ten spaces in a row show as one'), true], [L('`<pre>` usa di solito un carattere monospaziato', '`<pre>` usually uses a monospaced font'), true], [L('`<abbr>` usa `title` per il significato', '`<abbr>` uses `title` for the meaning'), true], [L('`<mark>` crea un link', '`<mark>` creates a link'), false], [L('`<code>` esegue il codice', '`<code>` runs the code'), false]], L('Sono tag di significato: non eseguono nulla.', 'They are tags of meaning: they run nothing.')),
        ]),
        lesson('embed', L('Iframe e contenuti esterni', 'Iframes and embedded content'), [
            P('`iframe` inserisce un’altra pagina nella tua: mappe, video, widget. Dagli sempre un `title`, per chi usa un lettore di schermo.', '`iframe` puts another page inside yours: maps, videos, widgets. Always give it a `title`, for screen reader users.'),
            C('<iframe
  src="https://esempio.it/mappa"
  title="La nostra sede"
  width="400" height="300"
  loading="lazy"></iframe>', 'html'),
            P('Un `iframe` carica codice di altri: con `sandbox` limiti quello che può fare, e `loading="lazy"` evita di caricarlo finché non serve.', 'An `iframe` loads other people’s code: `sandbox` limits what it can do, and `loading="lazy"` avoids loading it until needed.'),
        ], [
            Q('i1', L('Che cosa fa un `iframe`?', 'What does an `iframe` do?'), [L('Mostra un’altra pagina', 'Shows another page'), L('Crea una cornice CSS', 'Creates a CSS frame'), L('Salva un file', 'Saves a file'), L('Cambia font', 'Changes font')], 0, L('È una finestra su un altro documento.', 'It is a window on another document.')),
            Q('i2', L('Quale attributo limita i suoi poteri?', 'Which attribute limits its powers?'), ['sandbox', 'limit', 'safe', 'guard'], 0, L('Senza permessi aggiunti blocca script e moduli.', 'With no permissions added it blocks scripts and forms.')),
            Q('i3', L('Perché serve `title` su un iframe?', 'Why does an iframe need `title`?'), [L('Per l’accessibilità', 'For accessibility'), L('Per la SEO del titolo', 'For title SEO'), L('Per il colore', 'For colour'), L('Non serve', 'It is not needed')], 0, L('Spiega che cosa contiene a chi non lo vede.', 'It explains what it holds to those who cannot see it.')),
        ], task('html', L('Inserisci un `iframe` con `src="mappa.html"`, un `title` e `loading="lazy"`.', 'Add an `iframe` with `src="mappa.html"`, a `title` and `loading="lazy"`.'), '', [has('iframe[src="mappa.html"]', L('Il `src` è giusto', 'The `src` is right')), has('iframe[title]', L('C’è il `title`', 'The `title` is there')), has('iframe[loading="lazy"]', L('Il caricamento è pigro', 'Loading is lazy'))], '', '<iframe src="mappa.html" title="Mappa" loading="lazy"></iframe>'), [
            dBlanks(pFill(), '<iframe ___="video.html" ___="Il video" loading="___"></iframe>', ['src', 'title', 'lazy'], ['href', 'alt', 'fast'], L('Sorgente, descrizione e caricamento pigro.', 'Source, description and lazy loading.')),
            dBug(pBug(), ['<h2>La mappa</h2>', '<iframe href="mappa.html" title="Mappa"></iframe>'], 1, '<iframe src="mappa.html" title="Mappa"></iframe>', L('Come le immagini, un `iframe` usa `src`.', 'Like images, an `iframe` uses `src`.')),
        ]),
        lesson('more-inputs', L('Campi per ogni esigenza', 'A field for every need'), [
            P('I campi moderni fanno molto da soli: `date` apre un calendario, `range` un cursore, `color` una tavolozza, `number` accetta solo cifre, `search` è fatto per le ricerche.', 'Modern fields do a lot by themselves: `date` opens a calendar, `range` a slider, `color` a palette, `number` takes digits only, `search` is made for searching.'),
            C('<input type="date" min="2026-01-01">
<input type="range" min="0" max="10" step="2">
<input type="color" value="#FF5B2E">
<select name="corso">
  <option value="html">HTML</option>
  <option value="css" selected>CSS</option>
</select>', 'html'),
            P('In `select`, `selected` indica l’opzione scelta all’inizio. `value` è ciò che riceve il server, il testo è ciò che vede l’utente.', 'In `select`, `selected` marks the option chosen at the start. `value` is what the server gets, the text is what the user sees.'),
        ], [
            Q('f1', L('Quale tipo mostra un cursore?', 'Which type shows a slider?'), ['range', 'slider', 'scroll', 'bar'], 0, L('Si usa con `min`, `max` e `step`.', 'It works with `min`, `max` and `step`.')),
            Q('f2', L('Che cosa riceve il server da una `option`?', 'What does the server get from an `option`?'), [L('Il `value`', 'The `value`'), L('Il testo', 'The text'), L('L’id', 'The id'), L('Nulla', 'Nothing')], 0, L('Il testo è solo per chi guarda.', 'The text is just for the viewer.')),
            Q('f3', L('Quale attributo sceglie in partenza una `option`?', 'Which attribute picks an `option` at the start?'), ['selected', 'checked', 'default', 'on'], 0, L('`checked` è per checkbox e radio.', '`checked` is for checkboxes and radios.')),
        ], task('html', L('Crea un campo `range` da `0` a `10` e un campo `date`.', 'Create a `range` field from `0` to `10` and a `date` field.'), '', [has('input[type="range"][min="0"][max="10"]', L('Il cursore va da 0 a 10', 'The slider goes from 0 to 10')), has('input[type="date"]', L('C’è la data', 'The date is there'))], '', "<input type=\"range\" min=\"0\" max=\"10\">\n<input type=\"date\">"), [
            dMatch(pMatch(), [['type="date"', L('Calendario', 'Calendar')], ['type="range"', L('Cursore', 'Slider')], ['type="color"', L('Tavolozza', 'Palette')], ['type="number"', L('Solo cifre', 'Digits only')], ['type="search"', L('Ricerca', 'Search')]], L('Il tipo giusto regala un controllo pronto.', 'The right type gives you a ready-made control.')),
            dOrder(pSort(), ['<select name="corso">', '  <option value="html">HTML</option>', '  <option value="css">CSS</option>', '</select>'], L('`select` avvolge le sue `option`.', '`select` wraps its `option`s.')),
        ]),
    ],
];
