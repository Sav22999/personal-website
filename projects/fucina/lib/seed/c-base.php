<?php
/** C basics: compiling and the first program, types and control flow, arrays and strings, pointers and memory. */

return [
    'id' => 'c-base', 'family' => 'c', 'after' => null,
    'title' => L('C base', 'C Basics'), 'short' => L('C base', 'C Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio alla base dei sistemi operativi: compilare, tipi e controllo del flusso, array e stringhe, puntatori e gestione della memoria.', 'The language underneath operating systems: compiling, types and control flow, arrays and strings, pointers and memory management.'),
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
            P('I tipi base sono `int` (interi), `double` (decimali), `char` (un carattere). Le condizioni usano `if`/`else` e `switch`, i cicli `for` e `while`. In C non c’è un vero tipo booleano tradizionale: `0` è falso, ogni altro valore è vero (oppure `bool` da `stdbool.h`).', 'The basic types are `int` (integers), `double` (decimals), `char` (one character). Conditions use `if`/`else` and `switch`, loops `for` and `while`. C traditionally has no real boolean type: `0` is false, any other value is true (or `bool` from `stdbool.h`).'),
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
    ],
];
