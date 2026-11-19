<?php
/** JavaScript advanced: asynchronous code, fetch, modules, classes, closures and the event loop, prototypes and this, generators, cancelling requests. */

/** The three states of a promise: pending, then fulfilled (then) or rejected (catch), and finally in both cases. */
if (!function_exists('fig_jsadv_promise')) {
    function fig_jsadv_promise(string $l): string
    {
        $t = $l === 'it' ? ['in attesa', 'risolta', 'rifiutata', 'una volta decisa, una promise non cambia più'] : ['pending', 'fulfilled', 'rejected', 'once settled, a promise never changes again'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="japa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="20" y="82" width="150" height="62" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="7 4"/>
<rect x="260" y="20" width="180" height="62" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="260" y="144" width="180" height="62" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<rect x="510" y="82" width="110" height="62" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<g text-anchor="middle" font-weight="700" font-size="16"><text x="95" y="119">{$t[0]}</text><text x="350" y="46" fill="#0B7F5C">{$t[1]}</text><text x="350" y="170" fill="#B0245A">{$t[2]}</text></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="350" y="70">.then(valore)</text><text x="350" y="194">.catch(errore)</text><text x="565" y="118">.finally()</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#japa)"><path d="M170 104C210 104 215 52 256 52"/><path d="M170 122C210 122 215 174 256 174"/><path d="M440 52C480 52 480 100 506 104"/><path d="M440 174C480 174 480 126 506 122"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#5B5880" text-anchor="end"><text x="238" y="38">resolve</text><text x="238" y="200">reject</text></g>
<text x="20" y="230" font-size="13" fill="#5B5880">{$t[3]}</text>
</svg>
SVG;
    }
}

/** Two awaits one after the other take the sum of their times; Promise.all runs them together. */
if (!function_exists('fig_jsadv_parallel')) {
    function fig_jsadv_parallel(string $l): string
    {
        $t = $l === 'it' ? ['uno dopo l’altro', 'insieme', 'tempo'] : ['one after the other', 'together', 'time'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="20" y="52" font-weight="700">{$t[0]}</text><text x="20" y="72" font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#5B5880">await a(); await b();</text>
<text x="20" y="140" font-weight="700">{$t[1]}</text><text x="20" y="160" font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#5B5880">Promise.all([a(), b()])</text>
<path d="M240 24V196" stroke="#E4E2F0" stroke-width="2"/>
<rect x="250" y="40" width="160" height="36" rx="9" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="410" y="40" width="160" height="36" rx="9" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="250" y="110" width="160" height="30" rx="9" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="250" y="146" width="160" height="30" rx="9" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="330" y="63">a()</text><text x="490" y="63">b()</text><text x="330" y="130">a()</text><text x="330" y="166">b()</text></g>
<g font-weight="700" fill="#B84508"><text x="582" y="64">2 s</text><text x="422" y="150">1 s</text></g>
<path d="M250 200H580" stroke="#8A87B0" stroke-width="2"/><g stroke="#8A87B0" stroke-width="2"><path d="M250 195V205"/><path d="M410 195V205"/><path d="M570 195V205"/></g>
<g text-anchor="middle" font-size="13" fill="#5B5880"><text x="250" y="222">0</text><text x="410" y="222">1 s</text><text x="570" y="222">2 s</text></g>
<text x="636" y="205" font-size="13" fill="#5B5880" text-anchor="end">{$t[2]} →</text>
</svg>
SVG;
    }
}

/** A fetch as a round trip: the request goes to the server, the response comes back with status, headers and body. */
if (!function_exists('fig_jsadv_fetch')) {
    function fig_jsadv_fetch(string $l): string
    {
        $t = $l === 'it' ? ['browser', 'server', 'richiesta', 'risposta', 'metodo · indirizzo · header · corpo', 'stato · header · corpo'] : ['browser', 'server', 'request', 'response', 'method · address · headers · body', 'status · headers · body'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="jafa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="20" y="30" width="160" height="180" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="460" y="30" width="160" height="180" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g text-anchor="middle" font-weight="700" font-size="16"><text x="100" y="58">{$t[0]}</text><text x="540" y="58">{$t[1]}</text></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="100" y="100">fetch(url)</text><text x="100" y="168">r.ok ?</text><text x="100" y="188">await r.json()</text><text x="540" y="110">/api/punti</text><text x="540" y="150">{"punti":10}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#jafa)"><path d="M184 96H456"/><path d="M456 168H184"/></g>
<g text-anchor="middle"><text x="320" y="86" font-weight="700">{$t[2]} · <tspan font-family="ui-monospace, Menlo, monospace" font-size="14">GET /api/punti</tspan></text><text x="320" y="118" font-size="13" fill="#5B5880">{$t[4]}</text>
<text x="320" y="158" font-weight="700">{$t[3]} · <tspan font-family="ui-monospace, Menlo, monospace" font-size="14" fill="#0B7F5C">200 OK</tspan></text><text x="320" y="190" font-size="13" fill="#5B5880">{$t[5]}</text></g>
</svg>
SVG;
    }
}

/** The event loop: the stack runs the script, then the loop takes microtasks (promises) before tasks (timers); the console fills in order. */
if (!function_exists('fig_jsadv_loop')) {
    function fig_jsadv_loop(string $l): string
    {
        $t = $l === 'it' ? ['pila delle chiamate', 'script principale', 'microtask · prima', 'task · dopo', 'console'] : ['call stack', 'main script', 'microtasks · first', 'tasks · after', 'console'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.jae{animation:8s infinite}.jaeA{animation-name:jaeA}.jaeD{animation-name:jaeD}.jaeC{animation-name:jaeC}.jaeB{animation-name:jaeB}.jaeqC{animation-name:jaeqC}.jaeqB{animation-name:jaeqB}.jaer{transform-origin:320px 120px;animation:jaer 4s linear infinite}
@keyframes jaeA{0%,6%{opacity:0}9%,96%{opacity:1}100%{opacity:0}}@keyframes jaeD{0%,26%{opacity:0}29%,96%{opacity:1}100%{opacity:0}}@keyframes jaeC{0%,51%{opacity:0}54%,96%{opacity:1}100%{opacity:0}}@keyframes jaeB{0%,76%{opacity:0}79%,96%{opacity:1}100%{opacity:0}}
@keyframes jaeqC{0%,12%{opacity:0}15%,50%{opacity:1}53%,100%{opacity:0}}@keyframes jaeqB{0%,12%{opacity:0}15%,75%{opacity:1}78%,100%{opacity:0}}@keyframes jaer{to{transform:rotate(360deg)}}
@media (prefers-reduced-motion:reduce){.jae,.jaer{animation:none}}</style>
<defs><marker id="jaea" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="20" y="20" width="200" height="190" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="120" y="46" text-anchor="middle" font-weight="700">{$t[0]}</text>
<rect x="36" y="150" width="168" height="44" rx="9" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="120" y="177" text-anchor="middle" font-size="14">{$t[1]}</text>
<rect x="420" y="20" width="200" height="86" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="420" y="124" width="200" height="86" rx="14" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<g font-weight="700" font-size="14"><text x="436" y="44" fill="#1B66BC">{$t[2]}</text><text x="436" y="148" fill="#B84508">{$t[3]}</text></g>
<g class="jae jaeqC"><rect x="436" y="56" width="168" height="36" rx="8" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="448" y="79" font-family="ui-monospace, Menlo, monospace" font-size="13">then → log("C")</text></g>
<g class="jae jaeqB"><rect x="436" y="160" width="168" height="36" rx="8" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/><text x="448" y="183" font-family="ui-monospace, Menlo, monospace" font-size="13">timer → log("B")</text></g>
<g class="jaer" fill="none" stroke="#7048E8" stroke-width="3"><path d="M282 120A38 38 0 0 1 358 120"/><path d="M358 120A38 38 0 0 1 282 120"/><path d="M352 108L358 122L366 110" stroke-linejoin="round"/><path d="M288 132L282 118L274 130" stroke-linejoin="round"/></g>
<text x="320" y="182" text-anchor="middle" font-size="14" font-weight="700" fill="#5A35C8">event loop</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#jaea)"><path d="M416 63C390 63 375 82 364 96"/><path d="M416 167C390 167 375 152 364 142"/><path d="M276 120H226"/></g>
<rect x="20" y="236" width="600" height="64" rx="14" fill="#1E1C52"/>
<text x="40" y="274" font-size="14" fill="#C9C7DD">{$t[4]}</text>
<g font-family="ui-monospace, Menlo, monospace" font-size="20" font-weight="700" fill="#FFFFFF"><text class="jae jaeA" x="150" y="275">A</text><text class="jae jaeD" x="200" y="275">D</text><text class="jae jaeC" x="250" y="275" fill="#9CC8FF">C</text><text class="jae jaeB" x="300" y="275" fill="#FFB98A">B</text></g>
</svg>
SVG;
    }
}

/** The prototype chain: a property is looked up on the object, then on each prototype in turn, until null. */
if (!function_exists('fig_jsadv_proto')) {
    function fig_jsadv_proto(string $l): string
    {
        $t = $l === 'it' ? ['proprio? no', 'trovato!', 'fine della catena', 'ada.saluta() cerca da sinistra a destra'] : ['own? no', 'found!', 'end of the chain', 'ada.saluta() looks from left to right'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="jaoa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="20" y="30" font-size="14" fill="#5B5880">{$t[3]}</text>
<rect x="12" y="56" width="140" height="96" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="192" y="56" width="160" height="96" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="3"/>
<rect x="392" y="56" width="180" height="96" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="590" y="84" width="44" height="40" rx="9" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="26" y="82" font-weight="700" font-size="14">ada</text><text x="26" y="110">nome: "Ada"</text>
<text x="206" y="82" font-weight="700" font-size="14">Eroe.prototype</text><text x="206" y="110" font-weight="700" fill="#5A35C8">saluta()</text>
<text x="406" y="82" font-weight="700" font-size="14">Object.prototype</text><text x="406" y="110">toString()</text><text x="406" y="130">hasOwnProperty()</text>
<text x="612" y="109" text-anchor="middle" fill="#5B5880">null</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#jaoa)"><path d="M152 104H188"/><path d="M352 104H388"/><path d="M572 104H586"/></g>
<g text-anchor="middle" font-size="13" font-weight="700"><text x="82" y="176" fill="#B84508">1 · {$t[0]}</text><text x="272" y="176" fill="#5A35C8">2 · {$t[1]}</text><text x="560" y="176" fill="#5B5880">{$t[2]}</text></g>
<text x="320" y="214" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#5B5880">Object.getPrototypeOf(ada) === Eroe.prototype</text>
</svg>
SVG;
    }
}

return [
    'id' => 'js-advanced', 'family' => 'js', 'after' => 'js-intermediate',
    'title' => L('JavaScript avanzato', 'Advanced JavaScript'), 'short' => L('JS avanzato', 'JS Advanced'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Promise, async/await, fetch, moduli, classi, closure e il ciclo degli eventi, prototipi e `this`, generatori, richieste annullabili.', 'Promises, async/await, fetch, modules, classes, closures and the event loop, prototypes and `this`, generators, cancellable requests.'),
    'lessons' => [
        lesson('promises', L('Promise', 'Promises'), [
            P('Una **Promise** rappresenta un risultato che arriverà: può riuscire o fallire. Con `then` dici che cosa fare col valore, con `catch` come gestire l’errore.', 'A **Promise** stands for a result yet to come: it can succeed or fail. With `then` you say what to do with the value, with `catch` how to handle the error.'),
            P('Perché servono? Leggere dal server o aspettare un timer richiede tempo, e JavaScript non può fermare la pagina nell’attesa: la funzione restituisce subito una promise, cioè una ricevuta del risultato che arriverà. Una promise parte **in attesa** e si decide una volta sola: **risolta** con un valore o **rifiutata** con un errore.', 'Why do we need them? Reading from the server or waiting for a timer takes time, and JavaScript cannot freeze the page while it waits: the function gives back a promise straight away, a receipt for the result to come. A promise starts **pending** and is settled only once: **fulfilled** with a value or **rejected** with an error.'),
            Fig(L(fig_jsadv_promise('it'), fig_jsadv_promise('en')),
                L('Una promise in attesa ha due strade: con resolve diventa risolta e passa il valore a .then, con reject diventa rifiutata e passa l’errore a .catch. Entrambe le strade arrivano a .finally.', 'A pending promise has two paths: with resolve it becomes fulfilled and passes the value to .then, with reject it becomes rejected and passes the error to .catch. Both paths reach .finally.'),
                L('Da «in attesa» a risolta o rifiutata, una volta sola.', 'From pending to fulfilled or rejected, only once.')),
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
            Tip('Metti un solo `catch` in fondo alla catena: raccoglie l’errore di qualsiasi passo precedente. E dentro un `then` ricordati di fare `return`, altrimenti il passo successivo riceve `undefined`.', 'Put a single `catch` at the end of the chain: it collects the error from any step before it. And inside a `then` remember to `return`, otherwise the next step receives `undefined`.'),
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
            P('`await` mette in pausa solo la funzione `async`, non la pagina: intanto i click e le animazioni continuano. Errore tipico: dimenticare `await`. Allora `carica(id)` non dà l’utente ma una promise, e `utente.nome` vale `undefined`. Con `try…catch` gestisci gli errori come nel codice sincrono, al posto di `catch` nella catena.', '`await` pauses only the `async` function, not the page: meanwhile clicks and animations carry on. A typical mistake: forgetting `await`. Then `carica(id)` gives not the user but a promise, and `utente.nome` is `undefined`. With `try…catch` you handle errors as in synchronous code, instead of `catch` in the chain.'),
            Fig(L(fig_jsadv_parallel('it'), fig_jsadv_parallel('en')),
                L('Linea del tempo. In alto due await in sequenza: a() dura un secondo, poi b() un altro secondo, totale 2 secondi. In basso Promise.all avvia a() e b() insieme e finisce dopo 1 secondo.', 'A timeline. At the top two awaits in sequence: a() takes one second, then b() another second, 2 seconds in total. At the bottom Promise.all starts a() and b() together and ends after 1 second.'),
                L('Due attese indipendenti: in sequenza si sommano, con `Promise.all` si sovrappongono.', 'Two independent waits: in sequence they add up, with `Promise.all` they overlap.')),
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
            Fig(L(fig_jsadv_fetch('it'), fig_jsadv_fetch('en')),
                L('Il browser chiama fetch(url): una freccia porta la richiesta GET /api/punti al server; una freccia di ritorno porta la risposta 200 OK con il corpo {"punti":10}, che il browser controlla con r.ok e legge con r.json().', 'The browser calls fetch(url): an arrow carries the request GET /api/punti to the server; a return arrow carries the response 200 OK with the body {"punti":10}, which the browser checks with r.ok and reads with r.json().'),
                L('Ogni `fetch` è un andata e ritorno: richiesta e risposta.', 'Every `fetch` is a round trip: request and response.')),
            C('const r = await fetch("/api/punti");
if (!r.ok) throw new Error(`Errore ${r.status}`);
const dati = await r.json();

await fetch("/api/punti", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ punti: 10 }),
});', 'js'),
            P('Attenzione: `fetch` non fallisce su un errore 404 o 500, solo su problemi di rete. Per questo va controllato `r.ok`.', 'Careful: `fetch` does not fail on a 404 or 500, only on network problems. That is why you check `r.ok`.'),
            P('Il codice di **stato** racconta com’è andata: `2xx` tutto bene, `4xx` errore della richiesta (`404` non trovato, `401` non autorizzato), `5xx` errore del server. `r.json()` è a sua volta asincrono, perché il corpo può arrivare a pezzi, e si legge **una volta sola**. Per un `GET` basta l’indirizzo; per inviare dati servono `method`, gli `headers` e il `body`.', 'The **status** code tells how it went: `2xx` all good, `4xx` an error in the request (`404` not found, `401` unauthorised), `5xx` a server error. `r.json()` is asynchronous too, because the body can arrive in pieces, and it can be read **only once**. For a `GET` the address is enough; to send data you need `method`, the `headers` and the `body`.'),
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
            P('Un modulo può avere tante esportazioni **con nome**, che si importano tra graffe con lo stesso nome, e una sola esportazione **default**, che si importa senza graffe e con il nome che vuoi. Nel browser il percorso deve essere completo, estensione compresa: `./punti.js`, non `./punti`. Gli script `type="module"` partono dopo che l’HTML è stato letto, come con `defer`.', 'A module can have many **named** exports, imported between braces with the same name, and a single **default** export, imported without braces and with any name you like. In the browser the path must be complete, extension included: `./punti.js`, not `./punti`. `type="module"` scripts run after the HTML has been read, as with `defer`.'),
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
            P('Ogni `new Eroe("Ada")` crea un oggetto nuovo, un’**istanza**: dentro i metodi `this` è proprio quell’istanza. `Mago extends Eroe` riceve tutto quello che ha `Eroe` (il getter `vite`, il metodo `colpisci`) e aggiunge del suo. Le classi servono quando hai molti oggetti con gli stessi dati e comportamenti; i campi privati proteggono lo stato da modifiche sbagliate dall’esterno.', 'Every `new Eroe("Ada")` creates a new object, an **instance**: inside the methods `this` is that very instance. `Mago extends Eroe` receives everything `Eroe` has (the `vite` getter, the `colpisci` method) and adds its own. Classes are useful when you have many objects with the same data and behaviour; private fields protect the state from wrong changes from outside.'),
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
            P('Nell’esempio `contatore` ha finito da tempo, ma la funzione restituita ricorda ancora `n`: è uno stato privato, che nessun altro può toccare. Il giro dell’event loop è sempre lo stesso: prima finisce tutto lo script sulla pila delle chiamate, poi si svuotano **tutti** i microtask (le promise), poi si prende **un** task (un timer, un click) e si ricomincia.', 'In the example `contatore` finished long ago, but the function it gave back still remembers `n`: it is private state that nobody else can touch. The round of the event loop is always the same: first the whole script on the call stack finishes, then **all** the microtasks (promises) are emptied, then **one** task (a timer, a click) is taken and it starts again.'),
            Fig(L(fig_jsadv_loop('it'), fig_jsadv_loop('en')),
                L('A sinistra la pila delle chiamate con lo script principale; a destra due code: i microtask, con il then che stampa C, e i task, con il timer che stampa B. Al centro l’event loop porta i lavori dalle code alla pila, prima i microtask. In basso la console mostra A, D, C, B.', 'On the left the call stack with the main script; on the right two queues: microtasks, with the then that prints C, and tasks, with the timer that prints B. In the middle the event loop brings jobs from the queues to the stack, microtasks first. At the bottom the console shows A, D, C, B.'),
                L('Prima lo script (A, D), poi i microtask (C), poi i task (B).', 'First the script (A, D), then the microtasks (C), then the tasks (B).')),
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
            Fig(L(fig_jsadv_proto('it'), fig_jsadv_proto('en')),
                L('Una catena di quattro riquadri collegati da frecce: l’oggetto ada con nome, Eroe.prototype con saluta(), Object.prototype con toString() e hasOwnProperty(), e infine null. ada.saluta() non è su ada, viene trovato al secondo passo.', 'A chain of four boxes joined by arrows: the object ada with nome, Eroe.prototype with saluta(), Object.prototype with toString() and hasOwnProperty(), and finally null. ada.saluta() is not on ada, it is found at the second step.'),
                L('Una proprietà che manca si cerca nel prototipo, poi nel suo prototipo, fino a `null`.', 'A missing property is looked up in the prototype, then in its prototype, up to `null`.')),
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
