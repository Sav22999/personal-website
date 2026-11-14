<?php
/** PostgreSQL: psql, rich types, RETURNING and upserts, JSONB, indexes and EXPLAIN, roles and schemas. */

return [
    'id' => 'postgresql', 'family' => 'postgresql', 'after' => 'sql-base',
    'title' => L('PostgreSQL', 'PostgreSQL'), 'short' => L('PostgreSQL', 'PostgreSQL'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database open source più avanzato: `psql`, tipi ricchi, upsert con `ON CONFLICT`, JSONB, indici, `EXPLAIN`, ruoli e schemi.', 'The most advanced open source database: `psql`, rich types, upserts with `ON CONFLICT`, JSONB, indexes, `EXPLAIN`, roles and schemas.'),
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
            P('Per i nuovi progetti si consiglia `GENERATED ALWAYS AS IDENTITY` al posto di `SERIAL`: è lo standard SQL. E per le date reali usa `TIMESTAMPTZ`, che salva l’istante esatto.', 'For new projects `GENERATED ALWAYS AS IDENTITY` is recommended over `SERIAL`: it is the SQL standard. And for real dates use `TIMESTAMPTZ`, which stores the exact instant.'),
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
ON CONFLICT DO NOTHING;', 'sql'),
            P('Tra parentesi dopo `ON CONFLICT` si indica la colonna (o le colonne) con il vincolo unico che fa scattare il conflitto.', 'In brackets after `ON CONFLICT` you name the column (or columns) with the unique constraint that triggers the conflict.'),
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

CREATE INDEX eventi_dati ON eventi USING GIN (dati);', 'sql'),
            P('Un indice **GIN** rende veloci le ricerche con `@>` su tutto il documento. Il tipo `JSON` semplice invece conserva il testo così com’è, spazi e ordine compresi.', 'A **GIN** index makes `@>` searches over the whole document fast. The plain `JSON` type instead keeps the text as it is, spacing and order included.'),
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
            P('Nel piano, `Seq Scan` vuol dire che legge tutta la tabella; `Index Scan` che usa un indice. Se una ricerca frequente fa `Seq Scan` su una tabella grande, serve un indice.', 'In the plan, `Seq Scan` means it reads the whole table; `Index Scan` that it uses an index. If a frequent lookup does a `Seq Scan` on a big table, it needs an index.'),
        ], [
            Q('x1', L('Come si chiama lo schema predefinito?', 'What is the default schema called?'), ['public', 'main', 'dbo', 'default'], 0, L('`dbo` è di SQL Server.', '`dbo` belongs to SQL Server.')),
            Q('x2', L('Che cosa indica `Seq Scan` in un piano?', 'What does `Seq Scan` mean in a plan?'), [L('Legge tutta la tabella', 'It reads the whole table'), L('Usa un indice', 'It uses an index'), L('La query è fallita', 'The query failed'), L('Legge in parallelo', 'It reads in parallel')], 0, L('Su tabelle grandi è un campanello.', 'On big tables it is a warning bell.')),
            Q('x3', L('Che differenza c’è tra `EXPLAIN` e `EXPLAIN ANALYZE`?', 'What is the difference between `EXPLAIN` and `EXPLAIN ANALYZE`?'), [L('`ANALYZE` esegue davvero la query', '`ANALYZE` really runs the query'), L('Nessuna', 'None'), L('`ANALYZE` cancella le statistiche', '`ANALYZE` deletes the statistics'), L('`ANALYZE` funziona solo su viste', '`ANALYZE` only works on views')], 0, L('E mostra i tempi reali.', 'And shows the real timings.')),
        ], task('sql', L('Crea il ruolo `lettore` con `LOGIN` e dagli `SELECT` su tutte le tabelle dello schema `public`.', 'Create the role `lettore` with `LOGIN` and give it `SELECT` on every table of the `public` schema.'), '', [re('CREATE\s+ROLE\s+lettore\b[^;]*LOGIN', L('Il ruolo può collegarsi', 'The role can log in')), re('GRANT\s+SELECT\s+ON\s+ALL\s+TABLES\s+IN\s+SCHEMA\s+public\s+TO\s+lettore', L('Legge tutto lo schema', 'It reads the whole schema'))], '', "CREATE ROLE lettore LOGIN PASSWORD 'segreta';\nGRANT SELECT ON ALL TABLES IN SCHEMA public TO lettore;"), [
            dMatch(pMatch(), [['ROLE', L('Utente o gruppo', 'User or group')], ['SCHEMA', L('Cartella di tabelle', 'Folder of tables')], ['public', L('Lo schema predefinito', 'The default schema')], ['Seq Scan', L('Lettura completa', 'Full read')], ['Index Scan', L('Lettura con indice', 'Read through an index')]], L('Organizzare e misurare.', 'Organising and measuring.')),
            dOrder(pSort(), ['CREATE ROLE app LOGIN PASSWORD \'x\';', 'CREATE SCHEMA vendite AUTHORIZATION app;', 'GRANT USAGE ON SCHEMA vendite TO app;', 'GRANT SELECT ON ALL TABLES IN SCHEMA vendite TO app;'], L('Ruolo, schema, accesso, permessi.', 'Role, schema, access, privileges.')),
        ]),
    ],
];
