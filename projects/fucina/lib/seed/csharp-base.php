<?php
/** C# basics: .NET and the first program, types and control flow, classes and properties, collections and LINQ, async. */

return [
    'id' => 'csharp-base', 'family' => 'csharp', 'after' => null,
    'title' => L('C# base', 'C# Basics'), 'short' => L('C# base', 'C# Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di .NET per applicazioni web, desktop e giochi: tipi e controllo del flusso, classi e proprietà, collezioni e LINQ, async/await.', 'The .NET language for web, desktop and game apps: types and control flow, classes and properties, collections and LINQ, async/await.'),
    'lessons' => [
        lesson('basics', L('.NET e il primo programma', '.NET and the first program'), [
            P('**C#** è il linguaggio principale della piattaforma **.NET** di Microsoft, open source e multipiattaforma (Windows, macOS, Linux). Si usa per siti e API (ASP.NET Core), app desktop, servizi cloud e videogiochi con Unity.', '**C#** is the main language of Microsoft’s **.NET** platform, open source and cross-platform (Windows, macOS, Linux). It is used for sites and APIs (ASP.NET Core), desktop apps, cloud services and Unity video games.'),
            C('dotnet new console -o Saluti
cd Saluti
dotnet run', 'bash'),
            C('// Program.cs
string nome = "Ada";
int anni = 36;
var citta = "Trieste";            // tipo dedotto: string
Console.WriteLine($"{nome} ha {anni} anni e vive a {citta}");', 'csharp'),
            P('C# è a **tipizzazione statica**: ogni variabile ha un tipo controllato dal compilatore; `var` lo fa dedurre. Le stringhe con `$` davanti interpolano le variabili tra graffe. Le istruzioni finiscono con `;`.', 'C# is **statically typed**: every variable has a type checked by the compiler; `var` lets it be inferred. Strings prefixed with `$` interpolate variables in braces. Statements end with `;`.'),
        ], [
            Q('b1', L('Su quale piattaforma gira C#?', 'Which platform does C# run on?'), ['.NET', 'JVM', 'Node.js', 'PHP'], 0, L('Multipiattaforma.', 'Cross-platform.')),
            Q('b2', L('Che cosa fa il `$` davanti a una stringa?', 'What does `$` before a string do?'), [L('Interpola le variabili tra graffe', 'Interpolates variables in braces'), L('La rende un prezzo', 'Makes it a price'), L('La cifra', 'Encrypts it'), L('La commenta', 'Comments it')], 0, L('`$"Ciao {nome}"`.', '`$"Hi {nome}"`.')),
            Q('b3', L('Quale comando crea un progetto console?', 'Which command creates a console project?'), ['dotnet new console', 'npm init', 'cargo new', 'go mod init'], 0, L('Poi `dotnet run`.', 'Then `dotnet run`.')),
        ], task('csharp', L('Dichiara `string prodotto = "Tazza";` e `decimal prezzo = 8.50m;` e stampa `Tazza costa 8.50 euro` con l’interpolazione.', 'Declare `string prodotto = "Tazza";` and `decimal prezzo = 8.50m;` and print `Tazza costa 8.50 euro` with interpolation.'), '', [re('string\s+prodotto\s*=\s*"Tazza"\s*;', L('Il prodotto', 'The product')), re('decimal\s+prezzo\s*=\s*8\.50m\s*;', L('Il prezzo', 'The price')), re('Console\.WriteLine\s*\(\s*\$"\{prodotto\} costa \{prezzo\} euro"\s*\)', L('La stampa interpolata', 'The interpolated print'))], '', "string prodotto = \"Tazza\";\ndecimal prezzo = 8.50m;\nConsole.WriteLine(\$\"{prodotto} costa {prezzo} euro\");"), [
            dMatch(pMatch(), [['int', L('Intero', 'Integer')], ['decimal', L('Importi esatti', 'Exact amounts')], ['string', L('Testo', 'Text')], ['bool', L('Vero o falso', 'True or false')], ['var', L('Tipo dedotto', 'Inferred type')]], L('I tipi di C#.', 'C# types.')),
            dBug(pBug(), ['int quantita = 3;', 'string totale = quantita * 2;', 'Console.WriteLine(totale);'], 1, 'int totale = quantita * 2;', L('Un numero non si assegna a una stringa.', 'A number cannot be assigned to a string.')),
        ]),
        lesson('classes', L('Classi e proprietà', 'Classes and properties'), [
            P('Una **classe** descrive oggetti con dati e comportamenti. In C# i dati si espongono con le **proprietà** (`{ get; set; }`), il costruttore inizializza l’oggetto. Per dati semplici e immutabili ci sono i **record**.', 'A **class** describes objects with data and behaviour. In C# data is exposed through **properties** (`{ get; set; }`), the constructor initialises the object. For simple, immutable data there are **records**.'),
            C('public class Conto
{
    public string Titolare { get; }
    public decimal Saldo { get; private set; }

    public Conto(string titolare) => Titolare = titolare;

    public void Versa(decimal importo)
    {
        if (importo <= 0) throw new ArgumentException("Importo non valido");
        Saldo += importo;
    }
}

public record Prodotto(string Nome, decimal Prezzo);

var c = new Conto("Ada");
c.Versa(100);
var p = new Prodotto("Tazza", 8.5m);', 'csharp'),
            P('`private set` permette di leggere il saldo da fuori ma di cambiarlo solo dai metodi della classe: è l’**incapsulamento**. Gli errori si segnalano con le **eccezioni** (`throw`) e si gestiscono con `try`/`catch`.', '`private set` allows reading the balance from outside but changing it only from the class’s methods: that is **encapsulation**. Errors are signalled with **exceptions** (`throw`) and handled with `try`/`catch`.'),
        ], [
            Q('c1', L('Che cosa fa `{ get; private set; }`?', 'What does `{ get; private set; }` do?'), [L('Leggibile da fuori, modificabile solo dalla classe', 'Readable from outside, changeable only by the class'), L('Nascosta a tutti', 'Hidden from everyone'), L('Modificabile da tutti', 'Changeable by everyone'), L('Una costante globale', 'A global constant')], 0, L('Incapsulamento.', 'Encapsulation.')),
            Q('c2', L('A che cosa servono i record?', 'What are records for?'), [L('Dati semplici e immutabili', 'Simple, immutable data'), L('Registrare audio', 'Recording audio'), L('Database', 'Databases'), L('Interfacce grafiche', 'User interfaces')], 0, L('Una riga sola per definirli.', 'One line to define them.')),
            Q('c3', L('Come si segnala un errore in C#?', 'How is an error signalled in C#?'), [L('Con throw e un’eccezione', 'With throw and an exception'), L('Restituendo -1 sempre', 'Always returning -1'), L('Con un commento', 'With a comment'), L('Non si segnala', 'It is not signalled')], 0, L('E si gestisce con try/catch.', 'And handled with try/catch.')),
        ], task('csharp', L('Scrivi il record `Studente` con `string Nome` e `int Voto`, poi crea `var s = new Studente("Bo", 28);`.', 'Write the record `Studente` with `string Nome` and `int Voto`, then create `var s = new Studente("Bo", 28);`.'), '', [re('record\s+Studente\s*\(\s*string\s+Nome\s*,\s*int\s+Voto\s*\)', L('Il record', 'The record')), re('var\s+s\s*=\s*new\s+Studente\s*\(\s*"Bo"\s*,\s*28\s*\)', L('L’istanza', 'The instance'))], '', "public record Studente(string Nome, int Voto);\nvar s = new Studente(\"Bo\", 28);"), [
            dOrder(pSort(), ['public class Contatore', '{', '    public int Valore { get; private set; }', '    public void Incrementa() => Valore++;', '}'], L('Classe, proprietà, metodo.', 'Class, property, method.')),
            dMatch(pMatch(), [['class', L('Oggetti con stato', 'Objects with state')], ['record', L('Dati immutabili', 'Immutable data')], ['{ get; set; }', L('Proprietà', 'Property')], ['throw', L('Lancia un’eccezione', 'Throws an exception')], ['new', L('Crea un oggetto', 'Creates an object')]], L('Oggetti in C#.', 'Objects in C#.')),
        ]),
        lesson('linq', L('Collezioni e LINQ', 'Collections and LINQ'), [
            P('Le collezioni più usate sono **`List<T>`** (elenco che cresce) e **`Dictionary<K, V>`** (coppie chiave-valore). **LINQ** permette di filtrare, ordinare e trasformare collezioni con metodi concatenati, in modo simile a SQL.', 'The most used collections are **`List<T>`** (a growing list) and **`Dictionary<K, V>`** (key-value pairs). **LINQ** lets you filter, sort and transform collections with chained methods, much like SQL.'),
            C('var prodotti = new List<Prodotto>
{
    new("Tazza", 8.5m), new("Lampada", 35m), new("Quaderno", 3m),
};

var economici = prodotti
    .Where(p => p.Prezzo < 10)
    .OrderBy(p => p.Prezzo)
    .Select(p => p.Nome)
    .ToList();                       // ["Quaderno", "Tazza"]

decimal totale = prodotti.Sum(p => p.Prezzo);
var prezzi = new Dictionary<string, decimal> { ["Tazza"] = 8.5m };', 'csharp'),
            P('`Where` filtra, `Select` trasforma, `OrderBy` ordina, `Sum`, `Count`, `Any`, `First` riassumono. Le espressioni come `p => p.Prezzo < 10` sono **lambda**: piccole funzioni senza nome.', '`Where` filters, `Select` transforms, `OrderBy` sorts, `Sum`, `Count`, `Any`, `First` summarise. Expressions like `p => p.Prezzo < 10` are **lambdas**: small nameless functions.'),
        ], [
            Q('l1', L('Quale metodo LINQ filtra?', 'Which LINQ method filters?'), ['Where', 'Select', 'OrderBy', 'Sum'], 0, L('Come WHERE in SQL.', 'Like WHERE in SQL.')),
            Q('l2', L('Che cos’è `p => p.Prezzo < 10`?', 'What is `p => p.Prezzo < 10`?'), [L('Una lambda', 'A lambda'), L('Un confronto tra classi', 'A class comparison'), L('Un commento', 'A comment'), L('Un errore', 'An error')], 0, L('Funzione senza nome.', 'Nameless function.')),
            Q('l3', L('Quale collezione usa coppie chiave-valore?', 'Which collection uses key-value pairs?'), ['Dictionary<K, V>', 'List<T>', 'Array', 'string'], 0, L('Ricerca per chiave veloce.', 'Fast key lookup.')),
        ], task('csharp', L('Dalla lista `voti` (interi) ottieni con LINQ i voti maggiori o uguali a 18, ordinati in modo decrescente, in una lista `promossi`.', 'From the `voti` list (ints) use LINQ to get grades greater than or equal to 18, sorted descending, into a `promossi` list.'), "var voti = new List<int> { 30, 15, 22, 18, 27 };\n", [re('promossi\s*=\s*voti\s*\.\s*Where\s*\(\s*(\w+)\s*=>\s*\1\s*>=\s*18\s*\)', L('Filtri i promossi', 'You filter the passes')), re('\.OrderByDescending\s*\(\s*(\w+)\s*=>\s*\1\s*\)', L('Ordini decrescente', 'You sort descending')), re('\.ToList\s*\(\s*\)', L('Ottieni una lista', 'You get a list'))], '', "var promossi = voti.Where(v => v >= 18).OrderByDescending(v => v).ToList();"), [
            dMatch(pMatch(), [['Where', 'WHERE'], ['Select', 'SELECT'], ['OrderBy', 'ORDER BY'], ['Count', 'COUNT(*)'], ['Sum', 'SUM']], L('LINQ e SQL a confronto.', 'LINQ and SQL compared.')),
            dBlanks(pFill(), "var nomi = clienti\n    .___(c => c.Attivo)\n    .___(c => c.Nome)\n    .___();", ['Where', 'Select', 'ToList'], ['Filter', 'Map', 'ToArrayList'], L('Filtra, trasforma, materializza.', 'Filter, transform, materialise.')),
        ]),
        lesson('async', L('Async e ASP.NET Core', 'Async and ASP.NET Core'), [
            P('Le operazioni lente (rete, file, database) si scrivono con **`async`/`await`**: il metodo restituisce un **`Task`** e il thread non resta bloccato mentre aspetta. È la base delle app .NET reattive e dei server efficienti.', 'Slow operations (network, files, database) are written with **`async`/`await`**: the method returns a **`Task`** and the thread is not blocked while waiting. It is the basis of responsive .NET apps and efficient servers.'),
            C('// Program.cs di una API minimale ASP.NET Core
var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();

app.MapGet("/api/saluto/{nome}", (string nome) => new { messaggio = $"Ciao {nome}" });

app.MapGet("/api/meteo", async (HttpClient http) =>
{
    var json = await http.GetStringAsync("https://api.esempio.it/meteo");
    return Results.Content(json, "application/json");
});

app.Run();', 'csharp'),
            P('Con le **minimal API** di ASP.NET Core bastano poche righe per un endpoint JSON. Per convenzione i metodi asincroni finiscono con `Async` (`GetStringAsync`, `SaveChangesAsync`). Evita `.Result` e `.Wait()` sui Task: possono bloccare l’app.', 'With ASP.NET Core **minimal APIs** a few lines make a JSON endpoint. By convention async methods end in `Async` (`GetStringAsync`, `SaveChangesAsync`). Avoid `.Result` and `.Wait()` on Tasks: they can block the app.'),
        ], [
            Q('a1', L('Che cosa restituisce un metodo async?', 'What does an async method return?'), [L('Un Task', 'A Task'), L('Sempre void', 'Always void'), L('Una stringa', 'A string'), L('Un thread', 'A thread')], 0, L('O Task<T> con un risultato.', 'Or Task<T> with a result.')),
            Q('a2', L('Perché evitare `.Result` su un Task?', 'Why avoid `.Result` on a Task?'), [L('Può bloccare l’applicazione', 'It can block the application'), L('Non esiste', 'It does not exist'), L('È più veloce', 'It is faster'), L('Non c’è motivo', 'There is no reason')], 0, L('Usa await.', 'Use await.')),
            Q('a3', L('Che cosa fa `app.MapGet(...)`?', 'What does `app.MapGet(...)` do?'), [L('Definisce un endpoint GET', 'Defines a GET endpoint'), L('Disegna una mappa', 'Draws a map'), L('Avvia il server', 'Starts the server'), L('Crea un database', 'Creates a database')], 0, L('Minimal API.', 'Minimal API.')),
        ], task('csharp', L('Aggiungi un endpoint `app.MapGet("/api/ora", ...)` che restituisce `DateTime.Now`.', 'Add an endpoint `app.MapGet("/api/ora", ...)` returning `DateTime.Now`.'), "var builder = WebApplication.CreateBuilder(args);\nvar app = builder.Build();\n\napp.Run();", [re('app\.MapGet\s*\(\s*"/api/ora"\s*,\s*\(\s*\)\s*=>\s*DateTime\.Now\s*\)', L('L’endpoint', 'The endpoint'))], '', 'app.MapGet("/api/ora", () => DateTime.Now);'), [
            dBug(pBug(), ['public async Task<string> LeggiAsync()', '{', '    var testo = File.ReadAllTextAsync("dati.txt");', '    return testo;', '}'], 2, '    var testo = await File.ReadAllTextAsync("dati.txt");', L('Senza await hai un Task, non il testo.', 'Without await you get a Task, not the text.')),
            dMatch(pMatch(), [['async', L('Metodo asincrono', 'Async method')], ['await', L('Aspetta senza bloccare', 'Waits without blocking')], ['Task', L('Operazione in corso', 'Ongoing operation')], ['MapGet', L('Endpoint GET', 'GET endpoint')], ['.Result', L('Da evitare', 'To avoid')]], L('Asincronia in .NET.', 'Async in .NET.')),
        ]),
        lesson('records', L('Record, null e pattern matching', 'Records, null and pattern matching'), [
            P('Un **record** è un tipo pensato per i dati: si scrive in una riga, è immutabile e due record con gli stessi valori sono **uguali**. Con `with` crei una copia cambiando solo alcuni campi.', 'A **record** is a type designed for data: written in one line, immutable, and two records with the same values are **equal**. With `with` you create a copy changing only some fields.'),
            C('public record Studente(string Nome, int Voto);

var a = new Studente("Ada", 8);
var b = a with { Voto = 9 };

string giudizio = b.Voto switch {
    >= 9 => "Ottimo",
    >= 6 => "Sufficiente",
    _    => "Insufficiente"
};

string? soprannome = null;
Console.WriteLine(soprannome ?? "nessuno");', 'csharp'),
            P('Con i **tipi nullable** (`string?`) il compilatore avvisa quando un valore può essere `null`. `??` dà un valore di riserva, `?.` accede a un membro solo se l’oggetto esiste. L’espressione **`switch`** con i pattern sostituisce lunghe catene di `if`.', 'With **nullable types** (`string?`) the compiler warns when a value may be `null`. `??` gives a fallback value, `?.` accesses a member only if the object exists. The **`switch` expression** with patterns replaces long `if` chains.'),
        ], [
            Q('cr1', L('Quando due record sono uguali?', 'When are two records equal?'), [L('Quando hanno gli stessi valori', 'When they have the same values'), L('Solo se sono lo stesso oggetto', 'Only if they are the same object'), L('Mai', 'Never'), L('Se hanno lo stesso nome di tipo e basta', 'If they just share the type name')], 0, L('Uguaglianza per valore.', 'Value equality.')),
            Q('cr2', L('Che cosa fa `??`?', 'What does `??` do?'), [L('Dà un valore se a sinistra c’è null', 'Gives a value if the left is null'), L('Confronta due numeri', 'Compares two numbers'), L('Lancia un’eccezione', 'Throws an exception'), L('Concatena stringhe', 'Concatenates strings')], 0, L('Il valore di riserva.', 'The fallback value.')),
            Q('cr3', L('Che cosa fa `a with { Voto = 9 }`?', 'What does `a with { Voto = 9 }` do?'), [L('Crea una copia con Voto cambiato', 'Creates a copy with Voto changed'), L('Modifica a', 'Changes a'), L('Cancella Voto', 'Deletes Voto'), L('Confronta i voti', 'Compares marks')], 0, L('I record sono immutabili.', 'Records are immutable.')),
        ], task('csharp', L('Dichiara `public record Libro(string Titolo, int Pagine);` e stampa `nome ?? "anonimo"` con `Console.WriteLine`.', 'Declare `public record Libro(string Titolo, int Pagine);` and print `nome ?? "anonimo"` with `Console.WriteLine`.'), '', [re('public\s+record\s+Libro\(\s*string\s+Titolo\s*,\s*int\s+Pagine\s*\)\s*;', L('Il record è dichiarato', 'The record is declared')), re('Console\.WriteLine\(\s*nome\s*\?\?\s*"anonimo"\s*\)', L('Usi il valore di riserva', 'You use the fallback'))], '', "public record Libro(string Titolo, int Pagine);\nConsole.WriteLine(nome ?? \"anonimo\");"), [
            dMatch(pMatch(), [['record', L('Tipo per dati immutabili', 'Type for immutable data')], ['with', L('Copia con modifiche', 'Copy with changes')], ['??', L('Valore di riserva', 'Fallback value')], ['?.', L('Accesso sicuro', 'Safe access')], ['switch =>', L('Scelta con pattern', 'Choice with patterns')]], L('C# moderno.', 'Modern C#.')),
            dFlash(pFlash(), [[L('string? può contenere null', 'string? can hold null'), true], [L('I record si modificano in place', 'Records are changed in place'), false], [L('_ nello switch è il caso predefinito', '_ in a switch is the default case'), true], [L('?. lancia sempre un’eccezione', '?. always throws'), false]], L('Dati e null.', 'Data and null.')),
        ]),
    ],
];
