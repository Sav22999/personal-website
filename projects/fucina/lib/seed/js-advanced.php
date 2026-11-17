<?php
/** JavaScript advanced: asynchronous code, fetch, modules, classes, closures and the event loop, prototypes and this, generators, cancelling requests. */

return [
    'id' => 'js-advanced', 'family' => 'js', 'after' => 'js-intermediate',
    'title' => L('JavaScript avanzato', 'Advanced JavaScript'), 'short' => L('JS avanzato', 'JS Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Promise, async/await, fetch, moduli, classi, closure e il ciclo degli eventi, prototipi e `this`, generatori, richieste annullabili.', 'Promises, async/await, fetch, modules, classes, closures and the event loop, prototypes and `this`, generators, cancellable requests.'),
    'lessons' => [
        lesson('promises', L('Promise', 'Promises'), [
            P('Una **Promise** rappresenta un risultato che arriverà: può riuscire o fallire. Con `then` dici che cosa fare col valore, con `catch` come gestire l’errore.', 'A **Promise** stands for a result yet to come: it can succeed or fail. With `then` you say what to do with the value, with `catch` how to handle the error.'),
            C('function attendi(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
attendi(500)
  .then(() => "fatto")
  .then(testo => console.log(testo))
  .catch(errore => console.error(errore))
  .finally(() => console.log("fine"));

Promise.all([a(), b()]).then(([x, y]) => usa(x, y));', 'js'),
            P('`Promise.all` aspetta più operazioni insieme e fallisce se ne fallisce una. Ogni `then` riceve quello restituito dal precedente.', '`Promise.all` waits for several operations together and fails if one fails. Each `then` gets what the previous one gave back.'),
        ], [
            Q('p1', L('Che cosa rappresenta una Promise?', 'What does a Promise stand for?'), [L('Un risultato futuro', 'A future result'), L('Un ciclo', 'A loop'), L('Un file', 'A file'), L('Un errore certo', 'A certain error')], 0, L('Prima è in attesa, poi riesce o fallisce.', 'First it is pending, then it succeeds or fails.')),
            Q('p2', L('Quale metodo gestisce i fallimenti?', 'Which method handles failures?'), ['catch', 'then', 'fail', 'error'], 0, L('Prende gli errori di tutta la catena.', 'It catches errors from the whole chain.')),
            Q('p3', L('Che cosa fa `Promise.all`?', 'What does `Promise.all` do?'), [L('Aspetta tutte le promise', 'Waits for all promises'), L('Prende la più veloce', 'Takes the fastest'), L('Le cancella', 'Deletes them'), L('Le ripete', 'Repeats them')], 0, L('La più veloce la dà `Promise.race`.', '`Promise.race` gives the fastest.')),
        ], task('js', L('Sulla promise `carica()`, stampa il risultato con `then` e gestisci gli errori con `catch`.', 'On the promise `carica()`, print the result with `then` and handle errors with `catch`.'), '', [re('carica\(\)\s*\.then\(', L('Usi `then`', 'You use `then`')), re('\.catch\(', L('Usi `catch`', 'You use `catch`'))], '', "carica()\n  .then(r => console.log(r))\n  .catch(e => console.error(e));"), [
            dMatch(pMatch(), [['then', L('Quando riesce', 'When it succeeds')], ['catch', L('Quando fallisce', 'When it fails')], ['finally', L('In ogni caso', 'In any case')], ['Promise.all', L('Tutte insieme', 'All together')], ['Promise.race', L('La prima che finisce', 'The first to finish')]], L('Il vocabolario del codice che aspetta.', 'The vocabulary of code that waits.')),
            dOrder(pSort(), ['carica()', '  .then(dati => usa(dati))', '  .catch(err => mostra(err))', '  .finally(() => nascondi());'], L('Successo, errore, e infine in ogni caso.', 'Success, error, and finally in any case.')),
        ]),
        lesson('async', L('async e await', 'async and await'), [
            P('`async` e `await` fanno sembrare lineare il codice asincrono. `await` aspetta una Promise senza bloccare la pagina, e si scrive solo dentro una funzione `async`.', '`async` and `await` make asynchronous code look linear. `await` waits for a Promise without freezing the page, and goes only inside an `async` function.'),
            C('async function profilo(id) {
  try {
    const utente = await carica(id);
    const punti = await caricaPunti(utente);
    return { utente, punti };
  } catch (e) {
    console.error(e);
    return null;
  }
}
const [a, b] = await Promise.all([x(), y()]);', 'js'),
            P('Una funzione `async` restituisce sempre una Promise. Se due operazioni non dipendono l’una dall’altra, lanciale insieme con `Promise.all`: è più veloce.', 'An `async` function always gives back a Promise. If two operations do not depend on each other, start them together with `Promise.all`: it is faster.'),
        ], [
            Q('a1', L('Dove si può usare `await`?', 'Where can you use `await`?'), [L('In una funzione `async`', 'In an `async` function'), L('Ovunque nel CSS', 'Anywhere in CSS'), L('Solo in `if`', 'Only in `if`'), L('Solo nei cicli', 'Only in loops')], 0, L('O al livello principale di un modulo.', 'Or at the top level of a module.')),
            Q('a2', L('Che cosa restituisce una funzione `async`?', 'What does an `async` function give back?'), [L('Una Promise', 'A Promise'), L('Un numero', 'A number'), L('Niente', 'Nothing'), L('Un array', 'An array')], 0, L('Anche se scrivi `return 1`.', 'Even if you write `return 1`.')),
            Q('a3', L('Come si gestisce un errore con `await`?', 'How do you handle an error with `await`?'), ['try…catch', 'if…else', 'for…of', 'switch'], 0, L('Una Promise fallita lancia un errore.', 'A failed Promise throws an error.')),
        ], task('js', L('Scrivi `async function leggi()` che fa `await carica()` e restituisce il risultato.', 'Write `async function leggi()` that does `await carica()` and gives back the result.'), '', [re('async\s+function\s+leggi\s*\(', L('La funzione è `async`', 'The function is `async`')), re('await\s+carica\(\)', L('Aspetti il risultato', 'You wait for the result')), re('return', L('Lo restituisci', 'You give it back'))], '', "async function leggi() {\n  const r = await carica();\n  return r;\n}"), [
            dBlanks(pFill(), "___ function dati() {\n  const r = ___ carica();\n  return r;\n}", ['async', 'await'], ['wait', 'then', 'sync'], L('`async` sulla funzione, `await` sull’attesa.', '`async` on the function, `await` on the wait.')),
            dBug(pBug(), ['function dati() {', '  const r = await carica();', '  return r;', '}'], 0, 'async function dati() {', L('`await` funziona solo in una funzione `async`.', '`await` only works in an `async` function.')),
        ]),
        lesson('fetch', L('Fetch e API', 'Fetch and APIs'), [
            P('`fetch` parla con un server. Restituisce una risposta: controlla `ok`, poi leggi il corpo con `json()`. Per inviare dati usi `POST` con un corpo in JSON.', '`fetch` talks to a server. It gives back a response: check `ok`, then read the body with `json()`. To send data you use `POST` with a JSON body.'),
            C('const r = await fetch("/api/punti");
if (!r.ok) throw new Error(`Errore ${r.status}`);
const dati = await r.json();

await fetch("/api/punti", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ punti: 10 }),
});', 'js'),
            P('Attenzione: `fetch` non fallisce su un errore 404 o 500, solo su problemi di rete. Per questo va controllato `r.ok`.', 'Careful: `fetch` does not fail on a 404 or 500, only on network problems. That is why you check `r.ok`.'),
        ], [
            Q('f1', L('`fetch` fallisce su un 404?', 'Does `fetch` fail on a 404?'), [L('No, va controllato `ok`', 'No, check `ok`'), L('Sì, sempre', 'Yes, always'), L('Solo in POST', 'Only on POST'), L('Solo su Safari', 'Only on Safari')], 0, L('Fallisce solo se la rete non risponde.', 'It only fails if the network does not answer.')),
            Q('f2', L('Come si legge il corpo JSON?', 'How do you read the JSON body?'), ['await r.json()', 'r.body', 'JSON(r)', 'r.text'], 0, L('Anche `json()` restituisce una Promise.', '`json()` also gives back a Promise.')),
            Q('f3', L('Che cosa serve per inviare un oggetto?', 'What is needed to send an object?'), ['JSON.stringify', 'toString()', 'String()', 'eval'], 0, L('Il corpo è testo.', 'The body is text.')),
        ], task('js', L('In una funzione `async`, fai `fetch("/api/corsi")`, controlla `r.ok` e leggi `r.json()`.', 'In an `async` function, do `fetch("/api/corsi")`, check `r.ok` and read `r.json()`.'), '', [re('await\s+fetch\(\s*["\']\/api\/corsi["\']\s*\)', L('Chiami l’API', 'You call the API')), re('r\.ok', L('Controlli `ok`', 'You check `ok`')), re('await\s+r\.json\(\)', L('Leggi il JSON', 'You read the JSON'))], '', "async function corsi() {\n  const r = await fetch(\"/api/corsi\");\n  if (!r.ok) throw new Error(\"Errore\");\n  return await r.json();\n}"), [
            dOrder(pSort(), ['const r = await fetch(url);', 'if (!r.ok) throw new Error("Errore");', 'const dati = await r.json();', 'mostra(dati);'], L('Chiedi, controlla, leggi, usa.', 'Ask, check, read, use.')),
            dMatch(pMatch(), [['GET', L('Chiede dati', 'Asks for data')], ['POST', L('Invia dati', 'Sends data')], ['200', L('Tutto bene', 'All good')], ['404', L('Non trovato', 'Not found')], ['500', L('Errore del server', 'Server error')]], L('Metodi e codici sono la lingua del web.', 'Methods and codes are the language of the web.')),
            dBug(pBug(), ['const r = await fetch("/api/punti");', 'const dati = r.json();', 'console.log(dati.punti);'], 1, 'const dati = await r.json();', L('Anche `json()` è una Promise: serve `await`.', '`json()` is also a Promise: it needs `await`.')),
        ]),
        lesson('modules', L('Moduli', 'Modules'), [
            P('I **moduli** dividono il codice in file. `export` rende disponibile qualcosa, `import` lo usa. Nella pagina si caricano con `type="module"`.', '**Modules** split code into files. `export` makes something available, `import` uses it. In the page they load with `type="module"`.'),
            C('// punti.js
export const MAX = 100;
export function somma(a, b) { return a + b; }
export default class Gioco {}

// app.js
import Gioco, { MAX, somma } from "./punti.js";
const pesante = await import("./grafici.js");', 'js'),
            P('Ogni modulo ha il suo ambito: le variabili non finiscono più nello spazio globale. `import()` come funzione carica un modulo solo quando serve.', 'Each module has its own scope: variables no longer end up in the global space. `import()` as a function loads a module only when needed.'),
        ], [
            Q('m1', L('Quante `export default` può avere un modulo?', 'How many `export default` can a module have?'), [L('Una', 'One'), L('Quante vuoi', 'As many as you like'), L('Due', 'Two'), L('Nessuna', 'None')], 0, L('Le esportazioni con nome invece sono libere.', 'Named exports are unlimited.')),
            Q('m2', L('Come si importa una esportazione con nome?', 'How do you import a named export?'), [L('Tra graffe', 'In braces'), L('Tra virgolette', 'In quotes'), L('Con `@`', 'With `@`'), L('Con `#`', 'With `#`')], 0, L('`import { somma } from "./a.js"`.', '`import { somma } from "./a.js"`.')),
            Q('m3', L('Che attributo serve allo `script`?', 'What attribute does the `script` need?'), ['type="module"', 'async="module"', 'lang="es"', 'mode="import"'], 0, L('Un modulo è anche differito di serie.', 'A module is also deferred by default.')),
        ], task('js', L('Esporta la funzione `doppio` e la costante `MAX`.', 'Export the function `doppio` and the constant `MAX`.'), '', [re('export\s+function\s+doppio', L('Esporti `doppio`', 'You export `doppio`')), re('export\s+const\s+MAX', L('Esporti `MAX`', 'You export `MAX`'))], '', "export function doppio(n) {\n  return n * 2;\n}\nexport const MAX = 100;"), [
            dBlanks(pFill(), '___ function somma(a, b) { return a + b; }' . "\n" . '___ { somma } ___ "./punti.js";', ['export', 'import', 'from'], ['require', 'module', 'using'], L('Si esporta da una parte e si importa dall’altra.', 'Export on one side and import on the other.')),
            dBug(pBug(), ['import somma from "./punti.js";', 'console.log(somma(1, 2));'], 0, 'import { somma } from "./punti.js";', L('`somma` è un’esportazione con nome: servono le graffe.', '`somma` is a named export: braces are needed.')),
        ]),
        lesson('classes', L('Classi', 'Classes'), [
            P('Una **classe** è lo stampo di oggetti simili. `constructor` li prepara, i metodi definiscono che cosa sanno fare, `extends` eredita da un’altra classe.', 'A **class** is the mould for similar objects. `constructor` prepares them, methods define what they can do, `extends` inherits from another class.'),
            C('class Eroe {
  #vite = 3;
  constructor(nome) {
    this.nome = nome;
  }
  get vite() { return this.#vite; }
  colpisci() { this.#vite--; }
}
class Mago extends Eroe {
  constructor(nome) {
    super(nome);
    this.magia = 10;
  }
}
const m = new Mago("Ada");', 'js'),
            P('`#` rende un campo privato: da fuori non si tocca. Nel costruttore di una sottoclasse `super()` va chiamato prima di usare `this`.', '`#` makes a field private: it cannot be touched from outside. In a subclass constructor `super()` must be called before using `this`.'),
        ], [
            Q('c1', L('Con che cosa si crea un oggetto da una classe?', 'What creates an object from a class?'), ['new', 'make', 'create', 'class'], 0, L('`new Eroe("Ada")`.', '`new Eroe("Ada")`.')),
            Q('c2', L('Che cosa fa `super()`?', 'What does `super()` do?'), [L('Chiama il costruttore del genitore', 'Calls the parent constructor'), L('Rende forte', 'Makes strong'), L('Cancella', 'Deletes'), L('Copia', 'Copies')], 0, L('Va chiamato prima di `this`.', 'It must come before `this`.')),
            Q('c3', L('Che cosa significa `#vite`?', 'What does `#vite` mean?'), [L('Campo privato', 'Private field'), L('Commento', 'Comment'), L('Id', 'Id'), L('Colore', 'Colour')], 0, L('Accessibile solo dentro la classe.', 'Reachable only inside the class.')),
        ], task('js', L('Crea la classe `Gatto` con un `constructor(nome)` e un metodo `miagola()`.', 'Create the class `Gatto` with a `constructor(nome)` and a `miagola()` method.'), '', [re('class\s+Gatto', L('La classe esiste', 'The class exists')), re('constructor\s*\(\s*nome\s*\)', L('Ha il costruttore', 'It has the constructor')), re('miagola\s*\(\s*\)\s*\{', L('Ha il metodo', 'It has the method'))], '', "class Gatto {\n  constructor(nome) {\n    this.nome = nome;\n  }\n  miagola() {\n    return \"Miao\";\n  }\n}"), [
            dBlanks(pFill(), "class Mago ___ Eroe {\n  ___(nome) {\n    ___(nome);\n  }\n}\nconst m = ___ Mago(\"Ada\");", ['extends', 'constructor', 'super', 'new'], ['inherits', 'init', 'this'], L('Il lessico completo dell’ereditarietà.', 'The full vocabulary of inheritance.')),
            dBug(pBug(), ['class Mago extends Eroe {', '  constructor(nome) {', '    this.magia = 10;', '    super(nome);', '  }', '}'], 2, '    super(nome); this.magia = 10;', L('`this` non esiste prima di `super()`.', '`this` does not exist before `super()`.')),
        ]),
        lesson('closures', L('Closure e ciclo degli eventi', 'Closures and the event loop'), [
            P('Una **closure** è una funzione che ricorda le variabili di dove è nata, anche dopo. JavaScript esegue una cosa alla volta: il **ciclo degli eventi** prende dalle code i compiti in attesa quando il codice in corso è finito.', 'A **closure** is a function that remembers the variables of where it was born, even later. JavaScript runs one thing at a time: the **event loop** takes waiting jobs from the queues when the running code has finished.'),
            C('function contatore() {
  let n = 0;
  return () => ++n;
}
const conta = contatore();
conta(); // 1
conta(); // 2

console.log("A");
setTimeout(() => console.log("B"), 0);
Promise.resolve().then(() => console.log("C"));
console.log("D");
// A D C B', 'js'),
            P('Le Promise (microtask) passano prima dei timer: per questo `C` arriva prima di `B`, anche con attesa zero.', 'Promises (microtasks) go before timers: that is why `C` comes before `B`, even with a zero wait.'),
        ], [
            Q('l1', L('Che cosa ricorda una closure?', 'What does a closure remember?'), [L('Le variabili esterne', 'The outer variables'), L('La password', 'The password'), L('La cronologia', 'The history'), L('Il DOM', 'The DOM')], 0, L('Così `n` sopravvive tra una chiamata e l’altra.', 'So `n` survives between calls.')),
            Q('l2', L('Sul thread principale, JavaScript esegue più codice insieme?', 'On the main thread, does JavaScript run several pieces of code at once?'), [L('No, uno alla volta', 'No, one at a time'), L('Sì, sempre', 'Yes, always'), L('Solo in CSS', 'Only in CSS'), L('Solo di notte', 'Only at night')], 0, L('Per questo un ciclo infinito blocca tutto. Per i calcoli pesanti esistono i Web Worker, su thread separati.', 'That is why an endless loop freezes everything. For heavy work there are Web Workers, on separate threads.')),
            Q('l3', L('Chi passa prima?', 'Who goes first?'), [L('Le Promise', 'Promises'), L('`setTimeout` a 0', '`setTimeout` at 0'), L('Dipende dal colore', 'It depends on the colour'), L('Il CSS', 'CSS')], 0, L('I microtask svuotano la coda per primi.', 'Microtasks empty their queue first.')),
        ], task('js', L('Scrivi `creaContatore()` che restituisce una funzione: a ogni chiamata fa `++n`.', 'Write `creaContatore()` that gives back a function: each call does `++n`.'), '', [re('function\s+creaContatore', L('La funzione esiste', 'The function exists')), re('let\s+n\s*=\s*0', L('`n` sta nella funzione esterna', '`n` lives in the outer function')), re('return\s+(\(\)\s*=>|function)', L('Restituisci una funzione', 'You give back a function')), re('\+\+n|n\+\+', L('`n` cresce', '`n` grows'))], '', "function creaContatore() {\n  let n = 0;\n  return () => ++n;\n}"), [
            dOrder(L('In che ordine appaiono i messaggi? Metti le righe nell’ordine di stampa.', 'In which order do the messages appear? Put the lines in print order.'), ['console.log("sincrono 1");', 'console.log("sincrono 2");', 'Promise.then → "promise"', 'setTimeout 0 → "timer"'], L('Prima il codice sincrono, poi i microtask, infine i timer.', 'First synchronous code, then microtasks, finally timers.')),
            dFlash(pFlash(), [[L('Una closure tiene viva una variabile', 'A closure keeps a variable alive'), true], [L('`setTimeout(f, 0)` parte all’istante, prima di tutto', '`setTimeout(f, 0)` runs instantly, before everything'), false], [L('`await` non blocca la pagina', '`await` does not freeze the page'), true], [L('Un calcolo lunghissimo blocca i click', 'A very long calculation blocks clicks'), true]], L('Un solo filo di esecuzione: i compiti aspettano il loro turno.', 'A single thread: jobs wait their turn.')),
        ]),
        lesson('prototypes', L('Prototipi e this', 'Prototypes and this'), [
            P('Ogni oggetto ha un **prototipo**: un altro oggetto a cui chiede le proprietà che non ha. Le classi sono una sintassi comoda sopra questo meccanismo: i metodi stanno una volta sola nel prototipo e tutte le istanze li condividono.', 'Every object has a **prototype**: another object it asks for the properties it lacks. Classes are a handy syntax on top of this mechanism: methods live once in the prototype and every instance shares them.'),
            C('class Eroe {
  constructor(nome) { this.nome = nome; }
  saluta() { return `Sono ${this.nome}`; }
}
const ada = new Eroe("Ada");
Object.getPrototypeOf(ada) === Eroe.prototype; // true
Object.hasOwn(ada, "saluta");                  // false: sta nel prototipo

const f = ada.saluta;
f();                         // TypeError: this è undefined
const g = ada.saluta.bind(ada);
g();                         // "Sono Ada"', 'js'),
            P('`this` non dipende da dove la funzione è scritta, ma da **come viene chiamata**: `ada.saluta()` dà `this = ada`, la stessa funzione staccata perde l’oggetto. `bind` lo fissa. Le arrow function non hanno un `this` proprio e usano quello del codice intorno: per questo sono comode come callback dentro un metodo.', '`this` does not depend on where the function is written, but on **how it is called**: `ada.saluta()` gives `this = ada`, the same function detached loses the object. `bind` fixes it. Arrow functions have no `this` of their own and use the one of the surrounding code: that is why they are handy as callbacks inside a method.'),
        ], [
            Q('pr1', L('Dove sta il metodo `saluta` di una classe?', 'Where does a class’s `saluta` method live?'), [L('In ogni istanza, copiato', 'In every instance, copied'), L('Nel prototipo, condiviso da tutte le istanze', 'In the prototype, shared by every instance'), L('In una variabile globale', 'In a global variable'), L('Solo nel costruttore', 'Only in the constructor')], 1, L('Una sola copia per tutte le istanze.', 'One copy for every instance.')),
            Q('pr2', L('Da che cosa dipende `this` in una funzione normale?', 'What does `this` depend on in a regular function?'), [L('Da dove è scritta', 'On where it is written'), L('Dal nome del file', 'On the file name'), L('Da come viene chiamata', 'On how it is called'), L('Dal numero di argomenti', 'On the number of arguments')], 2, L('`oggetto.metodo()` passa `oggetto` come `this`.', '`object.method()` passes `object` as `this`.')),
            Q('pr3', L('Perché le arrow function sono comode come callback dentro un metodo?', 'Why are arrow functions handy as callbacks inside a method?'), [L('Sono sempre più veloci', 'They are always faster'), L('Creano un loro `this` nuovo', 'They create a new `this` of their own'), L('Non possono restituire valori', 'They cannot give back values'), L('Usano il `this` del codice intorno', 'They use the `this` of the surrounding code')], 3, L('Dentro `setInterval(() => …)` `this` resta l’istanza.', 'Inside `setInterval(() => …)` `this` stays the instance.')),
        ], task('js', L('Crea `salutaAda` legando `ada.saluta` ad `ada` con `bind`.', 'Create `salutaAda` by binding `ada.saluta` to `ada` with `bind`.'), 'const ada = new Eroe("Ada");
', [re('(const|let)\s+salutaAda\s*=\s*ada\.saluta\.bind\(\s*ada\s*\)', L('Leghi il metodo all’oggetto', 'You bind the method to the object'))], '', 'const ada = new Eroe("Ada");
const salutaAda = ada.saluta.bind(ada);'), [
            dBug(pBug(), ['class Timer {', '  constructor() { this.secondi = 0; }', '  avvia() {', '    setInterval(function () { this.secondi++; }, 1000);', '  }', '}'], 3, '    setInterval(() => { this.secondi++; }, 1000);', L('Una `function` normale perde `this`: l’arrow function usa quello del metodo.', 'A regular `function` loses `this`: the arrow function uses the method’s one.')),
            dFlash(pFlash(), [[L('Le classi usano i prototipi sotto il cofano', 'Classes use prototypes under the hood'), true], [L('Ogni istanza ha una sua copia dei metodi', 'Every instance has its own copy of the methods'), false], [L('`bind` restituisce una nuova funzione', '`bind` gives back a new function'), true], [L('Le arrow function hanno un `this` tutto loro', 'Arrow functions have a `this` all of their own'), false]], L('Metodi condivisi, `this` decisa dalla chiamata.', 'Shared methods, `this` decided by the call.')),
        ]),
        lesson('generators', L('Iteratori e generatori', 'Iterators and generators'), [
            P('Un oggetto è **iterabile** se sa dare i suoi elementi uno alla volta: array, stringhe, Map e Set lo sono, e per questo funzionano con `for…of` e con lo spread. Una **funzione generatrice** (`function*`) crea un iteratore: con `yield` consegna un valore e si mette in pausa fino alla richiesta successiva.', 'An object is **iterable** if it can hand out its items one at a time: arrays, strings, Maps and Sets are, which is why they work with `for…of` and spread. A **generator function** (`function*`) creates an iterator: with `yield` it hands over a value and pauses until the next request.'),
            C('function* contaFino(n) {
  for (let i = 1; i <= n; i++) {
    yield i;
  }
}
const it = contaFino(2);
it.next();   // { value: 1, done: false }
it.next();   // { value: 2, done: false }
it.next();   // { value: undefined, done: true }
[...contaFino(3)]; // [1, 2, 3]

function* numeri() {
  let n = 0;
  while (true) yield ++n;   // infinito, ma pigro
}
numeri().filter(n => n % 2 === 0).take(3).toArray(); // [2, 4, 6]', 'js'),
            P('I generatori sono **pigri**: calcolano un valore solo quando serve, quindi anche una sequenza infinita è sicura se ne prendi pochi elementi. Gli **iterator helpers** (`map`, `filter`, `take`, `toArray`) lavorano direttamente sugli iteratori, senza creare array intermedi.', 'Generators are **lazy**: they compute a value only when needed, so even an endless sequence is safe if you take only a few items. **Iterator helpers** (`map`, `filter`, `take`, `toArray`) work directly on iterators, without building intermediate arrays.'),
        ], [
            Q('g1', L('Che cosa fa `yield`?', 'What does `yield` do?'), [L('Termina il programma', 'Ends the program'), L('Crea un array', 'Creates an array'), L('Consegna un valore e mette in pausa il generatore', 'Hands over a value and pauses the generator'), L('Lancia un errore', 'Throws an error')], 2, L('Il generatore riparte al `next()` successivo.', 'The generator resumes at the next `next()`.')),
            Q('g2', L('Che cosa restituisce `it.next()` quando la sequenza è finita?', 'What does `it.next()` give back when the sequence is over?'), ['{ value: undefined, done: true }', '{ value: 0, done: false }', 'null', L('Un errore', 'An error')], 0, L('`done: true` dice che non ci sono altri valori.', '`done: true` says there are no more values.')),
            Q('g3', L('Perché `while (true) yield ++n` non blocca la pagina?', 'Why does `while (true) yield ++n` not freeze the page?'), [L('Il browser lo ferma dopo un secondo', 'The browser stops it after a second'), L('Calcola un valore solo quando viene chiesto', 'It computes a value only when asked'), L('`yield` è asincrono', '`yield` is asynchronous'), L('`while (true)` non gira mai', '`while (true)` never runs')], 1, L('Tra un `yield` e l’altro il generatore è in pausa.', 'Between one `yield` and the next the generator is paused.')),
        ], task('js', L('Scrivi il generatore `function* pari()` che con `yield` produce `0, 2, 4…` all’infinito.', 'Write the generator `function* pari()` that produces `0, 2, 4…` forever with `yield`.'), '', [re('function\s*\*\s*pari\s*\(', L('Il generatore esiste', 'The generator exists')), re('yield\s+', L('Usi `yield`', 'You use `yield`'))], '', 'function* pari() {
  let n = 0;
  while (true) {
    yield n;
    n += 2;
  }
}'), [
            dBlanks(pFill(), "function___ lettere() {\n  ___ \"a\";\n  ___ \"b\";\n}\nfor (const l ___ lettere()) console.log(l);", ['*', 'yield', 'yield', 'of'], ['return', 'in', '=>'], L('Asterisco, `yield` e `for…of`.', 'Asterisk, `yield` and `for…of`.')),
            dFlash(pFlash(), [[L('Le stringhe sono iterabili', 'Strings are iterable'), true], [L('Un generatore calcola tutti i valori subito', 'A generator computes every value at once'), false], [L('`[...gen()]` raccoglie i valori in un array', '`[...gen()]` collects the values into an array'), true], [L('`for…of` funziona solo con gli array', '`for…of` only works with arrays'), false]], L('Pigri e iterabili.', 'Lazy and iterable.')),
        ]),
        lesson('cancel', L('Annullare e combinare operazioni', 'Cancelling and combining operations'), [
            P('Una richiesta può diventare inutile: l’utente cambia pagina o scrive un’altra ricerca. Con **`AbortController`** la annulli: passi il suo `signal` a `fetch` e chiami `abort()`. `AbortSignal.timeout(ms)` crea un segnale che si annulla da solo dopo un certo tempo.', 'A request can become useless: the user changes page or types another search. With **`AbortController`** you cancel it: you pass its `signal` to `fetch` and call `abort()`. `AbortSignal.timeout(ms)` creates a signal that cancels itself after a while.'),
            C('const controllo = new AbortController();
fetch("/api/cerca?q=js", { signal: controllo.signal })
  .then(r => r.json())
  .catch(e => {
    if (e.name === "AbortError") return; // annullata da noi
    throw e;
  });
controllo.abort();

const r = await fetch("/api/lento", { signal: AbortSignal.timeout(5000) });

const esiti = await Promise.allSettled([a(), b(), c()]);
const riusciti = esiti.filter(x => x.status === "fulfilled").map(x => x.value);', 'js'),
            P('Per combinare più promise: `Promise.all` fallisce alla prima che fallisce, **`Promise.allSettled`** aspetta tutte e dice chi è riuscito e chi no, **`Promise.any`** restituisce la prima che riesce, **`Promise.race`** la prima che finisce, bene o male.', 'To combine several promises: `Promise.all` fails at the first failure, **`Promise.allSettled`** waits for all of them and tells who succeeded and who did not, **`Promise.any`** gives back the first that succeeds, **`Promise.race`** the first that finishes, well or badly.'),
        ], [
            Q('ab1', L('Che cosa passi a `fetch` per poterla annullare?', 'What do you pass to `fetch` to be able to cancel it?'), ['{ abort: true }', '{ cancel: controllo }', '{ signal: controllo.signal }', '{ timeout: controllo }'], 2, L('Poi `controllo.abort()` la ferma.', 'Then `controllo.abort()` stops it.')),
            Q('ab2', L('Quale metodo aspetta tutte le promise e riporta anche i fallimenti?', 'Which method waits for all promises and also reports the failures?'), ['Promise.all', 'Promise.race', 'Promise.any', 'Promise.allSettled'], 3, L('Ogni esito ha `status`: `"fulfilled"` o `"rejected"`.', 'Each outcome has `status`: `"fulfilled"` or `"rejected"`.')),
            Q('ab3', L('Che cosa fa `AbortSignal.timeout(5000)`?', 'What does `AbortSignal.timeout(5000)` do?'), [L('Annulla l’operazione dopo 5 secondi', 'Cancels the operation after 5 seconds'), L('Aspetta 5 secondi prima di partire', 'Waits 5 seconds before starting'), L('Ripete la richiesta ogni 5 secondi', 'Repeats the request every 5 seconds'), L('Limita la risposta a 5000 byte', 'Limits the response to 5000 bytes')], 0, L('Utile per non aspettare all’infinito un server lento.', 'Handy to avoid waiting forever for a slow server.')),
        ], task('js', L('Crea `controllo = new AbortController()`, passa `controllo.signal` a `fetch(url, …)` e poi chiama `controllo.abort()`.', 'Create `controllo = new AbortController()`, pass `controllo.signal` to `fetch(url, …)` and then call `controllo.abort()`.'), '', [re('(const|let)\s+controllo\s*=\s*new\s+AbortController\s*\(\s*\)', L('Crei il controller', 'You create the controller')), re('fetch\s*\([^)]*signal\s*:\s*controllo\.signal', L('Passi il segnale', 'You pass the signal')), re('controllo\.abort\s*\(\s*\)', L('Annulli', 'You cancel'))], '', 'const controllo = new AbortController();
fetch(url, { signal: controllo.signal });
controllo.abort();'), [
            dMatch(pMatch(), [['Promise.all', L('Tutte, o fallisce alla prima', 'All, or fails at the first')], ['Promise.allSettled', L('Tutte, con l’esito di ognuna', 'All, with each outcome')], ['Promise.any', L('La prima che riesce', 'The first to succeed')], ['Promise.race', L('La prima che finisce', 'The first to finish')], ['abort()', L('Annulla', 'Cancels')]], L('Strumenti per orchestrare l’attesa.', 'Tools to orchestrate waiting.')),
            dBug(pBug(), ['const controllo = new AbortController();', 'fetch(url, { signal: controllo });', 'controllo.abort();'], 1, 'fetch(url, { signal: controllo.signal });', L('A `fetch` va il `signal`, non il controller.', '`fetch` needs the `signal`, not the controller.')),
        ]),
    ],
];
