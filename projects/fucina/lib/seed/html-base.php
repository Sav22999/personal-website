<?php
/** HTML base: from the first tag to a simple form. */

return [
    'id' => 'html-base', 'family' => 'html',
    'title' => L('HTML base', 'HTML Basics'), 'short' => L('HTML base', 'HTML Basics'), 'level' => L('Base', 'Basic'),
    'desc' => L('Il linguaggio con cui è fatta ogni pagina web: tag, testi, link, immagini, liste, tabelle e moduli.', 'The language every web page is made of: tags, text, links, images, lists, tables and forms.'),
    'lessons' => [
        lesson('structure', L('La struttura di una pagina', 'The structure of a page'), [
            P('HTML descrive il **contenuto** di una pagina usando i `tag`: parole tra parentesi angolari. Quasi tutti si aprono e si chiudono, e quello che sta in mezzo è il contenuto dell’elemento.', 'HTML describes the **content** of a page using `tags`: words between angle brackets. Most of them open and close, and what sits in between is the content of the element.'),
            C('<!doctype html>
<html lang="it">
  <head>
    <meta charset="utf-8">
    <title>La mia pagina</title>
  </head>
  <body>
    <h1>Ciao mondo</h1>
  </body>
</html>'),
            P('`<!doctype html>` dice al browser che è una pagina moderna. Dentro `<head>` vanno le informazioni sulla pagina, che non si vedono nella pagina stessa. Dentro `<body>` va tutto ciò che si vede.', '`<!doctype html>` tells the browser this is a modern page. `<head>` holds information about the page, which is not shown on the page itself. `<body>` holds everything that is visible.'),
            Tip('Il contenuto di `<title>` è il testo che compare nella scheda del browser.', 'The content of `<title>` is the text shown on the browser tab.'),
        ], [
            Q('s1', L('Dove va il contenuto visibile della pagina?', 'Where does the visible content of the page go?'), ['<head>', '<body>', '<title>', '<meta>'], 1, L('Tutto ciò che si vede nella pagina sta dentro `<body>`.', 'Everything visible on the page sits inside `<body>`.')),
            Q('s2', L('A cosa serve `<!doctype html>`?', 'What is `<!doctype html>` for?'), [L('Crea un titolo', 'It creates a heading'), L('Dice al browser che è una pagina HTML moderna', 'It tells the browser this is a modern HTML page'), L('Collega un foglio di stile', 'It links a stylesheet'), L('Chiude il documento', 'It closes the document')], 1, L('Va sempre sulla prima riga: evita che il browser usi la vecchia modalità di compatibilità.', 'It always goes on the first line: it stops the browser using the old compatibility mode.')),
            Q('s3', L('Quale tag imposta il testo della scheda del browser?', 'Which tag sets the text of the browser tab?'), ['<h1>', '<header>', '<title>', '<tab>'], 2, L('`<title>` sta in `<head>` e dà il nome alla scheda.', '`<title>` sits in `<head>` and names the tab.')),
        ], task('html', L('Scrivi nel `body` un titolo `h1` con il testo **Ciao mondo**.', 'Write an `h1` heading in the `body` with the text **Ciao mondo**.'), '<!doctype html>
<html lang="it">
  <head>
    <title>Prima pagina</title>
  </head>
  <body>

  </body>
</html>', [has('h1', L('C’è un titolo `h1`', 'There is an `h1` heading')), has('h1', L('Il titolo dice «Ciao mondo»', 'The heading says “Ciao mondo”'), 'Ciao mondo')])),

        lesson('text', L('Titoli e paragrafi', 'Headings and paragraphs'), [
            P('I titoli vanno da `<h1>` a `<h6>`: `<h1>` è il più importante e di solito ce n’è uno per pagina. I paragrafi si scrivono con `<p>`.', 'Headings go from `<h1>` to `<h6>`: `<h1>` is the most important and there is usually one per page. Paragraphs use `<p>`.'),
            C('<h1>Ricette</h1>
<h2>Primi piatti</h2>
<p>La pasta si cuoce in acqua <strong>salata</strong>.</p>
<p>Il sugo va <em>assaggiato</em> spesso.</p>'),
            P('`<strong>` indica un testo importante e `<em>` un’enfasi: comunicano un significato, non solo un aspetto. `<br>` va a capo e non ha tag di chiusura.', '`<strong>` marks important text and `<em>` emphasis: they carry meaning, not just looks. `<br>` is a line break and has no closing tag.'),
            Tip('Scegli il titolo per la sua importanza, non per la sua grandezza: all’aspetto ci penserà il CSS.', 'Choose a heading for its importance, not its size: CSS will take care of the look.'),
        ], [
            Q('t1', L('Qual è il titolo più importante?', 'Which is the most important heading?'), ['<h6>', '<h1>', '<head>', '<p>'], 1, L('La numerazione va dal più importante, `h1`, al meno importante, `h6`.', 'Numbering goes from the most important, `h1`, to the least, `h6`.')),
            Q('t2', L('Quale tag indica un testo importante?', 'Which tag marks important text?'), ['<i>', '<strong>', '<big>', '<u>'], 1, L('`<strong>` dà importanza al testo, anche per chi usa uno screen reader.', '`<strong>` gives the text importance, also for screen reader users.')),
            Q('t3', L('Quale di questi tag non ha un tag di chiusura?', 'Which of these tags has no closing tag?'), ['<p>', '<em>', '<br>', '<h2>'], 2, L('`<br>` è un elemento vuoto: non ha contenuto e non si chiude.', '`<br>` is an empty element: it has no content and is not closed.')),
        ], task('html', L('Aggiungi un titolo `h2` e un paragrafo `p` che contenga una parola in `strong`.', 'Add an `h2` heading and a `p` paragraph containing a word in `strong`.'), '<h1>Il mio blog</h1>
', [has('h2', L('C’è un titolo `h2`', 'There is an `h2` heading')), has('p', L('C’è un paragrafo', 'There is a paragraph')), has('p strong', L('Il paragrafo contiene `strong`', 'The paragraph contains `strong`'))])),

        lesson('links', L('Link e immagini', 'Links and images'), [
            P('Gli **attributi** aggiungono informazioni a un tag e si scrivono nel tag di apertura, come `nome="valore"`. Un link è un tag `<a>` con l’attributo `href`.', '**Attributes** add information to a tag and go in the opening tag, as `name="value"`. A link is an `<a>` tag with the `href` attribute.'),
            C('<a href="https://example.com">Vai al sito</a>
<a href="contatti.html">Contatti</a>
<img src="gatto.jpg" alt="Un gatto rosso che dorme">'),
            P('`<img>` mostra un’immagine: `src` dice dove si trova il file, `alt` la descrive a chi non può vederla. Anche `<img>` non ha tag di chiusura.', '`<img>` shows an image: `src` says where the file is, `alt` describes it to whoever cannot see it. `<img>` has no closing tag either.'),
            Tip('Un `alt` utile descrive ciò che l’immagine comunica. Se è solo decorativa, scrivi `alt=""`.', 'A useful `alt` describes what the image conveys. If it is only decorative, write `alt=""`.'),
        ], [
            Q('l1', L('Quale attributo contiene la destinazione di un link?', 'Which attribute holds the destination of a link?'), ['src', 'href', 'link', 'to'], 1, L('`href` sta per «hypertext reference».', '`href` stands for “hypertext reference”.')),
            Q('l2', L('A cosa serve l’attributo `alt`?', 'What is the `alt` attribute for?'), [L('Allinea l’immagine', 'It aligns the image'), L('Descrive l’immagine a chi non può vederla', 'It describes the image to whoever cannot see it'), L('Indica l’altezza', 'It sets the height'), L('Apre un link', 'It opens a link')], 1, L('Lo leggono gli screen reader e compare se l’immagine non si carica.', 'Screen readers read it and it appears if the image fails to load.')),
            Q('l3', L('Quale riga è corretta?', 'Which line is correct?'), ['<a src="a.html">Vai</a>', '<img href="a.jpg">', '<a href="a.html">Vai</a>', '<link a="a.html">Vai</link>'], 2, L('I link usano `href`, le immagini `src`.', 'Links use `href`, images use `src`.')),
        ], task('html', L('Crea un link a `https://example.com` con il testo **Scopri** e un’immagine `logo.png` con un `alt`.', 'Create a link to `https://example.com` with the text **Scopri** and a `logo.png` image with an `alt`.'), '', [has('a[href="https://example.com"]', L('Il link punta a example.com', 'The link points to example.com')), has('a', L('Il testo del link è «Scopri»', 'The link text is “Scopri”'), 'Scopri'), has('img[src="logo.png"]', L('C’è l’immagine `logo.png`', 'There is the `logo.png` image')), has('img[alt]:not([alt=""])', L('L’immagine ha un `alt`', 'The image has an `alt`'))])),

        lesson('lists', L('Le liste', 'Lists'), [
            P('`<ul>` crea una lista puntata, `<ol>` una numerata. Ogni voce è un `<li>`, in entrambi i casi.', '`<ul>` creates a bulleted list, `<ol>` a numbered one. Each item is an `<li>`, in both cases.'),
            C('<ul>
  <li>Pane</li>
  <li>Latte</li>
</ul>

<ol>
  <li>Accendi il forno</li>
  <li>Inforna</li>
</ol>'),
            P('Una lista può stare dentro un’altra: la lista interna va **dentro** un `<li>`. Usa `<ol>` quando l’ordine conta.', 'A list can sit inside another: the inner list goes **inside** an `<li>`. Use `<ol>` when the order matters.'),
        ], [
            Q('i1', L('Quale tag crea una lista numerata?', 'Which tag creates a numbered list?'), ['<ul>', '<ol>', '<li>', '<nl>'], 1, L('`ol` sta per «ordered list».', '`ol` stands for “ordered list”.')),
            Q('i2', L('Con quale tag si scrive ogni voce?', 'Which tag is used for each item?'), ['<item>', '<li>', '<ul>', '<dd>'], 1, L('`<li>` vale sia per `ul` sia per `ol`.', '`<li>` works for both `ul` and `ol`.')),
            Q('i3', L('Dove va una lista annidata?', 'Where does a nested list go?'), [L('Dentro un `<li>`', 'Inside an `<li>`'), L('Tra due `<li>`', 'Between two `<li>`'), L('Fuori dalla lista', 'Outside the list'), L('Dentro `<head>`', 'Inside `<head>`')], 0, L('Figli diretti di `ul` e `ol` possono essere solo gli `li`.', 'Only `li` elements can be direct children of `ul` and `ol`.')),
        ], task('html', L('Crea una lista numerata con tre passaggi.', 'Create a numbered list with three steps.'), '', [has('ol', L('C’è una lista numerata', 'There is a numbered list')), has('ol > li:nth-child(3)', L('Ha almeno tre voci', 'It has at least three items'))])),

        lesson('tables', L('Le tabelle', 'Tables'), [
            P('Le tabelle servono a mostrare **dati**, non a impaginare. `<table>` contiene righe `<tr>`, che contengono celle: `<th>` per le intestazioni e `<td>` per i dati.', 'Tables are for showing **data**, not for page layout. `<table>` contains `<tr>` rows, which contain cells: `<th>` for headers and `<td>` for data.'),
            C('<table>
  <thead>
    <tr><th>Città</th><th>Abitanti</th></tr>
  </thead>
  <tbody>
    <tr><td>Udine</td><td>97 000</td></tr>
  </tbody>
</table>'),
            P('`<thead>` e `<tbody>` separano intestazione e dati. `colspan="2"` fa occupare a una cella due colonne, `rowspan="2"` due righe.', '`<thead>` and `<tbody>` separate header and data. `colspan="2"` makes a cell span two columns, `rowspan="2"` two rows.'),
        ], [
            Q('b1', L('Quale tag crea una riga?', 'Which tag creates a row?'), ['<td>', '<th>', '<tr>', '<row>'], 2, L('`tr` sta per «table row».', '`tr` stands for “table row”.')),
            Q('b2', L('Quale tag crea una cella di intestazione?', 'Which tag creates a header cell?'), ['<th>', '<td>', '<thead>', '<header>'], 0, L('`<th>` è la cella, `<thead>` è il gruppo delle righe di intestazione.', '`<th>` is the cell, `<thead>` the group of header rows.')),
            Q('b3', L('Come fai occupare a una cella due colonne?', 'How do you make a cell span two columns?'), ['rowspan="2"', 'colspan="2"', 'width="2"', 'cols="2"'], 1, L('`colspan` unisce colonne, `rowspan` unisce righe.', '`colspan` merges columns, `rowspan` merges rows.')),
        ], task('html', L('Crea una tabella con una riga di intestazione (`th`) e una riga di dati (`td`).', 'Create a table with a header row (`th`) and a data row (`td`).'), '<table>

</table>', [has('table tr th', L('C’è una cella `th`', 'There is a `th` cell')), has('table tr td', L('C’è una cella `td`', 'There is a `td` cell')), has('table tr:nth-of-type(1)', L('Le celle stanno in una riga `tr`', 'The cells sit in a `tr` row'))])),

        lesson('forms', L('I primi moduli', 'First forms'), [
            P('`<form>` raccoglie i dati dell’utente. Ogni campo `<input>` ha un `type` e un `name`, il nome con cui il dato viene inviato.', '`<form>` collects data from the user. Each `<input>` field has a `type` and a `name`, the name the data is sent with.'),
            C('<form action="/iscriviti" method="post">
  <label for="mail">Email</label>
  <input id="mail" name="mail" type="email" required>
  <button type="submit">Iscriviti</button>
</form>'),
            P('`<label>` è collegata al campo tramite `for` e `id`: toccando l’etichetta si attiva il campo. `required` rende il campo obbligatorio e `type="email"` fa controllare al browser il formato.', '`<label>` is tied to the field through `for` and `id`: tapping the label activates the field. `required` makes the field mandatory and `type="email"` has the browser check the format.'),
            Tip('Ogni campo deve avere una `label`: un segnaposto `placeholder` non basta, perché sparisce quando si scrive.', 'Every field needs a `label`: a `placeholder` is not enough, because it disappears while typing.'),
        ], [
            Q('f1', L('Come si collega una `label` a un campo?', 'How do you tie a `label` to a field?'), [L('Con `for` uguale all’`id` del campo', 'With `for` equal to the field’s `id`'), L('Con `name` uguale a `class`', 'With `name` equal to `class`'), L('Con `href`', 'With `href`'), L('Mettendola subito prima', 'By placing it right before')], 0, L('`for` e `id` devono avere lo stesso valore.', '`for` and `id` must have the same value.')),
            Q('f2', L('Quale attributo rende obbligatorio un campo?', 'Which attribute makes a field mandatory?'), ['needed', 'mandatory', 'required', 'must'], 2, L('Con `required` il browser blocca l’invio se il campo è vuoto.', 'With `required` the browser blocks sending if the field is empty.')),
            Q('f3', L('A cosa serve `name` in un campo?', 'What is `name` for in a field?'), [L('È il testo mostrato', 'It is the text shown'), L('È il nome con cui il dato viene inviato', 'It is the name the data is sent with'), L('È il suo stile', 'It is its style'), L('Dà un titolo alla pagina', 'It titles the page')], 1, L('Senza `name` il valore del campo non viene inviato.', 'Without `name` the value of the field is not sent.')),
        ], task('html', L('Completa il modulo: un campo `email` obbligatorio con la sua `label` e un pulsante di invio.', 'Complete the form: a required `email` field with its `label` and a submit button.'), '<form>

</form>', [has('form input[type="email"]', L('C’è un campo email', 'There is an email field')), has('form input[required]', L('Il campo è obbligatorio', 'The field is required')), has('form label[for]', L('C’è una `label` con `for`', 'There is a `label` with `for`')), has('form button, form input[type="submit"]', L('C’è un pulsante', 'There is a button'))])),
    ],
];
