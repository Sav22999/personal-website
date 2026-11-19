<?php
/** MariaDB: the open fork of MySQL and what it adds: RETURNING, sequences, system-versioned tables, engines and Galera, vector search, its own types. */

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

/** The versions of one row on a timeline, and the moment an AS OF query looks at. */
if (!function_exists('fig_mariadb_history')) {
    function fig_mariadb_history(string $current): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="420" y="34" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13" font-weight="700" fill="#4B2BBF">FOR SYSTEM_TIME AS OF '2026-01-01'</text>
<rect x="60" y="100" width="288" height="36" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="348" y="100" width="180" height="36" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<rect x="528" y="100" width="76" height="36" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<path d="M604 118 H618" stroke="#0B7F5C" stroke-width="2"/><path d="M616 112 L626 118 L616 124 Z" fill="#0B7F5C"/>
<g font-weight="700"><text x="204" y="123" text-anchor="middle">19.90</text><text x="362" y="123">22.50</text><text x="566" y="123" text-anchor="middle">24.90</text></g>
<text x="566" y="90" text-anchor="middle" font-size="13" font-weight="700" fill="#0B7F5C">{$current}</text>
<line x1="420" y1="44" x2="420" y2="170" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4"/>
<circle cx="420" cy="118" r="6" fill="#7048E8"/>
<path d="M40 170 H610" stroke="#8A87B0" stroke-width="2"/>
<g stroke="#8A87B0" stroke-width="2"><line x1="60" y1="164" x2="60" y2="176"/><line x1="348" y1="164" x2="348" y2="176"/><line x1="420" y1="164" x2="420" y2="176"/><line x1="528" y1="164" x2="528" y2="176"/></g>
<g font-size="13" text-anchor="middle" fill="#5A5880"><text x="60" y="196">2025-03</text><text x="348" y="196">2025-11</text><text x="420" y="196" font-weight="700" fill="#4B2BBF">2026-01</text><text x="528" y="196">2026-04</text></g>
</svg>
SVG;
    }
}

/** The same table stored by rows (InnoDB) and by columns (ColumnStore): a sum over one column reads very different blocks. */
if (!function_exists('fig_mariadb_columns')) {
    function fig_mariadb_columns(string $byRow, string $byCol, string $rows, string $rowNote, string $colNote): string
    {
        $c = ['g' => ['#2F8CF0', '#DCEBFF'], 'p' => ['#0B7F5C', '#D5F6EA'], 'v' => ['#E8590C', '#FFE8D6']];
        $blk = function (int $x, string $k, bool $hot) use ($c) {
            return '<rect x="' . $x . '" y="100" width="18" height="40" rx="4" fill="' . $c[$k][1] . '" stroke="' . $c[$k][0] . '" stroke-width="' . ($hot ? 3 : 2) . '"/><text x="' . ($x + 9) . '" y="125" text-anchor="middle" font-size="13" font-weight="700">' . $k . '</text>';
        };
        $g = '';
        for ($r = 0; $r < 4; $r++) foreach (['g', 'p', 'v'] as $j => $k) $g .= $blk(32 + $r * 68 + $j * 20, $k, $k === 'v');
        foreach (['g', 'p', 'v'] as $j => $k) for ($r = 0; $r < 4; $r++) $g .= $blk(344 + $j * 92 + $r * 20, $k, $k === 'v');
        // what SUM(visite) reads
        for ($r = 0; $r < 4; $r++) $g .= '<path d="M' . (81 + $r * 68) . ' 152 v10" stroke="#E8590C" stroke-width="2"/>';
        $g .= '<path d="M528 152 v8 H604 v-8" fill="none" stroke="#E8590C" stroke-width="2"/>';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="20" y="16" width="290" height="208" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<rect x="330" y="16" width="290" height="208" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<g font-weight="700"><text x="34" y="44">{$byRow}</text><text x="344" y="44">{$byCol}</text></g>
<g font-size="13" fill="#5A5880"><text x="34" y="80">{$rows}</text><text x="344" y="80">giorno · pagina · visite</text></g>
{$g}
<text x="34" y="190" font-size="13" fill="#B84508" font-weight="700">{$rowNote}</text>
<text x="344" y="190" font-size="13" fill="#B84508" font-weight="700">{$colNote}</text>
<text x="34" y="212" font-size="12" fill="#5A5880">g = giorno · p = pagina · v = visite</text>
</svg>
SVG;
    }
}

