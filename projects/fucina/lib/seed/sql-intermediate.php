<?php
/** SQL intermediate: aggregates, GROUP BY, joins, subqueries, keys, set operators, multiple joins and table design. */

/** The rows a query returns, as a small table for a demo page. A null value shows as a grey NULL; $label is the query, in monospace above. */
if (!function_exists('fucina_data_table')) {
    function fucina_data_table(array $head, array $rows, string $label = ''): string
    {
        $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES);
        $cell = fn($v) => $v === null ? '<td class="n">NULL</td>' : (is_int($v) || is_float($v) ? '<td class="r">' . $e($v) . '</td>' : '<td>' . $e($v) . '</td>');
        $html = '<style>body{margin:8px;font:14px system-ui,sans-serif;color:#1E1C52}table{border-collapse:collapse;margin:0 0 10px}th,td{border:1px solid #D9D7EA;padding:4px 10px;text-align:left}th{background:#F1F0F8}td.r{text-align:right;font-variant-numeric:tabular-nums}td.n{color:#8A87B0;font-style:italic}code{display:block;margin:0 0 4px;font:12px ui-monospace,monospace;color:#5A5880}</style>';
        if ($label !== '') $html .= '<code>' . $e($label) . '</code>';
        $html .= '<table><tr>' . implode('', array_map(fn($h) => '<th>' . $e($h) . '</th>', $head)) . '</tr>';
        foreach ($rows as $r) $html .= '<tr>' . implode('', array_map($cell, $r)) . '</tr>';
        return $html . '</table>';
    }
}

/** GROUP BY gathers the rows with the same city into one row each, with its count. */
if (!function_exists('fig_sqlint_group')) {
    function fig_sqlint_group(): string
    {
        $col = ['Roma' => ['#2F8CF0', '#DCEBFF'], 'Udine' => ['#0B7F5C', '#D5F6EA'], 'Trieste' => ['#E8590C', '#FFE8D6']];
        $gy = ['Roma' => 112, 'Udine' => 168, 'Trieste' => 224];
        $rows = [['Anna', 'Roma'], ['Luca', 'Udine'], ['Sara', 'Roma'], ['Marco', 'Trieste'], ['Elena', 'Udine'], ['Paolo', 'Roma']];
        $g = '';
        foreach ($rows as $i => [$n, $c]) {
            $y = 88 + $i * 34;
            [$s, $f] = $col[$c];
            $g .= '<path d="M232 ' . $y . ' C 320 ' . $y . ', 330 ' . $gy[$c] . ', 418 ' . $gy[$c] . '" fill="none" stroke="' . $s . '" stroke-width="2"/>';
            $g .= '<rect x="20" y="' . ($y - 15) . '" width="212" height="30" rx="8" fill="' . $f . '" stroke="' . $s . '" stroke-width="2"/><text x="34" y="' . ($y + 5) . '">' . $n . '</text><text x="124" y="' . ($y + 5) . '">' . $c . '</text>';
        }
        $count = ['Roma' => 3, 'Udine' => 2, 'Trieste' => 1];
        foreach ($count as $c => $k) {
            [$s, $f] = $col[$c];
            $y = $gy[$c];
            $g .= '<rect x="418" y="' . ($y - 18) . '" width="200" height="36" rx="10" fill="' . $f . '" stroke="' . $s . '" stroke-width="2"/><text x="434" y="' . ($y + 5) . '">' . $c . '</text><text x="600" y="' . ($y + 5) . '" text-anchor="end" font-weight="700">' . $k . '</text>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="250" y="14" width="150" height="32" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="325" y="35" text-anchor="middle" font-family="ui-monospace, monospace" font-size="14" font-weight="700" fill="#4B2BBF">GROUP BY citta</text>
<g font-weight="700"><text x="34" y="62">nome</text><text x="124" y="62">citta</text><text x="434" y="80">citta</text><text x="600" y="80" text-anchor="end">COUNT(*)</text></g>
{$g}
</svg>
SVG;
    }
}

/** INNER and LEFT JOIN on the same two tables: the user without orders is lost by one and kept, with NULL, by the other. */
if (!function_exists('fig_sqlint_join')) {
    function fig_sqlint_join(string $note1, string $note2, string $note3): string
    {
        $row = fn(int $x, int $w, int $y, array $cells, string $fill = '#FFFFFF') => '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="28" rx="6" fill="' . $fill . '"/>' . implode('', array_map(fn($c) => '<text x="' . $c[0] . '" y="' . ($y + 19) . '"' . ($c[2] ?? '') . '>' . $c[1] . '</text>', $cells));
        $g = '';
        // utenti
        $g .= '<rect x="20" y="64" width="150" height="124" rx="10" fill="none" stroke="#8A87B0" stroke-width="2"/>';
        foreach ([[1, 'Anna'], [2, 'Luca'], [3, 'Sara']] as $i => [$id, $n]) $g .= $row(24, 142, 96 + $i * 30, [[36, $id], [72, $n]], $n === 'Luca' ? '#FFE8D6' : '#FFFFFF');
        // ordini
        $g .= '<rect x="236" y="64" width="160" height="124" rx="10" fill="none" stroke="#8A87B0" stroke-width="2"/>';
        foreach ([[1, 50], [1, 20], [3, 35]] as $i => [$u, $t]) $g .= $row(240, 152, 96 + $i * 30, [[252, $u], [380, $t, ' text-anchor="end"']]);
        // match lines: Anna -> rows 0 and 1, Sara -> row 2
        foreach ([[110, 110], [110, 140], [170, 170]] as [$a, $b]) $g .= '<path d="M170 ' . $a . ' C 200 ' . $a . ', 206 ' . $b . ', 236 ' . $b . '" fill="none" stroke="#2F8CF0" stroke-width="2"/>';
        // results
        $g .= '<rect x="440" y="48" width="180" height="120" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>';
        foreach ([['Anna', 50], ['Anna', 20], ['Sara', 35]] as $i => [$n, $t]) $g .= $row(444, 172, 78 + $i * 29, [[456, $n], [604, $t, ' text-anchor="end"']], '#FFFFFF');
        $g .= '<rect x="440" y="208" width="180" height="148" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>';
        foreach ([['Anna', 50], ['Anna', 20], ['Luca', 'NULL'], ['Sara', 35]] as $i => [$n, $t]) $g .= $row(444, 172, 238 + $i * 29, [[456, $n], [604, $t, ' text-anchor="end"' . ($t === 'NULL' ? ' font-style="italic" fill="#8A87B0"' : '')]], $n === 'Luca' ? '#FFE8D6' : '#FFFFFF');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 370" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-weight="700" font-size="15"><text x="20" y="54">utenti</text><text x="236" y="54">ordini</text></g>
<g font-weight="700"><text x="36" y="86">id</text><text x="72" y="86">nome</text><text x="252" y="86">utente_id</text><text x="380" y="86" text-anchor="end">totale</text></g>
<g font-family="ui-monospace, monospace" font-weight="700" font-size="14"><text x="440" y="38" fill="#1B66BC">INNER JOIN</text><text x="440" y="198" fill="#B84508">LEFT JOIN</text></g>
{$g}
<g font-weight="700"><text x="456" y="68">nome</text><text x="604" y="68" text-anchor="end">totale</text><text x="456" y="228">nome</text><text x="604" y="228" text-anchor="end">totale</text></g>
<text x="20" y="232" font-weight="700" fill="#B84508">{$note1}</text>
<text x="20" y="254">{$note2}</text>
<text x="20" y="276">{$note3}</text>
</svg>
SVG;
    }
}

/** A foreign key points at an existing primary key; an order for a user who does not exist is refused. */
if (!function_exists('fig_sqlint_fk')) {
    function fig_sqlint_fk(string $fk, string $pk, string $refused): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-weight="700" font-size="15"><text x="20" y="28">ordini</text><text x="420" y="28">utenti</text></g>
<text x="185" y="52" text-anchor="middle" font-weight="700" fill="#1B66BC">{$fk}</text>
<text x="445" y="52" text-anchor="middle" font-weight="700" fill="#0B7F5C">{$pk}</text>
<rect x="130" y="62" width="110" height="128" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="420" y="62" width="50" height="128" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="20" y="62" width="300" height="128" rx="10" fill="none" stroke="#8A87B0" stroke-width="2"/>
<rect x="420" y="62" width="200" height="128" rx="10" fill="none" stroke="#8A87B0" stroke-width="2"/>
<g font-weight="700"><text x="36" y="84">id</text><text x="142" y="84">utente_id</text><text x="304" y="84" text-anchor="end">totale</text><text x="445" y="84" text-anchor="middle">id</text><text x="486" y="84">nome</text></g>
<text x="36" y="118">10</text><text x="185" y="118" text-anchor="middle">1</text><text x="304" y="118" text-anchor="end">50</text>
<text x="36" y="148">11</text><text x="185" y="148" text-anchor="middle">3</text><text x="304" y="148" text-anchor="end">35</text>
<text x="36" y="178">12</text><text x="185" y="178" text-anchor="middle">1</text><text x="304" y="178" text-anchor="end">20</text>
<text x="445" y="118" text-anchor="middle">1</text><text x="486" y="118">Anna</text>
<text x="445" y="148" text-anchor="middle">2</text><text x="486" y="148">Luca</text>
<text x="445" y="178" text-anchor="middle">3</text><text x="486" y="178">Sara</text>
<g fill="none" stroke="#2F8CF0" stroke-width="2"><path d="M320 113 H412"/><path d="M320 143 C 370 143, 362 173, 412 173"/><path d="M320 173 C 370 173, 362 113, 412 113"/></g><g fill="#2F8CF0"><circle cx="320" cy="113" r="4"/><circle cx="320" cy="143" r="4"/><circle cx="320" cy="173" r="4"/></g>
<g fill="#2F8CF0"><path d="M412 108 L420 113 L412 118 Z"/><path d="M412 168 L420 173 L412 178 Z"/></g>
<rect x="20" y="206" width="300" height="32" rx="8" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4"/>
<text x="36" y="227">13</text><text x="185" y="227" text-anchor="middle" font-weight="700">9</text><text x="304" y="227" text-anchor="end">40</text>
<path d="M320 222 H360" fill="none" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4"/>
<path d="M366 212 l20 20 M386 212 l-20 20" stroke="#F03E82" stroke-width="3" stroke-linecap="round"/>
<text x="20" y="266" font-weight="700" fill="#C2185B">{$refused}</text>
</svg>
SVG;
    }
}

