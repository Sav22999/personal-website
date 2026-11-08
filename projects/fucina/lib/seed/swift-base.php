<?php
/** Swift basics: constants and variables, optionals, control flow, collections and functions. */

return [
    'id' => 'swift-base', 'family' => 'swift', 'after' => null,
    'title' => L('Swift base', 'Swift Basics'), 'short' => L('Swift base', 'Swift Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di iPhone, iPad e Mac: costanti, optional, condizioni, collezioni e funzioni.', 'The language of iPhone, iPad and Mac: constants, optionals, conditions, collections and functions.'),
    'lessons' => [
        lesson('values', L('Costanti e variabili', 'Constants and variables'), [
            P('In Swift `let` crea una **costante** (non cambia), `var` una **variabile**. Il tipo si deduce dal valore, ma si può scrivere: `let nome: String = "Ada"`. I valori entrano nel testo con `\(valore)`.', 'In Swift `let` creates a **constant** (it does not change), `var` a **variable**. The type is inferred from the value, but you can write it: `let nome: String = "Ada"`. Values enter text with `\(value)`.'),
            C('let nome = "Ada"
var anni = 36
anni += 1
let altezza: Double = 1.68
print("\(nome) ha \(anni) anni")', 'swift'),
            P('Preferisci `let`: il compilatore ti avvisa se una `var` non viene mai cambiata.', 'Prefer `let`: the compiler warns you if a `var` is never changed.'),
        ], [
            Q('v1', L('Quale parola crea una costante?', 'Which word creates a constant?'), ['let', 'var', 'const', 'final'], 0, L('`var` è per ciò che cambia.', '`var` is for what changes.')),
            Q('v2', L('Come inserisci `x` in una stringa?', 'How do you put `x` into a string?'), ['"\\(x)"', '"${x}"', '"{x}"', '"#x"'], 0, L('Barra rovesciata e parentesi.', 'Backslash and brackets.')),
            Q('v3', L('Che cosa succede se cambi una `let`?', 'What happens if you change a `let`?'), [L('Errore di compilazione', 'Compile error'), L('Cambia', 'It changes'), L('Viene ignorato', 'It is ignored'), L('Diventa var', 'It becomes var')], 0, L('Le costanti non cambiano.', 'Constants do not change.')),
        ], task('swift', L('Crea `let corso = "Swift"` e `var livello = 1`.', 'Create `let corso = "Swift"` and `var livello = 1`.'), '', [re('let\s+corso\s*(:\s*String\s*)?=\s*"Swift"', L('La costante esiste', 'The constant exists')), re('var\s+livello\s*(:\s*Int\s*)?=\s*1', L('La variabile esiste', 'The variable exists'))], '', 'let corso = "Swift"
var livello = 1'), [
            dMatch(pMatch(), [['let', L('Costante', 'Constant')], ['var', L('Variabile', 'Variable')], ['String', L('Testo', 'Text')], ['Int', L('Intero', 'Integer')], ['Double', L('Decimale', 'Decimal')]], L('Le basi di Swift.', 'Swift basics.')),
            dFlash(pFlash(), [[L('In Swift servono i punti e virgola', 'Swift needs semicolons'), false], [L('Il tipo si può dedurre', 'The type can be inferred'), true], [L('`let` non si può riassegnare', '`let` cannot be reassigned'), true]], L('I punti e virgola sono facoltativi.', 'Semicolons are optional.')),
        ]),
        lesson('optionals', L('Optional', 'Optionals'), [
            P('Un **optional** (`String?`) può contenere un valore oppure `nil`. Prima di usarlo va “scartato”: `if let` o `guard let` lo aprono in sicurezza, `??` dà un valore di riserva.', 'An **optional** (`String?`) can hold a value or `nil`. Before using it you “unwrap” it: `if let` or `guard let` open it safely, `??` gives a fallback.'),
            C('var soprannome: String? = nil

if let s = soprannome {
    print("Ciao \(s)")
} else {
    print("Nessun soprannome")
}

let mostrato = soprannome ?? "Ospite"

func saluta(_ nome: String?) {
    guard let nome else { return }
    print("Ciao \(nome)")
}', 'swift'),
            P('Il punto esclamativo (`soprannome!`) forza l’apertura e fa crashare l’app se il valore è `nil`: usalo solo se sei certo.', 'The exclamation mark (`soprannome!`) forces unwrapping and crashes the app if the value is `nil`: use it only when certain.'),
        ], [
            Q('o1', L('Che cosa può contenere `Int?`', 'What can `Int?` hold?'), [L('Un intero o nil', 'An int or nil'), L('Solo interi', 'Only ints'), L('Solo nil', 'Only nil'), L('Testo', 'Text')], 0, L('È un optional.', 'It is an optional.')),
            Q('o2', L('Che cosa fa `a ?? b`?', 'What does `a ?? b` do?'), [L('Usa b se a è nil', 'Uses b if a is nil'), L('Somma', 'Adds'), L('Confronta', 'Compares'), L('Dà errore', 'Fails')], 0, L('Valore di riserva.', 'Fallback value.')),
            Q('o3', L('Perché evitare `!`?', 'Why avoid `!`?'), [L('Crasha se il valore è nil', 'It crashes if the value is nil'), L('È lento', 'It is slow'), L('È deprecato', 'It is deprecated'), L('Non funziona', 'It does not work')], 0, L('Meglio `if let` o `guard let`.', 'Better `if let` or `guard let`.')),
        ], task('swift', L('Scarta `email` con `if let e = email { print(e) }`.', 'Unwrap `email` with `if let e = email { print(e) }`.'), 'let email: String? = cercaEmail()
', [re('if\s+let\s+e\s*=\s*email\s*\{', L('Lo scarti in sicurezza', 'You unwrap it safely')), re('print\s*\(\s*e\s*\)', L('Lo usi', 'You use it'))], '', 'if let e = email {
    print(e)
}'), [
            dBug(pBug(), ['let numero: Int? = Int("abc")', 'let doppio = numero! * 2', 'print(doppio)'], 1, 'let doppio = (numero ?? 0) * 2', L('`Int("abc")` è nil: forzarlo fa crashare.', '`Int("abc")` is nil: forcing it crashes.')),
            dBlanks(pFill(), "func mostra(_ voto: Int?) {\n    ___ let voto ___ { return }\n    print(voto)\n}\nlet v = valore ___ 0", ['guard', 'else', '??'], ['if', 'then', '||'], L('Uscita anticipata e riserva.', 'Early exit and fallback.')),
        ]),
        lesson('control', L('Condizioni e cicli', 'Conditions and loops'), [
            P('`if` non vuole parentesi intorno alla condizione ma sempre le graffe. `switch` deve coprire tutti i casi e non “cade” nel successivo. `for-in` scorre intervalli e collezioni.', '`if` needs no brackets around the condition but always braces. `switch` must cover every case and does not “fall through”. `for-in` walks ranges and collections.'),
            C('for i in 1...3 {
    print("Giro \(i)")
}

switch voto {
case 9...10: print("Ottimo")
case 6..<9: print("Promosso")
default: print("Da ripassare")
}', 'swift'),
            P('`1...3` include il 3, `1..<3` no. `while` ripete finché la condizione è vera.', '`1...3` includes 3, `1..<3` does not. `while` repeats while the condition is true.'),
        ], [
            Q('c1', L('Quali numeri dà `1..<4`?', 'Which numbers does `1..<4` give?'), ['1, 2, 3', '1, 2, 3, 4', '0, 1, 2, 3', '4'], 0, L('Intervallo semiaperto.', 'Half-open range.')),
            Q('c2', L('Lo `switch` di Swift deve…', 'Swift’s `switch` must…'), [L('Coprire tutti i casi', 'Cover every case'), L('Avere `break`', 'Have `break`'), L('Usare solo numeri', 'Use only numbers'), L('Stare in una funzione', 'Be in a function')], 0, L('Altrimenti serve `default`.', 'Otherwise you need `default`.')),
            Q('c3', L('Servono le parentesi dopo `if`?', 'Do you need brackets after `if`?'), [L('No, ma servono le graffe', 'No, but braces are required'), L('Sì, sempre', 'Yes, always'), L('Solo con `else`', 'Only with `else`'), L('Né parentesi né graffe', 'Neither brackets nor braces')], 0, L('Le graffe sono obbligatorie.', 'Braces are mandatory.')),
        ], task('swift', L('Con `for n in 1...5` stampa ogni `n`.', 'With `for n in 1...5` print each `n`.'), '', [re('for\s+n\s+in\s+1\s*\.\.\.\s*5\s*\{', L('Il ciclo è giusto', 'The loop is right')), re('print\s*\(\s*n\s*\)', L('Stampi n', 'You print n'))], '', 'for n in 1...5 {
    print(n)
}'), [
            dOrder(pSort(), ['var conto = 3', 'while conto > 0 {', '    print(conto)', '    conto -= 1', '}', 'print("Via!")'], L('Prepara, ripeti, avanza, finisci.', 'Set up, repeat, advance, finish.')),
            dFlash(pFlash(), [[L('`1...5` include il 5', '`1...5` includes 5'), true], [L('Nello `switch` un caso continua nel successivo', 'In `switch` a case falls into the next'), false], [L('`for-in` scorre anche gli array', '`for-in` also walks arrays'), true]], L('Niente fall-through.', 'No fall-through.')),
        ]),
        lesson('collections', L('Array e dizionari', 'Arrays and dictionaries'), [
            P('Un **array** `[Int]` tiene valori in ordine; un **dizionario** `[String: Int]` associa chiavi e valori; un **Set** non ha doppioni. Con `let` sono immutabili, con `var` si possono cambiare.', 'An **array** `[Int]` holds values in order; a **dictionary** `[String: Int]` maps keys to values; a **Set** has no duplicates. With `let` they are immutable, with `var` they can change.'),
            C('var voti = [7, 8]
voti.append(9)
print(voti.count, voti.first ?? 0)

var eta: [String: Int] = ["Ada": 36]
eta["Bea"] = 28
for (nome, anni) in eta {
    print(nome, anni)
}

let alti = voti.filter { $0 > 7 }', 'swift'),
            P('Leggere un dizionario dà un optional: la chiave potrebbe mancare. `$0` è il primo argomento in una closure breve.', 'Reading a dictionary gives an optional: the key might be missing. `$0` is the first argument in a short closure.'),
        ], [
            Q('a1', L('Come aggiungi in fondo a un array?', 'How do you add to the end of an array?'), ['append', 'push', 'add', 'insertLast'], 0, L('Solo se è una `var`.', 'Only if it is a `var`.')),
            Q('a2', L('Che cosa dà `eta["Cleo"]` se la chiave manca?', 'What does `eta["Cleo"]` give if the key is missing?'), ['nil', '0', L('Un crash', 'A crash'), '""'], 0, L('Il risultato è un optional.', 'The result is an optional.')),
            Q('a3', L('Che cos’è `$0`?', 'What is `$0`?'), [L('Il primo argomento della closure', 'The closure’s first argument'), L('Il primo elemento', 'The first item'), L('Una variabile globale', 'A global variable'), L('Zero dollari', 'Zero dollars')], 0, L('Abbreviazione nelle closure.', 'Shorthand in closures.')),
        ], task('swift', L('Crea `var colori = ["rosso"]` e aggiungi `"blu"` con `append`.', 'Create `var colori = ["rosso"]` and add `"blu"` with `append`.'), '', [re('var\s+colori\s*(:\s*\[String\]\s*)?=\s*\[\s*"rosso"\s*\]', L('L’array esiste', 'The array exists')), re('colori\.append\s*\(\s*"blu"\s*\)', L('Aggiungi il blu', 'You add blue'))], '', 'var colori = ["rosso"]
colori.append("blu")'), [
            dMatch(pMatch(), [['[Int]', L('Array di interi', 'Array of ints')], ['[String: Int]', L('Dizionario', 'Dictionary')], ['Set<String>', L('Insieme senza doppioni', 'Set without duplicates')], ['.count', L('Quanti elementi', 'How many items')]], L('Le collezioni di Swift.', 'Swift collections.')),
            dBlanks(pFill(), "let numeri = [1, 2, 3, 4]\nlet pari = numeri.___ { ___ % 2 == 0 }\nlet doppi = numeri.___ { $0 * 2 }", ['filter', '$0', 'map'], ['where', 'it', 'each'], L('Filtra e trasforma.', 'Filter and transform.')),
        ]),
        lesson('functions', L('Funzioni e closure', 'Functions and closures'), [
            P('`func` crea una funzione. Ogni parametro ha un’**etichetta** usata nella chiamata; `_` la toglie. Il tipo di ritorno segue la freccia `->`.', '`func` creates a function. Each parameter has a **label** used at the call; `_` removes it. The return type follows the arrow `->`.'),
            C('func area(base: Double, altezza: Double) -> Double {
    base * altezza
}
area(base: 3, altezza: 2)

func saluta(_ nome: String, da mittente: String = "Fucina") -> String {
    "Ciao \(nome), da \(mittente)"
}
saluta("Ada")

let raddoppia = { (n: Int) in n * 2 }', 'swift'),
            P('Se il corpo è una sola espressione, `return` si può omettere. Una **closure** è una funzione senza nome tra graffe.', 'If the body is a single expression, `return` can be omitted. A **closure** is a nameless function between braces.'),
        ], [
            Q('f1', L('Che cosa fa `_` davanti a un parametro?', 'What does `_` in front of a parameter do?'), [L('Toglie l’etichetta nella chiamata', 'Removes the label at the call'), L('Lo rende privato', 'Makes it private'), L('Lo ignora', 'Ignores it'), L('Lo rende optional', 'Makes it optional')], 0, L('Si chiama `saluta("Ada")`.', 'You call `saluta("Ada")`.')),
            Q('f2', L('Dove si scrive il tipo di ritorno?', 'Where do you write the return type?'), [L('Dopo `->`', 'After `->`'), L('Prima di `func`', 'Before `func`'), L('Dopo il nome', 'After the name'), L('Non si scrive', 'It is not written')], 0, L('`func f() -> Int`.', '`func f() -> Int`.')),
            Q('f3', L('Che cos’è una closure?', 'What is a closure?'), [L('Una funzione senza nome', 'A nameless function'), L('Una classe chiusa', 'A closed class'), L('Un ciclo', 'A loop'), L('Un errore', 'An error')], 0, L('Si scrive tra graffe.', 'It is written in braces.')),
        ], task('swift', L('Scrivi `func doppio(_ n: Int) -> Int` che restituisce `n * 2`.', 'Write `func doppio(_ n: Int) -> Int` that returns `n * 2`.'), '', [re('func\s+doppio\s*\(\s*_\s+n\s*:\s*Int\s*\)\s*->\s*Int', L('La firma è giusta', 'The signature is right')), re('(return\s+)?n\s*\*\s*2', L('Restituisce il doppio', 'It returns the double'))], '', 'func doppio(_ n: Int) -> Int {
    n * 2
}'), [
            dOrder(pSort(), ['func media(_ voti: [Double]) -> Double {', '    guard !voti.isEmpty else { return 0 }', '    return voti.reduce(0, +) / Double(voti.count)', '}', 'print(media([6, 8]))'], L('Firma, controllo, calcolo, chiamata.', 'Signature, check, calculation, call.')),
            dFlash(pFlash(), [[L('Le etichette rendono chiare le chiamate', 'Labels make calls clear'), true], [L('Un parametro non può avere un valore predefinito', 'A parameter cannot have a default value'), false], [L('Con una sola espressione si può omettere `return`', 'With a single expression `return` can be omitted'), true]], L('I valori predefiniti sono comuni.', 'Default values are common.')),
        ]),
    ],
];
