<?php
/** SQL basics: tables, SELECT, WHERE, ORDER BY, changing data, NULL and CASE, functions and ALTER TABLE. */

return [
    'id' => 'sql-base', 'family' => 'sql', 'after' => null,
    'title' => L('SQL base', 'SQL Basics'), 'short' => L('SQL base', 'SQL Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Parlare con i database: tabelle, `SELECT`, filtri, ordinamento, inserimenti e modifiche, `NULL` e `CASE`, funzioni e `ALTER TABLE`.', 'Talking to databases: tables, `SELECT`, filters, sorting, inserts and updates, `NULL` and `CASE`, functions and `ALTER TABLE`.'),
    'lessons' => [
        lesson('tables', L('Tabelle e SELECT', 'Tables and SELECT'), [
            P('Un database relazionale tiene i dati in **tabelle**: righe (i record) e colonne (i campi). `SELECT` legge: dopo la parola si scrivono le colonne, dopo `FROM` la tabella.', 'A relational database keeps data in **tables**: rows (records) and columns (fields). `SELECT` reads: after the word you list the columns, after `FROM` the table.'),
            C('SELECT nome, email
FROM utenti;

SELECT * FROM corsi;', 'sql'),
            P('`*` significa tutte le colonne: comodo per esplorare, ma nel codice vero è meglio elencare solo quelle che servono. Le parole chiave si scrivono di solito in maiuscolo.', '`*` means every column: handy for exploring, but in real code it is better to list only the ones you need. Keywords are usually written in capitals.'),
        ], [
            Q('t1', L('Che cosa fa `SELECT`?', 'What does `SELECT` do?'), [L('Legge dati', 'Reads data'), L('Cancella dati', 'Deletes data'), L('Crea una tabella', 'Creates a table'), L('Cambia una password', 'Changes a password')], 0, L('È la domanda al database.', 'It is the question to the database.')),
            Q('t2', L('Che cosa significa `*`?', 'What does `*` mean?'), [L('Tutte le colonne', 'All columns'), L('Moltiplica', 'Multiply'), L('Nessuna colonna', 'No columns'), L('La prima colonna', 'The first column')], 0, L('Meglio elencarle nel codice vero.', 'Better list them in real code.')),
            Q('t3', L('Dopo quale parola si indica la tabella?', 'After which word do you name the table?'), ['FROM', 'IN', 'TABLE', 'AT'], 0, L('`SELECT … FROM tabella`.', '`SELECT … FROM table`.')),
        ], task('sql', L('Leggi le colonne `titolo` e `livello` dalla tabella `corsi`.', 'Read the columns `titolo` and `livello` from the table `corsi`.'), '', [re('SELECT\s+titolo\s*,\s*livello\s+FROM\s+corsi', L('La query è giusta', 'The query is right'))], '', 'SELECT titolo, livello FROM corsi;'), [
            dOrder(pSort(), ['SELECT nome, email', 'FROM utenti', 'WHERE attivo = 1', 'ORDER BY nome;'], L('Colonne, tabella, filtro, ordine.', 'Columns, table, filter, order.')),
            dMatch(pMatch(), [['SELECT', L('Legge', 'Reads')], ['FROM', L('Da quale tabella', 'From which table')], ['WHERE', L('Filtra le righe', 'Filters rows')], ['*', L('Tutte le colonne', 'All columns')], [';', L('Fine dell’istruzione', 'End of the statement')]], L('Il vocabolario di base.', 'The basic vocabulary.')),
        ]),
        lesson('where', L('Filtrare con WHERE', 'Filtering with WHERE'), [
            P('`WHERE` tiene solo le righe che rispettano una condizione. Si combinano con `AND` e `OR`; `LIKE` cerca nel testo con `%` come jolly, `IN` controlla un elenco, `BETWEEN` un intervallo.', '`WHERE` keeps only the rows that meet a condition. Combine with `AND` and `OR`; `LIKE` searches text with `%` as a wildcard, `IN` checks a list, `BETWEEN` a range.'),
            C('SELECT nome FROM utenti
WHERE citta = \'Udine\' AND eta >= 18;

SELECT titolo FROM corsi WHERE titolo LIKE \'%HTML%\';
SELECT * FROM ordini WHERE stato IN (\'nuovo\', \'pagato\');
SELECT * FROM voti WHERE voto BETWEEN 6 AND 8;', 'sql'),
            P('Il testo va tra apici singoli. Per cercare i valori mancanti si usa `IS NULL`, non `= NULL`.', 'Text goes between single quotes. To find missing values use `IS NULL`, not `= NULL`.'),
        ], [
            Q('w1', L('Che cosa trova `LIKE \'A%\'`?', 'What does `LIKE \'A%\'` find?'), [L('Testi che iniziano per A', 'Texts starting with A'), L('Testi che finiscono per A', 'Texts ending with A'), L('Solo «A»', 'Only “A”'), L('Testi senza A', 'Texts without A')], 0, L('`%` vale qualsiasi sequenza.', '`%` stands for any sequence.')),
            Q('w2', L('Come trovi i valori mancanti?', 'How do you find missing values?'), ['IS NULL', '= NULL', '== NULL', 'EMPTY'], 0, L('`NULL` non è uguale a nulla, nemmeno a sé stesso.', '`NULL` equals nothing, not even itself.')),
            Q('w3', L('`BETWEEN 6 AND 8` include il 6 e l’8?', 'Does `BETWEEN 6 AND 8` include 6 and 8?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo il 6', 'Only 6'), L('Solo l’8', 'Only 8')], 0, L('Gli estremi sono compresi.', 'The ends are included.')),
        ], task('sql', L('Dalla tabella `utenti` prendi il `nome` di chi ha `eta >= 18` e `citta = \'Roma\'`.', 'From the table `utenti` take the `nome` of those with `eta >= 18` and `citta = \'Roma\'`.'), '', [re('SELECT\s+nome\s+FROM\s+utenti\s+WHERE', L('C’è il filtro', 'There is the filter')), re('eta\s*>=\s*18', L('Maggiorenni', 'Adults')), re('AND\s+citta\s*=\s*\'Roma\'|citta\s*=\s*\'Roma\'\s+AND', L('Di Roma', 'From Rome'))], '', 'SELECT nome FROM utenti WHERE eta >= 18 AND citta = \'Roma\';'), [
            dBug(pBug(), ['SELECT nome', 'FROM utenti', 'WHERE telefono = NULL;'], 2, 'WHERE telefono IS NULL;', L('Con `NULL` si usa `IS NULL`.', 'With `NULL` you use `IS NULL`.')),
            dBlanks(pFill(), "SELECT titolo FROM corsi\n___ titolo ___ '%CSS%'\n___ livello IN ('base', 'intermedio');", ['WHERE', 'LIKE', 'AND'], ['IF', '=', 'OR NOT'], L('Filtro, ricerca nel testo, seconda condizione.', 'Filter, text search, second condition.')),
        ]),
        lesson('order', L('Ordinare e limitare', 'Sorting and limiting'), [
            P('`ORDER BY` ordina il risultato, crescente (`ASC`, il predefinito) o decrescente (`DESC`). `LIMIT` tiene solo le prime righe; `DISTINCT` toglie i doppioni.', '`ORDER BY` sorts the result, ascending (`ASC`, the default) or descending (`DESC`). `LIMIT` keeps only the first rows; `DISTINCT` removes duplicates.'),
            C('SELECT nome, punti FROM classifica
ORDER BY punti DESC, nome ASC
LIMIT 10;

SELECT DISTINCT citta FROM utenti;', 'sql'),
            P('L’ordine delle clausole è fisso: `SELECT`, `FROM`, `WHERE`, `ORDER BY`, `LIMIT`. `LIMIT` funziona in MySQL, MariaDB, PostgreSQL e SQLite; SQL Server usa `TOP` o `OFFSET … FETCH`, e lo standard SQL prevede `FETCH FIRST 10 ROWS ONLY`.', 'The order of the clauses is fixed: `SELECT`, `FROM`, `WHERE`, `ORDER BY`, `LIMIT`. `LIMIT` works in MySQL, MariaDB, PostgreSQL and SQLite; SQL Server uses `TOP` or `OFFSET … FETCH`, and standard SQL has `FETCH FIRST 10 ROWS ONLY`.'),
        ], [
            Q('o1', L('Come ordini dal più grande al più piccolo?', 'How do you sort from largest to smallest?'), ['DESC', 'ASC', 'DOWN', 'REVERSE'], 0, L('`ASC` è il contrario.', '`ASC` is the opposite.')),
            Q('o2', L('Che cosa fa `DISTINCT`?', 'What does `DISTINCT` do?'), [L('Toglie i doppioni', 'Removes duplicates'), L('Ordina', 'Sorts'), L('Conta', 'Counts'), L('Cancella righe', 'Deletes rows')], 0, L('Ogni valore una volta sola.', 'Each value once.')),
            Q('o3', L('Quale clausola viene per ultima?', 'Which clause comes last?'), ['LIMIT', 'WHERE', 'FROM', 'ORDER BY'], 0, L('Prima si ordina, poi si taglia.', 'First sort, then cut.')),
        ], task('sql', L('Prendi i 5 prodotti più cari: `ORDER BY prezzo DESC LIMIT 5`.', 'Take the 5 most expensive products: `ORDER BY prezzo DESC LIMIT 5`.'), 'SELECT nome, prezzo FROM prodotti', [re('ORDER\s+BY\s+prezzo\s+DESC', L('Ordini per prezzo decrescente', 'You sort by price descending')), re('LIMIT\s+5', L('Tieni i primi 5', 'You keep the first 5'))], '', 'SELECT nome, prezzo FROM prodotti ORDER BY prezzo DESC LIMIT 5;'), [
            dOrder(pSort(), ['SELECT titolo, voto', 'FROM recensioni', 'WHERE voto >= 4', 'ORDER BY voto DESC', 'LIMIT 3;'], L('L’ordine fisso delle clausole.', 'The fixed order of clauses.')),
            dFlash(pFlash(), [[L('`ASC` è l’ordine predefinito', '`ASC` is the default order'), true], [L('`LIMIT` va prima di `ORDER BY`', '`LIMIT` goes before `ORDER BY`'), false], [L('Si può ordinare per due colonne', 'You can sort by two columns'), true], [L('`DESC` ordina dal più grande al più piccolo', '`DESC` sorts from largest to smallest'), true]], L('Ordina, poi limita.', 'Sort, then limit.')),
        ]),
        lesson('changes', L('Inserire, modificare, cancellare', 'Insert, update, delete'), [
            P('`INSERT INTO` aggiunge righe, `UPDATE … SET` modifica, `DELETE FROM` cancella. Con `UPDATE` e `DELETE` il `WHERE` è fondamentale: senza, tocchi **tutte** le righe.', '`INSERT INTO` adds rows, `UPDATE … SET` changes them, `DELETE FROM` deletes. With `UPDATE` and `DELETE` the `WHERE` is vital: without it you touch **every** row.'),
            C('INSERT INTO corsi (titolo, livello) VALUES (\'SQL\', \'base\');

UPDATE utenti SET citta = \'Trieste\' WHERE id = 7;

DELETE FROM sessioni WHERE scadenza < CURRENT_TIMESTAMP;', 'sql'),
            P('Prima di un `DELETE`, prova lo stesso `WHERE` con un `SELECT` per vedere quali righe toccheresti.', 'Before a `DELETE`, try the same `WHERE` with a `SELECT` to see which rows you would touch.'),
        ], [
            Q('c1', L('Che cosa succede con `DELETE FROM utenti;` senza `WHERE`?', 'What happens with `DELETE FROM utenti;` and no `WHERE`?'), [L('Cancella tutte le righe', 'Deletes every row'), L('Niente', 'Nothing'), L('Un errore', 'An error'), L('Cancella la prima', 'Deletes the first')], 0, L('Attenzione massima.', 'Maximum care.')),
            Q('c2', L('Quale comando modifica righe esistenti?', 'Which command changes existing rows?'), ['UPDATE', 'INSERT', 'ALTER', 'CHANGE'], 0, L('`ALTER` cambia la struttura.', '`ALTER` changes the structure.')),
            Q('c3', L('Dove si mettono i valori in un `INSERT`?', 'Where do values go in an `INSERT`?'), [L('Dopo `VALUES`', 'After `VALUES`'), L('Dopo `WHERE`', 'After `WHERE`'), L('Dopo `SET`', 'After `SET`'), L('Dopo `FROM`', 'After `FROM`')], 0, L('Nello stesso ordine delle colonne.', 'In the same order as the columns.')),
        ], task('sql', L('Imposta `attivo = 0` per l’utente con `id = 3`.', 'Set `attivo = 0` for the user with `id = 3`.'), '', [re('UPDATE\s+utenti\s+SET\s+attivo\s*=\s*0', L('Modifichi il campo', 'You change the field')), re('WHERE\s+id\s*=\s*3', L('Solo quell’utente', 'Only that user'))], '', 'UPDATE utenti SET attivo = 0 WHERE id = 3;'), [
            dBug(pBug(), ['-- disattiva solo gli utenti di prova', 'UPDATE utenti', 'SET attivo = 0;'], 2, 'SET attivo = 0 WHERE email LIKE \'%@test.it\';', L('Senza `WHERE` disattivi tutti.', 'Without `WHERE` you disable everyone.')),
            dMatch(pMatch(), [['INSERT INTO', L('Aggiunge righe', 'Adds rows')], ['UPDATE', L('Modifica righe', 'Changes rows')], ['DELETE FROM', L('Cancella righe', 'Deletes rows')], ['SET', L('I nuovi valori', 'The new values')], ['VALUES', L('I valori da inserire', 'The values to insert')]], L('Scrivere nel database.', 'Writing to the database.')),
        ]),
        lesson('create', L('Creare tabelle', 'Creating tables'), [
            P('`CREATE TABLE` definisce una tabella: per ogni colonna un nome e un **tipo** (`INT`, `VARCHAR(100)`, `DATE`, `DECIMAL(8,2)`…). La **chiave primaria** identifica ogni riga in modo unico.', '`CREATE TABLE` defines a table: for each column a name and a **type** (`INT`, `VARCHAR(100)`, `DATE`, `DECIMAL(8,2)`…). The **primary key** identifies every row uniquely.'),
            C('CREATE TABLE corsi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titolo VARCHAR(120) NOT NULL,
    prezzo DECIMAL(8, 2) DEFAULT 0,
    creato_il DATE
);', 'sql'),
            P('`NOT NULL` obbliga a riempire il campo, `DEFAULT` dà un valore iniziale, `AUTO_INCREMENT` numera da solo le righe nuove. Attenzione: `AUTO_INCREMENT` è di MySQL e MariaDB; PostgreSQL usa `GENERATED ALWAYS AS IDENTITY` (lo standard), SQL Server `IDENTITY(1,1)`, SQLite una colonna `INTEGER PRIMARY KEY`.', '`NOT NULL` makes a field required, `DEFAULT` gives a starting value, `AUTO_INCREMENT` numbers new rows by itself. Careful: `AUTO_INCREMENT` belongs to MySQL and MariaDB; PostgreSQL uses `GENERATED ALWAYS AS IDENTITY` (the standard), SQL Server `IDENTITY(1,1)`, SQLite an `INTEGER PRIMARY KEY` column.'),
        ], [
            Q('k1', L('A che cosa serve la chiave primaria?', 'What is the primary key for?'), [L('Identifica ogni riga in modo unico', 'Identifies every row uniquely'), L('Cifra la tabella', 'Encrypts the table'), L('Ordina le righe', 'Sorts the rows'), L('Apre la tabella', 'Opens the table')], 0, L('Non si ripete mai.', 'It never repeats.')),
            Q('k2', L('Quale tipo per un prezzo con centesimi?', 'Which type for a price with cents?'), ['DECIMAL(8,2)', 'INT', 'VARCHAR(8)', 'DATE'], 0, L('Precisione esatta, niente arrotondamenti.', 'Exact precision, no rounding.')),
            Q('k3', L('Che cosa fa `NOT NULL`?', 'What does `NOT NULL` do?'), [L('Rende obbligatorio il campo', 'Makes the field required'), L('Lo rende vuoto', 'Makes it empty'), L('Lo cancella', 'Deletes it'), L('Lo rende unico', 'Makes it unique')], 0, L('`UNIQUE` lo rende unico.', '`UNIQUE` makes it unique.')),
        ], task('sql', L('Crea la tabella `note` con `id INT PRIMARY KEY` e `testo VARCHAR(200) NOT NULL`.', 'Create the table `note` with `id INT PRIMARY KEY` and `testo VARCHAR(200) NOT NULL`.'), '', [re('CREATE\s+TABLE\s+note\s*\(', L('La tabella esiste', 'The table exists')), re('id\s+INT\s+PRIMARY\s+KEY', L('C’è la chiave primaria', 'There is the primary key')), re('testo\s+VARCHAR\s*\(\s*200\s*\)\s+NOT\s+NULL', L('Il testo è obbligatorio', 'The text is required'))], '', 'CREATE TABLE note (
    id INT PRIMARY KEY,
    testo VARCHAR(200) NOT NULL
);'), [
            dBlanks(pFill(), "CREATE ___ utenti (\n    id INT ___ KEY AUTO_INCREMENT,\n    email VARCHAR(190) NOT ___ UNIQUE\n);", ['TABLE', 'PRIMARY', 'NULL'], ['DATABASE', 'MAIN', 'EMPTY'], L('Tabella, chiave, obbligo.', 'Table, key, requirement.')),
            dMatch(pMatch(), [['INT', L('Intero', 'Integer')], ['VARCHAR(50)', L('Testo fino a 50', 'Text up to 50')], ['DATE', L('Data', 'Date')], ['DECIMAL(8,2)', L('Numero con decimali esatti', 'Exact decimal number')], ['BOOLEAN', L('Vero o falso', 'True or false')]], L('I tipi delle colonne.', 'Column types.')),
        ]),
        lesson('null-case', L('NULL, COALESCE e CASE', 'NULL, COALESCE and CASE'), [
            P('`NULL` significa «valore sconosciuto». Ogni calcolo o confronto con `NULL` dà `NULL`: `prezzo + NULL` è `NULL`, e `WHERE sconto = NULL` non trova niente. `COALESCE(a, b, …)` restituisce il primo valore che non è `NULL`: perfetto per i valori di riserva.', '`NULL` means “unknown value”. Every calculation or comparison with `NULL` gives `NULL`: `prezzo + NULL` is `NULL`, and `WHERE sconto = NULL` finds nothing. `COALESCE(a, b, …)` returns the first value that is not `NULL`: perfect for fallback values.'),
            C('SELECT nome, COALESCE(telefono, \'non indicato\') AS telefono
FROM utenti;

SELECT titolo, prezzo,
       CASE
           WHEN prezzo = 0 THEN \'gratis\'
           WHEN prezzo < 20 THEN \'economico\'
           ELSE \'premium\'
       END AS fascia
FROM corsi;', 'sql'),
            P('`CASE` è il «se… allora» di SQL: controlla i `WHEN` in ordine e restituisce il valore del primo vero; `ELSE` copre tutti gli altri casi (senza `ELSE` il risultato è `NULL`). Si usa in `SELECT`, in `ORDER BY` e anche dentro un `UPDATE`.', '`CASE` is SQL’s “if… then”: it checks the `WHEN`s in order and returns the value of the first true one; `ELSE` covers every other case (without `ELSE` the result is `NULL`). You use it in `SELECT`, in `ORDER BY` and even inside an `UPDATE`.'),
        ], [
            Q('n1', L('Che cosa restituisce `COALESCE(NULL, NULL, \'x\')`?', 'What does `COALESCE(NULL, NULL, \'x\')` return?'), ['NULL', '\'x\'', L('Un errore', 'An error'), L('Un testo vuoto', 'An empty text')], 1, L('Il primo valore che non è `NULL`.', 'The first value that is not `NULL`.')),
            Q('n2', L('Quanto vale `10 + NULL`?', 'What is `10 + NULL`?'), ['10', '0', 'NULL', L('Un errore', 'An error')], 2, L('Un valore sconosciuto rende sconosciuto il risultato.', 'An unknown value makes the result unknown.')),
            Q('n3', L('Che cosa dà un `CASE` senza `ELSE` se nessun `WHEN` è vero?', 'What does a `CASE` without `ELSE` give if no `WHEN` is true?'), [L('Il valore del primo `WHEN`', 'The value of the first `WHEN`'), L('Il numero zero', 'The number zero'), L('Un errore di sintassi', 'A syntax error'), 'NULL'], 3, L('Per questo conviene scrivere sempre un `ELSE`.', 'That is why it pays to always write an `ELSE`.')),
        ], task('sql', L('Dalla tabella `prodotti` seleziona `nome` e una colonna `disponibile` che vale `\'sì\'` se `pezzi > 0`, altrimenti `\'no\'`, usando `CASE`.', 'From the table `prodotti` select `nome` and a column `disponibile` that is `\'sì\'` if `pezzi > 0`, otherwise `\'no\'`, using `CASE`.'), 'SELECT nome FROM prodotti;', [re('CASE\s+WHEN\s+pezzi\s*(>\s*0|>=\s*1)\s+THEN\s+\'s(i|ì)\'\s+ELSE\s+\'no\'\s+END', L('Il CASE sceglie sì o no', 'The CASE picks yes or no')), re('END\s+(AS\s+)?disponibile', L('La colonna si chiama disponibile', 'The column is called disponibile')), re('FROM\s+prodotti', L('Dalla tabella giusta', 'From the right table'))], '', "SELECT nome, CASE WHEN pezzi > 0 THEN 'sì' ELSE 'no' END AS disponibile\nFROM prodotti;"), [
            dBlanks(pFill(), "SELECT nome,\n       ___(soprannome, nome) AS chiamalo,\n       ___ WHEN eta >= 18 THEN 'adulto' ___ 'minore' END AS gruppo\nFROM utenti;", ['COALESCE', 'CASE', 'ELSE'], ['IFNULLS', 'IF', 'OTHERWISE'], L('Valore di riserva, poi una scelta con un caso finale.', 'Fallback value, then a choice with a final case.')),
            dFlash(pFlash(), [[L('`NULL = NULL` è vero', '`NULL = NULL` is true'), false], [L('`COALESCE` restituisce il primo valore non `NULL`', '`COALESCE` returns the first non-`NULL` value'), true], [L('`CASE` controlla i `WHEN` in ordine', '`CASE` checks the `WHEN`s in order'), true], [L('Un `CASE` senza `ELSE` dà sempre errore', 'A `CASE` without `ELSE` always errors'), false]], L('`NULL` è sconosciuto, `CASE` sceglie.', '`NULL` is unknown, `CASE` chooses.')),
        ]),
        lesson('functions', L('Funzioni su testi, numeri e date', 'Text, number and date functions'), [
            P('Le funzioni trasformano i valori riga per riga: `UPPER` e `LOWER` cambiano le maiuscole, `LENGTH` misura un testo (in SQL Server `LEN`), `TRIM` toglie gli spazi ai lati, `ROUND(x, 2)` arrotonda a due decimali, `CURRENT_DATE` dà la data di oggi.', 'Functions transform values row by row: `UPPER` and `LOWER` change the case, `LENGTH` measures a text (`LEN` in SQL Server), `TRIM` removes spaces at both ends, `ROUND(x, 2)` rounds to two decimals, `CURRENT_DATE` gives today’s date.'),
            C('SELECT UPPER(cognome) AS cognome,
       LENGTH(nome) AS lettere,
       CONCAT(nome, \' \', cognome) AS completo
FROM clienti;

SELECT nome, ROUND(prezzo * 1.22, 2) AS con_iva FROM prodotti;

SELECT CURRENT_DATE;', 'sql'),
            P('Unire testi è il punto in cui i dialetti divergono di più: lo standard usa `||` (PostgreSQL, SQLite, Oracle), mentre in MySQL `||` è un OR logico. `CONCAT(…)` invece esiste quasi ovunque. Anche le funzioni sulle date cambiano da un database all’altro: controlla sempre la documentazione del tuo.', 'Joining text is where dialects differ most: the standard uses `||` (PostgreSQL, SQLite, Oracle), while in MySQL `||` is a logical OR. `CONCAT(…)` instead exists almost everywhere. Date functions also change from one database to another: always check your own database’s documentation.'),
        ], [
            Q('f1', L('Che cosa fa `TRIM(\'  ciao  \')`?', 'What does `TRIM(\'  ciao  \')` do?'), [L('Mette tutto il testo in maiuscolo', 'Puts the whole text in capitals'), L('Toglie gli spazi all’inizio e alla fine', 'Removes the spaces at the start and end'), L('Conta le lettere del testo', 'Counts the letters of the text'), L('Taglia il testo a metà', 'Cuts the text in half')], 1, L('Restituisce `\'ciao\'`.', 'It returns `\'ciao\'`.')),
            Q('f2', L('Che cosa dà `ROUND(3.14159, 2)`?', 'What does `ROUND(3.14159, 2)` give?'), ['3', '3.15', '3.1416', '3.14'], 3, L('Due cifre dopo la virgola.', 'Two digits after the decimal point.')),
            Q('f3', L('Quale modo di unire due testi funziona nella maggior parte dei database?', 'Which way of joining two texts works in most databases?'), ['CONCAT(a, b)', 'JOIN(a, b)', 'MERGE(a, b)', 'a & b'], 0, L('`||` è standard, ma in MySQL significa OR.', '`||` is standard, but in MySQL it means OR.')),
        ], task('sql', L('Da `clienti` seleziona `UPPER(cognome)` con alias `cognome_maiuscolo` e la lunghezza di `nome` con alias `lettere`.', 'From `clienti` select `UPPER(cognome)` with alias `cognome_maiuscolo` and the length of `nome` with alias `lettere`.'), '', [re('UPPER\s*\(\s*cognome\s*\)\s+(AS\s+)?cognome_maiuscolo', L('Il cognome in maiuscolo', 'The surname in capitals')), re('(LENGTH|LEN|CHAR_LENGTH)\s*\(\s*nome\s*\)\s+(AS\s+)?lettere', L('La lunghezza del nome', 'The length of the name')), re('FROM\s+clienti', L('Dalla tabella giusta', 'From the right table'))], '', 'SELECT UPPER(cognome) AS cognome_maiuscolo, LENGTH(nome) AS lettere
FROM clienti;'), [
            dMatch(pMatch(), [['UPPER', L('Tutto maiuscolo', 'All capitals')], ['LOWER', L('Tutto minuscolo', 'All lowercase')], ['LENGTH', L('Quanti caratteri', 'How many characters')], ['TRIM', L('Toglie gli spazi ai lati', 'Removes spaces at the ends')], ['ROUND', L('Arrotonda', 'Rounds')], ['CURRENT_DATE', L('La data di oggi', 'Today’s date')]], L('Le funzioni più usate.', 'The most used functions.')),
            dBug(pBug(), ['SELECT nome,', '       ROUND(prezzo * 1.22; 2) AS con_iva', 'FROM prodotti;'], 1, '       ROUND(prezzo * 1.22, 2) AS con_iva', L('In SQL gli argomenti si separano con la virgola: il punto e virgola chiude l’istruzione.', 'In SQL arguments are separated by commas: the semicolon ends the statement.')),
        ]),
        lesson('alter', L('Modificare la struttura', 'Changing the structure'), [
            P('`ALTER TABLE` cambia una tabella esistente senza perderne i dati: aggiunge o toglie colonne e aggiunge vincoli. `DROP TABLE` cancella la tabella intera, struttura compresa; `TRUNCATE TABLE` la svuota in fretta ma la lascia esistere.', '`ALTER TABLE` changes an existing table without losing its data: it adds or removes columns and adds constraints. `DROP TABLE` deletes the whole table, structure included; `TRUNCATE TABLE` empties it quickly but leaves it in place.'),
            C('ALTER TABLE utenti ADD COLUMN telefono VARCHAR(20);
ALTER TABLE utenti ADD CONSTRAINT uq_utenti_email UNIQUE (email);
ALTER TABLE prodotti ADD CONSTRAINT ck_prezzo CHECK (prezzo >= 0);
ALTER TABLE utenti DROP COLUMN fax;

TRUNCATE TABLE log_visite;
DROP TABLE IF EXISTS vecchia_tabella;', 'sql'),
            P('I **vincoli** proteggono i dati: `UNIQUE` vieta i doppioni, `CHECK` rifiuta i valori che non rispettano una regola. `IF EXISTS` evita l’errore se la tabella non c’è. I dialetti variano: SQL Server scrive `ADD telefono …` senza `COLUMN`, e SQLite accetta solo alcune modifiche (aggiungere, rinominare o togliere colonne).', '**Constraints** protect data: `UNIQUE` forbids duplicates, `CHECK` rejects values that break a rule. `IF EXISTS` avoids the error if the table is not there. Dialects vary: SQL Server writes `ADD telefono …` without `COLUMN`, and SQLite accepts only some changes (adding, renaming or dropping columns).'),
            Tip('Prima di un `DROP` o di un `TRUNCATE` fai un backup: non c’è un cestino da cui recuperare.', 'Before a `DROP` or a `TRUNCATE` take a backup: there is no recycle bin to recover from.'),
        ], [
            Q('a1', L('Quale comando aggiunge una colonna a una tabella esistente?', 'Which command adds a column to an existing table?'), ['CREATE TABLE … ADD', 'ALTER TABLE … ADD COLUMN', 'UPDATE … SET COLUMN', 'INSERT INTO … COLUMN'], 1, L('`ALTER` cambia la struttura, `UPDATE` i dati.', '`ALTER` changes the structure, `UPDATE` the data.')),
            Q('a2', L('Che differenza c’è tra `TRUNCATE` e `DROP`?', 'What is the difference between `TRUNCATE` and `DROP`?'), [L('`TRUNCATE` elimina la tabella, `DROP` la svuota soltanto', '`TRUNCATE` removes the table, `DROP` only empties it'), L('Sono due nomi dello stesso identico comando', 'They are two names for the very same command'), L('`TRUNCATE` cancella solo le colonne vuote', '`TRUNCATE` deletes only the empty columns'), L('`TRUNCATE` svuota la tabella, `DROP` la elimina del tutto', '`TRUNCATE` empties the table, `DROP` removes it entirely')], 3, L('Dopo `TRUNCATE` la tabella c’è ancora, vuota.', 'After `TRUNCATE` the table is still there, empty.')),
            Q('a3', L('Che cosa fa `CHECK (prezzo >= 0)`?', 'What does `CHECK (prezzo >= 0)` do?'), [L('Controlla i prezzi ogni notte', 'Checks the prices every night'), L('Arrotonda a zero i prezzi negativi', 'Rounds negative prices to zero'), L('Rifiuta i prezzi negativi', 'Rejects negative prices'), L('Ordina i prezzi dal più basso', 'Sorts prices from the lowest')], 2, L('Un inserimento con prezzo negativo dà errore.', 'An insert with a negative price errors out.')),
        ], task('sql', L('Aggiungi alla tabella `corsi` la colonna `durata INT` e poi un vincolo `CHECK (durata > 0)`.', 'Add the column `durata INT` to the table `corsi`, then a `CHECK (durata > 0)` constraint.'), '', [re('ALTER\s+TABLE\s+corsi\s+ADD\s+(COLUMN\s+)?durata\s+INT', L('Aggiungi la colonna', 'You add the column')), re('CHECK\s*\(\s*durata\s*>\s*0\s*\)', L('La durata deve essere positiva', 'The duration must be positive'))], '', "ALTER TABLE corsi ADD COLUMN durata INT;\nALTER TABLE corsi ADD CONSTRAINT ck_durata CHECK (durata > 0);"), [
            dMatch(pMatch(), [['ALTER TABLE', L('Modifica la struttura', 'Changes the structure')], ['DROP TABLE', L('Elimina la tabella', 'Removes the table')], ['TRUNCATE', L('Svuota la tabella', 'Empties the table')], ['UNIQUE', L('Vieta i doppioni', 'Forbids duplicates')], ['CHECK', L('Impone una regola', 'Enforces a rule')]], L('Cambiare le tabelle con giudizio.', 'Changing tables wisely.')),
            dFlash(pFlash(), [[L('`DROP TABLE` cancella anche la struttura', '`DROP TABLE` also deletes the structure'), true], [L('`ALTER TABLE` cancella sempre i dati', '`ALTER TABLE` always deletes the data'), false], [L('`IF EXISTS` evita l’errore se la tabella non c’è', '`IF EXISTS` avoids the error if the table is missing'), true], [L('Dopo `TRUNCATE` la tabella esiste ancora', 'After `TRUNCATE` the table still exists'), true]], L('Struttura e dati sono cose diverse.', 'Structure and data are different things.')),
        ]),
    ],
];
