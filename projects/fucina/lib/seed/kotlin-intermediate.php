<?php
/** Kotlin intermediate: classes and data classes, sealed types, coroutines, flows, Jetpack Compose, scope functions, generics and Compose state with ViewModel. */

/** A sealed interface and its three subtypes: the whole family is known to the compiler. */
if (!function_exists('fig_kotlinint_sealed')) {
    function fig_kotlinint_sealed(string $note): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M320 60 V84 H110 V110 M320 84 V110 M320 84 H530 V110"/></g>
<rect x="190" y="16" width="260" height="44" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="320" y="43" text-anchor="middle" font-weight="700">sealed interface Stato</text>
<rect x="20" y="110" width="180" height="58" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="110" y="135" text-anchor="middle" font-weight="700">Caricamento</text><text x="110" y="156" text-anchor="middle" font-size="13" fill="#5F5C8A">data object</text>
<rect x="230" y="110" width="180" height="58" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="320" y="135" text-anchor="middle" font-weight="700">Pronto</text><text x="320" y="156" text-anchor="middle" font-size="13" fill="#0B7F5C">corsi: List&lt;String&gt;</text>
<rect x="440" y="110" width="180" height="58" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="530" y="135" text-anchor="middle" font-weight="700">Errore</text><text x="530" y="156" text-anchor="middle" font-size="13" fill="#C2185B">messaggio: String</text>
<path d="M20 192 V200 H620 V192" fill="none" stroke="#7048E8" stroke-width="2"/>
<text x="320" y="226" text-anchor="middle" font-size="14" fill="#5A35C9">{$note}</text>
</svg>
SVG;
    }
}

/** One thread, two jobs: with Thread.sleep the thread waits doing nothing, with delay the other coroutine runs in the gap. */
if (!function_exists('fig_kotlinint_suspend')) {
    function fig_kotlinint_suspend(string $blocks, string $suspends, string $free, string $time): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.cur{animation:cur 7s linear infinite}@keyframes cur{from{transform:none}to{transform:translateX(580px)}}@media (prefers-reduced-motion:reduce){.cur{animation:none}}</style>
<defs><pattern id="kch" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="8" height="8" fill="#F1F0F8"/><path d="M0 0 V8" stroke="#8A87B0" stroke-width="2"/></pattern></defs>
<text x="40" y="34" font-size="14" font-weight="700">{$blocks}</text>
<g stroke-width="2" font-size="14" font-weight="700" text-anchor="middle">
<rect x="40" y="44" width="80" height="34" rx="6" fill="#DCEBFF" stroke="#2F8CF0"/><text x="80" y="66">A</text>
<rect x="120" y="44" width="160" height="34" rx="6" fill="url(#kch)" stroke="#8A87B0"/>
<rect x="280" y="44" width="50" height="34" rx="6" fill="#DCEBFF" stroke="#2F8CF0"/><text x="305" y="66">A</text>
<rect x="330" y="44" width="80" height="34" rx="6" fill="#ECE6FF" stroke="#7048E8"/><text x="370" y="66">B</text>
<rect x="410" y="44" width="160" height="34" rx="6" fill="url(#kch)" stroke="#8A87B0"/>
<rect x="570" y="44" width="50" height="34" rx="6" fill="#ECE6FF" stroke="#7048E8"/><text x="595" y="66">B</text>
</g>
<text x="40" y="122" font-size="14" font-weight="700">{$suspends}</text>
<g stroke-width="2" font-size="14" font-weight="700" text-anchor="middle">
<rect x="40" y="132" width="80" height="34" rx="6" fill="#DCEBFF" stroke="#2F8CF0"/><text x="80" y="154">A</text>
<rect x="120" y="132" width="80" height="34" rx="6" fill="#ECE6FF" stroke="#7048E8"/><text x="160" y="154">B</text>
<rect x="200" y="132" width="80" height="34" rx="6" fill="#FFFFFF" stroke="#8A87B0" stroke-dasharray="4 3"/><text x="240" y="154" font-size="13" font-weight="400" fill="#5F5C8A">{$free}</text>
<rect x="280" y="132" width="50" height="34" rx="6" fill="#DCEBFF" stroke="#2F8CF0"/><text x="305" y="154">A</text>
<rect x="360" y="132" width="50" height="34" rx="6" fill="#ECE6FF" stroke="#7048E8"/><text x="385" y="154">B</text>
</g>
<path class="cur" d="M40 40 V172" stroke="#E8590C" stroke-width="2"/>
<path d="M40 196 H596" stroke="#8A87B0" stroke-width="2"/><path d="M588 189 L600 196 L588 203" stroke="#8A87B0" stroke-width="2" fill="none"/>
<text x="600" y="218" text-anchor="end" font-size="13" fill="#5F5C8A">{$time}</text>
</svg>
SVG;
    }
}

/** A marble diagram: conto() emits 3, 2, 1 one second apart, map turns each into a sentence, collect prints them. The values appear in turn. */
if (!function_exists('fig_kotlinint_flow')) {
    function fig_kotlinint_flow(string $map, array $out): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.g1{animation:k1 7s infinite}.g2{animation:k2 7s infinite}.g3{animation:k3 7s infinite}@keyframes k1{0%,4%{opacity:0}10%,92%{opacity:1}100%{opacity:0}}@keyframes k2{0%,30%{opacity:0}36%,92%{opacity:1}100%{opacity:0}}@keyframes k3{0%,56%{opacity:0}62%,92%{opacity:1}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.g1,.g2,.g3{animation:none}}</style>
<text x="40" y="30" font-size="14" font-weight="700" fill="#1B66BC">conto()</text>
<path d="M40 60 H596" stroke="#2F8CF0" stroke-width="2"/><path d="M588 53 L600 60 L588 67" stroke="#2F8CF0" stroke-width="2" fill="none"/>
<path d="M560 44 V76" stroke="#2F8CF0" stroke-width="3"/>
<rect x="40" y="96" width="560" height="32" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="320" y="117" text-anchor="middle" font-size="14" font-weight="700">{$map}</text>
<text x="40" y="154" font-size="14" font-weight="700" fill="#B84508">collect { println(it) }</text>
<path d="M40 182 H596" stroke="#E8590C" stroke-width="2"/><path d="M588 175 L600 182 L588 189" stroke="#E8590C" stroke-width="2" fill="none"/>
<path d="M560 166 V198" stroke="#E8590C" stroke-width="3"/>
<g class="g1"><path d="M120 78 V96 M120 128 V165" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"/><circle cx="120" cy="60" r="18" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="120" y="66" text-anchor="middle" font-weight="700">3</text><rect x="65" y="165" width="110" height="34" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="120" y="187" text-anchor="middle" font-size="14">{$out[0]}</text></g>
<g class="g2"><path d="M300 78 V96 M300 128 V165" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"/><circle cx="300" cy="60" r="18" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="300" y="66" text-anchor="middle" font-weight="700">2</text><rect x="245" y="165" width="110" height="34" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="300" y="187" text-anchor="middle" font-size="14">{$out[1]}</text></g>
<g class="g3"><path d="M480 78 V96 M480 128 V165" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"/><circle cx="480" cy="60" r="18" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="480" y="66" text-anchor="middle" font-weight="700">1</text><rect x="425" y="165" width="110" height="34" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="480" y="187" text-anchor="middle" font-size="14">{$out[2]}</text></g>
<g text-anchor="middle" font-size="13" fill="#5F5C8A"><text x="210" y="222">1 s</text><text x="390" y="222">1 s</text></g>
</svg>
SVG;
    }
}

