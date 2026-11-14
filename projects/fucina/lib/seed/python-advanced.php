<?php
/** Python advanced: generators, decorators, type hints, async and testing. */

return [
    'id' => 'python-advanced', 'family' => 'python', 'after' => 'python-intermediate',
    'title' => L('Python avanzato', 'Advanced Python'), 'short' => L('Python avanzato', 'Python Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Generatori, decoratori, tipi, programmazione asincrona, context manager e test con pytest.', 'Generators, decorators, typing, asynchronous programming, context managers and tests with pytest.'),
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
            Q('a3', L('L’asincrono velocizza un calcolo pesante?', 'Does async speed up heavy computation?'), [L('No, aiuta con le attese', 'No, it helps with waiting'), L('Sì, sempre', 'Yes, always'), L('Solo su Linux', 'Only on Linux'), L('Solo con i numeri', 'Only with numbers')], 0, L('Per i calcoli servono processi.', 'Computation needs processes.')),
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
    ],
];
