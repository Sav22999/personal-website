<?php
/** JavaScript intermediate: the DOM, events, array methods, modern syntax and JSON. */

return [
    'id' => 'js-intermediate', 'family' => 'js', 'after' => 'js-base',
    'title' => L('JavaScript intermedio', 'Intermediate JavaScript'), 'short' => L('JS intermedio', 'JS Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Fai reagire le pagine: DOM, eventi, metodi degli array, sintassi moderna, JSON e salvataggio dati.', 'Make pages react: DOM, events, array methods, modern syntax, JSON and saving data.'),
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
            C('const n = [1, 2, 3, 4];
n.map(x => x * 2);        // [2, 4, 6, 8]
n.filter(x => x % 2 === 0); // [2, 4]
n.find(x => x > 2);       // 3
n.reduce((a, x) => a + x, 0); // 10
n.some(x => x > 3);       // true', 'js'),
            P('`map` e `filter` creano un nuovo array e non toccano l’originale. Si possono concatenare.', '`map` and `filter` create a new array and leave the original alone. They can be chained.'),
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
            C('const dati = { punti: 10 };
const testo = JSON.stringify(dati); // \'{"punti":10}\'
localStorage.setItem("gioco", testo);

const salvato = localStorage.getItem("gioco");
const letto = salvato ? JSON.parse(salvato) : null;', 'js'),
            Tip('`localStorage` è visibile a tutti gli script della pagina: mai password o token.', '`localStorage` is visible to all the scripts on the page: never passwords or tokens.'),
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
        ], [
            Q('t1', L('In che unità lavora `setTimeout`?', 'What unit does `setTimeout` use?'), [L('Millisecondi', 'Milliseconds'), L('Secondi', 'Seconds'), L('Minuti', 'Minutes'), L('Frame', 'Frames')], 0, L('1000 è un secondo.', '1000 is one second.')),
            Q('t2', L('Come si ferma `setInterval`?', 'How do you stop `setInterval`?'), ['clearInterval(id)', 'stop(id)', 'break', 'return'], 0, L('Per questo si conserva l’id.', 'That is why you keep the id.')),
            Q('t3', L('A che cosa serve `catch`?', 'What is `catch` for?'), [L('A gestire un errore', 'To handle an error'), L('A prendere un file', 'To get a file'), L('A ripetere', 'To repeat'), L('A salvare', 'To save')], 0, L('Il programma continua invece di fermarsi.', 'The program goes on instead of stopping.')),
        ], task('js', L('Con `setTimeout`, esegui `fine()` dopo `2000` millisecondi.', 'With `setTimeout`, run `fine()` after `2000` milliseconds.'), '', [re('setTimeout\(', L('Usi `setTimeout`', 'You use `setTimeout`')), re('fine', L('Chiami `fine`', 'You call `fine`')), re('2000', L('Aspetti 2 secondi', 'You wait 2 seconds'))], '', "setTimeout(fine, 2000);"), [
            dBlanks(pFill(), "___ {\n  JSON.parse(testo);\n} ___ (errore) {\n  mostra(\"Oops\");\n}", ['try', 'catch'], ['if', 'else', 'do'], L('Si prova, e se va male si gestisce.', 'Try it, and if it goes wrong handle it.')),
            dFlash(pFlash(), [[L('`setTimeout` parte una volta sola', '`setTimeout` runs only once'), true], [L('`setInterval` si ripete', '`setInterval` repeats'), true], [L('`setTimeout(f, 1)` aspetta un secondo', '`setTimeout(f, 1)` waits one second'), false], [L('`try` senza `catch` o `finally` è valido', '`try` without `catch` or `finally` is valid'), false]], L('Un `try` ha sempre bisogno di `catch` o `finally`.', 'A `try` always needs `catch` or `finally`.')),
        ]),
    ],
];