/** State hoisting: the state flows down from the ViewModel to the composables, events flow back up. */
if (!function_exists('fig_kotlinint_hoisting')) {
    function fig_kotlinint_hoisting(string $down, string $up): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="190" y="14" width="260" height="56" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="320" y="38" text-anchor="middle" font-weight="700">ContatoreViewModel</text>
<text x="320" y="59" text-anchor="middle" font-size="13" fill="#5A35C9">conta: StateFlow&lt;Int&gt;</text>
<rect x="190" y="104" width="260" height="48" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="320" y="133" text-anchor="middle" font-weight="700">ContatoreScreen</text>
<rect x="190" y="190" width="260" height="56" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="320" y="214" text-anchor="middle" font-weight="700">Contatore</text>
<text x="320" y="235" text-anchor="middle" font-size="13" fill="#0B7F5C">Button("Clic: 3")</text>
<g stroke-width="2" fill="none">
<path d="M236 72 V100" stroke="#E8590C"/><path d="M229 92 L236 104 L243 92" stroke="#E8590C"/>
<path d="M236 154 V186" stroke="#E8590C"/><path d="M229 178 L236 190 L243 178" stroke="#E8590C"/>
<path d="M404 188 V158" stroke="#0B7F5C"/><path d="M397 166 L404 154 L411 166" stroke="#0B7F5C"/>
<path d="M404 102 V76" stroke="#0B7F5C"/><path d="M397 84 L404 72 L411 84" stroke="#0B7F5C"/>
</g>
<g font-size="13"><text x="226" y="92" text-anchor="end" fill="#B84508">conta</text><text x="226" y="176" text-anchor="end" fill="#B84508">valore</text>
<text x="414" y="92" fill="#0B7F5C">aumenta()</text><text x="414" y="176" fill="#0B7F5C">onAumenta()</text></g>
<text x="24" y="134" font-weight="700" fill="#B84508">{$down}</text>
<text x="616" y="134" text-anchor="end" font-weight="700" fill="#0B7F5C">{$up}</text>
</svg>
SVG;
    }
}

