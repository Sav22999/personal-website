<?php
/** Microsoft SQL Server and T-SQL: TOP, types and IDENTITY, variables and TRY/CATCH, stored procedures, paging and MERGE. */

return [
    'id' => 'mssql', 'family' => 'mssql', 'after' => 'sql-base',
    'title' => L('SQL Server (T-SQL)', 'SQL Server (T-SQL)'), 'short' => L('SQL Server', 'SQL Server'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database di Microsoft e il suo dialetto T-SQL: `TOP`, `IDENTITY`, `NVARCHAR`, variabili, `TRY…CATCH`, stored procedure, paginazione e `MERGE`.', 'Microsoft’s database and its T-SQL dialect: `TOP`, `IDENTITY`, `NVARCHAR`, variables, `TRY…CATCH`, stored procedures, paging and `MERGE`.'),
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
    ROLLBACK;
    PRINT ERROR_MESSAGE();
END CATCH;', 'sql'),
            P('`OUTPUT inserted.Id` dopo un `INSERT` restituisce le righe appena inserite, come `RETURNING` in PostgreSQL.', '`OUTPUT inserted.Id` after an `INSERT` returns the rows just inserted, like `RETURNING` in PostgreSQL.'),
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
    ],
];
