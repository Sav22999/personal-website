<?php
/** HTML avanzato: semantics, rich forms, media, accessibility and metadata; data attributes, SEO, popovers and dialogs, loading resources and responsive images are in extra-levels.php. */

/** The regions of a semantic page as a wireframe. $t: what each region holds. */
if (!function_exists('fig_htmladv_layout')) {
    function fig_htmladv_layout(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 334" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<g stroke-width="2">
<rect x="20" y="14" width="600" height="44" rx="10" fill="#ECE6FF" stroke="#7048E8"/>
<rect x="20" y="66" width="600" height="34" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/>
<rect x="20" y="108" width="600" height="162" rx="12" fill="#FFFFFF" stroke="#1E1C52" stroke-dasharray="7 5"/>
<rect x="34" y="136" width="400" height="122" rx="10" fill="#FFF3EA" stroke="#E8590C"/>
<rect x="50" y="184" width="368" height="62" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-dasharray="5 4"/>
<rect x="446" y="136" width="160" height="122" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="20" y="278" width="600" height="42" rx="10" fill="#F1F0F8" stroke="#8A87B0"/>
</g>
<g font-family="ui-monospace, Menlo, Consolas, monospace" font-size="15" font-weight="700">
<text x="36" y="42" fill="#5B37D0">&lt;header&gt;</text><text x="36" y="89" fill="#1B66BC">&lt;nav&gt;</text>
<text x="604" y="128" text-anchor="end" font-size="13">&lt;main&gt;</text>
<text x="48" y="160" fill="#B84508">&lt;article&gt;</text><text x="64" y="208" fill="#B84508" font-size="14">&lt;section&gt;</text>
<text x="460" y="174" fill="#0B7F5C">&lt;aside&gt;</text><text x="36" y="305" fill="#55527A">&lt;footer&gt;</text>
</g>
<g fill="#55527A"><text x="150" y="42">{$t[0]}</text><text x="110" y="89">{$t[1]}</text><text x="160" y="160">{$t[2]}</text><text x="64" y="230">{$t[3]}</text><text x="460" y="196">{$t[4]}</text><text x="460" y="214">{$t[5]}</text><text x="130" y="305">{$t[6]}</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'html-advanced', 'family' => 'html', 'after' => 'html-intermediate',
    'title' => L('HTML avanzato', 'Advanced HTML'), 'short' => L('HTML avanzato', 'Advanced HTML'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Pagine semantiche e accessibili: struttura, moduli ricchi, contenuti multimediali, metadati e SEO, popover e finestre di dialogo, caricamento delle risorse e immagini responsive.', 'Semantic, accessible pages: structure, rich forms, media, metadata and SEO, popovers and dialogs, resource loading and responsive images.'),
    'lessons' => [
        lesson('semantic', L('HTML semantico', 'Semantic HTML'), [
            P('I tag semantici dicono **che cosa è** un blocco, non come appare. Motori di ricerca e screen reader li usano per capire la pagina.', 'Semantic tags say **what a block is**, not how it looks. Search engines and screen readers use them to understand the page.'),
            C(L('<header>Logo e titolo</header>
<nav>Menu principale</nav>
<main>
  <article>
    <h1>Una notizia</h1>
    <section>Una parte della notizia</section>
  </article>
  <aside>Articoli correlati</aside>
</main>
<footer>Contatti</footer>', '<header>Logo and title</header>
<nav>Main menu</nav>
<main>
  <article>
    <h1>A news story</h1>
    <section>A part of the story</section>
  </article>
  <aside>Related articles</aside>
</main>
<footer>Contacts</footer>')),
            Fig(L(fig_htmladv_layout(['logo e titolo del sito', 'il menu principale', 'un contenuto che sta in piedi da solo', 'una parte tematica, con il suo titolo', 'contenuti', 'collegati', 'contatti, note legali']), fig_htmladv_layout(['site logo and title', 'the main menu', 'content that stands on its own', 'a thematic part, with its own heading', 'related', 'content', 'contacts, legal notes'])),
                L('Lo schema di una pagina: in alto `header` e `nav`; al centro `main`, che contiene un `article` con una `section` dentro e, a fianco, un `aside`; in fondo il `footer`.', 'The outline of a page: `header` and `nav` at the top; in the middle `main`, holding an `article` with a `section` inside and, beside it, an `aside`; the `footer` at the bottom.'),
                L('Lo stesso schema che vedi a occhio, scritto nel codice: chi non vede la pagina lo può percorrere a salti.', 'The same outline you see by eye, written into the code: someone who cannot see the page can jump through it.')),
            P('`<main>` è il contenuto principale e ce n’è uno solo per pagina. `<article>` è un contenuto che ha senso da solo; `<section>` è una parte tematica, di solito con un titolo. `<div>` non ha significato: usalo solo per raggruppare a fini di stile.', '`<main>` is the main content and there is only one per page. `<article>` is content that stands on its own; `<section>` is a thematic part, usually with a heading. `<div>` has no meaning: use it only to group for styling.'),
        ], [
            Q('m1', L('Quanti `<main>` visibili può avere una pagina?', 'How many visible `<main>` can a page have?'), [L('Uno', 'One'), L('Due', 'Two'), L('Uno per sezione', 'One per section'), L('Quanti ne servono', 'As many as needed')], 0, L('`<main>` indica il contenuto principale, che è uno solo.', '`<main>` marks the main content, of which there is only one.')),
            Q('m2', L('Quale tag è adatto a un post che ha senso anche da solo?', 'Which tag suits a post that makes sense on its own?'), ['<div>', '<article>', '<aside>', '<nav>'], 1, L('`<article>` è un contenuto autonomo e riutilizzabile.', '`<article>` is self-contained, reusable content.')),
            Q('m3', L('Quale tag contiene i collegamenti di navigazione principali?', 'Which tag holds the main navigation links?'), ['<menu-bar>', '<links>', '<nav>', '<header>'], 2, L('`<nav>` identifica i blocchi di navigazione.', '`<nav>` identifies navigation blocks.')),
        ], task('html', L('Struttura la pagina con `header`, `main` contenente un `article`, e `footer`.', 'Structure the page with `header`, `main` containing an `article`, and `footer`.'), '<body>

</body>', [has('header', L('C’è un `header`', 'There is a `header`')), has('main article', L('Il `main` contiene un `article`', '`main` contains an `article`')), has('footer', L('C’è un `footer`', 'There is a `footer`'))])),

        lesson('rich-forms', L('Moduli ricchi e validazione', 'Rich forms and validation'), [
            P('Il `type` giusto mostra la tastiera adatta e dà controlli gratuiti: `email`, `url`, `tel`, `number`, `date`, `range`, `color`.', 'The right `type` shows the suitable keyboard and gives free checks: `email`, `url`, `tel`, `number`, `date`, `range`, `color`.'),
            C(L('<fieldset>
  <legend>Taglia</legend>
  <label><input type="radio" name="taglia" value="s"> S</label>
  <label><input type="radio" name="taglia" value="m"> M</label>
</fieldset>

<label for="eta">Età</label>
<input id="eta" type="number" min="18" max="99">

<label for="cap">CAP</label>
<input id="cap" pattern="[0-9]{5}" title="5 cifre">

<select name="paese">
  <option value="it">Italia</option>
</select>', '<fieldset>
  <legend>Size</legend>
  <label><input type="radio" name="taglia" value="s"> S</label>
  <label><input type="radio" name="taglia" value="m"> M</label>
</fieldset>

<label for="eta">Age</label>
<input id="eta" type="number" min="18" max="99">

<label for="cap">Postcode</label>
<input id="cap" pattern="[0-9]{5}" title="5 digits">

<select name="paese">
  <option value="it">Italy</option>
</select>')),
            P('`<fieldset>` e `<legend>` raggruppano campi legati tra loro. I `radio` con lo stesso `name` si escludono a vicenda. `min`, `max` e `pattern` validano il dato nel browser.', '`<fieldset>` and `<legend>` group related fields. `radio` inputs with the same `name` exclude one another. `min`, `max` and `pattern` validate the value in the browser.'),
            Tip('La validazione del browser aiuta l’utente, ma il server deve sempre ricontrollare i dati.', 'Browser validation helps the user, but the server must always check the data again.'),
        ], [
            Q('r1', L('Perché due `radio` si escludano a vicenda devono avere…', 'For two `radio` inputs to exclude one another they must have…'), [L('Lo stesso `id`', 'The same `id`'), L('Lo stesso `name`', 'The same `name`'), L('Lo stesso `value`', 'The same `value`'), L('La stessa `label`', 'The same `label`')], 1, L('Lo stesso `name` li rende un unico gruppo.', 'The same `name` makes them a single group.')),
            Q('r2', L('Quale attributo valida un campo con un’espressione regolare?', 'Which attribute validates a field with a regular expression?'), ['regex', 'pattern', 'match', 'validate'], 1, L('`pattern` contiene l’espressione che il valore deve rispettare.', '`pattern` holds the expression the value must match.')),
            Q('r3', L('Quale coppia dà un titolo a un gruppo di campi?', 'Which pair gives a title to a group of fields?'), ['<group> <title>', '<fieldset> <legend>', '<form> <label>', '<section> <h6>'], 1, L('`<legend>` è la didascalia del `<fieldset>`.', '`<legend>` is the caption of the `<fieldset>`.')),
        ], task('html', L('Crea un `fieldset` con `legend` e due `radio` con lo stesso `name` «piano».', 'Create a `fieldset` with a `legend` and two `radio` inputs with the same `name` “piano”.'), '<form>

</form>', [has('fieldset legend', L('Il `fieldset` ha una `legend`', 'The `fieldset` has a `legend`')), has('fieldset input[type="radio"][name="piano"]', L('C’è un `radio` «piano»', 'There is a “piano” `radio`')), has('fieldset input[type="radio"][name="piano"] ~ input[type="radio"][name="piano"], fieldset label ~ label input[type="radio"][name="piano"]', L('Ce ne sono due', 'There are two of them'))])),

        lesson('media', L('Immagini responsive, audio e video', 'Responsive images, audio and video'), [
            P('`<picture>` e `srcset` fanno scegliere al browser il file più adatto allo schermo: si scaricano meno dati sui telefoni.', '`<picture>` and `srcset` let the browser choose the best file for the screen: phones download less data.'),
            C(L('<picture>
  <source srcset="foto.avif" type="image/avif">
  <img src="foto.jpg" alt="Il porto al tramonto"
       width="800" height="600" loading="lazy">
</picture>

<video src="corso.mp4" controls poster="anteprima.jpg">
  <track kind="captions" src="sottotitoli.vtt" srclang="it">
</video>', '<picture>
  <source srcset="photo.avif" type="image/avif">
  <img src="photo.jpg" alt="The harbour at sunset"
       width="800" height="600" loading="lazy">
</picture>

<video src="course.mp4" controls poster="preview.jpg">
  <track kind="captions" src="captions.vtt" srclang="en">
</video>')),
            P('`width` e `height` riservano lo spazio ed evitano che la pagina salti durante il caricamento. `loading="lazy"` rimanda le immagini lontane dallo schermo. In `<video>` e `<audio>` l’attributo `controls` mostra i comandi.', '`width` and `height` reserve the space and stop the page jumping while it loads. `loading="lazy"` delays images far from the screen. In `<video>` and `<audio>` the `controls` attribute shows the player buttons.'),
            P('Dentro `<picture>` il browser legge le `<source>` dall’alto e usa la prima che sa mostrare: per questo il formato più moderno e leggero (AVIF, WebP) va per primo. Il `<img>` finale non è un ripiego da trascurare: è quello che porta `alt`, misure e `loading`, e il browser lo usa se nessuna `source` va bene. Senza `controls` un video non ha comandi, e con `autoplay` deve anche essere `muted`, altrimenti i browser non lo fanno partire.', 'Inside `<picture>` the browser reads the `<source>` elements from the top and uses the first one it can show: that is why the most modern, lightest format (AVIF, WebP) goes first. The final `<img>` is not a fallback to neglect: it carries the `alt`, the sizes and `loading`, and the browser uses it if no `source` fits. Without `controls` a video has no buttons, and with `autoplay` it must also be `muted`, or browsers will not start it.'),
        ], [
            Q('d1', L('Quale attributo rimanda il caricamento di un’immagine?', 'Which attribute delays the loading of an image?'), ['defer', 'async', 'loading="lazy"', 'preload'], 2, L('Il browser la scarica quando sta per entrare nello schermo.', 'The browser downloads it when it is about to enter the screen.')),
            Q('d2', L('Perché indicare `width` e `height` su `<img>`?', 'Why set `width` and `height` on `<img>`?'), [L('Per comprimere il file', 'To compress the file'), L('Per riservare lo spazio ed evitare salti', 'To reserve the space and avoid jumps'), L('Per il SEO', 'For SEO'), L('Sono obbligatori', 'They are mandatory')], 1, L('Il browser conosce le proporzioni prima di scaricare il file.', 'The browser knows the proportions before downloading the file.')),
            Q('d3', L('Quale tag aggiunge i sottotitoli a un video?', 'Which tag adds subtitles to a video?'), ['<source>', '<caption>', '<track>', '<subtitle>'], 2, L('`<track kind="captions">` collega un file di sottotitoli.', '`<track kind="captions">` links a subtitle file.')),
        ], task('html', L('Inserisci un `video` con i `controls` e un’immagine con `loading="lazy"` e `alt`.', 'Add a `video` with `controls` and an image with `loading="lazy"` and `alt`.'), '', [has('video[controls]', L('Il video ha i comandi', 'The video has controls')), has('img[loading="lazy"]', L('L’immagine è lazy', 'The image is lazy')), has('img[alt]:not([alt=""])', L('L’immagine ha un `alt`', 'The image has an `alt`'))])),

        lesson('a11y', L('Accessibilità', 'Accessibility'), [
            P('Una pagina accessibile funziona per tutti, anche da tastiera e con uno screen reader. La prima regola è usare il tag giusto: un `<button>` è già raggiungibile e attivabile con la tastiera, un `<div>` no.', 'An accessible page works for everyone, keyboard and screen reader included. The first rule is using the right tag: a `<button>` can already be reached and activated with the keyboard, a `<div>` cannot.'),
            C(L('<button type="button" aria-label="Chiudi">✕</button>

<nav aria-label="Principale">…</nav>

<p id="aiuto">Almeno 8 caratteri</p>
<input type="password" aria-describedby="aiuto">', '<button type="button" aria-label="Close">✕</button>

<nav aria-label="Main">…</nav>

<p id="aiuto">At least 8 characters</p>
<input type="password" aria-describedby="aiuto">')),
            P('Gli attributi `aria-*` aggiungono informazioni dove l’HTML non basta: `aria-label` dà un nome a un elemento senza testo, `aria-describedby` collega una descrizione. Rispetta l’ordine dei titoli, senza saltare livelli.', '`aria-*` attributes add information where HTML is not enough: `aria-label` names an element with no text, `aria-describedby` links a description. Keep headings in order, without skipping levels.'),
            Tip('La regola d’oro di ARIA: se esiste un tag HTML adatto, usa quello.', 'The golden rule of ARIA: if a suitable HTML tag exists, use it.'),
        ], [
            Q('a1', L('Quale elemento funziona già da tastiera?', 'Which element already works with the keyboard?'), ['<div onclick>', '<span>', '<button>', '<p>'], 2, L('`<button>` riceve il focus e si attiva con Invio e Spazio.', '`<button>` gets focus and activates with Enter and Space.')),
            Q('a2', L('Come dai un nome a un pulsante con solo un’icona?', 'How do you name a button that only has an icon?'), ['title-text', 'aria-label', 'name', 'alt'], 1, L('`aria-label` è il nome che legge lo screen reader.', '`aria-label` is the name the screen reader reads.')),
            Q('a3', L('Qual è la prima regola di ARIA?', 'What is the first rule of ARIA?'), [L('Usarla ovunque', 'Use it everywhere'), L('Preferire i tag HTML adatti', 'Prefer suitable HTML tags'), L('Metterla solo nei `div`', 'Only put it on `div`s'), L('Sostituisce le `label`', 'It replaces `label`s')], 1, L('L’HTML nativo porta con sé ruolo, stato e tastiera.', 'Native HTML brings role, state and keyboard along.')),
        ], task('html', L('Sostituisci il `div` con un vero `button` e dagli un `aria-label` «Chiudi».', 'Replace the `div` with a real `button` and give it the `aria-label` “Chiudi”.'), '<div onclick="chiudi()">✕</div>', [has('button', L('C’è un `button`', 'There is a `button`')), has('button[aria-label="Chiudi"]', L('Ha l’`aria-label` giusto', 'It has the right `aria-label`'))])),

        lesson('meta', L('Metadati e tag interattivi', 'Metadata and interactive tags'), [
            P('In `<head>` i metadati spiegano la pagina agli altri programmi. `viewport` è indispensabile sui telefoni, `description` è il testo che i motori di ricerca possono mostrare, i tag Open Graph definiscono l’anteprima dei link condivisi.', 'In `<head>`, metadata explains the page to other programs. `viewport` is essential on phones, `description` is the text search engines can show, Open Graph tags define the preview of shared links.'),
            C('<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Corsi di HTML e CSS">
<meta property="og:title" content="Fucina">
<link rel="stylesheet" href="style.css">'),
            P('Alcuni tag sono interattivi senza JavaScript: `<details>` e `<summary>` creano una sezione apribile, `<dialog>` una finestra che un pulsante può aprire con gli attributi `commandfor` e `command`.', 'Some tags are interactive with no JavaScript: `<details>` and `<summary>` create a collapsible section, `<dialog>` a window a button can open with the `commandfor` and `command` attributes.'),
            C(L('<details>
  <summary>Come funziona?</summary>
  <p>Si apre con un tocco.</p>
</details>', '<details>
  <summary>How does it work?</summary>
  <p>It opens with a tap.</p>
</details>')),
        ], [
            Q('e1', L('Quale meta è indispensabile per i telefoni?', 'Which meta is essential for phones?'), ['charset', 'viewport', 'keywords', 'author'], 1, L('Senza `viewport` il telefono mostra la pagina come su un computer, rimpicciolita.', 'Without `viewport` the phone shows the page as on a computer, shrunk.')),
            Q('e2', L('Quali tag creano una sezione apribile senza JavaScript?', 'Which tags create a collapsible section with no JavaScript?'), ['<open> <close>', '<details> <summary>', '<toggle> <item>', '<section> <h2>'], 1, L('`<summary>` è la riga sempre visibile su cui si tocca.', '`<summary>` is the always-visible line you tap.')),
            Q('e3', L('Da dove vengono le anteprime dei link condivisi?', 'Where do the previews of shared links come from?'), [L('Dai meta Open Graph', 'From Open Graph metas'), L('Da `<title>` soltanto', 'From `<title>` alone'), L('Dal CSS', 'From CSS'), L('Da `<footer>`', 'From `<footer>`')], 0, L('Le app leggono `og:title`, `og:description` e `og:image`.', 'Apps read `og:title`, `og:description` and `og:image`.')),
        ], task('html', L('Crea un `details` con un `summary` e un paragrafo.', 'Create a `details` with a `summary` and a paragraph.'), '', [has('details summary', L('C’è un `summary` in `details`', 'There is a `summary` in `details`')), has('details p', L('C’è un paragrafo da aprire', 'There is a paragraph to reveal'))])),
    ],
];
