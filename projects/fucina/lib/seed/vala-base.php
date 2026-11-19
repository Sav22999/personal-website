<?php
/** Vala basics: the first program, types, control flow, arrays and methods, classes and GObject, strings, enums and structs, delegates. */

/** From Vala source to a native program: valac writes C, the C compiler turns it into an executable. */
if (!function_exists('fig_valabase_pipeline')) {
    function fig_valabase_pipeline(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 200" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="20" y="40" width="140" height="56" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="90" y="74" text-anchor="middle" font-weight="700">ciao.vala</text>
<rect x="250" y="40" width="140" height="56" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="320" y="74" text-anchor="middle" font-weight="700">ciao.c</text>
<rect x="480" y="40" width="140" height="56" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="550" y="74" text-anchor="middle" font-weight="700">./ciao</text>
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M160 68 H244"/><path d="M236 61 L246 68 L236 75"/><path d="M390 68 H474"/><path d="M466 61 L476 68 L466 75"/></g>
<g text-anchor="middle" font-size="14" font-weight="700"><text x="203" y="58" fill="#5A35C9">valac</text><text x="433" y="58" fill="#1B66BC">gcc</text></g>
<g text-anchor="middle" font-size="13" fill="#5F5C8A"><text x="90" y="118">{$t[0]}</text><text x="320" y="118">{$t[1]}</text><text x="550" y="118">{$t[2]}</text></g>
<path d="M20 140 V148 H620 V140" fill="none" stroke="#7048E8" stroke-width="2"/>
<text x="320" y="176" text-anchor="middle" font-size="14" fill="#5A35C9">{$t[3]}</text>
</svg>
SVG;
    }
}

/** A signal: versa() emits cambiato, and every handler connected with connect() is called. */
if (!function_exists('fig_valabase_signal')) {
    function fig_valabase_signal(string $others): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="20" y="75" width="170" height="70" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="105" y="104" text-anchor="middle" font-weight="700">Conto</text><text x="105" y="126" text-anchor="middle" font-size="13" fill="#5A35C9">versa (50)</text>
<rect x="236" y="90" width="160" height="40" rx="20" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="316" y="115" text-anchor="middle" font-weight="700" fill="#B84508">cambiato (50.0)</text>
<g stroke-width="2" fill="none">
<path d="M190 110 H230" stroke="#E8590C"/><path d="M222 103 L232 110 L222 117" stroke="#E8590C"/>
<path d="M396 104 C420 104 420 58 444 56" stroke="#0B7F5C"/><path d="M434 50 L446 56 L435 63" stroke="#0B7F5C"/>
<path d="M396 116 C420 116 420 164 444 166" stroke="#8A87B0"/><path d="M435 159 L446 166 L434 172" stroke="#8A87B0"/>
</g>
<text x="420" y="34" text-anchor="end" font-size="13" fill="#0B7F5C">connect</text>
<rect x="450" y="30" width="176" height="52" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="538" y="61" text-anchor="middle" font-size="14">(s) =&gt; print (…)</text>
<rect x="450" y="140" width="176" height="52" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/><text x="538" y="171" text-anchor="middle" font-size="14" fill="#5F5C8A">{$others}</text>
</svg>
SVG;
    }
}

/** UTF-8: the four characters of "però" take five bytes, because ò needs two. */
if (!function_exists('fig_valabase_utf8')) {
    function fig_valabase_utf8(string $chars, string $bytes): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 190" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="20" y="62" font-weight="700">char_count () = 4</text><text x="20" y="80" font-size="13" fill="#5F5C8A">{$chars}</text>
<text x="20" y="142" font-weight="700">length = 5</text><text x="20" y="160" font-size="13" fill="#5F5C8A">{$bytes}</text>
<g fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"><rect x="190" y="40" width="60" height="50" rx="8"/><rect x="256" y="40" width="60" height="50" rx="8"/><rect x="322" y="40" width="60" height="50" rx="8"/></g>
<rect x="388" y="40" width="126" height="50" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<g text-anchor="middle" font-size="17" font-weight="700"><text x="220" y="71">p</text><text x="286" y="71">e</text><text x="352" y="71">r</text><text x="451" y="71" fill="#B84508">ò</text></g>
<g fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"><rect x="190" y="120" width="60" height="50" rx="8"/><rect x="256" y="120" width="60" height="50" rx="8"/><rect x="322" y="120" width="60" height="50" rx="8"/></g>
<g fill="#FFF3EA" stroke="#E8590C" stroke-width="2"><rect x="388" y="120" width="60" height="50" rx="8"/><rect x="454" y="120" width="60" height="50" rx="8"/></g>
<g text-anchor="middle" font-size="14" font-family="ui-monospace, monospace"><text x="220" y="150">70</text><text x="286" y="150">65</text><text x="352" y="150">72</text><text x="418" y="150">C3</text><text x="484" y="150">B2</text></g>
<g stroke="#8A87B0" stroke-width="2"><path d="M220 90 V120"/><path d="M286 90 V120"/><path d="M352 90 V120"/></g>
<g stroke="#E8590C" stroke-width="2"><path d="M440 90 L418 120"/><path d="M462 90 L484 120"/></g>
</svg>
SVG;
    }
}

