<?php
/** Python basics: values and variables, conditions, loops, lists and functions, strings, input and types, tuples and sets, built-in functions. */

/** Variables as name tags pointing at values; giving a new value moves the tag. */
if (!function_exists('fig_pybase_names')) {
    function fig_pybase_names(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 236" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="an" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="90" y="30" text-anchor="middle" font-weight="700" fill="#5B33C9">{$t[0]}</text>
<text x="420" y="30" text-anchor="middle" font-weight="700" fill="#1B66BC">{$t[1]}</text>
<g font-family="ui-monospace, monospace" font-size="15">
<rect x="30" y="56" width="120" height="40" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="90" y="81" text-anchor="middle">nome</text>
<rect x="30" y="130" width="120" height="40" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="90" y="155" text-anchor="middle">eta</text>
<rect x="330" y="56" width="140" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="400" y="81" text-anchor="middle">"Ada"</text>
<text x="484" y="81" font-size="13" fill="#5B5880">str</text>
<rect x="330" y="130" width="140" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/><text x="400" y="155" text-anchor="middle" fill="#8A87B0">36</text>
<rect x="330" y="180" width="140" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="400" y="205" text-anchor="middle">37</text>
<text x="484" y="205" font-size="13" fill="#5B5880">int</text>
</g>
<line x1="154" y1="76" x2="324" y2="76" stroke="#1E1C52" stroke-width="2" marker-end="url(#an)"/>
<line x1="154" y1="150" x2="324" y2="150" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/>
<path d="M154 156 C240 160 260 200 324 200" fill="none" stroke="#E8590C" stroke-width="2" marker-end="url(#an)"/>
<text x="300" y="226" text-anchor="end" font-size="13" fill="#B84508" font-family="ui-monospace, monospace">eta = 37</text>
<text x="30" y="214" font-size="13" fill="#5B5880">{$t[2]}</text>
</svg>
SVG;
    }
}

/** The path of voto = 8 through if, elif and else. */
if (!function_exists('fig_pybase_if')) {
    function fig_pybase_if(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="ui-monospace, monospace" font-size="14" fill="#1E1C52">
<defs><marker id="ai" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker>
<marker id="ai2" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="4.5" markerHeight="4.5" orient="auto"><path d="M0 0L10 5L0 10z" fill="#E8590C"/></marker></defs>
<rect x="10" y="50" width="104" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="62" y="75" text-anchor="middle">voto = 8</text>
<path d="M210 30 L290 70 L210 110 L130 70 Z" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/><text x="210" y="75" text-anchor="middle">voto &gt;= 9</text>
<path d="M400 30 L480 70 L400 110 L320 70 Z" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="400" y="75" text-anchor="middle">voto &gt;= 6</text>
<rect x="140" y="170" width="140" height="44" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="210" y="197" text-anchor="middle" fill="#5B5880">"Ottimo"</text>
<rect x="330" y="170" width="140" height="44" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="3"/><text x="400" y="197" text-anchor="middle" font-weight="700">"Promosso"</text>
<rect x="500" y="170" width="130" height="44" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="565" y="197" text-anchor="middle" fill="#5B5880">"Da ripassare"</text>
<line x1="116" y1="70" x2="126" y2="70" stroke="#E8590C" stroke-width="3" marker-end="url(#ai2)"/>
<line x1="292" y1="70" x2="316" y2="70" stroke="#E8590C" stroke-width="3" marker-end="url(#ai2)"/>
<line x1="210" y1="112" x2="210" y2="164" stroke="#8A87B0" stroke-width="2" marker-end="url(#ai)"/>
<line x1="400" y1="112" x2="400" y2="164" stroke="#E8590C" stroke-width="3" marker-end="url(#ai2)"/>
<path d="M482 70 H565 V164" fill="none" stroke="#8A87B0" stroke-width="2" marker-end="url(#ai)"/>
<g font-family="system-ui, sans-serif" font-size="13">
<text x="304" y="54" text-anchor="middle" fill="#B84508">{$t[1]}</text>
<text x="218" y="144" fill="#5B5880">{$t[0]}</text>
<text x="408" y="144" fill="#B84508" font-weight="700">{$t[0]}</text>
<text x="520" y="60" text-anchor="middle" fill="#5B5880">{$t[1]}</text>
<text x="320" y="242" text-anchor="middle" font-size="14" fill="#5B5880">{$t[2]}</text>
</g>
</svg>
SVG;
    }
}

/** A list with its positive and negative indexes. */
if (!function_exists('fig_pybase_list')) {
    function fig_pybase_list(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 180" font-family="ui-monospace, monospace" font-size="15" fill="#1E1C52">
<text x="60" y="94" text-anchor="end" font-weight="700">frutta</text>
<g text-anchor="middle">
<text x="150" y="44" fill="#1B66BC">0</text><text x="290" y="44" fill="#1B66BC">1</text><text x="430" y="44" fill="#1B66BC">2</text>
<rect x="80" y="64" width="140" height="48" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="150" y="94">"mela"</text>
<rect x="220" y="64" width="140" height="48" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="290" y="94">"pera"</text>
<rect x="360" y="64" width="140" height="48" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="430" y="94">"kiwi"</text>
<text x="150" y="144" fill="#B84508">-3</text><text x="290" y="144" fill="#B84508">-2</text><text x="430" y="144" fill="#B84508">-1</text>
</g>
<text x="520" y="44" font-size="14" fill="#1B66BC">frutta[0]</text>
<text x="520" y="144" font-size="14" fill="#B84508">frutta[-1]</text>
<text x="520" y="94" font-size="14" fill="#5B5880">len = 3</text>
</svg>
SVG;
    }
}

