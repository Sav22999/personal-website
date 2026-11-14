<?php
/** Java advanced: generics, lambdas and streams, Optional, concurrency and testing. */

return [
    'id' => 'java-advanced', 'family' => 'java', 'after' => 'java-intermediate',
    'title' => L('Java avanzato', 'Advanced Java'), 'short' => L('Java avanzato', 'Java Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Generics, lambda e stream, Optional, programmazione concorrente e test con JUnit.', 'Generics, lambdas and streams, Optional, concurrent programming and tests with JUnit.'),
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
            P('**JUnit 5** è lo standard per i test in Java. Un metodo segnato con `@Test` verifica il codice con le asserzioni: `assertEquals`, `assertTrue`, `assertThrows`.', '**JUnit 5** is Java’s standard for tests. A method marked `@Test` checks code with assertions: `assertEquals`, `assertTrue`, `assertThrows`.'),
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
void somma() {
    assertEquals(4, somma(2, 2));
}'), [
            dOrder(pSort(), ['@Test', 'void scontoDelDieciPerCento() {', '    var p = new Prezzo(100);', '    p.sconto(10);', '    assertEquals(90, p.valore());', '}'], L('Annotazione, prepara, esegui, verifica.', 'Annotation, arrange, act, assert.')),
            dBlanks(pFill(), "@___\nvoid vuoto() {\n    ___(new Carrello().vuoto());\n    ___(IllegalStateException.class, () -> new Carrello().paga());\n}", ['Test', 'assertTrue', 'assertThrows'], ['Check', 'assertEquals', 'expect'], L('Annotazione e asserzioni.', 'Annotation and assertions.')),
        ]),
    ],
];
