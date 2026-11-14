<?php
/** Algorithms and data structures: Big O complexity, arrays, hash maps, stacks and queues, searching and sorting, recursion, trees and graphs. */

return [
    'id' => 'algorithms', 'family' => 'algorithms', 'after' => 'python-base',
    'title' => L('Algoritmi e strutture dati', 'Algorithms and Data Structures'), 'short' => L('Algoritmi', 'Algorithms'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Pensare in modo efficiente: complessità O grande, array e mappe, pile e code, ricerca e ordinamento, ricorsione, alberi e grafi. Esempi in Python.', 'Thinking efficiently: Big O complexity, arrays and maps, stacks and queues, searching and sorting, recursion, trees and graphs. Examples in Python.'),
    'lessons' => [
        lesson('bigo', L('Complessità: la notazione O', 'Complexity: Big O notation'), [
            P('La **notazione O grande** descrive come cresce il tempo (o la memoria) di un algoritmo quando cresce l’input `n`. Non conta i secondi esatti, ma l’andamento: raddoppiando i dati, il tempo resta uguale, raddoppia o esplode?', '**Big O notation** describes how an algorithm’s time (or memory) grows as the input `n` grows. It does not count exact seconds, but the trend: doubling the data, does time stay the same, double or explode?'),
            C('O(1)        costante      leggere lista[5], cercare in un dizionario
O(log n)    logaritmica   ricerca binaria in una lista ordinata
O(n)        lineare       scorrere tutta la lista una volta
O(n log n)  quasi lineare buoni algoritmi di ordinamento
O(n²)       quadratica    due cicli annidati sugli stessi dati
O(2ⁿ)       esponenziale  provare tutte le combinazioni', 'text'),
            P('Con un milione di elementi, O(n²) significa mille miliardi di passi: la differenza tra un millisecondo e un’ora. Spesso basta scegliere la struttura dati giusta per passare da O(n²) a O(n).', 'With a million items, O(n²) means a trillion steps: the difference between a millisecond and an hour. Often just picking the right data structure takes you from O(n²) to O(n).'),
        ], [
            Q('o1', L('Che complessità hanno due cicli annidati sugli stessi n dati?', 'What complexity do two nested loops over the same n items have?'), ['O(n²)', 'O(n)', 'O(1)', 'O(log n)'], 0, L('n volte n.', 'n times n.')),
            Q('o2', L('Che complessità ha la ricerca binaria?', 'What complexity does binary search have?'), ['O(log n)', 'O(n)', 'O(n²)', 'O(2ⁿ)'], 0, L('Dimezza a ogni passo.', 'It halves each step.')),
            Q('o3', L('Che cosa descrive la notazione O?', 'What does Big O describe?'), [L('Come cresce il costo al crescere dell’input', 'How cost grows as input grows'), L('I secondi esatti', 'Exact seconds'), L('Il linguaggio usato', 'The language used'), L('La memoria del PC', 'The PC’s memory')], 0, L('L’andamento, non il tempo preciso.', 'The trend, not precise time.')),
        ], task('text', L('Indica la complessità di ciascuna operazione, una per riga come `operazione: O(...)`: `leggere un elemento per indice`, `sommare tutti gli elementi`, `confrontare ogni elemento con ogni altro`.', 'Give the complexity of each operation, one per line as `operation: O(...)`: `read an element by index`, `sum all elements`, `compare every element with every other`.'), '', [re('(indice|index)[^:\n]*:\s*O\(1\)', L('Per indice: O(1)', 'By index: O(1)')), re('(somm|sum)[^:\n]*:\s*O\(n\)', L('Somma: O(n)', 'Sum: O(n)')), re('(confront|compar)[^:\n]*:\s*O\(n(²|\^2|\*n)\)', L('Tutti con tutti: O(n²)', 'All with all: O(n²)'))], '', "leggere un elemento per indice: O(1)\nsommare tutti gli elementi: O(n)\nconfrontare ogni elemento con ogni altro: O(n²)"), [
            dOrder(pSort(), ['O(1)', 'O(log n)', 'O(n)', 'O(n log n)', 'O(n²)'], L('Dalla più veloce alla più lenta.', 'From fastest to slowest.')),
            dFlash(pFlash(), [[L('O(log n) cresce più lentamente di O(n)', 'O(log n) grows more slowly than O(n)'), true], [L('O(n²) è sempre veloce con milioni di dati', 'O(n²) is always fast with millions of items'), false], [L('Leggere da un dizionario è in media O(1)', 'Reading from a dict is O(1) on average'), true], [L('La notazione O misura i secondi esatti', 'Big O measures exact seconds'), false]], L('Pensare in grande.', 'Thinking big.')),
        ]),
        lesson('structures', L('Strutture dati fondamentali', 'Core data structures'), [
            P('Le strutture dati sono il modo di organizzare le informazioni. Le più usate: **array/lista** (accesso per indice O(1), ricerca O(n)), **hash map/dizionario** (ricerca per chiave in media O(1)), **set** (appartenenza in O(1)), **pila** (l’ultimo entrato esce per primo, LIFO) e **coda** (il primo entrato esce per primo, FIFO).', 'Data structures are how information is organised. The most used: **array/list** (index access O(1), search O(n)), **hash map/dictionary** (key lookup O(1) on average), **set** (membership O(1)), **stack** (last in, first out, LIFO) and **queue** (first in, first out, FIFO).'),
            C('# trovare doppioni: O(n²) con due cicli, O(n) con un set
def ha_doppioni(lista):
    visti = set()
    for x in lista:
        if x in visti:
            return True
        visti.add(x)
    return False

from collections import deque
coda = deque(); coda.append("a"); coda.popleft()   # FIFO
pila = []; pila.append(1); pila.pop()              # LIFO', 'python'),
            P('Una pila serve per “annulla”, parentesi bilanciate e chiamate di funzione; una coda per elaborare richieste nell’ordine di arrivo. Il trucco del set qui sopra è uno dei più utili: trasforma ricerche lente in istantanee.', 'A stack is used for “undo”, balanced brackets and function calls; a queue for processing requests in arrival order. The set trick above is one of the most useful: it turns slow lookups into instant ones.'),
        ], [
            Q('s1', L('Come funziona una pila?', 'How does a stack work?'), [L('L’ultimo entrato esce per primo', 'Last in, first out'), L('Il primo entrato esce per primo', 'First in, first out'), L('A caso', 'At random'), L('Per ordine alfabetico', 'Alphabetically')], 0, L('LIFO, come una pila di piatti.', 'LIFO, like a stack of plates.')),
            Q('s2', L('Quanto costa in media `x in set`?', 'What does `x in set` cost on average?'), ['O(1)', 'O(n)', 'O(n²)', 'O(log n)'], 0, L('Hash.', 'Hashing.')),
            Q('s3', L('Quale struttura per servire richieste nell’ordine di arrivo?', 'Which structure serves requests in arrival order?'), [L('Una coda', 'A queue'), L('Una pila', 'A stack'), L('Un set', 'A set'), L('Un albero', 'A tree')], 0, L('FIFO.', 'FIFO.')),
        ], task('python', L('Scrivi `conta_parole(testo)` che restituisce un dizionario parola → numero di volte, usando un `dict` e `testo.split()`.', 'Write `conta_parole(testo)` returning a dict word → count, using a `dict` and `testo.split()`.'), '', [re('def\s+conta_parole\s*\(\s*testo\s*\)', L('La funzione', 'The function')), re('for\s+(\w+)\s+in\s+testo\.split\s*\(\s*\)', L('Scorri le parole', 'You loop over the words')), re('\[\s*\w+\s*\]\s*=\s*\w+\.get\s*\(\s*\w+\s*,\s*0\s*\)\s*\+\s*1|\[\s*\w+\s*\]\s*\+=\s*1', L('Conti con il dizionario', 'You count with the dict')), re('return\s+\w+', L('Restituisci il risultato', 'You return the result'))], '', "def conta_parole(testo):\n    conteggi = {}\n    for p in testo.split():\n        conteggi[p] = conteggi.get(p, 0) + 1\n    return conteggi"), [
            dMatch(pMatch(), [[L('Lista', 'List'), L('Accesso per indice', 'Index access')], [L('Dizionario', 'Dictionary'), L('Ricerca per chiave', 'Key lookup')], ['Set', L('Appartenenza veloce', 'Fast membership')], [L('Pila', 'Stack'), 'LIFO'], [L('Coda', 'Queue'), 'FIFO']], L('Le strutture di base.', 'Core structures.')),
            dFlash(pFlash(), [[L('Un set aiuta a trovare doppioni in O(n)', 'A set helps find duplicates in O(n)'), true], [L('Cercare in una lista non ordinata è O(1)', 'Searching an unsorted list is O(1)'), false], [L('La funzione annulla usa spesso una pila', 'Undo often uses a stack'), true], [L('Una coda è LIFO', 'A queue is LIFO'), false]], L('Organizzare per velocità.', 'Organising for speed.')),
        ]),
        lesson('search-sort', L('Ricerca e ordinamento', 'Searching and sorting'), [
            P('La **ricerca lineare** guarda gli elementi uno per uno: O(n). La **ricerca binaria**, su dati **ordinati**, guarda il centro e scarta metà dei dati a ogni passo: O(log n), cioè circa 20 passi per un milione di elementi.', '**Linear search** looks at items one by one: O(n). **Binary search**, on **sorted** data, looks at the middle and discards half the data each step: O(log n), about 20 steps for a million items.'),
            C('def ricerca_binaria(lista, obiettivo):
    basso, alto = 0, len(lista) - 1
    while basso <= alto:
        medio = (basso + alto) // 2
        if lista[medio] == obiettivo:
            return medio
        if lista[medio] < obiettivo:
            basso = medio + 1
        else:
            alto = medio - 1
    return -1', 'python'),
            P('Gli algoritmi di ordinamento semplici (bubble sort, insertion sort) sono O(n²); quelli efficienti (merge sort, quicksort, il Timsort di Python) sono O(n log n). Nella pratica usa l’ordinamento della libreria (`sorted`, `list.sort`): è veloce e collaudato.', 'Simple sorting algorithms (bubble sort, insertion sort) are O(n²); efficient ones (merge sort, quicksort, Python’s Timsort) are O(n log n). In practice use the library sort (`sorted`, `list.sort`): it is fast and battle-tested.'),
        ], [
            Q('b1', L('Che cosa serve per la ricerca binaria?', 'What does binary search need?'), [L('Dati ordinati', 'Sorted data'), L('Dati casuali', 'Random data'), L('Un database', 'A database'), L('Niente', 'Nothing')], 0, L('Altrimenti non si può scartare metà.', 'Otherwise you cannot discard half.')),
            Q('b2', L('Quanti passi circa per un milione di elementi con la ricerca binaria?', 'About how many steps for a million items with binary search?'), ['20', '1000', '1.000.000', '2'], 0, L('2²⁰ ≈ un milione.', '2²⁰ ≈ one million.')),
            Q('b3', L('Che complessità ha un buon algoritmo di ordinamento?', 'What complexity does a good sorting algorithm have?'), ['O(n log n)', 'O(n²)', 'O(1)', 'O(2ⁿ)'], 0, L('Come merge sort.', 'Like merge sort.')),
        ], task('python', L('Completa la ricerca binaria: se `lista[medio] < obiettivo` sposta `basso = medio + 1`, altrimenti `alto = medio - 1`.', 'Complete binary search: if `lista[medio] < obiettivo` move `basso = medio + 1`, otherwise `alto = medio - 1`.'), "while basso <= alto:\n    medio = (basso + alto) // 2\n    if lista[medio] == obiettivo:\n        return medio\n    ", [re('if\s+lista\s*\[\s*medio\s*\]\s*<\s*obiettivo\s*:\s*\n\s+basso\s*=\s*medio\s*\+\s*1', L('Vai a destra', 'Go right')), re('else\s*:\s*\n\s+alto\s*=\s*medio\s*-\s*1', L('Altrimenti a sinistra', 'Otherwise go left'))], '', "if lista[medio] < obiettivo:\n        basso = medio + 1\n    else:\n        alto = medio - 1"), [
            dMatch(pMatch(), [[L('Ricerca lineare', 'Linear search'), 'O(n)'], [L('Ricerca binaria', 'Binary search'), 'O(log n)'], ['Bubble sort', 'O(n²)'], ['Merge sort', 'O(n log n)'], ['sorted()', L('Ordinamento della libreria', 'Library sort')]], L('Cercare e ordinare.', 'Searching and sorting.')),
            dBug(pBug(), ['while basso <= alto:', '    medio = (basso + alto) // 2', '    if lista[medio] < obiettivo:', '        basso = medio', '    else:', '        alto = medio - 1'], 3, '        basso = medio + 1', L('Senza +1 il ciclo può non finire mai.', 'Without +1 the loop may never end.')),
        ]),
        lesson('recursion', L('Ricorsione, alberi e grafi', 'Recursion, trees and graphs'), [
            P('Una funzione **ricorsiva** chiama sé stessa su un problema più piccolo, fino a un **caso base** che si risolve subito. È naturale per strutture annidate come cartelle e **alberi**. Senza caso base la ricorsione non finisce e il programma va in errore.', 'A **recursive** function calls itself on a smaller problem, down to a **base case** solved at once. It is natural for nested structures like folders and **trees**. Without a base case recursion never ends and the program errors out.'),
            C('def fattoriale(n):
    if n <= 1:            # caso base
        return 1
    return n * fattoriale(n - 1)

from collections import deque
def bfs(grafo, inizio):   # visita in ampiezza: trova i percorsi più brevi
    visitati, coda = {inizio}, deque([inizio])
    while coda:
        nodo = coda.popleft()
        for vicino in grafo[nodo]:
            if vicino not in visitati:
                visitati.add(vicino)
                coda.append(vicino)
    return visitati', 'python'),
            P('Un **grafo** è fatto di nodi collegati da archi: reti stradali, amicizie, dipendenze tra pacchetti. La **visita in ampiezza** (BFS) esplora livello per livello con una coda e trova i percorsi con meno passi; la **visita in profondità** (DFS) va il più lontano possibile, spesso con la ricorsione.', 'A **graph** is made of nodes connected by edges: road networks, friendships, package dependencies. **Breadth-first search** (BFS) explores level by level with a queue and finds paths with fewest steps; **depth-first search** (DFS) goes as far as possible, often with recursion.'),
        ], [
            Q('r1', L('Che cos’è il caso base?', 'What is the base case?'), [L('Il caso che si risolve senza ricorsione', 'The case solved without recursion'), L('Il primo argomento', 'The first argument'), L('Un errore', 'An error'), L('Il caso più difficile', 'The hardest case')], 0, L('Ferma la ricorsione.', 'It stops recursion.')),
            Q('r2', L('Quale visita trova il percorso con meno passi?', 'Which search finds the path with fewest steps?'), [L('In ampiezza (BFS)', 'Breadth-first (BFS)'), L('In profondità (DFS)', 'Depth-first (DFS)'), L('A caso', 'Random'), L('Nessuna', 'None')], 0, L('Livello per livello.', 'Level by level.')),
            Q('r3', L('Quale di questi è un grafo?', 'Which of these is a graph?'), [L('Una rete di amicizie', 'A friendship network'), L('Un numero intero', 'An integer'), L('Una singola parola', 'A single word'), L('Un colore', 'A colour')], 0, L('Nodi e archi.', 'Nodes and edges.')),
        ], task('python', L('Scrivi la funzione ricorsiva `somma(n)` che restituisce `0` se `n == 0` e altrimenti `n + somma(n - 1)`.', 'Write the recursive function `somma(n)` returning `0` if `n == 0` and otherwise `n + somma(n - 1)`.'), '', [re('def\s+somma\s*\(\s*n\s*\)', L('La funzione', 'The function')), re('if\s+n\s*(==|<=)\s*0\s*:\s*\n\s+return\s+0', L('Il caso base', 'The base case')), re('return\s+n\s*\+\s*somma\s*\(\s*n\s*-\s*1\s*\)', L('La chiamata ricorsiva', 'The recursive call'))], '', "def somma(n):\n    if n == 0:\n        return 0\n    return n + somma(n - 1)"), [
            dMatch(pMatch(), [[L('Ricorsione', 'Recursion'), L('Funzione che chiama sé stessa', 'Function calling itself')], [L('Caso base', 'Base case'), L('Ferma la ricorsione', 'Stops recursion')], [L('Albero', 'Tree'), L('Struttura gerarchica', 'Hierarchical structure')], [L('Grafo', 'Graph'), L('Nodi e archi', 'Nodes and edges')], ['BFS', L('Percorsi più brevi', 'Shortest paths')]], L('Strutture annidate.', 'Nested structures.')),
            dBug(pBug(), ['def conto_alla_rovescia(n):', '    print(n)', '    conto_alla_rovescia(n - 1)'], 2, '    if n > 0: conto_alla_rovescia(n - 1)', L('Senza caso base la ricorsione non finisce.', 'Without a base case recursion never ends.')),
        ]),
        lesson('dp-greedy', L('Programmazione dinamica e algoritmi greedy', 'Dynamic programming and greedy algorithms'), [
            P('La **programmazione dinamica** risolve problemi che si ripetono in sottoproblemi uguali: salva i risultati già calcolati (**memoizzazione**) invece di rifarli. Fibonacci ricorsivo ingenuo è esponenziale; con una cache diventa lineare.', '**Dynamic programming** solves problems that repeat into identical subproblems: it saves results already computed (**memoisation**) instead of redoing them. Naive recursive Fibonacci is exponential; with a cache it becomes linear.'),
            C('const memo = new Map();
function fib(n) {
  if (n < 2) return n;
  if (memo.has(n)) return memo.get(n);
  const r = fib(n - 1) + fib(n - 2);
  memo.set(n, r);
  return r;
}', 'js'),
            P('Un algoritmo **greedy** (goloso) sceglie a ogni passo l’opzione migliore **in quel momento**. È semplice e veloce, ma non sempre ottimo: per dare il resto con monete da 1, 3 e 4, il greedy su 6 dà 4+1+1 (tre monete) invece di 3+3 (due).', 'A **greedy** algorithm picks the best option **at that moment** at each step. It is simple and fast, but not always optimal: to give change with coins of 1, 3 and 4, greedy on 6 gives 4+1+1 (three coins) instead of 3+3 (two).'),
        ], [
            Q('dp1', L('Che cosa fa la memoizzazione?', 'What does memoisation do?'), [L('Salva i risultati già calcolati', 'Saves results already computed'), L('Ordina i dati', 'Sorts data'), L('Cancella la ricorsione', 'Deletes recursion'), L('Usa meno variabili', 'Uses fewer variables')], 0, L('Niente calcoli ripetuti.', 'No repeated work.')),
            Q('dp2', L('Fibonacci con memoizzazione diventa…', 'Fibonacci with memoisation becomes…'), ['O(n)', 'O(2ⁿ)', 'O(n²)', 'O(1)'], 0, L('Ogni valore si calcola una volta.', 'Each value is computed once.')),
            Q('dp3', L('Un algoritmo greedy è sempre ottimo?', 'Is a greedy algorithm always optimal?'), [L('No, dipende dal problema', 'No, it depends on the problem'), L('Sì, sempre', 'Yes, always'), L('Solo con i numeri pari', 'Only with even numbers'), L('Solo se ricorsivo', 'Only if recursive')], 0, L('Vedi l’esempio delle monete.', 'See the coin example.')),
        ], task('js', L('In `fib`, prima di calcolare, restituisci `memo.get(n)` se `memo.has(n)`.', 'In `fib`, before computing, return `memo.get(n)` if `memo.has(n)`.'), '', [re('if\s*\(\s*memo\.has\(\s*n\s*\)\s*\)\s*\{?\s*return\s+memo\.get\(\s*n\s*\)', L('Usi la cache', 'You use the cache'))], '', 'if (memo.has(n)) return memo.get(n);'), [
            dOrder(pSort(), ['function fib(n) {', '  if (n < 2) return n;', '  if (memo.has(n)) return memo.get(n);', '  const r = fib(n - 1) + fib(n - 2);', '  memo.set(n, r);', '  return r;', '}'], L('Caso base, cache, calcolo, salvataggio.', 'Base case, cache, compute, save.')),
            dFlash(pFlash(), [[L('La programmazione dinamica riusa i sottoproblemi', 'Dynamic programming reuses subproblems'), true], [L('Il greedy trova sempre l’ottimo', 'Greedy always finds the optimum'), false], [L('Fibonacci ingenuo è esponenziale', 'Naive Fibonacci is exponential'), true], [L('La memoizzazione rende il codice più lento', 'Memoisation makes code slower'), false]], L('Strategie di soluzione.', 'Solving strategies.')),
        ]),
    ],
];
