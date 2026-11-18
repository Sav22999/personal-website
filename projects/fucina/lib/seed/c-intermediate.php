<?php
/** C intermediate: pointers and arrays, function pointers, linked lists, bit operations, multi-file projects with make, debugging and undefined behaviour, C23. */

return [
    'id' => 'c-intermediate', 'family' => 'c', 'after' => 'c-base',
    'title' => L('C intermedio', 'Intermediate C'), 'short' => L('C intermedio', 'C Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Puntatori e array, puntatori a funzione, liste collegate, operazioni sui bit, progetti su più file con make, debug con i sanitizer e le novità del C23.', 'Pointers and arrays, function pointers, linked lists, bit operations, multi-file projects with make, debugging with sanitizers and what is new in C23.'),
    'lessons' => [
        lesson('pointer-arithmetic', L('Puntatori e array', 'Pointers and arrays'), [
            P('Il nome di un array, usato in un’espressione, diventa un puntatore al primo elemento. Per questo `v[i]` equivale a `*(v + i)`: sommare 1 a un puntatore lo sposta all’elemento successivo, non al byte successivo. Quando passi un array a una funzione passi in realtà un puntatore: la dimensione si perde e va passata a parte.', 'An array’s name, used in an expression, becomes a pointer to the first element. That is why `v[i]` is the same as `*(v + i)`: adding 1 to a pointer moves it to the next element, not the next byte. When you pass an array to a function you actually pass a pointer: the size is lost and must be passed separately.'),
            C('#include <stdio.h>
#include <stddef.h>

int somma(const int *v, size_t n) {
    int totale = 0;
    for (const int *p = v; p < v + n; p++) {
        totale += *p;
    }
    return totale;
}

int main(void) {
    int voti[] = {7, 8, 9, 6};
    size_t n = sizeof voti / sizeof voti[0];   // 4: funziona solo qui, non nella funzione
    printf("%d\n", somma(voti, n));             // 30
    printf("%d %d\n", voti[2], *(voti + 2));    // 9 9
    return 0;
}', 'c'),
            P('`const int *v` promette che la funzione non modificherà i valori. `sizeof voti / sizeof voti[0]` dà il numero di elementi solo dove l’array è dichiarato: dentro `somma`, `sizeof v` sarebbe la dimensione di un puntatore. Andare oltre la fine, tranne il puntatore “uno dopo l’ultimo” usato per confrontare, è comportamento indefinito.', '`const int *v` promises that the function will not change the values. `sizeof voti / sizeof voti[0]` gives the number of elements only where the array is declared: inside `somma`, `sizeof v` would be the size of a pointer. Going past the end, except for the “one past the last” pointer used for comparisons, is undefined behaviour.'),
        ], [
            Q('pa1', L('A che cosa equivale `v[3]`?', 'What is `v[3]` equivalent to?'), ['*(v + 3)', '*v + 3', '&v + 3', 'v * 3'], 0, L('`*v + 3` sommerebbe 3 al primo elemento.', '`*v + 3` would add 3 to the first element.')),
            Q('pa2', L('`p` punta a un `int` all’indirizzo 1000 e un int occupa 4 byte: dove punta `p + 1`?', '`p` points to an `int` at address 1000 and an int takes 4 bytes: where does `p + 1` point?'), ['1001', '1004', '1008', '1000'], 1, L('L’aritmetica dei puntatori conta in elementi.', 'Pointer arithmetic counts in elements.')),
            Q('pa3', L('Perché una funzione che riceve un array ha bisogno anche della lunghezza?', 'Why does a function receiving an array also need its length?'), [L('Per andare più veloce', 'To run faster'), L('Perché gli array C hanno sempre 10 elementi', 'Because C arrays always have 10 elements'), L('Perché il compilatore lo impone sempre', 'Because the compiler always demands it'), L('Perché riceve solo un puntatore e la dimensione si perde', 'Because it receives only a pointer and the size is lost')], 3, L('Per questo si usa la coppia puntatore e `size_t n`.', 'That is why the pointer and `size_t n` pair is used.')),
        ], task('c', L('Scrivi `double media(const double *v, size_t n)` che somma gli elementi con un ciclo `for` e restituisce la somma divisa per `n`.', 'Write `double media(const double *v, size_t n)` that adds up the elements with a `for` loop and returns the sum divided by `n`.'), '', [re('double\s+media\s*\(\s*const\s+double\s*\*\s*v\s*,\s*size_t\s+n\s*\)', L('La firma è giusta', 'The signature is right')), re('for\s*\(', L('Usi un ciclo', 'You use a loop')), re('/\s*(\(\s*double\s*\)\s*)?n\b', L('Dividi per n', 'You divide by n'))], '', "double media(const double *v, size_t n) {\n    double s = 0;\n    for (size_t i = 0; i < n; i++) s += v[i];\n    return s / n;\n}"), [
            dMatch(pMatch(), [['v[i]', L('Uguale a *(v + i)', 'Same as *(v + i)')], ['p + 1', L('Elemento successivo', 'Next element')], ['const int *p', L('Valori in sola lettura', 'Read-only values')], ['sizeof v / sizeof v[0]', L('Numero di elementi dell’array', 'Number of elements in the array')], ['size_t', L('Tipo per dimensioni e indici', 'Type for sizes and indices')]], L('Puntatori e array.', 'Pointers and arrays.')),
            dFlash(pFlash(), [[L('Il nome di un array diventa un puntatore al primo elemento', 'An array name becomes a pointer to the first element'), true], [L('`p + 1` avanza sempre di un byte', '`p + 1` always moves one byte'), false], [L('Dentro una funzione, `sizeof` di un parametro array dà la dimensione dell’array', 'Inside a function, `sizeof` of an array parameter gives the array’s size'), false], [L('Si può confrontare un puntatore con `v + n`', 'You can compare a pointer with `v + n`'), true]], L('Nella funzione l’array è solo un puntatore.', 'Inside the function the array is just a pointer.')),
        ]),
        lesson('function-pointers', L('Puntatori a funzione', 'Function pointers'), [
            P('Anche una funzione ha un indirizzo e si può salvare in un **puntatore a funzione**. Serve per passare un comportamento a un’altra funzione: l’esempio classico è `qsort` della libreria standard, che ordina qualsiasi array usando la tua funzione di confronto.', 'A function has an address too and can be stored in a **function pointer**. It is used to pass behaviour to another function: the classic example is the standard library’s `qsort`, which sorts any array using your comparison function.'),
            C('#include <stdio.h>
#include <stdlib.h>

int confronta_interi(const void *a, const void *b) {
    int x = *(const int *)a;
    int y = *(const int *)b;
    return (x > y) - (x < y);      // -1, 0 o 1, senza rischio di overflow
}

typedef int (*Operazione)(int, int);

int somma(int a, int b) { return a + b; }
int prodotto(int a, int b) { return a * b; }

int main(void) {
    int v[] = {5, 2, 9, 1};
    qsort(v, 4, sizeof v[0], confronta_interi);    // 1 2 5 9

    Operazione op = prodotto;
    printf("%d\n", op(3, 4));                       // 12

    Operazione tabella[] = { somma, prodotto };
    printf("%d\n", tabella[0](3, 4));               // 7
    return 0;
}', 'c'),
            P('La sintassi `int (*nome)(int, int)` è difficile da leggere: per questo si usa quasi sempre un `typedef`. `void *` è un puntatore generico: `qsort` non sa che tipo di dati ordina, è la funzione di confronto a convertirli. Restituire `x - y` sembra più semplice ma può andare in overflow con numeri grandi.', 'The `int (*name)(int, int)` syntax is hard to read: that is why a `typedef` is almost always used. `void *` is a generic pointer: `qsort` does not know what type of data it sorts, the comparison function converts it. Returning `x - y` looks simpler but can overflow with large numbers.'),
        ], [
            Q('fp1', L('Che cosa deve restituire la funzione di confronto di `qsort`?', 'What must `qsort`’s comparison function return?'), [L('true o false', 'true or false'), L('Un numero negativo, zero o positivo', 'A negative number, zero or a positive number'), L('L’elemento più grande', 'The larger element'), L('L’indice dell’elemento', 'The element’s index')], 1, L('Negativo se a viene prima di b.', 'Negative if a comes before b.')),
            Q('fp2', L('Come dichiari `p`, puntatore a una funzione che riceve un `double` e restituisce un `double`?', 'How do you declare `p`, a pointer to a function taking a `double` and returning a `double`?'), ['double *p(double);', 'double p*(double);', 'function<double> p;', 'double (*p)(double);'], 3, L('La prima forma dichiara una funzione che restituisce un puntatore.', 'The first form declares a function returning a pointer.')),
            Q('fp3', L('Perché `qsort` usa `const void *`?', 'Why does `qsort` use `const void *`?'), [L('Perché deve funzionare con dati di qualsiasi tipo', 'Because it must work with data of any type'), L('Perché così è più veloce', 'Because it is faster that way'), L('Perché i dati non esistono', 'Because the data does not exist'), L('Per impedire di ordinare gli interi', 'To prevent sorting integers')], 0, L('Il tipo vero lo conosce solo la tua funzione.', 'Only your function knows the real type.')),
        ], task('c', L('Ordina `v` con `qsort(v, n, sizeof v[0], confronta);`.', 'Sort `v` with `qsort(v, n, sizeof v[0], confronta);`.'), 'int v[] = {4, 1, 3};
size_t n = 3;
', [re('qsort\s*\(\s*v\s*,\s*n\s*,\s*sizeof\s*\(?\s*(v\s*\[\s*0\s*\]|\*\s*v|int)\s*\)?\s*,\s*confronta\s*\)\s*;', L('Ordini con qsort', 'You sort with qsort'))], '', 'qsort(v, n, sizeof v[0], confronta);'), [
            dBlanks(pFill(), "typedef double (___Funzione)(double);\ndouble doppio(double x) { return x * 2; }\nFunzione f = ___;\nprintf(\"%f\\n\", ___(1.5));", ['*', 'doppio', 'f'], ['&&', 'doppio()', 'Funzione'], L('Tipo puntatore, assegnazione, chiamata.', 'Pointer type, assignment, call.')),
            dMatch(pMatch(), [['qsort', L('Ordina qualsiasi array', 'Sorts any array')], ['void *', L('Puntatore generico', 'Generic pointer')], ['typedef int (*Op)(int)', L('Nome per un tipo di funzione', 'Name for a function type')], ['(x > y) - (x < y)', L('Confronto senza overflow', 'Comparison without overflow')]], L('Funzioni come valori.', 'Functions as values.')),
        ]),
        lesson('linked-list', L('Liste collegate', 'Linked lists'), [
            P('Una **lista collegata** è fatta di nodi creati con `malloc`: ognuno contiene un valore e il puntatore al nodo successivo; l’ultimo punta a `NULL`. A differenza di un array cresce un nodo alla volta e inserire in testa costa sempre lo stesso, ma per raggiungere l’elemento n-esimo bisogna scorrerla.', 'A **linked list** is made of nodes created with `malloc`: each holds a value and the pointer to the next node; the last one points to `NULL`. Unlike an array it grows one node at a time and inserting at the head always costs the same, but to reach the n-th item you have to walk it.'),
            C('#include <stdio.h>
#include <stdlib.h>

typedef struct Nodo {
    int valore;
    struct Nodo *prossimo;
} Nodo;

Nodo *in_testa(Nodo *testa, int valore) {
    Nodo *n = malloc(sizeof *n);
    if (n == NULL) return testa;      // memoria finita: lista invariata
    n->valore = valore;
    n->prossimo = testa;
    return n;
}

void libera(Nodo *testa) {
    while (testa != NULL) {
        Nodo *dopo = testa->prossimo;
        free(testa);
        testa = dopo;
    }
}

int main(void) {
    Nodo *lista = NULL;
    for (int i = 1; i <= 3; i++) lista = in_testa(lista, i);
    for (Nodo *p = lista; p != NULL; p = p->prossimo) printf("%d ", p->valore);   // 3 2 1
    libera(lista);
    return 0;
}', 'c'),
            P('Nel `typedef` la struct ha anche un nome (`struct Nodo`) perché deve riferirsi a sé stessa prima che `Nodo` esista. In `libera` si salva il successivo **prima** di chiamare `free`: dopo, leggere `testa->prossimo` vorrebbe dire usare memoria già liberata.', 'In the `typedef` the struct also has a name (`struct Nodo`) because it must refer to itself before `Nodo` exists. In `libera` the next node is saved **before** calling `free`: afterwards, reading `testa->prossimo` would mean using freed memory.'),
        ], [
            Q('ll1', L('Dove punta il campo `prossimo` dell’ultimo nodo?', 'Where does the last node’s `prossimo` field point?'), ['NULL', L('Al primo nodo', 'To the first node'), L('A sé stesso', 'To itself'), L('A un indirizzo casuale', 'To a random address')], 0, L('Così il ciclo di lettura sa quando fermarsi.', 'That way the reading loop knows when to stop.')),
            Q('ll2', L('Perché in `libera` si salva `testa->prossimo` prima di `free(testa)`?', 'Why does `libera` save `testa->prossimo` before `free(testa)`?'), [L('Per andare più veloce', 'To run faster'), L('Perché free cancella tutta la lista', 'Because free deletes the whole list'), L('Perché dopo free quel nodo non si può più leggere', 'Because after free that node can no longer be read'), L('Non serve: è solo una questione di stile', 'It is not needed: it is just style')], 2, L('Sarebbe un use-after-free.', 'It would be a use-after-free.')),
            Q('ll3', L('Che cosa costa poco in una lista collegata rispetto a un array?', 'What is cheap in a linked list compared with an array?'), [L('Leggere l’elemento numero 500', 'Reading item number 500'), L('Inserire un elemento in testa', 'Inserting an item at the head'), L('Ordinare con qsort', 'Sorting with qsort'), L('Usare meno memoria per elemento', 'Using less memory per item')], 1, L('Ogni nodo in più occupa anche lo spazio di un puntatore.', 'Each node also takes the space of a pointer.')),
        ], task('c', L('Completa `in_testa`: collega il nuovo nodo con `n->prossimo = testa;` e restituisci `n`.', 'Complete `in_testa`: link the new node with `n->prossimo = testa;` and return `n`.'), 'Nodo *in_testa(Nodo *testa, int valore) {
    Nodo *n = malloc(sizeof *n);
    if (n == NULL) return testa;
    n->valore = valore;
}', [re('n\s*->\s*prossimo\s*=\s*testa\s*;', L('Colleghi il nodo', 'You link the node')), re('return\s+n\s*;', L('Restituisci la nuova testa', 'You return the new head'))], '', 'Nodo *in_testa(Nodo *testa, int valore) {
    Nodo *n = malloc(sizeof *n);
    if (n == NULL) return testa;
    n->valore = valore;
    n->prossimo = testa;
    return n;
}'), [
            dBug(pBug(), ['Nodo *n = malloc(sizeof n);', 'if (n == NULL) return testa;', 'n->valore = valore;', 'n->prossimo = testa;'], 0, 'Nodo *n = malloc(sizeof *n);', L('`sizeof n` è la dimensione del puntatore, non del nodo.', '`sizeof n` is the size of the pointer, not of the node.')),
            dFlash(pFlash(), [[L('Ogni nodo creato con malloc va liberato con free', 'Every node created with malloc must be freed with free'), true], [L('In una lista collegata l’n-esimo elemento si legge all’istante', 'In a linked list the n-th item is read instantly'), false], [L('`p->prossimo` equivale a `(*p).prossimo`', '`p->prossimo` is the same as `(*p).prossimo`'), true], [L('Inserire in testa non richiede di scorrere la lista', 'Inserting at the head does not require walking the list'), true]], L('Per l’n-esimo bisogna scorrere.', 'For the n-th you must walk.')),
        ]),
        lesson('bits', L('Operazioni sui bit', 'Bit operations'), [
            P('Gli operatori `&` (AND), `|` (OR), `^` (XOR), `~` (NOT), `<<` e `>>` lavorano sui singoli bit. Servono per i **flag**: tanti interruttori sì/no compressi in un solo intero, come i permessi dei file o i registri dell’hardware.', 'The operators `&` (AND), `|` (OR), `^` (XOR), `~` (NOT), `<<` and `>>` work on single bits. They are used for **flags**: many yes/no switches packed into one integer, such as file permissions or hardware registers.'),
            C('#include <stdio.h>
#include <stdint.h>

enum {
    LETTURA    = 1u << 0,   // 0b001
    SCRITTURA  = 1u << 1,   // 0b010
    ESECUZIONE = 1u << 2,   // 0b100
};

int main(void) {
    uint8_t permessi = 0;
    permessi |= LETTURA | SCRITTURA;        // accende due bit
    permessi &= ~SCRITTURA;                 // spegne SCRITTURA
    permessi ^= ESECUZIONE;                 // inverte ESECUZIONE

    if (permessi & LETTURA) printf("Si può leggere\n");
    printf("valore: %u\n", (unsigned)permessi);   // 5
    return 0;
}', 'c'),
            P('Per i bit usa tipi **senza segno** di dimensione nota, come `uint8_t` o `uint32_t` da `<stdint.h>`: spostare bit in un intero con segno può dare comportamento indefinito. Per i tipi senza segno `x << n` moltiplica per 2ⁿ e `x >> n` divide.', 'For bits use **unsigned** types of known size, such as `uint8_t` or `uint32_t` from `<stdint.h>`: shifting bits in a signed integer can be undefined behaviour. For unsigned types `x << n` multiplies by 2ⁿ and `x >> n` divides.'),
        ], [
            Q('b1', L('Come accendi il bit FLAG in `x` senza toccare gli altri?', 'How do you turn on the FLAG bit in `x` without touching the others?'), ['x &= FLAG', 'x |= FLAG', 'x ^= ~FLAG', 'x = FLAG'], 1, L('`x = FLAG` spegnerebbe tutti gli altri bit.', '`x = FLAG` would turn off every other bit.')),
            Q('b2', L('Come controlli se il bit FLAG è acceso?', 'How do you check whether the FLAG bit is on?'), ['if (x | FLAG)', 'if (x == FLAG)', 'if (x & FLAG)', 'if (x ^ FLAG)'], 2, L('`x == FLAG` sarebbe vero solo se fosse l’unico bit acceso.', '`x == FLAG` would be true only if it were the only bit on.')),
            Q('b3', L('Quanto vale `1u << 3`?', 'What is `1u << 3`?'), ['3', '4', '16', '8'], 3, L('1 spostato di tre posizioni: 0b1000.', '1 shifted three places: 0b1000.')),
        ], task('c', L('Spegni il bit `SCRITTURA` in `permessi` senza toccare gli altri: `permessi &= ~SCRITTURA;`.', 'Turn off the `SCRITTURA` bit in `permessi` without touching the others: `permessi &= ~SCRITTURA;`.'), 'uint8_t permessi = 7;
', [re('permessi\s*(&=\s*~\s*\(?\s*SCRITTURA\s*\)?|=\s*permessi\s*&\s*~\s*\(?\s*SCRITTURA\s*\)?)\s*;', L('Spegni solo quel bit', 'You turn off only that bit'))], '', 'permessi &= ~SCRITTURA;'), [
            dMatch(pMatch(), [['&', L('AND: entrambi i bit a 1', 'AND: both bits set')], ['|', L('OR: almeno un bit a 1', 'OR: at least one bit set')], ['^', L('XOR: bit diversi', 'XOR: bits differ')], ['~', L('Inverte tutti i bit', 'Flips every bit')], ['<<', L('Sposta a sinistra', 'Shifts left')]], L('Gli operatori sui bit.', 'Bit operators.')),
            dBlanks(pFill(), "flag ___ VISIBILE;      // accende\nflag ___ ~NASCOSTO;    // spegne\nif (flag ___ VISIBILE) mostra();", ['|=', '&=', '&'], ['+=', '&&', '||'], L('Accendi, spegni, controlla.', 'Set, clear, test.')),
        ]),
        lesson('multifile', L('Più file, header e make', 'Several files, headers and make'), [
            P('Un programma vero si divide in più file `.c`. Le **dichiarazioni** condivise (prototipi, struct, costanti) vanno in un file **header** `.h`, incluso con `#include "nome.h"`; le **definizioni** restano nel `.c`. Le **include guard** impediscono che un header venga incluso due volte.', 'A real program is split into several `.c` files. Shared **declarations** (prototypes, structs, constants) go in a **header** file `.h`, included with `#include "name.h"`; the **definitions** stay in the `.c`. **Include guards** stop a header from being included twice.'),
            C('/* geometria.h */
#ifndef GEOMETRIA_H
#define GEOMETRIA_H

typedef struct { double x, y; } Punto;
double distanza(Punto a, Punto b);

#endif

/* geometria.c */
#include <math.h>
#include "geometria.h"

double distanza(Punto a, Punto b) {
    return hypot(a.x - b.x, a.y - b.y);
}

/* main.c */
#include <stdio.h>
#include "geometria.h"

int main(void) {
    printf("%.1f\n", distanza((Punto){0, 0}, (Punto){3, 4}));   // 5.0
    return 0;
}', 'c'),
            C('CFLAGS = -Wall -Wextra -g

geometria: main.o geometria.o
	$(CC) -o $@ $^ -lm

clean:
	rm -f *.o geometria', 'text'),
            P('`gcc -c` compila ogni `.c` in un file oggetto `.o`; il **linker** li unisce in un eseguibile (`-lm` aggiunge la libreria matematica). `make` legge il `Makefile`, dove le righe dei comandi iniziano con un tab, e ricompila solo i file cambiati. `#include <…>` cerca negli header di sistema, `#include "…"` prima nella cartella del progetto.', '`gcc -c` compiles each `.c` into an object file `.o`; the **linker** joins them into an executable (`-lm` adds the maths library). `make` reads the `Makefile`, where command lines start with a tab, and rebuilds only the files that changed. `#include <…>` searches the system headers, `#include "…"` the project folder first.'),
        ], [
            Q('mf1', L('Che cosa va in un file header `.h`?', 'What goes in a `.h` header file?'), [L('Le dichiarazioni condivise: prototipi, tipi, costanti', 'Shared declarations: prototypes, types, constants'), L('La funzione main', 'The main function'), L('Il codice già compilato', 'The compiled code'), L('Solo commenti', 'Only comments')], 0, L('Le definizioni restano nei file `.c`.', 'Definitions stay in the `.c` files.')),
            Q('mf2', L('A che cosa servono `#ifndef`, `#define` e `#endif` in un header?', 'What are `#ifndef`, `#define` and `#endif` for in a header?'), [L('A velocizzare il programma', 'Speeding up the program'), L('A nascondere il codice', 'Hiding the code'), L('A evitare che l’header venga incluso due volte', 'Preventing the header from being included twice'), L('A definire il main', 'Defining main')], 2, L('Molti compilatori accettano anche `#pragma once`.', 'Many compilers also accept `#pragma once`.')),
            Q('mf3', L('Che cosa fa `make` se cambi un solo file `.c`?', 'What does `make` do if you change a single `.c` file?'), [L('Ricompila tutto da zero', 'Rebuilds everything from scratch'), L('Ricompila solo ciò che dipende da quel file', 'Rebuilds only what depends on that file'), L('Niente: serve prima `make clean`', 'Nothing: you need `make clean` first'), L('Cancella il Makefile', 'Deletes the Makefile')], 1, L('Confronta le date dei file.', 'It compares file timestamps.')),
        ], task('c', L('Aggiungi a `conto.h` le include guard: `#ifndef CONTO_H`, `#define CONTO_H` e, alla fine, `#endif`.', 'Add include guards to `conto.h`: `#ifndef CONTO_H`, `#define CONTO_H` and, at the end, `#endif`.'), 'double interesse(double capitale);
', [re('#ifndef\s+CONTO_H', L('Controlli se è già definito', 'You check whether it is already defined')), re('#define\s+CONTO_H', L('Lo definisci', 'You define it')), re('#endif', L('Chiudi la guardia', 'You close the guard'))], '', "#ifndef CONTO_H\n#define CONTO_H\n\ndouble interesse(double capitale);\n\n#endif"), [
            dOrder(pSort(), ['#ifndef UTIL_H', '#define UTIL_H', 'int massimo(int a, int b);', '#endif'], L('Controlla, definisci, dichiara, chiudi.', 'Check, define, declare, close.')),
            dMatch(pMatch(), [['.h', L('Dichiarazioni condivise', 'Shared declarations')], ['.c', L('Definizioni', 'Definitions')], ['.o', L('File oggetto compilato', 'Compiled object file')], ['linker', L('Unisce gli oggetti', 'Joins the objects')], ['make', L('Ricompila ciò che è cambiato', 'Rebuilds what changed')]], L('Dai sorgenti all’eseguibile.', 'From sources to executable.')),
        ]),
        lesson('debugging', L('Debug e comportamento indefinito', 'Debugging and undefined behaviour'), [
            P('Il **comportamento indefinito** (UB) è il punto debole del C: leggere fuori da un array, usare memoria liberata, leggere variabili non inizializzate, superare i limiti di un intero con segno. Il programma può sembrare funzionare e poi rompersi altrove, o su un’altra macchina. Gli strumenti moderni li scovano per te.', '**Undefined behaviour** (UB) is C’s weak spot: reading outside an array, using freed memory, reading uninitialised variables, overflowing a signed integer. The program may seem to work and then break elsewhere, or on another machine. Modern tools find these for you.'),
            C('#include <limits.h>

int main(void) {
    int v[3] = {1, 2, 3};
    int i = 3;
    int x = v[i];          // UB: fuori dai limiti, ASan lo segnala
    int grande = INT_MAX;
    grande += 1;           // UB: overflow con segno, UBSan lo segnala
    return x + grande;
}', 'c'),
            C('# AddressSanitizer e UndefinedBehaviorSanitizer
gcc -g -fsanitize=address,undefined -Wall -Wextra -o prova prova.c
./prova

# debugger passo passo
gdb ./prova
(gdb) break main
(gdb) run
(gdb) next
(gdb) print x
(gdb) backtrace

# perdite di memoria
valgrind --leak-check=full ./prova', 'bash'),
            P('`-g` aggiunge le informazioni per il debug, `-fsanitize=address` controlla gli accessi alla memoria, `-fsanitize=undefined` gli altri casi di UB. Usali durante lo sviluppo e nei test, perché rallentano il programma. Con `gdb` fermi il programma, avanzi riga per riga e guardi le variabili; `backtrace` mostra la catena di chiamate dopo un crash.', '`-g` adds debug information, `-fsanitize=address` checks memory accesses, `-fsanitize=undefined` the other kinds of UB. Use them during development and in tests, because they slow the program down. With `gdb` you stop the program, step line by line and inspect variables; `backtrace` shows the chain of calls after a crash.'),
        ], [
            Q('db1', L('Quale opzione di gcc attiva AddressSanitizer?', 'Which gcc option enables AddressSanitizer?'), ['-O3', '-fsanitize=address', '-Wall', '-lm'], 1, L('Si abbina bene a `-g`.', 'It pairs well with `-g`.')),
            Q('db2', L('Quale di questi è comportamento indefinito in C?', 'Which of these is undefined behaviour in C?'), [L('Dividere due double', 'Dividing two doubles'), L('Chiamare free(NULL)', 'Calling free(NULL)'), L('Confrontare due puntatori con ==', 'Comparing two pointers with =='), L('Superare INT_MAX in un int con segno', 'Going past INT_MAX in a signed int')], 3, L('`free(NULL)` invece è permesso e non fa nulla.', '`free(NULL)` is allowed and does nothing.')),
            Q('db3', L('Che cosa mostra `backtrace` in gdb?', 'What does `backtrace` show in gdb?'), [L('La catena di chiamate che ha portato fin lì', 'The chain of calls that led there'), L('Il codice assembly', 'The assembly code'), L('Le variabili globali', 'The global variables'), L('L’uso della memoria', 'Memory usage')], 0, L('Utilissimo dopo un crash.', 'Very useful after a crash.')),
        ], task('bash', L('Compila `prova.c` con le informazioni di debug e i sanitizer: `gcc -g -fsanitize=address,undefined -o prova prova.c`.', 'Compile `prova.c` with debug information and the sanitizers: `gcc -g -fsanitize=address,undefined -o prova prova.c`.'), '', [re('gcc\s', L('Usi gcc', 'You use gcc')), re('\s-g(\s|$)', L('Aggiungi -g', 'You add -g')), re('-fsanitize=(address,undefined|undefined,address)', L('Attivi i due sanitizer', 'You enable both sanitizers')), re('-o\s+prova(\s|$)', L('L’eseguibile si chiama prova', 'The executable is called prova')), re('prova\.c', L('Compili prova.c', 'You compile prova.c'))], '', 'gcc -g -fsanitize=address,undefined -o prova prova.c'), [
            dMatch(pMatch(), [['-g', L('Informazioni di debug', 'Debug information')], ['-fsanitize=address', L('Errori di memoria', 'Memory errors')], ['-fsanitize=undefined', L('Altri comportamenti indefiniti', 'Other undefined behaviour')], ['gdb', L('Debugger passo passo', 'Step-by-step debugger')], ['valgrind', L('Perdite di memoria', 'Memory leaks')]], L('La cassetta del debug.', 'The debugging toolbox.')),
            dFlash(pFlash(), [[L('Un programma con UB può sembrare funzionare', 'A program with UB can seem to work'), true], [L('L’overflow di un unsigned è comportamento indefinito', 'Unsigned overflow is undefined behaviour'), false], [L('I sanitizer rallentano il programma', 'Sanitizers slow the program down'), true], [L('Leggere una variabile locale non inizializzata è sicuro', 'Reading an uninitialised local variable is safe'), false]], L('Gli unsigned ricominciano da zero: è definito.', 'Unsigned values wrap around: it is defined.')),
        ]),
        lesson('c23', L('Il C moderno: C23', 'Modern C: C23'), [
            P('Lo standard **C23** (pubblicato come ISO/IEC 9899:2024) porta nel C idee prese dal C++ e dai compilatori. GCC lo usa come predefinito dalla versione 15; con compilatori più vecchi si chiede con `-std=c23` (o `-std=c2x`).', 'The **C23** standard (published as ISO/IEC 9899:2024) brings into C ideas taken from C++ and from compilers. GCC uses it by default from version 15; with older compilers you ask for it with `-std=c23` (or `-std=c2x`).'),
            C('#include <stdio.h>

constexpr int MAX_UTENTI = 100;          // costante vera, nota in compilazione

[[nodiscard]] int calcola(int x) { return x * 2; }

int main(void) {
    bool attivo = true;                  // niente più stdbool.h
    int *p = nullptr;                    // puntatore nullo tipizzato
    auto n = 42;                         // tipo dedotto: int
    typeof(n) m = 7;                     // stesso tipo di n
    int milione = 1\'000\'000;             // separatore delle cifre
    unsigned maschera = 0b1010;          // letterale binario
    int vuoto[4] = {};                   // tutto a zero

    static_assert(sizeof(int) >= 4);
    if (attivo && p == nullptr) {
        printf("%d %d %d %u %d %d\n", n, m, milione, maschera, MAX_UTENTI, vuoto[0]);
    }
    calcola(3);                          // avviso: risultato ignorato
    return 0;
}', 'c'),
            P('Le novità più utili: `bool`, `true` e `false` sono parole chiave; `nullptr` sostituisce `NULL`; `constexpr` crea costanti vere; gli attributi `[[nodiscard]]`, `[[maybe_unused]]` e `[[fallthrough]]` guidano gli avvisi. C’è anche `#embed`, che inserisce i byte di un file direttamente in un array.', 'The most useful additions: `bool`, `true` and `false` are keywords; `nullptr` replaces `NULL`; `constexpr` creates real constants; the attributes `[[nodiscard]]`, `[[maybe_unused]]` and `[[fallthrough]]` steer the warnings. There is also `#embed`, which puts a file’s bytes straight into an array.'),
        ], [
            Q('c23a', L('Che cosa sostituisce `nullptr` nel C23?', 'What does `nullptr` replace in C23?'), ['NULL', '0.0', 'void', 'free'], 0, L('Ha un tipo suo, `nullptr_t`, e non si confonde con un intero.', 'It has its own type, `nullptr_t`, and is not mistaken for an integer.')),
            Q('c23b', L('Che cosa fa `[[nodiscard]]` su una funzione?', 'What does `[[nodiscard]]` do on a function?'), [L('La rende inline', 'Makes it inline'), L('Le impedisce di restituire valori', 'Stops it returning values'), L('Fa avvisare il compilatore se il risultato viene ignorato', 'Makes the compiler warn if the result is ignored'), L('La esegue all’avvio', 'Runs it at start-up')], 2, L('Ottimo per funzioni che restituiscono errori.', 'Great for functions that return errors.')),
            Q('c23c', L('Nel C23 per usare `bool` serve ancora `#include <stdbool.h>`?', 'In C23, do you still need `#include <stdbool.h>` to use `bool`?'), [L('Sì, sempre', 'Yes, always'), L('Solo su Windows', 'Only on Windows'), L('Solo per `false`', 'Only for `false`'), L('No: `bool`, `true` e `false` sono parole chiave', 'No: `bool`, `true` and `false` are keywords')], 3, L('Includerlo resta innocuo.', 'Including it is still harmless.')),
        ], task('c', L('Riscrivi in stile C23: `constexpr int LATO = 8;` al posto del `#define` e `int *p = nullptr;` al posto di `NULL`.', 'Rewrite in C23 style: `constexpr int LATO = 8;` instead of the `#define` and `int *p = nullptr;` instead of `NULL`.'), '#define LATO 8
int *p = NULL;
', [re('constexpr\s+int\s+LATO\s*=\s*8\s*;', L('Costante constexpr', 'constexpr constant')), re('int\s*\*\s*p\s*=\s*nullptr\s*;', L('Puntatore nullo moderno', 'Modern null pointer'))], '', "constexpr int LATO = 8;\nint *p = nullptr;"), [
            dMatch(pMatch(), [['nullptr', L('Puntatore nullo tipizzato', 'Typed null pointer')], ['constexpr', L('Costante nota in compilazione', 'Compile-time constant')], ['typeof', L('Tipo di un’espressione', 'Type of an expression')], ['#embed', L('Inserisce i byte di un file', 'Inserts a file’s bytes')], ['1\'000\'000', L('Separatore delle cifre', 'Digit separator')]], L('Le novità del C23.', 'What is new in C23.')),
            dFlash(pFlash(), [[L('GCC 15 usa il C23 come standard predefinito', 'GCC 15 uses C23 as its default standard'), true], [L('Nel C23 `bool` richiede stdbool.h', 'In C23 `bool` requires stdbool.h'), false], [L('`0b1010` è un letterale binario valido in C23', '`0b1010` is a valid binary literal in C23'), true], [L('In C23 `auto` deduce il tipo dal valore iniziale', 'In C23 `auto` infers the type from the initial value'), true]], L('`bool` è ormai una parola chiave.', '`bool` is now a keyword.')),
        ]),
    ],
];
