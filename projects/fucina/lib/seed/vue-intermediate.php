<?php
/** Vue intermediate: composables, watchers, Vue Router, Pinia and data from the server. */

return [
    'id' => 'vue-intermediate', 'family' => 'vue', 'after' => 'vue-base',
    'title' => L('Vue intermedio', 'Intermediate Vue'), 'short' => L('Vue intermedio', 'Vue Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Composable, watcher, rotte con Vue Router, stato condiviso con Pinia e dati dal server.', 'Composables, watchers, routes with Vue Router, shared state with Pinia and data from the server.'),
    'lessons' => [
        lesson('composables', L('I composable', 'Composables'), [
            P('Un **composable** è una funzione che inizia per `use` e raccoglie logica riutilizzabile con `ref` e `computed`. Più componenti la chiamano e ognuno riceve il suo stato.', 'A **composable** is a function starting with `use` that packs reusable logic with `ref` and `computed`. Several components call it and each gets its own state.'),
            C('// useContatore.js
import { ref } from "vue";

export function useContatore(inizio = 0) {
  const n = ref(inizio);
  const piu = () => n.value++;
  const azzera = () => (n.value = inizio);
  return { n, piu, azzera };
}

// in un componente
const { n, piu } = useContatore(10);', 'js'),
            P('È il modo di Vue di condividere comportamento senza copiare codice tra i componenti.', 'It is Vue’s way of sharing behaviour without copying code between components.'),
        ], [
            Q('c1', L('Con che cosa inizia di solito il nome di un composable?', 'What does a composable’s name usually start with?'), ['use', 'get', 'make', 'vue'], 0, L('Per convenzione: `useQualcosa`.', 'By convention: `useSomething`.')),
            Q('c2', L('Due componenti che chiamano lo stesso composable condividono lo stato?', 'Do two components calling the same composable share state?'), [L('No, ognuno ha il suo', 'No, each has its own'), L('Sì, sempre', 'Yes, always'), L('Solo i numeri', 'Only numbers'), L('Solo in produzione', 'Only in production')], 0, L('Ogni chiamata crea nuovi `ref`.', 'Each call creates new `ref`s.')),
            Q('c3', L('Che cosa restituisce di solito un composable?', 'What does a composable usually return?'), [L('Un oggetto con dati e funzioni', 'An object with data and functions'), L('Un template', 'A template'), L('Un file CSS', 'A CSS file'), L('Niente', 'Nothing')], 0, L('Il componente prende ciò che gli serve.', 'The component takes what it needs.')),
        ], task('vue', L('Scrivi `useInterruttore()` che crea `acceso = ref(false)` e restituisce `{ acceso, cambia }`.', 'Write `useInterruttore()` that creates `acceso = ref(false)` and returns `{ acceso, cambia }`.'), 'import { ref } from "vue";
', [re('function\s+useInterruttore\s*\(', L('La funzione esiste', 'The function exists')), re('acceso\s*=\s*ref\s*\(\s*false\s*\)', L('Lo stato parte spento', 'The state starts off')), re('return\s*\{\s*acceso\s*,\s*cambia\s*\}', L('Restituisce stato e funzione', 'It returns state and function'))], '', 'export function useInterruttore() {
  const acceso = ref(false);
  const cambia = () => (acceso.value = !acceso.value);
  return { acceso, cambia };
}'), [
            dOrder(pSort(), ['export function useOra() {', '  const ora = ref(new Date());', '  setInterval(() => (ora.value = new Date()), 1000);', '  return { ora };', '}'], L('Stato, aggiornamento, risultato.', 'State, update, result.')),
            dFlash(pFlash(), [[L('Un composable può usare `ref` e `computed`', 'A composable can use `ref` and `computed`'), true], [L('Un composable deve avere un template', 'A composable must have a template'), false], [L('Si chiamano spesso `useQualcosa`', 'They are often called `useSomething`'), true], [L('Due componenti che chiamano lo stesso composable condividono sempre lo stesso stato', 'Two components calling the same composable always share the same state'), false]], L('È solo logica, niente HTML.', 'It is only logic, no HTML.')),
        ]),
        lesson('watch', L('Watcher e ciclo di vita', 'Watchers and lifecycle'), [
            P('`watch` esegue codice quando un dato cambia: utile per salvare, cercare o chiamare il server. `onMounted` esegue codice quando il componente compare nella pagina.', '`watch` runs code when some data changes: handy for saving, searching or calling the server. `onMounted` runs code when the component appears on the page.'),
            C('import { ref, watch, onMounted } from "vue";

const tema = ref(localStorage.getItem("tema") ?? "chiaro");

watch(tema, (nuovo, vecchio) => {
  localStorage.setItem("tema", nuovo);
});

onMounted(() => console.log("Componente pronto"));', 'js'),
            P('`computed` calcola un valore; `watch` fa qualcosa (un effetto). Se ti serve un risultato da mostrare, usa `computed`.', '`computed` works out a value; `watch` does something (a side effect). If you need a result to show, use `computed`.'),
        ], [
            Q('w1', L('Che cosa riceve la funzione di `watch`?', 'What does the `watch` callback receive?'), [L('Valore nuovo e vecchio', 'New and old value'), L('Solo l’evento', 'Only the event'), L('Il DOM', 'The DOM'), L('Niente', 'Nothing')], 0, L('Nell’ordine: nuovo, vecchio.', 'In order: new, old.')),
            Q('w2', L('Quando parte `onMounted`?', 'When does `onMounted` run?'), [L('Quando il componente è nella pagina', 'When the component is on the page'), L('A ogni clic', 'On every click'), L('Prima del server', 'Before the server'), L('Mai', 'Never')], 0, L('Una volta, all’inizio.', 'Once, at the start.')),
            Q('w3', L('Per mostrare un valore derivato è meglio…', 'To show a derived value it is better to use…'), ['computed', 'watch', 'onMounted', 'setTimeout'], 0, L('`watch` è per gli effetti.', '`watch` is for side effects.')),
        ], task('vue', L('Scrivi un `watch` su `filtro` che chiama `cerca(nuovo)`.', 'Write a `watch` on `filtro` that calls `cerca(nuovo)`.'), 'const filtro = ref("");
', [re('watch\s*\(\s*filtro\s*,\s*\(?\s*nuovo', L('Osserva `filtro`', 'It watches `filtro`')), re('cerca\s*\(\s*nuovo\s*\)', L('Chiama la ricerca', 'It calls the search'))], '', 'watch(filtro, (nuovo) => {
  cerca(nuovo);
});'), [
            dBlanks(pFill(), "___(() => carica());\n___(pagina, (nuova) => carica(nuova));", ['onMounted', 'watch'], ['computed', 'onClick', 'ref'], L('All’avvio e a ogni cambio di pagina.', 'At start and on every page change.')),
            dMatch(pMatch(), [['watch', L('Fa qualcosa quando cambia', 'Does something on change')], ['computed', L('Calcola un valore', 'Works out a value')], ['onMounted', L('All’apparizione', 'On appearance')], ['onUnmounted', L('Alla rimozione', 'On removal')]], L('Effetti e ciclo di vita.', 'Effects and lifecycle.')),
        ]),
        lesson('router', L('Vue Router', 'Vue Router'), [
            P('**Vue Router** collega gli indirizzi ai componenti. `<RouterLink to="/corsi">` crea un collegamento senza ricaricare, `<RouterView />` è il punto dove appare la pagina.', '**Vue Router** maps addresses to components. `<RouterLink to="/corsi">` makes a link without reloading, `<RouterView />` is where the page appears.'),
            C('const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", component: Home },
    { path: "/corsi/:id", component: Corso },
  ],
});

// dentro Corso.vue
const route = useRoute();
const id = computed(() => route.params.id);', 'js'),
            P('`useRoute()` legge l’indirizzo attuale e i parametri; `useRouter()` permette di spostarsi via codice con `router.push("/")`.', '`useRoute()` reads the current address and its parameters; `useRouter()` lets you move in code with `router.push("/")`.'),
        ], [
            Q('r1', L('Quale componente mostra la pagina della rotta?', 'Which component shows the route’s page?'), ['<RouterView />', '<RouterLink />', '<App />', '<Page />'], 0, L('`RouterLink` crea i collegamenti.', '`RouterLink` makes the links.')),
            Q('r2', L('Come leggi il parametro `id`?', 'How do you read the `id` parameter?'), ['useRoute().params.id', 'router.id', 'this.id', 'params()'], 0, L('`useRoute` dà la rotta attuale.', '`useRoute` gives the current route.')),
            Q('r3', L('Come vai alla home via codice?', 'How do you go home in code?'), ['router.push("/")', 'location = "/"', 'goHome()', 'route.home()'], 0, L('Senza ricaricare la pagina.', 'Without reloading the page.')),
        ], task('vue', L('Aggiungi la rotta `{ path: "/profilo", component: Profilo }`.', 'Add the route `{ path: "/profilo", component: Profilo }`.'), 'routes: [
  { path: "/", component: Home },
]', [re('path\s*:\s*["\']/profilo["\']\s*,\s*component\s*:\s*Profilo', L('La rotta esiste', 'The route exists'))], '', '{ path: "/profilo", component: Profilo },'), [
            dBlanks(pFill(), "<___ to=\"/corsi\">Corsi</___>\n<___ />", ['RouterLink', 'RouterLink', 'RouterView'], ['a', 'RouterPage', 'router-outlet'], L('Collegamento e contenitore.', 'Link and container.')),
            dFlash(pFlash(), [[L('`RouterLink` ricarica la pagina', '`RouterLink` reloads the page'), false], [L('`:id` è un parametro della rotta', '`:id` is a route parameter'), true], [L('`router.push` cambia pagina via codice', '`router.push` changes page in code'), true], [L('Una rotta può avere un nome', 'A route can have a name'), true]], L('Il router cambia pagina senza ricaricare.', 'The router changes page without reloading.')),
        ]),
        lesson('pinia', L('Stato condiviso con Pinia', 'Shared state with Pinia'), [
            P('Quando molti componenti devono vedere gli stessi dati (l’utente, il carrello) si usa uno **store**. Con **Pinia** si definisce con `defineStore` e si usa in ogni componente con la sua funzione.', 'When many components must see the same data (the user, the cart) you use a **store**. With **Pinia** you define it with `defineStore` and use it in any component through its function.'),
            C('export const useCarrello = defineStore("carrello", () => {
  const pezzi = ref([]);
  const totale = computed(() => pezzi.value.reduce((s, p) => s + p.prezzo, 0));
  function aggiungi(p) { pezzi.value.push(p); }
  return { pezzi, totale, aggiungi };
});

// in qualsiasi componente
const carrello = useCarrello();
carrello.aggiungi({ nome: "Corso", prezzo: 10 });', 'js'),
            P('A differenza di un composable, lo store è **unico**: tutti i componenti vedono gli stessi `pezzi`.', 'Unlike a composable, the store is **single**: every component sees the same `pezzi`.'),
        ], [
            Q('p1', L('Con che cosa si crea uno store Pinia?', 'How do you create a Pinia store?'), ['defineStore', 'createStore', 'useStore', 'new Store'], 0, L('Riceve un nome e una funzione.', 'It takes a name and a function.')),
            Q('p2', L('Lo stato di uno store è condiviso?', 'Is a store’s state shared?'), [L('Sì, tra tutti i componenti', 'Yes, between all components'), L('No, ognuno ha il suo', 'No, each has its own'), L('Solo tra fratelli', 'Only between siblings'), L('Solo con le props', 'Only via props')], 0, L('È la differenza dai composable.', 'That is the difference from composables.')),
            Q('p3', L('Quando conviene uno store?', 'When is a store worth it?'), [L('Dati usati da molti componenti', 'Data used by many components'), L('Un solo campo di testo', 'A single text field'), L('Lo stile', 'Styling'), L('Le immagini', 'Images')], 0, L('Per pochi dati locali basta un `ref`.', 'For a little local data a `ref` is enough.')),
        ], task('vue', L('Crea `useTema = defineStore("tema", () => { ... })` con `const scuro = ref(false)` restituito.', 'Create `useTema = defineStore("tema", () => { ... })` with `const scuro = ref(false)` returned.'), 'import { defineStore } from "pinia";
', [re('useTema\s*=\s*defineStore\s*\(\s*["\']tema["\']', L('Lo store esiste', 'The store exists')), re('scuro\s*=\s*ref\s*\(\s*false\s*\)', L('Lo stato parte chiaro', 'The state starts light')), re('return\s*\{\s*scuro', L('Lo stato è restituito', 'The state is returned'))], '', 'export const useTema = defineStore("tema", () => {
  const scuro = ref(false);
  return { scuro };
});'), [
            dOrder(pSort(), ['export const useUtente = defineStore("utente", () => {', '  const nome = ref("");', '  const entrato = computed(() => nome.value !== "");', '  return { nome, entrato };', '});'], L('Nome, stato, derivati, risultato.', 'Name, state, derived, result.')),
            dMatch(pMatch(), [['defineStore', L('Crea lo store', 'Creates the store')], ['useCarrello()', L('Usa lo store', 'Uses the store')], ['ref', L('Stato', 'State')], ['computed', L('Getter', 'Getter')]], L('I pezzi di Pinia.', 'The pieces of Pinia.')),
        ]),
        lesson('fetch', L('Dati dal server', 'Data from the server'), [
            P('Per caricare dati si usa `fetch` dentro una funzione `async`, tenendo tre stati: **caricamento**, **errore** e **dati**. Il template mostra uno dei tre.', 'To load data you use `fetch` inside an `async` function, keeping three states: **loading**, **error** and **data**. The template shows one of them.'),
            C('const corsi = ref([]);
const carica = ref(true);
const errore = ref(null);

onMounted(async () => {
  try {
    const r = await fetch("/api/corsi");
    if (!r.ok) throw new Error("Errore " + r.status);
    corsi.value = await r.json();
  } catch (e) {
    errore.value = e.message;
  } finally {
    carica.value = false;
  }
});', 'js'),
            P('`fetch` non dà errore con le risposte 404 o 500: va controllato `r.ok`.', '`fetch` does not throw on 404 or 500 responses: you must check `r.ok`.'),
        ], [
            Q('f1', L('`fetch` lancia un errore con una risposta 404?', 'Does `fetch` throw on a 404 response?'), [L('No, va controllato `r.ok`', 'No, check `r.ok`'), L('Sì, sempre', 'Yes, always'), L('Solo in Vue', 'Only in Vue'), L('Solo con JSON', 'Only with JSON')], 0, L('Lancia solo se la rete non risponde.', 'It throws only when the network fails.')),
            Q('f2', L('Quali tre stati conviene gestire?', 'Which three states should you handle?'), [L('Caricamento, errore, dati', 'Loading, error, data'), L('Inizio, metà, fine', 'Start, middle, end'), L('Rosso, giallo, verde', 'Red, yellow, green'), L('Solo i dati', 'Only data')], 0, L('L’utente sa sempre cosa sta succedendo.', 'The user always knows what is happening.')),
            Q('f3', L('Dove parte il caricamento all’apertura del componente?', 'Where does loading start when the component opens?'), ['onMounted', 'computed', 'v-for', 'defineProps'], 0, L('Oppure in un composable.', 'Or in a composable.')),
        ], task('vue', L('Controlla `if (!r.ok)` e lancia un errore prima di leggere il JSON.', 'Check `if (!r.ok)` and throw an error before reading the JSON.'), 'const r = await fetch("/api/note");
note.value = await r.json();', [re('if\s*\(\s*!\s*r\.ok\s*\)\s*throw', L('Controlli la risposta', 'You check the response'))], '', 'const r = await fetch("/api/note");
if (!r.ok) throw new Error("Errore " + r.status);
note.value = await r.json();'), [
            dBug(pBug(), ['const r = await fetch("/api/corsi");', 'corsi.value = r.json();', 'carica.value = false;'], 1, 'corsi.value = await r.json();', L('`r.json()` restituisce una promessa: serve `await`.', '`r.json()` returns a promise: it needs `await`.')),
            dOrder(pSort(), ['try {', '  const r = await fetch(url);', '  if (!r.ok) throw new Error(r.status);', '  dati.value = await r.json();', '} catch (e) {', '  errore.value = e.message;', '}'], L('Chiedi, controlla, leggi, gestisci l’errore.', 'Ask, check, read, handle the error.')),
        ]),
    ],
];
