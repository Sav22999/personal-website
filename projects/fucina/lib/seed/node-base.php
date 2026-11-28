<?php
/** Node.js: the runtime and npm, modules and the file system, async I/O, an HTTP API with Express, environment and errors, databases, events, streams and TypeScript. */

/** Blocking against non-blocking I/O: with readFileSync the thread waits for the disk, with await readFile it serves other requests meanwhile. */
if (!function_exists('fig_node_async')) {
    function fig_node_async(string $l): string
    {
        $t = $l === 'it' ? ['readFileSync: blocca tutto', 'await readFile: intanto serve gli altri', 'thread', 'disco', 'attesa del disco', 'tempo'] : ['readFileSync: blocks everything', 'await readFile: serves the others meanwhile', 'thread', 'disk', 'waiting for the disk', 'time'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ndaa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-weight="700" font-size="14"><text x="20" y="26">{$t[0]}</text><text x="20" y="114">{$t[1]}</text></g>
<g font-size="12" fill="#5B5880"><text x="20" y="57">{$t[2]}</text><text x="20" y="145">{$t[2]}</text><text x="20" y="183">{$t[3]}</text></g>
<g stroke-width="2"><rect x="80" y="38" width="60" height="28" rx="7" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="140" y="38" width="200" height="28" rx="7" fill="#F1F0F8" stroke="#8A87B0" stroke-dasharray="5 4"/><rect x="340" y="38" width="40" height="28" rx="7" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="380" y="38" width="80" height="28" rx="7" fill="#FFF3EA" stroke="#E8590C"/><rect x="460" y="38" width="80" height="28" rx="7" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="80" y="126" width="60" height="28" rx="7" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="140" y="126" width="80" height="28" rx="7" fill="#FFF3EA" stroke="#E8590C"/><rect x="220" y="126" width="80" height="28" rx="7" fill="#D5F6EA" stroke="#0B7F5C"/><rect x="340" y="126" width="40" height="28" rx="7" fill="#DCEBFF" stroke="#2F8CF0"/>
<rect x="140" y="164" width="200" height="28" rx="7" fill="#F1F0F8" stroke="#8A87B0"/></g>
<g text-anchor="middle" font-weight="700" font-size="13"><text x="110" y="57">A</text><text x="360" y="57">A</text><text x="420" y="57">B</text><text x="500" y="57">C</text><text x="110" y="145">A</text><text x="180" y="145">B</text><text x="260" y="145">C</text><text x="360" y="145">A</text></g>
<g text-anchor="middle" font-size="12" fill="#5B5880"><text x="240" y="57">{$t[4]}</text><text x="240" y="183">A · {$t[4]}</text></g>
<path d="M80 226H624" stroke="#8A87B0" stroke-width="2" marker-end="url(#ndaa)"/>
<text x="620" y="244" text-anchor="end" font-size="13" fill="#5B5880">{$t[5]} →</text>
<g stroke="#0B7F5C" stroke-width="2" stroke-dasharray="3 3"><path d="M380 120V210"/><path d="M540 32V210"/></g>
</svg>
SVG;
    }
}

/** An Express request walking the middleware in order; a middleware can answer early, the route sends the response. */
if (!function_exists('fig_node_mw')) {
    function fig_node_mw(string $l): string
    {
        $t = $l === 'it' ? ['client', 'controllo', 'richiesta', 'risposta', 'può fermarsi qui'] : ['client', 'check', 'request', 'response', 'can stop here'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ndma" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="62" width="96" height="62" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="150" y="62" width="130" height="62" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="310" y="62" width="120" height="62" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="460" y="62" width="168" height="62" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g text-anchor="middle"><text x="60" y="98" font-weight="700">{$t[0]}</text><text x="370" y="90" font-weight="700" font-size="14">{$t[1]}</text></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="215" y="98">express.json()</text><text x="370" y="110" font-size="12">if (!testo)</text><text x="544" y="98">app.post(…)</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ndma)"><path d="M108 93H146"/><path d="M280 93H306"/><path d="M430 93H456"/><path d="M544 124V166H60V128"/></g>
<path d="M370 124V146H84V128" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4" fill="none" marker-end="url(#ndma)"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="11" fill="#5A35C8" text-anchor="middle"><text x="293" y="56">next()</text><text x="443" y="56">next()</text></g>
<text x="128" y="56" text-anchor="middle" font-size="12" fill="#5B5880">{$t[2]}</text>
<text x="300" y="188" text-anchor="middle" font-size="13"><tspan font-weight="700">{$t[3]}</tspan> · <tspan font-family="ui-monospace, Menlo, monospace" font-size="12">res.status(201).json(nota)</tspan></text>
<text x="230" y="140" text-anchor="middle" font-size="12" font-weight="700" fill="#B0245A">400 · {$t[4]}</text>
</svg>
SVG;
    }
}

/** An EventEmitter: emit calls every listener of that event at once and in order; once is removed after the first time. */
if (!function_exists('fig_node_emitter')) {
    function fig_node_emitter(string $l): string
    {
        $t = $l === 'it' ? ['subito e in ordine', 'solo la prima volta', 'nessuno ascolta «error»? il processo si ferma'] : ['at once and in order', 'only the first time', 'nobody listens to “error”? the process stops'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ndea" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="50" width="210" height="80" rx="14" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="28" y="78" font-family="ui-monospace, Menlo, monospace" font-weight="700">ordini</text>
<text x="28" y="106" font-family="ui-monospace, Menlo, monospace" font-size="13">emit("nuovo", ordine)</text>
<rect x="330" y="20" width="298" height="54" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="330" y="96" width="298" height="54" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2" stroke-dasharray="7 4"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="346" y="44"><tspan font-weight="700">1</tspan> · on("nuovo")</text><text x="346" y="120"><tspan font-weight="700">2</tspan> · once("nuovo")</text></g>
<g font-size="12" fill="#5B5880"><text x="346" y="63">→ email</text><text x="346" y="139">{$t[1]}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ndea)"><path d="M222 80C270 80 280 47 326 47"/><path d="M222 100C270 100 280 123 326 123"/></g>
<text x="276" y="160" text-anchor="middle" font-size="12" font-weight="700" fill="#1B66BC">{$t[0]}</text>
<rect x="12" y="172" width="616" height="36" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="28" y="195" font-size="13"><tspan font-family="ui-monospace, Menlo, monospace" font-weight="700">on("error")</tspan> · {$t[2]}</text>
</svg>
SVG;
    }
}

/** A stream pipeline: chunks travel from the file through gzip into the compressed file, a few at a time, with constant memory. */
if (!function_exists('fig_node_stream')) {
    function fig_node_stream(string $l): string
    {
        $t = $l === 'it' ? ['leggibile', 'trasformazione', 'scrivibile', 'memoria usata: pochi pezzi alla volta, anche per un file di 10 GB'] : ['readable', 'transform', 'writable', 'memory used: a few chunks at a time, even for a 10 GB file'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.nds{animation:nds 2.4s linear infinite}@keyframes nds{from{transform:translateX(0)}to{transform:translateX(30px)}}@media (prefers-reduced-motion:reduce){.nds{animation:none}}</style>
<g font-size="12" font-weight="700" fill="#5B5880" text-anchor="middle"><text x="96" y="40">{$t[0]}</text><text x="320" y="40">{$t[1]}</text><text x="544" y="40">{$t[2]}</text></g>
<rect x="12" y="50" width="168" height="80" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="246" y="50" width="148" height="80" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="460" y="50" width="168" height="80" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="96" y="86">accessi.log</text><text x="320" y="86">createGzip()</text><text x="544" y="86">accessi.log.gz</text></g>
<g fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"><rect x="180" y="80" width="66" height="20"/><rect x="394" y="80" width="66" height="20"/></g>
<g class="nds"><g fill="#2F8CF0"><rect x="180" y="84" width="12" height="12" rx="2"/><rect x="210" y="84" width="12" height="12" rx="2"/></g><g fill="#7048E8"><rect x="394" y="84" width="9" height="12" rx="2"/><rect x="424" y="84" width="9" height="12" rx="2"/></g></g>
<g fill="#2F8CF0" opacity="0.5"><rect x="30" y="100" width="12" height="12" rx="2"/><rect x="46" y="100" width="12" height="12" rx="2"/><rect x="62" y="100" width="12" height="12" rx="2"/><rect x="78" y="100" width="12" height="12" rx="2"/><rect x="94" y="100" width="12" height="12" rx="2"/><rect x="110" y="100" width="12" height="12" rx="2"/><rect x="126" y="100" width="12" height="12" rx="2"/><rect x="142" y="100" width="12" height="12" rx="2"/></g>
<g fill="#7048E8" opacity="0.5"><rect x="490" y="100" width="9" height="12" rx="2"/><rect x="503" y="100" width="9" height="12" rx="2"/><rect x="516" y="100" width="9" height="12" rx="2"/></g>
<text x="320" y="166" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">pipeline(read, gzip, write)</text>
<text x="320" y="198" text-anchor="middle" font-size="13" fill="#5B5880">{$t[3]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'node-base', 'family' => 'node', 'after' => 'js-intermediate',
    'title' => L('Node.js', 'Node.js'), 'short' => L('Node.js', 'Node.js'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('JavaScript sul server: il runtime e npm, file system e I/O asincrono, un’API con Express, ambiente ed errori, database, eventi, stream e TypeScript.', 'JavaScript on the server: the runtime and npm, the file system and async I/O, an API with Express, environment and errors, databases, events, streams and TypeScript.'),
    'lessons' => [
        lesson('npm', L('Node e npm', 'Node and npm'), [
            P('**Node.js** esegue JavaScript fuori dal browser: server, strumenti da riga di comando, script. Ogni progetto ha un file **`package.json`** con nome, script e **dipendenze**, che si installano con **npm** nella cartella `node_modules`.', '**Node.js** runs JavaScript outside the browser: servers, command-line tools, scripts. Every project has a **`package.json`** file with name, scripts and **dependencies**, installed with **npm** into the `node_modules` folder.'),
            C('npm init -y                 # crea package.json
npm install express         # dipendenza
npm install -D vitest       # dipendenza solo per lo sviluppo
npm run dev                 # esegue lo script "dev"
node server.js              # esegue un file', 'bash'),
            P('`package-lock.json` fissa le versioni esatte installate: va nel repository. `node_modules` invece no (si ricrea con `npm install`). In `package.json` puoi scegliere i moduli moderni con `"type": "module"`.', '`package-lock.json` pins the exact installed versions: it goes in the repository. `node_modules` does not (it is recreated with `npm install`). In `package.json` you can choose modern modules with `"type": "module"`.'),
            P('Le **dependencies** servono all’app quando gira (Express), le **devDependencies** (`-D`) solo a chi sviluppa (i test, il compilatore). Una versione come `"^5.1.0"` accetta gli aggiornamenti minori e le correzioni, ma non la `6`, che potrebbe cambiare le regole. I comandi del progetto vanno nella sezione `scripts`, così tutti li lanciano allo stesso modo: `npm run dev`, `npm test`.', '**dependencies** are needed by the app when it runs (Express), **devDependencies** (`-D`) only by whoever develops it (tests, the compiler). A version like `"^5.1.0"` accepts minor updates and fixes, but not `6`, which might change the rules. The project commands go in the `scripts` section, so everyone runs them the same way: `npm run dev`, `npm test`.'),
        ], [
            Q('n1', L('Dove sono elencate le dipendenze di un progetto?', 'Where are a project’s dependencies listed?'), ['package.json', 'node_modules', 'index.js', '.env'], 0, L('E installate in node_modules.', 'And installed in node_modules.')),
            Q('n2', L('Va messa nel repository la cartella node_modules?', 'Does the node_modules folder go into the repository?'), [L('No, si ricrea con npm install', 'No, it is recreated with npm install'), L('Sì, sempre', 'Yes, always'), L('Solo in produzione', 'Only in production'), L('Solo il lunedì', 'Only on Mondays')], 0, L('Il lock file invece sì.', 'The lock file does.')),
            Q('n3', L('Che cosa fa `npm install -D vitest`?', 'What does `npm install -D vitest` do?'), [L('Installa una dipendenza di sviluppo', 'Installs a dev dependency'), L('Cancella vitest', 'Removes vitest'), L('Avvia i test', 'Runs the tests'), L('Aggiorna Node', 'Updates Node')], 0, L('Serve solo mentre sviluppi.', 'Only needed while developing.')),
        ], task('bash', L('Scrivi i comandi per creare un `package.json` con `npm init -y` e installare `express`.', 'Write the commands to create a `package.json` with `npm init -y` and install `express`.'), '', [re('npm\s+init\s+-y', L('Crei il progetto', 'You create the project')), re('npm\s+(install|i)\s+express', L('Installi Express', 'You install Express'))], '', "npm init -y\nnpm install express"), [
            dMatch(pMatch(), [['package.json', L('Descrive il progetto', 'Describes the project')], ['package-lock.json', L('Versioni esatte', 'Exact versions')], ['node_modules', L('Pacchetti installati', 'Installed packages')], ['npm run dev', L('Esegue uno script', 'Runs a script')], ['-D', L('Dipendenza di sviluppo', 'Dev dependency')]], L('Il mondo npm.', 'The npm world.')),
            dFlash(pFlash(), [[L('Node esegue JavaScript fuori dal browser', 'Node runs JavaScript outside the browser'), true], [L('package-lock.json va ignorato da git', 'package-lock.json should be git-ignored'), false], [L('npm install ricrea node_modules', 'npm install recreates node_modules'), true], [L('Node funziona solo su Windows', 'Node only works on Windows'), false]], L('Le basi di Node.', 'Node basics.')),
        ]),
        lesson('async', L('File system e asincronia', 'File system and async'), [
            P('Node fa I/O **senza bloccare**: mentre aspetta il disco o la rete, serve altre richieste. Le API moderne restituiscono **promesse** e si usano con `async`/`await`. I moduli integrati si importano con il prefisso `node:`.', 'Node does I/O **without blocking**: while waiting for disk or network, it serves other requests. Modern APIs return **promises** and are used with `async`/`await`. Built-in modules are imported with the `node:` prefix.'),
            C('import { readFile, writeFile } from "node:fs/promises";
import path from "node:path";

const file = path.join(import.meta.dirname, "dati", "note.json");
try {
  const note = JSON.parse(await readFile(file, "utf8"));
  note.push({ testo: "Nuova nota", data: new Date().toISOString() });
  await writeFile(file, JSON.stringify(note, null, 2));
} catch (err) {
  console.error("Impossibile aggiornare le note:", err.message);
}', 'js'),
            P('Evita le versioni “Sync” (`readFileSync`) in un server: bloccano tutto finché non finiscono. `path.join` costruisce percorsi corretti su ogni sistema operativo.', 'Avoid the “Sync” versions (`readFileSync`) in a server: they block everything until done. `path.join` builds correct paths on every operating system.'),
            Fig(L(fig_node_async('it'), fig_node_async('en')),
                L('Due linee del tempo con le richieste A, B e C. In alto, con readFileSync, il thread resta fermo ad aspettare il disco per A, e B e C partono solo dopo. In basso, con await readFile, mentre il disco lavora per A il thread serve B e C, poi completa A: tutto finisce prima.', 'Two timelines with requests A, B and C. At the top, with readFileSync, the thread sits waiting for the disk for A, and B and C start only afterwards. At the bottom, with await readFile, while the disk works for A the thread serves B and C, then completes A: everything finishes sooner.'),
                L('Mentre il disco lavora, Node non sta fermo: serve le altre richieste.', 'While the disk works, Node does not sit idle: it serves the other requests.')),
            P('Un solo thread esegue il tuo JavaScript: se lo blocchi con un calcolo lungo o una funzione `Sync`, ogni altro utente aspetta. Gli errori di file system arrivano come eccezioni, con un `code` che li descrive (`ENOENT` significa che il file non esiste): gestiscili con `try…catch`. Per leggere più file insieme, avvia le letture e attendile con `Promise.all`.', 'A single thread runs your JavaScript: if you block it with a long calculation or a `Sync` function, every other user waits. File system errors arrive as exceptions, with a `code` describing them (`ENOENT` means the file does not exist): handle them with `try…catch`. To read several files together, start the reads and wait for them with `Promise.all`.'),
        ], [
            Q('a1', L('Perché evitare `readFileSync` in un server?', 'Why avoid `readFileSync` in a server?'), [L('Blocca tutte le altre richieste', 'It blocks all other requests'), L('Non esiste', 'It does not exist'), L('Legge solo immagini', 'It only reads images'), L('È più sicuro', 'It is safer')], 0, L('Node è a thread singolo per il tuo codice.', 'Node is single-threaded for your code.')),
            Q('a2', L('Come si importano i moduli integrati in modo moderno?', 'How are built-in modules imported the modern way?'), [L('Con il prefisso node:', 'With the node: prefix'), L('Con require soltanto', 'With require only'), L('Da npm', 'From npm'), L('Non si importano', 'They are not imported')], 0, L('Per esempio `node:fs/promises`.', 'For example `node:fs/promises`.')),
            Q('a3', L('A che cosa serve `path.join`?', 'What is `path.join` for?'), [L('Costruire percorsi validi su ogni sistema', 'Building valid paths on every system'), L('Unire due file', 'Merging two files'), L('Creare cartelle', 'Creating folders'), L('Leggere file', 'Reading files')], 0, L('Gestisce le barre / e \\.', 'It handles / and \\ slashes.')),
        ], task('js', L('Importa `readFile` da `node:fs/promises` e leggi `config.json` in `utf8` con `await`, salvando il testo in `testo`.', 'Import `readFile` from `node:fs/promises` and read `config.json` as `utf8` with `await`, storing the text in `testo`.'), '', [re('import\s*\{\s*readFile\s*\}\s*from\s*["\']node:fs/promises["\']', L('L’import', 'The import')), re('const\s+testo\s*=\s*await\s+readFile\s*\(\s*["\']config\.json["\']\s*,\s*["\']utf-?8["\']\s*\)', L('La lettura asincrona', 'The async read'))], '', "import { readFile } from \"node:fs/promises\";\nconst testo = await readFile(\"config.json\", \"utf8\");"), [
            dBug(pBug(), ['import { readFile } from "node:fs/promises";', 'const dati = readFile("dati.json", "utf8");', 'console.log(JSON.parse(dati));'], 1, 'const dati = await readFile("dati.json", "utf8");', L('Senza `await` hai una promessa, non il testo.', 'Without `await` you get a promise, not the text.')),
            dMatch(pMatch(), [['node:fs/promises', L('File con promesse', 'Files with promises')], ['node:path', L('Percorsi', 'Paths')], ['await', L('Aspetta la promessa', 'Waits for the promise')], ['readFileSync', L('Blocca il processo', 'Blocks the process')]], L('I/O in Node.', 'I/O in Node.')),
        ]),
        lesson('express', L('Un’API con Express', 'An API with Express'), [
            P('**Express** è il framework web più usato per Node. Definisci **rotte** (metodo + percorso) e **middleware**, funzioni che elaborano la richiesta in sequenza: leggere il JSON, controllare l’accesso, registrare i log.', '**Express** is the most used web framework for Node. You define **routes** (method + path) and **middleware**, functions that process the request in sequence: parsing JSON, checking access, logging.'),
            C('import express from "express";

const app = express();
app.use(express.json());                    // middleware: legge il corpo JSON

const note = [];
app.get("/api/note", (req, res) => res.json(note));
app.post("/api/note", (req, res) => {
  if (!req.body.testo) return res.status(400).json({ errore: "testo obbligatorio" });
  const nota = { id: note.length + 1, testo: req.body.testo };
  note.push(nota);
  res.status(201).json(nota);
});
app.get("/api/note/:id", (req, res) => {
  const nota = note.find(n => n.id === Number(req.params.id));
  nota ? res.json(nota) : res.status(404).json({ errore: "non trovata" });
});

app.listen(3000, () => console.log("Pronto su http://localhost:3000"));', 'js'),
            P('`req.params` contiene i segmenti come `:id`, `req.query` i parametri dopo `?`, `req.body` il corpo. Usa i codici giusti: **201** creato, **400** richiesta sbagliata, **404** non trovato.', '`req.params` holds segments like `:id`, `req.query` the parameters after `?`, `req.body` the body. Use the right codes: **201** created, **400** bad request, **404** not found.'),
            Fig(L(fig_node_mw('it'), fig_node_mw('en')),
                L('Una richiesta parte dal client e attraversa in ordine express.json(), un controllo if (!testo) e il gestore app.post(…), passando con next(). Il gestore manda la risposta res.status(201).json(nota) al client; il controllo può fermarsi prima e rispondere 400, con una freccia tratteggiata.', 'A request leaves the client and goes in order through express.json(), an if (!testo) check and the app.post(…) handler, moving on with next(). The handler sends the response res.status(201).json(nota) back to the client; the check can stop earlier and answer 400, with a dashed arrow.'),
                L('Ogni middleware lavora sulla richiesta e la passa avanti, oppure risponde subito.', 'Every middleware works on the request and passes it on, or answers straight away.')),
            P('L’ordine conta: un middleware vale solo per le rotte scritte **dopo** di lui, per questo `express.json()` sta in cima. Ogni richiesta riceve **una sola** risposta: dopo `res.json()` non puoi mandarne un’altra, ed è per questo che nell’esempio c’è `return` davanti al 400. Un `app.use` finale senza percorso fa da pagina 404 per tutto quello che nessuna rotta ha preso.', 'Order matters: a middleware applies only to routes written **after** it, which is why `express.json()` sits at the top. Every request gets **a single** response: after `res.json()` you cannot send another, which is why the example has `return` before the 400. A final `app.use` with no path acts as the 404 page for everything no route has caught.'),
        ], [
            Q('e1', L('Dove trovi il valore di `:id` nella rotta?', 'Where do you find the value of `:id` in the route?'), ['req.params.id', 'req.query.id', 'req.body.id', 'req.id'], 0, L('`req.query` è per `?id=…`.', '`req.query` is for `?id=…`.')),
            Q('e2', L('Che cosa fa `app.use(express.json())`?', 'What does `app.use(express.json())` do?'), [L('Legge il corpo JSON delle richieste', 'Parses requests’ JSON body'), L('Risponde sempre in JSON', 'Always answers in JSON'), L('Avvia il server', 'Starts the server'), L('Crea una rotta', 'Creates a route')], 0, L('Senza, req.body è vuoto.', 'Without it, req.body is empty.')),
            Q('e3', L('Quale codice per una risorsa appena creata?', 'Which code for a newly created resource?'), ['201', '200', '404', '500'], 0, L('Created.', 'Created.')),
        ], task('js', L('Aggiungi una rotta `app.get("/api/salute", …)` che risponde con `res.json({ stato: "ok" })`.', 'Add a route `app.get("/api/salute", …)` that responds with `res.json({ stato: "ok" })`.'), "import express from \"express\";\nconst app = express();\n", [re('app\.get\s*\(\s*["\']/api/salute["\']\s*,\s*\(\s*req\s*,\s*res\s*\)\s*=>', L('La rotta', 'The route')), re('res\.json\s*\(\s*\{\s*stato\s*:\s*["\']ok["\']\s*\}\s*\)', L('La risposta', 'The response'))], '', "app.get(\"/api/salute\", (req, res) => res.json({ stato: \"ok\" }));"), [
            dOrder(pSort(), ['import express from "express";', 'const app = express();', 'app.use(express.json());', 'app.get("/api/note", (req, res) => res.json(note));', 'app.listen(3000);'], L('Crea, configura, definisci, ascolta.', 'Create, configure, define, listen.')),
            dMatch(pMatch(), [['req.params', L('Segmenti del percorso', 'Path segments')], ['req.query', L('Parametri dopo ?', 'Parameters after ?')], ['req.body', L('Corpo della richiesta', 'Request body')], ['res.status(404)', L('Non trovato', 'Not found')], ['app.use', L('Aggiunge un middleware', 'Adds middleware')]], L('Il lessico di Express.', 'Express vocabulary.')),
        ]),
        lesson('env', L('Ambiente ed errori', 'Environment and errors'), [
            P('Configurazione e segreti (porta, password del database, chiavi API) stanno nelle **variabili d’ambiente**, lette con `process.env`. In sviluppo si mettono in un file `.env` (escluso da git), caricato con `node --env-file=.env`.', 'Configuration and secrets (port, database password, API keys) live in **environment variables**, read with `process.env`. In development they go in a `.env` file (excluded from git), loaded with `node --env-file=.env`.'),
            C('// node --env-file=.env server.js
const porta = Number(process.env.PORT ?? 3000);
if (!process.env.DATABASE_URL) {
  console.error("Manca DATABASE_URL");
  process.exit(1);
}

// middleware di errore di Express: quattro argomenti
app.use((err, req, res, next) => {
  console.error(err);
  res.status(500).json({ errore: "Errore interno" });   // niente dettagli all’utente
});', 'js'),
            P('Gli errori non gestiti fanno cadere il processo: gestiscili con `try/catch` e con il middleware di errore (da Express 5 un errore lanciato in un gestore `async` ci arriva da solo), registrali nei log e non mostrare mai all’utente stack trace o messaggi interni. In produzione si usa un gestore di processi o un container che riavvia l’app se cade.', 'Unhandled errors crash the process: handle them with `try/catch` and the error middleware (since Express 5 an error thrown in an `async` handler reaches it by itself), log them and never show users stack traces or internal messages. In production you use a process manager or container that restarts the app if it crashes.'),
        ], [
            Q('v1', L('Come leggi una variabile d’ambiente in Node?', 'How do you read an environment variable in Node?'), ['process.env.NOME', 'env("NOME")', 'window.env.NOME', 'import.env.NOME'], 0, L('Sempre stringhe (o undefined).', 'Always strings (or undefined).')),
            Q('v2', L('Il file .env va nel repository?', 'Does the .env file go into the repository?'), [L('No, contiene segreti', 'No, it holds secrets'), L('Sì, sempre', 'Yes, always'), L('Solo se è piccolo', 'Only if small'), L('Solo in produzione', 'Only in production')], 0, L('Escludilo con .gitignore.', 'Exclude it with .gitignore.')),
            Q('v3', L('Quanti argomenti ha un middleware di errore di Express?', 'How many arguments does an Express error middleware have?'), ['4', '2', '3', '1'], 0, L('(err, req, res, next).', '(err, req, res, next).')),
        ], task('js', L('Leggi la porta da `process.env.PORT` con valore predefinito `8080` e salvala come numero in `porta`.', 'Read the port from `process.env.PORT` with default `8080` and store it as a number in `porta`.'), '', [re('const\s+porta\s*=\s*Number\s*\(\s*process\.env\.PORT\s*(\?\?|\|\|)\s*8080\s*\)|const\s+porta\s*=\s*Number\s*\(\s*process\.env\.PORT\s*\)\s*(\?\?|\|\|)\s*8080', L('Porta con valore predefinito', 'Port with default'))], '', 'const porta = Number(process.env.PORT ?? 8080);'), [
            dBug(pBug(), ['app.use((err, req, res, next) => {', '  res.status(500).json({ errore: err.stack });', '});'], 1, '  res.status(500).json({ errore: "Errore interno" });', L('Lo stack trace non va mostrato all’utente.', 'The stack trace must not be shown to the user.')),
            dFlash(pFlash(), [[L('I segreti vanno nelle variabili d’ambiente', 'Secrets belong in environment variables'), true], [L('process.env restituisce numeri', 'process.env returns numbers'), false], [L('Un errore non gestito può far cadere il processo', 'An unhandled error can crash the process'), true], [L('Conviene mostrare lo stack trace agli utenti', 'Showing stack traces to users is wise'), false]], L('Robusto e riservato.', 'Robust and discreet.')),
        ]),
        lesson('database', L('Database, validazione e test', 'Databases, validation and tests'), [
            P('Un’API vera salva i dati in un database. Con un client come `pg` (PostgreSQL) usa sempre le **query parametrizzate** (`$1`, `$2`): i dati dell’utente non diventano mai codice SQL. Prima di salvare, **valida** l’input e rispondi `400` se manca qualcosa.', 'A real API saves data in a database. With a client such as `pg` (PostgreSQL) always use **parameterised queries** (`$1`, `$2`): user data never becomes SQL code. Before saving, **validate** the input and answer `400` if something is missing.'),
            C('app.post("/api/note", async (req, res) => {
  const { testo } = req.body;
  if (!testo || testo.length > 500) {
    return res.status(400).json({ errore: "Testo mancante o troppo lungo" });
  }
  const r = await pool.query("INSERT INTO note (testo) VALUES ($1) RETURNING id", [testo]);
  res.status(201).json({ id: r.rows[0].id });
});', 'js'),
            P('Node ha un test runner incluso: `node --test`. Con `node:test` e `node:assert` provi le funzioni; con librerie come `supertest` chiami le rotte senza avviare il server sul serio.', 'Node has a built-in test runner: `node --test`. With `node:test` and `node:assert` you test functions; with libraries like `supertest` you call routes without really starting the server.'),
        ], [
            Q('db1', L('Perché le query parametrizzate?', 'Why parameterised queries?'), [L('Evitano la SQL injection', 'They prevent SQL injection'), L('Sono più corte', 'They are shorter'), L('Servono solo per MySQL', 'They are only for MySQL'), L('Disattivano gli indici', 'They disable indexes')], 0, L('I dati restano dati.', 'Data stays data.')),
            Q('db2', L('Che codice rispondi a un input non valido?', 'Which code do you answer to invalid input?'), ['400', '200', '500', '301'], 0, L('Errore del client.', 'Client error.')),
            Q('db3', L('Come avvii i test integrati di Node?', 'How do you run Node’s built-in tests?'), ['node --test', 'npm build', 'node --inspect', 'npx serve'], 0, L('Con `node:test` e `node:assert`.', 'With `node:test` and `node:assert`.')),
        ], task('js', L('Esegui `pool.query` con la query `SELECT * FROM utenti WHERE id = $1` e il parametro `[id]`.', 'Run `pool.query` with the query `SELECT * FROM utenti WHERE id = $1` and the parameter `[id]`.'), '', [re('pool\.query\(\s*["\'`]SELECT \* FROM utenti WHERE id = \$1["\'`]\s*,\s*\[\s*id\s*\]\s*\)', L('La query è parametrizzata', 'The query is parameterised'))], '', 'const r = await pool.query("SELECT * FROM utenti WHERE id = $1", [id]);'), [
            dBug(pBug(), ['app.get("/api/utenti/:id", async (req, res) => {', '  const sql = "SELECT * FROM utenti WHERE id = " + req.params.id;', '  const r = await pool.query(sql);', '  res.json(r.rows[0]);', '});'], 1, '  const r = await pool.query("SELECT * FROM utenti WHERE id = $1", [req.params.id]);', L('Concatenare l’input apre alla SQL injection.', 'Concatenating input opens SQL injection.')),
            dFlash(pFlash(), [[L('201 significa risorsa creata', '201 means resource created'), true], [L('Si può fidarsi di req.body senza controlli', 'req.body can be trusted without checks'), false], [L('Node ha un test runner incluso', 'Node has a built-in test runner'), true], [L('I parametri $1 vanno concatenati a mano', '$1 parameters must be concatenated by hand'), false]], L('API sicure e provate.', 'Safe, tested APIs.')),
        ]),
        lesson('events', L('Eventi ed EventEmitter', 'Events and EventEmitter'), [
            P('Molti oggetti di Node **emettono eventi**: un server quando arriva una richiesta, uno stream quando arrivano dati. Alla base c’è `EventEmitter`: con `on` registri un ascoltatore, con `emit` lanci un evento insieme ai suoi dati.', 'Many Node objects **emit events**: a server when a request arrives, a stream when data arrives. At the core is `EventEmitter`: with `on` you register a listener, with `emit` you fire an event together with its data.'),
            C('import { EventEmitter } from "node:events";

class Ordini extends EventEmitter {
  crea(prodotto) {
    const ordine = { id: Date.now(), prodotto };
    this.emit("nuovo", ordine);
    return ordine;
  }
}

const ordini = new Ordini();
ordini.on("nuovo", o => console.log("Email per l’ordine", o.id));
ordini.once("nuovo", () => console.log("Primo ordine della giornata!"));
ordini.on("error", err => console.error("Problema:", err.message));
ordini.crea("Tazza");', 'js'),
            P('Gli ascoltatori vengono chiamati **subito e in ordine**, in modo sincrono, quando chiami `emit`. `once` ascolta una volta sola, `off` toglie un ascoltatore. Un evento `"error"` senza ascoltatori fa cadere il processo: se un oggetto può emettere errori, ascoltali sempre.', 'Listeners are called **at once and in order**, synchronously, when you call `emit`. `once` listens only once, `off` removes a listener. An `"error"` event with no listeners crashes the process: if an object can emit errors, always listen to them.'),
            Fig(L(fig_node_emitter('it'), fig_node_emitter('en')),
                L('L’oggetto ordini chiama emit("nuovo", ordine): due frecce raggiungono subito e in ordine i listener 1, on("nuovo"), che manda un’email, e 2, once("nuovo"), tratteggiato perché vale solo la prima volta. Sotto, una nota: se nessuno ascolta «error», il processo si ferma.', 'The ordini object calls emit("nuovo", ordine): two arrows reach at once and in order listener 1, on("nuovo"), which sends an email, and listener 2, once("nuovo"), dashed because it applies only the first time. Below, a note: if nobody listens to “error”, the process stops.'),
                L('Un `emit`, tanti ascoltatori: chi emette non sa chi ascolta.', 'One `emit`, many listeners: whoever emits does not know who listens.')),
        ], [
            Q('ev1', L('Con che cosa si lancia un evento?', 'What fires an event?'), ['on()', 'emit()', 'listen()', 'send()'], 1, L('`on` invece ascolta.', '`on` listens instead.')),
            Q('ev2', L('Che cosa fa `once`?', 'What does `once` do?'), [L('Lancia l’evento una volta', 'Fires the event once'), L('Blocca l’evento', 'Blocks the event'), L('Ripete l’evento ogni secondo', 'Repeats the event every second'), L('Ascolta l’evento una volta sola', 'Listens to the event only once')], 3, L('Dopo la prima volta l’ascoltatore si toglie da solo.', 'After the first time the listener removes itself.')),
            Q('ev3', L('Che cosa succede con un evento `"error"` senza ascoltatori?', 'What happens with an `"error"` event and no listeners?'), [L('Viene ignorato', 'It is ignored'), L('Finisce in un log automatico', 'It ends up in an automatic log'), L('Il processo cade con un errore', 'The process crashes with an error'), L('Si ripete', 'It repeats')], 2, L('Per questo `"error"` va sempre ascoltato.', 'That is why `"error"` must always be listened to.')),
        ], task('js', L('Registra su `ordini` un ascoltatore di `"nuovo"` con `on`, poi lancia l’evento con `emit("nuovo", { id: 1 })`.', 'Register a `"nuovo"` listener on `ordini` with `on`, then fire the event with `emit("nuovo", { id: 1 })`.'), 'import { EventEmitter } from "node:events";
const ordini = new EventEmitter();
', [re('ordini\.on\s*\(\s*["\']nuovo["\']\s*,', L('Ascolti l’evento', 'You listen to the event')), re('ordini\.emit\s*\(\s*["\']nuovo["\']\s*,\s*\{\s*id\s*:\s*1\s*\}\s*\)', L('Lanci l’evento', 'You fire the event'))], '', 'import { EventEmitter } from "node:events";
const ordini = new EventEmitter();
ordini.on("nuovo", o => console.log(o.id));
ordini.emit("nuovo", { id: 1 });'), [
            dOrder(pSort(), ['import { EventEmitter } from "node:events";', 'const bus = new EventEmitter();', 'bus.on("saluto", nome => console.log("Ciao", nome));', 'bus.emit("saluto", "Ada");'], L('Prima si ascolta, poi si lancia: un evento emesso prima non viene ricevuto.', 'First listen, then fire: an event emitted earlier is not received.')),
            dMatch(pMatch(), [['on', L('Ascolta sempre', 'Always listens')], ['once', L('Ascolta una volta', 'Listens once')], ['emit', L('Lancia un evento', 'Fires an event')], ['off', L('Smette di ascoltare', 'Stops listening')], ['"error"', L('Evento da ascoltare sempre', 'Event to always listen to')]], L('Il vocabolario degli eventi.', 'The vocabulary of events.')),
        ]),
        lesson('streams', L('Stream e pipeline', 'Streams and pipeline'), [
            P('Leggere un file enorme tutto in memoria è lento e rischioso. Gli **stream** lavorano a pezzi (chunk): leggono, trasformano e scrivono un po’ alla volta, con memoria costante. `pipeline` collega più stream e gestisce errori e chiusura.', 'Reading a huge file entirely into memory is slow and risky. **Streams** work in pieces (chunks): they read, transform and write a little at a time, with constant memory. `pipeline` connects several streams and handles errors and closing.'),
            C('import { createReadStream, createWriteStream } from "node:fs";
import { pipeline } from "node:stream/promises";
import { createGzip } from "node:zlib";
import { createInterface } from "node:readline";

// comprime un log di qualsiasi dimensione
await pipeline(
  createReadStream("accessi.log"),
  createGzip(),
  createWriteStream("accessi.log.gz"),
);

// legge un file riga per riga
const righe = createInterface({ input: createReadStream("accessi.log") });
let errori = 0;
for await (const riga of righe) {
  if (riga.includes("ERROR")) errori++;
}', 'js'),
            P('Ci sono stream **leggibili** (un file, una richiesta HTTP), **scrivibili** (un file, una risposta) e di **trasformazione** (compressione, cifratura). Preferisci `pipeline` a `.pipe()`: se un passaggio fallisce, chiude tutti gli stream e ti restituisce l’errore.', 'There are **readable** streams (a file, an HTTP request), **writable** ones (a file, a response) and **transform** ones (compression, encryption). Prefer `pipeline` over `.pipe()`: if a step fails, it closes every stream and gives you the error.'),
            Fig(L(fig_node_stream('it'), fig_node_stream('en')),
                L('Tre riquadri collegati da tubi: il file leggibile accessi.log, la trasformazione createGzip() e il file scrivibile accessi.log.gz. Piccoli pezzi azzurri escono dal file, diventano più piccoli dopo gzip ed entrano nel file compresso. Sotto: pipeline(read, gzip, write) e una nota sulla memoria costante.', 'Three boxes joined by pipes: the readable file accessi.log, the createGzip() transform and the writable file accessi.log.gz. Small blue chunks leave the file, get smaller after gzip and enter the compressed file. Below: pipeline(read, gzip, write) and a note on constant memory.'),
                L('I dati scorrono a pezzi: in memoria ce ne sono sempre solo pochi.', 'Data flows in chunks: only a few are in memory at any time.')),
        ], [
            Q('st1', L('Perché usare uno stream per un file di 5 GB?', 'Why use a stream for a 5 GB file?'), [L('È l’unico modo di aprire un file', 'It is the only way to open a file'), L('Rende il file più piccolo', 'It makes the file smaller'), L('Evita di usare il disco', 'It avoids using the disk'), L('Lavora a pezzi con poca memoria', 'It works in pieces with little memory')], 3, L('La memoria usata resta piccola qualunque sia il file.', 'Memory use stays small whatever the file.')),
            Q('st2', L('Che vantaggio ha `pipeline` su `.pipe()`?', 'What advantage does `pipeline` have over `.pipe()`?'), [L('Gestisce errori e chiusura di tutti gli stream', 'It handles errors and closing for every stream'), L('È più corta da scrivere', 'It is shorter to write'), L('Funziona solo con i file', 'It only works with files'), L('Nessun vantaggio', 'No advantage')], 0, L('Niente stream lasciati aperti dopo un errore.', 'No streams left open after an error.')),
            Q('st3', L('Che tipo di stream è `createGzip()`?', 'What kind of stream is `createGzip()`?'), [L('Leggibile', 'Readable'), L('Scrivibile', 'Writable'), L('Di trasformazione', 'Transform'), L('Nessuno', 'None')], 2, L('Riceve dati, li comprime e li passa avanti.', 'It receives data, compresses it and passes it on.')),
        ], task('js', L('Copia `foto.jpg` in `copia.jpg` con `await pipeline(…)`, `createReadStream` e `createWriteStream`.', 'Copy `foto.jpg` to `copia.jpg` with `await pipeline(…)`, `createReadStream` and `createWriteStream`.'), 'import { createReadStream, createWriteStream } from "node:fs";
import { pipeline } from "node:stream/promises";
', [re('await\s+pipeline\s*\(', L('Usi `pipeline`', 'You use `pipeline`')), re('createReadStream\s*\(\s*["\']foto\.jpg["\']\s*\)', L('Leggi la foto', 'You read the photo')), re('createWriteStream\s*\(\s*["\']copia\.jpg["\']\s*\)', L('Scrivi la copia', 'You write the copy'))], '', 'import { createReadStream, createWriteStream } from "node:fs";
import { pipeline } from "node:stream/promises";
await pipeline(createReadStream("foto.jpg"), createWriteStream("copia.jpg"));'), [
            dMatch(pMatch(), [['createReadStream', L('Stream leggibile da file', 'Readable stream from a file')], ['createWriteStream', L('Stream scrivibile su file', 'Writable stream to a file')], ['createGzip', L('Trasforma comprimendo', 'Transforms by compressing')], ['pipeline', L('Collega gli stream e gestisce gli errori', 'Connects streams and handles errors')], ['for await', L('Legge a pezzi in un ciclo', 'Reads in pieces in a loop')]], L('Dati che scorrono a pezzi.', 'Data flowing in pieces.')),
            dFlash(pFlash(), [[L('Uno stream tiene in memoria tutto il file', 'A stream keeps the whole file in memory'), false], [L('`pipeline` chiude gli stream se uno fallisce', '`pipeline` closes the streams if one fails'), true], [L('Una risposta HTTP è uno stream scrivibile', 'An HTTP response is a writable stream'), true], [L('Gli stream servono solo per i file', 'Streams are only for files'), false]], L('File, rete, compressione: tutto può scorrere.', 'Files, network, compression: everything can flow.')),
        ]),
        lesson('typescript', L('TypeScript in Node', 'TypeScript in Node'), [
            P('Le versioni recenti di Node (dalla 22.18 e dalla 23.6 in poi) eseguono direttamente i file `.ts`: **tolgono i tipi** e avviano il JavaScript che resta. Non controllano i tipi: quel lavoro resta a `tsc --noEmit` o all’editor.', 'Recent Node versions (22.18 and 23.6 onwards) run `.ts` files directly: they **strip the types** and start the JavaScript that remains. They do not check types: that job stays with `tsc --noEmit` or the editor.'),
            C('node server.ts          # esegue togliendo i tipi
node --watch server.ts  # riparte a ogni modifica
npx tsc --noEmit        # controlla i tipi', 'bash'),
            C('// server.ts
import { createServer, type IncomingMessage, type ServerResponse } from "node:http";
import { saluta } from "./saluti.ts";   // estensione obbligatoria

createServer((req: IncomingMessage, res: ServerResponse) => {
  res.end(saluta("Ada"));
}).listen(3000);', 'ts'),
            P('Funziona solo la sintassi che si può **cancellare**: niente `enum`, `namespace` con codice o proprietà nei parametri del costruttore. Negli import scrivi l’estensione `.ts` e usa `type` per gli import di soli tipi. Nel `tsconfig.json` le opzioni `erasableSyntaxOnly` e `verbatimModuleSyntax` ti avvisano prima di eseguire.', 'Only syntax that can be **erased** works: no `enum`, no `namespace` with code, no properties in constructor parameters. In imports write the `.ts` extension and use `type` for type-only imports. In `tsconfig.json` the `erasableSyntaxOnly` and `verbatimModuleSyntax` options warn you before running.'),
        ], [
            Q('nt1', L('Che cosa fa `node app.ts`?', 'What does `node app.ts` do?'), [L('Controlla i tipi e poi esegue', 'Checks the types, then runs'), L('Toglie i tipi ed esegue senza controllarli', 'Strips the types and runs without checking them'), L('Lo compila in un eseguibile', 'Compiles it into an executable'), L('Dà sempre errore', 'Always errors')], 1, L('Per i controlli serve `tsc`.', 'Checks need `tsc`.')),
            Q('nt2', L('Come si scrive un import locale in un file eseguito così?', 'How do you write a local import in a file run this way?'), ['import { a } from "./a"', 'require("a")', 'import { a } from "./a.ts"', 'import a from a'], 2, L('L’estensione è obbligatoria.', 'The extension is mandatory.')),
            Q('nt3', L('Quale di queste Node NON sa eseguire togliendo i tipi?', 'Which of these can Node NOT run by stripping types?'), ['type Id = string', 'let n: number = 1', 'interface Utente { nome: string }', 'enum Stato { Attivo }'], 3, L('Un `enum` genera codice, non basta cancellarlo.', 'An `enum` generates code, erasing it is not enough.')),
        ], task('bash', L('Scrivi i comandi per controllare i tipi con `npx tsc --noEmit` e poi avviare `server.ts` con `node --watch`.', 'Write the commands to check types with `npx tsc --noEmit` and then start `server.ts` with `node --watch`.'), '', [re('(npx\s+)?tsc\s+--noEmit', L('Controlli i tipi', 'You check the types')), re('node\s+--watch\s+server\.ts', L('Avvii il server in modalità watch', 'You start the server in watch mode'))], '', 'npx tsc --noEmit
node --watch server.ts'), [
            dMatch(pMatch(), [['node app.ts', L('Esegue togliendo i tipi', 'Runs by stripping types')], ['node --watch', L('Riparte a ogni modifica', 'Restarts on every change')], ['tsc --noEmit', L('Controlla i tipi', 'Checks types')], ['import type', L('Import di soli tipi', 'Type-only import')], ['erasableSyntaxOnly', L('Avvisa sulla sintassi non cancellabile', 'Warns about non-erasable syntax')]], L('TypeScript senza passaggio di build.', 'TypeScript without a build step.')),
            dBug(pBug(), ['import { createServer } from "node:http";', 'import { saluta } from "./saluti";', 'createServer((req, res) => res.end(saluta("Ada"))).listen(3000);'], 1, 'import { saluta } from "./saluti.ts";', L('Node vuole l’estensione nei percorsi locali.', 'Node wants the extension in local paths.')),
        ]),
    ],
];
