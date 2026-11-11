<?php
/** C++ basics: from C to C++, the standard library containers, classes and RAII, smart pointers and modern C++. */

return [
    'id' => 'cpp-base', 'family' => 'cpp', 'after' => null,
    'title' => L('C++ base', 'C++ Basics'), 'short' => L('C++ base', 'C++ Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di videogiochi, browser e software ad alte prestazioni: libreria standard, vector e string, classi e RAII, smart pointer e C++ moderno.', 'The language of video games, browsers and high-performance software: the standard library, vector and string, classes and RAII, smart pointers and modern C++.'),
    'lessons' => [
        lesson('intro', L('Da C a C++', 'From C to C++'), [
            P('**C++** nasce come estensione di C e aggiunge classi, template e una ricca **libreria standard**. Si usa dove servono prestazioni e controllo: motori di videogiochi, browser, software finanziario, sistemi embedded. Il C++ “moderno” (dal 2011 in poi) è molto più sicuro e comodo di quello di una volta.', '**C++** started as an extension of C and adds classes, templates and a rich **standard library**. It is used where performance and control matter: game engines, browsers, financial software, embedded systems. “Modern” C++ (2011 onwards) is much safer and more convenient than the old style.'),
            C('#include <iostream>
#include <string>

int main() {
    std::string nome = "Ada";
    int anni = 36;
    std::cout << nome << " ha " << anni << " anni\n";
    return 0;
}', 'cpp'),
            C('g++ -std=c++20 -Wall -Wextra -o saluto saluto.cpp
./saluto', 'bash'),
            P('`std::` è lo spazio dei nomi della libreria standard. `std::cout <<` stampa, `std::string` gestisce il testo senza preoccuparti dei terminatori e della memoria come in C.', '`std::` is the standard library’s namespace. `std::cout <<` prints, `std::string` handles text without worrying about terminators and memory as in C.'),
        ], [
            Q('i1', L('Che cosa aggiunge C++ a C?', 'What does C++ add to C?'), [L('Classi, template e una ricca libreria standard', 'Classes, templates and a rich standard library'), L('Niente', 'Nothing'), L('Solo commenti', 'Only comments'), L('Un garbage collector obbligatorio', 'A mandatory garbage collector')], 0, L('Mantenendo le prestazioni.', 'While keeping performance.')),
            Q('i2', L('Che cosa fa `std::cout << "ciao"`?', 'What does `std::cout << "ciao"` do?'), [L('Stampa ciao', 'Prints ciao'), L('Legge ciao', 'Reads ciao'), L('Cancella ciao', 'Deletes ciao'), L('Confronta', 'Compares')], 0, L('Output standard.', 'Standard output.')),
            Q('i3', L('Che cos’è `std::`?', 'What is `std::`?'), [L('Lo spazio dei nomi della libreria standard', 'The standard library namespace'), L('Un tipo di variabile', 'A variable type'), L('Un errore', 'An error'), L('Un commento', 'A comment')], 0, L('Evita conflitti di nomi.', 'It avoids name clashes.')),
        ], task('cpp', L('Includi `<iostream>` e `<string>`, crea `std::string corso = "C++";` e stampalo con `std::cout`.', 'Include `<iostream>` and `<string>`, create `std::string corso = "C++";` and print it with `std::cout`.'), '', [re('#include\s*<iostream>', L('iostream', 'iostream')), re('#include\s*<string>', L('string', 'string')), re('std::string\s+corso\s*=\s*"C\+\+"\s*;', L('La variabile', 'The variable')), re('std::cout\s*<<\s*corso', L('La stampa', 'The print'))], '', "#include <iostream>\n#include <string>\n\nint main() {\n    std::string corso = \"C++\";\n    std::cout << corso << \"\\n\";\n}"), [
            dMatch(pMatch(), [['std::cout', L('Stampa', 'Prints')], ['std::cin', L('Legge da tastiera', 'Reads from keyboard')], ['std::string', L('Testo gestito', 'Managed text')], ['g++', L('Compilatore', 'Compiler')], ['-std=c++20', L('Versione del linguaggio', 'Language version')]], L('I primi strumenti.', 'First tools.')),
            dFlash(pFlash(), [[L('C++ è usato nei motori dei videogiochi', 'C++ is used in game engines'), true], [L('std::string richiede di gestire il terminatore a mano', 'std::string needs manual terminator handling'), false], [L('Il C++ moderno è più sicuro di quello vecchio', 'Modern C++ is safer than the old style'), true], [L('C++ non può usare codice C', 'C++ cannot use C code'), false]], L('Potenza con più comodità.', 'Power with more convenience.')),
        ]),
        lesson('stl', L('Contenitori della libreria standard', 'Standard library containers'), [
            P('La libreria standard offre **contenitori** pronti: `std::vector` (array che cresce), `std::map` e `std::unordered_map` (chiave-valore), `std::set`. Gestiscono da soli la memoria. Gli **algoritmi** (`std::sort`, `std::find`…) lavorano su qualsiasi contenitore.', 'The standard library offers ready **containers**: `std::vector` (growing array), `std::map` and `std::unordered_map` (key-value), `std::set`. They manage memory by themselves. **Algorithms** (`std::sort`, `std::find`…) work on any container.'),
            C('#include <vector>
#include <map>
#include <algorithm>

std::vector<int> voti = {28, 30, 24};
voti.push_back(27);
std::sort(voti.begin(), voti.end());

for (int v : voti) std::cout << v << " ";      // 24 27 28 30

std::map<std::string, int> magazzino;
magazzino["tazze"] = 12;
magazzino["piatti"] += 5;', 'cpp'),
            P('Il ciclo `for (int v : voti)` scorre tutti gli elementi (range-based for). Preferisci `std::vector` agli array in stile C: conosce la sua dimensione (`size()`) e con `at(i)` controlla i limiti.', 'The `for (int v : voti)` loop goes through every element (range-based for). Prefer `std::vector` to C-style arrays: it knows its size (`size()`) and `at(i)` checks bounds.'),
        ], [
            Q('s1', L('Quale contenitore è un array che cresce?', 'Which container is a growing array?'), ['std::vector', 'std::map', 'std::set', 'std::pair'], 0, L('push_back aggiunge in fondo.', 'push_back appends.')),
            Q('s2', L('Che cosa fa `std::sort(v.begin(), v.end())`?', 'What does `std::sort(v.begin(), v.end())` do?'), [L('Ordina il vettore', 'Sorts the vector'), L('Cancella il vettore', 'Deletes the vector'), L('Conta gli elementi', 'Counts the elements'), L('Inverte il vettore', 'Reverses the vector')], 0, L('Algoritmo standard.', 'Standard algorithm.')),
            Q('s3', L('Perché preferire `at(i)` a `[i]` quando sei incerto?', 'Why prefer `at(i)` over `[i]` when unsure?'), [L('at controlla i limiti', 'at checks bounds'), L('È più veloce', 'It is faster'), L('Non c’è differenza', 'There is no difference'), L('[i] non esiste', '[i] does not exist')], 0, L('Lancia un’eccezione fuori dai limiti.', 'It throws outside bounds.')),
        ], task('cpp', L('Crea `std::vector<std::string> nomi`, aggiungi `"Ada"` e `"Bo"` con `push_back` e stampali con un range-based for.', 'Create `std::vector<std::string> nomi`, add `"Ada"` and `"Bo"` with `push_back` and print them with a range-based for.'), '', [re('std::vector\s*<\s*std::string\s*>\s+nomi', L('Il vettore', 'The vector')), re('nomi\.push_back\s*\(\s*"Ada"\s*\)[\s\S]*nomi\.push_back\s*\(\s*"Bo"\s*\)', L('Aggiungi i nomi', 'You add the names')), re('for\s*\(\s*(const\s+)?(auto|std::string)\s*&?\s*\w+\s*:\s*nomi\s*\)', L('Il ciclo', 'The loop'))], '', "std::vector<std::string> nomi;\nnomi.push_back(\"Ada\");\nnomi.push_back(\"Bo\");\nfor (const auto& n : nomi) std::cout << n << \"\\n\";"), [
            dMatch(pMatch(), [['vector', L('Array dinamico', 'Dynamic array')], ['map', L('Chiave-valore ordinato', 'Ordered key-value')], ['unordered_map', L('Chiave-valore veloce', 'Fast key-value')], ['set', L('Valori unici', 'Unique values')], ['std::sort', L('Ordina', 'Sorts')]], L('La cassetta della libreria standard.', 'The standard library toolbox.')),
            dBlanks(pFill(), "std::vector<int> v = {3, 1, 2};\nv.___(4);\nstd::___(v.begin(), v.end());\nstd::cout << v.___();", ['push_back', 'sort', 'size'], ['add', 'order', 'length'], L('Aggiungi, ordina, conta.', 'Add, sort, count.')),
        ]),
        lesson('classes', L('Classi e RAII', 'Classes and RAII'), [
            P('Una **classe** C++ ha membri `private` (dati nascosti) e `public` (interfaccia). Il **costruttore** inizializza l’oggetto, il **distruttore** (`~Nome`) viene chiamato automaticamente quando l’oggetto esce dallo scope.', 'A C++ **class** has `private` members (hidden data) and `public` ones (interface). The **constructor** initialises the object, the **destructor** (`~Name`) is called automatically when the object goes out of scope.'),
            C('class File {
public:
    explicit File(const std::string& percorso) : f_(std::fopen(percorso.c_str(), "r")) {
        if (!f_) throw std::runtime_error("Impossibile aprire " + percorso);
    }
    ~File() { std::fclose(f_); }          // chiusura automatica
    File(const File&) = delete;           // niente copie
private:
    std::FILE* f_;
};

void leggi() {
    File config("config.txt");
    // … uso del file …
}   // qui il distruttore chiude il file, anche se c’è un’eccezione', 'cpp'),
            P('Questa idea si chiama **RAII** (Resource Acquisition Is Initialization): una risorsa (file, memoria, connessione) viene acquisita nel costruttore e rilasciata nel distruttore. Così non dimentichi mai di chiuderla, nemmeno in caso di errori.', 'This idea is called **RAII** (Resource Acquisition Is Initialization): a resource (file, memory, connection) is acquired in the constructor and released in the destructor. That way you never forget to close it, even when errors happen.'),
        ], [
            Q('c1', L('Quando viene chiamato il distruttore?', 'When is the destructor called?'), [L('Quando l’oggetto esce dallo scope', 'When the object goes out of scope'), L('Mai automaticamente', 'Never automatically'), L('All’avvio del programma', 'At program start'), L('Solo con delete del file', 'Only with file deletion')], 0, L('Anche in caso di eccezione.', 'Even on exceptions.')),
            Q('c2', L('Che cosa significa RAII?', 'What does RAII mean?'), [L('La risorsa si acquisisce nel costruttore e si rilascia nel distruttore', 'The resource is acquired in the constructor and released in the destructor'), L('Un tipo di ciclo', 'A kind of loop'), L('Un compilatore', 'A compiler'), L('Una libreria grafica', 'A graphics library')], 0, L('Niente dimenticanze.', 'No forgetting.')),
            Q('c3', L('Che cosa sono i membri private?', 'What are private members?'), [L('Dati nascosti all’esterno della classe', 'Data hidden from outside the class'), L('Dati pubblici', 'Public data'), L('Commenti', 'Comments'), L('Errori', 'Errors')], 0, L('Incapsulamento.', 'Encapsulation.')),
        ], task('cpp', L('Scrivi la classe `Contatore` con un membro privato `int valore_ = 0;` e i metodi pubblici `void incrementa()` e `int valore() const`.', 'Write the class `Contatore` with a private member `int valore_ = 0;` and public methods `void incrementa()` and `int valore() const`.'), '', [re('class\s+Contatore\s*\{', L('La classe', 'The class')), re('public\s*:[\s\S]*void\s+incrementa\s*\(\s*\)[\s\S]*int\s+valore\s*\(\s*\)\s*const', L('I metodi pubblici', 'The public methods')), re('private\s*:[\s\S]*int\s+valore_\s*=\s*0\s*;', L('Il dato privato', 'The private data'))], '', "class Contatore {\npublic:\n    void incrementa() { ++valore_; }\n    int valore() const { return valore_; }\nprivate:\n    int valore_ = 0;\n};"), [
            dOrder(pSort(), ['class Timer {', 'public:', '    Timer() { avvia(); }', '    ~Timer() { ferma(); }', '};'], L('Classe con costruttore e distruttore.', 'Class with constructor and destructor.')),
            dMatch(pMatch(), [[L('Costruttore', 'Constructor'), L('Inizializza', 'Initialises')], [L('Distruttore', 'Destructor'), L('Rilascia le risorse', 'Releases resources')], ['private', L('Nascosto', 'Hidden')], ['public', L('Interfaccia', 'Interface')], ['RAII', L('Risorse legate alla vita dell’oggetto', 'Resources tied to object lifetime')]], L('Classi in C++.', 'Classes in C++.')),
        ]),
        lesson('modern', L('Smart pointer e C++ moderno', 'Smart pointers and modern C++'), [
            P('Nel C++ moderno quasi mai si usano `new` e `delete` direttamente. Gli **smart pointer** gestiscono la memoria con RAII: **`std::unique_ptr`** ha un solo proprietario e libera la memoria da solo; **`std::shared_ptr`** conta i proprietari e libera quando l’ultimo sparisce.', 'In modern C++ you almost never use `new` and `delete` directly. **Smart pointers** manage memory with RAII: **`std::unique_ptr`** has a single owner and frees memory by itself; **`std::shared_ptr`** counts owners and frees when the last one goes.'),
            C('#include <memory>

auto giocatore = std::make_unique<Giocatore>("Ada");   // proprietario unico
giocatore->salta();

auto texture = std::make_shared<Texture>("erba.png");  // condivisa
auto copia = texture;                                  // contatore = 2

auto quadrato = [](int x) { return x * x; };           // lambda
for (const auto& [nome, qta] : magazzino) { /* … */ }  // structured bindings', 'cpp'),
            P('Altre comodità moderne: `auto` per dedurre i tipi, le **lambda**, i *range-based for*, le *structured bindings*, `std::optional` per valori che possono mancare. Regola d’oro: preferisci contenitori e smart pointer a puntatori nudi.', 'Other modern conveniences: `auto` to infer types, **lambdas**, range-based for, structured bindings, `std::optional` for values that may be missing. Golden rule: prefer containers and smart pointers to raw pointers.'),
        ], [
            Q('m1', L('Quanti proprietari ha uno std::unique_ptr?', 'How many owners does a std::unique_ptr have?'), [L('Uno', 'One'), L('Due', 'Two'), L('Infiniti', 'Unlimited'), L('Nessuno', 'None')], 0, L('Unico, appunto.', 'Unique, indeed.')),
            Q('m2', L('Quando libera la memoria uno std::shared_ptr?', 'When does a std::shared_ptr free memory?'), [L('Quando sparisce l’ultimo proprietario', 'When the last owner goes'), L('Subito', 'At once'), L('Mai', 'Never'), L('A ogni copia', 'On every copy')], 0, L('Conta i riferimenti.', 'It counts references.')),
            Q('m3', L('Che cosa usare al posto di new e delete?', 'What should you use instead of new and delete?'), [L('Smart pointer e contenitori', 'Smart pointers and containers'), L('malloc e free', 'malloc and free'), L('Variabili globali', 'Global variables'), L('Niente', 'Nothing')], 0, L('RAII ovunque.', 'RAII everywhere.')),
        ], task('cpp', L('Crea con `std::make_unique` un `std::unique_ptr` chiamato `conto` a un oggetto `Conto` costruito con `"Ada"`.', 'Create with `std::make_unique` a `std::unique_ptr` named `conto` to a `Conto` object built with `"Ada"`.'), "#include <memory>\n", [re('(auto|std::unique_ptr\s*<\s*Conto\s*>)\s+conto\s*=\s*std::make_unique\s*<\s*Conto\s*>\s*\(\s*"Ada"\s*\)', L('Lo smart pointer', 'The smart pointer'))], '', 'auto conto = std::make_unique<Conto>("Ada");'), [
            dMatch(pMatch(), [['unique_ptr', L('Un solo proprietario', 'A single owner')], ['shared_ptr', L('Proprietà condivisa', 'Shared ownership')], ['auto', L('Tipo dedotto', 'Inferred type')], ['std::optional', L('Valore che può mancare', 'Value that may be missing')], [L('Lambda', 'Lambda'), L('Funzione anonima', 'Anonymous function')]], L('Il C++ moderno.', 'Modern C++.')),
            dBug(pBug(), ['void crea() {', '    Giocatore* g = new Giocatore("Bo");', '    g->salta();', '}'], 1, '    auto g = std::make_unique<Giocatore>("Bo");', L('Senza delete la memoria si perde: usa uno smart pointer.', 'Without delete memory leaks: use a smart pointer.')),
        ]),
    ],
];
