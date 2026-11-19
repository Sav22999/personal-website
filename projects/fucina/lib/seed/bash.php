<?php
/** The terminal and Bash: navigating and files, pipes and redirection, permissions and processes, scripts, text tools,
 *  environment variables and PATH, exit codes and conditions, functions and case, SSH, transfers and archives. */

/** The folder tree from the root to the current folder, with the absolute path highlighted and the shortcuts ~ . .. */
if (!function_exists('fig_bash_tree')) {
    function fig_bash_tree(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="ui-monospace, Menlo, monospace" font-size="14" fill="#1E1C52">
<g fill="none" stroke-width="2">
<path d="M36,52 V272 M36,96 H64 M36,138 H64 M36,272 H64" stroke="#8A87B0"/>
<path d="M36,52 V138 H64" stroke="#E8590C" stroke-width="3"/>
<path d="M90,152 V182 H118 M144,196 V226 H172 M198,238 V256 H226" stroke="#E8590C" stroke-width="3"/>
</g>
<g stroke-width="2">
<rect x="16" y="26" width="40" height="28" rx="8" fill="#FFE8D6" stroke="#E8590C"/><text x="36" y="45" text-anchor="middle" font-weight="700">/</text>
<rect x="64" y="82" width="56" height="28" rx="8" fill="#F1F0F8" stroke="#8A87B0"/><text x="92" y="101" text-anchor="middle">etc</text>
<rect x="64" y="124" width="64" height="28" rx="8" fill="#FFE8D6" stroke="#E8590C"/><text x="96" y="143" text-anchor="middle" font-weight="700">home</text>
<rect x="118" y="168" width="56" height="28" rx="8" fill="#FFE8D6" stroke="#E8590C"/><text x="146" y="187" text-anchor="middle" font-weight="700">ada</text>
<rect x="172" y="212" width="96" height="28" rx="8" fill="#FFE8D6" stroke="#E8590C"/><text x="220" y="231" text-anchor="middle" font-weight="700">progetti</text>
<rect x="226" y="244" width="84" height="28" rx="8" fill="#E8590C" stroke="#E8590C"/><text x="268" y="263" text-anchor="middle" font-weight="700" fill="#FFFFFF">fucina</text>
<rect x="64" y="258" width="56" height="28" rx="8" fill="#F1F0F8" stroke="#8A87B0"/><text x="92" y="277" text-anchor="middle">var</text>
</g>
<g font-weight="700"><text x="184" y="187" fill="#7048E8">~</text><text x="278" y="231" fill="#2F8CF0">..</text><text x="320" y="263" fill="#0B7F5C">.</text></g>
<rect x="356" y="24" width="268" height="92" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="372" y="52" font-weight="700">$ pwd</text>
<text x="372" y="80" font-size="13">/home/ada/progetti/fucina</text>
<text x="372" y="102" font-size="13" fill="#B84508">/ → home → ada → …</text>
<g font-size="14">
<text x="372" y="162"><tspan font-weight="700" fill="#7048E8">~ </tspan> = /home/ada</text>
<text x="372" y="200"><tspan font-weight="700" fill="#2F8CF0">..</tspan> = /home/ada/progetti</text>
<text x="372" y="238"><tspan font-weight="700" fill="#0B7F5C">. </tspan> = /home/ada/progetti/fucina</text>
</g>
</svg>
SVG;
    }
}

/** A pipeline: each command's output becomes the next one's input, the lines get fewer at every step and travel along the pipes. */
if (!function_exists('fig_bash_pipe')) {
    function fig_bash_pipe(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 250" font-family="ui-monospace, Menlo, monospace" font-size="14" fill="#1E1C52">
<style>.pk{opacity:0;animation:flow 3s linear infinite}.pk2{animation-delay:1.5s}@keyframes flow{0%{transform:translateX(0);opacity:0}15%{opacity:1}85%{opacity:1}100%{transform:translateX(34px);opacity:0}}@media (prefers-reduced-motion:reduce){.pk{animation:none}}</style>
<g stroke-width="2">
<rect x="16" y="20" width="180" height="44" rx="10" fill="#FFF3EA" stroke="#E8590C"/><text x="106" y="47" text-anchor="middle" font-weight="700">cat accessi.log</text>
<rect x="246" y="20" width="170" height="44" rx="10" fill="#DCEBFF" stroke="#2F8CF0"/><text x="331" y="47" text-anchor="middle" font-weight="700">grep "404"</text>
<rect x="466" y="20" width="158" height="44" rx="10" fill="#D5F6EA" stroke="#0B7F5C"/><text x="545" y="47" text-anchor="middle" font-weight="700">wc -l</text>
</g>
<g stroke="#1E1C52" stroke-width="3"><line x1="196" y1="42" x2="246" y2="42"/><line x1="416" y1="42" x2="466" y2="42"/></g>
<g font-weight="700" font-size="17" text-anchor="middle"><text x="221" y="34">|</text><text x="441" y="34">|</text></g>
<g fill="#1E1C52"><rect class="pk" x="202" y="38" width="10" height="8" rx="2"/><rect class="pk pk2" x="202" y="38" width="10" height="8" rx="2"/>
<rect class="pk" x="422" y="38" width="10" height="8" rx="2"/></g>
<g stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 4"><line x1="106" y1="64" x2="106" y2="84"/><line x1="331" y1="64" x2="331" y2="84"/><line x1="545" y1="64" x2="545" y2="84"/></g>
<g stroke-width="2"><rect x="16" y="84" width="180" height="150" rx="10" fill="#FFFFFF" stroke="#E8590C"/>
<rect x="246" y="84" width="170" height="150" rx="10" fill="#FFFFFF" stroke="#2F8CF0"/>
<rect x="466" y="84" width="158" height="150" rx="10" fill="#FFFFFF" stroke="#0B7F5C"/></g>
<g font-size="13">
<text x="32" y="112">GET /     200</text>
<rect x="26" y="125" width="160" height="22" rx="6" fill="#FFE3EE"/><text x="32" y="141">GET /prova 404</text>
<text x="32" y="166">GET /blog 200</text>
<rect x="26" y="179" width="160" height="22" rx="6" fill="#FFE3EE"/><text x="32" y="195">GET /vecchia 404</text>
<text x="32" y="220">GET /info 301</text>
<rect x="256" y="98" width="150" height="22" rx="6" fill="#FFE3EE"/><text x="262" y="114">GET /prova 404</text>
<rect x="256" y="128" width="150" height="22" rx="6" fill="#FFE3EE"/><text x="262" y="144">GET /vecchia 404</text>
</g>
<text x="545" y="172" text-anchor="middle" font-size="40" font-weight="700" fill="#0B7F5C">2</text>
</svg>
SVG;
    }
}

