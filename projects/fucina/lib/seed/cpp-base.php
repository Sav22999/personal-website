<?php
/** C++ basics: from C to C++, standard library containers, classes and RAII, smart pointers and modern C++, algorithms and templates, references, strings and input, inheritance, exceptions. */

return [
    'id' => 'cpp-base', 'family' => 'cpp', 'after' => null,
    'title' => L('C++ base', 'C++ Basics'), 'short' => L('C++ base', 'C++ Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di videogiochi, browser e software ad alte prestazioni: libreria standard, classi e RAII, smart pointer, algoritmi, riferimenti, input e formattazione, ereditarietà ed eccezioni.', 'The language of video games, browsers and high-performance software: the standard library, classes and RAII, smart pointers, algorithms, references, input and formatting, inheritance and exceptions.'),
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
        lesson('algorithms', L('Algoritmi, lambda e templates', 'Algorithms, lambdas and templates'), [
            P('L’intestazione `<algorithm>` offre funzioni pronte su qualunque contenitore: `std::sort`, `std::find`, `std::count_if`, `std::transform`. Si combinano con le **lambda**, funzioni scritte al volo: `[](int x) { return x > 0; }`.', 'The `<algorithm>` header offers ready-made functions on any container: `std::sort`, `std::find`, `std::count_if`, `std::transform`. They combine with **lambdas**, functions written on the spot: `[](int x) { return x > 0; }`.'),
            C('#include <algorithm>
#include <vector>

std::vector<int> voti{7, 4, 9, 6};
std::sort(voti.begin(), voti.end());
auto sufficienti = std::count_if(voti.begin(), voti.end(),
                                 [](int v) { return v >= 6; });

template <typename T>
T massimo(T a, T b) { return a > b ? a : b; }

int m = massimo(3, 8);          // T = int
double d = massimo(2.5, 1.0);   // T = double', 'cpp'),
            P('Un **template** scrive una funzione o una classe una volta sola per più tipi: il compilatore genera la versione giusta per `int`, `double` o `std::string`. Così funziona tutta la libreria standard.', 'A **template** writes a function or a class once for many types: the compiler generates the right version for `int`, `double` or `std::string`. That is how the whole standard library works.'),
        ], [
            Q('ag1', L('Che cosa fa `std::count_if`?', 'What does `std::count_if` do?'), [L('Conta gli elementi che rispettano una condizione', 'Counts elements meeting a condition'), L('Ordina il vettore', 'Sorts the vector'), L('Cancella elementi', 'Deletes elements'), L('Conta i byte', 'Counts bytes')], 0, L('La condizione è spesso una lambda.', 'The condition is often a lambda.')),
            Q('ag2', L('Che cos’è una lambda?', 'What is a lambda?'), [L('Una funzione scritta al volo', 'A function written on the spot'), L('Un tipo di vettore', 'A vector type'), L('Una macro', 'A macro'), L('Un puntatore nullo', 'A null pointer')], 0, L('`[](int x) { … }`.', '`[](int x) { … }`.')),
            Q('ag3', L('A che cosa servono i template?', 'What are templates for?'), [L('Scrivere codice valido per più tipi', 'Writing code valid for many types'), L('Formattare il testo', 'Formatting text'), L('Gestire i file', 'Handling files'), L('Velocizzare la compilazione', 'Speeding up compilation')], 0, L('Il compilatore genera le versioni.', 'The compiler generates the versions.')),
        ], task('cpp', L('Ordina il vettore `v` con `std::sort(v.begin(), v.end());`.', 'Sort the vector `v` with `std::sort(v.begin(), v.end());`.'), '', [re('std::sort\(\s*v\.begin\(\)\s*,\s*v\.end\(\)\s*\)\s*;', L('Ordini il vettore', 'You sort the vector'))], '', 'std::sort(v.begin(), v.end());'), [
            dMatch(pMatch(), [['std::sort', L('Ordina', 'Sorts')], ['std::find', L('Cerca un valore', 'Finds a value')], ['std::count_if', L('Conta con condizione', 'Counts with a condition')], ['[](int x){}', L('Lambda', 'Lambda')], ['template', L('Codice per più tipi', 'Code for many types')]], L('Gli attrezzi della STL.', 'STL tools.')),
            dFlash(pFlash(), [[L('Gli algoritmi STL lavorano con begin e end', 'STL algorithms work with begin and end'), true], [L('Un template funziona solo con int', 'A template only works with int'), false], [L('Una lambda può essere passata a count_if', 'A lambda can be passed to count_if'), true], [L('std::sort esiste solo per gli array C', 'std::sort only exists for C arrays'), false]], L('Algoritmi generici.', 'Generic algorithms.')),
        ]),
        lesson('references', L('Riferimenti e const', 'References and const'), [
            P('Un **riferimento** (`int&`) è un altro nome per una variabile che esiste già. Passare un parametro per riferimento permette alla funzione di modificare l’originale senza puntatori; `const T&` evita la copia di oggetti grandi senza permettere modifiche. È il modo normale di passare stringhe e vettori.', 'A **reference** (`int&`) is another name for an existing variable. Passing a parameter by reference lets the function change the original without pointers; `const T&` avoids copying large objects without allowing changes. It is the normal way to pass strings and vectors.'),
            C('#include <iostream>
#include <vector>

void raddoppia(int& n) { n *= 2; }                 // modifica l’originale

double media(const std::vector<int>& v) {          // niente copia, sola lettura
    double s = 0;
    for (int x : v) s += x;
    return v.empty() ? 0 : s / v.size();
}

int main() {
    int x = 21;
    raddoppia(x);                                  // x vale 42
    std::vector<int> voti{7, 8, 9};
    std::cout << x << " " << media(voti) << "\n";

    int& alias = x;
    alias = 5;                                     // ora x vale 5
}', 'cpp'),
            P('Un riferimento va inizializzato subito e non può “cambiare bersaglio”. Regola pratica: tipi piccoli (`int`, `double`) per valore, oggetti grandi per `const&`, `&` solo se la funzione deve davvero modificarli. `const` dopo un metodo (`int valore() const`) promette che il metodo non cambia l’oggetto.', 'A reference must be initialised at once and cannot “change target”. Rule of thumb: small types (`int`, `double`) by value, large objects by `const&`, `&` only if the function really must change them. `const` after a method (`int valore() const`) promises the method does not change the object.'),
        ], [
            Q('r1', L('Che cosa succede a `x` dopo `raddoppia(x)` se il parametro è `int& n`?', 'What happens to `x` after `raddoppia(x)` if the parameter is `int& n`?'), [L('Resta uguale', 'It stays the same'), L('Raddoppia', 'It doubles'), L('Diventa 0', 'It becomes 0'), L('Errore di compilazione', 'Compile error')], 1, L('`n` è solo un altro nome per `x`.', '`n` is just another name for `x`.')),
            Q('r2', L('Perché passare un vettore come `const std::vector<int>&`?', 'Why pass a vector as `const std::vector<int>&`?'), [L('Per evitare la copia senza permettere modifiche', 'To avoid the copy without allowing changes'), L('Per poterlo modificare', 'To be able to change it'), L('Per renderlo globale', 'To make it global'), L('Perché i vector non si possono passare per valore', 'Because vectors cannot be passed by value')], 0, L('Per valore si potrebbe, ma copierebbe tutto.', 'By value is possible, but it would copy everything.')),
            Q('r3', L('Che cosa è vero per un riferimento?', 'What is true of a reference?'), [L('Può restare non inizializzato', 'It can stay uninitialised'), L('Può essere nullo come un puntatore', 'It can be null like a pointer'), L('Si può far riferire a un’altra variabile in seguito', 'It can later be made to refer to another variable'), L('Va inizializzato subito e resta legato a quella variabile', 'It must be initialised at once and stays bound to that variable')], 3, L('Assegnargli un valore cambia la variabile, non il legame.', 'Assigning to it changes the variable, not the binding.')),
        ], task('cpp', L('Scrivi `void azzera(int& n)` che imposta `n` a `0`.', 'Write `void azzera(int& n)` that sets `n` to `0`.'), '', [re('void\s+azzera\s*\(\s*int\s*&\s*n\s*\)', L('Il parametro è un riferimento', 'The parameter is a reference')), re('n\s*=\s*0\s*;', L('Lo azzeri', 'You set it to zero'))], '', 'void azzera(int& n) { n = 0; }'), [
            dMatch(pMatch(), [['int&', L('Riferimento modificabile', 'Modifiable reference')], ['const T&', L('Niente copia, sola lettura', 'No copy, read-only')], [L('T (per valore)', 'T (by value)'), L('Una copia', 'A copy')], ['int valore() const', L('Metodo che non cambia l’oggetto', 'Method that does not change the object')]], L('Come passare i parametri.', 'How to pass parameters.')),
            dBug(pBug(), ['void aggiungi(std::vector<int> v, int x) {', '    v.push_back(x);', '}'], 0, 'void aggiungi(std::vector<int>& v, int x) {', L('Per valore la funzione modifica una copia, non il vettore di chi chiama.', 'By value the function changes a copy, not the caller’s vector.')),
        ]),
        lesson('strings-io', L('Stringhe, input e formattazione', 'Strings, input and formatting'), [
            P('`std::string` cresce da sola e ha metodi comodi: `size()`, `substr()`, `find()`, `+` per concatenare, `==` per confrontare. `std::getline(std::cin, riga)` legge una riga intera; `std::cin >> n` legge una parola o un numero. Dal C++20 `std::format` compone testo con le graffe; dal C++23 `std::println` stampa direttamente.', '`std::string` grows by itself and has handy methods: `size()`, `substr()`, `find()`, `+` to concatenate, `==` to compare. `std::getline(std::cin, riga)` reads a whole line; `std::cin >> n` reads a word or a number. Since C++20 `std::format` composes text with braces; since C++23 `std::println` prints directly.'),
            C('#include <format>
#include <iostream>
#include <string>

int main() {
    std::string nome;
    std::cout << "Come ti chiami? ";
    std::getline(std::cin, nome);

    std::string saluto = "Ciao, " + nome + "!";
    if (saluto.find("Ada") != std::string::npos) saluto += " Benvenuta.";

    int anni = 0;
    std::cout << "Quanti anni hai? ";
    if (!(std::cin >> anni)) {
        std::cerr << "Non è un numero\n";
        return 1;
    }
    std::cout << std::format("{} Tra dieci anni ne avrai {}.\n", saluto, anni + 10);
}', 'cpp'),
            P('`find` restituisce la posizione, oppure `std::string::npos` se non trova nulla. Se `std::cin >> anni` fallisce, lo stream va in stato di errore e la condizione è falsa. `std::to_string(42)` e `std::stoi("42")` convertono tra numeri e testo.', '`find` returns the position, or `std::string::npos` if it finds nothing. If `std::cin >> anni` fails, the stream goes into an error state and the condition is false. `std::to_string(42)` and `std::stoi("42")` convert between numbers and text.'),
        ], [
            Q('si1', L('Che cosa restituisce `s.find("x")` se la x non c’è?', 'What does `s.find("x")` return if there is no x?'), ['-1', '0', 'std::string::npos', 'nullptr'], 2, L('È il valore massimo di `size_t`.', 'It is the largest value of `size_t`.')),
            Q('si2', L('Come leggi una riga intera, spazi compresi?', 'How do you read a whole line, spaces included?'), ['std::cin >> riga', 'std::getline(std::cin, riga)', 'scanf("%s", riga)', 'std::read(riga)'], 1, L('`>>` si ferma al primo spazio.', '`>>` stops at the first space.')),
            Q('si3', L('Che cosa produce `std::format("{} + {} = {}", 2, 3, 5)`?', 'What does `std::format("{} + {} = {}", 2, 3, 5)` produce?'), ['"2 + 3 = 5"', '"{} + {} = {}"', '"235"', L('Un errore di compilazione', 'A compile error')], 0, L('Ogni coppia di graffe prende il valore successivo.', 'Each pair of braces takes the next value.')),
        ], task('cpp', L('Componi `std::string msg = std::format("{} ha {} anni", nome, anni);`.', 'Compose `std::string msg = std::format("{} ha {} anni", nome, anni);`.'), '', [re('(std::string|auto)\s+msg\s*=\s*std::format\s*\(\s*"\{\}\s+ha\s+\{\}\s+anni"\s*,\s*nome\s*,\s*anni\s*\)\s*;', L('Componi il messaggio', 'You compose the message'))], '', 'std::string msg = std::format("{} ha {} anni", nome, anni);'), [
            dMatch(pMatch(), [['std::getline', L('Legge una riga intera', 'Reads a whole line')], ['std::cin >>', L('Legge una parola o un numero', 'Reads a word or a number')], ['find', L('Cerca, npos se manca', 'Searches, npos if missing')], ['std::format', L('Compone con le graffe', 'Composes with braces')], ['std::stoi', L('Testo in intero', 'Text to integer')]], L('Testo e input.', 'Text and input.')),
            dFlash(pFlash(), [[L('Due std::string si confrontano con ==', 'Two std::strings are compared with =='), true], [L('`std::cin >> s` legge anche gli spazi', '`std::cin >> s` also reads spaces'), false], [L('`std::format` esiste dal C++20', '`std::format` exists since C++20'), true], [L('`+` concatena due std::string', '`+` concatenates two std::strings'), true]], L('Per le righe con spazi c’è getline.', 'For lines with spaces there is getline.')),
        ]),
        lesson('inheritance', L('Ereditarietà e polimorfismo', 'Inheritance and polymorphism'), [
            P('Una classe può **ereditare** da un’altra (`class Cerchio : public Forma`) e riusarne l’interfaccia. Un metodo `virtual` può essere ridefinito dalle derivate con `override`; chiamato attraverso un puntatore o un riferimento alla base, esegue la versione giusta: è il **polimorfismo**.', 'A class can **inherit** from another (`class Cerchio : public Forma`) and reuse its interface. A `virtual` method can be redefined by derived classes with `override`; called through a pointer or reference to the base, it runs the right version: this is **polymorphism**.'),
            C('#include <iostream>
#include <memory>
#include <numbers>
#include <vector>

class Forma {
public:
    virtual ~Forma() = default;                 // distruttore virtuale
    virtual double area() const = 0;            // puramente virtuale: classe astratta
};

class Cerchio : public Forma {
public:
    explicit Cerchio(double r) : r_(r) {}
    double area() const override { return std::numbers::pi * r_ * r_; }
private:
    double r_;
};

class Quadrato : public Forma {
public:
    explicit Quadrato(double l) : l_(l) {}
    double area() const override { return l_ * l_; }
private:
    double l_;
};

int main() {
    std::vector<std::unique_ptr<Forma>> forme;
    forme.push_back(std::make_unique<Cerchio>(1.0));
    forme.push_back(std::make_unique<Quadrato>(2.0));
    for (const auto& f : forme) std::cout << f->area() << "\n";
}', 'cpp'),
            P('`= 0` rende il metodo **puramente virtuale**: la classe diventa astratta e non si può istanziare. Una base usata in modo polimorfico deve avere il **distruttore virtuale**, altrimenti distruggere una derivata attraverso la base è comportamento indefinito. `override` fa controllare al compilatore che tu stia davvero ridefinendo un metodo.', '`= 0` makes the method **pure virtual**: the class becomes abstract and cannot be instantiated. A base used polymorphically must have a **virtual destructor**, otherwise destroying a derived object through the base is undefined behaviour. `override` makes the compiler check that you really are redefining a method.'),
        ], [
            Q('in1', L('Che cosa fa `override`?', 'What does `override` do?'), [L('Rende il metodo più veloce', 'Makes the method faster'), L('Nasconde il metodo della base', 'Hides the base method'), L('Fa controllare al compilatore che il metodo ridefinisca un virtual della base', 'Makes the compiler check the method redefines a virtual of the base'), L('Rende la classe astratta', 'Makes the class abstract')], 2, L('Un errore di battitura nel nome diventa un errore di compilazione.', 'A typo in the name becomes a compile error.')),
            Q('in2', L('Che cosa significa `virtual double area() const = 0;`?', 'What does `virtual double area() const = 0;` mean?'), [L('Il metodo restituisce sempre 0', 'The method always returns 0'), L('Il metodo è puramente virtuale: la classe è astratta', 'The method is pure virtual: the class is abstract'), L('Il metodo è statico', 'The method is static'), L('Il metodo è privato', 'The method is private')], 1, L('Le derivate concrete devono implementarlo.', 'Concrete derived classes must implement it.')),
            Q('in3', L('Perché una base polimorfica ha il distruttore `virtual`?', 'Why does a polymorphic base have a `virtual` destructor?'), [L('Perché distruggere una derivata attraverso la base sia corretto', 'So that destroying a derived object through the base is correct'), L('Per velocità', 'For speed'), L('Per vietare le copie', 'To forbid copies'), L('Non serve mai', 'It is never needed')], 0, L('Altrimenti il distruttore della derivata non viene chiamato.', 'Otherwise the derived destructor is not called.')),
        ], task('cpp', L('Scrivi `class Cane : public Animale` con `std::string verso() const override { return "bau"; }`.', 'Write `class Cane : public Animale` with `std::string verso() const override { return "bau"; }`.'), 'class Animale {
public:
    virtual ~Animale() = default;
    virtual std::string verso() const = 0;
};
', [re('class\s+Cane\s*:\s*public\s+Animale', L('Cane eredita da Animale', 'Cane inherits from Animale')), re('std::string\s+verso\s*\(\s*\)\s*const\s+override', L('Ridefinisci il verso', 'You redefine the sound')), re('return\s+"bau"\s*;', L('Restituisci «bau»', 'You return “bau”'))], '', 'class Cane : public Animale {
public:
    std::string verso() const override { return "bau"; }
};'), [
            dBlanks(pFill(), "class Forma {\npublic:\n    ___ ~Forma() = default;\n    virtual double area() const = ___;\n};\nclass Cerchio : ___ Forma {\npublic:\n    double area() const ___ { return 3.14; }\n};", ['virtual', '0', 'public', 'override'], ['static', 'null', 'final()'], L('Distruttore virtuale, metodo astratto, ereditarietà, ridefinizione.', 'Virtual destructor, abstract method, inheritance, redefinition.')),
            dFlash(pFlash(), [[L('Una classe con un metodo `= 0` non si può istanziare', 'A class with an `= 0` method cannot be instantiated'), true], [L('Senza `virtual` la chiamata attraverso la base sceglie il tipo reale', 'Without `virtual` a call through the base picks the real type'), false], [L('`override` è facoltativo ma consigliato', '`override` is optional but recommended'), true], [L('Si possono tenere oggetti derivati in un vector di unique_ptr alla base', 'Derived objects can be kept in a vector of unique_ptr to the base'), true]], L('Senza virtual vale il tipo dichiarato.', 'Without virtual the declared type wins.')),
        ]),
        lesson('exceptions', L('Eccezioni', 'Exceptions'), [
            P('Quando una funzione non può fare il suo lavoro lancia un’**eccezione** con `throw`; chi può gestirla la cattura con `try`/`catch`. Grazie a RAII, mentre l’eccezione risale, i distruttori degli oggetti locali vengono chiamati e le risorse si liberano.', 'When a function cannot do its job it throws an **exception** with `throw`; whoever can handle it catches it with `try`/`catch`. Thanks to RAII, as the exception travels up, the destructors of local objects are called and resources are released.'),
            C('#include <iostream>
#include <stdexcept>
#include <string>

double dividi(double a, double b) {
    if (b == 0) throw std::invalid_argument("divisione per zero");
    return a / b;
}

int main() {
    try {
        std::cout << dividi(10, 2) << "\n";     // 5
        std::cout << dividi(1, 0) << "\n";      // lancia
        std::cout << std::stoi("abc") << "\n";  // non arriva qui
    } catch (const std::invalid_argument& e) {
        std::cerr << "Errore: " << e.what() << "\n";
    } catch (const std::exception& e) {
        std::cerr << "Altro errore: " << e.what() << "\n";
    }
}', 'cpp'),
            P('Si cattura per riferimento `const&`, dal caso più specifico al più generale (`std::exception` è la base di quelle della libreria standard). `e.what()` dà il messaggio. Le funzioni che non lanciano mai si segnano `noexcept`. Per errori previsti, come un input non valido, il C++23 offre anche `std::expected`.', 'You catch by `const&` reference, from the most specific case to the most general (`std::exception` is the base of the standard library ones). `e.what()` gives the message. Functions that never throw are marked `noexcept`. For expected errors, such as invalid input, C++23 also offers `std::expected`.'),
        ], [
            Q('ex1', L('In che ordine si scrivono i `catch`?', 'In what order do you write the `catch` blocks?'), [L('Dal più generale al più specifico', 'From the most general to the most specific'), L('In ordine alfabetico', 'In alphabetical order'), L('L’ordine non conta', 'The order does not matter'), L('Dal più specifico al più generale', 'From the most specific to the most general')], 3, L('Il primo che corrisponde vince.', 'The first that matches wins.')),
            Q('ex2', L('Che cosa succede agli oggetti locali quando un’eccezione esce dalla funzione?', 'What happens to local objects when an exception leaves the function?'), [L('Vengono distrutti e i loro distruttori chiamati', 'They are destroyed and their destructors called'), L('Restano in memoria', 'They stay in memory'), L('Vengono copiati nel catch', 'They are copied into the catch'), L('Il programma si ferma sempre', 'The program always stops')], 0, L('È lo stack unwinding.', 'It is stack unwinding.')),
            Q('ex3', L('Che cosa promette `noexcept`?', 'What does `noexcept` promise?'), [L('Che la funzione è più veloce', 'That the function is faster'), L('Che la funzione non lancia eccezioni', 'That the function throws no exceptions'), L('Che la funzione restituisce un errore', 'That the function returns an error'), L('Che il risultato va usato', 'That the result must be used')], 1, L('Se lanciasse comunque, il programma terminerebbe.', 'If it threw anyway, the program would terminate.')),
        ], task('cpp', L('Lancia `std::out_of_range("indice")` se `i >= v.size()`.', 'Throw `std::out_of_range("indice")` if `i >= v.size()`.'), 'int prendi(const std::vector<int>& v, std::size_t i) {
    return v[i];
}', [re('if\s*\(\s*i\s*>=\s*v\.size\s*\(\s*\)\s*\)', L('Controlli l’indice', 'You check the index')), re('throw\s+std::out_of_range\s*\(\s*"indice"\s*\)', L('Lanci l’eccezione', 'You throw the exception'))], '', 'int prendi(const std::vector<int>& v, std::size_t i) {
    if (i >= v.size()) throw std::out_of_range("indice");
    return v[i];
}'), [
            dOrder(pSort(), ['try {', '    auto n = std::stoi(testo);', '    std::cout << n * 2;', '} catch (const std::invalid_argument& e) {', '    std::cerr << e.what();', '}'], L('Prova, usa, gestisci.', 'Try, use, handle.')),
            dMatch(pMatch(), [['throw', L('Lancia un’eccezione', 'Throws an exception')], ['try', L('Prova il codice', 'Tries the code')], ['catch', L('Gestisce un tipo di errore', 'Handles a kind of error')], ['e.what()', L('Messaggio dell’errore', 'Error message')], ['noexcept', L('Non lancia mai', 'Never throws')]], L('Le eccezioni in C++.', 'Exceptions in C++.')),
        ]),
    ],
];
