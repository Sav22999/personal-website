<?php
/** Angular basics: components, templates, bindings, events, control flow, signals, services, dynamic classes, pipes and effects. */

/** The four bindings between class and template: interpolation and property towards the page, events back, ngModel both ways. */
if (!function_exists('fig_ngbase_bind')) {
    function fig_ngbase_bind(string $l): string
    {
        $t = $l === 'it' ? ['classe', 'template', 'testo', 'proprietà', 'evento', 'nei due sensi'] : ['class', 'template', 'text', 'property', 'event', 'both ways'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ngba" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="20" width="150" height="214" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="478" y="20" width="150" height="214" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g text-anchor="middle" font-weight="700" font-size="16"><text x="87" y="48">{$t[0]}</text><text x="553" y="48">{$t[1]}</text></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="87" y="110">n = 0</text><text x="87" y="134">piu()</text><text x="87" y="158">email = ""</text><text x="553" y="100">&lt;p&gt;</text><text x="553" y="124">&lt;button&gt;</text><text x="553" y="148">&lt;input&gt;</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none"><path d="M168 76H472" marker-end="url(#ngba)"/><path d="M168 124H472" marker-end="url(#ngba)"/><path d="M472 172H168" marker-end="url(#ngba)"/><path d="M168 220H472" marker-end="url(#ngba)" marker-start="url(#ngba)"/></g>
<g text-anchor="middle"><text x="320" y="68"><tspan font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#0B7F5C">{{ n }}</tspan> · <tspan font-size="13" fill="#5B5880">{$t[2]}</tspan></text>
<text x="320" y="116"><tspan font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#1B66BC">[disabled]</tspan> · <tspan font-size="13" fill="#5B5880">{$t[3]}</tspan></text>
<text x="320" y="164"><tspan font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#B84508">(click)</tspan> · <tspan font-size="13" fill="#5B5880">{$t[4]}</tspan></text>
<text x="320" y="212"><tspan font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#5A35C8">[(ngModel)]</tspan> · <tspan font-size="13" fill="#5B5880">{$t[5]}</tspan></text></g>
</svg>
SVG;
    }
}

/** A signal feeding a computed and the template; a click updates the signal and the change travels on, step by step. */
if (!function_exists('fig_ngbase_signal')) {
    function fig_ngbase_signal(string $l): string
    {
        $t = $l === 'it' ? ['1 · cambia', '2 · si ricalcola', '3 · si aggiorna', 'solo questi due testi'] : ['1 · changes', '2 · recomputed', '3 · updated', 'only these two texts'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.ngs{opacity:0;animation:ngs 4.5s infinite}.ngs2{animation-delay:1.5s}.ngs3{animation-delay:3s}@keyframes ngs{0%{opacity:0}4%{opacity:1}30%{opacity:1}34%{opacity:0}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.ngs{animation:none}}</style>
<defs><marker id="ngsa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g text-anchor="middle" font-size="13" font-weight="700" fill="#5B5880"><text x="120" y="52">{$t[0]}</text><text x="335" y="52">{$t[1]}</text><text x="550" y="52">{$t[2]}</text></g>
<rect x="40" y="62" width="160" height="64" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="250" y="62" width="170" height="64" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="470" y="62" width="160" height="64" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g fill="none" stroke="#F03E82" stroke-width="4"><rect class="ngs" x="34" y="56" width="172" height="76" rx="15"/><rect class="ngs ngs2" x="244" y="56" width="182" height="76" rx="15"/><rect class="ngs ngs3" x="464" y="56" width="172" height="76" rx="15"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="120" y="89" font-weight="700">pezzi</text><text x="120" y="110">signal(0)</text><text x="335" y="89" font-weight="700">totale</text><text x="335" y="110">computed(…)</text><text x="550" y="89">{{ pezzi() }}</text><text x="550" y="110">{{ totale() }}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ngsa)"><path d="M200 94H246"/><path d="M420 94H466"/><path d="M120 168V130"/><path d="M200 70C300 0 540 0 612 58"/></g>
<rect x="30" y="170" width="180" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="120" y="195" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">(click) update(n+1)</text>
<text x="550" y="150" text-anchor="middle" font-size="13" fill="#5B5880">{$t[3]}</text>
</svg>
SVG;
    }
}

/** Dependency injection: one CorsiService instance, which receives HttpClient, is handed to two components. */
if (!function_exists('fig_ngbase_di')) {
    function fig_ngbase_di(string $l): string
    {
        $t = $l === 'it' ? ['una sola istanza', 'per tutta l’app', 'lo stesso servizio per entrambi'] : ['a single instance', 'for the whole app', 'the same service for both'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ngda" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="40" width="150" height="50" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="87" y="70" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14">HttpClient</text>
<rect x="220" y="26" width="200" height="78" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="320" y="58" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-weight="700">CorsiService</text>
<text x="320" y="84" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#5B5880">providedIn: "root"</text>
<text x="436" y="58" font-size="13" fill="#0B7F5C" font-weight="700">{$t[0]}</text><text x="436" y="76" font-size="13" fill="#0B7F5C" font-weight="700">{$t[1]}</text>
<g fill="#ECE6FF" stroke="#7048E8" stroke-width="2"><rect x="40" y="166" width="250" height="56" rx="12"/><rect x="350" y="166" width="250" height="56" rx="12"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="165" y="199">ElencoComponent</text><text x="475" y="199">DettaglioComponent</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ngda)"><path d="M162 65H216"/><path d="M290 104C280 130 200 140 175 162"/><path d="M350 104C360 130 440 140 465 162"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#5A35C8"><text x="160" y="138">inject()</text><text x="420" y="138">inject()</text></g>
<text x="320" y="244" text-anchor="middle" font-size="13" fill="#5B5880">{$t[2]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'angular-base', 'family' => 'angular', 'after' => 'ts',
    'title' => L('Angular base', 'Angular Basics'), 'short' => L('Angular base', 'Angular Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Componenti, template, binding, eventi, `@if` e `@for`, signal, servizi, classi dinamiche, pipe ed effect: le basi del framework di Google.', 'Components, templates, bindings, events, `@if` and `@for`, signals, services, dynamic classes, pipes and effects: the basics of Google’s framework.'),
    'lessons' => [
        lesson('components', L('Il primo componente', 'The first component'), [
            P('In Angular l’interfaccia è fatta di **componenti**: una classe TypeScript con il decoratore `@Component`, che indica il **selettore** (il tag da usare nell’HTML) e il **template**.', 'In Angular the interface is made of **components**: a TypeScript class with the `@Component` decorator, which gives the **selector** (the tag to use in HTML) and the **template**.'),
            C('import { Component } from "@angular/core";

@Component({
  selector: "app-saluto",
  template: `<h1>Ciao, {{ nome }}!</h1>`,
})
export class SalutoComponent {
  nome = "Ada";
}', 'ts'),
            P('Le doppie graffe `{{ }}` mostrano nel template il valore di una proprietà della classe: è l’**interpolazione**. Dalla versione 19 ogni componente è **standalone** di default: elenca da solo ciò che usa nell’array `imports`, senza `NgModule`.', 'Double braces `{{ }}` show a class property in the template: it is **interpolation**. Since version 19 every component is **standalone** by default: it lists what it uses in its own `imports` array, with no `NgModule`.'),
            Tip('La CLI recente (`ng generate component saluto`) crea file come `saluto.ts` con una classe `Saluto`, senza il suffisso `Component`: entrambi gli stili funzionano.', 'The recent CLI (`ng generate component saluto`) creates files like `saluto.ts` with a class `Saluto`, without the `Component` suffix: both styles work.'),
        ], [
            Q('c1', L('Quale decoratore crea un componente?', 'Which decorator creates a component?'), ['@Component', '@Module', '@Injectable', '@View'], 0, L('`@Injectable` è per i servizi.', '`@Injectable` is for services.')),
            Q('c2', L('A che cosa serve `selector`?', 'What is `selector` for?'), [L('È il tag con cui usi il componente', 'It is the tag you use the component with'), L('Sceglie un colore', 'It picks a colour'), L('È il nome del file', 'It is the file name'), L('Seleziona un database', 'It selects a database')], 0, L('Scrivi `<app-saluto>` nell’HTML.', 'You write `<app-saluto>` in the HTML.')),
            Q('c3', L('Come mostri `nome` nel template?', 'How do you show `nome` in the template?'), ['{{ nome }}', '${nome}', '<?= nome ?>', '[nome]'], 0, L('Interpolazione con doppie graffe.', 'Interpolation with double braces.')),
        ], task('angular', L('Completa il componente `app-carta` con un template che mostra `{{ titolo }}`.', 'Complete the `app-carta` component with a template that shows `{{ titolo }}`.'), '@Component({
})
export class CartaComponent {
  titolo = "Fucina";
}', [re('selector\s*:\s*["\']app-carta["\']', L('Il selettore è giusto', 'The selector is right')), re('template\s*:[\s\S]*\{\{\s*titolo\s*\}\}', L('Il template mostra il titolo', 'The template shows the title'))], '', '@Component({
  selector: "app-carta",
  template: `<h2>{{ titolo }}</h2>`,
})'), [
            dOrder(pSort(), ['import { Component } from "@angular/core";', '@Component({', '  selector: "app-ciao",', '  template: `<p>Ciao</p>`,', '})', 'export class CiaoComponent {}'], L('Import, decoratore, classe.', 'Import, decorator, class.')),
            dMatch(pMatch(), [['@Component', L('Crea un componente', 'Creates a component')], ['selector', L('Il tag HTML', 'The HTML tag')], ['template', L('L’HTML del componente', 'The component’s HTML')], ['{{ }}', L('Interpolazione', 'Interpolation')]], L('I pezzi di un componente.', 'The parts of a component.')),
        ]),
        lesson('bindings', L('Binding di proprietà ed eventi', 'Property and event binding'), [
            P('Le **parentesi quadre** collegano una proprietà dell’elemento a un valore: `[disabled]="occupato"`. Le **tonde** ascoltano un evento: `(click)="salva()"`.', '**Square brackets** bind an element property to a value: `[disabled]="occupato"`. **Round brackets** listen to an event: `(click)="salva()"`.'),
            C('@Component({
  selector: "app-contatore",
  template: `
    <p>Clic: {{ n }}</p>
    <button (click)="piu()" [disabled]="n >= 10">+1</button>
  `,
})
export class ContatoreComponent {
  n = 0;
  piu() { this.n++; }
}', 'ts'),
            P('Dati verso la vista con `[ ]`, eventi verso la classe con `( )`. Insieme, `[(ngModel)]` crea un collegamento nei due sensi per i campi dei moduli (importando `FormsModule`).', 'Data to the view with `[ ]`, events to the class with `( )`. Together, `[(ngModel)]` makes a two-way link for form fields (importing `FormsModule`).'),
            Fig(L(fig_ngbase_bind('it'), fig_ngbase_bind('en')),
                L('Due riquadri, la classe a sinistra e il template a destra, collegati da quattro frecce: {{ n }} per il testo e [disabled] per una proprietà vanno verso il template, (click) per un evento torna verso la classe, [(ngModel)] va nei due sensi.', 'Two boxes, the class on the left and the template on the right, joined by four arrows: {{ n }} for text and [disabled] for a property go towards the template, (click) for an event goes back to the class, [(ngModel)] goes both ways.'),
                L('Le parentesi dicono la direzione: quadre verso la pagina, tonde verso la classe.', 'The brackets tell the direction: square towards the page, round towards the class.')),
            P('Tra le virgolette di `[disabled]` c’è un’espressione TypeScript, valutata con i dati della classe. Senza quadre, `disabled="n >= 10"` sarebbe solo una stringa di testo, sempre presente: un errore molto comune. Tra le tonde scrivi la chiamata da fare, con le parentesi: `(click)="piu()"`. Quando `n` cambia, Angular aggiorna da solo il testo e il pulsante.', 'Between the quotes of `[disabled]` there is a TypeScript expression, evaluated with the data of the class. Without square brackets, `disabled="n >= 10"` would be just a text string, always present: a very common mistake. Between round brackets you write the call to make, with its brackets: `(click)="piu()"`. When `n` changes, Angular updates the text and the button by itself.'),
        ], [
            Q('b1', L('Come ascolti il clic?', 'How do you listen to a click?'), ['(click)="fai()"', '[click]="fai()"', 'onclick={fai}', '{{click}}'], 0, L('Tonde per gli eventi.', 'Round brackets for events.')),
            Q('b2', L('Che cosa fa `[disabled]="occupato"`?', 'What does `[disabled]="occupato"` do?'), [L('Collega la proprietà al valore di `occupato`', 'Binds the property to the value of `occupato`'), L('Ascolta un evento', 'Listens to an event'), L('Stampa un testo', 'Prints a text'), L('Crea una variabile', 'Creates a variable')], 0, L('Quadre per le proprietà.', 'Square brackets for properties.')),
            Q('b3', L('Come si chiama `[( )]`?', 'What is `[( )]` called?'), [L('Binding bidirezionale', 'Two-way binding'), L('Pipe', 'Pipe'), L('Direttiva strutturale', 'Structural directive'), L('Decoratore', 'Decorator')], 0, L('La “banana nella scatola”.', 'The “banana in a box”.')),
        ], task('angular', L('Al bottone collega `(click)="salva()"` e `[disabled]="vuoto"`.', 'Bind `(click)="salva()"` and `[disabled]="vuoto"` to the button.'), '<button>Salva</button>', [re('\(click\)\s*=\s*"salva\(\)"', L('Ascolta il clic', 'It listens to the click')), re('\[disabled\]\s*=\s*"vuoto"', L('Si disattiva', 'It disables itself'))], '', '<button (click)="salva()" [disabled]="vuoto">Salva</button>'), [
            dBlanks(pFill(), "<input ___=\"nome\" />\n<button ___=\"invia()\">Invia</button>\n<p>{{ ___ }}</p>", ['[value]', '(click)', 'nome'], ['(value)', '[click]', 'this.nome'], L('Proprietà, evento, interpolazione.', 'Property, event, interpolation.')),
            dFlash(pFlash(), [[L('`(input)` ascolta quando scrivi', '`(input)` listens while you type'), true], [L('`[src]` collega l’attributo src', '`[src]` binds the src attribute'), true], [L('`{{ }}` può chiamare eventi', '`{{ }}` can trigger events'), false], [L('`[(ngModel)]` collega il dato nei due sensi', '`[(ngModel)]` binds the data both ways'), true]], L('Le graffe mostrano, le tonde ascoltano.', 'Braces show, round brackets listen.')),
        ]),
        lesson('control-flow', L('@if e @for', '@if and @for'), [
            P('Il **control flow** integrato di Angular (dalla versione 17, al posto di `*ngIf` e `*ngFor`) scrive condizioni e cicli direttamente nel template: `@if` mostra un blocco solo se la condizione è vera, `@for` ripete un blocco per ogni elemento.', 'Angular’s built-in **control flow** (since version 17, replacing `*ngIf` and `*ngFor`) writes conditions and loops right in the template: `@if` shows a block only if the condition is true, `@for` repeats a block for each item.'),
            C('@if (corsi.length > 0) {
  <ul>
    @for (c of corsi; track c.id) {
      <li>{{ c.titolo }}</li>
    }
  </ul>
} @else {
  <p>Nessun corso.</p>
}', 'html'),
            P('`track` dice ad Angular come riconoscere ogni elemento: così aggiorna solo le righe cambiate.', '`track` tells Angular how to recognise each item: so it only updates the rows that changed.'),
            P('`track` è obbligatorio: di solito usi un identificativo unico come `c.id`. Senza un buon `track`, cambiando l’ordine della lista Angular ricreerebbe tutte le righe, perdendo per esempio il testo scritto in un campo. Dopo `@for` puoi aggiungere `@empty { … }`, mostrato quando la lista è vuota, e per molti casi c’è `@switch` con i suoi `@case`.', '`track` is required: you usually use a unique identifier such as `c.id`. Without a good `track`, when the order of the list changes Angular would recreate every row, losing for example the text typed into a field. After `@for` you can add `@empty { … }`, shown when the list is empty, and for many cases there is `@switch` with its `@case` blocks.'),
        ], [
            Q('f1', L('Che cosa è obbligatorio in `@for`?', 'What is required in `@for`?'), ['track', 'index', 'key', 'let'], 0, L('Serve a riconoscere gli elementi.', 'It identifies the items.')),
            Q('f2', L('Come mostri un blocco se la lista è vuota?', 'How do you show a block when the list is empty?'), ['@empty', '@none', '@else-for', '@nothing'], 0, L('`@for` ha un suo blocco `@empty`.', '`@for` has its own `@empty` block.')),
            Q('f3', L('`@if` è scritto in…', '`@if` is written in…'), [L('Nel template', 'The template'), L('Nel CSS', 'The CSS'), L('Nel file di configurazione', 'The config file'), L('Nel database', 'The database')], 0, L('È sintassi del template.', 'It is template syntax.')),
        ], task('angular', L('Ripeti un `<li>` per ogni `u` di `utenti` con `@for`, usando `track u.id`.', 'Repeat an `<li>` for each `u` in `utenti` with `@for`, using `track u.id`.'), '<ul>
</ul>', [re('@for\s*\(\s*u\s+of\s+utenti\s*;\s*track\s+u\.id\s*\)', L('Il ciclo è giusto', 'The loop is right')), re('<li>[\s\S]*\{\{[\s\S]*\}\}[\s\S]*</li>', L('Il `<li>` mostra un valore', 'The `<li>` shows a value'))], '', '@for (u of utenti; track u.id) {
  <li>{{ u.nome }}</li>
}'), [
            dOrder(pSort(), ['@if (caricato) {', '  @for (p of prodotti; track p.id) {', '    <app-prodotto [dati]="p" />', '  }', '} @else {', '  <p>Caricamento…</p>', '}'], L('Condizione esterna, ciclo dentro.', 'Outer condition, loop inside.')),
            dBug(pBug(), ['<ul>', '  @for (c of corsi) {', '    <li>{{ c.titolo }}</li>', '  }', '</ul>'], 1, '  @for (c of corsi; track c.id) {', L('`@for` vuole sempre `track`.', '`@for` always needs `track`.')),
        ]),
        lesson('signals', L('I signal', 'Signals'), [
            P('Un **signal** è un valore che avvisa quando cambia. Si crea con `signal(0)`, si legge chiamandolo (`conta()`), si cambia con `set` o `update`. `computed` deriva un valore che si aggiorna da solo.', 'A **signal** is a value that tells when it changes. You create it with `signal(0)`, read it by calling it (`conta()`), change it with `set` or `update`. `computed` derives a value that updates by itself.'),
            C('import { Component, signal, computed } from "@angular/core";

@Component({
  selector: "app-carrello",
  template: `<p>{{ pezzi() }} pezzi · {{ totale() }} €</p>
             <button (click)="aggiungi()">+</button>`,
})
export class CarrelloComponent {
  pezzi = signal(0);
  totale = computed(() => this.pezzi() * 5);
  aggiungi() { this.pezzi.update(n => n + 1); }
}', 'ts'),
            P('Angular sa esattamente quali parti della pagina dipendono da un signal e aggiorna solo quelle.', 'Angular knows exactly which parts of the page depend on a signal and updates only those.'),
            Fig(L(fig_ngbase_signal('it'), fig_ngbase_signal('en')),
                L('Tre riquadri collegati: il signal pezzi, il computed totale e il template che mostra entrambi. Un click chiama update sul signal; la modifica passa a totale, che si ricalcola, e poi al template, che aggiorna solo quei due testi.', 'Three linked boxes: the signal pezzi, the computed totale and the template showing both. A click calls update on the signal; the change passes to totale, which is recomputed, and then to the template, which updates only those two texts.'),
                L('Una modifica segue le dipendenze: signal, computed, template.', 'A change follows the dependencies: signal, computed, template.')),
            P('Per questo un signal va sempre cambiato con `set` o `update`, mai modificando il suo contenuto di nascosto: con una lista scrivi `lista.update(l => [...l, nuovo])`, non `lista().push(nuovo)`, altrimenti Angular non si accorge del cambiamento. Un `computed` è di sola lettura e si ricalcola solo quando qualcuno lo legge e un suo signal è cambiato.', 'That is why a signal must always be changed with `set` or `update`, never by quietly modifying its content: with a list write `lista.update(l => [...l, nuovo])`, not `lista().push(nuovo)`, otherwise Angular does not notice the change. A `computed` is read-only and is recalculated only when someone reads it and one of its signals has changed.'),
        ], [
            Q('s1', L('Come leggi il valore del signal `conta`?', 'How do you read the value of the signal `conta`?'), ['conta()', 'conta.value', 'conta.get', '$conta'], 0, L('Un signal si legge chiamandolo.', 'A signal is read by calling it.')),
            Q('s2', L('Che cosa fa `computed`?', 'What does `computed` do?'), [L('Un valore derivato che si aggiorna da solo', 'A derived value that updates itself'), L('Un ciclo', 'A loop'), L('Una richiesta HTTP', 'An HTTP request'), L('Un componente', 'A component')], 0, L('Dipende da altri signal.', 'It depends on other signals.')),
            Q('s3', L('Come aumenti un signal di 1?', 'How do you increase a signal by 1?'), ['n.update(v => v + 1)', 'n++', 'n.value += 1', 'n = n + 1'], 0, L('`update` riceve il valore attuale.', '`update` receives the current value.')),
        ], task('angular', L('Crea `voti = signal(0)` e `doppio = computed(() => this.voti() * 2)`.', 'Create `voti = signal(0)` and `doppio = computed(() => this.voti() * 2)`.'), 'export class VotiComponent {
}', [re('voti\s*=\s*signal\s*\(\s*0\s*\)', L('Il signal esiste', 'The signal exists')), re('doppio\s*=\s*computed\s*\(\s*\(\)\s*=>\s*this\.voti\(\)\s*\*\s*2\s*\)', L('Il valore derivato è giusto', 'The derived value is right'))], '', 'voti = signal(0);
doppio = computed(() => this.voti() * 2);'), [
            dBlanks(pFill(), "nome = ___(\"Ada\");\nsaluto = ___(() => \"Ciao \" + this.nome());\ncambia() { this.nome.___(\"Bea\"); }", ['signal', 'computed', 'set'], ['effect', 'value', 'update'], L('Crea, deriva, imposta.', 'Create, derive, set.')),
            dFlash(pFlash(), [[L('Un signal si legge come una funzione', 'A signal is read like a function'), true], [L('`computed` si modifica con `set`', '`computed` is changed with `set`'), false], [L('`update` riceve il valore precedente', '`update` receives the previous value'), true], [L('Un signal non avvisa nessuno quando cambia', 'A signal tells nobody when it changes'), false]], L('I valori derivati si leggono soltanto.', 'Derived values are read only.')),
        ]),
        lesson('services', L('Servizi e dati', 'Services and data'), [
            P('Un **servizio** contiene la logica condivisa (dati, chiamate al server) ed è segnato con `@Injectable`. I componenti lo ricevono con `inject()`: è la **dependency injection**.', 'A **service** holds shared logic (data, server calls) and is marked with `@Injectable`. Components receive it with `inject()`: it is **dependency injection**.'),
            C('@Injectable({ providedIn: "root" })
export class CorsiService {
  private http = inject(HttpClient);
  elenco() {
    return this.http.get<Corso[]>("/api/corsi");
  }
}

export class ElencoComponent {
  private corsi = inject(CorsiService);
  dati = toSignal(this.corsi.elenco(), { initialValue: [] });
}', 'ts'),
            P('`providedIn: "root"` crea un solo esemplare per tutta l’app. `HttpClient` restituisce un Observable; `toSignal` lo trasforma in un signal da usare nel template.', '`providedIn: "root"` creates a single instance for the whole app. `HttpClient` returns an Observable; `toSignal` turns it into a signal to use in the template.'),
            Fig(L(fig_ngbase_di('it'), fig_ngbase_di('en')),
                L('In alto CorsiService, con providedIn: "root", che riceve HttpClient da sinistra. Due frecce inject() portano lo stesso servizio a ElencoComponent e a DettaglioComponent.', 'At the top CorsiService, with providedIn: "root", receiving HttpClient from the left. Two inject() arrows bring the same service to ElencoComponent and DettaglioComponent.'),
                L('Nessuno crea il servizio con `new`: Angular lo crea una volta e lo consegna a chi lo chiede.', 'Nobody creates the service with `new`: Angular creates it once and hands it to whoever asks.')),
            Tip('Da Angular 22 esiste anche `@Service()`, una scorciatoia di `@Injectable({ providedIn: "root" })` che la CLI usa di default.', 'Since Angular 22 there is also `@Service()`, a shorthand for `@Injectable({ providedIn: "root" })` that the CLI uses by default.'),
        ], [
            Q('v1', L('Quale decoratore segna un servizio?', 'Which decorator marks a service?'), ['@Injectable', '@Component', '@Provider', '@Input'], 0, L('Così Angular può iniettarlo. Da Angular 22 c’è anche la scorciatoia `@Service()`.', 'So Angular can inject it. Since Angular 22 there is also the `@Service()` shorthand.')),
            Q('v2', L('Come riceve un servizio un componente?', 'How does a component receive a service?'), ['inject(Servizio)', 'new Servizio()', 'import Servizio', 'require(Servizio)'], 0, L('Lo fornisce Angular.', 'Angular provides it.')),
            Q('v3', L('Che cosa restituisce `http.get`?', 'What does `http.get` return?'), [L('Un Observable', 'An Observable'), L('Un numero', 'A number'), L('Un componente', 'A component'), L('Niente', 'Nothing')], 0, L('Si ascolta o si converte in signal.', 'You subscribe or turn it into a signal.')),
        ], task('angular', L('Segna `MeteoService` con `@Injectable({ providedIn: "root" })`.', 'Mark `MeteoService` with `@Injectable({ providedIn: "root" })`.'), 'export class MeteoService {}', [re('@Injectable\s*\(\s*\{\s*providedIn\s*:\s*["\']root["\']\s*\}\s*\)\s*export\s+class\s+MeteoService', L('Il servizio è iniettabile in tutta l’app', 'The service is injectable app-wide'))], '', '@Injectable({ providedIn: "root" })
export class MeteoService {}'), [
            dMatch(pMatch(), [['@Injectable', L('Un servizio', 'A service')], ['inject()', L('Riceve una dipendenza', 'Receives a dependency')], ['HttpClient', L('Chiamate al server', 'Server calls')], ['toSignal', L('Observable → signal', 'Observable → signal')]], L('I servizi in Angular.', 'Services in Angular.')),
            dOrder(pSort(), ['@Injectable({ providedIn: "root" })', 'export class NoteService {', '  private http = inject(HttpClient);', '  tutte() { return this.http.get<Nota[]>("/api/note"); }', '}'], L('Decoratore, classe, dipendenza, metodo.', 'Decorator, class, dependency, method.')),
        ]),
        lesson('class-style', L('Classi e stili dinamici', 'Dynamic classes and styles'), [
            P('Con il binding di classe aggiungi o togli una classe CSS secondo una condizione: `[class.attivo]="aperta()"`. Con il binding di stile imposti una proprietà, anche con l’unità: `[style.height.px]="altezza"`.', 'With class binding you add or remove a CSS class based on a condition: `[class.attivo]="aperta()"`. With style binding you set a property, even with its unit: `[style.height.px]="altezza"`.'),
            C('@Component({
  selector: "app-scheda",
  template: `
    <button [class.attivo]="aperta()" (click)="aperta.set(!aperta())">Dettagli</button>
    <div [class]="{ evidenza: importante, muta: !importante }"
         [style.height.px]="aperta() ? 200 : 0">…</div>
  `,
  styles: `.attivo { font-weight: bold; }`,
})
export class SchedaComponent {
  aperta = signal(false);
  importante = true;
}', 'ts'),
            P('`[class]` accetta anche un oggetto `{ nome: condizione }` o una stringa con più classi. Le vecchie direttive `ngClass` e `ngStyle` funzionano ancora, ma oggi si preferiscono questi binding nativi. Gli stili scritti in `styles` valgono solo per il componente.', '`[class]` also accepts an object `{ name: condition }` or a string with several classes. The old `ngClass` and `ngStyle` directives still work, but today these native bindings are preferred. Styles written in `styles` apply only to the component.'),
        ], [
            Q('cs1', L('Che cosa fa `[class.attivo]="aperta()"`?', 'What does `[class.attivo]="aperta()"` do?'), [L('Aggiunge la classe `attivo` quando `aperta()` è vero', 'Adds the `attivo` class when `aperta()` is true'), L('Crea un componente `attivo`', 'Creates an `attivo` component'), L('Colora sempre il bottone di rosso', 'Always colours the button red'), L('Ascolta il clic sul bottone', 'Listens to the click on the button')], 0, L('Quando la condizione diventa falsa, la classe sparisce.', 'When the condition becomes false, the class goes away.')),
            Q('cs2', L('Come imposti l’altezza in pixel da un valore?', 'How do you set the height in pixels from a value?'), ['(style.height)="h"', '[height:px]="h"', '[style.height.px]="h"', '{{ style.height }}'], 2, L('L’unità si scrive dopo il nome della proprietà.', 'The unit goes after the property name.')),
            Q('cs3', L('Che cosa si preferisce oggi a `ngClass`?', 'What is preferred today over `ngClass`?'), [L('Un file CSS globale', 'A global CSS file'), 'document.querySelector', L('Un servizio dedicato', 'A dedicated service'), L('Il binding `[class]`', 'The `[class]` binding')], 3, L('È integrato e non va importato.', 'It is built in and needs no import.')),
        ], task('angular', L('Al `<li>` aggiungi `[class.fatto]="completato"` e un binding `[style.opacity]`.', 'Add `[class.fatto]="completato"` and a `[style.opacity]` binding to the `<li>`.'), '<li>Comprare il pane</li>', [re('<li[^>]*\[class\.fatto\]\s*=\s*"completato"', L('La classe dipende da `completato`', 'The class depends on `completato`')), re('<li[^>]*\[style\.opacity\]\s*=\s*"[^"]+"', L('Lo stile è collegato', 'The style is bound'))], '', '<li [class.fatto]="completato" [style.opacity]="completato ? 0.5 : 1">Comprare il pane</li>'), [
            dMatch(pMatch(), [['[class.x]="c"', L('Una classe secondo una condizione', 'A class based on a condition')], ['[class]="{ a: c }"', L('Più classi con un oggetto', 'Several classes with an object')], ['[style.color]', L('Una proprietà di stile', 'A style property')], ['[style.width.px]', L('Stile con unità', 'Style with a unit')], ['styles', L('CSS solo del componente', 'CSS for the component only')]], L('Classi e stili guidati dai dati.', 'Classes and styles driven by data.')),
            dBug(pBug(), ['<button', '  [class.attiva]="selezionata"', '  [style.width]="200"', '  (click)="scegli()">Scegli</button>'], 2, '  [style.width.px]="200"', L('Senza unità, 200 non è una larghezza CSS valida.', 'Without a unit, 200 is not a valid CSS width.')),
        ]),
        lesson('pipes', L('Le pipe', 'Pipes'), [
            P('Una **pipe** trasforma un valore solo per mostrarlo, direttamente nel template, con il simbolo `|`. Angular ne offre di pronte per date, valute, numeri, maiuscole e JSON. I parametri si passano dopo i due punti e più pipe si possono concatenare.', 'A **pipe** transforms a value just for display, right in the template, with the `|` symbol. Angular offers ready-made ones for dates, currencies, numbers, uppercase and JSON. Parameters go after a colon and several pipes can be chained.'),
            C('import { DatePipe, CurrencyPipe, UpperCasePipe } from "@angular/common";

@Component({
  selector: "app-ordine",
  imports: [DatePipe, CurrencyPipe, UpperCasePipe],
  template: `
    <p>{{ cliente | uppercase }}</p>
    <p>{{ data | date: "dd/MM/yyyy" }}</p>
    <p>{{ totale | currency: "EUR" }}</p>
  `,
})
export class OrdineComponent {
  cliente = "Ada";
  data = new Date();
  totale = 42.5;
}', 'ts', '<p>ADA</p>
<p>18/11/2026</p>
<p>€42.50</p>'),
            C('@Pipe({ name: "iniziali" })
export class InizialiPipe implements PipeTransform {
  transform(nome: string): string {
    return nome.split(" ").map(p => p[0]).join("");
  }
}
// {{ "Ada Lovelace" | iniziali }} → "AL"', 'ts'),
            P('Le pipe vanno messe nell’array `imports` del componente, come ogni altra dipendenza del template. Una pipe è **pura** di default: Angular la ricalcola solo quando cambia il valore in ingresso, quindi è più efficiente di un metodo chiamato nel template.', 'Pipes go in the component’s `imports` array, like any other template dependency. A pipe is **pure** by default: Angular recalculates it only when the input value changes, so it is more efficient than a method called in the template.'),
        ], [
            Q('pp1', L('Con quale simbolo si applica una pipe?', 'Which symbol applies a pipe?'), ['>', '|', '&', '=>'], 1, L('`{{ valore | pipe }}`.', '`{{ value | pipe }}`.')),
            Q('pp2', L('Come passi il formato alla pipe `date`?', 'How do you pass the format to the `date` pipe?'), ['{{ date(d, "dd/MM") }}', '{{ d.date = "dd/MM" }}', '{{ d | date: "dd/MM/yyyy" }}', '{{ d | date("dd/MM") }}'], 2, L('I parametri vanno dopo i due punti.', 'Parameters go after the colon.')),
            Q('pp3', L('Che cosa serve perché il template possa usare `DatePipe`?', 'What is needed for the template to use `DatePipe`?'), [L('Metterla nell’array `imports` del componente', 'Putting it in the component’s `imports` array'), L('Niente: è disponibile ovunque', 'Nothing: it is available everywhere'), L('Scriverla in `index.html`', 'Writing it in `index.html`'), L('Dichiararla nel foglio di stile', 'Declaring it in the stylesheet')], 0, L('I componenti standalone dichiarano ciò che usano.', 'Standalone components declare what they use.')),
        ], task('angular', L('Mostra `prezzo` con la pipe `currency` in euro: `{{ prezzo | currency: "EUR" }}`.', 'Show `prezzo` with the `currency` pipe in euros: `{{ prezzo | currency: "EUR" }}`.'), '<p>{{ prezzo }}</p>', [re('\{\{\s*prezzo\s*\|\s*currency\s*:\s*["\']EUR["\']\s*\}\}', L('Il prezzo è formattato in euro', 'The price is formatted in euros'))], '', '<p>{{ prezzo | currency: "EUR" }}</p>'), [
            dMatch(pMatch(), [['uppercase', L('Tutto maiuscolo', 'All uppercase')], ['date', L('Formatta una data', 'Formats a date')], ['currency', L('Mostra una valuta', 'Shows a currency')], ['json', L('Mostra un oggetto come testo', 'Shows an object as text')], ['percent', L('Mostra una percentuale', 'Shows a percentage')]], L('Le pipe pronte più usate.', 'The most used ready-made pipes.')),
            dBlanks(pFill(), "@___({ name: \"doppio\" })\nexport class DoppioPipe implements PipeTransform {\n  ___(n: number) { return n * 2; }\n}\n// {{ 4 ___ doppio }}", ['Pipe', 'transform', '|'], ['Component', 'convert', '>'], L('Decoratore, metodo `transform`, simbolo `|`.', 'Decorator, `transform` method, `|` symbol.')),
        ]),
        lesson('effects', L('effect e ciclo di vita', 'effect and lifecycle'), [
            P('`computed` calcola valori; quando invece devi **fare qualcosa** al cambiare di un signal (salvare, scrivere un log, aggiornare una libreria esterna) usi `effect()`. Angular lo riesegue da solo ogni volta che cambia un signal letto al suo interno.', '`computed` works out values; when instead you need to **do something** as a signal changes (saving, logging, updating an external library) you use `effect()`. Angular reruns it by itself whenever a signal read inside it changes.'),
            C('export class TemaComponent {
  tema = signal(localStorage.getItem("tema") ?? "chiaro");

  constructor() {
    effect(() => {
      localStorage.setItem("tema", this.tema());
    });
    afterNextRender(() => {
      console.log("Il componente è nel DOM");
    });
    inject(DestroyRef).onDestroy(() => console.log("Componente rimosso"));
  }
}', 'ts'),
            P('`effect` va creato in un **contesto di iniezione**, come il costruttore o l’inizializzazione di un campo. Non usarlo per copiare un signal in un altro: per i valori derivati c’è `computed`. Esistono anche i metodi classici `ngOnInit` e `ngOnDestroy`; `afterNextRender` serve quando devi toccare il DOM dopo il disegno.', '`effect` must be created in an **injection context**, such as the constructor or a field initialiser. Do not use it to copy one signal into another: for derived values there is `computed`. The classic `ngOnInit` and `ngOnDestroy` methods also exist; `afterNextRender` is for when you must touch the DOM after drawing.'),
        ], [
            Q('ef1', L('Quando si riesegue un `effect`?', 'When does an `effect` rerun?'), [L('A ogni clic sulla pagina', 'On every click on the page'), L('Quando cambia un signal letto al suo interno', 'When a signal read inside it changes'), L('Solo una volta, all’avvio', 'Only once, at start-up'), L('Ogni secondo', 'Every second')], 1, L('Angular tiene traccia dei signal che legge.', 'Angular tracks the signals it reads.')),
            Q('ef2', L('Per un valore derivato da mostrare è meglio…', 'For a derived value to show it is better to use…'), ['effect', 'ngOnDestroy', 'afterNextRender', 'computed'], 3, L('`effect` è per le azioni, non per i valori.', '`effect` is for actions, not values.')),
            Q('ef3', L('Dove si può creare un `effect`?', 'Where can you create an `effect`?'), [L('In un file CSS', 'In a CSS file'), L('Dentro il template', 'Inside the template'), L('In un contesto di iniezione, come il costruttore', 'In an injection context, such as the constructor'), L('In qualsiasi callback, anche in un `setTimeout`', 'In any callback, even in a `setTimeout`')], 2, L('Fuori da lì serve passare un `Injector`.', 'Outside it you must pass an `Injector`.')),
        ], task('angular', L('Nel costruttore crea un `effect` che esegue `console.log(this.punti())`.', 'In the constructor create an `effect` that runs `console.log(this.punti())`.'), 'export class PunteggioComponent {
  punti = signal(0);
  constructor() {
  }
}', [re('effect\s*\(\s*\(\s*\)\s*=>', L('Crei un effect', 'You create an effect')), re('console\.log\s*\(\s*this\.punti\s*\(\s*\)\s*\)', L('Leggi il signal', 'You read the signal'))], '', 'constructor() {
  effect(() => {
    console.log(this.punti());
  });
}'), [
            dFlash(pFlash(), [[L('`effect` serve per le azioni collaterali', '`effect` is for side effects'), true], [L('Un `effect` si riesegue quando cambia un signal che legge', 'An `effect` reruns when a signal it reads changes'), true], [L('`effect` è il modo migliore per calcolare un valore derivato', '`effect` is the best way to compute a derived value'), false], [L('`afterNextRender` gira prima che il componente sia nel DOM', '`afterNextRender` runs before the component is in the DOM'), false]], L('Valori con `computed`, azioni con `effect`.', 'Values with `computed`, actions with `effect`.')),
            dMatch(pMatch(), [['computed', L('Valore derivato', 'Derived value')], ['effect', L('Azione al cambio di un signal', 'Action when a signal changes')], ['afterNextRender', L('Dopo il disegno nel DOM', 'After drawing in the DOM')], ['DestroyRef', L('Pulizia alla rimozione', 'Clean-up on removal')]], L('Gli strumenti del ciclo di vita.', 'The lifecycle tools.')),
        ]),
    ],
];
