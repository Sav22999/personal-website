<?php
/** Flutter basics: Dart, widgets, layout, stateful widgets, navigation, Dart functions and classes, input, packages and assets. */

return [
    'id' => 'flutter-base', 'family' => 'flutter', 'after' => null,
    'title' => L('Flutter base', 'Flutter Basics'), 'short' => L('Flutter base', 'Flutter Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('App per telefono, web e desktop con un solo codice: Dart, widget, layout, stato, navigazione, input, pacchetti e immagini.', 'Apps for phone, web and desktop from one codebase: Dart, widgets, layout, state, navigation, input, packages and images.'),
    'lessons' => [
        lesson('dart', L('Un assaggio di Dart', 'A taste of Dart'), [
            P('Flutter si scrive in **Dart**. Le variabili si dichiarano con il tipo o con `var`/`final`; `final` non cambia più. Le stringhe accettano valori con `$nome` o `${espressione}`.', 'Flutter is written in **Dart**. Variables are declared with a type or with `var`/`final`; `final` never changes again. Strings take values with `$nome` or `${expression}`.'),
            C('void main() {
  final String nome = "Ada";
  var anni = 36;
  int? telefono;            // può essere null
  print("Ciao $nome, tra un anno ne avrai ${anni + 1}");
  final voti = [7, 8, 9];
  print(voti.where((v) => v > 7).toList());
}', 'dart'),
            P('Dart è **null-safe**: una variabile può contenere `null` solo se il tipo ha il punto di domanda (`int?`).', 'Dart is **null-safe**: a variable can hold `null` only if its type has a question mark (`int?`).'),
        ], [
            Q('d1', L('Come inserisci una variabile in una stringa Dart?', 'How do you put a variable in a Dart string?'), ['"Ciao $nome"', '"Ciao {nome}"', '"Ciao " . nome', 'f"Ciao {nome}"'], 0, L('Con `$nome`; per le espressioni serve `${…}`.', 'With `$nome`; expressions need `${…}`.')),
            Q('d2', L('Che cosa significa `int?`', 'What does `int?` mean?'), [L('Un intero che può essere null', 'An int that may be null'), L('Un intero casuale', 'A random int'), L('Un errore', 'An error'), L('Un intero positivo', 'A positive int')], 0, L('Null safety.', 'Null safety.')),
            Q('d3', L('Da quale funzione parte un programma Dart?', 'Which function does a Dart program start from?'), ['main', 'start', 'run', 'init'], 0, L('`void main()`.', '`void main()`.')),
        ], task('dart', L('Dichiara `final citta = "Udine";` e stampa `"Vivo a $citta"`.', 'Declare `final citta = "Udine";` and print `"Vivo a $citta"`.'), 'void main() {
}', [re('final\s+(String\s+)?citta\s*=\s*"Udine"\s*;', L('La variabile esiste', 'The variable exists')), re('print\s*\(\s*"Vivo a \$citta"\s*\)\s*;', L('La stampi', 'You print it'))], '', 'final citta = "Udine";
print("Vivo a $citta");'), [
            dMatch(pMatch(), [['final', L('Assegnata una volta', 'Set once')], ['var', L('Tipo dedotto', 'Inferred type')], ['int?', L('Può essere null', 'May be null')], ['$nome', L('Valore in una stringa', 'Value in a string')]], L('Le basi di Dart.', 'Dart basics.')),
            dFlash(pFlash(), [[L('In Dart ogni tipo accetta null', 'In Dart every type accepts null'), false], [L('Le istruzioni finiscono con `;`', 'Statements end with `;`'), true], [L('`final` impedisce di riassegnare', '`final` prevents reassignment'), true], [L('`var` deduce il tipo dal valore iniziale', '`var` infers the type from the first value'), true]], L('Serve il `?` per ammettere null.', 'You need `?` to allow null.')),
        ]),
        lesson('widgets', L('Tutto è un widget', 'Everything is a widget'), [
            P('In Flutter l’interfaccia è un **albero di widget**: testi, bottoni, colonne, margini. Un `StatelessWidget` non cambia: il suo metodo `build` restituisce i widget da mostrare.', 'In Flutter the interface is a **tree of widgets**: texts, buttons, columns, margins. A `StatelessWidget` does not change: its `build` method returns the widgets to show.'),
            C('void main() => runApp(const App());

class App extends StatelessWidget {
  const App({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      home: Scaffold(
        appBar: AppBar(title: const Text("Fucina")),
        body: const Center(child: Text("Ciao!")),
      ),
    );
  }
}', 'dart'),
            P('`MaterialApp` dà lo stile Material, `Scaffold` la struttura della pagina (barra, corpo, bottone flottante). `const` crea il widget una volta sola, già in compilazione: Flutter lo riusa senza ricostruirlo.', '`MaterialApp` gives the Material style, `Scaffold` the page structure (bar, body, floating button). `const` builds the widget once, at compile time: Flutter reuses it without rebuilding it.'),
        ], [
            Q('w1', L('Quale metodo descrive l’interfaccia di un widget?', 'Which method describes a widget’s interface?'), ['build', 'render', 'draw', 'paint'], 0, L('Restituisce un `Widget`.', 'It returns a `Widget`.')),
            Q('w2', L('Che cosa dà `Scaffold`?', 'What does `Scaffold` give?'), [L('La struttura di una pagina', 'The structure of a page'), L('Un database', 'A database'), L('Un font', 'A font'), L('Un’animazione', 'An animation')], 0, L('Barra, corpo, bottoni.', 'Bar, body, buttons.')),
            Q('w3', L('Quale funzione avvia l’app?', 'Which function starts the app?'), ['runApp', 'startApp', 'main.run', 'launch'], 0, L('Dentro `main`.', 'Inside `main`.')),
        ], task('dart', L('Nel `body` metti `Center(child: Text("Benvenuto"))`.', 'In the `body` put `Center(child: Text("Benvenuto"))`.'), 'Scaffold(
  body: ,
)', [re('body\s*:\s*(const\s+)?Center\s*\(\s*child\s*:\s*(const\s+)?Text\s*\(\s*"Benvenuto"\s*\)\s*\)', L('Il testo è centrato', 'The text is centred'))], '', 'Scaffold(
  body: Center(child: Text("Benvenuto")),
)'), [
            dOrder(pSort(), ['MaterialApp(', '  home: Scaffold(', '    appBar: AppBar(title: Text("Note")),', '    body: Center(child: Text("Vuoto")),', '  ),', ')'], L('App, pagina, barra, corpo.', 'App, page, bar, body.')),
            dMatch(pMatch(), [['Text', L('Un testo', 'A text')], ['Center', L('Centra il figlio', 'Centres its child')], ['Scaffold', L('Struttura della pagina', 'Page structure')], ['AppBar', L('Barra in alto', 'Top bar')], ['MaterialApp', L('L’app con stile Material', 'The Material-styled app')]], L('I primi widget.', 'The first widgets.')),
        ]),
        lesson('layout', L('Layout', 'Layout'), [
            P('`Column` mette i figli uno sotto l’altro, `Row` uno accanto all’altro. `Padding` aggiunge spazio, `SizedBox` crea uno spazio fisso, `Expanded` fa occupare lo spazio rimasto.', '`Column` stacks children vertically, `Row` side by side. `Padding` adds space, `SizedBox` creates a fixed gap, `Expanded` takes the remaining space.'),
            C('Padding(
  padding: const EdgeInsets.all(16),
  child: Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      const Text("Titolo", style: TextStyle(fontSize: 24)),
      const SizedBox(height: 8),
      Row(children: [
        const Icon(Icons.star),
        Expanded(child: Text(descrizione)),
      ]),
    ],
  ),
)', 'dart'),
            P('Come in Flexbox, c’è un asse principale (`mainAxisAlignment`) e uno trasversale (`crossAxisAlignment`).', 'As in Flexbox, there is a main axis (`mainAxisAlignment`) and a cross axis (`crossAxisAlignment`).'),
        ], [
            Q('l1', L('Quale widget mette i figli in verticale?', 'Which widget stacks children vertically?'), ['Column', 'Row', 'Stack', 'Wrap'], 0, L('`Row` li mette in orizzontale.', '`Row` puts them horizontally.')),
            Q('l2', L('Che cosa fa `Expanded`?', 'What does `Expanded` do?'), [L('Occupa lo spazio rimasto', 'Takes the remaining space'), L('Ingrandisce il testo', 'Enlarges text'), L('Apre un menu', 'Opens a menu'), L('Niente', 'Nothing')], 0, L('Dentro `Row` o `Column`.', 'Inside `Row` or `Column`.')),
            Q('l3', L('Come aggiungi 16 pixel logici di spazio tutto intorno?', 'How do you add 16 logical pixels of space all around?'), ['EdgeInsets.all(16)', 'Margin(16)', 'Space(16)', 'Pad(16)'], 0, L('Nel widget `Padding`.', 'In the `Padding` widget.')),
        ], task('dart', L('Crea una `Row` con `children: [Icon(Icons.home), Text("Casa")]`.', 'Create a `Row` with `children: [Icon(Icons.home), Text("Casa")]`.'), '', [re('Row\s*\(\s*children\s*:\s*\[', L('È una riga', 'It is a row')), re('Icon\s*\(\s*Icons\.home\s*\)', L('C’è l’icona', 'There is the icon')), re('Text\s*\(\s*"Casa"\s*\)', L('C’è il testo', 'There is the text'))], '', 'Row(children: [Icon(Icons.home), Text("Casa")])'), [
            dBlanks(pFill(), "___(\n  children: [\n    Text(\"A\"),\n    ___(height: 12),\n    ___(child: Text(\"B\")),\n  ],\n)", ['Column', 'SizedBox', 'Expanded'], ['List', 'Space', 'Grow'], L('Colonna, spazio fisso, spazio rimasto.', 'Column, fixed gap, remaining space.')),
            dFlash(pFlash(), [[L('`Row` dispone in orizzontale', '`Row` lays out horizontally'), true], [L('`Padding` cambia il colore', '`Padding` changes the colour'), false], [L('`Expanded` funziona dentro Row e Column', '`Expanded` works inside Row and Column'), true], [L('`Column` dispone i figli in orizzontale', '`Column` lays its children out horizontally'), false]], L('`Padding` aggiunge spazio.', '`Padding` adds space.')),
        ]),
        lesson('state', L('Stato e setState', 'State and setState'), [
            P('Un `StatefulWidget` può cambiare nel tempo. Lo stato vive in una classe `State`; quando cambi un valore lo fai dentro `setState`, così Flutter ridisegna il widget.', 'A `StatefulWidget` can change over time. Its state lives in a `State` class; when you change a value you do it inside `setState`, so Flutter redraws the widget.'),
            C('class Contatore extends StatefulWidget {
  const Contatore({super.key});
  @override
  State<Contatore> createState() => _ContatoreState();
}

class _ContatoreState extends State<Contatore> {
  int n = 0;

  @override
  Widget build(BuildContext context) {
    return ElevatedButton(
      onPressed: () => setState(() => n++),
      child: Text("Clic: $n"),
    );
  }
}', 'dart'),
            P('Il trattino basso davanti al nome (`_ContatoreState`) rende la classe privata al file (alla sua libreria, per la precisione).', 'The underscore in front of the name (`_ContatoreState`) makes the class private to the file (to its library, to be precise).'),
        ], [
            Q('s1', L('Perché si usa `setState`?', 'Why use `setState`?'), [L('Per ridisegnare il widget', 'To redraw the widget'), L('Per salvare su disco', 'To save to disk'), L('Per chiudere l’app', 'To close the app'), L('Per cambiare tema', 'To change theme')], 0, L('Senza, lo schermo non si aggiorna.', 'Without it the screen does not update.')),
            Q('s2', L('Dove vive lo stato di un StatefulWidget?', 'Where does a StatefulWidget’s state live?'), [L('Nella classe State', 'In the State class'), L('Nel widget stesso', 'In the widget itself'), L('Nel main', 'In main'), L('In un file JSON', 'In a JSON file')], 0, L('Creata da `createState`.', 'Created by `createState`.')),
            Q('s3', L('Che cosa significa il `_` davanti a un nome?', 'What does `_` in front of a name mean?'), [L('Privato al file', 'Private to the file'), L('Statico', 'Static'), L('Costante', 'Constant'), L('Asincrono', 'Async')], 0, L('Dart non ha `private`.', 'Dart has no `private`.')),
        ], task('dart', L('Nel bottone chiama `setState(() => acceso = !acceso)`.', 'In the button call `setState(() => acceso = !acceso)`.'), 'ElevatedButton(
  onPressed: () {},
  child: Text(acceso ? "Spegni" : "Accendi"),
)', [re('setState\s*\(\s*\(\)\s*(=>|\{)\s*acceso\s*=\s*!\s*acceso', L('Cambi lo stato', 'You change the state'))], '', 'onPressed: () => setState(() => acceso = !acceso),'), [
            dBug(pBug(), ['int voti = 0;', 'void aggiungi() {', '  voti++;', '}'], 2, '  setState(() => voti++);', L('Senza `setState` il valore cambia ma lo schermo no.', 'Without `setState` the value changes but the screen does not.')),
            dOrder(pSort(), ['class _ProfiloState extends State<Profilo> {', '  bool seguito = false;', '  @override', '  Widget build(BuildContext context) {', '    return Switch(value: seguito, onChanged: (v) => setState(() => seguito = v));', '  }', '}'], L('Stato, poi build che lo usa.', 'State, then the build that uses it.')),
        ]),
        lesson('navigation', L('Liste e navigazione', 'Lists and navigation'), [
            P('`ListView.builder` crea solo le righe visibili, anche con migliaia di elementi. `Navigator.push` apre una nuova pagina sopra quella attuale; `Navigator.pop` torna indietro.', '`ListView.builder` builds only the visible rows, even with thousands of items. `Navigator.push` opens a new page on top of the current one; `Navigator.pop` goes back.'),
            C('ListView.builder(
  itemCount: corsi.length,
  itemBuilder: (context, i) => ListTile(
    title: Text(corsi[i].titolo),
    trailing: const Icon(Icons.chevron_right),
    onTap: () => Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => CorsoPage(corso: corsi[i])),
    ),
  ),
)', 'dart'),
            P('Per app più grandi si usano router con indirizzi, come il pacchetto `go_router`.', 'Larger apps use routers with addresses, such as the `go_router` package.'),
        ], [
            Q('n1', L('Perché `ListView.builder` per liste lunghe?', 'Why `ListView.builder` for long lists?'), [L('Crea solo le righe visibili', 'It builds only visible rows'), L('È più colorato', 'It is more colourful'), L('Ordina da solo', 'It sorts by itself'), L('Non serve', 'It is not needed')], 0, L('Risparmia memoria.', 'It saves memory.')),
            Q('n2', L('Come apri una nuova pagina?', 'How do you open a new page?'), ['Navigator.push', 'Navigator.open', 'Page.show', 'Route.go'], 0, L('Con una `MaterialPageRoute`.', 'With a `MaterialPageRoute`.')),
            Q('n3', L('Come torni indietro?', 'How do you go back?'), ['Navigator.pop', 'Navigator.back', 'Page.close', 'Route.exit'], 0, L('Toglie la pagina in cima.', 'It removes the top page.')),
        ], task('dart', L('Nel `onTap` torna indietro con `Navigator.pop(context)`.', 'In `onTap` go back with `Navigator.pop(context)`.'), 'ListTile(
  title: Text("Chiudi"),
  onTap: () {},
)', [re('onTap\s*:\s*\(\)\s*(=>|\{)\s*Navigator\.pop\s*\(\s*context\s*\)', L('Torni indietro', 'You go back'))], '', 'onTap: () => Navigator.pop(context),'), [
            dBlanks(pFill(), "ListView.___(\n  itemCount: note.length,\n  itemBuilder: (context, i) => ListTile(\n    title: Text(note[i]),\n    onTap: () => Navigator.___(context, MaterialPageRoute(builder: (_) => Dettaglio(note[i]))),\n  ),\n)", ['builder', 'push'], ['list', 'open', 'go'], L('Lista pigra, nuova pagina.', 'Lazy list, new page.')),
            dMatch(pMatch(), [['ListView.builder', L('Lista lunga ed efficiente', 'Long, efficient list')], ['ListTile', L('Riga pronta', 'Ready-made row')], ['Navigator.push', L('Apre una pagina', 'Opens a page')], ['Navigator.pop', L('Torna indietro', 'Goes back')]], L('Liste e pagine.', 'Lists and pages.')),
        ]),
        lesson('dart-functions', L('Funzioni e classi in Dart', 'Functions and classes in Dart'), [
            P('In Dart le funzioni hanno parametri posizionali e **parametri con nome**, scritti tra graffe: si passano indicando il nome e possono essere `required` o avere un valore predefinito. Una funzione di una sola espressione si scrive con la freccia `=>`.', 'In Dart functions have positional parameters and **named parameters**, written in braces: you pass them by name and they can be `required` or have a default value. A single-expression function is written with the arrow `=>`.'),
            C('int somma(int a, int b) => a + b;

String saluta({required String nome, String saluto = "Ciao"}) {
  return "$saluto, $nome!";
}

class Corso {
  final String titolo;
  final int livello;
  const Corso(this.titolo, {this.livello = 1});

  String get etichetta => "$titolo (livello $livello)";
}

void main() {
  print(somma(2, 3));
  print(saluta(nome: "Ada"));
  final c = Corso("Flutter", livello: 2);
  print(c.etichetta);
}', 'dart'),
            P('`this.titolo` nel costruttore assegna il campo in un colpo solo; `get` crea una proprietà calcolata. I widget sono classi fatte proprio così: per questo scrivi `Text("Ciao", style: …)` con parametri con nome.', '`this.titolo` in the constructor sets the field in one go; `get` creates a computed property. Widgets are classes built exactly like this: that is why you write `Text("Ciao", style: …)` with named parameters.'),
        ], [
            Q('f1', L('Come dichiari un parametro con nome obbligatorio?', 'How do you declare a required named parameter?'), ['[String nome]', '(named String nome)', '{required String nome}', '{String! nome}'], 2, L('Le graffe lo rendono con nome, `required` obbligatorio. Le quadre servono per i posizionali facoltativi.', 'Braces make it named, `required` makes it mandatory. Square brackets are for optional positional ones.')),
            Q('f2', L('Che cosa fa `int doppio(int n) => n * 2;`?', 'What does `int doppio(int n) => n * 2;` do?'), [L('Dichiara una variabile chiamata doppio', 'Declares a variable called doppio'), L('Restituisce il doppio di n', 'Returns double n'), L('Confronta n con 2 e restituisce un bool', 'Compares n with 2 and returns a bool'), L('Crea una lista con n elementi', 'Creates a list of n items')], 1, L('La freccia equivale a `{ return n * 2; }`.', 'The arrow is the same as `{ return n * 2; }`.')),
            Q('f3', L('Nel costruttore `Corso(this.titolo)`, che cosa fa `this.titolo`?', 'In the constructor `Corso(this.titolo)`, what does `this.titolo` do?'), [L('Legge il titolo da un file', 'Reads the title from a file'), L('Rende il campo facoltativo', 'Makes the field optional'), L('Crea un getter chiamato titolo', 'Creates a getter called titolo'), L('Assegna il parametro al campo titolo', 'Assigns the parameter to the titolo field')], 3, L('Una scorciatoia per `this.titolo = titolo`.', 'A shortcut for `this.titolo = titolo`.')),
        ], task('dart', L('Scrivi la classe `Libro` con il campo `final String titolo;` e il costruttore `Libro(this.titolo);`.', 'Write the class `Libro` with the field `final String titolo;` and the constructor `Libro(this.titolo);`.'), 'class Libro {
}', [re('class\s+Libro\s*\{', L('La classe esiste', 'The class exists')), re('final\s+String\s+titolo\s*;', L('C’è il campo', 'There is the field')), re('(const\s+)?Libro\s*\(\s*this\.titolo\s*\)\s*;', L('C’è il costruttore', 'There is the constructor'))], '', 'class Libro {
  final String titolo;
  Libro(this.titolo);
}'), [
            dBlanks(pFill(), "String saluta({___ String nome}) => \"Ciao \$nome\";\nclass Punto {\n  final int x, y;\n  const Punto(___.x, this.y);\n}\nprint(saluta(___: \"Ada\"));", ['required', 'this', 'nome'], ['final', 'self', 'name'], L('Parametro obbligatorio, campo dal costruttore, argomento con nome.', 'Required parameter, field from the constructor, named argument.')),
            dFlash(pFlash(), [[L('I parametri con nome si scrivono tra graffe', 'Named parameters are written in braces'), true], [L('`=>` si usa solo nelle funzioni anonime', '`=>` is only used in anonymous functions'), false], [L('Un campo `final` si assegna una sola volta', 'A `final` field is set only once'), true], [L('Ogni classe Dart ha bisogno di un costruttore scritto a mano', 'Every Dart class needs a hand-written constructor'), false]], L('Se non lo scrivi, Dart crea un costruttore vuoto.', 'If you do not write one, Dart creates an empty constructor.')),
        ]),
        lesson('input', L('Bottoni e campi di testo', 'Buttons and text fields'), [
            P('Material 3 offre più bottoni: `FilledButton` per l’azione principale, `OutlinedButton` e `TextButton` per quelle secondarie, `IconButton` per un’icona sola. Se `onPressed` è `null`, il bottone appare disattivato.', 'Material 3 offers several buttons: `FilledButton` for the main action, `OutlinedButton` and `TextButton` for secondary ones, `IconButton` for a lone icon. If `onPressed` is `null`, the button shows as disabled.'),
            C('class _RicercaState extends State<Ricerca> {
  final _controller = TextEditingController();
  String _testo = "";

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Column(children: [
      TextField(
        controller: _controller,
        decoration: const InputDecoration(labelText: "Cerca"),
        onChanged: (v) => setState(() => _testo = v),
      ),
      FilledButton(
        onPressed: _testo.isEmpty ? null : () => cerca(_testo),
        child: const Text("Vai"),
      ),
    ]);
  }
}', 'dart'),
            P('`onChanged` riceve il testo a ogni tasto. Il controller va liberato in `dispose`, che Flutter chiama quando il widget sparisce; il suo gemello `initState` è il posto per preparare le cose una volta sola, all’inizio.', '`onChanged` receives the text at every keystroke. The controller must be released in `dispose`, which Flutter calls when the widget goes away; its twin `initState` is the place to set things up once, at the start.'),
        ], [
            Q('i1', L('Come mostri un bottone disattivato?', 'How do you show a disabled button?'), [L('Con `enabled: false` dentro il `Text`', 'With `enabled: false` inside the `Text`'), L('Togliendo il `child` del bottone', 'By removing the button’s `child`'), L('Passando `null` a `onPressed`', 'By passing `null` to `onPressed`'), L('Con `visible: false` sul bottone', 'With `visible: false` on the button')], 2, L('Flutter lo disegna spento e ignora i tocchi.', 'Flutter draws it greyed out and ignores taps.')),
            Q('i2', L('Quando viene chiamato `onChanged` di un `TextField`?', 'When is a `TextField`’s `onChanged` called?'), [L('A ogni modifica del testo', 'At every change of the text'), L('Solo quando premi Invio', 'Only when you press Enter'), L('Solo quando il campo perde il focus', 'Only when the field loses focus'), L('Una volta sola, alla creazione', 'Once, when it is created')], 0, L('Per l’Invio c’è `onSubmitted`.', 'For Enter there is `onSubmitted`.')),
            Q('i3', L('In quale metodo liberi un `TextEditingController`?', 'In which method do you release a `TextEditingController`?'), ['initState', 'build', 'setState', 'dispose'], 3, L('`dispose` viene chiamato quando il widget lascia lo schermo per sempre.', '`dispose` is called when the widget leaves the screen for good.')),
        ], task('dart', L('Aggiungi al `TextField` un `onChanged` che salva il testo con `setState(() => nome = v)`.', 'Add to the `TextField` an `onChanged` that stores the text with `setState(() => nome = v)`.'), 'TextField(
  decoration: const InputDecoration(labelText: "Nome"),
)', [re('onChanged\s*:\s*\(\s*(\w+)\s*\)\s*(=>|\{)\s*setState\s*\(\s*\(\s*\)\s*(=>|\{)\s*nome\s*=\s*\1', L('Salvi il testo nello stato', 'You store the text in the state'))], '', 'TextField(
  decoration: const InputDecoration(labelText: "Nome"),
  onChanged: (v) => setState(() => nome = v),
)'), [
            dMatch(pMatch(), [['FilledButton', L('Azione principale', 'Main action')], ['TextButton', L('Azione secondaria, solo testo', 'Secondary action, text only')], ['IconButton', L('Bottone con un’icona', 'Button with an icon')], ['onPressed: null', L('Bottone disattivato', 'Disabled button')]], L('I bottoni di Material 3.', 'Material 3 buttons.')),
            dBug(pBug(), ['TextField(', '  controller: _controller,', '  onChanged: (v) => _testo = v,', ')'], 2, '  onChanged: (v) => setState(() => _testo = v),', L('Senza `setState` il valore cambia ma lo schermo che lo usa non si aggiorna.', 'Without `setState` the value changes but the screen using it does not update.')),
        ]),
        lesson('assets-packages', L('Pacchetti, immagini e hot reload', 'Packages, images and hot reload'), [
            P('Ogni progetto ha un file `pubspec.yaml`: nome, versione di Dart, **dipendenze** e risorse. I pacchetti si cercano su **pub.dev** e si aggiungono con `flutter pub add nome`, che aggiorna il file e li scarica.', 'Every project has a `pubspec.yaml` file: name, Dart version, **dependencies** and assets. Packages are found on **pub.dev** and added with `flutter pub add name`, which updates the file and downloads them.'),
            C('name: fucina_app
environment:
  sdk: ^3.5.0
dependencies:
  flutter:
    sdk: flutter
  http: ^1.2.0

flutter:
  uses-material-design: true
  assets:
    - assets/images/', 'yaml'),
            C('Image.asset("assets/images/logo.png", width: 120)
Image.network("https://example.com/foto.jpg", fit: BoxFit.cover)', 'dart'),
            P('Con l’**hot reload** (tasto `r` nel terminale di `flutter run`) le modifiche appaiono in un attimo e lo stato resta; l’**hot restart** (`R`) riparte da `main` e azzera lo stato. Serve il restart quando cambi `main`, `initState` o le variabili globali.', 'With **hot reload** (the `r` key in the `flutter run` terminal) changes appear in a moment and state is kept; **hot restart** (`R`) starts again from `main` and resets state. You need a restart when you change `main`, `initState` or global variables.'),
        ], [
            Q('a1', L('Quale file elenca le dipendenze di un progetto Flutter?', 'Which file lists a Flutter project’s dependencies?'), ['package.json', 'build.gradle', 'pubspec.yaml', 'Cargo.toml'], 2, L('Gli altri sono di npm, Gradle e Cargo.', 'The others belong to npm, Gradle and Cargo.')),
            Q('a2', L('Un contatore segna 5. Dopo un hot reload, che cosa mostra?', 'A counter shows 5. After a hot reload, what does it show?'), [L('Riparte da 0', 'It starts again from 0'), L('Resta a 5', 'It stays at 5'), L('Diventa null', 'It becomes null'), L('L’app si chiude', 'The app closes')], 1, L('L’hot reload aggiorna il codice ma tiene lo stato; l’hot restart lo azzera.', 'Hot reload updates the code but keeps state; hot restart resets it.')),
            Q('a3', L('Come mostri un’immagine inclusa nell’app?', 'How do you show an image bundled with the app?'), ['Image.network("assets/logo.png")', 'Picture.load("assets/logo.png")', 'Asset.image("logo.png")', 'Image.asset("assets/logo.png")'], 3, L('E la sua cartella va dichiarata sotto `assets:` nel pubspec.yaml.', 'And its folder must be listed under `assets:` in pubspec.yaml.')),
        ], task('dart', L('Nel `child` mostra `assets/logo.png` largo 120 con `Image.asset`.', 'In the `child` show `assets/logo.png` 120 wide with `Image.asset`.'), 'Center(
  child: ,
)', [re('child\s*:\s*Image\.asset\s*\(\s*["\']assets/logo\.png["\']\s*,\s*width\s*:\s*120(\.0)?\s*[,)]', L('L’immagine è larga 120', 'The image is 120 wide'))], '', 'Center(
  child: Image.asset("assets/logo.png", width: 120),
)'), [
            dOrder(pSort(), ['flutter create fucina_app', 'cd fucina_app', 'flutter pub add http', 'flutter run'], L('Crea, entra nella cartella, aggiungi il pacchetto, avvia.', 'Create, enter the folder, add the package, run.')),
            dFlash(pFlash(), [[L('I pacchetti Dart si trovano su pub.dev', 'Dart packages are found on pub.dev'), true], [L('L’hot restart conserva lo stato', 'Hot restart keeps state'), false], [L('Le immagini locali vanno dichiarate nel pubspec.yaml', 'Local images must be listed in pubspec.yaml'), true], [L('`flutter pub add` modifica il pubspec.yaml', '`flutter pub add` edits pubspec.yaml'), true]], L('Il restart riparte da zero.', 'A restart starts from scratch.')),
        ]),
    ],
];
