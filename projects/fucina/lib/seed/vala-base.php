<?php
/** Vala basics: the first program, types, control flow, arrays and methods, classes and GObject. */

return [
    'id' => 'vala-base', 'family' => 'vala', 'after' => null,
    'title' => L('Vala base', 'Vala Basics'), 'short' => L('Vala base', 'Vala Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio delle app GNOME: sintassi simile a C#, compilato in C. Tipi, cicli, metodi e classi.', 'The language of GNOME apps: C#-like syntax, compiled to C. Types, loops, methods and classes.'),
    'lessons' => [
        lesson('hello', L('Il primo programma', 'The first program'), [
            P('**Vala** ha una sintassi simile a C# ma viene tradotto in C e compilato: programmi veloci e senza macchina virtuale. Il punto di partenza è `main`; `print` scrive (senza andare a capo da solo).', '**Vala** has a C#-like syntax but is translated to C and compiled: fast programs with no virtual machine. The starting point is `main`; `print` writes (without adding a newline by itself).'),
            C('void main () {
    string nome = "Ada";
    print ("Ciao, %s!\n", nome);
    stdout.printf ("Due più due fa %d\n", 2 + 2);
}

// terminale: valac ciao.vala && ./ciao', 'vala'),
            P('`%s` inserisce un testo, `%d` un intero. Lo stile GNOME mette uno spazio prima delle parentesi delle chiamate.', '`%s` inserts text, `%d` an integer. GNOME style puts a space before the brackets of calls.'),
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
        ], [
            Q('t1', L('Quale tipo per un numero con decimali?', 'Which type for a number with decimals?'), ['double', 'int', 'char', 'bool'], 0, L('Anche `float`, meno preciso.', 'Also `float`, less precise.')),
            Q('t2', L('A che cosa serve `@` davanti a una stringa?', 'What is `@` in front of a string for?'), [L('Inserire valori con $', 'Inserting values with $'), L('Commentare', 'Commenting'), L('Rendere la stringa costante', 'Making the string constant'), L('Niente', 'Nothing')], 0, L('Template di stringa.', 'String templates.')),
            Q('t3', L('Che cosa significa `string?`', 'What does `string?` mean?'), [L('Una stringa che può essere null', 'A string that may be null'), L('Una domanda', 'A question'), L('Un errore', 'An error'), L('Una stringa vuota', 'An empty string')], 0, L('Tipo nullable.', 'Nullable type.')),
        ], task('vala', L('Dichiara `int livelli = 6;` e `string corso = "Vala";`.', 'Declare `int livelli = 6;` and `string corso = "Vala";`.'), '', [re('int\s+livelli\s*=\s*6\s*;', L('C’è l’intero', 'There is the int')), re('string\s+corso\s*=\s*"Vala"\s*;', L('C’è la stringa', 'There is the string'))], '', 'int livelli = 6;
string corso = "Vala";'), [
            dBlanks(pFill(), "___ nome = \"Ada\";\n___ int MASSIMO = 5;\nstring frase = ___\"Ciao \$nome\";", ['var', 'const', '@'], ['let', 'final', '$'], L('Deduzione, costante, template.', 'Inference, constant, template.')),
            dFlash(pFlash(), [[L('`var` deduce il tipo', '`var` infers the type'), true], [L('Ogni tipo in Vala può valere null', 'Every type in Vala can be null'), false], [L('`const` non si può cambiare', '`const` cannot change'), true]], L('Serve il `?` per ammettere null.', 'You need `?` to allow null.')),
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
            C('int quadrato (int n) {
    return n * n;
}

string saluta (string nome, string punto = "!") {
    return @"Ciao $nome$punto";
}

void main () {
    print ("%d\n", quadrato (4));
    print ("%s\n", saluta ("Ada"));
}', 'vala'),
            P('I metodi fuori dalle classi si chiamano funzioni; dentro una classe diventano metodi dell’oggetto.', 'Methods outside classes are called functions; inside a class they become the object’s methods.'),
        ], [
            Q('m1', L('Che tipo di ritorno ha un metodo che non restituisce nulla?', 'What return type does a method returning nothing have?'), ['void', 'null', 'none', 'empty'], 0, L('Come in C.', 'As in C.')),
            Q('m2', L('Si può omettere un parametro con valore predefinito?', 'Can you leave out a parameter with a default value?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo il primo', 'Only the first'), L('Solo se è int', 'Only if it is int')], 0, L('Prende il valore predefinito.', 'It takes the default value.')),
            Q('m3', L('Come restituisci un valore?', 'How do you return a value?'), ['return', 'yield', 'give', 'out'], 0, L('`return valore;`.', '`return value;`.')),
        ], task('vala', L('Scrivi `int doppio (int n)` che restituisce `n * 2`.', 'Write `int doppio (int n)` that returns `n * 2`.'), '', [re('int\s+doppio\s*\(\s*int\s+n\s*\)', L('La firma è giusta', 'The signature is right')), re('return\s+n\s*\*\s*2\s*;', L('Restituisci il doppio', 'You return the double'))], '', 'int doppio (int n) {
    return n * 2;
}'), [
            dBlanks(pFill(), "___ media (double a, double b) {\n    ___ (a + b) / 2;\n}\n___ stampa (string s) {\n    print (\"%s\\n\", s);\n}", ['double', 'return', 'void'], ['int', 'give', 'null'], L('Tipo, restituzione, nessun risultato.', 'Type, return, no result.')),
            dFlash(pFlash(), [[L('I parametri hanno un tipo', 'Parameters have a type'), true], [L('Un metodo `void` restituisce un valore', 'A `void` method returns a value'), false], [L('I valori predefiniti sono permessi', 'Default values are allowed'), true]], L('`void` significa niente.', '`void` means nothing.')),
        ]),
        lesson('classes', L('Classi e GObject', 'Classes and GObject'), [
            P('Le classi che ereditano da `Object` sono **GObject**: il sistema a oggetti di GNOME. Hanno **proprietà** con `get`/`set`, un costruttore e possono emettere **segnali**.', 'Classes inheriting from `Object` are **GObjects**: GNOME’s object system. They have **properties** with `get`/`set`, a constructor and can emit **signals**.'),
            C('public class Conto : Object {
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
c.versa (50);', 'vala'),
            P('Un **segnale** avvisa chi si è collegato con `connect`: è il modo in cui i bottoni GTK dicono “sono stato cliccato”.', 'A **signal** notifies whoever connected with `connect`: it is how GTK buttons say “I was clicked”.'),
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
    ],
];
