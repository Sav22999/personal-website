<?php
/** Docker: images and containers, the Dockerfile, volumes and ports, Docker Compose. */

return [
    'id' => 'docker', 'family' => 'docker', 'after' => 'bash',
    'title' => L('Docker', 'Docker'), 'short' => L('Docker', 'Docker'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Impacchettare un’app con tutto quello che le serve: immagini e container, Dockerfile, volumi e porte, più servizi insieme con Docker Compose.', 'Packaging an app with everything it needs: images and containers, the Dockerfile, volumes and ports, several services together with Docker Compose.'),
    'lessons' => [
        lesson('containers', L('Immagini e container', 'Images and containers'), [
            P('Un’**immagine** Docker è un pacchetto con un’app e tutto quello che le serve (sistema minimo, librerie, configurazione). Un **container** è un’immagine in esecuzione, isolata dalle altre. Così l’app gira uguale sul tuo portatile, in test e in produzione: basta con “da me funziona”.', 'A Docker **image** is a package with an app and everything it needs (minimal system, libraries, configuration). A **container** is a running image, isolated from others. That way the app runs the same on your laptop, in testing and in production: no more “works on my machine”.'),
            C('docker pull nginx                     # scarica un’immagine
docker run -d -p 8080:80 --name web nginx   # avvia un container
docker ps                             # container in esecuzione
docker logs web                       # log del container
docker stop web && docker rm web      # ferma e rimuove', 'bash'),
            P('Le immagini pubbliche stanno su registri come **Docker Hub**. Un container è più leggero di una macchina virtuale perché condivide il kernel del sistema ospite.', 'Public images live on registries such as **Docker Hub**. A container is lighter than a virtual machine because it shares the host system’s kernel.'),
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
        ], [
            Q('d1', L('Quale istruzione indica l’immagine di partenza?', 'Which instruction sets the base image?'), ['FROM', 'RUN', 'CMD', 'COPY'], 0, L('Sempre la prima.', 'Always the first.')),
            Q('d2', L('Perché copiare prima package.json e poi il codice?', 'Why copy package.json first and then the code?'), [L('Per riusare la cache delle dipendenze', 'To reuse the dependency cache'), L('È obbligatorio', 'It is mandatory'), L('Per ordine alfabetico', 'For alphabetical order'), L('Non conta', 'It does not matter')], 0, L('Build molto più veloci.', 'Much faster builds.')),
            Q('d3', L('Che cosa fa `docker build -t mia-app:1.0 .`?', 'What does `docker build -t mia-app:1.0 .` do?'), [L('Costruisce l’immagine con un nome e una versione', 'Builds the image with a name and version'), L('Avvia un container', 'Starts a container'), L('Cancella le immagini', 'Deletes images'), L('Scarica nginx', 'Downloads nginx')], 0, L('-t = tag.', '-t = tag.')),
        ], task('dockerfile', L('Scrivi un Dockerfile per un’app Python: parti da `python:3.12-slim`, lavora in `/app`, copia `requirements.txt` e installalo con pip, copia il resto e avvia `python app.py`.', 'Write a Dockerfile for a Python app: start from `python:3.12-slim`, work in `/app`, copy `requirements.txt` and install it with pip, copy the rest and run `python app.py`.'), '', [re('^FROM\s+python:3\.12-slim', L('L’immagine base', 'The base image')), re('^WORKDIR\s+/app', L('La cartella di lavoro', 'The working directory')), re('^COPY\s+requirements\.txt\s+\.[\s\S]*^RUN\s+pip\s+install\s+(--no-cache-dir\s+)?-r\s+requirements\.txt[\s\S]*^COPY\s+\.\s+\.', L('Dipendenze prima del codice', 'Dependencies before code')), re('^CMD\s+\[\s*"python"\s*,\s*"app\.py"\s*\]', L('Il comando di avvio', 'The start command'))], '', "FROM python:3.12-slim\nWORKDIR /app\nCOPY requirements.txt .\nRUN pip install --no-cache-dir -r requirements.txt\nCOPY . .\nCMD [\"python\", \"app.py\"]"), [
            dOrder(pSort(), ['FROM node:22-slim', 'WORKDIR /app', 'COPY package*.json ./', 'RUN npm ci', 'COPY . .', 'CMD ["node", "server.js"]'], L('Base, cartella, dipendenze, codice, avvio.', 'Base, folder, dependencies, code, start.')),
            dMatch(pMatch(), [['FROM', L('Immagine di partenza', 'Base image')], ['RUN', L('Esegue durante la build', 'Runs during build')], ['COPY', L('Copia file', 'Copies files')], ['CMD', L('Comando all’avvio', 'Command at startup')], ['.dockerignore', L('File da non copiare', 'Files not to copy')]], L('Le istruzioni del Dockerfile.', 'Dockerfile instructions.')),
        ]),
        lesson('volumes', L('Volumi, porte e variabili', 'Volumes, ports and variables'), [
            P('I file scritti dentro un container spariscono quando lo rimuovi. Per i dati da conservare (un database, i file caricati) si usano i **volumi**, che vivono fuori dal container. Le **variabili d’ambiente** (`-e`) passano la configurazione senza cambiare l’immagine.', 'Files written inside a container vanish when you remove it. For data to keep (a database, uploaded files) you use **volumes**, which live outside the container. **Environment variables** (`-e`) pass configuration without changing the image.'),
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
    ],
];
