<?php
/** Swift intermediate: structs and classes, protocols, enums, errors, async/await, SwiftUI, generics, @Observable, navigation and actors. */

/** Value against reference: two independent Punto copies, two variables pointing at one Sessione. */
if (!function_exists('fig_swiftint_valueref')) {
    function fig_swiftint_valueref(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="12" y="12" width="296" height="236" rx="14" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="160" y="42" text-anchor="middle" font-weight="700">{$t[0]}</text>
<text x="42" y="102" text-anchor="middle" font-weight="700">a</text>
<rect x="64" y="74" width="214" height="44" rx="10" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/>
<text x="171" y="101" text-anchor="middle">Punto(x: 3, y: 4)</text>
<text x="42" y="172" text-anchor="middle" font-weight="700">b</text>
<rect x="64" y="144" width="214" height="44" rx="10" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/>
<text x="171" y="171" text-anchor="middle">Punto(x: <tspan font-weight="700" fill="#B84508">4</tspan>, y: 4)</text>
<text x="160" y="226" text-anchor="middle" font-size="13" fill="#B84508">{$t[1]}</text>
<rect x="332" y="12" width="296" height="236" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="480" y="42" text-anchor="middle" font-weight="700">{$t[2]}</text>
<rect x="352" y="76" width="60" height="40" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="382" y="101" text-anchor="middle" font-weight="700">s1</text>
<rect x="352" y="146" width="60" height="40" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="382" y="171" text-anchor="middle" font-weight="700">s2</text>
<g stroke="#2F8CF0" stroke-width="2" fill="none"><path d="M412 96 L470 122"/><path d="M459 123 L472 123 L465 112"/><path d="M412 166 L470 142"/><path d="M465 152 L472 141 L459 141"/></g>
<rect x="474" y="100" width="140" height="64" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="544" y="126" text-anchor="middle" font-weight="700">Sessione</text>
<text x="544" y="148" text-anchor="middle" font-size="13">utente: "Ada"</text>
<text x="480" y="226" text-anchor="middle" font-size="13" fill="#1B66BC">{$t[3]}</text>
</svg>
SVG;
    }
}

/** Two awaits one after the other against async let: the same two loads, run in a row or together. A time cursor sweeps across. */
if (!function_exists('fig_swiftint_asynclet')) {
    function fig_swiftint_asynclet(string $seq, string $par, string $sooner, string $time): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 245" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.cur{animation:cur 6s linear infinite}@keyframes cur{from{transform:none}to{transform:translateX(440px)}}@media (prefers-reduced-motion:reduce){.cur{animation:none}}</style>
<text x="40" y="38" font-weight="700">{$seq}</text>
<rect x="40" y="50" width="240" height="34" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="160" y="72" text-anchor="middle" font-size="14">caricaCorsi()</text>
<rect x="280" y="50" width="200" height="34" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="380" y="72" text-anchor="middle" font-size="14">caricaProfilo()</text>
<text x="40" y="126" font-weight="700">{$par}</text>
<rect x="40" y="138" width="240" height="30" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="160" y="158" text-anchor="middle" font-size="14">caricaCorsi()</text>
<rect x="40" y="174" width="200" height="30" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="140" y="194" text-anchor="middle" font-size="14">caricaProfilo()</text>
<path d="M280 132 V210" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="5 4"/>
<text x="292" y="192" font-size="13" font-weight="700" fill="#0B7F5C">{$sooner}</text>
<path class="cur" d="M40 44 V210" stroke="#E8590C" stroke-width="2"/>
<path d="M40 222 H596" stroke="#8A87B0" stroke-width="2"/><path d="M588 215 L600 222 L588 229" stroke="#8A87B0" stroke-width="2" fill="none"/>
<text x="600" y="241" text-anchor="end" font-size="13" fill="#5F5C8A">{$time}</text>
</svg>
SVG;
    }
}

/** A SwiftUI view tree: @State n on ContatoreView, body with a VStack holding a Text and a Button; the button changes n. */
if (!function_exists('fig_swiftint_viewtree')) {
    function fig_swiftint_viewtree(string $button, string $note): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 285" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M440 38 H470"/><path d="M320 60 V100"/><path d="M320 140 V164 H195 V190 M320 164 H445 V190"/></g>
<rect x="200" y="16" width="240" height="44" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="320" y="43" text-anchor="middle" font-weight="700">ContatoreView</text>
<rect x="470" y="16" width="130" height="44" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="535" y="43" text-anchor="middle" font-weight="700" fill="#B84508">@State n</text>
<text x="330" y="86" font-size="13" fill="#5F5C8A">body</text>
<rect x="230" y="100" width="180" height="40" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="320" y="125" text-anchor="middle" font-weight="700">VStack</text>
<rect x="90" y="190" width="210" height="44" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="195" y="217" text-anchor="middle">Text("Clic: \(n)")</text>
<rect x="340" y="190" width="210" height="44" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="445" y="217" text-anchor="middle">Button("{$button}")</text>
<g stroke="#E8590C" stroke-width="2" fill="none"><path d="M550 212 H610 V66"/><path d="M603 76 L610 64 L617 76"/></g>
<text x="602" y="150" text-anchor="end" font-size="14" font-weight="700" fill="#B84508">n += 1</text>
<text x="320" y="270" text-anchor="middle" font-size="14" fill="#5F5C8A">{$note}</text>
</svg>
SVG;
    }
}

