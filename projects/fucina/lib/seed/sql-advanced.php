<?php
/** SQL advanced: CTEs, window functions and frames, transactions, concurrency and locks, views, query plans and triggers. */

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

/** GROUP BY collapses five rows into two; a window function keeps all five and adds the group value to each. */
if (!function_exists('fig_sqladv_window')) {
    function fig_sqladv_window(string $collapse, string $keep): string
    {
        $c = ['HTML' => ['#2F8CF0', '#DCEBFF'], 'SQL' => ['#0B7F5C', '#D5F6EA']];
        $g = '';
        foreach ([['HTML', '7.5'], ['SQL', '8.0']] as $i => [$k, $v]) {
            $y = 104 + $i * 34;
            $g .= '<rect x="32" y="' . ($y - 20) . '" width="186" height="30" rx="8" fill="' . $c[$k][1] . '" stroke="' . $c[$k][0] . '" stroke-width="2"/><text x="44" y="' . $y . '">' . $k . '</text><text x="206" y="' . $y . '" text-anchor="end" font-weight="700">' . $v . '</text>';
        }
        foreach ([['Anna', 'HTML', 8, '7.5'], ['Luca', 'HTML', 7, '7.5'], ['Sara', 'SQL', 9, '8.0'], ['Marco', 'SQL', 7, '8.0'], ['Elena', 'SQL', 8, '8.0']] as $i => [$n, $k, $v, $m]) {
            $y = 104 + $i * 34;
            $g .= '<rect x="272" y="' . ($y - 20) . '" width="336" height="30" rx="8" fill="' . $c[$k][1] . '" stroke="' . $c[$k][0] . '" stroke-width="2"/><text x="284" y="' . $y . '">' . $n . '</text><text x="380" y="' . $y . '">' . $k . '</text><text x="476" y="' . $y . '" text-anchor="end">' . $v . '</text><text x="596" y="' . $y . '" text-anchor="end" font-weight="700">' . $m . '</text>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="20" y="12" width="210" height="266" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="260" y="12" width="360" height="266" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<g font-family="ui-monospace, monospace" font-weight="700" font-size="13" fill="#4B2BBF"><text x="32" y="36">GROUP BY corso</text><text x="272" y="36">AVG(voto) OVER (PARTITION BY corso)</text></g>
<text x="32" y="56" font-size="13" fill="#5A5880">{$collapse}</text><text x="272" y="56" font-size="13" fill="#5A5880">{$keep}</text>
<g font-weight="700" font-size="13"><text x="44" y="76">corso</text><text x="206" y="76" text-anchor="end">AVG</text><text x="284" y="76">nome</text><text x="380" y="76">corso</text><text x="476" y="76" text-anchor="end">voto</text><text x="596" y="76" text-anchor="end">media_corso</text></g>
{$g}
</svg>
SVG;
    }
}

/** A transfer in a transaction: COMMIT keeps both changes, an error and ROLLBACK keep neither. */
if (!function_exists('fig_sqladv_tx')) {
    function fig_sqladv_tx(string $before, string $acc1, string $acc2, string $error, string $okNote, string $koNote): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="20" y="24" font-size="13" fill="#5A5880">{$before}</text>
<g stroke-width="2">
<rect x="20" y="40" width="84" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0"/>
<rect x="124" y="40" width="140" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/>
<rect x="284" y="40" width="140" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/>
<rect x="444" y="40" width="110" height="40" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="20" y="170" width="84" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0"/>
<rect x="124" y="170" width="140" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/>
<rect x="284" y="170" width="140" height="40" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-dasharray="6 4"/>
<rect x="444" y="170" width="110" height="40" rx="10" fill="#FFE8D6" stroke="#E8590C"/>
</g>
<g font-family="ui-monospace, monospace" font-weight="700" font-size="13" text-anchor="middle"><text x="62" y="65">START</text><text x="499" y="65" fill="#0B7F5C">COMMIT</text><text x="62" y="195">START</text><text x="499" y="195" fill="#B84508">ROLLBACK</text></g>
<g text-anchor="middle"><text x="194" y="65">{$acc1}</text><text x="354" y="65">{$acc2}</text><text x="194" y="195">{$acc1}</text><text x="354" y="195" font-weight="700" fill="#C2185B">{$error}</text></g>
<g fill="#1E1C52"><path d="M108 55 L118 60 L108 65 Z"/><path d="M268 55 L278 60 L268 65 Z"/><path d="M428 55 L438 60 L428 65 Z"/><path d="M108 185 L118 190 L108 195 Z"/><path d="M268 185 L278 190 L268 195 Z"/><path d="M428 185 L438 190 L428 195 Z"/></g>
<path d="M568 50 l7 7 l13 -15" fill="none" stroke="#0B7F5C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
<text x="20" y="110" fill="#0B7F5C" font-weight="700">{$okNote}</text>
<path d="M499 168 C 499 140, 62 140, 62 162" fill="none" stroke="#E8590C" stroke-width="2" stroke-dasharray="6 4"/><path d="M56 158 L62 168 L68 158 Z" fill="#E8590C"/>
<text x="20" y="240" fill="#B84508" font-weight="700">{$koNote}</text>
</svg>
SVG;
    }
}

/** A B-tree index: looking up 42 reads three nodes, from the root down to a leaf. A dot travels the path. */
if (!function_exists('fig_sqladv_btree')) {
    function fig_sqladv_btree(string $root, string $leaves, string $find): string
    {
        $node = fn(int $x, int $y, int $w, string $t, bool $hot) => '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="36" rx="10" fill="' . ($hot ? '#FFE8D6' : '#DCEBFF') . '" stroke="' . ($hot ? '#E8590C' : '#2F8CF0') . '" stroke-width="2"/><text x="' . ($x + $w / 2) . '" y="' . ($y + 23) . '" text-anchor="middle" font-weight="700">' . $t . '</text>';
        $g = $node(280, 24, 80, '50', true) . $node(130, 110, 110, '20 · 35', true) . $node(400, 110, 110, '70 · 85', false);
        $leaf = ['5 · 12', '20 · 28', '35 · 42', '50 · 61', '70 · 77', '85 · 93'];
        $lines = '<g stroke="#8A87B0" stroke-width="2"><line x1="300" y1="60" x2="185" y2="110"/><line x1="340" y1="60" x2="455" y2="110"/>';
        foreach ($leaf as $i => $t) {
            $x = 20 + $i * 104;
            $g .= $node($x, 200, 84, $t, $i === 2);
            $px = $i < 3 ? 140 + $i * 45 : 410 + ($i - 3) * 45;
            $lines .= '<line x1="' . $px . '" y1="146" x2="' . ($x + 42) . '" y2="200"/>';
        }
        $lines .= '</g><g stroke="#E8590C" stroke-width="3"><line x1="300" y1="60" x2="185" y2="110"/><line x1="230" y1="146" x2="270" y2="200"/></g>';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.seek{animation:seek 6s ease-in-out infinite}@keyframes seek{0%,10%{transform:translate(0,0)}35%,45%{transform:translate(-135px,86px)}70%,100%{transform:translate(-50px,176px)}}@media (prefers-reduced-motion:reduce){.seek{animation:none}}</style>
{$lines}
{$g}
<text x="372" y="47" font-size="14" fill="#5A5880">{$root}</text>
<circle class="seek" cx="320" cy="20" r="6" fill="#E8590C"/>
<text x="20" y="274" font-size="14" fill="#5A5880">{$leaves}</text>
<text x="20" y="300" font-size="14" font-weight="700" fill="#B84508">{$find}</text>
</svg>
SVG;
    }
}

