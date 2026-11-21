<?php
/** Flutter intermediate: forms, async data, state management, themes, local storage, go_router, JSON models, animations and widget tests. */

/** The three states a FutureBuilder draws: waiting (a spinner that turns), then data or an error. */
if (!function_exists('fig_flutterint_future')) {
    function fig_flutterint_future(string $wait, string $data, string $err): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.spin{transform-box:fill-box;transform-origin:center;animation:spin 3s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}@media (prefers-reduced-motion:reduce){.spin{animation:none}}</style>
<g stroke="#8A87B0" stroke-width="2" fill="none">
<path d="M192 120 H230"/><path d="M222 113 L232 120 L222 127"/>
<path d="M407 108 L446 72"/><path d="M436 72 L448 70 L444 82"/>
<path d="M407 132 L446 168"/><path d="M444 158 L448 170 L436 168"/>
</g>
<rect x="20" y="80" width="170" height="80" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="105" y="114" text-anchor="middle" font-weight="700">FutureBuilder</text>
<text x="105" y="138" text-anchor="middle" font-size="13" fill="#5A35C9">future: caricaCorsi()</text>
<rect x="235" y="80" width="170" height="80" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<circle cx="268" cy="120" r="14" fill="none" stroke="#DDDBEA" stroke-width="4"/>
<circle class="spin" cx="268" cy="120" r="14" fill="none" stroke="#2F8CF0" stroke-width="4" stroke-dasharray="30 58" stroke-linecap="round"/>
<text x="294" y="115" font-weight="700">{$wait}</text>
<text x="294" y="137" font-size="13" fill="#5F5C8A">!snap.hasData</text>
<rect x="450" y="20" width="170" height="80" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="535" y="54" text-anchor="middle" font-weight="700" fill="#0B7F5C">{$data}</text>
<text x="535" y="78" text-anchor="middle" font-size="13" fill="#0B7F5C">snap.hasData</text>
<rect x="450" y="140" width="170" height="80" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="535" y="174" text-anchor="middle" font-weight="700" fill="#C2185B">{$err}</text>
<text x="535" y="198" text-anchor="middle" font-size="13" fill="#C2185B">snap.hasError</text>
</svg>
SVG;
    }
}

/** A ChangeNotifierProvider above two widgets: the button calls aggiungi() with read, notifyListeners() redraws the badge that watches. */
if (!function_exists('fig_flutterint_provider')) {
    function fig_flutterint_provider(string $redraws, string $noListen): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="170" y="12" width="300" height="80" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="320" y="32" text-anchor="middle" font-size="13" font-weight="700" fill="#5A35C9">ChangeNotifierProvider</text>
<rect x="240" y="42" width="160" height="38" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="320" y="66" text-anchor="middle" font-weight="700">Carrello · 3</text>
<g stroke-width="2" fill="none">
<path d="M290 82 V140 H150 V200" stroke="#E8590C"/><path d="M143 192 L150 204 L157 192" stroke="#E8590C"/>
<path d="M490 200 V140 H350 V88" stroke="#0B7F5C"/><path d="M343 96 L350 84 L357 96" stroke="#0B7F5C"/>
</g>
<text x="140" y="160" text-anchor="end" font-size="13" font-weight="700" fill="#B84508">notifyListeners()</text>
<text x="140" y="178" text-anchor="end" font-size="13" fill="#B84508">{$redraws}</text>
<text x="500" y="160" font-size="13" font-weight="700" fill="#0B7F5C">aggiungi("Corso")</text>
<text x="500" y="178" font-size="13" fill="#0B7F5C">{$noListen}</text>
<rect x="40" y="206" width="230" height="70" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="155" y="235" text-anchor="middle" font-weight="700">Badge</text>
<text x="155" y="258" text-anchor="middle" font-size="13" fill="#1B66BC">context.watch&lt;Carrello&gt;()</text>
<rect x="370" y="206" width="230" height="70" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="485" y="235" text-anchor="middle" font-weight="700">Button</text>
<text x="485" y="258" text-anchor="middle" font-size="13" fill="#0B7F5C">context.read&lt;Carrello&gt;()</text>
</svg>
SVG;
    }
}

/** One column on a phone, two on a tablet: the layout chosen from MediaQuery's width. */
if (!function_exists('fig_flutterint_adaptive')) {
    function fig_flutterint_adaptive(string $phone, string $tablet): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="50" y="16" width="130" height="214" rx="18" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="62" y="34" width="106" height="28" rx="8" fill="#FFE8D6"/>
<g fill="#F1F0F8" stroke="#8A87B0" stroke-width="2">
<rect x="62" y="72" width="106" height="32" rx="8"/><rect x="62" y="112" width="106" height="32" rx="8"/><rect x="62" y="152" width="106" height="32" rx="8"/>
</g>
<rect x="230" y="40" width="380" height="190" rx="18" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="244" y="54" width="352" height="28" rx="8" fill="#FFE8D6"/>
<g fill="#F1F0F8" stroke="#8A87B0" stroke-width="2">
<rect x="244" y="92" width="130" height="32" rx="8"/><rect x="244" y="132" width="130" height="32" rx="8"/><rect x="244" y="172" width="130" height="32" rx="8"/>
</g>
<rect x="384" y="92" width="212" height="122" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g stroke="#2F8CF0" stroke-width="2"><path d="M400 116 H560"/><path d="M400 140 H540"/><path d="M400 164 H570"/></g>
<text x="115" y="256" text-anchor="middle" font-size="14">{$phone}</text>
<text x="420" y="256" text-anchor="middle" font-size="14">{$tablet}</text>
</svg>
SVG;
    }
}

/** linear against Curves.easeInOut: the two curves of value over time, and two dots running with each timing. */
if (!function_exists('fig_flutterint_curves')) {
    function fig_flutterint_curves(string $time, string $value): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.run{animation:run 3s linear infinite alternate}.ease{animation-timing-function:cubic-bezier(.42,0,.58,1)}@keyframes run{from{transform:none}to{transform:translateX(210px)}}@media (prefers-reduced-motion:reduce){.run{animation:none}}</style>
<g stroke="#1E1C52" stroke-width="2" fill="none"><path d="M50 20 V220 H310"/></g>
<text x="300" y="244" text-anchor="end" font-size="13" fill="#5F5C8A">{$time} →</text>
<text transform="translate(36 120) rotate(-90)" text-anchor="middle" font-size="13" fill="#5F5C8A">{$value} →</text>
<path d="M50 220 L300 30" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 5" fill="none"/>
<path d="M50 220 C155 220 195 30 300 30" stroke="#E8590C" stroke-width="3" fill="none"/>
<path d="M64 40 H92" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 5"/><text x="100" y="45" font-size="14">linear</text>
<path d="M64 64 H92" stroke="#E8590C" stroke-width="3"/><text x="100" y="69" font-size="14">easeInOut</text>
<text x="370" y="70" font-size="14" fill="#5F5C8A">Curves.linear</text>
<path d="M370 96 H600" stroke="#DDDBEA" stroke-width="6" stroke-linecap="round"/>
<circle class="run" cx="380" cy="96" r="12" fill="#8A87B0"/>
<text x="370" y="160" font-size="14" fill="#B84508">Curves.easeInOut</text>
<path d="M370 186 H600" stroke="#FFE8D6" stroke-width="6" stroke-linecap="round"/>
<circle class="run ease" cx="380" cy="186" r="12" fill="#E8590C"/>
</svg>
SVG;
    }
}

