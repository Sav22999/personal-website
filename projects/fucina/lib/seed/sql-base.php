<?php
/** SQL basics: tables, SELECT, WHERE, ORDER BY and changing data. */

return [
    'id' => 'sql-base', 'family' => 'sql', 'after' => null,
    'title' => L('SQL base', 'SQL Basics'), 'short' => L('SQL base', 'SQL Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Parlare con i database: tabelle, `SELECT`, filtri, ordinamento, inserimenti e modifiche.', 'Talking to databases: tables, `SELECT`, filters, sorting, inserts and updates.'),
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
            Q('w1', L('Che cosa trova `LIKE \'A%\'`?', 'What does `LIKE \'A%\'` find?'), [L('Testi che iniziano per A', 'Texts starting with A'), L('Testi che finiscono per A', 'Texts ending with A'), L('Solo “A”', 'Only “A”'), L('Testi senza A', 'Texts without A')], 0, L('`%` vale qualsiasi sequenza.', '`%` stands for any sequence.')),
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
            P('L’ordine delle clausole è fisso: `SELECT`, `FROM`, `WHERE`, `ORDER BY`, `LIMIT`.', 'The order of the clauses is fixed: `SELECT`, `FROM`, `WHERE`, `ORDER BY`, `LIMIT`.'),
        ], [
            Q('o1', L('Come ordini dal più grande al più piccolo?', 'How do you sort from largest to smallest?'), ['DESC', 'ASC', 'DOWN', 'REVERSE'], 0, L('`ASC` è il contrario.', '`ASC` is the opposite.')),
            Q('o2', L('Che cosa fa `DISTINCT`?', 'What does `DISTINCT` do?'), [L('Toglie i doppioni', 'Removes duplicates'), L('Ordina', 'Sorts'), L('Conta', 'Counts'), L('Cancella righe', 'Deletes rows')], 0, L('Ogni valore una volta sola.', 'Each value once.')),
            Q('o3', L('Quale clausola viene per ultima?', 'Which clause comes last?'), ['LIMIT', 'WHERE', 'FROM', 'ORDER BY'], 0, L('Prima si ordina, poi si taglia.', 'First sort, then cut.')),
        ], task('sql', L('Prendi i 5 prodotti più cari: `ORDER BY prezzo DESC LIMIT 5`.', 'Take the 5 most expensive products: `ORDER BY prezzo DESC LIMIT 5`.'), 'SELECT nome, prezzo FROM prodotti', [re('ORDER\s+BY\s+prezzo\s+DESC', L('Ordini per prezzo decrescente', 'You sort by price descending')), re('LIMIT\s+5', L('Tieni i primi 5', 'You keep the first 5'))], '', 'SELECT nome, prezzo FROM prodotti ORDER BY prezzo DESC LIMIT 5;'), [
            dOrder(pSort(), ['SELECT titolo, voto', 'FROM recensioni', 'WHERE voto >= 4', 'ORDER BY voto DESC', 'LIMIT 3;'], L('L’ordine fisso delle clausole.', 'The fixed order of clauses.')),
            dFlash(pFlash(), [[L('`ASC` è l’ordine predefinito', '`ASC` is the default order'), true], [L('`LIMIT` va prima di `ORDER BY`', '`LIMIT` goes before `ORDER BY`'), false], [L('Si può ordinare per due colonne', 'You can sort by two columns'), true]], L('Ordina, poi limita.', 'Sort, then limit.')),
        ]),
        lesson('changes', L('Inserire, modificare, cancellare', 'Insert, update, delete'), [
            P('`INSERT INTO` aggiunge righe, `UPDATE … SET` modifica, `DELETE FROM` cancella. Con `UPDATE` e `DELETE` il `WHERE` è fondamentale: senza, tocchi **tutte** le righe.', '`INSERT INTO` adds rows, `UPDATE … SET` changes them, `DELETE FROM` deletes. With `UPDATE` and `DELETE` the `WHERE` is vital: without it you touch **every** row.'),
            C('INSERT INTO corsi (titolo, livello) VALUES (\'SQL\', \'base\');

UPDATE utenti SET citta = \'Trieste\' WHERE id = 7;

DELETE FROM sessioni WHERE scadenza < NOW();', 'sql'),
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
            P('`NOT NULL` obbliga a riempire il campo, `DEFAULT` dà un valore iniziale, `AUTO_INCREMENT` numera da solo le righe nuove.', '`NOT NULL` makes a field required, `DEFAULT` gives a starting value, `AUTO_INCREMENT` numbers new rows by itself.'),
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
    ],
];
