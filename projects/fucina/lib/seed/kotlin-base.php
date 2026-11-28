<?php
/** Kotlin basics: val and var, null safety, control flow, collections, functions, strings, lambdas and exceptions. */

/** A table: what ?., ?: and !! give when soprannome holds "Ada" and when it is null. */
if (!function_exists('fig_kotlinbase_nulls')) {
    function fig_kotlinbase_nulls(string $guest): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 190" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g font-weight="700" text-anchor="middle">
<rect x="200" y="12" width="130" height="44" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="265" y="39">?.length</text>
<rect x="338" y="12" width="130" height="44" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="403" y="39">?: "{$guest}"</text>
<rect x="476" y="12" width="152" height="44" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="552" y="39">!!.length</text>
</g>
<text x="16" y="96">soprannome = <tspan font-weight="700">"Ada"</tspan></text>
<text x="16" y="156">soprannome = <tspan font-weight="700">null</tspan></text>
<g stroke-width="2">
<rect x="200" y="66" width="130" height="50" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="338" y="66" width="130" height="50" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="476" y="66" width="152" height="50" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="200" y="126" width="130" height="50" rx="10" fill="#FFFFFF" stroke="#8A87B0" stroke-dasharray="5 4"/>
<rect x="338" y="126" width="130" height="50" rx="10" fill="#FFF3EA" stroke="#E8590C"/>
<rect x="476" y="126" width="152" height="50" rx="10" fill="#FFE3EE" stroke="#F03E82"/>
</g>
<g text-anchor="middle">
<text x="265" y="96" font-weight="700">3</text><text x="403" y="96" font-weight="700">"Ada"</text><text x="552" y="96" font-weight="700">3</text>
<text x="265" y="156" fill="#5F5C8A">null</text><text x="403" y="156" font-weight="700" fill="#B84508">"{$guest}"</text>
<text x="552" y="147" font-weight="700" fill="#C2185B">crash</text><text x="552" y="166" font-size="13" fill="#C2185B">NullPointerException</text>
</g>
</svg>
SVG;
    }
}

