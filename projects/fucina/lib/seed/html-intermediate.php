<?php
/** HTML intermediate: the step between the first tags and semantic pages (attributes, paths, rich text, iframes, inputs, tables, figures, sending forms). */

/** A site's folders and the paths from the current page. $t: you are here, notes. */
if (!function_exists('fig_htmlint_paths')) {
    function fig_htmlint_paths(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M54 62v150M54 98h30M54 134h30M54 212h30M110 150v24h40"/></g>
<g font-family="ui-monospace, Menlo, Consolas, monospace" font-size="14">
<rect x="24" y="30" width="120" height="32" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="38" y="51">sito/</text>
<rect x="84" y="82" width="140" height="32" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="98" y="103">index.html</text>
<rect x="84" y="196" width="120" height="32" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="98" y="217">foto/</text>
<rect x="84" y="118" width="120" height="32" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="98" y="139">corsi/</text>
<rect x="150" y="158" width="150" height="32" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="3"/><text x="164" y="179">html.html</text>
</g>
<text x="310" y="179" font-weight="700" fill="#0B7F5C">← {$t[0]}</text>
<g font-family="ui-monospace, Menlo, Consolas, monospace" font-size="14">
<rect x="440" y="30" width="186" height="36" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="454" y="53">css.html</text>
<rect x="440" y="96" width="186" height="36" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="454" y="119">../index.html</text>
<rect x="440" y="162" width="186" height="36" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="454" y="185">../foto/forgia.jpg</text>
</g>
<g font-size="13" fill="#55527A"><text x="440" y="84">{$t[1]}</text><text x="440" y="150">{$t[2]}</text><text x="440" y="216">{$t[3]}</text></g>
<text x="24" y="256" font-size="13" fill="#55527A">{$t[4]}</text>
</svg>
SVG;
    }
}

/** scope="col" headers name their column, scope="row" headers their row. $t: cells and labels. */
if (!function_exists('fig_htmlint_scope')) {
    function fig_htmlint_scope(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="40" y="34" font-weight="700" font-style="italic">{$t[0]}</text>
<g stroke="#FFFFFF" stroke-width="3">
<rect x="40" y="48" width="180" height="44" fill="#FFE8D6"/><rect x="220" y="48" width="140" height="44" fill="#FFE8D6"/>
<rect x="40" y="92" width="180" height="44" fill="#DCEBFF"/><rect x="220" y="92" width="140" height="44" fill="#FFFFFF"/>
<rect x="40" y="136" width="180" height="44" fill="#DCEBFF"/><rect x="220" y="136" width="140" height="44" fill="#FFFFFF"/>
<rect x="40" y="180" width="180" height="44" fill="#ECE6FF"/><rect x="220" y="180" width="140" height="44" fill="#F7F6FC"/>
</g>
<rect x="40" y="48" width="320" height="176" rx="4" fill="none" stroke="#8A87B0" stroke-width="2"/>
<g font-weight="700"><text x="56" y="76">{$t[1]}</text><text x="236" y="76">Euro</text><text x="56" y="120">{$t[2]}</text><text x="56" y="164">{$t[3]}</text><text x="56" y="208">{$t[4]}</text></g>
<g text-anchor="end"><text x="344" y="120">600</text><text x="344" y="164">250</text><text x="344" y="208" font-weight="700">850</text></g>
<path d="M290 98v74" stroke="#E8590C" stroke-width="3"/><path d="M290 182l-7-12h14z" fill="#E8590C"/>
<path d="M150 114h60" stroke="#2F8CF0" stroke-width="3"/><path d="M220 114l-12-7v14z" fill="#2F8CF0"/>
<g font-family="ui-monospace, Menlo, Consolas, monospace" font-size="14"><text x="390" y="76" fill="#B84508">th scope="col"</text><text x="390" y="120" fill="#1B66BC">th scope="row"</text><text x="390" y="208" fill="#5B37D0">tfoot</text></g>
<g font-size="13" fill="#55527A"><text x="390" y="96">{$t[5]}</text><text x="390" y="140">{$t[6]}</text></g>
<text x="40" y="250" font-size="14" fill="#55527A">{$t[7]}</text>
</svg>
SVG;
    }
}

