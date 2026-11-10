<?php
/** Python intermediate: comprehensions, files, errors, classes and modules. */

return [
    'id' => 'python-intermediate', 'family' => 'python', 'after' => 'python-base',
    'title' => L('Python intermedio', 'Intermediate Python'), 'short' => L('Python intermedio', 'Python Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Comprehension, file, eccezioni, classi, moduli e ambienti virtuali: Python per progetti veri.', 'Comprehensions, files, exceptions, classes, modules and virtual environments: Python for real projects.'),
    'lessons' => [
        lesson('comprehensions', L('Comprehension', 'Comprehensions'), [
            P('Una **list comprehension** costruisce una lista in una riga: `[espressione for x in sequenza if condizione]`. Esistono anche per dizionari e insiemi.', 'A **list comprehension** builds a list in one line: `[expression for x in sequence if condition]`. They also exist for dicts and sets.'),
            C('numeri = [1, 2, 3, 4, 5, 6]
pari = [n for n in numeri if n % 2 == 0]
quadrati = {n: n * n for n in numeri}
nomi = ["ada", "bea"]
maiuscoli = [n.capitalize() for n in nomi]', 'python'),
            P('Sono più brevi e spesso più veloci di un ciclo con `append`, ma se diventano lunghe meglio tornare al `for`.', 'They are shorter and often faster than a loop with `append`, but if they get long go back to a `for`.'),
        ], [
            Q('c1', L('Che cosa dà `[n * 2 for n in [1, 2]]`?', 'What does `[n * 2 for n in [1, 2]]` give?'), ['[2, 4]', '[1, 2, 1, 2]', '[1, 4]', '4'], 0, L('Ogni elemento raddoppiato.', 'Each item doubled.')),
            Q('c2', L('Dove va il filtro in una comprehension?', 'Where does the filter go in a comprehension?'), [L('In fondo, con `if`', 'At the end, with `if`'), L('All’inizio', 'At the start'), L('Fuori dalle parentesi', 'Outside the brackets'), L('Non si può filtrare', 'You cannot filter')], 0, L('`[x for x in l if cond]`.', '`[x for x in l if cond]`.')),
            Q('c3', L('Con che parentesi si fa una dict comprehension?', 'Which brackets make a dict comprehension?'), ['{k: v for …}', '[k: v for …]', '(k: v for …)', '<k: v for …>'], 0, L('Graffe e due punti.', 'Braces and a colon.')),
        ], task('python', L('Crea `lunghi = [p for p in parole if len(p) > 3]`.', 'Create `lunghi = [p for p in parole if len(p) > 3]`.'), 'parole = ["sole", "re", "mare", "tu"]
', [re('lunghi\s*=\s*\[\s*p\s+for\s+p\s+in\s+parole\s+if\s+len\s*\(\s*p\s*\)\s*>\s*3\s*\]', L('La comprehension è giusta', 'The comprehension is right'))], '', 'lunghi = [p for p in parole if len(p) > 3]'), [
            dBlanks(pFill(), "prezzi = [10, 20, 30]\nscontati = [p * 0.9 ___ p ___ prezzi ___ p > 15]", ['for', 'in', 'if'], ['while', 'of', 'when'], L('Per ogni elemento, nella lista, se…', 'For each item, in the list, if…')),
            dFlash(pFlash(), [[L('Una comprehension crea una nuova lista', 'A comprehension creates a new list'), true], [L('Non si può usare `if` dentro', 'You cannot use `if` inside'), false], [L('Esistono comprehension per i dizionari', 'There are dict comprehensions'), true], [L('Una comprehension modifica la lista di partenza', 'A comprehension changes the original list'), false]], L('Lista, dizionario, insieme.', 'List, dict, set.')),
        ]),
        lesson('files', L('File e with', 'Files and with'), [
            P('`open()` apre un file; con `with` il file si chiude da solo anche se c’è un errore. `"r"` legge, `"w"` scrive da capo, `"a"` aggiunge in fondo.', '`open()` opens a file; with `with` the file closes by itself even if there is an error. `"r"` reads, `"w"` writes from scratch, `"a"` appends.'),
            C('with open("note.txt", "a", encoding="utf-8") as f:
    f.write("Nuova riga\n")

with open("note.txt", encoding="utf-8") as f:
    for riga in f:
        print(riga.strip())

import json
with open("dati.json") as f:
    dati = json.load(f)', 'python'),
            P('Indica sempre `encoding="utf-8"` per le lettere accentate. Il modulo `json` legge e scrive dati strutturati.', 'Always give `encoding="utf-8"` for accented letters. The `json` module reads and writes structured data.'),
        ], [
            Q('f1', L('Perché usare `with open(...)`?', 'Why use `with open(...)`?'), [L('Il file si chiude da solo', 'The file closes by itself'), L('È più veloce', 'It is faster'), L('Cripta il file', 'It encrypts the file'), L('Crea una copia', 'It makes a copy')], 0, L('Anche in caso di errore.', 'Even when an error occurs.')),
            Q('f2', L('Quale modalità aggiunge in fondo?', 'Which mode appends at the end?'), ['"a"', '"w"', '"r"', '"x"'], 0, L('`"w"` cancellerebbe il contenuto.', '`"w"` would wipe the content.')),
            Q('f3', L('Quale funzione legge JSON da un file?', 'Which function reads JSON from a file?'), ['json.load', 'json.read', 'json.open', 'json.file'], 0, L('`json.loads` legge da una stringa.', '`json.loads` reads from a string.')),
        ], task('python', L('Con `with open("diario.txt", "a", encoding="utf-8") as f:` scrivi `"Oggi\n"`.', 'With `with open("diario.txt", "a", encoding="utf-8") as f:` write `"Oggi\n"`.'), '', [re('with\s+open\s*\(\s*["\']diario\.txt["\']\s*,\s*["\']a["\']', L('Apri in aggiunta', 'You open for appending')), re('f\.write\s*\(', L('Scrivi nel file', 'You write to the file'))], '', 'with open("diario.txt", "a", encoding="utf-8") as f:
    f.write("Oggi\n")'), [
            dMatch(pMatch(), [['"r"', L('Leggi', 'Read')], ['"w"', L('Scrivi da capo', 'Write from scratch')], ['"a"', L('Aggiungi in fondo', 'Append')], ['json.dump', L('Scrive JSON', 'Writes JSON')], ['json.load', L('Legge JSON', 'Reads JSON')]], L('Modalità e JSON.', 'Modes and JSON.')),
            dBug(pBug(), ['f = open("dati.txt")', 'testo = f.read()', 'print(testo)'], 0, 'with open("dati.txt", encoding="utf-8") as f:', L('Senza `with` il file resta aperto se qualcosa va storto.', 'Without `with` the file stays open if something goes wrong.')),
        ]),
        lesson('exceptions', L('Eccezioni', 'Exceptions'), [
            P('Quando qualcosa va storto Python lancia un’**eccezione**. Con `try` / `except` la intercetti e decidi che cosa fare; `finally` gira sempre. `raise` lancia un’eccezione tua.', 'When something goes wrong Python raises an **exception**. With `try` / `except` you catch it and decide what to do; `finally` always runs. `raise` throws your own exception.'),
            C('def leggi_eta(testo):
    try:
        eta = int(testo)
    except ValueError:
        print("Non è un numero")
        return None
    if eta < 0:
        raise ValueError("L’età non può essere negativa")
    return eta', 'python'),
            P('Intercetta solo gli errori che sai gestire: un `except:` vuoto nasconde anche i bug veri.', 'Catch only the errors you can handle: a bare `except:` hides real bugs too.'),
        ], [
            Q('e1', L('Quale eccezione dà `int("ciao")`?', 'Which exception does `int("ciao")` raise?'), ['ValueError', 'TypeError', 'KeyError', 'IndexError'], 0, L('Il valore non è convertibile.', 'The value cannot be converted.')),
            Q('e2', L('Quale blocco gira sempre?', 'Which block always runs?'), ['finally', 'else', 'except', 'try'], 0, L('Ottimo per chiudere risorse.', 'Great for releasing resources.')),
            Q('e3', L('Perché evitare `except:` senza tipo?', 'Why avoid a bare `except:`?'), [L('Nasconde anche i bug', 'It hides real bugs too'), L('È lento', 'It is slow'), L('Non funziona', 'It does not work'), L('È vietato', 'It is forbidden')], 0, L('Meglio nominare l’errore atteso.', 'Better name the expected error.')),
        ], task('python', L('Converti `testo` con `int` dentro `try`, e intercetta `ValueError` mettendo `n = 0`.', 'Convert `testo` with `int` inside `try`, and catch `ValueError` setting `n = 0`.'), 'testo = input()
', [re('try\s*:\s*\n\s+n\s*=\s*int\s*\(\s*testo\s*\)', L('Provi la conversione', 'You try the conversion')), re('except\s+ValueError\s*:\s*\n\s+n\s*=\s*0', L('Gestisci l’errore', 'You handle the error'))], '', 'try:
    n = int(testo)
except ValueError:
    n = 0'), [
            dOrder(pSort(), ['try:', '    x = 10 / int(valore)', 'except ZeroDivisionError:', '    x = 0', 'finally:', '    print("Fatto")'], L('Prova, gestisci, chiudi.', 'Try, handle, finish.')),
            dMatch(pMatch(), [['ValueError', L('Valore non valido', 'Invalid value')], ['KeyError', L('Chiave mancante', 'Missing key')], ['IndexError', L('Indice fuori dalla lista', 'Index out of the list')], ['ZeroDivisionError', L('Divisione per zero', 'Division by zero')]], L('Le eccezioni più comuni.', 'The most common exceptions.')),
        ]),
        lesson('classes', L('Classi e dataclass', 'Classes and dataclasses'), [
            P('Una **classe** descrive un tipo di oggetto. `__init__` prepara l’oggetto, `self` è l’oggetto stesso. Con `@dataclass` Python scrive per te il costruttore e il confronto.', 'A **class** describes a type of object. `__init__` sets up the object, `self` is the object itself. With `@dataclass` Python writes the constructor and comparison for you.'),
            C('from dataclasses import dataclass

class Conto:
    def __init__(self, titolare, saldo=0):
        self.titolare = titolare
        self.saldo = saldo

    def versa(self, importo):
        self.saldo += importo

@dataclass
class Punto:
    x: int
    y: int', 'python'),
            P('Un metodo riceve sempre `self` come primo parametro; si chiama però senza: `conto.versa(10)`.', 'A method always receives `self` as its first parameter; you call it without it though: `conto.versa(10)`.'),
        ], [
            Q('k1', L('Come si chiama il costruttore?', 'What is the constructor called?'), ['__init__', 'constructor', 'new', '__new_object__'], 0, L('Con due trattini bassi per lato.', 'With two underscores each side.')),
            Q('k2', L('Che cos’è `self`?', 'What is `self`?'), [L('L’oggetto stesso', 'The object itself'), L('La classe madre', 'The parent class'), L('Una parola vietata', 'A forbidden word'), L('Il modulo', 'The module')], 0, L('Come `this` in altri linguaggi.', 'Like `this` in other languages.')),
            Q('k3', L('Che cosa fa `@dataclass`?', 'What does `@dataclass` do?'), [L('Scrive costruttore e confronto per te', 'Writes constructor and comparison for you'), L('Salva nel database', 'Saves to the database'), L('Rende la classe privata', 'Makes the class private'), L('Niente', 'Nothing')], 0, L('Bastano i campi con il tipo.', 'Typed fields are enough.')),
        ], task('python', L('Crea la dataclass `Libro` con i campi `titolo: str` e `pagine: int`.', 'Create the dataclass `Libro` with the fields `titolo: str` and `pagine: int`.'), 'from dataclasses import dataclass
', [re('@dataclass\s*\n\s*class\s+Libro', L('La dataclass esiste', 'The dataclass exists')), re('titolo\s*:\s*str', L('Il titolo è una stringa', 'The title is a string')), re('pagine\s*:\s*int', L('Le pagine sono un intero', 'Pages are an int'))], '', '@dataclass
class Libro:
    titolo: str
    pagine: int'), [
            dBug(pBug(), ['class Cane:', '    def __init__(nome):', '        self.nome = nome'], 1, '    def __init__(self, nome):', L('Il primo parametro di un metodo è `self`.', 'A method’s first parameter is `self`.')),
            dBlanks(pFill(), "class Gatto:\n    def ___(self, nome):\n        ___.nome = nome\n    def miagola(self):\n        return f\"{self.nome}: miao\"", ['__init__', 'self'], ['init', 'this', 'cls'], L('Costruttore e oggetto.', 'Constructor and object.')),
        ]),
        lesson('modules', L('Moduli, pacchetti e venv', 'Modules, packages and venv'), [
            P('Ogni file `.py` è un **modulo** che si importa con `import`. Le librerie esterne si installano con `pip` dentro un **ambiente virtuale** (`venv`), così ogni progetto ha le sue versioni.', 'Every `.py` file is a **module** you import with `import`. External libraries are installed with `pip` inside a **virtual environment** (`venv`), so every project has its own versions.'),
            C('# terminale
python -m venv .venv
source .venv/bin/activate
pip install requests

# codice
import requests
from math import sqrt

r = requests.get("https://api.github.com")
print(r.status_code, sqrt(16))', 'python'),
            P('`if __name__ == "__main__":` esegue codice solo quando il file è lanciato direttamente, non quando viene importato.', '`if __name__ == "__main__":` runs code only when the file is launched directly, not when it is imported.'),
        ], [
            Q('m1', L('Con quale comando installi una libreria?', 'Which command installs a library?'), ['pip install', 'npm install', 'python get', 'import install'], 0, L('`pip` è il gestore dei pacchetti.', '`pip` is the package manager.')),
            Q('m2', L('A che cosa serve un ambiente virtuale?', 'What is a virtual environment for?'), [L('A tenere separate le librerie di ogni progetto', 'To keep each project’s libraries apart'), L('A rendere Python più veloce', 'To make Python faster'), L('A virtualizzare il computer', 'To virtualise the computer'), L('A cifrare il codice', 'To encrypt code')], 0, L('Ogni progetto, le sue versioni.', 'Each project, its own versions.')),
            Q('m3', L('Quando è vero `__name__ == "__main__"`?', 'When is `__name__ == "__main__"` true?'), [L('Quando il file è lanciato direttamente', 'When the file is run directly'), L('Quando è importato', 'When it is imported'), L('Sempre', 'Always'), L('Mai', 'Never')], 0, L('Utile per i piccoli test.', 'Handy for small tests.')),
        ], task('python', L('Importa `sqrt` da `math` e stampa `sqrt(81)`.', 'Import `sqrt` from `math` and print `sqrt(81)`.'), '', [re('from\s+math\s+import\s+sqrt', L('Importi `sqrt`', 'You import `sqrt`')), re('print\s*\(\s*sqrt\s*\(\s*81\s*\)\s*\)', L('Stampi la radice', 'You print the root'))], '', 'from math import sqrt
print(sqrt(81))'), [
            dOrder(pSort(), ['python -m venv .venv', 'source .venv/bin/activate', 'pip install requests', 'pip freeze > requirements.txt'], L('Crea, attiva, installa, annota.', 'Create, activate, install, record.')),
            dMatch(pMatch(), [['import', L('Carica un modulo', 'Loads a module')], ['pip', L('Installa pacchetti', 'Installs packages')], ['venv', L('Ambiente separato', 'Separate environment')], ['requirements.txt', L('Elenco delle dipendenze', 'List of dependencies')]], L('Gli attrezzi di un progetto Python.', 'The tools of a Python project.')),
        ]),
    ],
];