/** The three levels of Flutter tests as a pyramid: many fast unit tests at the base, few realistic integration tests at the top. */
if (!function_exists('fig_flutterint_tests')) {
    function fig_flutterint_tests(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<polygon points="290,10 350,10 392,90 248,90" fill="#ECE6FF" stroke="#7048E8" stroke-width="2" stroke-linejoin="round"/>
<polygon points="248,90 392,90 431,165 209,165" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2" stroke-linejoin="round"/>
<polygon points="209,165 431,165 470,240 170,240" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2" stroke-linejoin="round"/>
<g text-anchor="middle" font-weight="700">
<text x="320" y="62">integration</text><text x="320" y="134">widget</text><text x="320" y="209">unit</text>
</g>
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M150 236 V20"/><path d="M143 30 L150 18 L157 30"/></g>
<g text-anchor="end" font-size="13" fill="#5F5C8A">
<text x="138" y="34">{$t[0]}</text><text x="138" y="52">{$t[1]}</text>
<text x="138" y="214">{$t[2]}</text><text x="138" y="232">{$t[3]}</text>
</g>
<g font-size="13">
<text x="440" y="56" fill="#5A35C9">{$t[4]}</text><text x="470" y="133" fill="#1B66BC">{$t[5]}</text><text x="490" y="208" fill="#0B7F5C">{$t[6]}</text>
</g>
</svg>
SVG;
    }
}

