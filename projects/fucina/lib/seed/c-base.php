<?php
/** C basics: compiling and the first program, types and control flow, arrays and strings, pointers and memory, structs and files, functions, input, string.h, enum and typedef. */

return [
    'id' => 'c-base', 'family' => 'c', 'after' => null,
    'title' => L('C base', 'C Basics'), 'short' => L('C base', 'C Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio alla base dei sistemi operativi: compilare, tipi e controllo del flusso, array e stringhe, puntatori e memoria, struct e file, funzioni, input, string.h, enum e typedef.', 'The language underneath operating systems: compiling, types and control flow, arrays and strings, pointers and memory, structs and files, functions, input, string.h, enum and typedef.'),
    'lessons' => [
        lesson('hello', L('Compilare il primo programma', 'Compiling the first program'), [
            P('**C** è del 1972 ed è ancora ovunque: sistemi operativi (Linux), firmware, database, interpreti di altri linguaggi. È vicino all’hardware: veloce e potente, ma ti lascia la responsabilità della memoria. Il codice si **compila** in un eseguibile.', '**C** dates from 1972 and is still everywhere: operating systems (Linux), firmware, databases, interpreters of other languages. It is close to the hardware: fast and powerful, but it leaves memory responsibility to you. Code is **compiled** into an executable.'),
            C('#include <stdio.h>

int main(void) {
    printf("Ciao, mondo!\n");
    return 0;
}', 'c'),
            C('gcc -Wall -Wextra -o ciao ciao.c
./ciao', 'bash'),
            P('`#include` porta dentro le dichiarazioni di una libreria (`stdio.h` per input/output). `main` restituisce `0` se tutto è andato bene. Compila sempre con gli avvisi attivi (`-Wall -Wextra`): ti segnalano molti errori prima che diventino bug.', '`#include` brings in a library’s declarations (`stdio.h` for input/output). `main` returns `0` if all went well. Always compile with warnings on (`-Wall -Wextra`): they flag many mistakes before they become bugs.'),
        ], [
            Q('h1', L('Che cosa fa `#include <stdio.h>`?', 'What does `#include <stdio.h>` do?'), [L('Include le funzioni di input/output', 'Includes input/output functions'), L('Crea un file', 'Creates a file'), L('Avvia il programma', 'Starts the program'), L('Commenta il codice', 'Comments the code')], 0, L('Come printf.', 'Like printf.')),
            Q('h2', L('Che cosa significa `return 0` in main?', 'What does `return 0` in main mean?'), [L('Il programma è terminato con successo', 'The program ended successfully'), L('Errore grave', 'Serious error'), L('Ricomincia', 'Restart'), L('Niente', 'Nothing')], 0, L('Diverso da 0 = errore.', 'Non-zero = error.')),
            Q('h3', L('Perché compilare con -Wall -Wextra?', 'Why compile with -Wall -Wextra?'), [L('Per vedere gli avvisi su possibili errori', 'To see warnings about possible mistakes'), L('Per rendere il programma più lento', 'To make the program slower'), L('Per cifrarlo', 'To encrypt it'), L('Non serve', 'It is not needed')], 0, L('Avvisi gratis.', 'Free warnings.')),
        ], task('c', L('Scrivi un programma che includa `stdio.h` e stampi `Fucina` con `printf`, restituendo `0` da `main`.', 'Write a program that includes `stdio.h` and prints `Fucina` with `printf`, returning `0` from `main`.'), '', [re('#include\s*<stdio\.h>', L('L’include', 'The include')), re('int\s+main\s*\(\s*(void)?\s*\)', L('La funzione main', 'The main function')), re('printf\s*\(\s*"Fucina(\\\\n)?"\s*\)\s*;', L('La stampa', 'The print')), re('return\s+0\s*;', L('Restituisci 0', 'You return 0'))], '', "#include <stdio.h>\n\nint main(void) {\n    printf(\"Fucina\\n\");\n    return 0;\n}"), [
            dOrder(pSort(), ['#include <stdio.h>', 'int main(void) {', '    printf("Ciao\n");', '    return 0;', '}'], L('Include, main, stampa, ritorno.', 'Include, main, print, return.')),
            dMatch(pMatch(), [['#include', L('Include una libreria', 'Includes a library')], ['gcc', L('Il compilatore', 'The compiler')], ['-o ciao', L('Nome dell’eseguibile', 'Executable name')], ['printf', L('Stampa formattata', 'Formatted print')], ['return 0', L('Successo', 'Success')]], L('Dal sorgente all’eseguibile.', 'From source to executable.')),
        ]),
        lesson('types', L('Tipi e controllo del flusso', 'Types and control flow'), [
            P('I tipi base sono `int` (interi), `double` (decimali), `char` (un carattere). Le condizioni usano `if`/`else` e `switch`, i cicli `for` e `while`. Nelle condizioni `0` è falso e ogni altro valore è vero. Per i booleani c’è `bool` con `true` e `false`: dal C23 sono parole chiave, prima serviva `#include <stdbool.h>`.', 'The basic types are `int` (integers), `double` (decimals), `char` (one character). Conditions use `if`/`else` and `switch`, loops `for` and `while`. In conditions `0` is false and any other value is true. For booleans there is `bool` with `true` and `false`: since C23 they are keywords, before you needed `#include <stdbool.h>`.'),
            C('#include <stdio.h>

int quadrato(int n) {
    return n * n;
}

int main(void) {
    for (int i = 1; i <= 5; i++) {
        if (i % 2 == 0) {
            printf("%d è pari, quadrato %d\n", i, quadrato(i));
        } else {
            printf("%d è dispari\n", i);
        }
    }
    return 0;
}', 'c'),
            P('In `printf` i segnaposto dipendono dal tipo: `%d` interi, `%f` decimali, `%c` caratteri, `%s` stringhe. Attenzione alla divisione tra interi: `7 / 2` fa `3`, per avere `3.5` almeno un operando deve essere `double`.', 'In `printf` placeholders depend on the type: `%d` integers, `%f` decimals, `%c` characters, `%s` strings. Watch out for integer division: `7 / 2` is `3`; to get `3.5` at least one operand must be `double`.'),
        ], [
            Q('t1', L('Quanto fa `7 / 2` tra interi in C?', 'What is `7 / 2` between integers in C?'), ['3', '3.5', '4', L('Errore', 'Error')], 0, L('La parte decimale si perde.', 'The decimal part is lost.')),
            Q('t2', L('Quale segnaposto stampa un intero?', 'Which placeholder prints an integer?'), ['%d', '%s', '%f', '%c'], 0, L('d = decimal integer.', 'd = decimal integer.')),
            Q('t3', L('Che cosa è falso in una condizione C?', 'What is false in a C condition?'), ['0', '1', '-1', '42'], 0, L('Tutto il resto è vero.', 'Everything else is true.')),
        ], task('c', L('Scrivi la funzione `int massimo(int a, int b)` che restituisce il maggiore dei due con un `if`.', 'Write the function `int massimo(int a, int b)` returning the larger of the two with an `if`.'), '', [re('int\s+massimo\s*\(\s*int\s+a\s*,\s*int\s+b\s*\)', L('La firma', 'The signature')), re('if\s*\(\s*a\s*>=?\s*b\s*\)[\s\S]*return\s+a\s*;[\s\S]*return\s+b\s*;|if\s*\(\s*b\s*>=?\s*a\s*\)[\s\S]*return\s+b\s*;[\s\S]*return\s+a\s*;', L('Restituisce il maggiore', 'It returns the larger'))], '', "int massimo(int a, int b) {\n    if (a > b) {\n        return a;\n    }\n    return b;\n}"), [
            dMatch(pMatch(), [['int', L('Intero', 'Integer')], ['double', L('Decimale', 'Decimal')], ['char', L('Un carattere', 'One character')], ['%f', L('Stampa un decimale', 'Prints a decimal')], ['%s', L('Stampa una stringa', 'Prints a string')]], L('Tipi e segnaposto.', 'Types and placeholders.')),
            dBug(pBug(), ['double media(int a, int b) {', '    return (a + b) / 2;', '}'], 1, '    return (a + b) / 2.0;', L('Con /2 la divisione è tra interi: usa 2.0.', 'With /2 division is between integers: use 2.0.')),
        ]),
        lesson('arrays', L('Array e stringhe', 'Arrays and strings'), [
            P('Un **array** è una sequenza di elementi dello stesso tipo, di dimensione fissa, con indici da `0`. In C le **stringhe** sono array di `char` che finiscono con il carattere nullo `\0`: per questo una stringa di 5 lettere occupa 6 byte.', 'An **array** is a fixed-size sequence of same-type elements, indexed from `0`. In C **strings** are arrays of `char` ending with the null character `\0`: that is why a 5-letter string takes 6 bytes.'),
            C('#include <stdio.h>
#include <string.h>

int main(void) {
    int voti[5] = {28, 30, 24, 27, 30};
    int somma = 0;
    for (int i = 0; i < 5; i++) somma += voti[i];
    printf("Media: %.1f\n", somma / 5.0);

    char nome[20];
    snprintf(nome, sizeof nome, "%s", "Ada Lovelace");
    printf("%s ha %zu caratteri\n", nome, strlen(nome));
    return 0;
}', 'c'),
            P('C **non controlla i limiti**: leggere o scrivere `voti[5]` in un array di 5 elementi è un errore grave (buffer overflow) che può far crashare il programma o aprire falle di sicurezza. Usa funzioni con la dimensione, come `snprintf`, invece di `strcpy` e `sprintf`.', 'C **does not check bounds**: reading or writing `voti[5]` in a 5-element array is a serious error (buffer overflow) that can crash the program or open security holes. Use size-aware functions like `snprintf` instead of `strcpy` and `sprintf`.'),
        ], [
            Q('a1', L('Qual è l’indice dell’ultimo elemento di `int v[5]`?', 'What is the index of the last element of `int v[5]`?'), ['4', '5', '6', '0'], 0, L('Si parte da 0.', 'It starts from 0.')),
            Q('a2', L('Con che cosa finisce una stringa C?', 'What does a C string end with?'), [L('Con il carattere nullo \0', 'With the null character \0'), L('Con un punto', 'With a full stop'), L('Con \n', 'With \n'), L('Con niente', 'With nothing')], 0, L('Un byte in più.', 'One extra byte.')),
            Q('a3', L('Che cos’è un buffer overflow?', 'What is a buffer overflow?'), [L('Scrivere oltre la fine di un array', 'Writing past the end of an array'), L('Un file troppo grande', 'A file too big'), L('Una rete lenta', 'A slow network'), L('Un tipo di ciclo', 'A kind of loop')], 0, L('C non controlla i limiti.', 'C does not check bounds.')),
        ], task('c', L('Dato `int numeri[4] = {3, 8, 1, 6};`, calcola la somma in `somma` con un ciclo `for` da `0` a `i < 4`.', 'Given `int numeri[4] = {3, 8, 1, 6};`, compute the sum into `somma` with a `for` loop from `0` to `i < 4`.'), "int numeri[4] = {3, 8, 1, 6};\nint somma = 0;\n", [re('for\s*\(\s*int\s+i\s*=\s*0\s*;\s*i\s*<\s*4\s*;\s*i\s*\+\+\s*\)', L('Il ciclo giusto', 'The right loop')), re('somma\s*\+=\s*numeri\s*\[\s*i\s*\]|somma\s*=\s*somma\s*\+\s*numeri\s*\[\s*i\s*\]', L('Sommi gli elementi', 'You add the elements'))], '', "for (int i = 0; i < 4; i++) {\n    somma += numeri[i];\n}"), [
            dBug(pBug(), ['int v[3] = {1, 2, 3};', 'for (int i = 0; i <= 3; i++) {', '    printf("%d\n", v[i]);', '}'], 1, 'for (int i = 0; i < 3; i++) {', L('Con <= leggi oltre la fine dell’array.', 'With <= you read past the array end.')),
            dFlash(pFlash(), [[L('Gli array C partono dall’indice 0', 'C arrays start at index 0'), true], [L('C controlla sempre i limiti degli array', 'C always checks array bounds'), false], [L('"ciao" occupa 5 byte con il terminatore', '"ciao" takes 5 bytes with the terminator'), true], [L('strcpy controlla la dimensione della destinazione', 'strcpy checks the destination size'), false]], L('Array senza rete di sicurezza.', 'Arrays without a safety net.')),
        ]),
        lesson('pointers', L('Puntatori e memoria', 'Pointers and memory'), [
            P('Un **puntatore** contiene l’**indirizzo** di un’altra variabile. `&x` dà l’indirizzo di `x`, `*p` legge o scrive il valore a cui `p` punta. I puntatori permettono a una funzione di modificare le variabili di chi la chiama e di gestire memoria dinamica.', 'A **pointer** holds the **address** of another variable. `&x` gives `x`’s address, `*p` reads or writes the value `p` points to. Pointers let a function modify the caller’s variables and manage dynamic memory.'),
            C('#include <stdlib.h>

void raddoppia(int *n) {
    *n = *n * 2;
}

int main(void) {
    int x = 21;
    raddoppia(&x);            // x ora vale 42

    int *voti = malloc(100 * sizeof *voti);
    if (voti == NULL) return 1;
    voti[0] = 30;
    free(voti);               // restituisci la memoria
    voti = NULL;
    return 0;
}', 'c'),
            P('`malloc` chiede memoria al sistema, `free` la restituisce. Dimenticare `free` causa **memory leak**; usare memoria dopo `free` o un puntatore `NULL` causa crash e falle. Strumenti come Valgrind e AddressSanitizer (`-fsanitize=address`) aiutano a trovare questi errori.', '`malloc` asks the system for memory, `free` returns it. Forgetting `free` causes **memory leaks**; using memory after `free` or a `NULL` pointer causes crashes and holes. Tools like Valgrind and AddressSanitizer (`-fsanitize=address`) help find these mistakes.'),
        ], [
            Q('p1', L('Che cosa dà `&x`?', 'What does `&x` give?'), [L('L’indirizzo di x', 'The address of x'), L('Il valore di x', 'The value of x'), L('x raddoppiato', 'x doubled'), L('Un errore', 'An error')], 0, L('`*p` legge il valore.', '`*p` reads the value.')),
            Q('p2', L('Che cosa succede se dimentichi `free`?', 'What happens if you forget `free`?'), [L('Un memory leak', 'A memory leak'), L('Niente', 'Nothing'), L('Il programma è più veloce', 'The program is faster'), L('Il file si cancella', 'The file is deleted')], 0, L('Memoria persa.', 'Lost memory.')),
            Q('p3', L('Che cosa controllare dopo `malloc`?', 'What should you check after `malloc`?'), [L('Che non abbia restituito NULL', 'That it did not return NULL'), L('Il colore', 'The colour'), L('Niente', 'Nothing'), L('Che sia zero', 'That it is zero')], 0, L('La memoria può finire.', 'Memory can run out.')),
        ], task('c', L('Scrivi `void azzera(int *n)` che imposta a `0` il valore puntato, e chiamala su una variabile `conta` passando il suo indirizzo.', 'Write `void azzera(int *n)` that sets the pointed value to `0`, and call it on a variable `conta` passing its address.'), '', [re('void\s+azzera\s*\(\s*int\s*\*\s*n\s*\)\s*\{\s*\*\s*n\s*=\s*0\s*;\s*\}', L('La funzione', 'The function')), re('azzera\s*\(\s*&\s*conta\s*\)', L('La chiamata con l’indirizzo', 'The call with the address'))], '', "void azzera(int *n) { *n = 0; }\n\nint conta = 5;\nazzera(&conta);"), [
            dMatch(pMatch(), [['&x', L('Indirizzo di x', 'Address of x')], ['*p', L('Valore puntato', 'Pointed value')], ['malloc', L('Chiede memoria', 'Requests memory')], ['free', L('Restituisce memoria', 'Returns memory')], ['NULL', L('Puntatore a niente', 'Pointer to nothing')]], L('Puntatori in C.', 'Pointers in C.')),
            dBug(pBug(), ['int *p = malloc(sizeof *p);', 'free(p);', '*p = 10;'], 2, 'p = NULL;', L('Dopo free la memoria non va più usata.', 'After free the memory must not be used.')),
        ]),
        lesson('structs-files', L('Struct, memoria dinamica e file', 'Structs, dynamic memory and files'), [
            P('Una **struct** raggruppa dati diversi sotto un nome. Con `malloc` chiedi memoria mentre il programma gira; ogni `malloc` va restituito con `free`, e il risultato va sempre controllato: può essere `NULL`.', 'A **struct** groups different data under one name. With `malloc` you request memory while the program runs; every `malloc` must be given back with `free`, and the result must always be checked: it can be `NULL`.'),
            C('#include <stdio.h>
#include <stdlib.h>

struct Punto { int x; int y; };

int main(void) {
    struct Punto *p = malloc(sizeof *p);
    if (p == NULL) return 1;
    p->x = 3; p->y = 4;

    FILE *f = fopen("punto.txt", "w");
    if (f == NULL) { free(p); return 1; }
    fprintf(f, "%d %d\n", p->x, p->y);
    fclose(f);
    free(p);
    return 0;
}', 'c'),
            P('I file si aprono con `fopen` (modalità `"r"`, `"w"`, `"a"`), si scrivono con `fprintf` e si chiudono con `fclose`. Con un puntatore a struct si usa `->` per raggiungere i campi.', 'Files are opened with `fopen` (modes `"r"`, `"w"`, `"a"`), written with `fprintf` and closed with `fclose`. With a pointer to a struct you use `->` to reach the fields.'),
        ], [
            Q('sf1', L('Che cosa restituisce `malloc` se fallisce?', 'What does `malloc` return if it fails?'), ['NULL', '0 byte', '-1', L('Un errore di compilazione', 'A compile error')], 0, L('Controllalo sempre.', 'Always check it.')),
            Q('sf2', L('Che cosa succede senza `free`?', 'What happens without `free`?'), [L('Una perdita di memoria', 'A memory leak'), L('Il programma è più veloce', 'The program is faster'), L('Niente mai', 'Never anything'), L('Il file si chiude', 'The file closes')], 0, L('Memory leak.', 'Memory leak.')),
            Q('sf3', L('Come raggiungi un campo da un puntatore a struct?', 'How do you reach a field from a pointer to a struct?'), ['p->x', 'p.x', 'p::x', 'p[x]'], 0, L('`->` equivale a `(*p).x`.', '`->` equals `(*p).x`.')),
        ], task('c', L('Apri `log.txt` in modalità `"a"` con `fopen`, controlla che non sia `NULL`, scrivi con `fprintf` e chiudi con `fclose`.', 'Open `log.txt` in `"a"` mode with `fopen`, check it is not `NULL`, write with `fprintf` and close with `fclose`.'), '', [re('fopen\(\s*"log\.txt"\s*,\s*"a"\s*\)', L('Apri in aggiunta', 'You open in append mode')), re('==\s*NULL', L('Controlli NULL', 'You check NULL')), re('fprintf\(\s*f\s*,', L('Scrivi nel file', 'You write to the file')), re('fclose\(\s*f\s*\)', L('Chiudi il file', 'You close the file'))], '', "FILE *f = fopen(\"log.txt\", \"a\");\nif (f == NULL) return 1;\nfprintf(f, \"avvio\\n\");\nfclose(f);"), [
            dBug(pBug(), ['FILE *f = fopen("dati.txt", "w");', 'if (f = NULL) return 1;', 'fprintf(f, "ciao");', 'fclose(f);'], 1, 'if (f == NULL) return 1;', L('`=` assegna, `==` confronta: così f diventa NULL.', '`=` assigns, `==` compares: this way f becomes NULL.')),
            dFlash(pFlash(), [[L('Ogni malloc richiede un free', 'Every malloc needs a free'), true], [L('fopen non può mai fallire', 'fopen can never fail'), false], [L('"a" aggiunge in fondo al file', '"a" appends to the end of the file'), true], [L('p.x funziona con un puntatore', 'p.x works with a pointer'), false]], L('Memoria e file.', 'Memory and files.')),
        ]),
        lesson('functions', L('Funzioni, prototipi e scope', 'Functions, prototypes and scope'), [
            P('In C una funzione va dichiarata prima di essere usata: se la definisci dopo `main`, scrivi in alto il suo **prototipo**. Gli argomenti passano **per valore**: la funzione riceve una copia, quindi cambiare un parametro non cambia la variabile di chi chiama (per quello servono i puntatori).', 'In C a function must be declared before it is used: if you define it after `main`, write its **prototype** at the top. Arguments are passed **by value**: the function receives a copy, so changing a parameter does not change the caller’s variable (that is what pointers are for).'),
            C('#include <stdio.h>

double media(int a, int b);        // prototipo

static int chiamate = 0;           // visibile solo in questo file

void prova(int n) {
    n = 100;                       // cambia solo la copia
    chiamate++;
}

int main(void) {
    int x = 5;
    prova(x);
    printf("x = %d, media = %.1f\n", x, media(3, 4));   // x = 5, media = 3.5
    return 0;
}

double media(int a, int b) {
    return (a + b) / 2.0;
}', 'c'),
            P('Una variabile dichiarata dentro una funzione è **locale**: nasce e muore con la chiamata. Una variabile `static` dentro una funzione conserva il valore tra una chiamata e l’altra; fuori dalle funzioni, `static` la rende privata del file. `void` tra le parentesi, come in `main(void)`, vuol dire “nessun parametro”.', 'A variable declared inside a function is **local**: it is born and dies with the call. A `static` variable inside a function keeps its value between calls; outside functions, `static` makes it private to the file. `void` between the brackets, as in `main(void)`, means “no parameters”.'),
        ], [
            Q('fn1', L('Se `prova` assegna 100 al suo parametro, quanto vale `x` dopo `prova(x)`?', 'If `prova` assigns 100 to its parameter, what is `x` after `prova(x)`?'), ['100', '5', '0', L('Un valore casuale', 'A random value')], 1, L('La funzione ha cambiato solo la sua copia.', 'The function changed only its own copy.')),
            Q('fn2', L('A che cosa serve un prototipo come `double media(int a, int b);`?', 'What is a prototype like `double media(int a, int b);` for?'), [L('A dichiarare la funzione prima di usarla', 'Declaring the function before using it'), L('A renderla più veloce', 'Making it faster'), L('A renderla privata del file', 'Making it private to the file'), L('A eseguirla all’avvio', 'Running it at start-up')], 0, L('Così il compilatore conosce tipi e parametri.', 'That way the compiler knows its types and parameters.')),
            Q('fn3', L('Che cosa fa `static` davanti a una variabile locale?', 'What does `static` do in front of a local variable?'), [L('La rende costante', 'Makes it constant'), L('La mette nello heap', 'Puts it on the heap'), L('La rende visibile a tutto il programma', 'Makes it visible to the whole program'), L('Le fa conservare il valore tra una chiamata e l’altra', 'Makes it keep its value between calls')], 3, L('Viene inizializzata una volta sola.', 'It is initialised only once.')),
        ], task('c', L('Scrivi il prototipo `int somma(int a, int b);` prima di `main` e, dopo, la definizione che restituisce `a + b`.', 'Write the prototype `int somma(int a, int b);` before `main` and, after it, the definition returning `a + b`.'), '#include <stdio.h>

int main(void) {
    printf("%d\n", somma(2, 3));
    return 0;
}
', [re('int\s+somma\s*\(\s*int\s+a\s*,\s*int\s+b\s*\)\s*;', L('C’è il prototipo', 'There is the prototype')), re('int\s+somma\s*\(\s*int\s+a\s*,\s*int\s+b\s*\)\s*\{\s*return\s+a\s*\+\s*b\s*;\s*\}', L('C’è la definizione', 'There is the definition'))], '', "#include <stdio.h>\n\nint somma(int a, int b);\n\nint main(void) {\n    printf(\"%d\\n\", somma(2, 3));\n    return 0;\n}\n\nint somma(int a, int b) {\n    return a + b;\n}"), [
            dMatch(pMatch(), [[L('Prototipo', 'Prototype'), L('Dichiarazione prima dell’uso', 'Declaration before use')], [L('Per valore', 'By value'), L('La funzione riceve una copia', 'The function gets a copy')], [L('static (locale)', 'static (local)'), L('Conserva il valore tra le chiamate', 'Keeps its value between calls')], [L('static (globale)', 'static (global)'), L('Visibile solo nel file', 'Visible only in the file')], ['void', L('Nessun parametro o nessun risultato', 'No parameters or no result')]], L('Funzioni in C.', 'Functions in C.')),
            dFlash(pFlash(), [[L('In C gli argomenti passano per valore', 'In C arguments are passed by value'), true], [L('Una variabile locale sopravvive alla fine della funzione', 'A local variable outlives the end of the function'), false], [L('Un prototipo finisce con il punto e virgola', 'A prototype ends with a semicolon'), true], [L('Per modificare una variabile di chi chiama serve un puntatore', 'To change a caller’s variable you need a pointer'), true]], L('Le variabili locali muoiono con la chiamata.', 'Local variables die with the call.')),
        ]),
        lesson('input', L('Leggere l’input', 'Reading input'), [
            P('Per leggere una riga dalla tastiera il modo sicuro è `fgets`, che non scrive mai oltre la dimensione del buffer. Poi si converte il testo: `strtol` per gli interi, `strtod` per i decimali, controllando che la conversione sia riuscita.', 'To read a line from the keyboard the safe way is `fgets`, which never writes past the buffer size. Then you convert the text: `strtol` for integers, `strtod` for decimals, checking that the conversion succeeded.'),
            C('#include <stdio.h>
#include <stdlib.h>
#include <string.h>

int main(void) {
    char riga[64];
    printf("Quanti anni hai? ");
    if (fgets(riga, sizeof riga, stdin) == NULL) return 1;
    riga[strcspn(riga, "\n")] = \'\0\';          // toglie l’a capo

    char *fine;
    long anni = strtol(riga, &fine, 10);
    if (fine == riga || *fine != \'\0\') {
        printf("\"%s\" non è un numero\n", riga);
        return 1;
    }
    printf("Tra dieci anni ne avrai %ld\n", anni + 10);
    return 0;
}', 'c'),
            P('`scanf("%d", &n)` è più breve ma fragile: lascia l’a capo nel buffer, segnala male gli errori e con `%s` senza limite può traboccare. Se lo usi, controlla sempre il valore restituito (quanti campi ha letto) e metti un limite, come `%63s`.', '`scanf("%d", &n)` is shorter but fragile: it leaves the newline in the buffer, reports errors poorly and with an unbounded `%s` it can overflow. If you use it, always check its return value (how many fields it read) and set a limit, such as `%63s`.'),
        ], [
            Q('in1', L('Perché si usa `fgets` e non `gets`?', 'Why use `fgets` and not `gets`?'), [L('`fgets` è più veloce', '`fgets` is faster'), L('`gets` legge solo numeri', '`gets` reads only numbers'), L('`gets` non conosce la dimensione del buffer ed è stata tolta dal linguaggio', '`gets` does not know the buffer size and was removed from the language'), L('Non c’è nessuna differenza', 'There is no difference at all')], 2, L('`gets` è sparita con il C11.', '`gets` was removed in C11.')),
            Q('in2', L('Che cosa restituisce `scanf("%d", &n)` se l’utente scrive «ciao»?', 'What does `scanf("%d", &n)` return if the user types “ciao”?'), ['0', '1', '-1', L('Il numero 0 messo in n', 'The number 0 stored in n')], 0, L('Nessun campo letto; `n` resta com’era.', 'No field read; `n` stays as it was.')),
            Q('in3', L('A che cosa serve il secondo argomento di `strtol(riga, &fine, 10)`?', 'What is the second argument of `strtol(riga, &fine, 10)` for?'), [L('A indicare la base del numero', 'Giving the number’s base'), L('A dire dove si è fermata la conversione', 'Telling where the conversion stopped'), L('A fissare la lunghezza massima', 'Setting the maximum length'), L('A ignorare gli spazi', 'Ignoring spaces')], 1, L('La base è il terzo argomento, 10.', 'The base is the third argument, 10.')),
        ], task('c', L('Leggi una riga in `nome` con `fgets(nome, sizeof nome, stdin)` e controlla che non restituisca `NULL`.', 'Read a line into `nome` with `fgets(nome, sizeof nome, stdin)` and check it does not return `NULL`.'), 'char nome[32];
', [re('fgets\s*\(\s*nome\s*,\s*(sizeof\s*\(?\s*nome\s*\)?|32)\s*,\s*stdin\s*\)', L('Leggi con un limite', 'You read with a limit')), re('(==|!=)\s*NULL|NULL\s*(==|!=)|if\s*\(\s*!\s*fgets', L('Controlli il risultato', 'You check the result'))], '', 'char nome[32];
if (fgets(nome, sizeof nome, stdin) == NULL) return 1;'), [
            dOrder(pSort(), ['char riga[16];', 'if (fgets(riga, sizeof riga, stdin) == NULL) return 1;', 'long n = strtol(riga, NULL, 10);', 'printf("%ld\n", n * 2);'], L('Buffer, lettura, conversione, uso.', 'Buffer, reading, conversion, use.')),
            dMatch(pMatch(), [['fgets', L('Legge una riga con un limite', 'Reads a line with a limit')], ['strtol', L('Testo in intero', 'Text to integer')], ['strtod', L('Testo in decimale', 'Text to decimal')], ['stdin', L('Input standard', 'Standard input')], ['%63s', L('Lettura con un limite', 'Bounded read')]], L('Leggere in sicurezza.', 'Reading safely.')),
        ]),
        lesson('strings', L('Le funzioni di string.h', 'The string.h functions'), [
            P('`<string.h>` offre le funzioni per le stringhe C: `strlen` (lunghezza senza il terminatore), `strcmp` (confronto), `strchr` e `strstr` (ricerca), `memcpy` e `memset`. Due stringhe non si confrontano con `==`, che confronta gli indirizzi: si usa `strcmp`, che restituisce `0` se sono uguali.', '`<string.h>` offers the functions for C strings: `strlen` (length without the terminator), `strcmp` (comparison), `strchr` and `strstr` (search), `memcpy` and `memset`. Two strings are not compared with `==`, which compares addresses: you use `strcmp`, which returns `0` when they are equal.'),
            C('#include <stdio.h>
#include <string.h>
#include <ctype.h>

int main(void) {
    char nome[32] = "Ada";
    if (strcmp(nome, "Ada") == 0) printf("Uguali\n");

    char saluto[64];
    snprintf(saluto, sizeof saluto, "Ciao, %s!", nome);   // compone in sicurezza

    char *virgola = strchr(saluto, \',\');
    if (virgola != NULL) printf("Virgola alla posizione %td\n", virgola - saluto);

    for (size_t i = 0; saluto[i] != \'\0\'; i++) {
        saluto[i] = (char)toupper((unsigned char)saluto[i]);
    }
    printf("%s (%zu caratteri)\n", saluto, strlen(saluto));
    return 0;
}', 'c'),
            P('`strlen` conta i byte fino al `\0`, non la capacità dell’array. Per comporre o copiare testo `snprintf` è la scelta più sicura: non supera mai la dimensione indicata e termina sempre la stringa. `toupper` e `isdigit` (da `<ctype.h>`) lavorano su un carattere alla volta.', '`strlen` counts the bytes up to `\0`, not the array’s capacity. To compose or copy text `snprintf` is the safest choice: it never exceeds the given size and always terminates the string. `toupper` and `isdigit` (from `<ctype.h>`) work on one character at a time.'),
        ], [
            Q('ss1', L('Come controlli se due stringhe C sono uguali?', 'How do you check whether two C strings are equal?'), ['a == b', 'strcmp(a, b) == 0', 'a.equals(b)', 'strlen(a) == strlen(b)'], 1, L('`==` confronta gli indirizzi, non il testo.', '`==` compares addresses, not the text.')),
            Q('ss2', L('Quanto vale `strlen("ciao")`?', 'What is `strlen("ciao")`?'), ['5', '3', '4', L('Dipende dall’array', 'It depends on the array')], 2, L('Il terminatore non viene contato.', 'The terminator is not counted.')),
            Q('ss3', L('Che cosa restituisce `strchr` se il carattere non c’è?', 'What does `strchr` return if the character is not there?'), ['-1', '0', L('La lunghezza della stringa', 'The string’s length'), 'NULL'], 3, L('Restituisce un puntatore: `NULL` se non trova nulla.', 'It returns a pointer: `NULL` when nothing is found.')),
        ], task('c', L('Confronta `risposta` con `"si"` usando `strcmp` e stampa `OK` se sono uguali.', 'Compare `risposta` with `"si"` using `strcmp` and print `OK` if they are equal.'), 'char risposta[8] = "si";
', [re('if\s*\(\s*(strcmp\s*\(\s*risposta\s*,\s*"si"\s*\)\s*==\s*0|!\s*strcmp\s*\(\s*risposta\s*,\s*"si"\s*\))\s*\)', L('Confronti con strcmp', 'You compare with strcmp')), re('printf\s*\(\s*"OK(\\\\n)?"\s*\)', L('Stampi OK', 'You print OK'))], '', 'if (strcmp(risposta, "si") == 0) printf("OK\n");'), [
            dBug(pBug(), ['char a[] = "pane";', 'char b[] = "pane";', 'if (a == b) printf("Uguali\n");'], 2, 'if (strcmp(a, b) == 0) printf("Uguali\n");', L('`a == b` confronta due indirizzi diversi: è sempre falso.', '`a == b` compares two different addresses: it is always false.')),
            dMatch(pMatch(), [['strlen', L('Lunghezza senza terminatore', 'Length without terminator')], ['strcmp', L('Confronta, 0 se uguali', 'Compares, 0 if equal')], ['strchr', L('Cerca un carattere', 'Finds a character')], ['strstr', L('Cerca una sottostringa', 'Finds a substring')], ['snprintf', L('Compone con un limite', 'Composes with a limit')]], L('La cassetta di string.h.', 'The string.h toolbox.')),
        ]),
        lesson('enums-typedef', L('enum, typedef e union', 'enum, typedef and union'), [
            P('`enum` dà un nome a un gruppo di costanti intere. `typedef` crea un nome nuovo per un tipo, molto usato con le struct per non riscrivere `struct` ogni volta. Una `union` mette più campi nella **stessa** memoria: se ne usa uno alla volta.', '`enum` names a group of integer constants. `typedef` creates a new name for a type, often used with structs so you need not write `struct` every time. A `union` puts several fields in the **same** memory: you use one at a time.'),
            C('#include <stdio.h>

typedef enum { ROSSO, GIALLO, VERDE } Semaforo;

typedef struct {
    char nome[32];
    int livello;
} Giocatore;

const char *descrivi(Semaforo s) {
    switch (s) {
        case ROSSO:  return "fermo";
        case GIALLO: return "attenzione";
        case VERDE:  return "vai";
    }
    return "?";
}

int main(void) {
    Giocatore g = { .nome = "Ada", .livello = 3 };
    Semaforo s = VERDE;
    printf("%s, livello %d: %s\n", g.nome, g.livello, descrivi(s));
    return 0;
}', 'c'),
            P('I valori di un enum partono da 0 (`ROSSO` vale 0, `GIALLO` 1…) ma puoi sceglierli tu: `enum { KB = 1024 }`. Gli **inizializzatori designati** (`.nome = "Ada"`) indicano i campi per nome: più chiari e indipendenti dall’ordine. Con `-Wall`, uno `switch` su un enum che dimentica un valore produce un avviso.', 'Enum values start at 0 (`ROSSO` is 0, `GIALLO` 1…) but you can choose them: `enum { KB = 1024 }`. **Designated initialisers** (`.nome = "Ada"`) name the fields: clearer and independent of order. With `-Wall`, a `switch` on an enum that forgets a value produces a warning.'),
        ], [
            Q('et1', L('Quanto vale `GIALLO` in `enum { ROSSO, GIALLO, VERDE }`?', 'What is `GIALLO` in `enum { ROSSO, GIALLO, VERDE }`?'), ['0', '1', '2', L('La stringa «GIALLO»', 'The string “GIALLO”')], 1, L('Si conta da 0.', 'Counting starts at 0.')),
            Q('et2', L('A che cosa serve `typedef struct { … } Giocatore;`?', 'What is `typedef struct { … } Giocatore;` for?'), [L('A usare `Giocatore` come nome del tipo, senza scrivere `struct`', 'Using `Giocatore` as the type name, without writing `struct`'), L('A creare una variabile globale', 'Creating a global variable'), L('A copiare una struct', 'Copying a struct'), L('A rendere costante la struct', 'Making the struct constant')], 0, L('È solo un nuovo nome per il tipo.', 'It is just a new name for the type.')),
            Q('et3', L('Che cosa ha di speciale una `union`?', 'What is special about a `union`?'), [L('Può contenere solo interi', 'It can hold only integers'), L('Si libera da sola', 'It frees itself'), L('È sempre più grande di una struct', 'It is always larger than a struct'), L('Tutti i campi condividono la stessa memoria', 'All fields share the same memory')], 3, L('È grande quanto il campo più grande.', 'It is as large as its largest field.')),
        ], task('c', L('Definisci `typedef struct { int x; int y; } Punto;`.', 'Define `typedef struct { int x; int y; } Punto;`.'), '', [re('typedef\s+struct\s*(\w+\s*)?\{\s*int\s+x\s*(;\s*int\s+|,\s*)y\s*;\s*\}\s*Punto\s*;', L('Il tipo Punto esiste', 'The Punto type exists'))], '', 'typedef struct {
    int x;
    int y;
} Punto;'), [
            dBlanks(pFill(), "typedef ___ { PICCOLO, MEDIO, GRANDE } Taglia;\ntypedef ___ {\n    char nome[20];\n    Taglia taglia;\n} Maglia;\nMaglia m = { ___ = \"Blu\", .taglia = MEDIO };", ['enum', 'struct', '.nome'], ['define', 'class', 'nome:'], L('Enum, struct, inizializzatore designato.', 'Enum, struct, designated initialiser.')),
            dFlash(pFlash(), [[L('Il primo valore di un enum vale 0 se non indicato', 'An enum’s first value is 0 unless given'), true], [L('`typedef` crea un tipo nuovo con regole diverse', '`typedef` creates a new type with different rules'), false], [L('Gli inizializzatori designati scelgono i campi per nome', 'Designated initialisers pick fields by name'), true], [L('In una union si usa un campo alla volta', 'In a union you use one field at a time'), true]], L('`typedef` dà solo un altro nome a un tipo esistente.', '`typedef` only gives another name to an existing type.')),
        ]),
    ],
];