/** GET puts the data in the address, POST in the body of the request. $t: labels. */
if (!function_exists('fig_htmlint_getpost')) {
    function fig_htmlint_getpost(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="14" y="14" width="300" height="262" rx="14" fill="#F7F6FC" stroke="#2F8CF0" stroke-width="2"/>
<rect x="326" y="14" width="300" height="262" rx="14" fill="#F7F6FC" stroke="#E8590C" stroke-width="2"/>
<text x="164" y="44" text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace" fill="#1B66BC">method="get"</text>
<text x="476" y="44" text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace" fill="#B84508">method="post"</text>
<rect x="34" y="64" width="260" height="34" rx="17" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="50" y="86" font-family="ui-monospace, Menlo, monospace" font-size="14">/cerca<tspan fill="#1B66BC" font-weight="700">?q=gatti</tspan></text>
<rect x="34" y="114" width="260" height="70" rx="10" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="48" y="138" font-family="ui-monospace, Menlo, monospace" font-size="13">GET /cerca?q=gatti</text>
<text x="48" y="164" font-size="13" fill="#55527A">{$t[0]}</text>
<rect x="346" y="64" width="260" height="34" rx="17" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="362" y="86" font-family="ui-monospace, Menlo, monospace" font-size="14">/messaggio</text>
<rect x="346" y="114" width="260" height="70" rx="10" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="360" y="138" font-family="ui-monospace, Menlo, monospace" font-size="13">POST /messaggio</text>
<rect x="356" y="148" width="240" height="28" rx="6" fill="#FFE8D6"/>
<text x="366" y="167" font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#B84508">testo=…&amp;tel=…</text>
<g font-size="13"><text x="34" y="212">✓ {$t[1]}</text><text x="34" y="234">✓ {$t[2]}</text><text x="34" y="256">✗ {$t[3]}</text>
<text x="346" y="212">✓ {$t[4]}</text><text x="346" y="234">✓ {$t[5]}</text><text x="346" y="256">✗ {$t[6]}</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'html-intermediate', 'family' => 'html', 'after' => 'html-base',
    'title' => L('HTML intermedio', 'Intermediate HTML'), 'short' => L('HTML intermedio', 'HTML Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Attributi globali, link interni, testo ricco, iframe, tabelle accessibili, figure e date, moduli più completi e come si inviano.', 'Global attributes, internal links, rich text, iframes, accessible tables, figures and dates, fuller forms and how they are sent.'),
    'lessons' => [
        lesson('attributes', L('Attributi globali', 'Global attributes'), [
            P('Alcuni attributi valgono su quasi tutti i tag: `id` identifica un elemento, `class` lo raggruppa, `title` aggiunge un suggerimento, `hidden` lo nasconde, `lang` ne indica la lingua.', 'Some attributes work on almost every tag: `id` identifies an element, `class` groups it, `title` adds a tooltip, `hidden` hides it, `lang` states its language.'),
            C(L('<p id="intro" class="nota grande" lang="en" title="Una nota">Hello!</p>
<div hidden>Non mi vedi</div>', '<p id="intro" class="nota grande" lang="it" title="A note">Ciao!</p>
<div hidden>You cannot see me</div>'), 'html'),
            P('Un elemento può avere più classi separate da spazi, ma un solo `id`, unico in tutta la pagina.', 'An element can have several classes separated by spaces, but only one `id`, unique in the whole page.'),
            P('Che cosa scegliere? Una `class` per lo stile: la stessa regola CSS veste tutti gli elementi che la portano. Un `id` quando serve un indirizzo preciso: un link interno, una `label` collegata al suo campo, uno script che cerca proprio quell’elemento. `title` è comodo ma si vede solo passando col mouse, quindi non ci mettere informazioni importanti; `lang` su una parola straniera fa pronunciare bene la parola al lettore di schermo.', 'Which to choose? A `class` for styling: the same CSS rule dresses every element carrying it. An `id` when you need an exact address: an internal link, a `label` tied to its field, a script looking for that very element. `title` is handy but only shows on mouse hover, so do not put important information in it; `lang` on a foreign word makes the screen reader pronounce it correctly.'),
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
            C(L('<a href="#contatti">Vai ai contatti</a>
<a href="chi-siamo.html">Stessa cartella</a>
<a href="../index.html">Cartella sopra</a>
<a href="https://esempio.it/">Altro sito</a>
<a href="mailto:ciao@esempio.it">Scrivimi</a>', '<a href="#contatti">Go to contacts</a>
<a href="about.html">Same folder</a>
<a href="../index.html">Folder above</a>
<a href="https://example.com/">Another site</a>
<a href="mailto:hello@example.com">Write to me</a>'), 'html'),
            P('`mailto:` apre l’email e `tel:` il telefono. `../` sale di una cartella.', '`mailto:` opens email and `tel:` the phone. `../` goes up one folder.'),
            Fig(L(fig_htmlint_paths(['sei qui', 'stessa cartella', 'una cartella sopra', 'sopra, poi dentro foto/', 'Un percorso relativo parte sempre dalla cartella della pagina in cui si trova il link.']), fig_htmlint_paths(['you are here', 'same folder', 'one folder up', 'up, then into foto/', 'A relative path always starts from the folder of the page the link is in.'])),
                L('L’albero di un sito: la cartella sito contiene index.html, la cartella corsi con html.html e la cartella foto. Dalla pagina corsi/html.html, `css.html` è nella stessa cartella, `../index.html` è una cartella sopra, `../foto/forgia.jpg` sale e poi entra in foto.', 'A site’s tree: the sito folder holds index.html, the corsi folder with html.html and the foto folder. From the page corsi/html.html, `css.html` is in the same folder, `../index.html` is one folder up, `../foto/forgia.jpg` goes up and then into foto.'),
                L('`../` sale di un livello; un nome semplice resta nella stessa cartella.', '`../` goes up one level; a plain name stays in the same folder.')),
            P('Per i link dentro il tuo sito preferisci i percorsi relativi: se il sito cambia dominio, o lo provi sul tuo computer, continuano a funzionare. Un percorso che inizia con `/` parte invece dalla radice del sito, qualunque sia la pagina. L’errore più comune è un `#` che punta a un `id` scritto diverso: maiuscole e minuscole contano.', 'For links inside your site prefer relative paths: if the site changes domain, or you try it on your own computer, they keep working. A path starting with `/` starts from the site’s root instead, whatever the page. The most common mistake is a `#` pointing to an `id` spelled differently: upper and lower case matter.'),
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
            C(L('<blockquote>Il codice è poesia.</blockquote>
<p>Usa <code>console.log</code> per il <abbr title="debug">dbg</abbr>.</p>
<p>La parola <mark>chiave</mark> è qui.</p>
<pre>  rispetta
    gli spazi</pre>', '<blockquote>Code is poetry.</blockquote>
<p>Use <code>console.log</code> for <abbr title="debugging">dbg</abbr>.</p>
<p>The <mark>key</mark> word is here.</p>
<pre>  keeps
    the spaces</pre>'), 'html'),
            P('`pre` mantiene spazi e a capo così come li scrivi. Tutti gli altri tag li riducono a un solo spazio.', '`pre` keeps spaces and line breaks exactly as written. All other tags reduce them to a single space.'),
            Tip('Scegli il tag per il significato, non per l’aspetto: `mark` non è un evidenziatore decorativo ma «questo è rilevante qui», e `blockquote` non serve a rientrare il testo. Per un blocco di codice si usano insieme: `<pre><code>…</code></pre>`.', 'Choose the tag for its meaning, not its look: `mark` is not a decorative highlighter but “this is relevant here”, and `blockquote` is not for indenting text. For a block of code they are used together: `<pre><code>…</code></pre>`.'),
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
            C(L('<iframe
  src="https://esempio.it/mappa"
  title="La nostra sede"
  width="400" height="300"
  loading="lazy"></iframe>', '<iframe
  src="https://example.com/map"
  title="Our office"
  width="400" height="300"
  loading="lazy"></iframe>'), 'html'),
            P('Un `iframe` carica codice di altri: con `sandbox` limiti quello che può fare, e `loading="lazy"` evita di caricarlo finché non serve.', 'An `iframe` loads other people’s code: `sandbox` limits what it can do, and `loading="lazy"` avoids loading it until needed.'),
            P('Un `sandbox` vuoto blocca tutto: script, moduli, popup. Poi riapri solo quello che serve, per esempio `sandbox="allow-scripts"`. Ogni iframe è una pagina intera da scaricare, quindi pesa: per un video spesso basta un’immagine con un link, e l’iframe vero si carica solo quando l’utente lo chiede. Molti siti, infine, vietano di essere inseriti negli iframe altrui: in quel caso vedrai un riquadro vuoto o un errore.', 'An empty `sandbox` blocks everything: scripts, forms, popups. Then you reopen only what is needed, for example `sandbox="allow-scripts"`. Every iframe is a whole page to download, so it is heavy: for a video an image with a link is often enough, and the real iframe loads only when the user asks. Finally, many sites forbid being placed in other people’s iframes: in that case you will see an empty frame or an error.'),
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
            P('Il vantaggio è doppio: sul telefono compare la tastiera giusta (solo numeri per `number`, il calendario per `date`) e il browser controlla il valore senza una riga di JavaScript. Un browser che non conosce un tipo lo mostra come un semplice campo di testo, quindi nulla si rompe. Anche questi campi però hanno bisogno della loro `label`, come tutti gli altri.', 'The benefit is twofold: on a phone the right keyboard appears (digits only for `number`, the calendar for `date`) and the browser checks the value without a line of JavaScript. A browser that does not know a type shows it as a plain text field, so nothing breaks. These fields still need their `label`, like all the others.'),
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
            C(L('<table>
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
</table>', '<table>
  <caption>March expenses</caption>
  <thead>
    <tr><th scope="col">Item</th><th scope="col">Euro</th></tr>
  </thead>
  <tbody>
    <tr><th scope="row">Rent</th><td>600</td></tr>
    <tr><th scope="row">Shopping</th><td>250</td></tr>
  </tbody>
  <tfoot>
    <tr><th scope="row">Total</th><td>850</td></tr>
  </tfoot>
</table>'), 'html'),
            P('Un `<th>` può stare anche all’inizio di una riga: dà il nome a quella riga. Così chi ascolta la tabella sente «Affitto, Euro, 600» invece di un numero senza contesto.', 'A `<th>` can also start a row: it names that row. That way someone listening to the table hears “Rent, Euro, 600” instead of a number without context.'),
            Fig(L(fig_htmlint_scope(['Spese di marzo', 'Voce', 'Affitto', 'Spesa', 'Totale', 'nomina la colonna', 'nomina la riga', 'Ogni dato ha due nomi: quello della sua colonna e quello della sua riga.']), fig_htmlint_scope(['March expenses', 'Item', 'Rent', 'Shopping', 'Total', 'names the column', 'names the row', 'Every value has two names: its column’s and its row’s.'])),
                L('La tabella delle spese con la didascalia in alto. Una freccia scende dall’intestazione di colonna Euro sui numeri, un’altra va dall’intestazione di riga Affitto verso 600. L’ultima riga, Totale 850, sta nel `tfoot`.', 'The expenses table with its caption on top. One arrow goes down from the Euro column header over the numbers, another goes from the Rent row header towards 600. The last row, Total 850, sits in the `tfoot`.'),
                L('`scope` dice in che direzione vale un’intestazione.', '`scope` says which way a header applies.')),
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
            C(L('<figure>
  <img src="forgia.jpg" alt="Una forgia accesa">
  <figcaption>La forgia di bottega, 1920.</figcaption>
</figure>

<p>Corso aggiornato il <time datetime="2026-11-17">17 novembre</time>.</p>

<dl>
  <dt>HTML</dt>
  <dd>La struttura della pagina.</dd>
  <dt>CSS</dt>
  <dd>Il suo aspetto.</dd>
</dl>', '<figure>
  <img src="forge.jpg" alt="A lit forge">
  <figcaption>The workshop forge, 1920.</figcaption>
</figure>

<p>Course updated on <time datetime="2026-11-17">17 November</time>.</p>

<dl>
  <dt>HTML</dt>
  <dd>The structure of the page.</dd>
  <dt>CSS</dt>
  <dd>Its look.</dd>
</dl>'), 'html'),
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
            Fig(L(fig_htmlint_getpost(['nessun corpo: è tutto nell’indirizzo', 'si salva nei preferiti', 'si condivide', 'mai per dati privati', 'dati nel corpo', 'per cambiare qualcosa', 'non si salva come link']), fig_htmlint_getpost(['no body: it is all in the address', 'can be bookmarked', 'can be shared', 'never for private data', 'data in the body', 'for changing something', 'cannot be saved as a link'])),
                L('Due riquadri. A sinistra `get`: la ricerca finisce nell’indirizzo, /cerca?q=gatti, e la richiesta non ha corpo. A destra `post`: l’indirizzo resta /messaggio e i dati viaggiano nel corpo della richiesta. Sotto, pro e contro di ciascuno.', 'Two panels. On the left `get`: the search ends up in the address, /cerca?q=gatti, and the request has no body. On the right `post`: the address stays /messaggio and the data travels in the body of the request. Below, the pros and cons of each.'),
                L('Leggere: `get`. Scrivere o inviare dati privati: `post`.', 'Reading: `get`. Writing or sending private data: `post`.')),
            C(L('<form action="/cerca" method="get">
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
</form>', '<form action="/cerca" method="get">
  <label for="q">Search</label>
  <input id="q" name="q" type="search">
  <button>Go</button>
</form>

<form action="/messaggio" method="post">
  <label for="testo">Message</label>
  <textarea id="testo" name="testo" rows="4" maxlength="500"></textarea>
  <label for="tel">Phone</label>
  <input id="tel" name="tel" type="tel" autocomplete="tel">
  <button>Send</button>
</form>'), 'html'),
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
