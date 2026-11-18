<?php
/** Python advanced: generators, decorators, type hints, async, testing, pattern matching, threads and processes, packaging and profiling. */

return [
    'id' => 'python-advanced', 'family' => 'python', 'after' => 'python-intermediate',
    'title' => L('Python avanzato', 'Advanced Python'), 'short' => L('Python avanzato', 'Python Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Generatori, decoratori, tipi, programmazione asincrona, context manager e test con pytest, pattern matching, thread e processi, progetti con uv e ottimizzazione.', 'Generators, decorators, typing, asynchronous programming, context managers and tests with pytest, pattern matching, threads and processes, projects with uv and optimisation.'),
    'lessons' => [
        lesson('generators', L('Iteratori e generatori', 'Iterators and generators'), [
            P('Un **generatore** è una funzione con `yield`: produce un valore alla volta e si ferma fino alla richiesta successiva. Così si possono scorrere sequenze enormi, o infinite, senza riempire la memoria.', 'A **generator** is a function with `yield`: it produces one value at a time and pauses until the next request. So you can walk through huge, or endless, sequences without filling memory.'),
            C('def fibonacci():
    a, b = 0, 1
    while True:
        yield a
        a, b = b, a + b

from itertools import islice
print(list(islice(fibonacci(), 10)))

somma = sum(x * x for x in range(1_000_000))', 'python'),
            P('Le **generator expression** sono comprehension con le tonde: `(x * x for x in dati)` non crea nessuna lista.', '**Generator expressions** are comprehensions with round brackets: `(x * x for x in data)` builds no list at all.'),
        ], [
            Q('g1', L('Che cosa rende generatore una funzione?', 'What makes a function a generator?'), ['yield', 'return', 'async', 'lambda'], 0, L('Basta un `yield` nel corpo.', 'One `yield` in the body is enough.')),
            Q('g2', L('Che differenza c’è tra `[x for x in d]` e `(x for x in d)`?', 'What is the difference between `[x for x in d]` and `(x for x in d)`?'), [L('La seconda non crea una lista', 'The second builds no list'), L('Nessuna', 'None'), L('La prima è più lenta sempre', 'The first is always slower'), L('La seconda dà errore', 'The second fails')], 0, L('Produce i valori quando servono.', 'It produces values on demand.')),
            Q('g3', L('Che cosa fa `islice(gen, 10)`?', 'What does `islice(gen, 10)` do?'), [L('Prende i primi 10 valori', 'Takes the first 10 values'), L('Salta 10 valori', 'Skips 10 values'), L('Moltiplica per 10', 'Multiplies by 10'), L('Ordina', 'Sorts')], 0, L('Utile con i generatori infiniti.', 'Handy with endless generators.')),
        ], task('python', L('Scrivi il generatore `pari(n)` che fa `yield` dei numeri pari da 0 a `n`.', 'Write the generator `pari(n)` that yields the even numbers from 0 to `n`.'), '', [re('def\s+pari\s*\(\s*n\s*\)\s*:', L('La funzione esiste', 'The function exists')), re('yield\s+\w+', L('Usa `yield`', 'It uses `yield`'))], '', 'def pari(n):
    for i in range(0, n + 1, 2):
        yield i'), [
            dBlanks(pFill(), "def conta(fino):\n    i = 0\n    while i < fino:\n        ___ i\n        i += 1\n\ntotale = sum(x ___ x in conta(5))", ['yield', 'for'], ['return', 'in', 'print'], L('Un valore alla volta.', 'One value at a time.')),
            dFlash(pFlash(), [[L('Un generatore può essere infinito', 'A generator can be endless'), true], [L('`yield` termina la funzione per sempre', '`yield` ends the function for good'), false], [L('`sum` accetta una generator expression', '`sum` accepts a generator expression'), true], [L('Un generatore calcola tutti i valori subito', 'A generator works out all its values straight away'), false]], L('`yield` mette in pausa, non chiude.', '`yield` pauses, it does not end.')),
        ]),
        lesson('decorators', L('Decoratori', 'Decorators'), [
            P('Un **decoratore** è una funzione che avvolge un’altra funzione per aggiungerle qualcosa: misurare il tempo, controllare i permessi, ricordare i risultati. Si applica con la `@` sopra la definizione.', 'A **decorator** is a function that wraps another function to add something to it: timing, permission checks, caching results. You apply it with `@` above the definition.'),
            C('import functools, time

def cronometro(funzione):
    @functools.wraps(funzione)
    def involucro(*args, **kwargs):
        inizio = time.perf_counter()
        risultato = funzione(*args, **kwargs)
        print(f"{funzione.__name__}: {time.perf_counter() - inizio:.3f}s")
        return risultato
    return involucro

@cronometro
def calcola(n):
    return sum(range(n))', 'python'),
            P('`*args` e `**kwargs` raccolgono qualsiasi argomento. `functools.wraps` conserva il nome e la documentazione della funzione originale. `@functools.cache` è un decoratore già pronto che ricorda i risultati.', '`*args` and `**kwargs` collect any arguments. `functools.wraps` keeps the name and docs of the original function. `@functools.cache` is a ready-made decorator that remembers results.'),
        ], [
            Q('d1', L('Che cosa riceve un decoratore?', 'What does a decorator receive?'), [L('Una funzione', 'A function'), L('Una stringa', 'A string'), L('Un numero', 'A number'), L('Una classe CSS', 'A CSS class')], 0, L('E restituisce una funzione nuova.', 'And returns a new function.')),
            Q('d2', L('A che cosa serve `**kwargs`?', 'What is `**kwargs` for?'), [L('Raccoglie gli argomenti con nome', 'Collects keyword arguments'), L('Eleva al quadrato', 'Squares numbers'), L('Commenta', 'Comments'), L('Importa', 'Imports')], 0, L('`*args` raccoglie quelli posizionali.', '`*args` collects positional ones.')),
            Q('d3', L('Quale decoratore ricorda i risultati?', 'Which decorator remembers results?'), ['@functools.cache', '@memory', '@save', '@static'], 0, L('Ottimo per funzioni pure e costose.', 'Great for pure, costly functions.')),
        ], task('python', L('Applica `@functools.cache` alla funzione `fib(n)`.', 'Apply `@functools.cache` to the function `fib(n)`.'), 'import functools

def fib(n):
    return n if n < 2 else fib(n - 1) + fib(n - 2)', [re('@functools\.cache\s*\n\s*def\s+fib', L('La funzione è decorata', 'The function is decorated'))], '', '@functools.cache
def fib(n):
    return n if n < 2 else fib(n - 1) + fib(n - 2)'), [
            dOrder(pSort(), ['def ripeti(funzione):', '    def involucro(*args):', '        funzione(*args)', '        funzione(*args)', '    return involucro'], L('Ricevi, avvolgi, restituisci.', 'Receive, wrap, return.')),
            dBug(pBug(), ['def log(funzione):', '    def involucro(*args, **kwargs):', '        print("Chiamata", funzione.__name__)', '        funzione(*args, **kwargs)', '    return involucro'], 3, '        return funzione(*args, **kwargs)', L('Senza `return` il risultato della funzione va perso.', 'Without `return` the function’s result is lost.')),
        ]),
        lesson('typing', L('Tipi e Protocol', 'Types and Protocols'), [
            P('Le **annotazioni di tipo** rendono il codice più chiaro e permettono a strumenti come **mypy** di trovare errori prima di eseguire. `list[int]`, `dict[str, float]`, `str | None` descrivono i dati.', '**Type annotations** make code clearer and let tools such as **mypy** find errors before running. `list[int]`, `dict[str, float]`, `str | None` describe the data.'),
            C('from typing import Protocol

class Pagabile(Protocol):
    def totale(self) -> float: ...

def addebita(cosa: Pagabile, sconto: float | None = None) -> float:
    importo = cosa.totale()
    return importo * (1 - sconto) if sconto else importo

voti: dict[str, list[int]] = {"Ada": [8, 9]}', 'python'),
            P('Un **Protocol** descrive una forma: qualsiasi classe con un metodo `totale()` va bene, anche se non eredita nulla. È il “duck typing” controllato.', 'A **Protocol** describes a shape: any class with a `totale()` method fits, even without inheriting anything. It is checked “duck typing”.'),
        ], [
            Q('t1', L('Come scrivi “una stringa oppure None”?', 'How do you write “a string or None”?'), ['str | None', 'str or None', 'str & None', 'Maybe[str]'], 0, L('La barra unisce i tipi.', 'The bar unites types.')),
            Q('t2', L('Python blocca il programma se i tipi non tornano?', 'Does Python stop the program if types are wrong?'), [L('No, lo segnalano strumenti come mypy', 'No, tools like mypy report it'), L('Sì, sempre', 'Yes, always'), L('Solo con le classi', 'Only with classes'), L('Solo in produzione', 'Only in production')], 0, L('I tipi sono indicazioni.', 'Types are hints.')),
            Q('t3', L('Che cosa descrive un Protocol?', 'What does a Protocol describe?'), [L('I metodi che un oggetto deve avere', 'The methods an object must have'), L('Un protocollo di rete', 'A network protocol'), L('Un file', 'A file'), L('Un database', 'A database')], 0, L('Conta la forma, non l’ereditarietà.', 'Shape matters, not inheritance.')),
        ], task('python', L('Annota `def media(voti: list[float]) -> float:`.', 'Annotate `def media(voti: list[float]) -> float:`.'), 'def media(voti):
    return sum(voti) / len(voti)', [re('def\s+media\s*\(\s*voti\s*:\s*list\[float\]\s*\)\s*->\s*float\s*:', L('Parametro e risultato hanno un tipo', 'Parameter and result are typed'))], '', 'def media(voti: list[float]) -> float:
    return sum(voti) / len(voti)'), [
            dMatch(pMatch(), [['list[int]', L('Lista di interi', 'List of ints')], ['dict[str, int]', L('Da testo a intero', 'From text to int')], ['str | None', L('Testo o niente', 'Text or nothing')], ['-> bool', L('Restituisce vero/falso', 'Returns true/false')]], L('Leggere le annotazioni.', 'Reading annotations.')),
            dFlash(pFlash(), [[L('mypy controlla i tipi senza eseguire', 'mypy checks types without running'), true], [L('Un Protocol richiede ereditarietà', 'A Protocol requires inheritance'), false], [L('`tuple[int, int]` è una coppia di interi', '`tuple[int, int]` is a pair of ints'), true], [L('Python blocca il programma se un tipo annotato non torna', 'Python stops the program if an annotated type is wrong'), false]], L('I Protocol guardano la forma.', 'Protocols look at shape.')),
        ]),
        lesson('async', L('async e await', 'async and await'), [
            P('Il codice **asincrono** fa più cose mentre aspetta: richieste di rete, file, database. Una funzione `async def` si mette in pausa con `await`; `asyncio.gather` ne esegue molte insieme.', '**Asynchronous** code does several things while waiting: network requests, files, databases. An `async def` function pauses with `await`; `asyncio.gather` runs many together.'),
            C('import asyncio

async def scarica(nome, secondi):
    await asyncio.sleep(secondi)
    return f"{nome} pronto"

async def main():
    risultati = await asyncio.gather(
        scarica("A", 1), scarica("B", 1), scarica("C", 1),
    )
    print(risultati)

asyncio.run(main())', 'python'),
            P('Le tre attese da un secondo finiscono insieme in circa un secondo, non tre. L’asincrono aiuta con le attese, non con i calcoli pesanti.', 'The three one-second waits finish together in about one second, not three. Async helps with waiting, not with heavy computation.'),
        ], [
            Q('a1', L('Dove si può usare `await`?', 'Where can you use `await`?'), [L('Dentro una funzione `async`', 'Inside an `async` function'), L('Ovunque', 'Anywhere'), L('Solo nei cicli', 'Only in loops'), L('Solo nelle classi', 'Only in classes')], 0, L('Fuori serve `asyncio.run`.', 'Outside you need `asyncio.run`.')),
            Q('a2', L('Che cosa fa `asyncio.gather`?', 'What does `asyncio.gather` do?'), [L('Esegue più coroutine insieme', 'Runs several coroutines together'), L('Raccoglie la spazzatura', 'Collects garbage'), L('Ordina una lista', 'Sorts a list'), L('Chiude il programma', 'Ends the program')], 0, L('E restituisce tutti i risultati.', 'And returns all the results.')),
            Q('a3', L('L’asincrono velocizza un calcolo pesante?', 'Does async speed up heavy computation?'), [L('No, aiuta con le attese', 'No, it helps with waiting'), L('Sì, sempre', 'Yes, always'), L('Solo su Linux', 'Only on Linux'), L('Solo con i numeri', 'Only with numbers')], 0, L('Per i calcoli servono processi (o i thread della build senza GIL).', 'Computation needs processes (or threads on the build without the GIL).')),
        ], task('python', L('Scrivi `async def saluta():` che fa `await asyncio.sleep(1)` e restituisce `"ciao"`.', 'Write `async def saluta():` that does `await asyncio.sleep(1)` and returns `"ciao"`.'), 'import asyncio
', [re('async\s+def\s+saluta\s*\(\s*\)\s*:', L('La coroutine esiste', 'The coroutine exists')), re('await\s+asyncio\.sleep\s*\(\s*1\s*\)', L('Aspetta un secondo', 'It waits one second')), re('return\s+["\']ciao["\']', L('Restituisce “ciao”', 'It returns “ciao”'))], '', 'async def saluta():
    await asyncio.sleep(1)
    return "ciao"'), [
            dOrder(pSort(), ['import asyncio', 'async def main():', '    a, b = await asyncio.gather(uno(), due())', '    print(a, b)', 'asyncio.run(main())'], L('Importa, definisci, attendi, avvia.', 'Import, define, await, start.')),
            dBug(pBug(), ['async def leggi():', '    dati = carica_da_rete()', '    return dati'], 1, '    dati = await carica_da_rete()', L('Una coroutine va attesa con `await`, altrimenti resta un oggetto non eseguito.', 'A coroutine must be awaited, otherwise it stays an unrun object.')),
        ]),
        lesson('pytest', L('Context manager e pytest', 'Context managers and pytest'), [
            P('Un **context manager** prepara e chiude qualcosa intorno a un blocco `with`; il modo più semplice è `@contextmanager` con un `yield`. **pytest** trova le funzioni che iniziano per `test_` e controlla con `assert`.', 'A **context manager** sets up and tears down something around a `with` block; the simplest way is `@contextmanager` with a `yield`. **pytest** finds functions starting with `test_` and checks with `assert`.'),
            C('from contextlib import contextmanager
import time
import pytest

@contextmanager
def cronometro():
    inizio = time.perf_counter()
    yield
    print(time.perf_counter() - inizio)

def test_somma():
    assert somma(2, 3) == 5

def test_divisione_per_zero():
    with pytest.raises(ZeroDivisionError):
        1 / 0', 'python'),
            P('Si lanciano con `pytest` dal terminale. Le **fixture** preparano dati riutilizzabili tra i test.', 'You run them with `pytest` from the terminal. **Fixtures** prepare data reused across tests.'),
        ], [
            Q('p1', L('Come trova pytest i test?', 'How does pytest find tests?'), [L('Funzioni che iniziano per `test_`', 'Functions starting with `test_`'), L('Commenti speciali', 'Special comments'), L('File .test', '.test files'), L('Da un elenco a mano', 'From a manual list')], 0, L('Anche i file `test_*.py`.', 'And `test_*.py` files.')),
            Q('p2', L('Come controlli che venga lanciata un’eccezione?', 'How do you check an exception is raised?'), ['with pytest.raises(Errore):', 'assert Errore', 'try: test', 'expect(Errore)'], 0, L('Il test fallisce se l’errore non arriva.', 'The test fails if the error does not come.')),
            Q('p3', L('In un `@contextmanager`, che cosa separa preparazione e chiusura?', 'In a `@contextmanager`, what separates setup and teardown?'), ['yield', 'return', 'await', 'pass'], 0, L('Il blocco `with` gira al posto del `yield`.', 'The `with` block runs in place of `yield`.')),
        ], task('python', L('Scrivi `def test_doppio():` con `assert doppio(4) == 8`.', 'Write `def test_doppio():` with `assert doppio(4) == 8`.'), '', [re('def\s+test_doppio\s*\(\s*\)\s*:', L('Il test esiste', 'The test exists')), re('assert\s+doppio\s*\(\s*4\s*\)\s*==\s*8', L('L’asserzione è giusta', 'The assertion is right'))], '', 'def test_doppio():
    assert doppio(4) == 8'), [
            dBlanks(pFill(), "def ___maiuscolo():\n    ___ \"ada\".upper() == \"ADA\"\n\ndef test_errore():\n    with pytest.___(ValueError):\n        int(\"x\")", ['test_', 'assert', 'raises'], ['check_', 'expect', 'catch'], L('Nome, verifica, eccezione attesa.', 'Name, check, expected exception.')),
            dMatch(pMatch(), [['assert', L('Verifica una condizione', 'Checks a condition')], ['fixture', L('Prepara dati per i test', 'Prepares data for tests')], ['pytest.raises', L('Si aspetta un errore', 'Expects an error')], ['@contextmanager', L('Crea un `with`', 'Creates a `with`')]], L('Gli strumenti dei test.', 'The testing tools.')),
        ]),
        lesson('pattern-matching', L('Pattern matching con match', 'Pattern matching with match'), [
            P('`match` (da Python 3.10) confronta la **forma** dei dati, non solo il valore: liste, dizionari, oggetti. I nomi nel pattern **catturano** i pezzi, `|` offre alternative, `if` aggiunge una condizione (la *guardia*) e `_` prende tutto il resto.', '`match` (since Python 3.10) compares the **shape** of data, not just its value: lists, dicts, objects. Names in the pattern **capture** the pieces, `|` offers alternatives, `if` adds a condition (the *guard*) and `_` takes everything else.'),
            C('def gestisci(comando):
    match comando.split():
        case ["vai", direzione]:
            return f"Vado a {direzione}"
        case ["prendi", *oggetti]:
            return f"Prendo {len(oggetti)} oggetti"
        case ["esci" | "fine"]:
            return "Ciao!"
        case _:
            return "Non capisco"

def descrivi(evento: dict) -> str:
    match evento:
        case {"tipo": "clic", "x": x, "y": y} if x > 0:
            return f"Clic in {x},{y}"
        case {"tipo": "tasto", "codice": str(codice)}:
            return f"Tasto {codice}"
        case _:
            return "Sconosciuto"', 'python'),
            Tip('Un nome semplice in un `case` cattura sempre il valore, non lo confronta. Per confrontare con una costante usa un letterale o un nome con il punto, come `Colore.ROSSO`.', 'A plain name in a `case` always captures the value, it does not compare it. To compare with a constant use a literal or a dotted name, such as `Colore.ROSSO`.'),
        ], [
            Q('pm1', L('Che cosa fa `case ["vai", direzione]:` con `["vai", "nord"]`?', 'What does `case ["vai", direzione]:` do with `["vai", "nord"]`?'), [L('Fallisce perché direzione non esiste', 'Fails because direzione does not exist'), L('Corrisponde e mette "nord" in direzione', 'Matches and puts "nord" into direzione'), L('Confronta direzione con la stringa "nord"', 'Compares direzione with the string "nord"'), L('Corrisponde solo se la lista è vuota', 'Matches only if the list is empty')], 1, L('Il nome cattura il secondo elemento.', 'The name captures the second item.')),
            Q('pm2', L('A che cosa serve `case _:`?', 'What is `case _:` for?'), [L('A ignorare il primo elemento', 'To ignore the first item'), L('A dichiarare una variabile privata', 'To declare a private variable'), L('A prendere tutti i casi non gestiti prima', 'To take every case not handled before'), L('A chiudere il blocco match', 'To close the match block')], 2, L('È il caso predefinito.', 'It is the default case.')),
            Q('pm3', L('Da quale versione esiste `match`?', 'Since which version does `match` exist?'), ['Python 3.6', 'Python 3.8', 'Python 2.7', 'Python 3.10'], 3, L('PEP 634, uscito nel 2021.', 'PEP 634, released in 2021.')),
        ], task('python', L('Aggiungi prima di `case _:` il caso `case (0, 0):` che restituisce `"origine"` e il caso `case (x, 0):` che restituisce `f"asse x in {x}"`.', 'Before `case _:` add the case `case (0, 0):` returning `"origine"` and the case `case (x, 0):` returning `f"asse x in {x}"`.'), 'def dove(punto):
    match punto:
        case _:
            return "altrove"
', [re('case\s*\(\s*0\s*,\s*0\s*\)\s*:\s*\n\s*return\s+["\']origine["\']', L('Riconosci l’origine', 'You recognise the origin')), re('case\s*\(\s*(\w+)\s*,\s*0\s*\)\s*:\s*\n\s*return\s+f["\']asse x in \{\1\}["\']', L('Riconosci l’asse x', 'You recognise the x axis'))], '', 'def dove(punto):
    match punto:
        case (0, 0):
            return "origine"
        case (x, 0):
            return f"asse x in {x}"
        case _:
            return "altrove"'), [
            dBlanks(pFill(), 'match risposta:
    case {"stato": 200, "dati": dati}:
        mostra(dati)
    case {"stato": 404} ___ {"stato": 410}:
        print("Sparito")
    case {"stato": s} ___ s >= 500:
        print("Errore del server")
    case ___:
        print("Altro")', ['|', 'if', '_'], ['or', 'when', 'default'], L('Alternativa, guardia, caso predefinito.', 'Alternative, guard, default case.')),
            dFlash(pFlash(), [[L('`match` può riconoscere la struttura di un dizionario', '`match` can recognise the structure of a dict'), true], [L('In `match` serve `break` alla fine di ogni caso', '`match` needs `break` at the end of each case'), false], [L('`case _:` corrisponde a qualsiasi valore', '`case _:` matches any value'), true], [L('Un nome semplice in un `case` viene confrontato con la variabile omonima', 'A plain name in a `case` is compared with the variable of that name'), false]], L('Il pattern descrive una forma.', 'The pattern describes a shape.')),
        ]),
        lesson('parallelism', L('Thread, processi e GIL', 'Threads, processes and the GIL'), [
            P('Nella versione classica di CPython il **GIL** lascia eseguire codice Python a un solo thread alla volta: i **thread** aiutano con le attese (rete, disco), per i calcoli pesanti servono i **processi**. Da Python 3.14 la build **free-threaded** (senza GIL, ad esempio `python3.14t`) è supportata ufficialmente: lì anche i thread lavorano davvero in parallelo, ma resta una scelta opzionale.', 'In classic CPython the **GIL** lets only one thread run Python code at a time: **threads** help with waiting (network, disk), heavy computation needs **processes**. Since Python 3.14 the **free-threaded** build (without the GIL, for example `python3.14t`) is officially supported: there threads truly run in parallel too, but it remains an optional choice.'),
            C('from concurrent.futures import ThreadPoolExecutor, ProcessPoolExecutor
import urllib.request

def scarica(url):
    with urllib.request.urlopen(url) as r:
        return len(r.read())

def primi_fino_a(n):
    return sum(1 for k in range(2, n) if all(k % d for d in range(2, int(k**0.5) + 1)))

if __name__ == "__main__":
    urls = ["https://www.python.org", "https://pypi.org"]
    with ThreadPoolExecutor(max_workers=8) as pool:      # attese di rete
        print(list(pool.map(scarica, urls)))

    with ProcessPoolExecutor() as pool:                  # calcolo sulla CPU
        print(list(pool.map(primi_fino_a, [50_000, 60_000])))', 'python'),
            P('`concurrent.futures` offre la stessa interfaccia per thread e processi: `map` distribuisce il lavoro e il blocco `with` aspetta che tutto finisca. Con i processi serve `if __name__ == "__main__":`, perché ogni processo nuovo rilegge il modulo.', '`concurrent.futures` offers the same interface for threads and processes: `map` hands out the work and the `with` block waits for everything to finish. Processes need `if __name__ == "__main__":`, because each new process re-reads the module.'),
        ], [
            Q('pp1', L('Quale strumento conviene per scaricare 100 pagine web?', 'Which tool suits downloading 100 web pages?'), ['ProcessPoolExecutor', 'ThreadPoolExecutor', L('Un ciclo for normale', 'A plain for loop'), 'time.sleep'], 1, L('Il lavoro è quasi tutto attesa di rete.', 'The work is almost all network waiting.')),
            Q('pp2', L('Che cosa fa il GIL nella build classica di CPython?', 'What does the GIL do in classic CPython?'), [L('Lascia eseguire codice Python a un thread alla volta', 'Lets one thread at a time run Python code'), L('Blocca gli errori di rete', 'Blocks network errors'), L('Raccoglie la memoria non usata', 'Collects unused memory'), L('Divide il programma in processi', 'Splits the program into processes')], 0, L('Per questo i calcoli non accelerano con i thread.', 'That is why computation does not speed up with threads.')),
            Q('pp3', L('Che cosa porta Python 3.14 sul GIL?', 'What does Python 3.14 bring about the GIL?'), [L('Lo elimina da tutte le installazioni', 'It removes it from every installation'), L('Lo rende più lento', 'It makes it slower'), L('Nessuna novità', 'Nothing new'), L('Supporta ufficialmente una build opzionale senza GIL', 'It officially supports an optional build without the GIL')], 3, L('La build classica, con il GIL, resta quella predefinita.', 'The classic build, with the GIL, remains the default.')),
        ], task('python', L('In un blocco `with ThreadPoolExecutor() as pool:` applica `scarica` a ogni elemento di `urls` con `pool.map`, salvando il risultato come lista in `risultati`.', 'In a `with ThreadPoolExecutor() as pool:` block apply `scarica` to every item of `urls` with `pool.map`, saving the result as a list in `risultati`.'), 'from concurrent.futures import ThreadPoolExecutor
', [re('with\s+ThreadPoolExecutor\s*\([^)]*\)\s+as\s+(\w+)\s*:', L('Apri il pool di thread', 'You open the thread pool')), re('risultati\s*=\s*list\s*\(\s*\w+\.map\s*\(\s*scarica\s*,\s*urls\s*\)\s*\)', L('Distribuisci il lavoro', 'You hand out the work'))], '', 'with ThreadPoolExecutor() as pool:
    risultati = list(pool.map(scarica, urls))'), [
            dMatch(pMatch(), [['threading', L('Thread per le attese', 'Threads for waiting')], ['multiprocessing', L('Processi per i calcoli', 'Processes for computing')], ['asyncio', L('Tante attese in un solo thread', 'Many waits in one thread')], ['GIL', L('Un thread Python alla volta', 'One Python thread at a time')], ['python3.14t', L('Build senza GIL', 'Build without the GIL')]], L('Strumenti per fare più cose insieme.', 'Tools for doing several things at once.')),
            dFlash(pFlash(), [[L('I processi non condividono la memoria come i thread', 'Processes do not share memory like threads'), true], [L('Con il GIL i thread velocizzano i calcoli pesanti', 'With the GIL threads speed up heavy computation'), false], [L('Il blocco `with` di un executor aspetta la fine dei compiti', 'An executor’s `with` block waits for the tasks to finish'), true], [L('Da Python 3.14 esiste solo la build senza GIL', 'Since Python 3.14 only the build without the GIL exists'), false]], L('Thread per le attese, processi per i calcoli.', 'Threads for waiting, processes for computing.')),
        ]),
        lesson('packaging', L('Progetti moderni: pyproject, uv e ruff', 'Modern projects: pyproject, uv and ruff'), [
            P('`pyproject.toml` è il file standard che descrive un progetto: nome, versione, versione di Python richiesta, dipendenze e comandi. **uv** (veloce, scritto in Rust) crea l’ambiente virtuale, installa i pacchetti e blocca le versioni esatte in `uv.lock`. **ruff** controlla e formatta il codice in un attimo.', '`pyproject.toml` is the standard file describing a project: name, version, required Python version, dependencies and commands. **uv** (fast, written in Rust) creates the virtual environment, installs packages and locks the exact versions in `uv.lock`. **ruff** lints and formats code in no time.'),
            C('[project]
name = "fucina-stats"
version = "0.1.0"
requires-python = ">=3.12"
dependencies = ["httpx>=0.27"]

[project.scripts]
fucina-stats = "fucina_stats.cli:main"

[build-system]
requires = ["hatchling"]
build-backend = "hatchling.build"

[tool.ruff]
line-length = 100', 'toml'),
            C('uv init fucina-stats
uv add httpx
uv add --dev pytest ruff
uv run pytest
uv run ruff check .
uv build', 'bash'),
            P('`pip` e `venv` restano validi e funzionano con lo stesso `pyproject.toml`; uv li riunisce in un solo strumento. Il file `uv.lock` va messo su git: così chiunque installa esattamente le stesse versioni.', '`pip` and `venv` remain valid and work with the same `pyproject.toml`; uv brings them together in one tool. The `uv.lock` file goes into git: that way everyone installs exactly the same versions.'),
        ], [
            Q('k1', L('Quale file descrive un progetto Python moderno?', 'Which file describes a modern Python project?'), ['setup.cfg', 'requirements.txt', 'pyproject.toml', 'package.json'], 2, L('È lo standard definito dai PEP 518 e 621.', 'It is the standard defined by PEPs 518 and 621.')),
            Q('k2', L('A che cosa serve `uv.lock`?', 'What is `uv.lock` for?'), [L('Blocca le versioni esatte delle dipendenze', 'Locks the exact versions of dependencies'), L('Impedisce di modificare il codice', 'Prevents changing the code'), L('Cifra le password del progetto', 'Encrypts the project’s passwords'), L('Elenca i file da escludere da git', 'Lists the files to keep out of git')], 0, L('Installazioni riproducibili.', 'Reproducible installs.')),
            Q('k3', L('Che cosa fa `ruff check`?', 'What does `ruff check` do?'), [L('Esegue i test', 'Runs the tests'), L('Pubblica il pacchetto su PyPI', 'Publishes the package to PyPI'), L('Crea l’ambiente virtuale', 'Creates the virtual environment'), L('Trova errori e problemi di stile nel codice', 'Finds errors and style problems in the code')], 3, L('`ruff format` invece formatta.', '`ruff format` formats instead.')),
        ], task('text', L('Nella sezione `[project]` aggiungi `requires-python = ">=3.12"` e la dipendenza `"httpx"` nell’elenco `dependencies`.', 'In the `[project]` section add `requires-python = ">=3.12"` and the `"httpx"` dependency in the `dependencies` list.'), '[project]
name = "meteo"
version = "0.1.0"
', [re('requires-python\s*=\s*["\']>=\s*3\.1[2-9]["\']', L('Indichi la versione di Python', 'You state the Python version')), re('dependencies\s*=\s*\[[^\]]*["\']httpx[^"\']*["\'][^\]]*\]', L('Aggiungi httpx', 'You add httpx'))], '', '[project]
name = "meteo"
version = "0.1.0"
requires-python = ">=3.12"
dependencies = ["httpx"]'), [
            dOrder(pSort(), ['uv init meteo', 'cd meteo', 'uv add httpx', 'uv run main.py'], L('Crea, entra, aggiungi, esegui.', 'Create, enter, add, run.')),
            dMatch(pMatch(), [['pyproject.toml', L('Descrive il progetto', 'Describes the project')], ['uv.lock', L('Versioni bloccate', 'Locked versions')], ['uv add', L('Aggiunge una dipendenza', 'Adds a dependency')], ['ruff', L('Linter e formattatore', 'Linter and formatter')], ['[project.scripts]', L('Comandi da terminale', 'Terminal commands')]], L('Gli strumenti di un progetto moderno.', 'The tools of a modern project.')),
        ]),
        lesson('profiling', L('Misurare e ottimizzare', 'Measuring and optimising'), [
            P('Prima di ottimizzare, **misura**: `timeit` confronta piccoli pezzi di codice, `cProfile` mostra quali funzioni consumano più tempo. Spesso il guadagno vero viene dalla struttura dati giusta: cercare in un `set` è immediato, in una lista bisogna scorrerla tutta.', 'Before optimising, **measure**: `timeit` compares small snippets, `cProfile` shows which functions take the most time. The real gain often comes from the right data structure: looking something up in a `set` is instant, a list must be scanned all the way.'),
            C('import timeit
import cProfile

print(timeit.timeit("sum(range(1000))", number=10_000))

def lento(n):
    risultato = ""
    for i in range(n):
        risultato += str(i)
    return risultato

def veloce(n):
    return "".join(str(i) for i in range(n))

cProfile.run("lento(100_000)", sort="cumulative")

numeri = list(range(1_000_000))
insieme = set(numeri)
print(999_999 in numeri)    # scorre fino in fondo
print(999_999 in insieme)   # quasi istantaneo', 'python'),
            Tip('Dal terminale: `python -m cProfile -s cumtime script.py` profila un intero script senza toccarne il codice.', 'From the terminal: `python -m cProfile -s cumtime script.py` profiles a whole script without touching its code.'),
        ], [
            Q('f1', L('Quale modulo confronta la velocità di piccoli pezzi di codice?', 'Which module compares the speed of small snippets?'), ['cProfile', 'timeit', 'pdb', 'logging'], 1, L('Ripete il codice molte volte e misura.', 'It repeats the code many times and measures.')),
            Q('f2', L('Perché `x in un_set` è più veloce di `x in una_lista`?', 'Why is `x in a_set` faster than `x in a_list`?'), [L('Il set è sempre più piccolo', 'The set is always smaller'), L('La lista è scritta in Python puro', 'The list is written in pure Python'), L('Non è più veloce', 'It is not faster'), L('Il set usa una tabella hash e non scorre gli elementi', 'The set uses a hash table and does not scan the items')], 3, L('Il tempo resta quasi costante anche con milioni di elementi.', 'The time stays almost constant even with millions of items.')),
            Q('f3', L('Qual è la prima regola dell’ottimizzazione?', 'What is the first rule of optimisation?'), [L('Riscrivere tutto in C', 'Rewrite everything in C'), L('Usare sempre i thread', 'Always use threads'), L('Misurare prima di cambiare', 'Measure before changing'), L('Togliere i commenti', 'Remove the comments')], 2, L('Altrimenti rischi di ottimizzare la parte sbagliata.', 'Otherwise you may optimise the wrong part.')),
        ], task('python', L('Riscrivi `unisci` usando `"".join(...)` invece di `+=` in un ciclo.', 'Rewrite `unisci` using `"".join(...)` instead of `+=` in a loop.'), 'def unisci(parole):
    testo = ""
    for p in parole:
        testo += p
    return testo
', [re('return\s+(""|\'\')\.join\s*\(', L('Usi join', 'You use join')), re('\A(?![\s\S]*\+=)', L('Niente più `+=`', 'No more `+=`'))], '', 'def unisci(parole):
    return "".join(parole)'), [
            dMatch(pMatch(), [['timeit', L('Cronometra piccoli pezzi', 'Times small snippets')], ['cProfile', L('Tempo per funzione', 'Time per function')], ['set', L('Ricerca immediata', 'Instant lookup')], ['"".join', L('Unisce molte stringhe', 'Joins many strings')], ['functools.cache', L('Ricorda i risultati', 'Remembers results')]], L('Strumenti e trucchi per la velocità.', 'Tools and tricks for speed.')),
            dFlash(pFlash(), [[L('Ottimizzare senza misurare porta spesso a lavoro inutile', 'Optimising without measuring often wastes effort'), true], [L('Cercare in una lista lunga è veloce quanto in un set', 'Searching a long list is as fast as a set'), false], [L('`cProfile` mostra il tempo speso in ogni funzione', '`cProfile` shows the time spent in each function'), true], [L('Unire stringhe con `+=` in un ciclo è sempre il modo più veloce', 'Joining strings with `+=` in a loop is always fastest'), false]], L('Misura, poi scegli la struttura giusta.', 'Measure, then pick the right structure.')),
        ]),
    ],
];
