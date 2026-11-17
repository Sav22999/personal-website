<?php
/** PHP intermediate: objects and inheritance, databases with PDO and transactions, passwords, APIs, uploads, routing and tidy projects (until version 12 it was "PHP advanced"). */

return [
    'id' => 'php-intermediate', 'family' => 'php', 'after' => 'php-base',
    'title' => L('PHP intermedio', 'Intermediate PHP'), 'short' => L('PHP intermedio', 'PHP Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Classi ed ereditarietà, database con PDO e transazioni, password sicure, API JSON, errori, caricamento di file, router e progetti ordinati.', 'Classes and inheritance, databases with PDO and transactions, safe passwords, JSON APIs, errors, file uploads, routing and tidy projects.'),
    'lessons' => [
        lesson('classes', L('Classi e oggetti', 'Classes and objects'), [
            P('Una classe è lo stampo degli oggetti. Si accede a proprietà e metodi con `->`. Le visibilità `public`, `protected` e `private` decidono chi può usarli.', 'A class is the mould of objects. You reach properties and methods with `->`. The visibilities `public`, `protected` and `private` decide who can use them.'),
            C('<?php
class Eroe {
    public function __construct(
        public string $nome,
        private int $vite = 3,
    ) {}

    public function colpisci(): void {
        $this->vite--;
    }
}
$e = new Eroe("Ada");
$e->colpisci();
echo $e->nome;', 'php'),
            P('La **promozione nel costruttore** dichiara e assegna le proprietà in un colpo. `$this` è l’oggetto stesso.', '**Constructor promotion** declares and assigns properties in one go. `$this` is the object itself.'),
        ], [
            Q('c1', L('Quale operatore raggiunge il metodo di un oggetto?', 'Which operator reaches an object’s method?'), ['->', '.', '=>', '::'], 0, L('`::` serve per i membri statici.', '`::` is for static members.')),
            Q('c2', L('Chi vede una proprietà `private`?', 'Who sees a `private` property?'), [L('Solo la sua classe', 'Only its own class'), L('Tutti', 'Everyone'), L('Le sottoclassi', 'Subclasses'), L('Il browser', 'The browser')], 0, L('`protected` include le sottoclassi.', '`protected` includes subclasses.')),
            Q('c3', L('Come si chiama il costruttore?', 'What is the constructor called?'), ['__construct', 'constructor', 'init', 'new'], 0, L('Con due trattini bassi.', 'With two underscores.')),
        ], task('php', L('Crea la classe `Gatto` con un costruttore che riceve `public string $nome`.', 'Create the class `Gatto` with a constructor taking `public string $nome`.'), '<?php
', [re('class\s+Gatto', L('La classe esiste', 'The class exists')), re('__construct\s*\(\s*public\s+string\s+\$nome', L('Il costruttore è giusto', 'The constructor is right'))], '', "<?php\nclass Gatto {\n    public function __construct(public string \$nome) {}\n}"), [
            dBlanks(pFill(), "class Mago ___ Eroe {\n    public function lancia(): void {\n        ___->magia--;\n    }\n}\n\$m = ___ Mago(\"Ada\");\n\$m___lancia();", ['extends', '$this', 'new', '->'], ['implements', 'self', '.'], L('Ereditarietà, oggetto corrente, creazione e chiamata.', 'Inheritance, current object, creation and call.')),
            dMatch(pMatch(), [['public', L('Tutti', 'Everyone')], ['protected', L('Classe e sottoclassi', 'Class and subclasses')], ['private', L('Solo la classe', 'Only the class')], ['static', L('Della classe, non dell’oggetto', 'Of the class, not the object')], ['abstract', L('Solo da estendere', 'Only to be extended')]], L('Chi può toccare che cosa.', 'Who can touch what.')),
        ]),
        lesson('pdo', L('Database con PDO', 'Databases with PDO'), [
            P('**PDO** collega PHP a un database. Le **query preparate** separano il comando dai dati: è la difesa contro la **SQL injection**. Non inserire mai una variabile dentro il testo di una query.', '**PDO** connects PHP to a database. **Prepared queries** keep the command apart from the data: it is the defence against **SQL injection**. Never put a variable inside the text of a query.'),
            C('<?php
$pdo = new PDO($dsn, $utente, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$st = $pdo->prepare("SELECT nome FROM utenti WHERE id = ?");
$st->execute([$id]);
$riga = $st->fetch();

$st = $pdo->prepare("INSERT INTO corsi (titolo) VALUES (?)");
$st->execute(["PHP"]);', 'php'),
            P('`fetch` legge una riga, `fetchAll` tutte. Il `?` è un segnaposto: il valore viaggia separato e non può cambiare il comando.', '`fetch` reads one row, `fetchAll` all of them. `?` is a placeholder: the value travels apart and cannot change the command.'),
        ], [
            Q('d1', L('Che cosa blocca una query preparata?', 'What does a prepared query block?'), [L('La SQL injection', 'SQL injection'), L('L’XSS', 'XSS'), L('I virus', 'Viruses'), L('Lo spam', 'Spam')], 0, L('I dati non diventano mai comandi.', 'Data never becomes commands.')),
            Q('d2', L('Che cosa fa `?` in una query?', 'What does `?` do in a query?'), [L('È un segnaposto', 'It is a placeholder'), L('È una domanda', 'It is a question'), L('È un commento', 'It is a comment'), L('Rende casuale', 'Makes it random')], 0, L('Il valore arriva con `execute`.', 'The value comes with `execute`.')),
            Q('d3', L('Quale metodo legge tutte le righe?', 'Which method reads all the rows?'), ['fetchAll', 'all', 'getRows', 'read'], 0, L('`fetch` ne legge una.', '`fetch` reads one.')),
        ], task('php', L('Prepara `SELECT * FROM corsi WHERE id = ?` ed eseguila con `[$id]`.', 'Prepare `SELECT * FROM corsi WHERE id = ?` and run it with `[$id]`.'), '<?php
', [re('\$pdo->prepare\(\s*"SELECT \* FROM corsi WHERE id = \?"\s*\)', L('La query è preparata', 'The query is prepared')), re('->execute\(\s*\[\s*\$id\s*\]\s*\)', L('La esegui con il dato', 'You run it with the data'))], '', "<?php\n\$st = \$pdo->prepare(\"SELECT * FROM corsi WHERE id = ?\");\n\$st->execute([\$id]);"), [
            dBug(pBug(), ['$id = $_GET["id"];', '$st = $pdo->query("SELECT * FROM utenti WHERE id = $id");', '$riga = $st->fetch();'], 1, '$st = $pdo->prepare("SELECT * FROM utenti WHERE id = ?");', L('Una variabile dentro la query apre alla SQL injection: serve `prepare`.', 'A variable inside the query opens the door to SQL injection: use `prepare`.')),
            dOrder(pSort(), ['$pdo = new PDO($dsn, $utente, $password);', '$st = $pdo->prepare("SELECT * FROM corsi WHERE id = ?");', '$st->execute([$id]);', '$corso = $st->fetch();'], L('Connetti, prepara, esegui, leggi.', 'Connect, prepare, run, read.')),
        ]),
        lesson('passwords', L('Password e accessi', 'Passwords and logins'), [
            P('Le password non si salvano mai in chiaro. `password_hash` crea un’impronta sicura, `password_verify` controlla un tentativo. Un’impronta non si può riportare al testo originale.', 'Passwords are never saved in clear text. `password_hash` creates a safe fingerprint, `password_verify` checks an attempt. A fingerprint cannot be turned back into the original text.'),
            C('<?php
$impronta = password_hash($password, PASSWORD_DEFAULT);

if (password_verify($tentativo, $impronta)) {
    session_regenerate_id(true);
    $_SESSION["id"] = $utente["id"];
}
$token = bin2hex(random_bytes(32));', 'php'),
            P('Dopo l’accesso rigenera l’id di sessione. Per valori segreti usa `random_bytes`: `rand` è prevedibile. Limita anche i tentativi per fermare chi prova a indovinare.', 'After login regenerate the session id. For secret values use `random_bytes`: `rand` is predictable. Also limit attempts to stop those trying to guess.'),
        ], [
            Q('p1', L('Come si salva una password?', 'How do you save a password?'), [L('Con `password_hash`', 'With `password_hash`'), L('In chiaro', 'In clear text'), L('Con `md5`', 'With `md5`'), L('In un cookie', 'In a cookie')], 0, L('`md5` è troppo debole.', '`md5` is too weak.')),
            Q('p2', L('Si può risalire alla password dall’impronta?', 'Can you get the password back from the fingerprint?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo l’admin', 'Only the admin'), L('Con `password_undo`', 'With `password_undo`')], 0, L('Si può solo verificare un tentativo.', 'You can only verify an attempt.')),
            Q('p3', L('Quale funzione è adatta ai token?', 'Which function suits tokens?'), ['random_bytes', 'rand', 'time', 'uniqid'], 0, L('È crittograficamente sicura.', 'It is cryptographically secure.')),
        ], task('php', L('Crea l’impronta di `$password` con `password_hash` e `PASSWORD_DEFAULT`.', 'Create the fingerprint of `$password` with `password_hash` and `PASSWORD_DEFAULT`.'), '<?php
', [re('password_hash\(\s*\$password\s*,\s*PASSWORD_DEFAULT\s*\)', L('Usi `password_hash`', 'You use `password_hash`'))], '', "<?php\n\$impronta = password_hash(\$password, PASSWORD_DEFAULT);"), [
            dBlanks(pFill(), "\$h = ___(\$pw, PASSWORD_DEFAULT);\nif (___(\$prova, \$h)) {\n    session_regenerate_id(true);\n}\n\$t = bin2hex(___(32));", ['password_hash', 'password_verify', 'random_bytes'], ['md5', 'rand', 'strcmp'], L('Impronta, verifica e casualità sicura.', 'Fingerprint, verification and safe randomness.')),
            dFlash(pFlash(), [[L('`md5` va bene per le password', '`md5` is fine for passwords'), false], [L('Due utenti con la stessa password hanno impronte diverse', 'Two users with the same password get different fingerprints'), true], [L('Limitare i tentativi è utile', 'Limiting attempts is useful'), true], [L('`rand()` è sicuro per i token', '`rand()` is safe for tokens'), false]], L('Il sale casuale rende diversa ogni impronta.', 'Random salt makes every fingerprint different.')),
        ]),
        lesson('json-api', L('Costruire un’API JSON', 'Building a JSON API'), [
            P('Un’API risponde con dati invece che con pagine. Imposti il tipo di contenuto, il codice di stato e stampi il JSON. Il corpo di una richiesta si legge da `php://input`.', 'An API answers with data instead of pages. You set the content type, the status code and print the JSON. The body of a request is read from `php://input`.'),
            C('<?php
header("Content-Type: application/json; charset=utf-8");

$dati = json_decode(file_get_contents("php://input"), true);
if (!is_array($dati) || empty($dati["nome"])) {
    http_response_code(422);
    echo json_encode(["errore" => "Nome mancante"]);
    exit;
}
echo json_encode(["ok" => true, "nome" => $dati["nome"]]);', 'php'),
            P('Con `true`, `json_decode` restituisce array invece di oggetti. Usa i codici giusti: `200` riuscito, `401` non autenticato, `404` non trovato, `422` dati non validi.', 'With `true`, `json_decode` gives back arrays instead of objects. Use the right codes: `200` success, `401` not signed in, `404` not found, `422` invalid data.'),
        ], [
            Q('j1', L('Che cosa fa `json_encode`?', 'What does `json_encode` do?'), [L('Da valore a testo JSON', 'Value to JSON text'), L('Da testo a valore', 'Text to value'), L('Cifra', 'Encrypts'), L('Comprime', 'Compresses')], 0, L('`json_decode` è il contrario.', '`json_decode` is the opposite.')),
            Q('j2', L('Da dove si legge un corpo JSON?', 'Where do you read a JSON body from?'), ['php://input', '$_POST', '$_JSON', '$_BODY'], 0, L('`$_POST` vale solo per i moduli classici.', '`$_POST` only works for classic forms.')),
            Q('j3', L('Quale codice per dati non validi?', 'Which code for invalid data?'), ['422', '200', '301', '500'], 0, L('`500` è un errore del server, non dell’utente.', '`500` is a server error, not the user’s.')),
        ], task('php', L('Imposta l’intestazione JSON e stampa `["ok" => true]` con `json_encode`.', 'Set the JSON header and print `["ok" => true]` with `json_encode`.'), '<?php
', [re('header\(\s*"Content-Type:\s*application\/json', L('L’intestazione è giusta', 'The header is right')), re('echo\s+json_encode\(', L('Stampi il JSON', 'You print the JSON'))], '', "<?php\nheader(\"Content-Type: application/json\");\necho json_encode([\"ok\" => true]);"), [
            dMatch(pMatch(), [['200', L('Riuscito', 'Success')], ['401', L('Non autenticato', 'Not signed in')], ['403', L('Non permesso', 'Forbidden')], ['404', L('Non trovato', 'Not found')], ['422', L('Dati non validi', 'Invalid data')], ['500', L('Errore del server', 'Server error')]], L('Il codice dice a colpo d’occhio com’è andata.', 'The code says at a glance how it went.')),
            dOrder(pSort(), ['header("Content-Type: application/json");', '$dati = json_decode(file_get_contents("php://input"), true);', 'http_response_code(201);', 'echo json_encode(["ok" => true]);'], L('Intestazioni, lettura, stato, risposta.', 'Headers, reading, status, response.')),
        ]),
        lesson('errors', L('Errori ed eccezioni', 'Errors and exceptions'), [
            P('Quando qualcosa va storto, `throw` lancia un’eccezione e `catch` la gestisce. In produzione gli errori si scrivono nei log e non si mostrano mai ai visitatori.', 'When something goes wrong, `throw` raises an exception and `catch` handles it. In production errors are written to logs and never shown to visitors.'),
            C('<?php
declare(strict_types=1);

function dividi(int $a, int $b): float {
    if ($b === 0) {
        throw new InvalidArgumentException("Divisione per zero");
    }
    return $a / $b;
}
try {
    echo dividi(1, 0);
} catch (InvalidArgumentException $e) {
    error_log($e->getMessage());
    echo "Riprova";
} finally {
    echo "Fine";
}', 'php'),
            P('`strict_types` impedisce conversioni silenziose: passare un testo a un `int` diventa un errore. `Throwable` raccoglie tutti gli errori ed eccezioni.', '`strict_types` prevents silent conversions: passing text to an `int` becomes an error. `Throwable` gathers all errors and exceptions.'),
        ], [
            Q('e1', L('Quale parola lancia un’eccezione?', 'Which word raises an exception?'), ['throw', 'raise', 'error', 'fail'], 0, L('Si cattura con `catch`.', 'It is caught with `catch`.')),
            Q('e2', L('Che cosa mostrare ai visitatori?', 'What should visitors be shown?'), [L('Un messaggio generico', 'A generic message'), L('Tutto l’errore', 'The whole error'), L('La password', 'The password'), L('Il percorso dei file', 'The file paths')], 0, L('I dettagli aiutano chi attacca: vanno nei log.', 'Details help attackers: they go to the logs.')),
            Q('e3', L('Che cosa fa `strict_types`?', 'What does `strict_types` do?'), [L('Vieta le conversioni silenziose', 'Bans silent conversions'), L('Accelera', 'Speeds up'), L('Cifra', 'Encrypts'), L('Compila', 'Compiles')], 0, L('Va scritto in cima al file.', 'It goes at the top of the file.')),
        ], task('php', L('Con `try` e `catch (Exception $e)`, chiama `rischio()` e scrivi il messaggio con `error_log`.', 'With `try` and `catch (Exception $e)`, call `rischio()` and write the message with `error_log`.'), '<?php
', [re('try\s*\{[\s\S]*rischio\(\)', L('Provi `rischio()`', 'You try `rischio()`')), re('catch\s*\(\s*Exception\s+\$e\s*\)', L('Catturi l’eccezione', 'You catch the exception')), re('error_log\(', L('Scrivi nel log', 'You write to the log'))], '', "<?php\ntry {\n    rischio();\n} catch (Exception \$e) {\n    error_log(\$e->getMessage());\n}"), [
            dBlanks(pFill(), "___ {\n    salva();\n} ___ (Throwable \$e) {\n    error_log(\$e->___());\n}", ['try', 'catch', 'getMessage'], ['do', 'except', 'message'], L('Prova, cattura, scrivi nel log.', 'Try, catch, write to the log.')),
            dBug(pBug(), ['try {', '    salva();', '} catch (Throwable $e) {', '    echo $e->getMessage();', '}'], 3, '    error_log($e->getMessage());', L('Mostrare l’errore al visitatore rivela dettagli interni: va nel log.', 'Showing the error to visitors reveals internal details: it goes to the log.')),
        ]),
        lesson('project', L('Progetti ordinati', 'Tidy projects'), [
            P('Un progetto serio usa **Composer** per le librerie e i **namespace** per non far scontrare i nomi. Le credenziali stanno fuori dal codice pubblico, in un file escluso da git o nelle variabili d’ambiente.', 'A serious project uses **Composer** for libraries and **namespaces** to stop names clashing. Credentials stay out of public code, in a file excluded from git or in environment variables.'),
            C('<?php
namespace Fucina\Corsi;

use Fucina\Db\Connessione;

require __DIR__ . "/vendor/autoload.php";

$password = getenv("DB_PASSWORD");

final class Catalogo {
    public function __construct(private Connessione $db) {}
}', 'php'),
            P('Passare le dipendenze dal costruttore si chiama **iniezione delle dipendenze**: il codice diventa più facile da provare. `vendor/autoload.php` carica le classi da solo.', 'Passing dependencies through the constructor is called **dependency injection**: code becomes easier to test. `vendor/autoload.php` loads classes by itself.'),
        ], [
            Q('r1', L('A che cosa serve Composer?', 'What is Composer for?'), [L('A gestire le librerie', 'Managing libraries'), L('A scrivere musica', 'Writing music'), L('A comprimere immagini', 'Compressing images'), L('A fare il deploy', 'Deploying')], 0, L('Le elenca in `composer.json`.', 'It lists them in `composer.json`.')),
            Q('r2', L('Dove non vanno le password del database?', 'Where must database passwords not go?'), [L('In un repository pubblico', 'In a public repository'), L('In una variabile d’ambiente', 'In an environment variable'), L('In un file fuori da git', 'In a file outside git'), L('Sul server', 'On the server')], 0, L('Un segreto su git resta nella cronologia.', 'A secret on git stays in the history.')),
            Q('r3', L('Che cosa evitano i namespace?', 'What do namespaces avoid?'), [L('Scontri tra nomi', 'Name clashes'), L('I bug', 'Bugs'), L('Il CSS', 'CSS'), L('I database', 'Databases')], 0, L('Due classi con lo stesso nome possono convivere.', 'Two classes with the same name can coexist.')),
        ], task('php', L('Dichiara il namespace `Fucina\Corsi` e leggi la password con `getenv("DB_PASSWORD")`.', 'Declare the namespace `Fucina\Corsi` and read the password with `getenv("DB_PASSWORD")`.'), '<?php
', [re('namespace\s+Fucina\\\\Corsi', L('Il namespace è giusto', 'The namespace is right')), re('getenv\(\s*"DB_PASSWORD"\s*\)', L('Leggi la variabile', 'You read the variable'))], '', "<?php\nnamespace Fucina\\Corsi;\n\$password = getenv(\"DB_PASSWORD\");"), [
            dMatch(pMatch(), [['composer.json', L('Elenco delle librerie', 'List of libraries')], ['vendor/', L('Le librerie scaricate', 'Downloaded libraries')], ['namespace', L('Cognome delle classi', 'Surname of classes')], ['use', L('Importa una classe', 'Imports a class')], ['.gitignore', L('File esclusi da git', 'Files kept out of git')]], L('L’ordine oggi vale tempo domani.', 'Order today is time saved tomorrow.')),
            dFlash(pFlash(), [[L('`vendor/` di solito sta fuori da git', '`vendor/` usually stays out of git'), true], [L('Le password si scrivono nel codice pubblico', 'Passwords go in public code'), false], [L('L’iniezione delle dipendenze aiuta i test', 'Dependency injection helps testing'), true], [L('Un namespace rende il codice più lento', 'A namespace makes code slower'), false]], L('Segreti fuori dal codice, dipendenze dentro al costruttore.', 'Secrets out of the code, dependencies into the constructor.')),
        ]),
        lesson('inheritance', L('Ereditarietà, static e costanti', 'Inheritance, static and constants'), [
            P('Con `extends` una classe eredita proprietà e metodi da un’altra e può **ridefinirli**; `parent::` chiama la versione del padre. Le **costanti di classe** (`const`) e i membri `static` appartengono alla classe, non ai singoli oggetti: si raggiungono con `::`.', 'With `extends` a class inherits properties and methods from another and can **redefine** them; `parent::` calls the parent’s version. **Class constants** (`const`) and `static` members belong to the class, not to single objects: you reach them with `::`.'),
            C('<?php
class Personaggio {
    public const VITE_MAX = 5;
    private static int $creati = 0;

    public function __construct(protected string $nome) {
        self::$creati++;
    }
    public function saluta(): string {
        return "Sono $this->nome";
    }
    public static function quanti(): int {
        return self::$creati;
    }
}

class Mago extends Personaggio {
    public function saluta(): string {
        return parent::saluta() . " e faccio magie";
    }
}

$m = new Mago("Ada");
echo $m->saluta();            // Sono Ada e faccio magie
echo Personaggio::quanti();   // 1
echo Personaggio::VITE_MAX;   // 5', 'php'),
            P('Una classe PHP estende **una sola** classe. `self::` indica la classe in cui è scritto il codice. Un metodo `static` non ha `$this`, perché non lavora su un oggetto.', 'A PHP class extends **only one** class. `self::` means the class where the code is written. A `static` method has no `$this`, because it does not work on an object.'),
        ], [
            Q('h1', L('Che cosa fa `parent::saluta()`?', 'What does `parent::saluta()` do?'), [L('Chiama la versione della classe padre', 'Calls the parent class’s version'), L('Crea un nuovo oggetto padre', 'Creates a new parent object'), L('Rende il metodo statico', 'Makes the method static'), L('Cancella il metodo del figlio', 'Deletes the child’s method')], 0, L('Utile per estendere un metodo invece di sostituirlo.', 'Handy to extend a method instead of replacing it.')),
            Q('h2', L('Come si legge una costante di classe?', 'How do you read a class constant?'), ['$m->VITE_MAX', 'Personaggio.VITE_MAX', 'Personaggio::VITE_MAX', 'Personaggio->VITE_MAX'], 2, L('Le costanti appartengono alla classe: si usa `::`.', 'Constants belong to the class: you use `::`.')),
            Q('h3', L('A chi appartiene una proprietà `static`?', 'Who does a `static` property belong to?'), [L('A ogni oggetto separatamente', 'To each object separately'), L('Solo alle sottoclassi', 'Only to subclasses'), L('Solo al primo oggetto creato', 'Only to the first object created'), L('Alla classe, condivisa da tutti gli oggetti', 'To the class, shared by all objects')], 3, L('Per questo il contatore conta tutti i personaggi.', 'That is why the counter counts every character.')),
        ], task('php', L('Crea `class Cane extends Animale` con il metodo `verso(): string` che restituisce `"Bau"`.', 'Create `class Cane extends Animale` with the method `verso(): string` returning `"Bau"`.'), '<?php
', [re('class\s+Cane\s+extends\s+Animale', L('Cane eredita da Animale', 'Cane inherits from Animale')), re('function\s+verso\s*\(\s*\)\s*:\s*string', L('Il metodo ha il tipo giusto', 'The method has the right type')), re('return\s+["\']Bau["\']', L('Restituisce “Bau”', 'It returns “Bau”'))], '', '<?php
class Cane extends Animale {
    public function verso(): string {
        return "Bau";
    }
}'), [
            dBlanks(pFill(), 'class Gatto ___ Animale {
    public function __construct(string $nome) {
        ___::__construct($nome);
    }
}
echo Gatto___ZAMPE;', ['extends', 'parent', '::'], ['implements', 'super', '->'], L('Ereditarietà, costruttore del padre, costante.', 'Inheritance, parent constructor, constant.')),
            dFlash(pFlash(), [[L('Una classe PHP può estendere una sola classe', 'A PHP class can extend only one class'), true], [L('`self::` indica la classe stessa', '`self::` means the class itself'), true], [L('Una costante di classe si può cambiare con `=`', 'A class constant can be changed with `=`'), false], [L('Un metodo `static` può usare `$this`', 'A `static` method can use `$this`'), false]], L('`::` per la classe, `->` per l’oggetto.', '`::` for the class, `->` for the object.')),
        ]),
        lesson('transactions', L('Transazioni e segnaposto con nome', 'Transactions and named placeholders'), [
            P('Una **transazione** raggruppa più query in un blocco unico: o riescono tutte (`commit`) o non ne resta nessuna (`rollBack`). I **segnaposto con nome** come `:importo` rendono leggibili le query con molti parametri.', 'A **transaction** groups several queries into a single block: either they all succeed (`commit`) or none of them remains (`rollBack`). **Named placeholders** such as `:importo` make queries with many parameters readable.'),
            C('<?php
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
try {
    $pdo->beginTransaction();
    $st = $pdo->prepare("UPDATE conti SET saldo = saldo - :importo WHERE id = :da");
    $st->execute(["importo" => 50, "da" => 1]);
    $st = $pdo->prepare("UPDATE conti SET saldo = saldo + :importo WHERE id = :a");
    $st->execute(["importo" => 50, "a" => 2]);
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}', 'php'),
            P('Se il secondo `UPDATE` fallisce, `rollBack` annulla anche il primo: il denaro non sparisce a metà strada. `FETCH_ASSOC` fa restituire righe con le sole chiavi dei nomi di colonna; `$pdo->lastInsertId()` dà l’id dell’ultima riga inserita.', 'If the second `UPDATE` fails, `rollBack` undoes the first one too: the money does not vanish halfway. `FETCH_ASSOC` returns rows keyed only by column name; `$pdo->lastInsertId()` gives the id of the last inserted row.'),
        ], [
            Q('x1', L('A che cosa serve una transazione?', 'What is a transaction for?'), [L('A rendere le query più veloci', 'To make queries faster'), L('A eseguire più operazioni come un blocco: tutte o nessuna', 'To run several operations as one block: all or none'), L('A cifrare i dati salvati', 'To encrypt the saved data'), L('A copiare il database su un altro server', 'To copy the database to another server')], 1, L('Il database resta sempre coerente.', 'The database always stays consistent.')),
            Q('x2', L('Che cosa fa `rollBack()`?', 'What does `rollBack()` do?'), [L('Ripete l’ultima query', 'Repeats the last query'), L('Chiude la connessione al database', 'Closes the database connection'), L('Annulla le modifiche fatte dall’inizio della transazione', 'Undoes the changes made since the transaction began'), L('Salva definitivamente le modifiche', 'Saves the changes for good')], 2, L('`commit` invece le conferma.', '`commit` confirms them instead.')),
            Q('x3', L('Come passi il valore di `:id` a `execute`?', 'How do you pass the value of `:id` to `execute`?'), ['execute(":id = 7")', 'execute(7)', 'execute(id: 7)', 'execute(["id" => 7])'], 3, L('Un array con la chiave uguale al nome del segnaposto.', 'An array whose key matches the placeholder’s name.')),
        ], task('php', L('Racchiudi i due `execute` in una transazione: `$pdo->beginTransaction()` prima e `$pdo->commit()` dopo.', 'Wrap the two `execute` calls in a transaction: `$pdo->beginTransaction()` before and `$pdo->commit()` after.'), '<?php
$a->execute();
$b->execute();', [re('\$pdo->beginTransaction\s*\(\s*\)\s*;[\s\S]*\$a->execute[\s\S]*\$b->execute[\s\S]*\$pdo->commit\s*\(\s*\)', L('Le due query sono in una transazione', 'Both queries are inside a transaction'))], '', '<?php
$pdo->beginTransaction();
$a->execute();
$b->execute();
$pdo->commit();'), [
            dOrder(pSort(), ['try {', '    $pdo->beginTransaction();', '    $st->execute($dati);', '    $pdo->commit();', '} catch (Throwable $e) {', '    $pdo->rollBack();', '}'], L('Inizia, esegui, conferma; se qualcosa va storto, annulla.', 'Begin, run, confirm; if something goes wrong, undo.')),
            dMatch(pMatch(), [['beginTransaction', L('Inizia il blocco', 'Starts the block')], ['commit', L('Conferma tutto', 'Confirms everything')], ['rollBack', L('Annulla tutto', 'Undoes everything')], [':id', L('Segnaposto con nome', 'Named placeholder')], ['lastInsertId', L('Id dell’ultima riga inserita', 'Id of the last inserted row')]], L('Gli attrezzi delle transazioni.', 'The tools of transactions.')),
        ]),
        lesson('uploads', L('Caricare file', 'Uploading files'), [
            P('Per inviare file il modulo ha bisogno di `enctype="multipart/form-data"`. In PHP i file arrivano in `$_FILES`: controlla l’errore, la dimensione e il **contenuto vero**, poi spostalo con `move_uploaded_file`, dandogli un nome inventato da te.', 'To send files the form needs `enctype="multipart/form-data"`. In PHP files arrive in `$_FILES`: check the error, the size and the **real content**, then move it with `move_uploaded_file`, giving it a name you make up.'),
            C('<form method="post" enctype="multipart/form-data">
  <input type="file" name="foto" accept="image/*">
  <button>Carica</button>
</form>', 'html'),
            C('<?php
$file = $_FILES["foto"] ?? null;
if (!$file || $file["error"] !== UPLOAD_ERR_OK) exit("Caricamento fallito");
if ($file["size"] > 2_000_000) exit("File troppo grande");

$tipo = (new finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);
$estensioni = ["image/jpeg" => "jpg", "image/png" => "png"];
if (!isset($estensioni[$tipo])) exit("Formato non ammesso");

$nome = bin2hex(random_bytes(16)) . "." . $estensioni[$tipo];
move_uploaded_file($file["tmp_name"], __DIR__ . "/../uploads/" . $nome);', 'php'),
            P('Non fidarti del nome, dell’estensione né del tipo dichiarato in `$_FILES["foto"]["type"]`: li decide il browser e si falsificano. Il limite massimo dipende anche da `upload_max_filesize` e `post_max_size` in `php.ini`.', 'Do not trust the name, the extension or the type declared in `$_FILES["foto"]["type"]`: the browser decides them and they can be faked. The maximum size also depends on `upload_max_filesize` and `post_max_size` in `php.ini`.'),
        ], [
            Q('u1', L('Quale attributo serve al `<form>` per inviare file?', 'Which attribute does the `<form>` need to send files?'), ['enctype="multipart/form-data"', 'method="file"', 'type="upload"', 'accept="files"'], 0, L('Senza, arriva solo il nome del file.', 'Without it, only the file name arrives.')),
            Q('u2', L('Perché non usare `$_FILES["foto"]["type"]` per controllare il formato?', 'Why not use `$_FILES["foto"]["type"]` to check the format?'), [L('Contiene sempre il valore null', 'It always contains null'), L('Lo dichiara il browser e si può falsificare', 'The browser declares it and it can be faked'), L('Funziona solo con le immagini PNG', 'It only works with PNG images'), L('È disponibile solo dopo lo spostamento', 'It is only available after moving')], 1, L('Meglio `finfo`, che guarda il contenuto.', '`finfo` is better: it looks at the content.')),
            Q('u3', L('Quale funzione sposta il file caricato nella cartella finale?', 'Which function moves the uploaded file to its final folder?'), ['rename_upload()', 'copy_temp()', 'file_put_contents()', 'move_uploaded_file()'], 3, L('Controlla anche che il file arrivi davvero da un caricamento.', 'It also checks that the file really comes from an upload.')),
        ], task('php', L('Se `$_FILES["doc"]["error"]` vale `UPLOAD_ERR_OK`, sposta il file con `move_uploaded_file` da `tmp_name` a `$destinazione`.', 'If `$_FILES["doc"]["error"]` equals `UPLOAD_ERR_OK`, move the file with `move_uploaded_file` from `tmp_name` to `$destinazione`.'), '<?php
$destinazione = __DIR__ . "/archivio/documento.pdf";
', [re('\$_FILES\[\s*["\']doc["\']\s*\]\[\s*["\']error["\']\s*\]\s*===?\s*UPLOAD_ERR_OK|UPLOAD_ERR_OK\s*===?\s*\$_FILES\[\s*["\']doc["\']\s*\]\[\s*["\']error["\']\s*\]', L('Controlli l’errore', 'You check the error')), re('move_uploaded_file\s*\(\s*\$_FILES\[\s*["\']doc["\']\s*\]\[\s*["\']tmp_name["\']\s*\]\s*,\s*\$destinazione\s*\)', L('Sposti il file', 'You move the file'))], '', '<?php
if ($_FILES["doc"]["error"] === UPLOAD_ERR_OK) {
    move_uploaded_file($_FILES["doc"]["tmp_name"], $destinazione);
}'), [
            dBug(pBug(), ['$nome = $_FILES["foto"]["name"];', 'move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $nome);'], 0, '$nome = bin2hex(random_bytes(16)) . ".jpg";', L('Il nome scelto dall’utente può contenere `../` o finire in `.php`: inventalo tu.', 'The user’s file name may contain `../` or end in `.php`: make one up yourself.')),
            dFlash(pFlash(), [[L('`$_FILES` contiene i file inviati', '`$_FILES` holds the uploaded files'), true], [L('Il nome originale del file è sempre sicuro', 'The original file name is always safe'), false], [L('`finfo` guarda il contenuto vero del file', '`finfo` looks at the file’s real content'), true], [L('Un modulo senza `enctype` invia comunque i file', 'A form without `enctype` still sends files'), false]], L('Controlla tutto sul server.', 'Check everything on the server.')),
        ]),
        lesson('router', L('Un front controller con router', 'A front controller with a router'), [
            P('Invece di un file PHP per pagina, un **front controller** riceve tutte le richieste in un solo `index.php` e le smista: legge il metodo HTTP e il percorso, poi chiama il codice giusto. Il server web va configurato per mandare ogni indirizzo a quel file.', 'Instead of one PHP file per page, a **front controller** receives every request in a single `index.php` and dispatches it: it reads the HTTP method and the path, then calls the right code. The web server is set up to send every address to that file.'),
            C('<?php
// public/index.php: ogni richiesta passa da qui
$metodo = $_SERVER["REQUEST_METHOD"];
$percorso = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$risposta = match (true) {
    $metodo === "GET" && $percorso === "/" => "Home",
    $metodo === "GET" && $percorso === "/corsi" => elencoCorsi(),
    $metodo === "POST" && $percorso === "/iscriviti" => iscrivi($_POST),
    default => null,
};
if ($risposta === null) {
    http_response_code(404);
    $risposta = "Pagina non trovata";
}
echo $risposta;', 'php'),
            C('php -S localhost:8000 public/index.php', 'bash'),
            P('Con un solo punto d’ingresso, sessione, sicurezza e configurazione si preparano una volta sola. Codice e segreti restano fuori dalla cartella `public/`, irraggiungibili dal browser. Framework come Laravel e Symfony fanno questo lavoro con molte più comodità.', 'With a single entry point, session, security and configuration are set up once. Code and secrets stay outside the `public/` folder, out of the browser’s reach. Frameworks such as Laravel and Symfony do this job with many more conveniences.'),
        ], [
            Q('r1', L('Che cos’è un front controller?', 'What is a front controller?'), [L('Il controllo dei moduli lato browser', 'Form validation in the browser'), L('Un server proxy davanti a PHP', 'A proxy server in front of PHP'), L('Un file che riceve tutte le richieste e le smista', 'A file that receives every request and dispatches it'), L('Una classe che disegna la pagina', 'A class that draws the page')], 2, L('Un solo ingresso per tutta l’applicazione.', 'One way in for the whole application.')),
            Q('r2', L('Che cosa restituisce `parse_url("/corsi?p=2", PHP_URL_PATH)`?', 'What does `parse_url("/corsi?p=2", PHP_URL_PATH)` return?'), ['"/corsi?p=2"', '"/corsi"', '"p=2"', '["corsi", 2]'], 1, L('Solo il percorso, senza la query string.', 'Only the path, without the query string.')),
            Q('r3', L('Perché tenere il codice fuori da `public/`?', 'Why keep the code outside `public/`?'), [L('Il browser non può raggiungere quei file direttamente', 'The browser cannot reach those files directly'), L('PHP li esegue più in fretta', 'PHP runs them faster'), L('Composer lo richiede per l’autoload', 'Composer requires it for autoloading'), L('Le sessioni funzionano solo così', 'Sessions only work that way')], 0, L('Solo `public/` è la radice del sito.', 'Only `public/` is the site’s root.')),
        ], task('php', L('Aggiungi al `match` il ramo per `GET /contatti` che restituisce `"Scrivici"`.', 'Add to the `match` the arm for `GET /contatti` returning `"Scrivici"`.'), '<?php
$risposta = match (true) {
    $metodo === "GET" && $percorso === "/" => "Home",
    default => null,
};', [re('\$metodo\s*===\s*["\']GET["\']\s*&&\s*\$percorso\s*===\s*["\']/contatti["\']\s*=>\s*["\']Scrivici["\']|\$percorso\s*===\s*["\']/contatti["\']\s*&&\s*\$metodo\s*===\s*["\']GET["\']\s*=>\s*["\']Scrivici["\']', L('La nuova rotta risponde', 'The new route answers'))], '', '<?php
$risposta = match (true) {
    $metodo === "GET" && $percorso === "/" => "Home",
    $metodo === "GET" && $percorso === "/contatti" => "Scrivici",
    default => null,
};'), [
            dOrder(pSort(), ['$metodo = $_SERVER["REQUEST_METHOD"];', '$percorso = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);', '$risposta = instrada($metodo, $percorso);', 'if ($risposta === null) http_response_code(404);', 'echo $risposta ?? "Non trovata";'], L('Leggi la richiesta, smista, rispondi.', 'Read the request, dispatch, answer.')),
            dMatch(pMatch(), [['REQUEST_METHOD', L('GET, POST…', 'GET, POST…')], ['REQUEST_URI', L('Indirizzo richiesto', 'Requested address')], ['public/', L('Unica cartella visibile', 'Only visible folder')], ['404', L('Pagina non trovata', 'Page not found')], ['php -S', L('Server di sviluppo', 'Development server')]], L('I pezzi di un router.', 'The parts of a router.')),
        ]),
    ],
];
