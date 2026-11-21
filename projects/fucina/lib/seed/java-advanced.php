<?php
/** Java advanced: generics, lambdas and streams, Optional, concurrency, testing, sealed types and patterns, collectors, CompletableFuture and the JVM. */

/** A stream pipeline: filter drops "di", map turns names upper case, toList collects. */
if (!function_exists('fig_javaadv_stream')) {
    function fig_javaadv_stream(): string
    {
        $col = function (int $x, array $items, string $fill, string $stroke) {
            $o = '';
            foreach ($items as $i => $it) {
                $y = 50 + $i * 40;
                $gone = str_starts_with($it, '-');
                $txt = ltrim($it, '-');
                $o .= "<rect x=\"$x\" y=\"$y\" width=\"110\" height=\"32\" rx=\"8\" fill=\"" . ($gone ? '#F1F0F8' : $fill) . "\" stroke=\"" . ($gone ? '#C9C7DE' : $stroke) . "\" stroke-width=\"2\"" . ($gone ? ' stroke-dasharray="4 4"' : '') . "/><text x=\"" . ($x + 55) . "\" y=\"" . ($y + 21) . "\" text-anchor=\"middle\"" . ($gone ? ' fill="#8A87B0" text-decoration="line-through"' : '') . ">$txt</text>";
            }
            return $o;
        };
        $a = $col(20, ['"ada"', '"bea"', '"carlo"', '"di"'], '#DCEBFF', '#2F8CF0');
        $b = $col(180, ['"ada"', '"bea"', '"carlo"', '-"di"'], '#DCEBFF', '#2F8CF0');
        $c = $col(340, ['"ADA"', '"BEA"', '"CARLO"'], '#ECE6FF', '#7048E8');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="ui-monospace, monospace" font-size="13" fill="#1E1C52">
<defs><marker id="as" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g text-anchor="middle" font-weight="700"><text x="75" y="32">nomi</text><text x="235" y="32" fill="#B84508">.filter(len &gt; 2)</text><text x="395" y="32" fill="#5B33C9">.map(toUpperCase)</text><text x="560" y="32" fill="#0B7F5C">.toList()</text></g>
$a $b $c
<line x1="134" y1="126" x2="174" y2="126" stroke="#1E1C52" stroke-width="2" marker-end="url(#as)"/>
<line x1="294" y1="106" x2="334" y2="106" stroke="#1E1C52" stroke-width="2" marker-end="url(#as)"/>
<line x1="454" y1="106" x2="494" y2="106" stroke="#1E1C52" stroke-width="2" marker-end="url(#as)"/>
<rect x="500" y="70" width="124" height="72" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="562" y="96" text-anchor="middle">["ADA",</text><text x="562" y="114" text-anchor="middle">"BEA",</text><text x="562" y="132" text-anchor="middle">"CARLO"]</text>
</svg>
SVG;
    }
}

/** Optional: map works only when a value is there, orElse supplies one when it is not. */
if (!function_exists('fig_javaadv_opt')) {
    function fig_javaadv_opt(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 200" font-family="ui-monospace, monospace" font-size="13" fill="#1E1C52">
<defs><marker id="ao" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g text-anchor="middle" font-weight="700"><text x="198" y="30" fill="#5B33C9">.map(Utente::nome)</text><text x="428" y="30" fill="#B84508">.orElse("Ospite")</text></g>
<rect x="16" y="48" width="150" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="91" y="73" text-anchor="middle">Optional[Ada…]</text>
<rect x="232" y="48" width="150" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="307" y="73" text-anchor="middle">Optional["Ada"]</text>
<rect x="476" y="48" width="148" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="550" y="73" text-anchor="middle" font-weight="700">"Ada"</text>
<rect x="16" y="128" width="150" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/><text x="91" y="153" text-anchor="middle" fill="#5B5880">Optional.empty</text>
<rect x="232" y="128" width="150" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/><text x="307" y="153" text-anchor="middle" fill="#5B5880">Optional.empty</text>
<rect x="476" y="128" width="148" height="40" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="550" y="153" text-anchor="middle" font-weight="700">"Ospite"</text>
<g stroke="#1E1C52" stroke-width="2"><line x1="170" y1="68" x2="226" y2="68" marker-end="url(#ao)"/><line x1="386" y1="68" x2="470" y2="68" marker-end="url(#ao)"/><line x1="170" y1="148" x2="226" y2="148" marker-end="url(#ao)"/><line x1="386" y1="148" x2="470" y2="148" marker-end="url(#ao)"/></g>
</svg>
SVG;
    }
}

