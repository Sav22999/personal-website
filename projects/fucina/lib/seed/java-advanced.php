<?php
/** Java advanced: generics, lambdas and streams, Optional, concurrency, testing, sealed types and patterns, collectors, CompletableFuture and the JVM. */

return [
    'id' => 'java-advanced', 'family' => 'java', 'after' => 'java-intermediate',
    'title' => L('Java avanzato', 'Advanced Java'), 'short' => L('Java avanzato', 'Java Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Generics, lambda e stream, Optional, programmazione concorrente, test con JUnit, sealed e pattern matching, collector, CompletableFuture, memoria e garbage collector.', 'Generics, lambdas and streams, Optional, concurrent programming, tests with JUnit, sealed types and pattern matching, collectors, CompletableFuture, memory and the garbage collector.'),
    'lessons' => [
        lesson('generics', L('Generics', 'Generics'), [
            P('I **generics** permettono di scrivere classi e metodi che funzionano con qualsiasi tipo, mantenendo i controlli del compilatore. La lettera tra parentesi angolari (`<T>`) è il tipo scelto da chi li usa.', '**Generics** let you write classes and methods that work with any type, keeping the compiler’s checks. The letter between angle brackets (`<T>`) is the type chosen by whoever uses them.'),
            C('class Scatola<T> {
    private T contenuto;
    void metti(T cosa) { contenuto = cosa; }
    T prendi() { return contenuto; }
}

Scatola<String> s = new Scatola<>();
s.metti("libro");
String x = s.prendi();

static <T extends Comparable<T>> T massimo(T a, T b) {
    return a.compareTo(b) >= 0 ? a : b;
}', 'java'),
            P('`T extends Comparable<T>` pone un **limite**: vanno bene solo i tipi confrontabili.', '`T extends Comparable<T>` sets a **bound**: only comparable types are allowed.'),
        ], [
            Q('g1', L('A che cosa serve `<T>`?', 'What is `<T>` for?'), [L('Un tipo scelto da chi usa la classe', 'A type chosen by the class’s user'), L('Un tempo', 'A time'), L('Una costante', 'A constant'), L('Un tag HTML', 'An HTML tag')], 0, L('È un parametro di tipo.', 'It is a type parameter.')),
            Q('g2', L('Che vantaggio danno i generics?', 'What do generics give you?'), [L('Controlli di tipo senza cast', 'Type checks without casts'), L('Codice più lento', 'Slower code'), L('Meno memoria sempre', 'Always less memory'), L('Nessuno', 'None')], 0, L('Gli errori emergono in compilazione.', 'Errors show at compile time.')),
            Q('g3', L('Che cosa significa `<T extends Number>`?', 'What does `<T extends Number>` mean?'), [L('T deve essere un numero', 'T must be a number'), L('T eredita da tutti', 'T inherits from everything'), L('T è una stringa', 'T is a string'), L('Niente', 'Nothing')], 0, L('È un limite superiore.', 'It is an upper bound.')),
        ], task('java', L('Crea `class Coppia<A, B>` con i campi `A primo;` e `B secondo;`.', 'Create `class Coppia<A, B>` with the fields `A primo;` and `B secondo;`.'), '', [re('class\s+Coppia\s*<\s*A\s*,\s*B\s*>', L('La classe è generica', 'The class is generic')), re('A\s+primo\s*;', L('C’è il primo', 'There is the first')), re('B\s+secondo\s*;', L('C’è il secondo', 'There is the second'))], '', 'class Coppia<A, B> {
    A primo;
    B secondo;
}'), [
            dBlanks(pFill(), "List<___> nomi = new ArrayList<>();\nMap<String, ___> eta = new HashMap<>();\nstatic <___> T primo(List<T> l) { return l.get(0); }", ['String', 'Integer', 'T'], ['string', 'int', 'Object'], L('Tipi oggetto nei generics: `Integer`, non `int`.', 'Object types in generics: `Integer`, not `int`.')),
            dFlash(pFlash(), [[L('`List<int>` è valido', '`List<int>` is valid'), false], [L('I generics evitano molti cast', 'Generics avoid many casts'), true], [L('`<T extends Comparable<T>>` limita T', '`<T extends Comparable<T>>` bounds T'), true], [L('A runtime una `List<String>` ricorda di contenere stringhe', 'At runtime a `List<String>` remembers it holds strings'), false]], L('Si usano i tipi wrapper.', 'Wrapper types are used.')),
        ]),
        lesson('streams', L('Lambda e stream', 'Lambdas and streams'), [
            P('Una **lambda** è una funzione breve senza nome: `x -> x * 2`. Gli **stream** elaborano una collezione come una catena: `filter` scarta, `map` trasforma, `collect` raccoglie il risultato.', 'A **lambda** is a short nameless function: `x -> x * 2`. **Streams** process a collection as a chain: `filter` drops, `map` transforms, `collect` gathers the result.'),
            C('List<String> nomi = List.of("ada", "bea", "carlo", "di");

List<String> lunghi = nomi.stream()
    .filter(n -> n.length() > 2)
    .map(String::toUpperCase)
    .sorted()
    .toList();

int somma = IntStream.rangeClosed(1, 10).sum();', 'java'),
            P('`String::toUpperCase` è un **riferimento a metodo**, una lambda ancora più corta. Uno stream non modifica la lista originale.', '`String::toUpperCase` is a **method reference**, an even shorter lambda. A stream does not change the original list.'),
        ], [
            Q('s1', L('Che cosa fa `filter`?', 'What does `filter` do?'), [L('Tiene gli elementi che passano il test', 'Keeps the items that pass the test'), L('Trasforma ogni elemento', 'Transforms each item'), L('Ordina', 'Sorts'), L('Conta', 'Counts')], 0, L('`map` trasforma.', '`map` transforms.')),
            Q('s2', L('Che cos’è `String::length`?', 'What is `String::length`?'), [L('Un riferimento a metodo', 'A method reference'), L('Un errore', 'An error'), L('Un campo statico', 'A static field'), L('Un commento', 'A comment')], 0, L('Equivale a `s -> s.length()`.', 'It equals `s -> s.length()`.')),
            Q('s3', L('Uno stream modifica la lista di partenza?', 'Does a stream change the source list?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo con `map`', 'Only with `map`'), L('Solo se ordinata', 'Only if sorted')], 0, L('Ne crea una nuova.', 'It makes a new one.')),
        ], task('java', L('Da `numeri` ottieni con uno stream la lista dei pari: `filter(n -> n % 2 == 0)` e `toList()`.', 'From `numeri` get the list of evens with a stream: `filter(n -> n % 2 == 0)` and `toList()`.'), 'List<Integer> numeri = List.of(1, 2, 3, 4);
', [re('numeri\.stream\s*\(\s*\)', L('Parti dallo stream', 'You start from the stream')), re('\.filter\s*\(\s*n\s*->\s*n\s*%\s*2\s*==\s*0\s*\)', L('Filtri i pari', 'You filter the evens')), re('\.toList\s*\(\s*\)', L('Raccogli in una lista', 'You collect into a list'))], '', 'List<Integer> pari = numeri.stream()
    .filter(n -> n % 2 == 0)
    .toList();'), [
            dOrder(pSort(), ['prodotti.stream()', '    .filter(p -> p.prezzo() < 20)', '    .map(Prodotto::nome)', '    .sorted()', '    .toList();'], L('Sorgente, filtro, trasformazione, ordine, raccolta.', 'Source, filter, transform, sort, collect.')),
            dMatch(pMatch(), [['filter', L('Scarta', 'Drops')], ['map', L('Trasforma', 'Transforms')], ['reduce', L('Riduce a un valore', 'Reduces to one value')], ['collect', L('Raccoglie', 'Gathers')], ['forEach', L('Fa qualcosa per ognuno', 'Does something for each')]], L('Operazioni degli stream.', 'Stream operations.')),
        ]),
        lesson('optional', L('Optional e null', 'Optional and null'), [
            P('`NullPointerException` è l’errore più comune di Java. `Optional<T>` rende esplicito che un valore può mancare e offre metodi sicuri: `orElse`, `map`, `ifPresent`.', '`NullPointerException` is Java’s most common error. `Optional<T>` makes it explicit that a value may be missing and offers safe methods: `orElse`, `map`, `ifPresent`.'),
            C('Optional<Utente> trova(String email) {
    return utenti.stream()
        .filter(u -> u.email().equals(email))
        .findFirst();
}

String nome = trova("ada@example.com")
    .map(Utente::nome)
    .orElse("Ospite");

trova("x@y.it").ifPresent(u -> System.out.println(u.nome()));', 'java'),
            P('Usa `Optional` come valore di ritorno, non come campo o parametro: lì è meglio decidere chiaramente.', 'Use `Optional` as a return value, not as a field or parameter: there it is better to decide clearly.'),
        ], [
            Q('p1', L('Che cosa dà `orElse("x")` su un Optional vuoto?', 'What does `orElse("x")` give on an empty Optional?'), ['"x"', 'null', L('Un errore', 'An error'), '""'], 0, L('Il valore di riserva.', 'The fallback value.')),
            Q('p2', L('Quale metodo esegue codice solo se c’è un valore?', 'Which method runs code only if a value is there?'), ['ifPresent', 'orElse', 'get', 'isEmpty'], 0, L('Riceve una lambda.', 'It takes a lambda.')),
            Q('p3', L('Dove conviene usare `Optional`?', 'Where should you use `Optional`?'), [L('Come valore di ritorno', 'As a return value'), L('In ogni campo', 'In every field'), L('Come parametro sempre', 'Always as a parameter'), L('Mai', 'Never')], 0, L('Dice “può mancare”.', 'It says “may be missing”.')),
        ], task('java', L('Restituisci il nome con `.map(Utente::nome).orElse("Anonimo")`.', 'Return the name with `.map(Utente::nome).orElse("Anonimo")`.'), 'Optional<Utente> u = cerca(id);
', [re('\.map\s*\(\s*Utente::nome\s*\)\s*\.orElse\s*\(\s*"Anonimo"\s*\)', L('Usi map e orElse', 'You use map and orElse'))], '', 'String nome = u.map(Utente::nome).orElse("Anonimo");'), [
            dBug(pBug(), ['Optional<Corso> c = cerca("java");', 'String t = c.get().titolo();', 'System.out.println(t);'], 1, 'String t = c.map(Corso::titolo).orElse("—");', L('`get()` su un Optional vuoto lancia un’eccezione.', '`get()` on an empty Optional throws.')),
            dFlash(pFlash(), [[L('`Optional.empty()` non contiene nulla', '`Optional.empty()` holds nothing'), true], [L('`get()` è sempre sicuro', '`get()` is always safe'), false], [L('`map` su un Optional vuoto resta vuoto', '`map` on an empty Optional stays empty'), true], [L('`orElse` dà un valore di riserva se l’Optional è vuoto', '`orElse` gives a fallback value when the Optional is empty'), true]], L('Preferisci `orElse` e `map`.', 'Prefer `orElse` and `map`.')),
        ]),
        lesson('concurrency', L('Concorrenza', 'Concurrency'), [
            P('Più **thread** lavorano insieme. Invece di crearli a mano, un **ExecutorService** gestisce un gruppo di thread; i **virtual thread** di Java 21 sono leggerissimi e se ne possono avere migliaia.', 'Several **threads** work together. Instead of creating them by hand, an **ExecutorService** manages a pool of threads; Java 21 **virtual threads** are very light and you can have thousands.'),
            C('try (var executor = Executors.newVirtualThreadPerTaskExecutor()) {
    List<Future<String>> risultati = urls.stream()
        .map(url -> executor.submit(() -> scarica(url)))
        .toList();
    for (var f : risultati) System.out.println(f.get());
}

AtomicInteger contatore = new AtomicInteger();
contatore.incrementAndGet();', 'java'),
            P('Quando due thread toccano lo stesso dato servono strumenti sicuri come `AtomicInteger` o `ConcurrentHashMap`, altrimenti i risultati si mescolano.', 'When two threads touch the same data you need safe tools such as `AtomicInteger` or `ConcurrentHashMap`, otherwise results get mixed up.'),
        ], [
            Q('c1', L('Che cosa gestisce un `ExecutorService`?', 'What does an `ExecutorService` manage?'), [L('Un gruppo di thread', 'A pool of threads'), L('Un database', 'A database'), L('Un file', 'A file'), L('La grafica', 'Graphics')], 0, L('Tu gli dai i compiti.', 'You give it the tasks.')),
            Q('c2', L('Perché `AtomicInteger` e non `int`?', 'Why `AtomicInteger` and not `int`?'), [L('È sicuro tra più thread', 'It is safe across threads'), L('È più grande', 'It is bigger'), L('È più veloce sempre', 'It is always faster'), L('Non c’è differenza', 'No difference')], 0, L('Le operazioni non si sovrappongono.', 'Operations do not overlap.')),
            Q('c3', L('Che vantaggio hanno i virtual thread?', 'What is the advantage of virtual threads?'), [L('Sono leggerissimi', 'They are very light'), L('Usano la GPU', 'They use the GPU'), L('Non usano memoria', 'They use no memory'), L('Sono più sicuri', 'They are safer')], 0, L('Ottimi per tante attese di rete.', 'Great for many network waits.')),
        ], task('java', L('Crea `AtomicInteger visite = new AtomicInteger();` e incrementalo con `incrementAndGet()`.', 'Create `AtomicInteger visite = new AtomicInteger();` and increment it with `incrementAndGet()`.'), '', [re('AtomicInteger\s+visite\s*=\s*new\s+AtomicInteger\s*\(\s*\)\s*;', L('Il contatore esiste', 'The counter exists')), re('visite\.incrementAndGet\s*\(\s*\)', L('Lo incrementi', 'You increment it'))], '', 'AtomicInteger visite = new AtomicInteger();
visite.incrementAndGet();'), [
            dMatch(pMatch(), [['Thread', L('Un filo di esecuzione', 'A thread of execution')], ['ExecutorService', L('Gestisce i thread', 'Manages threads')], ['Future', L('Un risultato che arriverà', 'A result to come')], ['AtomicInteger', L('Contatore sicuro', 'Safe counter')], ['ConcurrentHashMap', L('Mappa sicura', 'Safe map')]], L('Gli attrezzi della concorrenza.', 'Concurrency tools.')),
            dFlash(pFlash(), [[L('Due thread possono scrivere lo stesso `int` senza problemi', 'Two threads can write the same `int` safely'), false], [L('`Future.get()` aspetta il risultato', '`Future.get()` waits for the result'), true], [L('I virtual thread arrivano con Java 21', 'Virtual threads come with Java 21'), true], [L('`synchronized` lascia entrare un thread alla volta', '`synchronized` lets one thread in at a time'), true]], L('Dati condivisi = strumenti sicuri.', 'Shared data = safe tools.')),
        ]),
        lesson('junit', L('Test con JUnit', 'Testing with JUnit'), [
            P('**JUnit** (con l’API Jupiter, dalla versione 5; la 6 richiede Java 17) è lo standard per i test in Java. Un metodo segnato con `@Test` verifica il codice con le asserzioni: `assertEquals`, `assertTrue`, `assertThrows`.', '**JUnit** (with the Jupiter API, since version 5; version 6 requires Java 17) is Java’s standard for tests. A method marked `@Test` checks code with assertions: `assertEquals`, `assertTrue`, `assertThrows`.'),
            C('import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.*;

class CarrelloTest {
    @Test
    void totaleConDuePezzi() {
        var c = new Carrello();
        c.aggiungi(10);
        c.aggiungi(5);
        assertEquals(15, c.totale());
    }

    @Test
    void importoNegativo() {
        assertThrows(IllegalArgumentException.class, () -> new Carrello().aggiungi(-1));
    }
}', 'java'),
            P('In `assertEquals` va prima il valore **atteso**, poi quello ottenuto: così i messaggi di errore si leggono bene.', 'In `assertEquals` the **expected** value comes first, then the actual one: so error messages read well.'),
        ], [
            Q('j1', L('Quale annotazione segna un test?', 'Which annotation marks a test?'), ['@Test', '@Check', '@Junit', '@Assert'], 0, L('Da `org.junit.jupiter.api`.', 'From `org.junit.jupiter.api`.')),
            Q('j2', L('Che cosa va per primo in `assertEquals`?', 'What comes first in `assertEquals`?'), [L('Il valore atteso', 'The expected value'), L('Il valore ottenuto', 'The actual value'), L('Il messaggio', 'The message'), L('Non conta', 'It does not matter')], 0, L('Atteso, poi ottenuto.', 'Expected, then actual.')),
            Q('j3', L('Come verifichi che venga lanciata un’eccezione?', 'How do you check an exception is thrown?'), ['assertThrows', 'assertError', 'expectThrow', 'catchTest'], 0, L('Riceve la classe e una lambda.', 'It takes the class and a lambda.')),
        ], task('java', L('Scrivi un `@Test` che verifica con `assertEquals(4, somma(2, 2))`.', 'Write a `@Test` that checks with `assertEquals(4, somma(2, 2))`.'), 'class SommaTest {
}', [re('@Test\s+void\s+\w+\s*\(\s*\)', L('Il test esiste', 'The test exists')), re('assertEquals\s*\(\s*4\s*,\s*somma\s*\(\s*2\s*,\s*2\s*\)\s*\)', L('L’asserzione è giusta', 'The assertion is right'))], '', '@Test
void sommaDiDue() {
    assertEquals(4, somma(2, 2));
}'), [
            dOrder(pSort(), ['@Test', 'void scontoDelDieciPerCento() {', '    var p = new Prezzo(100);', '    p.sconto(10);', '    assertEquals(90, p.valore());', '}'], L('Annotazione, prepara, esegui, verifica.', 'Annotation, arrange, act, assert.')),
            dBlanks(pFill(), "@___\nvoid vuoto() {\n    ___(new Carrello().vuoto());\n    ___(IllegalStateException.class, () -> new Carrello().paga());\n}", ['Test', 'assertTrue', 'assertThrows'], ['Check', 'assertEquals', 'expect'], L('Annotazione e asserzioni.', 'Annotation and assertions.')),
        ]),
        lesson('sealed-patterns', L('Sealed, record pattern e switch', 'Sealed types, record patterns and switch'), [
            P('Un tipo **sealed** dichiara con `permits` l’elenco chiuso dei suoi sottotipi. Così lo `switch` sa che i casi sono finiti e non serve `default`: se un domani aggiungi una forma, il compilatore indica ogni switch da aggiornare. I **record pattern** (Java 21) smontano un record nei suoi componenti, `when` aggiunge una condizione.', 'A **sealed** type declares with `permits` the closed list of its subtypes. So the `switch` knows the cases are finite and needs no `default`: if one day you add a shape, the compiler points to every switch to update. **Record patterns** (Java 21) take a record apart into its components, `when` adds a condition.'),
            C('sealed interface Forma permits Cerchio, Rettangolo, Triangolo {}
record Cerchio(double r) implements Forma {}
record Rettangolo(double b, double h) implements Forma {}
record Triangolo(double b, double h) implements Forma {}

static double area(Forma f) {
    return switch (f) {
        case Cerchio(double r) -> Math.PI * r * r;
        case Rettangolo(double b, double h) when b == h -> b * b;
        case Rettangolo(double b, double h) -> b * h;
        case Triangolo(var b, var h) -> b * h / 2;
    };
}

Object o = "ciao";
if (o instanceof String s && !s.isEmpty()) System.out.println(s.length());', 'java'),
            P('`instanceof String s` controlla il tipo e dichiara subito la variabile `s`, senza cast. Nei casi con `when` l’ordine conta: il caso più specifico va prima di quello generale.', '`instanceof String s` checks the type and declares the variable `s` straight away, with no cast. In cases with `when` order matters: the more specific case goes before the general one.'),
        ], [
            Q('sp1', L('Che cosa fa `permits` in un’interfaccia sealed?', 'What does `permits` do in a sealed interface?'), [L('Dà i permessi di lettura ai file', 'Grants read permission on files'), L('Elenca le sole classi che possono implementarla', 'Lists the only classes that may implement it'), L('Rende pubblici tutti i metodi', 'Makes every method public'), L('Permette l’ereditarietà multipla', 'Allows multiple inheritance')], 1, L('Nessun’altra classe può aggiungersi.', 'No other class can join.')),
            Q('sp2', L('Perché lo switch su una `Forma` sealed non ha bisogno di `default`?', 'Why does the switch on a sealed `Forma` need no `default`?'), [L('Perché default è vietato con i record', 'Because default is banned with records'), L('Perché restituisce sempre zero', 'Because it always returns zero'), L('Perché Java ignora i casi mancanti', 'Because Java ignores missing cases'), L('Perché il compilatore conosce tutti i casi possibili', 'Because the compiler knows every possible case')], 3, L('Lo switch è esaustivo.', 'The switch is exhaustive.')),
            Q('sp3', L('Che cosa fa `case Cerchio(double r) ->`?', 'What does `case Cerchio(double r) ->` do?'), [L('Crea un nuovo cerchio di raggio r', 'Creates a new circle of radius r'), L('Confronta r con zero', 'Compares r with zero'), L('Riconosce un Cerchio e mette il suo raggio in r', 'Recognises a Cerchio and puts its radius in r'), L('Converte la forma in double', 'Converts the shape to double')], 2, L('È un record pattern.', 'It is a record pattern.')),
        ], task('java', L('Completa lo switch: per `Quadrato(double lato)` restituisci `lato * lato`.', 'Complete the switch: for `Quadrato(double lato)` return `lato * lato`.'), 'sealed interface Figura permits Quadrato, Cerchio {}
record Quadrato(double lato) implements Figura {}
record Cerchio(double r) implements Figura {}

static double area(Figura f) {
    return switch (f) {
        case Cerchio(double r) -> Math.PI * r * r;
    };
}', [re('case\s+Quadrato\s*\(\s*(double|var)\s+(\w+)\s*\)\s*->\s*\2\s*\*\s*\2\s*;', L('Il quadrato ha il suo caso', 'The square has its case'))], '', 'sealed interface Figura permits Quadrato, Cerchio {}
record Quadrato(double lato) implements Figura {}
record Cerchio(double r) implements Figura {}

static double area(Figura f) {
    return switch (f) {
        case Cerchio(double r) -> Math.PI * r * r;
        case Quadrato(double lato) -> lato * lato;
    };
}'), [
            dBlanks(pFill(), '___ interface Veicolo ___ Auto, Bici {}
record Auto(int posti) implements Veicolo {}
record Bici() implements Veicolo {}

String tipo = switch (v) {
    case Auto(int p) ___ p > 5 -> "Furgone";
    case Auto a -> "Auto";
    case Bici b -> "Bici";
};', ['sealed', 'permits', 'when'], ['final', 'extends', 'if'], L('Tipo chiuso, elenco dei sottotipi, condizione.', 'Closed type, list of subtypes, condition.')),
            dFlash(pFlash(), [[L('Un record pattern smonta un record nei suoi componenti', 'A record pattern takes a record apart into its components'), true], [L('Con un tipo sealed, un nuovo sottotipo fa segnalare gli switch incompleti', 'With a sealed type, a new subtype makes incomplete switches get flagged'), true], [L('`when` aggiunge una condizione a un caso', '`when` adds a condition to a case'), true], [L('Le classi sealed esistono da Java 8', 'Sealed classes have existed since Java 8'), false]], L('Sono definitive da Java 17, i record pattern da Java 21.', 'They are final since Java 17, record patterns since Java 21.')),
        ]),
        lesson('collectors', L('Collector: raggruppare e riassumere', 'Collectors: grouping and summarising'), [
            P('`collect` con i **Collectors** trasforma uno stream in mappe, testi e statistiche. `groupingBy` è il `GROUP BY` di SQL e accetta un secondo collector per riassumere ogni gruppo; `partitioningBy` divide in due gruppi, `true` e `false`; `joining` unisce testi.', '`collect` with **Collectors** turns a stream into maps, text and statistics. `groupingBy` is SQL’s `GROUP BY` and takes a second collector to summarise each group; `partitioningBy` splits into two groups, `true` and `false`; `joining` joins text.'),
            C('record Vendita(String regione, String prodotto, double importo) {}

Map<String, Double> perRegione = vendite.stream()
    .collect(Collectors.groupingBy(Vendita::regione, Collectors.summingDouble(Vendita::importo)));

Map<Boolean, List<Vendita>> grandiPiccole = vendite.stream()
    .collect(Collectors.partitioningBy(v -> v.importo() > 100));

String elenco = vendite.stream()
    .map(Vendita::prodotto).distinct().sorted()
    .collect(Collectors.joining(", ", "[", "]"));

Map<String, Long> quante = vendite.stream()
    .collect(Collectors.groupingBy(Vendita::regione, Collectors.counting()));

DoubleSummaryStatistics stat = vendite.stream().mapToDouble(Vendita::importo).summaryStatistics();', 'java'),
            P('`summaryStatistics` dà in un colpo solo conteggio, somma, minimo, massimo e media. Con `groupingBy` senza secondo collector ottieni per ogni chiave la lista dei suoi elementi.', '`summaryStatistics` gives count, sum, minimum, maximum and average in one go. With `groupingBy` and no second collector you get, for each key, the list of its items.'),
        ], [
            Q('co1', L('A che cosa corrisponde `groupingBy` in SQL?', 'What does `groupingBy` correspond to in SQL?'), ['ORDER BY', 'WHERE', 'JOIN', 'GROUP BY'], 3, L('Con un collector che riassume ogni gruppo.', 'With a collector summarising each group.')),
            Q('co2', L('Che chiavi ha la mappa di `partitioningBy`?', 'What keys does the `partitioningBy` map have?'), [L('I valori distinti dello stream', 'The distinct values of the stream'), L('Le posizioni 0, 1, 2…', 'The positions 0, 1, 2…'), L('true e false', 'true and false'), L('Le classi degli elementi', 'The classes of the items')], 2, L('Chi passa il test e chi no.', 'Those passing the test and those not.')),
            Q('co3', L('Che cosa dà `Stream.of("a", "b").collect(Collectors.joining("-"))`?', 'What does `Stream.of("a", "b").collect(Collectors.joining("-"))` give?'), ['"ab"', '"a-b"', '["a", "b"]', '"a-b-"'], 1, L('Il separatore va solo tra gli elementi.', 'The separator goes only between items.')),
        ], task('java', L('Conta i libri per autore in `Map<String, Long> perAutore` con `groupingBy(Libro::autore, Collectors.counting())`.', 'Count books per author into `Map<String, Long> perAutore` with `groupingBy(Libro::autore, Collectors.counting())`.'), 'record Libro(String titolo, String autore) {}
', [re('Map<\s*String\s*,\s*Long\s*>\s+perAutore\s*=\s*libri\.stream\s*\(\s*\)\s*\.collect\s*\(\s*Collectors\.groupingBy\s*\(\s*(Libro::autore|(\w+)\s*->\s*\2\.autore\s*\(\s*\))\s*,\s*Collectors\.counting\s*\(\s*\)\s*\)\s*\)', L('Raggruppi e conti', 'You group and count'))], '', 'Map<String, Long> perAutore = libri.stream()
    .collect(Collectors.groupingBy(Libro::autore, Collectors.counting()));'), [
            dMatch(pMatch(), [['groupingBy', 'GROUP BY'], ['counting()', 'COUNT(*)'], ['summingDouble', 'SUM'], ['joining', L('Unisce in un testo', 'Joins into text')], ['partitioningBy', L('Due gruppi: true e false', 'Two groups: true and false')]], L('Collectors e SQL a confronto.', 'Collectors and SQL compared.')),
            dOrder(pSort(), ['Map<String, Double> medie = studenti.stream()', '    .collect(Collectors.groupingBy(', '        Studente::classe,', '        Collectors.averagingDouble(Studente::voto)', '    ));'], L('Sorgente, raccolta, chiave, riassunto.', 'Source, collect, key, summary.')),
        ]),
        lesson('completable', L('CompletableFuture e HttpClient', 'CompletableFuture and HttpClient'), [
            P('Un **CompletableFuture** è un risultato che arriverà: con `thenApply` lo trasformi, con `thenCombine` lo unisci a un altro, con `exceptionally` gestisci l’errore, tutto senza bloccare. `join()` aspetta il risultato finale. L’`HttpClient` di `java.net.http` fa richieste sincrone con `send` o asincrone con `sendAsync`.', 'A **CompletableFuture** is a result still to come: with `thenApply` you transform it, with `thenCombine` you merge it with another, with `exceptionally` you handle the error, all without blocking. `join()` waits for the final result. The `HttpClient` of `java.net.http` makes synchronous requests with `send` or asynchronous ones with `sendAsync`.'),
            C('HttpClient client = HttpClient.newHttpClient();
HttpRequest richiesta = HttpRequest.newBuilder(URI.create("https://api.esempio.it/meteo")).build();

CompletableFuture<String> meteo = client
    .sendAsync(richiesta, HttpResponse.BodyHandlers.ofString())
    .thenApply(HttpResponse::body);

CompletableFuture<String> notizie = CompletableFuture.supplyAsync(() -> leggiNotizie());

String pagina = meteo
    .thenCombine(notizie, (m, n) -> m + "\n" + n)
    .exceptionally(e -> "Servizio non disponibile")
    .join();', 'java'),
            Tip('Con i virtual thread spesso basta codice sincrono semplice, eseguito in un thread virtuale. `CompletableFuture` resta utile per comporre più operazioni indipendenti.', 'With virtual threads plain synchronous code, run on a virtual thread, is often enough. `CompletableFuture` remains useful for composing several independent operations.'),
        ], [
            Q('cf1', L('Che cosa fa `thenApply`?', 'What does `thenApply` do?'), [L('Aspetta bloccando il thread', 'Waits blocking the thread'), L('Annulla l’operazione', 'Cancels the operation'), L('Trasforma il risultato quando arriva', 'Transforms the result when it arrives'), L('Lancia un’eccezione', 'Throws an exception')], 2, L('Restituisce un nuovo future.', 'It returns a new future.')),
            Q('cf2', L('Che cosa fa `exceptionally`?', 'What does `exceptionally` do?'), [L('Lancia sempre un’eccezione', 'Always throws an exception'), L('Dà un valore di riserva se qualcosa fallisce', 'Gives a fallback value if something fails'), L('Ripete l’operazione', 'Repeats the operation'), L('Rende l’operazione sincrona', 'Makes the operation synchronous')], 1, L('Come un catch per i future.', 'Like a catch for futures.')),
            Q('cf3', L('Quale metodo di `HttpClient` è asincrono?', 'Which `HttpClient` method is asynchronous?'), ['send', 'get', 'request', 'sendAsync'], 3, L('Restituisce un `CompletableFuture`.', 'It returns a `CompletableFuture`.')),
        ], task('java', L('Da `prezzo` (un `CompletableFuture<Double>`) ottieni con `thenApply` il prezzo ivato `p * 1.22`, aspettalo con `join()` e salvalo in `double lordo`.', 'From `prezzo` (a `CompletableFuture<Double>`) get the price with VAT `p * 1.22` via `thenApply`, wait for it with `join()` and save it in `double lordo`.'), 'CompletableFuture<Double> prezzo = CompletableFuture.supplyAsync(() -> 10.0);
', [re('double\s+lordo\s*=\s*prezzo\s*\.thenApply\s*\(\s*(\w+)\s*->\s*\1\s*\*\s*1\.22\s*\)\s*\.join\s*\(\s*\)\s*;', L('Trasformi e aspetti', 'You transform and wait'))], '', 'double lordo = prezzo.thenApply(p -> p * 1.22).join();'), [
            dMatch(pMatch(), [['supplyAsync', L('Avvia un compito', 'Starts a task')], ['thenApply', L('Trasforma', 'Transforms')], ['thenCombine', L('Unisce due risultati', 'Combines two results')], ['exceptionally', L('Gestisce l’errore', 'Handles the error')], ['join', L('Aspetta il risultato', 'Waits for the result')]], L('Comporre operazioni asincrone.', 'Composing asynchronous operations.')),
            dBug(pBug(), ['CompletableFuture<String> f = CompletableFuture.supplyAsync(() -> scarica());', 'String testo = f.thenApply(String::trim);', 'System.out.println(testo);'], 1, 'String testo = f.thenApply(String::trim).join();', L('`thenApply` restituisce un altro future, non la stringa: serve `join()`.', '`thenApply` returns another future, not the string: you need `join()`.')),
        ]),
        lesson('jvm-memory', L('JVM, memoria e garbage collector', 'The JVM, memory and the garbage collector'), [
            P('La JVM mette gli oggetti nello **heap**; il **garbage collector** libera quelli che nessuno raggiunge più. **G1** è il collector predefinito sulla maggior parte delle macchine, **ZGC** tiene le pause sotto il millisecondo anche con heap enormi. `-Xmx` fissa il massimo dello heap: superarlo dà `OutOfMemoryError`.', 'The JVM puts objects on the **heap**; the **garbage collector** frees those nobody can reach any more. **G1** is the default collector on most machines, **ZGC** keeps pauses below a millisecond even with huge heaps. `-Xmx` sets the maximum heap: going past it gives `OutOfMemoryError`.'),
            C('java -Xms256m -Xmx1g -jar app.jar
java -XX:+UseZGC -jar app.jar
jcmd <pid> GC.heap_info
java -XX:StartFlightRecording=duration=60s,filename=registrazione.jfr -jar app.jar', 'bash'),
            C('Runtime rt = Runtime.getRuntime();
long usataMb = (rt.totalMemory() - rt.freeMemory()) / 1_048_576;
System.out.println("Memoria usata: " + usataMb + " MB, massimo " + rt.maxMemory() / 1_048_576 + " MB");

Map<String, byte[]> cache = new HashMap<>();   // cresce senza limiti: perdita di memoria', 'java'),
            P('Anche con il GC esistono **perdite di memoria**: oggetti che restano raggiungibili senza servire, come una cache che cresce senza limiti. **Java Flight Recorder** (JFR) registra con pochissimo impatto che cosa fa l’applicazione, anche in produzione; JDK Mission Control lo mostra in grafici.', 'Even with a GC there are **memory leaks**: objects that stay reachable without being needed, such as a cache growing without limit. **Java Flight Recorder** (JFR) records what the application does with very little overhead, even in production; JDK Mission Control shows it as charts.'),
        ], [
            Q('jv1', L('Che cosa fa il garbage collector?', 'What does the garbage collector do?'), [L('Comprime i file JAR', 'Compresses JAR files'), L('Libera gli oggetti che nessuno può più raggiungere', 'Frees objects nobody can reach any more'), L('Cancella le variabili locali a fine metodo', 'Deletes local variables at the end of a method'), L('Ottimizza le query al database', 'Optimises database queries')], 1, L('Non devi liberare la memoria a mano.', 'You do not free memory by hand.')),
            Q('jv2', L('Che cosa imposta `-Xmx1g`?', 'What does `-Xmx1g` set?'), [L('La memoria minima del sistema', 'The system’s minimum memory'), L('La dimensione del file di log', 'The size of the log file'), L('Il numero di thread', 'The number of threads'), L('Il massimo dello heap della JVM', 'The JVM’s maximum heap')], 3, L('`-Xms` imposta invece la dimensione iniziale.', '`-Xms` sets the initial size instead.')),
            Q('jv3', L('Può esserci una perdita di memoria in Java?', 'Can there be a memory leak in Java?'), [L('No, il GC lo impedisce sempre', 'No, the GC always prevents it'), L('Solo nel codice nativo', 'Only in native code'), L('Sì, se gli oggetti restano raggiungibili senza servire', 'Yes, if objects stay reachable without being needed'), L('Solo con più di un thread', 'Only with more than one thread')], 2, L('Il GC libera solo ciò che nessuno raggiunge.', 'The GC frees only what nobody reaches.')),
        ], task('java', L('Calcola in `long liberaMb` la memoria libera in MB: `Runtime.getRuntime().freeMemory()` diviso `1_048_576`.', 'Compute in `long liberaMb` the free memory in MB: `Runtime.getRuntime().freeMemory()` divided by `1_048_576`.'), '', [re('long\s+liberaMb\s*=\s*Runtime\.getRuntime\s*\(\s*\)\.freeMemory\s*\(\s*\)\s*/\s*(1_?048_?576|\(\s*1024\s*\*\s*1024\s*\)|1024\s*/\s*1024)\s*;', L('Calcoli i MB liberi', 'You compute the free MB'))], '', 'long liberaMb = Runtime.getRuntime().freeMemory() / 1_048_576;'), [
            dMatch(pMatch(), [['heap', L('Dove vivono gli oggetti', 'Where objects live')], ['-Xmx', L('Massimo dello heap', 'Maximum heap')], ['G1', L('Collector predefinito', 'Default collector')], ['ZGC', L('Pause brevissime', 'Very short pauses')], ['JFR', L('Registra che cosa fa la JVM', 'Records what the JVM does')]], L('La JVM sotto il cofano.', 'The JVM under the bonnet.')),
            dFlash(pFlash(), [[L('`OutOfMemoryError` arriva quando lo heap non basta', '`OutOfMemoryError` comes when the heap is not enough'), true], [L('Il GC libera anche gli oggetti ancora usati', 'The GC also frees objects still in use'), false], [L('Una cache senza limiti può causare una perdita di memoria', 'An unbounded cache can cause a memory leak'), true], [L('JFR rallenta molto l’applicazione e si usa solo in sviluppo', 'JFR slows the application a lot and is used only in development'), false]], L('Misura prima di cambiare le opzioni della JVM.', 'Measure before changing JVM options.')),
        ]),
    ],
];
