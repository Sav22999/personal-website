<?php
/** Vala intermediate: interfaces, collections with GLib and Gee, errors, async and a GTK window. */

return [
    'id' => 'vala-intermediate', 'family' => 'vala', 'after' => 'vala-base',
    'title' => L('Vala intermedio', 'Intermediate Vala'), 'short' => L('Vala intermedio', 'Vala Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Interfacce, collezioni, errori, metodi asincroni e una prima finestra GTK per il desktop GNOME.', 'Interfaces, collections, errors, async methods and a first GTK window for the GNOME desktop.'),
    'lessons' => [
        lesson('interfaces', L('Interfacce ed ereditarietà', 'Interfaces and inheritance'), [
            P('Una classe eredita da una sola classe ma può implementare più **interfacce**. I metodi da ridefinire sono `virtual` (o `abstract`) nella classe madre e `override` nella figlia.', 'A class inherits from one class but can implement several **interfaces**. Methods to redefine are `virtual` (or `abstract`) in the parent and `override` in the child.'),
            C('public interface Descrivibile : Object {
    public abstract string descrivi ();
}

public abstract class Animale : Object, Descrivibile {
    public abstract string verso ();
    public string descrivi () { return "Fa " + verso (); }
}

public class Cane : Animale {
    public override string verso () { return "bau"; }
}', 'vala'),
            P('Una classe `abstract` non si istanzia: serve da base comune.', 'An `abstract` class cannot be instantiated: it serves as a shared base.'),
        ], [
            Q('i1', L('Che parola usa la figlia per ridefinire un metodo?', 'Which word does the child use to redefine a method?'), ['override', 'virtual', 'new', 'redefine'], 0, L('La madre usa `virtual` o `abstract`.', 'The parent uses `virtual` or `abstract`.')),
            Q('i2', L('Quante interfacce può implementare una classe?', 'How many interfaces can a class implement?'), [L('Più di una', 'More than one'), L('Una', 'One'), L('Nessuna', 'None'), L('Due al massimo', 'Two at most')], 0, L('Separate da virgole dopo la classe madre.', 'Comma-separated after the parent class.')),
            Q('i3', L('Si può fare `new` di una classe `abstract`?', 'Can you `new` an `abstract` class?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo se vuota', 'Only if empty'), L('Solo in main', 'Only in main')], 0, L('Serve una figlia concreta.', 'A concrete child is needed.')),
        ], task('vala', L('Crea `public class Gatto : Animale` con `public override string verso () { return "miao"; }`.', 'Create `public class Gatto : Animale` with `public override string verso () { return "miao"; }`.'), '', [re('public\s+class\s+Gatto\s*:\s*Animale', L('Gatto è un Animale', 'Gatto is an Animale')), re('public\s+override\s+string\s+verso\s*\(\s*\)', L('Ridefinisce il verso', 'It redefines the sound')), re('return\s+"miao"\s*;', L('Restituisce “miao”', 'It returns “miao”'))], '', 'public class Gatto : Animale {
    public override string verso () { return "miao"; }
}'), [
            dBlanks(pFill(), "public ___ class Forma : Object {\n    public abstract double area ();\n}\npublic class Quadrato : Forma {\n    public double lato = 2;\n    public ___ double area () { return lato * lato; }\n}", ['abstract', 'override'], ['virtual', 'static', 'new'], L('Base astratta, ridefinizione.', 'Abstract base, redefinition.')),
            dMatch(pMatch(), [['abstract', L('Da ridefinire per forza', 'Must be redefined')], ['virtual', L('Si può ridefinire', 'Can be redefined')], ['override', L('Ridefinisce', 'Redefines')], ['interface', L('Contratto di metodi', 'Contract of methods')]], L('Ereditarietà in Vala.', 'Inheritance in Vala.')),
        ]),
        lesson('collections', L('Collezioni', 'Collections'), [
            P('Oltre agli array, GLib offre `GenericArray` e `HashTable`; la libreria **Gee** aggiunge `ArrayList`, `HashMap` e `HashSet`, comode e tipizzate con i generics.', 'Besides arrays, GLib offers `GenericArray` and `HashTable`; the **Gee** library adds `ArrayList`, `HashMap` and `HashSet`, handy and typed with generics.'),
            C('var nomi = new Gee.ArrayList<string> ();
nomi.add ("Ada");
nomi.add ("Bea");
foreach (var n in nomi) print ("%s\n", n);

var eta = new Gee.HashMap<string, int> ();
eta["Ada"] = 36;
if (eta.has_key ("Ada")) print ("%d\n", eta["Ada"]);', 'vala'),
            P('Per usare Gee si compila con `--pkg gee-0.8`. Le parentesi angolari indicano il tipo degli elementi.', 'To use Gee you compile with `--pkg gee-0.8`. Angle brackets give the type of the items.'),
        ], [
            Q('c1', L('Come aggiungi a una `Gee.ArrayList`?', 'How do you add to a `Gee.ArrayList`?'), ['add', 'push', 'append', 'insert_last'], 0, L('E `size` per contare.', 'And `size` to count.')),
            Q('c2', L('Come controlli se una chiave esiste in una HashMap?', 'How do you check a key exists in a HashMap?'), ['has_key', 'contains', 'exists', 'in'], 0, L('Prima di leggerla.', 'Before reading it.')),
            Q('c3', L('Quale opzione serve per compilare con Gee?', 'Which option is needed to compile with Gee?'), ['--pkg gee-0.8', '--use gee', '-gee', '--lib=gee'], 0, L('`--pkg` aggiunge una libreria.', '`--pkg` adds a library.')),
        ], task('vala', L('Crea `var colori = new Gee.ArrayList<string> ();` e aggiungi `"blu"`.', 'Create `var colori = new Gee.ArrayList<string> ();` and add `"blu"`.'), '', [re('var\s+colori\s*=\s*new\s+Gee\.ArrayList<string>\s*\(\s*\)\s*;', L('La lista esiste', 'The list exists')), re('colori\.add\s*\(\s*"blu"\s*\)\s*;', L('Aggiungi il blu', 'You add blue'))], '', 'var colori = new Gee.ArrayList<string> ();
colori.add ("blu");'), [
            dOrder(pSort(), ['var voti = new Gee.HashMap<string, int> ();', 'voti["Ada"] = 9;', 'voti["Bea"] = 8;', 'foreach (var k in voti.keys) print ("%s: %d\n", k, voti[k]);'], L('Crea, riempi, scorri.', 'Create, fill, walk.')),
            dFlash(pFlash(), [[L('Gee offre ArrayList e HashMap', 'Gee offers ArrayList and HashMap'), true], [L('Le collezioni Gee non hanno tipo', 'Gee collections are untyped'), false], [L('Una HashMap si legge con le parentesi quadre', 'A HashMap is read with square brackets'), true], [L('Un ArrayList di Gee si scorre con `foreach`', 'A Gee ArrayList can be walked with `foreach`'), true]], L('Sono tipizzate con i generics.', 'They are typed with generics.')),
        ]),
        lesson('errors', L('Errori', 'Errors'), [
            P('In Vala gli errori sono dichiarati con `errordomain`. Un metodo che può fallire lo dice con `throws`; chi lo chiama usa `try`/`catch`.', 'In Vala errors are declared with `errordomain`. A method that may fail says so with `throws`; the caller uses `try`/`catch`.'),
            C('errordomain ErroreConto {
    SALDO_INSUFFICIENTE,
    IMPORTO_NON_VALIDO
}

void preleva (double importo) throws ErroreConto {
    if (importo <= 0) throw new ErroreConto.IMPORTO_NON_VALIDO ("Importo %.2f non valido", importo);
}

try {
    preleva (-5);
} catch (ErroreConto e) {
    print ("Errore: %s\n", e.message);
}', 'vala'),
            P('Anche le funzioni di GLib per file e rete lanciano errori: `FileUtils.get_contents` va sempre dentro un `try`.', 'GLib functions for files and networking throw errors too: `FileUtils.get_contents` always goes inside a `try`.'),
        ], [
            Q('e1', L('Come si dichiarano i tipi di errore?', 'How do you declare error types?'), ['errordomain', 'enum error', 'exception', 'class Error'], 0, L('Un elenco di codici.', 'A list of codes.')),
            Q('e2', L('Come segnala un metodo che può fallire?', 'How does a method signal it may fail?'), ['throws', 'fails', 'error', 'try'], 0, L('Nella firma.', 'In the signature.')),
            Q('e3', L('Dove si legge il testo dell’errore?', 'Where do you read the error text?'), ['e.message', 'e.text', 'e.toString()', 'e.why'], 0, L('Dentro il `catch`.', 'Inside the `catch`.')),
        ], task('vala', L('Chiama `leggi ()` dentro `try` e stampa `e.message` nel `catch (Error e)`.', 'Call `leggi ()` inside `try` and print `e.message` in `catch (Error e)`.'), '', [re('try\s*\{\s*leggi\s*\(\s*\)\s*;', L('Provi la chiamata', 'You try the call')), re('catch\s*\(\s*Error\s+e\s*\)\s*\{[\s\S]*e\.message', L('Gestisci l’errore', 'You handle the error'))], '', 'try {
    leggi ();
} catch (Error e) {
    print ("%s\n", e.message);
}'), [
            dBlanks(pFill(), "void carica () ___ IOError {\n    ___ new IOError.NOT_FOUND (\"File assente\");\n}\n___ { carica (); } catch (IOError e) { print (\"%s\\n\", e.message); }", ['throws', 'throw', 'try'], ['fails', 'raise', 'do'], L('Dichiara, lancia, prova.', 'Declare, throw, try.')),
            dMatch(pMatch(), [['errordomain', L('Tipi di errore', 'Error types')], ['throws', L('Può fallire', 'May fail')], ['throw', L('Lancia', 'Throws')], ['catch', L('Gestisce', 'Handles')], ['e.message', L('Testo dell’errore', 'Error text')]], L('Errori in Vala.', 'Errors in Vala.')),
        ]),
        lesson('async', L('Metodi asincroni', 'Async methods'), [
            P('Un metodo `async` può mettersi in pausa con `yield` senza bloccare l’interfaccia, per esempio mentre legge un file o la rete. Si avvia con `.begin` e si raccoglie il risultato con `.end`.', 'An `async` method can pause with `yield` without blocking the interface, for example while reading a file or the network. You start it with `.begin` and collect the result with `.end`.'),
            C('async string leggi_titolo (File file) throws Error {
    uint8[] dati;
    yield file.load_contents_async (null, out dati, null);
    return ((string) dati).split ("\n")[0];
}

leggi_titolo.begin (File.new_for_path ("note.txt"), (obj, res) => {
    try {
        print ("%s\n", leggi_titolo.end (res));
    } catch (Error e) {
        print ("Errore: %s\n", e.message);
    }
});', 'vala'),
            P('Dentro un altro metodo `async` basta scrivere `yield leggi_titolo (file)`.', 'Inside another `async` method you just write `yield leggi_titolo (file)`.'),
        ], [
            Q('a1', L('Con quale parola un metodo async aspetta?', 'With which word does an async method wait?'), ['yield', 'await', 'wait', 'pause'], 0, L('In Vala non c’è `await`.', 'Vala has no `await`.')),
            Q('a2', L('Come si avvia un metodo async fuori da un altro async?', 'How do you start an async method outside another async?'), ['.begin (…)', '.start (…)', '.run (…)', '.go (…)'], 0, L('Il risultato arriva in una callback.', 'The result arrives in a callback.')),
            Q('a3', L('Dove si legge il risultato?', 'Where do you read the result?'), ['.end (res)', '.result', '.value', '.get ()'], 0, L('Nella callback, dentro `try`.', 'In the callback, inside `try`.')),
        ], task('vala', L('Dichiara `async void aggiorna () { yield carica_dati (); }`.', 'Declare `async void aggiorna () { yield carica_dati (); }`.'), '', [re('async\s+void\s+aggiorna\s*\(\s*\)', L('Il metodo è async', 'The method is async')), re('yield\s+carica_dati\s*\(\s*\)\s*;', L('Aspetta i dati', 'It waits for the data'))], '', 'async void aggiorna () {
    yield carica_dati ();
}'), [
            dOrder(pSort(), ['scarica.begin (url, (obj, res) => {', '    try {', '        var testo = scarica.end (res);', '        print ("%s\n", testo);', '    } catch (Error e) {', '        warning (e.message);', '    }', '});'], L('Avvia, poi raccogli nella callback.', 'Start, then collect in the callback.')),
            dFlash(pFlash(), [[L('Un metodo async non blocca l’interfaccia', 'An async method does not block the interface'), true], [L('In Vala si scrive `await`', 'In Vala you write `await`'), false], [L('`.end` può lanciare un errore', '`.end` can throw an error'), true], [L('Dentro un metodo async si aspetta con `yield`', 'Inside an async method you wait with `yield`'), true]], L('Si usa `yield`.', 'You use `yield`.')),
        ]),
        lesson('gtk', L('Una finestra GTK', 'A GTK window'), [
            P('**GTK** disegna le finestre di GNOME. Un’app parte da `Gtk.Application`; al segnale `activate` crea una `Gtk.ApplicationWindow` con dentro i widget, come `Gtk.Box`, `Gtk.Label` e `Gtk.Button`.', '**GTK** draws GNOME’s windows. An app starts from `Gtk.Application`; on the `activate` signal it creates a `Gtk.ApplicationWindow` holding widgets such as `Gtk.Box`, `Gtk.Label` and `Gtk.Button`.'),
            C('int main (string[] args) {
    var app = new Gtk.Application ("it.esempio.Contatore", ApplicationFlags.DEFAULT_FLAGS);
    app.activate.connect (() => {
        var n = 0;
        var etichetta = new Gtk.Label ("0");
        var bottone = new Gtk.Button.with_label ("Più uno");
        bottone.clicked.connect (() => etichetta.label = (++n).to_string ());

        var box = new Gtk.Box (Gtk.Orientation.VERTICAL, 12);
        box.append (etichetta);
        box.append (bottone);

        var finestra = new Gtk.ApplicationWindow (app) { title = "Contatore", child = box };
        finestra.present ();
    });
    return app.run (args);
}', 'vala'),
            P('Si compila con `valac --pkg gtk4 contatore.vala`. Per app complete GNOME usa **Meson** e **libadwaita**.', 'You compile with `valac --pkg gtk4 contatore.vala`. For full apps GNOME uses **Meson** and **libadwaita**.'),
        ], [
            Q('g1', L('Quale segnale crea la finestra all’avvio?', 'Which signal creates the window at start?'), ['activate', 'startup', 'clicked', 'open'], 0, L('Dell’oggetto `Gtk.Application`.', 'Of the `Gtk.Application` object.')),
            Q('g2', L('Come aggiungi un widget a un `Gtk.Box` in GTK 4?', 'How do you add a widget to a `Gtk.Box` in GTK 4?'), ['append', 'add', 'pack_start', 'push'], 0, L('`add` era di GTK 3.', '`add` was GTK 3.')),
            Q('g3', L('Quale pacchetto per compilare con GTK 4?', 'Which package to compile with GTK 4?'), ['--pkg gtk4', '--pkg gtk', '--gtk4', '--pkg gnome'], 0, L('`valac --pkg gtk4 file.vala`.', '`valac --pkg gtk4 file.vala`.')),
        ], task('vala', L('Collega il bottone: `bottone.clicked.connect (() => print ("Clic!\n"));`.', 'Connect the button: `bottone.clicked.connect (() => print ("Clic!\n"));`.'), 'var bottone = new Gtk.Button.with_label ("Prova");
', [re('bottone\.clicked\.connect\s*\(\s*\(\s*\)\s*=>', L('Ascolti il clic', 'You listen to the click'))], '', 'bottone.clicked.connect (() => print ("Clic!\n"));'), [
            dBlanks(pFill(), "var box = new Gtk.___ (Gtk.Orientation.VERTICAL, 6);\nbox.___ (new Gtk.Label (\"Ciao\"));\nfinestra.___ = box;", ['Box', 'append', 'child'], ['Column', 'add', 'content'], L('Contenitore, aggiunta, contenuto della finestra.', 'Container, adding, window content.')),
            dMatch(pMatch(), [['Gtk.Application', L('L’app', 'The app')], ['Gtk.ApplicationWindow', L('La finestra', 'The window')], ['Gtk.Box', L('Contenitore in fila', 'Row/column container')], ['Gtk.Label', L('Un testo', 'A text')], ['present ()', L('Mostra la finestra', 'Shows the window')]], L('I pezzi di un’app GTK.', 'The pieces of a GTK app.')),
        ]),
    ],
];
