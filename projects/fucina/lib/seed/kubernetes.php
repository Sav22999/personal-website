<?php
/** Kubernetes: why an orchestrator, pods and deployments, services and ingress, configuration, secrets and scaling. */

return [
    'id' => 'kubernetes', 'family' => 'kubernetes', 'after' => 'docker',
    'title' => L('Kubernetes', 'Kubernetes'), 'short' => L('Kubernetes', 'Kubernetes'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Far girare container su tante macchine: pod e deployment, service e ingress, ConfigMap e Secret, scalabilità, aggiornamenti e kubectl.', 'Running containers across many machines: pods and deployments, services and ingress, ConfigMaps and Secrets, scaling, updates and kubectl.'),
    'lessons' => [
        lesson('why', L('Perché un orchestratore', 'Why an orchestrator'), [
            P('Quando i container sono tanti e girano su più macchine, serve qualcuno che decida dove avviarli, li riavvii se cadono, li distribuisca e li aggiorni senza interruzioni. **Kubernetes** (K8s) è l’**orchestratore** più usato: tu descrivi lo **stato desiderato** in file YAML e il cluster lavora continuamente per raggiungerlo.', 'When containers are many and run on several machines, someone must decide where to start them, restart them if they crash, spread them and update them without downtime. **Kubernetes** (K8s) is the most used **orchestrator**: you describe the **desired state** in YAML files and the cluster works continuously to reach it.'),
            C('kubectl get nodes                 # le macchine del cluster
kubectl apply -f app.yaml         # applica lo stato desiderato
kubectl get pods                  # cosa gira
kubectl describe pod web-7d9c…    # dettagli ed eventi
kubectl logs web-7d9c…            # log', 'bash'),
            P('Un **cluster** ha un piano di controllo (che decide) e dei **nodi** (che eseguono i container). Per provare in locale esistono strumenti come kind, minikube o Docker Desktop; in produzione si usano servizi gestiti dei fornitori cloud.', 'A **cluster** has a control plane (which decides) and **nodes** (which run the containers). To try it locally there are tools like kind, minikube or Docker Desktop; in production you use cloud providers’ managed services.'),
        ], [
            Q('w1', L('Che cosa descrivi nei file YAML di Kubernetes?', 'What do you describe in Kubernetes YAML files?'), [L('Lo stato desiderato', 'The desired state'), L('I comandi da eseguire uno per uno', 'Commands to run one by one'), L('Le password degli utenti', 'Users’ passwords'), L('Il colore dell’interfaccia', 'The UI colour')], 0, L('Il cluster lo raggiunge da solo.', 'The cluster reaches it by itself.')),
            Q('w2', L('Che cosa fa `kubectl apply -f app.yaml`?', 'What does `kubectl apply -f app.yaml` do?'), [L('Applica lo stato descritto nel file', 'Applies the state described in the file'), L('Cancella il cluster', 'Deletes the cluster'), L('Scarica Kubernetes', 'Downloads Kubernetes'), L('Mostra i log', 'Shows logs')], 0, L('Dichiarativo.', 'Declarative.')),
            Q('w3', L('Che cosa sono i nodi?', 'What are nodes?'), [L('Le macchine che eseguono i container', 'The machines running containers'), L('I file YAML', 'The YAML files'), L('Gli utenti', 'The users'), L('Le immagini', 'The images')], 0, L('Il piano di controllo decide.', 'The control plane decides.')),
        ], task('bash', L('Applica il file `deploy.yaml` e poi elenca i pod.', 'Apply the `deploy.yaml` file and then list the pods.'), '', [re('kubectl\s+apply\s+-f\s+deploy\.yaml', L('Applichi il file', 'You apply the file')), re('kubectl\s+get\s+pods?', L('Elenchi i pod', 'You list the pods'))], '', "kubectl apply -f deploy.yaml\nkubectl get pods"), [
            dMatch(pMatch(), [['Cluster', L('Insieme di macchine', 'Set of machines')], [L('Nodo', 'Node'), L('Esegue i container', 'Runs containers')], [L('Piano di controllo', 'Control plane'), L('Decide dove e cosa', 'Decides where and what')], ['kubectl', L('Lo strumento da riga di comando', 'The command-line tool')], [L('Stato desiderato', 'Desired state'), L('Descritto in YAML', 'Described in YAML')]], L('Le basi di Kubernetes.', 'Kubernetes basics.')),
            dFlash(pFlash(), [[L('Kubernetes riavvia i container caduti', 'Kubernetes restarts crashed containers'), true], [L('Kubernetes gira solo su una macchina', 'Kubernetes runs on one machine only'), false], [L('Si può provare Kubernetes in locale', 'You can try Kubernetes locally'), true], [L('In Kubernetes si scrive ogni passo a mano', 'In Kubernetes you script every step by hand'), false]], L('Dichiarare, non comandare.', 'Declare, don’t command.')),
        ]),
        lesson('deployments', L('Pod e Deployment', 'Pods and Deployments'), [
            P('Il **pod** è la più piccola unità: uno o più container che girano insieme. Di solito non si creano pod a mano ma un **Deployment**, che dice quante **repliche** del pod tenere attive e come aggiornarle. Se un pod muore, il Deployment ne crea un altro.', 'The **pod** is the smallest unit: one or more containers running together. You usually do not create pods by hand but a **Deployment**, which says how many pod **replicas** to keep running and how to update them. If a pod dies, the Deployment creates another.'),
            C('apiVersion: apps/v1
kind: Deployment
metadata:
  name: web
spec:
  replicas: 3
  selector:
    matchLabels: { app: web }
  template:
    metadata:
      labels: { app: web }
    spec:
      containers:
        - name: web
          image: registro.esempio.it/web:1.4
          ports: [{ containerPort: 3000 }]
          resources:
            requests: { cpu: "100m", memory: "128Mi" }
            limits: { memory: "256Mi" }', 'yaml'),
            P('Cambiando l’immagine (`web:1.5`) e riapplicando, il Deployment fa un **aggiornamento progressivo**: sostituisce i pod pochi alla volta, senza interruzioni. Se qualcosa va storto: `kubectl rollout undo deployment/web`. Le **etichette** (labels) collegano Deployment, pod e servizi.', 'Changing the image (`web:1.5`) and reapplying, the Deployment does a **rolling update**: it replaces pods a few at a time, without downtime. If something goes wrong: `kubectl rollout undo deployment/web`. **Labels** connect Deployments, pods and services.'),
        ], [
            Q('d1', L('Che cos’è un pod?', 'What is a pod?'), [L('Uno o più container che girano insieme', 'One or more containers running together'), L('Una macchina fisica', 'A physical machine'), L('Un file di log', 'A log file'), L('Un utente', 'A user')], 0, L('L’unità più piccola.', 'The smallest unit.')),
            Q('d2', L('Che cosa fa `replicas: 3`?', 'What does `replicas: 3` do?'), [L('Tiene attivi 3 pod', 'Keeps 3 pods running'), L('Crea 3 cluster', 'Creates 3 clusters'), L('Riavvia 3 volte', 'Restarts 3 times'), L('Usa 3 immagini diverse', 'Uses 3 different images')], 0, L('Se uno cade, ne nasce un altro.', 'If one falls, another is born.')),
            Q('d3', L('Come annulli un aggiornamento andato male?', 'How do you undo a bad update?'), ['kubectl rollout undo deployment/web', 'kubectl delete cluster', 'docker stop', 'git revert'], 0, L('Torna alla versione precedente.', 'It goes back to the previous version.')),
        ], task('yaml', L('Completa il Deployment `api` con `replicas: 2` e un container `api` dall’immagine `esempio/api:2.0`.', 'Complete the `api` Deployment with `replicas: 2` and an `api` container from the `esempio/api:2.0` image.'), "apiVersion: apps/v1\nkind: Deployment\nmetadata:\n  name: api\nspec:\n", [re('^\s+replicas:\s*2\s*$', L('Due repliche', 'Two replicas')), re('-\s*name:\s*api\s*\n\s+image:\s*esempio/api:2\.0', L('Il container giusto', 'The right container'))], '', "spec:\n  replicas: 2\n  selector:\n    matchLabels: { app: api }\n  template:\n    metadata:\n      labels: { app: api }\n    spec:\n      containers:\n        - name: api\n          image: esempio/api:2.0"), [
            dMatch(pMatch(), [['Pod', L('Container insieme', 'Containers together')], ['Deployment', L('Gestisce le repliche', 'Manages replicas')], ['replicas', L('Quanti pod', 'How many pods')], ['labels', L('Collegano le risorse', 'Connect resources')], ['rollout undo', L('Torna indietro', 'Rolls back')]], L('Far girare le app.', 'Running apps.')),
            dFlash(pFlash(), [[L('Un Deployment ricrea i pod morti', 'A Deployment recreates dead pods'), true], [L('L’aggiornamento progressivo ferma tutto per un’ora', 'A rolling update stops everything for an hour'), false], [L('Le risorse limitano CPU e memoria', 'Resources limit CPU and memory'), true], [L('I pod si creano sempre a mano', 'Pods are always created by hand'), false]], L('Repliche e aggiornamenti.', 'Replicas and updates.')),
        ]),
        lesson('services', L('Service e Ingress', 'Services and Ingress'), [
            P('I pod nascono e muoiono e cambiano indirizzo. Un **Service** dà loro un nome e un indirizzo stabili e distribuisce il traffico tra le repliche, usando le etichette per trovarle. Un **Ingress** porta il traffico web dall’esterno ai servizi, in base a dominio e percorso, e gestisce HTTPS.', 'Pods are born, die and change address. A **Service** gives them a stable name and address and spreads traffic across replicas, using labels to find them. An **Ingress** brings outside web traffic to services, based on domain and path, and handles HTTPS.'),
            C('apiVersion: v1
kind: Service
metadata:
  name: web
spec:
  selector: { app: web }
  ports: [{ port: 80, targetPort: 3000 }]
---
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: web
spec:
  rules:
    - host: www.esempio.it
      http:
        paths:
          - path: /
            pathType: Prefix
            backend: { service: { name: web, port: { number: 80 } } }', 'yaml'),
            P('Dentro il cluster, gli altri pod raggiungono il servizio con il nome `web` (come in Docker Compose). Il `selector` del Service deve corrispondere alle etichette dei pod, altrimenti il traffico non arriva da nessuna parte.', 'Inside the cluster, other pods reach the service by the name `web` (as in Docker Compose). The Service’s `selector` must match the pods’ labels, otherwise traffic goes nowhere.'),
        ], [
            Q('s1', L('A che cosa serve un Service?', 'What is a Service for?'), [L('Dare un indirizzo stabile ai pod e distribuire il traffico', 'Giving pods a stable address and spreading traffic'), L('Salvare i file', 'Saving files'), L('Costruire immagini', 'Building images'), L('Gestire gli utenti', 'Managing users')], 0, L('I pod cambiano, il Service resta.', 'Pods change, the Service stays.')),
            Q('s2', L('Che cosa fa un Ingress?', 'What does an Ingress do?'), [L('Porta il traffico esterno ai servizi per dominio e percorso', 'Routes outside traffic to services by domain and path'), L('Cancella i pod', 'Deletes pods'), L('Scala il cluster', 'Scales the cluster'), L('Fa i backup', 'Makes backups')], 0, L('E gestisce HTTPS.', 'And handles HTTPS.')),
            Q('s3', L('Che cosa succede se il selector non corrisponde alle etichette?', 'What happens if the selector does not match the labels?'), [L('Il traffico non arriva ai pod', 'Traffic does not reach the pods'), L('Funziona lo stesso', 'It works anyway'), L('Il cluster si spegne', 'The cluster shuts down'), L('Si creano nuovi pod', 'New pods are created')], 0, L('Errore comune.', 'Common mistake.')),
        ], task('yaml', L('Scrivi un Service `api` che seleziona i pod con etichetta `app: api` e inoltra la porta `80` alla `8080` dei pod.', 'Write an `api` Service selecting pods labelled `app: api` and forwarding port `80` to the pods’ `8080`.'), "apiVersion: v1\nkind: Service\n", [re('name:\s*api', L('Il nome', 'The name')), re('selector:\s*(\{\s*app:\s*api\s*\}|\n\s+app:\s*api)', L('Il selettore', 'The selector')), re('port:\s*80\b[\s\S]*targetPort:\s*8080', L('Le porte', 'The ports'))], '', "apiVersion: v1\nkind: Service\nmetadata:\n  name: api\nspec:\n  selector: { app: api }\n  ports: [{ port: 80, targetPort: 8080 }]"), [
            dMatch(pMatch(), [['Service', L('Indirizzo stabile', 'Stable address')], ['Ingress', L('Traffico dall’esterno', 'Outside traffic')], ['selector', L('Trova i pod per etichetta', 'Finds pods by label')], ['targetPort', L('Porta del container', 'Container port')]], L('Raggiungere le app.', 'Reaching apps.')),
            dFlash(pFlash(), [[L('Un Service distribuisce il traffico tra le repliche', 'A Service spreads traffic across replicas'), true], [L('I pod hanno sempre lo stesso indirizzo', 'Pods always keep the same address'), false], [L('Un Ingress può instradare per dominio', 'An Ingress can route by domain'), true], [L('Il selector non serve', 'The selector is useless'), false]], L('Nomi stabili per cose mutevoli.', 'Stable names for changing things.')),
        ]),
        lesson('config', L('Configurazione, segreti e scalabilità', 'Configuration, secrets and scaling'), [
            P('La configurazione sta fuori dall’immagine: una **ConfigMap** per i valori normali, un **Secret** per password e chiavi, passati ai container come variabili d’ambiente o file. Attenzione: i Secret sono solo codificati in base64, non cifrati, a meno di attivare la cifratura o usare un gestore esterno.', 'Configuration lives outside the image: a **ConfigMap** for normal values, a **Secret** for passwords and keys, passed to containers as environment variables or files. Beware: Secrets are only base64-encoded, not encrypted, unless you enable encryption or use an external manager.'),
            C('kubectl create secret generic db --from-literal=password=segreta
kubectl scale deployment web --replicas=5
kubectl autoscale deployment web --cpu-percent=70 --min=2 --max=10', 'bash'),
            C('env:
  - name: LIVELLO_LOG
    valueFrom: { configMapKeyRef: { name: config-web, key: livello } }
  - name: DB_PASSWORD
    valueFrom: { secretKeyRef: { name: db, key: password } }', 'yaml'),
            P('Con l’**autoscaling** il numero di pod cresce o cala da solo in base al carico (per esempio sopra il 70% di CPU). Le **sonde** (readiness e liveness) dicono a Kubernetes quando un pod è pronto a ricevere traffico e quando va riavviato.', 'With **autoscaling** the number of pods grows or shrinks by itself based on load (for example above 70% CPU). **Probes** (readiness and liveness) tell Kubernetes when a pod is ready for traffic and when it must be restarted.'),
        ], [
            Q('k1', L('Dove metti una password per un pod?', 'Where do you put a password for a pod?'), [L('In un Secret', 'In a Secret'), L('Nell’immagine', 'In the image'), L('In una ConfigMap pubblica', 'In a public ConfigMap'), L('Nel nome del pod', 'In the pod name')], 0, L('E proteggi l’accesso ai Secret.', 'And protect access to Secrets.')),
            Q('k2', L('I Secret di Kubernetes sono cifrati di default?', 'Are Kubernetes Secrets encrypted by default?'), [L('No, solo codificati in base64', 'No, only base64-encoded'), L('Sì, sempre', 'Yes, always'), L('Solo il martedì', 'Only on Tuesdays'), L('Non esistono', 'They do not exist')], 0, L('Attiva la cifratura.', 'Enable encryption.')),
            Q('k3', L('Che cosa fa l’autoscaling?', 'What does autoscaling do?'), [L('Adatta il numero di pod al carico', 'Adapts pod count to load'), L('Aggiorna Kubernetes', 'Updates Kubernetes'), L('Cancella i log', 'Deletes logs'), L('Cambia immagine', 'Changes image')], 0, L('Tra un minimo e un massimo.', 'Between a minimum and maximum.')),
        ], task('bash', L('Porta il Deployment `web` a 4 repliche con `kubectl scale`.', 'Scale the `web` Deployment to 4 replicas with `kubectl scale`.'), '', [re('kubectl\s+scale\s+(deployment|deploy)[/\s]web\s+--replicas[=\s]4', L('Scali a 4', 'You scale to 4'))], '', 'kubectl scale deployment web --replicas=4'), [
            dMatch(pMatch(), [['ConfigMap', L('Configurazione normale', 'Normal configuration')], ['Secret', L('Password e chiavi', 'Passwords and keys')], ['autoscale', L('Pod in base al carico', 'Pods by load')], ['readinessProbe', L('Pronto per il traffico?', 'Ready for traffic?')], ['livenessProbe', L('Va riavviato?', 'Needs restarting?')]], L('Configurare e crescere.', 'Configuring and growing.')),
            dFlash(pFlash(), [[L('I Secret sono codificati in base64', 'Secrets are base64-encoded'), true], [L('La configurazione va scritta nell’immagine', 'Configuration belongs in the image'), false], [L('Le sonde aiutano a riavviare i pod bloccati', 'Probes help restart stuck pods'), true], [L('L’autoscaling aumenta solo, mai diminuisce', 'Autoscaling only grows, never shrinks'), false]], L('Elastico e configurabile.', 'Elastic and configurable.')),
        ]),
    ],
];
