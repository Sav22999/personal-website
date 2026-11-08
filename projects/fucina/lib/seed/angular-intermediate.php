<?php
/** Angular intermediate: inputs and outputs, routing, reactive forms, RxJS and guards. */

return [
    'id' => 'angular-intermediate', 'family' => 'angular', 'after' => 'angular-base',
    'title' => L('Angular intermedio', 'Intermediate Angular'), 'short' => L('Angular intermedio', 'Angular Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Componenti che si parlano, rotte, moduli reattivi, RxJS e guardie per un’app completa.', 'Components that talk, routes, reactive forms, RxJS and guards for a complete app.'),
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
        ], [
            Q('o1', L('Con che cosa un figlio manda un valore al padre?', 'How does a child send a value to the parent?'), ['output().emit()', 'input()', 'return', 'console.log'], 0, L('Il padre ascolta con `( )`.', 'The parent listens with `( )`.')),
            Q('o2', L('Che cosa contiene `$event`?', 'What does `$event` hold?'), [L('Il valore emesso', 'The emitted value'), L('Il componente padre', 'The parent component'), L('Il DOM', 'The DOM'), L('Un errore', 'An error')], 0, L('È ciò che passa `emit`.', 'It is what `emit` passes.')),
            Q('o3', L('Come rendi un input obbligatorio?', 'How do you make an input required?'), ['input.required<T>()', 'input<T>(true)', '@Required', 'input!()'], 0, L('Angular segnala se manca.', 'Angular complains if it is missing.')),
        ], task('angular', L('Nel componente dichiara `titolo = input.required<string>()` e `chiudi = output<void>()`.', 'In the component declare `titolo = input.required<string>()` and `chiudi = output<void>()`.'), 'export class AvvisoComponent {
}', [re('titolo\s*=\s*input\.required<string>\(\)', L('C’è l’input', 'There is the input')), re('chiudi\s*=\s*output<void>\(\)', L('C’è l’output', 'There is the output'))], '', 'titolo = input.required<string>();
chiudi = output<void>();'), [
            dBlanks(pFill(), "<app-voto [___]=\"media()\" (___)=\"salva(\$event)\" />", ['stelle', 'cambia'], ['(stelle)', 'emit', 'value'], L('Quadre per il dato, tonde per l’evento.', 'Brackets for data, parentheses for the event.')),
            dFlash(pFlash(), [[L('I dati scendono dal padre al figlio', 'Data goes down from parent to child'), true], [L('Un figlio può cambiare un input da solo', 'A child can change an input by itself'), false], [L('`emit` manda un valore al padre', '`emit` sends a value to the parent'), true]], L('Dati in giù, eventi in su.', 'Data down, events up.')),
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
        ], [
            Q('m1', L('Quale classe raccoglie i campi?', 'Which class gathers the fields?'), ['FormGroup', 'FormControl', 'Validators', 'NgModel'], 0, L('`FormControl` è un singolo campo.', '`FormControl` is a single field.')),
            Q('m2', L('Come colleghi un input a un campo?', 'How do you link an input to a field?'), ['formControlName="email"', '[email]', 'name="email"', 'ngField="email"'], 0, L('Il nome deve esistere nel gruppo.', 'The name must exist in the group.')),
            Q('m3', L('Quale validatore controlla un’email?', 'Which validator checks an email?'), ['Validators.email', 'Validators.mail', 'Validators.at', 'Validators.text'], 0, L('Si combina con `required`.', 'It combines with `required`.')),
        ], task('angular', L('Crea `nome: new FormControl("", Validators.required)` dentro il `FormGroup`.', 'Create `nome: new FormControl("", Validators.required)` inside the `FormGroup`.'), 'modulo = new FormGroup({
});', [re('nome\s*:\s*new\s+FormControl\s*\(\s*["\']["\']\s*,\s*\[?\s*Validators\.required', L('Il campo è obbligatorio', 'The field is required'))], '', 'modulo = new FormGroup({
  nome: new FormControl("", Validators.required),
});'), [
            dBlanks(pFill(), "<form [___]=\"modulo\" (___)=\"invia()\">\n  <input ___=\"email\" />\n</form>", ['formGroup', 'ngSubmit', 'formControlName'], ['ngModel', 'submit', 'name'], L('Gruppo, invio, campo.', 'Group, submit, field.')),
            dFlash(pFlash(), [[L('`modulo.valid` è vero se tutti i campi sono a posto', '`modulo.valid` is true when every field is fine'), true], [L('I validatori si scrivono nel template', 'Validators are written in the template'), false], [L('Un campo può avere più validatori', 'A field can have several validators'), true]], L('Le regole vivono nella classe.', 'The rules live in the class.')),
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
        ], [
            Q('g1', L('Dove si mette una guardia?', 'Where do you put a guard?'), [L('Nella definizione della rotta', 'In the route definition'), L('Nel CSS', 'In the CSS'), L('Nel template', 'In the template'), L('Nel database', 'In the database')], 0, L('`canActivate: [guardia]`.', '`canActivate: [guard]`.')),
            Q('g2', L('A che cosa serve un intercettore?', 'What is an interceptor for?'), [L('A modificare tutte le richieste HTTP', 'To change every HTTP request'), L('A disegnare componenti', 'To draw components'), L('A tradurre testi', 'To translate texts'), L('A creare rotte', 'To create routes')], 0, L('Per esempio aggiunge l’intestazione `Authorization`.', 'For example it adds the `Authorization` header.')),
            Q('g3', L('Una guardia basta a proteggere i dati?', 'Is a guard enough to protect data?'), [L('No, serve il controllo sul server', 'No, the server must check'), L('Sì, sempre', 'Yes, always'), L('Solo in produzione', 'Only in production'), L('Solo con HTTPS', 'Only with HTTPS')], 0, L('Il codice del browser si può aggirare.', 'Browser code can be bypassed.')),
        ], task('angular', L('Proteggi la rotta `admin` con `canActivate: [soloAdmin]`.', 'Protect the `admin` route with `canActivate: [soloAdmin]`.'), '{ path: "admin", component: AdminComponent }', [re('path\s*:\s*["\']admin["\'][^}]*canActivate\s*:\s*\[\s*soloAdmin\s*\]', L('La rotta è protetta', 'The route is protected'))], '', '{ path: "admin", component: AdminComponent, canActivate: [soloAdmin] }'), [
            dBug(pBug(), ['export const soloUtenti: CanActivateFn = () => {', '  const auth = inject(AuthService);', '  return auth.loggato() ? "/login" : true;', '};'], 2, '  return auth.loggato() ? true : inject(Router).parseUrl("/login");', L('Chi è dentro passa, gli altri vanno al login.', 'Signed-in users pass, the others go to login.')),
            dFlash(pFlash(), [[L('`canActivate` può restituire un altro indirizzo', '`canActivate` can return another address'), true], [L('Un intercettore vede tutte le richieste HTTP', 'An interceptor sees every HTTP request'), true], [L('Con una guardia il server non deve controllare', 'With a guard the server need not check'), false]], L('La sicurezza vera è sul server.', 'Real security is on the server.')),
        ]),
    ],
];
