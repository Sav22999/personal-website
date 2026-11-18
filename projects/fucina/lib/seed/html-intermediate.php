<?php
/** HTML intermediate: the step between the first tags and semantic pages (attributes, paths, rich text, iframes, inputs, tables, figures, sending forms). */

return [
    'id' => 'html-intermediate', 'family' => 'html', 'after' => 'html-base',
    'title' => L('HTML intermedio', 'Intermediate HTML'), 'short' => L('HTML intermedio', 'HTML Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Attributi globali, link interni, testo ricco, iframe, tabelle accessibili, figure e date, moduli più completi e come si inviano.', 'Global attributes, internal links, rich text, iframes, accessible tables, figures and dates, fuller forms and how they are sent.'),
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
        lesson('tables-plus', L('Tabelle chiare e accessibili', 'Clear, accessible tables'), [
            P('`<caption>` dà un titolo alla tabella, e lo screen reader lo legge per primo. `scope="col"` o `scope="row"` su un `<th>` dice se l’intestazione vale per la colonna o per la riga. `<tfoot>` raccoglie le righe dei totali.', '`<caption>` gives the table a title, and the screen reader reads it first. `scope="col"` or `scope="row"` on a `<th>` says whether the header applies to the column or to the row. `<tfoot>` holds the total rows.'),
            C('<table>
  <caption>Spese di marzo</caption>
  <thead>
    <tr><th scope="col">Voce</th><th scope="col">Euro</th></tr>
  </thead>
  <tbody>
    <tr><th scope="row">Affitto</th><td>600</td></tr>
    <tr><th scope="row">Spesa</th><td>250</td></tr>
  </tbody>
  <tfoot>
    <tr><th scope="row">Totale</th><td>850</td></tr>
  </tfoot>
</table>', 'html'),
            P('Un `<th>` può stare anche all’inizio di una riga: dà il nome a quella riga. Così chi ascolta la tabella sente «Affitto, Euro, 600» invece di un numero senza contesto.', 'A `<th>` can also start a row: it names that row. That way someone listening to the table hears “Affitto, Euro, 600” instead of a number without context.'),
        ], [
            Q('t1', L('Quale tag dà un titolo a una tabella?', 'Which tag gives a table a title?'), ['<title>', '<legend>', '<caption>', '<summary>'], 2, L('Va subito dopo `<table>`.', 'It goes right after `<table>`.')),
            Q('t2', L('Che cosa dice `scope="row"` su un `<th>`?', 'What does `scope="row"` say on a `<th>`?'), [L('Che l’intestazione vale per la colonna', 'That the header applies to the column'), L('Che l’intestazione vale per la sua riga', 'That the header applies to its row'), L('Che la riga è nascosta', 'That the row is hidden'), L('Che la cella occupa due righe', 'That the cell spans two rows')], 1, L('`col` per le colonne, `row` per le righe.', '`col` for columns, `row` for rows.')),
            Q('t3', L('Dove vanno le righe dei totali?', 'Where do the total rows go?'), ['<thead>', '<tbody>', '<caption>', '<tfoot>'], 3, L('`tfoot` è il piede della tabella.', '`tfoot` is the foot of the table.')),
        ], task('html', L('Aggiungi alla tabella un `caption` e metti `scope="col"` su entrambi i `th`.', 'Add a `caption` to the table and put `scope="col"` on both `th` cells.'), "<table>\n  <tr><th>Città</th><th>Abitanti</th></tr>\n  <tr><td>Udine</td><td>97 000</td></tr>\n</table>", [has('table > caption', L('La tabella ha un titolo', 'The table has a title')), has('th[scope="col"] + th[scope="col"]', L('Entrambe le intestazioni hanno `scope="col"`', 'Both headers have `scope="col"`'))], '', "<table>\n  <caption>Città del Friuli</caption>\n  <tr><th scope=\"col\">Città</th><th scope=\"col\">Abitanti</th></tr>\n  <tr><td>Udine</td><td>97 000</td></tr>\n</table>"), [
            dBlanks(pFill(), "<table>\n  <___>Voti</caption>\n  <tr><th ___=\"col\">Nome</th></tr>\n</table>", ['caption', 'scope'], ['title', 'legend', 'span'], L('Titolo della tabella e direzione dell’intestazione.', 'The table title and the header’s direction.')),
            dMatch(pMatch(), [['<caption>', L('Titolo della tabella', 'Title of the table')], ['<thead>', L('Righe di intestazione', 'Header rows')], ['<tbody>', L('Righe dei dati', 'Data rows')], ['<tfoot>', L('Righe dei totali', 'Total rows')], ['scope', L('Quali celle copre un’intestazione', 'Which cells a header covers')]], L('Una tabella ben fatta si capisce anche ascoltandola.', 'A well-made table makes sense even when listened to.')),
        ]),
        lesson('figure-time', L('Figure, date e liste di definizioni', 'Figures, dates and description lists'), [
            P('`<figure>` racchiude un’immagine, un grafico o un pezzo di codice insieme alla sua didascalia `<figcaption>`. `<time datetime="…">` scrive una data in un formato che i programmi capiscono, mentre il testo resta libero. `<dl>` è una lista di definizioni: `<dt>` il termine, `<dd>` la descrizione.', '`<figure>` wraps an image, a chart or a piece of code together with its caption `<figcaption>`. `<time datetime="…">` writes a date in a format programs understand, while the text stays free. `<dl>` is a description list: `<dt>` the term, `<dd>` the description.'),
            C('<figure>
  <img src="forgia.jpg" alt="Una forgia accesa">
  <figcaption>La forgia di bottega, 1920.</figcaption>
</figure>

<p>Corso aggiornato il <time datetime="2026-11-17">17 novembre</time>.</p>

<dl>
  <dt>HTML</dt>
  <dd>La struttura della pagina.</dd>
  <dt>CSS</dt>
  <dd>Il suo aspetto.</dd>
</dl>', 'html'),
            P('`<address>` contiene i contatti di chi ha scritto la pagina o l’articolo, non un indirizzo qualsiasi.', '`<address>` holds the contact details of whoever wrote the page or the article, not just any address.'),
        ], [
            Q('f1', L('Quale tag è la didascalia di una `<figure>`?', 'Which tag is the caption of a `<figure>`?'), ['<caption>', '<figcaption>', '<legend>', '<label>'], 1, L('`<caption>` è la didascalia delle tabelle.', '`<caption>` is the caption of tables.')),
            Q('f2', L('A che cosa serve `datetime` su `<time>`?', 'What is `datetime` on `<time>` for?'), [L('Mostra un orologio nella pagina', 'It shows a clock on the page'), L('Imposta il fuso orario della pagina', 'It sets the page’s time zone'), L('Mette la data in grassetto', 'It makes the date bold'), L('Dà la data in un formato che i programmi capiscono', 'It gives the date in a format programs understand')], 3, L('Il testo visibile può essere «ieri» o «17 novembre».', 'The visible text can be “yesterday” or “17 November”.')),
            Q('f3', L('In una `<dl>`, quale tag contiene il termine?', 'In a `<dl>`, which tag holds the term?'), ['<dd>', '<dl>', '<dt>', '<li>'], 2, L('`dt` è il termine, `dd` la sua descrizione.', '`dt` is the term, `dd` its description.')),
        ], task('html', L('Crea una `figure` con un’immagine (con `alt`) e una `figcaption`.', 'Create a `figure` with an image (with an `alt`) and a `figcaption`.'), '', [has('figure img[alt]:not([alt=""])', L('C’è l’immagine con `alt`', 'The image with an `alt` is there')), has('figure figcaption', L('C’è la didascalia', 'The caption is there'))], '', "<figure>\n  <img src=\"porto.jpg\" alt=\"Il porto al tramonto\">\n  <figcaption>Il porto di Trieste</figcaption>\n</figure>"), [
            dOrder(pSort(), ['<dl>', '  <dt>Forgia</dt>', '  <dd>Il laboratorio del fabbro.</dd>', '</dl>'], L('Prima il termine, poi la sua descrizione.', 'First the term, then its description.')),
            dFlash(pFlash(), [[L('`<figcaption>` va dentro `<figure>`', '`<figcaption>` goes inside `<figure>`'), true], [L('`<time>` cambia l’aspetto della data', '`<time>` changes how the date looks'), false], [L('`<dd>` descrive il `<dt>` che lo precede', '`<dd>` describes the `<dt>` before it'), true], [L('`<figure>` si usa solo per le foto', '`<figure>` is only for photos'), false]], L('Tag piccoli che danno un significato preciso.', 'Small tags that give a precise meaning.')),
        ]),
        lesson('form-send', L('Come si inviano i moduli', 'How forms are sent'), [
            P('`action` dice dove vanno i dati, `method` come. Con `get` finiscono nell’indirizzo (`/cerca?q=gatti`): va bene per le ricerche, che si possono salvare e condividere. Con `post` viaggiano nel corpo della richiesta: si usa per dati privati o che cambiano qualcosa.', '`action` says where the data goes, `method` how. With `get` it ends up in the address (`/cerca?q=gatti`): fine for searches, which can be bookmarked and shared. With `post` it travels in the body of the request: used for private data or data that changes something.'),
            C('<form action="/cerca" method="get">
  <label for="q">Cerca</label>
  <input id="q" name="q" type="search">
  <button>Vai</button>
</form>

<form action="/messaggio" method="post">
  <label for="testo">Messaggio</label>
  <textarea id="testo" name="testo" rows="4" maxlength="500"></textarea>
  <label for="tel">Telefono</label>
  <input id="tel" name="tel" type="tel" autocomplete="tel">
  <button>Invia</button>
</form>', 'html'),
            P('`<textarea>` è il campo per il testo su più righe. `autocomplete` aiuta il browser a compilare i dati che conosce già (nome, email, telefono). Un `<button>` dentro un modulo lo invia; con `type="button"` non invia nulla.', '`<textarea>` is the field for multi-line text. `autocomplete` helps the browser fill in data it already knows (name, email, phone). A `<button>` inside a form submits it; with `type="button"` it submits nothing.'),
        ], [
            Q('m1', L('Con quale `method` i dati finiscono nell’indirizzo?', 'With which `method` does the data end up in the address?'), ['post', 'put', 'get', 'send'], 2, L('Si vedono dopo il `?`.', 'You see it after the `?`.')),
            Q('m2', L('Quale `method` usi per un modulo con la password?', 'Which `method` do you use for a form with a password?'), ['get', 'post', 'url', 'link'], 1, L('Nell’indirizzo resterebbe nella cronologia.', 'In the address it would stay in the history.')),
            Q('m3', L('Che cosa fa un `<button>` senza `type` dentro un modulo?', 'What does a `<button>` with no `type` do inside a form?'), [L('Svuota il modulo', 'It clears the form'), L('Niente, finché non ha JavaScript', 'Nothing, until it has JavaScript'), L('Apre una nuova finestra', 'It opens a new window'), L('Invia il modulo', 'It submits the form')], 3, L('Il tipo predefinito è `submit`.', 'The default type is `submit`.')),
        ], task('html', L('Completa il modulo con `action="/messaggio"` e `method="post"`, e aggiungi una `textarea` con `name="testo"` e la sua `label`.', 'Complete the form with `action="/messaggio"` and `method="post"`, and add a `textarea` with `name="testo"` and its `label`.'), "<form>\n  <button>Invia</button>\n</form>", [has('form[action="/messaggio"][method="post"]', L('Il modulo va a /messaggio in POST', 'The form goes to /messaggio by POST')), has('form textarea[name="testo"]', L('C’è la `textarea`', 'The `textarea` is there')), has('form label[for]', L('C’è una `label` collegata', 'There is a linked `label`'))], '', "<form action=\"/messaggio\" method=\"post\">\n  <label for=\"testo\">Messaggio</label>\n  <textarea id=\"testo\" name=\"testo\"></textarea>\n  <button>Invia</button>\n</form>"), [
            dMatch(pMatch(), [['method="get"', L('Dati nell’indirizzo', 'Data in the address')], ['method="post"', L('Dati nel corpo della richiesta', 'Data in the request body')], ['action', L('Dove vanno i dati', 'Where the data goes')], ['<textarea>', L('Testo su più righe', 'Multi-line text')], ['autocomplete', L('Il browser compila i dati noti', 'The browser fills in known data')]], L('Dove e come partono i dati di un modulo.', 'Where and how a form’s data leaves.')),
            dBug(pBug(), ['<form action="/login" method="get">', '  <input name="utente">', '  <input name="password" type="password">', '  <button>Entra</button>', '</form>'], 0, '<form action="/login" method="post">', L('Con `get` la password finirebbe nell’indirizzo, nella cronologia e nei log.', 'With `get` the password would end up in the address, the history and the logs.')),
        ]),
    ],
];
