<?php
/** Java basics: the main method, types, conditions and loops, arrays and methods, strings, numbers and input, the Arrays class, compact programs (Java 25). */

/** From source to bytecode to the JVM of every operating system. */
if (!function_exists('fig_javabase_jvm')) {
    function fig_javabase_jvm(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="aj" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="86" width="140" height="64" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="86" y="114" text-anchor="middle" font-family="ui-monospace, monospace" font-weight="700">Ciao.java</text>
<text x="86" y="136" text-anchor="middle" font-size="13" fill="#5B5880">{$t[0]}</text>
<line x1="160" y1="118" x2="222" y2="118" stroke="#1E1C52" stroke-width="2" marker-end="url(#aj)"/>
<text x="191" y="108" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">javac</text>
<rect x="228" y="86" width="140" height="64" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="298" y="114" text-anchor="middle" font-family="ui-monospace, monospace" font-weight="700">Ciao.class</text>
<text x="298" y="136" text-anchor="middle" font-size="13" fill="#5B5880">{$t[1]}</text>
<g font-family="ui-monospace, monospace" font-size="13" text-anchor="middle">
<rect x="450" y="24" width="174" height="50" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="537" y="54">JVM · Windows</text>
<rect x="450" y="93" width="174" height="50" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="537" y="123">JVM · macOS</text>
<rect x="450" y="162" width="174" height="50" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="537" y="192">JVM · Linux</text>
</g>
<g fill="none" stroke="#1E1C52" stroke-width="2">
<path d="M372 110 C410 100 410 50 444 49" marker-end="url(#aj)"/><path d="M372 118 L444 118" marker-end="url(#aj)"/><path d="M372 126 C410 136 410 186 444 187" marker-end="url(#aj)"/>
</g>
<text x="404" y="112" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">java</text>
<text x="320" y="232" text-anchor="middle" fill="#5B5880">{$t[2]}</text>
</svg>
SVG;
    }
}

/** A primitive holds its value; a reference points to an object. Two equal texts can be two objects. */
if (!function_exists('fig_javabase_refs')) {
    function fig_javabase_refs(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="ar" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="110" y="26" text-anchor="middle" font-weight="700" fill="#5B33C9">{$t[0]}</text>
<text x="450" y="26" text-anchor="middle" font-weight="700" fill="#1B66BC">{$t[1]}</text>
<g font-family="ui-monospace, monospace" font-size="14">
<text x="30" y="68">int anni</text><rect x="130" y="46" width="70" height="34" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="165" y="68" text-anchor="middle" font-weight="700">36</text>
<text x="30" y="118">String a</text><rect x="130" y="96" width="70" height="34" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><circle cx="165" cy="113" r="5" fill="#1E1C52"/>
<text x="30" y="168">String b</text><rect x="130" y="146" width="70" height="34" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><circle cx="165" cy="163" r="5" fill="#1E1C52"/>
<rect x="380" y="86" width="140" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="450" y="111" text-anchor="middle">"Ada"</text>
<rect x="380" y="146" width="140" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="450" y="171" text-anchor="middle">"Ada"</text>
<text x="530" y="111" font-size="12" fill="#5B5880">#1</text><text x="530" y="171" font-size="12" fill="#5B5880">#2</text>
</g>
<line x1="170" y1="113" x2="374" y2="106" stroke="#1E1C52" stroke-width="2" marker-end="url(#ar)"/>
<line x1="170" y1="163" x2="374" y2="166" stroke="#1E1C52" stroke-width="2" marker-end="url(#ar)"/>
<g font-family="ui-monospace, monospace" font-size="15">
<rect x="60" y="206" width="230" height="38" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/><text x="175" y="231" text-anchor="middle">a == b  →  false</text>
<rect x="350" y="206" width="230" height="38" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="465" y="231" text-anchor="middle">a.equals(b)  →  true</text>
</g>
</svg>
SVG;
    }
}

/** A call stack: each call puts a frame on top of main and takes it off when it returns. */
if (!function_exists('fig_javabase_stack')) {
    function fig_javabase_stack(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="ui-monospace, monospace" font-size="14" fill="#1E1C52">
<defs><marker id="as" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="160" y="28" text-anchor="middle" font-family="system-ui, sans-serif" font-weight="700">1. saluta("Ada")</text>
<text x="480" y="28" text-anchor="middle" font-family="system-ui, sans-serif" font-weight="700">2. quadrato(4)</text>
<rect x="60" y="100" width="200" height="50" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="76" y="122" font-weight="700">saluta</text><text x="76" y="142" font-size="13">nome = "Ada"</text>
<rect x="60" y="156" width="200" height="50" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="76" y="186" font-weight="700">main</text>
<rect x="380" y="100" width="200" height="50" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="396" y="122" font-weight="700">quadrato</text><text x="396" y="142" font-size="13">n = 4</text>
<rect x="380" y="156" width="200" height="50" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="396" y="186" font-weight="700">main</text>
<path d="M584 125 C604 125 604 181 588 181" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#as)"/>
<text x="610" y="158" font-weight="700" fill="#0B7F5C">16</text>
<line x1="40" y1="212" x2="280" y2="212" stroke="#8A87B0" stroke-width="2"/><line x1="360" y1="212" x2="600" y2="212" stroke="#8A87B0" stroke-width="2"/>
<text x="160" y="70" text-anchor="middle" font-family="system-ui, sans-serif" font-size="13" fill="#5B5880">{$t[0]}</text>
<text x="480" y="70" text-anchor="middle" font-family="system-ui, sans-serif" font-size="13" fill="#5B5880">{$t[0]}</text>
<text x="320" y="238" text-anchor="middle" font-family="system-ui, sans-serif" font-size="14" fill="#5B5880">{$t[1]}</text>
</svg>
SVG;
    }
}

