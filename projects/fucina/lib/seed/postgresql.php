<?php
/** PostgreSQL: psql, rich types, RETURNING and upserts, JSONB, roles and EXPLAIN, PL/pgSQL, MVCC and VACUUM, full-text search, extensions. */

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

/** A JSONB document as a tree, the path that -> and ->> follow, and what each operator gives back. */
if (!function_exists('fig_pg_jsonb')) {
    function fig_pg_jsonb(string $text): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-family="ui-monospace, monospace" font-size="13">
<path d="M100 120 C 135 120, 135 58, 170 58" fill="none" stroke="#8A87B0" stroke-width="2"/>
<path d="M100 120 C 135 120, 135 168, 170 168" fill="none" stroke="#E8590C" stroke-width="3"/>
<path d="M330 168 H370" stroke="#E8590C" stroke-width="3"/>
<rect x="20" y="102" width="80" height="36" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="60" y="125" text-anchor="middle" font-weight="700">dati</text>
<rect x="170" y="40" width="160" height="36" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="250" y="63" text-anchor="middle">"tipo": "login"</text>
<rect x="170" y="150" width="160" height="36" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="250" y="173" text-anchor="middle">"utente": {…}</text>
<rect x="370" y="150" width="140" height="36" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="440" y="173" text-anchor="middle">"nome": "Ada"</text>
<text x="128" y="204" font-weight="700" fill="#B84508">-&gt;'utente'</text>
<text x="340" y="140" font-weight="700" fill="#B84508">-&gt;&gt;'nome'</text>
<text x="20" y="250">dati-&gt;'utente'</text>
<text x="20" y="284">dati-&gt;'utente'-&gt;&gt;'nome'</text>
<path d="M232 245 H262" stroke="#1E1C52" stroke-width="2"/><path d="M260 239 L270 245 L260 251 Z" fill="#1E1C52"/>
<path d="M232 279 H262" stroke="#1E1C52" stroke-width="2"/><path d="M260 273 L270 279 L260 285 Z" fill="#1E1C52"/>
<rect x="278" y="228" width="160" height="30" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="358" y="248" text-anchor="middle">{"nome": "Ada"}</text>
<rect x="278" y="264" width="160" height="30" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="358" y="284" text-anchor="middle">Ada</text>
</g>
<text x="452" y="248" font-weight="700" fill="#4B2BBF">JSON</text>
<text x="452" y="284" font-weight="700" fill="#0B7F5C">{$text}</text>
</svg>
SVG;
    }
}

/** How PostgreSQL nests things: server, databases, schemas, tables; a role gets rights on a schema. */
if (!function_exists('fig_pg_schemas')) {
    function fig_pg_schemas(string $server, string $role): string
    {
        $t = fn(int $x, int $y, string $n) => '<rect x="' . $x . '" y="' . $y . '" width="108" height="30" rx="8" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/><text x="' . ($x + 12) . '" y="' . ($y + 20) . '">' . $n . '</text>';
        $g = $t(180, 126, 'ordini') . $t(180, 166, 'fatture') . $t(326, 126, 'clienti') . $t(326, 166, 'prodotti') . str_replace('width="108"', 'width="94"', $t(498, 126, 'articoli'));
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="132" y="14" width="492" height="242" rx="14" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="148" y="38" font-weight="700" fill="#B84508">{$server}</text>
<rect x="148" y="50" width="316" height="190" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="480" y="50" width="130" height="190" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g font-weight="700" fill="#1B66BC"><text x="162" y="74">database negozio</text><text x="494" y="74">database blog</text></g>
<rect x="164" y="86" width="140" height="140" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="3"/>
<rect x="310" y="86" width="140" height="140" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="490" y="86" width="110" height="140" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g font-size="13" font-weight="700" fill="#0B7F5C"><text x="180" y="110">schema vendite</text><text x="326" y="110">schema public</text><text x="500" y="110">schema public</text></g>
{$g}
<rect x="16" y="116" width="92" height="44" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="62" y="143" text-anchor="middle" font-weight="700" fill="#4B2BBF">{$role}</text>
<path d="M108 138 H156" stroke="#7048E8" stroke-width="2"/><path d="M154 132 L164 138 L154 144 Z" fill="#7048E8"/>
<text x="20" y="190" font-family="ui-monospace, monospace" font-size="12" fill="#4B2BBF">GRANT USAGE</text>
<text x="148" y="250" font-family="ui-monospace, monospace" font-size="12" fill="#5A5880">vendite.ordini</text>
</svg>
SVG;
    }
}

/** MVCC: an UPDATE writes a new version of the row; an older reader still sees the old one until VACUUM clears it. */
if (!function_exists('fig_pg_mvcc')) {
    function fig_pg_mvcc(string $old, string $new, string $sees, string $writes, string $note): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<defs><pattern id="pg-dead" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="8" height="8" fill="#F1F0F8"/><line x1="0" y1="0" x2="0" y2="8" stroke="#C9C7DE" stroke-width="2"/></pattern></defs>
<rect x="210" y="50" width="230" height="58" rx="12" fill="url(#pg-dead)" stroke="#8A87B0" stroke-width="2"/>
<text x="226" y="40" font-size="13" font-weight="700" fill="#5A5880">{$old}</text>
<text x="226" y="76" font-weight="700">id 7 · totale 50</text><text x="226" y="97" font-family="ui-monospace, monospace" font-size="12" fill="#5A5880">xmin 100 · xmax 105</text>
<rect x="210" y="146" width="230" height="58" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="226" y="136" font-size="13" font-weight="700" fill="#0B7F5C">{$new}</text>
<text x="226" y="172" font-weight="700">id 7 · totale 80</text><text x="226" y="193" font-family="ui-monospace, monospace" font-size="12" fill="#5A5880">xmin 105 · xmax –</text>
<g font-family="ui-monospace, monospace" font-size="13" font-weight="700">
<rect x="20" y="60" width="140" height="38" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="90" y="84" text-anchor="middle">T103 SELECT</text>
<rect x="20" y="156" width="140" height="38" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="90" y="180" text-anchor="middle">T106 SELECT</text>
<rect x="490" y="156" width="134" height="38" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="557" y="180" text-anchor="middle">T105 UPDATE</text>
</g>
<path d="M160 79 H200" stroke="#2F8CF0" stroke-width="2"/><path d="M198 73 L208 79 L198 85 Z" fill="#2F8CF0"/>
<path d="M160 175 H200" stroke="#2F8CF0" stroke-width="2"/><path d="M198 169 L208 175 L198 181 Z" fill="#2F8CF0"/>
<path d="M490 175 H450" stroke="#E8590C" stroke-width="2"/><path d="M452 169 L442 175 L452 181 Z" fill="#E8590C"/>
<path d="M557 156 V79 H450" fill="none" stroke="#E8590C" stroke-width="2" stroke-dasharray="6 4"/><path d="M452 73 L442 79 L452 85 Z" fill="#E8590C"/>
<g font-size="12" fill="#5A5880"><text x="164" y="70">{$sees}</text><text x="164" y="166">{$sees}</text><text x="452" y="166">{$writes}</text><text x="470" y="70" font-family="ui-monospace, monospace">xmax = 105</text></g>
<text x="20" y="242" font-weight="700" fill="#5A5880">{$note}</text>
</svg>
SVG;
    }
}

