<?php
/** Cloud basics: IaaS, PaaS, SaaS and serverless, the main services, regions and availability, costs and shared responsibility,
 *  infrastructure as code, networks (VPC, firewall, CDN, DNS), identities and permissions (IAM), scaling and backups. */

/** Who manages each layer in IaaS, PaaS, serverless and SaaS: you or the provider. */
if (!function_exists('fig_cloud_models')) {
    function fig_cloud_models(array $rows, string $you, string $prov): string
    {
        $mine = [4, 2, 2, 1]; // how many layers from the top you manage, per model
        $cells = '';
        foreach (['IaaS', 'PaaS', 'Serverless', 'SaaS'] as $c => $m) {
            $x = 206 + $c * 106; $cx = $x + 50;
            $cells .= "<text x=\"$cx\" y=\"38\" text-anchor=\"middle\" font-weight=\"700\">$m</text>";
            foreach ($rows as $r => $label) {
                $y = 52 + $r * 38; $ty = $y + 21; $own = $r < $mine[$c];
                $cells .= $own
                    ? "<rect x=\"$x\" y=\"$y\" width=\"100\" height=\"32\" rx=\"8\" fill=\"#FFE8D6\" stroke=\"#E8590C\" stroke-width=\"2\"/><text x=\"$cx\" y=\"$ty\" text-anchor=\"middle\" font-size=\"13\" font-weight=\"700\" fill=\"#B84508\">$you</text>"
                    : "<rect x=\"$x\" y=\"$y\" width=\"100\" height=\"32\" rx=\"8\" fill=\"url(#h)\" stroke=\"#8A87B0\" stroke-width=\"2\"/><text x=\"$cx\" y=\"$ty\" text-anchor=\"middle\" font-size=\"13\" fill=\"#4A4870\">$prov</text>";
            }
        }
        $labels = '';
        foreach ($rows as $r => $label) $labels .= '<text x="190" y="' . (73 + $r * 38) . '" text-anchor="end">' . $label . '</text>';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><pattern id="h" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="8" height="8" fill="#F1F0F8"/><line x1="0" y1="0" x2="0" y2="8" stroke="#E2E0EE" stroke-width="4"/></pattern></defs>
{$labels}
{$cells}
</svg>
SVG;
    }
}

