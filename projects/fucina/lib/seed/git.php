<?php
/** Git and GitHub: repositories and commits, branches and merges, remotes and pull requests, fixing mistakes, history,
 *  setup and authentication, reading history, reset and reflog, forks and syncing, GitHub Actions. */

/** The three areas of Git, with a file that travels from the working directory to the staging area and into a commit. */
if (!function_exists('fig_git_areas')) {
    function fig_git_areas(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.trip{animation:trip 9s ease-in-out infinite}@keyframes trip{0%,15%{transform:translate(0,0);opacity:1}30%,48%{transform:translate(224px,0);opacity:1}63%,80%{transform:translate(448px,0);opacity:1}90%{transform:translate(448px,0);opacity:0}91%{transform:translate(0,0);opacity:0}100%{transform:translate(0,0);opacity:1}}@media (prefers-reduced-motion:reduce){.trip{animation:none}}</style>
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="16" width="176" height="140" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="240" y="16" width="176" height="140" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="464" y="16" width="160" height="140" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="104" y="42" text-anchor="middle" font-weight="700" fill="#B84508">{$t[0]}</text>
<text x="328" y="42" text-anchor="middle" font-weight="700" fill="#1B66BC">{$t[1]}</text>
<text x="544" y="42" text-anchor="middle" font-weight="700" fill="#0B7F5C">{$t[2]}</text>
<text x="104" y="62" text-anchor="middle" font-size="13">{$t[3]}</text>
<text x="328" y="62" text-anchor="middle" font-size="13">{$t[4]}</text>
<text x="544" y="62" text-anchor="middle" font-size="13">{$t[5]}</text>
<rect x="48" y="116" width="112" height="28" rx="8" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="104" y="135" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">style.css</text>
<g stroke="#0B7F5C" stroke-width="2" fill="#FFFFFF"><line x1="500" y1="128" x2="588" y2="128"/><circle cx="500" cy="128" r="10"/><circle cx="544" cy="128" r="10"/><circle cx="588" cy="128" r="10" fill="#0B7F5C"/></g>
<g class="trip"><rect x="48" y="78" width="112" height="28" rx="8" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/>
<text x="104" y="97" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">index.html</text></g>
<g fill="none" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"><path d="M104,156 V186 Q104,196 114,196 H318 Q328,196 328,186 V164"/><path d="M328,156 V186 Q328,196 338,196 H534 Q544,196 544,186 V164"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700" text-anchor="middle">
<rect x="176" y="183" width="84" height="26" rx="8" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/><text x="218" y="201">git add</text>
<rect x="388" y="183" width="104" height="26" rx="8" fill="#FFFFFF" stroke="#1E1C52" stroke-width="2"/><text x="440" y="201">git commit</text></g>
</svg>
SVG;
    }
}

/** A feature branch that leaves main and comes back with a merge commit; the branch commits appear and the merge line is drawn. */
if (!function_exists('fig_git_branch')) {
    function fig_git_branch(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 220" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.f1{animation:pop 8s infinite}.f2{animation:pop2 8s infinite}.mg{stroke-dasharray:100;animation:draw 8s infinite}.mc{animation:pop3 8s infinite}@keyframes pop{0%,10%{opacity:0}20%,100%{opacity:1}}@keyframes pop2{0%,25%{opacity:0}35%,100%{opacity:1}}@keyframes draw{0%,45%{stroke-dashoffset:100}65%,100%{stroke-dashoffset:0}}@keyframes pop3{0%,62%{opacity:0}72%,100%{opacity:1}}@media (prefers-reduced-motion:reduce){.f1,.f2,.mg,.mc{animation:none}}</style>
<g fill="none" stroke-width="3">
<path d="M60,80 H520" stroke="#2F8CF0"/>
<path d="M160,80 C210,80 210,160 260,160 H400" stroke="#E8590C"/>
<path class="mg" pathLength="100" d="M400,160 C460,160 470,80 520,80" stroke="#E8590C"/>
</g>
<g stroke-width="2" font-size="13" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, monospace">
<circle cx="60" cy="80" r="16" fill="#DCEBFF" stroke="#2F8CF0"/><text x="60" y="85" stroke="none">a1</text>
<circle cx="160" cy="80" r="16" fill="#DCEBFF" stroke="#2F8CF0"/><text x="160" y="85" stroke="none">b2</text>
<circle cx="340" cy="80" r="16" fill="#DCEBFF" stroke="#2F8CF0"/><text x="340" y="85" stroke="none">c3</text>
<g class="f1"><circle cx="260" cy="160" r="16" fill="#FFE8D6" stroke="#E8590C"/><text x="260" y="165" stroke="none">f1</text></g>
<g class="f2"><circle cx="400" cy="160" r="16" fill="#FFE8D6" stroke="#E8590C"/><text x="400" y="165" stroke="none">f2</text></g>
<g class="mc"><circle cx="520" cy="80" r="21" fill="#FFFFFF" stroke="#2F8CF0"/><circle cx="520" cy="80" r="15" fill="#2F8CF0" stroke="#2F8CF0"/><text x="520" y="85" stroke="none" fill="#FFFFFF">M</text></g>
</g>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700">
<rect x="556" y="66" width="64" height="28" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="588" y="85" text-anchor="middle" fill="#1B66BC">main</text>
<rect x="276" y="186" width="140" height="28" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="346" y="205" text-anchor="middle" fill="#B84508">ricerca-corsi</text>
<text x="40" y="36" font-size="13" font-weight="400">git switch -c ricerca-corsi</text>
<path d="M150,44 L160,60" stroke="#8A87B0" stroke-width="2"/>
<text x="600" y="36" font-size="13" font-weight="400" text-anchor="end">git merge ricerca-corsi</text>
<path d="M530,44 L522,56" stroke="#8A87B0" stroke-width="2"/>
</g>
</svg>
SVG;
    }
}

/** Merge and rebase side by side: the same work joined with a merge commit, or replayed on top of main as A′ and B′. */
if (!function_exists('fig_git_rebase')) {
    function fig_git_rebase(string $merge, string $rebase): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.slide1{animation:s1 7s ease-in-out infinite}.slide2{animation:s2 7s ease-in-out infinite}@keyframes s1{0%,20%{transform:translate(-90px,70px)}55%,100%{transform:translate(0,0)}}@keyframes s2{0%,20%{transform:translate(-90px,70px)}55%,100%{transform:translate(0,0)}}@media (prefers-reduced-motion:reduce){.slide1,.slide2{animation:none}}</style>
<rect x="8" y="8" width="304" height="234" rx="12" fill="#F1F0F8"/>
<rect x="328" y="8" width="304" height="234" rx="12" fill="#F1F0F8"/>
<g font-family="ui-monospace, Menlo, monospace" font-weight="700" font-size="15"><text x="24" y="36">git merge</text><text x="344" y="36">git rebase main</text></g>
<text x="160" y="222" text-anchor="middle" font-size="13">{$merge}</text>
<text x="480" y="222" text-anchor="middle" font-size="13">{$rebase}</text>
<g fill="none" stroke-width="3">
<path d="M40,90 H280" stroke="#2F8CF0"/><path d="M100,90 C130,90 130,160 160,160 H220 C250,160 255,90 280,90" stroke="#E8590C"/>
<path d="M360,90 H600" stroke="#2F8CF0"/><path d="M420,90 C440,90 440,160 460,160 H510" stroke="#8A87B0" stroke-dasharray="6 5"/>
</g>
<g stroke-width="2" font-size="13" font-weight="700" text-anchor="middle">
<circle cx="40" cy="90" r="15" fill="#DCEBFF" stroke="#2F8CF0"/><circle cx="100" cy="90" r="15" fill="#DCEBFF" stroke="#2F8CF0"/><circle cx="200" cy="90" r="15" fill="#DCEBFF" stroke="#2F8CF0"/>
<circle cx="160" cy="160" r="15" fill="#FFE8D6" stroke="#E8590C"/><text x="160" y="165" stroke="none">A</text>
<circle cx="220" cy="160" r="15" fill="#FFE8D6" stroke="#E8590C"/><text x="220" y="165" stroke="none">B</text>
<circle cx="280" cy="90" r="18" fill="#2F8CF0" stroke="#2F8CF0"/><text x="280" y="95" stroke="none" fill="#FFFFFF">M</text>
<circle cx="360" cy="90" r="15" fill="#DCEBFF" stroke="#2F8CF0"/><circle cx="420" cy="90" r="15" fill="#DCEBFF" stroke="#2F8CF0"/><circle cx="480" cy="90" r="15" fill="#DCEBFF" stroke="#2F8CF0"/>
<circle cx="460" cy="160" r="15" fill="#FFFFFF" stroke="#8A87B0" stroke-dasharray="4 3"/><text x="460" y="165" stroke="none" fill="#6E6B96">A</text>
<circle cx="510" cy="160" r="15" fill="#FFFFFF" stroke="#8A87B0" stroke-dasharray="4 3"/><text x="510" y="165" stroke="none" fill="#6E6B96">B</text>
<g class="slide1"><circle cx="540" cy="90" r="15" fill="#FFE8D6" stroke="#E8590C"/><text x="540" y="95" stroke="none">A′</text></g>
<g class="slide2"><circle cx="600" cy="90" r="15" fill="#FFE8D6" stroke="#E8590C"/><text x="600" y="95" stroke="none">B′</text></g>
</g>
<path d="M495,90 H525" stroke="#2F8CF0" stroke-width="3"/><path d="M555,90 H585" stroke="#E8590C" stroke-width="3"/>
</svg>
SVG;
    }
}

