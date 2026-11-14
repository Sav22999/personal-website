<?php
/** The terminal and Bash: navigating and files, pipes and redirection, permissions and processes, scripts. */

return [
    'id' => 'bash', 'family' => 'bash', 'after' => null,
    'title' => L('Terminale Linux e Bash', 'Linux Terminal and Bash'), 'short' => L('Bash', 'Bash'), 'level' => L('Base', 'Basics'),
    'desc' => L('Usare il terminale senza paura: muoversi tra cartelle e file, pipe e redirezioni, permessi e processi, i primi script Bash.', 'Using the terminal without fear: moving through folders and files, pipes and redirection, permissions and processes, your first Bash scripts.'),
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
            C('cat accessi.log | grep "404" | wc -l          # quante pagine non trovate?
grep -i "errore" app.log > errori.txt          # salva le righe con "errore"
echo "Backup completato" >> registro.txt       # aggiunge una riga
sort nomi.txt | uniq -c | sort -nr | head -5   # i 5 nomi più frequenti
find . -name "*.php" | xargs grep -l "TODO"     # file PHP con TODO', 'bash'),
            P('Comandi da conoscere: `grep` cerca testo, `wc -l` conta righe, `sort` ordina, `uniq` toglie doppioni consecutivi, `head`/`tail` mostrano inizio e fine (`tail -f` segue un log in tempo reale), `find` cerca file.', 'Commands to know: `grep` searches text, `wc -l` counts lines, `sort` sorts, `uniq` removes consecutive duplicates, `head`/`tail` show start and end (`tail -f` follows a log live), `find` searches files.'),
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
            P('Prima di un comando distruttivo (come `-delete` o `sed -i`) provalo **senza** l’opzione per vedere su cosa agisce. Le virgolette proteggono spazi e caratteri speciali.', 'Before a destructive command (like `-delete` or `sed -i`) try it **without** the option to see what it acts on. Quotes protect spaces and special characters.'),
        ], [
            Q('tt1', L('Quale comando trova righe che contengono un testo?', 'Which command finds lines containing some text?'), ['grep', 'find', 'cut', 'mv'], 0, L('`-r` per cercare nelle cartelle.', '`-r` to search folders.')),
            Q('tt2', L('Che cosa fa `uniq -c` dopo `sort`?', 'What does `uniq -c` do after `sort`?'), [L('Conta le righe uguali', 'Counts identical lines'), L('Cancella il file', 'Deletes the file'), L('Ordina al contrario', 'Sorts in reverse'), L('Unisce due file', 'Joins two files')], 0, L('Serve l’ordinamento prima.', 'It needs sorting first.')),
            Q('tt3', L('Come provi un `find … -delete` in sicurezza?', 'How do you try a `find … -delete` safely?'), [L('Prima senza `-delete`', 'First without `-delete`'), L('Con sudo', 'With sudo'), L('Due volte di fila', 'Twice in a row'), L('Non si può', 'You cannot')], 0, L('Vedi cosa verrebbe cancellato.', 'You see what would be deleted.')),
        ], task('bash', L('Cerca in modo ricorsivo `password` nella cartella `config/`, mostrando i numeri di riga.', 'Recursively search for `password` in the `config/` folder, showing line numbers.'), '', [re('grep\s+(-rn|-nr|-r\s+-n|-n\s+-r)\s+["\']?password["\']?\s+config/?', L('La ricerca è giusta', 'The search is right'))], '', 'grep -rn "password" config/'), [
            dMatch(pMatch(), [['grep', L('Trova righe', 'Finds lines')], ['find', L('Trova file', 'Finds files')], ['sed', L('Sostituisce', 'Replaces')], ['awk', L('Lavora per colonne', 'Works by columns')], ['uniq -c', L('Conta le ripetizioni', 'Counts repeats')]], L('Gli attrezzi del testo.', 'Text tools.')),
            dOrder(pSort(), ['cut -d, -f3 ordini.csv', '| sort', '| uniq -c', '| sort -rn', '| head'], L('Estrai, ordina, conta, classifica, mostra i primi.', 'Extract, sort, count, rank, show the top.')),
        ]),
    ],
];
