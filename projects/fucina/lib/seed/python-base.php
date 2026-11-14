<?php
/** Python basics: values and variables, conditions, loops, lists and functions. */

return [
    'id' => 'python-base', 'family' => 'python', 'after' => null,
    'title' => L('Python base', 'Python Basics'), 'short' => L('Python base', 'Python Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio più amato per iniziare: variabili, condizioni, cicli, liste e funzioni.', 'The best-loved language to start with: variables, conditions, loops, lists and functions.'),
    'lessons' => [
        lesson('hello', L('Ciao, Python', 'Hello, Python'), [
            P('Python si legge quasi come l’inglese. `print()` scrive sullo schermo, una **variabile** nasce quando le dai un valore con `=`. Non servono punti e virgola né tipi da dichiarare.', 'Python reads almost like English. `print()` writes to the screen, a **variable** is born when you give it a value with `=`. No semicolons, no types to declare.'),
            C('nome = "Ada"
eta = 36
print("Ciao", nome)
print(f"{nome} ha {eta} anni")', 'python'),
            P('Le **f-string** (con la `f` davanti) mettono i valori dentro il testo tra graffe. I commenti iniziano con `#`.', '**f-strings** (with an `f` in front) put values inside the text between braces. Comments start with `#`.'),
        ], [
            Q('h1', L('Quale funzione scrive sullo schermo?', 'Which function writes to the screen?'), ['print()', 'echo()', 'console.log()', 'write()'], 0, L('In Python è `print`.', 'In Python it is `print`.')),
            Q('h2', L('Come si inserisce `nome` in una f-string?', 'How do you put `nome` into an f-string?'), ['f"Ciao {nome}"', '"Ciao $nome"', '"Ciao " + {nome}', 'f"Ciao (nome)"'], 0, L('La `f` davanti e le graffe dentro.', 'The `f` in front and braces inside.')),
            Q('h3', L('Con che cosa inizia un commento?', 'What does a comment start with?'), ['#', '//', '--', '/*'], 0, L('Tutto dopo `#` viene ignorato.', 'Everything after `#` is ignored.')),
        ], task('python', L('Crea `citta = "Udine"` e stampa `f"Vivo a {citta}"`.', 'Create `citta = "Udine"` and print `f"Vivo a {citta}"`.'), '', [re('citta\s*=\s*["\']Udine["\']', L('La variabile esiste', 'The variable exists')), re('print\s*\(\s*f["\']Vivo a \{citta\}["\']\s*\)', L('La stampi con una f-string', 'You print it with an f-string'))], '', 'citta = "Udine"
print(f"Vivo a {citta}")'), [
            dBlanks(pFill(), "nome = \"Bea\"\n___(___\"Ciao {nome}!\")", ['print', 'f'], ['echo', 'r', 'log'], L('Funzione e prefisso della f-string.', 'Function and f-string prefix.')),
            dFlash(pFlash(), [[L('In Python le righe finiscono con `;`', 'In Python lines end with `;`'), false], [L('Una variabile nasce con `=`', 'A variable is born with `=`'), true], [L('`#` inizia un commento', '`#` starts a comment'), true], [L('`print` mostra un testo sullo schermo', '`print` shows text on the screen'), true]], L('Niente punti e virgola.', 'No semicolons.')),
        ]),
        lesson('conditions', L('Condizioni e indentazione', 'Conditions and indentation'), [
            P('`if`, `elif` ed `else` scelgono che cosa fare. In Python i blocchi non hanno graffe: li decide l’**indentazione**, di solito 4 spazi dopo i due punti.', '`if`, `elif` and `else` choose what to do. In Python blocks have no braces: **indentation** decides them, usually 4 spaces after the colon.'),
            C('voto = 8
if voto >= 9:
    print("Ottimo")
elif voto >= 6:
    print("Promosso")
else:
    print("Da ripassare")', 'python'),
            P('Gli operatori logici sono parole: `and`, `or`, `not`.', 'Logical operators are words: `and`, `or`, `not`.'),
        ], [
            Q('c1', L('Come si scrive “altrimenti se”?', 'How do you write “else if”?'), ['elif', 'else if', 'elseif', 'elsif'], 0, L('Una parola sola.', 'A single word.')),
            Q('c2', L('Che cosa decide i blocchi in Python?', 'What decides blocks in Python?'), [L('L’indentazione', 'Indentation'), L('Le graffe', 'Braces'), L('Le parentesi', 'Parentheses'), L('I punti e virgola', 'Semicolons')], 0, L('Lo spazio a inizio riga conta.', 'Leading space matters.')),
            Q('c3', L('Come si scrive “e” logico?', 'How do you write logical “and”?'), ['and', '&&', '&', 'AND'], 0, L('Python usa parole.', 'Python uses words.')),
        ], task('python', L('Se `eta >= 18` stampa `"Adulto"`, altrimenti `"Minore"`.', 'If `eta >= 18` print `"Adulto"`, otherwise `"Minore"`.'), 'eta = 20
', [re('if\s+eta\s*>=\s*18\s*:', L('La condizione è giusta', 'The condition is right')), re('print\s*\(\s*["\']Adulto["\']\s*\)', L('Stampi “Adulto”', 'You print “Adulto”')), re('else\s*:\s*\n\s+print\s*\(\s*["\']Minore["\']\s*\)', L('E “Minore” altrimenti', 'And “Minore” otherwise'))], '', 'if eta >= 18:
    print("Adulto")
else:
    print("Minore")'), [
            dBug(pBug(), ['temperatura = 30', 'if temperatura > 25', '    print("Fa caldo")'], 1, 'if temperatura > 25:', L('Dopo la condizione servono i due punti.', 'The condition needs a colon after it.')),
            dMatch(pMatch(), [['and', L('Entrambe vere', 'Both true')], ['or', L('Almeno una vera', 'At least one true')], ['not', L('Il contrario', 'The opposite')], ['==', L('Uguale', 'Equal')], ['!=', L('Diverso', 'Not equal')]], L('Gli operatori di confronto e logici.', 'Comparison and logical operators.')),
        ]),
        lesson('loops', L('Cicli', 'Loops'), [
            P('`for` ripete per ogni elemento di una sequenza; `range(5)` dà i numeri da 0 a 4. `while` ripete finché una condizione è vera.', '`for` repeats for each item of a sequence; `range(5)` gives the numbers from 0 to 4. `while` repeats while a condition is true.'),
            C('for i in range(1, 4):
    print("Giro", i)

n = 3
while n > 0:
    print(n)
    n -= 1
print("Via!")', 'python'),
            P('`break` esce dal ciclo, `continue` salta al giro successivo.', '`break` leaves the loop, `continue` skips to the next round.'),
        ], [
            Q('l1', L('Quali numeri dà `range(3)`?', 'Which numbers does `range(3)` give?'), ['0, 1, 2', '1, 2, 3', '0, 1, 2, 3', '3'], 0, L('Si parte da 0 e ci si ferma prima del 3.', 'It starts at 0 and stops before 3.')),
            Q('l2', L('Quale parola esce subito da un ciclo?', 'Which word leaves a loop at once?'), ['break', 'exit', 'stop', 'return'], 0, L('`continue` salta solo un giro.', '`continue` only skips one round.')),
            Q('l3', L('Che cosa fa `n -= 1`?', 'What does `n -= 1` do?'), [L('Toglie 1 a `n`', 'Takes 1 from `n`'), L('Confronta', 'Compares'), L('Stampa', 'Prints'), L('Niente', 'Nothing')], 0, L('È `n = n - 1`.', 'It is `n = n - 1`.')),
        ], task('python', L('Con `for` e `range(1, 6)` stampa i numeri da 1 a 5.', 'With `for` and `range(1, 6)` print the numbers from 1 to 5.'), '', [re('for\s+\w+\s+in\s+range\s*\(\s*1\s*,\s*6\s*\)\s*:', L('Il ciclo è giusto', 'The loop is right')), re('print\s*\(\s*\w+\s*\)', L('Stampi il numero', 'You print the number'))], '', 'for n in range(1, 6):
    print(n)'), [
            dOrder(pSort(), ['totale = 0', 'for n in range(1, 11):', '    totale += n', 'print(totale)'], L('Prepara, ripeti, somma, mostra.', 'Set up, repeat, add, show.')),
            dFlash(pFlash(), [[L('`range(1, 4)` include il 4', '`range(1, 4)` includes 4'), false], [L('`while` può non finire mai', '`while` may never end'), true], [L('`continue` salta al giro dopo', '`continue` skips to the next round'), true], [L('`for` può scorrere i caratteri di una stringa', '`for` can go through the characters of a string'), true]], L('La fine di `range` è esclusa.', 'The end of `range` is excluded.')),
        ]),
        lesson('lists', L('Liste e dizionari', 'Lists and dictionaries'), [
            P('Una **lista** tiene più valori in ordine: `[3, 5, 7]`. Si legge con l’indice (`lista[0]` è il primo) e si allunga con `append`. Un **dizionario** associa chiavi e valori: `{"nome": "Ada"}`.', 'A **list** holds several values in order: `[3, 5, 7]`. You read it by index (`lista[0]` is the first) and grow it with `append`. A **dictionary** maps keys to values: `{"nome": "Ada"}`.'),
            C('frutta = ["mela", "pera"]
frutta.append("kiwi")
print(frutta[0], len(frutta))

utente = {"nome": "Ada", "eta": 36}
utente["citta"] = "Londra"
for chiave, valore in utente.items():
    print(chiave, valore)', 'python'),
            P('`lista[-1]` è l’ultimo elemento; `in` controlla se qualcosa c’è: `"pera" in frutta`.', '`lista[-1]` is the last item; `in` checks whether something is there: `"pera" in frutta`.'),
        ], [
            Q('d1', L('Come aggiungi un elemento in fondo a una lista?', 'How do you add an item at the end of a list?'), ['append()', 'push()', 'add()', 'insert_end()'], 0, L('`push` è JavaScript.', '`push` is JavaScript.')),
            Q('d2', L('Che cosa dà `lista[-1]`?', 'What does `lista[-1]` give?'), [L('L’ultimo elemento', 'The last item'), L('Un errore', 'An error'), L('Il primo', 'The first'), L('La lunghezza', 'The length')], 0, L('Gli indici negativi contano dalla fine.', 'Negative indexes count from the end.')),
            Q('d3', L('Come scorri chiavi e valori di un dizionario?', 'How do you loop over keys and values of a dict?'), ['for k, v in d.items():', 'for k in d.values():', 'for d in k:', 'foreach d as k => v'], 0, L('`items()` dà le coppie.', '`items()` gives the pairs.')),
        ], task('python', L('Crea `voti = [7, 8]`, aggiungi `9` con `append` e stampa `len(voti)`.', 'Create `voti = [7, 8]`, add `9` with `append` and print `len(voti)`.'), '', [re('voti\s*=\s*\[\s*7\s*,\s*8\s*\]', L('La lista esiste', 'The list exists')), re('voti\.append\s*\(\s*9\s*\)', L('Aggiungi il 9', 'You add 9')), re('print\s*\(\s*len\s*\(\s*voti\s*\)\s*\)', L('Stampi la lunghezza', 'You print the length'))], '', 'voti = [7, 8]
voti.append(9)
print(len(voti))'), [
            dMatch(pMatch(), [['[1, 2]', L('Lista', 'List')], ['{"a": 1}', L('Dizionario', 'Dictionary')], ['(1, 2)', L('Tupla, non si modifica', 'Tuple, cannot change')], ['{1, 2}', L('Insieme, senza doppioni', 'Set, no duplicates')]], L('Le collezioni di Python.', 'Python’s collections.')),
            dBlanks(pFill(), "nomi = [\"Ada\", \"Bea\"]\nnomi.___(\"Cleo\")\nprint(nomi[___])  # Ada\nprint(___(nomi))  # 3", ['append', '0', 'len'], ['push', '1', 'size'], L('Aggiungi, leggi il primo, conta.', 'Add, read the first, count.')),
        ]),
        lesson('functions', L('Funzioni', 'Functions'), [
            P('`def` crea una **funzione**: un pezzo di codice con un nome, che riceve **parametri** e restituisce un risultato con `return`. Un parametro può avere un valore predefinito.', '`def` creates a **function**: a named piece of code that takes **parameters** and gives back a result with `return`. A parameter can have a default value.'),
            C('def area(base, altezza=1):
    return base * altezza

print(area(4, 3))
print(area(5))

def saluta(nome: str) -> str:
    return f"Ciao {nome}"', 'python'),
            P('Le annotazioni come `nome: str` e `-> str` documentano i tipi: Python non le impone, ma gli strumenti le controllano.', 'Annotations such as `nome: str` and `-> str` document the types: Python does not enforce them, but tools check them.'),
        ], [
            Q('f1', L('Quale parola crea una funzione?', 'Which word creates a function?'), ['def', 'function', 'fun', 'fn'], 0, L('Da “define”.', 'From “define”.')),
            Q('f2', L('Che cosa dà `area(5)` con `altezza=1`?', 'What does `area(5)` give with `altezza=1`?'), ['5', '0', L('Un errore', 'An error'), '6'], 0, L('Usa il valore predefinito.', 'It uses the default value.')),
            Q('f3', L('Le annotazioni di tipo sono obbligatorie?', 'Are type annotations required?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo per i numeri', 'Only for numbers'), L('Solo nelle classi', 'Only in classes')], 0, L('Aiutano lettori e strumenti.', 'They help readers and tools.')),
        ], task('python', L('Scrivi `def doppio(n):` che restituisce `n * 2`.', 'Write `def doppio(n):` that returns `n * 2`.'), '', [re('def\s+doppio\s*\(\s*n\s*\)\s*(->\s*\w+\s*)?:', L('La funzione esiste', 'The function exists')), re('return\s+n\s*\*\s*2', L('Restituisce il doppio', 'It returns the double'))], '', 'def doppio(n):
    return n * 2'), [
            dBug(pBug(), ['def media(numeri):', '    totale = sum(numeri)', '    totale / len(numeri)', 'print(media([6, 8]))'], 2, '    return totale / len(numeri)', L('Senza `return` la funzione restituisce `None`.', 'Without `return` the function gives back `None`.')),
            dOrder(pSort(), ['def benvenuto(nome, lingua="it"):', '    if lingua == "it":', '        return f"Ciao {nome}"', '    return f"Hello {nome}"', 'print(benvenuto("Ada"))'], L('Definisci, decidi, restituisci, chiama.', 'Define, decide, return, call.')),
        ]),
    ],
];
