<?php
/** Java basics: the main method, types, conditions and loops, arrays and methods, strings, numbers and input, the Arrays class, compact programs (Java 25). */

return [
    'id' => 'java-base', 'family' => 'java', 'after' => null,
    'title' => L('Java base', 'Java Basics'), 'short' => L('Java base', 'Java Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di Android e dei grandi server: `main`, tipi, condizioni, cicli, array e metodi, stringhe, numeri e input, programmi compatti di Java 25.', 'The language of Android and big servers: `main`, types, conditions, loops, arrays and methods, strings, numbers and input, Java 25 compact programs.'),
    'lessons' => [
        lesson('main', L('Il primo programma', 'The first program'), [
            P('Ogni programma Java parte dal metodo `main` dentro una **classe**. `System.out.println` scrive una riga. Ogni istruzione finisce con il punto e virgola.', 'Every Java program starts from the `main` method inside a **class**. `System.out.println` writes a line. Every statement ends with a semicolon.'),
            C('public class Ciao {
    public static void main(String[] args) {
        System.out.println("Ciao, Java!");
    }
}', 'java'),
            P('Il file deve chiamarsi come la classe pubblica: `Ciao.java`. `javac Ciao.java` lo compila e `java Ciao` lo esegue; per un programma di un solo file basta `java Ciao.java`, che fa tutto in un colpo.', 'The file must be named like the public class: `Ciao.java`. `javac Ciao.java` compiles it and `java Ciao` runs it; for a single-file program `java Ciao.java` does both in one go.'),
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
            dFlash(pFlash(), [[L('In Java ogni variabile ha un tipo', 'In Java every variable has a type'), true], [L('`==` è il modo giusto di confrontare stringhe', '`==` is the right way to compare strings'), false], [L('`var` deduce il tipo', '`var` infers the type'), true], [L('`int` può contenere numeri con la virgola', '`int` can hold decimal numbers'), false]], L('Per le stringhe c’è `equals`.', 'Strings use `equals`.')),
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
}', 'java'),
            P('Stampare un array con `println` mostra solo un codice strano come `[I@1b6d3586`: usa `Arrays.toString`. In un nuovo array di numeri ogni elemento vale `0`, in uno di oggetti vale `null`.', 'Printing an array with `println` shows only an odd code such as `[I@1b6d3586`: use `Arrays.toString`. In a new array of numbers every item is `0`, in one of objects it is `null`.'),
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
