<?php
/** React basics: components and JSX, props, state with useState, events and lists, immutable updates, lifting state up and refs. */

/** A tree of components: App at the top, its children below, props flowing down the branches. */
if (!function_exists('fig_reactbase_tree')) {
    function fig_reactbase_tree(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="rbta" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="260" y="14" width="120" height="44" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<g fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"><rect x="70" y="94" width="150" height="44" rx="12"/><rect x="400" y="94" width="170" height="44" rx="12"/></g>
<g fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"><rect x="300" y="174" width="90" height="44" rx="12"/><rect x="420" y="174" width="90" height="44" rx="12"/><rect x="540" y="174" width="90" height="44" rx="12"/></g>
<g text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="320" y="41" font-weight="700">App</text><text x="145" y="121">Intestazione</text><text x="485" y="121">ListaCorsi</text><text x="345" y="201">Corso</text><text x="465" y="201">Corso</text><text x="585" y="201">Corso</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#rbta)"><path d="M290 58C260 80 180 74 160 90"/><path d="M350 58C380 80 470 74 480 90"/><path d="M440 138C410 160 360 154 350 170"/><path d="M470 138V170"/><path d="M520 138C550 160 580 154 585 170"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#5A35C8"><text x="196" y="74">utente</text><text x="420" y="74">corsi</text><text x="478" y="160">titolo</text></g>
</svg>
SVG;
    }
}

