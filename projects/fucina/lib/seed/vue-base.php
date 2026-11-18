<?php
/** Vue basics: the single-file component, reactivity, directives, events and lists, dynamic classes, slots and v-model on components. */

return [
    'id' => 'vue-base', 'family' => 'vue', 'after' => 'js-base',
    'title' => L('Vue base', 'Vue Basics'), 'short' => L('Vue base', 'Vue Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('Componenti in un file, dati reattivi con `ref`, direttive, eventi e liste, classi dinamiche, slot e `v-model` sui componenti: interfacce vive con poco codice.', 'Single-file components, reactive data with `ref`, directives, events and lists, dynamic classes, slots and `v-model` on components: live interfaces with little code.'),
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
            dFlash(pFlash(), [[L('Cambiare un ref aggiorna la pagina', 'Changing a ref updates the page'), true], [L('Nel template serve `.value`', 'The template needs `.value`'), false], [L('`computed` si ricalcola solo se serve', '`computed` recalculates only when needed'), true], [L('Un `ref` contiene il suo valore in `.value` nello script', 'A `ref` keeps its value in `.value` in the script'), true]], L('`.value` solo nello script.', '`.value` only in the script.')),
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
            dFlash(pFlash(), [[L('`@submit.prevent` evita il ricaricamento', '`@submit.prevent` avoids the reload'), true], [L('`v-model` funziona in un solo senso', '`v-model` works one way only'), false], [L('`@click` può chiamare una funzione con argomenti', '`@click` can call a function with arguments'), true], [L('`@click.once` ascolta il clic una sola volta', '`@click.once` listens to the click only once'), true]], L('`v-model` va nei due sensi.', '`v-model` goes both ways.')),
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
        lesson('class-style', L('Classi e stili dinamici', 'Dynamic classes and styles'), [
            P('`:class` aggiunge o toglie classi secondo delle condizioni: passi un oggetto `{ classe: condizione }` oppure un array. `:style` imposta gli stili da un oggetto, con i nomi delle proprietà in camelCase.', '`:class` adds or removes classes based on conditions: you pass an object `{ class: condition }` or an array. `:style` sets styles from an object, with property names in camelCase.'),
            C('<script setup>
import { ref } from "vue";
const attivo = ref(false);
const errore = ref(true);
const larghezza = ref(120);
</script>

<template>
  <button class="tasto" :class="{ attivo, rosso: errore }" @click="attivo = !attivo">
    Premi
  </button>
  <div :style="{ width: larghezza + \'px\', backgroundColor: \'tomato\' }"></div>
</template>', 'vue'),
            P('Le classi scritte con `class` normale restano e si sommano a quelle dinamiche. `{ attivo }` è la forma breve di `{ attivo: attivo }`.', 'Classes written with plain `class` stay and add up with the dynamic ones. `{ attivo }` is short for `{ attivo: attivo }`.'),
        ], [
            Q('cl1', L('Che cosa fa `:class="{ rosso: errore }"`?', 'What does `:class="{ rosso: errore }"` do?'), [L('Colora sempre di rosso', 'Always colours it red'), L('Crea una variabile `rosso`', 'Creates a `rosso` variable'), L('Aggiunge `rosso` quando `errore` è vero', 'Adds `rosso` when `errore` is true'), L('Cancella la classe `errore`', 'Removes the `errore` class')], 2, L('La chiave è la classe, il valore la condizione.', 'The key is the class, the value the condition.')),
            Q('cl2', L('Come scrivi `background-color` in un `:style`?', 'How do you write `background-color` in a `:style`?'), ['backgroundColor', 'background_color', 'BackgroundColor()', 'bg-color'], 0, L('Nomi in camelCase, come in JavaScript.', 'camelCase names, as in JavaScript.')),
            Q('cl3', L('Che succede a `class="tasto"` se aggiungi anche `:class`?', 'What happens to `class="tasto"` if you also add `:class`?'), [L('Viene sostituita', 'It gets replaced'), L('Si somma alle classi dinamiche', 'It adds up with the dynamic classes'), L('Dà errore', 'It errors'), L('Viene ignorata', 'It is ignored')], 1, L('Classi fisse e dinamiche convivono.', 'Fixed and dynamic classes live together.')),
        ], task('vue', L('Al `<li>` aggiungi `:class="{ fatto: completato }"`.', 'Add `:class="{ fatto: completato }"` to the `<li>`.'), '<li>{{ testo }}</li>', [re('<li[^>]*:class\s*=\s*"\{\s*fatto\s*:\s*completato\s*\}"', L('La classe dipende da `completato`', 'The class depends on `completato`'))], '', '<li :class="{ fatto: completato }">{{ testo }}</li>'), [
            dMatch(pMatch(), [[':class="{ a: x }"', L('Classe secondo una condizione', 'Class based on a condition')], [':class="[a, b]"', L('Più classi da un array', 'Several classes from an array')], [':style="{ color: c }"', L('Uno stile da un dato', 'A style from data')], ['class="fisso"', L('Classe sempre presente', 'Class always present')]], L('Classi e stili guidati dai dati.', 'Classes and styles driven by data.')),
            dBug(pBug(), ['<div', '  :class="{ attivo: selezionato }"', '  :style="{ font-size: dimensione + \'px\' }"', '></div>'], 2, '  :style="{ fontSize: dimensione + \'px\' }"', L('Nell’oggetto di stile i nomi vanno in camelCase.', 'In the style object names go in camelCase.')),
        ]),
        lesson('slots', L('Gli slot', 'Slots'), [
            P('Con le props un componente riceve dati; con gli **slot** riceve **HTML**. Nel figlio scrivi `<slot />` dove deve comparire il contenuto messo dal padre tra i tag del componente.', 'With props a component receives data; with **slots** it receives **HTML**. In the child you write `<slot />` where the content the parent put between the component’s tags should appear.'),
            C('<!-- Scheda.vue -->
<template>
  <article class="scheda">
    <header><slot name="titolo">Senza titolo</slot></header>
    <slot />
  </article>
</template>

<!-- App.vue -->
<Scheda>
  <template #titolo><h2>Corso di Vue</h2></template>
  <p>Otto lezioni per iniziare.</p>
</Scheda>', 'vue'),
            P('Gli **slot con nome** si riempiono con `<template #nome>`. Il testo dentro `<slot>` è il contenuto di riserva, mostrato se il padre non passa niente. Il contenuto dello slot usa i dati del padre.', '**Named slots** are filled with `<template #name>`. The text inside `<slot>` is fallback content, shown if the parent passes nothing. Slot content uses the parent’s data.'),
        ], [
            Q('sl1', L('Che cosa riceve un componente con uno slot?', 'What does a component receive through a slot?'), [L('Solo numeri', 'Only numbers'), L('Un file CSS', 'A CSS file'), L('Una rotta', 'A route'), L('HTML scritto dal padre', 'HTML written by the parent')], 3, L('Le props portano dati, gli slot contenuto.', 'Props carry data, slots carry content.')),
            Q('sl2', L('Come riempi lo slot `titolo`?', 'How do you fill the `titolo` slot?'), ['<template #titolo>', '<slot titolo>', ':titolo="…"', '<titolo-slot>'], 0, L('`#titolo` è la forma breve di `v-slot:titolo`.', '`#titolo` is short for `v-slot:titolo`.')),
            Q('sl3', L('Quando si vede il testo dentro `<slot>…</slot>`?', 'When does the text inside `<slot>…</slot>` show?'), [L('Sempre', 'Always'), L('Quando il padre non passa contenuto', 'When the parent passes no content'), L('Mai', 'Never'), L('Solo in sviluppo', 'Only in development')], 1, L('È il contenuto di riserva.', 'It is the fallback content.')),
        ], task('vue', L('Nel template di `Pannello.vue` mostra il contenuto del padre con `<slot />` dentro il `<div>`.', 'In the `Pannello.vue` template show the parent’s content with `<slot />` inside the `<div>`.'), '<template>
  <div class="pannello"></div>
</template>', [re('<div[^>]*>\s*<slot\s*/?>(\s*</slot>)?\s*</div>', L('Lo slot è nel div', 'The slot is in the div'))], '', '<template>
  <div class="pannello"><slot /></div>
</template>'), [
            dBlanks(pFill(), "<!-- Finestra.vue -->\n<header><slot ___=\"titolo\" /></header>\n<___ />\n<!-- padre -->\n<Finestra><template ___>Ciao</template></Finestra>", ['name', 'slot', '#titolo'], ['id', ':titolo', 'content'], L('Slot con nome, slot predefinito, e il padre che li riempie.', 'Named slot, default slot, and the parent filling them.')),
            dFlash(pFlash(), [[L('Gli slot permettono di passare HTML a un componente', 'Slots let you pass HTML to a component'), true], [L('Un componente può avere più slot con nome', 'A component can have several named slots'), true], [L('Il contenuto dello slot usa i dati del figlio', 'Slot content uses the child’s data'), false], [L('Uno slot non può avere contenuto di riserva', 'A slot cannot have fallback content'), false]], L('Il contenuto è del padre, la posizione del figlio.', 'The content belongs to the parent, the position to the child.')),
        ]),
        lesson('component-model', L('v-model sui componenti', 'v-model on components'), [
            P('`v-model` funziona anche sui tuoi componenti. Nel figlio `defineModel()` crea un `ref` collegato al padre: leggerlo dà il valore del padre, cambiarlo aggiorna il padre.', '`v-model` also works on your own components. In the child `defineModel()` creates a `ref` linked to the parent: reading it gives the parent’s value, changing it updates the parent.'),
            C('<!-- Stelle.vue -->
<script setup>
const voto = defineModel({ type: Number, default: 0 });
</script>
<template>
  <button v-for="n in 5" :key="n" @click="voto = n">
    {{ n <= voto ? "★" : "☆" }}
  </button>
</template>

<!-- App.vue -->
<Stelle v-model="valutazione" />
<p>Hai scelto {{ valutazione }} stelle</p>', 'vue'),
            P('Dietro le quinte `v-model` passa la prop `modelValue` e ascolta l’evento `update:modelValue`. Puoi averne più di uno, con un nome: `v-model:titolo="t"` nel padre e `defineModel("titolo")` nel figlio. `defineModel` esiste da Vue 3.4.', 'Behind the scenes `v-model` passes the `modelValue` prop and listens to the `update:modelValue` event. You can have more than one, with a name: `v-model:titolo="t"` in the parent and `defineModel("titolo")` in the child. `defineModel` has existed since Vue 3.4.'),
        ], [
            Q('cm1', L('Che cosa crea `defineModel()` nel figlio?', 'What does `defineModel()` create in the child?'), [L('Una rotta', 'A route'), L('Uno store Pinia', 'A Pinia store'), L('Un ref collegato al `v-model` del padre', 'A ref linked to the parent’s `v-model`'), L('Un evento del DOM', 'A DOM event')], 2, L('Leggi e scrivi come un `ref` qualsiasi.', 'You read and write it like any `ref`.')),
            Q('cm2', L('Quale prop usa `v-model` dietro le quinte?', 'Which prop does `v-model` use behind the scenes?'), ['value', 'modelValue', 'model', 'vModel'], 1, L('Insieme all’evento `update:modelValue`.', 'Together with the `update:modelValue` event.')),
            Q('cm3', L('Come colleghi un secondo valore chiamato `titolo`?', 'How do you bind a second value called `titolo`?'), ['v-model:titolo="t"', 'v-model="titolo"', ':model.titolo="t"', 'v-titolo="t"'], 0, L('E nel figlio `defineModel("titolo")`.', 'And in the child `defineModel("titolo")`.')),
        ], task('vue', L('Nel figlio crea `const testo = defineModel()` e collegalo all’input con `v-model="testo"`.', 'In the child create `const testo = defineModel()` and bind it to the input with `v-model="testo"`.'), '<script setup>
</script>

<template>
  <input />
</template>', [re('const\s+testo\s*=\s*defineModel\s*\(', L('Crei il modello', 'You create the model')), re('<input[^>]*v-model\s*=\s*"testo"', L('L’input è collegato', 'The input is bound'))], '', '<script setup>
const testo = defineModel();
</script>

<template>
  <input v-model="testo" />
</template>'), [
            dMatch(pMatch(), [['defineModel()', L('Il valore collegato al padre', 'The value linked to the parent')], ['v-model="x"', L('Collegamento nei due sensi', 'Two-way binding')], ['modelValue', L('La prop nascosta', 'The hidden prop')], ['update:modelValue', L('L’evento nascosto', 'The hidden event')]], L('Che cosa fa davvero `v-model`.', 'What `v-model` really does.')),
            dOrder(pSort(), ['<script setup>', 'const aperto = defineModel({ type: Boolean });', '</script>', '<template>', '  <button @click="aperto = !aperto">Menu</button>', '</template>'], L('Il modello nello script, poi il template che lo usa.', 'The model in the script, then the template using it.')),
        ]),
    ],
];
