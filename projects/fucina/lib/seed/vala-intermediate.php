<?php
/** Vala intermediate: interfaces, collections with GLib and Gee, errors, async, a GTK window, memory management, libadwaita and Meson. */

return [
    'id' => 'vala-intermediate', 'family' => 'vala', 'after' => 'vala-base',
    'title' => L('Vala intermedio', 'Intermediate Vala'), 'short' => L('Vala intermedio', 'Vala Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Interfacce, collezioni, errori, metodi asincroni, una prima finestra GTK, gestione della memoria, app con libadwaita e compilazione con Meson.', 'Interfaces, collections, errors, async methods, a first GTK window, memory management, libadwaita apps and building with Meson.'),
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
            dOrder(pSort(), ['scarica.begin (url, (obj, res) => {', '    try {', '        var testo = scarica.end (res);', '        print ("%s\n", testo);', '    } catch (Error e) {', '        warning ("%s", e.message);', '    }', '});'], L('Avvia, poi raccogli nella callback.', 'Start, then collect in the callback.')),
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
        lesson('memory', L('Memoria: owned, unowned e weak', 'Memory: owned, unowned and weak'), [
            P('Vala non ha garbage collector: gestisce la memoria con il **conteggio dei riferimenti**. Ogni oggetto sa quante variabili lo tengono; quando il conto arriva a zero viene liberato subito. Il compilatore inserisce da solo le chiamate a `ref` e `unref`.', 'Vala has no garbage collector: it manages memory with **reference counting**. Every object knows how many variables hold it; when the count reaches zero it is freed at once. The compiler inserts the `ref` and `unref` calls by itself.'),
            C('public class Nodo : Object {
    public string nome;
    public Nodo? figlio;          // riferimento forte: tiene vivo il figlio
    public weak Nodo? genitore;   // debole: non tiene vivo il genitore

    public Nodo (string nome) { this.nome = nome; }
}

void main () {
    var radice = new Nodo ("radice");
    var foglia = new Nodo ("foglia");
    radice.figlio = foglia;
    foglia.genitore = radice;     // con un riferimento forte sarebbe un ciclo
}

// I valori restituiti sono owned: chi chiama ne diventa proprietario.
string crea_saluto () { return "ciao"; }
// unowned: chi chiama lo usa senza diventarne proprietario.
unowned string nome_di (Nodo n) { return n.nome; }', 'vala'),
            P('Se due oggetti si tengono a vicenda con riferimenti forti, nessuno dei due arriva mai a zero: è un **ciclo** e la memoria si perde. La soluzione è segnare `weak` il riferimento che “torna indietro”, come quello verso il genitore. `unowned` indica un valore che usi senza possederlo; `owned` trasferisce la proprietà.', 'If two objects hold each other with strong references, neither ever reaches zero: it is a **cycle** and the memory leaks. The fix is to mark `weak` the reference that “points back”, such as the one to the parent. `unowned` marks a value you use without owning it; `owned` transfers ownership.'),
        ], [
            Q('me1', L('Come libera la memoria Vala?', 'How does Vala free memory?'), [L('Con un garbage collector che passa ogni tanto', 'With a garbage collector that runs now and then'), L('Con il conteggio dei riferimenti', 'With reference counting'), L('Solo a fine programma', 'Only at the end of the program'), L('Con `free` scritto a mano ovunque', 'With hand-written `free` everywhere')], 1, L('Gli oggetti spariscono appena nessuno li tiene.', 'Objects go as soon as nobody holds them.')),
            Q('me2', L('Che cosa provoca un ciclo di riferimenti forti?', 'What does a cycle of strong references cause?'), [L('Una perdita di memoria: gli oggetti non vengono mai liberati', 'A memory leak: the objects are never freed'), L('Un crash immediato', 'An immediate crash'), L('Un errore di compilazione', 'A compile error'), L('Niente di particolare', 'Nothing special')], 0, L('Il conto non arriva mai a zero.', 'The count never reaches zero.')),
            Q('me3', L('Quale parola si usa sul riferimento che rompe il ciclo?', 'Which keyword goes on the reference that breaks the cycle?'), ['owned', 'static', 'virtual', 'weak'], 3, L('Un riferimento `weak` non aumenta il conto e diventa null quando l’oggetto sparisce.', 'A `weak` reference does not raise the count and becomes null when the object goes.')),
        ], task('vala', L('Rendi debole il riferimento al genitore: `public weak Nodo? genitore;`.', 'Make the reference to the parent weak: `public weak Nodo? genitore;`.'), 'public class Nodo : Object {
    public Nodo? genitore;
}', [re('public\s+weak\s+Nodo\?\s+genitore\s*;', L('Il riferimento è debole', 'The reference is weak'))], '', 'public class Nodo : Object {
    public weak Nodo? genitore;
}'), [
            dMatch(pMatch(), [['ref / unref', L('Aumenta e diminuisce il conto', 'Raises and lowers the count')], ['weak', L('Non tiene vivo l’oggetto', 'Does not keep the object alive')], ['owned', L('Trasferisce la proprietà', 'Transfers ownership')], ['unowned', L('Usa senza possedere', 'Uses without owning')]], L('La memoria in Vala.', 'Memory in Vala.')),
            dFlash(pFlash(), [[L('Vala usa un garbage collector', 'Vala uses a garbage collector'), false], [L('Un oggetto viene liberato quando il suo conto arriva a zero', 'An object is freed when its count reaches zero'), true], [L('Due oggetti che si tengono a vicenda possono non essere mai liberati', 'Two objects holding each other may never be freed'), true], [L('`weak` aumenta il conteggio dei riferimenti', '`weak` raises the reference count'), false]], L('Conteggio dei riferimenti, niente garbage collector.', 'Reference counting, no garbage collector.')),
        ]),
        lesson('adwaita', L('App moderne con libadwaita', 'Modern apps with libadwaita'), [
            P('**libadwaita** estende GTK 4 con i widget e lo stile delle app GNOME: tema chiaro e scuro automatico, barre adattive, pagine di stato, dialoghi. Un’app parte da `Adw.Application` e la finestra è una `Adw.ApplicationWindow`, il cui contenuto si imposta con `content` (non `child`).', '**libadwaita** extends GTK 4 with the widgets and style of GNOME apps: automatic light and dark theme, adaptive bars, status pages, dialogs. An app starts from `Adw.Application` and the window is an `Adw.ApplicationWindow`, whose content is set with `content` (not `child`).'),
            C('public class App : Adw.Application {
    public App () {
        Object (application_id: "it.esempio.Note", flags: ApplicationFlags.DEFAULT_FLAGS);
    }

    protected override void activate () {
        var vista = new Adw.ToolbarView ();
        vista.add_top_bar (new Adw.HeaderBar ());
        vista.content = new Adw.StatusPage () {
            icon_name = "document-edit-symbolic",
            title = "Nessuna nota",
            description = "Premi + per crearne una"
        };

        var finestra = new Adw.ApplicationWindow (this) {
            title = "Note",
            default_width = 480,
            default_height = 600,
            content = vista
        };
        finestra.present ();
    }
}

int main (string[] args) {
    return new App ().run (args);
}', 'vala'),
            P('Si compila con `valac --pkg libadwaita-1 note.vala` (GTK 4 arriva insieme). `Adw.ToolbarView` mette barre in alto e in basso intorno al contenuto; `Adw.StyleManager` segue il tema chiaro o scuro del sistema.', 'You compile with `valac --pkg libadwaita-1 note.vala` (GTK 4 comes along). `Adw.ToolbarView` puts bars at the top and bottom around the content; `Adw.StyleManager` follows the system’s light or dark theme.'),
        ], [
            Q('ad1', L('Con quale proprietà imposti il contenuto di una `Adw.ApplicationWindow`?', 'Which property sets the content of an `Adw.ApplicationWindow`?'), ['child', 'body', 'content', 'view'], 2, L('`child` è quella della `Gtk.ApplicationWindow`.', '`child` is the one of `Gtk.ApplicationWindow`.')),
            Q('ad2', L('Da quale classe parte un’app libadwaita?', 'Which class does a libadwaita app start from?'), ['Adw.Application', 'Gtk.Window', 'GLib.MainLoop', 'Adw.Widget'], 0, L('Prepara anche lo stile Adwaita.', 'It also sets up the Adwaita style.')),
            Q('ad3', L('Quale pacchetto passi a valac per libadwaita?', 'Which package do you pass to valac for libadwaita?'), ['--pkg adwaita', '--pkg gnome-ui', '--pkg gtk5', '--pkg libadwaita-1'], 3, L('Il numero è la versione principale dell’API.', 'The number is the major API version.')),
        ], task('vala', L('Crea la finestra con `new Adw.ApplicationWindow (this)` e imposta `content = vista`.', 'Create the window with `new Adw.ApplicationWindow (this)` and set `content = vista`.'), 'var vista = new Adw.ToolbarView ();
', [re('new\s+Adw\.ApplicationWindow\s*\(\s*this\s*\)', L('Crei la finestra Adwaita', 'You create the Adwaita window')), re('content\s*=\s*vista', L('Imposti il contenuto', 'You set the content'))], '', 'var finestra = new Adw.ApplicationWindow (this) {
    content = vista
};
finestra.present ();'), [
            dMatch(pMatch(), [['Adw.Application', L('L’applicazione', 'The application')], ['Adw.ApplicationWindow', L('La finestra', 'The window')], ['Adw.HeaderBar', L('Barra del titolo', 'Title bar')], ['Adw.ToolbarView', L('Barre intorno al contenuto', 'Bars around the content')], ['Adw.StatusPage', L('Pagina vuota con icona e testo', 'Empty page with icon and text')]], L('I pezzi di un’app GNOME moderna.', 'The pieces of a modern GNOME app.')),
            dBug(pBug(), ['var finestra = new Adw.ApplicationWindow (this);', 'finestra.child = vista;', 'finestra.present ();'], 1, 'finestra.content = vista;', L('In una `Adw.ApplicationWindow` il contenuto va in `content`.', 'In an `Adw.ApplicationWindow` the content goes in `content`.')),
        ]),
        lesson('meson', L('Meson e distribuzione', 'Meson and distribution'), [
            P('Le app GNOME si compilano con **Meson**, che legge il file `meson.build` e prepara la compilazione con Ninja. Meson conosce Vala: basta elencare sorgenti e dipendenze, e penserà lui a chiamare valac e il compilatore C.', 'GNOME apps are built with **Meson**, which reads the `meson.build` file and prepares the build with Ninja. Meson knows Vala: you just list sources and dependencies, and it takes care of calling valac and the C compiler.'),
            C('project(\'note\', \'vala\', \'c\', version: \'1.0.0\')

dipendenze = [
  dependency(\'gtk4\'),
  dependency(\'libadwaita-1\'),
]

executable(\'note\',
  \'src/main.vala\',
  \'src/finestra.vala\',
  dependencies: dipendenze,
  install: true,
)', 'text'),
            C('meson setup build
meson compile -C build
./build/note
meson install -C build', 'bash'),
            P('Per distribuire l’app su qualsiasi distribuzione Linux, GNOME consiglia **Flatpak**, con **Flathub** come negozio. Un manifest descrive il runtime (`org.gnome.Platform`) e i moduli da compilare; GNOME Builder prepara tutto con pochi clic.', 'To ship the app on any Linux distribution, GNOME recommends **Flatpak**, with **Flathub** as the store. A manifest describes the runtime (`org.gnome.Platform`) and the modules to build; GNOME Builder sets it all up in a few clicks.'),
        ], [
            Q('ms1', L('Quale file descrive un progetto Meson?', 'Which file describes a Meson project?'), ['Makefile', 'meson.build', 'CMakeLists.txt', 'package.json'], 1, L('Uno per cartella, se serve.', 'One per folder, if needed.')),
            Q('ms2', L('Quale comando prepara la cartella di compilazione?', 'Which command prepares the build folder?'), ['meson setup build', 'meson init', 'ninja start', 'valac --meson'], 0, L('Poi `meson compile -C build`.', 'Then `meson compile -C build`.')),
            Q('ms3', L('Come si distribuiscono di solito le app GNOME?', 'How are GNOME apps usually distributed?'), [L('Solo come sorgenti da compilare', 'Only as source code to build'), L('Con un file .exe', 'As an .exe file'), L('Dall’App Store di Apple', 'From Apple’s App Store'), L('Come Flatpak, spesso su Flathub', 'As Flatpaks, often on Flathub')], 3, L('Un solo pacchetto per tutte le distribuzioni.', 'One package for every distribution.')),
        ], task('text', L('Nel `meson.build` aggiungi `dependency(\'libadwaita-1\')` all’elenco delle dipendenze.', 'In `meson.build` add `dependency(\'libadwaita-1\')` to the list of dependencies.'), 'dipendenze = [
  dependency(\'gtk4\'),
]', [re('dependency\s*\(\s*["\']libadwaita-1["\']\s*\)', L('libadwaita è tra le dipendenze', 'libadwaita is among the dependencies'))], '', 'dipendenze = [
  dependency(\'gtk4\'),
  dependency(\'libadwaita-1\'),
]'), [
            dOrder(pSort(), ['meson setup build', 'meson compile -C build', 'meson install -C build'], L('Prepara, compila, installa.', 'Set up, build, install.')),
            dMatch(pMatch(), [['meson.build', L('Descrive il progetto', 'Describes the project')], ['dependency()', L('Una libreria richiesta', 'A required library')], ['executable()', L('Il programma da creare', 'The program to build')], ['Flatpak', L('Pacchetto per ogni distribuzione', 'Package for every distribution')], ['Flathub', L('Negozio di app Flatpak', 'Flatpak app store')]], L('Dal sorgente agli utenti.', 'From source to users.')),
        ]),
    ],
];
