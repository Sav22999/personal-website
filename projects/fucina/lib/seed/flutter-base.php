<?php
/** Flutter basics: Dart, widgets, layout, stateful widgets and navigation. */

return [
    'id' => 'flutter-base', 'family' => 'flutter', 'after' => null,
    'title' => L('Flutter base', 'Flutter Basics'), 'short' => L('Flutter base', 'Flutter Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('App per telefono, web e desktop con un solo codice: Dart, widget, layout, stato e navigazione.', 'Apps for phone, web and desktop from one codebase: Dart, widgets, layout, state and navigation.'),
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
            Q('d1', L('Come inserisci una variabile in una stringa Dart?', 'How do you put a variable in a Dart string?'), ['"Ciao $nome"', '"Ciao {nome}"', '"Ciao " . nome', 'f"Ciao {nome}"'], 0, L('Per le espressioni `${…}`.', 'For expressions `${…}`.')),
            Q('d2', L('Che cosa significa `int?`', 'What does `int?` mean?'), [L('Un intero che può essere null', 'An int that may be null'), L('Un intero casuale', 'A random int'), L('Un errore', 'An error'), L('Un intero positivo', 'A positive int')], 0, L('Null safety.', 'Null safety.')),
            Q('d3', L('Da quale funzione parte un programma Dart?', 'Which function does a Dart program start from?'), ['main', 'start', 'run', 'init'], 0, L('`void main()`.', '`void main()`.')),
        ], task('dart', L('Dichiara `final citta = "Udine";` e stampa `"Vivo a $citta"`.', 'Declare `final citta = "Udine";` and print `"Vivo a $citta"`.'), 'void main() {
}', [re('final\s+(String\s+)?citta\s*=\s*"Udine"\s*;', L('La variabile esiste', 'The variable exists')), re('print\s*\(\s*"Vivo a \$citta"\s*\)\s*;', L('La stampi', 'You print it'))], '', 'final citta = "Udine";
print("Vivo a $citta");'), [
            dMatch(pMatch(), [['final', L('Assegnata una volta', 'Set once')], ['var', L('Tipo dedotto', 'Inferred type')], ['int?', L('Può essere null', 'May be null')], ['$nome', L('Valore in una stringa', 'Value in a string')]], L('Le basi di Dart.', 'Dart basics.')),
            dFlash(pFlash(), [[L('In Dart ogni tipo accetta null', 'In Dart every type accepts null'), false], [L('Le istruzioni finiscono con `;`', 'Statements end with `;`'), true], [L('`final` impedisce di riassegnare', '`final` prevents reassignment'), true]], L('Serve il `?` per ammettere null.', 'You need `?` to allow null.')),
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
            P('`MaterialApp` dà lo stile Material, `Scaffold` la struttura della pagina (barra, corpo, bottone flottante). `const` dice a Flutter che quel widget non cambierà mai.', '`MaterialApp` gives the Material style, `Scaffold` the page structure (bar, body, floating button). `const` tells Flutter the widget will never change.'),
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
            Q('l3', L('Come aggiungi 16 di spazio tutto intorno?', 'How do you add 16 of space all around?'), ['EdgeInsets.all(16)', 'Margin(16)', 'Space(16)', 'Pad(16)'], 0, L('Nel widget `Padding`.', 'In the `Padding` widget.')),
        ], task('dart', L('Crea una `Row` con `children: [Icon(Icons.home), Text("Casa")]`.', 'Create a `Row` with `children: [Icon(Icons.home), Text("Casa")]`.'), '', [re('Row\s*\(\s*children\s*:\s*\[', L('È una riga', 'It is a row')), re('Icon\s*\(\s*Icons\.home\s*\)', L('C’è l’icona', 'There is the icon')), re('Text\s*\(\s*"Casa"\s*\)', L('C’è il testo', 'There is the text'))], '', 'Row(children: [Icon(Icons.home), Text("Casa")])'), [
            dBlanks(pFill(), "___(\n  children: [\n    Text(\"A\"),\n    ___(height: 12),\n    ___(child: Text(\"B\")),\n  ],\n)", ['Column', 'SizedBox', 'Expanded'], ['List', 'Space', 'Grow'], L('Colonna, spazio fisso, spazio rimasto.', 'Column, fixed gap, remaining space.')),
            dFlash(pFlash(), [[L('`Row` dispone in orizzontale', '`Row` lays out horizontally'), true], [L('`Padding` cambia il colore', '`Padding` changes the colour'), false], [L('`Expanded` funziona dentro Row e Column', '`Expanded` works inside Row and Column'), true]], L('`Padding` aggiunge spazio.', '`Padding` adds space.')),
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
            P('Il trattino basso davanti al nome (`_ContatoreState`) rende la classe privata al file.', 'The underscore in front of the name (`_ContatoreState`) makes the class private to the file.'),
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
    ],
];