/** A race: two threads read the same value and one increment gets lost. */
if (!function_exists('fig_javaadv_race')) {
    function fig_javaadv_race(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="13" fill="#1E1C52">
<text x="16" y="62" font-weight="700" font-family="ui-monospace, monospace" fill="#1B66BC">T1</text>
<text x="16" y="122" font-weight="700" font-family="ui-monospace, monospace" fill="#B84508">T2</text>
<line x1="50" y1="57" x2="430" y2="57" stroke="#C9C7DE" stroke-width="2"/><line x1="50" y1="117" x2="430" y2="117" stroke="#C9C7DE" stroke-width="2"/>
<g text-anchor="middle">
<rect x="56" y="40" width="96" height="34" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="104" y="62">{$t[0]}</text>
<rect x="168" y="40" width="64" height="34" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="200" y="62">+1</text>
<rect x="248" y="40" width="96" height="34" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="296" y="62">{$t[1]}</text>
<rect x="100" y="100" width="96" height="34" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="148" y="122">{$t[0]}</text>
<rect x="212" y="100" width="64" height="34" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="244" y="122">+1</text>
<rect x="320" y="100" width="96" height="34" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="368" y="122">{$t[1]}</text>
</g>
<g font-family="ui-monospace, monospace" font-size="15" text-anchor="middle">
<text x="104" y="180">5</text><text x="296" y="180">6</text><text x="368" y="180" fill="#B8255F" font-weight="700">6</text>
</g>
<text x="16" y="180" font-family="ui-monospace, monospace">contatore</text>
<text x="240" y="208" text-anchor="middle" fill="#B8255F" font-weight="700">{$t[2]}</text>
<line x1="450" y1="20" x2="450" y2="230" stroke="#D9D7EA" stroke-width="2"/>
<text x="537" y="56" text-anchor="middle" font-family="ui-monospace, monospace" font-weight="700">AtomicInteger</text>
<rect x="470" y="76" width="134" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="537" y="101" text-anchor="middle" font-family="ui-monospace, monospace">5 → 6 → 7</text>
<text x="537" y="142" text-anchor="middle" fill="#0B7F5C">{$t[3]}</text>
</svg>
SVG;
    }
}

