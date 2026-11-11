<?php
/** React basics: components and JSX, props, state with useState, events and lists. */

return [
    'id' => 'react-base', 'family' => 'react', 'after' => 'js-base',
    'title' => L('React base', 'React Basics'), 'short' => L('React base', 'React Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('La libreria più usata per le interfacce: componenti e JSX, props, stato con `useState`, eventi, liste e rendering condizionale.', 'The most used library for interfaces: components and JSX, props, state with `useState`, events, lists and conditional rendering.'),
    'lessons' => [
        lesson('components', L('Componenti e JSX', 'Components and JSX'), [
            P('In React un’interfaccia è fatta di **componenti**: funzioni JavaScript che restituiscono **JSX**, una sintassi che somiglia all’HTML. Il nome di un componente inizia con la maiuscola; si usa come un tag: `<Saluto />`.', 'In React an interface is made of **components**: JavaScript functions returning **JSX**, a syntax that looks like HTML. A component’s name starts with a capital letter; you use it like a tag: `<Saluto />`.'),
            C('function Saluto() {
  const nome = "Ada";
  return (
    <div className="card">
      <h1>Ciao, {nome}!</h1>
      <p>Oggi è {new Date().toLocaleDateString()}</p>
    </div>
  );
}

export default function App() {
  return <Saluto />;
}', 'jsx'),
            P('Dentro le graffe `{}` metti qualsiasi espressione JavaScript. Differenze dall’HTML: `className` al posto di `class`, ogni tag va chiuso (`<img />`) e un componente restituisce **un solo** elemento radice (o un frammento `<>…</>`).', 'Inside braces `{}` you put any JavaScript expression. Differences from HTML: `className` instead of `class`, every tag must be closed (`<img />`) and a component returns **one** root element (or a fragment `<>…</>`).'),
        ], [
            Q('c1', L('Come si scrive l’attributo class in JSX?', 'How do you write the class attribute in JSX?'), ['className', 'class', 'cssClass', 'styleClass'], 0, L('`class` è una parola riservata di JavaScript.', '`class` is a reserved JavaScript word.')),
            Q('c2', L('Con che lettera inizia il nome di un componente?', 'What letter does a component name start with?'), [L('Maiuscola', 'Capital'), L('Minuscola', 'Lowercase'), L('Un trattino', 'A dash'), L('Un numero', 'A number')], 0, L('Le minuscole sono tag HTML.', 'Lowercase are HTML tags.')),
            Q('c3', L('Che cosa va dentro le graffe in JSX?', 'What goes inside braces in JSX?'), [L('Un’espressione JavaScript', 'A JavaScript expression'), L('Solo testo', 'Only text'), L('Solo CSS', 'Only CSS'), L('Un commento HTML', 'An HTML comment')], 0, L('Variabili, calcoli, chiamate.', 'Variables, maths, calls.')),
        ], task('jsx', L('Scrivi il componente `Benvenuto` che restituisce un `<h1>` con il testo `Benvenuto in Fucina` e una classe `titolo` (con `className`).', 'Write the component `Benvenuto` returning an `<h1>` with the text `Benvenuto in Fucina` and a class `titolo` (with `className`).'), '', [re('function\s+Benvenuto\s*\(\s*\)', L('Il componente esiste', 'The component exists')), re('return\s*\(?\s*<h1\s+className\s*=\s*["\']titolo["\']\s*>\s*Benvenuto in Fucina\s*</h1>', L('Restituisce il titolo', 'It returns the heading'))], '', "function Benvenuto() {\n  return <h1 className=\"titolo\">Benvenuto in Fucina</h1>;\n}"), [
            dBug(pBug(), ['function Card() {', '  return (', '    <div class="card">', '      <h2>Titolo</h2>', '    </div>', '  );', '}'], 2, '    <div className="card">', L('In JSX si usa `className`.', 'JSX uses `className`.')),
            dFlash(pFlash(), [[L('Un componente React è una funzione', 'A React component is a function'), true], [L('In JSX `<img>` può restare aperto', 'In JSX `<img>` can stay unclosed'), false], [L('Le graffe contengono espressioni JavaScript', 'Braces hold JavaScript expressions'), true], [L('Un componente può restituire due radici senza frammento', 'A component can return two roots without a fragment'), false]], L('Le regole di JSX.', 'JSX rules.')),
        ]),
        lesson('props', L('Props', 'Props'), [
            P('Le **props** sono i parametri di un componente: si passano come attributi e si ricevono come oggetto. Si usano spesso con la **destrutturazione**. Le props sono in sola lettura: un componente non le modifica.', '**Props** are a component’s parameters: you pass them as attributes and receive them as an object. They are often used with **destructuring**. Props are read-only: a component does not change them.'),
            C('function Prodotto({ nome, prezzo, inOfferta = false }) {
  return (
    <article>
      <h3>{nome}</h3>
      <p>{prezzo.toFixed(2)} €{inOfferta && " · in offerta!"}</p>
    </article>
  );
}

<Prodotto nome="Tazza" prezzo={8.5} inOfferta />', 'jsx'),
            P('I testi si passano tra virgolette, tutto il resto tra graffe (`prezzo={8.5}`). Un attributo senza valore vale `true`. La prop speciale `children` contiene quello che sta tra il tag di apertura e quello di chiusura.', 'Text is passed in quotes, everything else in braces (`prezzo={8.5}`). An attribute without a value is `true`. The special `children` prop holds whatever sits between the opening and closing tag.'),
        ], [
            Q('p1', L('Come passi il numero 8.5 a una prop?', 'How do you pass the number 8.5 to a prop?'), ['prezzo={8.5}', 'prezzo="8.5"', 'prezzo=8.5', 'prezzo:8.5'], 0, L('Tra virgolette sarebbe una stringa.', 'In quotes it would be a string.')),
            Q('p2', L('Un componente può modificare le sue props?', 'Can a component change its props?'), [L('No, sono in sola lettura', 'No, they are read-only'), L('Sì, sempre', 'Yes, always'), L('Solo i numeri', 'Only numbers'), L('Solo dentro un ciclo', 'Only inside a loop')], 0, L('Per cambiare dati serve lo stato.', 'Changing data needs state.')),
            Q('p3', L('Che cosa contiene `children`?', 'What does `children` contain?'), [L('Il contenuto tra i tag del componente', 'The content between the component’s tags'), L('I componenti figli nel file', 'Child components in the file'), L('Lo stato', 'The state'), L('Gli stili', 'The styles')], 0, L('Utile per contenitori e layout.', 'Handy for wrappers and layouts.')),
        ], task('jsx', L('Scrivi `Avatar({ nome, taglia })` che restituisce `<img alt={nome} width={taglia} />`, poi usalo con `nome="Ada"` e `taglia={48}`.', 'Write `Avatar({ nome, taglia })` returning `<img alt={nome} width={taglia} />`, then use it with `nome="Ada"` and `taglia={48}`.'), '', [re('function\s+Avatar\s*\(\s*\{\s*nome\s*,\s*taglia\s*\}\s*\)', L('Riceve le props', 'It receives the props')), re('<img\s+alt\s*=\s*\{\s*nome\s*\}\s+width\s*=\s*\{\s*taglia\s*\}\s*/>', L('Le usa', 'It uses them')), re('<Avatar\s+nome\s*=\s*["\']Ada["\']\s+taglia\s*=\s*\{\s*48\s*\}\s*/>', L('Lo usi con le props giuste', 'You use it with the right props'))], '', "function Avatar({ nome, taglia }) {\n  return <img alt={nome} width={taglia} />;\n}\n\n<Avatar nome=\"Ada\" taglia={48} />"), [
            dBlanks(pFill(), "function Pulsante({ ___, onClick }) {\n  return <button onClick={onClick}>{children}</button>;\n}\n<Pulsante onClick={salva}>Salva</___>", ['children', 'Pulsante'], ['props', 'button', 'text'], L('Il contenuto arriva in `children`.', 'Content arrives in `children`.')),
            dMatch(pMatch(), [['nome="Tazza"', L('Prop di testo', 'Text prop')], ['prezzo={8.5}', L('Prop numerica', 'Numeric prop')], ['inOfferta', L('Vale true', 'Equals true')], ['children', L('Contenuto tra i tag', 'Content between tags')]], L('Passare le props.', 'Passing props.')),
        ]),
        lesson('state', L('Stato ed eventi', 'State and events'), [
            P('Lo **stato** è la memoria di un componente: quando cambia, React ridisegna il componente. Si crea con l’hook **`useState`**, che restituisce il valore attuale e una funzione per cambiarlo.', '**State** is a component’s memory: when it changes, React redraws the component. You create it with the **`useState`** hook, which returns the current value and a function to change it.'),
            C('import { useState } from "react";

function Contatore() {
  const [conta, setConta] = useState(0);
  return (
    <button onClick={() => setConta(conta + 1)}>
      Hai cliccato {conta} volte
    </button>
  );
}', 'jsx'),
            P('Gli eventi si scrivono in camelCase (`onClick`, `onChange`, `onSubmit`) e ricevono una **funzione**, non una chiamata: `onClick={salva}`, non `onClick={salva()}`. Non modificare mai lo stato direttamente: usa sempre la funzione `set…`.', 'Events are written in camelCase (`onClick`, `onChange`, `onSubmit`) and take a **function**, not a call: `onClick={salva}`, not `onClick={salva()}`. Never change state directly: always use the `set…` function.'),
        ], [
            Q('s1', L('Che cosa restituisce `useState(0)`?', 'What does `useState(0)` return?'), [L('Il valore e una funzione per cambiarlo', 'The value and a function to change it'), L('Solo il numero 0', 'Only the number 0'), L('Un componente', 'A component'), L('Una promessa', 'A promise')], 0, L('Una coppia: [valore, setValore].', 'A pair: [value, setValue].')),
            Q('s2', L('Che cosa succede quando lo stato cambia?', 'What happens when state changes?'), [L('React ridisegna il componente', 'React redraws the component'), L('La pagina si ricarica', 'The page reloads'), L('Niente', 'Nothing'), L('Si perde lo stato', 'State is lost')], 0, L('È il cuore di React.', 'It is the heart of React.')),
            Q('s3', L('Qual è il modo giusto di passare un gestore di clic?', 'What is the right way to pass a click handler?'), ['onClick={salva}', 'onClick={salva()}', 'onclick="salva()"', 'click={salva}'], 0, L('Con le parentesi la chiameresti subito.', 'With parentheses you would call it at once.')),
        ], task('jsx', L('Crea `Interruttore` con uno stato `acceso` (iniziale `false`) e un `<button>` che a ogni clic lo inverte con `setAcceso(!acceso)`.', 'Create `Interruttore` with a state `acceso` (initially `false`) and a `<button>` that toggles it on each click with `setAcceso(!acceso)`.'), "import { useState } from \"react\";\n\nfunction Interruttore() {\n  \n}", [re('const\s*\[\s*acceso\s*,\s*setAcceso\s*\]\s*=\s*useState\s*\(\s*false\s*\)', L('Lo stato', 'The state')), re('onClick\s*=\s*\{\s*\(\s*\)\s*=>\s*setAcceso\s*\(\s*!\s*acceso\s*\)\s*\}', L('Il clic lo inverte', 'The click toggles it'))], '', "function Interruttore() {\n  const [acceso, setAcceso] = useState(false);\n  return <button onClick={() => setAcceso(!acceso)}>{acceso ? \"ON\" : \"OFF\"}</button>;\n}"), [
            dBug(pBug(), ['function Like() {', '  const [n, setN] = useState(0);', '  return <button onClick={setN(n + 1)}>❤ {n}</button>;', '}'], 2, '  return <button onClick={() => setN(n + 1)}>❤ {n}</button>;', L('Serve una funzione, non una chiamata immediata.', 'It needs a function, not an immediate call.')),
            dOrder(pSort(), ['import { useState } from "react";', 'function Contatore() {', '  const [conta, setConta] = useState(0);', '  return <button onClick={() => setConta(conta + 1)}>{conta}</button>;', '}'], L('Importa, definisci, crea lo stato, restituisci.', 'Import, define, create state, return.')),
        ]),
        lesson('lists', L('Liste e condizioni', 'Lists and conditions'), [
            P('Per mostrare un elenco si usa **`map`** su un array e si dà a ogni elemento una **`key`** unica e stabile (di solito l’id), che aiuta React a capire cosa è cambiato. Evita l’indice come key se l’elenco può cambiare ordine.', 'To show a list you use **`map`** on an array and give each item a unique, stable **`key`** (usually the id), which helps React figure out what changed. Avoid the index as key if the list can reorder.'),
            C('function ListaSpesa({ voci }) {
  if (voci.length === 0) return <p>La lista è vuota.</p>;
  return (
    <ul>
      {voci.map(v => (
        <li key={v.id}>
          {v.nome} {v.preso ? "✓" : ""}
        </li>
      ))}
    </ul>
  );
}', 'jsx'),
            P('Per mostrare qualcosa solo a certe condizioni: un `if` con `return` anticipato, l’operatore ternario `cond ? a : b`, oppure `cond && <Elemento />`.', 'To show something only under certain conditions: an `if` with early `return`, the ternary operator `cond ? a : b`, or `cond && <Element />`.'),
        ], [
            Q('l1', L('A che cosa serve la prop `key` nelle liste?', 'What is the `key` prop for in lists?'), [L('A far capire a React quali elementi sono cambiati', 'Helping React tell which items changed'), L('A ordinare l’elenco', 'Sorting the list'), L('A cifrare i dati', 'Encrypting data'), L('A dare uno stile', 'Styling')], 0, L('Unica e stabile.', 'Unique and stable.')),
            Q('l2', L('Quale metodo trasforma un array in elementi JSX?', 'Which method turns an array into JSX elements?'), ['map', 'forEach', 'filter', 'reduce'], 0, L('`forEach` non restituisce niente.', '`forEach` returns nothing.')),
            Q('l3', L('Che cosa mostra `{errore && <p>{errore}</p>}` se errore è vuoto?', 'What does `{errore && <p>{errore}</p>}` show if errore is empty?'), [L('Niente', 'Nothing'), L('Un paragrafo vuoto', 'An empty paragraph'), L('Un errore di React', 'A React error'), L('La parola false', 'The word false')], 0, L('Rendering condizionale.', 'Conditional rendering.')),
        ], task('jsx', L('Mostra l’array `utenti` come `<ul>` con un `<li key={u.id}>{u.nome}</li>` per ciascuno, usando `map`.', 'Show the `utenti` array as a `<ul>` with an `<li key={u.id}>{u.nome}</li>` for each, using `map`.'), "function Utenti({ utenti }) {\n  return (\n    \n  );\n}", [re('<ul>[\s\S]*utenti\.map\s*\(\s*\(?\s*u\s*\)?\s*=>', L('Usi map', 'You use map')), re('<li\s+key\s*=\s*\{\s*u\.id\s*\}\s*>\s*\{\s*u\.nome\s*\}\s*</li>', L('Ogni voce ha la sua key', 'Each item has its key'))], '', "<ul>\n  {utenti.map(u => <li key={u.id}>{u.nome}</li>)}\n</ul>"), [
            dBug(pBug(), ['<ul>', '  {prodotti.map(p => (', '    <li>{p.nome}</li>', '  ))}', '</ul>'], 2, '    <li key={p.id}>{p.nome}</li>', L('Ogni elemento di una lista vuole una `key`.', 'Every list item needs a `key`.')),
            dMatch(pMatch(), [['map', L('Array → elementi', 'Array → elements')], ['key', L('Identità dell’elemento', 'Item identity')], ['cond && <A/>', L('Mostra A se vero', 'Shows A if true')], ['a ? b : c', L('Uno o l’altro', 'One or the other')]], L('Liste e condizioni in JSX.', 'Lists and conditions in JSX.')),
        ]),
    ],
];
