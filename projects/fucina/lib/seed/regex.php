<?php
/** Regular expressions: literals and classes, quantifiers and anchors, groups and alternatives, using them in code. */

return [
    'id' => 'regex', 'family' => 'regex', 'after' => null,
    'title' => L('Espressioni regolari', 'Regular Expressions'), 'short' => L('Regex', 'Regex'), 'level' => L('Base', 'Basics'),
    'desc' => L('Cercare e validare testo con le regex: classi di caratteri, quantificatori, ancore, gruppi e alternative, e come usarle in JavaScript e Python.', 'Searching and validating text with regex: character classes, quantifiers, anchors, groups and alternatives, and how to use them in JavaScript and Python.'),
    'lessons' => [
        lesson('classes', L('Caratteri e classi', 'Characters and classes'), [
            P('Un’**espressione regolare** (regex) descrive un modello di testo. La maggior parte dei caratteri vale per sé stessa (`gatto` trova “gatto”); alcuni sono speciali. Le **classi** indicano un insieme di caratteri ammessi in una posizione.', 'A **regular expression** (regex) describes a text pattern. Most characters stand for themselves (`gatto` finds “gatto”); some are special. **Classes** state a set of characters allowed in one position.'),
            C('.        qualsiasi carattere (tranne a capo)
\\d       una cifra            \\D  una non-cifra
\\w       lettera, cifra o _   \\W  il contrario
\\s       spazio, tab, a capo  \\S  il contrario
[aeiou]  una vocale           [^0-9]  tutto tranne le cifre
[A-Za-z] una lettera latina   \\.   un punto vero (escape)', 'text'),
            P('Per cercare un carattere speciale come testo (`.`, `?`, `+`, `(`, `[`, `\\`) va **preceduto da una barra rovesciata**: `\\.` trova un punto vero, mentre `.` trova qualsiasi carattere.', 'To search for a special character as text (`.`, `?`, `+`, `(`, `[`, `\\`) it must be **preceded by a backslash**: `\\.` finds a real dot, while `.` finds any character.'),
        ], [
            Q('c1', L('Che cosa trova `\\d`?', 'What does `\\d` match?'), [L('Una cifra', 'A digit'), L('Una lettera', 'A letter'), L('Uno spazio', 'A space'), L('Un punto', 'A dot')], 0, L('d = digit.', 'd = digit.')),
            Q('c2', L('Che cosa trova `[^0-9]`?', 'What does `[^0-9]` match?'), [L('Qualsiasi carattere tranne le cifre', 'Any character except digits'), L('Solo le cifre', 'Only digits'), L('L’inizio della riga', 'Start of line'), L('Niente', 'Nothing')], 0, L('^ dentro le quadre nega.', '^ inside brackets negates.')),
            Q('c3', L('Come trovi un punto vero?', 'How do you match a real dot?'), ['\\.', '.', '[.*]', '\\d'], 0, L('Il punto da solo vale qualsiasi carattere.', 'A bare dot means any character.')),
        ], task('regex', L('Scrivi una regex che trovi un CAP italiano: esattamente 5 cifre, usando `\\d` e le ancore `^` e `$`.', 'Write a regex matching an Italian postcode: exactly 5 digits, using `\\d` and the anchors `^` and `$`.'), '', [re('^\s*\^(\\\\d\{5\}|\\\\d\\\\d\\\\d\\\\d\\\\d|\[0-9\]\{5\})\$\s*$', L('Cinque cifre, dall’inizio alla fine', 'Five digits, start to end'))], '', '^\d{5}$'), [
            dMatch(pMatch(), [['\\d', L('Cifra', 'Digit')], ['\\w', L('Lettera, cifra o _', 'Letter, digit or _')], ['\\s', L('Spazio', 'Whitespace')], ['.', L('Qualsiasi carattere', 'Any character')], ['[aeiou]', L('Una vocale', 'A vowel')]], L('Le classi più usate.', 'The most used classes.')),
            dFlash(pFlash(), [[L('`[A-Z]` trova una lettera maiuscola', '`[A-Z]` matches an uppercase letter'), true], [L('`.` trova solo il punto', '`.` only matches a dot'), false], [L('`\\s` trova gli spazi', '`\\s` matches whitespace'), true], [L('`\\D` trova una cifra', '`\\D` matches a digit'), false]], L('I mattoni delle regex.', 'Regex building blocks.')),
        ]),
        lesson('quantifiers', L('Quantificatori e ancore', 'Quantifiers and anchors'), [
            P('I **quantificatori** dicono quante volte ripetere l’elemento precedente: `*` zero o più, `+` una o più, `?` zero o una, `{3}` esattamente tre, `{2,4}` da due a quattro. Le **ancore** non trovano caratteri ma posizioni: `^` inizio, `$` fine, `\\b` confine di parola.', '**Quantifiers** say how many times to repeat the preceding element: `*` zero or more, `+` one or more, `?` zero or one, `{3}` exactly three, `{2,4}` two to four. **Anchors** match positions, not characters: `^` start, `$` end, `\\b` word boundary.'),
            C('colou?r            color, colour
\\d{2}/\\d{2}/\\d{4}  10/11/2026
^\\s*$              una riga vuota (o solo spazi)
\\bgatto\\b         “gatto” ma non “gattone”
<.+?>              un tag HTML, il più corto possibile', 'text'),
            P('Di base i quantificatori sono **avidi**: prendono più testo possibile. Aggiungendo `?` (`+?`, `*?`) diventano **pigri** e prendono il meno possibile. Per validare un campo intero usa sempre `^` e `$`, altrimenti basta che il modello compaia da qualche parte.', 'By default quantifiers are **greedy**: they take as much text as possible. Adding `?` (`+?`, `*?`) makes them **lazy**, taking as little as possible. To validate a whole field always use `^` and `$`, otherwise it is enough for the pattern to appear somewhere.'),
        ], [
            Q('q1', L('Che cosa significa `+`?', 'What does `+` mean?'), [L('Una o più volte', 'One or more times'), L('Zero o più volte', 'Zero or more times'), L('Esattamente una volta', 'Exactly once'), L('Una somma', 'A sum')], 0, L('`*` permette anche zero.', '`*` also allows zero.')),
            Q('q2', L('Perché usare `^` e `$` nella validazione?', 'Why use `^` and `$` in validation?'), [L('Per controllare tutto il campo, non una parte', 'To check the whole field, not a part'), L('Per renderla più veloce', 'To make it faster'), L('Non servono', 'They are not needed'), L('Per i colori', 'For colours')], 0, L('Altrimenti “abc12345xyz” passerebbe per un CAP.', 'Otherwise “abc12345xyz” would pass as a postcode.')),
            Q('q3', L('Che cosa fa `\\b`?', 'What does `\\b` do?'), [L('Indica un confine di parola', 'Marks a word boundary'), L('Trova una b', 'Matches a b'), L('Trova uno spazio', 'Matches a space'), L('Indica la fine del file', 'Marks end of file')], 0, L('Parole intere.', 'Whole words.')),
        ], task('regex', L('Scrivi una regex per un orario `HH:MM` (due cifre, due punti, due cifre), dall’inizio alla fine del testo.', 'Write a regex for a time `HH:MM` (two digits, colon, two digits), from start to end of the text.'), '', [re('^\s*\^(\\\\d\{2\}|\\\\d\\\\d|\[0-9\]\{2\}|\[0-2\]\\\\d|\[01\]\\\\d\|2\[0-3\]|\(\[01\]\\\\d\|2\[0-3\]\)):(\\\\d\{2\}|\\\\d\\\\d|\[0-9\]\{2\}|\[0-5\]\\\\d)\$\s*$', L('Due cifre, due punti, due cifre', 'Two digits, colon, two digits'))], '', '^\d{2}:\d{2}$'), [
            dMatch(pMatch(), [['*', L('Zero o più', 'Zero or more')], ['+', L('Una o più', 'One or more')], ['?', L('Zero o una', 'Zero or one')], ['{3}', L('Esattamente tre', 'Exactly three')], ['^ … $', L('Tutto il testo', 'The whole text')]], L('Quanto e dove.', 'How many and where.')),
            dBug(pBug(), ['// valida un codice di 6 cifre', 'const valido = /\\d{6}/.test(codice);'], 1, 'const valido = /^\\d{6}$/.test(codice);', L('Senza ancore passa anche “abc1234567”.', 'Without anchors “abc1234567” passes too.')),
        ]),
        lesson('groups', L('Gruppi e alternative', 'Groups and alternatives'), [
            P('Le parentesi tonde creano un **gruppo**: puoi applicare un quantificatore a più caratteri insieme `(ab)+` e **catturare** una parte del testo per usarla dopo. La barra verticale `|` indica un’**alternativa**: `gatto|cane`.', 'Round brackets create a **group**: you can apply a quantifier to several characters together `(ab)+` and **capture** part of the text to use later. The vertical bar `|` means an **alternative**: `gatto|cane`.'),
            C('(\\d{2})/(\\d{2})/(\\d{4})          cattura giorno, mese e anno
(?<anno>\\d{4})-(?<mese>\\d{2})     gruppi con nome
\\.(jpg|jpeg|png|gif)$             estensioni di immagine
(?:https?://)?www\\.               gruppo che non cattura', 'text'),
            P('Nelle sostituzioni i gruppi si richiamano con `$1`, `$2` (in JavaScript) o `\\1` (in Python): per esempio trasformare `10/11/2026` in `2026-11-10`. `(?:…)` raggruppa senza catturare, utile quando ti serve solo l’alternativa.', 'In replacements groups are referenced with `$1`, `$2` (in JavaScript) or `\\1` (in Python): for example turning `10/11/2026` into `2026-11-10`. `(?:…)` groups without capturing, handy when you only need the alternative.'),
        ], [
            Q('g1', L('Che cosa fa `|` in una regex?', 'What does `|` do in a regex?'), [L('Indica un’alternativa', 'Means an alternative'), L('Una pipe di Bash', 'A Bash pipe'), L('Fine della riga', 'End of line'), L('Un carattere qualsiasi', 'Any character')], 0, L('`jpg|png`.', '`jpg|png`.')),
            Q('g2', L('Come si richiama il primo gruppo in una sostituzione JavaScript?', 'How do you reference the first group in a JavaScript replacement?'), ['$1', '\\0', '%1', '#1'], 0, L('In Python `\\1`.', 'In Python `\\1`.')),
            Q('g3', L('Che cosa fa `(?:…)`?', 'What does `(?:…)` do?'), [L('Raggruppa senza catturare', 'Groups without capturing'), L('Cattura due volte', 'Captures twice'), L('Nega il gruppo', 'Negates the group'), L('Commenta', 'Comments')], 0, L('Solo per raggruppare.', 'Just for grouping.')),
        ], task('regex', L('Scrivi una regex che riconosca un nome di file che finisce con `.pdf`, `.doc` o `.docx`, usando un gruppo con alternative e l’ancora `$`.', 'Write a regex recognising a file name ending in `.pdf`, `.doc` or `.docx`, using a group with alternatives and the `$` anchor.'), '', [re('\\\\\.\((\?:)?(pdf|docx\??|doc)(\|(pdf|docx\??|doc)){1,2}\)\$', L('Gruppo con alternative e fine del testo', 'Group with alternatives and end of text'))], '', '\.(pdf|docx|doc)$'), [
            dMatch(pMatch(), [['( )', L('Gruppo che cattura', 'Capturing group')], ['(?: )', L('Gruppo che non cattura', 'Non-capturing group')], ['(?<nome> )', L('Gruppo con nome', 'Named group')], ['|', L('Alternativa', 'Alternative')], ['$1', L('Primo gruppo in sostituzione', 'First group in replacement')]], L('Raggruppare.', 'Grouping.')),
            dFlash(pFlash(), [[L('Le parentesi permettono di catturare parti del testo', 'Brackets let you capture parts of the text'), true], [L('`|` significa “e”', '`|` means “and”'), false], [L('Si può dare un nome a un gruppo', 'A group can be named'), true], [L('`(?:…)` cattura il testo', '`(?:…)` captures text'), false]], L('Parti e alternative.', 'Parts and alternatives.')),
        ]),
        lesson('code', L('Regex nel codice', 'Regex in code'), [
            P('In **JavaScript** una regex si scrive tra barre con i **flag** dopo: `i` ignora maiuscole/minuscole, `g` trova tutte le occorrenze, `m` fa valere `^`/`$` per ogni riga. In **Python** si usa il modulo `re` con stringhe *raw* (`r"…"`) per non raddoppiare le barre rovesciate.', 'In **JavaScript** a regex is written between slashes with **flags** after: `i` ignores case, `g` finds all occurrences, `m` makes `^`/`$` apply to every line. In **Python** you use the `re` module with *raw* strings (`r"…"`) so as not to double backslashes.'),
            C('// JavaScript
const email = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/i;
email.test("ada@example.com");                          // true
"Tel 333 1234567, 0432 123456".match(/\\d+/g);          // ["333", "1234567", "0432", "123456"]
"10/11/2026".replace(/(\\d{2})\\/(\\d{2})\\/(\\d{4})/, "$3-$2-$1");   // "2026-11-10"

# Python
import re
re.findall(r"#(\\w+)", "Viva #python e #regex")        # ["python", "regex"]
re.sub(r"\\s+", " ", "troppi    spazi   qui")            # "troppi spazi qui"', 'js'),
            P('Le regex sono potenti ma possono diventare illeggibili: commentale, provale su esempi veri (anche casi che **non** devono passare) e non usarle per tutto. Per validare email o analizzare HTML esistono librerie dedicate più affidabili.', 'Regex are powerful but can become unreadable: comment them, test them on real examples (including cases that must **not** pass) and do not use them for everything. To validate email or parse HTML there are more reliable dedicated libraries.'),
        ], [
            Q('r1', L('Che cosa fa il flag `g` in JavaScript?', 'What does the `g` flag do in JavaScript?'), [L('Trova tutte le occorrenze', 'Finds all occurrences'), L('Ignora maiuscole', 'Ignores case'), L('Rende la regex più veloce', 'Makes the regex faster'), L('Niente', 'Nothing')], 0, L('g = global.', 'g = global.')),
            Q('r2', L('Perché in Python si usano le stringhe raw `r"…"`?', 'Why use raw strings `r"…"` in Python?'), [L('Per non raddoppiare le barre rovesciate', 'To avoid doubling backslashes'), L('Sono più veloci', 'They are faster'), L('Sono obbligatorie per legge', 'They are legally required'), L('Per i colori', 'For colours')], 0, L('`r"\\d"` invece di `"\\\\d"`.', '`r"\\d"` instead of `"\\\\d"`.')),
            Q('r3', L('Che cosa restituisce `re.findall`?', 'What does `re.findall` return?'), [L('L’elenco di tutte le corrispondenze', 'The list of all matches'), L('Solo la prima', 'Only the first'), L('Un booleano', 'A boolean'), L('Il testo sostituito', 'The replaced text')], 0, L('Una lista.', 'A list.')),
        ], task('js', L('In JavaScript, scrivi `const hashtag = testo.match(/#\\w+/g);` per trovare tutti gli hashtag in `testo`.', 'In JavaScript, write `const hashtag = testo.match(/#\\w+/g);` to find every hashtag in `testo`.'), "const testo = \"Studio #regex e #javascript\";\n", [re('const\s+hashtag\s*=\s*testo\.match\s*\(\s*/#\\\\w\+/g\s*\)', L('Trovi tutti gli hashtag', 'You find every hashtag'))], '', 'const hashtag = testo.match(/#\w+/g);'), [
            dMatch(pMatch(), [['i', L('Ignora maiuscole', 'Ignores case')], ['g', L('Tutte le occorrenze', 'All occurrences')], ['m', L('^ e $ per ogni riga', '^ and $ per line')], ['re.sub', L('Sostituisce in Python', 'Replaces in Python')], ['r"…"', L('Stringa raw', 'Raw string')]], L('Flag e funzioni.', 'Flags and functions.')),
            dFlash(pFlash(), [[L('Conviene provare una regex anche su casi che non devono passare', 'Test a regex on cases that must not pass too'), true], [L('Le regex sono il modo migliore per analizzare HTML', 'Regex are the best way to parse HTML'), false], [L('Il flag i ignora maiuscole e minuscole', 'The i flag ignores case'), true], [L('Le regex non si possono commentare', 'Regex cannot be commented'), false]], L('Potenti, con misura.', 'Powerful, in moderation.')),
        ]),
    ],
];