/** The heap: objects reachable from the roots stay, the others are collected. */
if (!function_exists('fig_javaadv_gc')) {
    function fig_javaadv_gc(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="ui-monospace, monospace" font-size="13" fill="#1E1C52">
<defs><marker id="ag" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="80" y="30" text-anchor="middle" font-family="system-ui, sans-serif" font-weight="700" font-size="14" fill="#5B33C9">{$t[0]}</text>
<rect x="16" y="44" width="128" height="150" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="30" y="64" width="100" height="32" rx="8" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/><text x="80" y="85" text-anchor="middle">carrello</text>
<rect x="30" y="140" width="100" height="32" rx="8" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/><text x="80" y="161" text-anchor="middle">utente</text>
<text x="400" y="30" text-anchor="middle" font-family="system-ui, sans-serif" font-weight="700" font-size="14" fill="#1B66BC">heap</text>
<rect x="180" y="44" width="444" height="196" rx="14" fill="#F7FAFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="200" y="62" width="110" height="34" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="255" y="84" text-anchor="middle">Carrello</text>
<rect x="350" y="62" width="110" height="34" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="405" y="84" text-anchor="middle">ArrayList</text>
<rect x="500" y="62" width="110" height="34" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="555" y="84" text-anchor="middle">Prodotto</text>
<rect x="200" y="138" width="110" height="34" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="255" y="160" text-anchor="middle">Utente</text>
<rect x="380" y="138" width="110" height="34" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/><text x="435" y="160" text-anchor="middle" fill="#8A87B0">String</text>
<rect x="500" y="138" width="110" height="34" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/><text x="555" y="160" text-anchor="middle" fill="#8A87B0">byte[]</text>
<path d="M490 155 H500" stroke="#8A87B0" stroke-width="2"/>
<g stroke="#1E1C52" stroke-width="2" fill="none">
<path d="M134 80 H194" marker-end="url(#ag)"/><path d="M134 156 H194" marker-end="url(#ag)"/>
<path d="M314 79 H344" marker-end="url(#ag)"/><path d="M464 79 H494" marker-end="url(#ag)"/>
</g>
<text x="300" y="208" font-family="system-ui, sans-serif" fill="#1B66BC">■ {$t[1]}</text>
<text x="300" y="228" font-family="system-ui, sans-serif" fill="#5B5880">▢ {$t[2]}</text>
</svg>
SVG;
    }
}

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
            P('Senza generics una scatola dovrebbe contenere `Object`: ogni `prendi()` richiederebbe un cast, e un errore salterebbe fuori solo mentre il programma gira, come `ClassCastException`. Con `Scatola<String>` il compilatore rifiuta subito `s.metti(42)`. Il diamante `<>` evita di ripetere il tipo a destra. I generics esistono solo per il compilatore: a runtime il tipo viene cancellato, per questo non puoi scrivere `new T()`.', 'Without generics a box would have to hold `Object`: every `prendi()` would need a cast, and a mistake would only show up while the program runs, as a `ClassCastException`. With `Scatola<String>` the compiler rejects `s.metti(42)` straight away. The diamond `<>` saves repeating the type on the right. Generics exist only for the compiler: at runtime the type is erased, which is why you cannot write `new T()`.'),
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
            Fig(fig_javaadv_stream(),
                L('Quattro colonne. La lista nomi con ada, bea, carlo e di; dopo filter con lunghezza maggiore di 2 resta tutto tranne di, barrato; dopo map i nomi diventano ADA, BEA e CARLO; toList li raccoglie nella lista ["ADA", "BEA", "CARLO"].', 'Four columns. The list nomi with ada, bea, carlo and di; after filter with length greater than 2 everything stays except di, struck out; after map the names become ADA, BEA and CARLO; toList collects them into the list ["ADA", "BEA", "CARLO"].'),
                L('Ogni passo riceve ciò che esce dal passo prima.', 'Each step receives what comes out of the step before.')),
            P('Le operazioni come `filter`, `map` e `sorted` sono **intermedie**: da sole non fanno niente, preparano solo la catena. Il lavoro parte quando arriva un’operazione **finale** come `toList`, `count`, `sum` o `forEach`. Uno stream si usa una volta sola: per un secondo giro ne chiedi uno nuovo con `nomi.stream()`. Dentro una lambda puoi leggere le variabili esterne, ma non modificarle.', 'Operations such as `filter`, `map` and `sorted` are **intermediate**: on their own they do nothing, they just prepare the chain. The work starts when a **terminal** operation such as `toList`, `count`, `sum` or `forEach` arrives. A stream is used once: for a second pass you ask for a new one with `nomi.stream()`. Inside a lambda you can read outer variables, but not change them.'),
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
            Fig(fig_javaadv_opt(),
                L('Due strade. In alto Optional con un utente Ada passa per map(Utente::nome) e diventa Optional["Ada"], poi orElse dà "Ada". In basso Optional.empty resta vuoto dopo map, e orElse dà "Ospite".', 'Two paths. At the top an Optional with the user Ada goes through map(Utente::nome) and becomes Optional["Ada"], then orElse gives "Ada". At the bottom Optional.empty stays empty after map, and orElse gives "Ospite".'),
                L('`map` lavora solo se il valore c’è; `orElse` interviene solo se manca.', '`map` works only if the value is there; `orElse` steps in only if it is missing.')),
            P('Evita `get()` senza controlli: su un Optional vuoto lancia un’eccezione e riporta il problema da cui volevi scappare. Se l’assenza è un errore, scrivi `orElseThrow()`, che dice chiaramente che cosa ti aspetti. Nota che `orElse(valore)` calcola il valore sempre, anche quando non serve; se costa caro, ad esempio una query, usa `orElseGet(() -> …)`, che lo calcola solo se manca.', 'Avoid `get()` without checks: on an empty Optional it throws an exception and brings back the very problem you wanted to escape. If absence is an error, write `orElseThrow()`, which clearly says what you expect. Note that `orElse(value)` always computes the value, even when it is not needed; if that is costly, a query for example, use `orElseGet(() -> …)`, which computes it only when missing.'),
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
            P('Perché si mescolano? Anche `contatore++` sono tre passi: leggere il valore, aggiungere uno, riscriverlo. Se due thread leggono lo stesso 5 prima che l’altro abbia scritto, entrambi scrivono 6 e un incremento sparisce. `AtomicInteger.incrementAndGet()` fa i tre passi come un’operazione unica. I virtual thread sono ideali quando i compiti passano il tempo ad aspettare (rete, database); `f.get()` blocca finché il risultato non arriva.', 'Why do they get mixed up? Even `contatore++` is three steps: read the value, add one, write it back. If two threads read the same 5 before the other has written, both write 6 and one increment vanishes. `AtomicInteger.incrementAndGet()` does the three steps as a single operation. Virtual threads are ideal when tasks spend their time waiting (network, database); `f.get()` blocks until the result arrives.'),
            Fig(L(fig_javaadv_race(['legge 5', 'scrive 6', 'un incremento perso', 'un’operazione sola']), fig_javaadv_race(['reads 5', 'writes 6', 'one increment lost', 'a single operation'])),
                L('Due thread, T1 e T2, su due linee del tempo. Entrambi leggono 5, aggiungono 1 e scrivono 6, così il contatore passa da 5 a 6 invece che a 7. A destra, con AtomicInteger, il contatore va da 5 a 6 a 7.', 'Two threads, T1 and T2, on two timelines. Both read 5, add 1 and write 6, so the counter goes from 5 to 6 instead of 7. On the right, with AtomicInteger, the counter goes from 5 to 6 to 7.'),
                L('Leggere e scrivere separati lasciano un varco; l’operazione atomica lo chiude.', 'Separate read and write leave a gap; the atomic operation closes it.')),
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
            P('JUnit crea un oggetto nuovo della classe di test per ogni metodo, così i test non si passano dati tra loro; ciò che serve a tutti si prepara in un metodo con `@BeforeEach`. Dai ai test nomi che descrivono il comportamento, come `importoNegativo`, e prova i casi limite. Con `@ParameterizedTest` lo stesso test gira su più valori. Si lanciano con `mvn test` o `./gradlew test`, e nell’integrazione continua a ogni push.', 'JUnit creates a new instance of the test class for every method, so tests do not pass data to each other; whatever they all need is prepared in a method with `@BeforeEach`. Give tests names that describe the behaviour, such as `importoNegativo`, and test edge cases. With `@ParameterizedTest` the same test runs over several values. They run with `mvn test` or `./gradlew test`, and in continuous integration on every push.'),
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
            Fig(L(fig_javaadv_gc(['Radici (stack)', 'raggiungibili da una radice: restano', 'irraggiungibili: il GC li libera']), fig_javaadv_gc(['Roots (stack)', 'reachable from a root: they stay', 'unreachable: the GC frees them'])),
                L('A sinistra le radici, le variabili carrello e utente. Nello heap, carrello punta a un oggetto Carrello, che punta a un ArrayList, che punta a un Prodotto; utente punta a un Utente. Un String e un byte[] tratteggiati non sono raggiunti da nessuna freccia.', 'On the left the roots, the variables carrello and utente. In the heap, carrello points to a Carrello object, which points to an ArrayList, which points to a Prodotto; utente points to an Utente. A dashed String and byte[] are reached by no arrow.'),
                L('Il GC parte dalle radici e libera ciò che non riesce a raggiungere.', 'The GC starts from the roots and frees whatever it cannot reach.')),
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
