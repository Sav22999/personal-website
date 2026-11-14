<?php
/** SQLite: a whole database in one file, flexible types, PRAGMA, dates as text and using it from code. */

return [
    'id' => 'sqlite', 'family' => 'sqlite', 'after' => 'sql-base', 'categories' => ['data', 'mobile', 'desktop'],
    'title' => L('SQLite', 'SQLite'), 'short' => L('SQLite', 'SQLite'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database in un file, dentro telefoni, browser e app: shell `sqlite3`, tipi flessibili e tabelle `STRICT`, `PRAGMA`, date e uso da Python.', 'The database in a file, inside phones, browsers and apps: the `sqlite3` shell, flexible types and `STRICT` tables, `PRAGMA`, dates and use from Python.'),
    'lessons' => [
        lesson('file', L('Un database in un file', 'A database in a file'), [
            P('SQLite non ha un server: è una libreria che legge e scrive **un solo file**. Niente installazione, utenti o porte. È il database più diffuso al mondo: è dentro Android, iOS, i browser e moltissime app desktop.', 'SQLite has no server: it is a library that reads and writes **a single file**. No installation, users or ports. It is the most widely deployed database in the world: it lives inside Android, iOS, browsers and countless desktop apps.'),
            C('sqlite3 note.db

.tables
.schema note
.mode table
SELECT * FROM note;
.quit', 'sql'),
            P('I comandi che iniziano con il punto (`.tables`, `.schema`, `.mode`, `.import`) sono della shell `sqlite3`, non SQL. Se il file non esiste, viene creato alla prima scrittura.', 'Commands starting with a dot (`.tables`, `.schema`, `.mode`, `.import`) belong to the `sqlite3` shell, not SQL. If the file does not exist, it is created on the first write.'),
            Tip('SQLite è perfetto per app locali, prototipi e siti con poche scritture contemporanee; per molti utenti che scrivono insieme serve un database con server.', 'SQLite is perfect for local apps, prototypes and sites with few concurrent writes; for many users writing at once you need a server database.'),
        ], [
            Q('f1', L('Dove tiene i dati SQLite?', 'Where does SQLite keep its data?'), [L('In un unico file', 'In a single file'), L('In un server remoto', 'On a remote server'), L('Solo in memoria', 'Only in memory'), L('In un file per tabella', 'In one file per table')], 0, L('Copi il file e hai copiato il database.', 'Copy the file and you have copied the database.')),
            Q('f2', L('Che cosa sono i comandi come `.tables`?', 'What are commands like `.tables`?'), [L('Comandi della shell sqlite3', 'sqlite3 shell commands'), L('Istruzioni SQL standard', 'Standard SQL statements'), L('Errori di battitura', 'Typos'), L('Funzioni di Python', 'Python functions')], 0, L('Non funzionano da un programma.', 'They do not work from a program.')),
            Q('f3', L('Quando SQLite non è la scelta giusta?', 'When is SQLite not the right choice?'), [L('Con molti utenti che scrivono insieme', 'With many users writing at once'), L('In un’app mobile', 'In a mobile app'), L('Per un prototipo', 'For a prototype'), L('Per i dati di un’app desktop', 'For a desktop app’s data')], 0, L('Le scritture sono una alla volta.', 'Writes happen one at a time.')),
        ], task('sql', L('Crea la tabella `note` con `id INTEGER PRIMARY KEY` e `testo TEXT NOT NULL`.', 'Create the table `note` with `id INTEGER PRIMARY KEY` and `testo TEXT NOT NULL`.'), '', [re('CREATE\s+TABLE\s+note', L('La tabella esiste', 'The table exists')), re('id\s+INTEGER\s+PRIMARY\s+KEY', L('L’id è la chiave', 'The id is the key')), re('testo\s+TEXT\s+NOT\s+NULL', L('Il testo è obbligatorio', 'The text is required'))], '', "CREATE TABLE note (\n    id INTEGER PRIMARY KEY,\n    testo TEXT NOT NULL\n);"), [
            dMatch(pMatch(), [['.tables', L('Elenca le tabelle', 'Lists tables')], ['.schema', L('Mostra i CREATE', 'Shows the CREATE statements')], ['.mode table', L('Risultati in tabella', 'Results as a table')], ['.import', L('Carica un CSV', 'Loads a CSV')], ['.quit', L('Esce', 'Quits')]], L('La shell sqlite3.', 'The sqlite3 shell.')),
            dFlash(pFlash(), [[L('SQLite ha bisogno di un server', 'SQLite needs a server'), false], [L('SQLite è dentro Android e iOS', 'SQLite is inside Android and iOS'), true], [L('Il database è un solo file', 'The database is a single file'), true], [L('`.tables` funziona anche da Python', '`.tables` works from Python too'), false]], L('Piccolo, ovunque.', 'Small, everywhere.')),
        ]),
        lesson('types', L('Tipi flessibili e STRICT', 'Flexible types and STRICT'), [
            P('SQLite ha solo cinque classi di valore: `NULL`, `INTEGER`, `REAL`, `TEXT` e `BLOB`. Il tipo di una colonna è solo una **preferenza** (affinità): di base puoi salvare un testo in una colonna `INTEGER`.', 'SQLite has only five storage classes: `NULL`, `INTEGER`, `REAL`, `TEXT` and `BLOB`. A column’s type is only a **preference** (affinity): by default you can store text in an `INTEGER` column.'),
            C('CREATE TABLE prova (n INTEGER);
INSERT INTO prova VALUES (\'ciao\');   -- accettato!
SELECT n, typeof(n) FROM prova;      -- ciao | text

CREATE TABLE prezzi (
    id INTEGER PRIMARY KEY,
    euro REAL NOT NULL
) STRICT;', 'sql'),
            P('Con **`STRICT`** in fondo al `CREATE TABLE` i tipi diventano obbligatori, come negli altri database. Una colonna `INTEGER PRIMARY KEY` coincide con il `rowid` interno e si numera da sola.', 'With **`STRICT`** at the end of `CREATE TABLE` types become mandatory, as in other databases. An `INTEGER PRIMARY KEY` column is the internal `rowid` and numbers itself.'),
        ], [
            Q('y1', L('Che cosa succede inserendo `\'ciao\'` in una colonna `INTEGER` normale?', 'What happens when inserting `\'ciao\'` into a normal `INTEGER` column?'), [L('Viene accettato come testo', 'It is accepted as text'), L('Errore', 'Error'), L('Diventa 0', 'It becomes 0'), L('Diventa NULL', 'It becomes NULL')], 0, L('Il tipo è solo un’affinità.', 'The type is only an affinity.')),
            Q('y2', L('Che cosa fa `STRICT`?', 'What does `STRICT` do?'), [L('Rende obbligatori i tipi delle colonne', 'Makes column types mandatory'), L('Blocca le scritture', 'Blocks writes'), L('Cifra il file', 'Encrypts the file'), L('Vieta i NULL ovunque', 'Forbids NULL everywhere')], 0, L('Va in fondo al `CREATE TABLE`.', 'It goes at the end of `CREATE TABLE`.')),
            Q('y3', L('Che cos’è una colonna `INTEGER PRIMARY KEY`?', 'What is an `INTEGER PRIMARY KEY` column?'), [L('Un alias del `rowid`, si numera da sola', 'An alias of `rowid`, it numbers itself'), L('Una colonna di testo', 'A text column'), L('Una chiave esterna', 'A foreign key'), L('Un indice full text', 'A full-text index')], 0, L('Non serve `AUTOINCREMENT`.', 'No `AUTOINCREMENT` needed.')),
        ], task('sql', L('Crea la tabella `misure (id INTEGER PRIMARY KEY, valore REAL NOT NULL)` in modalità `STRICT`.', 'Create the table `misure (id INTEGER PRIMARY KEY, valore REAL NOT NULL)` in `STRICT` mode.'), '', [re('CREATE\s+TABLE\s+misure', L('La tabella esiste', 'The table exists')), re('valore\s+REAL\s+NOT\s+NULL', L('Il valore è un numero obbligatorio', 'The value is a required number')), re('\)\s*STRICT\s*;', L('I tipi sono rigidi', 'Types are strict'))], '', "CREATE TABLE misure (\n    id INTEGER PRIMARY KEY,\n    valore REAL NOT NULL\n) STRICT;"), [
            dMatch(pMatch(), [['INTEGER', L('Numero intero', 'Whole number')], ['REAL', L('Numero con la virgola', 'Floating-point number')], ['TEXT', L('Testo', 'Text')], ['BLOB', L('Dati binari', 'Binary data')], ['NULL', L('Nessun valore', 'No value')]], L('Le cinque classi di SQLite.', 'SQLite’s five storage classes.')),
            dBug(pBug(), ['CREATE TABLE STRICT voti (', '    id INTEGER PRIMARY KEY,', '    voto INTEGER', ');'], 0, 'CREATE TABLE voti (', L('`STRICT` va in fondo: `) STRICT;`.', '`STRICT` goes at the end: `) STRICT;`.')),
        ]),
        lesson('pragma', L('PRAGMA', 'PRAGMA'), [
            P('Le **PRAGMA** regolano il comportamento di SQLite. La più importante: le chiavi esterne sono **spente** di base e vanno accese a ogni connessione con `PRAGMA foreign_keys = ON`.', '**PRAGMA** statements tune SQLite’s behaviour. The most important: foreign keys are **off** by default and must be turned on at every connection with `PRAGMA foreign_keys = ON`.'),
            C('PRAGMA foreign_keys = ON;
PRAGMA journal_mode = WAL;
PRAGMA integrity_check;
PRAGMA table_info(note);', 'sql'),
            P('La modalità **WAL** (write-ahead log) permette di leggere mentre qualcuno scrive: quasi sempre conviene. `integrity_check` verifica che il file non sia rovinato, `table_info` elenca le colonne.', '**WAL** mode (write-ahead log) lets readers read while someone writes: almost always worth it. `integrity_check` verifies the file is not corrupted, `table_info` lists the columns.'),
        ], [
            Q('g1', L('Le chiavi esterne sono attive di base in SQLite?', 'Are foreign keys on by default in SQLite?'), [L('No, vanno accese', 'No, they must be turned on'), L('Sì, sempre', 'Yes, always'), L('Solo nei file .db', 'Only in .db files'), L('Solo con STRICT', 'Only with STRICT')], 0, L('`PRAGMA foreign_keys = ON` a ogni connessione.', '`PRAGMA foreign_keys = ON` at every connection.')),
            Q('g2', L('Che vantaggio dà `journal_mode = WAL`?', 'What does `journal_mode = WAL` give you?'), [L('Letture durante le scritture', 'Reads during writes'), L('Cifratura', 'Encryption'), L('File più piccoli sempre', 'Always smaller files'), L('Più utenti con password', 'More users with passwords')], 0, L('Lettori e scrittore non si bloccano.', 'Readers and writer do not block each other.')),
            Q('g3', L('Quale PRAGMA elenca le colonne di una tabella?', 'Which PRAGMA lists a table’s columns?'), ['table_info', 'integrity_check', 'foreign_keys', 'journal_mode'], 0, L('Come un `DESCRIBE`.', 'Like a `DESCRIBE`.')),
        ], task('sql', L('Accendi le chiavi esterne e passa alla modalità WAL.', 'Turn on foreign keys and switch to WAL mode.'), '', [re('PRAGMA\s+foreign_keys\s*=\s*(ON|1)', L('Chiavi esterne accese', 'Foreign keys on')), re('PRAGMA\s+journal_mode\s*=\s*WAL', L('Modalità WAL', 'WAL mode'))], '', "PRAGMA foreign_keys = ON;\nPRAGMA journal_mode = WAL;"), [
            dBlanks(pFill(), "PRAGMA ___ = ON;\nPRAGMA journal_mode = ___;", ['foreign_keys', 'WAL'], ['keys', 'FAST', 'strict'], L('Le due PRAGMA da mettere quasi sempre.', 'The two PRAGMAs to set almost always.')),
            dFlash(pFlash(), [[L('Le chiavi esterne si accendono una volta per sempre', 'Foreign keys are turned on once and for all'), false], [L('WAL permette letture durante le scritture', 'WAL allows reads during writes'), true], [L('`integrity_check` controlla il file', '`integrity_check` checks the file'), true], [L('Le PRAGMA sono comandi della shell con il punto', 'PRAGMAs are dot commands of the shell'), false]], L('Le PRAGMA sono SQL, valgono anche dal codice.', 'PRAGMAs are SQL, they work from code too.')),
        ]),
        lesson('dates', L('Date e ore', 'Dates and times'), [
            P('SQLite non ha un tipo data: le date si salvano come **testo ISO** (`2026-11-10 18:30:00`), come numero Unix o come giorno giuliano. Le funzioni `date()`, `time()`, `datetime()` e `strftime()` le leggono e le trasformano.', 'SQLite has no date type: dates are stored as **ISO text** (`2026-11-10 18:30:00`), as a Unix number or as a Julian day. The functions `date()`, `time()`, `datetime()` and `strftime()` read and transform them.'),
            C('SELECT date(\'now\');                      -- 2026-11-10
SELECT datetime(\'now\', \'+7 days\');
SELECT strftime(\'%Y\', creata) AS anno, COUNT(*)
FROM note GROUP BY anno;

SELECT * FROM note WHERE creata >= date(\'now\', \'-30 days\');', 'sql'),
            P('I modificatori (`+7 days`, `-1 month`, `start of month`) spostano la data. Il formato ISO si ordina e si confronta bene anche come testo.', 'Modifiers (`+7 days`, `-1 month`, `start of month`) move the date. The ISO format sorts and compares well even as text.'),
        ], [
            Q('d1', L('Come si salvano di solito le date in SQLite?', 'How are dates usually stored in SQLite?'), [L('Come testo ISO', 'As ISO text'), L('Con il tipo DATE nativo', 'With a native DATE type'), L('Come immagini', 'As images'), L('Non si possono salvare', 'They cannot be stored')], 0, L('Il formato ISO si ordina bene.', 'The ISO format sorts well.')),
            Q('d2', L('Che cosa restituisce `datetime(\'now\', \'+7 days\')`?', 'What does `datetime(\'now\', \'+7 days\')` return?'), [L('Tra una settimana', 'One week from now'), L('Una settimana fa', 'One week ago'), L('Il giorno della settimana', 'The day of the week'), L('Sette secondi dopo', 'Seven seconds later')], 0, L('Il modificatore sposta la data.', 'The modifier moves the date.')),
            Q('d3', L('Quale funzione estrae l’anno?', 'Which function extracts the year?'), ['strftime(\'%Y\', d)', 'year(d)', 'extract(d)', 'date_year(d)'], 0, L('Con i codici di formato come `%Y`.', 'With format codes like `%Y`.')),
        ], task('sql', L('Dalla tabella `ordini` seleziona tutto quello creato (`creato`) negli ultimi 7 giorni, con `date(\'now\', \'-7 days\')`.', 'From the table `ordini` select everything created (`creato`) in the last 7 days, with `date(\'now\', \'-7 days\')`.'), '', [re('SELECT\s+\*\s+FROM\s+ordini', L('Leggi gli ordini', 'You read the orders')), re('creato\s*>=?\s*date\s*\(\s*\'now\'\s*,\s*\'-7 days\'\s*\)', L('Filtri sugli ultimi 7 giorni', 'You filter the last 7 days'))], '', "SELECT * FROM ordini WHERE creato >= date('now', '-7 days');"), [
            dMatch(pMatch(), [['date(\'now\')', L('La data di oggi', 'Today’s date')], ['\'+1 day\'', L('Domani', 'Tomorrow')], ['\'start of month\'', L('Inizio del mese', 'Start of the month')], ['%Y', L('L’anno', 'The year')], ['%m', L('Il mese', 'The month')]], L('Date come testo, ma con gli strumenti giusti.', 'Dates as text, but with the right tools.')),
            dBug(pBug(), ['SELECT titolo FROM note', 'WHERE creata >= DATE_SUB(NOW(), INTERVAL 30 DAY);'], 1, 'WHERE creata >= date(\'now\', \'-30 days\');', L('`DATE_SUB` e `NOW()` sono di MySQL.', '`DATE_SUB` and `NOW()` are MySQL.')),
        ]),
        lesson('code', L('SQLite dal codice', 'SQLite from code'), [
            P('Python include SQLite nella libreria standard (`sqlite3`). I valori si passano sempre con i **segnaposto** `?`, mai incollandoli nella stringa: così eviti l’SQL injection.', 'Python ships SQLite in the standard library (`sqlite3`). Values are always passed with `?` **placeholders**, never pasted into the string: that is how you avoid SQL injection.'),
            C('import sqlite3

con = sqlite3.connect("note.db")
con.execute("PRAGMA foreign_keys = ON")
with con:  # transazione: commit alla fine, rollback se c\'è un errore
    con.execute("INSERT INTO note (testo) VALUES (?)", ("Comprare il pane",))
for riga in con.execute("SELECT id, testo FROM note WHERE id > ?", (10,)):
    print(riga)
con.close()', 'python'),
            P('Il blocco `with con:` apre una transazione: tutto va a buon fine insieme, oppure niente. Per un backup a caldo usa `con.backup(...)` o `VACUUM INTO \'copia.db\'`.', 'The `with con:` block opens a transaction: everything succeeds together, or nothing does. For a live backup use `con.backup(...)` or `VACUUM INTO \'copy.db\'`.'),
        ], [
            Q('c1', L('Come si passano i valori a una query?', 'How do you pass values to a query?'), [L('Con i segnaposto `?`', 'With `?` placeholders'), L('Concatenando le stringhe', 'By concatenating strings'), L('Con le f-string', 'With f-strings'), L('Con `%` sul testo', 'With `%` on the text')], 0, L('Così non c’è SQL injection.', 'That way there is no SQL injection.')),
            Q('c2', L('Che cosa fa `with con:`?', 'What does `with con:` do?'), [L('Apre una transazione', 'Opens a transaction'), L('Chiude il file', 'Closes the file'), L('Crea una tabella', 'Creates a table'), L('Accende WAL', 'Turns on WAL')], 0, L('Commit alla fine o rollback in caso di errore.', 'Commit at the end or rollback on error.')),
            Q('c3', L('Come fai una copia del database senza fermare l’app?', 'How do you copy the database without stopping the app?'), [L('`VACUUM INTO \'copia.db\'`', '`VACUUM INTO \'copy.db\'`'), L('`DROP DATABASE`', '`DROP DATABASE`'), L('`.quit`', '`.quit`'), L('`PRAGMA foreign_keys`', '`PRAGMA foreign_keys`')], 0, L('Oppure `con.backup()`.', 'Or `con.backup()`.')),
        ], task('python', L('Inserisci in `note (testo)` il valore della variabile `testo` usando un segnaposto `?`, dentro un blocco `with con:`.', 'Insert into `note (testo)` the value of the variable `testo` using a `?` placeholder, inside a `with con:` block.'), "import sqlite3\ncon = sqlite3.connect(\"note.db\")\ntesto = \"Ciao\"", [re('with\s+con\s*:', L('Usi una transazione', 'You use a transaction')), re('execute\s*\(\s*["\']INSERT\s+INTO\s+note\s*\(\s*testo\s*\)\s*VALUES\s*\(\s*\?\s*\)["\']\s*,\s*\(\s*testo\s*,\s*\)', L('Passi il valore con il segnaposto', 'You pass the value with the placeholder'))], '', "with con:\n    con.execute(\"INSERT INTO note (testo) VALUES (?)\", (testo,))"), [
            dBug(pBug(), ['nome = input("Nome: ")', 'con.execute(f"SELECT * FROM utenti WHERE nome = \'{nome}\'")'], 1, 'con.execute("SELECT * FROM utenti WHERE nome = ?", (nome,))', L('Mai il valore dentro la stringa: segnaposto.', 'Never the value inside the string: placeholder.')),
            dOrder(pSort(), ['import sqlite3', 'con = sqlite3.connect("app.db")', 'with con:', '    con.execute("INSERT INTO log (msg) VALUES (?)", ("avvio",))', 'con.close()'], L('Importa, collega, scrivi, chiudi.', 'Import, connect, write, close.')),
        ]),
    ],
];
