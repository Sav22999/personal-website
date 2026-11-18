<?php
/** Vue intermediate: composables, watchers, Vue Router, Pinia and data from the server, provide/inject, transitions and Teleport, TypeScript. */

return [
    'id' => 'vue-intermediate', 'family' => 'vue', 'after' => 'vue-base',
    'title' => L('Vue intermedio', 'Intermediate Vue'), 'short' => L('Vue intermedio', 'Vue Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Composable, watcher, rotte con Vue Router, stato condiviso con Pinia e provide/inject, dati dal server, animazioni e TypeScript.', 'Composables, watchers, routes with Vue Router, shared state with Pinia and provide/inject, data from the server, animations and TypeScript.'),
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
        lesson('provide-inject', L('provide e inject', 'provide and inject'), [
            P('Se un dato deve arrivare a un componente molto in basso, passarlo con le props a ogni livello è scomodo. Con `provide` un antenato **offre** un valore, e qualsiasi discendente lo **riceve** con `inject`, senza passaggi intermedi.', 'If some data must reach a component far down, passing it with props at every level is awkward. With `provide` an ancestor **offers** a value, and any descendant **receives** it with `inject`, with no steps in between.'),
            C('<!-- App.vue -->
<script setup>
import { ref, provide } from "vue";
const lingua = ref("it");
provide("lingua", lingua);
</script>

<!-- Pulsante.vue, molti livelli più in basso -->
<script setup>
import { inject } from "vue";
const lingua = inject("lingua", "it"); // "it" se nessuno lo fornisce
</script>
<template>
  <button>{{ lingua === "it" ? "Salva" : "Save" }}</button>
</template>', 'vue'),
            P('Se fornisci un `ref`, chi lo riceve vede i cambiamenti. Il secondo argomento di `inject` è il valore di riserva. È ideale per tema, lingua o configurazione di una libreria; per lo stato condiviso in tutta l’app Pinia resta più chiaro.', 'If you provide a `ref`, whoever receives it sees the changes. The second argument of `inject` is the fallback value. It is ideal for theme, language or a library’s configuration; for state shared across the whole app Pinia stays clearer.'),
        ], [
            Q('pi1', L('Che cosa risolve `provide`/`inject`?', 'What does `provide`/`inject` solve?'), [L('Caricare dati dal server', 'Loading data from the server'), L('Gestire le rotte', 'Handling routes'), L('Passare dati a componenti lontani senza props intermedie', 'Passing data to far-away components without props in between'), L('Animare gli elementi', 'Animating elements')], 2, L('Niente catene di props.', 'No chains of props.')),
            Q('pi2', L('Che cosa succede se il valore fornito è un `ref`?', 'What happens if the provided value is a `ref`?'), [L('Il discendente vede i cambiamenti', 'The descendant sees the changes'), L('Il discendente riceve una copia ferma', 'The descendant gets a frozen copy'), L('Vue dà errore', 'Vue errors'), L('Il ref viene cancellato', 'The ref is deleted')], 0, L('La reattività arriva fino in fondo.', 'Reactivity reaches all the way down.')),
            Q('pi3', L('Che cosa indica il secondo argomento di `inject`?', 'What does the second argument of `inject` mean?'), [L('La chiave da cercare', 'The key to look for'), L('Il valore di riserva se nessuno lo fornisce', 'The fallback value if nobody provides it'), L('Il componente padre', 'The parent component'), L('Il tipo del valore', 'The value’s type')], 1, L('Così il componente funziona anche da solo.', 'So the component works on its own too.')),
        ], task('vue', L('Nel padre fornisci `tema` con `provide("tema", tema)`.', 'In the parent provide `tema` with `provide("tema", tema)`.'), '<script setup>
import { ref, provide } from "vue";
const tema = ref("scuro");
</script>', [re('provide\s*\(\s*["\']tema["\']\s*,\s*tema\s*\)', L('Il tema è fornito', 'The theme is provided'))], '', '<script setup>
import { ref, provide } from "vue";
const tema = ref("scuro");
provide("tema", tema);
</script>'), [
            dBlanks(pFill(), "// antenato\n___(\"utente\", utente);\n// discendente\nconst utente = ___(\"utente\");", ['provide', 'inject'], ['emit', 'defineProps', 'watch'], L('Uno offre, l’altro riceve.', 'One offers, the other receives.')),
            dFlash(pFlash(), [[L('`inject` può avere un valore di riserva', '`inject` can have a fallback value'), true], [L('`provide` funziona solo tra padre e figlio diretto', '`provide` only works between parent and direct child'), false], [L('Fornire un `ref` mantiene la reattività', 'Providing a `ref` keeps reactivity'), true], [L('`provide` sostituisce sempre Pinia', '`provide` always replaces Pinia'), false]], L('Arriva a tutti i discendenti, a qualsiasi profondità.', 'It reaches every descendant, at any depth.')),
        ]),
        lesson('transitions', L('Transition e Teleport', 'Transition and Teleport'), [
            P('`<Transition>` anima un elemento quando compare o sparisce con `v-if` o `v-show`. Vue aggiunge in automatico delle classi nei vari momenti e tu scrivi il CSS: con `name="dissolvi"` le classi si chiamano `dissolvi-enter-from`, `dissolvi-leave-to` e così via.', '`<Transition>` animates an element when it appears or disappears with `v-if` or `v-show`. Vue adds classes automatically at each moment and you write the CSS: with `name="dissolvi"` the classes are called `dissolvi-enter-from`, `dissolvi-leave-to` and so on.'),
            C('<template>
  <button @click="aperto = !aperto">Info</button>
  <Transition name="dissolvi">
    <p v-if="aperto">Ciao!</p>
  </Transition>

  <Teleport to="body">
    <div v-if="modale" class="modale">Sei sicuro?</div>
  </Teleport>
</template>

<style>
.dissolvi-enter-active, .dissolvi-leave-active { transition: opacity 0.3s; }
.dissolvi-enter-from, .dissolvi-leave-to { opacity: 0; }
</style>', 'vue'),
            P('`<Teleport to="body">` disegna il contenuto in un altro punto della pagina, pur restando parte del componente: perfetto per finestre modali e notifiche, che così non vengono tagliate da un contenitore con `overflow: hidden`. Per animare le liste c’è `<TransitionGroup>`.', '`<Teleport to="body">` draws the content elsewhere on the page while staying part of the component: perfect for modals and notifications, which then are not clipped by a container with `overflow: hidden`. To animate lists there is `<TransitionGroup>`.'),
        ], [
            Q('tr1', L('Quando anima `<Transition>`?', 'When does `<Transition>` animate?'), [L('A ogni clic', 'On every click'), L('Solo al caricamento della pagina', 'Only on page load'), L('Mai senza JavaScript aggiuntivo', 'Never without extra JavaScript'), L('Quando l’elemento compare o sparisce', 'When the element appears or disappears')], 3, L('Con `v-if`, `v-show` o un componente dinamico.', 'With `v-if`, `v-show` or a dynamic component.')),
            Q('tr2', L('Con `name="dissolvi"`, quale classe vale all’inizio dell’entrata?', 'With `name="dissolvi"`, which class applies at the start of the enter?'), ['dissolvi-enter-from', 'dissolvi-start', 'enter-dissolvi', 'v-dissolvi-in'], 0, L('Poi `-enter-active` durante e `-enter-to` alla fine.', 'Then `-enter-active` during and `-enter-to` at the end.')),
            Q('tr3', L('A che cosa serve `<Teleport to="body">`?', 'What is `<Teleport to="body">` for?'), [L('A cambiare pagina', 'Changing page'), L('A disegnare il contenuto in un altro punto del DOM', 'Drawing the content elsewhere in the DOM'), L('A caricare un componente dal server', 'Loading a component from the server'), L('A copiare il componente', 'Copying the component')], 1, L('Dati ed eventi restano quelli del componente.', 'Data and events stay those of the component.')),
        ], task('vue', L('Avvolgi il `<p v-if="visibile">` in un `<Transition name="scivola">`.', 'Wrap the `<p v-if="visibile">` in a `<Transition name="scivola">`.'), '<p v-if="visibile">Salvato!</p>', [re('<Transition\s+name\s*=\s*"scivola"\s*>\s*<p[^>]*v-if\s*=\s*"visibile"[^>]*>[\s\S]*</p>\s*</Transition>', L('Il paragrafo è animato', 'The paragraph is animated'))], '', '<Transition name="scivola">
  <p v-if="visibile">Salvato!</p>
</Transition>'), [
            dMatch(pMatch(), [['<Transition>', L('Anima entrata e uscita', 'Animates enter and leave')], ['<TransitionGroup>', L('Anima le liste', 'Animates lists')], ['<Teleport>', L('Sposta il contenuto nel DOM', 'Moves the content in the DOM')], ['-enter-from', L('Stato iniziale dell’entrata', 'Initial state of the enter')], ['-leave-to', L('Stato finale dell’uscita', 'Final state of the leave')]], L('Movimento e posizione.', 'Movement and position.')),
            dFlash(pFlash(), [[L('`<Transition>` funziona con `v-if` e `v-show`', '`<Transition>` works with `v-if` and `v-show`'), true], [L('Con `<Teleport>` il componente perde i suoi dati', 'With `<Teleport>` the component loses its data'), false], [L('Le classi della transizione dipendono da `name`', 'The transition classes depend on `name`'), true], [L('`<Transition>` scrive da solo il CSS dell’animazione', '`<Transition>` writes the animation CSS by itself'), false]], L('Vue mette le classi, il CSS lo scrivi tu.', 'Vue sets the classes, you write the CSS.')),
        ]),
        lesson('typescript', L('Vue con TypeScript', 'Vue with TypeScript'), [
            P('Con `<script setup lang="ts">` puoi tipizzare tutto il componente. Le props si dichiarano con un tipo generico, `defineProps<{ … }>()`, e gli eventi con `defineEmits<{ … }>()`. Da Vue 3.5 puoi destrutturare le props, che restano reattive, e dare i valori di default direttamente.', 'With `<script setup lang="ts">` you can type the whole component. Props are declared with a generic type, `defineProps<{ … }>()`, and events with `defineEmits<{ … }>()`. Since Vue 3.5 you can destructure props, which stay reactive, and give default values directly.'),
            C('<script setup lang="ts">
import { ref, computed } from "vue";

const { titolo, livelli = 5 } = defineProps<{
  titolo: string;
  livelli?: number;
}>();

const emit = defineEmits<{
  scelto: [id: number];
}>();

const conta = ref<number>(0);
const descrizione = computed(() => `${titolo} · ${livelli} livelli`);
</script>', 'vue'),
            P('L’editor controlla che il padre passi le props giuste e che `emit("scelto", 7)` abbia l’argomento corretto. Per controllare i tipi di un progetto Vue da riga di comando si usa `vue-tsc --noEmit`, perché `tsc` da solo non legge i file `.vue`.', 'The editor checks that the parent passes the right props and that `emit("scelto", 7)` has the correct argument. To check the types of a Vue project from the command line you use `vue-tsc --noEmit`, because `tsc` alone cannot read `.vue` files.'),
        ], [
            Q('vt1', L('Come si attiva TypeScript in un componente?', 'How do you turn on TypeScript in a component?'), ['<script type="ts">', '<ts-script>', '<script setup lang="ts">', '<script setup typescript>'], 2, L('L’attributo `lang` sceglie il linguaggio.', 'The `lang` attribute picks the language.')),
            Q('vt2', L('Come dai un default a `livelli` con Vue 3.5?', 'How do you give `livelli` a default with Vue 3.5?'), [L('Con la destrutturazione: `const { livelli = 5 } = defineProps<…>()`', 'With destructuring: `const { livelli = 5 } = defineProps<…>()`'), L('Con `default:` nel template', 'With `default:` in the template'), L('Non si può', 'You cannot'), L('Con la direttiva `v-default`', 'With the `v-default` directive')], 0, L('Prima si usava `withDefaults`, che funziona ancora.', 'Before, you used `withDefaults`, which still works.')),
            Q('vt3', L('Perché si usa `vue-tsc` invece di `tsc`?', 'Why use `vue-tsc` instead of `tsc`?'), [L('È sempre più veloce', 'It is always faster'), L('`tsc` non sa leggere i file `.vue`', '`tsc` cannot read `.vue` files'), L('`tsc` non esiste più', '`tsc` no longer exists'), L('Serve solo in produzione', 'It is only for production')], 1, L('`vue-tsc` capisce i componenti a file singolo.', '`vue-tsc` understands single-file components.')),
        ], task('vue', L('In uno script `lang="ts"` dichiara le props con `defineProps<{ nome: string }>()`.', 'In a `lang="ts"` script declare the props with `defineProps<{ nome: string }>()`.'), '<script setup>
</script>', [re('<script\s+(setup\s+lang\s*=\s*"ts"|lang\s*=\s*"ts"\s+setup)\s*>', L('Lo script è in TypeScript', 'The script is in TypeScript')), re('defineProps\s*<\s*\{\s*nome\s*:\s*string\s*;?\s*\}\s*>\s*\(\s*\)', L('Le props sono tipizzate', 'The props are typed'))], '', '<script setup lang="ts">
const props = defineProps<{ nome: string }>();
</script>'), [
            dBlanks(pFill(), "<script setup lang=\"___\">\nconst props = defineProps<{ titolo: ___ }>();\nconst emit = ___<{ chiudi: [] }>();\n</script>", ['ts', 'string', 'defineEmits'], ['js', 'String', 'useEmits'], L('Linguaggio, tipo delle props, eventi tipizzati.', 'Language, props type, typed events.')),
            dFlash(pFlash(), [[L('`defineProps<{…}>()` usa un tipo generico', '`defineProps<{…}>()` uses a generic type'), true], [L('`tsc` controlla da solo i file `.vue`', '`tsc` checks `.vue` files by itself'), false], [L('Da Vue 3.5 le props destrutturate restano reattive', 'Since Vue 3.5 destructured props stay reactive'), true], [L('TypeScript in Vue richiede di riscrivere tutto in classi', 'TypeScript in Vue requires rewriting everything as classes'), false]], L('Stessa sintassi, con i tipi.', 'Same syntax, with types.')),
        ]),
    ],
];
