<?php
/** Rust basics: variables and mutability, types, control flow, functions, ownership, tuples and arrays, String and &str, modules. */

/** A move: a and b on the stack, "ciao" on the heap. The pointer passes from a to b and a becomes invalid (the still frame shows the end). */
if (!function_exists('fig_rustbase_move')) {
    function fig_rustbase_move(string $moved): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.pa{opacity:0;animation:pa 7s infinite}.pb,.mv{animation:pb 7s infinite}@keyframes pa{0%,40%{opacity:1}55%,100%{opacity:0}}@keyframes pb{0%,40%{opacity:0}55%,100%{opacity:1}}@media (prefers-reduced-motion:reduce){.pa,.pb,.mv{animation:none}}</style>
<text x="40" y="32" font-weight="700" fill="#5F5C8A">stack</text>
<text x="400" y="92" font-weight="700" fill="#5F5C8A">heap</text>
<rect x="40" y="46" width="230" height="76" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="56" y="70" font-weight="700">a</text>
<g font-size="13" text-anchor="middle"><rect x="56" y="82" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="87" y="101">ptr</text><rect x="124" y="82" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="155" y="101">len 4</text><rect x="192" y="82" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="223" y="101">cap 4</text></g>
<rect x="40" y="150" width="230" height="76" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="56" y="174" font-weight="700">b</text>
<g font-size="13" text-anchor="middle"><rect x="56" y="186" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/><text x="87" y="205">ptr</text><rect x="124" y="186" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/><text x="155" y="205">len 4</text><rect x="192" y="186" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/><text x="223" y="205">cap 4</text></g>
<rect x="400" y="104" width="216" height="56" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<g fill="#FFFFFF" stroke="#E8590C" stroke-width="2"><rect x="412" y="114" width="44" height="36" rx="6"/><rect x="462" y="114" width="44" height="36" rx="6"/><rect x="512" y="114" width="44" height="36" rx="6"/><rect x="562" y="114" width="44" height="36" rx="6"/></g>
<g text-anchor="middle" font-weight="700"><text x="434" y="138">c</text><text x="484" y="138">i</text><text x="534" y="138">a</text><text x="584" y="138">o</text></g>
<g class="pa" stroke="#2F8CF0" stroke-width="2" fill="none"><path d="M87 96 C87 20 300 30 398 120"/><path d="M386 117 L400 122 L392 109"/></g>
<g class="pb" stroke="#0B7F5C" stroke-width="2" fill="none"><path d="M87 200 C87 240 320 230 400 152"/><path d="M388 154 L402 150 L397 164"/></g>
<g class="mv"><rect x="40" y="46" width="230" height="76" rx="12" fill="#FFFFFF" fill-opacity="0.82" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/><text x="155" y="90" text-anchor="middle" font-size="14" font-weight="700" fill="#C2185B">{$moved}</text></g>
</svg>
SVG;
    }
}

/** String against &str in memory: a String owns a growable buffer on the heap, a literal &str points into read-only data in the program. */
if (!function_exists('fig_rustbase_strings')) {
    function fig_rustbase_strings(string $heap, string $ro): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="20" y="40" width="230" height="76" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="36" y="64" font-weight="700">s: String</text>
<g font-size="13" text-anchor="middle"><rect x="36" y="76" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="67" y="95">ptr</text><rect x="104" y="76" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="135" y="95">len 6</text><rect x="172" y="76" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="203" y="95">cap 8</text></g>
<rect x="20" y="150" width="230" height="76" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="36" y="174" font-weight="700">nome: &amp;str</text>
<g font-size="13" text-anchor="middle"><rect x="36" y="186" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/><text x="67" y="205">ptr</text><rect x="104" y="186" width="62" height="28" rx="6" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/><text x="135" y="205">len 3</text></g>
<rect x="320" y="20" width="300" height="100" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="334" y="42" font-size="13" font-weight="700" fill="#B84508">{$heap}</text>
<g fill="#FFFFFF" stroke="#E8590C" stroke-width="2"><rect x="334" y="58" width="32" height="40" rx="6"/><rect x="368" y="58" width="32" height="40" rx="6"/><rect x="402" y="58" width="32" height="40" rx="6"/><rect x="436" y="58" width="32" height="40" rx="6"/><rect x="470" y="58" width="32" height="40" rx="6"/><rect x="504" y="58" width="32" height="40" rx="6"/></g>
<g fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"><rect x="538" y="58" width="32" height="40" rx="6"/><rect x="572" y="58" width="32" height="40" rx="6"/></g>
<g text-anchor="middle" font-weight="700"><text x="350" y="84">F</text><text x="384" y="84">u</text><text x="418" y="84">c</text><text x="452" y="84">i</text><text x="486" y="84">n</text><text x="520" y="84">a</text></g>
<rect x="320" y="146" width="300" height="84" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="334" y="168" font-size="13" font-weight="700" fill="#5F5C8A">{$ro}</text>
<g fill="#FFFFFF" stroke="#7048E8" stroke-width="2"><rect x="334" y="180" width="32" height="40" rx="6"/><rect x="368" y="180" width="32" height="40" rx="6"/><rect x="402" y="180" width="32" height="40" rx="6"/></g>
<g text-anchor="middle" font-weight="700"><text x="350" y="206">A</text><text x="384" y="206">d</text><text x="418" y="206">a</text></g>
<g stroke-width="2" fill="none"><path d="M67 104 C67 138 300 138 346 102" stroke="#2F8CF0"/><path d="M333 104 L348 100 L344 114" stroke="#2F8CF0"/><path d="M67 214 C67 240 260 240 330 206" stroke="#7048E8"/><path d="M318 210 L332 204 L328 218" stroke="#7048E8"/></g>
</svg>
SVG;
    }
}