/** Full-text search: the text becomes a tsvector, the question a tsquery, and @@ compares them. */
if (!function_exists('fig_pg_fts')) {
    function fig_pg_fts(string $text, string $query, string $stems, string $yes): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g font-size="13" font-weight="700" fill="#5A5880"><text x="20" y="34">{$text}</text><text x="20" y="144">{$query}</text></g>
<rect x="20" y="44" width="150" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="95" y="69" text-anchor="middle">I gatti neri dormono</text>
<rect x="20" y="154" width="150" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="95" y="179" text-anchor="middle">gatti neri</text>
<g font-family="ui-monospace, monospace" font-size="12" fill="#4B2BBF" text-anchor="middle"><text x="250" y="56">to_tsvector</text><text x="250" y="166">websearch_to_tsquery</text></g>
<path d="M170 64 H322" stroke="#7048E8" stroke-width="2"/><path d="M320 58 L330 64 L320 70 Z" fill="#7048E8"/>
<path d="M170 174 H322" stroke="#7048E8" stroke-width="2"/><path d="M320 168 L330 174 L320 180 Z" fill="#7048E8"/>
<g font-family="ui-monospace, monospace" font-size="12">
<rect x="332" y="44" width="212" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="438" y="69" text-anchor="middle">'dorm':4 'gatt':2 'ner':3</text>
<rect x="332" y="154" width="212" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="438" y="179" text-anchor="middle">'gatt' &amp; 'ner'</text>
</g>
<text x="332" y="106" font-size="12" fill="#5A5880">{$stems}</text>
<path d="M544 64 C 572 64, 586 80, 590 96" fill="none" stroke="#0B7F5C" stroke-width="2"/><path d="M544 174 C 572 174, 586 156, 590 140" fill="none" stroke="#0B7F5C" stroke-width="2"/>
<circle cx="592" cy="118" r="22" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="592" y="124" text-anchor="middle" font-family="ui-monospace, monospace" font-weight="700" fill="#0B7F5C">@@</text>
<text x="592" y="226" text-anchor="middle" font-weight="700" fill="#0B7F5C">{$yes}</text>
<path d="M592 140 V206" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="4 4"/>
</svg>
SVG;
    }
}

