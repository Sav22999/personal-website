<?php
/** Vala intermediate: interfaces, collections with GLib and Gee, errors, async, a GTK window, memory management, libadwaita and Meson. */

/** The class diagram of the example: Cane inherits from the abstract Animale, which inherits from Object and implements Descrivibile. */
if (!function_exists('fig_valaint_classes')) {
    function fig_valaint_classes(string $inherits, string $implements): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 255" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="60" y="14" width="170" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="145" y="39" text-anchor="middle" font-weight="700">Object</text>
<rect x="40" y="96" width="210" height="72" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="145" y="118" text-anchor="middle" font-size="14" font-weight="700">abstract class Animale</text>
<text x="145" y="140" text-anchor="middle" font-size="13" fill="#5A35C9">abstract verso ()</text><text x="145" y="158" text-anchor="middle" font-size="13" fill="#5A35C9">descrivi ()</text>
<rect x="40" y="198" width="210" height="50" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="145" y="219" text-anchor="middle" font-size="14" font-weight="700">class Cane</text><text x="145" y="238" text-anchor="middle" font-size="13" fill="#0B7F5C">override verso ()</text>
<rect x="390" y="96" width="220" height="72" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2" stroke-dasharray="6 4"/>
<text x="500" y="122" text-anchor="middle" font-size="14" font-weight="700">interface Descrivibile</text>
<text x="500" y="146" text-anchor="middle" font-size="13" fill="#1B66BC">abstract descrivi ()</text>
<g stroke="#1E1C52" stroke-width="2" fill="none">
<path d="M145 96 V66"/><path d="M137 66 L145 54 L153 66 Z" fill="#FFFFFF"/>
<path d="M145 198 V180"/><path d="M137 180 L145 168 L153 180 Z" fill="#FFFFFF"/>
<path d="M250 132 H378" stroke-dasharray="6 4"/><path d="M378 124 L390 132 L378 140 Z" fill="#FFFFFF"/>
</g>
<text x="157" y="82" font-size="13" fill="#5F5C8A">{$inherits}</text><text x="157" y="190" font-size="13" fill="#5F5C8A">{$inherits}</text>
<text x="318" y="122" text-anchor="middle" font-size="13" fill="#1B66BC">{$implements}</text>
</svg>
SVG;
    }
}

/** The GLib main loop keeps turning, handling clicks and redraws, while an async method waits; when the file is ready the loop runs the callback. */
if (!function_exists('fig_valaint_mainloop')) {
    function fig_valaint_mainloop(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.spin{transform-origin:170px 125px;animation:spin 6s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}@media (prefers-reduced-motion:reduce){.spin{animation:none}}</style>
<circle cx="170" cy="125" r="85" fill="none" stroke="#2F8CF0" stroke-width="2" stroke-dasharray="8 6"/>
<circle class="spin" cx="170" cy="40" r="7" fill="#2F8CF0"/>
<text x="170" y="122" text-anchor="middle" font-weight="700">main loop</text><text x="170" y="141" text-anchor="middle" font-size="13" fill="#5F5C8A">GLib</text>
<g font-size="13" text-anchor="middle">
<rect x="125" y="26" width="90" height="28" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="170" y="45">{$t[0]}</text>
<rect x="38" y="111" width="94" height="28" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="85" y="130">{$t[1]}</text>
<rect x="115" y="196" width="110" height="28" rx="14" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="170" y="215" fill="#B84508">{$t[2]}</text>
</g>
<rect x="360" y="20" width="264" height="50" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="492" y="50" text-anchor="middle" font-size="14">leggi_titolo.begin (…)</text>
<rect x="360" y="96" width="264" height="56" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="492" y="119" text-anchor="middle" font-size="14" font-weight="700">yield</text><text x="492" y="139" text-anchor="middle" font-size="13" fill="#5F5C8A">{$t[3]}</text>
<rect x="360" y="178" width="264" height="50" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="492" y="208" text-anchor="middle" font-size="14">leggi_titolo.end (res)</text>
<g stroke-width="2" fill="none">
<path d="M492 70 V90" stroke="#7048E8"/><path d="M485 82 L492 94 L499 82" stroke="#7048E8"/>
<path d="M225 210 C290 210 310 203 354 203" stroke="#E8590C" stroke-dasharray="5 4"/><path d="M346 196 L356 203 L346 210" stroke="#E8590C"/>
</g>
</svg>
SVG;
    }
}

