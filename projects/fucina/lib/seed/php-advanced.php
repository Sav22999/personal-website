<?php
/** PHP advanced: namespaces and Composer, interfaces and traits, enums, generators, tests, security, attributes, property hooks, PHP 8.4/8.5 features, static analysis and performance. */

/** PSR-4: each part of the namespace is a folder, the class name is the file. */
if (!function_exists('fig_phpadv_psr4')) {
    function fig_phpadv_psr4(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 200" font-family="ui-monospace, monospace" font-size="15" fill="#1E1C52">
<defs><marker id="an" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="20" width="608" height="44" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="36" y="48">"autoload": {"psr-4": {"<tspan fill="#B84508" font-weight="700">App\\</tspan>": "<tspan fill="#B84508" font-weight="700">src/</tspan>"}}</text>
<rect x="16" y="86" width="260" height="40" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/>
<text x="32" y="112"><tspan fill="#B84508" font-weight="700">App\</tspan><tspan fill="#1B66BC">Corsi\</tspan><tspan fill="#0B7F5C" font-weight="700">Iscrizione</tspan></text>
<line x1="282" y1="106" x2="350" y2="106" stroke="#1E1C52" stroke-width="2" marker-end="url(#an)"/>
<rect x="356" y="86" width="268" height="40" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/>
<text x="372" y="112"><tspan fill="#B84508" font-weight="700">src/</tspan><tspan fill="#1B66BC">Corsi/</tspan><tspan fill="#0B7F5C" font-weight="700">Iscrizione</tspan>.php</text>
<rect x="16" y="140" width="260" height="40" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/>
<text x="32" y="166"><tspan fill="#B84508" font-weight="700">App\</tspan><tspan fill="#1B66BC">Utenti\</tspan><tspan fill="#0B7F5C" font-weight="700">Utente</tspan></text>
<line x1="282" y1="160" x2="350" y2="160" stroke="#1E1C52" stroke-width="2" marker-end="url(#an)"/>
<rect x="356" y="140" width="268" height="40" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/>
<text x="372" y="166"><tspan fill="#B84508" font-weight="700">src/</tspan><tspan fill="#1B66BC">Utenti/</tspan><tspan fill="#0B7F5C" font-weight="700">Utente</tspan>.php</text>
</svg>
SVG;
    }
}

/** An interface as a contract: two unrelated classes keep it, and a function accepts either. */
if (!function_exists('fig_phpadv_iface')) {
    function fig_phpadv_iface(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ai" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto"><path d="M0 0L10 5L0 10z" fill="#FFFFFF" stroke="#7048E8" stroke-width="1.5"/></marker>
<marker id="ai2" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="160" y="26" text-anchor="middle" font-size="13" fill="#5B33C9">{$t[0]}</text>
<rect x="50" y="36" width="220" height="64" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2" stroke-dasharray="8 5"/>
<g font-family="ui-monospace, monospace" font-size="14">
<text x="160" y="62" text-anchor="middle" font-weight="700">interface Pagabile</text>
<text x="160" y="86" text-anchor="middle">totale(): float</text>
<rect x="16" y="170" width="140" height="50" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="86" y="200" text-anchor="middle">Ordine</text>
<rect x="170" y="170" width="140" height="50" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="240" y="200" text-anchor="middle">Abbonamento</text>
<text x="160" y="140" text-anchor="middle" font-size="13" fill="#5B33C9">implements</text>
<rect x="380" y="100" width="244" height="50" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="502" y="130" text-anchor="middle">paga(Pagabile \$p)</text>
</g>
<path d="M86 166 L120 106" fill="none" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4" marker-end="url(#ai)"/>
<path d="M240 166 L200 106" fill="none" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4" marker-end="url(#ai)"/>
<path d="M274 68 C330 68 340 124 374 125" fill="none" stroke="#1E1C52" stroke-width="2" marker-end="url(#ai2)"/>
<text x="502" y="176" text-anchor="middle" font-size="13" fill="#0B7F5C">{$t[1]}</text>
<text x="320" y="248" text-anchor="middle" font-size="14" fill="#5B5880">{$t[2]}</text>
</svg>
SVG;
    }
}

/** file() loads every line at once; a generator holds one line at a time. The highlight walks down the file. */
if (!function_exists('fig_phpadv_gen')) {
    function fig_phpadv_gen(array $t): string
    {
        $bars = '';
        for ($i = 0; $i < 8; $i++) {
            $y = 64 + $i * 18;
            $bars .= "<rect x=\"44\" y=\"$y\" width=\"" . (150 + ($i * 37) % 70) . "\" height=\"11\" rx=\"5\" fill=\"#E8590C\"/>";
            $bars .= "<rect x=\"350\" y=\"$y\" width=\"" . (80 + ($i * 37) % 40) . "\" height=\"11\" rx=\"5\" fill=\"#C9C7DE\"/>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.cur{animation:walk 6s steps(8) infinite}@keyframes walk{to{transform:translateY(144px)}}@media (prefers-reduced-motion:reduce){.cur{animation:none}}</style>
<defs><marker id="ag" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="150" y="30" text-anchor="middle" font-weight="700" font-family="ui-monospace, monospace">file()</text>
<rect x="30" y="48" width="250" height="160" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="150" y="234" text-anchor="middle" font-size="13" fill="#B84508">{$t[0]}</text>
<line x1="320" y1="16" x2="320" y2="240" stroke="#D9D7EA" stroke-width="2"/>
<text x="480" y="30" text-anchor="middle" font-weight="700" font-family="ui-monospace, monospace">yield</text>
<rect x="336" y="48" width="150" height="160" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
$bars
<rect class="cur" x="342" y="60" width="138" height="19" rx="6" fill="none" stroke="#0B7F5C" stroke-width="2"/>
<line x1="490" y1="128" x2="522" y2="128" stroke="#1E1C52" stroke-width="2" marker-end="url(#ag)"/>
<rect x="528" y="108" width="96" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="540" y="122" width="72" height="11" rx="5" fill="#0B7F5C"/>
<text x="576" y="168" text-anchor="middle" font-size="13" fill="#0B7F5C">{$t[2]}</text>
<text x="480" y="234" text-anchor="middle" font-size="13" fill="#0B7F5C">{$t[1]}</text>
</svg>
SVG;
    }
}

/** CSRF: the form of your site carries the session's token, a form from another site does not and is refused. */
if (!function_exists('fig_phpadv_csrf')) {
    function fig_phpadv_csrf(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="ax" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="24" width="210" height="76" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="32" y="48" font-weight="700" fill="#1B66BC">{$t[0]}</text>
<text x="32" y="74" font-family="ui-monospace, monospace" font-size="13">cookie + csrf=9f2c</text>
<rect x="16" y="140" width="210" height="76" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="7 5"/>
<text x="32" y="164" font-weight="700" fill="#B8255F">{$t[1]}</text>
<text x="32" y="190" font-family="ui-monospace, monospace" font-size="13">cookie + ???</text>
<rect x="300" y="60" width="150" height="120" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="375" y="86" text-anchor="middle" font-weight="700" fill="#B84508">Server</text>
<text x="375" y="114" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">\$_SESSION</text>
<text x="375" y="134" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">csrf=9f2c</text>
<text x="375" y="160" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">hash_equals</text>
<line x1="230" y1="62" x2="294" y2="92" stroke="#1E1C52" stroke-width="2" marker-end="url(#ax)"/>
<line x1="230" y1="178" x2="294" y2="150" stroke="#1E1C52" stroke-width="2" marker-end="url(#ax)"/>
<rect x="510" y="40" width="114" height="44" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="567" y="67" text-anchor="middle" font-weight="700" fill="#0B7F5C">200 OK</text>
<rect x="510" y="156" width="114" height="44" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="567" y="183" text-anchor="middle" font-weight="700" fill="#B8255F">403</text>
<line x1="454" y1="96" x2="504" y2="68" stroke="#0B7F5C" stroke-width="2" marker-end="url(#ax)"/>
<line x1="454" y1="146" x2="504" y2="174" stroke="#F03E82" stroke-width="2" marker-end="url(#ax)"/>
<text x="567" y="104" text-anchor="middle" font-size="13" fill="#0B7F5C">{$t[2]}</text>
<text x="567" y="220" text-anchor="middle" font-size="13" fill="#B8255F">{$t[3]}</text>
<text x="320" y="256" text-anchor="middle" fill="#5B5880">{$t[4]}</text>
</svg>
SVG;
    }
}

/** The time of a request without and with OPcache, as two bars. */
if (!function_exists('fig_phpadv_opcache')) {
    function fig_phpadv_opcache(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="16" y="44" font-weight="700">{$t[0]}</text>
<rect x="170" y="24" width="110" height="34" rx="8" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="225" y="46" text-anchor="middle">{$t[2]}</text>
<rect x="284" y="24" width="160" height="34" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="364" y="46" text-anchor="middle">{$t[3]}</text>
<rect x="448" y="24" width="110" height="34" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="503" y="46" text-anchor="middle">{$t[4]}</text>
<text x="16" y="114" font-weight="700">{$t[1]}</text>
<rect x="170" y="94" width="110" height="34" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="225" y="116" text-anchor="middle">{$t[4]}</text>
<rect x="170" y="146" width="230" height="34" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4"/><text x="285" y="168" text-anchor="middle" font-size="13">{$t[5]}</text>
<path d="M225 144 v-12" stroke="#7048E8" stroke-width="2"/>
<line x1="170" y1="200" x2="620" y2="200" stroke="#8A87B0" stroke-width="2"/>
<text x="620" y="194" text-anchor="end" font-size="13" fill="#5B5880">{$t[6]} →</text>
</svg>
SVG;
    }
}

return [
    'id' => 'php-advanced', 'family' => 'php', 'after' => 'php-intermediate',
    'title' => L('PHP avanzato', 'Advanced PHP'), 'short' => L('PHP avanzato', 'PHP Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Namespace e Composer, interfacce e trait, enum, generatori, test automatici, sicurezza, attributi, hook delle proprietà, novità di PHP 8.4 e 8.5, analisi statica e prestazioni.', 'Namespaces and Composer, interfaces and traits, enums, generators, automated tests, security, attributes, property hooks, PHP 8.4 and 8.5 features, static analysis and performance.'),
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
            Fig(fig_phpadv_psr4(),
                L('In alto la regola di composer.json che lega App\ a src/. Sotto, due righe: App\Corsi\Iscrizione diventa src/Corsi/Iscrizione.php e App\Utenti\Utente diventa src/Utenti/Utente.php, con i pezzi corrispondenti dello stesso colore.', 'At the top the composer.json rule tying App\ to src/. Below, two rows: App\Corsi\Iscrizione becomes src/Corsi/Iscrizione.php and App\Utenti\Utente becomes src/Utenti/Utente.php, with matching parts in the same colour.'),
                L('Ogni pezzo del namespace è una cartella, il nome della classe è il file.', 'Each part of the namespace is a folder, the class name is the file.')),
            Tip('`namespace` deve essere la prima istruzione del file. Dentro un namespace, le classi globali di PHP vanno scritte con la barra iniziale (`new \DateTimeImmutable()`) oppure importate con `use`, altrimenti PHP le cerca nel namespace corrente.', '`namespace` must be the first statement in the file. Inside a namespace, PHP’s global classes need a leading backslash (`new \DateTimeImmutable()`) or a `use` import, otherwise PHP looks for them in the current namespace.'),
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
            Fig(L(fig_phpadv_iface(['contratto', 'accetta qualsiasi Pagabile', 'Due classi diverse, un solo contratto: paga() non deve sapere quale riceve.']), fig_phpadv_iface(['contract', 'accepts any Pagabile', 'Two different classes, one contract: paga() does not need to know which one it gets.'])),
                L('In alto l’interfaccia Pagabile con il metodo totale(). Le classi Ordine e Abbonamento la implementano, con frecce tratteggiate verso l’alto. Una freccia porta a paga(Pagabile $p), che le accetta entrambe.', 'At the top the interface Pagabile with the method totale(). The classes Ordine and Abbonamento implement it, with dashed arrows pointing up. One arrow leads to paga(Pagabile $p), which accepts both.'),
                L('Chi chiede un’interfaccia accetta ogni classe che la implementa.', 'Asking for an interface accepts every class that implements it.')),
            P('Come scegliere? Un’interfaccia quando classi diverse condividono una **capacità** ma non il codice; una classe astratta quando condividono anche una parte dell’implementazione; un trait quando vuoi solo copiare qualche metodo in più classi, senza creare un tipo. Una classe estende una sola classe ma può implementare quante interfacce vuole: `class Ordine implements Pagabile, JsonSerializable`.', 'How to choose? An interface when different classes share an **ability** but not the code; an abstract class when they also share part of the implementation; a trait when you only want to copy a few methods into several classes, without creating a type. A class extends only one class but can implement as many interfaces as it likes: `class Ordine implements Pagabile, JsonSerializable`.'),
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
            P('Un enum con valori (`: string` o `: int`) si salva bene nel database: `->value` dà `"draft"`, mentre `Stato::from("draft")` fa il viaggio inverso e `tryFrom` restituisce `null` invece di un errore se il valore non esiste. `Stato::cases()` elenca tutti i casi, comodo per un menu a tendina. Un enum può avere anche metodi, per esempio `etichetta()`, così la logica sta vicino ai valori.', 'An enum with values (`: string` or `: int`) stores well in the database: `->value` gives `"draft"`, while `Stato::from("draft")` makes the opposite trip and `tryFrom` returns `null` instead of an error if the value does not exist. `Stato::cases()` lists every case, handy for a drop-down menu. An enum can also have methods, such as `etichetta()`, so the logic sits next to the values.'),
            Tip('`readonly` è perfetto per gli oggetti che rappresentano un valore, come un prezzo o un intervallo di date: se serve un valore diverso, crei un oggetto nuovo invece di cambiare quello che altri stanno già usando.', '`readonly` is perfect for objects that stand for a value, such as a price or a date range: if you need a different value, you create a new object instead of changing the one others are already using.'),
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
            Fig(L(fig_phpadv_gen(['tutte le righe in memoria insieme', 'una riga alla volta', 'memoria']), fig_phpadv_gen(['every line in memory at once', 'one line at a time', 'memory'])),
                L('A sinistra file() riempie la memoria con tutte le otto righe del file. A destra un riquadro verde scorre le righe del file una alla volta e solo quella riga passa in memoria.', 'On the left file() fills memory with all eight lines of the file. On the right a green frame walks down the file lines one at a time and only that line goes into memory.'),
                L('Con `yield` la memoria usata resta la stessa, qualunque sia la dimensione del file.', 'With `yield` the memory used stays the same, whatever the size of the file.')),
            P('Il codice di un generatore parte solo quando il `foreach` chiede il valore successivo, e si ferma a ogni `yield` finché non gliene chiedi un altro: per questo può anche produrre una sequenza infinita. Un generatore però si percorre una volta sola. Quanto alle closure, la forma lunga `function ($p) use ($iva) { … }` deve elencare in `use` le variabili esterne, e le copia nel momento in cui la crei.', 'A generator’s code starts only when the `foreach` asks for the next value, and it pauses at every `yield` until you ask for another one: that is why it can even produce an endless sequence. A generator can be walked only once, though. As for closures, the long form `function ($p) use ($iva) { … }` must list the outer variables in `use`, and copies them at the moment you create it.'),
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
            P('Un **test** è codice che controlla altro codice. Con **PHPUnit** ogni metodo pubblico che inizia per `test` (o segnato con l’attributo `#[Test]`) prepara una situazione, esegue e verifica con le **asserzioni**.', 'A **test** is code that checks other code. With **PHPUnit** every public method starting with `test` (or marked with the `#[Test]` attribute) sets up a situation, runs it and checks with **assertions**.'),
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
            P('Un buon test segue tre passi, spesso chiamati **Arrange, Act, Assert**: prepari gli oggetti, esegui l’azione, controlli il risultato. Prova un solo comportamento per test e dagli un nome che lo racconti, come `testCarrelloVuotoCostaZero`: quando fallisce, il nome dice già che cosa si è rotto. Non dimenticare i casi limite: carrello vuoto, quantità negative, testo vuoto.', 'A good test follows three steps, often called **Arrange, Act, Assert**: you set up the objects, run the action, check the result. Test one behaviour per test and give it a name that tells it, such as `testCarrelloVuotoCostaZero`: when it fails, the name already says what broke. Do not forget edge cases: an empty cart, negative quantities, empty text.'),
            Tip('`assertSame` confronta anche il tipo (`15` non è `15.0` né `"15"`), `assertEquals` no: preferisci `assertSame`, che scopre più errori.', '`assertSame` compares the type too (`15` is not `15.0` nor `"15"`), `assertEquals` does not: prefer `assertSame`, which catches more mistakes.'),
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
            P('Perché serve il token? Il browser manda i cookie di un sito a ogni richiesta verso quel sito, anche quando il modulo sta su una pagina malevola: senza controlli, un altro sito potrebbe far partire a tuo nome un acquisto o una modifica. Il token CSRF sta solo nella sessione e nei moduli delle tue pagine, quindi un sito esterno non lo conosce e la sua richiesta viene respinta.', 'Why do you need the token? The browser sends a site’s cookies with every request to that site, even when the form sits on a malicious page: without checks, another site could start a purchase or a change in your name. The CSRF token lives only in the session and in the forms of your pages, so an outside site does not know it and its request is refused.'),
            Fig(L(fig_phpadv_csrf(['Il tuo sito', 'Un sito malevolo', 'token uguale', 'token assente', 'I cookie arrivano in entrambi i casi: solo il token distingue le richieste vere.']), fig_phpadv_csrf(['Your site', 'A malicious site', 'token matches', 'token missing', 'Cookies arrive in both cases: only the token tells the real requests apart.'])),
                L('Due moduli inviano una richiesta al server. Quello del tuo sito porta il cookie e il token csrf=9f2c, uguale a quello in sessione, e riceve 200 OK. Quello di un sito malevolo porta il cookie ma non il token e riceve 403.', 'Two forms send a request to the server. The one from your site carries the cookie and the token csrf=9f2c, equal to the one in the session, and gets 200 OK. The one from a malicious site carries the cookie but not the token and gets 403.'),
                L('Il token CSRF prova che il modulo viene dalle tue pagine.', 'The CSRF token proves the form comes from your pages.')),
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
        lesson('attributes', L('Attributi e Reflection', 'Attributes and Reflection'), [
            P('Un **attributo** `#[...]` aggiunge metadati a classi, metodi, proprietà o parametri. Da solo non fa nulla: lo legge un framework o il tuo codice con la **Reflection**. Symfony e Doctrine li usano per rotte, validazione e tabelle.', 'An **attribute** `#[...]` adds metadata to classes, methods, properties or parameters. On its own it does nothing: a framework or your code reads it through **Reflection**. Symfony and Doctrine use them for routes, validation and tables.'),
            C('<?php
#[Attribute(Attribute::TARGET_METHOD)]
final class Rotta {
    public function __construct(public string $percorso) {}
}

final class CorsiController {
    #[Rotta("/corsi")]
    public function elenco(): string { return "Tutti i corsi"; }
}

$metodo = new ReflectionMethod(CorsiController::class, "elenco");
foreach ($metodo->getAttributes(Rotta::class) as $attr) {
    echo $attr->newInstance()->percorso;   // /corsi
}', 'php'),
            C('<?php
class Base {
    public function prezzo(): float { return 10.0; }
}
class Scontato extends Base {
    #[\Override]
    public function prezzo(): float { return 8.0; }
}
function accedi(string $utente, #[\SensitiveParameter] string $password): void {}', 'php'),
            P('Alcuni attributi li controlla il motore stesso: `#[\Override]` (PHP 8.3) dà errore se il padre non ha quel metodo, `#[\SensitiveParameter]` nasconde il valore nelle tracce degli errori, `#[\Deprecated]` (PHP 8.4) segnala codice da non usare più.', 'Some attributes are checked by the engine itself: `#[\Override]` (PHP 8.3) fails if the parent has no such method, `#[\SensitiveParameter]` hides the value in error traces, `#[\Deprecated]` (PHP 8.4) flags code not to use any more.'),
        ], [
            Q('a1', L('Come si scrive un attributo in PHP?', 'How do you write an attribute in PHP?'), ['@Rotta("/corsi")', '#[Rotta("/corsi")]', '<<Rotta("/corsi")>>', '/** Rotta("/corsi") */'], 1, L('Cancelletto e parentesi quadre, da PHP 8.0.', 'Hash and square brackets, since PHP 8.0.')),
            Q('a2', L('Che cosa succede se segni con `#[\Override]` un metodo che il padre non ha?', 'What happens if you mark with `#[\Override]` a method the parent does not have?'), [L('PHP lo ignora in silenzio', 'PHP silently ignores it'), L('Il metodo diventa astratto', 'The method becomes abstract'), L('Errore: il metodo non sovrascrive niente', 'Error: the method overrides nothing'), L('Il metodo viene rinominato', 'The method gets renamed')], 2, L('Ti protegge dai refusi nei nomi dei metodi.', 'It protects you from typos in method names.')),
            Q('a3', L('Come legge un attributo il tuo codice?', 'How does your code read an attribute?'), [L('Con `$_SERVER["ATTRIBUTES"]`', 'With `$_SERVER["ATTRIBUTES"]`'), L('Con un `include` del file della classe', 'With an `include` of the class file'), L('Lo legge solo il browser', 'Only the browser reads it'), L('Con la Reflection, ad esempio `getAttributes()`', 'With Reflection, for example `getAttributes()`')], 3, L('`newInstance()` crea l’oggetto attributo con i suoi argomenti.', '`newInstance()` builds the attribute object with its arguments.')),
        ], task('php', L('Segna il metodo `totale()` della classe figlia con `#[\Override]`.', 'Mark the child class’s `totale()` method with `#[\Override]`.'), '<?php
class Carrello extends Base {
    public function totale(): float { return 0.0; }
}', [re('#\[\s*\\\\?Override\s*\]\s*public\s+function\s+totale', L('Il metodo è segnato', 'The method is marked'))], '', '<?php
class Carrello extends Base {
    #[\Override]
    public function totale(): float { return 0.0; }
}'), [
            dMatch(pMatch(), [['#[\Override]', L('Deve sovrascrivere un metodo', 'Must override a method')], ['#[\SensitiveParameter]', L('Nasconde il valore nelle tracce', 'Hides the value in traces')], ['#[\Deprecated]', L('Codice da non usare più', 'Code not to use any more')], ['#[Attribute]', L('Dichiara una classe attributo', 'Declares an attribute class')], ['getAttributes()', L('Legge gli attributi', 'Reads the attributes')]], L('Attributi del motore e attributi tuoi.', 'Engine attributes and your own.')),
            dFlash(pFlash(), [[L('Un attributo da solo non esegue codice', 'An attribute runs no code on its own'), true], [L('`#[\Override]` è controllato dal motore di PHP', '`#[\Override]` is checked by the PHP engine'), true], [L('Gli attributi si scrivono dentro i commenti', 'Attributes are written inside comments'), false], [L('Un attributo può avere argomenti', 'An attribute can take arguments'), true]], L('Metadati che il codice può leggere.', 'Metadata that code can read.')),
        ]),
        lesson('property-hooks', L('Hook delle proprietà e visibilità asimmetrica', 'Property hooks and asymmetric visibility'), [
            P('Da **PHP 8.4** una proprietà può avere degli **hook**: un blocco `set` che controlla o trasforma il valore quando viene assegnato, un blocco `get` che lo calcola quando viene letto. Così spariscono molti getter e setter scritti a mano.', 'Since **PHP 8.4** a property can have **hooks**: a `set` block that checks or transforms the value when it is assigned, a `get` block that computes it when it is read. Many hand-written getters and setters disappear.'),
            C('<?php
final class Utente {
    public private(set) string $email;

    public string $nome {
        set(string $valore) {
            $this->nome = trim($valore);
        }
    }

    public string $iniziale {
        get => strtoupper($this->nome[0]);
    }

    public function __construct(string $nome, string $email) {
        $this->nome = $nome;
        $this->email = strtolower($email);
    }
}

$u = new Utente("  ada ", "ADA@Example.com");
echo $u->nome;        // ada
echo $u->iniziale;    // A
$u->email = "x@y.it"; // Errore: si scrive solo dentro la classe', 'php'),
            P('`public private(set)` è la **visibilità asimmetrica**: tutti leggono, solo la classe scrive. Una proprietà con il solo `get` che non usa il proprio valore è **virtuale**: viene calcolata a ogni lettura e non si può assegnare. Gli hook non si possono mettere sulle proprietà `static`.', '`public private(set)` is **asymmetric visibility**: everyone reads, only the class writes. A property with only a `get` hook that does not use its own value is **virtual**: it is computed on every read and cannot be assigned. Hooks cannot be put on `static` properties.'),
        ], [
            Q('p1', L('Che cosa permette `public private(set) string $email`?', 'What does `public private(set) string $email` allow?'), [L('Leggerla da fuori, scriverla solo dentro la classe', 'Reading it from outside, writing it only inside the class'), L('Scriverla da fuori ma non leggerla', 'Writing it from outside but not reading it'), L('Leggerla solo dalle sottoclassi', 'Reading it only from subclasses'), L('Assegnarla una volta sola in tutto il programma', 'Assigning it only once in the whole program')], 0, L('Visibilità diversa per lettura e scrittura.', 'Different visibility for reading and writing.')),
            Q('p2', L('Da quale versione di PHP esistono gli hook delle proprietà?', 'Since which PHP version do property hooks exist?'), ['PHP 7.4', 'PHP 8.0', 'PHP 8.4', 'PHP 8.2'], 2, L('Arrivati a novembre 2024, insieme alla visibilità asimmetrica.', 'They arrived in November 2024, together with asymmetric visibility.')),
            Q('p3', L('Una proprietà con il solo hook `get` che non usa il proprio valore…', 'A property with only a `get` hook that does not use its own value…'), [L('Si può scrivere ma non leggere', 'Can be written but not read'), L('Viene salvata nella sessione', 'Is saved in the session'), L('Diventa automaticamente statica', 'Automatically becomes static'), L('È calcolata a ogni lettura e non si può assegnare', 'Is computed on every read and cannot be assigned')], 3, L('È una proprietà virtuale.', 'It is a virtual property.')),
        ], task('php', L('Aggiungi la proprietà virtuale `public float $lordo` con un hook `get` che restituisce `$this->netto * 1.22`.', 'Add the virtual property `public float $lordo` with a `get` hook returning `$this->netto * 1.22`.'), '<?php
final class Prezzo {
    public function __construct(public float $netto) {}
}', [re('public\s+float\s+\$lordo\s*\{\s*get\s*(=>\s*\$this->netto\s*\*\s*1\.22|\{\s*return\s+\$this->netto\s*\*\s*1\.22\s*;\s*\})', L('Il lordo si calcola dal netto', 'The gross is computed from the net'))], '', '<?php
final class Prezzo {
    public float $lordo {
        get => $this->netto * 1.22;
    }
    public function __construct(public float $netto) {}
}'), [
            dBlanks(pFill(), 'public string $titolo {
    ___(string $v) {
        $this->titolo = ucfirst($v);
    }
}
public ___(set) int $voti = 0;
public string $sigla {
    ___ => strtoupper($this->titolo);
}', ['set', 'private', 'get'], ['put', 'readonly', 'read'], L('Hook in scrittura, visibilità asimmetrica, hook in lettura.', 'Write hook, asymmetric visibility, read hook.')),
            dFlash(pFlash(), [[L('Gli hook evitano molti getter e setter scritti a mano', 'Hooks avoid many hand-written getters and setters'), true], [L('`private(set)` impedisce anche la lettura da fuori', '`private(set)` also blocks reading from outside'), false], [L('Una proprietà virtuale non occupa spazio nell’oggetto', 'A virtual property takes no space in the object'), true], [L('Gli hook si possono mettere anche sulle proprietà `static`', 'Hooks can also go on `static` properties'), false]], L('Logica dentro la proprietà, non in metodi a parte.', 'Logic inside the property, not in separate methods.')),
        ]),
        lesson('modern-php', L('Novità di PHP 8.4 e 8.5', 'What is new in PHP 8.4 and 8.5'), [
            P('PHP esce con una versione nuova ogni novembre. La **8.4** ha aggiunto `array_find`, `array_any` e `array_all`, che evitano cicli scritti a mano, e permette `new Classe()->metodo()` senza parentesi in più. La **8.5** ha portato l’operatore **pipe** `|>` e le funzioni `array_first` e `array_last`.', 'PHP ships a new version every November. **8.4** added `array_find`, `array_any` and `array_all`, which avoid hand-written loops, and allows `new Class()->method()` without extra parentheses. **8.5** brought the **pipe** operator `|>` and the functions `array_first` and `array_last`.'),
            C('<?php
$corsi = [
    ["titolo" => "PHP", "livello" => 3],
    ["titolo" => "Go", "livello" => 1],
];

// PHP 8.4
$facile = array_find($corsi, fn($c) => $c["livello"] === 1);   // il corso Go
$tuttiValidi = array_all($corsi, fn($c) => $c["titolo"] !== "");
$oggi = new DateTimeImmutable()->format("Y-m-d");

// PHP 8.5
$slug = "  Corso di PHP  "
    |> trim(...)
    |> strtolower(...)
    |> (fn($s) => str_replace(" ", "-", $s));                  // corso-di-php
$ultimo = array_last($corsi);', 'php'),
            P('Il pipe passa il valore a sinistra come unico argomento della funzione a destra: la catena si legge dall’alto in basso. `trim(...)` è la sintassi delle **callable di prima classe** (PHP 8.1): trasforma una funzione in un valore. Una funzione freccia dentro una pipe va tra parentesi.', 'The pipe passes the left-hand value as the only argument of the function on the right: the chain reads top to bottom. `trim(...)` is the **first-class callable** syntax (PHP 8.1): it turns a function into a value. An arrow function inside a pipe goes in parentheses.'),
            Tip('Prima di usare una novità, controlla la versione del server con `php -v` o `PHP_VERSION`: un hosting rimasto indietro darebbe un errore di sintassi.', 'Before using a new feature, check the server’s version with `php -v` or `PHP_VERSION`: a hosting that lags behind would throw a syntax error.'),
        ], [
            Q('m1', L('Che cosa restituisce `array_find`?', 'What does `array_find` return?'), [L('L’indice di tutti gli elementi trovati', 'The index of every item found'), L('true se almeno un elemento va bene', 'true if at least one item matches'), L('Un nuovo array con gli elementi trovati', 'A new array with the items found'), L('Il primo elemento che soddisfa la condizione, oppure null', 'The first item that meets the condition, or null')], 3, L('Per sapere solo se c’è, usa `array_any`.', 'To know only whether one exists, use `array_any`.')),
            Q('m2', L('Che cosa fa `$x |> strtoupper(...)`?', 'What does `$x |> strtoupper(...)` do?'), [L('Confronta $x con strtoupper', 'Compares $x with strtoupper'), L('Passa $x a strtoupper e ne restituisce il risultato', 'Passes $x to strtoupper and returns its result'), L('Aggiunge strtoupper a un array', 'Appends strtoupper to an array'), L('Crea un generatore', 'Creates a generator')], 1, L('È come scrivere `strtoupper($x)`.', 'It is like writing `strtoupper($x)`.')),
            Q('m3', L('Che cos’è `trim(...)`?', 'What is `trim(...)`?'), [L('Una chiamata di trim senza argomenti', 'A call to trim with no arguments'), L('Un errore di sintassi', 'A syntax error'), L('Una callable di prima classe che rappresenta trim', 'A first-class callable standing for trim'), L('Un parametro variadico', 'A variadic parameter')], 2, L('I tre punti fanno parte della sintassi.', 'The three dots are part of the syntax.')),
        ], task('php', L('Con `array_any` scrivi in `$ciSonoGratis` se in `$prezzi` c’è almeno un valore uguale a `0`.', 'With `array_any` store in `$ciSonoGratis` whether `$prezzi` has at least one value equal to `0`.'), '<?php
$prezzi = [12.5, 0, 8];
', [re('\$ciSonoGratis\s*=\s*array_any\s*\(\s*\$prezzi\s*,\s*(static\s+)?fn\s*\(\s*(\w+\s+)?\$(\w+)\s*\)\s*=>\s*(\$\3\s*===?\s*0(\.0)?|0(\.0)?\s*===?\s*\$\3)\s*\)', L('Usi array_any con una funzione freccia', 'You use array_any with an arrow function'))], '', '<?php
$ciSonoGratis = array_any($prezzi, fn($p) => $p == 0);'), [
            dMatch(pMatch(), [['array_find', L('Primo elemento che va bene', 'First matching item')], ['array_any', L('Almeno uno va bene?', 'Does at least one match?')], ['array_all', L('Vanno bene tutti?', 'Do they all match?')], ['|>', L('Passa il risultato avanti', 'Passes the result along')], ['array_first', L('Primo valore dell’array', 'First value of the array')]], L('Novità recenti di PHP.', 'Recent PHP additions.')),
            dOrder(pSort(), ['$titolo = "  Ciao Mondo  "', '    |> trim(...)', '    |> strtolower(...)', '    |> ucfirst(...);', 'echo $titolo; // Ciao mondo'], L('Il valore attraversa le funzioni dall’alto in basso.', 'The value flows through the functions top to bottom.')),
        ]),
        lesson('static-analysis', L('Analisi statica e qualità', 'Static analysis and quality'), [
            P('PHP controlla i tipi solo mentre il codice gira. Uno strumento di **analisi statica** come **PHPStan** (o Psalm) legge il codice senza eseguirlo e trova prima metodi chiamati su `null`, tipi sbagliati e codice morto. Si sceglie un livello di severità e lo si alza piano piano.', 'PHP checks types only while the code runs. A **static analysis** tool such as **PHPStan** (or Psalm) reads the code without running it and finds methods called on `null`, wrong types and dead code beforehand. You pick a strictness level and raise it step by step.'),
            C('composer require --dev phpstan/phpstan
vendor/bin/phpstan analyse src --level 8', 'bash'),
            C('<?php
/**
 * @param list<int> $voti
 * @return array{media: float|int, max: int}
 */
function riepilogo(array $voti): array {
    return ["media" => array_sum($voti) / count($voti), "max" => max($voti)];
}

function trova(int $id): ?Utente { /* ... */ }
echo trova(7)->nome;   // PHPStan: possibile accesso su null', 'php'),
            P('I commenti `@param` e `@return` aggiungono ciò che il linguaggio non sa dire, come il tipo degli elementi di un array. Uno strumento di stile come **PHP-CS-Fixer** sistema la formattazione. Lanciali a ogni push, nell’integrazione continua.', 'The `@param` and `@return` comments add what the language cannot express, such as the type of an array’s items. A style tool such as **PHP-CS-Fixer** fixes the formatting. Run them on every push, in continuous integration.'),
        ], [
            Q('s1', L('Che cosa fa PHPStan?', 'What does PHPStan do?'), [L('Esegue i test più in fretta', 'Runs tests faster'), L('Comprime i file PHP per il server', 'Compresses PHP files for the server'), L('Analizza il codice senza eseguirlo e trova errori', 'Analyses code without running it and finds errors'), L('Traduce PHP in JavaScript', 'Translates PHP into JavaScript')], 2, L('Gli errori emergono prima di arrivare in produzione.', 'Errors show up before reaching production.')),
            Q('s2', L('Che cosa significa `@param list<int> $voti`?', 'What does `@param list<int> $voti` mean?'), [L('Che $voti ha al massimo un elemento', 'That $voti has at most one item'), L('Che $voti è un singolo intero', 'That $voti is a single int'), L('Che $voti è facoltativo', 'That $voti is optional'), L('Che $voti è una lista di interi con indici 0, 1, 2…', 'That $voti is a list of ints indexed 0, 1, 2…')], 3, L('`array` da solo non dice che cosa contiene.', '`array` alone does not say what it holds.')),
            Q('s3', L('Perché PHPStan segnala `trova(7)->nome`?', 'Why does PHPStan flag `trova(7)->nome`?'), [L('Perché `nome` non è un metodo', 'Because `nome` is not a method'), L('Perché `trova` può restituire null', 'Because `trova` may return null'), L('Perché manca `declare(strict_types=1)`', 'Because `declare(strict_types=1)` is missing'), L('Perché 7 non è una stringa', 'Because 7 is not a string')], 1, L('Il tipo di ritorno è `?Utente`.', 'The return type is `?Utente`.')),
        ], task('php', L('Aggiungi il commento `/** @return list<string> */` sopra la funzione `nomi()`.', 'Add the comment `/** @return list<string> */` above the `nomi()` function.'), '<?php
function nomi(): array {
    return ["Ada", "Bea"];
}', [re('/\*\*[\s\S]*@return\s+(list|array)<\s*string\s*>[\s\S]*\*/\s*function\s+nomi', L('Il tipo di ritorno è descritto', 'The return type is described'))], '', '<?php
/** @return list<string> */
function nomi(): array {
    return ["Ada", "Bea"];
}'), [
            dMatch(pMatch(), [['PHPStan', L('Analisi statica', 'Static analysis')], ['PHP-CS-Fixer', L('Sistema lo stile', 'Fixes the style')], ['@param', L('Tipo di un parametro', 'Type of a parameter')], ['--level', L('Quanto essere severi', 'How strict to be')], ['CI', L('Controlli a ogni push', 'Checks on every push')]], L('Gli attrezzi della qualità.', 'The quality tools.')),
            dFlash(pFlash(), [[L('PHPStan deve eseguire il codice per trovare errori', 'PHPStan must run the code to find errors'), false], [L('I commenti `@return` possono descrivere la forma di un array', '`@return` comments can describe an array’s shape'), true], [L('Conviene partire da un livello basso e alzarlo', 'It pays to start at a low level and raise it'), true], [L('L’analisi statica sostituisce del tutto i test', 'Static analysis fully replaces tests'), false]], L('Analisi statica e test si completano.', 'Static analysis and tests complement each other.')),
        ]),
        lesson('performance', L('Prestazioni: OPcache, cache e misure', 'Performance: OPcache, caching and measuring'), [
            P('**OPcache** tiene in memoria il codice PHP già compilato, così non viene ricompilato a ogni richiesta: in produzione va sempre attivo. Il **JIT** (da PHP 8.0) compila in codice macchina e aiuta nei calcoli pesanti, poco nelle pagine che aspettano il database.', '**OPcache** keeps already compiled PHP code in memory, so it is not recompiled on every request: in production it must always be on. The **JIT** (since PHP 8.0) compiles to machine code and helps heavy computation, little with pages waiting on the database.'),
            Fig(L(fig_phpadv_opcache(['Senza OPcache', 'Con OPcache', 'legge il file', 'compila', 'esegue', 'opcode già in memoria', 'tempo']), fig_phpadv_opcache(['Without OPcache', 'With OPcache', 'reads the file', 'compiles', 'runs', 'opcodes already in memory', 'time'])),
                L('Due barre del tempo di una richiesta. Senza OPcache: legge il file, compila, esegue. Con OPcache: solo esegue, perché il codice compilato è già in memoria.', 'Two time bars for a request. Without OPcache: reads the file, compiles, runs. With OPcache: only runs, because the compiled code is already in memory.'),
                L('OPcache salta lettura e compilazione a ogni richiesta.', 'OPcache skips reading and compiling on every request.')),
            C('; php.ini in produzione
opcache.enable=1
opcache.memory_consumption=256
opcache.validate_timestamps=0', 'ini'),
            C('<?php
$inizio = hrtime(true);

$corsi = apcu_fetch("corsi_popolari", $trovato);
if (!$trovato) {
    $corsi = $pdo->query("SELECT * FROM corsi ORDER BY iscritti DESC LIMIT 10")->fetchAll();
    apcu_store("corsi_popolari", $corsi, 300);   // 5 minuti
}

$ms = (hrtime(true) - $inizio) / 1e6;
error_log("corsi in {$ms} ms");', 'php'),
            P('Prima misura, poi ottimizza. Spesso il collo di bottiglia è il database: una query dentro un ciclo (il problema **N+1**) costa più di qualsiasi trucco. Con `validate_timestamps=0` PHP non ricontrolla i file: dopo ogni rilascio va svuotata la cache, ad esempio riavviando PHP-FPM.', 'Measure first, then optimise. Often the bottleneck is the database: a query inside a loop (the **N+1** problem) costs more than any trick. With `validate_timestamps=0` PHP does not recheck the files: after every release the cache must be cleared, for example by restarting PHP-FPM.'),
        ], [
            Q('o1', L('Che cosa fa OPcache?', 'What does OPcache do?'), [L('Tiene in memoria il codice già compilato', 'Keeps already compiled code in memory'), L('Comprime le pagine prima di inviarle', 'Compresses pages before sending them'), L('Salva le sessioni nel database', 'Saves sessions in the database'), L('Riduce il peso delle immagini', 'Shrinks images')], 0, L('Niente ricompilazione a ogni richiesta.', 'No recompiling on every request.')),
            Q('o2', L('Che cos’è il problema N+1?', 'What is the N+1 problem?'), [L('Un errore di arrotondamento nei float', 'A rounding error in floats'), L('Un server con un processo di troppo', 'A server with one process too many'), L('Un ciclo che gira una volta in più', 'A loop running one extra time'), L('Una query in più per ogni elemento di un elenco', 'One extra query for every item of a list')], 3, L('Meglio una sola query con JOIN o GROUP BY.', 'A single query with JOIN or GROUP BY is better.')),
            Q('o3', L('Quando il JIT di PHP aiuta davvero?', 'When does PHP’s JIT really help?'), [L('Nelle pagine che aspettano il database', 'In pages waiting on the database'), L('Nei calcoli pesanti sulla CPU', 'In heavy CPU computation'), L('Nel caricamento delle immagini', 'In loading images'), L('Nell’invio delle email', 'In sending emails')], 1, L('Le attese di rete non diventano più corte.', 'Network waits do not get shorter.')),
        ], task('php', L('Misura il tempo di `calcola()`: salva `hrtime(true)` in `$inizio` prima della chiamata e calcola `$ms` dopo, dividendo la differenza per `1e6`.', 'Time `calcola()`: save `hrtime(true)` in `$inizio` before the call and compute `$ms` afterwards, dividing the difference by `1e6`.'), '<?php
calcola();
', [re('\$inizio\s*=\s*hrtime\s*\(\s*true\s*\)\s*;[\s\S]*calcola\s*\(\s*\)', L('Parti dal tempo iniziale', 'You start from the initial time')), re('\$ms\s*=\s*\(\s*hrtime\s*\(\s*true\s*\)\s*-\s*\$inizio\s*\)\s*/\s*(1e6|1_?000_?000)', L('Calcoli i millisecondi', 'You compute the milliseconds'))], '', '<?php
$inizio = hrtime(true);
calcola();
$ms = (hrtime(true) - $inizio) / 1e6;'), [
            dBug(pBug(), ['$corsi = $pdo->query("SELECT * FROM corsi")->fetchAll();', 'foreach ($corsi as $c) {', '    $n = $pdo->query("SELECT COUNT(*) FROM lezioni WHERE corso_id = " . (int) $c["id"])->fetchColumn();', '}'], 2, '    $n = $conteggi[$c["id"]] ?? 0; // da una sola query con GROUP BY', L('Una query per ogni corso: con 1000 corsi sono 1001 query. Meglio contarle tutte con una sola query e `GROUP BY`.', 'One query per course: with 1000 courses that is 1001 queries. Better to count them all with one query and `GROUP BY`.')),
            dMatch(pMatch(), [['OPcache', L('Bytecode in memoria', 'Bytecode in memory')], ['APCu', L('Cache nella memoria del server', 'Cache in server memory')], ['hrtime()', L('Misura il tempo', 'Measures time')], ['JIT', L('Compila in codice macchina', 'Compiles to machine code')], ['N+1', L('Troppe query', 'Too many queries')]], L('Gli attrezzi delle prestazioni.', 'The performance tools.')),
        ]),
    ],
];
