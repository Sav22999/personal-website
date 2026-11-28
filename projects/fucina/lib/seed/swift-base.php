<?php
/** Swift basics: constants and variables, optionals, control flow, collections, functions, strings, tuples and conversions. */

/** An optional as a box: full (if let opens it) or empty (?? gives a fallback, ! crashes). */
if (!function_exists('fig_swiftbase_optional')) {
    function fig_swiftbase_optional(string $full, string $empty, string $guest): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="20" y="24" width="150" height="80" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="32" y="44" font-size="13" font-weight="700" fill="#5F5C8A">String?</text>
<rect x="50" y="54" width="90" height="38" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="95" y="79" text-anchor="middle" font-weight="700">"Ada"</text>
<text x="95" y="124" text-anchor="middle" font-size="13" fill="#5F5C8A">{$full}</text>
<rect x="20" y="144" width="150" height="80" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="32" y="164" font-size="13" font-weight="700" fill="#5F5C8A">String?</text>
<rect x="50" y="174" width="90" height="38" rx="8" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/>
<text x="95" y="199" text-anchor="middle" fill="#5F5C8A">nil</text>
<text x="95" y="244" text-anchor="middle" font-size="13" fill="#5F5C8A">{$empty}</text>
<g stroke-width="2" fill="none">
<path d="M176 64 H296" stroke="#0B7F5C"/><path d="M288 57 L298 64 L288 71" stroke="#0B7F5C"/>
<path d="M176 168 H296" stroke="#E8590C"/><path d="M288 161 L298 168 L288 175" stroke="#E8590C"/>
<path d="M176 214 H476" stroke="#F03E82"/><path d="M468 207 L478 214 L468 221" stroke="#F03E82"/>
</g>
<text x="236" y="54" text-anchor="middle" font-size="14" font-weight="700" fill="#0B7F5C">if let s</text>
<text x="236" y="158" text-anchor="middle" font-size="14" font-weight="700" fill="#B84508">?? "{$guest}"</text>
<rect x="304" y="44" width="170" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="389" y="69" text-anchor="middle">s: String = "Ada"</text>
<rect x="304" y="148" width="170" height="40" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="389" y="173" text-anchor="middle">"{$guest}"</text>
<text x="330" y="207" text-anchor="middle" font-size="14" font-weight="700" fill="#C2185B">soprannome!</text>
<rect x="484" y="194" width="130" height="40" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="549" y="219" text-anchor="middle" font-weight="700" fill="#C2185B">crash</text>
</svg>
SVG;
    }
}

