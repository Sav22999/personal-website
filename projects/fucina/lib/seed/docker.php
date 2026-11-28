<?php
/** Docker: images and containers, the Dockerfile, volumes and ports, Docker Compose, light and secure images,
 *  debugging, networks, CMD/ENTRYPOINT/ARG/ENV, Compose healthchecks and profiles, registries and multi-platform builds. */

/** Virtual machines and containers side by side: each VM carries a whole operating system, containers share the host's. */
if (!function_exists('fig_docker_vm')) {
    function fig_docker_vm(array $t): string
    {
        $cols = '';
        foreach (['A', 'B', 'C'] as $i => $a) {
            $x = 20 + $i * 96; $cx = $x + 44; $x2 = 336 + $i * 96; $cx2 = $x2 + 44;
            $cols .= "<rect x=\"$x\" y=\"58\" width=\"88\" height=\"50\" rx=\"8\" fill=\"#DCEBFF\" stroke=\"#2F8CF0\" stroke-width=\"2\"/><text x=\"$cx\" y=\"80\" text-anchor=\"middle\" font-weight=\"700\">App $a</text><text x=\"$cx\" y=\"99\" text-anchor=\"middle\" font-size=\"13\">{$t[2]}</text>"
                   . "<rect x=\"$x\" y=\"114\" width=\"88\" height=\"54\" rx=\"8\" fill=\"#FFE3EE\" stroke=\"#F03E82\" stroke-width=\"2\"/><text x=\"$cx\" y=\"146\" text-anchor=\"middle\" font-size=\"13\" font-weight=\"700\">{$t[3]}</text>"
                   . "<rect x=\"$x2\" y=\"118\" width=\"88\" height=\"50\" rx=\"8\" fill=\"#DCEBFF\" stroke=\"#2F8CF0\" stroke-width=\"2\"/><text x=\"$cx2\" y=\"140\" text-anchor=\"middle\" font-weight=\"700\">App $a</text><text x=\"$cx2\" y=\"159\" text-anchor=\"middle\" font-size=\"13\">{$t[2]}</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<g font-weight="700" font-size="16" text-anchor="middle"><text x="160" y="34">{$t[0]}</text><text x="480" y="34">{$t[1]}</text></g>
<line x1="320" y1="16" x2="320" y2="290" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 5"/>
{$cols}
<g stroke-width="2">
<rect x="20" y="174" width="280" height="32" rx="8" fill="#ECE6FF" stroke="#7048E8"/><rect x="336" y="174" width="280" height="32" rx="8" fill="#D5F6EA" stroke="#0B7F5C"/>
<rect x="20" y="212" width="280" height="32" rx="8" fill="#FFE8D6" stroke="#E8590C"/><rect x="336" y="212" width="280" height="32" rx="8" fill="#FFE8D6" stroke="#E8590C"/>
<rect x="20" y="250" width="280" height="32" rx="8" fill="#F1F0F8" stroke="#8A87B0"/><rect x="336" y="250" width="280" height="32" rx="8" fill="#F1F0F8" stroke="#8A87B0"/>
</g>
<g text-anchor="middle" font-weight="700"><text x="160" y="195">Hypervisor</text><text x="476" y="195">Docker Engine</text>
<text x="160" y="233">{$t[4]}</text><text x="476" y="233">{$t[4]}</text><text x="160" y="271">Hardware</text><text x="476" y="271">Hardware</text></g>
<text x="476" y="80" text-anchor="middle" font-size="13">{$t[5]}</text>
<text x="476" y="100" text-anchor="middle" font-size="13">{$t[6]}</text>
</svg>
SVG;
    }
}

/** The layers of an image, one per Dockerfile instruction: after a code change the lower layers come from cache and only the top ones are rebuilt. */
if (!function_exists('fig_docker_layers')) {
    function fig_docker_layers(array $t): string
    {
        $rows = '';
        foreach (['FROM node:22-slim', 'WORKDIR /app', 'COPY package*.json ./', 'RUN npm ci --omit=dev', 'COPY . .', 'CMD ["node", "server.js"]'] as $i => $ins) {
            $y = 246 - $i * 40; $ty = $y + 22; $top = $i >= 4;
            [$f, $st] = $top ? ['#FFE8D6', '#E8590C'] : ['#D5F6EA', '#0B7F5C'];
            $cls = $top ? ' class="re"' : '';
            $rows .= "<g$cls><rect x=\"16\" y=\"$y\" width=\"330\" height=\"34\" rx=\"8\" fill=\"$f\" stroke=\"$st\" stroke-width=\"2\"/><text x=\"32\" y=\"$ty\" font-family=\"ui-monospace, Menlo, monospace\" font-size=\"14\">$ins</text></g>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 296" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.re{animation:rebuild 5s ease-in-out infinite}@keyframes rebuild{0%,15%{opacity:0;transform:translateX(40px)}35%,100%{opacity:1;transform:translateX(0)}}@media (prefers-reduced-motion:reduce){.re{animation:none}}</style>
{$rows}
<g fill="none" stroke-width="2"><path d="M360,46 H372 V120 H360" stroke="#E8590C"/><path d="M360,126 H372 V280 H360" stroke="#0B7F5C"/></g>
<text x="386" y="80" font-weight="700" fill="#B84508">{$t[0]}</text>
<text x="386" y="102" font-size="13">{$t[1]}</text>
<text x="386" y="218" font-weight="700" fill="#0B7F5C">{$t[2]}</text>
<text x="386" y="240" font-size="13">{$t[3]}</text>
<rect x="386" y="140" width="238" height="40" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="505" y="165" text-anchor="middle" font-size="13">{$t[4]}</text>
</svg>
SVG;
    }
}

/** A container on its host: its writable layer disappears with it, the named volume stays; -p links a host port to the container. */
if (!function_exists('fig_docker_volume')) {
    function fig_docker_volume(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="16" width="608" height="248" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="34" y="44" font-weight="700">{$t[0]}</text>
<rect x="170" y="60" width="240" height="186" rx="12" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="290" y="86" text-anchor="middle" font-weight="700" fill="#1B66BC">container db</text>
<rect x="184" y="100" width="212" height="56" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="290" y="124" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">/var/lib/postgresql</text>
<text x="290" y="144" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">/data</text>
<rect x="184" y="170" width="212" height="62" rx="8" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4"/>
<text x="290" y="196" text-anchor="middle" font-size="13" font-weight="700">{$t[1]}</text>
<text x="290" y="216" text-anchor="middle" font-size="13">{$t[2]}</text>
<path d="M452,96 v100 a68,16 0 0 0 136,0 v-100" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<ellipse cx="520" cy="96" rx="68" ry="16" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="520" y="140" text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace">dati-pg</text>
<text x="520" y="162" text-anchor="middle" font-size="13">volume</text>
<text x="520" y="184" text-anchor="middle" font-size="13" font-weight="700" fill="#0B7F5C">{$t[3]}</text>
<path d="M412,128 H444" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<text x="430" y="116" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700">-v</text>
<rect x="30" y="180" width="96" height="34" rx="8" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="78" y="202" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700">:5432</text>
<path d="M128,197 H162" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<text x="78" y="236" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">-p 5432:5432</text>
</svg>
SVG;
    }
}

/** A user-defined network: the browser reaches only the published API port, the API reaches the database by name, the database stays inside. */
if (!function_exists('fig_docker_net')) {
    function fig_docker_net(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="196" y="24" width="428" height="190" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2" stroke-dasharray="8 5"/>
<text x="214" y="50" font-family="ui-monospace, Menlo, monospace" font-weight="700" fill="#5A35C9">rete-app</text>
<rect x="16" y="94" width="116" height="56" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="74" y="127" text-anchor="middle" font-weight="700">browser</text>
<rect x="232" y="94" width="140" height="56" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="302" y="127" text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace">api</text>
<rect x="460" y="94" width="140" height="56" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="530" y="127" text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace">db</text>
<path d="M134,122 H224" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<text x="164" y="112" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">:3000</text>
<path d="M374,122 H452" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<text x="414" y="112" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">db:5432</text>
<text x="530" y="176" text-anchor="middle" font-size="13">{$t[0]}</text>
<path d="M74,152 V240 H520 V222" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/>
<path d="M506,222 H534" stroke="#F03E82" stroke-width="3"/>
<text x="300" y="258" text-anchor="middle" font-size="13" fill="#B0205A" font-weight="700">{$t[1]}</text>
</svg>
SVG;
    }
}

/** A multi-stage build: the big build stage is thrown away, only dist/ is copied into the small final image. */
if (!function_exists('fig_docker_stages')) {
    function fig_docker_stages(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 280" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="20" width="260" height="226" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2" stroke-dasharray="8 5"/>
<text x="146" y="48" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700">FROM node:22 AS build</text>
<g stroke-width="2" font-size="14" text-anchor="middle">
<rect x="36" y="64" width="220" height="32" rx="8" fill="#FFFFFF" stroke="#8A87B0"/><text x="146" y="85">{$t[0]}</text>
<rect x="36" y="104" width="220" height="32" rx="8" fill="#FFFFFF" stroke="#8A87B0"/><text x="146" y="125" font-family="ui-monospace, Menlo, monospace">node_modules/</text>
<rect x="36" y="144" width="220" height="32" rx="8" fill="#FFFFFF" stroke="#8A87B0"/><text x="146" y="165">{$t[1]}</text>
<rect x="36" y="194" width="220" height="36" rx="8" fill="#FFE8D6" stroke="#E8590C"/><text x="146" y="217" font-weight="700" font-family="ui-monospace, Menlo, monospace">dist/</text>
</g>
<rect x="420" y="120" width="204" height="126" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="522" y="148" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14" font-weight="700">FROM nginx:alpine</text>
<g stroke-width="2" font-size="14" text-anchor="middle">
<rect x="440" y="158" width="164" height="30" rx="8" fill="#FFFFFF" stroke="#0B7F5C"/><text x="522" y="178">nginx</text>
<rect x="440" y="196" width="164" height="36" rx="8" fill="#FFE8D6" stroke="#E8590C"/><text x="522" y="219" font-weight="700" font-family="ui-monospace, Menlo, monospace">dist/</text>
</g>
<path d="M258,212 H432" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<text x="348" y="184" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">COPY</text><text x="348" y="202" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700">--from=build</text>
<g font-size="13" text-anchor="middle"><text x="146" y="268">{$t[2]}</text><text x="522" y="268" font-weight="700" fill="#0B7F5C">{$t[3]}</text></g>
</svg>
SVG;
    }
}

return [
    'id' => 'docker', 'family' => 'docker', 'after' => 'bash',
    'title' => L('Docker', 'Docker'), 'short' => L('Docker', 'Docker'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Impacchettare un’app con tutto quello che le serve: immagini e container, Dockerfile, volumi, porte e reti, più servizi insieme con Docker Compose, diagnosi, registri e immagini sicure.', 'Packaging an app with everything it needs: images and containers, the Dockerfile, volumes, ports and networks, several services together with Docker Compose, debugging, registries and secure images.'),
    'lessons' => [
        lesson('containers', L('Immagini e container', 'Images and containers'), [
            P('Un’**immagine** Docker è un pacchetto con un’app e tutto quello che le serve (sistema minimo, librerie, configurazione). Un **container** è un’immagine in esecuzione, isolata dalle altre. Così l’app gira uguale sul tuo portatile, in test e in produzione: basta con “da me funziona”.', 'A Docker **image** is a package with an app and everything it needs (minimal system, libraries, configuration). A **container** is a running image, isolated from others. That way the app runs the same on your laptop, in testing and in production: no more “works on my machine”.'),
            C('docker pull nginx                     # scarica un’immagine
docker run -d -p 8080:80 --name web nginx   # avvia un container
docker ps                             # container in esecuzione
docker logs web                       # log del container
docker stop web && docker rm web      # ferma e rimuove', 'bash'),
            P('Le immagini pubbliche stanno su registri come **Docker Hub**. Un container è più leggero di una macchina virtuale perché condivide il kernel del sistema ospite.', 'Public images live on registries such as **Docker Hub**. A container is lighter than a virtual machine because it shares the host system’s kernel.'),
            Fig(L(fig_docker_vm(['Macchine virtuali', 'Container', 'e librerie', 'SO della VM', 'SO del computer', 'niente SO proprio:', 'usano il kernel sotto']), fig_docker_vm(['Virtual machines', 'Containers', 'and libraries', 'Guest OS', 'Host OS', 'no OS of their own:', 'they use the kernel below'])),
                L('Due pile a confronto. A sinistra tre macchine virtuali: ognuna ha app, librerie e un sistema operativo completo, sopra hypervisor, sistema del computer e hardware. A destra tre container: solo app e librerie, sopra Docker Engine, sistema del computer e hardware.', 'Two stacks compared. On the left three virtual machines: each has app, libraries and a full operating system, on top of a hypervisor, the host OS and hardware. On the right three containers: only app and libraries, on top of Docker Engine, the host OS and hardware.'),
                L('Ogni VM si porta dietro un sistema operativo intero; i container no, per questo partono in un attimo.', 'Each VM carries a whole operating system; containers do not, which is why they start in a moment.')),
        ], [
            Q('c1', L('Che differenza c’è tra immagine e container?', 'What is the difference between image and container?'), [L('Il container è un’immagine in esecuzione', 'The container is a running image'), L('Sono la stessa cosa', 'They are the same'), L('L’immagine gira, il container no', 'The image runs, the container does not'), L('Il container è un file di testo', 'The container is a text file')], 0, L('Come programma e processo.', 'Like program and process.')),
            Q('c2', L('Che cosa fa `-p 8080:80`?', 'What does `-p 8080:80` do?'), [L('Collega la porta 8080 del computer alla 80 del container', 'Maps host port 8080 to container port 80'), L('Limita la memoria', 'Limits memory'), L('Imposta la password', 'Sets the password'), L('Avvia 80 container', 'Starts 80 containers')], 0, L('Ospite:container.', 'Host:container.')),
            Q('c3', L('Perché un container è più leggero di una macchina virtuale?', 'Why is a container lighter than a virtual machine?'), [L('Condivide il kernel dell’ospite', 'It shares the host kernel'), L('Non ha file', 'It has no files'), L('Gira solo di notte', 'It only runs at night'), L('Non lo è', 'It is not')], 0, L('Niente sistema operativo completo.', 'No full operating system.')),
        ], task('bash', L('Avvia in background un container dell’immagine `nginx` chiamato `sito`, collegando la porta `3000` del computer alla `80` del container.', 'Start in the background a container of the `nginx` image named `sito`, mapping host port `3000` to container port `80`.'), '', [re('docker\s+run\b', L('Avvii un container', 'You start a container')), re('\s-d\b', L('In background', 'In the background')), re('-p\s+3000:80', L('Le porte giuste', 'The right ports')), re('--name\s+sito', L('Il nome', 'The name')), re('nginx\s*$', L('L’immagine nginx', 'The nginx image'))], '', 'docker run -d -p 3000:80 --name sito nginx'), [
            dMatch(pMatch(), [[L('Immagine', 'Image'), L('Pacchetto dell’app', 'App package')], ['Container', L('Immagine in esecuzione', 'Running image')], ['Docker Hub', L('Registro di immagini', 'Image registry')], ['docker ps', L('Container attivi', 'Running containers')], ['docker logs', L('Log del container', 'Container logs')]], L('Il vocabolario Docker.', 'Docker vocabulary.')),
            dFlash(pFlash(), [[L('Un container gira uguale su macchine diverse', 'A container runs the same on different machines'), true], [L('Un container è una macchina virtuale completa', 'A container is a full virtual machine'), false], [L('-d avvia il container in background', '-d runs the container in the background'), true], [L('Le immagini non si possono scaricare', 'Images cannot be downloaded'), false]], L('Impacchettare per portare ovunque.', 'Packaging to take anywhere.')),
        ]),
        lesson('dockerfile', L('Il Dockerfile', 'The Dockerfile'), [
            P('Il **Dockerfile** descrive come costruire un’immagine, passo per passo: da quale immagine partire (`FROM`), che file copiare (`COPY`), che comandi eseguire (`RUN`) e che cosa lanciare all’avvio (`CMD`).', 'The **Dockerfile** describes how to build an image, step by step: which image to start from (`FROM`), which files to copy (`COPY`), which commands to run (`RUN`) and what to launch at startup (`CMD`).'),
            C('FROM node:22-slim
WORKDIR /app
COPY package*.json ./
RUN npm ci --omit=dev
COPY . .
EXPOSE 3000
USER node
CMD ["node", "server.js"]', 'dockerfile'),
            C('docker build -t mia-app:1.0 .
docker run -d -p 3000:3000 mia-app:1.0', 'bash'),
            P('Ogni istruzione crea uno **strato** riusato dalla cache: copia prima i file delle dipendenze e installale, poi il resto del codice, così un cambio al codice non reinstalla tutto. Usa immagini base piccole, un file `.dockerignore` e un utente non root.', 'Each instruction creates a **layer** reused from cache: copy the dependency files first and install them, then the rest of the code, so a code change does not reinstall everything. Use small base images, a `.dockerignore` file and a non-root user.'),
            Fig(L(fig_docker_layers(['ricostruiti', 'dopo il cambio', 'dalla cache', 'pronti in un attimo', 'Hai cambiato solo server.js']), fig_docker_layers(['rebuilt', 'after the change', 'from cache', 'ready in a moment', 'You changed only server.js'])),
                L('Sei strati impilati, uno per istruzione del Dockerfile, da FROM in basso a CMD in alto. Dopo aver cambiato server.js i quattro strati in basso, fino a RUN npm ci, vengono dalla cache; COPY . . e CMD vengono ricostruiti.', 'Six stacked layers, one per Dockerfile instruction, from FROM at the bottom to CMD at the top. After changing server.js the four lower layers, up to RUN npm ci, come from cache; COPY . . and CMD are rebuilt.'),
                L('Un cambio invalida il suo strato e tutti quelli sopra: per questo le dipendenze vanno copiate prima del codice.', 'A change invalidates its layer and all the ones above it: that is why dependencies are copied before the code.')),
        ], [
            Q('d1', L('Quale istruzione indica l’immagine di partenza?', 'Which instruction sets the base image?'), ['FROM', 'RUN', 'CMD', 'COPY'], 0, L('Di solito è la prima riga: prima può esserci solo `ARG`.', 'It is usually the first line: only `ARG` may come before it.')),
            Q('d2', L('Perché copiare prima package.json e poi il codice?', 'Why copy package.json first and then the code?'), [L('Per riusare la cache delle dipendenze', 'To reuse the dependency cache'), L('È obbligatorio', 'It is mandatory'), L('Per ordine alfabetico', 'For alphabetical order'), L('Non conta', 'It does not matter')], 0, L('Build molto più veloci.', 'Much faster builds.')),
            Q('d3', L('Che cosa fa `docker build -t mia-app:1.0 .`?', 'What does `docker build -t mia-app:1.0 .` do?'), [L('Costruisce l’immagine con un nome e una versione', 'Builds the image with a name and version'), L('Avvia un container', 'Starts a container'), L('Cancella le immagini', 'Deletes images'), L('Scarica nginx', 'Downloads nginx')], 0, L('-t = tag.', '-t = tag.')),
        ], task('dockerfile', L('Scrivi un Dockerfile per un’app Python: parti da `python:3.12-slim`, lavora in `/app`, copia `requirements.txt` e installalo con pip, copia il resto e avvia `python app.py`.', 'Write a Dockerfile for a Python app: start from `python:3.12-slim`, work in `/app`, copy `requirements.txt` and install it with pip, copy the rest and run `python app.py`.'), '', [re('^FROM\s+python:3\.12-slim', L('L’immagine base', 'The base image')), re('^WORKDIR\s+/app', L('La cartella di lavoro', 'The working directory')), re('^COPY\s+requirements\.txt\s+\.[\s\S]*^RUN\s+pip\s+install\s+(--no-cache-dir\s+)?-r\s+requirements\.txt[\s\S]*^COPY\s+\.\s+\.', L('Dipendenze prima del codice', 'Dependencies before code')), re('^CMD\s+\[\s*"python"\s*,\s*"app\.py"\s*\]', L('Il comando di avvio', 'The start command'))], '', "FROM python:3.12-slim\nWORKDIR /app\nCOPY requirements.txt .\nRUN pip install --no-cache-dir -r requirements.txt\nCOPY . .\nCMD [\"python\", \"app.py\"]"), [
            dOrder(pSort(), ['FROM node:22-slim', 'WORKDIR /app', 'COPY package*.json ./', 'RUN npm ci', 'COPY . .', 'CMD ["node", "server.js"]'], L('Base, cartella, dipendenze, codice, avvio.', 'Base, folder, dependencies, code, start.')),
            dMatch(pMatch(), [['FROM', L('Immagine di partenza', 'Base image')], ['RUN', L('Esegue durante la build', 'Runs during build')], ['COPY', L('Copia file', 'Copies files')], ['CMD', L('Comando all’avvio', 'Command at startup')], ['.dockerignore', L('File da non copiare', 'Files not to copy')]], L('Le istruzioni del Dockerfile.', 'Dockerfile instructions.')),
        ]),
        lesson('volumes', L('Volumi, porte e variabili', 'Volumes, ports and variables'), [
            P('I file scritti dentro un container spariscono quando lo rimuovi. Per i dati da conservare (un database, i file caricati) si usano i **volumi**, che vivono fuori dal container. Le **variabili d’ambiente** (`-e`) passano la configurazione senza cambiare l’immagine.', 'Files written inside a container vanish when you remove it. For data to keep (a database, uploaded files) you use **volumes**, which live outside the container. **Environment variables** (`-e`) pass configuration without changing the image.'),
            Fig(L(fig_docker_volume(['Computer (host)', 'strato scrivibile', 'sparisce con docker rm', 'resta']), fig_docker_volume(['Computer (host)', 'writable layer', 'gone with docker rm', 'stays'])),
                L('Il computer contiene il container db. Dentro il container la cartella /var/lib/postgresql/data è collegata con -v al volume dati-pg, che sta fuori dal container e resta; lo strato scrivibile del container sparisce con docker rm. A sinistra la porta 5432 del computer porta al container, con -p 5432:5432.', 'The computer holds the db container. Inside the container the /var/lib/postgresql/data folder is linked with -v to the dati-pg volume, which sits outside the container and stays; the container’s writable layer is gone with docker rm. On the left the computer’s port 5432 leads into the container, with -p 5432:5432.'),
                L('Quello che conta va nel volume, fuori dal container.', 'What matters goes into the volume, outside the container.')),
            C('docker volume create dati-pg
docker run -d --name db \
  -e POSTGRES_PASSWORD=segreta \
  -v dati-pg:/var/lib/postgresql/data \
  -p 5432:5432 \
  postgres:17

docker run -v "$PWD":/app -w /app node:22 npm test   # monta la cartella corrente', 'bash'),
            P('Montare una cartella del computer (`-v "$PWD":/app`) è comodo in sviluppo: modifichi i file e il container li vede subito. Le password vere non vanno scritte nei comandi o nell’immagine: in produzione si usano gestori di segreti.', 'Mounting a host folder (`-v "$PWD":/app`) is handy in development: you edit files and the container sees them at once. Real passwords must not be written in commands or in the image: in production you use secret managers.'),
        ], [
            Q('v1', L('Che cosa succede ai file scritti in un container rimosso?', 'What happens to files written in a removed container?'), [L('Spariscono, se non sono in un volume', 'They vanish, unless in a volume'), L('Restano per sempre', 'They stay forever'), L('Vanno su Docker Hub', 'They go to Docker Hub'), L('Si stampano', 'They get printed')], 0, L('Per questo esistono i volumi.', 'That is why volumes exist.')),
            Q('v2', L('Che cosa fa `-e NOME=valore`?', 'What does `-e NAME=value` do?'), [L('Imposta una variabile d’ambiente', 'Sets an environment variable'), L('Esporta l’immagine', 'Exports the image'), L('Esegue un test', 'Runs a test'), L('Elimina il container', 'Deletes the container')], 0, L('Configurazione senza ricostruire.', 'Configuration without rebuilding.')),
            Q('v3', L('Dove non vanno scritte le password vere?', 'Where must real passwords not be written?'), [L('Nell’immagine o nei comandi salvati', 'In the image or saved commands'), L('In un gestore di segreti', 'In a secrets manager'), L('Nella tua testa', 'In your head'), L('Da nessuna parte vale tutto', 'Anywhere is fine')], 0, L('Si leggono facilmente.', 'They are easily read.')),
        ], task('bash', L('Avvia `redis:7` chiamato `cache` con il volume `dati-redis` montato su `/data` e la porta `6379` esposta.', 'Start `redis:7` named `cache` with the `dati-redis` volume mounted at `/data` and port `6379` exposed.'), '', [re('docker\s+run\b', L('Avvii un container', 'You start a container')), re('--name\s+cache', L('Il nome', 'The name')), re('-v\s+dati-redis:/data', L('Il volume', 'The volume')), re('-p\s+6379:6379', L('La porta', 'The port')), re('redis:7', L('L’immagine', 'The image'))], '', 'docker run -d --name cache -v dati-redis:/data -p 6379:6379 redis:7'), [
            dMatch(pMatch(), [['-v nome:/percorso', L('Volume nominato', 'Named volume')], ['-v "$PWD":/app', L('Cartella del computer', 'Host folder')], ['-e', L('Variabile d’ambiente', 'Environment variable')], ['-p', L('Porta', 'Port')]], L('Collegare il container al mondo.', 'Connecting the container to the world.')),
            dFlash(pFlash(), [[L('I volumi conservano i dati oltre il container', 'Volumes keep data beyond the container'), true], [L('Le password vanno scritte nel Dockerfile', 'Passwords belong in the Dockerfile'), false], [L('Si può montare la cartella corrente nel container', 'You can mount the current folder into the container'), true], [L('Un container rimosso conserva i suoi file interni', 'A removed container keeps its internal files'), false]], L('Dati che restano.', 'Data that lasts.')),
        ]),
        lesson('compose', L('Docker Compose', 'Docker Compose'), [
            P('Un’app vera ha spesso più servizi: il server web, il database, una cache. **Docker Compose** li descrive tutti in un file `compose.yaml` e li avvia insieme con un comando. I servizi si raggiungono tra loro usando il **nome del servizio** come indirizzo.', 'A real app often has several services: the web server, the database, a cache. **Docker Compose** describes them all in a `compose.yaml` file and starts them together with one command. Services reach each other using the **service name** as address.'),
            C('services:
  web:
    build: .
    ports: ["3000:3000"]
    environment:
      DATABASE_URL: postgres://app:segreta@db:5432/app
    depends_on: [db]
  db:
    image: postgres:17
    environment:
      POSTGRES_USER: app
      POSTGRES_PASSWORD: segreta
    volumes: ["dati:/var/lib/postgresql/data"]
volumes:
  dati:', 'yaml'),
            C('docker compose up -d      # avvia tutto
docker compose logs -f web
docker compose down       # ferma e rimuove (i volumi restano)', 'bash'),
            P('Nel file, `web` si collega al database con l’indirizzo `db`, il nome del servizio. Compose è perfetto per lo sviluppo e per piccoli server; per molte macchine e alta disponibilità si passa a orchestratori come Kubernetes.', 'In the file, `web` connects to the database at address `db`, the service name. Compose is perfect for development and small servers; for many machines and high availability you move to orchestrators like Kubernetes.'),
        ], [
            Q('o1', L('Come si raggiunge il servizio db dal servizio web?', 'How does the web service reach the db service?'), [L('Usando il nome db come indirizzo', 'Using the name db as address'), L('Con l’IP pubblico', 'With the public IP'), L('Non si può', 'It cannot'), L('Via email', 'Via email')], 0, L('Rete interna di Compose.', 'Compose’s internal network.')),
            Q('o2', L('Che cosa fa `docker compose up -d`?', 'What does `docker compose up -d` do?'), [L('Avvia tutti i servizi in background', 'Starts all services in the background'), L('Cancella i volumi', 'Deletes volumes'), L('Costruisce solo il web', 'Only builds web'), L('Aggiorna Docker', 'Updates Docker')], 0, L('`down` li ferma.', '`down` stops them.')),
            Q('o3', L('Quando passare a Kubernetes?', 'When should you move to Kubernetes?'), [L('Con molte macchine e alta disponibilità', 'With many machines and high availability'), L('Per un progetto personale', 'For a personal project'), L('Mai', 'Never'), L('Per ogni sito', 'For every site')], 0, L('Compose basta per tanti casi.', 'Compose suffices in many cases.')),
        ], task('yaml', L('Scrivi un `compose.yaml` con il servizio `cache` dall’immagine `redis:7` e il servizio `app` che si costruisce dalla cartella corrente (`build: .`) e dipende da `cache`.', 'Write a `compose.yaml` with the `cache` service from the `redis:7` image and the `app` service built from the current folder (`build: .`) depending on `cache`.'), "services:\n", [re('^\s+cache:\s*\n\s+image:\s*redis:7', L('Il servizio cache', 'The cache service')), re('^\s+app:\s*\n(\s+.*\n)*?\s+build:\s*\.', L('Il servizio app', 'The app service')), re('depends_on:\s*(\[\s*cache\s*\]|\n\s+-\s*cache)', L('Dipende da cache', 'It depends on cache'))], '', "services:\n  app:\n    build: .\n    depends_on: [cache]\n  cache:\n    image: redis:7"), [
            dMatch(pMatch(), [['services', L('Elenco dei servizi', 'List of services')], ['build: .', L('Costruisce dal Dockerfile', 'Builds from the Dockerfile')], ['image', L('Usa un’immagine pronta', 'Uses a ready image')], ['depends_on', L('Ordine di avvio', 'Start order')], ['volumes', L('Dati persistenti', 'Persistent data')]], L('Il file di Compose.', 'The Compose file.')),
            dFlash(pFlash(), [[L('Compose avvia più servizi con un comando', 'Compose starts several services with one command'), true], [L('docker compose down cancella sempre i volumi', 'docker compose down always deletes volumes'), false], [L('I servizi si chiamano per nome', 'Services call each other by name'), true], [L('Compose sostituisce sempre Kubernetes', 'Compose always replaces Kubernetes'), false]], L('Più servizi, un comando.', 'Many services, one command.')),
        ]),
        lesson('production', L('Immagini leggere e sicure', 'Light and secure images'), [
            P('Un **build multi-stage** usa un’immagine grande per compilare e copia solo il risultato in un’immagine piccola: meno peso, meno software inutile, meno falle. Il file **`.dockerignore`** evita di copiare `node_modules`, `.git` e segreti nel contesto di build.', 'A **multi-stage build** uses a large image to compile and copies only the result into a small image: less weight, less needless software, fewer flaws. The **`.dockerignore`** file avoids copying `node_modules`, `.git` and secrets into the build context.'),
            Fig(L(fig_docker_stages(['codice sorgente', 'strumenti di build', 'fase di build: buttata via', 'immagine finale: pubblicata']), fig_docker_stages(['source code', 'build tools', 'build stage: thrown away', 'final image: shipped'])),
                L('A sinistra la fase di build, tratteggiata, con codice sorgente, node_modules, strumenti di build e la cartella dist. Una freccia COPY --from=build porta solo dist nella fase finale, nginx:alpine, che contiene nginx e dist.', 'On the left the build stage, dashed, with source code, node_modules, build tools and the dist folder. A COPY --from=build arrow carries only dist into the final stage, nginx:alpine, which holds nginx and dist.'),
                L('Nell’immagine che pubblichi resta solo il risultato.', 'Only the result stays in the image you ship.')),
            C('FROM node:22 AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM nginx:alpine
COPY --from=build /app/dist /usr/share/nginx/html', 'dockerfile'),
            P('Altre buone regole: fissa la versione dell’immagine base (`node:22`, non `latest`), esegui come utente **non root** con `USER` (per nginx esiste l’immagine `nginxinc/nginx-unprivileged`, già senza root e in ascolto sulla porta 8080), non scrivere segreti nel Dockerfile, e aggiungi un `HEALTHCHECK` per sapere se il servizio risponde.', 'Other good rules: pin the base image version (`node:22`, not `latest`), run as a **non-root** user with `USER` (for nginx there is the `nginxinc/nginx-unprivileged` image, already non-root and listening on port 8080), never write secrets in the Dockerfile, and add a `HEALTHCHECK` to know whether the service answers.'),
        ], [
            Q('pd1', L('Perché un build multi-stage?', 'Why a multi-stage build?'), [L('L’immagine finale contiene solo il necessario', 'The final image holds only what is needed'), L('Compila più lentamente', 'It compiles more slowly'), L('Serve per Windows', 'It is for Windows'), L('Evita il Dockerfile', 'It avoids the Dockerfile')], 0, L('Più piccola e più sicura.', 'Smaller and safer.')),
            Q('pd2', L('A che cosa serve `.dockerignore`?', 'What is `.dockerignore` for?'), [L('Escludere file dal contesto di build', 'Excluding files from the build context'), L('Ignorare gli errori', 'Ignoring errors'), L('Nascondere i container', 'Hiding containers'), L('Fermare Docker', 'Stopping Docker')], 0, L('Come `node_modules` e `.git`.', 'Like `node_modules` and `.git`.')),
            Q('pd3', L('Perché evitare il tag `latest`?', 'Why avoid the `latest` tag?'), [L('Cambia nel tempo e rende il build imprevedibile', 'It changes over time, making builds unpredictable'), L('È più pesante', 'It is heavier'), L('Non esiste', 'It does not exist'), L('È a pagamento', 'It costs money')], 0, L('Fissa la versione.', 'Pin the version.')),
        ], task('dockerfile', L('Scrivi la seconda fase: `FROM nginx:alpine` e copia `/app/dist` dalla fase `build` in `/usr/share/nginx/html`.', 'Write the second stage: `FROM nginx:alpine` and copy `/app/dist` from the `build` stage into `/usr/share/nginx/html`.'), '', [re('^FROM\s+nginx:alpine', L('L’immagine finale è leggera', 'The final image is light')), re('^COPY\s+--from=build\s+/app/dist\s+/usr/share/nginx/html', L('Copi il risultato', 'You copy the result'))], '', "FROM nginx:alpine\nCOPY --from=build /app/dist /usr/share/nginx/html"), [
            dOrder(pSort(), ['FROM node:22 AS build', 'COPY package*.json ./', 'RUN npm ci', 'RUN npm run build', 'FROM nginx:alpine', 'COPY --from=build /app/dist /usr/share/nginx/html'], L('Prima si costruisce, poi si copia il risultato.', 'Build first, then copy the result.')),
            dFlash(pFlash(), [[L('USER evita di eseguire come root', 'USER avoids running as root'), true], [L('I segreti vanno scritti nel Dockerfile', 'Secrets belong in the Dockerfile'), false], [L('.dockerignore riduce il contesto di build', '.dockerignore shrinks the build context'), true], [L('latest è sempre la versione più sicura', 'latest is always the safest version'), false]], L('Immagini da produzione.', 'Production images.')),
        ]),
        lesson('debug', L('Diagnosi e pulizia', 'Debugging and cleanup'), [
            P('Quando un container non si comporta bene, gli strumenti sono pochi e potenti: `docker logs` mostra l’output dell’app, `docker exec -it` apre una shell **dentro** un container in esecuzione, `docker inspect` dà tutti i dettagli in JSON (variabili, volumi, rete) e `docker stats` il consumo di CPU e memoria in tempo reale.', 'When a container misbehaves, the tools are few and powerful: `docker logs` shows the app’s output, `docker exec -it` opens a shell **inside** a running container, `docker inspect` gives every detail as JSON (variables, volumes, network) and `docker stats` live CPU and memory usage.'),
            C('docker ps -a                              # anche i container fermi
docker logs -f --tail 50 web              # segue le ultime 50 righe
docker exec -it web sh                    # una shell dentro il container
docker inspect web --format "{{.State.Status}}"
docker stats                              # CPU e memoria in tempo reale
docker system df                          # quanto spazio occupa Docker
docker system prune                       # toglie ciò che non si usa più', 'bash'),
            P('Un container che si ferma subito appare in `docker ps -a` come `Exited`: il motivo quasi sempre è nei suoi log. Immagini vecchie, container fermi e cache di build si accumulano: `docker system prune` li toglie, mentre con `--volumes` cancella anche volumi non usati, quindi attenzione ai dati.', 'A container that stops at once appears in `docker ps -a` as `Exited`: the reason is almost always in its logs. Old images, stopped containers and build cache pile up: `docker system prune` removes them, while with `--volumes` it also deletes unused volumes, so mind your data.'),
        ], [
            Q('db1', L('Come apri una shell nel container `web` già in esecuzione?', 'How do you open a shell in the already running `web` container?'), ['docker run -it web sh', 'docker exec -it web sh', 'docker attach --shell web', 'docker logs -it web'], 1, L('`run` creerebbe un nuovo container.', '`run` would create a new container.')),
            Q('db2', L('Un container si ferma appena avviato. Qual è la prima cosa da fare?', 'A container stops right after starting. What is the first thing to do?'), [L('Ricostruire l’immagine senza cache', 'Rebuild the image without cache'), L('Riavviare il computer', 'Restart the computer'), L('Leggere i suoi log con `docker logs`', 'Read its logs with `docker logs`'), L('Dargli più memoria con `--memory`', 'Give it more memory with `--memory`')], 2, L('Il messaggio d’errore di solito è lì.', 'The error message is usually there.')),
            Q('db3', L('Che cosa toglie `docker system prune` senza opzioni?', 'What does `docker system prune` remove without options?'), [L('Container fermi, reti inutilizzate, immagini orfane e cache di build', 'Stopped containers, unused networks, dangling images and build cache'), L('Tutti i volumi, compresi quelli con i dati', 'All volumes, including those holding data'), L('Solo i container in esecuzione', 'Only the running containers'), L('Le immagini ancora usate dai container attivi', 'Images still used by running containers')], 0, L('I volumi solo con `--volumes`.', 'Volumes only with `--volumes`.')),
        ], task('bash', L('Segui i log del container `api` partendo dalle ultime 20 righe, poi apri una shell `sh` dentro di lui.', 'Follow the logs of the `api` container starting from the last 20 lines, then open an `sh` shell inside it.'), '', [re('docker\s+(container\s+)?logs\b[^\n]*\s(-f|--follow)\b', L('Segui i log', 'You follow the logs')), re('docker\s+(container\s+)?logs\b[^\n]*--tail[=\s]+20\b', L('Ultime 20 righe', 'Last 20 lines')), re('docker\s+(container\s+)?logs\b[^\n]*\sapi\b', L('Del container api', 'Of the api container')), re('docker\s+(container\s+)?exec\s+(-it|-ti|-i\s+-t)\s+api\s+(sh|/bin/sh)\b', L('La shell nel container', 'The shell in the container'))], '', "docker logs -f --tail 20 api\ndocker exec -it api sh"), [
            dMatch(pMatch(), [['docker exec', L('Comando in un container attivo', 'Command in a running container')], ['docker logs -f', L('Segue i log', 'Follows the logs')], ['docker inspect', L('Tutti i dettagli in JSON', 'Every detail as JSON')], ['docker stats', L('CPU e memoria dal vivo', 'Live CPU and memory')], ['docker system prune', L('Pulizia di ciò che non si usa', 'Cleanup of unused things')]], L('La cassetta degli attrezzi.', 'The toolbox.')),
            dOrder(pSort(), ['docker ps -a', 'docker logs api', 'docker inspect api', L('Correggi la configurazione', 'Fix the configuration'), 'docker start api'], L('Trova il container, leggi, indaga, correggi, riavvia.', 'Find the container, read, investigate, fix, restart.')),
        ]),
        lesson('networks', L('Reti tra container', 'Networks between containers'), [
            P('I container collegati alla stessa **rete definita da te** si trovano per **nome**, grazie al DNS interno di Docker: il container `api` raggiunge il database all’indirizzo `db`. La rete `bridge` predefinita invece non risolve i nomi. Docker Compose crea da solo una rete per ogni progetto.', 'Containers attached to the same **user-defined network** find each other by **name**, thanks to Docker’s internal DNS: the `api` container reaches the database at the address `db`. The default `bridge` network, instead, does not resolve names. Docker Compose creates a network for each project by itself.'),
            Fig(L(fig_docker_net(['nessuna porta pubblicata', 'da fuori il database non si raggiunge']), fig_docker_net(['no published port', 'the database cannot be reached from outside'])),
                L('Dentro la rete rete-app ci sono i container api e db. Il browser, fuori, raggiunge api sulla porta 3000; api raggiunge il database all’indirizzo db:5432. Una linea tratteggiata dal browser verso db si ferma al bordo della rete.', 'Inside the rete-app network are the api and db containers. The browser, outside, reaches api on port 3000; api reaches the database at db:5432. A dashed line from the browser towards db stops at the edge of the network.'),
                L('Dentro la rete si usano i nomi; verso fuori si apre solo ciò che serve.', 'Inside the network you use names; towards the outside you open only what is needed.')),
            C('docker network create rete-app
docker run -d --name db --network rete-app \
  -e POSTGRES_PASSWORD=segreta postgres:17           # nessuna porta pubblicata
docker run -d --name api --network rete-app \
  -p 127.0.0.1:3000:3000 mia-api:1.0                # solo dal computer stesso
docker network ls
docker network inspect rete-app', 'bash'),
            P('Pubblica con `-p` solo le porte che devono essere raggiunte **da fuori**: se il database serve solo all’API, nella rete interna basta il nome e non esporlo è più sicuro. Con `-p 127.0.0.1:3000:3000` la porta è raggiungibile solo dal computer stesso. Attenzione: su Linux le porte pubblicate da Docker possono scavalcare le regole di firewall come ufw.', 'Publish with `-p` only the ports that must be reached **from outside**: if the database serves only the API, on the internal network the name is enough and not exposing it is safer. With `-p 127.0.0.1:3000:3000` the port can be reached only from the machine itself. Beware: on Linux ports published by Docker can bypass firewall rules such as ufw.'),
        ], [
            Q('nt1', L('Come si raggiungono due container sulla stessa rete creata da te?', 'How do two containers on the same network you created reach each other?'), [L('Solo con l’IP pubblico del server', 'Only with the server’s public IP'), L('Solo pubblicando le porte con -p', 'Only by publishing ports with -p'), L('Usando il nome del container come indirizzo', 'Using the container name as address'), L('Non possono: serve una rete per ciascuno', 'They cannot: each needs its own network')], 2, L('Il DNS interno di Docker risolve i nomi.', 'Docker’s internal DNS resolves the names.')),
            Q('nt2', L('Il database serve solo al container dell’API sulla stessa rete. Serve `-p 5432:5432`?', 'The database is used only by the API container on the same network. Do you need `-p 5432:5432`?'), [L('Sì, altrimenti l’API non lo raggiunge', 'Yes, otherwise the API cannot reach it'), L('No: basta il nome, e non esporlo è più sicuro', 'No: the name is enough, and not exposing it is safer'), L('Sì, ma solo quando si usa Compose', 'Yes, but only when using Compose'), L('No, perché i database non usano porte', 'No, because databases do not use ports')], 1, L('Meno porte aperte, meno superficie d’attacco.', 'Fewer open ports, less attack surface.')),
            Q('nt3', L('Che cosa fa `-p 127.0.0.1:8080:80`?', 'What does `-p 127.0.0.1:8080:80` do?'), [L('Rende la porta raggiungibile solo dal computer stesso', 'Makes the port reachable only from the machine itself'), L('Apre la porta a tutta la rete locale', 'Opens the port to the whole local network'), L('Blocca la porta 80 del container', 'Blocks the container’s port 80'), L('Cambia l’indirizzo IP del container', 'Changes the container’s IP address')], 0, L('Utile in sviluppo e dietro un proxy.', 'Handy in development and behind a proxy.')),
        ], task('bash', L('Crea la rete `negozio` e avvia in background il container `cache` dall’immagine `redis:7` collegato a quella rete, senza pubblicare porte.', 'Create the `negozio` network and start in the background the `cache` container from the `redis:7` image attached to that network, without publishing ports.'), '', [re('docker\s+network\s+create\s+negozio\b', L('Crei la rete', 'You create the network')), re('docker\s+run\b[^\n]*--network[=\s]+negozio\b', L('Colleghi il container', 'You attach the container')), re('docker\s+run\b[^\n]*--name[=\s]+cache\b', L('Il nome', 'The name')), re('docker\s+run\b[^\n]*\sredis:7\s*$', L('L’immagine', 'The image'))], '', "docker network create negozio\ndocker run -d --name cache --network negozio redis:7"), [
            dMatch(pMatch(), [['docker network create', L('Crea una rete', 'Creates a network')], ['--network', L('Collega il container', 'Attaches the container')], [L('Nome del container', 'Container name'), L('Indirizzo nella rete', 'Address on the network')], ['127.0.0.1:3000:3000', L('Porta solo locale', 'Local-only port')], [L('Bridge predefinita', 'Default bridge'), L('Niente nomi, solo IP', 'No names, only IPs')]], L('Container che si parlano.', 'Containers talking to each other.')),
            dFlash(pFlash(), [[L('Compose crea una rete per ogni progetto', 'Compose creates a network for each project'), true], [L('Sulla bridge predefinita i container si trovano per nome', 'On the default bridge containers find each other by name'), false], [L('Ogni database deve pubblicare la sua porta', 'Every database must publish its port'), false], [L('Le porte pubblicate possono scavalcare ufw', 'Published ports can bypass ufw'), true]], L('Aprire solo il necessario.', 'Open only what is needed.')),
        ]),
        lesson('build-config', L('CMD, ENTRYPOINT, ARG ed ENV', 'CMD, ENTRYPOINT, ARG and ENV'), [
            P('`ENTRYPOINT` fissa il programma che il container esegue sempre; `CMD` dà gli **argomenti predefiniti**, che `docker run immagine …` sostituisce. Scrivili nella **forma JSON** (`["node", "server.js"]`): il processo riceve direttamente i segnali, così `docker stop` lo chiude con ordine invece di aspettare e ucciderlo.', '`ENTRYPOINT` fixes the program the container always runs; `CMD` gives the **default arguments**, which `docker run image …` replaces. Write them in **JSON form** (`["node", "server.js"]`): the process receives signals directly, so `docker stop` shuts it down cleanly instead of waiting and killing it.'),
            C('FROM python:3.13-slim
ARG VERSIONE=dev
ENV PYTHONUNBUFFERED=1 \
    APP_VERSIONE=$VERSIONE
WORKDIR /app
COPY . .
ENTRYPOINT ["python", "cli.py"]
CMD ["--help"]', 'dockerfile'),
            C('docker build --build-arg VERSIONE=1.4.0 -t strumento:1.4.0 .
docker run strumento:1.4.0                    # python cli.py --help
docker run strumento:1.4.0 importa dati.csv   # python cli.py importa dati.csv', 'bash'),
            P('`ARG` esiste solo durante la build, `ENV` resta nell’immagine e nei container. Nessuno dei due va bene per i segreti: restano leggibili nella storia dell’immagine. Per un segreto che serve solo alla build si usa `RUN --mount=type=secret,id=npmrc,target=/root/.npmrc npm ci` con `docker build --secret id=npmrc,src=.npmrc`.', '`ARG` exists only during the build, `ENV` stays in the image and in containers. Neither is fine for secrets: they remain readable in the image history. For a secret needed only by the build you use `RUN --mount=type=secret,id=npmrc,target=/root/.npmrc npm ci` with `docker build --secret id=npmrc,src=.npmrc`.'),
        ], [
            Q('bc1', L('Con `ENTRYPOINT ["python", "cli.py"]` e `CMD ["--help"]`, che cosa esegue `docker run immagine importa`?', 'With `ENTRYPOINT ["python", "cli.py"]` and `CMD ["--help"]`, what does `docker run image importa` run?'), ['python cli.py --help importa', 'importa', 'python cli.py importa', 'python cli.py --help'], 2, L('Gli argomenti di run sostituiscono CMD, non ENTRYPOINT.', 'run’s arguments replace CMD, not ENTRYPOINT.')),
            Q('bc2', L('Che differenza c’è tra `ARG` ed `ENV`?', 'What is the difference between `ARG` and `ENV`?'), [L('ENV vale solo durante la build, ARG resta nei container', 'ENV applies only during the build, ARG stays in containers'), L('ARG vale solo durante la build, ENV resta anche nei container', 'ARG applies only during the build, ENV stays in containers too'), L('ARG è per i numeri, ENV per i testi', 'ARG is for numbers, ENV for text'), L('Nessuna: sono due nomi della stessa istruzione', 'None: they are two names for one instruction')], 1, L('Ma nessuno dei due è adatto ai segreti.', 'But neither is suitable for secrets.')),
            Q('bc3', L('Perché preferire la forma JSON `CMD ["node", "server.js"]`?', 'Why prefer the JSON form `CMD ["node", "server.js"]`?'), [L('Occupa meno spazio nell’immagine', 'It takes less space in the image'), L('Permette di usare le variabili della shell', 'It lets you use shell variables'), L('È l’unica forma accettata da Compose', 'It is the only form Compose accepts'), L('Il processo riceve direttamente i segnali, come lo stop', 'The process receives signals directly, such as stop')], 3, L('Senza shell in mezzo.', 'With no shell in between.')),
        ], task('dockerfile', L('Scrivi un Dockerfile: base `node:22-slim`, `ARG VERSIONE` con valore predefinito `dev`, `ENV APP_VERSIONE` uguale a `$VERSIONE`, `ENTRYPOINT ["node", "cli.js"]` e `CMD ["--help"]`.', 'Write a Dockerfile: base `node:22-slim`, `ARG VERSIONE` with default value `dev`, `ENV APP_VERSIONE` equal to `$VERSIONE`, `ENTRYPOINT ["node", "cli.js"]` and `CMD ["--help"]`.'), '', [re('^FROM\s+node:22-slim\s*$', L('L’immagine base', 'The base image')), re('^ARG\s+VERSIONE=dev\s*$', L('L’argomento di build', 'The build argument')), re('^ENV\s+APP_VERSIONE[=\s]+["\']?\$\{?VERSIONE\}?', L('La variabile d’ambiente', 'The environment variable')), re('^ENTRYPOINT\s+\[\s*"node"\s*,\s*"cli\.js"\s*\]', L('Il programma fisso', 'The fixed program')), re('^CMD\s+\[\s*"--help"\s*\]', L('Gli argomenti predefiniti', 'The default arguments'))], '', "FROM node:22-slim\nARG VERSIONE=dev\nENV APP_VERSIONE=\$VERSIONE\nWORKDIR /app\nCOPY . .\nENTRYPOINT [\"node\", \"cli.js\"]\nCMD [\"--help\"]"), [
            dMatch(pMatch(), [['ENTRYPOINT', L('Il programma fisso', 'The fixed program')], ['CMD', L('Gli argomenti predefiniti', 'The default arguments')], ['ARG', L('Variabile della build', 'Build variable')], ['ENV', L('Variabile dell’immagine', 'Image variable')], ['--mount=type=secret', L('Segreto solo durante la build', 'Secret only during the build')]], L('Le istruzioni che configurano.', 'The configuring instructions.')),
            dFlash(pFlash(), [[L('ENV è un posto sicuro per una password', 'ENV is a safe place for a password'), false], [L('Gli argomenti di docker run sostituiscono CMD', 'docker run’s arguments replace CMD'), true], [L('ARG non resta nelle variabili del container', 'ARG does not stay in the container’s variables'), true], [L('La forma shell inoltra i segnali meglio di quella JSON', 'The shell form forwards signals better than the JSON form'), false]], L('Configurare con criterio.', 'Configuring wisely.')),
        ]),
        lesson('compose-advanced', L('Compose: salute, profili e sviluppo', 'Compose: health, profiles and development'), [
            P('Di base `depends_on` garantisce solo l’**ordine di avvio**, non che il database sia pronto. Con un **`healthcheck`** sul database e `condition: service_healthy` Compose aspetta che risponda davvero prima di avviare l’app.', 'By default `depends_on` guarantees only the **start order**, not that the database is ready. With a **`healthcheck`** on the database and `condition: service_healthy` Compose waits until it really answers before starting the app.'),
            C('services:
  web:
    build: .
    ports: ["3000:3000"]
    depends_on:
      db:
        condition: service_healthy
    develop:
      watch:
        - action: sync
          path: ./src
          target: /app/src
  db:
    image: postgres:17
    environment:
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U postgres"]
      interval: 5s
      retries: 10
  adminer:
    image: adminer
    profiles: [strumenti]', 'yaml'),
            C('docker compose up -d                        # web e db
docker compose --profile strumenti up -d    # anche adminer
docker compose watch                        # sincronizza il codice mentre scrivi
docker compose exec db psql -U postgres', 'bash'),
            P('Compose legge variabili come `${DB_PASSWORD}` dal file **`.env`** accanto a `compose.yaml` (da tenere fuori da Git). I **profili** avviano i servizi facoltativi solo su richiesta, e `docker compose watch` copia nel container i file che modifichi, senza ricostruire a mano.', 'Compose reads variables like `${DB_PASSWORD}` from the **`.env`** file next to `compose.yaml` (to be kept out of Git). **Profiles** start optional services only on request, and `docker compose watch` copies the files you edit into the container, without rebuilding by hand.'),
        ], [
            Q('ca1', L('Che cosa garantisce `depends_on` senza condizioni?', 'What does `depends_on` without conditions guarantee?'), [L('Che il database sia pronto a rispondere', 'That the database is ready to answer'), L('Solo l’ordine di avvio dei container', 'Only the start order of the containers'), L('Che i servizi girino sulla stessa macchina', 'That the services run on the same machine'), L('Che i dati siano copiati tra i servizi', 'That data is copied between services')], 1, L('Avviato non vuol dire pronto.', 'Started does not mean ready.')),
            Q('ca2', L('Come fai aspettare a `web` che `db` risponda davvero?', 'How do you make `web` wait until `db` really answers?'), [L('Aggiungendo `restart: always` al database', 'Adding `restart: always` to the database'), L('Scrivendo db prima di web nel file', 'Writing db before web in the file'), L('Con `sleep 30` nel comando di web', 'With `sleep 30` in web’s command'), L('Con un `healthcheck` su db e `condition: service_healthy`', 'With a `healthcheck` on db and `condition: service_healthy`')], 3, L('Niente attese a caso.', 'No arbitrary waits.')),
            Q('ca3', L('Che cosa succede a un servizio con `profiles: [strumenti]`?', 'What happens to a service with `profiles: [strumenti]`?'), [L('Non può più essere avviato', 'It can no longer be started'), L('Parte sempre per primo', 'It always starts first'), L('Parte solo se attivi quel profilo', 'It starts only if you enable that profile'), L('Usa le risorse di un altro servizio', 'It uses another service’s resources')], 2, L('Con `--profile strumenti`.', 'With `--profile strumenti`.')),
        ], task('yaml', L('Aggiungi a `db` un `healthcheck` con `test: ["CMD-SHELL", "pg_isready -U postgres"]` e fai dipendere `web` da `db` con `condition: service_healthy`.', 'Add to `db` a `healthcheck` with `test: ["CMD-SHELL", "pg_isready -U postgres"]` and make `web` depend on `db` with `condition: service_healthy`.'), "services:\n  web:\n    build: .\n  db:\n    image: postgres:17\n", [re('healthcheck:\s*\n\s+test:\s*\[\s*"CMD-SHELL"\s*,\s*"pg_isready[^"]*"\s*\]', L('Il controllo di salute', 'The health check')), re('depends_on:\s*\n\s+db:\s*\n\s+condition:\s*service_healthy', L('web aspetta db sano', 'web waits for a healthy db'))], '', "services:\n  web:\n    build: .\n    depends_on:\n      db:\n        condition: service_healthy\n  db:\n    image: postgres:17\n    healthcheck:\n      test: [\"CMD-SHELL\", \"pg_isready -U postgres\"]\n      interval: 5s\n      retries: 10"), [
            dMatch(pMatch(), [['healthcheck', L('Come capire se il servizio sta bene', 'How to tell the service is healthy')], ['service_healthy', L('Aspetta il servizio sano', 'Waits for the healthy service')], ['profiles', L('Servizi facoltativi', 'Optional services')], ['.env', L('Variabili per il file Compose', 'Variables for the Compose file')], ['watch', L('Sincronizza il codice', 'Syncs the code')]], L('Compose oltre le basi.', 'Compose beyond the basics.')),
            dBug(pBug(), ['  web:', '    depends_on:', '      db:', '        condition: healthy', '  db:', '    image: postgres:17'], 3, '        condition: service_healthy', L('La condizione si chiama `service_healthy`.', 'The condition is called `service_healthy`.')),
        ]),
        lesson('registry', L('Registri e build per più piattaforme', 'Registries and multi-platform builds'), [
            P('Per condividere un’immagine la pubblichi in un **registro**: Docker Hub, GitHub Container Registry (`ghcr.io`) o il registro del tuo fornitore cloud. Il nome completo contiene registro, proprietario, nome e tag: `ghcr.io/ada/negozio:1.2.0`.', 'To share an image you push it to a **registry**: Docker Hub, GitHub Container Registry (`ghcr.io`) or your cloud provider’s registry. The full name contains registry, owner, name and tag: `ghcr.io/ada/negozio:1.2.0`.'),
            C('docker login ghcr.io
docker tag negozio:1.2.0 ghcr.io/ada/negozio:1.2.0
docker push ghcr.io/ada/negozio:1.2.0
docker pull ghcr.io/ada/negozio@sha256:4f1c…      # un contenuto preciso
docker buildx build --platform linux/amd64,linux/arm64 \
  -t ghcr.io/ada/negozio:1.2.0 --push .
docker scout cves ghcr.io/ada/negozio:1.2.0       # vulnerabilità note', 'bash'),
            P('Un **tag** può essere spostato su un’altra immagine; il **digest** `@sha256:…` identifica un solo contenuto: usalo quando ti serve certezza. Con **buildx** costruisci per più architetture insieme (server x86 e ARM, come i Mac con Apple Silicon). Prima di pubblicare, cerca le vulnerabilità note con `docker scout` o Trivy.', 'A **tag** can be moved to another image; the **digest** `@sha256:…` identifies exactly one content: use it when you need certainty. With **buildx** you build for several architectures at once (x86 and ARM servers, such as Apple Silicon Macs). Before publishing, look for known vulnerabilities with `docker scout` or Trivy.'),
        ], [
            Q('rg1', L('Che cosa fai prima di pubblicare su GHCR l’immagine locale `negozio:1.2.0`?', 'What do you do before pushing the local image `negozio:1.2.0` to GHCR?'), [L('La ricostruisci con un Dockerfile diverso', 'Rebuild it with a different Dockerfile'), L('Le dai con `docker tag` un nome che contiene il registro', 'Give it a name containing the registry with `docker tag`'), L('La comprimi in un archivio con tar', 'Compress it into an archive with tar'), L('La trasformi in un volume', 'Turn it into a volume')], 1, L('Il nome dice dove va pubblicata.', 'The name says where it is pushed.')),
            Q('rg2', L('Che differenza c’è tra tag e digest?', 'What is the difference between a tag and a digest?'), [L('Il tag è più sicuro perché non cambia mai', 'The tag is safer because it never changes'), L('Il digest è un nome scelto da te', 'The digest is a name you choose'), L('Il tag si può spostare, il digest identifica un contenuto preciso', 'The tag can be moved, the digest identifies one exact content'), L('Sono la stessa cosa scritta in due modi', 'They are the same thing written two ways')], 2, L('Il digest è un’impronta del contenuto.', 'The digest is a fingerprint of the content.')),
            Q('rg3', L('A che cosa serve `--platform linux/amd64,linux/arm64`?', 'What is `--platform linux/amd64,linux/arm64` for?'), [L('A pubblicare l’immagine su due registri', 'Pushing the image to two registries'), L('A renderla compatibile con Windows e Linux', 'Making it compatible with Windows and Linux'), L('A cifrarla due volte', 'Encrypting it twice'), L('A farla girare su processori sia x86 sia ARM', 'Running it on both x86 and ARM processors')], 3, L('Un solo nome, più architetture.', 'One name, several architectures.')),
        ], task('bash', L('Dai all’immagine `negozio:2.0` il nome `ghcr.io/ada/negozio:2.0` e pubblicala.', 'Give the `negozio:2.0` image the name `ghcr.io/ada/negozio:2.0` and push it.'), '', [re('docker\s+(image\s+)?tag\s+negozio:2\.0\s+ghcr\.io/ada/negozio:2\.0', L('Il nuovo nome', 'The new name')), re('docker\s+(image\s+)?push\s+ghcr\.io/ada/negozio:2\.0', L('La pubblichi', 'You push it'))], '', "docker tag negozio:2.0 ghcr.io/ada/negozio:2.0\ndocker push ghcr.io/ada/negozio:2.0"), [
            dMatch(pMatch(), [['docker login', L('Accedi al registro', 'Sign in to the registry')], ['docker tag', L('Un altro nome per l’immagine', 'Another name for the image')], ['docker push', L('Pubblica', 'Publishes')], ['@sha256:…', L('Un contenuto esatto', 'An exact content')], ['buildx --platform', L('Più architetture', 'Several architectures')]], L('Distribuire immagini.', 'Distributing images.')),
            dOrder(pSort(), ['docker build -t negozio:2.0 .', 'docker tag negozio:2.0 ghcr.io/ada/negozio:2.0', 'docker push ghcr.io/ada/negozio:2.0', L('Sul server: docker pull ghcr.io/ada/negozio:2.0', 'On the server: docker pull ghcr.io/ada/negozio:2.0'), L('Sul server: docker run -d ghcr.io/ada/negozio:2.0', 'On the server: docker run -d ghcr.io/ada/negozio:2.0')], L('Costruisci, dai il nome, pubblica, scarica, avvia.', 'Build, name, push, pull, run.')),
        ]),
    ],
];
