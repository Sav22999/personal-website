<?php
/** Next.js: the App Router and file-based routes, server and client components, data fetching and caching, route handlers and deployment, loading and error files, Cache Components, proxy. */

/** The address /corsi/react drawn as nested files: the root layout stays around, the page of the [id] folder fills the middle. */
if (!function_exists('fig_next_routes')) {
    function fig_next_routes(string $l): string
    {
        $t = $l === 'it' ? ['intestazione e menu', 'resta tra una pagina e l’altra', 'cambia con l’indirizzo'] : ['header and menu', 'stays from one page to the next', 'changes with the address'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="12" y="12" width="616" height="246" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="12" y="12" width="616" height="40" rx="14" fill="#1E1C52"/><rect x="12" y="36" width="616" height="16" fill="#1E1C52"/>
<g fill="#FFFFFF" opacity="0.5"><circle cx="34" cy="32" r="5"/><circle cx="52" cy="32" r="5"/><circle cx="70" cy="32" r="5"/></g>
<rect x="96" y="20" width="300" height="24" rx="12" fill="#FFFFFF"/>
<text x="110" y="37" font-family="ui-monospace, Menlo, monospace" font-size="13">fucina.app<tspan font-weight="700" fill="#B84508">/corsi/react</tspan></text>
<rect x="28" y="66" width="584" height="178" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="44" y="90" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#5A35C8">app/layout.tsx</text>
<text x="200" y="90" font-size="13">{$t[0]} · <tspan fill="#5B5880">{$t[1]}</tspan></text>
<rect x="44" y="104" width="552" height="124" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2" stroke-dasharray="7 4"/>
<text x="60" y="128" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" fill="#B84508">app/corsi/[id]/page.tsx</text>
<text x="60" y="150" font-size="13" fill="#5B5880">{$t[2]}</text>
<text x="60" y="196" font-family="ui-monospace, Menlo, monospace" font-size="16">&lt;h1&gt;Corso: react&lt;/h1&gt;</text>
<rect x="400" y="178" width="180" height="32" rx="8" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/>
<text x="490" y="199" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">id = "react"</text>
</svg>
SVG;
    }
}

/** Server and client components: the server sends ready HTML, only the interactive island also ships its JavaScript. */
if (!function_exists('fig_next_islands')) {
    function fig_next_islands(string $l): string
    {
        $t = $l === 'it' ? ['sul server', 'nel browser', 'legge il database', 'solo HTML', 'HTML + JS', 'isola interattiva'] : ['on the server', 'in the browser', 'reads the database', 'HTML only', 'HTML + JS', 'interactive island'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="nxia" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-weight="700" font-size="15"><text x="20" y="30">{$t[0]}</text><text x="360" y="30">{$t[1]}</text></g>
<rect x="20" y="42" width="260" height="200" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"><rect x="36" y="58" width="228" height="48" rx="10"/><rect x="36" y="118" width="228" height="48" rx="10"/></g>
<rect x="36" y="178" width="228" height="48" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="50" y="80">Pagina</text><text x="50" y="140">ListaCorsi</text><text x="50" y="200">MiPiace</text></g>
<g font-size="12" fill="#5B5880"><text x="50" y="97">server component</text><text x="50" y="157">{$t[2]}</text><text x="50" y="217" fill="#B84508">"use client"</text></g>
<rect x="360" y="42" width="260" height="200" rx="14" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<g fill="#F1F0F8"><rect x="376" y="60" width="160" height="14" rx="4"/><rect x="376" y="84" width="228" height="10" rx="4"/><rect x="376" y="102" width="200" height="10" rx="4"/><rect x="376" y="120" width="216" height="10" rx="4"/><rect x="376" y="138" width="180" height="10" rx="4"/></g>
<rect x="376" y="168" width="150" height="52" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="3"/>
<text x="451" y="191" text-anchor="middle" font-size="13" font-weight="700" fill="#B84508">MiPiace</text>
<text x="451" y="209" text-anchor="middle" font-size="11" fill="#B84508">{$t[5]}</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#nxia)"><path d="M264 110H356"/><path d="M264 202H372"/></g>
<g font-size="12" font-weight="700" text-anchor="middle"><text x="318" y="102" fill="#0B7F5C">{$t[3]}</text><text x="318" y="194" fill="#B84508">{$t[4]}</text></g>
</svg>
SVG;
    }
}

/** The special files as nested boundaries: layout outside, then error, then loading, then the page. */
if (!function_exists('fig_next_bounds')) {
    function fig_next_bounds(string $l): string
    {
        $t = $l === 'it' ? ['resta sempre', 'se qualcosa si rompe, al posto di tutto quello che c’è dentro', 'mentre la pagina aspetta i dati', 'il contenuto vero'] : ['always stays', 'if something breaks, instead of everything inside', 'while the page waits for its data', 'the real content'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="10" y="10" width="620" height="260" rx="16" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="40" y="58" width="560" height="196" rx="14" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<rect x="70" y="106" width="500" height="132" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2" stroke-dasharray="7 4"/>
<rect x="100" y="154" width="440" height="68" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700"><text x="28" y="40" fill="#5A35C8">layout.tsx</text><text x="58" y="88" fill="#B0245A">error.tsx</text><text x="88" y="136" fill="#B84508">loading.tsx</text><text x="118" y="184" fill="#1B66BC">page.tsx</text></g>
<g font-size="13" fill="#5B5880"><text x="150" y="40">{$t[0]}</text><text x="150" y="88">{$t[1]}</text><text x="190" y="136">{$t[2]}</text><text x="118" y="206">{$t[3]}</text></g>
</svg>
SVG;
    }
}

/** The proxy on the way in: a request to a reserved path without the session cookie is redirected to /login, otherwise it goes on to the page. */
if (!function_exists('fig_next_proxy')) {
    function fig_next_proxy(string $l): string
    {
        $t = $l === 'it' ? ['richiesta', 'c’è il cookie «sessione»?', 'sì', 'no', 'la pagina', 'controlla di nuovo i dati'] : ['request', 'is the “sessione” cookie there?', 'yes', 'no', 'the page', 'checks the data again'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="nxpa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="84" width="150" height="62" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="87" y="108" text-anchor="middle" font-size="13" font-weight="700" fill="#5B5880">{$t[0]}</text>
<text x="87" y="130" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12">/area-riservata</text>
<rect x="200" y="72" width="200" height="86" rx="14" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="300" y="104" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-weight="700">proxy.ts</text>
<text x="300" y="132" text-anchor="middle" font-size="12">{$t[1]}</text>
<rect x="450" y="20" width="178" height="62" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="450" y="150" width="178" height="62" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<text x="539" y="46" text-anchor="middle" font-size="14" font-weight="700">{$t[4]}</text><text x="539" y="68" text-anchor="middle" font-size="12" fill="#5B5880">{$t[5]}</text>
<text x="539" y="187" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">redirect /login</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#nxpa)"><path d="M162 115H196"/><path d="M400 96C420 70 425 52 446 52"/><path d="M400 134C420 160 425 180 446 180"/></g>
<g font-size="12" font-weight="700" text-anchor="end"><text x="440" y="34" fill="#0B7F5C">{$t[2]} · next()</text><text x="440" y="214" fill="#B0245A">{$t[3]}</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'nextjs', 'family' => 'next', 'after' => 'react-intermediate',
    'title' => L('Next.js', 'Next.js'), 'short' => L('Next.js', 'Next.js'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Il framework React per siti e app completi: rotte da cartelle, componenti server e client, dati e cache (anche con `"use cache"`), route handler, server action, errori, proxy e pubblicazione.', 'The React framework for complete sites and apps: folder-based routes, server and client components, data and caching (including `"use cache"`), route handlers, server actions, errors, proxy and deployment.'),
    'lessons' => [
        lesson('routing', L('Rotte dalle cartelle', 'Routes from folders'), [
            P('Con l’**App Router** di Next.js le rotte sono cartelle dentro `app/`: ogni cartella con un file `page.tsx` diventa una pagina. `layout.tsx` avvolge le pagine figlie (intestazione, menu), e le cartelle tra parentesi quadre sono **segmenti dinamici**.', 'With Next.js’s **App Router** routes are folders inside `app/`: every folder with a `page.tsx` file becomes a page. `layout.tsx` wraps child pages (header, menu), and folders in square brackets are **dynamic segments**.'),
            C('app/
├── layout.tsx            → struttura comune
├── page.tsx              → /
├── corsi/
│   ├── page.tsx          → /corsi
│   └── [id]/page.tsx     → /corsi/react, /corsi/sql …
└── api/ricerca/route.ts  → /api/ricerca', 'text'),
            C('export default async function Corso({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  return <h1>Corso: {id}</h1>;
}', 'tsx'),
            P('Per i link interni si usa il componente `<Link href="/corsi">`, che naviga senza ricaricare la pagina e precarica le pagine visibili.', 'For internal links you use the `<Link href="/corsi">` component, which navigates without reloading the page and prefetches visible pages.'),
            P('Il layout resta montato quando passi da una pagina all’altra: l’intestazione non si ridisegna e il suo stato (un menu aperto, un campo di ricerca) non si perde. Una cartella diventa una rotta solo se contiene `page.tsx`, quindi accanto puoi tenere componenti e file di supporto. Il valore del segmento dinamico arriva nella prop `params`, che è una promise: per questo si scrive `await params`.', 'The layout stays mounted when you move from one page to another: the header is not redrawn and its state (an open menu, a search field) is not lost. A folder becomes a route only if it contains `page.tsx`, so you can keep components and support files next to it. The value of the dynamic segment arrives in the `params` prop, which is a promise: that is why you write `await params`.'),
            Fig(L(fig_next_routes('it'), fig_next_routes('en')),
                L('Una finestra del browser con l’indirizzo /corsi/react. Dentro, un riquadro viola app/layout.tsx con intestazione e menu, che resta tra una pagina e l’altra; al suo interno un riquadro tratteggiato app/corsi/[id]/page.tsx, che cambia con l’indirizzo e mostra «Corso: react» con id = "react".', 'A browser window with the address /corsi/react. Inside, a purple app/layout.tsx box with header and menu, which stays from one page to the next; within it a dashed app/corsi/[id]/page.tsx box, which changes with the address and shows “Corso: react” with id = "react".'),
                L('Il layout fa da cornice fissa, la pagina cambia con l’indirizzo.', 'The layout is a fixed frame, the page changes with the address.')),
        ], [
            Q('r1', L('Quale file trasforma una cartella in una pagina?', 'Which file turns a folder into a page?'), ['page.tsx', 'index.html', 'route.ts', 'main.tsx'], 0, L('`layout.tsx` la avvolge.', '`layout.tsx` wraps it.')),
            Q('r2', L('Che cosa indica la cartella `[id]`?', 'What does the `[id]` folder mean?'), [L('Un segmento dinamico', 'A dynamic segment'), L('Una cartella nascosta', 'A hidden folder'), L('Un errore', 'An error'), L('Un file di stile', 'A style file')], 0, L('Il valore arriva in `params`.', 'The value arrives in `params`.')),
            Q('r3', L('Quale componente si usa per i link interni?', 'Which component is used for internal links?'), ['<Link>', '<a> sempre', '<Route>', '<Nav>'], 0, L('Naviga senza ricaricare.', 'It navigates without reloading.')),
        ], task('text', L('Scrivi il percorso del file che crea la pagina `/blog/[slug]` nell’App Router (es. `app/…/page.tsx`).', 'Write the file path that creates the `/blog/[slug]` page in the App Router (e.g. `app/…/page.tsx`).'), '', [re('^\s*app/blog/\[slug\]/page\.(tsx|jsx|js|ts)\s*$', L('Il percorso è giusto', 'The path is right'))], '', 'app/blog/[slug]/page.tsx'), [
            dMatch(pMatch(), [['app/page.tsx', '/'], ['app/corsi/page.tsx', '/corsi'], ['app/corsi/[id]/page.tsx', '/corsi/:id'], ['app/api/x/route.ts', '/api/x'], ['layout.tsx', L('Struttura comune', 'Shared structure')]], L('Dalle cartelle alle rotte.', 'From folders to routes.')),
            dFlash(pFlash(), [[L('Ogni cartella con page.tsx è una pagina', 'Every folder with page.tsx is a page'), true], [L('Il layout si ridisegna da zero a ogni navigazione', 'The layout is redrawn from scratch on every navigation'), false], [L('Le parentesi quadre creano segmenti dinamici', 'Square brackets create dynamic segments'), true], [L('`<Link>` ricarica tutta la pagina', '`<Link>` reloads the whole page'), false]], L('Il routing di Next.js.', 'Next.js routing.')),
        ]),
        lesson('server-client', L('Componenti server e client', 'Server and client components'), [
            P('Nell’App Router i componenti sono **componenti server** per impostazione predefinita: girano sul server, possono leggere database e segreti, e mandano al browser solo HTML, senza il loro JavaScript. Sono ottimi per contenuti e dati.', 'In the App Router components are **server components** by default: they run on the server, can read databases and secrets, and send only HTML to the browser, without their JavaScript. They are great for content and data.'),
            C('// app/corsi/page.tsx — componente server
import { db } from "@/lib/db";
import Preferito from "./Preferito";

export default async function Corsi() {
  const corsi = await db.corso.findMany();
  return corsi.map(c => <div key={c.id}>{c.titolo} <Preferito id={c.id} /></div>);
}

// app/corsi/Preferito.tsx — componente client
"use client";
import { useState } from "react";
export default function Preferito({ id }: { id: string }) {
  const [on, setOn] = useState(false);
  return <button onClick={() => setOn(!on)}>{on ? "★" : "☆"}</button>;
}', 'tsx'),
            P('Quando servono stato, effetti o eventi del browser, scrivi `"use client"` in cima al file: diventa un **componente client**. Regola pratica: tieni client solo le “isole” interattive, il resto server.', 'When you need state, effects or browser events, write `"use client"` at the top of the file: it becomes a **client component**. Rule of thumb: keep only the interactive “islands” on the client, the rest on the server.'),
            Fig(L(fig_next_islands('it'), fig_next_islands('en')),
                L('A sinistra, sul server, tre componenti: Pagina e ListaCorsi, che legge il database, sono server component e mandano solo HTML; MiPiace, con "use client", manda HTML e JavaScript. A destra la pagina nel browser: testo statico e un solo riquadro interattivo, il pulsante MiPiace.', 'On the left, on the server, three components: Pagina and ListaCorsi, which reads the database, are server components and send HTML only; MiPiace, with "use client", sends HTML and JavaScript. On the right the page in the browser: static text and a single interactive box, the MiPiace button.'),
                L('Quasi tutto arriva come HTML pronto: il JavaScript serve solo all’isola interattiva.', 'Almost everything arrives as ready HTML: JavaScript is needed only for the interactive island.')),
        ], [
            Q('s1', L('Come sono i componenti per impostazione predefinita nell’App Router?', 'What are components by default in the App Router?'), [L('Componenti server', 'Server components'), L('Componenti client', 'Client components'), L('Pagine statiche HTML', 'Static HTML pages'), L('Web worker', 'Web workers')], 0, L('Girano sul server.', 'They run on the server.')),
            Q('s2', L('Che cosa rende un componente client?', 'What makes a component a client one?'), [L('La direttiva "use client" in cima al file', 'The "use client" directive at the top of the file'), L('Il nome del file', 'The file name'), L('Un import di React', 'A React import'), L('La cartella client/', 'The client/ folder')], 0, L('Serve per stato ed eventi.', 'Needed for state and events.')),
            Q('s3', L('Un componente server può leggere il database direttamente?', 'Can a server component read the database directly?'), [L('Sì', 'Yes'), L('No, mai', 'No, never'), L('Solo con useEffect', 'Only with useEffect'), L('Solo nel browser', 'Only in the browser')], 0, L('Il codice non arriva al browser.', 'The code never reaches the browser.')),
        ], task('tsx', L('Trasforma `Contatore.tsx` in componente client aggiungendo la direttiva giusta in cima, prima degli import.', 'Turn `Contatore.tsx` into a client component by adding the right directive at the top, before the imports.'), "import { useState } from \"react\";\n\nexport default function Contatore() {\n  const [n, setN] = useState(0);\n  return <button onClick={() => setN(n + 1)}>{n}</button>;\n}", [re('^\s*["\']use client["\'];?\s*\n\s*import', L('La direttiva è in cima', 'The directive is at the top'))], '', "\"use client\";\nimport { useState } from \"react\";"), [
            dMatch(pMatch(), [[L('Componente server', 'Server component'), L('Dati e contenuti', 'Data and content')], [L('Componente client', 'Client component'), L('Stato ed eventi', 'State and events')], ['"use client"', L('Direttiva in cima al file', 'Directive at the top of the file')], [L('Isola interattiva', 'Interactive island'), L('Piccola parte client', 'Small client part')]], L('Server o client?', 'Server or client?')),
            dBug(pBug(), ['import { useState } from "react";', 'export default function Like() {', '  const [n, setN] = useState(0);', '  return <button onClick={() => setN(n + 1)}>{n}</button>;', '}'], 0, '"use client"; import { useState } from "react";', L('Con useState serve "use client".', 'useState needs "use client".')),
        ]),
        lesson('data', L('Dati e cache', 'Data and caching'), [
            P('Nei componenti server si caricano i dati con `await` direttamente nel componente. Next.js può rendere una pagina **statica** (generata una volta e servita a tutti) o **dinamica** (generata a ogni richiesta), e offre opzioni per aggiornare i dati statici dopo un certo tempo.', 'In server components you load data with `await` directly in the component. Next.js can make a page **static** (generated once and served to everyone) or **dynamic** (generated on each request), and offers options to refresh static data after a while.'),
            C('// rigenera la pagina al massimo una volta all’ora
export const revalidate = 3600;

export default async function Notizie() {
  const r = await fetch("https://api.esempio.it/notizie");
  const notizie = await r.json();
  return <ul>{notizie.map((n: any) => <li key={n.id}>{n.titolo}</li>)}</ul>;
}', 'tsx'),
            P('Le **server action** sono funzioni marcate con `"use server"` che girano sul server e si possono chiamare da un modulo: gestiscono salvataggi senza scrivere un’API a parte. Dopo una modifica, `revalidatePath` aggiorna le pagine che mostrano quei dati.', '**Server actions** are functions marked with `"use server"` that run on the server and can be called from a form: they handle saves without writing a separate API. After a change, `revalidatePath` refreshes the pages showing that data.'),
            Tip('Con le **Cache Components** di Next.js 16 (`cacheComponents: true`) la cache si dichiara con `"use cache"` e `cacheLife` invece di opzioni come `revalidate`: le vedrai in una lezione più avanti.', 'With Next.js 16 **Cache Components** (`cacheComponents: true`) caching is declared with `"use cache"` and `cacheLife` instead of options like `revalidate`: you will see them in a later lesson.'),
        ], [
            Q('d1', L('Che cosa fa `export const revalidate = 3600`?', 'What does `export const revalidate = 3600` do?'), [L('Rigenera la pagina al massimo una volta all’ora', 'Regenerates the page at most once an hour'), L('Cancella la cache ogni secondo', 'Clears the cache every second'), L('Rende la pagina privata', 'Makes the page private'), L('Niente', 'Nothing')], 0, L('3600 secondi.', '3600 seconds.')),
            Q('d2', L('Che cos’è una server action?', 'What is a server action?'), [L('Una funzione "use server" chiamabile da un modulo', 'A "use server" function callable from a form'), L('Un componente client', 'A client component'), L('Un file CSS', 'A CSS file'), L('Un test', 'A test')], 0, L('Niente API a parte.', 'No separate API.')),
            Q('d3', L('Che differenza c’è tra pagina statica e dinamica?', 'What is the difference between static and dynamic pages?'), [L('Statica generata una volta, dinamica a ogni richiesta', 'Static generated once, dynamic on each request'), L('Nessuna', 'None'), L('La statica non ha HTML', 'Static has no HTML'), L('La dinamica non usa React', 'Dynamic does not use React')], 0, L('La statica è velocissima.', 'Static is very fast.')),
        ], task('tsx', L('Scrivi una server action `salvaNota(dati: FormData)` con `"use server"` che legge il campo `testo` con `dati.get("testo")`.', 'Write a server action `salvaNota(dati: FormData)` with `"use server"` that reads the `testo` field with `dati.get("testo")`.'), '', [re('async\s+function\s+salvaNota\s*\(\s*dati\s*:\s*FormData\s*\)', L('La funzione', 'The function')), re('["\']use server["\']', L('La direttiva server', 'The server directive')), re('dati\.get\s*\(\s*["\']testo["\']\s*\)', L('Legge il campo', 'It reads the field'))], '', "async function salvaNota(dati: FormData) {\n  \"use server\";\n  const testo = dati.get(\"testo\");\n  await db.nota.create({ data: { testo: String(testo) } });\n}"), [
            dOrder(pSort(), ['async function aggiungi(dati: FormData) {', '  "use server";', '  await db.voce.create({ data: { nome: String(dati.get("nome")) } });', '  revalidatePath("/lista");', '}'], L('Dichiara, salva, aggiorna la pagina.', 'Declare, save, refresh the page.')),
            dMatch(pMatch(), [['revalidate', L('Ogni quanto rigenerare', 'How often to regenerate')], ['"use server"', L('Funzione sul server', 'Server function')], ['revalidatePath', L('Aggiorna le pagine dopo un salvataggio', 'Refreshes pages after a save')], ['await fetch', L('Dati nel componente server', 'Data in the server component')]], L('Dati in Next.js.', 'Data in Next.js.')),
        ]),
        lesson('api', L('Route handler e pubblicazione', 'Route handlers and deployment'), [
            P('Un file `route.ts` crea un **endpoint API**: esporti funzioni chiamate come i metodi HTTP (`GET`, `POST`…) che ricevono una `Request` e restituiscono una `Response`. Utile per webhook, app mobili o chiamate da altri servizi.', 'A `route.ts` file creates an **API endpoint**: you export functions named after HTTP methods (`GET`, `POST`…) that receive a `Request` and return a `Response`. Handy for webhooks, mobile apps or calls from other services.'),
            C('// app/api/ricerca/route.ts
export async function GET(req: Request) {
  const q = new URL(req.url).searchParams.get("q") ?? "";
  const risultati = await cerca(q);
  return Response.json({ risultati });
}', 'ts'),
            P('Per pubblicare: `next build` crea la versione ottimizzata, `next start` la avvia su un server Node; oppure si usa una piattaforma che supporta Next.js. Le variabili segrete stanno in `.env.local` (mai nel repository); solo quelle con prefisso `NEXT_PUBLIC_` arrivano al browser.', 'To publish: `next build` creates the optimised version, `next start` runs it on a Node server; or you use a platform that supports Next.js. Secret variables live in `.env.local` (never in the repository); only those prefixed `NEXT_PUBLIC_` reach the browser.'),
        ], [
            Q('a1', L('Come crei un endpoint GET in un route handler?', 'How do you create a GET endpoint in a route handler?'), [L('Esporti una funzione chiamata GET', 'Export a function named GET'), L('Scrivi un file get.html', 'Write a get.html file'), L('Con un componente <Get>', 'With a <Get> component'), L('Non si può', 'You cannot')], 0, L('Nel file route.ts.', 'In the route.ts file.')),
            Q('a2', L('Quali variabili d’ambiente arrivano al browser?', 'Which environment variables reach the browser?'), [L('Quelle con prefisso NEXT_PUBLIC_', 'Those prefixed NEXT_PUBLIC_'), L('Tutte', 'All of them'), L('Nessuna, mai', 'None, ever'), L('Quelle in maiuscolo', 'Uppercase ones')], 0, L('I segreti restano sul server.', 'Secrets stay on the server.')),
            Q('a3', L('Quale comando crea la versione ottimizzata?', 'Which command creates the optimised build?'), ['next build', 'next dev', 'next lint', 'next serve'], 0, L('Poi `next start`.', 'Then `next start`.')),
        ], task('ts', L('In `app/api/saluto/route.ts` esporta `GET` che restituisce `Response.json({ messaggio: "ciao" })`.', 'In `app/api/saluto/route.ts` export `GET` returning `Response.json({ messaggio: "ciao" })`.'), '', [re('export\s+(async\s+)?function\s+GET\s*\(', L('L’endpoint GET', 'The GET endpoint')), re('return\s+Response\.json\s*\(\s*\{\s*messaggio\s*:\s*["\']ciao["\']\s*\}\s*\)', L('La risposta JSON', 'The JSON response'))], '', "export async function GET() {\n  return Response.json({ messaggio: \"ciao\" });\n}"), [
            dMatch(pMatch(), [['route.ts', L('Endpoint API', 'API endpoint')], ['next build', L('Versione ottimizzata', 'Optimised build')], ['next start', L('Avvia in produzione', 'Starts in production')], ['.env.local', L('Segreti locali', 'Local secrets')], ['NEXT_PUBLIC_', L('Visibile nel browser', 'Visible in the browser')]], L('Dal codice alla produzione.', 'From code to production.')),
            dBug(pBug(), ['# .env.local', 'NEXT_PUBLIC_STRIPE_SECRET=sk_live_…', 'NEXT_PUBLIC_SITO=https://esempio.it'], 1, 'STRIPE_SECRET=sk_live_…', L('Un segreto con NEXT_PUBLIC_ finisce nel browser.', 'A secret with NEXT_PUBLIC_ ends up in the browser.')),
        ]),
        lesson('actions-seo', L('Server Actions, metadati e SEO', 'Server Actions, metadata and SEO'), [
            P('Una **Server Action** è una funzione asincrona con `"use server"` che gira sul server: la passi a un `<form action={...}>` e riceve i dati in un `FormData`, senza scrivere un’API a parte. Dopo il salvataggio, `revalidatePath()` aggiorna le pagine in cache.', 'A **Server Action** is an async function with `"use server"` that runs on the server: you pass it to a `<form action={...}>` and it receives the data as `FormData`, with no separate API to write. After saving, `revalidatePath()` refreshes cached pages.'),
            C('// app/commenti/page.tsx
async function aggiungi(dati: FormData) {
  "use server";
  await db.commento.create({ data: { testo: String(dati.get("testo")) } });
  revalidatePath("/commenti");
}

export const metadata = { title: "Commenti", description: "Dite la vostra" };

export default function Pagina() {
  return <form action={aggiungi}><input name="testo" /><button>Invia</button></form>;
}', 'tsx'),
            P('L’oggetto **`metadata`** (o la funzione `generateMetadata` per le pagine dinamiche) genera `<title>`, descrizione e immagini di anteprima. Le pagine rese sul server sono già leggibili dai motori di ricerca.', 'The **`metadata`** object (or the `generateMetadata` function for dynamic pages) generates `<title>`, description and preview images. Pages rendered on the server are already readable by search engines.'),
        ], [
            Q('as1', L('Dove gira una Server Action?', 'Where does a Server Action run?'), [L('Sul server', 'On the server'), L('Nel browser', 'In the browser'), L('In un service worker', 'In a service worker'), L('Nel CDN soltanto', 'Only in the CDN')], 0, L('Lo dice `"use server"`.', '`"use server"` says so.')),
            Q('as2', L('A che cosa serve `revalidatePath`?', 'What is `revalidatePath` for?'), [L('Aggiornare la cache di una pagina', 'Refreshing a page’s cache'), L('Validare il form', 'Validating the form'), L('Cambiare rotta', 'Changing route'), L('Cancellare i cookie', 'Deleting cookies')], 0, L('Così si vede il nuovo commento.', 'So the new comment shows.')),
            Q('as3', L('Come imposti il titolo di una pagina?', 'How do you set a page’s title?'), [L('Esportando `metadata`', 'By exporting `metadata`'), L('Con `document.title` nel server', 'With `document.title` on the server'), L('In `next.config.js`', 'In `next.config.js`'), L('Non si può', 'You cannot')], 0, L('O `generateMetadata` per le pagine dinamiche.', 'Or `generateMetadata` for dynamic pages.')),
        ], task('ts', L('Esporta `metadata` con `title: "Chi siamo"`.', 'Export `metadata` with `title: "Chi siamo"`.'), '', [re('export\s+const\s+metadata\s*=\s*\{[^}]*title\s*:\s*["\']Chi siamo["\']', L('I metadati sono esportati', 'The metadata is exported'))], '', 'export const metadata = { title: "Chi siamo" };'), [
            dMatch(pMatch(), [['"use server"', L('Funzione sul server', 'Function on the server')], ['FormData', L('I dati del form', 'The form data')], ['revalidatePath', L('Aggiorna la cache', 'Refreshes the cache')], ['metadata', L('Titolo e descrizione', 'Title and description')], ['generateMetadata', L('Metadati dinamici', 'Dynamic metadata')]], L('Azioni e SEO.', 'Actions and SEO.')),
            dFlash(pFlash(), [[L('Una Server Action può ricevere un form', 'A Server Action can receive a form'), true], [L('Le Server Action girano nel browser', 'Server Actions run in the browser'), false], [L('Le pagine rese sul server aiutano la SEO', 'Server-rendered pages help SEO'), true], [L('metadata va scritto in index.html', 'metadata goes in index.html'), false]], L('Next.js sul server.', 'Next.js on the server.')),
        ]),
        lesson('loading-errors', L('Caricamento, errori e pagina non trovata', 'Loading, errors and not found'), [
            P('Alcuni nomi di file hanno un ruolo speciale. `loading.tsx` compare subito mentre la pagina carica i dati (Next.js lo avvolge in un `<Suspense>`), `error.tsx` sostituisce il segmento quando qualcosa si rompe, `not-found.tsx` appare quando chiami `notFound()`.', 'Some file names have a special role. `loading.tsx` shows at once while the page loads its data (Next.js wraps it in a `<Suspense>`), `error.tsx` replaces the segment when something breaks, `not-found.tsx` appears when you call `notFound()`.'),
            Fig(L(fig_next_bounds('it'), fig_next_bounds('en')),
                L('Quattro riquadri uno dentro l’altro: layout.tsx all’esterno, che resta sempre; error.tsx, che se qualcosa si rompe prende il posto di tutto quello che c’è dentro; loading.tsx, tratteggiato, mostrato mentre la pagina aspetta i dati; al centro page.tsx con il contenuto vero.', 'Four boxes one inside the other: layout.tsx outside, which always stays; error.tsx, which if something breaks takes the place of everything inside; loading.tsx, dashed, shown while the page waits for its data; in the middle page.tsx with the real content.'),
                L('Ogni file speciale avvolge quelli più interni: un errore non fa sparire il layout.', 'Each special file wraps the inner ones: an error does not take the layout away.')),
            C('app/corsi/[id]/
├── page.tsx        → la pagina
├── loading.tsx     → mentre aspetta
├── error.tsx       → se qualcosa si rompe
└── not-found.tsx   → se il corso non esiste', 'text'),
            C('// app/corsi/[id]/page.tsx
import { notFound } from "next/navigation";

export default async function Corso({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const corso = await db.corso.findUnique({ where: { id } });
  if (!corso) notFound();
  return <h1>{corso.titolo}</h1>;
}

// app/corsi/[id]/error.tsx
"use client";
export default function Errore({ retry }: { error: unknown; retry: () => void }) {
  return <button onClick={() => retry()}>Riprova</button>;
}', 'tsx'),
            P('`error.tsx` deve essere un componente client, perché è un error boundary di React. `retry()` riprova a caricare e disegnare il segmento. Per gli errori nel layout radice c’è `global-error.tsx`. In produzione i messaggi degli errori del server non arrivano al browser, per non svelare dettagli interni.', '`error.tsx` must be a client component, because it is a React error boundary. `retry()` tries to load and draw the segment again. For errors in the root layout there is `global-error.tsx`. In production server error messages do not reach the browser, so internal details are not revealed.'),
        ], [
            Q('le1', L('Quale file compare mentre la pagina carica i dati?', 'Which file shows while the page loads its data?'), ['error.tsx', 'loading.tsx', 'layout.tsx', 'route.ts'], 1, L('Diventa il `fallback` di un `<Suspense>`.', 'It becomes the `fallback` of a `<Suspense>`.')),
            Q('le2', L('Perché `error.tsx` inizia con `"use client"`?', 'Why does `error.tsx` start with `"use client"`?'), [L('Per leggere il database', 'To read the database'), L('Per migliorare la SEO', 'To improve SEO'), L('Gli error boundary sono componenti client', 'Error boundaries are client components'), L('Non serve, è facoltativo', 'It is not needed, it is optional')], 2, L('Senza la direttiva Next.js segnala un errore.', 'Without the directive Next.js reports an error.')),
            Q('le3', L('Che cosa succede chiamando `notFound()`?', 'What happens when you call `notFound()`?'), [L('La pagina si ricarica', 'The page reloads'), L('Il server si ferma', 'The server stops'), L('Si torna alla home', 'You go back home'), L('Viene mostrato `not-found.tsx`', '`not-found.tsx` is shown')], 3, L('Con lo stato HTTP 404.', 'With the HTTP 404 status.')),
        ], task('tsx', L('Nella pagina, se `corso` non esiste chiama `notFound()` prima di usarlo.', 'In the page, if `corso` does not exist call `notFound()` before using it.'), 'import { notFound } from "next/navigation";

const corso = await db.corso.findUnique({ where: { id } });
return <h1>{corso.titolo}</h1>;', [re('if\s*\(\s*!\s*corso\s*\)\s*\{?\s*(return\s+)?notFound\s*\(\s*\)', L('Gestisci il corso mancante', 'You handle the missing course'))], '', 'const corso = await db.corso.findUnique({ where: { id } });
if (!corso) notFound();
return <h1>{corso.titolo}</h1>;'), [
            dMatch(pMatch(), [['loading.tsx', L('Mentre si caricano i dati', 'While data loads')], ['error.tsx', L('Se qualcosa si rompe', 'If something breaks')], ['not-found.tsx', L('Risorsa inesistente', 'Missing resource')], ['global-error.tsx', L('Errori nel layout radice', 'Errors in the root layout')], ['retry()', L('Riprova a caricare il segmento', 'Retries loading the segment')]], L('File speciali per i momenti difficili.', 'Special files for hard moments.')),
            dBug(pBug(), ['"use server";', 'export default function Errore({ retry }: { retry: () => void }) {', '  return <button onClick={() => retry()}>Riprova</button>;', '}'], 0, '"use client";', L('Un error boundary è un componente client.', 'An error boundary is a client component.')),
        ]),
        lesson('use-cache', L('Cache Components e "use cache"', 'Cache Components and "use cache"'), [
            P('Next.js 16 ha introdotto un modello di cache esplicito, le **Cache Components**, che si attiva con `cacheComponents: true` in `next.config.ts`. Di base i dati vengono letti a ogni richiesta; quello che vuoi tenere in cache lo segni con la direttiva `"use cache"`, su una funzione, un componente o un intero file.', 'Next.js 16 introduced an explicit caching model, **Cache Components**, turned on with `cacheComponents: true` in `next.config.ts`. By default data is read on every request; what you want to keep in cache you mark with the `"use cache"` directive, on a function, a component or a whole file.'),
            C('// next.config.ts
const nextConfig = { cacheComponents: true };
export default nextConfig;

// lib/corsi.ts
import { cacheLife, cacheTag } from "next/cache";

export async function elencoCorsi() {
  "use cache";
  cacheLife("hours");   // si rinnova dopo qualche ora
  cacheTag("corsi");    // etichetta per invalidarla
  return db.corso.findMany();
}

// app/admin/actions.ts
"use server";
import { updateTag } from "next/cache";

export async function aggiungiCorso(dati: FormData) {
  await db.corso.create({ data: { titolo: String(dati.get("titolo")) } });
  updateTag("corsi");   // la prossima lettura è fresca
}', 'ts'),
            P('`cacheLife` sceglie per quanto tenere il risultato (profili come `"minutes"`, `"hours"`, `"days"`, `"max"`), `cacheTag` gli dà un’etichetta e `updateTag` o `revalidateTag` lo invalidano dopo una modifica. Una funzione con `"use cache"` deve essere `async` e non può leggere `cookies()` o `headers()`: leggili fuori e passa i valori come argomenti. Con le Cache Components opzioni come `export const revalidate` non si usano più.', '`cacheLife` chooses how long to keep the result (profiles like `"minutes"`, `"hours"`, `"days"`, `"max"`), `cacheTag` gives it a label and `updateTag` or `revalidateTag` invalidate it after a change. A function with `"use cache"` must be `async` and cannot read `cookies()` or `headers()`: read them outside and pass the values as arguments. With Cache Components options like `export const revalidate` are no longer used.'),
        ], [
            Q('uc1', L('Come si attivano le Cache Components?', 'How do you turn on Cache Components?'), [L('Con `"use client"` in cima a ogni file', 'With `"use client"` at the top of every file'), L('Con `cacheComponents: true` in `next.config`', 'With `cacheComponents: true` in `next.config`'), L('Con `export const cache = true`', 'With `export const cache = true`'), L('Installando un pacchetto a parte', 'By installing a separate package')], 1, L('Poi si usa `"use cache"` dove serve.', 'Then you use `"use cache"` where needed.')),
            Q('uc2', L('Che cosa fa `cacheTag("corsi")`?', 'What does `cacheTag("corsi")` do?'), [L('Etichetta il risultato per poterlo invalidare', 'Labels the result so it can be invalidated'), L('Cancella la cache', 'Clears the cache'), L('Crea una rotta', 'Creates a route'), L('Rende la funzione client', 'Makes the function client-side')], 0, L('`updateTag("corsi")` la invalida dopo una modifica.', '`updateTag("corsi")` invalidates it after a change.')),
            Q('uc3', L('Perché non leggere `cookies()` dentro una funzione con `"use cache"`?', 'Why not read `cookies()` inside a function with `"use cache"`?'), [L('È solo più lento', 'It is just slower'), L('I cookie non esistono sul server', 'Cookies do not exist on the server'), L('`cookies()` è stato rimosso', '`cookies()` has been removed'), L('Cambiano a ogni richiesta: Next.js lo vieta e dà errore', 'They change on every request: Next.js forbids it and errors')], 3, L('Leggili fuori e passa il valore come argomento.', 'Read them outside and pass the value as an argument.')),
        ], task('tsx', L('Metti in cache `ultimiArticoli` con `"use cache"` come prima riga e `cacheLife("days")`.', 'Cache `ultimiArticoli` with `"use cache"` as the first line and `cacheLife("days")`.'), 'import { cacheLife } from "next/cache";

export async function ultimiArticoli() {
  return db.articolo.findMany({ take: 10 });
}', [re('async\s+function\s+ultimiArticoli\s*\(\s*\)\s*\{\s*["\']use cache["\']', L('La funzione è in cache', 'The function is cached')), re('cacheLife\s*\(\s*["\']days["\']\s*\)', L('Dura qualche giorno', 'It lasts a few days'))], '', 'export async function ultimiArticoli() {
  "use cache";
  cacheLife("days");
  return db.articolo.findMany({ take: 10 });
}'), [
            dMatch(pMatch(), [['"use cache"', L('Segna che cosa tenere in cache', 'Marks what to keep in cache')], ['cacheLife("hours")', L('Per quanto tenerlo', 'How long to keep it')], ['cacheTag("x")', L('Etichetta per invalidare', 'Label for invalidating')], ['updateTag("x")', L('Invalida dopo una modifica', 'Invalidates after a change')], ['cacheComponents', L('Attiva il modello', 'Turns on the model')]], L('La cache, dichiarata dove serve.', 'Caching, declared where needed.')),
            dFlash(pFlash(), [[L('Con le Cache Components i dati si leggono a ogni richiesta, salvo `"use cache"`', 'With Cache Components data is read on every request, unless `"use cache"`'), true], [L('Una funzione con `"use cache"` può essere sincrona', 'A function with `"use cache"` can be synchronous'), false], [L('`cookies()` si può leggere dentro `"use cache"`', '`cookies()` can be read inside `"use cache"`'), false], [L('`updateTag` si usa dopo aver modificato i dati', '`updateTag` is used after changing the data'), true]], L('Esplicito è meglio di implicito.', 'Explicit beats implicit.')),
        ]),
        lesson('proxy', L('Proxy, cookie e accesso', 'Proxy, cookies and access'), [
            P('Il file `proxy.ts`, nella radice del progetto, esegue codice **prima** che una richiesta arrivi alla pagina: può reindirizzare, riscrivere l’indirizzo o aggiungere intestazioni. Da Next.js 16 sostituisce il vecchio `middleware.ts`, che è deprecato.', 'The `proxy.ts` file, at the project root, runs code **before** a request reaches the page: it can redirect, rewrite the address or add headers. Since Next.js 16 it replaces the old `middleware.ts`, which is deprecated.'),
            Fig(L(fig_next_proxy('it'), fig_next_proxy('en')),
                L('Una richiesta a /area-riservata passa da proxy.ts, che controlla se c’è il cookie «sessione». Se sì, con next() arriva alla pagina, che controlla di nuovo i dati; se no, viene reindirizzata a /login.', 'A request to /area-riservata goes through proxy.ts, which checks whether the “sessione” cookie is there. If yes, with next() it reaches the page, which checks the data again; if not, it is redirected to /login.'),
                L('Il proxy sta davanti a tutte le pagine: un controllo veloce, non l’ultimo.', 'The proxy stands in front of every page: a quick check, not the last one.')),
            C('// proxy.ts
import { NextResponse, type NextRequest } from "next/server";

export function proxy(request: NextRequest) {
  const sessione = request.cookies.get("sessione");
  if (!sessione) {
    return NextResponse.redirect(new URL("/login", request.url));
  }
  return NextResponse.next();
}

export const config = { matcher: ["/area-riservata/:path*"] };', 'ts'),
            C('// in un componente server
import { cookies } from "next/headers";

const lingua = (await cookies()).get("lingua")?.value ?? "it";', 'tsx'),
            P('`matcher` limita il proxy ai percorsi che ti interessano. Usalo per controlli veloci e ottimistici (c’è il cookie?); la verifica vera di chi è l’utente va ripetuta vicino ai dati: nelle pagine, nelle Server Action e nei route handler. Nei componenti server `cookies()` e `headers()` sono asincroni: servono `await`.', '`matcher` limits the proxy to the paths you care about. Use it for quick, optimistic checks (is the cookie there?); the real check of who the user is must be repeated close to the data: in pages, Server Actions and route handlers. In server components `cookies()` and `headers()` are async: they need `await`.'),
        ], [
            Q('px1', L('Quando gira `proxy.ts`?', 'When does `proxy.ts` run?'), [L('Dopo che la pagina è stata mandata', 'After the page has been sent'), L('Solo nel browser', 'Only in the browser'), L('Prima che la richiesta arrivi alla pagina', 'Before the request reaches the page'), L('Solo durante `next build`', 'Only during `next build`')], 2, L('Può reindirizzare prima di disegnare qualsiasi cosa.', 'It can redirect before drawing anything.')),
            Q('px2', L('Che cosa sostituisce `proxy.ts` da Next.js 16?', 'What does `proxy.ts` replace since Next.js 16?'), ['middleware.ts', 'layout.tsx', 'next.config.ts', 'route.ts'], 0, L('Il vecchio nome è deprecato.', 'The old name is deprecated.')),
            Q('px3', L('Basta il proxy per proteggere i dati?', 'Is the proxy enough to protect data?'), [L('Sì, sempre', 'Yes, always'), L('No: il controllo va ripetuto vicino ai dati', 'No: the check must be repeated close to the data'), L('Solo in sviluppo', 'Only in development'), L('Solo con HTTPS', 'Only with HTTPS')], 1, L('Pagine, Server Action e API devono verificare da sole.', 'Pages, Server Actions and APIs must check by themselves.')),
        ], task('ts', L('In `proxy.ts` esporta `config` con `matcher: ["/admin/:path*"]`.', 'In `proxy.ts` export `config` with `matcher: ["/admin/:path*"]`.'), 'export function proxy(request: NextRequest) {
  return NextResponse.next();
}', [re('export\s+const\s+config\s*=\s*\{\s*matcher\s*:\s*(\[\s*)?["\']/admin/:path\*["\']', L('Il proxy copre l’area admin', 'The proxy covers the admin area'))], '', 'export function proxy(request: NextRequest) {
  return NextResponse.next();
}

export const config = { matcher: ["/admin/:path*"] };'), [
            dMatch(pMatch(), [['proxy.ts', L('Gira prima della pagina', 'Runs before the page')], ['matcher', L('Percorsi coperti', 'Paths covered')], ['NextResponse.redirect', L('Manda altrove', 'Sends elsewhere')], ['NextResponse.next', L('Lascia passare', 'Lets it through')], ['cookies()', L('Legge i cookie sul server', 'Reads cookies on the server')]], L('Il portiere davanti alle pagine.', 'The doorkeeper in front of the pages.')),
            dBug(pBug(), ['import { cookies } from "next/headers";', 'export default async function Pagina() {', '  const tema = cookies().get("tema")?.value;', '  return <p>{tema}</p>;', '}'], 2, '  const tema = (await cookies()).get("tema")?.value;', L('`cookies()` è asincrona: serve `await`.', '`cookies()` is async: it needs `await`.')),
        ]),
    ],
];
