<?php
/** Microsoft SQL Server and T-SQL: TOP, types and IDENTITY, TRY/CATCH, stored procedures, paging and MERGE, temporary tables, indexes and JSON. */

return [
    'id' => 'mssql', 'family' => 'mssql', 'after' => 'sql-base',
    'title' => L('SQL Server (T-SQL)', 'SQL Server (T-SQL)'), 'short' => L('SQL Server', 'SQL Server'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database di Microsoft e il suo dialetto T-SQL: `TOP`, `IDENTITY`, `NVARCHAR`, variabili, `TRY…CATCH`, stored procedure, paginazione e `MERGE`, tabelle temporanee, indici e JSON.', 'Microsoft’s database and its T-SQL dialect: `TOP`, `IDENTITY`, `NVARCHAR`, variables, `TRY…CATCH`, stored procedures, paging and `MERGE`, temporary tables, indexes and JSON.'),
    'lessons' => [
        lesson('basics', L('Il dialetto T-SQL', 'The T-SQL dialect'), [
            P('SQL Server parla **T-SQL** (Transact-SQL). Ci si lavora con SQL Server Management Studio, con l’estensione per VS Code o con `sqlcmd`. Le tabelle stanno in schemi: il predefinito è `dbo`.', 'SQL Server speaks **T-SQL** (Transact-SQL). You work with SQL Server Management Studio, the VS Code extension or `sqlcmd`. Tables live in schemas: the default is `dbo`.'),
            C('USE Negozio;
GO

SELECT TOP (10) [Nome], [Prezzo]
FROM dbo.Prodotti
ORDER BY Prezzo DESC;', 'sql'),
            P('Al posto di `LIMIT` si usa **`TOP (n)`** subito dopo `SELECT`. I nomi si possono racchiudere tra **parentesi quadre** (`[Nome cliente]`). `GO` non è SQL: dice allo strumento di inviare il blocco di comandi scritto fin lì.', 'Instead of `LIMIT` you use **`TOP (n)`** right after `SELECT`. Names can be wrapped in **square brackets** (`[Customer name]`). `GO` is not SQL: it tells the tool to send the batch written so far.'),
        ], [
            Q('b1', L('Come prendi le prime 5 righe in T-SQL?', 'How do you take the first 5 rows in T-SQL?'), ['SELECT TOP (5) …', 'SELECT … LIMIT 5', 'SELECT FIRST 5 …', 'SELECT … ROWS 5'], 0, L('`LIMIT` non esiste in SQL Server.', '`LIMIT` does not exist in SQL Server.')),
            Q('b2', L('Qual è lo schema predefinito?', 'What is the default schema?'), ['dbo', 'public', 'main', 'sys'], 0, L('Database owner.', 'Database owner.')),
            Q('b3', L('Che cos’è `GO`?', 'What is `GO`?'), [L('Un separatore di blocchi per lo strumento', 'A batch separator for the tool'), L('Un’istruzione che avvia una transazione', 'A statement that starts a transaction'), L('Un tipo di dato', 'A data type'), L('Un ciclo', 'A loop')], 0, L('Il server non lo vede nemmeno.', 'The server never even sees it.')),
        ], task('sql', L('Seleziona i 3 clienti con più `Punti` dalla tabella `dbo.Clienti`, mostrando `Nome` e `Punti`.', 'Select the 3 customers with the most `Punti` from the table `dbo.Clienti`, showing `Nome` and `Punti`.'), '', [re('SELECT\s+TOP\s*\(?\s*3\s*\)?\s+\[?Nome\]?\s*,\s*\[?Punti\]?', L('Prendi i primi 3', 'You take the top 3')), re('FROM\s+dbo\.Clienti', L('Dalla tabella giusta', 'From the right table')), re('ORDER\s+BY\s+\[?Punti\]?\s+DESC', L('Ordini dal più alto', 'You sort from the highest'))], '', "SELECT TOP (3) Nome, Punti\nFROM dbo.Clienti\nORDER BY Punti DESC;"), [
            dBug(pBug(), ['SELECT LIMIT (10) Nome, Prezzo', 'FROM dbo.Prodotti', 'ORDER BY Prezzo DESC;'], 0, 'SELECT TOP (10) Nome, Prezzo', L('In T-SQL si scrive `SELECT TOP (10) …`, non `LIMIT`.', 'In T-SQL you write `SELECT TOP (10) …`, not `LIMIT`.')),
            dMatch(pMatch(), [['TOP (n)', L('Le prime n righe', 'The first n rows')], ['[Nome]', L('Un nome tra parentesi quadre', 'A name in square brackets')], ['dbo', L('Lo schema predefinito', 'The default schema')], ['GO', L('Fine del blocco', 'End of the batch')], ['sqlcmd', L('Client a riga di comando', 'Command-line client')]], L('Le parole di SQL Server.', 'SQL Server’s words.')),
        ]),
        lesson('types', L('Tipi e IDENTITY', 'Types and IDENTITY'), [
            P('Per i testi con qualsiasi alfabeto si usa **`NVARCHAR`** (Unicode); i letterali Unicode si scrivono con la `N` davanti: `N\'Città\'`. Il contatore automatico è **`IDENTITY(1,1)`**: parte da 1 e avanza di 1.', 'For text in any alphabet you use **`NVARCHAR`** (Unicode); Unicode literals are written with an `N` in front: `N\'City\'`. The automatic counter is **`IDENTITY(1,1)`**: it starts at 1 and advances by 1.'),
            C('CREATE TABLE dbo.Ordini (
    Id INT IDENTITY(1, 1) PRIMARY KEY,
    Cliente NVARCHAR(100) NOT NULL,
    Pagato BIT NOT NULL DEFAULT 0,
    Totale DECIMAL(10, 2),
    CreatoIl DATETIME2 DEFAULT SYSDATETIME()
);

INSERT INTO dbo.Ordini (Cliente, Totale) VALUES (N\'Zoë\', 19.90);', 'sql'),
            P('`BIT` vale 0 o 1 e fa da booleano. `DATETIME2` è più preciso e ampio del vecchio `DATETIME`. `NVARCHAR(MAX)` regge testi lunghissimi.', '`BIT` is 0 or 1 and acts as a boolean. `DATETIME2` is more precise and wider than the old `DATETIME`. `NVARCHAR(MAX)` holds very long text.'),
        ], [
            Q('y1', L('Che cosa fa `IDENTITY(1,1)`?', 'What does `IDENTITY(1,1)` do?'), [L('Numera da 1 di 1 in 1', 'Numbers from 1, step 1'), L('Crea una colonna di testo', 'Creates a text column'), L('Rende la colonna unica e basta', 'Just makes the column unique'), L('Duplica la riga', 'Duplicates the row')], 0, L('È l’`AUTO_INCREMENT` di SQL Server.', 'It is SQL Server’s `AUTO_INCREMENT`.')),
            Q('y2', L('Perché scrivere `N\'Zoë\'`?', 'Why write `N\'Zoë\'`?'), [L('È un letterale Unicode', 'It is a Unicode literal'), L('Significa “nullo”', 'It means “null”'), L('È un numero', 'It is a number'), L('È un commento', 'It is a comment')], 0, L('Senza `N` i caratteri speciali possono perdersi.', 'Without `N` special characters can get lost.')),
            Q('y3', L('Quale tipo fa da booleano?', 'Which type acts as a boolean?'), ['BIT', 'BOOL', 'TINYTEXT', 'FLAG'], 0, L('Vale 0 o 1.', 'It is 0 or 1.')),
        ], task('sql', L('Crea `dbo.Utenti` con `Id INT IDENTITY(1,1) PRIMARY KEY` e `Nome NVARCHAR(80) NOT NULL`.', 'Create `dbo.Utenti` with `Id INT IDENTITY(1,1) PRIMARY KEY` and `Nome NVARCHAR(80) NOT NULL`.'), '', [re('CREATE\s+TABLE\s+dbo\.Utenti', L('La tabella esiste', 'The table exists')), re('Id\s+INT\s+IDENTITY\s*\(\s*1\s*,\s*1\s*\)\s+PRIMARY\s+KEY', L('L’id si numera da solo', 'The id numbers itself')), re('Nome\s+NVARCHAR\s*\(\s*80\s*\)\s+NOT\s+NULL', L('Il nome è Unicode e obbligatorio', 'The name is Unicode and required'))], '', "CREATE TABLE dbo.Utenti (\n    Id INT IDENTITY(1, 1) PRIMARY KEY,\n    Nome NVARCHAR(80) NOT NULL\n);"), [
            dBlanks(pFill(), "CREATE TABLE dbo.Note (\n    Id INT ___(1, 1) PRIMARY KEY,\n    Testo ___(MAX),\n    Letta ___ DEFAULT 0\n);", ['IDENTITY', 'NVARCHAR', 'BIT'], ['AUTO_INCREMENT', 'TEXT', 'BOOLEAN'], L('Contatore, testo Unicode, booleano.', 'Counter, Unicode text, boolean.')),
            dFlash(pFlash(), [[L('`NVARCHAR` regge l’Unicode', '`NVARCHAR` holds Unicode'), true], [L('SQL Server usa `AUTO_INCREMENT`', 'SQL Server uses `AUTO_INCREMENT`'), false], [L('`DATETIME2` è più preciso di `DATETIME`', '`DATETIME2` is more precise than `DATETIME`'), true], [L('`BIT` può valere 0, 1 o 2', '`BIT` can be 0, 1 or 2'), false]], L('I tipi di T-SQL.', 'T-SQL types.')),
        ]),
        lesson('tsql', L('Variabili, IF e TRY…CATCH', 'Variables, IF and TRY…CATCH'), [
            P('T-SQL è anche un linguaggio di programmazione: le **variabili** iniziano con `@` e si dichiarano con `DECLARE`, i valori si assegnano con `SET` o `SELECT`. Ci sono `IF…ELSE`, `WHILE` e la gestione degli errori con `BEGIN TRY … END TRY BEGIN CATCH … END CATCH`.', 'T-SQL is also a programming language: **variables** start with `@` and are declared with `DECLARE`, values are assigned with `SET` or `SELECT`. There is `IF…ELSE`, `WHILE` and error handling with `BEGIN TRY … END TRY BEGIN CATCH … END CATCH`.'),
            C('DECLARE @Totale DECIMAL(10, 2);
SELECT @Totale = SUM(Totale) FROM dbo.Ordini WHERE Pagato = 1;

BEGIN TRY
    BEGIN TRANSACTION;
    UPDATE dbo.Conti SET Saldo = Saldo - 50 WHERE Id = 1;
    UPDATE dbo.Conti SET Saldo = Saldo + 50 WHERE Id = 2;
    COMMIT;
END TRY
BEGIN CATCH
    IF @@TRANCOUNT > 0 ROLLBACK;
    THROW;   -- rilancia l’errore a chi ha chiamato
END CATCH;', 'sql'),
            P('Nel `CATCH`, `IF @@TRANCOUNT > 0` annulla solo se c’è davvero una transazione aperta, e `THROW` rilancia l’errore invece di nasconderlo. `OUTPUT inserted.Id` dopo un `INSERT` restituisce le righe appena inserite, come `RETURNING` in PostgreSQL.', 'In the `CATCH`, `IF @@TRANCOUNT > 0` rolls back only if a transaction is really open, and `THROW` re-raises the error instead of hiding it. `OUTPUT inserted.Id` after an `INSERT` returns the rows just inserted, like `RETURNING` in PostgreSQL.'),
        ], [
            Q('v1', L('Con che cosa inizia una variabile T-SQL?', 'What does a T-SQL variable start with?'), ['@', '$', ':', '#'], 0, L('`#` invece indica una tabella temporanea.', '`#` marks a temporary table instead.')),
            Q('v2', L('Che cosa succede nel blocco `CATCH`?', 'What happens in the `CATCH` block?'), [L('Si gestisce l’errore del blocco `TRY`', 'The error of the `TRY` block is handled'), L('Si dichiarano le variabili', 'Variables are declared'), L('Si crea la tabella', 'The table is created'), L('Niente, è un commento', 'Nothing, it is a comment')], 0, L('Tipicamente `ROLLBACK`.', 'Typically `ROLLBACK`.')),
            Q('v3', L('Come restituisci l’id appena inserito?', 'How do you return the id just inserted?'), ['OUTPUT inserted.Id', 'RETURNING Id', 'LAST_INSERT_ID()', 'SELECT NEXT Id'], 0, L('Si scrive tra `INSERT INTO` e `VALUES`.', 'It goes between `INSERT INTO` and `VALUES`.')),
        ], task('sql', L('Dichiara la variabile `@Conta INT` e assegnale il numero di righe di `dbo.Ordini` con `SELECT @Conta = COUNT(*)`.', 'Declare the variable `@Conta INT` and assign it the number of rows of `dbo.Ordini` with `SELECT @Conta = COUNT(*)`.'), '', [re('DECLARE\s+@Conta\s+INT', L('La variabile esiste', 'The variable exists')), re('SELECT\s+@Conta\s*=\s*COUNT\s*\(\s*\*\s*\)\s+FROM\s+dbo\.Ordini', L('Conti gli ordini', 'You count the orders'))], '', "DECLARE @Conta INT;\nSELECT @Conta = COUNT(*) FROM dbo.Ordini;"), [
            dOrder(pSort(), ['BEGIN TRY', '    BEGIN TRANSACTION;', '    DELETE FROM dbo.Carrelli WHERE Vecchio = 1;', '    COMMIT;', 'END TRY', 'BEGIN CATCH', '    ROLLBACK;', 'END CATCH;'], L('Prova, conferma; se va male, annulla.', 'Try, confirm; if it fails, undo.')),
            dBug(pBug(), ['DECLARE Totale INT;', 'SELECT @Totale = COUNT(*) FROM dbo.Ordini;', 'PRINT @Totale;'], 0, 'DECLARE @Totale INT;', L('Le variabili iniziano con `@`.', 'Variables start with `@`.')),
        ]),
        lesson('procedures', L('Stored procedure', 'Stored procedures'), [
            P('Una **stored procedure** è un blocco di T-SQL salvato nel database con un nome e dei parametri. Si esegue con `EXEC`. Centralizza la logica e, con i parametri, protegge dall’SQL injection.', 'A **stored procedure** is a block of T-SQL saved in the database with a name and parameters. You run it with `EXEC`. It centralises logic and, with parameters, guards against SQL injection.'),
            C('CREATE OR ALTER PROCEDURE dbo.OrdiniDelCliente
    @ClienteId INT,
    @Da DATE = NULL
AS
BEGIN
    SET NOCOUNT ON;
    SELECT Id, Totale, CreatoIl
    FROM dbo.Ordini
    WHERE ClienteId = @ClienteId
      AND (@Da IS NULL OR CreatoIl >= @Da);
END;
GO

EXEC dbo.OrdiniDelCliente @ClienteId = 42, @Da = \'2026-01-01\';', 'sql'),
            P('`CREATE OR ALTER` crea la procedura o la aggiorna se c’è già. `SET NOCOUNT ON` evita i messaggi “n righe interessate”. Un parametro con `= NULL` è facoltativo.', '`CREATE OR ALTER` creates the procedure or updates it if it exists. `SET NOCOUNT ON` suppresses the “n rows affected” messages. A parameter with `= NULL` is optional.'),
        ], [
            Q('p1', L('Come esegui una stored procedure?', 'How do you run a stored procedure?'), ['EXEC', 'CALL ME', 'RUN', 'START'], 0, L('`EXEC nome @param = valore`.', '`EXEC name @param = value`.')),
            Q('p2', L('Che cosa fa `CREATE OR ALTER`?', 'What does `CREATE OR ALTER` do?'), [L('Crea o aggiorna la procedura', 'Creates or updates the procedure'), L('Cancella la procedura', 'Drops the procedure'), L('Crea una tabella', 'Creates a table'), L('Avvia una transazione', 'Starts a transaction')], 0, L('Niente errore se esiste già.', 'No error if it already exists.')),
            Q('p3', L('Come rendi facoltativo un parametro?', 'How do you make a parameter optional?'), [L('Gli dai un valore predefinito', 'Give it a default value'), L('Lo scrivi in minuscolo', 'Write it in lowercase'), L('Lo metti per primo', 'Put it first'), L('Non si può', 'You cannot')], 0, L('Per esempio `@Da DATE = NULL`.', 'For example `@Da DATE = NULL`.')),
        ], task('sql', L('Crea (o aggiorna) la procedura `dbo.ProdottoPerId` con il parametro `@Id INT` che seleziona tutto da `dbo.Prodotti` dove `Id = @Id`.', 'Create (or update) the procedure `dbo.ProdottoPerId` with the parameter `@Id INT` that selects everything from `dbo.Prodotti` where `Id = @Id`.'), '', [re('CREATE\s+(OR\s+ALTER\s+)?PROCEDURE\s+dbo\.ProdottoPerId', L('La procedura esiste', 'The procedure exists')), re('@Id\s+INT', L('Ha il parametro', 'It has the parameter')), re('AS\s+(BEGIN\s+)?SELECT\s+\*\s+FROM\s+dbo\.Prodotti\s+WHERE\s+Id\s*=\s*@Id', L('Usa il parametro nel filtro', 'It uses the parameter in the filter'))], '', "CREATE OR ALTER PROCEDURE dbo.ProdottoPerId\n    @Id INT\nAS\nSELECT * FROM dbo.Prodotti WHERE Id = @Id;"), [
            dBlanks(pFill(), "CREATE OR ___ PROCEDURE dbo.Saluta\n    @Nome NVARCHAR(50)\n___\nBEGIN\n    PRINT N'Ciao ' + @Nome;\nEND;\nGO\n___ dbo.Saluta @Nome = N'Ada';", ['ALTER', 'AS', 'EXEC'], ['UPDATE', 'IS', 'CALL'], L('Definire ed eseguire una procedura.', 'Defining and running a procedure.')),
            dMatch(pMatch(), [['CREATE OR ALTER', L('Crea o aggiorna', 'Creates or updates')], ['EXEC', L('Esegue', 'Runs')], ['SET NOCOUNT ON', L('Niente conteggi a video', 'No row counts shown')], ['@Da DATE = NULL', L('Parametro facoltativo', 'Optional parameter')]], L('Le parti di una procedura.', 'The parts of a procedure.')),
        ]),
        lesson('paging', L('Paginazione e MERGE', 'Paging and MERGE'), [
            P('Per mostrare i risultati a pagine si usa **`OFFSET … ROWS FETCH NEXT … ROWS ONLY`**, che richiede un `ORDER BY`. **`MERGE`** confronta una tabella di origine con una di destinazione e in un colpo solo inserisce, aggiorna o cancella.', 'To show results in pages you use **`OFFSET … ROWS FETCH NEXT … ROWS ONLY`**, which requires an `ORDER BY`. **`MERGE`** compares a source table with a target and inserts, updates or deletes in one go.'),
            C('-- pagina 3, 20 righe per pagina
SELECT Id, Nome FROM dbo.Prodotti
ORDER BY Nome
OFFSET 40 ROWS FETCH NEXT 20 ROWS ONLY;

MERGE dbo.Prezzi AS d
USING dbo.PrezziNuovi AS o ON d.ProdottoId = o.ProdottoId
WHEN MATCHED THEN UPDATE SET d.Prezzo = o.Prezzo
WHEN NOT MATCHED THEN INSERT (ProdottoId, Prezzo) VALUES (o.ProdottoId, o.Prezzo);', 'sql'),
            P('L’`OFFSET` di una pagina è `(pagina - 1) × dimensione`. `MERGE` deve finire con il punto e virgola.', 'A page’s `OFFSET` is `(page - 1) × size`. `MERGE` must end with a semicolon.'),
        ], [
            Q('m1', L('Che cosa serve sempre con `OFFSET … FETCH`?', 'What do you always need with `OFFSET … FETCH`?'), ['ORDER BY', 'GROUP BY', 'TOP', 'DISTINCT'], 0, L('Senza ordine le pagine non hanno senso.', 'Without an order pages make no sense.')),
            Q('m2', L('Qual è l’`OFFSET` della pagina 4 con 10 righe per pagina?', 'What is the `OFFSET` of page 4 with 10 rows per page?'), ['30', '40', '4', '10'], 0, L('(4 − 1) × 10 = 30.', '(4 − 1) × 10 = 30.')),
            Q('m3', L('Che cosa fa `WHEN NOT MATCHED THEN INSERT`?', 'What does `WHEN NOT MATCHED THEN INSERT` do?'), [L('Inserisce le righe nuove dell’origine', 'Inserts the source’s new rows'), L('Cancella le righe uguali', 'Deletes matching rows'), L('Aggiorna tutto', 'Updates everything'), L('Annulla il MERGE', 'Cancels the MERGE')], 0, L('Quelle che nella destinazione non ci sono.', 'The ones missing from the target.')),
        ], task('sql', L('Mostra la seconda pagina di 10 righe di `dbo.Clienti`, ordinata per `Nome`.', 'Show the second page of 10 rows of `dbo.Clienti`, sorted by `Nome`.'), 'SELECT Id, Nome FROM dbo.Clienti', [re('ORDER\s+BY\s+\[?Nome\]?', L('Ordini per nome', 'You sort by name')), re('OFFSET\s+10\s+ROWS\s+FETCH\s+NEXT\s+10\s+ROWS\s+ONLY', L('Salti 10 righe e ne prendi 10', 'You skip 10 rows and take 10'))], '', "SELECT Id, Nome FROM dbo.Clienti\nORDER BY Nome\nOFFSET 10 ROWS FETCH NEXT 10 ROWS ONLY;"), [
            dOrder(pSort(), ['MERGE dbo.Stock AS d', 'USING dbo.Arrivi AS o ON d.Codice = o.Codice', 'WHEN MATCHED THEN UPDATE SET d.Qta = d.Qta + o.Qta', 'WHEN NOT MATCHED THEN INSERT (Codice, Qta) VALUES (o.Codice, o.Qta);'], L('Destinazione, origine, poi i due casi.', 'Target, source, then the two cases.')),
            dBlanks(pFill(), "SELECT Titolo FROM dbo.Articoli\nORDER BY Data DESC\n___ 20 ROWS ___ NEXT 10 ROWS ONLY;", ['OFFSET', 'FETCH'], ['SKIP', 'LIMIT', 'TAKE'], L('La paginazione standard di T-SQL.', 'T-SQL’s standard paging.')),
        ]),
        lesson('temp', L('Tabelle temporanee e variabili tabella', 'Temporary tables and table variables'), [
            P('Per i risultati intermedi T-SQL offre le **tabelle temporanee**: il nome inizia con `#` e la tabella vive solo nella tua sessione (con `##` è globale, visibile a tutti). Le **variabili tabella** (`DECLARE @t TABLE (…)`) vivono solo nel blocco di codice in cui le dichiari.', 'For intermediate results T-SQL offers **temporary tables**: the name starts with `#` and the table lives only in your session (with `##` it is global, visible to everyone). **Table variables** (`DECLARE @t TABLE (…)`) live only in the batch where you declare them.'),
            C('SELECT ClienteId, SUM(Totale) AS Speso
INTO #Spese
FROM dbo.Ordini
WHERE CreatoIl >= \'2026-01-01\'
GROUP BY ClienteId;

CREATE INDEX IX_Spese ON #Spese (Speso);
SELECT TOP (10) * FROM #Spese ORDER BY Speso DESC;
DROP TABLE IF EXISTS #Spese;

DECLARE @Nuovi TABLE (Id INT PRIMARY KEY, Nome NVARCHAR(80));
INSERT INTO @Nuovi (Id, Nome) VALUES (1, N\'Ada\');', 'sql'),
            P('`SELECT … INTO #Tabella` crea la tabella e la riempie in un colpo solo. Le tabelle temporanee stanno nel database di sistema `tempdb`, hanno statistiche e si possono indicizzare: convengono per molte righe. Le variabili tabella sono comode per pochi dati. Per un risultato usato una volta sola basta spesso una CTE.', '`SELECT … INTO #Table` creates the table and fills it in one go. Temporary tables live in the `tempdb` system database, have statistics and can be indexed: they suit many rows. Table variables are handy for small data. For a result used only once a CTE is often enough.'),
        ], [
            Q('m1', L('Che cosa indica il `#` all’inizio del nome di una tabella?', 'What does `#` at the start of a table name mean?'), [L('Una tabella di sistema in sola lettura', 'A read-only system table'), L('Una tabella cifrata', 'An encrypted table'), L('Una tabella temporanea della sessione', 'A session temporary table'), L('Un commento', 'A comment')], 2, L('Sparisce quando la sessione si chiude.', 'It vanishes when the session closes.')),
            Q('m2', L('Che cosa fa `SELECT … INTO #Spese`?', 'What does `SELECT … INTO #Spese` do?'), [L('Crea la tabella e la riempie con il risultato', 'Creates the table and fills it with the result'), L('Aggiunge righe a una tabella che deve già esistere', 'Adds rows to a table that must already exist'), L('Esporta il risultato in un file', 'Exports the result to a file'), L('Assegna il risultato a una variabile', 'Assigns the result to a variable')], 0, L('Per aggiungere a una tabella esistente si usa `INSERT INTO … SELECT`.', 'To add to an existing table you use `INSERT INTO … SELECT`.')),
            Q('m3', L('Dove vengono salvate le tabelle temporanee?', 'Where are temporary tables stored?'), [L('Nella memoria del client', 'In the client’s memory'), L('Nel database `master`', 'In the `master` database'), L('In un file accanto alla query', 'In a file next to the query'), L('Nel database di sistema `tempdb`', 'In the `tempdb` system database')], 3, L('È condiviso da tutto il server.', 'It is shared by the whole server.')),
        ], task('sql', L('Copia in una tabella temporanea `#Attivi` gli `Id` e i `Nome` di `dbo.Clienti` con `Attivo = 1`, usando `SELECT … INTO`.', 'Copy into a temporary table `#Attivi` the `Id` and `Nome` of `dbo.Clienti` with `Attivo = 1`, using `SELECT … INTO`.'), '', [re('SELECT\s+\[?Id\]?\s*,\s*\[?Nome\]?\s+INTO\s+#Attivi\s+FROM\s+dbo\.Clienti', L('Crei la tabella temporanea', 'You create the temporary table')), re('WHERE\s+\[?Attivo\]?\s*=\s*1', L('Solo i clienti attivi', 'Only active customers'))], '', "SELECT Id, Nome\nINTO #Attivi\nFROM dbo.Clienti\nWHERE Attivo = 1;"), [
            dMatch(pMatch(), [['#Tabella', L('Temporanea della sessione', 'Session temporary')], ['##Tabella', L('Temporanea globale', 'Global temporary')], ['@Tabella', L('Variabile tabella', 'Table variable')], ['tempdb', L('Dove vivono le temporanee', 'Where temporary tables live')], ['SELECT … INTO', L('Crea e riempie', 'Creates and fills')]], L('I contenitori temporanei di T-SQL.', 'T-SQL’s temporary containers.')),
            dBug(pBug(), ['DECLARE Lista TABLE (Id INT);', 'INSERT INTO @Lista (Id) VALUES (1), (2);', 'SELECT * FROM @Lista;'], 0, 'DECLARE @Lista TABLE (Id INT);', L('Anche le variabili tabella iniziano con `@`.', 'Table variables also start with `@`.')),
        ]),
        lesson('indexes', L('Indici clustered e non clustered', 'Clustered and nonclustered indexes'), [
            P('In SQL Server ogni tabella può avere un solo indice **clustered**: decide l’ordine fisico delle righe ed è creato di base sulla chiave primaria. Gli indici **non clustered** sono strutture separate che puntano alle righe; con `INCLUDE` vi aggiungi colonne extra, così la query trova tutto nell’indice (indice *coprente*).', 'In SQL Server each table can have only one **clustered** index: it sets the physical order of the rows and is created on the primary key by default. **Nonclustered** indexes are separate structures pointing to the rows; with `INCLUDE` you add extra columns, so the query finds everything in the index (a *covering* index).'),
            C('CREATE NONCLUSTERED INDEX IX_Ordini_Cliente
ON dbo.Ordini (ClienteId, CreatoIl DESC)
INCLUDE (Totale);

-- indice filtrato: solo le righe che interessano
CREATE INDEX IX_Ordini_DaSpedire
ON dbo.Ordini (CreatoIl)
WHERE Spedito = 0;

SET STATISTICS IO ON;
SELECT CreatoIl, Totale FROM dbo.Ordini WHERE ClienteId = 42;', 'sql'),
            P('Il **piano di esecuzione effettivo** (in SSMS con Ctrl+M) mostra se la query fa un `Index Seek` (ricerca mirata, bene) o uno `Scan` (lettura completa); un `Key Lookup` ripetuto suggerisce di aggiungere colonne con `INCLUDE`. `SET STATISTICS IO ON` conta le pagine lette: meno pagine, query più leggera.', 'The **actual execution plan** (Ctrl+M in SSMS) shows whether the query does an `Index Seek` (a targeted lookup, good) or a `Scan` (a full read); a repeated `Key Lookup` suggests adding columns with `INCLUDE`. `SET STATISTICS IO ON` counts the pages read: fewer pages, lighter query.'),
        ], [
            Q('i1', L('Quanti indici clustered può avere una tabella?', 'How many clustered indexes can a table have?'), [L('Uno solo', 'Only one'), L('Uno per colonna', 'One per column'), L('Fino a 999', 'Up to 999'), L('Nessuno', 'None')], 0, L('Decide l’ordine fisico delle righe.', 'It sets the physical order of the rows.')),
            Q('i2', L('A che cosa serve `INCLUDE` in un indice?', 'What is `INCLUDE` for in an index?'), [L('A includere tutte le tabelle collegate', 'Including every linked table'), L('A rendere l’indice unico', 'Making the index unique'), L('A ordinare per più colonne', 'Sorting by several columns'), L('Ad aggiungere colonne extra per coprire la query', 'Adding extra columns to cover the query')], 3, L('Evita i `Key Lookup` sulla tabella.', 'It avoids `Key Lookup`s on the table.')),
            Q('i3', L('Che cosa è meglio vedere nel piano per una ricerca su pochi valori?', 'What is best to see in the plan for a lookup of a few values?'), ['Table Scan', 'Index Scan', 'Index Seek', 'Sort'], 2, L('Una ricerca mirata, non una lettura completa.', 'A targeted lookup, not a full read.')),
        ], task('sql', L('Crea l’indice non clustered `IX_Prodotti_Categoria` su `dbo.Prodotti (CategoriaId)` che includa le colonne `Nome` e `Prezzo`.', 'Create the nonclustered index `IX_Prodotti_Categoria` on `dbo.Prodotti (CategoriaId)` including the columns `Nome` and `Prezzo`.'), '', [re('CREATE\s+(NONCLUSTERED\s+)?INDEX\s+IX_Prodotti_Categoria\s+ON\s+dbo\.Prodotti\s*\(\s*\[?CategoriaId\]?\s*\)', L('L’indice sulla categoria', 'The index on the category')), re('INCLUDE\s*\(\s*(\[?Nome\]?\s*,\s*\[?Prezzo\]?|\[?Prezzo\]?\s*,\s*\[?Nome\]?)\s*\)', L('Include nome e prezzo', 'It includes name and price'))], '', "CREATE NONCLUSTERED INDEX IX_Prodotti_Categoria\nON dbo.Prodotti (CategoriaId)\nINCLUDE (Nome, Prezzo);"), [
            dMatch(pMatch(), [['Clustered', L('Ordine fisico delle righe', 'Physical row order')], ['Nonclustered', L('Struttura separata', 'Separate structure')], ['INCLUDE', L('Colonne extra nell’indice', 'Extra columns in the index')], ['Index Seek', L('Ricerca mirata', 'Targeted lookup')], ['Key Lookup', L('Viaggio in più verso la tabella', 'Extra trip to the table')]], L('Gli indici di SQL Server.', 'SQL Server indexes.')),
            dFlash(pFlash(), [[L('La chiave primaria crea di base un indice clustered', 'The primary key creates a clustered index by default'), true], [L('Una tabella può avere più indici clustered', 'A table can have several clustered indexes'), false], [L('Un indice filtrato copre solo alcune righe', 'A filtered index covers only some rows'), true], [L('Uno `Scan` su una tabella enorme è sempre ideale', 'A `Scan` on a huge table is always ideal'), false]], L('Seek bene, scan da controllare.', 'Seek good, scan to check.')),
        ]),
        lesson('json', L('JSON in T-SQL', 'JSON in T-SQL'), [
            P('SQL Server legge e scrive JSON con funzioni dedicate: `JSON_VALUE` estrae un valore semplice, `JSON_QUERY` un oggetto o un array, `ISJSON` controlla la validità, `OPENJSON` trasforma un documento in righe e `FOR JSON PATH` fa il contrario. Fino alla versione 2022 il JSON si salva in `NVARCHAR(MAX)`; SQL Server 2025 aggiunge un tipo **`json`** nativo, salvato in forma binaria.', 'SQL Server reads and writes JSON with dedicated functions: `JSON_VALUE` extracts a simple value, `JSON_QUERY` an object or array, `ISJSON` checks validity, `OPENJSON` turns a document into rows and `FOR JSON PATH` does the opposite. Up to the 2022 version JSON is stored in `NVARCHAR(MAX)`; SQL Server 2025 adds a native **`json`** type, stored in binary form.'),
            C('DECLARE @Doc NVARCHAR(MAX) = N\'{"cliente": "Ada", "righe": [{"cod": "A1", "qta": 2}, {"cod": "B7", "qta": 1}]}\';

SELECT JSON_VALUE(@Doc, \'$.cliente\') AS Cliente;

SELECT r.Cod, r.Qta
FROM OPENJSON(@Doc, \'$.righe\')
     WITH (Cod NVARCHAR(10) \'$.cod\', Qta INT \'$.qta\') AS r;

SELECT TOP (3) Id, Nome FROM dbo.Clienti FOR JSON PATH;', 'sql'),
            P('`OPENJSON … WITH (…)` dichiara le colonne e i loro percorsi: il risultato si usa come una tabella, per esempio in un `INSERT … SELECT`. Per una colonna JSON salvata come testo aggiungi un vincolo `CHECK (ISJSON(Colonna) = 1)`.', '`OPENJSON … WITH (…)` declares the columns and their paths: the result is used like a table, for example in an `INSERT … SELECT`. For a JSON column stored as text add a `CHECK (ISJSON(Column) = 1)` constraint.'),
        ], [
            Q('o1', L('Quale funzione estrae un valore semplice da un JSON?', 'Which function extracts a simple value from JSON?'), ['JSON_QUERY', 'JSON_VALUE', 'OPENJSON', 'ISJSON'], 1, L('`JSON_QUERY` restituisce oggetti e array.', '`JSON_QUERY` returns objects and arrays.')),
            Q('o2', L('Che cosa fa `OPENJSON`?', 'What does `OPENJSON` do?'), [L('Apre un file JSON dal disco', 'Opens a JSON file from disk'), L('Controlla che il testo sia JSON', 'Checks that the text is JSON'), L('Trasforma un documento JSON in righe', 'Turns a JSON document into rows'), L('Trasforma le righe in JSON', 'Turns rows into JSON')], 2, L('Il contrario lo fa `FOR JSON PATH`.', '`FOR JSON PATH` does the opposite.')),
            Q('o3', L('Che cosa aggiunge SQL Server 2025 per il JSON?', 'What does SQL Server 2025 add for JSON?'), [L('Un tipo `json` nativo in forma binaria', 'A native binary `json` type'), L('Il divieto di salvare JSON', 'A ban on storing JSON'), L('Gli operatori `->` di MySQL', 'MySQL’s `->` operators'), L('Niente di nuovo', 'Nothing new')], 0, L('Prima si usava `NVARCHAR(MAX)`.', 'Before, you used `NVARCHAR(MAX)`.')),
        ], task('sql', L('Dalla tabella `dbo.Ordini` leggi `Id` e il valore `$.citta` della colonna JSON `Spedizione` con `JSON_VALUE`, chiamandolo `Citta`.', 'From the table `dbo.Ordini` read `Id` and the value `$.citta` of the JSON column `Spedizione` with `JSON_VALUE`, naming it `Citta`.'), '', [re('JSON_VALUE\s*\(\s*\[?Spedizione\]?\s*,\s*N?\'\$\.citta\'\s*\)\s+(AS\s+)?\[?Citta\]?', L('Estrai la città', 'You extract the city')), re('FROM\s+dbo\.Ordini', L('Dalla tabella giusta', 'From the right table'))], '', "SELECT Id, JSON_VALUE(Spedizione, '$.citta') AS Citta\nFROM dbo.Ordini;"), [
            dMatch(pMatch(), [['JSON_VALUE', L('Un valore semplice', 'A simple value')], ['JSON_QUERY', L('Un oggetto o un array', 'An object or array')], ['ISJSON', L('Controlla la validità', 'Checks validity')], ['OPENJSON', L('JSON in righe', 'JSON into rows')], ['FOR JSON PATH', L('Righe in JSON', 'Rows into JSON')]], L('Le funzioni JSON di T-SQL.', 'T-SQL’s JSON functions.')),
            dBlanks(pFill(), "SELECT r.Cod, r.Qta\nFROM ___(@Doc, '$.righe')\n     ___ (Cod NVARCHAR(10) '$.cod', Qta INT '$.qta') AS r;\nSELECT Id, Nome FROM dbo.Clienti FOR JSON ___;", ['OPENJSON', 'WITH', 'PATH'], ['READJSON', 'AS', 'AUTO_ALL'], L('Da JSON a righe, e ritorno.', 'From JSON to rows, and back.')),
        ]),
    ],
];