/** A 3 × 4 matrix: the first index picks the row, the second the column. */
if (!function_exists('fig_javabase_grid')) {
    function fig_javabase_grid(): string
    {
        $cells = '';
        for ($r = 0; $r < 3; $r++) for ($c = 0; $c < 4; $c++) {
            $x = 100 + $c * 60;
            $y = 50 + $r * 50;
            $hot = $r === 1 && $c === 2;
            $cells .= "<rect x=\"$x\" y=\"$y\" width=\"56\" height=\"46\" rx=\"8\" fill=\"" . ($hot ? '#FFE8D6' : '#F1F0F8') . "\" stroke=\"" . ($hot ? '#E8590C' : '#C9C7DE') . "\" stroke-width=\"2\"/><text x=\"" . ($x + 28) . "\" y=\"" . ($y + 29) . "\" text-anchor=\"middle\"" . ($hot ? ' font-weight="700"' : ' fill="#8A87B0"') . ">" . ($hot ? 5 : 0) . "</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="ui-monospace, monospace" font-size="15" fill="#1E1C52">
<g fill="#1B66BC" text-anchor="middle" font-size="13"><text x="128" y="38">0</text><text x="188" y="38">1</text><text x="248" y="38">2</text><text x="308" y="38">3</text></g>
<g fill="#B84508" text-anchor="end" font-size="13"><text x="88" y="78">0</text><text x="88" y="128">1</text><text x="88" y="178">2</text></g>
$cells
<text x="390" y="104" fill="#B84508" font-weight="700">griglia[1][2] = 5</text>
<text x="390" y="140" font-size="14">griglia.length    → 3</text>
<text x="390" y="166" font-size="14">griglia[0].length → 4</text>
</svg>
SVG;
    }
}

return [
    'id' => 'java-base', 'family' => 'java', 'after' => null,
    'title' => L('Java base', 'Java Basics'), 'short' => L('Java base', 'Java Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di Android e dei grandi server: `main`, tipi, condizioni, cicli, array e metodi, stringhe, numeri e input, programmi compatti di Java 25.', 'The language of Android and big servers: `main`, types, conditions, loops, arrays and methods, strings, numbers and input, Java 25 compact programs.'),
    'lessons' => [
        lesson('main', L('Il primo programma', 'The first program'), [
            P('Ogni programma Java parte dal metodo `main` dentro una **classe**. `System.out.println` scrive una riga. Ogni istruzione finisce con il punto e virgola.', 'Every Java program starts from the `main` method inside a **class**. `System.out.println` writes a line. Every statement ends with a semicolon.'),
            Fig(L(fig_javabase_jvm(['codice sorgente', 'bytecode', 'La stessa .class gira ovunque ci sia una JVM.']), fig_javabase_jvm(['source code', 'bytecode', 'The same .class runs wherever there is a JVM.'])),
                L('Ciao.java, il codice sorgente, passa per javac e diventa Ciao.class, il bytecode. Con il comando java lo stesso file viene eseguito da tre JVM: su Windows, su macOS e su Linux.', 'Ciao.java, the source code, goes through javac and becomes Ciao.class, the bytecode. With the java command the same file is run by three JVMs: on Windows, on macOS and on Linux.'),
                L('Si compila una volta, si esegue su qualsiasi sistema.', 'Compile once, run on any system.')),
            C(L('public class Ciao {
    public static void main(String[] args) {
        System.out.println("Ciao, Java!");
    }
}', 'public class Ciao {
    public static void main(String[] args) {
        System.out.println("Hello, Java!");
    }
}'), 'java', L('<pre>Ciao, Java!</pre>', '<pre>Hello, Java!</pre>')),
            P('Il file deve chiamarsi come la classe pubblica: `Ciao.java`. `javac Ciao.java` lo compila e `java Ciao` lo esegue; per un programma di un solo file basta `java Ciao.java`, che fa tutto in un colpo.', 'The file must be named like the public class: `Ciao.java`. `javac Ciao.java` compiles it and `java Ciao` runs it; for a single-file program `java Ciao.java` does both in one go.'),
            P('`javac` non produce un programma per Windows o per Mac, ma **bytecode**: istruzioni per la **Java Virtual Machine**, che esiste per ogni sistema. Le parole intorno a `main` hanno tutte un ruolo: `public` lo rende visibile, `static` permette di chiamarlo senza creare oggetti, `void` dice che non restituisce nulla, `String[] args` riceve le parole scritte dopo il nome del programma. Java distingue maiuscole e minuscole: `system.out` non funziona.', '`javac` does not produce a program for Windows or Mac but **bytecode**: instructions for the **Java Virtual Machine**, which exists for every system. The words around `main` all have a role: `public` makes it visible, `static` lets it be called without creating objects, `void` says it returns nothing, `String[] args` receives the words written after the program name. Java is case-sensitive: `system.out` does not work.'),
        ], [
            Q('m1', L('Da quale metodo parte un programma?', 'Which method does a program start from?'), ['main', 'start', 'run', 'init'], 0, L('`public static void main(String[] args)`.', '`public static void main(String[] args)`.')),
            Q('m2', L('Come scrivi una riga sullo schermo?', 'How do you write a line to the screen?'), ['System.out.println()', 'print()', 'echo', 'console.log()'], 0, L('`println` va a capo.', '`println` adds a newline.')),
            Q('m3', L('Come si deve chiamare il file della classe `Ciao`?', 'What must the file of class `Ciao` be called?'), ['Ciao.java', 'ciao.js', 'Main.java', 'Ciao.class'], 0, L('Stesso nome, maiuscole comprese.', 'Same name, capitals included.')),
        ], task('java', L('Nel `main` stampa `"Benvenuto"` con `System.out.println`.', 'In `main` print `"Benvenuto"` with `System.out.println`.'), 'public class App {
    public static void main(String[] args) {
    }
}', [re('System\.out\.println\s*\(\s*"Benvenuto"\s*\)\s*;', L('Stampi il saluto', 'You print the greeting'))], '', 'System.out.println("Benvenuto");'), [
            dOrder(pSort(), ['public class Ciao {', '    public static void main(String[] args) {', '        System.out.println("Ciao");', '    }', '}'], L('Classe, metodo, istruzione, chiusure.', 'Class, method, statement, closings.')),
            dBug(pBug(), ['public class Saluto {', '    public static void main(String[] args) {', '        System.out.println("Ciao")', '    }', '}'], 2, '        System.out.println("Ciao");', L('Ogni istruzione finisce con `;`.', 'Every statement ends with `;`.')),
        ]),
        lesson('types', L('Tipi e variabili', 'Types and variables'), [
            P('Java è **tipizzato**: ogni variabile ha un tipo deciso alla dichiarazione. `int` per gli interi, `double` per i decimali, `boolean` per vero/falso, `String` per il testo. `var` lascia dedurre il tipo al compilatore.', 'Java is **typed**: every variable has a type fixed when declared. `int` for whole numbers, `double` for decimals, `boolean` for true/false, `String` for text. `var` lets the compiler infer the type.'),
            C('int anni = 36;
double prezzo = 9.99;
boolean attivo = true;
String nome = "Ada";
var citta = "Londra"; // String

final int MAX = 10;   // costante
System.out.println(nome + " ha " + anni + " anni");', 'java'),
            P('`final` impedisce di cambiare il valore. Le stringhe si confrontano con `equals`, non con `==`.', '`final` prevents changing the value. Strings are compared with `equals`, not `==`.'),
            P('Perché `equals`? I tipi in minuscolo (`int`, `double`, `boolean`) sono **primitivi**: la variabile contiene direttamente il valore. `String` e tutte le classi sono **riferimenti**: la variabile contiene l’indirizzo di un oggetto. `==` confronta gli indirizzi, cioè chiede «è lo stesso oggetto?», mentre `equals` confronta il contenuto. Con le stringhe scritte tra virgolette Java a volte riusa lo stesso oggetto e `==` sembra funzionare: è proprio questo che rende l’errore insidioso.', 'Why `equals`? Lower-case types (`int`, `double`, `boolean`) are **primitives**: the variable holds the value directly. `String` and every class are **references**: the variable holds the address of an object. `==` compares addresses, that is it asks “is it the same object?”, while `equals` compares the content. With strings written between quotes Java sometimes reuses the same object and `==` seems to work: that is exactly what makes the mistake sneaky.'),
            Fig(L(fig_javabase_refs(['Variabili', 'Oggetti']), fig_javabase_refs(['Variables', 'Objects'])),
                L('A sinistra tre variabili: int anni contiene 36, String a e String b contengono un riferimento. Le frecce portano a due oggetti diversi, #1 e #2, entrambi con il testo "Ada". Sotto: a == b dà false, a.equals(b) dà true.', 'On the left three variables: int anni holds 36, String a and String b hold a reference. The arrows lead to two different objects, #1 and #2, both with the text "Ada". Below: a == b gives false, a.equals(b) gives true.'),
                L('`==` chiede se è lo stesso oggetto, `equals` se il contenuto è uguale.', '`==` asks whether it is the same object, `equals` whether the content is equal.')),
            Tip('Per i soldi non usare `double`: `0.1 + 0.2` fa `0.30000000000000004`. Usa `BigDecimal`, oppure conta in centesimi con un `long`.', 'Do not use `double` for money: `0.1 + 0.2` gives `0.30000000000000004`. Use `BigDecimal`, or count in cents with a `long`.'),
        ], [
            Q('t1', L('Quale tipo per un prezzo con decimali?', 'Which type for a price with decimals?'), ['double', 'int', 'boolean', 'char'], 0, L('`int` tiene solo interi.', '`int` holds only whole numbers.')),
            Q('t2', L('Come confronti due stringhe?', 'How do you compare two strings?'), ['a.equals(b)', 'a == b', 'a === b', 'compare(a, b)'], 0, L('`==` confronta i riferimenti.', '`==` compares references.')),
            Q('t3', L('Che cosa fa `final`?', 'What does `final` do?'), [L('Rende il valore non modificabile', 'Makes the value unchangeable'), L('Chiude il programma', 'Ends the program'), L('Rende pubblico', 'Makes it public'), L('Niente', 'Nothing')], 0, L('Assegnato una volta sola.', 'Assigned only once.')),
        ], task('java', L('Dichiara `int livelli = 6;` e `String corso = "Java";`.', 'Declare `int livelli = 6;` and `String corso = "Java";`.'), '', [re('int\s+livelli\s*=\s*6\s*;', L('C’è l’intero', 'There is the int')), re('String\s+corso\s*=\s*"Java"\s*;', L('C’è la stringa', 'There is the string'))], '', 'int livelli = 6;
String corso = "Java";'), [
            dMatch(pMatch(), [['int', L('Numero intero', 'Whole number')], ['double', L('Numero decimale', 'Decimal number')], ['boolean', L('Vero o falso', 'True or false')], ['char', L('Un carattere', 'One character')], ['String', L('Testo', 'Text')]], L('I tipi di base.', 'The basic types.')),
            dFlash(pFlash(), [[L('In Java ogni variabile ha un tipo', 'In Java every variable has a type'), true], [L('`==` è il modo giusto di confrontare stringhe', '`==` is the right way to compare strings'), false], [L('`var` deduce il tipo', '`var` infers the type'), true], [L('`int` può contenere numeri con la virgola', '`int` can hold decimal numbers'), false]], L('Per le stringhe c’è `equals`.', 'Strings use `equals`.')),
        ]),
        lesson('control', L('Condizioni e cicli', 'Conditions and loops'), [
            P('`if`/`else` sceglie, `switch` confronta un valore con molti casi, `for` e `while` ripetono. I blocchi stanno tra graffe.', '`if`/`else` chooses, `switch` compares one value with many cases, `for` and `while` repeat. Blocks sit between braces.'),
            C(L('for (int i = 1; i <= 3; i++) {
    System.out.println("Giro " + i);
}

String giudizio = switch (voto) {
    case 10, 9 -> "Ottimo";
    case 8, 7, 6 -> "Promosso";
    default -> "Da ripassare";
};', 'for (int i = 1; i <= 3; i++) {
    System.out.println("Round " + i);
}

String giudizio = switch (voto) {
    case 10, 9 -> "Excellent";
    case 8, 7, 6 -> "Passed";
    default -> "Revise";
};'), 'java', L('<pre>Giro 1
Giro 2
Giro 3</pre>', '<pre>Round 1
Round 2
Round 3</pre>')),
            P('Lo `switch` con la freccia `->` restituisce un valore e non ha bisogno di `break`.', 'The arrow `->` switch returns a value and needs no `break`.'),
            P('Il `for` ha tre parti separate da punto e virgola: da dove parti (`int i = 1`), fino a quando continui (`i <= 3`) e che cosa cambia a ogni giro (`i++`). `while` controlla la condizione prima di ogni giro, `do { … } while (…)` dopo, quindi gira almeno una volta. Le condizioni si combinano con `&&` (e), `||` (o) e `!` (non). Nel vecchio `switch` con i due punti, invece, senza `break` l’esecuzione «cade» nel caso successivo: un errore classico che la freccia elimina.', 'The `for` has three parts separated by semicolons: where you start (`int i = 1`), how long you keep going (`i <= 3`) and what changes each round (`i++`). `while` checks the condition before every round, `do { … } while (…)` after, so it runs at least once. Conditions combine with `&&` (and), `||` (or) and `!` (not). In the old colon `switch`, instead, without `break` execution “falls through” into the next case: a classic mistake that the arrow removes.'),
        ], [
            Q('c1', L('Quante volte gira `for (int i = 0; i < 3; i++)`?', 'How many times does `for (int i = 0; i < 3; i++)` run?'), ['3', '2', '4', '0'], 0, L('i = 0, 1, 2.', 'i = 0, 1, 2.')),
            Q('c2', L('Lo `switch` con `->` ha bisogno di `break`?', 'Does the `->` switch need `break`?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo nell’ultimo caso', 'Only in the last case'), L('Solo con le stringhe', 'Only with strings')], 0, L('Ogni caso è separato.', 'Each case is separate.')),
            Q('c3', L('Quale operatore significa “e”?', 'Which operator means “and”?'), ['&&', 'and', '!', '||'], 0, L('`||` significa “o” e `!` significa “non”.', '`||` means “or” and `!` means “not”.')),
        ], task('java', L('Con un `for` da 1 a 5 stampa ogni numero.', 'With a `for` from 1 to 5 print each number.'), '', [re('for\s*\(\s*int\s+\w+\s*=\s*1\s*;\s*\w+\s*<=\s*5\s*;\s*\w+\+\+\s*\)', L('Il ciclo è giusto', 'The loop is right')), re('System\.out\.println\s*\(\s*\w+\s*\)', L('Stampi il numero', 'You print the number'))], '', 'for (int i = 1; i <= 5; i++) {
    System.out.println(i);
}'), [
            dBlanks(pFill(), "___ (int i = 0; i ___ 10; i++) {\n    ___ (i % 2 == 0) System.out.println(i);\n}", ['for', '<', 'if'], ['while', '=<', 'when'], L('Ciclo, limite, condizione.', 'Loop, limit, condition.')),
            dOrder(pSort(), ['int n = 3;', 'while (n > 0) {', '    System.out.println(n);', '    n--;', '}'], L('Prepara, controlla, agisci, avanza.', 'Set up, check, act, advance.')),
        ]),
        lesson('arrays', L('Array e liste', 'Arrays and lists'), [
            P('Un **array** ha una lunghezza fissa: `int[] voti = {7, 8, 9};`. Un `ArrayList` cresce quando serve, con `add` e `get`. Il ciclo **for-each** scorre ogni elemento.', 'An **array** has a fixed length: `int[] voti = {7, 8, 9};`. An `ArrayList` grows as needed, with `add` and `get`. The **for-each** loop walks every item.'),
            C('int[] voti = {7, 8, 9};
System.out.println(voti.length);

List<String> nomi = new ArrayList<>();
nomi.add("Ada");
nomi.add("Bea");
for (String n : nomi) {
    System.out.println(n);
}', 'java'),
            P('Tra le parentesi angolari va il tipo degli elementi: `List<String>`. Gli indici partono da 0.', 'The type of the items goes between angle brackets: `List<String>`. Indexes start from 0.'),
            P('Leggere `voti[3]` in un array di tre elementi lancia `ArrayIndexOutOfBoundsException`: l’ultimo indice è sempre `length - 1`. Una lista ha `size()` invece di `length`, e metodi come `get(i)`, `remove(i)` e `contains(x)`. Tra le parentesi angolari ci vanno solo classi, non primitivi: per una lista di interi si scrive `List<Integer>`, e Java converte da solo tra `int` e `Integer`. `List.of("a", "b")` crea invece una lista che non si può modificare.', 'Reading `voti[3]` in a three-item array throws `ArrayIndexOutOfBoundsException`: the last index is always `length - 1`. A list has `size()` instead of `length`, and methods such as `get(i)`, `remove(i)` and `contains(x)`. Only classes go between the angle brackets, not primitives: for a list of integers you write `List<Integer>`, and Java converts between `int` and `Integer` by itself. `List.of("a", "b")`, instead, creates a list that cannot be changed.'),
        ], [
            Q('a1', L('Come aggiungi a un `ArrayList`?', 'How do you add to an `ArrayList`?'), ['add()', 'push()', 'append()', 'insert()'], 0, L('E `get(i)` per leggere.', 'And `get(i)` to read.')),
            Q('a2', L('Come leggi la lunghezza di un array?', 'How do you read an array’s length?'), ['voti.length', 'voti.size()', 'len(voti)', 'voti.count'], 0, L('Per le liste è `size()`.', 'For lists it is `size()`.')),
            Q('a3', L('Come si scrive il for-each?', 'How do you write the for-each?'), ['for (String n : nomi)', 'for n in nomi', 'foreach (nomi as n)', 'for (n of nomi)'], 0, L('Tipo, nome, due punti, collezione.', 'Type, name, colon, collection.')),
        ], task('java', L('Crea `List<String> corsi = new ArrayList<>();` e aggiungi `"Java"`.', 'Create `List<String> corsi = new ArrayList<>();` and add `"Java"`.'), '', [re('List<String>\s+corsi\s*=\s*new\s+ArrayList<>\s*\(\s*\)\s*;', L('La lista esiste', 'The list exists')), re('corsi\.add\s*\(\s*"Java"\s*\)\s*;', L('Aggiungi “Java”', 'You add “Java”'))], '', 'List<String> corsi = new ArrayList<>();
corsi.add("Java");'), [
            dMatch(pMatch(), [['int[]', L('Array di interi', 'Array of ints')], ['ArrayList', L('Lista che cresce', 'List that grows')], ['.length', L('Lunghezza di un array', 'Length of an array')], ['.size()', L('Lunghezza di una lista', 'Length of a list')]], L('Array e liste.', 'Arrays and lists.')),
            dBug(pBug(), ['int[] numeri = {1, 2, 3};', 'for (int i = 0; i <= numeri.length; i++) {', '    System.out.println(numeri[i]);', '}'], 1, 'for (int i = 0; i < numeri.length; i++) {', L('L’ultimo indice è `length - 1`: con `<=` si esce dall’array.', 'The last index is `length - 1`: `<=` goes past the array.')),
        ]),
        lesson('methods', L('Metodi', 'Methods'), [
            P('Un **metodo** è un blocco di codice con nome, parametri tipizzati e un tipo di ritorno (`void` se non restituisce nulla). I metodi `static` si chiamano dalla classe, senza creare oggetti.', 'A **method** is a named block of code with typed parameters and a return type (`void` if it returns nothing). `static` methods are called on the class, without creating objects.'),
            C(L('public class Calcoli {
    static int quadrato(int n) {
        return n * n;
    }

    static void saluta(String nome) {
        System.out.println("Ciao " + nome);
    }

    public static void main(String[] args) {
        saluta("Ada");
        System.out.println(quadrato(4));
    }
}', 'public class Calcoli {
    static int quadrato(int n) {
        return n * n;
    }

    static void saluta(String nome) {
        System.out.println("Hello " + nome);
    }

    public static void main(String[] args) {
        saluta("Ada");
        System.out.println(quadrato(4));
    }
}'), 'java', L('<pre>Ciao Ada
16</pre>', '<pre>Hello Ada
16</pre>')),
            P('Due metodi possono avere lo stesso nome con parametri diversi: è l’**overloading**.', 'Two methods can share a name with different parameters: it is **overloading**.'),
            P('Ogni chiamata mette un nuovo blocco sulla **pila delle chiamate**, con i suoi parametri e le sue variabili; `return` lo toglie e consegna il risultato a chi ha chiamato. Java passa gli argomenti **per valore**: il metodo riceve una copia, quindi cambiare `n` dentro `quadrato` non cambia la variabile di chi l’ha chiamato. Il tipo del `return` deve corrispondere a quello dichiarato, e un metodo non `void` deve restituire qualcosa su ogni strada.', 'Every call puts a new block on the **call stack**, with its parameters and variables; `return` removes it and hands the result to the caller. Java passes arguments **by value**: the method gets a copy, so changing `n` inside `quadrato` does not change the caller’s variable. The type in `return` must match the declared one, and a non-`void` method must return something on every path.'),
            Fig(L(fig_javabase_stack(['pila delle chiamate', 'Ogni chiamata si appoggia sopra main; return la toglie e consegna il risultato.']), fig_javabase_stack(['call stack', 'Each call sits on top of main; return removes it and hands back the result.'])),
                L('Due istantanee della pila. Nella prima, sopra main c’è saluta con nome = "Ada". Nella seconda, sopra main c’è quadrato con n = 4, e una freccia verde riporta 16 a main.', 'Two snapshots of the stack. In the first, saluta with nome = "Ada" sits on top of main. In the second, quadrato with n = 4 sits on top of main, and a green arrow brings 16 back to main.'),
                L('Il metodo in cima è quello che sta lavorando adesso.', 'The method on top is the one working right now.')),
        ], [
            Q('f1', L('Che tipo di ritorno ha un metodo che non restituisce nulla?', 'What return type does a method returning nothing have?'), ['void', 'null', 'empty', 'none'], 0, L('Niente `return` di un valore.', 'No `return` of a value.')),
            Q('f2', L('Che cos’è l’overloading?', 'What is overloading?'), [L('Stesso nome, parametri diversi', 'Same name, different parameters'), L('Un errore di memoria', 'A memory error'), L('Un metodo troppo lungo', 'A method that is too long'), L('Un ciclo infinito', 'An endless loop')], 0, L('Il compilatore sceglie quello giusto.', 'The compiler picks the right one.')),
            Q('f3', L('Un metodo `static` richiede un oggetto?', 'Does a `static` method need an object?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo se è pubblico', 'Only if public'), L('Solo nei test', 'Only in tests')], 0, L('Si chiama dalla classe.', 'It is called on the class.')),
        ], task('java', L('Scrivi `static int doppio(int n)` che restituisce `n * 2`.', 'Write `static int doppio(int n)` that returns `n * 2`.'), '', [re('static\s+int\s+doppio\s*\(\s*int\s+n\s*\)', L('La firma è giusta', 'The signature is right')), re('return\s+n\s*\*\s*2\s*;', L('Restituisci il doppio', 'You return the double'))], '', 'static int doppio(int n) {
    return n * 2;
}'), [
            dBlanks(pFill(), "static ___ media(int a, int b) {\n    ___ (a + b) / 2.0;\n}\nstatic ___ stampa(String s) {\n    System.out.println(s);\n}", ['double', 'return', 'void'], ['int', 'print', 'null'], L('Tipo di ritorno, restituzione, niente.', 'Return type, return, nothing.')),
            dFlash(pFlash(), [[L('I parametri hanno un tipo', 'Parameters have a type'), true], [L('Un metodo `void` restituisce un valore', 'A `void` method returns a value'), false], [L('Si possono avere due metodi con lo stesso nome', 'You can have two methods with the same name'), true], [L('Un metodo può restituire un solo valore alla volta', 'A method can return one value at a time'), true]], L('`void` vuol dire “niente”.', '`void` means “nothing”.')),
        ]),
        lesson('strings', L('Stringhe e StringBuilder', 'Strings and StringBuilder'), [
            P('`String` ha tanti metodi utili: `length`, `toUpperCase`, `substring`, `contains`, `charAt`, `split`, `strip`. `String.format` costruisce un testo con dei segnaposto. I **text block** tra tre virgolette `"""` scrivono testo su più righe.', '`String` has many useful methods: `length`, `toUpperCase`, `substring`, `contains`, `charAt`, `split`, `strip`. `String.format` builds text with placeholders. **Text blocks** between triple quotes `"""` write text across several lines.'),
            C('String nome = "  Ada Lovelace  ".strip();
System.out.println(nome.length());          // 12
System.out.println(nome.toUpperCase());     // ADA LOVELACE
System.out.println(nome.substring(0, 3));   // Ada
System.out.println(nome.contains("Love"));  // true
System.out.println(nome.charAt(4));         // L
String[] parti = nome.split(" ");           // ["Ada", "Lovelace"]

StringBuilder sb = new StringBuilder();
for (int i = 1; i <= 3; i++) sb.append(i).append(" ");
System.out.println(sb);                     // 1 2 3

String json = """
    {"nome": "Ada"}
    """;
System.out.println(String.format("%s ha %d anni", "Ada", 36));', 'java'),
            P('Le stringhe sono **immutabili**: `toUpperCase` ne crea una nuova. Per costruire un testo in un ciclo usa `StringBuilder`, che non crea una copia a ogni passo. In `substring(inizio, fine)` la fine è esclusa.', 'Strings are **immutable**: `toUpperCase` creates a new one. To build text in a loop use `StringBuilder`, which does not make a copy at every step. In `substring(start, end)` the end is excluded.'),
        ], [
            Q('s1', L('Che cosa dà `"Java".substring(1, 3)`?', 'What does `"Java".substring(1, 3)` give?'), ['"Ja"', '"av"', '"ava"', '"jav"'], 1, L('Le posizioni 1 e 2: la 3 è esclusa.', 'Positions 1 and 2: 3 is excluded.')),
            Q('s2', L('Perché usare `StringBuilder` in un ciclo?', 'Why use `StringBuilder` in a loop?'), [L('Perché String non ha il metodo concat', 'Because String has no concat method'), L('Rende la stringa immutabile', 'It makes the string immutable'), L('Converte i numeri in testo', 'It converts numbers to text'), L('Evita di creare una nuova stringa a ogni aggiunta', 'It avoids creating a new string at every addition')], 3, L('Con `+` in un ciclo nasce una stringa nuova a ogni giro.', 'With `+` in a loop a new string is born every round.')),
            Q('s3', L('Che cosa restituisce `"ciao".charAt(0)`?', 'What does `"ciao".charAt(0)` return?'), [L('Il carattere c, di tipo char', 'The character c, of type char'), L('La stringa "c"', 'The string "c"'), L('Il numero 0', 'The number 0'), L('La stringa intera', 'The whole string')], 0, L('`charAt` restituisce un `char`, non una `String`.', '`charAt` returns a `char`, not a `String`.')),
        ], task('java', L('Con uno `StringBuilder` chiamato `sb` aggiungi `"Ciao"` e `" mondo"` con `append`, poi stampa `sb.toString()`.', 'With a `StringBuilder` named `sb` append `"Ciao"` and `" mondo"`, then print `sb.toString()`.'), '', [re('StringBuilder\s+sb\s*=\s*new\s+StringBuilder\s*\(\s*\)', L('Crei lo StringBuilder', 'You create the StringBuilder')), re('sb\.append\s*\(\s*"Ciao"\s*\)', L('Aggiungi “Ciao”', 'You append “Ciao”')), re('\.append\s*\(\s*" mondo"\s*\)', L('Aggiungi “ mondo”', 'You append “ mondo”')), re('System\.out\.println\s*\(\s*sb(\.toString\s*\(\s*\))?\s*\)', L('Stampi il risultato', 'You print the result'))], '', 'StringBuilder sb = new StringBuilder();
sb.append("Ciao");
sb.append(" mondo");
System.out.println(sb.toString());'), [
            dMatch(pMatch(), [['length()', L('Quanti caratteri', 'How many characters')], ['substring()', L('Un pezzo di stringa', 'Part of a string')], ['contains()', L('Contiene il testo?', 'Contains the text?')], ['strip()', L('Toglie gli spazi ai lati', 'Trims spaces at the ends')], ['split()', L('Divide in un array', 'Splits into an array')]], L('I metodi più usati di String.', 'The most used String methods.')),
            dFlash(pFlash(), [[L('`String` è immutabile', '`String` is immutable'), true], [L('`"abc".length()` vale 3', '`"abc".length()` is 3'), true], [L('`substring(0, 2)` include il carattere in posizione 2', '`substring(0, 2)` includes the character at position 2'), false], [L('I text block si scrivono tra tre apici singoli', 'Text blocks are written between three single quotes'), false]], L('Inizio incluso, fine esclusa.', 'Start included, end excluded.')),
        ]),
        lesson('math-input', L('Numeri, conversioni e input', 'Numbers, conversions and input'), [
            P('Tra due `int` la divisione è **intera**: `7 / 2` fa `3`. Per avere i decimali converti uno dei due con il **cast** `(double)`. `%` dà il resto. La classe `Math` offre `max`, `min`, `sqrt`, `pow`, `round`. `Scanner` legge ciò che l’utente scrive.', 'Between two `int` values division is **integer**: `7 / 2` is `3`. To get decimals convert one of them with the **cast** `(double)`. `%` gives the remainder. The `Math` class offers `max`, `min`, `sqrt`, `pow`, `round`. `Scanner` reads what the user types.'),
            C('import java.util.Scanner;

public class Numeri {
    public static void main(String[] args) {
        Scanner in = new Scanner(System.in);
        System.out.print("Quanti anni hai? ");
        int anni = in.nextInt();

        int a = 7, b = 2;
        System.out.println(a / b);            // 3: divisione tra interi
        System.out.println((double) a / b);   // 3.5
        System.out.println(a % b);            // 1
        System.out.println(Math.max(a, b) + " " + Math.sqrt(16) + " " + Math.round(2.6));   // 7 4.0 3

        int n = Integer.parseInt("42");
        String testo = String.valueOf(3.14);
    }
}', 'java'),
            P('`Integer.parseInt` trasforma un testo in numero e lancia `NumberFormatException` se il testo non è un numero. `String.valueOf` fa il contrario.', '`Integer.parseInt` turns text into a number and throws `NumberFormatException` if the text is not a number. `String.valueOf` does the opposite.'),
        ], [
            Q('n1', L('Quanto fa `7 / 2` tra due int?', 'What is `7 / 2` between two ints?'), ['3.5', '4', '3', '3.0'], 2, L('La parte decimale viene scartata.', 'The decimal part is dropped.')),
            Q('n2', L('Come converti `"42"` in int?', 'How do you convert `"42"` to an int?'), ['(int) "42"', 'Integer.parseInt("42")', '"42".toInt()', 'int("42")'], 1, L('Il cast non funziona tra String e int.', 'Casting does not work between String and int.')),
            Q('n3', L('Quanto fa `10 % 3`?', 'What is `10 % 3`?'), ['1', '3', '0', '3.33'], 0, L('10 = 3 × 3 + 1.', '10 = 3 × 3 + 1.')),
        ], task('java', L('Calcola in `double media` la media di `somma` e `n` (entrambi int) senza perdere i decimali, usando il cast.', 'Compute in `double media` the average of `somma` and `n` (both int) without losing decimals, using a cast.'), 'int somma = 17;
int n = 4;
', [re('double\s+media\s*=\s*(\(\s*double\s*\)\s*somma\s*/\s*n|somma\s*/\s*\(\s*double\s*\)\s*n|\(\s*double\s*\)\s*somma\s*/\s*\(\s*double\s*\)\s*n)\s*;', L('La media ha i decimali', 'The average keeps decimals'))], '', 'double media = (double) somma / n;'), [
            dBug(pBug(), ['int pezzi = 5;', 'int persone = 2;', 'double aTesta = pezzi / persone;', 'System.out.println(aTesta); // voglio 2.5'], 2, 'double aTesta = (double) pezzi / persone;', L('Prima avviene la divisione tra interi (2), poi la conversione: il cast va messo prima.', 'Integer division happens first (2), then the conversion: the cast must come first.')),
            dMatch(pMatch(), [['Math.max', L('Il più grande', 'The larger')], ['Math.sqrt', L('Radice quadrata', 'Square root')], ['Math.round', L('Arrotonda', 'Rounds')], ['Integer.parseInt', L('Testo in intero', 'Text to int')], ['%', L('Resto della divisione', 'Remainder')]], L('Numeri in Java.', 'Numbers in Java.')),
        ]),
        lesson('arrays-tools', L('Matrici e la classe Arrays', 'Matrices and the Arrays class'), [
            P('La classe `Arrays` offre attrezzi pronti: `sort` ordina, `toString` trasforma in testo leggibile, `copyOf` copia, `fill` riempie. Un array di array è una **matrice**: il primo indice sceglie la riga, il secondo la colonna.', 'The `Arrays` class offers ready-made tools: `sort` sorts, `toString` turns into readable text, `copyOf` copies, `fill` fills. An array of arrays is a **matrix**: the first index picks the row, the second the column.'),
            C('import java.util.Arrays;

int[] voti = {8, 5, 9, 7};
Arrays.sort(voti);
System.out.println(Arrays.toString(voti));   // [5, 7, 8, 9]
int[] copia = Arrays.copyOf(voti, 6);        // [5, 7, 8, 9, 0, 0]
int somma = Arrays.stream(voti).sum();       // 29

int[][] griglia = new int[3][4];             // 3 righe, 4 colonne
griglia[1][2] = 5;
for (int[] riga : griglia) {
    System.out.println(Arrays.toString(riga));
}', 'java', '<pre>[5, 7, 8, 9]
[0, 0, 0, 0]
[0, 0, 5, 0]
[0, 0, 0, 0]</pre>'),
            P('Stampare un array con `println` mostra solo un codice strano come `[I@1b6d3586`: usa `Arrays.toString`. In un nuovo array di numeri ogni elemento vale `0`, in uno di oggetti vale `null`.', 'Printing an array with `println` shows only an odd code such as `[I@1b6d3586`: use `Arrays.toString`. In a new array of numbers every item is `0`, in one of objects it is `null`.'),
            Fig(fig_javabase_grid(),
                L('Una griglia di 3 righe e 4 colonne piena di zeri, con gli indici delle colonne da 0 a 3 in alto e quelli delle righe da 0 a 2 a sinistra. La cella alla riga 1, colonna 2 vale 5 ed è evidenziata. A fianco: griglia.length vale 3, griglia[0].length vale 4.', 'A grid of 3 rows and 4 columns full of zeros, with column indexes 0 to 3 at the top and row indexes 0 to 2 on the left. The cell at row 1, column 2 holds 5 and is highlighted. Beside it: griglia.length is 3, griglia[0].length is 4.'),
                L('Prima la riga, poi la colonna.', 'Row first, then column.')),
        ], [
            Q('ar1', L('Che cosa stampa `Arrays.toString(new int[]{3, 1})`?', 'What does `Arrays.toString(new int[]{3, 1})` print?'), ['[3, 1]', '[1, 3]', '{3, 1}', '31'], 0, L('`toString` non ordina: mostra gli elementi così come sono.', '`toString` does not sort: it shows the items as they are.')),
            Q('ar2', L('In `new int[3][4]`, quante colonne ha ogni riga?', 'In `new int[3][4]`, how many columns does each row have?'), ['3', '12', '4', '7'], 2, L('Il primo numero conta le righe, il secondo le colonne.', 'The first number counts rows, the second columns.')),
            Q('ar3', L('Che cosa fa `Arrays.sort(voti)`?', 'What does `Arrays.sort(voti)` do?'), [L('Restituisce una copia ordinata', 'Returns a sorted copy'), L('Ordina l’array stesso, dal più piccolo', 'Sorts the array itself, smallest first'), L('Ordina dal più grande al più piccolo', 'Sorts from largest to smallest'), L('Toglie i doppioni', 'Removes duplicates')], 1, L('Non restituisce nulla: cambia l’array.', 'It returns nothing: it changes the array.')),
        ], task('java', L('Ordina `numeri` con `Arrays.sort` e stampalo con `Arrays.toString`.', 'Sort `numeri` with `Arrays.sort` and print it with `Arrays.toString`.'), 'int[] numeri = {4, 2, 9};
', [re('Arrays\.sort\s*\(\s*numeri\s*\)', L('Ordini l’array', 'You sort the array')), re('System\.out\.println\s*\(\s*Arrays\.toString\s*\(\s*numeri\s*\)\s*\)', L('Lo stampi leggibile', 'You print it readably'))], '', 'Arrays.sort(numeri);
System.out.println(Arrays.toString(numeri));'), [
            dBlanks(pFill(), 'int[][] tabella = new int[2][3];
tabella[0][1] = 7;
for (int[] riga ___ tabella) {
    System.out.println(Arrays.___(riga));
}
Arrays.___(tabella[0]);', [':', 'toString', 'sort'], ['in', 'print', 'order'], L('For-each, stampa leggibile, ordinamento.', 'For-each, readable print, sorting.')),
            dFlash(pFlash(), [[L('Un nuovo `int[]` è pieno di zeri', 'A new `int[]` is full of zeros'), true], [L('`println(array)` stampa gli elementi', '`println(array)` prints the items'), false], [L('`griglia[0][1]` è riga 0, colonna 1', '`griglia[0][1]` is row 0, column 1'), true], [L('Un array può cambiare lunghezza dopo la creazione', 'An array can change length after creation'), false]], L('Per la lunghezza variabile c’è `ArrayList`.', 'For a variable length there is `ArrayList`.')),
        ]),
        lesson('compact', L('Programmi compatti e JShell', 'Compact programs and JShell'), [
            P('Da **Java 25** (JEP 512) un piccolo programma non ha bisogno di dichiarare la classe né di `public static void main(String[] args)`: basta un metodo `void main()`. La classe `IO` (in `java.lang`, senza import) offre `println`, `print` e `readln`, e le classi principali come `List` sono già importate.', 'Since **Java 25** (JEP 512) a small program needs neither a class declaration nor `public static void main(String[] args)`: a `void main()` method is enough. The `IO` class (in `java.lang`, no import) offers `println`, `print` and `readln`, and the main classes such as `List` are already imported.'),
            C('// Saluto.java: un file sorgente compatto (Java 25)
void main() {
    String nome = IO.readln("Come ti chiami? ");
    IO.println("Ciao, " + nome + "!");
    List<String> corsi = List.of("Java", "Go");
    IO.println(corsi);
}', 'java'),
            C('java Saluto.java          # compila ed esegue in un colpo
jshell                    # prova righe di Java al volo
jshell> 7 / 2
$1 ==> 3', 'bash'),
            P('**JShell** esegue un’istruzione alla volta e mostra subito il risultato: perfetto per provare un metodo prima di usarlo. Per i progetti veri si torna alle classi normali, ma per imparare e per gli script i file compatti sono comodissimi.', '**JShell** runs one statement at a time and shows the result straight away: perfect for trying a method before using it. Real projects go back to normal classes, but for learning and scripts compact files are very handy.'),
        ], [
            Q('cp1', L('Che cosa serve, al minimo, in un file sorgente compatto di Java 25?', 'What does a Java 25 compact source file need at minimum?'), [L('public static void main(String[] args) in una classe', 'public static void main(String[] args) in a class'), 'void main()', L('Una classe con @Main', 'A class with @Main'), L('Un metodo start()', 'A start() method')], 1, L('La classe viene dichiarata implicitamente.', 'The class is declared implicitly.')),
            Q('cp2', L('Quale metodo di `IO` legge una riga dal terminale?', 'Which `IO` method reads a line from the terminal?'), ['IO.input()', 'IO.scan()', 'IO.nextLine()', 'IO.readln()'], 3, L('Può ricevere la domanda da mostrare.', 'It can take the prompt to show.')),
            Q('cp3', L('A che cosa serve JShell?', 'What is JShell for?'), [L('A compilare progetti grandi', 'Compiling big projects'), L('A creare file JAR', 'Creating JAR files'), L('A provare istruzioni Java una alla volta', 'Trying Java statements one at a time'), L('A installare librerie', 'Installing libraries')], 2, L('Si chiude con `/exit`.', 'You leave it with `/exit`.')),
        ], task('java', L('Scrivi un programma compatto: un `void main()` che stampa `"Ciao da Java"` con `IO.println`.', 'Write a compact program: a `void main()` that prints `"Ciao da Java"` with `IO.println`.'), '', [re('^\s*void\s+main\s*\(\s*\)\s*\{', L('C’è il main compatto', 'There is the compact main')), re('IO\.println\s*\(\s*"Ciao da Java"\s*\)\s*;', L('Stampi il saluto', 'You print the greeting'))], '', 'void main() {
    IO.println("Ciao da Java");
}'), [
            dOrder(pSort(), ['void main() {', '    String nome = IO.readln("Nome? ");', '    IO.println("Ciao " + nome);', '}'], L('Metodo, lettura, stampa, chiusura.', 'Method, read, print, close.')),
            dMatch(pMatch(), [['void main()', L('Punto di partenza compatto', 'Compact entry point')], ['IO.println', L('Scrive una riga', 'Writes a line')], ['IO.readln', L('Legge una riga', 'Reads a line')], ['java File.java', L('Esegue senza compilare a parte', 'Runs without a separate compile')], ['jshell', L('Prove al volo', 'Quick experiments')]], L('Java senza cerimonie.', 'Java without ceremony.')),
        ]),
    ],
];
