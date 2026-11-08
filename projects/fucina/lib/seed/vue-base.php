<?php
/** Vue basics: the single-file component, reactivity, directives, events and lists. */

return [
    'id' => 'vue-base', 'family' => 'vue', 'after' => 'js-base',
    'title' => L('Vue base', 'Vue Basics'), 'short' => L('Vue base', 'Vue Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Componenti in un file, dati reattivi con `ref`, direttive, eventi e liste: interfacce vive con poco codice.', 'Single-file components, reactive data with `ref`, directives, events and lists: live interfaces with little code.'),
    'lessons' => [
        lesson('sfc', L('Il componente in un file', 'The single-file component'), [
            P('Un componente Vue vive in un file `.vue` con tre blocchi: `<script setup>` per la logica, `<template>` per l’HTML e `<style scoped>` per lo stile che vale solo lì.', 'A Vue component lives in a `.vue` file with three blocks: `<script setup>` for logic, `<template>` for HTML and `<style scoped>` for styles that apply only there.'),
            C('<script setup>
const nome = "Ada";
</script>

<template>
  <h1>Ciao, {{ nome }}!</h1>
</template>

<style scoped>
h1 { color: tomato; }
</style>', 'vue'),
            P('Nel template le doppie graffe mostrano un valore. Tutto ciò che dichiari in `<script setup>` è subito usabile nel template.', 'In the template, double braces show a value. Everything declared in `<script setup>` can be used in the template right away.'),
        ], [
            Q('s1', L('Quale blocco contiene la logica?', 'Which block holds the logic?'), ['<script setup>', '<template>', '<style>', '<logic>'], 0, L('Il template è l’HTML.', 'The template is the HTML.')),
            Q('s2', L('Che cosa fa `scoped` su `<style>`?', 'What does `scoped` do on `<style>`?'), [L('Lo stile vale solo in quel componente', 'Styles apply only in that component'), L('Lo stile vale ovunque', 'Styles apply everywhere'), L('Disattiva lo stile', 'Turns styles off'), L('Usa SCSS', 'Uses SCSS')], 0, L('Niente conflitti tra componenti.', 'No clashes between components.')),
            Q('s3', L('Qual è l’estensione di un componente Vue?', 'What is the extension of a Vue component?'), ['.vue', '.vjs', '.component', '.html'], 0, L('Single File Component.', 'Single File Component.')),
        ], task('vue', L('Nel template mostra `{{ titolo }}` dentro un `<h2>`.', 'In the template show `{{ titolo }}` inside an `<h2>`.'), '<script setup>
const titolo = "Fucina";
</script>

<template>
</template>', [re('<template>[\s\S]*<h2>\s*\{\{\s*titolo\s*\}\}\s*</h2>[\s\S]*</template>', L('Il titolo è nel template', 'The title is in the template'))], '', '<template>
  <h2>{{ titolo }}</h2>
</template>'), [
            dOrder(pSort(), ['<script setup>', 'const saluto = "Ciao";', '</script>', '<template>', '  <p>{{ saluto }}</p>', '</template>'], L('Prima lo script, poi il template.', 'First the script, then the template.')),
            dMatch(pMatch(), [['<script setup>', L('La logica', 'The logic')], ['<template>', L('L’HTML', 'The HTML')], ['<style scoped>', L('Lo stile locale', 'Local styles')], ['{{ }}', L('Mostra un valore', 'Shows a value')]], L('I tre blocchi di un file `.vue`.', 'The three blocks of a `.vue` file.')),
        ]),
        lesson('reactivity', L('Dati reattivi', 'Reactive data'), [
            P('`ref()` crea un dato **reattivo**: quando cambia, la pagina si aggiorna da sola. Nello script si legge e si scrive con `.value`; nel template no, Vue lo fa per te.', '`ref()` creates **reactive** data: when it changes, the page updates by itself. In the script you read and write it with `.value`; in the template you do not, Vue does it for you.'),
            C('<script setup>
import { ref, computed } from "vue";

const conta = ref(0);
const doppio = computed(() => conta.value * 2);
function piu() { conta.value++; }
</script>

<template>
  <button @click="piu">{{ conta }} · {{ doppio }}</button>
</template>', 'vue'),
            P('`computed` calcola un valore dagli altri e lo ricorda finché non cambiano.', '`computed` works out a value from the others and remembers it until they change.'),
        ], [
            Q('r1', L('Come cambi un `ref` nello script?', 'How do you change a `ref` in the script?'), ['conta.value = 5', 'conta = 5', 'conta(5)', 'set(conta, 5)'], 0, L('Nello script serve `.value`.', 'In the script you need `.value`.')),
            Q('r2', L('Nel template si scrive `.value`?', 'Do you write `.value` in the template?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo per i numeri', 'Only for numbers'), L('Solo nei cicli', 'Only in loops')], 0, L('Vue lo toglie da solo.', 'Vue unwraps it for you.')),
            Q('r3', L('Che cosa fa `computed`?', 'What does `computed` do?'), [L('Un valore derivato aggiornato da solo', 'A derived value that updates itself'), L('Un ciclo', 'A loop'), L('Un evento', 'An event'), L('Un componente', 'A component')], 0, L('Si ricalcola quando cambiano le dipendenze.', 'It recalculates when its dependencies change.')),
        ], task('vue', L('Crea `const voti = ref(0)` e `const meta = computed(() => voti.value / 2)`.', 'Create `const voti = ref(0)` and `const meta = computed(() => voti.value / 2)`.'), '<script setup>
import { ref, computed } from "vue";
</script>', [re('const\s+voti\s*=\s*ref\s*\(\s*0\s*\)', L('Il ref esiste', 'The ref exists')), re('const\s+meta\s*=\s*computed\s*\(\s*\(\)\s*=>\s*voti\.value\s*/\s*2\s*\)', L('Il computed è giusto', 'The computed is right'))], '', 'const voti = ref(0);
const meta = computed(() => voti.value / 2);'), [
            dBlanks(pFill(), "const nome = ___(\"Ada\");\nconst lungo = ___(() => nome.___.length > 5);", ['ref', 'computed', 'value'], ['reactive', 'watch', 'val'], L('Dato, derivato, lettura.', 'Data, derived, reading.')),
            dFlash(pFlash(), [[L('Cambiare un ref aggiorna la pagina', 'Changing a ref updates the page'), true], [L('Nel template serve `.value`', 'The template needs `.value`'), false], [L('`computed` si ricalcola solo se serve', '`computed` recalculates only when needed'), true]], L('`.value` solo nello script.', '`.value` only in the script.')),
        ]),
        lesson('directives', L('Direttive', 'Directives'), [
            P('Le **direttive** sono attributi speciali che iniziano con `v-`. `v-if` mostra un elemento solo se la condizione è vera, `v-show` lo nasconde con il CSS, `v-bind` (abbreviato `:`) collega un attributo.', '**Directives** are special attributes starting with `v-`. `v-if` shows an element only if the condition is true, `v-show` hides it with CSS, `v-bind` (short `:`) binds an attribute.'),
            C('<img :src="foto" :alt="descrizione" />
<p v-if="errore" class="rosso">{{ errore }}</p>
<p v-else>Tutto a posto</p>
<div v-show="aperto">Menu</div>', 'vue'),
            P('`v-if` crea e distrugge l’elemento; `v-show` lo tiene ma cambia `display`. Usa `v-show` per qualcosa che si apre e chiude spesso.', '`v-if` creates and destroys the element; `v-show` keeps it but changes `display`. Use `v-show` for something that opens and closes often.'),
        ], [
            Q('d1', L('Qual è l’abbreviazione di `v-bind:src`?', 'What is the shorthand of `v-bind:src`?'), [':src', '@src', '#src', '*src'], 0, L('I due punti sono `v-bind`.', 'The colon is `v-bind`.')),
            Q('d2', L('Quale direttiva toglie l’elemento dalla pagina?', 'Which directive removes the element from the page?'), ['v-if', 'v-show', 'v-bind', 'v-model'], 0, L('`v-show` lo nasconde soltanto.', '`v-show` only hides it.')),
            Q('d3', L('Che cosa segue `v-if` per il caso contrario?', 'What follows `v-if` for the opposite case?'), ['v-else', 'v-not', 'v-otherwise', 'v-if-not'], 0, L('Deve stare subito dopo.', 'It must come right after.')),
        ], task('vue', L('Mostra il `<p>` solo se `caricando` è vero, con `v-if`.', 'Show the `<p>` only if `caricando` is true, with `v-if`.'), '<p>Caricamento…</p>', [re('<p\s+v-if\s*=\s*"caricando"\s*>', L('C’è la condizione', 'The condition is there'))], '', '<p v-if="caricando">Caricamento…</p>'), [
            dMatch(pMatch(), [['v-if', L('Crea o toglie', 'Creates or removes')], ['v-show', L('Mostra o nasconde', 'Shows or hides')], [':href', L('Collega un attributo', 'Binds an attribute')], ['v-else', L('Il caso contrario', 'The opposite case')]], L('Le direttive di base.', 'The basic directives.')),
            dBug(pBug(), ['<template>', '  <p v-if="ok">Fatto</p>', '  <span>!</span>', '  <p v-else>Riprova</p>', '</template>'], 2, '  <p v-else>Riprova</p>', L('`v-else` deve stare subito dopo `v-if`.', '`v-else` must come right after `v-if`.')),
        ]),
        lesson('events', L('Eventi e moduli', 'Events and forms'), [
            P('`v-on:click`, abbreviato `@click`, chiama una funzione. I **modificatori** cambiano il comportamento: `@submit.prevent` blocca il ricaricamento della pagina.', '`v-on:click`, short `@click`, calls a function. **Modifiers** change the behaviour: `@submit.prevent` stops the page from reloading.'),
            C('<script setup>
import { ref } from "vue";
const email = ref("");
function invia() { alert("Iscritto: " + email.value); }
</script>

<template>
  <form @submit.prevent="invia">
    <input v-model="email" type="email" />
    <button>Iscriviti</button>
  </form>
</template>', 'vue'),
            P('`v-model` collega un campo a un dato nei due sensi: scrivi nel campo e il dato cambia, cambi il dato e il campo si aggiorna.', '`v-model` links a field to data both ways: type in the field and the data changes, change the data and the field updates.'),
        ], [
            Q('e1', L('Qual è l’abbreviazione di `v-on:click`?', 'What is the shorthand of `v-on:click`?'), ['@click', ':click', '#click', 'on-click'], 0, L('La chiocciola è `v-on`.', 'The at sign is `v-on`.')),
            Q('e2', L('Che cosa fa `.prevent`?', 'What does `.prevent` do?'), [L('Chiama `preventDefault()`', 'Calls `preventDefault()`'), L('Ferma Vue', 'Stops Vue'), L('Ritarda l’evento', 'Delays the event'), L('Duplica l’evento', 'Doubles the event')], 0, L('Il modulo non ricarica la pagina.', 'The form does not reload the page.')),
            Q('e3', L('Quale direttiva lega un campo nei due sensi?', 'Which directive binds a field both ways?'), ['v-model', 'v-bind', 'v-on', 'v-field'], 0, L('Lettura e scrittura insieme.', 'Reading and writing together.')),
        ], task('vue', L('Collega l’input a `nome` con `v-model` e il bottone a `saluta` con `@click`.', 'Bind the input to `nome` with `v-model` and the button to `saluta` with `@click`.'), '<input />
<button>Saluta</button>', [re('<input[^>]*v-model\s*=\s*"nome"', L('L’input è collegato', 'The input is bound')), re('<button[^>]*@click\s*=\s*"saluta(\(\))?"', L('Il bottone chiama `saluta`', 'The button calls `saluta`'))], '', '<input v-model="nome" />
<button @click="saluta">Saluta</button>'), [
            dBlanks(pFill(), "<form ___.prevent=\"cerca\">\n  <input ___=\"testo\" />\n</form>", ['@submit', 'v-model'], ['@click', ':value', 'v-bind'], L('Invio senza ricaricare, campo collegato.', 'Submit without reload, bound field.')),
            dFlash(pFlash(), [[L('`@submit.prevent` evita il ricaricamento', '`@submit.prevent` avoids the reload'), true], [L('`v-model` funziona in un solo senso', '`v-model` works one way only'), false], [L('`@click` può chiamare una funzione con argomenti', '`@click` can call a function with arguments'), true]], L('`v-model` va nei due sensi.', '`v-model` goes both ways.')),
        ]),
        lesson('lists', L('Liste e componenti figli', 'Lists and child components'), [
            P('`v-for` ripete un elemento per ogni voce di un array; `:key` dà a ogni voce un’identità. I componenti figli ricevono dati con le **props**, dichiarate con `defineProps`.', '`v-for` repeats an element for each item of an array; `:key` gives each item an identity. Child components receive data through **props**, declared with `defineProps`.'),
            C('<!-- Corso.vue -->
<script setup>
defineProps({ titolo: String, livelli: Number });
</script>
<template><li>{{ titolo }} · {{ livelli }}</li></template>

<!-- App.vue -->
<ul>
  <Corso v-for="c in corsi" :key="c.id" :titolo="c.titolo" :livelli="c.livelli" />
</ul>', 'vue'),
            P('Le props scendono dal padre al figlio e il figlio non le modifica: se deve avvisare il padre, emette un evento con `defineEmits`.', 'Props go down from parent to child and the child does not change them: to tell the parent something, it emits an event with `defineEmits`.'),
        ], [
            Q('l1', L('Perché serve `:key` in `v-for`?', 'Why does `v-for` need `:key`?'), [L('Per riconoscere ogni voce', 'To identify each item'), L('Per ordinarle', 'To sort them'), L('Per colorarle', 'To colour them'), L('Non serve', 'It is not needed')], 0, L('Vue aggiorna solo ciò che cambia.', 'Vue updates only what changes.')),
            Q('l2', L('Con che cosa un figlio dichiara le props?', 'How does a child declare props?'), ['defineProps', 'useProps', 'props()', 'getProps'], 0, L('Dentro `<script setup>`.', 'Inside `<script setup>`.')),
            Q('l3', L('Un figlio può cambiare una prop?', 'Can a child change a prop?'), [L('No, emette un evento', 'No, it emits an event'), L('Sì, sempre', 'Yes, always'), L('Solo i numeri', 'Only numbers'), L('Solo con `v-if`', 'Only with `v-if`')], 0, L('Il dato appartiene al padre.', 'The data belongs to the parent.')),
        ], task('vue', L('Ripeti un `<li>` per ogni `p` di `prodotti` con `v-for` e `:key="p.id"`.', 'Repeat an `<li>` for each `p` in `prodotti` with `v-for` and `:key="p.id"`.'), '<ul>
  <li>{{ p.nome }}</li>
</ul>', [re('<li[^>]*v-for\s*=\s*"p\s+in\s+prodotti"', L('Il ciclo è giusto', 'The loop is right')), re('<li[^>]*:key\s*=\s*"p\.id"', L('Ogni voce ha la sua chiave', 'Each item has its key'))], '', '<li v-for="p in prodotti" :key="p.id">{{ p.nome }}</li>'), [
            dOrder(pSort(), ['<script setup>', 'defineProps({ nome: String });', 'const emit = defineEmits(["scelto"]);', '</script>', '<template>', '  <button @click="emit(\'scelto\')">{{ nome }}</button>', '</template>'], L('Props ed eventi nello script, poi il template.', 'Props and events in the script, then the template.')),
            dMatch(pMatch(), [['v-for', L('Ripete', 'Repeats')], [':key', L('Identità della voce', 'Identity of the item')], ['defineProps', L('Dati dal padre', 'Data from the parent')], ['defineEmits', L('Eventi verso il padre', 'Events to the parent')]], L('Liste e comunicazione.', 'Lists and communication.')),
        ]),
    ],
];
