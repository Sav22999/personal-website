<?php
/** C# intermediate: generics, delegates and events, advanced LINQ, EF Core, dependency injection, advanced async, xUnit and modern C# (12–14). */

return [
    'id' => 'csharp-intermediate', 'family' => 'csharp', 'after' => 'csharp-base',
    'title' => L('C# intermedio', 'Intermediate C#'), 'short' => L('C# intermedio', 'C# Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('C# per applicazioni vere: generics, delegati ed eventi, LINQ avanzato, database con EF Core, iniezione delle dipendenze, async avanzato, test con xUnit e le novità di C# 14.', 'C# for real applications: generics, delegates and events, advanced LINQ, databases with EF Core, dependency injection, advanced async, tests with xUnit and what is new in C# 14.'),
    'lessons' => [
        lesson('generics', L('Generics e vincoli', 'Generics and constraints'), [
            P('I **generics** scrivono codice una volta per molti tipi, con il controllo del compilatore e senza conversioni. `where T : ...` pone un **vincolo**: qui `T` deve essere confrontabile. A differenza di Java, in .NET i generics esistono anche a runtime: `List<int>` contiene davvero `int`, senza boxing.', '**Generics** write code once for many types, with the compiler’s checks and no conversions. `where T : ...` sets a **constraint**: here `T` must be comparable. Unlike Java, in .NET generics exist at runtime too: `List<int>` really holds `int`, with no boxing.'),
            C('var pila = new Pila<string>();
pila.Metti("a");
pila.Metti("b");
Console.WriteLine(pila.Togli());                // b
Console.WriteLine(Massimo(3, 7));               // 7

static T Massimo<T>(T a, T b) where T : IComparable<T>
    => a.CompareTo(b) >= 0 ? a : b;

public class Pila<T>
{
    private readonly List<T> _elementi = [];
    public void Metti(T valore) => _elementi.Add(valore);
    public T Togli()
    {
        var ultimo = _elementi[^1];
        _elementi.RemoveAt(_elementi.Count - 1);
        return ultimo;
    }
    public int Quanti => _elementi.Count;
}', 'csharp'),
            P('Altri vincoli utili: `where T : class` (tipo riferimento), `where T : struct` (tipo valore), `where T : new()` (costruttore senza parametri). `^1` indica l’ultimo elemento contando dalla fine.', 'Other useful constraints: `where T : class` (reference type), `where T : struct` (value type), `where T : new()` (parameterless constructor). `^1` means the last item, counting from the end.'),
        ], [
            Q('g1', L('Che cosa significa `where T : IComparable<T>`?', 'What does `where T : IComparable<T>` mean?'), [L('T deve essere un intero', 'T must be an integer'), L('T deve implementare IComparable<T>', 'T must implement IComparable<T>'), L('T deve essere una classe astratta', 'T must be an abstract class'), L('T deve avere un costruttore vuoto', 'T must have an empty constructor')], 1, L('Così dentro il metodo si può chiamare CompareTo.', 'So inside the method you can call CompareTo.')),
            Q('g2', L('Che cosa dà `lista[^1]`?', 'What does `lista[^1]` give?'), [L('Il primo elemento', 'The first item'), L('Un errore di sintassi', 'A syntax error'), L('La lunghezza della lista', 'The list’s length'), L('L’ultimo elemento', 'The last item')], 3, L('`^` conta dalla fine.', '`^` counts from the end.')),
            Q('g3', L('Si può scrivere `List<int>` in C#?', 'Can you write `List<int>` in C#?'), [L('Sì, e i valori int sono salvati senza boxing', 'Yes, and the int values are stored without boxing'), L('No, serve List<Integer>', 'No, it needs List<Integer>'), L('Solo nel codice unsafe', 'Only in unsafe code'), L('Solo nelle versioni vecchie', 'Only in old versions')], 0, L('Una differenza importante rispetto a Java.', 'An important difference from Java.')),
        ], task('csharp', L('Scrivi `static T Primo<T>(List<T> lista) => lista[0];`.', 'Write `static T Primo<T>(List<T> lista) => lista[0];`.'), '', [re('static\s+T\s+Primo\s*<\s*T\s*>\s*\(\s*List\s*<\s*T\s*>\s+lista\s*\)\s*(=>\s*lista\s*\[\s*0\s*\]\s*;|\{\s*return\s+lista\s*\[\s*0\s*\]\s*;\s*\})', L('Il metodo generico è giusto', 'The generic method is right'))], '', 'static T Primo<T>(List<T> lista) => lista[0];'), [
            dBlanks(pFill(), 'public class Scatola___
{
    public T? Contenuto { get; set; }
}
static T Copia<T>(T x) ___ T : ___ => x;', ['<T>', 'where', 'class'], ['[T]', 'when', 'object()'], L('Parametro di tipo, vincolo, tipo riferimento.', 'Type parameter, constraint, reference type.')),
            dFlash(pFlash(), [[L('I generics di .NET esistono anche a runtime', '.NET generics exist at runtime too'), true], [L('`where T : new()` richiede un costruttore senza parametri', '`where T : new()` requires a parameterless constructor'), true], [L('`List<int>` non è permesso', '`List<int>` is not allowed'), false], [L('Un metodo generico deve stare in una classe generica', 'A generic method must live in a generic class'), false]], L('Un codice, tanti tipi.', 'One piece of code, many types.')),
        ]),
        lesson('delegates-events', L('Delegati, Func ed eventi', 'Delegates, Func and events'), [
            P('Un **delegato** è un tipo che rappresenta un metodo. `Func<...>` restituisce un valore (l’ultimo tipo è il risultato), `Action<...>` no, `Predicate<T>` dice vero o falso. Le lambda di LINQ sono proprio dei `Func`.', 'A **delegate** is a type that represents a method. `Func<...>` returns a value (the last type is the result), `Action<...>` does not, `Predicate<T>` says true or false. LINQ lambdas are exactly `Func`s.'),
            C('Func<int, int, int> somma = (a, b) => a + b;
Action<string> stampa = msg => Console.WriteLine(msg);
Predicate<int> pari = n => n % 2 == 0;

stampa($"2 + 3 = {somma(2, 3)}");

var termostato = new Termostato();
termostato.TroppoCaldo += (_, gradi) => Console.WriteLine($"Allarme: {gradi} gradi");
termostato.Misura(31);

public class Termostato
{
    public event EventHandler<double>? TroppoCaldo;

    public void Misura(double gradi)
    {
        if (gradi > 30) TroppoCaldo?.Invoke(this, gradi);
    }
}', 'csharp'),
            P('Un **evento** è un delegato a cui altri si iscrivono con `+=` e si disiscrivono con `-=`; solo la classe che lo dichiara può lanciarlo. `?.Invoke` lo lancia solo se qualcuno ascolta.', 'An **event** is a delegate others subscribe to with `+=` and unsubscribe from with `-=`; only the class declaring it can raise it. `?.Invoke` raises it only if someone is listening.'),
        ], [
            Q('d1', L('In `Func<int, string>`, che cosa è `string`?', 'In `Func<int, string>`, what is `string`?'), [L('Il tipo del parametro', 'The parameter type'), L('Il tipo restituito', 'The return type'), L('Il nome della funzione', 'The function’s name'), L('Un vincolo generico', 'A generic constraint')], 1, L('L’ultimo tipo è sempre il risultato.', 'The last type is always the result.')),
            Q('d2', L('Che differenza c’è tra `Action` e `Func`?', 'What is the difference between `Action` and `Func`?'), [L('Action non restituisce nulla, Func restituisce un valore', 'Action returns nothing, Func returns a value'), L('Func è più veloce', 'Func is faster'), L('Action accetta solo stringhe', 'Action only takes strings'), L('Nessuna', 'None')], 0, L('Action è come un metodo void.', 'Action is like a void method.')),
            Q('d3', L('Come ci si iscrive a un evento?', 'How do you subscribe to an event?'), ['evento = gestore', 'evento.Add(gestore)', 'evento += gestore', 'subscribe(evento, gestore)'], 2, L('E con `-=` ci si disiscrive.', 'And `-=` unsubscribes.')),
        ], task('csharp', L('Dichiara `Func<double, double> ivato = p => p * 1.22;`.', 'Declare `Func<double, double> ivato = p => p * 1.22;`.'), '', [re('Func\s*<\s*double\s*,\s*double\s*>\s+ivato\s*=\s*\(?\s*(\w+)\s*\)?\s*=>\s*\1\s*\*\s*1\.22\s*;', L('Il delegato è giusto', 'The delegate is right'))], '', 'Func<double, double> ivato = p => p * 1.22;'), [
            dMatch(pMatch(), [['Func<T, R>', L('Funzione con risultato', 'Function with a result')], ['Action<T>', L('Funzione senza risultato', 'Function without a result')], ['Predicate<T>', L('Test vero o falso', 'True or false test')], ['event', L('Notifica chi si iscrive', 'Notifies subscribers')], ['?.Invoke', L('Lancia se qualcuno ascolta', 'Raises if anyone listens')]], L('Metodi come valori.', 'Methods as values.')),
            dBug(pBug(), ['public event EventHandler? Cliccato;', 'public void Clic()', '{', '    Cliccato.Invoke(this, EventArgs.Empty);', '}'], 3, '    Cliccato?.Invoke(this, EventArgs.Empty);', L('Se nessuno si è iscritto l’evento è null: senza `?.` arriva una NullReferenceException.', 'If nobody subscribed the event is null: without `?.` you get a NullReferenceException.')),
        ]),
        lesson('linq-advanced', L('LINQ avanzato: raggruppare e unire', 'Advanced LINQ: grouping and joining'), [
            P('`GroupBy` crea gruppi con una chiave (`g.Key`) su cui calcolare somme e conteggi; `Join` unisce due sequenze su una chiave comune, come in SQL. `Skip` e `Take` fanno la paginazione, `ToDictionary` crea una mappa, `MaxBy` trova l’elemento con il valore più alto.', '`GroupBy` creates groups with a key (`g.Key`) on which to compute sums and counts; `Join` combines two sequences on a common key, as in SQL. `Skip` and `Take` do paging, `ToDictionary` builds a map, `MaxBy` finds the item with the highest value.'),
            C('var perCategoria = prodotti
    .GroupBy(p => p.Categoria)
    .Select(g => new { Categoria = g.Key, Totale = g.Sum(p => p.Prezzo), Quanti = g.Count() })
    .OrderByDescending(x => x.Totale);

var righe = ordini.Join(clienti,
    o => o.ClienteId, c => c.Id,
    (o, c) => $"{c.Nome}: {o.Totale}");

var pagina = prodotti.OrderBy(p => p.Nome).Skip(20).Take(10);
var perId = prodotti.ToDictionary(p => p.Id);
bool ciSonoGratis = prodotti.Any(p => p.Prezzo == 0);
var piuCaro = prodotti.MaxBy(p => p.Prezzo);
var gruppiDaTre = prodotti.Chunk(3);', 'csharp'),
            P('Attenzione all’**esecuzione differita**: una query LINQ non gira finché non la scorri o la materializzi con `ToList`, `Count`, `First`… Se la scorri due volte, gira due volte.', 'Watch out for **deferred execution**: a LINQ query does not run until you iterate it or materialise it with `ToList`, `Count`, `First`… If you iterate it twice, it runs twice.'),
        ], [
            Q('la1', L('Che cosa contiene `g.Key` dentro un `GroupBy`?', 'What does `g.Key` hold inside a `GroupBy`?'), [L('Il primo elemento del gruppo', 'The group’s first item'), L('Il valore della chiave di raggruppamento', 'The value of the grouping key'), L('Il numero di elementi', 'The number of items'), L('L’indice del gruppo', 'The group’s index')], 1, L('Il gruppo stesso si scorre come una sequenza.', 'The group itself is iterated like a sequence.')),
            Q('la2', L('Che cosa fanno `Skip(20).Take(10)`?', 'What do `Skip(20).Take(10)` do?'), [L('Saltano 20 elementi e ne prendono 10', 'Skip 20 items and take 10'), L('Prendono 20 elementi e ne saltano 10', 'Take 20 items and skip 10'), L('Ripetono la query 10 volte', 'Repeat the query 10 times'), L('Ordinano gli elementi dal 20 al 30', 'Sort items 20 to 30')], 0, L('La terza pagina da 10 elementi.', 'The third page of 10 items.')),
            Q('la3', L('Quando viene eseguita una query LINQ su una lista?', 'When does a LINQ query on a list run?'), [L('Quando viene scritta', 'When it is written'), L('Solo al riavvio dell’app', 'Only when the app restarts'), L('Quando la scorri o la materializzi, ad esempio con ToList', 'When you iterate or materialise it, for example with ToList'), L('Mai, LINQ prepara solo SQL', 'Never, LINQ only prepares SQL')], 2, L('È l’esecuzione differita.', 'That is deferred execution.')),
        ], task('csharp', L('Raggruppa `voti` (elementi `Voto` con `Materia` e `Valore`) per materia con `GroupBy` e calcola in `medie` la media di ogni gruppo con `Average`.', 'Group `voti` (`Voto` items with `Materia` and `Valore`) by subject with `GroupBy` and compute in `medie` each group’s average with `Average`.'), 'var voti = CaricaVoti();
', [re('medie\s*=\s*voti\s*\.GroupBy\s*\(\s*(\w+)\s*=>\s*\1\.Materia\s*\)', L('Raggruppi per materia', 'You group by subject')), re('\.Average\s*\(\s*(\w+)\s*=>\s*\1\.Valore\s*\)', L('Calcoli la media', 'You compute the average'))], '', 'var medie = voti.GroupBy(v => v.Materia)
    .Select(g => new { Materia = g.Key, Media = g.Average(v => v.Valore) });'), [
            dMatch(pMatch(), [['GroupBy', 'GROUP BY'], ['Join', 'JOIN'], ['Skip/Take', 'OFFSET/LIMIT'], ['Any', 'EXISTS'], ['Distinct', 'DISTINCT']], L('LINQ e SQL a confronto.', 'LINQ and SQL compared.')),
            dBlanks(pFill(), 'var conteggi = ordini
    .___(o => o.Citta)
    .Select(g => new { Citta = g.___, N = g.___() })
    .ToList();', ['GroupBy', 'Key', 'Count'], ['OrderBy', 'Id', 'Length'], L('Raggruppa, leggi la chiave, conta.', 'Group, read the key, count.')),
        ]),
        lesson('ef-core', L('Database con Entity Framework Core', 'Databases with Entity Framework Core'), [
            P('**EF Core** è l’ORM di .NET: le classi diventano tabelle e le query LINQ diventano SQL, con i parametri sempre separati (niente SQL injection). Un `DbContext` rappresenta la sessione con il database, ogni `DbSet<T>` una tabella.', '**EF Core** is .NET’s ORM: classes become tables and LINQ queries become SQL, with parameters always kept apart (no SQL injection). A `DbContext` represents the session with the database, each `DbSet<T>` a table.'),
            C('public class Corso
{
    public int Id { get; set; }
    public required string Titolo { get; set; }
    public List<Lezione> Lezioni { get; set; } = [];
}

public class Lezione
{
    public int Id { get; set; }
    public required string Nome { get; set; }
    public int CorsoId { get; set; }
}

public class FucinaDb(DbContextOptions<FucinaDb> opzioni) : DbContext(opzioni)
{
    public DbSet<Corso> Corsi => Set<Corso>();
}', 'csharp'),
            C('db.Corsi.Add(new Corso { Titolo = "C# intermedio" });
await db.SaveChangesAsync();

var conLezioni = await db.Corsi
    .Where(c => c.Titolo.StartsWith("C#"))
    .Include(c => c.Lezioni)
    .AsNoTracking()
    .ToListAsync();', 'csharp'),
            P('Le **migrazioni** tengono la struttura del database allineata alle classi: `dotnet ef migrations add Iniziale` e `dotnet ef database update`. `Include` carica le righe collegate, `AsNoTracking` velocizza le letture che non devi modificare.', '**Migrations** keep the database structure in step with the classes: `dotnet ef migrations add Iniziale` and `dotnet ef database update`. `Include` loads related rows, `AsNoTracking` speeds up reads you will not change.'),
        ], [
            Q('ef1', L('Che cosa fa EF Core con una query LINQ su un DbSet?', 'What does EF Core do with a LINQ query on a DbSet?'), [L('La esegue in memoria su tutte le righe', 'Runs it in memory over every row'), L('La traduce in SQL e la esegue nel database', 'Translates it to SQL and runs it in the database'), L('La salva in un file', 'Saves it to a file'), L('La manda a un servizio esterno', 'Sends it to an external service')], 1, L('Arrivano solo le righe che servono.', 'Only the rows you need come back.')),
            Q('ef2', L('A che cosa servono le migrazioni?', 'What are migrations for?'), [L('A spostare il database su un altro server', 'Moving the database to another server'), L('A fare il backup dei dati', 'Backing up the data'), L('A tenere la struttura del database allineata alle classi', 'Keeping the database structure in step with the classes'), L('A cifrare le colonne', 'Encrypting columns')], 2, L('Ogni migrazione è un passo che si può applicare o annullare.', 'Each migration is a step you can apply or undo.')),
            Q('ef3', L('Che cosa fa `SaveChangesAsync()`?', 'What does `SaveChangesAsync()` do?'), [L('Scrive nel database le modifiche tracciate', 'Writes the tracked changes to the database'), L('Ricarica tutte le tabelle', 'Reloads every table'), L('Annulla le modifiche', 'Undoes the changes'), L('Crea una migrazione', 'Creates a migration')], 0, L('Tutte insieme, in una transazione.', 'All together, in one transaction.')),
        ], task('csharp', L('Aggiungi `new Corso { Titolo = "Go" }` a `db.Corsi` e salva con `await db.SaveChangesAsync();`.', 'Add `new Corso { Titolo = "Go" }` to `db.Corsi` and save with `await db.SaveChangesAsync();`.'), '', [re('db\.Corsi\.Add\s*\(\s*new\s+Corso\s*\{\s*Titolo\s*=\s*"Go"\s*\}\s*\)', L('Aggiungi il corso', 'You add the course')), re('await\s+db\.SaveChangesAsync\s*\(\s*\)', L('Salvi', 'You save'))], '', 'db.Corsi.Add(new Corso { Titolo = "Go" });
await db.SaveChangesAsync();'), [
            dOrder(pSort(), ['dotnet add package Microsoft.EntityFrameworkCore.Design', 'dotnet ef migrations add Iniziale', 'dotnet ef database update'], L('Strumenti, migrazione, aggiornamento del database.', 'Tools, migration, database update.')),
            dMatch(pMatch(), [['DbContext', L('La sessione con il database', 'The database session')], ['DbSet<T>', L('Una tabella', 'A table')], ['Include', L('Carica i dati collegati', 'Loads related data')], ['AsNoTracking', L('Lettura senza tracciamento', 'Read without tracking')], ['SaveChangesAsync', L('Salva le modifiche', 'Saves changes')]], L('Le parti di EF Core.', 'The parts of EF Core.')),
        ]),
        lesson('dependency-injection', L('Iniezione delle dipendenze e configurazione', 'Dependency injection and configuration'), [
            P('Con l’**iniezione delle dipendenze** le classi chiedono ciò che serve nel costruttore e il contenitore di ASP.NET Core lo fornisce. Si registra un’interfaccia con la sua implementazione e una **durata**: `Singleton` (una per tutta l’app), `Scoped` (una per richiesta), `Transient` (nuova ogni volta).', 'With **dependency injection** classes ask for what they need in the constructor and the ASP.NET Core container provides it. You register an interface with its implementation and a **lifetime**: `Singleton` (one for the whole app), `Scoped` (one per request), `Transient` (new every time).'),
            C('var builder = WebApplication.CreateBuilder(args);

builder.Services.AddSingleton<IOrologio, OrologioDiSistema>();
builder.Services.AddScoped<ICatalogo, Catalogo>();
builder.Services.AddDbContext<FucinaDb>(o =>
    o.UseSqlite(builder.Configuration.GetConnectionString("Fucina")));

var app = builder.Build();
app.MapGet("/api/corsi", (ICatalogo catalogo) => catalogo.ElencoAsync());
app.Run();

public interface IOrologio { DateTime Adesso { get; } }
public class OrologioDiSistema : IOrologio { public DateTime Adesso => DateTime.UtcNow; }

public interface ICatalogo { Task<List<string>> ElencoAsync(); }
public class Catalogo(FucinaDb db) : ICatalogo
{
    public Task<List<string>> ElencoAsync() => db.Corsi.Select(c => c.Titolo).ToListAsync();
}', 'csharp'),
            P('Registrare un’interfaccia permette di sostituire l’implementazione, ad esempio con un orologio finto nei test. La configurazione arriva da `appsettings.json`, variabili d’ambiente e, in sviluppo, dagli **user secrets**, che restano fuori dal repository.', 'Registering an interface lets you swap the implementation, for example with a fake clock in tests. Configuration comes from `appsettings.json`, environment variables and, in development, **user secrets**, which stay out of the repository.'),
        ], [
            Q('di1', L('Quale durata crea un oggetto per ogni richiesta HTTP?', 'Which lifetime creates one object per HTTP request?'), ['Singleton', 'Transient', 'Scoped', 'Static'], 2, L('Il DbContext di solito è Scoped.', 'The DbContext is usually Scoped.')),
            Q('di2', L('Perché registrare un’interfaccia invece della classe concreta?', 'Why register an interface instead of the concrete class?'), [L('È obbligatorio in C#', 'It is required in C#'), L('Si può sostituire l’implementazione, ad esempio nei test', 'You can swap the implementation, for example in tests'), L('Rende il codice più veloce', 'It makes code faster'), L('Evita di scrivere il costruttore', 'It avoids writing the constructor')], 1, L('Il codice dipende dal contratto, non dai dettagli.', 'Code depends on the contract, not the details.')),
            Q('di3', L('Dove si mettono le password in sviluppo, senza finire nel repository?', 'Where do passwords go in development, without ending up in the repository?'), [L('In Program.cs', 'In Program.cs'), L('In un appsettings.json pubblicato', 'In a published appsettings.json'), L('In un commento nel codice', 'In a code comment'), L('Negli user secrets o nelle variabili d’ambiente', 'In user secrets or environment variables')], 3, L('Si gestiscono con `dotnet user-secrets`.', 'You manage them with `dotnet user-secrets`.')),
        ], task('csharp', L('Registra `IMailer` con l’implementazione `SmtpMailer` come servizio `Scoped`.', 'Register `IMailer` with the `SmtpMailer` implementation as a `Scoped` service.'), 'var builder = WebApplication.CreateBuilder(args);
', [re('builder\.Services\.AddScoped\s*<\s*IMailer\s*,\s*SmtpMailer\s*>\s*\(\s*\)\s*;', L('Il servizio è registrato', 'The service is registered'))], '', 'builder.Services.AddScoped<IMailer, SmtpMailer>();'), [
            dMatch(pMatch(), [['AddSingleton', L('Una per tutta l’app', 'One for the whole app')], ['AddScoped', L('Una per richiesta', 'One per request')], ['AddTransient', L('Nuova ogni volta', 'New every time')], ['appsettings.json', L('Configurazione', 'Configuration')], ['user secrets', L('Segreti in sviluppo', 'Development secrets')]], L('Durate e configurazione.', 'Lifetimes and configuration.')),
            dFlash(pFlash(), [[L('Un servizio Scoped si può iniettare senza problemi in un Singleton', 'A Scoped service can safely be injected into a Singleton'), false], [L('Il contenitore passa le dipendenze nel costruttore', 'The container passes dependencies through the constructor'), true], [L('Le variabili d’ambiente possono sovrascrivere appsettings.json', 'Environment variables can override appsettings.json'), true], [L('Con la DI non si possono più scrivere test', 'With DI you can no longer write tests'), false]], L('Un Singleton vivrebbe più a lungo della richiesta.', 'A Singleton would outlive the request.')),
        ]),
        lesson('async-advanced', L('Async avanzato: WhenAll, annullamento e flussi', 'Advanced async: WhenAll, cancellation and streams'), [
            P('`Task.WhenAll` avvia più operazioni insieme e aspetta che finiscano tutte: due download da un secondo durano circa un secondo. Un **CancellationToken** si passa lungo le chiamate per poterle annullare, per un timeout o perché l’utente ha chiuso la pagina.', '`Task.WhenAll` starts several operations together and waits for all of them: two one-second downloads take about one second. A **CancellationToken** is passed along the calls so they can be cancelled, for a timeout or because the user closed the page.'),
            C('using var cts = new CancellationTokenSource(TimeSpan.FromSeconds(5));
using var http = new HttpClient();

Task<string> a = http.GetStringAsync("https://example.com/a", cts.Token);
Task<string> b = http.GetStringAsync("https://example.com/b", cts.Token);
string[] pagine = await Task.WhenAll(a, b);

await foreach (var riga in LeggiRighe("log.txt", cts.Token))
    Console.WriteLine(riga);

static async IAsyncEnumerable<string> LeggiRighe(string file,
    [EnumeratorCancellation] CancellationToken token = default)
{
    using var lettore = new StreamReader(file);
    while (await lettore.ReadLineAsync(token) is { } riga)
        yield return riga;
}', 'csharp'),
            P('`IAsyncEnumerable` con `await foreach` produce valori uno alla volta, mentre arrivano. In un’app vera non creare un `HttpClient` nuovo a ogni richiesta: usa `IHttpClientFactory` (con `AddHttpClient`) o un’istanza condivisa.', '`IAsyncEnumerable` with `await foreach` produces values one at a time, as they arrive. In a real app do not create a new `HttpClient` for every request: use `IHttpClientFactory` (with `AddHttpClient`) or a shared instance.'),
        ], [
            Q('aa1', L('Che cosa fa `await Task.WhenAll(a, b)`?', 'What does `await Task.WhenAll(a, b)` do?'), [L('Esegue a e poi b, uno dopo l’altro', 'Runs a and then b, one after the other'), L('Aspetta che finiscano entrambi, eseguiti insieme', 'Waits for both to finish, run together'), L('Aspetta solo il primo che finisce', 'Waits only for the first to finish'), L('Annulla entrambi', 'Cancels both')], 1, L('Per il primo che finisce c’è `Task.WhenAny`.', 'For the first to finish there is `Task.WhenAny`.')),
            Q('aa2', L('A che cosa serve un `CancellationToken`?', 'What is a `CancellationToken` for?'), [L('A segnalare che un’operazione va annullata', 'Signalling that an operation should be cancelled'), L('A cifrare la richiesta', 'Encrypting the request'), L('A contare i Task', 'Counting Tasks'), L('A misurare il tempo', 'Measuring time')], 0, L('Il metodo che lo riceve decide quando fermarsi.', 'The method receiving it decides when to stop.')),
            Q('aa3', L('Come si scorre un `IAsyncEnumerable<T>`?', 'How do you iterate an `IAsyncEnumerable<T>`?'), ['foreach async', 'for await', 'await foreach', 'Parallel.ForEach'], 2, L('Ogni giro può attendere il valore successivo.', 'Each round can await the next value.')),
        ], task('csharp', L('Avvia `CaricaUtentiAsync()` e `CaricaOrdiniAsync()` insieme e aspettali con `await Task.WhenAll(...)`.', 'Start `CaricaUtentiAsync()` and `CaricaOrdiniAsync()` together and await them with `await Task.WhenAll(...)`.'), '', [re('await\s+Task\.WhenAll\s*\(', L('Aspetti con WhenAll', 'You await with WhenAll')), re('CaricaUtentiAsync\s*\(\s*\)', L('Carichi gli utenti', 'You load the users')), re('CaricaOrdiniAsync\s*\(\s*\)', L('Carichi gli ordini', 'You load the orders'))], '', 'var utenti = CaricaUtentiAsync();
var ordini = CaricaOrdiniAsync();
await Task.WhenAll(utenti, ordini);'), [
            dBug(pBug(), ['foreach (var url in urls)', '{', '    var pagina = http.GetStringAsync(url).Result;', '    Console.WriteLine(pagina.Length);', '}'], 2, '    var pagina = await http.GetStringAsync(url);', L('`.Result` blocca il thread e può causare deadlock: usa `await`.', '`.Result` blocks the thread and can cause deadlocks: use `await`.')),
            dMatch(pMatch(), [['Task.WhenAll', L('Aspetta tutti', 'Waits for all')], ['Task.WhenAny', L('Aspetta il primo', 'Waits for the first')], ['CancellationToken', L('Annullamento', 'Cancellation')], ['IAsyncEnumerable', L('Flusso asincrono', 'Async stream')], ['ConfigureAwait(false)', L('Non torna al contesto originale', 'Does not resume on the original context')]], L('Strumenti dell’asincronia.', 'Async tools.')),
        ]),
        lesson('xunit', L('Test con xUnit', 'Testing with xUnit'), [
            P('In **xUnit** un `[Fact]` è un test senza parametri, una `[Theory]` lo stesso test ripetuto con dati diversi (`[InlineData]`). `Assert.Equal` vuole prima il valore atteso, poi quello ottenuto. NUnit e MSTest sono alternative con idee simili.', 'In **xUnit** a `[Fact]` is a test with no parameters, a `[Theory]` the same test repeated with different data (`[InlineData]`). `Assert.Equal` wants the expected value first, then the actual one. NUnit and MSTest are alternatives with similar ideas.'),
            C('using Xunit;

public class CarrelloTest
{
    [Fact]
    public void TotaleConDuePezzi()
    {
        var c = new Carrello();
        c.Aggiungi(10m);
        c.Aggiungi(5m);
        Assert.Equal(15m, c.Totale);
    }

    [Theory]
    [InlineData(100, 10, 90)]
    [InlineData(50, 0, 50)]
    public void Sconto(int prezzo, int percento, int atteso)
    {
        Assert.Equal(atteso, Prezzi.Sconta(prezzo, percento));
    }

    [Fact]
    public void ImportoNegativo() =>
        Assert.Throws<ArgumentException>(() => new Carrello().Aggiungi(-1m));
}', 'csharp'),
            C('dotnet new xunit -o Negozio.Tests
dotnet test', 'bash'),
        ], [
            Q('xu1', L('Che differenza c’è tra `[Fact]` e `[Theory]`?', 'What is the difference between `[Fact]` and `[Theory]`?'), [L('Theory ripete il test con dati diversi', 'Theory repeats the test with different data'), L('Fact è più lento', 'Fact is slower'), L('Theory non usa Assert', 'Theory does not use Assert'), L('Nessuna', 'None')], 0, L('I dati arrivano da `[InlineData]`.', 'The data comes from `[InlineData]`.')),
            Q('xu2', L('Come verifichi che venga lanciata un’eccezione?', 'How do you check an exception is thrown?'), ['Assert.Error', 'Assert.Catch', 'Expect.Exception', 'Assert.Throws<T>(...)'], 3, L('Riceve una lambda con il codice da provare.', 'It takes a lambda with the code to try.')),
            Q('xu3', L('Quale comando esegue i test?', 'Which command runs the tests?'), ['dotnet run --tests', 'dotnet test', 'dotnet check', 'dotnet xunit'], 1, L('Trova i progetti di test e li esegue.', 'It finds the test projects and runs them.')),
        ], task('csharp', L('Scrivi un `[Fact]` chiamato `SommaDiDue` che verifica `Assert.Equal(4, Calcoli.Somma(2, 2));`.', 'Write a `[Fact]` named `SommaDiDue` that checks `Assert.Equal(4, Calcoli.Somma(2, 2));`.'), 'public class CalcoliTest
{
}', [re('\[\s*Fact\s*\]\s*public\s+void\s+SommaDiDue\s*\(\s*\)', L('Il test esiste', 'The test exists')), re('Assert\.Equal\s*\(\s*4\s*,\s*Calcoli\.Somma\s*\(\s*2\s*,\s*2\s*\)\s*\)', L('L’asserzione è giusta', 'The assertion is right'))], '', 'public class CalcoliTest
{
    [Fact]
    public void SommaDiDue()
    {
        Assert.Equal(4, Calcoli.Somma(2, 2));
    }
}'), [
            dBlanks(pFill(), '[___]
[InlineData(2, 4)]
[InlineData(3, 6)]
public void Doppio(int n, int atteso)
{
    Assert.___(atteso, Calcoli.Doppio(n));
}', ['Theory', 'Equal'], ['Fact', 'Same', 'Test'], L('Test con dati e confronto di valori.', 'Data-driven test and value comparison.')),
            dMatch(pMatch(), [['[Fact]', L('Test semplice', 'Simple test')], ['[Theory]', L('Test con più dati', 'Test with several data sets')], ['[InlineData]', L('Una riga di dati', 'One row of data')], ['Assert.Equal', L('Atteso e ottenuto', 'Expected and actual')], ['dotnet test', L('Esegue i test', 'Runs the tests')]], L('Gli attrezzi di xUnit.', 'The xUnit tools.')),
        ]),
        lesson('modern-csharp', L('C# moderno: collection expression e C# 14', 'Modern C#: collection expressions and C# 14'), [
            P('C# si aggiorna ogni anno insieme a .NET. Le **collection expression** (C# 12) creano liste e array con la stessa sintassi, `[1, 2, 3]`, e `..` espande un’altra sequenza. **C# 14**, arrivato con .NET 10, aggiunge i **membri di estensione**, la parola chiave `field` e l’assegnazione condizionale `a?.b = c`.', 'C# is updated every year together with .NET. **Collection expressions** (C# 12) create lists and arrays with the same syntax, `[1, 2, 3]`, and `..` spreads another sequence. **C# 14**, which came with .NET 10, adds **extension members**, the `field` keyword and null-conditional assignment `a?.b = c`.'),
            C('List<int> numeri = [1, 2, 3];
int[] altri = [..numeri, 4, 5];               // [1, 2, 3, 4, 5]

Console.WriteLine("ciao fucina".Titolo());     // Ciao fucina
Console.WriteLine(numeri.Media);               // 2

Cliente? cliente = TrovaCliente(7);
cliente?.Indirizzo = "Via Roma 1";             // C# 14: assegna solo se cliente non è null

public static class Estensioni
{
    extension(string s)                        // C# 14: membri di estensione
    {
        public string Titolo() => s.Length == 0 ? s : char.ToUpper(s[0]) + s[1..];
    }

    extension(List<int> lista)
    {
        public double Media => lista.Count == 0 ? 0 : lista.Average();
    }
}

public class Prodotto
{
    public decimal Prezzo
    {
        get;
        set => field = value >= 0 ? value : throw new ArgumentOutOfRangeException(nameof(value));
    }
}', 'csharp'),
            P('Un blocco `extension` aggiunge metodi e anche **proprietà** a un tipo esistente senza modificarlo. `field` indica il campo nascosto che il compilatore crea per la proprietà: puoi validare il valore senza dichiararlo a mano.', 'An `extension` block adds methods and even **properties** to an existing type without changing it. `field` refers to the hidden backing field the compiler creates for the property: you can validate the value without declaring it by hand.'),
        ], [
            Q('mc1', L('Che cosa crea `int[] tutti = [..a, ..b];`?', 'What does `int[] tutti = [..a, ..b];` create?'), [L('Un array con gli elementi di a seguiti da quelli di b', 'An array with a’s items followed by b’s'), L('Un array di due array', 'An array of two arrays'), L('Un intervallo da a a b', 'A range from a to b'), L('Un errore di sintassi', 'A syntax error')], 0, L('`..` espande una sequenza dentro un’altra.', '`..` spreads one sequence into another.')),
            Q('mc2', L('A che cosa serve `field` in un accessor di proprietà (C# 14)?', 'What is `field` for in a property accessor (C# 14)?'), [L('Dichiara un nuovo campo pubblico', 'Declares a new public field'), L('Indica il campo nascosto creato dal compilatore per la proprietà', 'Refers to the hidden field the compiler creates for the property'), L('Rende la proprietà statica', 'Makes the property static'), L('Legge un valore dal database', 'Reads a value from the database')], 1, L('Niente più campo privato scritto a mano.', 'No more hand-written private field.')),
            Q('mc3', L('Che cosa permette un blocco `extension(string s)`?', 'What does an `extension(string s)` block allow?'), [L('Ereditare dalla classe string', 'Inheriting from the string class'), L('Modificare il codice sorgente di .NET', 'Changing .NET’s source code'), L('Aggiungere metodi e proprietà al tipo string senza modificarlo', 'Adding methods and properties to string without changing it'), L('Creare una sottoclasse anonima', 'Creating an anonymous subclass')], 2, L('Si usano come se fossero del tipo stesso.', 'They are used as if they belonged to the type itself.')),
        ], task('csharp', L('Crea con una collection expression la lista `List<string> colori` con `"rosso"` e `"blu"`.', 'Use a collection expression to create the list `List<string> colori` with `"rosso"` and `"blu"`.'), '', [re('List\s*<\s*string\s*>\s+colori\s*=\s*\[\s*"rosso"\s*,\s*"blu"\s*\]\s*;', L('La lista è creata', 'The list is created'))], '', 'List<string> colori = ["rosso", "blu"];'), [
            dMatch(pMatch(), [['[1, 2, 3]', L('Collection expression', 'Collection expression')], ['..', L('Espande una sequenza', 'Spreads a sequence')], ['extension', L('Membri aggiunti a un tipo', 'Members added to a type')], ['field', L('Campo nascosto della proprietà', 'Hidden backing field')], ['a?.b = c', L('Assegna solo se a non è null', 'Assigns only if a is not null')]], L('Novità recenti di C#.', 'Recent C# additions.')),
            dFlash(pFlash(), [[L('Le collection expression funzionano sia con liste sia con array', 'Collection expressions work with both lists and arrays'), true], [L('I membri di estensione di C# 14 possono essere anche proprietà', 'C# 14 extension members can be properties too'), true], [L('`field` esiste da C# 1', '`field` has existed since C# 1'), false], [L('Le estensioni modificano il tipo originale nella libreria', 'Extensions change the original type in the library'), false]], L('Meno codice ripetitivo, stesso significato.', 'Less boilerplate, same meaning.')),
        ]),
    ],
];
