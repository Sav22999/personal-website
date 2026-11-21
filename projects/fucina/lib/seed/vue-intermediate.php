<?php
/** Vue intermediate: composables, watchers, Vue Router, Pinia and data from the server, provide/inject, transitions and Teleport, TypeScript. */

/** The life of a component: setup, mounted, updated at every change (where watch fires), unmounted. */
if (!function_exists('fig_vueint_life')) {
    function fig_vueint_life(string $l): string
    {
        $t = $l === 'it' ? ['crea dati|e funzioni', 'è nel DOM: ora|puoi toccarlo', 'a ogni modifica|dei dati', 'tolto: pulisci|timer e ascolti', 'qui scatta watch'] : ['creates data|and functions', 'it is in the DOM:|now you can|touch it', 'at every|data change', 'removed: clear|timers and|listeners', 'watch fires here'];
        $x = [24, 182, 340, 498];
        for ($i = 0; $i < 4; $i++) {
            $t[$i] = implode('', array_map(fn($k, $line) => '<tspan x="' . $x[$i] . '" dy="' . ($k ? 17 : 0) . '">' . $line . '</tspan>', array_keys(explode('|', $t[$i])), explode('|', $t[$i])));
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="vila" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g stroke-width="2"><rect x="12" y="30" width="138" height="104" rx="12" fill="#F1F0F8" stroke="#8A87B0"/><rect x="170" y="30" width="138" height="104" rx="12" fill="#D5F6EA" stroke="#0B7F5C"/><rect x="328" y="30" width="138" height="104" rx="12" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="486" y="30" width="142" height="104" rx="12" fill="#FFE3EE" stroke="#F03E82"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700"><text x="24" y="56">setup</text><text x="182" y="56">onMounted</text><text x="340" y="56">onUpdated</text><text x="498" y="56">onUnmounted</text></g>
<g font-size="13"><text x="24" y="84">{$t[0]}</text>
<text x="182" y="84">{$t[1]}</text>
<text x="340" y="84">{$t[2]}</text>
<text x="498" y="84">{$t[3]}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#vila)"><path d="M150 82H166"/><path d="M308 82H324"/><path d="M466 82H482"/><path d="M370 134C370 168 424 168 424 138"/></g>
<text x="397" y="190" text-anchor="middle" font-size="13" font-weight="700" fill="#1B66BC">↺ {$t[4]}</text>
</svg>
SVG;
    }
}

/** A composable gives every component its own state; a Pinia store is one state shared by all. */
if (!function_exists('fig_vueint_store')) {
    function fig_vueint_store(string $l): string
    {
        $t = $l === 'it' ? ['composable: uno stato a testa', 'store: un solo stato condiviso', 'componente'] : ['composable: one state each', 'store: one shared state', 'component'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="visa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-weight="700" font-size="15"><text x="20" y="30">{$t[0]}</text><text x="340" y="30">{$t[1]}</text></g>
<path d="M322 16V226" stroke="#E4E2F0" stroke-width="2"/>
<g fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"><rect x="20" y="150" width="130" height="66" rx="12"/><rect x="170" y="150" width="130" height="66" rx="12"/><rect x="340" y="150" width="130" height="66" rx="12"/><rect x="490" y="150" width="130" height="66" rx="12"/></g>
<g font-size="13" fill="#5B5880"><text x="32" y="172">{$t[2]} A</text><text x="182" y="172">{$t[2]} B</text><text x="352" y="172">{$t[2]} A</text><text x="502" y="172">{$t[2]} B</text></g>
<g fill="#ECE6FF" stroke="#7048E8" stroke-width="2"><rect x="32" y="182" width="106" height="26" rx="7"/><rect x="182" y="182" width="106" height="26" rx="7"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="42" y="200">n = 3</text><text x="192" y="200">n = 0</text><text x="352" y="200">pezzi: 2</text><text x="502" y="200">pezzi: 2</text></g>
<g fill="#FFFFFF" stroke="#7048E8" stroke-width="2" stroke-dasharray="5 4"><rect x="40" y="60" width="240" height="40" rx="10"/></g>
<text x="160" y="85" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14">useContatore()</text>
<rect x="400" y="52" width="160" height="56" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="480" y="76" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">useCarrello</text>
<text x="480" y="97" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700">pezzi = 2</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#visa)"><path d="M120 100L90 146"/><path d="M200 100L230 146"/><path d="M450 108L410 146"/><path d="M510 108L550 146"/></g>
</svg>
SVG;
    }
}

/** provide and inject: App offers the theme and a deep descendant receives it, skipping the components in between. */
if (!function_exists('fig_vueint_provide')) {
    function fig_vueint_provide(string $l): string
    {
        $t = $l === 'it' ? ['non lo toccano', 'nessuna prop in mezzo'] : ['they do not touch it', 'no props in between'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="vipa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="40" y="16" width="280" height="46" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g fill="#F1F0F8" stroke="#C9C7DD" stroke-width="2" stroke-dasharray="6 4"><rect x="70" y="80" width="250" height="40" rx="10"/><rect x="100" y="138" width="220" height="40" rx="10"/></g>
<rect x="130" y="196" width="190" height="46" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M56 62V100H70"/><path d="M86 120V158H100"/><path d="M116 178V219H130"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="56" y="45"><tspan font-weight="700">App</tspan> · provide("tema")</text><text x="86" y="105" fill="#8A87B0">Layout</text><text x="116" y="163" fill="#8A87B0">Barra</text><text x="146" y="225"><tspan font-weight="700">Pulsante</tspan> · inject</text></g>
<text x="310" y="105" text-anchor="end" font-size="12" fill="#8A87B0">{$t[0]}</text>
<path d="M324 39C470 39 470 219 328 219" stroke="#0B7F5C" stroke-width="3" fill="none" marker-end="url(#vipa)"/>
<text x="452" y="124" font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#0B7F5C">"tema"</text>
<text x="452" y="144" font-size="13" fill="#5B5880">{$t[1]}</text>
</svg>
SVG;
    }
}

/** The classes of a Transition along time: enter-from, enter-active, enter-to, then the same for leave; a highlight walks them in order. */
if (!function_exists('fig_vueint_trans')) {
    function fig_vueint_trans(string $l): string
    {
        $t = $l === 'it' ? ['entrata', 'uscita', 'tempo'] : ['enter', 'leave', 'time'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.vit{opacity:0;animation:vit 6s infinite}.vit2{animation-delay:1s}.vit3{animation-delay:2s}.vit4{animation-delay:3s}.vit5{animation-delay:4s}.vit6{animation-delay:5s}@keyframes vit{0%{opacity:0}3%{opacity:1}15%{opacity:1}17%{opacity:0}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.vit{animation:none}}</style>
<defs><marker id="vita" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-weight="700" font-size="14"><text x="12" y="74">{$t[0]}</text><text x="12" y="166">{$t[1]}</text></g>
<g fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"><rect x="100" y="44" width="170" height="52" rx="10"/><rect x="280" y="44" width="170" height="52" rx="10"/><rect x="460" y="44" width="170" height="52" rx="10"/><rect x="100" y="136" width="170" height="52" rx="10"/><rect x="280" y="136" width="170" height="52" rx="10"/><rect x="460" y="136" width="170" height="52" rx="10"/></g>
<g fill="#7048E8"><rect x="112" y="56" width="28" height="28" rx="6" opacity="0.12"/><rect x="292" y="56" width="28" height="28" rx="6" opacity="0.5"/><rect x="472" y="56" width="28" height="28" rx="6"/><rect x="112" y="148" width="28" height="28" rx="6"/><rect x="292" y="148" width="28" height="28" rx="6" opacity="0.5"/><rect x="472" y="148" width="28" height="28" rx="6" opacity="0.12"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12"><text x="148" y="75">-enter-from</text><text x="328" y="75">-enter-active</text><text x="508" y="75">-enter-to</text><text x="148" y="167">-leave-from</text><text x="328" y="167">-leave-active</text><text x="508" y="167">-leave-to</text></g>
<g fill="none" stroke="#F03E82" stroke-width="4"><rect class="vit" x="96" y="40" width="178" height="60" rx="12"/><rect class="vit vit2" x="276" y="40" width="178" height="60" rx="12"/><rect class="vit vit3" x="456" y="40" width="178" height="60" rx="12"/><rect class="vit vit4" x="96" y="132" width="178" height="60" rx="12"/><rect class="vit vit5" x="276" y="132" width="178" height="60" rx="12"/><rect class="vit vit6" x="456" y="132" width="178" height="60" rx="12"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#5B5880" text-anchor="middle"><text x="185" y="30">opacity: 0</text><text x="365" y="30">transition 0.3s</text><text x="545" y="30">opacity: 1</text></g>
<path d="M100 220H624" stroke="#8A87B0" stroke-width="2" marker-end="url(#vita)"/>
<text x="620" y="242" text-anchor="end" font-size="13" fill="#5B5880">{$t[2]} →</text>
</svg>
SVG;
    }
}

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
            P('Per convenzione il nome inizia con `use` e la funzione restituisce un oggetto di `ref` e funzioni, che il componente destruttura. Ogni chiamata crea `ref` nuovi: due componenti che usano `useContatore` hanno due contatori indipendenti. Dentro un composable puoi usare anche `watch` e `onMounted`, come in un componente: per esempio `useMouse()` può iniziare ad ascoltare il mouse all’avvio e smettere alla fine.', 'By convention the name starts with `use` and the function gives back an object of `ref`s and functions, which the component destructures. Every call creates new `ref`s: two components using `useContatore` have two independent counters. Inside a composable you can also use `watch` and `onMounted`, as in a component: for example `useMouse()` can start listening to the mouse at start-up and stop at the end.'),
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
            P('La funzione di `watch` riceve il valore nuovo e quello vecchio. Con `{ immediate: true }` parte anche subito, non solo al primo cambiamento; `watchEffect` invece segue da solo tutti i dati che legge. Quello che apri in `onMounted` (un timer, un ascolto su `window`) va chiuso in `onUnmounted`, altrimenti continua anche quando il componente è sparito.', 'The `watch` function receives the new value and the old one. With `{ immediate: true }` it also runs straight away, not only at the first change; `watchEffect` instead follows by itself all the data it reads. Whatever you open in `onMounted` (a timer, a listener on `window`) must be closed in `onUnmounted`, otherwise it carries on even when the component has gone.'),
            Fig(L(fig_vueint_life('it'), fig_vueint_life('en')),
                L('Quattro fasi in fila: setup crea dati e funzioni, onMounted quando il componente è nel DOM, onUpdated a ogni modifica dei dati, con una freccia circolare dove scatta watch, e onUnmounted quando viene tolto e si puliscono timer e ascolti.', 'Four phases in a row: setup creates data and functions, onMounted when the component is in the DOM, onUpdated at every data change, with a circular arrow where watch fires, and onUnmounted when it is removed and timers and listeners are cleared.'),
                L('La vita di un componente: nasce, compare, cambia, sparisce.', 'The life of a component: it is born, appears, changes, disappears.')),
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
            P('Le rotte si elencano in un array passato a `createRouter`, ognuna con `path` e `component`; un segmento come `/corsi/:id` è un parametro, che leggi con `useRoute().params.id`. Il cambio di pagina avviene senza ricaricare: cambia solo quello che sta dentro `<RouterView />`. Con `router.beforeEach` puoi controllare ogni navigazione, per esempio per mandare al login chi non è entrato.', 'Routes are listed in an array passed to `createRouter`, each with a `path` and a `component`; a segment like `/corsi/:id` is a parameter, which you read with `useRoute().params.id`. Changing page happens without reloading: only what is inside `<RouterView />` changes. With `router.beforeEach` you can check every navigation, for example to send to the login page whoever has not signed in.'),
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
            Fig(L(fig_vueint_store('it'), fig_vueint_store('en')),
                L('A sinistra la funzione useContatore() chiamata da due componenti, che hanno ciascuno il proprio stato, n = 3 e n = 0. A destra lo store useCarrello con pezzi = 2, collegato a due componenti che mostrano entrambi pezzi: 2.', 'On the left the function useContatore() called by two components, each with its own state, n = 3 and n = 0. On the right the store useCarrello with pezzi = 2, linked to two components that both show pezzi: 2.'),
                L('Composable: stessa logica, stati separati. Store: un solo stato per tutti.', 'Composable: same logic, separate states. Store: one state for everyone.')),
            P('Uno store ha tre parti: lo **stato** (i dati), i **getter** (valori calcolati, come `computed`) e le **azioni** (funzioni che cambiano lo stato, anche asincrone). Occhio alla destrutturazione: `const { pezzi } = useCarrello()` perde la reattività. Per i dati usa `storeToRefs(store)`; le azioni invece si possono destrutturare senza problemi.', 'A store has three parts: the **state** (the data), the **getters** (computed values, like `computed`) and the **actions** (functions that change the state, async ones too). Watch out for destructuring: `const { pezzi } = useCarrello()` loses reactivity. For data use `storeToRefs(store)`; actions instead can be destructured without problems.'),
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
            P('Perché tre stati? Una richiesta può durare a lungo o fallire, e l’utente deve sempre capire cosa succede: una scritta di caricamento, un messaggio d’errore chiaro o i dati. Metti `caricamento.value = false` nel blocco `finally`, così si spegne in entrambi i casi. Se i dati dipendono da un parametro, come l’`id` della rotta, osservalo con `watch` e ricarica quando cambia.', 'Why three states? A request can take a long time or fail, and the user must always understand what is going on: a loading message, a clear error message or the data. Put `caricamento.value = false` in the `finally` block, so it is switched off in both cases. If the data depends on a parameter, such as the route `id`, watch it with `watch` and reload when it changes.'),
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
            Fig(L(fig_vueint_provide('it'), fig_vueint_provide('en')),
                L('Un albero di componenti: App con provide("tema") in cima, poi Layout e Barra tratteggiati, che non toccano il tema, e in fondo Pulsante con inject. Una freccia verde va direttamente da App a Pulsante portando "tema", senza prop in mezzo.', 'A component tree: App with provide("tema") at the top, then Layout and Barra, dashed, which do not touch the theme, and at the bottom Pulsante with inject. A green arrow goes straight from App to Pulsante carrying "tema", with no props in between.'),
                L('Il valore salta i livelli intermedi e arriva dove serve.', 'The value skips the levels in between and lands where it is needed.')),
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
            Fig(L(fig_vueint_trans('it'), fig_vueint_trans('en')),
                L('Due righe di tre fasi lungo il tempo. Entrata: -enter-from con opacità 0, -enter-active con la transizione di 0,3 secondi, -enter-to con opacità 1. Uscita: -leave-from, -leave-active, -leave-to, con il quadrato che sbiadisce. Un riquadro evidenziato percorre le fasi in ordine.', 'Two rows of three phases along time. Enter: -enter-from with opacity 0, -enter-active with the 0.3 second transition, -enter-to with opacity 1. Leave: -leave-from, -leave-active, -leave-to, with the square fading out. A highlight walks the phases in order.'),
                L('Tu scrivi il CSS di inizio e fine, Vue mette e toglie le classi al momento giusto.', 'You write the CSS for start and end, Vue adds and removes the classes at the right moment.')),
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
