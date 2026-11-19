<?php
/** JavaScript intermediate: the DOM, events, array methods, modern syntax, JSON, dates and errors, Map and Set, sorting and copying, event delegation. */

/** A small page as the DOM tree: document, html, head and body, down to the text nodes; the h1 a querySelector finds is highlighted. */
if (!function_exists('fig_jsint_dom')) {
    function fig_jsint_dom(string $l): string
    {
        $t = $l === 'it' ? ['elemento', 'testo', 'trovato da querySelector("h1")', 'Pane', 'Latte'] : ['element', 'text', 'found by querySelector("h1")', 'Bread', 'Milk'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g stroke="#8A87B0" stroke-width="2" fill="none">
<path d="M320 52V76"/><path d="M320 112C320 126 170 120 170 136"/><path d="M320 112C320 126 450 120 450 136"/><path d="M170 172V196"/><path d="M450 172C450 186 360 180 360 196"/><path d="M450 172C450 186 560 180 560 196"/><path d="M170 232V256"/><path d="M360 232V256"/><path d="M560 232C560 246 515 240 515 256"/><path d="M560 232C560 246 600 240 600 256"/></g>
<rect x="260" y="16" width="120" height="36" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<g fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"><rect x="280" y="76" width="80" height="36" rx="10"/><rect x="130" y="136" width="80" height="36" rx="10"/><rect x="410" y="136" width="80" height="36" rx="10"/><rect x="130" y="196" width="80" height="36" rx="10"/><rect x="520" y="196" width="80" height="36" rx="10"/><rect x="484" y="256" width="62" height="36" rx="10"/><rect x="569" y="256" width="62" height="36" rx="10"/></g>
<rect x="320" y="196" width="80" height="36" rx="10" fill="#FFE8D6" stroke="#E8590C" stroke-width="3"/>
<g fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="5 4"><rect x="125" y="256" width="90" height="36" rx="10"/><rect x="315" y="256" width="90" height="36" rx="10"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="15"><text x="320" y="39">document</text><text x="320" y="99">html</text><text x="170" y="159">head</text><text x="450" y="159">body</text><text x="170" y="219">title</text><text x="360" y="219" font-weight="700">h1</text><text x="560" y="219">ul</text><text x="515" y="279">li</text><text x="600" y="279">li</text></g>
<g text-anchor="middle" font-size="14" fill="#0B7F5C"><text x="170" y="279">"Fucina"</text><text x="360" y="279">"Ciao!"</text></g>
<g font-size="13" fill="#5B5880"><rect x="20" y="296" width="12" height="12" rx="3" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="38" y="307">{$t[0]}</text><rect x="130" y="296" width="12" height="12" rx="3" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="3 2"/><text x="148" y="307">{$t[1]}</text><rect x="220" y="296" width="12" height="12" rx="3" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="238" y="307">{$t[2]}</text></g>
</svg>
SVG;
    }
}

/** An event climbing from the clicked li to the document; the highlight moves up one level at a time. */
if (!function_exists('fig_jsint_bubble')) {
    function fig_jsint_bubble(string $l): string
    {
        $t = $l === 'it' ? ['Pane', 'Latte', 'Uova', 'click qui', 'un solo listener qui', 'l’evento risale'] : ['Bread', 'Milk', 'Eggs', 'click here', 'a single listener here', 'the event bubbles up'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 320" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.jib{opacity:0;animation:jib 6s infinite}.jib2{animation-delay:1.2s}.jib3{animation-delay:2.4s}.jib4{animation-delay:3.6s}@keyframes jib{0%{opacity:0}3%{opacity:1}20%{opacity:1}24%{opacity:0}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.jib{animation:none}}</style>
<defs><marker id="jiba" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#F03E82"/></marker></defs>
<rect x="10" y="10" width="620" height="300" rx="16" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="34" y="44" width="572" height="246" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="58" y="78" width="420" height="192" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"><rect x="82" y="118" width="240" height="38" rx="9"/><rect x="82" y="214" width="240" height="38" rx="9"/></g>
<rect x="82" y="166" width="240" height="38" rx="9" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700"><text x="26" y="34">document</text><text x="50" y="68" fill="#5A35C8">body</text><text x="74" y="104" fill="#1B66BC">ul#lista</text></g>
<g font-size="15"><text x="100" y="142">li · {$t[0]}</text><text x="100" y="190">li · {$t[1]}</text><text x="100" y="238">li · {$t[2]}</text></g>
<path d="M290 178l0 24l6 -6l5 10l4 -2l-5 -10l8 0z" fill="#1E1C52" stroke="#FFFFFF" stroke-width="1.5"/>
<text x="334" y="190" font-size="13" font-weight="700" fill="#B84508">← {$t[3]}</text>
<text x="190" y="104" font-size="13" font-weight="700" fill="#1B66BC">← {$t[4]}</text>
<path d="M548 260V36" stroke="#F03E82" stroke-width="3" fill="none" marker-end="url(#jiba)"/>
<text x="538" y="150" font-size="14" font-weight="700" fill="#B0245A" text-anchor="middle" transform="rotate(-90 538 150)">{$t[5]}</text>
<g fill="none" stroke="#F03E82" stroke-width="4">
<rect class="jib" x="78" y="162" width="248" height="46" rx="11"/><rect class="jib jib2" x="54" y="74" width="428" height="200" rx="14"/><rect class="jib jib3" x="30" y="40" width="580" height="254" rx="16"/><rect class="jib jib4" x="6" y="6" width="628" height="308" rx="18"/></g>
</svg>
SVG;
    }
}

/** JSON as the bridge: stringify turns an object into text, setItem stores it, getItem and parse bring it back. */
if (!function_exists('fig_jsint_json')) {
    function fig_jsint_json(string $l): string
    {
        $t = $l === 'it' ? ['oggetto JavaScript', 'testo JSON'] : ['JavaScript object', 'JSON text'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 200" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="jija" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="20" y="62" width="150" height="76" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="240" y="62" width="160" height="76" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="470" y="62" width="150" height="76" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g font-size="13" font-weight="700" fill="#5B5880" text-anchor="middle"><text x="95" y="86">{$t[0]}</text><text x="320" y="86">{$t[1]}</text><text x="545" y="86">localStorage</text></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="95" y="116">{ punti: 10 }</text><text x="320" y="116">'{"punti":10}'</text><text x="545" y="116">"gioco"</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#jija)"><path d="M120 60C150 22 210 22 250 58"/><path d="M270 142C230 180 160 180 124 142"/><path d="M360 60C390 22 450 22 490 58"/><path d="M510 142C470 180 400 180 364 142"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700"><text x="185" y="20" fill="#5A35C8">JSON.stringify</text><text x="195" y="194" fill="#5A35C8">JSON.parse</text><text x="425" y="20" fill="#0B7F5C">setItem</text><text x="435" y="194" fill="#0B7F5C">getItem</text></g>
</svg>
SVG;
    }
}

/** Spread copies only the first level and shares the nested object; structuredClone copies every level. */
if (!function_exists('fig_jsint_copy')) {
    function fig_jsint_copy(string $l): string
    {
        $t = $l === 'it' ? ['copia superficiale', 'copia profonda', 'condiviso!', 'due oggetti separati'] : ['shallow copy', 'deep copy', 'shared!', 'two separate objects'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="jica" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<text x="20" y="30" font-weight="700" font-size="16"><tspan font-family="ui-monospace, Menlo, monospace">{ ...a }</tspan> · {$t[0]}</text>
<text x="340" y="30" font-weight="700" font-size="16"><tspan font-family="ui-monospace, Menlo, monospace">structuredClone</tspan> · {$t[1]}</text>
<path d="M322 16V246" stroke="#E4E2F0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#5B5880"><text x="20" y="62">a</text><text x="20" y="162">copia = { ...a }</text><text x="340" y="62">a</text><text x="340" y="162">b = structuredClone(a)</text></g>
<g stroke="#8A87B0" stroke-width="2" fill="#F1F0F8"><rect x="20" y="70" width="120" height="44" rx="10"/><rect x="20" y="170" width="120" height="44" rx="10"/><rect x="340" y="70" width="120" height="44" rx="10"/><rect x="340" y="170" width="120" height="44" rx="10"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="32" y="97">utente:</text><text x="32" y="197">utente:</text><text x="352" y="97">utente:</text><text x="352" y="197">utente:</text></g>
<g fill="#1E1C52"><circle cx="122" cy="92" r="5"/><circle cx="122" cy="192" r="5"/><circle cx="442" cy="92" r="5"/><circle cx="442" cy="192" r="5"/></g>
<rect x="170" y="118" width="140" height="48" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<g fill="#ECE6FF" stroke="#7048E8" stroke-width="2"><rect x="490" y="68" width="136" height="48" rx="12"/><rect x="490" y="168" width="136" height="48" rx="12"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="240" y="147">{ nome: "Ada" }</text><text x="558" y="97">{ nome: "Ada" }</text><text x="558" y="197">{ nome: "Ada" }</text></g>
<text x="240" y="190" text-anchor="middle" font-size="14" font-weight="700" fill="#B0245A">{$t[2]}</text>
<text x="558" y="240" text-anchor="middle" font-size="13" fill="#5B5880">{$t[3]}</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#jica)"><path d="M127 92C150 92 150 128 166 134"/><path d="M127 192C150 192 150 156 166 150"/><path d="M447 92H486"/><path d="M447 192H486"/></g>
</svg>
SVG;
    }
}

/** map, filter and reduce as a pipeline: four numbers doubled, the small ones dropped, the rest summed. */
if (!function_exists('fig_jsint_pipe')) {
    function fig_jsint_pipe(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="jipa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#8A87B0"/></marker></defs>
<g font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="20" y="45">[1, 2, 3, 4]</text><text x="20" y="107" fill="#1B66BC">.map(x =&gt; x * 2)</text><text x="20" y="169" fill="#B84508">.filter(x =&gt; x &gt; 4)</text><text x="20" y="231" fill="#0B7F5C">.reduce((a, x) =&gt; a + x, 0)</text></g>
<g stroke="#8A87B0" stroke-width="2" fill="#F1F0F8"><rect x="380" y="20" width="50" height="40" rx="9"/><rect x="438" y="20" width="50" height="40" rx="9"/><rect x="496" y="20" width="50" height="40" rx="9"/><rect x="554" y="20" width="50" height="40" rx="9"/></g>
<g stroke="#2F8CF0" stroke-width="2" fill="#DCEBFF"><rect x="380" y="82" width="50" height="40" rx="9"/><rect x="438" y="82" width="50" height="40" rx="9"/><rect x="496" y="82" width="50" height="40" rx="9"/><rect x="554" y="82" width="50" height="40" rx="9"/></g>
<g stroke="#C9C7DD" stroke-width="2" fill="#FFFFFF" stroke-dasharray="5 4"><rect x="380" y="144" width="50" height="40" rx="9"/><rect x="438" y="144" width="50" height="40" rx="9"/></g>
<g stroke="#E8590C" stroke-width="2" fill="#FFF3EA"><rect x="496" y="144" width="50" height="40" rx="9"/><rect x="554" y="144" width="50" height="40" rx="9"/></g>
<rect x="496" y="206" width="108" height="40" rx="9" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g text-anchor="middle" font-weight="700" font-size="17"><text x="405" y="46">1</text><text x="463" y="46">2</text><text x="521" y="46">3</text><text x="579" y="46">4</text><text x="405" y="108">2</text><text x="463" y="108">4</text><text x="521" y="108">6</text><text x="579" y="108">8</text><text x="521" y="170">6</text><text x="579" y="170">8</text><text x="550" y="232">14</text></g>
<g text-anchor="middle" font-size="17" fill="#B4B2CC"><text x="405" y="170" text-decoration="line-through">2</text><text x="463" y="170" text-decoration="line-through">4</text></g>
<g stroke="#8A87B0" stroke-width="2" marker-end="url(#jipa)"><path d="M350 52V84"/><path d="M350 114V146"/><path d="M350 176V208"/></g>
</svg>
SVG;
    }
}

return [
    'id' => 'js-intermediate', 'family' => 'js', 'after' => 'js-base',
    'title' => L('JavaScript intermedio', 'Intermediate JavaScript'), 'short' => L('JS intermedio', 'JS Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Fai reagire le pagine: DOM, eventi e delega, metodi degli array, Map e Set, ordinare e copiare, sintassi moderna, JSON e salvataggio dati.', 'Make pages react: DOM, events and delegation, array methods, Map and Set, sorting and copying, modern syntax, JSON and saving data.'),
    'lessons' => [
        lesson('dom', L('Il DOM', 'The DOM'), [
            P('Il **DOM** è la pagina vista da JavaScript: un albero di elementi. `querySelector` ne trova uno con un selettore CSS, poi puoi cambiarne testo, classi e attributi.', 'The **DOM** is the page as JavaScript sees it: a tree of elements. `querySelector` finds one with a CSS selector, then you can change its text, classes and attributes.'),
            C('const titolo = document.querySelector("h1");
titolo.textContent = "Ciao!";
titolo.classList.add("grande");
const voci = document.querySelectorAll("li");
const nuovo = document.createElement("p");
nuovo.textContent = "Nuovo";
document.body.append(nuovo);', 'js'),
            Tip('Usa `textContent` per i testi degli utenti: `innerHTML` può eseguire codice nascosto.', 'Use `textContent` for user text: `innerHTML` can run hidden code.'),
            P('Ogni tag diventa un **nodo** dell’albero, figlio del tag che lo contiene, e anche i testi sono nodi. Se `querySelector` non trova niente restituisce `null`, e `null.textContent` dà errore: succede spesso quando lo script parte prima che l’HTML sia caricato, per questo si mette `defer` sul tag `<script>`. `querySelectorAll` restituisce una lista di tutti gli elementi trovati, da scorrere con `for…of`.', 'Every tag becomes a **node** of the tree, a child of the tag that contains it, and texts are nodes too. If `querySelector` finds nothing it gives back `null`, and `null.textContent` throws an error: this often happens when the script runs before the HTML has loaded, which is why you put `defer` on the `<script>` tag. `querySelectorAll` gives back a list of all the elements found, to walk with `for…of`.'),
            Fig(L(fig_jsint_dom('it'), fig_jsint_dom('en')),
                L('Albero del DOM: document in cima, sotto html, che ha due figli, head e body. head contiene title con il testo «Fucina», body contiene h1 con il testo «Ciao!» e una ul con due li. Il nodo h1 è evidenziato in arancione.', 'DOM tree: document at the top, then html, which has two children, head and body. head holds title with the text “Fucina”, body holds h1 with the text “Ciao!” and a ul with two li. The h1 node is highlighted in orange.'),
                L('La pagina è un albero di nodi: un selettore CSS ne sceglie uno.', 'The page is a tree of nodes: a CSS selector picks one of them.')),
        ], [
            Q('d1', L('Che cosa trova `querySelector`?', 'What does `querySelector` find?'), [L('Il primo elemento adatto', 'The first matching element'), L('Tutti gli elementi', 'All elements'), L('Solo gli id', 'Only ids'), L('Un file', 'A file')], 0, L('`querySelectorAll` li trova tutti.', '`querySelectorAll` finds them all.')),
            Q('d2', L('Perché preferire `textContent`?', 'Why prefer `textContent`?'), [L('Non interpreta l’HTML', 'It does not read HTML'), L('È più colorato', 'It is more colourful'), L('È più lungo', 'It is longer'), L('Salva i dati', 'It saves data')], 0, L('Protegge da codice inserito dagli utenti.', 'It protects from code entered by users.')),
            Q('d3', L('Come si aggiunge una classe?', 'How do you add a class?'), ['el.classList.add("x")', 'el.class = "x"', 'el.addClass("x")', 'el.css("x")'], 0, L('Esistono anche `remove` e `toggle`.', 'There are also `remove` and `toggle`.')),
        ], task('js', L('Trova `h1` con `querySelector` e cambiane il testo in `Fucina`.', 'Find `h1` with `querySelector` and change its text to `Fucina`.'), '', [re('document\.querySelector\(\s*["\']h1["\']\s*\)', L('Trovi il titolo', 'You find the heading')), re('\.textContent\s*=\s*["\']Fucina["\']', L('Cambi il testo', 'You change the text'))], '', "const t = document.querySelector(\"h1\");\nt.textContent = \"Fucina\";"), [
            dMatch(pMatch(), [['querySelector()', L('Trova il primo', 'Finds the first')], ['querySelectorAll()', L('Trova tutti', 'Finds all')], ['textContent', L('Il testo', 'The text')], ['classList', L('Le classi', 'The classes')], ['createElement()', L('Crea un elemento', 'Creates an element')]], L('Gli strumenti per toccare la pagina.', 'The tools for touching the page.')),
            dOrder(pSort(), ['const p = document.createElement("p");', 'p.textContent = "Ciao";', 'document.body.append(p);'], L('Si crea, si riempie e si aggiunge alla pagina.', 'Create it, fill it and add it to the page.')),
            dBug(pBug(), ['const box = document.querySelector(".box");', 'box.classList.add(".attivo");'], 1, 'box.classList.add("attivo");', L('In `classList` il nome va senza punto.', 'In `classList` the name goes without the dot.')),
        ]),
        lesson('events', L('Eventi', 'Events'), [
            P('Un **evento** è qualcosa che succede: un click, un tasto, un invio. Con `addEventListener` dici che cosa fare quando accade.', 'An **event** is something that happens: a click, a key, a submit. With `addEventListener` you say what to do when it occurs.'),
            C('const btn = document.querySelector("button");
btn.addEventListener("click", () => {
  btn.textContent = "Fatto!";
});

const form = document.querySelector("form");
form.addEventListener("submit", e => {
  e.preventDefault();
  console.log(new FormData(form).get("email"));
});', 'js'),
            P('La funzione riceve un oggetto evento: `e.target` è l’elemento coinvolto, `e.preventDefault()` ferma il comportamento normale, come il ricaricamento di un modulo.', 'The function gets an event object: `e.target` is the element involved, `e.preventDefault()` stops the normal behaviour, like a form reloading.'),
            P('Gli eventi più usati sono `click`, `input` (a ogni tasto in un campo), `change`, `keydown` e `submit`. Errore classico: scrivere `addEventListener("click", salva())`. Con le parentesi la funzione parte subito, una volta sola, e al click non succede niente: passa la funzione senza chiamarla, `salva`, oppure una arrow function.', 'The most used events are `click`, `input` (at every key in a field), `change`, `keydown` and `submit`. A classic mistake: writing `addEventListener("click", salva())`. With the brackets the function runs straight away, only once, and nothing happens on click: pass the function without calling it, `salva`, or an arrow function.'),
        ], [
            Q('e1', L('Quale metodo ascolta un evento?', 'Which method listens to an event?'), ['addEventListener', 'onEvent', 'listen', 'watch'], 0, L('Riceve il nome dell’evento e una funzione.', 'It takes the event name and a function.')),
            Q('e2', L('Che cosa fa `e.preventDefault()`?', 'What does `e.preventDefault()` do?'), [L('Ferma l’azione normale', 'Stops the normal action'), L('Cancella la pagina', 'Deletes the page'), L('Annulla JavaScript', 'Cancels JavaScript'), L('Salva', 'Saves')], 0, L('Un modulo non ricarica più la pagina.', 'A form no longer reloads the page.')),
            Q('e3', L('Quale evento scatta scrivendo in un campo?', 'Which event fires when typing in a field?'), ['input', 'write', 'change-key', 'type'], 0, L('`change` scatta solo alla fine.', '`change` fires only at the end.')),
        ], task('js', L('Aggiungi a `btn` un ascoltatore di `click` che esegue `conta++`.', 'Add to `btn` a `click` listener that runs `conta++`.'), 'let conta = 0;
', [re('btn\.addEventListener\(\s*["\']click["\']', L('Ascolti il click', 'You listen to the click')), re('conta\+\+', L('Il contatore sale', 'The counter goes up'))], '', "let conta = 0;\nbtn.addEventListener(\"click\", () => {\n  conta++;\n});"), [
            dBlanks(pFill(), "btn.___(\"___\", e => {\n  e.___();\n});", ['addEventListener', 'click', 'preventDefault'], ['onClick', 'press', 'stop'], L('Ascolta, reagisci, ferma l’azione normale.', 'Listen, react, stop the normal action.')),
            dMatch(pMatch(), [['click', L('Tocco o click', 'Tap or click')], ['submit', L('Invio del modulo', 'Form submit')], ['input', L('Scrittura in un campo', 'Typing in a field')], ['keydown', L('Tasto premuto', 'Key pressed')], ['scroll', L('Scorrimento', 'Scrolling')]], L('Una pagina viva risponde a tutti questi segnali.', 'A live page responds to all these signals.')),
            dBug(pBug(), ['const btn = document.querySelector("button");', 'btn.addEventListener("click", saluta());', 'function saluta() { alert("Ciao"); }'], 1, 'btn.addEventListener("click", saluta);', L('Con le parentesi la funzione parte subito: va passata senza.', 'With brackets the function runs at once: pass it without them.')),
        ]),
        lesson('array-methods', L('map, filter e reduce', 'map, filter and reduce'), [
            P('Gli array hanno metodi potenti. `map` trasforma ogni elemento, `filter` tiene quelli che superano una prova, `find` restituisce il primo adatto, `reduce` riduce tutto a un valore.', 'Arrays have powerful methods. `map` transforms each item, `filter` keeps those passing a test, `find` gives back the first match, `reduce` boils it all down to one value.'),
            P('Tutti ricevono una funzione che viene chiamata per ogni elemento. `map` restituisce sempre un array lungo **uguale** all’originale; `filter` uno uguale o più corto, perché tiene solo gli elementi per cui la funzione dà `true`. `reduce` porta con sé un **accumulatore** (`a`): parte dal valore iniziale, qui `0`, e a ogni giro diventa quello che la funzione restituisce.', 'They all take a function that is called for every item. `map` always gives back an array of the **same** length as the original; `filter` one of the same length or shorter, because it keeps only the items for which the function gives `true`. `reduce` carries an **accumulator** (`a`): it starts from the initial value, here `0`, and at every round becomes what the function gives back.'),
            C('const n = [1, 2, 3, 4];
n.map(x => x * 2);        // [2, 4, 6, 8]
n.filter(x => x % 2 === 0); // [2, 4]
n.find(x => x > 2);       // 3
n.reduce((a, x) => a + x, 0); // 10
n.some(x => x > 3);       // true', 'js'),
            P('`map` e `filter` creano un nuovo array e non toccano l’originale. Si possono concatenare.', '`map` and `filter` create a new array and leave the original alone. They can be chained.'),
            Fig(fig_jsint_pipe(),
                L('Quattro righe di caselle: l’array 1, 2, 3, 4; dopo map(x => x * 2) diventa 2, 4, 6, 8; dopo filter(x => x > 4) restano 6 e 8, mentre 2 e 4 sono barrati; dopo reduce con la somma resta un solo valore, 14.', 'Four rows of cells: the array 1, 2, 3, 4; after map(x => x * 2) it becomes 2, 4, 6, 8; after filter(x => x > 4) only 6 and 8 remain, while 2 and 4 are crossed out; after reduce with a sum a single value is left, 14.'),
                L('Una catena: `map` trasforma, `filter` scarta, `reduce` riassume in un valore.', 'A chain: `map` transforms, `filter` drops, `reduce` sums it up into one value.')),
        ], [
            Q('m1', L('Quale metodo trasforma ogni elemento?', 'Which method transforms each item?'), ['map', 'filter', 'find', 'some'], 0, L('L’array risultante ha la stessa lunghezza.', 'The resulting array has the same length.')),
            Q('m2', L('`filter` cambia l’array originale?', 'Does `filter` change the original array?'), [L('No', 'No'), L('Sì', 'Yes'), L('A volte', 'Sometimes'), L('Solo con `let`', 'Only with `let`')], 0, L('Ne crea uno nuovo.', 'It creates a new one.')),
            Q('m3', L('Quanto vale `[1,2].reduce((a,x)=>a+x, 0)`?', 'What is `[1,2].reduce((a,x)=>a+x, 0)`?'), ['3', '0', '12', '[1,2]'], 0, L('Somma partendo da 0.', 'It adds up starting from 0.')),
        ], task('js', L('Da `prezzi`, crea `cari` con `filter` tenendo quelli sopra `10`.', 'From `prezzi`, create `cari` with `filter`, keeping those above `10`.'), 'const prezzi = [5, 12, 30];
', [re('cari\s*=\s*prezzi\.filter\(', L('Usi `filter`', 'You use `filter`')), re('>\s*10', L('Il limite è 10', 'The limit is 10'))], '', "const prezzi = [5, 12, 30];\nconst cari = prezzi.filter(p => p > 10);"), [
            dMatch(pMatch(), [['map', L('Trasforma tutti', 'Transforms all')], ['filter', L('Tiene alcuni', 'Keeps some')], ['find', L('Trova il primo', 'Finds the first')], ['reduce', L('Riduce a un valore', 'Reduces to one value')], ['some', L('Almeno uno?', 'At least one?')]], L('Niente cicli a mano: il codice racconta che cosa vuoi.', 'No manual loops: the code tells what you want.')),
            dFlash(pFlash(), [[L('`[1,2].map(x => x + 1)` dà `[2, 3]`', '`[1,2].map(x => x + 1)` gives `[2, 3]`'), true], [L('`find` restituisce un array', '`find` gives back an array'), false], [L('`filter` può dare un array vuoto', '`filter` can give an empty array'), true], [L('`map` può cambiare la lunghezza', '`map` can change the length'), false]], L('`find` restituisce l’elemento, non una lista.', '`find` gives back the item, not a list.')),
        ]),
        lesson('modern-syntax', L('Sintassi moderna', 'Modern syntax'), [
            P('La **destrutturazione** estrae valori in un colpo. Lo **spread** `...` copia e unisce. `?.` legge in sicurezza una proprietà che può mancare, `??` dà un valore di riserva.', '**Destructuring** pulls out values in one go. **Spread** `...` copies and merges. `?.` safely reads a property that may be missing, `??` gives a fallback value.'),
            C('const utente = { nome: "Sav", punti: 10 };
const { nome, punti } = utente;
const [primo, secondo] = ["a", "b"];

const copia = { ...utente, punti: 20 };
const tutti = [...listaA, ...listaB];

utente.indirizzo?.via;     // undefined, nessun errore
const n = utente.vite ?? 3; // 3', 'js'),
            P('`??` scatta solo con `null` e `undefined`: a differenza di `||` non scarta `0` e `""`.', '`??` only fires on `null` and `undefined`: unlike `||` it does not discard `0` and `""`.'),
            P('La destrutturazione è comoda soprattutto nei parametri: `function saluta({ nome, eta = 18 })` prende dall’oggetto solo quello che serve e dà un valore di default. Puoi anche rinominare: `const { nome: chi } = utente`. Lo spread invece copia solo il **primo livello**: gli oggetti annidati restano condivisi, come vedrai parlando di copie.', 'Destructuring is handy above all in parameters: `function saluta({ nome, eta = 18 })` takes from the object only what it needs and gives a default value. You can rename too: `const { nome: chi } = utente`. Spread, on the other hand, copies only the **first level**: nested objects stay shared, as you will see when we talk about copies.'),
        ], [
            Q('s1', L('Che cosa fa `{ ...a }`?', 'What does `{ ...a }` do?'), [L('Copia l’oggetto', 'Copies the object'), L('Lo cancella', 'Deletes it'), L('Lo ordina', 'Sorts it'), L('Dà errore', 'Errors')], 0, L('È una copia superficiale.', 'It is a shallow copy.')),
            Q('s2', L('Quanto vale `0 ?? 5`?', 'What is `0 ?? 5`?'), ['0', '5', 'null', 'false'], 0, L('`0` non è `null` né `undefined`.', '`0` is neither `null` nor `undefined`.')),
            Q('s3', L('Che cosa evita `?.`?', 'What does `?.` avoid?'), [L('L’errore su valori mancanti', 'The error on missing values'), L('I cicli', 'Loops'), L('I commenti', 'Comments'), L('Le funzioni', 'Functions')], 0, L('Restituisce `undefined` invece di fermarsi.', 'It gives back `undefined` instead of stopping.')),
        ], task('js', L('Estrai `titolo` da `libro` con la destrutturazione.', 'Pull `titolo` out of `libro` with destructuring.'), 'const libro = { titolo: "Fucina", pagine: 50 };
', [re('const\s*\{\s*titolo[^}]*\}\s*=\s*libro', L('Destrutturi `libro`', 'You destructure `libro`'))], '', "const libro = { titolo: \"Fucina\", pagine: 50 };\nconst { titolo } = libro;"), [
            dBlanks(pFill(), "const { nome } ___ utente;\nconst copia = { ___utente };\nconst via = utente.casa___via;\nconst vite = utente.vite ___ 3;", ['=', '...', '?.', '??'], ['->', '&&', '::'], L('Quattro scorciatoie che userai ogni giorno.', 'Four shortcuts you will use every day.')),
            dMatch(pMatch(), [['const { a } = o', L('Estrae una proprietà', 'Pulls out a property')], ['[...a, ...b]', L('Unisce due array', 'Merges two arrays')], ['o?.x', L('Legge senza errori', 'Reads without errors')], ['x ?? 1', L('Valore di riserva', 'Fallback value')]], L('Meno righe, stesso significato.', 'Fewer lines, same meaning.')),
        ]),
        lesson('json-storage', L('JSON e localStorage', 'JSON and localStorage'), [
            P('**JSON** è il formato di testo con cui viaggiano i dati. `JSON.stringify` trasforma un valore in testo, `JSON.parse` fa il contrario. `localStorage` conserva testi nel browser, anche dopo la chiusura.', '**JSON** is the text format data travels in. `JSON.stringify` turns a value into text, `JSON.parse` does the opposite. `localStorage` keeps text in the browser, even after closing.'),
            Fig(L(fig_jsint_json('it'), fig_jsint_json('en')),
                L('Tre riquadri: l’oggetto { punti: 10 }, il testo JSON e la chiave «gioco» in localStorage. Le frecce vanno dall’oggetto al testo con JSON.stringify e dal testo a localStorage con setItem; al ritorno getItem e JSON.parse.', 'Three boxes: the object { punti: 10 }, the JSON text and the key “gioco” in localStorage. Arrows go from the object to the text with JSON.stringify and from the text to localStorage with setItem; on the way back getItem and JSON.parse.'),
                L('`localStorage` conserva solo testo: JSON fa da ponte all’andata e al ritorno.', '`localStorage` keeps only text: JSON is the bridge both ways.')),
            C('const dati = { punti: 10 };
const testo = JSON.stringify(dati); // \'{"punti":10}\'
localStorage.setItem("gioco", testo);

const salvato = localStorage.getItem("gioco");
const letto = salvato ? JSON.parse(salvato) : null;', 'js'),
            Tip('`localStorage` è visibile a tutti gli script della pagina: mai password o token.', '`localStorage` is visible to all the scripts on the page: never passwords or tokens.'),
            P('JSON non conosce tutto: le funzioni e `undefined` spariscono, e una `Date` diventa una stringa da riconvertire. `JSON.parse` su un testo rovinato **lancia un errore**, quindi i dati salvati vanno letti dentro un `try…catch` (lo vedi nella prossima lezione). `getItem` restituisce `null` se la chiave non esiste, ed è per questo che il codice controlla `salvato` prima di leggerlo.', 'JSON does not know everything: functions and `undefined` disappear, and a `Date` becomes a string to convert back. `JSON.parse` on broken text **throws an error**, so saved data should be read inside a `try…catch` (you will see it in the next lesson). `getItem` gives back `null` if the key does not exist, which is why the code checks `salvato` before reading it.'),
        ], [
            Q('j1', L('Che cosa fa `JSON.parse`?', 'What does `JSON.parse` do?'), [L('Da testo a valore', 'Text to value'), L('Da valore a testo', 'Value to text'), L('Salva', 'Saves'), L('Scarica', 'Downloads')], 0, L('`stringify` fa il percorso inverso.', '`stringify` goes the other way.')),
            Q('j2', L('Che cosa salva `localStorage`?', 'What does `localStorage` save?'), [L('Solo testi', 'Text only'), L('Solo numeri', 'Numbers only'), L('File', 'Files'), L('Oggetti diretti', 'Objects directly')], 0, L('Per gli oggetti serve JSON.', 'Objects need JSON.')),
            Q('j3', L('Che cosa non va in `localStorage`?', 'What must not go in `localStorage`?'), [L('Le password', 'Passwords'), L('Il tema scelto', 'The chosen theme'), L('La lingua', 'The language'), L('Una bozza', 'A draft')], 0, L('Può leggerlo qualsiasi script della pagina.', 'Any script on the page can read it.')),
        ], task('js', L('Salva `dati` in `localStorage` con la chiave `gioco`, usando `JSON.stringify`.', 'Save `dati` in `localStorage` with the key `gioco`, using `JSON.stringify`.'), 'const dati = { punti: 10 };
', [re('localStorage\.setItem\(\s*["\']gioco["\']', L('Usi la chiave giusta', 'You use the right key')), re('JSON\.stringify\(\s*dati\s*\)', L('Converti in testo', 'You convert to text'))], '', "const dati = { punti: 10 };\nlocalStorage.setItem(\"gioco\", JSON.stringify(dati));"), [
            dOrder(pSort(), ['const dati = { livello: 3 };', 'const testo = JSON.stringify(dati);', 'localStorage.setItem("save", testo);', 'const letto = localStorage.getItem("save");', 'const oggetto = JSON.parse(letto);'], L('Oggetto, testo, salvataggio, lettura, di nuovo oggetto.', 'Object, text, saving, reading, object again.')),
            dBug(pBug(), ['const dati = { livello: 3 };', 'localStorage.setItem("save", dati);'], 1, 'localStorage.setItem("save", JSON.stringify(dati));', L('Senza `stringify` si salverebbe il testo `[object Object]`.', 'Without `stringify` you would save the text `[object Object]`.')),
        ]),
        lesson('dates-errors', L('Date, timer ed errori', 'Dates, timers and errors'), [
            P('`Date` lavora con date e orari. `setTimeout` esegue qualcosa dopo un’attesa, `setInterval` a intervalli. `try…catch` gestisce gli errori senza fermare tutto il programma.', '`Date` works with dates and times. `setTimeout` runs something after a wait, `setInterval` at intervals. `try…catch` handles errors without stopping the whole program.'),
            C('const ora = new Date();
ora.getFullYear();

setTimeout(() => console.log("Dopo 1 secondo"), 1000);
const id = setInterval(tick, 500);
clearInterval(id);

try {
  JSON.parse("rotto");
} catch (errore) {
  console.log("Dati non validi");
}', 'js'),
            P('I tempi si indicano in millisecondi. Conserva l’id di `setInterval` per poterlo fermare.', 'Times are given in milliseconds. Keep the id of `setInterval` so you can stop it.'),
            P('Due trappole delle date: i mesi partono da **zero** (`new Date(2026, 0, 15)` è il 15 gennaio) e `new Date()` usa il fuso orario del dispositivo. Un timer non blocca la pagina: il resto del codice continua e la funzione arriva dopo. Per segnalare un errore tuo usa `throw new Error("messaggio")`: lo raccoglierà il `catch` più vicino.', 'Two date traps: months start from **zero** (`new Date(2026, 0, 15)` is 15 January) and `new Date()` uses the time zone of the device. A timer does not block the page: the rest of the code carries on and the function arrives later. To signal an error of your own use `throw new Error("message")`: the nearest `catch` will pick it up.'),
        ], [
            Q('t1', L('In che unità lavora `setTimeout`?', 'What unit does `setTimeout` use?'), [L('Millisecondi', 'Milliseconds'), L('Secondi', 'Seconds'), L('Minuti', 'Minutes'), L('Frame', 'Frames')], 0, L('1000 è un secondo.', '1000 is one second.')),
            Q('t2', L('Come si ferma `setInterval`?', 'How do you stop `setInterval`?'), ['clearInterval(id)', 'stop(id)', 'break', 'return'], 0, L('Per questo si conserva l’id.', 'That is why you keep the id.')),
            Q('t3', L('A che cosa serve `catch`?', 'What is `catch` for?'), [L('A gestire un errore', 'To handle an error'), L('A prendere un file', 'To get a file'), L('A ripetere', 'To repeat'), L('A salvare', 'To save')], 0, L('Il programma continua invece di fermarsi.', 'The program goes on instead of stopping.')),
        ], task('js', L('Con `setTimeout`, esegui `fine()` dopo `2000` millisecondi.', 'With `setTimeout`, run `fine()` after `2000` milliseconds.'), '', [re('setTimeout\(', L('Usi `setTimeout`', 'You use `setTimeout`')), re('fine', L('Chiami `fine`', 'You call `fine`')), re('2000', L('Aspetti 2 secondi', 'You wait 2 seconds'))], '', "setTimeout(fine, 2000);"), [
            dBlanks(pFill(), "___ {\n  JSON.parse(testo);\n} ___ (errore) {\n  mostra(\"Oops\");\n}", ['try', 'catch'], ['if', 'else', 'do'], L('Si prova, e se va male si gestisce.', 'Try it, and if it goes wrong handle it.')),
            dFlash(pFlash(), [[L('`setTimeout` parte una volta sola', '`setTimeout` runs only once'), true], [L('`setInterval` si ripete', '`setInterval` repeats'), true], [L('`setTimeout(f, 1)` aspetta un secondo', '`setTimeout(f, 1)` waits one second'), false], [L('`try` senza `catch` o `finally` è valido', '`try` without `catch` or `finally` is valid'), false]], L('Un `try` ha sempre bisogno di `catch` o `finally`.', 'A `try` always needs `catch` or `finally`.')),
        ]),
        lesson('map-set', L('Map e Set', 'Map and Set'), [
            P('Un **Set** è una raccolta di valori **senza doppioni**: aggiungere due volte lo stesso valore non cambia niente. Una **Map** è come un oggetto, ma le chiavi possono essere di qualsiasi tipo e l’ordine di inserimento è garantito.', 'A **Set** is a collection of values **without duplicates**: adding the same value twice changes nothing. A **Map** is like an object, but keys can be of any type and insertion order is guaranteed.'),
            C('const tag = new Set(["js", "css", "js"]);
tag.size;            // 2
tag.add("html");
tag.has("css");      // true
const unici = [...new Set([3, 1, 3, 2])]; // [3, 1, 2]

const punti = new Map();
punti.set("Ada", 10);
punti.set("Bea", 7);
punti.get("Ada");    // 10
for (const [nome, p] of punti) {
  console.log(nome, p);
}

Object.entries({ a: 1, b: 2 }); // [["a", 1], ["b", 2]]', 'js'),
            P('Per scorrere un oggetto normale usa `Object.entries(obj)`, che dà coppie `[chiave, valore]`. `Object.groupBy(lista, fn)` raggruppa gli elementi di un array secondo il valore restituito dalla funzione.', 'To walk a plain object use `Object.entries(obj)`, which gives `[key, value]` pairs. `Object.groupBy(list, fn)` groups the items of an array by the value the function returns.'),
        ], [
            Q('ms1', L('Quanto vale `new Set([1, 1, 2]).size`?', 'What is `new Set([1, 1, 2]).size`?'), ['3', '1', '2', '0'], 2, L('Il doppione `1` conta una volta sola.', 'The duplicate `1` counts only once.')),
            Q('ms2', L('Come leggi un valore da una Map?', 'How do you read a value from a Map?'), ['mappa["chiave"]', 'mappa.get("chiave")', 'mappa.read("chiave")', 'mappa.chiave()'], 1, L('Le parentesi quadre leggono le proprietà dell’oggetto, non le voci della Map.', 'Square brackets read the object’s properties, not the Map’s entries.')),
            Q('ms3', L('Che cosa dà `Object.entries({ a: 1 })`?', 'What does `Object.entries({ a: 1 })` give?'), ['["a"]', '[1]', '{ a: 1 }', '[["a", 1]]'], 3, L('Un array di coppie chiave-valore.', 'An array of key-value pairs.')),
        ], task('js', L('Togli i doppioni da `voti` creando `unici` con `new Set` e lo spread.', 'Remove the duplicates from `voti` by creating `unici` with `new Set` and spread.'), 'const voti = [7, 8, 7, 9];
', [re('(const|let)\s+unici\s*=\s*(\[\s*\.\.\.\s*new\s+Set\s*\(\s*voti\s*\)\s*\]|Array\.from\s*\(\s*new\s+Set\s*\(\s*voti\s*\)\s*\))', L('`unici` nasce da un Set', '`unici` comes from a Set'))], '', 'const voti = [7, 8, 7, 9];
const unici = [...new Set(voti)];'), [
            dMatch(pMatch(), [['new Set()', L('Valori senza doppioni', 'Values without duplicates')], ['new Map()', L('Coppie chiave-valore', 'Key-value pairs')], ['.has()', L('C’è?', 'Is it there?')], ['.size', L('Quanti sono', 'How many')], ['Object.entries()', L('Coppie da un oggetto', 'Pairs from an object')]], L('Raccolte diverse per bisogni diversi.', 'Different collections for different needs.')),
            dFlash(pFlash(), [[L('Un Set tiene i doppioni', 'A Set keeps duplicates'), false], [L('Una Map accetta chiavi di ogni tipo', 'A Map accepts keys of any type'), true], [L('`mappa.get("x")` legge una voce', '`mappa.get("x")` reads an entry'), true], [L('`set.length` dice quanti valori ci sono', '`set.length` says how many values there are'), false]], L('Set e Map usano `size`, non `length`.', 'Set and Map use `size`, not `length`.')),
        ]),
        lesson('sort-copy', L('Ordinare e copiare', 'Sorting and copying'), [
            P('`sort` ordina un array **sul posto**: cambia l’originale. Senza argomenti confronta i valori come testi, quindi `[10, 9, 1].sort()` dà `[1, 10, 9]`. Per i numeri passa una funzione di confronto: `(a, b) => a - b`.', '`sort` sorts an array **in place**: it changes the original. Without arguments it compares values as text, so `[10, 9, 1].sort()` gives `[1, 10, 9]`. For numbers pass a compare function: `(a, b) => a - b`.'),
            C('const voti = [10, 9, 1];
voti.sort();                  // [1, 10, 9]: ordine da testo!
voti.sort((a, b) => a - b);   // [1, 9, 10]

const nomi = ["Bea", "ada", "Carlo"];
const ordinati = nomi.toSorted((a, b) => a.localeCompare(b)); // nomi non cambia
const alContrario = nomi.toReversed();
const corretto = nomi.with(1, "Ada");

const profonda = structuredClone({ utente: { nome: "Ada" } });', 'js'),
            P('`toSorted`, `toReversed` e `with` restituiscono un **nuovo** array e lasciano intatto l’originale: perfetti quando non vuoi effetti a sorpresa. Lo spread copia solo il primo livello; per una copia completa di oggetti annidati c’è `structuredClone`.', '`toSorted`, `toReversed` and `with` give back a **new** array and leave the original untouched: perfect when you want no surprise side effects. Spread copies only the first level; for a full copy of nested objects there is `structuredClone`.'),
            Fig(L(fig_jsint_copy('it'), fig_jsint_copy('en')),
                L('A sinistra a e la sua copia fatta con lo spread hanno entrambe una proprietà utente che punta allo stesso oggetto { nome: "Ada" }, segnato come condiviso. A destra a e la copia fatta con structuredClone puntano a due oggetti { nome: "Ada" } separati.', 'On the left a and its spread copy both have a utente property pointing to the same object { nome: "Ada" }, marked as shared. On the right a and the copy made with structuredClone point to two separate { nome: "Ada" } objects.'),
                L('Lo spread copia il primo livello; `structuredClone` copia anche gli oggetti annidati.', 'Spread copies the first level; `structuredClone` copies the nested objects too.')),
        ], [
            Q('so1', L('Quanto vale `[10, 9, 1].sort()`?', 'What is `[10, 9, 1].sort()`?'), ['[1, 9, 10]', '[10, 9, 1]', '[1, 10, 9]', '[9, 10, 1]'], 2, L('Senza funzione confronta i testi: `"10"` viene prima di `"9"`.', 'Without a function it compares text: `"10"` comes before `"9"`.')),
            Q('so2', L('Quale funzione ordina i numeri dal più piccolo al più grande?', 'Which function sorts numbers from smallest to largest?'), ['(a, b) => a - b', '(a, b) => b - a', '(a, b) => a + b', '(a, b) => a * b'], 0, L('Un risultato negativo mette `a` prima di `b`.', 'A negative result puts `a` before `b`.')),
            Q('so3', L('Che differenza c’è tra `sort` e `toSorted`?', 'What is the difference between `sort` and `toSorted`?'), [L('Nessuna', 'None'), L('`toSorted` lascia intatto l’originale', '`toSorted` leaves the original untouched'), L('`sort` funziona solo con i testi', '`sort` only works with text'), L('`toSorted` ordina sempre al contrario', '`toSorted` always sorts in reverse')], 1, L('`sort` cambia l’array, `toSorted` ne crea uno nuovo.', '`sort` changes the array, `toSorted` creates a new one.')),
        ], task('js', L('Crea `classifica` ordinando `punti` dal più alto al più basso con `toSorted`, senza cambiare `punti`.', 'Create `classifica` by sorting `punti` from highest to lowest with `toSorted`, without changing `punti`.'), 'const punti = [40, 95, 70];
', [re('(const|let)\s+classifica\s*=\s*punti\.toSorted\s*\(', L('Usi `toSorted`', 'You use `toSorted`')), re('\(\s*(\w+)\s*,\s*(\w+)\s*\)\s*=>\s*\2\s*-\s*\1', L('Dal più alto al più basso', 'From highest to lowest'))], '', 'const punti = [40, 95, 70];
const classifica = punti.toSorted((a, b) => b - a);'), [
            dBug(pBug(), ['const prezzi = [5, 20, 100];', 'prezzi.sort();', 'console.log(prezzi[0]); // il più basso'], 1, 'prezzi.sort((a, b) => a - b);', L('Senza funzione `"100"` viene prima di `"20"` e di `"5"`.', 'Without a function `"100"` comes before `"20"` and `"5"`.')),
            dMatch(pMatch(), [['sort()', L('Ordina l’originale', 'Sorts the original')], ['toSorted()', L('Ordina una copia', 'Sorts a copy')], ['toReversed()', L('Copia al contrario', 'Reversed copy')], ['with(i, v)', L('Copia con un elemento cambiato', 'Copy with one item changed')], ['structuredClone()', L('Copia completa', 'Full copy')]], L('Cambiare o copiare: scegli consapevolmente.', 'Change or copy: choose on purpose.')),
        ]),
        lesson('delegation', L('Propagazione e delega degli eventi', 'Event bubbling and delegation'), [
            P('Un evento non resta sull’elemento cliccato: **risale** (bubbling) verso i genitori, fino al documento. Così puoi mettere un solo ascoltatore sul contenitore e capire con `e.target` quale figlio è stato toccato: è la **delega degli eventi**.', 'An event does not stay on the clicked element: it **bubbles up** to the parents, all the way to the document. So you can put a single listener on the container and find out with `e.target` which child was touched: that is **event delegation**.'),
            Fig(L(fig_jsint_bubble('it'), fig_jsint_bubble('en')),
                L('Riquadri annidati: document contiene body, che contiene ul#lista con tre li. Il secondo li è cliccato; una freccia rosa sale verso l’alto e l’evidenziazione passa dal li alla ul, poi a body, poi a document. Sulla ul è indicato l’unico listener.', 'Nested boxes: document holds body, which holds ul#lista with three li. The second li is clicked; a pink arrow points upwards and the highlight moves from the li to the ul, then to body, then to document. The single listener is marked on the ul.'),
                L('Il click parte dal `li` e risale: il listener sulla `ul` lo sente per tutte le voci.', 'The click starts on the `li` and climbs: the listener on the `ul` hears it for every item.')),
            C('const lista = document.querySelector("#lista");
lista.addEventListener("click", e => {
  const voce = e.target.closest("li");
  if (!voce) return;             // click fuori da un <li>
  voce.classList.toggle("fatto");
  console.log(voce.dataset.id);  // da <li data-id="7">: "7"
});

// funziona anche per le voci aggiunte dopo
const nuova = document.createElement("li");
nuova.textContent = "Comprare il pane";
lista.append(nuova);', 'js'),
            P('`closest(selettore)` risale dall’elemento al primo antenato che corrisponde, o restituisce `null`. `e.stopPropagation()` ferma la risalita, ma usalo con parsimonia. Gli attributi `data-*` portano informazioni sugli elementi e si leggono con `dataset`, sempre come stringhe.', '`closest(selector)` climbs from the element to the first matching ancestor, or gives back `null`. `e.stopPropagation()` stops the bubbling, but use it sparingly. `data-*` attributes carry information on elements and are read with `dataset`, always as strings.'),
        ], [
            Q('dl1', L('Che cosa significa che un evento «risale»?', 'What does it mean that an event “bubbles up”?'), [L('Si ripete due volte', 'It repeats twice'), L('Va solo al primo figlio', 'It only goes to the first child'), L('Passa dall’elemento ai suoi genitori', 'It passes from the element to its parents'), L('Viene annullato dal browser', 'The browser cancels it')], 2, L('Per questo il contenitore lo sente.', 'That is why the container hears it.')),
            Q('dl2', L('Perché la delega funziona anche con le voci aggiunte dopo?', 'Why does delegation also work with items added later?'), [L('L’ascoltatore sta sul contenitore, che esiste già', 'The listener is on the container, which already exists'), L('Il browser copia gli ascoltatori sui nuovi elementi', 'The browser copies listeners onto new elements'), L('`append` aggiunge anche l’evento', '`append` also adds the event'), L('`closest` crea gli ascoltatori mancanti', '`closest` creates the missing listeners')], 0, L('I nuovi figli fanno risalire i loro click allo stesso contenitore.', 'The new children bubble their clicks up to the same container.')),
            Q('dl3', L('Con `<li data-id="7">`, che cosa vale `voce.dataset.id`?', 'With `<li data-id="7">`, what is `voce.dataset.id`?'), ['7', '"7"', 'undefined', '"data-id"'], 1, L('`dataset` restituisce sempre stringhe.', '`dataset` always gives back strings.')),
        ], task('js', L('Aggiungi a `menu` un solo ascoltatore di `click` che con `e.target.closest("button")` trova il bottone cliccato.', 'Add to `menu` a single `click` listener that finds the clicked button with `e.target.closest("button")`.'), 'const menu = document.querySelector("nav");
', [re('menu\.addEventListener\(\s*["\']click["\']', L('Ascolti il click sul contenitore', 'You listen to the click on the container')), re('\.closest\(\s*["\']button["\']\s*\)', L('Trovi il bottone', 'You find the button'))], '', 'const menu = document.querySelector("nav");
menu.addEventListener("click", e => {
  const btn = e.target.closest("button");
  if (btn) console.log(btn.textContent);
});'), [
            dOrder(pSort(), ['lista.addEventListener("click", e => {', '  const voce = e.target.closest("li");', '  if (!voce) return;', '  voce.classList.toggle("fatto");', '});'], L('Ascolta, trova la voce, controlla, agisci.', 'Listen, find the item, check, act.')),
            dFlash(pFlash(), [[L('Un click su un `<li>` arriva anche alla `<ul>`', 'A click on an `<li>` also reaches the `<ul>`'), true], [L('`closest` cerca tra i figli', '`closest` searches among the children'), false], [L('`dataset` restituisce stringhe', '`dataset` gives back strings'), true], [L('Con la delega serve un ascoltatore per ogni voce', 'With delegation you need one listener per item'), false]], L('`closest` risale verso gli antenati.', '`closest` climbs towards the ancestors.')),
        ]),
    ],
];