/** Your computer and GitHub: push, pull and clone between them, and a pull request inside GitHub. */
if (!function_exists('fig_git_remote')) {
    function fig_git_remote(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker>
<marker id="ap" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#7048E8"/></marker></defs>
<rect x="16" y="16" width="208" height="228" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="416" y="16" width="208" height="228" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="120" y="44" text-anchor="middle" font-weight="700">{$t[0]}</text>
<text x="520" y="44" text-anchor="middle" font-weight="700" fill="#5A35C9">GitHub · origin</text>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" text-anchor="middle">
<rect x="56" y="70" width="128" height="28" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="120" y="89">main</text>
<rect x="44" y="118" width="152" height="28" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="120" y="137">pagina-contatti</text>
<rect x="456" y="70" width="128" height="28" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="520" y="89">main</text>
<rect x="444" y="170" width="152" height="28" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="520" y="189">pagina-contatti</text>
</g>
<path d="M444,184 C420,184 420,84 452,84" fill="none" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4" marker-end="url(#ap)"/>
<text x="520" y="126" text-anchor="middle" font-weight="700" fill="#5A35C9">pull request</text>
<text x="520" y="146" text-anchor="middle" font-size="13">{$t[1]}</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ah)">
<path d="M232,108 H406"/><path d="M408,150 H234"/><path d="M408,212 H234" stroke-dasharray="6 4"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700" text-anchor="middle">
<text x="320" y="100">git push</text><text x="320" y="142">git pull</text><text x="320" y="204">git clone</text></g>
<text x="320" y="268" text-anchor="middle" font-size="13">{$t[2]}</text>
</svg>
SVG;
    }
}