/** A region with three availability zones behind a load balancer; now and then zone B goes down and the other two carry on. */
if (!function_exists('fig_cloud_zones')) {
    function fig_cloud_zones(array $t): string
    {
        $zones = '';
        foreach (['A', 'B', 'C'] as $i => $z) {
            $x = 36 + $i * 196; $cx = $x + 88;
            $zones .= "<path d=\"M320,92 C320,108 $cx,104 $cx,120\" fill=\"none\" stroke=\"#1E1C52\" stroke-width=\"2\"/>"
                    . "<rect x=\"$x\" y=\"120\" width=\"176\" height=\"126\" rx=\"12\" fill=\"#DCEBFF\" stroke=\"#2F8CF0\" stroke-width=\"2\"/><text x=\"$cx\" y=\"146\" text-anchor=\"middle\" font-weight=\"700\">{$t[1]} $z</text>"
                    . "<rect x=\"" . ($x + 16) . "\" y=\"160\" width=\"66\" height=\"34\" rx=\"8\" fill=\"#FFFFFF\" stroke=\"#2F8CF0\" stroke-width=\"2\"/><text x=\"" . ($x + 49) . "\" y=\"182\" text-anchor=\"middle\" font-size=\"13\">app</text>"
                    . "<rect x=\"" . ($x + 94) . "\" y=\"160\" width=\"66\" height=\"34\" rx=\"8\" fill=\"#FFFFFF\" stroke=\"#2F8CF0\" stroke-width=\"2\"/><text x=\"" . ($x + 127) . "\" y=\"182\" text-anchor=\"middle\" font-size=\"13\">app</text>"
                    . "<text x=\"$cx\" y=\"226\" text-anchor=\"middle\" font-size=\"13\">{$t[2]}</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 276" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.down{opacity:0;animation:down 8s infinite}@keyframes down{0%,35%{opacity:0}42%,80%{opacity:1}88%,100%{opacity:0}}@media (prefers-reduced-motion:reduce){.down{animation:none}}</style>
<rect x="16" y="14" width="608" height="250" rx="14" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<text x="34" y="42" font-weight="700">{$t[0]}</text>
<rect x="230" y="56" width="180" height="36" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="320" y="79" text-anchor="middle" font-weight="700">load balancer</text>
{$zones}
<g class="down"><rect x="232" y="120" width="176" height="126" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="6 4"/>
<text x="320" y="176" text-anchor="middle" font-weight="700" fill="#B0205A">{$t[1]} B</text><text x="320" y="200" text-anchor="middle" font-weight="700" fill="#B0205A">{$t[3]}</text>
<path d="M306,98 L334,114 M334,98 L306,114" stroke="#F03E82" stroke-width="3"/></g>
</svg>
SVG;
    }
}

/** A VPC: only the load balancer in the public subnet faces the internet; app servers and database sit in private subnets. */
if (!function_exists('fig_cloud_vpc')) {
    function fig_cloud_vpc(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="8" y="120" width="76" height="50" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="46" y="150" text-anchor="middle" font-weight="700">Internet</text>
<rect x="110" y="14" width="520" height="276" rx="14" fill="#FFFFFF" stroke="#7048E8" stroke-width="2"/>
<text x="126" y="40" font-weight="700" fill="#5A35C9">VPC <tspan font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="400">10.0.0.0/16</tspan></text>
<rect x="124" y="56" width="132" height="176" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="292" y="56" width="146" height="176" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="474" y="56" width="148" height="176" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g font-size="13" font-weight="700" text-anchor="middle"><text x="190" y="78" fill="#0B7F5C">{$t[0]}</text><text x="365" y="78" fill="#1B66BC">{$t[1]}</text><text x="548" y="78" fill="#1B66BC">{$t[1]}</text></g>
<rect x="136" y="116" width="108" height="56" rx="10" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/><text x="190" y="140" text-anchor="middle" font-size="13" font-weight="700">load</text><text x="190" y="158" text-anchor="middle" font-size="13" font-weight="700">balancer</text>
<g stroke-width="2"><rect x="306" y="92" width="118" height="38" rx="8" fill="#FFFFFF" stroke="#2F8CF0"/><rect x="306" y="138" width="118" height="38" rx="8" fill="#FFFFFF" stroke="#2F8CF0"/><rect x="306" y="184" width="118" height="38" rx="8" fill="#FFFFFF" stroke="#2F8CF0"/></g>
<g font-size="13" text-anchor="middle"><text x="365" y="116">app</text><text x="365" y="162">app</text><text x="365" y="208">app</text></g>
<path d="M504,122 v52 a44,12 0 0 0 88,0 v-52" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/><ellipse cx="548" cy="122" rx="44" ry="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="548" y="164" text-anchor="middle" font-size="13" font-weight="700">database</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ah)"><path d="M86,145 H128"/><path d="M246,144 H298"/><path d="M426,157 H496"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" font-weight="700" text-anchor="middle"><text x="97" y="134">443</text><text x="274" y="134">8080</text><text x="456" y="147">5432</text></g>
<path d="M46,172 V254 H548 V242" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="6 4"/><path d="M534,242 H562" stroke="#F03E82" stroke-width="3"/>
<text x="300" y="276" text-anchor="middle" font-size="13" font-weight="700" fill="#B0205A">{$t[2]}</text>
</svg>
SVG;
    }
}

/** Vertical scaling (a bigger machine) beside horizontal scaling (more equal machines behind a load balancer, one added by autoscaling). */
if (!function_exists('fig_cloud_scale')) {
    function fig_cloud_scale(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.add{animation:add 6s ease-in-out infinite}@keyframes add{0%,30%{opacity:0;transform:translateY(-16px)}45%,85%{opacity:1;transform:translateY(0)}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.add{animation:none}}</style>
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="8" y="8" width="250" height="254" rx="12" fill="#F1F0F8"/><rect x="270" y="8" width="362" height="254" rx="12" fill="#F1F0F8"/>
<text x="24" y="36" font-weight="700">{$t[0]}</text><text x="286" y="36" font-weight="700">{$t[1]}</text>
<rect x="30" y="160" width="64" height="56" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="62" y="193" text-anchor="middle" font-size="13" font-weight="700">2 CPU</text>
<rect x="140" y="80" width="100" height="136" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="190" y="153" text-anchor="middle" font-weight="700">8 CPU</text>
<path d="M98,186 C112,186 118,150 132,150" fill="none" stroke="#1E1C52" stroke-width="2" marker-end="url(#ah)"/>
<text x="133" y="244" text-anchor="middle" font-size="13">{$t[2]}</text>
<rect x="376" y="52" width="150" height="36" rx="10" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="451" y="75" text-anchor="middle" font-size="14" font-weight="700">load balancer</text>
<g fill="none" stroke="#1E1C52" stroke-width="2"><path d="M451,88 V112 H322 V150 M451,112 H406 V150 M451,112 H490 V150"/><path class="add" d="M451,112 H574 V150"/></g>
<g stroke-width="2"><rect x="292" y="150" width="60" height="66" rx="8" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="376" y="150" width="60" height="66" rx="8" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="460" y="150" width="60" height="66" rx="8" fill="#DCEBFF" stroke="#2F8CF0"/></g>
<g class="add"><rect x="544" y="150" width="60" height="66" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="5 3"/><text x="574" y="188" text-anchor="middle" font-size="17" font-weight="700" fill="#0B7F5C">+1</text></g>
<g font-size="13" text-anchor="middle"><text x="322" y="188">app</text><text x="406" y="188">app</text><text x="490" y="188">app</text></g>
<text x="451" y="244" text-anchor="middle" font-size="13">{$t[3]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'cloud-base', 'family' => 'cloud', 'after' => null,
    'title' => L('Basi di cloud', 'Cloud Basics'), 'short' => L('Cloud', 'Cloud'), 'level' => L('Base', 'Basics'),
    'desc' => L('Capire il cloud di AWS, Azure e Google Cloud: modelli di servizio, calcolo, archiviazione e database, regioni e disponibilità, costi e sicurezza condivisa, reti e permessi, scalabilità e backup.', 'Understanding the cloud of AWS, Azure and Google Cloud: service models, compute, storage and databases, regions and availability, costs and shared security, networks and permissions, scaling and backups.'),
    'lessons' => [
        lesson('models', L('IaaS, PaaS, SaaS e serverless', 'IaaS, PaaS, SaaS and serverless'), [
            P('Il **cloud** è usare computer, archiviazione e servizi di un fornitore via internet, pagando quanto consumi, invece di comprare server. I modelli si distinguono per **quanto gestisci tu**.', 'The **cloud** means using a provider’s computers, storage and services over the internet, paying for what you use, instead of buying servers. The models differ by **how much you manage yourself**.'),
            C('IaaS        macchine virtuali: gestisci sistema e app      (EC2, Azure VM, Compute Engine)
PaaS        piattaforma: carichi l’app, il resto è gestito  (App Service, App Engine, Elastic Beanstalk)
Serverless  funzioni che girano a richiesta                 (Lambda, Azure Functions, Cloud Run functions)
SaaS        software pronto da usare                       (Gmail, Microsoft 365, Salesforce)', 'text'),
            P('Più sali verso SaaS, meno manutenzione e meno controllo. Il **serverless** non vuol dire senza server: vuol dire che non li vedi, paghi solo quando il codice gira e scala da solo.', 'The higher you go toward SaaS, the less maintenance and the less control. **Serverless** does not mean no servers: it means you do not see them, you pay only when code runs and it scales by itself.'),
            Fig(L(fig_cloud_models(['Dati e accessi', 'Applicazione', 'Runtime', 'Sistema operativo', 'Virtualizzazione', 'Server e rete'], 'tu', 'fornitore'), fig_cloud_models(['Data and access', 'Application', 'Runtime', 'Operating system', 'Virtualisation', 'Servers and network'], 'you', 'provider')),
                L('Una tabella con sei strati, dai dati in alto ai server in basso, e quattro colonne. In IaaS gestisci tu dati, applicazione, runtime e sistema operativo; in PaaS e serverless solo dati e applicazione; in SaaS solo i dati e gli accessi. Il resto lo gestisce il fornitore.', 'A table with six layers, from data at the top to servers at the bottom, and four columns. In IaaS you manage data, application, runtime and operating system; in PaaS and serverless only data and application; in SaaS only data and access. The provider manages the rest.'),
                L('Da sinistra a destra la parte tua si accorcia: i tuoi dati però restano sempre tuoi.', 'From left to right your part gets shorter: your data, though, always stays yours.')),
            P('Come scegliere: un sito vetrina o la posta aziendale sono **SaaS**; un’app web fatta da te va bene su un **PaaS**; un compito che scatta ogni tanto (ridimensionare una foto caricata) è perfetto **serverless**; se ti serve controllare il sistema operativo o installare software particolare, prendi una macchina **IaaS**. Spesso un progetto li mescola.', 'How to choose: a brochure site or company email are **SaaS**; a web app you built fits a **PaaS**; a task that fires now and then (resizing an uploaded photo) is perfect **serverless**; if you need to control the operating system or install special software, take an **IaaS** machine. A project often mixes them.'),
        ], [
            Q('m1', L('In quale modello gestisci il sistema operativo?', 'In which model do you manage the operating system?'), ['IaaS', 'SaaS', 'PaaS', 'Serverless'], 0, L('Macchine virtuali.', 'Virtual machines.')),
            Q('m2', L('Che cosa significa serverless?', 'What does serverless mean?'), [L('I server ci sono ma non li gestisci, paghi quando il codice gira', 'Servers exist but you do not manage them, you pay when code runs'), L('Non ci sono server', 'There are no servers'), L('Funziona offline', 'It works offline'), L('È gratis', 'It is free')], 0, L('E scala da solo.', 'And it scales by itself.')),
            Q('m3', L('Gmail è un esempio di…', 'Gmail is an example of…'), ['SaaS', 'IaaS', 'PaaS', L('Hardware', 'Hardware')], 0, L('Software pronto.', 'Ready software.')),
        ], task('text', L('Classifica, uno per riga come `servizio: modello`: `macchina virtuale`, `Microsoft 365`, `funzione che gira a ogni caricamento di file`. Modelli: IaaS, SaaS, serverless.', 'Classify, one per line as `service: model`: `virtual machine`, `Microsoft 365`, `function running on each file upload`. Models: IaaS, SaaS, serverless.'), '', [re('(macchina virtuale|virtual machine)\s*:\s*IaaS', L('VM = IaaS', 'VM = IaaS')), re('Microsoft 365\s*:\s*SaaS', L('Microsoft 365 = SaaS', 'Microsoft 365 = SaaS')), re('(funzione|function)[^:\n]*:\s*serverless', L('Funzione = serverless', 'Function = serverless'))], '', "macchina virtuale: IaaS\nMicrosoft 365: SaaS\nfunzione che gira a ogni caricamento di file: serverless"), [
            dOrder(pSort(), ['IaaS', 'PaaS', 'Serverless', 'SaaS'], L('Da più controllo a meno gestione.', 'From more control to less management.')),
            dMatch(pMatch(), [['IaaS', L('Macchine virtuali', 'Virtual machines')], ['PaaS', L('Carichi l’app', 'You upload the app')], ['Serverless', L('Funzioni a richiesta', 'On-demand functions')], ['SaaS', L('Software pronto', 'Ready software')]], L('I modelli di servizio.', 'Service models.')),
        ]),
        lesson('services', L('I servizi principali', 'The main services'), [
            P('I tre grandi fornitori (**AWS**, **Microsoft Azure**, **Google Cloud**) offrono servizi simili con nomi diversi. Conoscere le categorie aiuta a muoversi tra tutti.', 'The three big providers (**AWS**, **Microsoft Azure**, **Google Cloud**) offer similar services with different names. Knowing the categories helps you move among all of them.'),
            C('categoria          AWS          Azure              Google Cloud
calcolo (VM)       EC2          Virtual Machines   Compute Engine
archivio file      S3           Blob Storage       Cloud Storage
database SQL       RDS          Azure SQL          Cloud SQL
container          ECS / EKS    AKS                GKE / Cloud Run
funzioni           Lambda       Functions          Cloud Run functions', 'text'),
            P('L’**object storage** (S3 e simili) salva file di ogni dimensione in “bucket”, accessibili via URL: ideale per immagini, backup, siti statici. I **database gestiti** fanno da soli backup, aggiornamenti e repliche.', '**Object storage** (S3 and similar) stores files of any size in “buckets”, accessible via URL: ideal for images, backups, static sites. **Managed databases** handle backups, updates and replicas by themselves.'),
            P('Una tipica app web ne usa uno per categoria: calcolo (una VM o un container) per il codice, object storage per le immagini caricate, un database gestito per i dati. I dischi delle VM (block storage) sono un’altra cosa: legati a una macchina, non a un URL. Quando puoi, preferisci i servizi **gestiti**: quello che non gestisci tu, non devi aggiornarlo tu alle tre di notte.', 'A typical web app uses one per category: compute (a VM or a container) for the code, object storage for uploaded images, a managed database for the data. VM disks (block storage) are something else: tied to a machine, not to a URL. When you can, prefer **managed** services: what you do not manage, you do not have to patch at three in the morning.'),
        ], [
            Q('s1', L('Qual è il servizio di object storage di AWS?', 'What is AWS’s object storage service?'), ['S3', 'EC2', 'Lambda', 'RDS'], 0, L('Bucket di file.', 'Buckets of files.')),
            Q('s2', L('Che vantaggio dà un database gestito?', 'What does a managed database give you?'), [L('Backup e aggiornamenti automatici', 'Automatic backups and updates'), L('Nessun costo', 'No cost'), L('Nessun dato', 'No data'), L('Solo velocità', 'Only speed')], 0, L('Meno manutenzione.', 'Less maintenance.')),
            Q('s3', L('Qual è il servizio Kubernetes gestito di Google Cloud?', 'What is Google Cloud’s managed Kubernetes service?'), ['GKE', 'EKS', 'AKS', 'S3'], 0, L('EKS è AWS, AKS è Azure.', 'EKS is AWS, AKS is Azure.')),
        ], task('text', L('Abbina ogni servizio AWS alla sua categoria, uno per riga come `servizio: categoria`: `EC2`, `S3`, `Lambda`. Categorie: calcolo, archivio, funzioni.', 'Match each AWS service to its category, one per line as `service: category`: `EC2`, `S3`, `Lambda`. Categories: compute, storage, functions.'), '', [re('EC2\s*:\s*(calcolo|compute)', L('EC2 = calcolo', 'EC2 = compute')), re('S3\s*:\s*(archivio|storage)', L('S3 = archivio', 'S3 = storage')), re('Lambda\s*:\s*(funzioni|functions)', L('Lambda = funzioni', 'Lambda = functions'))], '', "EC2: calcolo\nS3: archivio\nLambda: funzioni"), [
            dMatch(pMatch(), [['EC2', 'Compute Engine'], ['S3', 'Cloud Storage'], ['RDS', 'Cloud SQL'], ['Lambda', 'Cloud Run functions'], ['EKS', 'GKE']], L('AWS e Google Cloud a confronto.', 'AWS and Google Cloud compared.')),
            dFlash(pFlash(), [[L('S3 è adatto per immagini e backup', 'S3 suits images and backups'), true], [L('I tre fornitori hanno servizi completamente diversi', 'The three providers have completely different services'), false], [L('Azure ha un servizio di macchine virtuali', 'Azure has a virtual machine service'), true], [L('Un database gestito non fa mai backup', 'A managed database never backs up'), false]], L('Stesse idee, nomi diversi.', 'Same ideas, different names.')),
        ]),
        lesson('regions', L('Regioni e disponibilità', 'Regions and availability'), [
            P('I fornitori hanno data center in tutto il mondo, raggruppati in **regioni** (per esempio Milano, Francoforte, Irlanda). Ogni regione ha più **zone di disponibilità**: data center separati, con alimentazione e rete indipendenti.', 'Providers have data centres worldwide, grouped into **regions** (for example Milan, Frankfurt, Ireland). Each region has several **availability zones**: separate data centres with independent power and network.'),
            Fig(L(fig_cloud_zones(['Regione Milano', 'Zona', 'data center separato', 'fuori servizio']), fig_cloud_zones(['Milan region', 'Zone', 'separate data centre', 'down'])),
                L('Una regione con un load balancer collegato a tre zone di disponibilità, A, B e C, ognuna con due server dell’app. A tratti la zona B va fuori servizio e il load balancer continua con le zone A e C.', 'A region with a load balancer linked to three availability zones, A, B and C, each with two app servers. Now and then zone B goes down and the load balancer carries on with zones A and C.'),
                L('Una zona può cadere: con l’app su tre zone, gli utenti non se ne accorgono.', 'A zone can fail: with the app on three zones, users do not notice.')),
            P('Scegliere la regione conta per la **latenza** (vicino agli utenti), per le **leggi** (dati dei cittadini europei in UE, GDPR) e per i prezzi. Distribuire l’app su più zone la rende **resistente ai guasti**: se un data center ha un problema, le altre zone continuano.', 'Choosing the region matters for **latency** (close to users), for **law** (EU citizens’ data in the EU, GDPR) and for prices. Spreading the app across several zones makes it **fault-tolerant**: if one data centre has a problem, the other zones carry on.'),
            Tip('Non tutti i servizi esistono in tutte le regioni e i prezzi cambiano da una all’altra: controlla prima di scegliere. Usare più zone della stessa regione è normale e costa poco; usare più regioni serve contro i disastri più grandi, ma costa di più e complica la copia dei dati.', 'Not every service exists in every region and prices change from one to another: check before choosing. Using several zones of the same region is normal and cheap; using several regions protects against bigger disasters, but costs more and makes copying data harder.'),
        ], [
            Q('r1', L('Che cos’è una zona di disponibilità?', 'What is an availability zone?'), [L('Un data center separato dentro una regione', 'A separate data centre within a region'), L('Un paese', 'A country'), L('Un tipo di database', 'A database type'), L('Una password', 'A password')], 0, L('Alimentazione e rete indipendenti.', 'Independent power and network.')),
            Q('r2', L('Perché scegliere una regione europea per dati di cittadini UE?', 'Why choose a European region for EU citizens’ data?'), [L('Per il GDPR e la latenza', 'For the GDPR and latency'), L('Per il colore della console', 'For the console colour'), L('Non conta', 'It does not matter'), L('Perché costa sempre di più', 'Because it always costs more')], 0, L('Legge e vicinanza.', 'Law and proximity.')),
            Q('r3', L('Che cosa dà distribuire l’app su più zone?', 'What does spreading the app across zones give?'), [L('Resistenza ai guasti', 'Fault tolerance'), L('Più bug', 'More bugs'), L('Meno sicurezza', 'Less security'), L('Niente', 'Nothing')], 0, L('Se una zona cade, le altre reggono.', 'If one zone falls, the others hold.')),
        ], task('text', L('Scrivi due motivi per scegliere la regione di Milano per un’app usata da clienti italiani, uno per riga con `-`.', 'Write two reasons to choose the Milan region for an app used by Italian customers, one per line with `-`.'), "- \n- ", [re('^-\s*\S.{5,}\n-\s*\S.{5,}', L('Due motivi', 'Two reasons')), re('latenz|latency|vicin|close|veloc|fast', L('La latenza', 'Latency')), re('GDPR|legg|law|UE|EU|europ', L('Le leggi', 'The law'))], '', "- latenza bassa perché i server sono vicini ai clienti\n- i dati restano in UE, utile per il GDPR"), [
            dMatch(pMatch(), [[L('Regione', 'Region'), L('Area geografica di data center', 'Geographic area of data centres')], [L('Zona di disponibilità', 'Availability zone'), L('Data center indipendente', 'Independent data centre')], [L('Latenza', 'Latency'), L('Distanza dagli utenti', 'Distance from users')], [L('Più zone', 'Multiple zones'), L('Resistenza ai guasti', 'Fault tolerance')]], L('Dove girano le app.', 'Where apps run.')),
            dFlash(pFlash(), [[L('Una regione ha più zone di disponibilità', 'A region has several availability zones'), true], [L('La regione non influisce sulla latenza', 'The region does not affect latency'), false], [L('Il GDPR conta nella scelta della regione', 'The GDPR matters when choosing a region'), true], [L('Una sola zona è più resistente di tre', 'One zone is more resilient than three'), false]], L('Geografia del cloud.', 'Cloud geography.')),
        ]),
        lesson('costs', L('Costi e sicurezza condivisa', 'Costs and shared security'), [
            P('Nel cloud paghi a consumo: comodo, ma i costi possono sorprendere. Buone abitudini: **avvisi di budget**, spegnere le risorse inutilizzate, scegliere la dimensione giusta, usare l’autoscaling e controllare il traffico in uscita, che spesso si paga.', 'In the cloud you pay as you go: convenient, but costs can surprise. Good habits: **budget alerts**, turning off unused resources, picking the right size, using autoscaling and watching outbound traffic, which is often charged.'),
            P('La sicurezza segue il **modello di responsabilità condivisa**: il fornitore protegge l’infrastruttura (data center, hardware, rete), tu proteggi quello che ci metti sopra: accessi, configurazioni, dati, aggiornamenti delle tue macchine. Molte violazioni nascono da un **bucket lasciato pubblico** o da chiavi di accesso finite in un repository.', 'Security follows the **shared responsibility model**: the provider protects the infrastructure (data centres, hardware, network), you protect what you put on it: access, configuration, data, updates of your machines. Many breaches come from a **bucket left public** or access keys ending up in a repository.'),
            Tip('Attiva l’autenticazione a due fattori sull’account principale, usa ruoli con permessi minimi e non usare mai l’account root per il lavoro di ogni giorno.', 'Turn on two-factor authentication on the root account, use roles with minimal permissions and never use the root account for everyday work.'),
        ], [
            Q('c1', L('Che cosa protegge il fornitore nel modello condiviso?', 'What does the provider protect in the shared model?'), [L('L’infrastruttura fisica e la rete', 'Physical infrastructure and network'), L('I tuoi dati e accessi', 'Your data and access'), L('Le tue password', 'Your passwords'), L('Tutto', 'Everything')], 0, L('Il resto è tuo.', 'The rest is yours.')),
            Q('c2', L('Qual è una causa frequente di violazioni nel cloud?', 'What is a frequent cause of cloud breaches?'), [L('Un bucket lasciato pubblico', 'A bucket left public'), L('Troppi backup', 'Too many backups'), L('Server troppo veloci', 'Servers too fast'), L('Il colore della console', 'The console colour')], 0, L('Errore di configurazione.', 'Configuration error.')),
            Q('c3', L('Come eviti sorprese sui costi?', 'How do you avoid cost surprises?'), [L('Avvisi di budget e spegnere ciò che non serve', 'Budget alerts and turning off what is unused'), L('Ignorando la fattura', 'Ignoring the bill'), L('Usando sempre le macchine più grandi', 'Always using the biggest machines'), L('Non si può', 'You cannot')], 0, L('Controllo costante.', 'Constant monitoring.')),
        ], task('text', L('Scrivi quattro buone pratiche per un account cloud nuovo, una per riga con `-`, tra cui una sul **budget** e una sull’**accesso**.', 'Write four good practices for a new cloud account, one per line with `-`, including one on **budget** and one on **access**.'), "- \n- \n- \n- ", [re('^-\s*\S.*\n-\s*\S.*\n-\s*\S.*\n-\s*\S', L('Quattro pratiche', 'Four practices')), re('budget|cost|spes|avvis|alert', L('Una sul budget', 'One on budget')), re('due fattori|2FA|MFA|two.factor|ruol|role|permess|permission|root|accesso|access', L('Una sull’accesso', 'One on access'))], '', "- attivare un avviso di budget mensile\n- autenticazione a due fattori sull’account principale\n- ruoli con permessi minimi, niente root per il lavoro quotidiano\n- controllare che nessun bucket sia pubblico"), [
            dMatch(pMatch(), [[L('Fornitore', 'Provider'), L('Data center, hardware, rete', 'Data centres, hardware, network')], [L('Cliente', 'Customer'), L('Accessi, configurazioni, dati', 'Access, configuration, data')], [L('Avviso di budget', 'Budget alert'), L('Ti avverte prima dello sforamento', 'Warns before overspending')], [L('Permessi minimi', 'Least privilege'), L('Solo ciò che serve', 'Only what is needed')]], L('Responsabilità condivisa.', 'Shared responsibility.')),
            dFlash(pFlash(), [[L('Il traffico in uscita spesso si paga', 'Outbound traffic is often charged'), true], [L('Il fornitore è responsabile delle tue password', 'The provider is responsible for your passwords'), false], [L('Spegnere le risorse inutilizzate fa risparmiare', 'Turning off unused resources saves money'), true], [L('Conviene usare l’account root ogni giorno', 'Using the root account daily is wise'), false]], L('Spendere bene, proteggere meglio.', 'Spend well, protect better.')),
        ]),
        lesson('iac', L('Infrastruttura come codice e monitoraggio', 'Infrastructure as code and monitoring'), [
            P('Con l’**infrastruttura come codice** (IaC) server, reti e database si descrivono in file di testo (Terraform o il suo fork open source OpenTofu, CloudFormation, Bicep) tenuti in Git: si rivedono, si ripetono uguali in più ambienti e si ricreano dopo un guasto. Niente più configurazioni fatte a mano e dimenticate.', 'With **infrastructure as code** (IaC) servers, networks and databases are described in text files (Terraform or its open-source fork OpenTofu, CloudFormation, Bicep) kept in Git: they can be reviewed, repeated identically across environments and recreated after a failure. No more hand-made, forgotten configurations.'),
            C('resource "aws_s3_bucket" "foto" {
  bucket = "fucina-foto-produzione"
  tags   = { progetto = "fucina" }
}

# terraform plan   → mostra cosa cambierà
# terraform apply  → lo applica', 'hcl'),
            P('In produzione serve vedere cosa succede: **metriche** (CPU, latenza, errori), **log** centralizzati e **allarmi** che avvisano prima degli utenti. Imposta anche avvisi di **budget** per non scoprire i costi a fine mese.', 'In production you need to see what is happening: **metrics** (CPU, latency, errors), centralised **logs** and **alerts** that warn you before users do. Also set **budget** alerts so you do not discover costs at the end of the month.'),
        ], [
            Q('ia1', L('Che cos’è l’infrastruttura come codice?', 'What is infrastructure as code?'), [L('Descrivere le risorse in file versionati', 'Describing resources in versioned files'), L('Scrivere codice sul server', 'Writing code on the server'), L('Un linguaggio di programmazione', 'A programming language'), L('Un tipo di cloud', 'A type of cloud')], 0, L('Ripetibile e rivedibile.', 'Repeatable and reviewable.')),
            Q('ia2', L('Che cosa fa `terraform plan`?', 'What does `terraform plan` do?'), [L('Mostra cosa cambierà', 'Shows what will change'), L('Cancella tutto', 'Deletes everything'), L('Applica subito', 'Applies right away'), L('Scrive i log', 'Writes logs')], 0, L('Poi `apply`.', 'Then `apply`.')),
            Q('ia3', L('A che cosa servono gli avvisi di budget?', 'What are budget alerts for?'), [L('Sapere in tempo se i costi salgono', 'Knowing in time if costs rise'), L('Pagare meno tasse', 'Paying less tax'), L('Bloccare gli utenti', 'Blocking users'), L('Velocizzare il server', 'Speeding up the server')], 0, L('Niente sorprese in fattura.', 'No surprises on the bill.')),
        ], task('text', L('Scrivi i due comandi Terraform nell’ordine giusto, uno per riga: prima quello che mostra le modifiche, poi quello che le applica.', 'Write the two Terraform commands in the right order, one per line: first the one showing the changes, then the one applying them.'), '', [re('terraform\s+plan\s*\n\s*terraform\s+apply', L('Prima plan, poi apply', 'First plan, then apply'))], '', "terraform plan\nterraform apply"), [
            dMatch(pMatch(), [['Terraform', L('Infrastruttura come codice', 'Infrastructure as code')], [L('Metriche', 'Metrics'), L('CPU, latenza, errori', 'CPU, latency, errors')], [L('Allarmi', 'Alerts'), L('Avvisano dei problemi', 'Warn about problems')], ['Budget', L('Controllo dei costi', 'Cost control')]], L('Gestire il cloud.', 'Managing the cloud.')),
            dFlash(pFlash(), [[L('I file IaC si tengono in Git', 'IaC files are kept in Git'), true], [L('terraform plan applica le modifiche', 'terraform plan applies changes'), false], [L('Gli allarmi dovrebbero arrivare prima degli utenti', 'Alerts should come before users notice'), true], [L('I costi del cloud sono sempre fissi', 'Cloud costs are always fixed'), false]], L('Codice e controllo.', 'Code and control.')),
        ]),
        lesson('networking', L('Reti, firewall, CDN e DNS', 'Networks, firewalls, CDN and DNS'), [
            P('Nel cloud le tue risorse vivono in una rete privata: **VPC** su AWS e Google Cloud, **VNet** su Azure. La dividi in **subnet**: pubbliche (raggiungibili da internet, per esempio il load balancer) e private (server dell’app e database). I **security group** o le regole firewall decidono quale traffico entra: si apre solo il necessario.', 'In the cloud your resources live in a private network: **VPC** on AWS and Google Cloud, **VNet** on Azure. You split it into **subnets**: public ones (reachable from the internet, for example the load balancer) and private ones (app servers and database). **Security groups** or firewall rules decide which traffic gets in: you open only what is needed.'),
            Fig(L(fig_cloud_vpc(['subnet pubblica', 'subnet privata', 'da internet il database non si raggiunge']), fig_cloud_vpc(['public subnet', 'private subnet', 'the database cannot be reached from the internet'])),
                L('Una VPC con tre subnet. Da Internet si entra sulla porta 443 nel load balancer della subnet pubblica; il load balancer passa sulla 8080 ai tre server dell’app in una subnet privata, che usano la 5432 per il database in un’altra subnet privata. Una linea tratteggiata da Internet al database si ferma prima di arrivarci.', 'A VPC with three subnets. From the Internet you enter on port 443 into the load balancer in the public subnet; the load balancer forwards on 8080 to the three app servers in a private subnet, which use 5432 for the database in another private subnet. A dashed line from the Internet to the database stops before reaching it.'),
                L('Ogni livello accetta traffico solo da quello prima, e solo sulla sua porta.', 'Each tier accepts traffic only from the one before it, and only on its own port.')),
            C('Internet
   │ 443 (HTTPS)
[Load balancer]          subnet pubblica
   │ 8080
[Server dell’app ×3]     subnet privata
   │ 5432
[Database gestito]       subnet privata, nessun accesso da internet', 'text'),
            P('Una **CDN** (CloudFront, Azure Front Door, Cloud CDN o Cloudflare) copia i file statici su server vicini agli utenti: pagine più veloci e meno carico. Il **DNS** (Route 53, Azure DNS, Cloud DNS) traduce il dominio nell’indirizzo del load balancer o della CDN. Non aprire mai SSH (22) o le porte del database a tutto internet (`0.0.0.0/0`).', 'A **CDN** (CloudFront, Azure Front Door, Cloud CDN or Cloudflare) copies static files to servers close to users: faster pages and less load. **DNS** (Route 53, Azure DNS, Cloud DNS) translates the domain into the address of the load balancer or CDN. Never open SSH (22) or database ports to the whole internet (`0.0.0.0/0`).'),
        ], [
            Q('nt1', L('Che cos’è una VPC?', 'What is a VPC?'), [L('Un tipo di macchina virtuale molto potente', 'A very powerful type of virtual machine'), L('Una rete privata e isolata per le tue risorse cloud', 'A private, isolated network for your cloud resources'), L('Un servizio che copia i file vicino agli utenti', 'A service copying files close to users'), L('Un elenco degli utenti e dei loro permessi', 'A list of users and their permissions')], 1, L('Su Azure si chiama VNet.', 'On Azure it is called VNet.')),
            Q('nt2', L('Dove dovrebbe stare il database di un’app web?', 'Where should a web app’s database live?'), [L('In una subnet pubblica con un IP pubblico', 'In a public subnet with a public IP'), L('Su una CDN vicina agli utenti', 'On a CDN close to users'), L('In una subnet privata, raggiungibile solo dai server dell’app', 'In a private subnet, reachable only from the app servers'), L('In un bucket pubblico per fare prima', 'In a public bucket to be quicker')], 2, L('Meno è esposto, meno è attaccabile.', 'The less exposed, the less attackable.')),
            Q('nt3', L('Che cosa fa una CDN?', 'What does a CDN do?'), [L('Copia i file statici su server vicini agli utenti', 'Copies static files to servers close to users'), L('Cifra il contenuto del database', 'Encrypts the database contents'), L('Traduce i domini in indirizzi IP', 'Translates domains into IP addresses'), L('Assegna i permessi agli utenti', 'Assigns permissions to users')], 0, L('Tradurre i domini è il lavoro del DNS.', 'Translating domains is DNS’s job.')),
        ], task('text', L('Scrivi tre regole firewall, una per riga come `porta: origine`: HTTPS (443) da chiunque (`0.0.0.0/0`), il database (5432) solo dalla subnet dell’app `10.0.1.0/24`, SSH (22) solo dall’IP dell’ufficio `203.0.113.10`.', 'Write three firewall rules, one per line as `port: source`: HTTPS (443) from anyone (`0.0.0.0/0`), the database (5432) only from the app subnet `10.0.1.0/24`, SSH (22) only from the office IP `203.0.113.10`.'), '', [re('^443\s*:\s*0\.0\.0\.0/0\s*$', L('HTTPS aperto a tutti', 'HTTPS open to everyone')), re('^5432\s*:\s*10\.0\.1\.0/24\s*$', L('Database solo dall’app', 'Database only from the app')), re('^22\s*:\s*203\.0\.113\.10(/32)?\s*$', L('SSH solo dall’ufficio', 'SSH only from the office'))], '', "443: 0.0.0.0/0\n5432: 10.0.1.0/24\n22: 203.0.113.10/32"), [
            dMatch(pMatch(), [['VPC / VNet', L('La tua rete privata', 'Your private network')], [L('Subnet privata', 'Private subnet'), L('Niente accesso diretto da internet', 'No direct internet access')], ['Security group', L('Chi può entrare e da dove', 'Who may get in and from where')], ['CDN', L('File vicini agli utenti', 'Files close to users')], ['DNS', L('Dal dominio all’indirizzo', 'From domain to address')]], L('La rete nel cloud.', 'The network in the cloud.')),
            dFlash(pFlash(), [[L('Aprire SSH a 0.0.0.0/0 va bene', 'Opening SSH to 0.0.0.0/0 is fine'), false], [L('Il load balancer può stare in una subnet pubblica e i server in una privata', 'The load balancer can sit in a public subnet and the servers in a private one'), true], [L('Una CDN velocizza i file statici', 'A CDN speeds up static files'), true], [L('Un database gestito deve sempre avere un IP pubblico', 'A managed database must always have a public IP'), false]], L('Aprire solo il necessario.', 'Open only what is needed.')),
        ]),
        lesson('iam', L('Identità e permessi (IAM)', 'Identities and permissions (IAM)'), [
            P('L’**IAM** decide chi può fare cosa. Ci sono **utenti** (persone), **gruppi** e **ruoli**: identità temporanee che app e servizi assumono quando servono. Le **policy** elencano le azioni permesse su certe risorse. La regola d’oro è il **privilegio minimo**: ogni identità ha solo i permessi che le servono.', '**IAM** decides who can do what. There are **users** (people), **groups** and **roles**: temporary identities that apps and services assume when needed. **Policies** list the actions allowed on certain resources. The golden rule is **least privilege**: every identity has only the permissions it needs.'),
            C('{
  "Version": "2012-10-17",
  "Statement": [{
    "Effect": "Allow",
    "Action": ["s3:GetObject", "s3:PutObject"],
    "Resource": "arn:aws:s3:::fucina-foto/*"
  }]
}', 'json'),
            P('Evita le **chiavi di accesso permanenti** nel codice o sui portatili: le app ricevono credenziali temporanee da un ruolo (su una VM, in una funzione, o da GitHub Actions tramite OIDC), le persone entrano con un accesso unico (SSO) e l’MFA. Una chiave finita in un repository va **revocata subito**: cancellarla dal file non basta, resta nella storia di Git.', 'Avoid **permanent access keys** in code or on laptops: apps get temporary credentials from a role (on a VM, in a function, or from GitHub Actions through OIDC), people sign in with single sign-on (SSO) and MFA. A key that ended up in a repository must be **revoked at once**: deleting it from the file is not enough, it stays in Git history.'),
        ], [
            Q('im1', L('Che cosa dice il principio del privilegio minimo?', 'What does the principle of least privilege say?'), [L('Gli amministratori devono essere pochi utenti', 'Administrators must be few users'), L('Si dà a tutti il ruolo di amministratore per non bloccare il lavoro', 'Everyone gets the admin role so work is never blocked'), L('Il team usa una sola password condivisa', 'The team uses a single shared password'), L('Ogni identità ha solo i permessi che le servono', 'Every identity has only the permissions it needs')], 3, L('Così un account violato fa meno danni.', 'That way a breached account does less damage.')),
            Q('im2', L('Come dovrebbe accedere a un bucket un’app che gira su una VM?', 'How should an app running on a VM access a bucket?'), [L('Con chiavi di accesso scritte nel codice', 'With access keys written in the code'), L('Con le credenziali dell’account root', 'With the root account’s credentials'), L('Con un ruolo che le dà credenziali temporanee', 'With a role giving it temporary credentials'), L('Rendendo il bucket pubblico', 'By making the bucket public')], 2, L('Niente segreti da custodire nel codice.', 'No secrets to keep in the code.')),
            Q('im3', L('Trovi una chiave di accesso in un repository pubblico. Che cosa fai?', 'You find an access key in a public repository. What do you do?'), [L('La revochi subito e ne crei una nuova se serve', 'Revoke it at once and create a new one if needed'), L('Cancelli il file nel prossimo commit', 'Delete the file in the next commit'), L('Rendi privato il repository e basta', 'Make the repository private and nothing more'), L('Aspetti di vedere se qualcuno la usa', 'Wait to see whether anyone uses it')], 0, L('I bot cercano chiavi nei repository di continuo.', 'Bots search repositories for keys all the time.')),
        ], task('json', L('Scrivi nello `Statement` una regola che permette solo `s3:GetObject` sulla risorsa `arn:aws:s3:::fucina-report/*`.', 'Write in the `Statement` a rule that allows only `s3:GetObject` on the resource `arn:aws:s3:::fucina-report/*`.'), "{\n  \"Version\": \"2012-10-17\",\n  \"Statement\": [\n\n  ]\n}", [re('"Effect"\s*:\s*"Allow"', L('Permetti', 'You allow')), re('"Action"\s*:\s*(\[\s*)?"s3:GetObject"\s*(\]\s*)?,?\s*$', L('Solo la lettura', 'Read only')), re('"Resource"\s*:\s*(\[\s*)?"arn:aws:s3:::fucina-report/\*"', L('Sulla risorsa giusta', 'On the right resource'))], '', "{\n  \"Version\": \"2012-10-17\",\n  \"Statement\": [{\n    \"Effect\": \"Allow\",\n    \"Action\": \"s3:GetObject\",\n    \"Resource\": \"arn:aws:s3:::fucina-report/*\"\n  }]\n}"), [
            dMatch(pMatch(), [[L('Utente', 'User'), L('Una persona', 'A person')], [L('Ruolo', 'Role'), L('Identità temporanea per app e servizi', 'Temporary identity for apps and services')], ['Policy', L('Le azioni permesse', 'The allowed actions')], ['MFA', L('Un secondo fattore', 'A second factor')], ['SSO', L('Un solo accesso per tanti servizi', 'One sign-in for many services')]], L('Chi può fare cosa.', 'Who can do what.')),
            dBug(pBug(), ['{', '  "Effect": "Allow",', '  "Action": "s3:*",', '  "Resource": "arn:aws:s3:::fucina-foto/*"', '}'], 2, '  "Action": ["s3:GetObject", "s3:PutObject"],', L('`s3:*` permette anche di cancellare: dai solo le azioni che servono.', '`s3:*` also allows deleting: grant only the actions needed.')),
        ]),
        lesson('scaling', L('Scalabilità, disponibilità e backup', 'Scaling, availability and backups'), [
            P('Scalare in **verticale** vuol dire passare a una macchina più grande: semplice, ma ha un limite e spesso richiede un riavvio. Scalare in **orizzontale** vuol dire aggiungere macchine uguali dietro un **load balancer**: serve un’app **senza stato**, con sessioni e file in servizi condivisi (Redis, object storage, database). L’autoscaling aggiunge e toglie macchine in base al carico.', 'Scaling **vertically** means moving to a bigger machine: simple, but it has a limit and often needs a restart. Scaling **horizontally** means adding identical machines behind a **load balancer**: it needs a **stateless** app, with sessions and files in shared services (Redis, object storage, database). Autoscaling adds and removes machines based on load.'),
            Fig(L(fig_cloud_scale(['In verticale', 'In orizzontale', 'una macchina più grande: ha un limite', 'più macchine uguali: l’autoscaling ne aggiunge']), fig_cloud_scale(['Vertically', 'Horizontally', 'a bigger machine: it has a limit', 'more equal machines: autoscaling adds some'])),
                L('A sinistra un server da 2 CPU diventa un server da 8 CPU. A destra un load balancer distribuisce il traffico su tre server uguali e l’autoscaling ne aggiunge un quarto.', 'On the left a 2-CPU server becomes an 8-CPU server. On the right a load balancer spreads traffic over three equal servers and autoscaling adds a fourth.'),
                L('In orizzontale non c’è un tetto, ma l’app non deve tenere dati in memoria locale.', 'Horizontally there is no ceiling, but the app must not keep data in local memory.')),
            C('verticale    1 server: da 2 a 8 CPU      semplice, ma ha un limite
orizzontale  da 2 a 6 server uguali      servono load balancer e app senza stato
RPO          quanti dati puoi perdere    “al massimo 15 minuti”
RTO          in quanto tempo riparti     “entro 1 ora”', 'text'),
            P('I **backup** vanno automatici (snapshot dei database), con una copia in un’altra regione, e soprattutto vanno **provati**: un backup mai ripristinato è solo una speranza. **RPO** (quanti dati puoi perdere) e **RTO** (in quanto tempo devi ripartire) guidano le scelte e i costi: più zone, repliche in altre regioni, backup più frequenti.', '**Backups** should be automatic (database snapshots), with a copy in another region, and above all they must be **tested**: a backup never restored is only a hope. **RPO** (how much data you can lose) and **RTO** (how quickly you must be back) drive the choices and the costs: more zones, replicas in other regions, more frequent backups.'),
        ], [
            Q('sc1', L('Che cosa vuol dire scalare in orizzontale?', 'What does scaling horizontally mean?'), [L('Passare a un server con più CPU e RAM', 'Moving to a server with more CPU and RAM'), L('Aggiungere server uguali dietro un load balancer', 'Adding identical servers behind a load balancer'), L('Spostare l’app in un’altra regione', 'Moving the app to another region'), L('Comprimere i dati del database', 'Compressing the database data')], 1, L('Il verticale invece ingrandisce una macchina.', 'Vertical scaling instead enlarges one machine.')),
            Q('sc2', L('Perché per scalare in orizzontale l’app deve essere senza stato?', 'Why must the app be stateless to scale horizontally?'), [L('Per consumare meno CPU', 'To use less CPU'), L('Perché i load balancer non accettano cookie', 'Because load balancers do not accept cookies'), L('Per non dover usare un database', 'So as not to need a database'), L('Perché ogni richiesta può arrivare a un server diverso', 'Because each request may reach a different server')], 3, L('Le sessioni vanno in un servizio condiviso.', 'Sessions go in a shared service.')),
            Q('sc3', L('Che cos’è l’RPO?', 'What is the RPO?'), [L('Quanti dati al massimo puoi permetterti di perdere', 'How much data at most you can afford to lose'), L('In quanto tempo il servizio deve ripartire', 'How quickly the service must be back'), L('Il numero di server in una regione', 'The number of servers in a region'), L('Il costo mensile dei backup', 'The monthly cost of backups')], 0, L('Il tempo per ripartire è l’RTO.', 'The time to recover is the RTO.')),
        ], task('text', L('Scrivi gli obiettivi di un negozio online: una riga `RPO: …` (al massimo 15 minuti di dati persi), una riga `RTO: …` (ripartire entro 1 ora) e una riga che dica come verifichi che i backup funzionano.', 'Write an online shop’s targets: a line `RPO: …` (at most 15 minutes of data lost), a line `RTO: …` (back within 1 hour) and a line saying how you check that the backups work.'), '', [re('^RPO\s*:\s*15\s*(min|minuti|minutes)', L('L’RPO', 'The RPO')), re('^RTO\s*:\s*(1\s*(ora|h|hour)|60\s*(min|minuti|minutes))', L('L’RTO', 'The RTO')), re('ripristin|restor|recuper', L('La prova di ripristino', 'The restore test'))], '', "RPO: 15 minuti\nRTO: 1 ora\nprova di ripristino del backup ogni mese in un ambiente di test"), [
            dMatch(pMatch(), [[L('Verticale', 'Vertical'), L('Una macchina più grande', 'A bigger machine')], [L('Orizzontale', 'Horizontal'), L('Più macchine uguali', 'More identical machines')], ['Load balancer', L('Distribuisce le richieste', 'Spreads the requests')], ['RPO', L('Dati persi al massimo', 'Maximum data lost')], ['RTO', L('Tempo per ripartire', 'Time to recover')]], L('Crescere e resistere.', 'Growing and holding up.')),
            dFlash(pFlash(), [[L('Un backup mai ripristinato è una garanzia sicura', 'A backup never restored is a sure guarantee'), false], [L('Tenere le sessioni in Redis aiuta a scalare in orizzontale', 'Keeping sessions in Redis helps horizontal scaling'), true], [L('Scalare in verticale non ha limiti', 'Vertical scaling has no limits'), false], [L('Una copia in un’altra regione protegge da un guasto regionale', 'A copy in another region protects against a regional outage'), true]], L('Pronti al peggio.', 'Ready for the worst.')),
        ]),
    ],
];
