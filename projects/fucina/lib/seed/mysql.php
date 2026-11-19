<?php
/** MySQL: the server, utf8mb4, upserts, JSON columns, users and privileges, EXPLAIN ANALYZE, backups and replication, versions. */

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

/** One server, many databases, many tables; clients connect to the server. */
if (!function_exists('fig_mysql_server')) {
    function fig_mysql_server(string $client, string $server, string $port): string
    {
        $t = fn(int $x, int $y, string $n) => '<rect x="' . $x . '" y="' . $y . '" width="130" height="30" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="' . ($x + 14) . '" y="' . ($y + 20) . '">' . $n . '</text>';
        $g = '';
        foreach (['mysql (CLI)', 'phpMyAdmin', 'PHP / Python'] as $i => $c) {
            $y = 80 + $i * 64;
            $g .= '<rect x="20" y="' . $y . '" width="140" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="90" y="' . ($y + 25) . '" text-anchor="middle">' . $c . '</text>';
            $g .= '<path d="M160 ' . ($y + 20) . ' C 190 ' . ($y + 20) . ', 196 164, 222 164" fill="none" stroke="#8A87B0" stroke-width="2"/>';
        }
        $g .= $t(266, 112, 'prodotti') . $t(266, 150, 'ordini') . $t(266, 188, 'clienti') . $t(458, 112, 'articoli') . $t(458, 150, 'commenti');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="90" y="60" text-anchor="middle" font-weight="700">{$client}</text>
<rect x="230" y="20" width="390" height="244" rx="14" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="248" y="46" font-weight="700" fill="#B84508">{$server}</text>
<rect x="250" y="62" width="162" height="184" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="442" y="62" width="162" height="184" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g font-weight="700"><text x="266" y="90">negozio</text><text x="458" y="90">blog</text></g>
<g fill="#1E1C52"><path d="M222 158 L232 164 L222 170 Z"/></g>
<text x="196" y="270" text-anchor="middle" font-size="13" fill="#5A5880">{$port}</text>
{$g}
</svg>
SVG;
    }
}

/** ON DUPLICATE KEY UPDATE as a decision: insert the row, or update the one already there. */
if (!function_exists('fig_mysql_upsert')) {
    function fig_mysql_upsert(string $q1, string $q2, string $yes, string $no, string $newRow): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="16" y="98" width="150" height="44" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="91" y="125" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13" font-weight="700" fill="#4B2BBF">INSERT (7, 10)</text>
<path d="M166 120 H184" stroke="#1E1C52" stroke-width="2"/><path d="M182 114 L192 120 L182 126 Z" fill="#1E1C52"/>
<path d="M300 56 L408 120 L300 184 L192 120 Z" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="300" y="116" text-anchor="middle" font-weight="700">{$q1}</text><text x="300" y="134" text-anchor="middle" font-weight="700">{$q2}</text>
<path d="M408 120 C 430 120, 430 64, 452 64" fill="none" stroke="#0B7F5C" stroke-width="2"/><path d="M448 58 L458 64 L448 70 Z" fill="#0B7F5C"/>
<path d="M408 120 C 430 120, 430 176, 452 176" fill="none" stroke="#2F8CF0" stroke-width="2"/><path d="M448 170 L458 176 L448 182 Z" fill="#2F8CF0"/>
<text x="428" y="62" text-anchor="end" font-weight="700" fill="#0B7F5C">{$yes}</text><text x="428" y="192" text-anchor="end" font-weight="700" fill="#1B66BC">{$no}</text>
<rect x="460" y="36" width="164" height="56" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="542" y="59" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13" font-weight="700">UPDATE</text><text x="542" y="79" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">punti = punti + 10</text>
<rect x="460" y="148" width="164" height="56" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="542" y="171" text-anchor="middle" font-weight="700">{$newRow}</text><text x="542" y="191" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13">(7, 10)</text>
</svg>
SVG;
    }
}