/** The permission string -rwxr-xr-- split into owner, group and others, with the numbers that make chmod 754. */
if (!function_exists('fig_bash_perm')) {
    function fig_bash_perm(array $t): string
    {
        $cells = '';
        foreach (str_split('-rwxr-xr--') as $i => $ch) {
            [$f, $s] = $i === 0 ? ['#F1F0F8', '#8A87B0'] : ($i < 4 ? ['#FFE8D6', '#E8590C'] : ($i < 7 ? ['#DCEBFF', '#2F8CF0'] : ['#D5F6EA', '#0B7F5C']));
            $x = 80 + $i * 48;
            $cells .= "<rect x=\"$x\" y=\"52\" width=\"44\" height=\"52\" rx=\"8\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"/><text x=\"" . ($x + 22) . "\" y=\"86\" text-anchor=\"middle\" font-size=\"24\" font-weight=\"700\" font-family=\"ui-monospace, Menlo, monospace\">$ch</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<text x="320" y="32" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14">r = 4 · w = 2 · x = 1</text>
{$cells}
<g fill="none" stroke-width="2"><path d="M130,114 V122 H266 V114" stroke="#E8590C"/><path d="M274,114 V122 H410 V114" stroke="#2F8CF0"/><path d="M418,114 V122 H554 V114" stroke="#0B7F5C"/><path d="M102,114 V150" stroke="#8A87B0"/></g>
<g font-weight="700" text-anchor="middle"><text x="198" y="146" fill="#B84508">{$t[0]}</text><text x="342" y="146" fill="#1B66BC">{$t[1]}</text><text x="486" y="146" fill="#0B7F5C">{$t[2]}</text></g>
<text x="102" y="170" text-anchor="middle" font-size="13">{$t[3]}</text>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" text-anchor="middle"><text x="198" y="180">4+2+1 = 7</text><text x="342" y="180">4+0+1 = 5</text><text x="486" y="180">4+0+0 = 4</text></g>
<rect x="240" y="206" width="160" height="44" rx="12" fill="#1E1C52"/>
<text x="320" y="234" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="17" font-weight="700" fill="#FFFFFF">chmod 754</text>
</svg>
SVG;
    }
}

/** How the shell finds a command: it looks in the PATH folders in order and runs the first match. */
if (!function_exists('fig_bash_path')) {
    function fig_bash_path(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.look{animation:look 6s ease-in-out infinite}@keyframes look{0%,20%{transform:translateX(-420px)}33%,53%{transform:translateX(-210px)}66%,100%{transform:translateX(0)}}@media (prefers-reduced-motion:reduce){.look{animation:none}}</style>
<g font-family="ui-monospace, Menlo, monospace"><text x="16" y="34" font-size="15" font-weight="700">$ python3</text>
<text x="16" y="66" font-size="14">PATH=<tspan fill="#B84508">/home/ada/bin</tspan>:<tspan fill="#1B66BC">/usr/local/bin</tspan>:<tspan fill="#0B7F5C">/usr/bin</tspan></text></g>
<g stroke-width="2"><rect x="16" y="96" width="188" height="100" rx="12" fill="#F1F0F8" stroke="#8A87B0"/><rect x="226" y="96" width="188" height="100" rx="12" fill="#F1F0F8" stroke="#8A87B0"/><rect x="436" y="96" width="188" height="100" rx="12" fill="#D5F6EA" stroke="#0B7F5C"/></g>
<rect class="look" x="432" y="92" width="196" height="108" rx="14" fill="none" stroke="#E8590C" stroke-width="3"/>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700" text-anchor="middle"><text x="110" y="132">/home/ada/bin</text><text x="320" y="132">/usr/local/bin</text><text x="530" y="132">/usr/bin</text></g>
<g text-anchor="middle"><text x="110" y="170">1 · {$t[0]}</text><text x="320" y="170">2 · {$t[0]}</text><text x="530" y="170" font-weight="700" fill="#0B7F5C">3 · {$t[1]}</text></g>
<g stroke="#1E1C52" stroke-width="2"><path d="M206,146 H222 M216,140 L222,146 L216,152" fill="none"/><path d="M416,146 H432 M426,140 L432,146 L426,152" fill="none"/></g>
<text x="320" y="220" text-anchor="middle" font-size="13">{$t[2]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'bash', 'family' => 'bash', 'after' => null,
    'title' => L('Terminale Linux e Bash', 'Linux Terminal and Bash'), 'short' => L('Bash', 'Bash'), 'level' => L('Base', 'Basics'),
    'desc' => L('Usare il terminale senza paura: muoversi tra cartelle e file, pipe e redirezioni, permessi e processi, variabili e PATH, script Bash con condizioni e funzioni, SSH e archivi.', 'Using the terminal without fear: moving through folders and files, pipes and redirection, permissions and processes, variables and PATH, Bash scripts with conditions and functions, SSH and archives.'),
    'lessons' => [
        lesson('navigate', L('Cartelle e file', 'Folders and files'), [
            P('Il **terminale** dà ordini al computer scrivendo comandi. **Bash** (e il simile Zsh su macOS) è la shell più diffusa su Linux e sui server. Sei sempre “dentro” una cartella: quella corrente.', 'The **terminal** gives the computer orders by typing commands. **Bash** (and the similar Zsh on macOS) is the most common shell on Linux and servers. You are always “inside” a folder: the current one.'),
            C('pwd                    # dove sono?
ls -la                 # elenco completo, anche file nascosti
cd progetti/fucina     # entra in una cartella
cd ..                  # sale di un livello
cd ~                   # torna alla home
mkdir -p dati/2026     # crea cartelle (anche intermedie)
cp nota.txt copia.txt  # copia
mv copia.txt archivio/ # sposta o rinomina
rm vecchio.log         # cancella (niente cestino!)', 'bash'),
            P('`rm` cancella **definitivamente**: niente cestino. Con `rm -r` cancella cartelle intere, quindi controlla due volte il percorso. Il tasto **Tab** completa nomi di file e comandi.', '`rm` deletes **permanently**: no recycle bin. With `rm -r` it deletes whole folders, so double-check the path. The **Tab** key completes file and command names.'),
            P('Un **percorso** dice dove si trova un file. Quello **assoluto** parte dalla radice `/` (`/home/ada/progetti`) e funziona da qualsiasi cartella; quello **relativo** parte dalla cartella corrente (`progetti/fucina`). Tre scorciatoie: `~` è la tua home, `.` la cartella corrente, `..` quella sopra. Se un comando risponde «No such file or directory», controlla prima con `pwd` dove ti trovi.', 'A **path** says where a file is. An **absolute** path starts from the root `/` (`/home/ada/progetti`) and works from any folder; a **relative** one starts from the current folder (`progetti/fucina`). Three shortcuts: `~` is your home, `.` the current folder, `..` the one above. If a command answers “No such file or directory”, first check with `pwd` where you are.'),
            Fig(fig_bash_tree(),
                L('Un albero di cartelle: dalla radice / partono etc, home e var; dentro home c’è ada, dentro ada progetti, dentro progetti fucina. Il percorso da / a fucina è evidenziato. A destra: pwd stampa /home/ada/progetti/fucina; ~ vale /home/ada, .. vale /home/ada/progetti, . vale /home/ada/progetti/fucina.', 'A folder tree: from the root / come etc, home and var; inside home is ada, inside ada progetti, inside progetti fucina. The path from / to fucina is highlighted. On the right: pwd prints /home/ada/progetti/fucina; ~ means /home/ada, .. means /home/ada/progetti, . means /home/ada/progetti/fucina.'),
                L('Il percorso assoluto segue l’albero dalla radice fino alla cartella corrente.', 'The absolute path follows the tree from the root down to the current folder.')),
        ], [
            Q('n1', L('Quale comando mostra la cartella corrente?', 'Which command shows the current folder?'), ['pwd', 'ls', 'cd', 'where'], 0, L('print working directory.', 'print working directory.')),
            Q('n2', L('Che cosa fa `cd ..`?', 'What does `cd ..` do?'), [L('Sale di un livello', 'Goes up one level'), L('Torna alla home', 'Goes home'), L('Cancella la cartella', 'Deletes the folder'), L('Elenca i file', 'Lists files')], 0, L('`..` è la cartella genitore.', '`..` is the parent folder.')),
            Q('n3', L('Dove finiscono i file cancellati con `rm`?', 'Where do files deleted with `rm` go?'), [L('Da nessuna parte: sono cancellati', 'Nowhere: they are gone'), L('Nel cestino', 'In the bin'), L('In una cartella nascosta', 'In a hidden folder'), L('Sul server', 'On the server')], 0, L('Prudenza con rm -r.', 'Careful with rm -r.')),
        ], task('bash', L('Crea la cartella `backup`, copia `dati.csv` dentro di essa e poi elenca il suo contenuto con `ls`.', 'Create the folder `backup`, copy `dati.csv` into it and then list its contents with `ls`.'), '', [re('mkdir\s+(-p\s+)?backup', L('Crei la cartella', 'You create the folder')), re('cp\s+dati\.csv\s+backup/?', L('Copi il file', 'You copy the file')), re('ls(\s+-\w+)*\s+backup', L('Elenchi la cartella', 'You list the folder'))], '', "mkdir backup\ncp dati.csv backup/\nls backup"), [
            dMatch(pMatch(), [['pwd', L('Cartella corrente', 'Current folder')], ['ls -la', L('Elenco completo', 'Full listing')], ['cd ~', L('Vai alla home', 'Go home')], ['mkdir -p', L('Crea cartelle', 'Creates folders')], ['mv', L('Sposta o rinomina', 'Moves or renames')]], L('Muoversi nel file system.', 'Moving through the file system.')),
            dFlash(pFlash(), [[L('Il tasto Tab completa i nomi', 'The Tab key completes names'), true], [L('rm sposta i file nel cestino', 'rm moves files to the bin'), false], [L('mv può rinominare un file', 'mv can rename a file'), true], [L('cd .. entra in una sottocartella', 'cd .. enters a subfolder'), false]], L('Le basi del terminale.', 'Terminal basics.')),
        ]),
        lesson('pipes', L('Pipe e redirezioni', 'Pipes and redirection'), [
            P('La forza del terminale è combinare piccoli comandi. La **pipe** `|` passa l’uscita di un comando all’ingresso del successivo. `>` scrive l’uscita in un file (sovrascrivendo), `>>` la aggiunge in fondo.', 'The terminal’s strength is combining small commands. The **pipe** `|` passes one command’s output to the next one’s input. `>` writes output to a file (overwriting), `>>` appends it.'),
            Fig(fig_bash_pipe(),
                L('Tre comandi collegati da pipe: cat accessi.log, grep "404" e wc -l. Sotto ciascuno la sua uscita: cat stampa cinque righe di log, grep tiene solo le due con 404, wc -l stampa il numero 2.', 'Three commands joined by pipes: cat accessi.log, grep "404" and wc -l. Under each one its output: cat prints five log lines, grep keeps only the two with 404, wc -l prints the number 2.'),
                L('Ogni comando riceve l’uscita del precedente e la passa, trasformata, al successivo.', 'Each command receives the previous one’s output and passes it on, transformed, to the next.')),
            C('cat accessi.log | grep "404" | wc -l          # quante pagine non trovate?
grep -i "errore" app.log > errori.txt          # salva le righe con "errore"
echo "Backup completato" >> registro.txt       # aggiunge una riga
sort nomi.txt | uniq -c | sort -nr | head -5   # i 5 nomi più frequenti
find . -name "*.php" | xargs grep -l "TODO"     # file PHP con TODO', 'bash'),
            P('Comandi da conoscere: `grep` cerca testo, `wc -l` conta righe, `sort` ordina, `uniq` toglie doppioni consecutivi, `head`/`tail` mostrano inizio e fine (`tail -f` segue un log in tempo reale), `find` cerca file.', 'Commands to know: `grep` searches text, `wc -l` counts lines, `sort` sorts, `uniq` removes consecutive duplicates, `head`/`tail` show start and end (`tail -f` follows a log live), `find` searches files.'),
            P('Ogni comando ha tre canali: l’**ingresso** standard, l’**uscita** standard e l’**errore** standard (il canale 2). La pipe e `>` portano solo l’uscita, per questo i messaggi d’errore compaiono ancora a schermo: con `2>` li mandi in un file (`comando 2> errori.txt`), con `2>&1` li unisci all’uscita. È l’idea di Unix: tanti piccoli attrezzi, ognuno fa una cosa sola e bene, collegati in catena.', 'Every command has three channels: standard **input**, standard **output** and standard **error** (channel 2). The pipe and `>` carry only the output, which is why error messages still appear on screen: with `2>` you send them to a file (`command 2> errors.txt`), with `2>&1` you join them to the output. It is the Unix idea: many small tools, each doing one thing well, chained together.'),
        ], [
            Q('p1', L('Che cosa fa la pipe `|`?', 'What does the pipe `|` do?'), [L('Passa l’uscita al comando successivo', 'Passes output to the next command'), L('Divide due file', 'Splits two files'), L('Commenta la riga', 'Comments the line'), L('Esegue in parallelo', 'Runs in parallel')], 0, L('Piccoli comandi, grandi risultati.', 'Small commands, big results.')),
            Q('p2', L('Che differenza c’è tra `>` e `>>`?', 'What is the difference between `>` and `>>`?'), [L('> sovrascrive, >> aggiunge', '> overwrites, >> appends'), L('Nessuna', 'None'), L('>> cancella il file', '>> deletes the file'), L('> legge un file', '> reads a file')], 0, L('Attenzione a non sovrascrivere.', 'Careful not to overwrite.')),
            Q('p3', L('Quale comando segue un log mentre cresce?', 'Which command follows a log as it grows?'), ['tail -f', 'head -n', 'cat -f', 'grep -f'], 0, L('Ctrl+C per uscire.', 'Ctrl+C to exit.')),
        ], task('bash', L('Conta quante righe di `server.log` contengono `ERROR`, usando `grep` e `wc -l` con una pipe.', 'Count how many lines of `server.log` contain `ERROR`, using `grep` and `wc -l` with a pipe.'), '', [re('grep\s+(-\w+\s+)*["\']?ERROR["\']?\s+server\.log\s*\|\s*wc\s+-l|cat\s+server\.log\s*\|\s*grep\s+(-\w+\s+)*["\']?ERROR["\']?\s*\|\s*wc\s+-l', L('grep e wc con una pipe', 'grep and wc with a pipe'))], '', 'grep "ERROR" server.log | wc -l'), [
            dMatch(pMatch(), [['grep', L('Cerca testo', 'Searches text')], ['wc -l', L('Conta righe', 'Counts lines')], ['sort', L('Ordina', 'Sorts')], ['uniq -c', L('Conta i ripetuti', 'Counts repeats')], ['find', L('Cerca file', 'Finds files')]], L('I mattoncini del terminale.', 'The terminal’s building blocks.')),
            dBlanks(pFill(), "grep \"timeout\" app.log ___ wc -l\necho \"fine\" ___ registro.txt", ['|', '>>'], ['&', '<', '||'], L('Pipe e aggiunta in fondo.', 'Pipe and append.')),
        ]),
        lesson('permissions', L('Permessi e processi', 'Permissions and processes'), [
            P('Ogni file ha permessi di **lettura** (r), **scrittura** (w) ed **esecuzione** (x) per il proprietario, il gruppo e gli altri: `ls -l` li mostra, come `-rwxr-xr--`. `chmod` li cambia; `sudo` esegue un comando come amministratore.', 'Every file has **read** (r), **write** (w) and **execute** (x) permissions for the owner, the group and others: `ls -l` shows them, like `-rwxr-xr--`. `chmod` changes them; `sudo` runs a command as administrator.'),
            P('Leggi la stringa a gruppi di tre, dopo il primo carattere (`-` per un file, `d` per una cartella): `rwx` per il proprietario, `r-x` per il gruppo, `r--` per gli altri. Nella forma numerica ogni lettera vale un numero, r=4, w=2, x=1, da sommare per gruppo: `chmod 754` dà proprio `rwxr-xr--`, e `600` vuol dire `rw-------`.', 'Read the string in groups of three, after the first character (`-` for a file, `d` for a folder): `rwx` for the owner, `r-x` for the group, `r--` for others. In numeric form each letter is worth a number, r=4, w=2, x=1, added up per group: `chmod 754` gives exactly `rwxr-xr--`, and `600` means `rw-------`.'),
            Fig(L(fig_bash_perm(['proprietario', 'gruppo', 'altri', 'tipo']), fig_bash_perm(['owner', 'group', 'others', 'type'])),
                L('La stringa -rwxr-xr-- in dieci caselle. La prima indica il tipo; poi rwx del proprietario, che vale 4+2+1 = 7; r-x del gruppo, 4+0+1 = 5; r-- degli altri, 4+0+0 = 4. In basso: chmod 754.', 'The string -rwxr-xr-- in ten cells. The first shows the type; then rwx for the owner, worth 4+2+1 = 7; r-x for the group, 4+0+1 = 5; r-- for others, 4+0+0 = 4. At the bottom: chmod 754.'),
                L('Tre gruppi, tre cifre: ogni cifra è la somma delle lettere attive.', 'Three groups, three digits: each digit is the sum of the letters switched on.')),
            C('chmod +x deploy.sh        # rende eseguibile
chmod 600 chiave.pem      # solo il proprietario legge e scrive
ps aux | grep node        # processi in esecuzione
top                       # risorse in tempo reale (q per uscire)
kill 12345                # chiede a un processo di terminare
sudo apt install htop     # installa un programma (Debian/Ubuntu)', 'bash'),
            P('Usa `sudo` solo quando serve e solo su comandi di cui sei sicuro. Le chiavi private e i file con segreti devono essere leggibili solo da te (`chmod 600`).', 'Use `sudo` only when needed and only on commands you are sure about. Private keys and files with secrets must be readable only by you (`chmod 600`).'),
        ], [
            Q('m1', L('Che cosa fa `chmod +x script.sh`?', 'What does `chmod +x script.sh` do?'), [L('Lo rende eseguibile', 'Makes it executable'), L('Lo cancella', 'Deletes it'), L('Lo nasconde', 'Hides it'), L('Lo copia', 'Copies it')], 0, L('Poi si lancia con ./script.sh.', 'Then run it with ./script.sh.')),
            Q('m2', L('Che cosa significa `chmod 600`?', 'What does `chmod 600` mean?'), [L('Solo il proprietario legge e scrive', 'Only the owner reads and writes'), L('Tutti possono tutto', 'Everyone can do everything'), L('Nessuno può leggere', 'Nobody can read'), L('Solo esecuzione', 'Execute only')], 0, L('Ideale per le chiavi.', 'Ideal for keys.')),
            Q('m3', L('Quale comando mostra i processi in esecuzione?', 'Which command shows running processes?'), ['ps aux', 'ls -l', 'chmod', 'pwd'], 0, L('Oppure `top`.', 'Or `top`.')),
        ], task('bash', L('Rendi eseguibile `avvia.sh` e lancialo dalla cartella corrente.', 'Make `avvia.sh` executable and run it from the current folder.'), '', [re('chmod\s+(\+x|u\+x|7[0-7][0-7])\s+avvia\.sh', L('Lo rendi eseguibile', 'You make it executable')), re('\./avvia\.sh', L('Lo lanci', 'You run it'))], '', "chmod +x avvia.sh\n./avvia.sh"), [
            dMatch(pMatch(), [['r', L('Lettura', 'Read')], ['w', L('Scrittura', 'Write')], ['x', L('Esecuzione', 'Execute')], ['sudo', L('Come amministratore', 'As administrator')], ['kill', L('Termina un processo', 'Ends a process')]], L('Permessi e processi.', 'Permissions and processes.')),
            dFlash(pFlash(), [[L('Una chiave privata dovrebbe avere permessi 600', 'A private key should have 600 permissions'), true], [L('Conviene usare sudo per ogni comando', 'Using sudo for every command is wise'), false], [L('top mostra le risorse in tempo reale', 'top shows resources live'), true], [L('chmod +x cancella un file', 'chmod +x deletes a file'), false]], L('Potere con prudenza.', 'Power with care.')),
        ]),
        lesson('scripts', L('Script Bash', 'Bash scripts'), [
            P('Uno **script** è un file di comandi da eseguire in sequenza. Inizia con la riga **shebang** `#!/usr/bin/env bash`. Ha variabili (senza spazi attorno a `=`), condizioni, cicli e argomenti (`$1`, `$2`…).', 'A **script** is a file of commands run in sequence. It starts with the **shebang** line `#!/usr/bin/env bash`. It has variables (no spaces around `=`), conditions, loops and arguments (`$1`, `$2`…).'),
            C('#!/usr/bin/env bash
set -euo pipefail                 # fermati al primo errore

cartella="${1:-.}"                # primo argomento, o la cartella corrente
data=$(date +%Y-%m-%d)

if [ ! -d "$cartella" ]; then
  echo "Cartella non trovata: $cartella" >&2
  exit 1
fi

for f in "$cartella"/*.log; do
  gzip -k "$f"
  echo "Compresso $f"
done
echo "Fatto il $data"', 'bash'),
            P('Metti sempre le variabili tra **virgolette** (`"$f"`): così i nomi con spazi non rompono lo script. `set -euo pipefail` ferma lo script al primo errore invece di continuare alla cieca.', 'Always put variables in **quotes** (`"$f"`): that way names with spaces do not break the script. `set -euo pipefail` stops the script at the first error instead of carrying on blindly.'),
            Tip('Per lanciare uno script rendilo eseguibile una volta con `chmod +x backup.sh`, poi avvialo con `./backup.sh logs`: il `./` dice alla shell di cercarlo nella cartella corrente, che non è nel PATH. In alternativa, `bash backup.sh logs`. `$(comando)` mette l’uscita di un comando in una variabile, come `data` qui sopra.', 'To run a script make it executable once with `chmod +x backup.sh`, then start it with `./backup.sh logs`: the `./` tells the shell to look in the current folder, which is not in the PATH. Alternatively, `bash backup.sh logs`. `$(command)` puts a command’s output into a variable, like `data` above.'),
        ], [
            Q('s1', L('Con che riga inizia uno script Bash?', 'What line does a Bash script start with?'), ['#!/usr/bin/env bash', '// bash', '<?bash', '#include bash'], 0, L('La shebang.', 'The shebang.')),
            Q('s2', L('Come si legge il primo argomento?', 'How do you read the first argument?'), ['$1', '$0', 'arg[1]', '%1'], 0, L('`$0` è il nome dello script.', '`$0` is the script name.')),
            Q('s3', L('Perché mettere le variabili tra virgolette?', 'Why quote variables?'), [L('Per gestire nomi con spazi', 'To handle names with spaces'), L('Per colorarle', 'To colour them'), L('Non serve', 'It is not needed'), L('Per renderle costanti', 'To make them constant')], 0, L('`"$f"` invece di `$f`.', '`"$f"` instead of `$f`.')),
        ], task('bash', L('Scrivi uno script con la shebang `#!/usr/bin/env bash` che salva il primo argomento in `nome` e stampa `Ciao, $nome`.', 'Write a script with the `#!/usr/bin/env bash` shebang that stores the first argument in `nome` and prints `Ciao, $nome`.'), '', [re('^#!/usr/bin/env bash', L('La shebang', 'The shebang')), re('^nome="?\$\{?1', L('Leggi il primo argomento', 'You read the first argument')), re('echo\s+"Ciao, \$\{?nome\}?"', L('Stampi il saluto', 'You print the greeting'))], '', "#!/usr/bin/env bash\nnome=\"\$1\"\necho \"Ciao, \$nome\""), [
            dBug(pBug(), ['#!/usr/bin/env bash', 'citta = "Udine"', 'echo "Benvenuto a $citta"'], 1, 'citta="Udine"', L('In Bash niente spazi attorno a `=`.', 'In Bash no spaces around `=`.')),
            dOrder(pSort(), ['#!/usr/bin/env bash', 'set -euo pipefail', 'for f in *.txt; do', '  echo "$f"', 'done'], L('Shebang, sicurezza, ciclo.', 'Shebang, safety, loop.')),
        ]),
        lesson('text-tools', L('Cercare e trasformare testo', 'Searching and transforming text'), [
            P('Il terminale è fortissimo con il testo. `grep` trova le righe che contengono un modello, `find` cerca file per nome o data, `sed` sostituisce, `awk` lavora per colonne, `sort` e `uniq -c` contano le ripetizioni.', 'The terminal is very strong with text. `grep` finds lines containing a pattern, `find` searches files by name or date, `sed` replaces, `awk` works by columns, `sort` and `uniq -c` count repeats.'),
            C('grep -rn "TODO" src/                      # righe con TODO, con numero di riga
find . -name "*.log" -mtime +7 -delete      # cancella log più vecchi di 7 giorni
sed -i "s/vecchio/nuovo/g" config.txt       # sostituisce in tutto il file
awk -F, \'{print $2}\' clienti.csv           # seconda colonna di un CSV
cut -d, -f3 ordini.csv | sort | uniq -c | sort -rn | head', 'bash'),
            P('Prima di un comando distruttivo (come `-delete` o `sed -i`) provalo **senza** l’opzione per vedere su cosa agisce. Le virgolette proteggono spazi e caratteri speciali. Attenzione: sul `sed` di macOS l’opzione si scrive `sed -i \'\'`.', 'Before a destructive command (like `-delete` or `sed -i`) try it **without** the option to see what it acts on. Quotes protect spaces and special characters. Note: with macOS’s `sed` the option is written `sed -i \'\'`.'),
        ], [
            Q('tt1', L('Quale comando trova righe che contengono un testo?', 'Which command finds lines containing some text?'), ['grep', 'find', 'cut', 'mv'], 0, L('`-r` per cercare nelle cartelle.', '`-r` to search folders.')),
            Q('tt2', L('Che cosa fa `uniq -c` dopo `sort`?', 'What does `uniq -c` do after `sort`?'), [L('Conta le righe uguali', 'Counts identical lines'), L('Cancella il file', 'Deletes the file'), L('Ordina al contrario', 'Sorts in reverse'), L('Unisce due file', 'Joins two files')], 0, L('Serve l’ordinamento prima.', 'It needs sorting first.')),
            Q('tt3', L('Come provi un `find … -delete` in sicurezza?', 'How do you try a `find … -delete` safely?'), [L('Prima senza `-delete`', 'First without `-delete`'), L('Con sudo', 'With sudo'), L('Due volte di fila', 'Twice in a row'), L('Non si può', 'You cannot')], 0, L('Vedi cosa verrebbe cancellato.', 'You see what would be deleted.')),
        ], task('bash', L('Cerca in modo ricorsivo `password` nella cartella `config/`, mostrando i numeri di riga.', 'Recursively search for `password` in the `config/` folder, showing line numbers.'), '', [re('grep\s+(-rn|-nr|-r\s+-n|-n\s+-r)\s+["\']?password["\']?\s+config/?', L('La ricerca è giusta', 'The search is right'))], '', 'grep -rn "password" config/'), [
            dMatch(pMatch(), [['grep', L('Trova righe', 'Finds lines')], ['find', L('Trova file', 'Finds files')], ['sed', L('Sostituisce', 'Replaces')], ['awk', L('Lavora per colonne', 'Works by columns')], ['uniq -c', L('Conta le ripetizioni', 'Counts repeats')]], L('Gli attrezzi del testo.', 'Text tools.')),
            dOrder(pSort(), ['cut -d, -f3 ordini.csv', '| sort', '| uniq -c', '| sort -rn', '| head'], L('Estrai, ordina, conta, classifica, mostra i primi.', 'Extract, sort, count, rank, show the top.')),
        ]),
        lesson('env', L('Variabili d’ambiente, PATH e alias', 'Environment variables, PATH and aliases'), [
            P('Una variabile della shell si crea con `nome=valore` e si legge con `$nome`. Con `export` diventa una **variabile d’ambiente**, visibile anche ai programmi che lanci. La più importante è **`PATH`**: l’elenco di cartelle, separate da `:`, dove la shell cerca i comandi che scrivi.', 'A shell variable is created with `name=value` and read with `$name`. With `export` it becomes an **environment variable**, visible to the programs you launch too. The most important is **`PATH`**: the list of folders, separated by `:`, where the shell looks for the commands you type.'),
            Fig(L(fig_bash_path(['non c’è', 'trovato!', 'La shell esegue il primo python3 che trova, nell’ordine del PATH.']), fig_bash_path(['not here', 'found!', 'The shell runs the first python3 it finds, in PATH order.'])),
                L('Il comando python3 e la variabile PATH con tre cartelle. Tre riquadri in fila: /home/ada/bin, non c’è; /usr/local/bin, non c’è; /usr/bin, trovato.', 'The python3 command and the PATH variable with three folders. Three boxes in a row: /home/ada/bin, not here; /usr/local/bin, not here; /usr/bin, found.'),
                L('Per questo mettere `$HOME/bin` all’inizio del PATH fa vincere i tuoi comandi.', 'That is why putting `$HOME/bin` at the start of PATH makes your own commands win.')),
            C('echo "$HOME"                         # la tua cartella home
echo "$PATH"                         # dove si cercano i comandi
export EDITOR=nano                   # visibile ai programmi lanciati
PORTA=8080 npm start                 # variabile solo per questo comando
command -v python3                   # dove si trova un comando
alias ll=\'ls -la\'                    # una scorciatoia
echo \'export PATH="$HOME/bin:$PATH"\' >> ~/.bashrc
source ~/.bashrc                     # ricarica la configurazione', 'bash'),
            P('Quello che scrivi nel terminale vale solo fino alla sua chiusura. Le impostazioni da conservare vanno in **`~/.bashrc`** (Bash) o **`~/.zshrc`** (Zsh, la shell predefinita di macOS), letti a ogni nuovo terminale. Non mettere password in questi file né nella cronologia dei comandi.', 'What you type in the terminal lasts only until you close it. Settings to keep go in **`~/.bashrc`** (Bash) or **`~/.zshrc`** (Zsh, macOS’s default shell), read by every new terminal. Do not put passwords in these files or in the command history.'),
        ], [
            Q('e1', L('Che cosa fa `export`?', 'What does `export` do?'), [L('Salva la variabile su disco per sempre', 'Saves the variable to disk for good'), L('Rende la variabile visibile ai programmi lanciati dalla shell', 'Makes the variable visible to programs launched by the shell'), L('Stampa il valore della variabile', 'Prints the variable’s value'), L('Cancella la variabile dalla sessione', 'Removes the variable from the session')], 1, L('Per renderla permanente va scritta in `~/.bashrc`.', 'To make it permanent write it in `~/.bashrc`.')),
            Q('e2', L('Che cos’è `PATH`?', 'What is `PATH`?'), [L('La cartella in cui si trova il terminale', 'The folder the terminal is in'), L('Il percorso del file di configurazione', 'The path of the configuration file'), L('Il nome dell’utente collegato', 'The name of the signed-in user'), L('L’elenco di cartelle dove la shell cerca i comandi', 'The list of folders where the shell looks for commands')], 3, L('Separate da due punti.', 'Separated by colons.')),
            Q('e3', L('Dove scrivi un alias perché resti anche domani?', 'Where do you write an alias so it is still there tomorrow?'), ['~/.bash_history', '~/.bashrc', '/tmp/alias', '/etc/hosts'], 1, L('Su macOS con Zsh: `~/.zshrc`.', 'On macOS with Zsh: `~/.zshrc`.')),
        ], task('bash', L('Aggiungi `$HOME/bin` all’inizio del `PATH` con `export`, poi crea l’alias `ll` per `ls -la`.', 'Add `$HOME/bin` at the start of `PATH` with `export`, then create the alias `ll` for `ls -la`.'), '', [re('export\s+PATH=["\']?(\$HOME|\$\{HOME\})/bin:\$\{?PATH\}?', L('Il PATH aggiornato', 'The updated PATH')), re('alias\s+ll=["\']ls\s+-la["\']', L('L’alias', 'The alias'))], '', "export PATH=\"\$HOME/bin:\$PATH\"\nalias ll='ls -la'"), [
            dMatch(pMatch(), [['$HOME', L('La cartella home', 'The home folder')], ['$PATH', L('Dove cercare i comandi', 'Where to look for commands')], ['export', L('Passa la variabile ai programmi', 'Passes the variable to programs')], ['alias', L('Una scorciatoia per un comando', 'A shortcut for a command')], ['source', L('Ricarica un file di configurazione', 'Reloads a configuration file')]], L('L’ambiente della shell.', 'The shell environment.')),
            dBlanks(pFill(), "___ PATH=\"\$HOME/bin:\$PATH\"\n___ ll='ls -la'", ['export', 'alias'], ['set', 'let', 'def'], L('export per le variabili, alias per le scorciatoie.', 'export for variables, alias for shortcuts.')),
        ]),
        lesson('exit-codes', L('Codici di uscita e condizioni', 'Exit codes and conditions'), [
            P('Ogni comando finisce con un **codice di uscita**: `0` vuol dire successo, qualsiasi altro numero un errore. La variabile `$?` contiene quello dell’ultimo comando. `&&` esegue il comando successivo **solo se** il precedente è riuscito, `||` **solo se** è fallito.', 'Every command ends with an **exit code**: `0` means success, any other number an error. The `$?` variable holds the last command’s code. `&&` runs the next command **only if** the previous one succeeded, `||` **only if** it failed.'),
            C('mkdir -p backup && cp dati.csv backup/      # copia solo se la cartella c’è
grep -q "ERROR" app.log || echo "Nessun errore"
ls file-che-non-esiste; echo $?              # un numero diverso da 0

if [[ -f config.env ]]; then
  echo "Configurazione trovata"
elif [[ -d config ]]; then
  echo "C’è una cartella config"
else
  echo "Niente configurazione" >&2
  exit 1
fi', 'bash'),
            P('Dentro `[[ … ]]` scrivi i test: `-f` il file esiste, `-d` la cartella esiste, `-z` la stringa è vuota, `==` confronta testi, `-eq` e `-gt` confrontano numeri. Nei tuoi script chiudi con `exit 1` quando qualcosa va storto: così chi li lancia (un altro script, un cron, una pipeline) se ne accorge.', 'Inside `[[ … ]]` you write tests: `-f` the file exists, `-d` the folder exists, `-z` the string is empty, `==` compares text, `-eq` and `-gt` compare numbers. In your scripts end with `exit 1` when something goes wrong: that way whoever runs them (another script, a cron job, a pipeline) notices.'),
        ], [
            Q('x1', L('Quale codice di uscita indica successo?', 'Which exit code means success?'), ['1', '0', '-1', '255'], 1, L('Tutto il resto è un errore.', 'Everything else is an error.')),
            Q('x2', L('Che cosa fa `comando1 && comando2`?', 'What does `command1 && command2` do?'), [L('Esegue il secondo solo se il primo riesce', 'Runs the second only if the first succeeds'), L('Esegue il secondo solo se il primo fallisce', 'Runs the second only if the first fails'), L('Esegue i due comandi in parallelo', 'Runs the two commands in parallel'), L('Passa l’uscita del primo al secondo', 'Passes the first one’s output to the second')], 0, L('`||` fa il contrario.', '`||` does the opposite.')),
            Q('x3', L('Quale test controlla che una cartella esista?', 'Which test checks that a folder exists?'), ['[[ -f dati ]]', '[[ -z dati ]]', '[[ -d dati ]]', '[[ -x dati ]]'], 2, L('d = directory.', 'd = directory.')),
        ], task('bash', L('Scrivi un `if`: se il file `config.env` non esiste, stampa `Manca la configurazione` ed esci con codice `1`.', 'Write an `if`: if the file `config.env` does not exist, print `Manca la configurazione` and exit with code `1`.'), '', [re('if\s+\[\[?\s*!\s+-[fe]\s+"?config\.env"?\s*\]\]?|\[\[?\s*-[fe]\s+"?config\.env"?\s*\]\]?\s*\|\|', L('Controlli il file', 'You check the file')), re('echo\s+["\']Manca la configurazione["\']', L('Il messaggio', 'The message')), re('exit\s+1', L('Esci con errore', 'You exit with an error'))], '', "if [[ ! -f config.env ]]; then\n  echo \"Manca la configurazione\"\n  exit 1\nfi"), [
            dMatch(pMatch(), [['0', L('Successo', 'Success')], ['$?', L('Codice dell’ultimo comando', 'The last command’s code')], ['&&', L('Solo se riesce', 'Only if it succeeds')], ['||', L('Solo se fallisce', 'Only if it fails')], ['-f', L('Il file esiste', 'The file exists')]], L('Riuscito o no?', 'Succeeded or not?')),
            dBug(pBug(), ['#!/usr/bin/env bash', 'if [[ -d "$1" ]] then', '  echo "Cartella trovata"', 'fi'], 1, 'if [[ -d "$1" ]]; then', L('Prima di `then` serve il punto e virgola (o un a capo).', 'Before `then` you need a semicolon (or a newline).')),
        ]),
        lesson('functions', L('Funzioni, case e input', 'Functions, case and input'), [
            P('Una **funzione** raggruppa comandi da riusare: si definisce con `nome() { … }` e si chiama come un comando, `nome arg1 arg2`. Dentro, gli argomenti sono `$1`, `$2`…; `local` tiene una variabile solo nella funzione. `read -r` legge una risposta dell’utente e **`case`** sceglie tra più modelli.', 'A **function** groups commands to reuse: you define it with `name() { … }` and call it like a command, `name arg1 arg2`. Inside, the arguments are `$1`, `$2`…; `local` keeps a variable inside the function. `read -r` reads the user’s answer and **`case`** chooses among several patterns.'),
            C('#!/usr/bin/env bash
set -euo pipefail

log() {
  local livello="$1"; shift
  echo "[$(date +%H:%M:%S)] $livello: $*"
}

read -r -p "Ambiente (dev/prod)? " ambiente
case "$ambiente" in
  dev)  log INFO "Avvio in sviluppo" ;;
  prod) log AVVISO "Avvio in produzione" ;;
  *)    log ERRORE "Ambiente sconosciuto: $ambiente"; exit 1 ;;
esac

server=("web1" "web2" "db1")
for s in "${server[@]}"; do log INFO "Controllo $s"; done', 'bash'),
            P('`$#` è il numero di argomenti, `"$@"` li passa tutti così come sono, `shift` toglie il primo. Una funzione restituisce testo con `echo` e un esito con `return 0` o `return 1`, che si controlla con `if` come per ogni comando.', '`$#` is the number of arguments, `"$@"` passes them all as they are, `shift` removes the first. A function returns text with `echo` and an outcome with `return 0` or `return 1`, which you check with `if` as for any command.'),
        ], [
            Q('fn1', L('Come chiami la funzione `saluta` con l’argomento `Ada`?', 'How do you call the `saluta` function with the argument `Ada`?'), ['saluta("Ada")', 'saluta Ada', 'call saluta Ada', 'saluta -> Ada'], 1, L('Come un comando qualsiasi, senza parentesi.', 'Like any command, without brackets.')),
            Q('fn2', L('Che cosa fa `local` dentro una funzione?', 'What does `local` do inside a function?'), [L('Salva la variabile in un file locale', 'Saves the variable in a local file'), L('Trasforma la variabile in una costante', 'Turns the variable into a constant'), L('Rende la variabile visibile solo nella funzione', 'Makes the variable visible only inside the function'), L('Esporta la variabile agli altri script', 'Exports the variable to other scripts')], 2, L('Così non sovrascrivi variabili esterne.', 'That way you do not overwrite outside variables.')),
            Q('fn3', L('In un `case`, che cosa prende `*)`?', 'In a `case`, what does `*)` catch?'), [L('Solo un asterisco scritto dall’utente', 'Only an asterisk typed by the user'), L('Niente: è un commento', 'Nothing: it is a comment'), L('Il primo caso dell’elenco', 'The first case in the list'), L('Qualsiasi valore non preso dai casi precedenti', 'Any value not caught by the previous cases')], 3, L('È il caso “tutto il resto”.', 'It is the “everything else” case.')),
        ], task('bash', L('Scrivi la funzione `saluta` che stampa `Ciao, ` seguito dal primo argomento, poi chiamala con `Ada`.', 'Write the `saluta` function that prints `Ciao, ` followed by its first argument, then call it with `Ada`.'), '', [re('saluta\s*\(\s*\)\s*\{|function\s+saluta\b', L('La funzione', 'The function')), re('echo\s+"Ciao, \$\{?1\}?"', L('Stampi il saluto', 'You print the greeting')), re('^\s*saluta\s+["\']?Ada["\']?\s*$', L('La chiami', 'You call it'))], '', "saluta() {\n  echo \"Ciao, \$1\"\n}\nsaluta Ada"), [
            dOrder(pSort(), ['conta_righe() {', '  local file="$1"', '  wc -l < "$file"', '}', 'conta_righe dati.csv'], L('Definisci, usa l’argomento, chiudi, chiama.', 'Define, use the argument, close, call.')),
            dMatch(pMatch(), [['$1', L('Il primo argomento', 'The first argument')], ['$#', L('Quanti argomenti', 'How many arguments')], ['"$@"', L('Tutti gli argomenti', 'All the arguments')], ['local', L('Variabile della funzione', 'Function variable')], ['read -r', L('Legge l’input', 'Reads input')]], L('Gli attrezzi degli script.', 'Script tools.')),
        ]),
        lesson('remote', L('SSH, trasferimenti e archivi', 'SSH, transfers and archives'), [
            P('`ssh utente@server` apre un terminale su un’altra macchina, cifrando tutto. Invece della password conviene una **chiave**: la crei con `ssh-keygen`, copi la parte pubblica sul server con `ssh-copy-id` e da lì entri senza digitare la password (sui server si può poi disattivare l’accesso con password).', '`ssh user@server` opens a terminal on another machine, encrypting everything. Instead of a password it is better to use a **key**: you create it with `ssh-keygen`, copy the public part to the server with `ssh-copy-id` and from then on you get in without typing the password (on servers you can then turn off password login).'),
            C('ssh ada@server.esempio.it
ssh-copy-id ada@server.esempio.it                 # entra con la chiave
scp report.pdf ada@server.esempio.it:/home/ada/   # copia un file
rsync -avz --delete sito/ ada@server.esempio.it:/var/www/sito/
tar -czf backup.tar.gz progetti/                  # crea un archivio compresso
tar -xzf backup.tar.gz                            # lo estrae
curl -fsSL https://api.esempio.it/stato           # una richiesta HTTP', 'bash'),
            P('`rsync` trasferisce solo le **differenze**: ideale per backup e pubblicazioni (attenzione alla barra finale: `sito/` copia il contenuto della cartella). In `tar -czf`, `c` crea, `z` comprime con gzip, `f` indica il file; con `x` si estrae. `curl` parla HTTP dal terminale: non eseguire mai `curl … | bash` da siti di cui non ti fidi.', '`rsync` transfers only the **differences**: ideal for backups and deployments (watch the trailing slash: `sito/` copies the folder’s contents). In `tar -czf`, `c` creates, `z` compresses with gzip, `f` names the file; with `x` you extract. `curl` speaks HTTP from the terminal: never run `curl … | bash` from sites you do not trust.'),
        ], [
            Q('rm1', L('Che cosa fa `ssh-copy-id`?', 'What does `ssh-copy-id` do?'), [L('Copia un file qualsiasi sul server', 'Copies any file to the server'), L('Copia la tua chiave pubblica sul server per entrare con la chiave', 'Copies your public key to the server so you sign in with the key'), L('Crea una nuova coppia di chiavi', 'Creates a new key pair'), L('Duplica la sessione SSH aperta', 'Duplicates the open SSH session')], 1, L('Le chiavi si creano con `ssh-keygen`.', 'Keys are created with `ssh-keygen`.')),
            Q('rm2', L('Perché usare `rsync` per una cartella grande aggiornata spesso?', 'Why use `rsync` for a large folder updated often?'), [L('Comprime tutto in un archivio .zip', 'It compresses everything into a .zip archive'), L('Funziona anche senza rete', 'It works without a network'), L('Cancella i file originali dopo la copia', 'It deletes the original files after copying'), L('Trasferisce solo le parti cambiate', 'It transfers only the parts that changed')], 3, L('Molto più veloce dalla seconda volta.', 'Much faster from the second time on.')),
            Q('rm3', L('Che cosa fa `tar -xzf backup.tar.gz`?', 'What does `tar -xzf backup.tar.gz` do?'), [L('Estrae l’archivio compresso', 'Extracts the compressed archive'), L('Crea l’archivio compresso', 'Creates the compressed archive'), L('Mostra il contenuto senza estrarre', 'Lists the contents without extracting'), L('Cifra l’archivio con una password', 'Encrypts the archive with a password')], 0, L('x = extract.', 'x = extract.')),
        ], task('bash', L('Crea l’archivio compresso `sito.tar.gz` della cartella `sito/`, poi copialo con `scp` sul server `ada@esempio.it` nella cartella `/backup/`.', 'Create the compressed archive `sito.tar.gz` of the `sito/` folder, then copy it with `scp` to the server `ada@esempio.it` in the `/backup/` folder.'), '', [re('tar\s+-?(?=[a-z]*c)(?=[a-z]*z)[a-z]*f\s+sito\.tar\.gz\s+sito/?', L('Crei l’archivio', 'You create the archive')), re('scp\s+sito\.tar\.gz\s+ada@esempio\.it:/backup/?', L('Lo copi sul server', 'You copy it to the server'))], '', "tar -czf sito.tar.gz sito/\nscp sito.tar.gz ada@esempio.it:/backup/"), [
            dMatch(pMatch(), [['ssh', L('Terminale su un’altra macchina', 'Terminal on another machine')], ['scp', L('Copia un file via SSH', 'Copies a file over SSH')], ['rsync', L('Sincronizza le differenze', 'Syncs the differences')], ['tar -czf', L('Crea un archivio compresso', 'Creates a compressed archive')], ['curl', L('Richiesta HTTP', 'HTTP request')]], L('Lavorare a distanza.', 'Working remotely.')),
            dFlash(pFlash(), [[L('La chiave privata va copiata sul server', 'The private key must be copied to the server'), false], [L('rsync può copiare solo i file cambiati', 'rsync can copy only the changed files'), true], [L('curl … | bash da un sito sconosciuto è sicuro', 'curl … | bash from an unknown site is safe'), false], [L('tar -xzf estrae un archivio .tar.gz', 'tar -xzf extracts a .tar.gz archive'), true]], L('Chiavi, copie e archivi.', 'Keys, copies and archives.')),
        ]),
    ],
];
