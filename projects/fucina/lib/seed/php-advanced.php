<?php
/** PHP advanced: namespaces and Composer, interfaces and traits, enums, generators, tests and security. */

return [
    'id' => 'php-advanced', 'family' => 'php', 'after' => 'php-intermediate',
    'title' => L('PHP avanzato', 'Advanced PHP'), 'short' => L('PHP avanzato', 'PHP Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Namespace e Composer, interfacce e trait, enum, generatori, test automatici e sicurezza.', 'Namespaces and Composer, interfaces and traits, enums, generators, automated tests and security.'),
    'lessons' => [
        lesson('namespaces', L('Namespace e Composer', 'Namespaces and Composer'), [
            P('Un **namespace** è il cognome di una classe: due classi `Utente` possono convivere se stanno in namespace diversi. `use` importa una classe per chiamarla con il nome corto.', 'A **namespace** is the surname of a class: two `Utente` classes can live together in different namespaces. `use` imports a class so you can call it by its short name.'),
            C('<?php
namespace App\Corsi;

use App\Utenti\Utente;

final class Iscrizione {
    public function __construct(public Utente $chi) {}
}', 'php'),
            P('**Composer** scarica le librerie e crea l’**autoload**: con lo standard PSR-4 il namespace `App\` corrisponde alla cartella `src/`, e ogni classe si carica da sola quando serve. Basta un `require "vendor/autoload.php";`.', '**Composer** downloads libraries and creates the **autoloader**: with the PSR-4 standard the namespace `App\` matches the `src/` folder, and every class loads by itself when needed. One `require "vendor/autoload.php";` is enough.'),
        ], [
            Q('n1', L('A che cosa serve un namespace?', 'What is a namespace for?'), [L('A evitare conflitti tra nomi', 'To avoid name clashes'), L('A rendere il codice più veloce', 'To make code faster'), L('A criptare le classi', 'To encrypt classes'), L('A creare tabelle', 'To create tables')], 0, L('È come un cognome per le classi.', 'It is like a surname for classes.')),
            Q('n2', L('Quale file carica tutte le classi installate con Composer?', 'Which file loads every class installed with Composer?'), ['vendor/autoload.php', 'composer.json', 'index.php', 'autoload.ini'], 0, L('Lo genera Composer.', 'Composer generates it.')),
            Q('n3', L('Che cosa fa `use App\Utenti\Utente;`?', 'What does `use App\Utenti\Utente;` do?'), [L('Permette di scrivere solo `Utente`', 'Lets you write just `Utente`'), L('Crea un oggetto', 'Creates an object'), L('Include un file a mano', 'Includes a file by hand'), L('Cancella la classe', 'Deletes the class')], 0, L('Importa il nome, non esegue nulla.', 'It imports the name, it runs nothing.')),
        ], task('php', L('Dichiara il namespace `App\Corsi` e importa `App\Utenti\Utente`.', 'Declare the namespace `App\Corsi` and import `App\Utenti\Utente`.'), '<?php
', [re('namespace\s+App\\\\Corsi\s*;', L('Il namespace è dichiarato', 'The namespace is declared')), re('use\s+App\\\\Utenti\\\\Utente\s*;', L('La classe è importata', 'The class is imported'))], '', '<?php
namespace App\Corsi;

use App\Utenti\Utente;'), [
            dOrder(pSort(), ['<?php', 'namespace App\Corsi;', 'use App\Utenti\Utente;', 'final class Corso {}'], L('Prima il namespace, poi gli import, poi il codice.', 'First the namespace, then the imports, then the code.')),
            dMatch(pMatch(), [['namespace', L('Il cognome delle classi', 'The surname of classes')], ['use', L('Importa un nome', 'Imports a name')], ['composer.json', L('Elenca le dipendenze', 'Lists the dependencies')], ['vendor/', L('Le librerie scaricate', 'The downloaded libraries')], ['PSR-4', L('Namespace → cartella', 'Namespace → folder')]], L('Gli attrezzi di un progetto moderno.', 'The tools of a modern project.')),
        ]),
        lesson('interfaces', L('Interfacce, astratte e trait', 'Interfaces, abstracts and traits'), [
            P('Un’**interfaccia** è un contratto: elenca i metodi che una classe promette di avere. Una classe **astratta** dà una base comune ma non si può istanziare. Un **trait** è un pezzo di codice da riusare in più classi.', 'An **interface** is a contract: it lists the methods a class promises to have. An **abstract** class gives a shared base but cannot be instantiated. A **trait** is a piece of code reused in several classes.'),
            C('<?php
interface Pagabile {
    public function totale(): float;
}

trait ConData {
    public function oggi(): string { return date("Y-m-d"); }
}

final class Ordine implements Pagabile {
    use ConData;
    public function totale(): float { return 42.0; }
}', 'php'),
            P('Programmare verso le interfacce rende il codice sostituibile: una funzione che chiede `Pagabile` accetta qualsiasi classe che rispetta il contratto.', 'Coding against interfaces makes code swappable: a function that asks for `Pagabile` accepts any class that keeps the contract.'),
        ], [
            Q('i1', L('Quale parola fa rispettare un’interfaccia?', 'Which keyword makes a class follow an interface?'), ['implements', 'extends', 'use', 'include'], 0, L('`extends` è per l’ereditarietà.', '`extends` is for inheritance.')),
            Q('i2', L('Si può fare `new` di una classe astratta?', 'Can you `new` an abstract class?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo in PHP 8', 'Only in PHP 8'), L('Solo se è vuota', 'Only if empty')], 0, L('Va estesa da una classe concreta.', 'A concrete class must extend it.')),
            Q('i3', L('Come si usa un trait dentro una classe?', 'How do you use a trait inside a class?'), ['use NomeTrait;', 'implements NomeTrait', 'extends NomeTrait', 'trait NomeTrait;'], 0, L('`use` dentro il corpo della classe.', '`use` inside the class body.')),
        ], task('php', L('Crea l’interfaccia `Stampabile` con il metodo `stampa(): string`, e la classe `Report` che la implementa.', 'Create the interface `Stampabile` with the method `stampa(): string`, and the class `Report` that implements it.'), '<?php
', [re('interface\s+Stampabile', L('L’interfaccia esiste', 'The interface exists')), re('function\s+stampa\s*\(\s*\)\s*:\s*string', L('Il metodo ha il tipo giusto', 'The method has the right type')), re('class\s+Report\s+implements\s+Stampabile', L('`Report` la implementa', '`Report` implements it'))], '', '<?php
interface Stampabile {
    public function stampa(): string;
}

class Report implements Stampabile {
    public function stampa(): string { return "ok"; }
}'), [
            dBlanks(pFill(), "interface Volante {\n    public function vola(): void;\n}\nclass Drone ___ Volante {\n    ___ Batteria;\n    public ___ vola(): void {}\n}", ['implements', 'use', 'function'], ['extends', 'include', 'trait'], L('Contratto, trait e metodo.', 'Contract, trait and method.')),
            dFlash(pFlash(), [[L('Una classe può implementare più interfacce', 'A class can implement several interfaces'), true], [L('Una classe può estendere due classi', 'A class can extend two classes'), false], [L('Un trait può contenere metodi', 'A trait can contain methods'), true], [L('Un’interfaccia contiene il codice dei metodi', 'An interface holds the code of its methods'), false]], L('Un solo `extends`, tante interfacce.', 'One `extends`, many interfaces.')),
        ]),
        lesson('enums', L('Enum, readonly e match', 'Enums, readonly and match'), [
            P('Un **enum** elenca i soli valori possibili di qualcosa: niente più stringhe scritte male. Una proprietà **readonly** si assegna una volta sola e poi non cambia.', 'An **enum** lists the only possible values of something: no more misspelt strings. A **readonly** property is set once and never changes.'),
            C('<?php
enum Stato: string {
    case Bozza = "draft";
    case Pubblicato = "published";
}

final class Lezione {
    public function __construct(public readonly Stato $stato) {}
}

$etichetta = match ($lezione->stato) {
    Stato::Bozza => "Da finire",
    Stato::Pubblicato => "Online",
};', 'php'),
            P('`match` è un `switch` più rigoroso: confronta con `===`, restituisce un valore e dà errore se un caso manca.', '`match` is a stricter `switch`: it compares with `===`, returns a value and fails if a case is missing.'),
        ], [
            Q('e1', L('Che cosa succede se modifichi una proprietà `readonly` già assegnata?', 'What happens if you change a `readonly` property already set?'), [L('Un errore', 'An error'), L('Cambia valore', 'It changes'), L('Viene ignorato', 'It is ignored'), L('Diventa null', 'It becomes null')], 0, L('Si assegna una volta sola.', 'It is set only once.')),
            Q('e2', L('Come confronta `match`?', 'How does `match` compare?'), ['===', '==', '=', '<=>'], 0, L('Confronto stretto, tipo compreso.', 'Strict comparison, type included.')),
            Q('e3', L('Come si legge un caso di un enum?', 'How do you read a case of an enum?'), ['Stato::Bozza', 'Stato->Bozza', 'Stato.Bozza', '$Stato[Bozza]'], 0, L('Con l’operatore `::`.', 'With the `::` operator.')),
        ], task('php', L('Crea l’enum `Livello: int` con i casi `Base = 1` e `Avanzato = 2`.', 'Create the enum `Livello: int` with the cases `Base = 1` and `Avanzato = 2`.'), '<?php
', [re('enum\s+Livello\s*:\s*int', L('L’enum ha valori interi', 'The enum has int values')), re('case\s+Base\s*=\s*1', L('C’è `Base`', 'There is `Base`')), re('case\s+Avanzato\s*=\s*2', L('C’è `Avanzato`', 'There is `Avanzato`'))], '', '<?php
enum Livello: int {
    case Base = 1;
    case Avanzato = 2;
}'), [
            dBug(pBug(), ['$voto = 7;', '$giudizio = match ($voto) {', '    10 => "Perfetto",', '    7 => "Buono"', '    default => "Da rivedere",', '};'], 3, '    7 => "Buono",', L('Ogni ramo di `match` finisce con una virgola.', 'Every arm of `match` ends with a comma.')),
            dMatch(pMatch(), [['enum', L('Valori possibili fissi', 'Fixed possible values')], ['readonly', L('Assegnata una volta', 'Set once')], ['match', L('Switch che restituisce', 'A switch that returns')], ['final', L('Non si può estendere', 'Cannot be extended')]], L('Parole del PHP moderno.', 'Words of modern PHP.')),
        ]),
        lesson('generators', L('Closure e generatori', 'Closures and generators'), [
            P('Una **closure** è una funzione senza nome da passare come valore. La forma corta `fn($x) => $x * 2` vede da sola le variabili esterne.', 'A **closure** is a nameless function passed around as a value. The short form `fn($x) => $x * 2` sees outer variables by itself.'),
            C('<?php
$iva = 0.22;
$lordi = array_map(fn($p) => $p * (1 + $iva), [10, 20]);

function righe(string $file): Generator {
    $h = fopen($file, "r");
    while (($riga = fgets($h)) !== false) {
        yield trim($riga);
    }
    fclose($h);
}

foreach (righe("log.txt") as $riga) echo $riga;', 'php'),
            P('Un **generatore** usa `yield` per dare un valore alla volta: legge un file enorme senza caricarlo tutto in memoria.', 'A **generator** uses `yield` to hand out one value at a time: it reads a huge file without loading it all into memory.'),
        ], [
            Q('g1', L('Che cosa fa `yield`?', 'What does `yield` do?'), [L('Dà un valore e si ferma lì', 'Hands out a value and pauses'), L('Chiude la funzione', 'Ends the function'), L('Stampa', 'Prints'), L('Crea una classe', 'Creates a class')], 0, L('Riprende dal punto giusto al giro dopo.', 'It resumes from the same spot next time.')),
            Q('g2', L('Perché un generatore risparmia memoria?', 'Why does a generator save memory?'), [L('Produce un valore alla volta', 'It produces one value at a time'), L('Comprime i dati', 'It compresses data'), L('Usa il disco', 'It uses the disk'), L('Non lo fa', 'It does not')], 0, L('Non serve tenere tutto in un array.', 'There is no need to keep everything in an array.')),
            Q('g3', L('La funzione freccia `fn` vede le variabili esterne?', 'Does the arrow function `fn` see outer variables?'), [L('Sì, da sola', 'Yes, by itself'), L('Solo con `global`', 'Only with `global`'), L('No, mai', 'No, never'), L('Solo le costanti', 'Only constants')], 0, L('Con `function` serve `use`.', 'With `function` you need `use`.')),
        ], task('php', L('Scrivi il generatore `numeri(int $n)` che fa `yield` dei numeri da 1 a `$n`.', 'Write the generator `numeri(int $n)` that yields the numbers from 1 to `$n`.'), '<?php
', [re('function\s+numeri\s*\(\s*int\s+\$n\s*\)', L('La funzione esiste', 'The function exists')), re('yield\s+\$', L('Usa `yield`', 'It uses `yield`'))], '', '<?php
function numeri(int $n): Generator {
    for ($i = 1; $i <= $n; $i++) {
        yield $i;
    }
}'), [
            dBlanks(pFill(), "\$doppi = array_map(___(\$x) => \$x * 2, \$numeri);\nfunction pari(): Generator {\n    foreach ([2, 4, 6] as \$n) ___ \$n;\n}", ['fn', 'yield'], ['return', 'function', 'echo'], L('Funzione freccia e generatore.', 'Arrow function and generator.')),
            dFlash(pFlash(), [[L('`array_map` applica una funzione a ogni elemento', '`array_map` applies a function to each item'), true], [L('Un generatore restituisce un array', 'A generator returns an array'), false], [L('`array_filter` tiene gli elementi che passano un test', '`array_filter` keeps the items that pass a test'), true], [L('`fn` può contenere più istruzioni', '`fn` can contain several statements'), false]], L('`fn` ha una sola espressione.', '`fn` has a single expression.')),
        ]),
        lesson('testing', L('Test automatici', 'Automated tests'), [
            P('Un **test** è codice che controlla altro codice. Con **PHPUnit** ogni metodo che inizia per `test` prepara una situazione, esegue e verifica con le **asserzioni**.', 'A **test** is code that checks other code. With **PHPUnit** every method starting with `test` sets up a situation, runs it and checks with **assertions**.'),
            C('<?php
use PHPUnit\Framework\TestCase;

final class CarrelloTest extends TestCase {
    public function testTotale(): void {
        $c = new Carrello();
        $c->aggiungi(10);
        $c->aggiungi(5);
        $this->assertSame(15, $c->totale());
    }
}', 'php'),
            P('Si lanciano con `vendor/bin/phpunit`. Quando cambi il codice, i test ti dicono subito se hai rotto qualcosa.', 'You run them with `vendor/bin/phpunit`. When you change the code, tests tell you at once if you broke something.'),
        ], [
            Q('t1', L('Quale asserzione controlla valore e tipo?', 'Which assertion checks value and type?'), ['assertSame', 'assertTrue', 'assertCount', 'assertEmpty'], 0, L('`assertSame` usa `===`.', '`assertSame` uses `===`.')),
            Q('t2', L('Da quale classe estende un test PHPUnit?', 'Which class does a PHPUnit test extend?'), ['TestCase', 'Test', 'Assert', 'PHPUnit'], 0, L('`PHPUnit\Framework\TestCase`.', '`PHPUnit\Framework\TestCase`.')),
            Q('t3', L('A che cosa servono i test?', 'What are tests for?'), [L('A scoprire subito le regressioni', 'To catch regressions at once'), L('A rendere il sito più bello', 'To make the site prettier'), L('A sostituire il database', 'To replace the database'), L('A nascondere errori', 'To hide errors')], 0, L('Ti avvisano quando qualcosa si rompe.', 'They warn you when something breaks.')),
        ], task('php', L('Scrivi `testSomma()` che verifica con `assertSame` che `somma(2, 3)` dia `5`.', 'Write `testSomma()` that checks with `assertSame` that `somma(2, 3)` gives `5`.'), '<?php
use PHPUnit\Framework\TestCase;

final class SommaTest extends TestCase {
}', [re('function\s+testSomma\s*\(', L('Il test esiste', 'The test exists')), re('assertSame\s*\(\s*5\s*,\s*somma\s*\(\s*2\s*,\s*3\s*\)\s*\)', L('L’asserzione è giusta', 'The assertion is right'))], '', '<?php
public function testSomma(): void {
    $this->assertSame(5, somma(2, 3));
}'), [
            dOrder(pSort(), ['public function testSconto(): void {', '    $p = new Prezzo(100);', '    $p->sconto(20);', '    $this->assertSame(80, $p->valore());', '}'], L('Prepara, esegui, verifica.', 'Arrange, act, assert.')),
            dMatch(pMatch(), [['assertSame', L('Uguale e stesso tipo', 'Equal and same type')], ['assertTrue', L('Vale true', 'Is true')], ['assertCount', L('Quanti elementi', 'How many items')], ['expectException', L('Deve lanciare un errore', 'Must throw an error')]], L('Le asserzioni più usate.', 'The most used assertions.')),
        ]),
        lesson('security', L('Sicurezza delle applicazioni', 'Application security'), [
            P('Tre regole d’oro: **mai fidarsi dell’input**, **escapare l’output** con `htmlspecialchars` (contro l’XSS) e **proteggere i moduli** con un token CSRF legato alla sessione.', 'Three golden rules: **never trust input**, **escape output** with `htmlspecialchars` (against XSS) and **protect forms** with a CSRF token tied to the session.'),
            C('<?php
session_start();
$_SESSION["csrf"] ??= bin2hex(random_bytes(32));

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && !hash_equals($_SESSION["csrf"], $_POST["csrf"] ?? "")) {
    http_response_code(403);
    exit("Richiesta non valida");
}

echo htmlspecialchars($commento, ENT_QUOTES, "UTF-8");', 'php'),
            P('`hash_equals` confronta senza rivelare nulla con i tempi di risposta. Imposta anche intestazioni come `Content-Security-Policy` e cookie `HttpOnly` e `SameSite`.', '`hash_equals` compares without leaking anything through timing. Also set headers such as `Content-Security-Policy` and `HttpOnly`, `SameSite` cookies.'),
        ], [
            Q('s1', L('Contro che cosa protegge `htmlspecialchars`?', 'What does `htmlspecialchars` protect against?'), ['XSS', 'SQL injection', 'CSRF', L('Password deboli', 'Weak passwords')], 0, L('Trasforma `<` in `&lt;`.', 'It turns `<` into `&lt;`.')),
            Q('s2', L('A che cosa serve un token CSRF?', 'What is a CSRF token for?'), [L('A provare che il modulo arriva dal tuo sito', 'To prove the form comes from your site'), L('A cifrare la password', 'To encrypt the password'), L('A velocizzare la sessione', 'To speed up the session'), L('A contare le visite', 'To count visits')], 0, L('Un altro sito non conosce il token.', 'Another site does not know the token.')),
            Q('s3', L('Perché usare `hash_equals` invece di `===`?', 'Why use `hash_equals` instead of `===`?'), [L('Ha sempre lo stesso tempo di risposta', 'It always takes the same time'), L('È più corto', 'It is shorter'), L('Ignora le maiuscole', 'It ignores case'), L('Funziona solo con i numeri', 'It only works with numbers')], 0, L('Evita gli attacchi a tempo.', 'It avoids timing attacks.')),
        ], task('php', L('Stampa `$nome` con `htmlspecialchars($nome, ENT_QUOTES, "UTF-8")`.', 'Print `$nome` with `htmlspecialchars($nome, ENT_QUOTES, "UTF-8")`.'), '<?php
$nome = $_GET["nome"] ?? "";
', [re('echo\s+htmlspecialchars\s*\(\s*\$nome\s*,\s*ENT_QUOTES', L('L’output è protetto', 'The output is escaped'))], '', '<?php
echo htmlspecialchars($nome, ENT_QUOTES, "UTF-8");'), [
            dBug(pBug(), ['$q = $_GET["q"] ?? "";', 'echo "<h1>Risultati per " . $q . "</h1>";', '$st = $pdo->prepare("SELECT * FROM corsi WHERE titolo LIKE ?");'], 1, 'echo "<h1>Risultati per " . htmlspecialchars($q) . "</h1>";', L('Ciò che scrive l’utente va escapato prima di mostrarlo.', 'What the user writes must be escaped before it is shown.')),
            dMatch(pMatch(), [['XSS', L('Script iniettato nella pagina', 'Script injected into the page')], ['CSRF', L('Richiesta falsa da un altro sito', 'Forged request from another site')], ['SQL injection', L('Dati che diventano comandi', 'Data that becomes commands')], ['HttpOnly', L('Il cookie non si legge da JS', 'JS cannot read the cookie')]], L('Gli attacchi e le loro difese.', 'Attacks and their defences.')),
        ]),
    ],
];
