<?php
/** Rust intermediate: structs and impl, enums and match, Option and Result, collections, traits, custom errors, trait objects and closures. */

/** How a Forma value sits in memory: a tag saying the variant, then room for the largest variant's data. */
if (!function_exists('fig_rustint_enum')) {
    function fig_rustint_enum(string $note): string
    {
        $row = function (int $y, string $name, string $tag, array $slots) {
            $t = $y + 26;
            $s = "<text x=\"166\" y=\"$t\" text-anchor=\"end\" font-weight=\"700\">$name</text><rect x=\"180\" y=\"$y\" width=\"70\" height=\"40\" rx=\"8\" fill=\"#ECE6FF\" stroke=\"#7048E8\" stroke-width=\"2\"/><text x=\"215\" y=\"$t\" text-anchor=\"middle\" font-size=\"14\">tag $tag</text>";
            foreach ($slots as $i => $v) {
                $x = 260 + $i * 170;
                $s .= $v === ''
                    ? "<rect x=\"$x\" y=\"$y\" width=\"160\" height=\"40\" rx=\"8\" fill=\"#FFFFFF\" stroke=\"#8A87B0\" stroke-width=\"2\" stroke-dasharray=\"5 4\"/>"
                    : "<rect x=\"$x\" y=\"$y\" width=\"160\" height=\"40\" rx=\"8\" fill=\"#FFF3EA\" stroke=\"#E8590C\" stroke-width=\"2\"/><text x=\"" . ($x + 80) . "\" y=\"$t\" text-anchor=\"middle\" font-size=\"14\">$v</text>";
            }
            return $s . "\n";
        };
        $rows = $row(20, 'Cerchio', '0', ['r: f64', '']) . $row(76, 'Rettangolo', '1', ['base: f64', 'altezza: f64']) . $row(132, 'Punto', '2', ['', '']);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
{$rows}<text x="320" y="200" text-anchor="middle" font-size="14" fill="#5F5C8A">{$note}</text>
</svg>
SVG;
    }
}

/** Option and Result side by side, then what ? does with each outcome of a Result. */
if (!function_exists('fig_rustint_optres')) {
    function fig_rustint_optres(string $ok, string $err): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 255" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="12" y="12" width="296" height="104" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="160" y="38" text-anchor="middle" font-weight="700">Option&lt;T&gt;</text>
<rect x="30" y="56" width="120" height="42" rx="21" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="90" y="82" text-anchor="middle">Some(4)</text>
<rect x="170" y="56" width="120" height="42" rx="21" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/><text x="230" y="82" text-anchor="middle" fill="#5F5C8A">None</text>
<rect x="332" y="12" width="296" height="104" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="480" y="38" text-anchor="middle" font-weight="700">Result&lt;T, E&gt;</text>
<rect x="350" y="56" width="120" height="42" rx="21" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="410" y="82" text-anchor="middle">Ok(36)</text>
<rect x="490" y="56" width="120" height="42" rx="21" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/><text x="550" y="82" text-anchor="middle">Err(e)</text>
<rect x="20" y="172" width="150" height="44" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="95" y="199" text-anchor="middle" font-weight="700">parse()?</text>
<g stroke-width="2" fill="none"><path d="M170 186 C200 186 200 160 224 160" stroke="#0B7F5C"/><path d="M216 154 L226 160 L216 166" stroke="#0B7F5C"/><path d="M170 202 C200 202 200 228 224 228" stroke="#F03E82"/><path d="M216 222 L226 228 L216 234" stroke="#F03E82"/></g>
<rect x="230" y="140" width="398" height="40" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="246" y="165" font-size="14"><tspan font-weight="700">Ok(v)</tspan> → {$ok}</text>
<rect x="230" y="208" width="398" height="40" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="246" y="233" font-size="14"><tspan font-weight="700">Err(e)</tspan> → {$err}</text>
</svg>
SVG;
    }
}