/** The module tree of the example: the crate, main, the geometria module with two public items and a private one. */
if (!function_exists('fig_rustbase_modules')) {
    function fig_rustbase_modules(string $private): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M320 52 V70 H120 V86 M320 70 H430 V86"/><path d="M430 126 V146 H295 V170 M430 146 V170 M430 146 H570 V170"/></g>
<rect x="210" y="12" width="220" height="40" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="320" y="37" text-anchor="middle" font-weight="700">crate · main.rs</text>
<rect x="40" y="86" width="160" height="40" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="120" y="111" text-anchor="middle" font-weight="700">fn main</text>
<rect x="330" y="86" width="200" height="40" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="430" y="111" text-anchor="middle" font-weight="700">mod geometria</text>
<g font-size="14" text-anchor="middle">
<rect x="225" y="170" width="140" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="295" y="195"><tspan font-weight="700" fill="#0B7F5C">pub</tspan> struct Punto</text>
<rect x="372" y="170" width="116" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="430" y="195"><tspan font-weight="700" fill="#0B7F5C">pub</tspan> fn distanza</text>
<rect x="510" y="170" width="120" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/><text x="570" y="195" fill="#5F5C8A">fn segreto</text>
</g>
<text x="570" y="232" text-anchor="middle" font-size="13" fill="#5F5C8A">{$private}</text>
<g stroke="#2F8CF0" stroke-width="2" fill="none" stroke-dasharray="6 4"><path d="M120 126 V236 H430 V216"/></g>
<path d="M423 224 L430 212 L437 224" stroke="#2F8CF0" stroke-width="2" fill="none"/>
<text x="132" y="228" font-size="13" fill="#1B66BC">use geometria::distanza</text>
</svg>
SVG;
    }
}

