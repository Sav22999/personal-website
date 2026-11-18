<?php
/** Algorithms and data structures: Big O complexity, arrays, hash maps, stacks and queues, searching and sorting, recursion, trees and graphs,
 *  dynamic programming and greedy, binary search trees, heaps, two pointers and sliding windows, Dijkstra. */

return [
    'id' => 'algorithms', 'family' => 'algorithms', 'after' => 'python-base',
    'title' => L('Algoritmi e strutture dati', 'Algorithms and Data Structures'), 'short' => L('Algoritmi', 'Algorithms'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Pensare in modo efficiente: complessità O grande, array e mappe, pile e code, ricerca e ordinamento, ricorsione, alberi, heap e grafi, programmazione dinamica, due puntatori e cammini minimi. Esempi in Python.', 'Thinking efficiently: Big O complexity, arrays and maps, stacks and queues, searching and sorting, recursion, trees, heaps and graphs, dynamic programming, two pointers and shortest paths. Examples in Python.'),
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
            P('Gli algoritmi di ordinamento semplici (bubble sort, insertion sort) sono O(n²); quelli efficienti (merge sort, heapsort, quicksort in media, l’ordinamento di Python, il Timsort che dalla versione 3.11 usa la strategia di fusione Powersort) sono O(n log n). Nella pratica usa l’ordinamento della libreria (`sorted`, `list.sort`): è veloce e collaudato.', 'Simple sorting algorithms (bubble sort, insertion sort) are O(n²); efficient ones (merge sort, heapsort, quicksort on average, Python’s sort, the Timsort that since version 3.11 uses the Powersort merge strategy) are O(n log n). In practice use the library sort (`sorted`, `list.sort`): it is fast and battle-tested.'),
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
        lesson('bst', L('Alberi binari di ricerca', 'Binary search trees'), [
            P('In un **albero binario di ricerca** (BST) ogni nodo ha al massimo due figli: i valori **più piccoli a sinistra**, quelli più grandi a destra. Cercare e inserire costa O(log n) se l’albero è **bilanciato**, ma O(n) se degenera in una lista (per esempio inserendo dati già ordinati). Gli alberi bilanciati (AVL, rosso-neri) restano in O(log n), e i database usano i B-tree per gli indici.', 'In a **binary search tree** (BST) each node has at most two children: **smaller values on the left**, larger ones on the right. Searching and inserting cost O(log n) if the tree is **balanced**, but O(n) if it degenerates into a list (for example when inserting already sorted data). Balanced trees (AVL, red-black) stay at O(log n), and databases use B-trees for indexes.'),
            C('class Nodo:
    def __init__(self, valore):
        self.valore, self.sinistra, self.destra = valore, None, None

def inserisci(radice, valore):
    if radice is None:
        return Nodo(valore)
    if valore < radice.valore:
        radice.sinistra = inserisci(radice.sinistra, valore)
    else:
        radice.destra = inserisci(radice.destra, valore)
    return radice

def in_ordine(nodo):                  # sinistra, nodo, destra
    if nodo:
        yield from in_ordine(nodo.sinistra)
        yield nodo.valore
        yield from in_ordine(nodo.destra)

radice = None
for v in [50, 30, 70, 20, 40]:
    radice = inserisci(radice, v)
print(list(in_ordine(radice)))        # [20, 30, 40, 50, 70]', 'python'),
            P('Le visite: **in ordine** (in un BST dà i valori ordinati), **anticipata** (prima il nodo: per copiare un albero), **posticipata** (prima i figli: per calcolare la dimensione delle cartelle) e **per livelli** (la BFS, con una coda).', 'The traversals: **in-order** (in a BST it yields the values sorted), **pre-order** (node first: to copy a tree), **post-order** (children first: to compute folder sizes) and **level-order** (BFS, with a queue).'),
        ], [
            Q('bt1', L('In un BST, dove stanno i valori più piccoli di un nodo?', 'In a BST, where are the values smaller than a node?'), [L('Nel sottoalbero di destra', 'In the right subtree'), L('Nel sottoalbero di sinistra', 'In the left subtree'), L('Sempre nella radice', 'Always in the root'), L('Sparsi nelle foglie a caso', 'Scattered randomly in the leaves')], 1, L('Sinistra minori, destra maggiori.', 'Smaller left, larger right.')),
            Q('bt2', L('Che cosa dà la visita in ordine di un BST?', 'What does an in-order traversal of a BST yield?'), [L('I valori in ordine casuale', 'The values in random order'), L('Solo le foglie', 'Only the leaves'), L('I valori in ordine crescente', 'The values in ascending order'), L('Solo la radice e i suoi figli', 'Only the root and its children')], 2, L('Sinistra, nodo, destra.', 'Left, node, right.')),
            Q('bt3', L('Inserisci 1, 2, 3, 4, 5 in quest’ordine in un BST semplice. Quanto costa poi una ricerca?', 'You insert 1, 2, 3, 4, 5 in this order into a plain BST. What does a search then cost?'), ['O(1)', 'O(log n)', 'O(n log n)', 'O(n)'], 3, L('L’albero è diventato una lista che scende a destra.', 'The tree has become a list going down to the right.')),
        ], task('python', L('Scrivi la funzione ricorsiva `cerca(nodo, valore)`: `False` se il nodo è `None`, `True` se il valore è quello del nodo, altrimenti cerca a sinistra o a destra.', 'Write the recursive function `cerca(nodo, valore)`: `False` if the node is `None`, `True` if the value is the node’s, otherwise search left or right.'), '', [re('def\s+cerca\s*\(\s*nodo\s*,\s*valore\s*\)', L('La funzione', 'The function')), re('if\s+(nodo\s+is\s+None|not\s+nodo)\s*:\s*\n\s+return\s+False', L('Il caso base', 'The base case')), re('cerca\s*\(\s*nodo\.sinistra\s*,\s*valore\s*\)', L('Cerchi a sinistra', 'You search left')), re('cerca\s*\(\s*nodo\.destra\s*,\s*valore\s*\)', L('Cerchi a destra', 'You search right'))], '', "def cerca(nodo, valore):\n    if nodo is None:\n        return False\n    if valore == nodo.valore:\n        return True\n    if valore < nodo.valore:\n        return cerca(nodo.sinistra, valore)\n    return cerca(nodo.destra, valore)"), [
            dMatch(pMatch(), [[L('Radice', 'Root'), L('Il nodo in cima', 'The top node')], [L('Foglia', 'Leaf'), L('Un nodo senza figli', 'A node with no children')], ['BST', L('Minori a sinistra, maggiori a destra', 'Smaller left, larger right')], [L('In ordine', 'In-order'), L('Valori ordinati', 'Sorted values')], [L('Albero bilanciato', 'Balanced tree'), L('Resta in O(log n)', 'Stays at O(log n)')]], L('Il vocabolario degli alberi.', 'Tree vocabulary.')),
            dFlash(pFlash(), [[L('Cercare in un BST costa sempre O(log n)', 'Searching a BST always costs O(log n)'), false], [L('La visita in ordine di un BST dà i valori ordinati', 'An in-order traversal of a BST yields sorted values'), true], [L('Gli indici dei database usano spesso i B-tree', 'Database indexes often use B-trees'), true], [L('La visita anticipata visita i figli prima del nodo', 'Pre-order visits the children before the node'), false]], L('Rami ordinati.', 'Tidy branches.')),
        ]),
        lesson('heaps', L('Heap e code con priorità', 'Heaps and priority queues'), [
            P('Un **heap** (coda con priorità) tiene sempre in cima l’elemento più piccolo (min-heap): leggerlo costa O(1), estrarlo o inserirne uno nuovo O(log n). In Python c’è il modulo `heapq`, che lavora su una normale lista. Serve per i compiti in ordine di urgenza, per i “primi k”, per unire liste ordinate e per l’algoritmo di Dijkstra.', 'A **heap** (priority queue) always keeps the smallest element on top (min-heap): reading it costs O(1), extracting it or inserting a new one O(log n). Python has the `heapq` module, which works on an ordinary list. It is used for tasks in order of urgency, for the “top k”, for merging sorted lists and for Dijkstra’s algorithm.'),
            C('import heapq

compiti = []
heapq.heappush(compiti, (2, "rispondere alle email"))
heapq.heappush(compiti, (1, "correggere il bug in produzione"))
heapq.heappush(compiti, (3, "aggiornare la documentazione"))
print(heapq.heappop(compiti))     # (1, \'correggere il bug in produzione\')

voti = [7, 3, 9, 1, 8, 6]
print(heapq.nlargest(3, voti))    # [9, 8, 7]
print(heapq.nsmallest(2, voti))   # [1, 3]', 'python'),
            P('Ordinare un milione di elementi per prenderne i primi 3 costa O(n log n); con un heap di dimensione k costa O(n log k). `heapq` è un min-heap: per avere prima il massimo si inseriscono i numeri **cambiati di segno**. Le tuple si confrontano campo per campo, quindi la priorità va per prima.', 'Sorting a million items to take the top 3 costs O(n log n); with a heap of size k it costs O(n log k). `heapq` is a min-heap: to get the maximum first you insert the numbers **with their sign flipped**. Tuples are compared field by field, so the priority goes first.'),
        ], [
            Q('hp1', L('Che cosa restituisce `heappop` da un min-heap?', 'What does `heappop` return from a min-heap?'), [L('L’elemento più grande', 'The largest element'), L('L’ultimo elemento inserito', 'The last element inserted'), L('L’elemento più piccolo', 'The smallest element'), L('Un elemento a caso', 'A random element')], 2, L('Il minimo sta sempre in cima.', 'The minimum is always on top.')),
            Q('hp2', L('Quanto costa inserire un elemento in un heap?', 'What does inserting an element into a heap cost?'), ['O(1)', 'O(log n)', 'O(n)', 'O(n log n)'], 1, L('Risale l’albero fino al suo posto.', 'It climbs the tree to its place.')),
            Q('hp3', L('`heapq` è un min-heap. Come ottieni prima il massimo?', '`heapq` is a min-heap. How do you get the maximum first?'), [L('Con la funzione `heapq.reverse`', 'With the `heapq.reverse` function'), L('Ordinando la lista dopo ogni inserimento', 'Sorting the list after every insertion'), L('Non si può fare in Python', 'It cannot be done in Python'), L('Inserendo i numeri cambiati di segno', 'Inserting the numbers with their sign flipped')], 3, L('Il più grande diventa il più piccolo.', 'The largest becomes the smallest.')),
        ], task('python', L('Importa `heapq` e salva in `migliori` i 5 valori più alti della lista `punteggi`, usando `nlargest`.', 'Import `heapq` and store in `migliori` the 5 highest values of the `punteggi` list, using `nlargest`.'), "punteggi = [88, 42, 97, 65, 73, 91, 55, 80]\n", [re('import\s+heapq|from\s+heapq\s+import', L('Importi heapq', 'You import heapq')), re('migliori\s*=\s*(heapq\.)?nlargest\s*\(\s*5\s*,\s*punteggi\s*\)', L('I 5 più alti', 'The top 5'))], '', "import heapq\nmigliori = heapq.nlargest(5, punteggi)"), [
            dMatch(pMatch(), [['heappush', L('Inserisce in O(log n)', 'Inserts in O(log n)')], ['heappop', L('Estrae il minimo', 'Extracts the minimum')], ['nlargest', L('I k più grandi', 'The k largest')], ['Min-heap', L('Il minimo in cima', 'The minimum on top')], [L('Coda con priorità', 'Priority queue'), L('Prima il più urgente', 'Most urgent first')]], L('Prima le cose importanti.', 'Important things first.')),
            dBlanks(pFill(), "import heapq\ncoda = []\nheapq.___(coda, (2, \"email\"))\nprimo = heapq.___(coda)", ['heappush', 'heappop'], ['append', 'pop', 'sort'], L('Si inserisce con heappush e si estrae con heappop.', 'You insert with heappush and extract with heappop.')),
        ]),
        lesson('two-pointers', L('Due puntatori e finestra scorrevole', 'Two pointers and sliding window'), [
            P('Due tecniche frequenti trasformano O(n²) in O(n). Con i **due puntatori**, su una lista ordinata, un indice parte dall’inizio e uno dalla fine e si avvicinano: così trovi una coppia con una certa somma. Con la **finestra scorrevole** guardi k elementi consecutivi e, a ogni passo, aggiungi quello che entra e togli quello che esce, invece di ricalcolare tutto.', 'Two common techniques turn O(n²) into O(n). With **two pointers**, on a sorted list, one index starts at the beginning and one at the end and they move towards each other: that way you find a pair with a given sum. With the **sliding window** you look at k consecutive items and, at each step, add the one coming in and remove the one going out, instead of recomputing everything.'),
            C('def coppia_con_somma(ordinata, obiettivo):   # due puntatori: O(n)
    i, j = 0, len(ordinata) - 1
    while i < j:
        s = ordinata[i] + ordinata[j]
        if s == obiettivo:
            return ordinata[i], ordinata[j]
        if s < obiettivo:
            i += 1
        else:
            j -= 1
    return None

def somma_massima(valori, k):                 # finestra scorrevole: O(n)
    finestra = sum(valori[:k])
    migliore = finestra
    for i in range(k, len(valori)):
        finestra += valori[i] - valori[i - k]
        migliore = max(migliore, finestra)
    return migliore

print(coppia_con_somma([1, 3, 4, 6, 9], 10))  # (1, 9)
print(somma_massima([2, 1, 5, 1, 3, 2], 3))   # 9', 'python'),
            P('Quando un problema parla di “sottoliste consecutive”, “coppia in una lista ordinata” o “sottostringa più lunga senza ripetizioni”, pensa a queste tecniche: una sola passata sui dati invece di due cicli annidati.', 'When a problem talks about “consecutive sublists”, “a pair in a sorted list” or “the longest substring without repeats”, think of these techniques: a single pass over the data instead of two nested loops.'),
        ], [
            Q('tp1', L('Perché la ricerca della coppia con due puntatori vuole una lista ordinata?', 'Why does the two-pointer pair search need a sorted list?'), [L('Perché Python non sa scorrere liste disordinate', 'Because Python cannot loop over unsorted lists'), L('Per poter usare la ricerca binaria dentro il ciclo', 'To be able to use binary search inside the loop'), L('Per sapere quale puntatore spostare in base alla somma', 'To know which pointer to move based on the sum'), L('Per avere meno elementi da controllare', 'To have fewer elements to check')], 2, L('Somma troppo piccola: avanza a sinistra; troppo grande: arretra a destra.', 'Sum too small: move left forward; too large: move right back.')),
            Q('tp2', L('Quanto costa la finestra scorrevole su n elementi?', 'What does the sliding window cost over n items?'), ['O(n·k)', 'O(n²)', 'O(log n)', 'O(n)'], 3, L('Ogni elemento entra ed esce una volta.', 'Each item enters and leaves once.')),
            Q('tp3', L('Nella finestra scorrevole, che cosa fai a ogni passo?', 'In the sliding window, what do you do at each step?'), [L('Ricalcoli la somma di tutta la finestra', 'Recompute the sum of the whole window'), L('Aggiungi l’elemento che entra e togli quello che esce', 'Add the item coming in and remove the one going out'), L('Riordini tutta la lista', 'Re-sort the whole list'), L('Raddoppi la larghezza della finestra', 'Double the window’s width')], 1, L('Da O(k) a O(1) per passo.', 'From O(k) to O(1) per step.')),
        ], task('python', L('Completa il ciclo: aggiorna `finestra` aggiungendo `valori[i]` e togliendo `valori[i - k]`, poi `migliore = max(migliore, finestra)`.', 'Complete the loop: update `finestra` adding `valori[i]` and removing `valori[i - k]`, then `migliore = max(migliore, finestra)`.'), "def somma_massima(valori, k):\n    finestra = sum(valori[:k])\n    migliore = finestra\n    for i in range(k, len(valori)):\n        # aggiorna la finestra e il migliore\n    return migliore", [re('finestra\s*(\+=\s*valori\s*\[\s*i\s*\]\s*-\s*valori\s*\[\s*i\s*-\s*k\s*\]|=\s*finestra\s*\+\s*valori\s*\[\s*i\s*\]\s*-\s*valori\s*\[\s*i\s*-\s*k\s*\])', L('Fai scorrere la finestra', 'You slide the window')), re('migliore\s*=\s*max\s*\(\s*(migliore\s*,\s*finestra|finestra\s*,\s*migliore)\s*\)', L('Tieni il migliore', 'You keep the best'))], '', "        finestra += valori[i] - valori[i - k]\n        migliore = max(migliore, finestra)"), [
            dMatch(pMatch(), [[L('Due puntatori', 'Two pointers'), L('Dagli estremi verso il centro', 'From the ends towards the middle')], [L('Finestra scorrevole', 'Sliding window'), L('Elementi consecutivi', 'Consecutive items')], [L('Lista ordinata', 'Sorted list'), L('Serve ai due puntatori', 'Needed by two pointers')], ['O(n)', L('Una sola passata', 'A single pass')], ['valori[i - k]', L('L’elemento che esce', 'The item going out')]], L('Una passata basta.', 'One pass is enough.')),
            dBug(pBug(), ['while i < j:', '    s = ordinata[i] + ordinata[j]', '    if s < obiettivo:', '        i -= 1', '    else:', '        j -= 1'], 3, '        i += 1', L('Se la somma è piccola il puntatore sinistro deve avanzare.', 'If the sum is small the left pointer must move forward.')),
        ]),
        lesson('dijkstra', L('Cammini minimi: Dijkstra', 'Shortest paths: Dijkstra'), [
            P('In un grafo **pesato** (distanze, tempi, costi) la BFS non basta: il percorso con meno tappe non è sempre il più corto. L’algoritmo di **Dijkstra** trova i cammini minimi da un nodo quando i pesi non sono negativi: estrae ogni volta da un heap il nodo più vicino non ancora sistemato e aggiorna i suoi vicini. Con un heap costa O((V + E) log V).', 'In a **weighted** graph (distances, times, costs) BFS is not enough: the path with fewest stops is not always the shortest. **Dijkstra’s** algorithm finds the shortest paths from a node when weights are not negative: each time it extracts from a heap the nearest node not yet settled and updates its neighbours. With a heap it costs O((V + E) log V).'),
            C('import heapq

def dijkstra(grafo, inizio):
    distanze = {inizio: 0}
    coda = [(0, inizio)]
    while coda:
        d, nodo = heapq.heappop(coda)
        if d > distanze[nodo]:
            continue                      # voce vecchia, già migliorata
        for vicino, peso in grafo[nodo].items():
            nuova = d + peso
            if nuova < distanze.get(vicino, float("inf")):
                distanze[vicino] = nuova
                heapq.heappush(coda, (nuova, vicino))
    return distanze

minuti = {
    "Udine":     {"Pordenone": 50, "Venezia": 130},
    "Pordenone": {"Venezia": 70},
    "Venezia":   {},
}
print(dijkstra(minuti, "Udine"))   # {\'Udine\': 0, \'Pordenone\': 50, \'Venezia\': 120}', 'python'),
            P('Qui la strada diretta per Venezia (130) perde contro quella che passa da Pordenone (50 + 70). Con pesi negativi Dijkstra sbaglia: serve Bellman-Ford. Mappe e navigatori usano varianti come **A***, che aggiungono una stima della distanza mancante per esplorare meno nodi.', 'Here the direct road to Venice (130) loses to the one through Pordenone (50 + 70). With negative weights Dijkstra goes wrong: you need Bellman-Ford. Maps and satnavs use variants such as **A***, which add an estimate of the remaining distance to explore fewer nodes.'),
        ], [
            Q('dj1', L('Perché la BFS non basta su una mappa con i chilometri?', 'Why is BFS not enough on a map with kilometres?'), [L('Perché il percorso con meno tappe non è sempre il più corto', 'Because the path with fewest stops is not always the shortest'), L('Perché la BFS funziona solo sugli alberi', 'Because BFS only works on trees'), L('Perché la BFS non usa una coda', 'Because BFS does not use a queue'), L('Perché le mappe hanno troppi nodi', 'Because maps have too many nodes')], 0, L('La BFS conta i passi, non i pesi.', 'BFS counts steps, not weights.')),
            Q('dj2', L('Quale struttura usa Dijkstra per scegliere il nodo più vicino?', 'Which structure does Dijkstra use to pick the nearest node?'), [L('Una pila', 'A stack'), L('Un set', 'A set'), L('Una coda con priorità (heap)', 'A priority queue (heap)'), L('Una lista riordinata a mano a ogni passo', 'A list re-sorted by hand at every step')], 2, L('Estrarre il minimo costa O(log n).', 'Extracting the minimum costs O(log n).')),
            Q('dj3', L('Quando Dijkstra dà risultati sbagliati?', 'When does Dijkstra give wrong results?'), [L('Con grafi molto grandi', 'With very large graphs'), L('Con nodi chiamati con delle lettere', 'With nodes named with letters'), L('Con grafi senza cicli', 'With graphs without cycles'), L('Con archi di peso negativo', 'With negative-weight edges')], 3, L('Allora serve Bellman-Ford.', 'Then you need Bellman-Ford.')),
        ], task('python', L('Completa l’aggiornamento: se `nuova < distanze.get(vicino, float("inf"))`, salva `distanze[vicino] = nuova` e inserisci `(nuova, vicino)` nell’heap `coda`.', 'Complete the update: if `nuova < distanze.get(vicino, float("inf"))`, store `distanze[vicino] = nuova` and push `(nuova, vicino)` onto the `coda` heap.'), "for vicino, peso in grafo[nodo].items():\n    nuova = d + peso\n    # aggiorna se è migliore\n", [re('if\s+nuova\s*<\s*distanze\.get\s*\(\s*vicino\s*,\s*(float\s*\(\s*["\']inf["\']\s*\)|math\.inf)\s*\)\s*:', L('Confronti le distanze', 'You compare the distances')), re('distanze\s*\[\s*vicino\s*\]\s*=\s*nuova', L('Salvi la nuova distanza', 'You store the new distance')), re('heapq\.heappush\s*\(\s*coda\s*,\s*\(\s*nuova\s*,\s*vicino\s*\)\s*\)', L('Lo rimetti nell’heap', 'You push it onto the heap'))], '', "for vicino, peso in grafo[nodo].items():\n    nuova = d + peso\n    if nuova < distanze.get(vicino, float(\"inf\")):\n        distanze[vicino] = nuova\n        heapq.heappush(coda, (nuova, vicino))"), [
            dMatch(pMatch(), [[L('Peso', 'Weight'), L('Il costo di un arco', 'The cost of an edge')], ['Dijkstra', L('Cammini minimi, pesi non negativi', 'Shortest paths, non-negative weights')], ['Heap', L('Il nodo più vicino', 'The nearest node')], ['Bellman-Ford', L('Ammette pesi negativi', 'Allows negative weights')], ['A*', L('Usa una stima della distanza mancante', 'Uses an estimate of the remaining distance')]], L('Arrivare prima.', 'Getting there first.')),
            dOrder(pSort(), [L('Distanza 0 alla partenza, infinito agli altri', 'Distance 0 at the start, infinity elsewhere'), L('Estrai dall’heap il nodo più vicino', 'Extract the nearest node from the heap'), L('Aggiorna le distanze dei suoi vicini', 'Update its neighbours’ distances'), L('Rimetti nell’heap i vicini migliorati', 'Push the improved neighbours onto the heap'), L('Ripeti finché l’heap è vuoto', 'Repeat until the heap is empty')], L('Il ciclo di Dijkstra.', 'Dijkstra’s loop.')),
        ]),
    ],
];
