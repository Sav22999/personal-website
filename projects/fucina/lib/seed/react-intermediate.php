<?php
/** Intermediate React: useEffect and data fetching, forms, lifting state and context, custom hooks and performance. */

return [
    'id' => 'react-intermediate', 'family' => 'react', 'after' => 'react-base',
    'title' => L('React intermedio', 'Intermediate React'), 'short' => L('React intermedio', 'React Intermediate'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Effetti e dati dal server con `useEffect`, moduli controllati, stato condiviso e Context, hook personalizzati e prestazioni.', 'Effects and server data with `useEffect`, controlled forms, shared state and Context, custom hooks and performance.'),
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
            C('const TemaContext = createContext("chiaro");

function App() {
  const [tema, setTema] = useState("chiaro");
  return (
    <TemaContext.Provider value={tema}>
      <Pagina />
      <button onClick={() => setTema(tema === "chiaro" ? "scuro" : "chiaro")}>Cambia tema</button>
    </TemaContext.Provider>
  );
}

function Pulsante() {
  const tema = useContext(TemaContext);   // nessuna prop da passare
  return <button className={tema}>OK</button>;
}', 'jsx'),
            P('Il Context è perfetto per dati “globali” e poco frequenti: tema, lingua, utente collegato. Per stato complesso e molto aggiornato esistono librerie dedicate (Redux, Zustand) o `useReducer`.', 'Context is perfect for “global”, rarely changing data: theme, language, signed-in user. For complex, frequently updated state there are dedicated libraries (Redux, Zustand) or `useReducer`.'),
        ], [
            Q('x1', L('Che cos’è il prop drilling?', 'What is prop drilling?'), [L('Passare props attraverso molti livelli', 'Passing props through many levels'), L('Un errore di React', 'A React error'), L('Un tipo di hook', 'A type of hook'), L('Un modo di fare CSS', 'A way of doing CSS')], 0, L('Il Context lo evita.', 'Context avoids it.')),
            Q('x2', L('Quale hook legge un Context?', 'Which hook reads a Context?'), ['useContext', 'useState', 'useEffect', 'useRef'], 0, L('Dentro un Provider.', 'Inside a Provider.')),
            Q('x3', L('Per quali dati è adatto il Context?', 'What data suits Context?'), [L('Tema, lingua, utente collegato', 'Theme, language, signed-in user'), L('Ogni carattere digitato', 'Every typed character'), L('I dati di un solo componente', 'Data of a single component'), L('Nessuno', 'None')], 0, L('Globali e poco frequenti.', 'Global and infrequent.')),
        ], task('jsx', L('Crea `LinguaContext` con `createContext("it")` e nel componente `Saluto` leggi la lingua con `useContext(LinguaContext)`.', 'Create `LinguaContext` with `createContext("it")` and in the `Saluto` component read the language with `useContext(LinguaContext)`.'), '', [re('const\s+LinguaContext\s*=\s*createContext\s*\(\s*["\']it["\']\s*\)', L('Il Context', 'The Context')), re('function\s+Saluto[\s\S]*useContext\s*\(\s*LinguaContext\s*\)', L('Saluto lo legge', 'Saluto reads it'))], '', "const LinguaContext = createContext(\"it\");\n\nfunction Saluto() {\n  const lingua = useContext(LinguaContext);\n  return <p>{lingua === \"it\" ? \"Ciao\" : \"Hello\"}</p>;\n}"), [
            dMatch(pMatch(), [['createContext', L('Crea il contesto', 'Creates the context')], ['Provider', L('Fornisce il valore', 'Provides the value')], ['useContext', L('Legge il valore', 'Reads the value')], [L('Sollevare lo stato', 'Lifting state'), L('Spostarlo nel genitore comune', 'Moving it to the common parent')]], L('Condividere lo stato.', 'Sharing state.')),
            dFlash(pFlash(), [[L('Il Context evita di passare props a mano per molti livelli', 'Context avoids passing props by hand through many levels'), true], [L('useContext funziona fuori da qualsiasi Provider con il valore dato', 'useContext outside any Provider gets the given value'), false], [L('Il tema è un buon uso del Context', 'Theme is a good use of Context'), true], [L('Ogni stato va messo nel Context', 'All state belongs in Context'), false]], L('Globale solo quando serve.', 'Global only when needed.')),
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
            P('Regole degli hook: chiamali solo al primo livello del componente (mai dentro `if` o cicli) e solo da componenti o altri hook. Per le prestazioni, **`useMemo`** ricorda un calcolo costoso e **`memo`** evita di ridisegnare un componente con le stesse props: usali solo dove misuri un problema.', 'Rules of hooks: call them only at the top level of the component (never inside `if` or loops) and only from components or other hooks. For performance, **`useMemo`** remembers an expensive calculation and **`memo`** avoids redrawing a component with the same props: use them only where you measure a problem.'),
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
            C('function carrello(stato, azione) {
  switch (azione.type) {
    case "aggiungi": return [...stato, azione.prodotto];
    case "svuota": return [];
    default: return stato;
  }
}
const [voci, dispatch] = useReducer(carrello, []);
dispatch({ type: "aggiungi", prodotto: "Libro" });', 'jsx'),
            P('Con **React Testing Library** si prova il componente come lo usa una persona: lo si disegna con `render`, si trovano gli elementi per ruolo o testo (`getByRole`) e si simula il clic. Si controlla ciò che si **vede**, non lo stato interno.', 'With **React Testing Library** you test the component the way a person uses it: draw it with `render`, find elements by role or text (`getByRole`) and simulate the click. You check what is **seen**, not the internal state.'),
        ], [
            Q('rt1', L('Che cosa riceve un reducer?', 'What does a reducer receive?'), [L('Lo stato attuale e un’azione', 'The current state and an action'), L('Solo le props', 'Only props'), L('Il DOM', 'The DOM'), L('Un URL', 'A URL')], 0, L('E restituisce il nuovo stato.', 'And returns the new state.')),
            Q('rt2', L('Un reducer deve…', 'A reducer must…'), [L('Restituire un nuovo stato senza modificare il vecchio', 'Return a new state without changing the old one'), L('Chiamare il server', 'Call the server'), L('Modificare lo stato in place', 'Change state in place'), L('Usare setTimeout', 'Use setTimeout')], 0, L('È una funzione pura.', 'It is a pure function.')),
            Q('rt3', L('Che cosa controlla un buon test di componente?', 'What does a good component test check?'), [L('Ciò che l’utente vede e fa', 'What the user sees and does'), L('Le variabili interne', 'Internal variables'), L('Il numero di render', 'The number of renders'), L('Il nome dei file', 'File names')], 0, L('Così il test sopravvive ai refactoring.', 'That way the test survives refactors.')),
        ], task('jsx', L('Scrivi `const [n, dispatch] = useReducer(contatore, 0);` e poi `dispatch({ type: "piu" });`.', 'Write `const [n, dispatch] = useReducer(contatore, 0);` and then `dispatch({ type: "piu" });`.'), '', [re('const\s*\[\s*n\s*,\s*dispatch\s*\]\s*=\s*useReducer\(\s*contatore\s*,\s*0\s*\)', L('Usi useReducer', 'You use useReducer')), re('dispatch\(\s*\{\s*type\s*:\s*["\']piu["\']\s*\}\s*\)', L('Invii l’azione', 'You dispatch the action'))], '', "const [n, dispatch] = useReducer(contatore, 0);\ndispatch({ type: \"piu\" });"), [
            dOrder(pSort(), ['render(<Contatore />);', 'const btn = screen.getByRole("button");', 'fireEvent.click(btn);', 'expect(screen.getByText("1")).toBeInTheDocument();'], L('Disegna, trova, agisci, verifica.', 'Render, find, act, assert.')),
            dFlash(pFlash(), [[L('Un reducer è una funzione pura', 'A reducer is a pure function'), true], [L('dispatch modifica direttamente lo stato', 'dispatch changes state directly'), false], [L('getByRole trova gli elementi come li vede l’utente', 'getByRole finds elements as the user sees them'), true], [L('I test devono leggere lo stato interno', 'Tests must read internal state'), false]], L('Reducer e test.', 'Reducers and tests.')),
        ]),
    ],
];
