<?php
/** Flutter intermediate: forms, async data, state management, themes and local storage. */

return [
    'id' => 'flutter-intermediate', 'family' => 'flutter', 'after' => 'flutter-base',
    'title' => L('Flutter intermedio', 'Intermediate Flutter'), 'short' => L('Flutter intermedio', 'Flutter Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Moduli e validazione, dati dalla rete, gestione dello stato, temi e salvataggi sul dispositivo.', 'Forms and validation, data from the network, state management, themes and on-device storage.'),
    'lessons' => [
        lesson('forms', L('Moduli e validazione', 'Forms and validation'), [
            P('Un `Form` raccoglie più `TextFormField`; ogni campo ha un `validator` che restituisce un messaggio d’errore o `null` se va bene. Una `GlobalKey<FormState>` permette di validare tutto insieme.', 'A `Form` gathers several `TextFormField`s; each field has a `validator` returning an error message, or `null` when fine. A `GlobalKey<FormState>` lets you validate everything at once.'),
            C('final _chiave = GlobalKey<FormState>();

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
)', 'dart'),
            P('Un `TextEditingController` legge e cambia il testo di un campo da codice.', 'A `TextEditingController` reads and changes a field’s text from code.'),
        ], [
            Q('f1', L('Che cosa restituisce un validator se il valore è giusto?', 'What does a validator return when the value is fine?'), ['null', 'true', '""', '0'], 0, L('Una stringa indica l’errore.', 'A string signals the error.')),
            Q('f2', L('Come validi tutto il modulo?', 'How do you validate the whole form?'), ['chiave.currentState!.validate()', 'form.check()', 'validateAll()', 'Form.ok'], 0, L('Tramite la GlobalKey.', 'Through the GlobalKey.')),
            Q('f3', L('A che cosa serve un `TextEditingController`?', 'What is a `TextEditingController` for?'), [L('Leggere e cambiare il testo da codice', 'Reading and changing text from code'), L('Correggere l’ortografia', 'Spell-checking'), L('Cambiare font', 'Changing font'), L('Nascondere la tastiera', 'Hiding the keyboard')], 0, L('Ricorda di chiamare `dispose`.', 'Remember to call `dispose`.')),
        ], task('dart', L('Aggiungi un `validator` che restituisce `"Obbligatorio"` se `v` è vuoto, altrimenti `null`.', 'Add a `validator` returning `"Obbligatorio"` if `v` is empty, otherwise `null`.'), 'TextFormField(
  decoration: const InputDecoration(labelText: "Nome"),
)', [re('validator\s*:\s*\(\s*v\s*\)\s*=>', L('C’è il validator', 'There is the validator')), re('"Obbligatorio"\s*:\s*null', L('Messaggio o null', 'Message or null'))], '', 'validator: (v) => (v == null || v.isEmpty) ? "Obbligatorio" : null,'), [
            dOrder(pSort(), ['final chiave = GlobalKey<FormState>();', 'Form(', '  key: chiave,', '  child: TextFormField(validator: (v) => v!.isEmpty ? "Vuoto" : null),', ')'], L('Chiave, modulo, campo con regola.', 'Key, form, field with rule.')),
            dFlash(pFlash(), [[L('Un validator restituisce `null` se va tutto bene', 'A validator returns `null` when all is fine'), true], [L('`validate()` controlla un solo campo', '`validate()` checks a single field'), false], [L('I controller vanno liberati con `dispose`', 'Controllers must be released with `dispose`'), true]], L('`validate()` controlla tutto il modulo.', '`validate()` checks the whole form.')),
        ]),
        lesson('async', L('Future e dati dalla rete', 'Futures and network data'), [
            P('Le operazioni lente restituiscono un `Future`. Con `async`/`await` si scrivono come codice normale. `FutureBuilder` mostra un widget diverso mentre aspetta, in caso di errore o con i dati.', 'Slow operations return a `Future`. With `async`/`await` you write them like normal code. `FutureBuilder` shows a different widget while waiting, on error or with data.'),
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
            dFlash(pFlash(), [[L('`context.watch` ridisegna quando lo stato cambia', '`context.watch` redraws on state change'), true], [L('Senza `notifyListeners` i widget si aggiornano lo stesso', 'Without `notifyListeners` widgets update anyway'), false], [L('Esistono anche Riverpod e Bloc', 'Riverpod and Bloc exist too'), true]], L('Bisogna avvisare.', 'You must notify.')),
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
            P('Per piccole preferenze c’è `shared_preferences`: chiave e valore, come il localStorage del web. Per dati strutturati si usa un database locale come **SQLite** (pacchetto `sqflite`).', 'For small preferences there is `shared_preferences`: key and value, like the web’s localStorage. For structured data use a local database such as **SQLite** (the `sqflite` package).'),
            C('final prefs = await SharedPreferences.getInstance();
await prefs.setBool("tema_scuro", true);
final scuro = prefs.getBool("tema_scuro") ?? false;

final db = await openDatabase("note.db", version: 1,
  onCreate: (db, v) => db.execute("CREATE TABLE note (id INTEGER PRIMARY KEY, testo TEXT)"));
await db.insert("note", {"testo": "Comprare il pane"});', 'dart'),
            P('Non salvare mai password o token in chiaro: per i segreti esiste `flutter_secure_storage`, che usa il portachiavi del sistema.', 'Never store passwords or tokens in clear text: for secrets there is `flutter_secure_storage`, which uses the system keychain.'),
        ], [
            Q('s1', L('Dove salvi una preferenza semplice?', 'Where do you save a simple preference?'), ['shared_preferences', 'sqflite', L('In un widget', 'In a widget'), L('Nel codice', 'In the code')], 0, L('Chiave e valore.', 'Key and value.')),
            Q('s2', L('Che cosa dà `getBool` se la chiave non esiste?', 'What does `getBool` give if the key is missing?'), ['null', 'false', L('Un errore', 'An error'), 'true'], 0, L('Per questo si usa `??`.', 'That is why `??` is used.')),
            Q('s3', L('Dove vanno i token di accesso?', 'Where do access tokens go?'), ['flutter_secure_storage', 'shared_preferences', L('Un file di testo', 'A text file'), L('Il nome dell’app', 'The app name')], 0, L('Nel portachiavi cifrato.', 'In the encrypted keychain.')),
        ], task('dart', L('Salva `await prefs.setString("lingua", "it");`.', 'Save `await prefs.setString("lingua", "it");`.'), 'final prefs = await SharedPreferences.getInstance();
', [re('await\s+prefs\.setString\s*\(\s*"lingua"\s*,\s*"it"\s*\)\s*;', L('Salvi la lingua', 'You save the language'))], '', 'await prefs.setString("lingua", "it");'), [
            dBlanks(pFill(), "final prefs = ___ SharedPreferences.getInstance();\nfinal volume = prefs.getInt(\"volume\") ___ 5;", ['await', '??'], ['async', '||', '?:'], L('Attendi e dai un valore di riserva.', 'Await and give a fallback.')),
            dFlash(pFlash(), [[L('shared_preferences va bene per le password', 'shared_preferences is fine for passwords'), false], [L('SQLite è un database locale', 'SQLite is a local database'), true], [L('`??` dà un valore se a sinistra c’è null', '`??` gives a value when the left is null'), true]], L('I segreti vanno nel portachiavi.', 'Secrets go in the keychain.')),
        ]),
    ],
];
