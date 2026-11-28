<?php
/** C# basics: .NET and the first program, types, classes and properties, collections and LINQ, async, records and null, control flow, methods, inheritance and exceptions. */

return [
    'id' => 'csharp-base', 'family' => 'csharp', 'after' => null,
    'title' => L('C# base', 'C# Basics'), 'short' => L('C# base', 'C# Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio di .NET per applicazioni web, desktop e giochi: tipi e controllo del flusso, metodi, classi, interfacce ed ereditarietà, eccezioni, collezioni e LINQ, async/await.', 'The .NET language for web, desktop and game apps: types and control flow, methods, classes, interfaces and inheritance, exceptions, collections and LINQ, async/await.'),
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
            P('C# è a **tipizzazione statica**: ogni variabile ha un tipo controllato dal compilatore; `var` lo fa dedurre. Le stringhe con `$` davanti interpolano le variabili tra graffe. Le istruzioni finiscono con `;`. Da .NET 10 un singolo file si esegue anche senza progetto: `dotnet run saluti.cs`.', 'C# is **statically typed**: every variable has a type checked by the compiler; `var` lets it be inferred. Strings prefixed with `$` interpolate variables in braces. Statements end with `;`. Since .NET 10 a single file also runs without a project: `dotnet run saluti.cs`.'),
        ], [
            Q('b1', L('Su quale piattaforma gira C#?', 'Which platform does C# run on?'), ['.NET', 'JVM', 'Node.js', 'PHP'], 0, L('Multipiattaforma.', 'Cross-platform.')),
            Q('b2', L('Che cosa fa il `$` davanti a una stringa?', 'What does `$` before a string do?'), [L('Interpola le variabili tra graffe', 'Interpolates variables in braces'), L('La rende un prezzo', 'Makes it a price'), L('La cifra', 'Encrypts it'), L('La commenta', 'Comments it')], 0, L('`$"Ciao {nome}"`.', '`$"Hi {nome}"`.')),
            Q('b3', L('Quale comando crea un progetto console?', 'Which command creates a console project?'), ['dotnet new console', 'npm init', 'cargo new', 'go mod init'], 0, L('Poi `dotnet run`.', 'Then `dotnet run`.')),
        ], task('csharp', L('Dichiara `string prodotto = "Tazza";` e `decimal prezzo = 8.50m;` e stampa `$"{prodotto} costa {prezzo} euro"` con `Console.WriteLine` (su un computer italiano il prezzo appare come 8,50).', 'Declare `string prodotto = "Tazza";` and `decimal prezzo = 8.50m;` and print `$"{prodotto} costa {prezzo} euro"` with `Console.WriteLine` (on an Italian computer the price shows as 8,50).'), '', [re('string\s+prodotto\s*=\s*"Tazza"\s*;', L('Il prodotto', 'The product')), re('decimal\s+prezzo\s*=\s*8\.50m\s*;', L('Il prezzo', 'The price')), re('Console\.WriteLine\s*\(\s*\$"\{prodotto\} costa \{prezzo\} euro"\s*\)', L('La stampa interpolata', 'The interpolated print'))], '', "string prodotto = \"Tazza\";\ndecimal prezzo = 8.50m;\nConsole.WriteLine(\$\"{prodotto} costa {prezzo} euro\");"), [
            dMatch(pMatch(), [['int', L('Intero', 'Integer')], ['decimal', L('Importi esatti', 'Exact amounts')], ['string', L('Testo', 'Text')], ['bool', L('Vero o falso', 'True or false')], ['var', L('Tipo dedotto', 'Inferred type')]], L('I tipi di C#.', 'C# types.')),
            dBug(pBug(), ['int quantita = 3;', 'string totale = quantita * 2;', 'Console.WriteLine(totale);'], 1, 'int totale = quantita * 2;', L('Un numero non si assegna a una stringa.', 'A number cannot be assigned to a string.')),
        ]),
        lesson('classes', L('Classi e proprietà', 'Classes and properties'), [
            P('Una **classe** descrive oggetti con dati e comportamenti. In C# i dati si espongono con le **proprietà** (`{ get; set; }`), il costruttore inizializza l’oggetto. Per dati semplici e immutabili ci sono i **record**.', 'A **class** describes objects with data and behaviour. In C# data is exposed through **properties** (`{ get; set; }`), the constructor initialises the object. For simple, immutable data there are **records**.'),
            C('var c = new Conto("Ada");
c.Versa(100);
var p = new Prodotto("Tazza", 8.5m);

public class Conto
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

public record Prodotto(string Nome, decimal Prezzo);', 'csharp'),
            P('`private set` permette di leggere il saldo da fuori ma di cambiarlo solo dai metodi della classe: è l’**incapsulamento**. Gli errori si segnalano con le **eccezioni** (`throw`) e si gestiscono con `try`/`catch`. In un `Program.cs` con istruzioni di primo livello, classi e record vanno scritti dopo le istruzioni.', '`private set` allows reading the balance from outside but changing it only from the class’s methods: that is **encapsulation**. Errors are signalled with **exceptions** (`throw`) and handled with `try`/`catch`. In a `Program.cs` with top-level statements, classes and records go after the statements.'),
        ], [
            Q('c1', L('Che cosa fa `{ get; private set; }`?', 'What does `{ get; private set; }` do?'), [L('Leggibile da fuori, modificabile solo dalla classe', 'Readable from outside, changeable only by the class'), L('Nascosta a tutti', 'Hidden from everyone'), L('Modificabile da tutti', 'Changeable by everyone'), L('Una costante globale', 'A global constant')], 0, L('Incapsulamento.', 'Encapsulation.')),
            Q('c2', L('A che cosa servono i record?', 'What are records for?'), [L('Dati semplici e immutabili', 'Simple, immutable data'), L('Registrare audio', 'Recording audio'), L('Database', 'Databases'), L('Interfacce grafiche', 'User interfaces')], 0, L('Una riga sola per definirli.', 'One line to define them.')),
            Q('c3', L('Come si segnala un errore in C#?', 'How is an error signalled in C#?'), [L('Con throw e un’eccezione', 'With throw and an exception'), L('Restituendo -1 sempre', 'Always returning -1'), L('Con un commento', 'With a comment'), L('Non si segnala', 'It is not signalled')], 0, L('E si gestisce con try/catch.', 'And handled with try/catch.')),
        ], task('csharp', L('Scrivi il record `Studente` con `string Nome` e `int Voto`, poi crea `var s = new Studente("Bo", 28);`.', 'Write the record `Studente` with `string Nome` and `int Voto`, then create `var s = new Studente("Bo", 28);`.'), '', [re('record\s+Studente\s*\(\s*string\s+Nome\s*,\s*int\s+Voto\s*\)', L('Il record', 'The record')), re('var\s+s\s*=\s*new\s+Studente\s*\(\s*"Bo"\s*,\s*28\s*\)', L('L’istanza', 'The instance'))], '', "var s = new Studente(\"Bo\", 28);\n\npublic record Studente(string Nome, int Voto);"), [
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
builder.Services.AddHttpClient();
var app = builder.Build();

app.MapGet("/api/saluto/{nome}", (string nome) => new { messaggio = $"Ciao {nome}" });

app.MapGet("/api/meteo", async (IHttpClientFactory fabbrica) =>
{
    var http = fabbrica.CreateClient();
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
            C('var a = new Studente("Ada", 8);
var b = a with { Voto = 9 };

string giudizio = b.Voto switch {
    >= 9 => "Ottimo",
    >= 6 => "Sufficiente",
    _    => "Insufficiente"
};

string? soprannome = null;
Console.WriteLine(soprannome ?? "nessuno");

public record Studente(string Nome, int Voto);', 'csharp'),
            P('Con i **tipi nullable** (`string?`) il compilatore avvisa quando un valore può essere `null`. `??` dà un valore di riserva, `?.` accede a un membro solo se l’oggetto esiste. L’espressione **`switch`** con i pattern sostituisce lunghe catene di `if`.', 'With **nullable types** (`string?`) the compiler warns when a value may be `null`. `??` gives a fallback value, `?.` accesses a member only if the object exists. The **`switch` expression** with patterns replaces long `if` chains.'),
        ], [
            Q('cr1', L('Quando due record sono uguali?', 'When are two records equal?'), [L('Quando hanno gli stessi valori', 'When they have the same values'), L('Solo se sono lo stesso oggetto', 'Only if they are the same object'), L('Mai', 'Never'), L('Se hanno lo stesso nome di tipo e basta', 'If they just share the type name')], 0, L('Uguaglianza per valore.', 'Value equality.')),
            Q('cr2', L('Che cosa fa `??`?', 'What does `??` do?'), [L('Dà un valore se a sinistra c’è null', 'Gives a value if the left is null'), L('Confronta due numeri', 'Compares two numbers'), L('Lancia un’eccezione', 'Throws an exception'), L('Concatena stringhe', 'Concatenates strings')], 0, L('Il valore di riserva.', 'The fallback value.')),
            Q('cr3', L('Che cosa fa `a with { Voto = 9 }`?', 'What does `a with { Voto = 9 }` do?'), [L('Crea una copia con Voto cambiato', 'Creates a copy with Voto changed'), L('Modifica a', 'Changes a'), L('Cancella Voto', 'Deletes Voto'), L('Confronta i voti', 'Compares marks')], 0, L('I record sono immutabili.', 'Records are immutable.')),
        ], task('csharp', L('Dichiara `public record Libro(string Titolo, int Pagine);` e stampa `nome ?? "anonimo"` con `Console.WriteLine`.', 'Declare `public record Libro(string Titolo, int Pagine);` and print `nome ?? "anonimo"` with `Console.WriteLine`.'), '', [re('public\s+record\s+Libro\(\s*string\s+Titolo\s*,\s*int\s+Pagine\s*\)\s*;', L('Il record è dichiarato', 'The record is declared')), re('Console\.WriteLine\(\s*nome\s*\?\?\s*"anonimo"\s*\)', L('Usi il valore di riserva', 'You use the fallback'))], '', "Console.WriteLine(nome ?? \"anonimo\");\n\npublic record Libro(string Titolo, int Pagine);"), [
            dMatch(pMatch(), [['record', L('Tipo per dati immutabili', 'Type for immutable data')], ['with', L('Copia con modifiche', 'Copy with changes')], ['??', L('Valore di riserva', 'Fallback value')], ['?.', L('Accesso sicuro', 'Safe access')], ['switch =>', L('Scelta con pattern', 'Choice with patterns')]], L('C# moderno.', 'Modern C#.')),
            dFlash(pFlash(), [[L('string? può contenere null', 'string? can hold null'), true], [L('I record si modificano in place', 'Records are changed in place'), false], [L('_ nello switch è il caso predefinito', '_ in a switch is the default case'), true], [L('?. lancia sempre un’eccezione', '?. always throws'), false]], L('Dati e null.', 'Data and null.')),
        ]),
        lesson('control-flow', L('Condizioni e cicli', 'Conditions and loops'), [
            P('`if`, `else if` ed `else` scelgono che cosa fare; `for`, `while` e `foreach` ripetono. `foreach` scorre ogni elemento di una collezione senza contatore: è il ciclo più usato con liste e array.', '`if`, `else if` and `else` choose what to do; `for`, `while` and `foreach` repeat. `foreach` walks every item of a collection without a counter: it is the loop most used with lists and arrays.'),
            C('int voto = 8;
if (voto >= 9) Console.WriteLine("Ottimo");
else if (voto >= 6) Console.WriteLine("Promosso");
else Console.WriteLine("Da ripassare");

for (int i = 1; i <= 3; i++)
    Console.WriteLine($"Giro {i}");

string[] frutta = { "mela", "pera" };
foreach (var f in frutta)
    Console.WriteLine(f);

int n = 3;
while (n > 0) n--;

switch (DateTime.Today.DayOfWeek)
{
    case DayOfWeek.Saturday:
    case DayOfWeek.Sunday:
        Console.WriteLine("Weekend");
        break;
    default:
        Console.WriteLine("Si lavora");
        break;
}', 'csharp'),
            P('Nello `switch` classico ogni sezione non vuota finisce con `break` (o `return`): C# non permette di «cadere» per sbaglio nella sezione successiva. Per scegliere un valore c’è l’espressione `switch` con `=>`, che vedrai nella lezione sui record.', 'In the classic `switch` every non-empty section ends with `break` (or `return`): C# does not let you “fall through” into the next section by mistake. To pick a value there is the `switch` expression with `=>`, which you will meet in the records lesson.'),
        ], [
            Q('cf1', L('Quale ciclo scorre gli elementi senza indice?', 'Which loop walks the items without an index?'), ['for', 'while', 'foreach', 'do'], 2, L('`foreach (var x in lista)`.', '`foreach (var x in lista)`.')),
            Q('cf2', L('Che cosa succede se una sezione non vuota dello `switch` classico non finisce con `break`?', 'What happens if a non-empty section of the classic `switch` does not end with `break`?'), [L('Esegue anche la sezione dopo', 'It runs the next section too'), L('Errore di compilazione', 'Compile error'), L('Il programma si ferma', 'The program stops'), L('Niente, viene ignorato', 'Nothing, it is ignored')], 1, L('Così si evitano bug difficili da vedere.', 'This avoids bugs that are hard to spot.')),
            Q('cf3', L('Quante volte gira `for (int i = 0; i < 5; i += 2)`?', 'How many times does `for (int i = 0; i < 5; i += 2)` run?'), ['5', '2', '4', '3'], 3, L('i vale 0, 2 e 4.', 'i is 0, 2 and 4.')),
        ], task('csharp', L('Con `foreach` stampa ogni nome dell’array `nomi`.', 'With `foreach` print every name in the `nomi` array.'), 'string[] nomi = { "Ada", "Bea", "Cleo" };
', [re('foreach\s*\(\s*(var|string)\s+(\w+)\s+in\s+nomi\s*\)', L('Scorri l’array', 'You walk the array')), re('Console\.WriteLine\s*\(\s*\w+\s*\)', L('Stampi ogni nome', 'You print each name'))], '', 'foreach (var n in nomi)
{
    Console.WriteLine(n);
}'), [
            dBlanks(pFill(), 'for (int i = 0; i ___ 3; i++)
    Console.WriteLine(i);
___ (var x ___ lista)
    Console.WriteLine(x);', ['<', 'foreach', 'in'], ['=<', 'for', 'of'], L('Limite, ciclo sugli elementi, parola chiave in.', 'Limit, loop over items, the in keyword.')),
            dBug(pBug(), ['switch (colore)', '{', '    case "rosso":', '        Console.WriteLine("Stop");', '    case "verde":', '        Console.WriteLine("Vai");', '        break;', '}'], 3, '        Console.WriteLine("Stop"); break;', L('In C# ogni sezione non vuota dello switch deve finire con break.', 'In C# every non-empty switch section must end with break.')),
        ]),
        lesson('methods', L('Metodi e parametri', 'Methods and parameters'), [
            P('Un metodo dichiara tipo di ritorno, nome e parametri tipizzati; `void` se non restituisce nulla. Un parametro può avere un **valore predefinito** e si può passare **per nome**. `=>` è la forma breve per i metodi di una sola espressione.', 'A method declares its return type, name and typed parameters; `void` if it returns nothing. A parameter can have a **default value** and can be passed **by name**. `=>` is the short form for single-expression methods.'),
            C('Console.WriteLine(Area(3, 4));                  // 12
Console.WriteLine(Saluta("Ada"));               // Ciao Ada!
Console.WriteLine(Saluta("Bea", saluto: "Hey")); // Hey Bea!
if (int.TryParse("42", out int numero))
    Console.WriteLine(numero + 1);              // 43

static double Area(double b, double h) => b * h;

static string Saluta(string nome, string saluto = "Ciao")
{
    return $"{saluto} {nome}!";
}', 'csharp'),
            P('I parametri `out` restituiscono un valore in più: `int.TryParse` dice se la conversione è riuscita e mette il numero in `numero`, senza lanciare eccezioni. Due metodi possono avere lo stesso nome con parametri diversi: è l’**overloading**.', '`out` parameters return an extra value: `int.TryParse` says whether the conversion worked and puts the number in `numero`, without throwing. Two methods can share a name with different parameters: that is **overloading**.'),
        ], [
            Q('me1', L('Che cosa restituisce `int.TryParse("ciao", out int n)`?', 'What does `int.TryParse("ciao", out int n)` return?'), ['0', 'false', 'null', '-1'], 1, L('E `n` vale 0.', 'And `n` is 0.')),
            Q('me2', L('Che cosa fa `Saluta("Bea", saluto: "Hey")`?', 'What does `Saluta("Bea", saluto: "Hey")` do?'), [L('Passa il parametro saluto per nome', 'Passes the saluto parameter by name'), L('Crea una variabile globale', 'Creates a global variable'), L('Dà errore perché l’ordine è sbagliato', 'Fails because the order is wrong'), L('Ignora il secondo argomento', 'Ignores the second argument')], 0, L('Rende chiare le chiamate con molti parametri.', 'It makes calls with many parameters clear.')),
            Q('me3', L('Che cosa significa `static double Area(double b, double h) => b * h;`?', 'What does `static double Area(double b, double h) => b * h;` mean?'), [L('Un campo di sola lettura', 'A read-only field'), L('Una lambda salvata in una variabile', 'A lambda stored in a variable'), L('Un metodo che restituisce b * h, in forma breve', 'A method returning b * h, in short form'), L('Una proprietà calcolata', 'A computed property')], 2, L('`=>` sostituisce `{ return …; }`.', '`=>` replaces `{ return …; }`.')),
        ], task('csharp', L('Scrivi il metodo `static int Doppio(int n)` che restituisce `n * 2`, nella forma breve con `=>`.', 'Write the method `static int Doppio(int n)` returning `n * 2`, in the short form with `=>`.'), '', [re('static\s+int\s+Doppio\s*\(\s*int\s+n\s*\)\s*=>\s*n\s*\*\s*2\s*;', L('Il metodo è giusto', 'The method is right'))], '', 'static int Doppio(int n) => n * 2;'), [
            dMatch(pMatch(), [['void', L('Non restituisce nulla', 'Returns nothing')], ['out', L('Valore restituito in più', 'Extra returned value')], ['= "Ciao"', L('Valore predefinito', 'Default value')], ['saluto: "Hey"', L('Argomento per nome', 'Named argument')], ['=>', L('Corpo di una sola espressione', 'Single-expression body')]], L('Le parti di un metodo.', 'The parts of a method.')),
            dFlash(pFlash(), [[L('Un metodo `void` restituisce un valore', 'A `void` method returns a value'), false], [L('`int.TryParse` non lancia eccezioni se il testo non è un numero', '`int.TryParse` does not throw if the text is not a number'), true], [L('I parametri con valore predefinito vanno dopo quelli obbligatori', 'Parameters with a default value go after the required ones'), true], [L('Due metodi non possono mai avere lo stesso nome', 'Two methods can never share a name'), false]], L('L’overloading permette nomi uguali con parametri diversi.', 'Overloading allows equal names with different parameters.')),
        ]),
        lesson('inheritance', L('Interfacce ed ereditarietà', 'Interfaces and inheritance'), [
            P('Una classe eredita da **una sola** classe base con `:` ma può implementare **molte interfacce**, che per convenzione iniziano con `I`. Un metodo `abstract` va scritto dalle classi figlie, uno `virtual` si può ridefinire con `override`. Una variabile `Forma` può contenere un `Cerchio`: è il **polimorfismo**.', 'A class inherits from **a single** base class with `:` but can implement **many interfaces**, which by convention start with `I`. An `abstract` method must be written by child classes, a `virtual` one can be redefined with `override`. A `Forma` variable can hold a `Cerchio`: that is **polymorphism**.'),
            C('Forma[] forme = { new Cerchio(1), new Quadrato(2) };
foreach (var f in forme)
    Console.WriteLine(f.Descrivi());

public interface IStampabile
{
    string Descrivi();
}

public abstract class Forma : IStampabile
{
    public abstract string Nome { get; }
    public abstract double Area();
    public virtual string Descrivi() => $"{Nome} di area {Area():F1}";
}

public class Cerchio(double raggio) : Forma
{
    public override string Nome => "Cerchio";
    public override double Area() => Math.PI * raggio * raggio;
}

public class Quadrato(double lato) : Forma
{
    public override string Nome => "Quadrato";
    public override double Area() => lato * lato;
}', 'csharp'),
            P('`public class Cerchio(double raggio)` usa il **costruttore primario** (C# 12): i parametri sono visibili in tutta la classe. `base` chiama la versione della classe genitore.', '`public class Cerchio(double raggio)` uses the **primary constructor** (C# 12): its parameters are visible throughout the class. `base` calls the parent class’s version.'),
        ], [
            Q('ih1', L('Con quale simbolo una classe eredita da un’altra in C#?', 'Which symbol makes a class inherit from another in C#?'), ['extends', 'implements', ':', '=>'], 2, L('Lo stesso simbolo vale per le interfacce.', 'The same symbol works for interfaces.')),
            Q('ih2', L('Che cosa deve fare una classe figlia con un metodo `abstract`?', 'What must a child class do with an `abstract` method?'), [L('Niente, è facoltativo', 'Nothing, it is optional'), L('Scriverlo con override', 'Write it with override'), L('Chiamarlo nel costruttore', 'Call it in the constructor'), L('Renderlo static', 'Make it static')], 1, L('Altrimenti anche la figlia deve essere astratta.', 'Otherwise the child must be abstract too.')),
            Q('ih3', L('Quante interfacce può implementare una classe?', 'How many interfaces can a class implement?'), [L('Una sola', 'Only one'), L('Al massimo due', 'At most two'), L('Nessuna, solo le struct possono', 'None, only structs can'), L('Quante ne vuole', 'As many as it likes')], 3, L('Separate da virgole dopo la classe base.', 'Separated by commas after the base class.')),
        ], task('csharp', L('Crea `public class Cane : Animale` che sovrascrive `public override string Verso() => "Bau";`.', 'Create `public class Cane : Animale` overriding `public override string Verso() => "Bau";`.'), 'public abstract class Animale
{
    public abstract string Verso();
}
', [re('class\s+Cane\s*:\s*Animale', L('Cane eredita da Animale', 'Cane inherits from Animale')), re('public\s+override\s+string\s+Verso\s*\(\s*\)\s*(=>\s*"Bau"|\{\s*return\s+"Bau"\s*;\s*\})', L('Il metodo è sovrascritto', 'The method is overridden'))], '', 'public class Cane : Animale
{
    public override string Verso() => "Bau";
}'), [
            dBlanks(pFill(), 'public ___ class Veicolo
{
    public abstract int Ruote { get; }
}
public class Bici : Veicolo
{
    public ___ int Ruote => 2;
}
Veicolo v = ___ Bici();', ['abstract', 'override', 'new'], ['virtual', 'base', 'make'], L('Classe astratta, ridefinizione, creazione.', 'Abstract class, override, creation.')),
            dMatch(pMatch(), [['interface', L('Contratto di metodi', 'Contract of methods')], ['abstract', L('Da completare nelle figlie', 'To be completed by children')], ['virtual', L('Si può ridefinire', 'Can be redefined')], ['override', L('Ridefinisce', 'Redefines')], ['base', L('La classe genitore', 'The parent class')]], L('Le parole dell’ereditarietà.', 'The words of inheritance.')),
        ]),
        lesson('exceptions', L('Eccezioni e using', 'Exceptions and using'), [
            P('Le operazioni che possono fallire stanno in un `try`; i blocchi `catch` si scrivono dal più specifico al più generico, ed entra il primo che corrisponde. `finally` gira sempre. `using` chiama da solo `Dispose` su file e connessioni quando la variabile esce dal blocco.', 'Operations that may fail go in a `try`; `catch` blocks are written from the most specific to the most general, and the first one that matches runs. `finally` always runs. `using` calls `Dispose` on files and connections by itself when the variable leaves the block.'),
            C('try
{
    string testo = File.ReadAllText("prezzi.txt");
    decimal prezzo = decimal.Parse(testo);
    Console.WriteLine(prezzo * 1.22m);
}
catch (FileNotFoundException)
{
    Console.WriteLine("File mancante");
}
catch (FormatException)
{
    Console.WriteLine("Il file non contiene un numero");
}
finally
{
    Console.WriteLine("Fine");
}

using var lettore = new StreamReader("log.txt");   // chiuso alla fine del blocco
Console.WriteLine(lettore.ReadLine());', 'csharp'),
            P('Lancia eccezioni per i casi davvero eccezionali; per i controlli comuni preferisci metodi come `TryParse`. Dentro un `catch`, `throw;` rilancia l’eccezione mantenendo la traccia originale, mentre `throw ex;` la perde.', 'Throw exceptions for truly exceptional cases; for common checks prefer methods such as `TryParse`. Inside a `catch`, `throw;` rethrows the exception keeping the original trace, while `throw ex;` loses it.'),
        ], [
            Q('ex1', L('In che ordine vanno i blocchi `catch`?', 'In what order do `catch` blocks go?'), [L('Dal più specifico al più generico', 'From the most specific to the most general'), L('Dal più generico al più specifico', 'From the most general to the most specific'), L('In ordine alfabetico', 'In alphabetical order'), L('L’ordine non conta', 'Order does not matter')], 0, L('Un `catch (Exception)` in cima catturerebbe tutto.', 'A `catch (Exception)` at the top would catch everything.')),
            Q('ex2', L('Che cosa fa `using var f = ...;`?', 'What does `using var f = ...;` do?'), [L('Importa uno spazio dei nomi', 'Imports a namespace'), L('Rende f immutabile', 'Makes f immutable'), L('Crea una copia di f', 'Creates a copy of f'), L('Chiude la risorsa da sola alla fine del blocco', 'Closes the resource by itself at the end of the block')], 3, L('Anche se nel frattempo arriva un’eccezione.', 'Even if an exception arrives meanwhile.')),
            Q('ex3', L('Che differenza c’è tra `throw;` e `throw ex;` in un catch?', 'What is the difference between `throw;` and `throw ex;` in a catch?'), [L('Nessuna', 'None'), L('`throw;` mantiene la traccia originale dello stack', '`throw;` keeps the original stack trace'), L('`throw ex;` è più veloce', '`throw ex;` is faster'), L('`throw;` ignora l’eccezione', '`throw;` ignores the exception')], 1, L('La traccia dice dove è nato l’errore.', 'The trace tells where the error began.')),
        ], task('csharp', L('Converti `testo` con `int.Parse` dentro un `try` e cattura `FormatException` stampando `"Non è un numero"`.', 'Convert `testo` with `int.Parse` inside a `try` and catch `FormatException` printing `"Non è un numero"`.'), 'string testo = Console.ReadLine() ?? "";
', [re('try\s*\{[\s\S]*int\.Parse\s*\(\s*testo\s*\)', L('Provi la conversione', 'You try the conversion')), re('catch\s*\(\s*FormatException(\s+\w+)?\s*\)\s*\{\s*Console\.WriteLine\s*\(\s*"Non è un numero"\s*\)', L('Gestisci l’errore', 'You handle the error'))], '', 'try
{
    int n = int.Parse(testo);
}
catch (FormatException)
{
    Console.WriteLine("Non è un numero");
}'), [
            dOrder(pSort(), ['try { Salva(); }', 'catch (IOException ex) { Log(ex); }', 'catch (Exception ex) { Log(ex); throw; }', 'finally { Chiudi(); }'], L('Prova, errore specifico, errore generico, chiusura.', 'Try, specific error, general error, clean-up.')),
            dMatch(pMatch(), [['try', L('Prova il codice', 'Tries the code')], ['catch', L('Gestisce l’errore', 'Handles the error')], ['finally', L('Gira sempre', 'Always runs')], ['throw;', L('Rilancia', 'Rethrows')], ['using', L('Chiude la risorsa', 'Disposes the resource')]], L('Gestire gli errori in C#.', 'Handling errors in C#.')),
        ]),
    ],
];
