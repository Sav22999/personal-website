<?php
/** Regular expressions: literals and classes, quantifiers and anchors, groups and alternatives, using them in code, lookarounds,
 *  flags, Unicode and backreferences, regex in command-line tools and engine differences. */

/** A row of characters in equal cells, so that matches can be highlighted exactly: $marks maps a cell index to [fill, stroke, dashed]. */
if (!function_exists('fig_rx_cells')) {
    function fig_rx_cells(string $text, int $x0, int $y, int $w, array $marks = []): string
    {
        $out = '';
        foreach (mb_str_split($text) as $i => $ch) {
            $x = $x0 + $i * $w; [$f, $st, $dash] = $marks[$i] ?? ['#FFFFFF', '#D9D7E8', false];
            $d = $dash ? ' stroke-dasharray="4 3"' : '';
            $out .= "<rect x=\"$x\" y=\"$y\" width=\"" . ($w - 2) . "\" height=\"34\" rx=\"5\" fill=\"$f\" stroke=\"$st\" stroke-width=\"2\"$d/>";
            $out .= '<text x="' . ($x + ($w - 2) / 2) . '" y="' . ($y + 23) . '" text-anchor="middle">' . htmlspecialchars($ch, ENT_XML1) . '</text>';
        }
        return $out;
    }
}

/** Greedy and lazy: the same text matched by <.+> (one long match) and by <.+?> (four short ones). */
if (!function_exists('fig_rx_greedy')) {
    function fig_rx_greedy(string $greedy, string $lazy, string $one, string $four): string
    {
        $text = '<b>ciao</b> <b>mondo</b>';
        $all = array_fill(0, 24, ['#FFE8D6', '#E8590C', false]);
        $lz = [];
        foreach ([[0, 2], [7, 10], [12, 14], [20, 23]] as $k => [$a, $b]) for ($i = $a; $i <= $b; $i++) $lz[$i] = $k % 2 ? ['#ECE6FF', '#7048E8', false] : ['#DCEBFF', '#2F8CF0', false];
        $r1 = fig_rx_cells($text, 40, 62, 24, $all);
        $r2 = fig_rx_cells($text, 40, 170, 24, $lz);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="ui-monospace, Menlo, monospace" font-size="16" fill="#1E1C52">
<text x="40" y="44" font-weight="700">&lt;.+&gt;</text><text x="130" y="44" font-family="system-ui, sans-serif" font-size="14">{$greedy}</text>
{$r1}
<path d="M40,104 V112 H614 V104" fill="none" stroke="#E8590C" stroke-width="2"/><text x="327" y="130" text-anchor="middle" font-size="13" fill="#B84508" font-weight="700">{$one}</text>
<text x="40" y="156" font-weight="700">&lt;.+?&gt;</text><text x="130" y="156" font-family="system-ui, sans-serif" font-size="14">{$lazy}</text>
{$r2}
<text x="327" y="224" text-anchor="middle" font-size="13" fill="#1B66BC" font-weight="700">{$four}</text>
</svg>
SVG;
    }
}