/** try / catch / finally: with no error the value comes out of try, with an exception catch runs; finally runs in both cases. */
if (!function_exists('fig_kotlinbase_trycatch')) {
    function fig_kotlinbase_trycatch(string $value, string $noError, string $exception, string $always): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g stroke-width="2" fill="none">
<path d="M190 104 C220 104 220 48 244 48" stroke="#0B7F5C"/><path d="M236 42 L246 48 L236 54" stroke="#0B7F5C"/>
<path d="M190 132 C220 132 220 192 244 192" stroke="#F03E82"/><path d="M236 186 L246 192 L236 198" stroke="#F03E82"/>
<path d="M420 48 C450 48 446 104 466 108" stroke="#8A87B0"/><path d="M456 101 L468 108 L457 114" stroke="#8A87B0"/>
<path d="M420 192 C450 192 446 136 466 130" stroke="#8A87B0"/><path d="M457 124 L468 130 L456 137" stroke="#8A87B0"/>
</g>
<rect x="20" y="90" width="170" height="56" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="105" y="114" text-anchor="middle" font-weight="700">try</text>
<text x="105" y="134" text-anchor="middle" font-size="13" fill="#1B66BC">leggiEta("abc")</text>
<text x="105" y="78" text-anchor="middle" font-size="13" fill="#0B7F5C">{$noError}</text>
<text x="105" y="168" text-anchor="middle" font-size="13" fill="#C2185B">{$exception}</text>
<rect x="250" y="24" width="170" height="48" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="335" y="53" text-anchor="middle">{$value}</text>
<rect x="250" y="164" width="170" height="56" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="335" y="188" text-anchor="middle" font-weight="700">catch</text>
<text x="335" y="208" text-anchor="middle" font-size="13" fill="#C2185B">NumberFormatException</text>
<rect x="470" y="90" width="150" height="56" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="545" y="114" text-anchor="middle" font-weight="700">finally</text>
<text x="545" y="134" text-anchor="middle" font-size="13" fill="#5A35C9">{$always}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'kotlin-base', 'family' => 'kotlin', 'after' => null,
    'title' => L('Kotlin base', 'Kotlin Basics'), 'short' => L('Kotlin base', 'Kotlin Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio consigliato da Google per Android: `val` e `var`, sicurezza sui null, `when`, collezioni, funzioni, stringhe, lambda ed eccezioni.', 'Google’s recommended language for Android: `val` and `var`, null safety, `when`, collections, functions, strings, lambdas and exceptions.'),
    'lessons' => [
        lesson('values', L('val e var', 'val and var'), [
            P('`val` crea un valore che non cambia, `var` una variabile. Il tipo si deduce o si scrive dopo i due punti. I **template** inseriscono valori nel testo con `$nome` o `${espressione}`.', '`val` creates a value that does not change, `var` a variable. The type is inferred or written after a colon. **Templates** put values in text with `$nome` or `${expression}`.'),
            C(L('fun main() {
    val nome = "Ada"
    var anni: Int = 36
    anni++
    println("Ciao $nome, hai ${anni} anni")
}', 'fun main() {
    val nome = "Ada"
    var anni: Int = 36
    anni++
    println("Hi $nome, you are ${anni}")
}'), 'kotlin', L('<pre>Ciao Ada, hai 37 anni</pre>', '<pre>Hi Ada, you are 37</pre>')),
            P('Niente punti e virgola a fine riga. Il programma parte da `fun main()`.', 'No semicolons at the end of lines. The program starts from `fun main()`.'),
            P('Come scegliere? Parti sempre da `val` e passa a `var` solo quando il valore deve davvero cambiare: meno cose cambiano, meno sorprese ci sono leggendo il codice. Il tipo, dedotto o scritto, resta fisso: dopo `var anni = 36`, l’assegnazione `anni = "tanti"` non compila. Attenzione: `val` blocca la variabile, non l’oggetto. Una `val` che contiene una lista modificabile non può puntare a un’altra lista, ma la lista stessa può cambiare.', 'How to choose? Always start with `val` and switch to `var` only when the value really has to change: the fewer things change, the fewer surprises when reading the code. The type, inferred or written, stays fixed: after `var anni = 36`, the assignment `anni = "many"` does not compile. Careful: `val` fixes the variable, not the object. A `val` holding a mutable list cannot point to another list, but the list itself can change.'),
        ], [
            Q('v1', L('Quale parola crea un valore che non cambia?', 'Which word creates a value that does not change?'), ['val', 'var', 'const', 'let'], 0, L('`var` si può riassegnare.', '`var` can be reassigned.')),
            Q('v2', L('Come inserisci `x` nel testo?', 'How do you put `x` into text?'), ['"$x"', '"{x}"', '"#{x}"', '"\\(x)"'], 0, L('Per espressioni `${…}`.', 'For expressions `${…}`.')),
            Q('v3', L('Quale funzione stampa una riga?', 'Which function prints a line?'), ['println', 'print_line', 'echo', 'log'], 0, L('`print` non va a capo.', '`print` adds no newline.')),
        ], task('kotlin', L('Dichiara `val corso = "Kotlin"` e stampa `"Studio $corso"`.', 'Declare `val corso = "Kotlin"` and print `"Studio $corso"`.'), 'fun main() {
}', [re('val\s+corso\s*(:\s*String\s*)?=\s*"Kotlin"', L('Il valore esiste', 'The value exists')), re('println\s*\(\s*"Studio \$corso"\s*\)', L('Lo stampi', 'You print it'))], '', 'val corso = "Kotlin"
println("Studio $corso")'), [
            dMatch(pMatch(), [['val', L('Non cambia', 'Does not change')], ['var', L('Può cambiare', 'Can change')], ['fun', L('Funzione', 'Function')], ['Int', L('Intero', 'Integer')], ['String', L('Testo', 'Text')]], L('Parole di base di Kotlin.', 'Basic Kotlin words.')),
            dFlash(pFlash(), [[L('In Kotlin servono i punti e virgola', 'Kotlin needs semicolons'), false], [L('`val` non si può riassegnare', '`val` cannot be reassigned'), true], [L('Il tipo si può dedurre', 'The type can be inferred'), true], [L('`var` si può riassegnare', '`var` can be reassigned'), true]], L('Niente punti e virgola.', 'No semicolons.')),
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
            Fig(L(fig_kotlinbase_nulls('Ospite'), fig_kotlinbase_nulls('Guest')), L('Una tabella con due righe, soprannome uguale a "Ada" e soprannome uguale a null, e tre colonne. Con "Ada": ?.length dà 3, ?: dà "Ada", !!.length dà 3. Con null: ?.length dà null, ?: dà il valore di riserva, !!.length fa crashare il programma con una NullPointerException.', 'A table with two rows, soprannome equal to "Ada" and soprannome equal to null, and three columns. With "Ada": ?.length gives 3, ?: gives "Ada", !!.length gives 3. With null: ?.length gives null, ?: gives the fallback value, !!.length crashes the program with a NullPointerException.'), L('Con un valore le tre forme si equivalgono; con null solo `!!` esplode.', 'With a value the three forms agree; with null only `!!` blows up.')),
            P('Perché il linguaggio insiste tanto? L’errore più comune nei programmi Java è la `NullPointerException`: un valore che si credeva presente e invece manca. Kotlin sposta il problema in compilazione: se il tipo non ha `?`, `null` non può entrare; se ce l’ha, il compilatore ti obbliga a gestirlo. Dopo un controllo `if (x != null)` il compilatore sa che `x` c’è e te lo lascia usare direttamente (lo **smart cast** del codice sopra).', 'Why does the language insist so much? The most common error in Java programs is the `NullPointerException`: a value believed to be there that is missing. Kotlin moves the problem to compile time: if the type has no `?`, `null` cannot get in; if it has one, the compiler makes you handle it. After an `if (x != null)` check the compiler knows `x` is there and lets you use it directly (the **smart cast** in the code above).'),
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
for (i in 10 downTo 0 step 5) println(i)', 'kotlin', '<pre>1
2
3
10
5
0</pre>'),
            P('`1..3` include il 3; `until` (o l’operatore `..<`) lo esclude; `downTo` e `step` contano all’indietro o a salti.', '`1..3` includes 3; `until` (or the `..<` operator) excludes it; `downTo` and `step` count backwards or in jumps.'),
            P('Il vantaggio di `if` e `when` come espressioni è che il risultato finisce subito in una `val`, senza dichiarare una variabile vuota da riempire nei vari rami. Un `when` usato come espressione deve coprire tutti i casi, per questo serve `else`. Senza argomento, `when { x < 0 -> … ; x == 0 -> … }` diventa una catena di condizioni più leggibile di tanti `else if`.', 'The advantage of `if` and `when` as expressions is that the result goes straight into a `val`, with no empty variable to fill in each branch. A `when` used as an expression must cover every case, which is why it needs `else`. Without an argument, `when { x < 0 -> … ; x == 0 -> … }` becomes a chain of conditions easier to read than many `else if`.'),
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
            P('`listOf` crea una lista di sola lettura, `mutableListOf` una che si modifica. Lo stesso per `mapOf`/`mutableMapOf` e `setOf`. Le funzioni `filter`, `map`, `sumOf` lavorano con le **lambda** e `it`.', '`listOf` makes a read-only list, `mutableListOf` one that can change. The same goes for `mapOf`/`mutableMapOf` and `setOf`. `filter`, `map`, `sumOf` work with **lambdas** and `it`.'),
            C('val voti = listOf(6, 8, 9, 5)
val promossi = voti.filter { it >= 6 }
val doppi = voti.map { it * 2 }

val carrello = mutableListOf("libro")
carrello.add("penna")

val eta = mapOf("Ada" to 36, "Bea" to 28)
println(eta["Ada"])', 'kotlin'),
            P('`it` è il nome automatico dell’unico parametro di una lambda. `to` crea una coppia chiave-valore.', '`it` is the automatic name of a lambda’s single parameter. `to` creates a key-value pair.'),
            Fig('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="20" y="72" font-weight="700">voti</text>
<rect x="20" y="80" width="180" height="60" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<g fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"><rect x="30" y="92" width="36" height="36" rx="8"/><rect x="72" y="92" width="36" height="36" rx="8"/><rect x="114" y="92" width="36" height="36" rx="8"/><rect x="156" y="92" width="36" height="36" rx="8"/></g>
<g text-anchor="middle" font-weight="700"><text x="48" y="115">6</text><text x="90" y="115">8</text><text x="132" y="115">9</text><text x="174" y="115">5</text></g>
<g stroke-width="2" fill="none"><path d="M200 100 H236 V52 H420" stroke="#0B7F5C"/><path d="M412 45 L422 52 L412 59" stroke="#0B7F5C"/><path d="M200 120 H236 V168 H420" stroke="#2F8CF0"/><path d="M412 161 L422 168 L412 175" stroke="#2F8CF0"/></g>
<text x="330" y="42" text-anchor="middle" font-size="14" font-weight="700" fill="#0B7F5C">.filter { it &gt;= 6 }</text>
<text x="330" y="158" text-anchor="middle" font-size="14" font-weight="700" fill="#1B66BC">.map { it * 2 }</text>
<rect x="430" y="24" width="190" height="56" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"><rect x="442" y="34" width="36" height="36" rx="8"/><rect x="484" y="34" width="36" height="36" rx="8"/><rect x="526" y="34" width="36" height="36" rx="8"/></g>
<rect x="572" y="34" width="36" height="36" rx="8" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"/>
<g text-anchor="middle" font-weight="700"><text x="460" y="57">6</text><text x="502" y="57">8</text><text x="544" y="57">9</text><text x="590" y="57" fill="#8A87B0" text-decoration="line-through">5</text></g>
<text x="525" y="100" text-anchor="middle" font-size="14" fill="#0B7F5C">promossi</text>
<rect x="430" y="140" width="190" height="56" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"><rect x="440" y="150" width="40" height="36" rx="8"/><rect x="485" y="150" width="40" height="36" rx="8"/><rect x="530" y="150" width="40" height="36" rx="8"/><rect x="575" y="150" width="40" height="36" rx="8"/></g>
<g text-anchor="middle" font-weight="700"><text x="460" y="173">12</text><text x="505" y="173">16</text><text x="550" y="173">18</text><text x="595" y="173">10</text></g>
<text x="525" y="216" text-anchor="middle" font-size="14" fill="#1B66BC">doppi</text>
</svg>', L('La lista voti con 6, 8, 9 e 5. Una freccia verde, filter it >= 6, porta a promossi con 6, 8 e 9, mentre il 5 è barrato. Una freccia blu, map it * 2, porta a doppi con 12, 16, 18 e 10.', 'The list voti with 6, 8, 9 and 5. A green arrow, filter it >= 6, leads to promossi with 6, 8 and 9, while 5 is crossed out. A blue arrow, map it * 2, leads to doppi with 12, 16, 18 and 10.'), L('`filter` sceglie gli elementi, `map` li trasforma: la lista di partenza non cambia.', '`filter` picks elements, `map` transforms them: the original list does not change.')),
            P('Preferisci le collezioni di sola lettura e usa quelle `mutable` solo dove serve davvero aggiungere o togliere: così nessun’altra parte del programma può cambiarti i dati alle spalle. `filter` e `map` non modificano mai la lista, ne creano una nuova, e si possono concatenare: `voti.filter { it >= 6 }.map { it * 2 }`. Nota infine che `eta["Ada"]` restituisce `Int?`: la chiave potrebbe non esserci.', 'Prefer read-only collections and use the `mutable` ones only where you really need to add or remove: that way no other part of the program can change your data behind your back. `filter` and `map` never modify the list, they create a new one, and they can be chained: `voti.filter { it >= 6 }.map { it * 2 }`. Finally, note that `eta["Ada"]` returns `Int?`: the key might not be there.'),
        ], [
            Q('k1', L('Si può fare `add` su `listOf(...)`?', 'Can you `add` to `listOf(...)`?'), [L('No, serve `mutableListOf`', 'No, you need `mutableListOf`'), L('Sì', 'Yes'), L('Solo stringhe', 'Only strings'), L('Solo in main', 'Only in main')], 0, L('`List` è un’interfaccia di sola lettura: non ha `add`.', '`List` is a read-only interface: it has no `add`.')),
            Q('k2', L('Che cos’è `it` in una lambda?', 'What is `it` in a lambda?'), [L('L’unico parametro', 'The single parameter'), L('Una variabile globale', 'A global variable'), L('Il risultato', 'The result'), L('Un errore', 'An error')], 0, L('Nome automatico.', 'Automatic name.')),
            Q('k3', L('Che cosa crea `"Ada" to 36`?', 'What does `"Ada" to 36` create?'), [L('Una coppia', 'A pair'), L('Un intervallo', 'A range'), L('Una stringa', 'A string'), L('Un errore', 'An error')], 0, L('Usata nelle mappe.', 'Used in maps.')),
        ], task('kotlin', L('Da `prezzi` ottieni `val scontati = prezzi.map { it * 0.9 }`.', 'From `prezzi` get `val scontati = prezzi.map { it * 0.9 }`.'), 'val prezzi = listOf(10.0, 20.0)
', [re('val\s+scontati\s*=\s*prezzi\.map\s*\{\s*it\s*\*\s*0\.9\s*\}', L('La trasformazione è giusta', 'The transformation is right'))], '', 'val scontati = prezzi.map { it * 0.9 }'), [
            dBlanks(pFill(), "val nomi = ___(\"Ada\")\nnomi.add(\"Bea\")\nval lunghi = nomi.___ { ___.length > 3 }", ['mutableListOf', 'filter', 'it'], ['listOf', 'where', 'this'], L('Lista modificabile, filtro, parametro.', 'Mutable list, filter, parameter.')),
            dFlash(pFlash(), [[L('`mapOf` usa `to` per le coppie', '`mapOf` uses `to` for pairs'), true], [L('`listOf` si può modificare', '`listOf` can be modified'), false], [L('`sumOf { it.prezzo }` somma un campo', '`sumOf { it.prezzo }` adds up a field'), true], [L('`filter` restituisce una nuova lista', '`filter` returns a new list'), true]], L('Per modificare serve `mutable`.', 'To modify you need `mutable`.')),
        ]),
        lesson('functions', L('Funzioni', 'Functions'), [
            P('`fun` crea una funzione; i parametri hanno tipo, possono avere valori predefiniti e si possono passare per nome. Una funzione di una sola espressione si scrive con `=`.', '`fun` creates a function; parameters are typed, can have default values and can be passed by name. A single-expression function is written with `=`.'),
            C(L('fun area(base: Double, altezza: Double = 1.0): Double = base * altezza

fun saluta(nome: String, punto: String = "!") {
    println("Ciao $nome$punto")
}

area(3.0, 2.0)
area(altezza = 4.0, base = 2.0)
saluta("Ada")

fun String.urla() = uppercase() + "!"
println("ciao".urla())', 'fun area(base: Double, altezza: Double = 1.0): Double = base * altezza

fun saluta(nome: String, punto: String = "!") {
    println("Hi $nome$punto")
}

area(3.0, 2.0)
area(altezza = 4.0, base = 2.0)
saluta("Ada")

fun String.urla() = uppercase() + "!"
println("hello".urla())'), 'kotlin', L('<pre>Ciao Ada!
CIAO!</pre>', '<pre>Hi Ada!
HELLO!</pre>')),
            P('Le **funzioni di estensione** aggiungono metodi a tipi esistenti, come `String.urla()`.', '**Extension functions** add methods to existing types, like `String.urla()`.'),
            P('I valori predefiniti e gli argomenti con nome sostituiscono le tante versioni della stessa funzione che servirebbero in Java: `area(3.0)` usa `altezza = 1.0`, e con i nomi puoi passare gli argomenti in qualunque ordine, rendendo chiara la chiamata. Una funzione senza un risultato utile restituisce `Unit`, che non si scrive. Le estensioni non cambiano davvero la classe: sono funzioni normali che si chiamano con il punto.', 'Default values and named arguments replace the many versions of the same function you would need in Java: `area(3.0)` uses `altezza = 1.0`, and with names you can pass arguments in any order, making the call clear. A function with no useful result returns `Unit`, which you do not write. Extensions do not really change the class: they are plain functions called with the dot.'),
        ], [
            Q('f1', L('Come si scrive una funzione di una sola espressione?', 'How do you write a single-expression function?'), ['fun f() = 42', 'fun f() -> 42', 'fun f(): 42', 'f = () => 42'], 0, L('Con il segno uguale.', 'With an equals sign.')),
            Q('f2', L('Che cosa sono gli argomenti con nome?', 'What are named arguments?'), [L('Passare un parametro scrivendone il nome', 'Passing a parameter by writing its name'), L('Variabili globali', 'Global variables'), L('Costanti', 'Constants'), L('Commenti', 'Comments')], 0, L('L’ordine non conta più.', 'Order no longer matters.')),
            Q('f3', L('Che cosa fa una funzione di estensione?', 'What does an extension function do?'), [L('Aggiunge un metodo a un tipo esistente', 'Adds a method to an existing type'), L('Allunga una funzione', 'Lengthens a function'), L('Crea una sottoclasse', 'Creates a subclass'), L('Niente', 'Nothing')], 0, L('Senza toccare il tipo originale.', 'Without touching the original type.')),
        ], task('kotlin', L('Scrivi `fun doppio(n: Int): Int = n * 2`.', 'Write `fun doppio(n: Int): Int = n * 2`.'), '', [re('fun\s+doppio\s*\(\s*n\s*:\s*Int\s*\)\s*(:\s*Int\s*)?(=\s*n\s*\*\s*2|\{\s*return\s+n\s*\*\s*2)', L('La funzione è giusta', 'The function is right'))], '', 'fun doppio(n: Int): Int = n * 2'), [
            dOrder(pSort(), ['fun media(voti: List<Int>): Double {', '    if (voti.isEmpty()) return 0.0', '    return voti.sum().toDouble() / voti.size', '}', 'println(media(listOf(6, 8)))'], L('Firma, controllo, calcolo, chiamata.', 'Signature, check, calculation, call.')),
            dMatch(pMatch(), [['fun', L('Crea una funzione', 'Creates a function')], ['= espressione', L('Corpo di una riga', 'One-line body')], ['Unit', L('Nessun risultato', 'No result')], ['String.x()', L('Estensione', 'Extension')]], L('Funzioni in Kotlin.', 'Functions in Kotlin.')),
        ]),
        lesson('strings', L('Stringhe', 'Strings'), [
            P('Le stringhe Kotlin sono immutabili e hanno tanti metodi pronti: `uppercase()`, `trim()`, `split()`, `substring()`, `startsWith()`. Per trasformare un testo in numero c’è `toInt()`, che lancia un’eccezione se il testo non è un numero, e `toIntOrNull()`, che restituisce `null`.', 'Kotlin strings are immutable and have many ready-made methods: `uppercase()`, `trim()`, `split()`, `substring()`, `startsWith()`. To turn text into a number there is `toInt()`, which throws if the text is not a number, and `toIntOrNull()`, which returns `null`.'),
            C('val nome = "  Ada Lovelace  "
println(nome.trim().uppercase())        // ADA LOVELACE
val parti = "uno,due,tre".split(",")    // [uno, due, tre]
println("kotlin".replaceFirstChar { it.uppercase() })  // Kotlin
println("7".padStart(3, \'0\'))           // 007

val eta = "36".toIntOrNull() ?: 0
val poesia = """
    Prima riga
    Seconda riga
""".trimIndent()

val elenco = buildString {
    for (i in 1..3) append("Voce $i\n")
}', 'kotlin'),
            P('Le stringhe tra tre virgolette (`"""`) vanno su più righe e non interpretano `\n`; `trimIndent()` toglie il rientro comune. `buildString` costruisce un testo pezzo per pezzo senza creare tante stringhe intermedie.', 'Triple-quoted strings (`"""`) span several lines and do not interpret `\n`; `trimIndent()` removes the common indentation. `buildString` builds text piece by piece without creating many intermediate strings.'),
        ], [
            Q('s1', L('Che cosa restituisce `"abc".toIntOrNull()`?', 'What does `"abc".toIntOrNull()` return?'), ['0', L('Un’eccezione', 'An exception'), '""', 'null'], 3, L('`toInt()` invece lancerebbe un’eccezione.', '`toInt()` would throw instead.')),
            Q('s2', L('Quale funzione toglie gli spazi all’inizio e alla fine?', 'Which function removes spaces at the start and end?'), ['clean()', 'trim()', 'cut()', 'spaces()'], 1, L('Esistono anche `trimStart()` e `trimEnd()`.', 'There are also `trimStart()` and `trimEnd()`.')),
            Q('s3', L('Che cosa fa `trimIndent()` su una stringa tra tre virgolette?', 'What does `trimIndent()` do on a triple-quoted string?'), [L('Toglie il rientro comune a tutte le righe', 'Removes the indentation common to all lines'), L('Unisce tutte le righe in una', 'Joins all lines into one'), L('Converte il testo in maiuscolo', 'Converts the text to capitals'), L('Toglie le righe vuote in mezzo al testo', 'Removes the blank lines inside the text')], 0, L('Toglie anche la prima e l’ultima riga se sono vuote.', 'It also drops the first and last lines if blank.')),
        ], task('kotlin', L('Converti `testo` in numero con `val numero = testo.toIntOrNull() ?: 0`.', 'Convert `testo` to a number with `val numero = testo.toIntOrNull() ?: 0`.'), 'val testo = readln()
', [re('val\s+numero\s*(:\s*Int\s*)?=\s*testo\.toIntOrNull\s*\(\s*\)\s*\?:\s*0', L('Converti con un valore di riserva', 'You convert with a fallback'))], '', 'val numero = testo.toIntOrNull() ?: 0'), [
            dMatch(pMatch(), [['trim()', L('Toglie gli spazi ai lati', 'Removes spaces at the sides')], ['split(",")', L('Divide il testo', 'Splits the text')], ['toIntOrNull()', L('Numero o null', 'Number or null')], ['"""', L('Testo su più righe', 'Multi-line text')], ['buildString', L('Costruisce un testo a pezzi', 'Builds text in pieces')]], L('Gli attrezzi delle stringhe.', 'String tools.')),
            dBug(pBug(), ['val input = "dieci"', 'val n = input.toInt()', 'println(n * 2)'], 1, 'val n = input.toIntOrNull() ?: 0', L('`toInt()` su un testo non numerico lancia `NumberFormatException`.', '`toInt()` on non-numeric text throws `NumberFormatException`.')),
        ]),
        lesson('lambdas', L('Lambda e funzioni di ordine superiore', 'Lambdas and higher-order functions'), [
            P('In Kotlin le funzioni sono valori: si salvano in variabili e si passano ad altre funzioni. Il tipo `(Int) -> Int` indica una funzione che riceve un intero e ne restituisce un altro. Una funzione che riceve o restituisce funzioni si dice **di ordine superiore**.', 'In Kotlin functions are values: you store them in variables and pass them to other functions. The type `(Int) -> Int` means a function taking an int and returning another. A function that takes or returns functions is called **higher-order**.'),
            C('val doppio: (Int) -> Int = { n -> n * 2 }
val somma = { a: Int, b: Int -> a + b }

fun ripeti(volte: Int, azione: (Int) -> Unit) {
    for (i in 1..volte) azione(i)
}

ripeti(3) { giro -> println("Giro $giro") }

val parole = listOf("sole", "re", "montagna")
val ordinate = parole.sortedBy { it.length }
val lunghe = parole.count { it.length > 3 }
val maiuscole = parole.map(String::uppercase)', 'kotlin'),
            P('Se l’ultimo parametro è una funzione, la lambda si scrive **fuori dalle parentesi** (trailing lambda), come in `ripeti(3) { … }`. L’ultima espressione della lambda è il suo risultato. `String::uppercase` è un riferimento a una funzione che esiste già.', 'If the last parameter is a function, the lambda goes **outside the brackets** (trailing lambda), as in `ripeti(3) { … }`. The last expression of the lambda is its result. `String::uppercase` is a reference to an existing function.'),
        ], [
            Q('l1', L('Che cosa indica il tipo `(String) -> Boolean`?', 'What does the type `(String) -> Boolean` mean?'), [L('Una funzione che riceve una String e restituisce un Boolean', 'A function taking a String and returning a Boolean'), L('Una coppia di String e Boolean', 'A pair of String and Boolean'), L('Una String convertita in Boolean', 'A String converted to Boolean'), L('Una lista di Boolean', 'A list of Booleans')], 0, L('È il tipo di una funzione.', 'It is a function type.')),
            Q('l2', L('Come scrivi la chiamata se l’ultimo parametro è una lambda?', 'How do you write the call if the last parameter is a lambda?'), [L('Solo `ripeti(3, { … })`', 'Only `ripeti(3, { … })`'), L('`ripeti(3) { … }`, con la lambda fuori dalle parentesi', '`ripeti(3) { … }`, with the lambda outside the brackets'), L('`ripeti { … }(3)`', '`ripeti { … }(3)`'), L('`ripeti(3) -> { … }`', '`ripeti(3) -> { … }`')], 1, L('È la trailing lambda, usata ovunque in Compose.', 'It is the trailing lambda, used everywhere in Compose.')),
            Q('l3', L('Che cosa restituisce la lambda `{ x: Int -> x + 1 }`?', 'What does the lambda `{ x: Int -> x + 1 }` return?'), [L('Sempre Unit', 'Always Unit'), L('Il valore del parametro x', 'The value of parameter x'), L('Niente: serve `return`', 'Nothing: it needs `return`'), L('Il valore dell’ultima espressione, x + 1', 'The value of the last expression, x + 1')], 3, L('Nelle lambda non si scrive `return`.', 'In lambdas you do not write `return`.')),
        ], task('kotlin', L('Scrivi `val quadrato: (Int) -> Int = { it * it }`.', 'Write `val quadrato: (Int) -> Int = { it * it }`.'), '', [re('val\s+quadrato\s*:\s*\(\s*Int\s*\)\s*->\s*Int\s*=\s*\{\s*(it\s*\*\s*it|(\w+)\s*->\s*\2\s*\*\s*\2)\s*\}', L('La lambda è giusta', 'The lambda is right'))], '', 'val quadrato: (Int) -> Int = { it * it }'), [
            dBlanks(pFill(), "fun applica(n: Int, f: (Int) ___ Int) = f(n)\nval r = applica(5) { ___ * 3 }\nval nomi = listOf(\"ada\").map(String::___)", ['->', 'it', 'uppercase'], ['=>', 'this', 'upper'], L('Tipo funzione, parametro implicito, riferimento.', 'Function type, implicit parameter, reference.')),
            dFlash(pFlash(), [[L('Una funzione si può salvare in una variabile', 'A function can be stored in a variable'), true], [L('La trailing lambda va sempre dentro le parentesi', 'The trailing lambda always goes inside the brackets'), false], [L('`it` è il nome automatico dell’unico parametro', '`it` is the automatic name of the single parameter'), true], [L('`sortedBy` restituisce una nuova lista ordinata', '`sortedBy` returns a new sorted list'), true]], L('Fuori dalle parentesi, se è l’ultima.', 'Outside the brackets, if it is the last one.')),
        ]),
        lesson('exceptions', L('Eccezioni', 'Exceptions'), [
            P('Quando qualcosa va storto Kotlin lancia un’**eccezione**, che si gestisce con `try`/`catch`; `finally` gira in ogni caso. A differenza di Java non esistono eccezioni “controllate”: il compilatore non ti obbliga a catturarle. `try` è un’espressione, quindi restituisce un valore.', 'When something goes wrong Kotlin throws an **exception**, handled with `try`/`catch`; `finally` runs in any case. Unlike Java there are no “checked” exceptions: the compiler does not force you to catch them. `try` is an expression, so it returns a value.'),
            C(L('fun leggiEta(testo: String): Int {
    val eta = testo.toInt()        // NumberFormatException se non è un numero
    require(eta >= 0) { "Età negativa: $eta" }
    return eta
}

val eta = try {
    leggiEta("abc")
} catch (e: NumberFormatException) {
    println("Non è un numero")
    0
} finally {
    println("Controllo finito")
}

val risultato = runCatching { leggiEta("-3") }
println(risultato.getOrElse { -1 })   // -1', 'fun leggiEta(testo: String): Int {
    val eta = testo.toInt()        // NumberFormatException if it is not a number
    require(eta >= 0) { "Negative age: $eta" }
    return eta
}

val eta = try {
    leggiEta("abc")
} catch (e: NumberFormatException) {
    println("Not a number")
    0
} finally {
    println("Check finished")
}

val risultato = runCatching { leggiEta("-3") }
println(risultato.getOrElse { -1 })   // -1'), 'kotlin', L('<pre>Non è un numero
Controllo finito
-1</pre>', '<pre>Not a number
Check finished
-1</pre>')),
            P('`require` controlla gli argomenti e lancia `IllegalArgumentException`, `check` controlla lo stato e lancia `IllegalStateException`. `runCatching` racchiude l’esito in un `Result`: comodo quando l’errore è un caso normale, non un’emergenza.', '`require` checks arguments and throws `IllegalArgumentException`, `check` checks state and throws `IllegalStateException`. `runCatching` wraps the outcome in a `Result`: handy when the error is a normal case, not an emergency.'),
            Fig(L(fig_kotlinbase_trycatch('valore restituito', 'nessun errore', 'eccezione', 'gira sempre'), fig_kotlinbase_trycatch('value returned', 'no error', 'exception', 'always runs')), L('Dal blocco try, che chiama leggiEta("abc"), partono due strade: senza errori si arriva al valore restituito, con un’eccezione si entra nel catch di NumberFormatException. Entrambe le strade finiscono nel blocco finally, che gira sempre.', 'From the try block, which calls leggiEta("abc"), two paths start: with no error you reach the value returned, with an exception you enter the catch for NumberFormatException. Both paths end in the finally block, which always runs.'), L('`finally` è il posto per chiudere file e connessioni, qualunque cosa succeda.', '`finally` is the place to close files and connections, whatever happens.')),
        ], [
            Q('e1', L('Kotlin obbliga a catturare le eccezioni come Java con le checked exception?', 'Does Kotlin force you to catch exceptions as Java does with checked exceptions?'), [L('Sì, sempre', 'Yes, always'), L('Solo nelle funzioni suspend', 'Only in suspend functions'), L('No, non esistono eccezioni controllate', 'No, there are no checked exceptions'), L('Solo per gli errori di rete', 'Only for network errors')], 2, L('Sta a te decidere dove gestirle.', 'It is up to you where to handle them.')),
            Q('e2', L('Che cosa lancia `require(x > 0)` se x vale -1?', 'What does `require(x > 0)` throw if x is -1?'), ['IllegalArgumentException', 'NullPointerException', 'IllegalStateException', 'ArithmeticException'], 0, L('`check` lancerebbe `IllegalStateException`.', '`check` would throw `IllegalStateException`.')),
            Q('e3', L('Quando gira il blocco `finally`?', 'When does the `finally` block run?'), [L('Solo se c’è un errore', 'Only if there is an error'), L('Solo se non c’è un errore', 'Only if there is no error'), L('Solo prima del `try`', 'Only before the `try`'), L('Sempre, con o senza errore', 'Always, with or without an error')], 3, L('Ideale per chiudere risorse.', 'Ideal for closing resources.')),
        ], task('kotlin', L('Scrivi `val n = try { testo.toInt() } catch (e: NumberFormatException) { 0 }`.', 'Write `val n = try { testo.toInt() } catch (e: NumberFormatException) { 0 }`.'), 'val testo = "12a"
', [re('val\s+n\s*(:\s*Int\s*)?=\s*try\s*\{\s*testo\.toInt\s*\(\s*\)\s*\}', L('Provi la conversione', 'You try the conversion')), re('catch\s*\(\s*\w+\s*:\s*(NumberFormatException|Exception)\s*\)\s*\{\s*0\s*\}', L('In caso di errore dai 0', 'On error you give 0'))], '', 'val n = try { testo.toInt() } catch (e: NumberFormatException) { 0 }'), [
            dOrder(pSort(), ['try {', '    val dati = leggiFile("note.txt")', '    println(dati)', '} catch (e: IOException) {', '    println("Errore: ${e.message}")', '} finally {', '    println("Fine")', '}'], L('Prova, gestisci, chiudi.', 'Try, handle, close.')),
            dMatch(pMatch(), [['try', L('Prova il codice', 'Tries the code')], ['catch', L('Gestisce l’errore', 'Handles the error')], ['finally', L('Gira sempre', 'Always runs')], ['require', L('Controlla un argomento', 'Checks an argument')], ['runCatching', L('Racchiude l’esito in un Result', 'Wraps the outcome in a Result')]], L('Gestire gli errori in Kotlin.', 'Handling errors in Kotlin.')),
        ]),
    ],
];
