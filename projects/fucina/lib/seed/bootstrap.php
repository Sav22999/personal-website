<?php
/** Bootstrap 5.3: the grid, utilities, components and customisation with Sass variables and CSS variables. */

return [
    'id' => 'bootstrap', 'family' => 'bootstrap', 'after' => 'css-base', 'categories' => ['frontend', 'design'],
    'title' => L('Bootstrap', 'Bootstrap'), 'short' => L('Bootstrap', 'Bootstrap'), 'level' => L('Base', 'Basics'),
    'desc' => L('Uno dei framework CSS più diffusi, nella versione stabile 5.3: griglia a 12 colonne e breakpoint, classi di utilità, componenti come navbar, card, modali, accordion, offcanvas e form, il JavaScript di Bootstrap, helper di accessibilità, temi e personalizzazione.', 'One of the most widespread CSS frameworks, in its stable 5.3 release: 12-column grid and breakpoints, utility classes, components such as navbars, cards, modals, accordions, offcanvas panels and forms, Bootstrap’s JavaScript, accessibility helpers, themes and customisation.'),
    'lessons' => [
        lesson('grid', L('La griglia', 'The grid'), [
            P('**Bootstrap** è un framework CSS (con un po’ di JavaScript) che offre una griglia, classi di utilità e componenti pronti. La **griglia** usa `.container` (larghezza massima centrata), `.row` (una riga flex) e `.col` (colonne). Ogni riga ha **12 colonne**: `.col-4` occupa un terzo, `.col-6` metà.', '**Bootstrap** is a CSS framework (with some JavaScript) offering a grid, utility classes and ready-made components. The **grid** uses `.container` (centred max width), `.row` (a flex row) and `.col` (columns). Every row has **12 columns**: `.col-4` takes a third, `.col-6` half.'),
            P('I **breakpoint** rendono la griglia responsiva: `sm` (≥576px), `md` (≥768px), `lg` (≥992px), `xl` (≥1200px), `xxl` (≥1400px). Si legge “da qui in su”: `.col-12 .col-md-6 .col-lg-4` è a tutta larghezza su telefono, metà da tablet, un terzo da desktop. Lo spazio tra colonne si regola con `.g-*` (gutter).', '**Breakpoints** make the grid responsive: `sm` (≥576px), `md` (≥768px), `lg` (≥992px), `xl` (≥1200px), `xxl` (≥1400px). Read it as “from here up”: `.col-12 .col-md-6 .col-lg-4` is full width on phones, half from tablets, a third from desktop. Space between columns is set with `.g-*` (gutters).'),
            C('<div class="container">
  <div class="row g-3">
    <div class="col-12 col-md-6 col-lg-4">Uno</div>
    <div class="col-12 col-md-6 col-lg-4">Due</div>
    <div class="col-12 col-md-12 col-lg-4">Tre</div>
  </div>
</div>', 'html'),
        ], [
            Q('g1', L('Quante colonne ha una riga di Bootstrap?', 'How many columns does a Bootstrap row have?'), ['12', '10', '16', '24'], 0, L('`.col-6` è metà.', '`.col-6` is half.')),
            Q('g2', L('Che cosa significa `.col-md-6`?', 'What does `.col-md-6` mean?'), [L('Metà larghezza da 768px in su', 'Half width from 768px up'), L('Metà larghezza solo sotto 768px', 'Half width only below 768px'), L('Sei colonne su telefono', 'Six columns on phones'), L('Una colonna media', 'A medium column')], 0, L('Mobile first: da qui in su.', 'Mobile first: from here up.')),
            Q('g3', L('Che cosa contiene di solito una `.row`?', 'What does a `.row` usually contain?'), [L('Colonne `.col`', '`.col` columns'), L('Solo testo', 'Only text'), L('Un’altra pagina', 'Another page'), L('Lo script di Bootstrap', 'The Bootstrap script')], 0, L('Container › row › col.', 'Container › row › col.')),
        ], task('html', L('Crea una riga con due colonne che stanno una sotto l’altra su telefono e affiancate da `md`: usa `.row` e due `.col-12 .col-md-6`.', 'Create a row with two columns stacked on phones and side by side from `md`: use `.row` and two `.col-12 .col-md-6`.'), "<div class=\"container\">\n  \n</div>", [has('.container .row', L('Una riga nel container', 'A row in the container')), has('.row > .col-12.col-md-6 + .col-12.col-md-6', L('Due colonne responsive', 'Two responsive columns'))], '', "<div class=\"container\">\n  <div class=\"row\">\n    <div class=\"col-12 col-md-6\">Testo</div>\n    <div class=\"col-12 col-md-6\">Foto</div>\n  </div>\n</div>"), [
            dMatch(pMatch(), [['sm', '≥576px'], ['md', '≥768px'], ['lg', '≥992px'], ['xl', '≥1200px'], ['.col-4', L('Un terzo della riga', 'A third of the row')]], L('Breakpoint e colonne.', 'Breakpoints and columns.')),
            dOrder(pSort(), ['<div class="container">', '  <div class="row">', '    <div class="col-md-6">Uno</div>', '  </div>', '</div>'], L('Container, riga, colonna.', 'Container, row, column.')),
        ]),
        lesson('utilities', L('Classi di utilità', 'Utility classes'), [
            P('Le **utilità** applicano una sola proprietà: spaziature `m-*` (margin) e `p-*` (padding) con lati `t b s e x y` e valori 0-5 (`mt-3`, `px-2`, `mx-auto`), testo `text-center`, `fw-bold`, `fs-4`, colori `text-primary`, `bg-light`, `text-bg-dark`, flex `d-flex`, `justify-content-between`, `align-items-center`, `gap-2`, bordi `border`, `rounded`, ombre `shadow-sm`.', '**Utilities** apply a single property: spacing `m-*` (margin) and `p-*` (padding) with sides `t b s e x y` and values 0-5 (`mt-3`, `px-2`, `mx-auto`), text `text-center`, `fw-bold`, `fs-4`, colours `text-primary`, `bg-light`, `text-bg-dark`, flex `d-flex`, `justify-content-between`, `align-items-center`, `gap-2`, borders `border`, `rounded`, shadows `shadow-sm`.'),
            P('Anche le utilità accettano i breakpoint: `d-none d-md-block` nasconde un elemento su telefono e lo mostra da tablet. `s` ed `e` (start/end) sostituiscono left/right per supportare le lingue scritte da destra a sinistra.', 'Utilities accept breakpoints too: `d-none d-md-block` hides an element on phones and shows it from tablets. `s` and `e` (start/end) replace left/right to support right-to-left languages.'),
        ], [
            Q('u1', L('Che cosa fa `mt-3`?', 'What does `mt-3` do?'), [L('Aggiunge un margine superiore', 'Adds a top margin'), L('Aggiunge padding a destra', 'Adds right padding'), L('Ingrandisce il testo', 'Enlarges text'), L('Crea tre colonne', 'Creates three columns')], 0, L('m = margin, t = top.', 'm = margin, t = top.')),
            Q('u2', L('Come si nasconde un elemento solo su telefono?', 'How do you hide an element only on phones?'), ['d-none d-md-block', 'd-block d-md-none', 'hide-mobile', 'visible-md'], 0, L('Nascosto, poi visibile da md.', 'Hidden, then visible from md.')),
            Q('u3', L('Perché Bootstrap usa `s` ed `e` invece di left e right?', 'Why does Bootstrap use `s` and `e` instead of left and right?'), [L('Per supportare le lingue da destra a sinistra', 'To support right-to-left languages'), L('Per risparmiare lettere', 'To save letters'), L('Per errore', 'By mistake'), L('Per i vecchi browser', 'For old browsers')], 0, L('Start ed end.', 'Start and end.')),
        ], task('html', L('Trasforma l’header in una barra flex con `d-flex`, `justify-content-between`, `align-items-center` e padding `p-3`.', 'Turn the header into a flex bar with `d-flex`, `justify-content-between`, `align-items-center` and `p-3` padding.'), "<header class=\"\">\n  <strong>Logo</strong>\n  <a href=\"#\">Accedi</a>\n</header>", [has('header.d-flex', L('È flex', 'It is flex')), has('header.justify-content-between.align-items-center', L('Allineato e distribuito', 'Aligned and distributed')), has('header.p-3', L('Con padding', 'With padding'))], '', '<header class="d-flex justify-content-between align-items-center p-3">'), [
            dMatch(pMatch(), [['p-3', 'Padding'], ['mx-auto', L('Centra orizzontalmente', 'Centres horizontally')], ['d-flex', 'display: flex'], ['fw-bold', L('Grassetto', 'Bold')], ['d-none', 'display: none']], L('Una classe, una proprietà.', 'One class, one property.')),
            dBlanks(pFill(), '<div class="d-___ justify-content-___ gap-2">…</div>', ['flex', 'between'], ['grid', 'left'], L('Flex distribuito.', 'Distributed flex.')),
        ]),
        lesson('components', L('Componenti', 'Components'), [
            P('Bootstrap offre componenti pronti con classi e markup precisi: **pulsanti** (`.btn .btn-primary`, `.btn-outline-secondary`, `.btn-sm`), **card** (`.card`, `.card-body`, `.card-title`), **navbar** responsiva con menu che si chiude su telefono, **alert**, **badge**, **modal**, **dropdown**, **accordion**, **toast**. Quelli interattivi usano il JavaScript di Bootstrap tramite attributi `data-bs-*`.', 'Bootstrap offers ready components with precise classes and markup: **buttons** (`.btn .btn-primary`, `.btn-outline-secondary`, `.btn-sm`), **cards** (`.card`, `.card-body`, `.card-title`), a responsive **navbar** whose menu collapses on phones, **alerts**, **badges**, **modals**, **dropdowns**, **accordions**, **toasts**. Interactive ones use Bootstrap’s JavaScript through `data-bs-*` attributes.'),
            C('<div class="card" style="width: 18rem;">
  <img src="pizza.jpg" class="card-img-top" alt="Pizza">
  <div class="card-body">
    <h5 class="card-title">Margherita</h5>
    <p class="card-text">Pomodoro, mozzarella, basilico.</p>
    <a href="#" class="btn btn-primary">Ordina</a>
  </div>
</div>

<button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#info">Info</button>', 'html'),
            Tip('I componenti includono già gli attributi ARIA di base, ma il testo dei pulsanti, gli `alt` e le etichette dei form restano compito tuo.', 'Components already include basic ARIA attributes, but button text, `alt`s and form labels are still your job.'),
        ], [
            Q('c1', L('Quali classi servono per un pulsante principale?', 'Which classes make a primary button?'), ['btn btn-primary', 'button primary', 'btn-main', 'primary-btn'], 0, L('Base più variante.', 'Base plus variant.')),
            Q('c2', L('Come si apre una modale senza scrivere JavaScript?', 'How do you open a modal without writing JavaScript?'), [L('Con data-bs-toggle e data-bs-target', 'With data-bs-toggle and data-bs-target'), L('Con la classe .open', 'With the .open class'), L('Non si può', 'You cannot'), L('Con un link a modal.html', 'With a link to modal.html')], 0, L('Attributi data-bs-*.', 'data-bs-* attributes.')),
            Q('c3', L('Che cosa resta compito dello sviluppatore per l’accessibilità?', 'What remains the developer’s job for accessibility?'), [L('Testi, alt ed etichette', 'Text, alts and labels'), L('Niente, fa tutto Bootstrap', 'Nothing, Bootstrap does it all'), L('Solo i colori', 'Only colours'), L('Solo il JavaScript', 'Only the JavaScript')], 0, L('Il framework non conosce i tuoi contenuti.', 'The framework does not know your content.')),
        ], task('html', L('Crea una card con `.card`, dentro un `.card-body` con un titolo `.card-title` e un pulsante `.btn.btn-primary`.', 'Create a card with `.card`, containing a `.card-body` with a `.card-title` heading and a `.btn.btn-primary` button.'), '', [has('.card > .card-body', L('Card con corpo', 'Card with body')), has('.card-body .card-title', L('Titolo della card', 'Card title')), has('.card-body .btn.btn-primary', L('Pulsante principale', 'Primary button'))], '', "<div class=\"card\">\n  <div class=\"card-body\">\n    <h5 class=\"card-title\">Margherita</h5>\n    <a href=\"#\" class=\"btn btn-primary\">Ordina</a>\n  </div>\n</div>"), [
            dMatch(pMatch(), [['.btn-primary', L('Pulsante principale', 'Primary button')], ['.btn-outline-secondary', L('Pulsante con contorno', 'Outline button')], ['.card-body', L('Contenuto della card', 'Card content')], ['.alert', L('Messaggio in evidenza', 'Highlighted message')], ['data-bs-toggle', L('Attiva un componente', 'Activates a component')]], L('Componenti Bootstrap.', 'Bootstrap components.')),
            dBug(pBug(), ['<div class="card">', '  <div class="card-body">', '    <a href="#" class="button-primary">Ordina</a>', '  </div>', '</div>'], 2, '    <a href="#" class="btn btn-primary">Ordina</a>', L('Le classi giuste sono btn e btn-primary.', 'The right classes are btn and btn-primary.')),
        ]),
        lesson('theming', L('Temi e personalizzazione', 'Themes and customisation'), [
            P('Un sito che usa Bootstrap senza modifiche “sembra Bootstrap”. Per dargli un’identità si personalizza: con **Sass** si cambiano le variabili **prima** di importare il framework (`$primary`, `$border-radius`, `$font-family-base`, `$enable-shadows`) e si importano solo le parti che servono, per un CSS più leggero.', 'A site using Bootstrap unmodified “looks like Bootstrap”. To give it identity you customise: with **Sass** you change variables **before** importing the framework (`$primary`, `$border-radius`, `$font-family-base`, `$enable-shadows`) and import only the parts you need, for lighter CSS.'),
            C('// custom.scss
$primary: #e8590c;
$border-radius: .75rem;
$font-family-base: "Inter", system-ui, sans-serif;

@import "bootstrap/scss/bootstrap";', 'scss'),
            Tip('Bootstrap 5.3 è scritto con `@import`, che Dart Sass segnala come deprecato: gli avvisi sono normali e si possono silenziare con `--silence-deprecation=import`. Il passaggio ai moduli `@use` è previsto con Bootstrap 6, ancora in sviluppo.', 'Bootstrap 5.3 is written with `@import`, which Dart Sass flags as deprecated: the warnings are normal and can be silenced with `--silence-deprecation=import`. The move to `@use` modules is planned for Bootstrap 6, still in development.'),
            P('Bootstrap 5 espone anche **variabili CSS** (`--bs-primary`, `--bs-body-font-size`, `--bs-border-radius`) modificabili senza ricompilare, e dalla 5.3 le **modalità colore**: con `data-bs-theme="dark"` su `<html>` o su un singolo componente si attiva il tema scuro.', 'Bootstrap 5 also exposes **CSS variables** (`--bs-primary`, `--bs-body-font-size`, `--bs-border-radius`) you can change without recompiling, and since 5.3 **colour modes**: with `data-bs-theme="dark"` on `<html>` or on a single component you turn on the dark theme.'),
        ], [
            Q('t1', L('Quando si cambiano le variabili Sass di Bootstrap?', 'When do you change Bootstrap’s Sass variables?'), [L('Prima di importare il framework', 'Before importing the framework'), L('Dopo averlo importato', 'After importing it'), L('Nel file HTML', 'In the HTML file'), L('Non si possono cambiare', 'They cannot be changed')], 0, L('Così il framework le usa.', 'So the framework uses them.')),
            Q('t2', L('Come si attiva il tema scuro in Bootstrap 5.3?', 'How do you turn on dark theme in Bootstrap 5.3?'), ['data-bs-theme="dark"', 'class="dark-mode"', 'theme="night"', 'style="dark"'], 0, L('Su html o su un componente.', 'On html or a component.')),
            Q('t3', L('Perché importare solo alcune parti di Bootstrap?', 'Why import only some parts of Bootstrap?'), [L('Per un CSS più leggero', 'For lighter CSS'), L('È obbligatorio', 'It is mandatory'), L('Per avere più bug', 'To get more bugs'), L('Per cambiare lingua', 'To change language')], 0, L('Meno codice inutilizzato.', 'Less unused code.')),
        ], task('scss', L('Personalizza Bootstrap: imposta `$primary` a `#e8590c` e `$border-radius` a `.75rem`, poi importa `bootstrap/scss/bootstrap`.', 'Customise Bootstrap: set `$primary` to `#e8590c` and `$border-radius` to `.75rem`, then import `bootstrap/scss/bootstrap`.'), '', [re('^\$primary\s*:\s*#e8590c\s*;', L('Colore primario', 'Primary colour')), re('^\$border-radius\s*:\s*0?\.75rem\s*;', L('Raggio degli angoli', 'Corner radius')), re('\$primary[\s\S]*@(import|use)\s+["\']bootstrap/scss/bootstrap["\']', L('Import dopo le variabili', 'Import after the variables'))], '', "\$primary: #e8590c;\n\$border-radius: .75rem;\n\n@import \"bootstrap/scss/bootstrap\";"), [
            dOrder(pSort(), ['$primary: #e8590c;', '$border-radius: .75rem;', '$enable-shadows: true;', '@import "bootstrap/scss/bootstrap";'], L('Prima le variabili, poi l’import.', 'Variables first, then the import.')),
            dFlash(pFlash(), [[L('Bootstrap espone variabili CSS come --bs-primary', 'Bootstrap exposes CSS variables like --bs-primary'), true], [L('Le variabili Sass vanno cambiate dopo l’import', 'Sass variables must be changed after the import'), false], [L('data-bs-theme="dark" funziona anche su un singolo componente', 'data-bs-theme="dark" works on a single component too'), true], [L('Un sito Bootstrap non si può personalizzare', 'A Bootstrap site cannot be customised'), false]], L('Bootstrap con identità.', 'Bootstrap with identity.')),
        ]),
        lesson('forms-js', L('Form, modali e JavaScript', 'Forms, modals and JavaScript'), [
            P('I form di Bootstrap usano `.form-label`, `.form-control` per i campi e `.form-select` per i menu. La validazione mostra i messaggi con `.is-invalid` e `.invalid-feedback`. Tieni sempre l’etichetta collegata al campo con `for` e `id`.', 'Bootstrap forms use `.form-label`, `.form-control` for fields and `.form-select` for menus. Validation shows messages with `.is-invalid` and `.invalid-feedback`. Always keep the label tied to the field with `for` and `id`.'),
            C('<div class="mb-3">
  <label for="email" class="form-label">Email</label>
  <input id="email" type="email" class="form-control is-invalid">
  <div class="invalid-feedback">Scrivi un indirizzo valido.</div>
</div>

<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#conferma">Apri</button>', 'html'),
            P('I componenti interattivi (modali, menu a tendina, tooltip) funzionano con il file `bootstrap.bundle.js` e gli attributi **`data-bs-*`**: `data-bs-toggle` dice cosa aprire, `data-bs-target` quale elemento. Nessuna riga di JavaScript da scrivere per i casi semplici.', 'Interactive components (modals, dropdowns, tooltips) work with the `bootstrap.bundle.js` file and the **`data-bs-*`** attributes: `data-bs-toggle` says what to open, `data-bs-target` which element. No JavaScript to write for simple cases.'),
        ], [
            Q('fj1', L('Quale classe dà lo stile a un campo di testo?', 'Which class styles a text field?'), ['.form-control', '.input', '.field', '.form-text-box'], 0, L('`.form-select` per i menu.', '`.form-select` for menus.')),
            Q('fj2', L('Che cosa apre un modale senza scrivere JavaScript?', 'What opens a modal without writing JavaScript?'), [L('`data-bs-toggle="modal"` con `data-bs-target`', '`data-bs-toggle="modal"` with `data-bs-target`'), L('La classe `.open`', 'The `.open` class'), L('Un link a `#modal`', 'A link to `#modal`'), L('`onclick="modal()"`', '`onclick="modal()"`')], 0, L('Serve `bootstrap.bundle.js`.', 'It needs `bootstrap.bundle.js`.')),
            Q('fj3', L('Come mostri un campo non valido?', 'How do you show an invalid field?'), [L('`.is-invalid` più `.invalid-feedback`', '`.is-invalid` plus `.invalid-feedback`'), L('`.error`', '`.error`'), L('`.text-red`', '`.text-red`'), L('`.bad`', '`.bad`')], 0, L('Il messaggio compare sotto il campo.', 'The message appears under the field.')),
        ], task('html', L('Crea un campo con etichetta `Nome` (`for="nome"`) e un `<input id="nome">` con la classe `form-control`.', 'Create a field with the label `Nome` (`for="nome"`) and an `<input id="nome">` with the `form-control` class.'), '', [has('label[for="nome"]', L('C’è l’etichetta collegata', 'The linked label is there'), 'Nome'), has('input#nome.form-control', L('Il campo ha la classe giusta', 'The field has the right class'))], '', "<label for=\"nome\" class=\"form-label\">Nome</label>\n<input id=\"nome\" class=\"form-control\">"), [
            dMatch(pMatch(), [['.form-control', L('Campo di testo', 'Text field')], ['.form-select', L('Menu a tendina', 'Select menu')], ['.is-invalid', L('Campo non valido', 'Invalid field')], ['data-bs-toggle', L('Cosa aprire', 'What to open')], ['data-bs-target', L('Quale elemento', 'Which element')]], L('Form e componenti.', 'Forms and components.')),
            dFlash(pFlash(), [[L('I modali funzionano con bootstrap.bundle.js', 'Modals work with bootstrap.bundle.js'), true], [L('L’etichetta non serve se c’è il placeholder', 'The label is not needed if there is a placeholder'), false], [L('.invalid-feedback mostra il messaggio d’errore', '.invalid-feedback shows the error message'), true], [L('Ogni modale richiede JavaScript scritto a mano', 'Every modal needs hand-written JavaScript'), false]], L('Form e interazione.', 'Forms and interaction.')),
        ]),
        lesson('navbar', L('La navbar', 'The navbar'), [
            P('La **navbar** è la barra di navigazione responsiva. `.navbar-expand-lg` la tiene distesa da `lg` in su; sotto, i link si raccolgono dietro un pulsante (`.navbar-toggler`) che apre e chiude il blocco `.collapse.navbar-collapse`. I link stanno in `.navbar-nav` come `.nav-link`.', 'The **navbar** is the responsive navigation bar. `.navbar-expand-lg` keeps it spread out from `lg` up; below that, the links gather behind a button (`.navbar-toggler`) that opens and closes the `.collapse.navbar-collapse` block. The links sit in `.navbar-nav` as `.nav-link`.'),
            C('<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">
    <a class="navbar-brand" href="#">Fucina</a>
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#menu"
            aria-controls="menu" aria-expanded="false" aria-label="Apri il menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="#">Corsi</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Giochi</a></li>
      </ul>
    </div>
  </div>
</nav>', 'html'),
            P('Il pulsante ha solo un’icona, quindi gli serve un `aria-label`; `aria-current="page"` dice allo screen reader qual è la pagina attuale. `ms-auto` spinge i link a destra.', 'The button only has an icon, so it needs an `aria-label`; `aria-current="page"` tells the screen reader which page is the current one. `ms-auto` pushes the links to the right.'),
        ], [
            Q('n1', L('Che cosa fa `.navbar-expand-lg`?', 'What does `.navbar-expand-lg` do?'), [L('Allarga la barra a tutto schermo', 'It widens the bar to full screen'), L('Mostra il menu disteso da `lg` in su e raccolto sotto', 'It shows the menu spread out from `lg` up and collapsed below'), L('Ingrandisce il logo', 'It enlarges the logo'), L('Nasconde la barra sui computer', 'It hides the bar on computers')], 1, L('Come le colonne: da quel breakpoint in su.', 'Like columns: from that breakpoint up.')),
            Q('n2', L('Perché il `.navbar-toggler` ha un `aria-label`?', 'Why does the `.navbar-toggler` have an `aria-label`?'), [L('Per cambiare il colore dell’icona', 'To change the icon colour'), L('Perché Bootstrap non funziona senza', 'Because Bootstrap does not work without it'), L('Per il SEO della pagina', 'For the page’s SEO'), L('Perché ha solo un’icona e serve un nome da leggere', 'Because it only has an icon and needs a name to read')], 3, L('Senza, lo screen reader annuncia solo «pulsante».', 'Without it, the screen reader only announces “button”.')),
            Q('n3', L('Quale attributo segna il link della pagina attuale?', 'Which attribute marks the link of the current page?'), ['aria-current="page"', 'data-bs-active', 'selected', 'href="#current"'], 0, L('Insieme alla classe `active`, che è solo visiva.', 'Together with the `active` class, which is only visual.')),
        ], task('html', L('Completa il pulsante: `class="navbar-toggler"`, `data-bs-toggle="collapse"`, `data-bs-target="#menu"` e un `aria-label`.', 'Complete the button: `class="navbar-toggler"`, `data-bs-toggle="collapse"`, `data-bs-target="#menu"` and an `aria-label`.'), "<nav class=\"navbar navbar-expand-lg\">\n  <a class=\"navbar-brand\" href=\"#\">Fucina</a>\n  <button type=\"button\">\n    <span class=\"navbar-toggler-icon\"></span>\n  </button>\n  <div class=\"collapse navbar-collapse\" id=\"menu\">…</div>\n</nav>", [has('button.navbar-toggler[data-bs-toggle="collapse"]', L('Il pulsante apre un collapse', 'The button opens a collapse')), has('button.navbar-toggler[data-bs-target="#menu"]', L('Punta a `#menu`', 'It points to `#menu`')), has('button.navbar-toggler[aria-label]:not([aria-label=""])', L('Ha un nome accessibile', 'It has an accessible name'))], '', "<button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#menu\" aria-controls=\"menu\" aria-expanded=\"false\" aria-label=\"Apri il menu\">\n  <span class=\"navbar-toggler-icon\"></span>\n</button>"), [
            dMatch(pMatch(), [['.navbar-brand', L('Logo o nome del sito', 'Logo or site name')], ['.navbar-toggler', L('Pulsante del menu su telefono', 'Menu button on phones')], ['.navbar-collapse', L('Blocco che si apre e si chiude', 'Block that opens and closes')], ['.nav-link', L('Un link del menu', 'A menu link')], ['ms-auto', L('Spinge a destra', 'Pushes to the right')]], L('I pezzi di una navbar.', 'The pieces of a navbar.')),
            dBug(pBug(), ['<button class="navbar-toggler" type="button"', '  data-bs-toggle="collapse" data-bs-target="menu">', '  <span class="navbar-toggler-icon"></span>', '</button>'], 1, '  data-bs-toggle="collapse" data-bs-target="#menu">', L('`data-bs-target` vuole un selettore: l’id con il cancelletto.', '`data-bs-target` needs a selector: the id with its hash.')),
        ]),
        lesson('collapse-offcanvas', L('Collapse, accordion e offcanvas', 'Collapse, accordion and offcanvas'), [
            P('`.collapse` nasconde un blocco che un pulsante con `data-bs-toggle="collapse"` apre e chiude. Un **accordion** è una pila di collapse: con `data-bs-parent` aprirne uno chiude gli altri. L’**offcanvas** è un pannello che scorre dal bordo dello schermo, comodo per menu e filtri.', '`.collapse` hides a block that a button with `data-bs-toggle="collapse"` opens and closes. An **accordion** is a stack of collapses: with `data-bs-parent` opening one closes the others. The **offcanvas** is a panel that slides in from the edge of the screen, handy for menus and filters.'),
            C('<div class="accordion" id="faq">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button"
              data-bs-toggle="collapse" data-bs-target="#d1"
              aria-expanded="false" aria-controls="d1">Quanto costa?</button>
    </h2>
    <div id="d1" class="accordion-collapse collapse" data-bs-parent="#faq">
      <div class="accordion-body">È gratis.</div>
    </div>
  </div>
</div>

<button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#filtri">Filtri</button>
<div class="offcanvas offcanvas-start" tabindex="-1" id="filtri" aria-labelledby="titolo-filtri">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="titolo-filtri">Filtri</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Chiudi"></button>
  </div>
  <div class="offcanvas-body">…</div>
</div>', 'html'),
            P('`.show` apre un collapse già all’inizio. `.offcanvas-start` lo fa entrare da sinistra (in una pagina da destra a sinistra, da destra), `-end` dal lato opposto, `-top` e `-bottom` dall’alto e dal basso. `data-bs-dismiss` chiude il componente in cui si trova.', '`.show` opens a collapse from the start. `.offcanvas-start` brings it in from the left (from the right on a right-to-left page), `-end` from the other side, `-top` and `-bottom` from above and below. `data-bs-dismiss` closes the component it sits in.'),
        ], [
            Q('o1', L('Che cosa fa `data-bs-parent` in un accordion?', 'What does `data-bs-parent` do in an accordion?'), [L('Colora la voce aperta', 'It colours the open item'), L('Ordina le voci', 'It sorts the items'), L('Quando se ne apre una, chiude le altre', 'When one opens, it closes the others'), L('Blocca l’apertura', 'It blocks opening')], 2, L('Senza, le voci si aprono indipendenti.', 'Without it, items open independently.')),
            Q('o2', L('Che cos’è un offcanvas?', 'What is an offcanvas?'), [L('Un pannello che scorre dal bordo dello schermo', 'A panel that slides in from the screen edge'), L('Un’immagine fuori dalla pagina', 'An image outside the page'), L('Una tabella nascosta', 'A hidden table'), L('Un tipo di griglia', 'A kind of grid')], 0, L('Ottimo per menu e filtri su telefono.', 'Great for menus and filters on phones.')),
            Q('o3', L('Quale classe apre un collapse già al caricamento?', 'Which class opens a collapse when the page loads?'), ['.open', '.active', '.visible', '.show'], 3, L('È la stessa classe che Bootstrap aggiunge quando lo apri.', 'It is the same class Bootstrap adds when you open it.')),
        ], task('html', L('Crea un pulsante con `data-bs-toggle="collapse"` e `data-bs-target="#dettagli"`, e un `div` con `id="dettagli"` e la classe `collapse`.', 'Create a button with `data-bs-toggle="collapse"` and `data-bs-target="#dettagli"`, and a `div` with `id="dettagli"` and the `collapse` class.'), '', [has('button[data-bs-toggle="collapse"][data-bs-target="#dettagli"]', L('Il pulsante comanda il collapse', 'The button controls the collapse')), has('div#dettagli.collapse', L('Il blocco si nasconde', 'The block hides'))], '', "<button class=\"btn btn-secondary\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#dettagli\" aria-expanded=\"false\" aria-controls=\"dettagli\">Dettagli</button>\n<div class=\"collapse\" id=\"dettagli\">Tutte le informazioni.</div>"), [
            dMatch(pMatch(), [['.collapse', L('Blocco nascosto che si apre', 'Hidden block that opens')], ['.accordion', L('Pila di voci apribili', 'Stack of openable items')], ['data-bs-parent', L('Una voce aperta alla volta', 'One open item at a time')], ['.offcanvas-start', L('Pannello da sinistra', 'Panel from the left')], ['data-bs-dismiss', L('Chiude il componente', 'Closes the component')]], L('Contenuti che si aprono quando servono.', 'Content that opens when needed.')),
            dFlash(pFlash(), [[L('Un accordion è fatto di più collapse', 'An accordion is made of several collapses'), true], [L('`.offcanvas-end` entra sempre da sinistra', '`.offcanvas-end` always enters from the left'), false], [L('`.show` apre il collapse all’inizio', '`.show` opens the collapse at the start'), true], [L('L’offcanvas funziona senza il JavaScript di Bootstrap', 'The offcanvas works without Bootstrap’s JavaScript'), false]], L('Per i componenti interattivi serve sempre `bootstrap.bundle.js`.', 'Interactive components always need `bootstrap.bundle.js`.')),
        ]),
        lesson('js-api', L('Il JavaScript di Bootstrap', 'Bootstrap’s JavaScript'), [
            P('Gli attributi `data-bs-*` bastano per i casi semplici. Per comandare un componente dal tuo codice usi l’oggetto `bootstrap`: `bootstrap.Modal.getOrCreateInstance(elemento)` restituisce il modale, che poi apri con `.show()` e chiudi con `.hide()`.', 'The `data-bs-*` attributes are enough for simple cases. To control a component from your own code you use the `bootstrap` object: `bootstrap.Modal.getOrCreateInstance(element)` gives back the modal, which you then open with `.show()` and close with `.hide()`.'),
            C('// Tooltip e popover vanno attivati a mano
document.querySelectorAll(\'[data-bs-toggle="tooltip"]\')
  .forEach(el => new bootstrap.Tooltip(el));

// Aprire un modale dal codice
const conferma = bootstrap.Modal.getOrCreateInstance("#conferma");
conferma.show();

// Reagire agli eventi
document.querySelector("#conferma")
  .addEventListener("hidden.bs.modal", () => console.log("Chiuso"));', 'js'),
            P('Tooltip e popover non partono da soli, per non pesare sulle pagine che non li usano: vanno creati con `new bootstrap.Tooltip(…)`. Ogni componente emette eventi come `show.bs.modal` (sta per aprirsi) e `hidden.bs.modal` (si è chiuso del tutto).', 'Tooltips and popovers do not start by themselves, so as not to weigh down pages that do not use them: you create them with `new bootstrap.Tooltip(…)`. Every component fires events like `show.bs.modal` (about to open) and `hidden.bs.modal` (fully closed).'),
        ], [
            Q('j1', L('Quale componente va attivato a mano con JavaScript?', 'Which component must be turned on by hand with JavaScript?'), [L('Il collapse', 'The collapse'), L('Il tooltip', 'The tooltip'), L('Il modale con `data-bs-toggle`', 'The modal with `data-bs-toggle`'), L('L’accordion', 'The accordion')], 1, L('Anche i popover: sono facoltativi per ragioni di prestazioni.', 'Popovers too: they are opt-in for performance reasons.')),
            Q('j2', L('Che cosa fa `bootstrap.Modal.getOrCreateInstance(el)`?', 'What does `bootstrap.Modal.getOrCreateInstance(el)` do?'), [L('Crea un nuovo elemento HTML', 'It creates a new HTML element'), L('Scarica Bootstrap da internet', 'It downloads Bootstrap from the internet'), L('Restituisce il modale esistente o ne crea uno', 'It gives back the existing modal or creates one'), L('Chiude tutti i modali', 'It closes every modal')], 2, L('Così non crei due istanze per lo stesso elemento.', 'That way you never create two instances for the same element.')),
            Q('j3', L('Quale evento arriva quando un modale si è chiuso del tutto?', 'Which event arrives when a modal has fully closed?'), ['show.bs.modal', 'shown.bs.modal', 'hide.bs.modal', 'hidden.bs.modal'], 3, L('`show` e `hide` arrivano all’inizio dell’azione, `shown` e `hidden` alla fine.', '`show` and `hide` arrive at the start of the action, `shown` and `hidden` at the end.')),
        ], task('js', L('Attiva tutti i tooltip: seleziona gli elementi `[data-bs-toggle="tooltip"]` e crea per ognuno un `new bootstrap.Tooltip`.', 'Turn on every tooltip: select the `[data-bs-toggle="tooltip"]` elements and create a `new bootstrap.Tooltip` for each one.'), '// attiva i tooltip
', [re('querySelectorAll\(\s*[\'"`]\[data-bs-toggle=[\'"]?tooltip[\'"]?\][\'"`]\s*\)', L('Selezioni i tooltip', 'You select the tooltips')), re('new\s+bootstrap\.Tooltip\s*\(', L('Crei ogni tooltip', 'You create each tooltip'))], '', "document.querySelectorAll('[data-bs-toggle=\"tooltip\"]')\n  .forEach(el => new bootstrap.Tooltip(el));"), [
            dOrder(pSort(), ['const el = document.querySelector("#conferma");', 'const modale = bootstrap.Modal.getOrCreateInstance(el);', 'modale.show();'], L('Trova l’elemento, prendi il modale, aprilo.', 'Find the element, get the modal, open it.')),
            dMatch(pMatch(), [['new bootstrap.Tooltip(el)', L('Attiva un tooltip', 'Turns on a tooltip')], ['getOrCreateInstance()', L('Prende o crea il componente', 'Gets or creates the component')], ['.show()', L('Apre', 'Opens')], ['show.bs.modal', L('Sta per aprirsi', 'About to open')], ['hidden.bs.modal', L('Si è chiuso', 'Has closed')]], L('Quando gli attributi non bastano, c’è il codice.', 'When attributes are not enough, there is code.')),
        ]),
        lesson('helpers', L('Helper e accessibilità', 'Helpers and accessibility'), [
            P('Bootstrap ha piccole classi «helper» per problemi frequenti. `.visually-hidden` nasconde un testo alla vista ma lo lascia agli screen reader; `.visually-hidden-focusable` lo mostra quando riceve il focus, perfetto per il link «Vai al contenuto». `.stretched-link` rende cliccabile tutta la card che contiene il link.', 'Bootstrap has small “helper” classes for common problems. `.visually-hidden` hides text from view but keeps it for screen readers; `.visually-hidden-focusable` shows it when it gets focus, perfect for the “Skip to content” link. `.stretched-link` makes the whole card containing the link clickable.'),
            C('<a class="visually-hidden-focusable" href="#contenuto">Vai al contenuto</a>

<button class="btn btn-outline-danger">
  <svg aria-hidden="true">…</svg>
  <span class="visually-hidden">Elimina</span>
</button>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Corso CSS</h5>
    <a href="corso.html" class="stretched-link">Apri</a>
  </div>
</div>

<div class="ratio ratio-16x9">
  <iframe src="video.html" title="Il video del corso"></iframe>
</div>', 'html'),
            P('`.ratio .ratio-16x9` tiene le proporzioni di video e iframe; `.text-truncate` taglia un testo lungo con i puntini; `.vstack` e `.hstack` impilano gli elementi in colonna o in riga con `gap-*`.', '`.ratio .ratio-16x9` keeps the proportions of videos and iframes; `.text-truncate` cuts long text with an ellipsis; `.vstack` and `.hstack` stack items in a column or a row with `gap-*`.'),
        ], [
            Q('h1', L('Che cosa fa `.visually-hidden`?', 'What does `.visually-hidden` do?'), [L('Cancella il testo dalla pagina', 'It deletes the text from the page'), L('Nasconde il testo alla vista ma non agli screen reader', 'It hides the text from view but not from screen readers'), L('Nasconde il testo a tutti', 'It hides the text from everyone'), L('Mette il testo in trasparenza', 'It makes the text see-through')], 1, L('`display: none` invece lo toglierebbe anche agli screen reader.', '`display: none` would remove it from screen readers too.')),
            Q('h2', L('A che cosa serve `.stretched-link`?', 'What is `.stretched-link` for?'), [L('Allunga il testo del link', 'It stretches the link text'), L('Apre il link in una nuova scheda', 'It opens the link in a new tab'), L('Sottolinea il link', 'It underlines the link'), L('Rende cliccabile tutto il contenitore', 'It makes the whole container clickable')], 3, L('Il contenitore deve essere posizionato, come lo è `.card`.', 'The container must be positioned, as `.card` is.')),
            Q('h3', L('Quale classe tiene un iframe in proporzione 16:9?', 'Which class keeps an iframe at 16:9?'), ['.ratio .ratio-16x9', '.embed-16-9', '.video-wide', '.aspect-16'], 0, L('Il figlio riempie la scatola con le proporzioni giuste.', 'The child fills the box at the right proportions.')),
        ], task('html', L('Al pulsante con sola icona aggiungi uno `span` con la classe `visually-hidden` e il testo `Elimina`.', 'Add to the icon-only button a `span` with the `visually-hidden` class and the text `Elimina`.'), "<button class=\"btn btn-outline-danger\">\n  <svg aria-hidden=\"true\" width=\"16\" height=\"16\"></svg>\n</button>", [has('button span.visually-hidden', L('C’è il testo nascosto', 'The hidden text is there'), 'Elimina'), has('button svg[aria-hidden="true"]', L('L’icona resta nascosta ai lettori', 'The icon stays hidden from readers'))], '', "<button class=\"btn btn-outline-danger\">\n  <svg aria-hidden=\"true\" width=\"16\" height=\"16\"></svg>\n  <span class=\"visually-hidden\">Elimina</span>\n</button>"), [
            dMatch(pMatch(), [['.visually-hidden', L('Solo per gli screen reader', 'Only for screen readers')], ['.visually-hidden-focusable', L('Si vede con il focus', 'Shows on focus')], ['.stretched-link', L('Tutta la card cliccabile', 'The whole card clickable')], ['.ratio-16x9', L('Proporzioni del video', 'Video proportions')], ['.text-truncate', L('Testo tagliato con i puntini', 'Text cut with an ellipsis')]], L('Piccole classi, grandi comodità.', 'Small classes, big conveniences.')),
            dFlash(pFlash(), [[L('Un pulsante con sola icona ha bisogno di un testo per gli screen reader', 'An icon-only button needs text for screen readers'), true], [L('`.visually-hidden` equivale a `display: none`', '`.visually-hidden` is the same as `display: none`'), false], [L('Il link «Vai al contenuto» aiuta chi usa la tastiera', 'The “Skip to content” link helps keyboard users'), true], [L('`.stretched-link` funziona solo nelle tabelle', '`.stretched-link` only works in tables'), false]], L('Bootstrap aiuta, ma il testo giusto lo scegli tu.', 'Bootstrap helps, but you choose the right text.')),
        ]),
    ],
];
