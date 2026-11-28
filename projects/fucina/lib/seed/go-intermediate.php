<?php
/** Go intermediate: generics, advanced errors, embedding, select and context, mutexes, io and JSON, iterators, benchmarks and fuzzing. */

return [
    'id' => 'go-intermediate', 'family' => 'go', 'after' => 'go-base',
    'title' => L('Go intermedio', 'Intermediate Go'), 'short' => L('Go intermedio', 'Go Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Go per progetti veri: generics, errori avvolti e personalizzati, composizione, select e context, mutex, io.Reader e JSON, iteratori, benchmark e fuzzing.', 'Go for real projects: generics, wrapped and custom errors, composition, select and context, mutexes, io.Reader and JSON, iterators, benchmarks and fuzzing.'),
    'lessons' => [
        lesson('generics', L('Generics', 'Generics'), [
            P('Dai **generics** (Go 1.18) una funzione o un tipo può lavorare con più tipi: `[T cmp.Ordered]` è il **parametro di tipo** con il suo **vincolo**. `any` accetta tutto, `comparable` i tipi confrontabili con `==`, un’interfaccia con `|` elenca i tipi ammessi e `~int` include anche i tipi definiti sopra `int`.', 'With **generics** (Go 1.18) a function or a type can work with several types: `[T cmp.Ordered]` is the **type parameter** with its **constraint**. `any` accepts everything, `comparable` the types comparable with `==`, an interface with `|` lists the allowed types and `~int` also includes types defined on top of `int`.'),
            C('func Massimo[T cmp.Ordered](valori []T) T {
    m := valori[0]
    for _, v := range valori[1:] {
        if v > m {
            m = v
        }
    }
    return m
}

type Numero interface {
    ~int | ~int64 | ~float64
}

func Somma[T Numero](valori ...T) T {
    var tot T
    for _, v := range valori {
        tot += v
    }
    return tot
}

type Pila[T any] struct{ elementi []T }

func (p *Pila[T]) Metti(v T) { p.elementi = append(p.elementi, v) }

fmt.Println(Massimo([]string{"kiwi", "mela"}), Somma(1.5, 2.5))   // mela 4', 'go'),
            P('Usa i generics quando lo stesso codice si ripete identico per tipi diversi (contenitori, funzioni su slice). Per comportamenti diversi a seconda del tipo restano più adatte le interfacce.', 'Use generics when the same code repeats identically for different types (containers, functions on slices). For behaviour that differs by type, interfaces remain the better fit.'),
        ], [
            Q('g1', L('Che cosa significa `[T cmp.Ordered]`?', 'What does `[T cmp.Ordered]` mean?'), [L('T è un tipo che si può confrontare con < e >', 'T is a type that can be compared with < and >'), L('T è un tipo ordinato alfabeticamente', 'T is an alphabetically sorted type'), L('T deve essere una slice', 'T must be a slice'), L('T è una costante', 'T is a constant')], 0, L('Numeri e stringhe soddisfano il vincolo.', 'Numbers and strings satisfy the constraint.')),
            Q('g2', L('Quale vincolo accetta qualsiasi tipo?', 'Which constraint accepts any type?'), ['comparable', 'any', 'interface{ int }', 'cmp.Ordered'], 1, L('`any` è un altro nome di `interface{}`.', '`any` is another name for `interface{}`.')),
            Q('g3', L('Che cosa include `~int` in un vincolo?', 'What does `~int` include in a constraint?'), [L('Solo int', 'Only int'), L('Tutti i numeri', 'All numbers'), L('int e i tipi definiti sopra int, come type Euro int', 'int and types defined on int, such as type Euro int'), L('Gli int negativi', 'Negative ints')], 2, L('La tilde guarda al tipo sottostante.', 'The tilde looks at the underlying type.')),
        ], task('go', L('Scrivi `func Primo[T any](s []T) T` che restituisce `s[0]`.', 'Write `func Primo[T any](s []T) T` returning `s[0]`.'), '', [re('func\s+Primo\s*\[\s*T\s+any\s*\]\s*\(\s*s\s+\[\]T\s*\)\s*T\s*\{', L('La firma è generica', 'The signature is generic')), re('return\s+s\[\s*0\s*\]', L('Restituisci il primo elemento', 'You return the first item'))], '', "func Primo[T any](s []T) T {\n    return s[0]\n}"), [
            dBlanks(pFill(), 'func Filtra[T ___](s []T, ok func(T) bool) []T {
    var out []T
    for _, v := ___ s {
        if ok(v) {
            out = ___(out, v)
        }
    }
    return out
}', ['any', 'range', 'append'], ['all', 'in', 'push'], L('Vincolo, ciclo, aggiunta.', 'Constraint, loop, append.')),
            dMatch(pMatch(), [['any', L('Qualsiasi tipo', 'Any type')], ['comparable', L('Confrontabile con ==', 'Comparable with ==')], ['cmp.Ordered', L('Ordinabile con < e >', 'Orderable with < and >')], ['~int', L('int e tipi derivati', 'int and derived types')], ['[T any]', L('Parametro di tipo', 'Type parameter')]], L('Vincoli dei generics.', 'Generic constraints.')),
        ]),
        lesson('errors-advanced', L('Errori: Is, As e tipi personalizzati', 'Errors: Is, As and custom types'), [
            P('Gli **errori sentinella** (`var ErrX = errors.New(...)`) si riconoscono con `errors.Is`, che guarda anche dentro gli errori avvolti con `%w`. Un **tipo errore** personalizzato porta più informazioni: basta un metodo `Error() string`, e `errors.As` lo ritrova nella catena.', '**Sentinel errors** (`var ErrX = errors.New(...)`) are recognised with `errors.Is`, which also looks inside errors wrapped with `%w`. A custom **error type** carries more information: a method `Error() string` is enough, and `errors.As` finds it in the chain.'),
            C('var ErrNonTrovato = errors.New("non trovato")

type ErroreValidazione struct {
    Campo string
}

func (e *ErroreValidazione) Error() string {
    return "campo non valido: " + e.Campo
}

func cerca(id int) error {
    if id <= 0 {
        return &ErroreValidazione{Campo: "id"}
    }
    return fmt.Errorf("utente %d: %w", id, ErrNonTrovato)
}

if errors.Is(cerca(7), ErrNonTrovato) {
    fmt.Println("404")
}
var ev *ErroreValidazione
if errors.As(cerca(0), &ev) {
    fmt.Println("Correggi", ev.Campo)
}', 'go'),
            P('Non confrontare i messaggi di testo: possono cambiare. `panic` è per i bug irrecuperabili (uno stato impossibile, un indice fuori dalla slice), non per gli errori normali; `recover` dentro un `defer` può fermarlo, ad esempio in un server che non deve cadere.', 'Do not compare message text: it can change. `panic` is for unrecoverable bugs (an impossible state, an index out of range), not for normal errors; `recover` inside a `defer` can stop it, for example in a server that must not go down.'),
        ], [
            Q('ea1', L('Perché usare `errors.Is` invece di `err == ErrNonTrovato`?', 'Why use `errors.Is` instead of `err == ErrNonTrovato`?'), [L('È più veloce', 'It is faster'), L('Trova l’errore anche quando è avvolto con %w', 'It finds the error even when wrapped with %w'), L('Funziona solo con i panic', 'It only works with panics'), L('Converte l’errore in stringa', 'It converts the error to a string')], 1, L('`==` vede solo l’errore più esterno.', '`==` only sees the outermost error.')),
            Q('ea2', L('Che cosa serve per creare un tipo errore?', 'What do you need to create an error type?'), [L('Ereditare da Exception', 'Inheriting from Exception'), L('La parola chiave errortype', 'The errortype keyword'), L('Un campo Message', 'A Message field'), L('Un metodo Error() string', 'An Error() string method')], 3, L('Così soddisfa l’interfaccia `error`.', 'That way it satisfies the `error` interface.')),
            Q('ea3', L('Quando usare `panic`?', 'When should you use `panic`?'), [L('Per ogni file che non si apre', 'For every file that will not open'), L('Per i dati sbagliati inseriti dall’utente', 'For wrong data entered by the user'), L('Per un bug irrecuperabile del programma', 'For an unrecoverable bug in the program'), L('Mai, in Go non esiste', 'Never, it does not exist in Go')], 2, L('Gli errori previsti si restituiscono come `error`.', 'Expected errors are returned as `error`.')),
        ], task('go', L('Definisci l’errore sentinella `ErrScaduto` con `errors.New("scaduto")` e controlla `err` con `if errors.Is(err, ErrScaduto)`.', 'Define the sentinel error `ErrScaduto` with `errors.New("scaduto")` and check `err` with `if errors.Is(err, ErrScaduto)`.'), '', [re('var\s+ErrScaduto\s*=\s*errors\.New\s*\(\s*"scaduto"\s*\)', L('Definisci l’errore', 'You define the error')), re('if\s+errors\.Is\s*\(\s*err\s*,\s*ErrScaduto\s*\)', L('Lo riconosci', 'You recognise it'))], '', "var ErrScaduto = errors.New(\"scaduto\")\n\nif errors.Is(err, ErrScaduto) {\n    fmt.Println(\"rinnova\")\n}"), [
            dMatch(pMatch(), [['errors.New', L('Crea un errore', 'Creates an error')], ['%w', L('Avvolge mantenendo l’originale', 'Wraps keeping the original')], ['errors.Is', L('È quell’errore?', 'Is it that error?')], ['errors.As', L('Ritrova un tipo di errore', 'Finds an error type')], ['recover', L('Ferma un panic', 'Stops a panic')]], L('Gli attrezzi degli errori.', 'The error tools.')),
            dBug(pBug(), ['if err != nil {', '    if err.Error() == "non trovato" {', '        return nil', '    }', '    return err', '}'], 1, '    if errors.Is(err, ErrNonTrovato) {', L('Confrontare il testo è fragile: usa `errors.Is` con l’errore sentinella.', 'Comparing text is fragile: use `errors.Is` with the sentinel error.')),
        ]),
        lesson('embedding', L('Composizione con l’embedding', 'Composition with embedding'), [
            P('Go non ha ereditarietà: si **compone**. Incorporando un tipo in una struct, cioè scrivendolo senza nome di campo, i suoi campi e metodi vengono **promossi**: `s.Scrivi` chiama `s.Registro.Scrivi`. Anche le interfacce si combinano incorporandone altre.', 'Go has no inheritance: it **composes**. When you embed a type in a struct, writing it without a field name, its fields and methods are **promoted**: `s.Scrivi` calls `s.Registro.Scrivi`. Interfaces combine the same way, by embedding others.'),
            C('type Registro struct{ righe []string }

func (r *Registro) Scrivi(s string) { r.righe = append(r.righe, s) }

type Servizio struct {
    *Registro            // incorporato: i suoi metodi diventano di Servizio
    Nome string
}

type Scrittore interface{ Scrivi(string) }

func main() {
    s := Servizio{Registro: &Registro{}, Nome: "pagamenti"}
    s.Scrivi("avviato")              // promosso da Registro
    var w Scrittore = s              // Servizio soddisfa Scrittore
    w.Scrivi("pronto")
    fmt.Println(len(s.righe))        // 2
}', 'go'),
            C('type ReadWriter interface {
    io.Reader
    io.Writer
}', 'go'),
            P('Le interfacce di Go sono piccole, spesso di un solo metodo (`io.Reader`, `fmt.Stringer`), e si uniscono quando serve. Il campo incorporato resta accessibile con il nome del suo tipo: `s.Registro`.', 'Go interfaces are small, often a single method (`io.Reader`, `fmt.Stringer`), and are combined when needed. The embedded field remains reachable through its type name: `s.Registro`.'),
        ], [
            Q('em1', L('Come si incorpora `Registro` in `Servizio`?', 'How do you embed `Registro` in `Servizio`?'), ['Servizio extends Registro', L('Con un campo senza nome di tipo Registro nella struct', 'With an unnamed field of type Registro in the struct'), 'implements Registro', 'import Registro'], 1, L('Basta scrivere il tipo da solo.', 'Writing the type on its own is enough.')),
            Q('em2', L('Che cosa significa che un metodo è “promosso”?', 'What does it mean that a method is “promoted”?'), [L('Diventa pubblico', 'It becomes public'), L('Viene eseguito per primo', 'It runs first'), L('Si può chiamare direttamente sulla struct esterna', 'It can be called directly on the outer struct'), L('Diventa statico', 'It becomes static')], 2, L('Senza scrivere il campo intermedio.', 'Without writing the intermediate field.')),
            Q('em3', L('Che cosa fa `interface { io.Reader; io.Writer }`?', 'What does `interface { io.Reader; io.Writer }` do?'), [L('Unisce i metodi delle due interfacce', 'Combines the methods of both interfaces'), L('Sceglie una delle due interfacce', 'Picks one of the two interfaces'), L('Crea due interfacce separate', 'Creates two separate interfaces'), L('Dà errore di compilazione', 'Causes a compile error')], 0, L('È esattamente `io.ReadWriter`.', 'It is exactly `io.ReadWriter`.')),
        ], task('go', L('Incorpora `Motore` nella struct `Auto` (campo senza nome) e aggiungi il campo `Modello string`.', 'Embed `Motore` in the `Auto` struct (unnamed field) and add the field `Modello string`.'), 'type Motore struct{ Cavalli int }

func (m Motore) Avvia() string { return "vroom" }
', [re('type\s+Auto\s+struct\s*\{[^}]*^\s*\*?Motore\s*$[^}]*\}', L('Motore è incorporato', 'Motore is embedded')), re('type\s+Auto\s+struct\s*\{[^}]*Modello\s+string[^}]*\}', L('C’è il modello', 'There is the model'))], '', "type Auto struct {\n    Motore\n    Modello string\n}"), [
            dFlash(pFlash(), [[L('Go supporta l’ereditarietà tra struct', 'Go supports inheritance between structs'), false], [L('I metodi del tipo incorporato si chiamano sulla struct esterna', 'The embedded type’s methods are called on the outer struct'), true], [L('Un’interfaccia può incorporare altre interfacce', 'An interface can embed other interfaces'), true], [L('Per incorporare un tipo serve la parola chiave embed', 'Embedding a type needs the embed keyword'), false]], L('Composizione al posto dell’ereditarietà.', 'Composition instead of inheritance.')),
            dBlanks(pFill(), 'type Animale struct{ Nome string }
func (a Animale) Saluta() string { return "Ciao, sono " + a.Nome }

type Cane struct {
    ___
    Razza string
}

c := Cane{Animale: Animale{Nome: "Fido"}, Razza: "beagle"}
fmt.Println(c.___())', ['Animale', 'Saluta'], ['extends Animale', 'super.Saluta'], L('Tipo incorporato e metodo promosso.', 'Embedded type and promoted method.')),
        ]),
        lesson('select-context', L('select, timeout e context', 'select, timeouts and context'), [
            P('`select` aspetta su più canali e prosegue con il primo pronto. Un **context** porta scadenze e annullamenti lungo tutta la catena di chiamate: si passa come **primo parametro** (`ctx context.Context`) e chi lavora controlla `ctx.Done()`.', '`select` waits on several channels and goes on with the first one ready. A **context** carries deadlines and cancellation along the whole call chain: it is passed as the **first parameter** (`ctx context.Context`) and whoever does the work watches `ctx.Done()`.'),
            C('func cerca(ctx context.Context, query string) (string, error) {
    risultato := make(chan string, 1)
    go func() { risultato <- interrogaDatabase(query) }()

    select {
    case r := <-risultato:
        return r, nil
    case <-ctx.Done():
        return "", ctx.Err()    // context.DeadlineExceeded o context.Canceled
    }
}

func main() {
    ctx, cancel := context.WithTimeout(context.Background(), 2*time.Second)
    defer cancel()
    r, err := cerca(ctx, "corsi")
    fmt.Println(r, err)
}', 'go'),
            P('`defer cancel()` libera sempre le risorse del context, anche se il lavoro finisce prima. Il canale con buffer `make(chan string, 1)` evita che la goroutine resti bloccata per sempre se nessuno legge più il risultato. In un server HTTP, `r.Context()` si annulla da solo quando il client chiude la connessione.', '`defer cancel()` always frees the context’s resources, even if the work ends early. The buffered channel `make(chan string, 1)` keeps the goroutine from blocking forever if nobody reads the result any more. In an HTTP server, `r.Context()` is cancelled by itself when the client closes the connection.'),
        ], [
            Q('sc1', L('Che cosa fa `select`?', 'What does `select` do?'), [L('Sceglie un elemento a caso da una slice', 'Picks a random item from a slice'), L('Aspetta su più canali e prosegue con il primo pronto', 'Waits on several channels and goes on with the first ready'), L('Filtra le righe di un database', 'Filters database rows'), L('Chiude tutti i canali', 'Closes every channel')], 1, L('Come uno switch per i canali.', 'Like a switch for channels.')),
            Q('sc2', L('Dove va il `context.Context` in una funzione?', 'Where does `context.Context` go in a function?'), [L('Come ultimo parametro', 'As the last parameter'), L('In una variabile globale', 'In a global variable'), L('Come primo parametro, di solito chiamato ctx', 'As the first parameter, usually called ctx'), L('Sempre dentro una struct', 'Always inside a struct')], 2, L('È una convenzione seguita da tutta la libreria standard.', 'It is a convention followed by the whole standard library.')),
            Q('sc3', L('Perché scrivere `defer cancel()`?', 'Why write `defer cancel()`?'), [L('Per rilasciare le risorse del context anche se si finisce prima', 'To release the context’s resources even when finishing early'), L('Per annullare subito la richiesta', 'To cancel the request at once'), L('Per riavviare il timer', 'To restart the timer'), L('Non serve a niente', 'It serves no purpose')], 0, L('`go vet` segnala un cancel dimenticato.', '`go vet` flags a forgotten cancel.')),
        ], task('go', L('Crea un context con timeout di 5 secondi da `context.Background()` e scrivi subito `defer cancel()`.', 'Create a context with a 5-second timeout from `context.Background()` and immediately write `defer cancel()`.'), '', [re('ctx\s*,\s*cancel\s*:=\s*context\.WithTimeout\s*\(\s*context\.Background\s*\(\s*\)\s*,\s*5\s*\*\s*time\.Second\s*\)', L('Crei il context', 'You create the context')), re('defer\s+cancel\s*\(\s*\)', L('Liberi le risorse', 'You free the resources'))], '', "ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)\ndefer cancel()"), [
            dOrder(pSort(), ['ctx, cancel := context.WithTimeout(context.Background(), time.Second)', 'defer cancel()', 'select {', 'case r := <-risultati:', '    fmt.Println(r)', 'case <-ctx.Done():', '    fmt.Println("tempo scaduto")', '}'], L('Context, pulizia, attesa sul primo evento.', 'Context, clean-up, wait for the first event.')),
            dMatch(pMatch(), [['select', L('Aspetta più canali', 'Waits on several channels')], ['ctx.Done()', L('Canale chiuso all’annullamento', 'Channel closed on cancel')], ['WithTimeout', L('Scadenza dopo un tempo', 'Deadline after a duration')], ['cancel()', L('Annulla e libera risorse', 'Cancels and frees resources')], ['time.After', L('Canale che scatta dopo un tempo', 'Channel that fires after a while')]], L('Attese controllate.', 'Controlled waiting.')),
        ]),
        lesson('sync', L('Mutex e race detector', 'Mutexes and the race detector'), [
            P('Quando più goroutine scrivono lo stesso dato serve un **mutex**: `Lock` lascia entrare una goroutine alla volta, `defer Unlock` la fa uscire anche in caso di errore. Le mappe di Go non sono sicure tra goroutine. Per contatori semplici c’è `sync/atomic`.', 'When several goroutines write the same data you need a **mutex**: `Lock` lets one goroutine in at a time, `defer Unlock` lets it out even when an error occurs. Go maps are not safe across goroutines. For simple counters there is `sync/atomic`.'),
            C('type Contatore struct {
    mu     sync.Mutex
    valori map[string]int
}

func (c *Contatore) Incrementa(chiave string) {
    c.mu.Lock()
    defer c.mu.Unlock()
    c.valori[chiave]++
}

func main() {
    c := Contatore{valori: map[string]int{}}
    var wg sync.WaitGroup
    for range 1000 {
        wg.Go(func() { c.Incrementa("visite") })   // Go 1.25+
    }
    wg.Wait()
    fmt.Println(c.valori["visite"])   // 1000
}', 'go'),
            C('go run -race main.go
go test -race ./...', 'bash'),
            P('Il flag `-race` attiva il **race detector**, che segnala gli accessi concorrenti non protetti mentre il programma gira: lancialo almeno nei test. `wg.Go` (Go 1.25) avvia la goroutine e la conta, al posto della coppia `Add` e `Done`.', 'The `-race` flag turns on the **race detector**, which reports unprotected concurrent access while the program runs: use it at least in tests. `wg.Go` (Go 1.25) starts the goroutine and tracks it, instead of the `Add` and `Done` pair.'),
        ], [
            Q('sy1', L('Che cosa fa `mu.Lock()`?', 'What does `mu.Lock()` do?'), [L('Blocca il programma per sempre', 'Blocks the program forever'), L('Lascia entrare una sola goroutine alla volta nella sezione', 'Lets only one goroutine at a time into the section'), L('Chiude il canale', 'Closes the channel'), L('Cifra i dati', 'Encrypts the data')], 1, L('Le altre aspettano l’Unlock.', 'The others wait for Unlock.')),
            Q('sy2', L('A che cosa serve `go test -race`?', 'What is `go test -race` for?'), [L('A eseguire i test più in fretta', 'Running tests faster'), L('A misurare la memoria', 'Measuring memory'), L('A lanciare i test in ordine casuale', 'Running tests in random order'), L('A trovare accessi concorrenti non protetti', 'Finding unprotected concurrent access')], 3, L('Le data race sono bug difficili da riprodurre.', 'Data races are bugs that are hard to reproduce.')),
            Q('sy3', L('Una `map` normale è sicura se più goroutine la scrivono insieme?', 'Is a plain `map` safe if several goroutines write it at once?'), [L('Sì, sempre', 'Yes, always'), L('Solo se è piccola', 'Only if it is small'), L('No: serve un mutex o sync.Map', 'No: it needs a mutex or sync.Map'), L('Solo con chiavi stringa', 'Only with string keys')], 2, L('Il runtime può anche fermare il programma con un errore fatale.', 'The runtime may even stop the program with a fatal error.')),
        ], task('go', L('Proteggi `c.saldo += importo` con `c.mu.Lock()` e `defer c.mu.Unlock()`.', 'Protect `c.saldo += importo` with `c.mu.Lock()` and `defer c.mu.Unlock()`.'), 'func (c *Conto) Versa(importo int) {
    c.saldo += importo
}', [re('c\.mu\.Lock\s*\(\s*\)[\s\S]*defer\s+c\.mu\.Unlock\s*\(\s*\)[\s\S]*c\.saldo\s*\+=\s*importo', L('Il saldo è protetto', 'The balance is protected'))], '', 'func (c *Conto) Versa(importo int) {
    c.mu.Lock()
    defer c.mu.Unlock()
    c.saldo += importo
}'), [
            dBug(pBug(), ['func (c *Cache) Metti(k, v string) {', '    c.mu.Lock()', '    c.dati[k] = v', '}'], 1, '    c.mu.Lock(); defer c.mu.Unlock()', L('Senza Unlock il mutex resta chiuso e la chiamata successiva si blocca per sempre.', 'Without Unlock the mutex stays locked and the next call blocks forever.')),
            dMatch(pMatch(), [['sync.Mutex', L('Uno alla volta', 'One at a time')], ['sync.RWMutex', L('Tanti lettori, uno scrittore', 'Many readers, one writer')], ['sync/atomic', L('Contatori senza lock', 'Lock-free counters')], ['wg.Go', L('Avvia e conta una goroutine', 'Starts and tracks a goroutine')], ['-race', L('Trova le data race', 'Finds data races')]], L('Dati condivisi in sicurezza.', 'Sharing data safely.')),
        ]),
        lesson('io', L('io.Reader, file e JSON', 'io.Reader, files and JSON'), [
            P('`io.Reader` e `io.Writer` sono le interfacce più importanti della libreria standard: file, connessioni di rete, buffer e stringhe le soddisfano tutti. Una funzione che accetta un `io.Reader` funziona con qualsiasi sorgente ed è facile da testare. `os.ReadFile` e `os.WriteFile` leggono e scrivono un file intero in una riga.', '`io.Reader` and `io.Writer` are the most important interfaces in the standard library: files, network connections, buffers and strings all satisfy them. A function that accepts an `io.Reader` works with any source and is easy to test. `os.ReadFile` and `os.WriteFile` read and write a whole file in one line.'),
            C('func contaRighe(r io.Reader) (int, error) {
    scanner := bufio.NewScanner(r)
    n := 0
    for scanner.Scan() {
        n++
    }
    return n, scanner.Err()
}

func main() {
    f, err := os.Open("note.txt")
    if err != nil {
        log.Fatal(err)
    }
    defer f.Close()
    n, _ := contaRighe(f)                            // un file
    m, _ := contaRighe(strings.NewReader("a\nb"))    // una stringa
    fmt.Println(n, m)

    dati, err := os.ReadFile("config.json")
    if err != nil {
        log.Fatal(err)
    }
    var cfg struct {
        Porta int `json:"porta"`
    }
    if err := json.Unmarshal(dati, &cfg); err != nil {
        log.Fatal(err)
    }
}', 'go'),
            P('`json.Marshal` trasforma una struct in JSON, `json.Unmarshal` fa il contrario e vuole un **puntatore** alla destinazione. Solo i campi esportati (con la maiuscola) finiscono nel JSON.', '`json.Marshal` turns a struct into JSON, `json.Unmarshal` does the opposite and needs a **pointer** to the destination. Only exported (capitalised) fields end up in the JSON.'),
        ], [
            Q('io1', L('Perché `contaRighe` accetta un `io.Reader` invece di un `*os.File`?', 'Why does `contaRighe` accept an `io.Reader` instead of an `*os.File`?'), [L('Perché è più veloce', 'Because it is faster'), L('Funziona con file, stringhe e rete, e si testa facilmente', 'It works with files, strings and network, and is easy to test'), L('Perché os.File non si può leggere', 'Because os.File cannot be read'), L('Perché lo impone il compilatore', 'Because the compiler requires it')], 1, L('Chiedi il minimo indispensabile.', 'Ask for the bare minimum.')),
            Q('io2', L('Quale funzione legge un file intero?', 'Which function reads a whole file?'), ['os.ReadFile', 'io.ReadAll(path)', 'bufio.Read', 'os.Open'], 0, L('`os.Open` apre soltanto.', '`os.Open` only opens.')),
            Q('io3', L('Che cosa fa `json.Unmarshal(dati, &cfg)`?', 'What does `json.Unmarshal(dati, &cfg)` do?'), [L('Scrive cfg in un file JSON', 'Writes cfg to a JSON file'), L('Converte cfg in testo', 'Converts cfg to text'), L('Riempie cfg con i valori del JSON', 'Fills cfg with the JSON’s values'), L('Controlla solo che il JSON sia valido', 'Only checks that the JSON is valid')], 2, L('Per questo serve il puntatore.', 'That is why the pointer is needed.')),
        ], task('go', L('Converti la struct `p` in JSON con `json.Marshal(p)`, gestendo l’errore, e scrivilo nel file `persona.json` con `os.WriteFile` e permessi `0644`.', 'Convert the struct `p` to JSON with `json.Marshal(p)`, handling the error, and write it to `persona.json` with `os.WriteFile` and permissions `0644`.'), '', [re('(\w+)\s*,\s*err\s*:=\s*json\.Marshal\s*\(\s*p\s*\)', L('Converti in JSON', 'You convert to JSON')), re('os\.WriteFile\s*\(\s*"persona\.json"\s*,\s*\w+\s*,\s*0o?644\s*\)', L('Scrivi il file', 'You write the file'))], '', "dati, err := json.Marshal(p)\nif err != nil {\n    return err\n}\nerr = os.WriteFile(\"persona.json\", dati, 0644)"), [
            dMatch(pMatch(), [['io.Reader', L('Qualcosa da cui leggere', 'Something to read from')], ['io.Writer', L('Qualcosa su cui scrivere', 'Something to write to')], ['bufio.Scanner', L('Legge riga per riga', 'Reads line by line')], ['os.ReadFile', L('Legge tutto il file', 'Reads the whole file')], ['json.Marshal', L('Da struct a JSON', 'Struct to JSON')]], L('Leggere e scrivere dati.', 'Reading and writing data.')),
            dFlash(pFlash(), [[L('`strings.NewReader` crea un io.Reader da una stringa', '`strings.NewReader` makes an io.Reader from a string'), true], [L('Un file aperto con `os.Open` si chiude da solo', 'A file opened with `os.Open` closes by itself'), false], [L('`json.Unmarshal` vuole un puntatore alla destinazione', '`json.Unmarshal` needs a pointer to the destination'), true], [L('I campi in minuscolo di una struct finiscono nel JSON', 'A struct’s lowercase fields end up in the JSON'), false]], L('Interfacce piccole, tanti usi.', 'Small interfaces, many uses.')),
        ]),
        lesson('iterators', L('Iteratori con range sulle funzioni', 'Iterators with range over functions'), [
            P('Da **Go 1.23** `range` funziona anche sulle funzioni: un **iteratore** (`iter.Seq[T]`) è una funzione che riceve `yield` e lo chiama per ogni valore. Se `yield` restituisce `false`, chi sta ciclando ha fatto `break` e l’iteratore deve fermarsi.', 'Since **Go 1.23** `range` also works on functions: an **iterator** (`iter.Seq[T]`) is a function that receives `yield` and calls it for every value. If `yield` returns `false`, the loop has hit `break` and the iterator must stop.'),
            C('func Pari(fino int) iter.Seq[int] {
    return func(yield func(int) bool) {
        for n := 0; n <= fino; n += 2 {
            if !yield(n) {
                return           // chi usa il ciclo ha fatto break
            }
        }
    }
}

func main() {
    for n := range Pari(10) {
        if n > 6 {
            break
        }
        fmt.Println(n)       // 0 2 4 6
    }

    eta := map[string]int{"Bea": 28, "Ada": 36}
    for _, nome := range slices.Sorted(maps.Keys(eta)) {
        fmt.Println(nome, eta[nome])   // Ada 36, poi Bea 28
    }
}', 'go'),
            P('I pacchetti `slices` e `maps` lavorano con gli iteratori: `maps.Keys` e `slices.Values` li producono, `slices.Sorted` e `slices.Collect` li trasformano in slice. Un iteratore calcola i valori solo quando servono.', 'The `slices` and `maps` packages work with iterators: `maps.Keys` and `slices.Values` produce them, `slices.Sorted` and `slices.Collect` turn them into slices. An iterator computes values only when they are needed.'),
        ], [
            Q('it1', L('Che tipo restituisce un iteratore di interi?', 'What type does an iterator of ints return?'), ['[]int', 'chan int', 'iter.Seq[int]', 'func() int'], 2, L('Dal pacchetto `iter`.', 'From the `iter` package.')),
            Q('it2', L('Che cosa significa `yield` che restituisce `false`?', 'What does `yield` returning `false` mean?'), [L('Che il valore non era valido', 'That the value was not valid'), L('Che il ciclo è stato interrotto e bisogna fermarsi', 'That the loop was interrupted and you must stop'), L('Che la slice è vuota', 'That the slice is empty'), L('Che c’è stato un errore di rete', 'That there was a network error')], 1, L('Continuare a chiamare yield causerebbe un panic.', 'Calling yield again would cause a panic.')),
            Q('it3', L('Come ottieni le chiavi di una mappa in ordine?', 'How do you get a map’s keys in order?'), ['slices.Sorted(maps.Keys(m))', 'sort(m.keys())', 'maps.Keys(m).Sort()', 'range m sorted'], 0, L('`maps.Keys` dà un iteratore, `slices.Sorted` lo raccoglie ordinato.', '`maps.Keys` gives an iterator, `slices.Sorted` collects it sorted.')),
        ], task('go', L('Scrivi `func Conto(n int) iter.Seq[int]` che fa `yield` dei numeri da 1 a `n`, fermandosi se `yield` restituisce false.', 'Write `func Conto(n int) iter.Seq[int]` that yields the numbers from 1 to `n`, stopping if `yield` returns false.'), '', [re('func\s+Conto\s*\(\s*n\s+int\s*\)\s*iter\.Seq\[\s*int\s*\]', L('La firma restituisce un iteratore', 'The signature returns an iterator')), re('return\s+func\s*\(\s*yield\s+func\s*\(\s*int\s*\)\s*bool\s*\)', L('Restituisci la funzione con yield', 'You return the function with yield')), re('if\s+!\s*yield\s*\(\s*\w+\s*\)', L('Ti fermi quando serve', 'You stop when needed'))], '', "func Conto(n int) iter.Seq[int] {\n    return func(yield func(int) bool) {\n        for i := 1; i <= n; i++ {\n            if !yield(i) {\n                return\n            }\n        }\n    }\n}"), [
            dBlanks(pFill(), 'func Lettere(s string) iter.Seq[rune] {
    return func(___ func(rune) bool) {
        for _, r := range s {
            if !yield(r) {
                ___
            }
        }
    }
}
for r := ___ Lettere("go") {
    fmt.Println(string(r))
}', ['yield', 'return', 'range'], ['next', 'continue', 'in'], L('Parametro yield, uscita, ciclo.', 'yield parameter, exit, loop.')),
            dFlash(pFlash(), [[L('Da Go 1.23 si può fare range su una funzione iteratore', 'Since Go 1.23 you can range over an iterator function'), true], [L('`maps.Keys` restituisce una slice ordinata', '`maps.Keys` returns a sorted slice'), false], [L('Se yield restituisce false l’iteratore deve smettere', 'If yield returns false the iterator must stop'), true], [L('Gli iteratori richiedono una goroutine per ogni valore', 'Iterators need a goroutine per value'), false]], L('Sequenze pigre, senza canali.', 'Lazy sequences, without channels.')),
        ]),
        lesson('benchmarks', L('Sottotest, benchmark e fuzzing', 'Subtests, benchmarks and fuzzing'), [
            P('`t.Run` crea un **sottotest** per ogni caso di una tabella: i casi si leggono meglio e si possono lanciare uno per uno. I **benchmark** misurano velocità e allocazioni; il **fuzzing** genera input casuali per scoprire casi che non avevi immaginato.', '`t.Run` creates a **subtest** for each case in a table: cases read better and can be run one at a time. **Benchmarks** measure speed and allocations; **fuzzing** generates random inputs to uncover cases you had not imagined.'),
            C('func TestSlug(t *testing.T) {
    casi := []struct {
        nome, in, atteso string
    }{
        {"spazi", "Ciao Mondo", "ciao-mondo"},
        {"vuoto", "", ""},
    }
    for _, c := range casi {
        t.Run(c.nome, func(t *testing.T) {
            if got := Slug(c.in); got != c.atteso {
                t.Errorf("Slug(%q) = %q, atteso %q", c.in, got, c.atteso)
            }
        })
    }
}

func BenchmarkSlug(b *testing.B) {
    for b.Loop() {               // Go 1.24+
        Slug("Ciao Mondo")
    }
}

func FuzzSlug(f *testing.F) {
    f.Add("Ciao Mondo")
    f.Fuzz(func(t *testing.T, s string) {
        if strings.Contains(Slug(s), " ") {
            t.Errorf("spazio in %q", Slug(s))
        }
    })
}', 'go'),
            C('go test -run TestSlug/vuoto ./...
go test -bench . -benchmem
go test -fuzz FuzzSlug -fuzztime 30s', 'bash'),
        ], [
            Q('tb1', L('Che cosa fa `t.Run(nome, func...)`?', 'What does `t.Run(name, func...)` do?'), [L('Avvia un sottotest con un nome', 'Starts a named subtest'), L('Esegue il programma', 'Runs the program'), L('Lancia tutti i benchmark', 'Runs every benchmark'), L('Salta il test', 'Skips the test')], 0, L('Si filtra con `-run Test/nome`.', 'You filter it with `-run Test/name`.')),
            Q('tb2', L('Come si lanciano i benchmark?', 'How do you run benchmarks?'), ['go bench', 'go run -bench', 'go test -fuzz', 'go test -bench .'], 3, L('Il punto è un’espressione che li prende tutti.', 'The dot is a pattern that matches them all.')),
            Q('tb3', L('A che cosa serve il fuzzing?', 'What is fuzzing for?'), [L('A formattare il codice', 'Formatting code'), L('A generare input inattesi per trovare bug', 'Generating unexpected inputs to find bugs'), L('A misurare la memoria', 'Measuring memory'), L('A rendere i test più leggibili', 'Making tests more readable')], 1, L('Gli input che falliscono vengono salvati per ripetere il test.', 'Failing inputs are saved to repeat the test.')),
        ], task('go', L('Scrivi `func BenchmarkSomma(b *testing.B)` che chiama `Somma(2, 3)` in un ciclo `for b.Loop()`.', 'Write `func BenchmarkSomma(b *testing.B)` calling `Somma(2, 3)` in a `for b.Loop()` loop.'), '', [re('func\s+BenchmarkSomma\s*\(\s*b\s+\*testing\.B\s*\)', L('Il benchmark esiste', 'The benchmark exists')), re('for\s+(b\.Loop\s*\(\s*\)|(\w+)\s*:=\s*0\s*;\s*\2\s*<\s*b\.N\s*;\s*\2\+\+|range\s+b\.N)\s*\{\s*Somma\s*\(\s*2\s*,\s*3\s*\)', L('Ripeti la chiamata', 'You repeat the call'))], '', "func BenchmarkSomma(b *testing.B) {\n    for b.Loop() {\n        Somma(2, 3)\n    }\n}"), [
            dMatch(pMatch(), [['t.Run', L('Sottotest', 'Subtest')], ['b.Loop()', L('Ciclo del benchmark', 'Benchmark loop')], ['-benchmem', L('Mostra le allocazioni', 'Shows allocations')], ['f.Fuzz', L('Input casuali', 'Random inputs')], ['t.Errorf', L('Segnala e continua', 'Reports and continues')]], L('Testare oltre i casi base.', 'Testing beyond the basic cases.')),
            dOrder(pSort(), ['func TestDoppio(t *testing.T) {', '    for _, c := range casi {', '        t.Run(c.nome, func(t *testing.T) {', '            if Doppio(c.in) != c.atteso { t.Error("sbagliato") }', '        })', '    }', '}'], L('Test, ciclo sui casi, sottotest, controllo.', 'Test, loop over cases, subtest, check.')),
        ]),
    ],
];
