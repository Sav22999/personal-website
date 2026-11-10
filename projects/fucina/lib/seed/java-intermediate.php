<?php
/** Java intermediate: classes and objects, inheritance, interfaces, collections and exceptions. */

return [
    'id' => 'java-intermediate', 'family' => 'java', 'after' => 'java-base',
    'title' => L('Java intermedio', 'Intermediate Java'), 'short' => L('Java intermedio', 'Java Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Programmazione a oggetti: classi, incapsulamento, ereditarietà, interfacce, collezioni ed eccezioni.', 'Object-oriented programming: classes, encapsulation, inheritance, interfaces, collections and exceptions.'),
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
    ],
];