return [
    'id' => 'rust-base', 'family' => 'rust', 'after' => null,
    'title' => L('Rust base', 'Rust Basics'), 'short' => L('Rust base', 'Rust Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Veloce e sicuro: variabili immutabili, tipi, controllo del flusso, funzioni, il famoso ownership, tuple e array, stringhe e moduli.', 'Fast and safe: immutable variables, types, control flow, functions, the famous ownership, tuples and arrays, strings and modules.'),
    'lessons' => [
        lesson('hello', L('Ciao, Cargo', 'Hello, Cargo'), [
            P('**Cargo** crea e compila i progetti Rust: `cargo new ciao` prepara la cartella, `cargo run` compila ed esegue. Il programma parte da `fn main()`; `println!` (con il punto esclamativo, è una **macro**) stampa.', '**Cargo** creates and builds Rust projects: `cargo new ciao` sets up the folder, `cargo run` builds and runs. The program starts from `fn main()`; `println!` (with the exclamation mark, it is a **macro**) prints.'),
            C(L('fn main() {
    let nome = "Ada";
    let anni = 36;
    println!("Ciao, {nome}! Hai {} anni.", anni);
}', 'fn main() {
    let nome = "Ada";
    let anni = 36;
    println!("Hi, {nome}! You are {}.", anni);
}'), 'rust', L('<pre>Ciao, Ada! Hai 36 anni.</pre>', '<pre>Hi, Ada! You are 36.</pre>')),
            P('Le graffe `{}` nel testo vengono riempite dagli argomenti, o direttamente dal nome di una variabile come `{nome}`.', 'Braces `{}` in the text are filled by the arguments, or directly by a variable name like `{nome}`.'),
            P('Il progetto creato da Cargo contiene `Cargo.toml`, con nome, versione e dipendenze, e `src/main.rs`, con il codice. Mentre scrivi, `cargo check` controlla gli errori senza produrre l’eseguibile ed è molto più rapido; `cargo build --release` crea la versione ottimizzata da distribuire. `println!` è una macro perché accetta un numero variabile di argomenti e controlla già in compilazione che le graffe corrispondano.', 'The project Cargo creates contains `Cargo.toml`, with the name, version and dependencies, and `src/main.rs`, with the code. While you write, `cargo check` looks for errors without producing the executable and is much faster; `cargo build --release` builds the optimised version to ship. `println!` is a macro because it takes a variable number of arguments and checks at compile time that the braces match.'),
        ], [
            Q('h1', L('Quale comando compila ed esegue?', 'Which command builds and runs?'), ['cargo run', 'rustc go', 'rust start', 'cargo exec'], 0, L('`cargo build` compila soltanto.', '`cargo build` only builds.')),
            Q('h2', L('Perché `println!` ha il punto esclamativo?', 'Why does `println!` have an exclamation mark?'), [L('È una macro', 'It is a macro'), L('È urgente', 'It is urgent'), L('Indica un errore', 'It marks an error'), L('È opzionale', 'It is optional')], 0, L('Le macro finiscono con `!`.', 'Macros end with `!`.')),
            Q('h3', L('Da quale funzione parte un programma?', 'Which function does a program start from?'), ['main', 'start', 'run', 'init'], 0, L('`fn main()`.', '`fn main()`.')),
        ], task('rust', L('Nel `main` stampa `"Ciao, Rust!"` con `println!`.', 'In `main` print `"Ciao, Rust!"` with `println!`.'), 'fn main() {
}', [re('println!\s*\(\s*"Ciao, Rust!"\s*\)\s*;', L('Stampi il saluto', 'You print the greeting'))], '', 'println!("Ciao, Rust!");'), [
            dOrder(pSort(), ['cargo new saluto', 'cd saluto', 'cargo run'], L('Crea, entra, esegui.', 'Create, enter, run.')),
            dBug(pBug(), ['fn main() {', '    let x = 5;', '    println("{}", x);', '}'], 2, '    println!("{}", x);', L('`println` è una macro: serve il punto esclamativo.', '`println` is a macro: it needs the exclamation mark.')),
        ]),
        lesson('variables', L('Variabili e tipi', 'Variables and types'), [
            P('In Rust le variabili sono **immutabili** per impostazione: per cambiarle serve `let mut`. I tipi si deducono o si scrivono: `i32` (intero), `f64` (decimale), `bool`, `char`, `&str` e `String` per il testo.', 'In Rust variables are **immutable** by default: to change one you need `let mut`. Types are inferred or written: `i32` (integer), `f64` (decimal), `bool`, `char`, `&str` and `String` for text.'),
            C('let x = 5;            // immutabile
let mut conta = 0;    // modificabile
conta += 1;

let prezzo: f64 = 9.99;
let attivo: bool = true;
let saluto: &str = "ciao";
let mut nome = String::from("Ada");
nome.push_str(" Lovelace");

let x = x * 2;        // shadowing: una nuova x', 'rust'),
            P('Lo **shadowing** dichiara una nuova variabile con lo stesso nome, anche di tipo diverso.', '**Shadowing** declares a new variable with the same name, even of a different type.'),
            P('Perché immutabili per impostazione? Perché un valore che non cambia non può essere cambiato per sbaglio da un’altra parte del codice, e il compilatore può ottimizzarlo meglio: `mut` diventa un segnale esplicito di «qui le cose cambiano». Gli interi hanno una dimensione nel nome: `i8`…`i64` con segno, `u8`…`u64` senza, `usize` per indici e lunghezze. Se una somma supera il limite del tipo, in modalità debug il programma si ferma invece di dare un numero sbagliato.', 'Why immutable by default? Because a value that does not change cannot be changed by mistake somewhere else in the code, and the compiler can optimise it better: `mut` becomes an explicit signal of “things change here”. Integers carry their size in the name: `i8`…`i64` signed, `u8`…`u64` unsigned, `usize` for indices and lengths. If a sum goes past the type’s limit, in debug mode the program stops instead of giving a wrong number.'),
        ], [
            Q('v1', L('Come rendi modificabile una variabile?', 'How do you make a variable changeable?'), ['let mut', 'var', 'let change', 'mut let'], 0, L('Di base è immutabile.', 'By default it is immutable.')),
            Q('v2', L('Quale tipo per un decimale?', 'Which type for a decimal?'), ['f64', 'i32', 'u8', 'dec'], 0, L('`f32` o `f64`.', '`f32` or `f64`.')),
            Q('v3', L('Che cos’è lo shadowing?', 'What is shadowing?'), [L('Una nuova variabile con lo stesso nome', 'A new variable with the same name'), L('Un errore', 'An error'), L('Una variabile nascosta al compilatore', 'A variable hidden from the compiler'), L('Un tipo', 'A type')], 0, L('Con un nuovo `let`.', 'With a new `let`.')),
        ], task('rust', L('Dichiara `let mut punti = 0;` e poi `punti += 10;`.', 'Declare `let mut punti = 0;` then `punti += 10;`.'), 'fn main() {
}', [re('let\s+mut\s+punti\s*(:\s*\w+\s*)?=\s*0\s*;', L('La variabile è modificabile', 'The variable is mutable')), re('punti\s*\+=\s*10\s*;', L('La aumenti', 'You increase it'))], '', 'let mut punti = 0;
punti += 10;'), [
            dMatch(pMatch(), [['i32', L('Intero con segno', 'Signed integer')], ['u8', L('Intero da 0 a 255', 'Integer from 0 to 255')], ['f64', L('Decimale', 'Decimal')], ['bool', L('Vero o falso', 'True or false')], ['String', L('Testo modificabile', 'Growable text')]], L('I tipi di Rust.', 'Rust types.')),
            dBug(pBug(), ['fn main() {', '    let totale = 0;', '    totale += 5;', '}'], 1, '    let mut totale = 0;', L('Per cambiarla serve `mut`.', 'To change it you need `mut`.')),
        ]),
        lesson('control', L('Controllo del flusso', 'Control flow'), [
            P('`if` è un’espressione e restituisce un valore. `loop` ripete per sempre (si esce con `break`), `while` finché la condizione è vera, `for` scorre intervalli e collezioni.', '`if` is an expression and returns a value. `loop` repeats forever (exit with `break`), `while` while the condition holds, `for` walks ranges and collections.'),
            C(L('let voto = 8;
let esito = if voto >= 6 { "promosso" } else { "bocciato" };

for i in 1..=3 {
    println!("Giro {i}");
}

let mut n = 0;
let risultato = loop {
    n += 1;
    if n == 5 { break n * 10; }
};', 'let voto = 8;
let esito = if voto >= 6 { "passed" } else { "failed" };

for i in 1..=3 {
    println!("Round {i}");
}

let mut n = 0;
let risultato = loop {
    n += 1;
    if n == 5 { break n * 10; }
};'), 'rust', L('<pre>Giro 1
Giro 2
Giro 3</pre>', '<pre>Round 1
Round 2
Round 3</pre>')),
            P('`1..=3` include il 3, `1..3` no. `break` in un `loop` può restituire un valore.', '`1..=3` includes 3, `1..3` does not. `break` in a `loop` can return a value.'),
            P('Siccome `if` restituisce un valore, i due rami devono avere lo **stesso tipo**: `if voto >= 6 { "promosso" } else { 0 }` non compila. Le condizioni vanno senza parentesi ma devono essere un vero `bool`: `if n { … }` con un numero è un errore, non c’è la conversione automatica di C. Per scorrere una collezione usa `for`: è più sicuro di un `while` con un indice, perché non può uscire dai limiti.', 'Since `if` returns a value, both branches must have the **same type**: `if voto >= 6 { "passed" } else { 0 }` does not compile. Conditions take no brackets but must be a real `bool`: `if n { … }` with a number is an error, there is no automatic conversion as in C. To walk a collection use `for`: it is safer than a `while` with an index, because it cannot go out of bounds.'),
        ], [
            Q('c1', L('`1..4` include il 4?', 'Does `1..4` include 4?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo nei for', 'Only in for loops'), L('Dipende', 'It depends')], 0, L('Per includerlo `1..=4`.', 'To include it `1..=4`.')),
            Q('c2', L('Come si esce da `loop`?', 'How do you exit a `loop`?'), ['break', 'exit', 'stop', 'return only'], 0, L('Anche con un valore.', 'Even with a value.')),
            Q('c3', L('Servono le parentesi intorno alla condizione di `if`?', 'Do you need brackets around an `if` condition?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo con `else`', 'Only with `else`'), L('Solo con numeri', 'Only with numbers')], 0, L('Ma le graffe sì.', 'But braces yes.')),
        ], task('rust', L('Con `for i in 1..=5` stampa ogni `i`.', 'With `for i in 1..=5` print each `i`.'), 'fn main() {
}', [re('for\s+i\s+in\s+1\s*\.\.=\s*5\s*\{', L('Il ciclo è giusto', 'The loop is right')), re('println!\s*\(', L('Stampi', 'You print'))], '', 'for i in 1..=5 {
    println!("{i}");
}'), [
            dBlanks(pFill(), "let taglia = ___ altezza < 170 { \"M\" } ___ { \"L\" };\n___ i in 0..3 { println!(\"{i}\"); }", ['if', 'else', 'for'], ['when', 'otherwise', 'loop'], L('Espressione condizionale e ciclo.', 'Conditional expression and loop.')),
            dFlash(pFlash(), [[L('`if` può restituire un valore', '`if` can return a value'), true], [L('`loop` si ferma da solo dopo 100 giri', '`loop` stops by itself after 100 rounds'), false], [L('`break` può restituire un valore da `loop`', '`break` can return a value from `loop`'), true], [L('Le condizioni di `if` vogliono le parentesi tonde', '`if` conditions need round brackets'), false]], L('`loop` va fermato con `break`.', '`loop` must be stopped with `break`.')),
        ]),
        lesson('functions', L('Funzioni', 'Functions'), [
            P('`fn` crea una funzione; parametri e risultato hanno sempre un tipo (`-> i32`). L’ultima espressione **senza punto e virgola** è il valore restituito.', '`fn` creates a function; parameters and result always have a type (`-> i32`). The last expression **without a semicolon** is the returned value.'),
            C('fn area(base: f64, altezza: f64) -> f64 {
    base * altezza
}

fn segno(n: i32) -> &\'static str {
    if n < 0 { return "negativo"; }
    "positivo o zero"
}

fn main() {
    println!("{}", area(3.0, 2.0));
}', 'rust', '<pre>6</pre>'),
            P('Se aggiungi il punto e virgola all’ultima riga diventa un’istruzione e la funzione non restituisce più nulla: errore.', 'If you add a semicolon to the last line it becomes a statement and the function returns nothing: error.'),
            P('Dietro c’è un’idea semplice: in Rust quasi tutto è un’**espressione**, cioè produce un valore. Un blocco `{ … }` vale quanto la sua ultima espressione, e il punto e virgola la trasforma in un’istruzione che vale `()`. `return` serve solo per uscire in anticipo, come nel caso del numero negativo. I tipi dei parametri vanno sempre scritti: Rust non li deduce, così la firma basta a capire come usare la funzione.', 'Behind this is a simple idea: in Rust almost everything is an **expression**, that is it produces a value. A block `{ … }` is worth its last expression, and the semicolon turns it into a statement worth `()`. `return` is only needed to leave early, as in the negative-number case. Parameter types must always be written: Rust does not infer them, so the signature alone tells you how to use the function.'),
        ], [
            Q('f1', L('Come si restituisce un valore senza `return`?', 'How do you return a value without `return`?'), [L('Ultima espressione senza `;`', 'Last expression without `;`'), L('Con `yield`', 'With `yield`'), L('Con `out`', 'With `out`'), L('Non si può', 'You cannot')], 0, L('È lo stile tipico di Rust.', 'It is typical Rust style.')),
            Q('f2', L('Dove si scrive il tipo di ritorno?', 'Where do you write the return type?'), [L('Dopo `->`', 'After `->`'), L('Prima di `fn`', 'Before `fn`'), L('Dopo `:`', 'After `:`'), L('Non si scrive', 'It is not written')], 0, L('`fn f() -> i32`.', '`fn f() -> i32`.')),
            Q('f3', L('I parametri devono avere un tipo?', 'Must parameters have a type?'), [L('Sì, sempre', 'Yes, always'), L('No', 'No'), L('Solo i numeri', 'Only numbers'), L('Solo in main', 'Only in main')], 0, L('Il tipo non si deduce nella firma.', 'Types are not inferred in signatures.')),
        ], task('rust', L('Scrivi `fn doppio(n: i32) -> i32` che restituisce `n * 2`.', 'Write `fn doppio(n: i32) -> i32` that returns `n * 2`.'), '', [re('fn\s+doppio\s*\(\s*n\s*:\s*i32\s*\)\s*->\s*i32', L('La firma è giusta', 'The signature is right')), re('n\s*\*\s*2', L('Restituisci il doppio', 'You return the double'))], '', 'fn doppio(n: i32) -> i32 {
    n * 2
}'), [
            dBug(pBug(), ['fn quadrato(x: i32) -> i32 {', '    x * x;', '}'], 1, '    x * x', L('Con il `;` l’espressione non è più il risultato.', 'With `;` the expression is no longer the result.')),
            dOrder(pSort(), ['fn media(a: f64, b: f64) -> f64 {', '    (a + b) / 2.0', '}', 'fn main() {', '    println!("{}", media(6.0, 8.0));', '}'], L('Definisci, poi usa nel main.', 'Define, then use in main.')),
        ]),
        lesson('ownership', L('Ownership e prestiti', 'Ownership and borrowing'), [
            P('Ogni valore in Rust ha un solo **proprietario**. Assegnarlo a un’altra variabile lo **sposta**: la prima non si può più usare. Per usarlo senza prenderlo si **presta** con un riferimento: `&valore` (lettura) o `&mut valore` (modifica).', 'Every value in Rust has a single **owner**. Assigning it to another variable **moves** it: the first can no longer be used. To use it without taking it you **borrow** with a reference: `&value` (read) or `&mut value` (write).'),
            C('let a = String::from("ciao");
let b = a;              // a è stato spostato in b
// println!("{a}");     // errore: a non è più valido

fn lunghezza(s: &String) -> usize { s.len() }
fn urla(s: &mut String) { s.push_str("!"); }

let mut testo = String::from("Fucina");
let n = lunghezza(&testo);
urla(&mut testo);', 'rust'),
            P('Regola d’oro: tanti riferimenti in lettura **oppure** uno solo in scrittura, mai insieme. Così Rust evita interi tipi di bug già in compilazione, senza garbage collector.', 'Golden rule: many read references **or** a single write reference, never both. This is how Rust prevents whole kinds of bugs at compile time, with no garbage collector.'),
            Fig(L(fig_rustbase_move('spostato: a non è più valido'), fig_rustbase_move('moved: a is no longer valid')), L('Sullo stack ci sono a e b, ognuno con ptr, len 4 e cap 4; sullo heap i quattro byte di «ciao». All’inizio la freccia parte dal ptr di a; dopo let b = a parte dal ptr di b, e a appare sbiadito con la scritta spostato.', 'On the stack are a and b, each with ptr, len 4 and cap 4; on the heap the four bytes of “ciao”. At first the arrow starts from a’s ptr; after let b = a it starts from b’s ptr, and a is faded with the word moved.'), L('Lo spostamento copia solo i tre campi sullo stack: il testo sullo heap resta dov’è, con un solo proprietario.', 'The move copies only the three fields on the stack: the text on the heap stays where it is, with a single owner.')),
            P('Perché un solo proprietario? Perché quando il proprietario esce dal suo blocco Rust libera la memoria in automatico (il **drop**): se `a` e `b` puntassero entrambe al testo, verrebbe liberato due volte. I tipi piccoli che stanno tutti sullo stack, come `i32`, `bool` e `char`, sono `Copy`: assegnarli li copia e l’originale resta valido. Quando ti serve davvero un secondo `String`, chiedilo esplicitamente con `a.clone()`.', 'Why a single owner? Because when the owner leaves its block Rust frees the memory automatically (the **drop**): if `a` and `b` both pointed at the text, it would be freed twice. Small types that live entirely on the stack, such as `i32`, `bool` and `char`, are `Copy`: assigning them copies them and the original stays valid. When you really need a second `String`, ask for it explicitly with `a.clone()`.'),
        ], [
            Q('o1', L('Che cosa succede con `let b = a;` se `a` è una String?', 'What happens with `let b = a;` if `a` is a String?'), [L('a viene spostato in b', 'a is moved into b'), L('Si copia', 'It is copied'), L('Errore', 'Error'), L('Niente', 'Nothing')], 0, L('`a` non si usa più.', '`a` can no longer be used.')),
            Q('o2', L('Come presti un valore in sola lettura?', 'How do you lend a value read-only?'), ['&valore', '*valore', '&mut valore', 'valore.clone()'], 0, L('`&mut` per modificarlo.', '`&mut` to modify it.')),
            Q('o3', L('Quanti riferimenti `&mut` insieme?', 'How many `&mut` references at once?'), [L('Uno solo', 'Just one'), L('Quanti vuoi', 'As many as you like'), L('Due', 'Two'), L('Nessuno', 'None')], 0, L('E nessun `&` nel frattempo.', 'And no `&` meanwhile.')),
        ], task('rust', L('Scrivi `fn conta(s: &str) -> usize` che restituisce `s.len()`.', 'Write `fn conta(s: &str) -> usize` that returns `s.len()`.'), '', [re('fn\s+conta\s*\(\s*s\s*:\s*&str\s*\)\s*->\s*usize', L('Prende in prestito il testo', 'It borrows the text')), re('s\.len\s*\(\s*\)', L('Restituisce la lunghezza', 'It returns the length'))], '', 'fn conta(s: &str) -> usize {
    s.len()
}'), [
            dBug(pBug(), ['let nome = String::from("Ada");', 'let altro = nome;', 'println!("{nome}");'], 1, 'let altro = nome.clone();', L('Dopo lo spostamento `nome` non vale più: usa `clone` o un riferimento.', 'After the move `nome` is no longer valid: use `clone` or a reference.')),
            dMatch(pMatch(), [[L('Proprietario', 'Owner'), L('Chi possiede il valore', 'Who owns the value')], [L('Spostamento', 'Move'), L('Il valore cambia proprietario', 'The value changes owner')], ['&', L('Prestito in lettura', 'Read borrow')], ['&mut', L('Prestito in scrittura', 'Write borrow')], ['clone()', L('Copia completa', 'Full copy')]], L('Le regole della memoria in Rust.', 'Rust’s memory rules.')),
        ]),
        lesson('compound', L('Tuple e array', 'Tuples and arrays'), [
            P('Una **tupla** raggruppa valori anche di tipi diversi: `(&str, u32)`; si leggono con `.0`, `.1` o scomponendola. Un **array** ha elementi dello stesso tipo e lunghezza fissa, scritta nel tipo: `[i32; 5]`.', 'A **tuple** groups values, even of different types: `(&str, u32)`; you read them with `.0`, `.1` or by destructuring. An **array** has items of the same type and a fixed length, written in the type: `[i32; 5]`.'),
            C('let persona: (&str, u32) = ("Ada", 36);
println!("{} ha {} anni", persona.0, persona.1);
let (nome, anni) = persona;

let voti = [7, 8, 9, 6, 10];
let zeri = [0; 3];            // [0, 0, 0]
println!("primo {}, quanti {}", voti[0], voti.len());

for v in voti {
    println!("{v}");
}

let centro = &voti[1..4];     // slice: [8, 9, 6]', 'rust'),
            P('Un indice fuori dai limiti non è un comportamento indefinito come in C: se il compilatore non lo scopre prima, il programma si ferma con un **panic**. Una **slice** `&voti[1..4]` è una vista su una parte dell’array, senza copiarla. La tupla vuota `()` si chiama *unit*: è ciò che restituisce una funzione senza risultato.', 'An out-of-bounds index is not undefined behaviour as in C: if the compiler does not catch it first, the program stops with a **panic**. A **slice** `&voti[1..4]` is a view on part of the array, without copying it. The empty tuple `()` is called *unit*: it is what a function with no result returns.'),
            Fig('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 170" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="60" y="101" font-weight="700">voti</text>
<g fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"><rect x="145" y="70" width="70" height="50" rx="8"/><rect x="425" y="70" width="70" height="50" rx="8"/></g>
<g fill="#FFF3EA" stroke="#E8590C" stroke-width="2"><rect x="215" y="70" width="70" height="50" rx="8"/><rect x="285" y="70" width="70" height="50" rx="8"/><rect x="355" y="70" width="70" height="50" rx="8"/></g>
<g text-anchor="middle" font-weight="700" font-size="17"><text x="180" y="101">7</text><text x="250" y="101">8</text><text x="320" y="101">9</text><text x="390" y="101">6</text><text x="460" y="101">10</text></g>
<g text-anchor="middle" font-size="14" fill="#5F5C8A"><text x="180" y="146">0</text><text x="250" y="146">1</text><text x="320" y="146">2</text><text x="390" y="146">3</text><text x="460" y="146">4</text></g>
<path d="M219 60 V50 H421 V60" fill="none" stroke="#E8590C" stroke-width="2"/>
<text x="320" y="38" text-anchor="middle" font-weight="700" fill="#B84508">&amp;voti[1..4]</text>
</svg>', L('L’array voti con le caselle 7, 8, 9, 6, 10 e gli indici da 0 a 4. Una parentesi sopra le caselle di indice 1, 2 e 3 è etichettata &voti[1..4].', 'The array voti with the cells 7, 8, 9, 6, 10 and indices 0 to 4. A bracket over the cells at indices 1, 2 and 3 is labelled &voti[1..4].'), L('La slice parte dall’indice 1 e si ferma prima del 4, senza copiare nulla.', 'The slice starts at index 1 and stops before 4, without copying anything.')),
        ], [
            Q('c1', L('Come leggi il secondo valore della tupla `t`?', 'How do you read the second value of the tuple `t`?'), ['t[1]', 't.1', 't.second', 't(1)'], 1, L('I campi di una tupla si contano da 0.', 'Tuple fields are counted from 0.')),
            Q('c2', L('Che cosa crea `[0; 4]`?', 'What does `[0; 4]` create?'), [L('Un array di quattro zeri', 'An array of four zeros'), L('Un array con 0 e 4', 'An array holding 0 and 4'), L('Un intervallo da 0 a 4', 'A range from 0 to 4'), L('Una tupla di due numeri', 'A tuple of two numbers')], 0, L('Valore e quantità, separati da `;`.', 'Value and count, separated by `;`.')),
            Q('c3', L('Che cosa succede se mentre il programma gira leggi un indice fuori dai limiti di un array?', 'What happens if, while the program runs, you read an index outside an array’s bounds?'), [L('Si legge memoria a caso', 'Random memory is read'), L('Restituisce 0', 'It returns 0'), L('Restituisce None', 'It returns None'), L('Il programma va in panic e si ferma', 'The program panics and stops')], 3, L('Per un accesso senza panic c’è `get(i)`, che dà un `Option`.', 'For access without panics there is `get(i)`, which gives an `Option`.')),
        ], task('rust', L('Crea l’array `let giorni = ["lun", "mar", "mer"];` e stampa la sua lunghezza con `giorni.len()`.', 'Create the array `let giorni = ["lun", "mar", "mer"];` and print its length with `giorni.len()`.'), 'fn main() {
}', [re('let\s+giorni\s*(:[^=]+)?=\s*\[\s*"lun"\s*,\s*"mar"\s*,\s*"mer"\s*\]\s*;', L('L’array esiste', 'The array exists')), re('println!\s*\([^;]*giorni\.len\s*\(\s*\)', L('Stampi la lunghezza', 'You print the length'))], '', 'let giorni = ["lun", "mar", "mer"];
println!("{}", giorni.len());'), [
            dMatch(pMatch(), [['(i32, f64)', L('Tupla', 'Tuple')], ['[i32; 5]', L('Array di 5 interi', 'Array of 5 integers')], ['t.0', L('Primo campo di una tupla', 'First field of a tuple')], ['&a[1..3]', L('Slice di un array', 'Slice of an array')], ['()', L('Unit, nessun valore', 'Unit, no value')]], L('I tipi composti.', 'Compound types.')),
            dBug(pBug(), ['let punto = (3, 4);', 'let x = punto[0];', 'println!("{x}");'], 1, 'let x = punto.0;', L('Le tuple non si indicizzano con le quadre: si usa `.0`.', 'Tuples are not indexed with brackets: you use `.0`.')),
        ]),
        lesson('strings', L('String e &str', 'String and &str'), [
            P('Rust ha due tipi di testo. `String` possiede il suo contenuto, sta nello heap e può crescere. `&str` è una **slice di stringa**: un riferimento a testo che sta altrove, come i letterali `"ciao"`. Le funzioni che leggono soltanto accettano `&str`, così vanno bene entrambi.', 'Rust has two text types. `String` owns its content, lives on the heap and can grow. `&str` is a **string slice**: a reference to text stored elsewhere, like the literal `"ciao"`. Functions that only read take `&str`, so both work.'),
            C('fn saluta(nome: &str) -> String {
    format!("Ciao, {nome}!")
}

let mut s = String::from("Fucina");
s.push_str(" Rust");
s.push(\'!\');

println!("{}", saluta(&s));       // &String diventa &str da solo
println!("{}", saluta("Ada"));

let parole: Vec<&str> = "uno due tre".split_whitespace().collect();
let maiuscolo = s.to_uppercase();
for c in "però".chars() {
    print!("{c} ");
}
println!("{}", "però".len());     // 5: byte, non caratteri', 'rust'),
            P('Le stringhe sono in UTF-8: `len()` conta i **byte**, e una lettera accentata ne occupa due. Per questo non si può scrivere `s[0]`; si scorre con `.chars()`. `format!` crea una nuova `String` come farebbe `println!`, ma senza stampare.', 'Strings are UTF-8: `len()` counts **bytes**, and an accented letter takes two. That is why you cannot write `s[0]`; you walk it with `.chars()`. `format!` builds a new `String` the way `println!` would, but without printing.'),
            Fig(L(fig_rustbase_strings('heap: può crescere fino a cap', 'nel programma, sola lettura'), fig_rustbase_strings('heap: can grow up to cap', 'in the program, read-only')), L('Sullo stack, s di tipo String ha ptr, len 6 e cap 8, e punta sullo heap ai byte di «Fucina» seguiti da due posti liberi. nome di tipo &str ha solo ptr e len 3, e punta ai byte di «Ada» nella memoria di sola lettura del programma.', 'On the stack, s of type String has ptr, len 6 and cap 8, and points on the heap to the bytes of “Fucina” followed by two free slots. nome of type &str has only ptr and len 3, and points to the bytes of “Ada” in the program’s read-only memory.'), L('Una String possiede e può crescere; una &str guarda soltanto.', 'A String owns and can grow; a &str only looks.')),
        ], [
            Q('s1', L('Quale tipo usi per il parametro di una funzione che legge soltanto un testo?', 'Which type do you use for the parameter of a function that only reads text?'), ['String', '&str', 'char', 'Vec<u8>'], 1, L('Accetta sia letterali sia `&String`.', 'It accepts both literals and `&String`.')),
            Q('s2', L('Quanto vale `"è".len()`?', 'What is `"è".len()`?'), ['1', '0', L('Errore di compilazione', 'Compile error'), '2'], 3, L('In UTF-8 la «è» occupa due byte.', 'In UTF-8 “è” takes two bytes.')),
            Q('s3', L('Che cosa fa `format!`?', 'What does `format!` do?'), [L('Crea una String con i valori inseriti, senza stampare', 'Builds a String with the values filled in, without printing'), L('Stampa sul terminale', 'Prints to the terminal'), L('Formatta il codice sorgente', 'Formats the source code'), L('Converte una String in numero', 'Converts a String to a number')], 0, L('Usa le stesse graffe di `println!`.', 'It uses the same braces as `println!`.')),
        ], task('rust', L('Scrivi `fn urla(s: &str) -> String` che restituisce `s.to_uppercase()`.', 'Write `fn urla(s: &str) -> String` that returns `s.to_uppercase()`.'), '', [re('fn\s+urla\s*\(\s*s\s*:\s*&str\s*\)\s*->\s*String', L('La firma è giusta', 'The signature is right')), re('s\.to_uppercase\s*\(\s*\)', L('Restituisci il maiuscolo', 'You return the capitals'))], '', 'fn urla(s: &str) -> String {
    s.to_uppercase()
}'), [
            dBlanks(pFill(), "let mut nome = String::___(\"Ada\");\nnome.___(\" Lovelace\");\nlet saluto = ___!(\"Ciao {nome}\");", ['from', 'push_str', 'format'], ['new', 'append', 'print'], L('Crea, allunga, componi.', 'Create, extend, compose.')),
            dFlash(pFlash(), [[L('Il letterale "ciao" è di tipo &str', 'The literal "ciao" has type &str'), true], [L('`len()` conta i caratteri', '`len()` counts characters'), false], [L('Una &String si può passare dove serve &str', 'A &String can be passed where &str is needed'), true], [L('`.chars()` scorre i caratteri Unicode', '`.chars()` walks the Unicode characters'), true]], L('`len()` conta i byte.', '`len()` counts bytes.')),
        ]),
        lesson('modules', L('Moduli e crate', 'Modules and crates'), [
            P('Un progetto Rust si divide in **moduli** con `mod`. Tutto è privato per impostazione: per usarlo fuori dal modulo serve `pub`. `use` accorcia i percorsi. Un pacchetto compilato si chiama **crate**: binaria (con `main.rs`) o libreria (con `lib.rs`).', 'A Rust project is split into **modules** with `mod`. Everything is private by default: to use it outside the module you need `pub`. `use` shortens paths. A compiled package is called a **crate**: binary (with `main.rs`) or library (with `lib.rs`).'),
            C('// src/main.rs
mod geometria {
    pub struct Punto { pub x: f64, pub y: f64 }

    pub fn distanza(a: &Punto, b: &Punto) -> f64 {
        ((a.x - b.x).powi(2) + (a.y - b.y).powi(2)).sqrt()
    }

    fn segreto() {}            // privato: visibile solo qui dentro
}

use geometria::{distanza, Punto};

fn main() {
    let a = Punto { x: 0.0, y: 0.0 };
    let b = Punto { x: 3.0, y: 4.0 };
    println!("{}", distanza(&a, &b));   // 5
}', 'rust', '<pre>5</pre>'),
            P('Un modulo può stare in un file a parte: `mod geometria;` in `main.rs` carica `src/geometria.rs`. Le crate esterne si aggiungono al `Cargo.toml` (con `cargo add`) e si usano con `use nome_crate::…`.', 'A module can live in its own file: `mod geometria;` in `main.rs` loads `src/geometria.rs`. External crates are added to `Cargo.toml` (with `cargo add`) and used with `use crate_name::…`.'),
            Fig(L(fig_rustbase_modules('privata'), fig_rustbase_modules('private')), L('Un albero: in cima la crate di main.rs, sotto fn main e mod geometria. Dentro geometria ci sono pub struct Punto e pub fn distanza, in verde, e fn segreto, tratteggiata e privata. Una freccia tratteggiata da main arriva a distanza con use geometria::distanza.', 'A tree: at the top the crate from main.rs, below it fn main and mod geometria. Inside geometria are pub struct Punto and pub fn distanza, in green, and fn segreto, dashed and private. A dashed arrow from main reaches distanza with use geometria::distanza.'), L('Da fuori si arriva solo a ciò che è segnato `pub`.', 'From outside you can reach only what is marked `pub`.')),
        ], [
            Q('m1', L('Che cosa serve per usare una funzione fuori dal suo modulo?', 'What do you need to use a function outside its module?'), [L('Niente: è pubblica di default', 'Nothing: it is public by default'), L('Scriverne il nome in maiuscolo', 'Writing its name in capitals'), L('La parola `pub`', 'The `pub` keyword'), L('La parola `export`', 'The `export` keyword')], 2, L('In Rust tutto nasce privato.', 'In Rust everything starts private.')),
            Q('m2', L('Dove cerca Rust il modulo dichiarato con `mod rete;` in main.rs?', 'Where does Rust look for the module declared with `mod rete;` in main.rs?'), [L('In `src/rete.rs` (o `src/rete/mod.rs`)', 'In `src/rete.rs` (or `src/rete/mod.rs`)'), L('Su crates.io', 'On crates.io'), L('Nel file Cargo.lock', 'In the Cargo.lock file'), L('Nella cartella target', 'In the target folder')], 0, L('Il nome del file segue il nome del modulo.', 'The file name follows the module name.')),
            Q('m3', L('Che cosa fa `use std::collections::HashMap;`?', 'What does `use std::collections::HashMap;` do?'), [L('Scarica la crate HashMap', 'Downloads the HashMap crate'), L('Copia il codice di HashMap nel file', 'Copies HashMap’s code into the file'), L('Crea una HashMap vuota', 'Creates an empty HashMap'), L('Permette di scrivere HashMap invece del percorso completo', 'Lets you write HashMap instead of the full path')], 3, L('È solo una scorciatoia per il nome.', 'It is just a shortcut for the name.')),
        ], task('rust', L('Rendi pubblica la funzione con `pub fn saluta()` dentro `mod saluti`, poi importala con `use saluti::saluta;`.', 'Make the function public with `pub fn saluta()` inside `mod saluti`, then import it with `use saluti::saluta;`.'), 'mod saluti {
    fn saluta() {
        println!("Ciao!");
    }
}
', [re('pub\s+fn\s+saluta\s*\(\s*\)', L('La funzione è pubblica', 'The function is public')), re('use\s+(crate::)?saluti::(saluta|\{\s*saluta\s*\})\s*;', L('La importi', 'You import it'))], '', 'mod saluti {
    pub fn saluta() {
        println!("Ciao!");
    }
}

use saluti::saluta;'), [
            dMatch(pMatch(), [['mod', L('Dichiara un modulo', 'Declares a module')], ['pub', L('Rende visibile fuori', 'Makes it visible outside')], ['use', L('Accorcia un percorso', 'Shortens a path')], ['crate', L('Unità di compilazione', 'Compilation unit')], ['lib.rs', L('Radice di una libreria', 'Root of a library')]], L('Organizzare il codice.', 'Organising code.')),
            dBug(pBug(), ['mod util {', '    fn doppio(n: i32) -> i32 { n * 2 }', '}', 'fn main() { println!("{}", util::doppio(4)); }'], 1, '    pub fn doppio(n: i32) -> i32 { n * 2 }', L('Senza `pub` la funzione è privata del modulo.', 'Without `pub` the function is private to the module.')),
        ]),
    ],
];