/** The GTK widget tree of the counter next to the window it draws. */
if (!function_exists('fig_valaint_gtk')) {
    function fig_valaint_gtk(string $title, string $button): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M150 56 V86"/><path d="M150 126 V146 H80 V170 M150 146 H215 V170"/><path d="M300 120 H334"/><path d="M326 113 L336 120 L326 127"/></g>
<rect x="40" y="16" width="220" height="40" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="150" y="41" text-anchor="middle" font-size="14" font-weight="700">Gtk.ApplicationWindow</text>
<rect x="40" y="86" width="220" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="150" y="111" text-anchor="middle" font-size="14" font-weight="700">Gtk.Box · VERTICAL</text>
<rect x="20" y="170" width="120" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="80" y="195" text-anchor="middle" font-size="14">Gtk.Label</text>
<rect x="150" y="170" width="130" height="40" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="215" y="195" text-anchor="middle" font-size="14">Gtk.Button</text>
<rect x="360" y="16" width="260" height="208" rx="14" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<path d="M360 56 V30 Q360 16 374 16 H606 Q620 16 620 30 V56 Z" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="490" y="42" text-anchor="middle" font-size="14" font-weight="700">{$title}</text>
<rect x="450" y="88" width="80" height="40" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="5 4"/>
<text x="490" y="114" text-anchor="middle" font-size="17" font-weight="700">0</text>
<rect x="420" y="144" width="140" height="40" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="490" y="169" text-anchor="middle" font-size="14">{$button}</text>
</svg>
SVG;
    }
}