/** The three set operators as overlapping circles: what each one keeps of A and B. */
if (!function_exists('fig_sqlint_sets')) {
    function fig_sqlint_sets(): string
    {
        $c = fn($x, $fill) => '<circle cx="' . $x . '" cy="120" r="56" fill="' . $fill . '"/>';
        $o = fn($x) => '<circle cx="' . $x . '" cy="120" r="56" fill="none" stroke="#8A87B0" stroke-width="2"/>';
        $ab = fn($cx) => '<text x="' . ($cx - 52) . '" y="125" text-anchor="middle" font-weight="700">A</text><text x="' . ($cx + 52) . '" y="125" text-anchor="middle" font-weight="700">B</text>';
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="16" fill="#1E1C52">'
            . '<defs><clipPath id="sqlint-a2"><circle cx="294" cy="120" r="56"/></clipPath><pattern id="sqlint-hatch" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="8" height="8" fill="#FFE8D6"/><line x1="0" y1="0" x2="0" y2="8" stroke="#E8590C" stroke-width="2"/></pattern></defs>'
            // UNION
            . $c(80, 'url(#sqlint-hatch)') . $c(136, 'url(#sqlint-hatch)') . $o(80) . $o(136) . $ab(108)
            // INTERSECT
            . '<circle cx="350" cy="120" r="56" fill="url(#sqlint-hatch)" clip-path="url(#sqlint-a2)"/>' . $o(294) . $o(350) . $ab(322)
            // EXCEPT
            . $c(506, 'url(#sqlint-hatch)') . $c(562, '#FFFFFF') . $o(506) . $o(562) . $ab(534)
            . '<g font-family="ui-monospace, monospace" font-weight="700" font-size="15" text-anchor="middle"><text x="108" y="210">A UNION B</text><text x="322" y="210">A INTERSECT B</text><text x="534" y="210">A EXCEPT B</text></g>'
            . '</svg>';
    }
}

/** A many-to-many relationship: students and courses joined by a junction table. */
if (!function_exists('fig_sqlint_er')) {
    function fig_sqlint_er(string $bridge): string
    {
        $box = function (int $x, int $w, string $name, array $cols, string $s, string $f) {
            $h = 40 + count($cols) * 30;
            $o = '<rect x="' . $x . '" y="60" width="' . $w . '" height="' . $h . '" rx="12" fill="#FFFFFF" stroke="' . $s . '" stroke-width="2"/>'
                . '<path d="M' . $x . ' 96 V72 a12 12 0 0 1 12 -12 H' . ($x + $w - 12) . ' a12 12 0 0 1 12 12 V96 Z" fill="' . $f . '" stroke="' . $s . '" stroke-width="2"/>'
                . '<text x="' . ($x + $w / 2) . '" y="84" text-anchor="middle" font-weight="700">' . $name . '</text>';
            foreach ($cols as $i => [$c, $tag]) {
                $y = 120 + $i * 30;
                $o .= '<text x="' . ($x + 12) . '" y="' . $y . '">' . $c . '</text>';
                if ($tag) $o .= '<text x="' . ($x + $w - 10) . '" y="' . $y . '" text-anchor="end" font-size="13" font-weight="700" fill="' . $s . '">' . $tag . '</text>';
            }
            return $o;
        };
        $g = $box(20, 160, 'studenti', [['id', 'PK'], ['nome', '']], '#2F8CF0', '#DCEBFF')
            . $box(236, 172, 'iscrizioni', [['studente_id', 'PK FK'], ['corso_id', 'PK FK'], ['iscritto_il', '']], '#7048E8', '#ECE6FF')
            . $box(464, 156, 'corsi', [['id', 'PK'], ['titolo', '']], '#0B7F5C', '#D5F6EA');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="322" y="40" text-anchor="middle" font-weight="700" fill="#4B2BBF">{$bridge}</text>
<path d="M180 115 C 210 115, 206 115, 236 115" fill="none" stroke="#1E1C52" stroke-width="2"/>
<path d="M408 145 C 436 145, 436 115, 464 115" fill="none" stroke="#1E1C52" stroke-width="2"/>
<path d="M236 115 L224 107 M236 115 L224 123" stroke="#1E1C52" stroke-width="2"/>
<path d="M408 145 L420 137 M408 145 L420 153" stroke="#1E1C52" stroke-width="2"/>
<g font-weight="700" font-size="14"><text x="190" y="108">1</text><text x="218" y="140">N</text><text x="452" y="108">1</text><text x="414" y="170">N</text></g>
{$g}
<text x="20" y="244" font-size="13" fill="#5A5880">PK = primary key · FK = foreign key</text>
</svg>
SVG;
    }
}

