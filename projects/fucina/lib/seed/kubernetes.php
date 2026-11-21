<?php
/** Kubernetes: why an orchestrator, pods and deployments, services and ingress, configuration, secrets and scaling, debugging,
 *  persistent storage, Jobs/CronJobs/DaemonSets, namespaces and RBAC, Gateway API, Helm and Kustomize. */

/** A cluster: the control plane that decides and the nodes that run the pods, each with its kubelet. */
if (!function_exists('fig_k8s_cluster')) {
    function fig_k8s_cluster(array $t): string
    {
        $nodes = '';
        foreach ([1, 2, 3] as $i) {
            $x = 16 + ($i - 1) * 208; $cx = $x + 96;
            $pods = '';
            foreach (range(0, $i === 2 ? 1 : 2) as $k) { $px = $x + 14 + $k * 58; $pods .= "<rect x=\"$px\" y=\"222\" width=\"50\" height=\"40\" rx=\"8\" fill=\"#DCEBFF\" stroke=\"#2F8CF0\" stroke-width=\"2\"/><text x=\"" . ($px + 25) . "\" y=\"247\" text-anchor=\"middle\" font-size=\"13\">pod</text>"; }
            $nodes .= "<rect x=\"$x\" y=\"150\" width=\"192\" height=\"126\" rx=\"12\" fill=\"#F1F0F8\" stroke=\"#8A87B0\" stroke-width=\"2\"/><text x=\"" . ($x + 14) . "\" y=\"174\" font-weight=\"700\">{$t[1]} $i</text>"
                    . "<rect x=\"" . ($x + 100) . "\" y=\"160\" width=\"80\" height=\"26\" rx=\"8\" fill=\"#D5F6EA\" stroke=\"#0B7F5C\" stroke-width=\"2\"/><text x=\"" . ($x + 140) . "\" y=\"178\" text-anchor=\"middle\" font-size=\"13\" font-family=\"ui-monospace, Menlo, monospace\">kubelet</text>"
                    . "<text x=\"" . ($x + 14) . "\" y=\"208\" font-size=\"13\">container runtime</text>" . $pods
                    . "<path d=\"M$cx,112 V150\" stroke=\"#7048E8\" stroke-width=\"2\" stroke-dasharray=\"5 4\"/>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="16" y="14" width="608" height="98" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="32" y="40" font-weight="700" fill="#5A35C9">{$t[0]}</text>
<text x="608" y="40" text-anchor="end" font-size="13">{$t[2]}</text>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" text-anchor="middle" stroke-width="2">
<rect x="32" y="56" width="150" height="38" rx="8" fill="#FFFFFF" stroke="#7048E8"/><text x="107" y="80" font-weight="700">kube-apiserver</text>
<rect x="196" y="56" width="80" height="38" rx="8" fill="#FFFFFF" stroke="#7048E8"/><text x="236" y="80">etcd</text>
<rect x="290" y="56" width="116" height="38" rx="8" fill="#FFFFFF" stroke="#7048E8"/><text x="348" y="80">scheduler</text>
<rect x="420" y="56" width="188" height="38" rx="8" fill="#FFFFFF" stroke="#7048E8"/><text x="514" y="80">controller-manager</text>
</g>
{$nodes}
</svg>
SVG;
    }
}

/** A Deployment with three replicas during a rolling update: the pods move from web:1.4 to web:1.5 one at a time. */
if (!function_exists('fig_k8s_rolling')) {
    function fig_k8s_rolling(): string
    {
        $pods = '';
        foreach ([0, 1, 2] as $i) {
            $x = 40 + $i * 200; $cx = $x + 80; $n = $i + 1;
            $pods .= "<path d=\"M320,74 C320,110 $cx,104 $cx,142\" fill=\"none\" stroke=\"#8A87B0\" stroke-width=\"2\"/>"
                   . "<rect x=\"$x\" y=\"142\" width=\"160\" height=\"84\" rx=\"12\" fill=\"#DCEBFF\" stroke=\"#2F8CF0\" stroke-width=\"2\"/><text x=\"$cx\" y=\"172\" text-anchor=\"middle\" font-weight=\"700\">pod</text><text x=\"$cx\" y=\"204\" text-anchor=\"middle\" font-family=\"ui-monospace, Menlo, monospace\" font-size=\"14\" fill=\"#1B66BC\">web:1.4</text>"
                   . "<g class=\"n$n\"><rect x=\"$x\" y=\"142\" width=\"160\" height=\"84\" rx=\"12\" fill=\"#D5F6EA\" stroke=\"#0B7F5C\" stroke-width=\"3\"/><text x=\"$cx\" y=\"172\" text-anchor=\"middle\" font-weight=\"700\">pod</text><text x=\"$cx\" y=\"204\" text-anchor=\"middle\" font-family=\"ui-monospace, Menlo, monospace\" font-size=\"14\" font-weight=\"700\" fill=\"#0B7F5C\">web:1.5</text></g>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.n1{opacity:1;animation:r1 9s infinite}.n2{opacity:0;animation:r2 9s infinite}.n3{opacity:0;animation:r3 9s infinite}@keyframes r1{0%,10%{opacity:0}18%,90%{opacity:1}100%{opacity:0}}@keyframes r2{0%,35%{opacity:0}43%,90%{opacity:1}100%{opacity:0}}@keyframes r3{0%,60%{opacity:0}68%,90%{opacity:1}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.n1,.n2,.n3{animation:none}}</style>
<rect x="190" y="16" width="260" height="58" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<text x="320" y="40" text-anchor="middle" font-weight="700">Deployment web</text>
<text x="320" y="62" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">replicas: 3</text>
{$pods}
<text x="320" y="252" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">rolling update · web:1.4 → web:1.5</text>
</svg>
SVG;
    }
}

/** Outside traffic through an Ingress to a Service, which sends it only to the pods whose labels match its selector. */
if (!function_exists('fig_k8s_service')) {
    function fig_k8s_service(): string
    {
        $pods = '';
        foreach ([['app=web', 1], ['app=web', 1], ['app=web', 1], ['app=db', 0]] as $i => [$lab, $on]) {
            $y = 24 + $i * 58; $cy = $y + 22;
            $pods .= $on
                ? "<path d=\"M444,135 C462,135 462,$cy 484,$cy\" fill=\"none\" stroke=\"#1E1C52\" stroke-width=\"2\" marker-end=\"url(#ah)\"/><rect x=\"490\" y=\"$y\" width=\"134\" height=\"44\" rx=\"10\" fill=\"#D5F6EA\" stroke=\"#0B7F5C\" stroke-width=\"2\"/>"
                : "<rect x=\"490\" y=\"$y\" width=\"134\" height=\"44\" rx=\"10\" fill=\"#F1F0F8\" stroke=\"#8A87B0\" stroke-width=\"2\" stroke-dasharray=\"6 4\"/>";
            $pods .= "<text x=\"506\" y=\"" . ($cy + 5) . "\" font-weight=\"700\">pod</text><text x=\"610\" y=\"" . ($cy + 5) . "\" text-anchor=\"end\" font-family=\"ui-monospace, Menlo, monospace\" font-size=\"13\">$lab</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 262" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="110" width="88" height="50" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="56" y="140" text-anchor="middle" font-weight="700">Internet</text>
<rect x="126" y="98" width="156" height="74" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="204" y="126" text-anchor="middle" font-weight="700">Ingress</text>
<text x="204" y="150" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">www.esempio.it/</text>
<rect x="298" y="98" width="146" height="74" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="371" y="126" text-anchor="middle" font-weight="700">Service web</text>
<text x="371" y="150" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="13">selector app=web</text>
<path d="M102,135 H118" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/><path d="M283,135 H292" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
{$pods}
</svg>
SVG;
    }
}

/** Readiness and liveness compared: a failing readiness probe takes the pod out of the Service, a failing liveness probe restarts the container. */
if (!function_exists('fig_k8s_probes')) {
    function fig_k8s_probes(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker>
<marker id="ag" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#E8590C"/></marker></defs>
<rect x="8" y="8" width="306" height="254" rx="12" fill="#F1F0F8"/><rect x="326" y="8" width="306" height="254" rx="12" fill="#F1F0F8"/>
<text x="24" y="36" font-weight="700"><tspan font-family="ui-monospace, Menlo, monospace">readinessProbe</tspan> {$t[0]}</text>
<text x="342" y="36" font-weight="700"><tspan font-family="ui-monospace, Menlo, monospace">livenessProbe</tspan> {$t[0]}</text>
<rect x="96" y="54" width="130" height="40" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="161" y="79" text-anchor="middle" font-weight="700">Service</text>
<path d="M130,96 L86,140" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<path d="M192,96 L232,136" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/><path d="M200,124 L222,112" stroke="#F03E82" stroke-width="3"/>
<rect x="24" y="146" width="120" height="56" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="84" y="171" text-anchor="middle" font-weight="700">pod A</text><text x="84" y="191" text-anchor="middle" font-size="13">{$t[1]}</text>
<rect x="178" y="146" width="120" height="56" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4"/><text x="238" y="171" text-anchor="middle" font-weight="700">pod B</text><text x="238" y="191" text-anchor="middle" font-size="13">{$t[2]}</text>
<text x="161" y="230" text-anchor="middle" font-size="13">{$t[3]}</text>
<text x="161" y="250" text-anchor="middle" font-size="13">{$t[4]}</text>
<rect x="404" y="70" width="150" height="132" rx="12" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="479" y="96" text-anchor="middle" font-weight="700">pod</text>
<rect x="430" y="112" width="98" height="40" rx="8" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="479" y="137" text-anchor="middle" font-size="13">container</text>
<path d="M440,162 A42,22 0 1 0 520,162" fill="none" stroke="#E8590C" stroke-width="2" marker-end="url(#ag)"/>
<text x="479" y="230" text-anchor="middle" font-size="13" font-weight="700" fill="#B84508">{$t[5]}</text>
<text x="479" y="250" text-anchor="middle" font-size="13">{$t[6]}</text>
</svg>
SVG;
    }
}

/** The storage chain: the pod mounts a PVC, the PVC is bound to a PV that a StorageClass created on a cloud disk. */
if (!function_exists('fig_k8s_storage')) {
    function fig_k8s_storage(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="16" y="128" width="110" height="64" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="71" y="156" text-anchor="middle" font-weight="700">pod</text><text x="71" y="178" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">db-0</text>
<rect x="164" y="128" width="140" height="64" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="234" y="156" text-anchor="middle" font-weight="700">PVC</text><text x="234" y="178" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">10Gi · RWO</text>
<rect x="342" y="128" width="120" height="64" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="402" y="156" text-anchor="middle" font-weight="700">PV</text><text x="402" y="178" text-anchor="middle" font-size="13">{$t[0]}</text>
<path d="M512,124 v64 a56,14 0 0 0 112,0 v-64" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><ellipse cx="568" cy="124" rx="56" ry="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="568" y="168" text-anchor="middle" font-size="13" font-weight="700">{$t[1]}</text>
<rect x="302" y="22" width="200" height="50" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="402" y="52" text-anchor="middle" font-weight="700">StorageClass</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ah)"><path d="M128,160 H156"/><path d="M306,160 H334"/><path d="M464,160 H504"/><path d="M402,74 V120" stroke-dasharray="5 4"/></g>
<g font-size="13" text-anchor="middle"><text x="142" y="214">{$t[2]}</text><text x="320" y="214">{$t[3]}</text><text x="484" y="214">{$t[4]}</text><text x="458" y="102" text-anchor="start">{$t[5]}</text></g>
</svg>
SVG;
    }
}

/** The Gateway API roles: the platform team runs GatewayClass and Gateway, each app team writes its HTTPRoute towards its Service. */
if (!function_exists('fig_k8s_gateway')) {
    function fig_k8s_gateway(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="8" y="8" width="624" height="82" rx="12" fill="#ECE6FF"/>
<rect x="8" y="104" width="624" height="188" rx="12" fill="#F1F0F8"/>
<text x="24" y="32" font-size="13" font-weight="700" fill="#5A35C9">{$t[0]}</text>
<text x="24" y="128" font-size="13" font-weight="700">{$t[1]}</text>
<rect x="40" y="40" width="170" height="42" rx="10" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/><text x="125" y="66" text-anchor="middle" font-weight="700">GatewayClass</text>
<rect x="260" y="36" width="220" height="48" rx="10" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><text x="370" y="57" text-anchor="middle" font-weight="700">Gateway</text><text x="370" y="76" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">:443 · TLS</text>
<path d="M212,61 H252" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<rect x="120" y="140" width="190" height="52" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="215" y="162" text-anchor="middle" font-weight="700">HTTPRoute</text><text x="215" y="182" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">/ → web</text>
<rect x="400" y="140" width="190" height="52" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="495" y="162" text-anchor="middle" font-weight="700">HTTPRoute</text><text x="495" y="182" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">/api → api</text>
<rect x="140" y="226" width="150" height="46" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="215" y="255" text-anchor="middle" font-weight="700">Service web</text>
<rect x="420" y="226" width="150" height="46" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="495" y="255" text-anchor="middle" font-weight="700">Service api</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ah)"><path d="M340,86 C340,110 215,110 215,134"/><path d="M400,86 C400,110 495,110 495,134"/><path d="M215,194 V220"/><path d="M495,194 V220"/></g>
</svg>
SVG;
    }
}

return [
    'id' => 'kubernetes', 'family' => 'kubernetes', 'after' => 'docker',
    'title' => L('Kubernetes', 'Kubernetes'), 'short' => L('Kubernetes', 'Kubernetes'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Far girare container su tante macchine: pod e deployment, service, ingress e Gateway API, ConfigMap e Secret, scalabilità e aggiornamenti, volumi persistenti, job, permessi, Helm e Kustomize.', 'Running containers across many machines: pods and deployments, services, ingress and Gateway API, ConfigMaps and Secrets, scaling and updates, persistent volumes, jobs, permissions, Helm and Kustomize.'),
    'lessons' => [
        lesson('why', L('Perché un orchestratore', 'Why an orchestrator'), [
            P('Quando i container sono tanti e girano su più macchine, serve qualcuno che decida dove avviarli, li riavvii se cadono, li distribuisca e li aggiorni senza interruzioni. **Kubernetes** (K8s) è l’**orchestratore** più usato: tu descrivi lo **stato desiderato** in file YAML e il cluster lavora continuamente per raggiungerlo.', 'When containers are many and run on several machines, someone must decide where to start them, restart them if they crash, spread them and update them without downtime. **Kubernetes** (K8s) is the most used **orchestrator**: you describe the **desired state** in YAML files and the cluster works continuously to reach it.'),
            C('kubectl get nodes                 # le macchine del cluster
kubectl apply -f app.yaml         # applica lo stato desiderato
kubectl get pods                  # cosa gira
kubectl describe pod web-7d9c…    # dettagli ed eventi
kubectl logs web-7d9c…            # log', 'bash'),
            P('Un **cluster** ha un piano di controllo (che decide) e dei **nodi** (che eseguono i container). Per provare in locale esistono strumenti come kind, minikube o Docker Desktop; in produzione si usano servizi gestiti dei fornitori cloud.', 'A **cluster** has a control plane (which decides) and **nodes** (which run the containers). To try it locally there are tools like kind, minikube or Docker Desktop; in production you use cloud providers’ managed services.'),
            Fig(L(fig_k8s_cluster(['Piano di controllo', 'Nodo', 'decide dove e quando']), fig_k8s_cluster(['Control plane', 'Node', 'decides where and when'])),
                L('In alto il piano di controllo con kube-apiserver, etcd, scheduler e controller-manager. Sotto tre nodi, ognuno con il suo kubelet, un container runtime e alcuni pod; linee tratteggiate collegano il piano di controllo a ogni nodo.', 'At the top the control plane with kube-apiserver, etcd, scheduler and controller-manager. Below three nodes, each with its kubelet, a container runtime and some pods; dashed lines link the control plane to every node.'),
                L('`kubectl` parla solo con l’API server; sui nodi il kubelet avvia i pod che lo scheduler gli assegna.', '`kubectl` talks only to the API server; on the nodes the kubelet starts the pods the scheduler assigns to it.')),
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
            Fig(fig_k8s_rolling(),
                L('Un Deployment web con replicas: 3 e tre pod sotto di lui. Durante il rolling update i pod passano da web:1.4 a web:1.5 uno alla volta: qui il primo è già aggiornato, gli altri due no.', 'A Deployment web with replicas: 3 and three pods below it. During the rolling update the pods move from web:1.4 to web:1.5 one at a time: here the first is already updated, the other two are not.'),
                L('Durante l’aggiornamento ci sono sempre pod pronti a rispondere.', 'During the update there are always pods ready to answer.')),
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
            Fig(fig_k8s_service(),
                L('Il traffico da Internet entra nell’Ingress per www.esempio.it, passa al Service web con selector app=web e da lì a tre pod con l’etichetta app=web. Un quarto pod, con app=db, non riceve nulla.', 'Traffic from the Internet enters the Ingress for www.esempio.it, goes to the Service web with selector app=web and from there to three pods labelled app=web. A fourth pod, labelled app=db, gets nothing.'),
                L('Il Service trova i pod con le etichette, non con gli indirizzi.', 'The Service finds pods by their labels, not by their addresses.')),
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
            P('Dentro il cluster, gli altri pod raggiungono il servizio con il nome `web` (come in Docker Compose). Il `selector` del Service deve corrispondere alle etichette dei pod, altrimenti il traffico non arriva da nessuna parte. L’Ingress funziona solo con un **controller** installato e oggi è stabile ma congelato: le novità arrivano nella più recente **Gateway API**.', 'Inside the cluster, other pods reach the service by the name `web` (as in Docker Compose). The Service’s `selector` must match the pods’ labels, otherwise traffic goes nowhere. Ingress works only with an installed **controller** and today it is stable but frozen: new features go into the newer **Gateway API**.'),
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
kubectl autoscale deployment web --cpu=70% --min=2 --max=10', 'bash'),
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
        lesson('debug', L('Diagnosi, salute e aggiornamenti', 'Diagnosis, health and updates'), [
            P('Quando un pod non parte, i comandi da conoscere sono: `kubectl get pods` (lo stato), `kubectl describe pod` (gli **eventi**, per esempio un’immagine non trovata) e `kubectl logs` (l’output dell’applicazione). Stati tipici: `CrashLoopBackOff`, `ImagePullBackOff`, `Pending`.', 'When a pod will not start, the commands to know are: `kubectl get pods` (status), `kubectl describe pod` (the **events**, for example an image not found) and `kubectl logs` (the app’s output). Typical states: `CrashLoopBackOff`, `ImagePullBackOff`, `Pending`.'),
            C('kubectl get pods
kubectl describe pod web-7d9f-abcde
kubectl logs web-7d9f-abcde --previous
kubectl set image deployment/web web=registro/web:1.3
kubectl rollout status deployment/web
kubectl rollout undo deployment/web', 'bash'),
            P('Le **sonde** dicono a Kubernetes come sta l’app: la **readiness** decide se il pod riceve traffico, la **liveness** riavvia il container bloccato. Un Deployment aggiorna i pod **a scaglioni** (rolling update) e con `rollout undo` torni alla versione precedente.', '**Probes** tell Kubernetes how the app is doing: **readiness** decides whether the pod gets traffic, **liveness** restarts a stuck container. A Deployment updates pods **in batches** (rolling update) and with `rollout undo` you go back to the previous version.'),
            Fig(L(fig_k8s_probes(['fallisce', 'pronto', 'non pronto', 'pod B esce dal Service:', 'niente traffico, nessun riavvio', 'container riavviato', 'il pod resta, il processo riparte']), fig_k8s_probes(['fails', 'ready', 'not ready', 'pod B leaves the Service:', 'no traffic, no restart', 'container restarted', 'the pod stays, the process restarts'])),
                L('Due riquadri. A sinistra, quando fallisce la readinessProbe, il Service manda traffico solo al pod A pronto, mentre il pod B non pronto non riceve nulla e non viene riavviato. A destra, quando fallisce la livenessProbe, il container dentro il pod viene riavviato.', 'Two panels. On the left, when the readinessProbe fails, the Service sends traffic only to the ready pod A, while the not-ready pod B gets nothing and is not restarted. On the right, when the livenessProbe fails, the container inside the pod is restarted.'),
                L('Readiness decide il traffico, liveness decide il riavvio.', 'Readiness decides traffic, liveness decides restarts.')),
        ], [
            Q('kd1', L('Dove leggi gli eventi di un pod?', 'Where do you read a pod’s events?'), ['kubectl describe pod', 'kubectl get svc', 'kubectl apply', 'kubectl top'], 0, L('In fondo all’output.', 'At the bottom of the output.')),
            Q('kd2', L('Che cosa fa la readiness probe?', 'What does the readiness probe do?'), [L('Decide se il pod riceve traffico', 'Decides whether the pod gets traffic'), L('Riavvia il nodo', 'Restarts the node'), L('Scarica l’immagine', 'Pulls the image'), L('Cancella il pod', 'Deletes the pod')], 0, L('La liveness invece riavvia.', 'Liveness restarts instead.')),
            Q('kd3', L('Come torni alla versione precedente?', 'How do you go back to the previous version?'), ['kubectl rollout undo', 'kubectl delete', 'kubectl scale --replicas=0', 'kubectl logs'], 0, L('Il Deployment ricorda le versioni.', 'The Deployment remembers versions.')),
        ], task('bash', L('Mostra i log del pod `api-1` e poi annulla l’ultimo rollout del deployment `api`.', 'Show the logs of pod `api-1` and then undo the last rollout of deployment `api`.'), '', [re('kubectl\s+logs\s+api-1', L('Leggi i log', 'You read the logs')), re('kubectl\s+rollout\s+undo\s+(deployment|deploy|deployments)[/\s]api\b', L('Annulli il rollout', 'You undo the rollout'))], '', "kubectl logs api-1\nkubectl rollout undo deployment/api"), [
            dMatch(pMatch(), [['CrashLoopBackOff', L('Il container continua a crashare', 'The container keeps crashing')], ['ImagePullBackOff', L('Immagine non scaricabile', 'Image cannot be pulled')], ['Pending', L('Non ancora avviato, spesso senza un nodo adatto', 'Not started yet, often no suitable node')], ['readiness', L('Pronto per il traffico', 'Ready for traffic')], ['liveness', L('Vivo o da riavviare', 'Alive or to restart')]], L('Leggere lo stato.', 'Reading the state.')),
            dOrder(pSort(), ['kubectl get pods', 'kubectl describe pod web-1', 'kubectl logs web-1', L('Correggi e ridistribuisci', 'Fix and redeploy'), 'kubectl rollout status deployment/web'], L('Dalla diagnosi alla correzione.', 'From diagnosis to fix.')),
        ]),
        lesson('storage', L('Volumi persistenti e StatefulSet', 'Persistent volumes and StatefulSets'), [
            P('I pod sono usa e getta: quello che scrivono nel container sparisce quando vengono ricreati. Per i dati da conservare l’app chiede spazio con un **PersistentVolumeClaim** (PVC); il cluster lo soddisfa con un **PersistentVolume**, spesso creato al momento da una **StorageClass** (per esempio un disco del fornitore cloud).', 'Pods are disposable: what they write in the container vanishes when they are recreated. For data to keep, the app requests space with a **PersistentVolumeClaim** (PVC); the cluster fulfils it with a **PersistentVolume**, often created on the fly by a **StorageClass** (for example a cloud provider disk).'),
            Fig(L(fig_k8s_storage(['il disco vero', 'disco cloud', 'monta', 'legato a', 'su', 'lo crea al momento']), fig_k8s_storage(['the real disk', 'cloud disk', 'mounts', 'bound to', 'on', 'creates it on demand'])),
                L('Una catena da sinistra a destra: il pod db-0 monta un PVC da 10Gi in ReadWriteOnce, il PVC è legato a un PV, il PV sta su un disco cloud. Sopra, una StorageClass crea il PV al momento.', 'A chain from left to right: the pod db-0 mounts a 10Gi ReadWriteOnce PVC, the PVC is bound to a PV, the PV sits on a cloud disk. Above, a StorageClass creates the PV on demand.'),
                L('L’app chiede spazio (PVC) senza sapere da dove arriva (PV).', 'The app asks for space (PVC) without knowing where it comes from (PV).')),
            C('apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: caricati
spec:
  accessModes: [ReadWriteOnce]
  resources:
    requests:
      storage: 10Gi', 'yaml'),
            C('# nel template del pod
spec:
  containers:
    - name: web
      image: registro.esempio.it/web:1.4
      volumeMounts:
        - name: dati
          mountPath: /app/caricati
  volumes:
    - name: dati
      persistentVolumeClaim:
        claimName: caricati', 'yaml'),
            P('`ReadWriteOnce` vuol dire che il volume è montato in scrittura da **un nodo alla volta**; `ReadWriteMany` richiede uno storage che lo supporti. Per i database replicati c’è lo **StatefulSet**: pod con nomi stabili (`db-0`, `db-1`) e un volume per ciascuno. Molte squadre però tengono i database su servizi gestiti, fuori dal cluster.', '`ReadWriteOnce` means the volume is mounted read-write by **one node at a time**; `ReadWriteMany` needs storage that supports it. For replicated databases there is the **StatefulSet**: pods with stable names (`db-0`, `db-1`) and a volume each. Many teams, though, keep databases on managed services, outside the cluster.'),
        ], [
            Q('pv1', L('Che cos’è un PersistentVolumeClaim?', 'What is a PersistentVolumeClaim?'), [L('Un pod che contiene un database', 'A pod containing a database'), L('Una richiesta di spazio persistente fatta per un’app', 'A request for persistent space made for an app'), L('Un backup automatico del cluster', 'An automatic cluster backup'), L('Un nodo riservato ai dischi', 'A node reserved for disks')], 1, L('Il cluster la soddisfa con un PersistentVolume.', 'The cluster fulfils it with a PersistentVolume.')),
            Q('pv2', L('Perché usare uno StatefulSet per un database replicato?', 'Why use a StatefulSet for a replicated database?'), [L('Scala più in fretta di un Deployment', 'It scales faster than a Deployment'), L('Non ha bisogno di immagini', 'It needs no images'), L('Dà nomi stabili e un volume a ogni replica', 'It gives stable names and a volume to each replica'), L('Cifra i dati in automatico', 'It encrypts data automatically')], 2, L('`db-0` resta `db-0`, con i suoi dati.', '`db-0` stays `db-0`, with its data.')),
            Q('pv3', L('Che cosa significa `ReadWriteOnce`?', 'What does `ReadWriteOnce` mean?'), [L('Il volume si può scrivere una sola volta', 'The volume can be written only once'), L('Tutti i pod leggono, uno solo scrive', 'All pods read, only one writes'), L('Il volume si cancella dopo la prima lettura', 'The volume is deleted after the first read'), L('Lo monta in scrittura un solo nodo alla volta', 'It is mounted read-write by one node at a time')], 3, L('Per più nodi serve ReadWriteMany.', 'Several nodes need ReadWriteMany.')),
        ], task('yaml', L('Scrivi il PVC `foto` con `accessModes` `ReadWriteOnce` e una richiesta di `5Gi`.', 'Write the `foto` PVC with `accessModes` `ReadWriteOnce` and a request of `5Gi`.'), "apiVersion: v1\nkind: PersistentVolumeClaim\n", [re('name:\s*["\']?foto["\']?\s*$', L('Il nome', 'The name')), re('accessModes:\s*(\[\s*["\']?ReadWriteOnce["\']?\s*\]|\n\s+-\s*["\']?ReadWriteOnce)', L('La modalità di accesso', 'The access mode')), re('storage:\s*["\']?5Gi["\']?', L('Lo spazio richiesto', 'The requested space'))], '', "apiVersion: v1\nkind: PersistentVolumeClaim\nmetadata:\n  name: foto\nspec:\n  accessModes: [ReadWriteOnce]\n  resources:\n    requests:\n      storage: 5Gi"), [
            dMatch(pMatch(), [['PersistentVolumeClaim', L('Richiesta di spazio', 'Request for space')], ['PersistentVolume', L('Lo spazio vero e proprio', 'The actual space')], ['StorageClass', L('Tipo di disco e creazione automatica', 'Disk type and automatic creation')], ['StatefulSet', L('Nomi stabili, un volume a testa', 'Stable names, a volume each')], ['ReadWriteOnce', L('Un nodo alla volta', 'One node at a time')]], L('Dati che restano.', 'Data that stays.')),
            dFlash(pFlash(), [[L('I file scritti nel container senza volume sopravvivono alla ricreazione del pod', 'Files written in the container without a volume survive the pod being recreated'), false], [L('I pod di uno StatefulSet si chiamano db-0, db-1…', 'A StatefulSet’s pods are named db-0, db-1…'), true], [L('ReadWriteMany funziona con qualsiasi storage', 'ReadWriteMany works with any storage'), false], [L('Una StorageClass può creare il volume quando serve', 'A StorageClass can create the volume when needed'), true]], L('Persistenza nel cluster.', 'Persistence in the cluster.')),
        ]),
        lesson('workloads', L('Job, CronJob e DaemonSet', 'Jobs, CronJobs and DaemonSets'), [
            P('Non tutto è un server sempre acceso. Un **Job** esegue un pod **fino alla fine** (una migrazione, un’importazione) e lo riprova se fallisce, fino a `backoffLimit`. Un **CronJob** crea dei Job a orario, con la sintassi di cron. Un **DaemonSet** fa girare un pod su **ogni nodo**, per esempio l’agente che raccoglie log o metriche.', 'Not everything is an always-on server. A **Job** runs a pod **to completion** (a migration, an import) and retries it on failure, up to `backoffLimit`. A **CronJob** creates Jobs on a schedule, with cron syntax. A **DaemonSet** runs a pod on **every node**, for example the agent collecting logs or metrics.'),
            C('apiVersion: batch/v1
kind: CronJob
metadata:
  name: backup-notturno
spec:
  schedule: "0 3 * * *"          # ogni giorno alle 3:00
  timeZone: "Europe/Rome"
  concurrencyPolicy: Forbid      # mai due backup insieme
  jobTemplate:
    spec:
      backoffLimit: 2
      template:
        spec:
          restartPolicy: OnFailure
          containers:
            - name: backup
              image: registro.esempio.it/backup:1.0
              args: ["--destinazione", "s3://backup-fucina"]', 'yaml'),
            C('kubectl create job prova --from=cronjob/backup-notturno   # lancialo subito
kubectl get jobs
kubectl logs job/prova', 'bash'),
            P('I pod di un Job devono avere `restartPolicy: OnFailure` o `Never`, non `Always`: devono poter finire. Quando aggiungi un nodo al cluster, il DaemonSet ci mette da solo il suo pod.', 'A Job’s pods must have `restartPolicy: OnFailure` or `Never`, not `Always`: they must be able to finish. When you add a node to the cluster, the DaemonSet places its pod there by itself.'),
        ], [
            Q('wl1', L('Che cosa usi per una migrazione del database da fare una volta a ogni rilascio?', 'What do you use for a database migration to run once per release?'), ['Deployment', 'Job', 'DaemonSet', 'Service'], 1, L('Gira fino alla fine e si ferma.', 'It runs to completion and stops.')),
            Q('wl2', L('Che cosa significa `schedule: "0 3 * * *"`?', 'What does `schedule: "0 3 * * *"` mean?'), [L('Ogni 3 minuti', 'Every 3 minutes'), L('Il giorno 3 di ogni mese', 'On the 3rd of every month'), L('Tre volte al giorno', 'Three times a day'), L('Ogni giorno alle 3:00', 'Every day at 3:00')], 3, L('Minuto 0, ora 3, ogni giorno, mese e giorno della settimana.', 'Minute 0, hour 3, every day, month and weekday.')),
            Q('wl3', L('Quale risorsa per un agente che raccoglie i log su ogni nodo?', 'Which resource for an agent collecting logs on every node?'), ['Job', 'CronJob', 'DaemonSet', 'StatefulSet'], 2, L('Un pod per nodo, anche per quelli nuovi.', 'One pod per node, new ones included.')),
        ], task('yaml', L('Completa il CronJob `pulizia`: deve girare ogni giorno alle 2:30 (`"30 2 * * *"`) con un pod che ha `restartPolicy: OnFailure`.', 'Complete the `pulizia` CronJob: it must run every day at 2:30 (`"30 2 * * *"`) with a pod that has `restartPolicy: OnFailure`.'), "apiVersion: batch/v1\nkind: CronJob\nmetadata:\n  name: pulizia\nspec:\n", [re('schedule:\s*["\']30 2 \* \* \*["\']', L('L’orario', 'The schedule')), re('jobTemplate:', L('Il modello del Job', 'The Job template')), re('restartPolicy:\s*(OnFailure|Never)\b', L('Il pod può finire', 'The pod can finish'))], '', "apiVersion: batch/v1\nkind: CronJob\nmetadata:\n  name: pulizia\nspec:\n  schedule: \"30 2 * * *\"\n  jobTemplate:\n    spec:\n      template:\n        spec:\n          restartPolicy: OnFailure\n          containers:\n            - name: pulizia\n              image: registro.esempio.it/pulizia:1.0"), [
            dMatch(pMatch(), [['Job', L('Gira fino alla fine', 'Runs to completion')], ['CronJob', L('Job a orario', 'Scheduled Jobs')], ['DaemonSet', L('Un pod su ogni nodo', 'A pod on every node')], ['backoffLimit', L('Quanti tentativi', 'How many retries')], ['concurrencyPolicy: Forbid', L('Niente esecuzioni sovrapposte', 'No overlapping runs')]], L('Lavori a tempo e di servizio.', 'Timed and service work.')),
            dBug(pBug(), ['apiVersion: batch/v1', 'kind: Job', 'metadata:', '  name: migrazione', 'spec:', '  template:', '    spec:', '      restartPolicy: Always', '      containers:', '        - name: migra', '          image: registro.esempio.it/migra:1.0'], 7, '      restartPolicy: OnFailure', L('Un Job deve poter finire: `Always` non è ammesso.', 'A Job must be able to finish: `Always` is not allowed.')),
        ]),
        lesson('access', L('Namespace, RBAC e pod sicuri', 'Namespaces, RBAC and secure pods'), [
            P('I **namespace** dividono un cluster in aree (`negozio`, `staging`) con nomi, quote e permessi propri. Con **RBAC** un **Role** elenca le azioni (verbi) permesse su certe risorse di un namespace e un **RoleBinding** le assegna a un utente o a un **ServiceAccount** (l’identità dei pod). Per tutto il cluster ci sono ClusterRole e ClusterRoleBinding.', '**Namespaces** split a cluster into areas (`negozio`, `staging`) with their own names, quotas and permissions. With **RBAC** a **Role** lists the actions (verbs) allowed on certain resources in a namespace and a **RoleBinding** grants them to a user or a **ServiceAccount** (the pods’ identity). For the whole cluster there are ClusterRole and ClusterRoleBinding.'),
            C('apiVersion: rbac.authorization.k8s.io/v1
kind: Role
metadata:
  name: lettore-pod
  namespace: negozio
rules:
  - apiGroups: [""]
    resources: ["pods", "pods/log"]
    verbs: ["get", "list", "watch"]
---
apiVersion: rbac.authorization.k8s.io/v1
kind: RoleBinding
metadata:
  name: ada-legge-pod
  namespace: negozio
subjects:
  - kind: User
    name: ada@esempio.it
    apiGroup: rbac.authorization.k8s.io
roleRef:
  kind: Role
  name: lettore-pod
  apiGroup: rbac.authorization.k8s.io', 'yaml'),
            C('kubectl create namespace negozio
kubectl get pods -n negozio
kubectl auth can-i delete pods -n negozio --as ada@esempio.it   # no', 'bash'),
            P('Nel pod, un `securityContext` limita i danni se l’app viene violata: `runAsNonRoot: true`, `allowPrivilegeEscalation: false`, `readOnlyRootFilesystem: true` e nessuna capability in più. Con l’etichetta `pod-security.kubernetes.io/enforce: restricted` un namespace rifiuta i pod che non rispettano queste regole.', 'In the pod, a `securityContext` limits the damage if the app is breached: `runAsNonRoot: true`, `allowPrivilegeEscalation: false`, `readOnlyRootFilesystem: true` and no extra capabilities. With the label `pod-security.kubernetes.io/enforce: restricted` a namespace rejects pods that break these rules.'),
        ], [
            Q('rb1', L('Che cosa fa un RoleBinding?', 'What does a RoleBinding do?'), [L('Crea un nuovo namespace', 'Creates a new namespace'), L('Collega un Service ai suoi pod', 'Connects a Service to its pods'), L('Assegna i permessi di un Role a un utente o ServiceAccount', 'Grants a Role’s permissions to a user or ServiceAccount'), L('Unisce due cluster in uno', 'Merges two clusters into one')], 2, L('Il Role dice cosa, il binding a chi.', 'The Role says what, the binding to whom.')),
            Q('rb2', L('Quale comando verifica se un utente può fare un’azione?', 'Which command checks whether a user may perform an action?'), ['kubectl describe user', 'kubectl auth can-i', 'kubectl get roles --check', 'kubectl whoami --can'], 1, L('Con `--as` provi per conto di un altro.', 'With `--as` you test on someone else’s behalf.')),
            Q('rb3', L('Quale impostazione impedisce a un container di girare come root?', 'Which setting stops a container from running as root?'), ['privileged: true', 'hostNetwork: true', 'replicas: 0', 'runAsNonRoot: true'], 3, L('Va nel securityContext.', 'It goes in the securityContext.')),
        ], task('bash', L('Crea il namespace `staging` e poi elenca i pod che contiene.', 'Create the `staging` namespace and then list the pods it contains.'), '', [re('kubectl\s+create\s+(namespace|ns)\s+staging\b', L('Crei il namespace', 'You create the namespace')), re('kubectl\s+get\s+(pods?|po)\b[^\n]*(-n|--namespace)[=\s]+staging\b', L('Elenchi i suoi pod', 'You list its pods'))], '', "kubectl create namespace staging\nkubectl get pods -n staging"), [
            dMatch(pMatch(), [['Namespace', L('Area separata del cluster', 'Separate area of the cluster')], ['Role', L('Permessi in un namespace', 'Permissions in a namespace')], ['ClusterRole', L('Permessi su tutto il cluster', 'Permissions across the cluster')], ['RoleBinding', L('Assegna i permessi', 'Grants the permissions')], ['ServiceAccount', L('Identità dei pod', 'The pods’ identity')]], L('Chi può fare cosa.', 'Who can do what.')),
            dFlash(pFlash(), [[L('Conviene dare cluster-admin a ogni app', 'Giving cluster-admin to every app is wise'), false], [L('runAsNonRoot blocca i container che partirebbero come root', 'runAsNonRoot blocks containers that would start as root'), true], [L('Namespace diversi possono avere permessi diversi', 'Different namespaces can have different permissions'), true], [L('I verbi get e list permettono di cancellare', 'The get and list verbs allow deleting'), false]], L('Il minimo indispensabile.', 'The bare minimum.')),
        ]),
        lesson('gateway', L('Gateway API', 'Gateway API'), [
            P('La **Gateway API** è l’evoluzione dell’Ingress, stabile dalla versione 1.0. Divide i ruoli: la **GatewayClass** indica il controller che la realizza, il **Gateway** è il punto d’ingresso con porte e certificati (gestito da chi cura la piattaforma), le **HTTPRoute** sono le regole di ogni app (gestite dai team delle app). Pesi, intestazioni e reindirizzamenti si scrivono nello standard, senza annotazioni diverse per ogni controller.', 'The **Gateway API** is the evolution of Ingress, stable since version 1.0. It splits roles: the **GatewayClass** names the controller that implements it, the **Gateway** is the entry point with ports and certificates (managed by whoever runs the platform), **HTTPRoutes** are each app’s rules (managed by the app teams). Weights, headers and redirects are written in the standard, without different annotations for each controller.'),
            Fig(L(fig_k8s_gateway(['team della piattaforma', 'team delle app']), fig_k8s_gateway(['platform team', 'app teams'])),
                L('Nella fascia in alto, curata dal team della piattaforma, la GatewayClass punta al Gateway con porta 443 e TLS. Nella fascia sotto, dei team delle app, due HTTPRoute: una manda / al Service web, l’altra /api al Service api.', 'In the top band, run by the platform team, the GatewayClass points to the Gateway with port 443 and TLS. In the band below, belonging to the app teams, two HTTPRoutes: one sends / to the Service web, the other /api to the Service api.'),
                L('Ogni ruolo tocca solo la sua risorsa: il Gateway è condiviso, le rotte sono di ogni app.', 'Each role touches only its own resource: the Gateway is shared, the routes belong to each app.')),
            C('apiVersion: gateway.networking.k8s.io/v1
kind: Gateway
metadata:
  name: pubblico
spec:
  gatewayClassName: esempio-gw
  listeners:
    - name: https
      protocol: HTTPS
      port: 443
      hostname: "*.esempio.it"
      tls:
        certificateRefs:
          - name: certificato-esempio
---
apiVersion: gateway.networking.k8s.io/v1
kind: HTTPRoute
metadata:
  name: negozio
spec:
  parentRefs:
    - name: pubblico
  hostnames: ["www.esempio.it"]
  rules:
    - matches:
        - path: { type: PathPrefix, value: /api }
      backendRefs:
        - name: api
          port: 80
    - backendRefs:            # il resto: 90% alla versione 1, 10% alla 2
        - name: web-v1
          port: 80
          weight: 90
        - name: web-v2
          port: 80
          weight: 10', 'yaml'),
            P('L’Ingress resta supportato, ma è congelato e il diffuso controller ingress-nginx è stato ritirato nel marzo 2026: niente più correzioni di sicurezza. Per i nuovi cluster si sceglie un controller che implementa la Gateway API, e per migrare esistono strumenti come ingress2gateway.', 'Ingress is still supported, but it is frozen and the widespread ingress-nginx controller was retired in March 2026: no more security fixes. For new clusters you choose a controller implementing the Gateway API, and tools such as ingress2gateway help with migration.'),
        ], [
            Q('gw1', L('Chi gestisce di solito il Gateway e chi le HTTPRoute?', 'Who usually manages the Gateway and who the HTTPRoutes?'), [L('Entrambi li gestisce il fornitore cloud', 'The cloud provider manages both'), L('Il Gateway il team della piattaforma, le HTTPRoute i team delle app', 'The Gateway the platform team, the HTTPRoutes the app teams'), L('Il Gateway i team delle app, le HTTPRoute nessuno', 'The Gateway the app teams, the HTTPRoutes nobody'), L('Li crea Kubernetes da solo per ogni Service', 'Kubernetes creates them by itself for each Service')], 1, L('Ruoli separati, permessi separati.', 'Separate roles, separate permissions.')),
            Q('gw2', L('Come mandi il 10% del traffico a una nuova versione con una HTTPRoute?', 'How do you send 10% of traffic to a new version with an HTTPRoute?'), [L('Con 10 repliche della nuova versione', 'With 10 replicas of the new version'), L('Con un’annotazione nginx sul Service', 'With an nginx annotation on the Service'), L('Con un CronJob che cambia versione ogni ora', 'With a CronJob switching version every hour'), L('Con due backendRefs con i pesi 90 e 10', 'With two backendRefs weighted 90 and 10')], 3, L('Il rilascio graduale è nello standard.', 'Gradual rollout is part of the standard.')),
            Q('gw3', L('Qual è oggi lo stato dell’Ingress?', 'What is the state of Ingress today?'), [L('È stato rimosso da Kubernetes', 'It has been removed from Kubernetes'), L('Resta supportato ma è congelato: le novità vanno nella Gateway API', 'It is still supported but frozen: new features go into the Gateway API'), L('È diventato obbligatorio al posto dei Service', 'It has become mandatory instead of Services'), L('Funziona solo con il controller ingress-nginx', 'It works only with the ingress-nginx controller')], 1, L('Ma il controller ingress-nginx non è più mantenuto.', 'But the ingress-nginx controller is no longer maintained.')),
        ], task('yaml', L('Completa la HTTPRoute `blog`: collegala al Gateway `principale`, con hostname `blog.esempio.it` e il traffico al Service `blog` sulla porta `80`.', 'Complete the `blog` HTTPRoute: attach it to the `principale` Gateway, with hostname `blog.esempio.it` and traffic to the `blog` Service on port `80`.'), "apiVersion: gateway.networking.k8s.io/v1\nkind: HTTPRoute\nmetadata:\n  name: blog\nspec:\n", [re('parentRefs:\s*(\n\s+-\s*name:\s*["\']?principale|\[\s*\{\s*name:\s*["\']?principale)', L('Il Gateway', 'The Gateway')), re('hostnames:\s*(\[\s*["\']?blog\.esempio\.it|\n\s+-\s*["\']?blog\.esempio\.it)', L('L’hostname', 'The hostname')), re('backendRefs:\s*(\n\s+-\s*name:\s*["\']?blog["\']?\s*\n\s+port:\s*80\b|\[\s*\{\s*name:\s*["\']?blog["\']?\s*,\s*port:\s*80\s*\})', L('Il Service di destinazione', 'The target Service'))], '', "apiVersion: gateway.networking.k8s.io/v1\nkind: HTTPRoute\nmetadata:\n  name: blog\nspec:\n  parentRefs:\n    - name: principale\n  hostnames: [\"blog.esempio.it\"]\n  rules:\n    - backendRefs:\n        - name: blog\n          port: 80"), [
            dMatch(pMatch(), [['GatewayClass', L('Il controller che la realizza', 'The controller implementing it')], ['Gateway', L('Ingresso con porte e TLS', 'Entry point with ports and TLS')], ['HTTPRoute', L('Le regole di un’app', 'An app’s rules')], ['weight', L('Quota di traffico', 'Share of traffic')], ['parentRefs', L('A quale Gateway si collega', 'Which Gateway it attaches to')]], L('Il nuovo ingresso.', 'The new entrance.')),
            dFlash(pFlash(), [[L('La Gateway API è stabile (v1)', 'The Gateway API is stable (v1)'), true], [L('ingress-nginx riceve ancora correzioni di sicurezza dal progetto Kubernetes', 'ingress-nginx still gets security fixes from the Kubernetes project'), false], [L('Una HTTPRoute può dividere il traffico con dei pesi', 'An HTTPRoute can split traffic with weights'), true], [L('Con la Gateway API i Service non servono più', 'With the Gateway API Services are no longer needed'), false]], L('Instradare con lo standard.', 'Routing with the standard.')),
        ]),
        lesson('packaging', L('Helm, Kustomize e GitOps', 'Helm, Kustomize and GitOps'), [
            P('Copiare a mano i YAML per ogni ambiente porta a errori. **Kustomize**, integrato in `kubectl` (`-k`), parte da una **base** e applica per ogni ambiente delle **patch**, senza template. **Helm** impacchetta un’app in un **chart** con template e un file `values.yaml`; installa, aggiorna e riporta indietro le **release**.', 'Copying YAML by hand for each environment leads to mistakes. **Kustomize**, built into `kubectl` (`-k`), starts from a **base** and applies **patches** for each environment, with no templates. **Helm** packages an app into a **chart** with templates and a `values.yaml` file; it installs, upgrades and rolls back **releases**.'),
            C('# overlays/produzione/kustomization.yaml
apiVersion: kustomize.config.k8s.io/v1beta1
kind: Kustomization
resources:
  - ../../base
namespace: produzione
images:
  - name: registro.esempio.it/web
    newTag: "1.5"
patches:
  - path: repliche.yaml', 'yaml'),
            C('kubectl apply -k overlays/produzione
helm install negozio ./chart -n negozio -f valori-produzione.yaml
helm upgrade negozio ./chart -n negozio --set image.tag=1.5
helm history negozio -n negozio
helm rollback negozio 3 -n negozio
helm install metriche oci://registro.esempio.it/charts/metriche --version 2.4.1', 'bash'),
            P('Qualunque strumento scegli, tieni i file in Git e lascia che il cluster si allinei da solo (**GitOps**, con strumenti come Argo CD o Flux): ogni modifica è un commit rivisto e tornare indietro è un revert. Fissa sempre la versione dei chart che installi.', 'Whichever tool you choose, keep the files in Git and let the cluster align itself (**GitOps**, with tools such as Argo CD or Flux): every change is a reviewed commit and going back is a revert. Always pin the version of the charts you install.'),
        ], [
            Q('pk1', L('Come adatta Kustomize i YAML a ogni ambiente?', 'How does Kustomize adapt YAML to each environment?'), [L('Con template scritti in un linguaggio di programmazione', 'With templates written in a programming language'), L('Con una base e delle patch per ogni ambiente', 'With a base and patches for each environment'), L('Scaricando i file da un registro pubblico', 'By downloading the files from a public registry'), L('Creando un cluster diverso per ogni ambiente', 'By creating a different cluster per environment')], 1, L('Niente template: solo modifiche dichiarate.', 'No templates: only declared changes.')),
            Q('pk2', L('Dove imposti i valori di un chart Helm per un ambiente?', 'Where do you set a Helm chart’s values for an environment?'), [L('Nel Dockerfile dell’app', 'In the app’s Dockerfile'), L('Nei log del cluster', 'In the cluster logs'), L('In un ConfigMap del namespace kube-system', 'In a ConfigMap in the kube-system namespace'), L('In un file di valori passato con -f, o con --set', 'In a values file passed with -f, or with --set')], 3, L('I valori riempiono i template del chart.', 'The values fill in the chart’s templates.')),
            Q('pk3', L('Che cos’è il GitOps?', 'What is GitOps?'), [L('Usare Git al posto di kubectl per leggere i log', 'Using Git instead of kubectl to read logs'), L('Fare il backup del cluster dentro GitHub', 'Backing up the cluster inside GitHub'), L('Il cluster si allinea da solo ai file versionati in Git', 'The cluster aligns itself to the files versioned in Git'), L('Modificare il cluster a mano e poi fare un commit', 'Changing the cluster by hand and then committing')], 2, L('Git è la fonte di verità.', 'Git is the source of truth.')),
        ], task('bash', L('Applica l’overlay Kustomize `overlays/staging`, poi aggiorna la release Helm `negozio` dal chart `./chart` impostando `image.tag=2.1`.', 'Apply the Kustomize overlay `overlays/staging`, then upgrade the Helm release `negozio` from the `./chart` chart setting `image.tag=2.1`.'), '', [re('kubectl\s+apply\s+-k\s+(\./)?overlays/staging/?', L('Applichi l’overlay', 'You apply the overlay')), re('helm\s+upgrade\s+((--install|-i)\s+)?negozio\s+\./chart/?(\s|$)[^\n]*--set\s+image\.tag=["\']?2\.1', L('Aggiorni la release', 'You upgrade the release'))], '', "kubectl apply -k overlays/staging\nhelm upgrade negozio ./chart --set image.tag=2.1"), [
            dMatch(pMatch(), [['kubectl apply -k', L('Applica Kustomize', 'Applies Kustomize')], ['values.yaml', L('Valori predefiniti del chart', 'The chart’s default values')], ['helm upgrade', L('Aggiorna una release', 'Upgrades a release')], ['helm rollback', L('Torna a una revisione', 'Goes back to a revision')], ['GitOps', L('Git come fonte di verità', 'Git as source of truth')]], L('Impacchettare e distribuire.', 'Packaging and delivering.')),
            dFlash(pFlash(), [[L('Kustomize è integrato in kubectl', 'Kustomize is built into kubectl'), true], [L('Helm può riportare una release a una revisione precedente', 'Helm can roll a release back to an earlier revision'), true], [L('Senza versione fissata un chart installa sempre la stessa cosa', 'Without a pinned version a chart always installs the same thing'), false], [L('GitOps vuol dire modificare il cluster a mano', 'GitOps means changing the cluster by hand'), false]], L('Ambienti ripetibili.', 'Repeatable environments.')),
        ]),
    ],
];
