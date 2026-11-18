<?php
/** Java intermediate: classes and objects, inheritance, interfaces, collections and exceptions, enums, equality and sorting, files with java.nio, packages and build tools. */

return [
    'id' => 'java-intermediate', 'family' => 'java', 'after' => 'java-base',
    'title' => L('Java intermedio', 'Intermediate Java'), 'short' => L('Java intermedio', 'Java Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Programmazione a oggetti: classi, incapsulamento, ereditarietà, interfacce, collezioni ed eccezioni, enum, uguaglianza e ordinamenti, file, pacchetti, Maven e Gradle.', 'Object-oriented programming: classes, encapsulation, inheritance, interfaces, collections and exceptions, enums, equality and sorting, files, packages, Maven and Gradle.'),
    'lessons' => [
        lesson('classes', L('Classi e oggetti', 'Classes and objects'), [
            P('Una **classe** è lo stampo, un **oggetto** è una copia creata con `new`. I campi `private` si leggono e cambiano solo attraverso metodi pubblici: è l’**incapsulamento**.', 'A **class** is the mould, an **object** is a copy created with `new`. `private` fields are read and changed only through public methods: it is **encapsulation**.'),
            C('public class Conto {
    private double saldo;
    private final String titolare;

    public Conto(String titolare) {
        this.titolare = titolare;
    }

    public void versa(double importo) {
        if (importo > 0) saldo += importo;
    }

    public double getSaldo() { return saldo; }
}

Conto c = new Conto("Ada");
c.versa(50);', 'java'),
            P('Il **costruttore** ha il nome della classe e nessun tipo di ritorno. `this` indica l’oggetto attuale.', 'The **constructor** has the class name and no return type. `this` is the current object.'),
        ], [
            Q('o1', L('Con quale parola crei un oggetto?', 'Which word creates an object?'), ['new', 'create', 'make', 'object'], 0, L('`new Conto("Ada")`.', '`new Conto("Ada")`.')),
            Q('o2', L('Perché i campi sono `private`?', 'Why are fields `private`?'), [L('Per controllare come cambiano', 'To control how they change'), L('Per velocità', 'For speed'), L('Per obbligo del compilatore', 'Because the compiler requires it'), L('Per nasconderli al debugger', 'To hide them from the debugger')], 0, L('Solo i metodi decidono.', 'Only methods decide.')),
            Q('o3', L('Che tipo di ritorno ha un costruttore?', 'What return type does a constructor have?'), [L('Nessuno', 'None'), 'void', 'Object', L('Il tipo della classe', 'The class type')], 0, L('Ha solo il nome della classe.', 'It only has the class name.')),
        ], task('java', L('Crea la classe `Gatto` con il campo `private String nome` e un costruttore che lo imposta.', 'Create the class `Gatto` with the field `private String nome` and a constructor that sets it.'), '', [re('class\s+Gatto', L('La classe esiste', 'The class exists')), re('private\s+String\s+nome\s*;', L('Il campo è privato', 'The field is private')), re('Gatto\s*\(\s*String\s+nome\s*\)\s*\{\s*this\.nome\s*=\s*nome\s*;', L('Il costruttore lo imposta', 'The constructor sets it'))], '', 'public class Gatto {
    private String nome;
    public Gatto(String nome) {
        this.nome = nome;
    }
}'), [
            dBlanks(pFill(), "public class Punto {\n    ___ int x;\n    public ___(int x) {\n        ___.x = x;\n    }\n}\nPunto p = ___ Punto(3);", ['private', 'Punto', 'this', 'new'], ['public', 'void', 'self', 'make'], L('Campo privato, costruttore, oggetto, creazione.', 'Private field, constructor, object, creation.')),
            dMatch(pMatch(), [['private', L('Solo la classe', 'Only the class')], ['public', L('Tutti', 'Everyone')], ['protected', L('Classe e sottoclassi', 'Class and subclasses')], ['static', L('Della classe', 'Of the class')], ['final', L('Non cambia', 'Does not change')]], L('Modificatori di Java.', 'Java modifiers.')),
        ]),
        lesson('inheritance', L('Ereditarietà e polimorfismo', 'Inheritance and polymorphism'), [
            P('Con `extends` una classe eredita campi e metodi da un’altra e può **sovrascriverli** con `@Override`. Una variabile del tipo padre può contenere un figlio: è il **polimorfismo**.', 'With `extends` a class inherits fields and methods from another and can **override** them with `@Override`. A variable of the parent type can hold a child: it is **polymorphism**.'),
            C('abstract class Animale {
    abstract String verso();
}

class Cane extends Animale {
    @Override String verso() { return "Bau"; }
}

class Gatto extends Animale {
    @Override String verso() { return "Miao"; }
}

List<Animale> zoo = List.of(new Cane(), new Gatto());
for (Animale a : zoo) System.out.println(a.verso());', 'java'),
            P('Una classe `abstract` non si istanzia e obbliga i figli a scrivere i metodi astratti. `super` chiama la versione del padre.', 'An `abstract` class cannot be instantiated and forces children to write the abstract methods. `super` calls the parent’s version.'),
        ], [
            Q('i1', L('Quale parola fa ereditare una classe?', 'Which word makes a class inherit?'), ['extends', 'implements', 'inherits', 'super'], 0, L('Una sola classe padre.', 'A single parent class.')),
            Q('i2', L('A che cosa serve `@Override`?', 'What is `@Override` for?'), [L('Segnala che sovrascrivi un metodo', 'It marks that you override a method'), L('Rende il metodo statico', 'Makes the method static'), L('Cancella il metodo', 'Deletes the method'), L('Rende pubblico', 'Makes it public')], 0, L('Il compilatore controlla che il padre lo abbia.', 'The compiler checks the parent has it.')),
            Q('i3', L('Che cosa chiama `super.metodo()`?', 'What does `super.metodo()` call?'), [L('La versione del padre', 'The parent’s version'), L('Un metodo statico', 'A static method'), L('Il costruttore del figlio', 'The child’s constructor'), L('Niente', 'Nothing')], 0, L('Utile per estendere invece di sostituire.', 'Handy to extend instead of replace.')),
        ], task('java', L('Crea `class Auto extends Veicolo` che sovrascrive `String tipo()` restituendo `"auto"`.', 'Create `class Auto extends Veicolo` that overrides `String tipo()` returning `"auto"`.'), '', [re('class\s+Auto\s+extends\s+Veicolo', L('Auto eredita da Veicolo', 'Auto inherits from Veicolo')), re('@Override\s+(public\s+)?String\s+tipo\s*\(\s*\)', L('Il metodo è sovrascritto', 'The method is overridden')), re('return\s+"auto"\s*;', L('Restituisce “auto”', 'It returns “auto”'))], '', 'class Auto extends Veicolo {
    @Override
    String tipo() { return "auto"; }
}'), [
            dOrder(pSort(), ['abstract class Forma {', '    abstract double area();', '}', 'class Quadrato extends Forma {', '    double lato = 2;', '    @Override double area() { return lato * lato; }', '}'], L('Astratta prima, concreta dopo.', 'Abstract first, concrete after.')),
            dFlash(pFlash(), [[L('Una classe può estenderne due', 'A class can extend two'), false], [L('Una classe astratta non si istanzia', 'An abstract class cannot be instantiated'), true], [L('Un `Animale` può contenere un `Cane`', 'An `Animale` can hold a `Cane`'), true], [L('Una classe può implementare più interfacce', 'A class can implement several interfaces'), true]], L('Un solo `extends`.', 'One `extends` only.')),
        ]),
        lesson('interfaces', L('Interfacce e record', 'Interfaces and records'), [
            P('Un’**interfaccia** dichiara che cosa un oggetto sa fare; una classe la rispetta con `implements` e può implementarne molte. Un **record** è una classe di soli dati, immutabile, scritta in una riga.', 'An **interface** declares what an object can do; a class follows it with `implements` and can implement many. A **record** is a data-only, immutable class written in one line.'),
            C('interface Pagabile {
    double totale();
}

record Prodotto(String nome, double prezzo) implements Pagabile {
    public double totale() { return prezzo * 1.22; }
}

var p = new Prodotto("Libro", 10);
System.out.println(p.nome() + " " + p.totale());', 'java'),
            P('Il record crea da solo costruttore, metodi di lettura (`nome()`), `equals`, `hashCode` e `toString`.', 'The record creates the constructor, accessors (`nome()`), `equals`, `hashCode` and `toString` by itself.'),
        ], [
            Q('r1', L('Quante interfacce può implementare una classe?', 'How many interfaces can a class implement?'), [L('Quante vuole', 'As many as it likes'), L('Una', 'One'), L('Due', 'Two'), L('Nessuna', 'None')], 0, L('Separate da virgole.', 'Separated by commas.')),
            Q('r2', L('Come leggi il campo `nome` di un record?', 'How do you read the `nome` field of a record?'), ['p.nome()', 'p.getNome()', 'p.nome', 'p["nome"]'], 0, L('Il metodo ha il nome del campo.', 'The method has the field’s name.')),
            Q('r3', L('Un record si può modificare dopo la creazione?', 'Can a record be changed after creation?'), [L('No, è immutabile', 'No, it is immutable'), L('Sì', 'Yes'), L('Solo i numeri', 'Only numbers'), L('Solo con `set`', 'Only with `set`')], 0, L('I campi sono `final`.', 'Fields are `final`.')),
        ], task('java', L('Crea `record Utente(String nome, int eta) {}`.', 'Create `record Utente(String nome, int eta) {}`.'), '', [re('record\s+Utente\s*\(\s*String\s+nome\s*,\s*int\s+eta\s*\)', L('Il record esiste', 'The record exists'))], '', 'record Utente(String nome, int eta) {}'), [
            dBlanks(pFill(), "___ Stampabile {\n    String stampa();\n}\n___ Nota(String testo) ___ Stampabile {\n    public String stampa() { return testo; }\n}", ['interface', 'record', 'implements'], ['class', 'extends', 'abstract'], L('Contratto, dati, rispetto del contratto.', 'Contract, data, honouring the contract.')),
            dMatch(pMatch(), [['interface', L('Cosa sa fare', 'What it can do')], ['implements', L('Rispetta un’interfaccia', 'Follows an interface')], ['record', L('Classe di soli dati', 'Data-only class')], ['extends', L('Eredita da una classe', 'Inherits from a class')]], L('Interfacce e record.', 'Interfaces and records.')),
        ]),
        lesson('collections', L('Collezioni', 'Collections'), [
            P('Le **collezioni** principali sono `List` (ordinata, con doppioni), `Set` (senza doppioni) e `Map` (chiave → valore). Si dichiara l’interfaccia e si sceglie l’implementazione: `ArrayList`, `HashSet`, `HashMap`.', 'The main **collections** are `List` (ordered, with duplicates), `Set` (no duplicates) and `Map` (key → value). You declare the interface and pick the implementation: `ArrayList`, `HashSet`, `HashMap`.'),
            C('Map<String, Integer> voti = new HashMap<>();
voti.put("Ada", 9);
voti.put("Bea", 8);
System.out.println(voti.get("Ada"));
System.out.println(voti.getOrDefault("Cleo", 0));

Set<String> tag = new HashSet<>(List.of("java", "java", "oop"));
System.out.println(tag.size()); // 2', 'java'),
            P('`getOrDefault` evita il `null` quando la chiave manca. Un `Set` scarta da solo i doppioni.', '`getOrDefault` avoids `null` when the key is missing. A `Set` drops duplicates by itself.'),
        ], [
            Q('k1', L('Quale collezione non ha doppioni?', 'Which collection has no duplicates?'), ['Set', 'List', 'ArrayList', 'Array'], 0, L('Un insieme.', 'A set.')),
            Q('k2', L('Come metti una coppia in una `Map`?', 'How do you put a pair into a `Map`?'), ['put(k, v)', 'add(k, v)', 'set(k, v)', 'push(k, v)'], 0, L('E `get(k)` per leggere.', 'And `get(k)` to read.')),
            Q('k3', L('Che cosa dà `get` di una chiave assente?', 'What does `get` give for a missing key?'), ['null', '0', L('Un errore', 'An error'), '""'], 0, L('Per questo esiste `getOrDefault`.', 'That is why `getOrDefault` exists.')),
        ], task('java', L('Crea `Map<String, Integer> eta = new HashMap<>();` e inserisci `"Ada"` con `36`.', 'Create `Map<String, Integer> eta = new HashMap<>();` and put `"Ada"` with `36`.'), '', [re('Map<String,\s*Integer>\s+eta\s*=\s*new\s+HashMap<>\s*\(\s*\)\s*;', L('La mappa esiste', 'The map exists')), re('eta\.put\s*\(\s*"Ada"\s*,\s*36\s*\)\s*;', L('Inserisci la coppia', 'You insert the pair'))], '', 'Map<String, Integer> eta = new HashMap<>();
eta.put("Ada", 36);'), [
            dMatch(pMatch(), [['List', L('Ordinata, con doppioni', 'Ordered, with duplicates')], ['Set', L('Senza doppioni', 'No duplicates')], ['Map', L('Chiave → valore', 'Key → value')], ['Queue', L('Coda: primo entrato, primo uscito', 'Queue: first in, first out')]], L('Le collezioni di Java.', 'Java’s collections.')),
            dFlash(pFlash(), [[L('`HashMap` mantiene l’ordine di inserimento', '`HashMap` keeps insertion order'), false], [L('`List.of` crea una lista non modificabile', '`List.of` makes an unmodifiable list'), true], [L('Un `Set` scarta i doppioni', 'A `Set` drops duplicates'), true], [L('Un `ArrayList` può crescere dopo essere stato creato', 'An `ArrayList` can grow after it is created'), true]], L('Per l’ordine c’è `LinkedHashMap`.', 'For order there is `LinkedHashMap`.')),
        ]),
        lesson('exceptions', L('Eccezioni', 'Exceptions'), [
            P('Gli errori in Java sono **eccezioni**: si lanciano con `throw` e si catturano con `try`/`catch`. Le eccezioni **controllate** (come `IOException`) vanno gestite o dichiarate con `throws`.', 'Errors in Java are **exceptions**: thrown with `throw` and caught with `try`/`catch`. **Checked** exceptions (like `IOException`) must be handled or declared with `throws`.'),
            C('try (var lettore = Files.newBufferedReader(Path.of("dati.txt"))) {
    System.out.println(lettore.readLine());
} catch (IOException e) {
    System.out.println("Impossibile leggere: " + e.getMessage());
}

static void preleva(double importo) {
    if (importo <= 0) throw new IllegalArgumentException("Importo non valido");
}', 'java'),
            P('Il **try-with-resources** chiude da solo file e connessioni alla fine del blocco.', '**try-with-resources** closes files and connections by itself at the end of the block.'),
        ], [
            Q('e1', L('Con quale parola lanci un’eccezione?', 'Which word throws an exception?'), ['throw', 'raise', 'error', 'catch'], 0, L('`throws` invece la dichiara.', '`throws` declares it instead.')),
            Q('e2', L('Che cosa fa il try-with-resources?', 'What does try-with-resources do?'), [L('Chiude le risorse da solo', 'Closes resources by itself'), L('Riprova all’infinito', 'Retries forever'), L('Ignora gli errori', 'Ignores errors'), L('Crea risorse', 'Creates resources')], 0, L('Le risorse vanno tra le tonde dopo `try`.', 'Resources go in brackets after `try`.')),
            Q('e3', L('Quale eccezione è controllata?', 'Which exception is checked?'), ['IOException', 'NullPointerException', 'IllegalArgumentException', 'ArithmeticException'], 0, L('Le altre sono runtime.', 'The others are runtime ones.')),
        ], task('java', L('Lancia `new IllegalArgumentException("Età negativa")` se `eta < 0`.', 'Throw `new IllegalArgumentException("Età negativa")` if `eta < 0`.'), 'static void setEta(int eta) {
}', [re('if\s*\(\s*eta\s*<\s*0\s*\)\s*\{?\s*throw\s+new\s+IllegalArgumentException\s*\(', L('Lanci l’eccezione giusta', 'You throw the right exception'))], '', 'if (eta < 0) throw new IllegalArgumentException("Età negativa");'), [
            dOrder(pSort(), ['try {', '    int n = Integer.parseInt(testo);', '    System.out.println(n * 2);', '} catch (NumberFormatException e) {', '    System.out.println("Non è un numero");', '}'], L('Prova, usa, cattura.', 'Try, use, catch.')),
            dBug(pBug(), ['try {', '    leggiFile();', '} catch (Exception e) {', '}'], 3, '    System.err.println(e.getMessage()); }', L('Un `catch` vuoto nasconde l’errore: almeno segnalalo.', 'An empty `catch` hides the error: at least report it.')),
        ]),
        lesson('enums', L('Enum', 'Enums'), [
            P('Un **enum** elenca un insieme chiuso di costanti: il compilatore impedisce valori inventati come `"avanzto"`. Un enum può avere campi, un costruttore e metodi. `values()` dà tutte le costanti, `valueOf` trova quella con un certo nome.', 'An **enum** lists a closed set of constants: the compiler prevents made-up values such as `"avanzto"`. An enum can have fields, a constructor and methods. `values()` gives all the constants, `valueOf` finds the one with a given name.'),
            C('enum Livello {
    BASE("Base"), INTERMEDIO("Intermedio"), AVANZATO("Avanzato");

    private final String etichetta;

    Livello(String etichetta) {
        this.etichetta = etichetta;
    }
    String etichetta() { return etichetta; }
}

Livello l = Livello.valueOf("BASE");
for (Livello x : Livello.values()) {
    System.out.println(x + " " + x.ordinal() + " " + x.etichetta());
}

String consiglio = switch (l) {
    case BASE -> "Inizia da qui";
    case INTERMEDIO, AVANZATO -> "Vai avanti";
};', 'java'),
            P('Uno `switch` su un enum che copre tutti i casi non ha bisogno di `default`: se aggiungi una costante, il compilatore ti segnala gli switch da aggiornare. `ordinal()` dà la posizione, partendo da 0.', 'A `switch` on an enum covering every case needs no `default`: if you add a constant, the compiler flags the switches to update. `ordinal()` gives the position, starting from 0.'),
        ], [
            Q('en1', L('Come leggi tutte le costanti di un enum?', 'How do you read all the constants of an enum?'), ['Livello.all()', 'Livello.values()', 'Livello.list()', 'Livello.entries'], 1, L('Restituisce un array nell’ordine di dichiarazione.', 'It returns an array in declaration order.')),
            Q('en2', L('Che cosa fa `Livello.valueOf("BASE")`?', 'What does `Livello.valueOf("BASE")` do?'), [L('Restituisce il numero 0', 'Returns the number 0'), L('Crea una nuova costante', 'Creates a new constant'), L('Restituisce la costante BASE', 'Returns the BASE constant'), L('Restituisce la stringa "Base"', 'Returns the string "Base"')], 2, L('Se il nome non esiste lancia `IllegalArgumentException`.', 'If the name does not exist it throws `IllegalArgumentException`.')),
            Q('en3', L('Si può fare `new Livello(...)` fuori dall’enum?', 'Can you do `new Livello(...)` outside the enum?'), [L('Sì, come per ogni classe', 'Yes, as for any class'), L('Solo se il costruttore è public', 'Only if the constructor is public'), L('Solo dentro un metodo static', 'Only inside a static method'), L('No, le costanti sono solo quelle dichiarate', 'No, the constants are only those declared')], 3, L('Il costruttore di un enum è sempre privato.', 'An enum’s constructor is always private.')),
        ], task('java', L('Crea `enum Semaforo` con le costanti `ROSSO`, `GIALLO` e `VERDE`.', 'Create `enum Semaforo` with the constants `ROSSO`, `GIALLO` and `VERDE`.'), '', [re('enum\s+Semaforo\s*\{\s*ROSSO\s*,\s*GIALLO\s*,\s*VERDE\s*;?\s*\}', L('L’enum ha le tre costanti', 'The enum has the three constants'))], '', 'enum Semaforo { ROSSO, GIALLO, VERDE }'), [
            dBlanks(pFill(), '___ Taglia { S, M, L }
Taglia t = Taglia.___("M");
String nota = switch (t) {
    case S -> "Piccola";
    case M, L -> "Grande";
};
System.out.println(t.___());', ['enum', 'valueOf', 'ordinal'], ['class', 'parse', 'index'], L('Dichiara, cerca per nome, leggi la posizione.', 'Declare, look up by name, read the position.')),
            dFlash(pFlash(), [[L('Un enum può avere un costruttore', 'An enum can have a constructor'), true], [L('Le costanti di un enum si scrivono di solito in maiuscolo', 'Enum constants are usually written in capitals'), true], [L('Si possono aggiungere costanti mentre il programma gira', 'Constants can be added while the program runs'), false], [L('`ordinal()` della prima costante vale 1', '`ordinal()` of the first constant is 1'), false]], L('Un insieme chiuso, deciso nel codice.', 'A closed set, decided in the code.')),
        ]),
        lesson('equality-sorting', L('equals, hashCode e ordinamenti', 'equals, hashCode and sorting'), [
            P('`equals` decide quando due oggetti sono «uguali» nel contenuto; se lo ridefinisci devi ridefinire anche `hashCode`, altrimenti `HashSet` e `HashMap` si comportano male. `toString` dà un testo leggibile. `Comparable` definisce l’**ordine naturale** di una classe, un `Comparator` un ordine a scelta.', '`equals` decides when two objects are “equal” in content; if you redefine it you must redefine `hashCode` too, otherwise `HashSet` and `HashMap` misbehave. `toString` gives readable text. `Comparable` defines a class’s **natural order**, a `Comparator` an order of your choice.'),
            C('class Libro implements Comparable<Libro> {
    final String isbn;
    final String titolo;
    final int anno;

    Libro(String isbn, String titolo, int anno) {
        this.isbn = isbn;
        this.titolo = titolo;
        this.anno = anno;
    }
    @Override public boolean equals(Object o) {
        return o instanceof Libro l && isbn.equals(l.isbn);
    }
    @Override public int hashCode() { return isbn.hashCode(); }
    @Override public String toString() { return titolo + " (" + anno + ")"; }
    @Override public int compareTo(Libro altro) { return Integer.compare(anno, altro.anno); }
}

List<Libro> libri = new ArrayList<>(catalogo);
Collections.sort(libri);                                       // per anno
libri.sort(Comparator.comparing((Libro l) -> l.titolo).reversed());', 'java'),
            P('`compareTo` restituisce un numero negativo se l’oggetto viene prima, zero se sono pari, positivo se viene dopo. I **record** scrivono da soli `equals`, `hashCode` e `toString`.', '`compareTo` returns a negative number if the object comes first, zero if they are level, positive if it comes after. **Records** write `equals`, `hashCode` and `toString` by themselves.'),
        ], [
            Q('eq1', L('Se ridefinisci `equals`, che cos’altro devi ridefinire?', 'If you redefine `equals`, what else must you redefine?'), ['toString', 'compareTo', 'hashCode', 'clone'], 2, L('Oggetti uguali devono avere lo stesso hashCode.', 'Equal objects must have the same hashCode.')),
            Q('eq2', L('Che cosa restituisce `compareTo` quando l’oggetto viene prima dell’altro?', 'What does `compareTo` return when the object comes before the other?'), [L('Un numero negativo', 'A negative number'), 'true', L('Zero', 'Zero'), L('Un numero positivo', 'A positive number')], 0, L('Zero vuol dire pari.', 'Zero means level.')),
            Q('eq3', L('Che differenza c’è tra Comparable e Comparator?', 'What is the difference between Comparable and Comparator?'), [L('Nessuna, sono sinonimi', 'None, they are synonyms'), L('Comparable è l’ordine naturale della classe, Comparator un ordine esterno a scelta', 'Comparable is the class’s natural order, Comparator an external order of choice'), L('Comparator funziona solo con i numeri', 'Comparator only works with numbers'), L('Comparable serve solo per le stringhe', 'Comparable is only for strings')], 1, L('Puoi avere molti Comparator per la stessa classe.', 'You can have many Comparators for the same class.')),
        ], task('java', L('Ordina `persone` (record `Persona(String nome, int eta)`) per età con `persone.sort` e `Comparator.comparing`.', 'Sort `persone` (record `Persona(String nome, int eta)`) by age with `persone.sort` and `Comparator.comparing`.'), 'record Persona(String nome, int eta) {}
List<Persona> persone = new ArrayList<>(elenco);
', [re('persone\.sort\s*\(\s*Comparator\.(comparing|comparingInt)\s*\(\s*(Persona::eta|\(?\s*(Persona\s+)?(\w+)\s*\)?\s*->\s*\4\.eta\s*\(\s*\))\s*\)\s*\)', L('Ordini per età', 'You sort by age'))], '', 'persone.sort(Comparator.comparing(Persona::eta));'), [
            dBug(pBug(), ['String a = new String("ciao");', 'String b = "ciao";', 'if (a == b) {', '    System.out.println("Uguali");', '}'], 2, 'if (a.equals(b)) {', L('`==` confronta i riferimenti: per il contenuto serve `equals`.', '`==` compares references: content needs `equals`.')),
            dMatch(pMatch(), [['equals', L('Uguaglianza di contenuto', 'Content equality')], ['hashCode', L('Codice per le tabelle hash', 'Code for hash tables')], ['toString', L('Testo leggibile', 'Readable text')], ['compareTo', L('Ordine naturale', 'Natural order')], ['Comparator', L('Ordine a scelta', 'Custom order')]], L('I metodi che ogni classe dati dovrebbe avere.', 'The methods every data class should have.')),
        ]),
        lesson('files-nio', L('File con java.nio', 'Files with java.nio'), [
            P('`Path` rappresenta un percorso, `Files` offre metodi pronti: `readString`, `writeString`, `readAllLines`, `exists`, `createDirectories`. Questi metodi usano UTF-8 e lanciano `IOException`, un’eccezione **controllata** che va gestita.', '`Path` represents a path, `Files` offers ready-made methods: `readString`, `writeString`, `readAllLines`, `exists`, `createDirectories`. These methods use UTF-8 and throw `IOException`, a **checked** exception that must be handled.'),
            C('import java.io.IOException;
import java.nio.file.*;
import java.util.List;

Path note = Path.of("dati", "note.txt");
try {
    Files.createDirectories(note.getParent());
    Files.writeString(note, "Prima riga\n");
    Files.writeString(note, "Seconda riga\n", StandardOpenOption.APPEND);

    List<String> righe = Files.readAllLines(note);
    System.out.println(righe.size());            // 2

    try (var flusso = Files.lines(note)) {
        long piene = flusso.filter(r -> !r.isBlank()).count();
    }
} catch (IOException e) {
    System.err.println("Errore: " + e.getMessage());
}', 'java'),
            P('`Files.lines` legge un file grande riga per riga come stream, senza caricarlo tutto: tiene però aperto il file, quindi va chiuso con un try-with-resources.', '`Files.lines` reads a big file line by line as a stream, without loading it all: it keeps the file open, though, so it must be closed with try-with-resources.'),
        ], [
            Q('fi1', L('Quale classe offre `readString` e `writeString`?', 'Which class offers `readString` and `writeString`?'), ['File', 'Path', 'Files', 'Scanner'], 2, L('`Path` descrive il percorso, `Files` lavora sui file.', '`Path` describes the path, `Files` works on files.')),
            Q('fi2', L('Come aggiungi testo in fondo con `writeString`?', 'How do you append text with `writeString`?'), [L('Con StandardOpenOption.APPEND', 'With StandardOpenOption.APPEND'), L('Con un secondo writeString senza opzioni', 'With a second writeString without options'), L('Con Files.append()', 'With Files.append()'), L('Non si può', 'You cannot')], 0, L('Senza opzioni il file viene riscritto.', 'Without options the file is rewritten.')),
            Q('fi3', L('Perché `Files.lines` sta in un try-with-resources?', 'Why does `Files.lines` go in a try-with-resources?'), [L('Per leggerlo più in fretta', 'To read it faster'), L('Perché altrimenti legge solo la prima riga', 'Because otherwise it reads only the first line'), L('Perché lo richiede il compilatore', 'Because the compiler requires it'), L('Perché tiene aperto il file finché lo stream non viene chiuso', 'Because it keeps the file open until the stream is closed')], 3, L('Così il file si chiude anche in caso di errore.', 'That way the file closes even when an error occurs.')),
        ], task('java', L('Dentro un `try`, leggi tutto `config.txt` in `String testo` con `Files.readString(Path.of("config.txt"))` e gestisci `IOException` nel `catch`.', 'Inside a `try`, read all of `config.txt` into `String testo` with `Files.readString(Path.of("config.txt"))` and handle `IOException` in the `catch`.'), '', [re('String\s+testo\s*=\s*Files\.readString\s*\(\s*(Path\.of|Paths\.get)\s*\(\s*"config\.txt"\s*\)\s*\)\s*;', L('Leggi il file', 'You read the file')), re('catch\s*\(\s*IOException\s+\w+\s*\)', L('Gestisci l’errore', 'You handle the error'))], '', 'try {
    String testo = Files.readString(Path.of("config.txt"));
    System.out.println(testo);
} catch (IOException e) {
    System.err.println(e.getMessage());
}'), [
            dOrder(pSort(), ['Path p = Path.of("voti.txt");', 'try {', '    List<String> righe = Files.readAllLines(p);', '    System.out.println(righe.size());', '} catch (IOException e) {', '    System.err.println(e.getMessage());', '}'], L('Percorso, prova, leggi, usa, gestisci.', 'Path, try, read, use, handle.')),
            dMatch(pMatch(), [['Path.of', L('Crea un percorso', 'Creates a path')], ['Files.readString', L('Legge tutto il file', 'Reads the whole file')], ['Files.writeString', L('Scrive un testo', 'Writes text')], ['Files.lines', L('Righe come stream', 'Lines as a stream')], ['Files.exists', L('Il file c’è?', 'Does the file exist?')]], L('Gli attrezzi di java.nio.file.', 'The tools of java.nio.file.')),
        ]),
        lesson('build-tools', L('Pacchetti, Maven e Gradle', 'Packages, Maven and Gradle'), [
            P('Il `package` è il cognome delle classi e corrisponde alle cartelle: `it.fucina.corsi` sta in `it/fucina/corsi/`. Per convenzione si usa il proprio dominio al contrario. Senza modificatore di accesso, una classe o un metodo è visibile solo dentro il suo pacchetto.', 'The `package` is the surname of classes and matches the folders: `it.fucina.corsi` lives in `it/fucina/corsi/`. By convention you use your own domain reversed. Without an access modifier, a class or method is visible only inside its package.'),
            C('// src/main/java/it/fucina/corsi/Catalogo.java
package it.fucina.corsi;

import java.util.List;

public class Catalogo {
    public List<String> titoli() { return List.of("Java base"); }
}', 'java'),
            C('<project>
  <modelVersion>4.0.0</modelVersion>
  <groupId>it.fucina</groupId>
  <artifactId>corsi</artifactId>
  <version>1.0.0</version>
  <properties>
    <maven.compiler.release>25</maven.compiler.release>
  </properties>
  <dependencies>
    <dependency>
      <groupId>org.junit.jupiter</groupId>
      <artifactId>junit-jupiter</artifactId>
      <version>6.0.0</version>
      <scope>test</scope>
    </dependency>
  </dependencies>
</project>', 'xml'),
            P('**Maven** (`pom.xml`, `mvn package`) e **Gradle** (`build.gradle`, `./gradlew build`) scaricano le librerie, compilano, lanciano i test e creano il JAR. Una dipendenza si identifica con tre coordinate: `groupId`, `artifactId` e `version`.', '**Maven** (`pom.xml`, `mvn package`) and **Gradle** (`build.gradle`, `./gradlew build`) download libraries, compile, run the tests and build the JAR. A dependency is identified by three coordinates: `groupId`, `artifactId` and `version`.'),
        ], [
            Q('bt1', L('Dove sta il file della classe `it.fucina.corsi.Catalogo`?', 'Where does the file of class `it.fucina.corsi.Catalogo` live?'), ['it.fucina.corsi/Catalogo.java', 'it/fucina/corsi/Catalogo.java', 'corsi/it/fucina/Catalogo.java', 'Catalogo/it.fucina.corsi.java'], 1, L('Ogni parte del pacchetto è una cartella.', 'Each part of the package is a folder.')),
            Q('bt2', L('Chi vede un metodo senza modificatore di accesso?', 'Who sees a method with no access modifier?'), [L('Tutti', 'Everyone'), L('Solo la sua classe', 'Only its own class'), L('Le classi dello stesso pacchetto', 'Classes in the same package'), L('Solo le sottoclassi', 'Only subclasses')], 2, L('È la visibilità «di pacchetto».', 'It is “package-private” visibility.')),
            Q('bt3', L('Quali sono le tre coordinate di una dipendenza Maven?', 'What are the three coordinates of a Maven dependency?'), ['name, url, hash', 'package, class, method', 'id, tag, branch', 'groupId, artifactId, version'], 3, L('Chi la pubblica, come si chiama, quale versione.', 'Who publishes it, what it is called, which version.')),
        ], task('xml', L('Nel `pom.xml` aggiungi la dipendenza con groupId `com.google.code.gson`, artifactId `gson` e versione `2.11.0`.', 'In the `pom.xml` add the dependency with groupId `com.google.code.gson`, artifactId `gson` and version `2.11.0`.'), '<dependencies>
</dependencies>', [re('<groupId>\s*com\.google\.code\.gson\s*</groupId>', L('Il groupId', 'The groupId')), re('<artifactId>\s*gson\s*</artifactId>', L('L’artifactId', 'The artifactId')), re('<version>\s*2\.11\.0\s*</version>', L('La versione', 'The version'))], '', '<dependencies>
  <dependency>
    <groupId>com.google.code.gson</groupId>
    <artifactId>gson</artifactId>
    <version>2.11.0</version>
  </dependency>
</dependencies>'), [
            dMatch(pMatch(), [['package', L('Il cognome della classe', 'The class’s surname')], ['import', L('Usa una classe di un altro pacchetto', 'Uses a class from another package')], ['pom.xml', L('Progetto Maven', 'Maven project')], ['build.gradle', L('Progetto Gradle', 'Gradle project')], ['JAR', L('Archivio del programma', 'Program archive')]], L('Organizzare e costruire un progetto.', 'Organising and building a project.')),
            dOrder(pSort(), ['package it.fucina.app;', 'import java.util.List;', 'public class App {', '    public static void main(String[] args) {', '        System.out.println(List.of(1, 2));', '    }', '}'], L('Pacchetto, import, classe, metodo.', 'Package, imports, class, method.')),
        ]),
    ],
];
