<?php
/** PHP basics: the server side of the web. */

return [
    'id' => 'php-base', 'family' => 'php', 'after' => 'html-base',
    'title' => L('PHP base', 'PHP Basics'), 'short' => L('PHP base', 'PHP Basics'), 'level' => L('Base', 'Basic'),
    'desc' => L('Il lato server del web: variabili, condizioni, array, funzioni, moduli e sicurezza di base.', 'The server side of the web: variables, conditions, arrays, functions, forms and basic security.'),
    'lessons' => [
        lesson('hello', L('Ciao dal server', 'Hello from the server'), [
            P('**PHP** gira sul server e produce la pagina prima che arrivi al browser. Il codice sta tra `<?php` e `?>`. Le variabili iniziano con `$` e ogni istruzione finisce con `;`.', '**PHP** runs on the server and produces the page before it reaches the browser. Code lives between `<?php` and `?>`. Variables start with `$` and every statement ends with `;`.'),
            C('<?php
$nome = "Sav";
$punti = 10;
echo "Ciao $nome, hai $punti punti";
echo \'Virgolette singole: $nome\';
?>
<p><?= $nome ?></p>', 'php'),
            P('Tra virgolette doppie le variabili vengono sostituite, tra singole no. `<?=` è la scorciatoia per stampare un valore nell’HTML.', 'Between double quotes variables are replaced, between single quotes they are not. `<?=` is the shortcut for printing a value in HTML.'),
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
            C('<?php
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
};', 'php'),
            P('`match` è un confronto compatto che restituisce un valore. Il punto `.` unisce i testi: `+` farebbe una somma.', '`match` is a compact comparison that gives back a value. The dot `.` joins text: `+` would add numbers.'),
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
            C('<?php
$corsi = ["HTML", "CSS"];
$corsi[] = "PHP";

$utente = ["nome" => "Sav", "punti" => 10];
echo $utente["nome"];

foreach ($utente as $chiave => $valore) {
    echo "$chiave: $valore<br>";
}
echo count($corsi); // 3
in_array("CSS", $corsi); // true', 'php'),
            P('`$lista[] = x` aggiunge in fondo. `=>` lega una chiave al suo valore.', '`$lista[] = x` adds to the end. `=>` ties a key to its value.'),
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
        ], [
            Q('f1', L('Una funzione vede `$x` definita fuori?', 'Does a function see `$x` defined outside?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo se è un numero', 'Only if a number'), L('Solo in `if`', 'Only in `if`')], 0, L('Si passa come parametro.', 'Pass it as a parameter.')),
            Q('f2', L('Che cosa fa `explode`?', 'What does `explode` do?'), [L('Divide un testo in array', 'Splits text into an array'), L('Cancella un file', 'Deletes a file'), L('Unisce', 'Joins'), L('Ordina', 'Sorts')], 0, L('`implode` fa il contrario.', '`implode` does the opposite.')),
            Q('f3', L('Che cosa fa `trim`?', 'What does `trim` do?'), [L('Toglie gli spazi ai lati', 'Removes spaces at the ends'), L('Taglia a metà', 'Cuts in half'), L('Conta', 'Counts'), L('Maiuscolo', 'Uppercases')], 0, L('Utile per pulire i dati dei moduli.', 'Handy for cleaning form data.')),
        ], task('php', L('Scrivi `function saluta(string $nome): string` che restituisce `"Ciao " . $nome`.', 'Write `function saluta(string $nome): string` that gives back `"Ciao " . $nome`.'), '<?php
', [re('function\s+saluta\s*\(\s*string\s+\$nome\s*\)\s*:\s*string', L('La firma è giusta', 'The signature is right')), re('return\s+"Ciao "\s*\.\s*\$nome', L('Restituisci il saluto', 'You give back the greeting'))], '', "<?php\nfunction saluta(string \$nome): string {\n    return \"Ciao \" . \$nome;\n}"), [
            dMatch(pMatch(), [['strlen()', L('Lunghezza', 'Length')], ['strtoupper()', L('Maiuscolo', 'Uppercase')], ['str_replace()', L('Sostituisce', 'Replaces')], ['explode()', L('Divide', 'Splits')], ['trim()', L('Toglie gli spazi', 'Removes spaces')]], L('Gli attrezzi per lavorare i testi.', 'The tools for working with text.')),
            dBug(pBug(), ['$bonus = 5;', 'function totale(int $p): int {', '    return $p + $bonus;', '}'], 2, '    return $p + 5;', L('`$bonus` è fuori dalla funzione: dentro non esiste. Va passato come parametro.', '`$bonus` is outside the function: it does not exist inside. It must be passed as a parameter.')),
        ]),
        lesson('forms', L('Moduli e sicurezza', 'Forms and security'), [
            P('I dati di un modulo arrivano in `$_POST` o `$_GET`. **Non fidarti mai** di ciò che arriva: controllalo e, prima di stamparlo, proteggilo con `htmlspecialchars`.', 'Form data arrives in `$_POST` or `$_GET`. **Never trust** what arrives: check it and, before printing it, protect it with `htmlspecialchars`.'),
            C('<?php
$nome = trim($_POST["nome"] ?? "");
if ($nome === "") {
    echo "Scrivi il nome";
} else {
    echo "Ciao " . htmlspecialchars($nome);
}
$email = filter_var($_POST["email"] ?? "", FILTER_VALIDATE_EMAIL);', 'php'),
            P('Senza `htmlspecialchars`, un utente potrebbe inserire un tag `<script>` che verrebbe eseguito: si chiama **XSS**. `??` dà un valore se il campo manca.', 'Without `htmlspecialchars`, a user could insert a `<script>` tag that would run: it is called **XSS**. `??` gives a value if the field is missing.'),
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
        ], [
            Q('i1', L('Che cosa ricorda una sessione?', 'What does a session remember?'), [L('Dati dell’utente sul server', 'User data on the server'), L('I colori', 'Colours'), L('Il codice', 'The code'), L('Il dominio', 'The domain')], 0, L('Nel browser resta solo un codice.', 'Only a code stays in the browser.')),
            Q('i2', L('Quando va chiamato `session_start`?', 'When must `session_start` be called?'), [L('Prima di ogni output', 'Before any output'), L('Alla fine', 'At the end'), L('Mai', 'Never'), L('Dentro l’HTML', 'Inside HTML')], 0, L('Deve inviare un’intestazione.', 'It has to send a header.')),
            Q('i3', L('Che cosa fa `httponly`?', 'What does `httponly` do?'), [L('Nasconde il cookie a JavaScript', 'Hides the cookie from JavaScript'), L('Lo cancella', 'Deletes it'), L('Lo rende pubblico', 'Makes it public'), L('Lo cifra', 'Encrypts it')], 0, L('Protegge se una pagina subisce XSS.', 'It protects if a page suffers XSS.')),
        ], task('php', L('Avvia la sessione e salva `"Sav"` in `$_SESSION["utente"]`.', 'Start the session and save `"Sav"` in `$_SESSION["utente"]`.'), '<?php
', [re('session_start\(\)', L('Avvii la sessione', 'You start the session')), re('\$_SESSION\[\s*["\']utente["\']\s*\]\s*=', L('Salvi l’utente', 'You save the user'))], '', "<?php\nsession_start();\n\$_SESSION[\"utente\"] = \"Sav\";"), [
            dOrder(pSort(), ['<?php', 'session_start();', '$_SESSION["utente"] = "Sav";', 'header("Location: home.php");', 'exit;'], L('Sessione, dati, reindirizzamento e uscita.', 'Session, data, redirect and exit.')),
            dBug(pBug(), ['<?php', 'echo "Benvenuto";', 'session_start();'], 2, 'session_start(); // prima di echo', L('Dopo un `echo` è troppo tardi: `session_start` va chiamato prima di ogni output.', 'After an `echo` it is too late: `session_start` must come before any output.')),
        ]),
    ],
];