/** InnoDB: a secondary index holds the key and the primary key; the rows live in the clustered index, ordered by id. */
if (!function_exists('fig_mysql_clustered')) {
    function fig_mysql_clustered(string $sec, string $clu, string $step1, string $step2): string
    {
        $g = '';
        foreach ([[17, 3], [42, 8], [42, 12], [58, 5]] as $i => [$c, $id]) {
            $y = 96 + $i * 36;
            $hot = $c === 42;
            $g .= '<rect x="32" y="' . ($y - 21) . '" width="196" height="30" rx="8" fill="' . ($hot ? '#FFE8D6' : '#FFFFFF') . '" stroke="' . ($hot ? '#E8590C' : '#C9C7DE') . '" stroke-width="2"/><text x="48" y="' . $y . '">' . $c . '</text><text x="212" y="' . $y . '" text-anchor="end" fill="#0B7F5C" font-weight="700">' . $id . '</text>';
        }
        foreach ([[3, 17, 'nuovo'], [5, 58, 'spedito'], [8, 42, 'pagato'], [12, 42, 'pagato']] as $i => [$id, $c, $st]) {
            $y = 96 + $i * 36;
            $hot = $c === 42;
            $g .= '<rect x="352" y="' . ($y - 21) . '" width="256" height="30" rx="8" fill="' . ($hot ? '#FFE8D6' : '#FFFFFF') . '" stroke="' . ($hot ? '#E8590C' : '#C9C7DE') . '" stroke-width="2"/><text x="368" y="' . $y . '" fill="#0B7F5C" font-weight="700">' . $id . '</text><text x="420" y="' . $y . '">' . $c . '</text><text x="510" y="' . $y . '">' . $st . '</text>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<text x="20" y="26" font-weight="700">{$sec}</text><text x="340" y="26" font-weight="700">{$clu}</text>
<rect x="20" y="38" width="220" height="190" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="340" y="38" width="280" height="190" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g font-weight="700" font-size="13"><text x="48" y="62">cliente_id</text><text x="212" y="62" text-anchor="end">id</text><text x="368" y="62">id</text><text x="420" y="62">cliente_id</text><text x="510" y="62">stato</text></g>
{$g}
<g fill="none" stroke="#E8590C" stroke-width="2"><path d="M228 126 C 290 126, 290 162, 344 162"/><path d="M228 162 C 290 162, 290 198, 344 198"/></g>
<g fill="#E8590C"><path d="M342 156 L352 162 L342 168 Z"/><path d="M342 192 L352 198 L342 204 Z"/></g>
<text x="20" y="260" font-weight="700" fill="#B84508">{$step1}</text>
<text x="20" y="284" font-weight="700" fill="#0B7F5C">{$step2}</text>
</svg>
SVG;
    }
}

/** Replication: the app writes to the source, the binary log carries the changes to the replicas, reads go to the replicas. */
if (!function_exists('fig_mysql_replica')) {
    function fig_mysql_replica(string $writes, string $reads, string $binlog): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.pk{animation:pk1 4s linear infinite}.pk2{animation:pk2 4s linear infinite 2s}@keyframes pk1{0%{transform:translate(0,0);opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translate(112px,-62px);opacity:0}}@keyframes pk2{0%{transform:translate(0,0);opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translate(112px,62px);opacity:0}}@media (prefers-reduced-motion:reduce){.pk,.pk2{animation:none}}</style>
<rect x="20" y="110" width="100" height="50" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="70" y="140" text-anchor="middle" font-weight="700">app</text>
<path d="M120 135 H200" stroke="#E8590C" stroke-width="2"/><path d="M198 129 L208 135 L198 141 Z" fill="#E8590C"/>
<text x="162" y="126" text-anchor="middle" font-size="13" font-weight="700" fill="#B84508">{$writes}</text>
<rect x="210" y="86" width="150" height="98" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="285" y="112" text-anchor="middle" font-weight="700" fill="#B84508">source</text>
<rect x="226" y="126" width="118" height="42" rx="8" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/>
<g stroke="#E8590C" stroke-width="2"><line x1="236" y1="139" x2="334" y2="139"/><line x1="236" y1="147" x2="334" y2="147"/><line x1="236" y1="155" x2="300" y2="155"/></g>
<text x="285" y="204" text-anchor="middle" font-size="13" fill="#5A5880">{$binlog}</text>
<path d="M360 116 L474 52" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/><path d="M360 154 L474 218" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/>
<rect class="pk" x="356" y="108" width="14" height="14" rx="3" fill="#E8590C"/>
<rect class="pk2" x="356" y="148" width="14" height="14" rx="3" fill="#E8590C"/>
<rect x="476" y="22" width="148" height="60" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="550" y="57" text-anchor="middle" font-weight="700" fill="#1B66BC">replica 1</text>
<rect x="476" y="188" width="148" height="60" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="550" y="223" text-anchor="middle" font-weight="700" fill="#1B66BC">replica 2</text>
<path d="M70 110 V36 H468" fill="none" stroke="#2F8CF0" stroke-width="2"/><path d="M466 30 L476 36 L466 42 Z" fill="#2F8CF0"/>
<path d="M70 160 V238 H468" fill="none" stroke="#2F8CF0" stroke-width="2"/><path d="M466 232 L476 238 L466 244 Z" fill="#2F8CF0"/>
<text x="160" y="28" font-size="13" font-weight="700" fill="#1B66BC">{$reads}</text><text x="160" y="258" font-size="13" font-weight="700" fill="#1B66BC">{$reads}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'mysql', 'family' => 'mysql', 'after' => 'sql-base',
    'title' => L('MySQL', 'MySQL'), 'short' => L('MySQL', 'MySQL'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Uno dei database più diffusi sul web: client, InnoDB, `utf8mb4`, upsert, colonne JSON, utenti e permessi, `EXPLAIN ANALYZE`, backup e replica, versioni LTS.', 'One of the most widely used databases on the web: client, InnoDB, `utf8mb4`, upserts, JSON columns, users and privileges, `EXPLAIN ANALYZE`, backups and replication, LTS versions.'),
    'lessons' => [
        lesson('server', L('Server, client e database', 'Server, client and databases'), [
            P('MySQL è un **server**: un programma sempre acceso che custodisce i database e risponde a chi si collega. Ci si collega con il client a riga di comando, con phpMyAdmin o da un linguaggio come PHP o Python.', 'MySQL is a **server**: an always-on program that keeps the databases and answers whoever connects. You connect with the command-line client, with phpMyAdmin or from a language such as PHP or Python.'),
            C('mysql -u root -p

SHOW DATABASES;
CREATE DATABASE negozio CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE negozio;
SHOW TABLES;
DESCRIBE prodotti;', 'sql'),
            Fig(L(fig_mysql_server('client', 'server MySQL (mysqld)', 'porta 3306'), fig_mysql_server('clients', 'MySQL server (mysqld)', 'port 3306')),
                L('Tre client (il comando mysql, phpMyAdmin, PHP o Python) si collegano a un unico server MySQL. Dentro il server ci sono due database: negozio, con le tabelle prodotti, ordini e clienti, e blog, con articoli e commenti.', 'Three clients (the mysql command, phpMyAdmin, PHP or Python) connect to a single MySQL server. Inside the server there are two databases: negozio, with the tables prodotti, ordini and clienti, and blog, with articoli and commenti.'),
                L('Server → database → tabelle: `USE` sceglie il database su cui lavorare.', 'Server → databases → tables: `USE` picks the database to work on.')),
            P('Un server contiene più database, ognuno con le sue tabelle. Il motore di archiviazione predefinito è **InnoDB**: supporta transazioni e chiavi esterne. Il vecchio MyISAM non le supporta.', 'A server holds several databases, each with its own tables. The default storage engine is **InnoDB**: it supports transactions and foreign keys. The old MyISAM does not.'),
            Tip('`DESCRIBE tabella` mostra colonne, tipi e chiavi: il primo comando da lanciare su un database che non conosci.', '`DESCRIBE table` shows columns, types and keys: the first command to run on a database you do not know.'),
        ], [
            Q('s1', L('Quale comando sceglie il database su cui lavorare?', 'Which command picks the database to work on?'), ['USE negozio;', 'OPEN negozio;', 'SELECT negozio;', 'CONNECT negozio;'], 0, L('Da lì in poi le query vanno su quel database.', 'From then on queries go to that database.')),
            Q('s2', L('Qual è il motore predefinito di MySQL?', 'What is MySQL’s default engine?'), ['MyISAM', 'InnoDB', 'Memory', 'CSV'], 1, L('InnoDB, con transazioni e chiavi esterne.', 'InnoDB, with transactions and foreign keys.')),
            Q('s3', L('Che cosa mostra `DESCRIBE prodotti`?', 'What does `DESCRIBE prodotti` show?'), [L('Le righe della tabella', 'The table rows'), L('Colonne, tipi e chiavi', 'Columns, types and keys'), L('Chi ha creato la tabella', 'Who created the table'), L('Lo spazio su disco', 'The disk space')], 1, L('È la struttura, non il contenuto.', 'It is the structure, not the content.')),
        ], task('sql', L('Crea il database `scuola` con il set di caratteri `utf8mb4`, poi selezionalo con `USE`.', 'Create the database `scuola` with the `utf8mb4` character set, then select it with `USE`.'), '', [re('CREATE\s+DATABASE\s+scuola\b[^;]*CHARACTER\s+SET\s*=?\s*utf8mb4', L('Il database usa utf8mb4', 'The database uses utf8mb4')), re('USE\s+scuola\s*;', L('Lo selezioni', 'You select it'))], '', "CREATE DATABASE scuola CHARACTER SET utf8mb4;\nUSE scuola;"), [
            dOrder(pSort(), ['mysql -u root -p', 'CREATE DATABASE negozio;', 'USE negozio;', 'SHOW TABLES;'], L('Collegarsi, creare, scegliere, guardare.', 'Connect, create, choose, look.')),
            dMatch(pMatch(), [['SHOW DATABASES', L('Elenca i database', 'Lists the databases')], ['USE', L('Sceglie il database', 'Picks the database')], ['SHOW TABLES', L('Elenca le tabelle', 'Lists the tables')], ['DESCRIBE', L('Mostra la struttura', 'Shows the structure')], ['InnoDB', L('Motore con transazioni', 'Engine with transactions')]], L('Orientarsi su un server MySQL.', 'Finding your way on a MySQL server.')),
        ]),
        lesson('types', L('Tipi e utf8mb4', 'Types and utf8mb4'), [
            P('In MySQL `utf8` è un nome storico (oggi alias deprecato di `utf8mb3`) che regge solo 3 byte per carattere: emoji e molti simboli non entrano. Usa sempre **`utf8mb4`**, il vero UTF-8: da MySQL 8.0 è il predefinito, ma nei database creati con versioni vecchie va controllato.', 'In MySQL `utf8` is a historical name (now a deprecated alias of `utf8mb3`) that only holds 3 bytes per character: emoji and many symbols do not fit. Always use **`utf8mb4`**, the real UTF-8: since MySQL 8.0 it is the default, but databases created with old versions need checking.'),
            C('CREATE TABLE ordini (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stato ENUM(\'nuovo\', \'pagato\', \'spedito\') NOT NULL DEFAULT \'nuovo\',
    totale DECIMAL(10, 2) NOT NULL,
    creato_il DATETIME DEFAULT CURRENT_TIMESTAMP,
    note TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;', 'sql'),
            P('`UNSIGNED` vieta i negativi e raddoppia il massimo positivo. `ENUM` accetta solo i valori elencati. `DATETIME` salva data e ora così come sono; `TIMESTAMP` le converte in UTC e funziona solo dal 1970 al 2038.', '`UNSIGNED` forbids negatives and doubles the positive maximum. `ENUM` accepts only the listed values. `DATETIME` stores date and time as they are; `TIMESTAMP` converts them to UTC and only works from 1970 to 2038.'),
        ], [
            Q('y1', L('Perché preferire `utf8mb4` a `utf8`?', 'Why prefer `utf8mb4` to `utf8`?'), [L('È più veloce', 'It is faster'), L('Regge emoji e tutti i caratteri', 'It holds emoji and every character'), L('Occupa sempre meno spazio', 'It always takes less space'), L('È l’unico con gli indici', 'It is the only one with indexes')], 1, L('Il vecchio `utf8` si ferma a 3 byte.', 'The old `utf8` stops at 3 bytes.')),
            Q('y2', L('Che cosa fa `ENUM(\'a\', \'b\')`?', 'What does `ENUM(\'a\', \'b\')` do?'), [L('Accetta solo i valori elencati', 'Accepts only the listed values'), L('Numera le righe', 'Numbers the rows'), L('Crea due colonne', 'Creates two columns'), L('Cifra il testo', 'Encrypts the text')], 0, L('Un elenco chiuso di valori.', 'A closed list of values.')),
            Q('y3', L('Che cosa vieta `UNSIGNED`?', 'What does `UNSIGNED` forbid?'), [L('I valori nulli', 'Null values'), L('I numeri negativi', 'Negative numbers'), L('I doppioni', 'Duplicates'), L('I decimali', 'Decimals')], 1, L('In cambio il massimo positivo raddoppia.', 'In exchange the positive maximum doubles.')),
        ], task('sql', L('Crea la tabella `messaggi` con `id INT AUTO_INCREMENT PRIMARY KEY`, `testo TEXT` e `DEFAULT CHARSET=utf8mb4`.', 'Create the table `messaggi` with `id INT AUTO_INCREMENT PRIMARY KEY`, `testo TEXT` and `DEFAULT CHARSET=utf8mb4`.'), '', [re('CREATE\s+TABLE\s+messaggi', L('La tabella esiste', 'The table exists')), re('id\s+INT\b[^,]*AUTO_INCREMENT', L('L’id si numera da solo', 'The id numbers itself')), re('testo\s+TEXT', L('C’è il testo', 'There is the text')), re('CHARSET\s*=?\s*utf8mb4', L('Usa utf8mb4', 'It uses utf8mb4'))], '', "CREATE TABLE messaggi (\n    id INT AUTO_INCREMENT PRIMARY KEY,\n    testo TEXT\n) DEFAULT CHARSET=utf8mb4;"), [
            dBug(pBug(), ['CREATE TABLE commenti (', '    id INT AUTO_INCREMENT PRIMARY KEY,', '    testo TEXT', ') DEFAULT CHARSET=utf8;'], 3, ') DEFAULT CHARSET=utf8mb4;', L('Con `utf8` le emoji non entrano.', 'With `utf8` emoji do not fit.')),
            dFlash(pFlash(), [[L('`utf8` in MySQL regge le emoji', '`utf8` in MySQL holds emoji'), false], [L('`TIMESTAMP` viene salvato in UTC', '`TIMESTAMP` is stored in UTC'), true], [L('`ENUM` accetta qualsiasi testo', '`ENUM` accepts any text'), false], [L('`DECIMAL` è adatto ai prezzi', '`DECIMAL` suits prices'), true]], L('I tipi giusti evitano sorprese.', 'The right types avoid surprises.')),
        ]),
        lesson('upsert', L('Inserire o aggiornare', 'Insert or update'), [
            P('Capita spesso di voler inserire una riga **oppure** aggiornarla se esiste già. MySQL lo fa in un’istruzione sola con `ON DUPLICATE KEY UPDATE`, che scatta quando l’inserimento violerebbe una chiave primaria o un indice `UNIQUE`.', 'You often want to insert a row **or** update it if it already exists. MySQL does it in one statement with `ON DUPLICATE KEY UPDATE`, which fires when the insert would break a primary key or a `UNIQUE` index.'),
            C('INSERT INTO punteggi (utente_id, punti) VALUES (7, 10)
ON DUPLICATE KEY UPDATE punti = punti + 10;

INSERT IGNORE INTO iscrizioni (utente_id, corso) VALUES (7, \'sql\');', 'sql'),
            Fig(L(fig_mysql_upsert('la chiave 7', 'esiste già?', 'sì', 'no', 'nuova riga'), fig_mysql_upsert('does key 7', 'exist already?', 'yes', 'no', 'new row')),
                L('Un diagramma di flusso: INSERT (7, 10) arriva a una domanda, la chiave 7 esiste già? Se sì si esegue UPDATE punti = punti + 10, se no si inserisce la nuova riga (7, 10).', 'A flowchart: INSERT (7, 10) reaches a question, does key 7 exist already? If yes it runs UPDATE punti = punti + 10, if no it inserts the new row (7, 10).'),
                L('Una sola istruzione, due esiti: decide la chiave primaria o un indice `UNIQUE`.', 'One statement, two outcomes: the primary key or a `UNIQUE` index decides.')),
            P('Dentro `UPDATE` puoi leggere i valori nuovi con un alias di riga: `VALUES (7, 10) AS n ON DUPLICATE KEY UPDATE punti = punti + n.punti` (la vecchia funzione `VALUES()` è deprecata). `INSERT IGNORE` salta le righe che sarebbero doppie, con un semplice avviso al posto dell’errore. `REPLACE` invece cancella la vecchia riga e ne inserisce una nuova: attenzione, perché cambia l’`AUTO_INCREMENT` e fa scattare le cancellazioni a cascata.', 'Inside `UPDATE` you can read the new values with a row alias: `VALUES (7, 10) AS n ON DUPLICATE KEY UPDATE punti = punti + n.punti` (the old `VALUES()` function is deprecated). `INSERT IGNORE` skips rows that would be duplicates, with a mere warning instead of an error. `REPLACE` instead deletes the old row and inserts a new one: careful, because it changes the `AUTO_INCREMENT` and triggers cascading deletes.'),
        ], [
            Q('u1', L('Quando scatta `ON DUPLICATE KEY UPDATE`?', 'When does `ON DUPLICATE KEY UPDATE` fire?'), [L('Sempre', 'Always'), L('Quando una chiave unica sarebbe ripetuta', 'When a unique key would repeat'), L('Quando la tabella è vuota', 'When the table is empty'), L('Solo nelle transazioni', 'Only in transactions')], 1, L('Serve una chiave primaria o `UNIQUE`.', 'It needs a primary or `UNIQUE` key.')),
            Q('u2', L('Che cosa fa `INSERT IGNORE` con una riga doppia?', 'What does `INSERT IGNORE` do with a duplicate row?'), [L('La salta', 'Skips it'), L('La sovrascrive', 'Overwrites it'), L('Blocca tutto con un errore', 'Stops everything with an error'), L('La duplica comunque', 'Duplicates it anyway')], 0, L('Niente errore, niente riga.', 'No error, no row.')),
            Q('u3', L('Perché `REPLACE` può essere pericoloso?', 'Why can `REPLACE` be dangerous?'), [L('Cancella e reinserisce la riga', 'It deletes and reinserts the row'), L('È lentissimo', 'It is very slow'), L('Non esiste in MySQL', 'It does not exist in MySQL'), L('Svuota la tabella', 'It empties the table')], 0, L('Nuovo id e cancellazioni a cascata.', 'New id and cascading deletes.')),
        ], task('sql', L('Inserisci `(3, 5)` in `visite (pagina_id, conteggio)`; se la pagina esiste già, somma 1 a `conteggio`.', 'Insert `(3, 5)` into `visite (pagina_id, conteggio)`; if the page already exists, add 1 to `conteggio`.'), '', [re('INSERT\s+INTO\s+visite\s*\(\s*pagina_id\s*,\s*conteggio\s*\)\s*VALUES\s*\(\s*3\s*,\s*5\s*\)', L('Inserisci la riga', 'You insert the row')), re('ON\s+DUPLICATE\s+KEY\s+UPDATE\s+conteggio\s*=\s*conteggio\s*\+\s*1', L('Altrimenti aggiorni', 'Otherwise you update'))], '', "INSERT INTO visite (pagina_id, conteggio) VALUES (3, 5)\nON DUPLICATE KEY UPDATE conteggio = conteggio + 1;"), [
            dBlanks(pFill(), "INSERT INTO carrello (utente_id, prodotto_id, qta)\nVALUES (1, 42, 1)\nON ___ KEY ___ qta = qta + 1;", ['DUPLICATE', 'UPDATE'], ['CONFLICT', 'SET', 'REPLACE'], L('La sintassi MySQL dell’upsert.', 'MySQL’s upsert syntax.')),
            dMatch(pMatch(), [['ON DUPLICATE KEY UPDATE', L('Inserisce o aggiorna', 'Inserts or updates')], ['INSERT IGNORE', L('Salta i doppioni', 'Skips duplicates')], ['REPLACE', L('Cancella e reinserisce', 'Deletes and reinserts')], ['UNIQUE', L('Vieta i valori ripetuti', 'Forbids repeated values')]], L('Tre modi di gestire i doppioni.', 'Three ways to handle duplicates.')),
        ]),
        lesson('json', L('Colonne JSON', 'JSON columns'), [
            P('Il tipo `JSON` salva documenti JSON validati. Per leggere un campo si usa un **percorso** che parte da `$`: `JSON_EXTRACT(dati, \'$.citta\')`, oppure le scorciatoie `->` (valore JSON) e `->>` (testo senza virgolette).', 'The `JSON` type stores validated JSON documents. To read a field you use a **path** starting from `$`: `JSON_EXTRACT(data, \'$.city\')`, or the shortcuts `->` (JSON value) and `->>` (text without quotes).'),
            C('CREATE TABLE profili (id INT PRIMARY KEY, dati JSON);

INSERT INTO profili VALUES (1, \'{"nome": "Ada", "tag": ["sql", "php"]}\');

SELECT dati->>\'$.nome\' AS nome FROM profili
WHERE JSON_CONTAINS(dati->\'$.tag\', \'"sql"\');', 'sql', fucina_data_table(['nome'], [['Ada']], 'SELECT dati->>\'$.nome\' AS nome …')),
            P('Il JSON è comodo per dati che cambiano forma, ma i campi su cui filtri spesso meritano una colonna vera, con il suo indice. Si può anche indicizzare un campo JSON con una **colonna generata**.', 'JSON is handy for data that changes shape, but fields you often filter on deserve a real column, with its own index. You can also index a JSON field through a **generated column**.'),
        ], [
            Q('j1', L('Che cosa restituisce `dati->>\'$.nome\'`?', 'What does `dati->>\'$.nome\'` return?'), [L('Il nome come testo semplice', 'The name as plain text'), L('Il nome tra virgolette JSON', 'The name in JSON quotes'), L('Tutto il documento', 'The whole document'), L('Un errore', 'An error')], 0, L('`->` lo darebbe tra virgolette.', '`->` would give it in quotes.')),
            Q('j2', L('Da quale simbolo parte un percorso JSON?', 'Which symbol does a JSON path start with?'), ['$', '#', '@', '&'], 0, L('`$` è la radice del documento.', '`$` is the document root.')),
            Q('j3', L('Come indicizzi un campo dentro una colonna JSON?', 'How do you index a field inside a JSON column?'), [L('Con una colonna generata', 'With a generated column'), L('Non si può', 'You cannot'), L('Con `ENUM`', 'With `ENUM`'), L('Con `REPLACE`', 'With `REPLACE`')], 0, L('La colonna generata si indicizza come le altre.', 'The generated column is indexed like any other.')),
        ], task('sql', L('Dalla tabella `ordini` leggi `id` e il campo `citta` della colonna JSON `spedizione` come testo, con `->>`.', 'From the table `ordini` read `id` and the `citta` field of the JSON column `spedizione` as text, with `->>`.'), '', [re('SELECT\s+id\s*,\s*spedizione\s*->>\s*\'\$\.citta\'', L('Leggi il campo come testo', 'You read the field as text')), re('FROM\s+ordini', L('Dalla tabella giusta', 'From the right table'))], '', "SELECT id, spedizione->>'$.citta' FROM ordini;"), [
            dBug(pBug(), ['SELECT', '  dati->>\'nome\' AS nome', 'FROM profili;'], 1, '  dati->>\'$.nome\' AS nome', L('Il percorso parte da `$`.', 'The path starts from `$`.')),
            dFlash(pFlash(), [[L('MySQL controlla che il JSON sia valido', 'MySQL checks that the JSON is valid'), true], [L('`->>` toglie le virgolette', '`->>` removes the quotes'), true], [L('Un campo JSON non si può mai indicizzare', 'A JSON field can never be indexed'), false], [L('Il percorso della radice è `#`', 'The root path is `#`'), false]], L('JSON sì, ma con giudizio.', 'JSON yes, but wisely.')),
        ]),
        lesson('users', L('Utenti, permessi e transazioni', 'Users, privileges and transactions'), [
            P('Un’applicazione non dovrebbe mai collegarsi come `root`. Si crea un utente con solo i **permessi** che servono: è il principio del minimo privilegio.', 'An application should never connect as `root`. You create a user with only the **privileges** it needs: the principle of least privilege.'),
            C('CREATE USER \'app\'@\'localhost\' IDENTIFIED BY \'una-password-lunga\';
GRANT SELECT, INSERT, UPDATE, DELETE ON negozio.* TO \'app\'@\'localhost\';
REVOKE DELETE ON negozio.* FROM \'app\'@\'localhost\';

START TRANSACTION;
UPDATE conti SET saldo = saldo - 50 WHERE id = 1;
UPDATE conti SET saldo = saldo + 50 WHERE id = 2;
COMMIT;', 'sql'),
            P('Una **transazione** raggruppa più istruzioni: o vanno a buon fine tutte (`COMMIT`) o nessuna (`ROLLBACK`). Funziona con le tabelle InnoDB.', 'A **transaction** groups several statements: either they all succeed (`COMMIT`) or none does (`ROLLBACK`). It works with InnoDB tables.'),
            P('Un account MySQL è fatto di due parti, **utente e host**: `\'app\'@\'localhost\'` può collegarsi solo dalla stessa macchina, `\'app\'@\'%\'` da ovunque, e per MySQL sono due account diversi. Per gruppi di utenti con gli stessi permessi, da MySQL 8.0 esistono i **ruoli** (`CREATE ROLE`, poi `GRANT ruolo TO utente`). E tieni la password fuori dal codice, in una variabile d’ambiente.', 'A MySQL account has two parts, **user and host**: `\'app\'@\'localhost\'` can connect only from the same machine, `\'app\'@\'%\'` from anywhere, and to MySQL they are two different accounts. For groups of users with the same privileges, MySQL 8.0 has **roles** (`CREATE ROLE`, then `GRANT role TO user`). And keep the password out of the code, in an environment variable.'),
            Tip('MySQL lavora in **autocommit**: ogni istruzione fuori da una transazione viene confermata subito. `START TRANSACTION` sospende l’autocommit fino al `COMMIT` o al `ROLLBACK`.', 'MySQL works in **autocommit** mode: every statement outside a transaction is committed straight away. `START TRANSACTION` suspends autocommit until the `COMMIT` or the `ROLLBACK`.'),
        ], [
            Q('p1', L('Che cosa significa `negozio.*` in un `GRANT`?', 'What does `negozio.*` mean in a `GRANT`?'), [L('Tutte le tabelle di `negozio`', 'Every table of `negozio`'), L('Tutti i database', 'Every database'), L('Tutte le colonne di una tabella', 'Every column of a table'), L('Tutti gli utenti', 'Every user')], 0, L('database.tabella, con `*` come jolly.', 'database.table, with `*` as wildcard.')),
            Q('p2', L('Che cosa fa `ROLLBACK`?', 'What does `ROLLBACK` do?'), [L('Annulla la transazione', 'Undoes the transaction'), L('Conferma la transazione', 'Confirms the transaction'), L('Riavvia il server', 'Restarts the server'), L('Cancella l’utente', 'Deletes the user')], 0, L('Come se non fosse successo niente.', 'As if nothing had happened.')),
            Q('p3', L('Perché l’app non deve usare `root`?', 'Why should the app not use `root`?'), [L('Per il minimo privilegio', 'For least privilege'), L('`root` è più lento', '`root` is slower'), L('`root` non può leggere', '`root` cannot read'), L('È vietato dalla licenza', 'The licence forbids it')], 0, L('Una falla non deve poter fare tutto.', 'A breach must not be able to do everything.')),
        ], task('sql', L('Dai all’utente `\'report\'@\'localhost\'` solo il permesso `SELECT` su tutte le tabelle del database `negozio`.', 'Give the user `\'report\'@\'localhost\'` only the `SELECT` privilege on every table of the database `negozio`.'), '', [re('GRANT\s+SELECT\s+ON\s+negozio\.\*', L('Solo lettura su negozio', 'Read only on negozio')), re('TO\s+\'report\'@\'localhost\'', L('All’utente giusto', 'To the right user'))], '', "GRANT SELECT ON negozio.* TO 'report'@'localhost';"), [
            dOrder(pSort(), ['START TRANSACTION;', 'UPDATE conti SET saldo = saldo - 50 WHERE id = 1;', 'UPDATE conti SET saldo = saldo + 50 WHERE id = 2;', 'COMMIT;'], L('Apri, sposta, conferma.', 'Open, move, confirm.')),
            dBlanks(pFill(), "CREATE ___ 'app'@'localhost' IDENTIFIED BY 'segreta';\n___ SELECT, INSERT ON negozio.* ___ 'app'@'localhost';", ['USER', 'GRANT', 'TO'], ['ROLE', 'ALLOW', 'FOR'], L('Creare l’utente e dargli i permessi.', 'Create the user and give it privileges.')),
        ]),
        lesson('explain', L('Indici ed EXPLAIN ANALYZE', 'Indexes and EXPLAIN ANALYZE'), [
            P('In InnoDB ogni tabella è ordinata per **chiave primaria** (indice *clustered*); gli altri indici contengono la colonna indicizzata più la chiave primaria. `EXPLAIN` mostra il piano previsto; **`EXPLAIN ANALYZE`** esegue davvero la query e riporta tempi e righe reali, in forma ad albero.', 'In InnoDB every table is ordered by its **primary key** (a *clustered* index); other indexes hold the indexed column plus the primary key. `EXPLAIN` shows the expected plan; **`EXPLAIN ANALYZE`** really runs the query and reports actual times and rows, as a tree.'),
            C('EXPLAIN ANALYZE
SELECT * FROM ordini WHERE cliente_id = 42 AND stato = \'pagato\';

CREATE INDEX ix_ordini_cliente_stato ON ordini (cliente_id, stato);

-- provare a «spegnere» un indice prima di cancellarlo
ALTER TABLE ordini ALTER INDEX ix_vecchio INVISIBLE;', 'sql'),
            Fig(L(fig_mysql_clustered('Indice secondario (cliente_id)', 'Tabella = indice clustered (id)', '1. Cerca 42 nell’indice secondario: trova gli id 8 e 12.', '2. Con quegli id legge le righe intere nella tabella.'), fig_mysql_clustered('Secondary index (cliente_id)', 'Table = clustered index (id)', '1. Find 42 in the secondary index: it gives ids 8 and 12.', '2. With those ids it reads the whole rows in the table.')),
                L('A sinistra l’indice secondario, ordinato per cliente_id, dove ogni voce porta con sé l’id della riga. A destra la tabella, ordinata per id. Due frecce vanno dalle voci con cliente_id 42 alle righe con id 8 e 12.', 'On the left the secondary index, sorted by cliente_id, where each entry carries the row’s id. On the right the table, sorted by id. Two arrows go from the entries with cliente_id 42 to the rows with id 8 and 12.'),
                L('Due ricerche: per questo una chiave primaria corta rende più leggeri tutti gli altri indici.', 'Two lookups: that is why a short primary key makes every other index lighter.')),
            P('Nel piano tradizionale guarda la colonna `type`: `ALL` vuol dire scansione completa, `ref` o `range` che usa un indice; `key` dice quale. Un indice **invisibile** resta aggiornato ma l’ottimizzatore lo ignora: utile per capire se puoi cancellarlo senza rischi.', 'In the traditional plan look at the `type` column: `ALL` means a full scan, `ref` or `range` that an index is used; `key` says which one. An **invisible** index stays up to date but the optimiser ignores it: useful to find out whether you can drop it safely.'),
        ], [
            Q('e1', L('Che cosa fa in più `EXPLAIN ANALYZE` rispetto a `EXPLAIN`?', 'What does `EXPLAIN ANALYZE` do beyond `EXPLAIN`?'), [L('Crea gli indici che mancano', 'Creates the missing indexes'), L('Esegue la query e mostra tempi e righe reali', 'Runs the query and shows real times and rows'), L('Analizza la sintassi senza eseguire nulla', 'Checks the syntax without running anything'), L('Aggiorna le statistiche di tutte le tabelle', 'Updates the statistics of every table')], 1, L('Attenzione: la query viene eseguita davvero.', 'Careful: the query is really executed.')),
            Q('e2', L('Che cosa indica `type: ALL` nel piano?', 'What does `type: ALL` mean in the plan?'), [L('Che usa tutti gli indici', 'That it uses every index'), L('Che restituisce tutte le colonne', 'That it returns every column'), L('Una scansione completa della tabella', 'A full scan of the table'), L('Che la query è perfetta', 'That the query is perfect')], 2, L('Su tabelle grandi è un segnale d’allarme.', 'On big tables it is a warning sign.')),
            Q('e3', L('A che cosa serve un indice invisibile?', 'What is an invisible index for?'), [L('A provare a farne a meno prima di cancellarlo', 'To try doing without it before dropping it'), L('A nascondere i dati agli utenti', 'To hide data from users'), L('A cifrare la colonna indicizzata', 'To encrypt the indexed column'), L('A risparmiare spazio su disco', 'To save disk space')], 0, L('Si rende di nuovo visibile in un attimo.', 'It can be made visible again in a moment.')),
        ], task('sql', L('Analizza con `EXPLAIN ANALYZE` la query `SELECT * FROM utenti WHERE email = \'ada@example.com\'`, poi crea l’indice `ix_utenti_email` su `email`.', 'Analyse with `EXPLAIN ANALYZE` the query `SELECT * FROM utenti WHERE email = \'ada@example.com\'`, then create the index `ix_utenti_email` on `email`.'), "SELECT * FROM utenti WHERE email = 'ada@example.com';", [re('EXPLAIN\s+ANALYZE\s+SELECT\s+\*\s+FROM\s+utenti', L('Chiedi il piano reale', 'You ask for the real plan')), re('CREATE\s+(UNIQUE\s+)?INDEX\s+ix_utenti_email\s+ON\s+utenti\s*\(\s*email\s*\)', L('Crei l’indice', 'You create the index'))], '', "EXPLAIN ANALYZE SELECT * FROM utenti WHERE email = 'ada@example.com';\nCREATE INDEX ix_utenti_email ON utenti (email);"), [
            dMatch(pMatch(), [['EXPLAIN', L('Piano previsto', 'Expected plan')], ['EXPLAIN ANALYZE', L('Piano con tempi reali', 'Plan with real times')], ['type: ALL', L('Scansione completa', 'Full scan')], ['INVISIBLE', L('Indice ignorato dall’ottimizzatore', 'Index ignored by the optimiser')], [L('Chiave primaria', 'Primary key'), L('Ordine fisico in InnoDB', 'Physical order in InnoDB')]], L('Leggere il piano di MySQL.', 'Reading MySQL’s plan.')),
            dFlash(pFlash(), [[L('In InnoDB la tabella è ordinata per chiave primaria', 'In InnoDB the table is ordered by primary key'), true], [L('`EXPLAIN ANALYZE` non esegue la query', '`EXPLAIN ANALYZE` does not run the query'), false], [L('Un indice invisibile continua a essere aggiornato', 'An invisible index keeps being updated'), true], [L('Più indici rendono sempre più veloci gli `INSERT`', 'More indexes always make `INSERT`s faster'), false]], L('Misurare prima di indicizzare.', 'Measure before indexing.')),
        ]),
        lesson('backup', L('Backup e replica', 'Backups and replication'), [
            P('Il backup logico più semplice è **`mysqldump`**: scrive un file di istruzioni SQL che ricreano tabelle e dati. Con InnoDB, `--single-transaction` fa una copia coerente senza bloccare le scritture. Per database grandi ci sono le utility di dump di **MySQL Shell**, più veloci perché lavorano in parallelo.', 'The simplest logical backup is **`mysqldump`**: it writes a file of SQL statements that recreate tables and data. With InnoDB, `--single-transaction` takes a consistent copy without blocking writes. For big databases there are the dump utilities of **MySQL Shell**, faster because they work in parallel.'),
            C('# backup di un database
mysqldump -u root -p --single-transaction --routines negozio > negozio.sql

# ripristino
mysql -u root -p negozio < negozio.sql', 'bash'),
            P('Il **log binario** registra ogni modifica: serve per tornare a un istante preciso dopo il backup (*point-in-time recovery*) e per la **replica**, in cui uno o più server *replica* copiano le modifiche del server *source*. Le repliche alleggeriscono le letture e sono pronte a sostituire il source se si guasta.', 'The **binary log** records every change: it is used to return to a precise moment after the backup (*point-in-time recovery*) and for **replication**, where one or more *replica* servers copy the changes of the *source* server. Replicas take read load off and are ready to replace the source if it fails.'),
            Fig(L(fig_mysql_replica('scritture', 'letture', 'log binario'), fig_mysql_replica('writes', 'reads', 'binary log')),
                L('L’app scrive sul server source, che registra ogni modifica nel log binario. Due linee tratteggiate portano le modifiche a replica 1 e replica 2; le letture dell’app vanno direttamente alle repliche.', 'The app writes to the source server, which records every change in the binary log. Two dashed lines carry the changes to replica 1 and replica 2; the app’s reads go straight to the replicas.'),
                L('Le modifiche viaggiano dal source alle repliche con un piccolo ritardo.', 'Changes travel from the source to the replicas with a small delay.')),
            Tip('Un backup che non hai mai provato a ripristinare non è un backup: fai una prova di ripristino ogni tanto.', 'A backup you have never tried to restore is not a backup: do a test restore now and then.'),
        ], [
            Q('b1', L('Che cosa produce `mysqldump`?', 'What does `mysqldump` produce?'), [L('Una copia binaria dei file del server', 'A binary copy of the server’s files'), L('Un file di istruzioni SQL che ricreano i dati', 'A file of SQL statements that recreate the data'), L('Un foglio Excel con tutte le tabelle', 'An Excel sheet with every table'), L('Un nuovo server già configurato', 'A new, already configured server')], 1, L('Si ripristina passandolo al client `mysql`.', 'You restore it by feeding it to the `mysql` client.')),
            Q('b2', L('Perché usare `--single-transaction` con InnoDB?', 'Why use `--single-transaction` with InnoDB?'), [L('Per comprimere il file di backup', 'To compress the backup file'), L('Per copiare solo una tabella alla volta', 'To copy only one table at a time'), L('Per cifrare le password nel dump', 'To encrypt passwords in the dump'), L('Per una copia coerente senza bloccare le scritture', 'For a consistent copy without blocking writes')], 3, L('Legge tutto da una fotografia della transazione.', 'It reads everything from one transaction snapshot.')),
            Q('b3', L('A che cosa serve il log binario?', 'What is the binary log for?'), [L('Al ripristino a un istante preciso e alla replica', 'Point-in-time recovery and replication'), L('A salvare gli errori di sintassi', 'Saving syntax errors'), L('A tenere le password degli utenti', 'Keeping user passwords'), L('A velocizzare le query `SELECT`', 'Speeding up `SELECT` queries')], 0, L('Registra ogni modifica, in ordine.', 'It records every change, in order.')),
        ], task('bash', L('Scrivi il comando che fa il backup del database `scuola` nel file `scuola.sql` con `mysqldump`, utente `root` e `--single-transaction`.', 'Write the command that backs up the database `scuola` to the file `scuola.sql` with `mysqldump`, user `root` and `--single-transaction`.'), '', [re('mysqldump\b', L('Usi mysqldump', 'You use mysqldump')), re('-u\s*root|--user[= ]root', L('Come root', 'As root')), re('--single-transaction', L('Copia coerente', 'Consistent copy')), re('\bscuola\s*>\s*scuola\.sql', L('Salvi nel file', 'You save to the file'))], '', 'mysqldump -u root -p --single-transaction scuola > scuola.sql'), [
            dOrder(pSort(), ['mysqldump -u root -p --single-transaction negozio > negozio.sql', 'mysql -u root -p -e "CREATE DATABASE prova"', 'mysql -u root -p prova < negozio.sql', 'mysql -u root -p prova -e "SHOW TABLES"'], L('Backup, database di prova, ripristino, verifica.', 'Backup, test database, restore, check.')),
            dMatch(pMatch(), [['mysqldump', L('Backup logico in SQL', 'Logical backup in SQL')], ['--single-transaction', L('Copia coerente con InnoDB', 'Consistent copy with InnoDB')], [L('Log binario', 'Binary log'), L('Registro delle modifiche', 'Change log')], ['Source', L('Server che riceve le scritture', 'Server receiving writes')], ['Replica', L('Copia che segue il source', 'Copy following the source')]], L('Non perdere i dati.', 'Not losing data.')),
        ]),
        lesson('versions', L('Versioni e novità', 'Versions and what is new'), [
            P('MySQL esce in due binari: le versioni **LTS** (8.4, 9.7…), che per anni ricevono solo correzioni, e le versioni **Innovation**, con funzioni nuove ogni pochi mesi ma supporto breve. In produzione di solito si sceglie una LTS. MySQL 8.0 ha concluso il supporto nel 2026: chi la usa deve passare a una LTS più recente.', 'MySQL ships in two tracks: **LTS** versions (8.4, 9.7…), which get only fixes for years, and **Innovation** versions, with new features every few months but short support. In production you usually pick an LTS. MySQL 8.0 reached end of support in 2026: whoever still uses it should move to a newer LTS.'),
            C('SELECT VERSION();

-- dalla 8.0: CTE e funzioni finestra
WITH mensili AS (
    SELECT DATE_FORMAT(creato_il, \'%Y-%m\') AS mese, SUM(totale) AS incasso
    FROM ordini GROUP BY mese
)
SELECT mese, incasso, LAG(incasso) OVER (ORDER BY mese) AS mese_prima
FROM mensili;

-- dalla 9.0: il tipo VECTOR per gli embedding
CREATE TABLE documenti (id INT PRIMARY KEY, embedding VECTOR(384));', 'sql', fucina_data_table(['mese', 'incasso', 'mese_prima'], [['2026-08', 12400.0, null], ['2026-09', 13150.5, 12400.0], ['2026-10', 11980.0, 13150.5]], 'SELECT mese, incasso, LAG(incasso) OVER (ORDER BY mese) AS mese_prima …')),
            P('Le novità più utili arrivate negli anni: CTE, funzioni finestra e ruoli (8.0), autenticazione `caching_sha2_password` come predefinita (il vecchio `mysql_native_password` è stato tolto nella 9), il tipo `VECTOR` (9.0). Prima di aggiornare, leggi le note di rilascio e prova l’applicazione su una copia.', 'The most useful additions over the years: CTEs, window functions and roles (8.0), `caching_sha2_password` authentication as the default (the old `mysql_native_password` was removed in 9), the `VECTOR` type (9.0). Before upgrading, read the release notes and test the application on a copy.'),
        ], [
            Q('n1', L('Quale tipo di versione conviene di solito in produzione?', 'Which kind of version usually suits production?'), [L('L’ultima Innovation appena uscita', 'The latest Innovation just released'), L('Una versione di sviluppo', 'A development build'), L('Una LTS, con anni di correzioni', 'An LTS, with years of fixes'), L('La versione più vecchia disponibile', 'The oldest version available')], 2, L('Le Innovation hanno supporto breve.', 'Innovation releases have short support.')),
            Q('n2', L('Da quale versione MySQL ha CTE e funzioni finestra?', 'Since which version does MySQL have CTEs and window functions?'), ['5.7', '8.0', '9.0', '5.5'], 1, L('Prima servivano trucchi con variabili e sottoquery.', 'Before that you needed tricks with variables and subqueries.')),
            Q('n3', L('A che cosa serve il tipo `VECTOR`?', 'What is the `VECTOR` type for?'), [L('A salvare immagini in formato vettoriale', 'Storing images in vector format'), L('A salvare elenchi di testi separati da virgole', 'Storing comma-separated lists of text'), L('A salvare coordinate geografiche', 'Storing geographic coordinates'), L('A salvare gli embedding usati dall’IA', 'Storing the embeddings used by AI')], 3, L('Liste di numeri che rappresentano il significato di un testo.', 'Lists of numbers representing the meaning of a text.')),
        ], task('sql', L('Con una CTE `totali` che somma `importo` per `cliente_id` dalla tabella `pagamenti`, mostra i clienti con la loro posizione: `RANK() OVER (ORDER BY speso DESC) AS pos`.', 'With a CTE `totali` summing `importo` per `cliente_id` from the table `pagamenti`, show customers with their position: `RANK() OVER (ORDER BY speso DESC) AS pos`.'), '', [re('WITH\s+totali\s+AS\s*\(\s*SELECT\s+cliente_id\s*,\s*SUM\s*\(\s*importo\s*\)\s+(AS\s+)?speso\s+FROM\s+pagamenti\s+GROUP\s+BY\s+cliente_id\s*\)', L('La CTE somma per cliente', 'The CTE sums per customer')), re('RANK\s*\(\s*\)\s+OVER\s*\(\s*ORDER\s+BY\s+speso\s+DESC\s*\)\s+(AS\s+)?pos', L('La posizione in classifica', 'The ranking position')), re('FROM\s+totali', L('Usi la CTE', 'You use the CTE'))], '', "WITH totali AS (\n    SELECT cliente_id, SUM(importo) AS speso FROM pagamenti GROUP BY cliente_id\n)\nSELECT cliente_id, speso, RANK() OVER (ORDER BY speso DESC) AS pos\nFROM totali;"), [
            dMatch(pMatch(), [['LTS', L('Anni di sole correzioni', 'Years of fixes only')], ['Innovation', L('Novità frequenti, supporto breve', 'Frequent features, short support')], ['8.0', L('CTE e funzioni finestra', 'CTEs and window functions')], ['VECTOR', L('Embedding per l’IA', 'Embeddings for AI')], ['caching_sha2_password', L('Autenticazione predefinita', 'Default authentication')]], L('Le tappe di MySQL.', 'MySQL’s milestones.')),
            dFlash(pFlash(), [[L('Le versioni LTS ricevono correzioni per anni', 'LTS versions get fixes for years'), true], [L('MySQL 8.0 è ancora supportata senza scadenza', 'MySQL 8.0 is still supported with no end date'), false], [L('Prima di aggiornare conviene provare su una copia', 'Before upgrading it pays to test on a copy'), true], [L('`mysql_native_password` è il predefinito in MySQL 9', '`mysql_native_password` is the default in MySQL 9'), false]], L('Aggiornare con un piano.', 'Upgrading with a plan.')),
        ]),
    ],
];