/** Reference counts of two nodes: the strong link to the child adds one, the weak link to the parent adds nothing. */
if (!function_exists('fig_valaint_refs')) {
    function fig_valaint_refs(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 200" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="40" y="50" width="220" height="74" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="150" y="80" text-anchor="middle" font-weight="700">radice</text><text x="150" y="104" text-anchor="middle" font-size="13" fill="#5A35C9">{$t[0]} 1</text>
<rect x="380" y="50" width="220" height="74" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="490" y="80" text-anchor="middle" font-weight="700">foglia</text><text x="490" y="104" text-anchor="middle" font-size="13" fill="#0B7F5C">{$t[0]} 2</text>
<g stroke-width="2" fill="none">
<path d="M260 72 H372" stroke="#E8590C"/><path d="M364 65 L374 72 L364 79" stroke="#E8590C"/>
<path d="M380 104 H268" stroke="#8A87B0" stroke-dasharray="6 4"/><path d="M276 97 L266 104 L276 111" stroke="#8A87B0"/>
</g>
<text x="320" y="60" text-anchor="middle" font-size="13" font-weight="700" fill="#B84508">{$t[1]}</text>
<text x="320" y="128" text-anchor="middle" font-size="13" fill="#5F5C8A">{$t[2]}</text>
<text x="320" y="176" text-anchor="middle" font-size="14" fill="#5F5C8A">{$t[3]}</text>
</svg>
SVG;
    }
}

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
            Fig(L(fig_valaint_classes('eredita', 'implementa'), fig_valaint_classes('inherits', 'implements')), L('Uno schema delle classi: Cane eredita da abstract class Animale, che eredita da Object e, con una freccia tratteggiata, implementa interface Descrivibile. Animale dichiara abstract verso () e descrivi (), Cane fa override di verso ().', 'A class diagram: Cane inherits from abstract class Animale, which inherits from Object and, with a dashed arrow, implements interface Descrivibile. Animale declares abstract verso () and descrivi (), Cane overrides verso ().'), L('Una sola classe madre, ma tutte le interfacce che servono.', 'A single parent class, but as many interfaces as you need.')),
            P('La differenza tra `virtual` e `abstract`: un metodo `virtual` ha già un corpo, che la figlia **può** sostituire; un metodo `abstract` non ha corpo, e ogni figlia concreta **deve** scriverlo. Così `descrivi` funziona per tutti gli animali, mentre `verso` cambia per ognuno. Il `: Object` dopo il nome dell’interfaccia dice che solo i GObject possono implementarla. Un `Cane` si può passare ovunque si aspetti un `Animale`, un `Descrivibile` o un `Object`.', 'The difference between `virtual` and `abstract`: a `virtual` method already has a body, which the child **may** replace; an `abstract` method has no body, and every concrete child **must** write it. That way `descrivi` works for every animal, while `verso` changes for each one. The `: Object` after the interface name says that only GObjects can implement it. A `Cane` can be passed anywhere an `Animale`, a `Descrivibile` or an `Object` is expected.'),
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
if (eta.has_key ("Ada")) print ("%d\n", eta["Ada"]);', 'vala', '<pre>Ada
Bea
36</pre>'),
            P('Per usare Gee si compila con `--pkg gee-0.8`. Le parentesi angolari indicano il tipo degli elementi.', 'To use Gee you compile with `--pkg gee-0.8`. Angle brackets give the type of the items.'),
            P('Come scegliere: un array `string[]` va bene quando conosci gli elementi in anticipo; `ArrayList` quando la lista cresce e cala mentre il programma gira; `HashMap` quando cerchi per chiave, perché trova `eta["Ada"]` subito invece di scorrere tutto; `HashSet` quando ti interessa solo sapere se un elemento c’è, senza doppioni. Leggere una chiave che manca non dà errore ma un valore vuoto, quindi controlla prima con `has_key`, come nel codice.', 'How to choose: a `string[]` array is fine when you know the items in advance; `ArrayList` when the list grows and shrinks while the program runs; `HashMap` when you look things up by key, because it finds `eta["Ada"]` straight away instead of walking everything; `HashSet` when you only care whether an item is there, with no duplicates. Reading a missing key gives no error but an empty value, so check first with `has_key`, as in the code.'),
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
            C(L('errordomain ErroreConto {
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
}', 'errordomain ErroreConto {
    SALDO_INSUFFICIENTE,
    IMPORTO_NON_VALIDO
}

void preleva (double importo) throws ErroreConto {
    if (importo <= 0) throw new ErroreConto.IMPORTO_NON_VALIDO ("Amount %.2f is not valid", importo);
}

try {
    preleva (-5);
} catch (ErroreConto e) {
    print ("Error: %s\n", e.message);
}'), 'vala', L('<pre>Errore: Importo -5.00 non valido</pre>', '<pre>Error: Amount -5.00 is not valid</pre>')),
            P('Anche le funzioni di GLib per file e rete lanciano errori: `FileUtils.get_contents` va sempre dentro un `try`.', 'GLib functions for files and networking throw errors too: `FileUtils.get_contents` always goes inside a `try`.'),
            P('Un `errordomain` raggruppa gli errori di una stessa area, e ogni codice (`SALDO_INSUFFICIENTE`) è un caso preciso: chi chiama può catturarli tutti con `catch (ErroreConto e)` o uno solo con `catch (ErroreConto.SALDO_INSUFFICIENTE e)`. Se non vuoi gestire l’errore lì, dichiara `throws` anche nel metodo che chiama e l’errore sale al livello sopra. Il compilatore avvisa quando un errore possibile non viene né catturato né dichiarato.', 'An `errordomain` groups the errors of one area, and each code (`SALDO_INSUFFICIENTE`) is a precise case: the caller can catch them all with `catch (ErroreConto e)` or just one with `catch (ErroreConto.SALDO_INSUFFICIENTE e)`. If you do not want to handle the error there, declare `throws` on the calling method too and the error goes up a level. The compiler warns when a possible error is neither caught nor declared.'),
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
            Fig(L(fig_valaint_mainloop(['clic', 'ridisegno', 'file pronto', 'in pausa: l’interfaccia risponde']), fig_valaint_mainloop(['click', 'redraw', 'file ready', 'paused: the interface responds'])), L('A sinistra il main loop di GLib, un cerchio con un punto che gira, passa tra gli eventi clic, ridisegno e file pronto. A destra leggi_titolo.begin porta a yield, in pausa mentre l’interfaccia risponde; quando il file è pronto, una freccia dal main loop porta a leggi_titolo.end.', 'On the left the GLib main loop, a circle with a turning dot, passes through the events click, redraw and file ready. On the right leggi_titolo.begin leads to yield, paused while the interface responds; when the file is ready, an arrow from the main loop leads to leggi_titolo.end.'), L('Il metodo asincrono si ferma, il loop no: continua a servire gli altri eventi.', 'The async method stops, the loop does not: it keeps serving the other events.')),
            P('Tutto si regge sul **main loop** di GLib, il ciclo che in un’app GTK raccoglie clic, tasti e ridisegni: `app.run ()` lo avvia. Quando il metodo arriva a `yield` restituisce il controllo al loop, e quando il file è letto il loop richiama la lambda passata a `.begin`, dove `.end` consegna il risultato o lancia l’errore. Senza un loop in funzione la callback non arriverebbe mai: in un programma da terminale si crea con `new MainLoop ()`.', 'Everything rests on the GLib **main loop**, the cycle that in a GTK app gathers clicks, keys and redraws: `app.run ()` starts it. When the method reaches `yield` it hands control back to the loop, and once the file is read the loop calls the lambda passed to `.begin`, where `.end` delivers the result or throws the error. With no loop running the callback would never arrive: in a terminal program you create one with `new MainLoop ()`.'),
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
            C(L('int main (string[] args) {
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
}', 'int main (string[] args) {
    var app = new Gtk.Application ("it.esempio.Contatore", ApplicationFlags.DEFAULT_FLAGS);
    app.activate.connect (() => {
        var n = 0;
        var etichetta = new Gtk.Label ("0");
        var bottone = new Gtk.Button.with_label ("Plus one");
        bottone.clicked.connect (() => etichetta.label = (++n).to_string ());

        var box = new Gtk.Box (Gtk.Orientation.VERTICAL, 12);
        box.append (etichetta);
        box.append (bottone);

        var finestra = new Gtk.ApplicationWindow (app) { title = "Counter", child = box };
        finestra.present ();
    });
    return app.run (args);
}'), 'vala'),
            P('Si compila con `valac --pkg gtk4 contatore.vala`. Per app complete GNOME usa **Meson** e **libadwaita**.', 'You compile with `valac --pkg gtk4 contatore.vala`. For full apps GNOME uses **Meson** and **libadwaita**.'),
            Fig(L(fig_valaint_gtk('Contatore', 'Più uno'), fig_valaint_gtk('Counter', 'Plus one')), L('A sinistra l’albero: Gtk.ApplicationWindow contiene un Gtk.Box verticale con due figli, Gtk.Label e Gtk.Button. A destra la finestra disegnata: barra del titolo, l’etichetta 0 e sotto il bottone.', 'On the left the tree: Gtk.ApplicationWindow holds a vertical Gtk.Box with two children, Gtk.Label and Gtk.Button. On the right the drawn window: title bar, the label 0 and the button below it.'), L('La finestra ha un solo figlio, il Box, che dispone gli altri in colonna.', 'The window has a single child, the Box, which lays out the others in a column.')),
            P('Una finestra GTK 4 ha un solo `child`: per mettere più widget si usa un contenitore come `Gtk.Box`, che li dispone in colonna o in riga nell’ordine in cui li aggiungi con `append`, con 12 pixel di spazio tra uno e l’altro. L’interattività passa dai segnali: `clicked.connect` collega il bottone a una lambda che aggiorna l’etichetta. Le variabili `n` ed `etichetta` restano vive anche dopo `activate`, perché la lambda le cattura.', 'A GTK 4 window has a single `child`: to hold several widgets you use a container such as `Gtk.Box`, which lays them out in a column or a row in the order you add them with `append`, with 12 pixels of space between them. Interactivity goes through signals: `clicked.connect` links the button to a lambda that updates the label. The `n` and `etichetta` variables stay alive even after `activate`, because the lambda captures them.'),
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
            Fig(L(fig_valaint_refs(['conteggio:', 'figlio: forte (+1)', 'genitore: weak (+0)', 'con due riferimenti forti nessuno scenderebbe mai a 0']), fig_valaint_refs(['count:', 'figlio: strong (+1)', 'genitore: weak (+0)', 'with two strong references neither would ever drop to 0'])), L('Due oggetti: radice, con conteggio 1, e foglia, con conteggio 2. Una freccia piena da radice a foglia è figlio, forte, e aggiunge uno. Una freccia tratteggiata da foglia a radice è genitore, weak, e non aggiunge nulla.', 'Two objects: radice, with count 1, and foglia, with count 2. A solid arrow from radice to foglia is figlio, strong, and adds one. A dashed arrow from foglia to radice is genitore, weak, and adds nothing.'), L('Quando la variabile radice sparisce, il suo conteggio va a 0 e la catena si libera da sola.', 'When the radice variable goes away, its count drops to 0 and the chain frees itself.')),
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
            C(L('public class App : Adw.Application {
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
}', 'public class App : Adw.Application {
    public App () {
        Object (application_id: "it.esempio.Note", flags: ApplicationFlags.DEFAULT_FLAGS);
    }

    protected override void activate () {
        var vista = new Adw.ToolbarView ();
        vista.add_top_bar (new Adw.HeaderBar ());
        vista.content = new Adw.StatusPage () {
            icon_name = "document-edit-symbolic",
            title = "No notes",
            description = "Press + to create one"
        };

        var finestra = new Adw.ApplicationWindow (this) {
            title = "Notes",
            default_width = 480,
            default_height = 600,
            content = vista
        };
        finestra.present ();
    }
}

int main (string[] args) {
    return new App ().run (args);
}'), 'vala', L('<style>body{margin:0;font-family:Cantarell,system-ui,sans-serif;color:#2e3436;background:#fafafa}.h{text-align:center;font-weight:700;padding:12px;border-bottom:1px solid #e0e0e0;background:#ebebeb}.s{text-align:center;padding:28px 16px}.s h2{margin:12px 0 6px;font-size:22px}.s p{margin:0;color:#6b6b6b}</style><div class="h">Note</div><div class="s"><svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#9a9996" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M13 7l4 4"/></svg><h2>Nessuna nota</h2><p>Premi + per crearne una</p></div>', '<style>body{margin:0;font-family:Cantarell,system-ui,sans-serif;color:#2e3436;background:#fafafa}.h{text-align:center;font-weight:700;padding:12px;border-bottom:1px solid #e0e0e0;background:#ebebeb}.s{text-align:center;padding:28px 16px}.s h2{margin:12px 0 6px;font-size:22px}.s p{margin:0;color:#6b6b6b}</style><div class="h">Notes</div><div class="s"><svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#9a9996" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M13 7l4 4"/></svg><h2>No notes</h2><p>Press + to create one</p></div>')),
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
