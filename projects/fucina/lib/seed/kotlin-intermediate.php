<?php
/** Kotlin intermediate: classes and data classes, sealed types, coroutines, flows and Jetpack Compose. */

return [
    'id' => 'kotlin-intermediate', 'family' => 'kotlin', 'after' => 'kotlin-base',
    'title' => L('Kotlin intermedio', 'Intermediate Kotlin'), 'short' => L('Kotlin intermedio', 'Kotlin Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Classi e data class, interfacce, sealed class, coroutine, Flow e le prime schermate con Jetpack Compose.', 'Classes and data classes, interfaces, sealed classes, coroutines, Flow and first screens with Jetpack Compose.'),
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
            C('fun conto(): Flow<Int> = flow {
    for (i in 3 downTo 1) {
        emit(i)
        delay(1000)
    }
}

conto().map { "Mancano $it" }.collect { println(it) }

class CorsiViewModel : ViewModel() {
    private val _stato = MutableStateFlow<Stato>(Stato.Caricamento)
    val stato: StateFlow<Stato> = _stato
}', 'kotlin'),
            P('La versione `Mutable` resta privata nel ViewModel; fuori si espone quella in sola lettura.', 'The `Mutable` version stays private in the ViewModel; outside you expose the read-only one.'),
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
            C('@Composable
fun Contatore() {
    var n by remember { mutableStateOf(0) }
    Column(
        modifier = Modifier.padding(16.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
    ) {
        Text("Clic: $n", style = MaterialTheme.typography.titleLarge)
        Button(onClick = { n++ }) { Text("Più uno") }
    }
}', 'kotlin'),
            P('Il **Modifier** concatena dimensioni, margini, sfondi e clic. `LazyColumn` mostra liste lunghe in modo efficiente.', 'The **Modifier** chains size, padding, backgrounds and clicks. `LazyColumn` shows long lists efficiently.'),
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
    ],
];