return [
    'id' => 'postgresql', 'family' => 'postgresql', 'after' => 'sql-base',
    'title' => L('PostgreSQL', 'PostgreSQL'), 'short' => L('PostgreSQL', 'PostgreSQL'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database open source più avanzato: `psql`, tipi ricchi, upsert con `ON CONFLICT`, JSONB, ruoli ed `EXPLAIN`, PL/pgSQL e trigger, MVCC e `VACUUM`, ricerca full-text, pgvector e PostGIS.', 'The most advanced open source database: `psql`, rich types, upserts with `ON CONFLICT`, JSONB, roles and `EXPLAIN`, PL/pgSQL and triggers, MVCC and `VACUUM`, full-text search, pgvector and PostGIS.'),
    'lessons' => [
        lesson('psql', L('psql e i primi passi', 'psql and first steps'), [
            P('`psql` è il client a riga di comando di PostgreSQL. Oltre all’SQL accetta i **meta-comandi** che iniziano con la barra rovesciata: `\l` elenca i database, `\c` si collega a uno, `\dt` mostra le tabelle, `\d nome` descrive una tabella, `\q` esce.', '`psql` is PostgreSQL’s command-line client. Besides SQL it accepts **meta-commands** starting with a backslash: `\l` lists databases, `\c` connects to one, `\dt` shows tables, `\d name` describes a table, `\q` quits.'),
            C('psql -U postgres

CREATE DATABASE negozio;
\c negozio
CREATE TABLE prodotti (
    id SERIAL PRIMARY KEY,
    nome TEXT NOT NULL,
    prezzo NUMERIC(8, 2)
);
\d prodotti', 'sql'),
            P('In PostgreSQL i nomi non tra virgolette diventano minuscoli: `Prodotti` e `prodotti` sono la stessa tabella. Le virgolette doppie servono per i nomi, quelle singole per i testi.', 'In PostgreSQL unquoted names become lowercase: `Prodotti` and `prodotti` are the same table. Double quotes are for names, single quotes for text.'),
            Tip('`TEXT` in PostgreSQL non ha limiti pratici e non è più lento di `VARCHAR`: usalo senza timori.', '`TEXT` in PostgreSQL has no practical limit and is no slower than `VARCHAR`: use it freely.'),
        ], [
            Q('p1', L('Quale meta-comando elenca le tabelle in `psql`?', 'Which meta-command lists the tables in `psql`?'), ['\dt', '\l', '\q', 'SHOW TABLES'], 0, L('`\l` elenca i database.', '`\l` lists the databases.')),
            Q('p2', L('A che cosa servono le virgolette doppie?', 'What are double quotes for?'), [L('Ai nomi di tabelle e colonne', 'Table and column names'), L('Ai testi', 'Text'), L('Ai numeri', 'Numbers'), L('Ai commenti', 'Comments')], 0, L('I testi vanno tra apici singoli.', 'Text goes between single quotes.')),
            Q('p3', L('Che cosa diventa il nome `Clienti` senza virgolette?', 'What does the name `Clienti` become without quotes?'), ['clienti', 'CLIENTI', 'Clienti', L('Un errore', 'An error')], 0, L('Tutto minuscolo.', 'All lowercase.')),
        ], task('sql', L('Crea la tabella `autori` con `id SERIAL PRIMARY KEY` e `nome TEXT NOT NULL`.', 'Create the table `autori` with `id SERIAL PRIMARY KEY` and `nome TEXT NOT NULL`.'), '', [re('CREATE\s+TABLE\s+autori', L('La tabella esiste', 'The table exists')), re('id\s+SERIAL\s+PRIMARY\s+KEY', L('L’id si numera da solo', 'The id numbers itself')), re('nome\s+TEXT\s+NOT\s+NULL', L('Il nome è obbligatorio', 'The name is required'))], '', "CREATE TABLE autori (\n    id SERIAL PRIMARY KEY,\n    nome TEXT NOT NULL\n);"), [
            dMatch(pMatch(), [['\l', L('Elenca i database', 'Lists databases')], ['\c', L('Si collega a un database', 'Connects to a database')], ['\dt', L('Elenca le tabelle', 'Lists tables')], ['\d', L('Descrive una tabella', 'Describes a table')], ['\q', L('Esce', 'Quits')]], L('I meta-comandi di psql.', 'psql meta-commands.')),
            dFlash(pFlash(), [[L('I nomi senza virgolette diventano minuscoli', 'Unquoted names become lowercase'), true], [L('`TEXT` è molto più lento di `VARCHAR`', '`TEXT` is much slower than `VARCHAR`'), false], [L('I meta-comandi di psql finiscono con `;`', 'psql meta-commands end with `;`'), false], [L('`SERIAL` crea un contatore automatico', '`SERIAL` creates an automatic counter'), true]], L('Le abitudini di PostgreSQL.', 'PostgreSQL’s habits.')),
        ]),
        lesson('types', L('Tipi ricchi', 'Rich types'), [
            P('PostgreSQL ha tipi che altri database non hanno: `UUID`, `BOOLEAN` vero, `TIMESTAMPTZ` (data e ora con fuso), `INTERVAL`, gli **array** e i tipi enumerati creati con `CREATE TYPE`.', 'PostgreSQL has types other databases lack: `UUID`, a real `BOOLEAN`, `TIMESTAMPTZ` (date and time with time zone), `INTERVAL`, **arrays** and enumerated types made with `CREATE TYPE`.'),
            C('CREATE TYPE umore AS ENUM (\'felice\', \'neutro\', \'triste\');

CREATE TABLE note (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tag TEXT[] DEFAULT \'{}\',
    stato umore,
    creata TIMESTAMPTZ DEFAULT now()
);

SELECT * FROM note WHERE \'sql\' = ANY(tag);
SELECT now() - INTERVAL \'7 days\';', 'sql'),
            P('Per i nuovi progetti si consiglia `GENERATED ALWAYS AS IDENTITY` al posto di `SERIAL`: è lo standard SQL. E per le date reali usa `TIMESTAMPTZ`, che salva l’istante esatto. Da PostgreSQL 18 c’è anche `uuidv7()`, che genera UUID ordinati nel tempo, più amichevoli con gli indici.', 'For new projects `GENERATED ALWAYS AS IDENTITY` is recommended over `SERIAL`: it is the SQL standard. And for real dates use `TIMESTAMPTZ`, which stores the exact instant. Since PostgreSQL 18 there is also `uuidv7()`, which generates time-ordered UUIDs, kinder to indexes.'),
        ], [
            Q('t1', L('Come controlli se un array contiene `\'sql\'`?', 'How do you check whether an array contains `\'sql\'`?'), [L('`\'sql\' = ANY(tag)`', '`\'sql\' = ANY(tag)`'), L('`tag LIKE \'sql\'`', '`tag LIKE \'sql\'`'), L('`tag IN \'sql\'`', '`tag IN \'sql\'`'), L('`HAS(tag, \'sql\')`', '`HAS(tag, \'sql\')`')], 0, L('`ANY` confronta con ogni elemento.', '`ANY` compares with each element.')),
            Q('t2', L('Quale tipo per un istante preciso nel tempo?', 'Which type for an exact instant in time?'), ['TIMESTAMPTZ', 'DATE', 'TEXT', 'INTERVAL'], 0, L('Con il fuso orario.', 'With the time zone.')),
            Q('t3', L('Qual è l’alternativa standard a `SERIAL`?', 'What is the standard alternative to `SERIAL`?'), ['GENERATED ALWAYS AS IDENTITY', 'AUTO_INCREMENT', 'IDENTITY(1,1)', 'ROWID'], 0, L('È lo standard SQL.', 'It is the SQL standard.')),
        ], task('sql', L('Crea la tabella `articoli` con `id UUID PRIMARY KEY DEFAULT gen_random_uuid()` e `tag TEXT[]`.', 'Create the table `articoli` with `id UUID PRIMARY KEY DEFAULT gen_random_uuid()` and `tag TEXT[]`.'), '', [re('CREATE\s+TABLE\s+articoli', L('La tabella esiste', 'The table exists')), re('id\s+UUID\s+PRIMARY\s+KEY\s+DEFAULT\s+gen_random_uuid\(\)', L('L’id è un UUID casuale', 'The id is a random UUID')), re('tag\s+TEXT\s*\[\s*\]', L('I tag sono un array', 'Tags are an array'))], '', "CREATE TABLE articoli (\n    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),\n    tag TEXT[]\n);"), [
            dMatch(pMatch(), [['UUID', L('Identificatore universale', 'Universal identifier')], ['TEXT[]', L('Array di testi', 'Array of text')], ['TIMESTAMPTZ', L('Istante con fuso', 'Instant with time zone')], ['INTERVAL', L('Una durata', 'A duration')], ['BOOLEAN', L('Vero o falso', 'True or false')]], L('I tipi di PostgreSQL.', 'PostgreSQL types.')),
            dBlanks(pFill(), "CREATE ___ livello AS ___ ('base', 'medio', 'esperto');", ['TYPE', 'ENUM'], ['TABLE', 'LIST', 'DOMAIN'], L('Un tipo enumerato riusabile.', 'A reusable enumerated type.')),
        ]),
        lesson('upsert', L('RETURNING e ON CONFLICT', 'RETURNING and ON CONFLICT'), [
            P('`RETURNING` restituisce le righe toccate da `INSERT`, `UPDATE` e `DELETE`. `ON CONFLICT` gestisce i doppioni: `DO NOTHING` li salta, `DO UPDATE` li aggiorna; `EXCLUDED` è la riga che si voleva inserire.', '`RETURNING` returns the rows touched by `INSERT`, `UPDATE` and `DELETE`. `ON CONFLICT` handles duplicates: `DO NOTHING` skips them, `DO UPDATE` updates them; `EXCLUDED` is the row you tried to insert.'),
            C('INSERT INTO utenti (email, nome)
VALUES (\'ada@example.com\', \'Ada\')
ON CONFLICT (email) DO UPDATE SET nome = EXCLUDED.nome
RETURNING id;

INSERT INTO tag (nome) VALUES (\'sql\')
ON CONFLICT DO NOTHING;', 'sql', fucina_data_table(['id'], [[42]], 'INSERT INTO utenti … ON CONFLICT (email) DO UPDATE … RETURNING id;')),
            P('Tra parentesi dopo `ON CONFLICT` si indica la colonna (o le colonne) con il vincolo unico che fa scattare il conflitto.', 'In brackets after `ON CONFLICT` you name the column (or columns) with the unique constraint that triggers the conflict.'),
            P('Su quelle colonne deve esistere davvero un vincolo `UNIQUE` (o la chiave primaria), altrimenti PostgreSQL dà errore. Il vantaggio rispetto a «prima controllo con un `SELECT`, poi inserisco» è che tutto avviene in un passo solo: se due richieste arrivano insieme con la stessa email, nessuna delle due crea un doppione. Nel primo esempio, se Ada esiste già, il suo nome viene aggiornato e `RETURNING` restituisce comunque il suo `id`.', 'A `UNIQUE` constraint (or the primary key) must really exist on those columns, otherwise PostgreSQL raises an error. The advantage over “first check with a `SELECT`, then insert” is that it all happens in one step: if two requests arrive together with the same email, neither of them creates a duplicate. In the first example, if Ada already exists, her name is updated and `RETURNING` still gives back her `id`.'),
        ], [
            Q('o1', L('Che cosa rappresenta `EXCLUDED`?', 'What does `EXCLUDED` stand for?'), [L('La riga che si voleva inserire', 'The row you tried to insert'), L('La riga già presente', 'The row already there'), L('Le righe cancellate', 'The deleted rows'), L('Le colonne escluse dalla query', 'Columns left out of the query')], 0, L('Da lì prendi i valori nuovi.', 'That is where you take the new values.')),
            Q('o2', L('Che cosa fa `ON CONFLICT DO NOTHING`?', 'What does `ON CONFLICT DO NOTHING` do?'), [L('Salta i doppioni senza errore', 'Skips duplicates without error'), L('Svuota la tabella', 'Empties the table'), L('Annulla la transazione', 'Rolls back the transaction'), L('Inserisce comunque', 'Inserts anyway')], 0, L('Utile per importazioni ripetibili.', 'Handy for repeatable imports.')),
            Q('o3', L('Quale clausola restituisce l’id appena creato?', 'Which clause returns the id just created?'), ['RETURNING id', 'OUTPUT id', 'SELECT LAST id', 'GET id'], 0, L('Vale anche per `UPDATE` e `DELETE`.', 'It works for `UPDATE` and `DELETE` too.')),
        ], task('sql', L('Inserisci in `prodotti (codice, prezzo)` i valori `(\'A1\', 9.90)`; se il `codice` c’è già, aggiorna `prezzo` con quello nuovo.', 'Insert into `prodotti (codice, prezzo)` the values `(\'A1\', 9.90)`; if the `codice` already exists, update `prezzo` with the new one.'), '', [re('INSERT\s+INTO\s+prodotti\s*\(\s*codice\s*,\s*prezzo\s*\)', L('Inserisci nel posto giusto', 'You insert in the right place')), re('ON\s+CONFLICT\s*\(\s*codice\s*\)\s*DO\s+UPDATE', L('Gestisci il conflitto sul codice', 'You handle the conflict on the code')), re('prezzo\s*=\s*EXCLUDED\.prezzo', L('Usi il prezzo nuovo', 'You use the new price'))], '', "INSERT INTO prodotti (codice, prezzo) VALUES ('A1', 9.90)\nON CONFLICT (codice) DO UPDATE SET prezzo = EXCLUDED.prezzo;"), [
            dOrder(pSort(), ['INSERT INTO punti (utente_id, totale)', 'VALUES (7, 10)', 'ON CONFLICT (utente_id)', 'DO UPDATE SET totale = punti.totale + EXCLUDED.totale', 'RETURNING totale;'], L('Inserisci, gestisci il conflitto, restituisci.', 'Insert, handle the conflict, return.')),
            dBug(pBug(), ['INSERT INTO tag (nome)', 'VALUES (\'sql\')', 'ON DUPLICATE KEY UPDATE nome = nome;'], 2, 'ON CONFLICT DO NOTHING;', L('`ON DUPLICATE KEY` è MySQL: in PostgreSQL si usa `ON CONFLICT`.', '`ON DUPLICATE KEY` is MySQL: PostgreSQL uses `ON CONFLICT`.')),
        ]),
        lesson('jsonb', L('JSONB', 'JSONB'), [
            P('`JSONB` salva il JSON in forma binaria: più veloce da interrogare e indicizzabile. `->` restituisce JSON, `->>` testo, `@>` controlla se un documento ne **contiene** un altro.', '`JSONB` stores JSON in binary form: faster to query and indexable. `->` returns JSON, `->>` text, `@>` checks whether a document **contains** another.'),
            C('CREATE TABLE eventi (id SERIAL PRIMARY KEY, dati JSONB);

INSERT INTO eventi (dati) VALUES (\'{"tipo": "login", "utente": {"nome": "Ada"}}\');

SELECT dati->\'utente\'->>\'nome\' FROM eventi;
SELECT * FROM eventi WHERE dati @> \'{"tipo": "login"}\';

CREATE INDEX eventi_dati ON eventi USING GIN (dati);', 'sql', fucina_data_table(['?column?'], [['Ada']], 'SELECT dati->\'utente\'->>\'nome\' FROM eventi;')),
            Fig(L(fig_pg_jsonb('testo'), fig_pg_jsonb('text')),
                L('Il documento come albero: da dati partono due rami, "tipo": "login" e "utente", e da utente parte "nome": "Ada". Il percorso arancione segue ->\'utente\' e poi ->>\'nome\'. Sotto: dati->\'utente\' restituisce il JSON {"nome": "Ada"}, dati->\'utente\'->>\'nome\' il testo Ada.', 'The document as a tree: two branches leave dati, "tipo": "login" and "utente", and "nome": "Ada" hangs from utente. The orange path follows ->\'utente\' and then ->>\'nome\'. Below: dati->\'utente\' returns the JSON {"nome": "Ada"}, dati->\'utente\'->>\'nome\' the text Ada.'),
                L('`->` scende di un livello e resta JSON; `->>` all’ultimo passo dà testo semplice.', '`->` goes down one level and stays JSON; `->>` at the last step gives plain text.')),
            P('Un indice **GIN** rende veloci le ricerche con `@>` su tutto il documento. Il tipo `JSON` semplice invece conserva il testo così com’è, spazi e ordine compresi.', 'A **GIN** index makes `@>` searches over the whole document fast. The plain `JSON` type instead keeps the text as it is, spacing and order included.'),
            P('Un errore comune: confrontare `dati->\'tipo\' = \'login\'`. Con `->` il risultato è JSON e il confronto con un testo non funziona; usa `dati->>\'tipo\' = \'login\'` oppure `@>`, che sa anche sfruttare l’indice GIN. E se un campo serve in quasi tutte le query, dagli una colonna vera: `JSONB` è perfetto per la parte variabile dei dati, non per tutto.', 'A common mistake: comparing `dati->\'tipo\' = \'login\'`. With `->` the result is JSON and comparing it with text does not work; use `dati->>\'tipo\' = \'login\'` or `@>`, which can also use the GIN index. And if a field is needed in almost every query, give it a real column: `JSONB` is perfect for the variable part of the data, not for everything.'),
        ], [
            Q('b1', L('Che cosa fa `@>`?', 'What does `@>` do?'), [L('Controlla se il documento ne contiene un altro', 'Checks whether the document contains another'), L('Concatena testi', 'Concatenates text'), L('Confronta due date', 'Compares two dates'), L('Assegna un valore', 'Assigns a value')], 0, L('Il “contiene” di JSONB.', 'JSONB’s “contains”.')),
            Q('b2', L('Quale indice si usa per JSONB?', 'Which index is used for JSONB?'), ['GIN', 'BTREE', 'HASH', 'FULLTEXT'], 0, L('Indicizza chiavi e valori dentro il documento.', 'It indexes keys and values inside the document.')),
            Q('b3', L('Perché preferire `JSONB` a `JSON`?', 'Why prefer `JSONB` to `JSON`?'), [L('È più veloce da interrogare e si indicizza', 'It is faster to query and can be indexed'), L('Conserva gli spazi originali', 'It keeps the original spacing'), L('Occupa zero spazio', 'It takes zero space'), L('Non serve validarlo', 'It needs no validation')], 0, L('`JSON` tiene solo il testo.', '`JSON` only keeps the text.')),
        ], task('sql', L('Dalla tabella `ordini` seleziona tutto dove la colonna JSONB `dati` contiene `{"stato": "pagato"}`.', 'From the table `ordini` select everything where the JSONB column `dati` contains `{"stato": "pagato"}`.'), '', [re('SELECT\s+\*\s+FROM\s+ordini', L('Leggi gli ordini', 'You read the orders')), re('WHERE\s+dati\s*@>\s*\'\{\s*"stato"\s*:\s*"pagato"\s*\}\'', L('Filtri con @>', 'You filter with @>'))], '', "SELECT * FROM ordini WHERE dati @> '{\"stato\": \"pagato\"}';"), [
            dBlanks(pFill(), "SELECT dati ___ 'cliente' ___ 'email'\nFROM ordini\nWHERE dati ___ '{\"pagato\": true}';", ['->', '->>', '@>'], ['=>', '<@', '#'], L('JSON, poi testo, poi “contiene”.', 'JSON, then text, then “contains”.')),
            dFlash(pFlash(), [[L('`->>` restituisce testo', '`->>` returns text'), true], [L('Un indice GIN velocizza `@>`', 'A GIN index speeds up `@>`'), true], [L('`JSONB` conserva l’ordine originale delle chiavi', '`JSONB` keeps the original key order'), false], [L('JSONB non si può indicizzare', 'JSONB cannot be indexed'), false]], L('JSONB, il JSON che corre.', 'JSONB, the JSON that runs.')),
        ]),
        lesson('roles', L('Ruoli, schemi e EXPLAIN', 'Roles, schemas and EXPLAIN'), [
            P('In PostgreSQL utenti e gruppi sono **ruoli**. Un database si divide in **schemi** (il predefinito è `public`): cartelle di tabelle con permessi propri. `EXPLAIN ANALYZE` esegue la query e mostra come l’ha eseguita e quanto ha impiegato.', 'In PostgreSQL users and groups are **roles**. A database is split into **schemas** (the default is `public`): folders of tables with their own permissions. `EXPLAIN ANALYZE` runs the query and shows how it ran and how long it took.'),
            C('CREATE ROLE app LOGIN PASSWORD \'una-password-lunga\';
CREATE SCHEMA vendite AUTHORIZATION app;
GRANT USAGE ON SCHEMA vendite TO app;
GRANT SELECT, INSERT ON ALL TABLES IN SCHEMA vendite TO app;

EXPLAIN ANALYZE
SELECT * FROM vendite.ordini WHERE cliente_id = 42;', 'sql'),
            Fig(L(fig_pg_schemas('server PostgreSQL (cluster)', 'ruolo app'), fig_pg_schemas('PostgreSQL server (cluster)', 'role app')),
                L('Un server contiene due database, negozio e blog. Negozio contiene gli schemi vendite (tabelle ordini e fatture) e public (clienti e prodotti); blog contiene lo schema public con la tabella articoli. Il ruolo app, fuori dal server, ha una freccia GRANT USAGE verso lo schema vendite.', 'A server holds two databases, negozio and blog. Negozio holds the schemas vendite (tables ordini and fatture) and public (clienti and prodotti); blog holds the public schema with the articoli table. The app role, outside the server, has a GRANT USAGE arrow to the vendite schema.'),
                L('Il nome completo di una tabella è `schema.tabella`: `vendite.ordini`.', 'A table’s full name is `schema.table`: `vendite.ordini`.')),
            P('Nel piano, `Seq Scan` vuol dire che legge tutta la tabella; `Index Scan` che usa un indice. Se una ricerca frequente fa `Seq Scan` su una tabella grande, serve un indice. Nota: `GRANT … ON ALL TABLES` vale per le tabelle che esistono già; per quelle future si usa `ALTER DEFAULT PRIVILEGES`.', 'In the plan, `Seq Scan` means it reads the whole table; `Index Scan` that it uses an index. If a frequent lookup does a `Seq Scan` on a big table, it needs an index. Note: `GRANT … ON ALL TABLES` covers the tables that already exist; for future ones you use `ALTER DEFAULT PRIVILEGES`.'),
        ], [
            Q('x1', L('Come si chiama lo schema predefinito?', 'What is the default schema called?'), ['public', 'main', 'dbo', 'default'], 0, L('`dbo` è di SQL Server.', '`dbo` belongs to SQL Server.')),
            Q('x2', L('Che cosa indica `Seq Scan` in un piano?', 'What does `Seq Scan` mean in a plan?'), [L('Legge tutta la tabella', 'It reads the whole table'), L('Usa un indice', 'It uses an index'), L('La query è fallita', 'The query failed'), L('Legge in parallelo', 'It reads in parallel')], 0, L('Su tabelle grandi è un campanello.', 'On big tables it is a warning bell.')),
            Q('x3', L('Che differenza c’è tra `EXPLAIN` e `EXPLAIN ANALYZE`?', 'What is the difference between `EXPLAIN` and `EXPLAIN ANALYZE`?'), [L('`ANALYZE` esegue davvero la query', '`ANALYZE` really runs the query'), L('Nessuna', 'None'), L('`ANALYZE` cancella le statistiche', '`ANALYZE` deletes the statistics'), L('`ANALYZE` funziona solo su viste', '`ANALYZE` only works on views')], 0, L('E mostra i tempi reali.', 'And shows the real timings.')),
        ], task('sql', L('Crea il ruolo `lettore` con `LOGIN` e dagli `SELECT` su tutte le tabelle dello schema `public`.', 'Create the role `lettore` with `LOGIN` and give it `SELECT` on every table of the `public` schema.'), '', [re('CREATE\s+ROLE\s+lettore\b[^;]*LOGIN', L('Il ruolo può collegarsi', 'The role can log in')), re('GRANT\s+SELECT\s+ON\s+ALL\s+TABLES\s+IN\s+SCHEMA\s+public\s+TO\s+lettore', L('Legge tutto lo schema', 'It reads the whole schema'))], '', "CREATE ROLE lettore LOGIN PASSWORD 'segreta';\nGRANT SELECT ON ALL TABLES IN SCHEMA public TO lettore;"), [
            dMatch(pMatch(), [['ROLE', L('Utente o gruppo', 'User or group')], ['SCHEMA', L('Cartella di tabelle', 'Folder of tables')], ['public', L('Lo schema predefinito', 'The default schema')], ['Seq Scan', L('Lettura completa', 'Full read')], ['Index Scan', L('Lettura con indice', 'Read through an index')]], L('Organizzare e misurare.', 'Organising and measuring.')),
            dOrder(pSort(), ['CREATE ROLE app LOGIN PASSWORD \'x\';', 'CREATE SCHEMA vendite AUTHORIZATION app;', 'GRANT USAGE ON SCHEMA vendite TO app;', 'GRANT SELECT ON ALL TABLES IN SCHEMA vendite TO app;'], L('Ruolo, schema, accesso, permessi.', 'Role, schema, access, privileges.')),
        ]),
        lesson('plpgsql', L('Funzioni e trigger in PL/pgSQL', 'Functions and triggers in PL/pgSQL'), [
            P('**PL/pgSQL** è il linguaggio procedurale di PostgreSQL: variabili, `IF`, cicli e gestione degli errori dentro il database. Una funzione si crea con `CREATE OR REPLACE FUNCTION`; il corpo sta tra `$$`, così non devi raddoppiare gli apici.', '**PL/pgSQL** is PostgreSQL’s procedural language: variables, `IF`, loops and error handling inside the database. A function is created with `CREATE OR REPLACE FUNCTION`; the body sits between `$$`, so you do not have to double the quotes.'),
            C('CREATE OR REPLACE FUNCTION aggiorna_modificato()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    NEW.modificato_il := now();
    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_prodotti_modificato
BEFORE UPDATE ON prodotti
FOR EACH ROW EXECUTE FUNCTION aggiorna_modificato();', 'sql'),
            P('Un trigger in PostgreSQL ha due pezzi: una **funzione** che restituisce `trigger` e il **trigger** che la collega alla tabella. In un trigger `BEFORE`, `RETURN NEW` fa salvare la riga (anche modificata); `RETURN NULL` la scarta. Le funzioni semplici si possono scrivere anche in puro SQL, con `LANGUAGE sql`.', 'A trigger in PostgreSQL has two pieces: a **function** returning `trigger` and the **trigger** that links it to the table. In a `BEFORE` trigger, `RETURN NEW` saves the row (even modified); `RETURN NULL` discards it. Simple functions can also be written in plain SQL, with `LANGUAGE sql`.'),
        ], [
            Q('l1', L('A che cosa servono i `$$` nel corpo di una funzione?', 'What are the `$$` for in a function body?'), [L('Indicano una variabile globale', 'They mark a global variable'), L('Racchiudono il codice senza dover raddoppiare gli apici', 'They wrap the code without doubling the quotes'), L('Rendono la funzione più veloce', 'They make the function faster'), L('Indicano un valore in dollari', 'They mark a value in dollars')], 1, L('Si chiamano *dollar quoting*.', 'It is called *dollar quoting*.')),
            Q('l2', L('Che cosa deve restituire una funzione usata da un trigger?', 'What must a function used by a trigger return?'), ['void', 'integer', 'record', 'trigger'], 3, L('`RETURNS trigger`.', '`RETURNS trigger`.')),
            Q('l3', L('In un trigger `BEFORE UPDATE`, che cosa fa `RETURN NEW`?', 'In a `BEFORE UPDATE` trigger, what does `RETURN NEW` do?'), [L('Salva la riga, con le eventuali modifiche', 'Saves the row, with any changes'), L('Annulla l’aggiornamento', 'Cancels the update'), L('Crea una riga nuova in un’altra tabella', 'Creates a new row in another table'), L('Restituisce la riga al client e basta', 'Just returns the row to the client')], 0, L('`RETURN NULL` invece la scarterebbe.', '`RETURN NULL` would discard it instead.')),
        ], task('sql', L('Crea il trigger `tr_ordini_modificato` che prima di ogni `UPDATE` su `ordini` esegue la funzione `aggiorna_modificato()`.', 'Create the trigger `tr_ordini_modificato` that before every `UPDATE` on `ordini` runs the function `aggiorna_modificato()`.'), '', [re('CREATE\s+(OR\s+REPLACE\s+)?TRIGGER\s+tr_ordini_modificato', L('Il trigger esiste', 'The trigger exists')), re('BEFORE\s+UPDATE\s+ON\s+ordini', L('Prima di ogni aggiornamento', 'Before every update')), re('FOR\s+EACH\s+ROW\s+EXECUTE\s+(FUNCTION|PROCEDURE)\s+aggiorna_modificato\s*\(\s*\)', L('Esegue la funzione per ogni riga', 'It runs the function for each row'))], '', "CREATE TRIGGER tr_ordini_modificato\nBEFORE UPDATE ON ordini\nFOR EACH ROW EXECUTE FUNCTION aggiorna_modificato();"), [
            dOrder(pSort(), ['CREATE OR REPLACE FUNCTION blocca_negativi() RETURNS trigger', 'LANGUAGE plpgsql AS $$', 'BEGIN', '    IF NEW.prezzo < 0 THEN RAISE EXCEPTION \'Prezzo negativo\'; END IF;', '    RETURN NEW;', 'END;', '$$;'], L('Intestazione, linguaggio, corpo tra `$$`.', 'Header, language, body between `$$`.')),
            dMatch(pMatch(), [['RETURNS trigger', L('Funzione per un trigger', 'Function for a trigger')], ['$$', L('Racchiude il corpo', 'Wraps the body')], ['NEW', L('La riga nuova', 'The new row')], ['RAISE EXCEPTION', L('Lancia un errore', 'Raises an error')], ['EXECUTE FUNCTION', L('Collega la funzione al trigger', 'Links the function to the trigger')]], L('PL/pgSQL in breve.', 'PL/pgSQL in short.')),
        ]),
        lesson('mvcc', L('MVCC e VACUUM', 'MVCC and VACUUM'), [
            P('PostgreSQL usa l’**MVCC** (controllo della concorrenza a più versioni): un `UPDATE` non sovrascrive la riga, ne scrive una nuova versione e lascia la vecchia a chi la sta ancora leggendo. Così chi legge non blocca chi scrive, e viceversa.', 'PostgreSQL uses **MVCC** (multi-version concurrency control): an `UPDATE` does not overwrite the row, it writes a new version and leaves the old one to whoever is still reading it. So readers do not block writers, and vice versa.'),
            Fig(L(fig_pg_mvcc('versione vecchia (morta dopo il COMMIT)', 'versione nuova', 'la vede', 'la scrive', 'VACUUM toglie la versione vecchia quando nessuna transazione la vede più.'), fig_pg_mvcc('old version (dead after COMMIT)', 'new version', 'sees it', 'writes it', 'VACUUM removes the old version once no transaction can see it.')),
                L('Due versioni della stessa riga id 7: la vecchia con totale 50 (xmin 100, xmax 105), tratteggiata, e la nuova con totale 80 (xmin 105). La transazione T105 scrive la nuova e chiude la vecchia con xmax = 105. T103, partita prima, vede ancora la vecchia; T106, partita dopo, vede la nuova.', 'Two versions of the same row id 7: the old one with totale 50 (xmin 100, xmax 105), hatched, and the new one with totale 80 (xmin 105). Transaction T105 writes the new one and closes the old one with xmax = 105. T103, which started earlier, still sees the old one; T106, which started later, sees the new one.'),
                L('`xmin` e `xmax` dicono quale transazione ha creato e chiuso ogni versione.', '`xmin` and `xmax` say which transaction created and closed each version.')),
            C('-- quante righe «morte» aspettano la pulizia?
SELECT relname, n_live_tup, n_dead_tup, last_autovacuum
FROM pg_stat_user_tables
ORDER BY n_dead_tup DESC
LIMIT 5;

VACUUM (VERBOSE, ANALYZE) ordini;', 'sql', fucina_data_table(['relname', 'n_live_tup', 'n_dead_tup', 'last_autovacuum'], [['ordini', 182400, 9310, '2026-11-18 09:12'], ['sessioni', 5200, 4100, '2026-11-18 10:01'], ['prodotti', 830, 12, null]], 'SELECT relname, n_live_tup, n_dead_tup, last_autovacuum FROM pg_stat_user_tables …')),
            P('Le versioni vecchie diventano righe **morte**: le toglie il **`VACUUM`**, che lo **autovacuum** lancia da solo in background; `ANALYZE` aggiorna le statistiche usate dal pianificatore. Non spegnere mai l’autovacuum. `VACUUM FULL` riscrive la tabella e restituisce spazio al disco, ma la blocca per tutto il tempo.', 'Old versions become **dead** rows: **`VACUUM`** removes them, and **autovacuum** runs it by itself in the background; `ANALYZE` refreshes the statistics the planner uses. Never turn autovacuum off. `VACUUM FULL` rewrites the table and gives space back to the disk, but locks it the whole time.'),
            Tip('Per i backup si usa `pg_dump` (un database) o `pg_dumpall` (tutto il server); con il formato `-Fc` si ripristina con `pg_restore`, anche una tabella sola.', 'For backups you use `pg_dump` (one database) or `pg_dumpall` (the whole server); with the `-Fc` format you restore with `pg_restore`, even a single table.'),
        ], [
            Q('m1', L('Che cosa fa un `UPDATE` con l’MVCC?', 'What does an `UPDATE` do under MVCC?'), [L('Sovrascrive la riga sul posto', 'Overwrites the row in place'), L('Blocca tutte le letture della tabella', 'Blocks every read on the table'), L('Scrive una nuova versione della riga', 'Writes a new version of the row'), L('Cancella la riga e basta', 'Just deletes the row')], 2, L('La versione vecchia resta per chi la sta leggendo.', 'The old version stays for whoever is reading it.')),
            Q('m2', L('Chi toglie le righe morte di solito?', 'Who usually removes dead rows?'), [L('L’autovacuum, in background', 'Autovacuum, in the background'), L('L’amministratore, a mano ogni giorno', 'The administrator, by hand every day'), L('Il client `psql` alla chiusura', 'The `psql` client on exit'), L('Nessuno: restano per sempre', 'Nobody: they stay forever')], 0, L('Lancia `VACUUM` e `ANALYZE` quando servono.', 'It runs `VACUUM` and `ANALYZE` when needed.')),
            Q('m3', L('Qual è il difetto di `VACUUM FULL`?', 'What is the drawback of `VACUUM FULL`?'), [L('Cancella anche le righe vive', 'It deletes live rows too'), L('Non libera mai spazio', 'It never frees space'), L('Funziona solo sulle viste', 'It only works on views'), L('Blocca la tabella mentre la riscrive', 'It locks the table while rewriting it')], 3, L('Il `VACUUM` normale invece non blocca.', 'Plain `VACUUM` does not lock.')),
        ], task('sql', L('Esegui `VACUUM` con `ANALYZE` sulla tabella `sessioni`, poi leggi `n_dead_tup` da `pg_stat_user_tables` per `relname = \'sessioni\'`.', 'Run `VACUUM` with `ANALYZE` on the table `sessioni`, then read `n_dead_tup` from `pg_stat_user_tables` for `relname = \'sessioni\'`.'), '', [re('VACUUM\s+(\(\s*[^)]*ANALYZE[^)]*\)\s*|ANALYZE\s+)sessioni', L('Pulisci e aggiorni le statistiche', 'You clean up and refresh statistics')), re('SELECT\s+[^;]*n_dead_tup[^;]*FROM\s+pg_stat_user_tables\s+WHERE\s+relname\s*=\s*\'sessioni\'', L('Controlli le righe morte', 'You check the dead rows'))], '', "VACUUM ANALYZE sessioni;\nSELECT n_dead_tup FROM pg_stat_user_tables WHERE relname = 'sessioni';"), [
            dMatch(pMatch(), [['MVCC', L('Più versioni delle righe', 'Several row versions')], ['VACUUM', L('Toglie le righe morte', 'Removes dead rows')], ['ANALYZE', L('Aggiorna le statistiche', 'Refreshes statistics')], ['autovacuum', L('Pulizia automatica', 'Automatic clean-up')], ['VACUUM FULL', L('Riscrive e blocca', 'Rewrites and locks')]], L('Come PostgreSQL resta in salute.', 'How PostgreSQL stays healthy.')),
            dFlash(pFlash(), [[L('Con l’MVCC chi legge non blocca chi scrive', 'With MVCC readers do not block writers'), true], [L('Conviene spegnere l’autovacuum per andare più veloci', 'Turning autovacuum off is a good way to go faster'), false], [L('Le righe morte occupano spazio finché non c’è un VACUUM', 'Dead rows take space until a VACUUM'), true], [L('`pg_restore` legge i backup in formato `-Fc`', '`pg_restore` reads backups in `-Fc` format'), true]], L('Versioni, pulizia, statistiche.', 'Versions, clean-up, statistics.')),
        ]),
        lesson('fulltext', L('Ricerca full-text', 'Full-text search'), [
            P('`LIKE \'%gatto%\'` non trova «gatti», non ordina per pertinenza e non usa indici. La **ricerca full-text** di PostgreSQL trasforma il testo in un `tsvector` (parole ridotte alla radice, senza parole vuote come «il») e la domanda in un `tsquery`; l’operatore `@@` dice se combaciano.', '`LIKE \'%gatto%\'` does not find “gatti”, does not rank by relevance and uses no index. PostgreSQL’s **full-text search** turns the text into a `tsvector` (words reduced to their stem, without stop words such as “il”) and the question into a `tsquery`; the `@@` operator says whether they match.'),
            C('ALTER TABLE articoli ADD COLUMN ricerca tsvector
    GENERATED ALWAYS AS (to_tsvector(\'italian\', titolo || \' \' || testo)) STORED;

CREATE INDEX ix_articoli_ricerca ON articoli USING GIN (ricerca);

SELECT titolo, ts_rank(ricerca, q) AS punteggio
FROM articoli, websearch_to_tsquery(\'italian\', \'gatti neri\') AS q
WHERE ricerca @@ q
ORDER BY punteggio DESC
LIMIT 10;', 'sql', fucina_data_table(['titolo', 'punteggio'], [['Gatti neri e superstizioni', 0.0991], ['Il gatto nero di Poe', 0.0759], ['Adottare un gatto', 0.0304]], 'SELECT titolo, ts_rank(ricerca, q) AS punteggio … ORDER BY punteggio DESC')),
            Fig(L(fig_pg_fts('Testo', 'Domanda', 'radici e posizioni; «I» è una parola vuota', 'vero'), fig_pg_fts('Text', 'Query', 'stems and positions; “I” is a stop word', 'true')),
                L('In alto il testo «I gatti neri dormono» passa per to_tsvector e diventa dorm:4, gatt:2, ner:3. In basso la domanda «gatti neri» passa per websearch_to_tsquery e diventa gatt e ner. Le due strade arrivano all’operatore @@, che risponde vero.', 'On top the text “I gatti neri dormono” goes through to_tsvector and becomes dorm:4, gatt:2, ner:3. Below the query “gatti neri” goes through websearch_to_tsquery and becomes gatt and ner. The two paths reach the @@ operator, which answers true.'),
                L('Si confrontano le radici, non le parole: «gatto» troverebbe lo stesso articolo.', 'Stems are compared, not words: “gatto” would find the same article.')),
            P('La configurazione `\'italian\'` sa che «gatti» e «gatto» hanno la stessa radice. `websearch_to_tsquery` accetta domande come in un motore di ricerca (`"frase esatta"`, `-parola` per escludere). Una **colonna generata** tiene il `tsvector` sempre aggiornato e un indice **GIN** rende la ricerca veloce; `ts_rank` ordina per pertinenza.', 'The `\'italian\'` configuration knows that “gatti” and “gatto” share a stem. `websearch_to_tsquery` accepts queries like a search engine (`"exact phrase"`, `-word` to exclude). A **generated column** keeps the `tsvector` always up to date and a **GIN** index makes the search fast; `ts_rank` sorts by relevance.'),
        ], [
            Q('s1', L('Quale operatore confronta un `tsvector` con un `tsquery`?', 'Which operator matches a `tsvector` against a `tsquery`?'), ['@>', '@@', 'LIKE', '~~'], 1, L('Si legge «corrisponde a».', 'Read it as “matches”.')),
            Q('s2', L('Perché indicare la lingua, come `\'italian\'`?', 'Why give the language, such as `\'italian\'`?'), [L('Per tradurre i testi in italiano', 'To translate texts into Italian'), L('Per mostrare i risultati in italiano', 'To show results in Italian'), L('Per ordinare in ordine alfabetico italiano', 'To sort in Italian alphabetical order'), L('Per ridurre le parole alla radice e togliere le parole vuote', 'To reduce words to their stem and remove stop words')], 3, L('«Gatti» e «gatto» diventano la stessa radice.', '“Gatti” and “gatto” become the same stem.')),
            Q('s3', L('Quale indice si usa per una colonna `tsvector`?', 'Which index is used for a `tsvector` column?'), ['GIN', 'BRIN', 'HASH', L('Nessuno, non si può', 'None, it is not possible')], 0, L('Lo stesso tipo usato per JSONB.', 'The same kind used for JSONB.')),
        ], task('sql', L('Da `ricette` prendi `titolo` dove la colonna `ricerca` corrisponde a `websearch_to_tsquery(\'italian\', \'pasta funghi\')`.', 'From `ricette` take `titolo` where the column `ricerca` matches `websearch_to_tsquery(\'italian\', \'pasta funghi\')`.'), 'SELECT titolo FROM ricette', [re('WHERE\s+ricerca\s*@@\s*(websearch_to_tsquery|plainto_tsquery|to_tsquery)\s*\(\s*\'italian\'\s*,\s*\'pasta[ &|]+funghi\'\s*\)', L('Filtri con la ricerca full-text', 'You filter with full-text search'))], '', "SELECT titolo FROM ricette\nWHERE ricerca @@ websearch_to_tsquery('italian', 'pasta funghi');"), [
            dBlanks(pFill(), "SELECT titolo FROM libri\nWHERE ___('italian', trama) ___ websearch_to_tsquery('italian', 'drago')\nORDER BY ___(to_tsvector('italian', trama), websearch_to_tsquery('italian', 'drago')) DESC;", ['to_tsvector', '@@', 'ts_rank'], ['to_text', 'LIKE', 'ts_order'], L('Testo in vettore, confronto, pertinenza.', 'Text to vector, match, relevance.')),
            dFlash(pFlash(), [[L('`LIKE \'%gatto%\'` trova anche «gatti»', '`LIKE \'%gatto%\'` also finds “gatti”'), false], [L('Un indice GIN velocizza la ricerca full-text', 'A GIN index speeds up full-text search'), true], [L('`ts_rank` ordina per pertinenza', '`ts_rank` sorts by relevance'), true], [L('Una colonna generata tiene il `tsvector` aggiornato', 'A generated column keeps the `tsvector` up to date'), true]], L('Cercare come un motore di ricerca.', 'Searching like a search engine.')),
        ]),
        lesson('extensions', L('Estensioni: pgvector e PostGIS', 'Extensions: pgvector and PostGIS'), [
            P('Le **estensioni** aggiungono a PostgreSQL tipi, funzioni e indici nuovi con un comando: `CREATE EXTENSION`. Le più famose: **pgvector** per gli embedding dell’IA, **PostGIS** per i dati geografici, **pg_stat_statements** per scoprire le query più lente. In `psql`, `\dx` elenca quelle installate.', '**Extensions** add new types, functions and indexes to PostgreSQL with one command: `CREATE EXTENSION`. The best known: **pgvector** for AI embeddings, **PostGIS** for geographic data, **pg_stat_statements** to find the slowest queries. In `psql`, `\dx` lists the installed ones.'),
            C('CREATE EXTENSION IF NOT EXISTS vector;

CREATE TABLE documenti (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    testo TEXT,
    embedding vector(3)
);
CREATE INDEX ON documenti USING hnsw (embedding vector_cosine_ops);

SELECT testo FROM documenti
ORDER BY embedding <=> \'[0.2, 0.7, 0.3]\'
LIMIT 5;', 'sql'),
            P('Con pgvector l’operatore `<=>` misura la distanza del coseno e `<->` quella euclidea; un indice **HNSW** trova i vicini in fretta anche su milioni di righe. L’estensione deve essere installata sul server (molti servizi cloud la offrono già): `CREATE EXTENSION` la attiva nel database.', 'With pgvector the `<=>` operator measures cosine distance and `<->` Euclidean distance; an **HNSW** index finds neighbours quickly even on millions of rows. The extension must be installed on the server (many cloud services already offer it): `CREATE EXTENSION` enables it in the database.'),
        ], [
            Q('e1', L('Come si attiva un’estensione in un database?', 'How do you enable an extension in a database?'), [L('Con `INSTALL PLUGIN`', 'With `INSTALL PLUGIN`'), L('Riavviando sempre il server', 'By always restarting the server'), L('Con `CREATE EXTENSION`', 'With `CREATE EXTENSION`'), L('Con `GRANT EXTENSION`', 'With `GRANT EXTENSION`')], 2, L('I file però devono essere installati sul server.', 'Its files must be installed on the server, though.')),
            Q('e2', L('A che cosa serve PostGIS?', 'What is PostGIS for?'), [L('Ai dati geografici: punti, aree, distanze', 'Geographic data: points, areas, distances'), L('Alle immagini e ai video', 'Images and videos'), L('Ai backup automatici', 'Automatic backups'), L('Alla posta elettronica', 'Email')], 0, L('Per esempio «i negozi entro 5 km».', 'For example “shops within 5 km”.')),
            Q('e3', L('In pgvector che cosa misura `<=>`?', 'In pgvector what does `<=>` measure?'), [L('Se due testi sono identici', 'Whether two texts are identical'), L('La distanza del coseno tra vettori', 'The cosine distance between vectors'), L('La differenza tra due date', 'The difference between two dates'), L('La lunghezza di un vettore', 'The length of a vector')], 1, L('`<->` misura quella euclidea.', '`<->` measures the Euclidean one.')),
        ], task('sql', L('Attiva l’estensione `vector` se non c’è già, poi prendi i 3 `testo` di `documenti` più vicini al vettore `\'[0.1, 0.2, 0.3]\'` con `<->`.', 'Enable the `vector` extension if it is not there yet, then take the 3 `testo` of `documenti` nearest to the vector `\'[0.1, 0.2, 0.3]\'` with `<->`.'), '', [re('CREATE\s+EXTENSION\s+IF\s+NOT\s+EXISTS\s+"?vector"?', L('Attivi pgvector', 'You enable pgvector')), re('ORDER\s+BY\s+embedding\s*<->\s*\'\[\s*0\.1\s*,\s*0\.2\s*,\s*0\.3\s*\]\'(::vector)?', L('Ordini per distanza', 'You sort by distance')), re('LIMIT\s+3', L('I 3 più vicini', 'The 3 nearest'))], '', "CREATE EXTENSION IF NOT EXISTS vector;\nSELECT testo FROM documenti\nORDER BY embedding <-> '[0.1, 0.2, 0.3]'\nLIMIT 3;"), [
            dMatch(pMatch(), [['pgvector', L('Embedding per l’IA', 'AI embeddings')], ['PostGIS', L('Dati geografici', 'Geographic data')], ['pg_stat_statements', L('Statistiche delle query', 'Query statistics')], ['\dx', L('Elenca le estensioni', 'Lists extensions')], ['HNSW', L('Indice per i vicini', 'Nearest-neighbour index')]], L('L’ecosistema di PostgreSQL.', 'PostgreSQL’s ecosystem.')),
            dBug(pBug(), ['CREATE EXTENSION IF NOT EXISTS vector;', 'CREATE TABLE frasi (id INT PRIMARY KEY, embedding vector(3));', 'SELECT * FROM frasi ORDER BY embedding LIKE \'[1, 0, 0]\' LIMIT 5;'], 2, 'SELECT * FROM frasi ORDER BY embedding <=> \'[1, 0, 0]\' LIMIT 5;', L('Per i vettori si ordina per distanza con `<=>` o `<->`, non con `LIKE`.', 'For vectors you sort by distance with `<=>` or `<->`, not with `LIKE`.')),
        ]),
    ],
];
