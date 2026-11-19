<?php
/** Intermediate React: useEffect and data fetching, forms, lifting state and context, custom hooks and performance, useReducer and tests, React 19 actions, Suspense and use, React Router. */

/** The timeline of an effect: render, the DOM updated, the effect runs; when a dependency changes the cleanup runs first, and again at unmount. */
if (!function_exists('fig_reactint_effect')) {
    function fig_reactint_effect(string $l): string
    {
        $t = $l === 'it' ? ['render', 'DOM aggiornato', 'effetto', 'cambia citta', 'pulizia', 'effetto', 'smontato', 'pulizia', 'tempo'] : ['render', 'DOM updated', 'effect', 'citta changes', 'cleanup', 'effect', 'unmounted', 'cleanup', 'time'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 200" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="riea" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<path d="M12 100H624" stroke="#8A87B0" stroke-width="2" marker-end="url(#riea)"/>
<text x="620" y="190" text-anchor="end" font-size="13" fill="#5B5880">{$t[8]} →</text>
<g stroke-width="2"><rect x="16" y="78" width="78" height="44" rx="10" fill="#F1F0F8" stroke="#8A87B0"/><rect x="102" y="78" width="96" height="44" rx="10" fill="#F1F0F8" stroke="#8A87B0"/><rect x="206" y="78" width="70" height="44" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="342" y="78" width="72" height="44" rx="10" fill="#FFE3EE" stroke="#F03E82"/><rect x="420" y="78" width="70" height="44" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/><rect x="546" y="78" width="72" height="44" rx="10" fill="#FFE3EE" stroke="#F03E82"/></g>
<g text-anchor="middle" font-size="13"><text x="55" y="105">{$t[0]}</text><text x="150" y="105" font-size="12">{$t[1]}</text><text x="241" y="105" font-weight="700">{$t[2]}</text><text x="378" y="105" font-weight="700">{$t[4]}</text><text x="455" y="105" font-weight="700">{$t[5]}</text><text x="582" y="105" font-weight="700">{$t[7]}</text></g>
<g stroke="#E8590C" stroke-width="2" stroke-dasharray="4 3"><path d="M318 40V160"/><path d="M528 40V160"/></g>
<g text-anchor="middle" font-size="13" font-weight="700" fill="#B84508"><text x="318" y="32">{$t[3]}</text><text x="528" y="32">{$t[6]}</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#5B5880" text-anchor="middle"><text x="241" y="146">fetch()</text><text x="378" y="146">annullato</text><text x="455" y="146">fetch()</text></g>
</svg>
SVG;
    }
}

/** Prop drilling against Context: on the left tema crosses every level as a prop, on the right Pulsante reads it straight from the context. */
if (!function_exists('fig_reactint_context')) {
    function fig_reactint_context(string $l): string
    {
        $t = $l === 'it' ? ['prop drilling', 'Context', 'passa e basta', 'nessuna prop'] : ['prop drilling', 'Context', 'only passes it on', 'no props'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="rica" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<g font-weight="700" font-size="16"><text x="20" y="28">{$t[0]}</text><text x="340" y="28">{$t[1]}</text></g>
<path d="M322 14V246" stroke="#E4E2F0" stroke-width="2"/>
<g stroke-width="2"><rect x="20" y="42" width="150" height="34" rx="10" fill="#ECE6FF" stroke="#7048E8"/><rect x="20" y="100" width="150" height="34" rx="10" fill="#F1F0F8" stroke="#8A87B0"/><rect x="20" y="158" width="150" height="34" rx="10" fill="#F1F0F8" stroke="#8A87B0"/><rect x="20" y="216" width="150" height="34" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="34" y="64">App</text><text x="34" y="122">Pagina</text><text x="34" y="180">Barra</text><text x="34" y="238">Pulsante</text></g>
<g stroke="#E8590C" stroke-width="2" fill="none" marker-end="url(#rica)"><path d="M95 76V96"/><path d="M95 134V154"/><path d="M95 192V212"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#B84508"><text x="104" y="90">tema</text><text x="104" y="148">tema</text><text x="104" y="206">tema</text></g>
<g font-size="12" fill="#5B5880"><text x="180" y="122">← {$t[2]}</text><text x="180" y="180">← {$t[2]}</text></g>
<rect x="340" y="42" width="280" height="38" rx="10" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="354" y="66" font-family="ui-monospace, Menlo, monospace" font-size="13">&lt;TemaContext value={tema}&gt;</text>
<g fill="#F1F0F8" stroke="#C9C7DD" stroke-width="2" stroke-dasharray="6 4"><rect x="360" y="100" width="150" height="34" rx="10"/><rect x="360" y="158" width="150" height="34" rx="10"/></g>
<rect x="360" y="216" width="190" height="34" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="374" y="122" fill="#8A87B0">Pagina</text><text x="374" y="180" fill="#8A87B0">Barra</text><text x="374" y="238">useContext(…)</text></g>
<path d="M600 80C620 150 600 215 554 233" stroke="#0B7F5C" stroke-width="3" fill="none" marker-end="url(#rica)"/>
<text x="520" y="122" font-size="13" font-weight="700" fill="#0B7F5C">{$t[3]}</text>
</svg>
SVG;
    }
}

/** useReducer as a loop: the interface dispatches an action, the reducer works out the new state, React redraws. */
if (!function_exists('fig_reactint_reducer')) {
    function fig_reactint_reducer(string $l): string
    {
        $t = $l === 'it' ? ['interfaccia', 'funzione pura', 'nuovo stato', 'nuovo render'] : ['interface', 'pure function', 'new state', 'new render'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="rira" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="20" y="70" width="170" height="80" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="396" y="20" width="236" height="80" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="420" y="130" width="200" height="80" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g font-size="13" font-weight="700" fill="#5B5880"><text x="36" y="94">{$t[0]}</text><text x="412" y="44">reducer · {$t[1]}</text><text x="436" y="154">{$t[2]}</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13"><text x="36" y="124">click «Libro»</text><text x="412" y="76">carrello(stato, azione)</text><text x="436" y="188">["Libro"]</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#rira)"><path d="M190 96C290 96 300 60 392 60"/><path d="M520 100V126"/><path d="M416 170C320 170 290 124 194 124"/></g>
<text x="276" y="40" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12" fill="#5A35C8">dispatch({ type: "aggiungi" })</text>
<text x="300" y="184" text-anchor="middle" font-size="13" fill="#0B7F5C">{$t[3]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'react-intermediate', 'family' => 'react', 'after' => 'react-base',
    'title' => L('React intermedio', 'Intermediate React'), 'short' => L('React intermedio', 'React Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Effetti e dati dal server con `useEffect`, moduli e azioni di React 19, Context, hook personalizzati e prestazioni, Suspense e `use`, più pagine con React Router.', 'Effects and server data with `useEffect`, forms and React 19 actions, Context, custom hooks and performance, Suspense and `use`, multiple pages with React Router.'),
    'lessons' => [
        lesson('effects', L('useEffect e dati dal server', 'useEffect and server data'), [
            P('**`useEffect`** esegue codice *dopo* il rendering: caricare dati, iscriversi a eventi, avviare un timer. Il secondo argomento è l’elenco delle **dipendenze**: l’effetto si ripete quando una di esse cambia; con `[]` gira una volta sola, al montaggio.', '**`useEffect`** runs code *after* rendering: loading data, subscribing to events, starting a timer. The second argument is the **dependency** list: the effect re-runs when one of them changes; with `[]` it runs once, on mount.'),
            C('function Meteo({ citta }) {
  const [dati, setDati] = useState(null);
  const [errore, setErrore] = useState("");

  useEffect(() => {
    let annullato = false;
    fetch(`/api/meteo?citta=${encodeURIComponent(citta)}`)
      .then(r => r.json())
      .then(d => { if (!annullato) setDati(d); })
      .catch(() => setErrore("Impossibile caricare il meteo"));
    return () => { annullato = true; };   // pulizia
  }, [citta]);

  if (errore) return <p>{errore}</p>;
  return dati ? <p>{dati.gradi}°</p> : <p>Caricamento…</p>;
}', 'jsx'),
            P('La funzione restituita dall’effetto è la **pulizia**: gira prima del prossimo effetto o quando il componente sparisce. Serve per annullare timer, iscrizioni e risposte arrivate tardi.', 'The function returned by the effect is the **cleanup**: it runs before the next effect or when the component disappears. It cancels timers, subscriptions and late-arriving responses.'),
            Fig(L(fig_reactint_effect('it'), fig_reactint_effect('en')),
                L('Linea del tempo: render, DOM aggiornato, poi l’effetto con fetch(). Quando cambia citta parte prima la pulizia, che imposta annullato, poi di nuovo l’effetto con fetch(). Quando il componente viene smontato parte un’ultima pulizia.', 'A timeline: render, DOM updated, then the effect with fetch(). When citta changes the cleanup runs first, setting annullato, then the effect again with fetch(). When the component is unmounted a final cleanup runs.'),
                L('L’effetto viene dopo il disegno; la pulizia arriva prima del giro successivo e alla fine.', 'The effect comes after drawing; the cleanup comes before the next round and at the end.')),
            P('Errore classico: chiamare un `set…` dentro un effetto senza dipendenze. Ogni render fa partire l’effetto, l’effetto cambia lo stato, lo stato causa un render: un ciclo infinito. Metti nelle dipendenze tutto quello che l’effetto legge, e chiediti se l’effetto serve davvero: un valore calcolato dalle props si calcola nel render. In sviluppo, con `StrictMode`, React monta i componenti due volte apposta, per farti scoprire le pulizie mancanti.', 'A classic mistake: calling a `set…` inside an effect with no dependencies. Every render starts the effect, the effect changes the state, the state causes a render: an endless loop. Put in the dependencies everything the effect reads, and ask yourself whether you really need the effect: a value worked out from props is computed during rendering. In development, with `StrictMode`, React mounts components twice on purpose, to help you find missing cleanups.'),
        ], [
            Q('e1', L('Quando gira un effetto con dipendenze `[]`?', 'When does an effect with `[]` dependencies run?'), [L('Una volta, al montaggio', 'Once, on mount'), L('A ogni rendering', 'On every render'), L('Mai', 'Never'), L('Ogni secondo', 'Every second')], 0, L('Nessuna dipendenza da osservare.', 'No dependency to watch.')),
            Q('e2', L('A che cosa serve la funzione restituita da useEffect?', 'What is the function returned by useEffect for?'), [L('Pulizia: annullare timer e iscrizioni', 'Cleanup: cancelling timers and subscriptions'), L('Restituire dati al genitore', 'Returning data to the parent'), L('Ridisegnare la pagina', 'Redrawing the page'), L('Niente', 'Nothing')], 0, L('Gira prima del prossimo effetto.', 'It runs before the next effect.')),
            Q('e3', L('Perché `citta` sta tra le dipendenze?', 'Why is `citta` among the dependencies?'), [L('Per ricaricare quando cambia città', 'To reload when the city changes'), L('Per decorazione', 'For decoration'), L('È obbligatorio per legge', 'It is mandatory by law'), L('Per renderlo più lento', 'To make it slower')], 0, L('L’effetto dipende da lei.', 'The effect depends on it.')),
        ], task('jsx', L('Scrivi un `useEffect` che imposta `document.title` a `Ciao ${nome}` e si ripete quando cambia `nome`.', 'Write a `useEffect` that sets `document.title` to `Ciao ${nome}` and re-runs when `nome` changes.'), '', [re('useEffect\s*\(\s*\(\s*\)\s*=>\s*\{?\s*document\.title\s*=\s*`Ciao \$\{nome\}`', L('Imposti il titolo', 'You set the title')), re('\}?\s*,\s*\[\s*nome\s*\]\s*\)', L('Dipende da nome', 'It depends on nome'))], '', "useEffect(() => {\n  document.title = `Ciao \${nome}`;\n}, [nome]);"), [
            dMatch(pMatch(), [['[]', L('Una volta al montaggio', 'Once on mount')], ['[citta]', L('Quando cambia citta', 'When citta changes')], [L('Nessun array', 'No array'), L('A ogni rendering', 'On every render')], ['return () => …', L('Pulizia', 'Cleanup')]], L('Le dipendenze di useEffect.', 'useEffect dependencies.')),
            dBug(pBug(), ['useEffect(() => {', '  const id = setInterval(aggiorna, 1000);', '}, []);'], 1, '  const id = setInterval(aggiorna, 1000); return () => clearInterval(id);', L('Il timer va fermato nella pulizia.', 'The timer must be stopped in the cleanup.')),
        ]),
        lesson('forms', L('Moduli controllati', 'Controlled forms'), [
            P('In un **input controllato** il valore sta nello stato di React: `value` legge lo stato, `onChange` lo aggiorna. Così puoi validare, trasformare o svuotare il campo dal codice.', 'In a **controlled input** the value lives in React state: `value` reads the state, `onChange` updates it. That way you can validate, transform or clear the field from code.'),
            C('function Iscrizione() {
  const [email, setEmail] = useState("");
  const valida = email.includes("@");

  function invia(e) {
    e.preventDefault();          // niente ricarica della pagina
    salva(email);
    setEmail("");
  }

  return (
    <form onSubmit={invia}>
      <input type="email" value={email} onChange={e => setEmail(e.target.value)} />
      <button disabled={!valida}>Iscriviti</button>
    </form>
  );
}', 'jsx'),
            P('Nel gestore di `onSubmit` chiama `e.preventDefault()` per evitare che il browser ricarichi la pagina. Per moduli grandi esistono librerie come React Hook Form.', 'In the `onSubmit` handler call `e.preventDefault()` so the browser does not reload the page. For big forms there are libraries like React Hook Form.'),
            P('Con più campi conviene un solo oggetto di stato e una funzione comune: `setDati({ ...dati, [e.target.name]: e.target.value })` aggiorna il campo giusto grazie all’attributo `name`. Gli errori di validazione spesso non vanno nello stato: si calcolano dai valori a ogni render (`const emailOk = dati.email.includes("@")`). Mostrali dopo che l’utente ha lasciato il campo, non mentre inizia a scrivere.', 'With several fields a single state object and a shared function work well: `setDati({ ...dati, [e.target.name]: e.target.value })` updates the right field thanks to the `name` attribute. Validation errors often do not belong in state: they are computed from the values at every render (`const emailOk = dati.email.includes("@")`). Show them after the user has left the field, not while they start typing.'),
        ], [
            Q('f1', L('Dove sta il valore di un input controllato?', 'Where does a controlled input’s value live?'), [L('Nello stato di React', 'In React state'), L('Solo nel DOM', 'Only in the DOM'), L('Nel server', 'On the server'), L('Nel CSS', 'In the CSS')], 0, L('`value` + `onChange`.', '`value` + `onChange`.')),
            Q('f2', L('Perché chiamare `e.preventDefault()` nell’invio?', 'Why call `e.preventDefault()` on submit?'), [L('Per non ricaricare la pagina', 'To avoid reloading the page'), L('Per svuotare il modulo', 'To clear the form'), L('Per validare', 'To validate'), L('Non serve', 'It is not needed')], 0, L('Il comportamento predefinito del form.', 'The form’s default behaviour.')),
            Q('f3', L('Come leggi il testo digitato in onChange?', 'How do you read the typed text in onChange?'), ['e.target.value', 'e.value', 'e.text', 'this.value'], 0, L('Dall’elemento che ha generato l’evento.', 'From the element that fired the event.')),
        ], task('jsx', L('Crea un input controllato per lo stato `nome`: `value={nome}` e `onChange` che chiama `setNome(e.target.value)`.', 'Create a controlled input for the `nome` state: `value={nome}` and `onChange` calling `setNome(e.target.value)`.'), "const [nome, setNome] = useState(\"\");\nreturn (\n  \n);", [re('<input[^>]*value\s*=\s*\{\s*nome\s*\}', L('Il valore viene dallo stato', 'The value comes from state')), re('onChange\s*=\s*\{\s*\(?\s*e\s*\)?\s*=>\s*setNome\s*\(\s*e\.target\.value\s*\)\s*\}', L('onChange aggiorna lo stato', 'onChange updates state'))], '', '<input value={nome} onChange={e => setNome(e.target.value)} />'), [
            dOrder(pSort(), ['function invia(e) {', '  e.preventDefault();', '  salva(email);', '  setEmail("");', '}'], L('Blocca, salva, svuota.', 'Block, save, clear.')),
            dBlanks(pFill(), "<input\n  ___={testo}\n  ___={e => setTesto(e.target.___)}\n/>", ['value', 'onChange', 'value'], ['defaultValue', 'onInput', 'text'], L('Il trio dell’input controllato.', 'The controlled input trio.')),
        ]),
        lesson('context', L('Stato condiviso e Context', 'Shared state and Context'), [
            P('Quando due componenti fratelli devono condividere un dato, si **solleva lo stato** al genitore comune e lo si passa giù con le props. Se le props devono attraversare molti livelli (*prop drilling*), si usa il **Context**.', 'When two sibling components must share data, you **lift state up** to their common parent and pass it down with props. If props must cross many levels (*prop drilling*), you use **Context**.'),
            Fig(L(fig_reactint_context('it'), fig_reactint_context('en')),
                L('A sinistra il prop drilling: tema scende come prop da App a Pagina, a Barra, a Pulsante, e i livelli in mezzo lo passano e basta. A destra con Context: TemaContext avvolge l’albero e Pulsante legge tema con useContext, mentre Pagina e Barra non ricevono nessuna prop.', 'On the left prop drilling: tema goes down as a prop from App to Pagina, to Barra, to Pulsante, and the levels in between only pass it on. On the right with Context: TemaContext wraps the tree and Pulsante reads tema with useContext, while Pagina and Barra receive no props.'),
                L('Con Context chi ha bisogno del dato lo prende direttamente.', 'With Context whoever needs the data takes it directly.')),
            C('const TemaContext = createContext("chiaro");

function App() {
  const [tema, setTema] = useState("chiaro");
  return (
    <TemaContext value={tema}>
      <Pagina />
      <button onClick={() => setTema(tema === "chiaro" ? "scuro" : "chiaro")}>Cambia tema</button>
    </TemaContext>
  );
}

function Pulsante() {
  const tema = useContext(TemaContext);   // nessuna prop da passare
  return <button className={tema}>OK</button>;
}', 'jsx'),
            P('Da React 19 il contesto stesso fa da provider: `<TemaContext value={…}>` (prima si scriveva `<TemaContext.Provider>`, che funziona ancora). Il Context è perfetto per dati “globali” e poco frequenti: tema, lingua, utente collegato. Per stato complesso e molto aggiornato esistono librerie dedicate (Redux, Zustand) o `useReducer`.', 'Since React 19 the context itself acts as the provider: `<TemaContext value={…}>` (before, you wrote `<TemaContext.Provider>`, which still works). Context is perfect for “global”, rarely changing data: theme, language, signed-in user. For complex, frequently updated state there are dedicated libraries (Redux, Zustand) or `useReducer`.'),
        ], [
            Q('x1', L('Che cos’è il prop drilling?', 'What is prop drilling?'), [L('Passare props attraverso molti livelli', 'Passing props through many levels'), L('Un errore di React', 'A React error'), L('Un tipo di hook', 'A type of hook'), L('Un modo di fare CSS', 'A way of doing CSS')], 0, L('Il Context lo evita.', 'Context avoids it.')),
            Q('x2', L('Quale hook legge un Context?', 'Which hook reads a Context?'), ['useContext', 'useState', 'useEffect', 'useRef'], 0, L('Dentro un Provider.', 'Inside a Provider.')),
            Q('x3', L('Per quali dati è adatto il Context?', 'What data suits Context?'), [L('Tema, lingua, utente collegato', 'Theme, language, signed-in user'), L('Ogni carattere digitato', 'Every typed character'), L('I dati di un solo componente', 'Data of a single component'), L('Nessuno', 'None')], 0, L('Globali e poco frequenti.', 'Global and infrequent.')),
        ], task('jsx', L('Crea `LinguaContext` con `createContext("it")` e nel componente `Saluto` leggi la lingua con `useContext(LinguaContext)`.', 'Create `LinguaContext` with `createContext("it")` and in the `Saluto` component read the language with `useContext(LinguaContext)`.'), '', [re('const\s+LinguaContext\s*=\s*createContext\s*\(\s*["\']it["\']\s*\)', L('Il Context', 'The Context')), re('function\s+Saluto[\s\S]*useContext\s*\(\s*LinguaContext\s*\)', L('Saluto lo legge', 'Saluto reads it'))], '', "const LinguaContext = createContext(\"it\");\n\nfunction Saluto() {\n  const lingua = useContext(LinguaContext);\n  return <p>{lingua === \"it\" ? \"Ciao\" : \"Hello\"}</p>;\n}"), [
            dMatch(pMatch(), [['createContext', L('Crea il contesto', 'Creates the context')], ['Provider', L('Fornisce il valore', 'Provides the value')], ['useContext', L('Legge il valore', 'Reads the value')], [L('Sollevare lo stato', 'Lifting state'), L('Spostarlo nel genitore comune', 'Moving it to the common parent')]], L('Condividere lo stato.', 'Sharing state.')),
            dFlash(pFlash(), [[L('Il Context evita di passare props a mano per molti livelli', 'Context avoids passing props by hand through many levels'), true], [L('Fuori da ogni Provider useContext lancia un errore', 'Outside any Provider useContext throws an error'), false], [L('Il tema è un buon uso del Context', 'Theme is a good use of Context'), true], [L('Ogni stato va messo nel Context', 'All state belongs in Context'), false]], L('Fuori da ogni Provider useContext restituisce il valore di default di createContext. Globale solo quando serve.', 'Outside any Provider useContext returns the default value of createContext. Global only when needed.')),
        ]),
        lesson('hooks', L('Hook personalizzati e prestazioni', 'Custom hooks and performance'), [
            P('Un **hook personalizzato** è una funzione che inizia con `use` e usa altri hook: così la logica ripetuta (caricare dati, leggere dal localStorage) si scrive una volta e si riusa.', 'A **custom hook** is a function starting with `use` that uses other hooks: that way repeated logic (loading data, reading localStorage) is written once and reused.'),
            C('function useLocalStorage(chiave, iniziale) {
  const [valore, setValore] = useState(() => {
    const salvato = localStorage.getItem(chiave);
    return salvato !== null ? JSON.parse(salvato) : iniziale;
  });
  useEffect(() => {
    localStorage.setItem(chiave, JSON.stringify(valore));
  }, [chiave, valore]);
  return [valore, setValore];
}

const [tema, setTema] = useLocalStorage("tema", "chiaro");', 'jsx'),
            P('Regole degli hook: chiamali solo al primo livello del componente (mai dentro `if` o cicli) e solo da componenti o altri hook. Per le prestazioni, **`useMemo`** ricorda un calcolo costoso e **`memo`** evita di ridisegnare un componente con le stesse props: usali solo dove misuri un problema. Il **React Compiler** può aggiungere queste ottimizzazioni da solo durante la build.', 'Rules of hooks: call them only at the top level of the component (never inside `if` or loops) and only from components or other hooks. For performance, **`useMemo`** remembers an expensive calculation and **`memo`** avoids redrawing a component with the same props: use them only where you measure a problem. The **React Compiler** can add these optimisations by itself at build time.'),
        ], [
            Q('h1', L('Con che cosa inizia il nome di un hook?', 'What does a hook’s name start with?'), ['use', 'hook', 'get', 'on'], 0, L('Per esempio `useLocalStorage`.', 'For example `useLocalStorage`.')),
            Q('h2', L('Si può chiamare un hook dentro un `if`?', 'Can you call a hook inside an `if`?'), [L('No, solo al primo livello', 'No, only at the top level'), L('Sì, sempre', 'Yes, always'), L('Solo useState', 'Only useState'), L('Solo nei cicli', 'Only in loops')], 0, L('L’ordine degli hook deve restare uguale.', 'Hook order must stay the same.')),
            Q('h3', L('Che cosa fa `useMemo`?', 'What does `useMemo` do?'), [L('Ricorda un calcolo costoso', 'Remembers an expensive calculation'), L('Salva su disco', 'Saves to disk'), L('Crea uno stato', 'Creates state'), L('Carica dati', 'Loads data')], 0, L('Lo ricalcola solo se cambiano le dipendenze.', 'It recalculates only when dependencies change.')),
        ], task('jsx', L('Scrivi l’hook `useContatore(iniziale)` che usa `useState(iniziale)` e restituisce `[conta, () => setConta(conta + 1)]`.', 'Write the hook `useContatore(iniziale)` that uses `useState(iniziale)` and returns `[conta, () => setConta(conta + 1)]`.'), '', [re('function\s+useContatore\s*\(\s*iniziale\s*\)', L('L’hook esiste', 'The hook exists')), re('const\s*\[\s*conta\s*,\s*setConta\s*\]\s*=\s*useState\s*\(\s*iniziale\s*\)', L('Usa lo stato', 'It uses state')), re('return\s*\[\s*conta\s*,\s*\(\s*\)\s*=>\s*setConta\s*\(\s*conta\s*\+\s*1\s*\)\s*\]', L('Restituisce valore e incremento', 'It returns value and increment'))], '', "function useContatore(iniziale) {\n  const [conta, setConta] = useState(iniziale);\n  return [conta, () => setConta(conta + 1)];\n}"), [
            dBug(pBug(), ['function contaClic() {', '  const [n, setN] = useState(0);', '  return [n, () => setN(n + 1)];', '}'], 0, 'function useContaClic() {', L('Un hook personalizzato deve iniziare con `use`.', 'A custom hook must start with `use`.')),
            dMatch(pMatch(), [['useLocalStorage', L('Hook personalizzato', 'Custom hook')], ['useMemo', L('Ricorda un calcolo', 'Remembers a calculation')], ['memo', L('Evita rendering inutili', 'Avoids useless renders')], [L('Primo livello', 'Top level'), L('Dove chiamare gli hook', 'Where to call hooks')]], L('Hook e prestazioni.', 'Hooks and performance.')),
        ]),
        lesson('reducer-tests', L('useReducer e test dei componenti', 'useReducer and component tests'), [
            P('Quando lo stato ha molte azioni collegate, **`useReducer`** le raccoglie in una funzione pura: `(stato, azione) => nuovoStato`. I componenti chiamano `dispatch({type: "aggiungi"})` invece di tanti `setX`.', 'When state has many related actions, **`useReducer`** gathers them in a pure function: `(state, action) => newState`. Components call `dispatch({type: "add"})` instead of many `setX` calls.'),
            Fig(L(fig_reactint_reducer('it'), fig_reactint_reducer('en')),
                L('Un ciclo: l’interfaccia, al click su «Libro», chiama dispatch({ type: "aggiungi" }); il reducer carrello(stato, azione), una funzione pura, calcola il nuovo stato ["Libro"]; il nuovo stato porta a un nuovo render dell’interfaccia.', 'A loop: the interface, on a click on “Libro”, calls dispatch({ type: "aggiungi" }); the reducer carrello(stato, azione), a pure function, works out the new state ["Libro"]; the new state leads to a new render of the interface.'),
                L('Il componente dice cosa è successo, il reducer decide come cambia lo stato.', 'The component says what happened, the reducer decides how the state changes.')),
            C('function carrello(stato, azione) {
  switch (azione.type) {
    case "aggiungi": return [...stato, azione.prodotto];
    case "svuota": return [];
    default: return stato;
  }
}
const [voci, dispatch] = useReducer(carrello, []);
dispatch({ type: "aggiungi", prodotto: "Libro" });', 'jsx'),
            P('Con **React Testing Library** si prova il componente come lo usa una persona: lo si disegna con `render`, si trovano gli elementi per ruolo o testo (`getByRole`) e si simula il clic (meglio con `user-event`, più realistico). Si controlla ciò che si **vede**, non lo stato interno.', 'With **React Testing Library** you test the component the way a person uses it: draw it with `render`, find elements by role or text (`getByRole`) and simulate the click (better with `user-event`, more realistic). You check what is **seen**, not the internal state.'),
        ], [
            Q('rt1', L('Che cosa riceve un reducer?', 'What does a reducer receive?'), [L('Lo stato attuale e un’azione', 'The current state and an action'), L('Solo le props', 'Only props'), L('Il DOM', 'The DOM'), L('Un URL', 'A URL')], 0, L('E restituisce il nuovo stato.', 'And returns the new state.')),
            Q('rt2', L('Un reducer deve…', 'A reducer must…'), [L('Restituire un nuovo stato senza modificare il vecchio', 'Return a new state without changing the old one'), L('Chiamare il server', 'Call the server'), L('Modificare lo stato in place', 'Change state in place'), L('Usare setTimeout', 'Use setTimeout')], 0, L('È una funzione pura.', 'It is a pure function.')),
            Q('rt3', L('Che cosa controlla un buon test di componente?', 'What does a good component test check?'), [L('Ciò che l’utente vede e fa', 'What the user sees and does'), L('Le variabili interne', 'Internal variables'), L('Il numero di render', 'The number of renders'), L('Il nome dei file', 'File names')], 0, L('Così il test sopravvive ai refactoring.', 'That way the test survives refactors.')),
        ], task('jsx', L('Scrivi `const [n, dispatch] = useReducer(contatore, 0);` e poi `dispatch({ type: "piu" });`.', 'Write `const [n, dispatch] = useReducer(contatore, 0);` and then `dispatch({ type: "piu" });`.'), '', [re('const\s*\[\s*n\s*,\s*dispatch\s*\]\s*=\s*useReducer\(\s*contatore\s*,\s*0\s*\)', L('Usi useReducer', 'You use useReducer')), re('dispatch\(\s*\{\s*type\s*:\s*["\']piu["\']\s*\}\s*\)', L('Invii l’azione', 'You dispatch the action'))], '', "const [n, dispatch] = useReducer(contatore, 0);\ndispatch({ type: \"piu\" });"), [
            dOrder(pSort(), ['render(<Contatore />);', 'const btn = screen.getByRole("button");', 'fireEvent.click(btn);', 'expect(screen.getByText("1")).toBeInTheDocument();'], L('Disegna, trova, agisci, verifica.', 'Render, find, act, assert.')),
            dFlash(pFlash(), [[L('Un reducer è una funzione pura', 'A reducer is a pure function'), true], [L('dispatch modifica direttamente lo stato', 'dispatch changes state directly'), false], [L('getByRole trova gli elementi come li vede l’utente', 'getByRole finds elements as the user sees them'), true], [L('I test devono leggere lo stato interno', 'Tests must read internal state'), false]], L('Reducer e test.', 'Reducers and tests.')),
        ]),
        lesson('actions', L('Azioni dei form in React 19', 'Form actions in React 19'), [
            P('In React 19 un `<form>` accetta una **azione**: `action={funzione}`. La funzione riceve un `FormData` con i campi, anche se è asincrona, e React gestisce l’invio. Con `useActionState` ottieni anche il risultato dell’ultima azione e lo stato di attesa.', 'In React 19 a `<form>` accepts an **action**: `action={function}`. The function receives a `FormData` with the fields, even when it is async, and React handles the submit. With `useActionState` you also get the result of the last action and the pending state.'),
            C('import { useActionState } from "react";

async function iscrivi(statoPrecedente, dati) {
  const email = String(dati.get("email"));
  if (!email.includes("@")) return { errore: "Email non valida" };
  await salva(email);
  return { ok: true };
}

function Iscrizione() {
  const [stato, azione, inAttesa] = useActionState(iscrivi, {});
  return (
    <form action={azione}>
      <input name="email" type="email" />
      <button disabled={inAttesa}>{inAttesa ? "Invio…" : "Iscriviti"}</button>
      {stato.errore && <p>{stato.errore}</p>}
    </form>
  );
}', 'jsx'),
            P('Non servono `preventDefault` né uno stato per ogni campo: i valori arrivano nel `FormData` grazie all’attributo `name`. Dopo un’azione riuscita React svuota i campi non controllati. `useFormStatus` (da `react-dom`) dà `pending` a un componente dentro il form, e `useOptimistic` mostra subito il risultato atteso mentre il server risponde.', 'You need neither `preventDefault` nor a state per field: values reach the `FormData` thanks to the `name` attribute. After a successful action React resets uncontrolled fields. `useFormStatus` (from `react-dom`) gives `pending` to a component inside the form, and `useOptimistic` shows the expected result straight away while the server answers.'),
        ], [
            Q('ac1', L('Che cosa riceve la funzione passata a `<form action={…}>`?', 'What does the function passed to `<form action={…}>` receive?'), [L('Un evento da fermare con `preventDefault`', 'An event to stop with `preventDefault`'), L('Un `FormData` con i campi', 'A `FormData` with the fields'), L('Solo il primo campo', 'Only the first field'), L('Niente', 'Nothing')], 1, L('Si legge con `dati.get("nome")`.', 'You read it with `dati.get("nome")`.')),
            Q('ac2', L('Che cosa restituisce `useActionState`?', 'What does `useActionState` return?'), [L('Solo una Promise', 'Only a Promise'), L('Un ref al form', 'A ref to the form'), L('Stato, azione da passare al form e stato di attesa', 'State, action to pass to the form and pending state'), L('Il DOM del form', 'The form’s DOM')], 2, L('`[stato, azione, inAttesa]`.', '`[state, action, isPending]`.')),
            Q('ac3', L('Come fa un campo a finire nel `FormData`?', 'How does a field end up in the `FormData`?'), [L('Con `value={…}`', 'With `value={…}`'), L('Con l’attributo `id`', 'With the `id` attribute'), L('Con `onChange`', 'With `onChange`'), L('Con l’attributo `name`', 'With the `name` attribute')], 3, L('Come in un form HTML normale.', 'As in a normal HTML form.')),
        ], task('jsx', L('Collega il form all’azione: `const [stato, azione, inAttesa] = useActionState(salva, null)` e `<form action={azione}>`.', 'Connect the form to the action: `const [stato, azione, inAttesa] = useActionState(salva, null)` and `<form action={azione}>`.'), 'function Nota() {
  return (
    <form>
      <input name="testo" />
      <button>Salva</button>
    </form>
  );
}', [re('const\s*\[\s*stato\s*,\s*azione\s*(,\s*inAttesa\s*)?\]\s*=\s*useActionState\s*\(\s*salva\s*,', L('Usi `useActionState`', 'You use `useActionState`')), re('<form[^>]*action\s*=\s*\{\s*azione\s*\}', L('Il form usa l’azione', 'The form uses the action'))], '', 'function Nota() {
  const [stato, azione, inAttesa] = useActionState(salva, null);
  return (
    <form action={azione}>
      <input name="testo" />
      <button disabled={inAttesa}>Salva</button>
    </form>
  );
}'), [
            dMatch(pMatch(), [['action={fn}', L('L’azione del form', 'The form’s action')], ['useActionState', L('Risultato e attesa dell’azione', 'Result and pending state of the action')], ['useFormStatus', L('Il form è in invio?', 'Is the form submitting?')], ['useOptimistic', L('Mostra subito il risultato atteso', 'Shows the expected result at once')], ['name="email"', L('Il campo nel FormData', 'The field in the FormData')]], L('Il kit dei form di React 19.', 'React 19’s form kit.')),
            dOrder(pSort(), ['async function invia(prec, dati) {', '  const testo = dati.get("testo");', '  await salva(testo);', '  return { ok: true };', '}'], L('Leggi il campo, salva, restituisci il nuovo stato.', 'Read the field, save, return the new state.')),
        ]),
        lesson('suspense-use', L('Suspense, use ed error boundary', 'Suspense, use and error boundaries'), [
            P('`<Suspense>` mostra un contenuto di riserva (`fallback`) mentre una parte dell’albero aspetta qualcosa: il codice di un componente caricato con `lazy` o i dati letti con `use`. Il resto della pagina resta visibile.', '`<Suspense>` shows fallback content while part of the tree waits for something: the code of a component loaded with `lazy` or data read with `use`. The rest of the page stays visible.'),
            C('import { Suspense, lazy, use } from "react";

const Grafico = lazy(() => import("./Grafico.jsx"));

function Commenti({ promessa }) {
  const commenti = use(promessa); // sospende finché la promessa non si risolve
  return <ul>{commenti.map(c => <li key={c.id}>{c.testo}</li>)}</ul>;
}

function Pagina({ promessaCommenti }) {
  return (
    <>
      <Suspense fallback={<p>Caricamento grafico…</p>}>
        <Grafico />
      </Suspense>
      <Suspense fallback={<p>Caricamento commenti…</p>}>
        <Commenti promessa={promessaCommenti} />
      </Suspense>
    </>
  );
}', 'jsx'),
            P('`use` legge il valore di una promessa (o di un contesto) e, a differenza degli altri hook, si può chiamare anche dentro un `if`. La promessa va creata **fuori** dal componente che la legge, per esempio in un componente server o in una libreria di dati: se la ricrei a ogni rendering, l’attesa non finisce mai. Se la promessa fallisce, l’errore arriva al più vicino **error boundary**, che mostra un messaggio al posto del pezzo rotto (una classe con `componentDidCatch` o la libreria `react-error-boundary`).', '`use` reads the value of a promise (or a context) and, unlike other hooks, can be called even inside an `if`. The promise must be created **outside** the component reading it, for example in a server component or a data library: if you recreate it on every render, the wait never ends. If the promise fails, the error reaches the nearest **error boundary**, which shows a message in place of the broken piece (a class with `componentDidCatch` or the `react-error-boundary` library).'),
        ], [
            Q('ub1', L('Che cosa mostra `<Suspense>` mentre aspetta?', 'What does `<Suspense>` show while waiting?'), [L('Niente', 'Nothing'), L('Il contenuto di `fallback`', 'The `fallback` content'), L('Un errore', 'An error'), L('La pagina precedente', 'The previous page')], 1, L('Solo per la parte che aspetta.', 'Only for the part that is waiting.')),
            Q('ub2', L('Che cosa ha di speciale `use` rispetto agli altri hook?', 'What is special about `use` compared with other hooks?'), [L('Funziona solo nelle classi', 'It only works in classes'), L('Non restituisce valori', 'It returns no values'), L('Va chiamato solo sul server', 'It must only be called on the server'), L('Si può chiamare dentro un `if`', 'It can be called inside an `if`')], 3, L('Le altre regole degli hook restano valide.', 'The other rules of hooks still apply.')),
            Q('ub3', L('Dove va creata la promessa passata a `use`?', 'Where should the promise passed to `use` be created?'), [L('Dentro il componente che la legge, a ogni rendering', 'Inside the component reading it, on every render'), L('In un file CSS', 'In a CSS file'), L('Fuori dal componente che la legge', 'Outside the component reading it'), L('Non serve una promessa', 'No promise is needed')], 2, L('Una promessa nuova a ogni rendering ricomincia l’attesa da capo.', 'A new promise on every render restarts the wait.')),
        ], task('jsx', L('Avvolgi `<Profilo />` in `<Suspense fallback={<p>Caricamento…</p>}>`.', 'Wrap `<Profilo />` in `<Suspense fallback={<p>Caricamento…</p>}>`.'), 'function App() {
  return <Profilo />;
}', [re('<Suspense\s+fallback\s*=\s*\{\s*<p>[^<]*</p>\s*\}\s*>\s*<Profilo\s*/>\s*</Suspense>', L('Il profilo ha un contenuto di riserva', 'The profile has fallback content'))], '', 'function App() {
  return (
    <Suspense fallback={<p>Caricamento…</p>}>
      <Profilo />
    </Suspense>
  );
}'), [
            dMatch(pMatch(), [['<Suspense>', L('Riserva durante l’attesa', 'Fallback while waiting')], ['lazy()', L('Carica il codice di un componente quando serve', 'Loads a component’s code when needed')], ['use(promessa)', L('Legge il valore di una promessa', 'Reads the value of a promise')], ['Error boundary', L('Mostra un messaggio se qualcosa si rompe', 'Shows a message if something breaks')]], L('Attese ed errori, senza bloccare tutta la pagina.', 'Waits and errors, without blocking the whole page.')),
            dFlash(pFlash(), [[L('`use` può leggere anche un contesto', '`use` can also read a context'), true], [L('Un `<Suspense>` blocca tutta la pagina', 'A `<Suspense>` blocks the whole page'), false], [L('`lazy` divide il codice in pezzi caricati quando servono', '`lazy` splits code into pieces loaded when needed'), true], [L('Creare la promessa a ogni rendering va benissimo con `use`', 'Creating the promise on every render is fine with `use`'), false]], L('La promessa nasce fuori, `use` la legge.', 'The promise is born outside, `use` reads it.')),
        ]),
        lesson('router', L('Più pagine con React Router', 'Multiple pages with React Router'), [
            P('React da solo non gestisce gli indirizzi. **React Router** collega ogni percorso a un componente e cambia pagina senza ricaricare. Dalla versione 7 si importa tutto dal pacchetto `react-router`.', 'React alone does not handle addresses. **React Router** maps each path to a component and changes page without reloading. Since version 7 everything is imported from the `react-router` package.'),
            C('import { BrowserRouter, Routes, Route, Link, useParams, useNavigate } from "react-router";

function Corso() {
  const { id } = useParams();
  const vai = useNavigate();
  return (
    <>
      <h1>Corso {id}</h1>
      <button onClick={() => vai("/")}>Torna alla home</button>
    </>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <nav><Link to="/">Home</Link> <Link to="/corsi/react">React</Link></nav>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/corsi/:id" element={<Corso />} />
        <Route path="*" element={<NonTrovato />} />
      </Routes>
    </BrowserRouter>
  );
}', 'jsx'),
            P('`:id` è un parametro letto con `useParams`, `*` cattura gli indirizzi inesistenti e `useNavigate` cambia pagina dal codice. Se ti servono anche il rendering sul server e il caricamento dei dati per rotta, valuta un framework: React Router in modalità framework oppure Next.js.', '`:id` is a parameter read with `useParams`, `*` catches addresses that do not exist and `useNavigate` changes page from code. If you also need server rendering and per-route data loading, consider a framework: React Router in framework mode or Next.js.'),
        ], [
            Q('ro1', L('Con che cosa crei un collegamento interno senza ricaricare?', 'What makes an internal link without reloading?'), ['<a href>', '<Link to>', '<Route>', '<Routes>'], 1, L('`<a>` farebbe ricaricare tutta la pagina.', '`<a>` would reload the whole page.')),
            Q('ro2', L('Come leggi `:id` nel componente?', 'How do you read `:id` in the component?'), ['useState("id")', 'window.id', 'useParams()', L('`props.id`, sempre', '`props.id`, always')], 2, L('`const { id } = useParams()`.', '`const { id } = useParams()`.')),
            Q('ro3', L('Quale percorso cattura le pagine inesistenti?', 'Which path catches missing pages?'), ['/', '404', ':id', '*'], 3, L('Mettilo come ultima rotta.', 'Put it as the last route.')),
        ], task('jsx', L('Aggiungi la rotta `<Route path="/profilo" element={<Profilo />} />` dentro `<Routes>`.', 'Add the route `<Route path="/profilo" element={<Profilo />} />` inside `<Routes>`.'), '<Routes>
  <Route path="/" element={<Home />} />
</Routes>', [re('<Route\s+path\s*=\s*["\']/profilo["\']\s+element\s*=\s*\{\s*<Profilo\s*/>\s*\}\s*/>', L('La rotta del profilo esiste', 'The profile route exists'))], '', '<Routes>
  <Route path="/" element={<Home />} />
  <Route path="/profilo" element={<Profilo />} />
</Routes>'), [
            dMatch(pMatch(), [['<Link to>', L('Collegamento interno', 'Internal link')], ['<Route>', L('Percorso → componente', 'Path → component')], ['useParams', L('Legge i parametri', 'Reads the parameters')], ['useNavigate', L('Cambia pagina dal codice', 'Changes page from code')], ['path="*"', L('Pagina non trovata', 'Page not found')]], L('I pezzi del router.', 'The parts of the router.')),
            dBug(pBug(), ['import { Link } from "react-router";', 'function Menu() {', '  return <Link href="/corsi">Corsi</Link>;', '}'], 2, '  return <Link to="/corsi">Corsi</Link>;', L('`Link` usa la prop `to`, non `href`.', '`Link` uses the `to` prop, not `href`.')),
        ]),
    ],
];