/** An actor lets in one task at a time: calls queue up outside, one runs inside. The queue creeps forward. */
if (!function_exists('fig_swiftint_actor')) {
    function fig_swiftint_actor(string $waiting, string $oneAtATime): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.q{animation:q 3s linear infinite}.in{opacity:0;animation:fin 3s linear infinite}.out{animation:fout 3s linear infinite}@keyframes q{from{transform:none}to{transform:translateX(70px)}}@keyframes fin{from{opacity:0}to{opacity:1}}@keyframes fout{from{opacity:1}to{opacity:0}}@media (prefers-reduced-motion:reduce){.q,.in,.out{animation:none}}</style>
<g class="q" font-size="13" text-anchor="middle">
<g class="in"><rect x="10" y="100" width="60" height="36" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="40" y="123">versa</text></g>
<rect x="80" y="100" width="60" height="36" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="110" y="123">versa</text>
<rect x="150" y="100" width="60" height="36" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="180" y="123">versa</text>
<rect x="220" y="100" width="60" height="36" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="250" y="123">versa</text>
<g class="out"><rect x="290" y="100" width="60" height="36" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="320" y="123">versa</text></g>
</g>
<text x="190" y="176" text-anchor="middle" font-size="13" fill="#1B66BC">{$waiting}</text>
<path d="M400 96 V40 H620 V200 H400 V140" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="401" y="41" width="218" height="158" fill="#ECE6FF"/>
<path d="M400 96 V40 H620 V200 H400 V140" fill="none" stroke="#7048E8" stroke-width="2"/>
<text x="510" y="66" text-anchor="middle" font-weight="700">actor Banca</text>
<rect x="440" y="78" width="140" height="34" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="510" y="100" text-anchor="middle" font-size="14">saldo</text>
<rect x="440" y="130" width="140" height="40" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="510" y="155" text-anchor="middle" font-size="14" font-weight="700">versa(10)</text>
<text x="510" y="226" text-anchor="middle" font-size="13" fill="#5A35C9">{$oneAtATime}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'swift-intermediate', 'family' => 'swift', 'after' => 'swift-base',
    'title' => L('Swift intermedio', 'Intermediate Swift'), 'short' => L('Swift intermedio', 'Swift Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Struct e classi, protocolli, enum, errori, async/await, SwiftUI, generics, dati condivisi con @Observable, navigazione e actor di Swift 6.', 'Structs and classes, protocols, enums, errors, async/await, SwiftUI, generics, shared data with @Observable, navigation and Swift 6 actors.'),
    'lessons' => [
        lesson('structs', L('Struct e classi', 'Structs and classes'), [
            P('Una **struct** è un tipo per **valore**: copiandola ottieni un oggetto indipendente. Una **class** è un tipo per **riferimento**: due variabili possono puntare allo stesso oggetto. In Swift si preferiscono le struct.', 'A **struct** is a **value** type: copying it gives an independent object. A **class** is a **reference** type: two variables can point to the same object. In Swift structs are preferred.'),
            C('struct Punto {
    var x: Double
    var y: Double
    func distanza() -> Double { (x * x + y * y).squareRoot() }
    mutating func sposta(dx: Double) { x += dx }
}

var a = Punto(x: 3, y: 4)
var b = a
b.sposta(dx: 1)   // a non cambia

final class Sessione {
    var utente: String
    init(utente: String) { self.utente = utente }
}', 'swift'),
            P('Una struct riceve gratis un costruttore con tutti i campi. Un metodo che la modifica va segnato `mutating`.', 'A struct gets a memberwise initializer for free. A method that changes it must be marked `mutating`.'),
            Fig(L(fig_swiftint_valueref(['struct: tipo per valore', 'b = a fa una copia', 'class: tipo per riferimento', 's2 = s1 condivide l’oggetto']), fig_swiftint_valueref(['struct: value type', 'b = a makes a copy', 'class: reference type', 's2 = s1 shares the object'])), L('A sinistra due scatole separate, a con Punto x 3 e b con Punto x 4: spostare b non ha toccato a. A destra due variabili, s1 e s2, con due frecce verso un solo oggetto Sessione.', 'On the left two separate boxes, a with Punto x 3 and b with Punto x 4: moving b did not touch a. On the right two variables, s1 and s2, with two arrows to a single Sessione object.'), L('Una struct si copia, una classe si condivide.', 'A struct is copied, a class is shared.')),
            P('Quando serve una classe? Quando l’oggetto ha un’**identità** che più parti dell’app devono condividere (una sessione, una connessione, un modello osservato da più viste), quando serve l’ereditarietà o quando lo richiede un’API di sistema. Nota la differenza con `let`: una struct in una costante è immutabile in tutti i suoi campi, mentre per una classe `let` blocca solo il riferimento, e le proprietà `var` restano modificabili.', 'When do you need a class? When the object has an **identity** that several parts of the app must share (a session, a connection, a model watched by several views), when you need inheritance, or when a system API requires it. Note the difference with `let`: a struct in a constant is immutable in all its fields, while for a class `let` only fixes the reference, and its `var` properties stay changeable.'),
        ], [
            Q('s1', L('Copiando una struct, l’originale cambia se modifichi la copia?', 'Copying a struct, does the original change if you modify the copy?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo i numeri', 'Only numbers'), L('Solo se è `let`', 'Only if it is `let`')], 0, L('È un tipo per valore.', 'It is a value type.')),
            Q('s2', L('Che cosa serve a un metodo che modifica una struct?', 'What does a method changing a struct need?'), ['mutating', 'override', 'static', 'final'], 0, L('Lo dichiara esplicitamente.', 'It states it explicitly.')),
            Q('s3', L('Una class è un tipo per…', 'A class is a type by…'), [L('Riferimento', 'Reference'), L('Valore', 'Value'), L('Copia', 'Copy'), L('Nome', 'Name')], 0, L('Più variabili, stesso oggetto.', 'Several variables, same object.')),
        ], task('swift', L('Crea `struct Libro` con `let titolo: String` e `var pagine: Int`.', 'Create `struct Libro` with `let titolo: String` and `var pagine: Int`.'), '', [re('struct\s+Libro\s*\{', L('La struct esiste', 'The struct exists')), re('let\s+titolo\s*:\s*String', L('Il titolo è costante', 'The title is constant')), re('var\s+pagine\s*:\s*Int', L('Le pagine possono cambiare', 'Pages can change'))], '', 'struct Libro {
    let titolo: String
    var pagine: Int
}'), [
            dMatch(pMatch(), [['struct', L('Tipo per valore', 'Value type')], ['class', L('Tipo per riferimento', 'Reference type')], ['mutating', L('Metodo che modifica una struct', 'Method that changes a struct')], ['init', L('Costruttore', 'Initializer')], ['self', L('L’oggetto stesso', 'The object itself')]], L('Tipi in Swift.', 'Types in Swift.')),
            dFlash(pFlash(), [[L('Le struct ricevono un costruttore automatico', 'Structs get an automatic initializer'), true], [L('Modificare una copia di una struct cambia l’originale', 'Changing a copy of a struct changes the original'), false], [L('Le classi possono ereditare', 'Classes can inherit'), true], [L('Le struct sono tipi valore', 'Structs are value types'), true]], L('Le struct si copiano.', 'Structs are copied.')),
        ]),
        lesson('protocols', L('Protocolli ed estensioni', 'Protocols and extensions'), [
            P('Un **protocollo** elenca ciò che un tipo deve offrire; struct, classi ed enum possono adottarlo. Un’**estensione** aggiunge metodi a un tipo esistente, anche a quelli di sistema.', 'A **protocol** lists what a type must offer; structs, classes and enums can adopt it. An **extension** adds methods to an existing type, even system ones.'),
            C('protocol Descrivibile {
    var descrizione: String { get }
}

struct Corso: Descrivibile, Identifiable {
    let id = UUID()
    let titolo: String
    var descrizione: String { "Corso di \(titolo)" }
}

extension Int {
    var alQuadrato: Int { self * self }
}
print(5.alQuadrato)', 'swift'),
            P('`Identifiable`, `Equatable`, `Codable` sono protocolli di sistema molto usati: il terzo trasforma i dati in JSON e ritorno.', '`Identifiable`, `Equatable`, `Codable` are widely used system protocols: the third turns data into JSON and back.'),
            P('La forza dei protocolli sta nel poter scrivere codice che non conosce il tipo concreto: una funzione che accetta un `Descrivibile` funziona con corsi, utenti o qualunque tipo futuro che adotti il protocollo. Con un’estensione del protocollo stesso (`extension Descrivibile { … }`) puoi anche dare un’**implementazione predefinita**, che ogni tipo riceve gratis e può sostituire con una sua.', 'The power of protocols lies in writing code that does not know the concrete type: a function that accepts a `Descrivibile` works with courses, users or any future type that adopts the protocol. With an extension of the protocol itself (`extension Descrivibile { … }`) you can also provide a **default implementation**, which every type gets for free and can replace with its own.'),
        ], [
            Q('p1', L('Che cosa descrive un protocollo?', 'What does a protocol describe?'), [L('Ciò che un tipo deve offrire', 'What a type must offer'), L('Un protocollo di rete', 'A network protocol'), L('Un file', 'A file'), L('Una schermata', 'A screen')], 0, L('Come un’interfaccia.', 'Like an interface.')),
            Q('p2', L('Che cosa fa un’estensione?', 'What does an extension do?'), [L('Aggiunge funzioni a un tipo esistente', 'Adds features to an existing type'), L('Allunga una stringa', 'Lengthens a string'), L('Crea una sottoclasse', 'Creates a subclass'), L('Installa librerie', 'Installs libraries')], 0, L('Anche ai tipi di sistema.', 'Even to system types.')),
            Q('p3', L('Quale protocollo converte in JSON?', 'Which protocol converts to JSON?'), ['Codable', 'Equatable', 'Hashable', 'Identifiable'], 0, L('Encodable + Decodable.', 'Encodable + Decodable.')),
        ], task('swift', L('Crea `protocol Volante { func vola() }` e `struct Uccello: Volante` che lo implementa.', 'Create `protocol Volante { func vola() }` and `struct Uccello: Volante` implementing it.'), '', [re('protocol\s+Volante\s*\{\s*func\s+vola\s*\(\s*\)', L('Il protocollo esiste', 'The protocol exists')), re('struct\s+Uccello\s*:\s*Volante', L('Uccello lo adotta', 'Uccello adopts it')), re('func\s+vola\s*\(\s*\)\s*\{', L('Implementa il metodo', 'It implements the method'))], '', 'protocol Volante { func vola() }

struct Uccello: Volante {
    func vola() { print("Volo!") }
}'), [
            dBlanks(pFill(), "___ Pagabile {\n    func totale() -> Double\n}\n___ Double {\n    var euro: String { \"\\(self) €\" }\n}", ['protocol', 'extension'], ['interface', 'class', 'struct'], L('Contratto ed estensione.', 'Contract and extension.')),
            dMatch(pMatch(), [['Codable', L('JSON e ritorno', 'JSON and back')], ['Equatable', L('Si confronta con ==', 'Compared with ==')], ['Identifiable', L('Ha un id', 'Has an id')], ['Comparable', L('Si ordina con <', 'Sorted with <')]], L('Protocolli di sistema.', 'System protocols.')),
        ]),
        lesson('enums', L('Enum e errori', 'Enums and errors'), [
            P('Gli **enum** di Swift possono avere **valori associati**: ogni caso porta dati suoi. Gli errori sono tipi che adottano `Error`; una funzione che può fallire è `throws` e si chiama con `try` dentro `do`/`catch`.', 'Swift **enums** can have **associated values**: each case carries its own data. Errors are types adopting `Error`; a function that may fail is `throws` and is called with `try` inside `do`/`catch`.'),
            C(L('enum Stato {
    case caricamento
    case pronto(dati: [String])
    case errore(String)
}

enum ErroreLogin: Error { case passwordErrata, contoBloccato }

func entra(_ pw: String) throws -> String {
    guard pw == "segreta" else { throw ErroreLogin.passwordErrata }
    return "Benvenuto"
}

do {
    print(try entra("1234"))
} catch ErroreLogin.passwordErrata {
    print("Password errata")
}', 'enum Stato {
    case caricamento
    case pronto(dati: [String])
    case errore(String)
}

enum ErroreLogin: Error { case passwordErrata, contoBloccato }

func entra(_ pw: String) throws -> String {
    guard pw == "segreta" else { throw ErroreLogin.passwordErrata }
    return "Welcome"
}

do {
    print(try entra("1234"))
} catch ErroreLogin.passwordErrata {
    print("Wrong password")
}'), 'swift', L('<pre>Password errata</pre>', '<pre>Wrong password</pre>')),
            P('`try?` trasforma l’errore in `nil`: comodo quando non serve sapere perché è fallito.', '`try?` turns the error into `nil`: handy when you do not need to know why it failed.'),
            P('Per leggere un enum si usa `switch`, che deve coprire ogni caso: se domani aggiungi `case offline`, il compilatore ti indica tutti i punti da aggiornare. Con i valori associati lo `switch` estrae anche i dati: `case .pronto(let dati): mostra(dati)`. Così uno stato impossibile, come «pronto ma senza dati», non si può nemmeno scrivere.', 'To read an enum you use `switch`, which must cover every case: if tomorrow you add `case offline`, the compiler points you to every place to update. With associated values the `switch` also extracts the data: `case .pronto(let dati): mostra(dati)`. That way an impossible state, such as “ready but with no data”, cannot even be written.'),
        ], [
            Q('e1', L('Che cosa sono i valori associati?', 'What are associated values?'), [L('Dati portati da un caso dell’enum', 'Data carried by an enum case'), L('Valori predefiniti', 'Default values'), L('Costanti globali', 'Global constants'), L('Errori', 'Errors')], 0, L('`case pronto(dati:)`.', '`case pronto(dati:)`.')),
            Q('e2', L('Come chiami una funzione `throws`?', 'How do you call a `throws` function?'), [L('Con `try`', 'With `try`'), L('Con `await`', 'With `await`'), L('Con `catch`', 'With `catch`'), L('Normalmente', 'Normally')], 0, L('Dentro `do` / `catch`.', 'Inside `do` / `catch`.')),
            Q('e3', L('Che cosa fa `try?`', 'What does `try?` do?'), [L('Restituisce nil se fallisce', 'Returns nil if it fails'), L('Riprova', 'Retries'), L('Ignora il risultato', 'Ignores the result'), L('Crasha', 'Crashes')], 0, L('Il risultato è un optional.', 'The result is an optional.')),
        ], task('swift', L('Crea `enum Meteo { case sole, pioggia(mm: Int) }`.', 'Create `enum Meteo { case sole, pioggia(mm: Int) }`.'), '', [re('enum\s+Meteo\s*\{', L('L’enum esiste', 'The enum exists')), re('case\s+sole', L('C’è il sole', 'There is sun')), re('pioggia\s*\(\s*mm\s*:\s*Int\s*\)', L('La pioggia ha i millimetri', 'Rain carries millimetres'))], '', 'enum Meteo {
    case sole, pioggia(mm: Int)
}'), [
            dOrder(pSort(), ['do {', '    let dati = try carica()', '    mostra(dati)', '} catch {', '    print("Errore: \(error)")', '}'], L('Prova, usa, gestisci.', 'Try, use, handle.')),
            dFlash(pFlash(), [[L('Un caso di enum può portare dati', 'An enum case can carry data'), true], [L('Una funzione `throws` si chiama senza `try`', 'A `throws` function is called without `try`'), false], [L('Nel `catch` generico l’errore si chiama `error`', 'In a generic `catch` the error is named `error`'), true], [L('Uno `switch` su un enum deve coprire tutti i casi', 'A `switch` on an enum must cover every case'), true]], L('Serve sempre `try`.', '`try` is always needed.')),
        ]),
        lesson('concurrency', L('async e await', 'async and await'), [
            P('Una funzione `async` può sospendersi mentre aspetta (la rete, il disco) senza bloccare l’app. Si chiama con `await`. `async let` avvia più attese insieme.', 'An `async` function can suspend while waiting (network, disk) without blocking the app. You call it with `await`. `async let` starts several waits together.'),
            Fig(L(fig_swiftint_asynclet('await, uno dopo l’altro', 'async let, insieme', 'finito prima', 'tempo →'), fig_swiftint_asynclet('await, one after the other', 'async let, together', 'done sooner', 'time →')), L('Due righe sullo stesso asse del tempo. Nella prima caricaCorsi e caricaProfilo sono una dopo l’altra. Nella seconda, con async let, partono insieme e una linea verde tratteggiata segna che si finisce prima.', 'Two rows on the same time axis. In the first, caricaCorsi and caricaProfilo run one after the other. In the second, with async let, they start together and a green dashed line marks that it finishes sooner.'), L('Con async let il tempo totale è quello dell’attesa più lunga, non la somma.', 'With async let the total time is the longest wait, not the sum.')),
            C('struct Corso: Codable { let titolo: String }

func caricaCorsi() async throws -> [Corso] {
    let url = URL(string: "https://example.com/api/corsi")!
    let (dati, _) = try await URLSession.shared.data(from: url)
    return try JSONDecoder().decode([Corso].self, from: dati)
}

async let corsi = caricaCorsi()
async let profilo = caricaProfilo()
let (c, p) = try await (corsi, profilo)', 'swift'),
            P('L’interfaccia va aggiornata dal **main actor**. Le viste SwiftUI sono già isolate sul main actor; i tuoi tipi che toccano l’interfaccia si segnano con `@MainActor`. Con Swift 6.2 e Xcode 26 i nuovi progetti usano `@MainActor` come isolamento predefinito per tutto il codice dell’app.', 'The interface must be updated from the **main actor**. SwiftUI views are already isolated to the main actor; your own types that touch the interface are marked `@MainActor`. With Swift 6.2 and Xcode 26, new projects use `@MainActor` as the default isolation for all app code.'),
        ], [
            Q('c1', L('Come si chiama una funzione `async`?', 'How do you call an `async` function?'), [L('Con `await`', 'With `await`'), L('Con `try`', 'With `try`'), L('Con `go`', 'With `go`'), L('Normalmente', 'Normally')], 0, L('Se lancia errori anche `try`.', 'If it throws, also `try`.')),
            Q('c2', L('Che cosa fa `async let`?', 'What does `async let` do?'), [L('Avvia un compito in parallelo', 'Starts a task in parallel'), L('Crea una costante lenta', 'Creates a slow constant'), L('Rimanda il codice', 'Postpones code'), L('Niente', 'Nothing')], 0, L('Lo si attende dopo.', 'You await it later.')),
            Q('c3', L('Da dove si aggiorna l’interfaccia?', 'Where is the interface updated from?'), [L('Dal main actor', 'From the main actor'), L('Da qualsiasi thread', 'From any thread'), L('Dal server', 'From the server'), L('Da un file', 'From a file')], 0, L('`@MainActor`.', '`@MainActor`.')),
        ], task('swift', L('Scrivi `func saluto() async -> String` che fa `try? await Task.sleep(for: .seconds(1))` e restituisce `"Ciao"`.', 'Write `func saluto() async -> String` that does `try? await Task.sleep(for: .seconds(1))` and returns `"Ciao"`.'), '', [re('func\s+saluto\s*\(\s*\)\s+async\s*->\s*String', L('La funzione è asincrona', 'The function is async')), re('await\s+Task\.sleep', L('Aspetta', 'It waits')), re('return\s+"Ciao"', L('Restituisce “Ciao”', 'It returns “Ciao”'))], '', 'func saluto() async -> String {
    try? await Task.sleep(for: .seconds(1))
    return "Ciao"
}'), [
            dBug(pBug(), ['func carica() async throws -> Data {', '    let (dati, _) = try URLSession.shared.data(from: url)', '    return dati', '}'], 1, '    let (dati, _) = try await URLSession.shared.data(from: url)', L('Una chiamata `async` vuole `await`.', 'An `async` call needs `await`.')),
            dMatch(pMatch(), [['async', L('Può sospendersi', 'Can suspend')], ['await', L('Aspetta il risultato', 'Waits for the result')], ['async let', L('Avvia in parallelo', 'Starts in parallel')], ['@MainActor', L('Sul thread dell’interfaccia', 'On the UI thread')]], L('La concorrenza in Swift.', 'Concurrency in Swift.')),
        ]),
        lesson('swiftui', L('SwiftUI', 'SwiftUI'), [
            P('**SwiftUI** descrive l’interfaccia come una struct che adotta `View`. `@State` tiene un valore che, quando cambia, ridisegna la vista. Gli stack (`VStack`, `HStack`) dispongono gli elementi.', '**SwiftUI** describes the interface as a struct adopting `View`. `@State` holds a value that redraws the view when it changes. Stacks (`VStack`, `HStack`) lay out the elements.'),
            C(L('struct ContatoreView: View {
    @State private var n = 0

    var body: some View {
        VStack(spacing: 12) {
            Text("Clic: \(n)")
                .font(.title)
            Button("Più uno") { n += 1 }
                .buttonStyle(.borderedProminent)
        }
        .padding()
    }
}', 'struct ContatoreView: View {
    @State private var n = 0

    var body: some View {
        VStack(spacing: 12) {
            Text("Clic: \(n)")
                .font(.title)
            Button("Plus one") { n += 1 }
                .buttonStyle(.borderedProminent)
        }
        .padding()
    }
}'), 'swift', L('<style>body{margin:0;padding:16px;font-family:system-ui,sans-serif;text-align:center;color:#000}.t{font-size:28px;margin-bottom:12px}.b{display:inline-block;background:#007AFF;color:#fff;padding:8px 16px;border-radius:999px;font-size:16px}</style><div class="t">Clic: 0</div><span class="b">Più uno</span>', '<style>body{margin:0;padding:16px;font-family:system-ui,sans-serif;text-align:center;color:#000}.t{font-size:28px;margin-bottom:12px}.b{display:inline-block;background:#007AFF;color:#fff;padding:8px 16px;border-radius:999px;font-size:16px}</style><div class="t">Clic: 0</div><span class="b">Plus one</span>')),
            P('I **modificatori** (`.font`, `.padding`) si concatenano e restituiscono una vista nuova. `List` e `NavigationStack` creano elenchi e navigazione.', '**Modifiers** (`.font`, `.padding`) chain and return a new view. `List` and `NavigationStack` create lists and navigation.'),
            Fig(L(fig_swiftint_viewtree('Più uno', 'n cambia → SwiftUI ricalcola body'), fig_swiftint_viewtree('Plus one', 'n changes → SwiftUI recomputes body')), L('ContatoreView ha accanto lo stato @State n. Il suo body contiene un VStack con due figli, Text che mostra n e Button. Una freccia arancione dal Button torna a @State n con l’etichetta n += 1.', 'ContatoreView has the @State n state beside it. Its body holds a VStack with two children, a Text showing n and a Button. An orange arrow goes from the Button back to @State n, labelled n += 1.'), L('L’azione cambia lo stato, lo stato ridisegna le viste che lo leggono.', 'The action changes the state, the state redraws the views that read it.')),
            P('Una vista SwiftUI non è un oggetto sullo schermo ma una **descrizione** economica: quando `n` cambia, SwiftUI richiama `body`, confronta il risultato con il precedente e aggiorna solo ciò che è diverso. Per questo `body` deve solo descrivere, senza effetti collaterali. Per passare a una vista figlia il permesso di cambiare un `@State` si usa un **binding**: `Toggle("Attivo", isOn: $attivo)`.', 'A SwiftUI view is not an object on the screen but a cheap **description**: when `n` changes, SwiftUI calls `body` again, compares the result with the previous one and updates only what differs. That is why `body` should only describe, with no side effects. To let a child view change a `@State` you pass a **binding**: `Toggle("On", isOn: $attivo)`.'),
        ], [
            Q('u1', L('Che cosa fa `@State`?', 'What does `@State` do?'), [L('Tiene un valore che ridisegna la vista', 'Holds a value that redraws the view'), L('Salva su disco', 'Saves to disk'), L('Crea un thread', 'Creates a thread'), L('Cambia colore', 'Changes colour')], 0, L('Lo stato locale di una vista.', 'A view’s local state.')),
            Q('u2', L('Quale contenitore impila in verticale?', 'Which container stacks vertically?'), ['VStack', 'HStack', 'ZStack', 'List'], 0, L('`HStack` in orizzontale, `ZStack` sovrapposti.', '`HStack` horizontal, `ZStack` overlapped.')),
            Q('u3', L('Che cosa restituisce `body`?', 'What does `body` return?'), ['some View', 'String', 'Void', 'Int'], 0, L('Una qualche vista.', 'Some view.')),
        ], task('swift', L('Nella vista dichiara `@State private var acceso = false` e un `Toggle("Luce", isOn: $acceso)`.', 'In the view declare `@State private var acceso = false` and a `Toggle("Luce", isOn: $acceso)`.'), 'struct LuceView: View {
    var body: some View {
    }
}', [re('@State\s+(private\s+)?var\s+acceso\s*=\s*false', L('C’è lo stato', 'There is the state')), re('Toggle\s*\(\s*"Luce"\s*,\s*isOn\s*:\s*\$acceso\s*\)', L('Il toggle è collegato', 'The toggle is bound'))], '', '@State private var acceso = false

var body: some View {
    Toggle("Luce", isOn: $acceso)
}'), [
            dOrder(pSort(), ['struct SalutoView: View {', '    @State private var nome = ""', '    var body: some View {', '        TextField("Nome", text: $nome)', '    }', '}'], L('Vista, stato, corpo, campo.', 'View, state, body, field.')),
            dBlanks(pFill(), "___ {\n    Text(\"Fucina\").font(.___)\n    Button(\"Entra\") { entra() }\n}\n.___()", ['VStack', 'title', 'padding'], ['Column', 'big', 'margin'], L('Stack, stile, spaziatura.', 'Stack, style, spacing.')),
        ]),
        lesson('generics', L('Generics', 'Generics'), [
            P('Con i **generics** scrivi una funzione o un tipo una volta sola per molti tipi. Il segnaposto `<T>` viene sostituito dal tipo vero; con un vincolo (`T: Comparable`) chiedi che il tipo sappia fare qualcosa.', 'With **generics** you write a function or a type once for many types. The placeholder `<T>` is replaced by the real type; with a constraint (`T: Comparable`) you require the type to be able to do something.'),
            C('func piuGrande<T: Comparable>(_ a: T, _ b: T) -> T {
    a > b ? a : b
}
piuGrande(3, 8)            // 8
piuGrande("ape", "zebra")  // "zebra"

struct Pila<Elemento> {
    private var elementi: [Elemento] = []
    mutating func push(_ e: Elemento) { elementi.append(e) }
    mutating func pop() -> Elemento? { elementi.popLast() }
}

var pila = Pila<String>()
pila.push("a")

func descrivi(_ x: some CustomStringConvertible) -> String { x.description }
let forme: [any Forma] = [Cerchio(), Quadrato()]', 'swift'),
            P('Anche `Array<Element>` e `Optional<Wrapped>` sono generici. `some Protocollo` indica un tipo preciso ma nascosto, scelto dal compilatore; `any Protocollo` è una scatola che può contenere tipi diversi, utile per le collezioni miste.', '`Array<Element>` and `Optional<Wrapped>` are generic too. `some Protocol` means one precise but hidden type, chosen by the compiler; `any Protocol` is a box that can hold different types, handy for mixed collections.'),
        ], [
            Q('g1', L('Che cosa significa `<T: Comparable>`?', 'What does `<T: Comparable>` mean?'), [L('T può essere solo un numero', 'T can only be a number'), L('T deve potersi confrontare con < e >', 'T must be comparable with < and >'), L('T è una classe che eredita da Comparable', 'T is a class inheriting from Comparable'), L('T viene convertito in stringa', 'T is converted to a string')], 1, L('È un vincolo: anche le stringhe vanno bene.', 'It is a constraint: strings work too.')),
            Q('g2', L('Che cosa restituisce `pop()` su una `Pila` vuota?', 'What does `pop()` return on an empty `Pila`?'), ['0', L('Un crash', 'A crash'), 'nil', L('Una pila nuova', 'A new stack')], 2, L('`popLast()` restituisce un optional.', '`popLast()` returns an optional.')),
            Q('g3', L('Quando serve `any Forma` invece di `some Forma`?', 'When do you need `any Forma` instead of `some Forma`?'), [L('Quando tipi diversi stanno nella stessa collezione', 'When different types sit in the same collection'), L('Quando il tipo è sempre lo stesso', 'When the type is always the same'), L('Quando la funzione è async', 'When the function is async'), L('Mai: sono sinonimi', 'Never: they are synonyms')], 0, L('`some` fissa un solo tipo concreto.', '`some` fixes a single concrete type.')),
        ], task('swift', L('Scrivi `func primo<T>(_ lista: [T]) -> T?` che restituisce `lista.first`.', 'Write `func primo<T>(_ lista: [T]) -> T?` that returns `lista.first`.'), '', [re('func\s+primo\s*<\s*T\s*>\s*\(\s*_\s+lista\s*:\s*\[\s*T\s*\]\s*\)\s*->\s*T\?', L('La firma è generica', 'The signature is generic')), re('(return\s+)?lista\.first', L('Restituisci il primo', 'You return the first'))], '', 'func primo<T>(_ lista: [T]) -> T? {
    lista.first
}'), [
            dBlanks(pFill(), "func massimo<T: ___>(_ v: [T]) -> T? {\n    v.max()\n}\nstruct Scatola<___> {\n    var contenuto: Valore\n}\nlet s = Scatola<Int>(contenuto: ___)", ['Comparable', 'Valore', '5'], ['Equatable', 'T', '"5"'], L('Vincolo, segnaposto, valore del tipo giusto.', 'Constraint, placeholder, value of the right type.')),
            dFlash(pFlash(), [[L('Array è un tipo generico', 'Array is a generic type'), true], [L('I generics funzionano solo con le classi', 'Generics only work with classes'), false], [L('`some View` nasconde un tipo preciso', '`some View` hides one precise type'), true], [L('Un vincolo limita i tipi accettati', 'A constraint limits the accepted types'), true]], L('Funzionano con struct, enum e classi.', 'They work with structs, enums and classes.')),
        ]),
        lesson('observable', L('Dati condivisi con @Observable', 'Shared data with @Observable'), [
            P('`@State` basta per lo stato di una sola vista. Quando più viste condividono gli stessi dati si usa una classe con la macro **`@Observable`** (iOS 17 e successivi): SwiftUI ridisegna solo le viste che leggono le proprietà cambiate.', '`@State` is enough for one view’s state. When several views share the same data you use a class with the **`@Observable`** macro (iOS 17 and later): SwiftUI redraws only the views that read the changed properties.'),
            C('import SwiftUI
import Observation

@Observable
final class Carrello {
    var pezzi: [String] = []
    var totale: Int { pezzi.count }
    func aggiungi(_ p: String) { pezzi.append(p) }
}

@main
struct NegozioApp: App {
    @State private var carrello = Carrello()
    var body: some Scene {
        WindowGroup {
            ContentView().environment(carrello)
        }
    }
}

struct Riepilogo: View {
    @Environment(Carrello.self) private var carrello
    var body: some View {
        Text("\(carrello.totale) pezzi")
    }
}', 'swift'),
            P('Chi crea l’oggetto lo tiene in `@State`; le altre viste lo ricevono come parametro o con `@Environment`. Per collegare un campo a una sua proprietà (`$profilo.nome`) si usa `@Bindable`. Prende il posto del vecchio `ObservableObject` con `@Published`.', 'Whoever creates the object keeps it in `@State`; other views receive it as a parameter or through `@Environment`. To bind a field to one of its properties (`$profilo.nome`) you use `@Bindable`. It replaces the older `ObservableObject` with `@Published`.'),
        ], [
            Q('o1', L('Che cosa fa `@Observable` su una classe?', 'What does `@Observable` do on a class?'), [L('Salva la classe su disco', 'Saves the class to disk'), L('La rende un tipo per valore', 'Makes it a value type'), L('Fa ridisegnare le viste che leggono le proprietà cambiate', 'Redraws the views that read the changed properties'), L('La trasforma in una vista', 'Turns it into a view')], 2, L('Il tracciamento è per singola proprietà.', 'Tracking is per property.')),
            Q('o2', L('Come legge una vista lontana un oggetto passato con `.environment(carrello)`?', 'How does a distant view read an object passed with `.environment(carrello)`?'), ['@Environment(Carrello.self)', '@State var carrello', '@Published carrello', '@Binding carrello'], 0, L('Il tipo fa da chiave.', 'The type acts as the key.')),
            Q('o3', L('Che cosa sostituisce `@Observable`?', 'What does `@Observable` replace?'), [L('`@State` nelle viste', '`@State` in views'), L('Le struct', 'Structs'), L('Il protocollo View', 'The View protocol'), L('`ObservableObject` con `@Published`', '`ObservableObject` with `@Published`')], 3, L('Meno codice e aggiornamenti più mirati.', 'Less code and more targeted updates.')),
        ], task('swift', L('Rendi osservabile la classe: aggiungi `@Observable` prima di `final class Profilo`.', 'Make the class observable: add `@Observable` before `final class Profilo`.'), 'final class Profilo {
    var nome = ""
}', [re('@Observable\s+(final\s+)?class\s+Profilo', L('La classe è osservabile', 'The class is observable'))], '', '@Observable
final class Profilo {
    var nome = ""
}'), [
            dMatch(pMatch(), [['@Observable', L('Classe che SwiftUI osserva', 'Class SwiftUI observes')], ['@State', L('Chi crea l’oggetto lo tiene qui', 'The creator keeps the object here')], ['@Environment', L('Legge un oggetto condiviso', 'Reads a shared object')], ['@Bindable', L('Crea collegamenti alle proprietà', 'Creates bindings to properties')]], L('Il flusso dei dati in SwiftUI.', 'Data flow in SwiftUI.')),
            dFlash(pFlash(), [[L('`@Observable` si applica alle classi', '`@Observable` applies to classes'), true], [L('Con `@Observable` servono ancora i `@Published`', 'With `@Observable` you still need `@Published`'), false], [L('Una vista si aggiorna solo se legge la proprietà cambiata', 'A view updates only if it reads the changed property'), true], [L('`.environment(oggetto)` lo rende disponibile alle viste figlie', '`.environment(object)` makes it available to child views'), true]], L('Niente più `@Published`.', 'No more `@Published`.')),
        ]),
        lesson('navigation', L('Liste e navigazione', 'Lists and navigation'), [
            P('`List` mostra righe scorrevoli da una collezione di elementi `Identifiable`. `NavigationStack` aggiunge la barra del titolo e la navigazione: `NavigationLink(value:)` dice dove andare e `.navigationDestination(for:)` costruisce la pagina per quel tipo di valore.', '`List` shows scrolling rows from a collection of `Identifiable` items. `NavigationStack` adds the title bar and navigation: `NavigationLink(value:)` says where to go and `.navigationDestination(for:)` builds the page for that type of value.'),
            C(L('struct Corso: Identifiable, Hashable {
    let id = UUID()
    let titolo: String
}

struct CorsiView: View {
    let corsi: [Corso]
    var body: some View {
        NavigationStack {
            List(corsi) { corso in
                NavigationLink(corso.titolo, value: corso)
            }
            .navigationTitle("Corsi")
            .navigationDestination(for: Corso.self) { corso in
                Text(corso.titolo).font(.largeTitle)
            }
        }
    }
}', 'struct Corso: Identifiable, Hashable {
    let id = UUID()
    let titolo: String
}

struct CorsiView: View {
    let corsi: [Corso]
    var body: some View {
        NavigationStack {
            List(corsi) { corso in
                NavigationLink(corso.titolo, value: corso)
            }
            .navigationTitle("Courses")
            .navigationDestination(for: Corso.self) { corso in
                Text(corso.titolo).font(.largeTitle)
            }
        }
    }
}'), 'swift', L('<style>body{margin:0;padding:12px 16px;font-family:system-ui,sans-serif;background:#F2F2F7;color:#000}h1{font-size:30px;margin:4px 0 12px}.l{background:#fff;border-radius:10px}.r{display:flex;justify-content:space-between;padding:11px 14px;border-bottom:1px solid #E5E5EA}.r:last-child{border:0}.r span{color:#C4C4C7}</style><h1>Corsi</h1><div class="l"><div class="r">Swift<span>&gt;</span></div><div class="r">SwiftUI<span>&gt;</span></div><div class="r">Kotlin<span>&gt;</span></div></div>', '<style>body{margin:0;padding:12px 16px;font-family:system-ui,sans-serif;background:#F2F2F7;color:#000}h1{font-size:30px;margin:4px 0 12px}.l{background:#fff;border-radius:10px}.r{display:flex;justify-content:space-between;padding:11px 14px;border-bottom:1px solid #E5E5EA}.r:last-child{border:0}.r span{color:#C4C4C7}</style><h1>Courses</h1><div class="l"><div class="r">Swift<span>&gt;</span></div><div class="r">SwiftUI<span>&gt;</span></div><div class="r">Kotlin<span>&gt;</span></div></div>')),
            P('Il valore del link deve essere `Hashable`. `.sheet` presenta una vista dal basso, `.toolbar` aggiunge bottoni alla barra. `NavigationView` è il nome vecchio, deprecato: nei progetti nuovi usa `NavigationStack` (o `NavigationSplitView` su iPad e Mac).', 'The link’s value must be `Hashable`. `.sheet` presents a view from the bottom, `.toolbar` adds buttons to the bar. `NavigationView` is the old, deprecated name: in new projects use `NavigationStack` (or `NavigationSplitView` on iPad and Mac).'),
        ], [
            Q('nv1', L('Che cosa devono essere gli elementi passati a `List(corsi)`?', 'What must the items passed to `List(corsi)` be?'), ['Codable', 'Identifiable', 'Comparable', 'Sendable'], 1, L('Ogni riga ha bisogno di un `id` stabile.', 'Each row needs a stable `id`.')),
            Q('nv2', L('Che cosa costruisce `.navigationDestination(for: Corso.self)`?', 'What does `.navigationDestination(for: Corso.self)` build?'), [L('La pagina da aprire quando si tocca un link con un Corso', 'The page to open when a link with a Corso is tapped'), L('L’elenco di tutti i corsi', 'The list of all courses'), L('Una barra di schede in basso', 'A tab bar at the bottom'), L('Un nuovo tipo Corso', 'A new Corso type')], 0, L('Riceve il valore del link.', 'It receives the link’s value.')),
            Q('nv3', L('Che cosa usi nei progetti nuovi al posto di `NavigationView`?', 'What do you use in new projects instead of `NavigationView`?'), ['TabView', 'NavigationLink', 'ScrollView', 'NavigationStack'], 3, L('`NavigationView` è deprecato.', '`NavigationView` is deprecated.')),
        ], task('swift', L('Dentro la `List` crea per ogni elemento `NavigationLink(corso.titolo, value: corso)`.', 'Inside the `List` create `NavigationLink(corso.titolo, value: corso)` for each item.'), 'NavigationStack {
    List(corsi) { corso in
    }
}', [re('NavigationLink\s*\(\s*corso\.titolo\s*,\s*value\s*:\s*corso\s*\)', L('Ogni riga apre il suo corso', 'Each row opens its course'))], '', 'NavigationStack {
    List(corsi) { corso in
        NavigationLink(corso.titolo, value: corso)
    }
}'), [
            dBlanks(pFill(), "___ {\n    List(note) { nota in\n        NavigationLink(nota.titolo, ___: nota)\n    }\n    .navigationTitle(\"Note\")\n    .navigationDestination(for: Nota.self) { NotaView(nota: \$0) }\n}", ['NavigationStack', 'value'], ['VStack', 'destination', 'to'], L('Pila di navigazione e valore del link.', 'Navigation stack and link value.')),
            dMatch(pMatch(), [['List', L('Righe scorrevoli', 'Scrolling rows')], ['NavigationStack', L('Pila di pagine con titolo', 'Stack of pages with a title')], ['NavigationLink', L('Riga che apre una pagina', 'Row that opens a page')], ['.sheet', L('Vista che sale dal basso', 'View sliding up from the bottom')], ['.toolbar', L('Bottoni nella barra', 'Buttons in the bar')]], L('Navigare in SwiftUI.', 'Navigating in SwiftUI.')),
        ]),
        lesson('actors', L('Actor e Swift 6', 'Actors and Swift 6'), [
            P('Una **data race** succede quando due thread toccano gli stessi dati insieme e almeno uno scrive. In modalità Swift 6 il compilatore le blocca già in compilazione. Lo strumento principale è l’**actor**: un tipo per riferimento che protegge il suo stato facendo entrare un compito alla volta.', 'A **data race** happens when two threads touch the same data at once and at least one writes. In Swift 6 language mode the compiler blocks them at compile time. The main tool is the **actor**: a reference type that protects its state by letting in one task at a time.'),
            C('actor Banca {
    private var saldo = 0
    func versa(_ importo: Int) { saldo += importo }
    func leggi() -> Int { saldo }
}

let banca = Banca()

await withTaskGroup(of: Void.self) { gruppo in
    for _ in 1...100 {
        gruppo.addTask { await banca.versa(10) }
    }
}
print(await banca.leggi())   // sempre 1000', 'swift'),
            P('Da fuori ogni chiamata a un actor vuole `await`, perché potrebbe dover aspettare il suo turno. I valori che passano da un dominio di concorrenza all’altro devono essere `Sendable`: struct ed enum con campi `Sendable` lo sono da sole, le classi con proprietà modificabili no. `@MainActor` è un actor speciale: il thread dell’interfaccia.', 'From outside every call to an actor needs `await`, because it may have to wait its turn. Values crossing from one concurrency domain to another must be `Sendable`: structs and enums with `Sendable` fields are so automatically, classes with mutable properties are not. `@MainActor` is a special actor: the interface thread.'),
            Fig(L(fig_swiftint_actor('compiti in attesa (await)', 'dentro: uno alla volta'), fig_swiftint_actor('tasks waiting (await)', 'inside: one at a time')), L('Una fila di chiamate versa aspetta fuori da un riquadro viola, l’actor Banca. Dentro c’è il saldo e una sola chiamata versa(10) in esecuzione; la fila avanza verso l’ingresso.', 'A queue of versa calls waits outside a purple box, the Banca actor. Inside are the balance and a single versa(10) call running; the queue moves towards the entrance.'), L('Per questo il totale è sempre 1000: due versamenti non toccano mai il saldo insieme.', 'That is why the total is always 1000: two deposits never touch the balance at once.')),
        ], [
            Q('ac1', L('Che cosa garantisce un actor?', 'What does an actor guarantee?'), [L('Che il suo stato sia toccato da un compito alla volta', 'That its state is touched by one task at a time'), L('Che il codice giri sul thread principale', 'That the code runs on the main thread'), L('Che le sue funzioni non lancino errori', 'That its functions never throw'), L('Che i dati siano salvati su disco', 'That the data is saved to disk')], 0, L('Così le data race non sono possibili.', 'That way data races cannot happen.')),
            Q('ac2', L('Perché chiamare `banca.versa(10)` da fuori richiede `await`?', 'Why does calling `banca.versa(10)` from outside need `await`?'), [L('Perché `versa` lancia errori', 'Because `versa` throws'), L('Perché è una funzione statica', 'Because it is a static function'), L('Perché può dover aspettare il proprio turno', 'Because it may have to wait its turn'), L('Perché gli actor sono sempre lenti', 'Because actors are always slow')], 2, L('Il punto di attesa è esplicito.', 'The waiting point is explicit.')),
            Q('ac3', L('Quale di questi tipi è `Sendable` senza fare nulla?', 'Which of these types is `Sendable` with no extra work?'), [L('Una classe con proprietà `var`', 'A class with `var` properties'), L('Una struct con campi `Int` e `String`', 'A struct with `Int` and `String` fields'), L('Una closure che modifica una variabile esterna', 'A closure changing an outside variable'), L('Un puntatore a memoria non sicura', 'A pointer to unsafe memory')], 1, L('I tipi per valore con campi Sendable si copiano in sicurezza.', 'Value types with Sendable fields are copied safely.')),
        ], task('swift', L('Trasforma la classe in un actor: scrivi `actor Contatore` al posto di `class Contatore`.', 'Turn the class into an actor: write `actor Contatore` instead of `class Contatore`.'), 'class Contatore {
    var valore = 0
    func incrementa() { valore += 1 }
}', [re('^\s*actor\s+Contatore\s*\{', L('Contatore è un actor', 'Contatore is an actor'))], '', 'actor Contatore {
    var valore = 0
    func incrementa() { valore += 1 }
}'), [
            dBug(pBug(), ['actor Cassa {', '    var totale = 0', '    func aggiungi(_ n: Int) { totale += n }', '}', 'let cassa = Cassa()', 'cassa.aggiungi(5)'], 5, 'await cassa.aggiungi(5)', L('Da fuori un actor si chiama con `await`.', 'From outside, an actor is called with `await`.')),
            dFlash(pFlash(), [[L('In modalità Swift 6 le data race sono errori di compilazione', 'In Swift 6 mode data races are compile errors'), true], [L('Un actor è un tipo per valore', 'An actor is a value type'), false], [L('`@MainActor` corrisponde al thread dell’interfaccia', '`@MainActor` is the interface thread'), true], [L('Una struct con campi Sendable è Sendable', 'A struct with Sendable fields is Sendable'), true]], L('Gli actor sono tipi per riferimento.', 'Actors are reference types.')),
        ]),
    ],
];
