<?php
/** Java basics: the main method, types, conditions and loops, arrays and methods. */

return [
    'id' => 'java-base', 'family' => 'java', 'after' => null,
    'title' => L('Java base', 'Java Basics'), 'short' => L('Java base', 'Java Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di Android e dei grandi server: `main`, tipi, condizioni, cicli, array e metodi.', 'The language of Android and big servers: `main`, types, conditions, loops, arrays and methods.'),
    'lessons' => [
        lesson('main', L('Il primo programma', 'The first program'), [
            P('Ogni programma Java parte dal metodo `main` dentro una **classe**. `System.out.println` scrive una riga. Ogni istruzione finisce con il punto e virgola.', 'Every Java program starts from the `main` method inside a **class**. `System.out.println` writes a line. Every statement ends with a semicolon.'),
            C('public class Ciao {
    public static void main(String[] args) {
        System.out.println("Ciao, Java!");
    }
}', 'java'),
            P('Il file deve chiamarsi come la classe pubblica: `Ciao.java`. Si compila e si esegue con `java Ciao.java`.', 'The file must be named like the public class: `Ciao.java`. You compile and run it with `java Ciao.java`.'),
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
        ], [
            Q('t1', L('Quale tipo per un prezzo con decimali?', 'Which type for a price with decimals?'), ['double', 'int', 'boolean', 'char'], 0, L('`int` tiene solo interi.', '`int` holds only whole numbers.')),
            Q('t2', L('Come confronti due stringhe?', 'How do you compare two strings?'), ['a.equals(b)', 'a == b', 'a === b', 'compare(a, b)'], 0, L('`==` confronta i riferimenti.', '`==` compares references.')),
            Q('t3', L('Che cosa fa `final`?', 'What does `final` do?'), [L('Rende il valore non modificabile', 'Makes the value unchangeable'), L('Chiude il programma', 'Ends the program'), L('Rende pubblico', 'Makes it public'), L('Niente', 'Nothing')], 0, L('Assegnato una volta sola.', 'Assigned only once.')),
        ], task('java', L('Dichiara `int livelli = 6;` e `String corso = "Java";`.', 'Declare `int livelli = 6;` and `String corso = "Java";`.'), '', [re('int\s+livelli\s*=\s*6\s*;', L('C’è l’intero', 'There is the int')), re('String\s+corso\s*=\s*"Java"\s*;', L('C’è la stringa', 'There is the string'))], '', 'int livelli = 6;
String corso = "Java";'), [
            dMatch(pMatch(), [['int', L('Numero intero', 'Whole number')], ['double', L('Numero decimale', 'Decimal number')], ['boolean', L('Vero o falso', 'True or false')], ['char', L('Un carattere', 'One character')], ['String', L('Testo', 'Text')]], L('I tipi di base.', 'The basic types.')),
            dFlash(pFlash(), [[L('In Java ogni variabile ha un tipo', 'In Java every variable has a type'), true], [L('`==` è il modo giusto di confrontare stringhe', '`==` is the right way to compare strings'), false], [L('`var` deduce il tipo', '`var` infers the type'), true]], L('Per le stringhe c’è `equals`.', 'Strings use `equals`.')),
        ]),
        lesson('control', L('Condizioni e cicli', 'Conditions and loops'), [
            P('`if`/`else` sceglie, `switch` confronta un valore con molti casi, `for` e `while` ripetono. I blocchi stanno tra graffe.', '`if`/`else` chooses, `switch` compares one value with many cases, `for` and `while` repeat. Blocks sit between braces.'),
            C('for (int i = 1; i <= 3; i++) {
    System.out.println("Giro " + i);
}

String giudizio = switch (voto) {
    case 10, 9 -> "Ottimo";
    case 8, 7, 6 -> "Promosso";
    default -> "Da ripassare";
};', 'java'),
            P('Lo `switch` con la freccia `->` restituisce un valore e non ha bisogno di `break`.', 'The arrow `->` switch returns a value and needs no `break`.'),
        ], [
            Q('c1', L('Quante volte gira `for (int i = 0; i < 3; i++)`?', 'How many times does `for (int i = 0; i < 3; i++)` run?'), ['3', '2', '4', '0'], 0, L('i = 0, 1, 2.', 'i = 0, 1, 2.')),
            Q('c2', L('Lo `switch` con `->` ha bisogno di `break`?', 'Does the `->` switch need `break`?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo nell’ultimo caso', 'Only in the last case'), L('Solo con le stringhe', 'Only with strings')], 0, L('Ogni caso è separato.', 'Each case is separate.')),
            Q('c3', L('Quale operatore significa “e”?', 'Which operator means “and”?'), ['&&', 'and', '&', '||'], 0, L('`||` significa “o”.', '`||` means “or”.')),
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
            C('public class Calcoli {
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
}', 'java'),
            P('Due metodi possono avere lo stesso nome con parametri diversi: è l’**overloading**.', 'Two methods can share a name with different parameters: it is **overloading**.'),
        ], [
            Q('f1', L('Che tipo di ritorno ha un metodo che non restituisce nulla?', 'What return type does a method returning nothing have?'), ['void', 'null', 'empty', 'none'], 0, L('Niente `return` di un valore.', 'No `return` of a value.')),
            Q('f2', L('Che cos’è l’overloading?', 'What is overloading?'), [L('Stesso nome, parametri diversi', 'Same name, different parameters'), L('Un errore di memoria', 'A memory error'), L('Un metodo troppo lungo', 'A method that is too long'), L('Un ciclo infinito', 'An endless loop')], 0, L('Il compilatore sceglie quello giusto.', 'The compiler picks the right one.')),
            Q('f3', L('Un metodo `static` richiede un oggetto?', 'Does a `static` method need an object?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo se è pubblico', 'Only if public'), L('Solo nei test', 'Only in tests')], 0, L('Si chiama dalla classe.', 'It is called on the class.')),
        ], task('java', L('Scrivi `static int doppio(int n)` che restituisce `n * 2`.', 'Write `static int doppio(int n)` that returns `n * 2`.'), '', [re('static\s+int\s+doppio\s*\(\s*int\s+n\s*\)', L('La firma è giusta', 'The signature is right')), re('return\s+n\s*\*\s*2\s*;', L('Restituisci il doppio', 'You return the double'))], '', 'static int doppio(int n) {
    return n * 2;
}'), [
            dBlanks(pFill(), "static ___ media(int a, int b) {\n    ___ (a + b) / 2.0;\n}\nstatic ___ stampa(String s) {\n    System.out.println(s);\n}", ['double', 'return', 'void'], ['int', 'print', 'null'], L('Tipo di ritorno, restituzione, niente.', 'Return type, return, nothing.')),
            dFlash(pFlash(), [[L('I parametri hanno un tipo', 'Parameters have a type'), true], [L('Un metodo `void` restituisce un valore', 'A `void` method returns a value'), false], [L('Si possono avere due metodi con lo stesso nome', 'You can have two methods with the same name'), true]], L('`void` vuol dire “niente”.', '`void` means “nothing”.')),
        ]),
    ],
];
