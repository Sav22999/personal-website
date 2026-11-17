<?php
/** Python basics: values and variables, conditions, loops, lists and functions, strings, input and types, tuples and sets, built-in functions. */

return [
    'id' => 'python-base', 'family' => 'python', 'after' => null,
    'title' => L('Python base', 'Python Basics'), 'short' => L('Python base', 'Python Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio più amato per iniziare: variabili, condizioni, cicli, liste e funzioni, stringhe, input e tipi, tuple e insiemi, funzioni integrate.', 'The best-loved language to start with: variables, conditions, loops, lists and functions, strings, input and types, tuples and sets, built-in functions.'),
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
        lesson('strings', L('Stringhe e slicing', 'Strings and slicing'), [
            P('Le stringhe hanno tanti metodi pronti: `strip` toglie gli spazi ai lati, `upper` e `lower` cambiano le maiuscole, `replace` sostituisce, `split` divide in una lista e `join` riunisce. Con lo **slicing** `testo[inizio:fine]` prendi un pezzo di stringa.', 'Strings have many ready-made methods: `strip` trims spaces at the ends, `upper` and `lower` change case, `replace` substitutes, `split` breaks into a list and `join` puts it back together. With **slicing** `text[start:end]` you take a piece of a string.'),
            C('frase = "  Ciao, Mondo!  "
pulita = frase.strip()           # "Ciao, Mondo!"
print(pulita.upper())            # CIAO, MONDO!
print(pulita.replace("Mondo", "Ada"))
print(pulita[0:4])               # Ciao
print(pulita[-6:])               # Mondo!
print(len(pulita))               # 12

parole = "a,b,c".split(",")      # ["a", "b", "c"]
print("-".join(parole))          # a-b-c', 'python'),
            P('Le stringhe sono **immutabili**: i metodi non le cambiano, ne restituiscono una nuova. Nello slicing l’inizio è incluso e la fine esclusa; gli indici negativi contano dalla fine.', 'Strings are **immutable**: methods do not change them, they return a new one. In slicing the start is included and the end excluded; negative indexes count from the end.'),
        ], [
            Q('s1', L('Che cosa dà `"Python"[0:2]`?', 'What does `"Python"[0:2]` give?'), ['"Py"', '"Pyt"', '"yt"', '"P"'], 0, L('Le posizioni 0 e 1: la 2 è esclusa.', 'Positions 0 and 1: 2 is excluded.')),
            Q('s2', L('Che cosa fa `" ciao ".strip()`?', 'What does `" ciao ".strip()` do?'), [L('Divide la stringa in parole', 'Splits the string into words'), L('Mette tutto in maiuscolo', 'Uppercases everything'), L('Toglie gli spazi all’inizio e alla fine', 'Removes spaces at the start and end'), L('Conta gli spazi presenti', 'Counts the spaces')], 2, L('Utile per pulire ciò che scrive l’utente.', 'Handy for cleaning what the user types.')),
            Q('s3', L('Che cosa dà `"a-b".split("-")`?', 'What does `"a-b".split("-")` give?'), ['"ab"', '["a", "b"]', '("a", "b")', '["a-b"]'], 1, L('Una lista con i pezzi tra i separatori.', 'A list with the pieces between separators.')),
        ], task('python', L('Da `nome` ottieni in `pulito` il testo senza spazi ai lati e con le iniziali maiuscole, usando `strip()` e `title()`.', 'From `nome` get into `pulito` the text without spaces at the ends and with capitalised initials, using `strip()` and `title()`.'), 'nome = "  ada lovelace "
', [re('pulito\s*=\s*nome\.strip\s*\(\s*\)\.title\s*\(\s*\)|pulito\s*=\s*nome\.title\s*\(\s*\)\.strip\s*\(\s*\)', L('Pulisci e metti le iniziali maiuscole', 'You trim and capitalise'))], '', 'pulito = nome.strip().title()'), [
            dMatch(pMatch(), [['upper()', L('Tutto maiuscolo', 'All uppercase')], ['strip()', L('Toglie gli spazi ai lati', 'Trims spaces at the ends')], ['split()', L('Divide in una lista', 'Splits into a list')], ['join()', L('Unisce una lista', 'Joins a list')], ['replace()', L('Sostituisce', 'Replaces')]], L('I metodi più usati delle stringhe.', 'The most used string methods.')),
            dFlash(pFlash(), [[L('`"ciao"[1]` è `"i"`', '`"ciao"[1]` is `"i"`'), true], [L('Una stringa si può modificare carattere per carattere', 'A string can be changed character by character'), false], [L('`"abc"[-1]` è `"c"`', '`"abc"[-1]` is `"c"`'), true], [L('`s[0:3]` include il carattere in posizione 3', '`s[0:3]` includes the character at position 3'), false]], L('Inizio incluso, fine esclusa.', 'Start included, end excluded.')),
        ]),
        lesson('input-types', L('Input e conversioni di tipo', 'Input and type conversion'), [
            P('`input()` mostra una domanda e aspetta che l’utente scriva. Restituisce sempre una **stringa**: per fare calcoli va convertita con `int()` o `float()`. `type()` dice di che tipo è un valore.', '`input()` shows a question and waits for the user to type. It always returns a **string**: to do maths it must be converted with `int()` or `float()`. `type()` tells you the type of a value.'),
            C('nome = input("Come ti chiami? ")
anni = int(input("Quanti anni hai? "))
altezza = float("1.75")
print(type(anni))                 # <class \'int\'>
print(str(anni) + " anni")

print(10 / 4, 10 // 4, 10 % 4)    # 2.5 2 2
print(bool(0), bool("ciao"))      # False True', 'python'),
            P('`/` dà sempre un decimale, `//` la divisione intera, `%` il resto. Sommare una stringa e un numero dà `TypeError`: Python non converte da solo. Se il testo non è un numero, `int()` lancia `ValueError`.', '`/` always gives a decimal, `//` integer division, `%` the remainder. Adding a string and a number raises `TypeError`: Python does not convert by itself. If the text is not a number, `int()` raises `ValueError`.'),
        ], [
            Q('i1', L('Che tipo restituisce `input()`?', 'What type does `input()` return?'), ['int', 'str', 'float', 'bool'], 1, L('Anche se l’utente scrive un numero.', 'Even if the user types a number.')),
            Q('i2', L('Quanto fa `7 // 2`?', 'What is `7 // 2`?'), ['3.5', '4', '3', '1'], 2, L('Divisione intera: la parte decimale sparisce.', 'Integer division: the decimal part goes away.')),
            Q('i3', L('Che cosa succede con `"5" + 3`?', 'What happens with `"5" + 3`?'), [L('Dà 8', 'It gives 8'), L('Dà "53"', 'It gives "53"'), L('Dà 5.3', 'It gives 5.3'), L('Un errore TypeError', 'A TypeError')], 3, L('Prima converti: `int("5") + 3`.', 'Convert first: `int("5") + 3`.')),
        ], task('python', L('Leggi un numero con `input`, convertilo con `int` in `n` e stampa il suo doppio.', 'Read a number with `input`, convert it with `int` into `n` and print its double.'), '', [re('n\s*=\s*int\s*\(\s*input\s*\(', L('Leggi e converti', 'You read and convert')), re('print\s*\(\s*(n\s*\*\s*2|2\s*\*\s*n)\s*\)', L('Stampi il doppio', 'You print the double'))], '', 'n = int(input("Numero: "))
print(n * 2)'), [
            dMatch(pMatch(), [['int("4")', '4'], ['float("2.5")', '2.5'], ['str(7)', '"7"'], ['10 // 3', '3'], ['10 % 3', '1']], L('Conversioni e divisioni.', 'Conversions and divisions.')),
            dBug(pBug(), ['prezzo = "9.5"', 'totale = int(prezzo) * 2', 'print(totale)'], 1, 'totale = float(prezzo) * 2', L('`int("9.5")` dà `ValueError`: per i decimali serve `float`.', '`int("9.5")` raises `ValueError`: decimals need `float`.')),
        ]),
        lesson('tuples-sets', L('Tuple, insiemi e spacchettamento', 'Tuples, sets and unpacking'), [
            P('Una **tupla** `(3, 4)` è come una lista ma non si modifica: perfetta per valori che stanno insieme, come coordinate. Con lo **spacchettamento** `x, y = punto` assegni ogni elemento a una variabile. Un **insieme** (`set`) non ha doppioni né ordine.', 'A **tuple** `(3, 4)` is like a list but cannot change: perfect for values that belong together, such as coordinates. With **unpacking** `x, y = point` you assign each item to a variable. A **set** has no duplicates and no order.'),
            C('punto = (3, 4)
x, y = punto                      # spacchettamento
a, b = 1, 2
a, b = b, a                       # scambio: a vale 2, b vale 1

colori = {"rosso", "verde", "rosso"}
print(len(colori))                # 2: niente doppioni
colori.add("blu")
print("verde" in colori)          # True

pari = {2, 4, 6}
piccoli = {1, 2, 3}
print(pari & piccoli)             # {2}
print(pari | piccoli)             # {1, 2, 3, 4, 6}', 'python'),
            P('Controllare se un elemento è in un insieme è velocissimo, anche con milioni di elementi. Attenzione: `{}` crea un **dizionario** vuoto; l’insieme vuoto si scrive `set()`.', 'Checking whether an item is in a set is very fast, even with millions of items. Watch out: `{}` creates an empty **dictionary**; the empty set is written `set()`.'),
        ], [
            Q('u1', L('Che cosa crea `{}`?', 'What does `{}` create?'), [L('Un insieme vuoto', 'An empty set'), L('Un dizionario vuoto', 'An empty dictionary'), L('Una tupla vuota', 'An empty tuple'), L('Un errore', 'An error')], 1, L('Per l’insieme vuoto serve `set()`.', 'The empty set needs `set()`.')),
            Q('u2', L('Dopo `a, b = 1, 2` e `a, b = b, a`, quanto vale `a`?', 'After `a, b = 1, 2` and `a, b = b, a`, what is `a`?'), ['(2, 1)', '1', 'None', '2'], 3, L('Le due variabili si scambiano i valori.', 'The two variables swap values.')),
            Q('u3', L('Che cosa dà `len({1, 1, 2})`?', 'What does `len({1, 1, 2})` give?'), ['3', '1', '2', '0'], 2, L('Il doppione 1 conta una volta sola.', 'The duplicate 1 counts only once.')),
        ], task('python', L('Togli i doppioni da `nomi` trasformandola nell’insieme `unici`, poi stampa quanti sono con `len`.', 'Remove duplicates from `nomi` by turning it into the set `unici`, then print how many there are with `len`.'), 'nomi = ["Ada", "Bea", "Ada", "Cleo"]
', [re('unici\s*=\s*set\s*\(\s*nomi\s*\)', L('Crei l’insieme', 'You create the set')), re('print\s*\(\s*len\s*\(\s*unici\s*\)\s*\)', L('Stampi quanti sono', 'You print how many'))], '', 'unici = set(nomi)
print(len(unici))'), [
            dBlanks(pFill(), 'citta = ("Udine", "Trieste")
prima, seconda = ___
visti = ___()
visti.___("Udine")', ['citta', 'set', 'add'], ['tuple', 'dict', 'append'], L('Spacchetta, crea un insieme vuoto, aggiungi.', 'Unpack, create an empty set, add.')),
            dFlash(pFlash(), [[L('Una tupla si può modificare dopo la creazione', 'A tuple can be changed after creation'), false], [L('Un set non contiene doppioni', 'A set holds no duplicates'), true], [L('`set()` crea un insieme vuoto', '`set()` creates an empty set'), true], [L('Un set mantiene l’ordine di inserimento', 'A set keeps insertion order'), false]], L('Tuple fisse, insiemi senza doppioni.', 'Fixed tuples, sets without duplicates.')),
        ]),
        lesson('builtins', L('Funzioni integrate utili', 'Handy built-in functions'), [
            P('Python ha funzioni pronte per i compiti di tutti i giorni: `sum`, `min`, `max` riassumono una lista, `sorted` la ordina, `enumerate` dà indice e valore insieme, `zip` scorre più liste in parallelo, `any` e `all` controllano una condizione su tutti gli elementi.', 'Python has ready-made functions for everyday jobs: `sum`, `min`, `max` summarise a list, `sorted` sorts it, `enumerate` gives index and value together, `zip` walks several lists side by side, `any` and `all` check a condition on every item.'),
            C('voti = [7, 9, 6, 8]
print(sum(voti), min(voti), max(voti))   # 30 6 9
print(sorted(voti))                      # [6, 7, 8, 9]
print(sorted(voti, reverse=True))        # [9, 8, 7, 6]

for i, voto in enumerate(voti, start=1):
    print(i, voto)

nomi = ["Ada", "Bea"]
eta = [36, 28]
for nome, anni in zip(nomi, eta):
    print(nome, anni)

print(round(3.14159, 2), abs(-3))        # 3.14 3
print(any(v < 6 for v in voti), all(v >= 6 for v in voti))   # False True', 'python'),
            P('`sorted` restituisce una **lista nuova**, mentre `lista.sort()` ordina quella esistente e restituisce `None`. Prima di scrivere un ciclo, chiediti se c’è già una funzione integrata che lo fa.', '`sorted` returns a **new list**, while `list.sort()` sorts the existing one and returns `None`. Before writing a loop, ask yourself whether a built-in function already does it.'),
        ], [
            Q('b1', L('Che cosa fa `sorted(voti)`?', 'What does `sorted(voti)` do?'), [L('Ordina voti e restituisce None', 'Sorts voti and returns None'), L('Restituisce una nuova lista ordinata', 'Returns a new sorted list'), L('Restituisce il voto più alto', 'Returns the highest mark'), L('Mescola la lista a caso', 'Shuffles the list randomly')], 1, L('La lista originale non cambia.', 'The original list does not change.')),
            Q('b2', L('Che cosa produce `enumerate(["a", "b"])`?', 'What does `enumerate(["a", "b"])` produce?'), [L('Le coppie (0, "a") e (1, "b")', 'The pairs (0, "a") and (1, "b")'), L('Il numero 2', 'The number 2'), L('Le lettere in maiuscolo', 'The letters in uppercase'), L('La coppia ("a", "b")', 'The pair ("a", "b")')], 0, L('Indice e valore, partendo da 0 se non dici altro.', 'Index and value, starting at 0 unless told otherwise.')),
            Q('b3', L('Che cosa fa `zip(nomi, eta)`?', 'What does `zip(nomi, eta)` do?'), [L('Comprime le liste in un file', 'Compresses the lists into a file'), L('Unisce le liste una dopo l’altra', 'Joins the lists one after the other'), L('Ordina le due liste insieme', 'Sorts both lists together'), L('Abbina gli elementi nella stessa posizione', 'Pairs the items at the same position')], 3, L('Il primo nome con la prima età, e così via.', 'The first name with the first age, and so on.')),
        ], task('python', L('Con `enumerate` stampa ogni elemento di `spesa` preceduto dal suo numero, partendo da 1.', 'With `enumerate` print each item of `spesa` preceded by its number, starting from 1.'), 'spesa = ["pane", "latte", "uova"]
', [re('for\s+(\w+)\s*,\s*(\w+)\s+in\s+enumerate\s*\(\s*spesa\s*,\s*(start\s*=\s*)?1\s*\)\s*:', L('Usi enumerate da 1', 'You use enumerate from 1')), re('print\s*\(', L('Stampi ogni riga', 'You print each line'))], '', 'for i, cosa in enumerate(spesa, start=1):
    print(i, cosa)'), [
            dMatch(pMatch(), [['sum', L('Somma', 'Sum')], ['max', L('Il più grande', 'The largest')], ['sorted', L('Nuova lista ordinata', 'New sorted list')], ['enumerate', L('Indice e valore', 'Index and value')], ['zip', L('Liste in parallelo', 'Lists side by side')]], L('Funzioni integrate da conoscere.', 'Built-in functions worth knowing.')),
            dBug(pBug(), ['numeri = [3, 1, 2]', 'ordinati = numeri.sort()', 'print(ordinati[0])'], 1, 'ordinati = sorted(numeri)', L('`sort()` ordina sul posto e restituisce `None`.', '`sort()` sorts in place and returns `None`.')),
        ]),
    ],
];
