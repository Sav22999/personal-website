<?php
/** Swift intermediate: structs and classes, protocols, enums, errors, async/await and SwiftUI. */

return [
    'id' => 'swift-intermediate', 'family' => 'swift', 'after' => 'swift-base',
    'title' => L('Swift intermedio', 'Intermediate Swift'), 'short' => L('Swift intermedio', 'Swift Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Struct e classi, protocolli, enum, errori, async/await e le prime schermate in SwiftUI.', 'Structs and classes, protocols, enums, errors, async/await and your first SwiftUI screens.'),
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
        ], [
            Q('s1', L('Copiando una struct, l’originale cambia se modifichi la copia?', 'Copying a struct, does the original change if you modify the copy?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo i numeri', 'Only numbers'), L('Solo se è `let`', 'Only if it is `let`')], 0, L('È un tipo per valore.', 'It is a value type.')),
            Q('s2', L('Che cosa serve a un metodo che modifica una struct?', 'What does a method changing a struct need?'), ['mutating', 'override', 'static', 'final'], 0, L('Lo dichiara esplicitamente.', 'It states it explicitly.')),
            Q('s3', L('Una class è un tipo per…', 'A class is a type by…'), [L('Riferimento', 'Reference'), L('Valore', 'Value'), L('Copia', 'Copy'), L('Nome', 'Name')], 0, L('Più variabili, stesso oggetto.', 'Several variables, same object.')),
        ], task('swift', L('Crea `struct Libro` con `let titolo: String` e `var pagine: Int`.', 'Create `struct Libro` with `let titolo: String` and `var pagine: Int`.'), '', [re('struct\s+Libro\s*\{', L('La struct esiste', 'The struct exists')), re('let\s+titolo\s*:\s*String', L('Il titolo è costante', 'The title is constant')), re('var\s+pagine\s*:\s*Int', L('Le pagine possono cambiare', 'Pages can change'))], '', 'struct Libro {
    let titolo: String
    var pagine: Int
}'), [
            dMatch(pMatch(), [['struct', L('Tipo per valore', 'Value type')], ['class', L('Tipo per riferimento', 'Reference type')], ['mutating', L('Metodo che modifica una struct', 'Method that changes a struct')], ['init', L('Costruttore', 'Initializer')], ['self', L('L’oggetto stesso', 'The object itself')]], L('Tipi in Swift.', 'Types in Swift.')),
            dFlash(pFlash(), [[L('Le struct ricevono un costruttore automatico', 'Structs get an automatic initializer'), true], [L('Modificare una copia di una struct cambia l’originale', 'Changing a copy of a struct changes the original'), false], [L('Le classi possono ereditare', 'Classes can inherit'), true]], L('Le struct si copiano.', 'Structs are copied.')),
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
            C('enum Stato {
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
}', 'swift'),
            P('`try?` trasforma l’errore in `nil`: comodo quando non serve sapere perché è fallito.', '`try?` turns the error into `nil`: handy when you do not need to know why it failed.'),
        ], [
            Q('e1', L('Che cosa sono i valori associati?', 'What are associated values?'), [L('Dati portati da un caso dell’enum', 'Data carried by an enum case'), L('Valori predefiniti', 'Default values'), L('Costanti globali', 'Global constants'), L('Errori', 'Errors')], 0, L('`case pronto(dati:)`.', '`case pronto(dati:)`.')),
            Q('e2', L('Come chiami una funzione `throws`?', 'How do you call a `throws` function?'), [L('Con `try`', 'With `try`'), L('Con `await`', 'With `await`'), L('Con `catch`', 'With `catch`'), L('Normalmente', 'Normally')], 0, L('Dentro `do` / `catch`.', 'Inside `do` / `catch`.')),
            Q('e3', L('Che cosa fa `try?`', 'What does `try?` do?'), [L('Restituisce nil se fallisce', 'Returns nil if it fails'), L('Riprova', 'Retries'), L('Ignora il risultato', 'Ignores the result'), L('Crasha', 'Crashes')], 0, L('Il risultato è un optional.', 'The result is an optional.')),
        ], task('swift', L('Crea `enum Meteo { case sole, pioggia(mm: Int) }`.', 'Create `enum Meteo { case sole, pioggia(mm: Int) }`.'), '', [re('enum\s+Meteo\s*\{', L('L’enum esiste', 'The enum exists')), re('case\s+sole', L('C’è il sole', 'There is sun')), re('pioggia\s*\(\s*mm\s*:\s*Int\s*\)', L('La pioggia ha i millimetri', 'Rain carries millimetres'))], '', 'enum Meteo {
    case sole, pioggia(mm: Int)
}'), [
            dOrder(pSort(), ['do {', '    let dati = try carica()', '    mostra(dati)', '} catch {', '    print("Errore: \(error)")', '}'], L('Prova, usa, gestisci.', 'Try, use, handle.')),
            dFlash(pFlash(), [[L('Un caso di enum può portare dati', 'An enum case can carry data'), true], [L('Una funzione `throws` si chiama senza `try`', 'A `throws` function is called without `try`'), false], [L('Nel `catch` generico l’errore si chiama `error`', 'In a generic `catch` the error is named `error`'), true]], L('Serve sempre `try`.', '`try` is always needed.')),
        ]),
        lesson('concurrency', L('async e await', 'async and await'), [
            P('Una funzione `async` può sospendersi mentre aspetta (la rete, il disco) senza bloccare l’app. Si chiama con `await`. `async let` avvia più attese insieme.', 'An `async` function can suspend while waiting (network, disk) without blocking the app. You call it with `await`. `async let` starts several waits together.'),
            C('struct Corso: Codable { let titolo: String }

func caricaCorsi() async throws -> [Corso] {
    let url = URL(string: "https://example.com/api/corsi")!
    let (dati, _) = try await URLSession.shared.data(from: url)
    return try JSONDecoder().decode([Corso].self, from: dati)
}

async let corsi = caricaCorsi()
async let profilo = caricaProfilo()
let (c, p) = try await (corsi, profilo)', 'swift'),
            P('L’interfaccia va aggiornata dal **main actor**: in SwiftUI succede da solo, altrove si usa `@MainActor`.', 'The interface must be updated from the **main actor**: in SwiftUI this happens by itself, elsewhere you use `@MainActor`.'),
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
            C('struct ContatoreView: View {
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
}', 'swift'),
            P('I **modificatori** (`.font`, `.padding`) si concatenano e restituiscono una vista nuova. `List` e `NavigationStack` creano elenchi e navigazione.', '**Modifiers** (`.font`, `.padding`) chain and return a new view. `List` and `NavigationStack` create lists and navigation.'),
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
    ],
];
