<?php
/** Rust basics: variables and mutability, types, control flow, functions and ownership. */

return [
    'id' => 'rust-base', 'family' => 'rust', 'after' => null,
    'title' => L('Rust base', 'Rust Basics'), 'short' => L('Rust base', 'Rust Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Veloce e sicuro: variabili immutabili, tipi, controllo del flusso, funzioni e il famoso ownership.', 'Fast and safe: immutable variables, types, control flow, functions and the famous ownership.'),
    'lessons' => [
        lesson('hello', L('Ciao, Cargo', 'Hello, Cargo'), [
            P('**Cargo** crea e compila i progetti Rust: `cargo new ciao` prepara la cartella, `cargo run` compila ed esegue. Il programma parte da `fn main()`; `println!` (con il punto esclamativo, è una **macro**) stampa.', '**Cargo** creates and builds Rust projects: `cargo new ciao` sets up the folder, `cargo run` builds and runs. The program starts from `fn main()`; `println!` (with the exclamation mark, it is a **macro**) prints.'),
            C('fn main() {
    let nome = "Ada";
    let anni = 36;
    println!("Ciao, {nome}! Hai {} anni.", anni);
}', 'rust'),
            P('Le graffe `{}` nel testo vengono riempite dagli argomenti, o direttamente dal nome di una variabile come `{nome}`.', 'Braces `{}` in the text are filled by the arguments, or directly by a variable name like `{nome}`.'),
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
            C('let voto = 8;
let esito = if voto >= 6 { "promosso" } else { "bocciato" };

for i in 1..=3 {
    println!("Giro {i}");
}

let mut n = 0;
let risultato = loop {
    n += 1;
    if n == 5 { break n * 10; }
};', 'rust'),
            P('`1..=3` include il 3, `1..3` no. `break` in un `loop` può restituire un valore.', '`1..=3` includes 3, `1..3` does not. `break` in a `loop` can return a value.'),
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
}', 'rust'),
            P('Se aggiungi il punto e virgola all’ultima riga diventa un’istruzione e la funzione non restituisce più nulla: errore.', 'If you add a semicolon to the last line it becomes a statement and the function returns nothing: error.'),
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
    ],
];