/** What the three modes of git reset do to the last commit, the staging area and the working directory. */
if (!function_exists('fig_git_reset')) {
    function fig_git_reset(array $t): string
    {
        $cell = function (int $x, int $y, string $kind, string $text): string {
            [$fill, $stroke, $dash, $col] = match ($kind) {
                'keep' => ['#D5F6EA', '#0B7F5C', '', '#0B7F5C'],
                'lost' => ['#FFE3EE', '#F03E82', ' stroke-dasharray="6 4"', '#B0205A'],
                default => ['#F1F0F8', '#8A87B0', '', '#4A4870'],
            };
            $cx = $x + 78; $ty = $y + 30;
            return "<rect x=\"$x\" y=\"$y\" width=\"156\" height=\"48\" rx=\"10\" fill=\"$fill\" stroke=\"$stroke\" stroke-width=\"2\"$dash/><text x=\"$cx\" y=\"$ty\" text-anchor=\"middle\" font-weight=\"700\" fill=\"$col\">$text</text>";
        };
        $rows = '';
        foreach ([['--soft', 'gone', 'keep', 'keep'], ['--mixed', 'gone', 'gone2', 'keep2'], ['--hard', 'gone', 'gone2', 'lost']] as $r => $row) {
            $y = 72 + $r * 58;
            $rows .= '<text x="20" y="' . ($y + 30) . '" font-family="ui-monospace, Menlo, monospace" font-weight="700">' . $row[0] . '</text>';
            $rows .= $cell(136, $y, 'gone', $t[3]);
            $rows .= $row[2] === 'keep' ? $cell(302, $y, 'keep', $t[4]) : $cell(302, $y, 'gone', $t[5]);
            $rows .= match ($row[3]) { 'keep' => $cell(468, $y, 'keep', $t[6]), 'keep2' => $cell(468, $y, 'keep', $t[4]), default => $cell(468, $y, 'lost', $t[7]) };
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="20" y="40" font-family="ui-monospace, Menlo, monospace" font-size="13">git reset HEAD~1</text>
<g font-weight="700" text-anchor="middle"><text x="214" y="40">{$t[0]}</text><text x="380" y="40">{$t[1]}</text><text x="546" y="40">{$t[2]}</text></g>
<line x1="16" y1="56" x2="624" y2="56" stroke="#8A87B0" stroke-width="2"/>
{$rows}
</svg>
SVG;
    }
}

/** A fork: the original repository (upstream), your copy on GitHub (origin) and your computer, with the commands between them. */
if (!function_exists('fig_git_fork')) {
    function fig_git_fork(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="24" y="20" width="210" height="72" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="406" y="20" width="210" height="72" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="215" y="208" width="210" height="72" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<g font-family="ui-monospace, Menlo, monospace" font-weight="700" text-anchor="middle"><text x="129" y="50" fill="#5A35C9">upstream</text><text x="511" y="50" fill="#1B66BC">origin</text></g>
<g font-size="13" text-anchor="middle"><text x="129" y="74">{$t[0]}</text><text x="511" y="74">{$t[1]}</text></g>
<text x="320" y="250" text-anchor="middle" font-weight="700" fill="#B84508">{$t[2]}</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ah)">
<path d="M240,42 H398"/><path d="M400,72 H242" stroke-dasharray="6 4"/>
<path d="M150,98 L262,202"/><path d="M490,98 L378,202" marker-start="url(#ah)"/></g>
<g font-size="14" font-weight="700" text-anchor="middle"><text x="320" y="34">fork</text><text x="320" y="112">pull request</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700"><text x="196" y="170" text-anchor="end">git fetch upstream</text><text x="448" y="170">clone · push</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'git', 'family' => 'git', 'after' => null,
    'title' => L('Git e GitHub', 'Git and GitHub'), 'short' => L('Git', 'Git'), 'level' => L('Base', 'Basics'),
    'desc' => L('Tenere la storia del codice e lavorare in squadra: repository e commit, branch e merge, remote e pull request, rimediare agli errori, configurazione e chiavi SSH, reset e reflog, fork e GitHub Actions.', 'Keeping code history and working in a team: repositories and commits, branches and merges, remotes and pull requests, fixing mistakes, setup and SSH keys, reset and reflog, forks and GitHub Actions.'),
    'lessons' => [
        lesson('commits', L('Repository e commit', 'Repositories and commits'), [
            P('**Git** registra la storia di un progetto in **commit**: istantanee con un messaggio, un autore e una data. I file passano per tre zone: la **cartella di lavoro** (dove modifichi), l’**area di stage** (cosa entrerà nel prossimo commit) e il **repository** (la storia).', '**Git** records a project’s history in **commits**: snapshots with a message, an author and a date. Files move through three areas: the **working directory** (where you edit), the **staging area** (what goes into the next commit) and the **repository** (the history).'),
            Fig(L(fig_git_areas(['Cartella di lavoro', 'Area di stage', 'Repository', 'dove modifichi', 'il prossimo commit', 'la storia']), fig_git_areas(['Working directory', 'Staging area', 'Repository', 'where you edit', 'the next commit', 'the history'])),
                L('Tre riquadri affiancati: cartella di lavoro, area di stage e repository. Una freccia con `git add` porta dal primo al secondo, una con `git commit` dal secondo al terzo, dove i commit sono cerchi in fila.', 'Three boxes side by side: working directory, staging area and repository. An arrow labelled `git add` leads from the first to the second, one labelled `git commit` from the second to the third, where the commits are circles in a row.'),
                L('Un file modificato passa dallo stage prima di entrare nella storia.', 'A changed file goes through the stage before entering the history.')),
            C('git init                       # crea un repository
git status                     # cosa è cambiato
git add index.html style.css   # mette in stage
git commit -m "Aggiungi la pagina iniziale"
git log --oneline              # storia in breve
git diff                       # differenze non ancora in stage', 'bash'),
            P('Scrivi messaggi brevi all’imperativo che dicano **cosa** e **perché** (“Correggi il calcolo dell’IVA sugli sconti”). Il file **`.gitignore`** elenca ciò che non va mai registrato: `node_modules/`, file `.env`, file generati.', 'Write short imperative messages saying **what** and **why** (“Fix VAT calculation on discounts”). The **`.gitignore`** file lists what must never be recorded: `node_modules/`, `.env` files, generated files.'),
        ], [
            Q('c1', L('Che cosa fa `git add`?', 'What does `git add` do?'), [L('Mette i file nell’area di stage', 'Puts files in the staging area'), L('Crea un commit', 'Creates a commit'), L('Invia al server', 'Sends to the server'), L('Cancella i file', 'Deletes files')], 0, L('Poi `git commit`.', 'Then `git commit`.')),
            Q('c2', L('A che cosa serve `.gitignore`?', 'What is `.gitignore` for?'), [L('Elencare i file da non registrare', 'Listing files not to record'), L('Nascondere i commit', 'Hiding commits'), L('Ignorare gli errori', 'Ignoring errors'), L('Cancellare la storia', 'Deleting history')], 0, L('Come `.env` e `node_modules/`.', 'Like `.env` and `node_modules/`.')),
            Q('c3', L('Quale comando mostra cosa è cambiato?', 'Which command shows what changed?'), ['git status', 'git init', 'git push', 'git clone'], 0, L('E `git diff` per i dettagli.', 'And `git diff` for details.')),
        ], task('bash', L('Metti in stage `app.js` e crea un commit con il messaggio `Aggiungi il carrello`.', 'Stage `app.js` and create a commit with the message `Aggiungi il carrello`.'), '', [re('git\s+add\s+app\.js', L('Metti in stage', 'You stage it')), re('git\s+commit\s+-m\s+["\']Aggiungi il carrello["\']', L('Crei il commit', 'You commit'))], '', "git add app.js\ngit commit -m \"Aggiungi il carrello\""), [
            dOrder(pSort(), ['git init', 'git add .', 'git commit -m "Primo commit"', 'git log --oneline'], L('Crea, prepara, registra, controlla.', 'Create, stage, record, check.')),
            dMatch(pMatch(), [[L('Cartella di lavoro', 'Working directory'), L('Dove modifichi', 'Where you edit')], [L('Area di stage', 'Staging area'), L('Il prossimo commit', 'The next commit')], ['Repository', L('La storia', 'The history')], ['.gitignore', L('File da non registrare', 'Files not to record')]], L('Le tre zone di Git.', 'Git’s three areas.')),
        ]),
        lesson('branches', L('Branch e merge', 'Branches and merges'), [
            P('Un **branch** è una linea di sviluppo separata: lavori su una funzione senza toccare il ramo principale (`main`). Quando hai finito, fai il **merge** per riportare le modifiche.', 'A **branch** is a separate line of development: you work on a feature without touching the main branch (`main`). When done, you **merge** to bring the changes back.'),
            Fig(fig_git_branch(),
                L('Un grafico di commit: la linea blu di main con i commit a1, b2, c3; dal commit b2 parte la linea arancione ricerca-corsi con f1 e f2, che rientra in main nel commit di merge M.', 'A commit graph: the blue main line with commits a1, b2, c3; from commit b2 the orange ricerca-corsi line starts with f1 and f2, and rejoins main in the merge commit M.'),
                L('Il branch nasce da un commit, cresce da solo e torna in `main` con un commit di merge.', 'The branch starts from a commit, grows on its own and returns to `main` with a merge commit.')),
            C('git switch -c ricerca-corsi      # crea e passa al branch
# … modifiche e commit …
git switch main
git merge ricerca-corsi
git branch -d ricerca-corsi       # cancella il branch unito', 'bash'),
            P('Se due branch hanno cambiato le stesse righe, Git segnala un **conflitto**: nei file compaiono i segni `<<<<<<<`, `=======` e `>>>>>>>`. Scegli il contenuto giusto, togli i segni, poi `git add` e `git commit`.', 'If two branches changed the same lines, Git reports a **conflict**: the files show `<<<<<<<`, `=======` and `>>>>>>>` markers. Pick the right content, remove the markers, then `git add` and `git commit`.'),
            Tip('Un branch costa pochissimo: è solo un’etichetta che punta a un commit e avanza a ogni nuovo commit. Creane uno per ogni funzione o correzione, con un nome che dica il lavoro (`fix-login`, `ricerca-corsi`), e tienilo corto: più vive a lungo lontano da `main`, più conflitti troverai al merge.', 'A branch costs almost nothing: it is just a label that points to a commit and moves forward with each new commit. Create one for every feature or fix, with a name that says the work (`fix-login`, `ricerca-corsi`), and keep it short-lived: the longer it lives away from `main`, the more conflicts you will meet at merge time.'),
        ], [
            Q('b1', L('Che cosa fa `git switch -c nome`?', 'What does `git switch -c name` do?'), [L('Crea un branch e ci passa', 'Creates a branch and switches to it'), L('Cancella un branch', 'Deletes a branch'), L('Fa il merge', 'Merges'), L('Clona un repository', 'Clones a repository')], 0, L('-c = create.', '-c = create.')),
            Q('b2', L('Quando nasce un conflitto?', 'When does a conflict arise?'), [L('Quando due branch cambiano le stesse righe', 'When two branches change the same lines'), L('A ogni merge', 'On every merge'), L('Quando il disco è pieno', 'When the disk is full'), L('Mai', 'Never')], 0, L('Git non sa quale versione scegliere.', 'Git cannot pick a version.')),
            Q('b3', L('Che cosa fai dopo aver risolto un conflitto?', 'What do you do after resolving a conflict?'), [L('git add e git commit', 'git add and git commit'), L('git init', 'git init'), L('Niente', 'Nothing'), L('Cancelli il repository', 'Delete the repository')], 0, L('Il merge si completa.', 'The merge completes.')),
        ], task('bash', L('Crea e passa al branch `nuovo-menu`, poi torna su `main` e fai il merge di `nuovo-menu`.', 'Create and switch to the branch `nuovo-menu`, then go back to `main` and merge `nuovo-menu`.'), '', [re('git\s+(switch\s+-c|checkout\s+-b)\s+nuovo-menu', L('Crei il branch', 'You create the branch')), re('git\s+(switch|checkout)\s+main', L('Torni su main', 'You go back to main')), re('git\s+merge\s+nuovo-menu', L('Fai il merge', 'You merge'))], '', "git switch -c nuovo-menu\ngit switch main\ngit merge nuovo-menu"), [
            dOrder(pSort(), ['git switch -c fix-login', 'git commit -am "Correggi il login"', 'git switch main', 'git merge fix-login', 'git branch -d fix-login'], L('Branch, lavoro, ritorno, merge, pulizia.', 'Branch, work, return, merge, cleanup.')),
            dFlash(pFlash(), [[L('Un branch permette di lavorare senza toccare main', 'A branch lets you work without touching main'), true], [L('Ogni merge produce un conflitto', 'Every merge produces a conflict'), false], [L('I segni <<<<<<< indicano un conflitto', '<<<<<<< markers indicate a conflict'), true], [L('Un branch cancellato perde i commit già uniti a main', 'A deleted branch loses commits already merged into main'), false]], L('Rami e unioni.', 'Branches and merges.')),
        ]),
        lesson('remote', L('GitHub e pull request', 'GitHub and pull requests'), [
            P('Un **remote** è una copia del repository su un server come **GitHub**. `clone` scarica un repository, `push` invia i tuoi commit, `pull` scarica e integra quelli degli altri (con un merge, oppure con un rebase se lo configuri).', 'A **remote** is a copy of the repository on a server like **GitHub**. `clone` downloads a repository, `push` sends your commits, `pull` downloads and integrates others’ (with a merge, or with a rebase if you configure it).'),
            C('git clone https://github.com/esempio/progetto.git
git switch -c pagina-contatti
# … commit …
git push -u origin pagina-contatti   # pubblica il branch
git pull                             # aggiorna con le modifiche degli altri', 'bash'),
            P('Su GitHub si apre una **pull request** (PR): la proposta di unire il tuo branch in `main`. I colleghi leggono il codice, commentano, i test automatici girano; quando tutto va bene si fa il merge. Così il codice si rivede prima di entrare.', 'On GitHub you open a **pull request** (PR): the proposal to merge your branch into `main`. Colleagues read the code, comment, automated tests run; when all is well it is merged. That way code is reviewed before it lands.'),
            Fig(L(fig_git_remote(['Il tuo computer', 'revisione e test', 'linea tratteggiata: solo la prima volta']), fig_git_remote(['Your computer', 'review and tests', 'dashed line: the first time only'])),
                L('A sinistra il tuo computer, a destra GitHub con il remote origin, entrambi con i branch main e pagina-contatti. Le frecce in mezzo: git push verso GitHub, git pull e git clone verso il computer. Dentro GitHub una freccia «pull request» va da pagina-contatti a main.', 'On the left your computer, on the right GitHub with the origin remote, both with the main and pagina-contatti branches. The arrows in between: git push towards GitHub, git pull and git clone towards the computer. Inside GitHub a “pull request” arrow goes from pagina-contatti to main.'),
                L('`push` e `pull` copiano commit tra le due copie; il merge in `main` passa dalla pull request.', '`push` and `pull` copy commits between the two copies; the merge into `main` goes through the pull request.')),
        ], [
            Q('r1', L('Che cosa fa `git push`?', 'What does `git push` do?'), [L('Invia i commit al remote', 'Sends commits to the remote'), L('Scarica i commit', 'Downloads commits'), L('Crea un branch', 'Creates a branch'), L('Annulla un commit', 'Undoes a commit')], 0, L('`pull` fa il contrario.', '`pull` does the opposite.')),
            Q('r2', L('Che cos’è una pull request?', 'What is a pull request?'), [L('La proposta di unire un branch, con revisione', 'A proposal to merge a branch, with review'), L('Un download', 'A download'), L('Un errore di GitHub', 'A GitHub error'), L('Un tipo di commit', 'A type of commit')], 0, L('Si rivede prima del merge.', 'It is reviewed before merging.')),
            Q('r3', L('Come si chiama di solito il remote principale?', 'What is the main remote usually called?'), ['origin', 'main', 'master', 'github'], 0, L('`main` è un branch.', '`main` is a branch.')),
        ], task('bash', L('Pubblica il branch `pagina-faq` su `origin` impostando il collegamento con `-u`.', 'Publish the `pagina-faq` branch to `origin` setting up tracking with `-u`.'), '', [re('git\s+push\s+(-u|--set-upstream)\s+origin\s+pagina-faq', L('Pubblichi il branch', 'You publish the branch'))], '', 'git push -u origin pagina-faq'), [
            dMatch(pMatch(), [['clone', L('Scarica un repository', 'Downloads a repository')], ['push', L('Invia i commit', 'Sends commits')], ['pull', L('Scarica e unisce', 'Downloads and merges')], ['origin', L('Il remote principale', 'The main remote')], ['Pull request', L('Proposta di merge', 'Merge proposal')]], L('Lavorare con un remote.', 'Working with a remote.')),
            dOrder(pSort(), [L('Crea un branch', 'Create a branch'), L('Fai i commit', 'Make commits'), L('git push', 'git push'), L('Apri la pull request', 'Open the pull request'), L('Revisione e merge', 'Review and merge')], L('Il flusso con GitHub.', 'The GitHub flow.')),
        ]),
        lesson('undo', L('Rimediare agli errori', 'Fixing mistakes'), [
            P('Con Git quasi tutto si recupera. Per annullare modifiche **non ancora in commit**: `git restore file`. Per togliere un file dallo stage: `git restore --staged file`. Per correggere il **messaggio dell’ultimo commit** (non ancora pubblicato): `git commit --amend`.', 'With Git almost everything is recoverable. To discard changes **not yet committed**: `git restore file`. To unstage a file: `git restore --staged file`. To fix the **last commit message** (not yet pushed): `git commit --amend`.'),
            C('git restore app.js                 # butta le modifiche locali
git restore --staged app.js        # toglie dallo stage
git commit --amend -m "Messaggio corretto"
git revert a1b2c3d                 # nuovo commit che annulla un commit pubblicato
git stash                          # mette da parte le modifiche
git stash pop                      # le riprende', 'bash'),
            P('Per un commit **già pubblicato** usa `git revert`: crea un nuovo commit che annulla l’altro senza riscrivere la storia condivisa. Evita `git push --force` sui branch usati da altri.', 'For a commit **already pushed** use `git revert`: it creates a new commit undoing the other without rewriting shared history. Avoid `git push --force` on branches others use.'),
            Tip('La regola pratica: finché un commit è solo sul tuo computer puoi riscriverlo (`--amend`, `restore`); una volta pubblicato, rimedia con un commit nuovo (`revert`). E quando non ricordi il comando, leggi `git status`: suggerisce proprio come annullare ciò che vedi.', 'The rule of thumb: while a commit is only on your computer you may rewrite it (`--amend`, `restore`); once pushed, fix it with a new commit (`revert`). And when you cannot remember the command, read `git status`: it suggests exactly how to undo what you see.'),
        ], [
            Q('u1', L('Come annulli un commit già pubblicato?', 'How do you undo an already pushed commit?'), ['git revert', 'git commit --amend', 'git restore', 'git init'], 0, L('Non riscrive la storia condivisa.', 'It does not rewrite shared history.')),
            Q('u2', L('Che cosa fa `git stash`?', 'What does `git stash` do?'), [L('Mette da parte le modifiche', 'Sets changes aside'), L('Cancella il repository', 'Deletes the repository'), L('Pubblica le modifiche', 'Publishes changes'), L('Crea un branch', 'Creates a branch')], 0, L('Le riprendi con `stash pop`.', 'Bring them back with `stash pop`.')),
            Q('u3', L('Perché evitare `push --force` su un branch condiviso?', 'Why avoid `push --force` on a shared branch?'), [L('Riscrive la storia e può cancellare il lavoro altrui', 'It rewrites history and can erase others’ work'), L('È più lento', 'It is slower'), L('Non esiste', 'It does not exist'), L('Non c’è motivo', 'There is no reason')], 0, L('Collaborare richiede prudenza.', 'Collaboration needs caution.')),
        ], task('bash', L('Togli `config.js` dallo stage e poi annulla le sue modifiche locali.', 'Unstage `config.js` and then discard its local changes.'), '', [re('git\s+restore\s+--staged\s+config\.js', L('Lo togli dallo stage', 'You unstage it')), re('git\s+restore\s+config\.js', L('Annulli le modifiche', 'You discard the changes'))], '', "git restore --staged config.js\ngit restore config.js"), [
            dMatch(pMatch(), [['git restore', L('Butta le modifiche locali', 'Discards local changes')], ['--staged', L('Toglie dallo stage', 'Unstages')], ['--amend', L('Corregge l’ultimo commit', 'Fixes the last commit')], ['revert', L('Annulla un commit pubblicato', 'Undoes a pushed commit')], ['stash', L('Mette da parte', 'Sets aside')]], L('La cassetta del pronto soccorso.', 'The first-aid kit.')),
            dFlash(pFlash(), [[L('git revert crea un nuovo commit', 'git revert creates a new commit'), true], [L('--amend va bene su commit già pubblicati e condivisi', '--amend is fine on pushed, shared commits'), false], [L('git stash pop riprende le modifiche', 'git stash pop brings changes back'), true], [L('push --force è sempre sicuro', 'push --force is always safe'), false]], L('Sbagliare si può, rimediare anche.', 'Mistakes happen, fixes too.')),
        ]),
        lesson('history', L('Rebase, tag e storia pulita', 'Rebase, tags and a clean history'), [
            P('`git rebase main` riapplica i commit del tuo branch **sopra** l’ultima versione di `main`: la storia diventa una linea dritta, senza commit di merge. Usalo sui tuoi branch **locali**, mai su commit già condivisi.', '`git rebase main` replays your branch’s commits **on top of** the latest `main`: history becomes a straight line, with no merge commits. Use it on your **local** branches, never on commits already shared.'),
            Fig(L(fig_git_rebase('biforcazione + commit di merge M', 'una linea sola, commit riscritti A′ e B′'), fig_git_rebase('a fork + merge commit M', 'one straight line, commits rewritten as A′ and B′')),
                L('Due grafici affiancati. A sinistra, con git merge, i commit A e B restano su un ramo e rientrano in main con il commit di merge M. A destra, con git rebase main, A e B tratteggiati restano al vecchio posto e le loro copie A′ e B′ sono in fila in cima a main.', 'Two graphs side by side. On the left, with git merge, commits A and B stay on a branch and rejoin main with merge commit M. On the right, with git rebase main, dashed A and B stay in the old place and their copies A′ and B′ sit in line on top of main.'),
                L('Il rebase non sposta i commit: ne crea copie nuove, con hash diversi. Per questo non va usato su commit condivisi.', 'Rebase does not move commits: it creates new copies, with different hashes. That is why it must not be used on shared commits.')),
            C('git switch mia-funzione
git fetch origin
git rebase origin/main          # porta il branch in cima a main
git tag -a v1.2.0 -m "Versione 1.2.0"
git push origin v1.2.0          # pubblica il tag
git log --oneline --graph       # la storia come grafico', 'bash'),
            P('Un **tag** dà un nome stabile a un commit, di solito una versione (`v1.2.0`). Con `git log --graph` vedi branch e merge disegnati; con `git blame file` scopri chi ha cambiato ogni riga e in quale commit.', 'A **tag** gives a stable name to a commit, usually a version (`v1.2.0`). With `git log --graph` you see branches and merges drawn; with `git blame file` you find who changed each line and in which commit.'),
        ], [
            Q('hi1', L('Che cosa fa `git rebase main`?', 'What does `git rebase main` do?'), [L('Riapplica i tuoi commit sopra main', 'Replays your commits on top of main'), L('Cancella main', 'Deletes main'), L('Crea un tag', 'Creates a tag'), L('Unisce con un commit di merge', 'Merges with a merge commit')], 0, L('Storia lineare.', 'Linear history.')),
            Q('hi2', L('Quando evitare il rebase?', 'When should you avoid rebase?'), [L('Su commit già condivisi', 'On commits already shared'), L('Sui branch locali', 'On local branches'), L('Prima di una PR', 'Before a PR'), L('Mai', 'Never')], 0, L('Riscrive la storia.', 'It rewrites history.')),
            Q('hi3', L('A che cosa serve un tag?', 'What is a tag for?'), [L('Dare un nome stabile a un commit, come una versione', 'Giving a commit a stable name, like a version'), L('Creare un branch', 'Creating a branch'), L('Annullare un commit', 'Undoing a commit'), L('Nascondere file', 'Hiding files')], 0, L('Per esempio `v1.2.0`.', 'For example `v1.2.0`.')),
        ], task('bash', L('Crea il tag annotato `v2.0.0` con il messaggio `Versione 2` e pubblicalo su `origin`.', 'Create the annotated tag `v2.0.0` with the message `Versione 2` and push it to `origin`.'), '', [re('git\s+tag\s+-a\s+v2\.0\.0\s+-m\s+["\']Versione 2["\']', L('Crei il tag', 'You create the tag')), re('git\s+push\s+origin\s+v2\.0\.0', L('Lo pubblichi', 'You push it'))], '', "git tag -a v2.0.0 -m \"Versione 2\"\ngit push origin v2.0.0"), [
            dMatch(pMatch(), [['rebase', L('Storia lineare', 'Linear history')], ['tag', L('Nome di una versione', 'A version’s name')], ['blame', L('Chi ha cambiato la riga', 'Who changed the line')], ['log --graph', L('Storia disegnata', 'Drawn history')]], L('Leggere e ordinare la storia.', 'Reading and tidying history.')),
            dFlash(pFlash(), [[L('Il rebase riscrive i commit', 'Rebase rewrites commits'), true], [L('Il rebase su main condiviso è innocuo', 'Rebasing shared main is harmless'), false], [L('I tag vanno pubblicati con push', 'Tags must be pushed'), true], [L('git blame cancella le righe sbagliate', 'git blame deletes wrong lines'), false]], L('Una storia pulita.', 'A clean history.')),
        ]),
        lesson('setup', L('Configurazione e accesso a GitHub', 'Setup and access to GitHub'), [
            P('Prima del primo commit Git deve sapere **chi sei**: nome ed email finiscono in ogni commit. Con `--global` la configurazione vale per tutti i tuoi repository; senza, solo per quello corrente. Conviene anche impostare `init.defaultBranch`, così `git init` crea subito il branch `main` (le versioni 2.x di Git, se non configurate, usano ancora `master`).', 'Before your first commit Git must know **who you are**: name and email go into every commit. With `--global` the setting applies to all your repositories; without it, only to the current one. It is also worth setting `init.defaultBranch`, so that `git init` creates the `main` branch straight away (Git 2.x versions, if not configured, still use `master`).'),
            C('git config --global user.name "Ada Rossi"
git config --global user.email "ada@esempio.it"
git config --global init.defaultBranch main
git config --list --show-origin        # cosa è impostato e dove

ssh-keygen -t ed25519 -C "ada@esempio.it"   # crea la coppia di chiavi
cat ~/.ssh/id_ed25519.pub                   # la parte pubblica, da caricare su GitHub
ssh -T git@github.com                       # prova il collegamento', 'bash'),
            P('Per `push` e `pull` GitHub **non accetta la password dell’account**: usa una **chiave SSH** (carichi su GitHub solo il file `.pub`, la chiave privata non lascia mai il tuo computer), un token personale oppure l’accesso guidato di `gh auth login` o di Git Credential Manager.', 'For `push` and `pull` GitHub **does not accept your account password**: use an **SSH key** (you upload only the `.pub` file to GitHub, the private key never leaves your computer), a personal access token, or the guided sign-in of `gh auth login` or Git Credential Manager.'),
        ], [
            Q('st1', L('Che cosa imposta `git config --global user.email`?', 'What does `git config --global user.email` set?'), [L('L’email a cui GitHub manda gli avvisi', 'The email GitHub sends notifications to'), L('L’email registrata come autore nei tuoi commit', 'The email recorded as author in your commits'), L('L’indirizzo del server remoto', 'The address of the remote server'), L('L’email per recuperare la password', 'The email for password recovery')], 1, L('Nome ed email entrano in ogni commit.', 'Name and email go into every commit.')),
            Q('st2', L('Quale file di una chiave SSH carichi su GitHub?', 'Which file of an SSH key do you upload to GitHub?'), ['id_ed25519', 'known_hosts', 'id_ed25519.pub', '.gitconfig'], 2, L('La parte pubblica, `.pub`. La privata resta sul tuo computer.', 'The public part, `.pub`. The private one stays on your computer.')),
            Q('st3', L('Come fai in modo che `git init` crei sempre il branch `main`?', 'How do you make `git init` always create the `main` branch?'), ['git branch --default main', 'git init --main', 'git config core.branch main', 'git config --global init.defaultBranch main'], 3, L('Vale per tutti i nuovi repository.', 'It applies to every new repository.')),
        ], task('bash', L('Imposta a livello globale il nome `Ada Rossi` e il branch iniziale `main`.', 'Set globally the name `Ada Rossi` and the initial branch `main`.'), '', [re('git\s+config\s+--global\s+user\.name\s+["\']Ada Rossi["\']', L('Il nome', 'The name')), re('git\s+config\s+--global\s+init\.defaultBranch\s+["\']?main["\']?', L('Il branch iniziale', 'The initial branch'))], '', "git config --global user.name \"Ada Rossi\"\ngit config --global init.defaultBranch main"), [
            dMatch(pMatch(), [['user.name', L('Il nome nei commit', 'The name in commits')], ['init.defaultBranch', L('Il primo branch di git init', 'The first branch of git init')], ['id_ed25519.pub', L('Chiave pubblica da caricare', 'Public key to upload')], ['id_ed25519', L('Chiave privata da non condividere', 'Private key never to share')], ['gh auth login', L('Accesso a GitHub dal terminale', 'Signing in to GitHub from the terminal')]], L('Prepararsi a lavorare.', 'Getting ready to work.')),
            dFlash(pFlash(), [[L('GitHub accetta la password dell’account per git push via HTTPS', 'GitHub accepts the account password for git push over HTTPS'), false], [L('La chiave privata resta sul tuo computer', 'The private key stays on your computer'), true], [L('--global vale per tutti i tuoi repository', '--global applies to all your repositories'), true], [L('Nome ed email si scelgono una volta per ogni commit', 'Name and email are chosen once per commit'), false]], L('Chi sei e come entri.', 'Who you are and how you get in.')),
        ]),
        lesson('inspect', L('Leggere la storia e le differenze', 'Reading history and differences'), [
            P('`HEAD` è il commit su cui ti trovi; `HEAD~1` è quello prima, `HEAD~2` due prima. `git show` mostra un commit con le sue modifiche, `git diff` le differenze **non ancora in stage** e `git diff --staged` quelle **già in stage**, cioè cosa entrerà nel prossimo commit.', '`HEAD` is the commit you are on; `HEAD~1` is the one before, `HEAD~2` two before. `git show` displays a commit with its changes, `git diff` the differences **not yet staged** and `git diff --staged` those **already staged**, that is what goes into the next commit.'),
            C('git log --oneline -5                       # gli ultimi 5 commit
git log --author="Ada" --since="2 weeks ago"
git log -p -- src/carrello.js              # la storia di un file, con le modifiche
git show HEAD~1                            # il commit precedente
git diff --staged                          # cosa entrerà nel prossimo commit
git diff main..ricerca-corsi               # differenze tra due branch
git log -S "calcolaIva" --oneline          # commit che aggiungono o tolgono quel testo', 'bash'),
            P('Per scoprire **quale commit ha introdotto un bug** c’è `git bisect`: gli dici un commit buono e uno cattivo, Git ti porta a metà strada, tu provi e rispondi `good` o `bad`. Dimezzando ogni volta, tra mille commit lo trovi in una decina di passi.', 'To find **which commit introduced a bug** there is `git bisect`: you give it a good commit and a bad one, Git takes you halfway, you test and answer `good` or `bad`. Halving each time, among a thousand commits you find it in about ten steps.'),
        ], [
            Q('in1', L('Che cosa mostra `git diff --staged`?', 'What does `git diff --staged` show?'), [L('Le modifiche non ancora in stage', 'Changes not yet staged'), L('I commit non ancora pubblicati', 'Commits not yet pushed'), L('Le modifiche in stage, pronte per il commit', 'Staged changes, ready for the commit'), L('I file elencati in .gitignore', 'The files listed in .gitignore')], 2, L('Senza `--staged` vedi quelle non in stage.', 'Without `--staged` you see the unstaged ones.')),
            Q('in2', L('Che cosa indica `HEAD~2`?', 'What does `HEAD~2` refer to?'), [L('Il commit due passi prima di quello corrente', 'The commit two steps before the current one'), L('Il secondo branch creato nel repository', 'The second branch created in the repository'), L('Il secondo remote configurato', 'The second configured remote'), L('I due commit più recenti di main', 'The two latest commits on main')], 0, L('`~` risale la storia.', '`~` walks back through history.')),
            Q('in3', L('Quale comando cerca dimezzando il commit che ha introdotto un bug?', 'Which command finds, by halving, the commit that introduced a bug?'), ['git blame', 'git bisect', 'git log -p', 'git grep'], 1, L('`blame` dice chi ha cambiato una riga, non quando è nato il bug.', '`blame` tells who changed a line, not when the bug appeared.')),
        ], task('bash', L('Mostra gli ultimi 3 commit in forma compatta, poi i dettagli del commit precedente a quello corrente.', 'Show the last 3 commits in compact form, then the details of the commit before the current one.'), '', [re('git\s+log\s+(--oneline\s+(-3|-n\s*3|--max-count=3)\b|(-3|-n\s*3|--max-count=3)\s+--oneline)', L('Gli ultimi 3 commit', 'The last 3 commits')), re('git\s+show\s+HEAD(~1?|\^1?)\s*$', L('Il commit precedente', 'The previous commit'))], '', "git log --oneline -3\ngit show HEAD~1"), [
            dMatch(pMatch(), [['HEAD', L('Il commit corrente', 'The current commit')], ['HEAD~1', L('Il commit precedente', 'The previous commit')], ['git show', L('Un commit e le sue modifiche', 'A commit and its changes')], ['git diff --staged', L('Cosa entrerà nel commit', 'What goes into the commit')], ['git log -S', L('Cerca un testo nella storia', 'Searches text in history')]], L('Orientarsi nella storia.', 'Finding your way in history.')),
            dOrder(pSort(), ['git bisect start', 'git bisect bad', 'git bisect good v1.0', L('Prova la versione proposta e rispondi good o bad', 'Test the proposed version and answer good or bad'), 'git bisect reset'], L('Inizia, indica cattivo e buono, prova, torna dove eri.', 'Start, mark bad and good, test, go back where you were.')),
        ]),
        lesson('reset-reflog', L('Reset, reflog e cherry-pick', 'Reset, reflog and cherry-pick'), [
            P('`git reset` sposta il branch su un commit precedente. Con `--soft` i cambiamenti dei commit tolti restano **in stage**; con il modo normale (`--mixed`) restano nei file ma **fuori dallo stage**; con `--hard` vengono **buttati**, insieme alle modifiche non salvate. Usalo solo su commit **non ancora condivisi**.', '`git reset` moves the branch to an earlier commit. With `--soft` the changes of the removed commits stay **staged**; with the default mode (`--mixed`) they stay in the files but **unstaged**; with `--hard` they are **thrown away**, together with unsaved changes. Use it only on commits **not yet shared**.'),
            Fig(L(fig_git_reset(['Ultimo commit', 'Area di stage', 'Cartella di lavoro', 'annullato', 'modifiche qui', 'svuotata', 'intatta', 'modifiche perse']), fig_git_reset(['Last commit', 'Staging area', 'Working directory', 'undone', 'changes here', 'emptied', 'untouched', 'changes lost'])),
                L('Una tabella con tre righe, --soft, --mixed e --hard, e tre colonne: ultimo commit, area di stage, cartella di lavoro. Il commit è annullato in tutte e tre. Con --soft le modifiche sono nello stage; con --mixed lo stage è svuotato e le modifiche sono nella cartella di lavoro; con --hard anche la cartella perde le modifiche.', 'A table with three rows, --soft, --mixed and --hard, and three columns: last commit, staging area, working directory. The commit is undone in all three. With --soft the changes are in the stage; with --mixed the stage is emptied and the changes are in the working directory; with --hard the directory loses the changes too.'),
                L('Più si scende, più `reset` butta via: solo `--hard` tocca i tuoi file.', 'The further down, the more `reset` throws away: only `--hard` touches your files.')),
            C('git reset --soft HEAD~1      # annulla l’ultimo commit, modifiche in stage
git reset HEAD~1             # annulla il commit, modifiche nei file
git reset --hard HEAD~1      # annulla tutto: attenzione!
git reflog                   # dove è stato HEAD di recente
git reset --hard HEAD@{2}    # torna a com’era due spostamenti fa
git cherry-pick a1b2c3d      # copia un singolo commit sul branch corrente', 'bash'),
            P('Il **reflog** è il registro locale di dove è passato `HEAD`: anche dopo un `reset --hard` sbagliato ritrovi il commit perso e ci torni (le voci restano per un periodo limitato, di solito da 30 a 90 giorni). **`cherry-pick`** copia un commit su un altro branch, per esempio una correzione da `main` a un branch di rilascio.', 'The **reflog** is the local record of where `HEAD` has been: even after a wrong `reset --hard` you can find the lost commit and go back to it (entries are kept for a limited time, usually 30 to 90 days). **`cherry-pick`** copies a commit onto another branch, for example a fix from `main` to a release branch.'),
            Tip('Il reflog ricorda i commit, non le modifiche mai salvate: quello che `reset --hard` butta senza commit è perso. Nel dubbio, prima fai `git stash`.', 'The reflog remembers commits, not changes never saved: what `reset --hard` throws away without a commit is gone. When in doubt, run `git stash` first.'),
        ], [
            Q('rf1', L('Quale modo di `git reset` butta via le modifiche?', 'Which `git reset` mode throws the changes away?'), ['--soft', '--mixed', '--hard', '--keep'], 2, L('`--soft` e `--mixed` le conservano.', '`--soft` and `--mixed` keep them.')),
            Q('rf2', L('Dopo un `reset --hard` sbagliato, dove ritrovi il commit perso?', 'After a wrong `reset --hard`, where do you find the lost commit?'), [L('Nell’elenco di `git stash list`', 'In the `git stash list` output'), L('Nel registro di `git reflog`', 'In the `git reflog` record'), L('Nel file `.gitignore` del progetto', 'In the project’s `.gitignore` file'), L('Solo nel remote, se l’avevi pubblicato', 'Only on the remote, if you had pushed it')], 1, L('Il reflog registra ogni spostamento di HEAD.', 'The reflog records every move of HEAD.')),
            Q('rf3', L('Che cosa fa `git cherry-pick a1b2c3d`?', 'What does `git cherry-pick a1b2c3d` do?'), [L('Cancella quel commit dalla storia', 'Deletes that commit from history'), L('Sposta il branch corrente su quel commit', 'Moves the current branch to that commit'), L('Mostra chi ha scritto quel commit', 'Shows who wrote that commit'), L('Applica le modifiche di quel commit sul branch corrente', 'Applies that commit’s changes to the current branch')], 3, L('Nasce un nuovo commit con le stesse modifiche.', 'A new commit with the same changes is created.')),
        ], task('bash', L('Annulla l’ultimo commit locale tenendo le modifiche in stage, poi copia il commit `f00ba12` sul branch corrente.', 'Undo the last local commit keeping the changes staged, then copy commit `f00ba12` onto the current branch.'), '', [re('git\s+reset\s+--soft\s+HEAD(~1?|\^1?)(\s|$)', L('Reset morbido', 'Soft reset')), re('git\s+cherry-pick\s+f00ba12', L('Copi il commit', 'You copy the commit'))], '', "git reset --soft HEAD~1\ngit cherry-pick f00ba12"), [
            dMatch(pMatch(), [['--soft', L('Modifiche in stage', 'Changes staged')], ['--mixed', L('Modifiche nei file, fuori dallo stage', 'Changes in files, unstaged')], ['--hard', L('Modifiche buttate', 'Changes thrown away')], ['reflog', L('Dove è stato HEAD', 'Where HEAD has been')], ['cherry-pick', L('Copia un commit', 'Copies a commit')]], L('Tornare indietro con giudizio.', 'Going back wisely.')),
            dFlash(pFlash(), [[L('Il reflog è condiviso con il remote', 'The reflog is shared with the remote'), false], [L('reset --hard può perdere per sempre le modifiche non salvate', 'reset --hard can lose unsaved changes for good'), true], [L('cherry-pick crea un nuovo commit con un hash diverso', 'cherry-pick creates a new commit with a different hash'), true], [L('reset va bene su commit che altri hanno già scaricato', 'reset is fine on commits others have already pulled'), false]], L('La rete di sicurezza.', 'The safety net.')),
        ]),
        lesson('sync', L('Fetch, fork e sincronizzazione', 'Fetch, forks and syncing'), [
            P('`git fetch` scarica i nuovi commit del remote in `origin/main` **senza toccare** i tuoi file: puoi guardarli prima di integrarli. `git pull` fa fetch e poi integra. Se sia tu sia gli altri avete commit nuovi, Git ti chiede come unirli: con un merge o con `git pull --rebase`, che rimette i tuoi commit in cima.', '`git fetch` downloads the remote’s new commits into `origin/main` **without touching** your files: you can look at them before integrating them. `git pull` fetches and then integrates. If both you and others have new commits, Git asks how to combine them: with a merge or with `git pull --rebase`, which puts your commits back on top.'),
            C('git fetch origin
git log --oneline main..origin/main      # cosa è arrivato
git pull --rebase                        # integra mettendo i tuoi commit in cima
git config --global pull.rebase true     # rendilo il comportamento normale

# in un fork
git remote add upstream https://github.com/originale/progetto.git
git fetch upstream
git rebase upstream/main
git push --force-with-lease              # riscrive solo il TUO branch, con prudenza', 'bash'),
            P('Su un progetto dove non puoi scrivere fai un **fork**: la tua copia su GitHub. Lavori lì e apri la pull request verso l’originale, che di solito si aggiunge come remote `upstream`. Se devi riscrivere un tuo branch già pubblicato (dopo un rebase), usa `--force-with-lease`: si rifiuta se qualcun altro ha pubblicato nel frattempo.', 'On a project you cannot write to you make a **fork**: your own copy on GitHub. You work there and open the pull request towards the original, which is usually added as the `upstream` remote. If you must rewrite one of your branches already pushed (after a rebase), use `--force-with-lease`: it refuses if someone else has pushed in the meantime.'),
            Fig(L(fig_git_fork(['repository originale', 'il tuo fork su GitHub', 'Il tuo computer']), fig_git_fork(['original repository', 'your fork on GitHub', 'Your computer'])),
                L('Tre riquadri: in alto a sinistra upstream, il repository originale; in alto a destra origin, il tuo fork; in basso il tuo computer. Fork va da upstream a origin, la pull request da origin a upstream, clone e push collegano origin e il computer, git fetch upstream scende dall’originale al computer.', 'Three boxes: top left upstream, the original repository; top right origin, your fork; at the bottom your computer. Fork goes from upstream to origin, the pull request from origin to upstream, clone and push link origin and the computer, git fetch upstream comes down from the original to the computer.'),
                L('Scrivi solo sul tuo fork; all’originale arrivi con una pull request.', 'You write only to your fork; you reach the original through a pull request.')),
        ], [
            Q('sy1', L('Che differenza c’è tra `fetch` e `pull`?', 'What is the difference between `fetch` and `pull`?'), [L('fetch pubblica i commit, pull li scarica', 'fetch publishes commits, pull downloads them'), L('Nessuna, sono due nomi dello stesso comando', 'None, they are two names for one command'), L('fetch scarica senza toccare i tuoi file, pull scarica e integra', 'fetch downloads without touching your files, pull downloads and integrates'), L('fetch funziona solo con i fork, pull solo con origin', 'fetch only works with forks, pull only with origin')], 2, L('pull = fetch + merge o rebase.', 'pull = fetch + merge or rebase.')),
            Q('sy2', L('In un fork, che cosa indica di solito il remote `upstream`?', 'In a fork, what does the `upstream` remote usually point to?'), [L('La tua copia del progetto su GitHub', 'Your own copy of the project on GitHub'), L('Il repository originale da cui hai fatto il fork', 'The original repository you forked from'), L('Il branch principale del tuo computer', 'The main branch on your computer'), L('Un server di backup automatico di GitHub', 'An automatic GitHub backup server')], 1, L('`origin` è il tuo fork.', '`origin` is your fork.')),
            Q('sy3', L('Perché preferire `--force-with-lease` a `--force`?', 'Why prefer `--force-with-lease` to `--force`?'), [L('È più veloce sui repository molto grandi', 'It is faster on very large repositories'), L('Pubblica anche tutti i tag locali', 'It also pushes all local tags'), L('Funziona anche senza connessione a internet', 'It works without an internet connection too'), L('Si rifiuta se sul remote ci sono commit che non hai visto', 'It refuses if the remote has commits you have not seen')], 3, L('Non cancella per sbaglio il lavoro altrui.', 'It does not erase others’ work by mistake.')),
        ], task('bash', L('Aggiungi il remote `upstream` con l’indirizzo `https://github.com/fucina/corsi.git`, scaricalo e fai il rebase su `upstream/main`.', 'Add the `upstream` remote with the address `https://github.com/fucina/corsi.git`, fetch it and rebase onto `upstream/main`.'), '', [re('git\s+remote\s+add\s+upstream\s+https://github\.com/fucina/corsi\.git', L('Aggiungi il remote', 'You add the remote')), re('git\s+fetch\s+upstream', L('Lo scarichi', 'You fetch it')), re('git\s+rebase\s+upstream/main', L('Fai il rebase', 'You rebase'))], '', "git remote add upstream https://github.com/fucina/corsi.git\ngit fetch upstream\ngit rebase upstream/main"), [
            dMatch(pMatch(), [['fetch', L('Scarica senza integrare', 'Downloads without integrating')], ['pull --rebase', L('Scarica e mette i tuoi commit in cima', 'Downloads and puts your commits on top')], ['Fork', L('La tua copia su GitHub', 'Your copy on GitHub')], ['upstream', L('Il repository originale', 'The original repository')], ['--force-with-lease', L('Forzatura prudente', 'Careful force push')]], L('Restare al passo.', 'Keeping up.')),
            dOrder(pSort(), [L('Fai il fork su GitHub', 'Fork on GitHub'), L('Clona il tuo fork', 'Clone your fork'), 'git switch -c correzione', 'git push -u origin correzione', L('Apri la PR verso il repository originale', 'Open the PR towards the original repository')], L('Contribuire a un progetto altrui.', 'Contributing to someone else’s project.')),
        ]),
        lesson('actions', L('GitHub Actions e branch protetti', 'GitHub Actions and protected branches'), [
            P('**GitHub Actions** esegue dei **workflow** quando succede qualcosa nel repository: un push, una pull request, un orario. I workflow sono file YAML nella cartella `.github/workflows/`; ogni **job** gira su una macchina pulita (runner) e fa una serie di **step**: scaricare il codice, installare, testare.', '**GitHub Actions** runs **workflows** when something happens in the repository: a push, a pull request, a schedule. Workflows are YAML files in the `.github/workflows/` folder; each **job** runs on a clean machine (runner) and performs a series of **steps**: checking out the code, installing, testing.'),
            C('name: Test
on:
  pull_request:
  push:
    branches: [main]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v5
      - uses: actions/setup-node@v5
        with:
          node-version: 22
      - run: npm ci
      - run: npm test', 'yaml'),
            P('Con le **regole di protezione** (o i ruleset) su `main` puoi chiedere che si entri solo da una pull request, con un’approvazione e i controlli verdi: niente push diretti di codice rotto. Token e password vanno nei **secret** del repository e si leggono con `${{ secrets.NOME }}`, mai scritti nel file.', 'With **protection rules** (or rulesets) on `main` you can require that changes enter only through a pull request, with an approval and green checks: no direct pushes of broken code. Tokens and passwords go into the repository’s **secrets** and are read with `${{ secrets.NAME }}`, never written in the file.'),
        ], [
            Q('ac1', L('Dove si mettono i file dei workflow?', 'Where do workflow files go?'), ['.git/hooks/', '.github/workflows/', '.github/actions.json', 'ci/workflows/'], 1, L('Un file YAML per workflow.', 'One YAML file per workflow.')),
            Q('ac2', L('Che cosa fa `on: pull_request`?', 'What does `on: pull_request` do?'), [L('Avvia il workflow quando una PR si apre o si aggiorna', 'Starts the workflow when a PR is opened or updated'), L('Unisce la PR quando i test passano', 'Merges the PR when the tests pass'), L('Crea una pull request a ogni push', 'Creates a pull request on every push'), L('Blocca le PR senza una descrizione', 'Blocks PRs without a description')], 0, L('`on` elenca gli eventi che fanno partire il workflow.', '`on` lists the events that start the workflow.')),
            Q('ac3', L('Come deve usare un workflow un token segreto?', 'How should a workflow use a secret token?'), [L('Scritto nel file YAML, così è versionato', 'Written in the YAML file, so it is versioned'), L('In un commento in cima al workflow', 'In a comment at the top of the workflow'), L('Come secret del repository, letto con `${{ secrets.NOME }}`', 'As a repository secret, read with `${{ secrets.NAME }}`'), L('In un file `.env` pubblicato nel repository', 'In a `.env` file committed to the repository')], 2, L('Il file è visibile a chi legge il repository.', 'The file is visible to anyone reading the repository.')),
        ], task('yaml', L('Completa il job `test`: deve girare su `ubuntu-latest`, scaricare il codice con `actions/checkout` ed eseguire `npm test`.', 'Complete the `test` job: it must run on `ubuntu-latest`, check out the code with `actions/checkout` and run `npm test`.'), "name: Test\non: [push, pull_request]\njobs:\n  test:\n", [re('runs-on:\s*ubuntu-latest', L('Il runner', 'The runner')), re('-\s*uses:\s*actions/checkout@v\d+', L('Scarichi il codice', 'You check out the code')), re('-\s*run:\s*npm\s+(run\s+)?test', L('Esegui i test', 'You run the tests'))], '', "name: Test\non: [push, pull_request]\njobs:\n  test:\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@v5\n      - run: npm ci\n      - run: npm test"), [
            dMatch(pMatch(), [['on', L('Gli eventi che avviano il workflow', 'The events that start the workflow')], ['runs-on', L('La macchina che esegue', 'The machine that runs it')], ['uses', L('Un’azione già pronta', 'A ready-made action')], ['run', L('Un comando di shell', 'A shell command')], ['secrets', L('Valori segreti del repository', 'The repository’s secret values')]], L('Le parti di un workflow.', 'The parts of a workflow.')),
            dBug(pBug(), ['steps:', '  - uses: actions/checkout@v5', '  - run: ./pubblica.sh', '    env:', '      TOKEN: abc123segreto'], 4, '      TOKEN: ${{ secrets.TOKEN_PUBBLICAZIONE }}', L('Un segreto scritto nel file lo legge chiunque veda il repository.', 'A secret written in the file can be read by anyone who sees the repository.')),
        ]),
    ],
];
