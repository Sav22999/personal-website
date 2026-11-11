<?php
/** Go basics: packages and types, functions and errors, structs and interfaces, goroutines and channels. */

return [
    'id' => 'go-base', 'family' => 'go', 'after' => null,
    'title' => L('Go base', 'Go Basics'), 'short' => L('Go base', 'Go Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio semplice e veloce dei servizi cloud: pacchetti e tipi, funzioni ed errori, struct e interfacce, goroutine e canali.', 'The simple, fast language of cloud services: packages and types, functions and errors, structs and interfaces, goroutines and channels.'),
    'lessons' => [
        lesson('basics', L('Pacchetti, variabili e tipi', 'Packages, variables and types'), [
            P('**Go** è un linguaggio compilato creato da Google: semplice, veloce, con un solo modo idiomatico di fare le cose. Docker e Kubernetes sono scritti in Go. Ogni file appartiene a un **pacchetto**; il programma parte dalla funzione `main` del pacchetto `main`.', '**Go** is a compiled language created at Google: simple, fast, with one idiomatic way of doing things. Docker and Kubernetes are written in Go. Every file belongs to a **package**; the program starts from the `main` function of the `main` package.'),
            C('package main

import "fmt"

func main() {
    var citta string = "Udine"
    abitanti := 98000          // tipo dedotto: int
    const iva = 0.22
    fmt.Printf("%s ha %d abitanti\n", citta, abitanti)
}', 'go'),
            P('`:=` dichiara e assegna in un colpo solo, dentro le funzioni. Le variabili non usate e gli import inutili sono **errori di compilazione**: Go ti obbliga a tenere il codice pulito. `go run main.go` esegue, `go build` crea l’eseguibile, `gofmt` formatta.', '`:=` declares and assigns in one go, inside functions. Unused variables and imports are **compile errors**: Go forces you to keep code tidy. `go run main.go` runs, `go build` creates the executable, `gofmt` formats.'),
        ], [
            Q('b1', L('Da quale funzione parte un programma Go?', 'Which function does a Go program start from?'), ['main', 'start', 'init', 'run'], 0, L('Nel pacchetto main.', 'In the main package.')),
            Q('b2', L('Che cosa fa `:=`?', 'What does `:=` do?'), [L('Dichiara e assegna deducendo il tipo', 'Declares and assigns inferring the type'), L('Confronta due valori', 'Compares two values'), L('Crea una costante', 'Creates a constant'), L('Importa un pacchetto', 'Imports a package')], 0, L('Solo dentro le funzioni.', 'Only inside functions.')),
            Q('b3', L('Che cosa succede con una variabile non usata?', 'What happens with an unused variable?'), [L('Errore di compilazione', 'Compile error'), L('Niente', 'Nothing'), L('Un avviso ignorabile', 'An ignorable warning'), L('Viene cancellata', 'It gets deleted')], 0, L('Codice pulito per forza.', 'Clean code by force.')),
        ], task('go', L('Nel `main` dichiara con `:=` la variabile `nome` uguale a `"Gopher"` e stampala con `fmt.Println`.', 'In `main` declare with `:=` the variable `nome` equal to `"Gopher"` and print it with `fmt.Println`.'), "package main\n\nimport \"fmt\"\n\nfunc main() {\n    \n}", [re('nome\s*:=\s*"Gopher"', L('La variabile', 'The variable')), re('fmt\.Println\s*\(\s*nome\s*\)', L('La stampi', 'You print it'))], '', "nome := \"Gopher\"\n    fmt.Println(nome)"), [
            dOrder(pSort(), ['package main', 'import "fmt"', 'func main() {', '    fmt.Println("Ciao")', '}'], L('Pacchetto, import, funzione.', 'Package, import, function.')),
            dMatch(pMatch(), [['package main', L('Programma eseguibile', 'Executable program')], [':=', L('Dichiara e assegna', 'Declares and assigns')], ['go run', L('Esegue', 'Runs')], ['go build', L('Crea l’eseguibile', 'Builds the executable')], ['gofmt', L('Formatta il codice', 'Formats code')]], L('Gli strumenti di Go.', 'Go tools.')),
        ]),
        lesson('errors', L('Funzioni ed errori', 'Functions and errors'), [
            P('Le funzioni Go possono restituire **più valori**. Per convenzione l’ultimo è un **`error`**: se è diverso da `nil`, qualcosa è andato storto. Non ci sono eccezioni: gli errori si controllano esplicitamente, subito.', 'Go functions can return **multiple values**. By convention the last is an **`error`**: if it is not `nil`, something went wrong. There are no exceptions: errors are checked explicitly, right away.'),
            C('func dividi(a, b float64) (float64, error) {
    if b == 0 {
        return 0, errors.New("divisione per zero")
    }
    return a / b, nil
}

risultato, err := dividi(10, 0)
if err != nil {
    fmt.Println("Errore:", err)
    return
}
fmt.Println(risultato)', 'go'),
            P('Con `fmt.Errorf("lettura config: %w", err)` aggiungi contesto a un errore mantenendo l’originale. `defer` esegue una chiamata alla fine della funzione: perfetto per chiudere file e connessioni.', 'With `fmt.Errorf("reading config: %w", err)` you add context to an error while keeping the original. `defer` runs a call at the end of the function: perfect for closing files and connections.'),
        ], [
            Q('e1', L('Come segnala un errore una funzione Go?', 'How does a Go function signal an error?'), [L('Restituendo un valore error diverso da nil', 'Returning a non-nil error value'), L('Lanciando un’eccezione', 'Throwing an exception'), L('Stampando in rosso', 'Printing in red'), L('Chiudendo il programma sempre', 'Always exiting the program')], 0, L('Niente eccezioni.', 'No exceptions.')),
            Q('e2', L('Che cosa fa `defer f.Close()`?', 'What does `defer f.Close()` do?'), [L('Chiude il file alla fine della funzione', 'Closes the file at the end of the function'), L('Chiude subito', 'Closes at once'), L('Non lo chiude mai', 'Never closes it'), L('Cancella il file', 'Deletes the file')], 0, L('Anche se la funzione esce prima.', 'Even if the function returns early.')),
            Q('e3', L('A che cosa serve `%w` in `fmt.Errorf`?', 'What is `%w` for in `fmt.Errorf`?'), [L('Avvolgere l’errore originale aggiungendo contesto', 'Wrapping the original error adding context'), L('Stampare una parola', 'Printing a word'), L('Formattare numeri', 'Formatting numbers'), L('Ignorare l’errore', 'Ignoring the error')], 0, L('w = wrap.', 'w = wrap.')),
        ], task('go', L('Chiama `leggiConfig("app.json")` che restituisce `(cfg, err)` e, se `err != nil`, restituisci `fmt.Errorf("config: %w", err)`.', 'Call `leggiConfig("app.json")` which returns `(cfg, err)` and, if `err != nil`, return `fmt.Errorf("config: %w", err)`.'), '', [re('cfg\s*,\s*err\s*:=\s*leggiConfig\s*\(\s*"app\.json"\s*\)', L('La chiamata', 'The call')), re('if\s+err\s*!=\s*nil\s*\{\s*return\s+(nil\s*,\s*)?fmt\.Errorf\s*\(\s*"config: %w"\s*,\s*err\s*\)', L('Gestisci l’errore', 'You handle the error'))], '', "cfg, err := leggiConfig(\"app.json\")\nif err != nil {\n    return nil, fmt.Errorf(\"config: %w\", err)\n}"), [
            dBug(pBug(), ['f, err := os.Open("dati.txt")', 'defer f.Close()', 'if err != nil {', '    return err', '}'], 1, 'if err != nil { return err }; defer f.Close()', L('Controlla l’errore prima di usare f.', 'Check the error before using f.')),
            dMatch(pMatch(), [['error', L('Tipo degli errori', 'Error type')], ['nil', L('Nessun errore', 'No error')], ['defer', L('Esegue alla fine', 'Runs at the end')], ['%w', L('Avvolge un errore', 'Wraps an error')], ['errors.New', L('Crea un errore', 'Creates an error')]], L('Gestire gli errori alla Go.', 'Handling errors the Go way.')),
        ]),
        lesson('structs', L('Struct e interfacce', 'Structs and interfaces'), [
            P('Go non ha classi: ha le **struct** (insiemi di campi) e i **metodi** associati. Un’**interfaccia** elenca dei metodi; un tipo la soddisfa **automaticamente** se ha quei metodi, senza dichiararlo.', 'Go has no classes: it has **structs** (sets of fields) and associated **methods**. An **interface** lists methods; a type satisfies it **automatically** if it has those methods, without declaring it.'),
            C('type Forma interface {
    Area() float64
}

type Rettangolo struct {
    Base, Altezza float64
}

func (r Rettangolo) Area() float64 {
    return r.Base * r.Altezza
}

func stampaArea(f Forma) {
    fmt.Printf("%.2f\n", f.Area())
}

stampaArea(Rettangolo{Base: 3, Altezza: 4})   // 12.00', 'go'),
            P('I nomi con la **maiuscola** sono esportati (visibili da altri pacchetti), quelli in minuscolo restano privati. Un metodo con ricevitore puntatore (`func (r *Rettangolo)`) può modificare la struct.', 'Names starting with a **capital** are exported (visible from other packages), lowercase ones stay private. A method with a pointer receiver (`func (r *Rettangolo)`) can modify the struct.'),
        ], [
            Q('s1', L('Come si dichiara che un tipo implementa un’interfaccia?', 'How do you declare a type implements an interface?'), [L('Non serve: basta avere i metodi', 'No need: having the methods is enough'), L('Con implements', 'With implements'), L('Con extends', 'With extends'), L('Con un commento', 'With a comment')], 0, L('Implementazione implicita.', 'Implicit implementation.')),
            Q('s2', L('Un campo che inizia con la maiuscola è…', 'A field starting with a capital is…'), [L('Esportato, visibile da altri pacchetti', 'Exported, visible from other packages'), L('Una costante', 'A constant'), L('Privato', 'Private'), L('Un errore', 'An error')], 0, L('Minuscolo = privato.', 'Lowercase = private.')),
            Q('s3', L('Quando usare un ricevitore puntatore?', 'When should you use a pointer receiver?'), [L('Quando il metodo modifica la struct', 'When the method modifies the struct'), L('Mai', 'Never'), L('Solo per stampare', 'Only to print'), L('Sempre per le interfacce vuote', 'Always for empty interfaces')], 0, L('`func (r *T)`.', '`func (r *T)`.')),
        ], task('go', L('Definisci la struct `Cerchio` con il campo `Raggio float64` e il metodo `Area()` che restituisce `math.Pi * c.Raggio * c.Raggio`.', 'Define the struct `Cerchio` with the field `Raggio float64` and the method `Area()` returning `math.Pi * c.Raggio * c.Raggio`.'), '', [re('type\s+Cerchio\s+struct\s*\{\s*Raggio\s+float64\s*\}', L('La struct', 'The struct')), re('func\s*\(\s*c\s+\*?Cerchio\s*\)\s*Area\s*\(\s*\)\s*float64', L('Il metodo', 'The method')), re('return\s+math\.Pi\s*\*\s*c\.Raggio\s*\*\s*c\.Raggio', L('Il calcolo', 'The calculation'))], '', "type Cerchio struct {\n    Raggio float64\n}\n\nfunc (c Cerchio) Area() float64 {\n    return math.Pi * c.Raggio * c.Raggio\n}"), [
            dMatch(pMatch(), [['struct', L('Insieme di campi', 'Set of fields')], ['interface', L('Elenco di metodi', 'List of methods')], ['func (r T)', L('Metodo con ricevitore', 'Method with receiver')], ['*T', L('Puntatore: può modificare', 'Pointer: can modify')], [L('Maiuscola', 'Capital letter'), L('Esportato', 'Exported')]], L('Tipi in Go.', 'Types in Go.')),
            dFlash(pFlash(), [[L('Go non ha classi', 'Go has no classes'), true], [L('Bisogna scrivere implements per soddisfare un’interfaccia', 'You must write implements to satisfy an interface'), false], [L('I nomi in minuscolo sono privati al pacchetto', 'Lowercase names are package-private'), true], [L('Le struct non possono avere metodi', 'Structs cannot have methods'), false]], L('Semplice e implicito.', 'Simple and implicit.')),
        ]),
        lesson('concurrency', L('Goroutine e canali', 'Goroutines and channels'), [
            P('La **concorrenza** è il punto forte di Go. Una **goroutine** è una funzione che gira in parallelo, leggerissima: se ne possono avviare migliaia con `go funzione()`. I **canali** (`chan`) servono a comunicare tra goroutine in modo sicuro.', '**Concurrency** is Go’s strong point. A **goroutine** is a very lightweight function running concurrently: you can start thousands with `go function()`. **Channels** (`chan`) let goroutines communicate safely.'),
            C('func scarica(url string, risultati chan<- string) {
    resp, err := http.Get(url)
    if err != nil {
        risultati <- url + ": errore"
        return
    }
    defer resp.Body.Close()
    risultati <- fmt.Sprintf("%s: %d", url, resp.StatusCode)
}

func main() {
    urls := []string{"https://go.dev", "https://example.com"}
    risultati := make(chan string)
    for _, u := range urls {
        go scarica(u, risultati)
    }
    for range urls {
        fmt.Println(<-risultati)    // aspetta ogni risposta
    }
}', 'go'),
            P('`ch <- v` invia, `<-ch` riceve (e aspetta). Per aspettare un gruppo di goroutine si usa `sync.WaitGroup`. Il motto di Go: “non comunicare condividendo memoria; condividi memoria comunicando”.', '`ch <- v` sends, `<-ch` receives (and waits). To wait for a group of goroutines you use `sync.WaitGroup`. Go’s motto: “do not communicate by sharing memory; share memory by communicating”.'),
        ], [
            Q('g1', L('Come si avvia una goroutine?', 'How do you start a goroutine?'), ['go funzione()', 'async funzione()', 'thread funzione()', 'run funzione()'], 0, L('Leggerissime.', 'Very lightweight.')),
            Q('g2', L('Che cosa fa `<-ch`?', 'What does `<-ch` do?'), [L('Riceve dal canale, aspettando', 'Receives from the channel, waiting'), L('Chiude il canale', 'Closes the channel'), L('Invia al canale', 'Sends to the channel'), L('Cancella il canale', 'Deletes the channel')], 0, L('`ch <- v` invia.', '`ch <- v` sends.')),
            Q('g3', L('Che cosa serve per aspettare un gruppo di goroutine?', 'What is used to wait for a group of goroutines?'), ['sync.WaitGroup', 'time.Sleep sempre', 'fmt.Wait', 'os.Exit'], 0, L('Add, Done, Wait.', 'Add, Done, Wait.')),
        ], task('go', L('Crea un canale di interi `ch` con `make`, avvia una goroutine che invia `42` nel canale e stampa il valore ricevuto.', 'Create an int channel `ch` with `make`, start a goroutine sending `42` into it and print the received value.'), "package main\n\nimport \"fmt\"\n\nfunc main() {\n    \n}", [re('ch\s*:=\s*make\s*\(\s*chan\s+int\s*\)', L('Il canale', 'The channel')), re('go\s+func\s*\(\s*\)\s*\{\s*ch\s*<-\s*42\s*\}\s*\(\s*\)', L('La goroutine invia 42', 'The goroutine sends 42')), re('fmt\.Println\s*\(\s*<-\s*ch\s*\)', L('Stampi il valore ricevuto', 'You print the received value'))], '', "ch := make(chan int)\n    go func() { ch <- 42 }()\n    fmt.Println(<-ch)"), [
            dMatch(pMatch(), [['go f()', L('Avvia una goroutine', 'Starts a goroutine')], ['make(chan T)', L('Crea un canale', 'Creates a channel')], ['ch <- v', L('Invia', 'Sends')], ['<-ch', L('Riceve', 'Receives')], ['WaitGroup', L('Aspetta un gruppo', 'Waits for a group')]], L('Concorrenza in Go.', 'Concurrency in Go.')),
            dFlash(pFlash(), [[L('Si possono avviare migliaia di goroutine', 'You can start thousands of goroutines'), true], [L('Ricevere da un canale non aspetta mai', 'Receiving from a channel never waits'), false], [L('I canali permettono di comunicare tra goroutine', 'Channels let goroutines communicate'), true], [L('Docker è scritto in PHP', 'Docker is written in PHP'), false]], L('Parallelo, ma ordinato.', 'Concurrent, but orderly.')),
        ]),
    ],
];
