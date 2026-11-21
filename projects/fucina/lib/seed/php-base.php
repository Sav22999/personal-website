<?php
/** PHP basics: the server side of the web, from variables and arrays to forms, sessions, types, files and dates. */

/** A request for a PHP page: the server runs the code and sends back only HTML. A dot travels there and back. */
if (!function_exists('fig_phpbase_request')) {
    function fig_phpbase_request(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.dot{animation:trip 5s ease-in-out infinite}@keyframes trip{0%{transform:translate(206px,96px)}40%{transform:translate(414px,96px)}55%{transform:translate(414px,176px)}95%,100%{transform:translate(206px,176px)}}@media (prefers-reduced-motion:reduce){.dot{animation:none}}</style>
<defs><marker id="ah" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="50" width="180" height="160" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="106" y="78" text-anchor="middle" font-weight="700" fill="#1B66BC">{$t[0]}</text>
<rect x="36" y="92" width="140" height="100" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="106" y="124" text-anchor="middle" font-size="14">Ciao Sav</text>
<rect x="54" y="140" width="104" height="8" rx="4" fill="#DCEBFF"/><rect x="54" y="158" width="76" height="8" rx="4" fill="#DCEBFF"/>
<rect x="424" y="30" width="200" height="200" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="524" y="58" text-anchor="middle" font-weight="700" fill="#B84508">{$t[1]}</text>
<rect x="446" y="72" width="156" height="36" rx="8" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/>
<text x="524" y="95" text-anchor="middle" font-family="ui-monospace, monospace" font-size="14">index.php</text>
<line x1="524" y1="110" x2="524" y2="146" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<text x="534" y="133" font-size="13">{$t[4]}</text>
<rect x="446" y="150" width="156" height="36" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="524" y="173" text-anchor="middle" font-family="ui-monospace, monospace" font-size="14">HTML</text>
<line x1="200" y1="96" x2="418" y2="96" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<text x="309" y="84" text-anchor="middle" font-size="14">{$t[2]}</text>
<line x1="420" y1="176" x2="202" y2="176" stroke="#0B7F5C" stroke-width="2" marker-end="url(#ah)"/>
<text x="309" y="200" text-anchor="middle" font-size="14" fill="#0B7F5C">{$t[3]}</text>
<circle class="dot" r="7" fill="#E8590C" transform="translate(206 96)"/>
<text x="320" y="248" text-anchor="middle" font-size="14" fill="#5B5880">{$t[5]}</text>
</svg>
SVG;
    }
}

/** A list with automatic indexes next to an array with keys chosen by you. */
if (!function_exists('fig_phpbase_arrays')) {
    function fig_phpbase_arrays(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="160" y="34" text-anchor="middle" font-weight="700" fill="#1B66BC">{$t[0]}</text>
<g font-family="ui-monospace, monospace" font-size="14" text-anchor="middle">
<text x="70" y="78" fill="#5B5880">0</text><text x="160" y="78" fill="#5B5880">1</text><text x="250" y="78" fill="#5B5880">2</text>
<rect x="30" y="88" width="80" height="44" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="70" y="115">"HTML"</text>
<rect x="120" y="88" width="80" height="44" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="160" y="115">"CSS"</text>
<rect x="210" y="88" width="80" height="44" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="250" y="115">"PHP"</text>
<text x="160" y="166" fill="#1B66BC">\$corsi[2]</text>
</g>
<text x="160" y="200" text-anchor="middle" font-size="13" fill="#5B5880">{$t[2]}</text>
<line x1="320" y1="20" x2="320" y2="210" stroke="#D9D7EA" stroke-width="2"/>
<text x="480" y="34" text-anchor="middle" font-weight="700" fill="#0B7F5C">{$t[1]}</text>
<g font-family="ui-monospace, monospace" font-size="14">
<rect x="360" y="56" width="110" height="40" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="415" y="81" text-anchor="middle">"nome"</text>
<text x="482" y="81" fill="#5B5880">=&gt;</text>
<rect x="512" y="56" width="100" height="40" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="562" y="81" text-anchor="middle">"Sav"</text>
<rect x="360" y="108" width="110" height="40" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="415" y="133" text-anchor="middle">"punti"</text>
<text x="482" y="133" fill="#5B5880">=&gt;</text>
<rect x="512" y="108" width="100" height="40" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="562" y="133" text-anchor="middle">10</text>
<text x="480" y="180" text-anchor="middle" fill="#0B7F5C">\$utente["nome"]</text>
</g>
<text x="480" y="210" text-anchor="middle" font-size="13" fill="#5B5880">{$t[3]}</text>
</svg>
SVG;
    }
}

/** The trip of a form field: typed, sent by POST, escaped, shown as harmless text. */
if (!function_exists('fig_phpbase_form')) {
    function fig_phpbase_form(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="af" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g text-anchor="middle">
<text x="80" y="34" font-weight="700" font-size="15">{$t[0]}</text>
<text x="240" y="34" font-weight="700" font-size="15">{$t[1]}</text>
<text x="400" y="34" font-weight="700" font-size="15">{$t[2]}</text>
<text x="560" y="34" font-weight="700" font-size="15">{$t[3]}</text>
</g>
<rect x="12" y="50" width="136" height="120" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="24" y="80" width="112" height="32" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="80" y="101" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">&lt;b&gt;Ada&lt;/b&gt;</text>
<text x="80" y="140" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">name="nome"</text>
<rect x="172" y="50" width="136" height="120" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="240" y="98" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">\$_POST["nome"]</text>
<text x="240" y="126" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">"&lt;b&gt;Ada&lt;/b&gt;"</text>
<rect x="332" y="50" width="136" height="120" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="400" y="80" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">htmlspecialchars</text>
<text x="400" y="112" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">&amp;lt;b&amp;gt;Ada</text>
<text x="400" y="132" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">&amp;lt;/b&amp;gt;</text>
<rect x="492" y="50" width="136" height="120" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="560" y="98" text-anchor="middle" font-size="15">Ciao &lt;b&gt;Ada&lt;/b&gt;</text>
<text x="560" y="126" text-anchor="middle" font-size="13" fill="#0B7F5C">{$t[4]}</text>
<line x1="150" y1="110" x2="168" y2="110" stroke="#1E1C52" stroke-width="2" marker-end="url(#af)"/>
<line x1="310" y1="110" x2="328" y2="110" stroke="#1E1C52" stroke-width="2" marker-end="url(#af)"/>
<line x1="470" y1="110" x2="488" y2="110" stroke="#1E1C52" stroke-width="2" marker-end="url(#af)"/>
<text x="320" y="204" text-anchor="middle" font-size="14" fill="#5B5880">{$t[5]}</text>
</svg>
SVG;
    }
}

