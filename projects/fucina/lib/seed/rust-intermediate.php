<?php
/** Rust intermediate: structs and impl, enums and match, Option and Result, collections, traits. */

return [
    'id' => 'rust-intermediate', 'family' => 'rust', 'after' => 'rust-base',
    'title' => L('Rust intermedio', 'Intermediate Rust'), 'short' => L('Rust intermedio', 'Rust Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Struct e metodi, enum e `match`, `Option` e `Result`, collezioni e trait.', 'Structs and methods, enums and `match`, `Option` and `Result`, collections and traits.'),
    'lessons' => [
        lesson('structs', L('Struct e impl', 'Structs and impl'), [
            P('Una **struct** raggruppa campi con nome. I metodi si scrivono in un blocco `impl`: il primo parametro `&self` (o `&mut self`) è l’istanza. Le funzioni senza `self`, come `new`, si chiamano con `Tipo::`.', 'A **struct** groups named fields. Methods go in an `impl` block: the first parameter `&self` (or `&mut self`) is the instance. Functions without `self`, like `new`, are called with `Type::`.'),
            C('#[derive(Debug, Clone)]
struct Conto {
    titolare: String,
    saldo: f64,
}

impl Conto {
    fn new(titolare: &str) -> Self {
        Self { titolare: titolare.to_string(), saldo: 0.0 }
    }
    fn versa(&mut self, importo: f64) {
        self.saldo += importo;
    }
}

let mut c = Conto::new("Ada");
c.versa(50.0);
println!("{:?}", c);', 'rust'),
            P('`#[derive(Debug)]` fa scrivere a Rust il codice per stampare la struct con `{:?}`.', '`#[derive(Debug)]` makes Rust write the code to print the struct with `{:?}`.'),
        ], [
            Q('s1', L('Dove si scrivono i metodi di una struct?', 'Where do you write a struct’s methods?'), [L('In un blocco `impl`', 'In an `impl` block'), L('Dentro la struct', 'Inside the struct'), L('In main', 'In main'), L('In un trait sempre', 'Always in a trait')], 0, L('`impl NomeStruct { … }`.', '`impl StructName { … }`.')),
            Q('s2', L('Che cosa serve a un metodo che modifica l’istanza?', 'What does a method changing the instance need?'), ['&mut self', '&self', 'self only', 'mut'], 0, L('E l’istanza deve essere `mut`.', 'And the instance must be `mut`.')),
            Q('s3', L('Come si chiama `new`?', 'How do you call `new`?'), ['Conto::new(…)', 'Conto.new(…)', 'new Conto(…)', 'Conto->new(…)'], 0, L('Non ha `self`.', 'It has no `self`.')),
        ], task('rust', L('Crea `struct Punto { x: i32, y: i32 }` con `impl Punto` e il metodo `fn somma(&self) -> i32`.', 'Create `struct Punto { x: i32, y: i32 }` with `impl Punto` and the method `fn somma(&self) -> i32`.'), '', [re('struct\s+Punto\s*\{\s*x\s*:\s*i32\s*,\s*y\s*:\s*i32\s*,?\s*\}', L('La struct esiste', 'The struct exists')), re('impl\s+Punto\s*\{', L('C’è il blocco impl', 'There is the impl block')), re('fn\s+somma\s*\(\s*&self\s*\)\s*->\s*i32', L('Il metodo è giusto', 'The method is right'))], '', 'struct Punto { x: i32, y: i32 }

impl Punto {
    fn somma(&self) -> i32 { self.x + self.y }
}'), [
            dBlanks(pFill(), "___ Gatto { nome: String }\n___ Gatto {\n    fn miagola(___) -> String { format!(\"{}: miao\", self.nome) }\n}", ['struct', 'impl', '&self'], ['class', 'fn', 'this'], L('Struct, impl, istanza.', 'Struct, impl, instance.')),
            dMatch(pMatch(), [['struct', L('Dati con nome', 'Named data')], ['impl', L('Metodi del tipo', 'The type’s methods')], ['&self', L('Lettura dell’istanza', 'Reads the instance')], ['&mut self', L('Modifica l’istanza', 'Changes the instance')], ['Self', L('Il tipo stesso', 'The type itself')]], L('Struct in Rust.', 'Structs in Rust.')),
        ]),
        lesson('enums', L('Enum e match', 'Enums and match'), [
            P('Un **enum** elenca le varianti possibili, e ognuna può portare dati. `match` controlla tutte le varianti: il compilatore protesta se ne dimentichi una.', 'An **enum** lists the possible variants, and each can carry data. `match` checks every variant: the compiler complains if you forget one.'),
            C('enum Forma {
    Cerchio(f64),
    Rettangolo { base: f64, altezza: f64 },
    Punto,
}

fn area(f: &Forma) -> f64 {
    match f {
        Forma::Cerchio(r) => 3.14 * r * r,
        Forma::Rettangolo { base, altezza } => base * altezza,
        Forma::Punto => 0.0,
    }
}', 'rust'),
            P('`_` nel `match` cattura tutti i casi rimanenti. `if let` è comodo quando ti interessa una sola variante.', '`_` in `match` catches all remaining cases. `if let` is handy when you care about just one variant.'),
        ], [
            Q('e1', L('Che cosa succede se `match` non copre una variante?', 'What happens if `match` misses a variant?'), [L('Errore di compilazione', 'Compile error'), L('Niente', 'Nothing'), L('Crash a runtime', 'Runtime crash'), L('Avviso ignorabile', 'Ignorable warning')], 0, L('Il match deve essere esaustivo.', 'The match must be exhaustive.')),
            Q('e2', L('Che cosa cattura `_`?', 'What does `_` catch?'), [L('Tutti gli altri casi', 'All other cases'), L('Nessun caso', 'No case'), L('Solo zero', 'Only zero'), L('Gli errori', 'Errors')], 0, L('Il caso jolly.', 'The wildcard.')),
            Q('e3', L('Una variante di enum può portare dati?', 'Can an enum variant carry data?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo numeri', 'Only numbers'), L('Solo stringhe', 'Only strings')], 0, L('Anche campi con nome.', 'Even named fields.')),
        ], task('rust', L('Completa `match semaforo` con `Luce::Rosso => "fermo"`, `Luce::Verde => "vai"` e `_ => "attenzione"`.', 'Complete `match semaforo` with `Luce::Rosso => "fermo"`, `Luce::Verde => "vai"` and `_ => "attenzione"`.'), 'let azione = match semaforo {
};', [re('Luce::Rosso\s*=>\s*"fermo"', L('Caso rosso', 'Red case')), re('Luce::Verde\s*=>\s*"vai"', L('Caso verde', 'Green case')), re('_\s*=>\s*"attenzione"', L('Tutti gli altri', 'All the others'))], '', 'let azione = match semaforo {
    Luce::Rosso => "fermo",
    Luce::Verde => "vai",
    _ => "attenzione",
};'), [
            dOrder(pSort(), ['enum Moneta { Uno, Due, Cinque }', 'fn valore(m: Moneta) -> u32 {', '    match m {', '        Moneta::Uno => 1,', '        Moneta::Due => 2,', '        Moneta::Cinque => 5,', '    }', '}'], L('Enum, funzione, match completo.', 'Enum, function, full match.')),
            dFlash(pFlash(), [[L('`match` deve coprire tutte le varianti', '`match` must cover every variant'), true], [L('Un enum può avere una sola variante con dati', 'An enum can have only one variant with data'), false], [L('`if let` guarda una sola variante', '`if let` looks at a single variant'), true]], L('Ogni variante può portare dati.', 'Every variant can carry data.')),
        ]),
        lesson('option-result', L('Option e Result', 'Option and Result'), [
            P('Rust non ha `null`: un valore che può mancare è un `Option<T>` (`Some(valore)` o `None`). Un’operazione che può fallire restituisce un `Result<T, E>` (`Ok` o `Err`). L’operatore `?` passa l’errore a chi ha chiamato.', 'Rust has no `null`: a value that may be missing is an `Option<T>` (`Some(value)` or `None`). An operation that may fail returns a `Result<T, E>` (`Ok` or `Err`). The `?` operator passes the error up to the caller.'),
            C('fn primo_pari(v: &[i32]) -> Option<i32> {
    v.iter().copied().find(|n| n % 2 == 0)
}

fn leggi_eta(testo: &str) -> Result<u8, std::num::ParseIntError> {
    let eta: u8 = testo.trim().parse()?;
    Ok(eta)
}

let x = primo_pari(&[1, 3, 4]).unwrap_or(0);
match leggi_eta("36") {
    Ok(e) => println!("{e} anni"),
    Err(err) => println!("Errore: {err}"),
}', 'rust'),
            P('`unwrap()` estrae il valore ma fa crashare il programma se c’è `None` o `Err`: meglio `match`, `unwrap_or` o `?`.', '`unwrap()` extracts the value but crashes the program on `None` or `Err`: better `match`, `unwrap_or` or `?`.'),
        ], [
            Q('r1', L('Che cosa rappresenta `Option`?', 'What does `Option` represent?'), [L('Un valore che può mancare', 'A value that may be missing'), L('Una scelta dell’utente', 'A user choice'), L('Un errore', 'An error'), L('Un menu', 'A menu')], 0, L('`Some` o `None`.', '`Some` or `None`.')),
            Q('r2', L('Che cosa fa `?` su un `Result`?', 'What does `?` do on a `Result`?'), [L('Restituisce subito l’errore a chi chiama', 'Returns the error to the caller at once'), L('Ignora l’errore', 'Ignores the error'), L('Stampa', 'Prints'), L('Riprova', 'Retries')], 0, L('Se è `Ok` estrae il valore.', 'If `Ok` it extracts the value.')),
            Q('r3', L('Perché evitare `unwrap()`?', 'Why avoid `unwrap()`?'), [L('Crasha se manca il valore', 'It crashes if the value is missing'), L('È lento', 'It is slow'), L('È deprecato', 'It is deprecated'), L('Non compila', 'It does not compile')], 0, L('Va bene solo nei prototipi.', 'Fine only in prototypes.')),
        ], task('rust', L('Scrivi `fn dividi(a: f64, b: f64) -> Option<f64>` che restituisce `None` se `b == 0.0`, altrimenti `Some(a / b)`.', 'Write `fn dividi(a: f64, b: f64) -> Option<f64>` returning `None` if `b == 0.0`, otherwise `Some(a / b)`.'), '', [re('fn\s+dividi\s*\(\s*a\s*:\s*f64\s*,\s*b\s*:\s*f64\s*\)\s*->\s*Option<f64>', L('La firma è giusta', 'The signature is right')), re('None', L('C’è il caso vuoto', 'There is the empty case')), re('Some\s*\(\s*a\s*/\s*b\s*\)', L('E il risultato', 'And the result'))], '', 'fn dividi(a: f64, b: f64) -> Option<f64> {
    if b == 0.0 { None } else { Some(a / b) }
}'), [
            dBlanks(pFill(), "fn carica(p: &str) -> ___<String, std::io::Error> {\n    let testo = std::fs::read_to_string(p)___;\n    ___(testo)\n}", ['Result', '?', 'Ok'], ['Option', '!', 'Some'], L('Tipo, propagazione, successo.', 'Type, propagation, success.')),
            dMatch(pMatch(), [['Some(x)', L('C’è un valore', 'There is a value')], ['None', L('Nessun valore', 'No value')], ['Ok(x)', L('Riuscito', 'Succeeded')], ['Err(e)', L('Fallito', 'Failed')], ['?', L('Passa l’errore in su', 'Passes the error up')]], L('Option e Result.', 'Option and Result.')),
        ]),
        lesson('collections', L('Vec, HashMap e iteratori', 'Vec, HashMap and iterators'), [
            P('`Vec<T>` è una lista che cresce, `HashMap<K, V>` associa chiavi e valori. Gli **iteratori** (`iter`, `map`, `filter`, `sum`, `collect`) trasformano le collezioni senza cicli scritti a mano, ed è codice veloce quanto un ciclo.', '`Vec<T>` is a growable list, `HashMap<K, V>` maps keys to values. **Iterators** (`iter`, `map`, `filter`, `sum`, `collect`) transform collections with no hand-written loops, and the code is as fast as a loop.'),
            C('use std::collections::HashMap;

let mut voti = vec![7, 8, 9];
voti.push(6);
let promossi: Vec<i32> = voti.iter().copied().filter(|v| *v >= 6).collect();
let totale: i32 = voti.iter().sum();

let mut eta: HashMap<&str, u32> = HashMap::new();
eta.insert("Ada", 36);
*eta.entry("Bea").or_insert(0) += 1;', 'rust'),
            P('Le **closure** si scrivono tra barre verticali: `|v| *v >= 6`. `entry(...).or_insert(...)` inserisce un valore solo se la chiave manca.', '**Closures** are written between vertical bars: `|v| *v >= 6`. `entry(...).or_insert(...)` inserts a value only if the key is missing.'),
        ], [
            Q('c1', L('Come crei un Vec con valori?', 'How do you create a Vec with values?'), ['vec![1, 2, 3]', 'Vec(1, 2, 3)', '[1, 2, 3].vec', 'new Vec(1, 2, 3)'], 0, L('È una macro.', 'It is a macro.')),
            Q('c2', L('Che cosa fa `collect()`?', 'What does `collect()` do?'), [L('Raccoglie l’iteratore in una collezione', 'Gathers the iterator into a collection'), L('Pulisce la memoria', 'Cleans memory'), L('Conta', 'Counts'), L('Ordina', 'Sorts')], 0, L('Il tipo di destinazione va indicato.', 'The target type must be given.')),
            Q('c3', L('Come si scrive una closure?', 'How do you write a closure?'), ['|x| x * 2', 'x => x * 2', 'fn(x) x * 2', 'lambda x: x * 2'], 0, L('Tra barre verticali.', 'Between vertical bars.')),
        ], task('rust', L('Da `numeri` ottieni `let doppi: Vec<i32> = numeri.iter().map(|n| n * 2).collect();`.', 'From `numeri` get `let doppi: Vec<i32> = numeri.iter().map(|n| n * 2).collect();`.'), 'let numeri = vec![1, 2, 3];
', [re('let\s+doppi\s*:\s*Vec<i32>\s*=\s*numeri\.iter\s*\(\s*\)', L('Parti dall’iteratore', 'You start from the iterator')), re('\.map\s*\(\s*\|n\|\s*n\s*\*\s*2\s*\)', L('Raddoppi', 'You double')), re('\.collect\s*\(\s*\)', L('Raccogli', 'You collect'))], '', 'let doppi: Vec<i32> = numeri.iter().map(|n| n * 2).collect();'), [
            dOrder(pSort(), ['let parole = vec!["sole", "re", "mare"];', 'let lunghe: Vec<&str> = parole', '    .into_iter()', '    .filter(|p| p.len() > 2)', '    .collect();'], L('Dati, iteratore, filtro, raccolta.', 'Data, iterator, filter, collect.')),
            dFlash(pFlash(), [[L('Gli iteratori sono veloci come i cicli', 'Iterators are as fast as loops'), true], [L('`HashMap` mantiene l’ordine di inserimento', '`HashMap` keeps insertion order'), false], [L('`push` aggiunge in fondo a un Vec', '`push` appends to a Vec'), true]], L('L’ordine di una HashMap non è garantito.', 'A HashMap’s order is not guaranteed.')),
        ]),
        lesson('traits', L('Trait e generics', 'Traits and generics'), [
            P('Un **trait** descrive un comportamento che i tipi possono implementare, come un’interfaccia. Le funzioni **generiche** accettano qualsiasi tipo che implementi un trait: `fn stampa<T: Display>(x: T)`.', 'A **trait** describes a behaviour types can implement, like an interface. **Generic** functions accept any type that implements a trait: `fn stampa<T: Display>(x: T)`.'),
            C('trait Descrivi {
    fn descrizione(&self) -> String;
    fn breve(&self) -> String { self.descrizione().chars().take(10).collect() }
}

struct Corso { titolo: String }

impl Descrivi for Corso {
    fn descrizione(&self) -> String { format!("Corso di {}", self.titolo) }
}

fn mostra(x: &impl Descrivi) {
    println!("{}", x.descrizione());
}', 'rust'),
            P('Un trait può avere metodi già pronti (come `breve`). `#[derive(Debug, Clone, PartialEq)]` implementa da solo i trait più comuni.', 'A trait can have ready-made methods (like `breve`). `#[derive(Debug, Clone, PartialEq)]` implements the most common traits automatically.'),
        ], [
            Q('t1', L('Come implementi un trait per un tipo?', 'How do you implement a trait for a type?'), ['impl Trait for Tipo', 'Tipo: Trait', 'Tipo implements Trait', 'trait Tipo'], 0, L('Con un blocco `impl … for …`.', 'With an `impl … for …` block.')),
            Q('t2', L('Che cosa significa `<T: Display>`?', 'What does `<T: Display>` mean?'), [L('T deve implementare Display', 'T must implement Display'), L('T è una stringa', 'T is a string'), L('T si stampa sempre', 'T always prints'), L('Niente', 'Nothing')], 0, L('È un vincolo sul tipo.', 'It is a bound on the type.')),
            Q('t3', L('Un trait può avere metodi con codice?', 'Can a trait have methods with code?'), [L('Sì, metodi predefiniti', 'Yes, default methods'), L('No, mai', 'No, never'), L('Solo statici', 'Only static ones'), L('Solo uno', 'Only one')], 0, L('Chi lo implementa può sovrascriverli.', 'Implementers can override them.')),
        ], task('rust', L('Implementa `impl Saluta for Gatto` con `fn saluta(&self) -> String { String::from("Miao") }`.', 'Implement `impl Saluta for Gatto` with `fn saluta(&self) -> String { String::from("Miao") }`.'), 'trait Saluta { fn saluta(&self) -> String; }
struct Gatto;
', [re('impl\s+Saluta\s+for\s+Gatto\s*\{', L('Implementi il trait', 'You implement the trait')), re('fn\s+saluta\s*\(\s*&self\s*\)\s*->\s*String\s*\{', L('Il metodo ha un corpo', 'The method has a body'))], '', 'impl Saluta for Gatto {
    fn saluta(&self) -> String { String::from("Miao") }
}'), [
            dBlanks(pFill(), "___ Area { fn area(&self) -> f64; }\nimpl Area ___ Quadrato {\n    fn area(&self) -> f64 { self.lato * self.lato }\n}\nfn stampa<T: ___>(x: &T) { println!(\"{}\", x.area()); }", ['trait', 'for', 'Area'], ['interface', 'with', 'impl'], L('Definisci, implementa, vincola.', 'Define, implement, bound.')),
            dMatch(pMatch(), [['trait', L('Comportamento comune', 'Shared behaviour')], ['impl … for', L('Lo implementa', 'Implements it')], ['<T: Trait>', L('Generico vincolato', 'Bounded generic')], ['derive', L('Implementazione automatica', 'Automatic implementation')]], L('Trait e generics.', 'Traits and generics.')),
        ]),
    ],
];