return [
    'id' => 'sql-intermediate', 'family' => 'sql', 'after' => 'sql-base',
    'title' => L('SQL intermedio', 'Intermediate SQL'), 'short' => L('SQL intermedio', 'SQL Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Contare e raggruppare, unire tabelle con le JOIN, sottoquery, chiavi esterne e indici, `UNION` ed `EXCEPT`, self join e progettazione delle tabelle.', 'Counting and grouping, joining tables, subqueries, foreign keys and indexes, `UNION` and `EXCEPT`, self joins and table design.'),
    'lessons' => [
        lesson('aggregates', L('Funzioni di aggregazione', 'Aggregate functions'), [
            P('Le **funzioni di aggregazione** riassumono tante righe in un valore: `COUNT` conta, `SUM` somma, `AVG` fa la media, `MIN` e `MAX` trovano gli estremi.', '**Aggregate functions** sum up many rows into one value: `COUNT` counts, `SUM` adds, `AVG` averages, `MIN` and `MAX` find the extremes.'),
            C('SELECT COUNT(*) AS iscritti FROM utenti;
SELECT AVG(voto) AS media, MAX(voto) AS migliore FROM esami;
SELECT SUM(totale) FROM ordini WHERE stato = \'pagato\';', 'sql', fucina_data_table(['media', 'migliore'], [[7.4, 10]], 'SELECT AVG(voto) AS media, MAX(voto) AS migliore FROM esami;')),
            P('`AS` dà un nome leggibile alla colonna del risultato. `COUNT(colonna)` conta solo i valori non `NULL`.', '`AS` gives the result column a readable name. `COUNT(column)` counts only non-`NULL` values.'),
            P('Un’aggregazione **schiaccia** tutte le righe in una sola: per questo, senza `GROUP BY`, il risultato ha una riga sola. Anche `SUM`, `AVG`, `MIN` e `MAX` ignorano i `NULL`: la media di 6, 8 e `NULL` è 7, non 4,67. Se vuoi contare i valori diversi, usa `COUNT(DISTINCT citta)`.', 'An aggregate **squashes** all the rows into one: that is why, without `GROUP BY`, the result has a single row. `SUM`, `AVG`, `MIN` and `MAX` ignore `NULL`s too: the average of 6, 8 and `NULL` is 7, not 4.67. To count the different values, use `COUNT(DISTINCT citta)`.'),
        ], [
            Q('a1', L('Quale funzione fa la media?', 'Which function averages?'), ['AVG', 'MEAN', 'SUM', 'MID'], 0, L('Da “average”.', 'From “average”.')),
            Q('a2', L('`COUNT(email)` conta le righe con email `NULL`?', 'Does `COUNT(email)` count rows with a `NULL` email?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo se vuota', 'Only if empty'), L('Dipende dall’ordine', 'It depends on order')], 0, L('`COUNT(*)` conta tutte le righe.', '`COUNT(*)` counts every row.')),
            Q('a3', L('A che cosa serve `AS`?', 'What is `AS` for?'), [L('Rinominare una colonna del risultato', 'Renaming a result column'), L('Creare una tabella', 'Creating a table'), L('Filtrare', 'Filtering'), L('Ordinare', 'Sorting')], 0, L('È un alias.', 'It is an alias.')),
        ], task('sql', L('Calcola `AVG(prezzo) AS medio` dalla tabella `prodotti`.', 'Compute `AVG(prezzo) AS medio` from the table `prodotti`.'), '', [re('SELECT\s+AVG\s*\(\s*prezzo\s*\)\s+AS\s+medio\s+FROM\s+prodotti', L('La media è giusta', 'The average is right'))], '', 'SELECT AVG(prezzo) AS medio FROM prodotti;'), [
            dMatch(pMatch(), [['COUNT', L('Conta', 'Counts')], ['SUM', L('Somma', 'Adds up')], ['AVG', L('Media', 'Average')], ['MIN', L('Il più piccolo', 'The smallest')], ['MAX', L('Il più grande', 'The largest')]], L('Le funzioni di aggregazione.', 'Aggregate functions.')),
            dBlanks(pFill(), "SELECT ___(*) ___ totale,\n       ___(eta) AS piu_giovane\nFROM utenti;", ['COUNT', 'AS', 'MIN'], ['SUM', 'IS', 'MAX'], L('Conta, rinomina, trova il minimo.', 'Count, rename, find the minimum.')),
        ]),
        lesson('group-by', L('GROUP BY e HAVING', 'GROUP BY and HAVING'), [
            P('`GROUP BY` divide le righe in gruppi e calcola un’aggregazione per ciascuno. `HAVING` filtra i **gruppi**, come `WHERE` filtra le righe.', '`GROUP BY` splits rows into groups and computes an aggregate for each. `HAVING` filters **groups**, as `WHERE` filters rows.'),
            Fig(fig_sqlint_group(),
                L('Sei righe di utenti, ognuna con la sua città, sono collegate da linee a tre righe di risultato: Roma con conteggio 3, Udine con 2, Trieste con 1.', 'Six user rows, each with its city, are linked by lines to three result rows: Roma with a count of 3, Udine with 2, Trieste with 1.'),
                L('Ogni gruppo diventa una riga sola: le colonne non raggruppate si possono solo aggregare.', 'Each group becomes a single row: the columns that are not grouped can only be aggregated.')),
            C('SELECT citta, COUNT(*) AS utenti
FROM utenti
WHERE attivo = 1
GROUP BY citta
HAVING COUNT(*) >= 10
ORDER BY utenti DESC;', 'sql', fucina_data_table(['citta', 'utenti'], [['Roma', 42], ['Milano', 31], ['Udine', 12]])),
            P('Nel `SELECT`, oltre alle aggregazioni, metti solo le colonne del `GROUP BY`. Alcuni database accettano anche colonne che dipendono dalla chiave primaria raggruppata, ma la regola semplice funziona ovunque.', 'In the `SELECT`, besides aggregates, put only the `GROUP BY` columns. Some databases also accept columns that depend on a grouped primary key, but the simple rule works everywhere.'),
            P('L’ordine conta: `WHERE` scarta le righe **prima** di formare i gruppi, `HAVING` scarta i gruppi **dopo** averli calcolati. Per questo `WHERE COUNT(*) > 10` dà errore: in quel momento i gruppi non esistono ancora. Regola pratica: filtra in `WHERE` tutto quello che puoi, è più veloce.', 'Order matters: `WHERE` drops rows **before** the groups are formed, `HAVING` drops groups **after** they are computed. That is why `WHERE COUNT(*) > 10` is an error: at that point the groups do not exist yet. Rule of thumb: filter in `WHERE` everything you can, it is faster.'),
        ], [
            Q('g1', L('Che differenza c’è tra `WHERE` e `HAVING`?', 'What is the difference between `WHERE` and `HAVING`?'), [L('WHERE filtra righe, HAVING gruppi', 'WHERE filters rows, HAVING groups'), L('Nessuna', 'None'), L('HAVING è più veloce', 'HAVING is faster'), L('WHERE va dopo', 'WHERE goes after')], 0, L('`HAVING` vede le aggregazioni.', '`HAVING` sees the aggregates.')),
            Q('g2', L('Quale colonna può stare nel SELECT con `GROUP BY citta`?', 'Which column may be in SELECT with `GROUP BY citta`?'), ['citta', 'nome', 'email', 'id'], 0, L('Oltre alle aggregazioni.', 'Besides aggregates.')),
            Q('g3', L('Dove va `HAVING`?', 'Where does `HAVING` go?'), [L('Dopo `GROUP BY`', 'After `GROUP BY`'), L('Prima di `FROM`', 'Before `FROM`'), L('Dopo `LIMIT`', 'After `LIMIT`'), L('Dentro `WHERE`', 'Inside `WHERE`')], 0, L('Prima di `ORDER BY`.', 'Before `ORDER BY`.')),
        ], task('sql', L('Conta gli ordini per `cliente_id` e tieni chi ne ha più di 3.', 'Count orders per `cliente_id` and keep those with more than 3.'), 'SELECT cliente_id, COUNT(*) AS ordini FROM ordini', [re('GROUP\s+BY\s+cliente_id', L('Raggruppi per cliente', 'You group by customer')), re('HAVING\s+COUNT\s*\(\s*\*\s*\)\s*>\s*3', L('Filtri i gruppi', 'You filter the groups'))], '', 'SELECT cliente_id, COUNT(*) AS ordini FROM ordini
GROUP BY cliente_id
HAVING COUNT(*) > 3;'), [
            dOrder(pSort(), ['SELECT corso, AVG(voto) AS media', 'FROM esami', 'WHERE anno = 2026', 'GROUP BY corso', 'HAVING AVG(voto) > 7', 'ORDER BY media DESC;'], L('Righe, gruppi, filtro sui gruppi, ordine.', 'Rows, groups, group filter, order.')),
            dBug(pBug(), ['SELECT citta, COUNT(*)', 'FROM utenti', 'WHERE COUNT(*) > 5', 'GROUP BY citta;'], 2, 'HAVING COUNT(*) > 5', L('Le aggregazioni si filtrano con `HAVING`, dopo `GROUP BY`.', 'Aggregates are filtered with `HAVING`, after `GROUP BY`.')),
        ]),
        lesson('joins', L('Le JOIN', 'Joins'), [
            P('Una **JOIN** unisce le righe di due tabelle che hanno un valore in comune. `INNER JOIN` tiene solo le coppie che combaciano; `LEFT JOIN` tiene tutte le righe della prima tabella, anche senza corrispondenza.', 'A **JOIN** combines rows of two tables sharing a value. `INNER JOIN` keeps only matching pairs; `LEFT JOIN` keeps every row of the first table, even without a match.'),
            C('SELECT u.nome, o.totale
FROM utenti u
INNER JOIN ordini o ON o.utente_id = u.id;

SELECT u.nome, COUNT(o.id) AS ordini
FROM utenti u
LEFT JOIN ordini o ON o.utente_id = u.id
GROUP BY u.id, u.nome;', 'sql', fucina_data_table(['nome', 'ordini'], [['Anna', 2], ['Luca', 0], ['Sara', 1]], 'SELECT u.nome, COUNT(o.id) AS ordini … LEFT JOIN …')),
            P('Le lettere `u` e `o` sono **alias** delle tabelle: accorciano la query e chiariscono da dove viene ogni colonna.', 'The letters `u` and `o` are table **aliases**: they shorten the query and make clear where each column comes from.'),
            P('Che cosa succede a chi non ha corrispondenza? Con `INNER JOIN` sparisce dal risultato; con `LEFT JOIN` resta, e le colonne della seconda tabella valgono `NULL`. Per questo nella seconda query `COUNT(o.id)` dà 0 a chi non ha ordini, mentre `COUNT(*)` gli darebbe 1: conta la riga, anche se è vuota.', 'What happens to a row with no match? With `INNER JOIN` it vanishes from the result; with `LEFT JOIN` it stays, and the second table’s columns are `NULL`. That is why in the second query `COUNT(o.id)` gives 0 to users with no orders, while `COUNT(*)` would give them 1: it counts the row, even an empty one.'),
            Fig(L(fig_sqlint_join('Luca non ha ordini:', 'INNER JOIN lo perde,', 'LEFT JOIN lo tiene con NULL.'), fig_sqlint_join('Luca has no orders:', 'INNER JOIN loses him,', 'LEFT JOIN keeps him with NULL.')),
                L('A sinistra le tabelle utenti (Anna, Luca, Sara) e ordini, con linee che collegano Anna a due ordini e Sara a uno. A destra i due risultati: INNER JOIN ha tre righe senza Luca, LEFT JOIN quattro righe, con Luca e totale NULL.', 'On the left the utenti table (Anna, Luca, Sara) and the ordini table, with lines linking Anna to two orders and Sara to one. On the right the two results: INNER JOIN has three rows without Luca, LEFT JOIN four rows, with Luca and a NULL total.'),
                L('Anna compare due volte: una riga per ogni ordine che combacia.', 'Anna appears twice: one row for every matching order.')),
        ], [
            Q('j1', L('Quale JOIN tiene anche gli utenti senza ordini?', 'Which JOIN keeps users with no orders?'), ['LEFT JOIN', 'INNER JOIN', 'CROSS JOIN', 'SELF JOIN'], 0, L('Le colonne mancanti diventano `NULL`.', 'Missing columns become `NULL`.')),
            Q('j2', L('Dove si scrive la condizione di unione?', 'Where do you write the join condition?'), [L('Dopo `ON`', 'After `ON`'), L('Dopo `WHERE`', 'After `WHERE`'), L('Dopo `AS`', 'After `AS`'), L('Dopo `SET`', 'After `SET`')], 0, L('`ON tabella.col = altra.col`.', '`ON table.col = other.col`.')),
            Q('j3', L('Che cos’è `u` in `FROM utenti u`?', 'What is `u` in `FROM utenti u`?'), [L('Un alias della tabella', 'A table alias'), L('Una colonna', 'A column'), L('Un errore', 'An error'), L('Un indice', 'An index')], 0, L('Un nome corto.', 'A short name.')),
        ], task('sql', L('Unisci `corsi c` e `lezioni l` con `INNER JOIN lezioni l ON l.corso_id = c.id`.', 'Join `corsi c` and `lezioni l` with `INNER JOIN lezioni l ON l.corso_id = c.id`.'), 'SELECT c.titolo, l.titolo
FROM corsi c', [re('INNER\s+JOIN\s+lezioni\s+l\s+ON\s+l\.corso_id\s*=\s*c\.id', L('La JOIN è giusta', 'The join is right'))], '', 'SELECT c.titolo, l.titolo
FROM corsi c
INNER JOIN lezioni l ON l.corso_id = c.id;'), [
            dMatch(pMatch(), [['INNER JOIN', L('Solo le coppie che combaciano', 'Only matching pairs')], ['LEFT JOIN', L('Tutte le righe a sinistra', 'All rows on the left')], ['RIGHT JOIN', L('Tutte le righe a destra', 'All rows on the right')], ['ON', L('La condizione di unione', 'The join condition')]], L('I tipi di JOIN.', 'Kinds of join.')),
            dBlanks(pFill(), "SELECT p.nome, c.nome\nFROM prodotti p\n___ JOIN categorie c ___ c.id = p.categoria_id;", ['LEFT', 'ON'], ['OUTSIDE', 'WHERE', 'USING'], L('Tutti i prodotti, anche senza categoria.', 'All products, even without a category.')),
        ]),
        lesson('subqueries', L('Sottoquery', 'Subqueries'), [
            P('Una **sottoquery** è una `SELECT` dentro un’altra. Può dare un valore da confrontare, un elenco per `IN` o una tabella temporanea dopo `FROM`.', 'A **subquery** is a `SELECT` inside another. It can give a value to compare, a list for `IN` or a temporary table after `FROM`.'),
            C('SELECT nome, prezzo FROM prodotti
WHERE prezzo > (SELECT AVG(prezzo) FROM prodotti);

SELECT nome FROM utenti
WHERE id IN (SELECT utente_id FROM ordini WHERE totale > 100);

SELECT * FROM utenti u
WHERE EXISTS (SELECT 1 FROM ordini o WHERE o.utente_id = u.id);', 'sql'),
            P('`EXISTS` risponde solo «c’è o non c’è?». Gli ottimizzatori moderni spesso eseguono `IN` ed `EXISTS` allo stesso modo; la differenza vera è con `NOT IN`: se la sottoquery contiene un `NULL`, non restituisce nessuna riga, mentre `NOT EXISTS` funziona come ci si aspetta.', '`EXISTS` only answers “is there or not?”. Modern optimisers often run `IN` and `EXISTS` the same way; the real difference is with `NOT IN`: if the subquery contains a `NULL`, it returns no rows at all, while `NOT EXISTS` works as expected.'),
        ], [
            Q('s1', L('Dove può stare una sottoquery?', 'Where can a subquery sit?'), [L('In WHERE, FROM o SELECT', 'In WHERE, FROM or SELECT'), L('Solo in WHERE', 'Only in WHERE'), L('Solo in ORDER BY', 'Only in ORDER BY'), L('Da nessuna parte', 'Nowhere')], 0, L('Ovunque serva un valore o una tabella.', 'Wherever a value or table is needed.')),
            Q('s2', L('Che cosa controlla `EXISTS`?', 'What does `EXISTS` check?'), [L('Se la sottoquery trova almeno una riga', 'Whether the subquery finds at least one row'), L('Se la tabella esiste', 'Whether the table exists'), L('Se un file esiste', 'Whether a file exists'), L('Il numero di colonne', 'The number of columns')], 0, L('Vero o falso.', 'True or false.')),
            Q('s3', L('Che cosa restituisce `(SELECT AVG(prezzo) FROM prodotti)`?', 'What does `(SELECT AVG(prezzo) FROM prodotti)` return?'), [L('Un solo valore', 'A single value'), L('Una tabella intera', 'A whole table'), L('Niente', 'Nothing'), L('Un errore', 'An error')], 0, L('Si può confrontare con `>`.', 'It can be compared with `>`.')),
        ], task('sql', L('Prendi gli studenti con voto sopra la media: `WHERE voto > (SELECT AVG(voto) FROM studenti)`.', 'Take students above average: `WHERE voto > (SELECT AVG(voto) FROM studenti)`.'), 'SELECT nome FROM studenti', [re('WHERE\s+voto\s*>\s*\(\s*SELECT\s+AVG\s*\(\s*voto\s*\)\s+FROM\s+studenti\s*\)', L('La sottoquery è giusta', 'The subquery is right'))], '', 'SELECT nome FROM studenti
WHERE voto > (SELECT AVG(voto) FROM studenti);'), [
            dOrder(pSort(), ['SELECT titolo', 'FROM corsi', 'WHERE id IN (', '    SELECT corso_id FROM iscrizioni', '    WHERE anno = 2026', ');'], L('Esterna, condizione, interna.', 'Outer, condition, inner.')),
            dFlash(pFlash(), [[L('Una sottoquery va tra parentesi', 'A subquery goes in brackets'), true], [L('`IN` vuole un elenco di valori', '`IN` wants a list of values'), true], [L('Una sottoquery non può stare dopo `FROM`', 'A subquery cannot sit after `FROM`'), false], [L('Dopo `WHERE prezzo =` una sottoquery deve restituire un solo valore', 'After `WHERE price =` a subquery must return a single value'), true]], L('Dopo `FROM` diventa una tabella derivata.', 'After `FROM` it becomes a derived table.')),
        ]),
        lesson('keys', L('Chiavi esterne e indici', 'Foreign keys and indexes'), [
            P('Una **chiave esterna** collega una colonna alla chiave primaria di un’altra tabella e impedisce riferimenti a righe inesistenti. Un **indice** è come l’indice di un libro: rende veloci le ricerche su una colonna.', 'A **foreign key** links a column to another table’s primary key and blocks references to missing rows. An **index** is like a book’s index: it makes searches on a column fast.'),
            C('CREATE TABLE ordini (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utente_id INT NOT NULL,
    totale DECIMAL(8, 2),
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
);

CREATE INDEX ix_ordini_utente ON ordini (utente_id);', 'sql'),
            Fig(L(fig_sqlint_fk('chiave esterna', 'chiave primaria', 'rifiutato: l’utente 9 non esiste'), fig_sqlint_fk('foreign key', 'primary key', 'refused: user 9 does not exist')),
                L('La colonna utente_id della tabella ordini ha frecce verso la colonna id della tabella utenti: gli ordini 10 e 12 puntano ad Anna, l’ordine 11 a Sara. Un nuovo ordine tratteggiato con utente_id 9 non trova nessuna riga ed è barrato.', 'The utente_id column of the ordini table has arrows to the id column of the utenti table: orders 10 and 12 point to Anna, order 11 to Sara. A new dashed order with utente_id 9 finds no row and is crossed out.'),
                L('La chiave esterna garantisce che ogni ordine appartenga a un utente vero.', 'The foreign key makes sure every order belongs to a real user.')),
            P('`ON DELETE CASCADE` cancella gli ordini quando sparisce l’utente. Gli indici velocizzano le letture ma rallentano un po’ le scritture: mettili sulle colonne che usi in `WHERE` e `JOIN`.', '`ON DELETE CASCADE` deletes orders when the user disappears. Indexes speed up reads but slow writes a little: put them on columns used in `WHERE` and `JOIN`.'),
        ], [
            Q('k1', L('Che cosa impedisce una chiave esterna?', 'What does a foreign key prevent?'), [L('Riferimenti a righe inesistenti', 'References to missing rows'), L('I doppioni', 'Duplicates'), L('I numeri negativi', 'Negative numbers'), L('Le query lente', 'Slow queries')], 0, L('Mantiene i dati coerenti.', 'It keeps data consistent.')),
            Q('k2', L('Che cosa fa `ON DELETE CASCADE`?', 'What does `ON DELETE CASCADE` do?'), [L('Cancella anche le righe collegate', 'Also deletes linked rows'), L('Blocca la cancellazione', 'Blocks deletion'), L('Duplica le righe', 'Duplicates rows'), L('Niente', 'Nothing')], 0, L('Le righe figlie seguono il padre.', 'Child rows follow the parent.')),
            Q('k3', L('Su quali colonne conviene un indice?', 'Which columns deserve an index?'), [L('Quelle usate in WHERE e JOIN', 'Those used in WHERE and JOIN'), L('Tutte, sempre', 'All, always'), L('Nessuna', 'None'), L('Solo il testo lungo', 'Only long text')], 0, L('Gli indici hanno un costo in scrittura.', 'Indexes cost something on writes.')),
        ], task('sql', L('Crea l’indice `ix_utenti_email` sulla colonna `email` di `utenti`.', 'Create the index `ix_utenti_email` on the `email` column of `utenti`.'), '', [re('CREATE\s+(UNIQUE\s+)?INDEX\s+ix_utenti_email\s+ON\s+utenti\s*\(\s*email\s*\)', L('L’indice è giusto', 'The index is right'))], '', 'CREATE INDEX ix_utenti_email ON utenti (email);'), [
            dBlanks(pFill(), "FOREIGN ___ (corso_id) ___ corsi(id)\n    ON DELETE ___", ['KEY', 'REFERENCES', 'CASCADE'], ['INDEX', 'LINKS', 'REMOVE'], L('Chiave, riferimento, comportamento.', 'Key, reference, behaviour.')),
            dFlash(pFlash(), [[L('Un indice velocizza le ricerche', 'An index speeds up searches'), true], [L('Gli indici rendono più veloci anche le scritture', 'Indexes also speed up writes'), false], [L('Una chiave esterna punta a una chiave primaria (o a una colonna `UNIQUE`)', 'A foreign key points to a primary key (or a `UNIQUE` column)'), true], [L('Una chiave primaria può contenere valori ripetuti', 'A primary key can contain repeated values'), false]], L('Le scritture rallentano un po’.', 'Writes get a bit slower.')),
        ]),
        lesson('set-ops', L('UNION, INTERSECT ed EXCEPT', 'UNION, INTERSECT and EXCEPT'), [
            P('Gli **operatori insiemistici** combinano i risultati di due `SELECT` uno sotto l’altro. `UNION` li unisce togliendo i doppioni, `UNION ALL` li tiene tutti (ed è più veloce), `INTERSECT` tiene le righe presenti in entrambi, `EXCEPT` quelle del primo che mancano nel secondo.', '**Set operators** combine the results of two `SELECT`s one below the other. `UNION` merges them removing duplicates, `UNION ALL` keeps them all (and is faster), `INTERSECT` keeps the rows found in both, `EXCEPT` those of the first that are missing from the second.'),
            C('SELECT email FROM clienti
UNION
SELECT email FROM iscritti_newsletter;

SELECT email FROM clienti
EXCEPT
SELECT email FROM disiscritti
ORDER BY email;', 'sql'),
            Fig(fig_sqlint_sets(),
                L('Tre coppie di cerchi sovrapposti A e B. In UNION sono colorati entrambi i cerchi, in INTERSECT solo la parte comune, in EXCEPT solo la parte di A fuori da B.', 'Three pairs of overlapping circles A and B. In UNION both circles are shaded, in INTERSECT only the shared part, in EXCEPT only the part of A outside B.'),
                L('La parte tratteggiata è quello che finisce nel risultato.', 'The hatched part is what ends up in the result.')),
            P('Le due query devono avere lo **stesso numero di colonne**, con tipi compatibili; i nomi delle colonne vengono dalla prima. L’`ORDER BY` va una sola volta, in fondo. `INTERSECT` ed `EXCEPT` esistono in PostgreSQL, SQL Server, SQLite e da MySQL 8.0.31; Oracle chiama `EXCEPT` anche `MINUS`.', 'Both queries must have the **same number of columns**, with compatible types; column names come from the first one. `ORDER BY` goes only once, at the end. `INTERSECT` and `EXCEPT` exist in PostgreSQL, SQL Server, SQLite and since MySQL 8.0.31; Oracle also calls `EXCEPT` `MINUS`.'),
        ], [
            Q('u1', L('Che differenza c’è tra `UNION` e `UNION ALL`?', 'What is the difference between `UNION` and `UNION ALL`?'), [L('`UNION ALL` toglie i doppioni, `UNION` li tiene', '`UNION ALL` removes duplicates, `UNION` keeps them'), L('`UNION` toglie i doppioni, `UNION ALL` li tiene', '`UNION` removes duplicates, `UNION ALL` keeps them'), L('`UNION ALL` unisce anche le colonne diverse', '`UNION ALL` also merges different columns'), L('Nessuna, cambia solo il nome', 'None, only the name changes')], 1, L('Togliere i doppioni costa: se non servono, usa `UNION ALL`.', 'Removing duplicates costs time: if you do not need it, use `UNION ALL`.')),
            Q('u2', L('Quale operatore trova le email dei clienti che non si sono disiscritti?', 'Which operator finds the emails of customers who have not unsubscribed?'), ['UNION', 'INTERSECT', 'EXCEPT', 'JOIN ALL'], 2, L('Le righe del primo che mancano nel secondo.', 'The rows of the first that are missing from the second.')),
            Q('u3', L('Che cosa devono avere in comune le due `SELECT`?', 'What must the two `SELECT`s have in common?'), [L('Lo stesso numero di colonne, con tipi compatibili', 'The same number of columns, with compatible types'), L('La stessa tabella nel `FROM`', 'The same table in the `FROM`'), L('Lo stesso `WHERE` su ogni riga', 'The same `WHERE` on every row'), L('Gli stessi nomi di alias per tutte le colonne', 'The same alias names for every column')], 0, L('I nomi vengono dalla prima query.', 'Names come from the first query.')),
        ], task('sql', L('Unisci in un solo elenco, senza doppioni, la colonna `citta` di `clienti` e quella di `fornitori`.', 'Merge into a single list, without duplicates, the `citta` column of `clienti` and that of `fornitori`.'), '', [re('SELECT\s+citta\s+FROM\s+clienti\s+UNION\s+SELECT\s+citta\s+FROM\s+fornitori|SELECT\s+citta\s+FROM\s+fornitori\s+UNION\s+SELECT\s+citta\s+FROM\s+clienti', L('Unisci le due città con UNION', 'You merge both cities with UNION'))], '', "SELECT citta FROM clienti\nUNION\nSELECT citta FROM fornitori;"), [
            dMatch(pMatch(), [['UNION', L('Unisce senza doppioni', 'Merges without duplicates')], ['UNION ALL', L('Unisce tenendo tutto', 'Merges keeping everything')], ['INTERSECT', L('Righe in entrambi', 'Rows in both')], ['EXCEPT', L('Nel primo ma non nel secondo', 'In the first but not the second')]], L('Gli operatori insiemistici.', 'The set operators.')),
            dBug(pBug(), ['SELECT nome, email FROM clienti', 'UNION', 'SELECT nome FROM fornitori;'], 2, 'SELECT nome, email FROM fornitori;', L('Le due query devono avere lo stesso numero di colonne.', 'Both queries must have the same number of columns.')),
        ]),
        lesson('more-joins', L('JOIN multiple e self join', 'Multiple joins and self joins'), [
            P('Si possono unire **più tabelle** di fila: ogni `JOIN` aggiunge una tabella con la sua condizione `ON`. Una **self join** unisce una tabella a sé stessa, con due alias diversi: serve per le gerarchie, come dipendente e responsabile nella stessa tabella.', 'You can join **several tables** in a row: each `JOIN` adds a table with its own `ON` condition. A **self join** joins a table to itself, with two different aliases: it is used for hierarchies, such as employee and manager in the same table.'),
            C('SELECT c.nome, o.id, p.nome AS prodotto
FROM clienti c
JOIN ordini o ON o.cliente_id = c.id
JOIN righe_ordine r ON r.ordine_id = o.id
JOIN prodotti p ON p.id = r.prodotto_id;

SELECT d.nome, capo.nome AS responsabile
FROM dipendenti d
LEFT JOIN dipendenti capo ON capo.id = d.capo_id;

-- clienti senza ordini (anti-join)
SELECT c.nome FROM clienti c
LEFT JOIN ordini o ON o.cliente_id = c.id
WHERE o.id IS NULL;', 'sql', fucina_data_table(['nome', 'responsabile'], [['Giulia', null], ['Marco', 'Giulia'], ['Sara', 'Giulia'], ['Luca', 'Marco']], 'SELECT d.nome, capo.nome AS responsabile … LEFT JOIN dipendenti capo …')),
            P('`LEFT JOIN … WHERE destra.id IS NULL` trova le righe **senza** corrispondenza. `FULL OUTER JOIN` tiene le righe di entrambe le tabelle (MySQL non lo ha); `CROSS JOIN` combina ogni riga con ogni altra: con due tabelle da 1.000 righe dà un milione di righe.', '`LEFT JOIN … WHERE right.id IS NULL` finds the rows **without** a match. `FULL OUTER JOIN` keeps the rows of both tables (MySQL lacks it); `CROSS JOIN` combines every row with every other: with two 1,000-row tables it gives a million rows.'),
        ], [
            Q('m1', L('A che cosa serve una self join?', 'What is a self join for?'), [L('A copiare una tabella in un’altra', 'Copying a table into another'), L('A unire una tabella a sé stessa, per esempio per le gerarchie', 'Joining a table to itself, for example for hierarchies'), L('A unire una tabella senza alcuna condizione', 'Joining a table with no condition at all'), L('A cancellare le righe doppie di una tabella', 'Deleting a table’s duplicate rows')], 1, L('Due alias della stessa tabella.', 'Two aliases of the same table.')),
            Q('m2', L('Come trovi i clienti che non hanno ordini?', 'How do you find customers with no orders?'), [L('`INNER JOIN` e poi `WHERE o.id IS NOT NULL`', '`INNER JOIN` then `WHERE o.id IS NOT NULL`'), L('`CROSS JOIN` e poi `DISTINCT`', '`CROSS JOIN` then `DISTINCT`'), L('`RIGHT JOIN` senza nessuna condizione', '`RIGHT JOIN` with no condition'), L('`LEFT JOIN` e poi `WHERE o.id IS NULL`', '`LEFT JOIN` then `WHERE o.id IS NULL`')], 3, L('Le righe senza corrispondenza hanno `NULL` a destra.', 'Rows without a match have `NULL` on the right.')),
            Q('m3', L('Quante righe dà `CROSS JOIN` tra una tabella da 10 righe e una da 5?', 'How many rows does a `CROSS JOIN` between a 10-row and a 5-row table give?'), ['15', '10', '50', '5'], 2, L('Ogni riga con ogni altra: 10 × 5.', 'Every row with every other: 10 × 5.')),
        ], task('sql', L('Trova i `nome` dei `prodotti` mai ordinati: `LEFT JOIN righe_ordine r ON r.prodotto_id = p.id` e tieni le righe dove `r.id` è `NULL`.', 'Find the `nome` of `prodotti` never ordered: `LEFT JOIN righe_ordine r ON r.prodotto_id = p.id` and keep the rows where `r.id` is `NULL`.'), 'SELECT p.nome
FROM prodotti p', [re('LEFT\s+(OUTER\s+)?JOIN\s+righe_ordine\s+(AS\s+)?r\s+ON\s+(r\.prodotto_id\s*=\s*p\.id|p\.id\s*=\s*r\.prodotto_id)', L('Unisci con LEFT JOIN', 'You join with LEFT JOIN')), re('WHERE\s+r\.id\s+IS\s+NULL', L('Tieni chi non ha corrispondenza', 'You keep those without a match'))], '', "SELECT p.nome\nFROM prodotti p\nLEFT JOIN righe_ordine r ON r.prodotto_id = p.id\nWHERE r.id IS NULL;"), [
            dOrder(pSort(), ['SELECT c.nome, p.nome', 'FROM clienti c', 'JOIN ordini o ON o.cliente_id = c.id', 'JOIN righe_ordine r ON r.ordine_id = o.id', 'JOIN prodotti p ON p.id = r.prodotto_id;'], L('Una JOIN alla volta, seguendo le chiavi.', 'One join at a time, following the keys.')),
            dFlash(pFlash(), [[L('Una self join usa due alias della stessa tabella', 'A self join uses two aliases of the same table'), true], [L('MySQL supporta `FULL OUTER JOIN`', 'MySQL supports `FULL OUTER JOIN`'), false], [L('`CROSS JOIN` combina ogni riga con ogni altra', '`CROSS JOIN` combines every row with every other'), true], [L('Si possono fare al massimo due JOIN per query', 'You can do at most two joins per query'), false]], L('Le JOIN si concatenano.', 'Joins chain together.')),
        ]),
        lesson('design', L('Progettare le tabelle', 'Designing tables'), [
            P('**Normalizzare** vuol dire dividere i dati in tabelle in modo che ogni informazione stia in **un solo posto**. Se il nome di un cliente è ripetuto in ogni ordine, cambiarlo significa aggiornare mille righe; con una tabella `clienti` e una chiave esterna basta una riga.', '**Normalising** means splitting data into tables so that each piece of information lives in **one place only**. If a customer’s name is repeated in every order, changing it means updating a thousand rows; with a `clienti` table and a foreign key one row is enough.'),
            C('-- relazione molti-a-molti: studenti e corsi
CREATE TABLE iscrizioni (
    studente_id INT NOT NULL REFERENCES studenti(id),
    corso_id INT NOT NULL REFERENCES corsi(id),
    iscritto_il DATE,
    PRIMARY KEY (studente_id, corso_id)
);', 'sql'),
            Fig(L(fig_sqlint_er('tabella ponte'), fig_sqlint_er('junction table')),
                L('Tre riquadri: studenti (id, nome), iscrizioni (studente_id, corso_id, iscritto_il) e corsi (id, titolo). Una linea va da studenti a iscrizioni e una da corsi a iscrizioni, con 1 dal lato di studenti e corsi e N dal lato di iscrizioni.', 'Three boxes: studenti (id, nome), iscrizioni (studente_id, corso_id, iscritto_il) and corsi (id, titolo). One line runs from studenti to iscrizioni and one from corsi to iscrizioni, with 1 on the studenti and corsi side and N on the iscrizioni side.'),
                L('Un molti-a-molti si scioglie in due uno-a-molti, attraverso la tabella ponte.', 'A many-to-many is split into two one-to-many relationships, through the junction table.')),
            P('Le relazioni sono di tre tipi: **uno-a-molti** (un cliente, tanti ordini: chiave esterna nella tabella «molti»), **molti-a-molti** (studenti e corsi: serve una **tabella ponte** con le due chiavi), **uno-a-uno** (raro, per esempio dati opzionali a parte). Regole pratiche: niente liste in una cella (`"sql,php"`), niente colonne numerate (`telefono1`, `telefono2`).', 'Relationships come in three kinds: **one-to-many** (one customer, many orders: foreign key in the “many” table), **many-to-many** (students and courses: you need a **junction table** with both keys), **one-to-one** (rare, for example optional data kept apart). Practical rules: no lists in a cell (`"sql,php"`), no numbered columns (`telefono1`, `telefono2`).'),
        ], [
            Q('d1', L('Come si rappresenta una relazione molti-a-molti?', 'How do you represent a many-to-many relationship?'), [L('Con una colonna che elenca gli id separati da virgole', 'With a column listing the ids separated by commas'), L('Con una chiave esterna in entrambe le tabelle', 'With a foreign key in both tables'), L('Con una tabella ponte che contiene le due chiavi', 'With a junction table holding both keys'), L('Con una sola tabella che contiene tutto', 'With a single table holding everything')], 2, L('Per esempio `iscrizioni (studente_id, corso_id)`.', 'For example `iscrizioni (studente_id, corso_id)`.')),
            Q('d2', L('In una relazione uno-a-molti, dove va la chiave esterna?', 'In a one-to-many relationship, where does the foreign key go?'), [L('Nella tabella dal lato «molti»', 'In the table on the “many” side'), L('Nella tabella dal lato «uno»', 'In the table on the “one” side'), L('In una terza tabella separata', 'In a separate third table'), L('Non serve nessuna chiave esterna', 'No foreign key is needed')], 0, L('Ogni ordine sa a quale cliente appartiene.', 'Each order knows which customer it belongs to.')),
            Q('d3', L('Perché evitare di ripetere il nome del cliente in ogni ordine?', 'Why avoid repeating the customer’s name in every order?'), [L('Perché i nomi lunghi rallentano le query', 'Because long names slow down queries'), L('Perché i database non accettano testi ripetuti', 'Because databases do not accept repeated text'), L('Perché le tabelle hanno un limite di colonne', 'Because tables have a column limit'), L('Perché un cambio andrebbe fatto in tante righe', 'Because a change would have to be made in many rows')], 3, L('Un dato in un posto solo resta coerente.', 'A fact in one place stays consistent.')),
        ], task('sql', L('Crea la tabella ponte `autori_libri` con `autore_id INT` e `libro_id INT` e la chiave primaria composta dalle due colonne.', 'Create the junction table `autori_libri` with `autore_id INT` and `libro_id INT` and a primary key made of both columns.'), '', [re('CREATE\s+TABLE\s+autori_libri', L('La tabella esiste', 'The table exists')), re('autore_id\s+INT', L('C’è l’autore', 'There is the author')), re('libro_id\s+INT', L('C’è il libro', 'There is the book')), re('PRIMARY\s+KEY\s*\(\s*(autore_id\s*,\s*libro_id|libro_id\s*,\s*autore_id)\s*\)', L('La chiave usa le due colonne', 'The key uses both columns'))], '', "CREATE TABLE autori_libri (\n    autore_id INT NOT NULL,\n    libro_id INT NOT NULL,\n    PRIMARY KEY (autore_id, libro_id)\n);"), [
            dMatch(pMatch(), [[L('Uno-a-molti', 'One-to-many'), L('Un cliente, tanti ordini', 'One customer, many orders')], [L('Molti-a-molti', 'Many-to-many'), L('Studenti e corsi', 'Students and courses')], [L('Tabella ponte', 'Junction table'), L('Contiene le due chiavi', 'Holds both keys')], [L('Normalizzare', 'Normalising'), L('Ogni dato in un posto', 'Each fact in one place')]], L('Le relazioni tra tabelle.', 'Relationships between tables.')),
            dBug(pBug(), ['CREATE TABLE studenti (', '    id INT PRIMARY KEY,', '    nome VARCHAR(80),', '    corsi VARCHAR(200)  -- "sql,php,css"', ');'], 3, '    -- i corsi vanno nella tabella ponte iscrizioni', L('Una lista in una cella non si filtra né si collega: serve una tabella ponte.', 'A list in a cell cannot be filtered or linked: you need a junction table.')),
        ]),
    ],
];