/** How a session works: the browser keeps only an ID in a cookie, the data stays on the server. */
if (!function_exists('fig_phpbase_session')) {
    function fig_phpbase_session(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="as" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="40" width="190" height="170" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="111" y="68" text-anchor="middle" font-weight="700" fill="#1B66BC">{$t[0]}</text>
<rect x="34" y="96" width="154" height="60" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="111" y="120" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">PHPSESSID</text>
<text x="111" y="142" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">= a1b2c3</text>
<text x="111" y="186" text-anchor="middle" font-size="13" fill="#5B5880">{$t[4]}</text>
<rect x="420" y="40" width="204" height="170" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="522" y="68" text-anchor="middle" font-weight="700" fill="#B84508">{$t[1]}</text>
<rect x="438" y="84" width="168" height="86" rx="10" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/>
<text x="522" y="108" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">sess_a1b2c3</text>
<text x="522" y="134" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">"utente" =&gt; "Sav"</text>
<text x="522" y="156" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">"punti" =&gt; 10</text>
<text x="522" y="194" text-anchor="middle" font-size="13" fill="#5B5880">{$t[5]}</text>
<line x1="416" y1="104" x2="212" y2="104" stroke="#E8590C" stroke-width="2" marker-end="url(#as)"/>
<text x="314" y="94" text-anchor="middle" font-size="13">{$t[2]}</text>
<line x1="210" y1="150" x2="414" y2="150" stroke="#2F8CF0" stroke-width="2" marker-end="url(#as)"/>
<text x="314" y="172" text-anchor="middle" font-size="13">{$t[3]}</text>
<text x="320" y="238" text-anchor="middle" font-size="14" fill="#5B5880">{$t[6]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'php-base', 'family' => 'php', 'after' => 'html-base',
    'title' => L('PHP base', 'PHP Basics'), 'short' => L('PHP base', 'PHP Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il lato server del web: variabili, condizioni, array, funzioni, moduli e sicurezza di base, sessioni, tipi, file e date.', 'The server side of the web: variables, conditions, arrays, functions, forms and basic security, sessions, types, files and dates.'),
    'lessons' => [
        lesson('hello', L('Ciao dal server', 'Hello from the server'), [
            P('**PHP** gira sul server e produce la pagina prima che arrivi al browser. Il codice sta tra `<?php` e `?>`. Le variabili iniziano con `$` e ogni istruzione finisce con `;`.', '**PHP** runs on the server and produces the page before it reaches the browser. Code lives between `<?php` and `?>`. Variables start with `$` and every statement ends with `;`.'),
            Fig(L(fig_phpbase_request(['Browser', 'Server', '1. richiesta /index.php', '2. risposta: solo HTML', 'esegue il PHP', 'Il browser riceve il risultato, mai il codice PHP.']), fig_phpbase_request(['Browser', 'Server', '1. request /index.php', '2. response: HTML only', 'runs the PHP', 'The browser gets the result, never the PHP code.'])),
                L('A sinistra il browser, a destra il server. Una freccia porta la richiesta di index.php al server, che esegue il PHP e produce HTML; una seconda freccia riporta al browser solo l’HTML.', 'The browser on the left, the server on the right. One arrow carries the request for index.php to the server, which runs the PHP and produces HTML; a second arrow brings only the HTML back to the browser.'),
                L('Il PHP resta sul server: al browser arriva solo il suo risultato.', 'PHP stays on the server: only its result reaches the browser.')),
            C(L('<?php
$nome = "Sav";
$punti = 10;
echo "Ciao $nome, hai $punti punti";
echo \'Virgolette singole: $nome\';
?>
<p><?= $nome ?></p>', '<?php
$nome = "Sav";
$punti = 10;
echo "Hello $nome, you have $punti points";
echo \'Single quotes: $nome\';
?>
<p><?= $nome ?></p>'), 'php', L('Ciao Sav, hai 10 puntiVirgolette singole: $nome<p>Sav</p>', 'Hello Sav, you have 10 pointsSingle quotes: $nome<p>Sav</p>')),
            P('Tra virgolette doppie le variabili vengono sostituite, tra singole no. `<?=` è la scorciatoia per stampare un valore nell’HTML.', 'Between double quotes variables are replaced, between single quotes they are not. `<?=` is the shortcut for printing a value in HTML.'),
            P('Per questo, se apri «Visualizza sorgente» nel browser, vedi solo HTML: il codice PHP non lascia mai il server, ed è lì che puoi tenere password e query al database. Per provare i tuoi file ti serve quindi un server: in locale basta `php -S localhost:8000` nella cartella del progetto. Nota anche che `echo` non va a capo da solo: se vuoi una nuova riga nella pagina devi stampare un `<br>` o un tag di blocco.', 'That is why, if you open “View source” in the browser, you only see HTML: PHP code never leaves the server, and that is where you can keep passwords and database queries. To try your files you therefore need a server: locally, `php -S localhost:8000` in the project folder is enough. Notice too that `echo` does not start a new line by itself: if you want a new line in the page you must print a `<br>` or a block tag.'),
        ], [
            Q('h1', L('Dove gira PHP?', 'Where does PHP run?'), [L('Sul server', 'On the server'), L('Nel browser', 'In the browser'), L('Nel CSS', 'In CSS'), L('Sul telefono', 'On the phone')], 0, L('Il browser riceve solo il risultato.', 'The browser only gets the result.')),
            Q('h2', L('Con che cosa inizia una variabile?', 'What does a variable start with?'), ['$', '@', '#', '&'], 0, L('Sempre, sia quando la crei sia quando la usi.', 'Always, both creating and using it.')),
            Q('h3', L('Quale comando stampa?', 'Which command prints?'), ['echo', 'print_it', 'log', 'write'], 0, L('Il testo finisce nella pagina.', 'The text ends up in the page.')),
        ], task('php', L('Crea `$nome` e stampalo con `echo` dentro un saluto tra virgolette doppie.', 'Create `$nome` and print it with `echo` inside a greeting between double quotes.'), '<?php
', [re('\$nome\s*=', L('Crei `$nome`', 'You create `$nome`')), re('echo\s+"[^"]*\$nome', L('Lo stampi', 'You print it'))], '', "<?php\n\$nome = \"Sav\";\necho \"Ciao \$nome\";"), [
            dBlanks(pFill(), "<?php\n___nome = \"Sav\";\n___ \"Ciao \$nome\"___", ['$', 'echo', ';'], ['@', 'print_it', '.'], L('Dollaro, comando e punto e virgola.', 'Dollar, command and semicolon.')),
            dBug(pBug(), ['<?php', '$punti = 10', 'echo $punti;'], 1, '$punti = 10;', L('Ogni istruzione PHP finisce con il punto e virgola.', 'Every PHP statement ends with a semicolon.')),
            dFlash(pFlash(), [[L('Il browser vede il codice PHP', 'The browser sees the PHP code'), false], [L('Tra `"` le variabili vengono sostituite', 'Between `"` variables are replaced'), true], [L('`<?=` stampa un valore', '`<?=` prints a value'), true], [L('Le variabili PHP iniziano con `@`', 'PHP variables start with `@`'), false]], L('Al browser arriva solo l’HTML prodotto.', 'Only the produced HTML reaches the browser.')),
        ]),
        lesson('logic', L('Condizioni e cicli', 'Conditions and loops'), [
            P('`if`, `else`, `for` e `while` funzionano come in JavaScript. Le stringhe si uniscono con il punto. Per confrontare usa `===`.', '`if`, `else`, `for` and `while` work as in JavaScript. Strings join with a dot. To compare use `===`.'),
            C(L('<?php
$voto = 8;
if ($voto >= 6) {
    echo "Promosso";
} else {
    echo "Riprova";
}
for ($i = 1; $i <= 3; $i++) {
    echo "Giro " . $i . "<br>";
}
$esito = match (true) {
    $voto >= 9 => "Ottimo",
    default => "Bene",
};', '<?php
$voto = 8;
if ($voto >= 6) {
    echo "Passed";
} else {
    echo "Try again";
}
for ($i = 1; $i <= 3; $i++) {
    echo "Round " . $i . "<br>";
}
$esito = match (true) {
    $voto >= 9 => "Excellent",
    default => "Good",
};'), 'php', L('PromossoGiro 1<br>Giro 2<br>Giro 3<br>', 'PassedRound 1<br>Round 2<br>Round 3<br>')),
            P('`match` è un confronto compatto che restituisce un valore. Il punto `.` unisce i testi: `+` farebbe una somma.', '`match` is a compact comparison that gives back a value. The dot `.` joins text: `+` would add numbers.'),
            P('A differenza di `switch`, `match` confronta con `===`, non ha bisogno di `break` e dà errore se nessun caso corrisponde e manca `default`: niente casi dimenticati in silenzio. Per più rami in fila c’è `elseif`. E `while` ripete finché la condizione è vera: ricordati di cambiare qualcosa dentro il ciclo, altrimenti non finisce mai.', 'Unlike `switch`, `match` compares with `===`, needs no `break` and throws an error if no case matches and there is no `default`: no forgotten cases slipping by in silence. For several branches in a row there is `elseif`. And `while` repeats as long as the condition is true: remember to change something inside the loop, or it never ends.'),
            Tip('L’errore classico è `if ($voto = 6)`: un solo `=` assegna 6 a `$voto` e la condizione è sempre vera. Per confrontare servono `==` o, meglio, `===`.', 'The classic mistake is `if ($voto = 6)`: a single `=` assigns 6 to `$voto` and the condition is always true. To compare you need `==` or, better, `===`.'),
        ], [
            Q('l1', L('Come si uniscono due stringhe?', 'How do you join two strings?'), [L('Con il punto', 'With a dot'), L('Con `+`', 'With `+`'), L('Con `&`', 'With `&`'), L('Con `,`', 'With `,`')], 0, L('`"a" . "b"` dà `"ab"`.', '`"a" . "b"` gives `"ab"`.')),
            Q('l2', L('Quale confronto controlla anche il tipo?', 'Which comparison also checks the type?'), ['===', '==', '=', '<>'], 0, L('Come in JavaScript.', 'As in JavaScript.')),
            Q('l3', L('Che cosa restituisce `match`?', 'What does `match` give back?'), [L('Un valore', 'A value'), L('Nulla', 'Nothing'), L('Un file', 'A file'), L('Un ciclo', 'A loop')], 0, L('Va assegnato o usato.', 'It is assigned or used.')),
        ], task('php', L('Con un ciclo `for`, stampa i numeri da 1 a 5.', 'With a `for` loop, print the numbers from 1 to 5.'), '<?php
', [re('for\s*\(\s*\$i\s*=\s*1\s*;\s*\$i\s*<=\s*5\s*;\s*\$i\+\+\s*\)', L('Il ciclo va da 1 a 5', 'The loop goes from 1 to 5')), re('echo\s+\$i', L('Stampi `$i`', 'You print `$i`'))], '', "<?php\nfor (\$i = 1; \$i <= 5; \$i++) {\n    echo \$i;\n}"), [
            dOrder(pSort(), ['$voto = 8;', 'if ($voto >= 6) {', '    echo "Promosso";', '} else {', '    echo "Riprova";', '}'], L('Dato, condizione, blocco vero, blocco falso.', 'Data, condition, true block, false block.')),
            dBug(pBug(), ['$nome = "Sav";', '$saluto = "Ciao " + $nome;', 'echo $saluto;'], 1, '$saluto = "Ciao " . $nome;', L('In PHP le stringhe si uniscono con il punto, non con `+`.', 'In PHP strings join with a dot, not with `+`.')),
        ]),
        lesson('arrays', L('Array', 'Arrays'), [
            P('Gli array PHP fanno due lavori: liste e coppie chiave-valore. `foreach` li scorre. `count` dice quanti elementi ci sono.', 'PHP arrays do two jobs: lists and key-value pairs. `foreach` walks them. `count` says how many items there are.'),
            Fig(L(fig_phpbase_arrays(['Lista', 'Chiave => valore', 'indici automatici, da 0', 'chiavi scelte da te']), fig_phpbase_arrays(['List', 'Key => value', 'automatic indexes, from 0', 'keys chosen by you'])),
                L('A sinistra tre caselle con gli indici 0, 1 e 2 e i valori HTML, CSS e PHP. A destra due righe: la chiave nome punta a Sav, la chiave punti punta a 10.', 'On the left three boxes with indexes 0, 1 and 2 and the values HTML, CSS and PHP. On the right two rows: the key nome points to Sav, the key punti points to 10.'),
                L('Lo stesso tipo `array`: con indici numerici o con chiavi di testo.', 'The same `array` type: with numeric indexes or with text keys.')),
            C('<?php
$corsi = ["HTML", "CSS"];
$corsi[] = "PHP";

$utente = ["nome" => "Sav", "punti" => 10];
echo $utente["nome"];

foreach ($utente as $chiave => $valore) {
    echo "$chiave: $valore<br>";
}
echo count($corsi); // 3
in_array("CSS", $corsi); // true', 'php', 'Savnome: Sav<br>punti: 10<br>3'),
            P('`$lista[] = x` aggiunge in fondo. `=>` lega una chiave al suo valore.', '`$lista[] = x` adds to the end. `=>` ties a key to its value.'),
            P('Gli indici partono da **0**: il terzo corso è `$corsi[2]`. Leggere una chiave che non esiste dà un avviso e restituisce `null`, quindi quando non sei sicuro usa `$utente["email"] ?? ""` o controlla prima con `isset`. Un’altra cosa utile: quando assegni un array a un’altra variabile PHP ne fa una **copia**, così modificare la copia non tocca l’originale.', 'Indexes start at **0**: the third course is `$corsi[2]`. Reading a key that does not exist gives a warning and returns `null`, so when you are not sure use `$utente["email"] ?? ""` or check first with `isset`. Another useful fact: when you assign an array to another variable PHP makes a **copy**, so changing the copy does not touch the original.'),
        ], [
            Q('a1', L('Come si aggiunge in fondo a un array?', 'How do you add to the end of an array?'), ['$a[] = 1;', '$a.push(1);', 'add($a, 1);', '$a += 1;'], 0, L('Le parentesi vuote significano «in coda».', 'Empty brackets mean “at the end”.')),
            Q('a2', L('Quale ciclo è fatto per gli array?', 'Which loop is made for arrays?'), ['foreach', 'forall', 'each_of', 'loop'], 0, L('Niente contatore.', 'No counter.')),
            Q('a3', L('Che cosa lega una chiave al valore?', 'What ties a key to its value?'), ['=>', '->', ':', '='], 0, L('`->` serve per gli oggetti.', '`->` is for objects.')),
        ], task('php', L('Crea l’array associativo `$libro` con `titolo` e stampa il titolo.', 'Create the associative array `$libro` with `titolo` and print the title.'), '<?php
', [re('\$libro\s*=\s*\[\s*["\']titolo["\']\s*=>', L('L’array ha la chiave', 'The array has the key')), re('echo\s+\$libro\[\s*["\']titolo["\']\s*\]', L('Stampi il titolo', 'You print the title'))], '', "<?php\n\$libro = [\"titolo\" => \"Fucina\"];\necho \$libro[\"titolo\"];"), [
            dBlanks(pFill(), "\$u = [\"nome\" ___ \"Sav\"];\n___ (\$u as \$k ___ \$v) {\n    echo \$k;\n}", ['=>', 'foreach', '=>'], ['->', 'for', ':'], L('`=>` per le coppie, `foreach` per scorrerle.', '`=>` for pairs, `foreach` to walk them.')),
            dMatch(pMatch(), [['count()', L('Quanti elementi', 'How many items')], ['in_array()', L('Contiene il valore?', 'Does it hold the value?')], ['array_keys()', L('Le chiavi', 'The keys')], ['sort()', L('Ordina', 'Sorts')], ['implode()', L('Da array a testo', 'Array to text')]], L('PHP ha decine di funzioni pronte per gli array.', 'PHP has dozens of ready-made array functions.')),
        ]),
        lesson('functions', L('Funzioni e stringhe', 'Functions and strings'), [
            P('Le funzioni si dichiarano con `function` e possono avere tipi per parametri e risultato. PHP offre molte funzioni pronte per i testi.', 'Functions are declared with `function` and can have types for parameters and result. PHP offers many ready-made functions for text.'),
            C('<?php
function somma(int $a, int $b = 1): int {
    return $a + $b;
}
echo somma(2, 3);

strlen("ciao");          // 4
strtoupper("ciao");      // "CIAO"
str_replace("a", "o", "casa"); // "coso"
explode(",", "a,b");     // ["a", "b"]
trim("  ciao  ");        // "ciao"', 'php'),
            P('Le variabili esterne non si vedono dentro una funzione: si passano come parametri.', 'Outer variables are not visible inside a function: pass them as parameters.'),
            P('È una protezione, non un difetto: una funzione che dipende solo dai suoi parametri si capisce e si prova da sola. Per lo stesso motivo preferisci `return` a `echo`: chi chiama decide che cosa farne, se stamparlo, salvarlo o passarlo a un’altra funzione. Un parametro con un valore predefinito, come `$b = 1`, si può omettere nella chiamata.', 'It is a protection, not a flaw: a function that depends only on its parameters can be understood and tested on its own. For the same reason prefer `return` to `echo`: the caller decides what to do with the result, whether to print it, store it or pass it to another function. A parameter with a default value, such as `$b = 1`, can be left out of the call.'),
            Tip('Senza `declare(strict_types=1);` in cima al file, PHP converte in silenzio `somma("2", "3")` in numeri. Con la dichiarazione, un tipo sbagliato diventa subito un errore chiaro.', 'Without `declare(strict_types=1);` at the top of the file, PHP silently turns `somma("2", "3")` into numbers. With the declaration, a wrong type becomes a clear error straight away.'),
        ], [
            Q('f1', L('Una funzione vede `$x` definita fuori?', 'Does a function see `$x` defined outside?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo se è un numero', 'Only if a number'), L('Solo in `if`', 'Only in `if`')], 0, L('Si passa come parametro.', 'Pass it as a parameter.')),
            Q('f2', L('Che cosa fa `explode`?', 'What does `explode` do?'), [L('Divide un testo in array', 'Splits text into an array'), L('Cancella un file', 'Deletes a file'), L('Unisce', 'Joins'), L('Ordina', 'Sorts')], 0, L('`implode` fa il contrario.', '`implode` does the opposite.')),
            Q('f3', L('Che cosa fa `trim`?', 'What does `trim` do?'), [L('Toglie gli spazi ai lati', 'Removes spaces at the ends'), L('Taglia a metà', 'Cuts in half'), L('Conta', 'Counts'), L('Maiuscolo', 'Uppercases')], 0, L('Utile per pulire i dati dei moduli.', 'Handy for cleaning form data.')),
        ], task('php', L('Scrivi `function saluta(string $nome): string` che restituisce `"Ciao " . $nome`.', 'Write `function saluta(string $nome): string` that gives back `"Ciao " . $nome`.'), '<?php
', [re('function\s+saluta\s*\(\s*string\s+\$nome\s*\)\s*:\s*string', L('La firma è giusta', 'The signature is right')), re('return\s+"Ciao "\s*\.\s*\$nome', L('Restituisci il saluto', 'You give back the greeting'))], '', "<?php\nfunction saluta(string \$nome): string {\n    return \"Ciao \" . \$nome;\n}"), [
            dMatch(pMatch(), [['strlen()', L('Lunghezza', 'Length')], ['strtoupper()', L('Maiuscolo', 'Uppercase')], ['str_replace()', L('Sostituisce', 'Replaces')], ['explode()', L('Divide', 'Splits')], ['trim()', L('Toglie gli spazi', 'Removes spaces')]], L('Gli attrezzi per lavorare i testi.', 'The tools for working with text.')),
            dBug(pBug(), ['$bonus = 5;', 'function totale(int $p): int {', '    return $p + $bonus;', '}'], 2, '    return $p + 5;', L('`$bonus` è fuori dalla funzione: dentro non esiste. Usa il valore direttamente o passalo come parametro.', '`$bonus` is outside the function: it does not exist inside. Use the value directly or pass it as a parameter.')),
        ]),
        lesson('forms', L('Moduli e sicurezza', 'Forms and security'), [
            P('I dati di un modulo arrivano in `$_POST` o `$_GET`. **Non fidarti mai** di ciò che arriva: controllalo e, prima di stamparlo, proteggilo con `htmlspecialchars`.', 'Form data arrives in `$_POST` or `$_GET`. **Never trust** what arrives: check it and, before printing it, protect it with `htmlspecialchars`.'),
            P('La chiave dell’array è l’attributo `name` del campo: `<input name="nome">` arriva come `$_POST["nome"]`. Con `method="get"` i dati viaggiano nell’indirizzo (va bene per ricerche e filtri, che si possono condividere), con `method="post"` nel corpo della richiesta (per accessi e per tutto ciò che modifica dati).', 'The array key is the field’s `name` attribute: `<input name="nome">` arrives as `$_POST["nome"]`. With `method="get"` the data travels in the address (fine for searches and filters, which can be shared), with `method="post"` in the body of the request (for logins and anything that changes data).'),
            Fig(L(fig_phpbase_form(['Modulo', 'Server', 'Escape', 'Pagina', 'mostrato come testo', 'Ciò che arriva dall’utente diventa testo innocuo prima di finire nella pagina.']), fig_phpbase_form(['Form', 'Server', 'Escape', 'Page', 'shown as text', 'What comes from the user becomes harmless text before it reaches the page.'])),
                L('Quattro riquadri collegati da frecce: un campo con il testo <b>Ada</b>, lo stesso testo in $_POST["nome"], htmlspecialchars che lo trasforma in &lt;b&gt;Ada&lt;/b&gt;, e la pagina che lo mostra come testo e non come grassetto.', 'Four boxes linked by arrows: a field with the text <b>Ada</b>, the same text in $_POST["nome"], htmlspecialchars turning it into &lt;b&gt;Ada&lt;/b&gt;, and the page showing it as text rather than bold.'),
                L('`htmlspecialchars` trasforma i tag in testo: il browser li mostra, non li esegue.', '`htmlspecialchars` turns tags into text: the browser shows them, it does not run them.')),
            C('<?php
$nome = trim($_POST["nome"] ?? "");
if ($nome === "") {
    echo "Scrivi il nome";
} else {
    echo "Ciao " . htmlspecialchars($nome);
}
$email = filter_var($_POST["email"] ?? "", FILTER_VALIDATE_EMAIL);', 'php'),
            P('Senza `htmlspecialchars`, un utente potrebbe inserire un tag `<script>` che verrebbe eseguito: si chiama **XSS**. Da PHP 8.1 la funzione protegge di serie anche gli apici. `??` dà un valore se il campo manca.', 'Without `htmlspecialchars`, a user could insert a `<script>` tag that would run: it is called **XSS**. Since PHP 8.1 the function escapes quotes too by default. `??` gives a value if the field is missing.'),
        ], [
            Q('s1', L('Dove arrivano i dati di un POST?', 'Where does POST data arrive?'), ['$_POST', '$POST', '$_FORM', '$_DATA'], 0, L('È un array già pronto.', 'It is a ready-made array.')),
            Q('s2', L('Che cosa blocca `htmlspecialchars`?', 'What does `htmlspecialchars` block?'), [L('L’inserimento di HTML e script', 'Injecting HTML and scripts'), L('Lo spam', 'Spam'), L('Le immagini', 'Images'), L('I cookie', 'Cookies')], 0, L('Trasforma `<` in `&lt;`.', 'It turns `<` into `&lt;`.')),
            Q('s3', L('I controlli nel browser bastano?', 'Are browser checks enough?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo in HTTPS', 'Only on HTTPS'), L('Solo per le email', 'Only for emails')], 0, L('Una richiesta si può inviare senza passare dal modulo.', 'A request can be sent without going through the form.')),
        ], task('php', L('Leggi `$_POST["nome"]` con `??` e stampalo protetto da `htmlspecialchars`.', 'Read `$_POST["nome"]` with `??` and print it protected by `htmlspecialchars`.'), '<?php
', [re('\$_POST\[\s*["\']nome["\']\s*\]\s*\?\?', L('Leggi il campo in sicurezza', 'You read the field safely')), re('echo\s+htmlspecialchars\(', L('Proteggi il testo', 'You protect the text'))], '', "<?php\n\$nome = \$_POST[\"nome\"] ?? \"\";\necho htmlspecialchars(\$nome);"), [
            dBug(pBug(), ['$nome = $_GET["nome"] ?? "";', 'echo "<p>Ciao $nome</p>";'], 1, 'echo "<p>Ciao " . htmlspecialchars($nome) . "</p>";', L('Stampare senza protezione apre la strada all’XSS.', 'Printing with no protection opens the door to XSS.')),
            dFlash(pFlash(), [[L('Mai fidarsi dei dati dell’utente', 'Never trust user data'), true], [L('`$_GET` legge l’indirizzo della pagina', '`$_GET` reads the page address'), true], [L('Le password si passano in `$_GET`', 'Passwords go in `$_GET`'), false], [L('`required` nell’HTML basta sul server', '`required` in HTML is enough on the server'), false]], L('Controlla sempre sul server: il browser si aggira.', 'Always check on the server: the browser can be bypassed.')),
        ]),
        lesson('files-sessions', L('Include, sessioni e cookie', 'Includes, sessions and cookies'), [
            P('`require` inserisce un altro file: comodo per testate e funzioni condivise. Le **sessioni** ricordano un utente tra le pagine, i **cookie** salvano piccoli dati nel browser.', '`require` inserts another file: handy for shared headers and functions. **Sessions** remember a user across pages, **cookies** keep small data in the browser.'),
            C('<?php
require __DIR__ . "/header.php";

session_start();
$_SESSION["utente"] = "Sav";

setcookie("tema", "scuro", [
    "expires" => time() + 86400,
    "httponly" => true,
    "samesite" => "Lax",
]);
header("Location: home.php");
exit;', 'php'),
            P('`session_start` e `header` vanno chiamati prima di stampare qualsiasi cosa. Dopo un `header("Location: …")` scrivi sempre `exit`.', '`session_start` and `header` must be called before printing anything. After a `header("Location: …")` always write `exit`.'),
            P('Come fa il server a ricordarti? `session_start` crea un identificativo casuale e lo manda al browser in un cookie; a ogni richiesta successiva il browser lo rimanda e PHP ritrova i dati giusti. I dati della sessione restano sul server, mentre un cookie l’utente lo può leggere e modificare: non metterci mai niente di cui il server deve fidarsi, come un ruolo o un prezzo.', 'How does the server remember you? `session_start` creates a random identifier and sends it to the browser in a cookie; on every following request the browser sends it back and PHP finds the right data. Session data stays on the server, while a cookie can be read and changed by the user: never put anything the server must trust in it, such as a role or a price.'),
            Fig(L(fig_phpbase_session(['Browser', 'Server', 'risposta: Set-Cookie', 'ogni richiesta: Cookie', 'solo l’identificativo', 'i dati restano qui', 'Il cookie è la chiave, la sessione è il cassetto sul server.']), fig_phpbase_session(['Browser', 'Server', 'response: Set-Cookie', 'every request: Cookie', 'only the identifier', 'the data stays here', 'The cookie is the key, the session is the drawer on the server.'])),
                L('Il browser conserva un cookie PHPSESSID con il valore a1b2c3. Il server conserva un file sess_a1b2c3 con utente Sav e punti 10. Una freccia dal server porta Set-Cookie al browser, una freccia dal browser riporta il cookie a ogni richiesta.', 'The browser keeps a PHPSESSID cookie with the value a1b2c3. The server keeps a file sess_a1b2c3 with utente Sav and punti 10. One arrow from the server carries Set-Cookie to the browser, one arrow from the browser sends the cookie back on every request.'),
                L('Nel browser viaggia solo l’identificativo della sessione.', 'Only the session identifier travels in the browser.')),
            Tip('`require` si ferma con un errore se il file manca, `include` dà solo un avviso e prosegue. Per i file di funzioni usa `require_once`, che non lo carica due volte.', '`require` stops with an error if the file is missing, `include` only warns and carries on. For files of functions use `require_once`, which does not load them twice.'),
        ], [
            Q('i1', L('Che cosa ricorda una sessione?', 'What does a session remember?'), [L('Dati dell’utente sul server', 'User data on the server'), L('I colori', 'Colours'), L('Il codice', 'The code'), L('Il dominio', 'The domain')], 0, L('Nel browser resta solo un codice.', 'Only a code stays in the browser.')),
            Q('i2', L('Quando va chiamato `session_start`?', 'When must `session_start` be called?'), [L('Prima di ogni output', 'Before any output'), L('Alla fine', 'At the end'), L('Mai', 'Never'), L('Dentro l’HTML', 'Inside HTML')], 0, L('Deve inviare un’intestazione.', 'It has to send a header.')),
            Q('i3', L('Che cosa fa `httponly`?', 'What does `httponly` do?'), [L('Nasconde il cookie a JavaScript', 'Hides the cookie from JavaScript'), L('Lo cancella', 'Deletes it'), L('Lo rende pubblico', 'Makes it public'), L('Lo cifra', 'Encrypts it')], 0, L('Protegge se una pagina subisce XSS.', 'It protects if a page suffers XSS.')),
        ], task('php', L('Avvia la sessione e salva `"Sav"` in `$_SESSION["utente"]`.', 'Start the session and save `"Sav"` in `$_SESSION["utente"]`.'), '<?php
', [re('session_start\(\)', L('Avvii la sessione', 'You start the session')), re('\$_SESSION\[\s*["\']utente["\']\s*\]\s*=', L('Salvi l’utente', 'You save the user'))], '', "<?php\nsession_start();\n\$_SESSION[\"utente\"] = \"Sav\";"), [
            dOrder(pSort(), ['<?php', 'session_start();', '$_SESSION["utente"] = "Sav";', 'header("Location: home.php");', 'exit;'], L('Sessione, dati, reindirizzamento e uscita.', 'Session, data, redirect and exit.')),
            dBug(pBug(), ['<?php', 'echo "Benvenuto";', 'session_start();'], 2, 'session_start(); // prima di echo', L('Dopo un `echo` è troppo tardi: `session_start` va chiamato prima di ogni output.', 'After an `echo` it is too late: `session_start` must come before any output.')),
        ]),
        lesson('types', L('Tipi, null e conversioni', 'Types, null and conversions'), [
            P('Ogni valore PHP ha un **tipo**: `int` (interi), `float` (decimali), `string`, `bool` (vero/falso), `array` e `null` (nessun valore). `var_dump` mostra tipo e valore insieme: è il primo attrezzo per capire che cosa contiene una variabile.', 'Every PHP value has a **type**: `int` (whole numbers), `float` (decimals), `string`, `bool` (true/false), `array` and `null` (no value). `var_dump` shows type and value together: it is the first tool for finding out what a variable holds.'),
            C('<?php
$n = 42;                    // int
$prezzo = 9.5;              // float
$attivo = true;             // bool
$nulla = null;
var_dump($prezzo);          // float(9.5)

$eta = (int) "36";          // 36
var_dump("10" == 10);       // bool(true)
var_dump("10" === 10);      // bool(false)
echo $nulla ?? "vuoto";     // vuoto
echo is_numeric("3.14") ? "numero" : "testo";', 'php'),
            P('`(int)` e `intval()` convertono un valore in intero. `==` converte i tipi prima di confrontare, `===` no: preferisci sempre `===`. `??` restituisce il valore di destra se quello di sinistra è `null` o non esiste.', '`(int)` and `intval()` turn a value into an integer. `==` converts types before comparing, `===` does not: always prefer `===`. `??` returns the right-hand value if the left one is `null` or does not exist.'),
        ], [
            Q('t1', L('Che cosa stampa `var_dump(9.5)`?', 'What does `var_dump(9.5)` print?'), ['int(9)', 'string(3) "9.5"', 'float(9.5)', 'number(9.5)'], 2, L('Il tipo dei decimali è `float`.', 'The type for decimals is `float`.')),
            Q('t2', L('Quale espressione vale `false`?', 'Which expression is `false`?'), ['10 === 10', '"a" === "a"', '"10" == 10', '"10" === 10'], 3, L('`===` controlla anche il tipo: una stringa e un intero sono tipi diversi.', '`===` also checks the type: a string and an int are different types.')),
            Q('t3', L('Che cosa dà `null ?? "ospite"`?', 'What does `null ?? "ospite"` give?'), ['null', '"ospite"', L('Un errore fatale', 'A fatal error'), L('Una stringa vuota', 'An empty string')], 1, L('A sinistra c’è `null`, quindi vince il valore di destra.', 'The left side is `null`, so the right-hand value wins.')),
        ], task('php', L('Converti `$testo` in intero con `(int)`, salvalo in `$numero` e mostra tipo e valore con `var_dump($numero)`.', 'Convert `$testo` to an integer with `(int)`, save it in `$numero` and show type and value with `var_dump($numero)`.'), '<?php
$testo = "25";
', [re('\$numero\s*=\s*(\(\s*int\s*\)\s*\$testo|intval\s*\(\s*\$testo\s*\))', L('Converti il testo', 'You convert the text')), re('var_dump\s*\(\s*\$numero\s*\)', L('Mostri tipo e valore', 'You show type and value'))], '', '<?php
$testo = "25";
$numero = (int) $testo;
var_dump($numero);'), [
            dMatch(pMatch(), [['42', 'int'], ['9.5', 'float'], ['"ciao"', 'string'], ['true', 'bool'], ['null', L('Nessun valore', 'No value')]], L('I tipi di base di PHP.', 'PHP’s basic types.')),
            dFlash(pFlash(), [[L('`===` confronta anche il tipo', '`===` also compares the type'), true], [L('`(int) "7"` dà 7', '`(int) "7"` gives 7'), true], [L('`null ?? "x"` dà null', '`null ?? "x"` gives null'), false], [L('`var_dump` mostra solo il valore, senza il tipo', '`var_dump` shows only the value, without the type'), false]], L('`var_dump` e `===` evitano molte sorprese.', '`var_dump` and `===` avoid many surprises.')),
        ]),
        lesson('files', L('Leggere e scrivere file', 'Reading and writing files'), [
            P('`file_put_contents` scrive un testo in un file (e lo crea se non c’è), `file_get_contents` lo legge tutto in una stringa, `file()` lo legge come array di righe. `__DIR__` è la cartella del file PHP corrente: usala per costruire percorsi sicuri.', '`file_put_contents` writes text to a file (creating it if missing), `file_get_contents` reads it all into a string, `file()` reads it as an array of lines. `__DIR__` is the folder of the current PHP file: use it to build reliable paths.'),
            C('<?php
$percorso = __DIR__ . "/note.txt";
file_put_contents($percorso, "Prima riga\n");
file_put_contents($percorso, "Seconda riga\n", FILE_APPEND);

if (file_exists($percorso)) {
    $testo = file_get_contents($percorso);
    $righe = file($percorso, FILE_IGNORE_NEW_LINES);
    echo count($righe); // 2
}', 'php'),
            P('Senza `FILE_APPEND` il file viene riscritto da capo. Non usare mai un nome di file arrivato dall’utente senza controllarlo: potrebbe puntare a `../config.php` e mostrare i tuoi segreti.', 'Without `FILE_APPEND` the file is rewritten from scratch. Never use a file name sent by the user without checking it: it could point to `../config.php` and reveal your secrets.'),
        ], [
            Q('f1', L('Che cosa fa `FILE_APPEND`?', 'What does `FILE_APPEND` do?'), [L('Cancella il file prima di scrivere', 'Wipes the file before writing'), L('Aggiunge il testo in fondo al file', 'Adds the text at the end of the file'), L('Apre il file in sola lettura', 'Opens the file read-only'), L('Crea una copia di sicurezza', 'Makes a backup copy')], 1, L('Senza, il contenuto precedente sparisce.', 'Without it, the previous content disappears.')),
            Q('f2', L('Quale funzione legge un file intero in una stringa?', 'Which function reads a whole file into a string?'), ['file()', 'fopen()', 'file_get_contents()', 'readfile_all()'], 2, L('`file()` restituisce invece un array di righe.', '`file()` returns an array of lines instead.')),
            Q('f3', L('Perché è pericoloso `file_get_contents($_GET["f"])`?', 'Why is `file_get_contents($_GET["f"])` dangerous?'), [L('L’utente può leggere file riservati del server', 'The user can read private files on the server'), L('Il file viene letto due volte di seguito', 'The file is read twice in a row'), L('La funzione non accetta variabili', 'The function does not accept variables'), L('Rallenta solo la prima richiesta', 'It slows down only the first request')], 0, L('Il percorso lo decide chi visita la pagina.', 'Whoever visits the page decides the path.')),
        ], task('php', L('Scrivi `"Ciao"` nel file `saluto.txt` con `file_put_contents`, poi rileggilo in `$testo` con `file_get_contents`.', 'Write `"Ciao"` to the file `saluto.txt` with `file_put_contents`, then read it back into `$testo` with `file_get_contents`.'), '<?php
', [re('file_put_contents\s*\([^;]*saluto\.txt["\']\s*,\s*["\']Ciao', L('Scrivi il file', 'You write the file')), re('\$testo\s*=\s*file_get_contents\s*\([^;]*saluto\.txt', L('Lo rileggi', 'You read it back'))], '', '<?php
file_put_contents("saluto.txt", "Ciao");
$testo = file_get_contents("saluto.txt");'), [
            dBlanks(pFill(), '<?php
___("log.txt", "Accesso\n", ___);
$righe = ___("log.txt", FILE_IGNORE_NEW_LINES);', ['file_put_contents', 'FILE_APPEND', 'file'], ['file_write', 'APPEND_FILE', 'read'], L('Scrivi in coda, poi leggi le righe.', 'Append, then read the lines.')),
            dOrder(pSort(), ['$f = __DIR__ . "/dati.txt";', 'if (file_exists($f)) {', '    $testo = file_get_contents($f);', '    echo strlen($testo);', '}'], L('Percorso, controllo, lettura, uso.', 'Path, check, read, use.')),
        ]),
        lesson('dates-format', L('Date e numeri formattati', 'Dates and formatted numbers'), [
            P('`date("d/m/Y")` scrive la data di oggi nel formato che vuoi. Per i calcoli usa `DateTimeImmutable`: `modify` sposta la data, `diff` misura la distanza tra due date, `format` la trasforma in testo. Imposta il fuso orario, altrimenti PHP usa quello del server.', '`date("d/m/Y")` writes today’s date in the format you want. For calculations use `DateTimeImmutable`: `modify` moves the date, `diff` measures the gap between two dates, `format` turns it into text. Set the time zone, otherwise PHP uses the server’s.'),
            C('<?php
date_default_timezone_set("Europe/Rome");
echo date("d/m/Y H:i");                  // 17/11/2026 09:30

$oggi = new DateTimeImmutable("2026-11-17");
$scadenza = $oggi->modify("+30 days");
echo $scadenza->format("d/m/Y");         // 17/12/2026
echo $oggi->diff($scadenza)->days;       // 30

echo number_format(1234.5, 2, ",", "."); // 1.234,50
echo sprintf("%s ha %d punti", "Ada", 42);', 'php'),
            P('`DateTimeImmutable` non cambia mai: `modify` restituisce un oggetto nuovo. Con `DateTime` invece cambia anche l’originale, una fonte di bug sottili. `number_format` sceglie decimali, separatore decimale e separatore delle migliaia.', '`DateTimeImmutable` never changes: `modify` returns a new object. With `DateTime` the original changes too, a source of subtle bugs. `number_format` picks the decimals, the decimal separator and the thousands separator.'),
        ], [
            Q('d1', L('Che cosa stampa `date("Y")`?', 'What does `date("Y")` print?'), [L('L’anno con quattro cifre', 'The four-digit year'), L('Il giorno del mese', 'The day of the month'), L('Il nome del mese', 'The name of the month'), L('I secondi passati dal 1970', 'The seconds since 1970')], 0, L('`y` minuscola darebbe solo due cifre.', 'Lowercase `y` would give just two digits.')),
            Q('d2', L('Che cosa dà `number_format(1234.5, 2, ",", ".")`?', 'What does `number_format(1234.5, 2, ",", ".")` give?'), ['1,234.50', '1234,5', '1.234,5', '1.234,50'], 3, L('Due decimali, la virgola per i decimali e il punto per le migliaia.', 'Two decimals, a comma for decimals and a dot for thousands.')),
            Q('d3', L('Perché preferire `DateTimeImmutable` a `DateTime`?', 'Why prefer `DateTimeImmutable` to `DateTime`?'), [L('È l’unica classe che conosce i fusi orari', 'It is the only class that knows time zones'), L('Le modifiche creano un oggetto nuovo e l’originale resta uguale', 'Changes create a new object and the original stays the same'), L('Scrive le date direttamente nel database', 'It writes dates straight into the database'), L('Funziona anche senza indicare il formato', 'It works even without a format')], 1, L('Niente modifiche a sorpresa.', 'No surprise changes.')),
        ], task('php', L('Crea `$oggi = new DateTimeImmutable();` e stampa la data tra 7 giorni nel formato `d/m/Y`.', 'Create `$oggi = new DateTimeImmutable();` and print the date 7 days from now in the `d/m/Y` format.'), '<?php
', [re('new\s+DateTimeImmutable\s*\(', L('Crei la data', 'You create the date')), re('->modify\s*\(\s*["\']\+\s*7\s*days?["\']\s*\)|->add\s*\(\s*new\s+DateInterval\s*\(\s*["\']P7D["\']\s*\)\s*\)', L('Aggiungi 7 giorni', 'You add 7 days')), re('->format\s*\(\s*["\']d/m/Y["\']\s*\)', L('La formatti', 'You format it'))], '', '<?php
$oggi = new DateTimeImmutable();
echo $oggi->modify("+7 days")->format("d/m/Y");'), [
            dMatch(pMatch(), [['d', L('Giorno con due cifre', 'Two-digit day')], ['m', L('Mese con due cifre', 'Two-digit month')], ['Y', L('Anno con quattro cifre', 'Four-digit year')], ['H', L('Ore da 00 a 23', 'Hours 00 to 23')], ['i', L('Minuti', 'Minutes')]], L('Le lettere dei formati di data.', 'The letters of date formats.')),
            dBug(pBug(), ['$inizio = new DateTime("2026-01-01");', '$fine = $inizio->modify("+1 month");', 'echo $inizio->format("d/m/Y"); // voglio 01/01/2026'], 0, '$inizio = new DateTimeImmutable("2026-01-01");', L('`DateTime` è mutabile: `modify` cambia anche `$inizio`, che diventa 01/02/2026.', '`DateTime` is mutable: `modify` changes `$inizio` too, which becomes 01/02/2026.')),
        ]),
    ],
];