/** Capturing groups: each bracket of (\d{2})/(\d{2})/(\d{4}) captures a piece of the date, and $3-$2-$1 puts them back in another order. */
if (!function_exists('fig_rx_groups')) {
    function fig_rx_groups(): string
    {
        $c1 = ['#FFE8D6', '#E8590C', false]; $c2 = ['#DCEBFF', '#2F8CF0', false]; $c3 = ['#D5F6EA', '#0B7F5C', false];
        $row = fig_rx_cells('10/11/2026', 140, 70, 36, [0 => $c1, 1 => $c1, 3 => $c2, 4 => $c2, 6 => $c3, 7 => $c3, 8 => $c3, 9 => $c3]);
        $out = fig_rx_cells('2026-11-10', 140, 206, 36, [0 => $c3, 1 => $c3, 2 => $c3, 3 => $c3, 5 => $c2, 6 => $c2, 8 => $c1, 9 => $c1]);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="ui-monospace, Menlo, monospace" font-size="16" fill="#1E1C52">
<g font-size="14" font-weight="700" text-anchor="middle"><text x="175" y="56" fill="#B84508">(\d{2})</text><text x="229" y="56">/</text><text x="283" y="56" fill="#1B66BC">(\d{2})</text><text x="337" y="56">/</text><text x="427" y="56" fill="#0B7F5C">(\d{4})</text></g>
{$row}
<g fill="none" stroke-width="2"><path d="M142,110 V118 H210 V110" stroke="#E8590C"/><path d="M250,110 V118 H318 V110" stroke="#2F8CF0"/><path d="M358,110 V118 H498 V110" stroke="#0B7F5C"/></g>
<g font-size="15" font-weight="700" text-anchor="middle"><text x="176" y="138" fill="#B84508">\$1</text><text x="284" y="138" fill="#1B66BC">\$2</text><text x="428" y="138" fill="#0B7F5C">\$3</text></g>
<path d="M320,150 V194" stroke="#1E1C52" stroke-width="2"/><path d="M314,188 L320,196 L326,188" fill="none" stroke="#1E1C52" stroke-width="2"/>
<text x="334" y="178" font-size="14" font-weight="700">"\$3-\$2-\$1"</text>
{$out}
</svg>
SVG;
    }
}

/** A lookbehind: (?<=€)\d+ checks the euro sign before the number but leaves it out of the match. */
if (!function_exists('fig_rx_look')) {
    function fig_rx_look(string $text, string $checked, string $taken): string
    {
        $chars = mb_str_split($text); $e = array_search('€', $chars);
        $marks = [$e => ['#ECE6FF', '#7048E8', true], $e + 1 => ['#FFE8D6', '#E8590C', false], $e + 2 => ['#FFE8D6', '#E8590C', false]];
        $x0 = (int) round((640 - count($chars) * 36) / 2);
        $row = fig_rx_cells($text, $x0, 70, 36, $marks);
        $ex = $x0 + $e * 36 + 17; $mx = $x0 + ($e + 1) * 36 + 35;
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 200" font-family="ui-monospace, Menlo, monospace" font-size="16" fill="#1E1C52">
<text x="320" y="44" text-anchor="middle" font-weight="700"><tspan fill="#5A35C9">(?&lt;=€)</tspan><tspan fill="#B84508">\d+</tspan></text>
{$row}
<g font-family="system-ui, sans-serif" font-size="14" font-weight="700">
<path d="M{$ex},108 V140" stroke="#7048E8" stroke-width="2" stroke-dasharray="4 3"/><text x="{$ex}" y="160" text-anchor="end" fill="#5A35C9">{$checked}</text>
<path d="M{$mx},108 V170" stroke="#E8590C" stroke-width="2"/><text x="{$mx}" y="190" text-anchor="middle" fill="#B84508">{$taken}</text>
</g>
</svg>
SVG;
    }
}

