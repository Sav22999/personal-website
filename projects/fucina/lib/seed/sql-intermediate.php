<?php
/** SQL intermediate: aggregates, GROUP BY, joins, subqueries and relations. */

return [
    'id' => 'sql-intermediate', 'family' => 'sql', 'after' => 'sql-base',
    'title' => L('SQL intermedio', 'Intermediate SQL'), 'short' => L('SQL intermedio', 'SQL Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Contare e raggruppare, unire tabelle con le JOIN, sottoquery, chiavi esterne e indici.', 'Counting and grouping, joining tables, subqueries, foreign keys and indexes.'),
    'lessons' => [
        lesson('aggregates', L('Funzioni di aggregazione', 'Aggregate functions'), [
            P('Le **funzioni di aggregazione** riassumono tante righe in un valore: `COUNT` conta, `SUM` somma, `AVG` fa la media, `MIN` e `MAX` trovano gli estremi.', '**Aggregate functions** sum up many rows into one value: `COUNT` counts, `SUM` adds, `AVG` averages, `MIN` and `MAX` find the extremes.'),
            C('SELECT COUNT(*) AS iscritti FROM utenti;
SELECT AVG(voto) AS media, MAX(voto) AS migliore FROM esami;
SELECT SUM(totale) FROM ordini WHERE stato = \'pagato\';', 'sql'),
            P('`AS` dà un nome leggibile alla colonna del risultato. `COUNT(colonna)` conta solo i valori non `NULL`.', '`AS` gives the result column a readable name. `COUNT(column)` counts only non-`NULL` values.'),
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
            C('SELECT citta, COUNT(*) AS utenti
FROM utenti
WHERE attivo = 1
GROUP BY citta
HAVING COUNT(*) >= 10
ORDER BY utenti DESC;', 'sql'),
            P('Nel `SELECT`, oltre alle aggregazioni, metti solo le colonne del `GROUP BY`. Alcuni database accettano anche colonne che dipendono dalla chiave primaria raggruppata, ma la regola semplice funziona ovunque.', 'In the `SELECT`, besides aggregates, put only the `GROUP BY` columns. Some databases also accept columns that depend on a grouped primary key, but the simple rule works everywhere.'),
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
GROUP BY u.id, u.nome;', 'sql'),
            P('Le lettere `u` e `o` sono **alias** delle tabelle: accorciano la query e chiariscono da dove viene ogni colonna.', 'The letters `u` and `o` are table **aliases**: they shorten the query and make clear where each column comes from.'),
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
WHERE o.id IS NULL;', 'sql'),
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
