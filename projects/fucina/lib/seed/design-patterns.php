<?php
/** Design patterns and clean code: principles (SOLID, DRY), creational, structural and behavioural patterns. */

return [
    'id' => 'design-patterns', 'family' => 'patterns', 'after' => null,
    'title' => L('Design pattern e codice pulito', 'Design Patterns and Clean Code'), 'short' => L('Design pattern', 'Design Patterns'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Scrivere codice facile da cambiare: principi SOLID e DRY, pattern creazionali, strutturali e comportamentali, con esempi in TypeScript.', 'Writing code that is easy to change: SOLID and DRY principles, creational, structural and behavioural patterns, with TypeScript examples.'),
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
  addebita(euro: number) { return this.stripe.charges.create({ amount: euro * 100, currency: "eur" }); }
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
    ],
];
