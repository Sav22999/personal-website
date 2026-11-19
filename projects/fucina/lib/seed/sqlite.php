<?php
/** SQLite: a whole database in one file, flexible types, PRAGMA, dates as text, using it from code, upserts, JSON and FTS5. */

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

/** A database with a server (two processes and the network) next to SQLite (a library inside the app and one file). */
if (!function_exists('fig_sqlite_embedded')) {
    function fig_sqlite_embedded(string $server, string $network, string $files, string $noteL, string $lib, string $noteR): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="20" y="14" width="290" height="246" rx="12" fill="#FFFFFF" stroke="#C9C7DE" stroke-width="2"/>
<rect x="330" y="14" width="290" height="246" rx="12" fill="#FFFFFF" stroke="#C9C7DE" stroke-width="2"/>
<text x="36" y="40" font-weight="700">{$server}</text><text x="346" y="40" font-weight="700">SQLite</text>
<rect x="36" y="66" width="90" height="48" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="81" y="95" text-anchor="middle" font-weight="700">app</text>
<path d="M126 90 H186" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/><path d="M184 84 L194 90 L184 96 Z" fill="#8A87B0"/>
<text x="160" y="80" text-anchor="middle" font-size="12" fill="#5A5880">{$network}</text>
<rect x="196" y="66" width="100" height="48" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="246" y="95" text-anchor="middle" font-weight="700" fill="#B84508">server</text>
<path d="M246 114 V148" stroke="#E8590C" stroke-width="2"/><path d="M240 146 L246 156 L252 146 Z" fill="#E8590C"/>
<path d="M206 166 v40 a40 10 0 0 0 80 0 v-40" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><ellipse cx="246" cy="166" rx="40" ry="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="246" y="200" text-anchor="middle" font-size="13">{$files}</text>
<text x="36" y="246" font-size="13" font-weight="700" fill="#5A5880">{$noteL}</text>
<rect x="346" y="62" width="150" height="100" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="362" y="86" font-weight="700">app</text>
<rect x="362" y="100" width="118" height="44" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="421" y="127" text-anchor="middle" font-size="13" font-weight="700" fill="#0B7F5C">{$lib}</text>
<path d="M496 112 H530" stroke="#0B7F5C" stroke-width="2"/><path d="M528 106 L538 112 L528 118 Z" fill="#0B7F5C"/>
<path d="M542 78 H588 L604 94 V150 H542 Z" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><path d="M588 78 V94 H604" fill="none" stroke="#2F8CF0" stroke-width="2"/>
<text x="573" y="172" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13" font-weight="700">note.db</text>
<text x="346" y="246" font-size="13" font-weight="700" fill="#5A5880">{$noteR}</text>
</svg>
SVG;
    }
}

