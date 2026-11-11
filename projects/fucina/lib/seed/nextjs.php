<?php
/** Next.js: the App Router and file-based routes, server and client components, data fetching and caching, route handlers and deployment. */

return [
    'id' => 'nextjs', 'family' => 'next', 'after' => 'react-intermediate',
    'title' => L('Next.js', 'Next.js'), 'short' => L('Next.js', 'Next.js'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Il framework React per siti e app completi: rotte da cartelle, componenti server e client, dati e cache, route handler, server action e pubblicazione.', 'The React framework for complete sites and apps: folder-based routes, server and client components, data and caching, route handlers, server actions and deployment.'),
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
    ],
];