/** The render cycle: an event calls set, React runs the component again and updates only what changed on screen. */
if (!function_exists('fig_reactbase_cycle')) {
    function fig_reactbase_cycle(string $l): string
    {
        $t = $l === 'it' ? ['1 · evento', '2 · nuovo stato', '3 · nuovo render', '4 · schermo aggiornato', 'React richiama', 'cambia solo il testo', 'il pulsante mostra 1'] : ['1 · event', '2 · new state', '3 · new render', '4 · screen updated', 'React calls', 'only the text changes', 'the button shows 1'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.rbc{opacity:0;animation:rbc 6s infinite}.rbc2{animation-delay:1.5s}.rbc3{animation-delay:3s}.rbc4{animation-delay:4.5s}@keyframes rbc{0%{opacity:0}4%{opacity:1}22%{opacity:1}26%{opacity:0}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.rbc{animation:none}}</style>
<defs><marker id="rbca" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g stroke-width="2"><rect x="40" y="24" width="220" height="80" rx="12" fill="#FFF3EA" stroke="#E8590C"/><rect x="380" y="24" width="220" height="80" rx="12" fill="#ECE6FF" stroke="#7048E8"/><rect x="380" y="156" width="220" height="80" rx="12" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="40" y="156" width="220" height="80" rx="12" fill="#D5F6EA" stroke="#0B7F5C"/></g>
<g fill="none" stroke="#F03E82" stroke-width="4"><rect class="rbc" x="34" y="18" width="232" height="92" rx="15"/><rect class="rbc rbc2" x="374" y="18" width="232" height="92" rx="15"/><rect class="rbc rbc3" x="374" y="150" width="232" height="92" rx="15"/><rect class="rbc rbc4" x="34" y="150" width="232" height="92" rx="15"/></g>
<g font-size="13" font-weight="700" fill="#5B5880"><text x="56" y="48">{$t[0]}</text><text x="396" y="48">{$t[1]}</text><text x="396" y="180">{$t[2]}</text><text x="56" y="180">{$t[3]}</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="14"><text x="56" y="80">onClick</text><text x="396" y="80">setConta(c =&gt; c + 1)</text><text x="396" y="222">Contatore()</text></g>
<g font-size="13"><text x="396" y="202">{$t[4]}</text><text x="56" y="204">{$t[6]}</text><text x="56" y="222" fill="#5B5880">{$t[5]}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#rbca)"><path d="M260 64H376"/><path d="M490 104V152"/><path d="M380 196H264"/><path d="M150 156V108"/></g>
</svg>
SVG;
    }
}

/** Lifting state up: before, the state is stuck in Filtro and Elenco cannot see it; after, Rubrica owns it and passes it to both. */
if (!function_exists('fig_reactbase_lift')) {
    function fig_reactbase_lift(string $l): string
    {
        $t = $l === 'it' ? ['prima', 'dopo', 'non lo vede', 'una sola fonte di verità'] : ['before', 'after', 'cannot see it', 'a single source of truth'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="rbla" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-weight="700" font-size="16"><text x="20" y="30">{$t[0]}</text><text x="300" y="30">{$t[1]}</text></g>
<path d="M282 16V236" stroke="#E4E2F0" stroke-width="2"/>
<rect x="70" y="46" width="160" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="20" y="150" width="116" height="66" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="152" y="150" width="116" height="66" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="150" y="71" text-anchor="middle">Rubrica</text><text x="32" y="174">Filtro</text><text x="32" y="200" font-weight="700" fill="#B84508">testo</text><text x="164" y="174">Elenco</text><text x="164" y="200" fill="#B0245A">?</text></g>
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M120 86V146"/><path d="M190 86V146"/></g>
<path d="M136 183H150" stroke="#F03E82" stroke-width="2" stroke-dasharray="3 3"/>
<text x="210" y="236" text-anchor="middle" font-size="12" font-weight="700" fill="#B0245A">✗ {$t[2]}</text>
<rect x="370" y="44" width="200" height="56" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="470" y="66" text-anchor="middle">Rubrica</text><text x="470" y="88" text-anchor="middle" font-weight="700" fill="#B84508">[testo, setTesto]</text></g>
<g fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"><rect x="300" y="160" width="140" height="56" rx="10"/><rect x="490" y="160" width="140" height="56" rx="10"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" text-anchor="middle"><text x="370" y="193">Filtro</text><text x="560" y="193">Elenco</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#rbla)"><path d="M400 100V156"/><path d="M340 156V100" stroke="#B84508"/><path d="M540 100V156"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12"><text x="406" y="132">testo</text><text x="270" y="132" fill="#B84508">onCambia</text><text x="546" y="132">testo</text></g>
<text x="465" y="240" text-anchor="middle" font-size="13" font-weight="700" fill="#0B7F5C">{$t[3]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'react-base', 'family' => 'react', 'after' => 'js-base',
    'title' => L('React base', 'React Basics'), 'short' => L('React base', 'React Basics'), 'level' => L('Base', 'Basics'),
    'desc' => L('La libreria più usata per le interfacce: componenti e JSX, props, stato con `useState` e aggiornamenti immutabili, eventi, liste, stato condiviso e ref.', 'The most used library for interfaces: components and JSX, props, state with `useState` and immutable updates, events, lists, shared state and refs.'),
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
            Fig(fig_reactbase_tree(),
                L('Albero di componenti: App in cima ha due figli, Intestazione, che riceve la prop utente, e ListaCorsi, che riceve corsi. ListaCorsi ha tre figli Corso, ognuno con la prop titolo.', 'A component tree: App at the top has two children, Intestazione, which receives the utente prop, and ListaCorsi, which receives corsi. ListaCorsi has three Corso children, each with the titolo prop.'),
                L('Un’app React è un albero di componenti: i dati scendono lungo i rami come props.', 'A React app is a tree of components: data flows down the branches as props.')),
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
            P('Ogni volta che chiami la funzione `set…`, React esegue di nuovo il componente con il valore nuovo e aggiorna sullo schermo solo quello che è cambiato. Attenzione: il valore cambia al **prossimo** render. Subito dopo `setConta(conta + 1)`, nella stessa funzione `conta` vale ancora il numero vecchio. Ogni copia del componente ha il suo stato: due `<Contatore />` contano ognuno per conto suo.', 'Every time you call the `set…` function, React runs the component again with the new value and updates on screen only what has changed. Careful: the value changes at the **next** render. Right after `setConta(conta + 1)`, in the same function `conta` still holds the old number. Every copy of the component has its own state: two `<Contatore />` each count on their own.'),
            Fig(L(fig_reactbase_cycle('it'), fig_reactbase_cycle('en')),
                L('Un ciclo di quattro riquadri: l’evento onClick chiama setConta(c => c + 1), che crea il nuovo stato; React richiama Contatore() per un nuovo render; lo schermo si aggiorna e il pulsante mostra 1, cambiando solo il testo. Un’evidenziazione percorre i passi in ordine.', 'A cycle of four boxes: the onClick event calls setConta(c => c + 1), which creates the new state; React calls Contatore() again for a new render; the screen updates and the button shows 1, changing only the text. A highlight walks the steps in order.'),
                L('Evento, nuovo stato, nuovo render, schermo aggiornato: e si ricomincia.', 'Event, new state, new render, updated screen: and round again.')),
        ], [
            Q('s1', L('Che cosa restituisce `useState(0)`?', 'What does `useState(0)` return?'), [L('Il valore e una funzione per cambiarlo', 'The value and a function to change it'), L('Solo il numero 0', 'Only the number 0'), L('Un componente', 'A component'), L('Una promessa', 'A promise')], 0, L('Una coppia: [valore, setValore].', 'A pair: [value, setValue].')),
            Q('s2', L('Che cosa succede quando lo stato cambia?', 'What happens when state changes?'), [L('React ridisegna il componente', 'React redraws the component'), L('La pagina si ricarica', 'The page reloads'), L('Niente', 'Nothing'), L('Si perde lo stato', 'State is lost')], 0, L('È il cuore di React.', 'It is the heart of React.')),
            Q('s3', L('Qual è il modo giusto di passare un gestore di clic?', 'What is the right way to pass a click handler?'), ['onClick={salva}', 'onClick={salva()}', 'onclick="salva()"', 'click={salva}'], 0, L('Con le parentesi la chiameresti subito.', 'With parentheses you would call it at once.')),
        ], task('jsx', L('Crea `Interruttore` con uno stato `acceso` (iniziale `false`) e un `<button>` che a ogni clic lo inverte con `setAcceso(!acceso)`.', 'Create `Interruttore` with a state `acceso` (initially `false`) and a `<button>` that toggles it on each click with `setAcceso(!acceso)`.'), "import { useState } from \"react\";\n\nfunction Interruttore() {\n  \n}", [re('const\s*\[\s*acceso\s*,\s*setAcceso\s*\]\s*=\s*useState\s*\(\s*false\s*\)', L('Lo stato', 'The state')), re('onClick\s*=\s*\{\s*\(\s*\)\s*=>\s*setAcceso\s*\(\s*!\s*acceso\s*\)\s*\}', L('Il clic lo inverte', 'The click toggles it'))], '', "function Interruttore() {\n  const [acceso, setAcceso] = useState(false);\n  return <button onClick={() => setAcceso(!acceso)}>{acceso ? \"ON\" : \"OFF\"}</button>;\n}"), [
            dBug(pBug(), ['function Like() {', '  const [n, setN] = useState(0);', '  return <button onClick={setN(n + 1)}>Mi piace {n}</button>;', '}'], 2, '  return <button onClick={() => setN(n + 1)}>Mi piace {n}</button>;', L('Serve una funzione, non una chiamata immediata.', 'It needs a function, not an immediate call.')),
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
        lesson('forms', L('Form ed eventi dell’utente', 'Forms and user events'), [
            P('Un campo **controllato** prende il valore dallo stato e lo aggiorna con `onChange`: React resta l’unica fonte di verità. Nel `submit` chiama `e.preventDefault()` per non ricaricare la pagina.', 'A **controlled** field takes its value from state and updates it with `onChange`: React stays the single source of truth. In `submit` call `e.preventDefault()` so the page does not reload.'),
            C('function Iscrizione() {
  const [email, setEmail] = useState("");
  function invia(e) {
    e.preventDefault();
    alert("Iscritto: " + email);
  }
  return (
    <form onSubmit={invia}>
      <input value={email} onChange={e => setEmail(e.target.value)} />
      <button disabled={!email}>Iscriviti</button>
    </form>
  );
}', 'jsx'),
            P('Gli eventi in JSX si scrivono in **camelCase** (`onClick`, `onChange`, `onSubmit`) e ricevono una **funzione**, non una chiamata: `onClick={salva}`, non `onClick={salva()}`.', 'Events in JSX are written in **camelCase** (`onClick`, `onChange`, `onSubmit`) and receive a **function**, not a call: `onClick={salva}`, not `onClick={salva()}`.'),
            P('Con un campo **non controllato** il valore resta nel DOM e lo leggi solo all’invio; con uno controllato React sa sempre cosa c’è scritto, quindi puoi validare a ogni tasto o disattivare il pulsante. Le caselle di spunta usano `checked` invece di `value`, e anche `<select>` e `<textarea>` prendono `value`. Un errore tipico è mettere `value` senza `onChange`: il campo diventa di sola lettura.', 'With an **uncontrolled** field the value stays in the DOM and you read it only on submit; with a controlled one React always knows what is written, so you can validate at every key or disable the button. Checkboxes use `checked` instead of `value`, and `<select>` and `<textarea>` take `value` too. A typical mistake is setting `value` without `onChange`: the field becomes read-only.'),
        ], [
            Q('fo1', L('Che cos’è un campo controllato?', 'What is a controlled field?'), [L('Un campo il cui valore viene dallo stato', 'A field whose value comes from state'), L('Un campo disabilitato', 'A disabled field'), L('Un campo con validazione HTML', 'A field with HTML validation'), L('Un campo nascosto', 'A hidden field')], 0, L('`value` più `onChange`.', '`value` plus `onChange`.')),
            Q('fo2', L('Perché `e.preventDefault()` nel submit?', 'Why `e.preventDefault()` in submit?'), [L('Per non ricaricare la pagina', 'So the page does not reload'), L('Per svuotare il form', 'To empty the form'), L('Per inviare due volte', 'To send twice'), L('Per bloccare React', 'To block React')], 0, L('Il comportamento normale del form ricarica.', 'The form’s default behaviour reloads.')),
            Q('fo3', L('Quale scrittura è giusta?', 'Which is right?'), ['onClick={salva}', 'onclick="salva()"', 'onClick={salva()}', 'on-click={salva}'], 0, L('Passi la funzione, non il risultato.', 'You pass the function, not its result.')),
        ], task('jsx', L('Scrivi un `<input>` controllato con `value={nome}` e `onChange` che chiama `setNome(e.target.value)`.', 'Write a controlled `<input>` with `value={nome}` and `onChange` calling `setNome(e.target.value)`.'), '', [re('<input[^>]*value\s*=\s*\{\s*nome\s*\}', L('Il valore viene dallo stato', 'The value comes from state')), re('onChange\s*=\s*\{\s*\(?\s*e\s*\)?\s*=>\s*setNome\(\s*e\.target\.value\s*\)\s*\}', L('`onChange` aggiorna lo stato', '`onChange` updates state'))], '', '<input value={nome} onChange={e => setNome(e.target.value)} />'), [
            dBug(pBug(), ['function Bottone() {', '  function saluta() { alert("Ciao"); }', '  return <button onClick={saluta()}>Ciao</button>;', '}'], 2, '  return <button onClick={saluta}>Ciao</button>;', L('Passa la funzione, non chiamarla.', 'Pass the function, do not call it.')),
            dFlash(pFlash(), [[L('onChange aggiorna lo stato a ogni tasto', 'onChange updates state on every key'), true], [L('Gli eventi JSX si scrivono tutti minuscoli', 'JSX events are all lowercase'), false], [L('preventDefault evita il ricaricamento', 'preventDefault avoids the reload'), true], [L('onClick={f()} esegue f solo al clic', 'onClick={f()} runs f only on click'), false]], L('Eventi e form.', 'Events and forms.')),
        ]),
        lesson('state-updates', L('Aggiornare oggetti e liste nello stato', 'Updating objects and lists in state'), [
            P('Lo stato va trattato come **immutabile**: non si modifica un oggetto o un array che sta nello stato, se ne crea uno nuovo. React confronta il valore vecchio con il nuovo: se è lo stesso oggetto, solo modificato, non si accorge del cambiamento.', 'State must be treated as **immutable**: you do not change an object or array held in state, you create a new one. React compares the old value with the new one: if it is the same object, just modified, it does not notice the change.'),
            C('const [utente, setUtente] = useState({ nome: "Ada", punti: 0 });
const [voci, setVoci] = useState(["pane"]);

// oggetti: copia con lo spread e cambia ciò che serve
setUtente({ ...utente, punti: utente.punti + 1 });

// array: crea sempre un array nuovo
setVoci([...voci, "latte"]);                  // aggiungere
setVoci(voci.filter(v => v !== "pane"));      // togliere
setVoci(voci.map(v => (v === "latte" ? "latte di soia" : v))); // cambiare

// quando il nuovo valore dipende dal precedente
setConta(c => c + 1);', 'jsx'),
            P('Evita `push`, `splice` o `utente.punti++` sullo stato. Quando il nuovo valore dipende da quello vecchio, passa una **funzione di aggiornamento** (`c => c + 1`): React le applica in ordine, anche se chiami `set` più volte di fila.', 'Avoid `push`, `splice` or `utente.punti++` on state. When the new value depends on the old one, pass an **updater function** (`c => c + 1`): React applies them in order, even if you call `set` several times in a row.'),
        ], [
            Q('su1', L('Come aggiungi `"latte"` all’array di stato `voci`?', 'How do you add `"latte"` to the `voci` state array?'), ['voci.push("latte")', 'setVoci([...voci, "latte"])', 'voci[voci.length] = "latte"', 'setVoci(voci.push("latte"))'], 1, L('Un array nuovo, con l’elemento in più.', 'A new array, with the extra item.')),
            Q('su2', L('Partendo da 0, dopo tre `setConta(conta + 1)` nello stesso clic quanto vale `conta`?', 'Starting from 0, after three `setConta(conta + 1)` in the same click what is `conta`?'), ['3', '0', '1', '2'], 2, L('Tutte e tre leggono lo stesso `conta`, cioè 0. Con `c => c + 1` arriveresti a 3.', 'All three read the same `conta`, which is 0. With `c => c + 1` you would reach 3.')),
            Q('su3', L('Perché non modificare direttamente un oggetto nello stato?', 'Why not change an object in state directly?'), [L('È più lento da scrivere', 'It is slower to write'), L('Gli oggetti non possono cambiare', 'Objects cannot change'), L('Dà sempre un errore', 'It always errors'), L('React potrebbe non accorgersi del cambiamento', 'React may not notice the change')], 3, L('React confronta i riferimenti, non il contenuto.', 'React compares references, not content.')),
        ], task('jsx', L('Scrivi `rinomina(nome)` che aggiorna lo stato `utente` con `setUtente({ ...utente, nome })`.', 'Write `rinomina(nome)` that updates the `utente` state with `setUtente({ ...utente, nome })`.'), 'const [utente, setUtente] = useState({ nome: "Ada", punti: 0 });
', [re('function\s+rinomina\s*\(\s*nome\s*\)|rinomina\s*=\s*\(?\s*nome\s*\)?\s*=>', L('La funzione riceve `nome`', 'The function receives `nome`')), re('setUtente\s*\(\s*(\{\s*\.\.\.\s*utente\s*,\s*nome(\s*:\s*nome)?\s*\}|\(?\s*\w+\s*\)?\s*=>\s*\(\s*\{\s*\.\.\.\s*\w+\s*,\s*nome(\s*:\s*nome)?\s*\}\s*\))', L('Crei un oggetto nuovo con lo spread', 'You create a new object with spread'))], '', 'const [utente, setUtente] = useState({ nome: "Ada", punti: 0 });
function rinomina(nome) {
  setUtente({ ...utente, nome });
}'), [
            dBug(pBug(), ['const [voci, setVoci] = useState([]);', 'function aggiungi(v) {', '  voci.push(v);', '}'], 2, '  setVoci([...voci, v]);', L('`push` cambia l’array senza avvisare React: serve un array nuovo con `setVoci`.', '`push` changes the array without telling React: you need a new array with `setVoci`.')),
            dMatch(pMatch(), [['[...voci, x]', L('Aggiunge in fondo', 'Adds to the end')], ['voci.filter(…)', L('Toglie elementi', 'Removes items')], ['voci.map(…)', L('Cambia elementi', 'Changes items')], ['{ ...o, k: v }', L('Cambia una proprietà', 'Changes a property')], ['c => c + 1', L('Aggiorna dal valore precedente', 'Updates from the previous value')]], L('Sempre valori nuovi, mai modifiche sul posto.', 'Always new values, never in-place changes.')),
        ]),
        lesson('lift-state', L('Sollevare lo stato', 'Lifting state up'), [
            P('Quando due componenti devono mostrare lo **stesso dato**, lo stato non può stare in uno dei due: lo sposti nel loro **genitore comune**. Il genitore passa giù il valore con le props e, insieme, una funzione per cambiarlo.', 'When two components must show the **same data**, the state cannot live in one of them: you move it to their **common parent**. The parent passes the value down with props, together with a function to change it.'),
            Fig(L(fig_reactbase_lift('it'), fig_reactbase_lift('en')),
                L('Prima: lo stato testo sta dentro Filtro e il fratello Elenco non lo vede. Dopo: lo stato [testo, setTesto] sta nel padre Rubrica, che passa testo a Filtro e a Elenco, mentre Filtro risale con onCambia.', 'Before: the testo state lives inside Filtro and its sibling Elenco cannot see it. After: the [testo, setTesto] state lives in the parent Rubrica, which passes testo to Filtro and Elenco, while Filtro sends changes up with onCambia.'),
                L('Lo stato sale al primo genitore comune, poi scende a chi ne ha bisogno.', 'The state moves up to the closest common parent, then flows down to whoever needs it.')),
            C('function Filtro({ testo, onCambia }) {
  return <input value={testo} onChange={e => onCambia(e.target.value)} />;
}

function Elenco({ voci, testo }) {
  const visibili = voci.filter(v => v.toLowerCase().includes(testo.toLowerCase()));
  return <ul>{visibili.map(v => <li key={v}>{v}</li>)}</ul>;
}

export default function Rubrica() {
  const [testo, setTesto] = useState("");
  const voci = ["Ada", "Bea", "Carlo"];
  return (
    <>
      <Filtro testo={testo} onCambia={setTesto} />
      <Elenco voci={voci} testo={testo} />
    </>
  );
}', 'jsx'),
            P('Così c’è **una sola fonte di verità**: `Rubrica` possiede il testo, `Filtro` lo cambia tramite `onCambia`, `Elenco` lo legge. Non copiare le props nello stato del figlio e non mettere nello stato ciò che puoi calcolare, come `visibili`.', 'That way there is **a single source of truth**: `Rubrica` owns the text, `Filtro` changes it through `onCambia`, `Elenco` reads it. Do not copy props into the child’s state and do not put in state what you can compute, like `visibili`.'),
        ], [
            Q('ls1', L('Dove va lo stato condiviso da due componenti fratelli?', 'Where does state shared by two sibling components go?'), [L('In entrambi, copiato', 'In both, copied'), L('In una variabile globale', 'In a global variable'), L('Nel genitore comune', 'In the common parent'), L('Nel primo dei due', 'In the first of the two')], 2, L('Poi scende ai figli con le props.', 'Then it goes down to the children with props.')),
            Q('ls2', L('Come fa un figlio a cambiare lo stato del genitore?', 'How does a child change the parent’s state?'), [L('Modificando le sue props', 'By changing its props'), L('Chiamando una funzione ricevuta come prop', 'By calling a function received as a prop'), L('Con `document.querySelector`', 'With `document.querySelector`'), L('Non può in nessun modo', 'It cannot in any way')], 1, L('Per esempio `onCambia(nuovoValore)`.', 'For example `onCambia(newValue)`.')),
            Q('ls3', L('Perché `visibili` non sta nello stato?', 'Why is `visibili` not in state?'), [L('Si può calcolare dai dati che ci sono già', 'It can be computed from the data already there'), L('Gli array non possono stare nello stato', 'Arrays cannot be in state'), L('È troppo grande', 'It is too big'), L('React lo vieta', 'React forbids it')], 0, L('Meno stato, meno valori da tenere allineati.', 'Less state, fewer values to keep in sync.')),
        ], task('jsx', L('In `Pagina` crea lo stato `tema` e passa a `<Interruttore>` le props `tema={tema}` e `onCambia={setTema}`.', 'In `Pagina` create the `tema` state and pass `<Interruttore>` the props `tema={tema}` and `onCambia={setTema}`.'), 'function Pagina() {
  return <Interruttore />;
}', [re('const\s*\[\s*tema\s*,\s*setTema\s*\]\s*=\s*useState\s*\(', L('Lo stato è nel genitore', 'The state is in the parent')), re('<Interruttore[^>]*tema\s*=\s*\{\s*tema\s*\}', L('Passi il valore', 'You pass the value')), re('<Interruttore[^>]*onCambia\s*=\s*\{\s*setTema\s*\}', L('Passi la funzione', 'You pass the function'))], '', 'function Pagina() {
  const [tema, setTema] = useState("chiaro");
  return <Interruttore tema={tema} onCambia={setTema} />;
}'), [
            dFlash(pFlash(), [[L('Lo stato condiviso va nel genitore comune', 'Shared state goes in the common parent'), true], [L('Un figlio può riassegnare le sue props', 'A child can reassign its props'), false], [L('Un dato calcolabile non va messo nello stato', 'Computable data should not go in state'), true], [L('Due copie dello stesso stato restano sempre allineate', 'Two copies of the same state always stay in sync'), false]], L('Una sola fonte di verità.', 'A single source of truth.')),
            dBlanks(pFill(), "function Padre() {\n  const [n, setN] = ___(0);\n  return <Bottone valore={n} ___={setN} />;\n}\nfunction Bottone({ valore, onCambia }) {\n  return <button onClick={() => ___(valore + 1)}>{valore}</button>;\n}", ['useState', 'onCambia', 'onCambia'], ['useEffect', 'setN', 'valore'], L('Il padre possiede lo stato, il figlio chiama la funzione ricevuta.', 'The parent owns the state, the child calls the function it received.')),
        ]),
        lesson('refs', L('useRef e il DOM', 'useRef and the DOM'), [
            P('`useRef` crea una scatola che **sopravvive ai rendering** ma che, quando cambia, **non** fa ridisegnare il componente. Serve a due cose: tenere un riferimento a un elemento del DOM e ricordare valori che non si vedono a schermo, come l’id di un timer.', '`useRef` creates a box that **survives renders** but that, when it changes, does **not** make the component redraw. It is for two things: holding a reference to a DOM element and remembering values not shown on screen, like a timer id.'),
            C('function Ricerca() {
  const campo = useRef(null);
  return (
    <>
      <input ref={campo} />
      <button onClick={() => campo.current.focus()}>Cerca</button>
    </>
  );
}

function Cronometro() {
  const [secondi, setSecondi] = useState(0);
  const timer = useRef(null);
  const avvia = () => { timer.current = setInterval(() => setSecondi(s => s + 1), 1000); };
  const ferma = () => clearInterval(timer.current);
  return <p>{secondi}s <button onClick={avvia}>Via</button> <button onClick={ferma}>Stop</button></p>;
}', 'jsx'),
            P('Il valore sta in `.current`. Non leggere né scrivere `ref.current` durante il rendering: usalo nei gestori di eventi e negli effetti. Da React 19 `ref` si passa ai tuoi componenti come una prop qualsiasi, senza `forwardRef`.', 'The value lives in `.current`. Do not read or write `ref.current` during rendering: use it in event handlers and effects. Since React 19 `ref` is passed to your components like any other prop, without `forwardRef`.'),
        ], [
            Q('rf1', L('Dove sta il valore di un ref?', 'Where does a ref’s value live?'), ['ref.value', 'ref.current', 'ref()', 'ref.state'], 1, L('Sempre in `.current`.', 'Always in `.current`.')),
            Q('rf2', L('Che cosa succede quando cambi `ref.current`?', 'What happens when you change `ref.current`?'), [L('Il componente si ridisegna', 'The component redraws'), L('La pagina si ricarica', 'The page reloads'), L('React lancia un errore', 'React throws an error'), L('Niente: il componente non si ridisegna', 'Nothing: the component does not redraw')], 3, L('Per ciò che si vede a schermo serve lo stato.', 'For what shows on screen you need state.')),
            Q('rf3', L('Per quale di questi un ref è adatto?', 'Which of these is a ref suited for?'), [L('Mettere il focus su un input', 'Focusing an input'), L('Mostrare un contatore a schermo', 'Showing a counter on screen'), L('Sostituire tutte le props', 'Replacing all props'), L('Salvare dati sul server', 'Saving data on the server')], 0, L('Un accesso diretto all’elemento del DOM.', 'Direct access to the DOM element.')),
        ], task('jsx', L('Crea `const campo = useRef(null)` e collegalo all’input con `ref={campo}`.', 'Create `const campo = useRef(null)` and attach it to the input with `ref={campo}`.'), 'function Modulo() {
  return <input />;
}', [re('const\s+campo\s*=\s*useRef\s*\(\s*(null)?\s*\)', L('Crei il ref', 'You create the ref')), re('<input[^>]*ref\s*=\s*\{\s*campo\s*\}', L('Lo colleghi all’input', 'You attach it to the input'))], '', 'function Modulo() {
  const campo = useRef(null);
  return <input ref={campo} />;
}'), [
            dMatch(pMatch(), [['useRef', L('Valore che non ridisegna', 'Value that does not redraw')], ['ref={x}', L('Collega un elemento', 'Attaches an element')], ['x.current', L('Il valore del ref', 'The ref’s value')], ['useState', L('Valore che ridisegna', 'Value that redraws')]], L('Ref e stato: due memorie diverse.', 'Ref and state: two different memories.')),
            dBug(pBug(), ['function Campo() {', '  const c = useRef(null);', '  return <input ref={c} onFocus={() => c.select()} />;', '}'], 2, '  return <input ref={c} onFocus={() => c.current.select()} />;', L('L’elemento sta in `c.current`, non in `c`.', 'The element is in `c.current`, not in `c`.')),
        ]),
    ],
];