/** Slicing: the indexes sit between the characters; the start is included, the end excluded. */
if (!function_exists('fig_pybase_slice')) {
    function fig_pybase_slice(): string
    {
        $cells = '';
        foreach (preg_split('//u', 'Ciao, Mondo!', -1, PREG_SPLIT_NO_EMPTY) as $i => $ch) {
            $x = 56 + $i * 44;
            $fill = $i < 4 ? '#FFE8D6' : ($i >= 6 ? '#D5F6EA' : '#F1F0F8');
            $cells .= "<rect x=\"$x\" y=\"84\" width=\"44\" height=\"44\" fill=\"$fill\" stroke=\"#8A87B0\" stroke-width=\"2\"/><text x=\"" . ($x + 22) . "\" y=\"112\" text-anchor=\"middle\" font-size=\"17\">" . ($ch === ' ' ? '␣' : $ch) . "</text>";
        }
        $idx = '';
        for ($i = 0; $i <= 12; $i++) {
            $x = 56 + $i * 44;
            $idx .= "<text x=\"$x\" y=\"150\" text-anchor=\"middle\" font-size=\"13\" fill=\"#1B66BC\">$i</text>";
            if ($i < 12) $idx .= "<text x=\"$x\" y=\"172\" text-anchor=\"middle\" font-size=\"13\" fill=\"#B84508\">" . ($i - 12) . "</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 190" font-family="ui-monospace, monospace" font-size="15" fill="#1E1C52">
<path d="M56 72 V60 H232 V72" fill="none" stroke="#E8590C" stroke-width="2"/>
<text x="144" y="48" text-anchor="middle" fill="#B84508">pulita[0:4] → "Ciao"</text>
<path d="M320 72 V60 H584 V72" fill="none" stroke="#0B7F5C" stroke-width="2"/>
<text x="452" y="48" text-anchor="middle" fill="#0B7F5C">pulita[-6:] → "Mondo!"</text>
$cells
$idx
</svg>
SVG;
    }
}

/** Two sets as overlapping circles: & keeps the overlap, | everything. */
if (!function_exists('fig_pybase_sets')) {
    function fig_pybase_sets(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="ui-monospace, monospace" font-size="16" fill="#1E1C52">
<circle cx="250" cy="115" r="90" fill="#DCEBFF" fill-opacity="0.8" stroke="#2F8CF0" stroke-width="2"/>
<circle cx="370" cy="115" r="90" fill="#FFE8D6" fill-opacity="0.6" stroke="#E8590C" stroke-width="2"/>
<text x="210" y="20" text-anchor="middle" font-weight="700" fill="#1B66BC">pari</text>
<text x="410" y="20" text-anchor="middle" font-weight="700" fill="#B84508">piccoli</text>
<text x="215" y="100" text-anchor="middle">4</text><text x="215" y="140" text-anchor="middle">6</text>
<text x="310" y="122" text-anchor="middle" font-weight="700" font-size="20">2</text>
<text x="405" y="100" text-anchor="middle">1</text><text x="405" y="140" text-anchor="middle">3</text>
<text x="20" y="244" font-size="14">pari &amp; piccoli → {2}</text>
<text x="620" y="244" text-anchor="end" font-size="14">pari | piccoli → {1, 2, 3, 4, 6}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'python-base', 'family' => 'python', 'after' => null,
    'title' => L('Python base', 'Python Basics'), 'short' => L('Python base', 'Python Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio più amato per iniziare: variabili, condizioni, cicli, liste e funzioni, stringhe, input e tipi, tuple e insiemi, funzioni integrate.', 'The best-loved language to start with: variables, conditions, loops, lists and functions, strings, input and types, tuples and sets, built-in functions.'),
    'lessons' => [
        lesson('hello', L('Ciao, Python', 'Hello, Python'), [
            P('Python si legge quasi come l’inglese. `print()` scrive sullo schermo, una **variabile** nasce quando le dai un valore con `=`. Non servono punti e virgola né tipi da dichiarare.', 'Python reads almost like English. `print()` writes to the screen, a **variable** is born when you give it a value with `=`. No semicolons, no types to declare.'),
            C(L('nome = "Ada"
eta = 36
print("Ciao", nome)
print(f"{nome} ha {eta} anni")', 'nome = "Ada"
eta = 36
print("Hello", nome)
print(f"{nome} is {eta} years old")'), 'python', L('<pre>Ciao Ada
Ada ha 36 anni</pre>', '<pre>Hello Ada
Ada is 36 years old</pre>')),
            P('Le **f-string** (con la `f` davanti) mettono i valori dentro il testo tra graffe. I commenti iniziano con `#`.', '**f-strings** (with an `f` in front) put values inside the text between braces. Comments start with `#`.'),
            P('Una variabile è un **nome** attaccato a un valore, come un’etichetta: se scrivi `eta = 37`, l’etichetta si sposta sul nuovo valore. Può anche passare a un valore di un altro tipo, perché il tipo appartiene al valore, non al nome. I nomi distinguono maiuscole e minuscole (`Nome` e `nome` sono due variabili diverse) e per convenzione si scrivono in minuscolo con il trattino basso: `punti_totali`.', 'A variable is a **name** attached to a value, like a label: if you write `eta = 37`, the label moves to the new value. It can even move to a value of another type, because the type belongs to the value, not to the name. Names are case-sensitive (`Nome` and `nome` are two different variables) and by convention are written in lower case with underscores: `punti_totali`.'),
            Fig(L(fig_pybase_names(['Nomi', 'Valori', 'il 36 resta senza nome']), fig_pybase_names(['Names', 'Values', '36 is left without a name'])),
                L('Il nome nome punta al valore "Ada" di tipo str. Il nome eta puntava a 36 con una freccia tratteggiata; dopo eta = 37 una freccia arancione lo collega al nuovo valore 37 di tipo int.', 'The name nome points to the value "Ada" of type str. The name eta pointed to 36 with a dashed arrow; after eta = 37 an orange arrow links it to the new value 37 of type int.'),
                L('Assegnare di nuovo sposta l’etichetta, non cambia il vecchio valore.', 'Assigning again moves the label, it does not change the old value.')),
            Tip('Per provare il codice salvalo in un file e lancialo con `python3 ciao.py`, oppure scrivi `python3` nel terminale per aprire la console interattiva e vedere subito il risultato di ogni riga.', 'To try the code save it in a file and run it with `python3 ciao.py`, or type `python3` in the terminal to open the interactive console and see the result of each line straight away.'),
        ], [
            Q('h1', L('Quale funzione scrive sullo schermo?', 'Which function writes to the screen?'), ['print()', 'echo()', 'console.log()', 'write()'], 0, L('In Python è `print`.', 'In Python it is `print`.')),
            Q('h2', L('Come si inserisce `nome` in una f-string?', 'How do you put `nome` into an f-string?'), ['f"Ciao {nome}"', '"Ciao $nome"', '"Ciao " + {nome}', 'f"Ciao (nome)"'], 0, L('La `f` davanti e le graffe dentro.', 'The `f` in front and braces inside.')),
            Q('h3', L('Con che cosa inizia un commento?', 'What does a comment start with?'), ['#', '//', '--', '/*'], 0, L('Tutto dopo `#` viene ignorato.', 'Everything after `#` is ignored.')),
        ], task('python', L('Crea `citta = "Udine"` e stampa `f"Vivo a {citta}"`.', 'Create `citta = "Udine"` and print `f"Vivo a {citta}"`.'), '', [re('citta\s*=\s*["\']Udine["\']', L('La variabile esiste', 'The variable exists')), re('print\s*\(\s*f["\']Vivo a \{citta\}["\']\s*\)', L('La stampi con una f-string', 'You print it with an f-string'))], '', 'citta = "Udine"
print(f"Vivo a {citta}")'), [
            dBlanks(pFill(), "nome = \"Bea\"\n___(___\"Ciao {nome}!\")", ['print', 'f'], ['echo', 'r', 'log'], L('Funzione e prefisso della f-string.', 'Function and f-string prefix.')),
            dFlash(pFlash(), [[L('In Python le righe finiscono con `;`', 'In Python lines end with `;`'), false], [L('Una variabile nasce con `=`', 'A variable is born with `=`'), true], [L('`#` inizia un commento', '`#` starts a comment'), true], [L('`print` mostra un testo sullo schermo', '`print` shows text on the screen'), true]], L('Niente punti e virgola.', 'No semicolons.')),
        ]),
        lesson('conditions', L('Condizioni e indentazione', 'Conditions and indentation'), [
            P('`if`, `elif` ed `else` scelgono che cosa fare. In Python i blocchi non hanno graffe: li decide l’**indentazione**, di solito 4 spazi dopo i due punti.', '`if`, `elif` and `else` choose what to do. In Python blocks have no braces: **indentation** decides them, usually 4 spaces after the colon.'),
            C('voto = 8
if voto >= 9:
    print("Ottimo")
elif voto >= 6:
    print("Promosso")
else:
    print("Da ripassare")', 'python'),
            P('Gli operatori logici sono parole: `and`, `or`, `not`.', 'Logical operators are words: `and`, `or`, `not`.'),
            Fig(L(fig_pybase_if(['sì', 'no', 'Si prova una condizione alla volta, dall’alto: entra solo il primo ramo vero.']), fig_pybase_if(['yes', 'no', 'Conditions are tried one at a time, from the top: only the first true branch runs.'])),
                L('Diagramma di flusso: voto = 8 arriva alla domanda voto >= 9, la risposta è no; alla domanda voto >= 6 la risposta è sì e si arriva a "Promosso", evidenziato in arancione. Il ramo "Ottimo" e il ramo else "Da ripassare" restano grigi.', 'Flowchart: voto = 8 reaches the question voto >= 9, the answer is no; at the question voto >= 6 the answer is yes and it reaches "Promosso", highlighted in orange. The "Ottimo" branch and the else branch "Da ripassare" stay grey.'),
                L('Con `elif` Python si ferma al primo ramo vero.', 'With `elif` Python stops at the first true branch.')),
            P('Per confrontare si usano `==`, `!=`, `<`, `<=`, `>` e `>=`; un solo `=` invece assegna, e dentro un `if` dà errore di sintassi. Python permette anche i confronti a catena come `6 <= voto < 9`. Qualunque valore può fare da condizione: `0`, la stringa vuota `""`, la lista vuota `[]` e `None` valgono falso, tutto il resto vero. Così `if nome:` significa «se il nome non è vuoto».', 'To compare you use `==`, `!=`, `<`, `<=`, `>` and `>=`; a single `=` assigns instead, and inside an `if` it is a syntax error. Python also allows chained comparisons such as `6 <= voto < 9`. Any value can act as a condition: `0`, the empty string `""`, the empty list `[]` and `None` count as false, everything else as true. So `if nome:` means “if the name is not empty”.'),
            Tip('L’indentazione è parte della sintassi: se una riga è rientrata in modo diverso dalle altre dello stesso blocco ottieni `IndentationError`. Usa sempre 4 spazi e non mischiarli con i tab.', 'Indentation is part of the syntax: if one line is indented differently from the others in the same block you get `IndentationError`. Always use 4 spaces and do not mix them with tabs.'),
        ], [
            Q('c1', L('Come si scrive “altrimenti se”?', 'How do you write “else if”?'), ['elif', 'else if', 'elseif', 'elsif'], 0, L('Una parola sola.', 'A single word.')),
            Q('c2', L('Che cosa decide i blocchi in Python?', 'What decides blocks in Python?'), [L('L’indentazione', 'Indentation'), L('Le graffe', 'Braces'), L('Le parentesi', 'Parentheses'), L('I punti e virgola', 'Semicolons')], 0, L('Lo spazio a inizio riga conta.', 'Leading space matters.')),
            Q('c3', L('Come si scrive “e” logico?', 'How do you write logical “and”?'), ['and', '&&', '&', 'AND'], 0, L('Python usa parole.', 'Python uses words.')),
        ], task('python', L('Se `eta >= 18` stampa `"Adulto"`, altrimenti `"Minore"`.', 'If `eta >= 18` print `"Adulto"`, otherwise `"Minore"`.'), 'eta = 20
', [re('if\s+eta\s*>=\s*18\s*:', L('La condizione è giusta', 'The condition is right')), re('print\s*\(\s*["\']Adulto["\']\s*\)', L('Stampi “Adulto”', 'You print “Adulto”')), re('else\s*:\s*\n\s+print\s*\(\s*["\']Minore["\']\s*\)', L('E “Minore” altrimenti', 'And “Minore” otherwise'))], '', 'if eta >= 18:
    print("Adulto")
else:
    print("Minore")'), [
            dBug(pBug(), ['temperatura = 30', 'if temperatura > 25', '    print("Fa caldo")'], 1, 'if temperatura > 25:', L('Dopo la condizione servono i due punti.', 'The condition needs a colon after it.')),
            dMatch(pMatch(), [['and', L('Entrambe vere', 'Both true')], ['or', L('Almeno una vera', 'At least one true')], ['not', L('Il contrario', 'The opposite')], ['==', L('Uguale', 'Equal')], ['!=', L('Diverso', 'Not equal')]], L('Gli operatori di confronto e logici.', 'Comparison and logical operators.')),
        ]),
        lesson('loops', L('Cicli', 'Loops'), [
            P('`for` ripete per ogni elemento di una sequenza; `range(5)` dà i numeri da 0 a 4. `while` ripete finché una condizione è vera.', '`for` repeats for each item of a sequence; `range(5)` gives the numbers from 0 to 4. `while` repeats while a condition is true.'),
            C(L('for i in range(1, 4):
    print("Giro", i)

n = 3
while n > 0:
    print(n)
    n -= 1
print("Via!")', 'for i in range(1, 4):
    print("Round", i)

n = 3
while n > 0:
    print(n)
    n -= 1
print("Go!")'), 'python', L('<pre>Giro 1
Giro 2
Giro 3
3
2
1
Via!</pre>', '<pre>Round 1
Round 2
Round 3
3
2
1
Go!</pre>')),
            P('`break` esce dal ciclo, `continue` salta al giro successivo.', '`break` leaves the loop, `continue` skips to the next round.'),
            P('`range(1, 4)` parte da 1 e si ferma **prima** di 4: il numero finale non è mai incluso, come nello slicing. Per scorrere una lista non servono gli indici: `for frutto in frutta:` dà direttamente ogni elemento. Con `while`, invece, sei tu a dover cambiare qualcosa a ogni giro (qui `n -= 1`): se la condizione resta sempre vera, il ciclo non finisce più e devi fermarlo con Ctrl+C.', '`range(1, 4)` starts from 1 and stops **before** 4: the final number is never included, just like in slicing. To walk a list you do not need indexes: `for frutto in frutta:` gives you each item directly. With `while`, instead, it is up to you to change something on every round (here `n -= 1`): if the condition stays true forever, the loop never ends and you have to stop it with Ctrl+C.'),
            Tip('Usa `for` quando sai su che cosa ripetere (una lista, un intervallo di numeri) e `while` quando non sai quante volte servirà, per esempio finché l’utente non scrive «esci».', 'Use `for` when you know what to repeat over (a list, a range of numbers) and `while` when you do not know how many times it will take, for example until the user types “quit”.'),
        ], [
            Q('l1', L('Quali numeri dà `range(3)`?', 'Which numbers does `range(3)` give?'), ['0, 1, 2', '1, 2, 3', '0, 1, 2, 3', '3'], 0, L('Si parte da 0 e ci si ferma prima del 3.', 'It starts at 0 and stops before 3.')),
            Q('l2', L('Quale parola esce subito da un ciclo?', 'Which word leaves a loop at once?'), ['break', 'exit', 'stop', 'return'], 0, L('`continue` salta solo un giro.', '`continue` only skips one round.')),
            Q('l3', L('Che cosa fa `n -= 1`?', 'What does `n -= 1` do?'), [L('Toglie 1 a `n`', 'Takes 1 from `n`'), L('Confronta', 'Compares'), L('Stampa', 'Prints'), L('Niente', 'Nothing')], 0, L('È `n = n - 1`.', 'It is `n = n - 1`.')),
        ], task('python', L('Con `for` e `range(1, 6)` stampa i numeri da 1 a 5.', 'With `for` and `range(1, 6)` print the numbers from 1 to 5.'), '', [re('for\s+\w+\s+in\s+range\s*\(\s*1\s*,\s*6\s*\)\s*:', L('Il ciclo è giusto', 'The loop is right')), re('print\s*\(\s*\w+\s*\)', L('Stampi il numero', 'You print the number'))], '', 'for n in range(1, 6):
    print(n)'), [
            dOrder(pSort(), ['totale = 0', 'for n in range(1, 11):', '    totale += n', 'print(totale)'], L('Prepara, ripeti, somma, mostra.', 'Set up, repeat, add, show.')),
            dFlash(pFlash(), [[L('`range(1, 4)` include il 4', '`range(1, 4)` includes 4'), false], [L('`while` può non finire mai', '`while` may never end'), true], [L('`continue` salta al giro dopo', '`continue` skips to the next round'), true], [L('`for` può scorrere i caratteri di una stringa', '`for` can go through the characters of a string'), true]], L('La fine di `range` è esclusa.', 'The end of `range` is excluded.')),
        ]),
        lesson('lists', L('Liste e dizionari', 'Lists and dictionaries'), [
            P('Una **lista** tiene più valori in ordine: `[3, 5, 7]`. Si legge con l’indice (`lista[0]` è il primo) e si allunga con `append`. Un **dizionario** associa chiavi e valori: `{"nome": "Ada"}`.', 'A **list** holds several values in order: `[3, 5, 7]`. You read it by index (`lista[0]` is the first) and grow it with `append`. A **dictionary** maps keys to values: `{"nome": "Ada"}`.'),
            C('frutta = ["mela", "pera"]
frutta.append("kiwi")
print(frutta[0], len(frutta))

utente = {"nome": "Ada", "eta": 36}
utente["citta"] = "Londra"
for chiave, valore in utente.items():
    print(chiave, valore)', 'python'),
            P('`lista[-1]` è l’ultimo elemento; `in` controlla se qualcosa c’è: `"pera" in frutta`.', '`lista[-1]` is the last item; `in` checks whether something is there: `"pera" in frutta`.'),
            Fig(fig_pybase_list(),
                L('Tre caselle mela, pera e kiwi. Sopra gli indici 0, 1 e 2, sotto gli indici negativi -3, -2 e -1: frutta[0] è mela, frutta[-1] è kiwi.', 'Three boxes mela, pera and kiwi. Above them the indexes 0, 1 and 2, below them the negative indexes -3, -2 and -1: frutta[0] is mela, frutta[-1] is kiwi.'),
                L('Gli indici partono da 0; quelli negativi contano dalla fine.', 'Indexes start at 0; negative ones count from the end.')),
            P('Le liste si possono modificare: `frutta[1] = "uva"` cambia il secondo elemento, `remove` toglie un valore, `pop()` toglie e restituisce l’ultimo. Leggere un indice che non esiste dà `IndexError`. In un dizionario ogni chiave compare una sola volta; leggere una chiave assente con `utente["email"]` dà `KeyError`, mentre `utente.get("email", "")` restituisce un valore di riserva.', 'Lists can be changed: `frutta[1] = "uva"` replaces the second item, `remove` deletes a value, `pop()` removes and returns the last one. Reading an index that does not exist raises `IndexError`. In a dictionary each key appears only once; reading a missing key with `utente["email"]` raises `KeyError`, while `utente.get("email", "")` returns a fallback value.'),
        ], [
            Q('d1', L('Come aggiungi un elemento in fondo a una lista?', 'How do you add an item at the end of a list?'), ['append()', 'push()', 'add()', 'insert_end()'], 0, L('`push` è JavaScript.', '`push` is JavaScript.')),
            Q('d2', L('Che cosa dà `lista[-1]`?', 'What does `lista[-1]` give?'), [L('L’ultimo elemento', 'The last item'), L('Un errore', 'An error'), L('Il primo', 'The first'), L('La lunghezza', 'The length')], 0, L('Gli indici negativi contano dalla fine.', 'Negative indexes count from the end.')),
            Q('d3', L('Come scorri chiavi e valori di un dizionario?', 'How do you loop over keys and values of a dict?'), ['for k, v in d.items():', 'for k in d.values():', 'for d in k:', 'foreach d as k => v'], 0, L('`items()` dà le coppie.', '`items()` gives the pairs.')),
        ], task('python', L('Crea `voti = [7, 8]`, aggiungi `9` con `append` e stampa `len(voti)`.', 'Create `voti = [7, 8]`, add `9` with `append` and print `len(voti)`.'), '', [re('voti\s*=\s*\[\s*7\s*,\s*8\s*\]', L('La lista esiste', 'The list exists')), re('voti\.append\s*\(\s*9\s*\)', L('Aggiungi il 9', 'You add 9')), re('print\s*\(\s*len\s*\(\s*voti\s*\)\s*\)', L('Stampi la lunghezza', 'You print the length'))], '', 'voti = [7, 8]
voti.append(9)
print(len(voti))'), [
            dMatch(pMatch(), [['[1, 2]', L('Lista', 'List')], ['{"a": 1}', L('Dizionario', 'Dictionary')], ['(1, 2)', L('Tupla, non si modifica', 'Tuple, cannot change')], ['{1, 2}', L('Insieme, senza doppioni', 'Set, no duplicates')]], L('Le collezioni di Python.', 'Python’s collections.')),
            dBlanks(pFill(), "nomi = [\"Ada\", \"Bea\"]\nnomi.___(\"Cleo\")\nprint(nomi[___])  # Ada\nprint(___(nomi))  # 3", ['append', '0', 'len'], ['push', '1', 'size'], L('Aggiungi, leggi il primo, conta.', 'Add, read the first, count.')),
        ]),
        lesson('functions', L('Funzioni', 'Functions'), [
            P('`def` crea una **funzione**: un pezzo di codice con un nome, che riceve **parametri** e restituisce un risultato con `return`. Un parametro può avere un valore predefinito.', '`def` creates a **function**: a named piece of code that takes **parameters** and gives back a result with `return`. A parameter can have a default value.'),
            C('def area(base, altezza=1):
    return base * altezza

print(area(4, 3))
print(area(5))

def saluta(nome: str) -> str:
    return f"Ciao {nome}"', 'python'),
            P('Le annotazioni come `nome: str` e `-> str` documentano i tipi: Python non le impone, ma gli strumenti le controllano.', 'Annotations such as `nome: str` and `-> str` document the types: Python does not enforce them, but tools check them.'),
            P('Una funzione senza `return` restituisce `None`: è l’errore tipico di chi scrive `print` al posto di `return`, perché il valore appare sullo schermo ma non si può usare dopo. Le variabili create dentro la funzione sono **locali** e spariscono alla fine della chiamata. Quando chiami una funzione puoi anche nominare gli argomenti, `area(altezza=3, base=4)`: l’ordine non conta più e il codice si legge meglio.', 'A function without `return` gives back `None`: it is the typical mistake of writing `print` instead of `return`, because the value shows on screen but cannot be used afterwards. Variables created inside the function are **local** and vanish when the call ends. When you call a function you can also name the arguments, `area(altezza=3, base=4)`: order no longer matters and the code reads better.'),
        ], [
            Q('f1', L('Quale parola crea una funzione?', 'Which word creates a function?'), ['def', 'function', 'fun', 'fn'], 0, L('Da “define”.', 'From “define”.')),
            Q('f2', L('Che cosa dà `area(5)` con `altezza=1`?', 'What does `area(5)` give with `altezza=1`?'), ['5', '0', L('Un errore', 'An error'), '6'], 0, L('Usa il valore predefinito.', 'It uses the default value.')),
            Q('f3', L('Le annotazioni di tipo sono obbligatorie?', 'Are type annotations required?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo per i numeri', 'Only for numbers'), L('Solo nelle classi', 'Only in classes')], 0, L('Aiutano lettori e strumenti.', 'They help readers and tools.')),
        ], task('python', L('Scrivi `def doppio(n):` che restituisce `n * 2`.', 'Write `def doppio(n):` that returns `n * 2`.'), '', [re('def\s+doppio\s*\(\s*n\s*\)\s*(->\s*\w+\s*)?:', L('La funzione esiste', 'The function exists')), re('return\s+n\s*\*\s*2', L('Restituisce il doppio', 'It returns the double'))], '', 'def doppio(n):
    return n * 2'), [
            dBug(pBug(), ['def media(numeri):', '    totale = sum(numeri)', '    totale / len(numeri)', 'print(media([6, 8]))'], 2, '    return totale / len(numeri)', L('Senza `return` la funzione restituisce `None`.', 'Without `return` the function gives back `None`.')),
            dOrder(pSort(), ['def benvenuto(nome, lingua="it"):', '    if lingua == "it":', '        return f"Ciao {nome}"', '    return f"Hello {nome}"', 'print(benvenuto("Ada"))'], L('Definisci, decidi, restituisci, chiama.', 'Define, decide, return, call.')),
        ]),
        lesson('strings', L('Stringhe e slicing', 'Strings and slicing'), [
            P('Le stringhe hanno tanti metodi pronti: `strip` toglie gli spazi ai lati, `upper` e `lower` cambiano le maiuscole, `replace` sostituisce, `split` divide in una lista e `join` riunisce. Con lo **slicing** `testo[inizio:fine]` prendi un pezzo di stringa.', 'Strings have many ready-made methods: `strip` trims spaces at the ends, `upper` and `lower` change case, `replace` substitutes, `split` breaks into a list and `join` puts it back together. With **slicing** `text[start:end]` you take a piece of a string.'),
            C('frase = "  Ciao, Mondo!  "
pulita = frase.strip()           # "Ciao, Mondo!"
print(pulita.upper())            # CIAO, MONDO!
print(pulita.replace("Mondo", "Ada"))
print(pulita[0:4])               # Ciao
print(pulita[-6:])               # Mondo!
print(len(pulita))               # 12

parole = "a,b,c".split(",")      # ["a", "b", "c"]
print("-".join(parole))          # a-b-c', 'python'),
            P('Le stringhe sono **immutabili**: i metodi non le cambiano, ne restituiscono una nuova. Nello slicing l’inizio è incluso e la fine esclusa; gli indici negativi contano dalla fine.', 'Strings are **immutable**: methods do not change them, they return a new one. In slicing the start is included and the end excluded; negative indexes count from the end.'),
            Fig(fig_pybase_slice(),
                L('Le dodici lettere di "Ciao, Mondo!" in caselle, con i numeri da 0 a 12 sui bordi tra una casella e l’altra e, sotto, gli indici negativi da -12 a -1. Una parentesi arancione copre le caselle da 0 a 4 (Ciao), una verde quelle da -6 alla fine (Mondo!).', 'The twelve characters of "Ciao, Mondo!" in boxes, with the numbers 0 to 12 on the edges between boxes and, below, the negative indexes -12 to -1. An orange bracket covers boxes 0 to 4 (Ciao), a green one those from -6 to the end (Mondo!).'),
                L('Pensa agli indici come ai bordi tra le lettere: `[0:4]` prende ciò che sta tra il bordo 0 e il bordo 4.', 'Think of indexes as the edges between letters: `[0:4]` takes what lies between edge 0 and edge 4.')),
        ], [
            Q('s1', L('Che cosa dà `"Python"[0:2]`?', 'What does `"Python"[0:2]` give?'), ['"Py"', '"Pyt"', '"yt"', '"P"'], 0, L('Le posizioni 0 e 1: la 2 è esclusa.', 'Positions 0 and 1: 2 is excluded.')),
            Q('s2', L('Che cosa fa `" ciao ".strip()`?', 'What does `" ciao ".strip()` do?'), [L('Divide la stringa in parole', 'Splits the string into words'), L('Mette tutto in maiuscolo', 'Uppercases everything'), L('Toglie gli spazi all’inizio e alla fine', 'Removes spaces at the start and end'), L('Conta gli spazi presenti', 'Counts the spaces')], 2, L('Utile per pulire ciò che scrive l’utente.', 'Handy for cleaning what the user types.')),
            Q('s3', L('Che cosa dà `"a-b".split("-")`?', 'What does `"a-b".split("-")` give?'), ['"ab"', '["a", "b"]', '("a", "b")', '["a-b"]'], 1, L('Una lista con i pezzi tra i separatori.', 'A list with the pieces between separators.')),
        ], task('python', L('Da `nome` ottieni in `pulito` il testo senza spazi ai lati e con le iniziali maiuscole, usando `strip()` e `title()`.', 'From `nome` get into `pulito` the text without spaces at the ends and with capitalised initials, using `strip()` and `title()`.'), 'nome = "  ada lovelace "
', [re('pulito\s*=\s*nome\.strip\s*\(\s*\)\.title\s*\(\s*\)|pulito\s*=\s*nome\.title\s*\(\s*\)\.strip\s*\(\s*\)', L('Pulisci e metti le iniziali maiuscole', 'You trim and capitalise'))], '', 'pulito = nome.strip().title()'), [
            dMatch(pMatch(), [['upper()', L('Tutto maiuscolo', 'All uppercase')], ['strip()', L('Toglie gli spazi ai lati', 'Trims spaces at the ends')], ['split()', L('Divide in una lista', 'Splits into a list')], ['join()', L('Unisce una lista', 'Joins a list')], ['replace()', L('Sostituisce', 'Replaces')]], L('I metodi più usati delle stringhe.', 'The most used string methods.')),
            dFlash(pFlash(), [[L('`"ciao"[1]` è `"i"`', '`"ciao"[1]` is `"i"`'), true], [L('Una stringa si può modificare carattere per carattere', 'A string can be changed character by character'), false], [L('`"abc"[-1]` è `"c"`', '`"abc"[-1]` is `"c"`'), true], [L('`s[0:3]` include il carattere in posizione 3', '`s[0:3]` includes the character at position 3'), false]], L('Inizio incluso, fine esclusa.', 'Start included, end excluded.')),
        ]),
        lesson('input-types', L('Input e conversioni di tipo', 'Input and type conversion'), [
            P('`input()` mostra una domanda e aspetta che l’utente scriva. Restituisce sempre una **stringa**: per fare calcoli va convertita con `int()` o `float()`. `type()` dice di che tipo è un valore.', '`input()` shows a question and waits for the user to type. It always returns a **string**: to do maths it must be converted with `int()` or `float()`. `type()` tells you the type of a value.'),
            C('nome = input("Come ti chiami? ")
anni = int(input("Quanti anni hai? "))
altezza = float("1.75")
print(type(anni))                 # <class \'int\'>
print(str(anni) + " anni")

print(10 / 4, 10 // 4, 10 % 4)    # 2.5 2 2
print(bool(0), bool("ciao"))      # False True', 'python'),
            P('`/` dà sempre un decimale, `//` la divisione intera, `%` il resto. Sommare una stringa e un numero dà `TypeError`: Python non converte da solo. Se il testo non è un numero, `int()` lancia `ValueError`.', '`/` always gives a decimal, `//` integer division, `%` the remainder. Adding a string and a number raises `TypeError`: Python does not convert by itself. If the text is not a number, `int()` raises `ValueError`.'),
        ], [
            Q('i1', L('Che tipo restituisce `input()`?', 'What type does `input()` return?'), ['int', 'str', 'float', 'bool'], 1, L('Anche se l’utente scrive un numero.', 'Even if the user types a number.')),
            Q('i2', L('Quanto fa `7 // 2`?', 'What is `7 // 2`?'), ['3.5', '4', '3', '1'], 2, L('Divisione intera: la parte decimale sparisce.', 'Integer division: the decimal part goes away.')),
            Q('i3', L('Che cosa succede con `"5" + 3`?', 'What happens with `"5" + 3`?'), [L('Dà 8', 'It gives 8'), L('Dà "53"', 'It gives "53"'), L('Dà 5.3', 'It gives 5.3'), L('Un errore TypeError', 'A TypeError')], 3, L('Prima converti: `int("5") + 3`.', 'Convert first: `int("5") + 3`.')),
        ], task('python', L('Leggi un numero con `input`, convertilo con `int` in `n` e stampa il suo doppio.', 'Read a number with `input`, convert it with `int` into `n` and print its double.'), '', [re('n\s*=\s*int\s*\(\s*input\s*\(', L('Leggi e converti', 'You read and convert')), re('print\s*\(\s*(n\s*\*\s*2|2\s*\*\s*n)\s*\)', L('Stampi il doppio', 'You print the double'))], '', 'n = int(input("Numero: "))
print(n * 2)'), [
            dMatch(pMatch(), [['int("4")', '4'], ['float("2.5")', '2.5'], ['str(7)', '"7"'], ['10 // 3', '3'], ['10 % 3', '1']], L('Conversioni e divisioni.', 'Conversions and divisions.')),
            dBug(pBug(), ['prezzo = "9.5"', 'totale = int(prezzo) * 2', 'print(totale)'], 1, 'totale = float(prezzo) * 2', L('`int("9.5")` dà `ValueError`: per i decimali serve `float`.', '`int("9.5")` raises `ValueError`: decimals need `float`.')),
        ]),
        lesson('tuples-sets', L('Tuple, insiemi e spacchettamento', 'Tuples, sets and unpacking'), [
            P('Una **tupla** `(3, 4)` è come una lista ma non si modifica: perfetta per valori che stanno insieme, come coordinate. Con lo **spacchettamento** `x, y = punto` assegni ogni elemento a una variabile. Un **insieme** (`set`) non ha doppioni né ordine.', 'A **tuple** `(3, 4)` is like a list but cannot change: perfect for values that belong together, such as coordinates. With **unpacking** `x, y = point` you assign each item to a variable. A **set** has no duplicates and no order.'),
            C('punto = (3, 4)
x, y = punto                      # spacchettamento
a, b = 1, 2
a, b = b, a                       # scambio: a vale 2, b vale 1

colori = {"rosso", "verde", "rosso"}
print(len(colori))                # 2: niente doppioni
colori.add("blu")
print("verde" in colori)          # True

pari = {2, 4, 6}
piccoli = {1, 2, 3}
print(pari & piccoli)             # {2}
print(pari | piccoli)             # {1, 2, 3, 4, 6}', 'python'),
            P('Controllare se un elemento è in un insieme è velocissimo, anche con milioni di elementi. Attenzione: `{}` crea un **dizionario** vuoto; l’insieme vuoto si scrive `set()`.', 'Checking whether an item is in a set is very fast, even with millions of items. Watch out: `{}` creates an empty **dictionary**; the empty set is written `set()`.'),
            Fig(fig_pybase_sets(),
                L('Due cerchi sovrapposti: pari contiene 4 e 6, piccoli contiene 1 e 3, e nella parte comune c’è il 2. Sotto: pari & piccoli dà {2}, pari | piccoli dà {1, 2, 3, 4, 6}.', 'Two overlapping circles: pari holds 4 and 6, piccoli holds 1 and 3, and the shared area holds 2. Below: pari & piccoli gives {2}, pari | piccoli gives {1, 2, 3, 4, 6}.'),
                L('`&` tiene solo ciò che è in entrambi, `|` mette insieme tutto.', '`&` keeps only what is in both, `|` puts everything together.')),
        ], [
            Q('u1', L('Che cosa crea `{}`?', 'What does `{}` create?'), [L('Un insieme vuoto', 'An empty set'), L('Un dizionario vuoto', 'An empty dictionary'), L('Una tupla vuota', 'An empty tuple'), L('Un errore', 'An error')], 1, L('Per l’insieme vuoto serve `set()`.', 'The empty set needs `set()`.')),
            Q('u2', L('Dopo `a, b = 1, 2` e `a, b = b, a`, quanto vale `a`?', 'After `a, b = 1, 2` and `a, b = b, a`, what is `a`?'), ['(2, 1)', '1', 'None', '2'], 3, L('Le due variabili si scambiano i valori.', 'The two variables swap values.')),
            Q('u3', L('Che cosa dà `len({1, 1, 2})`?', 'What does `len({1, 1, 2})` give?'), ['3', '1', '2', '0'], 2, L('Il doppione 1 conta una volta sola.', 'The duplicate 1 counts only once.')),
        ], task('python', L('Togli i doppioni da `nomi` trasformandola nell’insieme `unici`, poi stampa quanti sono con `len`.', 'Remove duplicates from `nomi` by turning it into the set `unici`, then print how many there are with `len`.'), 'nomi = ["Ada", "Bea", "Ada", "Cleo"]
', [re('unici\s*=\s*set\s*\(\s*nomi\s*\)', L('Crei l’insieme', 'You create the set')), re('print\s*\(\s*len\s*\(\s*unici\s*\)\s*\)', L('Stampi quanti sono', 'You print how many'))], '', 'unici = set(nomi)
print(len(unici))'), [
            dBlanks(pFill(), 'citta = ("Udine", "Trieste")
prima, seconda = ___
visti = ___()
visti.___("Udine")', ['citta', 'set', 'add'], ['tuple', 'dict', 'append'], L('Spacchetta, crea un insieme vuoto, aggiungi.', 'Unpack, create an empty set, add.')),
            dFlash(pFlash(), [[L('Una tupla si può modificare dopo la creazione', 'A tuple can be changed after creation'), false], [L('Un set non contiene doppioni', 'A set holds no duplicates'), true], [L('`set()` crea un insieme vuoto', '`set()` creates an empty set'), true], [L('Un set mantiene l’ordine di inserimento', 'A set keeps insertion order'), false]], L('Tuple fisse, insiemi senza doppioni.', 'Fixed tuples, sets without duplicates.')),
        ]),
        lesson('builtins', L('Funzioni integrate utili', 'Handy built-in functions'), [
            P('Python ha funzioni pronte per i compiti di tutti i giorni: `sum`, `min`, `max` riassumono una lista, `sorted` la ordina, `enumerate` dà indice e valore insieme, `zip` scorre più liste in parallelo, `any` e `all` controllano una condizione su tutti gli elementi.', 'Python has ready-made functions for everyday jobs: `sum`, `min`, `max` summarise a list, `sorted` sorts it, `enumerate` gives index and value together, `zip` walks several lists side by side, `any` and `all` check a condition on every item.'),
            C('voti = [7, 9, 6, 8]
print(sum(voti), min(voti), max(voti))   # 30 6 9
print(sorted(voti))                      # [6, 7, 8, 9]
print(sorted(voti, reverse=True))        # [9, 8, 7, 6]

for i, voto in enumerate(voti, start=1):
    print(i, voto)

nomi = ["Ada", "Bea"]
eta = [36, 28]
for nome, anni in zip(nomi, eta):
    print(nome, anni)

print(round(3.14159, 2), abs(-3))        # 3.14 3
print(any(v < 6 for v in voti), all(v >= 6 for v in voti))   # False True', 'python', '<pre>30 6 9
[6, 7, 8, 9]
[9, 8, 7, 6]
1 7
2 9
3 6
4 8
Ada 36
Bea 28
3.14 3
False True</pre>'),
            P('`sorted` restituisce una **lista nuova**, mentre `lista.sort()` ordina quella esistente e restituisce `None`. Prima di scrivere un ciclo, chiediti se c’è già una funzione integrata che lo fa.', '`sorted` returns a **new list**, while `list.sort()` sorts the existing one and returns `None`. Before writing a loop, ask yourself whether a built-in function already does it.'),
        ], [
            Q('b1', L('Che cosa fa `sorted(voti)`?', 'What does `sorted(voti)` do?'), [L('Ordina voti e restituisce None', 'Sorts voti and returns None'), L('Restituisce una nuova lista ordinata', 'Returns a new sorted list'), L('Restituisce il voto più alto', 'Returns the highest mark'), L('Mescola la lista a caso', 'Shuffles the list randomly')], 1, L('La lista originale non cambia.', 'The original list does not change.')),
            Q('b2', L('Che cosa produce `enumerate(["a", "b"])`?', 'What does `enumerate(["a", "b"])` produce?'), [L('Le coppie (0, "a") e (1, "b")', 'The pairs (0, "a") and (1, "b")'), L('Il numero 2', 'The number 2'), L('Le lettere in maiuscolo', 'The letters in uppercase'), L('La coppia ("a", "b")', 'The pair ("a", "b")')], 0, L('Indice e valore, partendo da 0 se non dici altro.', 'Index and value, starting at 0 unless told otherwise.')),
            Q('b3', L('Che cosa fa `zip(nomi, eta)`?', 'What does `zip(nomi, eta)` do?'), [L('Comprime le liste in un file', 'Compresses the lists into a file'), L('Unisce le liste una dopo l’altra', 'Joins the lists one after the other'), L('Ordina le due liste insieme', 'Sorts both lists together'), L('Abbina gli elementi nella stessa posizione', 'Pairs the items at the same position')], 3, L('Il primo nome con la prima età, e così via.', 'The first name with the first age, and so on.')),
        ], task('python', L('Con `enumerate` stampa ogni elemento di `spesa` preceduto dal suo numero, partendo da 1.', 'With `enumerate` print each item of `spesa` preceded by its number, starting from 1.'), 'spesa = ["pane", "latte", "uova"]
', [re('for\s+(\w+)\s*,\s*(\w+)\s+in\s+enumerate\s*\(\s*spesa\s*,\s*(start\s*=\s*)?1\s*\)\s*:', L('Usi enumerate da 1', 'You use enumerate from 1')), re('print\s*\(', L('Stampi ogni riga', 'You print each line'))], '', 'for i, cosa in enumerate(spesa, start=1):
    print(i, cosa)'), [
            dMatch(pMatch(), [['sum', L('Somma', 'Sum')], ['max', L('Il più grande', 'The largest')], ['sorted', L('Nuova lista ordinata', 'New sorted list')], ['enumerate', L('Indice e valore', 'Index and value')], ['zip', L('Liste in parallelo', 'Lists side by side')]], L('Funzioni integrate da conoscere.', 'Built-in functions worth knowing.')),
            dBug(pBug(), ['numeri = [3, 1, 2]', 'ordinati = numeri.sort()', 'print(ordinati[0])'], 1, 'ordinati = sorted(numeri)', L('`sort()` ordina sul posto e restituisce `None`.', '`sort()` sorts in place and returns `None`.')),
        ]),
    ],
];
