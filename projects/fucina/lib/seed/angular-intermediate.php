<?php
/** Angular intermediate: inputs and outputs, routing, reactive forms, RxJS and guards, content projection, lazy loading and @defer, resources, Signal Forms. */

/** Inputs and outputs: data goes down from parent to child through [stelle], events come back up through (cambia). */
if (!function_exists('fig_ngint_io')) {
    function fig_ngint_io(string $l): string
    {
        $t = $l === 'it' ? ['padre', 'figlio', 'dati giù', 'eventi su'] : ['parent', 'child', 'data down', 'events up'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ngia" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="120" y="16" width="400" height="62" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="120" y="162" width="400" height="62" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g font-size="13" font-weight="700" fill="#5B5880"><text x="136" y="40">{$t[0]}</text><text x="136" y="186">{$t[1]} · app-voto</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="136" y="64">voto = signal(3)</text><text x="136" y="210">stelle = input()  ·  cambia = output()</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ngia)"><path d="M220 82V158"/><path d="M420 158V82"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-weight="700"><text x="232" y="116" fill="#1B66BC">[stelle]</text><text x="432" y="116" fill="#B84508">(cambia)</text></g>
<g font-size="13" fill="#5B5880"><text x="232" y="136">{$t[2]}</text><text x="432" y="136">{$t[3]} · \$event</text></g>
</svg>
SVG;
    }
}

/** The router: the address picks a route from the list and its component appears in the router-outlet, while the rest of the app stays. */
if (!function_exists('fig_ngint_router')) {
    function fig_ngint_router(string $l): string
    {
        $t = $l === 'it' ? ['rotte', 'intestazione e menu: restano', 'cambia a ogni indirizzo'] : ['routes', 'header and menu: they stay', 'changes with every address'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ngra" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="12" width="616" height="38" rx="19" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="34" y="37" font-family="ui-monospace, Menlo, monospace" font-size="14">https://fucina.app<tspan font-weight="700" fill="#B84508">/corsi/42</tspan></text>
<rect x="12" y="78" width="250" height="166" rx="14" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="28" y="102" font-size="13" font-weight="700" fill="#5B5880">{$t[0]}</text>
<rect x="24" y="152" width="226" height="34" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="34" y="138">""         → Home</text><text x="34" y="174" font-weight="700">corsi/:id  → Corso</text><text x="34" y="214">**         → NonTrovato</text></g>
<rect x="300" y="78" width="328" height="166" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="316" y="92" width="296" height="34" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="330" y="114" font-size="13">{$t[1]}</text>
<rect x="316" y="140" width="296" height="90" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2" stroke-dasharray="7 4"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="330" y="162" fill="#5B5880">&lt;router-outlet /&gt;</text><text x="330" y="190" font-weight="700">CorsoComponent</text><text x="330" y="210">id = "42"</text></g>
<text x="600" y="222" text-anchor="end" font-size="12" fill="#B84508">{$t[2]}</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ngra)"><path d="M200 50C200 100 150 110 140 148"/><path d="M250 169H312"/></g>
</svg>
SVG;
    }
}

/** A marble diagram: keystrokes, then debounceTime keeping only the last, then switchMap turning it into results; a "now" line sweeps along time. */
if (!function_exists('fig_ngint_rxjs')) {
    function fig_ngint_rxjs(string $l): string
    {
        $t = $l === 'it' ? ['tempo', 'risultati', 'pausa di 300 ms'] : ['time', 'results', '300 ms pause'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.ngx{animation:ngx 6s linear infinite}@keyframes ngx{from{transform:translateX(0)}to{transform:translateX(480px)}}@media (prefers-reduced-motion:reduce){.ngx{animation:none;opacity:0}}</style>
<defs><marker id="ngxa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700"><text x="20" y="40">valueChanges</text><text x="20" y="122" fill="#1B66BC">debounceTime(300)</text><text x="20" y="204" fill="#0B7F5C">switchMap(http.get)</text></g>
<g stroke="#8A87B0" stroke-width="2" fill="none" marker-end="url(#ngxa)"><path d="M20 70H624"/><path d="M20 152H624"/><path d="M20 234H624"/></g>
<text x="620" y="262" text-anchor="end" font-size="13" fill="#5B5880">{$t[0]} →</text>
<g fill="#FFF3EA" stroke="#E8590C" stroke-width="2"><circle cx="140" cy="70" r="20"/><circle cx="200" cy="70" r="20"/><circle cx="260" cy="70" r="20"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="140" y="75">a</text><text x="200" y="75">ad</text><text x="260" y="75">ada</text></g>
<path d="M262 96H378" stroke="#7048E8" stroke-width="2" stroke-dasharray="4 3"/><text x="320" y="112" text-anchor="middle" font-size="12" fill="#5A35C8">{$t[2]}</text>
<circle cx="380" cy="152" r="20" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="380" y="157" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">ada</text>
<rect x="440" y="216" width="110" height="36" rx="18" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="495" y="239" text-anchor="middle" font-size="13" font-weight="700" fill="#0B7F5C">{$t[1]}</text>
<g stroke="#8A87B0" stroke-width="1.5" stroke-dasharray="3 3"><path d="M380 92V130"/><path d="M386 172L470 214"/></g>
<g class="ngx" stroke="#F03E82" stroke-width="3" opacity="0.8"><path d="M120 54V86"/><path d="M120 136V168"/><path d="M120 218V250"/></g>
</svg>
SVG;
    }
}

/** A guard on the way into a route: if the user is logged in the page opens, otherwise the router sends them to /login. */
if (!function_exists('fig_ngint_guard')) {
    function fig_ngint_guard(string $l): string
    {
        $t = $l === 'it' ? ['vai a /profilo', 'loggato?', 'sì', 'no', 'pagina del profilo', 'reindirizza a /login'] : ['go to /profilo', 'logged in?', 'yes', 'no', 'profile page', 'redirect to /login'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ngga" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="84" width="150" height="52" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="87" y="115" text-anchor="middle" font-size="14">{$t[0]}</text>
<path d="M300 60L380 110L300 160L220 110Z" fill="#FFF3EA" stroke="#E8590C" stroke-width="2" stroke-linejoin="round"/>
<text x="300" y="104" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12">soloUtenti</text>
<text x="300" y="124" text-anchor="middle" font-size="13" font-weight="700">{$t[1]}</text>
<rect x="450" y="22" width="178" height="52" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="450" y="146" width="178" height="52" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<g text-anchor="middle" font-size="14"><text x="539" y="53">{$t[4]}</text><text x="539" y="177">{$t[5]}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ngga)"><path d="M162 110H216"/><path d="M350 90C390 60 410 48 446 48"/><path d="M350 130C390 160 410 172 446 172"/></g>
<g font-size="13" font-weight="700" text-anchor="end"><text x="440" y="30" fill="#0B7F5C">{$t[2]} · true</text><text x="440" y="206" fill="#B0245A">{$t[3]} · UrlTree</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'angular-intermediate', 'family' => 'angular', 'after' => 'angular-base',
    'title' => L('Angular intermedio', 'Intermediate Angular'), 'short' => L('Angular intermedio', 'Angular Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Componenti che si parlano, rotte e caricamento pigro, moduli reattivi e Signal Forms, RxJS e resource, guardie per un’app completa.', 'Components that talk, routes and lazy loading, reactive forms and Signal Forms, RxJS and resources, guards for a complete app.'),
    'lessons' => [
        lesson('io', L('Input e output', 'Inputs and outputs'), [
            P('Un componente figlio riceve dati con `input()` e manda eventi al padre con `output()`. Il padre li collega con le quadre e le tonde, come per gli elementi HTML.', 'A child component receives data with `input()` and sends events to the parent with `output()`. The parent binds them with square and round brackets, as with HTML elements.'),
            C('export class VotoComponent {
  stelle = input.required<number>();
  cambia = output<number>();
  scegli(n: number) { this.cambia.emit(n); }
}

<!-- nel padre -->
<app-voto [stelle]="voto()" (cambia)="voto.set($event)" />', 'ts'),
            P('`$event` contiene il valore emesso dal figlio. Il flusso è sempre lo stesso: dati in giù, eventi in su.', '`$event` holds the value emitted by the child. The flow is always the same: data down, events up.'),
            P('Dentro il figlio un input è un signal: lo leggi con `stelle()`, e il figlio non lo modifica mai, perché il dato appartiene al padre. `input.required` obbliga il padre a passarlo, `input(0)` invece ha un valore di default. Per cambiare il dato il figlio chiede al padre con `emit`: così ogni dato ha un solo proprietario ed è facile capire chi lo cambia.', 'Inside the child an input is a signal: you read it with `stelle()`, and the child never changes it, because the data belongs to the parent. `input.required` forces the parent to pass it, while `input(0)` has a default value. To change the data the child asks the parent with `emit`: this way every piece of data has a single owner and it is easy to see who changes it.'),
            Fig(L(fig_ngint_io('it'), fig_ngint_io('en')),
                L('In alto il componente padre con voto = signal(3), in basso il figlio app-voto con stelle = input() e cambia = output(). Una freccia [stelle] scende dal padre al figlio, una freccia (cambia) con $event sale dal figlio al padre.', 'At the top the parent component with voto = signal(3), at the bottom the child app-voto with stelle = input() and cambia = output(). A [stelle] arrow goes down from parent to child, a (cambia) arrow with $event goes up from child to parent.'),
                L('I dati scendono con gli input, gli eventi salgono con gli output.', 'Data goes down through inputs, events go up through outputs.')),
        ], [
            Q('o1', L('Con che cosa un figlio manda un valore al padre?', 'How does a child send a value to the parent?'), ['output().emit()', 'input()', 'return', 'console.log'], 0, L('Il padre ascolta con `( )`.', 'The parent listens with `( )`.')),
            Q('o2', L('Che cosa contiene `$event`?', 'What does `$event` hold?'), [L('Il valore emesso', 'The emitted value'), L('Il componente padre', 'The parent component'), L('Il DOM', 'The DOM'), L('Un errore', 'An error')], 0, L('È ciò che passa `emit`.', 'It is what `emit` passes.')),
            Q('o3', L('Come rendi un input obbligatorio?', 'How do you make an input required?'), ['input.required<T>()', 'input<T>(true)', '@Required', 'input!()'], 0, L('Angular segnala se manca.', 'Angular complains if it is missing.')),
        ], task('angular', L('Nel componente dichiara `titolo = input.required<string>()` e `chiudi = output<void>()`.', 'In the component declare `titolo = input.required<string>()` and `chiudi = output<void>()`.'), 'export class AvvisoComponent {
}', [re('titolo\s*=\s*input\.required<string>\(\)', L('C’è l’input', 'There is the input')), re('chiudi\s*=\s*output<void>\(\)', L('C’è l’output', 'There is the output'))], '', 'titolo = input.required<string>();
chiudi = output<void>();'), [
            dBlanks(pFill(), "<app-voto [___]=\"media()\" (___)=\"salva(\$event)\" />", ['stelle', 'cambia'], ['(stelle)', 'emit', 'value'], L('Quadre per il dato, tonde per l’evento.', 'Brackets for data, parentheses for the event.')),
            dFlash(pFlash(), [[L('I dati scendono dal padre al figlio', 'Data goes down from parent to child'), true], [L('Un figlio può cambiare un input da solo', 'A child can change an input by itself'), false], [L('`emit` manda un valore al padre', '`emit` sends a value to the parent'), true], [L('Un `output()` serve a ricevere dati dal padre', 'An `output()` is for receiving data from the parent'), false]], L('Dati in giù, eventi in su.', 'Data down, events up.')),
        ]),
        lesson('routing', L('Le rotte', 'Routing'), [
            P('Il **router** associa un indirizzo a un componente. Le rotte si elencano in un array; `<router-outlet />` è il punto della pagina dove compare il componente scelto, e `routerLink` crea i collegamenti.', 'The **router** maps an address to a component. Routes are listed in an array; `<router-outlet />` is the spot where the chosen component appears, and `routerLink` makes the links.'),
            C('export const routes: Routes = [
  { path: "", component: HomeComponent },
  { path: "corsi/:id", component: CorsoComponent },
  { path: "**", component: NonTrovatoComponent },
];

<a routerLink="/corsi/42">Apri il corso</a>
<router-outlet />', 'ts'),
            P('`:id` è un parametro: il componente lo legge con `input()` se attivi `withComponentInputBinding()`. `**` cattura ogni indirizzo che non esiste.', '`:id` is a parameter: the component reads it with `input()` if you enable `withComponentInputBinding()`. `**` catches every address that does not exist.'),
            Fig(L(fig_ngint_router('it'), fig_ngint_router('en')),
                L('In alto la barra dell’indirizzo con /corsi/42. A sinistra l’elenco delle rotte, dove è evidenziata corsi/:id → Corso. A destra l’app: l’intestazione resta, mentre nell’area tratteggiata del router-outlet compare CorsoComponent con id = "42".', 'At the top the address bar with /corsi/42. On the left the list of routes, with corsi/:id → Corso highlighted. On the right the app: the header stays, while CorsoComponent with id = "42" appears in the dashed router-outlet area.'),
                L('Cambia solo il contenuto del `router-outlet`: la pagina non si ricarica.', 'Only the content of the `router-outlet` changes: the page does not reload.')),
            P('È una **single-page application**: cliccando un `routerLink` il browser non scarica una pagina nuova, Angular cambia l’indirizzo e sostituisce solo il componente nell’outlet, quindi è tutto più veloce. Le rotte si controllano **in ordine** e vince la prima che corrisponde: per questo `**` va sempre in fondo, altrimenti catturerebbe tutto. Un normale `href` invece ricaricherebbe l’intera app.', 'It is a **single-page application**: when you click a `routerLink` the browser does not download a new page, Angular changes the address and swaps only the component in the outlet, so everything is faster. Routes are checked **in order** and the first match wins: that is why `**` always goes last, otherwise it would catch everything. A plain `href` instead would reload the whole app.'),
        ], [
            Q('r1', L('Dove compare il componente della rotta?', 'Where does the route’s component appear?'), ['<router-outlet />', '<app-root>', '<main>', '<route>'], 0, L('È il segnaposto del router.', 'It is the router’s placeholder.')),
            Q('r2', L('Che cosa significa `:id` in una rotta?', 'What does `:id` mean in a route?'), [L('Un parametro variabile', 'A variable parameter'), L('Un commento', 'A comment'), L('Una classe CSS', 'A CSS class'), L('L’ID del database', 'The database ID')], 0, L('`/corsi/7` dà `id = 7`.', '`/corsi/7` gives `id = 7`.')),
            Q('r3', L('Quale percorso cattura le pagine inesistenti?', 'Which path catches missing pages?'), ['**', '*', '404', '?'], 0, L('Va messo per ultimo.', 'It goes last.')),
        ], task('angular', L('Aggiungi la rotta `{ path: "profilo", component: ProfiloComponent }`.', 'Add the route `{ path: "profilo", component: ProfiloComponent }`.'), 'export const routes: Routes = [
];', [re('path\s*:\s*["\']profilo["\']\s*,\s*component\s*:\s*ProfiloComponent', L('La rotta esiste', 'The route exists'))], '', '{ path: "profilo", component: ProfiloComponent },'), [
            dOrder(pSort(), ['export const routes: Routes = [', '  { path: "", component: HomeComponent },', '  { path: "corsi/:id", component: CorsoComponent },', '  { path: "**", component: NonTrovatoComponent },', '];'], L('La rotta jolly va per ultima.', 'The wildcard route goes last.')),
            dMatch(pMatch(), [['routerLink', L('Collegamento interno', 'Internal link')], ['router-outlet', L('Dove si mostra la pagina', 'Where the page shows')], [':id', L('Parametro', 'Parameter')], ['**', L('Pagina non trovata', 'Page not found')]], L('I pezzi del router.', 'The parts of the router.')),
        ]),
        lesson('forms', L('Moduli reattivi', 'Reactive forms'), [
            P('Con i **moduli reattivi** il modulo è descritto nella classe: `FormGroup` raccoglie i campi, `FormControl` è un campo con il suo valore e i suoi **validatori**.', 'With **reactive forms** the form is described in the class: `FormGroup` gathers the fields, `FormControl` is a field with its value and its **validators**.'),
            C('modulo = new FormGroup({
  email: new FormControl("", [Validators.required, Validators.email]),
  eta: new FormControl(18, Validators.min(14)),
});

<form [formGroup]="modulo" (ngSubmit)="invia()">
  <input formControlName="email" />
  <button [disabled]="modulo.invalid">Invia</button>
</form>', 'ts'),
            P('`modulo.invalid` è vero finché un campo non rispetta le regole: il bottone si attiva solo quando tutto è a posto.', '`modulo.invalid` is true as long as a field breaks a rule: the button turns on only when everything is right.'),
            P('Ogni campo ha anche uno stato: `touched` diventa vero quando l’utente esce dal campo, `dirty` quando ne cambia il valore. Mostra i messaggi d’errore solo dopo `touched`, altrimenti il modulo appare pieno di errori prima ancora di iniziare. Il valore completo si legge con `modulo.value`, già pronto come oggetto da mandare al server.', 'Every field also has a state: `touched` becomes true when the user leaves the field, `dirty` when its value changes. Show error messages only after `touched`, otherwise the form looks full of errors before the user has even started. The whole value is read with `modulo.value`, already shaped as an object to send to the server.'),
        ], [
            Q('m1', L('Quale classe raccoglie i campi?', 'Which class gathers the fields?'), ['FormGroup', 'FormControl', 'Validators', 'NgModel'], 0, L('`FormControl` è un singolo campo.', '`FormControl` is a single field.')),
            Q('m2', L('Come colleghi un input a un campo?', 'How do you link an input to a field?'), ['formControlName="email"', '[email]', 'name="email"', 'ngField="email"'], 0, L('Il nome deve esistere nel gruppo.', 'The name must exist in the group.')),
            Q('m3', L('Quale validatore controlla un’email?', 'Which validator checks an email?'), ['Validators.email', 'Validators.mail', 'Validators.at', 'Validators.text'], 0, L('Si combina con `required`.', 'It combines with `required`.')),
        ], task('angular', L('Crea `nome: new FormControl("", Validators.required)` dentro il `FormGroup`.', 'Create `nome: new FormControl("", Validators.required)` inside the `FormGroup`.'), 'modulo = new FormGroup({
});', [re('nome\s*:\s*new\s+FormControl\s*\(\s*["\']["\']\s*,\s*\[?\s*Validators\.required', L('Il campo è obbligatorio', 'The field is required'))], '', 'modulo = new FormGroup({
  nome: new FormControl("", Validators.required),
});'), [
            dBlanks(pFill(), "<form [___]=\"modulo\" (___)=\"invia()\">\n  <input ___=\"email\" />\n</form>", ['formGroup', 'ngSubmit', 'formControlName'], ['ngModel', 'submit', 'name'], L('Gruppo, invio, campo.', 'Group, submit, field.')),
            dFlash(pFlash(), [[L('`modulo.valid` è vero se tutti i campi sono a posto', '`modulo.valid` is true when every field is fine'), true], [L('I validatori si scrivono nel template', 'Validators are written in the template'), false], [L('Un campo può avere più validatori', 'A field can have several validators'), true], [L('`Validators.required` rifiuta un campo vuoto', '`Validators.required` rejects an empty field'), true]], L('Le regole vivono nella classe.', 'The rules live in the class.')),
        ]),
        lesson('rxjs', L('RxJS e Observable', 'RxJS and Observables'), [
            P('Un **Observable** è un flusso di valori nel tempo: una richiesta, i tasti premuti, un timer. Gli **operatori** di RxJS lo trasformano in una catena con `pipe`.', 'An **Observable** is a stream of values over time: a request, keystrokes, a timer. RxJS **operators** transform it in a chain with `pipe`.'),
            C('this.ricerca.valueChanges.pipe(
  debounceTime(300),
  map(t => t.trim()),
  filter(t => t.length > 1),
  switchMap(t => this.http.get<Corso[]>(`/api/cerca?q=${t}`)),
).subscribe(risultati => this.trovati.set(risultati));', 'ts'),
            P('`debounceTime` aspetta che l’utente smetta di scrivere, `switchMap` annulla la richiesta vecchia quando ne parte una nuova.', '`debounceTime` waits until the user stops typing, `switchMap` cancels the old request when a new one starts.'),
            Fig(L(fig_ngint_rxjs('it'), fig_ngint_rxjs('en')),
                L('Diagramma a biglie su tre linee del tempo. In alto i tasti premuti producono a, ad, ada. Al centro, dopo una pausa di 300 ms, debounceTime lascia passare solo ada. In basso switchMap trasforma ada nei risultati della richiesta.', 'Marble diagram on three timelines. At the top the keystrokes produce a, ad, ada. In the middle, after a 300 ms pause, debounceTime lets only ada through. At the bottom switchMap turns ada into the results of the request.'),
                L('Ogni operatore riceve un flusso e ne restituisce un altro: da tre tasti a una sola richiesta.', 'Every operator takes a stream and gives back another: from three keystrokes to a single request.')),
            P('A differenza di una promise, un Observable può emettere **molti** valori nel tempo, ed è pigro: non parte finché qualcuno non chiama `subscribe`. Chi si iscrive deve anche disiscriversi, altrimenti l’ascolto continua anche dopo che il componente è sparito. Le strade comode sono `toSignal`, che lo fa da solo, oppure l’operatore `takeUntilDestroyed()`.', 'Unlike a promise, an Observable can emit **many** values over time, and it is lazy: it does not start until someone calls `subscribe`. Whoever subscribes must also unsubscribe, otherwise the listening goes on even after the component has gone. The easy ways are `toSignal`, which does it by itself, or the `takeUntilDestroyed()` operator.'),
        ], [
            Q('x1', L('Che cosa fa `debounceTime(300)`?', 'What does `debounceTime(300)` do?'), [L('Aspetta 300 ms di pausa', 'Waits for a 300 ms pause'), L('Ripete ogni 300 ms', 'Repeats every 300 ms'), L('Ferma tutto', 'Stops everything'), L('Accelera', 'Speeds up')], 0, L('Evita una richiesta per ogni tasto.', 'It avoids a request per keystroke.')),
            Q('x2', L('Con che cosa si concatenano gli operatori?', 'How are operators chained?'), ['pipe()', 'then()', 'chain()', 'map()'], 0, L('`pipe` riceve gli operatori in ordine.', '`pipe` takes the operators in order.')),
            Q('x3', L('Perché `switchMap` nella ricerca?', 'Why `switchMap` in a search?'), [L('Annulla la richiesta precedente', 'It cancels the previous request'), L('Ordina i risultati', 'It sorts the results'), L('Salva nel database', 'It saves to the database'), L('Non serve', 'It is not needed')], 0, L('Conta solo l’ultima ricerca.', 'Only the latest search counts.')),
        ], task('angular', L('Aggiungi `debounceTime(300)` dentro la `pipe`.', 'Add `debounceTime(300)` inside the `pipe`.'), 'testo$.pipe(
  map(t => t.trim()),
).subscribe(console.log);', [re('pipe\s*\(\s*debounceTime\s*\(\s*300\s*\)', L('Aspetta la pausa per primo', 'It waits for the pause first'))], '', 'testo$.pipe(
  debounceTime(300),
  map(t => t.trim()),
).subscribe(console.log);'), [
            dOrder(pSort(), ['campo.valueChanges.pipe(', '  debounceTime(300),', '  filter(t => t.length > 1),', '  switchMap(t => cerca(t)),', ').subscribe(mostra);'], L('Aspetta, filtra, cerca, mostra.', 'Wait, filter, search, show.')),
            dMatch(pMatch(), [['map', L('Trasforma ogni valore', 'Transforms each value')], ['filter', L('Lascia passare solo alcuni', 'Lets only some through')], ['debounceTime', L('Aspetta una pausa', 'Waits for a pause')], ['switchMap', L('Passa a un nuovo flusso', 'Switches to a new stream')]], L('Gli operatori più usati.', 'The most used operators.')),
        ]),
        lesson('guards', L('Guardie e intercettori', 'Guards and interceptors'), [
            P('Una **guardia** decide se si può entrare in una rotta: `canActivate` restituisce `true`, `false` o un altro indirizzo. Un **intercettore** modifica tutte le richieste HTTP, per esempio aggiungendo un token.', 'A **guard** decides whether you may enter a route: `canActivate` returns `true`, `false` or another address. An **interceptor** changes every HTTP request, for example adding a token.'),
            C('export const soloUtenti: CanActivateFn = () => {
  const auth = inject(AuthService);
  return auth.loggato() ? true : inject(Router).parseUrl("/login");
};

{ path: "profilo", component: ProfiloComponent, canActivate: [soloUtenti] }

export const conToken: HttpInterceptorFn = (req, next) =>
  next(req.clone({ setHeaders: { Authorization: `Bearer ${token()}` } }));', 'ts'),
            P('Le guardie migliorano l’esperienza, ma la sicurezza vera resta sul server: ogni API deve controllare chi la chiama.', 'Guards improve the experience, but real security stays on the server: every API must check who calls it.'),
            Fig(L(fig_ngint_guard('it'), fig_ngint_guard('en')),
                L('La navigazione verso /profilo passa per il guard soloUtenti, che chiede se l’utente è loggato. Se sì restituisce true e si apre la pagina del profilo; se no restituisce un UrlTree che reindirizza a /login.', 'Navigation to /profilo goes through the soloUtenti guard, which asks whether the user is logged in. If yes it returns true and the profile page opens; if not it returns a UrlTree that redirects to /login.'),
                L('Il guard sta all’ingresso della rotta e decide dove si va.', 'The guard stands at the entrance of the route and decides where you go.')),
            P('Guard e interceptor sono semplici funzioni che possono usare `inject()`. Un guard si aggiunge alla rotta, come nell’esempio; un interceptor si registra una volta all’avvio con `provideHttpClient(withInterceptors([conToken]))` e da lì vale per tutte le richieste. `req.clone()` serve perché una richiesta non si modifica: se ne crea una copia con le aggiunte.', 'Guards and interceptors are simple functions that can use `inject()`. A guard is added to the route, as in the example; an interceptor is registered once at start-up with `provideHttpClient(withInterceptors([conToken]))` and from then on it applies to every request. `req.clone()` is needed because a request cannot be changed: you create a copy with the additions.'),
        ], [
            Q('g1', L('Dove si mette una guardia?', 'Where do you put a guard?'), [L('Nella definizione della rotta', 'In the route definition'), L('Nel CSS', 'In the CSS'), L('Nel template', 'In the template'), L('Nel database', 'In the database')], 0, L('`canActivate: [guardia]`.', '`canActivate: [guard]`.')),
            Q('g2', L('A che cosa serve un intercettore?', 'What is an interceptor for?'), [L('A modificare tutte le richieste HTTP', 'To change every HTTP request'), L('A disegnare componenti', 'To draw components'), L('A tradurre testi', 'To translate texts'), L('A creare rotte', 'To create routes')], 0, L('Per esempio aggiunge l’intestazione `Authorization`.', 'For example it adds the `Authorization` header.')),
            Q('g3', L('Una guardia basta a proteggere i dati?', 'Is a guard enough to protect data?'), [L('No, serve il controllo sul server', 'No, the server must check'), L('Sì, sempre', 'Yes, always'), L('Solo in produzione', 'Only in production'), L('Solo con HTTPS', 'Only with HTTPS')], 0, L('Il codice del browser si può aggirare.', 'Browser code can be bypassed.')),
        ], task('angular', L('Proteggi la rotta `admin` con `canActivate: [soloAdmin]`.', 'Protect the `admin` route with `canActivate: [soloAdmin]`.'), '{ path: "admin", component: AdminComponent }', [re('path\s*:\s*["\']admin["\'][^}]*canActivate\s*:\s*\[\s*soloAdmin\s*\]', L('La rotta è protetta', 'The route is protected'))], '', '{ path: "admin", component: AdminComponent, canActivate: [soloAdmin] }'), [
            dBug(pBug(), ['export const soloUtenti: CanActivateFn = () => {', '  const auth = inject(AuthService);', '  return auth.loggato() ? "/login" : true;', '};'], 2, '  return auth.loggato() ? true : inject(Router).parseUrl("/login");', L('Chi è dentro passa, gli altri vanno al login.', 'Signed-in users pass, the others go to login.')),
            dFlash(pFlash(), [[L('`canActivate` può restituire un altro indirizzo', '`canActivate` can return another address'), true], [L('Un intercettore vede tutte le richieste HTTP', 'An interceptor sees every HTTP request'), true], [L('Con una guardia il server non deve controllare', 'With a guard the server need not check'), false], [L('Una guardia può bloccare l’accesso a una rotta', 'A guard can block access to a route'), true]], L('La sicurezza vera è sul server.', 'Real security is on the server.')),
        ]),
        lesson('projection', L('Proiezione dei contenuti', 'Content projection'), [
            P('Un componente può ricevere **HTML dal padre**, non solo dati: nel suo template scrivi `<ng-content />` dove quel contenuto deve comparire. È il modo di creare contenitori riutilizzabili come schede, finestre e pannelli.', 'A component can receive **HTML from its parent**, not just data: in its template you write `<ng-content />` where that content should appear. It is how you build reusable containers like cards, dialogs and panels.'),
            C('@Component({
  selector: "app-scheda",
  template: `
    <article class="scheda">
      <header><ng-content select="[titolo]" /></header>
      <ng-content />
    </article>
  `,
})
export class SchedaComponent {}

<!-- nel padre -->
<app-scheda>
  <h2 titolo>Corso di Angular</h2>
  <p>Dieci lezioni, dalle basi alle rotte.</p>
</app-scheda>', 'ts'),
            P('Con `select` crei più «slot»: il contenuto che corrisponde al selettore va lì, il resto finisce nell’`<ng-content />` senza selettore. Il contenuto proiettato appartiene al padre: usa i suoi dati e i suoi binding.', 'With `select` you create several “slots”: content matching the selector goes there, the rest ends up in the `<ng-content />` without a selector. Projected content belongs to the parent: it uses the parent’s data and bindings.'),
        ], [
            Q('pj1', L('Che cosa fa `<ng-content />`?', 'What does `<ng-content />` do?'), [L('Carica un file esterno', 'Loads an external file'), L('Mostra l’HTML passato dal padre', 'Shows the HTML passed by the parent'), L('Crea una rotta', 'Creates a route'), L('Svuota il componente', 'Empties the component')], 1, L('Il padre scrive il contenuto tra i tag del componente.', 'The parent writes the content between the component’s tags.')),
            Q('pj2', L('A che cosa serve `select="[titolo]"`?', 'What is `select="[titolo]"` for?'), [L('A scegliere quale parte del contenuto va in quello slot', 'To choose which part of the content goes in that slot'), L('A dare uno stile al titolo', 'To style the title'), L('A rendere obbligatorio il titolo', 'To make the title required'), L('A tradurre il titolo', 'To translate the title')], 0, L('Gli elementi con l’attributo `titolo` finiscono lì.', 'Elements with the `titolo` attribute end up there.')),
            Q('pj3', L('Di chi sono i dati usati nel contenuto proiettato?', 'Whose data does projected content use?'), [L('Del figlio che lo mostra', 'The child that shows it'), L('Di un servizio globale', 'A global service'), L('Del padre che lo scrive', 'The parent that writes it'), L('Del router', 'The router')], 2, L('Il figlio decide solo dove mostrarlo.', 'The child only decides where to show it.')),
        ], task('angular', L('Nel template di `app-pannello` aggiungi `<ng-content />` dentro il `<div>`.', 'In the `app-pannello` template add `<ng-content />` inside the `<div>`.'), '@Component({
  selector: "app-pannello",
  template: `<div class="pannello"></div>`,
})', [re('<div[^>]*>\s*<ng-content\s*/?>(\s*</ng-content>)?\s*</div>', L('Il contenuto del padre compare nel div', 'The parent’s content appears in the div'))], '', '@Component({
  selector: "app-pannello",
  template: `<div class="pannello"><ng-content /></div>`,
})'), [
            dBlanks(pFill(), "template: `\n  <header><___ select=\"[titolo]\" /></header>\n  <ng-content />\n`\n<!-- padre -->\n<app-scheda><h2 ___>Ciao</h2></app-scheda>", ['ng-content', 'titolo'], ['slot', '[titolo]', 'ng-template'], L('Lo slot con `select` e l’attributo che lo riempie.', 'The slot with `select` and the attribute that fills it.')),
            dFlash(pFlash(), [[L('`<ng-content />` mostra l’HTML scritto dal padre', '`<ng-content />` shows the HTML written by the parent'), true], [L('Un componente può avere più slot con `select`', 'A component can have several slots with `select`'), true], [L('Il contenuto proiettato usa i dati del figlio', 'Projected content uses the child’s data'), false], [L('`ng-content` funziona solo con il testo', '`ng-content` only works with text'), false]], L('Contenitori riutilizzabili, contenuto del padre.', 'Reusable containers, parent’s content.')),
        ]),
        lesson('lazy-defer', L('Caricamento pigro e @defer', 'Lazy loading and @defer'), [
            P('Un’app grande non deve scaricare tutto all’avvio. Con `loadComponent` una rotta carica il suo componente solo quando qualcuno la visita; con `loadChildren` carica un intero gruppo di rotte.', 'A big app should not download everything at start-up. With `loadComponent` a route loads its component only when someone visits it; with `loadChildren` it loads a whole group of routes.'),
            C('export const routes: Routes = [
  { path: "", component: HomeComponent },
  {
    path: "admin",
    loadComponent: () => import("./admin/admin.component").then(m => m.AdminComponent),
  },
  {
    path: "negozio",
    loadChildren: () => import("./negozio/negozio.routes").then(m => m.ROTTE_NEGOZIO),
  },
];', 'ts'),
            C('@defer (on viewport) {
  <app-grafico [dati]="vendite()" />
} @placeholder {
  <p>Il grafico apparirà qui.</p>
} @loading (minimum 300ms) {
  <p>Caricamento…</p>
}', 'html'),
            P('`@defer` rimanda il caricamento di una parte del template: il codice di `app-grafico` si scarica quando il blocco entra nello schermo (`on viewport`), al clic (`on interaction`) o quando il browser è libero (`on idle`, il predefinito). `@placeholder` si vede prima, `@loading` durante lo scaricamento.', '`@defer` postpones loading part of the template: the code of `app-grafico` is downloaded when the block enters the screen (`on viewport`), on click (`on interaction`) or when the browser is idle (`on idle`, the default). `@placeholder` shows before, `@loading` while downloading.'),
        ], [
            Q('lz1', L('Che cosa fa `loadComponent`?', 'What does `loadComponent` do?'), [L('Carica tutti i componenti all’avvio', 'Loads every component at start-up'), L('Crea un componente vuoto', 'Creates an empty component'), L('Duplica la rotta', 'Duplicates the route'), L('Carica il componente solo quando si visita la rotta', 'Loads the component only when the route is visited')], 3, L('Il primo download è più leggero.', 'The first download is lighter.')),
            Q('lz2', L('Quando si carica `@defer (on viewport)`?', 'When does `@defer (on viewport)` load?'), [L('All’avvio dell’app', 'At app start-up'), L('Quando il blocco entra nello schermo', 'When the block enters the screen'), L('Dopo dieci secondi', 'After ten seconds'), L('Mai', 'Never')], 1, L('Perfetto per i contenuti in fondo alla pagina.', 'Perfect for content at the bottom of the page.')),
            Q('lz3', L('Che cosa mostra `@placeholder`?', 'What does `@placeholder` show?'), [L('Il contenuto prima che il blocco venga caricato', 'The content before the block is loaded'), L('Un errore di rete', 'A network error'), L('Il codice sorgente', 'The source code'), L('La rotta precedente', 'The previous route')], 0, L('`@loading` invece compare durante lo scaricamento.', '`@loading` instead shows while downloading.')),
        ], task('angular', L('Rendi pigra la rotta `profilo` con `loadComponent: () => import("./profilo/profilo.component").then(m => m.ProfiloComponent)`.', 'Make the `profilo` route lazy with `loadComponent: () => import("./profilo/profilo.component").then(m => m.ProfiloComponent)`.'), '{ path: "profilo", component: ProfiloComponent }', [re('path\s*:\s*["\']profilo["\']', L('La rotta è `profilo`', 'The route is `profilo`')), re('loadComponent\s*:\s*\(\s*\)\s*=>\s*import\s*\(\s*["\'][^"\']*profilo[^"\']*["\']\s*\)', L('Il componente si carica con `import()`', 'The component loads with `import()`'))], '', '{
  path: "profilo",
  loadComponent: () => import("./profilo/profilo.component").then(m => m.ProfiloComponent),
}'), [
            dMatch(pMatch(), [['loadComponent', L('Un componente pigro per una rotta', 'A lazy component for a route')], ['loadChildren', L('Un gruppo di rotte pigre', 'A group of lazy routes')], ['@defer', L('Rimanda una parte del template', 'Postpones part of the template')], ['@placeholder', L('Prima del caricamento', 'Before loading')], ['@loading', L('Durante il caricamento', 'While loading')]], L('Scarica solo quello che serve, quando serve.', 'Download only what is needed, when it is needed.')),
            dOrder(pSort(), ['@defer (on interaction) {', '  <app-commenti />', '} @placeholder {', '  <button>Mostra i commenti</button>', '}'], L('Prima il blocco rimandato, poi il segnaposto.', 'First the deferred block, then the placeholder.')),
        ]),
        lesson('resource', L('Dati asincroni con resource', 'Async data with resource'), [
            P('Le **resource** portano i dati asincroni nel mondo dei signal. `httpResource` fa una richiesta HTTP e la ripete da sola quando cambiano i signal usati per costruire l’indirizzo. Il risultato espone signal pronti per il template: `value()`, `isLoading()`, `error()` e `hasValue()`.', '**Resources** bring async data into the world of signals. `httpResource` makes an HTTP request and repeats it by itself when the signals used to build the address change. The result exposes signals ready for the template: `value()`, `isLoading()`, `error()` and `hasValue()`.'),
            C('export class CorsoComponent {
  id = input.required<string>();
  corso = httpResource<Corso>(() => `/api/corsi/${this.id()}`);
}

<!-- template -->
@if (corso.isLoading()) {
  <p>Caricamento…</p>
} @else if (corso.error()) {
  <p>Impossibile caricare il corso.</p>
} @else if (corso.hasValue()) {
  <h1>{{ corso.value().titolo }}</h1>
}', 'ts'),
            P('Per qualsiasi altra operazione asincrona c’è `resource({ params, loader })`, e `rxResource` per gli Observable. Da Angular 22 queste API sono stabili. Se i parametri cambiano prima della risposta, la richiesta vecchia viene scartata, come con `switchMap`.', 'For any other async operation there is `resource({ params, loader })`, and `rxResource` for Observables. Since Angular 22 these APIs are stable. If the parameters change before the response, the old request is discarded, as with `switchMap`.'),
        ], [
            Q('rs1', L('Quando `httpResource` ripete la richiesta?', 'When does `httpResource` repeat the request?'), [L('A ogni clic sulla pagina', 'On every click on the page'), L('Quando cambiano i signal usati per costruire l’indirizzo', 'When the signals used to build the address change'), L('Ogni minuto', 'Every minute'), L('Solo se ricarichi la pagina', 'Only if you reload the page')], 1, L('Come un `computed`, ma asincrono.', 'Like a `computed`, but asynchronous.')),
            Q('rs2', L('Quale signal dice che i dati stanno arrivando?', 'Which signal says the data is on its way?'), ['value()', 'error()', 'isLoading()', 'hasValue()'], 2, L('Utile per mostrare un messaggio di attesa.', 'Handy for showing a waiting message.')),
            Q('rs3', L('Che cosa usi per caricare dati da una promessa qualsiasi?', 'What do you use to load data from any promise?'), ['httpResource', 'computed', L('`effect` con un `setTimeout`', '`effect` with a `setTimeout`'), 'resource({ params, loader })'], 3, L('`httpResource` è solo per le richieste HTTP.', '`httpResource` is only for HTTP requests.')),
        ], task('angular', L('Crea `meteo` con `httpResource<Meteo>()` che chiama `/api/meteo/` seguito da `this.citta()`.', 'Create `meteo` with `httpResource<Meteo>()` calling `/api/meteo/` followed by `this.citta()`.'), 'export class MeteoComponent {
  citta = input.required<string>();
}', [re('meteo\s*=\s*httpResource\s*(<\s*Meteo\s*>)?\s*\(\s*\(\s*\)\s*=>', L('Crei la resource', 'You create the resource')), re('this\.citta\s*\(\s*\)', L('L’indirizzo dipende da `citta`', 'The address depends on `citta`'))], '', 'meteo = httpResource<Meteo>(() => `/api/meteo/${this.citta()}`);'), [
            dMatch(pMatch(), [['value()', L('Il dato caricato', 'The loaded data')], ['isLoading()', L('Sta caricando?', 'Is it loading?')], ['error()', L('L’errore, se c’è', 'The error, if any')], ['httpResource', L('Richiesta HTTP come signal', 'HTTP request as a signal')], ['rxResource', L('Da un Observable', 'From an Observable')]], L('I pezzi di una resource.', 'The parts of a resource.')),
            dFlash(pFlash(), [[L('Una resource si aggiorna quando cambiano i suoi parametri', 'A resource updates when its parameters change'), true], [L('`httpResource` restituisce una Promise', '`httpResource` returns a Promise'), false], [L('Nel template si può controllare `isLoading()`', 'The template can check `isLoading()`'), true], [L('Con le resource bisogna sempre chiamare `subscribe`', 'With resources you must always call `subscribe`'), false]], L('Dati asincroni, letti come signal.', 'Async data, read as signals.')),
        ]),
        lesson('signal-forms', L('Signal Forms', 'Signal Forms'), [
            P('Le **Signal Forms**, stabili da Angular 22, costruiscono un modulo a partire da un signal con i dati. `form()` crea un albero di campi che segue la forma del modello; le regole di validazione si scrivono in uno **schema** con funzioni come `required` ed `email`.', '**Signal Forms**, stable since Angular 22, build a form from a signal holding the data. `form()` creates a tree of fields that follows the model’s shape; validation rules go in a **schema** with functions like `required` and `email`.'),
            C('import { form, FormField, required, email } from "@angular/forms/signals";

@Component({
  selector: "app-iscrizione",
  imports: [FormField],
  template: `
    <input type="email" [formField]="modulo.email" />
    @if (modulo.email().touched() && modulo.email().invalid()) {
      <p>Email non valida</p>
    }
    <button [disabled]="modulo().invalid()">Iscriviti</button>
  `,
})
export class IscrizioneComponent {
  dati = signal({ email: "", nome: "" });
  modulo = form(this.dati, campo => {
    required(campo.email);
    email(campo.email);
  });
}', 'ts'),
            P('Ogni campo si legge come un signal: `modulo.email()` dà il suo stato, con `value()`, `invalid()`, `touched()` ed `errors()`. Scrivere nel campo aggiorna direttamente il signal `dati`. I moduli reattivi con `FormGroup` restano supportati, ma nei progetti nuovi le Signal Forms sono più semplici.', 'Each field is read like a signal: `modulo.email()` gives its state, with `value()`, `invalid()`, `touched()` and `errors()`. Typing in the field updates the `dati` signal directly. Reactive forms with `FormGroup` are still supported, but in new projects Signal Forms are simpler.'),
        ], [
            Q('sf1', L('Da che cosa parte una Signal Form?', 'What does a Signal Form start from?'), [L('Da un FormGroup', 'From a FormGroup'), L('Da un file JSON', 'From a JSON file'), L('Da un signal con i dati del modello', 'From a signal with the model data'), L('Da un servizio HTTP', 'From an HTTP service')], 2, L('`form(this.dati, …)`.', '`form(this.dati, …)`.')),
            Q('sf2', L('In una Signal Form, quale direttiva collega un input a un campo?', 'In a Signal Form, which directive binds an input to a field?'), ['[formField]', 'formControlName', '[(ngModel)]', '[field-name]'], 0, L('`formControlName` appartiene ai moduli reattivi.', '`formControlName` belongs to reactive forms.')),
            Q('sf3', L('Dove si scrivono le regole come `required`?', 'Where do rules like `required` go?'), [L('Nel template', 'In the template'), L('Nello schema passato a `form()`', 'In the schema passed to `form()`'), L('Nel CSS', 'In the CSS'), L('In `main.ts`', 'In `main.ts`')], 1, L('Lo schema riceve i percorsi dei campi.', 'The schema receives the field paths.')),
        ], task('angular', L('Nello schema rendi obbligatorio il campo `nome` con `required(campo.nome)`.', 'In the schema make the `nome` field required with `required(campo.nome)`.'), 'modulo = form(this.dati, campo => {
});', [re('required\s*\(\s*campo\.nome\s*\)', L('Il nome è obbligatorio', 'The name is required'))], '', 'modulo = form(this.dati, campo => {
  required(campo.nome);
});'), [
            dBlanks(pFill(), "dati = ___({ email: \"\" });\nmodulo = ___(this.dati, c => {\n  required(c.email);\n});\n<input [___]=\"modulo.email\" />", ['signal', 'form', 'formField'], ['FormGroup', 'formControlName', 'ngModel'], L('Modello, modulo, collegamento.', 'Model, form, binding.')),
            dFlash(pFlash(), [[L('Scrivere nell’input aggiorna il signal del modello', 'Typing in the input updates the model signal'), true], [L('Le regole delle Signal Forms si scrivono nel template', 'Signal Forms rules are written in the template'), false], [L('`modulo.email().invalid()` dice se il campo non è valido', '`modulo.email().invalid()` says whether the field is invalid'), true], [L('Le Signal Forms richiedono RxJS', 'Signal Forms require RxJS'), false]], L('Moduli fatti di signal.', 'Forms made of signals.')),
        ]),
    ],
];
