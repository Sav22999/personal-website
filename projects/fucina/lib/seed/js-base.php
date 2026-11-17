<?php
/** JavaScript basics: values, decisions, loops, functions, arrays and objects, string and number methods, scope and references. */

return [
    'id' => 'js-base', 'family' => 'js', 'after' => 'html-base',
    'title' => L('JavaScript base', 'JavaScript Basics'), 'short' => L('JS base', 'JS Basics'), 'level' => L('Base', 'Basic'),
    'desc' => L('Il linguaggio del web: variabili, condizioni, cicli, funzioni, array e oggetti, metodi di stringhe e numeri, ambito e riferimenti.', 'The language of the web: variables, conditions, loops, functions, arrays and objects, string and number methods, scope and references.'),
    'lessons' => [
        lesson('variables', L('Variabili e tipi', 'Variables and types'), [
            P('JavaScript dà vita alle pagine. Una **variabile** è una scatola con un nome: `const` non si può riassegnare, `let` sì. I tipi di base sono numeri, stringhe, booleani, `null` e `undefined` (più `bigint` e `symbol`, che userai di rado).', 'JavaScript brings pages to life. A **variable** is a box with a name: `const` cannot be reassigned, `let` can. The basic types are numbers, strings, booleans, `null` and `undefined` (plus `bigint` and `symbol`, which you will rarely use).'),
            C('const nome = "Sav";
let punti = 10;
punti = punti + 5;
console.log(nome, punti); // Sav 15
console.log(typeof punti); // "number"', 'js'),
            Tip('Usa `const` di partenza e passa a `let` solo se il valore deve cambiare.', 'Start with `const` and move to `let` only if the value must change.'),
        ], [
            Q('v1', L('Quale parola crea una variabile che non si riassegna?', 'Which word creates a variable that cannot be reassigned?'), ['const', 'let', 'var', 'fix'], 0, L('`const` sta per costante.', '`const` stands for constant.')),
            Q('v2', L('Che tipo è `"42"`?', 'What type is `"42"`?'), [L('Stringa', 'String'), L('Numero', 'Number'), L('Booleano', 'Boolean'), L('Oggetto', 'Object')], 0, L('Le virgolette la rendono testo.', 'The quotes make it text.')),
            Q('v3', L('Che cosa stampa un messaggio di controllo?', 'What prints a debug message?'), ['console.log()', 'print()', 'echo', 'write()'], 0, L('Lo vedi negli strumenti del browser.', 'You see it in the browser tools.')),
        ], task('js', L('Crea una costante `nome` con il tuo nome e una variabile `punti` con `let`, uguale a `0`.', 'Create a constant `nome` with your name and a variable `punti` with `let`, equal to `0`.'), '', [re('const\s+nome\s*=\s*["\']', L('`nome` è una costante stringa', '`nome` is a string constant')), re('let\s+punti\s*=\s*0', L('`punti` parte da 0', '`punti` starts from 0'))], '', "const nome = \"Sav\";\nlet punti = 0;"), [
            dMatch(pMatch(), [['42', L('Numero', 'Number')], ['"ciao"', L('Stringa', 'String')], ['true', L('Booleano', 'Boolean')], ['null', L('Vuoto voluto', 'Intentionally empty')], ['undefined', L('Non ancora assegnato', 'Not assigned yet')]], L('Ogni valore ha un tipo.', 'Every value has a type.')),
            dBug(pBug(), ['const vite = 3;', 'console.log(vite);', 'vite = 2;'], 2, 'let vite = 2;', L('Una `const` non si può riassegnare: qui servirebbe `let`.', 'A `const` cannot be reassigned: `let` is needed here.')),
            dBlanks(pFill(), "___ nome = \"Sav\";\n___ punti = 0;\npunti = 10;", ['const', 'let'], ['var1', 'int', 'string'], L('`const` se non cambia, `let` se cambia.', '`const` if it stays, `let` if it changes.')),
        ]),
        lesson('operators', L('Operatori e stringhe', 'Operators and strings'), [
            P('Con i numeri usi `+ - * / %`. Il `%` dà il resto di una divisione. Per i confronti usa sempre `===`, che controlla anche il tipo. Le stringhe si uniscono con i **template literal**, tra apici inversi.', 'With numbers you use `+ - * / %`. `%` gives the remainder of a division. For comparisons always use `===`, which also checks the type. Strings join with **template literals**, between backticks.'),
            C('const a = 7 % 2;      // 1
5 === "5";            // false
5 == "5";             // true: da evitare
const nome = "Sav";
const saluto = `Ciao ${nome}!`;
saluto.length;        // 9
saluto.toUpperCase(); // "CIAO SAV!"', 'js'),
            P('`&&` significa «e», `||` «oppure», `!` «non».', '`&&` means “and”, `||` “or”, `!` “not”.'),
        ], [
            Q('o1', L('Quanto vale `10 % 3`?', 'What is `10 % 3`?'), ['1', '3', '0', '10'], 0, L('10 diviso 3 dà resto 1.', '10 divided by 3 leaves 1.')),
            Q('o2', L('Quale confronto è consigliato?', 'Which comparison is recommended?'), ['===', '==', '=', '=>'], 0, L('`===` non converte i tipi.', '`===` does not convert types.')),
            Q('o3', L('Come si inserisce una variabile in un testo?', 'How do you put a variable in a text?'), ['`${x}`', '"{x}"', '<x>', '#x'], 0, L('Servono gli apici inversi.', 'Backticks are needed.')),
        ], task('js', L('Crea `saluto` con un template literal che contenga `${nome}`.', 'Create `saluto` with a template literal containing `${nome}`.'), 'const nome = "Sav";
', [re('saluto\s*=\s*`[^`]*\$\{nome\}[^`]*`', L('Il template contiene `${nome}`', 'The template contains `${nome}`'))], '', "const nome = \"Sav\";\nconst saluto = `Ciao \${nome}!`;"), [
            dFlash(pFlash(), [[L('`5 === "5"` è falso', '`5 === "5"` is false'), true], [L('`"a" + "b"` fa `"ab"`', '`"a" + "b"` makes `"ab"`'), true], [L('`4 % 2` vale 2', '`4 % 2` is 2'), false], [L('`!true` è falso', '`!true` is false'), true], [L('`=` confronta due valori', '`=` compares two values'), false]], L('`=` assegna, `===` confronta.', '`=` assigns, `===` compares.')),
            dMatch(pMatch(), [['&&', L('E', 'And')], ['||', L('Oppure', 'Or')], ['!', L('Non', 'Not')], ['===', L('Uguale, anche nel tipo', 'Equal, in type too')], ['%', L('Resto', 'Remainder')]], L('Pochi simboli, usati ovunque.', 'A few symbols, used everywhere.')),
        ]),
        lesson('conditions', L('Condizioni', 'Conditions'), [
            P('`if` esegue un blocco solo se una condizione è vera. `else` copre il caso contrario. Il **ternario** è la versione breve, utile per scegliere un valore.', '`if` runs a block only if a condition is true. `else` covers the opposite case. The **ternary** is the short version, handy for choosing a value.'),
            C('if (punti >= 100) {
  console.log("Grande!");
} else if (punti >= 50) {
  console.log("Bene");
} else {
  console.log("Continua");
}
const stato = punti > 0 ? "attivo" : "fermo";', 'js'),
            P('Alcuni valori contano come falsi: `0`, `""`, `null`, `undefined`, `NaN`, `false`. Tutti gli altri contano come veri.', 'Some values count as false: `0`, `""`, `null`, `undefined`, `NaN`, `false`. All the others count as true.'),
        ], [
            Q('c1', L('Che cosa succede se la condizione di `if` è falsa?', 'What happens if the `if` condition is false?'), [L('Il blocco viene saltato', 'The block is skipped'), L('Dà errore', 'It errors'), L('Si ripete', 'It repeats'), L('Si ferma tutto', 'Everything stops')], 0, L('Si passa all’`else`, se c’è.', 'It moves to the `else`, if any.')),
            Q('c2', L('Quale di questi conta come falso?', 'Which of these counts as false?'), ['0', '"0"', '[]', '1'], 0, L('`"0"` è una stringa non vuota: conta come vera.', '`"0"` is a non-empty string: it counts as true.')),
            Q('c3', L('Quanto vale `3 > 2 ? "a" : "b"`?', 'What is `3 > 2 ? "a" : "b"`?'), ['"a"', '"b"', 'true', '3'], 0, L('La condizione è vera: vince il primo valore.', 'The condition is true: the first value wins.')),
        ], task('js', L('Scrivi un `if` che con `punti >= 10` assegna `premio = true`, altrimenti `premio = false`.', 'Write an `if` that with `punti >= 10` sets `premio = true`, otherwise `premio = false`.'), 'let premio;
', [re('if\s*\(\s*punti\s*>=\s*10\s*\)', L('La condizione è giusta', 'The condition is right')), re('else', L('C’è `else`', '`else` is there')), re('premio\s*=\s*true', L('Assegni true', 'You set true'))], '', "let premio;\nif (punti >= 10) {\n  premio = true;\n} else {\n  premio = false;\n}"), [
            dOrder(pSort(), ['if (vite > 0) {', '  gioca();', '} else {', '  fine();', '}'], L('Condizione, blocco vero, `else`, blocco falso.', 'Condition, true block, `else`, false block.')),
            dBug(pBug(), ['let vite = 3;', 'if (vite = 0) {', '  fine();', '}'], 1, 'if (vite === 0) {', L('`=` assegna: qui serve `===`, altrimenti `vite` diventa 0.', '`=` assigns: `===` is needed, otherwise `vite` becomes 0.')),
            dFlash(pFlash(), [[L('`""` conta come falso', '`""` counts as false'), true], [L('`"ciao"` conta come vero', '`"ciao"` counts as true'), true], [L('`null` conta come vero', '`null` counts as true'), false], [L('`else` è obbligatorio', '`else` is mandatory'), false]], L('`else` si scrive solo quando serve.', '`else` is written only when needed.')),
        ]),
        lesson('loops', L('Cicli', 'Loops'), [
            P('Un **ciclo** ripete un blocco. `for` conta, `while` continua finché una condizione è vera, `for…of` scorre gli elementi di una lista.', 'A **loop** repeats a block. `for` counts, `while` goes on while a condition is true, `for…of` walks the items of a list.'),
            C('for (let i = 0; i < 3; i++) {
  console.log(i); // 0, 1, 2
}
let n = 3;
while (n > 0) {
  n--;
}
for (const nome of ["Ada", "Sav"]) {
  console.log(nome);
}', 'js'),
            P('`break` esce dal ciclo, `continue` salta al giro dopo. Attenzione ai cicli che non finiscono mai: bloccano la pagina.', '`break` leaves the loop, `continue` jumps to the next round. Watch out for loops that never end: they freeze the page.'),
        ], [
            Q('l1', L('Quante volte gira `for (let i = 0; i < 3; i++)`?', 'How many times does `for (let i = 0; i < 3; i++)` run?'), ['3', '2', '4', '0'], 0, L('`i` vale 0, 1 e 2.', '`i` is 0, 1 and 2.')),
            Q('l2', L('Che cosa fa `break`?', 'What does `break` do?'), [L('Esce dal ciclo', 'Leaves the loop'), L('Salta un giro', 'Skips a round'), L('Riparte', 'Restarts'), L('Rompe la pagina', 'Breaks the page')], 0, L('`continue` è quello che salta un giro.', '`continue` is what skips a round.')),
            Q('l3', L('Che cosa scorre `for…of`?', 'What does `for…of` walk?'), [L('Gli elementi', 'The items'), L('Gli indici', 'The indexes'), L('I file', 'The files'), L('Le righe', 'The lines')], 0, L('Niente contatore da gestire.', 'No counter to manage.')),
        ], task('js', L('Scrivi un ciclo `for` che somma in `totale` i numeri da 1 a 5.', 'Write a `for` loop that adds the numbers from 1 to 5 into `totale`.'), 'let totale = 0;
', [re('for\s*\(\s*let\s+i\s*=\s*1\s*;\s*i\s*<=\s*5\s*;\s*i\+\+\s*\)', L('Il ciclo va da 1 a 5', 'The loop goes from 1 to 5')), re('totale\s*(\+=\s*i|=\s*totale\s*\+\s*i)', L('Sommi `i` a `totale`', 'You add `i` to `totale`'))], '', "let totale = 0;\nfor (let i = 1; i <= 5; i++) {\n  totale += i;\n}"), [
            dBlanks(pFill(), "___ (let i = 0; i ___ 3; i___) {\n  console.log(i);\n}", ['for', '<', '++'], ['while', '=', '--'], L('Partenza, condizione e passo.', 'Start, condition and step.')),
            dBug(pBug(), ['let n = 3;', 'while (n > 0) {', '  console.log(n);', '  n++;', '}'], 3, '  n--;', L('Con `n++` il numero cresce e il ciclo non finisce mai.', 'With `n++` the number grows and the loop never ends.')),
        ]),
        lesson('functions', L('Funzioni', 'Functions'), [
            P('Una **funzione** è un pezzo di codice con un nome, da riusare. Riceve dei **parametri** e con `return` restituisce un risultato. Le **arrow function** sono la forma breve.', 'A **function** is a named piece of code to reuse. It takes **parameters** and with `return` gives back a result. **Arrow functions** are the short form.'),
            C('function somma(a, b) {
  return a + b;
}
const doppio = n => n * 2;
const saluta = (nome = "amico") => `Ciao ${nome}`;

somma(2, 3);   // 5
doppio(4);     // 8
saluta();      // "Ciao amico"', 'js'),
            P('Dopo `return` la funzione finisce. Senza `return` restituisce `undefined`. Le variabili create dentro non si vedono da fuori.', 'After `return` the function ends. Without `return` it gives back `undefined`. Variables created inside cannot be seen from outside.'),
        ], [
            Q('f1', L('Che cosa fa `return`?', 'What does `return` do?'), [L('Restituisce un valore', 'Gives back a value'), L('Stampa', 'Prints'), L('Ripete', 'Repeats'), L('Riavvia', 'Restarts')], 0, L('E chiude la funzione.', 'And it ends the function.')),
            Q('f2', L('Che cosa restituisce una funzione senza `return`?', 'What does a function without `return` give back?'), ['undefined', '0', 'null', '""'], 0, L('È il valore «niente» di default.', 'It is the default “nothing” value.')),
            Q('f3', L('Quale è una arrow function?', 'Which is an arrow function?'), ['n => n + 1', 'n -> n + 1', 'fn n: n + 1', 'n >> n + 1'], 0, L('Una freccia con `=>`.', 'An arrow with `=>`.')),
        ], task('js', L('Scrivi una funzione `area(b, h)` che restituisce `b * h`.', 'Write a function `area(b, h)` that gives back `b * h`.'), '', [re('(function\s+area\s*\(\s*b\s*,\s*h\s*\)|area\s*=\s*\(\s*b\s*,\s*h\s*\)\s*=>)', L('`area` riceve `b` e `h`', '`area` takes `b` and `h`')), re('(return\s+b\s*\*\s*h|=>\s*b\s*\*\s*h)', L('Restituisce il prodotto', 'It gives back the product'))], '', "function area(b, h) {\n  return b * h;\n}"), [
            dOrder(pSort(), ['function somma(a, b) {', '  return a + b;', '}', 'const r = somma(2, 3);', 'console.log(r);'], L('Prima si definisce, poi si chiama.', 'First define, then call.')),
            dBlanks(pFill(), "___ doppio(n) {\n  ___ n * 2;\n}\nconst triplo = n ___ n * 3;", ['function', 'return', '=>'], ['def', 'give', '->'], L('Le due forme di una funzione.', 'The two forms of a function.')),
            dBug(pBug(), ['function somma(a, b) {', '  a + b;', '}', 'console.log(somma(1, 2));'], 1, '  return a + b;', L('Senza `return` il risultato si perde e arriva `undefined`.', 'Without `return` the result is lost and you get `undefined`.')),
        ]),
        lesson('arrays', L('Array', 'Arrays'), [
            P('Un **array** è una lista ordinata. Si parte a contare da `0`. `push` aggiunge in fondo, `pop` toglie l’ultimo, `length` dice quanti sono.', 'An **array** is an ordered list. Counting starts at `0`. `push` adds to the end, `pop` removes the last, `length` says how many.'),
            C('const corsi = ["HTML", "CSS"];
corsi.push("JS");
corsi[0];        // "HTML"
corsi.length;    // 3
corsi.includes("CSS"); // true
corsi.indexOf("JS");   // 2', 'js'),
            P('Anche se `const`, un array può cambiare contenuto: `const` blocca solo la riassegnazione della variabile.', 'Even if `const`, an array can change its content: `const` only blocks reassigning the variable.'),
        ], [
            Q('a1', L('Qual è l’indice del primo elemento?', 'What is the index of the first item?'), ['0', '1', '-1', 'a'], 0, L('In JavaScript si conta da zero.', 'JavaScript counts from zero.')),
            Q('a2', L('Che cosa fa `push`?', 'What does `push` do?'), [L('Aggiunge in fondo', 'Adds to the end'), L('Aggiunge all’inizio', 'Adds to the start'), L('Toglie', 'Removes'), L('Ordina', 'Sorts')], 0, L('`unshift` aggiunge all’inizio.', '`unshift` adds to the start.')),
            Q('a3', L('Con `const a = [1]`, `a.push(2)` funziona?', 'With `const a = [1]`, does `a.push(2)` work?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo con `let`', 'Only with `let`'), L('Dà errore', 'It errors')], 0, L('Cambia il contenuto, non la variabile.', 'It changes the content, not the variable.')),
        ], task('js', L('Crea l’array `frutti` con due elementi e aggiungine un terzo con `push`.', 'Create the array `frutti` with two items and add a third with `push`.'), '', [re('frutti\s*=\s*\[[^\]]+,[^\]]+\]', L('L’array ha due elementi', 'The array has two items')), re('frutti\.push\(', L('Usi `push`', 'You use `push`'))], '', "const frutti = [\"mela\", \"pera\"];\nfrutti.push(\"uva\");"), [
            dMatch(pMatch(), [['push()', L('Aggiunge in fondo', 'Adds to the end')], ['pop()', L('Toglie l’ultimo', 'Removes the last')], ['length', L('Quanti sono', 'How many')], ['includes()', L('Contiene?', 'Does it contain?')], ['indexOf()', L('In che posizione?', 'At which position?')]], L('I comandi di base di ogni lista.', 'The basic commands of every list.')),
            dFlash(pFlash(), [[L('`[1, 2, 3].length` vale 3', '`[1, 2, 3].length` is 3'), true], [L('`["a", "b"][1]` vale `"a"`', '`["a", "b"][1]` is `"a"`'), false], [L('Un array può contenere tipi diversi', 'An array can hold different types'), true], [L('`pop` toglie il primo', '`pop` removes the first'), false]], L('Si conta da zero: `[1]` è il secondo elemento.', 'Counting starts at zero: `[1]` is the second item.')),
        ]),
        lesson('objects', L('Oggetti', 'Objects'), [
            P('Un **oggetto** raggruppa dati con un nome: coppie chiave e valore. Si legge con il punto, oppure con le parentesi quadre se il nome è in una variabile.', 'An **object** groups named data: key and value pairs. You read it with a dot, or with square brackets if the name is in a variable.'),
            C('const utente = {
  nome: "Sav",
  punti: 10,
  saluta() {
    return `Ciao ${this.nome}`;
  },
};
utente.punti = 20;
utente["nome"];   // "Sav"
Object.keys(utente); // ["nome", "punti", "saluta"]', 'js'),
            P('Una funzione dentro un oggetto si chiama **metodo**: con `this` accede all’oggetto stesso.', 'A function inside an object is called a **method**: with `this` it reaches the object itself.'),
        ], [
            Q('o1', L('Come si legge `punti` da `utente`?', 'How do you read `punti` from `utente`?'), ['utente.punti', 'utente->punti', 'utente:punti', 'punti(utente)'], 0, L('La notazione a punto è la più comune.', 'Dot notation is the most common.')),
            Q('o2', L('Come si chiama una funzione in un oggetto?', 'What is a function in an object called?'), [L('Metodo', 'Method'), L('Chiave', 'Key'), L('Classe', 'Class'), L('Modulo', 'Module')], 0, L('Si chiama con `oggetto.metodo()`.', 'It is called with `object.method()`.')),
            Q('o3', L('Che cosa restituisce `Object.keys()`?', 'What does `Object.keys()` give back?'), [L('I nomi delle chiavi', 'The key names'), L('I valori', 'The values'), L('Un numero', 'A number'), L('Una stringa', 'A string')], 0, L('`Object.values()` dà i valori.', '`Object.values()` gives the values.')),
        ], task('js', L('Crea l’oggetto `libro` con `titolo` e `pagine`, poi cambia `pagine` a `100`.', 'Create the object `libro` with `titolo` and `pagine`, then change `pagine` to `100`.'), '', [re('libro\s*=\s*\{[\s\S]*titolo\s*:[\s\S]*pagine\s*:', L('L’oggetto ha le due chiavi', 'The object has both keys')), re('libro\.pagine\s*=\s*100', L('Cambi `pagine`', 'You change `pagine`'))], '', "const libro = { titolo: \"Fucina\", pagine: 50 };\nlibro.pagine = 100;"), [
            dBlanks(pFill(), "const gatto = {\n  nome___ \"Micio\",\n  zampe: 4___\n};\ngatto___nome;", [':', ',', '.'], ['=', ';', '->'], L('Due punti tra chiave e valore, punto per leggere.', 'A colon between key and value, a dot to read.')),
            dBug(pBug(), ['const utente = {', '  nome = "Sav",', '  punti: 10,', '};'], 1, '  nome: "Sav",', L('Dentro un oggetto si usano i due punti, non l’uguale.', 'Inside an object you use a colon, not an equals sign.')),
        ]),
        lesson('strings', L('Metodi delle stringhe', 'String methods'), [
            P('Le stringhe hanno molti metodi pronti. Nessuno cambia la stringa originale: le stringhe sono **immutabili**, quindi ogni metodo restituisce una stringa nuova.', 'Strings have many ready-made methods. None of them changes the original string: strings are **immutable**, so every method gives back a new string.'),
            C('const frase = "  Ciao Fucina  ";
frase.trim();                // "Ciao Fucina"
frase.trim().toLowerCase();  // "ciao fucina"
"Fucina".includes("cin");    // true
"Fucina".startsWith("Fu");   // true
"Fucina".slice(0, 3);        // "Fuc"
"Fucina".at(-1);             // "a"
"a,b,c".split(",");          // ["a", "b", "c"]
["a", "b"].join("-");        // "a-b"
"gatto".replace("g", "r");   // "ratto"', 'js'),
            P('`slice(inizio, fine)` prende i caratteri da `inizio` fino a `fine` **escluso**. `split` divide una stringa in un array, `join` fa il contrario. Per tenere il risultato salvalo: `nome = nome.trim()`.', '`slice(start, end)` takes the characters from `start` up to `end`, **excluded**. `split` breaks a string into an array, `join` does the opposite. To keep the result, store it: `nome = nome.trim()`.'),
        ], [
            Q('s1', L('Quanto vale `"Fucina".slice(1, 3)`?', 'What is `"Fucina".slice(1, 3)`?'), ['"Fuc"', '"ucin"', '"uc"', '"uci"'], 2, L('Prende gli indici 1 e 2: la fine è esclusa.', 'It takes indexes 1 and 2: the end is excluded.')),
            Q('s2', L('Che cosa fa `trim()`?', 'What does `trim()` do?'), [L('Toglie tutti gli spazi, anche in mezzo', 'Removes every space, even in the middle'), L('Toglie gli spazi all’inizio e alla fine', 'Removes the spaces at the start and the end'), L('Taglia la stringa a metà', 'Cuts the string in half'), L('Rende la stringa minuscola', 'Makes the string lowercase')], 1, L('Gli spazi in mezzo restano.', 'The spaces in the middle stay.')),
            Q('s3', L('Dopo `let s = "ciao"; s.toUpperCase();` quanto vale `s`?', 'After `let s = "ciao"; s.toUpperCase();` what is `s`?'), ['"CIAO"', '"Ciao"', 'undefined', '"ciao"'], 3, L('Il metodo restituisce una stringa nuova: per tenerla serve `s = s.toUpperCase()`.', 'The method gives back a new string: to keep it you need `s = s.toUpperCase()`.')),
        ], task('js', L('Da `email` crea la costante `pulita`, usando `trim()` e `toLowerCase()`.', 'From `email` create the constant `pulita`, using `trim()` and `toLowerCase()`.'), 'const email = "  Ada@Example.COM ";
', [re('(const|let)\s+pulita\s*=\s*email', L('Crei `pulita` da `email`', 'You create `pulita` from `email`')), re('\.trim\(\s*\)', L('Togli gli spazi', 'You remove the spaces')), re('\.toLowerCase\(\s*\)', L('Passi alle minuscole', 'You switch to lowercase'))], '', 'const email = "  Ada@Example.COM ";
const pulita = email.trim().toLowerCase();'), [
            dMatch(pMatch(), [['trim()', L('Toglie gli spazi ai lati', 'Removes the spaces at the sides')], ['split(",")', L('Divide in un array', 'Splits into an array')], ['includes()', L('Contiene?', 'Does it contain?')], ['slice(0, 3)', L('I primi tre caratteri', 'The first three characters')], ['toUpperCase()', L('Tutto maiuscolo', 'All uppercase')]], L('I metodi che userai ogni giorno.', 'The methods you will use every day.')),
            dBlanks(pFill(), "const nomi = \"Ada,Bea\".___(\",\");\nconst iniziale = nomi[0].___(0, 1);\nconst grande = iniziale.___();", ['split', 'slice', 'toUpperCase'], ['join', 'push', 'upper'], L('Dividi, taglia, trasforma.', 'Split, cut, transform.')),
            dFlash(pFlash(), [[L('`"ciao".length` vale 4', '`"ciao".length` is 4'), true], [L('`trim()` cambia la stringa originale', '`trim()` changes the original string'), false], [L('`"abc".at(-1)` vale `"c"`', '`"abc".at(-1)` is `"c"`'), true], [L('`"a-b".split("-")` dà `"ab"`', '`"a-b".split("-")` gives `"ab"`'), false]], L('`split` restituisce un array: `["a", "b"]`.', '`split` gives back an array: `["a", "b"]`.')),
        ]),
        lesson('numbers', L('Numeri, conversioni e Math', 'Numbers, conversions and Math'), [
            P('In JavaScript interi e decimali sono lo stesso tipo: `number`. Quello che arriva da un campo di testo però è una **stringa**: per fare i conti va convertito con `Number()`. Se la conversione fallisce ottieni `NaN`, «not a number».', 'In JavaScript whole and decimal numbers are the same type: `number`. What comes from a text field, though, is a **string**: to do maths you convert it with `Number()`. If the conversion fails you get `NaN`, “not a number”.'),
            C('"5" + 2;                 // "52": unisce due testi
Number("5") + 2;         // 7
Number("ciao");          // NaN
Number.isNaN(Number("ciao")); // true
parseInt("42px");        // 42
(3.14159).toFixed(2);    // "3.14"
Math.round(2.5);         // 3
Math.max(4, 9, 1);       // 9
Math.floor(Math.random() * 6) + 1; // un dado da 1 a 6', 'js'),
            P('Attenzione ai decimali: `0.1 + 0.2` dà `0.30000000000000004`, perché il computer li salva in binario. Per i prezzi lavora in centesimi oppure arrotonda solo quando mostri il risultato, con `toFixed`.', 'Watch out for decimals: `0.1 + 0.2` gives `0.30000000000000004`, because the computer stores them in binary. For prices work in cents, or round only when you show the result, with `toFixed`.'),
        ], [
            Q('n1', L('Quanto vale `"5" + 2`?', 'What is `"5" + 2`?'), ['7', '"52"', 'NaN', L('Dà errore', 'It errors')], 1, L('Se c’è una stringa, `+` unisce i testi.', 'If there is a string, `+` joins the texts.')),
            Q('n2', L('Come controlli che una conversione sia fallita?', 'How do you check that a conversion failed?'), ['x === NaN', 'x == undefined', 'typeof x === "NaN"', 'Number.isNaN(x)'], 3, L('`NaN` non è uguale nemmeno a se stesso: `x === NaN` è sempre falso.', '`NaN` is not even equal to itself: `x === NaN` is always false.')),
            Q('n3', L('Che cosa restituisce `Math.floor(Math.random() * 6) + 1`?', 'What does `Math.floor(Math.random() * 6) + 1` give back?'), [L('Un intero da 1 a 6', 'A whole number from 1 to 6'), L('Un decimale da 0 a 6', 'A decimal from 0 to 6'), L('Un intero da 0 a 5', 'A whole number from 0 to 5'), L('Sempre 6', 'Always 6')], 0, L('`random` va da 0 a quasi 1: per 6 dà da 0 a quasi 6, `floor` toglie i decimali, `+ 1` sposta.', '`random` goes from 0 to almost 1: times 6 it gives 0 to almost 6, `floor` drops the decimals, `+ 1` shifts it.')),
        ], task('js', L('Converti `testo` in numero con `Number()` e salvalo in `eta`, poi crea `adulto` uguale a `eta >= 18`.', 'Convert `testo` to a number with `Number()` and store it in `eta`, then create `adulto` equal to `eta >= 18`.'), 'const testo = "21";
', [re('(const|let)\s+eta\s*=\s*Number\s*\(\s*testo\s*\)', L('Converti il testo', 'You convert the text')), re('adulto\s*=\s*eta\s*>=\s*18', L('Confronti con 18', 'You compare with 18'))], '', 'const testo = "21";
const eta = Number(testo);
const adulto = eta >= 18;'), [
            dFlash(pFlash(), [[L('`typeof 3.5` è `"number"`', '`typeof 3.5` is `"number"`'), true], [L('`Number("")` dà `NaN`', '`Number("")` gives `NaN`'), false], [L('`0.1 + 0.2 === 0.3` è vero', '`0.1 + 0.2 === 0.3` is true'), false], [L('`Math.max(2, 8)` vale 8', '`Math.max(2, 8)` is 8'), true]], L('Un solo tipo per i numeri. E occhio: `Number("")` vale 0.', 'One type for all numbers. And watch out: `Number("")` is 0.')),
            dMatch(pMatch(), [['Math.round(2.6)', '3'], ['Math.floor(2.9)', '2'], ['Math.abs(-5)', '5'], ['Math.max(1, 4)', '4'], ['parseInt("7kg")', '7']], L('`round` arrotonda, `floor` scende, `abs` toglie il segno.', '`round` rounds, `floor` goes down, `abs` drops the sign.')),
            dBug(pBug(), ['const prezzo = "10";', 'const spedizione = 5;', 'const totale = prezzo + spedizione;', 'console.log(totale); // "105"'], 2, 'const totale = Number(prezzo) + spedizione;', L('Con una stringa `+` unisce i testi: prima va convertita.', 'With a string `+` joins the texts: convert it first.')),
        ]),
        lesson('scope', L('Ambito, valori e riferimenti', 'Scope, values and references'), [
            P('L’**ambito** (scope) dice dove una variabile esiste. `let` e `const` vivono solo nel blocco `{ }` in cui sono nate: fuori non si vedono. La vecchia `var` ignora i blocchi e vale per tutta la funzione: per questo oggi si evita.', '**Scope** tells where a variable exists. `let` and `const` live only in the `{ }` block where they were born: outside they cannot be seen. The old `var` ignores blocks and is valid for the whole function: that is why it is avoided today.'),
            C('const livello = 1;
if (livello > 0) {
  const messaggio = "Via!";   // esiste solo nel blocco
  console.log(livello, messaggio);
}
console.log(messaggio);       // ReferenceError

const a = { punti: 1 };
const b = a;                  // stesso oggetto, non una copia
b.punti = 99;
console.log(a.punti);         // 99
const c = { ...a };           // una copia vera
c.punti = 0;
console.log(a.punti);         // ancora 99', 'js'),
            P('Numeri, stringhe e booleani si copiano per **valore**. Oggetti e array invece passano per **riferimento**: due variabili possono indicare lo stesso oggetto, e cambiarlo da una lo cambia anche per l’altra. Per una copia usa lo spread: `{ ...a }` o `[...lista]`.', 'Numbers, strings and booleans are copied by **value**. Objects and arrays instead pass by **reference**: two variables can point to the same object, and changing it through one changes it for the other too. For a copy use spread: `{ ...a }` or `[...lista]`.'),
            Tip('`[1] === [1]` è falso: `===` tra oggetti controlla se sono lo **stesso** oggetto, non se hanno lo stesso contenuto.', '`[1] === [1]` is false: `===` between objects checks whether they are the **same** object, not whether they have the same content.'),
        ], [
            Q('sc1', L('Dove si vede una `const` dichiarata dentro un `if`?', 'Where can a `const` declared inside an `if` be seen?'), [L('Ovunque nel file', 'Anywhere in the file'), L('Solo dentro quel blocco', 'Only inside that block'), L('In tutta la funzione', 'In the whole function'), L('Solo dopo l’`if`', 'Only after the `if`')], 1, L('`let` e `const` rispettano i blocchi.', '`let` and `const` respect blocks.')),
            Q('sc2', L('Con `a` oggetto, dopo `const b = a; b.punti = 5;` quanto vale `a.punti`?', 'With `a` an object, after `const b = a; b.punti = 5;` what is `a.punti`?'), [L('Il valore che aveva prima', 'The value it had before'), 'undefined', L('Dà errore', 'It errors'), '5'], 3, L('`a` e `b` indicano lo stesso oggetto.', '`a` and `b` point to the same object.')),
            Q('sc3', L('Perché oggi si evita `var`?', 'Why is `var` avoided today?'), [L('È più lenta di `let`', 'It is slower than `let`'), L('Non funziona nei browser', 'It does not work in browsers'), L('Ignora i blocchi e può creare confusione', 'It ignores blocks and can cause confusion'), L('Accetta solo numeri', 'It only accepts numbers')], 2, L('`let` e `const` sono più prevedibili.', '`let` and `const` are more predictable.')),
        ], task('js', L('Crea `copia`, una copia di `lista` fatta con lo spread `[...lista]`, poi aggiungi `4` a `copia` con `push`.', 'Create `copia`, a copy of `lista` made with spread `[...lista]`, then add `4` to `copia` with `push`.'), 'const lista = [1, 2, 3];
', [re('(const|let)\s+copia\s*=\s*(\[\s*\.\.\.\s*lista\s*\]|lista\.slice\(\s*\)|Array\.from\(\s*lista\s*\)|structuredClone\(\s*lista\s*\))', L('`copia` è una copia di `lista`', '`copia` is a copy of `lista`')), re('copia\.push\(\s*4\s*\)', L('Aggiungi 4 alla copia', 'You add 4 to the copy'))], '', 'const lista = [1, 2, 3];
const copia = [...lista];
copia.push(4);'), [
            dFlash(pFlash(), [[L('Una `let` dentro `{ }` non si vede fuori', 'A `let` inside `{ }` cannot be seen outside'), true], [L('`const b = a` copia un oggetto', '`const b = a` copies an object'), false], [L('Un numero si copia per valore', 'A number is copied by value'), true], [L('`[1] === [1]` è vero', '`[1] === [1]` is true'), false]], L('Gli oggetti si passano per riferimento.', 'Objects are passed by reference.')),
            dBug(pBug(), ['const originale = { vite: 3 };', 'const copia = originale;', 'copia.vite = 0;', 'console.log(originale.vite); // deve restare 3'], 1, 'const copia = { ...originale };', L('Senza spread `copia` è lo stesso oggetto di `originale`.', 'Without spread `copia` is the same object as `originale`.')),
            dBlanks(pFill(), "const a = { n: 1 };\nconst b = { ___a };\nb.n = 2;\nconsole.log(a.n); // ___", ['...', '1'], ['&', '2', 'undefined'], L('Lo spread crea un oggetto nuovo: `a` non cambia.', 'Spread creates a new object: `a` does not change.')),
        ]),
    ],
];
