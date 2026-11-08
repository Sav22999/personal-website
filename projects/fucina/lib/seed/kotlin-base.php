<?php
/** Kotlin basics: val and var, null safety, control flow, collections and functions. */

return [
    'id' => 'kotlin-base', 'family' => 'kotlin', 'after' => null,
    'title' => L('Kotlin base', 'Kotlin Basics'), 'short' => L('Kotlin base', 'Kotlin Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio ufficiale di Android: `val` e `var`, sicurezza sui null, `when`, collezioni e funzioni.', 'The official language of Android: `val` and `var`, null safety, `when`, collections and functions.'),
    'lessons' => [
        lesson('values', L('val e var', 'val and var'), [
            P('`val` crea un valore che non cambia, `var` una variabile. Il tipo si deduce o si scrive dopo i due punti. I **template** inseriscono valori nel testo con `$nome` o `${espressione}`.', '`val` creates a value that does not change, `var` a variable. The type is inferred or written after a colon. **Templates** put values in text with `$nome` or `${expression}`.'),
            C('fun main() {
    val nome = "Ada"
    var anni: Int = 36
    anni++
    println("Ciao $nome, hai ${anni} anni")
}', 'kotlin'),
            P('Niente punti e virgola a fine riga. Il programma parte da `fun main()`.', 'No semicolons at the end of lines. The program starts from `fun main()`.'),
        ], [
            Q('v1', L('Quale parola crea un valore che non cambia?', 'Which word creates a value that does not change?'), ['val', 'var', 'const', 'let'], 0, L('`var` si può riassegnare.', '`var` can be reassigned.')),
            Q('v2', L('Come inserisci `x` nel testo?', 'How do you put `x` into text?'), ['"$x"', '"{x}"', '"#{x}"', '"\\(x)"'], 0, L('Per espressioni `${…}`.', 'For expressions `${…}`.')),
            Q('v3', L('Quale funzione stampa una riga?', 'Which function prints a line?'), ['println', 'print_line', 'echo', 'log'], 0, L('`print` non va a capo.', '`print` adds no newline.')),
        ], task('kotlin', L('Dichiara `val corso = "Kotlin"` e stampa `"Studio $corso"`.', 'Declare `val corso = "Kotlin"` and print `"Studio $corso"`.'), 'fun main() {
}', [re('val\s+corso\s*(:\s*String\s*)?=\s*"Kotlin"', L('Il valore esiste', 'The value exists')), re('println\s*\(\s*"Studio \$corso"\s*\)', L('Lo stampi', 'You print it'))], '', 'val corso = "Kotlin"
println("Studio $corso")'), [
            dMatch(pMatch(), [['val', L('Non cambia', 'Does not change')], ['var', L('Può cambiare', 'Can change')], ['fun', L('Funzione', 'Function')], ['Int', L('Intero', 'Integer')], ['String', L('Testo', 'Text')]], L('Parole di base di Kotlin.', 'Basic Kotlin words.')),
            dFlash(pFlash(), [[L('In Kotlin servono i punti e virgola', 'Kotlin needs semicolons'), false], [L('`val` non si può riassegnare', '`val` cannot be reassigned'), true], [L('Il tipo si può dedurre', 'The type can be inferred'), true]], L('Niente punti e virgola.', 'No semicolons.')),
        ]),
        lesson('nulls', L('Sicurezza sui null', 'Null safety'), [
            P('Un tipo con il punto di domanda (`String?`) può essere `null`. Per usarlo ci sono la **chiamata sicura** `?.`, l’**operatore Elvis** `?:` per un valore di riserva, e i controlli con `if`.', 'A type with a question mark (`String?`) can be `null`. To use it there is the **safe call** `?.`, the **Elvis operator** `?:` for a fallback, and checks with `if`.'),
            C('var soprannome: String? = null

val lunghezza = soprannome?.length        // null
val mostrato = soprannome ?: "Ospite"     // "Ospite"

if (soprannome != null) {
    println(soprannome.uppercase())       // qui è sicuro
}', 'kotlin'),
            P('`!!` forza il valore e lancia un’eccezione se è `null`: va evitato.', '`!!` forces the value and throws if it is `null`: avoid it.'),
        ], [
            Q('n1', L('Che cosa fa `a?.length` se `a` è null?', 'What does `a?.length` do if `a` is null?'), [L('Dà null', 'Gives null'), L('Lancia un errore', 'Throws'), L('Dà 0', 'Gives 0'), L('Dà ""', 'Gives ""')], 0, L('La chiamata sicura si ferma.', 'The safe call stops.')),
            Q('n2', L('Come si chiama `?:`', 'What is `?:` called?'), [L('Operatore Elvis', 'Elvis operator'), L('Ternario', 'Ternary'), L('Pipe', 'Pipe'), L('Lambda', 'Lambda')], 0, L('Dà un valore di riserva.', 'It gives a fallback.')),
            Q('n3', L('Perché evitare `!!`?', 'Why avoid `!!`?'), [L('Lancia un’eccezione se è null', 'It throws if null'), L('È lento', 'It is slow'), L('Non esiste', 'It does not exist'), L('È deprecato', 'It is deprecated')], 0, L('Meglio `?.` e `?:`.', 'Better `?.` and `?:`.')),
        ], task('kotlin', L('Crea `val nome = input ?: "Anonimo"`.', 'Create `val nome = input ?: "Anonimo"`.'), 'val input: String? = readlnOrNull()
', [re('val\s+nome\s*=\s*input\s*\?:\s*"Anonimo"', L('Usi Elvis', 'You use Elvis'))], '', 'val nome = input ?: "Anonimo"'), [
            dBug(pBug(), ['val email: String? = trovaEmail()', 'val dominio = email!!.substringAfter("@")', 'println(dominio)'], 1, 'val dominio = email?.substringAfter("@") ?: "—"', L('Con `!!` l’app si ferma se manca l’email.', 'With `!!` the app stops if the email is missing.')),
            dBlanks(pFill(), "val citta: String___ = null\nval n = citta___.length ___ 0", ['?', '?', '?:'], ['!', '.', '||'], L('Tipo nullable, chiamata sicura, riserva.', 'Nullable type, safe call, fallback.')),
        ]),
        lesson('control', L('if e when', 'if and when'), [
            P('In Kotlin `if` è un’**espressione**: restituisce un valore. `when` sostituisce lo `switch` ed è molto più flessibile: accetta valori, intervalli e condizioni.', 'In Kotlin `if` is an **expression**: it returns a value. `when` replaces `switch` and is far more flexible: it accepts values, ranges and conditions.'),
            C('val voto = 8
val esito = if (voto >= 6) "promosso" else "bocciato"

val giudizio = when (voto) {
    10, 9 -> "Ottimo"
    in 6..8 -> "Buono"
    else -> "Da ripassare"
}

for (i in 1..3) println(i)
for (i in 10 downTo 0 step 5) println(i)', 'kotlin'),
            P('`1..3` include il 3; `until` lo esclude; `downTo` e `step` contano all’indietro o a salti.', '`1..3` includes 3; `until` excludes it; `downTo` and `step` count backwards or in jumps.'),
        ], [
            Q('c1', L('Che cosa sostituisce `when`?', 'What does `when` replace?'), ['switch', 'if', 'for', 'try'], 0, L('Con molte più possibilità.', 'With many more options.')),
            Q('c2', L('`1..5` include il 5?', 'Does `1..5` include 5?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo con `step`', 'Only with `step`'), L('Dipende', 'It depends')], 0, L('Per escluderlo c’è `until`.', 'To exclude it use `until`.')),
            Q('c3', L('`if` in Kotlin restituisce un valore?', 'Does `if` in Kotlin return a value?'), [L('Sì, è un’espressione', 'Yes, it is an expression'), L('No', 'No'), L('Solo nei cicli', 'Only in loops'), L('Solo con `when`', 'Only with `when`')], 0, L('Niente operatore ternario.', 'No ternary operator.')),
        ], task('kotlin', L('Scrivi `val taglia = when { altezza < 160 -> "S"; altezza < 180 -> "M"; else -> "L" }` su più righe.', 'Write `val taglia = when { altezza < 160 -> "S"; altezza < 180 -> "M"; else -> "L" }` on several lines.'), 'val altezza = 172
', [re('val\s+taglia\s*=\s*when\s*\{', L('Usi when', 'You use when')), re('altezza\s*<\s*160\s*->\s*"S"', L('Primo caso', 'First case')), re('else\s*->\s*"L"', L('Caso finale', 'Last case'))], '', 'val taglia = when {
    altezza < 160 -> "S"
    altezza < 180 -> "M"
    else -> "L"
}'), [
            dOrder(pSort(), ['val messaggio = when (ora) {', '    in 5..11 -> "Buongiorno"', '    in 12..17 -> "Buon pomeriggio"', '    else -> "Buonasera"', '}'], L('Soggetto, casi, riserva.', 'Subject, cases, fallback.')),
            dMatch(pMatch(), [['1..5', L('Da 1 a 5 incluso', 'From 1 to 5 inclusive')], ['1 until 5', L('Da 1 a 4', 'From 1 to 4')], ['5 downTo 1', L('Da 5 a 1', 'From 5 to 1')], ['step 2', L('A salti di due', 'In steps of two')]], L('Gli intervalli di Kotlin.', 'Kotlin ranges.')),
        ]),
        lesson('collections', L('Collezioni', 'Collections'), [
            P('`listOf` crea una lista che non si modifica, `mutableListOf` una che si modifica. Lo stesso per `mapOf`/`mutableMapOf` e `setOf`. Le funzioni `filter`, `map`, `sumOf` lavorano con le **lambda** e `it`.', '`listOf` makes a list that cannot change, `mutableListOf` one that can. The same goes for `mapOf`/`mutableMapOf` and `setOf`. `filter`, `map`, `sumOf` work with **lambdas** and `it`.'),
            C('val voti = listOf(6, 8, 9, 5)
val promossi = voti.filter { it >= 6 }
val doppi = voti.map { it * 2 }

val carrello = mutableListOf("libro")
carrello.add("penna")

val eta = mapOf("Ada" to 36, "Bea" to 28)
println(eta["Ada"])', 'kotlin'),
            P('`it` è il nome automatico dell’unico parametro di una lambda. `to` crea una coppia chiave-valore.', '`it` is the automatic name of a lambda’s single parameter. `to` creates a key-value pair.'),
        ], [
            Q('k1', L('Si può fare `add` su `listOf(...)`?', 'Can you `add` to `listOf(...)`?'), [L('No, serve `mutableListOf`', 'No, you need `mutableListOf`'), L('Sì', 'Yes'), L('Solo stringhe', 'Only strings'), L('Solo in main', 'Only in main')], 0, L('Le liste di base sono immutabili.', 'Plain lists are immutable.')),
            Q('k2', L('Che cos’è `it` in una lambda?', 'What is `it` in a lambda?'), [L('L’unico parametro', 'The single parameter'), L('Una variabile globale', 'A global variable'), L('Il risultato', 'The result'), L('Un errore', 'An error')], 0, L('Nome automatico.', 'Automatic name.')),
            Q('k3', L('Che cosa crea `"Ada" to 36`?', 'What does `"Ada" to 36` create?'), [L('Una coppia', 'A pair'), L('Un intervallo', 'A range'), L('Una stringa', 'A string'), L('Un errore', 'An error')], 0, L('Usata nelle mappe.', 'Used in maps.')),
        ], task('kotlin', L('Da `prezzi` ottieni `val scontati = prezzi.map { it * 0.9 }`.', 'From `prezzi` get `val scontati = prezzi.map { it * 0.9 }`.'), 'val prezzi = listOf(10.0, 20.0)
', [re('val\s+scontati\s*=\s*prezzi\.map\s*\{\s*it\s*\*\s*0\.9\s*\}', L('La trasformazione è giusta', 'The transformation is right'))], '', 'val scontati = prezzi.map { it * 0.9 }'), [
            dBlanks(pFill(), "val nomi = ___(\"Ada\")\nnomi.add(\"Bea\")\nval lunghi = nomi.___ { ___.length > 3 }", ['mutableListOf', 'filter', 'it'], ['listOf', 'where', 'this'], L('Lista modificabile, filtro, parametro.', 'Mutable list, filter, parameter.')),
            dFlash(pFlash(), [[L('`mapOf` usa `to` per le coppie', '`mapOf` uses `to` for pairs'), true], [L('`listOf` si può modificare', '`listOf` can be modified'), false], [L('`sumOf { it.prezzo }` somma un campo', '`sumOf { it.prezzo }` adds up a field'), true]], L('Per modificare serve `mutable`.', 'To modify you need `mutable`.')),
        ]),
        lesson('functions', L('Funzioni', 'Functions'), [
            P('`fun` crea una funzione; i parametri hanno tipo, possono avere valori predefiniti e si possono passare per nome. Una funzione di una sola espressione si scrive con `=`.', '`fun` creates a function; parameters are typed, can have default values and can be passed by name. A single-expression function is written with `=`.'),
            C('fun area(base: Double, altezza: Double = 1.0): Double = base * altezza

fun saluta(nome: String, punto: String = "!") {
    println("Ciao $nome$punto")
}

area(3.0, 2.0)
area(altezza = 4.0, base = 2.0)
saluta("Ada")

fun String.urla() = uppercase() + "!"
println("ciao".urla())', 'kotlin'),
            P('Le **funzioni di estensione** aggiungono metodi a tipi esistenti, come `String.urla()`.', '**Extension functions** add methods to existing types, like `String.urla()`.'),
        ], [
            Q('f1', L('Come si scrive una funzione di una sola espressione?', 'How do you write a single-expression function?'), ['fun f() = 42', 'fun f() -> 42', 'fun f(): 42', 'f = () => 42'], 0, L('Con il segno uguale.', 'With an equals sign.')),
            Q('f2', L('Che cosa sono gli argomenti con nome?', 'What are named arguments?'), [L('Passare un parametro scrivendone il nome', 'Passing a parameter by writing its name'), L('Variabili globali', 'Global variables'), L('Costanti', 'Constants'), L('Commenti', 'Comments')], 0, L('L’ordine non conta più.', 'Order no longer matters.')),
            Q('f3', L('Che cosa fa una funzione di estensione?', 'What does an extension function do?'), [L('Aggiunge un metodo a un tipo esistente', 'Adds a method to an existing type'), L('Allunga una funzione', 'Lengthens a function'), L('Crea una sottoclasse', 'Creates a subclass'), L('Niente', 'Nothing')], 0, L('Senza toccare il tipo originale.', 'Without touching the original type.')),
        ], task('kotlin', L('Scrivi `fun doppio(n: Int): Int = n * 2`.', 'Write `fun doppio(n: Int): Int = n * 2`.'), '', [re('fun\s+doppio\s*\(\s*n\s*:\s*Int\s*\)\s*(:\s*Int\s*)?(=\s*n\s*\*\s*2|\{\s*return\s+n\s*\*\s*2)', L('La funzione è giusta', 'The function is right'))], '', 'fun doppio(n: Int): Int = n * 2'), [
            dOrder(pSort(), ['fun media(voti: List<Int>): Double {', '    if (voti.isEmpty()) return 0.0', '    return voti.sum().toDouble() / voti.size', '}', 'println(media(listOf(6, 8)))'], L('Firma, controllo, calcolo, chiamata.', 'Signature, check, calculation, call.')),
            dMatch(pMatch(), [['fun', L('Crea una funzione', 'Creates a function')], ['= espressione', L('Corpo di una riga', 'One-line body')], ['Unit', L('Nessun risultato', 'No result')], ['String.x()', L('Estensione', 'Extension')]], L('Funzioni in Kotlin.', 'Functions in Kotlin.')),
        ]),
    ],
];