return [
    'id' => 'vala-base', 'family' => 'vala', 'after' => null,
    'title' => L('Vala base', 'Vala Basics'), 'short' => L('Vala base', 'Vala Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio delle app GNOME: sintassi simile a C#, compilato in C. Tipi, cicli, metodi, classi, stringhe, enum e struct, delegate.', 'The language of GNOME apps: C#-like syntax, compiled to C. Types, loops, methods, classes, strings, enums and structs, delegates.'),
    'lessons' => [
        lesson('hello', L('Il primo programma', 'The first program'), [
            P('**Vala** ha una sintassi simile a C# ma viene tradotto in C e compilato: programmi veloci e senza macchina virtuale. Il punto di partenza è `main`; `print` scrive (senza andare a capo da solo).', '**Vala** has a C#-like syntax but is translated to C and compiled: fast programs with no virtual machine. The starting point is `main`; `print` writes (without adding a newline by itself).'),
            C(L('void main () {
    string nome = "Ada";
    print ("Ciao, %s!\n", nome);
    stdout.printf ("Due più due fa %d\n", 2 + 2);
}

// terminale: valac ciao.vala && ./ciao', 'void main () {
    string nome = "Ada";
    print ("Hi, %s!\n", nome);
    stdout.printf ("Two plus two is %d\n", 2 + 2);
}

// terminal: valac ciao.vala && ./ciao'), 'vala', L('<pre>Ciao, Ada!
Due più due fa 4</pre>', '<pre>Hi, Ada!
Two plus two is 4</pre>')),
            P('`%s` inserisce un testo, `%d` un intero. Lo stile GNOME mette uno spazio prima delle parentesi delle chiamate.', '`%s` inserts text, `%d` an integer. GNOME style puts a space before the brackets of calls.'),
            Fig(L(fig_valabase_pipeline(['il tuo codice', 'C generato', 'eseguibile nativo', 'valac esegue entrambi i passi con un solo comando']), fig_valabase_pipeline(['your code', 'generated C', 'native executable', 'valac runs both steps with a single command'])), L('Tre riquadri in fila: ciao.vala, il tuo codice; una freccia valac porta a ciao.c, il C generato; una freccia gcc porta a ./ciao, l’eseguibile nativo. Una parentesi sotto indica che valac fa entrambi i passi.', 'Three boxes in a row: ciao.vala, your code; a valac arrow leads to ciao.c, the generated C; a gcc arrow leads to ./ciao, the native executable. A bracket below shows that valac does both steps.'), L('Il C intermedio di solito non lo vedi nemmeno: valac lo passa da solo al compilatore C.', 'You usually never even see the intermediate C: valac hands it to the C compiler by itself.')),
            P('Perché passare dal C? Perché così Vala ottiene la velocità del C e può usare direttamente le librerie scritte in C, come GLib, GTK e migliaia di altre: per ognuna basta un file di descrizione `.vapi`, e la chiami come se fosse codice Vala. Il prezzo è che gli errori del compilatore C, rari, a volte parlano di righe che tu non hai scritto. Nei progetti veri non chiami `valac` a mano ma usi Meson, che vedrai nel corso intermedio.', 'Why go through C? Because that way Vala gets C’s speed and can use libraries written in C directly, such as GLib, GTK and thousands of others: each just needs a `.vapi` description file, and you call it as if it were Vala code. The price is that C compiler errors, which are rare, sometimes talk about lines you did not write. In real projects you do not call `valac` by hand but use Meson, which you will meet in the intermediate course.'),
        ], [
            Q('h1', L('In che cosa viene tradotto Vala?', 'What is Vala translated into?'), ['C', 'JavaScript', L('Bytecode Java', 'Java bytecode'), 'Python'], 0, L('Poi lo compila un compilatore C.', 'Then a C compiler builds it.')),
            Q('h2', L('Che cosa inserisce `%d`?', 'What does `%d` insert?'), [L('Un intero', 'An integer'), L('Un testo', 'A text'), L('Una data', 'A date'), L('Un decimale', 'A decimal')], 0, L('`%s` per i testi, `%f` per i decimali.', '`%s` for text, `%f` for decimals.')),
            Q('h3', L('Con quale comando si compila?', 'Which command compiles?'), ['valac', 'vala run', 'gcc vala', 'vc'], 0, L('`valac file.vala`.', '`valac file.vala`.')),
        ], task('vala', L('Nel `main` stampa `"Benvenuto\n"` con `print`.', 'In `main` print `"Benvenuto\n"` with `print`.'), 'void main () {
}', [re('print\s*\(\s*"Benvenuto\\\\n"\s*\)\s*;', L('Stampi il saluto', 'You print the greeting'))], '', 'void main () {
    print ("Benvenuto\n");
}'), [
            dOrder(pSort(), ['void main () {', '    int anni = 36;', '    print ("Anni: %d\n", anni);', '}'], L('Funzione, variabile, stampa, chiusura.', 'Function, variable, print, close.')),
            dMatch(pMatch(), [['%s', L('Testo', 'Text')], ['%d', L('Intero', 'Integer')], ['%f', L('Decimale', 'Decimal')], ['\n', L('A capo', 'Newline')]], L('I segnaposto di stampa.', 'Print placeholders.')),
        ]),
        lesson('types', L('Tipi e variabili', 'Types and variables'), [
            P('I tipi principali sono `int`, `double`, `bool`, `char` e `string`. `var` deduce il tipo. `const` crea una costante. Le stringhe con il segno `@` davanti accettano valori con `$nome`.', 'The main types are `int`, `double`, `bool`, `char` and `string`. `var` infers the type. `const` creates a constant. Strings prefixed with `@` accept values with `$nome`.'),
            C('int pezzi = 3;
double prezzo = 9.5;
bool pronto = true;
var citta = "Udine";          // string
const int MAX = 10;

string riepilogo = @"$pezzi pezzi a $prezzo euro";
string? soprannome = null;    // può essere null', 'vala'),
            P('Come in altri linguaggi moderni, un tipo con `?` può valere `null`; senza punto di domanda no.', 'As in other modern languages, a type with `?` can be `null`; without the question mark it cannot.'),
            P('Vala è a **tipi statici**: una variabile `int` resterà un intero, e `pezzi = "tre"` è un errore di compilazione. `var` non cambia questa regola, evita solo di ripetere il tipo quando è ovvio. Sul `null` il controllo è meno rigido di quanto sembri: il compilatore verifica a fondo solo con l’opzione `--enable-experimental-non-null`, ma il `?` documenta comunque l’intenzione, e sui parametri Vala aggiunge controlli che segnalano un `null` inatteso mentre il programma gira.', 'Vala is **statically typed**: an `int` variable will stay an integer, and `pezzi = "three"` is a compile error. `var` does not change this rule, it only saves repeating the type when it is obvious. On `null` the check is less strict than it looks: the compiler verifies it thoroughly only with the `--enable-experimental-non-null` option, but the `?` still documents the intent, and on parameters Vala adds checks that report an unexpected `null` while the program runs.'),
        ], [
            Q('t1', L('Quale tipo per un numero con decimali?', 'Which type for a number with decimals?'), ['double', 'int', 'char', 'bool'], 0, L('Anche `float`, meno preciso.', 'Also `float`, less precise.')),
            Q('t2', L('A che cosa serve `@` davanti a una stringa?', 'What is `@` in front of a string for?'), [L('Inserire valori con $', 'Inserting values with $'), L('Commentare', 'Commenting'), L('Rendere la stringa costante', 'Making the string constant'), L('Niente', 'Nothing')], 0, L('Template di stringa.', 'String templates.')),
            Q('t3', L('Che cosa significa `string?`', 'What does `string?` mean?'), [L('Una stringa che può essere null', 'A string that may be null'), L('Una domanda', 'A question'), L('Un errore', 'An error'), L('Una stringa vuota', 'An empty string')], 0, L('Tipo nullable.', 'Nullable type.')),
        ], task('vala', L('Dichiara `int livelli = 6;` e `string corso = "Vala";`.', 'Declare `int livelli = 6;` and `string corso = "Vala";`.'), '', [re('int\s+livelli\s*=\s*6\s*;', L('C’è l’intero', 'There is the int')), re('string\s+corso\s*=\s*"Vala"\s*;', L('C’è la stringa', 'There is the string'))], '', 'int livelli = 6;
string corso = "Vala";'), [
            dBlanks(pFill(), "___ nome = \"Ada\";\n___ int MASSIMO = 5;\nstring frase = ___\"Ciao \$nome\";", ['var', 'const', '@'], ['let', 'final', '$'], L('Deduzione, costante, template.', 'Inference, constant, template.')),
            dFlash(pFlash(), [[L('`var` deduce il tipo', '`var` infers the type'), true], [L('Ogni tipo in Vala può valere null', 'Every type in Vala can be null'), false], [L('`const` non si può cambiare', '`const` cannot change'), true], [L('`string?` può valere null', '`string?` can be null'), true]], L('Serve il `?` per ammettere null.', 'You need `?` to allow null.')),
        ]),
        lesson('control', L('Condizioni e cicli', 'Conditions and loops'), [
            P('`if`/`else`, `switch`, `while`, `for` funzionano come in C#. `foreach` scorre gli elementi di un array o di una lista.', '`if`/`else`, `switch`, `while`, `for` work as in C#. `foreach` walks the items of an array or a list.'),
            C('for (int i = 1; i <= 3; i++) {
    print ("Giro %d\n", i);
}

string[] frutta = { "mela", "pera", "kiwi" };
foreach (string f in frutta) {
    print ("%s\n", f);
}

switch (voto) {
    case 10:
        print ("Perfetto\n");
        break;
    default:
        print ("Bene\n");
        break;
}', 'vala'),
            P('Nello `switch` ogni caso finisce con `break`.', 'In `switch` each case ends with `break`.'),
            P('Quando ti serve ogni elemento, `foreach` è più chiaro e più sicuro del `for` con l’indice, perché non può uscire dall’array; il `for` classico resta utile quando ti serve proprio il numero, come nei giri. Dentro un ciclo `continue` salta al giro successivo e `break` esce. A differenza di C, lo `switch` di Vala accetta anche le **stringhe**: `case "mela":` funziona, ed è comodo per leggere comandi o opzioni.', 'When you need every item, `foreach` is clearer and safer than the indexed `for`, because it cannot go past the end of the array; the classic `for` stays useful when you really need the number, as with the rounds. Inside a loop `continue` jumps to the next round and `break` leaves. Unlike C, Vala’s `switch` also accepts **strings**: `case "mela":` works, and it is handy for reading commands or options.'),
        ], [
            Q('c1', L('Come scorri tutti gli elementi di un array?', 'How do you walk every item of an array?'), ['foreach (var x in arr)', 'for x in arr', 'arr.each', 'loop arr'], 0, L('Come in C#.', 'As in C#.')),
            Q('c2', L('Con che cosa finisce un caso di `switch`?', 'What ends a `switch` case?'), ['break', 'end', 'stop', 'next'], 0, L('Altrimenti errore.', 'Otherwise an error.')),
            Q('c3', L('Quante volte gira `for (int i = 0; i < 4; i++)`?', 'How many times does `for (int i = 0; i < 4; i++)` run?'), ['4', '3', '5', '0'], 0, L('0, 1, 2, 3.', '0, 1, 2, 3.')),
        ], task('vala', L('Con `foreach (string n in nomi)` stampa ogni nome.', 'With `foreach (string n in nomi)` print each name.'), 'string[] nomi = { "Ada", "Bea" };
', [re('foreach\s*\(\s*(string|var)\s+n\s+in\s+nomi\s*\)', L('Il ciclo è giusto', 'The loop is right')), re('print\s*\(', L('Stampi', 'You print'))], '', 'foreach (string n in nomi) {
    print ("%s\n", n);
}'), [
            dOrder(pSort(), ['int n = 3;', 'while (n > 0) {', '    print ("%d\n", n);', '    n--;', '}'], L('Prepara, controlla, stampa, avanza.', 'Set up, check, print, advance.')),
            dBug(pBug(), ['string[] voti = { "6", "8" };', 'foreach (string v of voti) {', '    print ("%s\n", v);', '}'], 1, 'foreach (string v in voti) {', L('In Vala si scrive `in`, non `of`.', 'In Vala you write `in`, not `of`.')),
        ]),
        lesson('methods', L('Metodi', 'Methods'), [
            P('Un metodo ha un tipo di ritorno (`void` se non restituisce nulla), un nome e parametri tipizzati. I parametri con valore predefinito si possono omettere nella chiamata.', 'A method has a return type (`void` if it returns nothing), a name and typed parameters. Parameters with a default value can be left out of the call.'),
            C(L('int quadrato (int n) {
    return n * n;
}

string saluta (string nome, string punto = "!") {
    return @"Ciao $nome$punto";
}

void main () {
    print ("%d\n", quadrato (4));
    print ("%s\n", saluta ("Ada"));
}', 'int quadrato (int n) {
    return n * n;
}

string saluta (string nome, string punto = "!") {
    return @"Hi $nome$punto";
}

void main () {
    print ("%d\n", quadrato (4));
    print ("%s\n", saluta ("Ada"));
}'), 'vala', L('<pre>16
Ciao Ada!</pre>', '<pre>16
Hi Ada!</pre>')),
            P('I metodi fuori dalle classi si chiamano funzioni; dentro una classe diventano metodi dell’oggetto.', 'Methods outside classes are called functions; inside a class they become the object’s methods.'),
            P('Una differenza da C# e Java: Vala **non ha l’overloading**, quindi non puoi avere due metodi `saluta` con parametri diversi. Si usano invece i valori predefiniti, come `punto = "!"`, o nomi distinti. Per restituire più di un valore c’è il parametro `out`: `void dividi (int a, int b, out int resto)`, chiamato con `dividi (7, 2, out r)`. Con `ref` il metodo riceve una variabile esistente e può modificarla.', 'One difference from C# and Java: Vala has **no overloading**, so you cannot have two `saluta` methods with different parameters. You use default values instead, like `punto = "!"`, or distinct names. To return more than one value there is the `out` parameter: `void dividi (int a, int b, out int resto)`, called with `dividi (7, 2, out r)`. With `ref` the method receives an existing variable and can change it.'),
        ], [
            Q('m1', L('Che tipo di ritorno ha un metodo che non restituisce nulla?', 'What return type does a method returning nothing have?'), ['void', 'null', 'none', 'empty'], 0, L('Come in C.', 'As in C.')),
            Q('m2', L('Si può omettere un parametro con valore predefinito?', 'Can you leave out a parameter with a default value?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo il primo', 'Only the first'), L('Solo se è int', 'Only if it is int')], 0, L('Prende il valore predefinito.', 'It takes the default value.')),
            Q('m3', L('Come restituisci un valore?', 'How do you return a value?'), ['return', 'yield', 'give', 'out'], 0, L('`return valore;`.', '`return value;`.')),
        ], task('vala', L('Scrivi `int doppio (int n)` che restituisce `n * 2`.', 'Write `int doppio (int n)` that returns `n * 2`.'), '', [re('int\s+doppio\s*\(\s*int\s+n\s*\)', L('La firma è giusta', 'The signature is right')), re('return\s+n\s*\*\s*2\s*;', L('Restituisci il doppio', 'You return the double'))], '', 'int doppio (int n) {
    return n * 2;
}'), [
            dBlanks(pFill(), "___ media (double a, double b) {\n    ___ (a + b) / 2;\n}\n___ stampa (string s) {\n    print (\"%s\\n\", s);\n}", ['double', 'return', 'void'], ['int', 'give', 'null'], L('Tipo, restituzione, nessun risultato.', 'Type, return, no result.')),
            dFlash(pFlash(), [[L('I parametri hanno un tipo', 'Parameters have a type'), true], [L('Un metodo `void` restituisce un valore', 'A `void` method returns a value'), false], [L('I valori predefiniti sono permessi', 'Default values are allowed'), true], [L('Due metodi della stessa classe possono avere lo stesso nome con parametri diversi', 'Two methods of the same class can share a name with different parameters'), false]], L('`void` significa niente.', '`void` means nothing.')),
        ]),
        lesson('classes', L('Classi e GObject', 'Classes and GObject'), [
            P('Le classi che ereditano da `Object` sono **GObject**: il sistema a oggetti di GNOME. Hanno **proprietà** con `get`/`set`, un costruttore e possono emettere **segnali**.', 'Classes inheriting from `Object` are **GObjects**: GNOME’s object system. They have **properties** with `get`/`set`, a constructor and can emit **signals**.'),
            C(L('public class Conto : Object {
    public string titolare { get; construct; }
    public double saldo { get; private set; default = 0; }

    public signal void cambiato (double nuovo);

    public Conto (string titolare) {
        Object (titolare: titolare);
    }

    public void versa (double importo) {
        saldo += importo;
        cambiato (saldo);
    }
}

var c = new Conto ("Ada");
c.cambiato.connect ((s) => print ("Saldo: %.2f\n", s));
c.versa (50);', 'public class Conto : Object {
    public string titolare { get; construct; }
    public double saldo { get; private set; default = 0; }

    public signal void cambiato (double nuovo);

    public Conto (string titolare) {
        Object (titolare: titolare);
    }

    public void versa (double importo) {
        saldo += importo;
        cambiato (saldo);
    }
}

var c = new Conto ("Ada");
c.cambiato.connect ((s) => print ("Balance: %.2f\n", s));
c.versa (50);'), 'vala', L('<pre>Saldo: 50.00</pre>', '<pre>Balance: 50.00</pre>')),
            P('Un **segnale** avvisa chi si è collegato con `connect`: è il modo in cui i bottoni GTK dicono “sono stato cliccato”.', 'A **signal** notifies whoever connected with `connect`: it is how GTK buttons say “I was clicked”.'),
            Fig(L(fig_valabase_signal('altri gestori collegati'), fig_valabase_signal('other connected handlers')), L('L’oggetto Conto esegue versa (50) ed emette il segnale cambiato (50.0). Dal segnale partono due frecce: una, connect, verso la lambda che stampa il saldo, l’altra verso eventuali altri gestori collegati.', 'The Conto object runs versa (50) and emits the cambiato (50.0) signal. Two arrows leave the signal: one, connect, to the lambda that prints the balance, the other to any other connected handlers.'), L('Chi emette non sa chi ascolta: possono essere nessuno, uno o molti.', 'The emitter does not know who listens: it may be nobody, one or many.')),
            P('Le proprietà sono più di semplici campi: ogni volta che cambiano, GObject emette il segnale `notify`, e altri oggetti possono reagire, per esempio un’etichetta che si aggiorna da sola. `construct` indica una proprietà che si imposta una volta, alla creazione, con la chiamata `Object (titolare: titolare)`. Gli oggetti GObject contano i propri riferimenti: quando nessuno li usa più vengono liberati, senza un garbage collector.', 'Properties are more than plain fields: every time they change, GObject emits the `notify` signal, and other objects can react, for example a label that updates itself. `construct` marks a property set once, at creation, through the `Object (titolare: titolare)` call. GObjects count their own references: when nobody uses them any more they are freed, without a garbage collector.'),
        ], [
            Q('o1', L('Da quale classe ereditano i GObject?', 'Which class do GObjects inherit from?'), ['Object', 'GClass', 'Base', 'Widget'], 0, L('`class X : Object`.', '`class X : Object`.')),
            Q('o2', L('Come ci si collega a un segnale?', 'How do you connect to a signal?'), ['.connect (…)', '.on (…)', '.listen (…)', '.bind (…)'], 0, L('Con una funzione o una lambda.', 'With a function or a lambda.')),
            Q('o3', L('Che cosa fa `private set`?', 'What does `private set` do?'), [L('Leggibile da fuori, modificabile solo dentro', 'Readable outside, changeable only inside'), L('Nasconde la proprietà', 'Hides the property'), L('La rende costante', 'Makes it constant'), L('Niente', 'Nothing')], 0, L('Protegge lo stato.', 'It protects the state.')),
        ], task('vala', L('Crea `public class Gatto : Object` con la proprietà `public string nome { get; set; }`.', 'Create `public class Gatto : Object` with the property `public string nome { get; set; }`.'), '', [re('public\s+class\s+Gatto\s*:\s*Object', L('La classe è un GObject', 'The class is a GObject')), re('public\s+string\s+nome\s*\{\s*get\s*;\s*set\s*;\s*\}', L('C’è la proprietà', 'There is the property'))], '', 'public class Gatto : Object {
    public string nome { get; set; }
}'), [
            dOrder(pSort(), ['var b = new Gtk.Button.with_label ("Salva");', 'b.clicked.connect (() => {', '    print ("Salvato\n");', '});'], L('Crea il bottone, collegati al segnale, reagisci.', 'Create the button, connect to the signal, react.')),
            dMatch(pMatch(), [['Object', L('La base dei GObject', 'The base of GObjects')], ['signal', L('Avviso agli ascoltatori', 'Notice to listeners')], ['connect', L('Ascolta un segnale', 'Listens to a signal')], ['{ get; set; }', L('Proprietà', 'Property')], ['construct', L('Assegnata alla creazione', 'Set at construction')]], L('GObject in Vala.', 'GObject in Vala.')),
        ]),
        lesson('strings', L('Stringhe', 'Strings'), [
            P('Le stringhe di Vala hanno molti metodi pronti: `strip ()` toglie gli spazi ai lati, `up ()` e `down ()` cambiano le maiuscole, `replace ()`, `split ()`, `has_prefix ()` e `contains ()` cercano e trasformano. Ogni metodo restituisce una stringa nuova: l’originale non cambia.', 'Vala strings have many ready-made methods: `strip ()` removes spaces at the sides, `up ()` and `down ()` change case, `replace ()`, `split ()`, `has_prefix ()` and `contains ()` search and transform. Each method returns a new string: the original does not change.'),
            C('void main () {
    string nome = "  Ada Lovelace  ";
    string pulito = nome.strip ();
    print ("%s\n", pulito.up ());              // ADA LOVELACE
    print ("%d\n", pulito.length);             // 12 (byte)
    print ("%s\n", pulito.replace ("Ada", "Augusta"));

    string[] parti = "uno,due,tre".split (",");
    print ("%s\n", string.joinv (" - ", parti));

    if (pulito.has_prefix ("Ada")) print ("Inizia con Ada\n");
    int anni = int.parse ("36");
    print ("%s\n", (anni + 1).to_string ());
}', 'vala'),
            P('Le stringhe sono in UTF-8: `length` conta i **byte**, e una lettera accentata ne occupa due; per i caratteri c’è `char_count ()`. `int.parse` trasforma un testo in numero, `to_string ()` fa il contrario.', 'Strings are UTF-8: `length` counts **bytes**, and an accented letter takes two; for characters there is `char_count ()`. `int.parse` turns text into a number, `to_string ()` does the opposite.'),
            Fig(L(fig_valabase_utf8('caratteri', 'byte'), fig_valabase_utf8('characters', 'bytes')), L('La parola «però»: sopra i quattro caratteri p, e, r, ò; sotto i cinque byte 70, 65, 72, C3, B2. Le prime tre lettere corrispondono a un byte ciascuna, la ò a due byte.', 'The word “però”: above the four characters p, e, r, ò; below the five bytes 70, 65, 72, C3, B2. The first three letters match one byte each, ò matches two bytes.'), L('Per questo `length` e `char_count ()` danno numeri diversi con le lettere accentate.', 'That is why `length` and `char_count ()` give different numbers with accented letters.')),
        ], [
            Q('st1', L('Quale metodo toglie gli spazi all’inizio e alla fine?', 'Which method removes spaces at the start and end?'), ['trim ()', 'strip ()', 'clean ()', 'cut ()'], 1, L('`chug ()` e `chomp ()` li tolgono da un lato solo.', '`chug ()` and `chomp ()` remove them from one side only.')),
            Q('st2', L('Quanto vale `"è".length`?', 'What is `"è".length`?'), ['1', '0', '2', '-1'], 2, L('In UTF-8 la «è» occupa due byte; `char_count ()` darebbe 1.', 'In UTF-8 “è” takes two bytes; `char_count ()` would give 1.')),
            Q('st3', L('Come trasformi `"42"` in un intero?', 'How do you turn `"42"` into an integer?'), ['Integer.valueOf ("42")', 'parseInt ("42")', '"42" as int', 'int.parse ("42")'], 3, L('Gli altri vengono da Java, JavaScript e C#.', 'The others come from Java, JavaScript and C#.')),
        ], task('vala', L('Stampa `titolo` in maiuscolo con `print ("%s\n", titolo.up ());`.', 'Print `titolo` in capitals with `print ("%s\n", titolo.up ());`.'), 'string titolo = "vala";
', [re('print\s*\(\s*"%s(\\\\n)?"\s*,\s*titolo\.up\s*\(\s*\)\s*\)\s*;', L('Stampi in maiuscolo', 'You print it in capitals'))], '', 'print ("%s\n", titolo.up ());'), [
            dMatch(pMatch(), [['strip ()', L('Toglie gli spazi ai lati', 'Removes spaces at the sides')], ['up ()', L('Tutto maiuscolo', 'All capitals')], ['split (",")', L('Divide in un array', 'Splits into an array')], ['has_prefix', L('Inizia con…', 'Starts with…')], ['int.parse', L('Da testo a numero', 'From text to number')]], L('Gli attrezzi delle stringhe.', 'String tools.')),
            dFlash(pFlash(), [[L('`length` conta i byte', '`length` counts bytes'), true], [L('`replace` modifica la stringa originale', '`replace` changes the original string'), false], [L('`to_string ()` trasforma un numero in testo', '`to_string ()` turns a number into text'), true], [L('Le stringhe di Vala sono in UTF-8', 'Vala strings are UTF-8'), true]], L('Ogni metodo restituisce una stringa nuova.', 'Every method returns a new string.')),
        ]),
        lesson('enums-structs', L('Enum, struct e namespace', 'Enums, structs and namespaces'), [
            P('Un **enum** elenca valori con nome e in Vala può avere anche metodi. Una **struct** è un tipo per **valore**: assegnarla la copia, al contrario delle classi, che sono per riferimento. I **namespace** raggruppano tipi e funzioni sotto un nome.', 'An **enum** lists named values and in Vala it can also have methods. A **struct** is a **value** type: assigning it copies it, unlike classes, which are by reference. **Namespaces** group types and functions under one name.'),
            C('namespace Fucina {
    public enum Livello {
        BASE,
        INTERMEDIO,
        AVANZATO;

        public string etichetta () {
            switch (this) {
                case Livello.BASE: return "Base";
                case Livello.INTERMEDIO: return "Intermedio";
                default: return "Avanzato";
            }
        }
    }

    public struct Punto {
        public double x;
        public double y;
    }
}

void main () {
    var l = Fucina.Livello.INTERMEDIO;
    print ("%s\n", l.etichetta ());

    Fucina.Punto a = { 1, 2 };
    var b = a;          // copia: è un tipo per valore
    b.x = 10;
    print ("%g %g\n", a.x, b.x);   // 1 10
}', 'vala'),
            P('Con `using Fucina;` in cima al file si usano i nomi senza prefisso, come fai con `using Gtk;`. Le struct sono adatte a dati piccoli come punti, colori e dimensioni; per oggetti con identità, segnali e proprietà si usa una classe.', 'With `using Fucina;` at the top of the file you use the names without a prefix, as you do with `using Gtk;`. Structs suit small data such as points, colours and sizes; for objects with identity, signals and properties you use a class.'),
        ], [
            Q('es1', L('Che cosa succede ad `a` se modifichi `b`, una copia della struct?', 'What happens to `a` if you change `b`, a copy of the struct?'), [L('Cambia anche a', 'a changes too'), L('Niente: b è una copia indipendente', 'Nothing: b is an independent copy'), L('Errore di compilazione', 'Compile error'), L('a diventa null', 'a becomes null')], 1, L('Le struct sono tipi per valore.', 'Structs are value types.')),
            Q('es2', L('Un enum di Vala può avere metodi?', 'Can a Vala enum have methods?'), [L('No, mai', 'No, never'), L('Solo statici', 'Only static ones'), L('Sì', 'Yes'), L('Solo se eredita da Object', 'Only if it inherits from Object')], 2, L('Dopo l’ultimo valore si mette `;` e poi i metodi.', 'After the last value you put `;` and then the methods.')),
            Q('es3', L('A che cosa serve `using Gtk;` in cima al file?', 'What is `using Gtk;` at the top of the file for?'), [L('A usare i nomi di Gtk senza il prefisso `Gtk.`', 'Using Gtk names without the `Gtk.` prefix'), L('A installare GTK', 'Installing GTK'), L('A compilare con GTK', 'Compiling with GTK'), L('A creare un namespace Gtk', 'Creating a Gtk namespace')], 0, L('Per compilare serve comunque `--pkg gtk4`.', 'To compile you still need `--pkg gtk4`.')),
        ], task('vala', L('Dichiara `enum Semaforo` con i valori `ROSSO`, `GIALLO` e `VERDE`.', 'Declare `enum Semaforo` with the values `ROSSO`, `GIALLO` and `VERDE`.'), '', [re('enum\s+Semaforo\s*\{', L('L’enum esiste', 'The enum exists')), re('ROSSO\s*,\s*GIALLO\s*,\s*VERDE', L('Ha i tre colori', 'It has the three colours'))], '', 'enum Semaforo {
    ROSSO,
    GIALLO,
    VERDE
}'), [
            dMatch(pMatch(), [['enum', L('Valori con nome', 'Named values')], ['struct', L('Tipo per valore', 'Value type')], ['class', L('Tipo per riferimento', 'Reference type')], ['namespace', L('Raggruppa i nomi', 'Groups names')], ['using', L('Evita il prefisso', 'Avoids the prefix')]], L('Tipi e nomi in Vala.', 'Types and names in Vala.')),
            dBlanks(pFill(), "___ Colore { ROSSO, BLU }\n___ Pixel {\n    public int x;\n    public int y;\n}\n___ Fucina;", ['enum', 'struct', 'using'], ['class', 'record', 'import'], L('Enum, struct, namespace in uso.', 'Enum, struct, namespace in use.')),
        ]),
        lesson('delegates', L('Delegate e lambda', 'Delegates and lambdas'), [
            P('Un **delegate** è il tipo di una funzione: dice quali parametri riceve e che cosa restituisce. Una variabile di quel tipo può contenere una funzione con nome o una **lambda** come `(a, b) => a + b`. È lo stesso meccanismo che usi con `connect` sui segnali.', 'A **delegate** is the type of a function: it says which parameters it takes and what it returns. A variable of that type can hold a named function or a **lambda** such as `(a, b) => a + b`. It is the same mechanism you use with `connect` on signals.'),
            C('delegate int Operazione (int a, int b);

int somma (int a, int b) {
    return a + b;
}

int applica (Operazione op, int x, int y) {
    return op (x, y);
}

void main () {
    print ("%d\n", applica (somma, 2, 3));                 // 5
    print ("%d\n", applica ((a, b) => a * b, 4, 5));       // 20

    int extra = 10;
    Operazione piu_extra = (a, b) => a + b + extra;        // cattura extra
    print ("%d\n", piu_extra (1, 2));                      // 13

    var nomi = new GenericArray<string> ();
    nomi.add ("Bea");
    nomi.add ("Ada");
    nomi.sort ((a, b) => strcmp (a, b));
}', 'vala'),
            P('Le lambda possono usare le variabili intorno a sé: si dice che le **catturano**. Se il corpo è un blocco si scrive `(a, b) => { … return …; }`. Molte funzioni di GLib, come `sort`, accettano una lambda per decidere come confrontare gli elementi.', 'Lambdas can use the variables around them: they are said to **capture** them. If the body is a block you write `(a, b) => { … return …; }`. Many GLib functions, such as `sort`, accept a lambda to decide how to compare items.'),
        ], [
            Q('dl1', L('Che cosa descrive un delegate?', 'What does a delegate describe?'), [L('Il tipo di una funzione: parametri e risultato', 'The type of a function: parameters and result'), L('Una classe astratta', 'An abstract class'), L('Un segnale di GTK', 'A GTK signal'), L('Un thread', 'A thread')], 0, L('Poi ci metti una funzione o una lambda compatibile.', 'Then you put a matching function or lambda in it.')),
            Q('dl2', L('Come scrivi in Vala una lambda che somma due numeri?', 'How do you write a lambda adding two numbers in Vala?'), ['lambda a, b: a + b', 'fn(a, b) { a + b }', 'a, b -> a + b', '(a, b) => a + b'], 3, L('Come in C#.', 'As in C#.')),
            Q('dl3', L('Una lambda può usare una variabile locale definita fuori da essa?', 'Can a lambda use a local variable defined outside it?'), [L('No, mai', 'No, never'), L('Sì, la cattura', 'Yes, it captures it'), L('Solo se è `const`', 'Only if it is `const`'), L('Solo nei segnali', 'Only in signals')], 1, L('È una closure.', 'It is a closure.')),
        ], task('vala', L('Dichiara `delegate bool Filtro (int n);` e una variabile `Filtro pari = (n) => n % 2 == 0;`.', 'Declare `delegate bool Filtro (int n);` and a variable `Filtro pari = (n) => n % 2 == 0;`.'), '', [re('delegate\s+bool\s+Filtro\s*\(\s*int\s+n\s*\)\s*;', L('Il delegate esiste', 'The delegate exists')), re('Filtro\s+pari\s*=\s*\(?\s*n\s*\)?\s*=>\s*n\s*%\s*2\s*==\s*0', L('La lambda controlla i pari', 'The lambda checks for even numbers'))], '', 'delegate bool Filtro (int n);

Filtro pari = (n) => n % 2 == 0;'), [
            dBug(pBug(), ['delegate int Calcolo (int x);', 'Calcolo triplo = (x) => x * 3;', 'int r = triplo ("5");'], 2, 'int r = triplo (5);', L('Il delegate vuole un `int`, non una stringa.', 'The delegate wants an `int`, not a string.')),
            dFlash(pFlash(), [[L('Un delegate è il tipo di una funzione', 'A delegate is the type of a function'), true], [L('Le lambda non possono avere un corpo a blocco', 'Lambdas cannot have a block body'), false], [L('`connect` sui segnali accetta una lambda', '`connect` on signals accepts a lambda'), true], [L('Una funzione con nome si può passare come delegate', 'A named function can be passed as a delegate'), true]], L('Il corpo può essere un’espressione o un blocco.', 'The body can be an expression or a block.')),
        ]),
    ],
];
