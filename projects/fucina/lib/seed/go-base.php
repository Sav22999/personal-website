<?php
/** Go basics: packages and types, functions and errors, structs and interfaces, goroutines and channels, HTTP and tests, control flow, slices and maps, pointers, modules. */

/** Two goroutines send their results into one channel; main receives them one at a time. Messages travel along it. */
if (!function_exists('fig_gobase_chan')) {
    function fig_gobase_chan(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<style>.m1{animation:send1 5s ease-in-out infinite}.m2{animation:send2 5s ease-in-out infinite}
@keyframes send1{0%{transform:translate(196px,64px);opacity:1}30%{transform:translate(262px,126px)}75%{transform:translate(452px,126px);opacity:1}85%,100%{transform:translate(452px,126px);opacity:0}}
@keyframes send2{0%,20%{transform:translate(196px,188px);opacity:1}50%{transform:translate(262px,126px)}92%{transform:translate(452px,126px);opacity:1}100%{transform:translate(452px,126px);opacity:0}}
@media (prefers-reduced-motion:reduce){.m1,.m2{animation:none}}</style>
<g font-family="ui-monospace, monospace" font-size="13">
<rect x="16" y="40" width="176" height="48" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="104" y="69" text-anchor="middle">go scarica(go.dev)</text>
<rect x="16" y="164" width="176" height="48" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="104" y="185" text-anchor="middle">go scarica(</text><text x="104" y="202" text-anchor="middle">example.com)</text>
<rect x="250" y="106" width="210" height="40" rx="20" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="355" y="98" text-anchor="middle" font-weight="700" fill="#B84508">chan string</text>
<rect x="470" y="96" width="154" height="60" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="547" y="122" text-anchor="middle" font-weight="700">main</text><text x="547" y="142" text-anchor="middle">&lt;-risultati</text>
</g>
<path d="M194 64 C230 64 236 118 248 122" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/>
<path d="M194 188 C230 188 236 134 248 130" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/>
<circle class="m1" r="8" fill="#2F8CF0" transform="translate(320 126)"/>
<circle class="m2" r="8" fill="#7048E8" transform="translate(390 126)"/>
<text x="104" y="30" text-anchor="middle" font-size="13" fill="#1B66BC">{$t[0]}</text>
<text x="355" y="172" text-anchor="middle" font-size="13" fill="#B84508">{$t[1]}</text>
<text x="547" y="180" text-anchor="middle" font-size="13" fill="#0B7F5C">{$t[2]}</text>
<text x="320" y="240" text-anchor="middle" fill="#5B5880">{$t[3]}</text>
</svg>
SVG;
    }
}

