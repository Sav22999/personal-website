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
            P('Nel `SELECT` possono stare solo le colonne del `GROUP BY` e le aggregazioni.', 'The `SELECT` may contain only the `GROUP BY` columns and aggregates.'),
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
            P('`EXISTS` risponde solo “c’è o non c’è?”: è spesso più veloce di `IN` su tabelle grandi.', '`EXISTS` only answers “is there or not?”: it is often faster than `IN` on big tables.'),
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
            dFlash(pFlash(), [[L('Un indice velocizza le ricerche', 'An index speeds up searches'), true], [L('Gli indici rendono più veloci anche le scritture', 'Indexes also speed up writes'), false], [L('Una chiave esterna punta a una chiave primaria', 'A foreign key points to a primary key'), true], [L('Una chiave primaria può contenere valori ripetuti', 'A primary key can contain repeated values'), false]], L('Le scritture rallentano un po’.', 'Writes get a bit slower.')),
        ]),
    ],
];
