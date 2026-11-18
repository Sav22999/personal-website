<?php
/** SQL advanced: CTEs, window functions and frames, transactions, concurrency and locks, views, query plans and triggers. */

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
            P('Senza transazione, un errore tra i due `UPDATE` lascerebbe sparire 100 euro. I livelli di **isolamento** decidono che cosa vedono le transazioni che girano insieme. `START TRANSACTION` è la forma di MySQL e PostgreSQL (che accetta anche `BEGIN`); SQL Server scrive `BEGIN TRANSACTION`.', 'Without a transaction, an error between the two `UPDATE`s would make 100 euros vanish. **Isolation** levels decide what concurrent transactions can see. `START TRANSACTION` is the MySQL and PostgreSQL form (PostgreSQL also accepts `BEGIN`); SQL Server writes `BEGIN TRANSACTION`.'),
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
SELECT * FROM classifica WHERE pos <= 3;', 'sql'),
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