/** A frame of seven rows (6 PRECEDING to CURRENT ROW) sliding over ten days of takings. */
if (!function_exists('fig_sqladv_frame')) {
    function fig_sqladv_frame(): string
    {
        $v = [30, 45, 38, 52, 60, 48, 70, 65, 58, 80];
        $bars = '';
        foreach ($v as $i => $n) {
            $x = 40 + $i * 58;
            $h = (int)round($n * 1.7);
            $bars .= '<rect x="' . $x . '" y="' . (196 - $h) . '" width="40" height="' . $h . '" rx="6" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="' . ($x + 20) . '" y="218" text-anchor="middle" font-size="13">' . ($i + 1) . '</text>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.slide{animation:slide 12s ease-in-out infinite}@keyframes slide{0%,18%{transform:translateX(0)}25%,43%{transform:translateX(58px)}50%,68%{transform:translateX(116px)}75%,93%{transform:translateX(174px)}100%{transform:translateX(0)}}@media (prefers-reduced-motion:reduce){.slide{animation:none}}</style>
<g class="slide"><rect x="32" y="52" width="404" height="152" rx="12" fill="#ECE6FF"/></g>
{$bars}
<g class="slide">
<rect x="32" y="52" width="404" height="152" rx="12" fill="none" stroke="#7048E8" stroke-width="2"/>
<rect x="384" y="56" width="48" height="144" rx="8" fill="none" stroke="#E8590C" stroke-width="3"/>
<g font-family="ui-monospace, monospace" font-weight="700" font-size="13"><text x="36" y="40" fill="#4B2BBF">6 PRECEDING</text><text x="436" y="40" text-anchor="end" fill="#B84508">CURRENT ROW</text></g>
</g>
<text x="40" y="234" font-size="13" fill="#5A5880">giorno</text>
</svg>
SVG;
    }
}

/** Two transactions and a row lock: the second waits until the first commits. */
if (!function_exists('fig_sqladv_lock')) {
    function fig_sqladv_lock(string $lock, string $wait, string $time): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><pattern id="sqladv-wait" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="8" height="8" fill="#F1F0F8"/><line x1="0" y1="0" x2="0" y2="8" stroke="#8A87B0" stroke-width="2"/></pattern></defs>
<g font-weight="700" font-size="16"><text x="20" y="66">T1</text><text x="20" y="166">T2</text></g>
<g stroke-width="2">
<rect x="60" y="44" width="80" height="34" rx="10" fill="#F1F0F8" stroke="#8A87B0"/>
<rect x="150" y="44" width="180" height="34" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/>
<rect x="340" y="44" width="90" height="34" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/>
<rect x="440" y="44" width="90" height="34" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="150" y="86" width="380" height="14" rx="7" fill="#FFE8D6" stroke="#E8590C"/>
<rect x="210" y="144" width="80" height="34" rx="10" fill="#F1F0F8" stroke="#8A87B0"/>
<rect x="300" y="144" width="230" height="34" rx="10" fill="url(#sqladv-wait)" stroke="#8A87B0"/>
<rect x="540" y="144" width="90" height="34" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/>
</g>
<g font-family="ui-monospace, monospace" font-size="13" font-weight="700" text-anchor="middle"><text x="100" y="66">START</text><text x="240" y="66">SELECT … FOR UPDATE</text><text x="385" y="66">UPDATE</text><text x="485" y="66" fill="#0B7F5C">COMMIT</text><text x="250" y="166">START</text><text x="585" y="166">UPDATE</text></g>
<text x="340" y="122" text-anchor="middle" font-size="13" font-weight="700" fill="#B84508">{$lock}</text>
<rect x="352" y="150" width="126" height="22" rx="6" fill="#FFFFFF"/><text x="415" y="166" text-anchor="middle" font-weight="700">{$wait}</text>
<path d="M530 100 V140" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="5 4"/><path d="M525 136 L530 144 L535 136 Z" fill="#0B7F5C"/>
<path d="M60 220 H610" stroke="#8A87B0" stroke-width="2"/><path d="M610 214 L620 220 L610 226 Z" fill="#8A87B0"/>
<text x="60" y="242" font-size="13" fill="#5A5880">{$time}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'sql-advanced', 'family' => 'sql', 'after' => 'sql-intermediate',
    'title' => L('SQL avanzato', 'Advanced SQL'), 'short' => L('SQL avanzato', 'SQL Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('CTE e ricorsione, funzioni finestra e cornici, transazioni, concorrenza e lock, viste, piani di esecuzione e trigger.', 'CTEs and recursion, window functions and frames, transactions, concurrency and locks, views, execution plans and triggers.'),
    'lessons' => [
        lesson('cte', L('CTE con WITH', 'CTEs with WITH'), [
            P('Una **CTE** (Common Table Expression) dà un nome a una sottoquery con `WITH`, così la query principale resta leggibile. Con `WITH RECURSIVE` si percorrono strutture ad albero, come categorie e sottocategorie.', 'A **CTE** (Common Table Expression) names a subquery with `WITH`, so the main query stays readable. With `WITH RECURSIVE` you walk tree structures, like categories and subcategories.'),
            C('WITH spese AS (
    SELECT utente_id, SUM(totale) AS speso
    FROM ordini
    GROUP BY utente_id
)
SELECT u.nome, s.speso
FROM utenti u JOIN spese s ON s.utente_id = u.id
WHERE s.speso > 500;

WITH RECURSIVE numeri(n) AS (
    SELECT 1
    UNION ALL
    SELECT n + 1 FROM numeri WHERE n < 10
)
SELECT n FROM numeri;', 'sql', fucina_data_table(['nome', 'speso'], [['Anna', 742.5], ['Marco', 615.0], ['Giulia', 530.9]], 'WITH spese AS (…) SELECT u.nome, s.speso … WHERE s.speso > 500;')),
            P('Una CTE si legge dall’alto in basso, come una ricetta: prima prepari `spese`, poi la usi come se fosse una tabella. Puoi scriverne diverse di fila, separate da virgole, e ognuna può usare quelle prima: una query lunga diventa una serie di passi con un nome.', 'A CTE reads from top to bottom, like a recipe: first you prepare `spese`, then you use it as if it were a table. You can write several in a row, separated by commas, and each can use the ones before: a long query becomes a series of named steps.'),
            P('Una CTE ricorsiva ha un punto di partenza, `UNION ALL`, e un passo che richiama sé stesso finché la condizione lo permette. Nell’esempio il punto di partenza dà 1, ogni giro aggiunge `n + 1` e `WHERE n < 10` ferma tutto a 10. Senza quella condizione il ciclo non finirebbe: alcuni database si fermano con un errore dopo un certo numero di giri, altri continuano finché non li interrompi.', 'A recursive CTE has a starting point, `UNION ALL`, and a step that calls itself while the condition allows. In the example the starting point gives 1, each round adds `n + 1` and `WHERE n < 10` stops it all at 10. Without that condition the loop would never end: some databases stop with an error after a set number of rounds, others carry on until you interrupt them.'),
        ], [
            Q('c1', L('Con quale parola inizia una CTE?', 'Which word starts a CTE?'), ['WITH', 'CTE', 'AS', 'DEFINE'], 0, L('`WITH nome AS (…)`.', '`WITH name AS (…)`.')),
            Q('c2', L('A che cosa serve `WITH RECURSIVE`?', 'What is `WITH RECURSIVE` for?'), [L('Percorrere strutture ad albero', 'Walking tree structures'), L('Ripetere una query a orario', 'Repeating a query on a schedule'), L('Creare utenti', 'Creating users'), L('Cancellare tabelle', 'Dropping tables')], 0, L('Gerarchie, sequenze, grafi.', 'Hierarchies, sequences, graphs.')),
            Q('c3', L('Che cosa unisce il passo iniziale e quello ricorsivo?', 'What joins the initial and recursive steps?'), ['UNION ALL', 'JOIN', 'AND', 'MERGE'], 0, L('Le righe si accumulano.', 'Rows accumulate.')),
        ], task('sql', L('Crea la CTE `attivi AS (SELECT * FROM utenti WHERE attivo = 1)` e poi `SELECT COUNT(*) FROM attivi`.', 'Create the CTE `attivi AS (SELECT * FROM utenti WHERE attivo = 1)` then `SELECT COUNT(*) FROM attivi`.'), '', [re('WITH\s+attivi\s+AS\s*\(\s*SELECT\s+\*\s+FROM\s+utenti\s+WHERE\s+attivo\s*=\s*1\s*\)', L('La CTE è giusta', 'The CTE is right')), re('SELECT\s+COUNT\s*\(\s*\*\s*\)\s+FROM\s+attivi', L('La usi', 'You use it'))], '', 'WITH attivi AS (SELECT * FROM utenti WHERE attivo = 1)
SELECT COUNT(*) FROM attivi;'), [
            dOrder(pSort(), ['WITH RECURSIVE albero AS (', '    SELECT id, nome, padre_id FROM categorie WHERE padre_id IS NULL', '    UNION ALL', '    SELECT c.id, c.nome, c.padre_id FROM categorie c JOIN albero a ON c.padre_id = a.id', ')', 'SELECT * FROM albero;'], L('Radice, unione, passo ricorsivo, uso.', 'Root, union, recursive step, use.')),
            dFlash(pFlash(), [[L('Una CTE rende leggibile una query lunga', 'A CTE makes a long query readable'), true], [L('Una CTE crea una tabella permanente', 'A CTE creates a permanent table'), false], [L('Si possono avere più CTE separate da virgole', 'You can have several CTEs separated by commas'), true], [L('Una CTE ricorsiva può percorrere una gerarchia', 'A recursive CTE can walk through a hierarchy'), true]], L('Vive solo per quella query.', 'It lives only for that query.')),
        ]),
        lesson('windows', L('Funzioni finestra', 'Window functions'), [
            P('Le **funzioni finestra** calcolano un valore per ogni riga guardando un gruppo di righe vicine, senza raggrupparle. `OVER (PARTITION BY … ORDER BY …)` definisce la finestra.', '**Window functions** compute a value for each row by looking at a group of nearby rows, without collapsing them. `OVER (PARTITION BY … ORDER BY …)` defines the window.'),
            P('La differenza con `GROUP BY` è tutta qui: `PARTITION BY` divide le righe in gruppi allo stesso modo, ma **non le schiaccia**. Ogni riga resta al suo posto e riceve in più il valore calcolato sul suo gruppo: puoi mettere il voto di uno studente accanto alla media del suo corso, cosa impossibile con un semplice `GROUP BY`.', 'The difference from `GROUP BY` is all here: `PARTITION BY` splits the rows into groups the same way, but **does not squash them**. Every row stays where it is and also gets the value worked out on its group: you can put a student’s mark next to their course average, which a plain `GROUP BY` cannot do.'),
            Fig(L(fig_sqladv_window('5 righe → 2 righe', '5 righe → 5 righe, con una colonna in più'), fig_sqladv_window('5 rows → 2 rows', '5 rows → 5 rows, with one more column')),
                L('A sinistra GROUP BY corso riduce cinque voti a due righe: HTML 7,5 e SQL 8,0. A destra la funzione finestra tiene tutte e cinque le righe (Anna, Luca, Sara, Marco, Elena) e aggiunge a ognuna la media del suo corso.', 'On the left GROUP BY corso reduces five marks to two rows: HTML 7.5 and SQL 8.0. On the right the window function keeps all five rows (Anna, Luca, Sara, Marco, Elena) and adds each one’s course average.'),
                L('Stessi gruppi, stessa media: cambia solo quante righe restano.', 'Same groups, same average: only the number of rows left changes.')),
            C('SELECT nome, corso, voto,
       RANK() OVER (PARTITION BY corso ORDER BY voto DESC) AS posizione,
       AVG(voto) OVER (PARTITION BY corso) AS media_corso,
       SUM(importo) OVER (ORDER BY giorno) AS totale_progressivo
FROM esami;', 'sql', fucina_data_table(['nome', 'corso', 'voto', 'posizione', 'media_corso'], [['Anna', 'HTML', 8, 1, 7.5], ['Luca', 'HTML', 7, 2, 7.5], ['Sara', 'SQL', 9, 1, 8.0], ['Elena', 'SQL', 8, 2, 8.0], ['Marco', 'SQL', 7, 3, 8.0]], 'SELECT nome, corso, voto, RANK() OVER (…) AS posizione, AVG(voto) OVER (…) AS media_corso …')),
            P('`ROW_NUMBER` numera le righe, `RANK` gestisce i pari merito, `LAG` e `LEAD` leggono la riga precedente o successiva.', '`ROW_NUMBER` numbers rows, `RANK` handles ties, `LAG` and `LEAD` read the previous or next row.'),
        ], [
            Q('w1', L('Che cosa fa `PARTITION BY`?', 'What does `PARTITION BY` do?'), [L('Divide le righe in gruppi per la finestra', 'Splits rows into groups for the window'), L('Cancella righe', 'Deletes rows'), L('Crea una tabella', 'Creates a table'), L('Ordina sul disco', 'Sorts on disk')], 0, L('Le righe restano tutte.', 'All rows stay.')),
            Q('w2', L('Quale funzione legge la riga precedente?', 'Which function reads the previous row?'), ['LAG', 'LEAD', 'PREV', 'BEFORE'], 0, L('`LEAD` legge la successiva.', '`LEAD` reads the next.')),
            Q('w3', L('Che differenza c’è con `GROUP BY`?', 'How is it different from `GROUP BY`?'), [L('Le righe non vengono riunite', 'Rows are not collapsed'), L('Nessuna', 'None'), L('È più lento sempre', 'It is always slower'), L('Non usa aggregazioni', 'It uses no aggregates')], 0, L('Ogni riga ha il suo valore.', 'Each row gets its own value.')),
        ], task('sql', L('Numera i prodotti per prezzo decrescente: `ROW_NUMBER() OVER (ORDER BY prezzo DESC) AS pos`.', 'Number products by descending price: `ROW_NUMBER() OVER (ORDER BY prezzo DESC) AS pos`.'), 'SELECT nome, prezzo
FROM prodotti;', [re('ROW_NUMBER\s*\(\s*\)\s+OVER\s*\(\s*ORDER\s+BY\s+prezzo\s+DESC\s*\)\s+AS\s+pos', L('La finestra è giusta', 'The window is right'))], '', 'SELECT nome, prezzo, ROW_NUMBER() OVER (ORDER BY prezzo DESC) AS pos
FROM prodotti;'), [
            dMatch(pMatch(), [['ROW_NUMBER', L('Numero progressivo', 'Running number')], ['RANK', L('Posizione con pari merito', 'Position with ties')], ['LAG', L('Riga precedente', 'Previous row')], ['LEAD', L('Riga successiva', 'Next row')], ['OVER', L('Definisce la finestra', 'Defines the window')]], L('Le funzioni finestra.', 'Window functions.')),
            dBlanks(pFill(), "SELECT giorno, incasso,\n       SUM(incasso) ___ (___ BY giorno) AS cumulato\nFROM vendite;", ['OVER', 'ORDER'], ['WITH', 'GROUP', 'PARTITION'], L('Somma progressiva nel tempo.', 'Running total over time.')),
        ]),
        lesson('transactions', L('Transazioni', 'Transactions'), [
            P('Una **transazione** raccoglie più comandi in un blocco “tutto o niente”: con `COMMIT` diventano definitivi, con `ROLLBACK` si annullano tutti. Garantisce le proprietà **ACID**.', 'A **transaction** groups several commands into an “all or nothing” block: `COMMIT` makes them permanent, `ROLLBACK` undoes them all. It guarantees the **ACID** properties.'),
            Fig(L(fig_sqladv_tx('Prima: conto 1 = 500, conto 2 = 200', 'conto 1: −100', 'conto 2: +100', 'errore!', 'Tutto riuscito: conto 1 = 400, conto 2 = 300', 'Niente è cambiato: conto 1 = 500, conto 2 = 200'), fig_sqladv_tx('Before: account 1 = 500, account 2 = 200', 'account 1: −100', 'account 2: +100', 'error!', 'All went well: account 1 = 400, account 2 = 300', 'Nothing changed: account 1 = 500, account 2 = 200')),
                L('Due sequenze di passi partendo da conto 1 = 500 e conto 2 = 200. In alto: START, conto 1 meno 100, conto 2 più 100, COMMIT, ed entrambe le modifiche restano. In basso: START, conto 1 meno 100, un errore, ROLLBACK, e una freccia torna all’inizio: nessuna modifica resta.', 'Two sequences of steps starting from account 1 = 500 and account 2 = 200. On top: START, account 1 minus 100, account 2 plus 100, COMMIT, and both changes stay. Below: START, account 1 minus 100, an error, ROLLBACK, and an arrow goes back to the start: no change stays.'),
                L('Tutto o niente: il conto 1 non resta mai con 100 euro in meno da solo.', 'All or nothing: account 1 is never left alone with 100 euros less.')),
            C('START TRANSACTION;

UPDATE conti SET saldo = saldo - 100 WHERE id = 1;
UPDATE conti SET saldo = saldo + 100 WHERE id = 2;

-- se tutto è andato bene
COMMIT;
-- altrimenti
-- ROLLBACK;', 'sql'),
            P('Senza transazione, un errore tra i due `UPDATE` lascerebbe sparire 100 euro. I livelli di **isolamento** decidono che cosa vedono le transazioni che girano insieme. `START TRANSACTION` è la forma di MySQL e PostgreSQL (che accetta anche `BEGIN`); SQL Server scrive `BEGIN TRANSACTION`.', 'Without a transaction, an error between the two `UPDATE`s would make 100 euros vanish. **Isolation** levels decide what concurrent transactions can see. `START TRANSACTION` is the MySQL and PostgreSQL form (PostgreSQL also accepts `BEGIN`); SQL Server writes `BEGIN TRANSACTION`.'),
            P('**ACID** sono le quattro promesse di una transazione: **A**tomicità (tutto o niente), **C**oerenza (alla fine i vincoli sono rispettati), **I**solamento (le transazioni contemporanee non si pestano i piedi), **D**urabilità (dopo il `COMMIT` i dati sopravvivono anche a un crash o a un blackout).', '**ACID** are a transaction’s four promises: **A**tomicity (all or nothing), **C**onsistency (at the end the constraints hold), **I**solation (concurrent transactions do not tread on each other’s toes), **D**urability (after `COMMIT` the data survive even a crash or a power cut).'),
        ], [
            Q('t1', L('Che cosa fa `ROLLBACK`?', 'What does `ROLLBACK` do?'), [L('Annulla tutta la transazione', 'Undoes the whole transaction'), L('Salva', 'Saves'), L('Riavvia il server', 'Restarts the server'), L('Cancella la tabella', 'Drops the table')], 0, L('Come se non fosse mai successo.', 'As if it never happened.')),
            Q('t2', L('Che cosa significa la A di ACID?', 'What does the A in ACID stand for?'), [L('Atomicità: tutto o niente', 'Atomicity: all or nothing'), L('Accesso', 'Access'), L('Automatico', 'Automatic'), L('Asincrono', 'Asynchronous')], 0, L('Poi Coerenza, Isolamento, Durabilità.', 'Then Consistency, Isolation, Durability.')),
            Q('t3', L('Perché un bonifico va in una transazione?', 'Why does a transfer belong in a transaction?'), [L('Per non perdere soldi a metà', 'So money is not lost halfway'), L('Per andare più veloce', 'To go faster'), L('Per obbligo di legge', 'Because the law says so'), L('Non serve', 'It is not needed')], 0, L('Due scritture, un solo esito.', 'Two writes, one outcome.')),
        ], task('sql', L('Racchiudi i due `UPDATE` tra `START TRANSACTION;` e `COMMIT;`.', 'Wrap the two `UPDATE`s between `START TRANSACTION;` and `COMMIT;`.'), 'UPDATE magazzino SET pezzi = pezzi - 1 WHERE id = 5;
UPDATE ordini SET stato = \'spedito\' WHERE id = 9;', [re('^\s*START\s+TRANSACTION\s*;', L('Apri la transazione', 'You open the transaction')), re('COMMIT\s*;\s*$', L('La confermi alla fine', 'You commit at the end'))], '', 'START TRANSACTION;
UPDATE magazzino SET pezzi = pezzi - 1 WHERE id = 5;
UPDATE ordini SET stato = \'spedito\' WHERE id = 9;
COMMIT;'), [
            dOrder(pSort(), ['START TRANSACTION;', 'INSERT INTO ordini (utente_id, totale) VALUES (7, 40);', 'UPDATE utenti SET punti = punti + 40 WHERE id = 7;', 'COMMIT;'], L('Apri, scrivi, scrivi, conferma.', 'Open, write, write, commit.')),
            dMatch(pMatch(), [[L('Atomicità', 'Atomicity'), L('Tutto o niente', 'All or nothing')], [L('Coerenza', 'Consistency'), L('Regole sempre rispettate', 'Rules always kept')], [L('Isolamento', 'Isolation'), L('Transazioni separate', 'Transactions kept apart')], [L('Durabilità', 'Durability'), L('Ciò che è confermato resta', 'What is committed stays')]], L('Le proprietà ACID.', 'The ACID properties.')),
        ]),
        lesson('views', L('Viste e procedure', 'Views and procedures'), [
            P('Una **vista** è una query salvata con un nome: si interroga come una tabella. Serve a nascondere la complessità o a mostrare solo alcune colonne. Una **procedura** salva un blocco di comandi da richiamare con `CALL` (in SQL Server con `EXEC`).', 'A **view** is a saved query with a name: you query it like a table. It hides complexity or shows only some columns. A **procedure** saves a block of commands to run with `CALL` (`EXEC` in SQL Server).'),
            C('CREATE VIEW classifica_mensile AS
SELECT u.nome, SUM(p.punti) AS punti
FROM punteggi p JOIN utenti u ON u.id = p.utente_id
WHERE p.giorno >= CURRENT_DATE - INTERVAL 30 DAY  -- MySQL; in PostgreSQL: INTERVAL \'30 days\'
GROUP BY u.id, u.nome;

SELECT * FROM classifica_mensile ORDER BY punti DESC LIMIT 10;', 'sql'),
            P('La vista non copia i dati: ogni volta rilegge le tabelle. Per dati pesanti esistono le viste **materializzate** (per esempio in PostgreSQL e Oracle; in SQL Server le viste indicizzate) che salvano il risultato.', 'A view copies no data: it re-reads the tables every time. For heavy data there are **materialised** views (for example in PostgreSQL and Oracle; indexed views in SQL Server) that store the result.'),
        ], [
            Q('v1', L('Una vista contiene una copia dei dati?', 'Does a view hold a copy of the data?'), [L('No, rilegge le tabelle', 'No, it re-reads the tables'), L('Sì, sempre', 'Yes, always'), L('Solo il primo giorno', 'Only on the first day'), L('Solo i numeri', 'Only numbers')], 0, L('Quelle materializzate sì.', 'Materialized ones do.')),
            Q('v2', L('Come si interroga una vista?', 'How do you query a view?'), [L('Come una tabella', 'Like a table'), L('Con CALL', 'With CALL'), L('Con EXEC VIEW', 'With EXEC VIEW'), L('Non si può', 'You cannot')], 0, L('`SELECT * FROM vista`.', '`SELECT * FROM view`.')),
            Q('v3', L('Come si esegue una procedura?', 'How do you run a procedure?'), ['CALL', 'RUN', 'DO', 'START'], 0, L('`CALL nome(parametri)`; SQL Server usa `EXEC`.', '`CALL name(params)`; SQL Server uses `EXEC`.')),
        ], task('sql', L('Crea la vista `corsi_pubblicati` con `SELECT * FROM corsi WHERE stato = \'published\'`.', 'Create the view `corsi_pubblicati` with `SELECT * FROM corsi WHERE stato = \'published\'`.'), '', [re('CREATE\s+(OR\s+REPLACE\s+)?VIEW\s+corsi_pubblicati\s+AS', L('La vista esiste', 'The view exists')), re('SELECT\s+\*\s+FROM\s+corsi\s+WHERE\s+stato\s*=\s*\'published\'', L('La query è giusta', 'The query is right'))], '', 'CREATE VIEW corsi_pubblicati AS
SELECT * FROM corsi WHERE stato = \'published\';'), [
            dBlanks(pFill(), "CREATE ___ attivi ___\nSELECT id, nome FROM utenti WHERE attivo = 1;\nSELECT * ___ attivi;", ['VIEW', 'AS', 'FROM'], ['TABLE', 'IS', 'IN'], L('Crea, definisci, usa.', 'Create, define, use.')),
            dFlash(pFlash(), [[L('Una vista può nascondere colonne sensibili', 'A view can hide sensitive columns'), true], [L('Le viste rallentano sempre il database', 'Views always slow the database'), false], [L('In MySQL e PostgreSQL una procedura si richiama con `CALL`', 'In MySQL and PostgreSQL a procedure is invoked with `CALL`'), true], [L('Una vista è una query salvata con un nome', 'A view is a query saved under a name'), true]], L('Una vista è una query con un nome.', 'A view is a query with a name.')),
        ]),
        lesson('explain', L('EXPLAIN e prestazioni', 'EXPLAIN and performance'), [
            P('`EXPLAIN` mostra come il database eseguirà una query: quali indici usa e quante righe legge. Una “scansione completa” su una tabella grande è il segnale che manca un indice.', '`EXPLAIN` shows how the database will run a query: which indexes it uses and how many rows it reads. A “full scan” on a big table signals a missing index.'),
            C('EXPLAIN SELECT * FROM ordini WHERE utente_id = 42;

-- se non usa indici:
CREATE INDEX ix_ordini_utente ON ordini (utente_id);

-- indice composto: l’ordine delle colonne conta
CREATE INDEX ix_ordini_utente_data ON ordini (utente_id, creato_il);', 'sql'),
            P('La maggior parte degli indici è un **B-tree**: un albero ordinato e bilanciato. Per trovare un valore il database parte dalla radice e a ogni nodo sceglie il ramo giusto, come quando cerchi una parola nel dizionario: con un milione di righe bastano tre o quattro passi, invece di leggerle tutte.', 'Most indexes are a **B-tree**: a sorted, balanced tree. To find a value the database starts at the root and at each node picks the right branch, as you do when you look a word up in a dictionary: with a million rows three or four steps are enough, instead of reading them all.'),
            Fig(L(fig_sqladv_btree('radice', 'Foglie: ogni chiave punta alla sua riga nella tabella.', 'Cercare 42: tre nodi letti invece di tutta la tabella.'), fig_sqladv_btree('root', 'Leaves: each key points to its row in the table.', 'Finding 42: three nodes read instead of the whole table.')),
                L('Un albero a tre livelli. La radice contiene 50; sotto due nodi, 20 e 35 a sinistra, 70 e 85 a destra; in fondo sei foglie con le chiavi. Il percorso in arancione va dalla radice al nodo 20 · 35 e alla foglia 35 · 42.', 'A three-level tree. The root holds 50; below it two nodes, 20 and 35 on the left, 70 and 85 on the right; at the bottom six leaves with the keys. The orange path goes from the root to the 20 · 35 node and to the 35 · 42 leaf.'),
                L('42 è minore di 50 (a sinistra), poi maggiore di 35 (ultimo ramo): eccolo.', '42 is less than 50 (go left), then greater than 35 (last branch): there it is.')),
            P('Un indice composto serve le query che filtrano sulla prima colonna (da sola o con la seconda), non quelle che filtrano solo sulla seconda (alcuni motori, come MySQL 8 e PostgreSQL 18, sanno fare uno «skip scan», ma resta meno efficiente). Evita funzioni sulle colonne indicizzate in `WHERE`: `YEAR(data) = 2026` non usa l’indice, `data >= \'2026-01-01\'` sì.', 'A composite index serves queries filtering on the first column (alone or with the second), not those filtering only on the second (some engines, such as MySQL 8 and PostgreSQL 18, can do a “skip scan”, but it is still less efficient). Avoid functions on indexed columns in `WHERE`: `YEAR(data) = 2026` skips the index, `data >= \'2026-01-01\'` uses it.'),
        ], [
            Q('e1', L('Che cosa mostra `EXPLAIN`?', 'What does `EXPLAIN` show?'), [L('Il piano di esecuzione', 'The execution plan'), L('I dati', 'The data'), L('La struttura dei file', 'The file structure'), L('Gli utenti connessi', 'Connected users')], 0, L('Indici usati e righe lette.', 'Indexes used and rows read.')),
            Q('e2', L('Un indice su `(a, b)` aiuta una query che filtra solo su `b`?', 'Does an index on `(a, b)` help a query filtering only on `b`?'), [L('Di solito no', 'Usually not'), L('Sì, sempre', 'Yes, always'), L('Solo con LIMIT', 'Only with LIMIT'), L('Solo con ORDER BY', 'Only with ORDER BY')], 0, L('Conta la colonna più a sinistra; lo «skip scan» di alcuni motori è un ripiego.', 'The leftmost column matters; some engines’ “skip scan” is only a fallback.')),
            Q('e3', L('Perché `WHERE YEAR(data) = 2026` può essere lento?', 'Why can `WHERE YEAR(data) = 2026` be slow?'), [L('La funzione impedisce di usare l’indice', 'The function prevents index use'), L('Le date sono lente', 'Dates are slow'), L('2026 è un numero grande', '2026 is a big number'), L('Non è lento', 'It is not slow')], 0, L('Meglio un intervallo di date.', 'Better a date range.')),
        ], task('sql', L('Analizza `SELECT * FROM lezioni WHERE corso = \'sql-base\'` con `EXPLAIN`.', 'Analyse `SELECT * FROM lezioni WHERE corso = \'sql-base\'` with `EXPLAIN`.'), 'SELECT * FROM lezioni WHERE corso = \'sql-base\';', [re('^\s*EXPLAIN\s+SELECT\s+\*\s+FROM\s+lezioni', L('Chiedi il piano', 'You ask for the plan'))], '', 'EXPLAIN SELECT * FROM lezioni WHERE corso = \'sql-base\';'), [
            dBug(pBug(), ['SELECT id, totale', 'FROM ordini', 'WHERE YEAR(creato_il) = 2026;'], 2, 'WHERE creato_il >= \'2026-01-01\' AND creato_il < \'2027-01-01\';', L('Una funzione sulla colonna nasconde l’indice: meglio un intervallo.', 'A function on the column hides the index: better a range.')),
            dFlash(pFlash(), [[L('Una scansione completa su milioni di righe è lenta', 'A full scan on millions of rows is slow'), true], [L('Più indici rendono sempre tutto più veloce', 'More indexes always make everything faster'), false], [L('Nell’indice composto conta l’ordine delle colonne', 'Column order matters in a composite index'), true], [L('`EXPLAIN` mostra come il database eseguirà la query', '`EXPLAIN` shows how the database will run the query'), true]], L('Gli indici hanno un costo in scrittura.', 'Indexes cost something on writes.')),
        ]),
        lesson('window-frames', L('Cornici e top-N per gruppo', 'Frames and top-N per group'), [
            P('Dentro `OVER` si può fissare una **cornice**: quali righe vicine entrano nel calcolo. `ROWS BETWEEN 6 PRECEDING AND CURRENT ROW` prende la riga attuale e le 6 prima: è una **media mobile** su 7 giorni. `FIRST_VALUE` e `NTILE(4)` (divide in quartili) completano gli strumenti.', 'Inside `OVER` you can set a **frame**: which nearby rows enter the calculation. `ROWS BETWEEN 6 PRECEDING AND CURRENT ROW` takes the current row and the 6 before it: a 7-day **moving average**. `FIRST_VALUE` and `NTILE(4)` (split into quartiles) round off the toolbox.'),
            Fig(fig_sqladv_frame(),
                L('Dieci barre, una per giorno. Un riquadro viola copre sette barre consecutive: a destra la riga attuale, bordata in arancione (CURRENT ROW), a sinistra le sei precedenti (6 PRECEDING). Il riquadro scorre di un giorno alla volta verso destra.', 'Ten bars, one per day. A purple frame covers seven consecutive bars: on the right the current row, outlined in orange (CURRENT ROW), on the left the six before it (6 PRECEDING). The frame slides one day at a time to the right.'),
                L('Per ogni riga la cornice si sposta con lei: la media usa sempre gli ultimi sette giorni.', 'For every row the frame moves with it: the average always uses the last seven days.')),
            C('SELECT giorno, incasso,
       AVG(incasso) OVER (ORDER BY giorno
                          ROWS BETWEEN 6 PRECEDING AND CURRENT ROW) AS media_7g
FROM vendite;

-- i 3 prodotti più venduti di ogni categoria
WITH classifica AS (
    SELECT categoria, nome, venduti,
           ROW_NUMBER() OVER (PARTITION BY categoria ORDER BY venduti DESC) AS pos
    FROM prodotti
)
SELECT * FROM classifica WHERE pos <= 3;', 'sql', L(fucina_data_table(['categoria', 'nome', 'venduti', 'pos'], [['libri', 'Dune', 410, 1], ['libri', 'Emma', 388, 2], ['libri', 'Ulisse', 251, 3], ['giochi', 'Scacchi', 530, 1], ['giochi', 'Go', 302, 2], ['giochi', 'Dama', 190, 3]], 'SELECT * FROM classifica WHERE pos <= 3;'), fucina_data_table(['categoria', 'nome', 'venduti', 'pos'], [['books', 'Dune', 410, 1], ['books', 'Emma', 388, 2], ['books', 'Ulysses', 251, 3], ['games', 'Chess', 530, 1], ['games', 'Go', 302, 2], ['games', 'Draughts', 190, 3]], 'SELECT * FROM classifica WHERE pos <= 3;'))),
            P('Le funzioni finestra non si possono usare direttamente in `WHERE`, perché vengono calcolate dopo: per filtrare su `pos` si passa da una CTE o da una sottoquery. Con un `ORDER BY` nella finestra e senza cornice esplicita, la cornice predefinita va dall’inizio fino alla riga attuale: per questo `SUM` diventa un totale progressivo.', 'Window functions cannot be used directly in `WHERE`, because they are computed later: to filter on `pos` you go through a CTE or a subquery. With an `ORDER BY` in the window and no explicit frame, the default frame runs from the start to the current row: that is why `SUM` becomes a running total.'),
        ], [
            Q('f1', L('Che cosa calcola `AVG(x) OVER (ORDER BY giorno ROWS BETWEEN 6 PRECEDING AND CURRENT ROW)`?', 'What does `AVG(x) OVER (ORDER BY giorno ROWS BETWEEN 6 PRECEDING AND CURRENT ROW)` compute?'), [L('La media di tutta la tabella', 'The average of the whole table'), L('Una media mobile su 7 righe', 'A moving average over 7 rows'), L('La media dei primi 6 giorni', 'The average of the first 6 days'), L('La media dei 6 giorni successivi', 'The average of the next 6 days')], 1, L('La riga attuale più le 6 precedenti.', 'The current row plus the 6 before it.')),
            Q('f2', L('Perché non si scrive `WHERE ROW_NUMBER() OVER (…) <= 3`?', 'Why can you not write `WHERE ROW_NUMBER() OVER (…) <= 3`?'), [L('Perché `ROW_NUMBER` funziona solo con `GROUP BY`', 'Because `ROW_NUMBER` only works with `GROUP BY`'), L('Perché `WHERE` accetta solo numeri fissi', 'Because `WHERE` only accepts fixed numbers'), L('Perché le finestre si calcolano dopo il `WHERE`', 'Because windows are computed after the `WHERE`'), L('Perché il limite massimo sarebbe 1', 'Because the maximum limit would be 1')], 2, L('Si filtra dopo, con una CTE o una sottoquery.', 'You filter afterwards, with a CTE or a subquery.')),
            Q('f3', L('Che cosa fa `NTILE(4) OVER (ORDER BY punti)`?', 'What does `NTILE(4) OVER (ORDER BY punti)` do?'), [L('Divide le righe in quattro gruppi di uguale grandezza', 'Splits the rows into four equal-sized groups'), L('Prende solo le prime quattro righe', 'Takes only the first four rows'), L('Moltiplica i punti per quattro', 'Multiplies the points by four'), L('Arrotonda i punti a quattro cifre', 'Rounds the points to four digits')], 0, L('Quartili: 1, 2, 3, 4.', 'Quartiles: 1, 2, 3, 4.')),
        ], task('sql', L('Aggiungi la colonna `media_3` con la media di `temperatura` sulla riga attuale e le 2 precedenti, ordinando per `giorno`.', 'Add the column `media_3` with the average of `temperatura` over the current row and the 2 before it, ordered by `giorno`.'), 'SELECT giorno, temperatura
FROM meteo;', [re('AVG\s*\(\s*temperatura\s*\)\s+OVER\s*\(\s*ORDER\s+BY\s+giorno\s+ROWS\s+BETWEEN\s+2\s+PRECEDING\s+AND\s+CURRENT\s+ROW\s*\)\s+(AS\s+)?media_3', L('La media mobile su 3 righe', 'The 3-row moving average'))], '', "SELECT giorno, temperatura,\n       AVG(temperatura) OVER (ORDER BY giorno ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) AS media_3\nFROM meteo;"), [
            dBlanks(pFill(), "SELECT giorno,\n       SUM(incasso) OVER (ORDER BY giorno\n           ___ BETWEEN 29 ___ AND ___ ROW) AS ultimi_30\nFROM vendite;", ['ROWS', 'PRECEDING', 'CURRENT'], ['RANGE OF', 'BEFORE', 'THIS'], L('Una somma mobile sugli ultimi 30 giorni.', 'A moving sum over the last 30 days.')),
            dOrder(pSort(), ['WITH classifica AS (', '    SELECT reparto, nome, stipendio,', '           RANK() OVER (PARTITION BY reparto ORDER BY stipendio DESC) AS pos', '    FROM dipendenti', ')', 'SELECT * FROM classifica WHERE pos = 1;'], L('Prima la finestra nella CTE, poi il filtro.', 'First the window in the CTE, then the filter.')),
        ]),
        lesson('isolation', L('Concorrenza e lock', 'Concurrency and locks'), [
            P('Quando più transazioni lavorano insieme possono nascere **anomalie**: leggere dati non ancora confermati (*dirty read*), rileggere una riga e trovarla cambiata, vedere comparire righe nuove (*phantom*). I **livelli di isolamento** scelgono quante anomalie evitare: `READ COMMITTED`, `REPEATABLE READ`, `SERIALIZABLE`.', 'When several transactions run together **anomalies** can appear: reading data not yet committed (*dirty read*), re-reading a row and finding it changed, seeing new rows appear (*phantom*). **Isolation levels** choose how many anomalies to prevent: `READ COMMITTED`, `REPEATABLE READ`, `SERIALIZABLE`.'),
            C('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
START TRANSACTION;

-- blocca la riga finché la transazione non finisce
SELECT pezzi FROM magazzino WHERE id = 5 FOR UPDATE;
UPDATE magazzino SET pezzi = pezzi - 1 WHERE id = 5;

COMMIT;', 'sql'),
            Fig(L(fig_sqladv_lock('lock sulla riga 5', 'in attesa', 'tempo'), fig_sqladv_lock('lock on row 5', 'waiting', 'time')),
                L('Due corsie nel tempo. T1 esegue START, SELECT … FOR UPDATE, UPDATE e COMMIT; sotto, una barra arancione mostra il lock sulla riga 5 dal SELECT al COMMIT. T2 parte dopo, prova a modificare la stessa riga e resta in attesa (barra tratteggiata) fino al COMMIT di T1, poi esegue il suo UPDATE.', 'Two lanes over time. T1 runs START, SELECT … FOR UPDATE, UPDATE and COMMIT; below it, an orange bar shows the lock on row 5 from the SELECT to the COMMIT. T2 starts later, tries to change the same row and waits (hatched bar) until T1’s COMMIT, then runs its UPDATE.'),
                L('Il lock mette in fila chi tocca la stessa riga: nessuno dei due vende l’ultimo pezzo due volte.', 'The lock queues whoever touches the same row: neither of them sells the last item twice.')),
            P('`SELECT … FOR UPDATE` mette un **lock** sulle righe lette: chi vuole modificarle aspetta. Se due transazioni si bloccano a vicenda nasce un **deadlock**: il database ne annulla una, che va ripetuta. I predefiniti cambiano: `READ COMMITTED` in PostgreSQL e SQL Server, `REPEATABLE READ` in MySQL con InnoDB.', '`SELECT … FOR UPDATE` puts a **lock** on the rows it reads: whoever wants to change them waits. If two transactions block each other a **deadlock** occurs: the database cancels one, which must be retried. Defaults differ: `READ COMMITTED` in PostgreSQL and SQL Server, `REPEATABLE READ` in MySQL with InnoDB.'),
        ], [
            Q('i1', L('Che cos’è una *dirty read*?', 'What is a *dirty read*?'), [L('Leggere una tabella senza indici', 'Reading a table without indexes'), L('Leggere righe cancellate dal disco', 'Reading rows deleted from disk'), L('Leggere un file corrotto', 'Reading a corrupted file'), L('Leggere dati di un’altra transazione non ancora confermati', 'Reading another transaction’s data not yet committed')], 3, L('Quei dati potrebbero sparire con un `ROLLBACK`.', 'That data could vanish with a `ROLLBACK`.')),
            Q('i2', L('Che cosa fa `SELECT … FOR UPDATE`?', 'What does `SELECT … FOR UPDATE` do?'), [L('Blocca le righe lette fino alla fine della transazione', 'Locks the rows read until the transaction ends'), L('Aggiorna subito tutte le righe trovate', 'Immediately updates every row found'), L('Rende la query più veloce saltando i lock', 'Makes the query faster by skipping locks'), L('Crea una copia delle righe da modificare', 'Creates a copy of the rows to change')], 0, L('Così nessuno le cambia mentre decidi.', 'So nobody changes them while you decide.')),
            Q('i3', L('Che cosa succede in un deadlock?', 'What happens in a deadlock?'), [L('Il server si spegne per sicurezza', 'The server shuts down for safety'), L('Il database annulla una delle transazioni', 'The database cancels one of the transactions'), L('Le due transazioni aspettano per sempre', 'Both transactions wait forever'), L('Vengono confermate tutte e due', 'Both get committed')], 1, L('L’applicazione deve ripeterla.', 'The application must retry it.')),
        ], task('sql', L('Apri una transazione, blocca con `FOR UPDATE` la riga di `conti` con `id = 1` leggendo `saldo`, poi conferma con `COMMIT`.', 'Open a transaction, lock with `FOR UPDATE` the row of `conti` with `id = 1` reading `saldo`, then commit with `COMMIT`.'), '', [re('(START\s+TRANSACTION|BEGIN)\s*;', L('Apri la transazione', 'You open the transaction')), re('SELECT\s+saldo\s+FROM\s+conti\s+WHERE\s+id\s*=\s*1\s+FOR\s+UPDATE', L('Blocchi la riga', 'You lock the row')), re('COMMIT\s*;', L('Confermi', 'You commit'))], '', "START TRANSACTION;\nSELECT saldo FROM conti WHERE id = 1 FOR UPDATE;\nCOMMIT;"), [
            dMatch(pMatch(), [['Dirty read', L('Dati non confermati', 'Uncommitted data')], ['Phantom', L('Righe nuove che compaiono', 'New rows appearing')], ['SERIALIZABLE', L('Isolamento massimo', 'Maximum isolation')], ['FOR UPDATE', L('Blocca le righe lette', 'Locks the rows read')], ['Deadlock', L('Blocco reciproco', 'Mutual block')]], L('Il vocabolario della concorrenza.', 'The vocabulary of concurrency.')),
            dFlash(pFlash(), [[L('`SERIALIZABLE` evita più anomalie di `READ COMMITTED`', '`SERIALIZABLE` prevents more anomalies than `READ COMMITTED`'), true], [L('Tutti i database hanno lo stesso livello predefinito', 'Every database has the same default level'), false], [L('Dopo un deadlock la transazione annullata va ripetuta', 'After a deadlock the cancelled transaction must be retried'), true], [L('Un lock impedisce sempre anche la lettura', 'A lock always prevents reading too'), false]], L('Isolamento e lock hanno un prezzo in attese.', 'Isolation and locks cost waiting time.')),
        ]),
        lesson('triggers', L('Trigger', 'Triggers'), [
            P('Un **trigger** è codice che il database esegue da solo **prima o dopo** un `INSERT`, `UPDATE` o `DELETE` su una tabella. Serve per tenere un registro delle modifiche, aggiornare un campo automatico o controllare regole complesse.', 'A **trigger** is code the database runs by itself **before or after** an `INSERT`, `UPDATE` or `DELETE` on a table. It is used to keep a log of changes, update an automatic field or enforce complex rules.'),
            C('-- MySQL / MariaDB
CREATE TRIGGER log_prezzi
AFTER UPDATE ON prodotti
FOR EACH ROW
INSERT INTO storico_prezzi (prodotto_id, vecchio, nuovo, quando)
VALUES (OLD.id, OLD.prezzo, NEW.prezzo, NOW());', 'sql'),
            P('Dentro il trigger `OLD` è la riga com’era e `NEW` com’è diventata (in un `INSERT` esiste solo `NEW`, in un `DELETE` solo `OLD`). La sintassi cambia molto: PostgreSQL chiama una funzione con `EXECUTE FUNCTION`, SQL Server usa le tabelle `inserted` e `deleted`. Usali con misura: la logica nascosta nei trigger è difficile da scoprire e da testare.', 'Inside the trigger `OLD` is the row as it was and `NEW` as it became (an `INSERT` has only `NEW`, a `DELETE` only `OLD`). The syntax varies a lot: PostgreSQL calls a function with `EXECUTE FUNCTION`, SQL Server uses the `inserted` and `deleted` tables. Use them sparingly: logic hidden in triggers is hard to discover and to test.'),
        ], [
            Q('r1', L('Quando parte un trigger?', 'When does a trigger fire?'), [L('Solo quando lo chiami con `CALL`', 'Only when you call it with `CALL`'), L('A un orario fisso ogni notte', 'At a fixed time every night'), L('Prima o dopo un `INSERT`, `UPDATE` o `DELETE`', 'Before or after an `INSERT`, `UPDATE` or `DELETE`'), L('Solo quando il server si riavvia', 'Only when the server restarts')], 2, L('Lo fa partire la modifica stessa.', 'The change itself fires it.')),
            Q('r2', L('In un trigger su `DELETE`, quale riga è disponibile?', 'In a trigger on `DELETE`, which row is available?'), ['NEW', 'OLD', L('Sia OLD sia NEW', 'Both OLD and NEW'), L('Nessuna delle due', 'Neither')], 1, L('La riga cancellata, com’era.', 'The deleted row, as it was.')),
            Q('r3', L('Qual è il rischio principale dei trigger?', 'What is the main risk of triggers?'), [L('Logica nascosta, difficile da trovare e da testare', 'Hidden logic, hard to find and test'), L('Funzionano solo su tabelle vuote', 'They only work on empty tables'), L('Cancellano gli indici della tabella', 'They delete the table’s indexes'), L('Non possono scrivere in altre tabelle', 'They cannot write to other tables')], 0, L('Chi legge il codice dell’app non li vede.', 'Whoever reads the app code does not see them.')),
        ], task('sql', L('Crea (sintassi MySQL) il trigger `conta_iscritti` che dopo ogni `INSERT` su `iscrizioni` aumenta di 1 `iscritti` nella tabella `corsi` dove `id = NEW.corso_id`.', 'Create (MySQL syntax) the trigger `conta_iscritti` that after every `INSERT` on `iscrizioni` adds 1 to `iscritti` in the table `corsi` where `id = NEW.corso_id`.'), '', [re('CREATE\s+TRIGGER\s+conta_iscritti', L('Il trigger esiste', 'The trigger exists')), re('AFTER\s+INSERT\s+ON\s+iscrizioni\s+FOR\s+EACH\s+ROW', L('Dopo ogni inserimento', 'After every insert')), re('UPDATE\s+corsi\s+SET\s+iscritti\s*=\s*iscritti\s*\+\s*1\s+WHERE\s+id\s*=\s*NEW\.corso_id', L('Aggiorna il contatore', 'It updates the counter'))], '', "CREATE TRIGGER conta_iscritti\nAFTER INSERT ON iscrizioni\nFOR EACH ROW\nUPDATE corsi SET iscritti = iscritti + 1 WHERE id = NEW.corso_id;"), [
            dBlanks(pFill(), "CREATE TRIGGER storico\n___ UPDATE ON ordini\nFOR ___ ROW\nINSERT INTO log (ordine_id, stato_prima)\nVALUES (___.id, OLD.stato);", ['AFTER', 'EACH', 'OLD'], ['WHEN', 'EVERY', 'PREV'], L('Momento, granularità, riga di prima.', 'Timing, granularity, previous row.')),
            dMatch(pMatch(), [['BEFORE', L('Prima della modifica', 'Before the change')], ['AFTER', L('Dopo la modifica', 'After the change')], ['OLD', L('La riga com’era', 'The row as it was')], ['NEW', L('La riga com’è diventata', 'The row as it became')], ['FOR EACH ROW', L('Una volta per riga', 'Once per row')]], L('Le parti di un trigger.', 'The parts of a trigger.')),
        ]),
    ],
];