/** A Vec grows: full at cap 3, push(6) moves the items to a new buffer of capacity 6 and frees the old one. */
if (!function_exists('fig_rustint_vec')) {
    function fig_rustint_vec(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="20" y="34" font-weight="700">{$t[0]}</text>
<g font-size="13" text-anchor="middle"><rect x="20" y="48" width="62" height="40" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="51" y="73">ptr</text><rect x="86" y="48" width="62" height="40" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="117" y="73">len 3</text><rect x="152" y="48" width="62" height="40" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="183" y="73">cap 3</text></g>
<g stroke="#2F8CF0" stroke-width="2" fill="none"><path d="M51 88 C51 116 270 116 298 84"/><path d="M286 88 L300 82 L297 96"/></g>
<g fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"><rect x="300" y="48" width="44" height="40" rx="6"/><rect x="348" y="48" width="44" height="40" rx="6"/><rect x="396" y="48" width="44" height="40" rx="6"/></g>
<g text-anchor="middle" font-weight="700" fill="#5F5C8A"><text x="322" y="73">7</text><text x="370" y="73">8</text><text x="418" y="73">9</text></g>
<text x="452" y="73" font-size="13" fill="#5F5C8A">{$t[1]}</text>
<g stroke="#E8590C" stroke-width="2" fill="none" stroke-dasharray="4 3"><path d="M370 92 V150"/></g><path d="M363 142 L370 154 L377 142" stroke="#E8590C" stroke-width="2" fill="none"/>
<text x="382" y="128" font-size="13" font-weight="700" fill="#B84508">{$t[2]}</text>
<text x="20" y="150" font-weight="700">{$t[3]}</text>
<g font-size="13" text-anchor="middle"><rect x="20" y="164" width="62" height="40" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="51" y="189">ptr</text><rect x="86" y="164" width="62" height="40" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="117" y="189">len 4</text><rect x="152" y="164" width="62" height="40" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="183" y="189">cap 6</text></g>
<g stroke="#2F8CF0" stroke-width="2" fill="none"><path d="M51 204 C51 236 260 236 296 202"/><path d="M285 207 L299 200 L294 214"/></g>
<g fill="#FFF3EA" stroke="#E8590C" stroke-width="2"><rect x="300" y="164" width="44" height="40" rx="6"/><rect x="348" y="164" width="44" height="40" rx="6"/><rect x="396" y="164" width="44" height="40" rx="6"/><rect x="444" y="164" width="44" height="40" rx="6"/></g>
<g fill="#FFFFFF" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"><rect x="492" y="164" width="44" height="40" rx="6"/><rect x="540" y="164" width="44" height="40" rx="6"/></g>
<g text-anchor="middle" font-weight="700"><text x="322" y="189">7</text><text x="370" y="189">8</text><text x="418" y="189">9</text><text x="466" y="189" fill="#B84508">6</text></g>
</svg>
SVG;
    }
}

/** Box<dyn Forma> is a fat pointer: one half points at the data, the other at the type's table of methods. */
if (!function_exists('fig_rustint_dyn')) {
    function fig_rustint_dyn(string $data): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="20" y="20" font-size="13" font-weight="700" fill="#5F5C8A">forme: Vec&lt;Box&lt;dyn Forma&gt;&gt;</text>
<g font-size="14" text-anchor="middle">
<rect x="20" y="42" width="90" height="44" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="65" y="69">{$data}</text>
<rect x="114" y="42" width="90" height="44" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="159" y="69">vtable</text>
<rect x="20" y="158" width="90" height="44" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="65" y="185">{$data}</text>
<rect x="114" y="158" width="90" height="44" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="159" y="185">vtable</text>
</g>
<rect x="290" y="16" width="170" height="40" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="375" y="41" text-anchor="middle" font-size="14">Cerchio { r: 1.0 }</text>
<rect x="290" y="132" width="170" height="40" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="375" y="157" text-anchor="middle" font-size="14">Quadrato { lato: 2.0 }</text>
<rect x="476" y="70" width="152" height="50" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="552" y="90" text-anchor="middle" font-size="13" font-weight="700">vtable Cerchio</text><text x="552" y="110" text-anchor="middle" font-size="13">area → Cerchio::area</text>
<rect x="476" y="186" width="152" height="50" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="552" y="206" text-anchor="middle" font-size="13" font-weight="700">vtable Quadrato</text><text x="552" y="226" text-anchor="middle" font-size="13">area → Quadrato::area</text>
<g stroke-width="2" fill="none">
<path d="M65 42 C65 36 200 36 286 36" stroke="#2F8CF0"/><path d="M276 30 L288 36 L276 42" stroke="#2F8CF0"/>
<path d="M204 72 C300 76 400 95 472 95" stroke="#7048E8"/><path d="M462 89 L474 95 L462 101" stroke="#7048E8"/>
<path d="M65 158 C65 136 200 152 286 152" stroke="#2F8CF0"/><path d="M276 146 L288 152 L276 158" stroke="#2F8CF0"/>
<path d="M204 188 C300 192 400 211 472 211" stroke="#7048E8"/><path d="M462 205 L474 211 L462 217" stroke="#7048E8"/>
</g>
</svg>
SVG;
    }
}