/** WAL mode: writers append to the -wal file, readers read the database plus the log, a checkpoint copies the log back. */
if (!function_exists('fig_sqlite_wal')) {
    function fig_sqlite_wal(string $writer, string $readers): string
    {
        $slots = '';
        for ($i = 0; $i < 6; $i++) {
            $x = 300 + $i * 44;
            $slots .= '<rect' . ($i === 3 ? ' class="nw"' : '') . ' x="' . $x . '" y="104" width="38" height="44" rx="6" fill="' . ($i < 4 ? '#FFE8D6' : '#FFFFFF') . '" stroke="' . ($i < 4 ? '#E8590C' : '#C9C7DE') . '" stroke-width="2"/>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.nw{animation:nw 4s ease-in-out infinite}@keyframes nw{0%,20%{opacity:0}45%,100%{opacity:1}}@media (prefers-reduced-motion:reduce){.nw{animation:none}}</style>
<path d="M40 80 H172 L196 104 V196 H40 Z" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><path d="M172 80 V104 H196" fill="none" stroke="#2F8CF0" stroke-width="2"/>
<g fill="#FFFFFF" stroke="#2F8CF0" stroke-width="1.5"><rect x="56" y="116" width="36" height="26" rx="4"/><rect x="100" y="116" width="36" height="26" rx="4"/><rect x="144" y="116" width="36" height="26" rx="4"/><rect x="56" y="152" width="36" height="26" rx="4"/><rect x="100" y="152" width="36" height="26" rx="4"/><rect x="144" y="152" width="36" height="26" rx="4"/></g>
<text x="118" y="218" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13" font-weight="700">note.db</text>
<rect x="290" y="94" width="282" height="64" rx="10" fill="none" stroke="#E8590C" stroke-width="2"/>
{$slots}
<text x="431" y="180" text-anchor="middle" font-family="ui-monospace, monospace" font-size="13" font-weight="700">note.db-wal</text>
<rect x="400" y="16" width="140" height="38" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="470" y="40" text-anchor="middle" font-weight="700" fill="#B84508">{$writer}</text>
<path d="M451 54 V94" stroke="#E8590C" stroke-width="2"/><path d="M445 92 L451 102 L457 92 Z" fill="#E8590C"/>
<path d="M290 112 C 250 112, 236 70, 196 92" fill="none" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4"/><path d="M204 84 L194 94 L208 96 Z" fill="#7048E8"/>
<text x="246" y="66" text-anchor="middle" font-family="ui-monospace, monospace" font-size="12" font-weight="700" fill="#4B2BBF">checkpoint</text>
<rect x="250" y="206" width="140" height="38" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="320" y="230" text-anchor="middle" font-weight="700" fill="#1B66BC">{$readers}</text>
<path d="M250 225 C 220 225, 210 206, 196 190" fill="none" stroke="#2F8CF0" stroke-width="2"/><path d="M193 199 L194 186 L204 194 Z" fill="#2F8CF0"/>
<path d="M390 225 C 420 225, 431 210, 431 190" fill="none" stroke="#2F8CF0" stroke-width="2"/><path d="M425 194 L431 184 L437 194 Z" fill="#2F8CF0"/>
</svg>
SVG;
    }
}

/** The inverted index behind FTS5: each word lists the documents that contain it. */
if (!function_exists('fig_sqlite_fts')) {
    function fig_sqlite_fts(string $docs, string $index, string $note): string
    {
        $d = '';
        foreach ([[1, 'Comprare pane, latte e uova'], [2, 'Latte di mandorla'], [3, 'Pane integrale']] as $i => [$n, $t]) {
            $y = 52 + $i * 70;
            $hot = $n !== 2;
            $d .= '<rect x="20" y="' . $y . '" width="250" height="50" rx="10" fill="' . ($hot ? '#FFF3EA' : '#FFFFFF') . '" stroke="' . ($hot ? '#E8590C' : '#C9C7DE') . '" stroke-width="2"/><text x="36" y="' . ($y + 31) . '" font-weight="700">' . $n . '</text><text x="58" y="' . ($y + 31) . '">' . $t . '</text>';
        }
        $r = '';
        foreach ([['comprare', '1'], ['di', '2'], ['e', '1'], ['integrale', '3'], ['latte', '1, 2'], ['mandorla', '2'], ['pane', '1, 3'], ['uova', '1']] as $i => [$w, $l]) {
            $y = 64 + $i * 26;
            if ($w === 'pane') $r .= '<rect x="372" y="' . ($y - 18) . '" width="236" height="26" rx="6" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>';
            $r .= '<text x="386" y="' . $y . '" font-family="ui-monospace, monospace" font-size="13">' . $w . '</text><text x="594" y="' . $y . '" text-anchor="end" font-weight="700">' . $l . '</text>';
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-weight="700"><text x="20" y="34">{$docs}</text><text x="364" y="30">{$index}</text></g>
{$d}
<rect x="364" y="40" width="252" height="218" rx="12" fill="none" stroke="#2F8CF0" stroke-width="2"/>
{$r}
<g fill="none" stroke="#E8590C" stroke-width="2"><path d="M372 215 C 320 215, 320 77, 274 77"/><path d="M372 215 C 320 215, 320 217, 274 217"/></g>
<text x="20" y="280" font-size="13" font-weight="700" fill="#B84508">{$note}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'sqlite', 'family' => 'sqlite', 'after' => 'sql-base', 'categories' => ['data', 'mobile', 'desktop'],
    'title' => L('SQLite', 'SQLite'), 'short' => L('SQLite', 'SQLite'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database in un file, dentro telefoni, browser e app: shell `sqlite3`, tipi flessibili e tabelle `STRICT`, `PRAGMA`, date, uso da Python, upsert, JSON e ricerca full-text FTS5.', 'The database in a file, inside phones, browsers and apps: the `sqlite3` shell, flexible types and `STRICT` tables, `PRAGMA`, dates, use from Python, upserts, JSON and FTS5 full-text search.'),
    'lessons' => [
        lesson('file', L('Un database in un file', 'A database in a file'), [
            P('SQLite non ha un server: è una libreria che legge e scrive **un solo file**. Niente installazione, utenti o porte. È il database più diffuso al mondo: è dentro Android, iOS, i browser e moltissime app desktop.', 'SQLite has no server: it is a library that reads and writes **a single file**. No installation, users or ports. It is the most widely deployed database in the world: it lives inside Android, iOS, browsers and countless desktop apps.'),
            Fig(L(fig_sqlite_embedded('Database con server', 'rete', 'file', 'Due programmi, la rete in mezzo.', 'libreria sqlite', 'Un programma, un file.'), fig_sqlite_embedded('Server database', 'network', 'files', 'Two programs, the network between.', 'sqlite library', 'One program, one file.')),
                L('A sinistra un’app parla attraverso la rete con un server, che a sua volta legge i suoi file. A destra la libreria SQLite sta dentro l’app e legge direttamente un solo file, note.db.', 'On the left an app talks over the network to a server, which in turn reads its files. On the right the SQLite library sits inside the app and reads a single file, note.db, directly.'),
                L('Copiare il database vuol dire copiare un file.', 'Copying the database means copying a file.')),
            C('sqlite3 note.db

.tables
.schema note
.mode table
SELECT * FROM note;
.quit', 'sql'),
            P('I comandi che iniziano con il punto (`.tables`, `.schema`, `.mode`, `.import`) sono della shell `sqlite3`, non SQL. Se il file non esiste, viene creato alla prima scrittura.', 'Commands starting with a dot (`.tables`, `.schema`, `.mode`, `.import`) belong to the `sqlite3` shell, not SQL. If the file does not exist, it is created on the first write.'),
            Tip('SQLite è perfetto per app locali, prototipi e siti con poche scritture contemporanee; per molti utenti che scrivono insieme serve un database con server.', 'SQLite is perfect for local apps, prototypes and sites with few concurrent writes; for many users writing at once you need a server database.'),
        ], [
            Q('f1', L('Dove tiene i dati SQLite?', 'Where does SQLite keep its data?'), [L('In un unico file', 'In a single file'), L('In un server remoto', 'On a remote server'), L('Solo in memoria', 'Only in memory'), L('In un file per tabella', 'In one file per table')], 0, L('Copi il file e hai copiato il database.', 'Copy the file and you have copied the database.')),
            Q('f2', L('Che cosa sono i comandi come `.tables`?', 'What are commands like `.tables`?'), [L('Comandi della shell sqlite3', 'sqlite3 shell commands'), L('Istruzioni SQL standard', 'Standard SQL statements'), L('Errori di battitura', 'Typos'), L('Funzioni di Python', 'Python functions')], 0, L('Non funzionano da un programma.', 'They do not work from a program.')),
            Q('f3', L('Quando SQLite non è la scelta giusta?', 'When is SQLite not the right choice?'), [L('Con molti utenti che scrivono insieme', 'With many users writing at once'), L('In un’app mobile', 'In a mobile app'), L('Per un prototipo', 'For a prototype'), L('Per i dati di un’app desktop', 'For a desktop app’s data')], 0, L('Le scritture sono una alla volta.', 'Writes happen one at a time.')),
        ], task('sql', L('Crea la tabella `note` con `id INTEGER PRIMARY KEY` e `testo TEXT NOT NULL`.', 'Create the table `note` with `id INTEGER PRIMARY KEY` and `testo TEXT NOT NULL`.'), '', [re('CREATE\s+TABLE\s+note', L('La tabella esiste', 'The table exists')), re('id\s+INTEGER\s+PRIMARY\s+KEY', L('L’id è la chiave', 'The id is the key')), re('testo\s+TEXT\s+NOT\s+NULL', L('Il testo è obbligatorio', 'The text is required'))], '', "CREATE TABLE note (\n    id INTEGER PRIMARY KEY,\n    testo TEXT NOT NULL\n);"), [
            dMatch(pMatch(), [['.tables', L('Elenca le tabelle', 'Lists tables')], ['.schema', L('Mostra i CREATE', 'Shows the CREATE statements')], ['.mode table', L('Risultati in tabella', 'Results as a table')], ['.import', L('Carica un CSV', 'Loads a CSV')], ['.quit', L('Esce', 'Quits')]], L('La shell sqlite3.', 'The sqlite3 shell.')),
            dFlash(pFlash(), [[L('SQLite ha bisogno di un server', 'SQLite needs a server'), false], [L('SQLite è dentro Android e iOS', 'SQLite is inside Android and iOS'), true], [L('Il database è un solo file', 'The database is a single file'), true], [L('`.tables` funziona anche da Python', '`.tables` works from Python too'), false]], L('Piccolo, ovunque.', 'Small, everywhere.')),
        ]),
        lesson('types', L('Tipi flessibili e STRICT', 'Flexible types and STRICT'), [
            P('SQLite ha solo cinque classi di valore: `NULL`, `INTEGER`, `REAL`, `TEXT` e `BLOB`. Il tipo di una colonna è solo una **preferenza** (affinità): di base puoi salvare un testo in una colonna `INTEGER`.', 'SQLite has only five storage classes: `NULL`, `INTEGER`, `REAL`, `TEXT` and `BLOB`. A column’s type is only a **preference** (affinity): by default you can store text in an `INTEGER` column.'),
            C('CREATE TABLE prova (n INTEGER);
INSERT INTO prova VALUES (\'ciao\');   -- accettato!
SELECT n, typeof(n) FROM prova;      -- ciao | text

CREATE TABLE prezzi (
    id INTEGER PRIMARY KEY,
    euro REAL NOT NULL
) STRICT;', 'sql', fucina_data_table(['n', 'typeof(n)'], [['ciao', 'text']], 'SELECT n, typeof(n) FROM prova;')),
            P('Con **`STRICT`** in fondo al `CREATE TABLE` i tipi diventano obbligatori, come negli altri database. Una colonna `INTEGER PRIMARY KEY` coincide con il `rowid` interno e si numera da sola.', 'With **`STRICT`** at the end of `CREATE TABLE` types become mandatory, as in other databases. An `INTEGER PRIMARY KEY` column is the internal `rowid` and numbers itself.'),
            P('L’affinità prova comunque a convertire: il testo `\'42\'` in una colonna `INTEGER` diventa il numero 42, mentre `\'ciao\'` resta testo perché non sembra un numero. Il rischio è scoprire tardi una colonna piena di valori misti, che si ordinano e confrontano in modo strano. Per le tabelle nuove `STRICT` è la scelta più sicura: un valore sbagliato dà subito errore.', 'Affinity still tries to convert: the text `\'42\'` in an `INTEGER` column becomes the number 42, while `\'ciao\'` stays text because it does not look like a number. The risk is finding out late about a column full of mixed values, which sort and compare oddly. For new tables `STRICT` is the safest choice: a wrong value errors out straight away.'),
        ], [
            Q('y1', L('Che cosa succede inserendo `\'ciao\'` in una colonna `INTEGER` normale?', 'What happens when inserting `\'ciao\'` into a normal `INTEGER` column?'), [L('Viene accettato come testo', 'It is accepted as text'), L('Errore', 'Error'), L('Diventa 0', 'It becomes 0'), L('Diventa NULL', 'It becomes NULL')], 0, L('Il tipo è solo un’affinità.', 'The type is only an affinity.')),
            Q('y2', L('Che cosa fa `STRICT`?', 'What does `STRICT` do?'), [L('Rende obbligatori i tipi delle colonne', 'Makes column types mandatory'), L('Blocca le scritture', 'Blocks writes'), L('Cifra il file', 'Encrypts the file'), L('Vieta i NULL ovunque', 'Forbids NULL everywhere')], 0, L('Va in fondo al `CREATE TABLE`.', 'It goes at the end of `CREATE TABLE`.')),
            Q('y3', L('Che cos’è una colonna `INTEGER PRIMARY KEY`?', 'What is an `INTEGER PRIMARY KEY` column?'), [L('Un alias del `rowid`, si numera da sola', 'An alias of `rowid`, it numbers itself'), L('Una colonna di testo', 'A text column'), L('Una chiave esterna', 'A foreign key'), L('Un indice full text', 'A full-text index')], 0, L('Non serve `AUTOINCREMENT`.', 'No `AUTOINCREMENT` needed.')),
        ], task('sql', L('Crea la tabella `misure (id INTEGER PRIMARY KEY, valore REAL NOT NULL)` in modalità `STRICT`.', 'Create the table `misure (id INTEGER PRIMARY KEY, valore REAL NOT NULL)` in `STRICT` mode.'), '', [re('CREATE\s+TABLE\s+misure', L('La tabella esiste', 'The table exists')), re('valore\s+REAL\s+NOT\s+NULL', L('Il valore è un numero obbligatorio', 'The value is a required number')), re('\)\s*STRICT\s*;', L('I tipi sono rigidi', 'Types are strict'))], '', "CREATE TABLE misure (\n    id INTEGER PRIMARY KEY,\n    valore REAL NOT NULL\n) STRICT;"), [
            dMatch(pMatch(), [['INTEGER', L('Numero intero', 'Whole number')], ['REAL', L('Numero con la virgola', 'Floating-point number')], ['TEXT', L('Testo', 'Text')], ['BLOB', L('Dati binari', 'Binary data')], ['NULL', L('Nessun valore', 'No value')]], L('Le cinque classi di SQLite.', 'SQLite’s five storage classes.')),
            dBug(pBug(), ['CREATE TABLE STRICT voti (', '    id INTEGER PRIMARY KEY,', '    voto INTEGER', ');'], 0, 'CREATE TABLE voti (', L('`STRICT` va in fondo: `) STRICT;`.', '`STRICT` goes at the end: `) STRICT;`.')),
        ]),
        lesson('pragma', L('PRAGMA', 'PRAGMA'), [
            P('Le **PRAGMA** regolano il comportamento di SQLite. La più importante: le chiavi esterne sono **spente** di base e vanno accese a ogni connessione con `PRAGMA foreign_keys = ON`.', '**PRAGMA** statements tune SQLite’s behaviour. The most important: foreign keys are **off** by default and must be turned on at every connection with `PRAGMA foreign_keys = ON`.'),
            C('PRAGMA foreign_keys = ON;
PRAGMA journal_mode = WAL;
PRAGMA integrity_check;
PRAGMA table_info(note);', 'sql'),
            Fig(L(fig_sqlite_wal('scrittura', 'lettori'), fig_sqlite_wal('writer', 'readers')),
                L('A sinistra il file note.db con le sue pagine. A destra il file note.db-wal, una fila di caselle: chi scrive aggiunge pagine in fondo. I lettori leggono sia note.db sia il log; una freccia checkpoint riporta di tanto in tanto le pagine dal log al database.', 'On the left the note.db file with its pages. On the right the note.db-wal file, a row of slots: the writer appends pages at the end. Readers read both note.db and the log; a checkpoint arrow now and then carries the pages from the log back into the database.'),
                L('Chi scrive non tocca il file principale, quindi chi legge non deve aspettare.', 'The writer does not touch the main file, so readers do not have to wait.')),
            P('La modalità **WAL** (write-ahead log) permette di leggere mentre qualcuno scrive: quasi sempre conviene. Accanto al database compaiono allora i file `-wal` e `-shm`: fanno parte del database, non cancellarli. `integrity_check` verifica che il file non sia rovinato, `table_info` elenca le colonne.', '**WAL** mode (write-ahead log) lets readers read while someone writes: almost always worth it. The files `-wal` and `-shm` then appear next to the database: they are part of it, do not delete them. `integrity_check` verifies the file is not corrupted, `table_info` lists the columns.'),
        ], [
            Q('g1', L('Le chiavi esterne sono attive di base in SQLite?', 'Are foreign keys on by default in SQLite?'), [L('No, vanno accese', 'No, they must be turned on'), L('Sì, sempre', 'Yes, always'), L('Solo nei file .db', 'Only in .db files'), L('Solo con STRICT', 'Only with STRICT')], 0, L('`PRAGMA foreign_keys = ON` a ogni connessione.', '`PRAGMA foreign_keys = ON` at every connection.')),
            Q('g2', L('Che vantaggio dà `journal_mode = WAL`?', 'What does `journal_mode = WAL` give you?'), [L('Letture durante le scritture', 'Reads during writes'), L('Cifratura', 'Encryption'), L('File più piccoli sempre', 'Always smaller files'), L('Più utenti con password', 'More users with passwords')], 0, L('Lettori e scrittore non si bloccano.', 'Readers and writer do not block each other.')),
            Q('g3', L('Quale PRAGMA elenca le colonne di una tabella?', 'Which PRAGMA lists a table’s columns?'), ['table_info', 'integrity_check', 'foreign_keys', 'journal_mode'], 0, L('Come un `DESCRIBE`.', 'Like a `DESCRIBE`.')),
        ], task('sql', L('Accendi le chiavi esterne e passa alla modalità WAL.', 'Turn on foreign keys and switch to WAL mode.'), '', [re('PRAGMA\s+foreign_keys\s*=\s*(ON|1)', L('Chiavi esterne accese', 'Foreign keys on')), re('PRAGMA\s+journal_mode\s*=\s*WAL', L('Modalità WAL', 'WAL mode'))], '', "PRAGMA foreign_keys = ON;\nPRAGMA journal_mode = WAL;"), [
            dBlanks(pFill(), "PRAGMA ___ = ON;\nPRAGMA journal_mode = ___;", ['foreign_keys', 'WAL'], ['keys', 'FAST', 'strict'], L('Le due PRAGMA da mettere quasi sempre.', 'The two PRAGMAs to set almost always.')),
            dFlash(pFlash(), [[L('Le chiavi esterne si accendono una volta per sempre', 'Foreign keys are turned on once and for all'), false], [L('WAL permette letture durante le scritture', 'WAL allows reads during writes'), true], [L('`integrity_check` controlla il file', '`integrity_check` checks the file'), true], [L('Le PRAGMA sono comandi della shell con il punto', 'PRAGMAs are dot commands of the shell'), false]], L('Le PRAGMA sono SQL, valgono anche dal codice.', 'PRAGMAs are SQL, they work from code too.')),
        ]),
        lesson('dates', L('Date e ore', 'Dates and times'), [
            P('SQLite non ha un tipo data: le date si salvano come **testo ISO** (`2026-11-10 18:30:00`), come numero Unix o come giorno giuliano. Le funzioni `date()`, `time()`, `datetime()` e `strftime()` le leggono e le trasformano.', 'SQLite has no date type: dates are stored as **ISO text** (`2026-11-10 18:30:00`), as a Unix number or as a Julian day. The functions `date()`, `time()`, `datetime()` and `strftime()` read and transform them.'),
            C('SELECT date(\'now\');                      -- 2026-11-10
SELECT datetime(\'now\', \'+7 days\');
SELECT strftime(\'%Y\', creata) AS anno, COUNT(*)
FROM note GROUP BY anno;

SELECT * FROM note WHERE creata >= date(\'now\', \'-30 days\');', 'sql', fucina_data_table(['anno', 'COUNT(*)'], [['2024', 37], ['2025', 112], ['2026', 85]], 'SELECT strftime(\'%Y\', creata) AS anno, COUNT(*) FROM note GROUP BY anno;')),
            P('I modificatori (`+7 days`, `-1 month`, `start of month`) spostano la data. Il formato ISO si ordina e si confronta bene anche come testo.', 'Modifiers (`+7 days`, `-1 month`, `start of month`) move the date. The ISO format sorts and compares well even as text.'),
            P('Proprio perché sono testo, conta essere coerenti: scegli un formato e usalo per tutte le righe. Se alcune date sono `2026-11-10` e altre `10/11/2026`, i confronti e l’ordinamento danno risultati sbagliati senza nessun errore. Attenzione anche al fuso: `\'now\'` è in UTC; per l’ora locale aggiungi il modificatore `\'localtime\'`, ma salva sempre in UTC.', 'Precisely because they are text, consistency matters: pick one format and use it for every row. If some dates are `2026-11-10` and others `10/11/2026`, comparisons and sorting give wrong results without any error. Mind the time zone too: `\'now\'` is in UTC; for local time add the `\'localtime\'` modifier, but always store in UTC.'),
        ], [
            Q('d1', L('Come si salvano di solito le date in SQLite?', 'How are dates usually stored in SQLite?'), [L('Come testo ISO', 'As ISO text'), L('Con il tipo DATE nativo', 'With a native DATE type'), L('Come immagini', 'As images'), L('Non si possono salvare', 'They cannot be stored')], 0, L('Il formato ISO si ordina bene.', 'The ISO format sorts well.')),
            Q('d2', L('Che cosa restituisce `datetime(\'now\', \'+7 days\')`?', 'What does `datetime(\'now\', \'+7 days\')` return?'), [L('Tra una settimana', 'One week from now'), L('Una settimana fa', 'One week ago'), L('Il giorno della settimana', 'The day of the week'), L('Sette secondi dopo', 'Seven seconds later')], 0, L('Il modificatore sposta la data.', 'The modifier moves the date.')),
            Q('d3', L('Quale funzione estrae l’anno?', 'Which function extracts the year?'), ['strftime(\'%Y\', d)', 'year(d)', 'extract(d)', 'date_year(d)'], 0, L('Con i codici di formato come `%Y`.', 'With format codes like `%Y`.')),
        ], task('sql', L('Dalla tabella `ordini` seleziona tutto quello creato (`creato`) negli ultimi 7 giorni, con `date(\'now\', \'-7 days\')`.', 'From the table `ordini` select everything created (`creato`) in the last 7 days, with `date(\'now\', \'-7 days\')`.'), '', [re('SELECT\s+\*\s+FROM\s+ordini', L('Leggi gli ordini', 'You read the orders')), re('creato\s*>=?\s*date\s*\(\s*\'now\'\s*,\s*\'-7 days\'\s*\)', L('Filtri sugli ultimi 7 giorni', 'You filter the last 7 days'))], '', "SELECT * FROM ordini WHERE creato >= date('now', '-7 days');"), [
            dMatch(pMatch(), [['date(\'now\')', L('La data di oggi', 'Today’s date')], ['\'+1 day\'', L('Domani', 'Tomorrow')], ['\'start of month\'', L('Inizio del mese', 'Start of the month')], ['%Y', L('L’anno', 'The year')], ['%m', L('Il mese', 'The month')]], L('Date come testo, ma con gli strumenti giusti.', 'Dates as text, but with the right tools.')),
            dBug(pBug(), ['SELECT titolo FROM note', 'WHERE creata >= DATE_SUB(NOW(), INTERVAL 30 DAY);'], 1, 'WHERE creata >= date(\'now\', \'-30 days\');', L('`DATE_SUB` e `NOW()` sono di MySQL.', '`DATE_SUB` and `NOW()` are MySQL.')),
        ]),
        lesson('code', L('SQLite dal codice', 'SQLite from code'), [
            P('Python include SQLite nella libreria standard (`sqlite3`). I valori si passano sempre con i **segnaposto** `?`, mai incollandoli nella stringa: così eviti l’SQL injection.', 'Python ships SQLite in the standard library (`sqlite3`). Values are always passed with `?` **placeholders**, never pasted into the string: that is how you avoid SQL injection.'),
            C('import sqlite3

con = sqlite3.connect("note.db")
con.execute("PRAGMA foreign_keys = ON")
with con:  # transazione: commit alla fine, rollback se c\'è un errore
    con.execute("INSERT INTO note (testo) VALUES (?)", ("Comprare il pane",))
for riga in con.execute("SELECT id, testo FROM note WHERE id > ?", (10,)):
    print(riga)
con.close()', 'python', '<pre style="margin:8px;font:13px ui-monospace,monospace;color:#1E1C52">(11, \'Comprare il pane\')
(12, \'Chiamare Luca\')</pre>'),
            P('Il blocco `with con:` apre una transazione: tutto va a buon fine insieme, oppure niente. Per un backup a caldo usa `con.backup(...)` o `VACUUM INTO \'copia.db\'`.', 'The `with con:` block opens a transaction: everything succeeds together, or nothing does. For a live backup use `con.backup(...)` or `VACUUM INTO \'copy.db\'`.'),
            P('Perché il segnaposto è così importante? Se scrivi `f"... WHERE nome = \'{nome}\'"` e qualcuno inserisce come nome `x\' OR \'1\'=\'1`, la condizione diventa sempre vera e la query restituisce tutte le righe. Con `?` il valore viaggia separato dal comando e resta solo un dato, qualunque cosa contenga. Il secondo argomento è sempre una tupla: con un solo valore serve la virgola, `(10,)`.', 'Why does the placeholder matter so much? If you write `f"... WHERE nome = \'{nome}\'"` and someone types `x\' OR \'1\'=\'1` as the name, the condition becomes always true and the query returns every row. With `?` the value travels apart from the command and stays just data, whatever it contains. The second argument is always a tuple: with a single value you need the comma, `(10,)`.'),
        ], [
            Q('c1', L('Come si passano i valori a una query?', 'How do you pass values to a query?'), [L('Con i segnaposto `?`', 'With `?` placeholders'), L('Concatenando le stringhe', 'By concatenating strings'), L('Con le f-string', 'With f-strings'), L('Con `%` sul testo', 'With `%` on the text')], 0, L('Così non c’è SQL injection.', 'That way there is no SQL injection.')),
            Q('c2', L('Che cosa fa `with con:`?', 'What does `with con:` do?'), [L('Apre una transazione', 'Opens a transaction'), L('Chiude il file', 'Closes the file'), L('Crea una tabella', 'Creates a table'), L('Accende WAL', 'Turns on WAL')], 0, L('Commit alla fine o rollback in caso di errore.', 'Commit at the end or rollback on error.')),
            Q('c3', L('Come fai una copia del database senza fermare l’app?', 'How do you copy the database without stopping the app?'), [L('`VACUUM INTO \'copia.db\'`', '`VACUUM INTO \'copy.db\'`'), L('`DROP DATABASE`', '`DROP DATABASE`'), L('`.quit`', '`.quit`'), L('`PRAGMA foreign_keys`', '`PRAGMA foreign_keys`')], 0, L('Oppure `con.backup()`.', 'Or `con.backup()`.')),
        ], task('python', L('Inserisci in `note (testo)` il valore della variabile `testo` usando un segnaposto `?`, dentro un blocco `with con:`.', 'Insert into `note (testo)` the value of the variable `testo` using a `?` placeholder, inside a `with con:` block.'), "import sqlite3\ncon = sqlite3.connect(\"note.db\")\ntesto = \"Ciao\"", [re('with\s+con\s*:', L('Usi una transazione', 'You use a transaction')), re('execute\s*\(\s*["\']INSERT\s+INTO\s+note\s*\(\s*testo\s*\)\s*VALUES\s*\(\s*\?\s*\)["\']\s*,\s*\(\s*testo\s*,\s*\)', L('Passi il valore con il segnaposto', 'You pass the value with the placeholder'))], '', "with con:\n    con.execute(\"INSERT INTO note (testo) VALUES (?)\", (testo,))"), [
            dBug(pBug(), ['nome = input("Nome: ")', 'con.execute(f"SELECT * FROM utenti WHERE nome = \'{nome}\'")'], 1, 'con.execute("SELECT * FROM utenti WHERE nome = ?", (nome,))', L('Mai il valore dentro la stringa: segnaposto.', 'Never the value inside the string: placeholder.')),
            dOrder(pSort(), ['import sqlite3', 'con = sqlite3.connect("app.db")', 'with con:', '    con.execute("INSERT INTO log (msg) VALUES (?)", ("avvio",))', 'con.close()'], L('Importa, collega, scrivi, chiudi.', 'Import, connect, write, close.')),
        ]),
        lesson('upsert', L('UPSERT e RETURNING', 'UPSERT and RETURNING'), [
            P('SQLite usa la sintassi di PostgreSQL per l’**upsert**: `ON CONFLICT (colonna) DO UPDATE` aggiorna la riga se il vincolo unico scatta, `DO NOTHING` la salta. `excluded` è la riga che si voleva inserire. Con **`RETURNING`** (dalla 3.35) ti fai restituire le righe toccate.', 'SQLite uses PostgreSQL’s syntax for **upserts**: `ON CONFLICT (column) DO UPDATE` updates the row if the unique constraint fires, `DO NOTHING` skips it. `excluded` is the row you tried to insert. With **`RETURNING`** (since 3.35) you get the touched rows back.'),
            C('CREATE TABLE contatori (pagina TEXT PRIMARY KEY, visite INTEGER NOT NULL);

INSERT INTO contatori (pagina, visite) VALUES (\'/home\', 1)
ON CONFLICT (pagina) DO UPDATE SET visite = visite + 1
RETURNING visite;

INSERT INTO tag (nome) VALUES (\'sql\') ON CONFLICT DO NOTHING;', 'sql', fucina_data_table(['visite'], [[2]], '-- seconda esecuzione · second run')),
            P('C’è anche la vecchia forma `INSERT OR REPLACE`, ma attenzione: come il `REPLACE` di MySQL **cancella** la riga e ne inserisce una nuova, perdendo le colonne non indicate. `INSERT OR IGNORE` salta i doppioni come `DO NOTHING`.', 'There is also the old `INSERT OR REPLACE` form, but careful: like MySQL’s `REPLACE` it **deletes** the row and inserts a new one, losing the columns you did not give. `INSERT OR IGNORE` skips duplicates like `DO NOTHING`.'),
        ], [
            Q('u1', L('Che cosa rappresenta `excluded` in un upsert?', 'What does `excluded` stand for in an upsert?'), [L('La riga già presente nella tabella', 'The row already in the table'), L('Le colonne escluse dall’indice', 'The columns left out of the index'), L('La riga che si voleva inserire', 'The row you tried to insert'), L('Le righe cancellate dal conflitto', 'The rows deleted by the conflict')], 2, L('Da lì prendi i valori nuovi.', 'That is where you take the new values.')),
            Q('u2', L('Perché `INSERT OR REPLACE` può essere pericoloso?', 'Why can `INSERT OR REPLACE` be dangerous?'), [L('Cancella e reinserisce la riga, perdendo le colonne non indicate', 'It deletes and reinserts the row, losing the columns not given'), L('Funziona solo sulle tabelle `STRICT`', 'It only works on `STRICT` tables'), L('Blocca il file per sempre', 'It locks the file forever'), L('Duplica sempre la riga', 'It always duplicates the row')], 0, L('Meglio `ON CONFLICT … DO UPDATE`.', 'Better `ON CONFLICT … DO UPDATE`.')),
            Q('u3', L('Da quale versione SQLite supporta `RETURNING`?', 'Since which version does SQLite support `RETURNING`?'), ['3.0', '2.8', '3.7', '3.35'], 3, L('Uscita nel 2021.', 'Released in 2021.')),
        ], task('sql', L('Inserisci in `punteggi (utente, punti)` i valori `(\'ada\', 10)`; se `utente` c’è già, somma i punti nuovi con `excluded.punti`.', 'Insert into `punteggi (utente, punti)` the values `(\'ada\', 10)`; if `utente` already exists, add the new points with `excluded.punti`.'), '', [re('INSERT\s+INTO\s+punteggi\s*\(\s*utente\s*,\s*punti\s*\)\s*VALUES\s*\(\s*\'ada\'\s*,\s*10\s*\)', L('Inserisci la riga', 'You insert the row')), re('ON\s+CONFLICT\s*\(\s*utente\s*\)\s*DO\s+UPDATE\s+SET\s+punti\s*=\s*(punti\s*\+\s*excluded\.punti|excluded\.punti\s*\+\s*punti)', L('Sommi i punti in caso di conflitto', 'You add the points on conflict'))], '', "INSERT INTO punteggi (utente, punti) VALUES ('ada', 10)\nON CONFLICT (utente) DO UPDATE SET punti = punti + excluded.punti;"), [
            dBlanks(pFill(), "INSERT INTO prezzi (codice, euro) VALUES ('A1', 9.5)\nON ___ (codice) DO ___ SET euro = ___.euro\nRETURNING euro;", ['CONFLICT', 'UPDATE', 'excluded'], ['DUPLICATE', 'REPLACE', 'new'], L('La sintassi dell’upsert.', 'The upsert syntax.')),
            dMatch(pMatch(), [['ON CONFLICT DO UPDATE', L('Inserisce o aggiorna', 'Inserts or updates')], ['ON CONFLICT DO NOTHING', L('Salta i doppioni', 'Skips duplicates')], ['INSERT OR REPLACE', L('Cancella e reinserisce', 'Deletes and reinserts')], ['RETURNING', L('Restituisce le righe toccate', 'Returns the touched rows')]], L('Gestire i doppioni in SQLite.', 'Handling duplicates in SQLite.')),
        ]),
        lesson('json', L('JSON in SQLite', 'JSON in SQLite'), [
            P('SQLite non ha un tipo JSON dedicato: il JSON si salva come testo e si legge con le **funzioni JSON**, incluse di base. Dalla 3.38 ci sono anche gli operatori `->` (restituisce JSON) e `->>` (restituisce un valore SQL), con percorsi che partono da `$`.', 'SQLite has no dedicated JSON type: JSON is stored as text and read with the built-in **JSON functions**. Since 3.38 there are also the `->` (returns JSON) and `->>` (returns an SQL value) operators, with paths starting from `$`.'),
            C('CREATE TABLE eventi (id INTEGER PRIMARY KEY, dati TEXT);
INSERT INTO eventi (dati) VALUES (\'{"tipo": "login", "tag": ["web", "mobile"]}\');

SELECT dati ->> \'$.tipo\' AS tipo FROM eventi;
SELECT json_extract(dati, \'$.tag[0]\') FROM eventi;   -- web

-- un elemento dell’array per riga
SELECT e.id, t.value AS tag
FROM eventi e, json_each(e.dati, \'$.tag\') AS t;', 'sql', fucina_data_table(['id', 'tag'], [[1, 'web'], [1, 'mobile']], 'SELECT e.id, t.value AS tag FROM eventi e, json_each(…) AS t;')),
            P('`json_each` trasforma un array in righe, utile per filtrare o contare gli elementi. `json_valid` controlla che il testo sia JSON corretto: mettilo in un `CHECK` per non salvare spazzatura. Dalla 3.45 esiste anche **JSONB**, un formato binario più veloce da leggere.', '`json_each` turns an array into rows, handy for filtering or counting elements. `json_valid` checks that the text is valid JSON: put it in a `CHECK` so you do not store junk. Since 3.45 there is also **JSONB**, a binary format that is faster to read.'),
        ], [
            Q('j1', L('Come salva il JSON SQLite?', 'How does SQLite store JSON?'), [L('In un tipo JSON nativo con indici dedicati', 'In a native JSON type with dedicated indexes'), L('Come testo, letto dalle funzioni JSON', 'As text, read by the JSON functions'), L('In un file separato per documento', 'In a separate file per document'), L('Non può salvarlo', 'It cannot store it')], 1, L('Le funzioni JSON sono incluse di base.', 'The JSON functions are built in.')),
            Q('j2', L('Che cosa fa `json_each`?', 'What does `json_each` do?'), [L('Controlla che il JSON sia valido', 'Checks that the JSON is valid'), L('Unisce più documenti in uno', 'Merges several documents into one'), L('Comprime il JSON in binario', 'Compresses the JSON into binary'), L('Trasforma gli elementi di un array in righe', 'Turns an array’s elements into rows')], 3, L('Si usa nel `FROM`, come una tabella.', 'It is used in the `FROM`, like a table.')),
            Q('j3', L('Come eviti di salvare JSON non valido?', 'How do you avoid storing invalid JSON?'), [L('Con `CHECK (json_valid(dati))`', 'With `CHECK (json_valid(dati))`'), L('Con `PRAGMA json = ON`', 'With `PRAGMA json = ON`'), L('Con una tabella `STRICT JSON`', 'With a `STRICT JSON` table'), L('Non si può evitare', 'It cannot be avoided')], 0, L('Il vincolo rifiuta il testo sbagliato.', 'The constraint rejects bad text.')),
        ], task('sql', L('Dalla tabella `ordini` leggi `id` e il campo `citta` della colonna JSON `spedizione` come valore SQL, con `->>`.', 'From the table `ordini` read `id` and the `citta` field of the JSON column `spedizione` as an SQL value, with `->>`.'), '', [re('SELECT\s+id\s*,\s*spedizione\s*->>\s*\'\$\.citta\'', L('Leggi il campo come valore', 'You read the field as a value')), re('FROM\s+ordini', L('Dalla tabella giusta', 'From the right table'))], '', "SELECT id, spedizione ->> '$.citta' FROM ordini;"), [
            dMatch(pMatch(), [['->>', L('Valore SQL', 'SQL value')], ['->', L('Frammento JSON', 'JSON fragment')], ['json_each', L('Array in righe', 'Array into rows')], ['json_valid', L('Controlla il JSON', 'Checks the JSON')], ['$', L('La radice del documento', 'The document root')]], L('Gli strumenti JSON di SQLite.', 'SQLite’s JSON tools.')),
            dFlash(pFlash(), [[L('SQLite ha un tipo di colonna JSON obbligatorio', 'SQLite has a mandatory JSON column type'), false], [L('Gli operatori `->` e `->>` esistono dalla 3.38', 'The `->` and `->>` operators exist since 3.38'), true], [L('`json_each` si usa nel `FROM`', '`json_each` is used in the `FROM`'), true], [L('Un `CHECK` con `json_valid` blocca il JSON sbagliato', 'A `CHECK` with `json_valid` blocks bad JSON'), true]], L('JSON come testo, letto bene.', 'JSON as text, read well.')),
        ]),
        lesson('fts5', L('Ricerca full-text con FTS5', 'Full-text search with FTS5'), [
            P('**FTS5** è il modulo di ricerca full-text di SQLite. Si crea una **tabella virtuale** con le colonne da cercare e si interroga con `MATCH`: trova le parole in un attimo anche in migliaia di documenti, e ordina per pertinenza con la colonna nascosta `rank`.', '**FTS5** is SQLite’s full-text search module. You create a **virtual table** with the columns to search and query it with `MATCH`: it finds words instantly even in thousands of documents, and sorts by relevance with the hidden `rank` column.'),
            C('CREATE VIRTUAL TABLE note_fts USING fts5(titolo, testo);

INSERT INTO note_fts (titolo, testo) VALUES (\'Spesa\', \'Comprare pane, latte e uova\');

SELECT titolo, highlight(note_fts, 1, \'[\', \']\') AS estratto
FROM note_fts
WHERE note_fts MATCH \'pane OR latt*\'
ORDER BY rank;', 'sql', fucina_data_table(['titolo', 'estratto'], [['Spesa', 'Comprare [pane], [latte] e uova']], 'SELECT titolo, highlight(note_fts, 1, \'[\', \']\') AS estratto …')),
            Fig(L(fig_sqlite_fts('Documenti', 'Indice: parola → documenti', 'MATCH \'pane\' legge una riga dell’indice, non tutti i testi.'), fig_sqlite_fts('Documents', 'Index: word → documents', 'MATCH \'pane\' reads one index row, not every text.')),
                L('A sinistra tre note: 1 «Comprare pane, latte e uova», 2 «Latte di mandorla», 3 «Pane integrale». A destra l’indice in ordine alfabetico, con ogni parola e i numeri delle note che la contengono; la riga pane, con 1 e 3, è evidenziata e collegata alle note 1 e 3.', 'On the left three notes: 1 “Comprare pane, latte e uova”, 2 “Latte di mandorla”, 3 “Pane integrale”. On the right the index in alphabetical order, with each word and the numbers of the notes that contain it; the pane row, with 1 and 3, is highlighted and linked to notes 1 and 3.'),
                L('È un indice inverso: come l’indice analitico in fondo a un libro.', 'It is an inverted index: like the index at the back of a book.')),
            P('Nella domanda puoi usare `AND`, `OR`, `NOT`, le frasi tra virgolette doppie e il prefisso con `*` (`latt*` trova «latte» e «lattuga»). `highlight` evidenzia le parole trovate. La tabella FTS va tenuta allineata ai dati veri: di solito con dei trigger, oppure con una tabella *external content*.', 'In the query you can use `AND`, `OR`, `NOT`, phrases in double quotes and prefixes with `*` (`latt*` finds “latte” and “lattuga”). `highlight` marks the words found. The FTS table must be kept in sync with the real data: usually with triggers, or with an *external content* table.'),
        ], [
            Q('t1', L('Come si crea una tabella di ricerca FTS5?', 'How do you create an FTS5 search table?'), [L('`CREATE TABLE … FULLTEXT`', '`CREATE TABLE … FULLTEXT`'), L('`CREATE INDEX … USING fts5`', '`CREATE INDEX … USING fts5`'), L('`CREATE VIRTUAL TABLE … USING fts5(…)`', '`CREATE VIRTUAL TABLE … USING fts5(…)`'), L('`PRAGMA fts5 = ON`', '`PRAGMA fts5 = ON`')], 2, L('È una tabella virtuale.', 'It is a virtual table.')),
            Q('t2', L('Che cosa trova `latt*`?', 'What does `latt*` find?'), [L('Solo la parola «latt»', 'Only the word “latt”'), L('Le parole che iniziano con «latt»', 'Words starting with “latt”'), L('Le parole che finiscono con «latt»', 'Words ending with “latt”'), L('Tutte le parole tranne «latt»', 'Every word except “latt”')], 1, L('È una ricerca per prefisso.', 'It is a prefix search.')),
            Q('t3', L('Come ordini i risultati per pertinenza?', 'How do you sort results by relevance?'), [L('`ORDER BY titolo`', '`ORDER BY titolo`'), L('`ORDER BY rowid DESC`', '`ORDER BY rowid DESC`'), L('`ORDER BY random()`', '`ORDER BY random()`'), L('`ORDER BY rank`', '`ORDER BY rank`')], 3, L('`rank` è una colonna nascosta di FTS5.', '`rank` is a hidden FTS5 column.')),
        ], task('sql', L('Crea la tabella virtuale `articoli_fts` con FTS5 sulle colonne `titolo` e `corpo`, poi cerca le righe che corrispondono a `\'database\'`, ordinate per `rank`.', 'Create the virtual table `articoli_fts` with FTS5 on the columns `titolo` and `corpo`, then search for the rows matching `\'database\'`, sorted by `rank`.'), '', [re('CREATE\s+VIRTUAL\s+TABLE\s+articoli_fts\s+USING\s+fts5\s*\(\s*titolo\s*,\s*corpo\s*\)', L('La tabella FTS5 esiste', 'The FTS5 table exists')), re('WHERE\s+articoli_fts\s+MATCH\s+\'database\'', L('Cerchi la parola', 'You search for the word')), re('ORDER\s+BY\s+rank', L('Ordini per pertinenza', 'You sort by relevance'))], '', "CREATE VIRTUAL TABLE articoli_fts USING fts5(titolo, corpo);\nSELECT * FROM articoli_fts WHERE articoli_fts MATCH 'database' ORDER BY rank;"), [
            dOrder(pSort(), ['CREATE VIRTUAL TABLE ricette_fts USING fts5(nome, ingredienti);', 'INSERT INTO ricette_fts VALUES (\'Carbonara\', \'uova, guanciale, pecorino\');', 'SELECT nome FROM ricette_fts', 'WHERE ricette_fts MATCH \'uova AND pecorino\'', 'ORDER BY rank;'], L('Crea, riempi, cerca, ordina.', 'Create, fill, search, sort.')),
            dBug(pBug(), ['SELECT titolo FROM note_fts', 'WHERE testo LIKE \'pane\'', 'ORDER BY rank;'], 1, 'WHERE note_fts MATCH \'pane\'', L('Con FTS5 si cerca con `MATCH`: `LIKE` non usa l’indice full-text.', 'With FTS5 you search with `MATCH`: `LIKE` does not use the full-text index.')),
        ]),
    ],
];