/** A slice is a small header (pointer, length, capacity) over an array; a sub-slice shares the same array. */
if (!function_exists('fig_gobase_slice')) {
    function fig_gobase_slice(array $t): string
    {
        $cells = '';
        foreach (['6', '7', '8', '9', '', ''] as $i => $v) {
            $x = 200 + $i * 64;
            $used = $v !== '';
            $cells .= "<text x=\"" . ($x + 30) . "\" y=\"108\" text-anchor=\"middle\" font-size=\"12\" fill=\"#5B5880\">$i</text><rect x=\"$x\" y=\"116\" width=\"60\" height=\"44\" rx=\"8\" fill=\"" . ($used ? '#DCEBFF' : '#F1F0F8') . "\" stroke=\"" . ($used ? '#2F8CF0' : '#C9C7DE') . "\" stroke-width=\"2\"" . ($used ? '' : ' stroke-dasharray="5 4"') . "/><text x=\"" . ($x + 30) . "\" y=\"144\" text-anchor=\"middle\" font-weight=\"700\">$v</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="ui-monospace, monospace" font-size="14" fill="#1E1C52">
<defs><marker id="sl" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="20" width="160" height="70" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="28" y="42" font-weight="700">voti</text><text x="28" y="62" font-size="13">len 4</text><text x="28" y="80" font-size="13">cap 6</text>
<circle cx="150" cy="55" r="5" fill="#1E1C52"/>
<path d="M150 55 C190 55 210 70 226 110" fill="none" stroke="#1E1C52" stroke-width="2" marker-end="url(#sl)"/>
$cells
<rect x="262" y="112" width="128" height="52" rx="10" fill="none" stroke="#E8590C" stroke-width="3"/>
<rect x="16" y="186" width="160" height="70" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="28" y="208" font-weight="700">voti[1:3]</text><text x="28" y="228" font-size="13">len 2</text><text x="28" y="246" font-size="13">cap 5</text>
<circle cx="150" cy="221" r="5" fill="#1E1C52"/>
<path d="M150 221 C220 221 280 200 290 168" fill="none" stroke="#E8590C" stroke-width="2" marker-end="url(#sl)"/>
<text x="420" y="200" font-family="system-ui, sans-serif" font-size="13" fill="#5B5880">{$t[0]}</text>
<text x="420" y="218" font-family="system-ui, sans-serif" font-size="13" fill="#5B5880">{$t[1]}</text>
<text x="456" y="98" font-family="system-ui, sans-serif" font-size="12" fill="#8A87B0">{$t[2]}</text>
</svg>
SVG;
    }
}

/** Passing a struct by value makes a copy; passing a pointer lets the function change the original. */
if (!function_exists('fig_gobase_ptr')) {
    function fig_gobase_ptr(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="ui-monospace, monospace" font-size="14" fill="#1E1C52">
<defs><marker id="pt" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="210" y="16" width="220" height="80" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="226" y="38" font-weight="700">main</text>
<rect x="250" y="48" width="140" height="36" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="320" y="71" text-anchor="middle">c.Valore = 1</text>
<rect x="16" y="140" width="260" height="76" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="30" y="162" font-weight="700" font-size="13">incrementaCopia(c)</text>
<rect x="56" y="172" width="180" height="34" rx="8" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4"/><text x="146" y="194" text-anchor="middle" font-size="13">copia.Valore = 1</text>
<rect x="364" y="140" width="260" height="76" rx="12" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="378" y="162" font-weight="700" font-size="13">incrementa(&amp;c)</text>
<rect x="404" y="172" width="180" height="34" rx="8" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="440" y="194" font-size="13">c *Contatore</text><circle cx="562" cy="189" r="5" fill="#1E1C52"/>
<path d="M248 72 C170 80 146 110 146 166" fill="none" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4" marker-end="url(#pt)"/>
<path d="M562 184 C562 100 470 66 396 66" fill="none" stroke="#7048E8" stroke-width="2" marker-end="url(#pt)"/>
<g font-family="system-ui, sans-serif" font-size="13">
<text x="146" y="236" text-anchor="middle" fill="#B8255F">{$t[0]}</text>
<text x="494" y="236" text-anchor="middle" fill="#5B33C9">{$t[1]}</text>
</g>
</svg>
SVG;
    }
}

return [
    'id' => 'go-base', 'family' => 'go', 'after' => null,
    'title' => L('Go base', 'Go Basics'), 'short' => L('Go base', 'Go Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il linguaggio semplice e veloce dei servizi cloud: pacchetti e tipi, funzioni ed errori, struct e interfacce, goroutine e canali, cicli, slice e mappe, puntatori e moduli.', 'The simple, fast language of cloud services: packages and types, functions and errors, structs and interfaces, goroutines and channels, loops, slices and maps, pointers and modules.'),
    'lessons' => [
        lesson('basics', L('Pacchetti, variabili e tipi', 'Packages, variables and types'), [
            P('**Go** è un linguaggio compilato creato da Google: semplice, veloce, con un solo modo idiomatico di fare le cose. Docker e Kubernetes sono scritti in Go. Ogni file appartiene a un **pacchetto**; il programma parte dalla funzione `main` del pacchetto `main`.', '**Go** is a compiled language created at Google: simple, fast, with one idiomatic way of doing things. Docker and Kubernetes are written in Go. Every file belongs to a **package**; the program starts from the `main` function of the `main` package.'),
            C(L('package main

import "fmt"

func main() {
    var citta string = "Udine"
    abitanti := 98000          // tipo dedotto: int
    const iva = 0.22
    fmt.Printf("%s ha %d abitanti\n", citta, abitanti)
}', 'package main

import "fmt"

func main() {
    var citta string = "Udine"
    abitanti := 98000          // tipo dedotto: int
    const iva = 0.22
    fmt.Printf("%s has %d inhabitants\n", citta, abitanti)
}'), 'go', L('<pre>Udine ha 98000 abitanti</pre>', '<pre>Udine has 98000 inhabitants</pre>')),
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

stampaArea(Rettangolo{Base: 3, Altezza: 4})   // 12.00', 'go', '<pre>12.00</pre>'),
            P('I nomi con la **maiuscola** sono esportati (visibili da altri pacchetti), quelli in minuscolo restano privati. Un metodo con ricevitore puntatore (`func (r *Rettangolo)`) può modificare la struct.', 'Names starting with a **capital** are exported (visible from other packages), lowercase ones stay private. A method with a pointer receiver (`func (r *Rettangolo)`) can modify the struct.'),
            P('Perché le interfacce implicite? Perché chi scrive `Rettangolo` non deve conoscere `Forma`: l’interfaccia la definisce chi la usa, ed è normale dichiararla vicino alla funzione che ne ha bisogno. In Go si preferiscono interfacce piccole, spesso di un solo metodo, come `fmt.Stringer` (`String() string`) o `io.Reader`. Per il ricevitore, una regola semplice: se un metodo deve modificare la struct, o la struct è grande, usa il puntatore, e allora usalo per tutti i metodi di quel tipo.', 'Why implicit interfaces? Because whoever writes `Rettangolo` does not need to know about `Forma`: the interface is defined by whoever uses it, and it is normal to declare it next to the function that needs it. Go prefers small interfaces, often of a single method, such as `fmt.Stringer` (`String() string`) or `io.Reader`. For the receiver, a simple rule: if a method must change the struct, or the struct is large, use a pointer, and then use it for every method of that type.'),
        ], [
            Q('s1', L('Come si dichiara che un tipo implementa un’interfaccia?', 'How do you declare a type implements an interface?'), [L('Non serve: basta avere i metodi', 'No need: having the methods is enough'), L('Con implements', 'With implements'), L('Con extends', 'With extends'), L('Con un commento', 'With a comment')], 0, L('Implementazione implicita.', 'Implicit implementation.')),
            Q('s2', L('Un campo che inizia con la maiuscola è…', 'A field starting with a capital is…'), [L('Esportato, visibile da altri pacchetti', 'Exported, visible from other packages'), L('Una costante', 'A constant'), L('Privato', 'Private'), L('Un errore', 'An error')], 0, L('Minuscolo = privato.', 'Lowercase = private.')),
            Q('s3', L('Quando usare un ricevitore puntatore?', 'When should you use a pointer receiver?'), [L('Quando il metodo modifica la struct', 'When the method modifies the struct'), L('Mai', 'Never'), L('Solo per stampare', 'Only to print'), L('Sempre per le interfacce vuote', 'Always for empty interfaces')], 0, L('`func (r *T)`.', '`func (r *T)`.')),
        ], task('go', L('Definisci la struct `Cerchio` con il campo `Raggio float64` e il metodo `Area()` che restituisce `math.Pi * c.Raggio * c.Raggio`.', 'Define the struct `Cerchio` with the field `Raggio float64` and the method `Area()` returning `math.Pi * c.Raggio * c.Raggio`.'), '', [re('type\s+Cerchio\s+struct\s*\{\s*Raggio\s+float64\s*\}', L('La struct', 'The struct')), re('func\s*\(\s*c\s+\*?Cerchio\s*\)\s*Area\s*\(\s*\)\s*float64', L('Il metodo', 'The method')), re('return\s+math\.Pi\s*\*\s*c\.Raggio\s*\*\s*c\.Raggio', L('Il calcolo', 'The calculation'))], '', "type Cerchio struct {\n    Raggio float64\n}\n\nfunc (c Cerchio) Area() float64 {\n    return math.Pi * c.Raggio * c.Raggio\n}"), [
            dMatch(pMatch(), [['struct', L('Insieme di campi', 'Set of fields')], ['interface', L('Elenco di metodi', 'List of methods')], ['func (r T)', L('Metodo con ricevitore', 'Method with receiver')], ['*T', L('Puntatore: può modificare', 'Pointer: can modify')], [L('Maiuscola', 'Capital letter'), L('Esportato', 'Exported')]], L('Tipi in Go.', 'Types in Go.')),
            dFlash(pFlash(), [[L('Go non ha classi', 'Go has no classes'), true], [L('Bisogna scrivere implements per soddisfare un’interfaccia', 'You must write implements to satisfy an interface'), false], [L('I nomi in minuscolo sono privati al pacchetto', 'Lowercase names are package-private'), true], [L('Le struct non possono avere metodi', 'Structs cannot have methods'), false]], L('Semplice e implicito.', 'Simple and implicit.')),
        ]),
        lesson('concurrency', L('Goroutine e canali', 'Goroutines and channels'), [
            P('La **concorrenza** è il punto forte di Go. Una **goroutine** è una funzione che gira in modo concorrente, leggerissima: se ne possono avviare migliaia con `go funzione()`. I **canali** (`chan`) servono a comunicare tra goroutine in modo sicuro.', '**Concurrency** is Go’s strong point. A **goroutine** is a very lightweight function running concurrently: you can start thousands with `go function()`. **Channels** (`chan`) let goroutines communicate safely.'),
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
            P('`ch <- v` invia, `<-ch` riceve (e aspetta). Per aspettare un gruppo di goroutine si usa `sync.WaitGroup` (da Go 1.25 con il comodo `wg.Go(f)`). Il motto di Go: “non comunicare condividendo memoria; condividi memoria comunicando”.', '`ch <- v` sends, `<-ch` receives (and waits). To wait for a group of goroutines you use `sync.WaitGroup` (since Go 1.25 with the handy `wg.Go(f)`). Go’s motto: “do not communicate by sharing memory; share memory by communicating”.'),
            Fig(L(fig_gobase_chan(['goroutine', 'il canale', 'riceve uno alla volta', 'Chi invia aspetta chi riceve: il canale sincronizza e trasporta.']), fig_gobase_chan(['goroutines', 'the channel', 'receives one at a time', 'The sender waits for the receiver: the channel synchronises and carries.'])),
                L('A sinistra due goroutine, go scarica(go.dev) e go scarica(example.com). Entrambe mandano un messaggio in un canale chan string, disegnato come un tubo, che porta a main, dove <-risultati li riceve uno alla volta.', 'On the left two goroutines, go scarica(go.dev) and go scarica(example.com). Both send a message into a chan string channel, drawn as a pipe, leading to main, where <-risultati receives them one at a time.'),
                L('Più goroutine, un canale, un solo punto che raccoglie.', 'Several goroutines, one channel, a single point that collects.')),
        ], [
            Q('g1', L('Come si avvia una goroutine?', 'How do you start a goroutine?'), ['go funzione()', 'async funzione()', 'thread funzione()', 'run funzione()'], 0, L('Leggerissime.', 'Very lightweight.')),
            Q('g2', L('Che cosa fa `<-ch`?', 'What does `<-ch` do?'), [L('Riceve dal canale, aspettando', 'Receives from the channel, waiting'), L('Chiude il canale', 'Closes the channel'), L('Invia al canale', 'Sends to the channel'), L('Cancella il canale', 'Deletes the channel')], 0, L('`ch <- v` invia.', '`ch <- v` sends.')),
            Q('g3', L('Che cosa serve per aspettare un gruppo di goroutine?', 'What is used to wait for a group of goroutines?'), ['sync.WaitGroup', 'time.Sleep sempre', 'fmt.Wait', 'os.Exit'], 0, L('Add, Done, Wait.', 'Add, Done, Wait.')),
        ], task('go', L('Crea un canale di interi `ch` con `make`, avvia una goroutine che invia `42` nel canale e stampa il valore ricevuto.', 'Create an int channel `ch` with `make`, start a goroutine sending `42` into it and print the received value.'), "package main\n\nimport \"fmt\"\n\nfunc main() {\n    \n}", [re('ch\s*:=\s*make\s*\(\s*chan\s+int\s*\)', L('Il canale', 'The channel')), re('go\s+func\s*\(\s*\)\s*\{\s*ch\s*<-\s*42\s*\}\s*\(\s*\)', L('La goroutine invia 42', 'The goroutine sends 42')), re('fmt\.Println\s*\(\s*<-\s*ch\s*\)', L('Stampi il valore ricevuto', 'You print the received value'))], '', "ch := make(chan int)\n    go func() { ch <- 42 }()\n    fmt.Println(<-ch)"), [
            dMatch(pMatch(), [['go f()', L('Avvia una goroutine', 'Starts a goroutine')], ['make(chan T)', L('Crea un canale', 'Creates a channel')], ['ch <- v', L('Invia', 'Sends')], ['<-ch', L('Riceve', 'Receives')], ['WaitGroup', L('Aspetta un gruppo', 'Waits for a group')]], L('Concorrenza in Go.', 'Concurrency in Go.')),
            dFlash(pFlash(), [[L('Si possono avviare migliaia di goroutine', 'You can start thousands of goroutines'), true], [L('Ricevere da un canale non aspetta mai', 'Receiving from a channel never waits'), false], [L('I canali permettono di comunicare tra goroutine', 'Channels let goroutines communicate'), true], [L('Docker è scritto in PHP', 'Docker is written in PHP'), false]], L('Parallelo, ma ordinato.', 'Concurrent, but orderly.')),
        ]),
        lesson('http', L('Un server HTTP e i test', 'An HTTP server and tests'), [
            P('La libreria standard di Go basta per un server web: `net/http` gestisce le rotte, `encoding/json` converte le struct in JSON. I **tag** delle struct (`json:"nome"`) decidono i nomi dei campi.', 'Go’s standard library is enough for a web server: `net/http` handles routes, `encoding/json` turns structs into JSON. Struct **tags** (`json:"name"`) decide the field names.'),
            C('type Corso struct {
    ID    int    `json:"id"`
    Titolo string `json:"titolo"`
}

func corsi(w http.ResponseWriter, r *http.Request) {
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode([]Corso{{1, "Go base"}})
}

func main() {
    http.HandleFunc("GET /api/corsi", corsi)
    log.Fatal(http.ListenAndServe(":8080", nil))
}', 'go'),
            P('I test stanno in file che finiscono con `_test.go`, in funzioni `TestXxx(t *testing.T)`, e si lanciano con `go test ./...`. In Go si usano spesso i **test a tabella**: una lista di casi con input e risultato atteso.', 'Tests live in files ending in `_test.go`, in `TestXxx(t *testing.T)` functions, and run with `go test ./...`. Go often uses **table tests**: a list of cases with input and expected result.'),
        ], [
            Q('gh1', L('Quale pacchetto avvia un server web?', 'Which package starts a web server?'), ['net/http', 'fmt', 'os', 'strings'], 0, L('Con `ListenAndServe`.', 'With `ListenAndServe`.')),
            Q('gh2', L('A che cosa serve `json:"titolo"`?', 'What is `json:"titolo"` for?'), [L('Dà il nome al campo nel JSON', 'Names the field in the JSON'), L('Commenta il campo', 'Comments the field'), L('Rende il campo privato', 'Makes the field private'), L('Lo cifra', 'Encrypts it')], 0, L('È un tag della struct.', 'It is a struct tag.')),
            Q('gh3', L('Come lanci tutti i test?', 'How do you run all tests?'), ['go test ./...', 'go run test', 'go build -t', 'go vet'], 0, L('I file finiscono in `_test.go`.', 'Files end in `_test.go`.')),
        ], task('go', L('Scrivi `func TestSomma(t *testing.T)` che chiama `t.Errorf("errore")` se `Somma(2, 3) != 5`.', 'Write `func TestSomma(t *testing.T)` calling `t.Errorf("errore")` if `Somma(2, 3) != 5`.'), '', [re('func\s+TestSomma\(\s*t\s+\*testing\.T\s*\)', L('La funzione di test esiste', 'The test function exists')), re('if\s+Somma\(\s*2\s*,\s*3\s*\)\s*!=\s*5\s*\{\s*t\.Errorf\(', L('Controlli il risultato', 'You check the result'))], '', "func TestSomma(t *testing.T) {\n    if Somma(2, 3) != 5 {\n        t.Errorf(\"errore\")\n    }\n}"), [
            dMatch(pMatch(), [['net/http', L('Server web', 'Web server')], ['encoding/json', L('Conversione JSON', 'JSON conversion')], ['_test.go', L('File di test', 'Test file')], ['t.Errorf', L('Segnala un errore', 'Reports a failure')], ['go test ./...', L('Lancia i test', 'Runs the tests')]], L('Web e test in Go.', 'Web and tests in Go.')),
            dFlash(pFlash(), [[L('La libreria standard basta per un server', 'The standard library is enough for a server'), true], [L('I test di Go stanno in file .spec.go', 'Go tests live in .spec.go files'), false], [L('I test a tabella elencano più casi', 'Table tests list several cases'), true], [L('I tag json sono commenti ignorati', 'json tags are ignored comments'), false]], L('Go pratico.', 'Practical Go.')),
        ]),
        lesson('control-flow', L('if, for e switch', 'if, for and switch'), [
            P('Go ha **un solo ciclo**, `for`, che fa anche da `while` e da ciclo infinito (`for { }`). Da Go 1.22 `for i := range 3` conta da 0 a 2. `if` e `switch` possono iniziare con una breve dichiarazione, e le condizioni non vanno tra parentesi tonde.', 'Go has **a single loop**, `for`, which also acts as `while` and as an endless loop (`for { }`). Since Go 1.22 `for i := range 3` counts from 0 to 2. `if` and `switch` can start with a short declaration, and conditions take no round brackets.'),
            C(L('for i := 0; i < 3; i++ {
    fmt.Println("giro", i)
}
for i := range 3 {                 // 0, 1, 2
    fmt.Println(i)
}
n := 10
for n > 0 {                        // il "while" di Go
    n -= 3
}
if eta := 20; eta >= 18 {
    fmt.Println("adulto")
}
switch giorno := "sab"; giorno {
case "sab", "dom":
    fmt.Println("weekend")
default:
    fmt.Println("lavoro")
}', 'for i := 0; i < 3; i++ {
    fmt.Println("round", i)
}
for i := range 3 {                 // 0, 1, 2
    fmt.Println(i)
}
n := 10
for n > 0 {                        // il "while" di Go
    n -= 3
}
if eta := 20; eta >= 18 {
    fmt.Println("adult")
}
switch giorno := "sab"; giorno {
case "sab", "dom":
    fmt.Println("weekend")
default:
    fmt.Println("work")
}'), 'go', L('<pre>giro 0
giro 1
giro 2
0
1
2
adulto
weekend</pre>', '<pre>round 0
round 1
round 2
0
1
2
adult
weekend</pre>')),
            P('Nello `switch` i casi non «cadono» nel successivo: niente `break` alla fine di ogni caso. Se vuoi davvero passare al caso dopo, scrivi `fallthrough`.', 'In a `switch` cases do not “fall through” to the next one: no `break` at the end of each case. If you really want to move on to the next case, write `fallthrough`.'),
        ], [
            Q('cf1', L('Come si scrive un “while” in Go?', 'How do you write a “while” in Go?'), ['while n > 0 { }', 'for n > 0 { }', 'loop n > 0 { }', 'do { } while n > 0'], 1, L('`for` con la sola condizione.', '`for` with just the condition.')),
            Q('cf2', L('Quali numeri produce `for i := range 3`?', 'Which numbers does `for i := range 3` produce?'), ['1, 2, 3', '0, 1, 2, 3', '0, 1, 2', '3'], 2, L('Si parte da 0 e ci si ferma prima di 3.', 'It starts at 0 and stops before 3.')),
            Q('cf3', L('Uno `switch` di Go passa al caso successivo dopo averne eseguito uno?', 'Does a Go `switch` move on to the next case after running one?'), [L('Sì, sempre', 'Yes, always'), L('Solo se manca default', 'Only if default is missing'), L('Solo con le stringhe', 'Only with strings'), L('No, a meno di scrivere fallthrough', 'No, unless you write fallthrough')], 3, L('Il contrario di C e Java.', 'The opposite of C and Java.')),
        ], task('go', L('Con un ciclo `for` stampa i numeri da 1 a 5 con `fmt.Println`.', 'With a `for` loop print the numbers from 1 to 5 with `fmt.Println`.'), "package main\n\nimport \"fmt\"\n\nfunc main() {\n    \n}", [re('for\s+(\w+)\s*:=\s*1\s*;\s*\1\s*(<=\s*5|<\s*6)\s*;\s*\1\+\+\s*\{', L('Il ciclo va da 1 a 5', 'The loop goes from 1 to 5')), re('fmt\.Println\s*\(\s*\w+\s*\)', L('Stampi il numero', 'You print the number'))], '', "for i := 1; i <= 5; i++ {\n        fmt.Println(i)\n    }"), [
            dBlanks(pFill(), 'for i := ___ 4 {
    if i%2 == 0 {
        ___
    }
    fmt.Println(i)
}
___ {
    break
}', ['range', 'continue', 'for'], ['in', 'next', 'while'], L('Range, salta il giro, ciclo infinito.', 'Range, skip the round, endless loop.')),
            dFlash(pFlash(), [[L('Go ha anche il ciclo `while`', 'Go also has a `while` loop'), false], [L('`for { }` è un ciclo infinito', '`for { }` is an endless loop'), true], [L('Nello `switch` di Go non serve `break`', 'Go’s `switch` needs no `break`'), true], [L('Le parentesi tonde attorno alla condizione di `if` sono obbligatorie', 'Round brackets around an `if` condition are required'), false]], L('Un ciclo solo, condizioni senza parentesi.', 'One loop, conditions without brackets.')),
        ]),
        lesson('slices-maps', L('Slice e mappe', 'Slices and maps'), [
            P('Una **slice** è una sequenza che può crescere: `append` aggiunge in fondo (e il risultato va riassegnato), `s[a:b]` ne prende un pezzo, `len` dice quanti elementi ha. Una **mappa** associa chiavi e valori. I pacchetti `slices` e `maps` offrono ordinamenti e ricerche pronti.', 'A **slice** is a sequence that can grow: `append` adds at the end (and the result must be reassigned), `s[a:b]` takes a piece, `len` tells how many items it has. A **map** ties keys to values. The `slices` and `maps` packages offer ready-made sorting and searching.'),
            C('voti := []int{7, 9, 6}
voti = append(voti, 8)
fmt.Println(len(voti), voti[1:3])     // 4 [9 6]
slices.Sort(voti)                     // [6 7 8 9]

for i, v := range voti {
    fmt.Println(i, v)
}

eta := map[string]int{"Ada": 36}
eta["Bea"] = 28
if e, ok := eta["Cleo"]; !ok {
    fmt.Println("Cleo non c’è, e vale", e)   // e vale 0
}
delete(eta, "Ada")', 'go'),
            P('Leggere una chiave assente non dà errore ma lo **zero value** del tipo (0, "", false): per sapere se la chiave c’era si usa la forma `v, ok := m[k]`. L’ordine in cui `range` visita una mappa non è garantito.', 'Reading a missing key gives no error but the type’s **zero value** (0, "", false): to know whether the key was there you use the `v, ok := m[k]` form. The order in which `range` visits a map is not guaranteed.'),
            Fig(L(fig_gobase_slice(['due slice, un solo array:', 'cambiare voti[1] cambia anche l’altra', 'capacità libera']), fig_gobase_slice(['two slices, one array:', 'changing voti[1] changes the other too', 'spare capacity'])),
                L('La slice voti ha lunghezza 4 e capacità 6 e punta a un array con 6, 7, 8, 9 e due caselle libere. La slice voti[1:3] ha lunghezza 2 e capacità 5 e punta alle caselle 1 e 2 dello stesso array, evidenziate in arancione.', 'The slice voti has length 4 and capacity 6 and points to an array holding 6, 7, 8, 9 and two free cells. The slice voti[1:3] has length 2 and capacity 5 and points to cells 1 and 2 of the same array, highlighted in orange.'),
                L('Una slice è una finestra su un array: più finestre possono guardare lo stesso.', 'A slice is a window on an array: several windows can look at the same one.')),
        ], [
            Q('sm1', L('Come aggiungi 5 alla slice `s`?', 'How do you add 5 to the slice `s`?'), ['s.push(5)', 'append(s, 5)', 's = append(s, 5)', 's += 5'], 2, L('`append` restituisce la nuova slice.', '`append` returns the new slice.')),
            Q('sm2', L('Che cosa dà `m["x"]` su una `map[string]int` senza la chiave "x"?', 'What does `m["x"]` give on a `map[string]int` without the key "x"?'), ['nil', '0', L('Un panic', 'A panic'), '-1'], 1, L('Lo zero value di `int`.', 'The zero value of `int`.')),
            Q('sm3', L('Che cosa contiene `ok` in `v, ok := m[k]`?', 'What does `ok` hold in `v, ok := m[k]`?'), [L('Se la chiave esiste', 'Whether the key exists'), L('Il numero di chiavi', 'The number of keys'), L('Un errore', 'An error'), L('La chiave stessa', 'The key itself')], 0, L('Un booleano.', 'A boolean.')),
        ], task('go', L('Crea la slice `nomi` con `"Ada"` e `"Bea"`, aggiungi `"Cleo"` con `append` e stampa `len(nomi)`.', 'Create the slice `nomi` with `"Ada"` and `"Bea"`, add `"Cleo"` with `append` and print `len(nomi)`.'), "package main\n\nimport \"fmt\"\n\nfunc main() {\n    \n}", [re('nomi\s*:=\s*\[\]string\s*\{\s*"Ada"\s*,\s*"Bea"\s*\}', L('Crei la slice', 'You create the slice')), re('nomi\s*=\s*append\s*\(\s*nomi\s*,\s*"Cleo"\s*\)', L('Aggiungi Cleo', 'You add Cleo')), re('fmt\.Println\s*\(\s*len\s*\(\s*nomi\s*\)\s*\)', L('Stampi la lunghezza', 'You print the length'))], '', "nomi := []string{\"Ada\", \"Bea\"}\n    nomi = append(nomi, \"Cleo\")\n    fmt.Println(len(nomi))"), [
            dBug(pBug(), ['voti := []int{6, 7}', 'append(voti, 8)', 'fmt.Println(voti)'], 1, 'voti = append(voti, 8)', L('`append` restituisce la nuova slice: va riassegnata, altrimenti il compilatore segnala un valore non usato.', '`append` returns the new slice: it must be reassigned, otherwise the compiler reports an unused value.')),
            dMatch(pMatch(), [['[]int{}', L('Slice di interi', 'Slice of ints')], ['append', L('Aggiunge in fondo', 'Appends')], ['map[string]int', L('Da testo a intero', 'Text to int')], ['delete', L('Toglie una chiave', 'Removes a key')], ['len', L('Quanti elementi', 'How many items')]], L('Le collezioni di Go.', 'Go’s collections.')),
        ]),
        lesson('pointers', L('Puntatori', 'Pointers'), [
            P('In Go gli argomenti si passano **per copia**: una funzione che riceve una struct ne modifica solo la copia. Un **puntatore** (`*T`) contiene l’indirizzo di un valore: `&x` lo prende, `*p` legge o scrive il valore puntato.', 'In Go arguments are passed **by copy**: a function receiving a struct changes only its copy. A **pointer** (`*T`) holds the address of a value: `&x` takes it, `*p` reads or writes the value it points to.'),
            C('type Contatore struct{ Valore int }

func incrementaCopia(c Contatore) { c.Valore++ }
func incrementa(c *Contatore)     { c.Valore++ }

func main() {
    c := Contatore{}
    incrementaCopia(c)
    fmt.Println(c.Valore)      // 0: è cambiata solo la copia
    incrementa(&c)
    fmt.Println(c.Valore)      // 1

    x := 10
    p := &x                    // p punta a x
    *p = 20                    // cambia x attraverso p
    fmt.Println(x, p == nil)   // 20 false
}', 'go', '<pre>0
1
20 false</pre>'),
            P('Un puntatore non inizializzato vale `nil`: leggerlo provoca un *panic*. In Go non esiste l’aritmetica dei puntatori, quindi sono semplici e sicuri. Con le struct Go è comodo: `c.Valore` funziona anche se `c` è un puntatore.', 'An uninitialised pointer is `nil`: reading through it causes a *panic*. Go has no pointer arithmetic, so pointers are simple and safe. With structs Go is handy: `c.Valore` works even when `c` is a pointer.'),
            Fig(L(fig_gobase_ptr(['copia: main non vede la modifica', 'indirizzo: cambia l’originale']), fig_gobase_ptr(['copy: main does not see the change', 'address: changes the original'])),
                L('In alto main con c.Valore = 1. In basso a sinistra incrementaCopia(c) riceve una copia tratteggiata della struct; in basso a destra incrementa(&c) riceve un puntatore, con una freccia che torna alla c di main.', 'At the top main with c.Valore = 1. At the bottom left incrementaCopia(c) receives a dashed copy of the struct; at the bottom right incrementa(&c) receives a pointer, with an arrow leading back to main’s c.'),
                L('Con `&c` la funzione lavora sull’originale, non su una copia.', 'With `&c` the function works on the original, not on a copy.')),
        ], [
            Q('pt1', L('Che cosa fa `&x`?', 'What does `&x` do?'), [L('Prende l’indirizzo di x', 'Takes the address of x'), L('Copia x', 'Copies x'), L('Confronta x con zero', 'Compares x with zero'), L('Cancella x', 'Deletes x')], 0, L('Il risultato è di tipo `*int` se x è un int.', 'The result is of type `*int` if x is an int.')),
            Q('pt2', L('Perché `incrementaCopia(c)` non cambia `c`?', 'Why does `incrementaCopia(c)` not change `c`?'), [L('Perché Valore è privato', 'Because Valore is private'), L('Perché la funzione riceve una copia della struct', 'Because the function receives a copy of the struct'), L('Perché manca return', 'Because return is missing'), L('Perché Go non permette di modificare le struct', 'Because Go does not allow changing structs')], 1, L('Serve un puntatore per cambiare l’originale.', 'A pointer is needed to change the original.')),
            Q('pt3', L('Che cosa succede leggendo `*p` se `p` è `nil`?', 'What happens reading `*p` if `p` is `nil`?'), [L('Restituisce 0', 'It returns 0'), L('Restituisce nil', 'It returns nil'), L('Il programma va in panic', 'The program panics'), L('Niente, viene ignorato', 'Nothing, it is ignored')], 2, L('Controlla sempre i puntatori che potrebbero essere nil.', 'Always check pointers that might be nil.')),
        ], task('go', L('Scrivi `func azzera(n *int)` che mette a 0 il valore puntato da `n`.', 'Write `func azzera(n *int)` that sets the value pointed to by `n` to 0.'), '', [re('func\s+azzera\s*\(\s*n\s+\*int\s*\)\s*\{', L('La funzione riceve un puntatore', 'The function takes a pointer')), re('\*n\s*=\s*0', L('Azzeri il valore puntato', 'You zero the pointed value'))], '', "func azzera(n *int) {\n    *n = 0\n}"), [
            dBlanks(pFill(), 'x := 5
p := ___x
___p = 8
fmt.Println(x) // 8
var q *int
fmt.Println(q == ___)', ['&', '*', 'nil'], ['@', '$', 'null'], L('Indirizzo, valore puntato, puntatore vuoto.', 'Address, pointed value, empty pointer.')),
            dFlash(pFlash(), [[L('In Go esiste l’aritmetica dei puntatori', 'Go has pointer arithmetic'), false], [L('`&x` dà l’indirizzo di x', '`&x` gives the address of x'), true], [L('Gli argomenti di una funzione sono copie', 'Function arguments are copies'), true], [L('Un puntatore non inizializzato vale `nil`', 'An uninitialised pointer is `nil`'), true]], L('Copie di serie, puntatori per modificare.', 'Copies by default, pointers to modify.')),
        ]),
        lesson('modules', L('Moduli e pacchetti', 'Modules and packages'), [
            P('Un **modulo** è un progetto con il file `go.mod`, che ne dichiara il percorso, la versione di Go e le dipendenze; `go.sum` conserva le impronte dei pacchetti scaricati. Ogni cartella è un **pacchetto**, che si importa con il percorso del modulo più la cartella.', 'A **module** is a project with a `go.mod` file, which declares its path, the Go version and the dependencies; `go.sum` keeps the checksums of downloaded packages. Each folder is a **package**, imported with the module path plus the folder.'),
            C('go mod init github.com/ada/meteo
go get github.com/google/uuid@latest
go mod tidy
go run .', 'bash'),
            C('// temperatura/converti.go
package temperatura

// InFahrenheit è esportata: inizia con la maiuscola.
func InFahrenheit(c float64) float64 {
    return c*9/5 + 32
}

// main.go
package main

import (
    "fmt"

    "github.com/ada/meteo/temperatura"
)

func main() {
    fmt.Println(temperatura.InFahrenheit(20)) // 68
}', 'go'),
            P('`go mod tidy` aggiunge le dipendenze mancanti e toglie quelle inutili. Solo i nomi con la maiuscola sono visibili da un altro pacchetto.', '`go mod tidy` adds missing dependencies and removes unused ones. Only capitalised names are visible from another package.'),
            P('Le versioni seguono il **versionamento semantico**: `v1.4.2` significa versione maggiore 1, minore 4, correzione 2, e dentro la stessa versione maggiore un aggiornamento non dovrebbe rompere il tuo codice. Sia `go.mod` sia `go.sum` vanno su git: `go.sum` garantisce che chiunque scarichi esattamente gli stessi file. Una cartella chiamata `internal/` è speciale: i suoi pacchetti si possono importare solo dall’interno del modulo.', 'Versions follow **semantic versioning**: `v1.4.2` means major version 1, minor 4, patch 2, and within the same major version an update should not break your code. Both `go.mod` and `go.sum` go into git: `go.sum` guarantees that everyone downloads exactly the same files. A folder named `internal/` is special: its packages can be imported only from inside the module.'),
        ], [
            Q('md1', L('Quale comando crea il file `go.mod`?', 'Which command creates the `go.mod` file?'), ['go new', 'go mod init', 'go init mod', 'go create'], 1, L('Seguito dal percorso del modulo.', 'Followed by the module path.')),
            Q('md2', L('Che cosa fa `go mod tidy`?', 'What does `go mod tidy` do?'), [L('Formatta il codice', 'Formats the code'), L('Esegue i test', 'Runs the tests'), L('Cancella la cartella vendor', 'Deletes the vendor folder'), L('Aggiunge le dipendenze mancanti e toglie quelle inutili', 'Adds missing dependencies and removes unused ones')], 3, L('Tiene in ordine `go.mod` e `go.sum`.', 'It keeps `go.mod` and `go.sum` tidy.')),
            Q('md3', L('Come chiami da `main` la funzione `InFahrenheit` del pacchetto `temperatura`?', 'How do you call the `InFahrenheit` function of the `temperatura` package from `main`?'), ['InFahrenheit(20)', 'temperatura::InFahrenheit(20)', 'temperatura.InFahrenheit(20)', 'import.temperatura.InFahrenheit(20)'], 2, L('Nome del pacchetto, punto, nome esportato.', 'Package name, dot, exported name.')),
        ], task('go', L('Importa il pacchetto `github.com/ada/meteo/temperatura` e stampa `temperatura.InFahrenheit(30)` con `fmt.Println`.', 'Import the package `github.com/ada/meteo/temperatura` and print `temperatura.InFahrenheit(30)` with `fmt.Println`.'), "package main\n\nimport \"fmt\"\n\nfunc main() {\n    \n}", [re('import\s*\([^)]*"github\.com/ada/meteo/temperatura"[^)]*\)|import\s+"github\.com/ada/meteo/temperatura"', L('Importi il pacchetto', 'You import the package')), re('fmt\.Println\s*\(\s*temperatura\.InFahrenheit\s*\(\s*30\s*\)\s*\)', L('Chiami la funzione', 'You call the function'))], '', "package main\n\nimport (\n    \"fmt\"\n\n    \"github.com/ada/meteo/temperatura\"\n)\n\nfunc main() {\n    fmt.Println(temperatura.InFahrenheit(30))\n}"), [
            dOrder(pSort(), ['go mod init github.com/ada/app', 'go get github.com/google/uuid', 'go mod tidy', 'go build'], L('Crea il modulo, aggiungi, riordina, compila.', 'Create the module, add, tidy, build.')),
            dMatch(pMatch(), [['go.mod', L('Percorso e dipendenze', 'Path and dependencies')], ['go.sum', L('Impronte dei pacchetti scaricati', 'Checksums of downloaded packages')], ['go get', L('Aggiunge una dipendenza', 'Adds a dependency')], ['go mod tidy', L('Riordina le dipendenze', 'Tidies the dependencies')], ['package', L('Una cartella di codice', 'A folder of code')]], L('Organizzare un progetto Go.', 'Organising a Go project.')),
        ]),
    ],
];
