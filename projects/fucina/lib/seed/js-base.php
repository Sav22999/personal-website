<?php
/** JavaScript basics: values, decisions, loops, functions, arrays and objects, string and number methods, scope and references. */

/** The four steps of a for loop as a cycle; a highlight walks condition, body and step in turn. */
if (!function_exists('fig_jsbase_loop')) {
    function fig_jsbase_loop(string $l): string
    {
        $t = $l === 'it' ? ['1 · partenza', '2 · condizione', '3 · corpo', '4 · passo', 'vero', 'falso', 'fine del ciclo'] : ['1 · start', '2 · condition', '3 · body', '4 · step', 'true', 'false', 'loop ends'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.jbl{opacity:0;animation:jbl 4.5s infinite}.jbl2{animation-delay:1.5s}.jbl3{animation-delay:3s}@keyframes jbl{0%{opacity:0}4%{opacity:1}30%{opacity:1}34%{opacity:0}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.jbl{animation:none}}</style>
<defs><marker id="jbla" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-size="13" font-weight="700" fill="#5B5880">
<text x="80" y="104" text-anchor="middle">{$t[0]}</text><text x="265" y="104" text-anchor="middle">{$t[1]}</text><text x="510" y="28" text-anchor="middle">{$t[2]}</text><text x="510" y="252" text-anchor="middle">{$t[3]}</text></g>
<rect x="20" y="112" width="120" height="52" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="200" y="112" width="130" height="52" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="420" y="38" width="180" height="52" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="420" y="182" width="180" height="52" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="200" y="214" width="130" height="38" rx="10" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/>
<g class="jbl" fill="none" stroke="#F03E82" stroke-width="4"><rect x="194" y="106" width="142" height="64" rx="14"/></g>
<g class="jbl jbl2" fill="none" stroke="#F03E82" stroke-width="4"><rect x="414" y="32" width="192" height="64" rx="14"/></g>
<g class="jbl jbl3" fill="none" stroke="#F03E82" stroke-width="4"><rect x="414" y="176" width="192" height="64" rx="14"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="15" text-anchor="middle">
<text x="80" y="143">let i = 0</text><text x="265" y="143">i &lt; 3 ?</text><text x="510" y="69">console.log(i)</text><text x="510" y="213">i++</text></g>
<text x="265" y="238" text-anchor="middle" font-size="13">{$t[6]}</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#jbla)">
<path d="M140 138H196"/><path d="M330 124C370 124 380 64 416 64"/><path d="M510 90V178"/><path d="M420 208C380 208 370 152 334 152"/><path d="M265 164V210"/></g>
<g font-size="13" font-weight="700"><text x="352" y="84" fill="#0B7F5C">{$t[4]}</text><text x="274" y="192" fill="#B84508">{$t[5]}</text></g>
</svg>
SVG;
    }
}

/** An array as a row of numbered cells, with push adding a cell at the end and pop taking it away. */
if (!function_exists('fig_jsbase_array')) {
    function fig_jsbase_array(string $l): string
    {
        $t = $l === 'it' ? ['indice', 'il primo è 0, l’ultimo è length − 1'] : ['index', 'the first is 0, the last is length − 1'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="jbaa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="20" y="99" font-family="ui-monospace, Menlo, monospace" font-weight="700">corsi</text>
<text x="20" y="145" font-size="13" fill="#5B5880">{$t[0]}</text>
<text x="270" y="40" text-anchor="middle" font-size="14" font-family="ui-monospace, Menlo, monospace">length = 3</text>
<path d="M124 50V56H416V50" fill="none" stroke="#8A87B0" stroke-width="2"/>
<g stroke="#2F8CF0" stroke-width="2" fill="#DCEBFF"><rect x="120" y="66" width="96" height="56" rx="10"/><rect x="222" y="66" width="96" height="56" rx="10"/><rect x="324" y="66" width="96" height="56" rx="10"/></g>
<rect x="426" y="66" width="96" height="56" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4"/>
<g text-anchor="middle" font-weight="700"><text x="168" y="100">"HTML"</text><text x="270" y="100">"CSS"</text><text x="372" y="100">"JS"</text><text x="474" y="100" fill="#B0245A">"TS"</text></g>
<g text-anchor="middle" font-weight="700" fill="#B84508"><text x="168" y="145">0</text><text x="270" y="145">1</text><text x="372" y="145">2</text><text x="474" y="145">3</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="15"><text x="560" y="34">push()</text><text x="560" y="176">pop()</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#jbaa)"><path d="M584 42C584 54 560 60 528 70"/><path d="M522 112C556 122 580 138 586 156"/></g>
<text x="120" y="194" font-size="14" fill="#5B5880">{$t[1]}</text>
</svg>
SVG;
    }
}

/** The indexes of a string, from the start and from the end, with the part slice(1, 3) takes. */
if (!function_exists('fig_jsbase_slice')) {
    function fig_jsbase_slice(string $l): string
    {
        $t = $l === 'it' ? ['indice', 'dalla fine', 'la fine (3) è esclusa'] : ['index', 'from the end', 'the end (3) is excluded'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="20" y="60" font-size="13" fill="#5B5880">{$t[0]}</text>
<text x="20" y="150" font-size="13" fill="#5B5880">{$t[1]}</text>
<g stroke="#8A87B0" stroke-width="2" fill="#F1F0F8"><rect x="120" y="72" width="64" height="56" rx="10"/><rect x="330" y="72" width="64" height="56" rx="10"/><rect x="400" y="72" width="64" height="56" rx="10"/><rect x="470" y="72" width="64" height="56" rx="10"/></g>
<g stroke="#0B7F5C" stroke-width="2" fill="#D5F6EA"><rect x="190" y="72" width="64" height="56" rx="10"/><rect x="260" y="72" width="64" height="56" rx="10"/></g>
<g text-anchor="middle" font-size="22" font-weight="700"><text x="152" y="108">F</text><text x="222" y="108">u</text><text x="292" y="108">c</text><text x="362" y="108">i</text><text x="432" y="108">n</text><text x="502" y="108">a</text></g>
<g text-anchor="middle" font-weight="700" fill="#B84508"><text x="152" y="60">0</text><text x="222" y="60">1</text><text x="292" y="60">2</text><text x="362" y="60">3</text><text x="432" y="60">4</text><text x="502" y="60">5</text></g>
<g text-anchor="middle" fill="#5B5880"><text x="152" y="150">-6</text><text x="222" y="150">-5</text><text x="292" y="150">-4</text><text x="362" y="150">-3</text><text x="432" y="150">-2</text><text x="502" y="150">-1</text></g>
<g stroke="#F03E82" stroke-width="2" stroke-dasharray="5 4"><path d="M187 36V166"/><path d="M327 36V166"/></g>
<text x="190" y="192" font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#0B7F5C">slice(1, 3) → "uc"</text>
<text x="400" y="192" font-size="14" fill="#5B5880">{$t[2]}</text>
</svg>
SVG;
    }
}

/** Values against references: two numbers copied, two variables pointing at one object and a spread copy. */
if (!function_exists('fig_jsbase_refs')) {
    function fig_jsbase_refs(string $l): string
    {
        $t = $l === 'it' ? ['per valore', 'per riferimento', 'due copie separate', 'lo stesso oggetto', 'una copia nuova'] : ['by value', 'by reference', 'two separate copies', 'the same object', 'a new copy'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="jbra" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="20" y="32" font-weight="700" font-size="16">{$t[0]}</text>
<text x="260" y="32" font-weight="700" font-size="16">{$t[1]}</text>
<path d="M232 20V250" stroke="#E4E2F0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-weight="700"><text x="28" y="92">x</text><text x="28" y="164">y</text><text x="262" y="82">a</text><text x="262" y="142">b</text><text x="262" y="212">c</text></g>
<g stroke="#E8590C" stroke-width="2" fill="#FFF3EA"><rect x="52" y="62" width="100" height="48" rx="10"/><rect x="52" y="134" width="100" height="48" rx="10"/></g>
<g text-anchor="middle" font-weight="700" font-size="17"><text x="102" y="92">5</text><text x="102" y="164">5</text></g>
<text x="20" y="214" font-family="ui-monospace, Menlo, monospace" font-size="14">let y = x;</text>
<text x="20" y="240" font-size="13" fill="#5B5880">{$t[2]}</text>
<g stroke="#8A87B0" stroke-width="2" fill="#F1F0F8"><rect x="284" y="56" width="56" height="40" rx="8"/><rect x="284" y="116" width="56" height="40" rx="8"/><rect x="284" y="186" width="56" height="40" rx="8"/></g>
<g fill="#1E1C52"><circle cx="312" cy="76" r="5"/><circle cx="312" cy="136" r="5"/><circle cx="312" cy="206" r="5"/></g>
<g stroke="#7048E8" stroke-width="2" fill="#ECE6FF"><rect x="440" y="72" width="180" height="60" rx="12"/><rect x="440" y="176" width="180" height="54" rx="12"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="15"><text x="530" y="107">{ punti: 99 }</text><text x="530" y="208">{ punti: 0 }</text></g>
<g text-anchor="middle" font-size="13" fill="#5B5880"><text x="530" y="62">{$t[3]}</text><text x="530" y="250">{$t[4]}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#jbra)"><path d="M317 76C380 76 390 92 436 96"/><path d="M317 136C380 136 390 114 436 110"/><path d="M317 206C380 206 400 204 436 204"/></g>
</svg>
SVG;
    }
}

return [
    'id' => 'js-base', 'family' => 'js', 'after' => 'html-base',
    'title' => L('JavaScript base', 'JavaScript Basics'), 'short' => L('JS base', 'JS Basics'), 'level' => L('Base', 'Basics'),
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
            P('Ogni variabile va dichiarata prima di usarla, altrimenti ottieni un `ReferenceError`. I nomi distinguono maiuscole e minuscole (`punti` e `Punti` sono due variabili diverse), non possono iniziare con un numero e di solito si scrivono in *camelCase*: `puntiTotali`. `undefined` significa «non ancora assegnato», `null` invece lo scrivi tu per dire «vuoto, apposta». `typeof` ti dice di che tipo è un valore: utile quando qualcosa non torna.', 'Every variable must be declared before you use it, otherwise you get a `ReferenceError`. Names are case-sensitive (`punti` and `Punti` are two different variables), cannot start with a digit and are usually written in *camelCase*: `puntiTotali`. `undefined` means “not assigned yet”, while `null` is something you write yourself to say “empty, on purpose”. `typeof` tells you the type of a value: handy when something does not add up.'),
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
            P('Ci sono anche le scorciatoie: `x += 5` vale `x = x + 5`, `x++` aggiunge uno. Perché evitare `==`? Prima di confrontare converte i tipi con regole difficili da ricordare: `0 == ""` e `null == undefined` sono veri, e un bug così è difficile da trovare. Con `===` (e il suo contrario `!==`) quello che vedi è quello che ottieni. Come in matematica, `*` e `/` vengono prima di `+` e `-`: nel dubbio usa le parentesi.', 'There are shortcuts too: `x += 5` means `x = x + 5`, `x++` adds one. Why avoid `==`? Before comparing it converts the types with rules that are hard to remember: `0 == ""` and `null == undefined` are true, and a bug like that is hard to find. With `===` (and its opposite `!==`) what you see is what you get. As in maths, `*` and `/` come before `+` and `-`: when in doubt, use brackets.'),
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
            Tip('Con `else if` vince il **primo** ramo vero e gli altri vengono saltati: metti prima i casi più stretti. Se controlli `punti >= 50` prima di `punti >= 100`, il «Grande!» non arriva mai.', 'With `else if` the **first** true branch wins and the others are skipped: put the narrower cases first. If you check `punti >= 50` before `punti >= 100`, “Grande!” never shows up.'),
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
            P('Le parentesi di `for` hanno tre parti separate da `;`: la **partenza** (`let i = 0`), eseguita una volta sola, la **condizione** (`i < 3`), controllata prima di ogni giro, e il **passo** (`i++`), eseguito dopo ogni giro. Quando la condizione diventa falsa il ciclo finisce e il programma prosegue.', 'The brackets of `for` hold three parts separated by `;`: the **start** (`let i = 0`), run only once, the **condition** (`i < 3`), checked before every round, and the **step** (`i++`), run after every round. When the condition becomes false the loop ends and the program moves on.'),
            Fig(L(fig_jsbase_loop('it'), fig_jsbase_loop('en')),
                L('Schema a ciclo: la partenza «let i = 0» porta alla condizione «i < 3». Se è vera si esegue il corpo, poi il passo «i++», poi si torna alla condizione; se è falsa il ciclo finisce.', 'A cycle diagram: the start “let i = 0” leads to the condition “i < 3”. If it is true the body runs, then the step “i++”, then back to the condition; if it is false the loop ends.'),
                L('La partenza si esegue una volta; condizione, corpo e passo girano finché la condizione è vera.', 'The start runs once; condition, body and step go round while the condition is true.')),
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
            P('Perché le funzioni? Scrivi una cosa una volta e la usi ovunque: se c’è un errore lo correggi in un solo punto. Nella definizione `a` e `b` sono i **parametri**, i valori che passi nella chiamata `somma(2, 3)` sono gli **argomenti**. Errore tipico: dimenticare le parentesi. `somma` da sola è la funzione stessa, solo `somma()` la esegue.', 'Why functions? You write something once and use it everywhere: if there is a mistake you fix it in one place. In the definition `a` and `b` are the **parameters**, the values you pass in the call `somma(2, 3)` are the **arguments**. A typical mistake: forgetting the brackets. `somma` alone is the function itself, only `somma()` runs it.'),
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
            Fig(L(fig_jsbase_array('it'), fig_jsbase_array('en')),
                L('L’array corsi come una fila di caselle: «HTML» all’indice 0, «CSS» all’1, «JS» al 2, length uguale a 3. push aggiunge «TS» in una nuova casella all’indice 3, pop toglie l’ultima casella.', 'The array corsi as a row of cells: “HTML” at index 0, “CSS” at 1, “JS” at 2, length equal to 3. push adds “TS” in a new cell at index 3, pop takes the last cell away.'),
                L('Gli indici partono da 0: `push` e `pop` lavorano sempre in fondo.', 'Indexes start at 0: `push` and `pop` always work at the end.')),
            C('const corsi = ["HTML", "CSS"];
corsi.push("JS");
corsi[0];        // "HTML"
corsi.length;    // 3
corsi.includes("CSS"); // true
corsi.indexOf("JS");   // 2', 'js'),
            P('Anche se `const`, un array può cambiare contenuto: `const` blocca solo la riassegnazione della variabile.', 'Even if `const`, an array can change its content: `const` only blocks reassigning the variable.'),
            P('L’ultimo elemento è sempre a `length - 1`, oppure lo prendi con `corsi.at(-1)`. Se leggi un indice che non esiste, come `corsi[10]`, non ottieni un errore ma `undefined`: controlla sempre `length` prima di fidarti di un indice.', 'The last item is always at `length - 1`, or you can get it with `corsi.at(-1)`. If you read an index that does not exist, such as `corsi[10]`, you get no error but `undefined`: always check `length` before trusting an index.'),
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
            P('Array o oggetto? Un array è una lista di cose simili in ordine, un oggetto descrive **una** cosa con le sue proprietà. Per aggiungere una chiave basta assegnarla (`utente.livello = 2`), per toglierla c’è `delete utente.livello`. Leggere una chiave che non c’è dà `undefined`: occhio agli errori di battitura, `utente.pnuti` non dà errore.', 'Array or object? An array is a list of similar things in order, an object describes **one** thing with its properties. To add a key you just assign it (`utente.livello = 2`), to remove it there is `delete utente.livello`. Reading a key that is not there gives `undefined`: watch out for typos, `utente.pnuti` gives no error.'),
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
            Fig(L(fig_jsbase_slice('it'), fig_jsbase_slice('en')),
                L('La parola «Fucina» in sei caselle, con gli indici da 0 a 5 sopra e da -6 a -1 sotto. Le caselle «u» e «c», indici 1 e 2, sono evidenziate: slice(1, 3) restituisce «uc».', 'The word “Fucina” in six cells, with indexes 0 to 5 above and -6 to -1 below. The cells “u” and “c”, indexes 1 and 2, are highlighted: slice(1, 3) gives back “uc”.'),
                L('`slice` taglia tra due posizioni: l’inizio è compreso, la fine no.', '`slice` cuts between two positions: the start is included, the end is not.')),
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
            Fig(L(fig_jsbase_refs('it'), fig_jsbase_refs('en')),
                L('A sinistra x e y hanno ciascuna il proprio 5. A destra le variabili a e b puntano con una freccia allo stesso oggetto { punti: 99 }, mentre c punta a un oggetto diverso { punti: 0 }.', 'On the left x and y each hold their own 5. On the right the variables a and b point with an arrow to the same object { punti: 99 }, while c points to a different object { punti: 0 }.'),
                L('Una variabile con un oggetto contiene una freccia, non l’oggetto: copiare la variabile copia la freccia.', 'A variable with an object holds an arrow, not the object: copying the variable copies the arrow.')),
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
