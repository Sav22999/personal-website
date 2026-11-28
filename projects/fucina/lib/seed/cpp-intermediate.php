<?php
/** C++ intermediate: move semantics, templates and concepts, ranges, operator overloading, vocabulary types, concurrency, CMake. */

return [
    'id' => 'cpp-intermediate', 'family' => 'cpp', 'after' => 'cpp-base',
    'title' => L('C++ intermedio', 'Intermediate C++'), 'short' => L('C++ intermedio', 'C++ Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il C++ moderno in profondità: spostamenti e regola del cinque, template e concept, ranges, operatori, optional e string_view, thread e progetti con CMake.', 'Modern C++ in depth: moves and the rule of five, templates and concepts, ranges, operators, optional and string_view, threads and projects with CMake.'),
    'lessons' => [
        lesson('move-semantics', L('Copie, spostamenti e regola del cinque', 'Copies, moves and the rule of five'), [
            P('Copiare un oggetto grande (un vector di un milione di elementi) costa. Lo **spostamento** invece “ruba” le risorse di un oggetto che non serve più: il vector nuovo prende il buffer del vecchio, che resta vuoto ma valido. `std::move` dice esplicitamente: “questo puoi spostarlo”.', 'Copying a large object (a vector of a million items) is expensive. A **move** instead “steals” the resources of an object no longer needed: the new vector takes the old one’s buffer, and the old one stays empty but valid. `std::move` says explicitly: “you may move this”.'),
            C('#include <string>
#include <utility>
#include <vector>

std::vector<std::string> crea() {
    std::vector<std::string> v(1\'000\'000, "ciao");
    return v;                        // spostato (o costruito sul posto), non copiato
}

int main() {
    std::vector<std::string> a = crea();
    std::vector<std::string> b = a;              // copia: un milione di stringhe
    std::vector<std::string> c = std::move(a);   // spostamento: a resta vuoto
    std::string nome = "Ada";
    std::vector<std::string> nomi;
    nomi.push_back(std::move(nome));             // da qui non contare sul valore di nome
}', 'cpp'),
            P('La **regola dello zero**: se una classe usa solo membri che si gestiscono da soli (`std::string`, `std::vector`, smart pointer), non scrivere né distruttore né copie né spostamenti, perché quelli generati vanno bene. Se invece gestisci a mano una risorsa, la **regola del cinque** chiede di definire insieme distruttore, costruttore e assegnamento di copia, costruttore e assegnamento di spostamento.', 'The **rule of zero**: if a class only uses members that manage themselves (`std::string`, `std::vector`, smart pointers), write no destructor, copies or moves, because the generated ones are fine. If instead you manage a resource by hand, the **rule of five** asks you to define together the destructor, copy constructor and copy assignment, move constructor and move assignment.'),
        ], [
            Q('mv1', L('Che cosa fa davvero `std::move(a)`?', 'What does `std::move(a)` really do?'), [L('Sposta subito la memoria di a', 'Moves a’s memory at once'), L('Tratta a come un valore temporaneo, così può essere spostato', 'Treats a as a temporary value, so it can be moved'), L('Cancella a', 'Deletes a'), L('Copia a più in fretta', 'Copies a faster')], 1, L('Lo spostamento vero lo fa il costruttore o l’assegnamento che riceve il risultato.', 'The actual move is done by the constructor or assignment receiving the result.')),
            Q('mv2', L('In che stato resta un `std::vector` dopo essere stato spostato?', 'What state is a `std::vector` in after being moved from?'), [L('Valido ma non specificato, di solito vuoto', 'Valid but unspecified, usually empty'), L('Distrutto: usarlo non compila', 'Destroyed: using it does not compile'), L('Identico a prima', 'The same as before'), L('Pieno di zeri', 'Full of zeros')], 0, L('Puoi riassegnarlo o svuotarlo e riusarlo.', 'You can reassign or clear it and reuse it.')),
            Q('mv3', L('Che cosa dice la regola dello zero?', 'What does the rule of zero say?'), [L('Una classe deve avere zero metodi', 'A class must have zero methods'), L('Inizializza tutto a zero', 'Initialise everything to zero'), L('Non usare mai std::move', 'Never use std::move'), L('Se i membri si gestiscono da soli, non scrivere distruttore, copie e spostamenti', 'If the members manage themselves, write no destructor, copies or moves')], 3, L('Meno codice, meno errori.', 'Less code, fewer bugs.')),
        ], task('cpp', L('Aggiungi `testo` al vettore spostandolo: `righe.push_back(std::move(testo));`.', 'Add `testo` to the vector by moving it: `righe.push_back(std::move(testo));`.'), 'std::vector<std::string> righe;
std::string testo = "una riga lunga";
righe.push_back(testo);
', [re('righe\.(push|emplace)_back\s*\(\s*std::move\s*\(\s*testo\s*\)\s*\)\s*;', L('Sposti il testo nel vettore', 'You move the text into the vector'))], '', 'righe.push_back(std::move(testo));'), [
            dMatch(pMatch(), [['T(const T&)', L('Costruttore di copia', 'Copy constructor')], ['T(T&&)', L('Costruttore di spostamento', 'Move constructor')], ['std::move', L('Permette lo spostamento', 'Allows the move')], [L('Regola dello zero', 'Rule of zero'), L('Nessuna funzione speciale scritta a mano', 'No hand-written special functions')], [L('Regola del cinque', 'Rule of five'), L('Tutte e cinque insieme', 'All five together')]], L('Copie e spostamenti.', 'Copies and moves.')),
            dFlash(pFlash(), [[L('Uno spostamento costa di solito molto meno di una copia', 'A move usually costs far less than a copy'), true], [L('`std::move` sposta i dati da solo', '`std::move` moves the data by itself'), false], [L('Restituire un vector locale per valore non lo copia', 'Returning a local vector by value does not copy it'), true], [L('Dopo uno spostamento si può riassegnare l’oggetto', 'After a move the object can be reassigned'), true]], L('`std::move` è solo un cast.', '`std::move` is only a cast.')),
        ]),
        lesson('templates-concepts', L('Template e concept', 'Templates and concepts'), [
            P('I template scrivono codice per più tipi; i **concept** del C++20 dicono quali requisiti deve avere il tipo. Così gli errori diventano chiari (“double non soddisfa integral”) invece di pagine di messaggi incomprensibili.', 'Templates write code for many types; C++20 **concepts** state what the type must provide. That makes errors clear (“double does not satisfy integral”) instead of pages of baffling messages.'),
            C('#include <concepts>
#include <iostream>
#include <string>
#include <vector>

template <std::integral T>
T somma_fino(T n) {
    T s = 0;
    for (T i = 1; i <= n; ++i) s += i;
    return s;
}

template <typename T>
concept HaArea = requires(const T& f) {
    { f.area() } -> std::convertible_to<double>;
};

template <HaArea F>
void stampa(const F& f) { std::cout << f.area() << "\n"; }

struct Quadrato {
    double lato;
    double area() const { return lato * lato; }
};

template <typename T>
class Pila {
public:
    void push(const T& v) { dati_.push_back(v); }
    T pop() { T v = dati_.back(); dati_.pop_back(); return v; }
    bool vuota() const { return dati_.empty(); }
private:
    std::vector<T> dati_;
};

int main() {
    std::cout << somma_fino(10) << "\n";   // 55
    stampa(Quadrato{3});                  // 9
    Pila<std::string> p;
    p.push("ciao");
    // somma_fino(2.5);  // errore chiaro: double non è integral
}', 'cpp'),
            P('`template <std::integral T>` accetta solo tipi interi; con `requires` scrivi un concept tuo, descrivendo che cosa il tipo deve saper fare. La forma breve `void stampa(const HaArea auto& f)` è equivalente. Il codice dei template sta di solito negli header, perché il compilatore deve vederlo per generare ogni versione.', '`template <std::integral T>` accepts only integer types; with `requires` you write your own concept, describing what the type must be able to do. The short form `void stampa(const HaArea auto& f)` is equivalent. Template code usually lives in headers, because the compiler must see it to generate each version.'),
        ], [
            Q('tc1', L('Che cosa succede chiamando `somma_fino(2.5)`?', 'What happens when calling `somma_fino(2.5)`?'), [L('Restituisce 3.5', 'It returns 3.5'), L('Arrotonda a 2', 'It rounds to 2'), L('Errore di compilazione: double non soddisfa std::integral', 'Compile error: double does not satisfy std::integral'), L('Errore a runtime', 'Runtime error')], 2, L('Il concept blocca il tipo prima di generare il codice.', 'The concept rejects the type before generating code.')),
            Q('tc2', L('A che cosa serve un concept?', 'What is a concept for?'), [L('A descrivere i requisiti di un tipo nei template', 'Describing a type’s requirements in templates'), L('A creare classi astratte', 'Creating abstract classes'), L('A gestire la memoria', 'Managing memory'), L('A velocizzare il programma', 'Speeding up the program')], 0, L('È un controllo in compilazione.', 'It is a compile-time check.')),
            Q('tc3', L('Perché il codice dei template sta di solito negli header?', 'Why does template code usually live in headers?'), [L('Solo per abitudine', 'Only out of habit'), L('Perché il compilatore deve vederlo per generare ogni versione', 'Because the compiler must see it to generate each version'), L('Perché i file .cpp non accettano template', 'Because .cpp files do not accept templates'), L('Per nasconderlo agli altri file', 'To hide it from other files')], 1, L('Ogni file che lo usa genera le versioni che gli servono.', 'Each file using it generates the versions it needs.')),
        ], task('cpp', L('Scrivi `template <std::floating_point T> T meta(T x) { return x / 2; }`.', 'Write `template <std::floating_point T> T meta(T x) { return x / 2; }`.'), '', [re('template\s*<\s*std::floating_point\s+T\s*>', L('Il template accetta solo decimali', 'The template accepts only floating-point types')), re('T\s+meta\s*\(\s*T\s+x\s*\)\s*\{\s*return\s+x\s*/\s*2(\.0)?\s*;\s*\}', L('La funzione dimezza x', 'The function halves x'))], '', 'template <std::floating_point T>
T meta(T x) { return x / 2; }'), [
            dBlanks(pFill(), "template <typename T>\n___ Confrontabile = requires(T a, T b) {\n    { a < b } -> std::convertible_to<bool>;\n};\ntemplate <___ T>\nT minimo(T a, T b) { return b < a ? b : a; }", ['concept', 'Confrontabile'], ['class', 'interface', 'requires'], L('Definisci il concept, poi usalo come vincolo.', 'Define the concept, then use it as a constraint.')),
            dMatch(pMatch(), [['std::integral', L('Solo tipi interi', 'Integer types only')], ['std::floating_point', L('Solo decimali', 'Floating-point only')], ['requires', L('Elenca i requisiti', 'Lists the requirements')], ['concept', L('Nome per un insieme di requisiti', 'Name for a set of requirements')], ['template <typename T>', L('Qualsiasi tipo', 'Any type')]], L('Vincoli sui tipi.', 'Type constraints.')),
        ]),
        lesson('ranges', L('Ranges e views', 'Ranges and views'), [
            P('Il C++20 aggiunge i **ranges**: gli algoritmi accettano direttamente il contenitore (`std::ranges::sort(v)`) invece della coppia `begin`/`end`. Le **views** filtrano e trasformano in modo pigro, senza creare copie, e si concatenano con l’operatore `|`.', 'C++20 adds **ranges**: algorithms take the container directly (`std::ranges::sort(v)`) instead of the `begin`/`end` pair. **Views** filter and transform lazily, without making copies, and are chained with the `|` operator.'),
            C('#include <algorithm>
#include <iostream>
#include <ranges>
#include <vector>

int main() {
    std::vector<int> voti{7, 4, 9, 6, 10, 5};
    std::ranges::sort(voti);

    auto doppi_sufficienti = voti
        | std::views::filter([](int v) { return v >= 6; })
        | std::views::transform([](int v) { return v * 2; });

    for (int x : doppi_sufficienti) std::cout << x << " ";   // 12 14 18 20

    for (int i : std::views::iota(1, 4)) std::cout << i;      // 123

    auto it = std::ranges::find(voti, 9);
    if (it != voti.end()) std::cout << "\ntrovato\n";
}', 'cpp'),
            P('Una view non contiene dati: li calcola mentre la scorri, quindi il contenitore originale deve vivere più a lungo della view. Per ottenere un vettore vero dal risultato il C++23 offre `std::ranges::to<std::vector>()`. `std::views::take`, `drop`, `reverse` e `iota` completano la cassetta.', 'A view holds no data: it computes it while you walk it, so the original container must outlive the view. To get a real vector from the result C++23 offers `std::ranges::to<std::vector>()`. `std::views::take`, `drop`, `reverse` and `iota` complete the toolbox.'),
        ], [
            Q('rg1', L('Che cosa fa l’operatore `|` tra views?', 'What does the `|` operator do between views?'), [L('Calcola un OR tra i bit', 'Computes a bitwise OR'), L('Concatena le trasformazioni in una pipeline', 'Chains the transformations into a pipeline'), L('Unisce due vettori', 'Joins two vectors'), L('Ordina il range', 'Sorts the range')], 1, L('Si legge da sinistra a destra.', 'It reads left to right.')),
            Q('rg2', L('Quando calcola i valori una view come `views::transform`?', 'When does a view such as `views::transform` compute its values?'), [L('Mentre la scorri, uno alla volta', 'While you walk it, one at a time'), L('Appena la crei, tutti insieme', 'As soon as it is created, all at once'), L('Mai: va convertita prima', 'Never: it must be converted first'), L('Solo in modalità release', 'Only in release mode')], 0, L('È pigra.', 'It is lazy.')),
            Q('rg3', L('Come ordini `v` con i ranges?', 'How do you sort `v` with ranges?'), ['v.sort()', 'std::sort(v)', 'std::views::sort(v)', 'std::ranges::sort(v)'], 3, L('`std::sort` vuole ancora `begin` ed `end`.', '`std::sort` still wants `begin` and `end`.')),
        ], task('cpp', L('Da `numeri` crea la view `auto pari = numeri | std::views::filter([](int n) { return n % 2 == 0; });`.', 'From `numeri` create the view `auto pari = numeri | std::views::filter([](int n) { return n % 2 == 0; });`.'), 'std::vector<int> numeri{1, 2, 3, 4};
', [re('auto\s+pari\s*=\s*numeri\s*\|\s*std::views::filter\s*\(', L('Filtri con una view', 'You filter with a view')), re('(\w+)\s*%\s*2\s*==\s*0', L('Tieni i pari', 'You keep the even numbers'))], '', 'auto pari = numeri | std::views::filter([](int n) { return n % 2 == 0; });'), [
            dOrder(pSort(), ['auto risultato = parole', '    | std::views::filter([](const std::string& p) { return p.size() > 3; })', '    | std::views::transform([](const std::string& p) { return p.size(); })', '    | std::views::take(2);'], L('Sorgente, filtro, trasformazione, limite.', 'Source, filter, transform, limit.')),
            dMatch(pMatch(), [['std::ranges::sort(v)', L('Ordina tutto il contenitore', 'Sorts the whole container')], ['views::filter', L('Tiene solo alcuni elementi', 'Keeps only some items')], ['views::transform', L('Trasforma ogni elemento', 'Transforms each item')], ['views::iota(1, 5)', L('Numeri da 1 a 4', 'Numbers from 1 to 4')], ['ranges::to', L('Raccoglie in un contenitore (C++23)', 'Collects into a container (C++23)')]], L('Gli attrezzi dei ranges.', 'Range tools.')),
        ]),
        lesson('operators', L('Overloading degli operatori', 'Operator overloading'), [
            P('Puoi definire che cosa fanno `+`, `==`, `<<` e gli altri operatori per i tuoi tipi. Usalo quando il significato è ovvio (somma di vettori, confronto di date), mai per sorprendere chi legge.', 'You can define what `+`, `==`, `<<` and the other operators do for your types. Use it when the meaning is obvious (adding vectors, comparing dates), never to surprise the reader.'),
            C('#include <compare>
#include <iostream>

struct Vettore {
    double x = 0, y = 0;

    Vettore operator+(const Vettore& o) const { return {x + o.x, y + o.y}; }
    Vettore& operator+=(const Vettore& o) { x += o.x; y += o.y; return *this; }
    bool operator==(const Vettore&) const = default;
};

struct Data {
    int anno, mese, giorno;
    auto operator<=>(const Data&) const = default;   // genera <, >, <=, >=, ==, !=
};

std::ostream& operator<<(std::ostream& os, const Vettore& v) {
    return os << "(" << v.x << ", " << v.y << ")";
}

int main() {
    Vettore a{1, 2}, b{3, 4};
    std::cout << a + b << "\n";                                     // (4, 6)
    std::cout << (Data{2026, 3, 1} < Data{2026, 11, 17}) << "\n";   // 1
}', 'cpp'),
            P('L’operatore **astronave** `<=>` del C++20, se messo `= default`, genera tutti i confronti confrontando i membri in ordine. `operator<<` si scrive come funzione libera e restituisce lo stream, così le stampe si concatenano. `+=` modifica e restituisce `*this`; `+` restituisce un oggetto nuovo.', 'The C++20 **spaceship** operator `<=>`, when set to `= default`, generates every comparison by comparing members in order. `operator<<` is written as a free function and returns the stream, so prints can be chained. `+=` modifies and returns `*this`; `+` returns a new object.'),
        ], [
            Q('op1', L('Che cosa genera `auto operator<=>(const Data&) const = default;`?', 'What does `auto operator<=>(const Data&) const = default;` generate?'), [L('Solo l’operatore <=>', 'Only the <=> operator'), L('Una copia della classe', 'A copy of the class'), L('Tutti gli operatori di confronto, membro per membro', 'Every comparison operator, member by member'), L('Un costruttore', 'A constructor')], 2, L('L’ordine dei membri decide il confronto.', 'The order of the members decides the comparison.')),
            Q('op2', L('Perché `operator<<` restituisce `std::ostream&`?', 'Why does `operator<<` return `std::ostream&`?'), [L('Per poter concatenare più `<<` di seguito', 'To chain several `<<` in a row'), L('Per velocità', 'For speed'), L('Perché è obbligatorio per tutti gli operatori', 'Because every operator must'), L('Per chiudere lo stream', 'To close the stream')], 0, L('`std::cout << a << b` usa il risultato del primo `<<`.', '`std::cout << a << b` uses the result of the first `<<`.')),
            Q('op3', L('Che cosa restituisce di solito `operator+=`?', 'What does `operator+=` usually return?'), ['void', L('Un oggetto nuovo', 'A new object'), 'bool', L('Un riferimento all’oggetto stesso, *this', 'A reference to the object itself, *this')], 3, L('Come fanno i tipi predefiniti.', 'As built-in types do.')),
        ], task('cpp', L('Aggiungi a `Punto` il confronto di default: `bool operator==(const Punto&) const = default;`.', 'Add the default comparison to `Punto`: `bool operator==(const Punto&) const = default;`.'), 'struct Punto {
    int x, y;
};', [re('bool\s+operator\s*==\s*\(\s*const\s+Punto\s*&\s*\w*\s*\)\s*const\s*=\s*default\s*;', L('Il confronto è generato', 'The comparison is generated'))], '', 'struct Punto {
    int x, y;
    bool operator==(const Punto&) const = default;
};'), [
            dBlanks(pFill(), "struct Euro {\n    long centesimi;\n    Euro ___+(const Euro& o) const { return {centesimi + o.centesimi}; }\n    auto operator___(const Euro&) const = default;\n};", ['operator', '<=>'], ['func', '=>', 'compare'], L('Somma e confronti generati.', 'Addition and generated comparisons.')),
            dFlash(pFlash(), [[L('`<=>` è arrivato con il C++20', '`<=>` arrived with C++20'), true], [L('Si può ridefinire il significato di + per gli int', 'You can redefine what + means for ints'), false], [L('`operator<<` per la stampa si scrive di solito fuori dalla classe', '`operator<<` for printing is usually written outside the class'), true], [L('Un operatore dal significato sorprendente rende il codice più chiaro', 'An operator with a surprising meaning makes code clearer'), false]], L('Gli operatori si ridefiniscono solo per i tuoi tipi.', 'Operators can only be redefined for your own types.')),
        ]),
        lesson('vocabulary-types', L('optional, variant, string_view e span', 'optional, variant, string_view and span'), [
            P('La libreria standard offre tipi che dicono chiaramente che cosa contengono. `std::optional<T>`: un valore che può mancare. `std::variant<A, B>`: uno tra più tipi. `std::string_view` e `std::span<T>`: una **vista** su testo o elementi che appartengono a qualcun altro, senza copia. `std::expected<T, E>` (C++23): un risultato oppure un errore.', 'The standard library offers types that say clearly what they hold. `std::optional<T>`: a value that may be missing. `std::variant<A, B>`: one of several types. `std::string_view` and `std::span<T>`: a **view** on text or items owned by someone else, without copying. `std::expected<T, E>` (C++23): a result or an error.'),
            C('#include <expected>
#include <iostream>
#include <optional>
#include <span>
#include <string>
#include <string_view>
#include <variant>

std::optional<int> cerca(std::span<const int> v, int x) {
    for (std::size_t i = 0; i < v.size(); ++i)
        if (v[i] == x) return static_cast<int>(i);
    return std::nullopt;
}

std::expected<int, std::string> eta(std::string_view testo) {
    if (testo.empty()) return std::unexpected("testo vuoto");
    return std::stoi(std::string(testo));
}

int main() {
    int voti[] = {7, 9, 6};
    if (auto pos = cerca(voti, 9)) std::cout << "posizione " << *pos << "\n";

    std::variant<int, std::string> valore = "ciao";
    if (std::holds_alternative<std::string>(valore)) std::cout << std::get<std::string>(valore) << "\n";

    auto r = eta("36");
    std::cout << r.value_or(-1) << "\n";
}', 'cpp'),
            P('Una vista non possiede i dati: non restituire mai una `string_view` su una stringa locale, perché sparirebbe con la funzione. Per i parametri di sola lettura `std::string_view` è comodo: accetta letterali, `std::string` e pezzi di testo senza copiarli.', 'A view does not own the data: never return a `string_view` on a local string, because it would vanish with the function. For read-only parameters `std::string_view` is handy: it accepts literals, `std::string` and pieces of text without copying them.'),
        ], [
            Q('vt1', L('Quale tipo rappresenta un valore che può mancare?', 'Which type represents a value that may be missing?'), ['std::variant', 'std::span', 'std::optional', 'std::string_view'], 2, L('Vuoto si scrive `std::nullopt`.', 'Empty is written `std::nullopt`.')),
            Q('vt2', L('Che cosa è pericoloso con `std::string_view`?', 'What is dangerous with `std::string_view`?'), [L('Usarla dopo che la stringa a cui si riferisce è stata distrutta', 'Using it after the string it refers to has been destroyed'), L('Passarla per valore', 'Passing it by value'), L('Leggerne la lunghezza', 'Reading its length'), L('Confrontarla con ==', 'Comparing it with ==')], 0, L('È solo una vista: punta a memoria di altri.', 'It is only a view: it points at someone else’s memory.')),
            Q('vt3', L('Che cosa contiene uno `std::expected<int, std::string>`?', 'What does a `std::expected<int, std::string>` hold?'), [L('Sempre entrambi', 'Always both'), L('Un int e un elenco di errori', 'An int and a list of errors'), L('Niente fino al C++26', 'Nothing until C++26'), L('Un int oppure un messaggio di errore', 'An int or an error message')], 3, L('Si controlla con `has_value()` o `value_or`.', 'You check it with `has_value()` or `value_or`.')),
        ], task('cpp', L('Scrivi `std::optional<double> radice(double x)` che restituisce `std::nullopt` se `x < 0`, altrimenti `std::sqrt(x)`.', 'Write `std::optional<double> radice(double x)` that returns `std::nullopt` if `x < 0`, otherwise `std::sqrt(x)`.'), '', [re('std::optional\s*<\s*double\s*>\s+radice\s*\(\s*double\s+x\s*\)', L('La firma è giusta', 'The signature is right')), re('std::nullopt', L('C’è il caso vuoto', 'There is the empty case')), re('std::sqrt\s*\(\s*x\s*\)', L('Calcoli la radice', 'You compute the root'))], '', 'std::optional<double> radice(double x) {
    if (x < 0) return std::nullopt;
    return std::sqrt(x);
}'), [
            dMatch(pMatch(), [['std::optional', L('Valore che può mancare', 'Value that may be missing')], ['std::variant', L('Uno tra più tipi', 'One of several types')], ['std::string_view', L('Vista su un testo', 'View on a text')], ['std::span', L('Vista su elementi contigui', 'View on contiguous items')], ['std::expected', L('Risultato oppure errore', 'Result or error')]], L('Tipi che parlano chiaro.', 'Types that speak clearly.')),
            dFlash(pFlash(), [[L('`string_view` non copia il testo', '`string_view` does not copy the text'), true], [L('`optional` usa sempre memoria dinamica', '`optional` always uses dynamic memory'), false], [L('`span` funziona con array C e vector', '`span` works with C arrays and vectors'), true], [L('`std::nullopt` indica un optional vuoto', '`std::nullopt` marks an empty optional'), true]], L('`optional` tiene il valore al suo interno.', '`optional` keeps the value inside itself.')),
        ]),
        lesson('concurrency', L('Thread e concorrenza', 'Threads and concurrency'), [
            P('`std::jthread` (C++20) avvia un thread e, a differenza del vecchio `std::thread`, lo aspetta da solo quando viene distrutto. Quando più thread toccano gli stessi dati serve un `std::mutex`, bloccato con `std::scoped_lock` (RAII: si sblocca da solo). Per un contatore semplice basta `std::atomic`.', '`std::jthread` (C++20) starts a thread and, unlike the old `std::thread`, waits for it by itself when destroyed. When several threads touch the same data you need a `std::mutex`, locked with `std::scoped_lock` (RAII: it unlocks by itself). For a simple counter `std::atomic` is enough.'),
            C('#include <atomic>
#include <future>
#include <iostream>
#include <mutex>
#include <thread>
#include <vector>

int main() {
    std::atomic<int> contatore = 0;
    std::mutex m;
    std::vector<int> risultati;

    {
        std::vector<std::jthread> lavoratori;
        for (int id = 0; id < 4; ++id) {
            lavoratori.emplace_back([&, id] {
                contatore += 1;                     // atomico: niente data race
                std::scoped_lock lock(m);           // protegge il vector
                risultati.push_back(id * id);
            });
        }
    }   // qui i jthread vengono aspettati

    auto futuro = std::async(std::launch::async, [] { return 6 * 7; });
    std::cout << contatore << " " << risultati.size() << " " << futuro.get() << "\n";   // 4 4 42
}', 'cpp'),
            P('Una **data race** (due thread che toccano gli stessi dati, almeno uno scrivendo, senza protezione) è comportamento indefinito. `std::async` esegue una funzione e restituisce un `std::future`, da cui `get()` legge il risultato aspettando se serve. Su alcuni sistemi si compila con `-pthread`; prova il codice con `-fsanitize=thread`.', 'A **data race** (two threads touching the same data, at least one writing, without protection) is undefined behaviour. `std::async` runs a function and returns a `std::future`, from which `get()` reads the result, waiting if needed. On some systems you compile with `-pthread`; test the code with `-fsanitize=thread`.'),
        ], [
            Q('cc1', L('Che differenza c’è tra `std::jthread` e `std::thread`?', 'What is the difference between `std::jthread` and `std::thread`?'), [L('jthread è più veloce', 'jthread is faster'), L('jthread viene aspettato da solo quando è distrutto', 'jthread is waited for automatically when destroyed'), L('jthread non accetta lambda', 'jthread does not accept lambdas'), L('Nessuna', 'None')], 1, L('Un `std::thread` non unito alla distruzione termina il programma.', 'A `std::thread` not joined at destruction terminates the program.')),
            Q('cc2', L('Che cosa fa `std::scoped_lock lock(m);`?', 'What does `std::scoped_lock lock(m);` do?'), [L('Blocca il mutex e lo sblocca alla fine del blocco', 'Locks the mutex and unlocks it at the end of the block'), L('Crea un nuovo thread', 'Creates a new thread'), L('Distrugge il mutex', 'Destroys the mutex'), L('Blocca il mutex per sempre', 'Locks the mutex forever')], 0, L('È RAII applicato ai lock.', 'It is RAII applied to locks.')),
            Q('cc3', L('Che cosa restituisce `std::async`?', 'What does `std::async` return?'), [L('Il risultato, subito', 'The result, at once'), L('Un thread', 'A thread'), L('Un mutex', 'A mutex'), L('Un std::future da cui leggere il risultato', 'A std::future to read the result from')], 3, L('`get()` aspetta se il risultato non è pronto.', '`get()` waits if the result is not ready.')),
        ], task('cpp', L('Proteggi l’aggiunta: prima di `lista.push_back(x);` scrivi `std::scoped_lock lock(m);`.', 'Protect the insertion: before `lista.push_back(x);` write `std::scoped_lock lock(m);`.'), 'void aggiungi(int x) {
    lista.push_back(x);
}', [re('std::(scoped_lock|lock_guard)\s*(<\s*std::mutex\s*>)?\s+\w+\s*[({]\s*m\s*[)}]\s*;[\s\S]*lista\.push_back', L('Blocchi il mutex prima di modificare', 'You lock the mutex before changing'))], '', 'void aggiungi(int x) {
    std::scoped_lock lock(m);
    lista.push_back(x);
}'), [
            dMatch(pMatch(), [['std::jthread', L('Thread aspettato da solo', 'Self-joining thread')], ['std::mutex', L('Accesso uno alla volta', 'One-at-a-time access')], ['std::scoped_lock', L('Blocco RAII su un mutex', 'RAII lock on a mutex')], ['std::atomic', L('Variabile sicura tra thread', 'Thread-safe variable')], ['std::future', L('Risultato che arriverà', 'Result to come')]], L('Gli attrezzi della concorrenza.', 'Concurrency tools.')),
            dFlash(pFlash(), [[L('Una data race è comportamento indefinito', 'A data race is undefined behaviour'), true], [L('`+= 1` su uno `std::atomic<int>` è sicuro tra thread', '`+= 1` on a `std::atomic<int>` is thread-safe'), true], [L('`scoped_lock` va sbloccato a mano', '`scoped_lock` must be unlocked by hand'), false], [L('`-fsanitize=thread` aiuta a trovare le data race', '`-fsanitize=thread` helps find data races'), true]], L('Si sblocca da solo alla fine del blocco.', 'It unlocks by itself at the end of the block.')),
        ]),
        lesson('cmake', L('Progetti con CMake', 'Projects with CMake'), [
            P('Per i progetti C++ lo strumento più diffuso è **CMake**: descrivi il progetto in `CMakeLists.txt` e CMake genera i file per Make, Ninja o Visual Studio. Lo stesso file funziona su Linux, macOS e Windows.', 'For C++ projects the most widespread tool is **CMake**: you describe the project in `CMakeLists.txt` and CMake generates the files for Make, Ninja or Visual Studio. The same file works on Linux, macOS and Windows.'),
            C('cmake_minimum_required(VERSION 3.20)
project(Fucina LANGUAGES CXX)

set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

add_library(geometria src/geometria.cpp)
target_include_directories(geometria PUBLIC include)

add_executable(app src/main.cpp)
target_link_libraries(app PRIVATE geometria)', 'text'),
            C('cmake -S . -B build -DCMAKE_BUILD_TYPE=Debug
cmake --build build
./build/app', 'bash'),
            P('I comandi `target_…` legano opzioni e dipendenze a un singolo bersaglio, e `PUBLIC` le passa anche a chi lo usa. La cartella `build` resta separata dai sorgenti. Per le librerie esterne ci sono `find_package` e gestori come vcpkg e Conan. I **moduli** del C++20 (`import`) stanno arrivando, ma il supporto di compilatori e strumenti è ancora disomogeneo.', 'The `target_…` commands attach options and dependencies to a single target, and `PUBLIC` passes them on to its users too. The `build` folder stays separate from the sources. For external libraries there are `find_package` and managers such as vcpkg and Conan. C++20 **modules** (`import`) are on their way, but compiler and tool support is still uneven.'),
        ], [
            Q('cm1', L('Quale file descrive un progetto CMake?', 'Which file describes a CMake project?'), ['Makefile', 'CMakeLists.txt', 'meson.build', 'project.json'], 1, L('Uno nella radice, e altri nelle sottocartelle se serve.', 'One at the root, and others in subfolders if needed.')),
            Q('cm2', L('Che cosa fa `target_link_libraries(app PRIVATE geometria)`?', 'What does `target_link_libraries(app PRIVATE geometria)` do?'), [L('Collega la libreria geometria all’eseguibile app', 'Links the geometria library to the app executable'), L('Scarica geometria da internet', 'Downloads geometria from the internet'), L('Rende privato il codice di app', 'Makes app’s code private'), L('Compila soltanto geometria', 'Builds only geometria')], 0, L('`PRIVATE`: serve ad app ma non a chi usa app.', '`PRIVATE`: app needs it but app’s users do not.')),
            Q('cm3', L('Che cosa fa `cmake -S . -B build`?', 'What does `cmake -S . -B build` do?'), [L('Compila ed esegue il programma', 'Builds and runs the program'), L('Cancella la cartella build', 'Deletes the build folder'), L('Installa CMake', 'Installs CMake'), L('Configura il progetto e genera i file di compilazione in build', 'Configures the project and generates the build files in build')], 3, L('Poi si compila con `cmake --build build`.', 'Then you build with `cmake --build build`.')),
        ], task('text', L('Nel `CMakeLists.txt` imposta lo standard C++20 con `set(CMAKE_CXX_STANDARD 20)`.', 'In `CMakeLists.txt` set the C++20 standard with `set(CMAKE_CXX_STANDARD 20)`.'), 'cmake_minimum_required(VERSION 3.20)
project(Gioco LANGUAGES CXX)
add_executable(gioco main.cpp)
', [re('set\s*\(\s*CMAKE_CXX_STANDARD\s+(20|23)\s*\)', L('Imposti lo standard', 'You set the standard'))], '', 'cmake_minimum_required(VERSION 3.20)
project(Gioco LANGUAGES CXX)
set(CMAKE_CXX_STANDARD 20)
add_executable(gioco main.cpp)'), [
            dOrder(pSort(), ['cmake_minimum_required(VERSION 3.20)', 'project(Demo LANGUAGES CXX)', 'add_executable(demo main.cpp)', 'target_compile_features(demo PRIVATE cxx_std_20)'], L('Versione minima, progetto, bersaglio, opzioni del bersaglio.', 'Minimum version, project, target, target options.')),
            dMatch(pMatch(), [['add_executable', L('Crea un programma', 'Creates a program')], ['add_library', L('Crea una libreria', 'Creates a library')], ['target_link_libraries', L('Collega una libreria', 'Links a library')], ['find_package', L('Trova una libreria installata', 'Finds an installed library')], ['cmake --build build', L('Compila', 'Builds')]], L('I comandi di CMake.', 'CMake commands.')),
        ]),
    ],
];