/** An Array by index, a Dictionary by key, a Set with no order and no duplicates. */
if (!function_exists('fig_swiftbase_collections')) {
    function fig_swiftbase_collections(string $dict, string $byIndex, string $byKey, string $noOrder): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="12" y="12" width="196" height="226" rx="14" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="110" y="40" text-anchor="middle" font-weight="700">Array</text>
<text x="110" y="60" text-anchor="middle" font-size="13" fill="#B84508">[Int]</text>
<g fill="#FFFFFF" stroke="#E8590C" stroke-width="2"><rect x="38" y="96" width="48" height="48" rx="8"/><rect x="86" y="96" width="48" height="48" rx="8"/><rect x="134" y="96" width="48" height="48" rx="8"/></g>
<g text-anchor="middle" font-weight="700"><text x="62" y="126">7</text><text x="110" y="126">8</text><text x="158" y="126">9</text></g>
<g text-anchor="middle" font-size="13" fill="#B84508"><text x="62" y="166">0</text><text x="110" y="166">1</text><text x="158" y="166">2</text></g>
<text x="110" y="216" text-anchor="middle" font-size="13">{$byIndex}</text>
<rect x="222" y="12" width="196" height="226" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="320" y="40" text-anchor="middle" font-weight="700">{$dict}</text>
<text x="320" y="60" text-anchor="middle" font-size="13" fill="#1B66BC">[String: Int]</text>
<g fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"><rect x="240" y="84" width="72" height="36" rx="8"/><rect x="240" y="132" width="72" height="36" rx="8"/><rect x="346" y="84" width="54" height="36" rx="8"/><rect x="346" y="132" width="54" height="36" rx="8"/></g>
<g stroke="#2F8CF0" stroke-width="2" fill="none"><path d="M316 102 H340"/><path d="M334 97 L342 102 L334 107"/><path d="M316 150 H340"/><path d="M334 145 L342 150 L334 155"/></g>
<g text-anchor="middle"><text x="276" y="107">"Ada"</text><text x="276" y="155">"Bea"</text><text x="373" y="107" font-weight="700">36</text><text x="373" y="155" font-weight="700">28</text></g>
<text x="320" y="216" text-anchor="middle" font-size="13">{$byKey}</text>
<rect x="432" y="12" width="196" height="226" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="530" y="40" text-anchor="middle" font-weight="700">Set</text>
<text x="530" y="60" text-anchor="middle" font-size="13" fill="#0B7F5C">Set&lt;Int&gt;</text>
<ellipse cx="530" cy="128" rx="78" ry="46" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/>
<g text-anchor="middle" font-weight="700"><text x="494" y="122">9</text><text x="536" y="150">7</text><text x="566" y="116">8</text></g>
<text x="530" y="216" text-anchor="middle" font-size="13">{$noOrder}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'swift-base', 'family' => 'swift', 'after' => null,
    'title' => L('Swift base', 'Swift Basics'), 'short' => L('Swift base', 'Swift Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di iPhone, iPad e Mac: costanti, optional, condizioni, collezioni, funzioni, stringhe e tuple.', 'The language of iPhone, iPad and Mac: constants, optionals, conditions, collections, functions, strings and tuples.'),
    'lessons' => [
        lesson('values', L('Costanti e variabili', 'Constants and variables'), [
            P('In Swift `let` crea una **costante** (non cambia), `var` una **variabile**. Il tipo si deduce dal valore, ma si può scrivere: `let nome: String = "Ada"`. I valori entrano nel testo con `\(valore)`.', 'In Swift `let` creates a **constant** (it does not change), `var` a **variable**. The type is inferred from the value, but you can write it: `let nome: String = "Ada"`. Values enter text with `\(value)`.'),
            C(L('let nome = "Ada"
var anni = 36
anni += 1
let altezza: Double = 1.68
print("\(nome) ha \(anni) anni")', 'let nome = "Ada"
var anni = 36
anni += 1
let altezza: Double = 1.68
print("\(nome) is \(anni) years old")'), 'swift', L('<pre>Ada ha 37 anni</pre>', '<pre>Ada is 37 years old</pre>')),
            P('Preferisci `let`: il compilatore ti avvisa se una `var` non viene mai cambiata.', 'Prefer `let`: the compiler warns you if a `var` is never changed.'),
            P('Perché insistere su `let`? Un valore che non cambia è un valore su cui puoi contare: leggendo il codice sai che `nome` è lo stesso dalla prima all’ultima riga. Anche il tipo, una volta dedotto, resta fisso: dopo `var anni = 36` scrivere `anni = "tanti"` è un errore di compilazione. Il tipo va scritto a mano quando la deduzione darebbe altro, come `Double` per `1.68` voluto esplicito, o quando dichiari senza valore iniziale.', 'Why insist on `let`? A value that does not change is a value you can rely on: reading the code you know `nome` is the same from the first line to the last. The type too, once inferred, stays fixed: after `var anni = 36`, writing `anni = "many"` is a compile error. You write the type by hand when inference would give something else, or when you declare without an initial value.'),
        ], [
            Q('v1', L('Quale parola crea una costante?', 'Which word creates a constant?'), ['let', 'var', 'const', 'final'], 0, L('`var` è per ciò che cambia.', '`var` is for what changes.')),
            Q('v2', L('Come inserisci `x` in una stringa?', 'How do you put `x` into a string?'), ['"\\(x)"', '"${x}"', '"{x}"', '"#x"'], 0, L('Barra rovesciata e parentesi.', 'Backslash and brackets.')),
            Q('v3', L('Che cosa succede se cambi una `let`?', 'What happens if you change a `let`?'), [L('Errore di compilazione', 'Compile error'), L('Cambia', 'It changes'), L('Viene ignorato', 'It is ignored'), L('Diventa var', 'It becomes var')], 0, L('Le costanti non cambiano.', 'Constants do not change.')),
        ], task('swift', L('Crea `let corso = "Swift"` e `var livello = 1`.', 'Create `let corso = "Swift"` and `var livello = 1`.'), '', [re('let\s+corso\s*(:\s*String\s*)?=\s*"Swift"', L('La costante esiste', 'The constant exists')), re('var\s+livello\s*(:\s*Int\s*)?=\s*1', L('La variabile esiste', 'The variable exists'))], '', 'let corso = "Swift"
var livello = 1'), [
            dMatch(pMatch(), [['let', L('Costante', 'Constant')], ['var', L('Variabile', 'Variable')], ['String', L('Testo', 'Text')], ['Int', L('Intero', 'Integer')], ['Double', L('Decimale', 'Decimal')]], L('Le basi di Swift.', 'Swift basics.')),
            dFlash(pFlash(), [[L('In Swift servono i punti e virgola', 'Swift needs semicolons'), false], [L('Il tipo si può dedurre', 'The type can be inferred'), true], [L('`let` non si può riassegnare', '`let` cannot be reassigned'), true], [L('`var` si può riassegnare', '`var` can be reassigned'), true]], L('I punti e virgola sono facoltativi.', 'Semicolons are optional.')),
        ]),
        lesson('optionals', L('Optional', 'Optionals'), [
            P('Un **optional** (`String?`) può contenere un valore oppure `nil`. Prima di usarlo va “scartato”: `if let` o `guard let` lo aprono in sicurezza, `??` dà un valore di riserva.', 'An **optional** (`String?`) can hold a value or `nil`. Before using it you “unwrap” it: `if let` or `guard let` open it safely, `??` gives a fallback.'),
            C(L('var soprannome: String? = nil

if let s = soprannome {
    print("Ciao \(s)")
} else {
    print("Nessun soprannome")
}

let mostrato = soprannome ?? "Ospite"

func saluta(_ nome: String?) {
    guard let nome else { return }
    print("Ciao \(nome)")
}', 'var soprannome: String? = nil

if let s = soprannome {
    print("Hi \(s)")
} else {
    print("No nickname")
}

let mostrato = soprannome ?? "Guest"

func saluta(_ nome: String?) {
    guard let nome else { return }
    print("Hi \(nome)")
}'), 'swift', L('<pre>Nessun soprannome</pre>', '<pre>No nickname</pre>')),
            P('Il punto esclamativo (`soprannome!`) forza l’apertura e fa crashare l’app se il valore è `nil`: usalo solo se sei certo.', 'The exclamation mark (`soprannome!`) forces unwrapping and crashes the app if the value is `nil`: use it only when certain.'),
            Fig(L(fig_swiftbase_optional('contiene un valore', 'vuoto', 'Ospite'), fig_swiftbase_optional('holds a value', 'empty', 'Guest')), L('Due scatole di tipo String?: una contiene "Ada", l’altra è vuota e vale nil. Dalla prima, if let s ricava s: String = "Ada". Dalla seconda, ?? dà il valore di riserva; forzarla con il punto esclamativo porta a un crash.', 'Two boxes of type String?: one holds "Ada", the other is empty and is nil. From the first, if let s gets s: String = "Ada". From the second, ?? gives the fallback value; forcing it with the exclamation mark leads to a crash.'), L('Un optional è una scatola: prima di usare il valore devi aprirla.', 'An optional is a box: you have to open it before using the value.')),
            P('Perché tanta cerimonia? In molti linguaggi qualunque variabile può essere vuota, e l’errore salta fuori solo quando l’app è già nelle mani di qualcuno. In Swift il `?` è scritto nel tipo, quindi il compilatore ti obbliga a decidere cosa fare quando il valore manca. Per scendere in più optional di fila c’è il **chaining**: `utente?.indirizzo?.citta` dà `nil` appena un pezzo manca, senza crash.', 'Why so much ceremony? In many languages any variable can be empty, and the error only shows up once the app is already in someone’s hands. In Swift the `?` is written in the type, so the compiler makes you decide what to do when the value is missing. To go down several optionals in a row there is **chaining**: `utente?.indirizzo?.citta` gives `nil` as soon as one piece is missing, with no crash.'),
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
            Fig('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 180" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="40" y="55" font-size="17" font-weight="700" fill="#B84508">1...3</text>
<path d="M280 50 H440" stroke="#E8590C" stroke-width="6" stroke-linecap="round"/>
<g fill="#E8590C"><circle cx="280" cy="50" r="10"/><circle cx="360" cy="50" r="10"/><circle cx="440" cy="50" r="10"/></g>
<text x="40" y="115" font-size="17" font-weight="700" fill="#1B66BC">1..&lt;3</text>
<path d="M280 110 H430" stroke="#2F8CF0" stroke-width="6" stroke-linecap="round"/>
<g fill="#2F8CF0"><circle cx="280" cy="110" r="10"/><circle cx="360" cy="110" r="10"/></g>
<circle cx="440" cy="110" r="9" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="3"/>
<path d="M200 150 H600" stroke="#8A87B0" stroke-width="2"/>
<g stroke="#8A87B0" stroke-width="2"><path d="M200 144 V156"/><path d="M280 144 V156"/><path d="M360 144 V156"/><path d="M440 144 V156"/><path d="M520 144 V156"/><path d="M600 144 V156"/></g>
<g text-anchor="middle" font-size="14"><text x="200" y="174">0</text><text x="280" y="174">1</text><text x="360" y="174">2</text><text x="440" y="174">3</text><text x="520" y="174">4</text><text x="600" y="174">5</text></g>
</svg>', L('Due righe sopra una linea dei numeri da 0 a 5. 1...3 copre 1, 2 e 3 con tre pallini pieni. 1..<3 copre 1 e 2, e al 3 ha un pallino vuoto: il 3 è escluso.', 'Two rows above a number line from 0 to 5. 1...3 covers 1, 2 and 3 with three filled dots. 1..<3 covers 1 and 2, and has a hollow dot at 3: 3 is left out.'), L('Tre punti includono l’ultimo numero, il minore lo esclude.', 'Three dots include the last number, the less-than sign leaves it out.')),
            P('`1..<n` è la forma che userai di più, perché gli indici partono da 0: `for i in 0..<voti.count` visita ogni posizione senza uscire dall’array. Lo `switch` di Swift è più ricco di quello di C: un caso può essere un intervallo, un elenco (`case "a", "e":`) o avere una condizione con `where`. Dentro un ciclo, `continue` salta al giro successivo e `break` esce del tutto.', '`1..<n` is the form you will use most, because indices start at 0: `for i in 0..<voti.count` visits every position without going past the end of the array. Swift’s `switch` is richer than C’s: a case can be a range, a list (`case "a", "e":`) or carry a condition with `where`. Inside a loop, `continue` jumps to the next round and `break` leaves it altogether.'),
        ], [
            Q('c1', L('Quali numeri dà `1..<4`?', 'Which numbers does `1..<4` give?'), ['1, 2, 3', '1, 2, 3, 4', '0, 1, 2, 3', '4'], 0, L('Intervallo semiaperto.', 'Half-open range.')),
            Q('c2', L('Lo `switch` di Swift deve…', 'Swift’s `switch` must…'), [L('Coprire tutti i casi', 'Cover every case'), L('Avere `break`', 'Have `break`'), L('Usare solo numeri', 'Use only numbers'), L('Stare in una funzione', 'Be in a function')], 0, L('Altrimenti serve `default`.', 'Otherwise you need `default`.')),
            Q('c3', L('Servono le parentesi dopo `if`?', 'Do you need brackets after `if`?'), [L('No, ma servono le graffe', 'No, but braces are required'), L('Sì, sempre', 'Yes, always'), L('Solo con `else`', 'Only with `else`'), L('Né parentesi né graffe', 'Neither brackets nor braces')], 0, L('Le graffe sono obbligatorie.', 'Braces are mandatory.')),
        ], task('swift', L('Con `for n in 1...5` stampa ogni `n`.', 'With `for n in 1...5` print each `n`.'), '', [re('for\s+n\s+in\s+1\s*\.\.\.\s*5\s*\{', L('Il ciclo è giusto', 'The loop is right')), re('print\s*\(\s*n\s*\)', L('Stampi n', 'You print n'))], '', 'for n in 1...5 {
    print(n)
}'), [
            dOrder(pSort(), ['var conto = 3', 'while conto > 0 {', '    print(conto)', '    conto -= 1', '}', 'print("Via!")'], L('Prepara, ripeti, avanza, finisci.', 'Set up, repeat, advance, finish.')),
            dFlash(pFlash(), [[L('`1...5` include il 5', '`1...5` includes 5'), true], [L('Nello `switch` un caso continua nel successivo', 'In `switch` a case falls into the next'), false], [L('`for-in` scorre anche gli array', '`for-in` also walks arrays'), true], [L('`1..<5` include il 5', '`1..<5` includes 5'), false]], L('Niente fall-through.', 'No fall-through.')),
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
            Fig(L(fig_swiftbase_collections('Dizionario', 'in ordine, per indice', 'per chiave', 'senza ordine né doppioni'), fig_swiftbase_collections('Dictionary', 'ordered, by index', 'by key', 'no order, no duplicates')), L('Tre riquadri. Array: le caselle 7, 8, 9 con gli indici 0, 1, 2. Dizionario: le chiavi "Ada" e "Bea" con frecce verso 36 e 28. Set: i numeri 9, 7, 8 sparsi in un ovale.', 'Three panels. Array: the cells 7, 8, 9 with indices 0, 1, 2. Dictionary: the keys "Ada" and "Bea" with arrows to 36 and 28. Set: the numbers 9, 7, 8 scattered in an oval.'), L('Scegli la collezione da come cercherai i dati.', 'Pick the collection by how you will look the data up.')),
            P('In Swift le collezioni sono **tipi per valore**: `var copia = voti` crea una copia indipendente, e cambiarla non tocca l’originale. Attenzione agli indici: `voti[5]` su un array di tre elementi non dà `nil` ma ferma l’app, quindi controlla prima con `isEmpty`, `count` o usa `first` e `last`, che restituiscono un optional.', 'In Swift collections are **value types**: `var copia = voti` makes an independent copy, and changing it does not touch the original. Mind the indices: `voti[5]` on a three-item array does not give `nil` but stops the app, so check first with `isEmpty` or `count`, or use `first` and `last`, which return an optional.'),
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
            P('Le etichette servono a far leggere la chiamata come una frase: `saluta("Ada", da: "Bea")` si capisce senza aprire la funzione. Dentro, il parametro si chiama `mittente`: l’etichetta è per chi chiama, il nome per chi scrive il corpo. Le closure sono ovunque nelle API di Swift: quando sono l’ultimo argomento si scrivono fuori dalle tonde, come in `voti.sorted { $0 > $1 }`.', 'Labels make the call read like a sentence: `saluta("Ada", da: "Bea")` is clear without opening the function. Inside, the parameter is called `mittente`: the label is for the caller, the name for whoever writes the body. Closures are everywhere in Swift’s APIs: when they are the last argument they go outside the brackets, as in `voti.sorted { $0 > $1 }`.'),
        ], [
            Q('f1', L('Che cosa fa `_` davanti a un parametro?', 'What does `_` in front of a parameter do?'), [L('Toglie l’etichetta nella chiamata', 'Removes the label at the call'), L('Lo rende privato', 'Makes it private'), L('Lo ignora', 'Ignores it'), L('Lo rende optional', 'Makes it optional')], 0, L('Si chiama `saluta("Ada")`.', 'You call `saluta("Ada")`.')),
            Q('f2', L('Dove si scrive il tipo di ritorno?', 'Where do you write the return type?'), [L('Dopo `->`', 'After `->`'), L('Prima di `func`', 'Before `func`'), L('Dopo il nome', 'After the name'), L('Non si scrive', 'It is not written')], 0, L('`func f() -> Int`.', '`func f() -> Int`.')),
            Q('f3', L('Che cos’è una closure?', 'What is a closure?'), [L('Una funzione senza nome', 'A nameless function'), L('Una classe chiusa', 'A closed class'), L('Un ciclo', 'A loop'), L('Un errore', 'An error')], 0, L('Si scrive tra graffe.', 'It is written in braces.')),
        ], task('swift', L('Scrivi `func doppio(_ n: Int) -> Int` che restituisce `n * 2`.', 'Write `func doppio(_ n: Int) -> Int` that returns `n * 2`.'), '', [re('func\s+doppio\s*\(\s*_\s+n\s*:\s*Int\s*\)\s*->\s*Int', L('La firma è giusta', 'The signature is right')), re('(return\s+)?n\s*\*\s*2', L('Restituisce il doppio', 'It returns the double'))], '', 'func doppio(_ n: Int) -> Int {
    n * 2
}'), [
            dOrder(pSort(), ['func media(_ voti: [Double]) -> Double {', '    guard !voti.isEmpty else { return 0 }', '    return voti.reduce(0, +) / Double(voti.count)', '}', 'print(media([6, 8]))'], L('Firma, controllo, calcolo, chiamata.', 'Signature, check, calculation, call.')),
            dFlash(pFlash(), [[L('Le etichette rendono chiare le chiamate', 'Labels make calls clear'), true], [L('Un parametro non può avere un valore predefinito', 'A parameter cannot have a default value'), false], [L('Con una sola espressione si può omettere `return`', 'With a single expression `return` can be omitted'), true], [L('`->` indica il tipo restituito', '`->` shows the return type'), true]], L('I valori predefiniti sono comuni.', 'Default values are common.')),
        ]),
        lesson('strings', L('Stringhe e caratteri', 'Strings and characters'), [
            P('Una `String` di Swift è una sequenza di `Character` Unicode: una lettera accentata conta come un carattere solo, anche se occupa più byte. Per questo non si indicizza con un numero (`s[0]` non compila): si usano `first`, `prefix`, `hasPrefix` o gli indici della stringa.', 'A Swift `String` is a sequence of Unicode `Character`s: an accented letter counts as one character, even if it takes several bytes. That is why you cannot index it with a number (`s[0]` does not compile): you use `first`, `prefix`, `hasPrefix` or the string’s own indices.'),
            C('let nome = "Niccolò"
print(nome.count)              // 7
print(nome.uppercased())       // NICCOLÒ
print(nome.hasPrefix("Nic"))   // true
print(nome.prefix(3))          // Nic

let frase = "uno,due,tre"
let parti = frase.split(separator: ",")
let unite = parti.joined(separator: " - ")

let testo = """
    Più righe
    in un colpo solo
    """
let n = Int("42") ?? 0', 'swift'),
            P('Le stringhe sono tipi per valore e si confrontano con `==`. `Int("42")` restituisce un optional, perché il testo potrebbe non essere un numero. Il testo su più righe va tra tre virgolette.', 'Strings are value types and are compared with `==`. `Int("42")` returns an optional, because the text might not be a number. Multi-line text goes between triple quotes.'),
        ], [
            Q('s1', L('Perché `nome[0]` non compila in Swift?', 'Why does `nome[0]` not compile in Swift?'), [L('Le stringhe sono sempre immutabili', 'Strings are always immutable'), L('Gli indici partono da 1', 'Indices start from 1'), L('I caratteri Unicode hanno lunghezze diverse, quindi non c’è un indice intero', 'Unicode characters have different lengths, so there is no integer index'), L('Serve prima `import Foundation`', 'You need `import Foundation` first')], 2, L('Si usano `first`, `prefix` o `String.Index`.', 'You use `first`, `prefix` or `String.Index`.')),
            Q('s2', L('Che cosa restituisce `Int("ciao")`?', 'What does `Int("ciao")` return?'), ['0', 'nil', '-1', L('Un errore di compilazione', 'A compile error')], 1, L('La conversione può fallire: il risultato è `Int?`.', 'The conversion may fail: the result is `Int?`.')),
            Q('s3', L('Quale metodo controlla l’inizio di una stringa?', 'Which method checks the start of a string?'), ['startsWith', 'beginsWith', 'first(of:)', 'hasPrefix'], 3, L('E `hasSuffix` controlla la fine.', 'And `hasSuffix` checks the end.')),
        ], task('swift', L('Crea `let titolo = "swift"` e stampa `titolo.uppercased()`.', 'Create `let titolo = "swift"` and print `titolo.uppercased()`.'), '', [re('let\s+titolo\s*(:\s*String\s*)?=\s*"swift"', L('La costante esiste', 'The constant exists')), re('print\s*\(\s*titolo\.uppercased\s*\(\s*\)\s*\)', L('Stampi in maiuscolo', 'You print it in capitals'))], '', 'let titolo = "swift"
print(titolo.uppercased())'), [
            dMatch(pMatch(), [['count', L('Quanti caratteri', 'How many characters')], ['uppercased()', L('Tutto maiuscolo', 'All capitals')], ['hasPrefix', L('Inizia con…', 'Starts with…')], ['split(separator:)', L('Divide in pezzi', 'Splits into pieces')], ['"""', L('Testo su più righe', 'Multi-line text')]], L('Gli attrezzi delle stringhe.', 'String tools.')),
            dFlash(pFlash(), [[L('`"è".count` vale 1', '`"è".count` is 1'), true], [L('Le stringhe Swift si indicizzano con `s[0]`', 'Swift strings are indexed with `s[0]`'), false], [L('`Int("7")` restituisce un optional', '`Int("7")` returns an optional'), true], [L('Due stringhe si confrontano con `==`', 'Two strings are compared with `==`'), true]], L('Niente indici interi sulle stringhe.', 'No integer indices on strings.')),
        ]),
        lesson('tuples-types', L('Tuple e conversioni', 'Tuples and conversions'), [
            P('Una **tupla** raggruppa pochi valori senza creare un tipo: `(nome: "Ada", anni: 36)`. È comoda per far restituire più risultati a una funzione. Swift non converte mai i numeri da solo: `Int` e `Double` vanno convertiti a mano.', 'A **tuple** groups a few values without creating a type: `(nome: "Ada", anni: 36)`. It is handy for returning several results from a function. Swift never converts numbers by itself: `Int` and `Double` must be converted by hand.'),
            C('func minMax(_ voti: [Int]) -> (min: Int, max: Int)? {
    guard let primo = voti.first else { return nil }
    var r = (min: primo, max: primo)
    for v in voti {
        r.min = min(r.min, v)
        r.max = max(r.max, v)
    }
    return r
}

if let r = minMax([6, 9, 7]) {
    print("Da \(r.min) a \(r.max)")
}

let (x, y) = (3, 4)
let pezzi = 7
let media = Double(pezzi) / 2      // 3.5
typealias Punto = (x: Double, y: Double)', 'swift'),
            P('`let (x, y) = …` **scompone** la tupla in due costanti. `typealias` dà un nome nuovo a un tipo esistente. Per dati con un significato preciso e dei metodi, meglio una `struct`.', '`let (x, y) = …` **destructures** the tuple into two constants. `typealias` gives a new name to an existing type. For data with a precise meaning and methods, a `struct` is better.'),
        ], [
            Q('t1', L('Quanto vale `7 / 2` se entrambi sono `Int`?', 'What is `7 / 2` if both are `Int`?'), ['3.5', '3', '4', L('Errore di compilazione', 'Compile error')], 1, L('La divisione tra interi tronca i decimali.', 'Integer division drops the decimals.')),
            Q('t2', L('Come sommi `let a: Int = 2` e `let b: Double = 1.5`?', 'How do you add `let a: Int = 2` and `let b: Double = 1.5`?'), ['a + b', '(a + b) as Double', 'a.plus(b)', 'Double(a) + b'], 3, L('Swift non mescola tipi numerici diversi senza conversione.', 'Swift does not mix numeric types without a conversion.')),
            Q('t3', L('Che cosa fa `let (q, r) = (17 / 5, 17 % 5)`?', 'What does `let (q, r) = (17 / 5, 17 % 5)` do?'), [L('Crea due costanti: q vale 3 e r vale 2', 'Creates two constants: q is 3 and r is 2'), L('Crea un array di due elementi', 'Creates an array of two items'), L('Dà errore: servono le graffe', 'Fails: braces are needed'), L('Crea una sola costante tupla chiamata q', 'Creates a single tuple constant called q')], 0, L('È la scomposizione di una tupla.', 'It is tuple destructuring.')),
        ], task('swift', L('Scrivi `func dividi(_ a: Int, _ b: Int) -> (quoziente: Int, resto: Int)` che restituisce `(a / b, a % b)`.', 'Write `func dividi(_ a: Int, _ b: Int) -> (quoziente: Int, resto: Int)` that returns `(a / b, a % b)`.'), '', [re('func\s+dividi\s*\(\s*_\s+a\s*:\s*Int\s*,\s*_\s+b\s*:\s*Int\s*\)\s*->\s*\(\s*quoziente\s*:\s*Int\s*,\s*resto\s*:\s*Int\s*\)', L('La firma è giusta', 'The signature is right')), re('\(\s*(quoziente\s*:\s*)?a\s*/\s*b\s*,\s*(resto\s*:\s*)?a\s*%\s*b\s*\)', L('Restituisci la tupla', 'You return the tuple'))], '', 'func dividi(_ a: Int, _ b: Int) -> (quoziente: Int, resto: Int) {
    (a / b, a % b)
}'), [
            dBug(pBug(), ['let pezzi = 3', 'let prezzo = 2.5', 'let totale = pezzi * prezzo', 'print(totale)'], 2, 'let totale = Double(pezzi) * prezzo', L('Int per Double non compila: converti prima.', 'Int times Double does not compile: convert first.')),
            dMatch(pMatch(), [['(a, b)', L('Tupla', 'Tuple')], ['let (x, y) = p', L('Scompone una tupla', 'Destructures a tuple')], ['Double(n)', L('Converte in decimale', 'Converts to decimal')], ['typealias', L('Nuovo nome per un tipo', 'New name for a type')]], L('Tuple e tipi.', 'Tuples and types.')),
        ]),
    ],
];
