<?php
/** MariaDB: the open fork of MySQL and what it adds: RETURNING, sequences, system-versioned tables, engines and Galera. */

return [
    'id' => 'mariadb', 'family' => 'mariadb', 'after' => 'mysql',
    'title' => L('MariaDB', 'MariaDB'), 'short' => L('MariaDB', 'MariaDB'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il fork libero di MySQL: compatibilità, `RETURNING`, sequenze, tabelle con la storia dei dati, motori e cluster Galera.', 'The open fork of MySQL: compatibility, `RETURNING`, sequences, tables that keep data history, engines and Galera clusters.'),
    'lessons' => [
        lesson('fork', L('Un fork di MySQL', 'A fork of MySQL'), [
            P('MariaDB nasce nel 2009 dagli sviluppatori originali di MySQL, dopo l’acquisizione di MySQL da parte di Oracle. Resta **compatibile** in gran parte: stesso protocollo, stesso client, stessa sintassi di base, e molte distribuzioni Linux la installano al posto di MySQL.', 'MariaDB was born in 2009 from MySQL’s original developers, after Oracle acquired MySQL. It stays largely **compatible**: same protocol, same client, same basic syntax, and many Linux distributions install it in place of MySQL.'),
            C('mariadb -u root -p

SELECT VERSION();          -- 11.4.x-MariaDB
SHOW ENGINES;
SELECT @@version_comment;', 'sql'),
            P('Con il tempo le due strade si sono separate: MariaDB ha funzioni sue (sequenze, tabelle versionate, motori in più), MySQL altre. I connettori di PHP, Python e Java funzionano con entrambi.', 'Over time the two paths drifted apart: MariaDB has features of its own (sequences, versioned tables, extra engines), MySQL others. PHP, Python and Java connectors work with both.'),
            Tip('Su un hosting condiviso “MySQL” spesso è in realtà MariaDB: `SELECT VERSION();` lo rivela.', 'On shared hosting “MySQL” is often MariaDB in disguise: `SELECT VERSION();` gives it away.'),
        ], [
            Q('f1', L('Chi ha creato MariaDB?', 'Who created MariaDB?'), [L('Gli sviluppatori originali di MySQL', 'MySQL’s original developers'), L('Oracle', 'Oracle'), L('Microsoft', 'Microsoft'), L('Il team di PostgreSQL', 'The PostgreSQL team')], 0, L('Dopo l’acquisizione di MySQL da parte di Oracle.', 'After Oracle acquired MySQL.')),
            Q('f2', L('Come scopri se il server è MariaDB?', 'How do you find out whether the server is MariaDB?'), ['SELECT VERSION();', 'SHOW USERS;', 'DESCRIBE server;', 'SELECT ENGINE();'], 0, L('La versione contiene “MariaDB”.', 'The version contains “MariaDB”.')),
            Q('f3', L('Il connettore MySQL di PHP funziona con MariaDB?', 'Does PHP’s MySQL connector work with MariaDB?'), [L('Sì, il protocollo è lo stesso', 'Yes, the protocol is the same'), L('No, serve un driver diverso', 'No, it needs another driver'), L('Solo in sola lettura', 'Only read only'), L('Solo su Windows', 'Only on Windows')], 0, L('PDO e mysqli si collegano senza cambiare nulla.', 'PDO and mysqli connect with no changes.')),
        ], task('sql', L('Mostra la versione del server con `SELECT VERSION();` e poi l’elenco dei motori con `SHOW ENGINES;`.', 'Show the server version with `SELECT VERSION();` and then the list of engines with `SHOW ENGINES;`.'), '', [re('SELECT\s+VERSION\s*\(\s*\)\s*;', L('Chiedi la versione', 'You ask for the version')), re('SHOW\s+ENGINES\s*;', L('Elenchi i motori', 'You list the engines'))], '', "SELECT VERSION();\nSHOW ENGINES;"), [
            dMatch(pMatch(), [['mariadb', L('Il client a riga di comando', 'The command-line client')], ['VERSION()', L('La versione del server', 'The server version')], ['SHOW ENGINES', L('I motori disponibili', 'The available engines')], ['2009', L('L’anno del fork', 'The year of the fork')]], L('Conoscere il proprio server.', 'Knowing your own server.')),
            dFlash(pFlash(), [[L('MariaDB usa lo stesso protocollo di MySQL', 'MariaDB uses the same protocol as MySQL'), true], [L('MariaDB è un prodotto di Oracle', 'MariaDB is an Oracle product'), false], [L('Molte distribuzioni Linux installano MariaDB', 'Many Linux distributions install MariaDB'), true], [L('MariaDB e MySQL hanno oggi funzioni identiche', 'MariaDB and MySQL have identical features today'), false]], L('Parenti stretti, non gemelli.', 'Close relatives, not twins.')),
        ]),
        lesson('returning', L('INSERT … RETURNING', 'INSERT … RETURNING'), [
            P('Dopo un inserimento spesso serve l’id appena creato. MariaDB permette di farselo restituire subito con **`RETURNING`**, anche su `DELETE`: niente seconda query.', 'After an insert you often need the id just created. MariaDB lets you get it back at once with **`RETURNING`**, on `DELETE` too: no second query.'),
            C('INSERT INTO utenti (nome, email)
VALUES (\'Ada\', \'ada@example.com\')
RETURNING id, creato_il;

DELETE FROM sessioni WHERE scadenza < NOW()
RETURNING utente_id;', 'sql'),
            P('In MySQL lo stesso si ottiene con `LAST_INSERT_ID()` subito dopo l’`INSERT`, ma vale solo per l’`AUTO_INCREMENT` e solo per l’ultima riga.', 'In MySQL you get the same with `LAST_INSERT_ID()` right after the `INSERT`, but it only covers the `AUTO_INCREMENT`, and only the last row.'),
        ], [
            Q('r1', L('Che cosa aggiunge `RETURNING` a un `INSERT`?', 'What does `RETURNING` add to an `INSERT`?'), [L('Restituisce le colonne delle righe inserite', 'Returns the columns of the inserted rows'), L('Annulla l’inserimento', 'Undoes the insert'), L('Inserisce due volte', 'Inserts twice'), L('Ordina la tabella', 'Sorts the table')], 0, L('Niente query in più per leggere l’id.', 'No extra query to read the id.')),
            Q('r2', L('Si può usare `RETURNING` con `DELETE`?', 'Can you use `RETURNING` with `DELETE`?'), [L('Sì', 'Yes'), L('No', 'No'), L('Solo con `LIMIT 1`', 'Only with `LIMIT 1`'), L('Solo da root', 'Only as root')], 0, L('Vedi cosa hai cancellato.', 'You see what you deleted.')),
            Q('r3', L('Qual è l’alternativa classica di MySQL per l’ultimo id?', 'What is MySQL’s classic alternative for the last id?'), ['LAST_INSERT_ID()', 'NEXT_ID()', 'CURRENT_ROW()', 'MAX_ID()'], 0, L('Vale per l’`AUTO_INCREMENT`.', 'It covers the `AUTO_INCREMENT`.')),
        ], task('sql', L('Inserisci in `corsi (titolo)` il valore `\'MariaDB\'` e fatti restituire `id`.', 'Insert into `corsi (titolo)` the value `\'MariaDB\'` and get back `id`.'), '', [re('INSERT\s+INTO\s+corsi\s*\(\s*titolo\s*\)\s*VALUES\s*\(\s*\'MariaDB\'\s*\)', L('Inserisci il corso', 'You insert the course')), re('RETURNING\s+id', L('Ti fai restituire l’id', 'You get the id back'))], '', "INSERT INTO corsi (titolo) VALUES ('MariaDB') RETURNING id;"), [
            dBlanks(pFill(), "INSERT INTO ordini (cliente_id, totale)\nVALUES (5, 49.90)\n___ id, creato_il;", ['RETURNING'], ['OUTPUT', 'RETURN', 'LAST_INSERT_ID'], L('Le colonne tornano subito indietro.', 'The columns come straight back.')),
            dBug(pBug(), ['DELETE FROM carrelli', 'WHERE aggiornato_il < NOW() - INTERVAL 30 DAY', 'RETURN id;'], 2, 'RETURNING id;', L('La parola chiave è `RETURNING`, in fondo all’istruzione.', 'The keyword is `RETURNING`, at the end of the statement.')),
        ]),
        lesson('sequences', L('Sequenze', 'Sequences'), [
            P('Una **sequenza** è un contatore indipendente dalle tabelle: ogni `NEXTVAL` dà il numero successivo, anche a più tabelle o a più server. Utile per numeri di fattura o codici condivisi.', 'A **sequence** is a counter independent of tables: every `NEXTVAL` gives the next number, even to several tables or servers. Handy for invoice numbers or shared codes.'),
            C('CREATE SEQUENCE fatture START WITH 1000 INCREMENT BY 1;

SELECT NEXTVAL(fatture);   -- 1000
SELECT NEXTVAL(fatture);   -- 1001
SELECT LASTVAL(fatture);   -- 1001

CREATE TABLE fattura (
    numero INT DEFAULT (NEXTVAL(fatture)) PRIMARY KEY,
    cliente VARCHAR(100)
);', 'sql'),
            P('`LASTVAL` legge l’ultimo valore dato nella tua sessione senza avanzare. A differenza dell’`AUTO_INCREMENT`, la sequenza non appartiene a una colonna.', '`LASTVAL` reads the last value given in your session without advancing. Unlike `AUTO_INCREMENT`, the sequence does not belong to a column.'),
        ], [
            Q('q1', L('Che cosa fa `NEXTVAL(fatture)`?', 'What does `NEXTVAL(fatture)` do?'), [L('Dà il numero successivo', 'Gives the next number'), L('Legge senza avanzare', 'Reads without advancing'), L('Azzera la sequenza', 'Resets the sequence'), L('Cancella la sequenza', 'Drops the sequence')], 0, L('Ogni chiamata avanza.', 'Every call advances.')),
            Q('q2', L('Che cosa distingue una sequenza dall’`AUTO_INCREMENT`?', 'What sets a sequence apart from `AUTO_INCREMENT`?'), [L('Non appartiene a una colonna', 'It does not belong to a column'), L('È più lenta', 'It is slower'), L('Conta all’indietro', 'It counts backwards'), L('Funziona solo con i testi', 'It only works with text')], 0, L('Si può condividere tra tabelle.', 'It can be shared between tables.')),
            Q('q3', L('Che cosa fa `LASTVAL`?', 'What does `LASTVAL` do?'), [L('Legge l’ultimo valore dato, senza avanzare', 'Reads the last value given, without advancing'), L('Dà il valore massimo possibile', 'Gives the highest possible value'), L('Avanza di due', 'Advances by two'), L('Chiude la sequenza', 'Closes the sequence')], 0, L('Nella tua sessione.', 'In your session.')),
        ], task('sql', L('Crea la sequenza `tessere` che parte da `500` e avanza di `10`.', 'Create the sequence `tessere` starting at `500` and advancing by `10`.'), '', [re('CREATE\s+SEQUENCE\s+tessere', L('La sequenza esiste', 'The sequence exists')), re('START\s+WITH\s+500', L('Parte da 500', 'It starts at 500')), re('INCREMENT\s+BY\s+10', L('Avanza di 10', 'It advances by 10'))], '', 'CREATE SEQUENCE tessere START WITH 500 INCREMENT BY 10;'), [
            dOrder(pSort(), ['CREATE SEQUENCE ticket START WITH 1;', 'SELECT NEXTVAL(ticket);', 'SELECT NEXTVAL(ticket);', 'SELECT LASTVAL(ticket);'], L('Crea, avanza, avanza, rileggi.', 'Create, advance, advance, read again.')),
            dMatch(pMatch(), [['CREATE SEQUENCE', L('Crea il contatore', 'Creates the counter')], ['START WITH', L('Il primo valore', 'The first value')], ['INCREMENT BY', L('Il passo', 'The step')], ['NEXTVAL', L('Prende il prossimo', 'Takes the next one')], ['LASTVAL', L('Rilegge l’ultimo', 'Reads the last one again')]], L('Il vocabolario delle sequenze.', 'The vocabulary of sequences.')),
        ]),
        lesson('versioning', L('Tabelle con la storia', 'Tables with history'), [
            P('Con `WITH SYSTEM VERSIONING` MariaDB tiene da sola **tutte le versioni** di ogni riga: chi aggiorna o cancella non perde il passato. Poi si interroga la tabella com’era in un momento preciso.', 'With `WITH SYSTEM VERSIONING` MariaDB keeps **every version** of each row by itself: updates and deletes do not lose the past. Then you query the table as it was at a given moment.'),
            C('CREATE TABLE prezzi (
    prodotto_id INT PRIMARY KEY,
    prezzo DECIMAL(8, 2)
) WITH SYSTEM VERSIONING;

UPDATE prezzi SET prezzo = 24.90 WHERE prodotto_id = 1;

SELECT * FROM prezzi
FOR SYSTEM_TIME AS OF TIMESTAMP \'2026-01-01 00:00:00\';

SELECT * FROM prezzi FOR SYSTEM_TIME ALL WHERE prodotto_id = 1;', 'sql'),
            P('È utile per audit, annullare errori e analisi nel tempo. Lo spazio cresce con le modifiche: le versioni vecchie si possono potare con `DELETE HISTORY`.', 'It is useful for audits, undoing mistakes and analysis over time. Space grows with changes: old versions can be pruned with `DELETE HISTORY`.'),
        ], [
            Q('v1', L('Che cosa fa `WITH SYSTEM VERSIONING`?', 'What does `WITH SYSTEM VERSIONING` do?'), [L('Conserva tutte le versioni delle righe', 'Keeps every version of the rows'), L('Numera le versioni del server', 'Numbers the server versions'), L('Blocca le modifiche', 'Blocks changes'), L('Crea un backup su file', 'Creates a backup file')], 0, L('Il passato resta interrogabile.', 'The past stays queryable.')),
            Q('v2', L('Come leggi la tabella com’era il 1° gennaio?', 'How do you read the table as it was on 1 January?'), ['FOR SYSTEM_TIME AS OF', 'WHERE date = ', 'HISTORY OF', 'AT VERSION'], 0, L('Con un `TIMESTAMP` preciso.', 'With a precise `TIMESTAMP`.')),
            Q('v3', L('Come togli le versioni vecchie?', 'How do you remove old versions?'), ['DELETE HISTORY', 'DROP VERSIONS', 'TRUNCATE PAST', 'PURGE TABLE'], 0, L('Così lo spazio non cresce all’infinito.', 'So space does not grow forever.')),
        ], task('sql', L('Crea la tabella `stipendi (dipendente_id INT PRIMARY KEY, importo DECIMAL(10,2))` con la storia di sistema attiva.', 'Create the table `stipendi (dipendente_id INT PRIMARY KEY, importo DECIMAL(10,2))` with system history on.'), '', [re('CREATE\s+TABLE\s+stipendi', L('La tabella esiste', 'The table exists')), re('dipendente_id\s+INT\s+PRIMARY\s+KEY', L('Ha la chiave', 'It has the key')), re('\)\s*WITH\s+SYSTEM\s+VERSIONING', L('Conserva la storia', 'It keeps history'))], '', "CREATE TABLE stipendi (\n    dipendente_id INT PRIMARY KEY,\n    importo DECIMAL(10, 2)\n) WITH SYSTEM VERSIONING;"), [
            dBlanks(pFill(), "SELECT * FROM prezzi\nFOR ___ AS ___ TIMESTAMP '2026-03-01 09:00:00';", ['SYSTEM_TIME', 'OF'], ['HISTORY', 'AT', 'VERSION'], L('Viaggio nel tempo dei dati.', 'Time travel for data.')),
            dFlash(pFlash(), [[L('Una tabella versionata perde le righe cancellate', 'A versioned table loses deleted rows'), false], [L('`FOR SYSTEM_TIME ALL` mostra tutte le versioni', '`FOR SYSTEM_TIME ALL` shows every version'), true], [L('Le versioni occupano spazio', 'Versions take up space'), true], [L('Serve un trigger scritto a mano', 'It needs a hand-written trigger'), false]], L('La storia la tiene il database.', 'The database keeps the history.')),
        ]),
        lesson('engines', L('Motori e Galera', 'Engines and Galera'), [
            P('Oltre a InnoDB, MariaDB offre motori per esigenze diverse: **Aria** (sostituto sicuro di MyISAM, usato per le tabelle di sistema), **ColumnStore** (analisi su grandi volumi, dati per colonna), **Spider** (tabelle divise su più server).', 'Beyond InnoDB, MariaDB offers engines for different needs: **Aria** (a crash-safe replacement for MyISAM, used for system tables), **ColumnStore** (analytics on big volumes, data stored by column), **Spider** (tables split across servers).'),
            C('CREATE TABLE log_visite (
    giorno DATE,
    pagina VARCHAR(200),
    visite INT
) ENGINE=ColumnStore;

SHOW STATUS LIKE \'wsrep_cluster_size\';  -- nodi del cluster Galera', 'sql'),
            P('**Galera Cluster** è integrato: più nodi MariaDB accettano tutti scritture e restano sincronizzati (replica sincrona multi-master). Se un nodo cade, gli altri continuano.', '**Galera Cluster** is built in: several MariaDB nodes all accept writes and stay in sync (synchronous multi-master replication). If one node fails, the others carry on.'),
        ], [
            Q('e1', L('A che cosa serve ColumnStore?', 'What is ColumnStore for?'), [L('Analisi su grandi volumi', 'Analytics on large volumes'), L('Tabelle temporanee', 'Temporary tables'), L('Salvare immagini', 'Storing images'), L('Le password', 'Passwords')], 0, L('I dati sono salvati per colonna.', 'Data is stored by column.')),
            Q('e2', L('Che tipo di replica offre Galera?', 'What kind of replication does Galera offer?'), [L('Sincrona multi-master', 'Synchronous multi-master'), L('Solo backup notturni', 'Nightly backups only'), L('Asincrona a un solo master', 'Asynchronous single master'), L('Nessuna', 'None')], 0, L('Tutti i nodi accettano scritture.', 'Every node accepts writes.')),
            Q('e3', L('Come scegli il motore di una tabella?', 'How do you pick a table’s engine?'), [L('Con `ENGINE=` nel `CREATE TABLE`', 'With `ENGINE=` in `CREATE TABLE`'), L('Riavviando il server', 'By restarting the server'), L('Con un `GRANT`', 'With a `GRANT`'), L('Non si può scegliere', 'You cannot choose')], 0, L('Ogni tabella può avere il suo.', 'Every table can have its own.')),
        ], task('sql', L('Crea la tabella `eventi (giorno DATE, tipo VARCHAR(50))` con il motore `Aria`.', 'Create the table `eventi (giorno DATE, tipo VARCHAR(50))` with the `Aria` engine.'), '', [re('CREATE\s+TABLE\s+eventi', L('La tabella esiste', 'The table exists')), re('giorno\s+DATE', L('C’è la data', 'There is the date')), re('ENGINE\s*=\s*Aria', L('Usa Aria', 'It uses Aria'))], '', "CREATE TABLE eventi (\n    giorno DATE,\n    tipo VARCHAR(50)\n) ENGINE=Aria;"), [
            dMatch(pMatch(), [['InnoDB', L('Transazioni, uso generale', 'Transactions, general use')], ['Aria', L('Erede sicuro di MyISAM', 'Crash-safe heir of MyISAM')], ['ColumnStore', L('Analisi per colonna', 'Column analytics')], ['Spider', L('Tabelle su più server', 'Tables across servers')], ['Galera', L('Cluster sincrono', 'Synchronous cluster')]], L('Lo strumento giusto per ogni lavoro.', 'The right tool for each job.')),
            dBug(pBug(), ['CREATE TABLE statistiche (', '    giorno DATE, visite INT', ') MOTORE=ColumnStore;'], 2, ') ENGINE=ColumnStore;', L('La parola chiave è `ENGINE`.', 'The keyword is `ENGINE`.')),
        ]),
    ],
];