/** The five scope functions in a table: how the block sees the object and what it returns. */
if (!function_exists('fig_kotlinint_scope')) {
    function fig_kotlinint_scope(array $h): string
    {
        $rows = '';
        $data = [['let', 'it', 0], ['run', 'this', 0], ['with', 'this', 0], ['apply', 'this', 1], ['also', 'it', 1]];
        foreach ($data as $i => [$f, $o, $r]) {
            $y = 56 + $i * 40;
            $ty = $y + 23;
            $oc = $o === 'it' ? ['#DCEBFF', '#2F8CF0'] : ['#ECE6FF', '#7048E8'];
            $rc = $r ? ['#FFF3EA', '#E8590C'] : ['#D5F6EA', '#0B7F5C'];
            $rt = $r ? $h[4] : $h[3];
            $rows .= "<rect x=\"12\" y=\"$y\" width=\"130\" height=\"34\" rx=\"8\" fill=\"#F1F0F8\" stroke=\"#8A87B0\" stroke-width=\"2\"/><text x=\"77\" y=\"$ty\" text-anchor=\"middle\" font-weight=\"700\">$f</text>"
                . "<rect x=\"152\" y=\"$y\" width=\"200\" height=\"34\" rx=\"8\" fill=\"{$oc[0]}\" stroke=\"{$oc[1]}\" stroke-width=\"2\"/><text x=\"252\" y=\"$ty\" text-anchor=\"middle\">$o</text>"
                . "<rect x=\"362\" y=\"$y\" width=\"266\" height=\"34\" rx=\"8\" fill=\"{$rc[0]}\" stroke=\"{$rc[1]}\" stroke-width=\"2\"/><text x=\"495\" y=\"$ty\" text-anchor=\"middle\">$rt</text>\n";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g font-size="14" font-weight="700" fill="#5F5C8A" text-anchor="middle"><text x="77" y="38">{$h[0]}</text><text x="252" y="38">{$h[1]}</text><text x="495" y="38">{$h[2]}</text></g>
{$rows}</svg>
SVG;
    }
}

return [
    'id' => 'kotlin-intermediate', 'family' => 'kotlin', 'after' => 'kotlin-base',
    'title' => L('Kotlin intermedio', 'Intermediate Kotlin'), 'short' => L('Kotlin intermedio', 'Kotlin Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Classi e data class, interfacce, sealed class, coroutine, Flow, Jetpack Compose, funzioni di scope, generics e stato con ViewModel.', 'Classes and data classes, interfaces, sealed classes, coroutines, Flow, Jetpack Compose, scope functions, generics and state with ViewModel.'),
    'lessons' => [
        lesson('classes', L('Classi e data class', 'Classes and data classes'), [
            P('Il costruttore principale sta nella riga della classe: `class Conto(val titolare: String)`. Una **data class** è fatta per contenere dati: Kotlin genera `equals`, `toString` e `copy`.', 'The primary constructor sits on the class line: `class Conto(val titolare: String)`. A **data class** is made to hold data: Kotlin generates `equals`, `toString` and `copy`.'),
            C('class Conto(val titolare: String, saldoIniziale: Double = 0.0) {
    var saldo = saldoIniziale
        private set
    fun versa(importo: Double) { require(importo > 0); saldo += importo }
}

data class Utente(val nome: String, val eta: Int)

val a = Utente("Ada", 36)
val b = a.copy(eta = 37)
println(a == Utente("Ada", 36))   // true', 'kotlin'),
            P('`private set` permette di leggere `saldo` da fuori ma di cambiarlo solo dentro la classe. Le classi sono `final` per impostazione: per estenderle servono `open`.', '`private set` lets outsiders read `saldo` but change it only inside the class. Classes are `final` by default: to extend them you need `open`.'),
            P('Quando usare una data class? Quando l’oggetto è definito dai suoi valori: due `Utente("Ada", 36)` sono lo stesso utente, e `==` lo conferma. Con una classe normale `==` confronta invece l’identità, cioè se è proprio lo stesso oggetto in memoria. Con campi `val` e `copy` cambi un valore creando un oggetto nuovo invece di modificare quello vecchio: niente sorprese se qualcun altro lo stava usando.', 'When should you use a data class? When the object is defined by its values: two `Utente("Ada", 36)` are the same user, and `==` confirms it. With a normal class `==` compares identity instead, that is whether it is the very same object in memory. With `val` fields and `copy` you change a value by creating a new object instead of modifying the old one: no surprises if someone else was using it.'),
        ], [
            Q('c1', L('Che cosa genera una data class?', 'What does a data class generate?'), [L('equals, toString, copy…', 'equals, toString, copy…'), L('Un database', 'A database'), L('Una schermata', 'A screen'), L('Niente', 'Nothing')], 0, L('Fatta per i dati.', 'Made for data.')),
            Q('c2', L('Che cosa fa `copy(eta = 37)`?', 'What does `copy(eta = 37)` do?'), [L('Un nuovo oggetto con un campo cambiato', 'A new object with one field changed'), L('Modifica l’originale', 'Changes the original'), L('Copia negli appunti', 'Copies to clipboard'), L('Errore', 'Error')], 0, L('L’originale resta uguale.', 'The original stays the same.')),
            Q('c3', L('Come rendi una classe estendibile?', 'How do you make a class extendable?'), ['open', 'public', 'abstract only', 'extends'], 0, L('Di base sono `final`.', 'They are `final` by default.')),
        ], task('kotlin', L('Crea `data class Prodotto(val nome: String, val prezzo: Double)`.', 'Create `data class Prodotto(val nome: String, val prezzo: Double)`.'), '', [re('data\s+class\s+Prodotto\s*\(\s*val\s+nome\s*:\s*String\s*,\s*val\s+prezzo\s*:\s*Double\s*\)', L('La data class è giusta', 'The data class is right'))], '', 'data class Prodotto(val nome: String, val prezzo: Double)'), [
            dBlanks(pFill(), "___ class Punto(val x: Int, val y: Int)\nval p = Punto(1, 2)\nval q = p.___(y = 5)", ['data', 'copy'], ['open', 'clone', 'with'], L('Data class e copia modificata.', 'Data class and modified copy.')),
            dFlash(pFlash(), [[L('Due data class con gli stessi valori sono uguali con ==', 'Two data classes with equal values are == equal'), true], [L('Le classi Kotlin si estendono senza `open`', 'Kotlin classes extend without `open`'), false], [L('`private set` rende la proprietà leggibile ma non scrivibile da fuori', '`private set` makes the property readable but not writable from outside'), true], [L('Una data class genera da sola `toString`', 'A data class generates `toString` by itself'), true]], L('Serve `open` per estendere.', '`open` is needed to extend.')),
        ]),
        lesson('sealed', L('Interfacce e sealed class', 'Interfaces and sealed classes'), [
            P('Un’**interfaccia** dichiara funzioni che le classi implementano. Una **sealed class** (o interface) elenca tutti i sottotipi possibili: perfetta per stati come caricamento, successo, errore. Con `when` il compilatore controlla che tu li gestisca tutti.', 'An **interface** declares functions classes implement. A **sealed class** (or interface) lists every possible subtype: perfect for states like loading, success, error. With `when` the compiler checks you handle them all.'),
            C('sealed interface Stato {
    data object Caricamento : Stato
    data class Pronto(val corsi: List<String>) : Stato
    data class Errore(val messaggio: String) : Stato
}

fun descrivi(s: Stato) = when (s) {
    is Stato.Caricamento -> "Attendi…"
    is Stato.Pronto -> "${s.corsi.size} corsi"
    is Stato.Errore -> "Errore: ${s.messaggio}"
}', 'kotlin'),
            P('Dopo `is Stato.Pronto` Kotlin sa che `s` è un `Pronto`: è lo **smart cast**, niente conversioni a mano.', 'After `is Stato.Pronto` Kotlin knows `s` is a `Pronto`: it is the **smart cast**, no manual casting.'),
            Fig(L(fig_kotlinint_sealed('il compilatore conosce tutti i sottotipi: when non ha bisogno di else'), fig_kotlinint_sealed('the compiler knows every subtype: when needs no else')), L('In alto sealed interface Stato; sotto, collegati, i suoi tre sottotipi: Caricamento, un data object; Pronto, con la lista dei corsi; Errore, con un messaggio. Una parentesi li raccoglie tutti.', 'At the top sealed interface Stato; below it, connected, its three subtypes: Caricamento, a data object; Pronto, with the list of courses; Errore, with a message. A bracket gathers them all.'), L('La famiglia è chiusa: nessuno può aggiungere un sottotipo da un altro modulo.', 'The family is closed: nobody can add a subtype from another module.')),
            P('Rispetto a un enum, ogni sottotipo di una sealed class può portare dati suoi: solo `Pronto` ha i corsi, solo `Errore` ha il messaggio. Rispetto a una stringa come `"errore"`, un errore di battitura non compila. E se domani aggiungi `Offline`, ogni `when` senza `else` smette di compilare finché non lo gestisci: il compilatore ti mostra la lista dei posti da aggiornare.', 'Compared with an enum, each subtype of a sealed class can carry its own data: only `Pronto` has the courses, only `Errore` has the message. Compared with a string like `"error"`, a typo does not compile. And if tomorrow you add `Offline`, every `when` without `else` stops compiling until you handle it: the compiler gives you the list of places to update.'),
        ], [
            Q('s1', L('Perché usare una sealed class per gli stati?', 'Why use a sealed class for states?'), [L('Il compilatore controlla tutti i casi', 'The compiler checks every case'), L('È più veloce', 'It is faster'), L('Si salva da sola', 'It saves itself'), L('Non serve', 'It is not needed')], 0, L('Niente `else` dimenticati.', 'No forgotten branches.')),
            Q('s2', L('Che cos’è lo smart cast?', 'What is the smart cast?'), [L('Kotlin deduce il tipo dopo un controllo `is`', 'Kotlin infers the type after an `is` check'), L('Una conversione manuale', 'A manual cast'), L('Un errore', 'An error'), L('Un’animazione', 'An animation')], 0, L('Niente cast espliciti.', 'No explicit casts.')),
            Q('s3', L('Che cos’è `data object`?', 'What is `data object`?'), [L('Un oggetto unico con toString leggibile', 'A single object with readable toString'), L('Una classe con dati', 'A class with data'), L('Un database', 'A database'), L('Una lista', 'A list')], 0, L('Utile per stati senza dati.', 'Handy for data-less states.')),
        ], task('kotlin', L('Crea `sealed interface Risultato` con `data class Ok(val valore: Int) : Risultato`.', 'Create `sealed interface Risultato` with `data class Ok(val valore: Int) : Risultato`.'), '', [re('sealed\s+(interface|class)\s+Risultato', L('Il tipo è sealed', 'The type is sealed')), re('data\s+class\s+Ok\s*\(\s*val\s+valore\s*:\s*Int\s*\)\s*:\s*Risultato', L('Ok è un Risultato', 'Ok is a Risultato'))], '', 'sealed interface Risultato {
    data class Ok(val valore: Int) : Risultato
}'), [
            dOrder(pSort(), ['sealed interface Pagamento', 'data class Carta(val ultime4: String) : Pagamento', 'data object Contanti : Pagamento', 'fun etichetta(p: Pagamento) = when (p) {', '    is Carta -> "Carta ${p.ultime4}"', '    Contanti -> "Contanti"', '}'], L('Tipo sealed, sottotipi, when completo.', 'Sealed type, subtypes, exhaustive when.')),
            dMatch(pMatch(), [['interface', L('Contratto di funzioni', 'Contract of functions')], ['sealed', L('Sottotipi chiusi e noti', 'Closed, known subtypes')], ['is', L('Controllo del tipo', 'Type check')], ['object', L('Un solo esemplare', 'A single instance')]], L('Tipi in Kotlin.', 'Types in Kotlin.')),
        ]),
        lesson('coroutines', L('Coroutine', 'Coroutines'), [
            P('Le **coroutine** eseguono lavoro lento senza bloccare: una funzione `suspend` può mettersi in pausa. `launch` avvia un lavoro, `async`/`await` uno che restituisce un valore, dentro uno **scope** che ne controlla la vita.', '**Coroutines** run slow work without blocking: a `suspend` function can pause. `launch` starts a job, `async`/`await` one that returns a value, inside a **scope** that controls its lifetime.'),
            C('suspend fun caricaUtente(id: Int): Utente {
    delay(500)                    // non blocca il thread
    return api.utente(id)
}

fun main() = runBlocking {
    val a = async { caricaUtente(1) }
    val b = async { caricaUtente(2) }
    println("${a.await().nome} e ${b.await().nome}")
}', 'kotlin'),
            P('In Android si usano `viewModelScope` e `lifecycleScope`: quando la schermata sparisce, i lavori si annullano da soli.', 'On Android you use `viewModelScope` and `lifecycleScope`: when the screen goes away, jobs cancel by themselves.'),
            Fig(L(fig_kotlinint_suspend('Thread.sleep(): il thread resta bloccato', 'delay(): la coroutine si sospende, il thread lavora', 'libero', 'tempo →'), fig_kotlinint_suspend('Thread.sleep(): the thread stays blocked', 'delay(): the coroutine suspends, the thread keeps working', 'free', 'time →')), L('Due righe sullo stesso asse del tempo, con un solo thread e due lavori, A e B. Con Thread.sleep il thread aspetta a vuoto, a righe grigie, e B parte solo dopo A. Con delay, mentre A è sospesa il thread esegue B, e tutto finisce molto prima.', 'Two rows on the same time axis, with one thread and two jobs, A and B. With Thread.sleep the thread waits idle, in grey stripes, and B starts only after A. With delay, while A is suspended the thread runs B, and everything finishes much sooner.'), L('Sospendere libera il thread per altro lavoro; bloccare lo spreca.', 'Suspending frees the thread for other work; blocking wastes it.')),
            P('Le coroutine seguono la **concorrenza strutturata**: ogni lavoro nasce dentro uno scope, lo scope aspetta che tutti i figli finiscano, e se uno fallisce o lo scope viene annullato si fermano anche gli altri. Così nessun lavoro resta orfano a consumare batteria. Per il lavoro pesante si cambia thread con `withContext(Dispatchers.IO) { … }` (disco e rete) o `Dispatchers.Default` (calcoli).', 'Coroutines follow **structured concurrency**: every job is born inside a scope, the scope waits for all its children to finish, and if one fails or the scope is cancelled the others stop too. That way no job is left orphaned, draining the battery. For heavy work you switch thread with `withContext(Dispatchers.IO) { … }` (disk and network) or `Dispatchers.Default` (computation).'),
        ], [
            Q('k1', L('Che cosa può fare una funzione `suspend`?', 'What can a `suspend` function do?'), [L('Mettersi in pausa senza bloccare', 'Pause without blocking'), L('Fermare l’app', 'Stop the app'), L('Saltare il codice', 'Skip code'), L('Niente', 'Nothing')], 0, L('Si chiama da una coroutine.', 'It is called from a coroutine.')),
            Q('k2', L('Quale costruttore restituisce un valore?', 'Which builder returns a value?'), ['async', 'launch', 'runBlocking only', 'delay'], 0, L('Si legge con `await`.', 'You read it with `await`.')),
            Q('k3', L('Che differenza c’è tra `delay` e `Thread.sleep`?', 'What is the difference between `delay` and `Thread.sleep`?'), [L('`delay` non blocca il thread', '`delay` does not block the thread'), L('Nessuna', 'None'), L('`delay` è in secondi', '`delay` is in seconds'), L('`sleep` è più preciso', '`sleep` is more precise')], 0, L('Il thread fa altro nel frattempo.', 'The thread does other work meanwhile.')),
        ], task('kotlin', L('Scrivi `suspend fun saluto(): String` che fa `delay(1000)` e restituisce `"Ciao"`.', 'Write `suspend fun saluto(): String` that does `delay(1000)` and returns `"Ciao"`.'), '', [re('suspend\s+fun\s+saluto\s*\(\s*\)\s*:\s*String', L('La funzione è sospendibile', 'The function is suspending')), re('delay\s*\(\s*1000\s*\)', L('Aspetta un secondo', 'It waits one second')), re('return\s+"Ciao"', L('Restituisce “Ciao”', 'It returns “Ciao”'))], '', 'suspend fun saluto(): String {
    delay(1000)
    return "Ciao"
}'), [
            dBlanks(pFill(), "viewModelScope.___ {\n    val a = ___ { api.corsi() }\n    val b = async { api.profilo() }\n    mostra(a.___(), b.await())\n}", ['launch', 'async', 'await'], ['run', 'thread', 'get'], L('Avvia, parallelizza, attendi.', 'Start, parallelise, await.')),
            dFlash(pFlash(), [[L('`launch` restituisce un valore', '`launch` returns a value'), false], [L('`viewModelScope` annulla i lavori quando serve', '`viewModelScope` cancels jobs when needed'), true], [L('Una funzione `suspend` si chiama da una coroutine', 'A `suspend` function is called from a coroutine'), true], [L('`delay` blocca l’intero thread', '`delay` blocks the whole thread'), false]], L('Per un valore c’è `async`.', 'For a value there is `async`.')),
        ]),
        lesson('flow', L('Flow', 'Flow'), [
            P('Un **Flow** è un flusso di valori nel tempo, come un elenco che arriva un pezzo alla volta. Si trasforma con `map` e `filter` e si raccoglie con `collect`. `StateFlow` tiene sempre l’ultimo valore: ideale per lo stato di una schermata.', 'A **Flow** is a stream of values over time, like a list arriving one piece at a time. You transform it with `map` and `filter` and gather it with `collect`. `StateFlow` always holds the latest value: ideal for a screen’s state.'),
            C(L('fun conto(): Flow<Int> = flow {
    for (i in 3 downTo 1) {
        emit(i)
        delay(1000)
    }
}

conto().map { "Mancano $it" }.collect { println(it) }

class CorsiViewModel : ViewModel() {
    private val _stato = MutableStateFlow<Stato>(Stato.Caricamento)
    val stato: StateFlow<Stato> = _stato.asStateFlow()
}', 'fun conto(): Flow<Int> = flow {
    for (i in 3 downTo 1) {
        emit(i)
        delay(1000)
    }
}

conto().map { "$it to go" }.collect { println(it) }

class CorsiViewModel : ViewModel() {
    private val _stato = MutableStateFlow<Stato>(Stato.Caricamento)
    val stato: StateFlow<Stato> = _stato.asStateFlow()
}'), 'kotlin', L('<pre>Mancano 3
Mancano 2
Mancano 1</pre>', '<pre>3 to go
2 to go
1 to go</pre>')),
            P('La versione `Mutable` resta privata nel ViewModel; fuori si espone quella in sola lettura.', 'The `Mutable` version stays private in the ViewModel; outside you expose the read-only one.'),
            Fig(L(fig_kotlinint_flow('.map { "Mancano $it" }', ['Mancano 3', 'Mancano 2', 'Mancano 1']), fig_kotlinint_flow('.map { "$it to go" }', ['3 to go', '2 to go', '1 to go'])), L('Un diagramma a biglie: sulla linea di conto() arrivano i valori 3, 2 e 1, a un secondo di distanza, poi una barra segna la fine. Ognuno passa dal map e arriva sulla linea di collect come frase.', 'A marble diagram: on the conto() line the values 3, 2 and 1 arrive one second apart, then a bar marks the end. Each passes through map and reaches the collect line as a sentence.'), L('I valori arrivano uno alla volta, nel tempo, e ognuno attraversa tutta la catena.', 'Values arrive one at a time, over time, and each one goes through the whole chain.')),
            P('Un `flow { }` è **freddo**: finché nessuno chiama `collect` non succede nulla, e ogni nuovo `collect` fa ripartire il conto da capo. `collect` è una funzione `suspend`, quindi si chiama dentro una coroutine e la tiene occupata finché il flusso non finisce. Uno `StateFlow` invece è **caldo**: esiste e ha un valore anche senza nessuno che ascolta, e chi arriva riceve subito l’ultimo.', 'A `flow { }` is **cold**: until someone calls `collect` nothing happens, and every new `collect` starts the count again from the top. `collect` is a `suspend` function, so it is called inside a coroutine and keeps it busy until the flow ends. A `StateFlow` instead is **hot**: it exists and has a value even with nobody listening, and whoever arrives gets the latest one straight away.'),
        ], [
            Q('w1', L('Come si manda un valore in un `flow { }`?', 'How do you send a value in a `flow { }`?'), ['emit', 'send', 'yield', 'return'], 0, L('`emit(valore)`.', '`emit(value)`.')),
            Q('w2', L('Che cosa tiene sempre uno `StateFlow`?', 'What does a `StateFlow` always hold?'), [L('L’ultimo valore', 'The latest value'), L('Tutti i valori', 'Every value'), L('Niente', 'Nothing'), L('Il primo valore', 'The first value')], 0, L('Chi arriva dopo lo vede subito.', 'Late subscribers see it at once.')),
            Q('w3', L('Perché esporre `StateFlow` e non `MutableStateFlow`?', 'Why expose `StateFlow` and not `MutableStateFlow`?'), [L('Solo il ViewModel deve cambiarlo', 'Only the ViewModel should change it'), L('È più veloce', 'It is faster'), L('È obbligatorio', 'It is mandatory'), L('Non c’è differenza', 'No difference')], 0, L('Dall’esterno si legge soltanto.', 'Outside it is read only.')),
        ], task('kotlin', L('Crea `private val _conta = MutableStateFlow(0)` e `val conta: StateFlow<Int> = _conta`.', 'Create `private val _conta = MutableStateFlow(0)` and `val conta: StateFlow<Int> = _conta`.'), '', [re('private\s+val\s+_conta\s*=\s*MutableStateFlow\s*\(\s*0\s*\)', L('Lo stato modificabile è privato', 'The mutable state is private')), re('val\s+conta\s*:\s*StateFlow<Int>\s*=\s*_conta', L('Esponi la sola lettura', 'You expose read-only'))], '', 'private val _conta = MutableStateFlow(0)
val conta: StateFlow<Int> = _conta'), [
            dOrder(pSort(), ['fun numeri() = flow {', '    emit(1)', '    emit(2)', '}', 'numeri().map { it * 10 }.collect { println(it) }'], L('Produci, trasforma, raccogli.', 'Produce, transform, collect.')),
            dMatch(pMatch(), [['flow { }', L('Crea un flusso', 'Creates a stream')], ['emit', L('Manda un valore', 'Sends a value')], ['collect', L('Riceve i valori', 'Receives the values')], ['StateFlow', L('Ultimo stato sempre disponibile', 'Latest state always available')]], L('I pezzi di Flow.', 'The pieces of Flow.')),
        ]),
        lesson('compose', L('Jetpack Compose', 'Jetpack Compose'), [
            P('**Jetpack Compose** descrive l’interfaccia Android con funzioni `@Composable`. `remember { mutableStateOf(…) }` tiene uno stato che, quando cambia, ridisegna solo ciò che serve. `Column`, `Row` e `Box` dispongono gli elementi.', '**Jetpack Compose** describes the Android interface with `@Composable` functions. `remember { mutableStateOf(…) }` holds state that, when it changes, redraws only what is needed. `Column`, `Row` and `Box` lay out the elements.'),
            C(L('@Composable
fun Contatore() {
    var n by remember { mutableIntStateOf(0) }
    Column(
        modifier = Modifier.padding(16.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
    ) {
        Text("Clic: $n", style = MaterialTheme.typography.titleLarge)
        Button(onClick = { n++ }) { Text("Più uno") }
    }
}', '@Composable
fun Contatore() {
    var n by remember { mutableIntStateOf(0) }
    Column(
        modifier = Modifier.padding(16.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
    ) {
        Text("Clic: $n", style = MaterialTheme.typography.titleLarge)
        Button(onClick = { n++ }) { Text("Plus one") }
    }
}'), 'kotlin', L('<style>body{margin:0;padding:16px;font-family:system-ui,sans-serif;text-align:center;color:#1d1b20}.t{font-size:22px;margin-bottom:10px}.b{display:inline-block;background:#6750a4;color:#fff;padding:10px 24px;border-radius:20px;font-size:14px}</style><div class="t">Clic: 0</div><span class="b">Più uno</span>', '<style>body{margin:0;padding:16px;font-family:system-ui,sans-serif;text-align:center;color:#1d1b20}.t{font-size:22px;margin-bottom:10px}.b{display:inline-block;background:#6750a4;color:#fff;padding:10px 24px;border-radius:20px;font-size:14px}</style><div class="t">Clic: 0</div><span class="b">Plus one</span>')),
            P('Il **Modifier** concatena dimensioni, margini, sfondi e clic. `LazyColumn` mostra liste lunghe in modo efficiente.', 'The **Modifier** chains size, padding, backgrounds and clicks. `LazyColumn` shows long lists efficiently.'),
            P('Quando `n` cambia, Compose richiama le funzioni che lo leggono: è la **ricomposizione**. Per questo lo stato va dentro `remember`: senza, a ogni ricomposizione `n` ripartirebbe da 0. Le funzioni `@Composable` devono essere veloci e senza effetti collaterali, perché Compose può chiamarle spesso e in qualunque ordine. `by` evita di scrivere `n.value` ogni volta.', 'When `n` changes, Compose calls again the functions that read it: that is **recomposition**. That is why state goes inside `remember`: without it, `n` would start again from 0 at every recomposition. `@Composable` functions must be fast and free of side effects, because Compose may call them often and in any order. `by` saves you from writing `n.value` every time.'),
        ], [
            Q('p1', L('Quale annotazione segna una funzione di interfaccia?', 'Which annotation marks a UI function?'), ['@Composable', '@View', '@Ui', '@Widget'], 0, L('Compose le ridisegna quando serve.', 'Compose redraws them when needed.')),
            Q('p2', L('A che cosa serve `remember`?', 'What is `remember` for?'), [L('A conservare lo stato tra un ridisegno e l’altro', 'To keep state across redraws'), L('A salvare su disco', 'To save to disk'), L('A ricordare la password', 'To remember the password'), L('Niente', 'Nothing')], 0, L('Altrimenti ripartirebbe da zero.', 'Otherwise it would reset.')),
            Q('p3', L('Quale contenitore per una lista lunga?', 'Which container for a long list?'), ['LazyColumn', 'Column', 'Row', 'Box'], 0, L('Crea solo le righe visibili.', 'It builds only visible rows.')),
        ], task('kotlin', L('Nella composable dichiara `var testo by remember { mutableStateOf("") }` e un `TextField(value = testo, onValueChange = { testo = it })`.', 'In the composable declare `var testo by remember { mutableStateOf("") }` and a `TextField(value = testo, onValueChange = { testo = it })`.'), '@Composable
fun Campo() {
}', [re('var\s+testo\s+by\s+remember\s*\{\s*mutableStateOf\s*\(\s*""\s*\)\s*\}', L('C’è lo stato', 'There is the state')), re('TextField\s*\(\s*value\s*=\s*testo\s*,\s*onValueChange\s*=\s*\{\s*testo\s*=\s*it\s*\}', L('Il campo è collegato', 'The field is bound'))], '', 'var testo by remember { mutableStateOf("") }
TextField(value = testo, onValueChange = { testo = it })'), [
            dBlanks(pFill(), "@___\nfun Saluto(nome: String) {\n    ___(modifier = Modifier.padding(8.dp)) {\n        Text(\"Ciao \$nome\")\n    }\n}", ['Composable', 'Column'], ['View', 'Div', 'Layout'], L('Annotazione e contenitore.', 'Annotation and container.')),
            dFlash(pFlash(), [[L('In Compose l’interfaccia è fatta di funzioni', 'In Compose the interface is made of functions'), true], [L('Senza `remember` lo stato sopravvive ai ridisegni', 'Without `remember` state survives redraws'), false], [L('Il Modifier concatena margini e dimensioni', 'The Modifier chains padding and size'), true], [L('Una funzione `@Composable` può chiamarne altre', 'A `@Composable` function can call others'), true]], L('Serve `remember`.', '`remember` is needed.')),
        ]),
        lesson('scope-functions', L('Funzioni di scope', 'Scope functions'), [
            P('`let`, `apply`, `also`, `run` e `with` eseguono un blocco di codice su un oggetto. Cambiano in due cose: come chiami l’oggetto nel blocco (`it` oppure `this`) e che cosa restituiscono (l’oggetto stesso o il risultato del blocco).', '`let`, `apply`, `also`, `run` and `with` run a block of code on an object. They differ in two things: how you refer to the object in the block (`it` or `this`) and what they return (the object itself or the block’s result).'),
            C('val email: String? = trovaEmail()
email?.let { println("Scrivo a $it") }          // solo se non è null

val utente = Utente().apply {                    // configura e restituisce l’oggetto
    nome = "Ada"
    eta = 36
}

val lista = mutableListOf(1, 2).also { println("Creata: $it") }

val lunghezza = with(StringBuilder()) {
    append("Fucina")
    length                                       // risultato del blocco
}', 'kotlin'),
            P('Regola pratica: `?.let` per usare un valore nullable, `apply` per configurare un oggetto appena creato, `also` per un’azione in più (come un log), `run` e `with` per calcolare un risultato usando l’oggetto. Non annidarle troppo: il codice diventa difficile da leggere.', 'Rule of thumb: `?.let` to use a nullable value, `apply` to configure a freshly created object, `also` for an extra action (such as a log), `run` and `with` to compute a result using the object. Do not nest them too deeply: the code becomes hard to read.'),
            Fig(L(fig_kotlinint_scope(['funzione', 'l’oggetto nel blocco', 'restituisce', 'il risultato del blocco', 'l’oggetto stesso']), fig_kotlinint_scope(['function', 'the object in the block', 'returns', 'the block’s result', 'the object itself'])), L('Una tabella con cinque righe. let: l’oggetto è it, restituisce il risultato del blocco. run e with: l’oggetto è this, restituiscono il risultato del blocco. apply: this, restituisce l’oggetto stesso. also: it, restituisce l’oggetto stesso.', 'A table with five rows. let: the object is it, it returns the block’s result. run and with: the object is this, they return the block’s result. apply: this, it returns the object itself. also: it, it returns the object itself.'), L('Due domande bastano per scegliere: `it` o `this`? Oggetto o risultato?', 'Two questions are enough to choose: `it` or `this`? Object or result?')),
        ], [
            Q('sf1', L('Che cosa restituisce `apply`?', 'What does `apply` return?'), [L('Il risultato dell’ultima riga', 'The result of the last line'), 'Unit', L('L’oggetto su cui è chiamata', 'The object it is called on'), L('Un Boolean', 'A Boolean')], 2, L('Per questo è perfetta per configurare.', 'That is why it is perfect for configuring.')),
            Q('sf2', L('Quale forma esegue un blocco solo se il valore non è null?', 'Which form runs a block only if the value is not null?'), ['x?.let { }', 'x!!.run { }', 'with(x) { }', 'x?.also = { }'], 0, L('La chiamata sicura salta il blocco se x è null.', 'The safe call skips the block if x is null.')),
            Q('sf3', L('Dentro `apply`, come ti riferisci all’oggetto?', 'Inside `apply`, how do you refer to the object?'), [L('Con `it`', 'With `it`'), L('Con `self`', 'With `self`'), L('Con `obj`', 'With `obj`'), L('Con `this`, spesso sottinteso', 'With `this`, often implicit')], 3, L('Per questo scrivi direttamente `nome = "Ada"`.', 'That is why you write `nome = "Ada"` directly.')),
        ], task('kotlin', L('Configura il paragrafo con `apply`: `val p = Paragrafo().apply { testo = "Ciao" }`.', 'Configure the paragraph with `apply`: `val p = Paragrafo().apply { testo = "Ciao" }`.'), 'class Paragrafo { var testo = "" }
', [re('val\s+p\s*=\s*Paragrafo\s*\(\s*\)\s*\.apply\s*\{\s*(this\.)?testo\s*=\s*"Ciao"\s*\}', L('Configuri con apply', 'You configure with apply'))], '', 'val p = Paragrafo().apply { testo = "Ciao" }'), [
            dMatch(pMatch(), [['let', L('`it`, restituisce il risultato del blocco', '`it`, returns the block’s result')], ['apply', L('`this`, restituisce l’oggetto', '`this`, returns the object')], ['also', L('`it`, restituisce l’oggetto', '`it`, returns the object')], ['run', L('`this`, restituisce il risultato del blocco', '`this`, returns the block’s result')]], L('Le quattro combinazioni.', 'The four combinations.')),
            dFlash(pFlash(), [[L('`apply` restituisce l’oggetto stesso', '`apply` returns the object itself'), true], [L('`x?.let { }` gira anche se x è null', '`x?.let { }` runs even if x is null'), false], [L('`also` è comodo per aggiungere un log', '`also` is handy for adding a log'), true], [L('`with` riceve l’oggetto come argomento', '`with` takes the object as an argument'), true]], L('Con null la chiamata sicura salta il blocco.', 'With null the safe call skips the block.')),
        ]),
        lesson('generics', L('Generics e varianza', 'Generics and variance'), [
            P('Classi e funzioni **generiche** lavorano con un tipo segnaposto: `class Scatola<T>(val contenuto: T)`. Con un vincolo (`T : Comparable<T>`) chiedi che il tipo sappia fare qualcosa.', '**Generic** classes and functions work with a placeholder type: `class Scatola<T>(val contenuto: T)`. With a constraint (`T : Comparable<T>`) you require the type to be able to do something.'),
            C('class Scatola<T>(val contenuto: T)

fun <T : Comparable<T>> massimo(a: T, b: T): T = if (a > b) a else b

val s = Scatola("libro")          // Scatola<String>
println(massimo(3, 8))            // 8

interface Sorgente<out T> { fun prossimo(): T }
interface Confronto<in T> { fun confronta(a: T, b: T): Int }

val numeri: List<Int> = listOf(1, 2)
val qualsiasi: List<Any> = numeri   // va bene: List è covariante (out)

inline fun <reified T> Any.eDi(): Boolean = this is T
println("ciao".eDi<String>())       // true', 'kotlin'),
            P('`out T` vuol dire che il tipo esce soltanto (viene prodotto): così una `List<Int>` vale anche come `List<Any>`. `in T` vuol dire che entra soltanto (viene consumato). `reified`, nelle funzioni `inline`, conserva il tipo a runtime, che di solito viene cancellato.', '`out T` means the type only comes out (it is produced): that is why a `List<Int>` also works as a `List<Any>`. `in T` means it only goes in (it is consumed). `reified`, in `inline` functions, keeps the type at runtime, where it is normally erased.'),
        ], [
            Q('g1', L('Perché `val a: List<Any> = listOf(1, 2)` compila?', 'Why does `val a: List<Any> = listOf(1, 2)` compile?'), [L('Kotlin converte gli Int in String', 'Kotlin converts the Ints to Strings'), L('List è covariante: è dichiarata con `out`', 'List is covariant: it is declared with `out`'), L('List non ha un tipo', 'List has no type'), L('Perché Any è un sottotipo di Int', 'Because Any is a subtype of Int')], 1, L('`MutableList` invece non lo è.', '`MutableList` is not.')),
            Q('g2', L('Che cosa significa `in T` in un’interfaccia?', 'What does `in T` mean in an interface?'), [L('T può solo uscire dai metodi', 'T can only come out of methods'), L('T è un tipo interno al modulo', 'T is a module-internal type'), L('T può solo entrare, come parametro', 'T can only go in, as a parameter'), L('T è facoltativo', 'T is optional')], 2, L('È la controvarianza.', 'It is contravariance.')),
            Q('g3', L('A che cosa serve `reified`?', 'What is `reified` for?'), [L('A rendere la funzione più veloce', 'Making the function faster'), L('A creare una data class', 'Creating a data class'), L('A rendere T nullable', 'Making T nullable'), L('A conoscere il tipo T a runtime in una funzione inline', 'Knowing type T at runtime in an inline function')], 3, L('Permette controlli come `is T`.', 'It allows checks such as `is T`.')),
        ], task('kotlin', L('Scrivi `class Coppia<A, B>(val primo: A, val secondo: B)`.', 'Write `class Coppia<A, B>(val primo: A, val secondo: B)`.'), '', [re('class\s+Coppia\s*<\s*A\s*,\s*B\s*>\s*\(\s*val\s+primo\s*:\s*A\s*,\s*val\s+secondo\s*:\s*B\s*\)', L('La classe ha due tipi generici', 'The class has two generic types'))], '', 'class Coppia<A, B>(val primo: A, val secondo: B)'), [
            dBlanks(pFill(), "class Pila<___> {\n    private val voci = mutableListOf<T>()\n    fun push(v: T) = voci.add(v)\n}\nfun <T : ___<T>> minimo(a: T, b: T) = if (a < b) a else b\ninterface Produttore<___ T> { fun crea(): T }", ['T', 'Comparable', 'out'], ['Any', 'Equatable', 'in'], L('Segnaposto, vincolo, varianza.', 'Placeholder, constraint, variance.')),
            dMatch(pMatch(), [['<T>', L('Tipo segnaposto', 'Placeholder type')], ['out T', L('Il tipo esce soltanto', 'The type only comes out')], ['in T', L('Il tipo entra soltanto', 'The type only goes in')], ['reified', L('Tipo noto a runtime', 'Type known at runtime')], ['T : Comparable<T>', L('Vincolo sul tipo', 'Type constraint')]], L('Generics in Kotlin.', 'Generics in Kotlin.')),
        ]),
        lesson('compose-state', L('Stato e ViewModel in Compose', 'State and ViewModel in Compose'), [
            P('In Compose conviene **sollevare lo stato** (state hoisting): una composable riceve il valore e una funzione per cambiarlo, invece di tenerlo dentro. Così è riutilizzabile e facile da provare. Lo stato di una schermata vive in un **ViewModel**, che sopravvive alla rotazione dello schermo.', 'In Compose it pays to **hoist state**: a composable receives the value and a function to change it, instead of keeping it inside. That makes it reusable and easy to test. A screen’s state lives in a **ViewModel**, which survives screen rotation.'),
            C('class ContatoreViewModel : ViewModel() {
    private val _conta = MutableStateFlow(0)
    val conta: StateFlow<Int> = _conta.asStateFlow()
    fun aumenta() { _conta.update { it + 1 } }
}

@Composable
fun ContatoreScreen(vm: ContatoreViewModel = viewModel()) {
    val conta by vm.conta.collectAsStateWithLifecycle()
    Contatore(valore = conta, onAumenta = vm::aumenta)
}

@Composable
fun Contatore(valore: Int, onAumenta: () -> Unit) {
    Button(onClick = onAumenta) { Text("Clic: $valore") }
}', 'kotlin'),
            P('`collectAsStateWithLifecycle()` trasforma il Flow in stato Compose e smette di raccogliere quando l’app va in background. `rememberSaveable` è come `remember` ma sopravvive anche alla rotazione: va bene per piccoli valori della sola interfaccia.', '`collectAsStateWithLifecycle()` turns the Flow into Compose state and stops collecting when the app goes to the background. `rememberSaveable` is like `remember` but also survives rotation: fine for small, interface-only values.'),
            Fig(L(fig_kotlinint_hoisting('↓ stato', 'eventi ↑'), fig_kotlinint_hoisting('↓ state', 'events ↑')), L('Tre riquadri uno sotto l’altro: ContatoreViewModel con conta, ContatoreScreen, Contatore con il bottone. A sinistra frecce arancioni scendono, conta e poi valore; a destra frecce verdi salgono, onAumenta e poi aumenta.', 'Three boxes one under the other: ContatoreViewModel with conta, ContatoreScreen, Contatore with the button. On the left orange arrows go down, conta and then valore; on the right green arrows go up, onAumenta and then aumenta.'), L('Lo stato scende come parametro, gli eventi salgono come funzioni.', 'State goes down as a parameter, events go up as functions.')),
        ], [
            Q('cs1', L('Che cos’è lo state hoisting?', 'What is state hoisting?'), [L('Salvare lo stato in un file', 'Saving state to a file'), L('Passare a una composable il valore e una funzione per cambiarlo', 'Passing a composable the value and a function to change it'), L('Mettere tutto lo stato in variabili globali', 'Putting all state in global variables'), L('Ricreare lo stato a ogni ridisegno', 'Recreating state at every redraw')], 1, L('Lo stato sale verso chi lo possiede.', 'State moves up to its owner.')),
            Q('cs2', L('Perché lo stato di una schermata sta in un ViewModel?', 'Why does a screen’s state live in a ViewModel?'), [L('Sopravvive ai cambi di configurazione come la rotazione', 'It survives configuration changes such as rotation'), L('Disegna l’interfaccia più in fretta', 'It draws the interface faster'), L('Sostituisce il database', 'It replaces the database'), L('È obbligatorio per usare Text', 'It is required to use Text')], 0, L('E separa la logica dall’interfaccia.', 'And it separates logic from the interface.')),
            Q('cs3', L('Che cosa fa `collectAsStateWithLifecycle()`?', 'What does `collectAsStateWithLifecycle()` do?'), [L('Salva il Flow su disco', 'Saves the Flow to disk'), L('Converte uno StateFlow in LiveData', 'Converts a StateFlow into LiveData'), L('Avvia un nuovo thread', 'Starts a new thread'), L('Trasforma un Flow in stato Compose rispettando il ciclo di vita', 'Turns a Flow into Compose state, respecting the lifecycle')], 3, L('In background smette di raccogliere.', 'In the background it stops collecting.')),
        ], task('kotlin', L('Solleva lo stato: `Interruttore` deve ricevere `acceso: Boolean` e `onCambia: (Boolean) -> Unit` e usarli in `Switch(checked = acceso, onCheckedChange = onCambia)`.', 'Hoist the state: `Interruttore` must take `acceso: Boolean` and `onCambia: (Boolean) -> Unit` and use them in `Switch(checked = acceso, onCheckedChange = onCambia)`.'), '@Composable
fun Interruttore() {
    var acceso by remember { mutableStateOf(false) }
    Switch(checked = acceso, onCheckedChange = { acceso = it })
}', [re('fun\s+Interruttore\s*\(\s*acceso\s*:\s*Boolean\s*,\s*onCambia\s*:\s*\(\s*Boolean\s*\)\s*->\s*Unit\s*,?\s*\)', L('La composable riceve stato ed evento', 'The composable takes state and event')), re('Switch\s*\(\s*checked\s*=\s*acceso\s*,\s*onCheckedChange\s*=\s*onCambia\s*\)', L('Lo Switch usa i parametri', 'The Switch uses the parameters'))], '', '@Composable
fun Interruttore(acceso: Boolean, onCambia: (Boolean) -> Unit) {
    Switch(checked = acceso, onCheckedChange = onCambia)
}'), [
            dOrder(pSort(), ['class NoteViewModel : ViewModel() {', '    private val _note = MutableStateFlow(listOf<String>())', '    val note = _note.asStateFlow()', '    fun aggiungi(n: String) { _note.update { it + n } }', '}'], L('Stato privato, vista in sola lettura, azione.', 'Private state, read-only view, action.')),
            dFlash(pFlash(), [[L('Una composable senza stato interno è più facile da riutilizzare', 'A composable with no internal state is easier to reuse'), true], [L('Il ViewModel viene ricreato a ogni rotazione', 'The ViewModel is recreated at every rotation'), false], [L('`rememberSaveable` sopravvive alla rotazione', '`rememberSaveable` survives rotation'), true], [L('`update { }` cambia un MutableStateFlow in modo sicuro', '`update { }` changes a MutableStateFlow safely'), true]], L('Il ViewModel resta, la schermata si ridisegna.', 'The ViewModel stays, the screen is redrawn.')),
        ]),
    ],
];