return [
    'id' => 'rust-intermediate', 'family' => 'rust', 'after' => 'rust-base',
    'title' => L('Rust intermedio', 'Intermediate Rust'), 'short' => L('Rust intermedio', 'Rust Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Struct e metodi, enum e `match`, `Option` e `Result`, collezioni, trait, errori su misura, trait object e closure.', 'Structs and methods, enums and `match`, `Option` and `Result`, collections, traits, custom errors, trait objects and closures.'),
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
println!("{:?}", c);', 'rust', '<pre>Conto { titolare: "Ada", saldo: 50.0 }</pre>'),
            P('`#[derive(Debug)]` fa scrivere a Rust il codice per stampare la struct con `{:?}`.', '`#[derive(Debug)]` makes Rust write the code to print the struct with `{:?}`.'),
            P('Il primo parametro dice che cosa fa il metodo con l’istanza: `&self` la legge, `&mut self` la modifica (e la variabile deve essere `let mut`, come `c`), `self` la consuma, e dopo la chiamata non si può più usare. Rust non ha ereditarietà tra struct: il comportamento comune si condivide con i trait, che vedrai fra poco. Dentro `impl`, `Self` è un modo breve per dire `Conto`.', 'The first parameter says what the method does with the instance: `&self` reads it, `&mut self` changes it (and the variable must be `let mut`, like `c`), `self` consumes it, and after the call it can no longer be used. Rust has no inheritance between structs: shared behaviour goes into traits, which you will see shortly. Inside `impl`, `Self` is a short way of saying `Conto`.'),
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
        Forma::Cerchio(r) => std::f64::consts::PI * r * r,
        Forma::Rettangolo { base, altezza } => base * altezza,
        Forma::Punto => 0.0,
    }
}', 'rust'),
            P('`_` nel `match` cattura tutti i casi rimanenti. `if let` è comodo quando ti interessa una sola variante.', '`_` in `match` catches all remaining cases. `if let` is handy when you care about just one variant.'),
            P('Un enum con dati evita le combinazioni che non dovrebbero esistere: un `Cerchio` ha il raggio, un `Rettangolo` base e altezza, e non c’è modo di creare un cerchio con l’altezza. Il `match` è un’espressione: ogni ramo restituisce un valore dello stesso tipo, e i nomi tra parentesi (`r`, `base`) estraggono i dati della variante. Se aggiungi una variante, ogni `match` che non la gestisce smette di compilare.', 'An enum with data rules out combinations that should not exist: a `Cerchio` has a radius, a `Rettangolo` a base and a height, and there is no way to build a circle with a height. `match` is an expression: every arm returns a value of the same type, and the names in brackets (`r`, `base`) extract the variant’s data. If you add a variant, every `match` that does not handle it stops compiling.'),
            Fig(L(fig_rustint_enum('ogni Forma occupa lo spazio della variante più grande, più il tag'), fig_rustint_enum('every Forma takes the space of its largest variant, plus the tag')), L('Tre righe della stessa lunghezza. Cerchio: tag 0, r, e uno spazio vuoto. Rettangolo: tag 1, base e altezza. Punto: tag 2 e due spazi vuoti.', 'Three rows of the same length. Cerchio: tag 0, r, and an empty slot. Rettangolo: tag 1, base and altezza. Punto: tag 2 and two empty slots.'), L('Il tag dice quale variante c’è: è ciò che `match` legge.', 'The tag says which variant is there: it is what `match` reads.')),
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
            dFlash(pFlash(), [[L('`match` deve coprire tutte le varianti', '`match` must cover every variant'), true], [L('Un enum può avere una sola variante con dati', 'An enum can have only one variant with data'), false], [L('`if let` guarda una sola variante', '`if let` looks at a single variant'), true], [L('`Option` è un enum con `Some` e `None`', '`Option` is an enum with `Some` and `None`'), true]], L('Ogni variante può portare dati.', 'Every variant can carry data.')),
        ]),
        lesson('option-result', L('Option e Result', 'Option and Result'), [
            P('Rust non ha `null`: un valore che può mancare è un `Option<T>` (`Some(valore)` o `None`). Un’operazione che può fallire restituisce un `Result<T, E>` (`Ok` o `Err`). L’operatore `?` passa l’errore a chi ha chiamato.', 'Rust has no `null`: a value that may be missing is an `Option<T>` (`Some(value)` or `None`). An operation that may fail returns a `Result<T, E>` (`Ok` or `Err`). The `?` operator passes the error up to the caller.'),
            C(L('fn primo_pari(v: &[i32]) -> Option<i32> {
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
}', 'fn primo_pari(v: &[i32]) -> Option<i32> {
    v.iter().copied().find(|n| n % 2 == 0)
}

fn leggi_eta(testo: &str) -> Result<u8, std::num::ParseIntError> {
    let eta: u8 = testo.trim().parse()?;
    Ok(eta)
}

let x = primo_pari(&[1, 3, 4]).unwrap_or(0);
match leggi_eta("36") {
    Ok(e) => println!("{e} years"),
    Err(err) => println!("Error: {err}"),
}'), 'rust', L('<pre>36 anni</pre>', '<pre>36 years</pre>')),
            P('`unwrap()` estrae il valore ma fa crashare il programma se c’è `None` o `Err`: meglio `match`, `unwrap_or` o `?`.', '`unwrap()` extracts the value but crashes the program on `None` or `Err`: better `match`, `unwrap_or` or `?`.'),
            Fig(L(fig_rustint_optres('la funzione continua con v', 'esce subito: return Err(e) a chi ha chiamato'), fig_rustint_optres('the function carries on with v', 'leaves at once: return Err(e) to the caller')), L('In alto due riquadri: Option, che vale Some(4) oppure None, e Result, che vale Ok(36) oppure Err(e). Sotto, parse() con il punto di domanda ha due uscite: con Ok(v) la funzione continua con v, con Err(e) esce subito restituendo l’errore a chi l’ha chiamata.', 'At the top two panels: Option, which is Some(4) or None, and Result, which is Ok(36) or Err(e). Below, parse() with the question mark has two exits: with Ok(v) the function carries on with v, with Err(e) it leaves at once, returning the error to its caller.'), L('`?` funziona solo in una funzione che restituisce anch’essa un `Result` (o un `Option`).', '`?` only works in a function that itself returns a `Result` (or an `Option`).')),
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
            P('Quando un `Vec` è pieno, `push` chiede un blocco di memoria più grande, ci copia gli elementi e libera il vecchio. Per questo un riferimento a un elemento non può restare valido mentre aggiungi: `let primo = &voti[0]; voti.push(1);` non compila se poi usi `primo`, perché `primo` punterebbe a memoria liberata. Se sai quanti elementi arriveranno, `Vec::with_capacity(n)` evita le copie. E per un indice incerto usa `voti.get(10)`, che restituisce `None` invece di andare in panic.', 'When a `Vec` is full, `push` asks for a bigger block of memory, copies the items into it and frees the old one. That is why a reference to an item cannot stay valid while you add: `let primo = &voti[0]; voti.push(1);` does not compile if you use `primo` afterwards, because `primo` would point at freed memory. If you know how many items are coming, `Vec::with_capacity(n)` avoids the copies. And for an uncertain index use `voti.get(10)`, which returns `None` instead of panicking.'),
            Fig(L(fig_rustint_vec(['prima di push(6)', 'poi liberato', 'copiati', 'dopo push(6)']), fig_rustint_vec(['before push(6)', 'then freed', 'copied', 'after push(6)'])), L('Prima: il Vec ha ptr, len 3 e cap 3 e punta a un blocco pieno con 7, 8, 9. Dopo push(6): ptr, len 4 e cap 6, e punta a un blocco nuovo con 7, 8, 9, 6 e due posti liberi; il blocco vecchio, tratteggiato, viene liberato.', 'Before: the Vec has ptr, len 3 and cap 3 and points to a full block with 7, 8, 9. After push(6): ptr, len 4 and cap 6, and it points to a new block with 7, 8, 9, 6 and two free slots; the old block, dashed, is freed.'), L('Lo spostamento in un blocco nuovo è il motivo per cui il compilatore protegge i riferimenti.', 'The move to a new block is why the compiler guards references.')),
        ], [
            Q('c1', L('Come crei un Vec con valori?', 'How do you create a Vec with values?'), ['vec![1, 2, 3]', 'Vec(1, 2, 3)', '[1, 2, 3].vec', 'new Vec(1, 2, 3)'], 0, L('È una macro.', 'It is a macro.')),
            Q('c2', L('Che cosa fa `collect()`?', 'What does `collect()` do?'), [L('Raccoglie l’iteratore in una collezione', 'Gathers the iterator into a collection'), L('Pulisce la memoria', 'Cleans memory'), L('Conta', 'Counts'), L('Ordina', 'Sorts')], 0, L('Il tipo di destinazione va indicato.', 'The target type must be given.')),
            Q('c3', L('Come si scrive una closure?', 'How do you write a closure?'), ['|x| x * 2', 'x => x * 2', 'fn(x) x * 2', 'lambda x: x * 2'], 0, L('Tra barre verticali.', 'Between vertical bars.')),
        ], task('rust', L('Da `numeri` ottieni `let doppi: Vec<i32> = numeri.iter().map(|n| n * 2).collect();`.', 'From `numeri` get `let doppi: Vec<i32> = numeri.iter().map(|n| n * 2).collect();`.'), 'let numeri = vec![1, 2, 3];
', [re('let\s+doppi\s*:\s*Vec<i32>\s*=\s*numeri\.iter\s*\(\s*\)', L('Parti dall’iteratore', 'You start from the iterator')), re('\.map\s*\(\s*\|n\|\s*n\s*\*\s*2\s*\)', L('Raddoppi', 'You double')), re('\.collect\s*\(\s*\)', L('Raccogli', 'You collect'))], '', 'let doppi: Vec<i32> = numeri.iter().map(|n| n * 2).collect();'), [
            dOrder(pSort(), ['let parole = vec!["sole", "re", "mare"];', 'let lunghe: Vec<&str> = parole', '    .into_iter()', '    .filter(|p| p.len() > 2)', '    .collect();'], L('Dati, iteratore, filtro, raccolta.', 'Data, iterator, filter, collect.')),
            dFlash(pFlash(), [[L('Gli iteratori sono veloci come i cicli', 'Iterators are as fast as loops'), true], [L('`HashMap` mantiene l’ordine di inserimento', '`HashMap` keeps insertion order'), false], [L('`push` aggiunge in fondo a un Vec', '`push` appends to a Vec'), true], [L('Un `Vec` può cambiare lunghezza', 'A `Vec` can change length'), true]], L('L’ordine di una HashMap non è garantito.', 'A HashMap’s order is not guaranteed.')),
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
            P('I trait sono il modo in cui Rust condivide comportamento senza ereditarietà: `Corso` non eredita nulla, dichiara soltanto di saper fare `Descrivi`. Puoi implementare i tuoi trait per tipi esistenti e i trait della libreria standard per i tuoi tipi: implementando `Display`, per esempio, una struct si stampa con `{}`. Il compilatore crea una versione di `mostra` per ogni tipo usato, quindi i generics non costano nulla mentre il programma gira.', 'Traits are how Rust shares behaviour without inheritance: `Corso` inherits nothing, it only declares that it can do `Descrivi`. You can implement your own traits for existing types and the standard library’s traits for your types: by implementing `Display`, for example, a struct prints with `{}`. The compiler creates one version of `mostra` per type used, so generics cost nothing while the program runs.'),
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
        lesson('errors', L('Errori su misura', 'Custom errors'), [
            P('Nei programmi veri conviene un **tipo di errore** tuo, di solito un enum con una variante per ogni problema. Implementando `Display` e `std::error::Error` diventa un errore come gli altri; implementando `From`, l’operatore `?` converte da solo gli errori delle librerie nel tuo.', 'In real programs it pays to have your own **error type**, usually an enum with one variant per problem. Implementing `Display` and `std::error::Error` makes it an error like any other; implementing `From` lets the `?` operator convert library errors into yours automatically.'),
            C('use std::fmt;
use std::num::ParseIntError;

#[derive(Debug)]
enum ErroreConfig {
    Mancante(String),
    NonNumero(ParseIntError),
}

impl fmt::Display for ErroreConfig {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            ErroreConfig::Mancante(k) => write!(f, "manca la chiave {k}"),
            ErroreConfig::NonNumero(e) => write!(f, "non è un numero: {e}"),
        }
    }
}

impl std::error::Error for ErroreConfig {}

impl From<ParseIntError> for ErroreConfig {
    fn from(e: ParseIntError) -> Self { ErroreConfig::NonNumero(e) }
}

fn porta(valore: Option<&str>) -> Result<u16, ErroreConfig> {
    let testo = valore.ok_or(ErroreConfig::Mancante("porta".into()))?;
    Ok(testo.parse::<u16>()?)       // ParseIntError diventa ErroreConfig
}', 'rust'),
            P('Nelle applicazioni si usa spesso `Box<dyn std::error::Error>` per accettare qualsiasi errore: `fn main() -> Result<(), Box<dyn Error>>`. Le crate **thiserror** (per le librerie) e **anyhow** (per le applicazioni) scrivono gran parte di questo codice al posto tuo.', 'In applications you often use `Box<dyn std::error::Error>` to accept any error: `fn main() -> Result<(), Box<dyn Error>>`. The **thiserror** (for libraries) and **anyhow** (for applications) crates write most of this code for you.'),
        ], [
            Q('er1', L('Perché implementare `From<ParseIntError>` per il tuo errore?', 'Why implement `From<ParseIntError>` for your error?'), [L('Per stampare l’errore a colori', 'To print the error in colour'), L('Perché `?` converta da solo ParseIntError nel tuo tipo', 'So that `?` converts ParseIntError into your type by itself'), L('Per non dover scrivere `Result`', 'So you need not write `Result`'), L('Per trasformare l’errore in un panic', 'To turn the error into a panic')], 1, L('`?` chiama `From::from` sull’errore.', '`?` calls `From::from` on the error.')),
            Q('er2', L('Che cosa accetta `Box<dyn Error>`?', 'What does `Box<dyn Error>` accept?'), [L('Solo gli errori di I/O', 'Only I/O errors'), L('Solo il tuo enum', 'Only your enum'), L('Qualsiasi tipo che implementa il trait Error', 'Any type implementing the Error trait'), L('Nessun errore: è un tipo vuoto', 'No error: it is an empty type')], 2, L('Comodo, ma chi chiama sa meno sull’errore.', 'Handy, but callers learn less about the error.')),
            Q('er3', L('Quale crate è pensata per definire errori nelle librerie?', 'Which crate is meant for defining errors in libraries?'), ['serde', 'tokio', 'anyhow', 'thiserror'], 3, L('`anyhow` è pensata per le applicazioni.', '`anyhow` is meant for applications.')),
        ], task('rust', L('Fai restituire a `main` un `Result`: `fn main() -> Result<(), Box<dyn std::error::Error>>`, con `Ok(())` alla fine.', 'Make `main` return a `Result`: `fn main() -> Result<(), Box<dyn std::error::Error>>`, with `Ok(())` at the end.'), 'fn main() {
    let n: i32 = "42".parse()?;
    println!("{n}");
}', [re('fn\s+main\s*\(\s*\)\s*->\s*Result\s*<\s*\(\s*\)\s*,\s*Box\s*<\s*dyn\s+(std::error::)?Error\s*>\s*>', L('main restituisce un Result', 'main returns a Result')), re('Ok\s*\(\s*\(\s*\)\s*\)', L('Finisci con Ok(())', 'You end with Ok(())'))], '', 'fn main() -> Result<(), Box<dyn std::error::Error>> {
    let n: i32 = "42".parse()?;
    println!("{n}");
    Ok(())
}'), [
            dBlanks(pFill(), "impl ___<std::io::Error> for MioErrore {\n    fn from(e: std::io::Error) -> Self { MioErrore::Io(e) }\n}\nfn leggi() -> Result<String, MioErrore> {\n    let t = std::fs::read_to_string(\"a.txt\")___;\n    ___(t)\n}", ['From', '?', 'Ok'], ['Into', '!', 'Some'], L('Conversione, propagazione, successo.', 'Conversion, propagation, success.')),
            dMatch(pMatch(), [['From', L('Converte un errore in un altro', 'Converts one error into another')], ['Display', L('Testo leggibile dell’errore', 'Readable error text')], ['Box<dyn Error>', L('Qualsiasi errore', 'Any error')], ['thiserror', L('Errori per librerie', 'Errors for libraries')], ['anyhow', L('Errori per applicazioni', 'Errors for applications')]], L('Gestire gli errori in grande.', 'Handling errors at scale.')),
        ]),
        lesson('trait-objects', L('Trait object e dyn', 'Trait objects and dyn'), [
            P('Con i generics il compilatore crea una versione del codice per ogni tipo (**dispatch statico**). A volte però servono tipi diversi nella stessa collezione: allora si usa un **trait object**, `Box<dyn Trait>` o `&dyn Trait`, e il metodo giusto si sceglie mentre il programma gira (**dispatch dinamico**).', 'With generics the compiler creates one version of the code per type (**static dispatch**). Sometimes, though, you need different types in the same collection: then you use a **trait object**, `Box<dyn Trait>` or `&dyn Trait`, and the right method is chosen while the program runs (**dynamic dispatch**).'),
            C('trait Forma {
    fn area(&self) -> f64;
}

struct Cerchio { r: f64 }
struct Quadrato { lato: f64 }

impl Forma for Cerchio {
    fn area(&self) -> f64 { std::f64::consts::PI * self.r * self.r }
}
impl Forma for Quadrato {
    fn area(&self) -> f64 { self.lato * self.lato }
}

let forme: Vec<Box<dyn Forma>> = vec![
    Box::new(Cerchio { r: 1.0 }),
    Box::new(Quadrato { lato: 2.0 }),
];
let totale: f64 = forme.iter().map(|f| f.area()).sum();

fn stampa_area(f: &dyn Forma) { println!("{:.2}", f.area()); }', 'rust'),
            P('`impl Trait` e i generics sono più veloci ma vogliono un solo tipo concreto per volta; `dyn Trait` è flessibile e costa una piccola indirezione. Non tutti i trait possono diventare `dyn`: per esempio non quelli con metodi generici.', '`impl Trait` and generics are faster but need one concrete type at a time; `dyn Trait` is flexible and costs a small indirection. Not every trait can become `dyn`: for example not those with generic methods.'),
            Fig(L(fig_rustint_dyn('dati'), fig_rustint_dyn('data')), L('Due Box<dyn Forma> nel Vec forme, ognuno fatto di due metà. La prima metà punta ai dati, Cerchio con r 1.0 oppure Quadrato con lato 2.0; la seconda alla vtable del tipo, dove area porta a Cerchio::area o a Quadrato::area.', 'Two Box<dyn Forma> in the forme Vec, each made of two halves. The first half points at the data, Cerchio with r 1.0 or Quadrato with lato 2.0; the second at the type’s vtable, where area leads to Cerchio::area or Quadrato::area.'), L('Il «puntatore grasso»: chiamare `area()` vuol dire seguire la vtable, la piccola indirezione del dispatch dinamico.', 'The “fat pointer”: calling `area()` means following the vtable, the small indirection of dynamic dispatch.')),
        ], [
            Q('to1', L('Come metti un Cerchio e un Quadrato nello stesso Vec?', 'How do you put a Cerchio and a Quadrato in the same Vec?'), ['Vec<Forma>', 'Vec<impl Forma>', 'Vec<Box<dyn Forma>>', 'Vec<T: Forma>'], 2, L('Il Box dà a ogni elemento la stessa dimensione.', 'The Box gives every element the same size.')),
            Q('to2', L('Quando si sceglie il metodo da chiamare con `dyn Trait`?', 'When is the method to call chosen with `dyn Trait`?'), [L('Mentre il programma gira', 'While the program runs'), L('Durante la compilazione', 'During compilation'), L('Quando si scrive il Cargo.toml', 'When writing Cargo.toml'), L('Mai: dyn non chiama metodi', 'Never: dyn calls no methods')], 0, L('Attraverso una tabella di metodi, la vtable.', 'Through a table of methods, the vtable.')),
            Q('to3', L('Qual è il prezzo di `dyn Trait` rispetto ai generics?', 'What is the cost of `dyn Trait` compared with generics?'), [L('Il codice non compila in release', 'The code does not compile in release'), L('Non si possono usare riferimenti', 'References cannot be used'), L('Serve sempre `unsafe`', 'It always needs `unsafe`'), L('Una piccola indirezione a runtime', 'A small runtime indirection')], 3, L('Di solito trascurabile.', 'Usually negligible.')),
        ], task('rust', L('Crea `let animali: Vec<Box<dyn Verso>> = vec![Box::new(Cane), Box::new(Gatto)];`.', 'Create `let animali: Vec<Box<dyn Verso>> = vec![Box::new(Cane), Box::new(Gatto)];`.'), 'trait Verso { fn verso(&self) -> String; }
', [re('let\s+animali\s*:\s*Vec\s*<\s*Box\s*<\s*dyn\s+Verso\s*>\s*>\s*=', L('Il vettore contiene trait object', 'The vector holds trait objects')), re('Box::new\s*\(\s*Cane\s*\)', L('C’è il cane', 'There is the dog')), re('Box::new\s*\(\s*Gatto\s*\)', L('C’è il gatto', 'There is the cat'))], '', 'let animali: Vec<Box<dyn Verso>> = vec![Box::new(Cane), Box::new(Gatto)];'), [
            dMatch(pMatch(), [['impl Trait', L('Un tipo concreto, scelto dal compilatore', 'One concrete type, chosen by the compiler')], ['dyn Trait', L('Tipo deciso a runtime', 'Type decided at runtime')], ['Box<dyn Trait>', L('Trait object posseduto', 'Owned trait object')], ['&dyn Trait', L('Trait object in prestito', 'Borrowed trait object')]], L('Statico o dinamico.', 'Static or dynamic.')),
            dFlash(pFlash(), [[L('Con i generics il dispatch è statico', 'With generics dispatch is static'), true], [L('Un Vec<Box<dyn Forma>> può contenere tipi diversi', 'A Vec<Box<dyn Forma>> can hold different types'), true], [L('`dyn Trait` è più veloce dei generics', '`dyn Trait` is faster than generics'), false], [L('Ogni trait può diventare dyn', 'Every trait can become dyn'), false]], L('Flessibilità in cambio di un’indirezione.', 'Flexibility in exchange for an indirection.')),
        ]),
        lesson('closures', L('Closure e trait Fn', 'Closures and the Fn traits'), [
            P('Una **closure** può usare le variabili intorno a sé: le **cattura**. Secondo come le usa implementa uno dei tre trait: `Fn` (le legge), `FnMut` (le modifica), `FnOnce` (le consuma, quindi si chiama una volta sola). `move` obbliga la closure a prendere possesso di ciò che cattura.', 'A **closure** can use the variables around it: it **captures** them. Depending on how it uses them it implements one of three traits: `Fn` (reads them), `FnMut` (changes them), `FnOnce` (consumes them, so it can be called only once). `move` forces the closure to take ownership of what it captures.'),
            C('let soglia = 6;
let promosso = |v: &i32| *v >= soglia;            // Fn: legge soglia

let mut conta = 0;
let mut incrementa = || conta += 1;               // FnMut: modifica conta
incrementa();
incrementa();

let nome = String::from("Ada");
let saluta = move || println!("Ciao {nome}");     // possiede nome

fn applica<F: Fn(i32) -> i32>(f: F, x: i32) -> i32 { f(x) }
println!("{}", applica(|n| n * 10, 4));           // 40

fn crea_moltiplicatore(k: i32) -> impl Fn(i32) -> i32 {
    move |n| n * k
}', 'rust'),
            P('Le funzioni che accettano closure usano i trait come vincoli: `F: Fn(i32) -> i32`. Per restituire una closure si scrive `impl Fn(…)`; il `move` serve perché `k` vive solo dentro la funzione.', 'Functions that accept closures use the traits as bounds: `F: Fn(i32) -> i32`. To return a closure you write `impl Fn(…)`; `move` is needed because `k` only lives inside the function.'),
        ], [
            Q('cl1', L('Quale trait implementa una closure che modifica una variabile catturata?', 'Which trait does a closure that changes a captured variable implement?'), ['Fn', 'FnMut', 'FnOnce', 'Copy'], 1, L('E la closure va dichiarata `mut`.', 'And the closure must be declared `mut`.')),
            Q('cl2', L('Perché in `crea_moltiplicatore` serve `move`?', 'Why does `crea_moltiplicatore` need `move`?'), [L('Per rendere la closure più veloce', 'To make the closure faster'), L('Perché le closure non possono avere parametri', 'Because closures cannot have parameters'), L('Per poterla chiamare più volte', 'So it can be called several times'), L('Perché `k` sparisce alla fine della funzione e la closure deve possederlo', 'Because `k` disappears at the end of the function and the closure must own it')], 3, L('Altrimenti terrebbe un riferimento a un valore distrutto.', 'Otherwise it would hold a reference to a dropped value.')),
            Q('cl3', L('Quante volte si può chiamare una closure che è solo `FnOnce`?', 'How many times can you call a closure that is only `FnOnce`?'), [L('Una volta sola', 'Only once'), L('Due volte', 'Twice'), L('Quante vuoi', 'As many as you like'), L('Nessuna', 'None')], 0, L('Consuma ciò che ha catturato.', 'It consumes what it captured.')),
        ], task('rust', L('Scrivi `fn due_volte<F: Fn(i32) -> i32>(f: F, x: i32) -> i32` che restituisce `f(f(x))`.', 'Write `fn due_volte<F: Fn(i32) -> i32>(f: F, x: i32) -> i32` that returns `f(f(x))`.'), '', [re('fn\s+due_volte\s*<\s*F\s*:\s*Fn\s*\(\s*i32\s*\)\s*->\s*i32\s*>\s*\(\s*f\s*:\s*F\s*,\s*x\s*:\s*i32\s*\)\s*->\s*i32', L('La firma è giusta', 'The signature is right')), re('f\s*\(\s*f\s*\(\s*x\s*\)\s*\)', L('Applichi f due volte', 'You apply f twice'))], '', 'fn due_volte<F: Fn(i32) -> i32>(f: F, x: i32) -> i32 {
    f(f(x))
}'), [
            dMatch(pMatch(), [['Fn', L('Legge ciò che cattura', 'Reads what it captures')], ['FnMut', L('Modifica ciò che cattura', 'Changes what it captures')], ['FnOnce', L('Consuma ciò che cattura', 'Consumes what it captures')], ['move', L('Prende possesso delle variabili', 'Takes ownership of the variables')]], L('I tre trait delle closure.', 'The three closure traits.')),
            dBug(pBug(), ['let mut totale = 0;', 'let aggiungi = |n: i32| totale += n;', 'aggiungi(5);'], 1, 'let mut aggiungi = |n: i32| totale += n;', L('Una closure FnMut va dichiarata `mut` per poterla chiamare.', 'An FnMut closure must be declared `mut` to be called.')),
        ]),
    ],
];
