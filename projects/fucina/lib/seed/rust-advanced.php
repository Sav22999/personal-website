<?php
/** Rust advanced: lifetimes, smart pointers, threads and channels, async with Tokio, tests and crates, macros, unsafe and FFI, advanced traits. */

return [
    'id' => 'rust-advanced', 'family' => 'rust', 'after' => 'rust-intermediate',
    'title' => L('Rust avanzato', 'Advanced Rust'), 'short' => L('Rust avanzato', 'Rust Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Lifetime, smart pointer, thread e canali, async con Tokio, test e crate, macro, unsafe e FFI, trait avanzati.', 'Lifetimes, smart pointers, threads and channels, async with Tokio, tests and crates, macros, unsafe and FFI, advanced traits.'),
    'lessons' => [
        lesson('lifetimes', L('Lifetime', 'Lifetimes'), [
            P('Un **lifetime** dice al compilatore per quanto tempo un riferimento resta valido. Quando una funzione riceve più riferimenti e ne restituisce uno, l’annotazione `\'a` collega le loro durate.', 'A **lifetime** tells the compiler how long a reference stays valid. When a function takes several references and returns one, the `\'a` annotation links their lifetimes.'),
            C('fn piu_lunga<\'a>(a: &\'a str, b: &\'a str) -> &\'a str {
    if a.len() >= b.len() { a } else { b }
}

struct Estratto<\'a> {
    testo: &\'a str,
}', 'rust'),
            P('La firma dice: il risultato vive al massimo quanto il più breve tra `a` e `b`. Nella maggior parte dei casi Rust deduce i lifetime da solo.', 'The signature says: the result lives at most as long as the shorter of `a` and `b`. In most cases Rust infers lifetimes by itself.'),
        ], [
            Q('l1', L('Che cosa indica un lifetime?', 'What does a lifetime indicate?'), [L('Per quanto un riferimento è valido', 'How long a reference is valid'), L('La durata del programma', 'How long the program runs'), L('Un timer', 'A timer'), L('La versione di Rust', 'The Rust version')], 0, L('Evita riferimenti a dati già distrutti.', 'It avoids references to dropped data.')),
            Q('l2', L('Bisogna sempre scrivere i lifetime?', 'Must you always write lifetimes?'), [L('No, spesso li deduce il compilatore', 'No, the compiler often infers them'), L('Sì, sempre', 'Yes, always'), L('Solo nei main', 'Only in main'), L('Mai', 'Never')], 0, L('Servono nei casi ambigui.', 'They are needed in ambiguous cases.')),
            Q('l3', L('Come si scrive un lifetime?', 'How do you write a lifetime?'), ["'a", '@a', '#a', '$a'], 0, L('Un apice e un nome.', 'An apostrophe and a name.')),
        ], task('rust', L('Scrivi la firma `fn primo<\'a>(v: &\'a [i32]) -> &\'a i32`.', 'Write the signature `fn primo<\'a>(v: &\'a [i32]) -> &\'a i32`.'), '', [re("fn\\s+primo\\s*<\\s*'a\\s*>\\s*\\(\\s*v\\s*:\\s*&'a\\s*\\[i32\\]\\s*\\)\\s*->\\s*&'a\\s+i32", L('La firma collega i lifetime', 'The signature links the lifetimes'))], '', "fn primo<'a>(v: &'a [i32]) -> &'a i32 {
    &v[0]
}"), [
            dBug(pBug(), ['fn crea() -> &String {', '    let s = String::from("ciao");', '    &s', '}'], 0, 'fn crea() -> String {', L('`s` viene distrutta alla fine: restituisci il valore, non un riferimento.', '`s` is dropped at the end: return the value, not a reference.')),
            dFlash(pFlash(), [[L('Un riferimento non può vivere più del valore', 'A reference cannot outlive its value'), true], [L('I lifetime cambiano la velocità del programma', 'Lifetimes change the program’s speed'), false], [L('Le struct con riferimenti hanno bisogno di lifetime', 'Structs holding references need lifetimes'), true], [L('`\'static` dura per tutto il programma', '`\'static` lasts for the whole program'), true]], L('I lifetime sono solo controlli.', 'Lifetimes are only checks.')),
        ]),
        lesson('smart-pointers', L('Smart pointer', 'Smart pointers'), [
            P('`Box<T>` mette un valore nello heap (utile per strutture ricorsive). `Rc<T>` permette più proprietari dello stesso valore in un thread; `Arc<T>` lo stesso tra thread. `RefCell` e `Mutex` permettono di modificare dentro un valore condiviso.', '`Box<T>` puts a value on the heap (handy for recursive structures). `Rc<T>` allows several owners of the same value in one thread; `Arc<T>` the same across threads. `RefCell` and `Mutex` let you change things inside a shared value.'),
            C('use std::rc::Rc;
use std::sync::{Arc, Mutex};

enum Lista { Nodo(i32, Box<Lista>), Fine }

let condiviso = Rc::new(String::from("Fucina"));
let altro = Rc::clone(&condiviso);
println!("{}", Rc::strong_count(&condiviso)); // 2

let contatore = Arc::new(Mutex::new(0));
*contatore.lock().unwrap() += 1;', 'rust'),
            P('`Rc::clone` non copia il dato: aumenta un contatore. Quando l’ultimo proprietario sparisce, il valore viene liberato.', '`Rc::clone` does not copy the data: it increments a counter. When the last owner goes away, the value is freed.'),
        ], [
            Q('p1', L('Quale smart pointer per più proprietari tra thread?', 'Which smart pointer for several owners across threads?'), ['Arc', 'Rc', 'Box', 'Cell'], 0, L('Atomic Reference Counted.', 'Atomic Reference Counted.')),
            Q('p2', L('Che cosa fa `Rc::clone`?', 'What does `Rc::clone` do?'), [L('Aumenta il conteggio dei proprietari', 'Increments the owner count'), L('Copia i dati', 'Copies the data'), L('Cancella il valore', 'Drops the value'), L('Crea un thread', 'Creates a thread')], 0, L('Il dato resta uno.', 'The data stays single.')),
            Q('p3', L('Perché serve `Box` in una lista ricorsiva?', 'Why does a recursive list need `Box`?'), [L('Per avere una dimensione nota', 'To have a known size'), L('Per velocità', 'For speed'), L('Per i thread', 'For threads'), L('Non serve', 'It is not needed')], 0, L('Il puntatore ha dimensione fissa.', 'The pointer has a fixed size.')),
        ], task('rust', L('Crea `let dati = Arc::new(Mutex::new(Vec::<i32>::new()));`.', 'Create `let dati = Arc::new(Mutex::new(Vec::<i32>::new()));`.'), 'use std::sync::{Arc, Mutex};
', [re('let\s+dati\s*=\s*Arc::new\s*\(\s*Mutex::new\s*\(', L('Dati condivisi e protetti', 'Shared, protected data'))], '', 'let dati = Arc::new(Mutex::new(Vec::<i32>::new()));'), [
            dMatch(pMatch(), [['Box', L('Valore nello heap', 'Value on the heap')], ['Rc', L('Più proprietari, un thread', 'Several owners, one thread')], ['Arc', L('Più proprietari, più thread', 'Several owners, many threads')], ['Mutex', L('Accesso uno alla volta', 'One-at-a-time access')], ['RefCell', L('Modifica controllata a runtime', 'Runtime-checked mutation')]], L('Gli smart pointer.', 'Smart pointers.')),
            dFlash(pFlash(), [[L('`Rc` si può usare tra thread', '`Rc` can be used across threads'), false], [L('`lock()` dà accesso esclusivo al dato', '`lock()` gives exclusive access to the data'), true], [L('Il valore si libera quando sparisce l’ultimo proprietario', 'The value is freed when the last owner goes'), true], [L('`Box` mette il valore nello heap', '`Box` puts the value on the heap'), true]], L('Tra thread serve `Arc`.', 'Across threads you need `Arc`.')),
        ]),
        lesson('threads', L('Thread e canali', 'Threads and channels'), [
            P('`thread::spawn` avvia un thread; `join` aspetta che finisca. I thread comunicano bene con i **canali**: uno manda con `send`, l’altro riceve con `recv`. Il compilatore impedisce le data race.', '`thread::spawn` starts a thread; `join` waits for it to finish. Threads communicate well through **channels**: one sends with `send`, the other receives with `recv`. The compiler prevents data races.'),
            C('use std::sync::mpsc;
use std::thread;

let (tx, rx) = mpsc::channel();

for id in 0..3 {
    let tx = tx.clone();
    thread::spawn(move || {
        tx.send(format!("lavoratore {id} ha finito")).unwrap();
    });
}
drop(tx);

for messaggio in rx {
    println!("{messaggio}");
}', 'rust'),
            P('`move` sposta nel thread le variabili che usa. `mpsc` significa “più produttori, un consumatore”.', '`move` moves into the thread the variables it uses. `mpsc` means “multiple producers, single consumer”.'),
        ], [
            Q('t1', L('Che cosa fa `move` in una closure di thread?', 'What does `move` do in a thread closure?'), [L('Sposta le variabili nel thread', 'Moves variables into the thread'), L('Muove la finestra', 'Moves the window'), L('Copia il thread', 'Copies the thread'), L('Niente', 'Nothing')], 0, L('Il thread ne diventa proprietario.', 'The thread becomes their owner.')),
            Q('t2', L('Che cosa significa mpsc?', 'What does mpsc mean?'), [L('Più produttori, un consumatore', 'Multiple producers, single consumer'), L('Memoria protetta', 'Protected memory'), L('Un tipo di mutex', 'A kind of mutex'), L('Un protocollo di rete', 'A network protocol')], 0, L('Tanti `tx`, un `rx`.', 'Many `tx`, one `rx`.')),
            Q('t3', L('Come aspetti la fine di un thread?', 'How do you wait for a thread to finish?'), ['join()', 'wait()', 'sleep()', 'end()'], 0, L('Sul `JoinHandle`.', 'On the `JoinHandle`.')),
        ], task('rust', L('Avvia un thread con `let h = thread::spawn(move || { println!("ciao"); });` e poi `h.join().unwrap();`.', 'Start a thread with `let h = thread::spawn(move || { println!("ciao"); });` then `h.join().unwrap();`.'), 'use std::thread;
', [re('let\s+h\s*=\s*thread::spawn\s*\(\s*move\s*\|\|', L('Avvii il thread', 'You start the thread')), re('h\.join\s*\(\s*\)\s*\.unwrap\s*\(\s*\)', L('Aspetti la fine', 'You wait for the end'))], '', 'let h = thread::spawn(move || {
    println!("ciao");
});
h.join().unwrap();'), [
            dOrder(pSort(), ['let (tx, rx) = mpsc::channel();', 'thread::spawn(move || {', '    tx.send(42).unwrap();', '});', 'println!("{}", rx.recv().unwrap());'], L('Canale, thread, invio, ricezione.', 'Channel, thread, send, receive.')),
            dBlanks(pFill(), "let h = thread::___(___ || calcola());\nlet risultato = h.___().unwrap();", ['spawn', 'move', 'join'], ['start', 'async', 'wait'], L('Avvia, sposta, aspetta.', 'Start, move, wait.')),
        ]),
        lesson('async', L('Async con Tokio', 'Async with Tokio'), [
            P('Il codice `async` di Rust ha bisogno di un **runtime** che lo esegua: il più usato è **Tokio**. `#[tokio::main]` prepara il runtime, `.await` aspetta un futuro, `tokio::join!` ne esegue più insieme.', 'Rust `async` code needs a **runtime** to run it: the most used is **Tokio**. `#[tokio::main]` sets up the runtime, `.await` waits for a future, `tokio::join!` runs several together.'),
            C('use tokio::time::{sleep, Duration};

async fn scarica(nome: &str) -> String {
    sleep(Duration::from_millis(500)).await;
    format!("{nome} pronto")
}

#[tokio::main]
async fn main() {
    let (a, b) = tokio::join!(scarica("A"), scarica("B"));
    println!("{a}, {b}");
}', 'rust'),
            P('Un futuro non fa nulla finché non viene atteso: è **pigro**. Per lavori di calcolo pesante meglio i thread.', 'A future does nothing until awaited: it is **lazy**. For heavy computation threads are better.'),
        ], [
            Q('a1', L('Chi esegue il codice async in Rust?', 'Who runs async code in Rust?'), [L('Un runtime come Tokio', 'A runtime such as Tokio'), L('Il compilatore', 'The compiler'), L('Il sistema operativo da solo', 'The OS by itself'), L('Cargo', 'Cargo')], 0, L('Rust non include un runtime.', 'Rust ships no runtime.')),
            Q('a2', L('Dove si scrive `.await`?', 'Where do you write `.await`?'), [L('Dopo il futuro da attendere', 'After the future to wait for'), L('Prima di `fn`', 'Before `fn`'), L('Nel Cargo.toml', 'In Cargo.toml'), L('Mai', 'Never')], 0, L('È un suffisso.', 'It is a suffix.')),
            Q('a3', L('Un futuro parte appena creato?', 'Does a future start as soon as it is created?'), [L('No, è pigro', 'No, it is lazy'), L('Sì', 'Yes'), L('Solo con Tokio', 'Only with Tokio'), L('Solo nei test', 'Only in tests')], 0, L('Parte quando viene atteso.', 'It starts when awaited.')),
        ], task('rust', L('Scrivi `async fn saluto() -> String` che restituisce `String::from("ciao")`, e chiamalo con `.await`.', 'Write `async fn saluto() -> String` returning `String::from("ciao")`, and call it with `.await`.'), '', [re('async\s+fn\s+saluto\s*\(\s*\)\s*->\s*String', L('La funzione è async', 'The function is async')), re('saluto\s*\(\s*\)\s*\.await', L('La attendi', 'You await it'))], '', 'async fn saluto() -> String {
    String::from("ciao")
}

let s = saluto().await;'), [
            dBug(pBug(), ['#[tokio::main]', 'async fn main() {', '    let testo = leggi_file("note.txt");', '    println!("{testo}");', '}'], 2, '    let testo = leggi_file("note.txt").await;', L('Senza `.await` hai un futuro, non il testo.', 'Without `.await` you have a future, not the text.')),
            dMatch(pMatch(), [['async fn', L('Restituisce un futuro', 'Returns a future')], ['.await', L('Aspetta il risultato', 'Waits for the result')], ['tokio::join!', L('Più futuri insieme', 'Several futures together')], ['#[tokio::main]', L('Prepara il runtime', 'Sets up the runtime')]], L('Async in Rust.', 'Async in Rust.')),
        ]),
        lesson('tests-crates', L('Test, crate e Cargo', 'Tests, crates and Cargo'), [
            P('I test stanno accanto al codice in un modulo `#[cfg(test)]`; ogni funzione `#[test]` usa `assert_eq!` e `assert!`. `cargo test` li esegue tutti. Le librerie si chiamano **crate** e si aggiungono con `cargo add`.', 'Tests sit next to the code in a `#[cfg(test)]` module; each `#[test]` function uses `assert_eq!` and `assert!`. `cargo test` runs them all. Libraries are called **crates** and you add them with `cargo add`.'),
            C('pub fn somma(a: i32, b: i32) -> i32 { a + b }

#[cfg(test)]
mod tests {
    use super::*;

    #[test]
    fn somma_due_numeri() {
        assert_eq!(somma(2, 3), 5);
    }

    #[test]
    #[should_panic]
    fn divisione_per_zero() {
        let _ = 1 / std::hint::black_box(0);
    }
}

// terminale: cargo add serde --features derive', 'rust'),
            P('`cargo clippy` suggerisce miglioramenti, `cargo fmt` formatta il codice come tutti i progetti Rust.', '`cargo clippy` suggests improvements, `cargo fmt` formats code like every Rust project.'),
        ], [
            Q('x1', L('Quale comando esegue i test?', 'Which command runs the tests?'), ['cargo test', 'cargo check', 'rust test', 'cargo run --test-only'], 0, L('Compila ed esegue i `#[test]`.', 'It builds and runs the `#[test]`s.')),
            Q('x2', L('Quale macro confronta due valori in un test?', 'Which macro compares two values in a test?'), ['assert_eq!', 'compare!', 'equal!', 'check!'], 0, L('Mostra entrambi se diversi.', 'It shows both when different.')),
            Q('x3', L('Come aggiungi una libreria?', 'How do you add a library?'), ['cargo add nome', 'cargo install nome', 'npm install nome', 'use nome'], 0, L('Aggiorna il Cargo.toml.', 'It updates Cargo.toml.')),
        ], task('rust', L('Scrivi un `#[test]` chiamato `doppio_di_due` con `assert_eq!(doppio(2), 4);`.', 'Write a `#[test]` named `doppio_di_due` with `assert_eq!(doppio(2), 4);`.'), '#[cfg(test)]
mod tests {
    use super::*;
}', [re('#\[test\]\s*fn\s+doppio_di_due\s*\(\s*\)', L('Il test esiste', 'The test exists')), re('assert_eq!\s*\(\s*doppio\s*\(\s*2\s*\)\s*,\s*4\s*\)', L('L’asserzione è giusta', 'The assertion is right'))], '', '#[test]
fn doppio_di_due() {
    assert_eq!(doppio(2), 4);
}'), [
            dOrder(pSort(), ['#[cfg(test)]', 'mod tests {', '    use super::*;', '    #[test]', '    fn somma_funziona() {', '        assert_eq!(somma(2, 3), 5);', '    }', '}'], L('Modulo solo per i test, import, attributo, funzione, asserzione.', 'Test-only module, import, attribute, function, assertion.')),
            dMatch(pMatch(), [['cargo test', L('Esegue i test', 'Runs tests')], ['cargo fmt', L('Formatta il codice', 'Formats code')], ['cargo clippy', L('Suggerimenti', 'Suggestions')], ['cargo add', L('Aggiunge una crate', 'Adds a crate')], ['Cargo.toml', L('Configurazione del progetto', 'Project configuration')]], L('Gli strumenti di Cargo.', 'Cargo tools.')),
        ]),
        lesson('macros', L('Macro dichiarative', 'Declarative macros'), [
            P('Una **macro** genera codice prima della compilazione: per questo `println!` accetta un numero variabile di argomenti. Con `macro_rules!` scrivi le tue: ogni regola confronta un **pattern** di token e lo sostituisce con codice.', 'A **macro** generates code before compilation: that is why `println!` accepts any number of arguments. With `macro_rules!` you write your own: each rule matches a **pattern** of tokens and replaces it with code.'),
            C('macro_rules! quadrato {
    ($x:expr) => {
        $x * $x
    };
}

macro_rules! mappa {
    ($($chiave:expr => $valore:expr),* $(,)?) => {{
        let mut m = std::collections::HashMap::new();
        $( m.insert($chiave, $valore); )*
        m
    }};
}

let n = quadrato!(7);                       // 49
let eta = mappa!("Ada" => 36, "Bea" => 28);', 'rust'),
            P('`$x:expr` cattura un’espressione (ci sono anche `ident`, `ty`, `tt`…); `$( … ),*` ripete per ogni elemento separato da virgole. A differenza delle macro di C lavorano sulla struttura del codice: `quadrato!(1 + 2)` dà 9, non 5. Le **macro procedurali**, come `#[derive(Serialize)]`, sono funzioni Rust che generano codice e vivono in una crate a parte.', '`$x:expr` captures an expression (there are also `ident`, `ty`, `tt`…); `$( … ),*` repeats for each comma-separated item. Unlike C macros they work on the structure of the code: `quadrato!(1 + 2)` gives 9, not 5. **Procedural macros**, such as `#[derive(Serialize)]`, are Rust functions that generate code and live in a separate crate.'),
        ], [
            Q('ma1', L('Che cosa cattura `$x:expr`?', 'What does `$x:expr` capture?'), [L('Un identificatore', 'An identifier'), L('Un’espressione', 'An expression'), L('Un tipo', 'A type'), L('Un file', 'A file')], 1, L('Per un nome si usa `ident`, per un tipo `ty`.', 'For a name you use `ident`, for a type `ty`.')),
            Q('ma2', L('Quanto vale `quadrato!(1 + 2)` con la macro dell’esempio?', 'What does `quadrato!(1 + 2)` give with the sample macro?'), ['5', '3', '9', L('Errore di compilazione', 'Compile error')], 2, L('L’espressione resta un blocco unico: (1 + 2) * (1 + 2).', 'The expression stays one unit: (1 + 2) * (1 + 2).')),
            Q('ma3', L('Che cosa fa `$( … ),*` in una regola?', 'What does `$( … ),*` do in a rule?'), [L('Ripete il pezzo per ogni elemento separato da virgole', 'Repeats the piece for each comma-separated item'), L('Commenta il codice', 'Comments out the code'), L('Moltiplica i valori', 'Multiplies the values'), L('Rende pubblica la macro', 'Makes the macro public')], 0, L('`*` vuol dire zero o più volte, `+` una o più.', '`*` means zero or more times, `+` one or more.')),
        ], task('rust', L('Scrivi `macro_rules! doppio` con la regola `($x:expr) => { $x * 2 };`.', 'Write `macro_rules! doppio` with the rule `($x:expr) => { $x * 2 };`.'), '', [re('macro_rules!\s*doppio\s*\{', L('La macro esiste', 'The macro exists')), re('\(\s*\$x\s*:\s*expr\s*\)\s*=>\s*\{\s*\$x\s*\*\s*2\s*\}', L('La regola è giusta', 'The rule is right'))], '', 'macro_rules! doppio {
    ($x:expr) => { $x * 2 };
}'), [
            dBlanks(pFill(), "macro_rules! saluta {\n    (\$nome:___) => {\n        println!(\"Ciao {}\", \$nome)\n    };\n}\n___!(\"Ada\");", ['expr', 'saluta'], ['string', 'macro', 'fn'], L('Frammento e chiamata con il punto esclamativo.', 'Fragment and call with the exclamation mark.')),
            dFlash(pFlash(), [[L('Le macro si espandono prima della compilazione', 'Macros expand before compilation'), true], [L('`println!` è una funzione normale', '`println!` is an ordinary function'), false], [L('`#[derive]` usa macro procedurali', '`#[derive]` uses procedural macros'), true], [L('`macro_rules!` lavora su token, non su testo grezzo', '`macro_rules!` works on tokens, not raw text'), true]], L('Il punto esclamativo segnala una macro.', 'The exclamation mark marks a macro.')),
        ]),
        lesson('unsafe-ffi', L('unsafe e FFI', 'unsafe and FFI'), [
            P('Il compilatore non può verificare tutto. Dentro un blocco `unsafe` puoi fare alcune cose in più, come dereferenziare puntatori grezzi e chiamare funzioni `unsafe`: sei tu a garantire che siano corrette. `unsafe` non spegne il borrow checker, apre solo quelle porte.', 'The compiler cannot check everything. Inside an `unsafe` block you can do a few extra things, such as dereferencing raw pointers and calling `unsafe` functions: you guarantee they are correct. `unsafe` does not switch off the borrow checker, it only opens those doors.'),
            C('let mut x = 10;
let p = &mut x as *mut i32;      // puntatore grezzo: crearlo è sicuro
unsafe {
    *p += 1;                     // usarlo no
}

// Chiamare una funzione C (edizione 2024: il blocco è `unsafe extern`)
unsafe extern "C" {
    fn abs(n: i32) -> i32;
}

fn valore_assoluto(n: i32) -> i32 {
    unsafe { abs(n) }
}

// Esportare una funzione Rust verso il C
#[unsafe(no_mangle)]
pub extern "C" fn somma(a: i32, b: i32) -> i32 { a + b }', 'rust'),
            P('**FFI** (Foreign Function Interface) è il ponte verso il C: le librerie di sistema e molte altre parlano C. La pratica migliore è chiudere l’`unsafe` in una piccola funzione sicura, come `valore_assoluto`, e scrivere in un commento perché è corretto. **Miri** (`cargo miri test`) aiuta a scovare comportamenti indefiniti.', '**FFI** (Foreign Function Interface) is the bridge to C: system libraries and many others speak C. Best practice is to wrap the `unsafe` in a small safe function, like `valore_assoluto`, and explain in a comment why it is correct. **Miri** (`cargo miri test`) helps find undefined behaviour.'),
        ], [
            Q('un1', L('Che cosa fa un blocco `unsafe`?', 'What does an `unsafe` block do?'), [L('Disattiva il borrow checker', 'Switches off the borrow checker'), L('Rende il codice più veloce', 'Makes the code faster'), L('Permette alcune operazioni che il compilatore non può verificare', 'Allows some operations the compiler cannot check'), L('Fa girare il codice in un altro thread', 'Runs the code in another thread')], 2, L('Le altre regole restano attive.', 'The other rules stay active.')),
            Q('un2', L('Creare un puntatore grezzo `*const i32` richiede `unsafe`?', 'Does creating a raw pointer `*const i32` need `unsafe`?'), [L('No, serve solo per dereferenziarlo', 'No, only dereferencing it does'), L('Sì, sempre', 'Yes, always'), L('Solo in modalità release', 'Only in release mode'), L('Solo nelle librerie', 'Only in libraries')], 0, L('Il pericolo è leggere o scrivere attraverso il puntatore.', 'The danger is reading or writing through the pointer.')),
            Q('un3', L('Come si dichiara, nell’edizione 2024, un blocco di funzioni C da chiamare?', 'In the 2024 edition, how do you declare a block of C functions to call?'), ['extern "C" { … }', 'import C { … }', 'ffi "C" { … }', 'unsafe extern "C" { … }'], 3, L('Dichiarare male una firma è già un rischio, quindi il blocco va segnato `unsafe`.', 'Getting a signature wrong is already a risk, so the block must be marked `unsafe`.')),
        ], task('rust', L('Dereferenzia il puntatore dentro un blocco unsafe: `unsafe { println!("{}", *p); }`.', 'Dereference the pointer inside an unsafe block: `unsafe { println!("{}", *p); }`.'), 'let x = 5;
let p = &x as *const i32;
', [re('unsafe\s*\{[\s\S]*\*\s*p[\s\S]*\}', L('Dereferenzi dentro unsafe', 'You dereference inside unsafe'))], '', 'unsafe {
    println!("{}", *p);
}'), [
            dBug(pBug(), ['let v = 42;', 'let p = &v as *const i32;', 'println!("{}", *p);'], 2, 'println!("{}", unsafe { *p });', L('Dereferenziare un puntatore grezzo richiede `unsafe`.', 'Dereferencing a raw pointer needs `unsafe`.')),
            dMatch(pMatch(), [['unsafe { }', L('Operazioni non verificate', 'Unchecked operations')], ['*const T', L('Puntatore grezzo in lettura', 'Read-only raw pointer')], ['extern "C"', L('Convenzione di chiamata del C', 'C calling convention')], ['#[unsafe(no_mangle)]', L('Nome del simbolo invariato', 'Symbol name kept as is')], ['Miri', L('Trova comportamenti indefiniti', 'Finds undefined behaviour')]], L('Ai confini della sicurezza.', 'At the edges of safety.')),
        ]),
        lesson('advanced-traits', L('Trait avanzati', 'Advanced traits'), [
            P('Un trait può avere un **tipo associato**: un tipo scelto da chi lo implementa. L’esempio più famoso è `Iterator`, con `type Item`: implementando solo `next` ottieni gratis `map`, `filter`, `sum` e tutti gli altri adattatori.', 'A trait can have an **associated type**: a type chosen by the implementer. The best-known example is `Iterator`, with `type Item`: implementing only `next` gives you `map`, `filter`, `sum` and all the other adapters for free.'),
            C('struct Conto { da: u32, a: u32 }

impl Iterator for Conto {
    type Item = u32;
    fn next(&mut self) -> Option<u32> {
        if self.da > self.a { return None; }
        let v = self.da;
        self.da += 1;
        Some(v)
    }
}

let pari: Vec<u32> = Conto { da: 1, a: 10 }.filter(|n| n % 2 == 0).collect();

use std::ops::Add;

#[derive(Debug, Clone, Copy, PartialEq)]
struct Vettore { x: f64, y: f64 }

impl Add for Vettore {
    type Output = Vettore;
    fn add(self, o: Vettore) -> Vettore { Vettore { x: self.x + o.x, y: self.y + o.y } }
}

let v = Vettore { x: 1.0, y: 2.0 } + Vettore { x: 3.0, y: 4.0 };', 'rust'),
            P('I trait di `std::ops` (`Add`, `Mul`, `Index`…) ridefiniscono gli operatori. Con `impl<T: Display> Descrivi for T` si implementa un trait per tutti i tipi che rispettano un vincolo (**blanket implementation**). Con un tipo associato un tipo implementa il trait una volta sola; con un parametro generico (`trait Converti<T>`) può farlo per più `T`.', 'The `std::ops` traits (`Add`, `Mul`, `Index`…) redefine operators. With `impl<T: Display> Descrivi for T` you implement a trait for every type meeting a bound (**blanket implementation**). With an associated type a type implements the trait only once; with a generic parameter (`trait Converti<T>`) it can do so for several `T`.'),
        ], [
            Q('at1', L('Che cosa basta implementare per avere un Iterator completo?', 'What is enough to implement for a complete Iterator?'), [L('`map` e `filter`', '`map` and `filter`'), L('Il metodo `next` e il tipo `Item`', 'The `next` method and the `Item` type'), L('Il metodo `collect`', 'The `collect` method'), L('`into_iter` e `len`', '`into_iter` and `len`')], 1, L('Gli adattatori sono metodi predefiniti del trait.', 'The adapters are default methods of the trait.')),
            Q('at2', L('Quale trait ridefinisce l’operatore `+`?', 'Which trait redefines the `+` operator?'), ['Plus', 'Sum', 'Operator', 'std::ops::Add'], 3, L('`Sum` serve a `.sum()` sugli iteratori.', '`Sum` powers `.sum()` on iterators.')),
            Q('at3', L('Che cos’è una blanket implementation?', 'What is a blanket implementation?'), [L('Un trait senza metodi', 'A trait with no methods'), L('Un’implementazione vuota da completare', 'An empty implementation to fill in'), L('Implementare un trait per tutti i tipi che rispettano un vincolo', 'Implementing a trait for every type meeting a bound'), L('Un trait implementato solo nei test', 'A trait implemented only in tests')], 2, L('La libreria standard implementa così `ToString` per ogni `Display`.', 'This is how the standard library implements `ToString` for every `Display`.')),
        ], task('rust', L('Completa `impl Iterator for Contatore` dichiarando `type Item = u32;`.', 'Complete `impl Iterator for Contatore` by declaring `type Item = u32;`.'), 'impl Iterator for Contatore {
    fn next(&mut self) -> Option<Self::Item> {
        self.n += 1;
        Some(self.n)
    }
}', [re('type\s+Item\s*=\s*u32\s*;', L('Il tipo associato è dichiarato', 'The associated type is declared'))], '', 'impl Iterator for Contatore {
    type Item = u32;
    fn next(&mut self) -> Option<Self::Item> {
        self.n += 1;
        Some(self.n)
    }
}'), [
            dBlanks(pFill(), "impl Iterator for Dado {\n    ___ Item = u8;\n    fn ___(&mut self) -> Option<u8> { Some(4) }\n}\nimpl std::ops::___ for Punto {\n    type Output = Punto;\n    fn add(self, o: Punto) -> Punto { Punto(self.0 + o.0) }\n}", ['type', 'next', 'Add'], ['let', 'iter', 'Plus'], L('Tipo associato, metodo richiesto, operatore.', 'Associated type, required method, operator.')),
            dMatch(pMatch(), [['type Item', L('Tipo associato di Iterator', 'Iterator’s associated type')], ['next', L('Unico metodo da scrivere', 'The only method to write')], ['std::ops::Add', L('Operatore +', 'The + operator')], ['impl<T: X> Y for T', L('Blanket implementation', 'Blanket implementation')]], L('Trait avanzati.', 'Advanced traits.')),
        ]),
    ],
];
