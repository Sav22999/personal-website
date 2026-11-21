<?php
/** TypeScript: JavaScript with types, from basics to discriminated unions, computed types and today's tooling. */

/** From TypeScript to running code: tsc checks the types and flags errors, then the types are erased and plain JavaScript runs. */
if (!function_exists('fig_ts_compile')) {
    function fig_ts_compile(string $l): string
    {
        $t = $l === 'it' ? ['controlla i tipi', 'tipi cancellati', 'esegue solo JS', 'errore prima di eseguire'] : ['checks the types', 'types erased', 'runs plain JS', 'error before running'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="tsca" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="30" width="124" height="104" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="176" y="30" width="124" height="104" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="340" y="30" width="124" height="104" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="504" y="30" width="124" height="104" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace"><text x="74" y="58">app.ts</text><text x="238" y="58">tsc</text><text x="402" y="58">app.js</text></g>
<text x="566" y="58" text-anchor="middle" font-weight="700" font-size="14">browser · Node</text>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12"><text x="74" y="92">let n: number</text><text x="74" y="110">= 3;</text><text x="402" y="92">let n</text><text x="402" y="110">= 3;</text></g>
<g text-anchor="middle" font-size="13" fill="#5B5880"><text x="238" y="100">{$t[0]}</text><text x="402" y="156">{$t[1]}</text><text x="566" y="100">{$t[2]}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#tsca)"><path d="M136 82H172"/><path d="M300 82H336"/><path d="M464 82H500"/><path d="M238 134V164"/></g>
<rect x="138" y="168" width="200" height="40" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="238" y="186" text-anchor="middle" font-size="12" font-weight="700" fill="#B0245A">✗ {$t[3]}</text>
<text x="238" y="202" text-anchor="middle" font-size="12" font-family="ui-monospace, Menlo, monospace" fill="#B0245A">n = "tanti"</text>
</svg>
SVG;
    }
}

/** Narrowing: a typeof check splits a union, and in each branch the type is precise. */
if (!function_exists('fig_ts_narrow')) {
    function fig_ts_narrow(string $l): string
    {
        $t = $l === 'it' ? ['vero', 'altrimenti', 'qui x è', 'il controllo restringe il tipo'] : ['true', 'otherwise', 'here x is', 'the check narrows the type'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="tsna" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="84" width="170" height="56" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="97" y="117" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12">x: string | string[]</text>
<rect x="214" y="84" width="196" height="56" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="312" y="117" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">typeof x === "string"</text>
<rect x="450" y="20" width="178" height="74" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="450" y="132" width="178" height="74" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g font-size="13" fill="#5B5880"><text x="464" y="44">{$t[2]}</text><text x="464" y="156">{$t[2]}</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-weight="700"><text x="464" y="68" fill="#1B66BC">string</text><text x="464" y="180" fill="#0B7F5C">string[]</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#5B5880"><text x="464" y="86">x.toUpperCase()</text><text x="464" y="198">x.join(", ")</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#tsna)"><path d="M182 112H210"/><path d="M410 102C430 102 430 58 446 58"/><path d="M410 122C430 122 430 168 446 168"/></g>
<g font-size="13" font-weight="700"><text x="398" y="60" text-anchor="end" fill="#1B66BC">{$t[0]}</text><text x="398" y="176" text-anchor="end" fill="#0B7F5C">{$t[1]}</text></g>
<text x="12" y="210" font-size="13" fill="#5B5880">{$t[3]}</text>
</svg>
SVG;
    }
}

/** A generic type parameter filled in at each call: the same function gives number for numbers and string for strings. */
if (!function_exists('fig_ts_generic')) {
    function fig_ts_generic(string $l): string
    {
        $t = $l === 'it' ? ['chiamata', 'T dedotto', 'risultato'] : ['call', 'T inferred', 'result'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="tsga" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="120" y="14" width="400" height="44" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="320" y="42" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="15">function primo&lt;<tspan fill="#7048E8" font-weight="700">T</tspan>&gt;(lista: <tspan fill="#7048E8" font-weight="700">T</tspan>[]): <tspan fill="#7048E8" font-weight="700">T</tspan></text>
<g text-anchor="middle" font-size="13" font-weight="700" fill="#5B5880"><text x="110" y="88">{$t[0]}</text><text x="330" y="88">{$t[1]}</text><text x="540" y="88">{$t[2]}</text></g>
<g stroke-width="2"><rect x="20" y="100" width="180" height="44" rx="10" fill="#FFFFFF" stroke="#8A87B0"/><rect x="20" y="164" width="180" height="44" rx="10" fill="#FFFFFF" stroke="#8A87B0"/>
<rect x="250" y="100" width="160" height="44" rx="10" fill="#ECE6FF" stroke="#7048E8"/><rect x="250" y="164" width="160" height="44" rx="10" fill="#ECE6FF" stroke="#7048E8"/>
<rect x="460" y="100" width="160" height="44" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="460" y="164" width="160" height="44" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="110" y="127">primo([1, 2])</text><text x="110" y="191">primo(["x"])</text><text x="330" y="127">T = number</text><text x="330" y="191">T = string</text><text x="540" y="127" font-weight="700">number</text><text x="540" y="191" font-weight="700">string</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#tsga)"><path d="M200 122H246"/><path d="M200 186H246"/><path d="M410 122H456"/><path d="M410 186H456"/></g>
</svg>
SVG;
    }
}

/** Computing types: typeof takes the type of a value, keyof turns its keys into a union. */
if (!function_exists('fig_ts_keyof')) {
    function fig_ts_keyof(string $l): string
    {
        $t = $l === 'it' ? ['valore', 'tipo', 'unione delle chiavi'] : ['value', 'type', 'union of the keys'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="tska" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g text-anchor="middle" font-size="13" font-weight="700" fill="#5B5880"><text x="100" y="40">{$t[0]}</text><text x="320" y="40">{$t[1]}</text><text x="540" y="40">{$t[2]}</text></g>
<rect x="12" y="52" width="176" height="96" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="232" y="52" width="176" height="96" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="452" y="72" width="176" height="56" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="24" y="76" font-weight="700">tema</text><text x="24" y="102"><tspan fill="#1B66BC" font-weight="700">chiaro</tspan>: "#ffffff"</text><text x="24" y="124"><tspan fill="#1B66BC" font-weight="700">scuro</tspan>: "#111111"</text>
<text x="244" y="76" font-weight="700">typeof tema</text><text x="244" y="102"><tspan fill="#1B66BC" font-weight="700">chiaro</tspan>: "#ffffff"</text><text x="244" y="124"><tspan fill="#1B66BC" font-weight="700">scuro</tspan>: "#111111"</text>
<text x="540" y="105" text-anchor="middle" font-weight="700" fill="#1B66BC">"chiaro" | "scuro"</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#tska)"><path d="M188 100H228"/><path d="M408 100H448"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#5A35C8"><text x="208" y="180">typeof</text><text x="428" y="180">keyof</text></g>
<g stroke="#8A87B0" stroke-width="1.5" stroke-dasharray="3 3"><path d="M208 108V164"/><path d="M428 108V164"/></g>
</svg>
SVG;
    }
}

return [
    'id' => 'ts', 'family' => 'ts', 'after' => 'js-intermediate',
    'title' => L('TypeScript', 'TypeScript'), 'short' => L('TypeScript', 'TypeScript'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('JavaScript con i tipi: errori trovati prima di eseguire, interfacce, unioni e generici, tipi calcolati e TypeScript con Node.', 'JavaScript with types: errors found before running, interfaces, unions and generics, computed types and TypeScript with Node.'),
    'lessons' => [
        lesson('types', L('Perché i tipi', 'Why types'), [
            P('**TypeScript** è JavaScript con i tipi. Dici che cosa contiene una variabile e il compilatore segnala gli errori prima ancora di eseguire. Il browser non lo legge: viene compilato in JavaScript (le versioni recenti di Node.js sanno eseguirlo togliendo i tipi, ma senza controllarli).', '**TypeScript** is JavaScript with types. You say what a variable holds and the compiler flags errors before anything runs. The browser does not read it: it is compiled to JavaScript (recent Node.js versions can run it by stripping the types, but without checking them).'),
            C('let nome: string = "Sav";
let punti: number = 10;
let attivo: boolean = true;
let voti: number[] = [7, 8];

punti = "tanti"; // Errore: non è un number
let vite = 3;    // tipo dedotto: number', 'ts'),
            P('Spesso il tipo non serve scriverlo: TypeScript lo **deduce** dal valore. `any` spegne i controlli: evitalo.', 'Often you need not write the type: TypeScript **infers** it from the value. `any` switches checks off: avoid it.'),
            Fig(L(fig_ts_compile('it'), fig_ts_compile('en')),
                L('Quattro riquadri in fila: app.ts con let n: number = 3, il compilatore tsc che controlla i tipi, app.js con let n = 3 senza tipi, il browser o Node che esegue JavaScript. Sotto tsc un riquadro rosa segnala l’errore n = "tanti" prima dell’esecuzione.', 'Four boxes in a row: app.ts with let n: number = 3, the tsc compiler checking the types, app.js with let n = 3 and no types, the browser or Node running JavaScript. Below tsc a pink box flags the error n = "tanti" before running.'),
                L('I tipi esistono solo prima dell’esecuzione: servono a trovare gli errori, poi spariscono.', 'Types exist only before running: they help find mistakes, then they disappear.')),
        ], [
            Q('t1', L('Quando TypeScript trova gli errori?', 'When does TypeScript find errors?'), [L('Prima di eseguire', 'Before running'), L('Solo in produzione', 'Only in production'), L('Mai', 'Never'), L('Dopo il deploy', 'After deploying')], 0, L('È il suo grande vantaggio.', 'That is its big advantage.')),
            Q('t2', L('Il browser esegue TypeScript?', 'Does the browser run TypeScript?'), [L('No, viene compilato', 'No, it is compiled'), L('Sì, sempre', 'Yes, always'), L('Solo Chrome', 'Only Chrome'), L('Solo in `<head>`', 'Only in `<head>`')], 0, L('Il risultato è JavaScript normale. Anche Node, quando avvia un `.ts`, toglie solo i tipi.', 'The result is plain JavaScript. Node too, when it starts a `.ts`, only strips the types.')),
            Q('t3', L('Che cosa fa `any`?', 'What does `any` do?'), [L('Spegne i controlli', 'Switches checks off'), L('Accetta solo numeri', 'Takes numbers only'), L('Crea un array', 'Creates an array'), L('Compila', 'Compiles')], 0, L('Usalo il meno possibile.', 'Use it as little as possible.')),
        ], task('ts', L('Dichiara `nome` di tipo `string` e `voti` come array di `number`.', 'Declare `nome` of type `string` and `voti` as an array of `number`.'), '', [re('nome\s*:\s*string', L('`nome` è una stringa', '`nome` is a string')), re('voti\s*:\s*number\[\]', L('`voti` è un array di numeri', '`voti` is an array of numbers'))], '', "let nome: string = \"Sav\";\nlet voti: number[] = [7, 8];"), [
            dBlanks(pFill(), "let nome___ string = \"Sav\";\nlet punti: ___ = 10;\nlet voti: number___ = [7];", [':', 'number', '[]'], ['=', 'int', '{}'], L('I due punti annunciano il tipo.', 'The colon announces the type.')),
            dBug(pBug(), ['let punti: number = 10;', 'let nome: string = 42;', 'let ok: boolean = true;'], 1, 'let nome: string = "42";', L('Un numero non è una stringa: TypeScript lo segnala subito.', 'A number is not a string: TypeScript flags it at once.')),
            dFlash(pFlash(), [[L('Ogni file JavaScript è quasi TypeScript valido', 'Every JavaScript file is almost valid TypeScript'), true], [L('I tipi esistono ancora a runtime', 'Types still exist at runtime'), false], [L('`let x = 1` ha tipo dedotto `number`', '`let x = 1` has inferred type `number`'), true], [L('`any` è la scelta più sicura', '`any` is the safest choice'), false]], L('I tipi spariscono alla compilazione: servono mentre scrivi.', 'Types vanish on compiling: they help while you write.')),
        ]),
        lesson('functions', L('Funzioni tipizzate', 'Typed functions'), [
            P('Si tipizzano parametri e risultato. Un parametro con `?` è facoltativo, uno con `=` ha un valore di default. `void` indica che una funzione non restituisce nulla.', 'You type parameters and the result. A parameter with `?` is optional, one with `=` has a default value. `void` means a function gives back nothing.'),
            C('function somma(a: number, b: number): number {
  return a + b;
}
function saluta(nome: string, titolo?: string): string {
  return titolo ? `${titolo} ${nome}` : nome;
}
const log = (testo: string): void => console.log(testo);', 'ts'),
            P('Il tipo del risultato si può dedurre, ma scriverlo è una buona abitudine: è un piccolo contratto.', 'The result type can be inferred, but writing it is a good habit: it is a small contract.'),
            P('Il vantaggio si vede a ogni chiamata: `somma(2, "3")` o `somma(2)` vengono segnalati subito, prima che arrivino agli utenti. I parametri facoltativi vanno sempre **dopo** quelli obbligatori. Anche una funzione passata come argomento ha un tipo: `(n: number) => string` descrive una funzione che riceve un numero e restituisce una stringa, utile per le callback.', 'The benefit shows at every call: `somma(2, "3")` or `somma(2)` are flagged at once, before they reach users. Optional parameters always go **after** the required ones. A function passed as an argument has a type too: `(n: number) => string` describes a function that takes a number and gives back a string, handy for callbacks.'),
        ], [
            Q('f1', L('Dove si scrive il tipo del risultato?', 'Where does the result type go?'), [L('Dopo le parentesi', 'After the brackets'), L('Prima del nome', 'Before the name'), L('Dentro le graffe', 'Inside the braces'), L('Nel `return`', 'In the `return`')], 0, L('`function f(): number`.', '`function f(): number`.')),
            Q('f2', L('Che cosa significa `titolo?: string`?', 'What does `titolo?: string` mean?'), [L('È facoltativo', 'It is optional'), L('È un dubbio', 'It is a doubt'), L('È privato', 'It is private'), L('È un errore', 'It is an error')], 0, L('Può essere `undefined`.', 'It may be `undefined`.')),
            Q('f3', L('Che cos’è `void`?', 'What is `void`?'), [L('Nessun risultato', 'No result'), L('Un numero', 'A number'), L('Un errore', 'An error'), L('Una classe', 'A class')], 0, L('Tipico delle funzioni che stampano o salvano.', 'Typical of functions that print or save.')),
        ], task('ts', L('Scrivi `doppio(n: number): number` che restituisce `n * 2`.', 'Write `doppio(n: number): number` that gives back `n * 2`.'), '', [re('doppio\s*\(\s*n\s*:\s*number\s*\)\s*:\s*number', L('Parametro e risultato hanno un tipo', 'Parameter and result have a type')), re('return\s+n\s*\*\s*2', L('Restituisci il doppio', 'You give back the double'))], '', "function doppio(n: number): number {\n  return n * 2;\n}"), [
            dBlanks(pFill(), "function area(b: ___, h: number)___ number {\n  return b * h;\n}\nfunction log(t?: string): ___ {}", ['number', ':', 'void'], ['int', '=>', 'null'], L('Tipi in ingresso, in uscita e `void` se non esce nulla.', 'Types in, types out, and `void` when nothing comes out.')),
            dBug(pBug(), ['function somma(a: number, b: number): number {', '  return `${a + b}`;', '}'], 1, '  return a + b;', L('Il template restituisce una stringa, ma la funzione promette un `number`.', 'The template gives back a string, but the function promises a `number`.')),
        ]),
        lesson('interfaces', L('Interfacce e type', 'Interfaces and type'), [
            P('Un’**interfaccia** descrive la forma di un oggetto. `?` rende una proprietà facoltativa, `readonly` la blocca. `type` dà un nome a un tipo qualsiasi.', 'An **interface** describes the shape of an object. `?` makes a property optional, `readonly` locks it. `type` gives a name to any type.'),
            C('interface Utente {
  readonly id: number;
  nome: string;
  email?: string;
}
type Punti = number;
const u: Utente = { id: 1, nome: "Sav" };

interface Admin extends Utente {
  ruolo: string;
}', 'ts'),
            P('Se manca una proprietà obbligatoria o ne scrivi una sbagliata, l’errore appare subito, e l’editor completa i nomi per te.', 'If a required property is missing or you write a wrong one, the error appears at once, and the editor completes the names for you.'),
            P('`interface` o `type`? Per descrivere oggetti vanno bene entrambi; l’`interface` si estende con `extends`, mentre `type` serve anche per unioni e altri tipi composti. TypeScript guarda la **forma**, non il nome: qualsiasi oggetto con `id` e `nome` del tipo giusto va bene come `Utente`, senza dichiararlo. Si chiama tipizzazione strutturale.', '`interface` or `type`? Both work to describe objects; an `interface` is extended with `extends`, while `type` also works for unions and other combined types. TypeScript looks at the **shape**, not the name: any object with an `id` and a `nome` of the right type is fine as a `Utente`, with no need to declare it. This is called structural typing.'),
        ], [
            Q('i1', L('Che cosa descrive un’interfaccia?', 'What does an interface describe?'), [L('La forma di un oggetto', 'The shape of an object'), L('Una pagina', 'A page'), L('Un colore', 'A colour'), L('Un ciclo', 'A loop')], 0, L('Quali chiavi ha e di che tipo.', 'Which keys it has and of what type.')),
            Q('i2', L('Che cosa fa `readonly`?', 'What does `readonly` do?'), [L('Impedisce di riassegnare', 'Prevents reassigning'), L('Nasconde', 'Hides'), L('Cancella', 'Deletes'), L('Copia', 'Copies')], 0, L('Il controllo avviene in compilazione.', 'The check happens at compile time.')),
            Q('i3', L('Come si estende un’interfaccia?', 'How do you extend an interface?'), ['extends', 'adds', 'with', 'plus'], 0, L('Eredita tutte le proprietà.', 'It inherits all the properties.')),
        ], task('ts', L('Crea l’interfaccia `Corso` con `titolo: string` e `livelli: number`.', 'Create the interface `Corso` with `titolo: string` and `livelli: number`.'), '', [re('interface\s+Corso', L('L’interfaccia esiste', 'The interface exists')), re('titolo\s*:\s*string', L('`titolo` è una stringa', '`titolo` is a string')), re('livelli\s*:\s*number', L('`livelli` è un numero', '`livelli` is a number'))], '', "interface Corso {\n  titolo: string;\n  livelli: number;\n}"), [
            dOrder(pSort(), ['interface Eroe {', '  nome: string;', '  vite: number;', '}', 'const e: Eroe = { nome: "Ada", vite: 3 };'], L('Prima la forma, poi l’oggetto che la rispetta.', 'First the shape, then the object that respects it.')),
            dBug(pBug(), ['interface Eroe {', '  nome: string;', '  vite: number;', '}', 'const e: Eroe = { nome: "Ada" };'], 4, 'const e: Eroe = { nome: "Ada", vite: 3 };', L('`vite` è obbligatoria: manca nell’oggetto.', '`vite` is required: it is missing from the object.')),
        ]),
        lesson('unions', L('Unioni e restringimento', 'Unions and narrowing'), [
            P('Un’**unione** accetta più tipi: `string | number`. Le unioni di valori creano scelte precise. Il **restringimento** usa un controllo per far capire a TypeScript quale tipo hai.', 'A **union** accepts several types: `string | number`. Unions of values create precise choices. **Narrowing** uses a check to tell TypeScript which type you have.'),
            C('type Stato = "attesa" | "fatto" | "errore";
let s: Stato = "fatto";

function lunghezza(x: string | string[]): number {
  if (typeof x === "string") {
    return x.length;  // qui x è string
  }
  return x.length;    // qui x è string[]
}
let nome: string | null = null;', 'ts'),
            Fig(L(fig_ts_narrow('it'), fig_ts_narrow('en')),
                L('Il valore x di tipo string | string[] passa per il controllo typeof x === "string". Se è vero, nel ramo x è string e si può usare toUpperCase; altrimenti x è string[] e si può usare join.', 'The value x of type string | string[] goes through the check typeof x === "string". If it is true, in that branch x is string and toUpperCase can be used; otherwise x is string[] and join can be used.'),
                L('Dopo il controllo TypeScript sa quale dei due tipi hai in mano.', 'After the check TypeScript knows which of the two types you are holding.')),
            P('Con `strictNullChecks` un valore che può essere `null` va controllato prima dell’uso: sparisce il classico errore «cannot read property of null».', 'With `strictNullChecks` a value that may be `null` must be checked before use: the classic “cannot read property of null” error disappears.'),
            Tip('Un’unione di valori come `Stato` è meglio di un semplice `string`: un errore di battitura come `"fato"` viene segnalato e l’editor ti propone le scelte possibili.', 'A union of values like `Stato` is better than a plain `string`: a typo like `"fato"` is flagged and the editor suggests the possible choices.'),
        ], [
            Q('u1', L('Che cosa significa `string | number`?', 'What does `string | number` mean?'), [L('Uno dei due', 'Either one'), L('Entrambi insieme', 'Both together'), L('Nessuno', 'Neither'), L('Un array', 'An array')], 0, L('La barra è «oppure».', 'The bar is “or”.')),
            Q('u2', L('Che cosa accetta `"a" | "b"`?', 'What does `"a" | "b"` accept?'), [L('Solo quei due testi', 'Only those two texts'), L('Ogni stringa', 'Any string'), L('Numeri', 'Numbers'), L('Niente', 'Nothing')], 0, L('Perfetto per stati e opzioni.', 'Perfect for states and options.')),
            Q('u3', L('Come si restringe un tipo?', 'How do you narrow a type?'), [L('Con un controllo come `typeof`', 'With a check like `typeof`'), L('Con un commento', 'With a comment'), L('Con `any`', 'With `any`'), L('Non si può', 'You cannot')], 0, L('Dentro l’`if` TypeScript sa di più.', 'Inside the `if` TypeScript knows more.')),
        ], task('ts', L('Crea il tipo `Tema` che accetta solo `"chiaro"` o `"scuro"`.', 'Create the type `Tema` that accepts only `"chiaro"` or `"scuro"`.'), '', [re('type\s+Tema\s*=', L('Il tipo esiste', 'The type exists')), re('"chiaro"\s*\|\s*"scuro"', L('Accetta i due valori', 'It accepts both values'))], '', "type Tema = \"chiaro\" | \"scuro\";"), [
            dBlanks(pFill(), "type Id = string ___ number;\nfunction f(x: Id) {\n  if (___ x === \"string\") {\n    return x.toUpperCase();\n  }\n}", ['|', 'typeof'], ['&', 'instanceof', '+'], L('Prima l’unione, poi il controllo che restringe.', 'First the union, then the check that narrows.')),
            dFlash(pFlash(), [[L('`typeof x === "string"` restringe il tipo', '`typeof x === "string"` narrows the type'), true], [L('`null` va controllato prima dell’uso', '`null` must be checked before use'), true], [L('`"a" | "b"` accetta `"c"`', '`"a" | "b"` accepts `"c"`'), false], [L('Le unioni funzionano solo con i numeri', 'Unions only work with numbers'), false]], L('Più il tipo è preciso, meno errori passano.', 'The more precise the type, the fewer errors slip through.')),
        ]),
        lesson('generics', L('Generici', 'Generics'), [
            P('Un **generico** è un tipo che fa da parametro: la stessa funzione lavora con tipi diversi senza perdere la precisione. Di solito si chiama `T`.', 'A **generic** is a type acting as a parameter: the same function works with different types without losing precision. It is usually called `T`.'),
            P('Perché non usare `any`? Con `any` la funzione accetta tutto ma restituisce `any`, e il controllo si perde. `T` invece **lega** ingresso e uscita: se passi una lista di numeri, TypeScript sa che otterrai un numero. Di solito non scrivi `T`: viene dedotto dagli argomenti. Con `<T extends { id: number }>` accetti solo tipi che abbiano almeno un `id`.', 'Why not use `any`? With `any` the function accepts everything but gives back `any`, and the checking is lost. `T` instead **links** input and output: if you pass a list of numbers, TypeScript knows you will get a number. You usually do not write `T`: it is inferred from the arguments. With `<T extends { id: number }>` you accept only types that have at least an `id`.'),
            Fig(L(fig_ts_generic('it'), fig_ts_generic('en')),
                L('In alto la firma function primo<T>(lista: T[]): T. Sotto due chiamate: primo([1, 2]) fa dedurre T = number e restituisce number; primo(["x"]) fa dedurre T = string e restituisce string.', 'At the top the signature function primo<T>(lista: T[]): T. Below two calls: primo([1, 2]) makes T = number and gives back number; primo(["x"]) makes T = string and gives back string.'),
                L('`T` è un segnaposto: ogni chiamata lo riempie con un tipo vero.', '`T` is a placeholder: every call fills it with a real type.')),
            C('function primo<T>(lista: T[]): T | undefined {
  return lista[0];
}
const a = primo([1, 2]);     // number
const b = primo(["x"]);      // string

interface Scatola<T> {
  contenuto: T;
}
const s: Scatola<number> = { contenuto: 5 };', 'ts'),
            P('Esistono tipi di utilità già pronti: `Partial<T>` rende tutto facoltativo, `Pick<T, K>` sceglie delle proprietà, `Record<K, V>` descrive una mappa.', 'Ready-made utility types exist: `Partial<T>` makes everything optional, `Pick<T, K>` picks properties, `Record<K, V>` describes a map.'),
        ], [
            Q('g1', L('Perché non usare `any` al posto di `T`?', 'Why not use `any` instead of `T`?'), [L('Si perderebbe il tipo', 'The type would be lost'), L('È più lento', 'It is slower'), L('Non compila', 'It does not compile'), L('È più lungo', 'It is longer')], 0, L('`T` mantiene il legame tra ingresso e uscita.', '`T` keeps the link between input and output.')),
            Q('g2', L('Che tipo ha `primo(["x"])`?', 'What type does `primo(["x"])` have?'), [L('`string` o `undefined`', '`string` or `undefined`'), L('`number`', '`number`'), L('`any`', '`any`'), L('`void`', '`void`')], 0, L('`T` viene dedotto come `string`.', '`T` is inferred as `string`.')),
            Q('g3', L('Che cosa fa `Partial<T>`?', 'What does `Partial<T>` do?'), [L('Rende tutto facoltativo', 'Makes everything optional'), L('Divide', 'Splits'), L('Copia', 'Copies'), L('Cancella', 'Deletes')], 0, L('Utile per gli aggiornamenti parziali.', 'Handy for partial updates.')),
        ], task('ts', L('Scrivi `ultimo<T>(lista: T[])` che restituisce l’ultimo elemento.', 'Write `ultimo<T>(lista: T[])` that gives back the last item.'), '', [re('ultimo\s*<\s*T\s*>\s*\(\s*lista\s*:\s*T\[\]\s*\)', L('La funzione è generica', 'The function is generic')), re('lista\[lista\.length\s*-\s*1\]|lista\.at\(-1\)', L('Restituisci l’ultimo', 'You give back the last one'))], '', "function ultimo<T>(lista: T[]): T | undefined {\n  return lista[lista.length - 1];\n}"), [
            dMatch(pMatch(), [['Partial<T>', L('Tutto facoltativo', 'All optional')], ['Pick<T, K>', L('Alcune proprietà', 'Some properties')], ['Omit<T, K>', L('Tutte tranne alcune', 'All but some')], ['Record<K, V>', L('Una mappa', 'A map')], ['Readonly<T>', L('Non modificabile', 'Not editable')]], L('Trasformano i tipi che hai già.', 'They transform the types you already have.')),
            dBlanks(pFill(), "function prendi___(lista: T[]): ___ {\n  return lista[0];\n}\nconst s: Scatola___ = { contenuto: 5 };", ['<T>', 'T', '<number>'], ['(T)', 'any', '[T]'], L('Si dichiara `T`, lo si usa, e si sceglie il tipo concreto.', 'Declare `T`, use it, and choose the concrete type.')),
        ]),
        lesson('tooling', L('Il compilatore e le classi', 'The compiler and classes'), [
            P('`tsconfig.json` regola il compilatore: `strict` attiva tutti i controlli (da TypeScript 6 è acceso di default). `tsc` compila: da TypeScript 7 il compilatore è riscritto in Go ed è molto più veloce, ma il comando resta lo stesso. Nelle classi `private` e `public` limitano l’accesso, e un costruttore può dichiarare i campi da solo.', '`tsconfig.json` sets up the compiler: `strict` turns on all the checks (since TypeScript 6 it is on by default). `tsc` compiles: since TypeScript 7 the compiler is rewritten in Go and is much faster, but the command stays the same. In classes `private` and `public` limit access, and a constructor can declare fields by itself.'),
            C('// tsconfig.json
{
  "compilerOptions": {
    "target": "ES2022",
    "strict": true
  }
}
class Eroe {
  constructor(public nome: string, private vite = 3) {}
  colpisci(): void { this.vite--; }
}', 'ts'),
            P('Per le librerie scritte in JavaScript esistono i pacchetti `@types`, che aggiungono i tipi. Un file `.d.ts` contiene solo tipi.', 'For libraries written in JavaScript there are `@types` packages, which add the types. A `.d.ts` file holds only types.'),
        ], [
            Q('c1', L('Che cosa attiva `strict`?', 'What does `strict` turn on?'), [L('Tutti i controlli severi', 'All the strict checks'), L('Il minify', 'Minifying'), L('Il server', 'The server'), L('I commenti', 'Comments')], 0, L('Da TypeScript 6 è acceso di default: tienilo così.', 'Since TypeScript 6 it is on by default: keep it that way.')),
            Q('c2', L('Quale comando compila?', 'Which command compiles?'), ['tsc', 'ts-run', 'compile', 'node'], 0, L('Legge `tsconfig.json`.', 'It reads `tsconfig.json`.')),
            Q('c3', L('Che cosa contiene un file `.d.ts`?', 'What does a `.d.ts` file hold?'), [L('Solo tipi', 'Only types'), L('Solo CSS', 'Only CSS'), L('Immagini', 'Images'), L('Dati', 'Data')], 0, L('Descrive codice che esiste altrove.', 'It describes code that lives elsewhere.')),
        ], task('ts', L('Scrivi una classe `Conto` con un campo `private saldo: number`.', 'Write a class `Conto` with a `private saldo: number` field.'), '', [re('class\s+Conto', L('La classe esiste', 'The class exists')), re('private\s+saldo\s*:\s*number', L('Il campo è privato', 'The field is private'))], '', "class Conto {\n  private saldo: number = 0;\n}"), [
            dMatch(pMatch(), [['tsc', L('Il compilatore', 'The compiler')], ['tsconfig.json', L('Le impostazioni', 'The settings')], ['strict', L('I controlli severi', 'The strict checks')], ['@types', L('Tipi per le librerie', 'Types for libraries')], ['.d.ts', L('File di soli tipi', 'Types-only file')]], L('Gli attrezzi del mestiere.', 'The tools of the trade.')),
            dFlash(pFlash(), [[L('`private` è controllato in compilazione', '`private` is checked at compile time'), true], [L('`strict` rende TypeScript meno utile', '`strict` makes TypeScript less useful'), false], [L('`tsc` produce JavaScript', '`tsc` produces JavaScript'), true], [L('`@types` aggiunge funzioni nuove alla libreria', '`@types` adds new functions to the library'), false]], L('`@types` descrive, non aggiunge: solo tipi.', '`@types` describes, it does not add: types only.')),
        ]),
        lesson('discriminated', L('Unioni discriminate e never', 'Discriminated unions and never'), [
            P('Un’**unione discriminata** è un’unione di oggetti che hanno tutti una proprietà comune con un valore fisso diverso, di solito `tipo` o `kind`. Controllando quella proprietà, TypeScript sa esattamente quale forma hai in mano.', 'A **discriminated union** is a union of objects that all share a property with a different fixed value, usually `tipo` or `kind`. By checking that property, TypeScript knows exactly which shape you are holding.'),
            C('type Forma =
  | { tipo: "cerchio"; raggio: number }
  | { tipo: "rettangolo"; base: number; altezza: number };

function area(f: Forma): number {
  switch (f.tipo) {
    case "cerchio":
      return Math.PI * f.raggio ** 2;
    case "rettangolo":
      return f.base * f.altezza;
    default: {
      const mai: never = f; // errore se dimentichi un caso
      return mai;
    }
  }
}', 'ts'),
            P('`never` è il tipo che non ha valori. Assegnare `f` a `never` nel `default` è un controllo di **completezza**: se domani aggiungi `{ tipo: "triangolo" }` e non lo gestisci, il compilatore lo segnala. Per i tuoi controlli puoi scrivere una **type guard**: una funzione che restituisce `x is Tipo`.', '`never` is the type with no values. Assigning `f` to `never` in the `default` is an **exhaustiveness** check: if tomorrow you add `{ tipo: "triangolo" }` and do not handle it, the compiler flags it. For your own checks you can write a **type guard**: a function returning `x is Type`.'),
            C('function eStringa(x: unknown): x is string {
  return typeof x === "string";
}', 'ts'),
        ], [
            Q('dn1', L('Che cosa rende «discriminata» un’unione?', 'What makes a union “discriminated”?'), [L('Il fatto di contenere solo numeri', 'Containing only numbers'), L('Una proprietà comune con valori fissi diversi', 'A shared property with different fixed values'), L('La parola chiave `discriminate`', 'The `discriminate` keyword'), L('Un’interfaccia che la estende', 'An interface extending it')], 1, L('Il valore di `tipo` dice quale forma è.', 'The value of `tipo` tells which shape it is.')),
            Q('dn2', L('A che cosa serve `const mai: never = f` nel `default`?', 'What is `const mai: never = f` in the `default` for?'), [L('A cancellare `f`', 'To delete `f`'), L('A rendere `f` facoltativo', 'To make `f` optional'), L('A convertire `f` in stringa', 'To turn `f` into a string'), L('A segnalare in compilazione i casi dimenticati', 'To flag forgotten cases at compile time')], 3, L('Se resta un caso non gestito, `f` non è `never` e c’è errore.', 'If a case is left unhandled, `f` is not `never` and there is an error.')),
            Q('dn3', L('Che cosa promette una funzione con risultato `x is string`?', 'What does a function with result `x is string` promise?'), [L('Che restituisce sempre una stringa', 'That it always gives back a string'), L('Che se restituisce `true`, `x` è una stringa', 'That if it gives back `true`, `x` is a string'), L('Che lancia un errore con i numeri', 'That it throws an error with numbers'), L('Che `x` diventa `any`', 'That `x` becomes `any`')], 1, L('Dentro `if (eStringa(v))` TypeScript tratta `v` come `string`.', 'Inside `if (eStringa(v))` TypeScript treats `v` as `string`.')),
        ], task('ts', L('Crea il tipo `Esito` come unione di `{ ok: true; dati: string }` e `{ ok: false; errore: string }`.', 'Create the type `Esito` as a union of `{ ok: true; dati: string }` and `{ ok: false; errore: string }`.'), '', [re('type\s+Esito\s*=', L('Il tipo esiste', 'The type exists')), re('\{\s*ok\s*:\s*true\s*[;,]\s*dati\s*:\s*string\s*[;,]?\s*\}', L('C’è il caso riuscito', 'The success case is there')), re('\{\s*ok\s*:\s*false\s*[;,]\s*errore\s*:\s*string\s*[;,]?\s*\}', L('C’è il caso fallito', 'The failure case is there')), re('\}\s*\|\s*\{', L('Sono uniti con `|`', 'They are joined with `|`'))], '', 'type Esito =
  | { ok: true; dati: string }
  | { ok: false; errore: string };'), [
            dBlanks(pFill(), "type Animale =\n  | { ___: \"cane\"; abbaia: boolean }\n  | { specie: \"gatto\"; fusa: boolean };\nfunction verso(a: Animale) {\n  if (a.specie === ___) return a.abbaia;\n  return a.fusa;\n}", ['specie', '"cane"'], ['tipo', '"gatto"', 'kind'], L('Stessa proprietà in ogni caso, poi il controllo che restringe.', 'The same property in every case, then the check that narrows.')),
            dFlash(pFlash(), [[L('`never` non ha nessun valore', '`never` has no values'), true], [L('Una type guard restituisce `x is Tipo`', 'A type guard returns `x is Type`'), true], [L('In uno `switch` sul discriminante il tipo non si restringe', 'In a `switch` on the discriminant the type does not narrow'), false], [L('Aggiungere un caso all’unione non cambia niente negli `switch` completi', 'Adding a case to the union changes nothing in exhaustive `switch`es'), false]], L('Il compilatore ti avvisa dei casi nuovi.', 'The compiler warns you about new cases.')),
        ]),
        lesson('type-ops', L('keyof, typeof e satisfies', 'keyof, typeof and satisfies'), [
            P('TypeScript può **calcolare** tipi da altri tipi. `keyof T` dà l’unione dei nomi delle chiavi, `typeof valore` ricava il tipo da una variabile, `T["chiave"]` legge il tipo di una proprietà.', 'TypeScript can **compute** types from other types. `keyof T` gives the union of the key names, `typeof value` derives the type from a variable, `T["key"]` reads the type of a property.'),
            Fig(L(fig_ts_keyof('it'), fig_ts_keyof('en')),
                L('Tre riquadri: il valore tema con chiaro e scuro; con typeof diventa un tipo con le stesse chiavi; con keyof diventa l’unione "chiaro" | "scuro".', 'Three boxes: the value tema with chiaro and scuro; with typeof it becomes a type with the same keys; with keyof it becomes the union "chiaro" | "scuro".'),
                L('`typeof` passa dal valore al tipo, `keyof` dal tipo ai nomi delle sue chiavi.', '`typeof` goes from the value to the type, `keyof` from the type to the names of its keys.')),
            C('const tema = {
  chiaro: "#ffffff",
  scuro: "#111111",
} as const;

type NomeTema = keyof typeof tema;      // "chiaro" | "scuro"
type Colore = (typeof tema)[NomeTema];  // "#ffffff" | "#111111"

function leggi<T, K extends keyof T>(obj: T, chiave: K): T[K] {
  return obj[chiave];
}

const config = { porta: 3000, host: "localhost" } satisfies Record<string, string | number>;
config.porta.toFixed(); // ok: resta number', 'ts'),
            P('`as const` rende i valori letterali e in sola lettura. `satisfies` controlla che un valore rispetti un tipo **senza allargarlo**: con `: Record<…>` `config.porta` diventerebbe `string | number`, con `satisfies` resta `number`.', '`as const` makes the values literal and read-only. `satisfies` checks that a value respects a type **without widening it**: with `: Record<…>` `config.porta` would become `string | number`, with `satisfies` it stays `number`.'),
        ], [
            Q('to1', L('Che cosa dà `keyof { a: 1; b: 2 }`?', 'What does `keyof { a: 1; b: 2 }` give?'), ['1 | 2', '["a", "b"]', '"a" | "b"', 'string'], 2, L('L’unione dei nomi delle chiavi.', 'The union of the key names.')),
            Q('to2', L('Che differenza c’è tra `: Tipo` e `satisfies Tipo`?', 'What is the difference between `: Type` and `satisfies Type`?'), [L('Nessuna', 'None'), L('`satisfies` controlla senza allargare il tipo dedotto', '`satisfies` checks without widening the inferred type'), L('`satisfies` spegne i controlli', '`satisfies` switches checks off'), L('`: Tipo` funziona solo con le classi', '`: Type` only works with classes')], 1, L('Hai il controllo e mantieni la precisione.', 'You get the check and keep the precision.')),
            Q('to3', L('Che effetto ha `as const` su un oggetto?', 'What effect does `as const` have on an object?'), [L('Lo rende modificabile', 'It makes it editable'), L('Lo converte in una classe', 'It turns it into a class'), L('Lo cancella in compilazione', 'It deletes it on compiling'), L('Rende i valori letterali e in sola lettura', 'It makes the values literal and read-only')], 3, L('`"#ffffff"` invece di `string`.', '`"#ffffff"` instead of `string`.')),
        ], task('ts', L('Dal valore `ruoli` ricava il tipo `Ruolo` con `keyof typeof`.', 'From the value `ruoli` derive the type `Ruolo` with `keyof typeof`.'), 'const ruoli = { admin: 1, ospite: 2 } as const;
', [re('type\s+Ruolo\s*=\s*keyof\s+typeof\s+ruoli', L('`Ruolo` nasce dalle chiavi di `ruoli`', '`Ruolo` comes from the keys of `ruoli`'))], '', 'const ruoli = { admin: 1, ospite: 2 } as const;
type Ruolo = keyof typeof ruoli;'), [
            dMatch(pMatch(), [['keyof T', L('Nomi delle chiavi', 'Key names')], ['typeof x', L('Tipo di una variabile', 'Type of a variable')], ['T["k"]', L('Tipo di una proprietà', 'Type of a property')], ['as const', L('Valori letterali e fissi', 'Literal, fixed values')], ['satisfies', L('Controlla senza allargare', 'Checks without widening')]], L('Tipi calcolati da altri tipi.', 'Types computed from other types.')),
            dBug(pBug(), ['const porte = { web: 80, ssh: 22 };', 'type NomePorta = keyof porte;', 'const p: NomePorta = "web";'], 1, 'type NomePorta = keyof typeof porte;', L('`keyof` lavora sui tipi: per partire da un valore serve `typeof`.', '`keyof` works on types: to start from a value you need `typeof`.')),
        ]),
        lesson('modern-setup', L('TypeScript oggi: Node e sintassi cancellabile', 'TypeScript today: Node and erasable syntax'), [
            P('Oggi puoi eseguire TypeScript in più modi. Le versioni recenti di **Node.js** avviano direttamente un file `.ts`: tolgono i tipi e non controllano niente, quindi il controllo resta compito di `tsc --noEmit` o dell’editor. Strumenti come Vite ed esbuild fanno lo stesso per il browser.', 'Today you can run TypeScript in several ways. Recent versions of **Node.js** start a `.ts` file directly: they strip the types and check nothing, so checking stays the job of `tsc --noEmit` or the editor. Tools like Vite and esbuild do the same for the browser.'),
            C('node app.ts          # Node toglie i tipi ed esegue
npx tsc --noEmit     # controlla i tipi senza produrre file', 'bash'),
            C('import type { Utente } from "./tipi.ts";
import { salva, type Opzioni } from "./db.ts";

// sintassi «cancellabile»: basta togliere i tipi
type Stato = "attesa" | "fatto";
const LIVELLI = { base: 1, medio: 2 } as const;

// questa invece genera codice JavaScript
enum Colore { Rosso, Verde }', 'ts'),
            P('Chi toglie soltanto i tipi non sa gestire la sintassi che produce codice: `enum`, `namespace` con codice e proprietà dichiarate nel costruttore. L’opzione `erasableSyntaxOnly` la segnala; al posto di un `enum` usa un’unione di stringhe o un oggetto `as const`. `import type` dice chiaramente che un import serve solo ai tipi e sparirà.', 'Tools that only strip types cannot handle syntax that produces code: `enum`, `namespace` with code and properties declared in the constructor. The `erasableSyntaxOnly` option flags it; instead of an `enum` use a union of strings or an `as const` object. `import type` says clearly that an import is only for types and will vanish.'),
        ], [
            Q('mo1', L('Che cosa fa Node.js quando esegue un file `.ts`?', 'What does Node.js do when it runs a `.ts` file?'), [L('Controlla i tipi e poi esegue', 'Checks the types, then runs'), L('Toglie i tipi senza controllarli ed esegue', 'Strips the types without checking them and runs'), L('Lo converte in un eseguibile', 'Turns it into an executable'), L('Rifiuta il file', 'Rejects the file')], 1, L('Per il controllo serve `tsc` o l’editor.', 'Checking needs `tsc` or the editor.')),
            Q('mo2', L('Quale di queste NON è sintassi cancellabile?', 'Which of these is NOT erasable syntax?'), ['type Stato = "a" | "b"', 'interface Utente { nome: string }', 'enum Colore { Rosso }', 'let n: number = 1'], 2, L('Un `enum` diventa un oggetto JavaScript.', 'An `enum` becomes a JavaScript object.')),
            Q('mo3', L('A che cosa serve `tsc --noEmit`?', 'What is `tsc --noEmit` for?'), [L('A controllare i tipi senza produrre file', 'To check types without producing files'), L('A cancellare i file `.js`', 'To delete the `.js` files'), L('Ad avviare il programma', 'To start the program'), L('A installare i pacchetti `@types`', 'To install `@types` packages')], 0, L('Perfetto in CI, accanto a chi esegue il codice.', 'Perfect in CI, next to whatever runs the code.')),
        ], task('ts', L('Sostituisci l’`enum` con un tipo unione `Livello` che accetta `"base"`, `"medio"` e `"avanzato"`.', 'Replace the `enum` with a union type `Livello` accepting `"base"`, `"medio"` and `"avanzato"`.'), 'enum Livello { Base, Medio, Avanzato }', [re('type\s+Livello\s*=', L('`Livello` è un tipo', '`Livello` is a type')), re('["\']base["\']\s*\|\s*["\']medio["\']\s*\|\s*["\']avanzato["\']', L('Accetta i tre valori', 'It accepts the three values'))], '', 'type Livello = "base" | "medio" | "avanzato";'), [
            dFlash(pFlash(), [[L('Node.js controlla i tipi quando esegue un `.ts`', 'Node.js checks types when it runs a `.ts`'), false], [L('`import type` sparisce dal JavaScript finale', '`import type` vanishes from the final JavaScript'), true], [L('Un `enum` genera codice JavaScript', 'An `enum` generates JavaScript code'), true], [L('`tsc --noEmit` produce i file `.js`', '`tsc --noEmit` produces the `.js` files'), false]], L('Eseguire e controllare sono due passi diversi.', 'Running and checking are two different steps.')),
            dMatch(pMatch(), [['node app.ts', L('Esegue togliendo i tipi', 'Runs by stripping types')], ['tsc --noEmit', L('Controlla senza produrre file', 'Checks without producing files')], ['import type', L('Import solo per i tipi', 'Import for types only')], ['erasableSyntaxOnly', L('Vieta la sintassi che genera codice', 'Forbids syntax that generates code')]], L('Gli attrezzi di oggi.', 'Today’s tools.')),
        ]),
    ],
];
