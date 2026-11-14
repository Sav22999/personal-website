<?php
/** Git and GitHub: repositories and commits, branches and merges, remotes and pull requests, fixing mistakes. */

return [
    'id' => 'git', 'family' => 'git', 'after' => null,
    'title' => L('Git e GitHub', 'Git and GitHub'), 'short' => L('Git', 'Git'), 'level' => L('Base', 'Basics'),
    'desc' => L('Tenere la storia del codice e lavorare in squadra: repository e commit, branch e merge, remote e pull request, rimediare agli errori.', 'Keeping code history and working in a team: repositories and commits, branches and merges, remotes and pull requests, fixing mistakes.'),
    'lessons' => [
        lesson('commits', L('Repository e commit', 'Repositories and commits'), [
            P('**Git** registra la storia di un progetto in **commit**: istantanee con un messaggio, un autore e una data. I file passano per tre zone: la **cartella di lavoro** (dove modifichi), l’**area di stage** (cosa entrerà nel prossimo commit) e il **repository** (la storia).', '**Git** records a project’s history in **commits**: snapshots with a message, an author and a date. Files move through three areas: the **working directory** (where you edit), the **staging area** (what goes into the next commit) and the **repository** (the history).'),
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
            C('git switch -c ricerca-corsi      # crea e passa al branch
# … modifiche e commit …
git switch main
git merge ricerca-corsi
git branch -d ricerca-corsi       # cancella il branch unito', 'bash'),
            P('Se due branch hanno cambiato le stesse righe, Git segnala un **conflitto**: nei file compaiono i segni `<<<<<<<`, `=======` e `>>>>>>>`. Scegli il contenuto giusto, togli i segni, poi `git add` e `git commit`.', 'If two branches changed the same lines, Git reports a **conflict**: the files show `<<<<<<<`, `=======` and `>>>>>>>` markers. Pick the right content, remove the markers, then `git add` and `git commit`.'),
        ], [
            Q('b1', L('Che cosa fa `git switch -c nome`?', 'What does `git switch -c name` do?'), [L('Crea un branch e ci passa', 'Creates a branch and switches to it'), L('Cancella un branch', 'Deletes a branch'), L('Fa il merge', 'Merges'), L('Clona un repository', 'Clones a repository')], 0, L('-c = create.', '-c = create.')),
            Q('b2', L('Quando nasce un conflitto?', 'When does a conflict arise?'), [L('Quando due branch cambiano le stesse righe', 'When two branches change the same lines'), L('A ogni merge', 'On every merge'), L('Quando il disco è pieno', 'When the disk is full'), L('Mai', 'Never')], 0, L('Git non sa quale versione scegliere.', 'Git cannot pick a version.')),
            Q('b3', L('Che cosa fai dopo aver risolto un conflitto?', 'What do you do after resolving a conflict?'), [L('git add e git commit', 'git add and git commit'), L('git init', 'git init'), L('Niente', 'Nothing'), L('Cancelli il repository', 'Delete the repository')], 0, L('Il merge si completa.', 'The merge completes.')),
        ], task('bash', L('Crea e passa al branch `nuovo-menu`, poi torna su `main` e fai il merge di `nuovo-menu`.', 'Create and switch to the branch `nuovo-menu`, then go back to `main` and merge `nuovo-menu`.'), '', [re('git\s+(switch\s+-c|checkout\s+-b)\s+nuovo-menu', L('Crei il branch', 'You create the branch')), re('git\s+(switch|checkout)\s+main', L('Torni su main', 'You go back to main')), re('git\s+merge\s+nuovo-menu', L('Fai il merge', 'You merge'))], '', "git switch -c nuovo-menu\ngit switch main\ngit merge nuovo-menu"), [
            dOrder(pSort(), ['git switch -c fix-login', 'git commit -am "Correggi il login"', 'git switch main', 'git merge fix-login', 'git branch -d fix-login'], L('Branch, lavoro, ritorno, merge, pulizia.', 'Branch, work, return, merge, cleanup.')),
            dFlash(pFlash(), [[L('Un branch permette di lavorare senza toccare main', 'A branch lets you work without touching main'), true], [L('Ogni merge produce un conflitto', 'Every merge produces a conflict'), false], [L('I segni <<<<<<< indicano un conflitto', '<<<<<<< markers indicate a conflict'), true], [L('Un branch cancellato perde i commit già uniti a main', 'A deleted branch loses commits already merged into main'), false]], L('Rami e unioni.', 'Branches and merges.')),
        ]),
        lesson('remote', L('GitHub e pull request', 'GitHub and pull requests'), [
            P('Un **remote** è una copia del repository su un server come **GitHub**. `clone` scarica un repository, `push` invia i tuoi commit, `pull` scarica e unisce quelli degli altri.', 'A **remote** is a copy of the repository on a server like **GitHub**. `clone` downloads a repository, `push` sends your commits, `pull` downloads and merges others’.'),
            C('git clone https://github.com/esempio/progetto.git
git switch -c pagina-contatti
# … commit …
git push -u origin pagina-contatti   # pubblica il branch
git pull                             # aggiorna con le modifiche degli altri', 'bash'),
            P('Su GitHub si apre una **pull request** (PR): la proposta di unire il tuo branch in `main`. I colleghi leggono il codice, commentano, i test automatici girano; quando tutto va bene si fa il merge. Così il codice si rivede prima di entrare.', 'On GitHub you open a **pull request** (PR): the proposal to merge your branch into `main`. Colleagues read the code, comment, automated tests run; when all is well it is merged. That way code is reviewed before it lands.'),
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
    ],
];
