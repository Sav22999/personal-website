<?php
/** Python intermediate: comprehensions, files, errors, classes and modules, inheritance, lambdas, standard library and command-line scripts. */

/** The three parts of a comprehension and the order they run in, with the values going through. */
if (!function_exists('fig_pyint_comp')) {
    function fig_pyint_comp(array $t): string
    {
        $nums = '';
        foreach (range(1, 6) as $i => $n) {
            $x = 30 + $i * 46;
            $even = $n % 2 === 0;
            $nums .= '<rect x="' . $x . '" y="150" width="38" height="38" rx="8" fill="' . ($even ? '#DCEBFF' : '#F1F0F8') . '" stroke="' . ($even ? '#2F8CF0' : '#C9C7DE') . '" stroke-width="2"' . ($even ? '' : ' stroke-dasharray="4 4"') . '/>'
                . '<text x="' . ($x + 19) . '" y="175" text-anchor="middle" fill="' . ($even ? '#1E1C52' : '#8A87B0') . '">' . $n . '</text>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="ui-monospace, monospace" font-size="16" fill="#1E1C52">
<defs><marker id="ac" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="22" y="56" font-size="22">[</text>
<rect x="40" y="30" width="120" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="100" y="56" text-anchor="middle">n * 10</text>
<rect x="172" y="30" width="200" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="272" y="56" text-anchor="middle">for n in numeri</text>
<rect x="384" y="30" width="200" height="40" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="484" y="56" text-anchor="middle">if n % 2 == 0</text>
<text x="594" y="56" font-size="22">]</text>
<g font-family="system-ui, sans-serif" font-size="13" text-anchor="middle">
<text x="100" y="92" fill="#0B7F5C">3. {$t[0]}</text><text x="272" y="92" fill="#1B66BC">1. {$t[1]}</text><text x="484" y="92" fill="#B84508">2. {$t[2]}</text>
<text x="154" y="216" fill="#5B5880">numeri</text><text x="420" y="134" fill="#B84508">{$t[3]}</text><text x="540" y="216" fill="#0B7F5C">{$t[4]}</text>
</g>
$nums
<line x1="312" y1="169" x2="384" y2="169" stroke="#1E1C52" stroke-width="2" marker-end="url(#ac)"/>
<text x="348" y="160" text-anchor="middle" font-size="13" fill="#B84508">2 4 6</text>
<rect x="392" y="150" width="56" height="38" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="420" y="175" text-anchor="middle">×10</text>
<line x1="452" y1="169" x2="474" y2="169" stroke="#1E1C52" stroke-width="2" marker-end="url(#ac)"/>
<rect x="480" y="150" width="140" height="38" rx="8" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/><text x="550" y="175" text-anchor="middle">[20, 40, 60]</text>
</svg>
SVG;
    }
}

/** try, except, else and finally: two paths, one shared ending. */
if (!function_exists('fig_pyint_try')) {
    function fig_pyint_try(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><marker id="at" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-family="ui-monospace, monospace" font-size="15" font-weight="700" text-anchor="middle">
<rect x="20" y="88" width="130" height="54" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="85" y="120">try</text>
<rect x="300" y="24" width="130" height="54" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="365" y="56">else</text>
<rect x="300" y="152" width="130" height="54" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/><text x="365" y="184">except</text>
<rect x="490" y="88" width="130" height="54" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="555" y="120">finally</text>
</g>
<path d="M152 104 C220 90 240 51 294 51" fill="none" stroke="#0B7F5C" stroke-width="2" marker-end="url(#at)"/>
<path d="M152 126 C220 140 240 179 294 179" fill="none" stroke="#F03E82" stroke-width="2" marker-end="url(#at)"/>
<path d="M432 51 C460 51 470 100 484 106" fill="none" stroke="#1E1C52" stroke-width="2" marker-end="url(#at)"/>
<path d="M432 179 C460 179 470 130 484 124" fill="none" stroke="#1E1C52" stroke-width="2" marker-end="url(#at)"/>
<text x="200" y="62" text-anchor="middle" fill="#0B7F5C">{$t[0]}</text>
<text x="200" y="180" text-anchor="middle" fill="#B8255F">{$t[1]}</text>
<text x="555" y="164" text-anchor="middle" fill="#5B33C9">{$t[2]}</text>
</svg>
SVG;
    }
}

/** A method call: the object before the dot becomes self. */
if (!function_exists('fig_pyint_self')) {
    function fig_pyint_self(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="ui-monospace, monospace" font-size="18" fill="#1E1C52">
<defs><marker id="as" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="180" y="20" width="90" height="38" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="225" y="46" text-anchor="middle">conto</text>
<text x="276" y="46">.versa(</text>
<rect x="360" y="20" width="44" height="38" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="382" y="46" text-anchor="middle">10</text>
<text x="408" y="46">)</text>
<text x="96" y="160">def versa(</text>
<rect x="210" y="134" width="70" height="38" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="245" y="160" text-anchor="middle">self</text>
<text x="284" y="160">,</text>
<rect x="302" y="134" width="110" height="38" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="357" y="160" text-anchor="middle">importo</text>
<text x="416" y="160">):</text>
<line x1="228" y1="62" x2="243" y2="128" stroke="#7048E8" stroke-width="2" marker-end="url(#as)"/>
<line x1="380" y1="62" x2="362" y2="128" stroke="#0B7F5C" stroke-width="2" marker-end="url(#as)"/>
<text x="222" y="100" text-anchor="end" font-family="system-ui, sans-serif" font-size="14" fill="#5B33C9">{$t[0]}</text>
<text x="320" y="200" text-anchor="middle" font-family="system-ui, sans-serif" font-size="14" fill="#5B5880">{$t[1]}</text>
</svg>
SVG;
    }
}

/** Two projects, each with its own virtual environment and versions, and the system Python left clean. */
if (!function_exists('fig_pyint_venv')) {
    function fig_pyint_venv(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="16" y="20" width="290" height="140" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="34" y="46" font-weight="700" fill="#1B66BC">{$t[0]} A</text>
<rect x="34" y="60" width="254" height="84" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2" stroke-dasharray="6 4"/>
<g font-family="ui-monospace, monospace" font-size="14"><text x="50" y="84" font-weight="700">.venv/</text><text x="50" y="106">requests 2.31</text><text x="50" y="128">flask 3.1</text></g>
<rect x="334" y="20" width="290" height="140" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="352" y="46" font-weight="700" fill="#0B7F5C">{$t[0]} B</text>
<rect x="352" y="60" width="254" height="84" rx="10" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="6 4"/>
<g font-family="ui-monospace, monospace" font-size="14"><text x="368" y="84" font-weight="700">.venv/</text><text x="368" y="106">requests 2.32</text><text x="368" y="128">pandas 3.0</text></g>
<rect x="16" y="184" width="608" height="48" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="34" y="214" font-weight="700">{$t[1]}</text>
<text x="606" y="214" text-anchor="end" font-size="14" fill="#5B5880">{$t[2]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'python-intermediate', 'family' => 'python', 'after' => 'python-base',
    'title' => L('Python intermedio', 'Intermediate Python'), 'short' => L('Python intermedio', 'Python Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Comprehension, file, eccezioni, classi ed ereditarietà, moduli e ambienti virtuali, lambda, libreria standard e script da terminale: Python per progetti veri.', 'Comprehensions, files, exceptions, classes and inheritance, modules and virtual environments, lambdas, the standard library and command-line scripts: Python for real projects.'),
    'lessons' => [
        lesson('comprehensions', L('Comprehension', 'Comprehensions'), [
            P('Una **list comprehension** costruisce una lista in una riga: `[espressione for x in sequenza if condizione]`. Esistono anche per dizionari e insiemi.', 'A **list comprehension** builds a list in one line: `[expression for x in sequence if condition]`. They also exist for dicts and sets.'),
            Fig(L(fig_pyint_comp(['espressione', 'sorgente', 'filtro', 'solo i pari', 'risultato']), fig_pyint_comp(['expression', 'source', 'filter', 'evens only', 'result'])),
                L('La comprehension [n * 10 for n in numeri if n % 2 == 0] divisa in tre riquadri: espressione, sorgente e filtro, numerati 3, 1 e 2 secondo l’ordine in cui agiscono. Sotto, i numeri da 1 a 6: i dispari sono grigi e scartati, 2, 4 e 6 passano, vengono moltiplicati per 10 e danno [20, 40, 60].', 'The comprehension [n * 10 for n in numeri if n % 2 == 0] split into three boxes: expression, source and filter, numbered 3, 1 and 2 by the order they act in. Below, the numbers 1 to 6: the odd ones are grey and dropped, 2, 4 and 6 pass, are multiplied by 10 and give [20, 40, 60].'),
                L('Si legge da sinistra, ma gira così: prendi, filtra, trasforma.', 'You read it from the left, but it runs like this: take, filter, transform.')),
            C('numeri = [1, 2, 3, 4, 5, 6]
pari = [n for n in numeri if n % 2 == 0]
quadrati = {n: n * n for n in numeri}
nomi = ["ada", "bea"]
maiuscoli = [n.capitalize() for n in nomi]', 'python'),
            P('Sono più brevi e spesso più veloci di un ciclo con `append`, ma se diventano lunghe meglio tornare al `for`.', 'They are shorter and often faster than a loop with `append`, but if they get long go back to a `for`.'),
            P('Con le tonde invece delle quadre ottieni un’**espressione generatrice**: `sum(n * n for n in numeri)` calcola la somma senza costruire una lista in memoria, utile con molti dati. Un errore frequente è usare una comprehension solo per i suoi effetti, come `[print(n) for n in numeri]`: crea una lista di `None` che nessuno usa. Se non ti serve la lista, scrivi un `for`.', 'With round brackets instead of square ones you get a **generator expression**: `sum(n * n for n in numeri)` computes the sum without building a list in memory, useful with lots of data. A common mistake is using a comprehension only for its side effects, as in `[print(n) for n in numeri]`: it builds a list of `None` that nobody uses. If you do not need the list, write a `for`.'),
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
with open("dati.json", encoding="utf-8") as f:
    dati = json.load(f)', 'python'),
            P('Indica sempre `encoding="utf-8"` per le lettere accentate: Python 3.15 usa UTF-8 di serie, ma le versioni precedenti, soprattutto su Windows, possono scegliere un’altra codifica. Il modulo `json` legge e scrive dati strutturati.', 'Always give `encoding="utf-8"` for accented letters: Python 3.15 uses UTF-8 by default, but earlier versions, especially on Windows, may pick another encoding. The `json` module reads and writes structured data.'),
            P('Attenzione a `"w"`: se il file esiste già, il suo contenuto viene cancellato appena lo apri, prima ancora di scrivere. Scorrere il file con `for riga in f` legge una riga alla volta, anche con file enormi; ogni riga però finisce con `\n`, ed è per questo che si usa `strip()`. Per salvare dati in JSON c’è `json.dump(dati, f, ensure_ascii=False, indent=2)`, che lascia gli accenti leggibili e va a capo in modo ordinato.', 'Careful with `"w"`: if the file already exists, its content is wiped as soon as you open it, before you even write. Walking the file with `for riga in f` reads one line at a time, even with huge files; each line ends with `\n`, though, which is why `strip()` is used. To save data as JSON there is `json.dump(dati, f, ensure_ascii=False, indent=2)`, which keeps accents readable and breaks lines tidily.'),
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
            P('Esiste anche un blocco `else`, che gira solo se nel `try` non è successo niente: tieni nel `try` solo la riga che può fallire, e il resto in `else`. Le eccezioni che incontrerai più spesso sono `ValueError` (valore sbagliato), `TypeError` (tipo sbagliato), `KeyError` e `IndexError` (chiave o indice assente), `FileNotFoundError` e `ZeroDivisionError`. Leggi il messaggio dal basso: l’ultima riga dice il tipo di errore, quelle sopra la strada che ha fatto.', 'There is also an `else` block, which runs only if nothing went wrong in the `try`: keep in the `try` only the line that can fail, and the rest in `else`. The exceptions you will meet most often are `ValueError` (wrong value), `TypeError` (wrong type), `KeyError` and `IndexError` (missing key or index), `FileNotFoundError` and `ZeroDivisionError`. Read the message from the bottom: the last line says the type of error, the ones above the path it took.'),
            Fig(L(fig_pyint_try(['nessun errore', 'eccezione', 'gira sempre']), fig_pyint_try(['no error', 'exception', 'always runs'])),
                L('Dal blocco try partono due frecce: se non ci sono errori si va a else, se c’è un’eccezione si va a except. Entrambe le strade finiscono in finally, che gira sempre.', 'Two arrows leave the try block: with no errors it goes to else, with an exception it goes to except. Both paths end in finally, which always runs.'),
                L('`else` e `except` si escludono, `finally` arriva comunque.', '`else` and `except` exclude each other, `finally` comes anyway.')),
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
            Fig(L(fig_pyint_self(['Python lo passa da solo', 'L’oggetto prima del punto diventa self, gli argomenti seguono in ordine.']), fig_pyint_self(['Python passes it for you', 'The object before the dot becomes self, the arguments follow in order.'])),
                L('In alto la chiamata conto.versa(10), in basso la definizione def versa(self, importo). Una freccia porta conto in self, un’altra porta 10 in importo.', 'At the top the call conto.versa(10), at the bottom the definition def versa(self, importo). One arrow carries conto into self, another carries 10 into importo.'),
                L('`conto.versa(10)` equivale a `Conto.versa(conto, 10)`.', '`conto.versa(10)` is the same as `Conto.versa(conto, 10)`.')),
            P('Ogni oggetto ha i suoi attributi: versare su un conto non cambia il saldo di un altro. La `@dataclass` scrive anche `__repr__`, così `print(Punto(1, 2))` mostra `Punto(x=1, y=2)` invece di un indirizzo di memoria, e `__eq__`, così due punti con le stesse coordinate risultano uguali con `==`. Usala per gli oggetti che servono soprattutto a tenere dati.', 'Each object has its own attributes: depositing into one account does not change the balance of another. The `@dataclass` also writes `__repr__`, so `print(Punto(1, 2))` shows `Punto(x=1, y=2)` instead of a memory address, and `__eq__`, so two points with the same coordinates compare equal with `==`. Use it for objects that mainly hold data.'),
            Tip('In una dataclass non scrivere `voti: list = []`: Python lo vieta, perché la stessa lista finirebbe condivisa da tutti gli oggetti. Si scrive `voti: list = field(default_factory=list)`.', 'In a dataclass do not write `voti: list = []`: Python forbids it, because the same list would end up shared by every object. You write `voti: list = field(default_factory=list)`.'),
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
            P('Un ambiente virtuale è una cartella (qui `.venv`) con un Python e le librerie di un solo progetto: due progetti possono usare versioni diverse della stessa libreria senza disturbarsi. Si attiva in ogni nuovo terminale; `pip freeze > requirements.txt` salva l’elenco delle versioni e `pip install -r requirements.txt` le reinstalla su un altro computer. La cartella `.venv` non va su git: si ricrea.', 'A virtual environment is a folder (here `.venv`) with a Python and the libraries of a single project: two projects can use different versions of the same library without getting in each other’s way. You activate it in every new terminal; `pip freeze > requirements.txt` saves the list of versions and `pip install -r requirements.txt` reinstalls them on another computer. The `.venv` folder does not go into git: it is recreated.'),
            Fig(L(fig_pyint_venv(['Progetto', 'Python di sistema', 'resta pulito: niente pip install qui']), fig_pyint_venv(['Project', 'System Python', 'stays clean: no pip install here'])),
                L('Due riquadri affiancati, Progetto A e Progetto B, ciascuno con la sua cartella .venv: A ha requests 2.31 e flask 3.1, B ha requests 2.32 e pandas 3.0. Sotto, una fascia grigia per il Python di sistema, che resta pulito.', 'Two boxes side by side, Project A and Project B, each with its own .venv folder: A has requests 2.31 and flask 3.1, B has requests 2.32 and pandas 3.0. Below, a grey band for the system Python, which stays clean.'),
                L('Ogni progetto ha le sue versioni, isolate dalle altre.', 'Each project has its own versions, isolated from the others.')),
            Tip('Non chiamare un tuo file come un modulo della libreria standard, per esempio `random.py` o `json.py`: Python cerca prima nella cartella dello script e importerebbe il tuo file al posto dell’originale.', 'Do not name one of your files like a standard library module, such as `random.py` or `json.py`: Python looks in the script’s folder first and would import your file instead of the real one.'),
        ], [
            Q('m1', L('Con quale comando installi una libreria?', 'Which command installs a library?'), ['pip install', 'npm install', 'python get', 'import install'], 0, L('`pip` è il gestore dei pacchetti.', '`pip` is the package manager.')),
            Q('m2', L('A che cosa serve un ambiente virtuale?', 'What is a virtual environment for?'), [L('A tenere separate le librerie di ogni progetto', 'To keep each project’s libraries apart'), L('A rendere Python più veloce', 'To make Python faster'), L('A virtualizzare il computer', 'To virtualise the computer'), L('A cifrare il codice', 'To encrypt code')], 0, L('Ogni progetto, le sue versioni.', 'Each project, its own versions.')),
            Q('m3', L('Quando è vero `__name__ == "__main__"`?', 'When is `__name__ == "__main__"` true?'), [L('Quando il file è lanciato direttamente', 'When the file is run directly'), L('Quando è importato', 'When it is imported'), L('Sempre', 'Always'), L('Mai', 'Never')], 0, L('Utile per i piccoli test.', 'Handy for small tests.')),
        ], task('python', L('Importa `sqrt` da `math` e stampa `sqrt(81)`.', 'Import `sqrt` from `math` and print `sqrt(81)`.'), '', [re('from\s+math\s+import\s+sqrt', L('Importi `sqrt`', 'You import `sqrt`')), re('print\s*\(\s*sqrt\s*\(\s*81\s*\)\s*\)', L('Stampi la radice', 'You print the root'))], '', 'from math import sqrt
print(sqrt(81))'), [
            dOrder(pSort(), ['python -m venv .venv', 'source .venv/bin/activate', 'pip install requests', 'pip freeze > requirements.txt'], L('Crea, attiva, installa, annota.', 'Create, activate, install, record.')),
            dMatch(pMatch(), [['import', L('Carica un modulo', 'Loads a module')], ['pip', L('Installa pacchetti', 'Installs packages')], ['venv', L('Ambiente separato', 'Separate environment')], ['requirements.txt', L('Elenco delle dipendenze', 'List of dependencies')]], L('Gli attrezzi di un progetto Python.', 'The tools of a Python project.')),
        ]),
        lesson('inheritance', L('Ereditarietà e metodi speciali', 'Inheritance and special methods'), [
            P('Con `class Cane(Animale):` una classe **eredita** attributi e metodi da un’altra e può **ridefinirli**. `super()` dà accesso alla classe genitore, ad esempio per riusarne il costruttore. `isinstance` controlla se un oggetto è di un certo tipo, genitori compresi.', 'With `class Cane(Animale):` a class **inherits** attributes and methods from another and can **redefine** them. `super()` gives access to the parent class, for example to reuse its constructor. `isinstance` checks whether an object is of a given type, parents included.'),
            C('class Animale:
    def __init__(self, nome):
        self.nome = nome

    def verso(self):
        return "..."

    def __str__(self):
        return f"{self.nome} dice {self.verso()}"

class Cane(Animale):
    def __init__(self, nome, razza):
        super().__init__(nome)
        self.razza = razza

    def verso(self):
        return "Bau"

print(Cane("Fido", "beagle"))                      # Fido dice Bau
print(isinstance(Cane("Rex", "lupo"), Animale))    # True', 'python'),
            P('I metodi con il doppio trattino, come `__str__`, `__eq__` e `__len__`, sono **metodi speciali**: Python li chiama da solo quando usi `print`, `==` o `len` sull’oggetto.', 'Methods with double underscores, such as `__str__`, `__eq__` and `__len__`, are **special methods**: Python calls them by itself when you use `print`, `==` or `len` on the object.'),
        ], [
            Q('n1', L('Come eredita `Cane` da `Animale`?', 'How does `Cane` inherit from `Animale`?'), ['class Cane extends Animale:', 'class Cane(Animale):', 'class Cane < Animale:', 'class Cane: Animale'], 1, L('Il genitore va tra le tonde.', 'The parent goes in brackets.')),
            Q('n2', L('Quale metodo usa `print(oggetto)` per il testo da mostrare?', 'Which method does `print(obj)` use for the text to show?'), ['__init__', '__print__', '__str__', '__text__'], 2, L('Se manca, Python ripiega su `__repr__`.', 'If missing, Python falls back to `__repr__`.')),
            Q('n3', L('A che cosa serve `super().__init__(nome)`?', 'What is `super().__init__(nome)` for?'), [L('A creare un secondo oggetto', 'To create a second object'), L('A rendere privato l’attributo nome', 'To make the nome attribute private'), L('A copiare la classe genitore', 'To copy the parent class'), L('A eseguire il costruttore della classe genitore', 'To run the parent class’s constructor')], 3, L('Così non riscrivi ciò che il genitore già fa.', 'So you do not rewrite what the parent already does.')),
        ], task('python', L('Crea `class Studente(Persona)` con un metodo `__str__` che restituisce `f"Studente {self.nome}"`.', 'Create `class Studente(Persona)` with a `__str__` method returning `f"Studente {self.nome}"`.'), 'class Persona:
    def __init__(self, nome):
        self.nome = nome
', [re('class\s+Studente\s*\(\s*Persona\s*\)\s*:', L('Studente eredita da Persona', 'Studente inherits from Persona')), re('def\s+__str__\s*\(\s*self\s*\)\s*(->\s*str\s*)?:', L('C’è __str__', 'There is __str__')), re('return\s+f["\']Studente \{self\.nome\}["\']', L('Restituisce il testo giusto', 'It returns the right text'))], '', 'class Studente(Persona):
    def __str__(self):
        return f"Studente {self.nome}"'), [
            dMatch(pMatch(), [['__init__', L('Prepara l’oggetto', 'Sets up the object')], ['__str__', L('Testo per print', 'Text for print')], ['__eq__', L('Confronto con ==', 'Comparison with ==')], ['__len__', L('Risposta a len()', 'Answer to len()')], ['super()', L('La classe genitore', 'The parent class')]], L('Metodi speciali ed ereditarietà.', 'Special methods and inheritance.')),
            dBug(pBug(), ['class Gatto(Animale):', '    def __init__(self, nome, colore):', '        self.colore = colore', '        super.__init__(nome)'], 3, '        super().__init__(nome)', L('`super` va chiamato con le parentesi: `super()`.', '`super` must be called with brackets: `super()`.')),
        ]),
        lesson('lambda-sorting', L('Lambda e ordinamenti', 'Lambdas and sorting'), [
            P('Una **lambda** è una piccola funzione anonima di una sola espressione: `lambda s: s[1]`. Si usa soprattutto con il parametro `key`, che dice a `sorted`, `min` e `max` **in base a che cosa** confrontare gli elementi.', 'A **lambda** is a small anonymous function of a single expression: `lambda s: s[1]`. It is mostly used with the `key` parameter, which tells `sorted`, `min` and `max` **by what** to compare items.'),
            C('studenti = [("Ada", 9), ("Bea", 7), ("Cleo", 8)]

per_voto = sorted(studenti, key=lambda s: s[1], reverse=True)
print(per_voto[0])                      # (\'Ada\', 9)

nomi = ["bea", "Ada", "cleo"]
print(sorted(nomi, key=str.lower))      # [\'Ada\', \'bea\', \'cleo\']

migliore = max(studenti, key=lambda s: s[1])
quadrati = list(map(lambda n: n * n, [1, 2, 3]))   # [1, 4, 9]
promossi = list(filter(lambda s: s[1] >= 8, studenti))', 'python'),
            P('In Python le funzioni sono valori: puoi passare `str.lower` o `len` come `key` senza chiamarle. `map` e `filter` restituiscono iteratori; spesso una comprehension è più leggibile.', 'In Python functions are values: you can pass `str.lower` or `len` as `key` without calling them. `map` and `filter` return iterators; a comprehension is often more readable.'),
        ], [
            Q('w1', L('Che cosa fa `key=lambda s: s[1]` in `sorted`?', 'What does `key=lambda s: s[1]` do in `sorted`?'), [L('Ordina in base al secondo elemento di ogni tupla', 'Sorts by the second item of each tuple'), L('Tiene solo il secondo elemento', 'Keeps only the second item'), L('Ordina dal più grande al più piccolo', 'Sorts from largest to smallest'), L('Salta il primo elemento della lista', 'Skips the first item of the list')], 0, L('Per l’ordine inverso serve `reverse=True`.', 'Reverse order needs `reverse=True`.')),
            Q('w2', L('Quante espressioni può contenere una lambda?', 'How many expressions can a lambda contain?'), [L('Quante vuoi', 'As many as you like'), L('Una sola', 'Just one'), L('Due', 'Two'), L('Nessuna', 'None')], 1, L('Per più righe scrivi una funzione con `def`.', 'For several lines write a function with `def`.')),
            Q('w3', L('Che cosa dà `max(["kiwi", "banana", "fico"], key=len)`?', 'What does `max(["kiwi", "banana", "fico"], key=len)` give?'), ['"kiwi"', '"fico"', '"banana"', '6'], 2, L('Restituisce l’elemento, non la lunghezza.', 'It returns the item, not the length.')),
        ], task('python', L('Ordina `prodotti` (coppie nome, prezzo) dal più economico con `sorted` e una `lambda`, salvando in `economici`.', 'Sort `prodotti` (name, price pairs) from cheapest with `sorted` and a `lambda`, saving into `economici`.'), 'prodotti = [("tazza", 8.5), ("penna", 1.2), ("zaino", 30.0)]
', [re('economici\s*=\s*sorted\s*\(\s*prodotti\s*,\s*key\s*=\s*lambda\s+(\w+)\s*:\s*\1\s*\[\s*1\s*\]\s*\)', L('Ordini per prezzo', 'You sort by price'))], '', 'economici = sorted(prodotti, key=lambda p: p[1])'), [
            dBlanks(pFill(), 'parole = ["sole", "a", "mare"]
ordinate = sorted(parole, ___=len)
lunga = max(parole, key=___ p: len(p))
corte = list(___(lambda p: len(p) < 3, parole))', ['key', 'lambda', 'filter'], ['by', 'def', 'sum'], L('Criterio, funzione anonima, filtro.', 'Criterion, anonymous function, filter.')),
            dFlash(pFlash(), [[L('`sorted` accetta `reverse=True`', '`sorted` accepts `reverse=True`'), true], [L('Una lambda può contenere un ciclo su più righe', 'A lambda can hold a multi-line loop'), false], [L('Una funzione si può passare come argomento', 'A function can be passed as an argument'), true], [L('`map` restituisce sempre una lista', '`map` always returns a list'), false]], L('Funzioni come valori.', 'Functions as values.')),
        ]),
        lesson('stdlib', L('Libreria standard: pathlib, datetime, collections', 'Standard library: pathlib, datetime, collections'), [
            P('Python arriva «con le batterie incluse». `pathlib.Path` tratta i percorsi come oggetti: `/` li unisce, `glob` cerca i file, `read_text` legge. `datetime` e `timedelta` fanno i conti con le date. `collections` offre `Counter`, che conta, e `defaultdict`, che crea da solo il valore mancante.', 'Python comes “with batteries included”. `pathlib.Path` treats paths as objects: `/` joins them, `glob` finds files, `read_text` reads. `datetime` and `timedelta` do date arithmetic. `collections` offers `Counter`, which counts, and `defaultdict`, which creates missing values by itself.'),
            C('from pathlib import Path
from datetime import date, datetime, timedelta
from collections import Counter, defaultdict

cartella = Path("dati")
for file in cartella.glob("*.csv"):
    print(file.name, file.stat().st_size)
testo = (cartella / "note.txt").read_text(encoding="utf-8")

scadenza = date.today() + timedelta(days=30)
print(scadenza.strftime("%d/%m/%Y"))
quando = datetime.fromisoformat("2026-11-17T09:30")

parole = "il gatto e il cane e il topo".split()
print(Counter(parole).most_common(1))    # [(\'il\', 3)]
per_iniziale = defaultdict(list)
for p in parole:
    per_iniziale[p[0]].append(p)', 'python'),
            P('Prima di installare una libreria esterna, controlla la libreria standard: spesso quello che cerchi c’è già, documentato e mantenuto con Python stesso.', 'Before installing an external library, check the standard library: what you need is often already there, documented and maintained with Python itself.'),
        ], [
            Q('z1', L('Che cosa fa `Path("dati") / "note.txt"`?', 'What does `Path("dati") / "note.txt"` do?'), [L('Divide due numeri', 'Divides two numbers'), L('Unisce i percorsi in dati/note.txt', 'Joins the paths into dati/note.txt'), L('Cancella il file note.txt', 'Deletes the file note.txt'), L('Apre il file in scrittura', 'Opens the file for writing')], 1, L('Funziona allo stesso modo su Windows, macOS e Linux.', 'It works the same on Windows, macOS and Linux.')),
            Q('z2', L('Come aggiungi 7 giorni a una data?', 'How do you add 7 days to a date?'), ['data + 7', 'data.add(7)', 'data + timedelta(days=7)', 'data.days += 7'], 2, L('`timedelta` rappresenta una durata.', '`timedelta` represents a duration.')),
            Q('z3', L('Che cosa fa `Counter(["a", "b", "a"])`?', 'What does `Counter(["a", "b", "a"])` do?'), [L('Ordina la lista', 'Sorts the list'), L('Toglie i doppioni', 'Removes duplicates'), L('Conta solo gli elementi diversi', 'Counts only the distinct items'), L('Conta le occorrenze: a 2, b 1', 'Counts occurrences: a 2, b 1')], 3, L('`most_common` dà i più frequenti.', '`most_common` gives the most frequent.')),
        ], task('python', L('Con `Counter` conta i `colori` e stampa il più frequente con `most_common(1)`.', 'With `Counter` count the `colori` and print the most frequent with `most_common(1)`.'), 'colori = ["rosso", "blu", "rosso", "verde"]
', [re('from\s+collections\s+import\s+([\w\s,]*\b)?Counter|import\s+collections', L('Importi Counter', 'You import Counter')), re('Counter\s*\(\s*colori\s*\)', L('Conti i colori', 'You count the colours')), re('most_common\s*\(\s*1\s*\)', L('Prendi il più frequente', 'You take the most frequent'))], '', 'from collections import Counter
conteggio = Counter(colori)
print(conteggio.most_common(1))'), [
            dMatch(pMatch(), [['Path', L('Percorsi di file', 'File paths')], ['timedelta', L('Durata da sommare', 'Duration to add')], ['Counter', L('Conta le occorrenze', 'Counts occurrences')], ['defaultdict', L('Valore predefinito automatico', 'Automatic default value')], ['strftime', L('Data in testo', 'Date to text')]], L('Attrezzi della libreria standard.', 'Standard library tools.')),
            dOrder(pSort(), ['from pathlib import Path', 'cartella = Path("foto")', 'for f in cartella.glob("*.jpg"):', '    print(f.name)'], L('Importa, scegli la cartella, cerca, stampa.', 'Import, pick the folder, search, print.')),
        ]),
        lesson('cli', L('Script da riga di comando', 'Command-line scripts'), [
            P('`argparse` legge gli argomenti scritti dopo il nome dello script e crea da solo l’aiuto `--help`. `logging` è meglio di `print` per i messaggi di servizio: ha **livelli** (`DEBUG`, `INFO`, `WARNING`, `ERROR`) e si può zittire o mandare su file senza toccare il codice.', '`argparse` reads the arguments written after the script name and builds the `--help` text by itself. `logging` beats `print` for service messages: it has **levels** (`DEBUG`, `INFO`, `WARNING`, `ERROR`) and can be silenced or sent to a file without touching the code.'),
            C(L('import argparse
import logging

logging.basicConfig(level=logging.INFO, format="%(levelname)s %(message)s")
log = logging.getLogger(__name__)

def main():
    parser = argparse.ArgumentParser(description="Conta le righe di un file")
    parser.add_argument("file")
    parser.add_argument("--vuote", action="store_true", help="conta anche le righe vuote")
    args = parser.parse_args()

    with open(args.file, encoding="utf-8") as f:
        righe = [r for r in f if args.vuote or r.strip()]
    log.info("Trovate %d righe", len(righe))

if __name__ == "__main__":
    main()', 'import argparse
import logging

logging.basicConfig(level=logging.INFO, format="%(levelname)s %(message)s")
log = logging.getLogger(__name__)

def main():
    parser = argparse.ArgumentParser(description="Count the lines of a file")
    parser.add_argument("file")
    parser.add_argument("--vuote", action="store_true", help="count empty lines too")
    args = parser.parse_args()

    with open(args.file, encoding="utf-8") as f:
        righe = [r for r in f if args.vuote or r.strip()]
    log.info("Found %d lines", len(righe))

if __name__ == "__main__":
    main()'), 'python'),
            C('python conta.py note.txt --vuote
python conta.py --help', 'bash', L('<pre>INFO Trovate 12 righe

usage: conta.py [-h] [--vuote] file

Conta le righe di un file

positional arguments:
  file

options:
  -h, --help  show this help message and exit
  --vuote     conta anche le righe vuote</pre>', '<pre>INFO Found 12 lines

usage: conta.py [-h] [--vuote] file

Count the lines of a file

positional arguments:
  file

options:
  -h, --help  show this help message and exit
  --vuote     count empty lines too</pre>')),
            P('Gli argomenti senza trattini, come `file`, sono **posizionali** e obbligatori; quelli con `--` sono **opzioni** facoltative. `action="store_true"` crea un interruttore che vale `True` se lo scrivi e `False` se no, mentre `type=int` converte il testo in numero e segnala da solo un valore sbagliato. Con `logging` scegli il livello all’avvio: in sviluppo `DEBUG` mostra tutto, in produzione `WARNING` lascia solo i problemi.', 'Arguments without dashes, such as `file`, are **positional** and required; those with `--` are optional **options**. `action="store_true"` creates a switch that is `True` if you write it and `False` if you do not, while `type=int` converts the text into a number and reports a wrong value by itself. With `logging` you choose the level at start-up: in development `DEBUG` shows everything, in production `WARNING` leaves only the problems.'),
        ], [
            Q('q1', L('Che cosa crea in automatico `argparse`?', 'What does `argparse` create automatically?'), [L('Un’interfaccia grafica', 'A graphical interface'), L('Il messaggio di aiuto --help', 'The --help message'), L('Un file di configurazione', 'A configuration file'), L('Un ambiente virtuale', 'A virtual environment')], 1, L('Con la descrizione di ogni argomento.', 'With the description of every argument.')),
            Q('q2', L('Che cosa fa `action="store_true"`?', 'What does `action="store_true"` do?'), [L('Rende obbligatorio l’argomento', 'Makes the argument required'), L('Salva l’argomento in un file', 'Saves the argument to a file'), L('L’opzione vale True se è presente, altrimenti False', 'The option is True if present, otherwise False'), L('Converte l’argomento in numero', 'Converts the argument to a number')], 2, L('È un interruttore senza valore.', 'It is a switch with no value.')),
            Q('q3', L('Perché usare `logging` invece di `print` per i messaggi di servizio?', 'Why use `logging` instead of `print` for service messages?'), [L('Ha livelli e si configura senza cambiare il codice', 'It has levels and is configured without changing code'), L('È l’unico modo di scrivere sul terminale', 'It is the only way to write to the terminal'), L('Rende il programma più veloce', 'It makes the program faster'), L('`print` non funziona negli script', '`print` does not work in scripts')], 0, L('Per il risultato vero del programma `print` va benissimo.', 'For the program’s real output `print` is fine.')),
        ], task('python', L('Aggiungi al `parser` l’argomento obbligatorio `nome` e l’opzione `--volte` di tipo `int` con valore predefinito `1`.', 'Add to `parser` the required argument `nome` and the `--volte` option of type `int` with default `1`.'), 'import argparse
parser = argparse.ArgumentParser()
', [re('parser\.add_argument\s*\(\s*["\']nome["\']', L('C’è l’argomento nome', 'There is the nome argument')), re('parser\.add_argument\s*\(\s*["\']--volte["\'](?=[^)]*type\s*=\s*int)(?=[^)]*default\s*=\s*1\b)', L('C’è l’opzione --volte', 'There is the --volte option'))], '', 'parser.add_argument("nome")
parser.add_argument("--volte", type=int, default=1)'), [
            dMatch(pMatch(), [['DEBUG', L('Dettagli per chi sviluppa', 'Details for developers')], ['INFO', L('Tutto procede', 'All is going well')], ['WARNING', L('Qualcosa di strano', 'Something odd')], ['ERROR', L('Qualcosa è fallito', 'Something failed')]], L('I livelli del logging.', 'Logging levels.')),
            dOrder(pSort(), ['import argparse', 'parser = argparse.ArgumentParser()', 'parser.add_argument("file")', 'args = parser.parse_args()', 'print(args.file)'], L('Importa, prepara, dichiara, leggi, usa.', 'Import, set up, declare, parse, use.')),
        ]),
    ],
];