/** Embeddings drawn in two dimensions: the query lands near the animals and the nearest three come back first. */
if (!function_exists('fig_mariadb_vectors')) {
    function fig_mariadb_vectors(array $w, string $query, string $result): string
    {
        [$cats, $dogs, $hamsters, $cars, $bikes, $pizza] = $w;
        $pt = fn(int $x, int $y, string $t, string $s, string $f, int $dx = 12) => '<circle cx="' . $x . '" cy="' . $y . '" r="7" fill="' . $f . '" stroke="' . $s . '" stroke-width="2"/><text x="' . ($x + $dx) . '" y="' . ($y + 5) . '"' . ($dx < 0 ? ' text-anchor="end"' : '') . '>' . $t . '</text>';
        $g = $pt(120, 96, $cats, '#2F8CF0', '#DCEBFF', -12) . $pt(188, 66, $dogs, '#2F8CF0', '#DCEBFF') . $pt(150, 150, $hamsters, '#2F8CF0', '#DCEBFF', -12)
            . $pt(320, 220, $cars, '#8A87B0', '#F1F0F8') . $pt(372, 196, $bikes, '#8A87B0', '#F1F0F8') . $pt(310, 76, $pizza, '#8A87B0', '#F1F0F8');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="20" y="16" width="410" height="250" rx="12" fill="#FFFFFF" stroke="#C9C7DE" stroke-width="2"/>
<circle cx="160" cy="104" r="58" fill="#FFF3EA" stroke="#E8590C" stroke-width="2" stroke-dasharray="6 4"/>
<g stroke="#E8590C" stroke-width="2" stroke-dasharray="3 3"><line x1="160" y1="104" x2="120" y2="96"/><line x1="160" y1="104" x2="188" y2="66"/><line x1="160" y1="104" x2="150" y2="150"/></g>
{$g}
<path d="M160 94 L170 104 L160 114 L150 104 Z" fill="#E8590C"/>
<text x="176" y="122" font-size="13" font-weight="700" fill="#B84508">{$query}</text>
<rect x="450" y="40" width="170" height="150" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="466" y="68" font-weight="700" fill="#1B66BC">{$result}</text>
<text x="466" y="100">1. {$cats}</text><text x="466" y="128">2. {$hamsters}</text><text x="466" y="156">3. {$dogs}</text>
<text x="450" y="222" font-family="ui-monospace, monospace" font-size="12" fill="#5A5880">ORDER BY VEC_DISTANCE…</text><text x="450" y="240" font-family="ui-monospace, monospace" font-size="12" fill="#5A5880">LIMIT 3</text>
</svg>
SVG;
    }
}

