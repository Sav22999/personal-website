<?php
/** MySQL: the server, utf8mb4, upserts, JSON columns, users, privileges and transactions. */

return [
    'id' => 'mysql', 'family' => 'mysql', 'after' => 'sql-base',
    'title' => L('MySQL', 'MySQL'), 'short' => L('MySQL', 'MySQL'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database più diffuso sul web: client, InnoDB, `utf8mb4`, upsert, colonne JSON, utenti, permessi e transazioni.', 'The most common database on the web: client, InnoDB, `utf8mb4`, upserts, JSON columns, users, privileges and transactions.'),
    'lessons' => [
        lesson('server', L('Server, client e database', 'Server, client and databases'), [
            P('MySQL è un **server**: un programma sempre acceso che custodisce i database e risponde a chi si collega. Ci si collega con il client a riga di comando, con phpMyAdmin o da un linguaggio come PHP o Python.', 'MySQL is a **server**: an always-on program that keeps the databases and answers whoever connects. You connect with the command-line client, with phpMyAdmin or from a language such as PHP or Python.'),
            C('mysql -u root -p

SHOW DATABASES;
CREATE DATABASE negozio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE negozio;
SHOW TABLES;
DESCRIBE prodotti;', 'sql'),
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
            P('In MySQL `utf8` è un nome storico che regge solo 3 byte per carattere: emoji e molti simboli non entrano. Usa sempre **`utf8mb4`**, il vero UTF-8.', 'In MySQL `utf8` is a historical name that only holds 3 bytes per character: emoji and many symbols do not fit. Always use **`utf8mb4`**, the real UTF-8.'),
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
            P('`INSERT IGNORE` salta in silenzio le righe che sarebbero doppie. `REPLACE` invece cancella la vecchia riga e ne inserisce una nuova: attenzione, perché cambia l’`AUTO_INCREMENT` e fa scattare le cancellazioni a cascata.', '`INSERT IGNORE` silently skips rows that would be duplicates. `REPLACE` instead deletes the old row and inserts a new one: careful, because it changes the `AUTO_INCREMENT` and triggers cascading deletes.'),
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
WHERE JSON_CONTAINS(dati->\'$.tag\', \'"sql"\');', 'sql'),
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
        ], [
            Q('p1', L('Che cosa significa `negozio.*` in un `GRANT`?', 'What does `negozio.*` mean in a `GRANT`?'), [L('Tutte le tabelle di `negozio`', 'Every table of `negozio`'), L('Tutti i database', 'Every database'), L('Tutte le colonne di una tabella', 'Every column of a table'), L('Tutti gli utenti', 'Every user')], 0, L('database.tabella, con `*` come jolly.', 'database.table, with `*` as wildcard.')),
            Q('p2', L('Che cosa fa `ROLLBACK`?', 'What does `ROLLBACK` do?'), [L('Annulla la transazione', 'Undoes the transaction'), L('Conferma la transazione', 'Confirms the transaction'), L('Riavvia il server', 'Restarts the server'), L('Cancella l’utente', 'Deletes the user')], 0, L('Come se non fosse successo niente.', 'As if nothing had happened.')),
            Q('p3', L('Perché l’app non deve usare `root`?', 'Why should the app not use `root`?'), [L('Per il minimo privilegio', 'For least privilege'), L('`root` è più lento', '`root` is slower'), L('`root` non può leggere', '`root` cannot read'), L('È vietato dalla licenza', 'The licence forbids it')], 0, L('Una falla non deve poter fare tutto.', 'A breach must not be able to do everything.')),
        ], task('sql', L('Dai all’utente `\'report\'@\'localhost\'` solo il permesso `SELECT` su tutte le tabelle del database `negozio`.', 'Give the user `\'report\'@\'localhost\'` only the `SELECT` privilege on every table of the database `negozio`.'), '', [re('GRANT\s+SELECT\s+ON\s+negozio\.\*', L('Solo lettura su negozio', 'Read only on negozio')), re('TO\s+\'report\'@\'localhost\'', L('All’utente giusto', 'To the right user'))], '', "GRANT SELECT ON negozio.* TO 'report'@'localhost';"), [
            dOrder(pSort(), ['START TRANSACTION;', 'UPDATE conti SET saldo = saldo - 50 WHERE id = 1;', 'UPDATE conti SET saldo = saldo + 50 WHERE id = 2;', 'COMMIT;'], L('Apri, sposta, conferma.', 'Open, move, confirm.')),
            dBlanks(pFill(), "CREATE ___ 'app'@'localhost' IDENTIFIED BY 'segreta';\n___ SELECT, INSERT ON negozio.* ___ 'app'@'localhost';", ['USER', 'GRANT', 'TO'], ['ROLE', 'ALLOW', 'FOR'], L('Creare l’utente e dargli i permessi.', 'Create the user and give it privileges.')),
        ]),
    ],
];