return [
    'id' => 'regex', 'family' => 'regex', 'after' => null,
    'title' => L('Espressioni regolari', 'Regular Expressions'), 'short' => L('Regex', 'Regex'), 'level' => L('Base', 'Basics'),
    'desc' => L('Cercare e validare testo con le regex: classi di caratteri, quantificatori, ancore, gruppi e alternative, lookaround e Unicode, e come usarle in JavaScript, Python e nel terminale.', 'Searching and validating text with regex: character classes, quantifiers, anchors, groups and alternatives, lookarounds and Unicode, and how to use them in JavaScript, Python and the terminal.'),
    'lessons' => [
        lesson('classes', L('Caratteri e classi', 'Characters and classes'), [
            P('Un’**espressione regolare** (regex) descrive un modello di testo. La maggior parte dei caratteri vale per sé stessa (`gatto` trova “gatto”); alcuni sono speciali. Le **classi** indicano un insieme di caratteri ammessi in una posizione.', 'A **regular expression** (regex) describes a text pattern. Most characters stand for themselves (`gatto` finds “gatto”); some are special. **Classes** state a set of characters allowed in one position.'),
            C('.        qualsiasi carattere (tranne a capo)
\\d       una cifra            \\D  una non-cifra
\\w       lettera, cifra o _   \\W  il contrario
\\s       spazio, tab, a capo  \\S  il contrario
[aeiou]  una vocale           [^0-9]  tutto tranne le cifre
[A-Za-z] una lettera latina   \\.   un punto vero (escape)', 'text', L('<style>td{padding:4px 10px}code{font-weight:700}mark{margin:0 1px}</style>
<table>
<tr><td><code>\d</code></td><td>Il volo AZ <mark>1</mark><mark>2</mark><mark>3</mark><mark>4</mark> parte alle <mark>7</mark>.<mark>3</mark><mark>0</mark>!</td></tr>
<tr><td><code>[aeiou]</code></td><td>Il v<mark>o</mark>l<mark>o</mark> AZ 1234 p<mark>a</mark>rt<mark>e</mark> <mark>a</mark>ll<mark>e</mark> 7.30!</td></tr>
<tr><td><code>\s</code></td><td>Il<mark>&nbsp;</mark>volo<mark>&nbsp;</mark>AZ<mark>&nbsp;</mark>1234<mark>&nbsp;</mark>parte<mark>&nbsp;</mark>alle<mark>&nbsp;</mark>7.30!</td></tr>
<tr><td><code>\.</code></td><td>Il volo AZ 1234 parte alle 7<mark>.</mark>30!</td></tr>
</table>', '<style>td{padding:4px 10px}code{font-weight:700}mark{margin:0 1px}</style>
<table>
<tr><td><code>\d</code></td><td>Flight AZ <mark>1</mark><mark>2</mark><mark>3</mark><mark>4</mark> leaves at <mark>7</mark>.<mark>3</mark><mark>0</mark>!</td></tr>
<tr><td><code>[aeiou]</code></td><td>Fl<mark>i</mark>ght AZ 1234 l<mark>e</mark><mark>a</mark>v<mark>e</mark>s <mark>a</mark>t 7.30!</td></tr>
<tr><td><code>\s</code></td><td>Flight<mark>&nbsp;</mark>AZ<mark>&nbsp;</mark>1234<mark>&nbsp;</mark>leaves<mark>&nbsp;</mark>at<mark>&nbsp;</mark>7.30!</td></tr>
<tr><td><code>\.</code></td><td>Flight AZ 1234 leaves at 7<mark>.</mark>30!</td></tr>
</table>')),
            P('Per cercare un carattere speciale come testo (`.`, `?`, `+`, `(`, `[`, `\\`) va **preceduto da una barra rovesciata**: `\\.` trova un punto vero, mentre `.` trova qualsiasi carattere.', 'To search for a special character as text (`.`, `?`, `+`, `(`, `[`, `\\`) it must be **preceded by a backslash**: `\\.` finds a real dot, while `.` finds any character.'),
            P('Una classe trova sempre **un solo** carattere: `[aeiou]` è una vocale, non una parola. Dentro le parentesi quadre quasi tutti i caratteri speciali perdono il loro potere (`[.]` è un punto vero), mentre `^` all’inizio nega la classe. Gli intervalli seguono i codici dei caratteri: `[A-Z]` non comprende lettere accentate come «È». Errore tipico: dimenticare che `.` prende tutto, così `3.14` trova anche «3514».', 'A class always matches **one single** character: `[aeiou]` is one vowel, not a word. Inside square brackets almost every special character loses its power (`[.]` is a real dot), while `^` at the start negates the class. Ranges follow character codes: `[A-Z]` does not include accented letters such as “È”. A typical mistake: forgetting that `.` matches anything, so `3.14` also finds “3514”.'),
        ], [
            Q('c1', L('Che cosa trova `\\d`?', 'What does `\\d` match?'), [L('Una cifra', 'A digit'), L('Una lettera', 'A letter'), L('Uno spazio', 'A space'), L('Un punto', 'A dot')], 0, L('d = digit.', 'd = digit.')),
            Q('c2', L('Che cosa trova `[^0-9]`?', 'What does `[^0-9]` match?'), [L('Qualsiasi carattere tranne le cifre', 'Any character except digits'), L('Solo le cifre', 'Only digits'), L('L’inizio della riga', 'Start of line'), L('Niente', 'Nothing')], 0, L('^ dentro le quadre nega.', '^ inside brackets negates.')),
            Q('c3', L('Come trovi un punto vero?', 'How do you match a real dot?'), ['\\.', '.', '\\s', '\\d'], 0, L('Il punto da solo vale qualsiasi carattere.', 'A bare dot means any character.')),
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
<.+?>              un tag HTML, il più corto possibile', 'text', L('<style>td{padding:4px 10px}code{font-weight:700}</style>
<table>
<tr><td><code>colou?r</code></td><td>Si scrive <mark>color</mark> o <mark>colour</mark>?</td></tr>
<tr><td><code>\d{2}/\d{2}/\d{4}</code></td><td>Scadenza: <mark>10/11/2026</mark></td></tr>
<tr><td><code>\bgatto\b</code></td><td>Il <mark>gatto</mark> e il gattone</td></tr>
<tr><td><code>&lt;.+?&gt;</code></td><td><mark>&lt;b&gt;</mark>ciao<mark>&lt;/b&gt;</mark></td></tr>
</table>', '<style>td{padding:4px 10px}code{font-weight:700}</style>
<table>
<tr><td><code>colou?r</code></td><td>Is it <mark>color</mark> or <mark>colour</mark>?</td></tr>
<tr><td><code>\d{2}/\d{2}/\d{4}</code></td><td>Due date: <mark>10/11/2026</mark></td></tr>
<tr><td><code>\bgatto\b</code></td><td>The <mark>gatto</mark> (cat) is not a gattone</td></tr>
<tr><td><code>&lt;.+?&gt;</code></td><td><mark>&lt;b&gt;</mark>hello<mark>&lt;/b&gt;</mark></td></tr>
</table>')),
            P('Di base i quantificatori sono **avidi**: prendono più testo possibile. Aggiungendo `?` (`+?`, `*?`) diventano **pigri** e prendono il meno possibile. Per validare un campo intero usa sempre `^` e `$`, altrimenti basta che il modello compaia da qualche parte.', 'By default quantifiers are **greedy**: they take as much text as possible. Adding `?` (`+?`, `*?`) makes them **lazy**, taking as little as possible. To validate a whole field always use `^` and `$`, otherwise it is enough for the pattern to appear somewhere.'),
            Fig(L(fig_rx_greedy('avido: prende più testo possibile', 'pigro: si ferma al primo > utile', '1 corrispondenza', '4 corrispondenze'), fig_rx_greedy('greedy: takes as much text as possible', 'lazy: stops at the first useful >', '1 match', '4 matches')),
                L('Lo stesso testo, <b>ciao</b> <b>mondo</b>, in due righe di caselle. Con <.+> una sola corrispondenza copre tutta la riga, dal primo < all’ultimo >. Con <.+?> le corrispondenze sono quattro: i singoli tag <b>, </b>, <b> e </b>.', 'The same text, <b>ciao</b> <b>mondo</b>, in two rows of cells. With <.+> a single match covers the whole row, from the first < to the last >. With <.+?> there are four matches: the single tags <b>, </b>, <b> and </b>.'),
                L('Un solo `?` cambia da una corrispondenza enorme a quattro piccole.', 'A single `?` turns one huge match into four small ones.')),
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
            P('Nelle sostituzioni i gruppi si richiamano con `$1`, `$2` (in JavaScript) o `\\1` (in Python): per esempio trasformare `10/11/2026` in `2026-11-10`. `(?:…)` raggruppa senza catturare, utile quando ti serve solo l’alternativa. I gruppi con nome in Python si scrivono `(?P<anno>…)`.', 'In replacements groups are referenced with `$1`, `$2` (in JavaScript) or `\\1` (in Python): for example turning `10/11/2026` into `2026-11-10`. `(?:…)` groups without capturing, handy when you only need the alternative. In Python named groups are written `(?P<anno>…)`.'),
            Fig(fig_rx_groups(),
                L('Il modello (\\d{2})/(\\d{2})/(\\d{4}) sopra la data 10/11/2026: il primo gruppo cattura 10, il secondo 11, il terzo 2026, indicati come $1, $2 e $3. Con la sostituzione "$3-$2-$1" la data diventa 2026-11-10, con i pezzi negli stessi colori.', 'The pattern (\\d{2})/(\\d{2})/(\\d{4}) above the date 10/11/2026: the first group captures 10, the second 11, the third 2026, marked $1, $2 and $3. With the replacement "$3-$2-$1" the date becomes 2026-11-10, with the pieces in the same colours.'),
                L('I gruppi si contano dalla parentesi aperta, da sinistra a destra.', 'Groups are counted by their opening bracket, from left to right.')),
            Tip('L’alternativa `|` ha la precedenza più bassa di tutte: `^gatto|cane$` vuol dire «`^gatto` oppure `cane$`», quindi accetta anche «gattone». Per validare una parola tra più scelte racchiudila in un gruppo: `^(gatto|cane)$`.', 'The alternative `|` has the lowest precedence of all: `^gatto|cane$` means “`^gatto` or `cane$`”, so it also accepts “gattone”. To validate one word among several choices wrap it in a group: `^(gatto|cane)$`.'),
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
        lesson('lookaround', L('Lookaround, gruppi con nome e prestazioni', 'Lookarounds, named groups and performance'), [
            P('Un **lookahead** `(?=…)` controlla che dopo ci sia qualcosa **senza consumarlo**; `(?!…)` che non ci sia. Il **lookbehind** `(?<=…)` guarda indietro. Servono per regole come “almeno una cifra” in una password (anche se le linee guida moderne, come quelle del NIST, preferiscono la lunghezza alle regole di composizione).', 'A **lookahead** `(?=…)` checks that something follows **without consuming it**; `(?!…)` that it does not. The **lookbehind** `(?<=…)` looks back. They help with rules like “at least one digit” in a password (even though modern guidance, such as NIST’s, prefers length to composition rules).'),
            Fig(L(fig_rx_look('Prezzo: €25', 'controllato, non preso', 'la corrispondenza: 25'), fig_rx_look('Price: €25', 'checked, not taken', 'the match: 25')),
                L('Il modello (?<=€)\\d+ sopra il testo in caselle. La casella del simbolo € è tratteggiata: il lookbehind la controlla ma non la prende. Le caselle con 2 e 5 sono evidenziate: sono la corrispondenza, 25.', 'The pattern (?<=€)\\d+ above the text in cells. The cell with the € sign is dashed: the lookbehind checks it but does not take it. The cells with 2 and 5 are highlighted: they are the match, 25.'),
                L('Il lookaround guarda intorno senza consumare: il simbolo € resta fuori dal risultato.', 'A lookaround looks around without consuming: the € sign stays out of the result.')),
            C('^(?=.*\d)(?=.*[A-Z]).{8,}$         password: 8+ caratteri, una cifra, una maiuscola
(?<=€)\d+                          numeri preceduti da €
(?<anno>\d{4})-(?<mese>\d{2})       gruppi con nome: anno e mese', 'regex'),
            P('I **gruppi con nome** `(?<nome>…)` rendono il codice leggibile (`m.groups.anno`, in JavaScript). Attenzione al **backtracking catastrofico**: modelli come `(a+)+$` su testi lunghi possono bloccare il programma. Evita quantificatori annidati e limita la lunghezza dell’input.', '**Named groups** `(?<name>…)` make code readable (`m.groups.anno`, in JavaScript). Beware **catastrophic backtracking**: patterns like `(a+)+$` on long texts can freeze the program. Avoid nested quantifiers and limit input length.'),
        ], [
            Q('lk1', L('Che cosa fa `(?=\d)`?', 'What does `(?=\d)` do?'), [L('Controlla che segua una cifra senza consumarla', 'Checks a digit follows without consuming it'), L('Cattura una cifra', 'Captures a digit'), L('Esclude le cifre', 'Excludes digits'), L('Cerca all’indietro', 'Looks backwards')], 0, L('È un lookahead.', 'It is a lookahead.')),
            Q('lk2', L('Come si scrive un gruppo con nome?', 'How do you write a named group?'), ['(?<nome>…)', '(nome:…)', '{nome}…', '[nome]…'], 0, L('In JavaScript lo leggi con `groups.nome`; Python usa `(?P<nome>…)`.', 'In JavaScript you read it with `groups.nome`; Python uses `(?P<nome>…)`.')),
            Q('lk3', L('Quale modello rischia il backtracking catastrofico?', 'Which pattern risks catastrophic backtracking?'), ['(a+)+$', '^a+$', '\d{4}', '[a-z]'], 0, L('Quantificatori annidati.', 'Nested quantifiers.')),
        ], task('regex', L('Scrivi una regex che accetta solo password di almeno 8 caratteri con almeno una cifra: `^(?=.*\d).{8,}$`.', 'Write a regex accepting only passwords of at least 8 characters with at least one digit: `^(?=.*\d).{8,}$`.'), '', [re('\^\(\?=\.\*\\\\d\)\.\{8,\}\$', L('La regex è giusta', 'The regex is right'))], '', '^(?=.*\d).{8,}$'), [
            dMatch(pMatch(), [['(?=…)', L('Segue', 'Followed by')], ['(?!…)', L('Non segue', 'Not followed by')], ['(?<=…)', L('Preceduto da', 'Preceded by')], ['(?<nome>…)', L('Gruppo con nome', 'Named group')], ['(a+)+', L('Rischio di blocco', 'Freeze risk')]], L('Regex avanzate.', 'Advanced regex.')),
            dFlash(pFlash(), [[L('Un lookahead non consuma caratteri', 'A lookahead consumes no characters'), true], [L('I quantificatori annidati sono sempre veloci', 'Nested quantifiers are always fast'), false], [L('I gruppi con nome migliorano la leggibilità', 'Named groups improve readability'), true], [L('(?<=€) cerca dopo il simbolo e lo cattura', '(?<=€) looks after the symbol and captures it'), false]], L('Guardare avanti e indietro.', 'Looking ahead and behind.')),
        ]),
        lesson('unicode', L('Flag, Unicode e riferimenti all’indietro', 'Flags, Unicode and backreferences'), [
            P('Oltre a `i`, `g` e `m` ci sono altri flag utili. Con `s` il punto prende anche l’a capo. Con `u` la regex lavora in modalità **Unicode**: `\\p{L}` trova una lettera di qualsiasi alfabeto, accentate comprese, `\\p{Lu}` una maiuscola. Attenzione: in JavaScript `\\w` conosce solo le lettere ASCII, quindi “perché” non è una parola intera per `\\w+`.', 'Besides `i`, `g` and `m` there are other useful flags. With `s` the dot matches newlines too. With `u` the regex works in **Unicode** mode: `\\p{L}` matches a letter of any alphabet, accented ones included, `\\p{Lu}` an uppercase one. Beware: in JavaScript `\\w` knows only ASCII letters, so “perché” is not a whole word for `\\w+`.'),
            C('/^\\p{Lu}\\p{Ll}+$/u.test("Élodie")            // true: anche con l’accento
"perché città".match(/\\w+/g)                   // ["perch", "citt"]
"perché città".match(/\\p{L}+/gu)               // ["perché", "città"]
/inizio.*fine/s.test("inizio\\nfine")           // true: il punto prende l’a capo
"è il il momento".replace(/\\b(\\w+)\\s+\\1\\b/g, "$1")   // "è il momento"', 'js'),
            P('Un **riferimento all’indietro** come `\\1` dentro il modello trova di nuovo lo stesso testo catturato dal gruppo 1: `\\b(\\w+)\\s+\\1\\b` scova le parole ripetute. In Python il modulo `re` è già Unicode per le stringhe (`\\w` prende anche “é”), ma non conosce `\\p{L}`: per quello serve il modulo esterno `regex`.', 'A **backreference** like `\\1` inside the pattern matches again the same text captured by group 1: `\\b(\\w+)\\s+\\1\\b` finds repeated words. In Python the `re` module is already Unicode for strings (`\\w` matches “é” too), but it does not know `\\p{L}`: for that you need the external `regex` module.'),
        ], [
            Q('un1', L('Quale flag fa prendere al punto anche l’a capo, in JavaScript?', 'Which flag makes the dot match newlines too, in JavaScript?'), ['g', 'm', 's', 'i'], 2, L('s = dotAll.', 's = dotAll.')),
            Q('un2', L('Che cosa trova `\\p{L}` con il flag `u`?', 'What does `\\p{L}` match with the `u` flag?'), [L('Solo le lettere latine senza accenti', 'Only Latin letters without accents'), L('Una lettera di qualsiasi alfabeto, anche accentata', 'A letter of any alphabet, accented too'), L('Un segno di punteggiatura', 'A punctuation mark'), L('La lettera L maiuscola', 'The capital letter L')], 1, L('L = letter, nelle categorie Unicode.', 'L = letter, in the Unicode categories.')),
            Q('un3', L('Che cosa trova `\\b(\\w+)\\s+\\1\\b`?', 'What does `\\b(\\w+)\\s+\\1\\b` match?'), [L('Due parole qualsiasi separate da uno spazio', 'Any two words separated by a space'), L('Una parola seguita dal numero 1', 'A word followed by the number 1'), L('Una parola all’inizio della riga', 'A word at the start of the line'), L('La stessa parola ripetuta due volte di fila', 'The same word repeated twice in a row')], 3, L('`\\1` ripete il testo del gruppo 1.', '`\\1` repeats group 1’s text.')),
        ], task('js', L('Scrivi `const nome = /^\\p{Lu}\\p{Ll}+$/u;`: una maiuscola seguita da minuscole, di qualsiasi alfabeto.', 'Write `const nome = /^\\p{Lu}\\p{Ll}+$/u;`: an uppercase letter followed by lowercase ones, of any alphabet.'), '', [re('const\s+nome\s*=\s*/\^\\\\p\{Lu\}\\\\p\{Ll\}\+\$/[dgimsy]*u[dgimsy]*\s*;?', L('La regex Unicode', 'The Unicode regex'))], '', 'const nome = /^\p{Lu}\p{Ll}+$/u;'), [
            dMatch(pMatch(), [['s', L('Il punto prende l’a capo', 'The dot matches newlines')], ['u', L('Modalità Unicode', 'Unicode mode')], ['\\p{L}', L('Qualsiasi lettera', 'Any letter')], ['\\1', L('Lo stesso testo del gruppo 1', 'The same text as group 1')], [L('\\w in JavaScript', '\\w in JavaScript'), L('Solo lettere ASCII', 'Only ASCII letters')]], L('Oltre l’alfabeto inglese.', 'Beyond the English alphabet.')),
            dFlash(pFlash(), [[L('In JavaScript `\\w` trova anche “é”', 'In JavaScript `\\w` matches “é” too'), false], [L('`\\1` si riferisce al primo gruppo', '`\\1` refers to the first group'), true], [L('Il modulo `re` di Python supporta `\\p{L}`', 'Python’s `re` module supports `\\p{L}`'), false], [L('Con il flag s il punto prende l’a capo', 'With the s flag the dot matches newlines'), true]], L('Testo di tutto il mondo.', 'Text from all over the world.')),
        ]),
        lesson('tools', L('Regex nel terminale e motori diversi', 'Regex in the terminal and different engines'), [
            P('Le regex non vivono solo nel codice: le usano `grep`, `sed`, la funzione “trova e sostituisci” degli editor (in VS Code con il pulsante `.*` e `$1` nella sostituzione), i database (`REGEXP`) e PHP (`preg_match`). Ma i **motori** sono diversi: le regex POSIX estese di `grep -E` e `sed -E` non conoscono `\\d`, i quantificatori pigri né i lookaround.', 'Regex do not live only in code: `grep`, `sed`, editors’ find and replace (in VS Code with the `.*` button and `$1` in the replacement), databases (`REGEXP`) and PHP (`preg_match`) all use them. But the **engines** differ: the POSIX extended regex of `grep -E` and `sed -E` know neither `\\d`, lazy quantifiers nor lookarounds.'),
            C('grep -E "^[0-9]{5}$" cap.txt                       # POSIX: [0-9] invece di \\d
grep -oE "[[:alnum:]._%+-]+@[[:alnum:].-]+" contatti.txt
grep -P "\\d{3}-\\d{4}" numeri.txt                   # stile Perl (GNU grep)
sed -E "s|([0-9]{2})/([0-9]{2})/([0-9]{4})|\\3-\\2-\\1|" date.txt
# PHP: preg_match(\'/^\\d{5}$/\', $cap)', 'bash'),
            P('Prova i modelli su un sito come regex101 scegliendo il motore giusto, con esempi buoni e cattivi. Con input non fidato attenzione al blocco per backtracking (ReDoS): alcuni motori, come RE2 e quelli di Go e Rust, garantiscono un tempo lineare, ma per questo non hanno riferimenti all’indietro né lookaround.', 'Test patterns on a site like regex101 choosing the right engine, with good and bad examples. With untrusted input beware of backtracking freezes (ReDoS): some engines, such as RE2 and those of Go and Rust, guarantee linear time, but for that reason they have neither backreferences nor lookarounds.'),
        ], [
            Q('tl1', L('Perché `grep -E "\\d+"` può non funzionare come ti aspetti?', 'Why might `grep -E "\\d+"` not work as you expect?'), [L('Perché grep cerca solo testo esatto', 'Because grep searches exact text only'), L('Perché -E disattiva le regex', 'Because -E turns regex off'), L('Perché le regex POSIX estese non conoscono `\\d`: si usa `[0-9]`', 'Because POSIX extended regex do not know `\\d`: you use `[0-9]`'), L('Perché `\\d` funziona solo sulle lettere', 'Because `\\d` only works on letters')], 2, L('Oppure `[[:digit:]]`.', 'Or `[[:digit:]]`.')),
            Q('tl2', L('Nella sostituzione di `sed -E`, come richiami il gruppo 1?', 'In a `sed -E` replacement, how do you refer to group 1?'), ['$1', '\\1', '%1', '{1}'], 1, L('`$1` si usa in JavaScript e negli editor.', '`$1` is used in JavaScript and editors.')),
            Q('tl3', L('Quale motore garantisce un tempo lineare ma non ha lookaround?', 'Which engine guarantees linear time but has no lookarounds?'), ['PCRE', L('Il motore di JavaScript', 'JavaScript’s engine'), L('Il modulo re di Python', 'Python’s re module'), 'RE2'], 3, L('Niente backtracking, niente ReDoS.', 'No backtracking, no ReDoS.')),
        ], task('bash', L('Con `grep -E` stampa le righe di `ordini.txt` fatte solo di `ORD-` seguito da esattamente 4 cifre (usa `[0-9]`, non `\\d`).', 'With `grep -E` print the lines of `ordini.txt` made only of `ORD-` followed by exactly 4 digits (use `[0-9]`, not `\\d`).'), '', [re('grep\s+(-[a-zA-Z]*E[a-zA-Z]*|--extended-regexp)\s+["\']\^ORD-(\[0-9\]\{4\}|\[\[:digit:\]\]\{4\}|\[0-9\]\[0-9\]\[0-9\]\[0-9\])\$["\']\s+ordini\.txt', L('La regex POSIX giusta', 'The right POSIX regex'))], '', 'grep -E "^ORD-[0-9]{4}$" ordini.txt'), [
            dMatch(pMatch(), [['grep -E', L('Regex estese POSIX', 'POSIX extended regex')], ['grep -P', L('Regex stile Perl', 'Perl-style regex')], ['sed -E', L('Sostituzioni con le regex', 'Replacements with regex')], ['[[:digit:]]', L('Una cifra in POSIX', 'A digit in POSIX')], ['RE2', L('Tempo lineare garantito', 'Guaranteed linear time')]], L('Un linguaggio, tanti dialetti.', 'One language, many dialects.')),
            dBug(pBug(), ['# trova i CAP validi', 'grep -E "^\\d{5}$" cap.txt'], 1, 'grep -E "^[0-9]{5}$" cap.txt', L('Le regex POSIX estese non conoscono `\\d`.', 'POSIX extended regex do not know `\\d`.')),
        ]),
    ],
];
