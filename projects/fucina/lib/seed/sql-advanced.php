<?php
/** SQL advanced: CTEs, window functions, transactions, views and query plans. */

return [
    'id' => 'sql-advanced', 'family' => 'sql', 'after' => 'sql-intermediate',
    'title' => L('SQL avanzato', 'Advanced SQL'), 'short' => L('SQL avanzato', 'SQL Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('CTE e ricorsione, funzioni finestra, transazioni, viste e piani di esecuzione per query veloci.', 'CTEs and recursion, window functions, transactions, views and execution plans for fast queries.'),
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
SELECT n FROM numeri;', 'sql'),
            P('Una CTE ricorsiva ha un punto di partenza, `UNION ALL`, e un passo che richiama sé stesso finché la condizione lo permette.', 'A recursive CTE has a starting point, `UNION ALL`, and a step that calls itself while the condition allows.'),
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
            C('SELECT nome, corso, voto,
       RANK() OVER (PARTITION BY corso ORDER BY voto DESC) AS posizione,
       AVG(voto) OVER (PARTITION BY corso) AS media_corso,
       SUM(importo) OVER (ORDER BY giorno) AS totale_progressivo
FROM esami;', 'sql'),
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
            C('START TRANSACTION;

UPDATE conti SET saldo = saldo - 100 WHERE id = 1;
UPDATE conti SET saldo = saldo + 100 WHERE id = 2;

-- se tutto è andato bene
COMMIT;
-- altrimenti
-- ROLLBACK;', 'sql'),
            P('Senza transazione, un errore tra i due `UPDATE` lascerebbe sparire 100 euro. I livelli di **isolamento** decidono che cosa vedono le transazioni che girano insieme.', 'Without a transaction, an error between the two `UPDATE`s would make 100 euros vanish. **Isolation** levels decide what concurrent transactions can see.'),
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
            P('Una **vista** è una query salvata con un nome: si interroga come una tabella. Serve a nascondere la complessità o a mostrare solo alcune colonne. Una **procedura** salva un blocco di comandi da richiamare con `CALL`.', 'A **view** is a saved query with a name: you query it like a table. It hides complexity or shows only some columns. A **procedure** saves a block of commands to run with `CALL`.'),
            C('CREATE VIEW classifica_mensile AS
SELECT u.nome, SUM(p.punti) AS punti
FROM punteggi p JOIN utenti u ON u.id = p.utente_id
WHERE p.giorno >= CURRENT_DATE - INTERVAL 30 DAY
GROUP BY u.id, u.nome;

SELECT * FROM classifica_mensile ORDER BY punti DESC LIMIT 10;', 'sql'),
            P('La vista non copia i dati: ogni volta rilegge le tabelle. Per dati pesanti esistono le viste **materializzate** (in PostgreSQL) che salvano il risultato.', 'A view copies no data: it re-reads the tables every time. For heavy data there are **materialized** views (in PostgreSQL) that store the result.'),
        ], [
            Q('v1', L('Una vista contiene una copia dei dati?', 'Does a view hold a copy of the data?'), [L('No, rilegge le tabelle', 'No, it re-reads the tables'), L('Sì, sempre', 'Yes, always'), L('Solo il primo giorno', 'Only on the first day'), L('Solo i numeri', 'Only numbers')], 0, L('Quelle materializzate sì.', 'Materialized ones do.')),
            Q('v2', L('Come si interroga una vista?', 'How do you query a view?'), [L('Come una tabella', 'Like a table'), L('Con CALL', 'With CALL'), L('Con EXEC VIEW', 'With EXEC VIEW'), L('Non si può', 'You cannot')], 0, L('`SELECT * FROM vista`.', '`SELECT * FROM view`.')),
            Q('v3', L('Come si esegue una procedura?', 'How do you run a procedure?'), ['CALL', 'RUN', 'DO', 'START'], 0, L('`CALL nome(parametri)`.', '`CALL name(params)`.')),
        ], task('sql', L('Crea la vista `corsi_pubblicati` con `SELECT * FROM corsi WHERE stato = \'published\'`.', 'Create the view `corsi_pubblicati` with `SELECT * FROM corsi WHERE stato = \'published\'`.'), '', [re('CREATE\s+(OR\s+REPLACE\s+)?VIEW\s+corsi_pubblicati\s+AS', L('La vista esiste', 'The view exists')), re('SELECT\s+\*\s+FROM\s+corsi\s+WHERE\s+stato\s*=\s*\'published\'', L('La query è giusta', 'The query is right'))], '', 'CREATE VIEW corsi_pubblicati AS
SELECT * FROM corsi WHERE stato = \'published\';'), [
            dBlanks(pFill(), "CREATE ___ attivi ___\nSELECT id, nome FROM utenti WHERE attivo = 1;\nSELECT * ___ attivi;", ['VIEW', 'AS', 'FROM'], ['TABLE', 'IS', 'IN'], L('Crea, definisci, usa.', 'Create, define, use.')),
            dFlash(pFlash(), [[L('Una vista può nascondere colonne sensibili', 'A view can hide sensitive columns'), true], [L('Le viste rallentano sempre il database', 'Views always slow the database'), false], [L('Una procedura si richiama con `CALL`', 'A procedure is invoked with `CALL`'), true], [L('Una vista è una query salvata con un nome', 'A view is a query saved under a name'), true]], L('Una vista è una query con un nome.', 'A view is a query with a name.')),
        ]),
        lesson('explain', L('EXPLAIN e prestazioni', 'EXPLAIN and performance'), [
            P('`EXPLAIN` mostra come il database eseguirà una query: quali indici usa e quante righe legge. Una “scansione completa” su una tabella grande è il segnale che manca un indice.', '`EXPLAIN` shows how the database will run a query: which indexes it uses and how many rows it reads. A “full scan” on a big table signals a missing index.'),
            C('EXPLAIN SELECT * FROM ordini WHERE utente_id = 42;

-- se non usa indici:
CREATE INDEX ix_ordini_utente ON ordini (utente_id);

-- indice composto: l’ordine delle colonne conta
CREATE INDEX ix_ordini_utente_data ON ordini (utente_id, creato_il);', 'sql'),
            P('Un indice composto serve le query che filtrano sulla prima colonna (da sola o con la seconda), non quelle che filtrano solo sulla seconda. Evita funzioni sulle colonne indicizzate in `WHERE`: `YEAR(data) = 2026` non usa l’indice, `data >= \'2026-01-01\'` sì.', 'A composite index serves queries filtering on the first column (alone or with the second), not those filtering only on the second. Avoid functions on indexed columns in `WHERE`: `YEAR(data) = 2026` skips the index, `data >= \'2026-01-01\'` uses it.'),
        ], [
            Q('e1', L('Che cosa mostra `EXPLAIN`?', 'What does `EXPLAIN` show?'), [L('Il piano di esecuzione', 'The execution plan'), L('I dati', 'The data'), L('La struttura dei file', 'The file structure'), L('Gli utenti connessi', 'Connected users')], 0, L('Indici usati e righe lette.', 'Indexes used and rows read.')),
            Q('e2', L('Un indice su `(a, b)` aiuta una query che filtra solo su `b`?', 'Does an index on `(a, b)` help a query filtering only on `b`?'), [L('Di solito no', 'Usually not'), L('Sì, sempre', 'Yes, always'), L('Solo con LIMIT', 'Only with LIMIT'), L('Solo con ORDER BY', 'Only with ORDER BY')], 0, L('Conta la colonna più a sinistra.', 'The leftmost column matters.')),
            Q('e3', L('Perché `WHERE YEAR(data) = 2026` può essere lento?', 'Why can `WHERE YEAR(data) = 2026` be slow?'), [L('La funzione impedisce di usare l’indice', 'The function prevents index use'), L('Le date sono lente', 'Dates are slow'), L('2026 è un numero grande', '2026 is a big number'), L('Non è lento', 'It is not slow')], 0, L('Meglio un intervallo di date.', 'Better a date range.')),
        ], task('sql', L('Analizza `SELECT * FROM lezioni WHERE corso = \'sql-base\'` con `EXPLAIN`.', 'Analyse `SELECT * FROM lezioni WHERE corso = \'sql-base\'` with `EXPLAIN`.'), 'SELECT * FROM lezioni WHERE corso = \'sql-base\';', [re('^\s*EXPLAIN\s+SELECT\s+\*\s+FROM\s+lezioni', L('Chiedi il piano', 'You ask for the plan'))], '', 'EXPLAIN SELECT * FROM lezioni WHERE corso = \'sql-base\';'), [
            dBug(pBug(), ['SELECT id, totale', 'FROM ordini', 'WHERE YEAR(creato_il) = 2026;'], 2, 'WHERE creato_il >= \'2026-01-01\' AND creato_il < \'2027-01-01\';', L('Una funzione sulla colonna nasconde l’indice: meglio un intervallo.', 'A function on the column hides the index: better a range.')),
            dFlash(pFlash(), [[L('Una scansione completa su milioni di righe è lenta', 'A full scan on millions of rows is slow'), true], [L('Più indici rendono sempre tutto più veloce', 'More indexes always make everything faster'), false], [L('Nell’indice composto conta l’ordine delle colonne', 'Column order matters in a composite index'), true], [L('`EXPLAIN` mostra come il database eseguirà la query', '`EXPLAIN` shows how the database will run the query'), true]], L('Gli indici hanno un costo in scrittura.', 'Indexes cost something on writes.')),
        ]),
    ],
];