return [
    'id' => 'flutter-intermediate', 'family' => 'flutter', 'after' => 'flutter-base',
    'title' => L('Flutter intermedio', 'Intermediate Flutter'), 'short' => L('Flutter intermedio', 'Flutter Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Moduli e validazione, dati dalla rete, gestione dello stato, temi, salvataggi sul dispositivo, go_router, modelli JSON, animazioni e test.', 'Forms and validation, data from the network, state management, themes, on-device storage, go_router, JSON models, animations and tests.'),
    'lessons' => [
        lesson('forms', L('Moduli e validazione', 'Forms and validation'), [
            P('Un `Form` raccoglie più `TextFormField`; ogni campo ha un `validator` che restituisce un messaggio d’errore o `null` se va bene. Una `GlobalKey<FormState>` permette di validare tutto insieme.', 'A `Form` gathers several `TextFormField`s; each field has a `validator` returning an error message, or `null` when fine. A `GlobalKey<FormState>` lets you validate everything at once.'),
            C(L('final _chiave = GlobalKey<FormState>();

Form(
  key: _chiave,
  child: Column(children: [
    TextFormField(
      decoration: const InputDecoration(labelText: "Email"),
      validator: (v) => (v == null || !v.contains("@")) ? "Email non valida" : null,
    ),
    ElevatedButton(
      onPressed: () {
        if (_chiave.currentState!.validate()) salva();
      },
      child: const Text("Invia"),
    ),
  ]),
)', 'final _chiave = GlobalKey<FormState>();

Form(
  key: _chiave,
  child: Column(children: [
    TextFormField(
      decoration: const InputDecoration(labelText: "Email"),
      validator: (v) => (v == null || !v.contains("@")) ? "Invalid email" : null,
    ),
    ElevatedButton(
      onPressed: () {
        if (_chiave.currentState!.validate()) salva();
      },
      child: const Text("Send"),
    ),
  ]),
)'), 'dart', L('<style>body{margin:0;padding:12px 16px;font-family:system-ui,sans-serif;color:#1d1b20}.f{border-bottom:2px solid #b3261e;padding:4px 0 6px}.f small{display:block;color:#b3261e;font-size:12px}.e{color:#b3261e;font-size:12px;margin:4px 0 14px}.b{display:inline-block;padding:9px 22px;border-radius:20px;background:#6750a4;color:#fff}</style><div class="f"><small>Email</small>ada.example.com</div><div class="e">Email non valida</div><span class="b">Invia</span>', '<style>body{margin:0;padding:12px 16px;font-family:system-ui,sans-serif;color:#1d1b20}.f{border-bottom:2px solid #b3261e;padding:4px 0 6px}.f small{display:block;color:#b3261e;font-size:12px}.e{color:#b3261e;font-size:12px;margin:4px 0 14px}.b{display:inline-block;padding:9px 22px;border-radius:20px;background:#6750a4;color:#fff}</style><div class="f"><small>Email</small>ada.example.com</div><div class="e">Invalid email</div><span class="b">Send</span>')),
            P('Un `TextEditingController` legge e cambia il testo di un campo da codice.', 'A `TextEditingController` reads and changes a field’s text from code.'),
            P('Il `validator` viene chiamato da `validate()`, di solito al tocco del bottone: se anche un solo campo restituisce un messaggio, `validate()` dà `false` e il messaggio appare in rosso sotto il campo. Per mostrare l’errore già mentre l’utente scrive, aggiungi al `Form` `autovalidateMode: AutovalidateMode.onUserInteraction`.', 'The `validator` is called by `validate()`, usually when the button is tapped: if even one field returns a message, `validate()` gives `false` and the message appears in red under the field. To show the error while the user is still typing, add `autovalidateMode: AutovalidateMode.onUserInteraction` to the `Form`.'),
            Tip('La validazione nell’app aiuta l’utente ma non protegge nulla: chiunque può chiamare il server senza passare dall’app, quindi lì i dati vanno controllati di nuovo.', 'Validation in the app helps the user but protects nothing: anyone can call the server without going through the app, so the data must be checked there again.'),
        ], [
            Q('f1', L('Che cosa restituisce un validator se il valore è giusto?', 'What does a validator return when the value is fine?'), ['null', 'true', '""', '0'], 0, L('Una stringa indica l’errore.', 'A string signals the error.')),
            Q('f2', L('Come validi tutto il modulo?', 'How do you validate the whole form?'), ['chiave.currentState!.validate()', 'form.check()', 'validateAll()', 'Form.ok'], 0, L('Tramite la GlobalKey.', 'Through the GlobalKey.')),
            Q('f3', L('A che cosa serve un `TextEditingController`?', 'What is a `TextEditingController` for?'), [L('Leggere e cambiare il testo da codice', 'Reading and changing text from code'), L('Correggere l’ortografia', 'Spell-checking'), L('Cambiare font', 'Changing font'), L('Nascondere la tastiera', 'Hiding the keyboard')], 0, L('Ricorda di chiamare `dispose`.', 'Remember to call `dispose`.')),
        ], task('dart', L('Aggiungi un `validator` che restituisce `"Obbligatorio"` se `v` è vuoto, altrimenti `null`.', 'Add a `validator` returning `"Obbligatorio"` if `v` is empty, otherwise `null`.'), 'TextFormField(
  decoration: const InputDecoration(labelText: "Nome"),
)', [re('validator\s*:\s*\(\s*v\s*\)\s*=>', L('C’è il validator', 'There is the validator')), re('"Obbligatorio"\s*:\s*null', L('Messaggio o null', 'Message or null'))], '', 'validator: (v) => (v == null || v.isEmpty) ? "Obbligatorio" : null,'), [
            dOrder(pSort(), ['final chiave = GlobalKey<FormState>();', 'Form(', '  key: chiave,', '  child: TextFormField(validator: (v) => v!.isEmpty ? "Vuoto" : null),', ')'], L('Chiave, modulo, campo con regola.', 'Key, form, field with rule.')),
            dFlash(pFlash(), [[L('Un validator restituisce `null` se va tutto bene', 'A validator returns `null` when all is fine'), true], [L('`validate()` controlla un solo campo', '`validate()` checks a single field'), false], [L('I controller vanno liberati con `dispose`', 'Controllers must be released with `dispose`'), true], [L('Un `TextFormField` può avere un validator', 'A `TextFormField` can have a validator'), true]], L('`validate()` controlla tutto il modulo.', '`validate()` checks the whole form.')),
        ]),
        lesson('async', L('Future e dati dalla rete', 'Futures and network data'), [
            P('Le operazioni lente restituiscono un `Future`. Con `async`/`await` si scrivono come codice normale. `FutureBuilder` mostra un widget diverso mentre aspetta, in caso di errore o con i dati.', 'Slow operations return a `Future`. With `async`/`await` you write them like normal code. `FutureBuilder` shows a different widget while waiting, on error or with data.'),
            Fig(L(fig_flutterint_future('in attesa', 'dati arrivati', 'errore'), fig_flutterint_future('waiting', 'data arrived', 'error')), L('FutureBuilder, con future caricaCorsi(), passa prima allo stato in attesa, con una rotella che gira. Da lì una freccia porta ai dati arrivati, quando snap.hasData è vero, e un’altra all’errore, quando snap.hasError è vero.', 'FutureBuilder, with the future caricaCorsi(), first goes to the waiting state, with a turning spinner. From there one arrow leads to data arrived, when snap.hasData is true, and another to the error, when snap.hasError is true.'), L('Il builder viene richiamato a ogni cambio di stato del Future: prevedi tutti e tre i casi.', 'The builder runs again at every change of the Future’s state: handle all three cases.')),
            C('Future<List<String>> caricaCorsi() async {
  final r = await http.get(Uri.parse("https://example.com/api/corsi"));
  if (r.statusCode != 200) throw Exception("Errore ${r.statusCode}");
  return List<String>.from(jsonDecode(r.body));
}

FutureBuilder<List<String>>(
  future: caricaCorsi(),
  builder: (context, snap) {
    if (snap.hasError) return Text("Errore: ${snap.error}");
    if (!snap.hasData) return const CircularProgressIndicator();
    return Text("${snap.data!.length} corsi");
  },
)', 'dart'),
            P('Per i flussi continui (chat, sensori) esiste `Stream` con il suo `StreamBuilder`.', 'For continuous flows (chat, sensors) there is `Stream` with its `StreamBuilder`.'),
            Tip('In un’app vera non creare il Future dentro `build`: ripartirebbe a ogni ridisegno. Crealo una volta in `initState`, salvalo in un campo e passa quello a `FutureBuilder`.', 'In a real app do not create the Future inside `build`: it would restart at every redraw. Create it once in `initState`, keep it in a field and pass that to `FutureBuilder`.'),
        ], [
            Q('a1', L('Che cosa rappresenta un `Future`?', 'What does a `Future` represent?'), [L('Un valore che arriverà', 'A value to come'), L('Una data futura', 'A future date'), L('Un widget', 'A widget'), L('Un errore', 'An error')], 0, L('Si attende con `await`.', 'You wait for it with `await`.')),
            Q('a2', L('Che cosa mostra `FutureBuilder` durante l’attesa?', 'What does `FutureBuilder` show while waiting?'), [L('Quello che decidi tu, per esempio una rotellina', 'Whatever you decide, e.g. a spinner'), L('Niente, sempre', 'Nothing, always'), L('Un errore', 'An error'), L('Il vecchio valore', 'The old value')], 0, L('Controlla `snap.hasData`.', 'Check `snap.hasData`.')),
            Q('a3', L('Per un flusso continuo di valori si usa…', 'For a continuous flow of values you use…'), ['Stream', 'Future', 'List', 'Map'], 0, L('Con `StreamBuilder`.', 'With `StreamBuilder`.')),
        ], task('dart', L('Scrivi `Future<String> saluto() async` che fa `await Future.delayed(Duration(seconds: 1))` e restituisce `"Ciao"`.', 'Write `Future<String> saluto() async` that does `await Future.delayed(Duration(seconds: 1))` and returns `"Ciao"`.'), '', [re('Future<String>\s+saluto\s*\(\s*\)\s+async', L('La funzione è asincrona', 'The function is async')), re('await\s+Future\.delayed\s*\(', L('Aspetta', 'It waits')), re('return\s+"Ciao"\s*;', L('Restituisce “Ciao”', 'It returns “Ciao”'))], '', 'Future<String> saluto() async {
  await Future.delayed(const Duration(seconds: 1));
  return "Ciao";
}'), [
            dBug(pBug(), ['Future<void> carica() async {', '  final r = http.get(Uri.parse(url));', '  print(r.body);', '}'], 1, '  final r = await http.get(Uri.parse(url));', L('Senza `await` hai un Future, non la risposta.', 'Without `await` you have a Future, not the response.')),
            dMatch(pMatch(), [['Future', L('Un valore che arriverà', 'A value to come')], ['Stream', L('Tanti valori nel tempo', 'Many values over time')], ['await', L('Aspetta il risultato', 'Waits for the result')], ['FutureBuilder', L('Widget che aspetta un Future', 'Widget that waits for a Future')]], L('Il codice asincrono in Dart.', 'Async code in Dart.')),
        ]),
        lesson('state-management', L('Gestire lo stato', 'Managing state'), [
            P('Quando più schermate condividono dati, `setState` non basta. Una soluzione semplice è un `ChangeNotifier` con il pacchetto **provider**: lo stato vive in una classe che avvisa chi la ascolta con `notifyListeners()`.', 'When several screens share data, `setState` is not enough. A simple solution is a `ChangeNotifier` with the **provider** package: state lives in a class that tells its listeners with `notifyListeners()`.'),
            C('class Carrello extends ChangeNotifier {
  final List<String> _pezzi = [];
  int get totale => _pezzi.length;
  void aggiungi(String p) {
    _pezzi.add(p);
    notifyListeners();
  }
}

// in main: ChangeNotifierProvider(create: (_) => Carrello(), child: const App())
// in un widget:
final n = context.watch<Carrello>().totale;
context.read<Carrello>().aggiungi("Corso");', 'dart'),
            P('`watch` ridisegna il widget quando lo stato cambia; `read` lo usa senza ascoltare, per esempio dentro un `onPressed`.', '`watch` redraws the widget when state changes; `read` uses it without listening, e.g. inside an `onPressed`.'),
            Fig(L(fig_flutterint_provider('→ si ridisegna', 'senza ascoltare'), fig_flutterint_provider('→ redraws', 'without listening')), L('In alto ChangeNotifierProvider contiene l’oggetto Carrello. Sotto ci sono due widget: Button usa context.read e chiama aggiungi, con una freccia verde verso il Carrello; dal Carrello una freccia arancione, notifyListeners, scende a Badge, che usa context.watch e si ridisegna.', 'At the top ChangeNotifierProvider holds the Carrello object. Below are two widgets: Button uses context.read and calls aggiungi, with a green arrow up to Carrello; from Carrello an orange arrow, notifyListeners, goes down to Badge, which uses context.watch and redraws.'), L('Le azioni salgono allo stato, gli avvisi scendono a chi ascolta.', 'Actions go up to the state, notifications come down to whoever listens.')),
            P('Il provider va messo **sopra** tutti i widget che usano lo stato: `context.watch` lo cerca risalendo l’albero e, se non lo trova, lancia un errore. Due sviste tipiche: dimenticare `notifyListeners()`, così i dati cambiano ma nessuno si ridisegna, e usare `watch` dentro un `onPressed`, dove serve `read`. Riverpod e Bloc sono alternative per app più grandi, ma l’idea resta: lo stato fuori dai widget, i widget che lo ascoltano.', 'The provider must sit **above** every widget that uses the state: `context.watch` looks for it going up the tree and throws an error if it does not find it. Two classic slips: forgetting `notifyListeners()`, so the data changes but nothing redraws, and using `watch` inside an `onPressed`, where you need `read`. Riverpod and Bloc are alternatives for larger apps, but the idea stays: state outside the widgets, widgets that listen to it.'),
        ], [
            Q('m1', L('Che cosa fa `notifyListeners()`?', 'What does `notifyListeners()` do?'), [L('Avvisa i widget che ascoltano', 'Tells the listening widgets'), L('Manda una notifica push', 'Sends a push notification'), L('Salva su disco', 'Saves to disk'), L('Chiude l’app', 'Closes the app')], 0, L('Così si ridisegnano.', 'So they redraw.')),
            Q('m2', L('Quando usare `read` invece di `watch`?', 'When use `read` instead of `watch`?'), [L('Dentro un callback come onPressed', 'Inside a callback like onPressed'), L('Sempre', 'Always'), L('Mai', 'Never'), L('Solo in main', 'Only in main')], 0, L('Non serve ridisegnare.', 'No redraw needed.')),
            Q('m3', L('Perché non basta `setState` per dati condivisi?', 'Why is `setState` not enough for shared data?'), [L('Vale solo dentro un widget', 'It works only inside one widget'), L('È lento', 'It is slow'), L('È deprecato', 'It is deprecated'), L('Basta sempre', 'It is always enough')], 0, L('Lo stato condiviso va sollevato.', 'Shared state must be lifted.')),
        ], task('dart', L('Nel metodo `aggiungi` chiama `notifyListeners();` dopo aver cambiato la lista.', 'In the `aggiungi` method call `notifyListeners();` after changing the list.'), 'void aggiungi(String p) {
  _pezzi.add(p);
}', [re('_pezzi\.add\s*\(\s*p\s*\)\s*;\s*notifyListeners\s*\(\s*\)\s*;', L('Avvisi dopo il cambio', 'You notify after the change'))], '', 'void aggiungi(String p) {
  _pezzi.add(p);
  notifyListeners();
}'), [
            dBlanks(pFill(), "class Tema extends ___ {\n  bool scuro = false;\n  void cambia() {\n    scuro = !scuro;\n    ___();\n  }\n}\nfinal t = context.___<Tema>();", ['ChangeNotifier', 'notifyListeners', 'watch'], ['StatefulWidget', 'setState', 'get'], L('Stato condiviso, avviso, ascolto.', 'Shared state, notify, listen.')),
            dFlash(pFlash(), [[L('`context.watch` ridisegna quando lo stato cambia', '`context.watch` redraws on state change'), true], [L('Senza `notifyListeners` i widget si aggiornano lo stesso', 'Without `notifyListeners` widgets update anyway'), false], [L('Esistono anche Riverpod e Bloc', 'Riverpod and Bloc exist too'), true], [L('`setState` aggiorna lo stato di uno StatefulWidget', '`setState` updates the state of a StatefulWidget'), true]], L('Bisogna avvisare.', 'You must notify.')),
        ]),
        lesson('themes', L('Temi e adattamento', 'Themes and adaptation'), [
            P('`ThemeData` definisce colori e testi di tutta l’app; `ColorScheme.fromSeed` crea una palette da un solo colore. Con `darkTheme` e `themeMode` l’app segue il tema chiaro o scuro del sistema.', '`ThemeData` defines colours and text for the whole app; `ColorScheme.fromSeed` makes a palette from one colour. With `darkTheme` and `themeMode` the app follows the system’s light or dark theme.'),
            C('MaterialApp(
  theme: ThemeData(colorScheme: ColorScheme.fromSeed(seedColor: Colors.deepOrange)),
  darkTheme: ThemeData(
    colorScheme: ColorScheme.fromSeed(seedColor: Colors.deepOrange, brightness: Brightness.dark),
  ),
  themeMode: ThemeMode.system,
  home: const Home(),
)

final largo = MediaQuery.sizeOf(context).width > 700;', 'dart'),
            P('`MediaQuery` dice quanto è grande lo schermo: utile per mostrare una colonna sul telefono e due sul tablet.', '`MediaQuery` tells how big the screen is: handy for showing one column on a phone and two on a tablet.'),
            Fig(L(fig_flutterint_adaptive('telefono: larghezza ≤ 700', 'tablet: larghezza > 700'), fig_flutterint_adaptive('phone: width ≤ 700', 'tablet: width > 700')), L('A sinistra un telefono con una sola colonna di righe. A destra un tablet in orizzontale con la lista a sinistra e il dettaglio azzurro a destra.', 'On the left a phone with a single column of rows. On the right a landscape tablet with the list on the left and the blue detail on the right.'), L('La stessa app sceglie il layout in base alla larghezza.', 'The same app picks its layout from the width.')),
            P('Il tema rende davvero quando i widget leggono i colori da lì invece di scriverli a mano: `Theme.of(context).colorScheme.primary` è arancione nel tema chiaro e una variante adatta nel tema scuro, senza nessun `if`. Lo stesso vale per i testi con `Theme.of(context).textTheme.titleLarge`. Cambi il colore seme in un punto solo e tutta l’app si adegua.', 'The theme really pays off when widgets read their colours from it instead of hard-coding them: `Theme.of(context).colorScheme.primary` is orange in the light theme and a suitable variant in the dark one, without a single `if`. The same goes for text with `Theme.of(context).textTheme.titleLarge`. Change the seed colour in one place and the whole app follows.'),
        ], [
            Q('t1', L('Che cosa fa `ColorScheme.fromSeed`?', 'What does `ColorScheme.fromSeed` do?'), [L('Crea una palette da un colore', 'Makes a palette from one colour'), L('Sceglie un colore a caso', 'Picks a random colour'), L('Carica un’immagine', 'Loads an image'), L('Cambia il font', 'Changes the font')], 0, L('Colori coordinati in automatico.', 'Coordinated colours automatically.')),
            Q('t2', L('Quale `themeMode` segue il sistema?', 'Which `themeMode` follows the system?'), ['ThemeMode.system', 'ThemeMode.auto', 'ThemeMode.dark', 'ThemeMode.os'], 0, L('Chiaro o scuro come il telefono.', 'Light or dark like the phone.')),
            Q('t3', L('Come leggi la larghezza dello schermo?', 'How do you read the screen width?'), ['MediaQuery.sizeOf(context).width', 'Screen.width', 'window.width', 'context.width()'], 0, L('Per layout adattivi.', 'For adaptive layouts.')),
        ], task('dart', L('Imposta `themeMode: ThemeMode.system` nella `MaterialApp`.', 'Set `themeMode: ThemeMode.system` in the `MaterialApp`.'), 'MaterialApp(
  theme: ThemeData.light(),
  darkTheme: ThemeData.dark(),
  home: const Home(),
)', [re('themeMode\s*:\s*ThemeMode\.system', L('Segue il tema del sistema', 'It follows the system theme'))], '', 'themeMode: ThemeMode.system,'), [
            dMatch(pMatch(), [['ThemeData', L('Stile dell’app', 'App style')], ['ColorScheme.fromSeed', L('Palette da un colore', 'Palette from one colour')], ['darkTheme', L('Tema scuro', 'Dark theme')], ['MediaQuery', L('Dimensioni dello schermo', 'Screen size')]], L('Temi e adattamento.', 'Themes and adaptation.')),
            dOrder(pSort(), ['Widget build(BuildContext context) {', '  final largo = MediaQuery.sizeOf(context).width > 700;', '  return largo ? const DueColonne() : const UnaColonna();', '}'], L('Misura, poi scegli il layout.', 'Measure, then pick the layout.')),
        ]),
        lesson('storage', L('Salvare sul dispositivo', 'Saving on the device'), [
            P('Per piccole preferenze c’è `shared_preferences`: chiave e valore, come il localStorage del web. Le versioni recenti consigliano ai nuovi progetti `SharedPreferencesAsync` (o `SharedPreferencesWithCache`); la classica `SharedPreferences.getInstance()` dell’esempio resta supportata. Per dati strutturati si usa un database locale come **SQLite** (pacchetto `sqflite`).', 'For small preferences there is `shared_preferences`: key and value, like the web’s localStorage. Recent versions recommend `SharedPreferencesAsync` (or `SharedPreferencesWithCache`) for new projects; the classic `SharedPreferences.getInstance()` used in the sample is still supported. For structured data use a local database such as **SQLite** (the `sqflite` package).'),
            C('final prefs = await SharedPreferences.getInstance();
await prefs.setBool("tema_scuro", true);
final scuro = prefs.getBool("tema_scuro") ?? false;

final db = await openDatabase("note.db", version: 1,
  onCreate: (db, v) => db.execute("CREATE TABLE note (id INTEGER PRIMARY KEY, testo TEXT)"));
await db.insert("note", {"testo": "Comprare il pane"});', 'dart'),
            P('Non salvare mai password o token in chiaro: per i segreti esiste `flutter_secure_storage`, che usa gli archivi sicuri del sistema (Keychain su iOS, Keystore su Android).', 'Never store passwords or tokens in clear text: for secrets there is `flutter_secure_storage`, which uses the system’s secure stores (Keychain on iOS, Keystore on Android).'),
        ], [
            Q('s1', L('Dove salvi una preferenza semplice?', 'Where do you save a simple preference?'), ['shared_preferences', 'sqflite', L('In un widget', 'In a widget'), L('Nel codice', 'In the code')], 0, L('Chiave e valore.', 'Key and value.')),
            Q('s2', L('Che cosa dà `getBool` se la chiave non esiste?', 'What does `getBool` give if the key is missing?'), ['null', 'false', L('Un errore', 'An error'), 'true'], 0, L('Per questo si usa `??`.', 'That is why `??` is used.')),
            Q('s3', L('Dove vanno i token di accesso?', 'Where do access tokens go?'), ['flutter_secure_storage', 'shared_preferences', L('Un file di testo', 'A text file'), L('Il nome dell’app', 'The app name')], 0, L('Negli archivi cifrati del sistema.', 'In the system’s encrypted stores.')),
        ], task('dart', L('Salva `await prefs.setString("lingua", "it");`.', 'Save `await prefs.setString("lingua", "it");`.'), 'final prefs = await SharedPreferences.getInstance();
', [re('await\s+prefs\.setString\s*\(\s*"lingua"\s*,\s*"it"\s*\)\s*;', L('Salvi la lingua', 'You save the language'))], '', 'await prefs.setString("lingua", "it");'), [
            dBlanks(pFill(), "final prefs = ___ SharedPreferences.getInstance();\nfinal volume = prefs.getInt(\"volume\") ___ 5;", ['await', '??'], ['async', '||', '?:'], L('Attendi e dai un valore di riserva.', 'Await and give a fallback.')),
            dFlash(pFlash(), [[L('shared_preferences va bene per le password', 'shared_preferences is fine for passwords'), false], [L('SQLite è un database locale', 'SQLite is a local database'), true], [L('`??` dà un valore se a sinistra c’è null', '`??` gives a value when the left is null'), true], [L('I dati salvati con shared_preferences spariscono a ogni avvio', 'Data saved with shared_preferences is lost at every launch'), false]], L('I segreti vanno nel portachiavi.', 'Secrets go in the keychain.')),
        ]),
        lesson('routing', L('Navigare con go_router', 'Navigating with go_router'), [
            P('Con molte pagine, link diretti e il tasto Indietro del browser, conviene descrivere le pagine come **indirizzi**: `/`, `/corso/42`. Il pacchetto **go_router**, curato dal team di Flutter, fa proprio questo; la configurazione si passa a `MaterialApp.router`.', 'With many pages, deep links and the browser’s Back button, it pays to describe pages as **addresses**: `/`, `/corso/42`. The **go_router** package, maintained by the Flutter team, does exactly this; the configuration is passed to `MaterialApp.router`.'),
            C('final router = GoRouter(
  routes: [
    GoRoute(path: "/", builder: (context, state) => const Home()),
    GoRoute(
      path: "/corso/:id",
      builder: (context, state) => CorsoPage(id: state.pathParameters["id"]!),
    ),
  ],
);

void main() => runApp(MaterialApp.router(routerConfig: router));

// in un widget
onTap: () => context.go("/corso/42"),', 'dart'),
            P('`context.go` porta all’indirizzo sostituendo la pila di pagine; `context.push` mette la pagina sopra, così `context.pop()` torna indietro. I parametri come `:id` arrivano in `state.pathParameters`.', '`context.go` takes you to the address replacing the page stack; `context.push` puts the page on top, so `context.pop()` goes back. Parameters such as `:id` arrive in `state.pathParameters`.'),
        ], [
            Q('r1', L('Con il percorso `/corso/:id`, come leggi `42` da `/corso/42`?', 'With the path `/corso/:id`, how do you read `42` from `/corso/42`?'), ['state.uri.path', 'state.pathParameters["id"]', 'context.read("id")', 'GoRoute.id'], 1, L('`state.uri.path` darebbe tutto l’indirizzo, `/corso/42`.', '`state.uri.path` would give the whole address, `/corso/42`.')),
            Q('r2', L('Che differenza c’è tra `context.go` e `context.push`?', 'What is the difference between `context.go` and `context.push`?'), [L('`go` sostituisce la pila, `push` mette la pagina sopra', '`go` replaces the stack, `push` puts the page on top'), L('Nessuna: sono sinonimi', 'None: they are synonyms'), L('`go` funziona solo sul web', '`go` only works on the web'), L('`push` apre il browser esterno', '`push` opens the external browser')], 0, L('Dopo `push`, `pop` riporta alla pagina di prima.', 'After `push`, `pop` brings back the previous page.')),
            Q('r3', L('A quale widget passi il `GoRouter`?', 'Which widget do you pass the `GoRouter` to?'), ['MaterialApp(home: router)', 'Navigator(router)', 'Scaffold(router: router)', 'MaterialApp.router(routerConfig: router)'], 3, L('Il costruttore `.router` lascia la navigazione al router.', 'The `.router` constructor hands navigation to the router.')),
        ], task('dart', L('Aggiungi una `GoRoute` con `path: "/profilo"` che costruisce `const Profilo()`.', 'Add a `GoRoute` with `path: "/profilo"` that builds `const Profilo()`.'), 'final router = GoRouter(routes: [
  GoRoute(path: "/", builder: (context, state) => const Home()),
]);', [re('GoRoute\s*\(\s*path\s*:\s*["\']/profilo["\']\s*,\s*builder\s*:\s*\(\s*\w+\s*,\s*\w+\s*\)\s*=>\s*(const\s+)?Profilo\s*\(\s*\)', L('La pagina del profilo ha il suo indirizzo', 'The profile page has its address'))], '', 'final router = GoRouter(routes: [
  GoRoute(path: "/", builder: (context, state) => const Home()),
  GoRoute(path: "/profilo", builder: (context, state) => const Profilo()),
]);'), [
            dBlanks(pFill(), "GoRoute(\n  path: \"/corso/:___\",\n  builder: (context, state) => CorsoPage(id: state.___[\"id\"]!),\n)\n// altrove\ncontext.___(\"/corso/7\");", ['id', 'pathParameters', 'go'], ['name', 'params', 'open'], L('Parametro, lettura, navigazione.', 'Parameter, reading, navigation.')),
            dMatch(pMatch(), [['context.go', L('Va a un indirizzo, sostituendo la pila', 'Goes to an address, replacing the stack')], ['context.push', L('Mette una pagina sopra', 'Puts a page on top')], ['context.pop', L('Torna indietro', 'Goes back')], [':id', L('Parametro nel percorso', 'Parameter in the path')]], L('go_router in breve.', 'go_router in short.')),
        ]),
        lesson('json-models', L('Modelli e JSON', 'Models and JSON'), [
            P('Usare ovunque una `Map<String, dynamic>` è fragile: un nome sbagliato si scopre solo a runtime. Meglio una classe **modello** con un costruttore `fromJson` e un metodo `toJson`: il resto dell’app lavora con campi tipizzati.', 'Using a `Map<String, dynamic>` everywhere is fragile: a wrong name only shows up at runtime. Better a **model** class with a `fromJson` constructor and a `toJson` method: the rest of the app works with typed fields.'),
            C('import "dart:convert";

class Corso {
  final int id;
  final String titolo;
  final bool gratis;

  const Corso({required this.id, required this.titolo, this.gratis = false});

  factory Corso.fromJson(Map<String, dynamic> j) => Corso(
        id: j["id"] as int,
        titolo: j["titolo"] as String,
        gratis: j["gratis"] as bool? ?? false,
      );

  Map<String, dynamic> toJson() => {"id": id, "titolo": titolo, "gratis": gratis};
}

final lista = (jsonDecode(testo) as List)
    .map((e) => Corso.fromJson(e as Map<String, dynamic>))
    .toList();
final ritorno = jsonEncode(lista.first.toJson());', 'dart'),
            P('Un costruttore `factory` può eseguire codice prima di creare l’oggetto. Per modelli grandi `fromJson` e `toJson` si generano con pacchetti come `json_serializable`; con Dart 3 si può anche leggere il JSON con i **pattern**: `if (j case {"id": int id}) …`.', 'A `factory` constructor can run code before creating the object. For large models `fromJson` and `toJson` are generated with packages such as `json_serializable`; with Dart 3 you can also read JSON with **patterns**: `if (j case {"id": int id}) …`.'),
        ], [
            Q('j1', L('Che cosa fa `jsonDecode`?', 'What does `jsonDecode` do?'), [L('Trasforma un oggetto Dart in testo JSON', 'Turns a Dart object into JSON text'), L('Scarica un file JSON dalla rete', 'Downloads a JSON file from the network'), L('Trasforma un testo JSON in mappe e liste Dart', 'Turns JSON text into Dart maps and lists'), L('Controlla soltanto che il JSON sia valido', 'Only checks that the JSON is valid')], 2, L('Il contrario è `jsonEncode`.', 'The opposite is `jsonEncode`.')),
            Q('j2', L('Perché un modello con `fromJson` invece della mappa ovunque?', 'Why a model with `fromJson` instead of the map everywhere?'), [L('Il compilatore controlla nomi e tipi dei campi', 'The compiler checks field names and types'), L('Il JSON scaricato diventa più piccolo', 'The downloaded JSON gets smaller'), L('Le richieste di rete diventano più veloci', 'Network requests get faster'), L('Non serve più gestire gli errori', 'Errors no longer need handling')], 0, L('Gli errori di battitura emergono subito.', 'Typos show up at once.')),
            Q('j3', L('Che cosa può fare un costruttore `factory`?', 'What can a `factory` constructor do?'), [L('Solo assegnare campi `final`', 'Only assign `final` fields'), L('Creare più classi in una volta', 'Create several classes at once'), L('Essere chiamato senza parentesi', 'Be called without brackets'), L('Eseguire codice prima di restituire l’oggetto', 'Run code before returning the object')], 3, L('Per esempio leggere e convertire i campi della mappa.', 'For example reading and converting the map’s fields.')),
        ], task('dart', L('Completa `Utente.fromJson` così che `nome` venga da `j["nome"] as String`.', 'Complete `Utente.fromJson` so that `nome` comes from `j["nome"] as String`.'), 'class Utente {
  final String nome;
  const Utente({required this.nome});
  factory Utente.fromJson(Map<String, dynamic> j) => Utente(nome: );
}', [re('factory\s+Utente\.fromJson\s*\(\s*Map<String,\s*dynamic>\s+j\s*\)', L('Il costruttore factory c’è', 'The factory constructor is there')), re('nome\s*:\s*j\s*\[\s*["\']nome["\']\s*\]\s*as\s+String', L('Leggi il nome dalla mappa', 'You read the name from the map'))], '', 'factory Utente.fromJson(Map<String, dynamic> j) => Utente(nome: j["nome"] as String);'), [
            dOrder(pSort(), ['final r = await http.get(Uri.parse(url));', 'final dati = jsonDecode(r.body) as List;', 'final corsi = dati.map((e) => Corso.fromJson(e)).toList();', 'setState(() => _corsi = corsi);'], L('Scarica, decodifica, converti, mostra.', 'Download, decode, convert, show.')),
            dFlash(pFlash(), [[L('`jsonEncode` produce un testo JSON', '`jsonEncode` produces JSON text'), true], [L('`fromJson` è una parola chiave di Dart', '`fromJson` is a Dart keyword'), false], [L('`json_serializable` può generare il codice dei modelli', '`json_serializable` can generate model code'), true], [L('Leggere una chiave assente da una Map dà `null`', 'Reading a missing key from a Map gives `null`'), true]], L('`fromJson` è solo una convenzione di nome.', '`fromJson` is just a naming convention.')),
        ]),
        lesson('animations', L('Animazioni', 'Animations'), [
            P('Le **animazioni implicite** sono le più semplici: `AnimatedContainer`, `AnimatedOpacity`, `AnimatedAlign`… Cambi un valore con `setState` e il widget passa dal vecchio al nuovo nella `duration` indicata, con la `curve` che preferisci.', '**Implicit animations** are the simplest: `AnimatedContainer`, `AnimatedOpacity`, `AnimatedAlign`… You change a value with `setState` and the widget moves from the old to the new one over the given `duration`, with the `curve` you prefer.'),
            C('AnimatedContainer(
  duration: const Duration(milliseconds: 300),
  curve: Curves.easeInOut,
  width: aperto ? 240 : 80,
  height: 80,
  decoration: BoxDecoration(
    color: aperto ? Colors.deepOrange : Colors.grey,
    borderRadius: BorderRadius.circular(aperto ? 24 : 8),
  ),
)', 'dart'),
            Fig(L(fig_flutterint_curves('tempo', 'valore'), fig_flutterint_curves('time', 'value')), L('A sinistra un grafico del valore nel tempo: la retta tratteggiata grigia è linear, la curva a S arancione è easeInOut, che parte piano, accelera e rallenta alla fine. A destra due pallini percorrono la stessa distanza, uno a velocità costante e uno con easeInOut.', 'On the left a chart of value over time: the grey dashed straight line is linear, the orange S curve is easeInOut, which starts slowly, speeds up and slows down at the end. On the right two dots cover the same distance, one at a constant speed and one with easeInOut.'), L('Stessa durata, stesso arrivo: cambia solo il ritmo.', 'Same duration, same finish: only the pace changes.')),
            P('Per animazioni continue o da comandare a mano (ripetere, fermare, tornare indietro) si usa un `AnimationController` in uno `State` con `SingleTickerProviderStateMixin`, e lo si libera in `dispose`. `Hero` anima un elemento che passa da una pagina all’altra.', 'For continuous animations or ones you drive by hand (repeat, stop, reverse) you use an `AnimationController` in a `State` with `SingleTickerProviderStateMixin`, and release it in `dispose`. `Hero` animates an element moving from one page to another.'),
            C('class _PulsaState extends State<Pulsa> with SingleTickerProviderStateMixin {
  late final AnimationController _c = AnimationController(
    vsync: this,
    duration: const Duration(seconds: 1),
  )..repeat(reverse: true);

  @override
  void dispose() {
    _c.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) =>
      FadeTransition(opacity: _c, child: const Icon(Icons.favorite));
}', 'dart'),
        ], [
            Q('n1', L('Che cosa serve a un `AnimatedContainer` per animarsi?', 'What does an `AnimatedContainer` need to animate?'), [L('Un `AnimationController` obbligatorio', 'A mandatory `AnimationController`'), L('Una `duration` e un valore che cambia con setState', 'A `duration` and a value changed with setState'), L('Un pacchetto esterno da pub.dev', 'An external package from pub.dev'), L('Un `Timer` che ridisegna a ogni frame', 'A `Timer` redrawing at every frame')], 1, L('Il resto lo fa il widget da solo.', 'The widget does the rest by itself.')),
            Q('n2', L('A che cosa serve `vsync: this`?', 'What is `vsync: this` for?'), [L('Rallenta l’animazione sui dispositivi lenti', 'It slows the animation on slow devices'), L('Collega l’animazione al tema scuro', 'It ties the animation to the dark theme'), L('Lega l’animazione ai frame dello schermo e la ferma quando non si vede', 'It ties the animation to screen frames and pauses it when not visible'), L('Salva lo stato dell’animazione tra un avvio e l’altro', 'It saves the animation state between launches')], 2, L('Il ticker arriva da `SingleTickerProviderStateMixin`.', 'The ticker comes from `SingleTickerProviderStateMixin`.')),
            Q('n3', L('Quale widget anima un elemento che passa da una pagina all’altra?', 'Which widget animates an element moving from one page to another?'), ['AnimatedOpacity', 'FadeTransition', 'AnimatedAlign', 'Hero'], 3, L('Serve lo stesso `tag` nelle due pagine.', 'It needs the same `tag` on both pages.')),
        ], task('dart', L('Rendi animato il riquadro: usa `AnimatedContainer` con `duration: const Duration(milliseconds: 300)`.', 'Make the box animated: use `AnimatedContainer` with `duration: const Duration(milliseconds: 300)`.'), 'Container(
  width: grande ? 200 : 100,
  height: 100,
  color: Colors.blue,
)', [re('AnimatedContainer\s*\(', L('Usi AnimatedContainer', 'You use AnimatedContainer')), re('duration\s*:\s*(const\s+)?Duration\s*\(\s*milliseconds\s*:\s*300\s*\)', L('Dura 300 millisecondi', 'It lasts 300 milliseconds'))], '', 'AnimatedContainer(
  duration: const Duration(milliseconds: 300),
  width: grande ? 200 : 100,
  height: 100,
  color: Colors.blue,
)'), [
            dMatch(pMatch(), [['AnimatedContainer', L('Anima dimensioni, colori, bordi', 'Animates size, colours, borders')], ['AnimatedOpacity', L('Anima la trasparenza', 'Animates transparency')], ['AnimationController', L('Comanda un’animazione a mano', 'Drives an animation by hand')], ['Curves.easeInOut', L('Lenta all’inizio e alla fine', 'Slow at the start and the end')], ['Hero', L('Elemento che vola tra due pagine', 'Element flying between two pages')]], L('Gli attrezzi delle animazioni.', 'Animation tools.')),
            dBlanks(pFill(), "class _GiraState extends State<Gira> with ___ {\n  late final _c = AnimationController(vsync: ___, duration: const Duration(seconds: 2));\n  @override\n  void ___() { _c.dispose(); super.dispose(); }\n}", ['SingleTickerProviderStateMixin', 'this', 'dispose'], ['StatefulMixin', 'context', 'close'], L('Ticker, sincronia, pulizia.', 'Ticker, sync, clean-up.')),
        ]),
        lesson('testing', L('Test dei widget', 'Widget tests'), [
            P('Flutter ha tre livelli di test: **unit test** per la logica, **widget test** per un pezzo d’interfaccia, **integration test** per l’app intera su un dispositivo. I widget test girano senza emulatore, in pochi secondi, con `flutter test`.', 'Flutter has three levels of tests: **unit tests** for logic, **widget tests** for a piece of interface, **integration tests** for the whole app on a device. Widget tests run without an emulator, in a few seconds, with `flutter test`.'),
            Fig(L(fig_flutterint_tests(['più realistico', 'e più lento', 'più veloce', 'e più numeroso', 'l’app intera', 'un pezzo di UI', 'una funzione']), fig_flutterint_tests(['more realistic', 'and slower', 'faster', 'and more of them', 'the whole app', 'a piece of UI', 'one function'])), L('Una piramide a tre piani: alla base i test unit, che provano una funzione; al centro i widget test, un pezzo di interfaccia; in cima gli integration test, l’app intera. Una freccia verso l’alto indica che salendo i test sono più realistici e più lenti.', 'A three-level pyramid: unit tests at the base, testing one function; widget tests in the middle, a piece of interface; integration tests at the top, the whole app. An upward arrow shows that going up, tests are more realistic and slower.'), L('Tanti test veloci alla base, pochi test completi in cima.', 'Many fast tests at the base, a few complete ones at the top.')),
            C('import "package:flutter/material.dart";
import "package:flutter_test/flutter_test.dart";
import "package:fucina_app/contatore.dart";

void main() {
  testWidgets("il contatore sale di uno", (tester) async {
    await tester.pumpWidget(const MaterialApp(home: Contatore()));
    expect(find.text("Clic: 0"), findsOneWidget);

    await tester.tap(find.byType(ElevatedButton));
    await tester.pump();

    expect(find.text("Clic: 1"), findsOneWidget);
  });
}', 'dart'),
            P('`pumpWidget` disegna il widget, `find` lo cerca (per testo, tipo, icona o `Key`), `tap` simula il tocco e `pump` ridisegna dopo il cambio di stato. Per la logica pura basta `test()` con `expect(valore, atteso)`.', '`pumpWidget` draws the widget, `find` looks for it (by text, type, icon or `Key`), `tap` simulates a touch and `pump` redraws after the state change. For pure logic, `test()` with `expect(value, expected)` is enough.'),
        ], [
            Q('t1', L('Perché dopo `tester.tap` serve `await tester.pump()`?', 'Why do you need `await tester.pump()` after `tester.tap`?'), [L('Per ripetere il tocco una seconda volta', 'To repeat the tap a second time'), L('Per chiudere il test e liberare memoria', 'To close the test and free memory'), L('Per avviare l’emulatore Android', 'To start the Android emulator'), L('Per ridisegnare il widget dopo il cambio di stato', 'To redraw the widget after the state change')], 3, L('Nel test i frame non arrivano da soli.', 'In a test frames do not come by themselves.')),
            Q('t2', L('Che cosa verifica `expect(find.text("Ciao"), findsOneWidget)`?', 'What does `expect(find.text("Ciao"), findsOneWidget)` check?'), [L('Che sullo schermo ci sia esattamente un widget con quel testo', 'That exactly one widget with that text is on screen'), L('Che il testo sia scritto senza errori di ortografia', 'That the text has no spelling mistakes'), L('Che almeno due widget dicano «Ciao»', 'That at least two widgets say “Ciao”'), L('Che la classe Text esista nel progetto', 'That the Text class exists in the project')], 0, L('Ci sono anche `findsNothing` e `findsWidgets`.', 'There are also `findsNothing` and `findsWidgets`.')),
            Q('t3', L('Quale comando esegue i test?', 'Which command runs the tests?'), ['flutter run --test', 'dart check', 'flutter test', 'flutter doctor'], 2, L('Cerca i file `_test.dart` nella cartella `test`.', 'It looks for `_test.dart` files in the `test` folder.')),
        ], task('dart', L('Nel test tocca l’icona `Icons.add` con `await tester.tap(find.byIcon(Icons.add));` e poi ridisegna con `await tester.pump();`.', 'In the test tap the `Icons.add` icon with `await tester.tap(find.byIcon(Icons.add));` then redraw with `await tester.pump();`.'), 'testWidgets("aggiunge", (tester) async {
  await tester.pumpWidget(const MaterialApp(home: Lista()));
});', [re('await\s+tester\.tap\s*\(\s*find\.byIcon\s*\(\s*Icons\.add\s*\)\s*\)\s*;', L('Tocchi l’icona', 'You tap the icon')), re('tester\.tap[\s\S]*await\s+tester\.pump(AndSettle)?\s*\(\s*\)', L('Ridisegni dopo il tocco', 'You redraw after the tap'))], '', 'testWidgets("aggiunge", (tester) async {
  await tester.pumpWidget(const MaterialApp(home: Lista()));
  await tester.tap(find.byIcon(Icons.add));
  await tester.pump();
});'), [
            dOrder(pSort(), ['testWidgets("saluta", (tester) async {', '  await tester.pumpWidget(const MaterialApp(home: Saluto()));', '  await tester.tap(find.text("Entra"));', '  await tester.pump();', '  expect(find.text("Ciao!"), findsOneWidget);', '});'], L('Disegna, tocca, ridisegna, verifica.', 'Draw, tap, redraw, check.')),
            dMatch(pMatch(), [['pumpWidget', L('Disegna il widget da provare', 'Draws the widget under test')], ['find.byType', L('Cerca per tipo', 'Finds by type')], ['tap', L('Simula un tocco', 'Simulates a tap')], ['pump', L('Ridisegna un frame', 'Redraws a frame')], ['findsNothing', L('Nessun widget trovato', 'No widget found')]], L('Il vocabolario dei widget test.', 'The vocabulary of widget tests.')),
        ]),
    ],
];
