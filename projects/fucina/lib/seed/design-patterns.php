<?php
/** Design patterns and clean code: principles (SOLID, DRY), creational, structural and behavioural patterns, dependency injection
 *  and refactoring, composition (Composite, Proxy), state machines, middleware chains, repositories and hexagonal architecture. */

return [
    'id' => 'design-patterns', 'family' => 'patterns', 'after' => null,
    'title' => L('Design pattern e codice pulito', 'Design Patterns and Clean Code'), 'short' => L('Design pattern', 'Design Patterns'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Scrivere codice facile da cambiare: principi SOLID e DRY, pattern creazionali, strutturali e comportamentali, composizione, macchine a stati, middleware e architettura a strati, con esempi in TypeScript.', 'Writing code that is easy to change: SOLID and DRY principles, creational, structural and behavioural patterns, composition, state machines, middleware and layered architecture, with TypeScript examples.'),
    'lessons' => [
        lesson('principles', L('Principi del codice pulito', 'Clean code principles'), [
            P('Il codice si legge molte più volte di quante si scriva. Principi utili: **nomi chiari** che dicono cosa fa una cosa; **funzioni piccole** che fanno una cosa sola; **DRY** (Don’t Repeat Yourself: non duplicare la logica); **KISS** (Keep It Simple: la soluzione più semplice che funziona).', 'Code is read far more often than it is written. Useful principles: **clear names** saying what something does; **small functions** doing one thing; **DRY** (Don’t Repeat Yourself: do not duplicate logic); **KISS** (Keep It Simple: the simplest solution that works).'),
            P('I principi **SOLID** guidano la progettazione a oggetti: **S**ingola responsabilità (una classe, un motivo per cambiare), **O**perta all’estensione e chiusa alla modifica, sostituzione di **L**iskov, **I**nterfacce piccole e specifiche, **D**ipendere da astrazioni, non da dettagli concreti.', 'The **SOLID** principles guide object design: **S**ingle responsibility (one class, one reason to change), **O**pen for extension and closed for modification, **L**iskov substitution, **I**nterface segregation (small, specific interfaces), **D**ependency inversion (depend on abstractions, not concrete details).'),
            C('// ✗ fa troppo: calcola, salva e invia email
function gestisciOrdine(o) { /* 120 righe */ }

// ✓ una responsabilità per funzione
const totale = calcolaTotale(o.righe);
await ordini.salva({ ...o, totale });
await notifiche.confermaOrdine(o.cliente);', 'ts'),
        ], [
            Q('p1', L('Che cosa dice il principio DRY?', 'What does the DRY principle say?'), [L('Non duplicare la logica', 'Do not duplicate logic'), L('Scrivi meno commenti', 'Write fewer comments'), L('Usa solo variabili globali', 'Use only global variables'), L('Non testare', 'Do not test')], 0, L('Don’t Repeat Yourself.', 'Don’t Repeat Yourself.')),
            Q('p2', L('Che cosa significa la S di SOLID?', 'What does the S in SOLID mean?'), [L('Singola responsabilità', 'Single responsibility'), L('Sicurezza', 'Security'), L('Semplicità', 'Simplicity'), L('Sintassi', 'Syntax')], 0, L('Un solo motivo per cambiare.', 'One reason to change.')),
            Q('p3', L('Perché i nomi chiari sono importanti?', 'Why do clear names matter?'), [L('Il codice si legge molte più volte di quante si scriva', 'Code is read far more often than written'), L('Occupano meno spazio', 'They take less space'), L('Il compilatore lo richiede', 'The compiler requires it'), L('Non lo sono', 'They do not')], 0, L('Leggibilità prima di tutto.', 'Readability first.')),
        ], task('ts', L('Rinomina la funzione `f(a, b)` che calcola il prezzo scontato in `prezzoScontato(prezzo, sconto)` mantenendo `return prezzo * (1 - sconto)`.', 'Rename the function `f(a, b)` computing the discounted price to `prezzoScontato(prezzo, sconto)` keeping `return prezzo * (1 - sconto)`.'), "function f(a: number, b: number): number {\n  return a * (1 - b);\n}", [re('function\s+prezzoScontato\s*\(\s*prezzo\s*:\s*number\s*,\s*sconto\s*:\s*number\s*\)', L('Nomi chiari', 'Clear names')), re('return\s+prezzo\s*\*\s*\(\s*1\s*-\s*sconto\s*\)', L('Stessa logica', 'Same logic'))], '', "function prezzoScontato(prezzo: number, sconto: number): number {\n  return prezzo * (1 - sconto);\n}"), [
            dMatch(pMatch(), [['DRY', L('Non ripetere', 'Do not repeat')], ['KISS', L('Mantienilo semplice', 'Keep it simple')], ['S', L('Singola responsabilità', 'Single responsibility')], ['O', L('Aperto all’estensione', 'Open for extension')], ['D', L('Dipendi da astrazioni', 'Depend on abstractions')]], L('I principi.', 'The principles.')),
            dFlash(pFlash(), [[L('Una funzione dovrebbe fare una cosa sola', 'A function should do one thing'), true], [L('Copiare la stessa logica in tre posti è DRY', 'Copying the same logic in three places is DRY'), false], [L('Nomi come x1 e tmp2 aiutano la lettura', 'Names like x1 and tmp2 help reading'), false], [L('SOLID riguarda la progettazione a oggetti', 'SOLID is about object design'), true]], L('Codice che si capisce.', 'Code that makes sense.')),
        ]),
        lesson('creational', L('Pattern creazionali', 'Creational patterns'), [
            P('I **design pattern** sono soluzioni collaudate a problemi ricorrenti, con un nome condiviso. I pattern **creazionali** riguardano come si creano gli oggetti. Una **Factory** decide quale classe istanziare in base a un parametro; un **Builder** costruisce oggetti complessi passo dopo passo; un **Singleton** garantisce un’unica istanza.', '**Design patterns** are proven solutions to recurring problems, with a shared name. **Creational** patterns are about how objects are created. A **Factory** decides which class to instantiate based on a parameter; a **Builder** constructs complex objects step by step; a **Singleton** guarantees a single instance.'),
            C('interface Notifica { invia(testo: string): Promise<void>; }
class Email implements Notifica { async invia(t: string) { /* … */ } }
class Sms implements Notifica { async invia(t: string) { /* … */ } }

function creaNotifica(tipo: "email" | "sms"): Notifica {   // Factory
  return tipo === "email" ? new Email() : new Sms();
}

const richiesta = new RichiestaHttp("https://api.esempio.it")  // Builder
  .metodo("POST").header("Accept", "application/json").corpo({ id: 7 }).costruisci();', 'ts'),
            P('Il Singleton va usato con parsimonia: è uno stato globale e rende il codice difficile da testare. Spesso è meglio creare un oggetto una volta e **passarlo** a chi ne ha bisogno (dependency injection).', 'Use the Singleton sparingly: it is global state and makes code hard to test. It is often better to create an object once and **pass it** to whoever needs it (dependency injection).'),
        ], [
            Q('c1', L('Che cosa fa una Factory?', 'What does a Factory do?'), [L('Decide quale classe istanziare', 'Decides which class to instantiate'), L('Cancella oggetti', 'Deletes objects'), L('Ordina un elenco', 'Sorts a list'), L('Stampa log', 'Prints logs')], 0, L('Il chiamante non deve saperlo.', 'The caller need not know.')),
            Q('c2', L('Quando è utile un Builder?', 'When is a Builder useful?'), [L('Per oggetti complessi con molte opzioni', 'For complex objects with many options'), L('Per numeri interi', 'For integers'), L('Mai', 'Never'), L('Per cancellare dati', 'For deleting data')], 0, L('Passo dopo passo.', 'Step by step.')),
            Q('c3', L('Perché usare il Singleton con parsimonia?', 'Why use the Singleton sparingly?'), [L('È stato globale e complica i test', 'It is global state and complicates tests'), L('È troppo veloce', 'It is too fast'), L('Non esiste in TypeScript', 'It does not exist in TypeScript'), L('Consuma troppa memoria', 'It uses too much memory')], 0, L('Meglio passare le dipendenze.', 'Better to pass dependencies.')),
        ], task('ts', L('Scrivi la factory `creaForma(tipo: "cerchio" | "quadrato")` che restituisce `new Cerchio()` o `new Quadrato()`.', 'Write the factory `creaForma(tipo: "cerchio" | "quadrato")` returning `new Cerchio()` or `new Quadrato()`.'), '', [re('function\s+creaForma\s*\(\s*tipo\s*:\s*"cerchio"\s*\|\s*"quadrato"\s*\)', L('La firma', 'The signature')), re('new\s+Cerchio\s*\(\s*\)', L('Crea un cerchio', 'It creates a circle')), re('new\s+Quadrato\s*\(\s*\)', L('Crea un quadrato', 'It creates a square'))], '', "function creaForma(tipo: \"cerchio\" | \"quadrato\"): Forma {\n  return tipo === \"cerchio\" ? new Cerchio() : new Quadrato();\n}"), [
            dMatch(pMatch(), [['Factory', L('Sceglie la classe da creare', 'Chooses the class to create')], ['Builder', L('Costruisce passo dopo passo', 'Builds step by step')], ['Singleton', L('Un’unica istanza', 'A single instance')], ['Dependency injection', L('Passare le dipendenze', 'Passing dependencies')]], L('Creare oggetti.', 'Creating objects.')),
            dFlash(pFlash(), [[L('I pattern sono soluzioni a problemi ricorrenti', 'Patterns are solutions to recurring problems'), true], [L('Il Singleton rende i test più facili', 'The Singleton makes tests easier'), false], [L('Una Factory nasconde quale classe viene creata', 'A Factory hides which class is created'), true], [L('Bisogna usare tutti i pattern in ogni progetto', 'Every project must use every pattern'), false]], L('Il pattern giusto al momento giusto.', 'The right pattern at the right time.')),
        ]),
        lesson('structural', L('Pattern strutturali', 'Structural patterns'), [
            P('I pattern **strutturali** riguardano come si compongono gli oggetti. Un **Adapter** fa parlare due interfacce incompatibili (per esempio una libreria esterna con il tuo codice). Un **Decorator** aggiunge comportamento a un oggetto avvolgendolo, senza cambiarne la classe. Una **Facade** offre un’interfaccia semplice a un sistema complicato.', '**Structural** patterns are about how objects are composed. An **Adapter** makes two incompatible interfaces talk (for example an external library with your code). A **Decorator** adds behaviour to an object by wrapping it, without changing its class. A **Facade** offers a simple interface to a complicated system.'),
            C('interface Archivio { leggi(chiave: string): Promise<string | null>; }

class ArchivioConCache implements Archivio {           // Decorator
  private cache = new Map<string, string | null>();
  constructor(private interno: Archivio) {}
  async leggi(chiave: string) {
    if (!this.cache.has(chiave)) this.cache.set(chiave, await this.interno.leggi(chiave));
    return this.cache.get(chiave)!;
  }
}

class PagamentiStripeAdapter implements Pagamenti {    // Adapter
  constructor(private stripe: StripeClient) {}
  addebita(euro: number) { return this.stripe.paymentIntents.create({ amount: Math.round(euro * 100), currency: "eur" }); }
}', 'ts'),
            P('Con l’Adapter puoi cambiare fornitore (da Stripe a un altro) toccando una sola classe. Con il Decorator puoi aggiungere cache, log o controlli a qualsiasi `Archivio` senza modificarlo: è il principio aperto/chiuso in pratica.', 'With the Adapter you can switch provider (from Stripe to another) touching a single class. With the Decorator you can add caching, logging or checks to any `Archivio` without modifying it: the open/closed principle in practice.'),
        ], [
            Q('s1', L('Che cosa fa un Adapter?', 'What does an Adapter do?'), [L('Fa parlare interfacce incompatibili', 'Makes incompatible interfaces talk'), L('Cancella una classe', 'Deletes a class'), L('Cifra i dati', 'Encrypts data'), L('Duplica oggetti', 'Duplicates objects')], 0, L('Come una presa da viaggio.', 'Like a travel plug.')),
            Q('s2', L('Come aggiunge comportamento un Decorator?', 'How does a Decorator add behaviour?'), [L('Avvolgendo l’oggetto originale', 'By wrapping the original object'), L('Modificando la classe originale', 'By modifying the original class'), L('Copiando il codice', 'By copying code'), L('Non lo aggiunge', 'It does not')], 0, L('Stessa interfaccia, più funzioni.', 'Same interface, more features.')),
            Q('s3', L('A che cosa serve una Facade?', 'What is a Facade for?'), [L('Un’interfaccia semplice per un sistema complesso', 'A simple interface for a complex system'), L('Una facciata grafica', 'A graphical facade'), L('Un database', 'A database'), L('Un test', 'A test')], 0, L('Nasconde la complessità.', 'It hides complexity.')),
        ], task('ts', L('Scrivi la classe `LoggerArchivio` che implementa `Archivio`, riceve nel costruttore `private interno: Archivio` e in `leggi` scrive `console.log(chiave)` prima di chiamare `this.interno.leggi(chiave)`.', 'Write the class `LoggerArchivio` implementing `Archivio`, taking `private interno: Archivio` in the constructor and in `leggi` logging `console.log(chiave)` before calling `this.interno.leggi(chiave)`.'), '', [re('class\s+LoggerArchivio\s+implements\s+Archivio', L('Implementa l’interfaccia', 'It implements the interface')), re('constructor\s*\(\s*private\s+interno\s*:\s*Archivio\s*\)', L('Avvolge l’originale', 'It wraps the original')), re('console\.log\s*\(\s*chiave\s*\)[\s\S]*return\s+this\.interno\.leggi\s*\(\s*chiave\s*\)', L('Log e poi delega', 'Log then delegate'))], '', "class LoggerArchivio implements Archivio {\n  constructor(private interno: Archivio) {}\n  leggi(chiave: string) {\n    console.log(chiave);\n    return this.interno.leggi(chiave);\n  }\n}"), [
            dMatch(pMatch(), [['Adapter', L('Collega interfacce diverse', 'Connects different interfaces')], ['Decorator', L('Aggiunge comportamento avvolgendo', 'Adds behaviour by wrapping')], ['Facade', L('Semplifica un sistema', 'Simplifies a system')], [L('Aperto/chiuso', 'Open/closed'), L('Estendere senza modificare', 'Extend without modifying')]], L('Comporre oggetti.', 'Composing objects.')),
            dFlash(pFlash(), [[L('Un Adapter aiuta a cambiare fornitore', 'An Adapter helps switch providers'), true], [L('Un Decorator cambia la classe originale', 'A Decorator changes the original class'), false], [L('Una cache può essere un Decorator', 'A cache can be a Decorator'), true], [L('La Facade rende tutto più complicato', 'The Facade makes everything more complicated'), false]], L('Strutture flessibili.', 'Flexible structures.')),
        ]),
        lesson('behavioral', L('Pattern comportamentali', 'Behavioural patterns'), [
            P('I pattern **comportamentali** riguardano come gli oggetti collaborano. **Strategy** incapsula algoritmi intercambiabili dietro la stessa interfaccia (calcolare la spedizione in modi diversi). **Observer** permette a più oggetti di essere avvisati quando qualcosa cambia (gli eventi). **Command** trasforma un’azione in un oggetto, utile per code di lavori e “annulla”.', '**Behavioural** patterns are about how objects collaborate. **Strategy** encapsulates interchangeable algorithms behind the same interface (computing shipping in different ways). **Observer** lets several objects be notified when something changes (events). **Command** turns an action into an object, handy for job queues and “undo”.'),
            C('type Spedizione = (pesoKg: number) => number;               // Strategy
const standard: Spedizione = p => 4.9 + p * 0.5;
const espressa: Spedizione = p => 9.9 + p * 0.8;
const costo = (s: Spedizione, peso: number) => s(peso);

class Evento<T> {                                           // Observer
  private ascoltatori: ((dato: T) => void)[] = [];
  iscrivi(f: (dato: T) => void) { this.ascoltatori.push(f); }
  emetti(dato: T) { this.ascoltatori.forEach(f => f(dato)); }
}
const ordineCreato = new Evento<{ id: number }>();
ordineCreato.iscrivi(o => console.log("Email per l’ordine", o.id));
ordineCreato.emetti({ id: 42 });', 'ts'),
            P('In JavaScript e TypeScript molti pattern diventano semplici funzioni: una Strategy è spesso solo una funzione passata come parametro, un Observer è il sistema di eventi del browser (`addEventListener`). Conoscere i nomi aiuta a riconoscerli e a comunicare.', 'In JavaScript and TypeScript many patterns become plain functions: a Strategy is often just a function passed as a parameter, an Observer is the browser’s event system (`addEventListener`). Knowing the names helps you recognise them and communicate.'),
        ], [
            Q('b1', L('Che cosa incapsula una Strategy?', 'What does a Strategy encapsulate?'), [L('Algoritmi intercambiabili', 'Interchangeable algorithms'), L('Una password', 'A password'), L('Un database', 'A database'), L('Uno stile CSS', 'A CSS style')], 0, L('Stessa interfaccia, calcolo diverso.', 'Same interface, different calculation.')),
            Q('b2', L('Quale pattern è alla base di addEventListener?', 'Which pattern underlies addEventListener?'), ['Observer', 'Singleton', 'Builder', 'Adapter'], 0, L('Ascoltatori avvisati.', 'Listeners notified.')),
            Q('b3', L('A che cosa è utile il pattern Command?', 'What is the Command pattern useful for?'), [L('Code di lavori e funzione annulla', 'Job queues and undo'), L('Disegnare forme', 'Drawing shapes'), L('Leggere file', 'Reading files'), L('Compilare', 'Compiling')], 0, L('L’azione diventa un oggetto.', 'The action becomes an object.')),
        ], task('ts', L('Definisci il tipo `Sconto = (prezzo: number) => number` e due strategie: `nessuno` che restituisce il prezzo e `saldi` che restituisce `prezzo * 0.7`.', 'Define the type `Sconto = (prezzo: number) => number` and two strategies: `nessuno` returning the price and `saldi` returning `prezzo * 0.7`.'), '', [re('type\s+Sconto\s*=\s*\(\s*prezzo\s*:\s*number\s*\)\s*=>\s*number', L('Il tipo', 'The type')), re('const\s+nessuno\s*:\s*Sconto\s*=\s*\(?\s*(\w+)\s*\)?\s*=>\s*\1\s*;?', L('Strategia nessuno', 'The nessuno strategy')), re('const\s+saldi\s*:\s*Sconto\s*=\s*\(?\s*(\w+)\s*\)?\s*=>\s*\1\s*\*\s*0\.7', L('Strategia saldi', 'The saldi strategy'))], '', "type Sconto = (prezzo: number) => number;\nconst nessuno: Sconto = p => p;\nconst saldi: Sconto = p => p * 0.7;"), [
            dMatch(pMatch(), [['Strategy', L('Algoritmi intercambiabili', 'Interchangeable algorithms')], ['Observer', L('Avvisare chi ascolta', 'Notifying listeners')], ['Command', L('Azione come oggetto', 'Action as object')], ['addEventListener', 'Observer']], L('Collaborare tra oggetti.', 'Objects collaborating.')),
            dFlash(pFlash(), [[L('In TypeScript una Strategy può essere una funzione', 'In TypeScript a Strategy can be a function'), true], [L('L’Observer non permette più ascoltatori', 'Observer does not allow several listeners'), false], [L('Il Command aiuta a implementare annulla', 'Command helps implement undo'), true], [L('I nomi dei pattern non servono a comunicare', 'Pattern names do not help communication'), false]], L('Un vocabolario comune.', 'A shared vocabulary.')),
        ]),
        lesson('architecture', L('Architettura e refactoring', 'Architecture and refactoring'), [
            P('L’**iniezione delle dipendenze** passa a un oggetto ciò di cui ha bisogno (un database, un servizio email) invece di farglielo creare dentro: il codice si testa sostituendo il vero servizio con un finto. È la “D” di SOLID in pratica.', '**Dependency injection** hands an object what it needs (a database, an email service) instead of letting it create it inside: the code can be tested by swapping the real service for a fake one. It is the “D” of SOLID in practice.'),
            C('// Prima: dipendenza nascosta
class Ordini { salva(o) { new MySqlDb().insert(o); } }

// Dopo: dipendenza iniettata
class Ordini {
  constructor(db) { this.db = db; }
  salva(o) { this.db.insert(o); }
}
const ordini = new Ordini(new DbFinto());   // nei test', 'js'),
            P('Il **refactoring** migliora la struttura senza cambiare il comportamento, a piccoli passi e con i test verdi: estrarre una funzione, rinominare, togliere duplicati. I segnali (**code smell**): funzioni lunghissime, troppi parametri, codice copiato, nomi vaghi come `dati2`.', '**Refactoring** improves structure without changing behaviour, in small steps with green tests: extract a function, rename, remove duplication. The signals (**code smells**): very long functions, too many parameters, copied code, vague names like `data2`.'),
        ], [
            Q('ar1', L('Che cosa fa l’iniezione delle dipendenze?', 'What does dependency injection do?'), [L('Passa all’oggetto ciò di cui ha bisogno', 'Hands the object what it needs'), L('Crea tutto dentro la classe', 'Creates everything inside the class'), L('Inietta codice malevolo', 'Injects malicious code'), L('Cancella le dipendenze', 'Deletes dependencies')], 0, L('Così si testa con un finto.', 'So it can be tested with a fake.')),
            Q('ar2', L('Che cosa non deve cambiare in un refactoring?', 'What must not change in a refactoring?'), [L('Il comportamento', 'The behaviour'), L('I nomi', 'The names'), L('La struttura', 'The structure'), L('I file', 'The files')], 0, L('Solo la forma migliora.', 'Only the shape improves.')),
            Q('ar3', L('Quale è un code smell?', 'Which is a code smell?'), [L('Codice copiato in più punti', 'Code copied in several places'), L('Una funzione con un nome chiaro', 'A function with a clear name'), L('Un test verde', 'A green test'), L('Un commento utile', 'A useful comment')], 0, L('La duplicazione va tolta.', 'Duplication should go.')),
        ], task('js', L('Scrivi la classe `Avvisi` con un `constructor(mailer)` che salva `this.mailer = mailer`.', 'Write the `Avvisi` class with a `constructor(mailer)` saving `this.mailer = mailer`.'), '', [re('class\s+Avvisi\s*\{', L('La classe esiste', 'The class exists')), re('constructor\(\s*mailer\s*\)\s*\{\s*this\.mailer\s*=\s*mailer', L('La dipendenza è iniettata', 'The dependency is injected'))], '', "class Avvisi {\n  constructor(mailer) { this.mailer = mailer; }\n}"), [
            dMatch(pMatch(), [[L('Iniezione delle dipendenze', 'Dependency injection'), L('Passare i servizi da fuori', 'Passing services from outside')], ['Refactoring', L('Migliora senza cambiare il comportamento', 'Improves without changing behaviour')], [L('Estrai funzione', 'Extract function'), L('Spezza codice lungo', 'Splits long code')], ['Code smell', L('Segnale di problema', 'Problem signal')]], L('Codice che invecchia bene.', 'Code that ages well.')),
            dFlash(pFlash(), [[L('Il refactoring si fa con i test verdi', 'Refactoring is done with green tests'), true], [L('Un refactoring aggiunge nuove funzioni', 'A refactoring adds new features'), false], [L('Un servizio iniettato si sostituisce nei test', 'An injected service can be swapped in tests'), true], [L('Nomi come dati2 sono chiari', 'Names like data2 are clear'), false]], L('Struttura sana.', 'Healthy structure.')),
        ]),
        lesson('composition', L('Composizione, Composite e Proxy', 'Composition, Composite and Proxy'), [
            P('L’ereditarietà lega le classi strettamente: un cambio nel genitore rompe i figli e le gerarchie profonde diventano goffe. Il principio **composizione invece di ereditarietà** costruisce gli oggetti unendo parti piccole che fanno una cosa sola. Il **Composite** tratta un gruppo di oggetti come se fosse uno solo (una cartella con file e sottocartelle, un menu con sottomenu). Il **Proxy** sta al posto di un altro oggetto, con la stessa interfaccia, e ne **controlla l’accesso**: permessi, caricamento pigro, cache, chiamate remote.', 'Inheritance ties classes tightly: a change in the parent breaks the children and deep hierarchies become awkward. The **composition over inheritance** principle builds objects by joining small parts that each do one thing. The **Composite** treats a group of objects as if it were a single one (a folder with files and subfolders, a menu with submenus). The **Proxy** stands in for another object, with the same interface, and **controls access** to it: permissions, lazy loading, caching, remote calls.'),
            C('interface Elemento { dimensione(): number; }

class File implements Elemento {
  constructor(private byte: number) {}
  dimensione() { return this.byte; }
}

class Cartella implements Elemento {                    // Composite
  private figli: Elemento[] = [];
  aggiungi(e: Elemento) { this.figli.push(e); return this; }
  dimensione() { return this.figli.reduce((t, e) => t + e.dimensione(), 0); }
}

const progetto = new Cartella()
  .aggiungi(new File(1200))
  .aggiungi(new Cartella().aggiungi(new File(300)));
progetto.dimensione();   // 1500

interface Documenti { leggi(id: string): Promise<string>; }

class DocumentiProtetti implements Documenti {          // Proxy
  constructor(private reale: Documenti, private utente: { admin: boolean }) {}
  async leggi(id: string) {
    if (!this.utente.admin) throw new Error("Accesso negato");
    return this.reale.leggi(id);
  }
}', 'ts'),
            P('Proxy e Decorator hanno la stessa forma (avvolgono un oggetto con la stessa interfaccia) ma uno scopo diverso: il Decorator **aggiunge** comportamento, il Proxy **controlla** l’accesso all’oggetto vero. JavaScript ha anche un oggetto `Proxy` integrato, usato per esempio dai sistemi reattivi come quello di Vue.', 'Proxy and Decorator have the same shape (they wrap an object with the same interface) but a different purpose: the Decorator **adds** behaviour, the Proxy **controls** access to the real object. JavaScript also has a built-in `Proxy` object, used for example by reactive systems such as Vue’s.'),
        ], [
            Q('co1', L('Perché preferire la composizione all’ereditarietà?', 'Why prefer composition to inheritance?'), [L('Perché le classi figlie sono più lente', 'Because child classes are slower'), L('Perché unisce parti piccole e indipendenti, senza gerarchie rigide', 'Because it joins small, independent parts, without rigid hierarchies'), L('Perché TypeScript non permette l’ereditarietà', 'Because TypeScript does not allow inheritance'), L('Perché così non servono più le interfacce', 'Because then interfaces are no longer needed')], 1, L('Meno legami, più flessibilità.', 'Fewer ties, more flexibility.')),
            Q('co2', L('Quale pattern tratta un gruppo di oggetti come un oggetto solo?', 'Which pattern treats a group of objects as a single object?'), ['Proxy', 'Singleton', 'Composite', 'Builder'], 2, L('Come una cartella che contiene cartelle.', 'Like a folder containing folders.')),
            Q('co3', L('Che differenza c’è tra Proxy e Decorator?', 'What is the difference between Proxy and Decorator?'), [L('Il Proxy cambia interfaccia, il Decorator no', 'The Proxy changes the interface, the Decorator does not'), L('Il Decorator funziona solo con le classi astratte', 'The Decorator only works with abstract classes'), L('Nessuna: sono due nomi dello stesso pattern', 'None: they are two names for one pattern'), L('Il Proxy controlla l’accesso, il Decorator aggiunge comportamento', 'The Proxy controls access, the Decorator adds behaviour')], 3, L('Stessa forma, scopo diverso.', 'Same shape, different purpose.')),
        ], task('ts', L('Scrivi la classe `Cartella` che implementa `Elemento`, con `private figli: Elemento[] = []` e un metodo `dimensione()` che somma la dimensione dei figli.', 'Write the `Cartella` class implementing `Elemento`, with `private figli: Elemento[] = []` and a `dimensione()` method that adds up the children’s sizes.'), "interface Elemento { dimensione(): number; }\n", [re('class\s+Cartella\s+implements\s+Elemento', L('Implementa l’interfaccia', 'It implements the interface')), re('figli\s*:\s*Elemento\[\]\s*=\s*\[\s*\]', L('L’elenco dei figli', 'The list of children')), re('dimensione\s*\(\s*\)\s*(:\s*number\s*)?\{[\s\S]*this\.figli[\s\S]*dimensione\s*\(\s*\)', L('Somma i figli', 'It adds up the children'))], '', "class Cartella implements Elemento {\n  private figli: Elemento[] = [];\n  dimensione(): number {\n    return this.figli.reduce((t, e) => t + e.dimensione(), 0);\n  }\n}"), [
            dMatch(pMatch(), [[L('Composizione', 'Composition'), L('Unire parti piccole', 'Joining small parts')], ['Composite', L('Un gruppo trattato come uno', 'A group treated as one')], ['Proxy', L('Controlla l’accesso', 'Controls access')], ['Decorator', L('Aggiunge comportamento', 'Adds behaviour')], [L('Gerarchia profonda', 'Deep hierarchy'), L('Fragile da cambiare', 'Fragile to change')]], L('Costruire con i mattoncini.', 'Building with bricks.')),
            dFlash(pFlash(), [[L('Con il Composite calcoli la dimensione di una cartella in modo ricorsivo', 'With the Composite you compute a folder’s size recursively'), true], [L('Un Proxy deve avere un’interfaccia diversa dall’originale', 'A Proxy must have a different interface from the original'), false], [L('La composizione riduce i legami tra le classi', 'Composition reduces the ties between classes'), true], [L('L’ereditarietà è sempre meglio della composizione', 'Inheritance is always better than composition'), false]], L('Comporre, non ereditare.', 'Compose, do not inherit.')),
        ]),
        lesson('state', L('State e macchine a stati', 'State and state machines'), [
            P('Quando un oggetto si comporta in modo diverso a seconda del suo **stato** (un ordine: nel carrello, pagato, spedito, consegnato), catene di `if` e `switch` si spargono ovunque. Il pattern **State**, o una **macchina a stati**, elenca in modo esplicito gli stati e le **transizioni** ammesse: un passaggio illegale, come da consegnato a carrello, diventa impossibile.', 'When an object behaves differently depending on its **state** (an order: in the cart, paid, shipped, delivered), chains of `if` and `switch` spread everywhere. The **State** pattern, or a **state machine**, lists explicitly the states and the allowed **transitions**: an illegal move, such as from delivered back to cart, becomes impossible.'),
            C('type Stato = "carrello" | "pagato" | "spedito" | "consegnato" | "annullato";

const transizioni: Record<Stato, Stato[]> = {
  carrello:   ["pagato", "annullato"],
  pagato:     ["spedito", "annullato"],
  spedito:    ["consegnato"],
  consegnato: [],
  annullato:  [],
};

class Ordine {
  stato: Stato = "carrello";
  passaA(nuovo: Stato) {
    if (!transizioni[this.stato].includes(nuovo)) {
      throw new Error(`Da ${this.stato} non si passa a ${nuovo}`);
    }
    this.stato = nuovo;
  }
}', 'ts'),
            P('Nella versione classica ogni stato è una classe con gli stessi metodi (`paga()`, `spedisci()`) e l’oggetto delega allo stato corrente. In TypeScript spesso bastano un tipo unione e una tabella delle transizioni: con `Record<Stato, …>` il compilatore controlla che nessuno stato sia dimenticato. Per macchine complesse esistono librerie come XState.', 'In the classic version each state is a class with the same methods (`paga()`, `spedisci()`) and the object delegates to its current state. In TypeScript a union type and a transition table are often enough: with `Record<Stato, …>` the compiler checks that no state is forgotten. For complex machines there are libraries such as XState.'),
        ], [
            Q('sm1', L('Quale problema risolve il pattern State?', 'Which problem does the State pattern solve?'), [L('Oggetti troppo grandi da salvare nel database', 'Objects too large to store in the database'), L('Classi che non si possono istanziare', 'Classes that cannot be instantiated'), L('Comportamento che cambia con lo stato, senza if sparsi ovunque', 'Behaviour that changes with state, without ifs scattered everywhere'), L('Funzioni chiamate troppe volte di seguito', 'Functions called too many times in a row')], 2, L('Stati e transizioni diventano espliciti.', 'States and transitions become explicit.')),
            Q('sm2', L('Con la tabella dell’esempio, che cosa succede chiamando `passaA("pagato")` su un ordine consegnato?', 'With the example’s table, what happens when calling `passaA("pagato")` on a delivered order?'), [L('L’ordine torna allo stato pagato', 'The order goes back to paid'), L('Viene lanciato un errore: la transizione non è ammessa', 'An error is thrown: the transition is not allowed'), L('Non succede niente, in silenzio', 'Nothing happens, silently'), L('L’ordine viene cancellato', 'The order is deleted')], 1, L('Da consegnato non si va da nessuna parte.', 'From delivered you go nowhere.')),
            Q('sm3', L('Perché usare `Record<Stato, Stato[]>` per la tabella?', 'Why use `Record<Stato, Stato[]>` for the table?'), [L('Per salvare gli ordini nel database', 'To save the orders in the database'), L('Per ordinare gli stati in ordine alfabetico', 'To sort the states alphabetically'), L('Per rendere gli stati modificabili da chiunque', 'To make the states editable by anyone'), L('Perché il compilatore controlli che ogni stato abbia le sue transizioni', 'So the compiler checks that every state has its transitions')], 3, L('Uno stato dimenticato è un errore di compilazione.', 'A forgotten state is a compile error.')),
        ], task('ts', L('Completa `passaA(nuovo: Stato)`: se `!transizioni[this.stato].includes(nuovo)` lancia un `Error`, altrimenti assegna `this.stato = nuovo`.', 'Complete `passaA(nuovo: Stato)`: if `!transizioni[this.stato].includes(nuovo)` throw an `Error`, otherwise assign `this.stato = nuovo`.'), "passaA(nuovo: Stato) {\n  // controlla la transizione\n}", [re('if\s*\(\s*!\s*transizioni\s*\[\s*this\.stato\s*\]\s*\.includes\s*\(\s*nuovo\s*\)\s*\)', L('Controlli la transizione', 'You check the transition')), re('throw\s+new\s+Error\s*\(', L('Lanci un errore', 'You throw an error')), re('this\.stato\s*=\s*nuovo', L('Cambi stato', 'You change state'))], '', "passaA(nuovo: Stato) {\n  if (!transizioni[this.stato].includes(nuovo)) throw new Error(\"Transizione non ammessa\");\n  this.stato = nuovo;\n}"), [
            dOrder(pSort(), ['"carrello"', '"pagato"', '"spedito"', '"consegnato"'], L('Il percorso normale di un ordine.', 'An order’s normal path.')),
            dFlash(pFlash(), [[L('Nell’esempio da consegnato si può tornare al carrello', 'In the example you can go from delivered back to cart'), false], [L('Una tabella delle transizioni rende espliciti i passaggi ammessi', 'A transition table makes the allowed moves explicit'), true], [L('Il pattern State richiede un if in ogni metodo', 'The State pattern needs an if in every method'), false], [L('Nell’esempio annullato è uno stato finale', 'In the example cancelled is a final state'), true]], L('Stati sotto controllo.', 'States under control.')),
        ]),
        lesson('chain', L('Catena di responsabilità e middleware', 'Chain of responsibility and middleware'), [
            P('Nella **catena di responsabilità** una richiesta attraversa una serie di gestori: ognuno può risponderle, modificarla o passarla al successivo. È il **middleware** dei framework web (Express, Laravel, ASP.NET): log, autenticazione, limiti di richieste, ognuno è un pezzetto piccolo e riusabile.', 'In the **chain of responsibility** a request passes through a series of handlers: each one can answer it, change it or pass it to the next. It is the **middleware** of web frameworks (Express, Laravel, ASP.NET): logging, authentication, rate limits, each one a small, reusable piece.'),
            C('type Richiesta = { utente?: string; percorso: string };
type Risposta = { stato: number; corpo: string };
type Middleware = (req: Richiesta, avanti: () => Risposta) => Risposta;

const log: Middleware = (req, avanti) => {
  console.log("→", req.percorso);
  return avanti();
};

const autenticazione: Middleware = (req, avanti) =>
  req.utente ? avanti() : { stato: 401, corpo: "Accedi prima" };

function catena(lista: Middleware[], finale: (req: Richiesta) => Risposta) {
  return (req: Richiesta): Risposta => {
    const esegui = (i: number): Risposta =>
      i < lista.length ? lista[i](req, () => esegui(i + 1)) : finale(req);
    return esegui(0);
  };
}

const profilo = catena([log, autenticazione], () => ({ stato: 200, corpo: "Profilo" }));
profilo({ percorso: "/profilo" });   // { stato: 401, corpo: "Accedi prima" }', 'ts'),
            P('L’**ordine** conta: il log messo per primo vede tutte le richieste, l’autenticazione prima del gestore lo protegge. Un middleware che non chiama `avanti()` **ferma la catena** e risponde lui, come fa qui l’autenticazione con il 401.', '**Order** matters: logging placed first sees every request, authentication before the handler protects it. A middleware that does not call `avanti()` **stops the chain** and answers itself, as authentication does here with the 401.'),
        ], [
            Q('ch1', L('Che cosa succede se un middleware non chiama `avanti()`?', 'What happens if a middleware does not call `avanti()`?'), [L('La catena si ferma e risponde quel middleware', 'The chain stops and that middleware answers'), L('Il framework lo richiama da solo dopo un po’', 'The framework calls it again by itself after a while'), L('Viene saltato e si passa al successivo', 'It is skipped and the next one runs'), L('Si genera sempre un errore 500', 'A 500 error is always raised')], 0, L('Così l’autenticazione blocca chi non è entrato.', 'That is how authentication blocks those not signed in.')),
            Q('ch2', L('Perché mettere l’autenticazione prima del gestore finale?', 'Why put authentication before the final handler?'), [L('Per rendere la risposta più veloce', 'To make the response faster'), L('Perché così i log sono più corti', 'Because then the logs are shorter'), L('Perché il framework non accetta altri ordini', 'Because the framework accepts no other order'), L('Per bloccare chi non è autenticato prima che arrivi ai dati', 'To block unauthenticated users before they reach the data')], 3, L('L’ordine della catena è una scelta di sicurezza.', 'The order of the chain is a security choice.')),
            Q('ch3', L('Quale pattern c’è dietro i middleware?', 'Which pattern lies behind middleware?'), ['Singleton', L('Catena di responsabilità', 'Chain of responsibility'), 'Factory', 'Composite'], 1, L('Ogni anello decide se rispondere o passare.', 'Each link decides whether to answer or pass on.')),
        ], task('ts', L('Scrivi il middleware `soloAdmin: Middleware` che risponde `{ stato: 403, corpo: "Vietato" }` se `req.utente` non è `"admin"`, altrimenti chiama `avanti()`.', 'Write the `soloAdmin: Middleware` middleware that answers `{ stato: 403, corpo: "Vietato" }` if `req.utente` is not `"admin"`, otherwise calls `avanti()`.'), '', [re('const\s+soloAdmin\s*:\s*Middleware\s*=', L('Il middleware', 'The middleware')), re('req\.utente\s*(!==?|===?)\s*["\']admin["\']|["\']admin["\']\s*(!==?|===?)\s*req\.utente', L('Controlli l’utente', 'You check the user')), re('stato\s*:\s*403', L('Rispondi 403', 'You answer 403')), re('avanti\s*\(\s*\)', L('Altrimenti passi avanti', 'Otherwise you pass on'))], '', "const soloAdmin: Middleware = (req, avanti) =>\n  req.utente === \"admin\" ? avanti() : { stato: 403, corpo: \"Vietato\" };"), [
            dMatch(pMatch(), [['Middleware', L('Un anello della catena', 'A link of the chain')], ['avanti()', L('Passa al successivo', 'Passes to the next')], ['401', L('Non autenticato', 'Not authenticated')], [L('Ordine', 'Order'), L('Chi vede la richiesta per primo', 'Who sees the request first')], [L('Gestore finale', 'Final handler'), L('Produce la risposta vera', 'Produces the real response')]], L('Anello dopo anello.', 'Link after link.')),
            dOrder(pSort(), [L('Arriva la richiesta', 'The request arrives'), L('Middleware di log', 'Logging middleware'), L('Middleware di autenticazione', 'Authentication middleware'), L('Gestore della rotta', 'Route handler'), L('Parte la risposta', 'The response goes out')], L('Il viaggio di una richiesta.', 'A request’s journey.')),
        ]),
        lesson('layers', L('Repository e architettura esagonale', 'Repositories and hexagonal architecture'), [
            P('Tieni le **regole di business** separate dai dettagli: database, framework, servizi esterni. Un **Repository** offre un’interfaccia simile a una collezione (`trova`, `salva`) e nasconde l’SQL. Nell’**architettura esagonale** (porte e adattatori) il nucleo definisce le interfacce di cui ha bisogno (le **porte**) e gli **adattatori** le realizzano per Postgres, per Stripe o per i test.', 'Keep **business rules** separate from the details: database, framework, external services. A **Repository** offers a collection-like interface (`trova`, `salva`) and hides the SQL. In the **hexagonal architecture** (ports and adapters) the core defines the interfaces it needs (the **ports**) and the **adapters** implement them for Postgres, for Stripe or for tests.'),
            C('// il nucleo: nessun import di database o framework
type Ordine = { id: number; stato: string };

interface OrdiniRepository {                              // porta
  trova(id: number): Promise<Ordine | null>;
  salva(o: Ordine): Promise<void>;
}

class ConfermaOrdine {                                    // caso d’uso
  constructor(private ordini: OrdiniRepository) {}
  async esegui(id: number) {
    const o = await this.ordini.trova(id);
    if (!o) throw new Error("Ordine inesistente");
    await this.ordini.salva({ ...o, stato: "confermato" });
  }
}

class OrdiniInMemoria implements OrdiniRepository {       // adattatore per i test
  private dati = new Map<number, Ordine>();
  async trova(id: number) { return this.dati.get(id) ?? null; }
  async salva(o: Ordine) { this.dati.set(o.id, o); }
}', 'ts'),
            P('Le dipendenze puntano **verso il nucleo**: le regole non sanno niente di Postgres, quindi puoi cambiare database o testare senza. Ma ricorda **YAGNI** (You Aren’t Gonna Need It, “non ti servirà”): non aggiungere strati e astrazioni “per sicurezza”. In uno script piccolo sono solo peso: introducili quando nasce un bisogno vero.', 'Dependencies point **towards the core**: the rules know nothing about Postgres, so you can switch database or test without it. But remember **YAGNI** (You Aren’t Gonna Need It): do not add layers and abstractions “just in case”. In a small script they are just weight: introduce them when a real need arises.'),
        ], [
            Q('ly1', L('Che cosa nasconde un Repository?', 'What does a Repository hide?'), [L('L’interfaccia grafica dell’app', 'The app’s user interface'), L('I dettagli dell’archivio dati, come le query SQL', 'The details of data storage, such as SQL queries'), L('Le regole di business del dominio', 'The domain’s business rules'), L('I test automatici del progetto', 'The project’s automated tests')], 1, L('Il resto del codice vede solo `trova` e `salva`.', 'The rest of the code sees only `trova` and `salva`.')),
            Q('ly2', L('Nell’architettura esagonale, dove va il codice specifico del database?', 'In the hexagonal architecture, where does database-specific code go?'), [L('Nel nucleo, insieme alle regole', 'In the core, with the rules'), L('Dentro i test', 'Inside the tests'), L('In un adattatore che realizza una porta del nucleo', 'In an adapter implementing one of the core’s ports'), L('Nel file di configurazione del framework', 'In the framework’s configuration file')], 2, L('Il nucleo resta pulito.', 'The core stays clean.')),
            Q('ly3', L('Che cosa dice YAGNI?', 'What does YAGNI say?'), [L('Ogni classe deve avere la sua interfaccia', 'Every class must have its own interface'), L('Bisogna prevedere ogni possibile uso futuro', 'You must foresee every possible future use'), L('Le funzioni devono avere al massimo tre righe', 'Functions must have at most three lines'), L('Non aggiungere ciò che non serve ancora', 'Do not add what is not needed yet')], 3, L('Astrazioni quando servono, non prima.', 'Abstractions when needed, not before.')),
        ], task('ts', L('Scrivi la classe `OrdiniInMemoria` che implementa `OrdiniRepository`, con `private dati = new Map<number, Ordine>()` e i metodi `trova` e `salva`.', 'Write the `OrdiniInMemoria` class implementing `OrdiniRepository`, with `private dati = new Map<number, Ordine>()` and the `trova` and `salva` methods.'), '', [re('class\s+OrdiniInMemoria\s+implements\s+OrdiniRepository', L('Implementa la porta', 'It implements the port')), re('new\s+Map\s*<\s*number\s*,\s*Ordine\s*>', L('Una mappa per i dati', 'A map for the data')), re('trova\s*\(\s*\w+\s*:\s*number\s*\)', L('Il metodo trova', 'The trova method')), re('salva\s*\(\s*\w+\s*:\s*Ordine\s*\)', L('Il metodo salva', 'The salva method'))], '', "class OrdiniInMemoria implements OrdiniRepository {\n  private dati = new Map<number, Ordine>();\n  async trova(id: number) { return this.dati.get(id) ?? null; }\n  async salva(o: Ordine) { this.dati.set(o.id, o); }\n}"), [
            dMatch(pMatch(), [['Repository', L('Nasconde l’archivio', 'Hides the storage')], [L('Porta', 'Port'), L('Interfaccia del nucleo', 'The core’s interface')], [L('Adattatore', 'Adapter'), L('Realizza la porta', 'Implements the port')], [L('Caso d’uso', 'Use case'), L('Una regola di business', 'A business rule')], ['YAGNI', L('Non ti servirà', 'You aren’t gonna need it')]], L('Strati con giudizio.', 'Layers with judgement.')),
            dFlash(pFlash(), [[L('Il nucleo importa direttamente il driver di Postgres', 'The core imports the Postgres driver directly'), false], [L('Un repository in memoria è utile nei test', 'An in-memory repository is useful in tests'), true], [L('YAGNI consiglia di aggiungere strati per sicurezza', 'YAGNI advises adding layers just in case'), false], [L('Le dipendenze puntano verso il nucleo', 'Dependencies point towards the core'), true]], L('Il cuore al centro.', 'The heart at the centre.')),
        ]),
    ],
];
