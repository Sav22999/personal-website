<?php
/** TypeScript: JavaScript with types. */

return [
    'id' => 'ts', 'family' => 'ts', 'after' => 'js-intermediate',
    'title' => L('TypeScript', 'TypeScript'), 'short' => L('TypeScript', 'TypeScript'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('JavaScript con i tipi: errori trovati prima di eseguire, interfacce, unioni e generici.', 'JavaScript with types: errors found before running, interfaces, unions and generics.'),
    'lessons' => [
        lesson('types', L('Perché i tipi', 'Why types'), [
            P('**TypeScript** è JavaScript con i tipi. Dici che cosa contiene una variabile e il compilatore segnala gli errori prima ancora di eseguire. Il browser non lo legge: viene compilato in JavaScript.', '**TypeScript** is JavaScript with types. You say what a variable holds and the compiler flags errors before anything runs. The browser does not read it: it is compiled to JavaScript.'),
            C('let nome: string = "Sav";
let punti: number = 10;
let attivo: boolean = true;
let voti: number[] = [7, 8];

punti = "tanti"; // Errore: non è un number
let vite = 3;    // tipo dedotto: number', 'ts'),
            P('Spesso il tipo non serve scriverlo: TypeScript lo **deduce** dal valore. `any` spegne i controlli: evitalo.', 'Often you need not write the type: TypeScript **infers** it from the value. `any` switches checks off: avoid it.'),
        ], [
            Q('t1', L('Quando TypeScript trova gli errori?', 'When does TypeScript find errors?'), [L('Prima di eseguire', 'Before running'), L('Solo in produzione', 'Only in production'), L('Mai', 'Never'), L('Dopo il deploy', 'After deploying')], 0, L('È il suo grande vantaggio.', 'That is its big advantage.')),
            Q('t2', L('Il browser esegue TypeScript?', 'Does the browser run TypeScript?'), [L('No, viene compilato', 'No, it is compiled'), L('Sì, sempre', 'Yes, always'), L('Solo Chrome', 'Only Chrome'), L('Solo in `<head>`', 'Only in `<head>`')], 0, L('Il risultato è JavaScript normale.', 'The result is plain JavaScript.')),
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
            P('Con `strictNullChecks` un valore che può essere `null` va controllato prima dell’uso: sparisce il classico errore «cannot read property of null».', 'With `strictNullChecks` a value that may be `null` must be checked before use: the classic “cannot read property of null” error disappears.'),
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
            P('`tsconfig.json` regola il compilatore: `strict` attiva tutti i controlli. `tsc` compila. Nelle classi `private` e `public` limitano l’accesso, e un costruttore può dichiarare i campi da solo.', '`tsconfig.json` sets up the compiler: `strict` turns on all the checks. `tsc` compiles. In classes `private` and `public` limit access, and a constructor can declare fields by itself.'),
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
            Q('c1', L('Che cosa attiva `strict`?', 'What does `strict` turn on?'), [L('Tutti i controlli severi', 'All the strict checks'), L('Il minify', 'Minifying'), L('Il server', 'The server'), L('I commenti', 'Comments')], 0, L('Va tenuto acceso nei progetti nuovi.', 'Keep it on in new projects.')),
            Q('c2', L('Quale comando compila?', 'Which command compiles?'), ['tsc', 'ts-run', 'compile', 'node'], 0, L('Legge `tsconfig.json`.', 'It reads `tsconfig.json`.')),
            Q('c3', L('Che cosa contiene un file `.d.ts`?', 'What does a `.d.ts` file hold?'), [L('Solo tipi', 'Only types'), L('Solo CSS', 'Only CSS'), L('Immagini', 'Images'), L('Dati', 'Data')], 0, L('Descrive codice che esiste altrove.', 'It describes code that lives elsewhere.')),
        ], task('ts', L('Scrivi una classe `Conto` con un campo `private saldo: number`.', 'Write a class `Conto` with a `private saldo: number` field.'), '', [re('class\s+Conto', L('La classe esiste', 'The class exists')), re('private\s+saldo\s*:\s*number', L('Il campo è privato', 'The field is private'))], '', "class Conto {\n  private saldo: number = 0;\n}"), [
            dMatch(pMatch(), [['tsc', L('Il compilatore', 'The compiler')], ['tsconfig.json', L('Le impostazioni', 'The settings')], ['strict', L('I controlli severi', 'The strict checks')], ['@types', L('Tipi per le librerie', 'Types for libraries')], ['.d.ts', L('File di soli tipi', 'Types-only file')]], L('Gli attrezzi del mestiere.', 'The tools of the trade.')),
            dFlash(pFlash(), [[L('`private` è controllato in compilazione', '`private` is checked at compile time'), true], [L('`strict` rende TypeScript meno utile', '`strict` makes TypeScript less useful'), false], [L('`tsc` produce JavaScript', '`tsc` produces JavaScript'), true], [L('`@types` aggiunge funzioni nuove alla libreria', '`@types` adds new functions to the library'), false]], L('`@types` descrive, non aggiunge: solo tipi.', '`@types` describes, it does not add: types only.')),
        ]),
    ],
];