return [
    'id' => 'mariadb', 'family' => 'mariadb', 'after' => 'mysql',
    'title' => L('MariaDB', 'MariaDB'), 'short' => L('MariaDB', 'MariaDB'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il fork libero di MySQL: compatibilità, `RETURNING`, sequenze, tabelle con la storia dei dati, motori e cluster Galera, ricerca vettoriale, tipi propri e versioni.', 'The open fork of MySQL: compatibility, `RETURNING`, sequences, tables that keep data history, engines and Galera clusters, vector search, its own types and versions.'),
    'lessons' => [
        lesson('fork', L('Un fork di MySQL', 'A fork of MySQL'), [
            P('MariaDB nasce nel 2009 dagli sviluppatori originali di MySQL, mentre Oracle acquisiva Sun, e con essa MySQL. Resta **compatibile** in gran parte: stesso protocollo, stesso client, stessa sintassi di base, e molte distribuzioni Linux la installano al posto di MySQL.', 'MariaDB was born in 2009 from MySQL’s original developers, while Oracle was acquiring Sun, and MySQL with it. It stays largely **compatible**: same protocol, same client, same basic syntax, and many Linux distributions install it in place of MySQL.'),
            C('mariadb -u root -p

SELECT VERSION();          -- per esempio 12.3.3-MariaDB
SHOW ENGINES;
SELECT @@version_comment;', 'sql'),
            P('Con il tempo le due strade si sono separate: MariaDB ha funzioni sue (sequenze, tabelle versionate, motori in più), MySQL altre. I connettori di PHP, Python e Java funzionano con entrambi.', 'Over time the two paths drifted apart: MariaDB has features of its own (sequences, versioned tables, extra engines), MySQL others. PHP, Python and Java connectors work with both.'),
            Tip('Su un hosting condiviso “MySQL” spesso è in realtà MariaDB: `SELECT VERSION();` lo rivela.', 'On shared hosting “MySQL” is often MariaDB in disguise: `SELECT VERSION();` gives it away.'),
        ], [
            Q('f1', L('Chi ha creato MariaDB?', 'Who created MariaDB?'), [L('Gli sviluppatori originali di MySQL', 'MySQL’s original developers'), L('Oracle', 'Oracle'), L('Microsoft', 'Microsoft'), L('Il team di PostgreSQL', 'The PostgreSQL team')], 0, L('Quando Oracle stava acquisendo Sun, proprietaria di MySQL.', 'When Oracle was acquiring Sun, the owner of MySQL.')),
            Q('f2', L('Come scopri se il server è MariaDB?', 'How do you find out whether the server is MariaDB?'), ['SELECT VERSION();', 'SHOW USERS;', 'DESCRIBE server;', 'SELECT ENGINE();'], 0, L('La versione contiene “MariaDB”.', 'The version contains “MariaDB”.')),
            Q('f3', L('Il connettore MySQL di PHP funziona con MariaDB?', 'Does PHP’s MySQL connector work with MariaDB?'), [L('Sì, il protocollo è lo stesso', 'Yes, the protocol is the same'), L('No, serve un driver diverso', 'No, it needs another driver'), L('Solo in sola lettura', 'Only read only'), L('Solo su Windows', 'Only on Windows')], 0, L('PDO e mysqli si collegano senza cambiare nulla.', 'PDO and mysqli connect with no changes.')),
        ], task('sql', L('Mostra la versione del server con `SELECT VERSION();` e poi l’elenco dei motori con `SHOW ENGINES;`.', 'Show the server version with `SELECT VERSION();` and then the list of engines with `SHOW ENGINES;`.'), '', [re('SELECT\s+VERSION\s*\(\s*\)\s*;', L('Chiedi la versione', 'You ask for the version')), re('SHOW\s+ENGINES\s*;', L('Elenchi i motori', 'You list the engines'))], '', "SELECT VERSION();\nSHOW ENGINES;"), [
            dMatch(pMatch(), [['mariadb', L('Il client a riga di comando', 'The command-line client')], ['VERSION()', L('La versione del server', 'The server version')], ['SHOW ENGINES', L('I motori disponibili', 'The available engines')], ['2009', L('L’anno del fork', 'The year of the fork')]], L('Conoscere il proprio server.', 'Knowing your own server.')),
            dFlash(pFlash(), [[L('MariaDB usa lo stesso protocollo di MySQL', 'MariaDB uses the same protocol as MySQL'), true], [L('MariaDB è un prodotto di Oracle', 'MariaDB is an Oracle product'), false], [L('Molte distribuzioni Linux installano MariaDB', 'Many Linux distributions install MariaDB'), true], [L('MariaDB e MySQL hanno oggi funzioni identiche', 'MariaDB and MySQL have identical features today'), false]], L('Parenti stretti, non gemelli.', 'Close relatives, not twins.')),
        ]),
        lesson('returning', L('INSERT … RETURNING', 'INSERT … RETURNING'), [
            P('Dopo un inserimento spesso serve l’id appena creato. MariaDB permette di farselo restituire subito con **`RETURNING`**, anche su `DELETE`: niente seconda query.', 'After an insert you often need the id just created. MariaDB lets you get it back at once with **`RETURNING`**, on `DELETE` too: no second query.'),
            C('INSERT INTO utenti (nome, email)
VALUES (\'Ada\', \'ada@example.com\')
RETURNING id, creato_il;

DELETE FROM sessioni WHERE scadenza < NOW()
RETURNING utente_id;', 'sql', fucina_data_table(['id', 'creato_il'], [[58, '2026-11-18 10:42:07']], 'INSERT INTO utenti … RETURNING id, creato_il;')),
            P('In MySQL lo stesso si ottiene con `LAST_INSERT_ID()` subito dopo l’`INSERT`, ma vale solo per l’`AUTO_INCREMENT` e, con un inserimento di più righe, restituisce l’id della prima.', 'In MySQL you get the same with `LAST_INSERT_ID()` right after the `INSERT`, but it only covers the `AUTO_INCREMENT` and, with a multi-row insert, it returns the id of the first row.'),
            P('Con `RETURNING` invece un `INSERT` di più righe restituisce una riga per ogni riga inserita, con tutti gli id. E puoi chiedere qualsiasi colonna, compresi i valori che ha riempito il database da solo, come un `DEFAULT` o una data di creazione: un solo viaggio al database invece di due.', 'With `RETURNING`, instead, a multi-row `INSERT` returns one row for every row inserted, with all the ids. And you can ask for any column, including the values the database filled in by itself, such as a `DEFAULT` or a creation date: a single trip to the database instead of two.'),
        ], [
            Q('r1', L('Che cosa aggiunge `RETURNING` a un `INSERT`?', 'What does `RETURNING` add to an `INSERT`?'), [L('Restituisce le colonne delle righe inserite', 'Returns the columns of the inserted rows'), L('Annulla l’inserimento', 'Undoes the insert'), L('Inserisce due volte', 'Inserts twice'), L('Ordina la tabella', 'Sorts the table')], 0, L('Niente query in più per leggere l’id.', 'No extra query to read the id.')),
            Q('r2', L('Si può usare `RETURNING` con `DELETE`?', 'Can you use `RETURNING` with `DELETE`?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo con `LIMIT 1`', 'Only with `LIMIT 1`'), L('Solo da root', 'Only as root')], 0, L('Vedi cosa hai cancellato.', 'You see what you deleted.')),
            Q('r3', L('Qual è l’alternativa classica di MySQL per l’ultimo id?', 'What is MySQL’s classic alternative for the last id?'), ['LAST_INSERT_ID()', 'NEXT_ID()', 'CURRENT_ROW()', 'MAX_ID()'], 0, L('Vale per l’`AUTO_INCREMENT`.', 'It covers the `AUTO_INCREMENT`.')),
        ], task('sql', L('Inserisci in `corsi (titolo)` il valore `\'MariaDB\'` e fatti restituire `id`.', 'Insert into `corsi (titolo)` the value `\'MariaDB\'` and get back `id`.'), '', [re('INSERT\s+INTO\s+corsi\s*\(\s*titolo\s*\)\s*VALUES\s*\(\s*\'MariaDB\'\s*\)', L('Inserisci il corso', 'You insert the course')), re('RETURNING\s+id', L('Ti fai restituire l’id', 'You get the id back'))], '', "INSERT INTO corsi (titolo) VALUES ('MariaDB') RETURNING id;"), [
            dBlanks(pFill(), "INSERT INTO ordini (cliente_id, totale)\nVALUES (5, 49.90)\n___ id, creato_il;", ['RETURNING'], ['OUTPUT', 'RETURN', 'LAST_INSERT_ID'], L('Le colonne tornano subito indietro.', 'The columns come straight back.')),
            dBug(pBug(), ['DELETE FROM carrelli', 'WHERE aggiornato_il < NOW() - INTERVAL 30 DAY', 'RETURN id;'], 2, 'RETURNING id;', L('La parola chiave è `RETURNING`, in fondo all’istruzione.', 'The keyword is `RETURNING`, at the end of the statement.')),
        ]),
        lesson('sequences', L('Sequenze', 'Sequences'), [
            P('Una **sequenza** è un contatore indipendente dalle tabelle: ogni `NEXTVAL` dà il numero successivo, anche a più tabelle. Utile per numeri di fattura o codici condivisi.', 'A **sequence** is a counter independent of tables: every `NEXTVAL` gives the next number, even to several tables. Handy for invoice numbers or shared codes.'),
            C('CREATE SEQUENCE fatture START WITH 1000 INCREMENT BY 1;

SELECT NEXTVAL(fatture);   -- 1000
SELECT NEXTVAL(fatture);   -- 1001
SELECT LASTVAL(fatture);   -- 1001

CREATE TABLE fattura (
    numero INT DEFAULT (NEXTVAL(fatture)) PRIMARY KEY,
    cliente VARCHAR(100)
);', 'sql'),
            P('`LASTVAL` legge l’ultimo valore dato nella tua sessione senza avanzare. A differenza dell’`AUTO_INCREMENT`, la sequenza non appartiene a una colonna.', '`LASTVAL` reads the last value given in your session without advancing. Unlike `AUTO_INCREMENT`, the sequence does not belong to a column.'),
            P('Un numero preso con `NEXTVAL` non torna indietro: se la transazione viene annullata, quel numero è perso e nella serie resta un buco. È voluto: così più sessioni prendono numeri insieme senza aspettarsi. Se ti servono numeri senza buchi, per esempio per fatture che la legge vuole consecutive, assegnali solo al momento della conferma.', 'A number taken with `NEXTVAL` never goes back: if the transaction is rolled back, that number is lost and the series has a gap. It is on purpose: this way several sessions take numbers at the same time without waiting for each other. If you need numbers without gaps, for instance invoices the law wants consecutive, assign them only at the moment of confirmation.'),
        ], [
            Q('q1', L('Che cosa fa `NEXTVAL(fatture)`?', 'What does `NEXTVAL(fatture)` do?'), [L('Dà il numero successivo', 'Gives the next number'), L('Legge senza avanzare', 'Reads without advancing'), L('Azzera la sequenza', 'Resets the sequence'), L('Cancella la sequenza', 'Drops the sequence')], 0, L('Ogni chiamata avanza.', 'Every call advances.')),
            Q('q2', L('Che cosa distingue una sequenza dall’`AUTO_INCREMENT`?', 'What sets a sequence apart from `AUTO_INCREMENT`?'), [L('Non appartiene a una colonna', 'It does not belong to a column'), L('È più lenta', 'It is slower'), L('Conta all’indietro', 'It counts backwards'), L('Funziona solo con i testi', 'It only works with text')], 0, L('Si può condividere tra tabelle.', 'It can be shared between tables.')),
            Q('q3', L('Che cosa fa `LASTVAL`?', 'What does `LASTVAL` do?'), [L('Legge l’ultimo valore dato, senza avanzare', 'Reads the last value given, without advancing'), L('Dà il valore massimo possibile', 'Gives the highest possible value'), L('Avanza di due', 'Advances by two'), L('Chiude la sequenza', 'Closes the sequence')], 0, L('Nella tua sessione.', 'In your session.')),
        ], task('sql', L('Crea la sequenza `tessere` che parte da `500` e avanza di `10`.', 'Create the sequence `tessere` starting at `500` and advancing by `10`.'), '', [re('CREATE\s+SEQUENCE\s+tessere', L('La sequenza esiste', 'The sequence exists')), re('START\s+WITH\s+500', L('Parte da 500', 'It starts at 500')), re('INCREMENT\s+BY\s+10', L('Avanza di 10', 'It advances by 10'))], '', 'CREATE SEQUENCE tessere START WITH 500 INCREMENT BY 10;'), [
            dOrder(pSort(), ['CREATE SEQUENCE ticket START WITH 1;', 'SELECT NEXTVAL(ticket);', 'SELECT NEXTVAL(ticket);', 'SELECT LASTVAL(ticket);'], L('Crea, avanza, avanza, rileggi.', 'Create, advance, advance, read again.')),
            dMatch(pMatch(), [['CREATE SEQUENCE', L('Crea il contatore', 'Creates the counter')], ['START WITH', L('Il primo valore', 'The first value')], ['INCREMENT BY', L('Il passo', 'The step')], ['NEXTVAL', L('Prende il prossimo', 'Takes the next one')], ['LASTVAL', L('Rilegge l’ultimo', 'Reads the last one again')]], L('Il vocabolario delle sequenze.', 'The vocabulary of sequences.')),
        ]),
        lesson('versioning', L('Tabelle con la storia', 'Tables with history'), [
            P('Con `WITH SYSTEM VERSIONING` MariaDB tiene da sola **tutte le versioni** di ogni riga: chi aggiorna o cancella non perde il passato. Poi si interroga la tabella com’era in un momento preciso.', 'With `WITH SYSTEM VERSIONING` MariaDB keeps **every version** of each row by itself: updates and deletes do not lose the past. Then you query the table as it was at a given moment.'),
            C('CREATE TABLE prezzi (
    prodotto_id INT PRIMARY KEY,
    prezzo DECIMAL(8, 2)
) WITH SYSTEM VERSIONING;

UPDATE prezzi SET prezzo = 24.90 WHERE prodotto_id = 1;

SELECT * FROM prezzi
FOR SYSTEM_TIME AS OF TIMESTAMP \'2026-01-01 00:00:00\';

SELECT * FROM prezzi FOR SYSTEM_TIME ALL WHERE prodotto_id = 1;', 'sql', fucina_data_table(['prodotto_id', 'prezzo'], [[1, 19.9], [1, 22.5], [1, 24.9]], 'SELECT * FROM prezzi FOR SYSTEM_TIME ALL WHERE prodotto_id = 1;')),
            P('Dietro le quinte ogni riga ha due colonne nascoste, `ROW_START` e `ROW_END`: il periodo in cui quella versione era valida. Un `UPDATE` chiude la versione vecchia (`ROW_END` diventa «adesso») e ne apre una nuova; un `SELECT` normale vede solo le versioni ancora aperte, mentre `FOR SYSTEM_TIME` guarda nel passato.', 'Behind the scenes every row has two hidden columns, `ROW_START` and `ROW_END`: the period in which that version was valid. An `UPDATE` closes the old version (`ROW_END` becomes “now”) and opens a new one; a plain `SELECT` sees only the versions still open, while `FOR SYSTEM_TIME` looks into the past.'),
            Fig(L(fig_mariadb_history('attuale'), fig_mariadb_history('current')),
                L('Una linea del tempo con tre versioni del prezzo del prodotto 1: 19.90 da marzo a novembre 2025, 22.50 da novembre 2025 ad aprile 2026, 24.90 da aprile 2026 in poi, la versione attuale. Una linea tratteggiata al 1° gennaio 2026 attraversa la versione 22.50.', 'A timeline with three versions of product 1’s price: 19.90 from March to November 2025, 22.50 from November 2025 to April 2026, 24.90 from April 2026 on, the current version. A dashed line at 1 January 2026 crosses the 22.50 version.'),
                L('`AS OF` restituisce la versione valida in quell’istante: qui 22.50.', '`AS OF` returns the version valid at that instant: here 22.50.')),
            P('È utile per audit, annullare errori e analisi nel tempo. Lo spazio cresce con le modifiche: le versioni vecchie si possono potare con `DELETE HISTORY`.', 'It is useful for audits, undoing mistakes and analysis over time. Space grows with changes: old versions can be pruned with `DELETE HISTORY`.'),
        ], [
            Q('v1', L('Che cosa fa `WITH SYSTEM VERSIONING`?', 'What does `WITH SYSTEM VERSIONING` do?'), [L('Conserva tutte le versioni delle righe', 'Keeps every version of the rows'), L('Numera le versioni del server', 'Numbers the server versions'), L('Blocca le modifiche', 'Blocks changes'), L('Crea un backup su file', 'Creates a backup file')], 0, L('Il passato resta interrogabile.', 'The past stays queryable.')),
            Q('v2', L('Come leggi la tabella com’era il 1° gennaio?', 'How do you read the table as it was on 1 January?'), ['FOR SYSTEM_TIME AS OF', 'WHERE date = ', 'HISTORY OF', 'AT VERSION'], 0, L('Con un `TIMESTAMP` preciso.', 'With a precise `TIMESTAMP`.')),
            Q('v3', L('Come togli le versioni vecchie?', 'How do you remove old versions?'), ['DELETE HISTORY', 'DROP VERSIONS', 'TRUNCATE PAST', 'PURGE TABLE'], 0, L('Così lo spazio non cresce all’infinito.', 'So space does not grow forever.')),
        ], task('sql', L('Crea la tabella `stipendi (dipendente_id INT PRIMARY KEY, importo DECIMAL(10,2))` con la storia di sistema attiva.', 'Create the table `stipendi (dipendente_id INT PRIMARY KEY, importo DECIMAL(10,2))` with system history on.'), '', [re('CREATE\s+TABLE\s+stipendi', L('La tabella esiste', 'The table exists')), re('dipendente_id\s+INT\s+PRIMARY\s+KEY', L('Ha la chiave', 'It has the key')), re('\)\s*WITH\s+SYSTEM\s+VERSIONING', L('Conserva la storia', 'It keeps history'))], '', "CREATE TABLE stipendi (\n    dipendente_id INT PRIMARY KEY,\n    importo DECIMAL(10, 2)\n) WITH SYSTEM VERSIONING;"), [
            dBlanks(pFill(), "SELECT * FROM prezzi\nFOR ___ AS ___ TIMESTAMP '2026-03-01 09:00:00';", ['SYSTEM_TIME', 'OF'], ['HISTORY', 'AT', 'VERSION'], L('Viaggio nel tempo dei dati.', 'Time travel for data.')),
            dFlash(pFlash(), [[L('Una tabella versionata perde le righe cancellate', 'A versioned table loses deleted rows'), false], [L('`FOR SYSTEM_TIME ALL` mostra tutte le versioni', '`FOR SYSTEM_TIME ALL` shows every version'), true], [L('Le versioni occupano spazio', 'Versions take up space'), true], [L('Serve un trigger scritto a mano', 'It needs a hand-written trigger'), false]], L('La storia la tiene il database.', 'The database keeps the history.')),
        ]),
        lesson('engines', L('Motori e Galera', 'Engines and Galera'), [
            P('Oltre a InnoDB, MariaDB offre motori per esigenze diverse: **Aria** (sostituto sicuro di MyISAM, usato per le tabelle di sistema), **ColumnStore** (analisi su grandi volumi, dati per colonna), **Spider** (tabelle divise su più server). ColumnStore e Spider si installano come plugin a parte.', 'Beyond InnoDB, MariaDB offers engines for different needs: **Aria** (a crash-safe replacement for MyISAM, used for system tables), **ColumnStore** (analytics on big volumes, data stored by column), **Spider** (tables split across servers). ColumnStore and Spider are installed as separate plugins.'),
            C('CREATE TABLE log_visite (
    giorno DATE,
    pagina VARCHAR(200),
    visite INT
) ENGINE=ColumnStore;

SHOW STATUS LIKE \'wsrep_cluster_size\';  -- nodi del cluster Galera', 'sql'),
            Fig(L(fig_mariadb_columns('InnoDB: per righe', 'ColumnStore: per colonne', 'riga 1 · riga 2 · riga 3 · riga 4', 'SUM(visite) salta da una riga all’altra', 'SUM(visite) legge un blocco solo'), fig_mariadb_columns('InnoDB: by rows', 'ColumnStore: by columns', 'row 1 · row 2 · row 3 · row 4', 'SUM(visite) hops from row to row', 'SUM(visite) reads a single block')),
                L('La stessa tabella con le colonne giorno, pagina e visite salvata in due modi. A sinistra i valori stanno in fila riga per riga (g, p, v, g, p, v…); a destra colonna per colonna (tutti i g, poi tutti i p, poi tutti i v). I blocchi delle visite sono sparsi a sinistra e contigui a destra.', 'The same table with the columns giorno, pagina and visite stored in two ways. On the left the values sit row after row (g, p, v, g, p, v…); on the right column by column (all the g, then all the p, then all the v). The visite blocks are scattered on the left and side by side on the right.'),
                L('Per le analisi che leggono poche colonne di milioni di righe, l’archivio per colonne vince.', 'For analytics that read a few columns of millions of rows, column storage wins.')),
            P('**Galera Cluster** è integrato: più nodi MariaDB accettano tutti scritture e restano sincronizzati (replica sincrona multi-master). Se un nodo cade, gli altri continuano.', '**Galera Cluster** is built in: several MariaDB nodes all accept writes and stay in sync (synchronous multi-master replication). If one node fails, the others carry on.'),
        ], [
            Q('e1', L('A che cosa serve ColumnStore?', 'What is ColumnStore for?'), [L('Analisi su grandi volumi', 'Analytics on large volumes'), L('Tabelle temporanee', 'Temporary tables'), L('Salvare immagini', 'Storing images'), L('Le password', 'Passwords')], 0, L('I dati sono salvati per colonna.', 'Data is stored by column.')),
            Q('e2', L('Che tipo di replica offre Galera?', 'What kind of replication does Galera offer?'), [L('Sincrona multi-master', 'Synchronous multi-master'), L('Solo backup notturni', 'Nightly backups only'), L('Asincrona a un solo master', 'Asynchronous single master'), L('Nessuna', 'None')], 0, L('Tutti i nodi accettano scritture.', 'Every node accepts writes.')),
            Q('e3', L('Come scegli il motore di una tabella?', 'How do you pick a table’s engine?'), [L('Con `ENGINE=` nel `CREATE TABLE`', 'With `ENGINE=` in `CREATE TABLE`'), L('Riavviando il server', 'By restarting the server'), L('Con un `GRANT`', 'With a `GRANT`'), L('Non si può scegliere', 'You cannot choose')], 0, L('Ogni tabella può avere il suo.', 'Every table can have its own.')),
        ], task('sql', L('Crea la tabella `eventi (giorno DATE, tipo VARCHAR(50))` con il motore `Aria`.', 'Create the table `eventi (giorno DATE, tipo VARCHAR(50))` with the `Aria` engine.'), '', [re('CREATE\s+TABLE\s+eventi', L('La tabella esiste', 'The table exists')), re('giorno\s+DATE', L('C’è la data', 'There is the date')), re('ENGINE\s*=\s*Aria', L('Usa Aria', 'It uses Aria'))], '', "CREATE TABLE eventi (\n    giorno DATE,\n    tipo VARCHAR(50)\n) ENGINE=Aria;"), [
            dMatch(pMatch(), [['InnoDB', L('Transazioni, uso generale', 'Transactions, general use')], ['Aria', L('Erede sicuro di MyISAM', 'Crash-safe heir of MyISAM')], ['ColumnStore', L('Analisi per colonna', 'Column analytics')], ['Spider', L('Tabelle su più server', 'Tables across servers')], ['Galera', L('Cluster sincrono', 'Synchronous cluster')]], L('Lo strumento giusto per ogni lavoro.', 'The right tool for each job.')),
            dBug(pBug(), ['CREATE TABLE statistiche (', '    giorno DATE, visite INT', ') MOTORE=ColumnStore;'], 2, ') ENGINE=ColumnStore;', L('La parola chiave è `ENGINE`.', 'The keyword is `ENGINE`.')),
        ]),
        lesson('vector', L('Ricerca vettoriale', 'Vector search'), [
            P('Da MariaDB 11.7 (e quindi nelle LTS 11.8 e 12.3) esiste il tipo **`VECTOR(n)`**: una lista di `n` numeri, l’**embedding** con cui un modello di IA rappresenta il significato di un testo o di un’immagine. Testi simili hanno vettori vicini, quindi si cerca «per significato» ordinando per distanza.', 'Since MariaDB 11.7 (and so in the 11.8 and 12.3 LTS releases) there is the **`VECTOR(n)`** type: a list of `n` numbers, the **embedding** an AI model uses to represent the meaning of a text or an image. Similar texts have nearby vectors, so you search “by meaning” by sorting on distance.'),
            C('CREATE TABLE documenti (
    id INT PRIMARY KEY,
    testo TEXT,
    embedding VECTOR(3) NOT NULL,
    VECTOR INDEX (embedding)
);

INSERT INTO documenti VALUES (1, \'Gatti\', VEC_FromText(\'[0.1, 0.8, 0.3]\'));

SELECT id, testo FROM documenti
ORDER BY VEC_DISTANCE_EUCLIDEAN(embedding, VEC_FromText(\'[0.2, 0.7, 0.3]\'))
LIMIT 5;', 'sql'),
            Fig(L(fig_mariadb_vectors(['Gatti', 'Cani', 'Criceti', 'Auto', 'Moto', 'Pizza'], 'domanda', 'Risultato'), fig_mariadb_vectors(['Cats', 'Dogs', 'Hamsters', 'Cars', 'Bikes', 'Pizza'], 'query', 'Result')),
                L('Un piano con sei punti: Gatti, Cani e Criceti vicini tra loro, Auto e Moto in un altro angolo, Pizza da sola. Il vettore della domanda cade vicino agli animali; un cerchio tratteggiato racchiude i tre punti più vicini, che formano il risultato.', 'A plane with six points: Cats, Dogs and Hamsters close together, Cars and Bikes in another corner, Pizza on its own. The query vector lands near the animals; a dashed circle encloses the three nearest points, which make up the result.'),
                L('Qui i vettori hanno due dimensioni per poterli disegnare: quelli veri ne hanno centinaia, ma l’idea è la stessa.', 'Here the vectors have two dimensions so they can be drawn: real ones have hundreds, but the idea is the same.')),
            P('`VEC_FromText` trasforma un array JSON in vettore; `VEC_DISTANCE_EUCLIDEAN` e `VEC_DISTANCE_COSINE` misurano la distanza. Il **`VECTOR INDEX`** rende veloce la ricerca dei più vicini anche su milioni di righe (la colonna deve essere `NOT NULL`). Gli embedding veri hanno centinaia di dimensioni e li calcola un modello esterno.', '`VEC_FromText` turns a JSON array into a vector; `VEC_DISTANCE_EUCLIDEAN` and `VEC_DISTANCE_COSINE` measure distance. The **`VECTOR INDEX`** makes nearest-neighbour search fast even on millions of rows (the column must be `NOT NULL`). Real embeddings have hundreds of dimensions and an external model computes them.'),
        ], [
            Q('x1', L('Che cosa contiene una colonna `VECTOR(384)`?', 'What does a `VECTOR(384)` column hold?'), [L('Un testo lungo al massimo 384 caratteri', 'A text up to 384 characters long'), L('Un’immagine di 384 pixel', 'A 384-pixel image'), L('Una lista di 384 numeri, un embedding', 'A list of 384 numbers, an embedding'), L('384 righe collegate', '384 linked rows')], 2, L('Il significato tradotto in numeri.', 'Meaning turned into numbers.')),
            Q('x2', L('Come trovi i documenti più simili a una domanda?', 'How do you find the documents most similar to a question?'), [L('Ordini per distanza dal vettore della domanda', 'You sort by distance from the question’s vector'), L('Usi `LIKE` con le parole della domanda', 'You use `LIKE` with the question’s words'), L('Ordini per `id` decrescente', 'You sort by descending `id`'), L('Usi `GROUP BY` sul testo', 'You use `GROUP BY` on the text')], 0, L('I più vicini sono i più simili.', 'The nearest are the most similar.')),
            Q('x3', L('A che cosa serve `VECTOR INDEX`?', 'What is `VECTOR INDEX` for?'), [L('A comprimere i vettori sul disco', 'Compressing vectors on disk'), L('A rendere veloce la ricerca dei vicini', 'Making nearest-neighbour search fast'), L('A calcolare gli embedding dal testo', 'Computing embeddings from text'), L('A vietare i vettori duplicati', 'Forbidding duplicate vectors')], 1, L('Gli embedding li calcola un modello esterno.', 'An external model computes the embeddings.')),
        ], task('sql', L('Dalla tabella `frasi` prendi le 3 righe più vicine al vettore `\'[0.5, 0.1, 0.9]\'`, ordinando per `VEC_DISTANCE_COSINE(embedding, VEC_FromText(…))`.', 'From the table `frasi` take the 3 rows nearest to the vector `\'[0.5, 0.1, 0.9]\'`, sorting by `VEC_DISTANCE_COSINE(embedding, VEC_FromText(…))`.'), 'SELECT id, testo FROM frasi', [re('ORDER\s+BY\s+VEC_DISTANCE_COSINE\s*\(\s*embedding\s*,\s*VEC_FromText\s*\(\s*\'\[\s*0\.5\s*,\s*0\.1\s*,\s*0\.9\s*\]\'\s*\)\s*\)', L('Ordini per distanza', 'You sort by distance')), re('LIMIT\s+3', L('Tieni i 3 più vicini', 'You keep the 3 nearest'))], '', "SELECT id, testo FROM frasi\nORDER BY VEC_DISTANCE_COSINE(embedding, VEC_FromText('[0.5, 0.1, 0.9]'))\nLIMIT 3;"), [
            dBlanks(pFill(), "CREATE TABLE immagini (\n    id INT PRIMARY KEY,\n    embedding ___(512) NOT NULL,\n    VECTOR ___ (embedding)\n);\nSELECT id FROM immagini\nORDER BY ___(embedding, VEC_FromText(@q)) LIMIT 10;", ['VECTOR', 'INDEX', 'VEC_DISTANCE_EUCLIDEAN'], ['ARRAY', 'KEY', 'DISTANCE'], L('Tipo, indice, distanza.', 'Type, index, distance.')),
            dFlash(pFlash(), [[L('Testi simili hanno embedding vicini', 'Similar texts have nearby embeddings'), true], [L('MariaDB calcola da sola gli embedding dal testo', 'MariaDB computes embeddings from text by itself'), false], [L('`VEC_FromText` accetta un array in stile JSON', '`VEC_FromText` accepts a JSON-style array'), true], [L('La colonna di un `VECTOR INDEX` può contenere NULL', 'A `VECTOR INDEX` column can contain NULL'), false]], L('Cercare per significato.', 'Searching by meaning.')),
        ]),
        lesson('types', L('Tipi propri e versioni', 'Its own types and versions'), [
            P('MariaDB ha tipi che MySQL non ha: **`UUID`** (salvato in 16 byte, mostrato come testo), **`INET6`** per gli indirizzi IP (anche IPv4). Il suo `JSON` invece è un alias di `LONGTEXT` con un controllo `JSON_VALID`: le funzioni JSON ci sono quasi tutte, ma gli operatori `->` e `->>` di MySQL mancano fino alla 12.3 LTS (arrivano con la serie 13).', 'MariaDB has types MySQL lacks: **`UUID`** (stored in 16 bytes, shown as text), **`INET6`** for IP addresses (IPv4 too). Its `JSON`, instead, is an alias of `LONGTEXT` with a `JSON_VALID` check: almost all JSON functions are there, but MySQL’s `->` and `->>` operators are missing up to the 12.3 LTS (they arrive with the 13 series).'),
            C('CREATE TABLE accessi (
    id UUID PRIMARY KEY DEFAULT UUID(),
    ip INET6 NOT NULL,
    dettagli JSON,
    quando DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO accessi (ip, dettagli) VALUES (\'192.168.1.10\', \'{"browser": "Firefox"}\');

SELECT ip, JSON_VALUE(dettagli, \'$.browser\') AS browser FROM accessi;', 'sql', fucina_data_table(['ip', 'browser'], [['192.168.1.10', 'Firefox']], 'SELECT ip, JSON_VALUE(dettagli, \'$.browser\') AS browser FROM accessi;')),
            P('Le versioni **LTS** escono ogni anno (la «.3» di ogni serie, come 12.3) con tre anni di correzioni; le altre sono versioni a rotazione con le novità. MariaDB sostituisce senza problemi un vecchio MySQL 5.x, ma da MySQL 8 in poi non è più un rimpiazzo immediato: le due strade si sono separate.', '**LTS** versions come out every year (the “.3” of each series, such as 12.3) with three years of fixes; the others are rolling releases with the new features. MariaDB replaces an old MySQL 5.x smoothly, but from MySQL 8 onwards it is no longer a drop-in replacement: the two paths have parted.'),
        ], [
            Q('t1', L('Che cos’è `JSON` in MariaDB?', 'What is `JSON` in MariaDB?'), [L('Un formato binario come JSONB', 'A binary format like JSONB'), L('Un tipo che accetta solo numeri', 'A type that accepts only numbers'), L('Una tabella speciale di sistema', 'A special system table'), L('Un alias di `LONGTEXT` con il controllo `JSON_VALID`', 'An alias of `LONGTEXT` with the `JSON_VALID` check')], 3, L('Si legge con funzioni come `JSON_VALUE`.', 'You read it with functions such as `JSON_VALUE`.')),
            Q('t2', L('Quale tipo conviene per salvare indirizzi IP?', 'Which type suits storing IP addresses?'), ['INET6', 'VARCHAR(4)', 'UUID', 'POINT'], 0, L('Regge IPv6 e IPv4 in modo compatto.', 'It holds IPv6 and IPv4 compactly.')),
            Q('t3', L('Ogni quanto esce una LTS di MariaDB?', 'How often does a MariaDB LTS come out?'), [L('Ogni mese', 'Every month'), L('Ogni dieci anni', 'Every ten years'), L('Ogni anno, con tre anni di correzioni', 'Every year, with three years of fixes'), L('Solo quando cambia il nome', 'Only when the name changes')], 2, L('La «.3» di ogni serie.', 'The “.3” of each series.')),
        ], task('sql', L('Crea la tabella `dispositivi` con `id UUID PRIMARY KEY DEFAULT UUID()` e `ip INET6 NOT NULL`.', 'Create the table `dispositivi` with `id UUID PRIMARY KEY DEFAULT UUID()` and `ip INET6 NOT NULL`.'), '', [re('CREATE\s+TABLE\s+dispositivi', L('La tabella esiste', 'The table exists')), re('id\s+UUID\s+PRIMARY\s+KEY\s+DEFAULT\s+\(?\s*UUID\s*\(\s*\)\s*\)?', L('L’id è un UUID automatico', 'The id is an automatic UUID')), re('ip\s+INET6\s+NOT\s+NULL', L('L’IP ha il suo tipo', 'The IP has its own type'))], '', "CREATE TABLE dispositivi (\n    id UUID PRIMARY KEY DEFAULT UUID(),\n    ip INET6 NOT NULL\n);"), [
            dMatch(pMatch(), [['UUID', L('Identificatore universale in 16 byte', 'Universal identifier in 16 bytes')], ['INET6', L('Indirizzi IP', 'IP addresses')], ['JSON', L('Testo con controllo di validità', 'Text with a validity check')], ['JSON_VALUE', L('Legge un valore dal JSON', 'Reads a value from JSON')], ['LTS', L('Tre anni di correzioni', 'Three years of fixes')]], L('Le particolarità di MariaDB.', 'MariaDB’s peculiarities.')),
            dBug(pBug(), ['SELECT ip,', '       dettagli->>\'$.browser\' AS browser', 'FROM accessi;'], 1, '       JSON_VALUE(dettagli, \'$.browser\') AS browser', L('Fino alla 12.3 LTS MariaDB non ha l’operatore `->>` di MySQL: si usa `JSON_VALUE`, che funziona in tutte le versioni.', 'Up to the 12.3 LTS MariaDB lacks MySQL’s `->>` operator: you use `JSON_VALUE`, which works in every version.')),
        ]),
    ],
];
