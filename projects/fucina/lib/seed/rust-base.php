<?php
/** Rust basics: variables and mutability, types, control flow, functions, ownership, tuples and arrays, String and &str, modules. */

return [
    'id' => 'rust-base', 'family' => 'rust', 'after' => null,
    'title' => L('Rust base', 'Rust Basics'), 'short' => L('Rust base', 'Rust Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Veloce e sicuro: variabili immutabili, tipi, controllo del flusso, funzioni, il famoso ownership, tuple e array, stringhe e moduli.', 'Fast and safe: immutable variables, types, control flow, functions, the famous ownership, tuples and arrays, strings and modules.'),
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
}', 'rust'),
            P('Un modulo può stare in un file a parte: `mod geometria;` in `main.rs` carica `src/geometria.rs`. Le crate esterne si aggiungono al `Cargo.toml` (con `cargo add`) e si usano con `use nome_crate::…`.', 'A module can live in its own file: `mod geometria;` in `main.rs` loads `src/geometria.rs`. External crates are added to `Cargo.toml` (with `cargo add`) and used with `use crate_name::…`.'),
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
