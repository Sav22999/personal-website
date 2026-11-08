<?php
/** Angular basics: components, templates, bindings, events, control flow and signals. */

return [
    'id' => 'angular-base', 'family' => 'angular', 'after' => 'ts',
    'title' => L('Angular base', 'Angular Basics'), 'short' => L('Angular base', 'Angular Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Componenti, template, binding, eventi, `@if` e `@for` e i signal: le basi del framework di Google.', 'Components, templates, bindings, events, `@if` and `@for` and signals: the basics of Google’s framework.'),
    'lessons' => [
        lesson('components', L('Il primo componente', 'The first component'), [
            P('In Angular l’interfaccia è fatta di **componenti**: una classe TypeScript con il decoratore `@Component`, che indica il **selettore** (il tag da usare nell’HTML) e il **template**.', 'In Angular the interface is made of **components**: a TypeScript class with the `@Component` decorator, which gives the **selector** (the tag to use in HTML) and the **template**.'),
            C('import { Component } from "@angular/core";

@Component({
  selector: "app-saluto",
  standalone: true,
  template: `<h1>Ciao, {{ nome }}!</h1>`,
})
export class SalutoComponent {
  nome = "Ada";
}', 'ts'),
            P('Le doppie graffe `{{ }}` mostrano nel template il valore di una proprietà della classe: è l’**interpolazione**.', 'Double braces `{{ }}` show a class property in the template: it is **interpolation**.'),
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
            P('Dati verso la vista con `[ ]`, eventi verso la classe con `( )`. Insieme, `[(ngModel)]` crea un collegamento nei due sensi per i campi dei moduli.', 'Data to the view with `[ ]`, events to the class with `( )`. Together, `[(ngModel)]` makes a two-way link for form fields.'),
        ], [
            Q('b1', L('Come ascolti il clic?', 'How do you listen to a click?'), ['(click)="fai()"', '[click]="fai()"', 'onclick={fai}', '{{click}}'], 0, L('Tonde per gli eventi.', 'Round brackets for events.')),
            Q('b2', L('Che cosa fa `[disabled]="occupato"`?', 'What does `[disabled]="occupato"` do?'), [L('Collega la proprietà al valore di `occupato`', 'Binds the property to the value of `occupato`'), L('Ascolta un evento', 'Listens to an event'), L('Stampa un testo', 'Prints a text'), L('Crea una variabile', 'Creates a variable')], 0, L('Quadre per le proprietà.', 'Square brackets for properties.')),
            Q('b3', L('Come si chiama `[( )]`?', 'What is `[( )]` called?'), [L('Binding bidirezionale', 'Two-way binding'), L('Pipe', 'Pipe'), L('Direttiva strutturale', 'Structural directive'), L('Decoratore', 'Decorator')], 0, L('La “banana nella scatola”.', 'The “banana in a box”.')),
        ], task('angular', L('Al bottone collega `(click)="salva()"` e `[disabled]="vuoto"`.', 'Bind `(click)="salva()"` and `[disabled]="vuoto"` to the button.'), '<button>Salva</button>', [re('\(click\)\s*=\s*"salva\(\)"', L('Ascolta il clic', 'It listens to the click')), re('\[disabled\]\s*=\s*"vuoto"', L('Si disattiva', 'It disables itself'))], '', '<button (click)="salva()" [disabled]="vuoto">Salva</button>'), [
            dBlanks(pFill(), "<input ___=\"nome\" />\n<button ___=\"invia()\">Invia</button>\n<p>{{ ___ }}</p>", ['[value]', '(click)', 'nome'], ['(value)', '[click]', 'this.nome'], L('Proprietà, evento, interpolazione.', 'Property, event, interpolation.')),
            dFlash(pFlash(), [[L('`(input)` ascolta quando scrivi', '`(input)` listens while you type'), true], [L('`[src]` collega l’attributo src', '`[src]` binds the src attribute'), true], [L('`{{ }}` può chiamare eventi', '`{{ }}` can trigger events'), false]], L('Le graffe mostrano, le tonde ascoltano.', 'Braces show, round brackets listen.')),
        ]),
        lesson('control-flow', L('@if e @for', '@if and @for'), [
            P('Il nuovo **control flow** di Angular scrive condizioni e cicli direttamente nel template: `@if` mostra un blocco solo se la condizione è vera, `@for` ripete un blocco per ogni elemento.', 'Angular’s new **control flow** writes conditions and loops right in the template: `@if` shows a block only if the condition is true, `@for` repeats a block for each item.'),
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
        ], [
            Q('f1', L('Che cosa è obbligatorio in `@for`?', 'What is required in `@for`?'), ['track', 'index', 'key', 'let'], 0, L('Serve a riconoscere gli elementi.', 'It identifies the items.')),
            Q('f2', L('Come mostri un blocco se la lista è vuota?', 'How do you show a block when the list is empty?'), ['@empty', '@none', '@else-for', '@nothing'], 0, L('`@for` ha un suo blocco `@empty`.', '`@for` has its own `@empty` block.')),
            Q('f3', L('`@if` è scritto in…', '`@if` is written in…'), [L('Template', 'The template'), L('Nel CSS', 'The CSS'), L('Nel file di configurazione', 'The config file'), L('Nel database', 'The database')], 0, L('È sintassi del template.', 'It is template syntax.')),
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
        ], [
            Q('s1', L('Come leggi il valore del signal `conta`?', 'How do you read the value of the signal `conta`?'), ['conta()', 'conta.value', 'conta.get', '$conta'], 0, L('Un signal si legge chiamandolo.', 'A signal is read by calling it.')),
            Q('s2', L('Che cosa fa `computed`?', 'What does `computed` do?'), [L('Un valore derivato che si aggiorna da solo', 'A derived value that updates itself'), L('Un ciclo', 'A loop'), L('Una richiesta HTTP', 'An HTTP request'), L('Un componente', 'A component')], 0, L('Dipende da altri signal.', 'It depends on other signals.')),
            Q('s3', L('Come aumenti un signal di 1?', 'How do you increase a signal by 1?'), ['n.update(v => v + 1)', 'n++', 'n.value += 1', 'n = n + 1'], 0, L('`update` riceve il valore attuale.', '`update` receives the current value.')),
        ], task('angular', L('Crea `voti = signal(0)` e `doppio = computed(() => this.voti() * 2)`.', 'Create `voti = signal(0)` and `doppio = computed(() => this.voti() * 2)`.'), 'export class VotiComponent {
}', [re('voti\s*=\s*signal\s*\(\s*0\s*\)', L('Il signal esiste', 'The signal exists')), re('doppio\s*=\s*computed\s*\(\s*\(\)\s*=>\s*this\.voti\(\)\s*\*\s*2\s*\)', L('Il valore derivato è giusto', 'The derived value is right'))], '', 'voti = signal(0);
doppio = computed(() => this.voti() * 2);'), [
            dBlanks(pFill(), "nome = ___(\"Ada\");\nsaluto = ___(() => \"Ciao \" + this.nome());\ncambia() { this.nome.___(\"Bea\"); }", ['signal', 'computed', 'set'], ['effect', 'value', 'update'], L('Crea, deriva, imposta.', 'Create, derive, set.')),
            dFlash(pFlash(), [[L('Un signal si legge come una funzione', 'A signal is read like a function'), true], [L('`computed` si modifica con `set`', '`computed` is changed with `set`'), false], [L('`update` riceve il valore precedente', '`update` receives the previous value'), true]], L('I valori derivati si leggono soltanto.', 'Derived values are read only.')),
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
        ], [
            Q('v1', L('Quale decoratore segna un servizio?', 'Which decorator marks a service?'), ['@Injectable', '@Component', '@Service', '@Input'], 0, L('Così Angular può iniettarlo.', 'So Angular can inject it.')),
            Q('v2', L('Come riceve un servizio un componente?', 'How does a component receive a service?'), ['inject(Servizio)', 'new Servizio()', 'import Servizio', 'require(Servizio)'], 0, L('Lo fornisce Angular.', 'Angular provides it.')),
            Q('v3', L('Che cosa restituisce `http.get`?', 'What does `http.get` return?'), [L('Un Observable', 'An Observable'), L('Un numero', 'A number'), L('Un componente', 'A component'), L('Niente', 'Nothing')], 0, L('Si ascolta o si converte in signal.', 'You subscribe or turn it into a signal.')),
        ], task('angular', L('Segna `MeteoService` con `@Injectable({ providedIn: "root" })`.', 'Mark `MeteoService` with `@Injectable({ providedIn: "root" })`.'), 'export class MeteoService {}', [re('@Injectable\s*\(\s*\{\s*providedIn\s*:\s*["\']root["\']\s*\}\s*\)\s*export\s+class\s+MeteoService', L('Il servizio è iniettabile in tutta l’app', 'The service is injectable app-wide'))], '', '@Injectable({ providedIn: "root" })
export class MeteoService {}'), [
            dMatch(pMatch(), [['@Injectable', L('Un servizio', 'A service')], ['inject()', L('Riceve una dipendenza', 'Receives a dependency')], ['HttpClient', L('Chiamate al server', 'Server calls')], ['toSignal', L('Observable → signal', 'Observable → signal')]], L('I servizi in Angular.', 'Services in Angular.')),
            dOrder(pSort(), ['@Injectable({ providedIn: "root" })', 'export class NoteService {', '  private http = inject(HttpClient);', '  tutte() { return this.http.get<Nota[]>("/api/note"); }', '}'], L('Decoratore, classe, dipendenza, metodo.', 'Decorator, class, dependency, method.')),
        ]),
    ],
];
