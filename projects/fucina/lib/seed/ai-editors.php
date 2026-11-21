<?php
/** Coding with AI editors and agents: the landscape, context and rules files, agentic workflows, reviewing and safety, parallel and cloud agents, MCP and skills. */

/** The context window as a bar that fills up: rules file, files you point to, errors, then the chat history. Labels in one language. */
if (!function_exists('fig_aied_context')) {
    function fig_aied_context(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Che cosa vede il modello', 'File di regole', 'sempre', 'File indicati', 'con @ o #', 'Errori e log', 'incollati', 'Cronologia', 'tentativi compresi', 'spazio libero', 'Finestra di contesto', 'piena di tentativi falliti? apri una nuova sessione']
            : ['What the model sees', 'Rules file', 'always', 'Files you point to', 'with @ or #', 'Errors and logs', 'pasted in', 'History', 'failed attempts too', 'free space', 'Context window', 'full of failed attempts? start a new session'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 228" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="16" y="28" font-weight="700">{$t[0]}</text>
<g stroke-width="2"><rect x="16" y="44" width="140" height="52" rx="10" fill="#ECE6FF" stroke="#7048E8"/><rect x="170" y="44" width="140" height="52" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="324" y="44" width="140" height="52" rx="10" fill="#FFE3EE" stroke="#F03E82"/><rect x="478" y="44" width="146" height="52" rx="10" fill="#FFF3EA" stroke="#E8590C"/></g>
<g font-weight="700" font-size="14"><text x="30" y="66" fill="#5A36C9">{$t[1]}</text><text x="184" y="66" fill="#1B66BC">{$t[3]}</text><text x="338" y="66" fill="#C2255C">{$t[5]}</text><text x="492" y="66" fill="#B84508">{$t[7]}</text></g>
<g font-size="13"><text x="30" y="85">{$t[2]}</text><text x="184" y="85">{$t[4]}</text><text x="338" y="85">{$t[6]}</text><text x="492" y="85">{$t[8]}</text></g>
<g stroke="#8A87B0" stroke-width="2" fill="none"><path d="M86 96V112L53 140"/><path d="M240 96V112L163 140"/><path d="M394 96V112L278 140"/><path d="M551 96V112L418 140"/></g>
<text x="16" y="210" font-size="13" font-weight="700">{$t[10]}</text>
<rect x="16" y="144" width="608" height="44" rx="10" fill="#F1F0F8" stroke="#1E1C52" stroke-width="2"/>
<rect x="18" y="146" width="70" height="40" rx="8" fill="#7048E8"/><rect x="88" y="146" width="150" height="40" fill="#2F8CF0"/><rect x="238" y="146" width="80" height="40" fill="#F03E82"/><rect x="318" y="146" width="200" height="40" fill="#E8590C"/>
<path d="M518 146V186" stroke="#1E1C52" stroke-width="2" stroke-dasharray="4 3"/>
<text x="570" y="171" text-anchor="middle" font-size="13">{$t[9]}</text>
<text x="624" y="210" text-anchor="end" font-size="13">{$t[11]}</text>
</svg>
SVG;
    }
}

/** The agentic workflow as a cycle of five numbered steps; the ring's dashes turn slowly. Labels in one language. */
if (!function_exists('fig_aied_cycle')) {
    function fig_aied_cycle(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Piano', 'chiedilo e correggilo', 'Un passo', 'piccolo e chiaro', 'Test', 'finché sono verdi', 'Diff', 'rileggi tutto', 'Commit', 'poi il passo dopo', 'un passo', 'alla volta']
            : ['Plan', 'ask for it, correct it', 'One step', 'small and clear', 'Tests', 'until they pass', 'Diff', 'read it all', 'Commit', 'then the next step', 'one step', 'at a time'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 310" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.ring{animation:turn 6s linear infinite}@keyframes turn{to{stroke-dashoffset:-56}}@media (prefers-reduced-motion:reduce){.ring{animation:none}}</style>
<circle class="ring" cx="320" cy="158" r="112" fill="none" stroke="#7048E8" stroke-width="2" stroke-dasharray="10 4"/>
<text x="320" y="154" text-anchor="middle" font-weight="700" fill="#5A36C9">{$t[10]}</text><text x="320" y="174" text-anchor="middle" font-weight="700" fill="#5A36C9">{$t[11]}</text>
<g stroke-width="2">
<rect x="245" y="20" width="150" height="52" rx="12" fill="#DCEBFF" stroke="#2F8CF0"/>
<rect x="400" y="96" width="150" height="52" rx="12" fill="#ECE6FF" stroke="#7048E8"/>
<rect x="352" y="232" width="150" height="52" rx="12" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="138" y="232" width="150" height="52" rx="12" fill="#FFF3EA" stroke="#E8590C"/>
<rect x="90" y="96" width="150" height="52" rx="12" fill="#FFE3EE" stroke="#F03E82"/>
</g>
<g font-weight="700"><text x="259" y="42" fill="#1B66BC">1  {$t[0]}</text><text x="414" y="118" fill="#5A36C9">2  {$t[2]}</text><text x="366" y="254" fill="#0B7F5C">3  {$t[4]}</text><text x="152" y="254" fill="#B84508">4  {$t[6]}</text><text x="104" y="118" fill="#C2255C">5  {$t[8]}</text></g>
<g font-size="13"><text x="259" y="62">{$t[1]}</text><text x="414" y="138">{$t[3]}</text><text x="366" y="274">{$t[5]}</text><text x="152" y="274">{$t[7]}</text><text x="104" y="138">{$t[9]}</text></g>
</svg>
SVG;
    }
}

/** Parallel agents with git worktrees: one repository, three folders on three branches, each with its own agent. Labels in one language. */
if (!function_exists('fig_aied_worktree')) {
    function fig_aied_worktree(string $lg): string
    {
        $t = $lg === 'it'
            ? ['Un solo repository', 'storia e branch in comune', 'Tu', 'Agente 1', 'Agente 2', 'branch', 'cartelle separate: nessuno pesta i piedi agli altri']
            : ['One repository', 'shared history and branches', 'You', 'Agent 1', 'Agent 2', 'branch', 'separate folders: nobody treads on anyone’s toes'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="16" y="100" width="180" height="80" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="106" y="134" text-anchor="middle" font-weight="700" fill="#5A36C9">{$t[0]}</text><text x="106" y="156" text-anchor="middle" font-size="13">{$t[1]}</text>
<g stroke="#7048E8" stroke-width="2" fill="none"><path d="M196 125C230 125 230 50 262 50"/><path d="M196 140H262"/><path d="M196 155C230 155 230 230 262 230"/></g>
<g stroke-width="2">
<path d="M262 30h46l8 8h180a10 10 0 0 1 10 10v26a10 10 0 0 1-10 10H272a10 10 0 0 1-10-10z" fill="#DCEBFF" stroke="#2F8CF0"/>
<path d="M262 120h46l8 8h180a10 10 0 0 1 10 10v26a10 10 0 0 1-10 10H272a10 10 0 0 1-10-10z" fill="#FFF3EA" stroke="#E8590C"/>
<path d="M262 210h46l8 8h180a10 10 0 0 1 10 10v26a10 10 0 0 1-10 10H272a10 10 0 0 1-10-10z" fill="#D5F6EA" stroke="#0B7F5C"/>
</g>
<g font-weight="700" font-size="14"><text x="276" y="62">shop/</text><text x="276" y="152">../shop-fix-iva/</text><text x="276" y="242">../shop-docs/</text></g>
<g font-size="13"><text x="414" y="62">{$t[5]} main</text><text x="414" y="152">{$t[5]} fix-iva</text><text x="414" y="242">{$t[5]} docs</text></g>
<g stroke="#8A87B0" stroke-width="2"><path d="M506 57H530"/><path d="M506 147H530"/><path d="M506 237H530"/></g>
<g stroke-width="2"><rect x="530" y="38" width="94" height="38" rx="19" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="530" y="128" width="94" height="38" rx="19" fill="#FFE8D6" stroke="#E8590C"/><rect x="530" y="218" width="94" height="38" rx="19" fill="#D5F6EA" stroke="#0B7F5C"/></g>
<g font-weight="700" font-size="14" text-anchor="middle"><text x="577" y="62" fill="#1B66BC">{$t[2]}</text><text x="577" y="152" fill="#B84508">{$t[3]}</text><text x="577" y="242" fill="#0B7F5C">{$t[4]}</text></g>
<text x="320" y="292" text-anchor="middle" font-size="13">{$t[6]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'ai-editors', 'family' => 'assistants', 'after' => null,
    'title' => L('Programmare con editor e agenti AI', 'Coding with AI Editors and Agents'), 'short' => L('Editor AI', 'AI Editors'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il modo di lavorare con Cursor, Windsurf, VS Code con Copilot e gli agenti da terminale: contesto, file di regole come AGENTS.md, flussi agentici, revisione e sicurezza, agenti in parallelo e nel cloud, MCP e skill.', 'How to work with Cursor, Windsurf, VS Code with Copilot and terminal agents: context, rules files like AGENTS.md, agentic workflows, review and safety, parallel and cloud agents, MCP and skills.'),
    'lessons' => [
        lesson('landscape', L('Il panorama', 'The landscape'), [
            P('Gli strumenti per programmare con l’AI si dividono in due famiglie. Gli **editor con AI** (Cursor, Windsurf, VS Code con Copilot, JetBrains con il suo assistente, Zed) integrano completamenti, chat e agenti nell’editor. Gli **agenti da terminale** (Claude Code, Codex CLI, Gemini CLI, Mistral Vibe) lavorano nella riga di comando sul tuo progetto.', 'AI coding tools fall into two families. **AI editors** (Cursor, Windsurf, VS Code with Copilot, JetBrains with its assistant, Zed) build completions, chat and agents into the editor. **Terminal agents** (Claude Code, Codex CLI, Gemini CLI, Mistral Vibe) work from the command line on your project.'),
            P('Le funzioni si somigliano: **completamento** mentre scrivi, **chat** con il codice come contesto, **modifiche su più file** e **modalità agente** che esegue comandi e test. Gli strumenti cambiano in fretta; le abitudini di lavoro che impari qui restano valide.', 'Features are alike: **completion** as you type, **chat** with code as context, **multi-file edits** and **agent mode** that runs commands and tests. Tools change fast; the work habits you learn here stay valid.'),
        ], [
            Q('l1', L('Quale di questi è un agente da terminale?', 'Which of these is a terminal agent?'), ['Claude Code', 'Excel', 'Photoshop', 'Outlook'], 0, L('Anche Codex CLI, Gemini CLI, Mistral Vibe.', 'Also Codex CLI, Gemini CLI, Mistral Vibe.')),
            Q('l2', L('Che cosa fa la modalità agente?', 'What does agent mode do?'), [L('Modifica file ed esegue comandi e test', 'Edits files and runs commands and tests'), L('Solo colora il codice', 'Only colours code'), L('Traduce l’interfaccia', 'Translates the interface'), L('Niente', 'Nothing')], 0, L('Fino a completare il compito.', 'Until the task is done.')),
            Q('l3', L('Che cosa resta valido quando gli strumenti cambiano?', 'What stays valid when tools change?'), [L('Le abitudini di lavoro', 'Work habits'), L('I nomi dei menu', 'Menu names'), L('Le scorciatoie', 'Shortcuts'), L('Niente', 'Nothing')], 0, L('Contesto, revisione, test.', 'Context, review, tests.')),
        ], task('text', L('Classifica, una per riga come `strumento: tipo`: `Cursor`, `Claude Code`. Tipi: editor, terminale.', 'Classify, one per line as `tool: type`: `Cursor`, `Claude Code`. Types: editor, terminal.'), '', [re('Cursor\s*:\s*editor', L('Cursor è un editor', 'Cursor is an editor')), re('Claude Code\s*:\s*(terminale|terminal)', L('Claude Code lavora nel terminale', 'Claude Code works in the terminal'))], '', "Cursor: editor\nClaude Code: terminale"), [
            dMatch(pMatch(), [[L('Completamento', 'Completion'), L('Mentre scrivi', 'As you type')], ['Chat', L('Domande con contesto', 'Questions with context')], [L('Modifica multi-file', 'Multi-file edit'), L('Più file insieme', 'Several files at once')], [L('Agente', 'Agent'), L('Comandi e test fino alla fine', 'Commands and tests to the end')]], L('Le funzioni comuni.', 'Common features.')),
            dFlash(pFlash(), [[L('Gli agenti da terminale lavorano sul progetto locale', 'Terminal agents work on the local project'), true], [L('Gli editor AI non permettono di scrivere codice a mano', 'AI editors do not allow typing code by hand'), false], [L('Gli strumenti cambiano spesso', 'Tools change often'), true], [L('Un agente non può eseguire i test', 'An agent cannot run tests'), false]], L('Due famiglie, stesse idee.', 'Two families, same ideas.')),
        ]),
        lesson('context', L('Contesto e file di regole', 'Context and rules files'), [
            P('La qualità delle risposte dipende dal **contesto**: quali file vede il modello, che cosa sa del progetto. Indica i file rilevanti (con `@` o `#` a seconda dello strumento), incolla errori e log, e descrivi il risultato atteso.', 'Answer quality depends on **context**: which files the model sees, what it knows about the project. Point to the relevant files (with `@` or `#` depending on the tool), paste errors and logs, and describe the expected result.'),
            Fig(L(fig_aied_context('it'), fig_aied_context('en')),
                L('Quattro riquadri (file di regole, file indicati, errori e log, cronologia) scendono in una barra orizzontale, la finestra di contesto, riempita da segmenti colorati; a destra resta poco spazio libero. Sotto: se è piena di tentativi falliti, apri una nuova sessione.', 'Four boxes (rules file, files you point to, errors and logs, history) drop into a horizontal bar, the context window, filled with coloured segments; little free space is left on the right. Below: if it is full of failed attempts, start a new session.'),
                L('Il contesto è limitato: riempilo con ciò che serve, non con i tentativi andati male.', 'Context is limited: fill it with what matters, not with attempts that went wrong.')),
            P('I **file di regole** danno istruzioni stabili per tutto il progetto: comandi di build e test, stile, cartelle da non toccare. Ogni strumento ha il suo (`CLAUDE.md`, `.cursor/rules`, `.github/copilot-instructions.md`); **`AGENTS.md`** è una convenzione aperta letta da molti agenti diversi.', '**Rules files** give stable instructions for the whole project: build and test commands, style, folders not to touch. Each tool has its own (`CLAUDE.md`, `.cursor/rules`, `.github/copilot-instructions.md`); **`AGENTS.md`** is an open convention read by many different agents.'),
            C('# AGENTS.md
## Comandi
- Installa: `npm ci`   - Test: `npm test`   - Lint: `npm run lint`
## Regole
- TypeScript strict; niente nuove dipendenze senza chiedere.
- Non modificare `src/generated/`: si rigenera con `npm run codegen`.
- Ogni modifica deve avere un test.', 'markdown'),
        ], [
            Q('c1', L('Che cos’è AGENTS.md?', 'What is AGENTS.md?'), [L('Un file di regole letto da molti agenti', 'A rules file read by many agents'), L('Un virus', 'A virus'), L('Un linguaggio', 'A language'), L('Il log degli errori', 'The error log')], 0, L('Una convenzione aperta.', 'An open convention.')),
            Q('c2', L('Che cosa mettere in un file di regole?', 'What goes in a rules file?'), [L('Comandi di test, stile, cartelle da non toccare', 'Test commands, style, folders not to touch'), L('Le chiavi API', 'API keys'), L('Le password', 'Passwords'), L('Niente', 'Nothing')], 0, L('Istruzioni stabili.', 'Stable instructions.')),
            Q('c3', L('Come migliori il contesto di una richiesta?', 'How do you improve a request’s context?'), [L('Indicando i file e incollando gli errori', 'Pointing to files and pasting errors'), L('Scrivendo meno', 'Writing less'), L('Chiudendo i file', 'Closing files'), L('Non si può', 'You cannot')], 0, L('Il modello non indovina.', 'The model cannot guess.')),
        ], task('markdown', L('Scrivi un `AGENTS.md` con una sezione `## Comandi` (con il comando dei test) e una `## Regole` con almeno due regole con `-`.', 'Write an `AGENTS.md` with a `## Commands` section (with the test command) and a `## Rules` section with at least two rules using `-`.'), "# AGENTS.md\n", [re('^##\s*(Comandi|Commands)', L('Sezione comandi', 'Commands section')), re('test', L('Il comando dei test', 'The test command')), re('^##\s*(Regole|Rules)\s*\n-\s*\S.*\n-\s*\S', L('Due regole', 'Two rules'))], '', "# AGENTS.md\n## Comandi\n- Test: `pytest -q`\n## Regole\n- Python 3.12 con type hint.\n- Non toccare la cartella migrations/ senza chiedere."), [
            dMatch(pMatch(), [['AGENTS.md', L('Convenzione aperta', 'Open convention')], ['CLAUDE.md', 'Claude Code'], ['.cursor/rules', 'Cursor'], ['copilot-instructions.md', 'GitHub Copilot']], L('Un file di regole per ogni strumento.', 'A rules file per tool.')),
            dFlash(pFlash(), [[L('I file di regole valgono per tutto il progetto', 'Rules files apply to the whole project'), true], [L('Le chiavi API vanno nel file di regole', 'API keys go in the rules file'), false], [L('Incollare l’errore aiuta il modello', 'Pasting the error helps the model'), true], [L('Il modello conosce già il tuo progetto', 'The model already knows your project'), false]], L('Contesto è potere.', 'Context is power.')),
        ]),
        lesson('workflow', L('Flussi agentici', 'Agentic workflows'), [
            P('Un flusso che funziona: **1)** chiedi prima un **piano** e correggilo, **2)** fai implementare un passo alla volta, **3)** fai **eseguire i test** e correggere finché passano, **4)** rivedi il **diff**, **5)** commit. Compiti piccoli e ben definiti riescono molto meglio di richieste enormi.', 'A workflow that works: **1)** ask for a **plan** first and correct it, **2)** have it implement one step at a time, **3)** have it **run the tests** and fix until they pass, **4)** review the **diff**, **5)** commit. Small, well-defined tasks succeed far better than huge requests.'),
            Fig(L(fig_aied_cycle('it'), fig_aied_cycle('en')),
                L('Cinque riquadri numerati disposti su un cerchio: 1 piano, 2 un passo, 3 test finché sono verdi, 4 rileggi il diff, 5 commit e poi il passo dopo. Al centro: un passo alla volta.', 'Five numbered boxes arranged on a circle: 1 plan, 2 one step, 3 tests until they pass, 4 read the diff, 5 commit and then the next step. In the middle: one step at a time.'),
                L('Il ciclo è corto apposta: ogni giro finisce con un commit che sai spiegare.', 'The cycle is short on purpose: every lap ends with a commit you can explain.')),
            P('Altre tecniche utili: scrivere prima i **test** e chiedere all’agente di farli passare; dare **esempi** di codice esistente da imitare; ricominciare una sessione pulita quando il contesto si è riempito di tentativi falliti.', 'Other useful techniques: write the **tests** first and ask the agent to make them pass; give **examples** of existing code to imitate; start a fresh session when the context has filled up with failed attempts.'),
        ], [
            Q('w1', L('Che cosa chiedere prima di far scrivere codice?', 'What should you ask for before having code written?'), [L('Un piano da correggere', 'A plan to correct'), L('Il prezzo', 'The price'), L('Niente', 'Nothing'), L('Una poesia', 'A poem')], 0, L('Correggere un piano costa poco.', 'Correcting a plan is cheap.')),
            Q('w2', L('Perché scrivere i test prima?', 'Why write the tests first?'), [L('L’agente ha un obiettivo verificabile', 'The agent has a verifiable goal'), L('Per perdere tempo', 'To waste time'), L('Non serve', 'It is not needed'), L('Per confondere l’agente', 'To confuse the agent')], 0, L('Passano o non passano.', 'They pass or fail.')),
            Q('w3', L('Quando ricominciare una sessione pulita?', 'When should you start a fresh session?'), [L('Quando il contesto è pieno di tentativi falliti', 'When context is full of failed attempts'), L('A ogni riga', 'On every line'), L('Mai', 'Never'), L('Solo il lunedì', 'Only on Mondays')], 0, L('Il rumore confonde il modello.', 'Noise confuses the model.')),
        ], task('text', L('Metti in ordine i passi del flusso, uno per riga numerata da `1.` a `5.`: rivedi il diff, chiedi un piano, fai commit, esegui i test, implementa un passo.', 'Order the workflow steps, one per line numbered `1.` to `5.`: review the diff, ask for a plan, commit, run the tests, implement a step.'), '', [re('^1\.\s*(chiedi|ask)', L('Prima il piano', 'Plan first')), re('^2\.\s*(implementa|implement)', L('Poi un passo', 'Then a step')), re('^3\.\s*(esegui|run)', L('Poi i test', 'Then the tests')), re('^4\.\s*(rivedi|review)', L('Poi il diff', 'Then the diff')), re('^5\.\s*(fai commit|commit)', L('Infine il commit', 'Finally commit'))], '', "1. chiedi un piano\n2. implementa un passo\n3. esegui i test\n4. rivedi il diff\n5. fai commit"), [
            dOrder(pSort(), [L('Chiedi un piano', 'Ask for a plan'), L('Implementa un passo', 'Implement a step'), L('Esegui i test', 'Run the tests'), L('Rivedi il diff', 'Review the diff'), L('Fai commit', 'Commit')], L('Il flusso agentico.', 'The agentic workflow.')),
            dFlash(pFlash(), [[L('Compiti piccoli riescono meglio', 'Small tasks succeed better'), true], [L('Conviene chiedere tutta l’app in un messaggio', 'Asking for the whole app in one message is best'), false], [L('Mostrare codice esistente da imitare aiuta', 'Showing existing code to imitate helps'), true], [L('Il diff non va mai letto', 'The diff never needs reading'), false]], L('Metodo prima della velocità.', 'Method before speed.')),
        ]),
        lesson('safety', L('Revisione e sicurezza', 'Review and safety'), [
            P('Il codice scritto da un agente è **tuo** quando fai commit. Rivedilo come una pull request di un collega: logica, casi limite, gestione degli errori, test che controllano davvero qualcosa (non test vuoti fatti per passare).', 'Code written by an agent is **yours** when you commit. Review it like a colleague’s pull request: logic, edge cases, error handling, tests that really check something (not empty tests made to pass).'),
            P('Sicurezza: non incollare **segreti** nei prompt e tieni i file `.env` fuori dal contesto; approva i comandi prima che l’agente li esegua (soprattutto cancellazioni, `git push`, installazioni); attenzione ai **pacchetti inventati**: verifica che una dipendenza suggerita esista davvero e sia quella giusta; tieni conto della **prompt injection** da file, issue o pagine web che l’agente legge.', 'Safety: do not paste **secrets** into prompts and keep `.env` files out of context; approve commands before the agent runs them (especially deletions, `git push`, installs); watch out for **made-up packages**: check that a suggested dependency really exists and is the right one; account for **prompt injection** from files, issues or web pages the agent reads.'),
        ], [
            Q('s1', L('Di chi è il codice scritto da un agente dopo il commit?', 'Whose is agent-written code after commit?'), [L('Tuo', 'Yours'), L('Dell’agente', 'The agent’s'), L('Di nessuno', 'Nobody’s'), L('Del fornitore', 'The provider’s')], 0, L('Rispondi tu di quello che entra.', 'You answer for what lands.')),
            Q('s2', L('Perché verificare le dipendenze suggerite?', 'Why verify suggested dependencies?'), [L('Possono essere inventate o malevole', 'They may be made up or malicious'), L('Per abitudine', 'Out of habit'), L('Non serve', 'It is not needed'), L('Per velocità', 'For speed')], 0, L('Nomi plausibili ma falsi.', 'Plausible but fake names.')),
            Q('s3', L('Quali comandi approvare con più attenzione?', 'Which commands deserve most care before approving?'), [L('Cancellazioni, git push, installazioni', 'Deletions, git push, installs'), L('ls', 'ls'), L('pwd', 'pwd'), L('echo', 'echo')], 0, L('Quelli che cambiano le cose.', 'Those that change things.')),
        ], task('text', L('Scrivi quattro regole di sicurezza per lavorare con un agente di programmazione, una per riga con `-`, tra cui una sui **segreti** e una sulle **dipendenze**.', 'Write four safety rules for working with a coding agent, one per line with `-`, including one about **secrets** and one about **dependencies**.'), "- \n- \n- \n- ", [re('^-\s*\S.*\n-\s*\S.*\n-\s*\S.*\n-\s*\S', L('Quattro regole', 'Four rules')), re('segret|secret|\.env|chiav|key|password', L('Una sui segreti', 'One about secrets')), re('dipendenz|dependenc|pacchett|package|librer|librar', L('Una sulle dipendenze', 'One about dependencies'))], '', "- mai segreti o file .env nei prompt\n- verificare che ogni dipendenza suggerita esista e sia affidabile\n- approvare a mano cancellazioni e git push\n- rivedere il diff e i test prima del commit"), [
            dMatch(pMatch(), [[L('Revisione', 'Review'), L('Come una pull request', 'Like a pull request')], [L('Segreti', 'Secrets'), L('Mai nei prompt', 'Never in prompts')], [L('Pacchetti inventati', 'Made-up packages'), L('Verificare che esistano', 'Check they exist')], [L('Prompt injection', 'Prompt injection'), L('Istruzioni nascoste nei file letti', 'Instructions hidden in files read')]], L('Fiducia con verifica.', 'Trust with verification.')),
            dFlash(pFlash(), [[L('Un test vuoto che passa non dimostra niente', 'An empty passing test proves nothing'), true], [L('Si possono approvare tutti i comandi senza leggerli', 'All commands can be approved unread'), false], [L('Un agente può essere ingannato da una issue', 'An agent can be tricked by an issue'), true], [L('Le dipendenze suggerite esistono sempre', 'Suggested dependencies always exist'), false]], L('Sicurezza prima di tutto.', 'Safety first.')),
        ]),
        lesson('feature', L('Una funzione dall’idea al merge', 'A feature from idea to merge'), [
            P('Con un agente di programmazione il lavoro va **spezzato**: prima un piano (cosa cambia, quali file, come si prova), poi passi piccoli, ciascuno verificato con i test e salvato in un commit. Un compito enorme in un solo prompt produce modifiche difficili da rivedere.', 'With a coding agent the work must be **broken down**: first a plan (what changes, which files, how it is tested), then small steps, each checked with tests and saved in a commit. A huge task in one prompt produces changes that are hard to review.'),
            C('1. Chiedi un piano e correggilo
2. Fai scrivere prima i test del comportamento atteso
3. Un passo alla volta: modifica, test verdi, commit
4. Rileggi il diff completo
5. Apri la pull request con una descrizione chiara', 'text'),
            P('Tieni il controllo: guarda ogni diff, non dare permessi più ampi del necessario, e se l’agente gira in tondo **fermalo** e riformula il compito con più contesto.', 'Stay in control: look at every diff, do not grant wider permissions than needed, and if the agent goes in circles **stop it** and rephrase the task with more context.'),
        ], [
            Q('fe1', L('Perché spezzare il lavoro in passi piccoli?', 'Why break work into small steps?'), [L('Ogni passo si verifica e si rivede facilmente', 'Each step is easy to check and review'), L('Per usare più token', 'To use more tokens'), L('Perché l’agente non sa fare di più', 'Because the agent cannot do more'), L('Non serve', 'It is not needed')], 0, L('Diff piccoli, revisioni vere.', 'Small diffs, real reviews.')),
            Q('fe2', L('Che cosa chiedi prima di tutto?', 'What do you ask for first?'), [L('Un piano da correggere', 'A plan to correct'), L('Il codice completo', 'The full code'), L('Il merge', 'The merge'), L('Il deploy', 'The deploy')], 0, L('Il piano evita sorprese.', 'The plan avoids surprises.')),
            Q('fe3', L('L’agente gira in tondo: che fai?', 'The agent goes in circles: what do you do?'), [L('Lo fermi e dai più contesto', 'Stop it and give more context'), L('Aspetti per ore', 'Wait for hours'), L('Gli dai più permessi', 'Give it more permissions'), L('Accetti tutto', 'Accept everything')], 0, L('Riformulare aiuta.', 'Rephrasing helps.')),
        ], task('text', L('Scrivi i passi `piano`, `test`, `codice`, `commit` nell’ordine giusto, separati da `->`.', 'Write the steps `piano`, `test`, `codice`, `commit` in the right order, separated by `->`.'), '', [re('piano\s*->\s*test\s*->\s*codice\s*->\s*commit', L('L’ordine è giusto', 'The order is right'))], '', 'piano -> test -> codice -> commit'), [
            dOrder(pSort(), [L('Piano approvato', 'Approved plan'), L('Test del comportamento', 'Behaviour tests'), L('Modifica piccola', 'Small change'), L('Test verdi e commit', 'Green tests and commit'), L('Pull request', 'Pull request')], L('Dall’idea al merge.', 'From idea to merge.')),
            dFlash(pFlash(), [[L('Ogni diff va letto', 'Every diff should be read'), true], [L('Più permessi rendono l’agente più sicuro', 'More permissions make the agent safer'), false], [L('Un piano iniziale riduce le sorprese', 'An initial plan reduces surprises'), true], [L('Un unico prompt enorme è il metodo migliore', 'One huge prompt is the best method'), false]], L('Lavorare con un agente.', 'Working with an agent.')),
        ]),
        lesson('parallel', L('Agenti in parallelo e nel cloud', 'Parallel and cloud agents'), [
            P('Molti strumenti permettono di far lavorare **più agenti insieme** su compiti diversi. Per non pestarsi i piedi, ogni agente deve avere la sua copia del progetto: di solito un **git worktree**, cioè una seconda cartella collegata allo stesso repository ma su un altro branch. Così un agente corregge un bug mentre un altro scrive la documentazione.', 'Many tools let **several agents work at once** on different tasks. So they do not get in each other’s way, each agent needs its own copy of the project: usually a **git worktree**, a second folder linked to the same repository but on another branch. That way one agent fixes a bug while another writes the documentation.'),
            Fig(L(fig_aied_worktree('it'), fig_aied_worktree('en')),
                L('A sinistra un solo repository, collegato a tre cartelle: shop sul branch main dove lavori tu, ../shop-fix-iva sul branch fix-iva con l’agente 1, ../shop-docs sul branch docs con l’agente 2.', 'On the left, a single repository linked to three folders: shop on the main branch where you work, ../shop-fix-iva on the fix-iva branch with agent 1, ../shop-docs on the docs branch with agent 2.'),
                L('Un worktree per agente: stessa storia, file separati.', 'One worktree per agent: same history, separate files.')),
            C('git worktree add ../shop-fix-iva -b fix-iva
cd ../shop-fix-iva
# qui lavora il secondo agente; a fine lavoro:
git worktree remove ../shop-fix-iva', 'bash'),
            P('Gli **agenti nel cloud** (o in background) lavorano su una macchina remota: assegni un compito, chiudi il portatile e ritrovi una **pull request** da rivedere. Funzionano bene per compiti chiari e verificabili con i test. Più agenti producono più codice da rivedere: avviane solo quanti riesci a controllare davvero, e dai a ciascuno un compito indipendente.', '**Cloud** (or background) agents work on a remote machine: you assign a task, close the laptop and find a **pull request** to review. They work well for clear tasks that tests can verify. More agents produce more code to review: start only as many as you can really check, and give each an independent task.'),
        ], [
            Q('pa1', L('Perché ogni agente in parallelo dovrebbe avere il suo worktree?', 'Why should each parallel agent have its own worktree?'), [L('Per usare meno disco', 'To use less disk space'), L('Perché le modifiche non si mescolino', 'So that changes do not get mixed up'), L('Perché git lo impone sempre', 'Because git always requires it'), L('Per avere più token gratis', 'To get more free tokens')], 1, L('Una cartella e un branch per agente.', 'One folder and one branch per agent.')),
            Q('pa2', L('Che cosa consegna di solito un agente nel cloud?', 'What does a cloud agent usually deliver?'), [L('Un merge diretto su main', 'A direct merge to main'), L('Un’email con il codice incollato', 'An email with pasted code'), L('Un file ZIP da scaricare', 'A ZIP file to download'), L('Una pull request da rivedere', 'A pull request to review')], 3, L('La revisione resta tua.', 'Review stays yours.')),
            Q('pa3', L('Quanti agenti conviene avviare insieme?', 'How many agents should you start at once?'), [L('Quanti ne riesci a rivedere davvero', 'As many as you can really review'), L('Sempre il massimo possibile', 'Always as many as possible'), L('Mai più di uno al mese', 'Never more than one a month'), L('Uno per ogni file del progetto', 'One per project file')], 0, L('Il collo di bottiglia è la revisione.', 'Review is the bottleneck.')),
        ], task('bash', L('Crea un worktree nella cartella `../shop-test` su un nuovo branch `nuovi-test`, poi entra nella cartella.', 'Create a worktree in the `../shop-test` folder on a new branch `nuovi-test`, then enter the folder.'), '', [re('git\s+worktree\s+add\s+(\S+\s+)*\.\./shop-test', L('Crei il worktree', 'You create the worktree')), re('-b\s+nuovi-test', L('Sul nuovo branch', 'On the new branch')), re('cd\s+\.\./shop-test', L('Entri nella cartella', 'You enter the folder'))], '', "git worktree add ../shop-test -b nuovi-test\ncd ../shop-test"), [
            dOrder(pSort(), ['git worktree add ../shop-fix -b fix', 'cd ../shop-fix', L('# l’agente lavora e fa commit', '# the agent works and commits'), 'git push -u origin fix', 'git worktree remove ../shop-fix'], L('Il giro di un worktree.', 'A worktree’s round trip.')),
            dFlash(pFlash(), [[L('Un worktree è una cartella collegata allo stesso repository', 'A worktree is a folder linked to the same repository'), true], [L('Due agenti possono modificare la stessa cartella senza problemi', 'Two agents can edit the same folder without trouble'), false], [L('Gli agenti nel cloud lavorano anche a portatile chiuso', 'Cloud agents keep working with the laptop closed'), true], [L('Più agenti significa meno codice da rivedere', 'More agents means less code to review'), false]], L('Lavoro in parallelo.', 'Parallel work.')),
        ]),
        lesson('mcp', L('Collegare strumenti: MCP e skill', 'Connecting tools: MCP and skills'), [
            P('Il **Model Context Protocol** (MCP) permette all’editor o all’agente di usare strumenti esterni: leggere le issue, interrogare un database di sviluppo, aprire un browser per provare la pagina, consultare la documentazione aggiornata di una libreria. Ogni strumento arriva da un **server MCP**, configurato in un file del progetto o dell’utente.', 'The **Model Context Protocol** (MCP) lets the editor or agent use external tools: reading issues, querying a development database, opening a browser to test the page, looking up a library’s current documentation. Each tool comes from an **MCP server**, configured in a project or user file.'),
            C('{
  "mcpServers": {
    "docs": {
      "command": "npx",
      "args": ["-y", "pacchetto-del-server-mcp"]
    }
  }
}', 'json'),
            P('Le **skill** (o comandi personalizzati) raccolgono istruzioni ricorrenti: come si prepara una release, come si scrive una migrazione. Regole di sicurezza: aggiungi solo server e skill di fonti affidabili, dai il minimo dei permessi (un database di prova, non quello di produzione), non mettere chiavi nel file di configurazione versionato e ricorda che ciò che uno strumento restituisce può contenere **prompt injection**.', '**Skills** (or custom commands) collect recurring instructions: how to prepare a release, how to write a migration. Safety rules: add only servers and skills from trusted sources, grant the minimum permissions (a test database, not production), do not put keys in the versioned configuration file, and remember that what a tool returns can contain **prompt injection**.'),
        ], [
            Q('mc1', L('A che cosa serve un server MCP?', 'What is an MCP server for?'), [L('A compilare il progetto più in fretta', 'Building the project faster'), L('A colorare il codice', 'Colouring the code'), L('A dare all’agente uno strumento esterno', 'Giving the agent an external tool'), L('A sostituire git', 'Replacing git')], 2, L('Issue, database, browser, documentazione…', 'Issues, databases, browsers, documentation…')),
            Q('mc2', L('A quale database conviene collegare un agente?', 'Which database should an agent be connected to?'), [L('A un database di prova', 'A test database'), L('A quello di produzione, per avere dati veri', 'Production, to have real data'), L('A tutti, così sceglie lui', 'All of them, so it can choose'), L('A nessuno, MCP non lo permette', 'None, MCP does not allow it')], 0, L('Minimo privilegio.', 'Least privilege.')),
            Q('mc3', L('Dove non vanno messe le chiavi di un server MCP?', 'Where should an MCP server’s keys not go?'), [L('In una variabile d’ambiente', 'In an environment variable'), L('In un gestore di segreti', 'In a secrets manager'), L('Nel file `.env` escluso da git', 'In the `.env` file excluded from git'), L('Nel file di configurazione versionato', 'In the versioned configuration file')], 3, L('Chi vede il repository vedrebbe la chiave.', 'Anyone seeing the repository would see the key.')),
        ], task('text', L('Scrivi tre regole, una per riga con `-`, per usare un server MCP in sicurezza: una sulla **fonte**, una sui **permessi** e una sulle **chiavi**.', 'Write three rules, one per line with `-`, for using an MCP server safely: one on the **source**, one on **permissions** and one on **keys**.'), "- \n- \n- ", [re('^-\s*\S.*\n-\s*\S.*\n-\s*\S', L('Tre regole', 'Three rules')), re('font[ei]|affidabil|fidat|ufficial|source|trusted|official', L('Una sulla fonte', 'One on the source')), re('permess|privileg|prova|test|sola lettura|read.only|permission', L('Una sui permessi', 'One on permissions')), re('chiav|key|segret|secret|token|\.env', L('Una sulle chiavi', 'One on keys'))], '', "- installare solo server MCP da fonti ufficiali o fidate\n- collegare un database di prova in sola lettura, mai quello di produzione\n- tenere le chiavi in variabili d’ambiente, non nel file versionato"), [
            dMatch(pMatch(), [['MCP', L('Protocollo per gli strumenti', 'Protocol for tools')], [L('Server MCP', 'MCP server'), L('Fornisce uno strumento', 'Provides a tool')], ['Skill', L('Istruzioni ricorrenti', 'Recurring instructions')], ['Prompt injection', L('Istruzioni nascoste nei risultati', 'Instructions hidden in results')]], L('Strumenti per l’agente.', 'Tools for the agent.')),
            dFlash(pFlash(), [[L('Un server MCP può dare accesso a un database', 'An MCP server can give access to a database'), true], [L('Conviene installare qualsiasi server trovato online', 'Installing any server found online is wise'), false], [L('Il risultato di uno strumento può contenere istruzioni ostili', 'A tool’s result can contain hostile instructions'), true], [L('Le chiavi vanno nel file di configurazione su git', 'Keys belong in the configuration file on git'), false]], L('Collegare con giudizio.', 'Connect with judgement.')),
        ]),
    ],
];
