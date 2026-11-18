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
        lesson('containers', L('Contenitori: div e span', 'Containers: div and span'), [
            P('`<div>` e `<span>` sono contenitori **neutri**: non dicono che cosa contengono, servono solo a raggruppare, di solito per dare uno stile con una `class`. `<div>` è un blocco e va a capo; `<span>` sta dentro una riga di testo.', '`<div>` and `<span>` are **neutral** containers: they say nothing about what they hold, they only group things, usually to style them with a `class`. `<div>` is a block and starts on a new line; `<span>` sits inside a line of text.'),
            C('<div class="scheda">
  <h2>Pane</h2>
  <p>Prezzo: <span class="prezzo">2 €</span> al chilo</p>
</div>', 'html'),
            P('Prima di usarli chiediti se esiste un tag con un significato: un titolo, un paragrafo, una lista, un `<strong>`. Nel corso avanzato incontrerai i tag semantici, come `<header>` e `<nav>`, che sostituiscono molti `div`.', 'Before using them, ask whether a tag with a meaning exists: a heading, a paragraph, a list, a `<strong>`. In the advanced course you will meet semantic tags, like `<header>` and `<nav>`, which replace many `div`s.'),
            Tip('Un `<div>` non va mai dentro un `<p>`: il browser chiuderebbe il paragrafo prima del `div`.', 'A `<div>` never goes inside a `<p>`: the browser would close the paragraph before the `div`.'),
        ], [
            Q('c1', L('Quale contenitore sta dentro una riga di testo?', 'Which container sits inside a line of text?'), ['<div>', '<p>', '<span>', '<ul>'], 2, L('`<span>` è in riga, `<div>` è un blocco.', '`<span>` is inline, `<div>` is a block.')),
            Q('c2', L('Che cosa comunica un `<div>` a uno screen reader?', 'What does a `<div>` tell a screen reader?'), [L('Che è un titolo', 'That it is a heading'), L('Niente di speciale: è un contenitore neutro', 'Nothing special: it is a neutral container'), L('Che è un link da seguire', 'That it is a link to follow'), L('Che il testo è importante', 'That the text is important')], 1, L('Per il significato servono altri tag.', 'Meaning needs other tags.')),
            Q('c3', L('Vuoi colorare una parola in un paragrafo, senza darle un significato. Quale tag usi?', 'You want to colour a word in a paragraph, without giving it a meaning. Which tag do you use?'), ['<h1>', '<br>', '<div>', '<span>'], 3, L('Uno `span` con una classe, poi il colore lo dà il CSS.', 'A `span` with a class, then CSS gives the colour.')),
        ], task('html', L('Metti il prezzo in uno `span` con la classe `prezzo` e avvolgi tutto in un `div` con la classe `scheda`.', 'Put the price in a `span` with the class `prezzo` and wrap everything in a `div` with the class `scheda`.'), '<h2>Pane</h2>
<p>Prezzo: 2 €</p>', [has('div.scheda h2', L('Il titolo sta nel `div.scheda`', 'The heading is inside `div.scheda`')), has('div.scheda p span.prezzo', L('Il prezzo è in `span.prezzo`', 'The price is in `span.prezzo`'), '2')], '', "<div class=\"scheda\">\n  <h2>Pane</h2>\n  <p>Prezzo: <span class=\"prezzo\">2 €</span></p>\n</div>"), [
            dMatch(pMatch(), [['<div>', L('Contenitore a blocco', 'Block container')], ['<span>', L('Contenitore in riga', 'Inline container')], ['class', L('Nome da usare nel CSS', 'Name to use in CSS')], ['<p>', L('Paragrafo, con un significato', 'Paragraph, with a meaning')]], L('Contenitori neutri e tag con un significato.', 'Neutral containers and tags with a meaning.')),
            dBug(pBug(), ['<div class="scheda">', '  <p>Costa <div class="prezzo">2 €</div> al chilo.</p>', '</div>'], 1, '  <p>Costa <span class="prezzo">2 €</span> al chilo.</p>', L('Un `div` dentro un paragrafo lo spezza: dentro una riga si usa `span`.', 'A `div` inside a paragraph breaks it: inside a line you use `span`.')),
        ]),
        lesson('devtools', L('Ispezionare e validare', 'Inspecting and validating'), [
            P('Ogni browser mostra il codice di una pagina: tasto destro, «Visualizza sorgente pagina». Gli **strumenti per sviluppatori** (F12, oppure tasto destro e «Ispeziona») mostrano invece l’albero degli elementi dal vivo: passa sopra un elemento e la pagina lo evidenzia.', 'Every browser shows a page’s code: right click, “View page source”. The **developer tools** (F12, or right click and “Inspect”) show the live element tree instead: hover over an element and the page highlights it.'),
            P('Il browser perdona gli errori senza dirti nulla: chiude i tag dove crede e va avanti. Il **validatore del W3C** (validator.w3.org) invece li elenca uno per uno: tag non chiusi, annidamenti sbagliati, attributi mancanti.', 'The browser forgives mistakes without telling you: it closes tags where it guesses and carries on. The **W3C validator** (validator.w3.org) lists them one by one instead: unclosed tags, wrong nesting, missing attributes.'),
            C('<p>Un <strong>testo</p></strong>   <!-- sbagliato -->
<p>Un <strong>testo</strong></p>   <!-- giusto -->
<img src="foto.jpg">               <!-- manca alt -->', 'html'),
            Tip('I tag si chiudono nell’ordine inverso a quello in cui li hai aperti, come scatole dentro scatole.', 'Tags close in the reverse order you opened them, like boxes inside boxes.'),
        ], [
            Q('v1', L('Che cosa fa il browser con un errore nell’HTML?', 'What does the browser do with an HTML mistake?'), [L('Mostra una pagina di errore', 'It shows an error page'), L('Prova a correggerlo senza dire nulla', 'It tries to fix it without saying anything'), L('Cancella la pagina', 'It deletes the page'), L('Chiede all’utente che cosa fare', 'It asks the user what to do')], 1, L('Per trovare gli errori serve il validatore.', 'To find mistakes you need the validator.')),
            Q('v2', L('Quale strumento mostra l’albero degli elementi dal vivo?', 'Which tool shows the live element tree?'), [L('Il validatore del W3C', 'The W3C validator'), L('La barra degli indirizzi', 'The address bar'), L('Gli strumenti per sviluppatori', 'The developer tools'), L('Il titolo della scheda', 'The tab title')], 2, L('Si aprono con F12 o con «Ispeziona».', 'They open with F12 or with “Inspect”.')),
            Q('v3', L('Quale riga è annidata correttamente?', 'Which line is nested correctly?'), ['<p><em>Ciao</p></em>', '<em><p>Ciao</em></p>', '<p><em>Ciao</p>', '<p><em>Ciao</em></p>'], 3, L('Prima si chiude il tag aperto per ultimo.', 'The tag opened last is closed first.')),
        ], task('html', L('Correggi i due errori: chiudi i tag nell’ordine giusto e dai un `alt` all’immagine.', 'Fix the two mistakes: close the tags in the right order and give the image an `alt`.'), '<p>Benvenuto in <strong>Fucina</p></strong>
<img src="logo.png">', [re('<strong>\s*Fucina\s*</strong>\s*</p>', L('`strong` si chiude prima di `p`', '`strong` closes before `p`')), has('img[alt]:not([alt=""])', L('L’immagine ha un `alt`', 'The image has an `alt`'))], '', "<p>Benvenuto in <strong>Fucina</strong></p>\n<img src=\"logo.png\" alt=\"Logo di Fucina\">"), [
            dOrder(pSort(), ['<ul>', '  <li>', '    <a href="corsi.html">Corsi</a>', '  </li>', '</ul>'], L('Ogni tag si chiude dopo quelli che contiene.', 'Each tag closes after the ones it contains.')),
            dFlash(pFlash(), [[L('Il sorgente di una pagina si può vedere dal browser', 'A page’s source can be seen from the browser'), true], [L('Il browser segnala sempre in rosso gli errori dell’HTML', 'The browser always flags HTML mistakes in red'), false], [L('Il validatore del W3C controlla l’HTML', 'The W3C validator checks HTML'), true], [L('Una modifica fatta in «Ispeziona» viene salvata nel file', 'A change made in “Inspect” is saved to the file'), false]], L('Gli strumenti del browser servono a guardare e provare, non a salvare.', 'Browser tools are for looking and trying, not for saving.')),
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
        lesson('popover-dialog', L('Popover e finestre di dialogo', 'Popovers and dialogs'), [
            P('L’attributo `popover` trasforma un elemento in un livello che si apre sopra la pagina. Un pulsante con `popovertarget` lo apre e lo chiude; si chiude anche con Esc o toccando fuori. Niente JavaScript.', 'The `popover` attribute turns an element into a layer that opens above the page. A button with `popovertarget` opens and closes it; it also closes with Esc or by tapping outside. No JavaScript.'),
            C('<button popovertarget="aiuto">Aiuto</button>
<div id="aiuto" popover>
  <p>Premi Invio per salvare.</p>
</div>

<button commandfor="conferma" command="show-modal">Elimina</button>
<dialog id="conferma">
  <p>Vuoi davvero eliminare il file?</p>
  <button commandfor="conferma" command="close">Annulla</button>
</dialog>', 'html'),
            P('Un `<dialog>` aperto come **modale** rende inattivo il resto della pagina e tiene il focus al suo interno finché non si chiude. Gli attributi `commandfor` (l’`id` del bersaglio) e `command` (`show-modal`, `close`, `toggle-popover`…) lo comandano senza script; nei browser più vecchi serve `showModal()` in JavaScript.', 'A `<dialog>` opened as a **modal** makes the rest of the page inert and keeps focus inside it until it closes. The `commandfor` (the target’s `id`) and `command` (`show-modal`, `close`, `toggle-popover`…) attributes control it with no script; older browsers need `showModal()` in JavaScript.'),
            Tip('Per una conferma che deve fermare l’utente usa un dialogo modale; per un suggerimento o un piccolo menu basta un popover.', 'For a confirmation that must stop the user use a modal dialog; for a hint or a small menu a popover is enough.'),
        ], [
            Q('p1', L('Quale attributo collega un pulsante al suo popover?', 'Which attribute links a button to its popover?'), ['target', 'aria-controls', 'popovertarget', 'for'], 2, L('Il valore è l’`id` dell’elemento con `popover`.', 'Its value is the `id` of the element with `popover`.')),
            Q('p2', L('Che cosa fa un dialogo modale al resto della pagina?', 'What does a modal dialog do to the rest of the page?'), [L('La ricarica da capo', 'It reloads it from scratch'), L('La rende inattiva finché non si chiude', 'It makes it inert until it closes'), L('La manda in stampa', 'It sends it to the printer'), L('Niente: si può ancora cliccare tutto', 'Nothing: everything can still be clicked')], 1, L('Così l’utente risponde prima di andare avanti.', 'So the user answers before moving on.')),
            Q('p3', L('Quale valore di `command` chiude un `<dialog>`?', 'Which `command` value closes a `<dialog>`?'), ['open', 'hide', 'show-modal', 'close'], 3, L('`show-modal` lo apre, `close` lo chiude.', '`show-modal` opens it, `close` closes it.')),
        ], task('html', L('Crea un `button` con `popovertarget="menu"` e un `div` con `id="menu"` e l’attributo `popover`.', 'Create a `button` with `popovertarget="menu"` and a `div` with `id="menu"` and the `popover` attribute.'), '', [has('button[popovertarget="menu"]', L('Il pulsante punta al menu', 'The button points to the menu')), has('div#menu[popover]', L('Il menu è un popover', 'The menu is a popover'))], '', "<button popovertarget=\"menu\">Menu</button>\n<div id=\"menu\" popover>Le voci del menu</div>"), [
            dBlanks(pFill(), '<button ___="info">Info</button>' . "\n" . '<div id="info" ___>Testo</div>', ['popovertarget', 'popover'], ['href', 'hidden', 'target'], L('Il pulsante indica il bersaglio, il bersaglio ha `popover`.', 'The button names the target, the target has `popover`.')),
            dMatch(pMatch(), [['popover', L('Livello sopra la pagina', 'Layer above the page')], ['popovertarget', L('Quale popover apre il pulsante', 'Which popover the button opens')], ['<dialog>', L('Finestra di dialogo', 'Dialog window')], ['command="show-modal"', L('Apre come modale', 'Opens as a modal')], ['Esc', L('Chiude popover e modali', 'Closes popovers and modals')]], L('Livelli e finestre native, senza librerie.', 'Native layers and windows, with no libraries.')),
        ]),
        lesson('loading', L('Caricare script e risorse', 'Loading scripts and resources'), [
            P('Un `<script src>` semplice nel `head` ferma la lettura della pagina finché non è scaricato ed eseguito. Con `defer` si scarica in parallelo e parte dopo che l’HTML è stato letto, nell’ordine in cui è scritto. Con `async` parte appena arriva, in ordine sparso: va bene per script indipendenti, come le statistiche. `type="module"` è già differito di suo.', 'A plain `<script src>` in the `head` stops the page being read until it is downloaded and run. With `defer` it downloads in parallel and runs once the HTML has been read, in the order written. With `async` it runs as soon as it arrives, in any order: fine for independent scripts, like analytics. `type="module"` is already deferred on its own.'),
            C('<head>
  <link rel="preconnect" href="https://cdn.esempio.it">
  <link rel="preload" href="font.woff2" as="font" type="font/woff2" crossorigin>
  <script src="app.js" defer></script>
  <script src="statistiche.js" async></script>
  <script type="module" src="main.js"></script>
</head>
<body>
  <img src="hero.jpg" alt="La forgia" fetchpriority="high">
</body>', 'html'),
            P('`preload` chiede subito un file importante che il browser scoprirebbe tardi, come un font. `preconnect` apre in anticipo la connessione verso un altro server. `fetchpriority="high"` indica l’immagine che conta di più, spesso quella grande in cima.', '`preload` requests early an important file the browser would discover late, like a font. `preconnect` opens the connection to another server in advance. `fetchpriority="high"` marks the image that matters most, often the big one at the top.'),
        ], [
            Q('s1', L('Quando parte uno script con `defer`?', 'When does a script with `defer` run?'), [L('Subito, fermando la pagina', 'At once, stopping the page'), L('Solo quando l’utente clicca', 'Only when the user clicks'), L('Dopo la lettura dell’HTML, in ordine', 'After the HTML is read, in order'), L('Mai, se la pagina è lunga', 'Never, if the page is long')], 2, L('Si scarica intanto, ma aspetta l’HTML.', 'It downloads meanwhile, but waits for the HTML.')),
            Q('s2', L('Quale script può usare `async` senza problemi?', 'Which script can use `async` safely?'), [L('Uno script di statistiche indipendente', 'An independent analytics script'), L('Uno che usa le funzioni di un altro script', 'One that uses another script’s functions'), L('Lo script principale che costruisce la pagina', 'The main script that builds the page'), L('Uno che deve partire per ultimo', 'One that must run last')], 0, L('`async` non garantisce l’ordine.', '`async` does not guarantee order.')),
            Q('s3', L('Come si carica di base uno script `type="module"`?', 'How is a `type="module"` script loaded by default?'), [L('Bloccando la pagina', 'Blocking the page'), L('Solo in Chrome', 'Only in Chrome'), L('Solo dopo un clic', 'Only after a click'), L('Differito, come con `defer`', 'Deferred, as with `defer`')], 3, L('Non serve aggiungere `defer`.', 'There is no need to add `defer`.')),
        ], task('html', L('Aggiungi `defer` allo script e un `<link rel="preload">` per `font.woff2` con `as="font"`.', 'Add `defer` to the script and a `<link rel="preload">` for `font.woff2` with `as="font"`.'), "<head>\n  <title>Fucina</title>\n  <script src=\"app.js\"></script>\n</head>", [has('script[src="app.js"][defer]', L('Lo script è differito', 'The script is deferred')), has('link[rel="preload"][as="font"]', L('Il font è precaricato', 'The font is preloaded'))], '', "<head>\n  <title>Fucina</title>\n  <link rel=\"preload\" href=\"font.woff2\" as=\"font\" type=\"font/woff2\" crossorigin>\n  <script src=\"app.js\" defer></script>\n</head>"), [
            dMatch(pMatch(), [['defer', L('Dopo l’HTML, in ordine', 'After the HTML, in order')], ['async', L('Appena pronto, in ordine sparso', 'As soon as ready, in any order')], ['type="module"', L('Differito di serie', 'Deferred by default')], ['preload', L('Un file importante, subito', 'An important file, early')], ['fetchpriority="high"', L('L’immagine più importante', 'The most important image')]], L('Il browser fa prima se gli dici che cosa conta.', 'The browser is faster if you tell it what matters.')),
            dFlash(pFlash(), [[L('Uno script semplice nel `head` blocca la lettura della pagina', 'A plain script in the `head` blocks reading the page'), true], [L('`async` mantiene l’ordine degli script', '`async` keeps scripts in order'), false], [L('`defer` ha effetto anche su uno script scritto in linea, senza `src`', '`defer` also works on an inline script with no `src`'), false], [L('`preconnect` apre in anticipo la connessione', '`preconnect` opens the connection in advance'), true]], L('`defer` e `async` valgono solo per gli script esterni.', '`defer` and `async` only apply to external scripts.')),
        ]),
        lesson('srcset-sizes', L('Immagini responsive: srcset e sizes', 'Responsive images: srcset and sizes'), [
            P('Con `srcset` e i descrittori `w` elenchi la stessa immagine in più larghezze; con `sizes` dici quanto sarà larga sulla pagina. Il browser fa i conti, densità dello schermo compresa, e scarica il file più piccolo che resta nitido.', 'With `srcset` and `w` descriptors you list the same image in several widths; with `sizes` you say how wide it will be on the page. The browser does the maths, screen density included, and downloads the smallest file that stays sharp.'),
            C('<img
  src="forgia-800.jpg"
  srcset="forgia-400.jpg 400w,
          forgia-800.jpg 800w,
          forgia-1600.jpg 1600w"
  sizes="(min-width: 900px) 50vw, 100vw"
  alt="La forgia accesa"
  width="1600" height="1067">', 'html'),
            P('Qui l’immagine occupa metà finestra sugli schermi larghi e tutta la finestra negli altri casi. Su un telefono largo 400px con densità 2 servono 800 pixel: il browser sceglie `forgia-800.jpg`. `<picture>` serve invece quando cambi l’immagine stessa (inquadratura o formato), non solo la sua dimensione.', 'Here the image takes half the window on wide screens and the whole window otherwise. On a 400px-wide phone with density 2 it needs 800 pixels: the browser picks `forgia-800.jpg`. `<picture>` is for when you change the image itself (crop or format), not only its size.'),
        ], [
            Q('z1', L('Che cosa dice `800w` in `srcset`?', 'What does `800w` say in `srcset`?'), [L('Che l’immagine si mostra a 800px', 'That the image is shown at 800px'), L('Che il file è largo 800 pixel', 'That the file is 800 pixels wide'), L('Che il file pesa 800 KB', 'That the file weighs 800 KB'), L('Che va bene per schermi da 800px in su', 'That it suits screens from 800px up')], 1, L('`w` descrive il file; la larghezza mostrata la dice `sizes`.', '`w` describes the file; the displayed width is given by `sizes`.')),
            Q('z2', L('Che cosa indica l’attributo `sizes`?', 'What does the `sizes` attribute state?'), [L('Il peso di ogni file', 'The weight of each file'), L('I formati disponibili', 'The available formats'), L('Quanto sarà larga l’immagine sulla pagina', 'How wide the image will be on the page'), L('Quante immagini ci sono', 'How many images there are')], 2, L('Il browser lo sa prima ancora di applicare il CSS.', 'The browser knows it even before applying the CSS.')),
            Q('z3', L('Quando conviene `<picture>` invece di `srcset`?', 'When is `<picture>` better than `srcset`?'), [L('Per tutte le immagini piccole', 'For all small images'), L('Sempre, perché è più recente', 'Always, because it is newer'), L('Mai, perché è deprecato', 'Never, because it is deprecated'), L('Quando cambi inquadratura o formato', 'When you change the crop or the format')], 3, L('Per la sola dimensione bastano `srcset` e `sizes`.', 'For size alone `srcset` and `sizes` are enough.')),
        ], task('html', L('Crea un’immagine con un `srcset` che offra una versione `400w` e una `800w`, un attributo `sizes` e un `alt`.', 'Create an image with a `srcset` offering a `400w` and an `800w` version, a `sizes` attribute and an `alt`.'), '', [has('img[srcset*="400w"][srcset*="800w"]', L('Ci sono le due larghezze', 'Both widths are there')), has('img[sizes]', L('C’è `sizes`', '`sizes` is there')), has('img[alt]:not([alt=""])', L('C’è un `alt`', 'There is an `alt`'))], '', "<img src=\"foto-800.jpg\"\n  srcset=\"foto-400.jpg 400w, foto-800.jpg 800w\"\n  sizes=\"100vw\" alt=\"Il porto\">"), [
            dBlanks(pFill(), "<img src=\"a-800.jpg\"\n  ___=\"a-400.jpg 400___, a-800.jpg 800w\"\n  ___=\"100vw\" alt=\"Mare\">", ['srcset', 'w', 'sizes'], ['src', 'px', 'media'], L('I file con le loro larghezze, poi la larghezza sulla pagina.', 'The files with their widths, then the width on the page.')),
            dFlash(pFlash(), [[L('Con `srcset` è il browser a scegliere il file', 'With `srcset` the browser picks the file'), true], [L('`sizes` elenca i file disponibili', '`sizes` lists the available files'), false], [L('Uno schermo con densità 2 chiede più pixel', 'A density-2 screen asks for more pixels'), true], [L('`w` indica il peso in KB', '`w` gives the weight in KB'), false]], L('Meno dati sui telefoni, immagini nitide ovunque.', 'Less data on phones, sharp images everywhere.')),
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
        lesson('attach', L('Collegare il CSS alla pagina', 'Linking CSS to the page'), [
            P('Il CSS arriva alla pagina in tre modi. Un **file esterno** collegato nel `head` con `<link rel="stylesheet">`: è il migliore, perché un solo file serve tutte le pagine e il browser lo tiene in memoria. Un blocco `<style>` nel `head`, che vale solo per quella pagina. L’attributo `style` su un singolo elemento, lo stile in linea.', 'CSS reaches the page in three ways. An **external file** linked in the `head` with `<link rel="stylesheet">`: it is the best, because one file serves every page and the browser keeps it cached. A `<style>` block in the `head`, which applies to that page only. The `style` attribute on a single element, the inline style.'),
            C('<head>
  <link rel="stylesheet" href="stile.css">
  <style>
    p { color: navy; } /* vale solo qui */
  </style>
</head>
<body>
  <p style="color: red;">Rosso: vince lo stile in linea</p>
</body>', 'html'),
            P('Lo stile in linea batte quasi tutto, ma è scomodo da cambiare: usalo il meno possibile. I commenti del CSS si scrivono tra `/*` e `*/`; la doppia barra `//` nel CSS non è un commento.', 'Inline style beats almost everything, but it is awkward to change: use it as little as possible. CSS comments go between `/*` and `*/`; a double slash `//` is not a comment in CSS.'),
        ], [
            Q('a1', L('Qual è il modo migliore per dare lo stile a un sito intero?', 'What is the best way to style a whole site?'), [L('L’attributo `style` su ogni elemento', 'The `style` attribute on every element'), L('Un blocco `<style>` in ogni pagina', 'A `<style>` block in every page'), L('Un file esterno collegato con `<link>`', 'An external file linked with `<link>`'), L('Un’immagine con i colori', 'An image with the colours')], 2, L('Un file, tutte le pagine, una sola modifica.', 'One file, every page, a single change.')),
            Q('a2', L('Che cosa dice `rel="stylesheet"`?', 'What does `rel="stylesheet"` say?'), [L('Che il file è un’immagine', 'That the file is an image'), L('Che il file è un foglio di stile', 'That the file is a stylesheet'), L('Che il link si apre in una nuova scheda', 'That the link opens in a new tab'), L('Che la pagina è in italiano', 'That the page is in Italian')], 1, L('`rel` descrive la relazione con il file collegato.', '`rel` describes the relation with the linked file.')),
            Q('a3', L('Come si scrive un commento nel CSS?', 'How do you write a comment in CSS?'), ['// nota', '<!-- nota -->', '# nota', '/* nota */'], 3, L('`<!-- -->` è il commento dell’HTML.', '`<!-- -->` is the HTML comment.')),
        ], task('html', L('Nel `head` collega il file `stile.css` con un `<link>` e aggiungi un blocco `<style>` che colori i paragrafi di `navy`.', 'In the `head` link the file `stile.css` with a `<link>` and add a `<style>` block that colours paragraphs `navy`.'), "<!doctype html>\n<html lang=\"it\">\n<head>\n  <title>Fucina</title>\n</head>\n<body>\n  <p>Ciao!</p>\n</body>\n</html>", [has('link[rel="stylesheet"][href="stile.css"]', L('Il foglio di stile è collegato', 'The stylesheet is linked')), css('p', 'color', 'rgb(0, 0, 128)', L('I paragrafi sono navy', 'Paragraphs are navy'))], '', "<!doctype html>\n<html lang=\"it\">\n<head>\n  <title>Fucina</title>\n  <link rel=\"stylesheet\" href=\"stile.css\">\n  <style>\n    p { color: navy; }\n  </style>\n</head>\n<body>\n  <p>Ciao!</p>\n</body>\n</html>"), [
            dMatch(pMatch(), [['<link rel="stylesheet">', L('File esterno', 'External file')], ['<style>', L('Blocco nella pagina', 'Block in the page')], ['style="…"', L('Stile di un solo elemento', 'Style of one element')], ['/* … */', L('Commento', 'Comment')]], L('Tre strade per il CSS, una sola consigliata per i siti.', 'Three roads for CSS, one recommended for sites.')),
            dBug(pBug(), ['<head>', '  <title>Fucina</title>', '  <link rel="stylesheet" src="stile.css">', '</head>'], 2, '  <link rel="stylesheet" href="stile.css">', L('`<link>` usa `href`, come i link; `src` è di immagini e script.', '`<link>` uses `href`, like links; `src` is for images and scripts.')),
        ]),
        lesson('sizes', L('Dimensioni flessibili', 'Flexible sizes'), [
            P('`width` e `height` fissano una misura. `max-width` mette un tetto: la scatola si stringe sugli schermi piccoli ma non supera mai il limite. `min-height` garantisce un’altezza minima e lascia crescere il contenuto.', '`width` and `height` fix a size. `max-width` sets a ceiling: the box shrinks on small screens but never goes past the limit. `min-height` guarantees a minimum height and lets the content grow.'),
            C('.pagina {
  width: 100%;
  max-width: 960px;
  margin: 0 auto;
}
.hero { min-height: 300px; }
.video { width: 100%; aspect-ratio: 16 / 9; }
img { max-width: 100%; height: auto; }', 'css'),
            P('`aspect-ratio` mantiene le proporzioni: dai la larghezza e l’altezza segue. Evita un `height` fisso sulle scatole con del testo: se il testo cresce, esce dalla scatola.', '`aspect-ratio` keeps the proportions: give the width and the height follows. Avoid a fixed `height` on boxes with text: if the text grows, it spills out of the box.'),
        ], [
            Q('z1', L('Che cosa fa `max-width: 960px`?', 'What does `max-width: 960px` do?'), [L('La scatola è sempre larga 960px', 'The box is always 960px wide'), L('Al massimo 960px, e si stringe se c’è meno spazio', 'At most 960px, and it shrinks if there is less room'), L('Almeno 960px, anche sui telefoni', 'At least 960px, even on phones'), L('La scatola è alta 960px', 'The box is 960px tall')], 1, L('È un tetto, non una misura fissa.', 'It is a ceiling, not a fixed size.')),
            Q('z2', L('Con `width: 320px` e `aspect-ratio: 16 / 9`, quanto è alta la scatola?', 'With `width: 320px` and `aspect-ratio: 16 / 9`, how tall is the box?'), ['320px', '90px', '160px', '180px'], 3, L('320 ÷ 16 × 9 = 180.', '320 ÷ 16 × 9 = 180.')),
            Q('z3', L('Perché evitare un `height` fisso su una scatola con del testo?', 'Why avoid a fixed `height` on a box with text?'), [L('Se il testo cresce, esce dalla scatola', 'If the text grows, it spills out of the box'), L('Perché `height` è vietato', 'Because `height` is forbidden'), L('Perché rallenta la pagina', 'Because it slows the page down'), L('Perché cambia il colore del testo', 'Because it changes the text colour')], 0, L('Meglio `min-height`: un minimo, senza tetto.', 'Better `min-height`: a minimum, with no ceiling.')),
        ], task('css', L('Dai a `#box` un `max-width` di `300px` e un `aspect-ratio` di `16 / 9`.', 'Give `#box` a `max-width` of `300px` and an `aspect-ratio` of `16 / 9`.'), "#box {\n  background: #ffe8d6;\n}", [css('#box', 'max-width', '300px', L('Il tetto è 300px', 'The ceiling is 300px')), css('#box', 'aspect-ratio', '16 / 9', L('Le proporzioni sono 16:9', 'The proportions are 16:9'))], $page, "#box {\n  background: #ffe8d6;\n  max-width: 300px;\n  aspect-ratio: 16 / 9;\n}"), [
            dMatch(pMatch(), [['max-width', L('Un tetto alla larghezza', 'A ceiling on the width')], ['min-height', L('Un’altezza minima', 'A minimum height')], ['aspect-ratio', L('Le proporzioni', 'The proportions')], ['height: auto', L('L’altezza segue il contenuto', 'The height follows the content')]], L('Misure che si adattano invece di rompersi.', 'Sizes that adapt instead of breaking.')),
            dFlash(pFlash(), [[L('`max-width: 100%` impedisce alle immagini di uscire dal contenitore', '`max-width: 100%` stops images overflowing their container'), true], [L('`min-height` impedisce al contenuto di crescere', '`min-height` stops the content growing'), false], [L('`aspect-ratio: 1` crea un quadrato', '`aspect-ratio: 1` makes a square'), true], [L('Un `height` fisso si adatta al testo', 'A fixed `height` adapts to the text'), false]], L('Dai i limiti, lascia al browser il resto.', 'Set the limits, leave the rest to the browser.')),
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
        lesson('layers', L('Livelli a cascata, :is() e :where()', 'Cascade layers, :is() and :where()'), [
            P('`@layer` raggruppa le regole in livelli con un ordine che decidi tu: un livello che viene dopo vince su quelli prima, **qualunque sia la specificità**. Gli stili fuori da ogni livello battono tutti i livelli.', '`@layer` groups rules into layers in an order you choose: a later layer wins over earlier ones, **whatever the specificity**. Styles outside any layer beat every layer.'),
            C('@layer reset, base, componenti;

@layer reset {
  #pagina a { color: black; } /* un id, ma nel livello più debole */
}
@layer componenti {
  .menu a { color: white; }   /* vince lui */
}

:is(h1, h2, h3) { line-height: 1.2; }
:where(ul, ol) { padding-left: 1.5rem; } /* specificità zero */', 'css'),
            P('`:is()` raggruppa più selettori e prende la specificità del più forte; `:where()` fa lo stesso ma con specificità zero: perfetto per gli stili di base, facili da sovrascrivere. Con i livelli metti in fila reset, librerie e componenti senza guerre di specificità.', '`:is()` groups several selectors and takes the specificity of the strongest; `:where()` does the same with zero specificity: perfect for default styles that are easy to override. With layers you line up resets, libraries and components with no specificity wars.'),
        ], [
            Q('y1', L('Tra due livelli, quale regola vince?', 'Between two layers, which rule wins?'), [L('Quella con il selettore più specifico', 'The one with the more specific selector'), L('Quella del livello che viene dopo nell’ordine', 'The one in the layer that comes later in the order'), L('Quella scritta per prima nel file', 'The one written first in the file'), L('Nessuna: si annullano a vicenda', 'Neither: they cancel each other out')], 1, L('L’ordine dei livelli conta prima della specificità.', 'Layer order counts before specificity.')),
            Q('y2', L('Qual è la specificità di `:where(.nota)`?', 'What is the specificity of `:where(.nota)`?'), ['0,1,0', '0,0,1', '1,0,0', '0,0,0'], 3, L('`:where()` azzera sempre la specificità.', '`:where()` always sets specificity to zero.')),
            Q('y3', L('Uno stile fuori dai livelli e uno dentro un livello: chi vince?', 'A style outside the layers and one inside a layer: which wins?'), [L('Sempre quello dentro il livello', 'Always the one inside the layer'), L('Dipende dalla lunghezza del file', 'It depends on the file length'), L('Quello fuori dai livelli', 'The one outside the layers'), L('Il browser segnala un errore', 'The browser reports an error')], 2, L('Vale per le dichiarazioni normali; con `!important` l’ordine si rovescia.', 'This holds for normal declarations; with `!important` the order flips.')),
        ], task('css', L('Dichiara l’ordine `@layer base, temi;`. Nel livello `base` colora `.nota` di blu (`blue`), nel livello `temi` colora i `p` di rosso (`red`): la nota deve risultare rossa.', 'Declare the order `@layer base, temi;`. In the `base` layer colour `.nota` blue (`blue`), in the `temi` layer colour `p` red (`red`): the note must end up red.'), '', [re('@layer\s+base\s*,\s*temi\s*;', L('L’ordine è dichiarato', 'The order is declared')), re('@layer\s+temi\s*\{', L('C’è il livello `temi`', 'The `temi` layer is there')), css('.nota', 'color', 'rgb(255, 0, 0)', L('La nota è rossa', 'The note is red'))], $page, "@layer base, temi;\n@layer base {\n  .nota { color: blue; }\n}\n@layer temi {\n  p { color: red; }\n}"), [
            dMatch(pMatch(), [['@layer a, b;', L('Dichiara l’ordine dei livelli', 'Declares the layer order')], [':is()', L('Raggruppa, specificità del più forte', 'Groups, strongest specificity')], [':where()', L('Raggruppa, specificità zero', 'Groups, zero specificity')], [L('Stile fuori dai livelli', 'Unlayered style'), L('Batte tutti i livelli', 'Beats every layer')]], L('Strumenti per tenere in pugno la cascata.', 'Tools to keep the cascade under control.')),
            dFlash(pFlash(), [[L('In un livello successivo un selettore di tag batte un id di un livello precedente', 'In a later layer a tag selector beats an id from an earlier layer'), true], [L('`:where()` aumenta la specificità', '`:where()` raises specificity'), false], [L('`:is(h1, h2)` sceglie sia gli `h1` sia gli `h2`', '`:is(h1, h2)` picks both `h1` and `h2`'), true], [L('Per usare `@layer` serve un preprocessore', '`@layer` needs a preprocessor'), false]], L('Livelli nativi del browser: niente strumenti in più.', 'Native browser layers: no extra tools.')),
        ]),
        lesson('grid-areas', L('Aree della griglia e subgrid', 'Grid areas and subgrid'), [
            P('`grid-template-areas` disegna l’impaginazione con dei nomi: ogni stringa è una riga, ogni parola una cella. Gli elementi vanno nella loro area con `grid-area`. Un punto `.` lascia una cella vuota, e ogni riga deve avere lo stesso numero di celle.', '`grid-template-areas` draws the layout with names: each string is a row, each word a cell. Items go into their area with `grid-area`. A dot `.` leaves a cell empty, and every row must have the same number of cells.'),
            C('.pagina {
  display: grid;
  grid-template-columns: 200px 1fr;
  grid-template-areas:
    "testa testa"
    "menu  corpo"
    "piede piede";
  gap: 16px;
}
header { grid-area: testa; }
nav    { grid-area: menu; }
main   { grid-area: corpo; }
footer { grid-area: piede; }

.card {
  display: grid;
  grid-row: span 3;
  grid-template-rows: subgrid;
}', 'css'),
            P('In una media query basta riscrivere le aree e cambia tutta la pagina. `subgrid` fa usare a una griglia figlia le righe o le colonne del genitore: così titoli e bottoni di card affiancate restano allineati anche con testi di lunghezza diversa.', 'In a media query you only rewrite the areas and the whole page changes. `subgrid` makes a child grid use its parent’s rows or columns: that way titles and buttons of side-by-side cards stay aligned even with texts of different lengths.'),
        ], [
            Q('g1', L('Che cosa rappresenta ogni stringa di `grid-template-areas`?', 'What does each string of `grid-template-areas` represent?'), [L('Una colonna', 'A column'), L('Una riga', 'A row'), L('Un singolo elemento', 'A single item'), L('Un livello di profondità', 'A depth layer')], 1, L('Le parole della stringa sono le celle di quella riga.', 'The words in the string are the cells of that row.')),
            Q('g2', L('Come lasci vuota una cella in `grid-template-areas`?', 'How do you leave a cell empty in `grid-template-areas`?'), ['_', 'none', '0', '.'], 3, L('Uno o più punti di fila valgono come cella vuota.', 'One or more dots in a row count as an empty cell.')),
            Q('g3', L('A che cosa serve `subgrid`?', 'What is `subgrid` for?'), [L('A far allineare i figli di più card sulle righe del genitore', 'To line up the children of several cards on the parent’s rows'), L('A creare una griglia dentro una tabella', 'To create a grid inside a table'), L('A nascondere le righe vuote', 'To hide empty rows'), L('A rendere la griglia più veloce', 'To make the grid faster')], 0, L('La griglia figlia eredita le tracce invece di crearne di nuove.', 'The child grid inherits the tracks instead of making new ones.')),
        ], task('css', L('Rendi `.pagina` una griglia con le aree `"testa"`, `"corpo"` e `"piede"` una sotto l’altra, e metti `header`, `main` e `footer` nelle loro aree.', 'Make `.pagina` a grid with the areas `"testa"`, `"corpo"` and `"piede"` one below the other, and put `header`, `main` and `footer` in their areas.'), '', [css('.pagina', 'display', 'grid', L('È una griglia', 'It is a grid')), re('grid-template-areas\s*:\s*["\']testa["\']\s*["\']corpo["\']\s*["\']piede["\']', L('Le aree sono disegnate', 'The areas are drawn')), css('header', 'grid-row-start', 'testa', L('`header` sta in `testa`', '`header` sits in `testa`')), css('footer', 'grid-row-start', 'piede', L('`footer` sta in `piede`', '`footer` sits in `piede`'))], '<div class="pagina">
  <header>Testa</header>
  <main>Corpo</main>
  <footer>Piede</footer>
</div>', ".pagina {\n  display: grid;\n  grid-template-areas: \"testa\" \"corpo\" \"piede\";\n}\nheader { grid-area: testa; }\nmain { grid-area: corpo; }\nfooter { grid-area: piede; }"), [
            dBug(pBug(), ['.pagina {', '  display: grid;', '  grid-template-areas: "testa testa" "menu";', '}'], 2, '  grid-template-areas: "testa testa" "menu corpo";', L('Ogni riga deve avere lo stesso numero di celle, altrimenti la dichiarazione viene ignorata.', 'Every row must have the same number of cells, or the declaration is ignored.')),
            dBlanks(pFill(), "header { ___: testa; }\n.card {\n  display: grid;\n  grid-template-rows: ___;\n}", ['grid-area', 'subgrid'], ['grid-name', 'inherit', 'area'], L('`grid-area` sceglie l’area, `subgrid` riusa le righe del genitore.', '`grid-area` picks the area, `subgrid` reuses the parent’s rows.')),
        ]),
        lesson('colors-modern', L('Colori moderni', 'Modern colours'), [
            P('`oklch()` descrive un colore con luminosità percepita, croma (intensità) e tinta. Se cambi solo la luminosità ottieni toni più chiari o più scuri che all’occhio sembrano davvero equidistanti, cosa che `hsl()` non garantisce. `color-mix()` mescola due colori nello spazio che scegli.', '`oklch()` describes a colour by perceived lightness, chroma (intensity) and hue. Changing only the lightness gives lighter or darker shades that really look evenly spaced to the eye, which `hsl()` does not guarantee. `color-mix()` mixes two colours in the space you choose.'),
            C(':root {
  color-scheme: light dark;
  --brand: oklch(65% 0.2 40);
  --brand-chiaro: color-mix(in oklch, var(--brand), white 30%);
  --sfondo: light-dark(#ffffff, #16161d);
  --testo: light-dark(#1b1b4b, #f1f1f5);
}
body { background: var(--sfondo); color: var(--testo); }
.btn:hover { background: oklch(from var(--brand) calc(l - 0.1) c h); }', 'css'),
            P('`light-dark()` usa il primo valore con il tema chiaro e il secondo con quello scuro, purché `color-scheme` li permetta entrambi. Con la sintassi relativa (`from`) ricavi un colore da un altro: qui lo stesso arancione, un po’ più scuro.', '`light-dark()` uses the first value with the light theme and the second with the dark one, as long as `color-scheme` allows both. With the relative syntax (`from`) you derive one colour from another: here the same orange, a little darker.'),
        ], [
            Q('k1', L('Che cosa indica il primo valore di `oklch(65% 0.2 40)`?', 'What does the first value of `oklch(65% 0.2 40)` stand for?'), [L('La tinta', 'The hue'), L('La saturazione', 'The saturation'), L('La luminosità percepita', 'The perceived lightness'), L('La trasparenza', 'The transparency')], 2, L('Poi vengono croma e tinta.', 'Chroma and hue come next.')),
            Q('k2', L('Che cosa serve perché `light-dark()` cambi con il tema?', 'What does `light-dark()` need to change with the theme?'), [L('`color-scheme: light dark`', '`color-scheme: light dark`'), L('Una riga di JavaScript', 'A line of JavaScript'), L('Una media query in ogni regola', 'A media query in every rule'), L('Un’immagine di sfondo', 'A background image')], 0, L('Senza `color-scheme` il browser usa sempre il primo valore.', 'Without `color-scheme` the browser always uses the first value.')),
            Q('k3', L('Che cosa produce `color-mix(in oklch, red, white 30%)`?', 'What does `color-mix(in oklch, red, white 30%)` produce?'), [L('Un rosso più scuro del 30%', 'A red 30% darker'), L('Un rosso con il 30% di bianco', 'A red with 30% white'), L('Un rosso trasparente al 30%', 'A red 30% transparent'), L('Un errore: si mescolano solo i grigi', 'An error: only greys can be mixed')], 1, L('La percentuale dice quanto pesa il secondo colore.', 'The percentage says how much the second colour weighs.')),
        ], task('css', L('In `:root` imposta `color-scheme: light dark`; dai a `.titolo` il colore `light-dark(#1b1b4b, #f1f1f5)` e a `.nota` uno sfondo fatto con `color-mix(in oklch, …)`.', 'In `:root` set `color-scheme: light dark`; give `.titolo` the colour `light-dark(#1b1b4b, #f1f1f5)` and `.nota` a background made with `color-mix(in oklch, …)`.'), '', [re('color-scheme\s*:\s*light\s+dark', L('Il tema chiaro e scuro è permesso', 'Light and dark themes are allowed')), re('\.titolo\s*\{[^}]*color\s*:\s*light-dark\(', L('Il titolo usa `light-dark()`', 'The heading uses `light-dark()`')), re('\.nota\s*\{[^}]*background(-color)?\s*:\s*color-mix\(\s*in\s+oklch', L('La nota usa `color-mix()`', 'The note uses `color-mix()`'))], $page, ":root { color-scheme: light dark; }\n.titolo { color: light-dark(#1b1b4b, #f1f1f5); }\n.nota { background: color-mix(in oklch, orange, white 40%); }"), [
            dMatch(pMatch(), [['oklch()', L('Luminosità, croma, tinta', 'Lightness, chroma, hue')], ['color-mix()', L('Mescola due colori', 'Mixes two colours')], ['light-dark()', L('Un valore per tema', 'One value per theme')], ['color-scheme', L('I temi che la pagina supporta', 'The themes the page supports')], ['from', L('Ricava un colore da un altro', 'Derives a colour from another')]], L('Colori calcolati dal browser, niente preprocessore.', 'Colours worked out by the browser, no preprocessor.')),
            dFlash(pFlash(), [[L('In `oklch` passi uguali di luminosità sembrano equidistanti', 'In `oklch` equal lightness steps look evenly spaced'), true], [L('`light-dark()` funziona anche senza `color-scheme`', '`light-dark()` works without `color-scheme` too'), false], [L('`color-mix()` richiede Sass', '`color-mix()` needs Sass'), false], [L('`oklch(65% 0.2 40)` è un colore valido', '`oklch(65% 0.2 40)` is a valid colour'), true]], L('Il CSS ora sa fare da solo i conti sui colori.', 'CSS can now do colour maths by itself.')),
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
        lesson('content-blocks', L('Mixin con @content', 'Mixins with @content'), [
            P('Un mixin può ricevere un intero blocco di regole: nel mixin `@content` segna il punto dove finirà il blocco scritto tra graffe dopo `@include`. È il modo classico per dare un nome ai breakpoint.', 'A mixin can receive a whole block of rules: inside the mixin `@content` marks the spot where the block written in braces after `@include` will go. It is the classic way to give breakpoints a name.'),
            C('@mixin schermo($min) {
  @media (min-width: $min) {
    @content;
  }
}

.lista {
  display: block;

  @include schermo(700px) {
    display: flex;
  }
}', 'scss'),
            P('Il risultato è `.lista { display: block; }` più una media query con `.lista { display: flex; }`. Lo stesso trucco funziona per stati come `&:hover` dentro `@media (hover: hover)`: scrivi la regola una volta, il mixin la incornicia.', 'The result is `.lista { display: block; }` plus a media query with `.lista { display: flex; }`. The same trick works for states like `&:hover` inside `@media (hover: hover)`: you write the rule once, the mixin frames it.'),
        ], [
            Q('w1', L('Che cosa inserisce `@content` dentro un mixin?', 'What does `@content` insert inside a mixin?'), [L('I parametri del mixin', 'The mixin’s parameters'), L('Un file parziale', 'A partial file'), L('Il blocco passato tra graffe con `@include`', 'The block passed in braces with `@include`'), L('Il contenuto HTML dell’elemento', 'The element’s HTML content')], 2, L('Il blocco viaggia insieme a `@include`.', 'The block travels with `@include`.')),
            Q('w2', L('Dove si scrive il blocco per `@content`?', 'Where do you write the block for `@content`?'), [L('Come stringa tra parentesi', 'As a string in brackets'), L('Tra graffe subito dopo `@include nome`', 'In braces right after `@include name`'), L('In un file separato', 'In a separate file'), L('Dopo un punto e virgola', 'After a semicolon')], 1, L('`@include schermo(700px) { … }`.', '`@include schermo(700px) { … }`.')),
            Q('w3', L('Qual è un uso tipico di `@content`?', 'What is a typical use of `@content`?'), [L('Calcolare un colore', 'Working out a colour'), L('Importare un font', 'Importing a font'), L('Dichiarare variabili globali', 'Declaring global variables'), L('Dare un nome ai breakpoint delle media query', 'Naming media query breakpoints')], 3, L('Così tutto il progetto usa gli stessi punti di rottura.', 'That way the whole project uses the same breakpoints.')),
        ], task('scss', L('Scrivi il mixin `largo` con una media query `min-width: 900px` che contiene `@content`, poi usalo in `.card` per impostare `padding: 32px`.', 'Write the `largo` mixin with a `min-width: 900px` media query holding `@content`, then use it in `.card` to set `padding: 32px`.'), '.card {
  padding: 16px;
}', [re('@mixin\s+largo\b', L('Definisci `largo`', 'You define `largo`')), re('@media[^{]*min-width\s*:\s*900px[^{]*\{\s*@content\s*;', L('La media query contiene `@content`', 'The media query holds `@content`')), re('@include\s+largo\s*(\(\s*\))?\s*\{[^}]*padding\s*:\s*32px', L('`.card` passa il blocco con il padding', '`.card` passes the block with the padding'))], '', "@mixin largo {\n  @media (min-width: 900px) {\n    @content;\n  }\n}\n.card {\n  padding: 16px;\n  @include largo {\n    padding: 32px;\n  }\n}"), [
            dBlanks(pFill(), "@mixin hover-sicuro {\n  @media (hover: hover) {\n    &:hover { ___; }\n  }\n}\n.btn {\n  ___ hover-sicuro { color: red; }\n}", ['@content', '@include'], ['@return', '@extend', '@use'], L('Il mixin incornicia, `@include` porta il blocco.', 'The mixin frames, `@include` brings the block.')),
            dOrder(pSort(), ['@mixin schermo($min) {', '  @media (min-width: $min) {', '    @content;', '  }', '}'], L('Il mixin avvolge la media query, che avvolge `@content`.', 'The mixin wraps the media query, which wraps `@content`.')),
        ]),
        lesson('control-flow', L('Condizioni e cicli: @if e @for', 'Conditions and loops: @if and @for'), [
            P('`@if` ed `@else` scelgono quali regole scrivere in base a un valore. `@for` ripete un blocco contando: con `through` include l’ultimo numero, con `to` lo esclude.', '`@if` and `@else` choose which rules to write based on a value. `@for` repeats a block while counting: with `through` it includes the last number, with `to` it leaves it out.'),
            C('@mixin tema($scuro: false) {
  @if $scuro {
    background: #16161d;
    color: white;
  } @else {
    background: white;
    color: #16161d;
  }
}
.pannello { @include tema($scuro: true); }

@for $i from 1 through 3 {
  .mt-#{$i} { margin-top: $i * 0.25rem; }
}', 'scss'),
            P('Il ciclo crea `.mt-1`, `.mt-2` e `.mt-3`. Esiste anche `@while`, ma serve di rado. Attenzione: un ciclo che genera centinaia di classi gonfia il CSS che il browser deve scaricare.', 'The loop creates `.mt-1`, `.mt-2` and `.mt-3`. There is also `@while`, but it is rarely needed. Careful: a loop that generates hundreds of classes bloats the CSS the browser has to download.'),
        ], [
            Q('c1', L('Quante classi crea `@for $i from 1 to 3`?', 'How many classes does `@for $i from 1 to 3` create?'), ['1', '2', '3', '4'], 1, L('`to` esclude il 3: restano 1 e 2.', '`to` leaves out 3: 1 and 2 remain.')),
            Q('c2', L('Quale regola segue un `@if` per il caso contrario?', 'Which rule follows an `@if` for the opposite case?'), ['@elif', '@otherwise', '@else', '@then'], 2, L('Si può anche scrivere `@else if` per una seconda condizione.', 'You can also write `@else if` for a second condition.')),
            Q('c3', L('Qual è il rischio dei cicli in SCSS?', 'What is the risk of loops in SCSS?'), [L('Il browser li esegue a ogni clic', 'The browser runs them on every click'), L('Non funzionano con le variabili', 'They do not work with variables'), L('Rendono lo SCSS illeggibile per il compilatore', 'They make SCSS unreadable for the compiler'), L('Possono produrre un CSS enorme', 'They can produce a huge CSS file')], 3, L('Il ciclo gira solo durante la compilazione, ma il risultato si scarica.', 'The loop only runs while compiling, but the result is downloaded.')),
        ], task('scss', L('Con `@for $i from 1 through 4` genera le classi `.p-1`…`.p-4` con `padding: $i * 4px`.', 'With `@for $i from 1 through 4` generate the classes `.p-1`…`.p-4` with `padding: $i * 4px`.'), '', [re('@for\s+\$i\s+from\s+1\s+through\s+4', L('Il ciclo va da 1 a 4', 'The loop goes from 1 to 4')), re('\.p-#\{\s*\$i\s*\}', L('Il nome usa l’interpolazione', 'The name uses interpolation')), re('padding\s*:\s*(\$i\s*\*\s*4px|4px\s*\*\s*\$i)', L('Il padding cresce di 4px', 'The padding grows by 4px'))], '', "@for \$i from 1 through 4 {\n  .p-#{\$i} { padding: \$i * 4px; }\n}"), [
            dMatch(pMatch(), [['@if', L('Applica una condizione', 'Applies a condition')], ['@else', L('Il caso contrario', 'The opposite case')], ['from 1 through 3', L('1, 2 e 3', '1, 2 and 3')], ['from 1 to 3', L('1 e 2', '1 and 2')], ['@while', L('Ripete finché è vero', 'Repeats while true')]], L('La logica gira nel compilatore, non nel browser.', 'The logic runs in the compiler, not in the browser.')),
            dBug(pBug(), ['@for $i from 1 through 3 {', '  .mt-$i { margin-top: $i * 4px; }', '}'], 1, '  .mt-#{$i} { margin-top: $i * 4px; }', L('In un selettore la variabile va interpolata con `#{}`.', 'In a selector the variable must be interpolated with `#{}`.')),
        ]),
        lesson('forward-config', L('@forward e configurazione', '@forward and configuration'), [
            P('`!default` assegna un valore solo se la variabile non ne ha già uno: chi usa il tuo modulo può configurarlo con `@use … with (…)`. `@forward` ripubblica i membri di un altro modulo: un file `_index.scss` rende caricabile un’intera cartella con un solo `@use`.', '`!default` assigns a value only if the variable does not have one yet: whoever uses your module can configure it with `@use … with (…)`. `@forward` republishes another module’s members: an `_index.scss` file makes a whole folder loadable with a single `@use`.'),
            C('// stile/_variabili.scss
$brand: #E8590C !default;
$raggio: 8px !default;

// stile/_index.scss
@forward "variabili";
@forward "bottoni";

// main.scss
@use "stile" with ($brand: #2B8A3E);

.card { border-radius: stile.$raggio; }', 'scss'),
            P('Con `as` cambi lo spazio dei nomi: `@use "stile" as s` si legge `s.$raggio`, `as *` lo toglie del tutto (comodo, ma rischi nomi in conflitto). Così si costruiscono librerie configurabili senza toccarne il codice.', 'With `as` you change the namespace: `@use "stile" as s` is read as `s.$raggio`, `as *` removes it altogether (handy, but you risk clashing names). That is how configurable libraries are built without touching their code.'),
        ], [
            Q('f1', L('Che cosa fa `!default`?', 'What does `!default` do?'), [L('Rende la variabile costante', 'It makes the variable constant'), L('Assegna il valore solo se la variabile non ne ha già uno', 'It assigns the value only if the variable has none yet'), L('Esporta la variabile nel CSS', 'It exports the variable to the CSS'), L('Cancella la variabile', 'It deletes the variable')], 1, L('Così il valore si può configurare da fuori.', 'So the value can be configured from outside.')),
            Q('f2', L('A che cosa serve `@forward`?', 'What is `@forward` for?'), [L('A ripubblicare i membri di un altro modulo', 'To republish another module’s members'), L('A spostare il CSS in fondo al file', 'To move the CSS to the end of the file'), L('A compilare più in fretta', 'To compile faster'), L('A importare un file CSS normale', 'To import a plain CSS file')], 0, L('Chi carica il modulo vede anche ciò che inoltra.', 'Whoever loads the module also sees what it forwards.')),
            Q('f3', L('Che cosa fa `@use "colori" as *`?', 'What does `@use "colori" as *` do?'), [L('Carica tutti i file della cartella', 'It loads every file in the folder'), L('Rinomina il file in `*`', 'It renames the file to `*`'), L('Carica il modulo due volte', 'It loads the module twice'), L('Usa i membri senza spazio dei nomi', 'It uses the members with no namespace')], 3, L('Scrivi `$brand` invece di `colori.$brand`.', 'You write `$brand` instead of `colori.$brand`.')),
        ], task('scss', L('In `_variabili.scss` dai a `$brand` il valore predefinito `#E8590C` con `!default`; in `main.scss` carica `"variabili"` configurando `$brand: green` con `with`.', 'In `_variabili.scss` give `$brand` the default value `#E8590C` with `!default`; in `main.scss` load `"variabili"` setting `$brand: green` with `with`.'), "// _variabili.scss\n\n// main.scss\n", [re('\$brand\s*:\s*#e8590c\s*!default', L('Il valore è predefinito', 'The value is a default')), re('@use\s+["\']variabili["\']\s+with\s*\(\s*\$brand\s*:\s*green\s*\)', L('`main.scss` lo configura', '`main.scss` configures it'))], '', "// _variabili.scss\n\$brand: #E8590C !default;\n\n// main.scss\n@use \"variabili\" with (\$brand: green);"), [
            dMatch(pMatch(), [['!default', L('Valore configurabile', 'Configurable value')], ['@forward', L('Ripubblica un modulo', 'Republishes a module')], ['with (…)', L('Configura al caricamento', 'Configures on loading')], ['as *', L('Niente spazio dei nomi', 'No namespace')], ['_index.scss', L('Il file di una cartella', 'A folder’s entry file')]], L('Gli attrezzi per costruire una libreria SCSS.', 'The tools for building an SCSS library.')),
            dFlash(pFlash(), [[L('`with` funziona solo con variabili dichiarate con `!default`', '`with` only works on variables declared with `!default`'), true], [L('`@forward` rende i membri visibili dentro il file che lo scrive', '`@forward` makes the members visible inside the file that writes it'), false], [L('`as s` permette di scrivere `s.$raggio`', '`as s` lets you write `s.$raggio`'), true], [L('`@import` è il modo consigliato per caricare i moduli', '`@import` is the recommended way to load modules'), false]], L('`@forward` inoltra ad altri: per usare i membri nel file stesso serve anche `@use`.', '`@forward` passes members on: to use them in the file itself you also need `@use`.')),
        ]),
    ],
    'sass' => [
        lesson('maps-sass', L('Variabili e mappe in Sass', 'Variables and maps in Sass'), [
            P('Nella sintassi indentata variabili e mappe funzionano come in SCSS: cambiano solo graffe e punti e virgola.', 'In the indented syntax variables and maps work as in SCSS: only braces and semicolons change.'),
            C('$temi: (ok: green, ko: red)

@each $nome, $tinta in $temi
  .avviso-#{$nome}
    color: $tinta', 'sass'),
            P('Nella sintassi indentata andare a capo chiude l’istruzione. Dentro le parentesi, però, una mappa lunga può andare su più righe (da Dart Sass 1.84).', 'In the indented syntax a line break ends the statement. Inside brackets, though, a long map can span several lines (since Dart Sass 1.84).'),
        ], [
            Q('v1', L('Come inizia una variabile Sass?', 'How does a Sass variable start?'), ['$', '@', '--', '#'], 0, L('Identica a SCSS.', 'Same as SCSS.')),
            Q('v2', L('`@each` funziona in Sass?', 'Does `@each` work in Sass?'), [L('Sì', 'Yes'), L('Solo in SCSS', 'Only in SCSS'), L('No', 'No'), L('Solo in CSS', 'Only in CSS')], 0, L('Le funzioni sono le stesse.', 'The features are the same.')),
            Q('v3', L('Che cosa segna il blocco di `@each`?', 'What marks the `@each` block?'), [L('Il rientro', 'The indent'), L('Le graffe', 'The braces'), L('Il punto e virgola', 'The semicolon'), L('Le parentesi', 'The brackets')], 0, L('In Sass il rientro sostituisce le graffe.', 'In Sass indentation replaces braces.')),
        ], task('sass', L('Definisci `$brand: orange` e usalo come `color` di `.titolo`.', 'Define `$brand: orange` and use it as the `color` of `.titolo`.'), '', [re('^\$brand:\s*orange', L('Definisci `$brand`', 'You define `$brand`')), re('^\s+color:\s*\$brand\s*;?\s*$', L('Lo usi come `color`, rientrato', 'You use it as the indented `color`'))], '', "\$brand: orange\n.titolo\n  color: \$brand"), [
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
        lesson('logic-sass', L('Funzioni e condizioni in Sass', 'Functions and conditions in Sass'), [
            P('Funzioni, condizioni e cicli funzionano come in SCSS: si scrivono con le stesse parole chiave, ma il blocco è segnato dal rientro. `@else` va allo stesso livello di rientro del suo `@if`.', 'Functions, conditions and loops work as in SCSS: they use the same keywords, but the block is marked by indentation. `@else` sits at the same indent level as its `@if`.'),
            C('@function spazio($n)
  @return $n * 8px

=tema($scuro: false)
  @if $scuro
    background: #16161d
    color: white
  @else
    background: white

.pannello
  +tema($scuro: true)
  padding: spazio(2)

@for $i from 1 through 2
  .mt-#{$i}
    margin-top: $i * 4px', 'sass'),
            P('Il risultato è lo stesso CSS che otterresti in SCSS: `.pannello` con sfondo scuro e `padding: 16px`, poi `.mt-1` e `.mt-2`. Cambia solo la forma, non la logica.', 'The result is the same CSS you would get from SCSS: `.pannello` with a dark background and `padding: 16px`, then `.mt-1` and `.mt-2`. Only the form changes, not the logic.'),
        ], [
            Q('l1', L('A quale livello di rientro va `@else` in Sass?', 'At which indent level does `@else` go in Sass?'), [L('Un livello più a destra del suo `@if`', 'One level further right than its `@if`'), L('Sempre a inizio riga', 'Always at the start of the line'), L('Allo stesso livello del suo `@if`', 'At the same level as its `@if`'), L('Dentro il blocco dell’`@if`', 'Inside the `@if` block')], 2, L('Come `if` ed `else` in Python.', 'Like `if` and `else` in Python.')),
            Q('l2', L('Come restituisce un valore una funzione Sass?', 'How does a Sass function give back a value?'), ['=', '+', '@return', '@content'], 2, L('`=` e `+` sono le scorciatoie dei mixin, non delle funzioni.', '`=` and `+` are mixin shortcuts, not function ones.')),
            Q('l3', L('Il CSS prodotto da queste regole in Sass e in SCSS è diverso?', 'Is the CSS produced by these rules in Sass and in SCSS different?'), [L('Sì, Sass non supporta i cicli', 'Yes, Sass does not support loops'), L('Sì, Sass non ha le funzioni', 'Yes, Sass has no functions'), L('Solo nelle condizioni', 'Only in conditions'), L('No, è identico', 'No, it is identical')], 3, L('Le due sintassi condividono tutto il linguaggio.', 'The two syntaxes share the whole language.')),
        ], task('sass', L('Scrivi in Sass la funzione `doppio($n)` che restituisce `$n * 2` e usala come `width: doppio(10px)` in `.box`.', 'Write in Sass the function `doppio($n)` that gives back `$n * 2` and use it as `width: doppio(10px)` in `.box`.'), '', [re('^@function\s+doppio\(\$n\)\s*$', L('La funzione è definita, senza graffa', 'The function is defined, with no brace')), re('^[ \t]+@return\s+\$n\s*\*\s*2\s*;?\s*$', L('Il `@return` è rientrato', 'The `@return` is indented')), re('^\.box\s*\n[ \t]+width:\s*doppio\(10px\)', L('`.box` usa la funzione', '`.box` uses the function'))], '', "@function doppio(\$n)\n  @return \$n * 2\n\n.box\n  width: doppio(10px)"), [
            dOrder(pSort(), ['=tema($scuro)', '  @if $scuro', '    color: white', '  @else', '    color: black'], L('`@if` ed `@else` allo stesso livello, ciascuno con il suo blocco rientrato.', '`@if` and `@else` at the same level, each with its indented block.')),
            dBug(pBug(), ['@function spazio($n)', '  @return $n * 8px', '.card', '  padding: +spazio(2)'], 3, '  padding: spazio(2)', L('Una funzione si chiama per nome; `+` serve a includere i mixin.', 'A function is called by name; `+` is for including mixins.')),
        ]),
        lesson('multiline', L('Righe lunghe e commenti in Sass', 'Long lines and comments in Sass'), [
            P('Nella sintassi indentata un a capo chiude l’istruzione. Da Dart Sass 1.84, però, un’istruzione può continuare sulla riga dopo se l’a capo cade dove non potrebbe finire, per esempio dentro parentesi aperte. Le versioni recenti accettano anche un `;` finale, che resta facoltativo.', 'In the indented syntax a line break ends the statement. Since Dart Sass 1.84, though, a statement can carry on to the next line if the break falls where it could not end, for example inside open brackets. Recent versions also accept a trailing `;`, which stays optional.'),
            C('$temi: (
  ok: green,
  ko: red
)

.griglia
  grid-template-columns: repeat(auto-fill,
    minmax(200px, 1fr))

// commento di riga: non arriva nel CSS
/* commento di blocco: arriva nel CSS */', 'sass'),
            P('I commenti `//` spariscono nella compilazione, quelli `/* */` restano nel CSS (salvo con `--style=compressed`). Le graffe invece non sono ammesse: un blocco si apre sempre con il rientro.', '`//` comments disappear when compiling, `/* */` ones stay in the CSS (unless you use `--style=compressed`). Braces, however, are not allowed: a block always opens with indentation.'),
            Tip('Le righe su più linee sono una novità: con strumenti o compilatori vecchi potrebbero non funzionare.', 'Statements on several lines are new: they may not work with old tools or compilers.'),
        ], [
            Q('m1', L('Dove può andare a capo un’istruzione senza chiudersi?', 'Where can a statement break the line without ending?'), [L('Dentro parentesi ancora aperte', 'Inside brackets that are still open'), L('Ovunque, il rientro non conta più', 'Anywhere, indentation no longer matters'), L('Solo dopo un punto e virgola', 'Only after a semicolon'), L('Da nessuna parte', 'Nowhere')], 0, L('Lì l’istruzione non potrebbe finire, quindi continua.', 'There the statement could not end, so it carries on.')),
            Q('m2', L('Quale commento non arriva nel CSS compilato?', 'Which comment does not reach the compiled CSS?'), ['/* nota */', '/*! nota */', '// nota', '<!-- nota -->'], 2, L('`//` esiste solo nel sorgente.', '`//` only exists in the source.')),
            Q('m3', L('Le graffe sono ammesse nella sintassi indentata?', 'Are braces allowed in the indented syntax?'), [L('Sì, come in SCSS', 'Yes, as in SCSS'), L('Sì, ma solo per i mixin', 'Yes, but only for mixins'), L('No, i blocchi si aprono con il rientro', 'No, blocks open with indentation'), L('Solo nelle versioni recenti', 'Only in recent versions')], 2, L('Le graffe compaiono solo nell’interpolazione `#{}`.', 'Braces only appear in `#{}` interpolation.')),
        ], task('sass', L('Scrivi la mappa `$temi` con le voci `ok: green` e `ko: red` su più righe, poi un `@each` che crei `.avviso-#{$nome}` con `color: $tinta`.', 'Write the `$temi` map with the entries `ok: green` and `ko: red` over several lines, then an `@each` that creates `.avviso-#{$nome}` with `color: $tinta`.'), '', [re('^\$temi:\s*\(\s*\n\s*ok:\s*green,\s*\n\s*ko:\s*red\s*\n?\s*\)', L('La mappa va su più righe', 'The map spans several lines')), re('^@each\s+\$nome,\s*\$tinta\s+in\s+\$temi\s*$', L('C’è il ciclo, senza graffa', 'The loop is there, with no brace')), re('^[ \t]+\.avviso-#\{\$nome\}\s*\n[ \t]+color:\s*\$tinta', L('Il ciclo crea le classi', 'The loop creates the classes'))], '', "\$temi: (\n  ok: green,\n  ko: red\n)\n\n@each \$nome, \$tinta in \$temi\n  .avviso-#{\$nome}\n    color: \$tinta"), [
            dMatch(pMatch(), [['// …', L('Commento che sparisce', 'Comment that disappears')], ['/* … */', L('Commento che resta nel CSS', 'Comment kept in the CSS')], [L('A capo dentro ( )', 'Line break inside ( )'), L('L’istruzione continua', 'The statement carries on')], [L('A capo fuori dalle parentesi', 'Line break outside brackets'), L('L’istruzione finisce', 'The statement ends')]], L('L’a capo ha un significato, ma non dentro le parentesi.', 'Line breaks mean something, but not inside brackets.')),
            dFlash(pFlash(), [[L('Da Dart Sass 1.84 una mappa può stare su più righe', 'Since Dart Sass 1.84 a map can span several lines'), true], [L('In Sass i commenti `//` finiscono nel CSS', 'In Sass `//` comments end up in the CSS'), false], [L('Un `;` finale oggi è tollerato', 'A trailing `;` is tolerated today'), true], [L('In Sass un blocco si può aprire con `{`', 'In Sass a block can open with `{`'), false]], L('Più comodità, stesse regole di fondo.', 'More comfort, the same basic rules.')),
        ]),
    ],
];
